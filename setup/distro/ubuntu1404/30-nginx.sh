#!/usr/bin/env bash
# Environment variables SCRIPTDIR and TEMPDIR are available

ssl_dir=/etc/nginx/ssl
[[ ! -d $ssl_dir ]] && mkdir "$ssl_dir"

if [[ -f "$ssl_dir/server.key" || -f  "$ssl_dir/server.crt" ]]; then
  echo "Skipping nginx self-signing certificate, since one already exists."
else
  openssl req -newkey rsa:4096 -days 365 -nodes -x509 \
    -subj "/C=US/ST=Calfornia/L=Windsor/O=Webird/OU=Department of Silly Walks/CN=*.webird.io" \
    -keyout "$ssl_dir/server.key" \
    -out "$ssl_dir/server.crt"
  [[ $? -ne 0 ]] && exit 1
fi

exit 0
