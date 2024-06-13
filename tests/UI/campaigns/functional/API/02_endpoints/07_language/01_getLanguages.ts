// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import {deleteAPIClientTest} from '@commonTests/BO/advancedParameters/authServer';
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import apiClientPage from 'pages/BO/advancedParameters/APIClient';
import addNewApiClientPage from '@pages/BO/advancedParameters/APIClient/add';
import localizationPage from '@pages/BO/international/localization';
import languagesPage from '@pages/BO/international/languages';
import addLanguagePage from '@pages/BO/international/languages/add';

import {expect} from 'chai';
import type {APIRequestContext, BrowserContext, Page} from 'playwright';
import {
  boDashboardPage,
  FakerAPIClient,
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
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Advanced Parameters > API Client\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAdminAPIPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.advancedParametersLink,
        boDashboardPage.adminAPILink,
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
      await localizationPage.closeSfToolBar(page);

      const pageTitle = await localizationPage.getPageTitle(page);
      expect(pageTitle).to.contains(localizationPage.pageTitle);
    });

    it('should go to \'Languages\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLanguagesPage', baseContext);

      await localizationPage.goToSubTabLanguages(page);

      const pageTitle = await languagesPage.getPageTitle(page);
      expect(pageTitle).to.contains(languagesPage.pageTitle);

      const numLanguages = await languagesPage.resetAndGetNumberOfLines(page);
      expect(numLanguages).to.eq(jsonResponse.totalItems);
    });

    it('should filter list by id', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkJSONItems', baseContext);

      for (let idxItem: number = 0; idxItem < jsonResponse.totalItems; idxItem++) {
        // eslint-disable-next-line no-loop-func
        await languagesPage.resetFilter(page);
        await languagesPage.filterTable(page, 'input', 'id_lang', jsonResponse.items[idxItem].langId);

        const numLanguages = await languagesPage.getNumberOfElementInGrid(page);
        expect(numLanguages).to.be.equal(1);

        const langId = parseInt((await languagesPage.getTextColumnFromTable(page, 1, 'id_lang')).toString(), 10);
        expect(langId).to.equal(jsonResponse.items[idxItem].langId);

        const langName = await languagesPage.getTextColumnFromTable(page, 1, 'name');
        expect(langName).to.equal(jsonResponse.items[idxItem].name);

        const langIsoCode = await languagesPage.getTextColumnFromTable(page, 1, 'iso_code');
        expect(langIsoCode).to.equal(jsonResponse.items[idxItem].isoCode);

        const langLanguageCode = await languagesPage.getTextColumnFromTable(page, 1, 'language_code');
        expect(langLanguageCode).to.equal(jsonResponse.items[idxItem].languageCode);

        // @todo : https://github.com/PrestaShop/PrestaShop/issues/35860
        // Check `jsonResponse.items[idxItem].locale`

        const langDateFormat = await languagesPage.getTextColumnFromTable(page, 1, 'date_format_lite');
        expect(langDateFormat).to.equal(jsonResponse.items[idxItem].dateFormat);

        const langDateTimeFormat = await languagesPage.getTextColumnFromTable(page, 1, 'date_format_full');
        expect(langDateTimeFormat).to.equal(jsonResponse.items[idxItem].dateTimeFormat);

        const langActive = await languagesPage.getStatus(page, 1);
        expect(langActive).to.equal(jsonResponse.items[idxItem].active);

        const langFlag = await languagesPage.getImgSrc(page, 1);
        expect(langFlag.split('?')[0]).to.equal(jsonResponse.items[idxItem].flag.split('?')[0]);

        // Go the edit page
        await languagesPage.goToEditLanguage(page, 1);

        const pageTitleEdit = await addLanguagePage.getPageTitle(page);
        expect(pageTitleEdit).to.contains(addLanguagePage.pageEditTitle);

        const langIsRTL = await addLanguagePage.isRTL(page);
        expect(langIsRTL).to.equal(jsonResponse.items[idxItem].isRtl);

        // Return languages tab
        await localizationPage.goToSubTabLanguages(page);

        const pageTitle = await languagesPage.getPageTitle(page);
        expect(pageTitle).to.contains(languagesPage.pageTitle);
      }
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilter', baseContext);

      await languagesPage.resetFilter(page);

      const numberOfLanguages = await languagesPage.resetAndGetNumberOfLines(page);
      expect(numberOfLanguages).to.be.above(0);
    });
  });

  // Post-condition: Delete the API Client
  deleteAPIClientTest(`${baseContext}_postTest`);
});
