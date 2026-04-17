// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import {requestAccessToken} from '@commonTests/BO/advancedParameters/authServer';

import {
  type APIRequestContext,
  boAttributesPage,
  boAttributesValueCreatePage,
  boAttributesViewPage,
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  dataAttributes,
  FakerAttributeValue,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';

const baseContext: string = 'functional_API_endpoints_attribute_deleteAttributesAttributeId';

describe('API : DELETE /attributes/attributes/{attributeId}', async () => {
  let apiContext: APIRequestContext;
  let browserContext: BrowserContext;
  let page: Page;
  let attributeId: number;
  let accessToken: string;

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

  describe('BackOffice : Create an attribute', async () => {
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
      await boAttributesPage.filterTable(page, 'name', createAttributeValue.attributeName);

      const textColumn = await boAttributesPage.getTextColumn(page, 1, 'name');
      expect(textColumn).to.contains(createAttributeValue.attributeName);
    });

    it('should view attribute', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewAttributeGroup', baseContext);

      await boAttributesPage.viewAttribute(page, 1);

      const pageTitle = await boAttributesViewPage.getPageTitle(page);
      expect(pageTitle).to.equal(boAttributesViewPage.pageTitle(createAttributeValue.attributeName));
    });

    it('should go to add new value page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCreateValuePage', baseContext);

      await boAttributesViewPage.goToAddNewValuePage(page);

      const pageTitle = await boAttributesValueCreatePage.getPageTitle(page);
      expect(pageTitle).to.equal(boAttributesValueCreatePage.createPageTitle);
    });

    it('should create value', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createValue', baseContext);

      const textResult = await boAttributesValueCreatePage.addEditValue(page, createAttributeValue);
      expect(textResult).to.contains(boAttributesViewPage.successfulCreationMessage);
    });

    it('should filter list of attributes', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToUpdateAttribute', baseContext);

      await boAttributesViewPage.resetAndGetNumberOfLines(page);
      await boAttributesViewPage.filterTable(page, 'name', createAttributeValue.value);

      const numRows = await boAttributesViewPage.getNumberOfElementInGrid(page);
      expect(numRows).to.equal(1);

      const textColumn = await boAttributesViewPage.getTextColumn(page, 1, 'name');
      expect(textColumn).to.contains(createAttributeValue.value);

      attributeId = parseInt(await boAttributesViewPage.getTextColumn(page, 1, 'id_attribute'), 10);
      expect(attributeId).to.greaterThan(0);
    });
  });

  describe('API : Delete the Attribute', async () => {
    it('should request the endpoint /attributes/attributes/{attributeId}', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestEndpoint', baseContext);

      const apiResponse = await apiContext.delete(`attributes/attributes/${attributeId}`, {
        headers: {
          Authorization: `Bearer ${accessToken}`,
        },
      });
      expect(apiResponse.status()).to.eq(204);
    });
  });

  describe('BackOffice : Check the Attribute is deleted', async () => {
    it('should filter list of attributes', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterAfterDeletion', baseContext);

      await boAttributesViewPage.resetAndGetNumberOfLines(page);
      await boAttributesViewPage.filterTable(page, 'id_attribute', attributeId.toString());

      const numberOfAttributesAfterDelete = await boAttributesViewPage.getNumberOfElementInGrid(page);
      expect(numberOfAttributesAfterDelete).to.equal(0);
    });

    it('should reset all filters and get number of attributes in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterDeletion', baseContext);

      const numberOfAttributes = await boAttributesViewPage.resetAndGetNumberOfLines(page);
      expect(numberOfAttributes).to.be.above(0);
    });
  });
});
