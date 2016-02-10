var path = require('path');
var webpack = require('webpack');
var ExtractTextPlugin = require("extract-text-webpack-plugin");

module.exports = {
  entry: [
    "./js/theme.js"
  ],
  output: {
    path: './public',
    filename: "bundle.js"
  },
  module: {
    loaders: [
      {
        test: /\.js$/,
        loader: 'babel',
        query: {
          presets: ['es2015']
        }
      },
      {
        test: /\.scss$/,
        loader: ExtractTextPlugin.extract(
            "style",
            "css!sass"
        )
      },
      {
        test: /\.css$/,
        loader: ExtractTextPlugin.extract(
            "style",
            "css?sourceMap!postcss!sass?sourceMap"
        )
      },
      {
        test: /.(png|woff(2)?|eot|ttf|svg)(\?[a-z0-9=\.]+)?$/,
        loader: 'file-loader?name=[hash].[ext]'
      }
    ]
  },
  plugins: [
    new ExtractTextPlugin('theme.css')
  ]
};
