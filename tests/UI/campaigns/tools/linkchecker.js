require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const files = require('@utils/files');
const urlsList = require('@tools/urls.js');

const LOG_PASSED = process.env.LOG_PASSED || false;

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
  errored: [],
};

let responseError = false;
let responseTextError = '';

let javascriptError = false;
let javascriptTextError = '';

let consoleError = false;
let consoleTextError = '';

let page;
let browser;

describe('Crawl every page for defects and issues', async () => {
  before(async () => {
    await files.createDirectory(reportPath);
    // Create report dir
    filename = await files.generateReportFilename();

    // Open browser
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    await page.setExtraHTTPHeaders({
      'Accept-Language': 'fr-FR',
    });

    // Intercepts responses
    await page.on('response', (response) => {
      checkResponseStatus(
        response.request().url(),
        response.status().toString(),
      );
    });

    // Intercepts JS errors
    await page.on('pageerror', (exception) => {
      javascriptTextError = exception.toString();
      javascriptError = true;

      outputEntry.errored.push({
        type: 'JS error',
        msg: javascriptTextError,
      });
    });


    // Intercept console errors
    await page.on('console', (msg) => {
      // Handle only errors.
      if (msg.type() === 'error') {
        consoleTextError = msg.text();
        consoleError = true;

        outputEntry.errored.push({
          type: 'Console error',
          msg: consoleTextError,
        });
      }
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

          let somethingFailed = false;
          const errors = [];

          // Checking all type of errors

          if (responseError) {
            somethingFailed = true;
            await errors.push(responseTextError);
          }

          if (javascriptError) {
            somethingFailed = true;
            await errors.push(javascriptTextError);
          }

          if (consoleError) {
            somethingFailed = true;
            await errors.push(consoleTextError);
          }

          // Print all errors
          await expect(somethingFailed, `List of errors : \n${errors}`).to.be.false;
        });
      });
    });
  });
});

/**
 * Crawl Page and write result
 * @param puppeteerPage
 * @param thisPageToCrawl
 * @return {Promise<void>}
 */
async function crawlPage(puppeteerPage, thisPageToCrawl) {
  responseError = false;
  javascriptError = false;
  consoleError = false;

  outputEntry = {
    name: thisPageToCrawl.name,
    url: thisPageToCrawl.url,
    passed: [],
    failed: [],
    errored: [],
  };

  await puppeteerPage.goto(`${thisPageToCrawl.url}`, {waitUntil: 'networkidle0'});

  if (typeof (thisPageToCrawl.customAction) !== 'undefined') {
    await thisPageToCrawl.customAction(puppeteerPage);
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
    responseError = true;
    responseTextError = `Request error : ${url} (${status})`;

    outputEntry.failed.push({url, status});
  } else if (JSON.parse(LOG_PASSED) === true) {
    outputEntry.passed.push({url, status});
  }
}
