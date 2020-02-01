var Encore = require('@symfony/webpack-encore');

Encore
// the project directory where compiled assets will be stored
    .setOutputPath('public/assets/')
    // the public path used by the web server to access the previous directory
    .setPublicPath('/assets/')
    .cleanupOutputBeforeBuild()
    .enableSourceMaps(!Encore.isProduction())
    // uncomment to create hashed filenames (e.g. app.abc123.css)
    // .enableVersioning(Encore.isProduction())

    // uncomment to define the assets of the project
    .addEntry('scripts', './assets/js/qa.js')
    .addStyleEntry('styles', './assets/css/qa.scss')

    // uncomment if you use Sass/SCSS files
    .enableSassLoader()

    .autoProvidejQuery()

    // uncomment for legacy applications that require $/jQuery as a global variable
    // .autoProvidejQuery()

    .enablePostCssLoader()

    .enableSingleRuntimeChunk()
;

module.exports = Encore.getWebpackConfig();
