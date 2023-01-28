/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const TerserPlugin = require('terser-webpack-plugin');
const CleanWebpackPlugin = require('clean-webpack-plugin');
const HtmlWebpackPlugin = require('html-webpack-plugin');
const FontPreloadPlugin = require('webpack-font-preload-plugin');
const CssoWebpackPlugin = require('csso-webpack-plugin').default;
const LicensePlugin = require('webpack-license-plugin');
const RemoveEmptyScriptsPlugin = require('webpack-remove-empty-scripts');

module.exports = (env, argv) => {
  const devMode = argv.mode === 'development';

  const config = {
    entry: {
      theme: './js/theme.js',
      rtl: './scss/rtl.scss',
    },
    output: {
      path: path.resolve(__dirname, 'public'),
      publicPath: '',
      filename: '[name].bundle.js',
    },
    module: {
      rules: [
        {
          test: path.join(__dirname, 'js'),
          use: [
            {
              loader: 'babel-loader',
              options: {
                presets: [['@babel/preset-env', {modules: false}]],
              },
            },
          ],
        },
        {
          test: /\.(scss|sass|css)$/,
          use: [
            {
              loader: MiniCssExtractPlugin.loader,
            },
            {
              loader: 'css-loader',
            },
            {
              loader: 'postcss-loader',
            },
            {
              loader: 'sass-loader',
            },
          ],
        },
        {
          test: /\.(jpg|png|woff2?|eot|otf|ttf|svg|gif)$/,
          type: 'asset/resource',
          generator: {
            filename: '[hash][ext]',
          },
          exclude: /MaterialIcons-Regular\.(woff2?|ttf)$/,
        },
        {
          test: /MaterialIcons-Regular\.(woff2?|ttf)$/,
          type: 'asset/resource',
          generator: {
            filename: '[hash].preload[ext]',
          },
        },
      ],
    },
    optimization: {},
    plugins: [
      new RemoveEmptyScriptsPlugin(),
      new CleanWebpackPlugin({
        root: path.resolve(__dirname),
        cleanOnceBeforeBuildPatterns: [
          '**/*', // required
          '!theme.rtlfix', // exclusion
        ],
      }),
      new MiniCssExtractPlugin({
        filename: '[name].css',
      }),
      new HtmlWebpackPlugin({
        filename: 'preload.tpl',
        templateContent: '{{{preloadLinks}}}',
        inject: false,
      }),
      new FontPreloadPlugin({
        index: 'preload.tpl',
        extensions: ['woff2'],
        filter: /preload/,
        // eslint-disable-next-line
        replaceCallback: ({indexSource, linksAsString}) =>
          indexSource.replace('{{{preloadLinks}}}', linksAsString.replace(/href="/g, 'href="{$admin_dir}')),
      }),
      new CssoWebpackPlugin({
        forceMediaMerge: true,
      }),
      new LicensePlugin({
        outputFilename: 'thirdPartyNotice.json',
        licenseOverrides: {
          'vazirmatn@32.102.0': 'OFL-1.1',
        },
        replenishDefaultLicenseTexts: true,
      }),
    ],
  };

  if (!devMode) {
    config.optimization = {
      minimize: true,
      minimizer: [
        new TerserPlugin({
          parallel: true,
          extractComments: false,
        }),
      ],
    };
  }

  return config;
};
