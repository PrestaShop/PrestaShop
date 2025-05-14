import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boContactsPage,
  boContactsCreatePage,
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  FakerContact,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_shopParameters_contact_contacts_contactBulkActions';

// Create contacts then delete with Bulk actions
describe('BO - Shop Parameters - Contact : Bulk delete contacts', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfContacts: number = 0;

  const firstContactData: FakerContact = new FakerContact({title: 'todelete'});
  const secondContactData: FakerContact = new FakerContact({title: 'todelete'});

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

  // 1 : Create 2 contacts In BO
  describe('Create 2 contacts in BO', async () => {
    const tests = [
      {args: {contactToCreate: firstContactData}},
      {args: {contactToCreate: secondContactData}},
    ];

    tests.forEach((test, index: number) => {
      it('should go to add new contact page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToNewContactPage${index + 1}`, baseContext);

        await boContactsPage.goToAddNewContactPage(page);

        const pageTitle = await boContactsCreatePage.getPageTitle(page);
        expect(pageTitle).to.contains(boContactsCreatePage.pageTitleCreate);
      });

      it('should create contact and check result', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `CreateContact${index + 1}`, baseContext);

        const textResult = await boContactsCreatePage.createEditContact(page, test.args.contactToCreate);
        expect(textResult).to.equal(boContactsPage.successfulCreationMessage);

        const numberOfContactsAfterCreation = await boContactsPage.getNumberOfElementInGrid(page);
        expect(numberOfContactsAfterCreation).to.be.equal(numberOfContacts + index + 1);
      });
    });
  });

  // 2 : Delete Contacts created with bulk actions
  describe('Delete contacts with Bulk Actions', async () => {
    it('should filter list by title', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForBulkDelete', baseContext);

      await boContactsPage.filterContacts(page, 'name', 'todelete');

      const numberOfContactsAfterFilter = await boContactsPage.getNumberOfElementInGrid(page);
      expect(numberOfContactsAfterFilter).to.be.at.most(numberOfContacts);

      for (let i = 1; i <= numberOfContactsAfterFilter; i++) {
        const textColumn = await boContactsPage.getTextColumnFromTableContacts(page, i, 'name');
        expect(textColumn).to.contains('todelete');
      }
    });

    it('should delete contacts with Bulk Actions and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteContacts', baseContext);

      const deleteTextResult = await boContactsPage.deleteContactsBulkActions(page);
      expect(deleteTextResult).to.be.equal(boContactsPage.successfulMultiDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterDelete', baseContext);

      const numberOfContactsAfterReset = await boContactsPage.resetAndGetNumberOfLines(page);
      expect(numberOfContactsAfterReset).to.be.equal(numberOfContacts);
    });
  });
});
