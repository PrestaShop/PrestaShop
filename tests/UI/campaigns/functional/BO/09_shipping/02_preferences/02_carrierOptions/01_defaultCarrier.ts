import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boDashboardPage,
  boLoginPage,
  boShippingPreferencesPage,
  type BrowserContext,
  dataCarriers,
  dataCustomers,
  FakerCarrier,
  foClassicCartPage,
  foClassicCheckoutPage,
  foClassicHomePage,
  foClassicProductPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_shipping_preferences_carrierOptions_defaultCarrier';

/*
Go to shipping > preferences page
Set default carrier to 'My carrier'
Go to Fo and check the update
Reset default carrier to 'PrestaShop'
Go to Fo and check the reset
 */
describe('BO - Shipping - Preferences : Update default carrier and check it in FO', async () => {
  let browserContext: BrowserContext;
  let page: Page;

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

  it('should go to \'Shipping > Preferences\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToPreferencesPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.shippingLink,
      boDashboardPage.shippingPreferencesLink,
    );
    await boShippingPreferencesPage.closeSfToolBar(page);

    const pageTitle = await boShippingPreferencesPage.getPageTitle(page);
    expect(pageTitle).to.contains(boShippingPreferencesPage.pageTitle);
  });

  const carriers: FakerCarrier[] = [
    dataCarriers.myCarrier,
    dataCarriers.clickAndCollect,
  ];

  carriers.forEach((carrier: FakerCarrier, index: number) => {
    describe(`Set default carrier to '${carrier.name}' and check result in FO`, async () => {
      it(`should set default carrier to ${carrier.name} in BO`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `setDefaultCarrier${index}`, baseContext);

        const textResult = await boShippingPreferencesPage.setDefaultCarrier(page, carrier);
        expect(textResult).to.contain(boShippingPreferencesPage.successfulUpdateMessage);
      });

      it('should view my shop', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `viewMyShop${index}`, baseContext);

        // Click on view my shop
        page = await boShippingPreferencesPage.viewMyShop(page);
        // Change FO language
        await foClassicHomePage.changeLanguage(page, 'en');

        const isHomePage = await foClassicHomePage.isHomePage(page);
        expect(isHomePage, 'Home page is not displayed').to.eq(true);
      });

      it('should go to shipping step in checkout', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkFinalSummary${index}`, baseContext);

        // Go to the first product page
        await foClassicHomePage.goToProductPage(page, 1);
        // Add the product to the cart
        await foClassicProductPage.addProductToTheCart(page);
        // Proceed to checkout the shopping cart
        await foClassicCartPage.clickOnProceedToCheckout(page);

        // Checkout the order
        if (index === 0) {
          // Personal information step - Login
          await foClassicCheckoutPage.clickOnSignIn(page);
          await foClassicCheckoutPage.customerLogin(page, dataCustomers.johnDoe);
        }

        // Address step - Go to delivery step
        const isStepAddressComplete = await foClassicCheckoutPage.goToDeliveryStep(page);
        expect(isStepAddressComplete, 'Step Address is not complete').to.eq(true);
      });

      it('should verify default carrier', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkDefaultCarrier${index}`, baseContext);

        const selectedShippingMethod = await foClassicCheckoutPage.getSelectedShippingMethod(page);
        expect(selectedShippingMethod, 'Wrong carrier was selected in FO').to.equal(carrier.name);
      });

      it('should go back to BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goBackToBO${index}`, baseContext);

        page = await foClassicCheckoutPage.closePage(browserContext, page, 0);

        const pageTitle = await boShippingPreferencesPage.getPageTitle(page);
        expect(pageTitle).to.contains(boShippingPreferencesPage.pageTitle);
      });
    });
  });
});
