require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const testContext = require('@utils/testContext');

// Import login steps
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const contactPage = require('@pages/BO/shopParameters/contact');
const storesPage = require('@pages/BO/shopParameters/stores');
const foHomePage = require('@pages/FO/home');

// Import data
const StoreFaker = require('@data/faker/store');
const {stores} = require('@data/demo/stores');

const baseContext = 'functional_BO_shopParameters_contact_store_contactDetails';

// Browser and tab
let browserContext;
let page;

const storesContactToCreate = new StoreFaker();

describe('BO - Shop Parameters - Contact : Configure contact details', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  it('should login in BO', async function () {
    await loginCommon.loginBO(this, page);
  });

  it('should go to \'Shop Parameters > Contact\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToContactPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.shopParametersParentLink,
      dashboardPage.contactLink,
    );

    await contactPage.closeSfToolBar(page);

    const pageTitle = await contactPage.getPageTitle(page);
    await expect(pageTitle).to.contains(contactPage.pageTitle);
  });

  it('should go to stores page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToStoresPage', baseContext);

    await contactPage.goToStoresPage(page);

    const pageTitle = await storesPage.getPageTitle(page);
    await expect(pageTitle).to.contains(storesPage.pageTitle);
  });

  it('should fill contact details form', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'configureContactDetails', baseContext);

    const textResult = await storesPage.setContactDetails(page, storesContactToCreate);
    await expect(textResult).to.contains(storesPage.contactFormSuccessfulUpdateMessage);
  });

  it('should view my shop', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop', baseContext);

    // View my shop and init pages
    page = await storesPage.viewMyShop(page);

    await foHomePage.changeLanguage(page, 'en');
    const isHomePage = await foHomePage.isHomePage(page);
    await expect(isHomePage, 'Fail to open FO home page').to.be.true;
  });

  it('should check contact details in FO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkContactDetailsInFO', baseContext);

    const storeInformation = await foHomePage.getStoreInformation(page);
    await expect(storeInformation).to.contains(storesContactToCreate.name);
    await expect(storeInformation).to.contains(storesContactToCreate.email);
    await expect(storeInformation).to.not.contains(storesContactToCreate.registrationNumber);
    await expect(storeInformation).to.contains(storesContactToCreate.address1);
    await expect(storeInformation).to.contains(storesContactToCreate.address2);
    await expect(storeInformation).to.contains(storesContactToCreate.postcode);
    await expect(storeInformation).to.contains(storesContactToCreate.city);
    await expect(storeInformation).to.contains(storesContactToCreate.country);
    await expect(storeInformation).to.contains(storesContactToCreate.phone);
    await expect(storeInformation).to.contains(storesContactToCreate.fax);
  });

  it('should go back to BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goBackToBo', baseContext);

    // Close tab and init other page objects with new current tab
    page = await foHomePage.closePage(browserContext, page, 0);

    const pageTitle = await storesPage.getPageTitle(page);
    await expect(pageTitle).to.contains(storesPage.pageTitle);
  });

  it('should back to default contact details information', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'backToDefaultInformation', baseContext);

    const textResult = await storesPage.setContactDetails(page, stores.contact);
    await expect(textResult).to.contains(storesPage.contactFormSuccessfulUpdateMessage);
  });
});
