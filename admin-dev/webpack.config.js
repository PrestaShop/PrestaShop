var path = require('path');
var webpack = require('webpack');

module.exports = {
  entry: [
    'cldrjs/dist/cldr.js',
    'cldrjs/dist/cldr/event.js',
    'cldrjs/dist/cldr/supplemental.js',
    'globalize/dist/globalize.js',
    'globalize/dist/globalize/message.js',
    'globalize/dist/globalize/number.js',
    'globalize/dist/globalize/plural.js',
    'globalize/dist/globalize/date.js',
    'globalize/dist/globalize/currency.js',
    'globalize/dist/globalize/relative-time.js'
  ],
  output: {
    path: './public',
    filename: 'bundle.js'
  },
  module: {
    loaders: [{
      test: [
        /cldrjs\/dist\/cldr/,
        /globalize\/dist\/globalize/
      ],
      loader: 'imports?this=>window&exports=>false&module=>false&define=>false'
    }]
  },
  plugins: [
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
