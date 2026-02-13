import testContext from '@utils/testContext';
import {expect} from 'chai';
import {faker} from '@faker-js/faker';

// Import commonTests
import {createProductTest, deleteProductTest} from '@commonTests/BO/catalog/product';
import {resetModule} from '@commonTests/BO/modules/moduleManager';

import {
  boDashboardPage,
  boLoginPage,
  boModuleManagerPage,
  type BrowserContext,
  dataLanguages,
  dataModules,
  dataProducts,
  FakerCustomer,
  FakerProduct,
  foHummingbirdContactUsPage,
  foHummingbirdCreateAccountPage,
  foHummingbirdHomePage,
  foHummingbirdLoginPage,
  foHummingbirdMyAccountPage,
  foHummingbirdMyInformationsPage,
  foHummingbirdProductPage,
  foHummingbirdSearchResultsPage,
  modPsGdprBoMain,
  modPsGdprBoTabDataConsent,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'modules_psgdpr_configuration_consentCheckboxCustomization';

describe('GDPR : Consent checkbox customization', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  const messageBase: string = faker.lorem.sentence();
  const messageAccountCreation: string = `Account Creation - ${messageBase}`;
  const messageCustomerAccount: string = `Customer Account - ${messageBase}`;
  const messageNewsletter: string = `Newsletter - ${messageBase}`;
  const messageContactForm: string = `Contact Form - ${messageBase}`;
  const messageProductComments: string = `Product Comments - ${messageBase}`;
  const messageMailAlerts: string = `Mail Alerts EN - ${messageBase}`;
  const messageMailAlertsFR: string = `Mail Alerts FR - ${messageBase}`;
  const customerData: FakerCustomer = new FakerCustomer();
  const productOutOfStock: FakerProduct = new FakerProduct({
    quantity: 0,
  });

  createProductTest(productOutOfStock, `${baseContext}_preTest_0`);

  describe('Consent checkbox customization', async () => {
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

      const isHomePage = await foHummingbirdHomePage.isHomePage(page);
      expect(isHomePage).to.eq(true);
    });

    it('should click on the \'Sign in\' link', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnSignInLink', baseContext);

      // Check sign in link
      await foHummingbirdHomePage.clickOnHeaderLink(page, 'Sign in');

      const pageTitle = await foHummingbirdLoginPage.getPageTitle(page);
      expect(pageTitle).to.equal(foHummingbirdLoginPage.pageTitle);
    });

    it('should go to create account page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCreateAccountPage', baseContext);

      await foHummingbirdLoginPage.goToCreateAccountPage(page);

      const pageHeaderTitle = await foHummingbirdCreateAccountPage.getHeaderTitle(page);
      expect(pageHeaderTitle).to.equal(foHummingbirdCreateAccountPage.formTitle);

      const gdprLabel = await foHummingbirdCreateAccountPage.getGDPRLabel(page);
      expect(gdprLabel).to.contains(messageAccountCreation);
    });

    it('should create new account', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createNewAccount', baseContext);

      await foHummingbirdCreateAccountPage.createAccount(page, customerData);

      const isCustomerConnected = await foHummingbirdHomePage.isCustomerConnected(page);
      expect(isCustomerConnected).to.eq(true);
    });

    it('should edit the consent message for the Customer Account form', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editConsentMessageCustomerAccount', baseContext);

      page = await foHummingbirdCreateAccountPage.changePage(browserContext, 0);
      await modPsGdprBoTabDataConsent.setCustomerAccountMessage(page, messageCustomerAccount);

      const successMessage = await modPsGdprBoTabDataConsent.saveForm(page);
      expect(successMessage).to.be.contains(modPsGdprBoTabDataConsent.saveFormMessage);
    });

    it('should check on the Information page the GDPR checkbox', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkAccountIdentityPage', baseContext);

      page = await modPsGdprBoTabDataConsent.changePage(browserContext, 1);
      await foHummingbirdHomePage.goToMyAccountPage(page);
      await foHummingbirdMyAccountPage.goToInformationPage(page);

      const pageTitle = await foHummingbirdMyInformationsPage.getPageTitle(page);
      expect(pageTitle).to.equal(foHummingbirdMyInformationsPage.pageTitle);

      const gdprLabel = await foHummingbirdMyInformationsPage.getGDPRLabel(page);
      expect(gdprLabel).to.contains(messageCustomerAccount);
    });

    it('should disable consent message on creation and customer account', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'disableConsentMessageCreationCustomer', baseContext);

      page = await foHummingbirdCreateAccountPage.changePage(browserContext, 0);
      await modPsGdprBoTabDataConsent.setAccountCreationStatus(page, false);
      await modPsGdprBoTabDataConsent.setCustomerAccountStatus(page, false);

      const successMessage = await modPsGdprBoTabDataConsent.saveForm(page);
      expect(successMessage).to.be.contains(modPsGdprBoTabDataConsent.saveFormMessage);
    });

    it('should check on the Information page the GDPR checkbox is removed', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkAccountIdentityPageDisabled', baseContext);

      page = await modPsGdprBoTabDataConsent.changePage(browserContext, 1);
      await foHummingbirdMyInformationsPage.reloadPage(page);

      const hasGDPRLabel = await foHummingbirdMyInformationsPage.hasGDPRLabel(page);
      expect(hasGDPRLabel).to.equal(false);
    });

    it('should logout', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'foLogout', baseContext);

      await foHummingbirdMyInformationsPage.logout(page);

      const isCustomerConnected = await foHummingbirdLoginPage.isCustomerConnected(page);
      expect(isCustomerConnected).to.eq(false);

      const pageTitle = await foHummingbirdLoginPage.getPageTitle(page);
      expect(pageTitle).to.equal(foHummingbirdLoginPage.pageTitle);
    });

    it('should return to the "Create account" page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'returnToCreateAccountPage', baseContext);

      await foHummingbirdLoginPage.goToCreateAccountPage(page);

      const pageHeaderTitle = await foHummingbirdCreateAccountPage.getHeaderTitle(page);
      expect(pageHeaderTitle).to.equal(foHummingbirdCreateAccountPage.formTitle);

      const hasGDPRLabel = await foHummingbirdCreateAccountPage.hasGDPRLabel(page);
      expect(hasGDPRLabel).to.equal(false);
    });

    it('should edit the consent message for the Newsletter form', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editConsentMessageNewsletter', baseContext);

      page = await foHummingbirdCreateAccountPage.changePage(browserContext, 0);
      await modPsGdprBoTabDataConsent.setNewsletterMessage(page, messageNewsletter);

      const successMessage = await modPsGdprBoTabDataConsent.saveForm(page);
      expect(successMessage).to.be.contains(modPsGdprBoTabDataConsent.saveFormMessage);
    });

    it('should check on the Newsletter Block is not displayed', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNewsletterHomepageHidden', baseContext);

      page = await modPsGdprBoTabDataConsent.changePage(browserContext, 1);
      await foHummingbirdCreateAccountPage.goToHomePage(page);

      const isHomePage = await foHummingbirdHomePage.isHomePage(page);
      expect(isHomePage).to.eq(true);

      const hasSubscribeNewsletterRGPD = await foHummingbirdHomePage.hasSubscribeNewsletterRGPD(page);
      expect(hasSubscribeNewsletterRGPD).to.be.equals(true);

      const labelSubscribeNewsletterRGPD = await foHummingbirdHomePage.getSubscribeNewsletterRGPDLabel(page);
      expect(labelSubscribeNewsletterRGPD).to.be.equals(messageNewsletter);
    });

    it('should register to the newsletter', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'subscribeNewsletter', baseContext);

      const newsletterSubscribeAlertMessage = await foHummingbirdHomePage.subscribeToNewsletter(page, customerData.email);
      expect(newsletterSubscribeAlertMessage).to.contains(foHummingbirdHomePage.successSubscriptionMessage);
    });

    it('should go to \'Modules > Module Manager\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'returnToModuleManagerPage', baseContext);

      page = await foHummingbirdHomePage.changePage(browserContext, 0);

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

      page = await foHummingbirdCreateAccountPage.changePage(browserContext, 0);
      await modPsGdprBoTabDataConsent.setNewsletterStatus(page, false);

      const successMessage = await modPsGdprBoTabDataConsent.saveForm(page);
      expect(successMessage).to.be.contains(modPsGdprBoTabDataConsent.saveFormMessage);
    });

    it('should check on the FO the Subscribe Newsletter Form', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'hasSubscribeNewsletterRGPDFalse', baseContext);

      page = await modPsGdprBoTabDataConsent.changePage(browserContext, 1);
      await foHummingbirdHomePage.reloadPage(page);

      const hasSubscribeNewsletterRGPD = await foHummingbirdHomePage.hasSubscribeNewsletterRGPD(page);
      expect(hasSubscribeNewsletterRGPD).to.be.equals(false);
    });

    it('should edit the consent message for the Product Comments form', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setProductCommentsMessage', baseContext);

      page = await foHummingbirdCreateAccountPage.changePage(browserContext, 0);
      await modPsGdprBoTabDataConsent.setProductCommentsMessage(page, messageProductComments);

      const successMessage = await modPsGdprBoTabDataConsent.saveForm(page);
      expect(successMessage).to.be.contains(modPsGdprBoTabDataConsent.saveFormMessage);
    });

    it('should go to the FO and click on "Sign in" link', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFoAndClickSignIn', baseContext);

      page = await modPsGdprBoTabDataConsent.changePage(browserContext, 1);
      await foHummingbirdHomePage.clickOnHeaderLink(page, 'Sign in');

      const pageTitle = await foHummingbirdLoginPage.getPageTitle(page);
      expect(pageTitle).to.equal(foHummingbirdLoginPage.pageTitle);
    });

    it('should sign in by default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signInFO', baseContext);

      await foHummingbirdLoginPage.customerLogin(page, customerData);

      const isCustomerConnected = await foHummingbirdLoginPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is not connected!').to.eq(true);

      const isHomePage = await foHummingbirdHomePage.isHomePage(page);
      expect(isHomePage).to.eq(true);
    });

    it('should go to the product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductPage', baseContext);

      await foHummingbirdHomePage.goToProductPage(page, 1);

      const pageTitle = await foHummingbirdProductPage.getPageTitle(page);
      expect(pageTitle.toUpperCase()).to.contains(dataProducts.demo_1.name.toUpperCase());
    });

    it('should open the product review modal and check the GDPR label', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickAddReviewButton', baseContext);

      await foHummingbirdProductPage.clickAddReviewButton(page);

      const hasProductReviewGDPRLabel = await foHummingbirdProductPage.hasProductReviewGDPRLabel(page);
      expect(hasProductReviewGDPRLabel).to.be.equals(true);

      const labelProductReviewGDPRLabel = await foHummingbirdProductPage.getProductReviewGDPRLabel(page);
      expect(labelProductReviewGDPRLabel).to.be.equals(messageProductComments);
    });

    it('should close the product review modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeProductReviewModal', baseContext);

      const isModalVisible = await foHummingbirdProductPage.closeProductReviewModal(page);
      expect(isModalVisible).to.be.equals(false);
    });

    it('should disable the consent message for the ProductComments form', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setProductCommentsStatusFalse', baseContext);

      page = await foHummingbirdCreateAccountPage.changePage(browserContext, 0);
      await modPsGdprBoTabDataConsent.setProductCommentsStatus(page, false);

      const successMessage = await modPsGdprBoTabDataConsent.saveForm(page);
      expect(successMessage).to.be.contains(modPsGdprBoTabDataConsent.saveFormMessage);
    });

    it('should check on the Product Review modal that the GDPR Label is hidden', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'hasProductReviewGDPRLabelFalse', baseContext);

      page = await modPsGdprBoTabDataConsent.changePage(browserContext, 1);
      await foHummingbirdProductPage.reloadPage(page);
      await foHummingbirdProductPage.clickAddReviewButton(page);

      const hasProductReviewGDPRLabel = await foHummingbirdProductPage.hasProductReviewGDPRLabel(page);
      expect(hasProductReviewGDPRLabel).to.be.equals(false);
    });

    it('should close the product review modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeProductReviewModal2', baseContext);

      const isModalVisible = await foHummingbirdProductPage.closeProductReviewModal(page);
      expect(isModalVisible).to.be.equals(false);
    });

    it('should edit the consent message for the Contact Form form', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setContactFormMessage', baseContext);

      page = await foHummingbirdCreateAccountPage.changePage(browserContext, 0);
      await modPsGdprBoTabDataConsent.setContactFormMessage(page, messageContactForm);

      const successMessage = await modPsGdprBoTabDataConsent.saveForm(page);
      expect(successMessage).to.be.contains(modPsGdprBoTabDataConsent.saveFormMessage);
    });

    it('should check on Contact Form the GDPR Label', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkContactFormGDPRLabel', baseContext);

      page = await modPsGdprBoTabDataConsent.changePage(browserContext, 1);
      await foHummingbirdProductPage.goToFooterLink(page, 'Contact us');

      const pageTitle = await foHummingbirdContactUsPage.getPageTitle(page);
      expect(pageTitle).to.equal(foHummingbirdContactUsPage.pageTitle);

      const hasGDPRLabel = await foHummingbirdContactUsPage.hasGDPRLabel(page);
      expect(hasGDPRLabel).to.equal(true);

      const gdprLabel = await foHummingbirdContactUsPage.getGDPRLabel(page);
      expect(gdprLabel).to.equal(messageContactForm);
    });

    it('should disable consent message on Contact Form', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setContactFormStatusFalse', baseContext);

      page = await foHummingbirdCreateAccountPage.changePage(browserContext, 0);
      await modPsGdprBoTabDataConsent.setContactFormStatus(page, false);

      const successMessage = await modPsGdprBoTabDataConsent.saveForm(page);
      expect(successMessage).to.be.contains(modPsGdprBoTabDataConsent.saveFormMessage);
    });

    it('should check on the Contact Form that the GDPR Label is hidden', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'hasGDPRLabelFalse', baseContext);

      page = await modPsGdprBoTabDataConsent.changePage(browserContext, 1);
      await foHummingbirdContactUsPage.reloadPage(page);

      const hasGDPRLabel = await foHummingbirdContactUsPage.hasGDPRLabel(page);
      expect(hasGDPRLabel).to.equal(false);
    });

    it('should edit the consent message for the Mail Alerts form', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setMailAlertsMessage', baseContext);

      page = await foHummingbirdCreateAccountPage.changePage(browserContext, 0);
      await modPsGdprBoTabDataConsent.setMailAlertsMessage(page, messageMailAlerts);

      const successMessage = await modPsGdprBoTabDataConsent.saveForm(page);
      expect(successMessage).to.be.contains(modPsGdprBoTabDataConsent.saveFormMessage);
    });

    it('should check on the MailAlerts Form the GDPR Label', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkMailAlertsFormGDPRLabel', baseContext);

      page = await modPsGdprBoTabDataConsent.changePage(browserContext, 1);
      await foHummingbirdContactUsPage.searchProduct(page, productOutOfStock.name);

      const pageTitleSearchResults = await foHummingbirdSearchResultsPage.getPageTitle(page);
      expect(pageTitleSearchResults).to.equal(foHummingbirdSearchResultsPage.pageTitle);

      await foHummingbirdSearchResultsPage.goToProductPage(page, 1);

      const pageTitleFoProduct = await foHummingbirdProductPage.getPageTitle(page);
      expect(pageTitleFoProduct).to.contains(productOutOfStock.name);

      const availabilityLabel = await foHummingbirdProductPage.getProductAvailabilityLabel(page);
      expect(availabilityLabel).to.contains('Out-of-Stock');

      const hasBlockMailAlert = await foHummingbirdProductPage.hasBlockMailAlert(page);
      expect(hasBlockMailAlert).to.be.equal(true);

      const hasBlockMailAlertGDPRLabel = await foHummingbirdProductPage.hasBlockMailAlertGDPRLabel(page);
      expect(hasBlockMailAlertGDPRLabel).to.be.equal(true);

      const gdprLabel = await foHummingbirdProductPage.getBlockMailAlertGDPRLabel(page);
      expect(gdprLabel).to.be.equal(messageMailAlerts);
    });

    it('should edit the consent message for the Mail Alerts form in French', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setMailAlertsMessageFR', baseContext);

      page = await foHummingbirdCreateAccountPage.changePage(browserContext, 0);
      await modPsGdprBoTabDataConsent.setMailAlertsMessage(page, messageMailAlertsFR, dataLanguages.french.id);

      const successMessage = await modPsGdprBoTabDataConsent.saveForm(page);
      expect(successMessage).to.be.contains(modPsGdprBoTabDataConsent.saveFormMessage);
    });

    it('should check on the MailAlerts Form the GDPR Label in French', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkMailAlertsFormGDPRLabelFR', baseContext);

      page = await modPsGdprBoTabDataConsent.changePage(browserContext, 1);
      await foHummingbirdProductPage.reloadPage(page);
      await foHummingbirdProductPage.changeLanguage(page, 'fr');

      const pageTitle = await foHummingbirdProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(productOutOfStock.nameFR);

      const availabilityLabel = await foHummingbirdProductPage.getProductAvailabilityLabel(page);
      expect(availabilityLabel).to.contains('Rupture de stock');

      const hasBlockMailAlert = await foHummingbirdProductPage.hasBlockMailAlert(page);
      expect(hasBlockMailAlert).to.be.equal(true);

      const hasBlockMailAlertGDPRLabel = await foHummingbirdProductPage.hasBlockMailAlertGDPRLabel(page);
      expect(hasBlockMailAlertGDPRLabel).to.be.equal(true);

      const gdprLabel = await foHummingbirdProductPage.getBlockMailAlertGDPRLabel(page);
      expect(gdprLabel).to.be.equal(messageMailAlertsFR);
    });

    it('should disable consent message on Mail Alerts Form', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setMailAlertsStatusFalse', baseContext);

      page = await foHummingbirdCreateAccountPage.changePage(browserContext, 0);
      await modPsGdprBoTabDataConsent.setMailAlertsStatus(page, false);

      const successMessage = await modPsGdprBoTabDataConsent.saveForm(page);
      expect(successMessage).to.be.contains(modPsGdprBoTabDataConsent.saveFormMessage);
    });

    it('should check on the MailAlerts Form the GDPR Label is hidden', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkMailAlertsFormGDPRLabelHidden', baseContext);

      page = await modPsGdprBoTabDataConsent.changePage(browserContext, 1);
      await foHummingbirdProductPage.reloadPage(page);

      const hasBlockMailAlert = await foHummingbirdProductPage.hasBlockMailAlert(page);
      expect(hasBlockMailAlert).to.be.equal(true);

      const hasBlockMailAlertGDPRLabel = await foHummingbirdProductPage.hasBlockMailAlertGDPRLabel(page);
      expect(hasBlockMailAlertGDPRLabel).to.be.equal(false);
    });
  });

  deleteProductTest(productOutOfStock, `${baseContext}_postTest_0`);

  resetModule(dataModules.psGdpr, `${baseContext}_postTest_1`);

  resetModule(dataModules.psEmailSubscription, `${baseContext}_postTest_2`);
});
