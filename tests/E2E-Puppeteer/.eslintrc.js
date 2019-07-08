module.exports = {
  env: {
    node: true,
    es6: true,
  },
  extends: [
    'prestashop',
  ],
  globals: {
    Atomics: 'readonly',
    SharedArrayBuffer: 'readonly',
  },
  parserOptions: {
    ecmaVersion: 2018,
    sourceType: 'module',
  },
  rules: {
  },
};
