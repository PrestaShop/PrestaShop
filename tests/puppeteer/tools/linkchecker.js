const puppeteer = require('puppeteer');
const expect = require('chai').expect;
const fs = require('fs');

const reportPath = 'reports';
// filename
const curDate = new Date();
const dateString = curDate.toJSON().slice(0, 10);
const hours = curDate.getHours();
const minutes = curDate.getMinutes();
const seconds = curDate.getSeconds();
const filename = `report_${dateString}_${hours}${minutes}${seconds}`;

const urlsList = require('./urls.js');

const URL_FO = process.env.URL_FO || 'http://localhost/prestashop/';
const URL_BO = process.env.URL_BO || `${URL_FO}admin-dev/`;

const LOG_PASSED = process.env.LOG_PASSED || false;

const loginInfos = {
  user : {
    login: process.env.CLIENT_LOGIN || 'pub@prestashop.com',
    password : process.env.CLIENT_PASSWD || '123456789'
  },
  admin: {
    login: process.env.LOGIN || 'demo@prestashop.com',
    password : process.env.PASSWD || 'prestashop_demo',
  }
};
const HEADLESS = process.env.HEADLESS || true;

let output = {
  startDate : new Date().toISOString(),
  endDate : null,
  pages : []
};
let outputEntry = {
  name : '',
  url : '',
  passed : [],
  failed: []
};

let page = null;

/**
 * Create the report folder
 */
(async function() {
  if (!fs.existsSync(reportPath)) await fs.mkdirSync(reportPath);
})();

describe('Crawl every page for defects and issues', async () => {
  before(async function() {
    browser = await puppeteer.launch({
      headless: JSON.parse(true),
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

    await page.on('request', (request) => {
      request.continue();
    });
    await page.on('response', (response) => {
      const request = response.request();
      const url = request.url();
      const status = response.status().toString();

      if (status.startsWith('4') || status.startsWith('5')) {
        console.warn(` !! Failed response : ${url} (${status})`);
        outputEntry.failed.push({
          url : url,
          status: status
        });
      } else {
        if (JSON.parse(LOG_PASSED) === true) {
          outputEntry.passed.push({
            url : url,
            status: status
          });
        }

      }
    });

  });

  after(async () => {
    await browser.close();
    output.endDate = new Date().toISOString();
    fs.writeFile(`${reportPath}/${filename}.json`, JSON.stringify(output), (err) => {
      if (err) {
        return console.error(err);
      }
      return console.log(`File ${reportPath}/${filename}.json saved!`);
    });
  });

  urlsList.forEach(function(section) {
    describe(section.name + ' - ' + section.description, async function() {
      //crawl every page
      pagesToCrawl = section.urls;
      let count = 1;
      pagesToCrawl.forEach(function (pageToCrawl) {
        pageToCrawl.urlPrefix = section.urlPrefix.replace('URL_BO', URL_BO).replace('URL_FO', URL_FO);
        pageToCrawl.sectionName = section.name;
        pageToCrawl.sectionDescription = section.description;
        it(`Crawling ${pageToCrawl.name} (${count}/${pagesToCrawl.length})`, async function () {
          outputEntry = {
            name : pageToCrawl.name,
            url : pageToCrawl.url,
            passed : [],
            failed: []
          };
          await Promise.all([
            page.goto(`${pageToCrawl.urlPrefix}${pageToCrawl.url}`),
            page.waitForNavigation({waitUntil: 'networkidle0'})
          ]);

          if (typeof(pageToCrawl.customAction) !== 'undefined') {
            await pageToCrawl.customAction({page, loginInfos});
          }
          output.pages.push(outputEntry);
        });
        count += 1;
      });
    });
  });
});
