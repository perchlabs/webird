# Webpack app configuration
You may change this configuration file type to; litcoffee, js, coffee or json

## Common Entry points
Attach entry points to a common code file.

    commons =
      init_complex: ['admin', 'public', 'angular', 'marionette', 'websocket']

## Constants
These constants are available in your Webpack CommonJS code and are evaluated at
build time. Anything that can be evaluated as true as (value === value) will
be removed from the final build by the optimizer/uglifier.

    constants =
      HELLO_WORLD_CONSTANT: JSON.stringify 'Hello World'

Note: THEME_ROOT, LOCALE_ROOT constants are defined in `./dev/gulpfile.webpack.cofee`

    # Export Data
    module.exports =
      commons: commons
      constants: constants
