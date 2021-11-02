const helper = require('@utils/helpers');

// Using chai
const {expect} = require('chai');

// Common tests login BO
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const zonesPage = require('@pages/BO/international/locations');
const addZonePage = require('@pages/BO/international/locations/add');

// Import data
const ZoneFaker = require('@data/faker/zone');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_international_locations_zones_bulkActionsZones';

let browserContext;
let page;

let numberOfZones = 0;

const zonesToCreate = [
  new ZoneFaker({name: 'todelete1'}),
  new ZoneFaker({name: 'todelete2'}),
];

describe('BO - International - Zones : Bulk enable, disable and delete', async () => {
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

  it('should reset all filters and get number of zones in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfZones = await zonesPage.resetAndGetNumberOfLines(page);
    await expect(numberOfZones).to.be.above(0);
  });

  describe('Create 2 zones in BO', async () => {
    zonesToCreate.forEach((zoneToCreate, index) => {
      it('should go to add new title page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToNewZonePage${index + 1}`, baseContext);

        await zonesPage.goToAddNewZonePage(page);
        const pageTitle = await addZonePage.getPageTitle(page);
        await expect(pageTitle).to.contains(addZonePage.pageTitleCreate);
      });

      it('should create zone and check result', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createZone${index + 1}`, baseContext);

        const textResult = await addZonePage.createEditZone(page, zoneToCreate);
        await expect(textResult).to.contains(zonesPage.successfulCreationMessage);

        const numberOfZonesAfterCreation = await zonesPage.getNumberOfElementInGrid(page);
        await expect(numberOfZonesAfterCreation).to.be.equal(numberOfZones + index + 1);
      });
    });
  });

  describe('Bulk actions zones', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForBulkDelete', baseContext);

      await zonesPage.filterZones(
        page,
        'input',
        'name',
        'todelete',
      );

      const numberOfZonesAfterFilter = await zonesPage.getNumberOfElementInGrid(page);
      await expect(numberOfZonesAfterFilter).to.be.at.most(numberOfZones);

      for (let i = 1; i <= numberOfZonesAfterFilter; i++) {
        const textColumn = await zonesPage.getTextColumn(
          page,
          i,
          'name',
        );

        await expect(textColumn).to.contains('todelete');
      }
    });

    [
      {action: 'enable', wantedStatus: true},
      {action: 'disable', wantedStatus: false},
    ].forEach((test) => {
      it(`should ${test.action} zones with bulk actions`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.action}Zones`, baseContext);

        await zonesPage.bulkSetStatus(page, test.wantedStatus);

        const numberOfZonesBulkActions = await zonesPage.getNumberOfElementInGrid(page);

        for (let row = 1; row <= numberOfZonesBulkActions; row++) {
          const rowStatus = await zonesPage.getZoneStatus(page, row);
          await expect(rowStatus).to.equal(test.wantedStatus);
        }
      });
    });

    it('should bulk delete zones', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteZones', baseContext);

      const deleteTextResult = await zonesPage.bulkDeleteZones(page);
      await expect(deleteTextResult).to.be.contains(zonesPage.successfulMultiDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterDelete', baseContext);

      const numberOfZonesAfterReset = await zonesPage.resetAndGetNumberOfLines(page);
      await expect(numberOfZonesAfterReset).to.be.equal(numberOfZones);
    });
  });
});
