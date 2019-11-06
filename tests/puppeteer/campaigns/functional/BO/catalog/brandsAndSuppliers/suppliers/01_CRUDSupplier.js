require('module-alias/register');
const {expect} = require('chai');
const helper = require('@utils/helpers');
const files = require('@utils/files');
const loginCommon = require('@commonTests/loginBO');
const SupplierFaker = require('@data/faker/supplier');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const BrandsPage = require('@pages/BO/brands');
const SuppliersPage = require('@pages/BO/suppliers');
const AddSupplierPage = require('@pages/BO/addSupplier');
const ViewSupplierPage = require('@pages/BO/viewSupplier');

let browser;
let page;
let createSupplierData;
let editSupplierData;

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    brandsPage: new BrandsPage(page),
    suppliersPage: new SuppliersPage(page),
    addSupplierPage: new AddSupplierPage(page),
    viewSupplierPage: new ViewSupplierPage(page),
  };
};

// CRUD Brand And Address
describe('Create, Update and Delete Brand and Address', async () => {
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

  // Login into BO and go to brands page
  loginCommon.loginBO();

  // GO to brands Page
  it('should go to brands page', async function () {
    await this.pageObjects.boBasePage.goToSubMenu(
      this.pageObjects.boBasePage.catalogParentLink,
      this.pageObjects.boBasePage.brandsAndSuppliersLink,
    );
    await this.pageObjects.boBasePage.closeSfToolBar();
    const pageTitle = await this.pageObjects.brandsPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.brandsPage.pageTitle);
  });

  // GO to Suppliers Page
  it('should go to suppliers page', async function () {
    await this.pageObjects.brandsPage.goToSubTabSuppliers();
    const pageTitle = await this.pageObjects.suppliersPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.suppliersPage.pageTitle);
  });
  // 1: Create supplier
  describe('Create Supplier', async () => {
    it('should go to new supplier page', async function () {
      await this.pageObjects.suppliersPage.goToAddNewSupplierPage();
      const pageTitle = await this.pageObjects.addSupplierPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.addSupplierPage.pageTitle);
    });

    it('should create supplier', async function () {
      const result = await this.pageObjects.addSupplierPage.createEditSupplier(createSupplierData);
      await expect(result).to.equal(this.pageObjects.suppliersPage.successfulCreationMessage);
    });
  });
  // 2: View Supplier
  describe('View Supplier', async () => {
    it('should view Supplier', async function () {
      await this.pageObjects.suppliersPage.viewSupplier(1);
      const pageTitle = await this.pageObjects.viewSupplierPage.getPageTitle();
      await expect(pageTitle).to.contains(createSupplierData.name);
    });

    it('should return Supplier Page', async function () {
      await this.pageObjects.viewSupplierPage.goToPreviousPage();
      const pageTitle = await this.pageObjects.suppliersPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.suppliersPage.pageTitle);
    });
  });
  // 3: Update Supplier
  describe('Update Supplier', async () => {
    it('Edit first Supplier', async function () {
      await this.pageObjects.suppliersPage.goToEditSupplierPage(1);
      const pageTitle = await this.pageObjects.addSupplierPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.addSupplierPage.pageTitleEdit);
    });

    it('should edit supplier', async function () {
      const result = await this.pageObjects.addSupplierPage.createEditSupplier(editSupplierData);
      await expect(result).to.equal(this.pageObjects.suppliersPage.successfulUpdateMessage);
    });
  });
  // 4: View Supplier
  describe('View Brand and check Address Value in list', async () => {
    it('should view brand', async function () {
      await this.pageObjects.suppliersPage.viewSupplier(1);
      const pageTitle = await this.pageObjects.viewSupplierPage.getPageTitle();
      await expect(pageTitle).to.contains(editSupplierData.name);
    });

    it('should return Supplier Page', async function () {
      await this.pageObjects.viewSupplierPage.goToPreviousPage();
      const pageTitle = await this.pageObjects.suppliersPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.suppliersPage.pageTitle);
    });
  });
  // 5: Delete Brand And Verify that Address has no Brand associated
  describe('Delete Supplier', async () => {
    it('should Delete brand', async function () {
      const result = await this.pageObjects.suppliersPage.deleteSupplier(1);
      await expect(result).to.be.equal(this.pageObjects.suppliersPage.successfulDeleteMessage);
    });
  });
});
