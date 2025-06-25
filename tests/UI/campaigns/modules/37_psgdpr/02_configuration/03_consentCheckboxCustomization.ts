import testContext from '@utils/testContext';
import {expect} from 'chai';
import {faker, fakerFR} from '@faker-js/faker';

// Import commonTests
import {createProductTest, deleteProductTest} from '@commonTests/BO/catalog/product';
import {resetModule} from '@commonTests/BO/modules/moduleManager';

import {
  boDashboardPage,
  boDesignPositionsPage,
  boDesignPositionsHookModulePage,
  boLoginPage,
  boModuleManagerPage,
  type BrowserContext,
  dataLanguages,
  dataModules,
  dataProducts,
  FakerCustomer,
  FakerProduct,
  foClassicContactUsPage,
  foClassicCreateAccountPage,
  foClassicHomePage,
  foClassicLoginPage,
  foClassicMyAccountPage,
  foClassicMyInformationsPage,
  foClassicProductPage,
  foClassicSearchResultsPage,
  modPsGdprBoMain,
  modPsGdprBoTabDataConsent,
  type Page,
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

    it(`should search the module ${dataModules.psGdpr.name}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchModule', baseContext);

      const isModuleVisible = await boModuleManagerPage.searchModule(page, dataModules.psGdpr);
      expect(isModuleVisible).to.eq(true);
    });

    it(`should go to the configuration page of the module '${dataModules.psGdpr.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToConfigurationPage', baseContext);

      await boModuleManagerPage.goToConfigurationPage(page, dataModules.psGdpr.tag);

      const pageTitle = await modPsGdprBoMain.getPageSubtitle(page);
      expect(pageTitle).to.eq(modPsGdprBoMain.pageSubTitle);
    });

    it('should display the tab "Consent checkbox customization"', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'displayTabDataContent', baseContext);

      const isTabVisible = await modPsGdprBoMain.goToTab(page, 3);
      expect(isTabVisible).to.be.equals(true);
    });

    it('should edit the consent message for the Account creation form', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editConsentMessageAccountCreation', baseContext);

      await modPsGdprBoTabDataConsent.setAccountCreationMessage(page, messageAccountCreation);

      const successMessage = await modPsGdprBoTabDataConsent.saveForm(page);
      expect(successMessage).to.be.contains(modPsGdprBoTabDataConsent.saveFormMessage);
    });

    it('should go to FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFo', baseContext);

      // View my shop and get the new tab
      page = await modPsGdprBoTabDataConsent.viewMyShop(page);

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage).to.eq(true);
    });

    it('should click on the \'Sign in\' link', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnSignInLink', baseContext);

      // Check sign in link
      await foClassicHomePage.clickOnHeaderLink(page, 'Sign in');

      const pageTitle = await foClassicLoginPage.getPageTitle(page);
      expect(pageTitle).to.equal(foClassicLoginPage.pageTitle);
    });

    it('should go to create account page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCreateAccountPage', baseContext);

      await foClassicLoginPage.goToCreateAccountPage(page);

      const pageHeaderTitle = await foClassicCreateAccountPage.getHeaderTitle(page);
      expect(pageHeaderTitle).to.equal(foClassicCreateAccountPage.formTitle);

      const gdprLabel = await foClassicCreateAccountPage.getGDPRLabel(page);
      expect(gdprLabel).to.contains(messageAccountCreation);
    });

    it('should create new account', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createNewAccount', baseContext);

      await foClassicCreateAccountPage.createAccount(page, customerData);

      const isCustomerConnected = await foClassicHomePage.isCustomerConnected(page);
      expect(isCustomerConnected).to.eq(true);
    });

    it('should edit the consent message for the Customer Account form', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editConsentMessageCustomerAccount', baseContext);

      page = await foClassicCreateAccountPage.changePage(browserContext, 0);
      await modPsGdprBoTabDataConsent.setCustomerAccountMessage(page, messageCustomerAccount);

      const successMessage = await modPsGdprBoTabDataConsent.saveForm(page);
      expect(successMessage).to.be.contains(modPsGdprBoTabDataConsent.saveFormMessage);
    });

    it('should check on the Information page the GDPR checkbox', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkAccountIdentityPage', baseContext);

      page = await modPsGdprBoTabDataConsent.changePage(browserContext, 1);
      await foClassicHomePage.goToMyAccountPage(page);
      await foClassicMyAccountPage.goToInformationPage(page);

      const pageTitle = await foClassicMyInformationsPage.getPageTitle(page);
      expect(pageTitle).to.equal(foClassicMyInformationsPage.pageTitle);

      const gdprLabel = await foClassicMyInformationsPage.getGDPRLabel(page);
      expect(gdprLabel).to.contains(messageCustomerAccount);
    });

    it('should disable consent message on creation and customer account', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'disableConsentMessageCreationCustomer', baseContext);

      page = await foClassicCreateAccountPage.changePage(browserContext, 0);
      await modPsGdprBoTabDataConsent.setAccountCreationStatus(page, false);
      await modPsGdprBoTabDataConsent.setCustomerAccountStatus(page, false);

      const successMessage = await modPsGdprBoTabDataConsent.saveForm(page);
      expect(successMessage).to.be.contains(modPsGdprBoTabDataConsent.saveFormMessage);
    });

    it('should check on the Information page the GDPR checkbox is removed', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkAccountIdentityPageDisabled', baseContext);

      page = await modPsGdprBoTabDataConsent.changePage(browserContext, 1);
      await foClassicMyInformationsPage.reloadPage(page);

      const hasGDPRLabel = await foClassicMyInformationsPage.hasGDPRLabel(page);
      expect(hasGDPRLabel).to.equal(false);
    });

    it('should logout', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'foLogout', baseContext);

      await foClassicMyInformationsPage.logout(page);

      const isCustomerConnected = await foClassicLoginPage.isCustomerConnected(page);
      expect(isCustomerConnected).to.eq(false);

      const pageTitle = await foClassicLoginPage.getPageTitle(page);
      expect(pageTitle).to.equal(foClassicLoginPage.pageTitle);
    });

    it('should return to the "Create account" page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'returnToCreateAccountPage', baseContext);

      await foClassicLoginPage.goToCreateAccountPage(page);

      const pageHeaderTitle = await foClassicCreateAccountPage.getHeaderTitle(page);
      expect(pageHeaderTitle).to.equal(foClassicCreateAccountPage.formTitle);

      const hasGDPRLabel = await foClassicCreateAccountPage.hasGDPRLabel(page);
      expect(hasGDPRLabel).to.equal(false);
    });

    it('should edit the consent message for the Newsletter form', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editConsentMessageNewsletter', baseContext);

      page = await foClassicCreateAccountPage.changePage(browserContext, 0);
      await modPsGdprBoTabDataConsent.setNewsletterMessage(page, messageNewsletter);

      const successMessage = await modPsGdprBoTabDataConsent.saveForm(page);
      expect(successMessage).to.be.contains(modPsGdprBoTabDataConsent.saveFormMessage);
    });

    it('should check on the Newsletter Block is not displayed', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNewsletterHomepageHidden', baseContext);

      page = await modPsGdprBoTabDataConsent.changePage(browserContext, 1);
      await foClassicCreateAccountPage.goToHomePage(page);

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage).to.eq(true);

      const hasSubscribeNewsletterRGPD = await foClassicHomePage.hasSubscribeNewsletterRGPD(page);
      expect(hasSubscribeNewsletterRGPD).to.equal(false);
    });

    it('should go to the Manage Hooks page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToManageHooks', baseContext);

      page = await foClassicHomePage.changePage(browserContext, 0);
      await modPsGdprBoTabDataConsent.clickHeaderManageHooks(page);

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
      await foClassicHomePage.reloadPage(page);

      const hasSubscribeNewsletterRGPD = await foClassicHomePage.hasSubscribeNewsletterRGPD(page);
      expect(hasSubscribeNewsletterRGPD).to.be.equals(true);

      const labelSubscribeNewsletterRGPD = await foClassicHomePage.getSubscribeNewsletterRGPDLabel(page);
      expect(labelSubscribeNewsletterRGPD).to.be.equals(messageNewsletter);
    });

    it('should register to the newsletter', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'subscribeNewsletter', baseContext);

      const newsletterSubscribeAlertMessage = await foClassicHomePage.subscribeToNewsletter(page, customerData.email);
      expect(newsletterSubscribeAlertMessage).to.contains(foClassicHomePage.successSubscriptionMessage);
    });

    it('should go to \'Modules > Module Manager\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'returnToModuleManagerPage', baseContext);

      page = await foClassicHomePage.changePage(browserContext, 0);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.modulesParentLink,
        boDashboardPage.moduleManagerLink,
      );
      await boModuleManagerPage.closeSfToolBar(page);

      const pageTitle = await boModuleManagerPage.getPageTitle(page);
      expect(pageTitle).to.contains(boModuleManagerPage.pageTitle);
    });

    it(`should search the module ${dataModules.psGdpr.name}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchModule2', baseContext);

      const isModuleVisible = await boModuleManagerPage.searchModule(page, dataModules.psGdpr);
      expect(isModuleVisible).to.eq(true);
    });

    it(`should go to the configuration page of the module '${dataModules.psGdpr.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToConfigurationPage2', baseContext);

      await boModuleManagerPage.goToConfigurationPage(page, dataModules.psGdpr.tag);

      const pageTitle = await modPsGdprBoMain.getPageSubtitle(page);
      expect(pageTitle).to.eq(modPsGdprBoMain.pageSubTitle);
    });

    it('should display the tab "Consent checkbox customization"', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'displayTabDataContent2', baseContext);

      const isTabVisible = await modPsGdprBoMain.goToTab(page, 3);
      expect(isTabVisible).to.be.equals(true);
    });

    it('should disable the consent message for the Newsletter form', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setNewsletterStatusFalse', baseContext);

      page = await foClassicCreateAccountPage.changePage(browserContext, 0);
      await modPsGdprBoTabDataConsent.setNewsletterStatus(page, false);

      const successMessage = await modPsGdprBoTabDataConsent.saveForm(page);
      expect(successMessage).to.be.contains(modPsGdprBoTabDataConsent.saveFormMessage);
    });

    it('should check on the FO the Subscribe Newsletter Form', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'hasSubscribeNewsletterRGPDFalse', baseContext);

      page = await modPsGdprBoTabDataConsent.changePage(browserContext, 1);
      await foClassicHomePage.reloadPage(page);

      const hasSubscribeNewsletterRGPD = await foClassicHomePage.hasSubscribeNewsletterRGPD(page);
      expect(hasSubscribeNewsletterRGPD).to.be.equals(false);
    });

    it('should edit the consent message for the Product Comments form', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setProductCommentsMessage', baseContext);

      page = await foClassicCreateAccountPage.changePage(browserContext, 0);
      await modPsGdprBoTabDataConsent.setProductCommentsMessage(page, messageProductComments);

      const successMessage = await modPsGdprBoTabDataConsent.saveForm(page);
      expect(successMessage).to.be.contains(modPsGdprBoTabDataConsent.saveFormMessage);
    });

    it('should go to the FO and click on "Sign in" link', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFoAndClickSignIn', baseContext);

      page = await modPsGdprBoTabDataConsent.changePage(browserContext, 1);
      await foClassicHomePage.clickOnHeaderLink(page, 'Sign in');

      const pageTitle = await foClassicLoginPage.getPageTitle(page);
      expect(pageTitle).to.equal(foClassicLoginPage.pageTitle);
    });

    it('should sign in by default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signInFO', baseContext);

      await foClassicLoginPage.customerLogin(page, customerData);

      const isCustomerConnected = await foClassicLoginPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is not connected!').to.eq(true);

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage).to.eq(true);
    });

    it('should go to the product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductPage', baseContext);

      await foClassicHomePage.goToProductPage(page, 1);

      const pageTitle = await foClassicProductPage.getPageTitle(page);
      expect(pageTitle.toUpperCase()).to.contains(dataProducts.demo_1.name.toUpperCase());
    });

    it('should open the product review modal and check the GDPR label', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickAddReviewButton', baseContext);

      await foClassicProductPage.clickAddReviewButton(page);

      const hasProductReviewGDPRLabel = await foClassicProductPage.hasProductReviewGDPRLabel(page);
      expect(hasProductReviewGDPRLabel).to.be.equals(true);

      const labelProductReviewGDPRLabel = await foClassicProductPage.getProductReviewGDPRLabel(page);
      expect(labelProductReviewGDPRLabel).to.be.equals(messageProductComments);
    });

    it('should close the product review modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeProductReviewModal', baseContext);

      const isModalVisible = await foClassicProductPage.closeProductReviewModal(page);
      expect(isModalVisible).to.be.equals(false);
    });

    it('should disable the consent message for the ProductComments form', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setProductCommentsStatusFalse', baseContext);

      page = await foClassicCreateAccountPage.changePage(browserContext, 0);
      await modPsGdprBoTabDataConsent.setProductCommentsStatus(page, false);

      const successMessage = await modPsGdprBoTabDataConsent.saveForm(page);
      expect(successMessage).to.be.contains(modPsGdprBoTabDataConsent.saveFormMessage);
    });

    it('should check on the Product Review modal that the GDPR Label is hidden', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'hasProductReviewGDPRLabelFalse', baseContext);

      page = await modPsGdprBoTabDataConsent.changePage(browserContext, 1);
      await foClassicProductPage.reloadPage(page);
      await foClassicProductPage.clickAddReviewButton(page);

      const hasProductReviewGDPRLabel = await foClassicProductPage.hasProductReviewGDPRLabel(page);
      expect(hasProductReviewGDPRLabel).to.be.equals(false);
    });

    it('should close the product review modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeProductReviewModal2', baseContext);

      const isModalVisible = await foClassicProductPage.closeProductReviewModal(page);
      expect(isModalVisible).to.be.equals(false);
    });

    it('should edit the consent message for the Contact Form form', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setContactFormMessage', baseContext);

      page = await foClassicCreateAccountPage.changePage(browserContext, 0);
      await modPsGdprBoTabDataConsent.setContactFormMessage(page, messageContactForm);

      const successMessage = await modPsGdprBoTabDataConsent.saveForm(page);
      expect(successMessage).to.be.contains(modPsGdprBoTabDataConsent.saveFormMessage);
    });

    it('should check on Contact Form the GDPR Label', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkContactFormGDPRLabel', baseContext);

      page = await modPsGdprBoTabDataConsent.changePage(browserContext, 1);
      await foClassicProductPage.goToFooterLink(page, 'Contact us');

      const pageTitle = await foClassicContactUsPage.getPageTitle(page);
      expect(pageTitle).to.equal(foClassicContactUsPage.pageTitle);

      const hasGDPRLabel = await foClassicContactUsPage.hasGDPRLabel(page);
      expect(hasGDPRLabel).to.equal(true);

      const gdprLabel = await foClassicContactUsPage.getGDPRLabel(page);
      expect(gdprLabel).to.equal(messageContactForm);
    });

    it('should disable consent message on Contact Form', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setContactFormStatusFalse', baseContext);

      page = await foClassicCreateAccountPage.changePage(browserContext, 0);
      await modPsGdprBoTabDataConsent.setContactFormStatus(page, false);

      const successMessage = await modPsGdprBoTabDataConsent.saveForm(page);
      expect(successMessage).to.be.contains(modPsGdprBoTabDataConsent.saveFormMessage);
    });

    it('should check on the Contact Form that the GDPR Label is hidden', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'hasGDPRLabelFalse', baseContext);

      page = await modPsGdprBoTabDataConsent.changePage(browserContext, 1);
      await foClassicContactUsPage.reloadPage(page);

      const hasGDPRLabel = await foClassicContactUsPage.hasGDPRLabel(page);
      expect(hasGDPRLabel).to.equal(false);
    });

    it('should edit the consent message for the Mail Alerts form', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setMailAlertsMessage', baseContext);

      page = await foClassicCreateAccountPage.changePage(browserContext, 0);
      await modPsGdprBoTabDataConsent.setMailAlertsMessage(page, messageMailAlerts);

      const successMessage = await modPsGdprBoTabDataConsent.saveForm(page);
      expect(successMessage).to.be.contains(modPsGdprBoTabDataConsent.saveFormMessage);
    });

    it('should check on the MailAlerts Form the GDPR Label', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkMailAlertsFormGDPRLabel', baseContext);

      page = await modPsGdprBoTabDataConsent.changePage(browserContext, 1);
      await foClassicContactUsPage.searchProduct(page, productOutOfStock.name);

      const pageTitleSearchResults = await foClassicSearchResultsPage.getPageTitle(page);
      expect(pageTitleSearchResults).to.equal(foClassicSearchResultsPage.pageTitle);

      await foClassicSearchResultsPage.goToProductPage(page, 1);

      const pageTitleFoProduct = await foClassicProductPage.getPageTitle(page);
      expect(pageTitleFoProduct).to.contains(productOutOfStock.name);

      const availabilityLabel = await foClassicProductPage.getProductAvailabilityLabel(page);
      expect(availabilityLabel).to.contains('Out-of-Stock');

      const hasBlockMailAlert = await foClassicProductPage.hasBlockMailAlert(page);
      expect(hasBlockMailAlert).to.be.equal(true);

      const hasBlockMailAlertGDPRLabel = await foClassicProductPage.hasBlockMailAlertGDPRLabel(page);
      expect(hasBlockMailAlertGDPRLabel).to.be.equal(true);

      const gdprLabel = await foClassicProductPage.getBlockMailAlertGDPRLabel(page);
      expect(gdprLabel).to.be.equal(messageMailAlerts);
    });

    it('should edit the consent message for the Mail Alerts form in French', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setMailAlertsMessageFR', baseContext);

      page = await foClassicCreateAccountPage.changePage(browserContext, 0);
      await modPsGdprBoTabDataConsent.setMailAlertsMessage(page, messageMailAlertsFR, dataLanguages.french.id);

      const successMessage = await modPsGdprBoTabDataConsent.saveForm(page);
      expect(successMessage).to.be.contains(modPsGdprBoTabDataConsent.saveFormMessage);
    });

    it('should check on the MailAlerts Form the GDPR Label in French', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkMailAlertsFormGDPRLabelFR', baseContext);

      page = await modPsGdprBoTabDataConsent.changePage(browserContext, 1);
      await foClassicProductPage.reloadPage(page);
      await foClassicProductPage.changeLanguage(page, 'fr');

      const pageTitle = await foClassicProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(productOutOfStock.nameFR);

      const availabilityLabel = await foClassicProductPage.getProductAvailabilityLabel(page);
      expect(availabilityLabel).to.contains('Rupture de stock');

      const hasBlockMailAlert = await foClassicProductPage.hasBlockMailAlert(page);
      expect(hasBlockMailAlert).to.be.equal(true);

      const hasBlockMailAlertGDPRLabel = await foClassicProductPage.hasBlockMailAlertGDPRLabel(page);
      expect(hasBlockMailAlertGDPRLabel).to.be.equal(true);

      const gdprLabel = await foClassicProductPage.getBlockMailAlertGDPRLabel(page);
      expect(gdprLabel).to.be.equal(messageMailAlertsFR);
    });

    it('should disable consent message on Mail Alerts Form', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setMailAlertsStatusFalse', baseContext);

      page = await foClassicCreateAccountPage.changePage(browserContext, 0);
      await modPsGdprBoTabDataConsent.setMailAlertsStatus(page, false);

      const successMessage = await modPsGdprBoTabDataConsent.saveForm(page);
      expect(successMessage).to.be.contains(modPsGdprBoTabDataConsent.saveFormMessage);
    });

    it('should check on the MailAlerts Form the GDPR Label is hidden', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkMailAlertsFormGDPRLabelHidden', baseContext);

      page = await modPsGdprBoTabDataConsent.changePage(browserContext, 1);
      await foClassicProductPage.reloadPage(page);

      const hasBlockMailAlert = await foClassicProductPage.hasBlockMailAlert(page);
      expect(hasBlockMailAlert).to.be.equal(true);

      const hasBlockMailAlertGDPRLabel = await foClassicProductPage.hasBlockMailAlertGDPRLabel(page);
      expect(hasBlockMailAlertGDPRLabel).to.be.equal(false);
    });
  });

  deleteProductTest(productOutOfStock, `${baseContext}_postTest_0`);

  resetModule(dataModules.psGdpr, `${baseContext}_postTest_1`);

  resetModule(dataModules.psEmailSubscription, `${baseContext}_postTest_2`);
});
