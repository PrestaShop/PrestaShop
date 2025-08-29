import {expect} from 'chai';
import testContext from '@utils/testContext';

import {
  type BrowserContext,
  dataCategories,
  dataProducts,
  foClassicAboutUsPage,
  foClassicBestSalesPage,
  foClassicCategoryPage,
  foClassicContactUsPage,
  foClassicCreateAccountPage,
  foClassicDeliveryPage,
  foClassicGuestOrderTrackingPage,
  foClassicHomePage,
  foClassicLegalNoticePage,
  foClassicLoginPage,
  foClassicNewProductsPage,
  foClassicPricesDropPage,
  foClassicProductPage,
  foClassicSearchResultsPage,
  foClassicSecurePaymentPage,
  foClassicSitemapPage,
  foClassicStoresPage,
  foClassicTermsAndConditionsOfUsePage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'audit_FO_classic_guest';

describe('Check FO public pages', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  before(async function () {
    utilsPlaywright.setErrorsCaptured(true);

    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  beforeEach(async () => {
    utilsPlaywright.resetJsErrors();
  });

  it('should go to the home page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToHome', baseContext);

    await foClassicHomePage.goTo(page, global.FO.URL);

    const result = await foClassicHomePage.isHomePage(page);
    expect(result).to.eq(true);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to a category page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCategory', baseContext);

    await foClassicHomePage.goToCategory(page, dataCategories.clothes.id);

    const pageTitle = await foClassicCategoryPage.getPageTitle(page);
    expect(pageTitle).to.equal(dataCategories.clothes.name);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to a subcategory page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToSubCategory', baseContext);

    await foClassicCategoryPage.goToSubCategory(page, dataCategories.clothes.id, dataCategories.men.id);

    const pageTitle = await foClassicCategoryPage.getPageTitle(page);
    expect(pageTitle).to.equal(dataCategories.men.name);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to a product page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToProduct', baseContext);

    await foClassicCategoryPage.goToProductPage(page, 1);

    const pageTitle = await foClassicProductPage.getPageTitle(page);
    expect(pageTitle.toUpperCase()).to.contains(dataProducts.demo_1.name.toUpperCase());

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should search a product', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToSearch', baseContext);

    await foClassicProductPage.searchProduct(page, 'shirt');

    const pageTitle = await foClassicSearchResultsPage.getPageTitle(page);
    expect(pageTitle).to.equal(foClassicSearchResultsPage.pageTitle);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  describe('Check \'Products\' footer links', async () => {
    [
      {linkSelector: 'Prices drop', pageTitle: foClassicPricesDropPage.pageTitle},
      {linkSelector: 'New products', pageTitle: foClassicNewProductsPage.pageTitle},
      {linkSelector: 'Best sellers', pageTitle: foClassicBestSalesPage.pageTitle},
    ].forEach((args, index: number) => {
      it(`should check '${args.linkSelector}' footer links`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkProductsFooterLinks${index}`, baseContext);

        await foClassicHomePage.goToFooterLink(page, args.linkSelector);

        const pageTitle = await foClassicHomePage.getPageTitle(page);
        expect(pageTitle).to.equal(args.pageTitle);

        const jsErrors = utilsPlaywright.getJsErrors();
        expect(jsErrors.length).to.equals(0);
      });
    });
  });

  describe('Check \'Our Company\' footer links', async () => {
    [
      {linkSelector: 'Delivery', pageTitle: foClassicDeliveryPage.pageTitle},
      {linkSelector: 'Legal Notice', pageTitle: foClassicLegalNoticePage.pageTitle},
      {linkSelector: 'Terms and conditions of use', pageTitle: foClassicTermsAndConditionsOfUsePage.pageTitle},
      {linkSelector: 'About us', pageTitle: foClassicAboutUsPage.pageTitle},
      {linkSelector: 'Secure payment', pageTitle: foClassicSecurePaymentPage.pageTitle},
      {linkSelector: 'Contact us', pageTitle: foClassicContactUsPage.pageTitle},
      {linkSelector: 'Sitemap', pageTitle: foClassicSitemapPage.pageTitle},
      {linkSelector: 'Stores', pageTitle: foClassicStoresPage.pageTitle},
    ].forEach((args, index: number) => {
      it(`should check '${args.linkSelector}' footer links`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkOurCompanyFooterLinks${index}`, baseContext);

        await foClassicHomePage.goToFooterLink(page, args.linkSelector);

        const pageTitle = await foClassicHomePage.getPageTitle(page);
        expect(pageTitle).to.equal(args.pageTitle);

        const jsErrors = utilsPlaywright.getJsErrors();
        expect(jsErrors.length).to.equals(0);
      });
    });
  });

  describe('Check \'Your Account\' footer links', async () => {
    [
      {linkSelector: 'Order tracking', pageTitle: foClassicGuestOrderTrackingPage.pageTitle},
      {linkSelector: 'Sign in', pageTitle: foClassicLoginPage.pageTitle},
      {linkSelector: 'Create account', pageTitle: foClassicCreateAccountPage.formTitle},
    ].forEach((args, index: number) => {
      it(`should check '${args.linkSelector}' footer links`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkYourAccountFooterLinks${index}`, baseContext);

        await foClassicHomePage.goToFooterLink(page, args.linkSelector);

        let pageTitle: string = '';

        if (args.linkSelector === 'Create account') {
          pageTitle = await foClassicCreateAccountPage.getHeaderTitle(page);
        } else {
          pageTitle = await foClassicHomePage.getPageTitle(page);
        }
        expect(pageTitle).to.equal(args.pageTitle);
      });
    });
  });
});
