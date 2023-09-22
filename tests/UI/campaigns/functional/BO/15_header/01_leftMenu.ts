// Import utils
import helper from '@utils/helpers';
import loginCommon from '@commonTests/BO/loginBO';
import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

// Import test context
import testContext from '@utils/testContext';

// Import pages
import dashboardPage from '@pages/BO/dashboard';

const baseContext: string = 'functional_BO_header_leftMenu';

describe('BO - Header : Left menu', async () => {
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

  dashboardPage.menuTree.forEach((test, index: number) => {
    test.children.forEach((subTest: string, subIndex: number) => {
      it(`should check that the child menu '${subTest}' is displayed and works`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkChildrenClick${index}_${subIndex}`, baseContext);

        await dashboardPage.clickSubMenu(page, test.parent);

        const isVisible = await dashboardPage.isSubmenuVisible(page, test.parent, subTest);
        expect(isVisible).to.eq(true);

        await dashboardPage.goToSubMenu(page, test.parent, subTest);

        const isMenuActive = await dashboardPage.isSubMenuActive(page, subTest);
        expect(isMenuActive).to.eq(true);
      });
    });
  });

  it('should close the menu', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'closeMenu', baseContext);

    await dashboardPage.setSidebarCollapsed(page, true);

    const isSidebarCollapsed = await dashboardPage.isSidebarCollapsed(page);
    expect(isSidebarCollapsed).to.eq(true);
  });

  dashboardPage.menuTree.forEach((test, index) => {
    test.children.forEach((subTest: string, subIndex: number) => {
      it(`should check that the child menu '${subTest}' is displayed and works`, async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `checkMenuCollapsedChildrenClick${index}_${subIndex}`,
          baseContext,
        );

        await dashboardPage.clickSubMenu(page, test.parent);

        const isVisible = await dashboardPage.isSubmenuVisible(page, test.parent, subTest);
        expect(isVisible).to.eq(true);

        await dashboardPage.goToSubMenu(page, test.parent, subTest);

        const isMenuActive = await dashboardPage.isSubMenuActive(page, subTest);
        expect(isMenuActive).to.eq(true);
      });
    });
  });

  it('should open the menu', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'openMenu', baseContext);

    await dashboardPage.setSidebarCollapsed(page, false);

    const isSidebarCollapsed = await dashboardPage.isSidebarCollapsed(page);
    expect(isSidebarCollapsed).to.eq(false);
  });

  it('should check the menu in mobile context', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkMenuMobileContext', baseContext);

    await dashboardPage.resize(page, true);

    const isMobileMenuVisible = await dashboardPage.isMobileMenuVisible(page);
    expect(isMobileMenuVisible).to.eq(true);

    const isNavbarVisible = await dashboardPage.isNavbarVisible(page);
    expect(isNavbarVisible).to.eq(false);
  });

  it('should check the menu in desktop context', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkMenuDesktopContext', baseContext);

    await dashboardPage.resize(page, false);

    const isMobileMenuVisible = await dashboardPage.isMobileMenuVisible(page);
    expect(isMobileMenuVisible).to.eq(false);

    const isNavbarVisible = await dashboardPage.isNavbarVisible(page);
    expect(isNavbarVisible).to.eq(true);
  });
});
