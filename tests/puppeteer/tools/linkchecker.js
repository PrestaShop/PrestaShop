const puppeteer = require('puppeteer');
const {expect} = require('chai');
const fs = require('fs');
const urlsList = require('./urls.js');

const reportPath = 'tools/reports';
// filename
const curDate = new Date();
const dateString = curDate.toJSON().slice(0, 10);
const hours = curDate.getHours();
const minutes = curDate.getMinutes();
const seconds = curDate.getSeconds();
const filename = `report_${dateString}_${hours}-${minutes}-${seconds}`;


const URL_FO = process.env.URL_FO || 'http://localhost/prestashop/';
const URL_BO = process.env.URL_BO || `${URL_FO}admin-dev/`;
const LOG_PASSED = process.env.LOG_PASSED || false;
const HEADLESS = process.env.HEADLESS || true;

const LOGININFOS = {
  user: {
    login: process.env.CLIENT_LOGIN || 'pub@prestashop.com',
    password: process.env.CLIENT_PASSWD || '123456789',
  },
  admin: {
    login: process.env.LOGIN || 'demo@prestashop.com',
    password: process.env.PASSWD || 'prestashop_demo',
  },
};

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
let javascriptError = false;

let page;
let browser;
let pagesToCrawl;

/**
 * Create the report folder
 */
(async function () {
  if (!fs.existsSync(reportPath)) await fs.mkdirSync(reportPath);
}());

describe('Crawl every page for defects and issues', async () => {
  before(async () => {
    browser = await puppeteer.launch({
      headless: JSON.parse(HEADLESS),
      timeout: 0,
      slowMo: 5,
      args: ['--start-maximized', '--no-sandbox', '--lang=en-GB'],
      defaultViewport: {
        width: 1680,
        height: 900,
      },
    });

    page = await browser.newPage();
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
      const request = response.request();
      const url = request.url();
      const status = response.status().toString();

      if (status.startsWith('4') || status.startsWith('5')) {
        requestError = true;
        outputEntry.failed.push({
          url,
          status,
        });
      } else if (JSON.parse(LOG_PASSED) === true) {
        outputEntry.passed.push({
          url,
          status,
        });
      }
    });
    // intercepts JS errors
    await page.on('pageerror', (pageerr) => {
      const jsError = pageerr.toString();
      javascriptError = true;
      outputEntry.jsError.push({
        error: jsError,
      });
    });
  });

  after(async () => {
    await browser.close();
    output.endDate = new Date().toISOString();
    fs.writeFile(`${reportPath}/${filename}.json`, JSON.stringify(output), (err) => {
      if (err) {
        return console.error(err);
      }
      return console.log('No error found');
    });
  });

  urlsList.forEach((section) => {
    describe(`${section.name} - ${section.description}`, async () => {
      // crawl every page
      pagesToCrawl = section.urls;
      let count = 1;
      pagesToCrawl.forEach((currentPage) => {
        const pageToCrawl = currentPage;
        pageToCrawl.url = `${section.urlPrefix.replace('URL_BO', URL_BO).replace('URL_FO', URL_FO)}${pageToCrawl.url}`;
        it(`Crawling ${pageToCrawl.name} (${count}/${pagesToCrawl.length})`, async () => {
          await crawlPage({page, pageToCrawl});
          await expect(requestError, 'Request error').to.be.false;
          await expect(javascriptError, 'Javascript error').to.be.false;
        });
        count += 1;
      });
    });
  });
});

async function crawlPage({puppeteerPage, thisPageToCrawl}) {
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
    puppeteerPage.goto(`${thisPageToCrawl.url}`),
    puppeteerPage.waitForNavigation({waitUntil: 'networkidle0'}),
  ]);

  if (typeof (thisPageToCrawl.customAction) !== 'undefined') {
    await thisPageToCrawl.customAction({puppeteerPage, LOGININFOS});
  }
  output.pages.push(outputEntry);
}
