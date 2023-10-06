module.exports = {
    rules: [{
      test: /\.css$/,
      use: [
        require.resolve('style-loader'),
        require.resolve('css-loader'),
        {
          loader: require.resolve('postcss-loader'),
          options: {
            postcssOptions: {
              plugins: {
                "postcss-prefix-selector": {
                  prefix: '.my-prefix',
                  transform(prefix, selector, prefixedSelector, filePath, rule) {
                    return prefixedSelector;
                  },
                },
                autoprefixer: {
                  browsers: ['last 4 versions']
                }
              }
            }
          }
        }
      ]
    }]
  }