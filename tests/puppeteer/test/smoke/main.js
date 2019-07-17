// importing pages
const BO_LOGIN_PAGE = require('../../pages/BO/BO_login');

let page;
let BO_LOGIN;
// creating pages objects in a function
const init = async () => {
  page = await global.browser.newPage();
  BO_LOGIN = await (new BO_LOGIN_PAGE(page));
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
    const pageTitle = await BO_LOGIN.getPageTitle();
    await global.expect(pageTitle).to.equal(BO_LOGIN.pageTitle);
  });
}, init, true);
