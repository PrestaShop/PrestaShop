// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import {moduleManager as moduleManagerPage} from '@pages/BO/modules/moduleManager';
import {moduleConfigurationPage} from '@pages/BO/modules/moduleConfiguration';
import psEmailSubscriptionPage from '@pages/BO/modules/psEmailSubscription';
// Import FO pages
import {homePage as foHomePage} from '@pages/FO/classic/home';
import {loginPage as foLoginPage} from '@pages/FO/classic/login';
import {myAccountPage} from '@pages/FO/classic/myAccount';
import {accountIdentityPage} from '@pages/FO/classic/myAccount/identity';

import {
  // Import data
  dataCustomers,
  FakerModule,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

// context
const baseContext: string = 'functional_FO_classic_newsletter_subscribeNewsletter';

/*
Go to the FO homepage
Fill the subscribe newsletter field and subscribe
Go to BO in newsletter module
Check if correctly subscribed
Go back to the FO homepage
Try to subscribe again with the same email
Go to back to BO and delete subscription
 */
describe('FO - Newsletter : Subscribe to Newsletter', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const moduleInformation: FakerModule = new FakerModule({
    tag: 'ps_emailsubscription',
    name: 'Newsletter subscription',
  });

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('Go to FO and try to subscribe with already used email', async () => {
    it('should open the shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'openFoShop', baseContext);

      await foHomePage.goTo(page, global.FO.URL);

      const result = await foHomePage.isHomePage(page);
      expect(result).to.eq(true);
    });

    it('should subscribe to newsletter with already used email', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'subscribeWithAlreadyUsedEmail', baseContext);

      const newsletterSubscribeAlertMessage = await foHomePage.subscribeToNewsletter(page, dataCustomers.johnDoe.email);
      expect(newsletterSubscribeAlertMessage).to.contains(foHomePage.alreadyUsedEmailMessage);
    });
  });

  describe('Go to FO customer account to unsubscribe newsletter', async () => {
    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFOLoginPage', baseContext);

      await foHomePage.goToLoginPage(page);

      const pageHeaderTitle = await foLoginPage.getPageTitle(page);
      expect(pageHeaderTitle).to.equal(foLoginPage.pageTitle);
    });

    it('Should sign in FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signInFo', baseContext);

      await foLoginPage.customerLogin(page, dataCustomers.johnDoe);

      const isCustomerConnected = await myAccountPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is not connected').to.eq(true);
    });

    it('should go account information page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAccountInformationPage', baseContext);

      await foHomePage.goToMyAccountPage(page);
      await myAccountPage.goToInformationPage(page);

      const pageTitle = await accountIdentityPage.getPageTitle(page);
      expect(pageTitle).to.equal(accountIdentityPage.pageTitle);
    });

    it('should unsubscribe from newsletter', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'unsubscribeFromNewsLetter', baseContext);

      const unsubscribeAlertText = await accountIdentityPage.unsubscribeNewsletter(page, dataCustomers.johnDoe.password);
      expect(unsubscribeAlertText).to.contains(accountIdentityPage.successfulUpdateMessage);
    });
  });

  describe('Go to BO to check if correctly unsubscribed', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to module manager page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToModuleManagerPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.modulesParentLink,
        dashboardPage.moduleManagerLink,
      );
      await moduleManagerPage.closeSfToolBar(page);

      const pageTitle = await moduleManagerPage.getPageTitle(page);
      expect(pageTitle).to.contains(moduleManagerPage.pageTitle);
    });

    it(`should search for module ${moduleInformation.name}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchForModule', baseContext);

      const isModuleVisible = await moduleManagerPage.searchModule(page, moduleInformation);
      expect(isModuleVisible).to.eq(true);
    });

    it('should go to newsletter subscription module configuration page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewsletterModuleConfigPage', baseContext);

      await moduleManagerPage.searchModule(page, moduleInformation);
      await moduleManagerPage.goToConfigurationPage(page, moduleInformation.tag);

      const moduleConfigurationPageSubtitle = await moduleConfigurationPage.getPageSubtitle(page);
      expect(moduleConfigurationPageSubtitle).to.contains(moduleInformation.name);
    });

    it('should check if user is unsubscribed from newsletter', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkThatEmailIsNotInTable', baseContext);

      const subscribedUserList = await psEmailSubscriptionPage.getListOfNewsletterRegistrationEmails(page);
      expect(subscribedUserList).to.not.contains(dataCustomers.johnDoe.email);
    });

    it('should logout from BO', async function () {
      await loginCommon.logoutBO(this, page);
    });
  });

  describe('Go to FO to subscribe to the newsletter', async () => {
    it('should open the shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFOToSubscribeToNewsletter', baseContext);

      await foHomePage.goTo(page, global.FO.URL);

      const result = await foHomePage.isHomePage(page);
      expect(result).to.eq(true);
    });

    it('should subscribe to newsletter', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'subscribeToNewsletter', baseContext);

      const newsletterSubscribeAlertMessage = await foHomePage.subscribeToNewsletter(page, dataCustomers.johnDoe.email);
      expect(newsletterSubscribeAlertMessage).to.contains(foHomePage.successSubscriptionMessage);
    });
  });

  describe('Go to BO to check if correctly subscribed', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to module manager page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToBOToCheckIfSubscribed', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.modulesParentLink,
        dashboardPage.moduleManagerLink,
      );
      await moduleManagerPage.closeSfToolBar(page);

      const pageTitle = await moduleManagerPage.getPageTitle(page);
      expect(pageTitle).to.contains(moduleManagerPage.pageTitle);
    });

    it('should go to newsletter subscription module configuration page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToNewsletterModuleConfig', baseContext);

      await moduleManagerPage.searchModule(page, moduleInformation);
      await moduleManagerPage.goToConfigurationPage(page, moduleInformation.tag);

      const moduleConfigurationPageSubtitle = await moduleConfigurationPage.getPageSubtitle(page);
      expect(moduleConfigurationPageSubtitle).to.contains(moduleInformation.name);
    });

    it('should check if previous customer subscription is visible in table', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkIfSubscriptionIsInTable', baseContext);

      const subscribedUserList = await psEmailSubscriptionPage.getListOfNewsletterRegistrationEmails(page);
      expect(subscribedUserList).to.contains(dataCustomers.johnDoe.email);
    });

    it('should logout from BO', async function () {
      await loginCommon.logoutBO(this, page);
    });
  });
});
