// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import zonesPage from '@pages/BO/international/locations';
import statesPage from '@pages/BO/international/locations/states';
import addStatePage from '@pages/BO/international/locations/states/add';

// Import data
import StateData from '@data/faker/state';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_international_locations_states_bulkActionsStates';

describe('BO - International - States : Bulk edit status and bulk delete', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfStates: number = 0;

  const statesToCreate: StateData[] = [
    new StateData({name: 'todelete1', isoCode: 'HM'}),
    new StateData({name: 'todelete2', isoCode: 'BV'}),
  ];

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

  describe('Create 2 states in BO', async () => {
    statesToCreate.forEach((stateToCreate: StateData, index: number) => {
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
