// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import pages
import loginPage from '@pages/BO/login';
import dashboardPage from '@pages/BO/dashboard';

// Import data
import Employees from '@data/demo/employees';
import EmployeeData from '@data/faker/employee';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_login_login';

describe('BO - Login : Login in BO', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const employeeData: EmployeeData = new EmployeeData({password: '123456789'});

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
    expect(pageTitle).to.contains(loginPage.pageTitle);
  });

  it('should enter an invalid credentials', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'enterInvalidCredentials', baseContext);

    await loginPage.failedLogin(page, employeeData.email, employeeData.password);

    const loginError = await loginPage.getLoginError(page);
    expect(loginError).to.contains(loginPage.loginErrorText);
  });

  it('should enter an invalid email', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'enterInvalidEmail', baseContext);

    await loginPage.failedLogin(page, employeeData.email, Employees.DefaultEmployee.password);

    const loginError = await loginPage.getLoginError(page);
    expect(loginError).to.contains(loginPage.loginErrorText);
  });

  it('should enter an invalid password', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'enterInvalidPassword', baseContext);

    await loginPage.failedLogin(page, Employees.DefaultEmployee.email, employeeData.password);

    const loginError = await loginPage.getLoginError(page);
    expect(loginError).to.contains(loginPage.loginErrorText);
  });

  it('should enter a valid credentials', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'enterValidCredentials', baseContext);

    await loginPage.successLogin(page, Employees.DefaultEmployee.email, Employees.DefaultEmployee.password);

    const pageTitle = await dashboardPage.getPageTitle(page);
    expect(pageTitle).to.contains(dashboardPage.pageTitle);
  });
});
