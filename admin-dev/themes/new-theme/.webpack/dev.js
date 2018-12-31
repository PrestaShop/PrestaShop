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
    }
  );

  return dev;
}

module.exports = devConfig;
