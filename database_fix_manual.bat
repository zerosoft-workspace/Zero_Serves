@echo off
echo SQLite veritabanı onarım scripti...

echo Bozuk veritabanı siliniyor...
if exist "database\database.sqlite" del "database\database.sqlite"

echo Yeni veritabanı oluşturuluyor...
type nul > "database\database.sqlite"

echo Migration'lar çalıştırılıyor...
php artisan migrate --force

echo Seeder'lar çalıştırılıyor...
php artisan db:seed --force

echo Veritabanı başarıyla yeniden oluşturuldu!
pause
