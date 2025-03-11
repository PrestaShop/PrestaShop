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

const baseContext: string = 'functional_BO_international_locations_states_bulkActionsStates';

describe('BO - International - States : Bulk edit status and bulk delete', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfStates: number = 0;

  const statesToCreate: FakerState[] = [
    new FakerState({name: 'todelete1', isoCode: 'HM', status: false}),
    new FakerState({name: 'todelete2', isoCode: 'BV', status: false}),
  ];

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

    await boStatesPage.resetAndGetNumberOfLines(page);

    numberOfStates = await boStatesPage.getNumberOfElement(page);
    expect(numberOfStates).to.be.above(0);
  });

  describe('Create 2 states in BO', async () => {
    statesToCreate.forEach((stateToCreate: FakerState, index: number) => {
      it('should go to add new title page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToNewStatePage${index + 1}`, baseContext);

        await boStatesPage.goToAddNewStatePage(page);

        const pageTitle = await boStatesCreatePage.getPageTitle(page);
        expect(pageTitle).to.contains(boStatesCreatePage.pageTitleCreate);
      });

      it('should create state and check result', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createState${index + 1}`, baseContext);

        const textResult = await boStatesCreatePage.createEditState(page, stateToCreate);
        expect(textResult).to.contains(boStatesPage.successfulCreationMessage);

        const numberOfStatesAfterCreation = await boStatesPage.getNumberOfElement(page);
        expect(numberOfStatesAfterCreation).to.be.equal(numberOfStates + index + 1);
      });
    });
  });

  describe('Bulk actions states', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForBulkActions', baseContext);

      await boStatesPage.filterStates(
        page,
        'input',
        'name',
        'todelete',
      );

      const numberOfStatesAfterFilter = await boStatesPage.getNumberOfElementInGrid(page);
      expect(numberOfStatesAfterFilter).to.be.at.most(numberOfStates);

      for (let i = 1; i <= numberOfStatesAfterFilter; i++) {
        const textColumn = await boStatesPage.getTextColumn(
          page,
          i,
          'name',
        );
        expect(textColumn).to.contains('todelete');
      }
    });

    [
      {action: 'enable', wantedStatus: true},
      {action: 'disable', wantedStatus: false},
    ].forEach((test) => {
      it(`should ${test.action} states with bulk actions`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.action}States`, baseContext);

        await boStatesPage.bulkSetStatus(page, test.wantedStatus);

        const numberOfStatesBulkActions = await boStatesPage.getNumberOfElementInGrid(page);

        for (let row = 1; row <= numberOfStatesBulkActions; row++) {
          const rowStatus = await boStatesPage.getStateStatus(page, row);
          expect(rowStatus).to.equal(test.wantedStatus);
        }
      });
    });

    it('should bulk delete states', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteStates', baseContext);

      const deleteTextResult = await boStatesPage.bulkDeleteStates(page);
      expect(deleteTextResult).to.be.contains(boStatesPage.successfulMultiDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterBulkActions', baseContext);

      const numberOfStatesAfterReset = await boStatesPage.resetAndGetNumberOfLines(page);
      expect(numberOfStatesAfterReset).to.be.equal(numberOfStates);
    });
  });
});
