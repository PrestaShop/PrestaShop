require("babel-polyfill");

exports.config = {
    user: process.env.SAUCE_USERNAME,
    key: process.env.SAUCE_ACCESS_KEY,
    //
    // ==================
    // Specify Test Files
    // ==================
    // Define which test specs should run. The pattern is relative to the directory
    // from which `wdio` was called. Notice that, if you are calling `wdio` from an
    // NPM script (see https://docs.npmjs.com/cli/run-script) then the current working
    // directory is where your package.json resides, so `wdio` will be called from there.
    //
    specs: [
        './specs/**/*.js'
    ],
    // Patterns to exclude.
    exclude: [
        // 'path/to/excluded/files'
    ],
    //
    // ============
    // Capabilities
    // ============
    // Define your capabilities here. WebdriverIO can run multiple capabilties at the same
    // time. Depending on the number of capabilities, WebdriverIO launches several test
    // sessions. Within your capabilities you can overwrite the spec and exclude option in
    // order to group specific specs to a specific capability.
    //
    // If you have trouble getting all important capabilities together, check out the
    // Sauce Labs platform configurator - a great tool to configure your capabilities:
    // https://docs.saucelabs.com/reference/platforms-configurator
    //
    capabilities: [{
        browserName: require('./settings').browserName,
        chromeOptions: {
          args: ['--headless', '--disable-gpu', '--window-size=1600,1200'],
        },
        tunnelIdentifier: process.env.TRAVIS_JOB_NUMBER,
        screenResolution: "1600x1200",
        platform: "Windows 7",
        name: process.env.TRAVIS_COMMIT
    }],
    //
    // ===================
    // Test Configurations
    // ===================
    // Define all options that are relevant for the WebdriverIO instance here
    //
    // Level of logging verbosity.
    logLevel: 'silent',
    //
    // Enables colors for log output.
    coloredLogs: true,
    //
    // Saves a screenshot to a given path if a command fails.
    screenshotPath: './errorShots/',
    //
    // Set a base URL in order to shorten url command calls. If your url parameter starts
    // with "/", the base url gets prepended.
    baseUrl: require('./settings').baseUrl,
    //
    // Default timeout for all waitForXXX commands.
    waitforTimeout: 2500,
    waitforInterval: 1000,
    //
    // Initialize the browser instance with a WebdriverIO plugin. The object should have the
    // plugin name as key and the desired plugin options as property. Make sure you have
    // the plugin installed before running any tests. The following plugins are currently
    // available:
    // WebdriverCSS: https://github.com/webdriverio/webdrivercss
    // WebdriverRTC: https://github.com/webdriverio/webdriverrtc
    // Browserevent: https://github.com/webdriverio/browserevent
    // plugins: {
    //     webdrivercss: {
    //         screenshotRoot: 'my-shots',
    //         failedComparisonsRoot: 'diffs',
    //         misMatchTolerance: 0.05,
    //         screenWidth: [320,480,640,1024]
    //     },
    //     webdriverrtc: {},
    //     browserevent: {}
    // },
    //
    // Framework you want to run your specs with.
    // The following are supported: mocha, jasmine and cucumber
    // see also: http://webdriver.io/guide/testrunner/frameworks.html
    //
    // Make sure you have the node package for the specific framework installed before running
    // any tests. If not please install the following package:
    // Mocha: `$ npm install mocha`
    // Jasmine: `$ npm install jasmine`
    // Cucumber: `$ npm install cucumber`
    framework: 'mocha',
    //
    // Test reporter for stdout.
    // The following are supported: dot (default), spec and xunit
    // see also: http://webdriver.io/guide/testrunner/reporters.html
    reporter: 'spec',

    //
    // Options to be passed to Mocha.
    // See the full list at http://mochajs.org/
    mochaOpts: {
        ui: 'bdd',
        compilers: ['js:babel-register'],
        bail: true,
        timeout: 10000
    },

    //
    // =====
    // Hooks
    // =====
    // Run functions before or after the test. If one of them returns with a promise, WebdriverIO
    // will wait until that promise got resolved to continue.
    // see also: http://webdriver.io/guide/testrunner/hooks.html
    //
    // Gets executed before all workers get launched.
    onPrepare: function() {
        // do something
    },
    //
    // Gets executed before test execution begins. At this point you will have access to all global
    // variables like `browser`. It is the perfect place to define custom commands.
    before: function() {
        /* global browser */
        var chai = require('chai');
        var chaiAsPromised = require('chai-as-promised');
        chai.use(chaiAsPromised);
        chai.should();
        // this hook allows to define custom functions on the global browser object
        require('./commands/init')(browser);
        return browser.windowHandleSize({width: 1600, height: 1200});
    },
    //
    // Gets executed after all tests are done. You still have access to all global variables from
    // the test.
    after: function(failures, pid) {
        // do something
    },
    //
    // Gets executed after all workers got shut down and the process is about to exit. It is not
    // possible to defer the end of the process using a promise.
    onComplete: function() {
        // do something
    }
};
