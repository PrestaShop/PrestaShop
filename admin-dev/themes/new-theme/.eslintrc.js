// http://eslint.org/docs/user-guide/configuring

const fs = require('fs');
const path = require('path');
const airbnbBestPractices = require('eslint-config-airbnb-base/rules/best-practices');
const airbnbStyle = require('eslint-config-airbnb-base/rules/style');
const airbnbVariables = require('eslint-config-airbnb-base/rules/variables');
// Files that still rely on jQuery: remove an entry once its file is converted or deleted,
// and only add one when a listed file is moved or renamed.
const jqueryLegacyFiles = require('./.eslint-jquery-legacy.json');

const missingLegacyFiles = jqueryLegacyFiles.filter((file) => !fs.existsSync(path.join(__dirname, file)));

if (missingLegacyFiles.length > 0) {
  throw new Error(`.eslint-jquery-legacy.json lists files that no longer exist: ${missingLegacyFiles.join(', ')}`);
}

const globalRestriction = {
  name: 'global',
  message: 'Use window variable instead.',
};
const jqueryMessage = 'jQuery is being phased out of the back office, use native DOM APIs instead.';
const jqueryGlobals = ['$', 'jQuery'].map((name) => ({name, message: jqueryMessage}));
const noJqueryRules = {
  'no-restricted-imports': ['error', {paths: [{name: 'jquery', message: jqueryMessage}]}],
  'no-restricted-properties': [
    ...airbnbBestPractices.rules['no-restricted-properties'],
    ...jqueryGlobals.map(({name}) => ({object: 'window', property: name, message: jqueryMessage})),
  ],
  'no-restricted-syntax': [
    ...airbnbStyle.rules['no-restricted-syntax'],
    {
      selector: 'TSTypeReference[typeName.name=/^JQuery/], TSTypeReference TSQualifiedName[left.name=/^JQuery/]',
      message: jqueryMessage,
    },
  ],
};

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
  extends: ['airbnb-base'],
  plugins: ['import'],
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
    'import/no-unresolved': 0,
    'import/extensions': ['off', 'never'],
    'no-use-before-define': 0,
    'class-methods-use-this': 0,
    'func-names': 0,
    'import/no-extraneous-dependencies': [
      'error',
      {
        devDependencies: ['tests/**/*.js', '.webpack/**/*.js', '.eslintrc.js'],
      },
    ],
    'max-len': ['error', {code: 120}],
    'no-alert': 0,
    'no-bitwise': 0,
    'no-new': 0,
    'no-param-reassign': ['error', {props: false}],
    'no-restricted-globals': ['error', globalRestriction],
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
        'vue/html-closing-bracket-spacing': 0,
        'vue/no-v-for-template-key': 0,
        'vue/multi-word-component-names': 0,
      },
      extends: ['plugin:vue/strongly-recommended', '@vue/typescript'],
      plugins: ['vue', '@typescript-eslint'],
    },
    {
      files: ['*.ts'],
      parser: '@typescript-eslint/parser',
      plugins: ['@typescript-eslint'],
      extends: ['airbnb-base', 'plugin:@typescript-eslint/eslint-recommended', 'plugin:@typescript-eslint/recommended'],
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
        'import/no-unresolved': 0,
        'import/extensions': ['off', 'never'],
        'no-use-before-define': 0,
        'spaced-comment': 0,
        '@typescript-eslint/no-extra-semi': 0,
        'max-len': ['error', {ignoreComments: true, code: 130}],
        'class-methods-use-this': 0,
        'no-alert': 0,
        '@typescript-eslint/ban-ts-comment': 0,
        'vue/no-v-for-template-key': 0,
        '@typescript-eslint/no-non-null-assertion': 0,
        '@typescript-eslint/no-explicit-any': 0,
        '@typescript-eslint/no-this-alias': 0,
        '@typescript-eslint/no-inferrable-types': 0,
        '@typescript-eslint/explicit-module-boundary-types': ['error', {allowArgumentsExplicitlyTypedAsAny: true}],
        'func-names': 0,
        'no-new': 0,
      },
    },
    {
      files: ['*.js', '*.vue'],
      excludedFiles: jqueryLegacyFiles,
      rules: {
        ...noJqueryRules,
        'no-restricted-globals': ['error', globalRestriction, ...jqueryGlobals],
      },
    },
    {
      files: ['*.ts'],
      excludedFiles: jqueryLegacyFiles,
      rules: {
        ...noJqueryRules,
        'no-restricted-globals': [...airbnbVariables.rules['no-restricted-globals'], ...jqueryGlobals],
      },
    },
  ],
};
