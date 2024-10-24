// Import utils
import testContext from '@utils/testContext';

import {expect} from 'chai';
import {
  type BrowserContext,
  foClassicHomePage,
  foClassicModalQuickViewPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_FO_classic_productPage_quickView_shareLinks';

/*
Scenario:
- Go to FO
- Quick view third product
- Check shared links (Facebook-Twitter-Pinterest)
 */
describe('FO - Product page - Quick view : Share links', async () => {
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

  it('should go to FO home page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToFo', baseContext);

    await foClassicHomePage.goToFo(page);

    const isHomePage = await foClassicHomePage.isHomePage(page);
    expect(isHomePage).to.eq(true);
  });

  it('should quick view the third product', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'quickView', baseContext);

    await foClassicHomePage.quickViewProduct(page, 3);

    const isModalVisible = await foClassicModalQuickViewPage.isQuickViewProductModalVisible(page);
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

      const url = await foClassicModalQuickViewPage.getSocialSharingLink(page, test.args.name);
      expect(url).to.contain(test.result.url);
    });
  });
});
