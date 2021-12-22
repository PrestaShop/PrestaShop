require('module-alias/register');

// Import expect from chai
const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const testContext = require('@utils/testContext');

// Import login steps
const loginCommon = require('@commonTests/loginBO');

// Import BO pages
const dashboardPage = require('@pages/BO/dashboard');
const customersPage = require('@pages/BO/customers');
const addCustomerPage = require('@pages/BO/customers/add');
const preferencesPage = require('@pages/BO/payment/preferences');

// Import FO pages
const productPage = require('@pages/FO/product');
const homePage = require('@pages/FO/home');
const cartPage = require('@pages/FO/cart');
const checkoutPage = require('@pages/FO/checkout');

// Import data
const {DefaultCustomer} = require('@data/demo/customer');
const AddressData = require('@data/faker/address');
const CustomerFaker = require('@data/faker/customer');

const baseContext = 'functional_BO_payment_preferences_groupRestrictions';

let browserContext;
let page;

let numberOfCustomers = 0;

// Init data
const address = new AddressData({city: 'Paris', country: 'France'});
const visitorData = new CustomerFaker({defaultCustomerGroup: 'Visitor'});
const guestData = new CustomerFaker({defaultCustomerGroup: 'Guest'});

describe('BO - Payment - Preferences : Configure group restrictions', async () => {
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

  describe('Create two customers in visitor and guest groups', async () => {
    it('should go to \'Customers > Customers\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCustomersPageToCreate', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.customersParentLink,
        dashboardPage.customersLink,
      );

      await customersPage.closeSfToolBar(page);

      const pageTitle = await customersPage.getPageTitle(page);
      await expect(pageTitle).to.contains(customersPage.pageTitle);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetBeforeCreate', baseContext);

      numberOfCustomers = await customersPage.resetAndGetNumberOfLines(page);
      await expect(numberOfCustomers).to.be.above(0);
    });

    [
      {args: {customerData: visitorData}},
      {args: {customerData: guestData}},
    ].forEach((test, index) => {
      it('should go to add new customer page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddNewCustomerPage${index}`, baseContext);

        await customersPage.goToAddNewCustomerPage(page);
        const pageTitle = await addCustomerPage.getPageTitle(page);
        await expect(pageTitle).to.contains(addCustomerPage.pageTitleCreate);
      });

      it(`should create customer n°${index + 1} and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createCustomer${index}`, baseContext);

        // Create customer
        const textResult = await addCustomerPage.createEditCustomer(page, test.args.customerData);
        await expect(textResult).to.equal(customersPage.successfulCreationMessage);

        // Check number of customers
        const numberOfCustomersAfterCreation = await customersPage.getNumberOfElementInGrid(page);
        await expect(numberOfCustomersAfterCreation).to.be.equal(numberOfCustomers + index + 1);
      });
    });
  });

  describe('Configure group restrictions and check in FO', async () => {
    it('should go to \'Payment > Preferences\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToPreferencesPage', baseContext);

      await customersPage.goToSubMenu(
        page,
        customersPage.paymentParentLink,
        customersPage.preferencesLink,
      );

      const pageTitle = await preferencesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(preferencesPage.pageTitle);
    });

    [
      {args: {groupName: 'Visitor', id: 0, customer: visitorData}},
      {args: {groupName: 'Guest', id: 1, customer: guestData}},
      {args: {groupName: 'Customer', id: 2, customer: DefaultCustomer}},
    ].forEach((group, groupIndex) => {
      describe(`Configure '${group.args.groupName}' group restrictions then check in FO`, async () => {
        const tests = [
          {
            args: {
              action: 'uncheck',
              paymentModuleToEdit: 'ps_wirepayment',
              defaultPaymentModule: 'ps_checkpayment',
              check: false,
              wirePaymentExist: false,
              checkPaymentExist: true,
            },
          },
          {
            args: {
              action: 'uncheck',
              paymentModuleToEdit: 'ps_checkpayment',
              defaultPaymentModule: 'ps_wirepayment',
              check: false,
              wirePaymentExist: false,
              checkPaymentExist: false,
            },
          },
          {
            args: {
              action: 'check',
              paymentModuleToEdit: 'ps_wirepayment',
              defaultPaymentModule: 'ps_checkpayment',
              check: true,
              wirePaymentExist: true,
              checkPaymentExist: false,
            },
          },
          {
            args: {
              action: 'check',
              paymentModuleToEdit: 'ps_checkpayment',
              defaultPaymentModule: 'ps_wirepayment',
              check: true,
              wirePaymentExist: true,
              checkPaymentExist: true,
            },
          },
        ];

        tests.forEach((test, index) => {
          it(`should ${test.args.action} '${test.args.paymentModuleToEdit}'`, async function () {
            await testContext.addContextItem(
              this,
              'testIdentifier',
              `${test.args.action}_${test.args.paymentModuleToEdit}From${group.args.groupName}Group`,
              baseContext,
            );

            const result = await preferencesPage.setGroupRestrictions(
              page,
              group.args.id,
              test.args.paymentModuleToEdit,
              test.args.check,
            );

            await expect(result).to.contains(preferencesPage.successfulUpdateMessage);
          });

          it('should view my shop', async function () {
            await testContext.addContextItem(this, 'testIdentifier', `viewMyShop${index}${groupIndex}`, baseContext);

            // Click on view my shop
            page = await preferencesPage.viewMyShop(page);

            // Logout if already login
            if (index === 0 && groupIndex !== 0) {
              await homePage.logout(page);
            }

            // Change FO language
            await homePage.changeLanguage(page, 'en');

            const pageTitle = await homePage.getPageTitle(page);
            await expect(pageTitle).to.contains(homePage.pageTitle);
          });

          it('should add the first product to the cart and proceed to checkout', async function () {
            await testContext.addContextItem(
              this,
              'testIdentifier',
              `addFirstProductToCart${index}${groupIndex}`,
              baseContext,
            );

            // Go to the first product page
            await homePage.goToProductPage(page, 1);

            // Add the product to the cart
            await productPage.addProductToTheCart(page);

            // Proceed to checkout the shopping cart
            await cartPage.clickOnProceedToCheckout(page);

            const isCheckoutPage = await checkoutPage.isCheckoutPage(page);
            await expect(isCheckoutPage).to.be.true;
          });

          // Personal information step - Login
          if (index === 0) {
            it('should login and go to address step', async function () {
              await testContext.addContextItem(
                this,
                'testIdentifier',
                `loginToFO${index}${groupIndex}`,
                baseContext,
              );

              await checkoutPage.clickOnSignIn(page);
              const isStepLoginComplete = await checkoutPage.customerLogin(page, group.args.customer);
              await expect(isStepLoginComplete, 'Step Personal information is not complete').to.be.true;
            });
          }

          // Address step - Add address
          if (group.args.groupName !== 'Customer' && index === 0) {
            it('should create address then continue to delivery step', async function () {
              await testContext.addContextItem(
                this,
                'testIdentifier',
                `createAddress${index}${groupIndex}`,
                baseContext,
              );

              const isStepAddressComplete = await checkoutPage.setAddress(page, address);
              await expect(isStepAddressComplete, 'Step Address is not complete').to.be.true;
            });
          }

          // Address step - Go to delivery step
          if (group.args.groupName === 'Customer' || index !== 0) {
            it('should continue to delivery step', async function () {
              await testContext.addContextItem(
                this,
                'testIdentifier',
                `goToDeliveryStep${index}${groupIndex}`,
                baseContext,
              );

              const isStepAddressComplete = await checkoutPage.goToDeliveryStep(page);
              await expect(isStepAddressComplete, 'Step Address is not complete').to.be.true;
            });
          }

          // Delivery step - Go to payment step and check payment module
          it('should continue to payment step and check the existence of payment method', async function () {
            await testContext.addContextItem(
              this,
              'testIdentifier',
              `goToPaymentStep${index}${groupIndex}`,
              baseContext,
            );

            // Go to payment step
            const isStepDeliveryComplete = await checkoutPage.goToPaymentStep(page);
            await expect(isStepDeliveryComplete, 'Step Address is not complete').to.be.true;

            // Check wire Payment block
            let isVisible = await checkoutPage.isPaymentMethodExist(page, test.args.paymentModuleToEdit);
            await expect(isVisible).to.be.equal(test.args.wirePaymentExist);

            // Check check Payment block
            isVisible = await checkoutPage.isPaymentMethodExist(page, test.args.defaultPaymentModule);
            await expect(isVisible).to.be.equal(test.args.checkPaymentExist);
          });

          it('should go back to BO', async function () {
            await testContext.addContextItem(this, 'testIdentifier', `goBackToBo${index}${groupIndex}`, baseContext);

            // Close current tab
            page = await homePage.closePage(browserContext, page, 0);

            const pageTitle = await preferencesPage.getPageTitle(page);
            await expect(pageTitle).to.contains(preferencesPage.pageTitle);
          });
        });
      });
    });
  });

  describe('Delete the two created customers', async () => {
    it('should go to \'Customers > Customers\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCustomersPageToDelete', baseContext);

      await preferencesPage.goToSubMenu(
        page,
        preferencesPage.customersParentLink,
        preferencesPage.customersLink,
      );

      const pageTitle = await customersPage.getPageTitle(page);
      await expect(pageTitle).to.contains(customersPage.pageTitle);
    });

    [
      {args: {customerData: visitorData}},
      {args: {customerData: guestData}},
    ].forEach((test, index) => {
      it('should filter list by email', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `filterToDelete${index}`, baseContext);

        // Reset before filter
        await customersPage.resetFilter(page);

        await customersPage.filterCustomers(
          page,
          'input',
          'email',
          test.args.customerData.email,
        );

        const textEmail = await customersPage.getTextColumnFromTableCustomers(page, 1, 'email');
        await expect(textEmail).to.contains(test.args.customerData.email);
      });

      it(`should delete customer n°${index + 1}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `deleteCustomer${index}`, baseContext);

        const textResult = await customersPage.deleteCustomer(page, 1);
        await expect(textResult).to.equal(customersPage.successfulDeleteMessage);

        // Check number of customers after delete
        const numberOfCustomersAfterDelete = await customersPage.resetAndGetNumberOfLines(page);
        await expect(numberOfCustomersAfterDelete).to.be.equal(numberOfCustomers - index + 1);
      });
    });
  });
});
