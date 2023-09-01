// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import contactsPage from '@pages/BO/shopParameters/contact';
import addContactPage from '@pages/BO/shopParameters/contact/add';

// Import data
import ContactData from '@data/faker/contact';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_shopParameters_contact_contacts_contactBulkActions';

// Create contacts then delete with Bulk actions
describe('BO - Shop Parameters - Contact : Bulk delete contacts', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfContacts: number = 0;

  const firstContactData: ContactData = new ContactData({title: 'todelete'});
  const secondContactData: ContactData = new ContactData({title: 'todelete'});

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

  it('should reset all filters and get number of contacts in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfContacts = await contactsPage.resetAndGetNumberOfLines(page);
    await expect(numberOfContacts).to.be.above(0);
  });

  // 1 : Create 2 contacts In BO
  describe('Create 2 contacts in BO', async () => {
    const tests = [
      {args: {contactToCreate: firstContactData}},
      {args: {contactToCreate: secondContactData}},
    ];

    tests.forEach((test, index: number) => {
      it('should go to add new contact page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToNewContactPage${index + 1}`, baseContext);

        await contactsPage.goToAddNewContactPage(page);

        const pageTitle = await addContactPage.getPageTitle(page);
        await expect(pageTitle).to.contains(addContactPage.pageTitleCreate);
      });

      it('should create contact and check result', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `CreateContact${index + 1}`, baseContext);

        const textResult = await addContactPage.createEditContact(page, test.args.contactToCreate);
        await expect(textResult).to.equal(contactsPage.successfulCreationMessage);

        const numberOfContactsAfterCreation = await contactsPage.getNumberOfElementInGrid(page);
        await expect(numberOfContactsAfterCreation).to.be.equal(numberOfContacts + index + 1);
      });
    });
  });

  // 2 : Delete Contacts created with bulk actions
  describe('Delete contacts with Bulk Actions', async () => {
    it('should filter list by title', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForBulkDelete', baseContext);

      await contactsPage.filterContacts(page, 'name', 'todelete');

      const numberOfContactsAfterFilter = await contactsPage.getNumberOfElementInGrid(page);
      await expect(numberOfContactsAfterFilter).to.be.at.most(numberOfContacts);

      for (let i = 1; i <= numberOfContactsAfterFilter; i++) {
        const textColumn = await contactsPage.getTextColumnFromTableContacts(page, i, 'name');
        await expect(textColumn).to.contains('todelete');
      }
    });

    it('should delete contacts with Bulk Actions and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteContacts', baseContext);

      const deleteTextResult = await contactsPage.deleteContactsBulkActions(page);
      await expect(deleteTextResult).to.be.equal(contactsPage.successfulMultiDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterDelete', baseContext);

      const numberOfContactsAfterReset = await contactsPage.resetAndGetNumberOfLines(page);
      await expect(numberOfContactsAfterReset).to.be.equal(numberOfContacts);
    });
  });
});
