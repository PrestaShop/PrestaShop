require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const files = require('@utils/files');
const urlsList = require('@tools/urls.js');

const LOG_PASSED = !!process.env.LOG_PASSED;

// Report dir and file
const reportPath = 'campaigns/tools/reports';
let filename;

const output = {
  startDate: new Date().toISOString(),
  endDate: null,
  pages: [],
};

let outputEntry = {
  name: '',
  url: '',
  passed: [],
  failed: [],
  jsError: [],
};

let requestError = false;
let requestTextError = '';

let javascriptError = false;
let javascriptTextError = '';

let page;
let browser;
let browserContext;

describe('Crawl every page for defects and issues', async () => {
  before(async () => {
    await files.createDirectory(reportPath);
    // Create report dir
    filename = await files.generateReportFilename();

    // Open browser
    browser = await helper.createBrowser();
    browserContext = await helper.createBrowserContext(browser);
    page = await helper.newTab(browserContext);

    // Intercepts responses
    await page.on('response', (response) => {
      checkResponseStatus(
        response.request().url(),
        response.status().toString(),
      );
    });

    // Intercepts JS errors
    await page.on('pageerror', (pageerr) => {
      javascriptTextError = pageerr.toString();
      javascriptError = true;

      outputEntry.jsError.push({
        error: javascriptTextError,
      });
    });
  });


  after(async () => {
    await helper.closeBrowser(browser);

    output.endDate = new Date().toISOString();
    await files.createFile(reportPath, `${filename}.json`, JSON.stringify(output));
  });

  urlsList.forEach((section) => {
    describe(`${section.name} - ${section.description}`, async () => {
      section.urls.forEach((pageToCrawl, index) => {
        it(`Crawling ${pageToCrawl.name} (${index + 1}/${section.urls.length})`, async () => {
          const crawledPage = pageToCrawl;
          crawledPage.url = `${section.urlPrefix}${pageToCrawl.url}`;

          await crawlPage(page, crawledPage);

          // Check no request error
          await expect(requestError, requestTextError).to.be.false;

          // Check no javascript error
          await expect(javascriptError, javascriptTextError).to.be.false;
        });
      });
    });
  });
});

/**
 * Crawl Page and write result
 * @param browserPage
 * @param thisPageToCrawl
 * @return {Promise<void>}
 */
async function crawlPage(browserPage, thisPageToCrawl) {
  requestError = false;
  javascriptError = false;

  outputEntry = {
    name: thisPageToCrawl.name,
    url: thisPageToCrawl.url,
    passed: [],
    failed: [],
    jsError: [],
  };

  await Promise.all([
    browserPage.goto(`${thisPageToCrawl.url}`),
    browserPage.waitForNavigation('load'),
  ]);

  if (typeof (thisPageToCrawl.customAction) !== 'undefined') {
    await thisPageToCrawl.customAction(browserPage);
  }

  output.pages.push(outputEntry);
}

/**
 * Check Response status
 * @param url
 * @param status
 * @return {Promise<void>}
 */
async function checkResponseStatus(url, status) {
  if (status.startsWith('4') || status.startsWith('5')) {
    requestError = true;
    requestTextError = `Request error : ${url} (${status}`;

    outputEntry.failed.push({url, status});
  } else if (JSON.parse(LOG_PASSED) === true) {
    outputEntry.passed.push({url, status});
  }
}
