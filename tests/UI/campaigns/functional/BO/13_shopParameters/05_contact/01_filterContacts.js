require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const ContactsPage = require('@pages/BO/shopParameters/contact/index');

// Import data
const {Contacts} = require('@data/demo/contacts');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_shopParameters_contact_filterContacts';

let browserContext;
let page;
let numberOfContacts = 0;

// Init objects needed
const init = async function () {
  return {
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    contactsPage: new ContactsPage(page),
  };
};

// Filter Contacts
describe('Filter Contacts', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    this.pageObjects = await init();
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  // Login into BO and go to contact page
  loginCommon.loginBO();

  it('should go to \'Shop parameters>Contact\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToContactsPage', baseContext);

    await this.pageObjects.dashboardPage.goToSubMenu(
      this.pageObjects.dashboardPage.shopParametersParentLink,
      this.pageObjects.dashboardPage.contactLink,
    );

    await this.pageObjects.contactsPage.closeSfToolBar();

    const pageTitle = await this.pageObjects.contactsPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.contactsPage.pageTitle);
  });

  it('should reset all filters and get number of contacts in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfContacts = await this.pageObjects.contactsPage.resetAndGetNumberOfLines();
    await expect(numberOfContacts).to.be.above(0);
  });

  // 1 : Filter Contacts with all inputs and selects in grid table
  describe('Filter Contacts', async () => {
    const tests = [
      {args: {testIdentifier: 'filterId', filterBy: 'id_contact', filterValue: Contacts.webmaster.id}},
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

        await this.pageObjects.contactsPage.filterContacts(
          test.args.filterBy,
          test.args.filterValue,
        );

        const numberOfContactsAfterFilter = await this.pageObjects.contactsPage.getNumberOfElementInGrid();
        await expect(numberOfContactsAfterFilter).to.be.at.most(numberOfContacts);

        for (let i = 1; i <= numberOfContactsAfterFilter; i++) {
          const textColumn = await this.pageObjects.contactsPage.getTextColumnFromTableContacts(
            i,
            test.args.filterBy,
          );

          await expect(textColumn).to.contains(test.args.filterValue);
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);

        const numberOfContactsAfterReset = await this.pageObjects.contactsPage.resetAndGetNumberOfLines();
        await expect(numberOfContactsAfterReset).to.equal(numberOfContacts);
      });
    });
  });
});
