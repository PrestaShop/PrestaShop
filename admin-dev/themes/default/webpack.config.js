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
const UglifyJsPlugin = require('uglifyjs-webpack-plugin');
const keepLicense = require('uglify-save-license');
const OptimizeCSSAssetsPlugin = require('optimize-css-assets-webpack-plugin');
const CleanWebpackPlugin = require('clean-webpack-plugin');
const HtmlWebpackPlugin = require('html-webpack-plugin');
const FontPreloadPlugin = require('webpack-font-preload-plugin');

module.exports = (env, argv) => {
  const devMode = argv.mode === 'development';

  const config = {
    entry: [
      './js/theme.js',
    ],
    output: {
      path: path.resolve(__dirname, 'public'),
      filename: 'bundle.js',
    },
    module: {
      rules: [{
        test: path.join(__dirname, 'js'),
        use: [{
          loader: 'babel-loader',
          options: {
            presets: [
              ['@babel/preset-env', {modules: false}],
            ],
          },
        }],
      }, {
        test: /\.(scss|sass|css)$/,
        use: [
          {
            loader: MiniCssExtractPlugin.loader,
            options: {
              publicPath: ''
            }
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
        test: /.(jpg|png|woff2?|eot|otf|ttf|svg|gif)$/,
        loader: 'file-loader',
        options: {
          name: '[hash].[ext]',
        },
        exclude: /MaterialIcons-Regular.(woff2?|ttf)/,
      },
      {
        test: /MaterialIcons-Regular.(woff2?|ttf)$/,
        loader: 'file-loader',
        options: {
          name: '[hash].preload.[ext]',
        },
      },
      ],
    },
    optimization: {

    },
    plugins: [
      new CleanWebpackPlugin({
        root: path.resolve(__dirname),
        cleanOnceBeforeBuildPatterns: [
          '**/*', // required
          '!theme.rtlfix', // exclusion
        ],
      }),
      new MiniCssExtractPlugin({
        filename: 'theme.css',
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
        replaceCallback: ({indexSource, linksAsString}) => indexSource.replace('{{{preloadLinks}}}', linksAsString.replace(/href="auto/g, 'href="{"`$admin_dir`"}')),
      }),
    ],
  };

  if (!devMode) {
    config.optimization.minimizer = [
      new UglifyJsPlugin({
        sourceMap: false,
        uglifyOptions: {
          compress: {
            drop_console: true,
          },
          output: {
            comments: keepLicense,
          },
        },
      }),
    ];
  }

  return config;
};
