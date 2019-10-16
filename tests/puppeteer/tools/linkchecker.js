require('events').EventEmitter.defaultMaxListeners = Infinity;
const puppeteer = require('puppeteer');
const fs = require('fs');
require('./globals');

const reportPath = './reports/';
const urlsList = require('./urls.js');

let output = {
  date : new Date().toISOString().slice(0, 10),
  startDate : new Date().toISOString(),
  endDate : null,
  urls : []
};
let outputEntry = {
  name : '',
  url : '',
  passed : [],
  failed: []
};

const run = async () => {
  console.log(global.BROWSER_CONFIG);
  const browser = await puppeteer.launch(global.BROWSER_CONFIG);
  const page = await browser.newPage();
  //await page.setRequestInterception(true);
  /*page.on('request', (request) => {
    request.continue();
  });
  page.on('response', async (response) => {
    const request = response.request();
    const url = request.url();
    const status = response.status().toString();

    if (status.startsWith('4') || status.startsWith('5')) {
      await outputEntry.failed.push({
        url : url,
        status : status
      });
    } else {
      await outputEntry.passed.push({
        url : url,
        status : status
      });
    }
  });*/

  // Start testing BO
  console.log('Begin testing');

  let count = 1;
  (async function() {
    urlsList.forEach(function(section) {
      (async function() {
        console.log(' - Section '+section.description);
        //crawl every page
        let pagesToCrawl = section.urls;
        pagesToCrawl.forEach(function(pageToCrawl) {
          pageToCrawl.urlPrefix = section.urlPrefix.replace('URL_BO', global.INFORMATIONS.URL_BO).replace('URL_FO', global.INFORMATIONS.URL_FO);
          pageToCrawl.sectionName = section.name;
          pageToCrawl.sectionDescription = section.description;
          console.log(`Crawling ${pageToCrawl.name} (${count}/${pagesToCrawl.length})`);

          outputEntry = {
            name : pageToCrawl.name,
            url : `${pageToCrawl.urlPrefix}${pageToCrawl.url}`,
            passed : [],
            failed: []
          };
          (async function () {
            await Promise.all([
              page.goto(`${pageToCrawl.urlPrefix}${pageToCrawl.url}`),
              page.waitForNavigation({waitUntil: 'networkidle0'})
            ]);
            if (typeof(pageToCrawl.customAction) !== 'undefined') {
              await pageToCrawl.customAction({page, infos: global.INFORMATIONS});
            }
            await output.urls.push(outputEntry);
          })();
          count += 1;
        });
      })();
    });
  })();


  output.endDate = new Date().toISOString();

  await createReportFile(output);
};

async function createReportFile() {
  const curDate = new Date();

  const dateString = curDate.toJSON().slice(0, 10);
  const hours = curDate.getHours();
  const minutes = curDate.getMinutes();
  const seconds = curDate.getSeconds();
  const filename = `report_${dateString}_${hours}${minutes}${seconds}.json`;
  // Create folder reports if not exist
  if (!fs.existsSync(reportPath)) {
    fs.mkdirSync(reportPath);
  }
  // Create report file
  fs.writeFile(reportPath + filename, JSON.stringify(output), (err) => {
    if (err) {
      return console.error(err);
    }
    return console.log(`File ${reportPath}${filename} saved!`);
  });
}

run()
  .then(async () => {
    console.log('--------the end--------');
  }).catch(e => console.error(`${e}`));
