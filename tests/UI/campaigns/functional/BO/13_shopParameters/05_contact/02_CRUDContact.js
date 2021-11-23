require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const testContext = require('@utils/testContext');

// Import login steps
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const contactsPage = require('@pages/BO/shopParameters/contact/index');
const addContactPage = require('@pages/BO/shopParameters/contact/add');

// Import data
const ContactFaker = require('@data/faker/contact');

const baseContext = 'functional_BO_shopParameters_contact_CRUDContact';

let browserContext;
let page;

let numberOfContacts = 0;

const createContactData = new ContactFaker();
const editContactData = new ContactFaker({saveMessage: false});

// Create, Update and Delete contact in BO
describe('BO - Shop Parameters - Contact : Create, Update and Delete contact in BO', async () => {
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
    await expect(pageTitle).to.contains(contactsPage.pageTitle);
  });

  it('should reset all filters', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfContacts = await contactsPage.resetAndGetNumberOfLines(page);
    await expect(numberOfContacts).to.be.above(0);
  });

  // 1 : Create contact in BO
  describe('Create contact in BO', async () => {
    it('should go to add new contact page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewContact', baseContext);

      await contactsPage.goToAddNewContactPage(page);
      const pageTitle = await addContactPage.getPageTitle(page);
      await expect(pageTitle).to.contains(addContactPage.pageTitleCreate);
    });

    it('should create contact and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createContact', baseContext);

      const textResult = await addContactPage.createEditContact(page, createContactData);
      await expect(textResult).to.equal(contactsPage.successfulCreationMessage);

      const numberOfContactsAfterCreation = await contactsPage.getNumberOfElementInGrid(page);
      await expect(numberOfContactsAfterCreation).to.be.equal(numberOfContacts + 1);
    });
  });

  // 2 : Update contact
  describe('Update contact created', async () => {
    it('should go to \'Shop parameters>Contact\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToContactsPageForUpdate', baseContext);

      await contactsPage.goToSubMenu(
        page,
        contactsPage.shopParametersParentLink,
        contactsPage.contactLink,
      );

      const pageTitle = await contactsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(contactsPage.pageTitle);
    });

    it('should filter list by email', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForUpdate', baseContext);

      await contactsPage.resetFilter(page);

      await contactsPage.filterContacts(page, 'email', createContactData.email);

      const textEmail = await contactsPage.getTextColumnFromTableContacts(page, 1, 'email');
      await expect(textEmail).to.contains(createContactData.email);
    });

    it('should go to edit contact page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditContactPage', baseContext);

      await contactsPage.goToEditContactPage(page, 1);
      const pageTitle = await addContactPage.getPageTitle(page);
      await expect(pageTitle).to.contains(addContactPage.pageTitleEdit);
    });

    it('should update contact', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateContact', baseContext);

      const textResult = await addContactPage.createEditContact(page, editContactData);
      await expect(textResult).to.equal(contactsPage.successfulUpdateMessage);

      const numberOfContactsAfterUpdate = await contactsPage.resetAndGetNumberOfLines(page);
      await expect(numberOfContactsAfterUpdate).to.be.equal(numberOfContacts + 1);
    });
  });

  // 3 : Delete contact from BO
  describe('Delete contact', async () => {
    it('should go to \'Shop parameters>Contact\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToContactsPageForDelete', baseContext);

      await contactsPage.goToSubMenu(
        page,
        contactsPage.shopParametersParentLink,
        contactsPage.contactLink,
      );

      const pageTitle = await contactsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(contactsPage.pageTitle);
    });

    it('should filter list by email', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForDelete', baseContext);

      await contactsPage.resetFilter(page);

      await contactsPage.filterContacts(page, 'email', editContactData.email);

      const textEmail = await contactsPage.getTextColumnFromTableContacts(page, 1, 'email');
      await expect(textEmail).to.contains(editContactData.email);
    });

    it('should delete contact', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteContact', baseContext);

      const textResult = await contactsPage.deleteContact(page, 1);
      await expect(textResult).to.equal(contactsPage.successfulDeleteMessage);

      const numberOfContactsAfterDelete = await contactsPage.resetAndGetNumberOfLines(page);
      await expect(numberOfContactsAfterDelete).to.be.equal(numberOfContacts);
    });
  });
});
