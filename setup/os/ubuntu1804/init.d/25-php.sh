#!/usr/bin/env bash
# Environment variables OS_DIR and TEMP_DIR are available

# phpConf cli memory_limit 512M
# phpConfAll memory_limit 512M

# FPM specific setup.
usingFpm=$(phpIsUsingSapi fpm)
if [[ "$usingFpm" -eq 0 ]]; then

  pooldConf=${PHP_ETC}/fpm/pool.d/www.conf
  fpmSockFile=/run/php/php${PHP_VERSION}-fpm.sock

  # FPM setup to listen on TCP/IP instead of socket file
  grep "^listen = $fpmSockFile" "$pooldConf" > /dev/null
  if [[ $? -eq 0 ]]; then
    sudo sed -i "\|^listen = ${fpmSockFile}|s|^|;|" $pooldConf
    sudo sed -i "\|;listen = ${fpmSockFile}|a listen = 127.0.0.1:9000" $pooldConf
  fi
fi
