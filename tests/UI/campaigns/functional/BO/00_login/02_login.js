// Import utils
import helper from '@utils/helpers';

// Import test context
import testContext from '@utils/testContext';

require('module-alias/register');

// Import expect from chai
const {expect} = require('chai');

// Import pages
const loginPage = require('@pages/BO/login/index');
const dashboardPage = require('@pages/BO/dashboard');

// Import data
const {DefaultEmployee} = require('@data/demo/employees');
const EmployeeFaker = require('@data/faker/employee');

const baseContext = 'functional_BO_login_login';

let browserContext;
let page;

const employeeData = new EmployeeFaker({password: '123456789'});

describe('BO - Login : Login in BO', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  it('should open the BO authentication page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'openAuthenticationPage', baseContext);

    await loginPage.goTo(page, global.BO.URL);

    const pageTitle = await loginPage.getPageTitle(page);
    await expect(pageTitle).to.contains(loginPage.pageTitle);
  });

  it('should enter an invalid credentials', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'enterInvalidCredentials', baseContext);

    await loginPage.failedLogin(page, employeeData.email, employeeData.password);

    const loginError = await loginPage.getLoginError(page);
    await expect(loginError).to.contains(loginPage.loginErrorText);
  });

  it('should enter an invalid email', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'enterInvalidEmail', baseContext);

    await loginPage.failedLogin(page, employeeData.email, DefaultEmployee.password);

    const loginError = await loginPage.getLoginError(page);
    await expect(loginError).to.contains(loginPage.loginErrorText);
  });

  it('should enter an invalid password', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'enterInvalidPassword', baseContext);

    await loginPage.failedLogin(page, DefaultEmployee.email, employeeData.password);

    const loginError = await loginPage.getLoginError(page);
    await expect(loginError).to.contains(loginPage.loginErrorText);
  });

  it('should enter a valid credentials', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'enterValidCredentials', baseContext);

    await loginPage.successLogin(page, DefaultEmployee.email, DefaultEmployee.password);

    const pageTitle = await dashboardPage.getPageTitle(page);
    await expect(pageTitle).to.contains(dashboardPage.pageTitle);
  });
});
