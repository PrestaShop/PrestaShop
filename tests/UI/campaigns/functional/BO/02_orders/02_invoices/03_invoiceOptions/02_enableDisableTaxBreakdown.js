require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
// Importing pages
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const InvoicesPage = require('@pages/BO/orders/invoices/index');
const TaxesPage = require('@pages/BO/international/taxes/index');
const TaxRulesPage = require('@pages/BO/international/taxes/taxRules/index');
const AddTaxRulesPage = require('@pages/BO/international/taxes/taxRules/add');
const ProductsPage = require('@pages/BO/catalog/products/index');
const AddProductPage = require('@pages/BO/catalog/products/add');
const ProductPage = require('@pages/FO/product');
const FOBasePage = require('@pages/FO/FObasePage');
const CartPage = require('@pages/FO/cart');
const CheckoutPage = require('@pages/FO/checkout');
const OrderConfirmationPage = require('@pages/FO/checkout/orderConfirmation');
const OrdersPage = require('@pages/BO/orders/index');
const ViewOrderPage = require('@pages/BO/orders/view');
const files = require('@utils/files');
// Importing data
const TaxRuleGroup = require('@data/faker/taxRuleGroup');
const TaxRule = require('@data/faker/taxRule');
const ProductFaker = require('@data/faker/product');
const {PaymentMethods} = require('@data/demo/paymentMethods');
const {DefaultAccount} = require('@data/demo/customer');
const {Statuses} = require('@data/demo/orderStatuses');
// Test context imports
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_orders_invoices_invoiceOptions_enableDisableTaxBreakdown';

let browserContext;
let page;
let taxRuleGroupToCreate;
let firstTaxRuleToCreate;
let secondTaxRuleToCreate;
let productData;
let firstInvoiceFileName;
let secondInvoiceFileName;

// Init objects needed
const init = async function () {
  return {
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    invoicesPage: new InvoicesPage(page),
    taxesPage: new TaxesPage(page),
    taxRulesPage: new TaxRulesPage(page),
    addTaxRulesPage: new AddTaxRulesPage(page),
    productsPage: new ProductsPage(page),
    addProductPage: new AddProductPage(page),
    productPage: new ProductPage(page),
    foBasePage: new FOBasePage(page),
    cartPage: new CartPage(page),
    checkoutPage: new CheckoutPage(page),
    orderConfirmationPage: new OrderConfirmationPage(page),
    ordersPage: new OrdersPage(page),
    viewOrderPage: new ViewOrderPage(page),
  };
};

/*
Enable tax breakdown
Create tax rule
Create new product with the new tax rule
Create new order in FO with the created product
Generate the invoice and check the tax breakdown
Disable tax breakdown
Generate the invoice and check that there is no tax breakdown
 */
