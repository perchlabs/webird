'use strict';
let _ = require('lodash');
let path = require('path');
let fs = require('fs');
let yaml = require('js-yaml');
let crypto = require('crypto');
let gulp = require('gulp');
let gutil = require('gulp-util');
let webpack = require('webpack');
let WebpackDevServer = require('webpack-dev-server');
let ExtractTextPlugin = require('extract-text-webpack-plugin');
let ResolverPlugin = require('webpack/lib/ResolverPlugin');
let ProvidePlugin = require('webpack/lib/ProvidePlugin');
let DefinePlugin = require('webpack/lib/DefinePlugin');
let CommonsChunkPlugin = require('webpack/lib/optimize/CommonsChunkPlugin');
let DedupePlugin = require('webpack/lib/optimize/DedupePlugin');
let UglifyJsPlugin = require('webpack/lib/optimize/UglifyJsPlugin');

let projectRoot = path.resolve('..');
let etcRoot = path.join(projectRoot, 'etc');
let appRoot = path.join(projectRoot, 'app');
let devRoot = path.join(projectRoot, 'dev');
let distRoot = path.join(projectRoot, 'dist');
let webpackRoot = path.join(appRoot, 'webpack');
let appModulesRoot = path.join(webpackRoot, 'modules');
let themeRoot = path.join(appRoot, 'theme');
let bowerRoot = path.join(devRoot, 'bower_components');
let nodeModulesRoot = path.join(devRoot, 'node_modules');
let projectRootHash = crypto.createHash('md5').update(projectRoot).digest('hex');

let appConfig = yaml.load(fs.readFileSync(webpackRoot + "/config.yml", 'utf8'));

/**
 *
 */
let entryMap = {};
for (let common of getNamesFromDirectory(`${webpackRoot}/commons`)) {
  entryMap[`commons/${common}`] = `./commons/${common}`;
}
for (let entry of getNamesFromDirectory(`${webpackRoot}/entries`)) {
  entryMap[`entries/${entry}`] = `./entries/${entry}`;
}

/**
 *
 */
let commonsChunkPluginArr = [];
for (let commonName in appConfig.commons) {
  let entryArrPath = appConfig.commons[commonName].map(function(entryName) {
    return `entries/${entryName}`;
  });
  commonsChunkPluginArr.push(new CommonsChunkPlugin({
    name    : `commons/${commonName}`,
    filename: 'js/[name].js',
    chunks  : entryArrPath
  }));
}

/**
 *
 */
