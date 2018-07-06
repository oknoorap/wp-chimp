#!/bin/bash

# shellcheck source=bin/shared
source "$(dirname "$0")/shared.sh"

# drun: docker-compose run
drun "$@"
