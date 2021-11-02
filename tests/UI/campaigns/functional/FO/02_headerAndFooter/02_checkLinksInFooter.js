require('module-alias/register');

const {expect} = require('chai');

const helper = require('@utils/helpers');

// Importing FO pages
const homePage = require('@pages/FO/home');
const loginPage = require('@pages/FO/login');
const pricesDropPage = require('@pages/FO/pricesDrop');
const newProductsPage = require('@pages/FO/newProducts');
const bestSalesPage = require('@pages/FO/bestSales');
const deliveryPage = require('@pages/FO/delivery');
const legalNoticePage = require('@pages/FO/legalNotice');
const termsAndConditionsOfUsePage = require('@pages/FO/termsAndConditionsOfUse');
const aboutUsPage = require('@pages/FO/aboutUs');
const securePaymentPage = require('@pages/FO/securePayment');
const contactUsPage = require('@pages/FO/contactUs');
const siteMapPage = require('@pages/FO/siteMap');
const storesPage = require('@pages/FO/stores');
const personalInfoPage = require('@pages/FO/myAccount/identity');
const ordersPage = require('@pages/FO/myAccount/orderHistory');
const creditSlipsPage = require('@pages/FO/myAccount/creditSlips');
const addressesPage = require('@pages/FO/myAccount/addresses');

// Import data
const {DefaultCustomer} = require('@data/demo/customer');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_FO_headerAndFooter_checkLinksInFooter';

let browserContext;
let page;

/*
Go to FO
Check footer Products links( Prices drop, New products and Best sales)
Check our company links( Delivery, Legal notices, Terms and conditions of use, About us, Secure payment, Contact us,
Sitemap, Stores)
Check your account links( Personal info, Orders, Credit slips, Addresses)
 */

describe('FO - Header and Footer : Check links in footer page', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  it('should go to FO home page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToFO', baseContext);

    await homePage.goToFo(page);

    const isHomePage = await homePage.isHomePage(page);
    await expect(isHomePage).to.be.true;
  });

  describe('Check \'Products\' footer links', async () => {
    [
      {linkSelector: 'Prices drop', pageTitle: pricesDropPage.pageTitle},
      {linkSelector: 'New products', pageTitle: newProductsPage.pageTitle},
      {linkSelector: 'Best sales', pageTitle: bestSalesPage.pageTitle},
    ].forEach((args, index) => {
      it(`should check '${args.linkSelector}' footer links`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkProductsFooterLinks${index}`, baseContext);

        // Check prices drop link
        await homePage.goToFooterLink(page, args.linkSelector);

        const pageTitle = await pricesDropPage.getPageTitle(page);
        await expect(pageTitle).to.equal(args.pageTitle);
      });
    });
  });

  describe('Check \'Our Company\' footer links', async () => {
    [
      {linkSelector: 'Delivery', pageTitle: deliveryPage.pageTitle},
      {linkSelector: 'Legal Notice', pageTitle: legalNoticePage.pageTitle},
      {linkSelector: 'Terms and conditions of use', pageTitle: termsAndConditionsOfUsePage.pageTitle},
      {linkSelector: 'About us', pageTitle: aboutUsPage.pageTitle},
      {linkSelector: 'Secure payment', pageTitle: securePaymentPage.pageTitle},
      {linkSelector: 'Contact us', pageTitle: contactUsPage.pageTitle},
      {linkSelector: 'Sitemap', pageTitle: siteMapPage.pageTitle},
      {linkSelector: 'Stores', pageTitle: storesPage.pageTitle},
    ].forEach((args, index) => {
      it(`should check '${args.linkSelector}' footer links`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkOurCompanyFooterLinks${index}`, baseContext);

        // Check prices drop link
        await homePage.goToFooterLink(page, args.linkSelector);

        const pageTitle = await pricesDropPage.getPageTitle(page);
        await expect(pageTitle).to.equal(args.pageTitle);
      });
    });
  });

  describe('Check \'Your Account\' footer links before login', async () => {
    [
      {linkSelector: 'Personal info', pageTitle: loginPage.pageTitle},
      {linkSelector: 'Orders', pageTitle: loginPage.pageTitle},
      {linkSelector: 'Credit slips', pageTitle: loginPage.pageTitle},
      {linkSelector: 'Addresses', pageTitle: loginPage.pageTitle},
    ].forEach((args, index) => {
      it(`should check '${args.linkSelector}' footer links`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkYourAccountFooterLinks1${index}`, baseContext);

        // Check prices drop link
        await homePage.goToFooterLink(page, args.linkSelector);

        const pageTitle = await pricesDropPage.getPageTitle(page);
        await expect(pageTitle).to.equal(args.pageTitle);
      });
    });
  });

  describe('Check \'Your Account\' footer links after login', async () => {
    it('should login to FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginFO', baseContext);

      await homePage.goToLoginPage(page);
      await loginPage.customerLogin(page, DefaultCustomer);

      const isCustomerConnected = await loginPage.isCustomerConnected(page);
      await expect(isCustomerConnected, 'Customer is not connected').to.be.true;
    });

    [
      {linkSelector: 'Personal info', pageTitle: personalInfoPage.pageTitle},
      {linkSelector: 'Orders', pageTitle: ordersPage.pageTitle},
      {linkSelector: 'Credit slips', pageTitle: creditSlipsPage.pageTitle},
      {linkSelector: 'Addresses', pageTitle: addressesPage.pageTitle},
    ].forEach((args, index) => {
      it(`should check '${args.linkSelector}' footer links`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkYourAccountFooterLinks2${index}`, baseContext);

        // Check prices drop link
        await homePage.goToFooterLink(page, args.linkSelector);

        const pageTitle = await pricesDropPage.getPageTitle(page);
        await expect(pageTitle).to.equal(args.pageTitle);
      });
    });
  });
});
