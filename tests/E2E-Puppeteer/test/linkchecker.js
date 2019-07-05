'use strict';

require('events').EventEmitter.prototype._maxListeners = Infinity;
const puppeteer = require('puppeteer');
const fs = require('fs');
const report_path = './reports/';
//maybe add tracing option : https://github.com/GoogleChrome/puppeteer/blob/master/docs/api.md#class-tracing
let URL_FO = process.env.URL_FO || 'http://localhost:8080/';
let URL_BO = process.env.URL_BO || 'http://localhost:8080/admin-dev/';
let EMAIL = process.env.LOGIN || 'demo@prestashop.com';
let PASSWD = process.env.PASSWD || 'prestashop_demo';
let HEADLESS = process.env.HEADLESS || true;
let responses = [];
let number_requests = {BO: 0, FO: 0};
let selector_BO = 'nav.nav-bar.d-none.d-md-block ul li a.link[href]';
let selector_FO = 'a[href]';
let office = 'BO';
let cur_href = null;
let object = {
  stats: {
    start: new Date().getTime(),
    end: null,
  },
  BO: {
    totalLinks: 0,
    totalRequests: 0,
    passed: [],
    failed: []
  },
  FO: {
    totalLinks: 0,
    totalRequests: 0,
    passed: [],
    failed: []
  },
};

const getAllUrls = async (page, selector) => {
  return await page.$$eval(selector, as => as.map(a => a.href))
};

const interceptRequestAndResponse = async (page) => {
  await page.setRequestInterception(true);
  await page.on('request', request => {
    request.continue();
  });
  await page.on('response', response => {
    const request = response.request();
    const url = request.url();
    const status = response.status().toString();
    number_requests[office] += 1;
    if (status.startsWith('4') || status.startsWith('5')) {
      console.log(' * Failed response in ' + office + ' from ' + cur_href + ': ' + url + ' (' + status + ')');
      object[office].failed.push({page: cur_href, URL: url, status: status});
    } else {
      responses.push({url: url, status: status});
    }
  });
};

const checkStatusUrls = async (page, hrefs) => {
  //check urls
  for (const href of hrefs) {
    cur_href = href;
    object[office].passed.push({url: href, date: new Date().getTime(), responses: responses});

    if (!href.includes('mailto:') && href.includes(URL_FO)) {
      await page.goto(href, {waitUntil: 'domcontentloaded'});
    }
    responses = [];
  }
  object[office].totalRequests = number_requests[office];
};

const login_BO = async (page) => {
  await page.type('#email', EMAIL);
  await page.type('#passwd', PASSWD);
  await page.click('#submit_login');
  await page.waitForNavigation({waitUntil: 'domcontentloaded'});
};

const run = async () => {
  let browser = await puppeteer.launch({
    headless: HEADLESS,
    args: ['--no-sandbox']
  });
  let page = await browser.newPage();
  await interceptRequestAndResponse(page, office);
  console.time('Check urls ');

  //Start testing BO
  console.log('Begin testing BO');
  await page.goto(URL_BO, {waitUntil: 'networkidle0'});
  await login_BO(page);
  let urlList = await getAllUrls(page, selector_BO);
  console.log(' - ' + urlList.length + ' URL to crawl');
  office = 'BO';
  object.BO.totalLinks = urlList.length;
  await checkStatusUrls(page, urlList);
  console.log('End testing BO');
  // End testing BO

  // Start testing FO
  console.log('Begin testing FO');
  await page.goto(URL_FO, {waitUntil: 'networkidle0'});
  urlList = await getAllUrls(page, selector_FO);
  console.log(' - ' + urlList.length + ' URL to crawl');
  office = 'FO';
  object.FO.totalLinks = urlList.length;
  await checkStatusUrls(page, urlList);
  console.log('End testing FO');
  // End testing FO

  browser.close();
  console.timeEnd('Check urls ');

  object.stats.end = new Date().getTime();

  const filename = 'report_' + new Date().toJSON().slice(0, 10) + '.json';
  fs.writeFile(report_path + filename, JSON.stringify(object), function (err) {
    if (err) {
      return console.log(err);
    }
    console.log("File " + report_path + filename + " saved!");
  });

  //is there any error when crawling ?
  if (object.BO.failed.length > 0 || object.FO.failed.length > 0) {
    let error_message = '';
    if (object.BO.failed.length > 0) {
      error_message += ' - ' + object.BO.failed.length + " errors in BO";
    }
    if (object.FO.failed.length > 0) {
      error_message += ' - ' + object.FO.failed.length + " errors in FO";
    }
    throw ('Errors caught : ' + error_message);
  }
};

run()
  .then(() => {
    console.log("--------the end--------")
  }).catch(e => console.log(`error: ${e}`));