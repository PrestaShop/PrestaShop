// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';
import mailHelper from '@utils/mailHelper';

// Import common tests
import loginCommon from '@commonTests/BO/loginBO';
import {setupSmtpConfigTest, resetSmtpConfigTest} from '@commonTests/BO/advancedParameters/smtp';

// Import pages
// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import emailSubscriptionPage from '@pages/BO/modules/psEmailSubscription';
import {moduleManager} from '@pages/BO/modules/moduleManager';
// Import FO pages
import {homePage as foHomePage} from '@pages/FO/home';

// Import data
import Modules from '@data/demo/modules';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import MailDevEmail from '@data/types/maildevEmail';
import MailDev from 'maildev';

const baseContext: string = 'modules_ps_emailsubscription_configuration_checkWelcomeVoucher';

/*
Pre-condition:
- Setup SMTP parameters
Scenario
- Set a welcome voucher
- Go to FO and subscribe to newsletter
- Check email
Post-condition:
- Reset SMTP parameters
 */
describe('Mail alerts module - Check welcome voucher code', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let allEmails: MailDevEmail[];
  let numberOfEmails: number;
  let mailListener: MailDev;

  // Pre-Condition : Setup config SMTP
  setupSmtpConfigTest(`${baseContext}_preTest`);

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    // Start listening to maildev server
    mailListener = mailHelper.createMailListener();
    mailHelper.startListener(mailListener);

    // get all emails
    // @ts-ignore
    mailListener.getAllEmail((err: Error, emails: MailDevEmail[]) => {
      allEmails = emails;
    });
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);

    // Stop listening to maildev server
    mailHelper.stopListener(mailListener);
  });

  describe(`Set a welcome voucher in the module '${Modules.psEmailSubscription.name}'`, async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Modules > Module Manager\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToModuleManagerPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.modulesParentLink,
        dashboardPage.moduleManagerLink,
      );
      await moduleManager.closeSfToolBar(page);

      const pageTitle = await moduleManager.getPageTitle(page);
      expect(pageTitle).to.contains(moduleManager.pageTitle);
    });

    it(`should search the module ${Modules.psEmailSubscription.name}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchModule', baseContext);

      const isModuleVisible = await moduleManager.searchModule(page, Modules.psEmailSubscription);
      expect(isModuleVisible).to.equal(true);
    });

    it(`should go to the configuration page of the module '${Modules.psEmailSubscription.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToConfigurationPage', baseContext);

      await moduleManager.goToConfigurationPage(page, Modules.psEmailSubscription.tag);

      const pageTitle = await emailSubscriptionPage.getPageSubtitle(page);
      expect(pageTitle).to.equal(emailSubscriptionPage.pageTitle);
    });

    it('should set a welcome voucher', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setWelcomeVoucher', baseContext);

      const successMessage = await emailSubscriptionPage.setWelcomeVoucher(page, 'welcome');
      expect(successMessage).to.contains(emailSubscriptionPage.updateSettingsSuccessMessage);
    });
  });

  describe('Go to FO to subscribe to the newsletter and check received email', async () => {
    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFOToSubscribeToNewsletter', baseContext);

      page = await emailSubscriptionPage.viewMyShop(page);

      const result = await foHomePage.isHomePage(page);
      expect(result).to.equal(true);
    });

    it('should subscribe to newsletter', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'subscribeToNewsletter', baseContext);

      const newsletterSubscribeAlertMessage = await foHomePage.subscribeToNewsletter(page, 'bonjour4@prestashop.com');
      expect(newsletterSubscribeAlertMessage).to.contains(foHomePage.successSubscriptionMessage);
    });

    it('should check the voucher email', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkConfirmationEmail', baseContext);

      numberOfEmails = allEmails.length;
      expect(allEmails[numberOfEmails - 1].subject).to.equal(`[${global.INSTALL.SHOP_NAME}] Newsletter voucher`);
      expect(allEmails[numberOfEmails - 1].text).to.contains('We are pleased to offer you the following voucher: welcome');
    });
  });

  describe(`Delete voucher '${Modules.psEmailSubscription.name}'`, async () => {
    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO', baseContext);

      page = await foHomePage.closePage(browserContext, page, 0);

      const pageTitle = await emailSubscriptionPage.getPageSubtitle(page);
      expect(pageTitle).to.equal(emailSubscriptionPage.pageTitle);
    });

    it('should delete the voucher', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteVoucher', baseContext);

      const successMessage = await emailSubscriptionPage.setWelcomeVoucher(page, '');
      expect(successMessage).to.contains(emailSubscriptionPage.updateSettingsSuccessMessage);
    });
  });

  describe('Go to FO to subscribe to the newsletter and check no voucher email is sent', async () => {
    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFOToSubscribeToNewsletter2', baseContext);

      page = await emailSubscriptionPage.viewMyShop(page);

      const result = await foHomePage.isHomePage(page);
      expect(result).to.equal(true);
    });

    it('should subscribe to newsletter', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'subscribeToNewsletter2', baseContext);

      const newsletterSubscribeAlertMessage = await foHomePage.subscribeToNewsletter(page, 'hola2@prestashop.com');
      expect(newsletterSubscribeAlertMessage).to.contains(foHomePage.successSubscriptionMessage);
    });

    it('should check that no voucher email is sent', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNoVoucherEmail', baseContext);

      const number = allEmails.length;
      expect(number).to.equal(numberOfEmails);
    });
  });

  // Post-Condition : Reset SMTP config
  resetSmtpConfigTest(`${baseContext}_postTest`);
});
