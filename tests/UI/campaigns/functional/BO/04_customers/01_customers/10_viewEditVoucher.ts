import testContext from '@utils/testContext';
import {expect} from 'chai';

// Import commonTests
import {createCartRuleTest} from '@commonTests/BO/catalog/cartRule';

import {
  boCartRulesCreatePage,
  boCustomersPage,
  boCustomersViewPage,
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  dataCustomers,
  FakerCartRule,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_customers_customers_viewEditVoucher';

describe('BO - Customers - Customers : View/edit voucher', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // Data to create cart rule
  const newCartRuleData: FakerCartRule = new FakerCartRule({
    name: 'reduction',
    customer: dataCustomers.johnDoe,
    discountType: 'Amount',
    discountAmount: {
      value: 20,
      currency: 'EUR',
      tax: 'Tax included',
    },
  });

  const editCartRuleData: FakerCartRule = new FakerCartRule({
    name: 'reduction',
    description: 'abkjbhvggfvfi',
    discountType: 'Amount',
    discountAmount: {
      value: 20,
      currency: 'EUR',
      tax: 'Tax included',
    },
  });

  // Pre-condition: Create cart rule
  createCartRuleTest(newCartRuleData, `${baseContext}_PreTest`);

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('View voucher', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
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
    });

    it(`should filter list by email '${dataCustomers.johnDoe.email}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToViewCreatedCustomer', baseContext);

      await boCustomersPage.resetFilter(page);
      await boCustomersPage.filterCustomers(page, 'input', 'email', dataCustomers.johnDoe.email);

      const textEmail = await boCustomersPage.getTextColumnFromTableCustomers(page, 1, 'email');
      expect(textEmail).to.contains(dataCustomers.johnDoe.email);
    });

    it('should click on view customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToViewCustomerPageAfterCreateCustomer', baseContext);

      await boCustomersPage.goToViewCustomerPage(page, 1);

      const pageTitle = await boCustomersViewPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCustomersViewPage.pageTitle('J. DOE'));
    });

    it('should check vouchers number', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkVouchersNumber', baseContext);

      const cardHeaderText = await boCustomersViewPage.getNumberOfElementFromTitle(page, 'Vouchers');
      expect(cardHeaderText).to.eq('1');
    });

    it('should check vouchers table', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkVouchersTable', baseContext);

      const vouchers = await boCustomersViewPage.getTextFromElement(page, 'Vouchers');
      expect(vouchers).to.contains(`${newCartRuleData.name} check 1`);
    });
  });

  describe('Edit voucher', async () => {
    it('should click on edit voucher button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnEditVoucherButton', baseContext);

      await boCustomersViewPage.goToPage(page, 'Vouchers');

      const pageTitle = await boCartRulesCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boCartRulesCreatePage.editPageTitle);
    });

    it('should update the created cart rule', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateCartRule', baseContext);

      await boCartRulesCreatePage.fillInformationForm(page, editCartRuleData);

      const validationMessage = await boCartRulesCreatePage.saveCartRule(page);
      expect(validationMessage).to.contains(boCustomersViewPage.updateSuccessfulMessage);
    });

    it('should delete the created voucher', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteVoucher', baseContext);

      const successMessage = await boCustomersViewPage.deleteVoucher(page, 1);
      expect(successMessage).to.contains(boCustomersViewPage.successfulDeleteMessage);
    });

    it('should go to Customers page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCustomersPage2', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.customersParentLink,
        boDashboardPage.customersLink,
      );

      const pageTitle = await boCustomersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCustomersPage.pageTitle);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilter', baseContext);

      await boCustomersPage.resetFilter(page);

      const numberOfCustomersAfterDelete = await boCustomersPage.resetAndGetNumberOfLines(page);
      expect(numberOfCustomersAfterDelete).to.be.at.least(0);
    });
  });
});
