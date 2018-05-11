#!/usr/bin/env bash
# Environment variables OS_DIR and TEMP_DIR are available

[[ ! -d $SSL_DIR ]] && mkdir "$SSL_DIR"

if [[ -f "$SSL_DIR/server.key" || -f  "$SSL_DIR/server.crt" ]]; then
  echo "Skipping nginx self-signing certificate, since one already exists."
else
  openssl req -newkey rsa:4096 -days 365 -nodes -x509 \
    -subj "/C=US/ST=Calfornia/L=Windsor/O=Webird/OU=Department of Silly Walks/CN=*.webird.io" \
    -keyout "$SSL_DIR/server.key" \
    -out "$SSL_DIR/server.crt"
  [[ $? -ne 0 ]] && exit 1
fi

# TODO: Configure gzip in /etc/nginx.conf

exit 0
