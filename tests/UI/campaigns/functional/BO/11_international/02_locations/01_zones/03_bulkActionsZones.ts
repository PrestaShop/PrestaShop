import testContext from '@utils/testContext';
import {expect} from 'chai';

import addZonePage from '@pages/BO/international/locations/add';

import {
  boDashboardPage,
  boLoginPage,
  boZonesPages,
  type BrowserContext,
  FakerZone,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_international_locations_zones_bulkActionsZones';

describe('BO - International - Zones : Bulk enable, disable and delete', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfZones: number = 0;

  const zonesToCreate: FakerZone[] = [
    new FakerZone({name: 'todelete1'}),
    new FakerZone({name: 'todelete2'}),
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
    await boZonesPages.closeSfToolBar(page);

    const pageTitle = await boZonesPages.getPageTitle(page);
    expect(pageTitle).to.contains(boZonesPages.pageTitle);
  });

  it('should reset all filters and get number of zones in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfZones = await boZonesPages.resetAndGetNumberOfLines(page);
    expect(numberOfZones).to.be.above(0);
  });

  describe('Create 2 zones in BO', async () => {
    zonesToCreate.forEach((zoneToCreate: FakerZone, index: number) => {
      it('should go to add new title page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToNewZonePage${index + 1}`, baseContext);

        await boZonesPages.goToAddNewZonePage(page);

        const pageTitle = await addZonePage.getPageTitle(page);
        expect(pageTitle).to.contains(addZonePage.pageTitleCreate);
      });

      it('should create zone and check result', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createZone${index + 1}`, baseContext);

        const textResult = await addZonePage.createEditZone(page, zoneToCreate);
        expect(textResult).to.contains(boZonesPages.successfulCreationMessage);

        const numberOfZonesAfterCreation = await boZonesPages.getNumberOfElementInGrid(page);
        expect(numberOfZonesAfterCreation).to.be.equal(numberOfZones + index + 1);
      });
    });
  });

  describe('Bulk actions zones', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForBulkDelete', baseContext);

      await boZonesPages.filterZones(
        page,
        'input',
        'name',
        'todelete',
      );

      const numberOfZonesAfterFilter = await boZonesPages.getNumberOfElementInGrid(page);
      expect(numberOfZonesAfterFilter).to.be.at.most(numberOfZones);

      for (let i = 1; i <= numberOfZonesAfterFilter; i++) {
        const textColumn = await boZonesPages.getTextColumn(
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
      it(`should ${test.action} zones with bulk actions`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.action}Zones`, baseContext);

        await boZonesPages.bulkSetStatus(page, test.wantedStatus);

        const numberOfZonesBulkActions = await boZonesPages.getNumberOfElementInGrid(page);

        for (let row = 1; row <= numberOfZonesBulkActions; row++) {
          const rowStatus = await boZonesPages.getZoneStatus(page, row);
          expect(rowStatus).to.equal(test.wantedStatus);
        }
      });
    });

    it('should bulk delete zones', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteZones', baseContext);

      const deleteTextResult = await boZonesPages.bulkDeleteZones(page);
      expect(deleteTextResult).to.be.contains(boZonesPages.successfulMultiDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterDelete', baseContext);

      const numberOfZonesAfterReset = await boZonesPages.resetAndGetNumberOfLines(page);
      expect(numberOfZonesAfterReset).to.be.equal(numberOfZones);
    });
  });
});
