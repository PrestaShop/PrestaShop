// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import pages
import {homePage} from '@pages/FO/classic/home';
import {quickViewModal} from '@pages/FO/classic/modal/quickView';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

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
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  it('should go to FO home page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToFo', baseContext);

    await homePage.goToFo(page);

    const isHomePage = await homePage.isHomePage(page);
    expect(isHomePage).to.eq(true);
  });

  it('should quick view the third product', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'quickView', baseContext);

    await homePage.quickViewProduct(page, 3);

    const isModalVisible = await quickViewModal.isQuickViewProductModalVisible(page);
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

      const url = await quickViewModal.getSocialSharingLink(page, test.args.name);
      expect(url).to.contain(test.result.url);
    });
  });
});
