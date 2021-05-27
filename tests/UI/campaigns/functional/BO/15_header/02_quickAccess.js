require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const statsPage = require('@pages/BO/stats');
const moduleManagerPage = require('@pages/BO/modules/moduleManager');
const newCategoryPage = require('@pages/BO/catalog/categories/add');
const newProductPage = require('@pages/BO/catalog/products/add');
const newVoucherPage = require('@pages/BO/catalog/discounts/add');
const ordersPage = require('@pages/BO/orders');
const quickAccessPage = require('@pages/BO/orders');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_header_quickAccess';

let browserContext;
let page;

describe('Quick access links', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  it('should login in BO', async function () {
    await loginCommon.loginBO(this, page);
  });

  [
    {args: {pageName: 'Catalog evaluation', idLink: 1, pageTitle: statsPage.pageTitle}},
    {args: {pageName: 'Installed modules', idLink: 2, pageTitle: moduleManagerPage.pageTitle}},
    {args: {pageName: 'New category', idLink: 3, pageTitle: newCategoryPage.pageTitleCreate}},
    {args: {pageName: 'New product', idLink: 4, pageTitle: newProductPage.pageTitle}},
    {args: {pageName: 'Orders', idLink: 6, pageTitle: ordersPage.pageTitle}},
    {args: {pageName: 'New voucher', idLink: 5, pageTitle: newVoucherPage.pageTitle}},
  ].forEach((test, index) => {
    it(`should check '${test.args.pageName}' link from quick access`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `checkLink${index}`, baseContext);

      await dashboardPage.quickAccessToPage(page, test.args.idLink);

      const pageTitle = await dashboardPage.getPageTitle(page);
      await expect(pageTitle).to.contains(test.args.pageTitle);
    });
  });

  it('should remove the last link \'New voucher\' from quick access', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'removeLinkFromQuickAccess', baseContext);

    const validationMessage = await dashboardPage.removeLinkFromQuickAccess(page);
    await expect(validationMessage).to.contains(dashboardPage.successfulUpdateMessage);
  });
});
