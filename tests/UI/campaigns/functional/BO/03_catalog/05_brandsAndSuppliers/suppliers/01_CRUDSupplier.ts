import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boBrandsPage,
  boDashboardPage,
  boLoginPage,
  boSuppliersCreatePage,
  boSuppliersPage,
  boSuppliersViewPage,
  type BrowserContext,
  FakerSupplier,
  type Page,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_catalog_brandsAndSuppliers_suppliers_CRUDSupplier';

// CRUD Supplier
describe('BO - Catalog - Brands & Suppliers : CRUD supplier', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const createSupplierData: FakerSupplier = new FakerSupplier({country: 'France'});
  const editSupplierData: FakerSupplier = new FakerSupplier({country: 'United States'});

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);

    // Generate logos
    await Promise.all([
      utilsFile.generateImage(createSupplierData.logo),
      utilsFile.generateImage(editSupplierData.logo),
    ]);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);

    await Promise.all([
      utilsFile.deleteFile(createSupplierData.logo),
      utilsFile.deleteFile(editSupplierData.logo),
    ]);
  });

  it('should login in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

    await boLoginPage.goTo(page, global.BO.URL);
    await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

    const pageTitle = await boDashboardPage.getPageTitle(page);
    expect(pageTitle).to.contains(boDashboardPage.pageTitle);
  });

  // Go to brands page
  it('should go to \'Catalog > Brands & Suppliers\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToBrandsPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.catalogParentLink,
      boDashboardPage.brandsAndSuppliersLink,
    );
    await boBrandsPage.closeSfToolBar(page);

    const pageTitle = await boBrandsPage.getPageTitle(page);
    expect(pageTitle).to.contains(boBrandsPage.pageTitle);
  });

  // Go to suppliers page
  it('should go to Suppliers page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToSuppliersPage', baseContext);

    await boBrandsPage.goToSubTabSuppliers(page);

    const pageTitle = await boSuppliersPage.getPageTitle(page);
    expect(pageTitle).to.contains(boSuppliersPage.pageTitle);
  });

  // 1: Create supplier
  describe('Create supplier', async () => {
    it('should go to new supplier page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddSupplierPage', baseContext);

      await boSuppliersPage.goToAddNewSupplierPage(page);

      const pageTitle = await boSuppliersCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boSuppliersCreatePage.pageTitle);
    });

    it('should create supplier', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createSupplier', baseContext);

      const result = await boSuppliersCreatePage.createEditSupplier(page, createSupplierData);
      expect(result).to.equal(boSuppliersPage.successfulCreationMessage);
    });
  });

  // 2: View supplier
  describe('View supplier', async () => {
    it('should filter suppliers by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToViewCreatedSupplier', baseContext);

      await boSuppliersPage.filterTable(page, 'input', 'name', createSupplierData.name);

      const textColumn = await boSuppliersPage.getTextColumnFromTableSupplier(page, 1, 'name');
      expect(textColumn).to.contain(createSupplierData.name);
    });

    it('should view supplier', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewCreatedSupplier', baseContext);

      // view supplier first row
      await boSuppliersPage.viewSupplier(page, 1);

      const pageTitle = await boSuppliersViewPage.getPageTitle(page);
      expect(pageTitle).to.contains(createSupplierData.name);
    });

    it('should return suppliers page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'returnToSuppliersPageAfterCreation', baseContext);

      await boSuppliersViewPage.goToPreviousPage(page);

      const pageTitle = await boSuppliersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boSuppliersPage.pageTitle);
    });
  });

  // 3: Update supplier
  describe('Update supplier', async () => {
    it('should go to edit first supplier page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditSupplierPage', baseContext);

      await boSuppliersPage.goToEditSupplierPage(page, 1);

      const pageTitle = await boSuppliersCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boSuppliersCreatePage.pageTitleEdit);
    });

    it('should edit supplier', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateSupplier', baseContext);

      const result = await boSuppliersCreatePage.createEditSupplier(page, editSupplierData);
      expect(result).to.equal(boSuppliersPage.successfulUpdateMessage);
    });
  });

  // 4: View supplier
  describe('View edited supplier', async () => {
    it('should filter suppliers by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToViewUpdatedSupplier', baseContext);

      await boSuppliersPage.resetFilter(page);
      await boSuppliersPage.filterTable(page, 'input', 'name', editSupplierData.name);

      const textColumn = await boSuppliersPage.getTextColumnFromTableSupplier(page, 1, 'name');
      expect(textColumn).to.contain(editSupplierData.name);
    });

    it('should view supplier', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewUpdatedSupplier', baseContext);

      // view supplier first row
      await boSuppliersPage.viewSupplier(page, 1);

      const pageTitle = await boSuppliersViewPage.getPageTitle(page);
      expect(pageTitle).to.contains(editSupplierData.name);
    });

    it('should return to suppliers page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'returnToSuppliersPageAfterUpdate', baseContext);

      await boSuppliersViewPage.goToPreviousPage(page);

      const pageTitle = await boSuppliersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boSuppliersPage.pageTitle);
    });
  });

  // 5: Delete supplier
  describe('Delete supplier', async () => {
    it('should filter suppliers by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToViewDeleteSupplier', baseContext);

      await boSuppliersPage.resetFilter(page);
      await boSuppliersPage.filterTable(page, 'input', 'name', editSupplierData.name);

      const textColumn = await boSuppliersPage.getTextColumnFromTableSupplier(page, 1, 'name');
      expect(textColumn).to.contain(editSupplierData.name);
    });

    it('should delete supplier', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteSupplier', baseContext);

      // delete supplier in first row
      const result = await boSuppliersPage.deleteSupplier(page, 1);
      expect(result).to.be.equal(boSuppliersPage.successfulDeleteMessage);
    });
  });
});
