#!/bin/bash
./wait_for_it.sh -h $DB_HOST -p $MYSQL_PORT -t 30 -s && {
    #Acccess
    mysql --password="$MYSQL_ROOT_PASSWORD" --execute="GRANT ALL PRIVILEGES ON $MYSQL_DATABASE.* TO '$MYSQL_USER'@'%';
    "

    #Migration & Seeding Here
    mysql --user="$MYSQL_USER" --password="$MYSQL_PASSWORD" --execute="CONNECT $MYSQL_DATABASE;
    
    "
}