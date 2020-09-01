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
var path = require('path');
var webpack = require('webpack');

module.exports = {
  entry: [
    'cldrjs/dist/cldr.js',
    'cldrjs/dist/cldr/event.js',
    'cldrjs/dist/cldr/supplemental.js',
    'globalize/dist/globalize.js',
    'globalize/dist/globalize/message.js',
    'globalize/dist/globalize/number.js',
    'globalize/dist/globalize/plural.js',
    'globalize/dist/globalize/date.js',
    'globalize/dist/globalize/currency.js',
    'globalize/dist/globalize/relative-time.js'
  ],
  output: {
    path: './public',
    filename: 'bundle.js'
  },
  module: {
    loaders: [{
      test: [
        /cldrjs\/dist\/cldr/,
        /globalize\/dist\/globalize/
      ],
      loader: 'imports?this=>window&exports=>false&module=>false&define=>false'
    }]
  },
  plugins: [
    new webpack.optimize.UglifyJsPlugin({
      sourceMap: false,
      compress: {
        sequences: true,
        conditionals: true,
        booleans: true,
        if_return: true,
        join_vars: true,
        drop_console: true
      },
      output: {
        comments: false
      }
    })
  ]
};
