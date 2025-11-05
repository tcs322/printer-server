#!/bin/bash
# Caminho absoluto até o diretório onde está o printer-server.php
cd "$(dirname "$0")"

# Porta configurável (altere se desejar)
PORTA=8051

# Inicia o servidor embutido do PHP
# O --docroot garante que o printer-server.php será servido corretamente
php -S 0.0.0.0:$PORTA printer-server.php
