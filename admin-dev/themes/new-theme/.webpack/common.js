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
const webpack = require('webpack');
const {CleanWebpackPlugin} = require('clean-webpack-plugin');
const CopyPlugin = require('copy-webpack-plugin');
const VueLoaderPlugin = require('vue-loader/lib/plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const FixStyleOnlyEntriesPlugin = require('webpack-fix-style-only-entries');
const bourbon = require('bourbon');

module.exports = {
  externals: {
    jquery: 'jQuery',
  },
  entry: {
    address: './js/pages/address',
    attachment: './js/pages/attachment',
    attribute: './js/pages/attribute',
    attribute_group: './js/pages/attribute-group',
    backup: './js/pages/backup',
    carrier: './js/pages/carrier',
    catalog: './js/app/pages/catalog',
    catalog_product: './js/pages/catalog/product',
    cart_rule: './js/pages/cart-rule',
    catalog_price_rule: './js/pages/catalog-price-rule',
    catalog_price_rule_form: './js/pages/catalog-price-rule/form',
    category: './js/pages/category',
    cldr: './js/app/cldr',
    cms_page: './js/pages/cms-page',
    cms_page_form: './js/pages/cms-page/form',
    contacts: './js/pages/contacts',
    credit_slip: './js/pages/credit-slip',
    currency: './js/pages/currency',
    currency_form: './js/pages/currency/form',
    customer: './js/pages/customer',
    customer_address_form: './js/pages/address/form.js',
    customer_outstanding: './js/pages/outstanding',
    customer_thread_view: './js/pages/customer-thread/view.js',
    email: './js/pages/email',
    employee: './js/pages/employee/index',
    employee_form: './js/pages/employee/form',
    error: './js/pages/error',
    feature_flag: './js/pages/feature-flag/index',
    feature_form: './js/pages/feature/form',
    form_popover_error: './js/components/form/form-popover-error',
    geolocation: './js/pages/geolocation',
    imports: './js/pages/import',
    improve_design_positions: './js/pages/improve/design_positions',
    invoices: './js/pages/invoices',
    language: './js/pages/language',
    localization: './js/pages/localization',
    logs: './js/pages/logs',
    main: './js/theme.js',
    maintenance: './js/pages/maintenance',
    manufacturer: './js/pages/manufacturer',
    manufacturer_address_form: './js/pages/manufacturer/manufacturer_address_form.js',
    merchandise_return: './js/pages/merchandise-return',
    meta: './js/pages/meta',
    module: './js/pages/module',
    module_card: './js/app/pages/module-card',
    monitoring: './js/pages/monitoring',
    order: './js/pages/order',
    order_create: './js/pages/order/create.js',
    order_delivery: './js/pages/order/delivery',
    order_message_form: './js/pages/order_message/form',
    order_message: './js/pages/order_message',
    order_preferences: './js/pages/order-preferences',
    order_states_form: './js/pages/order-states/form',
    order_states: './js/pages/order-states',
    order_return_states_form: './js/pages/order-return-states/form',
    order_view: './js/pages/order/view.js',
    payment_preferences: './js/pages/payment-preferences',
    product_edit: './js/pages/product/edit',
    combination_edit: './js/pages/product/combination/edit',
    product_page: './js/product-page/index',
    product_preferences: './js/pages/product-preferences',
    profiles: './js/pages/profiles',
    sql_manager: './js/pages/sql-manager',
    stock: './js/app/pages/stock',
    supplier: './js/pages/supplier',
    supplier_form: './js/pages/supplier/supplier-form.js',
    tax: './js/pages/tax',
    tax_rules_group: './js/pages/tax-rules-group',
    themes: './js/pages/themes',
    translation_settings: './js/pages/translation-settings',
    translations: './js/app/pages/translations',
    webservice: './js/pages/webservice',
    zone: './js/pages/zone',
    multistore_header: './js/components/multistore-header.js',
    multistore_dropdown: './js/components/multistore-dropdown',
    theme: './scss/theme.scss',
    orders: './scss/pages/orders/orders.scss',
    product: './scss/pages/product/product_page.scss',
    product_catalog: './scss/pages/product/products_catalog.scss',
    stock_page: './scss/pages/stock/stock_page.scss',
  },
  output: {
    path: path.resolve(__dirname, '../public'),
    filename: '[name].bundle.js',
    libraryTarget: 'window',
    library: '[name]',

    sourceMapFilename: '[name].[hash:8].map',
    chunkFilename: '[id].[hash:8].js',
  },
  resolve: {
    extensions: ['.js', '.vue', '.json'],
    alias: {
      vue$: 'vue/dist/vue.common.js',
      '@app': path.resolve(__dirname, '../js/app'),
      '@js': path.resolve(__dirname, '../js'),
      '@pages': path.resolve(__dirname, '../js/pages'),
      '@components': path.resolve(__dirname, '../js/components'),
      '@scss': path.resolve(__dirname, '../scss'),
      '@node_modules': path.resolve(__dirname, '../node_modules'),
      '@vue': path.resolve(__dirname, '../js/vue'),
    },
  },
  module: {
    rules: [
      {
        test: /\.js$/,
        include: path.resolve(__dirname, '../js'),
        use: [
          {
            loader: 'babel-loader',
            options: {
              presets: [['env', {useBuiltIns: 'usage', modules: false}]],
              plugins: ['transform-object-rest-spread', 'transform-runtime'],
            },
          },
        ],
      },
      {
        test: /jquery-ui\.js/,
        use: 'imports-loader?define=>false&this=>window',
      },
      {
        test: /jquery\.magnific-popup\.js/,
        use: 'imports-loader?define=>false&exports=>false&this=>window',
      },
      {
        test: /bloodhound\.min\.js/,
        use: [
          {
            loader: 'expose-loader',
            query: 'Bloodhound',
          },
        ],
      },
      {
        test: /dropzone\/dist\/dropzone\.js/,
        loader: 'imports-loader?this=>window&module=>null',
      },
      {
        test: require.resolve('moment'),
        loader: 'imports-loader?define=>false&this=>window',
      },
      {
        test: /typeahead\.jquery\.js/,
        loader: 'imports-loader?define=>false&exports=>false&this=>window',
      },
      {
        test: /bootstrap-tokenfield\.js/,
        loader: 'imports-loader?define=>false&exports=>false&this=>window',
      },
      {
        test: /bootstrap-datetimepicker\.js/,
        loader: 'imports-loader?define=>false&exports=>false&this=>window',
      },
      {
        test: /bootstrap-colorpicker\.js/,
        loader: 'imports-loader?define=>false&exports=>false&this=>window',
      },
      {
        test: /jwerty\/jwerty\.js/,
        loader: 'imports-loader?this=>window&module=>false',
      },
      {
        test: /\.vue$/,
        loader: 'vue-loader',
      },
      {
        test: /\.css$/,
        use: [
          {
            loader: MiniCssExtractPlugin.loader,
          },
          'css-loader',
        ],
      },
      {
        test: /\.scss$/,
        include: /scss/,
        exclude: /js/,
        use: [
          MiniCssExtractPlugin.loader,
          {
            loader: 'css-loader',
            options: {
              sourceMap: true,
            },
          },
          {
            loader: 'postcss-loader',
            options: {
              sourceMap: true,
            },
          },
          {
            loader: 'sass-loader',
            options: {
              sourceMap: true,
              sassOptions: {
                includePaths: [bourbon.includePaths],
              },
            },
          },
        ],
      },
      {
        test: /\.scss$/,
        include: /js/,
        use: ['vue-style-loader', 'css-loader', 'sass-loader'],
      },
      // FILES
      {
        test: /.(jpg|png|woff2?|eot|otf|ttf|svg|gif)$/,
        loader: 'file-loader?name=[hash].[ext]',
      },
    ],
  },
  plugins: [
    new FixStyleOnlyEntriesPlugin(),
    new CleanWebpackPlugin({
      cleanOnceBeforeBuildPatterns: ['!theme.rtlfix'],
    }),
    new MiniCssExtractPlugin({filename: '[name].css'}),
    new webpack.ProvidePlugin({
      moment: 'moment', // needed for bootstrap datetime picker
      $: 'jquery', // needed for jquery-ui
      jQuery: 'jquery',
    }),
    new CopyPlugin([{from: 'static'}]),
    new VueLoaderPlugin(),
  ],
};
