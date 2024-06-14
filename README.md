 1. git clone https://github.com/irvanrizki225/payment-test.git (untuk mengclone repositorinya)
 2. composer install (untuk menginstal laravel)
 3. cp .env.example .env (untuk mengcopy file .env.example dan membuat file .env)
 4. Pasti kan database dan redis nya sudah di install laptop atau komputer
 5. php artisan key:generate (untuk menggenerate key app laravelnya)
 6. php artisan migrate (untuk migrasi ke database nya)
 7. php artisan db:seed --class=UserSeeder (untuk membuat 1000 data fake user) 
 8. php artisan db:seed --class=TransactionSeeder (untuk membuat 10000 data fake payment)
 9. untuk memonitor job dan queue nya menggunakan laravel horizon
 10. php artisan test (untuk menjalankan unit testing nya)
 11. untuk link dokumentasi api nya ada pad url ini: https://documenter.getpostman.com/view/10182886/2sA3XPDiLb
