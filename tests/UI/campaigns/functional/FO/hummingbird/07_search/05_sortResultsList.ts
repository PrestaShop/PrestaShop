// Import utils
import testContext from '@utils/testContext';

// Import common tests
import {enableTheme, disableTheme} from '@commonTests/BO/design/hummingbird';

import {expect} from 'chai';
import {
  type BrowserContext,
  foHummingbirdHomePage,
  foHummingbirdSearchResultsPage,
  type Page,
  utilsCore,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_FO_hummingbird_search_sortResultsList';

/*
Pre-condition:
- Install hummingbird themeProducts
Scenario:
- Go to FO
- Search Mug value and see result
- Check sort by Relevance
- Sort by name ASC/DESC
- Sort by price ASC/DESC
Post-condition:
- Uninstall hummingbird theme
*/

describe('FO - Search Page : Sort results list', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // Pre-condition : Install Hummingbird
  enableTheme('hummingbird', `${baseContext}_preTest`);

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('Sort results list', async () => {
    it('should go to FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFO', baseContext);

      await foHummingbirdHomePage.goToFo(page);

      const isHomePage = await foHummingbirdHomePage.isHomePage(page);
      expect(isHomePage).to.eq(true);
    });

    it('should put \'Mug\' in the search input and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchProduct1', baseContext);

      await foHummingbirdHomePage.searchProduct(page, 'mug');

      const pageTitle = await foHummingbirdSearchResultsPage.getPageTitle(page);
      expect(pageTitle).to.equal(foHummingbirdSearchResultsPage.pageTitle);
    });

    it('should check the search result page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'countResult', baseContext);

      const countResults = await foHummingbirdSearchResultsPage.getSearchResultsNumber(page);
      expect(countResults).to.equal(5);
    });

    it('should check that the products as sorted by relevance', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDefaultSort', baseContext);

      const isSortingLinkVisible = await foHummingbirdSearchResultsPage.getSortByValue(page);
      expect(isSortingLinkVisible).to.contain('Relevance');
    });

    const tests = [
      {
        args: {
          testIdentifier: 'sortByNameAsc',
          sortName: 'Name, A to Z',
          attribute: 'miniature__title',
          sortBy: 'product.name.asc',
          sortDirection: 'asc',
        },
      },
      {
        args: {
          testIdentifier: 'sortByNameDesc',
          sortName: 'Name, Z to A',
          attribute: 'miniature__title',
          sortBy: 'product.name.desc',
          sortDirection: 'desc',
        },
      },
      {
        args: {
          testIdentifier: 'sortByPriceAsc',
          sortName: 'Price, low to high',
          attribute: 'miniature__price',
          sortBy: 'product.price.asc',
          sortDirection: 'asc',
        },
      },
      {
        args: {
          testIdentifier: 'sortByPriceDesc',
          sortName: 'Price, high to low',
          attribute: 'miniature__price',
          sortBy: 'product.price.desc',
          sortDirection: 'desc',
        },
      },
    ];
    tests.forEach((test) => {
      it(`should sort by '${test.args.sortName}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        const nonSortedTable = await foHummingbirdSearchResultsPage.getAllProductsAttribute(page, test.args.attribute);
        await foHummingbirdSearchResultsPage.sortProductsList(page, test.args.sortBy);
        const sortedTable = await foHummingbirdSearchResultsPage.getAllProductsAttribute(page, test.args.attribute);

        const expectedResult: string[] = await utilsCore.sortArray(nonSortedTable);

        if (test.args.sortDirection === 'asc') {
          expect(sortedTable).to.deep.equal(expectedResult);
        } else {
          expect(sortedTable).to.deep.equal(expectedResult.reverse());
        }
      });
    });
  });

  // Post-condition : Uninstall Hummingbird
  disableTheme('hummingbird', `${baseContext}_postTest`);
});
