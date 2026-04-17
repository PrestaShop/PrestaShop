// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import {requestAccessToken} from '@commonTests/BO/advancedParameters/authServer';

import {expect} from 'chai';
import {faker} from '@faker-js/faker';
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
  utilsAPI,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_API_endpoints_attribute_postAttributesGroup';

describe('API : POST /attributes/groups', async () => {
  let apiContext: APIRequestContext;
  let browserContext: BrowserContext;
  let page: Page;
  let accessToken: string;
  let jsonResponse: any;
  let numberOfAttributes: number = 0;

  const clientScope: string = 'attribute_group_write';
  const attributeData: FakerAttribute = new FakerAttribute({
    name: `${faker.lorem.word()}${faker.commerce.productMaterial()}`,
    publicName: `${faker.lorem.word()}${faker.commerce.productMaterial()}`,
  });
  const dataAttributeTypesReverse = Object.fromEntries(Object.entries(dataAttributeTypes).map((type) => type.reverse()));

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
    it('should request the endpoint /attributes/groups', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestEndpoint', baseContext);

      const apiResponse = await apiContext.post('attributes/groups', {
        headers: {
          Authorization: `Bearer ${accessToken}`,
        },
        data: {
          type: dataAttributeTypesReverse[attributeData.attributeType],
          names: {
            [dataLanguages.english.locale]: `${attributeData.name} EN`,
            [dataLanguages.french.locale]: `${attributeData.name} FR`,
          },
          publicNames: {
            [dataLanguages.english.locale]: `${attributeData.publicName} EN`,
            [dataLanguages.french.locale]: `${attributeData.publicName} FR`,
          },
          shopIds: [1],
        },
      });
      expect(apiResponse.status()).to.eq(201);
      expect(utilsAPI.hasResponseHeader(apiResponse, 'Content-Type')).to.eq(true);
      expect(utilsAPI.getResponseHeader(apiResponse, 'Content-Type')).to.contains('application/json');

      jsonResponse = await apiResponse.json();
    });

    // @todo : https://github.com/PrestaShop/PrestaShop/issues/39729
    it.skip('should check the JSON Response keys', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseKeys', baseContext);

      expect(jsonResponse).to.have.all.keys(
        'attributeGroupId',
        'names',
        'position',
        'publicNames',
        'type',
        'shopIds',
      );
    });

    it('should check the JSON Response', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseJSON', baseContext);

      expect(jsonResponse.attributeGroupId).to.be.gt(0);
      expect(jsonResponse.names).to.deep.equal({
        [dataLanguages.english.locale]: `${attributeData.name} EN`,
        [dataLanguages.french.locale]: `${attributeData.name} FR`,
      });
      expect(jsonResponse.publicNames).to.deep.equal({
        [dataLanguages.english.locale]: `${attributeData.publicName} EN`,
        [dataLanguages.french.locale]: `${attributeData.publicName} FR`,
      });
      expect(jsonResponse.type).to.equal(dataAttributeTypesReverse[attributeData.attributeType]);
      expect(jsonResponse.shopIds).to.deep.equal([1]);

      // @todo : https://github.com/PrestaShop/PrestaShop/issues/39729
      this.skip();
      expect(jsonResponse.position).to.equal(1);
    });
  });

  describe('BackOffice : Check the Attribute Group is created', async () => {
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

    it('should filter list of attributes', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToViewCreatedAttribute', baseContext);

      await boAttributesPage.filterTable(page, 'id_attribute_group', jsonResponse.attributeGroupId);

      const numberOfElementsInGrid = await boAttributesPage.getNumberOfElementInGrid(page);
      expect(numberOfElementsInGrid).to.equal(1);
    });

    it('should check the JSON Response : `attributeGroupId`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseAttributeGroupId', baseContext);

      const textColumn = await boAttributesPage.getTextColumn(page, 1, 'id_attribute_group');
      expect(textColumn).to.equal(jsonResponse.attributeGroupId.toString());
    });

    it('should go to edit attribute page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditAttributePage', baseContext);

      await boAttributesPage.goToEditAttributePage(page, 1);

      const pageTitle = await boAttributesCreatePage.getPageTitle(page);
      expect(pageTitle).to.equal(boAttributesCreatePage.editPageTitle(`${attributeData.name} EN`));
    });

    it('should check the JSON Response : `names`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseNames', baseContext);

      const valuePropertyFR = await boAttributesCreatePage.getValue(page, 'names', dataLanguages.french.id);
      const valuePropertyEN = await boAttributesCreatePage.getValue(page, 'names', dataLanguages.english.id);
      expect({
        [dataLanguages.french.locale]: valuePropertyFR,
        [dataLanguages.english.locale]: valuePropertyEN,
      }).to.deep.equal(jsonResponse.names);
    });

    it('should check the JSON Response : `publicNames`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponsePublicNames', baseContext);

      const valuePropertyFR = await boAttributesCreatePage.getValue(page, 'publicNames', dataLanguages.french.id);
      const valuePropertyEN = await boAttributesCreatePage.getValue(page, 'publicNames', dataLanguages.english.id);
      expect({
        [dataLanguages.french.locale]: valuePropertyFR,
        [dataLanguages.english.locale]: valuePropertyEN,
      }).to.deep.equal(jsonResponse.publicNames);
    });

    it('should check the JSON Response : `type`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseType', baseContext);

      const valueProperty = await boAttributesCreatePage.getValue(page, 'type');
      expect(valueProperty).to.equal(jsonResponse.type);
    });
  });

  describe('BackOffice : Delete the Attribute Group', async () => {
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

    it('should delete attribute', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteAttribute', baseContext);

      const textResult = await boAttributesPage.deleteAttribute(page, 1);
      expect(textResult).to.contains(boAttributesPage.successfulDeleteMessage);

      const numberOfAttributesAfterDelete = await boAttributesPage.resetAndGetNumberOfLines(page);
      expect(numberOfAttributesAfterDelete).to.equal(numberOfAttributes - 1);
    });
  });
});
