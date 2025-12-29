// Import utils
import testContext from '@utils/testContext';

// Import common tests
import {resetSmtpConfigTest, setupSmtpConfigTest} from '@commonTests/BO/advancedParameters/smtp';
import {deleteCustomerTest} from '@commonTests/BO/customers/customer';
import {enableTheme, disableTheme} from '@commonTests/BO/design/hummingbird';

import {
  type BrowserContext,
  dataPaymentMethods,
  FakerAddress,
  FakerCustomer,
  foHummingbirdCartPage,
  foHummingbirdCheckoutPage,
  foHummingbirdCheckoutOrderConfirmationPage,
  foHummingbirdHomePage,
  foHummingbirdProductPage,
  type MailDev,
  type MailDevEmail,
  type Page,
  utilsMail,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';

const baseContext: string = 'functional_FO_hummingbird_checkout_personalInformation_createAccount';

/*
Pre-condition:
- Setup SMTP parameters
- Install hummingbird them
Scenario:
- Open FO page
- Add first product to the cart
- Proceed to checkout and validate the cart
- Fill personal information
- Complete the order
- Check welcome email
Post-condition:
- Reset SMTP parameters
- Delete customer
- Uninstall hummingbird theme
 */

describe('FO - Checkout - Personal information : Create account', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  const guestData: FakerCustomer = new FakerCustomer();
  const addressData: FakerAddress = new FakerAddress({country: 'France'});
  let allEmails: MailDevEmail[];
  let numberOfEmails: number;
  let mailListener: MailDev;

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);

    mailListener = utilsMail.createMailListener();
    utilsMail.startListener(mailListener);

    // get all emails
    // @ts-ignore
    mailListener.getAllEmail((err: Error, emails: MailDevEmail[]) => {
      allEmails = emails;
    });
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
    utilsMail.stopListener(mailListener);
  });

  // Pre-Condition : Setup config SMTP
  setupSmtpConfigTest(`${baseContext}_preTest_0`);

  // Pre-condition : Install Hummingbird
  enableTheme('hummingbird', `${baseContext}_preTest_1`);

  describe('Create account', async () => {
    it('should open FO page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'openFO', baseContext);

      // Go to FO and change language
      await foHummingbirdHomePage.goToFo(page);
      await foHummingbirdHomePage.changeLanguage(page, 'en');

      const isHomePage = await foHummingbirdHomePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
    });

    it('should add product to cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      await foHummingbirdHomePage.goToProductPage(page, 1);
      await foHummingbirdProductPage.addProductToTheCart(page, 1);

      const pageTitle = await foHummingbirdCartPage.getPageTitle(page);
      expect(pageTitle).to.equal(foHummingbirdCartPage.pageTitle);
    });

    it('should proceed to checkout validate the cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'validateCart', baseContext);

      await foHummingbirdCartPage.clickOnProceedToCheckout(page);

      const isCheckoutPage = await foHummingbirdCheckoutPage.isCheckoutPage(page);
      expect(isCheckoutPage).to.equal(true);
    });

    it('should fill guest personal information', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setPersonalInformation', baseContext);

      const isStepPersonalInfoCompleted = await foHummingbirdCheckoutPage.setGuestPersonalInformation(page, guestData);
      expect(isStepPersonalInfoCompleted, 'Step personal information is not completed').to.equal(true);
    });

    it('should fill address form and go to delivery step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setAddressStep', baseContext);

      const isStepAddressComplete = await foHummingbirdCheckoutPage.setAddress(page, addressData);
      expect(isStepAddressComplete, 'Step Address is not complete').to.equal(true);
    });

    it('should validate the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'validateOrder', baseContext);

      // Delivery step - Go to payment step
      const isStepDeliveryComplete = await foHummingbirdCheckoutPage.goToPaymentStep(page);
      expect(isStepDeliveryComplete, 'Step Address is not complete').to.equal(true);

      // Payment step - Choose payment step
      await foHummingbirdCheckoutPage.choosePaymentAndOrder(page, dataPaymentMethods.wirePayment.moduleName);
      const cardTitle = await foHummingbirdCheckoutOrderConfirmationPage.getOrderConfirmationCardTitle(page);

      // Check the confirmation message
      expect(cardTitle).to.contains(foHummingbirdCheckoutOrderConfirmationPage.orderConfirmationCardTitle);
    });

    it('should check if welcome mail and order confirmation email are in mailbox', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkWelcomeMail', baseContext);

      numberOfEmails = allEmails.length;
      expect(allEmails[numberOfEmails - 1].subject).to.equal(`[${global.INSTALL.SHOP_NAME}] Order confirmation`);
      expect(allEmails[numberOfEmails - 2].subject).to.equal(`[${global.INSTALL.SHOP_NAME}] Awaiting bank wire payment`);
      expect(allEmails[numberOfEmails - 3].subject).to.equal(`[${global.INSTALL.SHOP_NAME}] Welcome!`);
    });

    it('should check the content of the welcome email', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkEmailText', baseContext);

      expect(allEmails[numberOfEmails - 3].text).to.contains(`Hi ${guestData.firstName} ${guestData.lastName}`)
        .and.to.contains(`Thank you for creating a customer account at ${global.INSTALL.SHOP_NAME}.`)
        .and.to.contains(`Email address: ${guestData.email}`);
    });
  });

  // Post-condition: Delete created customer account from BO
  deleteCustomerTest(guestData, `${baseContext}_postTest_1`);

  // Post-condition : Reset SMTP config
  resetSmtpConfigTest(`${baseContext}_postTest_2`);

  // Post-condition : Uninstall Hummingbird
  disableTheme('hummingbird', `${baseContext}_postTest_3`);
});
