import 'module-alias/register';

// Import utils
import helper from '@utils/helpers';
import files from '@utils/files';
import urlsList from '@tools/urls';

import {expect} from 'chai';
import {Browser, BrowserContext, Page} from 'playwright';

// Interfaces
interface outputEntryError{
  type: string,
  msg: string,
}
interface outputEntryBasic {
  url: string,
  status: string,
}
interface outputEntryPage {
  name: string,
  url: string,
  passed: outputEntryBasic[],
  failed: outputEntryBasic[],
  errored: outputEntryError[]
}

// Constants
const LOG_PASSED: string = process.env.LOG_PASSED || 'false';
// Report dir and file
const reportPath: string = 'tools/reports';

const output: {startDate: string, endDate: null|string, pages: outputEntryPage[]} = {
  startDate: new Date().toISOString(),
  endDate: null,
  pages: [],
};

// Variables
let filename: string;
let outputEntry: outputEntryPage = {
  name: '',
  url: '',
  passed: [],
  failed: [],
  errored: [],
};

let responseError: boolean = false;
let responseTextError: string = '';

let javascriptError: boolean = false;
let javascriptTextError: string = '';

let consoleError: boolean = false;
let consoleTextError: string = '';

let page: Page;
let browser: Browser;
let browserContext: BrowserContext;

describe('Crawl every page for defects and issues', async () => {
  before(async () => {
    await files.createDirectory(reportPath);
    // Create report dir
    filename = await files.generateReportFilename();

    // Open browser
    browser = await helper.createBrowser();
    browserContext = await helper.createBrowserContext(browser);
    page = await helper.newTab(browserContext);
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
      section.urls.forEach((
        pageToCrawl: {name: string, url: string, customAction?(page:Page): Promise<void>},
        index: number,
      ) => {
        it(`Crawling ${pageToCrawl.name} (${index + 1}/${section.urls.length})`, async () => {
          const crawledPage = pageToCrawl;
          crawledPage.url = `${section.urlPrefix}${pageToCrawl.url}`;

          await crawlPage(page, crawledPage);

          let somethingFailed = false;
          const errors = [];

          // Checking all type of errors

          if (responseError) {
            somethingFailed = true;
            errors.push(responseTextError);
          }

          if (javascriptError) {
            somethingFailed = true;
            errors.push(javascriptTextError);
          }

          if (consoleError) {
            somethingFailed = true;
            errors.push(consoleTextError);
          }

          // Print all errors
          expect(somethingFailed, `List of errors : \n${errors}`).to.eq(false);
        });
      });
    });
  });
});

/**
 * Crawl Page and write result
 * @param {Page} browserTab
 * @param {name: string, url: string, customAction?(page:Page): Promise<void>} thisPageToCrawl
 * @return {Promise<void>}
 */
async function crawlPage(
  browserTab: Page,
  thisPageToCrawl: {name: string, url: string, customAction?(page:Page): Promise<void>},
) {
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

  await browserTab.goto(`${thisPageToCrawl.url}`, {waitUntil: 'networkidle'});

  if (typeof (thisPageToCrawl.customAction) !== 'undefined') {
    await thisPageToCrawl.customAction(browserTab);
  }

  output.pages.push(outputEntry);
}

/**
 * Check Response status
 * @param url
 * @param status
 * @return {Promise<void>}
 */
async function checkResponseStatus(url: string, status: string): Promise<void> {
  if (status.startsWith('4') || status.startsWith('5')) {
    responseError = true;
    responseTextError = `Request error : ${url} (${status})`;

    outputEntry.failed.push({url, status});
  } else if (JSON.parse(LOG_PASSED) === true) {
    outputEntry.passed.push({url, status});
  }
}
