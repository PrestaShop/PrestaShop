// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import {requestAccessToken} from '@commonTests/BO/advancedParameters/authServer';

import {
  type APIRequestContext,
  boAttributesPage,
  boAttributesCreatePage,
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  dataAttributeTypes,
  dataLanguages,
  FakerAttribute,
  type Page,
  utilsPlaywright,
  utilsAPI,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';

const baseContext: string = 'functional_API_endpoints_attribute_getAttributesGroupId';

describe('API : GET /attributes/groups/{attributeGroupId}', async () => {
  let apiContext: APIRequestContext;
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfAttributes: number = 0;
  let attributeGroupId: number;
  let accessToken: string;
  let jsonResponse: any;

  const clientScope: string = 'attribute_group_read';
  const attributeData: FakerAttribute = new FakerAttribute();

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

  describe('BackOffice : Create an attribute group', async () => {
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
      expect(numberOfAttributes).to.be.above(0);
    });

    it('should go to add new attribute page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewAttributePage', baseContext);

      await boAttributesPage.goToAddAttributePage(page);

      const pageTitle = await boAttributesCreatePage.getPageTitle(page);
      expect(pageTitle).to.equal(boAttributesCreatePage.createPageTitle);
    });

    it('should create new attribute', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createNewAttribute', baseContext);

      const textResult = await boAttributesCreatePage.addEditAttribute(page, attributeData);
      expect(textResult).to.contains(boAttributesPage.successfulCreationMessage);

      const numberOfAttributesAfterCreation = await boAttributesPage.getNumberOfElementInGrid(page);
      expect(numberOfAttributesAfterCreation).to.equal(numberOfAttributes + 1);
    });

    it('should filter list of attributes', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToViewCreatedAttribute', baseContext);

      await boAttributesPage.filterTable(page, 'name', attributeData.name);

      const textColumn = await boAttributesPage.getTextColumn(page, 1, 'name');
      expect(textColumn).to.contains(attributeData.name);

      attributeGroupId = parseInt(await boAttributesPage.getTextColumn(page, 1, 'id_attribute_group'), 10);
      expect(attributeGroupId).to.be.gt(0);
    });
  });

  describe('API : Fetch the Attribute Group', async () => {
    it('should request the endpoint /attributes/groups/{attributeGroupId}', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestEndpoint', baseContext);

      const apiResponse = await apiContext.get(`attributes/groups/${attributeGroupId}`, {
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
        'names',
        'publicNames',
        'shopIds',
        'type',
      );
    });

    it('should check the JSON Response : `attributeGroupId`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseAttributeGroupId', baseContext);

      expect(jsonResponse).to.have.property('attributeGroupId');
      expect(jsonResponse.attributeGroupId).to.be.a('number');
      expect(jsonResponse.attributeGroupId).to.be.equal(attributeGroupId);
    });

    it('should check the JSON Response : `names`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseNames', baseContext);

      expect(jsonResponse).to.have.property('names');
      expect(jsonResponse.names).to.be.a('object');
      expect(jsonResponse.names[dataLanguages.english.locale]).to.be.equal(attributeData.name);
      expect(jsonResponse.names[dataLanguages.french.locale]).to.be.equal(attributeData.name);
    });

    it('should check the JSON Response : `publicNames`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponsePublicNames', baseContext);

      expect(jsonResponse).to.have.property('publicNames');
      expect(jsonResponse.publicNames).to.be.a('object');
      expect(jsonResponse.publicNames[dataLanguages.english.locale]).to.be.equal(attributeData.publicName);
      expect(jsonResponse.publicNames[dataLanguages.french.locale]).to.be.equal(attributeData.publicName);
    });

    it('should check the JSON Response : `type`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseType', baseContext);

      const dataAttributeTypesReverse = Object.fromEntries(Object.entries(dataAttributeTypes).map((type) => type.reverse()));

      expect(jsonResponse).to.have.property('type');
      expect(jsonResponse.type).to.be.a('string');
      expect(jsonResponse.type).to.be.equal(dataAttributeTypesReverse[attributeData.attributeType]);
    });

    it('should check the JSON Response : `shopIds`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseShopIds', baseContext);

      expect(jsonResponse).to.have.property('shopIds');
      expect(jsonResponse.shopIds).to.be.a('array');
      expect(jsonResponse.shopIds).to.deep.equal([1]);
    });
  });

  describe('BackOffice : Delete the Attribute Group', async () => {
    it('should delete attribute', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteAttribute', baseContext);

      const textResult = await boAttributesPage.deleteAttribute(page, 1);
      expect(textResult).to.contains(boAttributesPage.successfulDeleteMessage);

      const numberOfAttributesAfterDelete = await boAttributesPage.resetAndGetNumberOfLines(page);
      expect(numberOfAttributesAfterDelete).to.equal(numberOfAttributes);
    });
  });
});
