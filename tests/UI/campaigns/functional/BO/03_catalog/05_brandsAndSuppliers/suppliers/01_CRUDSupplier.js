require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const files = require('@utils/files');
const loginCommon = require('@commonTests/loginBO');

// Import data
const SupplierFaker = require('@data/faker/supplier');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const brandsPage = require('@pages/BO/catalog/brands');
const suppliersPage = require('@pages/BO/catalog/suppliers');
const addSupplierPage = require('@pages/BO/catalog/suppliers/add');
const viewSupplierPage = require('@pages/BO/catalog/suppliers/view');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_catalog_brandsAndSuppliers_suppliers_CRUDSupplier';

let browserContext;
let page;

const createSupplierData = new SupplierFaker();
const editSupplierData = new SupplierFaker();

// CRUD Supplier
describe('Create, update and delete supplier', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    // Generate logos
    await Promise.all([
      files.generateImage(createSupplierData.logo),
      files.generateImage(editSupplierData.logo),
    ]);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);

    await Promise.all([
      files.deleteFile(createSupplierData.logo),
      files.deleteFile(editSupplierData.logo),
    ]);
  });

  it('should login in BO', async function () {
    await loginCommon.loginBO(this, page);
  });

  // Go to brands page
  it('should go to brands page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToBrandsPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.catalogParentLink,
      dashboardPage.brandsAndSuppliersLink,
    );

    await brandsPage.closeSfToolBar(page);

    const pageTitle = await brandsPage.getPageTitle(page);
    await expect(pageTitle).to.contains(brandsPage.pageTitle);
  });

  // Go to suppliers page
  it('should go to suppliers page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToSuppliersPage', baseContext);

    await brandsPage.goToSubTabSuppliers(page);
    const pageTitle = await suppliersPage.getPageTitle(page);
    await expect(pageTitle).to.contains(suppliersPage.pageTitle);
  });

  // 1: Create supplier
  describe('Create supplier', async () => {
    it('should go to new supplier page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddSupplierPage', baseContext);

      await suppliersPage.goToAddNewSupplierPage(page);
      const pageTitle = await addSupplierPage.getPageTitle(page);
      await expect(pageTitle).to.contains(addSupplierPage.pageTitle);
    });

    it('should create supplier', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createSupplier', baseContext);

      const result = await addSupplierPage.createEditSupplier(page, createSupplierData);
      await expect(result).to.equal(suppliersPage.successfulCreationMessage);
    });
  });

  // 2: View supplier
  describe('View supplier', async () => {
    it('should filter suppliers by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToViewCreatedSupplier', baseContext);

      await suppliersPage.filterTable(page, 'input', 'name', createSupplierData.name);

      const textColumn = await suppliersPage.getTextColumnFromTableSupplier(page, 1, 'name');
      await expect(textColumn).to.contain(createSupplierData.name);
    });

    it('should view supplier', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewCreatedSupplier', baseContext);

      // view supplier first row
      await suppliersPage.viewSupplier(page, 1);
      const pageTitle = await viewSupplierPage.getPageTitle(page);
      await expect(pageTitle).to.contains(createSupplierData.name);
    });

    it('should return suppliers page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'returnToSuppliersPageAfterCreation', baseContext);

      await viewSupplierPage.goToPreviousPage(page);
      const pageTitle = await suppliersPage.getPageTitle(page);
      await expect(pageTitle).to.contains(suppliersPage.pageTitle);
    });
  });

  // 3: Update supplier
  describe('Update supplier', async () => {
    it('should go to edit first supplier page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditSupplierPage', baseContext);

      await suppliersPage.goToEditSupplierPage(page, 1);
      const pageTitle = await addSupplierPage.getPageTitle(page);
      await expect(pageTitle).to.contains(addSupplierPage.pageTitleEdit);
    });

    it('should edit supplier', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateSupplier', baseContext);

      const result = await addSupplierPage.createEditSupplier(page, editSupplierData);
      await expect(result).to.equal(suppliersPage.successfulUpdateMessage);
    });
  });

  // 4: View supplier
  describe('View edited supplier', async () => {
    it('should filter suppliers by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToViewUpdatedSupplier', baseContext);

      await suppliersPage.resetFilter(page);
      await suppliersPage.filterTable(page, 'input', 'name', editSupplierData.name);

      const textColumn = await suppliersPage.getTextColumnFromTableSupplier(page, 1, 'name');
      await expect(textColumn).to.contain(editSupplierData.name);
    });

    it('should view supplier', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewUpdatedSupplier', baseContext);

      // view supplier first row
      await suppliersPage.viewSupplier(page, 1);
      const pageTitle = await viewSupplierPage.getPageTitle(page);
      await expect(pageTitle).to.contains(editSupplierData.name);
    });

    it('should return to suppliers page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'returnToSuppliersPageAfterUpdate', baseContext);

      await viewSupplierPage.goToPreviousPage(page);
      const pageTitle = await suppliersPage.getPageTitle(page);
      await expect(pageTitle).to.contains(suppliersPage.pageTitle);
    });
  });

  // 5: Delete supplier
  describe('Delete supplier', async () => {
    it('should filter suppliers by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToViewDeleteSupplier', baseContext);

      await suppliersPage.resetFilter(page);
      await suppliersPage.filterTable(page, 'input', 'name', editSupplierData.name);

      const textColumn = await suppliersPage.getTextColumnFromTableSupplier(page, 1, 'name');
      await expect(textColumn).to.contain(editSupplierData.name);
    });

    it('should delete supplier', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteSupplier', baseContext);

      // delete supplier in first row
      const result = await suppliersPage.deleteSupplier(page, 1);
      await expect(result).to.be.equal(suppliersPage.successfulDeleteMessage);
    });
  });
});
