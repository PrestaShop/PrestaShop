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

let browser;
let page;
let firstSupplierData;
let secondSupplierData;
let numberOfSuppliers = 0;

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    brandsPage: new BrandsPage(page),
    suppliersPage: new SuppliersPage(page),
    addSupplierPage: new AddSupplierPage(page),
  };
};

// CRUD Brand And Address
describe('Create, Update and Delete Brand and Address', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    this.pageObjects = await init();
    firstSupplierData = await (new SupplierFaker());
    secondSupplierData = await (new SupplierFaker({enabled: false}));
  });
  after(async () => {
    await helper.closeBrowser(browser);
    await Promise.all([
      files.deleteFile(firstSupplierData.logo),
      files.deleteFile(secondSupplierData.logo),
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
  // 1: Create 2 suppliers
  describe('Create 2 suppliers', async () => {
    it('should go to new supplier page', async function () {
      await this.pageObjects.suppliersPage.goToAddNewSupplierPage();
      const pageTitle = await this.pageObjects.addSupplierPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.addSupplierPage.pageTitle);
    });

    it('should create first supplier', async function () {
      const result = await this.pageObjects.addSupplierPage.createEditSupplier(firstSupplierData);
      await expect(result).to.equal(this.pageObjects.suppliersPage.successfulCreationMessage);
    });

    it('should go to new supplier page', async function () {
      await this.pageObjects.suppliersPage.goToAddNewSupplierPage();
      const pageTitle = await this.pageObjects.addSupplierPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.addSupplierPage.pageTitle);
    });

    it('should create second supplier', async function () {
      const result = await this.pageObjects.addSupplierPage.createEditSupplier(secondSupplierData);
      await expect(result).to.equal(this.pageObjects.suppliersPage.successfulCreationMessage);
    });

    it('should reset filter and get number of suppliers after creation', async function () {
      numberOfSuppliers = await this.pageObjects.suppliersPage.resetAndGetNumberOfLines();
      await expect(numberOfSuppliers).to.be.at.least(2);
    });
  });
  // 2: Filter Suppliers
  describe('Filter suppliers', async () => {
    it('should filter supplier by name', async function () {
      await this.pageObjects.suppliersPage.filterTable(
        'input',
        'name',
        firstSupplierData.name,
      );
      // Check number od suppliers
      const numberOfSuppliersAfterFilter = await this.pageObjects.suppliersPage.getNumberFromText(
        this.pageObjects.suppliersPage.gridHeaderTitle,
      );
      await expect(numberOfSuppliersAfterFilter).to.be.at.most(numberOfSuppliers);
      // check text column of first row after filter
      const textColumn = await this.pageObjects.suppliersPage.getTextContent(
        this.pageObjects.suppliersPage.tableColumn
          .replace('%ROW', 1)
          .replace('%COLUMN', 'name'),
      );
      await expect(textColumn).to.contains(firstSupplierData.name);
    });

    it('should reset filter', async function () {
      const numberOfSuppliersAfterReset = await this.pageObjects.suppliersPage.resetAndGetNumberOfLines();
      await expect(numberOfSuppliersAfterReset).to.be.equal(numberOfSuppliers);
    });

    it('should filter supplier by number of products', async function () {
      await this.pageObjects.suppliersPage.filterTable(
        'input',
        'products_count',
        firstSupplierData.products.toString(),
      );
      // Check number od suppliers
      const numberOfSuppliersAfterFilter = await this.pageObjects.suppliersPage.getNumberFromText(
        this.pageObjects.suppliersPage.gridHeaderTitle,
      );
      await expect(numberOfSuppliersAfterFilter).to.be.at.most(numberOfSuppliers);
      // check text column of first row after filter
      const textColumn = await this.pageObjects.suppliersPage.getTextContent(
        this.pageObjects.suppliersPage.tableColumn
          .replace('%ROW', 1)
          .replace('%COLUMN', 'products_count'),
      );
      await expect(textColumn).to.contains(firstSupplierData.products.toString());
    });

    it('should reset filter', async function () {
      const numberOfSuppliersAfterReset = await this.pageObjects.suppliersPage.resetAndGetNumberOfLines();
      await expect(numberOfSuppliersAfterReset).to.be.equal(numberOfSuppliers);
    });

    it('should filter supplier by enabled', async function () {
      await this.pageObjects.suppliersPage.filterSupplierEnabled(firstSupplierData.enabled);
      // Check number od suppliers
      const numberOfSuppliersAfterFilter = await this.pageObjects.suppliersPage.getNumberFromText(
        this.pageObjects.suppliersPage.gridHeaderTitle,
      );
      await expect(numberOfSuppliersAfterFilter).to.be.at.most(numberOfSuppliers);
      // check text column of first row after filter
      const textColumn = await this.pageObjects.suppliersPage.getTextContent(
        this.pageObjects.suppliersPage.tableColumn
          .replace('%ROW', 1)
          .replace('%COLUMN', 'active'),
      );
      await expect(textColumn).to.contains('check');
    });

    it('should reset filter', async function () {
      const numberOfSuppliersAfterReset = await this.pageObjects.suppliersPage.resetAndGetNumberOfLines();
      await expect(numberOfSuppliersAfterReset).to.be.equal(numberOfSuppliers);
    });
  });
  // 3: Quick Edit Suppliers
  describe('Quick edit first supplier', async () => {
    it('should filter supplier by name', async function () {
      await this.pageObjects.suppliersPage.filterTable(
        'input',
        'name',
        firstSupplierData.name,
      );
      // Check number od suppliers
      const numberOfSuppliersAfterFilter = await this.pageObjects.suppliersPage.getNumberFromText(
        this.pageObjects.suppliersPage.gridHeaderTitle,
      );
      await expect(numberOfSuppliersAfterFilter).to.be.at.most(numberOfSuppliers);
      // check text column of first row after filter
      const textColumn = await this.pageObjects.suppliersPage.getTextContent(
        this.pageObjects.suppliersPage.tableColumn
          .replace('%ROW', 1)
          .replace('%COLUMN', 'name'),
      );
      await expect(textColumn).to.contains(firstSupplierData.name);
    });

    it('should disable first supplier', async function () {
      const isActionPerformed = await this.pageObjects.suppliersPage.updateEnabledValue(
        '1',
        false,
      );
      if (isActionPerformed) {
        const resultMessage = await this.pageObjects.suppliersPage.getTextContent(
          this.pageObjects.suppliersPage.alertSuccessBlockParagraph,
        );
        await expect(resultMessage).to.contains(this.pageObjects.suppliersPage.successfulUpdateStatusMessage);
      }
      const isStatusChanged = await this.pageObjects.suppliersPage.elementVisible(
        this.pageObjects.suppliersPage.enableColumnNotValidIcon.replace('%ROW', 1),
        100,
      );
      await expect(isStatusChanged).to.be.true;
    });

    it('should enable first supplier', async function () {
      const isActionPerformed = await this.pageObjects.suppliersPage.updateEnabledValue(
        '1',
        true,
      );
      if (isActionPerformed) {
        const resultMessage = await this.pageObjects.suppliersPage.getTextContent(
          this.pageObjects.suppliersPage.alertSuccessBlockParagraph,
        );
        await expect(resultMessage).to.contains(this.pageObjects.suppliersPage.successfulUpdateStatusMessage);
      }
      const isStatusChanged = await this.pageObjects.suppliersPage.elementVisible(
        this.pageObjects.suppliersPage.enableColumnValidIcon.replace('%ROW', 1),
        100,
      );
      await expect(isStatusChanged).to.be.true;
    });

    it('should reset filter', async function () {
      const numberOfSuppliersAfterReset = await this.pageObjects.suppliersPage.resetAndGetNumberOfLines();
      await expect(numberOfSuppliersAfterReset).to.be.equal(numberOfSuppliers);
    });
  });
  // 4: Enable, disable and delete with bulk actions
  describe('Enable, disable and delete with bulk actions', async () => {
    it('should disable with bulk actions', async function () {
      const disableTextResult = await this.pageObjects.suppliersPage.changeSuppliersEnabledColumnBulkActions(false);
      await expect(disableTextResult).to.be.equal(this.pageObjects.suppliersPage.successfulUpdateStatusMessage);

      const numberOfSuppliersInGrid = await this.pageObjects.suppliersPage.getNumberFromText(
        this.pageObjects.suppliersPage.gridHeaderTitle,
      );
      await expect(numberOfSuppliersInGrid).to.be.at.most(numberOfSuppliers);
      for (let i = 1; i <= numberOfSuppliersInGrid; i++) {
        const textColumn = await this.pageObjects.suppliersPage.getTextContent(
          this.pageObjects.suppliersPage.tableColumn
            .replace('%ROW', i)
            .replace('%COLUMN', 'active'),
        );
        await expect(textColumn).to.contains('clear');
      }
    });

    it('should enable with bulk actions', async function () {
      const disableTextResult = await this.pageObjects.suppliersPage.changeSuppliersEnabledColumnBulkActions(true);
      await expect(disableTextResult).to.be.equal(this.pageObjects.suppliersPage.successfulUpdateStatusMessage);

      const numberOfSuppliersInGrid = await this.pageObjects.suppliersPage.getNumberFromText(
        this.pageObjects.suppliersPage.gridHeaderTitle,
      );
      await expect(numberOfSuppliersInGrid).to.be.at.most(numberOfSuppliers);
      for (let i = 1; i <= numberOfSuppliersInGrid; i++) {
        const textColumn = await this.pageObjects.suppliersPage.getTextContent(
          this.pageObjects.suppliersPage.tableColumn
            .replace('%ROW', i)
            .replace('%COLUMN', 'active'),
        );
        await expect(textColumn).to.contains('check');
      }
    });

    it('should delete with bulk actions', async function () {
      const deleteTextResult = await this.pageObjects.suppliersPage.deleteWithBulkActions();
      await expect(deleteTextResult).to.be.equal(this.pageObjects.suppliersPage.successfulMultiDeleteMessage);
      // Check that empty row is visible (no elements in table)
      const tableIsVisible = await this.pageObjects.suppliersPage.elementVisible(
        this.pageObjects.suppliersPage.tableEmptyRow,
        1000,
      );
      await expect(tableIsVisible).to.be.true;
    });
  });
});
