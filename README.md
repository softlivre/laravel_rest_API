# laravel_rest_API
A REST API built with Laravel as an assessment project for company 'P'.

# Deployment instructions
## Requirements
- Docker

## Steps
1. Clone the repository
2. Run the following command in the root directory of the project. This will build all necessary containers and start the services.

```bash
docker-compose up -d --build
```

## Access to the app
APP (API)
- http://localhost:85

ADMINER
- http://localhost:8080

PGADMIN
- http://localhost:5050


## draft area @TODO

composer create-project "laravel/laravel:^10.0" .

cp .env.example .env
composer install --prefer-dist --optimize-autoloader
php artisan key:generate 
php artisan migrate:fresh --seed

## Misc configurations

- To configure pgAdmin, you need to add a new server with the following credentials:
  - Host: postgresql's container IP **(docker inspect henriquebarbosa-db | grep IPAddress)**
  - Port: 5432
  - Username: pguser
  - Password: pgpass

  You may use the same obtained IP if you prefer Adminer instead of pgAdmin.