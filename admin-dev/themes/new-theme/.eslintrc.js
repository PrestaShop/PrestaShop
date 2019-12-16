// http://eslint.org/docs/user-guide/configuring

module.exports = {
  root: true,
  env: {
    browser: true,
    node: true,
    es6: true,
    jquery: true,
  },
  globals: {
    google: true,
    document: true,
    navigator: false,
    window: true,
    Dropzone: true,
    ps_round: true,

    showErrorMessage: true,
    displayFieldsManager: true,
    refreshTotalCombinations: true,
    showSuccessMessage: true,
    translate_javascripts: true,
    modalConfirmation: true,
    baseAdminDir: true,

    // From default theme
    adminNotificationGetLink: true,
    adminNotificationPushLink: true,
    defaultCategory: true,
    form: true,
    tinyMCE: true,
    priceCalculation: true,
    productCategoriesTags: true,
    supplierCombinations: true,
    tokenAdminCustomerThreads: true,
    tokenAdminCustomers: true,
    tokenAdminOrders: true,
    warehouseCombinations: true,
  },
  parserOptions: {
    parser: 'babel-eslint',
  },
  extends: [
    'prestashop',
    'plugin:vue/strongly-recommended'
  ],
  plugins: [
    'import',
    'vue',
    'html',
  ],
  rules: {
    'no-new': 0,
    'func-names': 0,
    'no-alert': 0,
    'no-bitwise': 0,
    'class-methods-use-this': 0,
    'max-len': ['error', { code: 120 }],
  },
  settings: {
    'import/resolver': 'webpack',
  },
};
