const path = require('path')
const projectRoot = path.resolve(__dirname + '/..')
const appRoot = path.join(projectRoot, 'app')
const themeRoot = path.join(appRoot, 'theme')

module.exports = {
  plugins: {
    'postcss-import': {
      path: [themeRoot],
    },
    'autoprefixer': {},
    'postcss-preset-env': {},
  }
}