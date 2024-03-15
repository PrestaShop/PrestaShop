// Import utils
import api from '@utils/api';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import {deleteAPIClientTest} from '@commonTests/BO/advancedParameters/authServer';
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import apiClientPage from 'pages/BO/advancedParameters/APIClient';
import addNewApiClientPage from '@pages/BO/advancedParameters/APIClient/add';
import dashboardPage from '@pages/BO/dashboard';
import customerSettingsPage from '@pages/BO/shopParameters/customerSettings';
import groupsPage from '@pages/BO/shopParameters/customerSettings/groups';
import addGroupPage from '@pages/BO/shopParameters/customerSettings/groups/add';

// Import data
import Languages from '@data/demo/languages';
import APIClientData from '@data/faker/APIClient';

import {expect} from 'chai';
import type {APIRequestContext, BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_API_endpoints_customerGroup_getAPICustomerGroupsId';

describe('API : GET /customers/group/{customerGroupId}', async () => {
  let apiContext: APIRequestContext;
  let browserContext: BrowserContext;
  let page: Page;
  let accessToken: string;
  let jsonResponse: any;
  let clientSecret: string;
  let idCustomerGroup: number;
  let reductionPercent: number;
  let displayPriceTaxExcluded: boolean;
  let showPrice: boolean;
  let nameFr: string;
  let nameEn: string;

  const clientScope: string = 'customer_group_read';
  const clientData: APIClientData = new APIClientData({
    enabled: true,
    scopes: [
      clientScope,
    ],
  });

  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    apiContext = await helper.createAPIContext(global.API.URL);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('BackOffice : Fetch the access token', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Advanced Parameters > API Client\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAuthorizationServerPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.advancedParametersLink,
        dashboardPage.authorizationServerLink,
      );

      const pageTitle = await apiClientPage.getPageTitle(page);
      expect(pageTitle).to.eq(apiClientPage.pageTitle);
    });

    it('should check that no records found', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkThatNoRecordFound', baseContext);

      const noRecordsFoundText = await apiClientPage.getTextForEmptyTable(page);
      expect(noRecordsFoundText).to.contains('warning No records found');
    });

    it('should go to add New API Client page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewAPIClientPage', baseContext);

      await apiClientPage.goToNewAPIClientPage(page);

      const pageTitle = await addNewApiClientPage.getPageTitle(page);
      expect(pageTitle).to.eq(addNewApiClientPage.pageTitleCreate);
    });

    it('should create API Client', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createAPIClient', baseContext);

      const textResult = await addNewApiClientPage.addAPIClient(page, clientData);
      expect(textResult).to.contains(addNewApiClientPage.successfulCreationMessage);

      const textMessage = await addNewApiClientPage.getAlertInfoBlockParagraphContent(page);
      expect(textMessage).to.contains(addNewApiClientPage.apiClientGeneratedMessage);
    });

    it('should copy client secret', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'copyClientSecret', baseContext);

      await addNewApiClientPage.copyClientSecret(page);

      clientSecret = await addNewApiClientPage.getClipboardText(page);
      expect(clientSecret.length).to.be.gt(0);
    });

    it('should request the endpoint /access_token', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestOauth2Token', baseContext);

      const apiResponse = await apiContext.post('access_token', {
        form: {
          client_id: clientData.clientId,
          client_secret: clientSecret,
          grant_type: 'client_credentials',
          scope: clientScope,
        },
      });
      expect(apiResponse.status()).to.eq(200);
      expect(api.hasResponseHeader(apiResponse, 'Content-Type')).to.eq(true);
      expect(api.getResponseHeader(apiResponse, 'Content-Type')).to.contains('application/json');

      const jsonResponse = await apiResponse.json();
      expect(jsonResponse).to.have.property('access_token');
      expect(jsonResponse.token_type).to.be.a('string');

      accessToken = jsonResponse.access_token;
    });
  });

  describe('BackOffice : Expected data', async () => {
    it('should go to \'Shop Parameters > Customer Settings\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCustomerSettingsPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.shopParametersParentLink,
        dashboardPage.customerSettingsLink,
      );
      await customerSettingsPage.closeSfToolBar(page);

      const pageTitle = await customerSettingsPage.getPageTitle(page);
      expect(pageTitle).to.contains(customerSettingsPage.pageTitle);
    });

    it('should go to \'Groups\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToGroupsPage', baseContext);

      await customerSettingsPage.goToGroupsPage(page);

      const pageTitle = await groupsPage.getPageTitle(page);
      expect(pageTitle).to.contains(groupsPage.pageTitle);
    });

    it('should reset all filters and get number of groups in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

      const numberOfGroups = await groupsPage.resetAndGetNumberOfLines(page);
      expect(numberOfGroups).to.be.above(0);

      idCustomerGroup = parseInt(await groupsPage.getTextColumn(page, 1, 'id_group'), 10);
      expect(idCustomerGroup).to.be.gt(0);
    });

    it('should go to edit group page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditGroupPage', baseContext);

      await groupsPage.gotoEditGroupPage(page, 1);

      const pageTitle = await addGroupPage.getPageTitle(page);
      expect(pageTitle).to.contains(addGroupPage.pageTitleEdit);
    });

    it('should fetch informations', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'fetchInformations', baseContext);

      reductionPercent = parseInt(await addGroupPage.getValue(page, 'reductionPercent'), 10);
      expect(reductionPercent).to.be.gte(0);

      displayPriceTaxExcluded = (await addGroupPage.getValue(page, 'displayPriceTaxExcluded')) === 'Tax excluded';
      expect(displayPriceTaxExcluded).to.be.a('boolean');

      showPrice = (await addGroupPage.getValue(page, 'showPrice')) === '1';
      expect(showPrice).to.be.a('boolean');

      nameFr = await addGroupPage.getValue(page, 'localizedNames', Languages.french.id);
      expect(nameFr).to.be.a('string');

      nameEn = await addGroupPage.getValue(page, 'localizedNames', Languages.english.id);
      expect(nameFr).to.be.a('string');
    });
  });

  describe('API : Check Data', async () => {
    it('should request the endpoint /customers/group/{customerGroupId}', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestEndpoint', baseContext);

      const apiResponse = await apiContext.get(`customers/group/${idCustomerGroup}`, {
        headers: {
          Authorization: `Bearer ${accessToken}`,
        },
      });
      expect(apiResponse.status()).to.eq(200);
      expect(api.hasResponseHeader(apiResponse, 'Content-Type')).to.eq(true);
      expect(api.getResponseHeader(apiResponse, 'Content-Type')).to.contains('application/json');

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
      expect(jsonResponse.localizedNames[Languages.english.id]).to.be.equal(nameEn);
      expect(jsonResponse.localizedNames[Languages.french.id]).to.be.equal(nameFr);
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

  // Post-condition: Create an API Client
  deleteAPIClientTest(`${baseContext}_postTest`);
});
