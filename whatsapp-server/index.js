const express = require('express');
const cors = require('cors');
const pino = require('pino');
const fs = require('fs');
const path = require('path');
const qrcode = require('qrcode');
const {
    default: makeWASocket,
    useMultiFileAuthState,
    DisconnectReason,
    fetchLatestBaileysVersion
} = require('@whiskeysockets/baileys');

const app = express();
const SERVER_TOKEN = process.env.WHATSAPP_SERVER_TOKEN || 'local-dev-token';
const WEBHOOK_SECRET = process.env.WHATSAPP_WEBHOOK_SECRET || 'local-webhook-secret';
const HOST = process.env.WHATSAPP_SERVER_HOST || '127.0.0.1';
const CORS_ORIGINS = (process.env.WHATSAPP_SERVER_CORS_ORIGINS || '')
    .split(',')
    .map((v) => v.trim())
    .filter(Boolean);

app.use(cors({
    origin: (origin, callback) => {
        if (!origin) {
            return callback(null, true);
        }
        if (CORS_ORIGINS.length === 0 || CORS_ORIGINS.includes(origin)) {
            return callback(null, true);
        }
        return callback(new Error('CORS not allowed'));
    }
}));
app.use(express.json());

const LARAVEL_URL = process.env.LARAVEL_URL || 'http://127.0.0.1:8000';

async function notifyLaravel(event, sessionId) {
    const url = event === 'disconnect'
        ? `${LARAVEL_URL}/webhook/whatsapp/disconnect`
        : `${LARAVEL_URL}/webhook/whatsapp/connected`;
    try {
        const res = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Webhook-Secret': WEBHOOK_SECRET,
            },
            body: JSON.stringify({ sessionId })
        });
        console.log(`Notified Laravel [${event}] for session ${sessionId}: ${res.status}`);
    } catch (err) {
        console.error(`Failed to notify Laravel [${event}] for session ${sessionId}:`, err.message);
    }
}

async function forwardToBotWebhook(sender, message, sessionId) {
    try {
        const LARAVEL_WEBHOOK_URL = `${LARAVEL_URL}/webhook/whatsapp/bot`;
        
        const response = await fetch(LARAVEL_WEBHOOK_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Webhook-Secret': WEBHOOK_SECRET,
            },
            body: JSON.stringify({
                sender: sender,
                message: message,
                sessionId: sessionId,
                timestamp: new Date().toISOString()
            })
        });

        if (!response.ok) {
            console.error(`[${ts()}] Failed to forward message to webhook: ${response.status}`);
        } else {
            console.log(`[${ts()}] Message forwarded to webhook: ${sender} -> "${message}"`);
        }
    } catch (error) {
        console.error(`[${ts()}] Error forwarding to webhook:`, error);
    }
}

process.on('unhandledRejection', (reason, promise) => {
    console.error('Unhandled Rejection at:', promise, 'reason:', reason);
});
process.on('uncaughtException', (err) => {
    console.error('Uncaught Exception:', err);
});

const PORT = 3000;
const AUTH_DIR_BASE = path.join(__dirname, 'auth_info');

const sessions = new Map();
const sessionCreationInProgress = new Set();

function ts() {
    return new Date().toISOString();
}

let cachedBaileysVersion = null;

function checkServerToken(req, res, next) {
    const token = req.headers['x-server-token'];
    if (token !== SERVER_TOKEN) {
        return res.status(401).json({ status: 'error', message: 'Unauthorized' });
    }

    return next();
}

async function initBaileysVersion() {
    try {
        const { version } = await fetchLatestBaileysVersion();
        cachedBaileysVersion = version;
        console.log('Cached Baileys version:', version);
    } catch (err) {
        console.error('Failed to fetch Baileys version, using default:', err.message);
        cachedBaileysVersion = [2, 3000, 1015901307];
    }
}

