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

const baseContext: string = 'functional_BO_wallOfFame_howToContribute';
/*
Verify the Wall of Fame page displays contribution information.
*/
describe('BO - Wall of Fame : How to contribute', async () => {
  let browserContext: BrowserContext;
  let page: Page;

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
  it('should go to \'Wall of Fame\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToWallOfFamePage', baseContext);

    await boWallOfFamePage.goToSubMenu(page, '', boWallOfFamePage.wallOfFameLink);

    const pageTitle = await boWallOfFamePage.getPageTitle(page);
    expect(pageTitle).to.contains(boWallOfFamePage.pageTitle);
  });
  it('should check that the \'How to contribute\' section is visible', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkHowToContributeSection', baseContext);

    const isHowToContributeVisible = await boWallOfFamePage.isHowToContributeVisible(page);
    expect(isHowToContributeVisible, 'The \'How to contribute\' section is visible').to.equal(true);
  });

  it('should check that the redirection of \'Contribute\' button works', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkContributeButtonRedirection', baseContext);

    const contributionPage = await boWallOfFamePage.clickContributeLink(page);
    const contributionPageTitle = await contributionPage.title();
    expect(contributionPageTitle).to.contains(boWallOfFamePage.contributePageTitle);
    await contributionPage.close();
  });
  it('should check that the redirection of \'Join Slack\' button works', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkJoinSlackButtonRedirection', baseContext);

    const joinSlackPage = await boWallOfFamePage.clickJoinSlackLink(page);
    const joinSlackPageTitle = await joinSlackPage.title();
    expect(joinSlackPageTitle).to.contains(boWallOfFamePage.joinSlackPageTitle);
    await joinSlackPage.close();
  });
});
