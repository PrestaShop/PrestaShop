// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import {deleteAPIClientTest} from '@commonTests/BO/advancedParameters/authServer';

import {expect} from 'chai';
import {
  type APIRequestContext,
  boApiClientsPage,
  boApiClientsCreatePage,
  boDashboardPage,
  boDesignPositionsPage,
  boLoginPage,
  type BrowserContext,
  FakerAPIClient,
  type Page,
  utilsAPI,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_API_endpoints_hook_getHooksId';

describe('API : GET /hook/{id}', async () => {
  let apiContext: APIRequestContext;
  let browserContext: BrowserContext;
  let page: Page;
  let clientSecret: string;
  let accessToken: string;
  let jsonResponse: any;
  let idHook: number;
  let statusHook: boolean;
  let nameHook: string;
  let titleHook: string;
  let descriptionHook: string;

  const clientScope: string = 'hook_read';
  const clientData: FakerAPIClient = new FakerAPIClient({
    enabled: true,
    scopes: [
      clientScope,
    ],
  });

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

  describe('BackOffice : Expected data', async () => {
    it('should go to \'Design > Positions\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToPositionsPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.designParentLink,
        boDashboardPage.positionsLink,
      );
      await boDesignPositionsPage.closeSfToolBar(page);

      const pageTitle = await boDesignPositionsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDesignPositionsPage.pageTitle);
    });

    it('should get the hook informations', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getHookInformations', baseContext);

      idHook = await boDesignPositionsPage.getHookId(page, 0);
      expect(idHook).to.be.gt(0);

      statusHook = await boDesignPositionsPage.getHookStatus(page, 0);
      expect(statusHook).to.be.equal(true);

      nameHook = await boDesignPositionsPage.getHookName(page, 0);
      expect(nameHook.length).to.be.gt(0);

      titleHook = await boDesignPositionsPage.getHookTitle(page, 0);
      expect(titleHook.length).to.be.gt(0);

      descriptionHook = await boDesignPositionsPage.getHookDescription(page, 0);
      expect(descriptionHook.length).to.be.gt(0);
    });
  });

  describe('API : Check Data', async () => {
    it('should request the endpoint /hook/{id}', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestEndpoint', baseContext);

      const apiResponse = await apiContext.get(`hook/${idHook}`, {
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
        'id',
        'active',
        'name',
        'title',
        'description',
      );
    });

    it('should check the JSON Response : `id`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseId', baseContext);

      expect(jsonResponse).to.have.property('id');
      expect(jsonResponse.id).to.be.a('number');
      expect(jsonResponse.id).to.be.equal(idHook);
    });

    it('should check the JSON Response : `active`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseActive', baseContext);

      expect(jsonResponse).to.have.property('active');
      expect(jsonResponse.active).to.be.a('boolean');
      expect(jsonResponse.active).to.be.equal(statusHook);
    });

    it('should check the JSON Response : `name`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseName', baseContext);

      expect(jsonResponse).to.have.property('name');
      expect(jsonResponse.name).to.be.a('string');
      expect(jsonResponse.name).to.be.equal(nameHook);
    });

    it('should check the JSON Response : `title`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseTitle', baseContext);

      expect(jsonResponse).to.have.property('title');
      expect(jsonResponse.title).to.be.a('string');
      expect(jsonResponse.title).to.be.equal(titleHook);
    });

    it('should check the JSON Response : `description`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseDescription', baseContext);

      expect(jsonResponse).to.have.property('description');
      expect(jsonResponse.description).to.be.a('string');
      expect(jsonResponse.description).to.be.equal(descriptionHook);
    });
  });

  // Pre-condition: Create an API Client
  deleteAPIClientTest(`${baseContext}_postTest`);
});
