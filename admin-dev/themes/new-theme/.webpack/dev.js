const path = require('path');
const common = require('./common.js');

/**
 * Returns the development webpack config,
 * by merging development specific configuration with the common one.
 */
function devConfig() {
  let dev = Object.assign(
    common,
    {
      devtool: 'inline-source-map',
      devServer: {
        hot: true,
        contentBase: path.resolve(__dirname, '/../public'),
        publicPath: '/',
      },
    }
  );

  return dev;
}

module.exports = devConfig;
