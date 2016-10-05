'use strict';
let _ = require('lodash');
let path = require('path');
let fs = require('fs');
let yaml = require('js-yaml');
let crypto = require('crypto');
let webpack = require('webpack');
let WebpackDevServer = require('webpack-dev-server');
let ExtractTextPlugin = require('extract-text-webpack-plugin');
let LoaderOptionsPlugin = require('webpack/lib/LoaderOptionsPlugin');
let ProvidePlugin = require('webpack/lib/ProvidePlugin');
let DefinePlugin = require('webpack/lib/DefinePlugin');
let CommonsChunkPlugin = require('webpack/lib/optimize/CommonsChunkPlugin');
let DedupePlugin = require('webpack/lib/optimize/DedupePlugin');
let UglifyJsPlugin = require('webpack/lib/optimize/UglifyJsPlugin');

let projectRoot = path.resolve('..');
let etcRoot = path.join(projectRoot, 'etc');
let appRoot = path.join(projectRoot, 'app');
let devRoot = path.join(projectRoot, 'dev');
let buildRoot = path.join(projectRoot, 'build');
let webpackRoot = path.join(appRoot, 'webpack');
let appModulesRoot = path.join(webpackRoot, 'modules');
let themeRoot = path.join(appRoot, 'theme');
let nodeModulesRoot = path.join(devRoot, 'node_modules');
let projectRootHash = crypto.createHash('md5').update(projectRoot).digest('hex');

let appConfig = yaml.load(fs.readFileSync(webpackRoot + "/config.yml", 'utf8'));

/**
 *
 */
const babelCacheDir = '/tmp/babel-cache-' + projectRootHash;
if (!fs.existsSync(babelCacheDir)) {
  fs.mkdirSync(babelCacheDir);
}

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
  },
  resolve: {
    modules: [appModulesRoot, nodeModulesRoot, themeRoot],
    descriptionFiles: ['package.json'],
    mainFields: ['main', 'browser'],
    mainFiles: ['index'],
    alias: {
      underscore: 'lodash',
      handlebars: 'handlebars/dist/handlebars',
      highlight: 'highlight.js/lib/highlight'
    },
    extensions: [
      '.js',
      '.vue', '.html',
      '.css', '.scss', '.less',
      '.json', '.yml'
    ],
    enforceExtension: false,
    enforceModuleExtension: false
  },
  resolveLoader: {
    modules: [nodeModulesRoot],
    descriptionFiles: ['package.json'],
    mainFields: ['main'],
    mainFiles: ['index'],
    extensions: ['.js'],
    enforceExtension: false,
    enforceModuleExtension: false,
    moduleExtensions: ['-loader']
  },
  plugins: [
    new DefinePlugin({
      VERSION: JSON.stringify(require(devRoot + "/package.json").version),
      WEBPACK_ROOT: JSON.stringify(webpackRoot),
      LOCALE_ROOT: JSON.stringify(appRoot + "/locale"),
      THEME_ROOT: JSON.stringify(appRoot + "/theme"),
    }),
    // new ExtractTextPlugin('css/[name].css', { allChunks: false}),
    new ProvidePlugin({
      _: 'lodash',
      $: 'jquery',
      jQuery: 'jquery'
    }),
    new LoaderOptionsPlugin({
      options: {
        postcss: [
          require("postcss-import")({
            addDependencyTo: webpack,
            path: [themeRoot, appModulesRoot, nodeModulesRoot]
          }),
          require("postcss-url")(),
          require("postcss-cssnext")({
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
    })
  ]
  // Setup each entry chunk to use a common chunk as defined in './app/webpack/config'.
  .concat(commonsChunkPluginArr),
  module: {
    loaders: [
      // {
      //   test: /\.js?$/,
      //   exclude: /node_modules/,
      //   loader: 'babel',
      //   query: {
      //     cacheDirectory: babelCacheDir,
      //     plugins: [
      //       // require.resolve('babel-plugin-transform-runtime')
      //       'babel-plugin-transform-runtime'
      //     ],
      //     presets: [
      //       [
      //         require.resolve('babel-preset-es2015'),
      //         { modules: false }
      //       ],
      //       require.resolve('babel-preset-es2016'),
      //       require.resolve('babel-preset-es2017'),
      //       require.resolve('babel-preset-stage-0')
      //     ]
      //   }
      {
        test: /\.js?$/,
        loaders: 'buble'
      }, {
        test: /\.vue$/,
        loader: "vue"
      }, {
        test: /\.json$/,
        loader: "json"
      }, {
        test: /\.yml$/,
        loaders: ['json', 'yaml']
      }, {
        test: /\.po$/,
        loaders: [
          'json',
          { loader: 'po', query: { format: 'jed1.x'} }
        ]
      }, {
        test: /\.html$/,
        loader: "html"
      }, {
        test: /\.css$/,
        // loader: ExtractTextPlugin.extract("style-loader", "css-loader!postcss-loader")
        loaders: ['style', 'css', 'postcss']
      }, {
        test: /\.less$/,
        // loader: ExtractTextPlugin.extract("style-loader", "css-loader!less-loader")
        loaders: ['style', 'css', 'less']
      }, {
        test: /\.scss$/,
        // loader: ExtractTextPlugin.extract("style-loader", "css-loader!sass-loader")
        loaders: ['style', 'css', 'sass']
      }, {
        test: /\.(png|jpg|gif)$/,
        loader: 'url',
        query: {
          prefix: 'img/',
          limit: '8192'
        }
      }, {
        test: /\.(woff|woff2)$/,
        loader: 'url',
        query: {
          name: 'fonts/[hash].[ext]',
          limit: '10000',
          mimetype: 'application/font-woff'
        }
      }, {
        test: /\.ttf$/,
        loader: 'file',
        query: {
          name: 'fonts/[hash].[ext]'
        }
      }, {
        test: /\.eot$/,
        loader: 'file',
        query: {
          name: 'fonts/[hash].[ext]'
        }
      }, {
        test: /\.svg$/,
        loader: 'file',
        query: {
          name: 'fonts/[hash].[ext]'
        }
      }
    ]
  },
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
 function dev() {
  let config = yaml.load(fs.readFileSync(etcRoot + "/dev_defaults.yml", 'utf8'));
  let configCustom = yaml.load(fs.readFileSync(etcRoot + "/dev.yml", 'utf8'));
  _.merge(config, configCustom);

  let webpackPort = config.dev.webpackPort;
  wpConf.devtool = 'source-map';
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
  }).listen(webpackPort, '0.0.0.0', function(err) {
    if (err) {
    }
  });
}

/**
 *
 */
function build() {
    wpConf.output.path = path.join(projectRoot, 'build', 'public');
    wpConf.plugins.concat([
      new DefinePlugin({DEV: false}),
      new LoaderOptionsPlugin({
        minimize: true,
        debug: false
      }),
      new DedupePlugin(),
      new UglifyJsPlugin()
    ]);

    webpack(wpConf, function(err, stats) {
      if (err) {
      }
    });
}

if (process.env.NODE_ENV !== 'production') {
  dev()
} else {
  build()
}
