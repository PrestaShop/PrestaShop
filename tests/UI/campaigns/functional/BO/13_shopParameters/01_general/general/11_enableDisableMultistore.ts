// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import login steps
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import generalPage from '@pages/BO/shopParameters/general';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_shopParameters_general_general_enableDisableMultistore';

/*
Enable/Disable multistore
Check the existence of multistore page
 */
describe('BO - Shop Parameters - General : Enable/Disable multi store', async () => {
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

  it('should login in BO', async function () {
    await loginCommon.loginBO(this, page);
  });

  it('should go to \'Shop parameters > General\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToGeneralPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.shopParametersParentLink,
      dashboardPage.shopParametersGeneralLink,
    );
    await generalPage.closeSfToolBar(page);

    const pageTitle = await generalPage.getPageTitle(page);
    expect(pageTitle).to.contains(generalPage.pageTitle);
  });

  const tests = [
    {args: {action: 'enable', exist: true}},
    {args: {action: 'disable', exist: false}},
  ];

  tests.forEach((test, index: number) => {
    it(`should ${test.args.action} multi store`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}MultiStore`, baseContext);

      const result = await generalPage.setMultiStoreStatus(page, test.args.exist);
      expect(result).to.contains(generalPage.successfulUpdateMessage);
    });

    it('should check the existence of \'Advanced Parameters > Multistore\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goToMultiStorePage_${index}`, baseContext);

      const result = await generalPage.isSubmenuVisible(
        page,
        generalPage.advancedParametersLink,
        generalPage.multistoreLink,
      );
      expect(result).to.be.equal(test.args.exist);
    });
  });
});
