require('module-alias/register');

const {expect} = require('chai');

const helper = require('@utils/helpers');

// Importing pages
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

describe('Check links in footer page', async () => {
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

  it('should check \'Products\' footer links', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkProductsFooterLinks', baseContext);

    // Check prices drop link
    await homePage.goToFooterLink(page, 'Prices drop');

    let pageTitle = await pricesDropPage.getPageTitle(page);
    await expect(pageTitle).to.equal(pricesDropPage.pageTitle);

    // Check new products link
    await homePage.goToFooterLink(page, 'New products');

    pageTitle = await newProductsPage.getPageTitle(page);
    await expect(pageTitle).to.equal(newProductsPage.pageTitle);

    // Check best sales link
    await homePage.goToFooterLink(page, 'Best sales');

    pageTitle = await bestSalesPage.getPageTitle(page);
    await expect(pageTitle).to.equal(bestSalesPage.pageTitle);
  });

  it('should check \'Our Company\' footer links', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkOurCompanyFooterLinks', baseContext);

    // Check delivery link
    await homePage.goToFooterLink(page, 'Delivery');

    let pageTitle = await deliveryPage.getPageTitle(page);
    await expect(pageTitle).to.equal(deliveryPage.pageTitle);

    // Check legal notice link
    await homePage.goToFooterLink(page, 'Legal Notice');

    pageTitle = await legalNoticePage.getPageTitle(page);
    await expect(pageTitle).to.equal(legalNoticePage.pageTitle);

    // Check terms and conditions of use link
    await homePage.goToFooterLink(page, 'Terms and conditions of use');

    pageTitle = await termsAndConditionsOfUsePage.getPageTitle(page);
    await expect(pageTitle).to.equal(termsAndConditionsOfUsePage.pageTitle);

    // Check about us link
    await homePage.goToFooterLink(page, 'About us');

    pageTitle = await aboutUsPage.getPageTitle(page);
    await expect(pageTitle).to.equal(aboutUsPage.pageTitle);

    // Check secure payment link
    await homePage.goToFooterLink(page, 'Secure payment');

    pageTitle = await securePaymentPage.getPageTitle(page);
    await expect(pageTitle).to.equal(securePaymentPage.pageTitle);

    // Check contact us link
    await homePage.goToFooterLink(page, 'Contact us');

    pageTitle = await contactUsPage.getPageTitle(page);
    await expect(pageTitle).to.equal(contactUsPage.pageTitle);

    // Check sitemap link
    await homePage.goToFooterLink(page, 'Sitemap');

    pageTitle = await siteMapPage.getPageTitle(page);
    await expect(pageTitle).to.equal(siteMapPage.pageTitle);

    // Check stores link
    await homePage.goToFooterLink(page, 'Stores');

    pageTitle = await storesPage.getPageTitle(page);
    await expect(pageTitle).to.equal(storesPage.pageTitle);
  });

  it('should check \'Your Account\' footer links before login', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkYourAccountFooterLinks1', baseContext);

    // Check personal info link
    await homePage.goToFooterLink(page, 'Personal info');

    let pageTitle = await loginPage.getPageTitle(page);
    await expect(pageTitle).to.equal(loginPage.pageTitle);

    // Check orders link
    await homePage.goToFooterLink(page, 'Orders');

    pageTitle = await loginPage.getPageTitle(page);
    await expect(pageTitle).to.equal(loginPage.pageTitle);

    // Check credit slips link
    await homePage.goToFooterLink(page, 'Credit slips');

    pageTitle = await loginPage.getPageTitle(page);
    await expect(pageTitle).to.equal(loginPage.pageTitle);

    // Check addresses link
    await homePage.goToFooterLink(page, 'Addresses');

    pageTitle = await loginPage.getPageTitle(page);
    await expect(pageTitle).to.equal(loginPage.pageTitle);
  });

  it('should check \'Your Account\' footer links after login', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkYourAccountFooterLinks2', baseContext);

    // Login FO
    await homePage.goToLoginPage(page);
    await loginPage.customerLogin(page, DefaultCustomer);

    const isCustomerConnected = await loginPage.isCustomerConnected(page);
    await expect(isCustomerConnected, 'Customer is not connected').to.be.true;

    // Check personal info link
    await homePage.goToFooterLink(page, 'Personal info');

    let pageTitle = await personalInfoPage.getPageTitle(page);
    await expect(pageTitle).to.equal(personalInfoPage.pageTitle);

    // Check orders link
    await homePage.goToFooterLink(page, 'Orders');

    pageTitle = await ordersPage.getPageTitle(page);
    await expect(pageTitle).to.equal(ordersPage.pageTitle);

    // Check credit slips link
    await homePage.goToFooterLink(page, 'Credit slips');

    pageTitle = await creditSlipsPage.getPageTitle(page);
    await expect(pageTitle).to.equal(creditSlipsPage.pageTitle);

    // Check addresses link
    await homePage.goToFooterLink(page, 'Addresses');

    pageTitle = await addressesPage.getPageTitle(page);
    await expect(pageTitle).to.equal(addressesPage.pageTitle);
  });
});
