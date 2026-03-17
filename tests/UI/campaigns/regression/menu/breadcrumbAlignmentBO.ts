// Import utils
import testContext from '@utils/testContext';

import {expect} from 'chai';
import {
  boDashboardPage,
  boLoginPage,
  boThemeAndLogoPage,
  type BrowserContext,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'regression_menu_breadcrumbAlignmentBO';

/**
 * @bug https://github.com/PrestaShop/PrestaShop/issues/37999
 *
 * The BO header breadcrumb renders two types of <li> items:
 *  - a plain text item (container name, no anchor)
 *  - an anchor item (tab name, with <a href="...">)
 *
 * Without explicit `align-items: center` on `ol.breadcrumb`, Bootstrap defaults
 * to `align-items: stretch`, which causes a visible vertical misalignment between
 * the two items on browsers that compute different intrinsic heights for text
 * nodes and inline elements.
 */
describe('Regression - BO Header : Breadcrumb items should be vertically aligned', async () => {
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
    await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

    await boLoginPage.goTo(page, global.BO.URL);
    await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

    const pageTitle = await boDashboardPage.getPageTitle(page);
    expect(pageTitle).to.contains(boDashboardPage.pageTitle);
  });

  it('should navigate to Design > Theme & Logo to get a two-item breadcrumb', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'navigateToDesignPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.designParentLink,
      boDashboardPage.themeAndLogoParentLink,
    );

    const pageTitle = await boThemeAndLogoPage.getPageTitle(page);
    expect(pageTitle).to.contains(boThemeAndLogoPage.pageTitle);
  });

  it('should have at least two breadcrumb items visible', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkBreadcrumbItemCount', baseContext);

    const itemCount = await page.locator('.header-toolbar nav ol.breadcrumb .breadcrumb-item').count();
    expect(itemCount).to.be.gte(2, 'Expected at least two breadcrumb items (container + tab)');
  });

  it('should have all breadcrumb items vertically centered on the same axis', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkBreadcrumbAlignment', baseContext);

    // Retrieve the vertical center (midpoint) of each breadcrumb <li> using
    // getBoundingClientRect so the check is independent of CSS class names or
    // computed style values.
    const centers: number[] = await page.evaluate((): number[] => {
      const items = document.querySelectorAll<HTMLElement>(
        '.header-toolbar nav ol.breadcrumb .breadcrumb-item',
      );

      return Array.from(items).map((item: HTMLElement) => {
        const rect = item.getBoundingClientRect();

        return rect.top + rect.height / 2;
      });
    });

    expect(centers.length).to.be.gte(2);

    // Allow a 1 px tolerance to account for sub-pixel rendering differences
    // across browsers and operating systems.
    const referenceCenter: number = centers[0];

    centers.forEach((center: number, index: number) => {
      expect(
        Math.abs(center - referenceCenter),
        `Breadcrumb item ${index} is not vertically aligned with item 0 `
        + `(expected center ~${referenceCenter}px, got ${center}px)`,
      ).to.be.lte(1);
    });
  });
});
