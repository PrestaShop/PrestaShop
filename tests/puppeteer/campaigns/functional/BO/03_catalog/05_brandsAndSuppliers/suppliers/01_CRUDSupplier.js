require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const files = require('@utils/files');
const loginCommon = require('@commonTests/loginBO');

// Import data
const SupplierFaker = require('@data/faker/supplier');

// Import pages
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const BrandsPage = require('@pages/BO/catalog/brands');
const SuppliersPage = require('@pages/BO/catalog/suppliers');
const AddSupplierPage = require('@pages/BO/catalog/suppliers/add');
const ViewSupplierPage = require('@pages/BO/catalog/suppliers/view');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_catalog_brandsAndSuppliers_suppliers_CRUDSupplier';

let browser;
let page;

let createSupplierData;
let editSupplierData;

// Init objects needed
const init = async function () {
  return {
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    brandsPage: new BrandsPage(page),
    suppliersPage: new SuppliersPage(page),
    addSupplierPage: new AddSupplierPage(page),
    viewSupplierPage: new ViewSupplierPage(page),
  };
};

// CRUD Supplier
describe('Create, update and delete supplier', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);

    this.pageObjects = await init();

    createSupplierData = await (new SupplierFaker());
    editSupplierData = await (new SupplierFaker());
  });

  after(async () => {
    await helper.closeBrowser(browser);

    await Promise.all([
      files.deleteFile(createSupplierData.logo),
      files.deleteFile(editSupplierData.logo),
    ]);
  });

  // Login into BO
  loginCommon.loginBO();

  // Go to brands page
  it('should go to brands page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToBrandsPage', baseContext);

    await this.pageObjects.dashboardPage.goToSubMenu(
      this.pageObjects.dashboardPage.catalogParentLink,
      this.pageObjects.dashboardPage.brandsAndSuppliersLink,
    );

    await this.pageObjects.brandsPage.closeSfToolBar();

    const pageTitle = await this.pageObjects.brandsPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.brandsPage.pageTitle);
  });

  // Go to suppliers page
  it('should go to suppliers page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToSuppliersPage', baseContext);

    await this.pageObjects.brandsPage.goToSubTabSuppliers();
    const pageTitle = await this.pageObjects.suppliersPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.suppliersPage.pageTitle);
  });

  // 1: Create supplier
  describe('Create supplier', async () => {
    it('should go to new supplier page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddSupplierPage', baseContext);

      await this.pageObjects.suppliersPage.goToAddNewSupplierPage();
      const pageTitle = await this.pageObjects.addSupplierPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.addSupplierPage.pageTitle);
    });

    it('should create supplier', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createSupplier', baseContext);

      const result = await this.pageObjects.addSupplierPage.createEditSupplier(createSupplierData);
      await expect(result).to.equal(this.pageObjects.suppliersPage.successfulCreationMessage);
    });
  });

  // 2: View supplier
  describe('View supplier', async () => {
    it('should view supplier', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewCreatedSupplier', baseContext);

      // view supplier first row
      await this.pageObjects.suppliersPage.viewSupplier(1);
      const pageTitle = await this.pageObjects.viewSupplierPage.getPageTitle();
      await expect(pageTitle).to.contains(createSupplierData.name);
    });

    it('should return suppliers page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'returnToSuppliersPageAfterCreation', baseContext);

      await this.pageObjects.viewSupplierPage.goToPreviousPage();
      const pageTitle = await this.pageObjects.suppliersPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.suppliersPage.pageTitle);
    });
  });

  // 3: Update supplier
  describe('Update supplier', async () => {
    it('should go to edit first supplier page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditSupplierPage', baseContext);

      await this.pageObjects.suppliersPage.goToEditSupplierPage(1);
      const pageTitle = await this.pageObjects.addSupplierPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.addSupplierPage.pageTitleEdit);
    });

    it('should edit supplier', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateSupplier', baseContext);

      const result = await this.pageObjects.addSupplierPage.createEditSupplier(editSupplierData);
      await expect(result).to.equal(this.pageObjects.suppliersPage.successfulUpdateMessage);
    });
  });

  // 4: View supplier
  describe('View edited supplier', async () => {
    it('should view supplier', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewUpdatedSupplier', baseContext);

      // view supplier first row
      await this.pageObjects.suppliersPage.viewSupplier(1);
      const pageTitle = await this.pageObjects.viewSupplierPage.getPageTitle();
      await expect(pageTitle).to.contains(editSupplierData.name);
    });

    it('should return to suppliers page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'returnToSuppliersPageAfterUpdate', baseContext);

      await this.pageObjects.viewSupplierPage.goToPreviousPage();
      const pageTitle = await this.pageObjects.suppliersPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.suppliersPage.pageTitle);
    });
  });

  // 5: Delete supplier
  describe('Delete supplier', async () => {
    it('should delete supplier', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteSupplier', baseContext);

      // delete supplier in first row
      const result = await this.pageObjects.suppliersPage.deleteSupplier(1);
      await expect(result).to.be.equal(this.pageObjects.suppliersPage.successfulDeleteMessage);
    });
  });
});
