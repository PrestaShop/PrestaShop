import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boAddressesPage,
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  dataCustomers,
  FakerAddress,
  foClassicHomePage,
  foClassicLoginPage,
  foClassicMyAccountPage,
  foClassicMyAddressesPage,
  foClassicMyAddressesCreatePage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_customers_addresses_setRequiredFields';

/*
Check 'Vat number' to be a required fields
Go to FO, new address page and verify that 'Vat number' is required
Uncheck 'Vat number'
Go to FO, new address page and verify that 'Vat number' is not required
 */
describe('BO - Customers - Addresses : Set required fields for addresses', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const addressDataWithVatNumber: FakerAddress = new FakerAddress({country: 'France', vatNumber: '0102030405'});
  const addressDataWithoutVatNumber: FakerAddress = new FakerAddress({country: 'France'});

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

  it('should go to \'Customers > Addresses\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToAddressesPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.customersParentLink,
      boDashboardPage.addressesLink,
    );
    await boAddressesPage.closeSfToolBar(page);

    const pageTitle = await boAddressesPage.getPageTitle(page);
    expect(pageTitle).to.contains(boAddressesPage.pageTitle);
  });

  [
    {args: {action: 'select', exist: true, addressData: addressDataWithVatNumber}},
    {args: {action: 'unselect', exist: false, addressData: addressDataWithoutVatNumber}},
  ].forEach((test, index: number) => {
    it(`should ${test.args.action} 'Vat number' as required fields`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}VatNumber`, baseContext);

      const textResult = await boAddressesPage.setRequiredFields(page, 6, test.args.exist);
      expect(textResult).to.equal(boAddressesPage.successfulUpdateMessage);
    });

    it('should view my shop and login', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `viewMyShop${index}`, baseContext);

      // View shop
      page = await boAddressesPage.viewMyShop(page);
      // Change language in FO
      await foClassicHomePage.changeLanguage(page, 'en');

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
    });

    it('should login in FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `loginFO${index}`, baseContext);

      // Go to create account page
      await foClassicHomePage.goToLoginPage(page);
      await foClassicLoginPage.customerLogin(page, dataCustomers.johnDoe);

      const connected = await foClassicHomePage.isCustomerConnected(page);
      expect(connected, 'Customer is not connected in FO').to.eq(true);
    });

    it('should go to \'Customers > Addresses\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goToFOAddressesPage${index}`, baseContext);

      await foClassicHomePage.goToMyAccountPage(page);
      await foClassicMyAccountPage.goToAddressesPage(page);

      const pageHeaderTitle = await foClassicMyAddressesPage.getPageTitle(page);
      expect(pageHeaderTitle).to.equal(foClassicMyAddressesPage.pageTitle);
    });

    it('should go to create address page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goToNewAddressPage${index}`, baseContext);

      await foClassicMyAddressesPage.openNewAddressForm(page);

      const pageHeaderTitle = await foClassicMyAddressesCreatePage.getHeaderTitle(page);
      expect(pageHeaderTitle).to.equal(foClassicMyAddressesCreatePage.creationFormTitle);
    });

    it('should check if \'Vat number\' is required', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `checkOptionalLabel${index}`, baseContext);

      const result = await foClassicMyAddressesCreatePage.isVatNumberRequired(page);
      expect(result).to.equal(test.args.exist);
    });

    it('should sign out from FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `signOutFO${index}`, baseContext);

      await foClassicMyAddressesCreatePage.logout(page);

      const isCustomerConnected = await foClassicMyAddressesCreatePage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is connected').to.eq(false);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goBackToBO${index}`, baseContext);

      // Go back to BO
      page = await foClassicMyAddressesCreatePage.closePage(browserContext, page, 0);

      const pageTitle = await boAddressesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boAddressesPage.pageTitle);
    });
  });
});
