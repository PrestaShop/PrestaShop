var webpack = require('webpack');

var plugins = [];

var production = true;

if (production) {
    plugins.push(
        new webpack.optimize.UglifyJsPlugin({
            compress: {
                warnings: false
            }
        })
    );
}

module.exports = {
    entry: ['./js/theme.js'],
    output: {
        path: '../assets/js',
        filename: 'theme.js'
    },
    module: {
        loaders: [
            {test: /\.js$/     , loaders: ['babel-loader']},
        ]
    },
    externals: {
        jquery: '$',
        prestashop: 'prestashop'
    },
    devtool: 'source-map',
    plugins: plugins
};
