import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boContactsPage,
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  dataContacts,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_shopParameters_contact_contacts_filterContacts';

// Filter Contacts
describe('BO - Shop Parameters - Contact : Filter Contacts table', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfContacts = 0;

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

  it('should go to \'Shop parameters > Contact\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToContactsPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.shopParametersParentLink,
      boDashboardPage.contactLink,
    );
    await boContactsPage.closeSfToolBar(page);

    const pageTitle = await boContactsPage.getPageTitle(page);
    expect(pageTitle).to.contains(boContactsPage.pageTitle);
  });

  it('should reset all filters and get number of contacts in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfContacts = await boContactsPage.resetAndGetNumberOfLines(page);
    expect(numberOfContacts).to.be.above(0);
  });

  // 1 : Filter Contacts with all inputs and selects in grid table
  describe('Filter Contacts', async () => {
    const tests = [
      {args: {testIdentifier: 'filterId', filterBy: 'id_contact', filterValue: dataContacts.webmaster.id.toString()}},
      {args: {testIdentifier: 'filterName', filterBy: 'name', filterValue: dataContacts.customerService.title}},
      {args: {testIdentifier: 'filterEmail', filterBy: 'email', filterValue: dataContacts.webmaster.email}},
      {
        args:
          {
            testIdentifier: 'filterDescription',
            filterBy: 'description',
            filterValue: dataContacts.customerService.description,
          },
      },
    ];

    tests.forEach((test) => {
      it(`should filter by ${test.args.filterBy} '${test.args.filterValue}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}`, baseContext);

        await boContactsPage.filterContacts(page, test.args.filterBy, test.args.filterValue);

        const numberOfContactsAfterFilter = await boContactsPage.getNumberOfElementInGrid(page);
        expect(numberOfContactsAfterFilter).to.be.at.most(numberOfContacts);

        for (let i = 1; i <= numberOfContactsAfterFilter; i++) {
          const textColumn = await boContactsPage.getTextColumnFromTableContacts(page, i, test.args.filterBy);
          expect(textColumn).to.contains(test.args.filterValue);
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);

        const numberOfContactsAfterReset = await boContactsPage.resetAndGetNumberOfLines(page);
        expect(numberOfContactsAfterReset).to.equal(numberOfContacts);
      });
    });
  });
});
