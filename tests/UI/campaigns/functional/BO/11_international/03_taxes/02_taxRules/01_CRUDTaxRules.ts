import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boDashboardPage,
  boLoginPage,
  boTaxesPage,
  boTaxRulesPage,
  boTaxRulesCreatePage,
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

      const pageTitle = await boTaxesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boTaxesPage.pageTitle);
    });

    it('should go to \'Tax Rules\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToTaxRulesPage', baseContext);

      await boTaxesPage.goToTaxRulesPage(page);

      const pageTitle = await boTaxRulesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boTaxRulesPage.pageTitle);
    });

    it('should reset all filters and get number of Tax rules in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

      numberOfTaxRules = await boTaxRulesPage.resetAndGetNumberOfLines(page);
      expect(numberOfTaxRules).to.be.above(0);
    });

    it('should go to Add new tax rules group page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddTaxRulePageToCreate', baseContext);

      await boTaxRulesPage.goToAddNewTaxRulesGroupPage(page);

      const pageTitle = await boTaxRulesCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boTaxRulesCreatePage.pageTitleCreate);
    });

    it('should create new tax rule group', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createTaxRuleGroup', baseContext);

      const textResult = await boTaxRulesCreatePage.createEditTaxRulesGroup(page, taxRuleDataToCreate);
      expect(textResult).to.contains(boTaxRulesCreatePage.successfulCreationMessage);
    });
  });

  // 2 : Update Tax Rule with data generated with faker
  describe('Update Tax Rule created', async () => {
    it('should go to \'Tax Rules\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToTaxRulesPageToUpdate', baseContext);

      await boTaxesPage.goToTaxRulesPage(page);

      const pageTitle = await boTaxRulesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boTaxRulesPage.pageTitle);
    });

    it('should filter list by tax name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByNameToUpdate', baseContext);

      await boTaxRulesPage.filterTable(
        page,
        'input',
        'name',
        taxRuleDataToCreate.name,
      );

      const textName = await boTaxRulesPage.getTextColumnFromTable(page, 1, 'name');
      expect(textName).to.contains(taxRuleDataToCreate.name);
    });

    it('should go to edit tax Rule page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditPage', baseContext);

      await boTaxRulesPage.goToEditTaxRulePage(page, 1);

      const pageTitle = await boTaxRulesCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boTaxRulesCreatePage.pageTitleEdit);
    });

    it('should update tax', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateTaxRule', baseContext);

      const textResult = await boTaxRulesCreatePage.createEditTaxRulesGroup(page, taxRuleDataToEdit);
      expect(textResult).to.contains(boTaxRulesCreatePage.successfulUpdateMessage);
    });

    it('should go to \'Tax Rules\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToTaxRulesPageToReset', baseContext);

      await boTaxesPage.goToTaxRulesPage(page);

      const pageTitle = await boTaxRulesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boTaxRulesPage.pageTitle);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterUpdate', baseContext);

      const numberOfTaxRulesAfterReset = await boTaxRulesPage.resetAndGetNumberOfLines(page);
      expect(numberOfTaxRulesAfterReset).to.equal(numberOfTaxRules + 1);
    });
  });

  // 3 : Delete Tax Rule created
  describe('Delete Tax Rule', async () => {
    it('should filter list by tax name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByNameToDelete', baseContext);

      await boTaxRulesPage.filterTable(
        page,
        'input',
        'name',
        taxRuleDataToEdit.name,
      );

      const textName = await boTaxRulesPage.getTextColumnFromTable(page, 1, 'name');
      expect(textName).to.contains(taxRuleDataToEdit.name);
    });

    it('should delete Tax', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteTaxRule', baseContext);

      const textResult = await boTaxRulesPage.deleteTaxRule(page, 1);
      expect(textResult).to.contains(boTaxRulesPage.successfulDeleteMessage);

      const numberOfTaxRulesAfterDelete = await boTaxRulesPage.resetAndGetNumberOfLines(page);
      expect(numberOfTaxRulesAfterDelete).to.be.equal(numberOfTaxRules);
    });
  });
});
