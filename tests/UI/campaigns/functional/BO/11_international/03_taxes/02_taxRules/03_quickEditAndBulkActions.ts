// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import taxesPage from '@pages/BO/international/taxes';
import taxRulesPage from '@pages/BO/international/taxes/taxRules';
import addTaxRulesPage from '@pages/BO/international/taxes/taxRules/add';

// Import data
import TaxRulesGroupData from '@data/faker/taxRulesGroup';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_international_taxes_taxRules_quickEditAndBulkActions';

/*
Create 2 tax rules
Enable/Disable by quick edit
Enable/Disable/Delete by bulk actions
 */
describe('BO - International - Tax rules : Bulk actions', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfTaxRules: number = 0;

  const firstTaxRuleData: TaxRulesGroupData = new TaxRulesGroupData({name: 'toDelete1'});
  const secondTaxRuleData: TaxRulesGroupData = new TaxRulesGroupData({name: 'toDelete2', enabled: false});

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

  it('should go to \'International > Taxes\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToTaxesPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.internationalParentLink,
      dashboardPage.taxesLink,
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

  // 1 : Create 2 tax rules with data from faker
  describe('Create 2 Tax rules in BO', async () => {
    [
      {args: {taxRuleToCreate: firstTaxRuleData}},
      {args: {taxRuleToCreate: secondTaxRuleData}},
    ].forEach((test, index: number) => {
      it('should go to add new tax rule page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToNewTaxRulePage${index + 1}`, baseContext);

        await taxRulesPage.goToAddNewTaxRulesGroupPage(page);

        const pageTitle = await addTaxRulesPage.getPageTitle(page);
        expect(pageTitle).to.contains(addTaxRulesPage.pageTitleCreate);
      });

      it('should create tax rule and check result', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `CreateTax${index + 1}`, baseContext);

        const textResult = await addTaxRulesPage.createEditTaxRulesGroup(page, test.args.taxRuleToCreate);
        expect(textResult).to.contains(addTaxRulesPage.successfulCreationMessage);
      });

      it('should go to Tax Rules page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToTaxRulesPage${index}`, baseContext);

        await taxesPage.goToTaxRulesPage(page);

        const numberOfLineAfterCreation = await taxRulesPage.getNumberOfElementInGrid(page);
        expect(numberOfLineAfterCreation).to.be.equal(numberOfTaxRules + index + 1);

        const pageTitle = await taxRulesPage.getPageTitle(page);
        expect(pageTitle).to.contains(taxRulesPage.pageTitle);
      });
    });
  });

  // 2 - Enable/disable by quick edit
  describe('Enable and Disable Tax rules by quick edit', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterTaxesToQuickEdit', baseContext);

      await taxRulesPage.filterTable(page, 'input', 'name', firstTaxRuleData.name);

      const textResult = await taxRulesPage.getTextColumnFromTable(page, 1, 'name');
      expect(textResult).to.contains(firstTaxRuleData.name);
    });

    [
      {args: {action: 'disable', enabledValue: false}},
      {args: {action: 'enable', enabledValue: true}},
    ].forEach((test) => {
      it(`should ${test.args.action} tax rule`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}Category`, baseContext);

        const isActionPerformed = await taxRulesPage.setStatus(page, 1, test.args.enabledValue);

        if (isActionPerformed) {
          const resultMessage = await taxRulesPage.getAlertSuccessBlockContent(page);
          expect(resultMessage).to.contains(taxRulesPage.successfulUpdateStatusMessage);
        }

        const status = await taxRulesPage.getStatus(page, 1);
        expect(status).to.be.equal(test.args.enabledValue);
      });
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterQuickEdit', baseContext);

      const numberOfLinesAfterReset = await taxRulesPage.resetAndGetNumberOfLines(page);
      expect(numberOfLinesAfterReset).to.be.equal(numberOfTaxRules + 2);
    });
  });

  // 3 : Enable/Disable by bulk actions
  describe('Enable and Disable Tax rules by Bulk Actions', async () => {
    [
      {args: {taxRule: firstTaxRuleData.name, action: 'disable', enabledValue: false}},
      {args: {taxRule: secondTaxRuleData.name, action: 'enable', enabledValue: true}},
    ].forEach((test, index: number) => {
      it('should filter list by name', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `filterTaxesToChangeStatus${index}`, baseContext);

        await taxRulesPage.filterTable(
          page,
          'input',
          'name',
          test.args.taxRule,
        );

        const textResult = await taxRulesPage.getTextColumnFromTable(page, 1, 'name');
        expect(textResult).to.contains(test.args.taxRule);
      });

      it(`should ${test.args.action} tax rules with bulk actions and check Result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `bulk${test.args.action}`, baseContext);

        const textResult = await taxRulesPage.bulkSetStatus(page, test.args.enabledValue);
        expect(textResult).to.contains(taxRulesPage.successfulUpdateStatusMessage);

        const numberOfElementInGrid = await taxRulesPage.getNumberOfElementInGrid(page);

        for (let i = 1; i <= numberOfElementInGrid; i++) {
          const textColumn = await taxRulesPage.getStatus(page, i);
          expect(textColumn).to.equal(test.args.enabledValue);
        }
      });
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterBulkEdit', baseContext);

      const numberOfLinesAfterReset = await taxRulesPage.resetAndGetNumberOfLines(page);
      expect(numberOfLinesAfterReset).to.be.equal(numberOfTaxRules + 2);
    });
  });

  // 4 : Delete with bulk actions
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
      expect(textResult).to.contains('toDelete');
    });

    it('should delete Taxes with Bulk Actions and check Result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDelete', baseContext);

      const deleteTextResult = await taxRulesPage.bulkDeleteTaxRules(page);
      expect(deleteTextResult).to.contains(taxRulesPage.successfulMultiDeleteMessage);

      const numberOfTaxesAfterReset = await taxRulesPage.resetAndGetNumberOfLines(page);
      expect(numberOfTaxesAfterReset).to.be.equal(numberOfTaxRules);
    });
  });
});
