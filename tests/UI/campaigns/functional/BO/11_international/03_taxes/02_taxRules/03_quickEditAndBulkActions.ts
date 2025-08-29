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

  const firstTaxRuleData: FakerTaxRulesGroup = new FakerTaxRulesGroup({name: 'toDelete1'});
  const secondTaxRuleData: FakerTaxRulesGroup = new FakerTaxRulesGroup({name: 'toDelete2', enabled: false});

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

  // 1 : Create 2 tax rules with data from faker
  describe('Create 2 Tax rules in BO', async () => {
    [
      {args: {taxRuleToCreate: firstTaxRuleData}},
      {args: {taxRuleToCreate: secondTaxRuleData}},
    ].forEach((test, index: number) => {
      it('should go to add new tax rule page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToNewTaxRulePage${index + 1}`, baseContext);

        await boTaxRulesPage.goToAddNewTaxRulesGroupPage(page);

        const pageTitle = await boTaxRulesCreatePage.getPageTitle(page);
        expect(pageTitle).to.contains(boTaxRulesCreatePage.pageTitleCreate);
      });

      it('should create tax rule and check result', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `CreateTax${index + 1}`, baseContext);

        const textResult = await boTaxRulesCreatePage.createEditTaxRulesGroup(page, test.args.taxRuleToCreate);
        expect(textResult).to.contains(boTaxRulesCreatePage.successfulCreationMessage);
      });

      it('should go to Tax Rules page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToTaxRulesPage${index}`, baseContext);

        await boTaxesPage.goToTaxRulesPage(page);

        const numberOfLineAfterCreation = await boTaxRulesPage.getNumberOfElementInGrid(page);
        expect(numberOfLineAfterCreation).to.be.equal(numberOfTaxRules + index + 1);

        const pageTitle = await boTaxRulesPage.getPageTitle(page);
        expect(pageTitle).to.contains(boTaxRulesPage.pageTitle);
      });
    });
  });

  // 2 - Enable/disable by quick edit
  describe('Enable and Disable Tax rules by quick edit', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterTaxesToQuickEdit', baseContext);

      await boTaxRulesPage.filterTable(page, 'input', 'name', firstTaxRuleData.name);

      const textResult = await boTaxRulesPage.getTextColumnFromTable(page, 1, 'name');
      expect(textResult).to.contains(firstTaxRuleData.name);
    });

    [
      {args: {action: 'disable', enabledValue: false}},
      {args: {action: 'enable', enabledValue: true}},
    ].forEach((test) => {
      it(`should ${test.args.action} tax rule`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}Category`, baseContext);

        const isActionPerformed = await boTaxRulesPage.setStatus(page, 1, test.args.enabledValue);

        if (isActionPerformed) {
          const resultMessage = await boTaxRulesPage.getAlertSuccessBlockContent(page);
          expect(resultMessage).to.contains(boTaxRulesPage.successfulUpdateStatusMessage);
        }

        const status = await boTaxRulesPage.getStatus(page, 1);
        expect(status).to.be.equal(test.args.enabledValue);
      });
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterQuickEdit', baseContext);

      const numberOfLinesAfterReset = await boTaxRulesPage.resetAndGetNumberOfLines(page);
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

        await boTaxRulesPage.filterTable(
          page,
          'input',
          'name',
          test.args.taxRule,
        );

        const textResult = await boTaxRulesPage.getTextColumnFromTable(page, 1, 'name');
        expect(textResult).to.contains(test.args.taxRule);
      });

      it(`should ${test.args.action} tax rules with bulk actions and check Result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `bulk${test.args.action}`, baseContext);

        const textResult = await boTaxRulesPage.bulkSetStatus(page, test.args.enabledValue);
        expect(textResult).to.contains(boTaxRulesPage.successfulUpdateStatusMessage);

        const numberOfElementInGrid = await boTaxRulesPage.getNumberOfElementInGrid(page);

        for (let i = 1; i <= numberOfElementInGrid; i++) {
          const textColumn = await boTaxRulesPage.getStatus(page, i);
          expect(textColumn).to.equal(test.args.enabledValue);
        }
      });
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterBulkEdit', baseContext);

      const numberOfLinesAfterReset = await boTaxRulesPage.resetAndGetNumberOfLines(page);
      expect(numberOfLinesAfterReset).to.be.equal(numberOfTaxRules + 2);
    });
  });

  // 4 : Delete with bulk actions
  describe('Delete Tax rules with Bulk Actions', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToBulkDelete', baseContext);

      await boTaxRulesPage.filterTable(
        page,
        'input',
        'name',
        'toDelete',
      );

      const textResult = await boTaxRulesPage.getTextColumnFromTable(page, 1, 'name');
      expect(textResult).to.contains('toDelete');
    });

    it('should delete Taxes with Bulk Actions and check Result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDelete', baseContext);

      const deleteTextResult = await boTaxRulesPage.bulkDeleteTaxRules(page);
      expect(deleteTextResult).to.contains(boTaxRulesPage.successfulMultiDeleteMessage);

      const numberOfTaxesAfterReset = await boTaxRulesPage.resetAndGetNumberOfLines(page);
      expect(numberOfTaxesAfterReset).to.be.equal(numberOfTaxRules);
    });
  });
});
