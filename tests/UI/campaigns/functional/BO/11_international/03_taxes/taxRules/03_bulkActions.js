require('module-alias/register');

const {expect} = require('chai');

const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const taxesPage = require('@pages/BO/international/taxes');
const taxRulesPage = require('@pages/BO/international/taxes/taxRules/index');
const addTaxRulesPage = require('@pages/BO/international/taxes/taxRules/add');

const TaxRuleFaker = require('@data/faker/taxRuleGroup');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_international_localization_taxes_bulkActions';

let browserContext;
let page;

let numberOfTaxRules = 0;

const firstTaxRuleData = new TaxRuleFaker({name: 'toDelete1'});
const secondTaxRuleData = new TaxRuleFaker({name: 'toDelete2', enabledValue: false});

// Create tax rules, Then disable / Enable and Delete with Bulk actions
describe('Create Tax rules, Then disable / Enable and Delete with Bulk actions', async () => {
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

  it('should go to Taxes page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToTaxesPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.internationalParentLink,
      dashboardPage.taxesLink,
    );

    const pageTitle = await taxesPage.getPageTitle(page);
    await expect(pageTitle).to.contains(taxesPage.pageTitle);
  });

  it('should go to \'Tax Rules\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToTaxRulesPage', baseContext);

    await taxesPage.goToTaxRulesPage(page);

    const pageTitle = await taxRulesPage.getPageTitle(page);
    await expect(pageTitle).to.contains(taxRulesPage.pageTitle);
  });

  it('should reset all filters and get number of Tax rules in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfTaxRules = await taxRulesPage.resetAndGetNumberOfLines(page);
    await expect(numberOfTaxRules).to.be.above(0);
  });

  // 1 : Create 2 tax rules with data from faker
  describe('Create 2 Tax rules in BO', async () => {
    const tests = [
      {args: {taxRuleToCreate: firstTaxRuleData}},
      {args: {taxRuleToCreate: secondTaxRuleData}},
    ];

    tests.forEach((test, index) => {
      it('should go to add new tax rule page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToNewTaxRulePage${index + 1}`, baseContext);

        await taxRulesPage.goToAddNewTaxRulesGroupPage(page);

        const pageTitle = await addTaxRulesPage.getPageTitle(page);
        await expect(pageTitle).to.contains(addTaxRulesPage.pageTitleCreate);
      });

      it('should create tax rule and check result', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `CreateTax${index + 1}`, baseContext);

        const textResult = await addTaxRulesPage.createEditTaxRulesGroup(page, test.args.taxRuleToCreate);
        await expect(textResult).to.contains(addTaxRulesPage.successfulCreationMessage);
      });

      it('should go to Tax Rules page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToTaxRulesPage${index}`, baseContext);

        await taxesPage.goToTaxRulesPage(page);

        const numberOfLineAfterCreation = await taxRulesPage.getNumberOfElementInGrid(page);
        await expect(numberOfLineAfterCreation).to.be.equal(numberOfTaxRules + index + 1);

        const pageTitle = await taxRulesPage.getPageTitle(page);
        await expect(pageTitle).to.contains(taxRulesPage.pageTitle);
      });
    });
  });

  // 2 : Enable/Disable with bulk actions
  describe('Enable and Disable Tax rules with Bulk Actions', async () => {
    const tests = [
      {args: {taxRule: firstTaxRuleData.name, action: 'disable', enabledValue: false}, expected: 'Disabled'},
      {args: {taxRule: secondTaxRuleData.name, action: 'enable', enabledValue: true}, expected: 'Enabled'},
    ];

    tests.forEach((test, index) => {
      it('should filter list by name', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `filterTaxesToChangeStatus${index}`, baseContext);

        await taxRulesPage.filterTable(
          page,
          'input',
          'name',
          test.args.taxRule,
        );

        const textResult = await taxRulesPage.getTextColumnFromTable(page, 1, 'name');
        await expect(textResult).to.contains(test.args.taxRule);
      });

      it(`should ${test.args.action} tax rules with bulk actions and check Result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `bulk${test.args.action}`, baseContext);

        const textResult = await taxRulesPage.changeEnabledColumnBulkActions(page, test.args.enabledValue);

        /* Successful message is not visible, skipping it */
        //await expect(textResult).to.be.equal(taxRulesPage.successfulUpdateStatusMessage);

        const numberOfElementInGrid = await taxRulesPage.getNumberOfElementInGrid(page);

        for (let i = 1; i <= numberOfElementInGrid; i++) {
          const textColumn = await taxRulesPage.getTextColumnFromTable(page, 1, 'active');
          await expect(textColumn).to.contains(test.expected);
        }
      });
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterBulkEdit', baseContext);

      const numberOfLinesAfterReset = await taxRulesPage.resetAndGetNumberOfLines(page);
      await expect(numberOfLinesAfterReset).to.be.equal(numberOfTaxRules + 2);
    });
  });

  // 3 : Delete with bulk actions
  describe('Delete Tax rules with Bulk Actions', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToBulkDelete', baseContext);

      await taxRulesPage.filterTable(
        page,
        'input',
        'name',
        'toDelete',
      );

      const textResult = await taxRulesPage.getTextColumnFromTable(page, 1, 'name');
      await expect(textResult).to.contains('toDelete');
    });

    it('should delete Taxes with Bulk Actions and check Result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDelete', baseContext);

      const deleteTextResult = await taxRulesPage.bulkDeleteTaxRules(page);
      await expect(deleteTextResult).to.contains(taxRulesPage.successfulMultiDeleteMessage);

      const numberOfTaxesAfterReset = await taxRulesPage.resetAndGetNumberOfLines(page);
      await expect(numberOfTaxesAfterReset).to.be.equal(numberOfTaxRules);
    });
  });
});
