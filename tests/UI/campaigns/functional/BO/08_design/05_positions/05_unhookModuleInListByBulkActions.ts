// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import positionsPage from '@pages/BO/design/positions';

import {dataModules} from '@prestashop-core/ui-testing';
import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_design_positions_unhookModuleInListByBulkActions';

describe('BO - Design - Positions : Unhook module in list by Bulk actions', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // Pre-Test : Hook a module (Hook : "displayAdminCustomers" / Module : "")
  // @todo : https://github.com/PrestaShop/PrestaShop/issues/35612

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

  it('should go to \'Design > Positions\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToPositionsPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.designParentLink,
      dashboardPage.positionsLink,
    );
    await positionsPage.closeSfToolBar(page);

    const pageTitle = await positionsPage.getPageTitle(page);
    expect(pageTitle).to.contains(positionsPage.pageTitle);
  });

  it('should select a hook and display the selection box', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'selectHookModule', baseContext);

    const isSelectionBoxVisible = await positionsPage.selectHookModule(
      page,
      'displayAdminCustomers',
      dataModules.blockwishlist.tag,
    );
    expect(isSelectionBoxVisible).to.equal(true);

    const numSelectedHook = await positionsPage.getSelectedHookCount(page);
    expect(numSelectedHook).to.be.equal(1);
  });

  it('should unhook the selection', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'unhookSelection', baseContext);

    const textResult = await positionsPage.unhookSelection(page);
    expect(textResult).to.equal(positionsPage.messageModuleRemovedFromHook);
  });
});
