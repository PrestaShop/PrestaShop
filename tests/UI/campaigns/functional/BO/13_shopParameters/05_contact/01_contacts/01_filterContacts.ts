// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

import {expect} from 'chai';

// Import login steps
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import contactsPage from '@pages/BO/shopParameters/contact';

// Import data
import Contacts from '@data/demo/contacts';

import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_shopParameters_contact_contacts_filterContacts';

// Filter Contacts
describe('BO - Shop Parameters - Contact : Filter Contacts table', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfContacts = 0;

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

  // 1 : Filter Contacts with all inputs and selects in grid table
  describe('Filter Contacts', async () => {
    const tests = [
      {args: {testIdentifier: 'filterId', filterBy: 'id_contact', filterValue: Contacts.webmaster.id.toString()}},
      {args: {testIdentifier: 'filterName', filterBy: 'name', filterValue: Contacts.customerService.title}},
      {args: {testIdentifier: 'filterEmail', filterBy: 'email', filterValue: Contacts.webmaster.email}},
      {
        args:
          {
            testIdentifier: 'filterDescription',
            filterBy: 'description',
            filterValue: Contacts.customerService.description,
          },
      },
    ];

    tests.forEach((test) => {
      it(`should filter by ${test.args.filterBy} '${test.args.filterValue}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}`, baseContext);

        await contactsPage.filterContacts(page, test.args.filterBy, test.args.filterValue);

        const numberOfContactsAfterFilter = await contactsPage.getNumberOfElementInGrid(page);
        expect(numberOfContactsAfterFilter).to.be.at.most(numberOfContacts);

        for (let i = 1; i <= numberOfContactsAfterFilter; i++) {
          const textColumn = await contactsPage.getTextColumnFromTableContacts(page, i, test.args.filterBy);
          expect(textColumn).to.contains(test.args.filterValue);
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);

        const numberOfContactsAfterReset = await contactsPage.resetAndGetNumberOfLines(page);
        expect(numberOfContactsAfterReset).to.equal(numberOfContacts);
      });
    });
  });
});
