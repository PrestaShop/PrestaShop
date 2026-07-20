import {expect} from 'chai';
import {requestAccessToken} from '@commonTests/BO/advancedParameters/authServer';
import {
  type APIRequestContext,
  boContactsPage,
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  type Page,
  utilsAPI,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';
import testContext from '@utils/testContext';

const baseContext: string = 'functional_API_endpoints_contact_getContacts';

describe('API : GET /contacts', async () => {
  let apiContext: APIRequestContext;
  let browserContext: BrowserContext;
  let page: Page;
  let accessToken: string;
  let jsonResponse: any;

  const clientScope: string = 'contact_read';

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

  describe('API : Fetch Data', async () => {
    it('should request the endpoint /contacts', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestEndpoint', baseContext);

      const apiResponse = await apiContext.get('contacts', {
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
        'orderBy',
        'sortOrder',
        'limit',
        'filters',
        'items',
      );

      expect(jsonResponse.totalItems).to.be.gt(0);

      for (let i:number = 0; i < jsonResponse.totalItems; i++) {
        expect(jsonResponse.items[i]).to.have.all.keys(
          'contactId',
          'description',
          'email',
          'name',
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

    it('should go to \'Shop parameters > Contact\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToContactsPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.shopParametersParentLink,
        boDashboardPage.contactLink,
      );
      await boContactsPage.closeSfToolBar(page);

      const pageTitle = await boContactsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boContactsPage.pageTitle);
    });

    it('should filter list by id', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkJSONItems', baseContext);

      for (let idxItem: number = 0; idxItem < jsonResponse.totalItems; idxItem++) {
        await boContactsPage.resetFilter(page);

        await boContactsPage.filterContacts(page, 'id_contact', jsonResponse.items[idxItem].contactId.toString());

        const numContacts = await boContactsPage.getNumberOfElementInGrid(page);
        expect(numContacts).to.be.equal(1);

        const contactId = parseInt(await boContactsPage.getTextColumnFromTableContacts(page, 1, 'id_contact'), 10);
        expect(contactId).to.equal(jsonResponse.items[idxItem].contactId);

        const description = await boContactsPage.getTextColumnFromTableContacts(page, 1, 'description');
        expect(description).to.equal(jsonResponse.items[idxItem].description);

        const email = await boContactsPage.getTextColumnFromTableContacts(page, 1, 'email');
        expect(email).to.equal(jsonResponse.items[idxItem].email);

        const name = await boContactsPage.getTextColumnFromTableContacts(page, 1, 'name');
        expect(name).to.equal(jsonResponse.items[idxItem].name);
      }
    });
  });
});
