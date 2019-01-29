const path = require('path');
const webpack = require('webpack');
const CleanWebpackPlugin = require('clean-webpack-plugin');
const VueLoaderPlugin = require('vue-loader/lib/plugin');

module.exports = {
  externals: {
    jquery: 'jQuery',
  },
  entry: {
    main: './js/theme.js',
    catalog: './js/app/pages/catalog',
    stock: './js/app/pages/stock',
    translations: './js/app/pages/translations',
    logs: './js/pages/logs',
    improve_design_positions: './js/pages/improve/design_positions',
    order_preferences: './js/pages/order-preferences',
    order_delivery: './js/pages/order/delivery',
    product_preferences: './js/pages/product-preferences',
    imports: './js/pages/import',
    localization: './js/pages/localization',
    invoices: './js/pages/invoices',
    geolocation: './js/pages/geolocation',
    payment_preferences: './js/pages/payment-preferences',
    email: './js/pages/email',
    sql_manager: './js/pages/sql-manager',
    catalog_product: './js/pages/catalog/product',
    backup: './js/pages/backup',
    categories: './js/pages/categories',
    module_card: './js/app/pages/module-card',
    translation_settings: './js/pages/translation-settings',
    webservice: './js/pages/webservice',
    module: './js/pages/module',
    meta: './js/pages/meta',
    contacts: './js/pages/contacts',
    employee: './js/pages/employee',
    customer: './js/pages/customer',
    language: './js/pages/language',
    product_page: './js/product-page/index',
    currency: './js/pages/currency',
    supplier: './js/pages/supplier',
    themes: './js/pages/themes',
  },
  output: {
    path: path.resolve(__dirname, '../public'),
    filename: '[name].bundle.js',
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
            presets: ['@babel/preset-env']
          }
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
        loader: 'vue-loader'
      },
      // FILES
      {
        test: /.(jpg|png|woff2?|eot|otf|ttf|svg|gif)$/,
        loader: 'file-loader?name=[hash].[ext]',
      },
    ],
  },
  plugins: [
    new CleanWebpackPlugin(['public'], {
      root: path.resolve(__dirname, '../')
    }),
    new VueLoaderPlugin(),
    new webpack.ProvidePlugin({
      moment: 'moment', // needed for bootstrap datetime picker
      $: 'jquery', // needed for jquery-ui
      jQuery: 'jquery',
    }),
  ],
};
