import {expect} from 'chai';
import testContext from '@utils/testContext';

import {
  boAddressesPage,
  boAddressesCreatePage,
  boCustomersPage,
  boCustomersCreatePage,
  boCustomersViewPage,
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'audit_BO_customers';

describe('BO - Customers', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  before(async function () {
    utilsPlaywright.setErrorsCaptured(true);

    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  beforeEach(async () => {
    utilsPlaywright.resetJsErrors();
  });

  it('should login in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

    await boLoginPage.goTo(page, global.BO.URL);
    await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

    const pageTitle = await boDashboardPage.getPageTitle(page);
    expect(pageTitle).to.contains(boDashboardPage.pageTitle);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to \'Customers > Customers\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCustomersPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.customersParentLink,
      boDashboardPage.customersLink,
    );
    await boCustomersPage.closeSfToolBar(page);

    const pageTitle = await boCustomersPage.getPageTitle(page);
    expect(pageTitle).to.contains(boCustomersPage.pageTitle);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to \'Customers > Customers > View Customer\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCustomerViewPage', baseContext);

    await boCustomersPage.goToViewCustomerPage(page, 1);

    const pageTitle = await boCustomersViewPage.getPageTitle(page);
    expect(pageTitle).to.contains(boCustomersViewPage.pageTitle('J. DOE'));

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to \'Customers > Customers > Edit Customer\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCustomerEditPage', baseContext);

    await boCustomersViewPage.goToEditCustomerPage(page);

    const pageTitle = await boCustomersCreatePage.getPageTitle(page);
    expect(pageTitle).to.contains(boCustomersCreatePage.pageTitleEdit);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to \'Customers > Customers > New Customer\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToNewCustomerPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.customersParentLink,
      boDashboardPage.customersLink,
    );
    await boCustomersPage.goToAddNewCustomerPage(page);

    const pageTitle = await boCustomersCreatePage.getPageTitle(page);
    expect(pageTitle).to.contains(boCustomersCreatePage.pageTitleCreate);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to \'Customers > Addresses\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToAddressesPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.customersParentLink,
      boDashboardPage.addressesLink,
    );

    const pageTitle = await boAddressesPage.getPageTitle(page);
    expect(pageTitle).to.contains(boAddressesPage.pageTitle);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to \'Customers > Addresses > Edit Address\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToAddressEditPage', baseContext);

    await boAddressesPage.goToEditAddressPage(page, 1);

    const pageTitle = await boAddressesCreatePage.getPageTitle(page);
    expect(pageTitle).to.contains(boAddressesCreatePage.pageTitleEdit);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to \'Customers > Addresses > New Address\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToNewAddressPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.customersParentLink,
      boDashboardPage.addressesLink,
    );
    await boAddressesPage.goToAddNewAddressPage(page);

    const pageTitle = await boAddressesCreatePage.getPageTitle(page);
    expect(pageTitle).to.contains(boAddressesCreatePage.pageTitleCreate);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });
});
