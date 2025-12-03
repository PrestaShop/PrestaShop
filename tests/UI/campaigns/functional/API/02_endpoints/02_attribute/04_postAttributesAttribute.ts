// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import {requestAccessToken} from '@commonTests/BO/advancedParameters/authServer';

import {expect} from 'chai';
import {
  type APIRequestContext,
  boAttributesPage, boAttributesValueCreatePage, boAttributesViewPage,
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  dataAttributes, dataLanguages,
  FakerAttributeValue,
  type Page,
  utilsAPI,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_API_endpoints_attribute_postAttributesAttribute';

describe('API : POST /admin-api/attributes/attributes', async () => {
  let apiContext: APIRequestContext;
  let browserContext: BrowserContext;
  let page: Page;
  let accessToken: string;
  let jsonResponse: any;
  let numberOfAttributes: number;

  const clientScope: string = 'attribute_write';
  const createAttributeValue: FakerAttributeValue = new FakerAttributeValue({
    attributeID: dataAttributes.paperType.id,
    attributeName: dataAttributes.paperType.name,
  });

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

  describe('API : Create the API Access', async () => {
    it('should request the endpoint /admin-api/attributes/attributes', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestEndpoint', baseContext);

      const apiResponse = await apiContext.post('attributes/attributes', {
        headers: {
          Authorization: `Bearer ${accessToken}`,
        },
        data: {
          attributeGroupId: createAttributeValue.attributeID,
          names: {
            [dataLanguages.english.locale]: createAttributeValue.attributeName,
          },
          color: createAttributeValue.color,
          shopIds: [1],
        },
      });

      expect(apiResponse.status()).to.eq(201);
      expect(utilsAPI.hasResponseHeader(apiResponse, 'Content-Type')).to.eq(true);
      expect(utilsAPI.getResponseHeader(apiResponse, 'Content-Type')).to.contains('application/json');

      jsonResponse = await apiResponse.json();
    });

    it('should check the JSON Response keys', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseKeys', baseContext);

      expect(jsonResponse).to.have.all.keys(
        'attributeId',
        'attributeGroupId',
        'names',
        'color',
        'shopIds',
      );
    });

    it('should check the JSON Response', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseJSON', baseContext);

      expect(jsonResponse.attributeId).to.be.a('number');
      expect(jsonResponse.attributeId).to.be.gt(0);
      expect(jsonResponse.attributeGroupId).to.be.a('number');
      expect(jsonResponse.attributeGroupId).to.equal(createAttributeValue.attributeID);
      expect(jsonResponse.names).to.be.a('object');
      expect(jsonResponse.names[dataLanguages.english.locale]).to.equal(createAttributeValue.attributeName);
      expect(jsonResponse.color).to.be.a('string');
      expect(jsonResponse.color).to.equal(createAttributeValue.color);
      expect(jsonResponse.shopIds).to.be.a('array');
      expect(jsonResponse.shopIds).to.deep.equal([1]);
    });
  });

  describe('BackOffice : Check the Attribute is created', async () => {
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
      await boAttributesPage.filterTable(page, 'name', dataAttributes.paperType.name);

      const textColumn = await boAttributesPage.getTextColumn(page, 1, 'name');
      expect(textColumn).to.contains(dataAttributes.paperType.name);
    });

    it('should view attribute group', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewAttributeGroup', baseContext);

      await boAttributesPage.viewAttribute(page, 1);

      const pageTitle = await boAttributesViewPage.getPageTitle(page);
      expect(pageTitle).to.equal(boAttributesViewPage.pageTitle(dataAttributes.paperType.name));
    });

    it('should reset all filters and get the id of last attribute value', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterLast', baseContext);

      numberOfAttributes = await boAttributesViewPage.resetAndGetNumberOfLines(page);
      expect(numberOfAttributes).to.be.above(0);

      const attributeId = parseInt(await boAttributesViewPage.getTextColumn(page, numberOfAttributes, 'id_attribute'), 10);
      expect(attributeId).to.be.gt(0);
      expect(attributeId).to.be.equal(jsonResponse.attributeId);

      const attributeNameEN = await boAttributesViewPage.getTextColumn(page, numberOfAttributes, 'name');
      expect(attributeNameEN).to.be.a('string');
      expect(attributeNameEN).to.be.equal(jsonResponse.names[dataLanguages.english.locale]);
    });

    it('should go to edit value page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditValuePage', baseContext);

      await boAttributesViewPage.goToEditValuePage(page, numberOfAttributes);

      const pageTitle = await boAttributesValueCreatePage.getPageTitle(page);
      const attributeNameEN = jsonResponse.names[dataLanguages.english.locale];
      expect(pageTitle).to.contains(boAttributesValueCreatePage.editPageTitle(attributeNameEN));

      const attributeColor = await boAttributesValueCreatePage.getInputValue(page, 'color');
      expect(attributeColor).to.be.a('string');
      expect(attributeColor).to.be.equal(jsonResponse.color);
    });
  });

  describe('BackOffice : Delete the Attribute value', async () => {
    it('should go to \'Catalog > Attributes & Features\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'returnToAttributesPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.catalogParentLink,
        boDashboardPage.attributesAndFeaturesLink,
      );
      await boAttributesPage.closeSfToolBar(page);

      const pageTitle = await boAttributesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boAttributesPage.pageTitle);
    });

    it('should view attribute group', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewAttributeGroupForDelete', baseContext);

      await boAttributesPage.viewAttribute(page, 1);

      const pageTitle = await boAttributesViewPage.getPageTitle(page);
      expect(pageTitle).to.equal(boAttributesViewPage.pageTitle(dataAttributes.paperType.name));
    });

    it('should reset all filters and get the id of last attribute value', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterLastForDelete', baseContext);

      numberOfAttributes = await boAttributesViewPage.resetAndGetNumberOfLines(page);
      expect(numberOfAttributes).to.be.above(0);

      // Make sure we delete the right one
      const attributeId = parseInt(await boAttributesViewPage.getTextColumn(page, numberOfAttributes, 'id_attribute'), 10);
      expect(attributeId).to.be.gt(0);
      expect(attributeId).to.be.equal(jsonResponse.attributeId);
    });

    it('should delete attribute', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteAttribute', baseContext);

      const textResult = await boAttributesViewPage.deleteValue(page, numberOfAttributes);
      expect(textResult).to.contains(boAttributesPage.successfulDeleteMessage);

      const numberOfAttributesAfterDelete = await boAttributesViewPage.resetAndGetNumberOfLines(page);
      expect(numberOfAttributesAfterDelete).to.equal(numberOfAttributes - 1);
    });
  });
});
