#!/bin/bash

# Obtém o IP da interface ativa (exemplo para macOS)
IP=$(ipconfig getifaddr en0)

# Verifica se conseguiu obter o IP
if [ -z "$IP" ]; then
    echo "Erro: Unable to retrieve the current Wi-Fi IP address."
    exit 1
fi

# Inicia o servidor Laravel no IP detectado
export APP_URL=http://"$IP":8000
export FRONTEND_URL=http://"$IP":3000

php artisan serve --host="$IP"
