#!/usr/bin/env bash

# Any parameter passed in will be sent to composer. This is just a shorthand for running
# composer commands in the docker container.

docker-compose exec server_php composer $@