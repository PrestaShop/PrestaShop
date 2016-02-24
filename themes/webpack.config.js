var webpack = require('webpack');

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

module.exports = {
    entry: [
      './_core/js/theme.js'
    ],
    output: {
        path: '.',
        filename: 'core.js'
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
