#!/bin/sh
set -e

if [ ! -d "/app/node_modules" ] || [ -z "$(ls -A /app/node_modules)" ]; then
    echo "📦 Vue: Instalando dependências nativas para Linux (Alpine)..."
    npm install
else
    echo "✅ Vue: Dependências Node já instaladas."
fi

echo "🚀 Iniciando Vite (Front-End)..."
exec npm run dev -- --host 0.0.0.0