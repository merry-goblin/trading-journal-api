
# trading-journal-api
An API to log trades and effectiveness of a trading strategy

## Install

### Vendors
composer install

### Public and private keys
openssl genpkey -algorithm RSA -out config/jwt/private.pem -aes256  
openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout

## Tests

### unit tests
php bin/phpunit --testsuite unit

### integration tests
php bin/phpunit --testsuite integration
