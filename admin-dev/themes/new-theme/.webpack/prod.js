const webpack = require('webpack');
const keepLicense = require('uglify-save-license');
const UglifyJsPlugin = require('uglifyjs-webpack-plugin');
const common = require('./common.js');

/**
 * Returns the production webpack config,
 * by merging production specific configuration with the common one.
 *
 */
function prodConfig() {
  const prod = Object.assign(common, {
    stats: 'minimal',
    optimization: {
      minimizer: [
        new UglifyJsPlugin({
          sourceMap: false,
          uglifyOptions: {
            compress: {
              drop_console: true,
            },
            output: {
              comments: keepLicense,
            },
          },
        }),
      ],
    },
  });

  // Required for Vue production environment
  prod.plugins.push(
    new webpack.DefinePlugin({
      'process.env.NODE_ENV': JSON.stringify('production'),
    }),
  );

  return prod;
}

module.exports = prodConfig;
