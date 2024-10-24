// Import utils
import testContext from '@utils/testContext';

// Import pages
import contactPage from '@pages/BO/shopParameters/contact';
import storesPage from '@pages/BO/shopParameters/stores';

import {expect} from 'chai';
import {
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  dataStores,
  FakerStore,
  foClassicHomePage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_shopParameters_contact_stores_contactDetails';

describe('BO - Shop Parameters - Contact : Configure contact details', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const storesContactToCreate: FakerStore = new FakerStore();

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

  it('should go to \'Shop Parameters > Contact\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToContactPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.shopParametersParentLink,
      boDashboardPage.contactLink,
    );
    await contactPage.closeSfToolBar(page);

    const pageTitle = await contactPage.getPageTitle(page);
    expect(pageTitle).to.contains(contactPage.pageTitle);
  });

  it('should go to stores page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToStoresPage', baseContext);

    await contactPage.goToStoresPage(page);

    const pageTitle = await storesPage.getPageTitle(page);
    expect(pageTitle).to.contains(storesPage.pageTitle);
  });

  it('should fill contact details form', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'configureContactDetails', baseContext);

    const textResult = await storesPage.setContactDetails(page, storesContactToCreate);
    expect(textResult).to.contains(storesPage.contactFormSuccessfulUpdateMessage);
  });

  it('should view my shop', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop', baseContext);

    // View my shop and init pages
    page = await storesPage.viewMyShop(page);
    await foClassicHomePage.changeLanguage(page, 'en');

    const isHomePage = await foClassicHomePage.isHomePage(page);
    expect(isHomePage, 'Fail to open FO home page').to.eq(true);
  });

  it('should check contact details in FO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkContactDetailsInFO', baseContext);

    const storeInformation = await foClassicHomePage.getStoreInformation(page);
    expect(storeInformation).to.contains(storesContactToCreate.name);
    expect(storeInformation).to.contains(storesContactToCreate.email);
    expect(storeInformation).to.not.contains(storesContactToCreate.registrationNumber);
    expect(storeInformation).to.contains(storesContactToCreate.address1);
    expect(storeInformation).to.contains(storesContactToCreate.address2);
    expect(storeInformation).to.contains(storesContactToCreate.postcode);
    expect(storeInformation).to.contains(storesContactToCreate.city);
    expect(storeInformation).to.contains(storesContactToCreate.country);
    expect(storeInformation).to.contains(storesContactToCreate.phone);
    expect(storeInformation).to.contains(storesContactToCreate.fax);
  });

  it('should go back to BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goBackToBo', baseContext);

    // Close tab and init other page objects with new current tab
    page = await foClassicHomePage.closePage(browserContext, page, 0);

    const pageTitle = await storesPage.getPageTitle(page);
    expect(pageTitle).to.contains(storesPage.pageTitle);
  });

  it('should back to default contact details information', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'backToDefaultInformation', baseContext);

    const textResult = await storesPage.setContactDetails(page, dataStores.contact);
    expect(textResult).to.contains(storesPage.contactFormSuccessfulUpdateMessage);
  });
});
