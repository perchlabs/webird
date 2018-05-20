module.exports = {
  presets: [
    ['@babel/preset-env', {modules: false}],
    ['@babel/preset-stage-2', {decoratorsLegacy: true}],
    '@babel/preset-stage-3',
  ],
  plugins: [
    '@babel/plugin-syntax-dynamic-import',
    '@babel/plugin-transform-async-to-generator',
  ],
}