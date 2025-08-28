<?php
/**
 * SQLite veritabanı onarım scripti
 * Bozuk veritabanını siler ve yeniden oluşturur
 */

echo "SQLite veritabanı onarım scripti başlatılıyor...\n";

$databasePath = __DIR__ . '/database/database.sqlite';

// Mevcut bozuk veritabanını sil
if (file_exists($databasePath)) {
    unlink($databasePath);
    echo "Bozuk veritabanı silindi.\n";
}

// Yeni boş veritabanı dosyası oluştur
touch($databasePath);
echo "Yeni veritabanı dosyası oluşturuldu.\n";

// Laravel komutlarını çalıştır
echo "Migration'lar çalıştırılıyor...\n";
exec('php artisan migrate --force', $output, $returnCode);

if ($returnCode === 0) {
    echo "Migration'lar başarıyla tamamlandı.\n";
    
    // Seeder'ları çalıştır
    echo "Seeder'lar çalıştırılıyor...\n";
    exec('php artisan db:seed --force', $seedOutput, $seedReturnCode);
    
    if ($seedReturnCode === 0) {
        echo "Seeder'lar başarıyla tamamlandı.\n";
        echo "Veritabanı başarıyla yeniden oluşturuldu!\n";
    } else {
        echo "Seeder hatası:\n";
        print_r($seedOutput);
    }
} else {
    echo "Migration hatası:\n";
    print_r($output);
}

echo "\nScript tamamlandı.\n";
?>
