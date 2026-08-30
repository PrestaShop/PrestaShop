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

const baseContext: string = 'functional_BO_wallOfFame_newContributors';

/*
Pre-condition:
- Login in BO

Scenario:
- Go to Community > Wall of Fame
- Check New Contributors section title and description
- Check 6 contributors are displayed with avatar, name, GitHub username and contributions
- Navigate → 4 times: each click shows 1 new contributor, always 6 visible
- Check → button is disabled at the end of the list
- Navigate ← 4 times: each click shows 1 new contributor, always 6 visible
- Check ← button is disabled at the start of the list
*/
describe('BO - Community : Wall of Fame - New Contributors', async () => {
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

  describe('New Contributors section', async () => {
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

    it('should check the New Contributors section title', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNewContributorsTitle', baseContext);

      const title = await boWallOfFamePage.getNewContributorsSectionTitle(page);
      expect(title).to.contains('Say hello to our new contributors');
    });

    it('should check the New Contributors section description', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNewContributorsDescription', baseContext);

      const description = await boWallOfFamePage.getNewContributorsSectionDescription(page);
      expect(description).to.equal('Fresh commits, fresh faces. Meet the contributors who just joined!');
    });

    it('should check 5 contributors are displayed', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkInitialContributorsCount', baseContext);

      const count = await boWallOfFamePage.getVisibleNewContributorsCount(page);
      expect(count).to.equal(5);
    });

    it('should check the first contributor has an avatar visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkContributorAvatar', baseContext);

      const isVisible = await boWallOfFamePage.isFirstNewContributorAvatarVisible(page);
      expect(isVisible, 'Contributor avatar should be visible').to.eq(true);
    });

    [1, 2, 3, 4, 5].forEach((index: number) => {
      it(`should click the → button (${index}/5) and check 1 new contributor appeared`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `clickNextButton${index}`, baseContext);

        const namesBefore = await boWallOfFamePage.getVisibleNewContributorNames(page);
        await boWallOfFamePage.clickNextNewContributorButton(page);
        const namesAfter = await boWallOfFamePage.getVisibleNewContributorNames(page);

        const newNames = namesAfter.filter((n: string) => !namesBefore.includes(n));
        expect(newNames, '1 new contributor should have appeared').to.have.lengthOf(1);
      });
    });

    [1, 2, 3, 4, 5].forEach((index: number) => {
      it(`should click the ← button (${index}/5) and check 1 new contributor appeared`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `clickPreviousButton${index}`, baseContext);

        const namesBefore = await boWallOfFamePage.getVisibleNewContributorNames(page);
        await boWallOfFamePage.clickPreviousNewContributorButton(page);
        const namesAfter = await boWallOfFamePage.getVisibleNewContributorNames(page);

        const newNames = namesAfter.filter((n: string) => !namesBefore.includes(n));
        expect(newNames, '1 new contributor should have appeared').to.have.lengthOf(1);
      });
    });

    it('should check the ← button is disabled at the start of the list', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPreviousButtonDisabled', baseContext);

      const isDisabled = await boWallOfFamePage.isPreviousNewContributorButtonDisabled(page);
      expect(isDisabled, '← button should be disabled at the start of the list').to.eq(true);
    });

    it('should navigate to the end of the list', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'navigateToEnd', baseContext);

      let isDisabled = await boWallOfFamePage.isNextNewContributorButtonDisabled(page);

      for (let i = 0; i < 20 && !isDisabled; i++) {
        await boWallOfFamePage.clickNextNewContributorButton(page);
        isDisabled = await boWallOfFamePage.isNextNewContributorButtonDisabled(page);
      }
    });

    it('should check the → button is disabled at the end of the list', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNextButtonDisabled', baseContext);

      const isDisabled = await boWallOfFamePage.isNextNewContributorButtonDisabled(page);
      expect(isDisabled, '→ button should be disabled at the end of the list').to.eq(true);
    });
  });
});
