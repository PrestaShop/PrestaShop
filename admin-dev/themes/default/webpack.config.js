/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const TerserPlugin = require('terser-webpack-plugin');
const {CleanWebpackPlugin} = require('clean-webpack-plugin');
const HtmlWebpackPlugin = require('html-webpack-plugin');
const FontPreloadPlugin = require('webpack-font-preload-plugin');
const CssoWebpackPlugin = require('csso-webpack-plugin').default;
const LicensePlugin = require('webpack-license-plugin');
const RemoveEmptyScriptsPlugin = require('webpack-remove-empty-scripts');
const bourbon = require('bourbon');

module.exports = (env, argv) => {
  const devMode = argv.mode === 'development';

  const config = {
    mode: argv.mode || 'production',
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
              options: {
                sourceMap: true,
                sassOptions: {
                  includePaths: bourbon.includePaths,
                },
              },
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
        replaceCallback: ({indexSource, linksAsString}) => indexSource.replace('{{{preloadLinks}}}', linksAsString.replace(/href="/g, 'href="{$admin_dir}')),
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