describe('Enable tax breakdown', async () => {
  // before and after functions
  before(async function () {
    // Create new tab
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    this.pageObjects = await init();

    // Create data

    taxRuleGroupToCreate = await (new TaxRuleGroup());

    firstTaxRuleToCreate = await (
      new TaxRule(
        {
          country: 'France',
          behaviour: 'Combine',
          tax: 'TVA FR 20%',
        },
      )
    );

    secondTaxRuleToCreate = await (
      new TaxRule(
        {
          country: 'France',
          behaviour: 'Combine',
          tax: 'TVA FR 10%',
        },
      )
    );

    const productToCreate = {
      type: 'Standard product',
      taxRule: taxRuleGroupToCreate.name,
    };

    productData = await (new ProductFaker(productToCreate));
  });
  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  // Login into BO
  loginCommon.loginBO();

  describe('Enable tax breakdown then check it in the invoice created', async () => {
    describe('Enable tax breakdown', async () => {
      it('should go to invoices page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToInvoicesPageToEnableTaxBreakDown', baseContext);

        await this.pageObjects.dashboardPage.goToSubMenu(
          this.pageObjects.dashboardPage.ordersParentLink,
          this.pageObjects.dashboardPage.invoicesLink,
        );

        await this.pageObjects.invoicesPage.closeSfToolBar();

        const pageTitle = await this.pageObjects.invoicesPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.invoicesPage.pageTitle);
      });

      it('should enable tax breakdown', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'enableTaxBreakDown', baseContext);

        await this.pageObjects.invoicesPage.enableTaxBreakdown(true);
        const textMessage = await this.pageObjects.invoicesPage.saveInvoiceOptions();
        await expect(textMessage).to.contains(this.pageObjects.invoicesPage.successfulUpdateMessage);
      });
    });

    describe('Create tax rule', async () => {
      it('should go to "Taxes" page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToTaxesPage', baseContext);

        await this.pageObjects.invoicesPage.goToSubMenu(
          this.pageObjects.invoicesPage.internationalParentLink,
          this.pageObjects.invoicesPage.taxesLink,
        );

        const pageTitle = await this.pageObjects.taxesPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.taxesPage.pageTitle);
      });

      it('should go to "Tax Rules" page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToTaxRulesPage', baseContext);

        await this.pageObjects.taxesPage.goToTaxRulesPage();

        const pageTitle = await this.pageObjects.taxRulesPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.taxRulesPage.pageTitle);
      });

      it('should go to Add new tax rules group page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToAddTaxRulePage', baseContext);

        await this.pageObjects.taxRulesPage.goToAddNewTaxRulesGroupPage();

        const pageTitle = await this.pageObjects.addTaxRulesPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.addTaxRulesPage.pageTitleCreate);
      });

      it('should create new tax rule group', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'createTaxRuleGroup', baseContext);

        const textResult = await this.pageObjects.addTaxRulesPage.createEditTaxRulesGroup(taxRuleGroupToCreate);
        await expect(textResult).to.contains(this.pageObjects.addTaxRulesPage.successfulCreationMessage);
      });

      it('should create new tax rule', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'createFirstTaxRule', baseContext);

        const textResult = await this.pageObjects.addTaxRulesPage.createEditTaxRules(firstTaxRuleToCreate);
        await expect(textResult).to.contains(this.pageObjects.addTaxRulesPage.successfulUpdateMessage);
      });

      it('should click on Add new tax rule button', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'clickToCreateSecondTaxRule', baseContext);

        await this.pageObjects.addTaxRulesPage.clickOnAddNewTaxRule();

        const pageTitle = await this.pageObjects.addTaxRulesPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.addTaxRulesPage.pageTitleEdit);
      });

      it('should create new tax rule', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'createSecondTaxRule', baseContext);

        const textResult = await this.pageObjects.addTaxRulesPage.createEditTaxRules(secondTaxRuleToCreate);
        await expect(textResult).to.contains(this.pageObjects.addTaxRulesPage.successfulUpdateMessage);
      });
    });

    describe('Create new product with the new tax rule', async () => {
      it('should go to Products page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToProductPageToCreateProduct', baseContext);

        await this.pageObjects.addTaxRulesPage.goToSubMenu(
          this.pageObjects.addTaxRulesPage.catalogParentLink,
          this.pageObjects.addTaxRulesPage.productsLink,
        );

        const pageTitle = await this.pageObjects.productsPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.productsPage.pageTitle);
      });

      it('should create Product', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'createProduct', baseContext);

        await this.pageObjects.productsPage.goToAddProductPage();

        const createProductMessage = await this.pageObjects.addProductPage.createEditBasicProduct(productData);
        await expect(createProductMessage).to.equal(this.pageObjects.addProductPage.settingUpdatedMessage);
      });
    });

    describe('Create new order in FO with the created product', async () => {
      it('should go to FO and create an order', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'createOrderInFO', baseContext);

        // Click on preview button
        page = await this.pageObjects.addProductPage.previewProduct();

        // Get new tab opened and init other pages
        this.pageObjects = await init();

        // Change home page language
        await this.pageObjects.foBasePage.changeLanguage('en');

        // Add the created product to the cart
        await this.pageObjects.productPage.addProductToTheCart();

        // Proceed to checkout the shopping cart
        await this.pageObjects.cartPage.clickOnProceedToCheckout();

        // Checkout the order
        // Personal information step - Login
        await this.pageObjects.checkoutPage.clickOnSignIn();
        await this.pageObjects.checkoutPage.customerLogin(DefaultAccount);

        // Address step - Go to delivery step
        const isStepAddressComplete = await this.pageObjects.checkoutPage.goToDeliveryStep();
        await expect(isStepAddressComplete, 'Step Address is not complete').to.be.true;

        // Delivery step - Go to payment step
        const isStepDeliveryComplete = await this.pageObjects.checkoutPage.goToPaymentStep();
        await expect(isStepDeliveryComplete, 'Step Address is not complete').to.be.true;

        // Payment step - Choose payment step
        await this.pageObjects.checkoutPage.choosePaymentAndOrder(PaymentMethods.wirePayment.moduleName);

        // Check the confirmation message
        const cardTitle = await this.pageObjects.orderConfirmationPage.getOrderConfirmationCardTitle();
        await expect(cardTitle).to.contains(this.pageObjects.orderConfirmationPage.orderConfirmationCardTitle);
      });

      it('should go back to BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goBackToBo', baseContext);

        // Close tab and init other page objects with new current tab
        page = await this.pageObjects.orderConfirmationPage.closePage(browserContext, 0);
        this.pageObjects = await init();

        const pageTitle = await this.pageObjects.addProductPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.addProductPage.pageTitle);
      });
    });

    describe('Generate the invoice and check the tax breakdown', async () => {
      it('should go to orders page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPageTaxBreakdown', baseContext);

        await this.pageObjects.dashboardPage.goToSubMenu(
          this.pageObjects.dashboardPage.ordersParentLink,
          this.pageObjects.dashboardPage.ordersLink,
        );

        const pageTitle = await this.pageObjects.ordersPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.ordersPage.pageTitle);
      });

      it('should go to the first order page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToFirstOrderPageTaxBreakdown', baseContext);

        await this.pageObjects.ordersPage.goToOrder(1);

        const pageTitle = await this.pageObjects.viewOrderPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.viewOrderPage.pageTitle);
      });

      it(`should change the order status to '${Statuses.paymentAccepted.status}' and check it`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'changeOrderStatusTaxBreakdown', baseContext);

        const result = await this.pageObjects.viewOrderPage.modifyOrderStatus(Statuses.paymentAccepted.status);
        await expect(result).to.equal(Statuses.paymentAccepted.status);
      });

      it('should download the invoice', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'downloadInvoiceTaxBreakdown', baseContext);

        // Download invoice
        firstInvoiceFileName = await this.pageObjects.viewOrderPage.downloadInvoice();

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
      it('should go to invoices page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToInvoicesPageToDisableTaxBreakdown', baseContext);

        await this.pageObjects.viewOrderPage.goToSubMenu(
          this.pageObjects.viewOrderPage.ordersParentLink,
          this.pageObjects.viewOrderPage.invoicesLink,
        );

        const pageTitle = await this.pageObjects.invoicesPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.invoicesPage.pageTitle);
      });

      it('should disable tax breakdown', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'disableTaxBreakdown', baseContext);

        await this.pageObjects.invoicesPage.enableTaxBreakdown(false);
        const textMessage = await this.pageObjects.invoicesPage.saveInvoiceOptions();
        await expect(textMessage).to.contains(this.pageObjects.invoicesPage.successfulUpdateMessage);
      });
    });

    describe('Generate the invoice and check that there is no tax breakdown', async () => {
      it('should go to the orders page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPageNoTaxBreakdown', baseContext);

        await this.pageObjects.invoicesPage.goToSubMenu(
          this.pageObjects.invoicesPage.ordersParentLink,
          this.pageObjects.invoicesPage.ordersLink,
        );

        const pageTitle = await this.pageObjects.ordersPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.ordersPage.pageTitle);
      });

      it('should go to the first order page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToFirstOrderPageNoTaxBreakdown', baseContext);

        await this.pageObjects.ordersPage.goToOrder(1);
        const pageTitle = await this.pageObjects.viewOrderPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.viewOrderPage.pageTitle);
      });

      it('should download the invoice', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'downloadInvoiceNoTaxBreakdown', baseContext);

        // Download invoice and check existence
        secondInvoiceFileName = await this.pageObjects.viewOrderPage.downloadInvoice();

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
