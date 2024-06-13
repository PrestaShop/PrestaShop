// Import utils
import testContext from '@utils/testContext';

import loginCommon from '@commonTests/BO/loginBO';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import {
  boDashboardPage,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_header_leftMenu';

describe('BO - Header : Left menu', async () => {
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
    await loginCommon.loginBO(this, page);
  });

  boDashboardPage.menuTree.forEach((test, index: number) => {
    test.children.forEach((subTest: string, subIndex: number) => {
      it(`should check that the child menu '${subTest}' is displayed and works`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkChildrenClick${index}_${subIndex}`, baseContext);

        await boDashboardPage.clickSubMenu(page, test.parent);

        const isVisible = await boDashboardPage.isSubmenuVisible(page, test.parent, subTest);
        expect(isVisible).to.eq(true);

        await boDashboardPage.goToSubMenu(page, test.parent, subTest);

        const isMenuActive = await boDashboardPage.isSubMenuActive(page, subTest);
        expect(isMenuActive).to.eq(true);
      });
    });
  });

  it('should close the menu', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'closeMenu', baseContext);

    await boDashboardPage.setSidebarCollapsed(page, true);

    const isSidebarCollapsed = await boDashboardPage.isSidebarCollapsed(page);
    expect(isSidebarCollapsed).to.eq(true);
  });

  boDashboardPage.menuTree.forEach((test, index) => {
    test.children.forEach((subTest: string, subIndex: number) => {
      it(`should check that the child menu '${subTest}' is displayed and works`, async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `checkMenuCollapsedChildrenClick${index}_${subIndex}`,
          baseContext,
        );

        await boDashboardPage.clickSubMenu(page, test.parent);

        const isVisible = await boDashboardPage.isSubmenuVisible(page, test.parent, subTest);
        expect(isVisible).to.eq(true);

        await boDashboardPage.goToSubMenu(page, test.parent, subTest);

        const isMenuActive = await boDashboardPage.isSubMenuActive(page, subTest);
        expect(isMenuActive).to.eq(true);
      });
    });
  });

  it('should open the menu', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'openMenu', baseContext);

    await boDashboardPage.setSidebarCollapsed(page, false);

    const isSidebarCollapsed = await boDashboardPage.isSidebarCollapsed(page);
    expect(isSidebarCollapsed).to.eq(false);
  });

  it('should check the menu in mobile context', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkMenuMobileContext', baseContext);

    await boDashboardPage.resize(page, true);

    const isMobileMenuVisible = await boDashboardPage.isMobileMenuVisible(page);
    expect(isMobileMenuVisible).to.eq(true);

    const isNavbarVisible = await boDashboardPage.isNavbarVisible(page);
    expect(isNavbarVisible).to.eq(false);
  });

  it('should check the menu in desktop context', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkMenuDesktopContext', baseContext);

    await boDashboardPage.resize(page, false);

    const isMobileMenuVisible = await boDashboardPage.isMobileMenuVisible(page);
    expect(isMobileMenuVisible).to.eq(false);

    const isNavbarVisible = await boDashboardPage.isNavbarVisible(page);
    expect(isNavbarVisible).to.eq(true);
  });
});
