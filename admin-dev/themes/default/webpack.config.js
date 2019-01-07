/**
 * 2007-2018 PrestaShop
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
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
const path = require('path');
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const UglifyJsPlugin = require('uglifyjs-webpack-plugin');
const keepLicense = require('uglify-save-license');
const OptimizeCSSAssetsPlugin = require("optimize-css-assets-webpack-plugin");

module.exports = (env, argv) => {
  const devMode = argv.mode === 'development';

  const config = {
    entry: [
      './js/theme.js'
    ],
    output: {
      path: path.resolve(__dirname, 'public'),
      filename: 'bundle.js'
    },
    //devtool: 'source-map', // uncomment me to build source maps (really slow)
    module: {
      rules: [{
        test: path.join(__dirname, 'js'),
        use: [{
          loader: 'babel-loader',
          options: {
            presets: [
              ['@babel/preset-env', {modules: false}]
            ]
          }
        }]
      }, {
        test: /\.(scss|sass)$/,
        use: [
          MiniCssExtractPlugin.loader,
          {
            loader: 'css-loader',
            options: {
              //sourceMap: true, // uncomment me to generate source maps
            }
          },
          {
            loader: 'postcss-loader',
            options: {
              //sourceMap: true, // uncomment me to generate source maps
            }
          },
          {
            loader: 'sass-loader',
            options: {
              //sourceMap: true, // uncomment me to generate source maps
            }
          }
        ]
      }, {
        test: /.(gif|png|woff(2)?|eot|ttf|svg)(\?[a-z0-9=\.]+)?$/,
        loader: 'file-loader?name=[hash].[ext]'
      }]
    },
    optimization: {

    },
    plugins: [
      new MiniCssExtractPlugin({
        filename: 'theme.css'
      })
    ]
  };

  if (!devMode) {
    config.optimization.minimizer = [
      new UglifyJsPlugin({
        sourceMap: false,
        uglifyOptions: {
          compress: {
            drop_console: true
          },
          output: {
            comments: keepLicense
          }
        },
      }),
      new OptimizeCSSAssetsPlugin()
    ];
  }

  return config;
};
