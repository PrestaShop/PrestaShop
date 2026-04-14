import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boCMSPagesPage,
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  dataCMSPages,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_design_pages_pages_filterAndQuickEditPages';

/*
Filter pages table by : ID, Link, Meta title, Position and Displayed
Enable/Disable page status by quick edit
 */
describe('BO - Design - Pages : Filter and quick edit pages table', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfPages: number = 0;

  const pagesTableName: string = 'cms_page';

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

  it('should go to \'Design > Pages\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCmsPagesPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.designParentLink,
      boDashboardPage.pagesLink,
    );
    await boCMSPagesPage.closeSfToolBar(page);

    const pageTitle = await boCMSPagesPage.getPageTitle(page);
    expect(pageTitle).to.contains(boCMSPagesPage.pageTitle);
  });

  it('should reset all filters and get number of pages in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFiltersFirst', baseContext);

    numberOfPages = await boCMSPagesPage.resetAndGetNumberOfLines(page, pagesTableName);
    expect(numberOfPages).to.be.above(0);
  });

  // 1 : Filter pages with all inputs and selects in grid table
  describe('Filter pages table', async () => {
    const tests = [
      {
        args:
          {
            testIdentifier: 'filterById',
            filterType: 'input',
            filterBy: 'id_cms',
            filterValue: dataCMSPages.delivery.id.toString(),
          },
      },
      {
        args:
          {
            testIdentifier: 'filterByLink',
            filterType: 'input',
            filterBy: 'link_rewrite',
            filterValue: dataCMSPages.aboutUs.url,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterByMetaTitle',
            filterType: 'input',
            filterBy: 'meta_title',
            filterValue: dataCMSPages.termsAndCondition.title,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterByActive',
            filterType: 'select',
            filterBy: 'active',
            filterValue: dataCMSPages.securePayment.displayed ? '1' : '0',
          },
      },
    ];

    tests.forEach((test) => {
      it(`should filter by ${test.args.filterBy} '${test.args.filterValue}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        await boCMSPagesPage.filterTable(
          page,
          pagesTableName,
          test.args.filterType,
          test.args.filterBy,
          test.args.filterValue,
        );

        const numberOfPagesAfterFilter = await boCMSPagesPage.getNumberOfElementInGrid(page, pagesTableName);
        expect(numberOfPagesAfterFilter).to.be.at.most(numberOfPages);

        for (let i = 1; i <= numberOfPagesAfterFilter; i++) {
          if (test.args.filterBy === 'active') {
            const pagesStatus = await boCMSPagesPage.getStatus(page, pagesTableName, i);
            expect(pagesStatus).to.equal(test.args.filterValue === '1');
          } else {
            const textColumn = await boCMSPagesPage.getTextColumnFromTableCmsPage(page, i, test.args.filterBy);
            expect(textColumn).to.contains(test.args.filterValue);
          }
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `reset_${test.args.testIdentifier}`, baseContext);

        // Trigger reset then wait for full navigation before reading the count,
        // because resetAndGetNumberOfLines reads with waitForSelector=false which
        // can return a stale count if the page is still navigating.
        await boCMSPagesPage.resetAndGetNumberOfLines(page, pagesTableName);
        await page.waitForLoadState('networkidle');
        const numberOfPagesAfterReset = await boCMSPagesPage.getNumberOfElementInGrid(page, pagesTableName);
        expect(numberOfPagesAfterReset).to.be.equal(numberOfPages);
      });
    });
  });

  // 2 : Editing pages from grid table
  describe('Quick edit pages', async () => {
    it('should filter by Title \'Terms and conditions of use\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'quickEditFilter', baseContext);

      await boCMSPagesPage.filterTable(
        page,
        pagesTableName,
        'input',
        'meta_title',
        dataCMSPages.termsAndCondition.title,
      );

      const numberOfPagesAfterFilter = await boCMSPagesPage.getNumberOfElementInGrid(page, pagesTableName);

      if (numberOfPages === 0) {
        expect(numberOfPagesAfterFilter).to.be.equal(numberOfPages + 1);
      } else {
        expect(numberOfPagesAfterFilter).to.be.at.most(numberOfPages);
      }

      const textColumn = await boCMSPagesPage.getTextColumnFromTableCmsPage(page, 1, 'meta_title');
      expect(textColumn).to.contains(dataCMSPages.termsAndCondition.title);
    });

    [
      {args: {status: 'disable', enable: false}},
      {args: {status: 'enable', enable: true}},
    ].forEach((pageStatus) => {
      it(`should ${pageStatus.args.status} the page`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${pageStatus.args.status}Page`, baseContext);

        const isActionPerformed = await boCMSPagesPage.setStatus(page, pagesTableName, 1, pageStatus.args.enable);

        if (isActionPerformed) {
          const resultMessage = await boCMSPagesPage.getAlertSuccessBlockParagraphContent(page);
          expect(resultMessage).to.contains(boCMSPagesPage.successfulUpdateStatusMessage);
        }

        const currentStatus = await boCMSPagesPage.getStatus(page, pagesTableName, 1);
        expect(currentStatus).to.be.equal(pageStatus.args.enable);
      });
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'quickEditReset', baseContext);

      // The status toggle (setStatus) triggers a full redirect to improve/design/cms-pages/
      // which loads with the session filter still active. The grid's JS bundle may not yet
      // be loaded when the next it-block starts, so the reset button's click handler is not
      // bound. Waiting for networkidle ensures the JS is fully loaded before we attempt to
      // click the reset button.
      await page.waitForLoadState('networkidle');
      await boCMSPagesPage.resetAndGetNumberOfLines(page, pagesTableName);
      // Poll until the grid reflects the unfiltered count (reset AJAX + redirect complete).
      await page.waitForFunction(
        (expected: number) => {
          const el = document.querySelector('#cms_page_grid_panel h3.card-header-title');

          if (!el?.textContent) return false;
          const match = el.textContent.match(/\d+/);

          return match !== null && parseInt(match[0], 10) >= expected;
        },
        numberOfPages,
        {timeout: 30000},
      );
      const numberOfPagesAfterReset = await boCMSPagesPage.getNumberOfElementInGrid(page, pagesTableName);
      expect(numberOfPagesAfterReset).to.be.equal(numberOfPages);
    });
  });
});
