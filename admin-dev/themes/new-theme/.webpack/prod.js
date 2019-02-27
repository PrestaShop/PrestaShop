const common = require('./common.js');
const UglifyJsPlugin = require('uglifyjs-webpack-plugin');
const keepLicense = require('uglify-save-license');
const OptimizeCSSAssetsPlugin = require('optimize-css-assets-webpack-plugin');
const cssExtractedFileName = 'theme';

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
      mode: 'production',
      stats: 'minimal',
      optimization: {
        // With mini-css-extract-plugin, one file is created for each '.js' where css is imported.
        // The use of this optimization merges them into one file.
        splitChunks: {
          cacheGroups: {
            styles: {
              name: cssExtractedFileName,
              test: /\.(s*)css$/,
              chunks: 'all'
            }
          }
        },
        minimizer: [
          new OptimizeCSSAssetsPlugin(),
          new UglifyJsPlugin({
            sourceMap: true,
            uglifyOptions: {
              compress: {
                drop_console: true
              },
              output: {
                comments: keepLicense
              }
            },
          }),
        ]
      },
    }
  );

  if (analyze) {
    const BundleAnalyzerPlugin = require('webpack-bundle-analyzer').BundleAnalyzerPlugin;

    prod.plugins.push(new BundleAnalyzerPlugin());
  }

  return prod;
}

module.exports = prodConfig;
