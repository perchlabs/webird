#!/usr/bin/env bash
# Environment variables OS_DIR and TEMP_DIR are available

[[ -z "$NGINX_INTEREST" ]] && exit 0

echo -e "${COLOR_SECTION}*** Nginx ***${TEXT_RESET}"

if [[ -z "$NGINX_ETC" ]]; then
  >&2 echo "Skipping Nginx setup because the nginx etc path variable is not set."
  exit 1
fi

NGINX_SSL="${NGINX_ETC}/ssl"

[[ ! -d "$NGINX_SSL" ]] && sudo mkdir "$NGINX_SSL"

if [[ -f "$NGINX_SSL/server.key" || -f  "$NGINX_SSL/server.crt" ]]; then
  echo "Skipping nginx self-signing certificate, since one already exists."
else
  sudo openssl req -newkey rsa:4096 -days 365 -nodes -x509 \
    -subj "/C=US/ST=Calfornia/L=Windsor/O=Webird/OU=Department of Silly Walks/CN=*.webird.io" \
    -keyout "$NGINX_SSL/server.key" \
    -out "$NGINX_SSL/server.crt"
  [[ $? -ne 0 ]] && exit 1
fi

# TODO: Configure gzip in /etc/nginx.conf

exit 0
