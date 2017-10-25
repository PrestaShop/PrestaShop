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
const path = require('path');
const webpack = require('webpack');
const ExtractTextPlugin = require("extract-text-webpack-plugin");

let config = {
  entry: {
    main: [
      'prestakit/dist/js/prestashop-ui-kit.js',
      'jquery-ui-dist/jquery-ui.js',
      'bootstrap-tokenfield/dist/bootstrap-tokenfield.js',
      'eonasdan-bootstrap-datetimepicker/src/js/bootstrap-datetimepicker.js',
      'jwerty/jwerty.js',
      'magnific-popup/dist/jquery.magnific-popup.js',
      'dropzone/dist/dropzone.js',
      'typeahead.js/dist/typeahead.jquery.js',
      'typeahead.js/dist/bloodhound.min.js',
      // 'bootstrap-slider/dist/bootstrap-slider.js',
      'sprintf-js/src/sprintf.js',
      './js/theme.js',
    ],
    stock: [
      './js/app/pages/stock',
    ],
    translations: [
      './js/app/pages/translations',
    ]
  },
  output: {
    path: path.resolve(__dirname, 'public'),
    filename: '[name].bundle.js'
  },
  devServer: {
    hot: true,
    contentBase: path.resolve(__dirname, 'public'),
    publicPath: '/'
  },
  resolve: {
    extensions: ['.js', '.vue', '.json'],
    alias: {
      vue$: 'vue/dist/vue.common.js',
      app: path.resolve(__dirname, 'js/app')
    }
  },
  module: {
    rules: [
      {
        test: /jquery-ui\.js/,
        use: "imports-loader?define=>false&this=>window"
      }, {
        test: /jquery\.magnific-popup\.js/,
        use: "imports-loader?define=>false&exports=>false&this=>window"
      }, {
        test: /bloodhound\.min\.js/,
        use: [
          {
            loader: 'expose-loader',
            query: 'Bloodhound'
          }
        ]
      }, {
        test: /dropzone\/dist\/dropzone\.js/,
        loader: 'imports-loader?this=>window&module=>null'
      }, {
        test: require.resolve('moment'),
        loader: 'imports-loader?define=>false&this=>window',
      }, {
        test: /typeahead\.jquery\.js/,
        loader: 'imports-loader?define=>false&exports=>false&this=>window'
      }, {
        test: /bootstrap-tokenfield\.js/,
        loader: 'imports-loader?define=>false&exports=>false&this=>window'
      }, {
        test: /bootstrap-datetimepicker\.js/,
        loader: 'imports-loader?define=>false&exports=>false&this=>window'
      }, {
        test: /jwerty\/jwerty\.js/,
        loader: 'imports-loader?this=>window&module=>false'
      }, {
        test: /\.vue$/,
        loader: 'vue-loader',
        options: {
          loaders: {
            js: 'babel-loader?presets[]=es2015&presets[]=stage-2',
            css: 'postcss-loader'
          },
        }
      }, {
        test: /\.css$/,
        use: ExtractTextPlugin.extract({
          fallback: 'style-loader',
          use: ['css-loader']
        })
      }, {
        test: /\.scss$/,
        use: ExtractTextPlugin.extract({
          fallback: 'style-loader',
          use: [
            {
              loader: 'css-loader',
              options: {
                minimize: true,
                sourceMap: true,
              }
            },
            'postcss-loader',
            'sass-loader'
          ]
        })
      }, {
        test: /.(jpg|png|woff(2)?|eot|otf|ttf|svg|gif)(\?[a-z0-9=\.]+)?$/,
        use: 'file-loader?name=[hash].[ext]'
      }
    ]
  },
  plugins: [
    new webpack.HotModuleReplacementPlugin(),
    new ExtractTextPlugin('theme.css'),
    new webpack.ProvidePlugin({
      moment: 'moment', // needed for bootstrap datetime picker
    })
  ]
};

// if (process.env.NODE_ENV === 'production') {
//   config.plugins.push(
//     new webpack.optimize.UglifyJsPlugin({
//       sourceMap: false,
//       compress: {
//         sequences: true,
//         conditionals: true,
//         booleans: true,
//         if_return: true,
//         join_vars: true,
//         drop_console: true
//       },
//       output: {
//         comments: false
//       }
//     })
//   );
// } else {
  config.entry.stock.push('webpack/hot/only-dev-server');
  config.entry.stock.push('webpack-dev-server/client?http://localhost:8080');
// }

module.exports = config;
