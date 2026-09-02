// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import {requestAccessToken} from '@commonTests/BO/advancedParameters/authServer';

import {expect} from 'chai';
import {
  type APIRequestContext,
  boAttributesPage,
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  type Page,
  utilsAPI,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_API_endpoints_attribute_getAttributesGroups';

describe('API : GET /attributes/groups', async () => {
  let apiContext: APIRequestContext;
  let browserContext: BrowserContext;
  let page: Page;
  let accessToken: string;
  let jsonResponse: any;
  let numberOfAttributes: number = 0;
  const clientScope: string = 'attribute_group_read';

  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);

    apiContext = await utilsPlaywright.createAPIContext(global.API.URL);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('API : Fetch the access token', async () => {
    it('should request the endpoint /access_token', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestOauth2Token', baseContext);
      accessToken = await requestAccessToken(clientScope);
    });
  });

  describe('API : Fetch Data', async () => {
    it('should request the endpoint /attributes/groups', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestEndpoint', baseContext);

      const apiResponse = await apiContext.get('attributes/groups', {
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
        'sortOrder',
        'limit',
        'filters',
        'items',
      );

      expect(jsonResponse.totalItems).to.be.gt(0);

      for (let i:number = 0; i < jsonResponse.totalItems; i++) {
        expect(jsonResponse.items[i]).to.have.all.keys(
          'attributeGroupId',
          'name',
          'position',
          'values',
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

    it('should go to \'Catalog > Attributes & Features\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAttributesPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.catalogParentLink,
        boDashboardPage.attributesAndFeaturesLink,
      );
      await boAttributesPage.closeSfToolBar(page);

      const pageTitle = await boAttributesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boAttributesPage.pageTitle);
    });

    it('should reset all filters and get number of attributes in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

      numberOfAttributes = await boAttributesPage.resetAndGetNumberOfLines(page);
      expect(numberOfAttributes).to.be.equal(jsonResponse.totalItems);
    });

    it('should filter list by id', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkJSONItems', baseContext);

      for (let idxItem: number = 0; idxItem < jsonResponse.totalItems; idxItem++) {
        // eslint-disable-next-line no-loop-func
        await boAttributesPage.resetFilter(page);
        await boAttributesPage.filterTable(page, 'id_attribute_group', jsonResponse.items[idxItem].attributeGroupId);

        const numAttributes = await boAttributesPage.getNumberOfElementInGrid(page);
        expect(numAttributes).to.be.equal(1);

        const attributeGroupId = parseInt((await boAttributesPage.getTextColumn(page, 1, 'id_attribute_group')).toString(), 10);
        expect(attributeGroupId).to.equal(jsonResponse.items[idxItem].attributeGroupId);

        const name = await boAttributesPage.getTextColumn(page, 1, 'name');
        expect(name).to.equal(jsonResponse.items[idxItem].name);

        const values = parseFloat(await boAttributesPage.getTextColumn(page, 1, 'values'));
        expect(values).to.equal(jsonResponse.items[idxItem].values);

        // @todo : https://github.com/PrestaShop/PrestaShop/issues/39753
        // const position = parseFloat(await boAttributesPage.getTextColumn(page, 1, 'position'));
        // expect(position).to.equal(jsonResponse.items[idxItem].position);
      }
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilter', baseContext);

      await boAttributesPage.resetFilter(page);

      const numAttributes = await boAttributesPage.resetAndGetNumberOfLines(page);
      expect(numAttributes).to.be.equal(numberOfAttributes);
    });
  });
});
