// http://eslint.org/docs/user-guide/configuring

module.exports = {
    env: {
        browser: true,
        node: true,
        es6: true,
    },
    globals: {
        google: true,
        document: false,
        navigator: false,
        window: false,
    },
    root: true,
    extends: 'airbnb',
    plugins: [
        'import',
        'html',
    ],
    rules: {
        'indent': ['error', 4, {'SwitchCase': 1}],
        'import/no-unresolved': 0,
        'function-paren-newline': ['off', 'never'],
        'object-curly-spacing': ['error', 'never'],
        'no-debugger': process.env.NODE_ENV === 'production' ? 2 : 0,
        'no-console': process.env.NODE_ENV === 'production' ? 2 : 0,
        'import/extensions': ['off', 'never'],
    }
};
