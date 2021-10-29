require('module-alias/register');

const {expect} = require('chai');

const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const taxesPage = require('@pages/BO/international/taxes');
const taxRulesPage = require('@pages/BO/international/taxes/taxRules/index');
const addTaxRulesPage = require('@pages/BO/international/taxes/taxRules/add');

const TaxRuleGroupFaker = require('@data/faker/taxRuleGroup');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_international_taxes_taxRules_CRUDTaxRules';

let browserContext;
let page;
let numberOfTaxRules = 0;
const taxRuleDataToCreate = new TaxRuleGroupFaker();
const taxRuleDataToEdit = new TaxRuleGroupFaker({enabled: 'No'});

// Create, Update and Delete Tax rule in BO
describe('BO - International - Tax rules : Create, Update and Delete Tax rule', async () => {
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

  // 1 : Create Tax Rule
  describe('Create Tax Rule', async () => {
    it('should go to \'International > Taxes\' page', async function () {
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

    it('should go to Add new tax rules group page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddTaxRulePageToCreate', baseContext);

      await taxRulesPage.goToAddNewTaxRulesGroupPage(page);

      const pageTitle = await addTaxRulesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(addTaxRulesPage.pageTitleCreate);
    });

    it('should create new tax rule group', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createTaxRuleGroup', baseContext);

      const textResult = await addTaxRulesPage.createEditTaxRulesGroup(page, taxRuleDataToCreate);
      await expect(textResult).to.contains(addTaxRulesPage.successfulCreationMessage);
    });
  });

  // 2 : Update Tax Rule with data generated with faker
  describe('Update Tax Rule created', async () => {
    it('should go to \'Tax Rules\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToTaxRulesPageToUpdate', baseContext);

      await taxesPage.goToTaxRulesPage(page);

      const pageTitle = await taxRulesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(taxRulesPage.pageTitle);
    });

    it('should filter list by tax name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByNameToUpdate', baseContext);

      await taxRulesPage.filterTable(
        page,
        'input',
        'name',
        taxRuleDataToCreate.name,
      );

      const textName = await taxRulesPage.getTextColumnFromTable(page, 1, 'name');
      await expect(textName).to.contains(taxRuleDataToCreate.name);
    });

    it('should go to edit tax Rule page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditPage', baseContext);

      await taxRulesPage.goToEditTaxRulePage(page, '1');

      const pageTitle = await addTaxRulesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(addTaxRulesPage.pageTitleEdit);
    });

    it('should update tax', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateTaxRule', baseContext);

      const textResult = await addTaxRulesPage.createEditTaxRulesGroup(page, taxRuleDataToEdit);
      await expect(textResult).to.contains(addTaxRulesPage.successfulUpdateMessage);
    });

    it('should go to \'Tax Rules\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToTaxRulesPageToReset', baseContext);

      await taxesPage.goToTaxRulesPage(page);

      const pageTitle = await taxRulesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(taxRulesPage.pageTitle);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterUpdate', baseContext);

      const numberOfTaxRulesAfterReset = await taxRulesPage.resetAndGetNumberOfLines(page);
      await expect(numberOfTaxRulesAfterReset).to.equal(numberOfTaxRules + 1);
    });
  });

  // 3 : Delete Tax Rule created
  describe('Delete Tax Rule', async () => {
    it('should filter list by tax name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByNameToDelete', baseContext);

      await taxRulesPage.filterTable(
        page,
        'input',
        'name',
        taxRuleDataToEdit.name,
      );

      const textName = await taxRulesPage.getTextColumnFromTable(page, 1, 'name');
      await expect(textName).to.contains(taxRuleDataToEdit.name);
    });

    it('should delete Tax', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteTaxRule', baseContext);

      const textResult = await taxRulesPage.deleteTaxRule(page, '1');
      await expect(textResult).to.contains(taxRulesPage.successfulDeleteMessage);

      const numberOfTaxRulesAfterDelete = await taxRulesPage.resetAndGetNumberOfLines(page);
      await expect(numberOfTaxRulesAfterDelete).to.be.equal(numberOfTaxRules);
    });
  });
});
