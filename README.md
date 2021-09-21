# UNTU To Do Task Backend

Laravel Version: 8.54\
PHP Version: ^7.3|^8.0\
Database: MySql

## How to run Application

Copy `.env.example` to `.env` file

In the project directory, you can run:

`docker-compose up -d`

Enter docker app container: `docker-compose exec app bash`\
Install Composer if not loaded already: `composer install`\
Generate app_key: `php artisan key:generate`

Runs the app in the development mode.\
API will run in this address: [http://localhost:8000/api](http://localhost:8000/api)
