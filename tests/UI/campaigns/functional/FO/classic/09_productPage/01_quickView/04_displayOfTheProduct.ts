// Import utils
import testContext from '@utils/testContext';

import {expect} from 'chai';
import {
  type BrowserContext,
  dataProducts,
  foClassicHomePage,
  foClassicModalQuickViewPage,
  type Page,
  type ProductAttribute,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_FO_classic_productPage_quickView_displayOfTheProduct';

/*
Scenario:
- Go to FO
- Quick view third product
- Check quick view modal
 */
describe('FO - Product page - Quick view : Display of the product', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  const attributes: ProductAttribute = {
    name: 'dimension',
    value: '60x90cm',
  };

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  it('should go to FO home page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToFo', baseContext);

    await foClassicHomePage.goToFo(page);

    const isHomePage = await foClassicHomePage.isHomePage(page);
    expect(isHomePage).to.equal(true);
  });

  it('should quick view the third product', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'quickView', baseContext);

    await foClassicHomePage.quickViewProduct(page, 3);

    const isModalVisible = await foClassicModalQuickViewPage.isQuickViewProductModalVisible(page);
    expect(isModalVisible).to.equal(true);
  });

  it('should check product details', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkProductDetails', baseContext);

    const result = await foClassicModalQuickViewPage.getProductDetailsFromQuickViewModal(page);
    await Promise.all([
      expect(result.name).to.equal(dataProducts.demo_6.name),
      expect(result.price).to.equal(dataProducts.demo_6.combinations[0].price),
      expect(result.taxShippingDeliveryLabel).to.equal('Tax included'),
      expect(result.shortDescription).to.equal(dataProducts.demo_6.summary),
      expect(result.coverImage).to.contains(dataProducts.demo_6.coverImage),
      expect(result.thumbImage).to.contains(dataProducts.demo_6.thumbImage),
    ]);

    const resultAttributes = await foClassicModalQuickViewPage.getSelectedAttributesFromQuickViewModal(page, attributes);
    expect(resultAttributes.length).to.equal(1);
    expect(resultAttributes[0].name).to.equal('dimension');
    expect(resultAttributes[0].value).to.equal('40x60cm');
  });

  it('should check the product cover image', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkProductImage', baseContext);

    const quickViewImageMain = await foClassicModalQuickViewPage.getQuickViewCoverImage(page);
    expect(quickViewImageMain).to.contains(dataProducts.demo_6.coverImage);
  });

  it('should check that \'Add to cart\' button is enabled', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkAddToCartButton', baseContext);

    const isEnabled = await foClassicModalQuickViewPage.isAddToCartButtonEnabled(page);
    expect(isEnabled, 'Add to cart button is disabled').to.equal(true);
  });
});
