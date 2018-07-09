#!/bin/bash

# shellcheck source=bin/shared
source "$(dirname "$0")/shared.sh"

# Stop and remove containers
echo "⛔️ Stopping and removing containers..."
docker-compose down "$@"
echo "👌 Done!"
