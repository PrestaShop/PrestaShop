module.exports = {
  env: {
    node: true,
    es6: true,
    mocha: true,
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
    'no-plusplus': [2, {allowForLoopAfterthoughts: true}],
    'func-names': 'off',
    'no-await-in-loop': 'off',
    'class-methods-use-this': 'off',
    'max-len': [2, {code: 120}],
    'no-underscore-dangle': 'off',
    'no-shadow': 'off'
  },
};
