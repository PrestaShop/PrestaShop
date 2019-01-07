// http://eslint.org/docs/user-guide/configuring

module.exports = {
  env: {
    browser: true,
    node: true,
    es6: true,
  },
  globals: {
    google: true,
    document: true,
    navigator: false,
    window: true,
  },
  parserOptions: {
    ecmaVersion: 6,
    sourceType: "module"
  },
  root: true,
  extends: 'airbnb-base',
  plugins: [
    'import',
    'html',
  ],
  rules: {
    'indent': ['error', 2, {'SwitchCase': 1}],
    'import/no-unresolved': 0,
    'no-use-before-define': 0,
    'function-paren-newline': ['off', 'never'],
    'object-curly-spacing': ['error', 'never'],
    'no-debugger': process.env.NODE_ENV === 'production' ? 2 : 0,
    'no-console': process.env.NODE_ENV === 'production' ? 2 : 0,
    'import/extensions': ['off', 'never'],
  }
};
