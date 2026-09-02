// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import {setupSmtpConfigTest, resetSmtpConfigTest} from '@commonTests/BO/advancedParameters/smtp';

import {expect} from 'chai';
import {
  boDashboardPage,
  boDesignEmailThemesPage,
  boDesignEmailThemesPreviewPage,
  boLoginPage,
  boMyProfilePage,
  boTranslationsPage,
  type BrowserContext,
  type MailDev,
  type MailDevEmail,
  type Page,
  utilsMail,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_design_emailTheme_configurationOfTheWholePage';

describe('BO - Design - Email Theme : Configuration of the whole page', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let newMail: MailDevEmail;
  let mailListener: MailDev;
  const textToSearch: string = 'Thank you for creating a customer account at {shop_name}.';
  const newTranslation: string = "Merci d'avoir créé votre compte client sur {shop_name}.bonjour";

  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);

    // Start listening to maildev server
    mailListener = utilsMail.createMailListener();
    utilsMail.startListener(mailListener);

    // Handle every new email
    mailListener.on('new', (email: MailDevEmail) => {
      newMail = email;
    });
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);

    // Stop listening to maildev server
    utilsMail.stopListener(mailListener);
  });

  // Pre-Condition: Setup config SMTP
  setupSmtpConfigTest(baseContext);

  describe('Configuration of the whole page', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Design > Email Theme\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEmailThemePage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.designParentLink,
        boDashboardPage.emailThemeLink,
      );
      await boDesignEmailThemesPage.closeSfToolBar(page);

      const pageTitle = await boDesignEmailThemesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDesignEmailThemesPage.pageTitle);
    });

    it('should preview email theme \'classic\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewEmailTheme', baseContext);

      await boDesignEmailThemesPage.previewEmailTheme(page, 'classic');

      const pageTitle = await boDesignEmailThemesPreviewPage.getPageTitle(page);
      expect(pageTitle).to.contains(`${boDesignEmailThemesPreviewPage.pageTitle} classic`);
    });

    it('should click on HTTPS button for the email account', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickHTTPSButton2', baseContext);

      page = await boDesignEmailThemesPreviewPage.viewHTTPLink(page, 1);
      expect(page.url()).to.contain('classic');
    });

    it('should close the page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closePage8', baseContext);

      page = await boDesignEmailThemesPreviewPage.closePage(browserContext, page, 1);

      const pageTitle = await boDesignEmailThemesPreviewPage.getPageTitle(page);
      expect(pageTitle).to.contains(`${boDesignEmailThemesPreviewPage.pageTitle} classic`);
    });

    it('should view raw html', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewRawHtml', baseContext);

      page = await boDesignEmailThemesPreviewPage.viewRawHtml(page, 1);
      expect(page.url())
        .to.contain('classic')
        .and.to.contain('raw')
        .and.to.contain('.html');
    });

    it('should close the page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closePage2', baseContext);

      page = await boDesignEmailThemesPreviewPage.closePage(browserContext, page, 1);

      const pageTitle = await boDesignEmailThemesPreviewPage.getPageTitle(page);
      expect(pageTitle).to.contains(`${boDesignEmailThemesPreviewPage.pageTitle} classic`);
    });

    it('should click on text', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewRawText', baseContext);

      page = await boDesignEmailThemesPreviewPage.viewRawText(page, 1);
      expect(page.url())
        .to.contain('classic')
        .and.to.contain('raw')
        .and.to.contain('.txt');
    });

    it('should close the page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closePage3', baseContext);

      page = await boDesignEmailThemesPreviewPage.closePage(browserContext, page, 1);

      const pageTitle = await boDesignEmailThemesPreviewPage.getPageTitle(page);
      expect(pageTitle).to.contains(`${boDesignEmailThemesPreviewPage.pageTitle} classic`);
    });

    it('should click on send a test email and check the error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sendTestEmail5', baseContext);

      const successMessage = await boDesignEmailThemesPreviewPage.sendTestEmail(page, 1);
      expect(successMessage).to.contains(boDesignEmailThemesPreviewPage.errorMessageSendEmail);
    });

    it('should go back to configuration page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToConfigurationPage', baseContext);

      await boDesignEmailThemesPreviewPage.goBackToEmailThemesPage(page);

      const pageTitle = await boDesignEmailThemesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDesignEmailThemesPage.pageTitle);
    });

    it('should select \'classic\' as default email theme', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'classicAsDefaultEmailTheme', baseContext);

      const textMessage = await boDesignEmailThemesPage.selectDefaultEmailTheme(page, 'classic');
      expect(textMessage).to.contains(boDesignEmailThemesPage.emailThemeConfigurationSuccessfulMessage);
    });

    it('should configure generate emails form', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'generateEmails', baseContext);

      const successMessage = await boDesignEmailThemesPage.configureGenerateEmails(
        page,
        'classic',
        'English (English)',
        'classic',
        true,
      );
      expect(successMessage).to.contains(boDesignEmailThemesPage.generateEmailsSuccessMessage('classic', 'en'));
    });

    it('should preview email theme \'classic\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewEmailTheme2', baseContext);

      await boDesignEmailThemesPage.previewEmailTheme(page, 'classic');

      const pageTitle = await boDesignEmailThemesPreviewPage.getPageTitle(page);
      expect(pageTitle).to.contains(`${boDesignEmailThemesPreviewPage.pageTitle} classic`);
    });

    it('should click on send a test email and check the validation', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sendTestEmail2', baseContext);

      const successMessage = await boDesignEmailThemesPreviewPage.sendTestEmail(page, 1);
      expect(successMessage).to.contains(boDesignEmailThemesPreviewPage.successMessageSendEmail('account'));
    });

    it('should check if mail is in mailbox', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkEmail', baseContext);

      expect(newMail.subject).to.contains(`[${global.INSTALL.SHOP_NAME}] Test email account`);
    });

    it('should click on HTTPS button for customer_qty', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickHTTPSButtonCustomerQty', baseContext);

      page = await boDesignEmailThemesPreviewPage.viewHTTPLink(page, 35);
      expect(page.url()).to.contain('classic');
    });

    it('should close the page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closePage4', baseContext);

      page = await boDesignEmailThemesPreviewPage.closePage(browserContext, page, 1);

      const pageTitle = await boDesignEmailThemesPreviewPage.getPageTitle(page);
      expect(pageTitle).to.contains(`${boDesignEmailThemesPreviewPage.pageTitle} classic`);
    });

    it('should view raw html', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewRawHtml2', baseContext);

      page = await boDesignEmailThemesPreviewPage.viewRawHtml(page, 1);
      expect(page.url())
        .to.contain('classic')
        .and.to.contain('raw')
        .and.to.contain('.html');
    });

    it('should close the page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closePage5', baseContext);

      page = await boDesignEmailThemesPreviewPage.closePage(browserContext, page, 1);

      const pageTitle = await boDesignEmailThemesPreviewPage.getPageTitle(page);
      expect(pageTitle).to.contains(`${boDesignEmailThemesPreviewPage.pageTitle} classic`);
    });

    it('should click on text', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewRawText2', baseContext);

      page = await boDesignEmailThemesPreviewPage.viewRawText(page, 1);
      expect(page.url())
        .to.contain('classic')
        .and.to.contain('raw')
        .and.to.contain('.txt');
    });

    it('should close the page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closePage6', baseContext);

      page = await boDesignEmailThemesPreviewPage.closePage(browserContext, page, 1);

      const pageTitle = await boDesignEmailThemesPreviewPage.getPageTitle(page);
      expect(pageTitle).to.contains(`${boDesignEmailThemesPreviewPage.pageTitle} classic`);
    });

    it('should go back to configuration page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToConfigurationPage2', baseContext);

      await boDesignEmailThemesPreviewPage.goBackToEmailThemesPage(page);

      const pageTitle = await boDesignEmailThemesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDesignEmailThemesPage.pageTitle);
    });

    it('should select \'modern\' as default email theme', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'modernAsDefaultEmailTheme', baseContext);

      const textMessage = await boDesignEmailThemesPage.selectDefaultEmailTheme(page, 'modern');
      expect(textMessage).to.contains(boDesignEmailThemesPage.emailThemeConfigurationSuccessfulMessage);
    });

    it('should configure generate emails form', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'generateEmails2', baseContext);

      const successMessage = await boDesignEmailThemesPage.configureGenerateEmails(
        page,
        'modern',
        'English (English)',
        'classic',
        true,
      );
      expect(successMessage).to.contains(boDesignEmailThemesPage.generateEmailsSuccessMessage('modern', 'en'));
    });

    it('should preview email theme \'modern\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewEmailTheme3', baseContext);

      await boDesignEmailThemesPage.previewEmailTheme(page, 'modern');

      const pageTitle = await boDesignEmailThemesPreviewPage.getPageTitle(page);
      expect(pageTitle).to.contains(`${boDesignEmailThemesPreviewPage.pageTitle} modern`);
    });

    it('should click on HTTPS button for the backoffice_order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickHTTPSButton', baseContext);

      page = await boDesignEmailThemesPreviewPage.viewHTTPLink(page, 2);
      expect(page.url()).to.contain('modern');
    });

    it('should close the page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closePage7', baseContext);

      page = await boDesignEmailThemesPreviewPage.closePage(browserContext, page, 1);

      const pageTitle = await boDesignEmailThemesPreviewPage.getPageTitle(page);
      expect(pageTitle).to.contains(`${boDesignEmailThemesPreviewPage.pageTitle} modern`);
    });

    it('should click on send a test email and check the validation', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sendTestEmail3', baseContext);

      const successMessage = await boDesignEmailThemesPreviewPage.sendTestEmail(page, 2);
      expect(successMessage).to.contains(boDesignEmailThemesPreviewPage.successMessageSendEmail('backoffice_order'));
    });

    it('should check if mail is in mailbox', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkEmail2', baseContext);

      expect(newMail.subject).to.contains(`[${global.INSTALL.SHOP_NAME}] Test email backoffice_order`);
    });

    it('should go back to configuration page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToConfigurationPage3', baseContext);

      await boDesignEmailThemesPreviewPage.goBackToEmailThemesPage(page);

      const pageTitle = await boDesignEmailThemesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDesignEmailThemesPage.pageTitle);
    });

    it('should choose French in translate emails form', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'translateEmailsToFrench', baseContext);

      await boDesignEmailThemesPage.selectTranslateEmailLanguage(page, 'Français (French)');

      const pageTitle = await boTranslationsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boTranslationsPage.pageTitle);
    });

    it('should search an expression and modify the translation', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'translateExpression', baseContext);

      await boTranslationsPage.searchTranslation(page, textToSearch);

      const textResult = await boTranslationsPage.translateExpression(page, newTranslation);
      expect(textResult).to.equal(boTranslationsPage.validationMessage);
    });

    it('should go to \'Design > Email Theme\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEmailThemePage2', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.designParentLink,
        boDashboardPage.emailThemeLink,
      );
      await boDesignEmailThemesPage.closeSfToolBar(page);

      const pageTitle = await boDesignEmailThemesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDesignEmailThemesPage.pageTitle);
    });

    it('should configure generate emails form', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'generateEmails3', baseContext);

      const successMessage = await boDesignEmailThemesPage.configureGenerateEmails(
        page,
        'modern',
        'Français (French)',
        'Core (no theme selected)',
        true,
      );
      expect(successMessage).to.contains(boDesignEmailThemesPage.generateEmailsSuccessMessage('modern', 'fr'));
    });

    it('should go to \'Your profile\' page and update the language to French', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToMyProfilePage', baseContext);

      await boDashboardPage.goToMyProfile(page);
      await boMyProfilePage.editLanguage(page, 'Français (French)');

      const textResult = await boMyProfilePage.getAlertSuccess(page);
      expect(textResult).to.equal(boMyProfilePage.successfulUpdateMessage);
    });

    it('should go to \'Design > Email Theme\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEmailThemePage3', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.designParentLink,
        boDashboardPage.emailThemeLink,
      );
      await boDesignEmailThemesPage.closeSfToolBar(page);

      const pageTitle = await boDesignEmailThemesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDesignEmailThemesPage.pageTitleFR);
    });

    it('should preview email theme \'modern\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewEmailTheme4', baseContext);

      await boDesignEmailThemesPage.previewEmailTheme(page, 'modern');

      const pageTitle = await boDesignEmailThemesPreviewPage.getPageTitle(page);
      expect(pageTitle).to.contains(`${boDesignEmailThemesPreviewPage.pageTitleFR} modern`);
    });

    it('should click on send a test email and check the validation', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sendTestEmail4', baseContext);

      const successMessage = await boDesignEmailThemesPreviewPage.sendTestEmail(page, 1);
      expect(successMessage).to.contains(boDesignEmailThemesPreviewPage.successMessageSendEmailFR('account'));
    });

    it('should check if mail is in mailbox', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkEmail3', baseContext);

      expect(newMail.text).to.contains(`Merci d'avoir créé votre compte client sur ${global.INSTALL.SHOP_NAME}.bonjour`);
    });

    it('should go to \'Your profile\' page and update the language to English', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToMyProfilePage2', baseContext);

      await boDashboardPage.goToMyProfile(page);
      await boMyProfilePage.editLanguage(page, 'English (English)');

      const textResult = await boMyProfilePage.getAlertSuccess(page);
      expect(textResult).to.equal(boMyProfilePage.successfulUpdateMessageFR);
    });
  });

  // Post-Condition: Reset config SMTP
  resetSmtpConfigTest(baseContext);
});
