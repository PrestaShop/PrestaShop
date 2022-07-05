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
  },
  parserOptions: {
    parser: 'babel-eslint',
  },
  extends: [
    'airbnb-base',
  ],
  plugins: [
    'import',
  ],
  rules: {
    indent: ['error', 2, {SwitchCase: 1}],
    'function-paren-newline': ['off', 'never'],
    'object-curly-spacing': ['error', 'never'],
    'padding-line-between-statements': [
      'error',
      {
        blankLine: 'always',
        prev: ['for', 'switch', 'var', 'let', 'const'],
        next: 'return',
      },
      {
        blankLine: 'always',
        prev: ['for', 'switch'],
        next: ['var', 'let', 'const'],
      },
      {
        blankLine: 'always',
        prev: ['var', 'let', 'const'],
        next: ['switch', 'for', 'if'],
      },
    ],
    'no-debugger': process.env.NODE_ENV === 'production' ? 2 : 0,
    'no-console': process.env.NODE_ENV === 'production' ? 2 : 0,
    'class-methods-use-this': 0,
    camelcase: 0,
    'func-names': 0,
    'no-undef': 0,
    'no-use-before-define': 0,
    'import/no-unresolved': 0,
    'import/extensions': ['off', 'never'],
    'import/no-extraneous-dependencies': [
      'error',
      {
        devDependencies: [
          'tests/**/*.js',
          '.webpack/**/*.js',
        ],
      },
    ],
    'max-len': ['error', {code: 120}],
    'no-alert': 0,
    'no-bitwise': 0,
    'no-new': 0,
    'no-param-reassign': ['error', {props: false}],
    'no-restricted-globals': [
      'error',
      {
        name: 'global',
        message: 'Use window variable instead.',
      },
    ],
    'prefer-destructuring': ['error', {object: true, array: false}],
  },
  settings: {
    'import/resolver': 'webpack',
  },
};
