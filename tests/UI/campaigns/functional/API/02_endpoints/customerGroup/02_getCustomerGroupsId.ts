// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import {requestAccessToken} from '@commonTests/BO/advancedParameters/authServer';

import {expect} from 'chai';
import {
  type APIRequestContext,
  boCustomerGroupsPage,
  boCustomerGroupsCreatePage,
  boCustomerSettingsPage,
  boDashboardPage,
  type BrowserContext,
  dataLanguages,
  type Page,
  utilsAPI,
  utilsPlaywright, boLoginPage,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_API_endpoints_customerGroup_getCustomerGroupsId';

describe('API : GET /customers/groups/{customerGroupId}', async () => {
  let apiContext: APIRequestContext;
  let browserContext: BrowserContext;
  let page: Page;
  let accessToken: string;
  let jsonResponse: any;
  let idCustomerGroup: number;
  let reductionPercent: number;
  let displayPriceTaxExcluded: boolean;
  let showPrice: boolean;
  let nameFr: string;
  let nameEn: string;

  const clientScope: string = 'customer_group_read';

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

    it('should go to \'Shop Parameters > Customer Settings\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCustomerSettingsPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.shopParametersParentLink,
        boDashboardPage.customerSettingsLink,
      );
      await boCustomerSettingsPage.closeSfToolBar(page);

      const pageTitle = await boCustomerSettingsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCustomerSettingsPage.pageTitle);
    });

    it('should go to \'Groups\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToGroupsPage', baseContext);

      await boCustomerSettingsPage.goToGroupsPage(page);

      const pageTitle = await boCustomerGroupsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCustomerGroupsPage.pageTitle);
    });

    it('should reset all filters and get number of groups in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

      const numberOfGroups = await boCustomerGroupsPage.resetAndGetNumberOfLines(page);
      expect(numberOfGroups).to.be.above(0);

      idCustomerGroup = parseInt(await boCustomerGroupsPage.getTextColumn(page, 1, 'id_group'), 10);
      expect(idCustomerGroup).to.be.gt(0);
    });

    it('should go to edit group page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditGroupPage', baseContext);

      await boCustomerGroupsPage.gotoEditGroupPage(page, 1);

      const pageTitle = await boCustomerGroupsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boCustomerGroupsCreatePage.pageTitleEdit);
    });

    it('should fetch informations', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'fetchInformations', baseContext);

      reductionPercent = parseInt(await boCustomerGroupsCreatePage.getValue(page, 'reductionPercent'), 10);
      expect(reductionPercent).to.be.gte(0);

      displayPriceTaxExcluded = (await boCustomerGroupsCreatePage.getValue(page, 'displayPriceTaxExcluded')) === 'Tax excluded';
      expect(displayPriceTaxExcluded).to.be.a('boolean');

      showPrice = (await boCustomerGroupsCreatePage.getValue(page, 'showPrice')) === '1';
      expect(showPrice).to.be.a('boolean');

      nameFr = await boCustomerGroupsCreatePage.getValue(page, 'localizedNames', dataLanguages.french.id);
      expect(nameFr).to.be.a('string');

      nameEn = await boCustomerGroupsCreatePage.getValue(page, 'localizedNames', dataLanguages.english.id);
      expect(nameFr).to.be.a('string');
    });
  });

  describe('API : Check Data', async () => {
    it('should request the endpoint /customers/groups/{customerGroupId}', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestEndpoint', baseContext);

      const apiResponse = await apiContext.get(`customers/groups/${idCustomerGroup}`, {
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
        'customerGroupId',
        'localizedNames',
        'reductionPercent',
        'displayPriceTaxExcluded',
        'showPrice',
        'shopIds',
      );
    });

    it('should check the JSON Response : `customerGroupId`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseCustomerGroupId', baseContext);

      expect(jsonResponse).to.have.property('customerGroupId');
      expect(jsonResponse.customerGroupId).to.be.a('number');
      expect(jsonResponse.customerGroupId).to.be.equal(idCustomerGroup);
    });

    it('should check the JSON Response : `localizedNames`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseLocalizedNames', baseContext);

      expect(jsonResponse).to.have.property('localizedNames');
      expect(jsonResponse.localizedNames).to.be.a('object');
      expect(jsonResponse.localizedNames[dataLanguages.english.locale]).to.be.equal(nameEn);
      expect(jsonResponse.localizedNames[dataLanguages.french.locale]).to.be.equal(nameFr);
    });

    it('should check the JSON Response : `reductionPercent`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseReductionPercent', baseContext);

      expect(jsonResponse).to.have.property('reductionPercent');
      expect(jsonResponse.reductionPercent).to.be.a('number');
      expect(jsonResponse.reductionPercent).to.be.equal(reductionPercent);
    });

    it('should check the JSON Response : `displayPriceTaxExcluded`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseDisplayPriceTaxExcluded', baseContext);

      expect(jsonResponse).to.have.property('displayPriceTaxExcluded');
      expect(jsonResponse.displayPriceTaxExcluded).to.be.a('boolean');
      expect(jsonResponse.displayPriceTaxExcluded).to.be.equal(displayPriceTaxExcluded);
    });

    it('should check the JSON Response : `showPrice`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseShowPrice', baseContext);

      expect(jsonResponse).to.have.property('showPrice');
      expect(jsonResponse.showPrice).to.be.a('boolean');
      expect(jsonResponse.showPrice).to.be.equal(showPrice);
    });

    it('should check the JSON Response : `shopIds`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseShopIds', baseContext);

      expect(jsonResponse).to.have.property('shopIds');
      expect(jsonResponse.shopIds).to.be.a('array');
      expect(jsonResponse.shopIds).to.deep.equal([1]);
    });
  });
});
