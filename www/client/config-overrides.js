const {
    override,
    addDecoratorsLegacy,
    disableEsLint,
    overrideDevServer,
    watchAll
  } = require("customize-cra");
  
  module.exports = {
    webpack: override(
        addDecoratorsLegacy(),
        disableEsLint(),
    ),
    devServer: overrideDevServer(
        // dev server plugin
        watchAll()
    )
  };
