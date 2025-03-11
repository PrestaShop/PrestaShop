import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boDashboardPage,
  boLoginPage,
  boZonesPage,
  boZonesCreatePage,
  type BrowserContext,
  FakerZone,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_international_locations_zones_CRUDZone';

describe('BO - International - Zones : CRUD zone', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfZones: number = 0;

  const createZoneData: FakerZone = new FakerZone();
  const editZoneData: FakerZone = new FakerZone();

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

  it('should reset all filters and get number of zones in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfZones = await boZonesPage.resetAndGetNumberOfLines(page);
    expect(numberOfZones).to.be.above(0);
  });

  describe('Create zone', async () => {
    it('should go to add new zone page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewZonePage', baseContext);

      await boZonesPage.goToAddNewZonePage(page);

      const pageTitle = await boZonesCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boZonesCreatePage.pageTitleCreate);
    });

    it('should create new zone', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createNewZone', baseContext);

      const textResult = await boZonesCreatePage.createEditZone(page, createZoneData);
      expect(textResult).to.to.contains(boZonesPage.successfulCreationMessage);

      const numberOfZonesAfterCreation = await boZonesPage.getNumberOfElementInGrid(page);
      expect(numberOfZonesAfterCreation).to.be.equal(numberOfZones + 1);
    });
  });

  describe('Update zone', async () => {
    it(`should filter zone by name '${createZoneData.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToUpdate', baseContext);

      // Filter
      await boZonesPage.filterZones(page, 'input', 'name', createZoneData.name);

      // Check number of zones
      const numberOfZonesAfterFilter = await boZonesPage.getNumberOfElementInGrid(page);
      expect(numberOfZonesAfterFilter).to.be.at.least(1);

      // row = 1 (first row)
      const textColumn = await boZonesPage.getTextColumn(page, 1, 'name');
      expect(textColumn).to.contains(createZoneData.name);
    });

    it('should go to edit zone page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditZonePage', baseContext);

      await boZonesPage.goToEditZonePage(page, 1);

      const pageTitle = await boZonesCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boZonesCreatePage.pageTitleEdit);
    });

    it('should edit zone', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'ediZone', baseContext);

      const textResult = await boZonesCreatePage.createEditZone(page, editZoneData);
      expect(textResult).to.to.contains(boZonesPage.successfulUpdateMessage);

      const numberOfZonesAfterReset = await boZonesPage.resetAndGetNumberOfLines(page);
      expect(numberOfZonesAfterReset).to.be.equal(numberOfZones + 1);
    });
  });

  describe('Delete zone', async () => {
    it(`should filter zone by name '${editZoneData.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDelete', baseContext);

      // Filter
      await boZonesPage.filterZones(page, 'input', 'name', editZoneData.name);

      // Check number of zones
      const numberOfZonesAfterFilter = await boZonesPage.getNumberOfElementInGrid(page);
      expect(numberOfZonesAfterFilter).to.be.at.least(1);

      const textColumn = await boZonesPage.getTextColumn(page, 1, 'name');
      expect(textColumn).to.contains(editZoneData.name);
    });

    it('should delete zone', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteZone', baseContext);

      const textResult = await boZonesPage.deleteZone(page, 1);
      expect(textResult).to.to.contains(boZonesPage.successfulDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDelete', baseContext);

      const numberOfZonesAfterReset = await boZonesPage.resetAndGetNumberOfLines(page);
      expect(numberOfZonesAfterReset).to.be.equal(numberOfZones);
    });
  });
});
