// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';
import MailDevEmail from '@data/types/maildevEmail';
import mailHelper from '@utils/mailHelper';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';
import {installHummingbird, uninstallHummingbird} from '@commonTests/FO/hummingbird';
import {setupSmtpConfigTest, resetSmtpConfigTest} from '@commonTests/BO/advancedParameters/smtp';

// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import translationsPage from '@pages/BO/international/translations';
import cartRulesPage from '@pages/BO/catalog/discounts';
import addCartRulePage from '@pages/BO/catalog/discounts/add';
import storesPage from '@pages/BO/shopParameters/stores';
import {moduleManager as moduleManagerPage} from '@pages/BO/modules/moduleManager';

// Import FO pages classic theme
import {homePage} from '@pages/FO/classic/home';
import {loginPage as foLoginPage} from '@pages/FO/classic/login';
import {createAccountPage as foCreateAccountPage} from '@pages/FO/classic/myAccount/add';

// Import FO pages hummingbird theme
import homePageHummingbird from '@pages/FO/hummingbird/home';
import loginPageHummingbird from '@pages/FO/hummingbird/login';

// Import data
import Languages from '@data/demo/languages';
import Customers from '@data/demo/customers';
import Modules from '@data/demo/modules';
import CustomerData from '@data/faker/customer';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

import MailDev from 'maildev';

const baseContext: string = 'functional_BO_international_translations_modifyTranslation';

