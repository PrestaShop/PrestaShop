require('module-alias/register');

// Import utils
const helper = require('@utils/helpers');

// Common tests login BO
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const preferencesPage = require('@pages/BO/shipping/preferences');
const carriersPage = require('@pages/BO/shipping/carriers');
const foHomePage = require('@pages/FO/home');
const foProductPage = require('@pages/FO/product');
const foCartPage = require('@pages/FO/cart');
const foCheckoutPage = require('@pages/FO/checkout');

// Import data
const {Carriers} = require('@data/demo/carriers');
const {DefaultAccount} = require('@data/demo/customer');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_shipping_preferences_carrierOptions_updateCarriersSortOption';

// Import expect from chai
const {expect} = require('chai');

// Browser and tab
let browserContext;
let page;

let numberOfCarriers = 0;

/*
Go to shipping > Carriers page
Activate the 2 carriers 'My cheap carrier' and 'My light carrier'
Go to shipping > preferences page
Choose sort by 'Price'
Go to FO and check the carriers sort
Go back to BO and choose sort by 'Position'
Go to FO and check the carriers sort
Go back to BO > shipping > Carriers
Disable the 2 carriers 'My cheap carrier' and 'My light carrier'
 */
describe('Update \'sort carriers by\' and check it in FO', async () => {
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

  it('should go to \'Shipping > Carriers\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCarriersPageToEnable', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.shippingLink,
      dashboardPage.carriersLink,
    );

    const pageTitle = await carriersPage.getPageTitle(page);
    await expect(pageTitle).to.contains(carriersPage.pageTitle);
  });

  const carriersNames = [
    Carriers.cheapCarrier.name,
    Carriers.lightCarrier.name,
  ];

  describe(`Enable the 2 carriers '${Carriers.cheapCarrier.name}' and '${Carriers.lightCarrier.name}'`, async () => {
    it('should reset all filters and get number of carriers in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

      numberOfCarriers = await carriersPage.resetAndGetNumberOfLines(page);
      await expect(numberOfCarriers).to.be.above(0);
    });

    carriersNames.forEach((carrierName, index) => {
      it(`should filter list by name ${carrierName}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `filterByName${index}`, baseContext);

        await carriersPage.filterTable(page, 'input', 'name', carrierName);

        const textColumn = await carriersPage.getTextColumn(page, 1, 'name');
        await expect(textColumn).to.contains(carrierName);
      });

      it('should enable the carrier', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `enableCarrier${index}`, baseContext);

        const isActionPerformed = await carriersPage.setStatus(page, 1, true);

        if (isActionPerformed) {
          const resultMessage = await carriersPage.getTextContent(
            page,
            carriersPage.alertSuccessBlock,
          );

          await expect(resultMessage).to.contains(carriersPage.successfulUpdateStatusMessage);
        }

        const carrierStatus = await carriersPage.getStatus(page, 1);
        await expect(carrierStatus).to.be.true;
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `resetFilterAfterEnable${index}`, baseContext);

        const numberOfCarriersAfterReset = await carriersPage.resetAndGetNumberOfLines(page);
        await expect(numberOfCarriersAfterReset).to.be.equal(numberOfCarriers);
      });
    });
  });

  describe('Choose the sort option in BO and check it in FO', async () => {
    it('should go to \'Shipping > Preferences\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToPreferencesPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.shippingLink,
        dashboardPage.shippingPreferencesLink,
      );

      await preferencesPage.closeSfToolBar(page);

      const pageTitle = await preferencesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(preferencesPage.pageTitle);
    });

    const sortByPosition = [
      Carriers.default.name,
      Carriers.myCarrier.name,
      Carriers.cheapCarrier.name,
      Carriers.lightCarrier.name,
    ];

    ['Position', 'Price'].forEach((sortBy, index) => {
      it(`should set sort by '${sortBy}' in BO`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `setDefaultCarrier${index}`, baseContext);

        const textResult = await preferencesPage.setCarrierSortBy(page, sortBy);
        await expect(textResult).to.contain(preferencesPage.successfulUpdateMessage);
      });

      it('should view my shop', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `viewMyShop${index}`, baseContext);

        // Click on view my shop
        page = await preferencesPage.viewMyShop(page);

        // Change FO language
        await foHomePage.changeLanguage(page, 'en');

        const isHomePage = await foHomePage.isHomePage(page);
        await expect(isHomePage, 'Home page is not displayed').to.be.true;
      });

      it('should go to shipping step in checkout', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkFinalSummary${index}`, baseContext);

        // Go to the first product page
        await foHomePage.goToProductPage(page, 1);

        // Add the product to the cart
        await foProductPage.addProductToTheCart(page);

        // Proceed to checkout the shopping cart
        await foCartPage.clickOnProceedToCheckout(page);

        // Checkout the order
        if (index === 0) {
          // Personal information step - Login
          await foCheckoutPage.clickOnSignIn(page);
          await foCheckoutPage.customerLogin(page, DefaultAccount);
        }

        // Address step - Go to delivery step
        const isStepAddressComplete = await foCheckoutPage.goToDeliveryStep(page);
        await expect(isStepAddressComplete, 'Step Address is not complete').to.be.true;
      });

      it(`should verify the sort of carriers by '${sortBy}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkDefaultCarrier${index}`, baseContext);

        if (sortBy === 'Price') {
          const sortedCarriers = await foCheckoutPage.getAllCarriersPrices(page);
          const expectedResult = await foCheckoutPage.sortArray(sortedCarriers, true);
          await expect(sortedCarriers).to.deep.equal(expectedResult);
        } else {
          const sortedCarriers = await foCheckoutPage.getAllCarriersNames(page);
          await expect(sortedCarriers).to.deep.equal(sortByPosition);
        }
      });

      it('should go back to BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goBackToBO${index}`, baseContext);

        page = await foCheckoutPage.closePage(browserContext, page, 0);

        const pageTitle = await preferencesPage.getPageTitle(page);
        await expect(pageTitle).to.contains(preferencesPage.pageTitle);
      });
    });
  });

  describe(`Disable the 2 carriers '${Carriers.cheapCarrier.name}' and '${Carriers.lightCarrier.name}'`, async () => {
    it('should go to \'Shipping > Carriers\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCarriersPageToDisable', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.shippingLink,
        dashboardPage.carriersLink,
      );

      const pageTitle = await carriersPage.getPageTitle(page);
      await expect(pageTitle).to.contains(carriersPage.pageTitle);
    });

    carriersNames.forEach((carrierName, index) => {
      it(`should filter list by name ${carrierName}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `filterByName${index}ToDisable`, baseContext);

        await carriersPage.filterTable(page, 'input', 'name', carrierName);

        const textColumn = await carriersPage.getTextColumn(page, 1, 'name');
        await expect(textColumn).to.contains(carrierName);
      });

      it('should disable the carrier', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `disableCarrier${index}`, baseContext);

        const isActionPerformed = await carriersPage.setStatus(page, 1, false);

        if (isActionPerformed) {
          const resultMessage = await carriersPage.getTextContent(
            page,
            carriersPage.alertSuccessBlock,
          );

          await expect(resultMessage).to.contains(carriersPage.successfulUpdateStatusMessage);
        }

        const carrierStatus = await carriersPage.getStatus(page, 1);
        await expect(carrierStatus).to.be.false;
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `resetFilterAfterDisable${index}`, baseContext);

        const numberOfCarriersAfterReset = await carriersPage.resetAndGetNumberOfLines(page);
        await expect(numberOfCarriersAfterReset).to.be.equal(numberOfCarriers);
      });
    });
  });
});
