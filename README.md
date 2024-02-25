Here are the steps to set up and run the Laravel application for product management:

1. Clone the Repository:
git clone https://github.com/nitikadev/product_management_nitika.git

2. Create a Copy of Environment File:
- Navigate to the project directory.
- Make a copy of the .env.example file and rename it to .env.

3. Update Database Credentials:
- Open the .env file.
- Update the database connection details such as DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, and DB_PASSWORD.

4.Install Composer Dependencies:
composer install

5. Generate Application Key:
php artisan key:generate

6. Run Database Migrations:
php artisan migrate

7. Seed the Database:
php artisan db:seed

8. Create Symbolic Link for Storage:
php artisan storage:link

9. Generate JWT Secret:
php artisan jwt:secret

10. Start the Laravel Development Server:
php artisan serve

After following these steps, your Laravel application for product management should be up and running. You can access it by navigating to the provided URL after running the php artisan serve command. Make sure you have PHP version 8.2 installed and configured on your system before proceeding with these steps.

Postman Collections
https://www.postman.com/bold-sunset-54829/workspace/stye/documentation/25817942-795f31bc-d252-4c39-a930-9d4271a677a3

https://www.postman.com/bold-sunset-54829/workspace/stye/request/25817942-240ad93c-680a-46d5-845d-e72fc823b6ef