#!/usr/bin/env bash
# Environment variables SETUP_ROOT_DIR, OS_DIR and TEMP_DIR are available

sudo systemctl restart nginx
sudo systemctl restart mariadb
sudo systemctl restart "php${PHP_VERSION}-fpm"
