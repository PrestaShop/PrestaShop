// Using chai
const expect = require('chai').expect;

// importing pages
const BO_LOGIN_PAGE = require('../../pages/BO/login');
const BO_DASHBOARD_PAGE = require('../../pages/BO/dashboard');

let page;
let BO_LOGIN;
let BO_DASHBOARD;

// creating pages objects in a function
const init = async () => {
  page = await global.browser.newPage();
  BO_LOGIN = await (new BO_LOGIN_PAGE(page));
  BO_DASHBOARD = await (new BO_DASHBOARD_PAGE(page));
};


// test scenario
global.scenario('should go to the BO', async () => {
  test('should open the BO login page', async () => {
    await BO_LOGIN.goTo(global.URL_BO);
  });
  test('should enter credentials and submit', async () => {
    await BO_LOGIN.login(global.EMAIL, global.PASSWD);
  });
  test('should be on the dashboard', async () => {
    const pageTitle = await BO_DASHBOARD.getPageTitle();
    await expect(pageTitle).to.equal(BO_DASHBOARD.pageTitle);
  });
}, init, true);
