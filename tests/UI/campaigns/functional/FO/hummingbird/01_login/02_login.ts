// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import {enableTheme, disableTheme} from '@commonTests/BO/design/hummingbird';

import {
  type BrowserContext,
  dataCustomers,
  FakerCustomer,
  foHummingbirdHomePage,
  foHummingbirdLoginPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';

const baseContext: string = 'functional_FO_hummingbird_login_login';

describe('FO - Login : Login in FO', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const firstCredentialsData: FakerCustomer = new FakerCustomer();
  const secondCredentialsData: FakerCustomer = new FakerCustomer({password: dataCustomers.johnDoe.password});
  const thirdCredentialsData: FakerCustomer = new FakerCustomer({email: dataCustomers.johnDoe.email});

  // Pre-condition : Install Hummingbird
  enableTheme('hummingbird', `${baseContext}_preTest_1`);

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('Login in FO', () => {
    it('should open the shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShopFO', baseContext);

      await foHummingbirdHomePage.goTo(page, global.FO.URL);

      const result = await foHummingbirdHomePage.isHomePage(page);
      expect(result).to.eq(true);
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginPage', baseContext);

      await foHummingbirdHomePage.goToLoginPage(page);

      const pageTitle = await foHummingbirdLoginPage.getPageTitle(page);
      expect(pageTitle).to.equal(foHummingbirdLoginPage.pageTitle);
    });

    it('should enter an invalid credentials', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'enterInvalidCredentials', baseContext);

      await foHummingbirdLoginPage.customerLogin(page, firstCredentialsData, false);

      const loginError = await foHummingbirdLoginPage.getLoginError(page);
      expect(loginError).to.contains(foHummingbirdLoginPage.loginErrorText);
    });

    it('should enter an invalid email', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'enterInvalidEmail', baseContext);

      await foHummingbirdLoginPage.customerLogin(page, secondCredentialsData, false);

      const loginError = await foHummingbirdLoginPage.getLoginError(page);
      expect(loginError).to.contains(foHummingbirdLoginPage.loginErrorText);
    });

    it('should enter an invalid password', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'enterInvalidPassword', baseContext);

      await foHummingbirdLoginPage.customerLogin(page, thirdCredentialsData, false);

      const loginError = await foHummingbirdLoginPage.getLoginError(page);
      expect(loginError).to.contains(foHummingbirdLoginPage.loginErrorText);
    });

    it('should check password type', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPasswordType', baseContext);

      const inputType = await foHummingbirdLoginPage.getPasswordType(page);
      expect(inputType).to.equal('password');
    });

    it('should click on show button and check the password', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnShowButton', baseContext);

      const inputType = await foHummingbirdLoginPage.showPassword(page);
      expect(inputType).to.equal('text');
    });

    it('should enter a valid credentials', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'enterValidCredentials', baseContext);

      await foHummingbirdLoginPage.customerLogin(page, dataCustomers.johnDoe);

      const isCustomerConnected = await foHummingbirdLoginPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is not connected!').to.eq(true);

      const result = await foHummingbirdHomePage.isHomePage(page);
      expect(result).to.eq(true);
    });
  });

  // Post-condition : Uninstall Hummingbird
  disableTheme('hummingbird', `${baseContext}_postTest_2`);
});
