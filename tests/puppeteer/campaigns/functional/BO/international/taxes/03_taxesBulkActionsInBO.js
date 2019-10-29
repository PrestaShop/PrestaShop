require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const TaxesPage = require('@pages/BO/taxes');
const AddTaxPage = require('@pages/BO/addTax');
const TaxFaker = require('@data/faker/tax');

let browser;
let page;
let numberOfTaxes = 0;
let firstTaxData;
let secondTaxData;

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    taxesPage: new TaxesPage(page),
    addTaxPage: new AddTaxPage(page),
  };
};

// Create taxes, Then disable / Enable and Delete with Bulk actions
describe('Create Taxes, Then disable / Enable and Delete with Bulk actions', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    this.pageObjects = await init();
    firstTaxData = await (new TaxFaker({name: 'TVA to delete'}));
    secondTaxData = await (new TaxFaker({name: 'TVA to delete2'}));
  });
  after(async () => {
    await helper.closeBrowser(browser);
  });
  // Login into BO and go to Taxes page
  loginCommon.loginBO();
  it('should go to Taxes page', async function () {
    await this.pageObjects.boBasePage.goToSubMenu(
      this.pageObjects.boBasePage.internationalParentLink,
      this.pageObjects.boBasePage.taxesLink,
    );
    const pageTitle = await this.pageObjects.taxesPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.taxesPage.pageTitle);
  });
  it('should reset all filters', async function () {
    numberOfTaxes = await this.pageObjects.taxesPage.resetFilter();
    await expect(numberOfTaxes).to.be.above(0);
  });
  // 1 : Create 2 taxes with data from faker
  describe('Create 2 Taxes in BO', async () => {
    it('should go to add new tax page', async function () {
      await this.pageObjects.taxesPage.goToAddNewTaxPage();
      const pageTitle = await this.pageObjects.addTaxPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.addTaxPage.pageTitleCreate);
    });
    it('should create first tax and check result', async function () {
      const textResult = await this.pageObjects.addTaxPage.createEditTax(firstTaxData);
      await expect(textResult).to.equal(this.pageObjects.taxesPage.successfulCreationMessage);
      const numberOfTaxesAfterCreation = await this.pageObjects.taxesPage.getNumberFromText(
        this.pageObjects.taxesPage.gridHeaderTitle,
      );
      await expect(numberOfTaxesAfterCreation).to.be.equal(numberOfTaxes + 1);
    });
    it('should go to add new tax page', async function () {
      await this.pageObjects.taxesPage.goToAddNewTaxPage();
      const pageTitle = await this.pageObjects.addTaxPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.addTaxPage.pageTitleCreate);
    });
    it('should create second tax and check result', async function () {
      const textResult = await this.pageObjects.addTaxPage.createEditTax(secondTaxData);
      await expect(textResult).to.equal(this.pageObjects.taxesPage.successfulCreationMessage);
      const numberOfTaxesAfterCreation = await this.pageObjects.taxesPage.getNumberFromText(
        this.pageObjects.taxesPage.gridHeaderTitle,
      );
      await expect(numberOfTaxesAfterCreation).to.be.equal(numberOfTaxes + 2);
    });
  });
  // 2 : Enable/Disable with bulk actions
  describe('Enable and Disable Taxes with Bulk Actions', async () => {
    it('should filter list by name', async function () {
      await this.pageObjects.taxesPage.filterTaxes(
        'input',
        'name',
        'TVA to delete',
      );
      const textResult = await this.pageObjects.taxesPage.getTextContent(
        this.pageObjects.taxesPage.taxesGridColumn.replace('%ROW', '1').replace('%COLUMN', 'name'),
      );
      await expect(textResult).to.contains('TVA to delete');
    });
    it('should disable Taxes with Bulk Actions and check Result', async function () {
      const disableTextResult = await this.pageObjects.taxesPage.changeTaxesEnabledColumnBulkActions(false);
      await expect(disableTextResult).to.be.equal(this.pageObjects.taxesPage.successfulUpdateStatusMessage);
      const numberOfTaxesInGrid = await this.pageObjects.taxesPage.getNumberFromText(
        this.pageObjects.taxesPage.gridHeaderTitle,
      );
      await expect(numberOfTaxesInGrid).to.be.at.most(numberOfTaxes);
      /* eslint-disable no-await-in-loop */
      for (let i = 1; i <= numberOfTaxesInGrid; i++) {
        const textColumn = await this.pageObjects.taxesPage.getTextContent(
          this.pageObjects.taxesPage.taxesGridColumn.replace('%ROW', i).replace('%COLUMN', 'active'),
        );
        await expect(textColumn).to.contains('clear');
      }
      /* eslint-enable no-await-in-loop */
    });
    it('should enable Taxes with Bulk Actions and check Result', async function () {
      const enableTextResult = await this.pageObjects.taxesPage.changeTaxesEnabledColumnBulkActions(true);
      await expect(enableTextResult).to.be.equal(this.pageObjects.taxesPage.successfulUpdateStatusMessage);
      const numberOfTaxesInGrid = await this.pageObjects.taxesPage.getNumberFromText(
        this.pageObjects.taxesPage.gridHeaderTitle,
      );
      await expect(numberOfTaxesInGrid).to.be.at.most(numberOfTaxes);
      /* eslint-disable no-await-in-loop */
      for (let i = 1; i <= numberOfTaxesInGrid; i++) {
        const textColumn = await this.pageObjects.taxesPage.getTextContent(
          this.pageObjects.taxesPage.taxesGridColumn.replace('%ROW', i).replace('%COLUMN', 'active'),
        );
        await expect(textColumn).to.contains('check');
      }
      /* eslint-enable no-await-in-loop */
    });
    it('should reset all filters', async function () {
      const numberOfTaxesAfterReset = await this.pageObjects.taxesPage.resetFilter();
      await expect(numberOfTaxesAfterReset).to.be.equal(numberOfTaxes + 2);
    });
  });
  // 3 : Delete with bulk actions
  describe('Delete Taxes with Bulk Actions', async () => {
    it('should filter list by name', async function () {
      await this.pageObjects.taxesPage.filterTaxes(
        'input',
        'name',
        'TVA to delete',
      );
      const textResult = await this.pageObjects.taxesPage.getTextContent(
        this.pageObjects.taxesPage.taxesGridColumn.replace('%ROW', '1').replace('%COLUMN', 'name'),
      );
      await expect(textResult).to.contains('TVA to delete');
    });
    it('should delete Taxes with Bulk Actions and check Result', async function () {
      const deleteTextResult = await this.pageObjects.taxesPage.deleteTaxesBulkActions();
      await expect(deleteTextResult).to.be.equal(this.pageObjects.taxesPage.successfulDeleteMessage);
      const numberOfTaxesAfterReset = await this.pageObjects.taxesPage.resetFilter();
      await expect(numberOfTaxesAfterReset).to.be.equal(numberOfTaxes);
    });
  });
});
