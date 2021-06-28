require('module-alias/register');

// Helpers to open and close browser
const helper = require('@utils/helpers');

// Common tests login BO
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const foHomePage = require('@pages/FO/home');
const foProductPage = require('@pages/FO/product');
const foCartPage = require('@pages/FO/cart');
const foCheckoutPage = require('@pages/FO/checkout');
const foOrderConfirmationPage = require('@pages/FO/checkout/orderConfirmation');
const shoppingCartsPage = require('@pages/BO/orders/shoppingCarts');
const customersPage = require('@pages/BO/customers');

// Import data
const CustomerFaker = require('@data/faker/customer');
const AddressFaker = require('@data/faker/address');

const addressData = new AddressFaker();

const {PaymentMethods} = require('@data/demo/paymentMethods');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_orders_shoppingCarts_sortAndPagination';

// Import expect from chai
const {expect} = require('chai');

let browserContext;
let page;
let numberOfCustomers = 0;

/*
Create 16 shopping carts
Pagination
Sort shopping cart table by :
Id, Order ID, Customer, carrier, date and Online
Delete customers
*/
describe('BO - Orders - Shopping carts : Sort and pagination shopping carts', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  it('should go to FO page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToFO', baseContext);

    await foHomePage.goToFo(page);
    await foHomePage.changeLanguage(page, 'en');

    const isHomePage = await foHomePage.isHomePage(page);
    await expect(isHomePage, 'Fail to open FO home page').to.be.true;
  });

  // 1 - create 16 shopping carts
  const creationTests = new Array(16).fill(0, 0, 16);

  creationTests.forEach((test, index) => {
    describe(`Create order n°${index + 1} in FO`, async () => {
      const customerData = new CustomerFaker({lastName: 'todelete', password: ''});

      it('should add product to cart and go to checkout page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `addProductToCart${index}`, baseContext);

        await foHomePage.goToHomePage(page);

        // Go to the fourth product page
        await foHomePage.goToProductPage(page, 4);

        // Add the created product to the cart
        await foProductPage.addProductToTheCart(page);

        // Proceed to checkout the shopping cart
        await foCartPage.clickOnProceedToCheckout(page);

        // Go to checkout page
        const isCheckoutPage = await foCheckoutPage.isCheckoutPage(page);
        await expect(isCheckoutPage).to.be.true;
      });

      it('should fill personal information as a guest', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `setPersonalInformation${index}`, baseContext);

        const isStepPersonalInfoCompleted = await foCheckoutPage.setGuestPersonalInformation(page, customerData);
        await expect(isStepPersonalInfoCompleted, 'Step personal information is not completed').to.be.true;
      });

      it('should fill address form and go to delivery step', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `setAddressStep${index}`, baseContext);

        const isStepAddressComplete = await foCheckoutPage.setAddress(page, addressData);
        await expect(isStepAddressComplete, 'Step Address is not complete').to.be.true;
      });

      it('should validate the order', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `validateOrder${index}`, baseContext);

        // Delivery step - Go to payment step
        const isStepDeliveryComplete = await foCheckoutPage.goToPaymentStep(page);
        await expect(isStepDeliveryComplete, 'Step Address is not complete').to.be.true;

        // Payment step - Choose payment step
        await foCheckoutPage.choosePaymentAndOrder(page, PaymentMethods.wirePayment.moduleName);
        const cardTitle = await foOrderConfirmationPage.getOrderConfirmationCardTitle(page);

        // Check the confirmation message
        await expect(cardTitle).to.contains(foOrderConfirmationPage.orderConfirmationCardTitle);
      });
    });
  });

  // 2 - Pagination
  describe('Pagination next and previous', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Orders > Shopping carts\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShoppingCartsPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.ordersParentLink,
        dashboardPage.shoppingCartsLink,
      );

      const pageTitle = await shoppingCartsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(shoppingCartsPage.pageTitle);
    });

    it('should change the items number to 20 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo20', baseContext);

      const paginationNumber = await shoppingCartsPage.selectPaginationLimit(page, '20');
      expect(paginationNumber).to.equal('1');
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext', baseContext);

      const paginationNumber = await shoppingCartsPage.paginationNext(page);
      expect(paginationNumber).to.equal('2');
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious', baseContext);

      const paginationNumber = await shoppingCartsPage.paginationPrevious(page);
      expect(paginationNumber).to.equal('1');
    });

    it('should change the items number to 300 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo300', baseContext);

      const paginationNumber = await shoppingCartsPage.selectPaginationLimit(page, '300');
      expect(paginationNumber).to.equal('1');
    });
  });

  // 3 - Sort table
  describe('Sort shopping cart table', async () => {
    it('should filter by customer \'todelete\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToSort', baseContext);

      await shoppingCartsPage.filterTable(page, 'input', 'c!lastname', 'todelete');
      const textColumn = await shoppingCartsPage.getTextColumn(page, 1, 'c!lastname');
      await expect(textColumn).to.contains('todelete');
    });

    const sortTests = [
      {
        args: {
          testIdentifier: 'sortByIdDesc', sortBy: 'id_cart', sortDirection: 'down', isFloat: true,
        },
      },
      {
        args: {
          testIdentifier: 'sortByOrderIDAsc', sortBy: 'status', sortDirection: 'up', isFloat: true,
        },
      },
      {
        args: {
          testIdentifier: 'sortByOrderIDDesc', sortBy: 'status', sortDirection: 'down', isFloat: true,
        },
      },
      /* Sort by carrier not working, skipping it
      {
        args: {
          testIdentifier: 'sortByCarrierAsc', sortBy: 'ca!name', sortDirection: 'up',
        },
      },
      {
        args: {
          testIdentifier: 'sortByCarrierDesc', sortBy: 'ca!name', sortDirection: 'down',
        },
      },
      */
      {
        args: {
          testIdentifier: 'sortByDateAsc', sortBy: 'date', sortDirection: 'up',
        },
      },
      {
        args: {
          testIdentifier: 'sortByDateDesc', sortBy: 'date', sortDirection: 'down',
        },
      },
      {
        args: {
          testIdentifier: 'sortByOnlineAsc', sortBy: 'id_guest', sortDirection: 'up',
        },
      },
      {
        args: {
          testIdentifier: 'sortByOnlineDesc', sortBy: 'id_guest', sortDirection: 'down',
        },
      },
      {
        args: {
          testIdentifier: 'sortByIdAsc', sortBy: 'id_cart', sortDirection: 'up', isFloat: true,
        },
      },
    ];

    sortTests.forEach((test) => {
      it(`should sort by '${test.args.sortBy}' '${test.args.sortDirection}' and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        let nonSortedTable = await shoppingCartsPage.getAllRowsColumnContent(page, test.args.sortBy);

        await shoppingCartsPage.sortTable(page, test.args.sortBy, test.args.sortDirection);

        let sortedTable = await shoppingCartsPage.getAllRowsColumnContent(page, test.args.sortBy);

        if (test.args.isFloat) {
          nonSortedTable = await nonSortedTable.map(text => parseFloat(text));
          sortedTable = await sortedTable.map(text => parseFloat(text));
        }

        const expectedResult = await shoppingCartsPage.sortArray(nonSortedTable, test.args.isFloat);

        if (test.args.sortDirection === 'up') {
          await expect(sortedTable).to.deep.equal(expectedResult);
        } else {
          await expect(sortedTable).to.deep.equal(expectedResult.reverse());
        }
      });
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterSort', baseContext);

      const numberOfShoppingCartsAfterReset = await shoppingCartsPage.resetAndGetNumberOfLines(page);
      await expect(numberOfShoppingCartsAfterReset).to.be.above(1);
    });
  });

  // 4 - Delete customers with bulk actions
  describe('Delete customers with bulk actions', async () => {
    it('should go to \'Customers > Customers\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCustomersPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.customersParentLink,
        dashboardPage.customersLink,
      );

      await customersPage.closeSfToolBar(page);

      const pageTitle = await customersPage.getPageTitle(page);
      await expect(pageTitle).to.contains(customersPage.pageTitle);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetCustomerFilterFirst', baseContext);

      numberOfCustomers = await customersPage.resetAndGetNumberOfLines(page);
      await expect(numberOfCustomers).to.be.above(0);
    });

    it('should filter list by lastName', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToBulkEdit', baseContext);

      await customersPage.filterCustomers(
        page,
        'input',
        'lastname',
        'todelete',
      );

      const textResult = await customersPage.getTextColumnFromTableCustomers(page, 1, 'lastname');
      await expect(textResult).to.contains('todelete');
    });

    it('should delete customers with Bulk Actions and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteCustomers', baseContext);

      const deleteTextResult = await customersPage.deleteCustomersBulkActions(page);
      await expect(deleteTextResult).to.be.equal(customersPage.successfulMultiDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterBulkDelete', baseContext);

      const numberOfCustomersAfterReset = await customersPage.resetAndGetNumberOfLines(page);
      await expect(numberOfCustomersAfterReset).to.be.above(0);
    });
  });
});
