#!/bin/bash

# shellcheck source=bin/shared
source "$(dirname "$0")/shared.sh"

"$(dirname "$0")"/up.sh --build -d wordpress

while ! dexec cat wp-config.php > /dev/null 2>&1; do
	sleep 2
done

"$(dirname "$0")"/install.sh --wp-plugins --wp-themes "$@"

echo "👌 Done!"
