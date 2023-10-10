// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';
import {createCartRuleTest} from '@commonTests/BO/catalog/cartRule';

// Import pages
import customersPage from '@pages/BO/customers';
import dashboardPage from '@pages/BO/dashboard';
import viewCustomerPage from '@pages/BO/customers/view';
import editRulesPage from '@pages/BO/catalog/discounts/add';

// Import data
import Customers from '@data/demo/customers';
import CartRuleData from '@data/faker/cartRule';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_customers_customers_viewEditVoucher';

describe('BO - Customers - Customers : View/edit voucher', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // Data to create cart rule
  const newCartRuleData: CartRuleData = new CartRuleData({
    name: 'reduction',
    customer: Customers.johnDoe,
    discountType: 'Amount',
    discountAmount: {
      value: 20,
      currency: 'EUR',
      tax: 'Tax included',
    },
  });

  const editCartRuleData: CartRuleData = new CartRuleData({
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
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('View voucher', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Customers > Customers\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCustomersPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.customersParentLink,
        dashboardPage.customersLink,
      );
      await customersPage.closeSfToolBar(page);

      const pageTitle = await customersPage.getPageTitle(page);
      expect(pageTitle).to.contains(customersPage.pageTitle);
    });

    it(`should filter list by email '${Customers.johnDoe.email}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToViewCreatedCustomer', baseContext);

      await customersPage.resetFilter(page);
      await customersPage.filterCustomers(page, 'input', 'email', Customers.johnDoe.email);

      const textEmail = await customersPage.getTextColumnFromTableCustomers(page, 1, 'email');
      expect(textEmail).to.contains(Customers.johnDoe.email);
    });

    it('should click on view customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToViewCustomerPageAfterCreateCustomer', baseContext);

      await customersPage.goToViewCustomerPage(page, 1);

      const pageTitle = await viewCustomerPage.getPageTitle(page);
      expect(pageTitle).to.contains(viewCustomerPage.pageTitle('J. DOE'));
    });

    it('should check vouchers number', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkVouchersNumber', baseContext);

      const cardHeaderText = await viewCustomerPage.getNumberOfElementFromTitle(page, 'Vouchers');
      expect(cardHeaderText).to.eq('1');
    });

    it('should check vouchers table', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkVouchersTable', baseContext);

      const vouchers = await viewCustomerPage.getTextFromElement(page, 'Vouchers');
      expect(vouchers).to.contains(`${newCartRuleData.name} check 1`);
    });
  });

  describe('Edit voucher', async () => {
    it('should click on edit voucher button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnEditVoucherButton', baseContext);

      await viewCustomerPage.goToPage(page, 'Vouchers');

      const pageTitle = await editRulesPage.getPageTitle(page);
      expect(pageTitle).to.contains(editRulesPage.pageTitle);
    });

    it('should update the created cart rule', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateCartRule', baseContext);

      await editRulesPage.fillInformationForm(page, editCartRuleData);

      const validationMessage = await editRulesPage.saveCartRule(page);
      expect(validationMessage).to.contains(viewCustomerPage.updateSuccessfulMessage);
    });

    it('should delete the created voucher', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteVoucher', baseContext);

      const successMessage = await viewCustomerPage.deleteVoucher(page, 1);
      expect(successMessage).to.contains(viewCustomerPage.successfulDeleteMessage);
    });

    it('should go to Customers page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCustomersPage2', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.customersParentLink,
        dashboardPage.customersLink,
      );

      const pageTitle = await customersPage.getPageTitle(page);
      expect(pageTitle).to.contains(customersPage.pageTitle);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilter', baseContext);

      await customersPage.resetFilter(page);

      const textEmail = await customersPage.getTextColumnFromTableCustomers(page, 1, 'email');
      expect(textEmail).to.contains(Customers.johnDoe.email);
    });
  });
});
