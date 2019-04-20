#!/usr/bin/env bash
# Environment variables SETUP_ROOT_DIR, OS_DIR and TEMP_DIR are available

sudo systemctrl restart nginx
sudo systemctrl restart mariadb
sudo systemctrl restart "php${PHP_VERSION}-fpm"
