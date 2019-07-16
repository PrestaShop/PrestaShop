//importing pages
const BO_login = require('../../pages/BO/BO_login');

let page;
let BO_LOGIN;
//creating pages objects in a function
const beforeFunc = async () => {
  page = await global.browser.newPage();
  BO_LOGIN = await (new BO_login(page));
};


//test scenario
scenario('should go to the BO', async () => {
  test('should open the BO login page', async () => {
    await BO_LOGIN.goTo(global.URL_BO);
  });
  test('should enter credentials and submit', async () => {
    await BO_LOGIN.login(global.EMAIL, global.PASSWD);
  });
  test('should be on the dashboard', async() => {
    let pageTitle = await BO_LOGIN.getPageTitle();
    await expect(pageTitle).to.equal(BO_LOGIN.pageTitle);

  });
}, beforeFunc, true);
