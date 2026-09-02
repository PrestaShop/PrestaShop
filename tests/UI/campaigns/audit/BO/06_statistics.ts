import {expect} from 'chai';
import testContext from '@utils/testContext';

import {
  boDashboardPage,
  boLoginPage,
  boStatisticsPage,
  type BrowserContext,
  dataModules,
  FakerModule,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'audit_BO_statistics';

describe('BO - Statistics', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  before(async function () {
    utilsPlaywright.setErrorsCaptured(true);

    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  beforeEach(async () => {
    utilsPlaywright.resetJsErrors();
  });

  it('should login in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

    await boLoginPage.goTo(page, global.BO.URL);
    await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

    const pageTitle = await boDashboardPage.getPageTitle(page);
    expect(pageTitle).to.contains(boDashboardPage.pageTitle);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  it('should go to \'Statistics\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToStatisticsPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      '',
      boDashboardPage.statsLink,
    );

    const pageTitle = await boStatisticsPage.getPageTitle(page);
    expect(pageTitle).to.contains(boStatisticsPage.pageTitle);

    const jsErrors = utilsPlaywright.getJsErrors();
    expect(jsErrors.length).to.equals(0);
  });

  [
    {module: dataModules.statsstock, title: boStatisticsPage.subTabTitleStatsstock},
    {module: dataModules.statsbestmanufacturers, title: boStatisticsPage.subTabTitleStatsbestmanufacturers},
    {module: dataModules.statsbestcategories, title: boStatisticsPage.subTabTitleStatsbestcategories},
    {module: dataModules.statsbestcustomers, title: boStatisticsPage.subTabTitleStatsbestcustomers},
    {module: dataModules.statsbestsuppliers, title: boStatisticsPage.subTabTitleStatsbestsuppliers},
    {module: dataModules.statsbestvouchers, title: boStatisticsPage.subTabTitleStatsbestvouchers},
    {module: dataModules.statsbestproducts, title: boStatisticsPage.subTabTitleStatsbestproducts},
    {module: dataModules.statscarrier, title: boStatisticsPage.subTabTitleStatscarrier},
    {module: dataModules.statscheckup, title: boStatisticsPage.subTabTitleStatscheckup},
    {module: dataModules.statscatalog, title: boStatisticsPage.subTabTitleStatscatalog},
    {module: dataModules.statsregistrations, title: boStatisticsPage.subTabTitleStatsregistrations},
    {module: dataModules.statsnewsletter, title: boStatisticsPage.subTabTitleStatsnewsletter},
    {module: dataModules.pagesnotfound, title: boStatisticsPage.subTabTitlePagesnotfound},
    {module: dataModules.statsproduct, title: boStatisticsPage.subTabTitleStatsproduct},
    {module: dataModules.statspersonalinfos, title: boStatisticsPage.subTabTitleStatspersonalinfos},
    {module: dataModules.statssales, title: boStatisticsPage.subTabTitleStatssales},
    {module: dataModules.statssearch, title: boStatisticsPage.subTabTitleStatssearch},
    {module: dataModules.statsforecast, title: boStatisticsPage.subTabTitleStatsforecast},
  ].forEach((test: {module: FakerModule, title: string}, index:number) => {
    it(`should go to the subtab '${test.module.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goToSubTab${index}`, baseContext);

      await boStatisticsPage.goToSubTab(page, test.module.tag);

      const subTabTitle = await boStatisticsPage.getSubTabTitle(page);
      expect(subTabTitle).to.equals(test.title);

      const jsErrors = utilsPlaywright.getJsErrors();
      expect(jsErrors.length).to.equals(0);
    });
  });
});
