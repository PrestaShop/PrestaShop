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
const addZonePage = require('@pages/BO/international/locations/add');

// Import data
const ZoneFaker = require('@data/faker/zone');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_international_locations_zones_CRUDZone';

let browserContext;
let page;

const createZoneData = new ZoneFaker();
const editZoneData = new ZoneFaker();

let numberOfZones = 0;

describe('BO - International - Zones : CRUD zone', async () => {
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

  describe('Create zone', async () => {
    it('should go to add new zone page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewZonePage', baseContext);

      await zonesPage.goToAddNewZonePage(page);

      const pageTitle = await addZonePage.getPageTitle(page);
      await expect(pageTitle).to.contains(addZonePage.pageTitleCreate);
    });

    it('should create new zone', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createNewZone', baseContext);

      const textResult = await addZonePage.createEditZone(page, createZoneData);
      await expect(textResult).to.to.contains(zonesPage.successfulCreationMessage);

      const numberOfZonesAfterCreation = await zonesPage.getNumberOfElementInGrid(page);
      await expect(numberOfZonesAfterCreation).to.be.equal(numberOfZones + 1);
    });
  });

  describe('Update zone', async () => {
    it(`should filter zone by name '${createZoneData.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToUpdate', baseContext);

      // Filter
      await zonesPage.filterZones(page, 'input', 'name', createZoneData.name);

      // Check number of zones
      const numberOfZonesAfterFilter = await zonesPage.getNumberOfElementInGrid(page);
      await expect(numberOfZonesAfterFilter).to.be.at.least(1);

      // row = 1 (first row)
      const textColumn = await zonesPage.getTextColumn(page, 1, 'name');
      await expect(textColumn).to.contains(createZoneData.name);
    });

    it('should go to edit zone page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditZonePage', baseContext);

      await zonesPage.goToEditZonePage(page, 1);
      const pageTitle = await addZonePage.getPageTitle(page);
      await expect(pageTitle).to.contains(addZonePage.pageTitleEdit);
    });

    it('should edit zone', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'ediZone', baseContext);

      const textResult = await addZonePage.createEditZone(page, editZoneData);
      await expect(textResult).to.to.contains(zonesPage.successfulUpdateMessage);

      const numberOfZonesAfterReset = await zonesPage.resetAndGetNumberOfLines(page);
      await expect(numberOfZonesAfterReset).to.be.equal(numberOfZones + 1);
    });
  });

  describe('Delete zone', async () => {
    it(`should filter zone by name '${editZoneData.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDelete', baseContext);

      // Filter
      await zonesPage.filterZones(page, 'input', 'name', editZoneData.name);

      // Check number of zones
      const numberOfZonesAfterFilter = await zonesPage.getNumberOfElementInGrid(page);
      await expect(numberOfZonesAfterFilter).to.be.at.least(1);

      const textColumn = await zonesPage.getTextColumn(page, 1, 'name');
      await expect(textColumn).to.contains(editZoneData.name);
    });

    it('should delete zone', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteZone', baseContext);

      const textResult = await zonesPage.deleteZone(page, 1);
      await expect(textResult).to.to.contains(zonesPage.successfulDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDelete', baseContext);

      const numberOfZonesAfterReset = await zonesPage.resetAndGetNumberOfLines(page);
      await expect(numberOfZonesAfterReset).to.be.equal(numberOfZones);
    });
  });
});
