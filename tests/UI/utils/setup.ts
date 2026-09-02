import {
  type Browser,
  type BrowserContext,
  type Page,
  utilsCore,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';
import {
  config,
} from 'chai';

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
  config.truncateThreshold = 0;

  this.browser = await utilsPlaywright.createBrowser();
});

/**
 * @function after
 * @description Close browser after finish the run
 */
after(async function () {
  await utilsPlaywright.closeBrowser(this.browser);
});

const takeScreenShotAfterStep = async (browser: Browser, screenshotPath: string) => {
  const currentTab = await utilsPlaywright.getLastOpenedTab(browser);

  // Take a screenshot
  if (currentTab !== null) {
    await currentTab.screenshot(
      {
        path: screenshotPath.replace('%d', ''),
        fullPage: true,
      },
    );
  }

  // Take screenshots all contexts
  const contexts: BrowserContext[] = browser.contexts();

  for (let incContext = 0; incContext < contexts.length; incContext++) {
    const pathContext: string = incContext < 10 ? `0${incContext.toString()}` : incContext.toString();
    const pages: Page[] = contexts[incContext].pages();

    for (let incPage = 0; incPage < pages.length; incPage++) {
      const pathPage: string = incPage < 10 ? `0${incPage.toString()}` : incPage.toString();
      await pages[incPage].screenshot({
        path: screenshotPath.replace('%d', `_${pathContext}_${pathPage}`),
        fullPage: true,
      });
    }
  }
};

/**
 * @function afterEach
 * @description Take a screenshot if a step is failed
 */
afterEach(async function () {
  // Take screenshot if demanded after failed step
  if (global.SCREENSHOT.AFTER_FAIL && this.currentTest?.state === 'failed') {
    await takeScreenShotAfterStep(this.browser, `${global.SCREENSHOT.FOLDER}/fail_test_${screenshotNumber}%d.png`);
    screenshotNumber += 1;
  }
  if (global.SCREENSHOT.EACH_STEP) {
    const testPath = this.currentTest?.file;
    // eslint-disable-next-line no-unsafe-optional-chaining
    const folderPath = testPath?.slice(testPath?.indexOf('tests/UI') + 8).slice(0, -3);
    let stepId: string = `screenshot-${screenshotNumber}`;

    if (this.currentTest?.title) {
      stepId = `${screenshotNumber}-${this.currentTest?.title}`;
    }

    const screenshotPath = `${global.SCREENSHOT.FOLDER}${folderPath}/${utilsCore.slugify(stepId)}%d.png`;
    await takeScreenShotAfterStep(this.browser, screenshotPath).catch((err) => {
      console.log(`screenshot for ${this.currentTest?.title} failed`, err);
    });
    screenshotNumber += 1;
  }
});
