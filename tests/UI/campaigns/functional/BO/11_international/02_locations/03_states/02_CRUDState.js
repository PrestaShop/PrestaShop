require('module-alias/register');

// Using chai
const {expect} = require('chai');

// Helpers to open and close browser
const helper = require('@utils/helpers');

// Common tests login BO
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const zonesPage = require('@pages/BO/international/locations');
const statesPage = require('@pages/BO/international/locations/states');
const addStatePage = require('@pages/BO/international/locations/states/add');

// Import data
const StateFaker = require('@data/faker/state');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_international_locations_states_CRUDState';

let browserContext;
let page;

const createStateData = new StateFaker();
const editStateData = new StateFaker();

let numberOfStates = 0;

describe('BO - International - States : CRUD state', async () => {
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

  it('should go to \'International > Locations\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToLocationsPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.internationalParentLink,
      dashboardPage.locationsLink,
    );

    await zonesPage.closeSfToolBar(page);

    const pageTitle = await zonesPage.getPageTitle(page);
    await expect(pageTitle).to.contains(zonesPage.pageTitle);
  });

  it('should go to \'States\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToStatesPage', baseContext);

    await zonesPage.goToSubTabStates(page);
    const pageTitle = await statesPage.getPageTitle(page);
    await expect(pageTitle).to.contains(statesPage.pageTitle);
  });

  it('should reset all filters and get number of states in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfStates = await statesPage.resetAndGetNumberOfLines(page);
    await expect(numberOfStates).to.be.above(0);
  });

  describe('Create state', async () => {
    it('should go to add new state page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewStatePage', baseContext);

      await statesPage.goToAddNewStatePage(page);

      const pageTitle = await addStatePage.getPageTitle(page);
      await expect(pageTitle).to.contains(addStatePage.pageTitleCreate);
    });

    it('should create new state', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createNewState', baseContext);

      const textResult = await addStatePage.createEditState(page, createStateData);
      await expect(textResult).to.to.contains(statesPage.successfulCreationMessage);

      const numberOfStatesAfterCreation = await statesPage.getNumberOfElementInGrid(page);
      await expect(numberOfStatesAfterCreation).to.be.equal(numberOfStates + 1);
    });
  });

  describe('Update state', async () => {
    it(`should filter state by name '${createStateData.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToUpdate', baseContext);

      // Filter
      await statesPage.filterStates(page, 'input', 'a!name', createStateData.name);

      // Check number of states
      const numberOfStatesAfterFilter = await statesPage.getNumberOfElementInGrid(page);
      await expect(numberOfStatesAfterFilter).to.be.at.least(1);

      // row = 1 (first row)
      const textColumn = await statesPage.getTextColumn(page, 1, 'a!name');
      await expect(textColumn).to.contains(createStateData.name);
    });

    it('should go to edit state page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditStatePage', baseContext);

      await statesPage.goToEditStatePage(page, 1);
      const pageTitle = await addStatePage.getPageTitle(page);
      await expect(pageTitle).to.contains(addStatePage.pageTitleEdit);
    });

    it('should edit state', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'ediState', baseContext);

      const textResult = await addStatePage.createEditState(page, editStateData);
      await expect(textResult).to.to.contains(statesPage.successfulUpdateMessage);

      const numberOfStatesAfterReset = await statesPage.resetAndGetNumberOfLines(page);
      await expect(numberOfStatesAfterReset).to.be.equal(numberOfStates + 1);
    });
  });

  describe('Delete state', async () => {
    it(`should filter state by name '${editStateData.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDelete', baseContext);

      // Filter
      await statesPage.filterStates(page, 'input', 'a!name', editStateData.name);

      // Check number of state
      const numberOfStatesAfterFilter = await statesPage.getNumberOfElementInGrid(page);
      await expect(numberOfStatesAfterFilter).to.be.at.least(1);

      const textColumn = await statesPage.getTextColumn(page, 1, 'a!name');
      await expect(textColumn).to.contains(editStateData.name);
    });

    it('should delete state', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteState', baseContext);

      const textResult = await statesPage.deleteState(page, 1);
      await expect(textResult).to.to.contains(statesPage.successfulDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDelete', baseContext);

      const numberOfStatesAfterReset = await statesPage.resetAndGetNumberOfLines(page);
      await expect(numberOfStatesAfterReset).to.be.equal(numberOfStates);
    });
  });
});
