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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
const path = require('path');
const webpack = require('webpack');
const {CleanWebpackPlugin} = require('clean-webpack-plugin');
const CopyPlugin = require('copy-webpack-plugin');
const {VueLoaderPlugin} = require('vue-loader');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const RemoveEmptyScriptsPlugin = require('webpack-remove-empty-scripts');
const bourbon = require('bourbon');
const ForkTsCheckerWebpackPlugin = require('fork-ts-checker-webpack-plugin');
const HtmlWebpackPlugin = require('html-webpack-plugin');
const FontPreloadPlugin = require('webpack-font-preload-plugin');
const CssoWebpackPlugin = require('csso-webpack-plugin').default;

module.exports = {
  externals: {
    jquery: 'jQuery',
  },
  entry: {
    address: './js/pages/address',
    attachment: './js/pages/attachment',
    attribute: './js/pages/attribute',
    attribute_group: './js/pages/attribute-group',
    authorization_server: './js/pages/authorization-server',
    backup: './js/pages/backup',
    carrier: './js/pages/carrier',
    cart_rule: './js/pages/cart-rule',
    catalog: './js/app/pages/catalog',
    catalog_price_rule: './js/pages/catalog-price-rule',
    catalog_price_rule_form: './js/pages/catalog-price-rule/form',
    catalog_product: './js/pages/catalog/product',
    category: './js/pages/category',
    cldr: './js/app/cldr',
    cms_page: './js/pages/cms-page',
    cms_page_form: './js/pages/cms-page/form',
    combination_bulk: './js/pages/product/combination/bulk',
    combination_form: './js/pages/product/combination/form',
    contacts: './js/pages/contacts',
    credit_slip: './js/pages/credit-slip',
    currency: './js/pages/currency',
    currency_form: './js/pages/currency/form',
    customer: './js/pages/customer/index',
    customer_form: './js/pages/customer/form',
    customer_address_form: './js/pages/address/form',
    customer_outstanding: './js/pages/outstanding',
    customer_preferences: './js/pages/customer-preferences',
    customer_thread: './js/pages/customer-thread/index',
    customer_thread_view: './js/pages/customer-thread/view',
    customer_threads: './scss/pages/customer_thread/customer_thread.scss',
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
    main: './js/theme',
    maintenance: './js/pages/maintenance',
    manufacturer: './js/pages/manufacturer',
    manufacturer_address_form:
      './js/pages/manufacturer/manufacturer_address_form',
    merchandise_return: './js/pages/merchandise-return',
    meta: './js/pages/meta',
    module: './js/pages/module',
    module_card: './js/app/pages/module-card',
    monitoring: './js/pages/monitoring',
    multistore_dropdown: './js/components/multistore-dropdown',
    multistore_header: './js/components/multistore-header',
    order: './js/pages/order',
    order_create: './js/pages/order/create',
    order_delivery: './js/pages/order/delivery',
    order_message: './js/pages/order_message',
    order_message_form: './js/pages/order_message/form',
    order_preferences: './js/pages/order-preferences',
    order_return_states_form: './js/pages/order-return-states/form',
    order_states: './js/pages/order-states',
    order_states_form: './js/pages/order-states/form',
    order_view: './js/pages/order/view',
    orders: './scss/pages/orders/orders.scss',
    payment_preferences: './js/pages/payment-preferences',
    performance_preferences: './js/pages/performance-preferences',
    permission: './js/pages/permission',
    permissions: './scss/pages/permissions/permissions.scss',
    product: './scss/pages/product/product_page.scss',
    product_catalog: './scss/pages/product/products_catalog.scss',
    product_create: './js/pages/product/create',
    product_edit: './js/pages/product/edit',
    product_index: './js/pages/product/grid/index',
    product_page: './js/product-page/index',
    product_preferences: './js/pages/product-preferences',
    product_shops: './js/pages/product/shop',
    pre_select_product_shop: './js/pages/product/shop/pre-select-product-shop',
    profiles: './js/pages/profiles',
    search_engine: './js/pages/search-engine',
    security: './js/pages/security',
    shipping_preferences: './js/pages/shipping-preferences',
    specific_price_form: './js/pages/product/specific-price/form',
    sql_manager: './js/pages/sql-manager',
    state: './js/pages/state',
    stock: './js/app/pages/stock',
    stock_page: './scss/pages/stock/stock_page.scss',
    supplier: './js/pages/supplier',
    supplier_form: './js/pages/supplier/supplier-form',
    tax: './js/pages/tax',
    tax_rules_group: './js/pages/tax-rules-group',
    light_theme: './scss/light_theme.scss',
    theme: './scss/theme.scss',
    rtl: './scss/rtl.scss',
    themes: './js/pages/themes',
    title: './js/pages/title',
    translation_settings: './js/pages/translation-settings',
    translations: './js/app/pages/translations',
    webservice: './js/pages/webservice',
    zone: './js/pages/zone',
    country: './js/pages/country',
    country_form: './js/pages/country/form',
    create_product: './js/pages/product/create/create-product',
    create_product_default_theme: './scss/pages/product/create_product_default_theme.scss',
  },
  output: {
    publicPath: '',
    path: path.resolve(__dirname, '../public'),
    filename: '[name].bundle.js',
    libraryTarget: 'window',
    library: '[name]',

    sourceMapFilename: '[name].[hash:8].map',
    chunkFilename: '[id].[hash:8].js',
  },
  resolve: {
    extensions: ['.ts', '.js', '.vue', '.json'],
    alias: {
      vue: 'vue/dist/vue.esm-bundler.js',
      '@app': path.resolve(__dirname, '../js/app'),
      '@js': path.resolve(__dirname, '../js'),
      '@pages': path.resolve(__dirname, '../js/pages'),
      '@components': path.resolve(__dirname, '../js/components'),
      '@scss': path.resolve(__dirname, '../scss'),
      '@node_modules': path.resolve(__dirname, '../node_modules'),
      '@PSVue': path.resolve(__dirname, '../js/vue'),
      '@PSTypes': path.resolve(__dirname, '../js/types'),
      '@images': path.resolve(__dirname, '../img'),
    },
  },
  module: {
    rules: [
      {
        test: /\.vue$/,
        loader: 'vue-loader',
      },
      {
        test: /\.js$/,
        include: path.resolve(__dirname, '../js'),
        use: [
          {
            loader: 'esbuild-loader',
          },
        ],
      },
      {
        test: /\.ts?$/,
        include: path.resolve(__dirname, '../js'),
        loader: 'esbuild-loader',
        options: {
          loader: 'ts',
          target: 'es2015',
        },
        exclude: /node_modules/,
      },
      {
        test: /jquery-ui\.js/,
        loader: 'imports-loader',
        options: {
          wrapper: {
            thisArg: 'window',
            args: {
              define: false,
            },
          },
        },
      },
      {
        test: /jquery\.magnific-popup\.js/,
        loader: 'imports-loader',
        options: {
          wrapper: {
            thisArg: 'window',
            args: {
              define: false,
              exports: false,
            },
          },
        },
      },
      {
        test: /bloodhound\.min\.js/,
        use: [
          {
            loader: 'expose-loader',
            options: {
              exposes: 'Bloodhound',
            },
          },
        ],
      },
      {
        test: require.resolve('dropzone'),
        loader: 'imports-loader',
        options: {
          wrapper: {
            thisArg: 'window',
            args: {
              module: false,
            },
          },
        },
      },
      {
        test: require.resolve('moment'),
        loader: 'imports-loader',
        options: {
          wrapper: {
            thisArg: 'window',
            args: {
              define: false,
            },
          },
        },
      },
      {
        test: /typeahead\.jquery\.js/,
        loader: 'imports-loader',
        options: {
          wrapper: {
            thisArg: 'window',
            args: {
              define: false,
              exports: false,
            },
          },
        },
      },
      {
        test: /bootstrap-tokenfield\.js/,
        loader: 'imports-loader',
        options: {
          wrapper: {
            thisArg: 'window',
            args: {
              define: false,
              exports: false,
            },
          },
        },
      },
      {
        test: /bootstrap-datetimepicker\.js/,
        loader: 'imports-loader',
        options: {
          wrapper: {
            thisArg: 'window',
            args: {
              define: false,
              exports: false,
            },
          },
        },
      },
      {
        test: /bootstrap-colorpicker\.js/,
        loader: 'imports-loader',
        options: {
          wrapper: {
            thisArg: 'window',
            args: {
              define: false,
              exports: false,
            },
          },
        },
      },
      {
        test: /jwerty\/jwerty\.js/,
        loader: 'imports-loader',
        options: {
          wrapper: {
            thisArg: 'window',
            args: {
              module: false,
            },
          },
        },
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
                includePaths: bourbon.includePaths,
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
        type: 'asset/resource',
        generator: {
          filename: '[hash].[ext]',
        },
        exclude: /MaterialIcons-Regular\.(woff2?|ttf)$/,
      },
      {
        test: /MaterialIcons-Regular\.(woff2?|ttf)$/,
        type: 'asset/resource',
        generator: {
          filename: '[hash].preload.[ext]',
        },
      },
    ],
  },
  plugins: [
    new RemoveEmptyScriptsPlugin(),
    new CleanWebpackPlugin({
      cleanOnceBeforeBuildPatterns: ['!theme.rtlfix'],
    }),
    new MiniCssExtractPlugin({filename: '[name].css'}),
    new webpack.ProvidePlugin({
      moment: 'moment', // needed for bootstrap datetime picker
      $: 'jquery', // needed for jquery-ui
      jQuery: 'jquery',
    }),
    new CopyPlugin({
      patterns: [{from: 'static'}],
    }),
    new VueLoaderPlugin(),
    new ForkTsCheckerWebpackPlugin({
      typescript: {
        extensions: {
          vue: {
            enabled: true,
            compiler: '@vue/compiler-sfc',
          },
        },
        diagnosticOptions: {
          semantic: true,
          syntactic: true,
        },
      },
    }),
    new HtmlWebpackPlugin({
      filename: 'preload.tpl',
      templateContent: '{{{preloadLinks}}}',
      inject: false,
    }),
    new webpack.DefinePlugin({
      __VUE_OPTIONS_API__: true,
      __VUE_PROD_DEVTOOLS__: false,
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
  ],
};
