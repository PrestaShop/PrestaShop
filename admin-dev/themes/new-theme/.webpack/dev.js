const common = require('./common.js');
const LiveReloadPlugin = require('webpack-livereload-plugin');

/**
 * Returns the development webpack config,
 * by merging development specific configuration with the common one.
 *
 * @param {String} hostname Development host name, sent as a parameter to webpack. Defaults to localhost
 */
function devConfig(hostname) {
  if (!hostname) console.log('Default host for live reload is set to `localhost`. If you want to use a custom one, just launch the command with hostname option. Ex: `npm run dev -- --hostname="yourUrl"`');

  hostname = hostname || 'localhost';

  console.log('Live reload available on:', hostname);

  let dev = Object.assign(common, { devtool: 'inline-source-map' });

  dev.module.rules.push({
    test:/\.(s*)css$/,
    use: [
      'style-loader',
      'css-loader',
      'postcss-loader',
      'sass-loader'
    ]
  });

  dev.plugins.push(new LiveReloadPlugin({
    appendScriptTag: true,
    hostname: hostname
  }));

  return dev;
}

module.exports = devConfig;
