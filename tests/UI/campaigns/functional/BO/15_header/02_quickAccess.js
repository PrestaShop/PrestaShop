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
const quickAccessPage = require('@pages/BO/quickAccess');
const addNewQuickAccessPage = require('@pages/BO/quickAccess/add');
const newCustomerPage = require('@pages/BO/customers/add');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_header_quickAccess';

let browserContext;
let page;

const quickAccessLinkData = {name: 'New customer', url: 'index.php/sell/customers/new', openNewWindow: false};

describe('Header : Quick access links', async () => {
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
    it(`should check '${test.args.pageName}' link from Quick access`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `checkLink${index}`, baseContext);

      await dashboardPage.quickAccessToPage(page, test.args.idLink);

      const pageTitle = await dashboardPage.getPageTitle(page);
      await expect(pageTitle).to.contains(test.args.pageTitle);
    });
  });

  it('should remove the last link \'New voucher\' from Quick access', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'removeLinkFromQuickAccess', baseContext);

    const validationMessage = await newVoucherPage.removeLinkFromQuickAccess(page);
    await expect(validationMessage).to.contains(newVoucherPage.successfulUpdateMessage);
  });

  it('should refresh the page and add current page to Quick access', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'addCurrentPageToQuickAccess', baseContext);

    await newVoucherPage.reloadPage(page);

    const validationMessage = await newVoucherPage.addCurrentPageToQuickAccess(page, 'New voucher');
    await expect(validationMessage).to.contains(newVoucherPage.successfulUpdateMessage);
  });

  it('should go to \'Manage quick access\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToManageQuickAccessPageToCreateLink', baseContext);

    await newVoucherPage.reloadPage(page);

    await newVoucherPage.manageQuickAccess(page);

    const pageTitle = await quickAccessPage.getPageTitle(page);
    await expect(pageTitle).to.contains(quickAccessPage.pageTitle);
  });

  it('should go to \'Add new quick access\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToAddQuickAccessPage', baseContext);

    await quickAccessPage.goToAddNewQuickAccessPage(page);

    const pageTitle = await addNewQuickAccessPage.getPageTitle(page);
    await expect(pageTitle).to.contains(addNewQuickAccessPage.pageTitle);
  });

  it('should create new quick access link', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'createQuickAccessLink', baseContext);

    const validationMessage = await addNewQuickAccessPage.setQuickAccessLink(page, quickAccessLinkData);
    await expect(validationMessage).to.contains(addNewQuickAccessPage.successfulCreationMessage);
  });

  it('should check the new link from Quick access', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkNewLink', baseContext);

    await dashboardPage.quickAccessToPage(page, 4);

    const pageTitle = await newCustomerPage.getPageTitle(page);
    await expect(pageTitle).to.contains(newCustomerPage.pageTitleCreate);
  });

  it('should go to \'Manage quick access\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToManageQuickAccessPageToDeleteLink', baseContext);

    await newCustomerPage.manageQuickAccess(page);

    const pageTitle = await quickAccessPage.getPageTitle(page);
    await expect(pageTitle).to.contains(quickAccessPage.pageTitle);
  });

  it('should filter quick access table by link name', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'searchByName', baseContext);

    await quickAccessPage.filterTable(page, 'input', 'name', quickAccessLinkData.name);

    const textColumn = await quickAccessPage.getTextColumn(page, 1, 'name');
    await expect(textColumn).to.contains(quickAccessLinkData.name);
  });

  it('should delete the created quick access link by bulk actions', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'deleteByBulkActions', baseContext);

    const textColumn = await quickAccessPage.bulkDeleteQuickAccessLink(page);
    await expect(textColumn).to.be.contains(quickAccessPage.successfulMultiDeleteMessage);
  });
});
