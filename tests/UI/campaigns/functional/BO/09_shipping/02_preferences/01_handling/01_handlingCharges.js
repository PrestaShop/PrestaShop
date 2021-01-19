require('module-alias/register');

// Import utils
const helper = require('@utils/helpers');

// Common tests login BO
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const customerSettingsPage = require('@pages/BO/shopParameters/customerSettings');
const groupsPage = require('@pages/BO/shopParameters/customerSettings/groups');
const addGroupPage = require('@pages/BO/shopParameters/customerSettings/groups/add');
const preferencesPage = require('@pages/BO/shipping/preferences');
const foHomePage = require('@pages/FO/home');
const foProductPage = require('@pages/FO/product');
const foCartPage = require('@pages/FO/cart');
const foCheckoutPage = require('@pages/FO/checkout');

// Import data
const {groupAccess} = require('@data/demo/groupAccess');
const {Carriers} = require('@data/demo/carriers');
const {DefaultAccount} = require('@data/demo/customer');
const GroupFaker = require('@data/faker/group');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_shipping_preferences_handling_handlingCharges';

// Import expect from chai
const {expect} = require('chai');

// Browser and tab
let browserContext;
let page;
const editGroupData = new GroupFaker({name: 'Customer', frName: 'Client', discount: 0, priceDisplayMethod: 'Tax excluded', showPricesToggle: true});

/*
Choose Price display method : tax excluded
 */
describe('Handling charges', async () => {
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

  describe('Choose Price display method: tax excluded', async () => {
    it('should go to \'Shop parameters > Customer Settings\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCustomerSettingsPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.shopParametersParentLink,
        dashboardPage.customerSettingsLink,
      );

      await customerSettingsPage.closeSfToolBar(page);

      const pageTitle = await customerSettingsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(customerSettingsPage.pageTitle);
    });

    it('should go to Groups page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToGroupsPage', baseContext);

      await customerSettingsPage.goToGroupsPage(page);

      const pageTitle = await groupsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(groupsPage.pageTitle);
    });

    it(`should filter by '${groupAccess.customer.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByName', baseContext);

      await groupsPage.filterTable(page, 'input', 'b!name', groupAccess.customer.name);

      const textColumn = await groupsPage.getTextColumn(page, 1, 'b!name');
      await expect(textColumn).to.contains(groupAccess.customer.name);
    });

    it('should go to edit group page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditGroupPage', baseContext);

      await groupsPage.gotoEditGroupPage(page, 1);

      const pageTitle = await addGroupPage.getPageTitle(page);
      await expect(pageTitle).to.contains(addGroupPage.pageTitleEdit);
    });

    it('should update group by choosing \'Tax excluded\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateGroup', baseContext);

      const textResult = await addGroupPage.createEditGroup(page, editGroupData);
      await expect(textResult).to.contains(groupsPage.successfulUpdateMessage);
    });
  });

  it('should go to \'Shipping > Preferences\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToPreferencesPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.shippingLink,
      dashboardPage.shippingPreferencesLink,
    );

    const pageTitle = await preferencesPage.getPageTitle(page);
    await expect(pageTitle).to.contains(preferencesPage.pageTitle);
  });
});
