#!/usr/bin/env bash

if [[ -z "$DATABASE_PASSWORD" ]]; then
  export DATABASE_PASSWORD=open
fi


export PHP_VERSION=7.2
export NODE_VERSION=8.11.1

# export PHALCON_COMPILE=

export PHALCON_COMPILE=tarball
export PHALCON_VERSION=3.3.2

# export PHALCON_COMPILE=tree
# export PHALCON_VERSION=3.4.x

export ZEPHIR_VERSION=0.10.9
export ZEPHIR_PARSER_VERSION=1.1.2

export PHP_ETC=/etc/php/$PHP_VERSION
export NGINX_ETC=/etc/nginx
export SSL_DIR=$NGINX_ETC/ssl

export DEBIAN_FRONTEND=noninteractive
