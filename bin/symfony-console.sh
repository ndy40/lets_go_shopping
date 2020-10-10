#!/usr/bin/env bash

echo "Running command against PHP Container .. php bin/console $@"

docker-compose exec api bin/console $@