# Cars API

## Project structure

This is a monorepo containing:

- A file with a Postman collection `CarsAPI.postman_collection.json` , I could have used variables for the collection but didn't want to spend much time on it.
- A Symfony 6.4 API `api/`
- A Docker environment to build and run the API `docker/`

## Environment setup

Copy `docker\.env.dist` and paste as `docker\.env`.

### Run local environment

Build local environment using Docker 

`docker compose build --no-cache`

Run local environment

`docker-compose up`

Get inside the php container

`docker exec -it cars-php bash`

Install libraries and dependencies with composer

`composer install`

Run migrations and load initial colours using fixtures

`php bin/console doctrine:migrations:migrate`

`php bin/console doctrine:fixtures:load`

## Testing

I created a suite of test using DAMA doctrine test bundle, this library allows you to test the code and the database without committing transactions during testing. 

### Create db and load test Fixtures

Copy `api\phpunit.xml.dist` and paste as `api\phpunit.xml`.

`php bin/console --env=test doctrine:database:create`

`php bin/console --env=test doctrine:migrations:migrate`

`php bin/console --env=test doctrine:fixtures:load`

### Run tests with coverage

`php bin/phpunit --coverage-html coverage`

The command will run the tests and create a folder inside `api/` called `coverage` containing a nice html site showing lines of codes covered by tests.

## Improvements

Creating models for `make` with a relation to `car` and a model for `model` with a relation to `make` will improve the design and make validation more solid.

The API could be documented using Swagger, I would use NelmioApiDocBundle.