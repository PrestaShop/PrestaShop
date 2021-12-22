require('module-alias/register');

// Helpers to open and close browser
const helper = require('@utils/helpers');

// Common tests login BO
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const zonesPage = require('@pages/BO/international/locations');

// Import data
const {Zones} = require('@data/demo/zones');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_international_locations_zones_filterAndQuickEditZones';

// Import expect from chai
const {expect} = require('chai');

// Browser and tab
let browserContext;
let page;

let numberOfZones = 0;

/*
Filter zones by : is, name, status
Quick Edit 'North America'
 */
describe('BO - International - Zones : Filter and quick edit', async () => {
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
    await testContext.addContextItem(this, 'testIdentifier', 'goToZonesPage', baseContext);

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

  describe('Filter zones', async () => {
    [
      {
        args: {
          testIdentifier: 'filterId', filterType: 'input', filterBy: 'id_zone', filterValue: Zones.europe.id,
        },
      },
      {
        args: {
          testIdentifier: 'filterZone', filterType: 'input', filterBy: 'name', filterValue: Zones.europe.name,
        },
      },
      {
        args: {
          testIdentifier: 'filterStatus', filterType: 'select', filterBy: 'active', filterValue: Zones.europe.enabled,
        },
      },
    ].forEach((test) => {
      it(`should filter by ${test.args.filterBy} '${test.args.filterValue}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        await zonesPage.filterZones(
          page,
          test.args.filterType,
          test.args.filterBy,
          test.args.filterValue,
        );

        const numberOfZonesAfterFilter = await zonesPage.getNumberOfElementInGrid(page);
        await expect(numberOfZonesAfterFilter).to.be.at.most(numberOfZones);

        for (let row = 1; row <= numberOfZonesAfterFilter; row++) {
          if (test.args.filterBy === 'active') {
            const zoneStatus = await zonesPage.getZoneStatus(page, row);
            await expect(zoneStatus).to.equal(test.args.filterValue);
          } else {
            const textColumn = await zonesPage.getTextColumn(
              page,
              row,
              test.args.filterBy,
            );

            await expect(textColumn).to.contains(test.args.filterValue);
          }
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);

        const numberOfZonesAfterReset = await zonesPage.resetAndGetNumberOfLines(page);
        await expect(numberOfZonesAfterReset).to.equal(numberOfZones);
      });
    });
  });

  describe('Quick edit zone', async () => {
    it(`should filter by name '${Zones.northAmerica.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToQuickEdit', baseContext);

      await zonesPage.filterZones(
        page,
        'input',
        'name',
        Zones.northAmerica.name,
      );

      const numberOfZonesAfterFilter = await zonesPage.getNumberOfElementInGrid(page);
      await expect(numberOfZonesAfterFilter).to.be.below(numberOfZones);

      const textColumn = await zonesPage.getTextColumn(page, 1, 'name');
      await expect(textColumn).to.contains(Zones.northAmerica.name);
    });

    [
      {args: {status: 'disable', enable: false}},
      {args: {status: 'enable', enable: true}},
    ].forEach((status) => {
      it(`should ${status.args.status} the first zone`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${status.args.status}Zone`, baseContext);

        await zonesPage.setZoneStatus(
          page,
          1,
          status.args.enable,
        );


        const currentStatus = await zonesPage.getZoneStatus(page, 1);
        await expect(currentStatus).to.be.equal(status.args.enable);
      });
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterQuickEdit', baseContext);

      const numberOfZonesAfterReset = await zonesPage.resetAndGetNumberOfLines(page);
      await expect(numberOfZonesAfterReset).to.equal(numberOfZones);
    });
  });
});
