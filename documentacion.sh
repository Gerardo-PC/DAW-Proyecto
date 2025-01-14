#!/bin/bash
# Necesita en ruta PhpDocumentor.
# Descargar con:
# wget https://phpdoc.org/phpDocumentor.phar
# chmod +x phpDocumentor.phar
# Información: https://phpdoc.org/

if [ ! -f "./phpDocumentor.phar" ]; then
    wget https://phpdoc.org/phpDocumentor.phar
    chmod +x phpDocumentor.phar
fi

./phpDocumentor.phar -c phpdoc.xml

xdg-open ./docs/index.html