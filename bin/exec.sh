#!/bin/bash

# shellcheck source=bin/shared
source "$(dirname "$0")/shared.sh"

# dexec: docker-compose exec
dexec "$@"
