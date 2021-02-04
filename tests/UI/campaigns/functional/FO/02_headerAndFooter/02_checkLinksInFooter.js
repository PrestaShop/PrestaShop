require('module-alias/register');

const {expect} = require('chai');

const helper = require('@utils/helpers');

// Importing pages
const foHomePage = require('@pages/FO/home');
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

    await foHomePage.goToFo(page);

    const isHomePage = await foHomePage.isHomePage(page);
    await expect(isHomePage).to.be.true;
  });

  it('should check \'Products\' footer links', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkProductsFooterLinks', baseContext);

    // Check prices drop link
    await foHomePage.goToFooterLink(page, 'Prices drop');

    let pageTitle = await pricesDropPage.getPageTitle(page);
    await expect(pageTitle).to.equal(pricesDropPage.pageTitle);

    // Check new products link
    await foHomePage.goToFooterLink(page, 'New products');

    pageTitle = await newProductsPage.getPageTitle(page);
    await expect(pageTitle).to.equal(newProductsPage.pageTitle);

    // Check best sales link
    await foHomePage.goToFooterLink(page, 'Best sales');

    pageTitle = await bestSalesPage.getPageTitle(page);
    await expect(pageTitle).to.equal(bestSalesPage.pageTitle);
  });

  it('should check \'Our company\' footer links', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkOurCompanyFooterLinks', baseContext);

    // Check delivery link
    await foHomePage.goToFooterLink(page, 'Delivery');

    let pageTitle = await deliveryPage.getPageTitle(page);
    await expect(pageTitle).to.equal(deliveryPage.pageTitle);

    // Check legal notice link
    await foHomePage.goToFooterLink(page, 'Legal Notice');

    pageTitle = await legalNoticePage.getPageTitle(page);
    await expect(pageTitle).to.equal(legalNoticePage.pageTitle);

    // Check terms and conditions of use link
    await foHomePage.goToFooterLink(page, 'Terms and conditions of use');

    pageTitle = await termsAndConditionsOfUsePage.getPageTitle(page);
    await expect(pageTitle).to.equal(termsAndConditionsOfUsePage.pageTitle);

    // Check about us link
    await foHomePage.goToFooterLink(page, 'About us');

    pageTitle = await aboutUsPage.getPageTitle(page);
    await expect(pageTitle).to.equal(aboutUsPage.pageTitle);

    // Check secure payment link
    await foHomePage.goToFooterLink(page, 'Secure payment');

    pageTitle = await securePaymentPage.getPageTitle(page);
    await expect(pageTitle).to.equal(securePaymentPage.pageTitle);

    // Check contact us link
    await foHomePage.goToFooterLink(page, 'Contact us');

    pageTitle = await contactUsPage.getPageTitle(page);
    await expect(pageTitle).to.equal(contactUsPage.pageTitle);

    // Check sitemap link
    await foHomePage.goToFooterLink(page, 'Sitemap');

    pageTitle = await siteMapPage.getPageTitle(page);
    await expect(pageTitle).to.equal(siteMapPage.pageTitle);

    // Check stores link
    await foHomePage.goToFooterLink(page, 'Stores');

    pageTitle = await storesPage.getPageTitle(page);
    await expect(pageTitle).to.equal(storesPage.pageTitle);
  });
});
