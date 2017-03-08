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
      'tether/dist/js/tether.js',
      'jquery/dist/jquery.js',
      'jquery-ui/jquery-ui.js',
      'bootstrap/dist/js/npm.js',
      'bootstrap-tokenfield/dist/bootstrap-tokenfield.js',
      'eonasdan-bootstrap-datetimepicker/src/js/bootstrap-datetimepicker.js',
      'jwerty/jwerty.js',
      'magnific-popup/dist/jquery.magnific-popup.js',
      'dropzone/dist/dropzone.js',
      'typeahead.js/dist/typeahead.jquery.min.js',
      'typeahead.js/dist/bloodhound.min.js',
      'PrestaKit/dist/js/select2.min.js',
      'PrestaKit/dist/js/bootstrap-switch.min.js',
      'PrestaKit/dist/js/jquery.pstagger.min.js',
      'PrestaKit/dist/js/prestashop-ui-kit.js',
      'PrestaKit/dist/js/jquery.growl.js',
      'bootstrap-slider/dist/bootstrap-slider.js',
      'sprintf-js/src/sprintf.js',
<<<<<<< 1b27a4c6de342fa24caace94a38054e72139a373
      './js/theme.js',
    ],
    stock: [
      './js/app/pages/stock/main.js',
    ]
||||||| merged common ancestors
      './js/theme.js'
    ],
    stock: './js/stock-page/main.js'
=======
      './js/theme.js'
    ],
    stock: [
      'webpack-dev-server/client?http://localhost:8080',
      'webpack/hot/only-dev-server',
      './js/stock-page/main.js',
    ]
>>>>>>> BO: Split stock-app in components
  },
  output: {
    path: path.resolve(__dirname, 'public'),
    filename: '[name].bundle.js'
  },
<<<<<<< d0b91cc224faacca458d16294370be2d4aa7d239
<<<<<<< 1b27a4c6de342fa24caace94a38054e72139a373
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
||||||| merged common ancestors
  resolve: {
    extensions: ['.js', '.vue', '.json'],
    alias: {
      vue$: 'vue/dist/vue.common.js'
    }
  },
=======
>>>>>>> BO: Split stock-app in components
||||||| merged common ancestors
=======
  devServer: {
    hot: true,
    contentBase: path.resolve(__dirname, 'public'),
    publicPath: '/'
  },
>>>>>>> BO: Enable hot module replacement in development
  module: {
    rules: [
      {
        test: /jquery\/dist\/jquery\.js/,
        use: [
          {
            loader: 'expose-loader',
            query: 'jQuery',
          }, {
            loader: 'expose-loader',
            query: 'jquery',
          }, {
            loader: 'expose-loader',
            query: '$',
          }
        ]
      }, {
        test: require.resolve('tether'),
        use: [
          {
            loader: 'expose-loader',
            query: 'Tether'
          }
        ]
      }, {
        test: /bloodhound\.min\.js/,
        use: [
          {
            loader: 'expose-loader',
            query: 'Bloodhound'
          }
        ]
      }, {
        test: /jwerty\/jwerty\.js/,
        loader: 'imports-loader?this=>window&module=>false'
      }, {
        test: /typeahead\.jquery\.js/,
        loader: 'imports-loader?define=>false&exports-loader=>false&this=>window'
      }, {
        test: /bloodhound\.js/,
        loader: 'exports-loader?Bloodhound!imports-loader?define=>false&exports-loader=>false&this=>window'
      }, {
        test: /dropzone\/dist\/dropzone\.js/,
        loader: 'imports-loader?this=>window&module=>null'
      },
      {
        test: /\.vue$/,
        loader: 'vue-loader',
        options: {
          loaders: {
            js: 'babel-loader?presets[]=es2015&presets[]=stage-2'
          },
          postcss: [require('postcss-cssnext')()]
        }
      },
<<<<<<< 1b27a4c6de342fa24caace94a38054e72139a373
      {
        test: /\.css$/,
        use: ExtractTextPlugin.extract({
          fallback: 'style-loader',
          use: ['css-loader']
        })
      },
||||||| merged common ancestors
      { test: /\.vue$/,
        use: 'vue-loader',
      },
=======
>>>>>>> BO: Split stock-app in components
      {
<<<<<<< 1b27a4c6de342fa24caace94a38054e72139a373
        test: /\.scss$/,
        use: ExtractTextPlugin.extract({
          fallback: 'style-loader',
          use: ['css-loader', 'postcss-loader', 'sass-loader']
        })
||||||| merged common ancestors
        test: /\.json$/,
        use: 'json-loader',
=======
        test: /\.vue$/,
        loader: 'vue'
>>>>>>> BO: Split stock-app in components
      },
<<<<<<< d0b91cc224faacca458d16294370be2d4aa7d239
||||||| merged common ancestors
      {test: /\.css$/, use: ['style-loader', 'css-loader']},
      {test: /\.scss$/, use: ['style-loader', 'css-loader', 'sass-loader']},
=======
      {
        test: /\.css$/,
        use: ExtractTextPlugin.extract({
          fallback: 'style-loader',
          use: ['css-loader']
        })
      },
      {
        test: /\.scss$/,
        use: ExtractTextPlugin.extract({
          fallback: 'style-loader',
          use: ['css-loader', 'sass-loader']
        })
      },
>>>>>>> BO: Enable hot module replacement in development
      {
<<<<<<< d0b91cc224faacca458d16294370be2d4aa7d239
        test: /.(jpg|png|woff(2)?|eot|otf|ttf|svg|gif)(\?[a-z0-9=\.]+)?$/,
        use: 'file-loader?name=[hash].[ext]'
||||||| merged common ancestors
        test: /.(png|woff(2)?|eot|ttf|svg)(\?[a-z0-9=\.]+)?$/,
        loader: 'file-loader?name=[hash].[ext]&publicPath=../../../admin-dev/themes/new-theme/public/'
=======
        test: /.(png|woff(2)?|eot|ttf|svg)(\?[a-z0-9=\.]+)?$/,
        loader: 'file-loader?name=[hash].[ext]'
>>>>>>> BO: Enable hot module replacement in development
      }
    ]
  },
  plugins: [
<<<<<<< d0b91cc224faacca458d16294370be2d4aa7d239
    new webpack.HotModuleReplacementPlugin(),
    new ExtractTextPlugin('theme.css')
  ]
};

if (process.env.NODE_ENV === 'production') {
  config.plugins.push(
||||||| merged common ancestors
=======
    new webpack.HotModuleReplacementPlugin(),
    new ExtractTextPlugin('theme.css'),
    new webpack.NamedModulesPlugin()
  ]
};

if (process.env.NODE_ENV === 'production') {
  config.plugins.push(
>>>>>>> BO: Enable hot module replacement in development
    new webpack.optimize.UglifyJsPlugin({
      sourceMap: false,
      compress: {
        sequences: true,
        conditionals: true,
        booleans: true,
        if_return: true,
        join_vars: true,
        drop_console: true
      },
      output: {
        comments: false
      }
    })
<<<<<<< d0b91cc224faacca458d16294370be2d4aa7d239
  );
} else {
  config.entry.stock.push('webpack/hot/only-dev-server');
  config.entry.stock.push('webpack-dev-server/client?http://localhost:8080');
}

module.exports = config;
||||||| merged common ancestors
  ]
};
=======
  );
}

module.exports = config;
>>>>>>> BO: Enable hot module replacement in development
