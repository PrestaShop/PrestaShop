require('events').EventEmitter.defaultMaxListeners = Infinity;
const puppeteer = require('puppeteer');
const fs = require('fs');
require('./utils/globals');

const reportPath = './reports/';
// maybe add tracing option : https://github.com/GoogleChrome/puppeteer/blob/master/docs/api.md#class-tracing
const LOG_PASSED = process.env.LOG_PASSED || false;

let responses = [];
const numberRequests = {BO: 0, FO: 0};
const selectorBO = 'nav.nav-bar.d-none.d-md-block ul li a.link[href]';
const selectorFO = 'a[href]';
let office = null;
let curHref = null;
const object = {
  stats: {
    start: new Date().getTime(),
    end: null,
  },
  BO: {
    totalLinks: 0,
    totalRequests: 0,
    passed: [],
    failed: [],
  },
  FO: {
    totalLinks: 0,
    totalRequests: 0,
    passed: [],
    failed: [],
  },
};

const getAllUrls = async (page, selector) => {
  const listUrls = await page.$$eval(selector, as => as.map(a => a.href));
  // remove perfect duplicates
  await listUrls.filter((elem, pos) => listUrls.indexOf(elem) === pos);
  return listUrls;
};

const interceptRequestAndResponse = async (page) => {
  await page.setRequestInterception(true);
  await page.on('request', (request) => {
    request.continue();
  });
  await page.on('response', (response) => {
    const request = response.request();
    const url = request.url();
    const status = response.status().toString();
    numberRequests[office] += 1;
    if (status.startsWith('4') || status.startsWith('5')) {
      console.warn(` !! Failed response : ${url} (${status})`);
      object[office].failed.push({page: curHref, URL: url, status});
    } else {
      responses.push({url, status});
    }
  });
};

const checkStatusUrls = async (page, hrefs) => {
  // check urls
  let i = 1;
  // eslint-disable-next-line
  for (const href of hrefs) {
    curHref = href;
    if (LOG_PASSED === true) {
      object[office].passed.push({url: href, date: new Date().getTime(), responses});
    }

    if (href.includes(global.URL_FO)) {
      // eslint-disable-next-line
      await page.goto(href, {waitUntil: 'domcontentloaded'});
      outputSameLine(` - ${i}/${hrefs.length} checked (${href})`);
    }

    responses = [];
    i += 1;
  }

  object[office].totalRequests = numberRequests[office];
};

const loginBO = async (page) => {
  await page.type('#email', global.EMAIL);
  await page.type('#passwd', global.PASSWD);
  await page.click('#submit_login');
  await page.waitForNavigation({waitUntil: 'domcontentloaded'});
};

const run = async () => {
  const browser = await puppeteer.launch({
    headless: global.HEADLESS,
    args: ['--no-sandbox'],
  });
  const page = await browser.newPage();
  await interceptRequestAndResponse(page, office);
  console.time('Check urls ');

  // Start testing BO
  console.log('Begin testing BO');
  await page.goto(global.URL_BO, {waitUntil: 'networkidle0'});
  await loginBO(page);
  let urlList = await getAllUrls(page, selectorBO);
  await console.log(` - ${urlList.length} URL to crawl`);
  office = 'BO';
  object.BO.totalLinks = urlList.length;
  await checkStatusUrls(page, urlList);
  console.log('\nEnd testing BO\n');
  // End testing BO

  // Start testing FO
  console.log('Begin testing FO');
  await page.goto(global.URL_FO, {waitUntil: 'networkidle0'});
  urlList = await getAllUrls(page, selectorFO);
  console.log(` - ${urlList.length} URL to crawl`);
  office = 'FO';
  object.FO.totalLinks = urlList.length;
  await checkStatusUrls(page, urlList);
  console.log('\nEnd testing FO');
  // End testing FO

  browser.close();
  console.timeEnd('Check urls ');

  object.stats.end = new Date().getTime();

  const curDate = new Date();

  const filename = `report_${curDate.toJSON().slice(0, 10)}_${curDate.getHours()}${curDate.getMinutes()}${curDate.getSeconds()}.json`;
  // Create folder reports if not exist
  if (!fs.existsSync(reportPath)) {
    fs.mkdirSync(reportPath);
  }
  // Create report file
  fs.writeFile(reportPath + filename, JSON.stringify(object), (err) => {
    if (err) {
      return console.error(err);
    }
    return console.log(`File ${reportPath}${filename} saved!`);
  });

  // is there any error when crawling ?
  if (object.BO.failed.length > 0 || object.FO.failed.length > 0) {
    let errorMessage = '';
    if (object.BO.failed.length > 0) {
      errorMessage += ` - ${object.BO.failed.length} errors in BO`;
    }
    if (object.FO.failed.length > 0) {
      errorMessage += ` - ${object.FO.failed.length} errors in FO`;
    }
    throw new Error(`Errors caught : ${errorMessage}`);
  }
};

const outputSameLine = async (message) => {
  process.stdout.clearLine();
  process.stdout.cursorTo(0);
  process.stdout.write(message);
};

run()
  .then(() => {
    console.log('--------the end--------');
  }).catch(e => console.error(`${e}`));
