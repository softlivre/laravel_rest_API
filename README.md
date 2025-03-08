# laravel_rest_API
A REST API built with Laravel as an assessment project for company 'P'.

# Deployment instructions
## Requirements
- Docker

## Steps
1. Clone the repository

```bash
git clone https://github.com/softlivre/laravel_rest_API.git .
```

2. set your credentials, copy .env.example to .env

```bash
# in the root directory
cp .env.example .env

# in the src directory
cd src
cp .env.example .env
```

3. Run the following command in the root directory of the project. This will build all necessary containers and start the services.

```bash
docker-compose up -d --build
```

4. Now enter the app container and run the following commands to create the application.

```bash
docker compose exec app bash
composer install --prefer-dist --optimize-autoloader
php artisan key:generate 
php artisan migrate
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