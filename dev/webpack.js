'use strict'
const _ = require('lodash')
const path = require('path')
const fs = require('fs')
const crypto = require('crypto')
const webpack = require('webpack')
const WebpackDevServer = require('webpack-dev-server')
// const ExtractTextPlugin = require('extract-text-webpack-plugin')
const LoaderOptionsPlugin = require('webpack/lib/LoaderOptionsPlugin')
const DefinePlugin = require('webpack/lib/DefinePlugin')

const VueLoaderPlugin = require('vue-loader/lib/plugin')

const projectRoot = path.resolve(__dirname + '/..')
const etcRoot = path.join(projectRoot, 'etc')
const appRoot = path.join(projectRoot, 'app')
const devRoot = path.join(projectRoot, 'dev')
const buildRoot = path.join(projectRoot, 'build')
const webpackRoot = path.join(appRoot, 'webpack')
const appModulesRoot = path.join(webpackRoot, 'modules')
const themeRoot = path.join(appRoot, 'theme')
const nodeModulesRoot = path.join(projectRoot, 'node_modules')
const projectRootHash = crypto.createHash('md5').update(projectRoot).digest('hex')

const appConfig = require(`${webpackRoot}/config.json`)

const environment = process.env.NODE_ENV;

/**
 *
 */
const entryMap = {}
for (const entry of getNamesFromDirectory(`${webpackRoot}/entries`)) {
  entryMap[`entries/${entry}`] = `./entries/${entry}`
}

/**
 *  Build constants and combine developer added constants added in app/webpack/config
 */
const constants = {
  VERSION: JSON.stringify(require(`${projectRoot}/package.json`).version),
  LOCALE_ROOT: JSON.stringify(`${appRoot}/locale`),
  THEME_ROOT: JSON.stringify(`${appRoot}/theme`),
}
for (const i in appConfig.constants) {
  constants[i] = JSON.stringify(appConfig.constants[i])
}

/**
 *
 */
const wpConf = {
  mode: environment,
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
      highlight: 'highlight.js/lib/highlight',
    },
    extensions: [
      '.js',
      '.vue',
      '.json',
      '.css',
    ],
    enforceExtension: false,
    enforceModuleExtension: false,
  },
  performance: {
    hints: false,
  },
  resolveLoader: {
    modules: [nodeModulesRoot],
    descriptionFiles: ['package.json'],
    mainFields: ['main'],
    mainFiles: ['index'],
    extensions: ['.js'],
    enforceExtension: false,
    enforceModuleExtension: false,
    moduleExtensions: ['-loader'],
  },
  plugins: [
    new VueLoaderPlugin(),
    // new ExtractTextPlugin('css/[name].css', { allChunks: false}),
    // new LoaderOptionsPlugin({
    //   options: {
    //     postcss: postcssSetup,
    //   },
    // }),
  ],
  module: {
    noParse: [
      /$jquery^/,
    ],
    rules: [
      {
        test: /\.css$/,
        // loader: ExtractTextPlugin.extract('style-loader', 'css-loader!postcss-loader'),
        loaders: [
          'style-loader',
          {
            loader: 'css-loader',
            options: { importLoaders: 1 },
          },
          {
            loader: 'postcss-loader',
            options: {
              config: {
                path: `${devRoot}/postcss.config.js`,
              }
            },
          },
        ],
      },
      {
        test: /\.js$/,
        loader: 'babel-loader',
        exclude: file => (
          /node_modules/.test(file) &&
          !/\.vue\.js/.test(file)
        ),
        options: {
          cwd: projectRoot,
          configFile: `${devRoot}/babel.config.js`,
        },
      },
      {
        test: /\.vue$/,
        loader: 'vue-loader',
      },
      {
        test: /\.po$/,
        loaders: [
          'json-loader',
          {
            loader: 'po',
            query: {
              format: 'jed1.x'
            }
          },
        ],
      },
      {
        test: /\.(png|jpg|gif)$/,
        loader: 'url-loader?prefix=img/&limit=8192',
      },
      {
        test: /\.(png|jpg|gif)$/,
        loader: 'url-loader',
        query: {
          prefix: 'img/',
          limit: '8192',
        }
      },
      {
        test: /\.(woff|woff2)$/,
        loader: 'url-loader',
        query: {
          name: 'fonts/[hash].[ext]',
          limit: '10000',
          mimetype: 'application/font-woff',
        }
      },
      {
        test: /\.eot$/,
        loader: 'file-loader',
        query: {
          name: 'fonts/[hash].[ext]',
        },
      },
      {
        test: /\.ttf$/,
        loader: 'file-loader',
        query: {
          name: 'fonts/[hash].[ext]',
        },
      },
      {
        test: /\.svg$/,
        loader: 'file-loader',
        query: {
          name: 'fonts/[hash].[ext]',
        },
      },
    ],
  },
}

// /**
//  *
//  */
//  function postcssSetup(webpack) {
//   return [
//     require('postcss-import')({
//       // addDependencyTo: webpack,
//       path: [themeRoot, appModulesRoot, nodeModulesRoot],
//     }),
//     require('postcss-url')(),
//     require('postcss-cssnext')({
//       // Defined in './app/webpack/config'
//       import: {
//         path: [themeRoot],
//         // Setup watches on these files
//         onImport: function(files) {
//           files.forEach(this.addDependency)
//         }.bind(this),
//       }
//     }),
//     require('postcss-browser-reporter')(),
//     require('postcss-reporter')(),
//   ]
// }

/**
 *
 */
function getNamesFromDirectory(filepath) {
  const files = fs.readdirSync(filepath)

  return _.chain(files)
    .filter(filename => filename[0] !== '#')
    .map(function(filename) {
      let entryName, ext
      if (fs.lstatSync(`${filepath}/${filename}`).isDirectory()) {
        entryName = filename
      } else {
        ext = path.extname(filename)
        entryName = filename.substr(0, filename.length - ext.length)
      }
      return entryName
    })
    .value()
}

/**
 *
 */
 function dev() {
  const config = require(`${etcRoot}/dev_defaults.json`)
  const configCustom = require(`${etcRoot}/dev.json`)
  _.merge(config, configCustom)

  const webpackPort = config.dev.webpackPort
  // wpConf.devtool = 'source-map'
  // wpConf.devtool = 'inline-source-map'

  // Constants
  Object.assign(constants, {
    DEV: JSON.stringify(true),
    'process.env.NODE_ENV': JSON.stringify('development'),
  })
  wpConf.plugins.push(new DefinePlugin(constants))

  const devServer = new WebpackDevServer(webpack(wpConf), {
    contentBase: devRoot,
    disableHostCheck: true,
    stats: {
      assets: false,
      colors: true,
      children: false,
      chunks: false,
      modules: false,
    }
  }).listen(webpackPort, '0.0.0.0', function(err) {
    if (err) {
    }
  })
}

/**
 *
 */
function build() {

  wpConf.output.path = path.join(projectRoot, 'build', 'public')

  // Constants
  Object.assign(constants, {
    DEV: JSON.stringify(false),
    'process.env.NODE_ENV': JSON.stringify('production'),
  })

  wpConf.plugins = wpConf.plugins.concat([
    new DefinePlugin(constants),
    new LoaderOptionsPlugin({
      minimize: true,
      debug: false,
    }),
  ])

  webpack(wpConf, function(err, stats) {
    if (err) {
    }
  })
}

switch (environment) {
  case 'development':
    dev();
    break;
  case 'production':
    build();
    break;
}
