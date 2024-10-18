// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import {deleteAPIClientTest} from '@commonTests/BO/advancedParameters/authServer';

import {
  type APIRequestContext,
  boApiClientsPage,
  boApiClientsCreatePage,
  boCustomerGroupsPage,
  boCustomerGroupsCreatePage,
  boCustomerSettingsPage,
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  FakerAPIClient,
  FakerGroup,
  type Page,
  utilsAPI,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';

const baseContext: string = 'functional_API_endpoints_customerGroup_deleteCustomerGroupsId';

describe('API : DELETE /customers/group/{customerGroupId}', async () => {
  let apiContext: APIRequestContext;
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfGroups: number;
  let idCustomerGroup: number;
  let clientSecret: string;
  let accessToken: string;

  const clientScope: string = 'customer_group_write';
  const clientData: FakerAPIClient = new FakerAPIClient({
    enabled: true,
    scopes: [
      clientScope,
    ],
  });
  const createGroupData: FakerGroup = new FakerGroup();

  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);

    apiContext = await utilsPlaywright.createAPIContext(global.API.URL);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('BackOffice : Fetch the access token', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Advanced Parameters > API Client\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAdminAPIPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.advancedParametersLink,
        boDashboardPage.adminAPILink,
      );

      const pageTitle = await boApiClientsPage.getPageTitle(page);
      expect(pageTitle).to.eq(boApiClientsPage.pageTitle);
    });

    it('should check that no records found', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkThatNoRecordFound', baseContext);

      const noRecordsFoundText = await boApiClientsPage.getTextForEmptyTable(page);
      expect(noRecordsFoundText).to.contains('warning No records found');
    });

    it('should go to add New API Client page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewAPIClientPage', baseContext);

      await boApiClientsPage.goToNewAPIClientPage(page);

      const pageTitle = await boApiClientsCreatePage.getPageTitle(page);
      expect(pageTitle).to.eq(boApiClientsCreatePage.pageTitleCreate);
    });

    it('should create API Client', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createAPIClient', baseContext);

      const textResult = await boApiClientsCreatePage.addAPIClient(page, clientData);
      expect(textResult).to.contains(boApiClientsCreatePage.successfulCreationMessage);

      const textMessage = await boApiClientsCreatePage.getAlertInfoBlockParagraphContent(page);
      expect(textMessage).to.contains(boApiClientsCreatePage.apiClientGeneratedMessage);
    });

    it('should copy client secret', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'copyClientSecret', baseContext);

      await boApiClientsCreatePage.copyClientSecret(page);

      clientSecret = await boApiClientsCreatePage.getClipboardText(page);
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
      expect(utilsAPI.hasResponseHeader(apiResponse, 'Content-Type')).to.eq(true);
      expect(utilsAPI.getResponseHeader(apiResponse, 'Content-Type')).to.contains('application/json');

      const jsonResponse = await apiResponse.json();
      expect(jsonResponse).to.have.property('access_token');
      expect(jsonResponse.token_type).to.be.a('string');

      accessToken = jsonResponse.access_token;
    });
  });

  describe('BackOffice : Create a Customer Group', async () => {
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

      numberOfGroups = await boCustomerGroupsPage.resetAndGetNumberOfLines(page);
      expect(numberOfGroups).to.be.above(0);
    });

    it('should go to add new group page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewGroup', baseContext);

      await boCustomerGroupsPage.goToNewGroupPage(page);

      const pageTitle = await boCustomerGroupsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boCustomerGroupsCreatePage.pageTitleCreate);
    });

    it('should create group and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createGroup', baseContext);

      const textResult = await boCustomerGroupsCreatePage.createEditGroup(page, createGroupData);
      expect(textResult).to.contains(boCustomerGroupsPage.successfulCreationMessage);

      const numberOfGroupsAfterCreation = await boCustomerGroupsPage.getNumberOfElementInGrid(page);
      expect(numberOfGroupsAfterCreation).to.be.equal(numberOfGroups + 1);
    });

    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForCreation', baseContext);

      await boCustomerGroupsPage.resetFilter(page);
      await boCustomerGroupsPage.filterTable(page, 'input', 'b!name', createGroupData.name);

      const numberOfGroupsAfterCreation = await boCustomerGroupsPage.getNumberOfElementInGrid(page);
      expect(numberOfGroupsAfterCreation).to.be.equal(1);

      const textEmail = await boCustomerGroupsPage.getTextColumn(page, 1, 'b!name');
      expect(textEmail).to.contains(createGroupData.name);

      idCustomerGroup = parseInt(await boCustomerGroupsPage.getTextColumn(page, 1, 'id_group'), 10);
      expect(idCustomerGroup).to.be.gt(0);
    });
  });

  describe('API : Delete the Customer Group', async () => {
    it('should request the endpoint /customers/group/{customerGroupId}', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestEndpoint', baseContext);

      const apiResponse = await apiContext.delete(`customers/group/${idCustomerGroup}`, {
        headers: {
          Authorization: `Bearer ${accessToken}`,
        },
      });
      expect(apiResponse.status()).to.eq(204);
    });
  });

  describe('BackOffice : Check the Customer Group is deleted', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterAfterDeletion', baseContext);

      await boCustomerGroupsPage.resetFilter(page);
      await boCustomerGroupsPage.filterTable(page, 'input', 'b!name', createGroupData.name);

      const numberOfGroupsAfterCreation = await boCustomerGroupsPage.getNumberOfElementInGrid(page);
      expect(numberOfGroupsAfterCreation).to.be.equal(0);
    });
  });

  // Pre-condition: Create an API Client
  deleteAPIClientTest(`${baseContext}_postTest`);
});
