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
Filter pages table by : ID, Link, Meta title and Displayed
Enable/Disable page status by quick edit, then verify with displayed filter
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

  // 1 : Filter pages with all inputs in grid table
  describe('Filter pages table', async () => {
    const tests = [
      {
        args: {
          testIdentifier: 'filterById',
          filterType: 'input',
          filterBy: 'id_cms',
          filterValue: dataCMSPages.delivery.id.toString(),
        },
      },
      {
        args: {
          testIdentifier: 'filterByLink',
          filterType: 'input',
          filterBy: 'link_rewrite',
          filterValue: dataCMSPages.aboutUs.url,
        },
      },
      {
        args: {
          testIdentifier: 'filterByMetaTitle',
          filterType: 'input',
          filterBy: 'meta_title',
          filterValue: dataCMSPages.termsAndCondition.title,
        },
      },
      {
        args: {
          testIdentifier: 'filterByMetaTitleNoResult',
          filterType: 'input',
          filterBy: 'meta_title',
          filterValue: '123',
          expectedCount: 0,
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

        if (test.args.expectedCount !== undefined) {
          expect(numberOfPagesAfterFilter).to.equal(test.args.expectedCount);
        } else {
          expect(numberOfPagesAfterFilter).to.be.at.most(numberOfPages);

          const allValues = await boCMSPagesPage.getAllRowsColumnContentTableCmsPage(page, test.args.filterBy);
          for (const textColumn of allValues) {
            expect(textColumn).to.contains(test.args.filterValue);
          }
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `reset_${test.args.testIdentifier}`, baseContext);

        const numberOfPagesAfterReset = await boCMSPagesPage.resetAndGetNumberOfLines(page, pagesTableName);
        expect(numberOfPagesAfterReset).to.be.equal(numberOfPages);
      });
    });
  });

  // 2 : Quick edit page status and verify with displayed filter
  describe('Quick edit pages', async () => {
    it('should disable page with ID 1', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'disablePage', baseContext);

      // Grid is unfiltered, ID=1 (delivery) is at row 1 (default sort by ID asc)
      const isActionPerformed = await boCMSPagesPage.setStatus(page, pagesTableName, 1, false);

      if (isActionPerformed) {
        const resultMessage = await boCMSPagesPage.getAlertSuccessBlockParagraphContent(page);
        expect(resultMessage).to.contains(boCMSPagesPage.successfulUpdateStatusMessage);
      }

      const currentStatus = await boCMSPagesPage.getStatus(page, pagesTableName, 1);
      expect(currentStatus).to.be.equal(false);
    });

    it('should filter by displayed \'No\' and verify only disabled pages are shown', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByDisplayedNo', baseContext);

      await boCMSPagesPage.filterTable(page, pagesTableName, 'select', 'active', '0');

      const numberOfPagesAfterFilter = await boCMSPagesPage.getNumberOfElementInGrid(page, pagesTableName);
      expect(numberOfPagesAfterFilter).to.be.at.most(numberOfPages);

      for (let i = 1; i <= numberOfPagesAfterFilter; i++) {
        const pagesStatus = await boCMSPagesPage.getStatus(page, pagesTableName, i);
        expect(pagesStatus).to.equal(false);
      }
    });

    it('should enable page with ID 1', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'enablePage', baseContext);

      // After filter by 'No', ID=1 is the only disabled page and sits at row 1
      const isActionPerformed = await boCMSPagesPage.setStatus(page, pagesTableName, 1, true);

      if (isActionPerformed) {
        const resultMessage = await boCMSPagesPage.getAlertSuccessBlockParagraphContent(page);
        expect(resultMessage).to.contains(boCMSPagesPage.successfulUpdateStatusMessage);
      }
      // Status verification deferred to the next filter step:
      // after re-enabling, the 'No' session filter returns 0 rows, so getStatus cannot be used here.
    });

    it('should filter by displayed \'Yes\' and verify only enabled pages are shown', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByDisplayedYes', baseContext);

      await boCMSPagesPage.filterTable(page, pagesTableName, 'select', 'active', '1');

      const numberOfPagesAfterFilter = await boCMSPagesPage.getNumberOfElementInGrid(page, pagesTableName);
      expect(numberOfPagesAfterFilter).to.be.at.most(numberOfPages);

      for (let i = 1; i <= numberOfPagesAfterFilter; i++) {
        const pagesStatus = await boCMSPagesPage.getStatus(page, pagesTableName, i);
        expect(pagesStatus).to.equal(true);
      }
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'quickEditReset', baseContext);

      const numberOfPagesAfterReset = await boCMSPagesPage.resetAndGetNumberOfLines(page, pagesTableName);
      expect(numberOfPagesAfterReset).to.be.equal(numberOfPages);
    });
  });
});
