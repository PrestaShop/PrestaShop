require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const ContactsPage = require('@pages/BO/shopParameters/contact/index');
// Importing data
const {Contacts} = require('@data/demo/contacts');

let browser;
let page;
let numberOfContacts = 0;

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    contactsPage: new ContactsPage(page),
  };
};

// Filter Contacts
describe('Filter Contacts', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    this.pageObjects = await init();
  });
  after(async () => {
    await helper.closeBrowser(browser);
  });

  // Login into BO and go to contact page
  loginCommon.loginBO();

  it('should go to \'Shop parameters>Contact\' page', async function () {
    await this.pageObjects.boBasePage.goToSubMenu(
      this.pageObjects.boBasePage.shopParametersParentLink,
      this.pageObjects.boBasePage.contactLink,
    );
    await this.pageObjects.boBasePage.closeSfToolBar();
    const pageTitle = await this.pageObjects.contactsPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.contactsPage.pageTitle);
  });

  it('should reset all filters and get number of contacts in BO', async function () {
    numberOfContacts = await this.pageObjects.contactsPage.resetAndGetNumberOfLines();
    await expect(numberOfContacts).to.be.above(0);
  });
  // 1 : Filter Contacts with all inputs and selects in grid table
  describe('Filter Contacts', async () => {
    const tests = [
      {args: {filterBy: 'id_contact', filterValue: Contacts.webmaster.id}},
      {args: {filterBy: 'name', filterValue: Contacts.customerService.title}},
      {args: {filterBy: 'email', filterValue: Contacts.webmaster.email}},
      {args: {filterBy: 'description', filterValue: Contacts.customerService.description}},
    ];

    tests.forEach((test) => {
      it(`should filter by ${test.args.filterBy} '${test.args.filterValue}'`, async function () {
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
        const numberOfContactsAfterReset = await this.pageObjects.contactsPage.resetAndGetNumberOfLines();
        await expect(numberOfContactsAfterReset).to.equal(numberOfContacts);
      });
    });
  });
});
