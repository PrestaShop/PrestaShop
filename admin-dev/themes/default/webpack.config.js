/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
var path = require('path');
var webpack = require('webpack');
var ExtractTextPlugin = require('extract-text-webpack-plugin');
var UglifyJSPlugin = require('uglifyjs-webpack-plugin');

var cssLoaders = [{
  loader: "css-loader"
}];

module.exports = {
  entry: {
    main: ['./js/theme.js'],
    productPage: [
      './js/bundle/product/form.js',
      './js/bundle/product/product-manufacturer.js',
      './js/bundle/product/product-related.js',
      './js/bundle/product/product-category-tags.js',
      './js/bundle/product/default-category.js',
      './js/bundle/product/product-combinations.js',
      './js/bundle/category-tree.js',
      './js/bundle/module/module_card.js',
      './js/bundle/modal-confirmation.js',
    ]
  },
  output: {
    path: path.resolve(__dirname, 'public'),
    filename: '[name].bundle.js'
  },
  module: {
    loaders: [
      {
      test: path.join(__dirname, 'js'),
      exclude: ['/node_modules/', '/tiny_mce/', '/js\/admin/'],
      use: ["babel-loader"]
    }, {
      test: /\.scss$/,
      use: ExtractTextPlugin.extract({
        fallback: "style-loader",
        use: [
          ...cssLoaders,
          "sass-loader"
        ]
      })
    }, {
      test: /\.css$/,
      use: ExtractTextPlugin.extract({
        fallback: "style-loader",
        use: cssLoaders
    })
    }, {
      test: /.(png|woff(2)?|eot|ttf|svg)(\?[a-z0-9=\.]+)?$/,
      loader: 'file-loader?name=[hash].[ext]'
    }]
  },
  plugins: [
    new ExtractTextPlugin('theme.css'),
    new UglifyJSPlugin({
      sourceMap: true,
      uglifyOptions: {
          output: {
            comments: true,
            "ascii_only": true
          },
          mangle: false,
          compress: {
              warnings: false
          }
      }
    })
  ]
};
