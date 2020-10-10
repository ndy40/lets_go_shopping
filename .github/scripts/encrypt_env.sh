#!/bin/sh

echo "$env_secret" | base64 -d | tee .env > /dev/null