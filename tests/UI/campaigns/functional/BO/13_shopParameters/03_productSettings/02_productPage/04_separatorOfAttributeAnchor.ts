// Import utils
import testContext from '@utils/testContext';

import {expect} from 'chai';
import {
  boDashboardPage,
  boLoginPage,
  boProductSettingsPage,
  type BrowserContext,
  dataProducts,
  foHummingbirdHomePage,
  foHummingbirdProductPage,
  foHummingbirdSearchResultsPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_shopParameters_productSettings_productPage_separatorOfAttributeAnchor';

describe('BO - Shop Parameters - Product Settings : Update separator of attribute anchor on '
  + 'the product links', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const productAttributes: string[] = ['1', 'size', 's/8', 'color', 'white'];

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

  it('should go to \'Shop parameters > Product Settings\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToProductSettingsPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.shopParametersParentLink,
      boDashboardPage.productSettingsLink,
    );

    const pageTitle = await boProductSettingsPage.getPageTitle(page);
    expect(pageTitle).to.contains(boProductSettingsPage.pageTitle);
  });

  [
    {option: ',', attributesInProductLink: productAttributes.join(',')},
    {option: '-', attributesInProductLink: productAttributes.join('-')},
  ].forEach((arg, index: number) => {
    it(`should choose the separator option '${arg.option}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `chooseOption_${index}`, baseContext);

      const result = await boProductSettingsPage.setSeparatorOfAttributeOnProductLink(
        page,
        arg.option,
      );
      expect(result).to.contains(boProductSettingsPage.successfulUpdateMessage);
    });

    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `viewMyShop${index}`, baseContext);

      page = await boProductSettingsPage.viewMyShop(page);
      await foHummingbirdHomePage.changeLanguage(page, 'en');

      const isHomePage = await foHummingbirdHomePage.isHomePage(page);
      expect(isHomePage, 'Home page was not opened').to.eq(true);
    });

    it('should search for the product and go to product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goToProductPage${index}`, baseContext);

      await foHummingbirdHomePage.searchProduct(page, dataProducts.demo_1.name);
      await foHummingbirdSearchResultsPage.goToProductPage(page, 1);

      const pageTitle = await foHummingbirdProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(dataProducts.demo_1.name);
    });

    it('should check the attribute separator on the product links in FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `checkAttributeSeparator_${index}`, baseContext);

      const currentURL = await foHummingbirdProductPage.getProductPageURL(page);
      expect(currentURL).to.contains(arg.attributesInProductLink);
    });

    it('should close the page and go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `closePageAndBackToBO${index}`, baseContext);

      page = await foHummingbirdProductPage.closePage(browserContext, page, 0);

      const pageTitle = await boProductSettingsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductSettingsPage.pageTitle);
    });
  });
});