describe('BO - International - Translation : Modify translation', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let newMail: MailDevEmail;
  let mailListener: MailDev;
  const customerData: CustomerData = new CustomerData();

  // Pre-Condition: Setup config SMTP
  setupSmtpConfigTest(`${baseContext}_preTest_1`);

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    // Start listening to maildev server
    mailListener = mailHelper.createMailListener();
    mailHelper.startListener(mailListener);

    // Handle every new email
    mailListener.on('new', (email: MailDevEmail) => {
      newMail = email;
    });
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);

    // Stop listening to maildev server
    mailHelper.stopListener(mailListener);
  });

  it('should login in BO', async function () {
    await loginCommon.loginBO(this, page);
  });

  describe('Case 1 - Back office translations', async () => {
    it('should go to \'International > Translations\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToTranslationsPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.internationalParentLink,
        dashboardPage.translationsLink,
      );

      const pageTitle = await translationsPage.getPageTitle(page);
      expect(pageTitle).to.contains(translationsPage.pageTitle);
    });

    it(`should choose the translation 'Back office' and the language ${Languages.english.name}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'modifyTranslation', baseContext);

      await translationsPage.modifyTranslation(page, 'Back office translations', 'classic', Languages.english.name);

      const pageTitle = await translationsPage.getPageTitle(page);
      expect(pageTitle).to.contains(translationsPage.pageTitle);
    });

    it('should search \'Generate\' expression and modify the translation to \'Generate code\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'translateExpression', baseContext);

      await translationsPage.searchTranslation(page, 'Generate');

      const textResult = await translationsPage.translateExpression(page, 'Generate code');
      expect(textResult).to.equal(translationsPage.validationMessage);
    });

    it('should go to \'Catalog > Discounts\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToDiscountsPage2', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.catalogParentLink,
        dashboardPage.discountsLink,
      );

      const pageTitle = await cartRulesPage.getPageTitle(page);
      expect(pageTitle).to.contains(cartRulesPage.pageTitle);
    });

    it('should go to new cart rule page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewCartRulePage', baseContext);

      await cartRulesPage.goToAddNewCartRulesPage(page);

      const pageTitle = await addCartRulePage.getPageTitle(page);
      expect(pageTitle).to.contains(addCartRulePage.pageTitle);
    });

    it('should check that the button name is equal to \'Generate code\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkButtonName', baseContext);

      const buttonName = await addCartRulePage.getGenerateButtonName(page);
      expect(buttonName).to.equal('Generate code');
    });
  });

  describe('Case 2 - Front office translations', async () => {
    it('should go to \'International > Translations\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToTranslationsPage2', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.internationalParentLink,
        dashboardPage.translationsLink,
      );

      const pageTitle = await translationsPage.getPageTitle(page);
      expect(pageTitle).to.contains(translationsPage.pageTitle);
    });

    it(`should choose the translation 'Front office' and the language '${Languages.french.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'modifyTranslation2', baseContext);

      await translationsPage.modifyTranslation(page, 'Front office Translations', 'classic', Languages.french.name);

      const pageTitle = await translationsPage.getPageTitle(page);
      expect(pageTitle).to.contains(translationsPage.pageTitle);
    });

    it('should search \'Popular Products\' expression and modify the french translation', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'translateExpression2', baseContext);

      await translationsPage.searchTranslation(page, 'Popular Products');

      const textResult = await translationsPage.translateExpression(page, 'translate');
      expect(textResult).to.equal(translationsPage.validationMessage);
    });

    it('should go to FO page and change the language to French', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFO1', baseContext);

      page = await translationsPage.viewMyShop(page);
      await homePage.changeLanguage(page, 'fr');

      const isHomePage = await homePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
    });

    it('should check the translation', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkTranslation', baseContext);

      const title = await homePage.getBlockTitle(page);
      expect(title).to.contain('translate');
    });
  });

  // Pre-condition : Install Hummingbird
  installHummingbird(`${baseContext}_preTest_2`);

  describe('Case 3 - Front office translations with hummingbird theme', async () => {
    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBo', baseContext);

      // Close tab and init other page objects with new current tab
      page = await homePage.closePage(browserContext, page, 0);

      const pageTitle = await translationsPage.getPageTitle(page);
      expect(pageTitle).to.contains(translationsPage.pageTitle);
    });

    it('should go to \'International > Translations\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToTranslationsPage3', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.internationalParentLink,
        dashboardPage.translationsLink,
      );

      const pageTitle = await translationsPage.getPageTitle(page);
      expect(pageTitle).to.contains(translationsPage.pageTitle);
    });

    it(`should choose the translation 'Front office with hummingbird theme' and the language '${Languages.english.name}'`,
      async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'modifyTranslation3', baseContext);

        await translationsPage.modifyTranslation(page, 'Front office Translations', 'hummingbird', Languages.english.name);

        const pageTitle = await translationsPage.getPageTitle(page);
        expect(pageTitle).to.contains(translationsPage.pageTitle);
      });

    it('should search \'Popular Products\' expression and modify the french translation', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'translateExpression3', baseContext);

      await translationsPage.searchTranslation(page, 'Add to wishlist');

      const textResult = await translationsPage.translateExpression(page, 'Add to wishlist now');
      expect(textResult).to.equal(translationsPage.validationMessage);
    });

    it('should go to FO page and change the language to English', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFO2', baseContext);

      page = await translationsPage.viewMyShop(page);
      await homePageHummingbird.changeLanguage(page, 'en');

      const isHomePage = await homePageHummingbird.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginPage', baseContext);

      await homePage.goToLoginPage(page);

      const pageTitle = await loginPageHummingbird.getPageTitle(page);
      expect(pageTitle).to.equal(loginPageHummingbird.pageTitle);
    });

    it('should login by default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'login', baseContext);

      await loginPageHummingbird.customerLogin(page, Customers.johnDoe);

      const isCustomerConnected = await loginPageHummingbird.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is not connected!').to.eq(true);
    });

    it('should click on add product to wishlist icon', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnAddToWishlist', baseContext);

      const wishlistModalTitle = await homePageHummingbird.clickOnAddToWishListLink(page, 1);
      expect(wishlistModalTitle).to.equal('Add to wishlist now');
    });
  });

  // Post-condition : Uninstall Hummingbird
  uninstallHummingbird(`${baseContext}_postTest_1`);

  describe('Case 4 - Installed modules translations', async () => {
    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBo2', baseContext);

      // Close tab and init other page objects with new current tab
      page = await homePage.closePage(browserContext, page, 0);

      const pageTitle = await translationsPage.getPageTitle(page);
      expect(pageTitle).to.contains(translationsPage.pageTitle);
    });

    it('should go to \'International > Translations\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToTranslationsPage4', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.internationalParentLink,
        dashboardPage.translationsLink,
      );

      const pageTitle = await translationsPage.getPageTitle(page);
      expect(pageTitle).to.contains(translationsPage.pageTitle);
    });

    it(`should choose the translation 'Installed modules' and the language '${Languages.english.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'modifyTranslation4', baseContext);

      await translationsPage.modifyTranslation(
        page,
        'Installed modules translations',
        'classic',
        Languages.english.name,
        Modules.contactForm.name,
      );

      const pageTitle = await translationsPage.getPageTitle(page);
      expect(pageTitle).to.contains(translationsPage.pageTitle);
    });

    it('should search \'Contact form\' expression and modify the english translation', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'translateExpression4', baseContext);

      await translationsPage.searchTranslation(page, 'Contact form');

      const textResult = await translationsPage.translateExpression(page, 'Contact form Module Edited');
      expect(textResult).to.equal(translationsPage.validationMessage);
    });

    it('should go to \'Modules > Module Manager\' page', async function () {
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

    it(`should search the module ${Modules.contactForm.name}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchModule', baseContext);

      const isModuleVisible = await moduleManagerPage.searchModule(page, Modules.contactForm);
      expect(isModuleVisible, 'Module is not visible!').to.eq(true);
    });

    it('should check the module name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkModuleName', baseContext);

      const moduleName = await moduleManagerPage.getModuleName(page, Modules.contactForm);
      expect(moduleName).to.eq('Contact form Module Edited');
    });
  });

  describe('Case 5 - Email translations', async () => {
    it('should go to \'International > Translations\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToTranslationsPage5', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.internationalParentLink,
        dashboardPage.translationsLink,
      );

      const pageTitle = await translationsPage.getPageTitle(page);
      expect(pageTitle).to.contains(translationsPage.pageTitle);
    });

    it(`should choose the translation 'Email translations' and the language '${Languages.english.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'modifyTranslation5', baseContext);

      await translationsPage.modifyTranslation(
        page,
        'Email translations',
        'classic',
        Languages.english.name,
        '',
        'Subject',
      );

      const pageTitle = await translationsPage.getPageTitle(page);
      expect(pageTitle).to.contains(translationsPage.pageTitle);
    });

    it('should search \'Welcome!\' expression and modify the english translation', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'translateExpression5', baseContext);

      await translationsPage.searchTranslation(page, 'Welcome!');

      const textResult = await translationsPage.translateExpression(page, 'You"re welcome');
      expect(textResult).to.equal(translationsPage.validationMessage);
    });

    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'openFO', baseContext);

      page = await storesPage.viewMyShop(page);
      await homePage.changeLanguage(page, 'en');

      const isHomePage = await homePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
    });

    it('should logout by the link in the header', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signOutFOByHeaderLink', baseContext);

      await homePage.logout(page);

      const isCustomerConnected = await homePage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is connected!').to.eq(false);
    });

    it('should go to create account page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCreateAccountPage', baseContext);

      await homePage.goToLoginPage(page);
      await foLoginPage.goToCreateAccountPage(page);

      const pageHeaderTitle = await foCreateAccountPage.getHeaderTitle(page);
      expect(pageHeaderTitle).to.equal(foCreateAccountPage.formTitle);
    });

    it('should create new account', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createAccount', baseContext);

      await foCreateAccountPage.createAccount(page, customerData);

      const isCustomerConnected = await homePage.isCustomerConnected(page);
      expect(isCustomerConnected).to.eq(true);
    });

    it('should sign out from FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signOutFO', baseContext);

      await foCreateAccountPage.goToHomePage(page);
      await homePage.logout(page);

      const isCustomerConnected = await homePage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is connected').to.eq(false);
    });

    it('should check if the mail is in mailbox and check the subject', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkMailIsInMailbox', baseContext);

      expect(newMail.subject).to.contains('You"re welcome');
    });
  });

  // Pre-Condition: Setup config SMTP
  resetSmtpConfigTest(`${baseContext}_postTest_2`);

  describe('Case 6 - Other translations', async () => {
    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBo3', baseContext);

      // Close tab and init other page objects with new current tab
      page = await homePage.closePage(browserContext, page, 0);

      const pageTitle = await translationsPage.getPageTitle(page);
      expect(pageTitle).to.contains(translationsPage.pageTitle);
    });

    it('should go to \'International > Translations\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToTranslationsPage6', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.internationalParentLink,
        dashboardPage.translationsLink,
      );

      const pageTitle = await translationsPage.getPageTitle(page);
      expect(pageTitle).to.contains(translationsPage.pageTitle);
    });

    it(`should choose the translation 'Other' and the language '${Languages.english.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'modifyTranslation6', baseContext);

      await translationsPage.modifyTranslation(page, 'Other translations', 'classic', Languages.english.name);

      const pageTitle = await translationsPage.getPageTitle(page);
      expect(pageTitle).to.contains(translationsPage.pageTitle);
    });

    it('should search an expression and modify the english translation', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'translateExpression6', baseContext);

      await translationsPage.searchTranslation(page, 'If enabled, the voucher will not apply to products already on sale.');

      const textResult = await translationsPage.translateExpression(page,
        'The voucher is available only for new products');
      expect(textResult).to.equal(translationsPage.validationMessage);
    });

    it('should go to \'Catalog > Discounts\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToDiscountsPage', baseContext);

      await dashboardPage.goToSubMenu(page, dashboardPage.catalogParentLink, dashboardPage.discountsLink);

      const pageTitle = await cartRulesPage.getPageTitle(page);
      expect(pageTitle).to.contains(cartRulesPage.pageTitle);
    });

    it('should go to new cart rule page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewCartRulePage2', baseContext);

      await cartRulesPage.goToAddNewCartRulesPage(page);

      const pageTitle = await addCartRulePage.getPageTitle(page);
      expect(pageTitle).to.contains(addCartRulePage.pageTitle);
    });

    it('should go to ACTIONS tab and verify the translation of the title', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'verifyTranslation', baseContext);

      const title = await addCartRulePage.getTitleOfExcludeDiscountedProduct(page);
      expect(title).to.eq('The voucher is available only for new products');
    });
  });
});