async function startSession(sessionId) {
    const t0 = Date.now();
    console.log(`[${ts()}] [DEBUG] startSession called: sessionId=${sessionId}`);

    // Prevent concurrent session creation
    if (sessionCreationInProgress.has(sessionId)) {
        console.log(`[${ts()}] [DEBUG] Session creation already in progress for ${sessionId}, waiting...`);
        // Wait a bit and return existing session
        await new Promise(r => setTimeout(r, 500));
        if (sessions.has(sessionId)) {
            return sessions.get(sessionId);
        }
    }

    if (sessions.has(sessionId)) {
        const existing = sessions.get(sessionId);
        console.log(`[${ts()}] [DEBUG] Existing session found: status=${existing.status}`);
        if (existing.status !== 'disconnected') {
            console.log(`[${ts()}] [DEBUG] Returning existing session`);
            return existing;
        }
        sessions.delete(sessionId);
    }

    sessionCreationInProgress.add(sessionId);

    const sessionDir = path.join(AUTH_DIR_BASE, sessionId);
    if (!fs.existsSync(sessionDir)) {
        console.log(`[${ts()}] [DEBUG] Creating auth dir: ${sessionDir}`);
        fs.mkdirSync(sessionDir, { recursive: true });
    } else {
        console.log(`[${ts()}] [DEBUG] Auth dir exists: ${sessionDir}`);
    }

    console.log(`[${ts()}] [DEBUG] Loading auth state...`);
    const authStart = Date.now();
    const { state, saveCreds } = await useMultiFileAuthState(sessionDir);
    console.log(`[${ts()}] [DEBUG] Auth state loaded in ${Date.now() - authStart}ms`);

    const version = cachedBaileysVersion || [2, 3000, 1015901307];
    console.log(`[${ts()}] [DEBUG] Using Baileys version: ${JSON.stringify(version)}`);

    console.log(`[${ts()}] [DEBUG] Creating makeWASocket...`);
    const sockStart = Date.now();
    const sock = makeWASocket({
        version,
        logger: pino({ level: 'silent' }),
        printQRInTerminal: false,
        auth: state,
        browser: ['Mac OS', 'Safari', '17.0'],
        syncFullHistory: false,
        markOnlineOnConnect: false
    });

    const sessionData = {
        sock,
        status: 'disconnected',
        qr: null
    };
    sessions.set(sessionId, sessionData);
    console.log(`[${ts()}] [DEBUG] Session stored. Total time to create socket: ${Date.now() - t0}ms`);

    sock.ev.on('connection.update', async (update) => {
        const { connection, lastDisconnect, qr } = update;
        console.log(`[${ts()}] [DEBUG] connection.update for ${sessionId}: ${JSON.stringify({ connection, lastDisconnect: lastDisconnect?.error?.output?.statusCode, hasQr: !!qr })}`);

        if (qr && sessionData.status !== 'qr_ready') {
            const qrStart = Date.now();
            try {
                // Use medium error correction for reliable scanning
                sessionData.qr = await qrcode.toDataURL(qr, { 
                    width: 250, 
                    margin: 2,
                    errorCorrectionLevel: 'M'
                });
                sessionData.status = 'qr_ready';
                console.log(`[${ts()}] [DEBUG] QR Code (PNG) generated for session ${sessionId} in ${Date.now() - qrStart}ms, length=${sessionData.qr.length}`);
            } catch (qrErr) {
                console.error(`[${ts()}] [DEBUG] QR generation failed:`, qrErr.message);
            }
        } else if (qr) {
            console.log(`[${ts()}] [DEBUG] QR event received but already qr_ready, ignoring`);
        }

        if (connection === 'close') {
            const shouldReconnect = (lastDisconnect?.error)?.output?.statusCode !== DisconnectReason.loggedOut;
            console.log(`Session ${sessionId} closed. Reconnecting: ${shouldReconnect}`);
            
            sessions.delete(sessionId);

            if (shouldReconnect) {
                // If it's a timeout (408), don't auto reconnect immediately, let the user trigger it
                if ((lastDisconnect?.error)?.output?.statusCode !== 408) {
                    setTimeout(() => {
                        startSession(sessionId).catch(err => console.error('Error during reconnection:', err));
                    }, 3000);
                }
            } else {
                // Logged out - notify Laravel to update status
                notifyLaravel('disconnect', sessionId);
                sessionData.status = 'disconnected';
                sessionData.qr = null;
                if (fs.existsSync(sessionDir)) {
                    fs.rmSync(sessionDir, { recursive: true, force: true });
                }
            }
        } else if (connection === 'open') {
            console.log(`[${ts()}] [DEBUG] WhatsApp connection OPENED for session ${sessionId}! (total startup time: ${Date.now() - t0}ms)`);
            sessionData.status = 'connected';
            sessionData.qr = null;
            notifyLaravel('connected', sessionId);
        }
    });

    sock.ev.on('creds.update', (creds) => {
        console.log(`[${ts()}] [DEBUG] creds.update for ${sessionId}`);
        saveCreds(creds);
    });

    // Listen for incoming messages and forward to bot webhook
    sock.ev.on('messages.upsert', async ({ messages }) => {
        for (const msg of messages) {
            if (msg.key.fromMe) {
                continue;
            }
            if (msg.messageType === 'conversation' || msg.message?.conversation || msg.message?.extendedTextMessage?.text) {
                const messageText = msg.message?.conversation || msg.message?.extendedTextMessage?.text || '';
                let sender = msg.key.remoteJid.replace('@s.whatsapp.net', '');
                if (msg.key.remoteJid.includes('@lid') && msg.key.remoteJidAlt) {
                    sender = msg.key.remoteJidAlt.replace('@s.whatsapp.net', '');
                }
                
                console.log(`[${ts()}] [DEBUG] Full message structure:`, JSON.stringify({
                    key: msg.key,
                    participant: msg.key?.participant,
                    pushName: msg.pushName,
                    messageType: msg.messageType
                }, null, 2));
                
                // Extract actual sender from group messages
                if (msg.key.participant) {
                    // This is a group message, get the actual participant
                    sender = msg.key.participant.replace('@s.whatsapp.net', '');
                    console.log(`[${ts()}] [DEBUG] Group message from ${msg.key.remoteJid}, actual sender: ${sender}`);
                }
                
                console.log(`[${ts()}] [DEBUG] Received message from ${sender}: "${messageText}"`);
                
                // Forward to Laravel webhook for bot processing
                await forwardToBotWebhook(sender, messageText, sessionId);
            }
        }
    });

    sessionCreationInProgress.delete(sessionId);
    console.log(`[${ts()}] [DEBUG] startSession completed for ${sessionId}`);
    return sessionData;
}

