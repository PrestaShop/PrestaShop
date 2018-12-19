const fs = require('fs');
const common = require('./common.js');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');

/**
 * Returns the production webpack config,
 * by merging production specific configuration with the common one.
 *
 * @param {Boolean} analyze If true, bundle analyze plugin will launch
 */
function prodConfig(analyze) {
  const cssExtractedFileName = 'theme';

  let prod = Object.assign(common, {
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

  prod.plugins.push({
    apply: (compiler) => {
      /**
       * When using mini-css-extract-plugin and merging all chunks to one file (see optimization configuration),
       * a [cssExtractedFileName].bundle.js is created. This file is required for the js entry point to be executed.
       * see: https://github.com/webpack-contrib/mini-css-extract-plugin/issues/147
       * This hook merges the [cssExtractedFileName].bundle.js into the main.bundle.js file, so we avoid
       * to include the [cssExtractedFileName].bundle.js into the html
       */
      compiler.hooks.afterEmit.tap('AfterEmitTest', (compilation) => {
        let mainBundle = fs.createWriteStream('./public/main.bundle.js', {flags: 'a'});
        let themeBundle = fs.createReadStream('./public/'+ cssExtractedFileName +'.bundle.js');

        mainBundle.on('pipe', function() {
          console.log('prestashop-post-operation: Merging bundle.main.js and '+ cssExtractedFileName +'.bundle.js');
        });

        mainBundle.on('close', function() {
          console.log('prestashop-post-operation: Merging done.');
        });

        themeBundle.pipe(mainBundle);
      });
    }
  });

  return prod;
}

module.exports = prodConfig;
