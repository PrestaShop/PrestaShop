var webpack = require('webpack');
var path    = require('path');
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
        loaders: [
            {test: /\.js$/     , loaders: ['babel-loader']},
            {
                test: /\.scss$/,
                loader: ExtractTextPlugin.extract(
                    "style",
                    "css?sourceMap!postcss!sass?sourceMap"
                )
            }
        ]
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
