import testContext from '@utils/testContext';
import {requestAccessToken} from '@commonTests/BO/advancedParameters/authServer';
import {expect} from 'chai';

import {
  type APIRequestContext,
  boDashboardPage,
  boLocalizationPage,
  boLoginPage,
  boZonesPage,
  type BrowserContext,
  type Page,
  utilsAPI,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_API_endpoints_zone_getZones';

describe('API : GET /admin-api/zones', async () => {
  let apiContext: APIRequestContext;
  let browserContext: BrowserContext;
  let page: Page;
  let accessToken: string;
  let jsonResponse: any;

  const clientScope: string = 'zone_read';

  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);

    apiContext = await utilsPlaywright.createAPIContext(global.API.URL);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('API : Fetch the access token', async () => {
    it(`should request the endpoint /access_token with scope ${clientScope}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestOauth2Token', baseContext);
      accessToken = await requestAccessToken(clientScope);
    });
  });

  describe('API : Fetch Data', async () => {
    it('should request the endpoint /zones', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestEndpoint', baseContext);

      const apiResponse = await apiContext.get('zones', {
        headers: {
          Authorization: `Bearer ${accessToken}`,
        },
      });
      expect(apiResponse.status()).to.eq(200);
      expect(utilsAPI.hasResponseHeader(apiResponse, 'Content-Type')).to.eq(true);
      expect(utilsAPI.getResponseHeader(apiResponse, 'Content-Type')).to.contains('application/json');

      jsonResponse = await apiResponse.json();
    });

    it('should check the JSON Response keys', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseKeys', baseContext);
      expect(jsonResponse).to.have.all.keys(
        'totalItems',
        'orderBy',
        'sortOrder',
        'limit',
        'filters',
        'items',
      );

      expect(jsonResponse.totalItems).to.be.gt(0);

      for (let i:number = 0; i < jsonResponse.totalItems; i++) {
        expect(jsonResponse.items[i]).to.have.all.keys(
          'zoneId',
          'enabled',
          'name',
        );
      }
    });
  });

  describe('BackOffice : Expected data', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Locations > Zones\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToZonesPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.internationalParentLink,
        boDashboardPage.locationsLink,
      );
      await boLocalizationPage.closeSfToolBar(page);

      const pageTitle = await boZonesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boZonesPage.pageTitle);
    });

    it('should filter list by id', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkJSONItems', baseContext);

      for (let idxItem: number = 0; idxItem < jsonResponse.totalItems; idxItem++) {
        // eslint-disable-next-line no-loop-func
        await boZonesPage.resetAndGetNumberOfLines(page);
        await boZonesPage.filterZones(page, 'input', 'id_zone', jsonResponse.items[idxItem].zoneId);

        const numZones = await boZonesPage.getNumberOfElementInGrid(page);
        expect(numZones).to.be.equal(1);

        const zoneId = parseInt((await boZonesPage.getTextColumn(page, 1, 'id_zone')).toString(), 10);
        expect(zoneId).to.equal(jsonResponse.items[idxItem].zoneId);
        const zoneEnabled = await boZonesPage.getZoneStatus(page, 1);
        expect(zoneEnabled).to.equal(jsonResponse.items[idxItem].enabled);
        const zoneName = await boZonesPage.getTextColumn(page, 1, 'name');
        expect(zoneName).to.equal(jsonResponse.items[idxItem].name);
      }
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilter', baseContext);

      const numberOfProducts = await boZonesPage.resetAndGetNumberOfLines(page);
      expect(numberOfProducts).to.be.above(0);
    });
  });
});
