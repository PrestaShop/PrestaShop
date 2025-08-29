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

  // 1 : Create tax with data generated from faker
  describe('Create tax in BO', async () => {
    it('should go to add new tax page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewTax', baseContext);

      await boTaxesPage.goToAddNewTaxPage(page);

      const pageTitle = await boTaxesCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boTaxesCreatePage.pageTitleCreate);
    });

    it('should create Tax and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createTax', baseContext);

      const textResult = await boTaxesCreatePage.createEditTax(page, createTaxData);
      expect(textResult).to.equal(boTaxesCreatePage.successfulCreationMessage);

      const numberOfTaxesAfterCreation = await boTaxesPage.getNumberOfElementInGrid(page);
      expect(numberOfTaxesAfterCreation).to.be.equal(numberOfTaxes + 1);
    });
  });

  // 2 : Update Tax with data generated with faker
  describe('Update Tax Created', async () => {
    it('should filter list by tax name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByNameToUpdate', baseContext);

      await boTaxesPage.filterTaxes(
        page,
        'input',
        'name',
        createTaxData.name,
      );

      const textName = await boTaxesPage.getTextColumnFromTableTaxes(page, 1, 'name');
      expect(textName).to.contains(createTaxData.name);
    });

    it('should filter list by tax rate', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByRateToUpdate', baseContext);

      await boTaxesPage.filterTaxes(
        page,
        'input',
        'rate',
        createTaxData.rate,
      );

      const textRate = await boTaxesPage.getTextColumnFromTableTaxes(page, 1, 'rate');
      expect(textRate).to.contains(createTaxData.rate);
    });

    it('should go to edit tax page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditPage', baseContext);

      await boTaxesPage.goToEditTaxPage(page, 1);

      const pageTitle = await boTaxesCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boTaxesCreatePage.pageTitleEdit);
    });

    it('should update tax', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateTax', baseContext);

      const textResult = await boTaxesCreatePage.createEditTax(page, editTaxData);
      expect(textResult).to.equal(boTaxesPage.successfulUpdateMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterUpdate', baseContext);

      const numberOfTaxesAfterReset = await boTaxesPage.resetAndGetNumberOfLines(page);
      expect(numberOfTaxesAfterReset).to.equal(numberOfTaxes + 1);
    });
  });

  // 3 : Delete Tax created from dropdown Menu
  describe('Delete Tax', async () => {
    it('should filter list by Tax name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByNameToDelete', baseContext);

      await boTaxesPage.filterTaxes(
        page,
        'input',
        'name',
        editTaxData.name,
      );

      const textName = await boTaxesPage.getTextColumnFromTableTaxes(page, 1, 'name');
      expect(textName).to.contains(editTaxData.name);
    });

    it('should filter list by tax rate', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByRateToDelete', baseContext);

      await boTaxesPage.filterTaxes(
        page,
        'input',
        'rate',
        editTaxData.rate,
      );

      const textRate = await boTaxesPage.getTextColumnFromTableTaxes(page, 1, 'rate');
      expect(textRate).to.contains(editTaxData.rate);
    });

    it('should delete Tax', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteTax', baseContext);

      const textResult = await boTaxesPage.deleteTax(page, 1);
      expect(textResult).to.equal(boTaxesPage.successfulDeleteMessage);

      const numberOfTaxesAfterDelete = await boTaxesPage.resetAndGetNumberOfLines(page);
      expect(numberOfTaxesAfterDelete).to.be.equal(numberOfTaxes);
    });
  });
});
