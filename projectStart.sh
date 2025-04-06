#!bin/bash

cd "$(dirname "$0")"

cd mysql

docker build -t calendar-db .

cd ..

echo 'build php project.'
docker build -t calendar .

echo 'docker compose. php + mysql'
docker compose up -d