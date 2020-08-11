require('module-alias/register');

const helper = require('@utils/helpers');
const files = require('@utils/files');

/**
 * Create unique browser for all mocha run
 */
before(async function () {
  this.browser = await helper.createBrowser();

  // Create object for browser errors
  if (global.BROWSER.interceptErrors) {
    global.browserErrors = {
      responses: [],
      js: [],
      console: [],
    };
  }
});

/**
 * Close browser after finish the run
 */
after(async function () {
  await helper.closeBrowser(this.browser);

  if (global.BROWSER.interceptErrors) {
    // Delete duplicated errors and create json report
    const browserErrors = {
      responses: [...new Set(global.browserErrors.responses)],
      js: [...new Set(global.browserErrors.js)],
      console: [...new Set(global.browserErrors.console)],
    };

    const reportName = await files.generateReportFilename();
    await files.createFile('.', `${reportName}.json`, JSON.stringify(browserErrors));
  }
});
