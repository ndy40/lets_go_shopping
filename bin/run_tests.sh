#!/usr/bin/env bash

ROOT_DIR=$(pwd)

# this is the same as phpunit.xml
JWT_PASSPHRASE=70823db480215e7b83dd5a40696f0e2b
JWT_DIR="$ROOT_DIR/server/var/jwt"

export APP_ENV=test
export APP_DEBUG=true

#  Test if pem file exists
echo GENERATING KEYS

if [ ! -f "$JWT_DIR/localhost-key.pem" ]; then
    echo 'Generating Public \ Private Key'
    mkdir -p $JWT_DIR
    if ! echo "$JWT_PASSPHRASE" | openssl pkey -in "$JWT_DIR/localhost-key.pem" -passin stdin -noout > /dev/null 2>&1; then
        echo "$JWT_PASSPHRASE" | openssl genpkey -out "$JWT_DIR/localhost-key.pem" -pass stdin -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096
        echo "$JWT_PASSPHRASE" | openssl pkey -in "$JWT_DIR/localhost-key.pem" -passin stdin -out "$JWT_DIR/localhost.pem" -pubout
    fi
fi

echo "Installing dev requirements"
composer --working-dir="$ROOT_DIR/server" install --no-ansi -n --profile --no-scripts --no-suggest --no-progress --prefer-dist


echo "Switching to project directory"
cd $ROOT_DIR/server

echo "Running migrations and fixtures"
bin/console d:m:m -n --env=test

echo "Running Tests"
bin/phpunit -c phpunit.xml

 if [ $? -ne 0 ]; then
    echo "Test failed"
    exit 1
 fi

 exit 0


