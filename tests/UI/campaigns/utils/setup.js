require('module-alias/register');

const helper = require('@utils/helpers');
const files = require('@utils/files');

let failNumber = 1;
const maxFailNumber = 250;
const screenshotPath = number => `./screenshots/fail_test_${number}.png`;

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


afterEach(async function () {
  // Take screenshot if demanded after failed step
  if (this.currentTest.state === 'failed') {
    if (global.TAKE_SCREENSHOT_AFTER_FAIL) {
      const currentTab = await helper.getLastOpenedTab(this.browser);

      // Take a screenshot
      const screenshotOptions = {
        path: screenshotPath(failNumber),
        fullPage: true,
      };

      await currentTab.screenshot(screenshotOptions);
    }

    failNumber += 1;
  }
});

beforeEach(async function () {
  // Skipping tests if we reached the maximum of failing tests
  if (failNumber > maxFailNumber && !global.GENERATE_FAILED_STEPS) {
    this.skip();
  }
});
