// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import {createOrderByGuestTest} from '@commonTests/FO/classic/order';
import {deleteCustomerTest} from '@commonTests/BO/customers/customer';
import {setupSmtpConfigTest, resetSmtpConfigTest} from '@commonTests/BO/advancedParameters/smtp';

import {
  boCustomersPage,
  boCustomersViewPage,
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  dataPaymentMethods,
  dataProducts,
  FakerAddress,
  FakerCustomer,
  FakerOrder,
  type MailDev,
  type MailDevEmail,
  type Page,
  utilsMail,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';

const baseContext = 'functional_BO_customers_customers_transformGuestToCustomer';

describe('BO - Customers _ Customers : Transform guest to customer account', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfCustomers: number;
  let newMail: MailDevEmail;
  let mailListener: MailDev;

  const customerData: FakerCustomer = new FakerCustomer({password: ''});
  const addressData: FakerAddress = new FakerAddress({country: 'France'});

  const orderData: FakerOrder = new FakerOrder({
    customer: customerData,
    products: [
      {
        product: dataProducts.demo_1,
        quantity: 1,
      },
    ],
    deliveryAddress: addressData,
    paymentMethod: dataPaymentMethods.wirePayment,
  });

  // Pre-Condition: Setup config SMTP
  setupSmtpConfigTest(`${baseContext}_preTest_1`);

  // Pre-condition: Create order in FO by guest
  createOrderByGuestTest(orderData, `${baseContext}_preTest_2`);

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);

    // Start listening to maildev server
    mailListener = utilsMail.createMailListener();
    utilsMail.startListener(mailListener);

    // Handle every new email
    mailListener.on('new', (email: MailDevEmail) => {
      newMail = email;
    });
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);

    // Stop listening to maildev server
    utilsMail.stopListener(mailListener);
  });

  describe('Transform a guest to customer account', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Customers > Customers\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCustomersPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.customersParentLink,
        boDashboardPage.customersLink,
      );
      await boCustomersPage.closeSfToolBar(page);

      const pageTitle = await boCustomersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCustomersPage.pageTitle);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAllFilter', baseContext);

      numberOfCustomers = await boCustomersPage.resetAndGetNumberOfLines(page);
      expect(numberOfCustomers).to.be.above(1);
    });

    it('should filter customers group by guest', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterCustomer', baseContext);

      await boCustomersPage.resetFilter(page);
      await boCustomersPage.filterCustomers(page, 'input', 'default_group', 'Guest');

      const textEmail = await boCustomersPage.getTextColumnFromTableCustomers(page, 1, 'default_group');
      expect(textEmail).to.eq('Guest');
    });

    it('should go to view customer page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToViewPage', baseContext);

      await boCustomersPage.goToViewCustomerPage(page, 1);

      const pageTitle = await boCustomersViewPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCustomersViewPage.pageTitle(`${customerData.firstName[0]}. ${customerData.lastName}`));
    });

    it('should click on transform to customer account', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnTransferToCustomerAccount', baseContext);

      const successMessage = await boCustomersViewPage.clickOnTransformToCustomerAccount(page);
      expect(successMessage).to.contains(boCustomersViewPage.successfulCreationMessage);
    });

    it('should check if the mail is in mailbox and check the subject', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkMailIsInMailbox', baseContext);

      expect(newMail.subject).to.contains(`[${global.INSTALL.SHOP_NAME}] Your guest account was converted to a customer account`);
    });

    it('should check the transform to customer account button is not visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'isButtonVisible', baseContext);

      const isButtonVisible = await boCustomersViewPage.isTransformToCustomerAccountButtonVisible(page);
      expect(isButtonVisible).to.eq(false);
    });

    it('should go back to Customers page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToCustomersPage', baseContext);

      await boDashboardPage.goToSubMenu(page, boDashboardPage.customersParentLink, boDashboardPage.customersLink);

      const pageTitle = await boCustomersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCustomersPage.pageTitle);
    });

    it('should check that the customers table is empty', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNoRecordFound', baseContext);

      const noRecordsFoundText = await boCustomersPage.getTextWhenTableIsEmpty(page);
      expect(noRecordsFoundText).to.contains('No records found');
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilter', baseContext);

      await boCustomersPage.resetFilter(page);

      const numberOfCustomers = await boCustomersPage.resetAndGetNumberOfLines(page);
      expect(numberOfCustomers).to.be.at.least(0);
    });
  });

  // Post-condition: Delete customers
  deleteCustomerTest(customerData, `${baseContext}_postTest_1`);

  // Post-Condition: Setup config SMTP
  resetSmtpConfigTest(`${baseContext}_postTest_2`);
});
