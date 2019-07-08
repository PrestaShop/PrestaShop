require('events').EventEmitter.defaultMaxListeners = Infinity;
const puppeteer = require('puppeteer');
const fs = require('fs');

const reportPath = './reports/';
// maybe add tracing option : https://github.com/GoogleChrome/puppeteer/blob/master/docs/api.md#class-tracing
const URL_FO = process.env.URL_FO || 'http://localhost:8080/';
const URL_BO = process.env.URL_BO || 'http://localhost:8080/admin-dev/';
const EMAIL = process.env.LOGIN || 'demo@prestashop.com';
const PASSWD = process.env.PASSWD || 'prestashop_demo';
const HEADLESS = process.env.HEADLESS || true;
let responses = [];
const numberRequests = {BO: 0, FO: 0};
const selectorBO = 'nav.nav-bar.d-none.d-md-block ul li a.link[href]';
const selectorFO = 'a[href]';
let office = 'BO';
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

const getAllUrls = (page, selector) => page.$$eval(selector, as => as.map(a => a.href));

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
      console.log(` * Failed response in ${office} from ${curHref}: ${url} (${status})`);
      object[office].failed.push({page: curHref, URL: url, status});
    } else {
      responses.push({url, status});
    }
  });
};

const checkStatusUrls = async (page, hrefs) => {
  // check urls
  for (const href of hrefs) {
    curHref = href;
    object[office].passed.push({url: href, date: new Date().getTime(), responses});

    if (!href.includes('mailto:') && href.includes(URL_FO)) {
      await page.goto(href, {waitUntil: 'domcontentloaded'});
    }
    responses = [];
  }

  object[office].totalRequests = numberRequests[office];
};

const loginBO = async (page) => {
  await page.type('#email', EMAIL);
  await page.type('#passwd', PASSWD);
  await page.click('#submit_login');
  await page.waitForNavigation({waitUntil: 'domcontentloaded'});
};

const run = async () => {
  const browser = await puppeteer.launch({
    headless: HEADLESS,
    args: ['--no-sandbox'],
  });
  const page = await browser.newPage();
  await interceptRequestAndResponse(page, office);
  console.time('Check urls ');

  // Start testing BO
  console.log('Begin testing BO');
  await page.goto(URL_BO, {waitUntil: 'networkidle0'});
  await loginBO(page);
  let urlList = await getAllUrls(page, selectorBO);
  console.log(` - ${urlList.length} URL to crawl`);
  office = 'BO';
  object.BO.totalLinks = urlList.length;
  await checkStatusUrls(page, urlList);
  console.log('End testing BO');
  // End testing BO

  // Start testing FO
  console.log('Begin testing FO');
  await page.goto(URL_FO, {waitUntil: 'networkidle0'});
  urlList = await getAllUrls(page, selectorFO);
  console.log(` - ${urlList.length} URL to crawl`);
  office = 'FO';
  object.FO.totalLinks = urlList.length;
  await checkStatusUrls(page, urlList);
  console.log('End testing FO');
  // End testing FO

  browser.close();
  console.timeEnd('Check urls ');

  object.stats.end = new Date().getTime();

  const filename = `report_${new Date().toJSON().slice(0, 10)}.json`;
  fs.writeFile(reportPath + filename, JSON.stringify(object), (err) => {
    if (err) {
      return console.log(err);
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

run()
  .then(() => {
    console.log('--------the end--------');
  }).catch(e => console.log(`error: ${e}`));
