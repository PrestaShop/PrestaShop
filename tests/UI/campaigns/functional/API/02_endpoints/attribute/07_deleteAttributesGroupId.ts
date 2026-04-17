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
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';

const baseContext: string = 'functional_API_endpoints_attribute_deleteAttributesGroupId';

describe('API : DELETE /attributes/groups/{attributeGroupId}', async () => {
  let apiContext: APIRequestContext;
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfAttributes: number = 0;
  let attributeGroupId: number;
  let accessToken: string;

  const clientScope: string = 'attribute_group_write';
  const createAttributeData: FakerAttribute = new FakerAttribute();

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

      const textResult = await boAttributesCreatePage.addEditAttribute(page, createAttributeData);
      expect(textResult).to.contains(boAttributesPage.successfulCreationMessage);

      const numberOfAttributesAfterCreation = await boAttributesPage.getNumberOfElementInGrid(page);
      expect(numberOfAttributesAfterCreation).to.equal(numberOfAttributes + 1);
    });

    it('should filter list of attributes', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToViewCreatedAttribute', baseContext);

      await boAttributesPage.filterTable(page, 'name', createAttributeData.name);

      const textColumn = await boAttributesPage.getTextColumn(page, 1, 'name');
      expect(textColumn).to.contains(createAttributeData.name);

      attributeGroupId = parseInt(await boAttributesPage.getTextColumn(page, 1, 'id_attribute_group'), 10);
      expect(attributeGroupId).to.be.gt(0);
    });
  });

  describe('API : Delete the Attribute Group', async () => {
    it('should request the endpoint /attributes/groups/{attributeGroupId}', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestEndpoint', baseContext);

      const apiResponse = await apiContext.delete(`attributes/groups/${attributeGroupId}`, {
        headers: {
          Authorization: `Bearer ${accessToken}`,
        },
      });
      expect(apiResponse.status()).to.eq(204);
    });
  });

  describe('BackOffice : Check the Attribute Group is deleted', async () => {
    it('should filter list of attributes', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterAfterDeletion', baseContext);

      await boAttributesPage.filterTable(page, 'name', createAttributeData.name);

      const numberOfAttributesAfterDelete = await boAttributesPage.getNumberOfElementInGrid(page);
      expect(numberOfAttributesAfterDelete).to.be.equal(0);
    });

    it('should reset all filters and get number of attributes in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterDeletion', baseContext);

      numberOfAttributes = await boAttributesPage.resetAndGetNumberOfLines(page);
      expect(numberOfAttributes).to.be.above(0);
    });
  });
});
