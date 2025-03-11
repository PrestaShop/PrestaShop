import testContext from '@utils/testContext';
import {expect} from 'chai';
import setGeolocationCheckCommented from '@commonTests/BO/international/geolocation';

import {
  boDashboardPage,
  boGeolocationPage,
  boLocalizationPage,
  boLoginPage,
  type BrowserContext,
  foClassicHomePage,
  type Page,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_international_localization_geolocation_updateIpAddressWhitelist';

describe('BO - International - Localization - Geolocation: Update IP address whitelist', async () => {
  const urlGeolocationDB: string = 'https://github.com/wp-statistics/GeoLite2-City/raw/master/GeoLite2-City.mmdb.gz';
  const ipDocker: string = '172.18.0.1';

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
    await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

    await boLoginPage.goTo(page, global.BO.URL);
    await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

    const pageTitle = await boDashboardPage.getPageTitle(page);
    expect(pageTitle).to.contains(boDashboardPage.pageTitle);
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

    const pageTitle = await boGeolocationPage.getPageTitle(page);
    expect(pageTitle).to.equal(boGeolocationPage.pageTitle);

    //const hasAlertBlock  = await boGeolocationPage.hasAlertBlock(page);
    //expect(hasAlertBlock).to.equal(true);

    const messageWarning = await boGeolocationPage.getWarningMessage(page);
    expect(messageWarning).to.equal(boGeolocationPage.messageWarningNeedDB);
  });

  it('should try to enable the geolocation and get an error', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'tryEnableGeolocationAndGetError', baseContext);

    await boGeolocationPage.setGeolocationByIPAddressStatus(page, true);

    const result = await boGeolocationPage.saveFormGeolocationByIPAddress(page);
    expect(result).to.equal(boGeolocationPage.messageGeolocationDBUnavailable);
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

    await boGeolocationPage.reloadPage(page);

    const hasAlertBlock = await boGeolocationPage.hasAlertBlock(page);
    expect(hasAlertBlock).to.equal(false);
  });

  it('should enable the geolocation', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'enableGeolocation', baseContext);

    await boGeolocationPage.setGeolocationByIPAddressStatus(page, true);

    const resultForm1 = await boGeolocationPage.saveFormGeolocationByIPAddress(page);
    expect(resultForm1).to.equal(boGeolocationPage.successfulUpdateMessage);
  });

  it('should disable the check on front office and go to the FO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'disableCheckFO', baseContext);

    // In local
    setGeolocationCheckCommented(true);

    // In docker
    ipAddressWhiteList = await boGeolocationPage.getWhiteListedIPAddresses(page);
    expect(ipAddressWhiteList.length).to.be.gt(0);

    await boGeolocationPage.setWhiteListedIPAddresses(page, ipAddressWhiteList.replace('127.0.0.1', ipDocker).trim());

    const resultForm2 = await boGeolocationPage.saveFormIPAddressesWhitelist(page);
    expect(resultForm2).to.equal(boGeolocationPage.successfulUpdateMessage);

    page = await boGeolocationPage.viewMyShop(page);
    await foClassicHomePage.changeLanguage(page, 'en');

    const isHomePage = await foClassicHomePage.isHomePage(page);
    expect(isHomePage).to.equal(true);
  });

  it('should update the IP Address Whitelist', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'updateIPAddressWhitelist', baseContext);

    page = await foClassicHomePage.changePage(browserContext, 0);

    await boGeolocationPage.setWhiteListedIPAddresses(page, ipAddressWhiteList.replace(ipDocker, '').trim());

    const result = await boGeolocationPage.saveFormIPAddressesWhitelist(page);
    expect(result).to.equal(boGeolocationPage.successfulUpdateMessage);
  });

  it('should check the front office ', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkFO301', baseContext);

    page = await boGeolocationPage.changePage(browserContext, 1);
    await foClassicHomePage.reloadPage(page);

    const isRestrictedPage = await foClassicHomePage.isRestrictedPage(page);
    expect(isRestrictedPage).to.equal(true);

    const restrictedText = await foClassicHomePage.getRestrictedText(page);
    expect(restrictedText).to.equal(foClassicHomePage.restrictedContentCountry);
  });

  it('should disable the geolocation', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'disableGeolocation', baseContext);

    page = await foClassicHomePage.changePage(browserContext, 0);

    await boGeolocationPage.setGeolocationByIPAddressStatus(page, false);

    const result = await boGeolocationPage.saveFormGeolocationByIPAddress(page);
    expect(result).to.equal(boGeolocationPage.successfulUpdateMessage);
  });

  it('should check the front office ', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkFO200', baseContext);

    page = await boGeolocationPage.changePage(browserContext, 1);

    await foClassicHomePage.reloadPage(page);

    const isHomePage = await foClassicHomePage.isHomePage(page);
    expect(isHomePage).to.equal(true);
  });

  it('should reset the IP Address Whitelist', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetIPAddressWhitelist', baseContext);

    page = await foClassicHomePage.changePage(browserContext, 0);

    await boGeolocationPage.setWhiteListedIPAddresses(page, ipAddressWhiteList);

    const result = await boGeolocationPage.saveFormIPAddressesWhitelist(page);
    expect(result).to.equal(boGeolocationPage.successfulUpdateMessage);
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
