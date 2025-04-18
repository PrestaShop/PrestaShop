import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boDashboardPage,
  boLoginPage,
  boZonesPage,
  type BrowserContext,
  dataZones,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_international_locations_zones_filterAndQuickEditZones';

/*
Filter zones by : is, name, status
Quick Edit 'North America'
 */
describe('BO - International - Zones : Filter and quick edit', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfZones: number = 0;

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
    await testContext.addContextItem(this, 'testIdentifier', 'goToZonesPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.internationalParentLink,
      boDashboardPage.locationsLink,
    );
    await boZonesPage.closeSfToolBar(page);

    const pageTitle = await boZonesPage.getPageTitle(page);
    expect(pageTitle).to.contains(boZonesPage.pageTitle);
  });

  it('should reset all filters and get number of zones in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfZones = await boZonesPage.resetAndGetNumberOfLines(page);
    expect(numberOfZones).to.be.above(0);
  });

  describe('Filter zones', async () => {
    [
      {
        args: {
          testIdentifier: 'filterId',
          filterType: 'input',
          filterBy: 'id_zone',
          filterValue: dataZones.europe.id.toString(),
        },
      },
      {
        args: {
          testIdentifier: 'filterZone',
          filterType: 'input',
          filterBy: 'name',
          filterValue: dataZones.europe.name,
        },
      },
      {
        args: {
          testIdentifier: 'filterStatus',
          filterType: 'select',
          filterBy: 'active',
          filterValue: dataZones.europe.status ? '1' : '0',
        },
      },
    ].forEach((test) => {
      it(`should filter by ${test.args.filterBy} '${test.args.filterValue}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        await boZonesPage.filterZones(
          page,
          test.args.filterType,
          test.args.filterBy,
          test.args.filterValue,
        );

        const numberOfZonesAfterFilter = await boZonesPage.getNumberOfElementInGrid(page);
        expect(numberOfZonesAfterFilter).to.be.at.most(numberOfZones);

        for (let row = 1; row <= numberOfZonesAfterFilter; row++) {
          if (test.args.filterBy === 'active') {
            const zoneStatus = await boZonesPage.getZoneStatus(page, row);
            expect(zoneStatus).to.equal(test.args.filterValue === '1');
          } else {
            const textColumn = await boZonesPage.getTextColumn(
              page,
              row,
              test.args.filterBy,
            );
            expect(textColumn).to.contains(test.args.filterValue);
          }
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);

        const numberOfZonesAfterReset = await boZonesPage.resetAndGetNumberOfLines(page);
        expect(numberOfZonesAfterReset).to.equal(numberOfZones);
      });
    });
  });

  describe('Quick edit zone', async () => {
    it(`should filter by name '${dataZones.northAmerica.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToQuickEdit', baseContext);

      await boZonesPage.filterZones(
        page,
        'input',
        'name',
        dataZones.northAmerica.name,
      );

      const numberOfZonesAfterFilter = await boZonesPage.getNumberOfElementInGrid(page);
      expect(numberOfZonesAfterFilter).to.be.below(numberOfZones);

      const textColumn = await boZonesPage.getTextColumn(page, 1, 'name');
      expect(textColumn).to.contains(dataZones.northAmerica.name);
    });

    [
      {args: {status: 'disable', enable: false}},
      {args: {status: 'enable', enable: true}},
    ].forEach((status) => {
      it(`should ${status.args.status} the first zone`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${status.args.status}Zone`, baseContext);

        await boZonesPage.setZoneStatus(
          page,
          1,
          status.args.enable,
        );

        const currentStatus = await boZonesPage.getZoneStatus(page, 1);
        expect(currentStatus).to.be.equal(status.args.enable);
      });
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterQuickEdit', baseContext);

      const numberOfZonesAfterReset = await boZonesPage.resetAndGetNumberOfLines(page);
      expect(numberOfZonesAfterReset).to.equal(numberOfZones);
    });
  });
});
