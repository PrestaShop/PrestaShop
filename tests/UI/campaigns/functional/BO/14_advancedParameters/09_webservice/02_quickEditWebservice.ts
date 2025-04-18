import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boDashboardPage,
  boLoginPage,
  boWebservicesPage,
  boWebservicesCreatePage,
  type BrowserContext,
  FakerWebservice,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_advancedParameters_webservice_quickEditWebservice';

describe('BO - Advanced Parameters - Webservice : Quick edit webservice', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  let numberOfWebserviceKeys: number = 0;

  const webServiceData: FakerWebservice = new FakerWebservice();

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
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

  it('should go to \'Advanced parameters > Webservice\' page', async function () {
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

  it('should create webservice key', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'createWebserviceKey', baseContext);

    const textResult = await boWebservicesCreatePage.createEditWebservice(page, webServiceData, false);
    expect(textResult).to.equal(boWebservicesCreatePage.successfulCreationMessage);

    const numberOfWebserviceKeysAfterCreation = await boWebservicesPage.getNumberOfElementInGrid(page);
    expect(numberOfWebserviceKeysAfterCreation).to.be.equal(numberOfWebserviceKeys + 1);
  });

  describe('Quick edit webservice', async () => {
    const statuses = [
      {args: {status: 'disable', enable: false}},
      {args: {status: 'enable', enable: true}},
    ];

    statuses.forEach((webservice: { args: { status: string, enable: boolean } }) => {
      it(`should ${webservice.args.status} the webservice`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${webservice.args.status}Webservice`, baseContext);

        const isActionPerformed = await boWebservicesPage.setStatus(
          page,
          1,
          webservice.args.enable,
        );

        if (isActionPerformed) {
          const resultMessage = await boWebservicesPage.getValidationMessage(page);
          expect(resultMessage).to.contains(boWebservicesPage.successfulUpdateStatusMessage);
        }

        const webserviceStatus = await boWebservicesPage.getStatus(page, 1);
        expect(webserviceStatus).to.be.equal(webservice.args.enable);
      });
    });
  });

  describe('Delete the created webservice key', async () => {
    it('should delete the created webservice key', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteWebserviceKey', baseContext);

      const textResult = await boWebservicesPage.deleteWebserviceKey(page, 1);
      expect(textResult).to.equal(boWebservicesPage.successfulDeleteMessage);
    });

    it('should reset filter and check the number of webservice keys', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterDelete', baseContext);

      const numberOfElement = await boWebservicesPage.resetAndGetNumberOfLines(page);
      expect(numberOfElement).to.be.equal(numberOfWebserviceKeys);
    });
  });
});
