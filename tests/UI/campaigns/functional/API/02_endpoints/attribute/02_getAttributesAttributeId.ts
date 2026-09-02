// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import {requestAccessToken} from '@commonTests/BO/advancedParameters/authServer';

import {expect} from 'chai';
import {
  type APIRequestContext,
  boAttributesPage,
  boAttributesValueCreatePage,
  boAttributesViewPage,
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  dataAttributes,
  dataLanguages,
  type Page,
  utilsAPI,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_API_endpoints_attribute_getAttributesAttributeId';

describe('API : GET /attributes/attributes/{attributeId}', async () => {
  let apiContext: APIRequestContext;
  let browserContext: BrowserContext;
  let page: Page;
  let accessToken: string;
  let jsonResponse: any;
  let attributeId: number;
  let attributeNameEN: string;
  let attributeNameFR: string;
  let attributeColor: string;

  const clientScope: string = 'attribute_read';

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

    it('should filter the attribute Group', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterAttributeGroup', baseContext);

      await boAttributesPage.resetFilter(page);
      await boAttributesPage.filterTable(page, 'name', dataAttributes.color.name);

      const textColumn = await boAttributesPage.getTextColumn(page, 1, 'name');
      expect(textColumn).to.contains(dataAttributes.color.name);
    });

    it('should view attribute', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewAttributeGroup', baseContext);

      await boAttributesPage.viewAttribute(page, 1);

      const pageTitle = await boAttributesViewPage.getPageTitle(page);
      expect(pageTitle).to.equal(boAttributesViewPage.pageTitle(dataAttributes.color.name));
    });

    it('should reset all filters and get the id of first attribute value', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

      const numberOfAttributes = await boAttributesViewPage.resetAndGetNumberOfLines(page);
      expect(numberOfAttributes).to.be.above(0);

      attributeId = parseInt(await boAttributesViewPage.getTextColumn(page, 1, 'id_attribute'), 10);
      expect(attributeId).to.be.gt(0);

      attributeNameEN = await boAttributesViewPage.getTextColumn(page, 1, 'name');
      expect(attributeNameEN).to.be.a('string');
    });

    it('should go to edit value page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditValuePage', baseContext);

      await boAttributesViewPage.goToEditValuePage(page, 1);

      const pageTitle = await boAttributesValueCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boAttributesValueCreatePage.editPageTitle(attributeNameEN));

      attributeNameFR = await boAttributesValueCreatePage.getInputValue(page, 'name', dataLanguages.french.id);
      expect(attributeNameFR).to.be.a('string');

      attributeColor = await boAttributesValueCreatePage.getInputValue(page, 'color');
      expect(attributeColor).to.be.a('string');
    });
  });

  describe('API : Check Data', async () => {
    it('should request the endpoint /attributes/attributes/{attributeId}', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestEndpoint', baseContext);

      const apiResponse = await apiContext.get(`attributes/attributes/${attributeId}`, {
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
        'attributeGroupId',
        'attributeId',
        'color',
        'names',
        'shopIds',
      );
    });

    it('should check the JSON Response : `attributeGroupId`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseAttributeGroupId', baseContext);

      expect(jsonResponse).to.have.property('attributeGroupId');
      expect(jsonResponse.attributeGroupId).to.be.a('number');
      expect(jsonResponse.attributeGroupId).to.be.equal(dataAttributes.color.id);
    });

    it('should check the JSON Response : `attributeId`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseAttributeId', baseContext);

      expect(jsonResponse).to.have.property('attributeId');
      expect(jsonResponse.attributeId).to.be.a('number');
      expect(jsonResponse.attributeId).to.be.equal(attributeId);
    });

    it('should check the JSON Response : `color`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseColor', baseContext);

      expect(jsonResponse).to.have.property('color');
      expect(jsonResponse.color).to.be.a('string');
      expect(jsonResponse.color).to.be.equal(attributeColor);
    });

    it('should check the JSON Response : `names`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseNames', baseContext);

      expect(jsonResponse).to.have.property('names');
      expect(jsonResponse.names).to.be.a('object');
      expect(jsonResponse.names[dataLanguages.english.locale]).to.be.equal(attributeNameEN);
      expect(jsonResponse.names[dataLanguages.french.locale]).to.be.equal(attributeNameFR);
    });

    it('should check the JSON Response : `shopIds`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseShopIds', baseContext);

      expect(jsonResponse).to.have.property('shopIds');
      expect(jsonResponse.shopIds).to.be.a('array');
      expect(jsonResponse.shopIds).to.deep.equal([1]);
    });
  });
});
