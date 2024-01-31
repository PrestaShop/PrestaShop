// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import login steps
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import customersPage from '@pages/BO/customers';
import addCustomerPage from '@pages/BO/customers/add';
import preferencesPage from '@pages/BO/payment/preferences';
// Import FO pages
import {cartPage} from '@pages/FO/classic/cart';
import checkoutPage from '@pages/FO/classic/checkout';
import {homePage} from '@pages/FO/classic/home';
import productPage from '@pages/FO/classic/product';

// Import data
import Customers from '@data/demo/customers';
import AddressData from '@data/faker/address';
import CustomerData from '@data/faker/customer';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_payment_preferences_groupRestrictions';

describe('BO - Payment - Preferences : Configure group restrictions', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  let numberOfCustomers: number = 0;

  const address: AddressData = new AddressData({city: 'Paris', country: 'France'});
  const visitorData: CustomerData = new CustomerData({defaultCustomerGroup: 'Visitor'});
  const guestData: CustomerData = new CustomerData({defaultCustomerGroup: 'Guest'});

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
      expect(pageTitle).to.contains(customersPage.pageTitle);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetBeforeCreate', baseContext);

      numberOfCustomers = await customersPage.resetAndGetNumberOfLines(page);
      expect(numberOfCustomers).to.be.above(0);
    });

    [
      {args: {customerData: visitorData}},
      {args: {customerData: guestData}},
    ].forEach((test, index: number) => {
      it('should go to add new customer page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddNewCustomerPage${index}`, baseContext);

        await customersPage.goToAddNewCustomerPage(page);

        const pageTitle = await addCustomerPage.getPageTitle(page);
        expect(pageTitle).to.contains(addCustomerPage.pageTitleCreate);
      });

      it(`should create customer n°${index + 1} and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createCustomer${index}`, baseContext);

        // Create customer
        const textResult = await addCustomerPage.createEditCustomer(page, test.args.customerData);
        expect(textResult).to.equal(customersPage.successfulCreationMessage);

        // Check number of customers
        const numberOfCustomersAfterCreation = await customersPage.getNumberOfElementInGrid(page);
        expect(numberOfCustomersAfterCreation).to.be.equal(numberOfCustomers + index + 1);
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
      expect(pageTitle).to.contains(preferencesPage.pageTitle);
    });

    [
      {args: {groupName: 'Visitor', id: '0', customer: visitorData}},
      {args: {groupName: 'Guest', id: '1', customer: guestData}},
      {args: {groupName: 'Customer', id: '2', customer: Customers.johnDoe}},
    ].forEach((group, groupIndex: number) => {
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

        tests.forEach((test, index: number) => {
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
            expect(result).to.contains(preferencesPage.successfulUpdateMessage);
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
            expect(pageTitle).to.contains(homePage.pageTitle);
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
            expect(isCheckoutPage).to.eq(true);
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
              expect(isStepLoginComplete, 'Step Personal information is not complete').to.eq(true);
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
              expect(isStepAddressComplete, 'Step Address is not complete').to.eq(true);
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
              expect(isStepAddressComplete, 'Step Address is not complete').to.eq(true);
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
            expect(isStepDeliveryComplete, 'Step Address is not complete').to.eq(true);

            // Check wire Payment block
            let isVisible = await checkoutPage.isPaymentMethodExist(page, test.args.paymentModuleToEdit);
            expect(isVisible).to.be.equal(test.args.wirePaymentExist);

            // Check Payment block
            isVisible = await checkoutPage.isPaymentMethodExist(page, test.args.defaultPaymentModule);
            expect(isVisible).to.be.equal(test.args.checkPaymentExist);
          });

          it('should go back to BO', async function () {
            await testContext.addContextItem(this, 'testIdentifier', `goBackToBo${index}${groupIndex}`, baseContext);

            // Close current tab
            page = await homePage.closePage(browserContext, page, 0);

            const pageTitle = await preferencesPage.getPageTitle(page);
            expect(pageTitle).to.contains(preferencesPage.pageTitle);
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
      expect(pageTitle).to.contains(customersPage.pageTitle);
    });

    [
      {args: {customerData: visitorData}},
      {args: {customerData: guestData}},
    ].forEach((test, index: number) => {
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
        expect(textEmail).to.contains(test.args.customerData.email);
      });

      it(`should delete customer n°${index + 1}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `deleteCustomer${index}`, baseContext);

        const textResult = await customersPage.deleteCustomer(page, 1);
        expect(textResult).to.equal(customersPage.successfulDeleteMessage);

        // Check number of customers after delete
        const numberOfCustomersAfterDelete = await customersPage.resetAndGetNumberOfLines(page);
        expect(numberOfCustomersAfterDelete).to.be.equal(numberOfCustomers - index + 1);
      });
    });
  });
});
