const common = require('./common.js');
const webpack = require('webpack');
const keepLicense = require('uglify-save-license');

/**
 * Returns the production webpack config,
 * by merging production specific configuration with the common one.
 *
 * @param {Boolean} analyze If true, bundle analyze plugin will launch
 */
function prodConfig(analyze) {
  let prod = Object.assign(
    common,
    {
      stats: 'minimal',
    }
  );

  prod.plugins.push(
    new webpack.optimize.UglifyJsPlugin({
      sourceMap: true,
      uglifyOptions: {
        compress: {
          drop_console: true
        },
        output: {
          comments: keepLicense
        }
      },
    })
  );

  return prod;

}

module.exports = prodConfig;
