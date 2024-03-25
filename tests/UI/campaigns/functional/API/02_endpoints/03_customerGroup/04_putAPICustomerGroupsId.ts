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

import {
  FakerGroup,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';
import type {APIRequestContext, BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_API_endpoints_customerGroup_putAPICustomerGroupsId';

describe('API : PUT /customers/group/{customerGroupId}', async () => {
  let apiContext: APIRequestContext;
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfGroups: number;
  let idCustomerGroup: number;
  let jsonResponse: any;
  let clientSecret: string;
  let accessToken: string;

  const clientScope: string = 'customer_group_write';
  const clientData: APIClientData = new APIClientData({
    enabled: true,
    scopes: [
      clientScope,
    ],
  });
  const createGroupData: FakerGroup = new FakerGroup({
    priceDisplayMethod: 'Tax included',
  });
  const updateGroupData: FakerGroup = new FakerGroup({
    name: 'Customer Group EN',
    frName: 'Customer Group FR',
    discount: 42,
    shownPrices: false,
    priceDisplayMethod: 'Tax excluded',
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

  describe('BackOffice : Create a Customer Group', async () => {
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

      numberOfGroups = await groupsPage.resetAndGetNumberOfLines(page);
      expect(numberOfGroups).to.be.above(0);
    });

    it('should go to add new group page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewGroup', baseContext);

      await groupsPage.goToNewGroupPage(page);

      const pageTitle = await addGroupPage.getPageTitle(page);
      expect(pageTitle).to.contains(addGroupPage.pageTitleCreate);
    });

    it('should create group and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createGroup', baseContext);

      const textResult = await addGroupPage.createEditGroup(page, createGroupData);
      expect(textResult).to.contains(groupsPage.successfulCreationMessage);

      const numberOfGroupsAfterCreation = await groupsPage.getNumberOfElementInGrid(page);
      expect(numberOfGroupsAfterCreation).to.be.equal(numberOfGroups + 1);
    });

    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterAfterCreation', baseContext);

      await groupsPage.resetFilter(page);
      await groupsPage.filterTable(page, 'input', 'b!name', createGroupData.name);

      const numberOfGroupsAfterCreation = await groupsPage.getNumberOfElementInGrid(page);
      expect(numberOfGroupsAfterCreation).to.be.equal(1);

      const textEmail = await groupsPage.getTextColumn(page, 1, 'b!name');
      expect(textEmail).to.contains(createGroupData.name);

      idCustomerGroup = parseInt(await groupsPage.getTextColumn(page, 1, 'id_group'), 10);
      expect(idCustomerGroup).to.be.gt(0);
    });
  });

  describe('API : Update the Customer Group', async () => {
    it('should request the endpoint /customers/group/{customerGroupId}', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestEndpoint', baseContext);

      const apiResponse = await apiContext.put(`customers/group/${idCustomerGroup}`, {
        headers: {
          Authorization: `Bearer ${accessToken}`,
        },
        data: {
          customerGroupId: idCustomerGroup,
          localizedNames: {
            [Languages.french.id]: updateGroupData.frName,
            [Languages.english.id]: updateGroupData.name,
          },
          reductionPercent: updateGroupData.discount,
          displayPriceTaxExcluded: updateGroupData.priceDisplayMethod === 'Tax excluded',
          showPrice: updateGroupData.shownPrices,
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

    it('should check the JSON Response', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseJSON', baseContext);

      expect(jsonResponse.customerGroupId).to.equal(idCustomerGroup);
      expect(jsonResponse.localizedNames).to.deep.equal({1: updateGroupData.name, 2: updateGroupData.frName});
      expect(jsonResponse.reductionPercent).to.equal(updateGroupData.discount);
      expect(jsonResponse.displayPriceTaxExcluded).to.equal(updateGroupData.priceDisplayMethod === 'Tax excluded');
      expect(jsonResponse.showPrice).to.equal(updateGroupData.shownPrices);
      expect(jsonResponse.shopIds).to.deep.equal([1]);
    });
  });

  describe('BackOffice : Check the Customer Group is updated', async () => {
    it('should filter list by id', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterAfterUpdate', baseContext);

      await groupsPage.resetFilter(page);
      await groupsPage.filterTable(page, 'input', 'id_group', idCustomerGroup);

      const numberOfGroupsAfterCreation = await groupsPage.getNumberOfElementInGrid(page);
      expect(numberOfGroupsAfterCreation).to.be.equal(1);

      idCustomerGroup = parseInt(await groupsPage.getTextColumn(page, 1, 'id_group'), 10);
      expect(idCustomerGroup).to.be.equal(idCustomerGroup);
    });

    it('should edit the customer group', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editCustomerGroup', baseContext);

      await groupsPage.gotoEditGroupPage(page, 1);

      const pageTitle = await addGroupPage.getPageTitle(page);
      expect(pageTitle).to.contains(addGroupPage.pageTitleEdit);
    });

    it('should check the JSON Response : `localizedNames` (EN)', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseLocalizedNamesEN', baseContext);

      const value = await addGroupPage.getValue(page, 'localizedNames', Languages.english.id);
      expect(jsonResponse.localizedNames[Languages.english.id]).to.be.equal(value);
    });

    it('should check the JSON Response : `localizedNames` (FR)', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseLocalizedNamesFR', baseContext);

      const value = await addGroupPage.getValue(page, 'localizedNames', Languages.french.id);
      expect(jsonResponse.localizedNames[Languages.french.id]).to.be.equal(value);
    });

    it('should check the JSON Response : `reductionPercent`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseReductionPercent', baseContext);

      const value = parseInt(await addGroupPage.getValue(page, 'reductionPercent'), 10);
      expect(jsonResponse.reductionPercent).to.be.equal(value);
    });

    it('should check the JSON Response : `displayPriceTaxExcluded`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseDisplayPriceTaxExcluded', baseContext);

      const value = (await addGroupPage.getValue(page, 'displayPriceTaxExcluded')) === 'Tax excluded';
      expect(jsonResponse.displayPriceTaxExcluded).to.be.equal(value);
    });

    it('should check the JSON Response : `showPrice`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseShowPrice', baseContext);

      const value = (await addGroupPage.getValue(page, 'showPrice')) === '1';
      expect(jsonResponse.showPrice).to.be.equal(value);
    });
  });

  describe('BackOffice : Delete the Customer Group', async () => {
    it('should go to \'Groups\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToGroupsPageForDeletion', baseContext);

      await customerSettingsPage.goToGroupsPage(page);

      const pageTitle = await groupsPage.getPageTitle(page);
      expect(pageTitle).to.contains(groupsPage.pageTitle);
    });

    it('should filter list by id', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForDeletion', baseContext);

      await groupsPage.resetFilter(page);
      await groupsPage.filterTable(page, 'input', 'id_group', idCustomerGroup);

      const numberOfGroupsAfterCreation = await groupsPage.getNumberOfElementInGrid(page);
      expect(numberOfGroupsAfterCreation).to.be.equal(1);

      idCustomerGroup = parseInt(await groupsPage.getTextColumn(page, 1, 'id_group'), 10);
      expect(idCustomerGroup).to.be.equal(idCustomerGroup);
    });

    it('should delete group', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteGroup', baseContext);

      const textResult = await groupsPage.deleteGroup(page, 1);
      expect(textResult).to.contains(groupsPage.successfulDeleteMessage);
    });

    it('should reset filter', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterDelete', baseContext);

      const numberOfGroupsAfterDelete = await groupsPage.resetAndGetNumberOfLines(page);
      expect(numberOfGroupsAfterDelete).to.be.equal(numberOfGroups);
    });
  });

  // Pre-condition: Create an API Client
  deleteAPIClientTest(`${baseContext}_postTest`);
});
