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
  boLocalizationPage,
  boLanguagesPage,
  boLanguagesCreatePage,
  boLoginPage,
  type BrowserContext,
  FakerAPIClient,
  type Page,
  utilsAPI,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_API_endpoints_language_getLanguages';

describe('API : GET /languages', async () => {
  let apiContext: APIRequestContext;
  let browserContext: BrowserContext;
  let page: Page;
  let clientSecret: string;
  let accessToken: string;
  let jsonResponse: any;

  const clientData: FakerAPIClient = new FakerAPIClient({
    enabled: true,
    scopes: [],
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

  describe('API : Fetch Data', async () => {
    it('should request the endpoint /languages', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestEndpoint', baseContext);

      const apiResponse = await apiContext.get('languages', {
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
          'langId',
          'name',
          'isoCode',
          'languageCode',
          'locale',
          'dateFormat',
          'dateTimeFormat',
          'isRtl',
          'active',
          'flag',
        );
      }
    });
  });

  describe('BackOffice : Expected data', async () => {
    it('should go to \'International > Localization\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLocalizationPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.internationalParentLink,
        boDashboardPage.localizationLink,
      );
      await boLocalizationPage.closeSfToolBar(page);

      const pageTitle = await boLocalizationPage.getPageTitle(page);
      expect(pageTitle).to.contains(boLocalizationPage.pageTitle);
    });

    it('should go to \'Languages\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLanguagesPage', baseContext);

      await boLocalizationPage.goToSubTabLanguages(page);

      const pageTitle = await boLanguagesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boLanguagesPage.pageTitle);

      const numLanguages = await boLanguagesPage.resetAndGetNumberOfLines(page);
      expect(numLanguages).to.eq(jsonResponse.totalItems);
    });

    it('should filter list by id', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkJSONItems', baseContext);

      for (let idxItem: number = 0; idxItem < jsonResponse.totalItems; idxItem++) {
        // eslint-disable-next-line no-loop-func
        await boLanguagesPage.resetFilter(page);
        await boLanguagesPage.filterTable(page, 'input', 'id_lang', jsonResponse.items[idxItem].langId);

        const numLanguages = await boLanguagesPage.getNumberOfElementInGrid(page);
        expect(numLanguages).to.be.equal(1);

        const langId = parseInt((await boLanguagesPage.getTextColumnFromTable(page, 1, 'id_lang')).toString(), 10);
        expect(langId).to.equal(jsonResponse.items[idxItem].langId);

        const langName = await boLanguagesPage.getTextColumnFromTable(page, 1, 'name');
        expect(langName).to.equal(jsonResponse.items[idxItem].name);

        const langIsoCode = await boLanguagesPage.getTextColumnFromTable(page, 1, 'iso_code');
        expect(langIsoCode).to.equal(jsonResponse.items[idxItem].isoCode);

        const langLanguageCode = await boLanguagesPage.getTextColumnFromTable(page, 1, 'language_code');
        expect(langLanguageCode).to.equal(jsonResponse.items[idxItem].languageCode);

        const langLocale = await boLanguagesPage.getTextColumnFromTable(page, 1, 'locale');
        expect(langLocale).to.equal(jsonResponse.items[idxItem].locale);

        const langDateFormat = await boLanguagesPage.getTextColumnFromTable(page, 1, 'date_format_lite');
        expect(langDateFormat).to.equal(jsonResponse.items[idxItem].dateFormat);

        const langDateTimeFormat = await boLanguagesPage.getTextColumnFromTable(page, 1, 'date_format_full');
        expect(langDateTimeFormat).to.equal(jsonResponse.items[idxItem].dateTimeFormat);

        const langActive = await boLanguagesPage.getStatus(page, 1);
        expect(langActive).to.equal(jsonResponse.items[idxItem].active);

        const langFlag = await boLanguagesPage.getImgSrc(page, 1);
        expect(langFlag.split('?')[0]).to.equal(jsonResponse.items[idxItem].flag.split('?')[0]);

        // Go the edit page
        await boLanguagesPage.goToEditLanguage(page, 1);

        const pageTitleEdit = await boLanguagesCreatePage.getPageTitle(page);
        expect(pageTitleEdit).to.contains(boLanguagesCreatePage.pageEditTitle);

        const langIsRTL = await boLanguagesCreatePage.isRTL(page);
        expect(langIsRTL).to.equal(jsonResponse.items[idxItem].isRtl);

        // Return languages tab
        await boLocalizationPage.goToSubTabLanguages(page);

        const pageTitle = await boLanguagesPage.getPageTitle(page);
        expect(pageTitle).to.contains(boLanguagesPage.pageTitle);
      }
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilter', baseContext);

      await boLanguagesPage.resetFilter(page);

      const numberOfLanguages = await boLanguagesPage.resetAndGetNumberOfLines(page);
      expect(numberOfLanguages).to.be.above(0);
    });
  });

  // Post-condition: Delete the API Client
  deleteAPIClientTest(`${baseContext}_postTest`);
});
