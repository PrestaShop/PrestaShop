import {expect} from 'chai';
import {disableTheme, enableTheme} from '@commonTests/BO/design/hummingbird';
import testContext from '@utils/testContext';

import {
  type BrowserContext,
  dataCategories,
  dataProducts,
  foHummingbirdAboutUsPage,
  foHummingbirdBestSalesPage,
  foHummingbirdCategoryPage,
  foHummingbirdContactUsPage,
  foHummingbirdCreateAccountPage,
  foHummingbirdDeliveryPage,
  foHummingbirdGuestOrderTrackingPage,
  foHummingbirdHomePage,
  foHummingbirdLegalNoticePage,
  foHummingbirdLoginPage,
  foHummingbirdNewProductsPage,
  foHummingbirdPricesDropPage,
  foHummingbirdProductPage,
  foHummingbirdSearchResultsPage,
  foHummingbirdSecurePaymentPage,
  foHummingbirdSitemapPage,
  foHummingbirdStoresPage,
  foHummingbirdTermsAndConditionsOfUsePage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'audit_FO_hummingbird_guest';

describe('Check FO public pages', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // Pre-condition : Enable Hummingbird
  enableTheme('hummingbird', `${baseContext}_preTest_0`);

  describe('Check FO public pages', async () => {
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

      await foHummingbirdHomePage.goTo(page, global.FO.URL);

      const result = await foHummingbirdHomePage.isHomePage(page);
      expect(result).to.eq(true);

      const jsErrors = utilsPlaywright.getJsErrors();
      expect(jsErrors.length).to.equals(0);
    });

    it('should go to a category page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCategory', baseContext);

      await foHummingbirdHomePage.goToCategory(page, dataCategories.clothes.id);

      const pageTitle = await foHummingbirdCategoryPage.getPageTitle(page);
      expect(pageTitle).to.equal(dataCategories.clothes.name);

      const jsErrors = utilsPlaywright.getJsErrors();
      expect(jsErrors.length).to.equals(0);
    });

    it('should go to a subcategory page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToSubCategory', baseContext);

      await foHummingbirdCategoryPage.goToSubCategory(page, dataCategories.clothes.id, dataCategories.men.id);

      const pageTitle = await foHummingbirdCategoryPage.getPageTitle(page);
      expect(pageTitle).to.equal(dataCategories.men.name);

      const jsErrors = utilsPlaywright.getJsErrors();
      expect(jsErrors.length).to.equals(0);
    });

    it('should go to a product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProduct', baseContext);

      await foHummingbirdCategoryPage.goToProductPage(page, 1);

      const pageTitle = await foHummingbirdProductPage.getPageTitle(page);
      expect(pageTitle.toUpperCase()).to.contains(dataProducts.demo_1.name.toUpperCase());

      const jsErrors = utilsPlaywright.getJsErrors();
      expect(jsErrors.length).to.equals(0);
    });

    it('should search a product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToSearch', baseContext);

      await foHummingbirdProductPage.searchProduct(page, 'shirt');

      const pageTitle = await foHummingbirdSearchResultsPage.getPageTitle(page);
      expect(pageTitle).to.equal(foHummingbirdSearchResultsPage.pageTitle);

      const jsErrors = utilsPlaywright.getJsErrors();
      expect(jsErrors.length).to.equals(0);
    });

    describe('Check \'Products\' footer links', async () => {
      [
        {linkSelector: 'Prices drop', pageTitle: foHummingbirdPricesDropPage.pageTitle},
        {linkSelector: 'New products', pageTitle: foHummingbirdNewProductsPage.pageTitle},
        {linkSelector: 'Best sellers', pageTitle: foHummingbirdBestSalesPage.pageTitle},
      ].forEach((args, index: number) => {
        it(`should check '${args.linkSelector}' footer links`, async function () {
          await testContext.addContextItem(this, 'testIdentifier', `checkProductsFooterLinks${index}`, baseContext);

          await foHummingbirdHomePage.goToFooterLink(page, args.linkSelector);

          const pageTitle = await foHummingbirdHomePage.getPageTitle(page);
          expect(pageTitle).to.equal(args.pageTitle);

          const jsErrors = utilsPlaywright.getJsErrors();
          expect(jsErrors.length).to.equals(0);
        });
      });
    });

    describe('Check \'Our Company\' footer links', async () => {
      [
        {linkSelector: 'Delivery', pageTitle: foHummingbirdDeliveryPage.pageTitle},
        {linkSelector: 'Legal Notice', pageTitle: foHummingbirdLegalNoticePage.pageTitle},
        {linkSelector: 'Terms and conditions of use', pageTitle: foHummingbirdTermsAndConditionsOfUsePage.pageTitle},
        {linkSelector: 'About us', pageTitle: foHummingbirdAboutUsPage.pageTitle},
        {linkSelector: 'Secure payment', pageTitle: foHummingbirdSecurePaymentPage.pageTitle},
        {linkSelector: 'Contact us', pageTitle: foHummingbirdContactUsPage.pageTitle},
        {linkSelector: 'Sitemap', pageTitle: foHummingbirdSitemapPage.pageTitle},
        {linkSelector: 'Stores', pageTitle: foHummingbirdStoresPage.pageTitle},
      ].forEach((args, index: number) => {
        it(`should check '${args.linkSelector}' footer links`, async function () {
          await testContext.addContextItem(this, 'testIdentifier', `checkOurCompanyFooterLinks${index}`, baseContext);

          await foHummingbirdHomePage.goToFooterLink(page, args.linkSelector);

          const pageTitle = await foHummingbirdHomePage.getPageTitle(page);
          expect(pageTitle).to.equal(args.pageTitle);

          const jsErrors = utilsPlaywright.getJsErrors();
          expect(jsErrors.length).to.equals(0);
        });
      });
    });

    describe('Check \'Your Account\' footer links', async () => {
      [
        {linkSelector: 'Order tracking', pageTitle: foHummingbirdGuestOrderTrackingPage.pageTitle},
        {linkSelector: 'Sign in', pageTitle: foHummingbirdLoginPage.pageTitle},
        {linkSelector: 'Create account', pageTitle: foHummingbirdCreateAccountPage.formTitle},
      ].forEach((args, index: number) => {
        it(`should check '${args.linkSelector}' footer links`, async function () {
          await testContext.addContextItem(this, 'testIdentifier', `checkYourAccountFooterLinks${index}`, baseContext);

          await foHummingbirdHomePage.goToFooterLink(page, args.linkSelector);

          let pageTitle: string = '';

          if (args.linkSelector === 'Create account') {
            pageTitle = await foHummingbirdCreateAccountPage.getHeaderTitle(page);
          } else {
            pageTitle = await foHummingbirdHomePage.getPageTitle(page);
          }
          expect(pageTitle).to.equal(args.pageTitle);
        });
      });
    });
  });

  // Post-condition : Disable Hummingbird
  disableTheme('hummingbird', `${baseContext}_postTest_0`);
});
