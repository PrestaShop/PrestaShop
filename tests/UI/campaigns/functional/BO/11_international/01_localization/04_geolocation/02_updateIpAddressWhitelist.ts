// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
// Import BO pages
import geolocationPage from '@pages/BO/international/localization/geolocation';

import {expect} from 'chai';
import {BrowserContext, Page} from 'playwright';
import {
  boDashboardPage,
  boLocalizationPage,
  foClassicHomePage,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';
import setGeolocationCheckCommented from '@commonTests/BO/international/geolocation';

const baseContext: string = 'functional_BO_international_localization_geolocation_updateIpAddressWhitelist';

describe('BO - International - Localization - Geolocation: Update IP address whitelist', async () => {
  const urlGeolocationDB: string = 'https://github.com/wp-statistics/GeoLite2-City/raw/master/GeoLite2-City.mmdb.gz';

  let browserContext: BrowserContext;
  let page: Page;
  let ipAddressWhiteList: string;

  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  it('should login in BO', async function () {
    await loginCommon.loginBO(this, page);
  });

  it('should go to \'International > Localization\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToLocalizationPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.internationalParentLink,
      boDashboardPage.localizationLink,
    );
    await boLocalizationPage.closeSfToolBar(page);

    const pageTitle = await boLocalizationPage.getPageTitle(page);
    expect(pageTitle).to.equal(boLocalizationPage.pageTitle);
  });

  it('should go to \'Geolocation\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToGeolocationPage', baseContext);

    await boLocalizationPage.goToSubTabGeolocation(page);

    const pageTitle = await geolocationPage.getPageTitle(page);
    expect(pageTitle).to.equal(geolocationPage.pageTitle);

    //const hasAlertBlock  = await geolocationPage.hasAlertBlock(page);
    //expect(hasAlertBlock).to.equal(true);

    const messageWarning = await geolocationPage.getWarningMessage(page);
    expect(messageWarning).to.equal(geolocationPage.messageWarningNeedDB);
  });

  it('should try to enable the geolocation and get an error', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'tryEnableGeolocationAndGetError', baseContext);

    await geolocationPage.setGeolocationByIPAddressStatus(page, true);

    const result = await geolocationPage.saveFormGeolocationByIPAddress(page);
    expect(result).to.equal(geolocationPage.messageGeolocationDBUnavailable);
  });

  it('should download the database', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'downloadDB', baseContext);

    await utilsFile.downloadFile(
      urlGeolocationDB,
      `${utilsFile.getRootPath()}/app/Resources/geoip/GeoLite2-City.mmdb.gz`,
    );
    utilsFile.gunzip(
      `${utilsFile.getRootPath()}/app/Resources/geoip/GeoLite2-City.mmdb.gz`,
      `${utilsFile.getRootPath()}/app/Resources/geoip/GeoLite2-City.mmdb`,
    );

    const hasFound = await utilsFile.doesFileExist(`${utilsFile.getRootPath()}/app/Resources/geoip/GeoLite2-City.mmdb`);
    expect(hasFound).to.equal(true);

    await geolocationPage.reloadPage(page);

    const hasAlertBlock = await geolocationPage.hasAlertBlock(page);
    expect(hasAlertBlock).to.equal(false);
  });

  it('should enable the geolocation', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'enableGeolocation', baseContext);

    await geolocationPage.setGeolocationByIPAddressStatus(page, true);

    const result = await geolocationPage.saveFormGeolocationByIPAddress(page);
    expect(result).to.equal(geolocationPage.successfulUpdateMessage);
  });

  it('should disable the check on front office and go to the FO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'disableCheckFO', baseContext);

    setGeolocationCheckCommented(true);

    page = await geolocationPage.viewMyShop(page);
    await foClassicHomePage.changeLanguage(page, 'en');

    const isHomePage = await foClassicHomePage.isHomePage(page);
    expect(isHomePage).to.equal(true);
  });

  it('should update the IP Address Whitelist', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'updateIPAddressWhitelist', baseContext);

    page = await foClassicHomePage.changePage(browserContext, 0);

    ipAddressWhiteList = await geolocationPage.getWhiteListedIPAddresses(page);
    expect(ipAddressWhiteList.length).to.be.gt(0);

    await geolocationPage.setWhiteListedIPAddresses(page, ipAddressWhiteList.replace('127.0.0.1', '').trim());

    const result = await geolocationPage.saveFormIPAddressesWhitelist(page);
    expect(result).to.equal(geolocationPage.successfulUpdateMessage);
  });

  it('should check the front office ', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkFO301', baseContext);

    page = await geolocationPage.changePage(browserContext, 1);
    await foClassicHomePage.reloadPage(page);

    const isRestrictedPage = await foClassicHomePage.isRestrictedPage(page);
    expect(isRestrictedPage).to.equal(true);

    const restrictedText = await foClassicHomePage.getRestrictedText(page);
    expect(restrictedText).to.equal(foClassicHomePage.restrictedContentCountry);
  });

  it('should disable the geolocation', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'disableGeolocation', baseContext);

    page = await foClassicHomePage.changePage(browserContext, 0);

    await geolocationPage.setGeolocationByIPAddressStatus(page, false);

    const result = await geolocationPage.saveFormGeolocationByIPAddress(page);
    expect(result).to.equal(geolocationPage.successfulUpdateMessage);
  });

  it('should check the front office ', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkFO200', baseContext);

    page = await geolocationPage.changePage(browserContext, 1);

    await foClassicHomePage.reloadPage(page);

    const isHomePage = await foClassicHomePage.isHomePage(page);
    expect(isHomePage).to.equal(true);
  });

  it('should reset the IP Address Whitelist', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetIPAddressWhitelist', baseContext);

    page = await foClassicHomePage.changePage(browserContext, 0);

    await geolocationPage.setWhiteListedIPAddresses(page, ipAddressWhiteList);

    const result = await geolocationPage.saveFormIPAddressesWhitelist(page);
    expect(result).to.equal(geolocationPage.successfulUpdateMessage);
  });

  it('should remove the comment in code', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'removeCommentInCode', baseContext);

    setGeolocationCheckCommented(false);
  });

  it('should clean the database', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'cleanDB', baseContext);

    await utilsFile.deleteFile(`${utilsFile.getRootPath()}/app/Resources/geoip/GeoLite2-City.mmdb`);

    const hasFound = await utilsFile.doesFileExist(`${utilsFile.getRootPath()}/app/Resources/geoip/GeoLite2-City.mmdb`);
    expect(hasFound).to.equal(false);

    await utilsFile.deleteFile(`${utilsFile.getRootPath()}/app/Resources/geoip/GeoLite2-City.mmdb.gz`);

    const hasFoundArchive = await utilsFile.doesFileExist(`${utilsFile.getRootPath()}/app/Resources/geoip/GeoLite2-City.mmdb.gz`);
    expect(hasFoundArchive).to.equal(false);
  });
});
