const helper = require('@utils/helpers');

// Using chai
const {expect} = require('chai');

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

const baseContext = 'functional_BO_international_locations_states_bulkActionsStates';

let browserContext;
let page;

let numberOfStates = 0;

const statesToCreate = [
  new StateFaker({name: 'todelete1', isoCode: 'HM'}),
  new StateFaker({name: 'todelete2', isoCode: 'BV'}),
];

describe('Create 2 states then enable, disable and delete by bulk actions', async () => {
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

  it('should go to locations page', async function () {
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

  it('should go to states page', async function () {
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

  describe('Create 2 states in BO', async () => {
    statesToCreate.forEach((stateToCreate, index) => {
      it('should go to add new title page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToNewStatePage${index + 1}`, baseContext);

        await statesPage.goToAddNewStatePage(page);
        const pageTitle = await addStatePage.getPageTitle(page);
        await expect(pageTitle).to.contains(addStatePage.pageTitleCreate);
      });

      it('should create state and check result', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createState${index + 1}`, baseContext);

        const textResult = await addStatePage.createEditState(page, stateToCreate);
        await expect(textResult).to.contains(statesPage.successfulCreationMessage);

        const numberOfStatesAfterCreation = await statesPage.getNumberOfElementInGrid(page);
        await expect(numberOfStatesAfterCreation).to.be.equal(numberOfStates + index + 1);
      });
    });
  });

  describe('Bulk actions states', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForBulkActions', baseContext);

      await statesPage.filterStates(
        page,
        'input',
        'a!name',
        'todelete',
      );

      const numberOfStatesAfterFilter = await statesPage.getNumberOfElementInGrid(page);
      await expect(numberOfStatesAfterFilter).to.be.at.most(numberOfStates);

      for (let i = 1; i <= numberOfStatesAfterFilter; i++) {
        const textColumn = await statesPage.getTextColumn(
          page,
          i,
          'a!name',
        );

        await expect(textColumn).to.contains('todelete');
      }
    });

    [
      {action: 'enable', wantedStatus: true},
      {action: 'disable', wantedStatus: false},
    ].forEach((test) => {
      it(`should ${test.action} states with bulk actions`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.action}States`, baseContext);

        await statesPage.bulkSetStatus(page, test.wantedStatus);

        const numberOfStatesBulkActions = await statesPage.getNumberOfElementInGrid(page);

        for (let row = 1; row <= numberOfStatesBulkActions; row++) {
          const rowStatus = await statesPage.getStateStatus(page, row);
          await expect(rowStatus).to.equal(test.wantedStatus);
        }
      });
    });

    it('should bulk delete states', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteStates', baseContext);

      const deleteTextResult = await statesPage.bulkDeleteStates(page);
      await expect(deleteTextResult).to.be.contains(statesPage.successfulMultiDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterBulkActions', baseContext);

      const numberOfStatesAfterReset = await statesPage.resetAndGetNumberOfLines(page);
      await expect(numberOfStatesAfterReset).to.be.equal(numberOfStates);
    });
  });
});
