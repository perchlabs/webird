'use strict';
const _ = require('lodash');
const path = require('path');
const fs = require('fs');
const yaml = require('js-yaml');
const crypto = require('crypto');
const webpack = require('webpack');
const WebpackDevServer = require('webpack-dev-server');
// const ExtractTextPlugin = require('extract-text-webpack-plugin');
const LoaderOptionsPlugin = require('webpack/lib/LoaderOptionsPlugin');
const ProvidePlugin = require('webpack/lib/ProvidePlugin');
const DefinePlugin = require('webpack/lib/DefinePlugin');
const CommonsChunkPlugin = require('webpack/lib/optimize/CommonsChunkPlugin');
const DedupePlugin = require('webpack/lib/optimize/DedupePlugin');
const UglifyJsPlugin = require('webpack/lib/optimize/UglifyJsPlugin');

const projectRoot = path.resolve('..');
const etcRoot = path.join(projectRoot, 'etc');
const appRoot = path.join(projectRoot, 'app');
const devRoot = path.join(projectRoot, 'dev');
const buildRoot = path.join(projectRoot, 'build');
const webpackRoot = path.join(appRoot, 'webpack');
const appModulesRoot = path.join(webpackRoot, 'modules');
const themeRoot = path.join(appRoot, 'theme');
const nodeModulesRoot = path.join(devRoot, 'node_modules');
const projectRootHash = crypto.createHash('md5').update(projectRoot).digest('hex');

const appConfig = yaml.load(fs.readFileSync(webpackRoot + '/config.yml', 'utf8'));

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
const entryMap = {};
for (const common of getNamesFromDirectory(`${webpackRoot}/commons`)) {
  entryMap[`commons/${common}`] = `./commons/${common}`;
}
for (const entry of getNamesFromDirectory(`${webpackRoot}/entries`)) {
  entryMap[`entries/${entry}`] = `./entries/${entry}`;
}

/**
 *
 */
const commonsChunkPluginArr = [];
for (const commonName in appConfig.commons) {
  const entryArrPath = appConfig.commons[commonName].map(function(entryName) {
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
const bubbleOptions = {
  objectAssign: 'Object.assign',
  transforms: {
    modules: false,
    forOf: false,
  }
}

/**
 *
 */
const wpConf = {
  cache: true,
  context: webpackRoot,
  entry: entryMap,
  output: {
    path              : `/tmp/webird-${projectRootHash}-webpack`,
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
      highlight: 'highlight.js/lib/highlight'
    },
    extensions: [
      '.js',
      '.vue',
      '.json', '.yml',
      '.css',
    ],
    enforceExtension: false,
    enforceModuleExtension: false
  },
  performance: {
    hints: false
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
      VERSION: JSON.stringify(require(devRoot + '/package.json').version),
      WEBPACK_ROOT: JSON.stringify(webpackRoot),
      LOCALE_ROOT: JSON.stringify(appRoot + '/locale'),
      THEME_ROOT: JSON.stringify(appRoot + '/theme'),
    }),
    // new ExtractTextPlugin('css/[name].css', { allChunks: false}),
    new ProvidePlugin({
      $: 'jquery',
      jQuery: 'jquery'
    }),
    new LoaderOptionsPlugin({
      options: {
        postcss: postcssSetup,
        vue: {
          postcss: postcssSetup,
          buble: bubbleOptions,
        },
      },
    }),
   ]
  // Setup each entry chunk to use a common chunk as defined in './app/webpack/config'.
  .concat(commonsChunkPluginArr),
  module: {
    loaders: [
      {
        test: /\.js?$/,
        loaders: 'buble',
        query: bubbleOptions,
      }, {
        test: /\.vue$/,
        loader: 'vue'
      }, {
        test: /\.json$/,
        loader: 'json'
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
        test: /\.css$/,
        // loader: ExtractTextPlugin.extract('style-loader', 'css-loader!postcss-loader')
        loaders: ['style', 'css', 'postcss']
      }, {
        test: /\.(png|jpg|gif)$/,
        loader: 'url?prefix=img/&limit=8192'
      }, {
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
        test: /\.eot$/,
        loader: 'file',
        query: {
          name: 'fonts/[hash].[ext]'
        }
      }, {
        test: /\.ttf$/,
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
 function postcssSetup(webpack) {
  return [
    require('postcss-import')({
      // addDependencyTo: webpack,
      path: [themeRoot, appModulesRoot, nodeModulesRoot]
    }),
    require('postcss-url')(),
    require('postcss-cssnext')({
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
    require('postcss-browser-reporter')(),
    require('postcss-reporter')(),
  ]
}

/**
 *
 */
function getNamesFromDirectory(filepath) {
  const files = fs.readdirSync(filepath);
  const baseNames = _.chain(files).filter(function(filename) {
    return filename[0] !== '#';
  }).map(function(filename) {
    let entryName, ext;
    if (fs.lstatSync(`${filepath}/${filename}`).isDirectory()) {
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
  const config = yaml.load(fs.readFileSync(`${etcRoot}/dev_defaults.yml`, 'utf8'));
  const configCustom = yaml.load(fs.readFileSync(`${etcRoot}/dev.yml`, 'utf8'));
  _.merge(config, configCustom);

  const webpackPort = config.dev.webpackPort;
  // wpConf.devtool = 'source-map';
  // wpConf.devtool = 'inline-source-map';
  wpConf.plugins.push(new DefinePlugin({DEV: true}));
  wpConf.output.publicPath = "/";

  const devServer = new WebpackDevServer(webpack(wpConf), {
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
