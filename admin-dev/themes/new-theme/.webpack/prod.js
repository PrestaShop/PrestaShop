const common = require('./common.js');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');

/**
 * prodConfig function return the production webpack config,
 * by merging production specific configuration with the common one.
 *
 * @param {Boolean} analyze With analye to true, bundle analyze plugin will launch
 */
function prodConfig(analyze) {
  let prod = Object.assign(common, {
    stats: 'minimal',
    optimization: {
      // With mini-css-extract-plugin, one file is created for each '.js' where css is imported.
      // The use of this optimization merge them into one file.
      splitChunks: {
        cacheGroups: {
          styles: {
            name: 'theme',
            test: /\.(s*)css$/,
            chunks: 'all',
            enforce: true
          }
        }
      }
    }
  });

  prod.module.rules.push({
    test:/\.(s*)css$/,
    use: [
      MiniCssExtractPlugin.loader,
      'css-loader',
      'postcss-loader',
      'sass-loader'
    ]
  });

  prod.plugins.push(new MiniCssExtractPlugin({
    filename: '[name].css'
  }));

  if (analyze) {
    const BundleAnalyzerPlugin = require('webpack-bundle-analyzer').BundleAnalyzerPlugin;

    prod.plugins.push(new BundleAnalyzerPlugin());
  }

  return prod;
}

module.exports = prodConfig;