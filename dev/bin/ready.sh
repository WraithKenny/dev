#!/bin/bash

# Exit if any command fails
set -e

# Wait until the docker containers are setup properely
until [ "$(curl -m 1 -sLI 'http://localhost' | grep 'HTTP')" != "" ]; do sleep 1; done
echo "Docker Ready."
