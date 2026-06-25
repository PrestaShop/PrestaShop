import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boDashboardPage,
  boLoginPage,
  boWallOfFamePage,
  type BrowserContext,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_wallOfFame_OurCommunity';

/*
Verify the Wall of Fame page displays contribution percentages for PrestaShop and Community,
and that the sum of both contributions equals 100%.
 */
describe('BO - Community - Wall of Fame', async () => {
  let browserContext: BrowserContext;
  let page: Page;

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

  it('should go to \'Wall of Fame\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToWallOfFamePage', baseContext);

    await boWallOfFamePage.goToSubMenu(page, '', boWallOfFamePage.wallOfFameLink);

    const pageTitle = await boWallOfFamePage.getPageTitle(page);
    expect(pageTitle).to.contains(boWallOfFamePage.pageTitle);
  });

  it('should check that \'Contributions by PrestaShop\' percentage is greater than 0', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkPSContributionPercent', baseContext);

    const percentage = await boWallOfFamePage.getContributionPercentage(page, 'PrestaShop');
    expect(percentage, 'PrestaShop contribution percentage should be greater than 0').to.be.above(0);
  });

  it('should check that \'Contributions by Community\' percentage is greater than 0', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkCommunityContributionPercent', baseContext);

    const percentage = await boWallOfFamePage.getContributionPercentage(page, 'Community');
    expect(percentage, 'Community contribution percentage should be greater than 0').to.be.above(0);
  });

  it('should check that the sum of all contributions equals 100%', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkTotalContributionIs100', baseContext);

    const psPercentage = await boWallOfFamePage.getContributionPercentage(page, 'PrestaShop');
    const communityPercentage = await boWallOfFamePage.getContributionPercentage(page, 'Community');
    const total = psPercentage + communityPercentage;

    expect(total, 'Total contribution should equal 100%').to.equal(100);
  });
});
