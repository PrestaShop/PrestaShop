// Import utils
import testContext from '@utils/testContext';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import {
  boDashboardPage,
  boLoginPage,
  dataEmployees,
  FakerEmployee,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_login_login';

describe('BO - Login : Login in BO', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const employeeData: FakerEmployee = new FakerEmployee({password: '123456789'});

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  it('should open the BO authentication page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'openAuthenticationPage', baseContext);

    await boLoginPage.goTo(page, global.BO.URL);

    const pageTitle = await boLoginPage.getPageTitle(page);
    expect(pageTitle).to.contains(boLoginPage.pageTitle);
  });

  it('should enter an invalid credentials', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'enterInvalidCredentials', baseContext);

    await boLoginPage.failedLogin(page, employeeData.email, employeeData.password);

    const loginError = await boLoginPage.getLoginError(page);
    expect(loginError).to.contains(boLoginPage.loginErrorText);
  });

  it('should enter an invalid email', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'enterInvalidEmail', baseContext);

    await boLoginPage.failedLogin(page, employeeData.email, dataEmployees.defaultEmployee.password);

    const loginError = await boLoginPage.getLoginError(page);
    expect(loginError).to.contains(boLoginPage.loginErrorText);
  });

  it('should enter an invalid password', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'enterInvalidPassword', baseContext);

    await boLoginPage.failedLogin(page, dataEmployees.defaultEmployee.email, employeeData.password);

    const loginError = await boLoginPage.getLoginError(page);
    expect(loginError).to.contains(boLoginPage.loginErrorText);
  });

  it('should enter a valid credentials', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'enterValidCredentials', baseContext);

    await boLoginPage.successLogin(page, dataEmployees.defaultEmployee.email, dataEmployees.defaultEmployee.password);

    const pageTitle = await boDashboardPage.getPageTitle(page);
    expect(pageTitle).to.contains(boDashboardPage.pageTitle);
  });
});
