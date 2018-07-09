#!/bin/bash

# shellcheck source=bin/shared
source "$(dirname "$0")/shared.sh"

PARAMS=""
if [ -z "$*" ]; then
    PARAMS="--build"
fi

# Build containers
echo "🔄 Spinning up containers."
if [ -e "docker-custom.yml" ]; then
	docker-compose -f docker-compose.yml -f docker-custom.yml up ${PARAMS} "$@"
else
    docker-compose up ${PARAMS} "$@"
fi
