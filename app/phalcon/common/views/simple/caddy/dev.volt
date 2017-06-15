###################################################################
# You are encouraged to modify the source template and regenerate #
###################################################################

{{config.site.domains[0]}}:8080 {
#  tls self_signed
  tls off
  tls {
    alpn http/1.1
  }

  root {{config.dev.path.devDir}}public
  gzip {
#    not /websocket
  }

  fastcgi / 127.0.0.1:9000 php

#  proxy /webpack-dev-server localhost:{{config.dev.webpackPort}} {
#    transparent
#  }
  proxy /webpack-dev-server.js localhost:{{config.dev.webpackPort}} {
    transparent
  }
  proxy /js/ localhost:{{config.dev.webpackPort}} {
    transparent
  }
  proxy /fonts/ localhost:{{config.dev.webpackPort}} {
    transparent
  }
  proxy /css/ localhost:{{config.dev.webpackPort}} {
    transparent
  }

  proxy /websocket localhost:{{config.app.wsPort}} {
    websocket
    insecure_skip_verify
  }

  rewrite / {
    if {path} not_match ^\/(assets|assets__|css|js|fonts|webpack-dev-server.js)
    r /(.*)
    to {path} index.php?url={1}&{query}
    # to /{path} path /index.php?url={path}&{query}{fragment}
  }

}
