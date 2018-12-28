const common = require('./common.js');

/**
 * Returns the development webpack config,
 * by merging development specific configuration with the common one.
 *
 * @param {String} hostname Development host name, sent as a parameter to webpack. Defaults to localhost
 */
function devConfig(hostname) {
  let dev = Object.assign(
    common,
    {
      devtool: 'inline-source-map',
    }
  );

  return dev;
}

module.exports = devConfig;
