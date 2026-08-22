#!/usr/bin/env bash
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
ELECTRIK_ROOT="$(cd "${SCRIPT_DIR}/.." && pwd)"
LAB_ROOT="$(cd "${ELECTRIK_ROOT}/.." && pwd)"
SANDBOX="${LAB_ROOT}/electrik-sandbox"

chmod +x "${ELECTRIK_ROOT}/scripts/ci-package.sh"
"${ELECTRIK_ROOT}/scripts/ci-package.sh"

if [[ -d "${SANDBOX}" ]]; then
  chmod +x "${ELECTRIK_ROOT}/scripts/ci-sandbox.sh"
  "${ELECTRIK_ROOT}/scripts/ci-sandbox.sh"
else
  echo "electrik-sandbox not found; skipped integration tests."
fi
