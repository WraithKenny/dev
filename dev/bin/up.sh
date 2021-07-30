#!/bin/bash

# Exit if any command fails
set -e

trap "down ; exit" INT TERM
trap "kill 0" EXIT

function down () {
	docker compose -f dev/docker-compose.yml --project-directory . down
}

docker compose -f dev/docker-compose.yml --project-directory . up > /dev/null

wait
