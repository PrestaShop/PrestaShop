import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boCustomersPage,
  boCustomersCreatePage,
  boDashboardPage,
  boLoginPage,
  boPaymentPreferencesPage,
  type BrowserContext,
  dataCustomers,
  FakerAddress,
  FakerCustomer,
  foClassicCartPage,
  foClassicCheckoutPage,
  foClassicHomePage,
  foClassicProductPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_payment_preferences_groupRestrictions';

describe('BO - Payment - Preferences : Configure group restrictions', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  let numberOfCustomers: number = 0;

  const address: FakerAddress = new FakerAddress({city: 'Paris', country: 'France'});
  const visitorData: FakerCustomer = new FakerCustomer({defaultCustomerGroup: 'Visitor'});
  const guestData: FakerCustomer = new FakerCustomer({defaultCustomerGroup: 'Guest'});

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

  describe('Create two customers in visitor and guest groups', async () => {
    it('should go to \'Customers > Customers\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCustomersPageToCreate', baseContext);

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
      await testContext.addContextItem(this, 'testIdentifier', 'resetBeforeCreate', baseContext);

      numberOfCustomers = await boCustomersPage.resetAndGetNumberOfLines(page);
      expect(numberOfCustomers).to.be.above(0);
    });

    [
      {args: {customerData: visitorData}},
      {args: {customerData: guestData}},
    ].forEach((test, index: number) => {
      it('should go to add new customer page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddNewCustomerPage${index}`, baseContext);

        await boCustomersPage.goToAddNewCustomerPage(page);

        const pageTitle = await boCustomersCreatePage.getPageTitle(page);
        expect(pageTitle).to.contains(boCustomersCreatePage.pageTitleCreate);
      });

      it(`should create customer n°${index + 1} and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createCustomer${index}`, baseContext);

        // Create customer
        const textResult = await boCustomersCreatePage.createEditCustomer(page, test.args.customerData);
        expect(textResult).to.equal(boCustomersPage.successfulCreationMessage);

        // Check number of customers
        const numberOfCustomersAfterCreation = await boCustomersPage.getNumberOfElementInGrid(page);
        expect(numberOfCustomersAfterCreation).to.be.equal(numberOfCustomers + index + 1);
      });
    });
  });

  describe('Configure group restrictions and check in FO', async () => {
    it('should go to \'Payment > Preferences\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToPreferencesPage', baseContext);

      await boCustomersPage.goToSubMenu(
        page,
        boCustomersPage.paymentParentLink,
        boCustomersPage.preferencesLink,
      );

      const pageTitle = await boPaymentPreferencesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boPaymentPreferencesPage.pageTitle);
    });

    [
      {args: {groupName: 'Visitor', id: '0', customer: visitorData}},
      {args: {groupName: 'Guest', id: '1', customer: guestData}},
      {args: {groupName: 'Customer', id: '2', customer: dataCustomers.johnDoe}},
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

            const result = await boPaymentPreferencesPage.setGroupRestrictions(
              page,
              group.args.id,
              test.args.paymentModuleToEdit,
              test.args.check,
            );
            expect(result).to.contains(boPaymentPreferencesPage.successfulUpdateMessage);
          });

          it('should view my shop', async function () {
            await testContext.addContextItem(this, 'testIdentifier', `viewMyShop${index}${groupIndex}`, baseContext);

            // Click on view my shop
            page = await boPaymentPreferencesPage.viewMyShop(page);
            // Logout if already login
            if (index === 0 && groupIndex !== 0) {
              await foClassicHomePage.logout(page);
            }
            // Change FO language
            await foClassicHomePage.changeLanguage(page, 'en');

            const pageTitle = await foClassicHomePage.getPageTitle(page);
            expect(pageTitle).to.contains(foClassicHomePage.pageTitle);
          });

          it('should add the first product to the cart and proceed to checkout', async function () {
            await testContext.addContextItem(
              this,
              'testIdentifier',
              `addFirstProductToCart${index}${groupIndex}`,
              baseContext,
            );

            // Go to the first product page
            await foClassicHomePage.goToProductPage(page, 1);
            // Add the product to the cart
            await foClassicProductPage.addProductToTheCart(page);
            // Proceed to checkout the shopping cart
            await foClassicCartPage.clickOnProceedToCheckout(page);

            const isCheckoutPage = await foClassicCheckoutPage.isCheckoutPage(page);
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

              await foClassicCheckoutPage.clickOnSignIn(page);

              const isStepLoginComplete = await foClassicCheckoutPage.customerLogin(page, group.args.customer);
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

              const isStepAddressComplete = await foClassicCheckoutPage.setAddress(page, address);
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

              const isStepAddressComplete = await foClassicCheckoutPage.goToDeliveryStep(page);
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
            const isStepDeliveryComplete = await foClassicCheckoutPage.goToPaymentStep(page);
            expect(isStepDeliveryComplete, 'Step Address is not complete').to.eq(true);

            // Check wire Payment block
            let isVisible = await foClassicCheckoutPage.isPaymentMethodExist(page, test.args.paymentModuleToEdit);
            expect(isVisible).to.be.equal(test.args.wirePaymentExist);

            // Check Payment block
            isVisible = await foClassicCheckoutPage.isPaymentMethodExist(page, test.args.defaultPaymentModule);
            expect(isVisible).to.be.equal(test.args.checkPaymentExist);
          });

          it('should go back to BO', async function () {
            await testContext.addContextItem(this, 'testIdentifier', `goBackToBo${index}${groupIndex}`, baseContext);

            // Close current tab
            page = await foClassicHomePage.closePage(browserContext, page, 0);

            const pageTitle = await boPaymentPreferencesPage.getPageTitle(page);
            expect(pageTitle).to.contains(boPaymentPreferencesPage.pageTitle);
          });
        });
      });
    });
  });

  describe('Delete the two created customers', async () => {
    it('should go to \'Customers > Customers\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCustomersPageToDelete', baseContext);

      await boPaymentPreferencesPage.goToSubMenu(
        page,
        boPaymentPreferencesPage.customersParentLink,
        boPaymentPreferencesPage.customersLink,
      );

      const pageTitle = await boCustomersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCustomersPage.pageTitle);
    });

    [
      {args: {customerData: visitorData}},
      {args: {customerData: guestData}},
    ].forEach((test, index: number) => {
      it('should filter list by email', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `filterToDelete${index}`, baseContext);

        // Reset before filter
        await boCustomersPage.resetFilter(page);

        await boCustomersPage.filterCustomers(
          page,
          'input',
          'email',
          test.args.customerData.email,
        );

        const textEmail = await boCustomersPage.getTextColumnFromTableCustomers(page, 1, 'email');
        expect(textEmail).to.contains(test.args.customerData.email);
      });

      it(`should delete customer n°${index + 1}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `deleteCustomer${index}`, baseContext);

        const textResult = await boCustomersPage.deleteCustomer(page, 1);
        expect(textResult).to.equal(boCustomersPage.successfulDeleteMessage);

        // Check number of customers after delete
        const numberOfCustomersAfterDelete = await boCustomersPage.resetAndGetNumberOfLines(page);
        expect(numberOfCustomersAfterDelete).to.be.equal(numberOfCustomers - index + 1);
      });
    });
  });
});
