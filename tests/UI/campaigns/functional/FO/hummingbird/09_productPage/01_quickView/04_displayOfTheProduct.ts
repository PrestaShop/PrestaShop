// Import utils
import testContext from '@utils/testContext';

// Import common tests
import {enableTheme, disableTheme} from '@commonTests/BO/design/hummingbird';

import {expect} from 'chai';
import {
  type BrowserContext,
  dataProducts,
  foHummingbirdHomePage,
  foHummingbirdModalQuickViewPage,
  type Page,
  type ProductAttribute,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_FO_hummingbird_productPage_quickView_displayOfTheProduct';

/*
Pre-condition:
- Install hummingbird theme
Scenario:
- Go to FO
- Quick view third product
- Check quick view modal
Post-condition:
- Uninstall hummingbird theme
 */
describe('FO - Product page - Quick view : Display of the product', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  const attributes: ProductAttribute = {
    name: 'dimension',
    value: '60x90cm',
  };

  // Pre-condition : Install Hummingbird
  enableTheme('hummingbird', `${baseContext}_preTest`);

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe(`Display of the product '${dataProducts.demo_6.name}`, async () => {
    it('should go to FO home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFo', baseContext);

      await foHummingbirdHomePage.goToFo(page);

      const isHomePage = await foHummingbirdHomePage.isHomePage(page);
      expect(isHomePage).to.equal(true);
    });

    it('should quick view the third product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'quickView', baseContext);

      await foHummingbirdHomePage.quickViewProduct(page, 3);

      const isModalVisible = await foHummingbirdModalQuickViewPage.isQuickViewProductModalVisible(page);
      expect(isModalVisible).to.equal(true);
    });

    it('should check product details', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductDetails', baseContext);

      const result = await foHummingbirdModalQuickViewPage.getProductDetailsFromQuickViewModal(page);
      await Promise.all([
        expect(result.name).to.equal(dataProducts.demo_6.name),
        expect(result.price).to.equal(dataProducts.demo_6.combinations[0].price),
        expect(result.taxShippingDeliveryLabel).to.equal('Tax included'),
        expect(result.shortDescription).to.equal(dataProducts.demo_6.summary),
        expect(result.coverImage).to.contains(dataProducts.demo_6.coverImage),
        expect(result.thumbImage).to.contains(dataProducts.demo_6.thumbImage),
      ]);

      const resultAttributes = await foHummingbirdModalQuickViewPage.getSelectedAttributesFromQuickViewModal(page, attributes);
      expect(resultAttributes.length).to.equal(1);
      expect(resultAttributes[0].name).to.equal('dimension');
      expect(resultAttributes[0].value).to.equal('40x60cm');
    });

    it('should check the product cover image', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductImage', baseContext);

      const quickViewImageMain = await foHummingbirdModalQuickViewPage.getQuickViewImageMain(page);
      expect(quickViewImageMain).to.contains(dataProducts.demo_6.coverImage);
    });

    it('should check that \'Add to cart\' button is enabled', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkAddToCartButton', baseContext);

      const isEnabled = await foHummingbirdModalQuickViewPage.isAddToCartButtonEnabled(page);
      expect(isEnabled, 'Add to cart button is disabled').to.equal(true);
    });
  });

  // Post-condition : Uninstall Hummingbird
  disableTheme('hummingbird', `${baseContext}_postTest`);
});
