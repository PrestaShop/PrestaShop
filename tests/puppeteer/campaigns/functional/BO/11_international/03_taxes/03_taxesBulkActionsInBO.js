require('module-alias/register');

const {expect} = require('chai');

const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const TaxesPage = require('@pages/BO/international/taxes');
const AddTaxPage = require('@pages/BO/international/taxes/add');
const TaxFaker = require('@data/faker/tax');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_international_localization_taxes_taxesBulkActions';

let browser;
let page;

let numberOfTaxes = 0;

const firstTaxData = new TaxFaker({name: 'TVA to delete'});
const secondTaxData = new TaxFaker({name: 'TVA to delete2'});

// Init objects needed
const init = async function () {
  return {
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
  });

  after(async () => {
    await helper.closeBrowser(browser);
  });

  // Login into BO and go to Taxes page
  loginCommon.loginBO();

  it('should go to Taxes page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToTaxesPage', baseContext);

    await this.pageObjects.dashboardPage.goToSubMenu(
      this.pageObjects.dashboardPage.internationalParentLink,
      this.pageObjects.dashboardPage.taxesLink,
    );

    const pageTitle = await this.pageObjects.taxesPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.taxesPage.pageTitle);
  });

  it('should reset all filters', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);
    numberOfTaxes = await this.pageObjects.taxesPage.resetAndGetNumberOfLines();
    await expect(numberOfTaxes).to.be.above(0);
  });

  // 1 : Create 2 taxes with data from faker
  describe('Create 2 Taxes in BO', async () => {
    const tests = [
      {args: {taxToCreate: firstTaxData}},
      {args: {taxToCreate: secondTaxData}},
    ];

    tests.forEach((test, index) => {
      it('should go to add new tax page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToNewTaxPage${index + 1}`, baseContext);

        await this.pageObjects.taxesPage.goToAddNewTaxPage();
        const pageTitle = await this.pageObjects.addTaxPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.addTaxPage.pageTitleCreate);
      });

      it('should create tax and check result', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `CreateTax${index + 1}`, baseContext);

        const textResult = await this.pageObjects.addTaxPage.createEditTax(test.args.taxToCreate);
        await expect(textResult).to.equal(this.pageObjects.taxesPage.successfulCreationMessage);

        const numberOfTaxesAfterCreation = await this.pageObjects.taxesPage.getNumberOfElementInGrid();
        await expect(numberOfTaxesAfterCreation).to.be.equal(numberOfTaxes + index + 1);
      });
    });
  });

  // 2 : Enable/Disable with bulk actions
  describe('Enable and Disable Taxes with Bulk Actions', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterTaxesToChangeStatus', baseContext);

      await this.pageObjects.taxesPage.filterTaxes(
        'input',
        'name',
        'TVA to delete',
      );

      const textResult = await this.pageObjects.taxesPage.getTextColumnFromTableTaxes(1, 'name');
      await expect(textResult).to.contains('TVA to delete');
    });

    const tests = [
      {args: {action: 'disable', enabledValue: false}, expected: 'clear'},
      {args: {action: 'enable', enabledValue: true}, expected: 'check'},
    ];

    tests.forEach((test) => {
      it(`should ${test.args.action} taxes with bulk actions and check Result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `bulk${test.args.action}`, baseContext);

        const textResult = await this.pageObjects.taxesPage.changeTaxesEnabledColumnBulkActions(
          test.args.enabledValue,
        );

        await expect(textResult).to.be.equal(this.pageObjects.taxesPage.successfulUpdateStatusMessage);

        const numberOfTaxesInGrid = await this.pageObjects.taxesPage.getNumberOfElementInGrid();
        await expect(numberOfTaxesInGrid).to.be.at.most(numberOfTaxes);

        for (let i = 1; i <= numberOfTaxesInGrid; i++) {
          const textColumn = await this.pageObjects.taxesPage.getTextColumnFromTableTaxes(1, 'active');
          await expect(textColumn).to.contains(test.expected);
        }
      });
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterBulkEdit', baseContext);

      const numberOfTaxesAfterReset = await this.pageObjects.taxesPage.resetAndGetNumberOfLines();
      await expect(numberOfTaxesAfterReset).to.be.equal(numberOfTaxes + 2);
    });
  });

  // 3 : Delete with bulk actions
  describe('Delete Taxes with Bulk Actions', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToBulkDelete', baseContext);

      await this.pageObjects.taxesPage.filterTaxes(
        'input',
        'name',
        'TVA to delete',
      );

      const textResult = await this.pageObjects.taxesPage.getTextColumnFromTableTaxes(1, 'name');
      await expect(textResult).to.contains('TVA to delete');
    });

    it('should delete Taxes with Bulk Actions and check Result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDelete', baseContext);

      const deleteTextResult = await this.pageObjects.taxesPage.deleteTaxesBulkActions();
      await expect(deleteTextResult).to.be.equal(this.pageObjects.taxesPage.successfulDeleteMessage);

      const numberOfTaxesAfterReset = await this.pageObjects.taxesPage.resetAndGetNumberOfLines();
      await expect(numberOfTaxesAfterReset).to.be.equal(numberOfTaxes);
    });
  });
});
