'use strict';

const webpack = require('webpack');
const autoprefixer = require('autoprefixer');
const AssetsPlugin = require('assets-webpack-plugin');
const BrowserSyncPlugin = require('browser-sync-webpack-plugin');
const CleanWebpackPlugin = require('clean-webpack-plugin');
const ExtractTextPlugin = require('extract-text-webpack-plugin');
const FriendlyErrorsPlugin = require('friendly-errors-webpack-plugin');
const CopyWebpackPlugin = require('copy-webpack-plugin');
const path = require('path');
const fs = require('fs');

// Make sure any symlinks in the project folder are resolved:
// https://github.com/facebookincubator/create-react-app/issues/637
const appDirectory = fs.realpathSync(process.cwd());

function resolveApp(relativePath) {
    return path.resolve(appDirectory, relativePath);
}

const paths = {
    appSrc: resolveApp('src'),
    appBuild: resolveApp('dist'),
    appIndexJs: resolveApp('src/core.js'),
    appNodeModules: resolveApp('node_modules'),
    appImages: resolveApp('src/images'),
    appFonts: resolveApp('src/fonts'),
};

const DEV = process.env.NODE_ENV === 'development';

module.exports = {
  bail: !DEV,
  // We generate sourcemaps in production. This is slow but gives good results.
  // You can exclude the *.map files from the build during deployment.
  target: 'web',
  devtool: DEV ? 'cheap-eval-source-map' : 'source-map',
  entry: {
    core: paths.appIndexJs              // Core scripts/styles
  },
  externals: {
    jquery: 'jQuery'
  },
  output: {
    path: paths.appBuild,
    filename: DEV ? 'js/[name].js' : 'js/[name].[hash:8].js',
  },
  module: {
    rules: [
      // Disable require.ensure as it's not a standard language feature.
      { parser: { requireEnsure: false } },
      // Transform ES6 with Babel
      {
        test: /\.js?$/,
        loader: 'babel-loader',
        include: paths.appSrc,
      },
      {
        test: /.scss$/,
        use: ExtractTextPlugin.extract({
          fallback: 'style-loader',
          use: [
            {
              loader: 'css-loader',
              options: {
                importLoaders: 1,
                minimize: !DEV
              },
            },
            {
              loader: 'postcss-loader',
              options: {
                ident: 'postcss', // https://webpack.js.org/guides/migrating/#complex-options
                plugins: () => [
                  autoprefixer({
                    browsers: [
                      '>1%',
                      'last 4 versions',
                      'Firefox ESR',
                      'not ie < 9', // React doesn't support IE8 anyway
                    ],
                  }),
                ],
              },
            },
            'sass-loader',
          ],
        }),
      },
      {
        test: /\.(jpg|jpeg|png|gif|svg)$/,
        use: {
          loader: "file-loader",
          options: {
            name: '../images/[name].[ext]',
          },
        },
      },
      {
        test: /\.(ttf|eot|woff|woff2)$/,
        use: {
          loader: "file-loader",
          options: {
            name: '../fonts/[name].[ext]',
          },
        },
      },
    ],
  },
  plugins: [
    !DEV && new CleanWebpackPlugin([paths.appBuild] , { root: process.cwd() }),
    new ExtractTextPlugin(DEV ? 'css/[name].css' : 'css/[name].[hash:8].css'),
    new webpack.EnvironmentPlugin({
      NODE_ENV: 'development', // use 'development' unless process.env.NODE_ENV is defined
      DEBUG: false,
    }),
    new AssetsPlugin({
      path: paths.appBuild,
      filename: 'assets.json',
    }),
    new CopyWebpackPlugin([
        { from: paths.appImages, to: paths.appBuild + '/images' },
        { from: paths.appFonts, to: paths.appBuild + '/fonts' }
    ], { debug: 'info' }),
    !DEV &&
      new webpack.optimize.UglifyJsPlugin({
        compress: {
          screw_ie8: true, // React doesn't support IE8
          warnings: false,
        },
        mangle: {
          screw_ie8: true,
        },
        output: {
          comments: false,
          screw_ie8: true,
        },
        sourceMap: true,
      }),
    DEV &&
      new FriendlyErrorsPlugin({
        clearConsole: false,
      }),
    DEV &&
      new BrowserSyncPlugin({
          notify: false,
          host: 'dunktree.test',
          port: 4000,
          logLevel: 'silent',
          files: ['./*.php'],
          proxy: 'https://dunktree.test/',
      }),
  ].filter(Boolean),
};
