import 'module-alias/register';
import helper from '@utils/helpers';
import files from '@utils/files';

let screenshotNumber: number = 1;

/**
 * @module MochaHelper
 * @description Helper to define mocha hooks
 */

/**
 * @function before
 * @description Create unique browser for all mocha run
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
 * @function after
 * @description Close browser after finish the run
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

/**
 * @function afterEach
 * @description Take a screenshot if a step is failed
 */
afterEach(async function () {
  // Take screenshot if demanded after failed step
  if (global.SCREENSHOT.AFTER_FAIL && this.currentTest?.state === 'failed') {
    const currentTab = await helper.getLastOpenedTab(this.browser);

    // Take a screenshot
    if (currentTab !== null) {
      await currentTab.screenshot(
        {
          path: `${global.SCREENSHOT.FOLDER}/fail_test_${screenshotNumber}.png`,
          fullPage: true,
        },
      );
    }

    screenshotNumber += 1;
  }
});
