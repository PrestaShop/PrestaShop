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
const createContactData = new ContactFaker();
const editContactData = new ContactFaker({saveMessage: false});

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

// Create, Update and Delete contact in BO
describe('Create, Update and Delete contact in BO', async () => {
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

  it('should reset all filters', async function () {
    numberOfContacts = await this.pageObjects.contactsPage.resetAndGetNumberOfLines();
    await expect(numberOfContacts).to.be.above(0);
  });
  // 1 : Create contact in BO
  describe('Create contact in BO', async () => {
    it('should go to add new contact page', async function () {
      await this.pageObjects.contactsPage.goToAddNewContactPage();
      const pageTitle = await this.pageObjects.addContactPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.addContactPage.pageTitleCreate);
    });

    it('should create contact and check result', async function () {
      const textResult = await this.pageObjects.addContactPage.createEditContact(createContactData);
      await expect(textResult).to.equal(this.pageObjects.contactsPage.successfulCreationMessage);
      const numberOfContactsAfterCreation = await this.pageObjects.contactsPage.getNumberOfElementInGrid();
      await expect(numberOfContactsAfterCreation).to.be.equal(numberOfContacts + 1);
    });
  });

  // 2 : Update contact
  describe('Update contact created', async () => {
    it('should go to \'Shop parameters>Contact\' page', async function () {
      await this.pageObjects.boBasePage.goToSubMenu(
        this.pageObjects.boBasePage.shopParametersParentLink,
        this.pageObjects.boBasePage.contactLink,
      );
      await this.pageObjects.boBasePage.closeSfToolBar();
      const pageTitle = await this.pageObjects.contactsPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.contactsPage.pageTitle);
    });

    it('should filter list by email', async function () {
      await this.pageObjects.contactsPage.resetFilter();
      await this.pageObjects.contactsPage.filterContacts(
        'email',
        createContactData.email,
      );
      const textEmail = await this.pageObjects.contactsPage.getTextColumnFromTableContacts(1, 'email');
      await expect(textEmail).to.contains(createContactData.email);
    });

    it('should go to edit contact page', async function () {
      await this.pageObjects.contactsPage.goToEditContactPage(1);
      const pageTitle = await this.pageObjects.addContactPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.addContactPage.pageTitleEdit);
    });

    it('should update contact', async function () {
      const textResult = await this.pageObjects.addContactPage.createEditContact(editContactData);
      await expect(textResult).to.equal(this.pageObjects.contactsPage.successfulUpdateMessage);
      const numberOfContactsAfterUpdate = await this.pageObjects.contactsPage.resetAndGetNumberOfLines();
      await expect(numberOfContactsAfterUpdate).to.be.equal(numberOfContacts + 1);
    });
  });

  // 3 : Delete contact from BO
  describe('Delete contact', async () => {
    it('should go to \'Shop parameters>Contact\' page', async function () {
      await this.pageObjects.boBasePage.goToSubMenu(
        this.pageObjects.boBasePage.shopParametersParentLink,
        this.pageObjects.boBasePage.contactLink,
      );
      await this.pageObjects.boBasePage.closeSfToolBar();
      const pageTitle = await this.pageObjects.contactsPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.contactsPage.pageTitle);
    });

    it('should filter list by email', async function () {
      await this.pageObjects.contactsPage.resetFilter();
      await this.pageObjects.contactsPage.filterContacts(
        'email',
        editContactData.email,
      );
      const textEmail = await this.pageObjects.contactsPage.getTextColumnFromTableContacts(1, 'email');
      await expect(textEmail).to.contains(editContactData.email);
    });

    it('should delete contact', async function () {
      const textResult = await this.pageObjects.contactsPage.deleteContact(1);
      await expect(textResult).to.equal(this.pageObjects.contactsPage.successfulDeleteMessage);
      const numberOfContactsAfterDelete = await this.pageObjects.contactsPage.resetAndGetNumberOfLines();
      await expect(numberOfContactsAfterDelete).to.be.equal(numberOfContacts);
    });
  });
});
