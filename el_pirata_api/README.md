php artisan db:seed --class="RoleSeeder"
php artisan db:seed --class="AdminSeeder"
php artisan db:seed --class="LegalDocumentsTableSeeder"
php artisan db:seed --class="PaymentTypeSeeder"

## Tache cron
# archivage de chasse
cd /home/bebu8196/el_pirate_application/api/ && php artisan app:archive-expired-hunts