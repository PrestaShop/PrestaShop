const path = require('path');

module.exports = {
  entry: './themes/classic/assets/js/app.js', // Replace with your main JS file
  output: {
    filename: 'bundle.js',
    path: path.resolve(__dirname, 'themes/classic/assets/js')
  },
  module: {
    rules: [
      {
        test: /\.scss$/,
        use: [
          'style-loader', // Injects styles into DOM
          'css-loader',   // Turns CSS into JS
          'sass-loader'   // Compiles Sass to CSS
        ]
      },
      {
        test: /\.js$/,
        exclude: /node_modules/,
        use: {
          loader: 'babel-loader', // Transpile modern JS
          options: {
            presets: ['@babel/preset-env']
          }
        }
      }
    ]
  }
  
};
