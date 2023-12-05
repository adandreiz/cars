#Cars API
##Environment setup
Create `docker\.env` and `api\.env` after the `.dist` files.
##Run locally
####TAB-1
Run it and keep an eye on the logs, you will find the answer here if you come to trouble running it locally.
`docker-compose up`

####TAB-2
Get inside the php container
`docker exec -it cars-php bash`

###Install dependencies
`composer install`

##Testing
###Create db and load test Fixtures
`php bin/console --env=test doctrine:database:create`

`php bin/console --env=test doctrine:schema:create`

`php bin/console --env=test doctrine:fixtures:load`

###Run tests
`php bin/phpunit`