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

    // From default theme
    form: true,
    productCategoriesTags: true,
    defaultCategory: true,
    priceCalculation: true,
    supplierCombinations: true,
    warehouseCombinations: true,
  },
  parserOptions: {
    parser: 'babel-eslint',
  },
  extends: [
    'prestashop',
  ],
  plugins: [
    'import',
    'html',
  ],
  rules: {
    'no-new': 0,
    'func-names': 0,
    'class-methods-use-this': 0,
    'max-len': ['error', { code: 120 }],
  },
  settings: {
    'import/resolver': 'webpack',
  },
};
