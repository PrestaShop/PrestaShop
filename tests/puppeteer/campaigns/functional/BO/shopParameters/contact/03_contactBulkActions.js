require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const ContactsPage = require('@pages/BO/shopParameters/contact');
const AddContactPage = require('@pages/BO/shopParameters/contact/add');
// Importing data
const ContactFaker = require('@data/faker/contact');

let browser;
let page;
let numberOfContacts = 0;
const firstContactData = new ContactFaker({description: 'todelete'});
const secondContactData = new ContactFaker({description: 'todelete'});

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    contactsPage: new ContactsPage(page),
    addContactPage: new AddContactPage(page),
  };
};

// Create contacts then delete with Bulk actions
describe('Create contacts then delete with Bulk actions', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    this.pageObjects = await init();
  });
  after(async () => {
    await helper.closeBrowser(browser);
  });
  // Login into BO and go to contacts page
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
  // 1 : Create 2 contacts In BO
  describe('Create 2 contacts in BO', async () => {
    const tests = [
      {args: {contactToCreate: firstContactData}},
      {args: {contactToCreate: secondContactData}},
    ];

    tests.forEach((test, index) => {
      it('should go to add new contact page', async function () {
        await this.pageObjects.contactsPage.goToAddNewContactPage();
        const pageTitle = await this.pageObjects.addContactPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.addContactPage.pageTitleCreate);
      });

      it('should create contact and check result', async function () {
        const textResult = await this.pageObjects.addContactPage.createEditContact(test.args.contactToCreate);
        await expect(textResult).to.equal(this.pageObjects.contactsPage.successfulCreationMessage);
        const numberOfContactsAfterCreation = await this.pageObjects.contactsPage.getNumberOfElementInGrid();
        await expect(numberOfContactsAfterCreation).to.be.equal(numberOfContacts + index + 1);
      });
    });
  });

  // 3 : Delete Contacts created with bulk actions
  describe('Delete contacts with Bulk Actions', async () => {
    it('should filter list by description', async function () {
      await this.pageObjects.contactsPage.filterContacts(
        'description',
        'todelete',
      );
      const numberOfContactsAfterFilter = await this.pageObjects.contactsPage.getNumberOfElementInGrid();
      await expect(numberOfContactsAfterFilter).to.be.at.most(numberOfContacts);
      for (let i = 1; i <= numberOfContactsAfterFilter; i++) {
        const textColumn = await this.pageObjects.contactsPage.getTextColumnFromTableContacts(
          i,
          'description',
        );
        await expect(textColumn).to.contains('todelete');
      }
    });

    it('should delete contacts with Bulk Actions and check result', async function () {
      const deleteTextResult = await this.pageObjects.contactsPage.deleteContactsBulkActions();
      await expect(deleteTextResult).to.be.equal(this.pageObjects.contactsPage.successfulMultiDeleteMessage);
    });

    it('should reset all filters', async function () {
      const numberOfContactsAfterReset = await this.pageObjects.contactsPage.resetAndGetNumberOfLines();
      await expect(numberOfContactsAfterReset).to.be.equal(numberOfContacts);
    });
  });
});
