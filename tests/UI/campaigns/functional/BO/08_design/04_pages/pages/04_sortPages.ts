import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boCMSPagesPage,
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  type Page,
  utilsCore,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_design_pages_pages_sortPages';

/*
Pre-condition:
- Login in BO
Scenario:
- Go to Design > Pages
- Reset all filters
- Sort pages by ID asc
- Sort pages by ID desc
- Sort pages by URL asc
- Sort pages by URL desc
- Sort pages by Title asc
- Sort pages by Title desc
- Sort pages by Meta Title asc
- Sort pages by Meta Title desc
- Sort pages by Position asc
- Sort pages by Position desc
- Sort pages by Status asc
- Sort pages by Status desc
 */
describe('BO - Design - Pages : Sort pages table', async () => {
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

  // Sort pages table
  describe('Sort pages table', async () => {
    const sortTests: {
      args: { testIdentifier: string, sortBy: string, sortDirection: string, isFloat?: boolean },
    }[] = [
      {
        args: {
          testIdentifier: 'sortByIdAsc', sortBy: 'id_cms', sortDirection: 'asc', isFloat: true,
        },
      },
      {
        args: {
          testIdentifier: 'sortByIdDesc', sortBy: 'id_cms', sortDirection: 'desc', isFloat: true,
        },
      },
      {
        args: {
          testIdentifier: 'sortByUrlAsc', sortBy: 'link_rewrite', sortDirection: 'asc',
        },
      },
      {
        args: {
          testIdentifier: 'sortByUrlDesc', sortBy: 'link_rewrite', sortDirection: 'desc',
        },
      },
      {
        args: {
          testIdentifier: 'sortByTitleAsc', sortBy: 'meta_title', sortDirection: 'asc',
        },
      },
      {
        args: {
          testIdentifier: 'sortByTitleDesc', sortBy: 'meta_title', sortDirection: 'desc',
        },
      },
      {
        args: {
          testIdentifier: 'sortByMetaTitleAsc', sortBy: 'head_seo_title', sortDirection: 'asc',
        },
      },
      {
        args: {
          testIdentifier: 'sortByMetaTitleDesc', sortBy: 'head_seo_title', sortDirection: 'desc',
        },
      },
      {
        args: {
          testIdentifier: 'sortByPositionAsc', sortBy: 'position', sortDirection: 'asc', isFloat: true,
        },
      },
      {
        args: {
          testIdentifier: 'sortByPositionDesc', sortBy: 'position', sortDirection: 'desc', isFloat: true,
        },
      },
      {
        args: {
          testIdentifier: 'sortByStatusAsc', sortBy: 'active', sortDirection: 'asc',
        },
      },
      {
        args: {
          testIdentifier: 'sortByStatusDesc', sortBy: 'active', sortDirection: 'desc',
        },
      },
    ];

    sortTests.forEach((test) => {
      it(`should sort by '${test.args.sortBy}' '${test.args.sortDirection}' and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        const nonSortedTable = await boCMSPagesPage.getAllRowsColumnContentTableCmsPage(page, test.args.sortBy);
        await boCMSPagesPage.sortTableCmsPage(page, test.args.sortBy, test.args.sortDirection);

        const sortedTable = await boCMSPagesPage.getAllRowsColumnContentTableCmsPage(page, test.args.sortBy);

        if (test.args.isFloat) {
          const nonSortedTableFloat: number[] = nonSortedTable.map((text: string): number => parseFloat(text));
          const sortedTableFloat: number[] = sortedTable.map((text: string): number => parseFloat(text));

          const expectedResult: number[] = await utilsCore.sortArrayNumber(nonSortedTableFloat);

          if (test.args.sortDirection === 'asc') {
            expect(sortedTableFloat).to.deep.equal(expectedResult);
          } else {
            expect(sortedTableFloat).to.deep.equal(expectedResult.reverse());
          }
        } else {
          const expectedResult: string[] = await utilsCore.sortArray(nonSortedTable);

          if (test.args.sortDirection === 'asc') {
            expect(sortedTable).to.deep.equal(expectedResult);
          } else {
            expect(sortedTable).to.deep.equal(expectedResult.reverse());
          }
        }
      });
    });
  });
});
