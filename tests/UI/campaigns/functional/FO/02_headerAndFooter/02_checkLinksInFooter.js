require('module-alias/register');

const {expect} = require('chai');

const helper = require('@utils/helpers');

// Importing pages
const foHomePage = require('@pages/FO/home');
const pricesDropPage = require('@pages/FO/pricesDrop');
const newProductsPage = require('@pages/FO/newProducts');
const bestSalesPage = require('@pages/FO/bestSales');

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

  it('should check products footer links', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkProductsFooterLinks', baseContext);

    // Check prices drop page
    await foHomePage.goToFooterLink(page, 'Prices drop');

    let pageTitle = await pricesDropPage.getPageTitle(page);
    await expect(pageTitle).to.equal(pricesDropPage.pageTitle);

    // Check new products page
    await foHomePage.goToFooterLink(page, 'New products');

    pageTitle = await newProductsPage.getPageTitle(page);
    await expect(pageTitle).to.equal(newProductsPage.pageTitle);

    // Check best sales page
    await foHomePage.goToFooterLink(page, 'Best sales');

    pageTitle = await bestSalesPage.getPageTitle(page);
    await expect(pageTitle).to.equal(bestSalesPage.pageTitle);
  });
});
