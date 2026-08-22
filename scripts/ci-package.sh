#!/usr/bin/env bash
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
ELECTRIK_ROOT="$(cd "${SCRIPT_DIR}/.." && pwd)"

cd "${ELECTRIK_ROOT}"

composer install --no-interaction --prefer-dist

if [[ ! -f phpunit.xml ]]; then
  cp phpunit.xml.dist phpunit.xml
fi

./vendor/bin/phpunit --colors=always
