const fs = require('fs');
const path = require('path');
const webpack = require('webpack');
const CleanWebpackPlugin = require('clean-webpack-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const OptimizeCSSAssetsPlugin = require('optimize-css-assets-webpack-plugin');
const VueLoaderPlugin = require('vue-loader/lib/plugin');
const cssExtractedFileName = 'theme';

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
    profiles: './js/pages/profiles',
    cms_page: './js/pages/cms-page',
    form_popover_error: './js/components/form/form-popover-error.js',
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
    },
    minimizer: [
      new OptimizeCSSAssetsPlugin(),
    ]
  },
  module: {
    rules: [
      {
        test:/\.(s*)css$/,
        use: [
          MiniCssExtractPlugin.loader,
          'css-loader',
          'postcss-loader',
          'sass-loader'
        ]
      },
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
    new MiniCssExtractPlugin({
      filename: '[name].css'
    }),
    new CleanWebpackPlugin(['public'], {
      root: path.resolve(__dirname, '../'),
      exclude: ['theme.rtlfix']
    }),
    new VueLoaderPlugin(),
    new webpack.ProvidePlugin({
      moment: 'moment', // needed for bootstrap datetime picker
      $: 'jquery', // needed for jquery-ui
      jQuery: 'jquery',
    }),
    {
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
    },
  ],
};
