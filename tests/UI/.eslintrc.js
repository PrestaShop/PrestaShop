module.exports = {
  env: {
    node: true,
    es6: true,
    mocha: true,
  },
  extends: [
    'airbnb-base',
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
    'no-shadow': 'off',
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
    'import/no-unresolved': 0,
    'import/extensions': ['off', 'never'],
    'no-use-before-define': 0,
  },
};
