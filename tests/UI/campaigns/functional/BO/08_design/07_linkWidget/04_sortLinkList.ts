import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boDashboardPage,
  boDesignLinkListPage,
  boLoginPage,
  type BrowserContext,
  dataHooks,
  type Page,
  utilsCore,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_design_linkWidget_sortLinkList';

describe('BO - Design - Link Widget : Sort link list', async () => {
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

  it('should go to \'Design > Link Widget\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToLinkWidgetPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.designParentLink,
      boDashboardPage.linkWidgetLink,
    );
    await boDesignLinkListPage.closeSfToolBar(page);

    const pageTitle = await boDesignLinkListPage.getPageTitle(page);
    expect(pageTitle).to.contains(boDesignLinkListPage.pageTitle);
  });

  describe('Sort link list table', async () => {
    const sortTests = [
      {
        args: {
          testIdentifier: 'sortByIdDesc', sortBy: 'id_link_block', sortDirection: 'desc', isFloat: true,
        },
      },
      {
        args: {
          testIdentifier: 'sortByNameAsc', sortBy: 'block_name', sortDirection: 'asc',
        },
      },
      {
        args: {
          testIdentifier: 'sortByNameDesc', sortBy: 'block_name', sortDirection: 'desc',
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
          testIdentifier: 'sortByIdAsc', sortBy: 'id_link_block', sortDirection: 'asc', isFloat: true,
        },
      },
    ];
    sortTests.forEach((test) => {
      it(`should sort by '${test.args.sortBy}' '${test.args.sortDirection}' and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        const nonSortedTable = await boDesignLinkListPage.getAllRowsColumnContent(page,
          test.args.sortBy,
          test.args.sortBy,
          dataHooks.displayFooter.name,
        );

        await boDesignLinkListPage.sortLinkWidget(page, test.args.sortBy, test.args.sortDirection, dataHooks.displayFooter.name);

        const sortedTable = await boDesignLinkListPage.getAllRowsColumnContent(page,
          test.args.sortBy,
          test.args.sortBy,
          dataHooks.displayFooter.name,
        );

        if (test.args.isFloat) {
          const nonSortedTableFloat: number[] = nonSortedTable.map((text: string): number => parseFloat(text));
          const sortedTableFloat: number[] = sortedTable.map((text: string): number => parseFloat(text));

          const expectedResult = await utilsCore.sortArrayNumber(nonSortedTableFloat);

          if (test.args.sortDirection === 'asc') {
            expect(sortedTableFloat).to.deep.equal(expectedResult);
          } else {
            expect(sortedTableFloat).to.deep.equal(expectedResult.reverse());
          }
        } else {
          const expectedResult = await utilsCore.sortArray(nonSortedTable);

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
