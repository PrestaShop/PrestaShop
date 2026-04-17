/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
const TerserPlugin = require("terser-webpack-plugin");

module.exports = (env, argv) => {
  const path = require('path');
  const mode = argv.mode || 'production';

  return {
    mode,
    entry: [
      './_core/js/theme.js',
    ],
    output: {
      path: path.resolve(__dirname),
      filename: 'core.js',
      chunkFilename: '[chunkhash]-chunk.js',
    },
    module: {
      rules: [
        {
          test: /\.js$/,
          use: {
            loader: 'esbuild-loader',
          },
        },
      ],
    },
    externals: {
      prestashop: 'prestashop',
    },
    devtool: mode === 'production' ? false : 'source-map',
    optimization: {
      minimize: true,
      minimizer: [new TerserPlugin({
        extractComments: false
      })],
    },
  };
};
