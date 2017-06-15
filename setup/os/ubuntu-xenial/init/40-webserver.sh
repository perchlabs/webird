#!/usr/bin/env bash
# Environment variables OS_DIR and TEMP_DIR are available

case "$WEBIRD_WEBSERVER" in
  caddy)
    SSL_DIR=/etc/ssl/caddy
    CADDY_PATH=/usr/local/bin/caddy
    CADDY_ETC=/etc/caddy

    mkdir "$TEMP_DIR/caddy"
    cd "$TEMP_DIR/caddy"
    wget https://caddyserver.com/download/linux/amd64 -O caddy.tar.gz
    tar -xf caddy.tar.gz
    install -m 755 -o root -g root caddy $CADDY_PATH

    mkdir -p $CADDY_ETC/{sites-available,sites-enabled}

    # Create simple Caddyfile to import all enabled sites.
    cat > $CADDY_ETC/Caddyfile <<EOF
import /etc/caddy/sites-enabled/*.conf
EOF

    chown -R root:root $CADDY_ETC

    install -m 644 -o root -g root init/linux-systemd/caddy.service /etc/systemd/system/
    systemctl daemon-reload

    # Give the caddy binary the ability to bind to
    # privileged ports (e.g. 80, 443) as a non-root user.
    setcap 'cap_net_bind_service=+ep' $CADDY_PATH

    ;;
  nginx)
    SSL_DIR=/etc/ssl/nginx
    ;;
esac

# Create SSL directory and secure permissions.
[[ ! -d "$SSL_DIR" ]] && mkdir $SSL_DIR
chown -R www-data:root $SSL_DIR
chmod 0770 $SSL_DIR

if [[ -f "$SSL_DIR/server.key" || -f  "$SSL_DIR/server.crt" ]]; then
  echo "Skipping web server self-signing certificate, since one already exists."
else
  openssl req -newkey rsa:4096 -days 365 -nodes -x509 \
    -subj "/C=US/ST=Calfornia/L=Windsor/O=Webird/OU=Department of Silly Walks/CN=*.webird.io" \
    -keyout "$SSL_DIR/server.key" \
    -out "$SSL_DIR/server.crt"
  [[ $? -ne 0 ]] && exit 1
fi

exit 0
