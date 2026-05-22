<?php
$file = 'app/Services/WhatsAppBotService.php';
$content = file_get_contents($file);

$replacements = [
    '"❌ *Group Chat Not Supported*\n\nBot hanya bisa digunakan di private chat. Silakan kirim pesan langsung ke nomor bot."' => '"❌ *Group Chat Not Supported*\n\nBot can only be used in private chat. Please send a direct message to the bot number."',
    '"❌ *Akses Ditolak*\n\nNomor Anda tidak terdaftar di sistem. Silakan register terlebih dahulu di website."' => '"❌ *Access Denied*\n\nYour number is not registered in the system. Please register on the website first."',
    '"❌ *Akun Tidak Aktif*\n\nAkun Anda belum diverifikasi. Silakan login ke website untuk verifikasi OTP."' => '"❌ *Account Inactive*\n\nYour account has not been verified. Please log in to the website to verify OTP."',
    '"📋 *Form Order SMM*\n\nSilakan *Copy* form di bawah ini, isi datanya, lalu kirim kembali ke bot:\n\n/order:\n[\nid:,\ntarget:,\njumlah:\n]"' => '"📋 *SMM Order Form*\n\nPlease *Copy* the form below, fill in the data, and send it back to the bot:\n\n/order:\n[\nid:,\ntarget:,\namount:\n]"',
    '"❌ *Terjadi Kesalahan*\n\nSilakan coba lagi nanti atau hubungi admin."' => '"❌ *Error Occurred*\n\nPlease try again later or contact the admin."',
    '"📋 *Daftar Layanan*\n\nBelum ada layanan tersedia."' => '"📋 *Service List*\n\nNo services available yet."',
    '"📋 *DAFTAR LAYANAN SMM*\n\n"' => '"📋 *SMM SERVICE LIST*\n\n"',
    'number_format($service->price_sale, 0, \',\', \'.\')' => 'number_format($service->price_sale, 2, \'.\', \',\')',
    '"  💰 Rp {$price}\n"' => '"  💰 ${$price}\n"',
    '"💡 *Cara Order:*\nKetik perintah: /order\n\n"' => '"💡 *How to Order:*\nType command: /order\n\n"',
    '"Lalu isi form yang diberikan oleh bot."' => '"Then fill in the form provided by the bot."',
    '/\jumlah:\s*(\d+)/i' => '/amount:\s*(\d+)/i',
    '"❌ *Format Salah*\n\nGunakan format:\n/order:\n[\nid:123,\ntarget:username_atau_link,\njumlah:100\n]"' => '"❌ *Invalid Format*\n\nUse format:\n/order:\n[\nid:123,\ntarget:username_or_link,\namount:100\n]"',
    '"❌ *Layanan Tidak Ditemukan*\n\nID Service {$serviceId} tidak valid atau tidak aktif."' => '"❌ *Service Not Found*\n\nService ID {$serviceId} is invalid or inactive."',
    '"❌ *Saldo Tidak Cukup*\n\n💰 Harga: Rp " . number_format($totalPrice, 0, \',\', \'.\') . "\n💳 Saldo Anda: Rp " . number_format($user->balance, 0, \',\', \'.\') . "\n📉 Kurang: Rp " . number_format($needed, 0, \',\', \'.\') . "\n\nSilakan deposit terlebih dahulu:\n/deposit.cryptomus:JUMLAH"' => '"❌ *Insufficient Balance*\n\n💰 Price: $" . number_format($totalPrice, 2, \'.\', \',\') . "\n💳 Your Balance: $" . number_format($user->balance, 2, \'.\', \',\') . "\n📉 Short: $" . number_format($needed, 2, \'.\', \',\') . "\n\nPlease deposit first:\n/deposit.cryptomus:AMOUNT"',
    '"❌ *System Error*\n\nAPI Provider untuk layanan ini belum dikonfigurasi."' => '"❌ *System Error*\n\nAPI Provider for this service is not configured."',
    '"❌ *Gagal Koneksi*\n\nGagal terhubung ke API Provider SMM."' => '"❌ *Connection Failed*\n\nFailed to connect to SMM API Provider."',
    '"✅ *ORDER BERHASIL DITERUSKAN*\n\n"' => '"✅ *ORDER SUCCESSFULLY FORWARDED*\n\n"',
    '"🛒 Layanan: {$service->name_service}\n"' => '"🛒 Service: {$service->name_service}\n"',
    '"📦 Jumlah: {$amount}\n"' => '"📦 Amount: {$amount}\n"',
    '"💰 Harga: Rp " . number_format($totalPrice, 0, \',\', \'.\') . "\n"' => '"💰 Price: $" . number_format($totalPrice, 2, \'.\', \',\') . "\n"',
    '"💳 Saldo Terpotong: Rp " . number_format($totalPrice, 0, \',\', \'.\') . "\n"' => '"💳 Deducted Balance: $" . number_format($totalPrice, 2, \'.\', \',\') . "\n"',
    '"💡 *Cek Status:*\n/status:{$invoice}"' => '"💡 *Check Status:*\n/status:{$invoice}"',
    "'Gagal dari API Provider.'" => "'Failed from API Provider.'",
    '"❌ *Order Gagal*\n\nPesan Error dari API:\n{$errorMsg}"' => '"❌ *Order Failed*\n\nError Message from API:\n{$errorMsg}"',
    '"❌ *System Error*\n\nTerjadi kesalahan sistem: " . substr($e->getMessage(), 0, 100)' => '"❌ *System Error*\n\nA system error occurred: " . substr($e->getMessage(), 0, 100)',
    '"❌ *Invoice Tidak Ditemukan*\n\nInvoice {$invoice} tidak valid atau bukan milik Anda."' => '"❌ *Invoice Not Found*\n\nInvoice {$invoice} is invalid or doesn\'t belong to you."',
    '"📊 *STATUS ORDER*\n\n"' => '"📊 *ORDER STATUS*\n\n"',
    '"💰 Harga: Rp " . number_format($order->price_sale, 0, \',\', \'.\') . "\n"' => '"💰 Price: $" . number_format($order->price_sale, 2, \'.\', \',\') . "\n"',
    '"\n📅 Waktu: {$order->created_at->format(\'d M Y H:i\')}"' => '"\n📅 Date: {$order->created_at->format(\'d M Y H:i\')}"',
    '"💳 *INFORMASI SALDO*\n\n"' => '"💳 *BALANCE INFORMATION*\n\n"',
    '"👤 Nama: {$user->full_name}\n"' => '"👤 Name: {$user->full_name}\n"',
    '"💰 Saldo: Rp " . number_format($user->balance, 0, \',\', \'.\') . "\n\n"' => '"💰 Balance: $" . number_format($user->balance, 2, \'.\', \',\') . "\n\n"',
    '"💡 *Deposit:*\n/deposit.cryptomus:JUMLAH\n\n"' => '"💡 *Deposit:*\n/deposit.cryptomus:AMOUNT\n\n"',
    '"Contoh: /deposit.cryptomus:100000"' => '"Example: /deposit.cryptomus:10"',
    '10000' => '1',
    '"❌ *Jumlah Tidak Valid*\n\nMinimal deposit adalah Rp 10.000\n\nContoh: /deposit.cryptomus:100000"' => '"❌ *Invalid Amount*\n\nMinimum deposit is $1\n\nExample: /deposit.cryptomus:10"',
    '"💳 *DEPOSIT REQUEST*\n\n"' => '"💳 *DEPOSIT REQUEST*\n\n"',
    '"💰 Jumlah: Rp " . number_format($amount, 0, \',\', \'.\') . "\n"' => '"💰 Amount: $" . number_format($amount, 2, \'.\', \',\') . "\n"',
    '"💳 Metode: Cryptomus\n"' => '"💳 Method: Cryptomus\n"',
    '"🔗 *Silakan login ke website untuk melanjutkan pembayaran:*\n"' => '"🔗 *Please log in to the website to continue payment:*\n"',
    '"💡 Masukkan invoice: {$invoice} pada form deposit"' => '"💡 Enter invoice: {$invoice} in the deposit form"',
    '"🤖 *BOT ORDER SMM - HELP*\n\n"' => '"🤖 *SMM ORDER BOT - HELP*\n\n"',
    '"📋 *Daftar Perintah:*\n\n"' => '"📋 *Command List:*\n\n"',
    '"   Menampilkan semua layanan\n\n"' => '"   Show all services\n\n"',
    '"   Meminta form order layanan\n\n"' => '"   Request order form\n\n"',
    '"   Cek status order (contoh: /status:INV-12345678)\n\n"' => '"   Check order status (example: /status:INV-12345678)\n\n"',
    '"   Cek saldo akun\n\n"' => '"   Check account balance\n\n"',
    '"   Request deposit (contoh: /deposit.cryptomus:100000)\n\n"' => '"   Request deposit (example: /deposit.cryptomus:10)\n\n"',
    '"   Menampilkan pesan bantuan ini\n\n"' => '"   Show this help message\n\n"',
    '"💡 *Catatan:*\n"' => '"💡 *Notes:*\n"',
    '"• Nomor WhatsApp harus terdaftar di website\n"' => '"• WhatsApp number must be registered on the website\n"',
    '"• Akun harus aktif (sudah verifikasi OTP)\n"' => '"• Account must be active (OTP verified)\n"',
    '"• Minimal deposit Rp 10.000"' => '"• Minimum deposit $1"',
];

foreach ($replacements as $old => $new) {
    $content = str_replace($old, $new, $content);
}

file_put_contents($file, $content);
echo "Done";
