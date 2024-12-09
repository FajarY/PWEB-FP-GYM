#!/bin/bash

./wait_for_it.sh -h $DB_HOST -p $DB_PORT -t 30 -s && {
    sleep 1;
    #Runner
    php ./database/index.php

    #Checker
    PGPASSWORD=$POSTGRES_PASSWORD psql -h $DB_HOST -p $DB_PORT  -U $POSTGRES_USER -d $POSTGRES_DB  -c "
    SELECT table_name FROM information_schema.tables WHERE table_schema = 'public';
    "

    #Sleep for 1 day, enough for development, and auto die for 1 day in production
    sleep 86400;
}