#!/bin/bash

# Exit if any command fails
set -e

trap "down ; exit" INT TERM
trap "kill 0" EXIT

function down () {
	docker compose -f dev/docker-compose.yml --project-directory . --env-file .env down
}

docker compose -f dev/docker-compose.yml --project-directory . --env-file .env up > /dev/null

wait
