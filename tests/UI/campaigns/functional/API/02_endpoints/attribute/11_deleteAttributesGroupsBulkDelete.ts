// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import {requestAccessToken} from '@commonTests/BO/advancedParameters/authServer';

import {expect} from 'chai';
import {
  type APIRequestContext,
  boAttributesCreatePage,
  boAttributesPage,
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  FakerAttribute,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_API_endpoints_attribute_deleteAttributesGroupsBulkDelete';

describe('API : DELETE /attributes/groups/bulk-delete', async () => {
  let apiContext: APIRequestContext;
  let browserContext: BrowserContext;
  let page: Page;
  let accessToken: string;
  let numberOfAttributes: number = 0;
  const attributeGroupIds: number[] = [];
  const clientScope: string = 'attribute_group_write';
  const attributeData1: FakerAttribute = new FakerAttribute();
  const attributeData2: FakerAttribute = new FakerAttribute();

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

  describe('BackOffice : Go to Catalog > Attributes & Features', async () => {
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
  });

  [
    attributeData1,
    attributeData2,
  ].forEach((data: FakerAttribute, index: number) => {
    describe(`BackOffice : Create an attribute group #${index}`, async () => {
      it('should go to add new attribute page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddNewAttributePage${index}`, baseContext);

        await boAttributesPage.goToAddAttributePage(page);

        const pageTitle = await boAttributesCreatePage.getPageTitle(page);
        expect(pageTitle).to.equal(boAttributesCreatePage.createPageTitle);
      });

      it('should create new attribute', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createNewAttribute${index}`, baseContext);

        const textResult = await boAttributesCreatePage.addEditAttribute(page, data);
        expect(textResult).to.contains(boAttributesPage.successfulCreationMessage);

        const numberOfAttributesAfterCreation = await boAttributesPage.getNumberOfElementInGrid(page);
        expect(numberOfAttributesAfterCreation).to.equal(numberOfAttributes + index + 1);
      });

      it('should filter list of attributes', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `filterToViewCreatedAttribute${index}`, baseContext);

        await boAttributesPage.filterTable(page, 'name', data.name);

        const textColumn = await boAttributesPage.getTextColumn(page, 1, 'name');
        expect(textColumn).to.contains(data.name);

        const attributeGroupId = parseInt(await boAttributesPage.getTextColumn(page, 1, 'id_attribute_group'), 10);
        expect(attributeGroupId).to.be.gt(0);

        attributeGroupIds.push(attributeGroupId);
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `resetFilterAfterCreation${index}`, baseContext);

        const numberAttributes = await boAttributesPage.resetAndGetNumberOfLines(page);
        expect(numberAttributes).to.be.equal(numberOfAttributes + index + 1);
      });
    });
  });

  describe('API : Fetch Data', async () => {
    it('should request the endpoint /attributes/groups/bulk-delete', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestEndpoint', baseContext);

      const apiResponse = await apiContext.delete('attributes/groups/bulk-delete', {
        headers: {
          Authorization: `Bearer ${accessToken}`,
        },
        data: {
          attributeGroupIds,
        },
      });
      expect(apiResponse.status()).to.eq(204);
    });
  });

  describe('BackOffice : Expected data', async () => {
    it('should check attributes', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkAttributes', baseContext);

      expect(attributeGroupIds.length).to.equal(2);
      for (let i:number = 0; i < attributeGroupIds.length; i++) {
        await boAttributesPage.filterTable(page, 'id_attribute_group', attributeGroupIds[i].toString());

        const numberAttributes = await boAttributesPage.getNumberOfElementInGrid(page);
        expect(numberAttributes).to.be.equal(0);

        const numberAttributesAfterReset = await boAttributesPage.resetAndGetNumberOfLines(page);
        expect(numberAttributesAfterReset).to.be.equal(numberOfAttributes);
      }
    });
  });
});
