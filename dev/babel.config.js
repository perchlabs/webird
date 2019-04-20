module.exports = {
  presets: [
    [
      '@babel/preset-env',
      {
        modules: false,
      }
    ],
  ],
  plugins: [
    ["@babel/plugin-proposal-class-properties", { "loose": false }],
    ["@babel/plugin-proposal-decorators", { "legacy": true }],
    "@babel/plugin-proposal-export-namespace-from",
    "@babel/plugin-proposal-function-sent",
    "@babel/plugin-proposal-numeric-separator",
    "@babel/plugin-proposal-throw-expressions",
    "@babel/plugin-syntax-dynamic-import",
    "@babel/plugin-syntax-import-meta",
    '@babel/plugin-transform-runtime'
  ]
}
