############################################################
# Dockerfile to build Webird container images
############################################################

FROM ubuntu:18.04
MAINTAINER David Schissler

ADD . /opt/webird

RUN \
  export DATABASE_PASSWORD='open' && \
  /opt/webird/setup/provision-system.sh ubuntu-xenial

RUN \
  ln -s /opt/webird/dev/run /usr/local/bin/webird-dev && \
  ln -s /opt/webird/dist/run /usr/local/bin/webird && \
  useradd -G www-data -s /bin/bash --home /opt/webird webird && \
  chown -R webird.www-data /opt/webird

USER webird
RUN \
  export HOME=/opt/webird && \
  /opt/webird/setup/install-local-packages.sh

USER root
RUN \
  export DEBIAN_FRONTEND=noninteractive && \
  apt-get install -y daemontools && \
  mkdir  mkdir -p /etc/dockerservices/nginx && \
  mkdir  mkdir -p /etc/dockerservices/mysql && \
  echo "#!/bin/bash\nexec /usr/sbin/nginx" > /etc/dockerservices/nginx/run && \
  echo "#!/bin/bash\nexec /usr/sbin/mysqld" > /etc/dockerservices/mysql/run && \
  chmod -R +x /etc/dockerservices

# Webserver ports
EXPOSE 80:8080
EXPOSE 443

# Dev Webpack
EXPOSE 8091
# Dev Websocket
EXPOSE 8092

# Dist Websocket
EXPOSE 8192

# ENTRYPOINT ["/usr/bin/svscan", "/etc/dockerservices/"]
