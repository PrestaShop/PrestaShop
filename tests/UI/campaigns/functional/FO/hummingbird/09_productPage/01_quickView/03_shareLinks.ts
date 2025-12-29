// Import utils
import testContext from '@utils/testContext';

// Import common tests
import {enableTheme, disableTheme} from '@commonTests/BO/design/hummingbird';

import {expect} from 'chai';
import {
  type BrowserContext,
  foHummingbirdHomePage,
  foHummingbirdModalQuickViewPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_FO_hummingbird_productPage_quickView_shareLinks';

/*
Pre-condition:
- Install hummingbird theme
Scenario:
- Go to FO
- Quick view third product
- Check shared links (Facebook-Twitter-Pinterest)
Post-condition:
- Uninstall hummingbird theme
 */
describe('FO - Product page - Quick view : Share links', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // Pre-condition : Install Hummingbird
  enableTheme('hummingbird', `${baseContext}_preTest`);

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('Check share links', async () => {
    it('should go to FO home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFo', baseContext);

      await foHummingbirdHomePage.goToFo(page);

      const isHomePage = await foHummingbirdHomePage.isHomePage(page);
      expect(isHomePage).to.eq(true);
    });

    it('should quick view the third product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'quickView', baseContext);

      await foHummingbirdHomePage.quickViewProduct(page, 3);

      const isModalVisible = await foHummingbirdModalQuickViewPage.isQuickViewProductModalVisible(page);
      expect(isModalVisible).to.eq(true);
    });

    const tests = [
      {
        args:
          {
            name: 'Facebook',
          },
        result:
          {
            url: 'https://www.facebook.com/',
          },
      },
      {
        args:
          {
            name: 'Twitter',
          },
        result:
          {
            url: 'https://twitter.com/',
          },
      },
      {
        args:
          {
            name: 'Pinterest',
          },
        result:
          {
            url: 'https://www.pinterest.com/',
          },
      },
    ];

    tests.forEach((test, index: number) => {
      it(`should check the share link '${test.args.name}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkLink${index}`, baseContext);

        const url = await foHummingbirdModalQuickViewPage.getSocialSharingLink(page, test.args.name);
        expect(url).to.contain(test.result.url);
      });
    });
  });

  // Post-condition : Uninstall Hummingbird
  disableTheme('hummingbird', `${baseContext}_postTest`);
});
