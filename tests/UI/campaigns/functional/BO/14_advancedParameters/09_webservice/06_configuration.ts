// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import webservicePage from '@pages/BO/advancedParameters/webservice';
import addWebservicePage from '@pages/BO/advancedParameters/webservice/add';

import {expect} from 'chai';
import type {APIRequestContext, BrowserContext, Page} from 'playwright';
import {
  boDashboardPage,
  FakerWebservice,
  utilsPlaywright,
  utilsXML,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_advancedParameters_webservice_configuration';

// Create, Read, Update and Delete webservice key in BO
describe('BO - Advanced Parameters - Webservice : Configuration', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let apiContext: APIRequestContext;

  let numberOfWebserviceKeys: number = 0;

  const webserviceData: FakerWebservice = new FakerWebservice({
    permissions: [
      {
        resource: 'addresses',
        methods: ['all'],
      },
    ],
  });

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);

    apiContext = await utilsPlaywright.createAPIContext(global.BO.URL);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  it('should login in BO', async function () {
    await loginCommon.loginBO(this, page);
  });

  it('should go to \'Advanced Parameters > Webservice\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToWebservicePage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.advancedParametersLink,
      boDashboardPage.webserviceLink,
    );
    await webservicePage.closeSfToolBar(page);

    const pageTitle = await webservicePage.getPageTitle(page);
    expect(pageTitle).to.contains(webservicePage.pageTitle);
  });

  it('should reset all filters and get number of webservices', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'firstReset', baseContext);

    numberOfWebserviceKeys = await webservicePage.resetAndGetNumberOfLines(page);
    if (numberOfWebserviceKeys !== 0) expect(numberOfWebserviceKeys).to.be.above(0);
  });

  it('should go to add new webservice key page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewWebserviceKeyPage', baseContext);

    await webservicePage.goToAddNewWebserviceKeyPage(page);

    const pageTitle = await addWebservicePage.getPageTitle(page);
    expect(pageTitle).to.contains(addWebservicePage.pageTitleCreate);
  });

  it('should create webservice key and check result', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'createWebserviceKey', baseContext);

    const textResult = await addWebservicePage.createEditWebservice(page, webserviceData, false);
    expect(textResult).to.equal(addWebservicePage.successfulCreationMessage);

    const numberOfWebserviceKeysAfterCreation = await webservicePage.getNumberOfElementInGrid(page);
    expect(numberOfWebserviceKeysAfterCreation).to.be.equal(numberOfWebserviceKeys + 1);
  });

  it('should enable the webservice', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'setWebserviceStatusTrue', baseContext);

    const textResult = await webservicePage.setWebserviceStatus(page, true);
    expect(textResult).to.contains(webservicePage.successfulUpdateMessage);
  });

  it('should check endpoint API', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkEndpointAfterEnable', baseContext);

    const credentialsBase64 = Buffer.from(`${webserviceData.key}:`).toString('base64');
    const apiResponse = await apiContext.get('/api', {
      headers: {
        Authorization: `Basic ${credentialsBase64}`,
      },
    });
    expect(apiResponse.status()).to.eq(200);
    const xmlResponse = await apiResponse.text();

    const isValidXML = utilsXML.isValid(xmlResponse);
    expect(isValidXML).to.eq(true);

    expect(utilsXML.getRootNodeName(xmlResponse)).to.be.eq('prestashop');
  });

  it('should disable the webservice', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'setWebserviceStatusFalse', baseContext);

    const textResult = await webservicePage.setWebserviceStatus(page, false);
    expect(textResult).to.contains(webservicePage.successfulUpdateMessage);
  });

  it('should check endpoint API', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkEndpointAfterDisable', baseContext);

    const credentialsBase64 = Buffer.from(`${webserviceData.key}:`).toString('base64');
    const apiResponse = await apiContext.get('/api', {
      headers: {
        Authorization: `Basic ${credentialsBase64}`,
      },
    });
    expect(apiResponse.status()).to.eq(503);
    const xmlResponse = await apiResponse.text();

    const isValidXML = utilsXML.isValid(xmlResponse);
    expect(isValidXML).to.eq(true);

    expect(utilsXML.getNodeValue(xmlResponse, '/prestashop/errors/error/message'))
      .to.equals('The PrestaShop webservice is disabled. Please activate it in the PrestaShop Back Office');
  });

  it('should delete webservice key', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'deleteWebserviceKey', baseContext);

    const textResult = await webservicePage.deleteWebserviceKey(page, 1);
    expect(textResult).to.equal(webservicePage.successfulDeleteMessage);
  });
});
