require('module-alias/register');

// Import utils
const helper = require('@utils/helpers');
const files = require('@utils/files');

// Import login steps
const loginCommon = require('@commonTests/loginBO');

// Import BO pages
const dashboardPage = require('@pages/BO/dashboard');
const invoicesPage = require('@pages/BO/orders/invoices/index');
const taxesPage = require('@pages/BO/international/taxes/index');
const taxRulesPage = require('@pages/BO/international/taxes/taxRules/index');
const addTaxRulesPage = require('@pages/BO/international/taxes/taxRules/add');
const boProductsPage = require('@pages/BO/catalog/products/index');
const addProductPage = require('@pages/BO/catalog/products/add');
const ordersPage = require('@pages/BO/orders/index');
const viewOrderPage = require('@pages/BO/orders/view');

// Import FO pages
const foProductPage = require('@pages/FO/product');
const cartPage = require('@pages/FO/cart');
const checkoutPage = require('@pages/FO/checkout');
const orderConfirmationPage = require('@pages/FO/checkout/orderConfirmation');

// Import data
const TaxRuleGroup = require('@data/faker/taxRuleGroup');
const TaxRule = require('@data/faker/taxRule');
const ProductFaker = require('@data/faker/product');
const {PaymentMethods} = require('@data/demo/paymentMethods');
const {DefaultCustomer} = require('@data/demo/customer');
const {Statuses} = require('@data/demo/orderStatuses');

// Import test Context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_orders_invoices_invoiceOptions_enableDisableTaxBreakdown';

// Import expect from chai
const {expect} = require('chai');

let browserContext;
let page;
const taxRuleGroupToCreate = new TaxRuleGroup();
const firstTaxRuleToCreate = new TaxRule(
  {
    country: 'France',
    behaviour: 'Combine',
    tax: 'TVA FR 20%',
  },
);
const secondTaxRuleToCreate = new TaxRule(
  {
    country: 'France',
    behaviour: 'Combine',
    tax: 'TVA FR 10%',
  },
);

const productToCreate = {
  type: 'Standard product',
  taxRule: taxRuleGroupToCreate.name,
};
const productData = new ProductFaker(productToCreate);

let firstInvoiceFileName;
let secondInvoiceFileName;

/*
Enable tax breakdown
Create new tax rule group
Create 2 new tax rules
Create new product with the new tax rule
Create new order in FO with the created product
Generate the invoice and check the tax breakdown
Disable tax breakdown
Generate the invoice and check that there is no tax breakdown
 */
