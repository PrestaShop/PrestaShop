// Import utils
import testContext from '@utils/testContext';

// Import FO pages
import {homePage} from '@pages/FO/classic/home';
import {searchResultsPage} from '@pages/FO/classic/searchResults';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import {
  utilsCore,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_FO_classic_search_sortResultsList';

/*
Scenario:
- Go to FO
- Search Mug value and see result
- Check sort by Relevance
- Sort by name ASC/DESC
- Sort by price ASC/DESC
*/

describe('FO - Search Page : Sort results list', async () => {
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

  it('should go to FO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToFO', baseContext);

    await homePage.goToFo(page);

    const isHomePage = await homePage.isHomePage(page);
    expect(isHomePage).to.eq(true);
  });

  it('should put \'Mug\' in the search input and check result', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'searchProduct1', baseContext);

    await homePage.searchProduct(page, 'mug');

    const pageTitle = await searchResultsPage.getPageTitle(page);
    expect(pageTitle).to.equal(searchResultsPage.pageTitle);
  });

  it('should check the search result page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'countResult', baseContext);

    const countResults = await searchResultsPage.getSearchResultsNumber(page);
    expect(countResults).to.equal(5);
  });

  it('should check that the products is sorted by relevance', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkDefaultSort', baseContext);

    const isSortingLinkVisible = await searchResultsPage.getSortByValue(page);
    expect(isSortingLinkVisible).to.contain('Relevance');
  });

  const tests = [
    {
      args: {
        testIdentifier: 'sortByNameAsc',
        sortName: 'Name, A to Z',
        attribute: 'title',
        sortBy: 'product.name.asc',
        sortDirection: 'asc',
      },
    },
    {
      args: {
        testIdentifier: 'sortByNameDesc',
        sortName: 'Name, Z to A',
        attribute: 'title',
        sortBy: 'product.name.desc',
        sortDirection: 'desc',
      },
    },
    {
      args: {
        testIdentifier: 'sortByPriceAsc',
        sortName: 'Price, low to high',
        attribute: 'price-and-shipping .price',
        sortBy: 'product.price.asc',
        sortDirection: 'asc',
      },
    },
    {
      args: {
        testIdentifier: 'sortByPriceDesc',
        sortName: 'Price, high to low',
        attribute: 'price-and-shipping .price',
        sortBy: 'product.price.desc',
        sortDirection: 'desc',
      },
    },
  ];
  tests.forEach((test) => {
    it(`should sort by '${test.args.sortName}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

      const nonSortedTable = await searchResultsPage.getAllProductsAttribute(page, test.args.attribute);
      await searchResultsPage.sortProductsList(page, test.args.sortBy);
      const sortedTable = await searchResultsPage.getAllProductsAttribute(page, test.args.attribute);

      const expectedResult: string[] = await utilsCore.sortArray(nonSortedTable);

      if (test.args.sortDirection === 'asc') {
        expect(sortedTable).to.deep.equal(expectedResult);
      } else {
        expect(sortedTable).to.deep.equal(expectedResult.reverse());
      }
    });
  });
});
