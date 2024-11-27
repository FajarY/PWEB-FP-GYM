#!/bin/bash

npx tailwindcss -i ./input.css -o ./public/output.css
docker compose up --build --watch;