#!/bin/sh
set -e

if [ ! -f "/var/www/html/vendor/autoload.php" ]; then
    echo "📦 Instalando dependências do Composer (primeira execução)..."
    composer install --no-interaction --optimize-autoloader
else
    echo "✅ Dependências já instaladas, prosseguindo..."
fi

chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache

echo "⏳ Aguardando conexão com MySQL..."
php /var/www/html/wait-for-db.php

# ======== NOVA LÓGICA AQUI ========
if [ "$RUN_MIGRATIONS" = "true" ]; then
    echo "🛠️ Executando Migrations e Seeders (Serviço Principal)..."
    php artisan migrate --seed --force
else
    echo "⏭️ Pulando migrations (Serviço Secundário/Worker)..."
fi
# ==================================

# Executa comandos customizados ou inicia o servidor
if [ $# -gt 0 ]; then
    echo "🚀 Executando comando customizado: $@"
    exec "$@"
else
    echo '===================================================='
    echo '✅ SETUP CONCLUÍDO COM SUCESSO!'
    echo '🌐 Acesse: http://localhost:5173'
    echo '🛑 Parar: docker-compose stop | 🧹 Limpar: docker-compose down -v'
    echo '===================================================='
    echo "🚀 Iniciando Servidor Web Principal..."
    exec php artisan serve --host=0.0.0.0 --port=8000
fi