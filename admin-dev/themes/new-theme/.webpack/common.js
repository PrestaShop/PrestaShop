const fs = require('fs');
const path = require('path');
const webpack = require('webpack');
const CleanWebpackPlugin = require('clean-webpack-plugin');
const CopyPlugin = require('copy-webpack-plugin');
const ExtractTextPlugin = require('extract-text-webpack-plugin');
const cssExtractedFileName = 'theme';

module.exports = {
  externals: {
    jquery: 'jQuery',
  },
  entry: {
    backup: './js/pages/backup',
    catalog: './js/app/pages/catalog',
    catalog_product: './js/pages/catalog/product',
    category: './js/pages/category',
    cldr: './js/app/cldr',
    cms_page: './js/pages/cms-page',
    cms_page_form: './js/pages/cms-page/form',
    contacts: './js/pages/contacts',
    currency: './js/pages/currency',
    customer: './js/pages/customer',
    email: './js/pages/email',
    employee: './js/pages/employee/index',
    employee_form: './js/pages/employee/form',
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
    meta: './js/pages/meta',
    module: './js/pages/module',
    module_card: './js/app/pages/module-card',
    order_delivery: './js/pages/order/delivery',
    order_preferences: './js/pages/order-preferences',
    payment_preferences: './js/pages/payment-preferences',
    product_page: './js/product-page/index',
    product_preferences: './js/pages/product-preferences',
    profiles: './js/pages/profiles',
    sql_manager: './js/pages/sql-manager',
    stock: './js/app/pages/stock',
    supplier: './js/pages/supplier',
    tax: './js/pages/tax',
    themes: './js/pages/themes',
    translation_settings: './js/pages/translation-settings',
    translations: './js/app/pages/translations',
    webservice: './js/pages/webservice',
    error: './js/pages/error',
    monitoring: './js/pages/monitoring',
    order: './js/pages/order',
  },
  output: {
    path: path.resolve(__dirname, '../public'),
    filename: '[name].bundle.js',
    libraryTarget: 'window',
    library: '[name]',
  },
  resolve: {
    extensions: ['.js', '.vue', '.json'],
    alias: {
      vue$: 'vue/dist/vue.common.js',
      app: path.resolve(__dirname, '../js/app'),
    },
  },
  module: {
    rules: [
      {
        test: /\.js$/,
        include: path.resolve(__dirname, '../js'),
        use: [{
          loader: 'babel-loader',
          options: {
            presets: [
              ['es2015', {modules: false}],
            ],
          },
        }],
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
        test: /jwerty\/jwerty\.js/,
        loader: 'imports-loader?this=>window&module=>false',
      },
      {
        test: /\.vue$/,
        loader: 'vue-loader',
        options: {
          loaders: {
            js: 'babel-loader?presets[]=es2015&presets[]=stage-2',
            css: 'postcss-loader',
            scss: 'style-loader!css-loader!sass-loader',
          },
        },
      },
      {
        test: /\.css$/,
        use: ExtractTextPlugin.extract({
          fallback: 'style-loader',
          use: ['css-loader'],
        }),
      },
      {
        test: /\.scss$/,
        use: ExtractTextPlugin.extract({
          use: [
            {
              loader: 'css-loader',
              options: {
                minimize: true,
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
              },
            },
          ],
        }),
      },
      // FILES
      {
        test: /.(jpg|png|woff2?|eot|otf|ttf|svg|gif)$/,
        loader: 'file-loader?name=[hash].[ext]',
      },
    ],
  },
  plugins: [
    new ExtractTextPlugin('theme.css'),
    new CleanWebpackPlugin(['public'], {
      root: path.resolve(__dirname, '../'),
      exclude: ['theme.rtlfix']
    }),
    new webpack.ProvidePlugin({
      moment: 'moment', // needed for bootstrap datetime picker
      $: 'jquery', // needed for jquery-ui
      jQuery: 'jquery',
    }),
    new CopyPlugin([
      { from: 'static' },
    ])
  ],
};
