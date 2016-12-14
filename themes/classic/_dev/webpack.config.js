/**
 * 2007-2016 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2016 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
var webpack = require('webpack');
var path = require('path');
var ExtractTextPlugin = require("extract-text-webpack-plugin");

var plugins = [];

var production = false;

if (production) {
  plugins.push(
    new webpack.optimize.UglifyJsPlugin({
      compress: {
        warnings: false
      }
    })
  );
}

plugins.push(
  new ExtractTextPlugin(
    path.join(
      '..', 'css', 'theme.css'
    )
  )
);

module.exports = {
  entry: [
    './js/theme.js'
  ],
  output: {
    path: '../assets/js',
    filename: 'theme.js'
  },
  module: {
    loaders: [{
      test: /\.js$/,
      exclude: /node_modules/,
      loaders: ['babel-loader']
    }, {
      test: /\.scss$/,
      loader: ExtractTextPlugin.extract(
        "style",
        "css?sourceMap!postcss!sass?sourceMap"
      )
    }, {
      test: /.(png|woff(2)?|eot|ttf|svg)(\?[a-z0-9=\.]+)?$/,
      loader: 'file-loader?name=../css/[hash].[ext]'
    }, {
      test: /\.css$/,
      loader: "style-loader!css-loader!postcss-loader"
    }]
  },
  postcss: function() {
    return [require('postcss-flexibility')];
  },
  externals: {
    prestashop: 'prestashop'
  },
  devtool: 'source-map',
  plugins: plugins,
  resolve: {
    extensions: ['', '.js', '.scss']
  }
};
