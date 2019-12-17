require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const files = require('@utils/files');
const urlsList = require('./urls.js');

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
  jsError: [],
};
let requestError = false;
let requestTextError = '';
let javascriptError = false;
let jsError = '';

let page;
let browser;

describe('Crawl every page for defects and issues', async () => {
  before(async () => {
    await files.createDirectory(reportPath);
    // Create report dir
    filename = await files.generateReportFilename();
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    await page.setExtraHTTPHeaders({
      'Accept-Language': 'fr-FR',
    });

    await page.setRequestInterception(true);
    // intercepts requests
    await page.on('request', (request) => {
      request.continue();
    });
    // intercepts responses
    await page.on('response', (response) => {
      checkResponseStatus(
        response.request().url(),
        response.status().toString(),
      );
    });
    // intercepts JS errors
    await page.on('pageerror', (pageerr) => {
      jsError = pageerr.toString();
      javascriptError = true;
      outputEntry.jsError.push({
        error: jsError,
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
          pageToCrawl.url = `${section.urlPrefix}${pageToCrawl.url}`
            .replace('URL_BO', global.BO.URL)
            .replace('URL_FO', global.FO.URL)
          ;
          await crawlPage(page, pageToCrawl);
          await expect(requestError, requestTextError).to.be.false;
          await expect(javascriptError, jsError).to.be.false;
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
  requestError = false;
  javascriptError = false;
  outputEntry = {
    name: thisPageToCrawl.name,
    url: thisPageToCrawl.url,
    passed: [],
    failed: [],
    jsError: [],
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
    requestError = true;
    requestTextError = `Request error : ${url} (${status}`;
    outputEntry.failed.push({url,status});
  } else if (JSON.parse(LOG_PASSED) === true) {
    outputEntry.passed.push({url,status});
  }
}
