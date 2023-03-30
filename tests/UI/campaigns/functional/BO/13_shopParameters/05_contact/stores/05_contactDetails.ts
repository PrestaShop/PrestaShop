// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import contactPage from '@pages/BO/shopParameters/contact';
import storesPage from '@pages/BO/shopParameters/stores';
import {homePage as foHomePage} from '@pages/FO/home';

// Import data
import Stores from '@data/demo/stores';
import StoreData from '@data/faker/store';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_shopParameters_contact_stores_contactDetails';

describe('BO - Shop Parameters - Contact : Configure contact details', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const storesContactToCreate: StoreData = new StoreData();

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

    const textResult = await storesPage.setContactDetails(page, Stores.contact);
    await expect(textResult).to.contains(storesPage.contactFormSuccessfulUpdateMessage);
  });
});
