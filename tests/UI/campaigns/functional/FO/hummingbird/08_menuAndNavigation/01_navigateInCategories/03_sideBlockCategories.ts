import {expect} from 'chai';
import testContext from '@utils/testContext';
import {enableHummingbird, disableHummingbird} from '@commonTests/BO/design/hummingbird';

import {
  type BrowserContext,
  dataCategories,
  FakerCategory,
  foHummingbirdCategoryPage,
  foHummingbirdHomePage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_FO_hummingbird_menuAndNavigation_navigateInCategories_sideBlockCategories';

describe('FO - Menu and Navigation : Side block categories', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // Pre-condition : Install Hummingbird
  enableHummingbird(`${baseContext}_preTest`);

  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('Check Side block categories', async () => {
    it('should go to FO home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFO', baseContext);

      await foHummingbirdHomePage.goToFo(page);

      const isHomePage = await foHummingbirdHomePage.isHomePage(page);
      expect(isHomePage).to.eq(true);
    });

    [
      {
        parent: dataCategories.accessories,
        child: dataCategories.stationery,
      },
      {
        parent: dataCategories.clothes,
        child: dataCategories.women,
      },
      {
        parent: dataCategories.art,
      },
    ].forEach((arg: { parent: FakerCategory, child?: FakerCategory }, index: number) => {
      it(`should click on category '${arg.parent.name}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToCategory${index}`, baseContext);

        await foHummingbirdHomePage.goToCategory(page, arg.parent.id);

        const pageTitle = await foHummingbirdHomePage.getPageTitle(page);
        expect(pageTitle).to.equal(arg.parent.name);
      });

      if (arg.parent !== dataCategories.art) {
        it(`should check category block '${arg.parent.name}'`, async function () {
          await testContext.addContextItem(this, 'testIdentifier', `checkCategory${index}`, baseContext);

          const hasBlockCategories = await foHummingbirdCategoryPage.hasBlockCategories(page);
          expect(hasBlockCategories).to.equal(true);

          const numBlockCategories = await foHummingbirdCategoryPage.getNumBlockCategories(page, 0);
          expect(numBlockCategories).to.be.equal(dataCategories.home.children.length);
        });
      }

      if (arg.child) {
        it(`should click on category '${arg.child.name}' in sideBlock`, async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToSideBlock${index}`, baseContext);

          await foHummingbirdCategoryPage.clickBlockCategory(page, arg.child!.name, arg.parent.name);

          const pageTitle = await foHummingbirdHomePage.getPageTitle(page);
          expect(pageTitle).to.equal(arg.child!.name);
        });

        it(`should check category block '${arg.child.name}'`, async function () {
          await testContext.addContextItem(this, 'testIdentifier', `checkSubCategory${index}`, baseContext);

          const hasBlockCategories = await foHummingbirdCategoryPage.hasBlockCategories(page);
          expect(hasBlockCategories).to.be.equal(true);

          const numBlockCategories = await foHummingbirdCategoryPage.getNumBlockCategories(page, 0);
          expect(numBlockCategories).to.be.equal(dataCategories.home.children.length);
        });
      }
    });
  });

  // Post-condition : Uninstall Hummingbird
  disableHummingbird(`${baseContext}_postTest`);
});
