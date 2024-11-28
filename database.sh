#!/bin/bash

docker-entrypoint.sh postgres &
./wait_for_database.sh

wait