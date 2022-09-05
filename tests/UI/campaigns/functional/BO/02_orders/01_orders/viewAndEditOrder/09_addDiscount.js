require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const testContext = require('@utils/testContext');

// Import common tests
const loginCommon = require('@commonTests/BO/loginBO');
const {createOrderByCustomerTest} = require('@commonTests/FO/createOrder');
const {bulkDeleteCartRuleTest} = require('@commonTests/BO/catalog/createDeleteCartRule');

// Import BO pages
const dashboardPage = require('@pages/BO/dashboard');
const ordersPage = require('@pages/BO/orders');
const orderPageProductsBlock = require('@pages/BO/orders/view/productsBlock');
const orderPageTabListBlock = require('@pages/BO/orders/view/tabListBlock');
const cartRulesPage = require('@pages/BO/catalog/discounts');

// Import demo data
const {DefaultCustomer} = require('@data/demo/customer');
const {PaymentMethods} = require('@data/demo/paymentMethods');
const {Carriers} = require('@data/demo/carriers');

const baseContext = 'functional_BO_orders_orders_viewAndEditOrder_addDiscount';

let browserContext;
let page;
let totalOrder = 0;

// New order by customer data
const orderByCustomerData = {
  customer: DefaultCustomer,
  product: 1,
  productQuantity: 1,
  paymentMethod: PaymentMethods.wirePayment.moduleName,
};
// Discount data invalid value
const discountDataInvalidValue = {
  name: 'Test discount',
  type: 'Percent',
  value: '10%',
};
// Discount percent superior to 100
const discountPercentSup100Value = {
  name: 'Test discount',
  type: 'Percent',
  value: 500,
};
// Discount percent inferior to 0
const discountPercentInf0Value = {
  name: 'Test discount',
  type: 'Percent',
  value: -2,
};
// Discount percent good value
const discountPercentGoodValue = {
  name: 'Test discount percent',
  type: 'Percent',
  value: 50,
};
// Discount amount invalid value
const discountAmountTextValue = {
  name: 'Test discount',
  type: 'Amount',
  value: '10 euro',
};
// Discount amount negative value
const discountAmountNegativeValue = {
  name: 'Test discount',
  type: 'Amount',
  value: -10,
};
// Discount amount greater than total
const discountAmountGreaterThanTotal = {
  name: 'Test discount',
  type: 'Amount',
  value: 1000,
};
// Discount amount good value
const discountAmountGoodValue = {
  name: 'Test discount amount',
  type: 'Amount',
  value: 10.55,
};

const numberOfCartRules = 0;

// Discount amount good value
const discountFreeShipping = {
  name: 'Test discount free shipping',
  type: 'Free shipping',
};

const shippingDetailsData = {
  trackingNumber: '0523698',
  carrier: Carriers.myCarrier.name,
  carrierID: Carriers.myCarrier.id,
  shippingCost: '€8.40',
};
/*
Pre-condition :
- Create order by default customer
Scenario :
- Create different cart rules :
  - Incorrect percent value (text)
  - Percent value > 100
  - Percent value < 0
  - Good percent value
  - Incorrect amount value (text)
  - Amount value < 0
  - Amount value > total
  - Good amount value
  - Free shipping
- Check discount table, discounts and total ordered on each created cart rule
- Check created cart rules on discount page
Post-condition
- Delete created cart rules
 */

