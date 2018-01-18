# M1 CSI

## Requirements
At least PHP 5.5.


## Technologies

This project using : 
- Laravel 5.5 : https://laravel.com/
- jQuery 3.2.1 : https://jquery.com/
- Bootstrap 3.3.7 : https://getbootstrap.com/docs/3.3/


## Installation

Clone the repository :

    git clone git@gitlab.com:nlamblin/M1_CSI.git

Then, create a `.env` into root folder (like `.env.example`) file which content is : 

    APP_ENV=local
    APP_DEBUG=false
    APP_KEY=
    APP_URL=http://localhost

    DB_CONNECTION=pgsql
    DB_HOST=localhost
    DB_PORT=5432
    DB_DATABASE=*****
    DB_USERNAME=*****
    DB_PASSWORD=*****

    CACHE_DRIVER=file
    SESSION_DRIVER=file
    QUEUE_DRIVER=sync

    REDIS_HOST=127.0.0.1
    REDIS_PASSWORD=null
    REDIS_PORT=6379
    
    MAIL_DRIVER=smtp
    MAIL_HOST=mailtrap.io
    MAIL_PORT=2525
    MAIL_USERNAME=null
    MAIL_PASSWORD=null
    MAIL_ENCRYPTION=null
    
The database script is stored into `/baseInterne.sql`.    
    
The database script of the external group is stored into `/baseExterne.sql`. Into Postgres, external database name must be `M1_BASE_EXTERNE`. If you need to change the default configuration to change password for example, it is into `/config/database.php` -> `pgsql_externe`
    
Finally, execute both following commands : 

    composer install
    php artisan key:generate
    
    
## Usage

### Create controllers

    php artisan make:controller controller_name

### Create models

    php artisan make:model Models/model_name

### Directory Structure 


Javascript files : `public/js/`.

CSS files : `public/css/`.

Views : `resources/views/`.

Controlers : `app/Http/Controllers/`.

Models : `app/Models/`.

Routes : `routes/web.php`.