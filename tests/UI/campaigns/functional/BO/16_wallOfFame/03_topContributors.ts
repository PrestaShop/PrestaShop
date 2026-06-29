// Import utils
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

const baseContext: string = 'functional_BO_wallOfFame_topContributors';

/*
Pre-condition:
- Login in BO

Scenario:
- Go to Community > Wall of Fame
- Check Top Contributors card title
- Check Top Contributors card description
- Check Top Contributors table column headers
- Click action button for Progi1984 contributor
- Verify modal displays name, GitHub username and avatar
- Close the modal
- Click View all button and verify URL
*/
describe('BO - Community : Wall of Fame - Top Contributors', async () => {
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

  describe('Top Contributors card', async () => {
    it('should go to \'Community > Wall of Fame\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToWallOfFamePage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        '',
        boDashboardPage.wallOfFameLink,
      );
      await boWallOfFamePage.closeSfToolBar(page);

      const pageTitle = await boWallOfFamePage.getPageTitle(page);
      expect(pageTitle).to.contains(boWallOfFamePage.pageTitle);
    });

    it('should check the Top Contributors card title', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkTopContributorsTitle', baseContext);

      const cardTitle = await boWallOfFamePage.getTopContributorsCardTitle(page);
      expect(cardTitle).to.contains('Top contributors');
    });

    it('should check the Top Contributors card description', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkTopContributorsDescription', baseContext);

      const description = await boWallOfFamePage.getTopContributorsDescription(page);
      expect(description).to.equal('These experts spent hours improving PrestaShop\'s quality.');
    });

    it('should check the Top Contributors table column headers', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkTopContributorsTableHeaders', baseContext);

      const headers = await boWallOfFamePage.getTopContributorsTableColumnHeaders(page);
      expect(headers).to.include.members(['Rank', 'Avatar', 'Name', 'Contributions']);
    });

    it('should click the action button for Progi1984 and check the modal is displayed', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickProgi1984ActionButton', baseContext);

      await boWallOfFamePage.clickContributorActionButton(page, 'Progi1984');

      const name = await boWallOfFamePage.getContributorModalName(page);
      expect(name, 'Modal should display the contributor name').to.contains('Progi1984');
    });

    it('should check the GitHub username is displayed in the modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkContributorModalUsername', baseContext);

      const username = await boWallOfFamePage.getContributorModalGitHubUsername(page);
      expect(username, 'Modal should display the GitHub username').to.contains('lefevre.dev');
    });

    it('should check the avatar is visible in the modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkContributorModalAvatar', baseContext);

      const isVisible = await boWallOfFamePage.isContributorModalAvatarVisible(page);
      expect(isVisible, 'Modal avatar should be visible').to.eq(true);
    });

    it('should close the modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeContributorModal', baseContext);

      await boWallOfFamePage.closeContributorModal(page);

      const pageTitle = await boWallOfFamePage.getPageTitle(page);
      expect(pageTitle).to.contains(boWallOfFamePage.pageTitle);
    });

    it('should check the View all button URL', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkViewAllContributorsUrl', baseContext);

      const url = await boWallOfFamePage.getViewAllContributorsButtonUrl(page);
      expect(url, 'View all button should link to the contributors page').to.contains('contributors.prestashop-project.org');
    });
  });
});
