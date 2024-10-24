// Import utils
import testContext from '@utils/testContext';

// Import pages
import taxesPage from '@pages/BO/international/taxes';
import taxRulesPage from '@pages/BO/international/taxes/taxRules';
import addTaxRulesPage from '@pages/BO/international/taxes/taxRules/add';

import {expect} from 'chai';
import {
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  FakerTaxRulesGroup,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_international_taxes_taxRules_CRUDTaxRules';

// Create, Update and Delete Tax rule in BO
describe('BO - International - Tax rules : Create, Update and Delete Tax rule', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfTaxRules: number = 0;

  const taxRuleDataToCreate: FakerTaxRulesGroup = new FakerTaxRulesGroup();
  const taxRuleDataToEdit: FakerTaxRulesGroup = new FakerTaxRulesGroup({enabled: false});

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  it('should login in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

    await boLoginPage.goTo(page, global.BO.URL);
    await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

    const pageTitle = await boDashboardPage.getPageTitle(page);
    expect(pageTitle).to.contains(boDashboardPage.pageTitle);
  });

  // 1 : Create Tax Rule
  describe('Create Tax Rule', async () => {
    it('should go to \'International > Taxes\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToTaxesPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.internationalParentLink,
        boDashboardPage.taxesLink,
      );

      const pageTitle = await taxesPage.getPageTitle(page);
      expect(pageTitle).to.contains(taxesPage.pageTitle);
    });

    it('should go to \'Tax Rules\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToTaxRulesPage', baseContext);

      await taxesPage.goToTaxRulesPage(page);

      const pageTitle = await taxRulesPage.getPageTitle(page);
      expect(pageTitle).to.contains(taxRulesPage.pageTitle);
    });

    it('should reset all filters and get number of Tax rules in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

      numberOfTaxRules = await taxRulesPage.resetAndGetNumberOfLines(page);
      expect(numberOfTaxRules).to.be.above(0);
    });

    it('should go to Add new tax rules group page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddTaxRulePageToCreate', baseContext);

      await taxRulesPage.goToAddNewTaxRulesGroupPage(page);

      const pageTitle = await addTaxRulesPage.getPageTitle(page);
      expect(pageTitle).to.contains(addTaxRulesPage.pageTitleCreate);
    });

    it('should create new tax rule group', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createTaxRuleGroup', baseContext);

      const textResult = await addTaxRulesPage.createEditTaxRulesGroup(page, taxRuleDataToCreate);
      expect(textResult).to.contains(addTaxRulesPage.successfulCreationMessage);
    });
  });

  // 2 : Update Tax Rule with data generated with faker
  describe('Update Tax Rule created', async () => {
    it('should go to \'Tax Rules\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToTaxRulesPageToUpdate', baseContext);

      await taxesPage.goToTaxRulesPage(page);

      const pageTitle = await taxRulesPage.getPageTitle(page);
      expect(pageTitle).to.contains(taxRulesPage.pageTitle);
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
      expect(textName).to.contains(taxRuleDataToCreate.name);
    });

    it('should go to edit tax Rule page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditPage', baseContext);

      await taxRulesPage.goToEditTaxRulePage(page, 1);

      const pageTitle = await addTaxRulesPage.getPageTitle(page);
      expect(pageTitle).to.contains(addTaxRulesPage.pageTitleEdit);
    });

    it('should update tax', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateTaxRule', baseContext);

      const textResult = await addTaxRulesPage.createEditTaxRulesGroup(page, taxRuleDataToEdit);
      expect(textResult).to.contains(addTaxRulesPage.successfulUpdateMessage);
    });

    it('should go to \'Tax Rules\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToTaxRulesPageToReset', baseContext);

      await taxesPage.goToTaxRulesPage(page);

      const pageTitle = await taxRulesPage.getPageTitle(page);
      expect(pageTitle).to.contains(taxRulesPage.pageTitle);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterUpdate', baseContext);

      const numberOfTaxRulesAfterReset = await taxRulesPage.resetAndGetNumberOfLines(page);
      expect(numberOfTaxRulesAfterReset).to.equal(numberOfTaxRules + 1);
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
      expect(textName).to.contains(taxRuleDataToEdit.name);
    });

    it('should delete Tax', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteTaxRule', baseContext);

      const textResult = await taxRulesPage.deleteTaxRule(page, 1);
      expect(textResult).to.contains(taxRulesPage.successfulDeleteMessage);

      const numberOfTaxRulesAfterDelete = await taxRulesPage.resetAndGetNumberOfLines(page);
      expect(numberOfTaxRulesAfterDelete).to.be.equal(numberOfTaxRules);
    });
  });
});
