<?php
// Test dosyası - resim yükleme sorununu tespit etmek için
echo "PHP Upload Test\n";
echo "================\n\n";

echo "upload_max_filesize: " . ini_get('upload_max_filesize') . "\n";
echo "post_max_size: " . ini_get('post_max_size') . "\n";
echo "max_file_uploads: " . ini_get('max_file_uploads') . "\n";
echo "file_uploads: " . (ini_get('file_uploads') ? 'Enabled' : 'Disabled') . "\n";
echo "max_execution_time: " . ini_get('max_execution_time') . "\n";
echo "memory_limit: " . ini_get('memory_limit') . "\n\n";

// Storage dizinlerini kontrol et
$storagePath = __DIR__ . '/storage/app/public';
echo "Storage Path: $storagePath\n";
echo "Storage exists: " . (is_dir($storagePath) ? 'Yes' : 'No') . "\n";
echo "Storage writable: " . (is_writable($storagePath) ? 'Yes' : 'No') . "\n\n";

$productsPath = $storagePath . '/products';
echo "Products Path: $productsPath\n";
echo "Products exists: " . (is_dir($productsPath) ? 'Yes' : 'No') . "\n";
echo "Products writable: " . (is_writable($productsPath) ? 'Yes' : 'No') . "\n\n";

$categoriesPath = $storagePath . '/categories';
echo "Categories Path: $categoriesPath\n";
echo "Categories exists: " . (is_dir($categoriesPath) ? 'Yes' : 'No') . "\n";
echo "Categories writable: " . (is_writable($categoriesPath) ? 'Yes' : 'No') . "\n\n";

// Symlink kontrolü
$publicStoragePath = __DIR__ . '/public/storage';
echo "Public Storage Path: $publicStoragePath\n";
echo "Public Storage exists: " . (is_dir($publicStoragePath) ? 'Yes' : 'No') . "\n";
echo "Public Storage is symlink: " . (is_link($publicStoragePath) ? 'Yes' : 'No') . "\n";

if (is_link($publicStoragePath)) {
    echo "Symlink target: " . readlink($publicStoragePath) . "\n";
}
?>
