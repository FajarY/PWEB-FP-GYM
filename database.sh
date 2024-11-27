#!/bin/bash

docker-entrypoint.sh mysqld &

./wait_for_database.sh

wait