(async () => {
    await initBaileysVersion();

    // Restore sessions on boot
    if (fs.existsSync(AUTH_DIR_BASE)) {
        const folders = fs.readdirSync(AUTH_DIR_BASE);
        for (const folder of folders) {
            if (fs.statSync(path.join(AUTH_DIR_BASE, folder)).isDirectory()) {
                console.log(`Restoring session: ${folder}`);
                startSession(folder);
            }
        }
    }

// API Endpoints
app.get('/session/status', checkServerToken, (req, res) => {
    const sessionId = req.query.sessionId;
    console.log(`[${ts()}] [DEBUG] GET /session/status sessionId=${sessionId}`);
    if (!sessionId) {
        console.log(`[${ts()}] [DEBUG] Missing sessionId`);
        return res.status(400).json({ status: 'error', message: 'sessionId is required' });
    }
    
    if (sessions.has(sessionId)) {
        const s = sessions.get(sessionId);
        console.log(`[${ts()}] [DEBUG] Session found: status=${s.status}, hasQr=${!!s.qr}`);
        const session = sessions.get(sessionId);
        return res.json({ 
            status: session.status, 
            qr: session.qr 
        });
    }

    console.log(`[${ts()}] [DEBUG] Session not found for ${sessionId}`);
    res.json({ status: 'disconnected', qr: null });
});

app.post('/session/start', checkServerToken, async (req, res) => {
    const reqT0 = Date.now();
    let { number } = req.body;
    console.log(`[${ts()}] [DEBUG] POST /session/start number=${number}`);
    if (!number) {
        console.log(`[${ts()}] [DEBUG] Missing number`);
        return res.status(400).json({ status: 'error', message: 'Number is required' });
    }

    number = number.replace(/\D/g, '');
    if (number.startsWith('0')) {
        number = '62' + number.slice(1);
    }
    
    const sessionId = number;
    console.log(`[${ts()}] [DEBUG] Formatted sessionId=${sessionId}`);

    try {
        if (sessions.has(sessionId)) {
            const existing = sessions.get(sessionId);
            console.log(`[${ts()}] [DEBUG] Existing session: status=${existing.status}`);
            if (existing.status === 'qr_ready') {
                console.log(`[${ts()}] [DEBUG] Returning existing QR immediately`);
                return res.json({ status: 'qr_ready', qr: existing.qr, sessionId });
            }
            if (existing.status === 'connected') {
                console.log(`[${ts()}] [DEBUG] Returning connected status`);
                return res.json({ status: 'connected', sessionId });
            }
            sessions.delete(sessionId);
        }

        const sessionDir = path.join(AUTH_DIR_BASE, sessionId);
        if (fs.existsSync(sessionDir)) {
            console.log(`[${ts()}] [DEBUG] Removing stale auth dir: ${sessionDir}`);
            fs.rmSync(sessionDir, { recursive: true, force: true });
        }

        console.log(`[${ts()}] [DEBUG] Triggering startSession (non-blocking)`);
        startSession(sessionId).catch(err => console.error(`[${ts()}] startSession error:`, err));

        console.log(`[${ts()}] [DEBUG] POST /session/start done in ${Date.now() - reqT0}ms, returning 'starting'`);
        return res.json({ status: 'starting', sessionId });
        
    } catch (err) {
        console.error(`[${ts()}] [DEBUG] Error starting session:`, err);
        return res.status(500).json({ status: 'error', message: 'Failed to start session' });
    }
});

app.post('/session/send', checkServerToken, async (req, res) => {
    const { sessionId, number, message } = req.body;
    
    if (!number || !message) {
        return res.status(400).json({ status: 'error', message: 'Missing number or message' });
    }
    
    let targetSessionId = sessionId;
    
    if (!targetSessionId) {
        for (const [id, session] of sessions.entries()) {
            if (session.status === 'connected') {
                targetSessionId = id;
                break;
            }
        }
    }

    if (!targetSessionId || !sessions.has(targetSessionId)) {
        return res.status(400).json({ status: 'error', message: 'No connected WhatsApp session available' });
    }

    const sessionData = sessions.get(targetSessionId);
    
    if (sessionData.status !== 'connected' || !sessionData.sock) {
        return res.status(400).json({ status: 'error', message: `WhatsApp session ${targetSessionId} not connected` });
    }

    let targetNumber = number.replace(/\D/g, '');
    if (targetNumber.startsWith('0')) {
        targetNumber = '62' + targetNumber.slice(1);
    }
    const jid = targetNumber + '@s.whatsapp.net';

    try {
        const [result] = await sessionData.sock.onWhatsApp(jid);
        if (!result || !result.exists) {
            return res.status(400).json({ status: 'error', message: 'WhatsApp number not registered' });
        }

        await sessionData.sock.sendMessage(jid, { text: message });
        return res.json({ status: 'success', message: 'Message sent successfully', sender: targetSessionId });
    } catch (err) {
        console.error('Error sending message:', err);
        return res.status(500).json({ status: 'error', message: 'Failed to send message' });
    }
});

app.post('/session/logout', checkServerToken, async (req, res) => {
    const { sessionId } = req.body;
    if (!sessionId) {
        return res.status(400).json({ status: 'error', message: 'sessionId is required' });
    }
    
    if (sessions.has(sessionId)) {
        const sessionData = sessions.get(sessionId);
        try {
            await sessionData.sock.logout();
        } catch (e) {}
        
        sessions.delete(sessionId);
        const sessionDir = path.join(AUTH_DIR_BASE, sessionId);
        if (fs.existsSync(sessionDir)) {
            fs.rmSync(sessionDir, { recursive: true, force: true });
        }
        res.json({ status: 'success', message: 'Logged out successfully' });
    } else {
        res.status(400).json({ status: 'error', message: 'No active session found for this ID' });
    }
});

    app.listen(PORT, HOST, () => {
        console.log(`WhatsApp API server running on ${HOST}:${PORT}`);
    });
})();
