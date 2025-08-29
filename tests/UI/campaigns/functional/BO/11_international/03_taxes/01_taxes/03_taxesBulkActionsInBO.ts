import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boDashboardPage,
  boLoginPage,
  boTaxesPage,
  boTaxesCreatePage,
  type BrowserContext,
  FakerTax,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_international_taxes_taxes_taxesBulkActionsInBO';

// Create taxes, Then disable / Enable and Delete with Bulk actions
describe('BO - International - Taxes : Bulk actions', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfTaxes: number = 0;

  const firstTaxData: FakerTax = new FakerTax({name: 'TVA to delete'});
  const secondTaxData: FakerTax = new FakerTax({name: 'TVA to delete2'});

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

  it('should reset all filters', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfTaxes = await boTaxesPage.resetAndGetNumberOfLines(page);
    expect(numberOfTaxes).to.be.above(0);
  });

  // 1 : Create 2 taxes with data from faker
  describe('Create 2 Taxes in BO', async () => {
    const tests = [
      {args: {taxToCreate: firstTaxData}},
      {args: {taxToCreate: secondTaxData}},
    ];

    tests.forEach((test, index: number) => {
      it('should go to add new tax page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToNewTaxPage${index + 1}`, baseContext);

        await boTaxesPage.goToAddNewTaxPage(page);

        const pageTitle = await boTaxesCreatePage.getPageTitle(page);
        expect(pageTitle).to.contains(boTaxesCreatePage.pageTitleCreate);
      });

      it('should create tax and check result', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `CreateTax${index + 1}`, baseContext);

        const textResult = await boTaxesCreatePage.createEditTax(page, test.args.taxToCreate);
        expect(textResult).to.equal(boTaxesPage.successfulCreationMessage);

        const numberOfTaxesAfterCreation = await boTaxesPage.getNumberOfElementInGrid(page);
        expect(numberOfTaxesAfterCreation).to.be.equal(numberOfTaxes + index + 1);
      });
    });
  });

  // 2 : Enable/Disable with bulk actions
  describe('Enable and Disable Taxes with Bulk Actions', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterTaxesToChangeStatus', baseContext);

      await boTaxesPage.filterTaxes(
        page,
        'input',
        'name',
        'TVA to delete',
      );

      const textResult = await boTaxesPage.getTextColumnFromTableTaxes(page, 1, 'name');
      expect(textResult).to.contains('TVA to delete');
    });

    [
      {args: {action: 'disable', enabledValue: false}},
      {args: {action: 'enable', enabledValue: true}},
    ].forEach((test) => {
      it(`should ${test.args.action} taxes with bulk actions and check Result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `bulk${test.args.action}`, baseContext);

        const textResult = await boTaxesPage.bulkSetStatus(
          page,
          test.args.enabledValue,
        );
        expect(textResult).to.be.equal(boTaxesPage.successfulUpdateStatusMessage);

        const numberOfTaxesInGrid = await boTaxesPage.getNumberOfElementInGrid(page);
        expect(numberOfTaxesInGrid).to.be.at.most(numberOfTaxes);

        for (let i = 1; i <= numberOfTaxesInGrid; i++) {
          const taxStatus = await boTaxesPage.getStatus(page, i);
          expect(taxStatus).to.equal(test.args.enabledValue);
        }
      });
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterBulkEdit', baseContext);

      const numberOfTaxesAfterReset = await boTaxesPage.resetAndGetNumberOfLines(page);
      expect(numberOfTaxesAfterReset).to.be.equal(numberOfTaxes + 2);
    });
  });

  // 3 : Delete with bulk actions
  describe('Delete Taxes with Bulk Actions', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToBulkDelete', baseContext);

      await boTaxesPage.filterTaxes(
        page,
        'input',
        'name',
        'TVA to delete',
      );

      const textResult = await boTaxesPage.getTextColumnFromTableTaxes(page, 1, 'name');
      expect(textResult).to.contains('TVA to delete');
    });

    it('should delete Taxes with Bulk Actions and check Result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDelete', baseContext);

      const deleteTextResult = await boTaxesPage.deleteTaxesBulkActions(page);
      expect(deleteTextResult).to.be.equal(boTaxesPage.successfulDeleteMessage);

      const numberOfTaxesAfterReset = await boTaxesPage.resetAndGetNumberOfLines(page);
      expect(numberOfTaxesAfterReset).to.be.equal(numberOfTaxes);
    });
  });
});