describe('BO - Orders - Invoices : Enable/Disable tax breakdown', async () => {
  // before and after functions
  before(async function () {
    // Create new tab
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });


  it('should login in BO', async function () {
    await loginCommon.loginBO(this, page);
  });

  describe('Enable tax breakdown then check it in the invoice', async () => {
    describe('Enable tax breakdown', async () => {
      it('should go to \'Orders > Invoices\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToInvoicesPageToEnableTaxBreakDown', baseContext);

        await dashboardPage.goToSubMenu(
          page,
          dashboardPage.ordersParentLink,
          dashboardPage.invoicesLink,
        );

        await invoicesPage.closeSfToolBar(page);

        const pageTitle = await invoicesPage.getPageTitle(page);
        await expect(pageTitle).to.contains(invoicesPage.pageTitle);
      });

      it('should enable tax breakdown', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'enableTaxBreakDown', baseContext);

        await invoicesPage.enableTaxBreakdown(page, true);
        const textMessage = await invoicesPage.saveInvoiceOptions(page);
        await expect(textMessage).to.contains(invoicesPage.successfulUpdateMessage);
      });
    });

    describe('Create 2 new tax rules', async () => {
      it('should go to \'International > Taxes\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToTaxesPage', baseContext);

        await invoicesPage.goToSubMenu(
          page,
          invoicesPage.internationalParentLink,
          invoicesPage.taxesLink,
        );

        const pageTitle = await taxesPage.getPageTitle(page);
        await expect(pageTitle).to.contains(taxesPage.pageTitle);
      });

      it('should go to \'Tax Rules\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToTaxRulesPage', baseContext);

        await taxesPage.goToTaxRulesPage(page);

        const pageTitle = await taxRulesPage.getPageTitle(page);
        await expect(pageTitle).to.contains(taxRulesPage.pageTitle);
      });

      it('should go to \'Add new tax rules group\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToAddTaxRulePage', baseContext);

        await taxRulesPage.goToAddNewTaxRulesGroupPage(page);

        const pageTitle = await addTaxRulesPage.getPageTitle(page);
        await expect(pageTitle).to.contains(addTaxRulesPage.pageTitleCreate);
      });

      it('should create new tax rule group', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'createTaxRuleGroup', baseContext);

        const textResult = await addTaxRulesPage.createEditTaxRulesGroup(page, taxRuleGroupToCreate);
        await expect(textResult).to.contains(addTaxRulesPage.successfulCreationMessage);
      });

      it('should create new tax rule n°1', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'createFirstTaxRule', baseContext);

        const textResult = await addTaxRulesPage.createEditTaxRules(page, firstTaxRuleToCreate);
        await expect(textResult).to.contains(addTaxRulesPage.successfulUpdateMessage);
      });

      it('should go to \'Add new tax rule\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'clickToCreateSecondTaxRule', baseContext);

        await addTaxRulesPage.clickOnAddNewTaxRule(page);

        const pageTitle = await addTaxRulesPage.getPageTitle(page);
        await expect(pageTitle).to.contains(addTaxRulesPage.pageTitleEdit);
      });

      it('should create new tax rule n°2', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'createSecondTaxRule', baseContext);

        const textResult = await addTaxRulesPage.createEditTaxRules(page, secondTaxRuleToCreate);
        await expect(textResult).to.contains(addTaxRulesPage.successfulUpdateMessage);
      });
    });

    describe('Create new product with the new tax rule', async () => {
      it('should go to \'Products > Products\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToProductPageToCreateProduct', baseContext);

        await addTaxRulesPage.goToSubMenu(
          page,
          addTaxRulesPage.catalogParentLink,
          addTaxRulesPage.productsLink,
        );

        const pageTitle = await boProductsPage.getPageTitle(page);
        await expect(pageTitle).to.contains(boProductsPage.pageTitle);
      });

      it('should create product', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'createProduct', baseContext);

        await boProductsPage.goToAddProductPage(page);

        const createProductMessage = await addProductPage.createEditBasicProduct(page, productData);
        await expect(createProductMessage).to.equal(addProductPage.settingUpdatedMessage);
      });
    });

    describe('Create new order in FO with the created product', async () => {
      it('should preview product', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'previewProduct', baseContext);

        // Click on preview button
        page = await addProductPage.previewProduct(page);

        await foProductPage.changeLanguage(page, 'en');

        const pageTitle = await foProductPage.getPageTitle(page);
        await expect(pageTitle).to.contains(productData.name);
      });

      it('should add product to cart', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

        // Add the created product to the cart
        await foProductPage.addProductToTheCart(page);

        const pageTitle = await cartPage.getPageTitle(page);
        await expect(pageTitle).to.equal(cartPage.pageTitle);
      });

      it('should proceed to checkout and sign in by default customer', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'proceedToCheckoutAndSignIn', baseContext);

        // Proceed to checkout the shopping cart
        await cartPage.clickOnProceedToCheckout(page);

        // Personal information step - Login
        await checkoutPage.clickOnSignIn(page);
        await checkoutPage.customerLogin(page, DefaultCustomer);
      });

      it('should go to delivery step', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToDeliveryStep', baseContext);

        // Address step - Go to delivery step
        const isStepAddressComplete = await checkoutPage.goToDeliveryStep(page);
        await expect(isStepAddressComplete, 'Step Address is not complete').to.be.true;
      });

      it('should go to payment step', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToPaymentStep', baseContext);

        // Delivery step - Go to payment step
        const isStepDeliveryComplete = await checkoutPage.goToPaymentStep(page);
        await expect(isStepDeliveryComplete, 'Step Address is not complete').to.be.true;
      });

      it('should choose payment method and confirm the order', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'confirmOrder', baseContext);

        // Payment step - Choose payment step
        await checkoutPage.choosePaymentAndOrder(page, PaymentMethods.wirePayment.moduleName);

        // Check the confirmation message
        const cardTitle = await orderConfirmationPage.getOrderConfirmationCardTitle(page);
        await expect(cardTitle).to.contains(orderConfirmationPage.orderConfirmationCardTitle);
      });

      it('should go back to BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goBackToBo', baseContext);

        // Close tab and init other page objects with new current tab
        page = await orderConfirmationPage.closePage(browserContext, page, 0);

        const pageTitle = await addProductPage.getPageTitle(page);
        await expect(pageTitle).to.contains(addProductPage.pageTitle);
      });
    });

    describe('Generate the invoice and check the tax breakdown', async () => {
      it('should go to \'Orders > Orders\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPageTaxBreakdown', baseContext);

        await dashboardPage.goToSubMenu(
          page,
          dashboardPage.ordersParentLink,
          dashboardPage.ordersLink,
        );

        const pageTitle = await ordersPage.getPageTitle(page);
        await expect(pageTitle).to.contains(ordersPage.pageTitle);
      });

      it('should go to the first order page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToFirstOrderPageTaxBreakdown', baseContext);

        await ordersPage.goToOrder(page, 1);

        const pageTitle = await viewOrderPage.getPageTitle(page);
        await expect(pageTitle).to.contains(viewOrderPage.pageTitle);
      });

      it(`should change the order status to '${Statuses.paymentAccepted.status}' and check it`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'changeOrderStatusTaxBreakdown', baseContext);

        const result = await viewOrderPage.modifyOrderStatus(page, Statuses.paymentAccepted.status);
        await expect(result).to.equal(Statuses.paymentAccepted.status);
      });

      it('should download the invoice', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'downloadInvoiceTaxBreakdown', baseContext);

        // Download invoice
        firstInvoiceFileName = await viewOrderPage.downloadInvoice(page);

        // Check that file exist
        const exist = await files.doesFileExist(firstInvoiceFileName);
        await expect(exist).to.be.true;
      });

      it('should check the tax breakdown', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkTaxBreakdownInFile', baseContext);

        // Check the existence of the first tax
        let exist = await files.isTextInPDF(firstInvoiceFileName, '10.000 %');
        await expect(exist).to.be.true;

        // Check the existence of the second tax
        exist = await files.isTextInPDF(firstInvoiceFileName, '20.000 %');
        await expect(exist).to.be.true;
      });
    });
  });

  describe('Disable tax breakdown then check the invoice file', async () => {
    describe('Disable tax breakdown', async () => {
      it('should go to \'Orders > Invoices\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToInvoicesPageToDisableTaxBreakdown', baseContext);

        await viewOrderPage.goToSubMenu(
          page,
          viewOrderPage.ordersParentLink,
          viewOrderPage.invoicesLink,
        );

        const pageTitle = await invoicesPage.getPageTitle(page);
        await expect(pageTitle).to.contains(invoicesPage.pageTitle);
      });

      it('should disable tax breakdown', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'disableTaxBreakdown', baseContext);

        await invoicesPage.enableTaxBreakdown(page, false);
        const textMessage = await invoicesPage.saveInvoiceOptions(page);
        await expect(textMessage).to.contains(invoicesPage.successfulUpdateMessage);
      });
    });

    describe('Generate the invoice and check that there is no tax breakdown', async () => {
      it('should go to \'Orders > Orders\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPageNoTaxBreakdown', baseContext);

        await invoicesPage.goToSubMenu(
          page,
          invoicesPage.ordersParentLink,
          invoicesPage.ordersLink,
        );

        const pageTitle = await ordersPage.getPageTitle(page);
        await expect(pageTitle).to.contains(ordersPage.pageTitle);
      });

      it('should go to the first order page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToFirstOrderPageNoTaxBreakdown', baseContext);

        await ordersPage.goToOrder(page, 1);
        const pageTitle = await viewOrderPage.getPageTitle(page);
        await expect(pageTitle).to.contains(viewOrderPage.pageTitle);
      });

      it('should download the invoice', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'downloadInvoiceNoTaxBreakdown', baseContext);

        // Download invoice and check existence
        secondInvoiceFileName = await viewOrderPage.downloadInvoice(page);

        const exist = await files.doesFileExist(secondInvoiceFileName);
        await expect(exist).to.be.true;
      });

      it('should check that there is no tax breakdown', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkNoTaxBreakdownInFile', baseContext);

        // Check that there is only one tax line 30.000 %
        let exist = await files.isTextInPDF(secondInvoiceFileName, '10.000 %');
        await expect(exist).to.be.false;

        exist = await files.isTextInPDF(secondInvoiceFileName, '20.000 %');
        await expect(exist).to.be.false;

        exist = await files.isTextInPDF(secondInvoiceFileName, '30.000 %');
        await expect(exist).to.be.true;
      });
    });
  });
});