describe('BO - Orders - View and edit order : Add discount', async () => {
  // Pre-condition - Create order by default customer
  createOrderByCustomerTest(orderByCustomerData, baseContext);

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

  // 1 - Go to view order page
  describe('Go to view order page', async () => {
    it('should go to \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.ordersParentLink,
        dashboardPage.ordersLink,
      );

      await ordersPage.closeSfToolBar(page);

      const pageTitle = await ordersPage.getPageTitle(page);
      await expect(pageTitle).to.contains(ordersPage.pageTitle);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetOrderTable1', baseContext);

      const numberOfOrders = await ordersPage.resetAndGetNumberOfLines(page);
      await expect(numberOfOrders, 'Number of orders is not correct!').to.be.above(0);
    });

    it(`should filter the Orders table by 'Customer: ${DefaultCustomer.lastName}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByCustomer1', baseContext);

      await ordersPage.filterOrders(page, 'input', 'customer', DefaultCustomer.lastName);

      const textColumn = await ordersPage.getTextColumn(page, 'customer', 1);
      await expect(textColumn, 'Lastname is not correct').to.contains(DefaultCustomer.lastName);
    });

    it('should view the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'orderPageProductsBlock1', baseContext);

      await ordersPage.goToOrder(page, 1);

      const pageTitle = await orderPageProductsBlock.getPageTitle(page);
      await expect(pageTitle, 'Error when view order page!').to.contains(orderPageProductsBlock.pageTitle);
    });

    it('should get the total to pay', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getTotalToPay', baseContext);

      totalOrder = await orderPageProductsBlock.getOrderTotalPrice(page);
      await expect(totalOrder).is.not.equal(0);
    });
  });

  // 2 - Create discount
  describe('Create discount and check it', async () => {
    it('should add a discount with invalid value and check the error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addDiscountInvalidValue', baseContext);

      const errorMessage = await orderPageProductsBlock.addDiscount(page, discountDataInvalidValue);
      await expect(errorMessage, 'Error message is not correct!')
        .to.equal(orderPageProductsBlock.discountMustBeNumberErrorMessage);
    });

    it('should add a discount percent > 100 and check the error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addDiscountPercentSup100', baseContext);

      const errorMessage = await orderPageProductsBlock.addDiscount(page, discountPercentSup100Value);
      await expect(errorMessage, 'Error message is not correct!')
        .to.equal(orderPageProductsBlock.invalidPercentValueErrorMessage);
    });

    it('should add a discount percent < 0 and check the error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addDiscountPercentInf0', baseContext);

      const errorMessage = await orderPageProductsBlock.addDiscount(page, discountPercentInf0Value);
      await expect(errorMessage, 'Error message is not correct!')
        .to.equal(orderPageProductsBlock.percentValueNotPositiveErrorMessage);
    });

    it('should add discount percent and check successful message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addDiscountPercentGoodValue', baseContext);

      const errorMessage = await orderPageProductsBlock.addDiscount(page, discountPercentGoodValue);
      await expect(errorMessage, 'Validation message is not correct!')
        .to.equal(orderPageProductsBlock.successfulUpdateMessage);
    });

    it('should check the existence of new discount table', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNewDiscountTable', baseContext);

      const isVisible = await orderPageProductsBlock.isDiscountListTableVisible(page);
      await expect(isVisible, 'Discount list table is not visible').to.be.true;
    });

    it('should check the discount name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDiscountName', baseContext);

      const discountName = await orderPageProductsBlock.getTextColumnFromDiscountTable(page, 'name');
      await expect(discountName).to.be.equal(discountPercentGoodValue.name);
    });

    it('should check the discount value', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDiscountValue', baseContext);

      const discountValue = await orderPageProductsBlock.getTextColumnFromDiscountTable(page, 'value');
      await expect(discountValue).to.equal(
        `- €${totalOrder - (totalOrder * discountPercentGoodValue.value) / 100}`,
      );
    });

    it('should check total after discount', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkTotalAfterDiscount', baseContext);

      const totalAfterDiscount = await orderPageProductsBlock.getOrderTotalPrice(page);
      await expect(totalAfterDiscount)
        .to.be.equal(totalOrder - (totalOrder * discountPercentGoodValue.value) / 100);
    });

    it('should check the total discounts value', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkTotalDiscountValue', baseContext);

      const discountValue = await orderPageProductsBlock.getOrderTotalDiscounts(page);
      await expect(discountValue).to.equal((totalOrder * discountPercentGoodValue.value) / 100 - totalOrder);
    });

    it('should delete the discount', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteDiscount1', baseContext);

      const validationMessage = await orderPageProductsBlock.deleteDiscount(page);
      await expect(validationMessage, 'Successful update alert is not correct')
        .to.equal(orderPageProductsBlock.successfulUpdateMessage);
    });

    it('should add a discount amount invalid value and check error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addDiscountAmountInvalidValue', baseContext);

      const errorMessage = await orderPageProductsBlock.addDiscount(page, discountAmountTextValue);
      await expect(errorMessage, 'Error message is not correct!')
        .to.equal(orderPageProductsBlock.discountMustBeNumberErrorMessage);
    });

    it('should add a discount amount negative value and check error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addDiscountAmountNegativeValue', baseContext);

      const errorMessage = await orderPageProductsBlock.addDiscount(page, discountAmountNegativeValue);
      await expect(errorMessage, 'Error message is not correct!').to.equal('Amount value must be greater than 0.');
    });

    it('should add a discount amount greater than the total and check error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addDiscountAmountGreaterThanTotal', baseContext);

      const errorMessage = await orderPageProductsBlock.addDiscount(page, discountAmountGreaterThanTotal);
      await expect(errorMessage, 'Error message is not correct!')
        .to.equal(orderPageProductsBlock.discountCannotExceedTotalErrorMessage);
    });

    it('should add a good discount amount and check validation message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addCorrectDiscountAmount', baseContext);

      const errorMessage = await orderPageProductsBlock.addDiscount(page, discountAmountGoodValue);
      await expect(errorMessage, 'Validation message is not correct!')
        .to.equal(orderPageProductsBlock.successfulUpdateMessage);
    });

    it('should check the existence of new discount table', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNewDiscountTable2', baseContext);

      const isVisible = await orderPageProductsBlock.isDiscountListTableVisible(page);
      await expect(isVisible, 'Discount list table is not visible').to.be.true;
    });

    it('should check the discount name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDiscountName2', baseContext);

      const discountName = await orderPageProductsBlock.getTextColumnFromDiscountTable(page, 'name');
      await expect(discountName).to.be.equal(discountAmountGoodValue.name);
    });

    it('should check the discount value', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDiscountValue2', baseContext);

      const discountValue = await orderPageProductsBlock.getTextColumnFromDiscountTable(page, 'value');
      await expect(discountValue).to.equal(
        `- €${discountAmountGoodValue.value}`,
      );
    });

    it('should check total after discount', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkTotalAfterDiscount2', baseContext);

      const totalAfterDiscount = await orderPageProductsBlock.getOrderTotalPrice(page);
      await expect(totalAfterDiscount).to.be.equal(totalOrder - discountAmountGoodValue.value);
    });

    it('should check the total discounts value', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkTotalDiscountValue2', baseContext);

      const discountValue = await orderPageProductsBlock.getOrderTotalDiscounts(page);
      await expect(discountValue).to.equal(discountAmountGoodValue.value * -1);
    });
  });

  // 3 - Check cart rules created
  describe('Check created cart rules on \'Catalog > Discount\' page', async () => {
    it('should go to \'Catalog > Discounts\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToDiscountsPage2', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.catalogParentLink,
        dashboardPage.discountsLink,
      );

      const pageTitle = await cartRulesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(cartRulesPage.pageTitle);
    });
    [
      {
        args: {
          testIdentifier: 'filterFirstDiscount',
          filterType: 'input',
          filterBy: 'name',
          filterValue: discountAmountGoodValue.name,
        },
      },
      {
        args: {
          testIdentifier: 'filterSecondDiscount',
          filterType: 'input',
          filterBy: 'name',
          filterValue: discountPercentGoodValue.name,
        },
      },
    ].forEach((test) => {
      it(`should filter by ${test.args.filterBy} '${test.args.filterValue}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        await cartRulesPage.filterCartRules(
          page,
          test.args.filterType,
          test.args.filterBy,
          test.args.filterValue,
        );

        const numberOfCartRulesAfterFilter = await cartRulesPage.getNumberOfElementInGrid(page);
        await expect(numberOfCartRulesAfterFilter).to.be.equal(1);

        for (let row = 1; row <= numberOfCartRulesAfterFilter; row++) {
          const textColumn = await cartRulesPage.getTextColumn(page, row, test.args.filterBy);
          await expect(textColumn).to.contains(test.args.filterValue);
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);

        const numberOfCartRulesAfterReset = await cartRulesPage.resetAndGetNumberOfLines(page);
        await expect(numberOfCartRulesAfterReset).to.equal(numberOfCartRules + 2);
      });
    });
  });

  // 4 - Go back to view order page
  describe('Go back to view order page and delete discount', async () => {
    it('should go to \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage2', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.ordersParentLink,
        dashboardPage.ordersLink,
      );

      await ordersPage.closeSfToolBar(page);

      const pageTitle = await ordersPage.getPageTitle(page);
      await expect(pageTitle).to.contains(ordersPage.pageTitle);
    });

    it(`should filter the Orders table by 'Customer: ${DefaultCustomer.lastName}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByCustomer2', baseContext);

      await ordersPage.filterOrders(page, 'input', 'customer', DefaultCustomer.lastName);

      const textColumn = await ordersPage.getTextColumn(page, 'customer', 1);
      await expect(textColumn, 'Lastname is not correct').to.contains(DefaultCustomer.lastName);
    });

    it('should view the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'orderPageProductsBlock2', baseContext);

      await ordersPage.goToOrder(page, 1);

      const pageTitle = await orderPageProductsBlock.getPageTitle(page);
      await expect(pageTitle, 'Error when view order page!').to.contains(orderPageProductsBlock.pageTitle);
    });

    it('should delete the created discount', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteDiscount2', baseContext);

      const validationMessage = await orderPageProductsBlock.deleteDiscount(page);
      await expect(validationMessage, 'Successful update alert is not correct')
        .to.equal(orderPageProductsBlock.successfulUpdateMessage);
    });
  });

  // 5 - Create discount
  describe('Create free shipping cart rule and check it', async () => {
    it('should add cart rule free shipping and check validation message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addDiscountFreeShipping', baseContext);

      const errorMessage = await orderPageProductsBlock.addDiscount(page, discountFreeShipping);
      await expect(errorMessage, 'Error message is not correct!')
        .to.equal(orderPageProductsBlock.successfulUpdateMessage);
    });

    it('should check the existence of new discount table', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkFreeShippingDiscountTable', baseContext);

      const isVisible = await orderPageProductsBlock.isDiscountListTableVisible(page);
      await expect(isVisible, 'Discount list table is not visible').to.be.true;
    });

    it('should check the discount name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkFreeShippingDiscountName', baseContext);

      const discountName = await orderPageProductsBlock.getTextColumnFromDiscountTable(page, 'name');
      await expect(discountName).to.be.equal(discountFreeShipping.name);
    });

    it('should check the discount value', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkFreeShippingDiscountValue', baseContext);

      const discountValue = await orderPageProductsBlock.getTextColumnFromDiscountTable(page, 'value');
      await expect(discountValue).to.equal('€0.00');
    });

    it('should click on \'Carriers\' tab', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'displayCarriersTab', baseContext);

      const isTabOpened = await orderPageTabListBlock.goToCarriersTab(page);
      await expect(isTabOpened).to.be.true;
    });

    it('should click on \'Edit\' link and check the modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnEditLink', baseContext);

      const isModalVisible = await orderPageTabListBlock.clickOnEditLink(page);
      await expect(isModalVisible, 'Edit shipping modal is not visible!').to.be.true;
    });

    it('should update the carrier and add a tracking number', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateCarrier', baseContext);

      const textResult = await orderPageTabListBlock.setShippingDetails(page, shippingDetailsData);
      await expect(textResult).to.equal(orderPageTabListBlock.successfulUpdateMessage);
    });

    it('should check that the discount value is changed', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNewCarrierDiscountValue', baseContext);

      const discountValue = await orderPageProductsBlock.getTextColumnFromDiscountTable(page, 'value');
      await expect(discountValue).to.equal(`- ${shippingDetailsData.shippingCost}`);
    });

    it('should check total after discount', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkTotalAfterDiscount3', baseContext);

      const totalAfterDiscount = await orderPageProductsBlock.getOrderTotalPrice(page);
      await expect(totalAfterDiscount).to.be.equal(totalOrder);
    });

    it('should check the total discounts value', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkTotalDiscountValue3', baseContext);

      const discountValue = await orderPageProductsBlock.getOrderTotalDiscounts(page);
      await expect(discountValue).to.equal(-8.40);
    });

    it('should delete the created discount', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteFreeShippingDiscount', baseContext);

      const validationMessage = await orderPageProductsBlock.deleteDiscount(page);
      await expect(validationMessage, 'Successful update alert is not correct')
        .to.equal(orderPageProductsBlock.successfulUpdateMessage);
    });
  });

  // Post-Condition - Bulk delete cart rules
  bulkDeleteCartRuleTest(baseContext);
});
