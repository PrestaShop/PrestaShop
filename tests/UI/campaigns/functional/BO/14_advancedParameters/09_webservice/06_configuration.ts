import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  type APIRequestContext,
  boDashboardPage,
  boLoginPage,
  boWebservicesPage,
  boWebservicesCreatePage,
  type BrowserContext,
  FakerWebservice,
  type Page,
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
    await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

    await boLoginPage.goTo(page, global.BO.URL);
    await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

    const pageTitle = await boDashboardPage.getPageTitle(page);
    expect(pageTitle).to.contains(boDashboardPage.pageTitle);
  });

  it('should go to \'Advanced Parameters > Webservice\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToWebservicePage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.advancedParametersLink,
      boDashboardPage.webserviceLink,
    );
    await boWebservicesPage.closeSfToolBar(page);

    const pageTitle = await boWebservicesPage.getPageTitle(page);
    expect(pageTitle).to.contains(boWebservicesPage.pageTitle);
  });

  it('should reset all filters and get number of webservices', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'firstReset', baseContext);

    numberOfWebserviceKeys = await boWebservicesPage.resetAndGetNumberOfLines(page);
    if (numberOfWebserviceKeys !== 0) expect(numberOfWebserviceKeys).to.be.above(0);
  });

  it('should go to add new webservice key page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewWebserviceKeyPage', baseContext);

    await boWebservicesPage.goToAddNewWebserviceKeyPage(page);

    const pageTitle = await boWebservicesCreatePage.getPageTitle(page);
    expect(pageTitle).to.contains(boWebservicesCreatePage.pageTitleCreate);
  });

  it('should create webservice key and check result', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'createWebserviceKey', baseContext);

    const textResult = await boWebservicesCreatePage.createEditWebservice(page, webserviceData, false);
    expect(textResult).to.equal(boWebservicesCreatePage.successfulCreationMessage);

    const numberOfWebserviceKeysAfterCreation = await boWebservicesPage.getNumberOfElementInGrid(page);
    expect(numberOfWebserviceKeysAfterCreation).to.be.equal(numberOfWebserviceKeys + 1);
  });

  it('should enable the webservice', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'setWebserviceStatusTrue', baseContext);

    const textResult = await boWebservicesPage.setWebserviceStatus(page, true);
    expect(textResult).to.contains(boWebservicesPage.successfulUpdateMessage);
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

    const textResult = await boWebservicesPage.setWebserviceStatus(page, false);
    expect(textResult).to.contains(boWebservicesPage.successfulUpdateMessage);
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

    const textResult = await boWebservicesPage.deleteWebserviceKey(page, 1);
    expect(textResult).to.equal(boWebservicesPage.successfulDeleteMessage);
  });
});
