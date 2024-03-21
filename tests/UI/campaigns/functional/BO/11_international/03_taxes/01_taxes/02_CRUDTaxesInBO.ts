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

const baseContext: string = 'functional_BO_international_taxes_taxes_CRUDTaxesInBO';

// Create, Update and Delete Tax in BO
describe('BO - International - Taxes : Create, Update and Delete Tax', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfTaxes: number = 0;

  const createTaxData: FakerTax = new FakerTax();
  const editTaxData: FakerTax = new FakerTax({enabled: false});

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

  // 1 : Create tax with data generated from faker
  describe('Create tax in BO', async () => {
    it('should go to add new tax page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewTax', baseContext);

      await taxesPage.goToAddNewTaxPage(page);

      const pageTitle = await addTaxPage.getPageTitle(page);
      expect(pageTitle).to.contains(addTaxPage.pageTitleCreate);
    });

    it('should create Tax and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createTax', baseContext);

      const textResult = await addTaxPage.createEditTax(page, createTaxData);
      expect(textResult).to.equal(addTaxPage.successfulCreationMessage);

      const numberOfTaxesAfterCreation = await taxesPage.getNumberOfElementInGrid(page);
      expect(numberOfTaxesAfterCreation).to.be.equal(numberOfTaxes + 1);
    });
  });

  // 2 : Update Tax with data generated with faker
  describe('Update Tax Created', async () => {
    it('should filter list by tax name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByNameToUpdate', baseContext);

      await taxesPage.filterTaxes(
        page,
        'input',
        'name',
        createTaxData.name,
      );

      const textName = await taxesPage.getTextColumnFromTableTaxes(page, 1, 'name');
      expect(textName).to.contains(createTaxData.name);
    });

    it('should filter list by tax rate', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByRateToUpdate', baseContext);

      await taxesPage.filterTaxes(
        page,
        'input',
        'rate',
        createTaxData.rate,
      );

      const textRate = await taxesPage.getTextColumnFromTableTaxes(page, 1, 'rate');
      expect(textRate).to.contains(createTaxData.rate);
    });

    it('should go to edit tax page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditPage', baseContext);

      await taxesPage.goToEditTaxPage(page, 1);

      const pageTitle = await addTaxPage.getPageTitle(page);
      expect(pageTitle).to.contains(addTaxPage.pageTitleEdit);
    });

    it('should update tax', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateTax', baseContext);

      const textResult = await addTaxPage.createEditTax(page, editTaxData);
      expect(textResult).to.equal(taxesPage.successfulUpdateMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterUpdate', baseContext);

      const numberOfTaxesAfterReset = await taxesPage.resetAndGetNumberOfLines(page);
      expect(numberOfTaxesAfterReset).to.equal(numberOfTaxes + 1);
    });
  });

  // 3 : Delete Tax created from dropdown Menu
  describe('Delete Tax', async () => {
    it('should filter list by Tax name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByNameToDelete', baseContext);

      await taxesPage.filterTaxes(
        page,
        'input',
        'name',
        editTaxData.name,
      );

      const textName = await taxesPage.getTextColumnFromTableTaxes(page, 1, 'name');
      expect(textName).to.contains(editTaxData.name);
    });

    it('should filter list by tax rate', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByRateToDelete', baseContext);

      await taxesPage.filterTaxes(
        page,
        'input',
        'rate',
        editTaxData.rate,
      );

      const textRate = await taxesPage.getTextColumnFromTableTaxes(page, 1, 'rate');
      expect(textRate).to.contains(editTaxData.rate);
    });

    it('should delete Tax', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteTax', baseContext);

      const textResult = await taxesPage.deleteTax(page, 1);
      expect(textResult).to.equal(taxesPage.successfulDeleteMessage);

      const numberOfTaxesAfterDelete = await taxesPage.resetAndGetNumberOfLines(page);
      expect(numberOfTaxesAfterDelete).to.be.equal(numberOfTaxes);
    });
  });
});
