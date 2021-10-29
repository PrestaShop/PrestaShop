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
    JQuery: true,
    JQueryStatic: true,
  },
  parserOptions: {
    parser: '@babel/eslint-parser',
    requireConfigFile: false,
  },
  extends: ['prestashop'],
  plugins: ['import'],
  rules: {
    'class-methods-use-this': 0,
    'func-names': 0,
    'import/no-extraneous-dependencies': [
      'error',
      {
        devDependencies: ['tests/**/*.js', '.webpack/**/*.js'],
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
    'vue/script-indent': [
      'error',
      2,
      {
        baseIndent: 1,
        switchCase: 1,
      },
    ],
  },
  settings: {
    'import/resolver': 'webpack',
  },
  overrides: [
    {
      parser: 'vue-eslint-parser',
      parserOptions: {
        parser: '@typescript-eslint/parser',
        project: './tsconfig.json',
        ecmaFeatures: {
          jsx: false,
        },
      },
      files: ['*.vue'],
      rules: {
        indent: 0,
        'vue/no-mutating-props': 0,
        'no-undef': 0,
        'vue/no-template-shadow': 0,
      },
      extends: ['plugin:vue/strongly-recommended', '@vue/typescript'],
      plugins: ['vue', '@typescript-eslint'],
    },
    {
      files: ['*.ts'],
      parser: '@typescript-eslint/parser',
      plugins: ['@typescript-eslint'],
      extends: ['prestashop', 'plugin:@typescript-eslint/eslint-recommended', 'plugin:@typescript-eslint/recommended'],
      rules: {
        'spaced-comment': 0,
        '@typescript-eslint/no-extra-semi': 0,
        'max-len': ['error', {ignoreComments: true, code: 130}],
        'class-methods-use-this': 0,
        'no-alert': 0,
        '@typescript-eslint/ban-ts-comment': 0,
        '@typescript-eslint/no-non-null-assertion': 0,
        '@typescript-eslint/no-explicit-any': 0,
        '@typescript-eslint/no-this-alias': 0,
        '@typescript-eslint/no-inferrable-types': 0,
        '@typescript-eslint/explicit-module-boundary-types': ['error', {allowArgumentsExplicitlyTypedAsAny: true}],
        'func-names': 0,
        'no-new': 0,
      },
    },
  ],
};
