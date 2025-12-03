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
  FakerAttribute,
  type Page,
  utilsPlaywright,
  utilsAPI,
  dataLanguages,
  dataAttributeTypes,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';

const baseContext: string = 'functional_API_endpoints_attribute_patchAttributesGroupId';

describe('API : PATCH /attributes/groups/{attributeGroupId}', async () => {
  let apiContext: APIRequestContext;
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfAttributes: number = 0;
  let attributeGroupId: number;
  let accessToken: string;

  const clientScope: string = 'attribute_group_write';
  const attributeData: FakerAttribute = new FakerAttribute({
    attributeType: dataAttributeTypes.radio,
    name: 'Name Attribute',
    publicName: 'Public Name Attribute',
  });
  const attributeDataPatch: FakerAttribute = new FakerAttribute({
    attributeType: dataAttributeTypes.select,
    name: 'Name Attribute after PATCH',
    publicName: 'Public Name Attribute after PATCH',
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

    it('should go to edit attribute page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditAttributePage', baseContext);

      await boAttributesPage.goToEditAttributePage(page, 1);

      const pageTitle = await boAttributesCreatePage.getPageTitle(page);
      expect(pageTitle).to.equal(boAttributesCreatePage.editPageTitle(attributeData.name));
    });
  });

  [
    {
      propertyName: 'type',
      propertyValue: dataAttributeTypesReverse[attributeDataPatch.attributeType],
    },
    {
      propertyName: 'names',
      propertyValue: {
        [dataLanguages.french.locale]: attributeDataPatch.name,
        [dataLanguages.english.locale]: attributeDataPatch.name,
      },
    },
    {
      propertyName: 'publicNames',
      propertyValue: {
        [dataLanguages.french.locale]: attributeDataPatch.publicName,
        [dataLanguages.english.locale]: attributeDataPatch.publicName,
      },
    },
  ].forEach((data: { propertyName: string, propertyValue: boolean|number|string|string[] }) => {
    describe(`Update the property \`${data.propertyName}\` with API and check in BO`, async () => {
      it('should request the endpoint /attributes/groups/{attributeGroupId}', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `requestEndpoint${data.propertyName}`, baseContext);

        const dataPatch: any = {};
        dataPatch[data.propertyName] = data.propertyValue;

        const apiResponse = await apiContext.patch(`attributes/groups/${attributeGroupId}`, {
          headers: {
            Authorization: `Bearer ${accessToken}`,
          },
          data: dataPatch,
        });
        expect(apiResponse.status()).to.eq(200);
        expect(utilsAPI.hasResponseHeader(apiResponse, 'Content-Type')).to.eq(true);
        expect(utilsAPI.getResponseHeader(apiResponse, 'Content-Type')).to.contains('application/json');

        const jsonResponse = await apiResponse.json();
        expect(jsonResponse).to.have.property(data.propertyName);
        expect(jsonResponse[data.propertyName]).to.deep.equal(data.propertyValue);
      });

      it(`should check that the property "${data.propertyName}"`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkProperty${data.propertyName}`, baseContext);

        await boAttributesCreatePage.reloadPage(page);

        if (['names', 'publicNames'].includes(data.propertyName)) {
          const valuePropertyFR = await boAttributesCreatePage.getValue(page, data.propertyName, dataLanguages.french.id);
          const valuePropertyEN = await boAttributesCreatePage.getValue(page, data.propertyName, dataLanguages.english.id);
          expect({
            [dataLanguages.french.locale]: valuePropertyFR,
            [dataLanguages.english.locale]: valuePropertyEN,
          }).to.deep.equal(data.propertyValue);
        } else if (data.propertyName === 'type') {
          const valueProperty = await boAttributesCreatePage.getValue(page, data.propertyName);
          expect(valueProperty).to.equal(data.propertyValue);
        }
      });
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
      expect(numberOfAttributesAfterDelete).to.equal(numberOfAttributes);
    });
  });
});
