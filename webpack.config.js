const Encore = require('@symfony/webpack-encore');

// Configure the runtime environment if not already configured
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    // Directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // Public path used by the web server to access the output path
    .setPublicPath('/build')
    // Entry file for JavaScript and CSS
    .addEntry('app', './assets/app.js')

    // Enable file splitting for optimization
    .splitEntryChunks()

    // Enable React
    .enableReactPreset()

    // Enable TypeScript support (required for .tsx files)
    .enableTypeScriptLoader()

    // Enable single runtime chunk
    .enableSingleRuntimeChunk()

    // Additional configurations
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())

    // Configure Babel for polyfills
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = '3.38';
    })

    // Enable PostCSS loader
    .enablePostCssLoader();
;

module.exports = Encore.getWebpackConfig();
