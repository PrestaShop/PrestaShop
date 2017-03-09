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
      './js/theme.js'
    ],
    stock: [
      'webpack-dev-server/client?http://localhost:8080',
      'webpack/hot/only-dev-server',
      './js/stock-page/main.js',
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
  module: {
    rules: [
      {
        test: require.resolve('jquery'),
        use: [
          {
            loader: 'expose-loader',
            query: 'jQuery'
          },
          {
            loader: 'expose-loader',
            query: '$'
          },
          {
            loader: 'expose-loader',
            query: 'jquery'
          }
        ]
      },
      {
        test: require.resolve('tether'),
        use: [
          {
            loader: 'expose-loader',
            query: 'Tether'
          }
        ]
      },
      {
        test: /jwerty\/jwerty\.js/,
        loader: 'imports-loader?this=>window&module=>false'
      }, {
        test: /bootstrap-tokenfield\/dist\/bootstrap-tokenfield\.js/,
        loader: 'imports-loader?define=>false&exports-loader=>false'
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
        test: path.join(__dirname, 'js'),
        loader: 'babel-loader',
        query: {
          presets: ['es2015']
        }
      },
      {
        test: /\.vue$/,
        loader: 'vue'
      },
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
      {
        test: /.(png|woff(2)?|eot|ttf|svg)(\?[a-z0-9=\.]+)?$/,
        loader: 'file-loader?name=[hash].[ext]'
      }
    ]
  },
  plugins: [
    new webpack.HotModuleReplacementPlugin(),
    new ExtractTextPlugin('theme.css'),
    new webpack.NamedModulesPlugin()
  ]
};

if (process.env.NODE_ENV === 'production') {
  config.plugins.push(
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
  );
}

module.exports = config;
