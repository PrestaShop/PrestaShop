// Import utils
import basicHelper from '@utils/basicHelper';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import contactsPage from '@pages/BO/shopParameters/contact';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_shopParameters_contact_contacts_sortAndPagination';

// Sort contacts by id, name, email and description
describe('BO - Shop Parameters - Contact : Sort Contacts list', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfContacts: number = 0;

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

  it('should go to \'Shop parameters > Contact\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToContactsPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.shopParametersParentLink,
      dashboardPage.contactLink,
    );
    await contactsPage.closeSfToolBar(page);

    const pageTitle = await contactsPage.getPageTitle(page);
    expect(pageTitle).to.contains(contactsPage.pageTitle);
  });

  it('should reset all filters and get number of contacts in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfContacts = await contactsPage.resetAndGetNumberOfLines(page);
    expect(numberOfContacts).to.be.above(0);
  });

  // Start sorting contacts
  const tests = [
    {
      args:
        {
          testIdentifier: 'sortByIdDesc', sortBy: 'id_contact', sortDirection: 'desc', isFloat: true,
        },
    },
    {args: {testIdentifier: 'sortByNameAsc', sortBy: 'name', sortDirection: 'asc'}},
    {args: {testIdentifier: 'sortByNameDesc', sortBy: 'name', sortDirection: 'desc'}},
    {args: {testIdentifier: 'sortByEmailAsc', sortBy: 'email', sortDirection: 'asc'}},
    {args: {testIdentifier: 'sortByEmailDesc', sortBy: 'email', sortDirection: 'desc'}},
    {args: {testIdentifier: 'sortByDescriptionAsc', sortBy: 'description', sortDirection: 'asc'}},
    {args: {testIdentifier: 'sortByDescriptionDesc', sortBy: 'description', sortDirection: 'desc'}},
    {
      args:
        {
          testIdentifier: 'sortByIdAsc', sortBy: 'id_contact', sortDirection: 'asc', isFloat: true,
        },
    },
  ];

  tests.forEach((test) => {
    it(`should sort by '${test.args.sortBy}' '${test.args.sortDirection}' And check result`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

      const nonSortedTable = await contactsPage.getAllRowsColumnContent(page, test.args.sortBy);

      await contactsPage.sortTable(page, test.args.sortBy, test.args.sortDirection);

      const sortedTable = await contactsPage.getAllRowsColumnContent(page, test.args.sortBy);

      if (test.args.isFloat) {
        const nonSortedTableFloat = nonSortedTable.map((text: string): number => parseFloat(text));
        const sortedTableFloat = sortedTable.map((text: string): number => parseFloat(text));

        const expectedResult = await basicHelper.sortArrayNumber(nonSortedTableFloat);

        if (test.args.sortDirection === 'asc') {
          expect(sortedTableFloat).to.deep.equal(expectedResult);
        } else {
          expect(sortedTableFloat).to.deep.equal(expectedResult.reverse());
        }
      } else {
        const expectedResult = await basicHelper.sortArray(nonSortedTable);

        if (test.args.sortDirection === 'asc') {
          expect(sortedTable).to.deep.equal(expectedResult);
        } else {
          expect(sortedTable).to.deep.equal(expectedResult.reverse());
        }
      }
    });
  });
});
