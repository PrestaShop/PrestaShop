import testContext from '@utils/testContext';
import {expect} from 'chai';

import statesPage from '@pages/BO/international/locations/states';
import addStatePage from '@pages/BO/international/locations/states/add';

import {
  boDashboardPage,
  boLoginPage,
  boZonesPages,
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
    await boZonesPages.closeSfToolBar(page);

    const pageTitle = await boZonesPages.getPageTitle(page);
    expect(pageTitle).to.contains(boZonesPages.pageTitle);
  });

  it('should go to \'States\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToStatesPage', baseContext);

    await boZonesPages.goToSubTabStates(page);

    const pageTitle = await statesPage.getPageTitle(page);
    expect(pageTitle).to.contains(statesPage.pageTitle);
  });

  it('should reset all filters and get number of states in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfStates = await statesPage.resetAndGetNumberOfLines(page);
    expect(numberOfStates).to.be.above(0);
  });

  describe('Create state', async () => {
    it('should go to add new state page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewStatePage', baseContext);

      await statesPage.goToAddNewStatePage(page);

      const pageTitle = await addStatePage.getPageTitle(page);
      expect(pageTitle).to.contains(addStatePage.pageTitleCreate);
    });

    it('should create new state', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createNewState', baseContext);

      const textResult = await addStatePage.createEditState(page, createStateData);
      expect(textResult).to.to.contains(statesPage.successfulCreationMessage);

      const numberOfStatesAfterCreation = await statesPage.getNumberOfElement(page);
      expect(numberOfStatesAfterCreation).to.be.equal(numberOfStates + 1);
    });
  });

  describe('Update state', async () => {
    it(`should filter state by name '${createStateData.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToUpdate', baseContext);

      // Filter
      await statesPage.filterStates(page, 'input', 'name', createStateData.name);

      // Check number of states
      const numberOfStatesAfterFilter = await statesPage.getNumberOfElementInGrid(page);
      expect(numberOfStatesAfterFilter).to.be.at.least(1);

      // row = 1 (first row)
      const textColumn = await statesPage.getTextColumn(page, 1, 'name');
      expect(textColumn).to.contains(createStateData.name);
    });

    it('should go to edit state page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditStatePage', baseContext);

      await statesPage.goToEditStatePage(page, 1);

      const pageTitle = await addStatePage.getPageTitle(page);
      expect(pageTitle).to.contains(addStatePage.pageTitleEdit);
    });

    it('should edit state', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'ediState', baseContext);

      const textResult = await addStatePage.createEditState(page, editStateData);
      expect(textResult).to.to.contains(statesPage.successfulUpdateMessage);

      const numberOfStatesAfterReset = await statesPage.resetAndGetNumberOfLines(page);
      expect(numberOfStatesAfterReset).to.be.equal(numberOfStates + 1);
    });
  });

  describe('Delete state', async () => {
    it(`should filter state by name '${editStateData.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDelete', baseContext);

      // Filter
      await statesPage.filterStates(page, 'input', 'name', editStateData.name);

      // Check number of state
      const numberOfStatesAfterFilter = await statesPage.getNumberOfElementInGrid(page);
      expect(numberOfStatesAfterFilter).to.be.at.least(1);

      const textColumn = await statesPage.getTextColumn(page, 1, 'name');
      expect(textColumn).to.contains(editStateData.name);
    });

    it('should delete state', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteState', baseContext);

      const textResult = await statesPage.deleteState(page, 1);
      expect(textResult).to.to.contains(statesPage.successfulDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDelete', baseContext);

      const numberOfStatesAfterReset = await statesPage.resetAndGetNumberOfLines(page);
      expect(numberOfStatesAfterReset).to.be.equal(numberOfStates);
    });
  });
});
