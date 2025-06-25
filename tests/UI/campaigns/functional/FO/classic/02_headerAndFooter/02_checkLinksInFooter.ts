import testContext from '@utils/testContext';
import {expect} from 'chai';

// Import commonTests
import deleteCacheTest from '@commonTests/BO/advancedParameters/cache';
import {deleteCustomerTest} from '@commonTests/BO/customers/customer';
import {createAccountTest} from '@commonTests/FO/classic/account';

import {
  type BrowserContext,
  dataCustomers,
  FakerCustomer,
  foClassicAboutUsPage,
  foClassicBestSalesPage,
  foClassicContactUsPage,
  foClassicCreateAccountPage,
  foClassicDeliveryPage,
  foClassicGuestOrderTrackingPage,
  foClassicHomePage,
  foClassicLegalNoticePage,
  foClassicLoginPage,
  foClassicMyAddressesPage,
  foClassicMyAddressesCreatePage,
  foClassicMyCreditSlipsPage,
  foClassicMyInformationsPage,
  foClassicMyOrderHistoryPage,
  foClassicMyWishlistsPage,
  foClassicNewProductsPage,
  foClassicPricesDropPage,
  foClassicSecurePaymentPage,
  foClassicSitemapPage,
  foClassicStoresPage,
  foClassicTermsAndConditionsOfUsePage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_FO_classic_headerAndFooter_checkLinksInFooter';

/*
Pre-condition:
- Create new customer account
- Delete cache
Scenario:
- Go to FO
- Check footer Products links( Prices drop, New products and Best sales)
Check our company links( Delivery, Legal notices, Terms and conditions of use, About us, Secure payment, Contact us,
Sitemap, Stores)
- Check your account links( Personal info, Orders, Credit slips, Addresses)
- Check store information
- Check copyright
Post-condition:
- Delete created customer
 */
describe('FO - Header and Footer : Check links in footer page', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let pageTitle: string;

  const today: Date = new Date();
  const currentYear: string = today.getFullYear().toString();
  const createCustomerData: FakerCustomer = new FakerCustomer();

  // Pre-condition: Create new account on FO
  createAccountTest(createCustomerData, `${baseContext}_preTest1`);

  // Pre-condition: Delete cache
  deleteCacheTest(`${baseContext}_preTest2`);

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  it('should go to FO home page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToFO', baseContext);

    await foClassicHomePage.goToFo(page);

    const isHomePage = await foClassicHomePage.isHomePage(page);
    expect(isHomePage).to.eq(true);
  });

  describe('Check \'Products\' footer links', async () => {
    [
      {linkSelector: 'Prices drop', pageTitle: foClassicPricesDropPage.pageTitle},
      {linkSelector: 'New products', pageTitle: foClassicNewProductsPage.pageTitle},
      {linkSelector: 'Best sellers', pageTitle: foClassicBestSalesPage.pageTitle},
    ].forEach((args, index: number) => {
      it(`should check '${args.linkSelector}' footer links`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkProductsFooterLinks${index}`, baseContext);

        // Check prices drop link
        await foClassicHomePage.goToFooterLink(page, args.linkSelector);

        const pageTitle = await foClassicHomePage.getPageTitle(page);
        expect(pageTitle).to.equal(args.pageTitle);
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

        // Check prices drop link
        await foClassicHomePage.goToFooterLink(page, args.linkSelector);

        const pageTitle = await foClassicHomePage.getPageTitle(page);
        expect(pageTitle).to.equal(args.pageTitle);
      });
    });
  });

  describe('Check \'Your Account\' footer links before login', async () => {
    [
      {linkSelector: 'Order tracking', pageTitle: foClassicGuestOrderTrackingPage.pageTitle},
      {linkSelector: 'Sign in', pageTitle: foClassicLoginPage.pageTitle},
      {linkSelector: 'Create account', pageTitle: foClassicCreateAccountPage.formTitle},
    ].forEach((args, index: number) => {
      it(`should check '${args.linkSelector}' footer links`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkYourAccountFooterLinks1${index}`, baseContext);

        // Check prices drop link
        await foClassicHomePage.goToFooterLink(page, args.linkSelector);

        if (args.linkSelector === 'Create account') {
          pageTitle = await foClassicCreateAccountPage.getHeaderTitle(page);
        } else {
          pageTitle = await foClassicHomePage.getPageTitle(page);
        }
        expect(pageTitle).to.equal(args.pageTitle);
      });
    });
  });

  describe('Check \'Your Account\' footer links after login with default customer', async () => {
    it('should login to FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginFO', baseContext);

      await foClassicHomePage.goToLoginPage(page);
      await foClassicLoginPage.customerLogin(page, dataCustomers.johnDoe);

      const isCustomerConnected = await foClassicLoginPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is not connected').to.eq(true);
    });

    [
      {linkSelector: 'Information', pageTitle: foClassicMyInformationsPage.pageTitle},
      {linkSelector: 'Addresses', pageTitle: foClassicMyAddressesPage.pageTitle},
      {linkSelector: 'Orders', pageTitle: foClassicMyOrderHistoryPage.pageTitle},
      {linkSelector: 'Credit slips', pageTitle: foClassicMyCreditSlipsPage.pageTitle},
      {linkSelector: 'Wishlist', pageTitle: foClassicMyWishlistsPage.pageTitle},
      {linkSelector: 'Sign out', pageTitle: foClassicLoginPage.pageTitle},
    ].forEach((args, index: number) => {
      it(`should check '${args.linkSelector}' footer links`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkYourAccountFooterLinks2${index}`, baseContext);

        // Check prices drop link
        await foClassicHomePage.goToFooterLink(page, args.linkSelector);

        if (args.linkSelector === 'Wishlist') {
          pageTitle = await foClassicMyWishlistsPage.getPageTitle(page);
        } else {
          pageTitle = await foClassicHomePage.getPageTitle(page);
        }
        expect(pageTitle).to.equal(args.pageTitle);
      });
    });
  });

  // Pre-condition: Delete cache
  deleteCacheTest(`${baseContext}_preTest3`);

  describe('Check \'Your Account\' footer links after login with new customer without address', async () => {
    it('should login to FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginFONewCustomer', baseContext);

      await foClassicHomePage.goToLoginPage(page);
      await foClassicLoginPage.customerLogin(page, createCustomerData);

      const isCustomerConnected = await foClassicLoginPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is not connected').to.eq(true);
    });

    [
      {linkSelector: 'Information', pageTitle: foClassicMyInformationsPage.pageTitle},
      {linkSelector: 'Add first address', pageTitle: foClassicMyAddressesCreatePage.pageTitle},
      {linkSelector: 'Orders', pageTitle: foClassicMyOrderHistoryPage.pageTitle},
      {linkSelector: 'Credit slips', pageTitle: foClassicMyCreditSlipsPage.pageTitle},
      {linkSelector: 'Wishlist', pageTitle: foClassicMyWishlistsPage.pageTitle},
      {linkSelector: 'Sign out', pageTitle: foClassicLoginPage.pageTitle},
    ].forEach((args, index: number) => {
      it(`should check '${args.linkSelector}' footer links`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkYourAccountFooterLinks3${index}`, baseContext);

        // Check prices drop link
        await foClassicHomePage.goToFooterLink(page, args.linkSelector);

        if (args.linkSelector === 'Wishlist') {
          pageTitle = await foClassicMyWishlistsPage.getPageTitle(page);
        } else {
          pageTitle = await foClassicHomePage.getPageTitle(page);
        }
        expect(pageTitle).to.equal(args.pageTitle);
      });
    });
  });

  describe('Check \'Store Information\'', async () => {
    it('should check \'Store Information\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkStoreInformation', baseContext);

      const storeInformation = await foClassicHomePage.getStoreInformation(page);
      expect(storeInformation).to.contains(global.INSTALL.SHOP_NAME)
        .and.to.contain(global.INSTALL.COUNTRY)
        .and.to.contains(global.BO.EMAIL);
    });
  });

  describe('Check the copyright', async () => {
    it('should check the copyright', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCopyright', baseContext);

      const copyright = await foClassicHomePage.getCopyright(page);
      expect(copyright).to.equal(`© ${currentYear} - Ecommerce software by PrestaShop™`);
    });
  });

  // Post-condition: Delete the created customer account
  deleteCustomerTest(createCustomerData, `${baseContext}_postTest`);
});
