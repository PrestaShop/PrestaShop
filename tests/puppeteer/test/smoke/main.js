const BO_login = require('../../pages/BO/BO_login');

const BO_LOGIN = new BO_login();

scenario('should go to the BO', async () => {
  test('should open the BO login page', async () => {
    await global.page.goto(global.URL_BO);
  });
  test('should enter credentials and submit', async () => {
    await BO_LOGIN.login(global.EMAIL, global.PASSWD);
  });
  test('should be on the dashboard', async() => {
    await BO_LOGIN.checkPageTitle();
  });
}, true);
