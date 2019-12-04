// http://eslint.org/docs/user-guide/configuring

module.exports = {
  env: {
    browser: true,
  },
  globals: {
    google: true,
    document: true,
    navigator: false,
    window: true,
  },
  root: true,
  extends: 'airbnb',
  plugins: [
    'html',
  ],
  rules: {
    'indent': ['error', 4, {'SwitchCase': 1}],
    'no-use-before-define': 0,
    'function-paren-newline': ['off', 'never'],
    'object-curly-spacing': ['error', 'never'],
    'no-debugger': process.env.NODE_ENV === 'production' ? 2 : 0,
    'no-console': process.env.NODE_ENV === 'production' ? 2 : 0,
  }
};
