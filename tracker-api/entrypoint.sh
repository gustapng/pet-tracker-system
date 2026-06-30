#!/bin/sh
set -e

if [ ! -f "/opt/www/vendor/autoload.php" ]; then
    echo "📦 Hyperf: Dependências não encontradas. Instalando..."
    composer install --no-interaction --no-progress --optimize-autoloader
else
    echo "✅ Hyperf: Dependências já instaladas."
fi

mkdir -p /opt/www/runtime

chmod -R 777 /opt/www/runtime

if [ $# -gt 0 ]; then
    echo "🚀 Executando comando customizado: $@"
    exec "$@"
else
    echo "🚀 Iniciando Hyperf..."
    exec php bin/hyperf.php start
fi