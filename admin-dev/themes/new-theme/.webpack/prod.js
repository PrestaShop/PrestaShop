const webpack = require('webpack');
const TerserPlugin = require('terser-webpack-plugin');
const common = require('./common.js');
const CssoWebpackPlugin = require('csso-webpack-plugin').default;

/**
 * Returns the production webpack config,
 * by merging production specific configuration with the common one.
 *
 */
function prodConfig() {
  const prod = Object.assign(common, {
    stats: 'minimal',
    optimization: {
      minimize: true,
      minimizer: [
        new TerserPlugin({
          parallel: true,
          extractComments: false,
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

  prod.plugins.push(
    new CssoWebpackPlugin({
      forceMediaMerge: true,
    }),
  );

  return prod;
}

module.exports = prodConfig;
