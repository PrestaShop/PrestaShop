/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const ContactsPage = require('@pages/BO/shopParameters/contact/index');
const AddContactPage = require('@pages/BO/shopParameters/contact/add');

// Import data
const ContactFaker = require('@data/faker/contact');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_shopParameters_contact_contactBulkActions';

let browserContext;
let page;

let numberOfContacts = 0;

const firstContactData = new ContactFaker({title: 'todelete'});
const secondContactData = new ContactFaker({title: 'todelete'});

// Init objects needed
const init = async function () {
  return {
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
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    this.pageObjects = await init();
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  // Login into BO and go to contacts page
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

  // 1 : Create 2 contacts In BO
  describe('Create 2 contacts in BO', async () => {
    const tests = [
      {args: {contactToCreate: firstContactData}},
      {args: {contactToCreate: secondContactData}},
    ];

    tests.forEach((test, index) => {
      it('should go to add new contact page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToNewContactPage${index + 1}`, baseContext);

        await this.pageObjects.contactsPage.goToAddNewContactPage();
        const pageTitle = await this.pageObjects.addContactPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.addContactPage.pageTitleCreate);
      });

      it('should create contact and check result', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `CreateContact${index + 1}`, baseContext);

        const textResult = await this.pageObjects.addContactPage.createEditContact(test.args.contactToCreate);
        await expect(textResult).to.equal(this.pageObjects.contactsPage.successfulCreationMessage);

        const numberOfContactsAfterCreation = await this.pageObjects.contactsPage.getNumberOfElementInGrid();
        await expect(numberOfContactsAfterCreation).to.be.equal(numberOfContacts + index + 1);
      });
    });
  });

  // 2 : Delete Contacts created with bulk actions
  describe('Delete contacts with Bulk Actions', async () => {
    it('should filter list by title', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForBulkDelete', baseContext);

      await this.pageObjects.contactsPage.filterContacts(
        'name',
        'todelete',
      );

      const numberOfContactsAfterFilter = await this.pageObjects.contactsPage.getNumberOfElementInGrid();
      await expect(numberOfContactsAfterFilter).to.be.at.most(numberOfContacts);

      for (let i = 1; i <= numberOfContactsAfterFilter; i++) {
        const textColumn = await this.pageObjects.contactsPage.getTextColumnFromTableContacts(
          i,
          'name',
        );

        await expect(textColumn).to.contains('todelete');
      }
    });

    it('should delete contacts with Bulk Actions and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteContacts', baseContext);

      const deleteTextResult = await this.pageObjects.contactsPage.deleteContactsBulkActions();
      await expect(deleteTextResult).to.be.equal(this.pageObjects.contactsPage.successfulMultiDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterDelete', baseContext);

      const numberOfContactsAfterReset = await this.pageObjects.contactsPage.resetAndGetNumberOfLines();
      await expect(numberOfContactsAfterReset).to.be.equal(numberOfContacts);
    });
  });
});
