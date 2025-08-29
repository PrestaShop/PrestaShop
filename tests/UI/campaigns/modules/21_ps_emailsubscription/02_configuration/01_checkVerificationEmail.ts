// Import utils
import testContext from '@utils/testContext';

// Import common tests
import {setupSmtpConfigTest, resetSmtpConfigTest} from '@commonTests/BO/advancedParameters/smtp';

import {expect} from 'chai';
import {
  boDashboardPage,
  boLoginPage,
  boModuleManagerPage,
  type BrowserContext,
  dataModules,
  foClassicHomePage,
  type MailDev,
  type MailDevEmail,
  modPsEmailSubscriptionBoMain,
  type Page,
  utilsMail,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'modules_ps_emailsubscription_configuration_checkVerificationEmail';

/*
Pre-condition:
- Setup SMTP parameters
Scenario
- Enable send a verification email after registration
- Go to FO and subscribe to newsletter
- Check email
- Disable send a verification email after registration
- Go to FO and subscribe to newsletter
- Check no received email
Post-condition:
- Reset SMTP parameters
 */
describe('Mail alerts module - Enable/Disable send a verification email after subscription', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let allEmails: MailDevEmail[];
  let numberOfEmails: number;
  let mailListener: MailDev;

  // Pre-Condition : Setup config SMTP
  setupSmtpConfigTest(`${baseContext}_preTest`);

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

  describe(`BO: case 1 - Enable 'Send verification email' in the module '${dataModules.psEmailSubscription.name}'`, async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Modules > Module Manager\' page', async function () {
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

    it(`should search the module ${dataModules.psEmailSubscription.name}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchModule', baseContext);

      const isModuleVisible = await boModuleManagerPage.searchModule(page, dataModules.psEmailSubscription);
      expect(isModuleVisible).to.equal(true);
    });

    it(`should go to the configuration page of the module '${dataModules.psEmailSubscription.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToConfigurationPage', baseContext);

      await boModuleManagerPage.goToConfigurationPage(page, dataModules.psEmailSubscription.tag);

      const pageTitle = await modPsEmailSubscriptionBoMain.getPageSubtitle(page);
      expect(pageTitle).to.equal(modPsEmailSubscriptionBoMain.pageTitle);
    });

    it('should enable send verification email', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'enableSendVerificationEmail', baseContext);

      const successMessage = await modPsEmailSubscriptionBoMain.setSendVerificationEmail(page, true);
      expect(successMessage).to.contains(modPsEmailSubscriptionBoMain.updateSettingsSuccessMessage);
    });
  });

  describe('Go to FO to subscribe to the newsletter and check received email', async () => {
    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFOToSubscribeToNewsletter', baseContext);

      page = await modPsEmailSubscriptionBoMain.viewMyShop(page);

      const result = await foClassicHomePage.isHomePage(page);
      expect(result).to.equal(true);
    });

    it('should subscribe to newsletter', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'subscribeToNewsletter', baseContext);

      const newsletterSubscribeAlertMessage = await foClassicHomePage.subscribeToNewsletter(page, 'test@prestashop.com');
      expect(newsletterSubscribeAlertMessage).to.contains(foClassicHomePage.successSendVerificationEmailMessage);
    });

    it('should check the verification email', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkVerificationEmail', baseContext);

      numberOfEmails = allEmails.length;
      expect(allEmails[numberOfEmails - 1].subject).to.equal(`[${global.INSTALL.SHOP_NAME}] Email verification`);
    });
  });

  describe(
    `BO: case 2 - Disable 'Send verification email'' in the module '${dataModules.psEmailSubscription.name}'`,
    async () => {
      it('should go back to BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO', baseContext);

        page = await foClassicHomePage.closePage(browserContext, page, 0);

        const pageTitle = await modPsEmailSubscriptionBoMain.getPageSubtitle(page);
        expect(pageTitle).to.equal(modPsEmailSubscriptionBoMain.pageTitle);
      });

      it('should enable send verification email', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'disableSendVerificationEmail', baseContext);

        const successMessage = await modPsEmailSubscriptionBoMain.setSendVerificationEmail(page, false);
        expect(successMessage).to.contains(modPsEmailSubscriptionBoMain.updateSettingsSuccessMessage);
      });
    });

  describe('Go to FO to subscribe to the newsletter and check no verification email is sent', async () => {
    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFOToSubscribeToNewsletter2', baseContext);

      page = await modPsEmailSubscriptionBoMain.viewMyShop(page);

      const result = await foClassicHomePage.isHomePage(page);
      expect(result).to.equal(true);
    });

    it('should subscribe to newsletter', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'subscribeToNewsletter2', baseContext);

      const newsletterSubscribeAlertMessage = await foClassicHomePage.subscribeToNewsletter(page, 'hello@prestashop.com');
      expect(newsletterSubscribeAlertMessage).to.contains(foClassicHomePage.successSubscriptionMessage);
    });

    it('should check that no verification email is sent', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNoVerificationEmail', baseContext);

      const number = allEmails.length;
      expect(number).to.equal(numberOfEmails);
    });
  });

  // Post-Condition : Reset SMTP config
  resetSmtpConfigTest(`${baseContext}_postTest`);
});
