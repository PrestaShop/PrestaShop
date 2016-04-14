var path = require('path');
var webpack = require('webpack');
var ExtractTextPlugin = require('extract-text-webpack-plugin');

module.exports = {
  entry: [
    'tether/dist/js/tether.js',
    'jquery/dist/jquery.js',
    'jquery-ui/jquery-ui.js',
    'bootstrap/dist/js/npm.js',
    'bootstrap-tokenfield/dist/bootstrap-tokenfield.js',
    'moment/moment.js',
    'eonasdan-bootstrap-datetimepicker/src/js/bootstrap-datetimepicker.js',
    'jwerty/jwerty.js',
    'magnific-popup/dist/jquery.magnific-popup.js',
    'dropzone/dist/dropzone.js',
    'typeahead.js/dist/typeahead.jquery.js',
    'typeahead.js/dist/bloodhound.js',
    'moment/moment.js',
    'moment/min/locales.js',
    'PrestaKit/dist/js/select2.min.js',
    'PrestaKit/dist/js/bootstrap-switch.min.js',
    'PrestaKit/dist/js/jquery.pstagger.min.js',
    'PrestaKit/dist/js/prestashop-ui-kit.js',
    'PrestaKit/dist/js/jquery.growl.js',
    'bootstrap-slider/dist/bootstrap-slider.js',
    'sprintf-js/src/sprintf.js',
    './js/theme.js'
  ],
  output: {
    path: './public',
    filename: 'bundle.js'
  },
  module: {
    loaders: [{
      test: /jquery\/dist\/jquery\.js/,
      loader: 'expose?jquery!expose?jQuery!expose?$'
    }, {
      test: /tether\/dist\/js\/tether\.js/,
      loader: 'expose?Tether'
    }, {
      test: /jwerty\/jwerty\.js/,
      loader: 'imports?this=>window&module=>false'
    }, {
      test: /bootstrap-tokenfield\/dist\/bootstrap-tokenfield\.js/,
      loader: 'imports?define=>false&exports=>false'
    }, {
      test: /typeahead\.jquery\.js/,
      loader: 'imports?define=>false&exports=>false&this=>window'
    }, {
      test: /bloodhound\.js/,
      loader: 'exports?Bloodhound!imports?define=>false&exports=>false&this=>window'
    }, {
      test: /dropzone\/dist\/dropzone\.js/,
      loader: 'imports?this=>window&module=>null'
    }, {
      test: /eonasdan-bootstrap-datetimepicker\/src\/js\/bootstrap-datetimepicker\.js/,
      loader: 'imports?this=>window&exports=>false&define=>false'
    }, {
      test: [
        /moment\/moment\.js/,
        /moment\/min\/locales\.js/
      ],
      loader: 'imports?this=>window&exports=>false&define=>false'
    }, {
      test: path.join(__dirname, 'js'),
      loader: 'babel',
      query: {
        presets: ['es2015']
      }
    }, {
      test: /\.scss$/,
      loader: ExtractTextPlugin.extract('style', 'css!sass')
    }, {
      test: /\.css$/,
      loader: ExtractTextPlugin.extract('style', 'css?sourceMap!postcss!sass?sourceMap')
    }, {
      test: /.(png|woff(2)?|eot|ttf|svg)(\?[a-z0-9=\.]+)?$/,
      loader: 'file-loader?name=[hash].[ext]'
    }]
  },
  plugins: [
    new ExtractTextPlugin('theme.css'),
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
  ]
};
