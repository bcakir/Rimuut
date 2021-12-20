## Rimuut case study

Used Packages:
```
 - Passport (for authentication)
 - Spatie (for user role)
 - Swagger (for api documentation)
```
 
Local workspace:
```
 $ mkdir myapp
 $ cd myapp
 $ git clone https://github.com/bcakir/Rimuut.git
 $ cd Rimuut
 $ docker-compose up
```

Getting started:
```
 $ docker-compose exec myapp php artisan passport:install
 $ docker-compose exec myapp php artisan db:seed --class=CreateInitialDataSeeder
```

Serve address:
```
 http://localhost:3000/
```

API documentation:
```
 http://localhost:3000/api/documentation
```

Run cron job:
```
 $ docker-compose exec myapp php artisan invoice:expired
```
