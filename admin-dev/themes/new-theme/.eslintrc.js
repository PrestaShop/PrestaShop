// http://eslint.org/docs/user-guide/configuring

module.exports = {
  env: {
    browser: true,
    commonjs: true,
    node: true,
    es6: true,
    jquery: true,
  },
  globals: {
    google: true,
    document: true,
    navigator: false,
    window: true,
  },
  parserOptions: {
    ecmaVersion: 6,
    sourceType: 'module',
  },
  root: true,
  extends: 'prestashop',
  plugins: [
    'import',
    'html',
  ],
  rules: {
  },
  settings: {
    'import/resolver': 'webpack',
  },
};
