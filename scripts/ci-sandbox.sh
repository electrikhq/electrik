#!/usr/bin/env bash
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
ELECTRIK_ROOT="$(cd "${SCRIPT_DIR}/.." && pwd)"
LAB_ROOT="$(cd "${ELECTRIK_ROOT}/.." && pwd)"
SANDBOX="${LAB_ROOT}/electrik-sandbox"

if [[ ! -d "${SANDBOX}" ]]; then
  echo "electrik-sandbox not found at ${SANDBOX}" >&2
  exit 1
fi

cd "${SANDBOX}"

composer install --no-interaction --prefer-dist

if [[ ! -f .env ]]; then
  cp .env.example .env
  php artisan key:generate --ansi
fi

php artisan migrate --force --ansi
php artisan electrik:permissions:sync --teams --ansi

if [[ ! -L public/storage ]]; then
  php artisan storage:link --ansi || true
fi

if [[ -f package.json ]]; then
  npm ci --ignore-scripts 2>/dev/null || npm install --ignore-scripts
  npm run build
fi

php artisan test --ansi
