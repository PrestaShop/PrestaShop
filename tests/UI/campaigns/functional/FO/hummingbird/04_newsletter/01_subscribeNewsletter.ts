import testContext from '@utils/testContext';
import {expect} from 'chai';

import {enableTheme, disableTheme} from '@commonTests/BO/design/hummingbird';

import {
  boDashboardPage,
  boLoginPage,
  boModuleConfigurationPage,
  boModuleManagerPage,
  type BrowserContext,
  dataCustomers,
  FakerModule,
  foHummingbirdHomePage,
  foHummingbirdLoginPage,
  foHummingbirdMyAccountPage,
  foHummingbirdMyInformationsPage,
  modPsEmailSubscriptionBoMain,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_FO_hummingbird_newsletter_subscribeNewsletter';

/*
Pre-condition:
- Install hummingbird
Scenario:
- Go to the FO homepage
- Fill the subscribe newsletter field and subscribe
- Go to BO in newsletter module
- Check if correctly subscribed
- Go back to the FO homepage
- Try to subscribe again with the same email
- Go to back to BO and delete subscription
Post-condition:
- Uninstall hummingbird
 */
describe('FO - Newsletter : Subscribe to Newsletter', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const moduleInformation: FakerModule = new FakerModule({
    tag: 'ps_emailsubscription',
    name: 'Newsletter subscription',
  });

  // Pre-condition : Install Hummingbird
  enableTheme('hummingbird', `${baseContext}_preTest`);

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('Go to FO and try to subscribe with already used email', async () => {
    it('should open the shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'openFoShop', baseContext);

      await foHummingbirdHomePage.goTo(page, global.FO.URL);

      const result = await foHummingbirdHomePage.isHomePage(page);
      expect(result).to.eq(true);
    });

    it('should subscribe to newsletter with already used email', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'subscribeWithAlreadyUsedEmail', baseContext);

      const newsletterSubscribeAlertMessage = await foHummingbirdHomePage.subscribeToNewsletter(
        page,
        dataCustomers.johnDoe.email,
      );
      expect(newsletterSubscribeAlertMessage).to.contains(foHummingbirdHomePage.alreadyUsedEmailMessage);
    });
  });

  describe('Go to FO customer account to unsubscribe newsletter', async () => {
    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFOLoginPage', baseContext);

      await foHummingbirdHomePage.goToLoginPage(page);

      const pageHeaderTitle = await foHummingbirdLoginPage.getPageTitle(page);
      expect(pageHeaderTitle).to.equal(foHummingbirdLoginPage.pageTitle);
    });

    it('Should sign in FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signInFo', baseContext);

      await foHummingbirdLoginPage.customerLogin(page, dataCustomers.johnDoe);

      const isCustomerConnected = await foHummingbirdMyAccountPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is not connected').to.eq(true);
    });

    it('should go to account information page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAccountInformationPage', baseContext);

      await foHummingbirdHomePage.goToMyAccountPage(page);
      await foHummingbirdMyAccountPage.goToInformationPage(page);

      const pageTitle = await foHummingbirdMyInformationsPage.getPageTitle(page);
      expect(pageTitle).to.equal(foHummingbirdMyInformationsPage.pageTitle);
    });

    it('should unsubscribe from newsletter', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'unsubscribeFromNewsLetter', baseContext);

      const unsubscribeAlertText = await foHummingbirdMyInformationsPage.unsubscribeNewsletter(
        page,
        dataCustomers.johnDoe.password,
      );
      expect(unsubscribeAlertText).to.contains(foHummingbirdMyInformationsPage.successfulUpdateMessage);
    });
  });

  describe('Go to BO to check if correctly unsubscribed', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to module manager page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToModuleManagerPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.modulesParentLink,
        boDashboardPage.moduleManagerLink,
      );
      await boModuleManagerPage.closeSfToolBar(page);

      const pageTitle = await boModuleManagerPage.getPageTitle(page);
      expect(pageTitle).to.contains(boModuleManagerPage.pageTitle);
    });

    it(`should search for module ${moduleInformation.name}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchForModule', baseContext);

      const isModuleVisible = await boModuleManagerPage.searchModule(page, moduleInformation);
      expect(isModuleVisible).to.eq(true);
    });

    it('should go to newsletter subscription module configuration page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewsletterModuleConfigPage', baseContext);

      await boModuleManagerPage.searchModule(page, moduleInformation);
      await boModuleManagerPage.goToConfigurationPage(page, moduleInformation.tag);

      const boModuleConfigurationPageSubtitle = await boModuleConfigurationPage.getPageSubtitle(page);
      expect(boModuleConfigurationPageSubtitle).to.contains(moduleInformation.name);
    });

    it('should check if user is unsubscribed from newsletter', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkThatEmailIsNotInTable', baseContext);

      const subscribedUserList = await modPsEmailSubscriptionBoMain.getListOfNewsletterRegistrationEmails(page);
      expect(subscribedUserList).to.not.contains(dataCustomers.johnDoe.email);
    });

    it('should logout from BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'logoutBO', baseContext);

      await boDashboardPage.logoutBO(page);

      const pageTitle = await boLoginPage.getPageTitle(page);
      expect(pageTitle).to.contains(boLoginPage.pageTitle);
    });
  });

  describe('Go to FO to subscribe to the newsletter', async () => {
    it('should open the shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFOToSubscribeToNewsletter', baseContext);

      await foHummingbirdHomePage.goTo(page, global.FO.URL);

      const result = await foHummingbirdHomePage.isHomePage(page);
      expect(result).to.eq(true);
    });

    it('should subscribe to newsletter', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'subscribeToNewsletter', baseContext);

      const newsletterSubscribeAlertMessage = await foHummingbirdHomePage.subscribeToNewsletter(
        page,
        dataCustomers.johnDoe.email,
      );
      expect(newsletterSubscribeAlertMessage).to.contains(foHummingbirdHomePage.successSubscriptionMessage);
    });
  });

  describe('Go to BO to check if correctly subscribed', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to module manager page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToBOToCheckIfSubscribed', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.modulesParentLink,
        boDashboardPage.moduleManagerLink,
      );
      await boModuleManagerPage.closeSfToolBar(page);

      const pageTitle = await boModuleManagerPage.getPageTitle(page);
      expect(pageTitle).to.contains(boModuleManagerPage.pageTitle);
    });

    it('should go to newsletter subscription module configuration page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToNewsletterModuleConfig', baseContext);

      await boModuleManagerPage.searchModule(page, moduleInformation);
      await boModuleManagerPage.goToConfigurationPage(page, moduleInformation.tag);

      const boModuleConfigurationPageSubtitle = await boModuleConfigurationPage.getPageSubtitle(page);
      expect(boModuleConfigurationPageSubtitle).to.contains(moduleInformation.name);
    });

    it('should check if previous customer subscription is visible in table', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkIfSubscriptionIsInTable', baseContext);

      const subscribedUserList = await modPsEmailSubscriptionBoMain.getListOfNewsletterRegistrationEmails(page);
      expect(subscribedUserList).to.contains(dataCustomers.johnDoe.email);
    });

    it('should logout from BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'logoutBO', baseContext);

      await boDashboardPage.logoutBO(page);

      const pageTitle = await boLoginPage.getPageTitle(page);
      expect(pageTitle).to.contains(boLoginPage.pageTitle);
    });
  });

  // Post-condition : Uninstall Hummingbird
  disableTheme('hummingbird', `${baseContext}_postTest`);
});
