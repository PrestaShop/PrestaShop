// Import utils
import testContext from '@utils/testContext';

import {
  type BrowserContext,
  dataCustomers,
  FakerCustomer,
  foClassicHomePage,
  foClassicLoginPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';

const baseContext: string = 'functional_FO_classic_login_login';

describe('FO - Login : Login in FO', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const firstCredentialsData: FakerCustomer = new FakerCustomer();
  const secondCredentialsData: FakerCustomer = new FakerCustomer({password: dataCustomers.johnDoe.password});
  const thirdCredentialsData: FakerCustomer = new FakerCustomer({email: dataCustomers.johnDoe.email});

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  it('should open the shop page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToShopFO', baseContext);

    await foClassicHomePage.goTo(page, global.FO.URL);

    const result = await foClassicHomePage.isHomePage(page);
    expect(result).to.eq(true);
  });

  it('should go to login page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToLoginPage', baseContext);

    await foClassicHomePage.goToLoginPage(page);

    const pageTitle = await foClassicLoginPage.getPageTitle(page);
    expect(pageTitle).to.equal(foClassicLoginPage.pageTitle);
  });

  it('should enter an invalid credentials', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'enterInvalidCredentials', baseContext);

    await foClassicLoginPage.customerLogin(page, firstCredentialsData, false);

    const loginError = await foClassicLoginPage.getLoginError(page);
    expect(loginError).to.contains(foClassicLoginPage.loginErrorText);
  });

  it('should enter an invalid email', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'enterInvalidEmail', baseContext);

    await foClassicLoginPage.customerLogin(page, secondCredentialsData, false);

    const loginError = await foClassicLoginPage.getLoginError(page);
    expect(loginError).to.contains(foClassicLoginPage.loginErrorText);
  });

  it('should enter an invalid password', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'enterInvalidPassword', baseContext);

    await foClassicLoginPage.customerLogin(page, thirdCredentialsData, false);

    const loginError = await foClassicLoginPage.getLoginError(page);
    expect(loginError).to.contains(foClassicLoginPage.loginErrorText);
  });

  it('should check password type', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkPasswordType', baseContext);

    const inputType = await foClassicLoginPage.getPasswordType(page);
    expect(inputType).to.equal('password');
  });

  it('should click on show button and check the password', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'clickOnShowButton', baseContext);

    const inputType = await foClassicLoginPage.showPassword(page);
    expect(inputType).to.equal('text');
  });

  it('should enter a valid credentials', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'enterValidCredentials', baseContext);

    await foClassicLoginPage.customerLogin(page, dataCustomers.johnDoe);

    const isCustomerConnected = await foClassicLoginPage.isCustomerConnected(page);
    expect(isCustomerConnected, 'Customer is not connected!').to.eq(true);

    const result = await foClassicHomePage.isHomePage(page);
    expect(result).to.eq(true);
  });
});
