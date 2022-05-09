require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const testContext = require('@utils/testContext');

// Import BO pages
const dashboardPage = require('@pages/BO/dashboard');
const ordersPage = require('@pages/BO/orders');
const addOrderPage = require('@pages/BO/orders/add');
const shoppingCartsPage = require('@pages/BO/orders/shoppingCarts');
const editAddressPage = require('@pages/BO/customers/addresses/add');

// Import demo data
const {DefaultCustomer} = require('@data/demo/customer');
const Address = require('@data/demo/address');

// Import faker data
const AddressFaker = require('@data/faker/address');

// Import common tests
const loginCommon = require('@commonTests/BO/loginBO');

const baseContext = 'functional_BO_orders_orders_createOrders_chooseAddress';

let browserContext;
let page;
let editAddressIframe;
// Variable used to get the number of shopping carts
let numberOfShoppingCarts;
// Variable used to get the number of non ordered shopping carts
let numberOfNonOrderedShoppingCarts;
// Variable used to get the last shopping card ID
let lastShoppingCartId;

const addressToEditData = new AddressFaker({country: 'France'});

/*
Pre-condition:

Scenario:

Post-condition:

 */
describe('BO - Orders - Create order : Choose address', async () => {
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  // Pre-condition: Delete non ordered shopping carts
  describe('PRE-TEST: Get the last shopping cart ID', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Orders > Shopping carts\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShoppingCartsPage1', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.ordersParentLink,
        dashboardPage.shoppingCartsLink,
      );

      const pageTitle = await shoppingCartsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(shoppingCartsPage.pageTitle);
    });

    it('should reset all filters and get number of shopping carts', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFiltersFirst1', baseContext);

      numberOfShoppingCarts = await shoppingCartsPage.resetAndGetNumberOfLines(page);
      await expect(numberOfShoppingCarts).to.be.above(0);
    });

    it('should search the non ordered shopping carts', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchNonOrderedShoppingCarts1', baseContext);

      await shoppingCartsPage.filterTable(page, 'input', 'status', 'Non ordered');

      numberOfNonOrderedShoppingCarts = await shoppingCartsPage.getNumberOfElementInGrid(page);
      await expect(numberOfNonOrderedShoppingCarts).to.be.at.most(numberOfShoppingCarts);

      numberOfShoppingCarts -= numberOfNonOrderedShoppingCarts;

      for (let row = 1; row <= numberOfNonOrderedShoppingCarts; row++) {
        const textColumn = await shoppingCartsPage.getTextColumn(page, row, 'c!lastname');
        await expect(textColumn).to.contains('Non ordered');
      }
    });

    it('should delete the non ordered shopping carts if exist', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteNonOrderedShoppingCartsIfExists1', baseContext);

      if (numberOfNonOrderedShoppingCarts > 0) {
        const deleteTextResult = await shoppingCartsPage.bulkDeleteShoppingCarts(page);
        await expect(deleteTextResult).to.be.contains(shoppingCartsPage.successfulMultiDeleteMessage);
      }
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDeleteNonOrderedCarts1', baseContext);

      const numberOfShoppingCartsAfterReset = await shoppingCartsPage.resetAndGetNumberOfLines(page);
      await expect(numberOfShoppingCartsAfterReset).to.be.above(0);
    });

    it('should get the last shopping cart ID', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getIdOfLastShoppingCart1', baseContext);

      lastShoppingCartId = await shoppingCartsPage.getTextColumn(page, 1, 'id_cart');
      await expect(parseInt(lastShoppingCartId, 10)).to.be.above(0);
    });
  });

  // 1 - Go to create order page
  describe('Go to create order page', async () => {
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

    it('should go to create order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCreateOrderPage', baseContext);

      await ordersPage.goToCreateOrderPage(page);
      const pageTitle = await addOrderPage.getPageTitle(page);
      await expect(pageTitle).to.contains(addOrderPage.pageTitle);
    });

    it(`should choose customer ${DefaultCustomer.firstName} ${DefaultCustomer.lastName}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseDefaultCustomer', baseContext);

      await addOrderPage.searchCustomer(page, DefaultCustomer.email);

      const isCartsTableVisible = await addOrderPage.chooseCustomer(page);
      await expect(isCartsTableVisible, 'History block is not visible!').to.be.true;
    });
  });

  // 2 - Edit address
  describe('Choose and edit address', async () => {
    it('should choose the address \'My address\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseMyAddress', baseContext);

      const myAddress = await addOrderPage.chooseDeliveryAddress(page, 'My address');
      await expect(myAddress).to.be.equal(`${Address.third.firstName} ${Address.third.lastName}${Address.third.company}`
        + `${Address.third.address} ${Address.third.secondAddress}${Address.third.city}, ${Address.third.state} `
        + `${Address.third.zipCode}${Address.third.country}${Address.third.phone}`);
    });

    it('should click on edit address and check if edit address iframe is visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnEditAddress', baseContext);

      const isIframeVisible = await addOrderPage.clickOnEditDeliveryAddressButton(page);
      await expect(isIframeVisible, 'Edit address iframe is not visible!').to.be.true;
    });

    it('should edit the address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editAddress', baseContext);

      editAddressIframe = await addOrderPage.getEditAddressIframe(page, lastShoppingCartId + 1, 7);

      const textResult = await editAddressPage.createEditAddress(editAddressIframe, addressToEditData);
      await expect(textResult).to.equal(editAddressPage.successfulCreationMessage);
    });
  });
});
