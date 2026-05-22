<?php
$file = 'app/Services/WhatsAppBotService.php';
$content = file_get_contents($file);

$replacements = [
    '"❌ *Jumlah Tidak Valid*\n\nMinimal deposit adalah Rp 10.000\n\nContoh: /deposit.cryptomus:10"' => '"❌ *Invalid Amount*\n\nMinimum deposit is $1\n\nExample: /deposit.cryptomus:10"',
];

foreach ($replacements as $old => $new) {
    $content = str_replace($old, $new, $content);
}

file_put_contents($file, $content);
echo "Done";
