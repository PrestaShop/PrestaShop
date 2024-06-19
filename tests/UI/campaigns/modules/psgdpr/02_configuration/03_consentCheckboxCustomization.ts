// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import {createProductTest, deleteProductTest} from '@commonTests/BO/catalog/product';
import loginCommon from '@commonTests/BO/loginBO';
import {resetModule} from '@commonTests/BO/modules/moduleManager';

// Import pages
// Import FO pages
import {contactUsPage} from '@pages/FO/classic/contactUs';
import {homePage, homePage as foHomePage} from '@pages/FO/classic/home';
import {loginPage, loginPage as foLoginPage} from '@pages/FO/classic/login';
import {myAccountPage} from '@pages/FO/classic/myAccount';
import {createAccountPage as foCreateAccountPage} from '@pages/FO/classic/myAccount/add';
import {accountIdentityPage} from '@pages/FO/classic/myAccount/identity';
import {productPage as foProductPage} from '@pages/FO/classic/product';
import {searchResultsPage} from '@pages/FO/classic/searchResults';
// Import BO pages
import boDesignPositionsPage from '@pages/BO/design/positions';
import {moduleManager as moduleManagerPage} from '@pages/BO/modules/moduleManager';
import psGdpr from '@pages/BO/modules/psGdpr';
import psGdprTabDataConsent from '@pages/BO/modules/psGdpr/tabDataConsent';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import {faker, fakerFR} from '@faker-js/faker';
import {
  boDashboardPage,
  boDesignPositionsHookModulePage,
  dataLanguages,
  dataModules,
  dataProducts,
  FakerCustomer,
  FakerProduct,
  foClassicHomePage,
  foClassicLoginPage,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'modules_psgdpr_configuration_consentCheckboxCustomization';

describe('GDPR : Consent checkbox customization', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  const messageAccountCreation: string = faker.lorem.sentence();
  const messageCustomerAccount: string = faker.lorem.sentence();
  const messageNewsletter: string = faker.lorem.sentence();
  const messageContactForm: string = faker.lorem.sentence();
  const messageProductComments: string = faker.lorem.sentence();
  const messageMailAlerts: string = faker.lorem.sentence();
  const messageMailAlertsFR: string = fakerFR.lorem.sentence();
  const customerData: FakerCustomer = new FakerCustomer();
  const productOutOfStock: FakerProduct = new FakerProduct({
    quantity: 0,
  });

  createProductTest(productOutOfStock, `${baseContext}_preTest_0`);

  describe('Consent checkbox customization', async () => {
    // before and after functions
    before(async function () {
      browserContext = await utilsPlaywright.createBrowserContext(this.browser);
      page = await utilsPlaywright.newTab(browserContext);
    });

    after(async () => {
      await utilsPlaywright.closeBrowserContext(browserContext);
    });

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
      await moduleManagerPage.closeSfToolBar(page);

      const pageTitle = await moduleManagerPage.getPageTitle(page);
      expect(pageTitle).to.contains(moduleManagerPage.pageTitle);
    });

    it(`should search the module ${dataModules.psGdpr.name}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchModule', baseContext);

      const isModuleVisible = await moduleManagerPage.searchModule(page, dataModules.psGdpr);
      expect(isModuleVisible).to.eq(true);
    });

    it(`should go to the configuration page of the module '${dataModules.psGdpr.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToConfigurationPage', baseContext);

      await moduleManagerPage.goToConfigurationPage(page, dataModules.psGdpr.tag);

      const pageTitle = await psGdpr.getPageSubtitle(page);
      expect(pageTitle).to.eq(psGdpr.pageSubTitle);
    });

    it('should display the tab "Consent checkbox customization"', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'displayTabDataContent', baseContext);

      const isTabVisible = await psGdpr.goToTab(page, 3);
      expect(isTabVisible).to.be.equals(true);
    });

    it('should edit the consent message for the Account creation form', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editConsentMessageAccountCreation', baseContext);

      await psGdprTabDataConsent.setAccountCreationMessage(page, messageAccountCreation);

      const successMessage = await psGdprTabDataConsent.saveForm(page);
      expect(successMessage).to.be.contains(psGdprTabDataConsent.saveFormMessage);
    });

    it('should go to FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFo', baseContext);

      // View my shop and get the new tab
      page = await psGdprTabDataConsent.viewMyShop(page);

      const isHomePage = await foHomePage.isHomePage(page);
      expect(isHomePage).to.eq(true);
    });

    it('should click on the \'Sign in\' link', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnSignInLink', baseContext);

      // Check sign in link
      await foHomePage.clickOnHeaderLink(page, 'Sign in');

      const pageTitle = await foClassicLoginPage.getPageTitle(page);
      expect(pageTitle).to.equal(foClassicLoginPage.pageTitle);
    });

    it('should go to create account page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCreateAccountPage', baseContext);

      await foLoginPage.goToCreateAccountPage(page);

      const pageHeaderTitle = await foCreateAccountPage.getHeaderTitle(page);
      expect(pageHeaderTitle).to.equal(foCreateAccountPage.formTitle);

      const gdprLabel = await foCreateAccountPage.getGDPRLabel(page);
      expect(gdprLabel).to.contains(messageAccountCreation);
    });

    it('should create new account', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createNewAccount', baseContext);

      await foCreateAccountPage.createAccount(page, customerData);

      const isCustomerConnected = await foClassicHomePage.isCustomerConnected(page);
      expect(isCustomerConnected).to.eq(true);
    });

    it('should edit the consent message for the Customer Account form', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editConsentMessageCustomerAccount', baseContext);

      page = await foCreateAccountPage.changePage(browserContext, 0);
      await psGdprTabDataConsent.setCustomerAccountMessage(page, messageCustomerAccount);

      const successMessage = await psGdprTabDataConsent.saveForm(page);
      expect(successMessage).to.be.contains(psGdprTabDataConsent.saveFormMessage);
    });

    it('should check on the Information page the GDPR checkbox', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkAccountIdentityPage', baseContext);

      page = await psGdprTabDataConsent.changePage(browserContext, 1);
      await homePage.goToMyAccountPage(page);
      await myAccountPage.goToInformationPage(page);

      const pageTitle = await accountIdentityPage.getPageTitle(page);
      expect(pageTitle).to.equal(accountIdentityPage.pageTitle);

      const gdprLabel = await accountIdentityPage.getGDPRLabel(page);
      expect(gdprLabel).to.contains(messageCustomerAccount);
    });

    it('should disable consent message on creation and customer account', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'disableConsentMessageCreationCustomer', baseContext);

      page = await foCreateAccountPage.changePage(browserContext, 0);
      await psGdprTabDataConsent.setAccountCreationStatus(page, false);
      await psGdprTabDataConsent.setCustomerAccountStatus(page, false);

      const successMessage = await psGdprTabDataConsent.saveForm(page);
      expect(successMessage).to.be.contains(psGdprTabDataConsent.saveFormMessage);
    });

    it('should check on the Information page the GDPR checkbox is removed', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkAccountIdentityPageDisabled', baseContext);

      page = await psGdprTabDataConsent.changePage(browserContext, 1);
      await accountIdentityPage.reloadPage(page);

      const hasGDPRLabel = await accountIdentityPage.hasGDPRLabel(page);
      expect(hasGDPRLabel).to.equal(false);
    });

    it('should logout', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'foLogout', baseContext);

      await accountIdentityPage.logout(page);

      const isCustomerConnected = await foClassicLoginPage.isCustomerConnected(page);
      expect(isCustomerConnected).to.eq(false);

      const pageTitle = await foClassicLoginPage.getPageTitle(page);
      expect(pageTitle).to.equal(foClassicLoginPage.pageTitle);
    });

    it('should return to the "Create account" page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'returnToCreateAccountPage', baseContext);

      await foLoginPage.goToCreateAccountPage(page);

      const pageHeaderTitle = await foCreateAccountPage.getHeaderTitle(page);
      expect(pageHeaderTitle).to.equal(foCreateAccountPage.formTitle);

      const hasGDPRLabel = await foCreateAccountPage.hasGDPRLabel(page);
      expect(hasGDPRLabel).to.equal(false);
    });

    it('should edit the consent message for the Newsletter form', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editConsentMessageNewsletter', baseContext);

      page = await foCreateAccountPage.changePage(browserContext, 0);
      await psGdprTabDataConsent.setNewsletterMessage(page, messageNewsletter);

      const successMessage = await psGdprTabDataConsent.saveForm(page);
      expect(successMessage).to.be.contains(psGdprTabDataConsent.saveFormMessage);
    });

    it('should check on the Newsletter Block is not displayed', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNewsletterHomepageHidden', baseContext);

      page = await psGdprTabDataConsent.changePage(browserContext, 1);
      await foCreateAccountPage.goToHomePage(page);

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage).to.eq(true);

      const hasSubscribeNewsletterRGPD = await foHomePage.hasSubscribeNewsletterRGPD(page);
      expect(hasSubscribeNewsletterRGPD).to.equal(false);
    });

    it('should go to the Manage Hooks page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToManageHooks', baseContext);

      page = await foHomePage.changePage(browserContext, 0);
      await psGdprTabDataConsent.clickHeaderManageHooks(page);

      const pageTitle = await boDesignPositionsPage.getPageTitle(page);
      expect(pageTitle).to.be.equal(boDesignPositionsPage.pageTitle);
    });

    it('should add a new hook', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addNewHook', baseContext);

      await boDesignPositionsPage.clickHeaderHookModule(page);

      const pageTitle = await boDesignPositionsHookModulePage.getPageTitle(page);
      expect(pageTitle).to.be.equal(boDesignPositionsHookModulePage.pageTitle);
    });

    it('should connect the hook', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'saveHook', baseContext);

      const successMessage = await boDesignPositionsHookModulePage.saveForm(page);
      expect(successMessage).to.be.equal(boDesignPositionsPage.messageModuleAddedFromHook);
    });

    it('should check on the Subscribe Newsletter the RGPD checkbox', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkSubscribeNewsletterHasBlock', baseContext);

      page = await boDesignPositionsPage.changePage(browserContext, 1);
      await foHomePage.reloadPage(page);

      const hasSubscribeNewsletterRGPD = await foHomePage.hasSubscribeNewsletterRGPD(page);
      expect(hasSubscribeNewsletterRGPD).to.be.equals(true);

      const labelSubscribeNewsletterRGPD = await foHomePage.getSubscribeNewsletterRGPDLabel(page);
      expect(labelSubscribeNewsletterRGPD).to.be.equals(messageNewsletter);
    });

    it('should register to the newsletter', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'subscribeNewsletter', baseContext);

      const newsletterSubscribeAlertMessage = await foHomePage.subscribeToNewsletter(page, customerData.email);
      expect(newsletterSubscribeAlertMessage).to.contains(foHomePage.successSubscriptionMessage);
    });

    it('should go to \'Modules > Module Manager\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'returnToModuleManagerPage', baseContext);

      page = await foHomePage.changePage(browserContext, 0);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.modulesParentLink,
        boDashboardPage.moduleManagerLink,
      );
      await moduleManagerPage.closeSfToolBar(page);

      const pageTitle = await moduleManagerPage.getPageTitle(page);
      expect(pageTitle).to.contains(moduleManagerPage.pageTitle);
    });

    it(`should search the module ${dataModules.psGdpr.name}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchModule2', baseContext);

      const isModuleVisible = await moduleManagerPage.searchModule(page, dataModules.psGdpr);
      expect(isModuleVisible).to.eq(true);
    });

    it(`should go to the configuration page of the module '${dataModules.psGdpr.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToConfigurationPage2', baseContext);

      await moduleManagerPage.goToConfigurationPage(page, dataModules.psGdpr.tag);

      const pageTitle = await psGdpr.getPageSubtitle(page);
      expect(pageTitle).to.eq(psGdpr.pageSubTitle);
    });

    it('should display the tab "Consent checkbox customization"', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'displayTabDataContent2', baseContext);

      const isTabVisible = await psGdpr.goToTab(page, 3);
      expect(isTabVisible).to.be.equals(true);
    });

    it('should disable the consent message for the Newsletter form', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setNewsletterStatusFalse', baseContext);

      page = await foCreateAccountPage.changePage(browserContext, 0);
      await psGdprTabDataConsent.setNewsletterStatus(page, false);

      const successMessage = await psGdprTabDataConsent.saveForm(page);
      expect(successMessage).to.be.contains(psGdprTabDataConsent.saveFormMessage);
    });

    it('should check on the FO the Subscribe Newsletter Form', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'hasSubscribeNewsletterRGPDFalse', baseContext);

      page = await psGdprTabDataConsent.changePage(browserContext, 1);
      await foHomePage.reloadPage(page);

      const hasSubscribeNewsletterRGPD = await foHomePage.hasSubscribeNewsletterRGPD(page);
      expect(hasSubscribeNewsletterRGPD).to.be.equals(false);
    });

    it('should edit the consent message for the Product Comments form', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setProductCommentsMessage', baseContext);

      page = await foCreateAccountPage.changePage(browserContext, 0);
      await psGdprTabDataConsent.setProductCommentsMessage(page, messageProductComments);

      const successMessage = await psGdprTabDataConsent.saveForm(page);
      expect(successMessage).to.be.contains(psGdprTabDataConsent.saveFormMessage);
    });

    it('should go to the FO and click on "Sign in" link', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFoAndClickSignIn', baseContext);

      page = await psGdprTabDataConsent.changePage(browserContext, 1);
      await foHomePage.clickOnHeaderLink(page, 'Sign in');

      const pageTitle = await foClassicLoginPage.getPageTitle(page);
      expect(pageTitle).to.equal(foClassicLoginPage.pageTitle);
    });

    it('should sign in by default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signInFO', baseContext);

      await foClassicLoginPage.customerLogin(page, customerData);

      const isCustomerConnected = await loginPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is not connected!').to.eq(true);

      const isHomePage = await foHomePage.isHomePage(page);
      expect(isHomePage).to.eq(true);
    });

    it('should go to the product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductPage', baseContext);

      await foHomePage.goToProductPage(page, 1);

      const pageTitle = await foProductPage.getPageTitle(page);
      expect(pageTitle.toUpperCase()).to.contains(dataProducts.demo_1.name.toUpperCase());
    });

    it('should open the product review modal and check the GDPR label', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickAddReviewButton', baseContext);

      await foProductPage.clickAddReviewButton(page);

      const hasProductReviewGDPRLabel = await foProductPage.hasProductReviewGDPRLabel(page);
      expect(hasProductReviewGDPRLabel).to.be.equals(true);

      const labelProductReviewGDPRLabel = await foProductPage.getProductReviewGDPRLabel(page);
      expect(labelProductReviewGDPRLabel).to.be.equals(messageProductComments);
    });

    it('should close the product review modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeProductReviewModal', baseContext);

      const isModalVisible = await foProductPage.closeProductReviewModal(page);
      expect(isModalVisible).to.be.equals(false);
    });

    it('should disable the consent message for the ProductComments form', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setProductCommentsStatusFalse', baseContext);

      page = await foCreateAccountPage.changePage(browserContext, 0);
      await psGdprTabDataConsent.setProductCommentsStatus(page, false);

      const successMessage = await psGdprTabDataConsent.saveForm(page);
      expect(successMessage).to.be.contains(psGdprTabDataConsent.saveFormMessage);
    });

    it('should check on the Product Review modal that the GDPR Label is hidden', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'hasProductReviewGDPRLabelFalse', baseContext);

      page = await psGdprTabDataConsent.changePage(browserContext, 1);
      await foProductPage.reloadPage(page);
      await foProductPage.clickAddReviewButton(page);

      const hasProductReviewGDPRLabel = await foProductPage.hasProductReviewGDPRLabel(page);
      expect(hasProductReviewGDPRLabel).to.be.equals(false);
    });

    it('should close the product review modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeProductReviewModal2', baseContext);

      const isModalVisible = await foProductPage.closeProductReviewModal(page);
      expect(isModalVisible).to.be.equals(false);
    });

    it('should edit the consent message for the Contact Form form', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setContactFormMessage', baseContext);

      page = await foCreateAccountPage.changePage(browserContext, 0);
      await psGdprTabDataConsent.setContactFormMessage(page, messageContactForm);

      const successMessage = await psGdprTabDataConsent.saveForm(page);
      expect(successMessage).to.be.contains(psGdprTabDataConsent.saveFormMessage);
    });

    it('should check on Contact Form the GDPR Label', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkContactFormGDPRLabel', baseContext);

      page = await psGdprTabDataConsent.changePage(browserContext, 1);
      await foProductPage.goToFooterLink(page, 'Contact us');

      const pageTitle = await contactUsPage.getPageTitle(page);
      expect(pageTitle).to.equal(contactUsPage.pageTitle);

      const hasGDPRLabel = await contactUsPage.hasGDPRLabel(page);
      expect(hasGDPRLabel).to.equal(true);

      const gdprLabel = await contactUsPage.getGDPRLabel(page);
      expect(gdprLabel).to.equal(messageContactForm);
    });

    it('should disable consent message on Contact Form', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setContactFormStatusFalse', baseContext);

      page = await foCreateAccountPage.changePage(browserContext, 0);
      await psGdprTabDataConsent.setContactFormStatus(page, false);

      const successMessage = await psGdprTabDataConsent.saveForm(page);
      expect(successMessage).to.be.contains(psGdprTabDataConsent.saveFormMessage);
    });

    it('should check on the Contact Form that the GDPR Label is hidden', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'hasGDPRLabelFalse', baseContext);

      page = await psGdprTabDataConsent.changePage(browserContext, 1);
      await contactUsPage.reloadPage(page);

      const hasGDPRLabel = await contactUsPage.hasGDPRLabel(page);
      expect(hasGDPRLabel).to.equal(false);
    });

    it('should edit the consent message for the Mail Alerts form', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setMailAlertsMessage', baseContext);

      page = await foCreateAccountPage.changePage(browserContext, 0);
      await psGdprTabDataConsent.setMailAlertsMessage(page, messageMailAlerts);

      const successMessage = await psGdprTabDataConsent.saveForm(page);
      expect(successMessage).to.be.contains(psGdprTabDataConsent.saveFormMessage);
    });

    it('should check on the MailAlerts Form the GDPR Label', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkMailAlertsFormGDPRLabel', baseContext);

      page = await psGdprTabDataConsent.changePage(browserContext, 1);
      await contactUsPage.searchProduct(page, productOutOfStock.name);

      const pageTitleSearchResults = await searchResultsPage.getPageTitle(page);
      expect(pageTitleSearchResults).to.equal(searchResultsPage.pageTitle);

      await searchResultsPage.goToProductPage(page, 1);

      const pageTitleFoProduct = await foProductPage.getPageTitle(page);
      expect(pageTitleFoProduct).to.contains(productOutOfStock.name);

      const availabilityLabel = await foProductPage.getProductAvailabilityLabel(page);
      expect(availabilityLabel).to.contains('Out-of-Stock');

      const hasBlockMailAlert = await foProductPage.hasBlockMailAlert(page);
      expect(hasBlockMailAlert).to.be.equal(true);

      const hasBlockMailAlertGDPRLabel = await foProductPage.hasBlockMailAlertGDPRLabel(page);
      expect(hasBlockMailAlertGDPRLabel).to.be.equal(true);

      const gdprLabel = await foProductPage.getBlockMailAlertGDPRLabel(page);
      expect(gdprLabel).to.be.equal(messageMailAlerts);
    });

    it('should edit the consent message for the Mail Alerts form in French', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setMailAlertsMessageFR', baseContext);

      page = await foCreateAccountPage.changePage(browserContext, 0);
      await psGdprTabDataConsent.setMailAlertsMessage(page, messageMailAlertsFR, dataLanguages.french.id);

      const successMessage = await psGdprTabDataConsent.saveForm(page);
      expect(successMessage).to.be.contains(psGdprTabDataConsent.saveFormMessage);
    });

    it('should check on the MailAlerts Form the GDPR Label in French', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkMailAlertsFormGDPRLabelFR', baseContext);

      page = await psGdprTabDataConsent.changePage(browserContext, 1);
      await foProductPage.reloadPage(page);
      await foProductPage.changeLanguage(page, 'fr');

      const pageTitle = await foProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(productOutOfStock.nameFR);

      const availabilityLabel = await foProductPage.getProductAvailabilityLabel(page);
      expect(availabilityLabel).to.contains('Rupture de stock');

      const hasBlockMailAlert = await foProductPage.hasBlockMailAlert(page);
      expect(hasBlockMailAlert).to.be.equal(true);

      const hasBlockMailAlertGDPRLabel = await foProductPage.hasBlockMailAlertGDPRLabel(page);
      expect(hasBlockMailAlertGDPRLabel).to.be.equal(true);

      const gdprLabel = await foProductPage.getBlockMailAlertGDPRLabel(page);
      expect(gdprLabel).to.be.equal(messageMailAlertsFR);
    });

    it('should disable consent message on Mail Alerts Form', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setMailAlertsStatusFalse', baseContext);

      page = await foCreateAccountPage.changePage(browserContext, 0);
      await psGdprTabDataConsent.setMailAlertsStatus(page, false);

      const successMessage = await psGdprTabDataConsent.saveForm(page);
      expect(successMessage).to.be.contains(psGdprTabDataConsent.saveFormMessage);
    });

    it('should check on the MailAlerts Form the GDPR Label is hidden', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkMailAlertsFormGDPRLabelHidden', baseContext);

      page = await psGdprTabDataConsent.changePage(browserContext, 1);
      await foProductPage.reloadPage(page);

      const hasBlockMailAlert = await foProductPage.hasBlockMailAlert(page);
      expect(hasBlockMailAlert).to.be.equal(true);

      const hasBlockMailAlertGDPRLabel = await foProductPage.hasBlockMailAlertGDPRLabel(page);
      expect(hasBlockMailAlertGDPRLabel).to.be.equal(false);
    });
  });

  deleteProductTest(productOutOfStock, `${baseContext}_postTest_0`);

  resetModule(dataModules.psGdpr, `${baseContext}_postTest_1`);

  resetModule(dataModules.psEmailSubscription, `${baseContext}_postTest_2`);
});
