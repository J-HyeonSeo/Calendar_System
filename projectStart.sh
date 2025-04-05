#!bin/bash

echo 'build php project.'
docker build -t calendar .

echo 'docker compose. php + mysql'
docker compose up -d