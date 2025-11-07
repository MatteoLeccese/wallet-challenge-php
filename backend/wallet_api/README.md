# wallet_api

## Purpose
Thin API gateway/proxy that forwards requests to the wallet DB service and injects system API key protection for internal routes.

## Prerequisites
- PHP 8.2^
- Composer PHP
- MySQL server (local install or XAMPP)
- Apache or Nginx (local install or XAMPP)
- Laravel
- MailHog (SMTP development inbox; defaults: SMTP :1025, web UI :8025)

## Setup

First of all you have to config the Apache or Nginx service to expose the project folder, after expose the project then go into the project directory and follow the steps below.

1. Copy env
- cd backend/wallet_api
- cp .env.example .env
- Edit `.env` to set all the variables as needed.

2. Install
- composer install

3. Run the database migrations
- Run:
```bash
php artisan migrate
```


## Run the application

- To run the application use the following command:
```bash
php artisan serve --host=0.0.0.0 --port=8000
```

### Documentation

Open the documentation using Postman. The docs are inside the following folder:

```bash
cd ../documentation
```
