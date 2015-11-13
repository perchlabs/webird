'use strict';
var _ = require('lodash');
var path = require('path');
var fs = require('fs');
var yaml = require('js-yaml');
var crypto = require('crypto');
var gulp = require('gulp');
var gutil = require('gulp-util');
var webpack = require('webpack');
var WebpackDevServer = require('webpack-dev-server');
var ExtractTextPlugin = require('extract-text-webpack-plugin');
var ResolverPlugin = require('webpack/lib/ResolverPlugin');
var ProvidePlugin = require('webpack/lib/ProvidePlugin');
var DefinePlugin = require('webpack/lib/DefinePlugin');
var CommonsChunkPlugin = require('webpack/lib/optimize/CommonsChunkPlugin');
var DedupePlugin = require('webpack/lib/optimize/DedupePlugin');
var UglifyJsPlugin = require('webpack/lib/optimize/UglifyJsPlugin');
// PostCSS
var cssnextPlugin = require('cssnext');

var projectRoot = path.resolve('..');
var etcRoot = path.join(projectRoot, 'etc');
var appRoot = path.join(projectRoot, 'app');
var devRoot = path.join(projectRoot, 'dev');
var distRoot = path.join(projectRoot, 'dist');
var webpackRoot = path.join(appRoot, 'webpack');
var appModulesRoot = path.join(webpackRoot, 'modules');
var themeRoot = path.join(appRoot, 'theme');
var bowerRoot = path.join(devRoot, 'bower_components');
var nodeModulesRoot = path.join(devRoot, 'node_modules');
var projectRootHash = crypto.createHash('md5').update(projectRoot).digest('hex');

var appConfig = yaml.load(fs.readFileSync(webpackRoot + "/config.yml", 'utf8'));

/**
 *
 */
var entryMap = {};
for (let common of getNamesFromDirectory(`${webpackRoot}/commons`)) {
    entryMap[`commons/${common}`] = `./commons/${common}`;
}
for (let entry of getNamesFromDirectory(`${webpackRoot}/entries`)) {
    entryMap[`entries/${entry}`] = `./entries/${entry}`;
}

/**
 *
 */
var commonsChunkPluginArr = [];
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
var wpConf = {
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
            '.html', '.nunj',
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
                test: /\.nunj$/,
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
    postcss: function() {
        // Add more PostCSS plugins from the list: https://github.com/postcss/postcss
        // Be careful about the plugin order
        return [
            cssnextPlugin({
                // Defined in './app/webpack/config'
                browsers: appConfig.browsers,
                import: {
                    // Setup watches on these files
                    onImport: function (files) {
                        files.forEach(this.addDependency);
                    }.bind(this)
                }
            })
        ];
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

gulp.task('webpack:dev-server', function(callback) {
    let config = yaml.load(fs.readFileSync(etcRoot + "/dev_defaults.yml", 'utf8'));
    let configCustom = yaml.load(fs.readFileSync(etcRoot + "/dev.yml", 'utf8'));
    _.merge(config, configCustom);

    let webpackPort = config.dev.webpackPort;
    wpConf.devtool = 'source-map';
    wpConf.debug = true;
    wpConf.plugins.push(new DefinePlugin({DEV: true}));
    wpConf.output.publicPath = "http://" + config.site.domain[0] + "/";

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
});

/**
 *
 */
gulp.task('webpack:build', function(callback) {
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
});
