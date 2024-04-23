// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import taxesPage from '@pages/BO/international/taxes';
import addTaxPage from '@pages/BO/international/taxes/add';

import {
  FakerTax,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

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

  it('should reset all filters', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfTaxes = await taxesPage.resetAndGetNumberOfLines(page);
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

        await taxesPage.goToAddNewTaxPage(page);

        const pageTitle = await addTaxPage.getPageTitle(page);
        expect(pageTitle).to.contains(addTaxPage.pageTitleCreate);
      });

      it('should create tax and check result', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `CreateTax${index + 1}`, baseContext);

        const textResult = await addTaxPage.createEditTax(page, test.args.taxToCreate);
        expect(textResult).to.equal(taxesPage.successfulCreationMessage);

        const numberOfTaxesAfterCreation = await taxesPage.getNumberOfElementInGrid(page);
        expect(numberOfTaxesAfterCreation).to.be.equal(numberOfTaxes + index + 1);
      });
    });
  });

  // 2 : Enable/Disable with bulk actions
  describe('Enable and Disable Taxes with Bulk Actions', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterTaxesToChangeStatus', baseContext);

      await taxesPage.filterTaxes(
        page,
        'input',
        'name',
        'TVA to delete',
      );

      const textResult = await taxesPage.getTextColumnFromTableTaxes(page, 1, 'name');
      expect(textResult).to.contains('TVA to delete');
    });

    [
      {args: {action: 'disable', enabledValue: false}},
      {args: {action: 'enable', enabledValue: true}},
    ].forEach((test) => {
      it(`should ${test.args.action} taxes with bulk actions and check Result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `bulk${test.args.action}`, baseContext);

        const textResult = await taxesPage.bulkSetStatus(
          page,
          test.args.enabledValue,
        );
        expect(textResult).to.be.equal(taxesPage.successfulUpdateStatusMessage);

        const numberOfTaxesInGrid = await taxesPage.getNumberOfElementInGrid(page);
        expect(numberOfTaxesInGrid).to.be.at.most(numberOfTaxes);

        for (let i = 1; i <= numberOfTaxesInGrid; i++) {
          const taxStatus = await taxesPage.getStatus(page, i);
          expect(taxStatus).to.equal(test.args.enabledValue);
        }
      });
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterBulkEdit', baseContext);

      const numberOfTaxesAfterReset = await taxesPage.resetAndGetNumberOfLines(page);
      expect(numberOfTaxesAfterReset).to.be.equal(numberOfTaxes + 2);
    });
  });

  // 3 : Delete with bulk actions
  describe('Delete Taxes with Bulk Actions', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToBulkDelete', baseContext);

      await taxesPage.filterTaxes(
        page,
        'input',
        'name',
        'TVA to delete',
      );

      const textResult = await taxesPage.getTextColumnFromTableTaxes(page, 1, 'name');
      expect(textResult).to.contains('TVA to delete');
    });

    it('should delete Taxes with Bulk Actions and check Result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDelete', baseContext);

      const deleteTextResult = await taxesPage.deleteTaxesBulkActions(page);
      expect(deleteTextResult).to.be.equal(taxesPage.successfulDeleteMessage);

      const numberOfTaxesAfterReset = await taxesPage.resetAndGetNumberOfLines(page);
      expect(numberOfTaxesAfterReset).to.be.equal(numberOfTaxes);
    });
  });
});
