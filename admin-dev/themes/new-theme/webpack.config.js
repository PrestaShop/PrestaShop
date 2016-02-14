var path = require('path');
var webpack = require('webpack');
var ExtractTextPlugin = require("extract-text-webpack-plugin");

module.exports = {
  entry: [
    require.resolve('tether'),
    require.resolve('jquery'),
    require.resolve('jquery-ui'),
    require.resolve('bootstrap'),
    require.resolve('bootstrap-tokenfield'),
    require.resolve('moment'),
    require.resolve('eonasdan-bootstrap-datetimepicker'),
    require.resolve('jwerty'),
    require.resolve('magnific-popup'),
    require.resolve('dropzone'),
    'cldrjs/dist/cldr.js',
    'cldrjs/dist/cldr/event.js',
    'cldrjs/dist/cldr/supplemental.js',
    'globalize/dist/globalize.js',
    'globalize/dist/globalize/message.js',
    'globalize/dist/globalize/number.js',
    'globalize/dist/globalize/plural.js',
    'globalize/dist/globalize/date.js',
    'globalize/dist/globalize/currency.js',
    'globalize/dist/globalize/relative-time.js',
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
    filename: "bundle.js"
  },
  module: {
    loaders: [{
      test: require.resolve("jquery"),
      loader: "expose?jquery!expose?jQuery!expose?$"
    }, {
      test: require.resolve("tether"),
      loader: "expose?Tether"
    }, {
      test: require.resolve("jwerty"),
      loader: "imports?this=>window&module=>false"
    }, {
      test: require.resolve('bootstrap-tokenfield'),
      loader: "imports?define=>false&exports=>false"
    }, {
      test: /typeahead\.jquery\.js/,
      loader: "imports?define=>false&exports=>false&this=>window"
    }, {
      test: /bloodhound\.js/,
      loader: "exports?Bloodhound!imports?define=>false&exports=>false&this=>window"
    }, {
      test: require.resolve('dropzone'),
      loader: "imports?this=>window&module=>null"
    }, {
      test: [
        /cldrjs\/dist\/cldr/,
        /globalize\/dist\/globalize/
      ],
      loader: "imports?this=>window&exports=>false&module=>false&define=>false"
    }, {
      test: require.resolve('eonasdan-bootstrap-datetimepicker'),
      loader: "imports?this=>window&exports=>false&define=>false"
    }, {
      test: [
        /moment\/moment\.js/,
        /moment\/min\/locales\.js/
      ],
      loader: "imports?this=>window&exports=>false&define=>false"
    }, {
      test: path.join(__dirname, 'js'),
      loader: 'babel',
      query: {
        presets: ['es2015']
      }
    }, {
      test: /\.scss$/,
      loader: ExtractTextPlugin.extract("style", "css!sass")
    }, {
      test: /\.css$/,
      loader: ExtractTextPlugin.extract("style",  "css?sourceMap!postcss!sass?sourceMap")
    }, {
      test: /.(png|woff(2)?|eot|ttf|svg)(\?[a-z0-9=\.]+)?$/,
      loader: 'file-loader?name=[hash].[ext]'
    }]
  },
  plugins: [
    new ExtractTextPlugin('theme.css')
  ]
};