let wpConf = {
  cache: true,
  context: webpackRoot,
  entry: entryMap,
  output: {
    path              : "/tmp/webird-" + projectRootHash + "-webpack",
    publicPath        : '/',
    filename          : 'js/[name].js',
    chunkFilename     : 'js/chunk/[id].js',
    namedChunkFilename: 'js/[name].js'
  },
  resolve: {
    root: [appModulesRoot, bowerRoot, nodeModulesRoot, themeRoot],
    modulesDirectories: [appModulesRoot, 'node_modules', 'bower_components'],
    alias: {
      underscore: 'lodash',
      handlebars: 'handlebars/dist/handlebars',
      highlight: 'highlight.js/lib/highlight'
    },
    extensions: [
      '',
      '.js', '.coffee',
      '.html', '.njk',
      '.css', '.scss', '.less',
      '.json', '.yml'
    ]
  },
  resolveLoader: {
    root: nodeModulesRoot
  },
  plugins: [
    new DefinePlugin({
      VERSION: JSON.stringify(require(devRoot + "/package.json").version),
      WEBPACK_ROOT: JSON.stringify(webpackRoot),
      LOCALE_ROOT: JSON.stringify(appRoot + "/locale"),
      THEME_ROOT: JSON.stringify(appRoot + "/theme"),
    }),
    new ExtractTextPlugin('css/[name].css', { allChunks: false}),
    new ProvidePlugin({
      _: 'lodash',
      $: 'jquery',
      jQuery: 'jquery'
    }),
    new ResolverPlugin([new ResolverPlugin.DirectoryDescriptionFilePlugin("bower.json", ["main"])], ["normal", "loader"]),
    new DedupePlugin()
  ]
  // Setup each entry chunk to use a common chunk as defined in './app/webpack/config'.
  .concat(commonsChunkPluginArr),
  module: {
    noParse: [
      path.join(bowerRoot, "/lodash"),
      path.join(bowerRoot, "/jquery"),
      path.join(bowerRoot, "/bootstrap"),
      path.join(bowerRoot, "/angular"),
      path.join(bowerRoot, "/angular-ui-router"),
      path.join(bowerRoot, "/angular-cookies"),
      path.join(bowerRoot, "/angular-resource")
    ],
    loaders: [
      {
        test: /[\/]angular\.js$/,
        loader: "exports?angular"
      }, {
        test: /jquery\.js$/,
        loader: "expose?jQuery!expose?$"
      }, {
        test: /\.js?$/,
        exclude: /(node_modules|bower_components)/,
        loader: 'babel',
        query: {
          optional: ['runtime'],
          stage: 0,
          cacheDirectory: '/tmp'
        }
      }, {
        test: /\.coffee$/,
        loader: "coffee"
      }, {
        test: /\.json$/,
        loader: "json"
      }, {
        test: /\.yml$/,
        loader: "json!yaml"
      }, {
        test: /\.po$/,
        loader: "json!po?format=jed1.x"
      }, {
        test: /\.html$/,
        loader: "html"
      }, {
        test: /\.njk$/,
        loader: "nunjucks",
        query: {
          config: `${devRoot}/nunjucks.config.js`,
          // Don't show the 'Cannot configure nunjucks environment before precompile' warning
          // This can be made quite if you understand the implications
          // quiet: true
        }
      }, {
        test: /\.css$/,
        loader: ExtractTextPlugin.extract("style-loader", "css-loader!postcss-loader")
      }, {
        test: /\.less$/,
        loader: ExtractTextPlugin.extract("style-loader", "css-loader!less-loader")
      }, {
        test: /\.scss$/,
        loader: ExtractTextPlugin.extract("style-loader", "css-loader!sass-loader")
      }, {
        test: /\.(png|jpg|gif)$/,
        loader: 'url?prefix=img/&limit=8192'
      }, {
        test: /\.(woff|woff2)$/,
        loader: "url?name=fonts/[hash].[ext]&limit=10000&mimetype=application/font-woff"
      }, {
        test: /\.ttf$/,
        loader: "file?name=fonts/[hash].[ext]"
      }, {
        test: /\.eot$/,
        loader: "file?name=fonts/[hash].[ext]"
      }, {
        test: /\.svg$/,
        loader: "file?name=fonts/[hash].[ext]"
      }
    ]
  },
  postcss: function (webpack) {
    return [
      require("postcss-import")({
        addDependencyTo: webpack,
        path: [themeRoot, appModulesRoot, nodeModulesRoot]
      }),
      require("postcss-url")(),
      require("postcss-cssnext")({
        // Defined in './app/webpack/config'
        browsers: appConfig.browsers,
        import: {
          path: [themeRoot],
          // Setup watches on these files
          onImport: function(files) {
            files.forEach(this.addDependency);
          }.bind(this)
        }
      }),
      require("postcss-browser-reporter")(),
      require("postcss-reporter")(),
    ]
  }
};

/**
 *
 */
function getNamesFromDirectory(filepath) {
  let files = fs.readdirSync(filepath);
  let baseNames = _.chain(files).filter(function(filename) {
    return filename[0] !== '#';
  }).map(function(filename) {
    let entryName, ext;
    if (fs.lstatSync(filepath + "/" + filename).isDirectory()) {
      entryName = filename;
    } else {
      ext = path.extname(filename);
      entryName = filename.substr(0, filename.length - ext.length);
    }
    return entryName;
  }).value();
  return baseNames;
};

/**
 *
 */
const dev = gulp.series(function() {
  let config = yaml.load(fs.readFileSync(etcRoot + "/dev_defaults.yml", 'utf8'));
  let configCustom = yaml.load(fs.readFileSync(etcRoot + "/dev.yml", 'utf8'));
  _.merge(config, configCustom);

  let webpackPort = config.dev.webpackPort;
  wpConf.devtool = 'source-map';
  wpConf.debug = true;
  wpConf.plugins.push(new DefinePlugin({DEV: true}));
  wpConf.output.publicPath = "http://" + config.site.domains[0] + "/";

  let devServer = new WebpackDevServer(webpack(wpConf), {
    contentBase: devRoot,
    stats: {
      assets: false,
      colors: true,
      children: false,
      chunks: false,
      modules: false
    }
  }).listen(webpackPort, 'localhost', function(err) {
    if (err) {
      throw new gutil.PluginError('webpack-dev-server', err);
    }
  });
})

/**
 *
 */
const build = gulp.series(function(callback) {
    wpConf.output.path = path.join(projectRoot, 'dist', 'public');
    wpConf.plugins.concat([
      new DefinePlugin({DEV: false}),
      new UglifyJsPlugin()
    ]);

    webpack(wpConf, function(err, stats) {
      if (err) {
        throw new gutil.PluginError('webpack:build', err);
      }
      gutil.log('[webpack:build]', stats.toString({
        colors: true
      }));
      callback();
    });

})

module.exports = {
  dev,
  build
}
