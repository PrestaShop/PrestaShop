// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import zonesPage from '@pages/BO/international/locations';
import addZonePage from '@pages/BO/international/locations/add';

import {
  FakerZone,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_international_locations_zones_CRUDZone';

describe('BO - International - Zones : CRUD zone', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfZones: number = 0;

  const createZoneData: FakerZone = new FakerZone();
  const editZoneData: FakerZone = new FakerZone();

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
    expect(pageTitle).to.contains(zonesPage.pageTitle);
  });

  it('should reset all filters and get number of zones in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfZones = await zonesPage.resetAndGetNumberOfLines(page);
    expect(numberOfZones).to.be.above(0);
  });

  describe('Create zone', async () => {
    it('should go to add new zone page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewZonePage', baseContext);

      await zonesPage.goToAddNewZonePage(page);

      const pageTitle = await addZonePage.getPageTitle(page);
      expect(pageTitle).to.contains(addZonePage.pageTitleCreate);
    });

    it('should create new zone', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createNewZone', baseContext);

      const textResult = await addZonePage.createEditZone(page, createZoneData);
      expect(textResult).to.to.contains(zonesPage.successfulCreationMessage);

      const numberOfZonesAfterCreation = await zonesPage.getNumberOfElementInGrid(page);
      expect(numberOfZonesAfterCreation).to.be.equal(numberOfZones + 1);
    });
  });

  describe('Update zone', async () => {
    it(`should filter zone by name '${createZoneData.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToUpdate', baseContext);

      // Filter
      await zonesPage.filterZones(page, 'input', 'name', createZoneData.name);

      // Check number of zones
      const numberOfZonesAfterFilter = await zonesPage.getNumberOfElementInGrid(page);
      expect(numberOfZonesAfterFilter).to.be.at.least(1);

      // row = 1 (first row)
      const textColumn = await zonesPage.getTextColumn(page, 1, 'name');
      expect(textColumn).to.contains(createZoneData.name);
    });

    it('should go to edit zone page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditZonePage', baseContext);

      await zonesPage.goToEditZonePage(page, 1);

      const pageTitle = await addZonePage.getPageTitle(page);
      expect(pageTitle).to.contains(addZonePage.pageTitleEdit);
    });

    it('should edit zone', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'ediZone', baseContext);

      const textResult = await addZonePage.createEditZone(page, editZoneData);
      expect(textResult).to.to.contains(zonesPage.successfulUpdateMessage);

      const numberOfZonesAfterReset = await zonesPage.resetAndGetNumberOfLines(page);
      expect(numberOfZonesAfterReset).to.be.equal(numberOfZones + 1);
    });
  });

  describe('Delete zone', async () => {
    it(`should filter zone by name '${editZoneData.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDelete', baseContext);

      // Filter
      await zonesPage.filterZones(page, 'input', 'name', editZoneData.name);

      // Check number of zones
      const numberOfZonesAfterFilter = await zonesPage.getNumberOfElementInGrid(page);
      expect(numberOfZonesAfterFilter).to.be.at.least(1);

      const textColumn = await zonesPage.getTextColumn(page, 1, 'name');
      expect(textColumn).to.contains(editZoneData.name);
    });

    it('should delete zone', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteZone', baseContext);

      const textResult = await zonesPage.deleteZone(page, 1);
      expect(textResult).to.to.contains(zonesPage.successfulDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDelete', baseContext);

      const numberOfZonesAfterReset = await zonesPage.resetAndGetNumberOfLines(page);
      expect(numberOfZonesAfterReset).to.be.equal(numberOfZones);
    });
  });
});
