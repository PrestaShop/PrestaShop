require('module-alias/register');

const { expect } = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import pages
// BO pages
const dashboardPage = require('@pages/BO/dashboard');
const moduleManagerPage = require('@pages/BO/modules/moduleManager');
const moduleConfigurationPage = require('@pages/BO/modules/moduleConfiguration');
const psEmailSubscriptionPage = require('@pages/BO/modules/psEmailSubscription');
// FO pages
const foHomePage = require('@pages/FO/home');
const foLoginPage = require('@pages/FO/login');
const foMyAccountPage = require('@pages/FO/myAccount')
const foAccountIdentityPage = require('@pages/FO/myAccount/identity')

// Import datas
const { DefaultCustomer } = require('@data/demo/customer');


// Import test context
const testContext = require('@utils/testContext');

// context
const baseContext = 'functional_FO_newsletter_subscribeNewsletter';

const unusedEmailNewsletterSubscriptionAlertText = 'You have successfully subscribed to this newsletter.';
const alreadyUsedEmailNewsletterSubscriptionAlertText = 'This email address is already registered.';
const unsubscribeNewsletterText = 'Information successfully updated.';
const moduleName = 'Newsletter subscription';


/*
Go to the FO homepage
Fill the subscribe newsletter field and subscribe
Go to BO in newsletter module
Check if correctly subscribed
Go back to the FO homepage
Try to subscribe again with the same email
Go to back to BO and delete subsbcription
 */
describe('FO Subscribe to Newsletter', async () => {
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
        await testContext.addContextItem(this, 'testIdentifier', '', baseContext);

        await foHomePage.goTo(page, global.FO.URL);
        const result = await foHomePage.isHomePage(page);
        await expect(result).to.be.true;
      });

      it('should subscribe to newsletter with already used email', async function () {
        await testContext.addContextItem(this, 'testIdentifier', '', baseContext);

        const newsletterSubscribeAlertMessage = await foHomePage.subscribeToNewsletter(page, DefaultCustomer.email);
        await expect(newsletterSubscribeAlertMessage).to.contains(alreadyUsedEmailNewsletterSubscriptionAlertText);
      });
    });

  describe('Go to FO customer account to unsubscribe newsletter', async () => {
    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginFoPage', baseContext);

      await foHomePage.goToLoginPage(page);

      const pageHeaderTitle = await foLoginPage.getPageTitle(page);
      await expect(pageHeaderTitle).to.equal(foLoginPage.pageTitle);
    });

    it('Should sign in FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signInFo', baseContext);

      await foLoginPage.customerLogin(page, DefaultCustomer);
      const isCustomerConnected = await foMyAccountPage.isCustomerConnected(page);
      await expect(isCustomerConnected, 'Customer is not connected').to.be.true;
    });

    it('should go account information page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAccountInformationPage', baseContext);

      await foMyAccountPage.goToInformationPage(page);

      const pageTitle = await foAccountIdentityPage.getPageTitle(page);
      await expect(pageTitle).to.equal(foAccountIdentityPage.pageTitle);
    });

    it('should unsubscribe from newsletter', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'unsubscribeFromNewsLetter', baseContext);

      const unsubscribeAlertText = await foAccountIdentityPage.unsubscribeNewsletter(page, DefaultCustomer.password);
      await expect(unsubscribeAlertText).to.contains(unsubscribeNewsletterText)
    });
  });

  describe('Go to BO to check if correctly unsubscribed', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to module manager page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToModuleManagerPageToCheckIfUnsubscribed', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.modulesParentLink,
        dashboardPage.moduleManagerLink,
      );

      await moduleManagerPage.closeSfToolBar(page);

      const pageTitle = await moduleManagerPage.getPageTitle(page);
      await expect(pageTitle).to.contains(moduleManagerPage.pageTitle);
    });

    it('should go to newsletter subscription module configuration page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewsletterSubscriptionModuleConfigurationPageToCheckIfUnsubscribed', baseContext);

      await moduleManagerPage.goToConfigurationPage(page, moduleName);

      const moduleConfigurationPageSubtitle = await moduleConfigurationPage.getPageSubtitle(page);
      await expect(moduleConfigurationPageSubtitle).to.contains(moduleName);
    });

    it('should check if user is unsubscribed from newsletter', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkThatEmailIsNotInTable', baseContext);

      const subscribedUserList = await psEmailSubscriptionPage.getListOfNewsletterRegistrationEmails(page);
      await expect(subscribedUserList).to.not.contains(DefaultCustomer.email);
    });

    it('should logout from BO', async function () {
      await loginCommon.logoutBO(this, page);
    });
  });

  describe('Go to FO to subscribe to the newsletter', async () => {
    it('should open the shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShopFOToSubscribeToNewsletter', baseContext);

      await foHomePage.goTo(page, global.FO.URL);
      const result = await foHomePage.isHomePage(page);
      await expect(result).to.be.true;
    });

    it('should subscribe to newsletter', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'NewsletterSubscription', baseContext);

      const newsletterSubscribeAlertMessage = await foHomePage.subscribeToNewsletter(page, DefaultCustomer.email);
      await expect(newsletterSubscribeAlertMessage).to.contains(unusedEmailNewsletterSubscriptionAlertText);});
  });

  describe('Go to BO to check if correctly subscribed', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to module manager page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToModuleManagerPageToCheckIfSubscribed', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.modulesParentLink,
        dashboardPage.moduleManagerLink,
      );

      await moduleManagerPage.closeSfToolBar(page);

      const pageTitle = await moduleManagerPage.getPageTitle(page);
      await expect(pageTitle).to.contains(moduleManagerPage.pageTitle);
    });

    it('should go to newsletter subscription module configuration page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewsletterSubscriptionModuleConfigurationPageToCheckIfSubscribed', baseContext);

      await moduleManagerPage.goToConfigurationPage(page, moduleName);

      const moduleConfigurationPageSubtitle = await moduleConfigurationPage.getPageSubtitle(page);
      await expect(moduleConfigurationPageSubtitle).to.contains(moduleName);
    });

    it('should check if previous customer subscription is visible in table', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkIfSubscriptionIsInTable', baseContext);

      const subscribedUserList = await psEmailSubscriptionPage.getListOfNewsletterRegistrationEmails(page);
      await expect(subscribedUserList).to.contains(DefaultCustomer.email)
    });

    it('should logout from BO', async function () {
      await loginCommon.logoutBO(this, page);
    });
  });
});
