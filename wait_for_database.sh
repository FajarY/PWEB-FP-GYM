#!/bin/bash
./wait_for_it.sh -h $DB_HOST -p $DB_PORT -t 30 -s && {
    #Migration & Seeding Here
    psql -U $POSTGRES_USER -d $POSTGRES_DB -c "
    DROP TABLE IF EXISTS workout_logs_exercises;
    DROP TABLE IF EXISTS workout_plans_exercises;
    DROP TABLE IF EXISTS workout_plans;
    DROP TABLE IF EXISTS workout_logs;
    DROP TABLE IF EXISTS users;
    DROP TABLE IF EXISTS exercises;
    
    CREATE TABLE users (
        id CHAR(36) NOT NULL PRIMARY KEY,
        username VARCHAR(128) NOT NULL,
        password CHAR(60) NOT NULL
    );

    CREATE TABLE exercises (
        id CHAR(36) NOT NULL PRIMARY KEY,
        name VARCHAR(128) NOT NULL,
        score_multiplier DECIMAL(10, 2) NOT NULL
    );

    CREATE TABLE workout_plans (
        id CHAR(36) NOT NULL PRIMARY KEY,
        name VARCHAR(128) NOT NULL,
        users_id CHAR(36) NOT NULL,

        CONSTRAINT fk_workout_plans_users FOREIGN KEY (users_id) REFERENCES users(id)
    );

    CREATE TABLE workout_logs (
        id CHAR(36) NOT NULL PRIMARY KEY,
        name VARCHAR(128) NOT NULL,
        users_id CHAR(36) NOT NULL,

        CONSTRAINT fk_workout_logs_users FOREIGN KEY (users_id) REFERENCES users(id)
    );

    CREATE TABLE workout_plans_exercises (
        workout_plans_id CHAR(36) NOT NULL,
        exercises_id CHAR(36) NOT NULL,
        kg DECIMAL(4, 2) NOT NULL,
        reps INT NOT NULL,

        CONSTRAINT fk_workout_plans_exercises_workout_plans FOREIGN KEY (workout_plans_id) REFERENCES workout_plans(id),
        CONSTRAINT fk_workout_plans_exercises_exercises FOREIGN KEY (exercises_id) REFERENCES exercises(id),
        PRIMARY KEY (workout_plans_id, exercises_id)
    );

    CREATE TABLE workout_logs_exercises (
        workout_logs_id CHAR(36) NOT NULL,
        exercises_id CHAR(36) NOT NULL,
        kg DECIMAL(4, 2) NOT NULL,
        reps INT NOT NULL,

        CONSTRAINT fk_workout_logs_exercises_workout_logs FOREIGN KEY (workout_logs_id) REFERENCES workout_logs(id),
        CONSTRAINT fk_workout_logs_exercises_exercises FOREIGN KEY (exercises_id) REFERENCES exercises(id),
        PRIMARY KEY (workout_logs_id, exercises_id)
    );

    SELECT table_name FROM information_schema.tables WHERE table_schema = 'public';
    "
}