// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import {requestAccessToken} from '@commonTests/BO/advancedParameters/authServer';

import {expect} from 'chai';
import {
  type APIRequestContext,
  boCustomerGroupsPage,
  boCustomerSettingsPage,
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  type Page,
  utilsAPI,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_API_endpoints_customerGroup_getCustomersGroups';

describe('API : GET /customers/groups', async () => {
  let apiContext: APIRequestContext;
  let browserContext: BrowserContext;
  let page: Page;
  let accessToken: string;
  let jsonResponse: any;

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
      accessToken = await requestAccessToken('');
    });
  });

  describe('API : Fetch Data', async () => {
    it('should request the endpoint /customers/groups', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestEndpoint', baseContext);

      const apiResponse = await apiContext.get('customers/groups', {
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
          'customerGroupId',
          'customers',
          'name',
          'reductionPercent',
          'showPrice',
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

    it('should filter list by id', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkJSONItems', baseContext);

      for (let idxItem: number = 0; idxItem < jsonResponse.totalItems; idxItem++) {
        // eslint-disable-next-line no-loop-func
        await boCustomerGroupsPage.resetFilter(page);
        await boCustomerGroupsPage.filterTable(page, 'input', 'id_group', jsonResponse.items[idxItem].customerGroupId);

        const numLanguages = await boCustomerGroupsPage.getNumberOfElementInGrid(page);
        expect(numLanguages).to.be.equal(1);

        const customerGroupId = parseInt((await boCustomerGroupsPage.getTextColumn(page, 1, 'id_group')).toString(), 10);
        expect(customerGroupId).to.equal(jsonResponse.items[idxItem].customerGroupId);

        const customers = parseInt(await boCustomerGroupsPage.getTextColumn(page, 1, 'nb'), 10);
        expect(customers).to.equal(jsonResponse.items[idxItem].customers);

        const name = await boCustomerGroupsPage.getTextColumn(page, 1, 'b!name');
        expect(name).to.equal(jsonResponse.items[idxItem].name);

        const reductionPercent = parseFloat(await boCustomerGroupsPage.getTextColumn(page, 1, 'reduction'));
        expect(reductionPercent).to.equal(jsonResponse.items[idxItem].reductionPercent);

        const showPrice = (await boCustomerGroupsPage.getTextColumn(page, 1, 'show_prices') === 'Yes');
        expect(showPrice).to.equal(jsonResponse.items[idxItem].showPrice);
      }
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilter', baseContext);

      await boCustomerGroupsPage.resetFilter(page);

      const numberOfCustomerGroups = await boCustomerGroupsPage.resetAndGetNumberOfLines(page);
      expect(numberOfCustomerGroups).to.be.above(0);
    });
  });
});
