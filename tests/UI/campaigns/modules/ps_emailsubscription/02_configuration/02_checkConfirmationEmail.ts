// Import utils
import testContext from '@utils/testContext';

// Import common tests
import loginCommon from '@commonTests/BO/loginBO';
import {setupSmtpConfigTest, resetSmtpConfigTest} from '@commonTests/BO/advancedParameters/smtp';

// Import pages
// Import BO pages
import psEmailSubscriptionPage from '@pages/BO/modules/psEmailSubscription';
import {moduleManager} from '@pages/BO/modules/moduleManager';
// Import FO pages
import {homePage as foHomePage} from '@pages/FO/classic/home';
import {emailSubscriptionPage} from '@pages/FO/classic/emailSubscription';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import {
  boDashboardPage,
  dataModules,
  type MailDev,
  type MailDevEmail,
  utilsMail,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'modules_ps_emailsubscription_configuration_checkConfirmationEmail';

/*
Pre-condition:
- Setup SMTP parameters
Scenario
- Enable send a confirmation email after registration
- Go to FO and subscribe to newsletter
- Check confirmation email
- Enable send a verification email
- Go to FO and subscribe to newsletter
- Check verification email and click on the link
- Check validation message and confirmation email
- Disable send a confirmation email after registration
- Go to FO and subscribe to newsletter
- Check no received email
Post-condition:
- Reset SMTP parameters
 */
describe('Mail alerts module - Enable/Disable send a confirmation email after subscription', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let allEmails: MailDevEmail[];
  let numberOfEmails: number;
  let mailListener: MailDev;

  // Pre-Condition : Setup config SMTP
  setupSmtpConfigTest(baseContext);

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);

    // Start listening to maildev server
    mailListener = utilsMail.createMailListener();
    utilsMail.startListener(mailListener);

    // get all emails
    // @ts-ignore
    mailListener.getAllEmail((err: Error, emails: MailDevEmail[]) => {
      allEmails = emails;
    });
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);

    // Stop listening to maildev server
    utilsMail.stopListener(mailListener);
  });

  describe(`BO: case 1 - Enable 'Send confirmation email' in the module '${dataModules.psEmailSubscription.name}'`, async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Modules > Module Manager\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToModuleManagerPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.modulesParentLink,
        boDashboardPage.moduleManagerLink,
      );
      await moduleManager.closeSfToolBar(page);

      const pageTitle = await moduleManager.getPageTitle(page);
      expect(pageTitle).to.contains(moduleManager.pageTitle);
    });

    it(`should search the module ${dataModules.psEmailSubscription.name}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchModule', baseContext);

      const isModuleVisible = await moduleManager.searchModule(page, dataModules.psEmailSubscription);
      expect(isModuleVisible).to.equal(true);
    });

    it(`should go to the configuration page of the module '${dataModules.psEmailSubscription.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToConfigurationPage', baseContext);

      await moduleManager.goToConfigurationPage(page, dataModules.psEmailSubscription.tag);

      const pageTitle = await psEmailSubscriptionPage.getPageSubtitle(page);
      expect(pageTitle).to.equal(psEmailSubscriptionPage.pageTitle);
    });

    it('should enable \'Send confirmation email\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'enableNewOrder', baseContext);

      const successMessage = await psEmailSubscriptionPage.setSendConfirmationEmail(page, true);
      expect(successMessage).to.contains(psEmailSubscriptionPage.updateSettingsSuccessMessage);
    });
  });

  describe('Go to FO to subscribe to the newsletter and check received email', async () => {
    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFOToSubscribeToNewsletter', baseContext);

      page = await psEmailSubscriptionPage.viewMyShop(page);

      const result = await foHomePage.isHomePage(page);
      expect(result).to.equal(true);
    });

    it('should subscribe to newsletter', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'subscribeToNewsletter', baseContext);

      const newsletterSubscribeAlertMessage = await foHomePage.subscribeToNewsletter(page, 'bonjour@prestashop.com');
      expect(newsletterSubscribeAlertMessage).to.contains(foHomePage.successSubscriptionMessage);
    });

    it('should check the confirmation email', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkConfirmationEmail', baseContext);

      numberOfEmails = allEmails.length;
      expect(allEmails[numberOfEmails - 1].subject).to.equal(`[${global.INSTALL.SHOP_NAME}] Newsletter confirmation`);
    });
  });

  describe(`Enable 'Send verification email' in the module '${dataModules.psEmailSubscription.name}'`, async () => {
    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO', baseContext);

      page = await foHomePage.closePage(browserContext, page, 0);

      const pageTitle = await psEmailSubscriptionPage.getPageSubtitle(page);
      expect(pageTitle).to.equal(psEmailSubscriptionPage.pageTitle);
    });

    it('should enable \'Send verification email\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'enableConfirmationEmail', baseContext);

      const successMessage = await psEmailSubscriptionPage.setSendVerificationEmail(page, true);
      expect(successMessage).to.contains(psEmailSubscriptionPage.updateSettingsSuccessMessage);
    });
  });

  describe('Go to FO to subscribe to the newsletter and check validation email', async () => {
    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFOToSubscribeToNewsletter2', baseContext);

      page = await psEmailSubscriptionPage.viewMyShop(page);

      const result = await foHomePage.isHomePage(page);
      expect(result).to.equal(true);
    });

    it('should subscribe to newsletter', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'subscribeToNewsletter2', baseContext);

      const newsletterSubscribeAlertMessage = await foHomePage.subscribeToNewsletter(page, 'hola@prestashop.com');
      expect(newsletterSubscribeAlertMessage).to.contains('A verification email has been sent. Please check your inbox');
    });

    it('should check verification email', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkVerificationEmail', baseContext);

      const number = allEmails.length;
      expect(number).to.equal(numberOfEmails + 1);
      expect(allEmails[number - 1].subject).to.equal(`[${global.INSTALL.SHOP_NAME}] Email verification`);
    });

    it('should click on the link provided in the email', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnProvidedLink', baseContext);

      const link: string = allEmails[numberOfEmails].text.match(/https?:\/\/.*emailsubscription[^\s]*/)![0];
      await foHomePage.goTo(page, link);
    });

    it('should check the success message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkSuccessMessage', baseContext);

      const successMessage = await emailSubscriptionPage.getSuccessMessage(page);
      expect(successMessage).to.equal(emailSubscriptionPage.emailRegistrationSuccessMessage);
    });

    it('should check the confirmation email', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkConfirmationEmail2', baseContext);

      expect(allEmails[numberOfEmails + 1].subject).to.equal(`[${global.INSTALL.SHOP_NAME}] Newsletter confirmation`);
    });
  });

  describe(`BO: case 2 - Disable all options in the module '${dataModules.psEmailSubscription.name}'`, async () => {
    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO1', baseContext);

      page = await foHomePage.closePage(browserContext, page, 0);

      const pageTitle = await psEmailSubscriptionPage.getPageSubtitle(page);
      expect(pageTitle).to.equal(psEmailSubscriptionPage.pageTitle);
    });

    it('should disable \'Send confirmation email\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'disableSendConfirmationEmail', baseContext);

      const successMessage = await psEmailSubscriptionPage.setSendConfirmationEmail(page, false);
      expect(successMessage).to.contains(psEmailSubscriptionPage.updateSettingsSuccessMessage);
    });

    it('should disable \'Send verification email\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'disableSendVerificationEmail', baseContext);

      const successMessage = await psEmailSubscriptionPage.setSendVerificationEmail(page, false);
      expect(successMessage).to.contains(psEmailSubscriptionPage.updateSettingsSuccessMessage);
    });
  });

  describe('Go to FO to subscribe to the newsletter and check no confirmation email is sent', async () => {
    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFOToSubscribeToNewsletter3', baseContext);

      page = await psEmailSubscriptionPage.viewMyShop(page);

      const result = await foHomePage.isHomePage(page);
      expect(result).to.equal(true);
    });

    it('should subscribe to newsletter', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'subscribeToNewsletter3', baseContext);

      const newsletterSubscribeAlertMessage = await foHomePage.subscribeToNewsletter(page, 'hola3@prestashop.com');
      expect(newsletterSubscribeAlertMessage).to.contains(foHomePage.successSubscriptionMessage);
    });

    it('should check that no confirmation email is sent', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNoConfirmationEmail', baseContext);

      const number = allEmails.length;
      expect(number).to.equal(numberOfEmails + 2);
    });
  });

  // Post-Condition : Reset SMTP config
  resetSmtpConfigTest(baseContext);
});
