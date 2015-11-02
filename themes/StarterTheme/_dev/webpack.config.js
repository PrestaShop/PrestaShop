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
    entry: [
      './node_modules/jquery/dist/jquery.js',
      './js/theme.js'
    ],
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
        prestashop: 'prestashop'
    },
    devtool: 'source-map',
    plugins: plugins
};
