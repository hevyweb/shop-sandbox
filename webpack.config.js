var Encore = require('@symfony/webpack-encore');

if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore.enableSassLoader();

Encore
    .setOutputPath('public/assets/')
    .setPublicPath('/assets')
    .addEntry('js/app', './assets/js/app.js')
    .addEntry('js/categories', './assets/js/categories.js')
    .addStyleEntry('css/app', './assets/css/app.css')
    .splitEntryChunks()
    .enableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSassLoader()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())
    .configureBabel(() => {}, {
        useBuiltIns: 'usage',
        corejs: 3
    })
    .autoProvidejQuery()
;

module.exports = Encore.getWebpackConfig();
