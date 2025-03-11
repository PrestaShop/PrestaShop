import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boDashboardPage,
  boLoginPage,
  boStatesPage,
  boStatesCreatePage,
  boZonesPage,
  type BrowserContext,
  FakerState,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_international_locations_states_CRUDState';

describe('BO - International - States : CRUD state', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfStates: number = 0;

  const createStateData: FakerState = new FakerState();
  const editStateData: FakerState = new FakerState();

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

  it('should go to \'International > Locations\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToLocationsPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.internationalParentLink,
      boDashboardPage.locationsLink,
    );
    await boZonesPage.closeSfToolBar(page);

    const pageTitle = await boZonesPage.getPageTitle(page);
    expect(pageTitle).to.contains(boZonesPage.pageTitle);
  });

  it('should go to \'States\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToStatesPage', baseContext);

    await boZonesPage.goToSubTabStates(page);

    const pageTitle = await boStatesPage.getPageTitle(page);
    expect(pageTitle).to.contains(boStatesPage.pageTitle);
  });

  it('should reset all filters and get number of states in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfStates = await boStatesPage.resetAndGetNumberOfLines(page);
    expect(numberOfStates).to.be.above(0);
  });

  describe('Create state', async () => {
    it('should go to add new state page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewStatePage', baseContext);

      await boStatesPage.goToAddNewStatePage(page);

      const pageTitle = await boStatesCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boStatesCreatePage.pageTitleCreate);
    });

    it('should create new state', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createNewState', baseContext);

      const textResult = await boStatesCreatePage.createEditState(page, createStateData);
      expect(textResult).to.to.contains(boStatesPage.successfulCreationMessage);

      const numberOfStatesAfterCreation = await boStatesPage.getNumberOfElement(page);
      expect(numberOfStatesAfterCreation).to.be.equal(numberOfStates + 1);
    });
  });

  describe('Update state', async () => {
    it(`should filter state by name '${createStateData.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToUpdate', baseContext);

      // Filter
      await boStatesPage.filterStates(page, 'input', 'name', createStateData.name);

      // Check number of states
      const numberOfStatesAfterFilter = await boStatesPage.getNumberOfElementInGrid(page);
      expect(numberOfStatesAfterFilter).to.be.at.least(1);

      // row = 1 (first row)
      const textColumn = await boStatesPage.getTextColumn(page, 1, 'name');
      expect(textColumn).to.contains(createStateData.name);
    });

    it('should go to edit state page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditStatePage', baseContext);

      await boStatesPage.goToEditStatePage(page, 1);

      const pageTitle = await boStatesCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boStatesCreatePage.pageTitleEdit);
    });

    it('should edit state', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'ediState', baseContext);

      const textResult = await boStatesCreatePage.createEditState(page, editStateData);
      expect(textResult).to.to.contains(boStatesPage.successfulUpdateMessage);

      const numberOfStatesAfterReset = await boStatesPage.resetAndGetNumberOfLines(page);
      expect(numberOfStatesAfterReset).to.be.equal(numberOfStates + 1);
    });
  });

  describe('Delete state', async () => {
    it(`should filter state by name '${editStateData.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDelete', baseContext);

      // Filter
      await boStatesPage.filterStates(page, 'input', 'name', editStateData.name);

      // Check number of state
      const numberOfStatesAfterFilter = await boStatesPage.getNumberOfElementInGrid(page);
      expect(numberOfStatesAfterFilter).to.be.at.least(1);

      const textColumn = await boStatesPage.getTextColumn(page, 1, 'name');
      expect(textColumn).to.contains(editStateData.name);
    });

    it('should delete state', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteState', baseContext);

      const textResult = await boStatesPage.deleteState(page, 1);
      expect(textResult).to.to.contains(boStatesPage.successfulDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDelete', baseContext);

      const numberOfStatesAfterReset = await boStatesPage.resetAndGetNumberOfLines(page);
      expect(numberOfStatesAfterReset).to.be.equal(numberOfStates);
    });
  });
});
