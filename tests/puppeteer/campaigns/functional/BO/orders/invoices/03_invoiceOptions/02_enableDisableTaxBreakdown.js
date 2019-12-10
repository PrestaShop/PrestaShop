require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
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
const OrderConfirmationPage = require('@pages/FO/orderConfirmation');
const OrdersPage = require('@pages/BO/orders/index');
const ViewOrderPage = require('@pages/BO/orders/view');
const files = require('@utils/files');
// Importing data
const TaxRuleGroup = require('@data/faker/taxRuleGroup');
const TaxRule = require('@data/faker/taxRule');
const ProductFaker = require('@data/faker/product');
const {PaymentMethods} = require('@data/demo/orders');
const {DefaultAccount} = require('@data/demo/customer');
const {Statuses} = require('@data/demo/orders');

let browser;
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
    boBasePage: new BOBasePage(page),
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

// Enable/disable tax breakdown
describe('Test enable/disable tax breakdown', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    await helper.setDownloadBehavior(page);
    this.pageObjects = await init();
    taxRuleGroupToCreate = await (new TaxRuleGroup());
    firstTaxRuleToCreate = await (new TaxRule({
      country: 'France',
      behaviour: 'Combine',
      tax: 'TVA FR 20%',
    }));
    secondTaxRuleToCreate = await (new TaxRule({
      country: 'France',
      behaviour: 'Combine',
      tax: 'TVA FR 10%',
    }));
    const productToCreate = {
      type: 'Standard product',
      productHasCombinations: false,
      taxRule: taxRuleGroupToCreate.name,
    };
    productData = await (new ProductFaker(productToCreate));
  });
  after(async () => {
    /* Delete the generated invoice */
    files.deleteFile(`${global.BO.DOWNLOAD_PATH}/${secondInvoiceFileName}.pdf`);
    await helper.closeBrowser(browser);
  });

  // Login into BO
  loginCommon.loginBO();

  describe('Enable tax breakdown then check it in the invoice created', async () => {
    describe('Enable tax breakdown', async () => {
      it('should go to invoices page', async function () {
        await this.pageObjects.boBasePage.goToSubMenu(
          this.pageObjects.boBasePage.ordersParentLink,
          this.pageObjects.boBasePage.invoicesLink,
        );
        await this.pageObjects.boBasePage.closeSfToolBar();
        const pageTitle = await this.pageObjects.invoicesPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.invoicesPage.pageTitle);
      });

      it('should enable tax breakdown', async function () {
        await this.pageObjects.invoicesPage.enableTaxBreakdown(true);
        const textMessage = await this.pageObjects.invoicesPage.saveInvoiceOptions();
        await expect(textMessage).to.contains(this.pageObjects.invoicesPage.successfulUpdateMessage);
      });
    });

    describe('Create tax rule', async () => {
      it('should go to "Taxes" page', async function () {
        await this.pageObjects.boBasePage.goToSubMenu(
          this.pageObjects.boBasePage.internationalParentLink,
          this.pageObjects.boBasePage.taxesLink,
        );
        const pageTitle = await this.pageObjects.taxesPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.taxesPage.pageTitle);
      });

      it('should go to "Tax Rules" page', async function () {
        await this.pageObjects.taxesPage.goToTaxRulesPage();
        const pageTitle = await this.pageObjects.taxRulesPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.taxRulesPage.pageTitle);
      });

      it('should go to Add new tax rules group page', async function () {
        await this.pageObjects.taxRulesPage.goToAddNewTaxRulesGroupPage();
        const pageTitle = await this.pageObjects.addTaxRulesPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.addTaxRulesPage.pageTitleCreate);
      });

      it('should create new tax rule group', async function () {
        const textResult = await this.pageObjects.addTaxRulesPage.createEditTaxRulesGroup(taxRuleGroupToCreate);
        await expect(textResult).to.contains(this.pageObjects.addTaxRulesPage.successfulCreationMessage);
      });

      it('should create new tax rule', async function () {
        const textResult = await this.pageObjects.addTaxRulesPage.createEditTaxRules(firstTaxRuleToCreate);
        await expect(textResult).to.contains(this.pageObjects.addTaxRulesPage.successfulUpdateMessage);
      });

      it('should click on Add new tax rule button', async function () {
        await this.pageObjects.addTaxRulesPage.clickOnAddNewTaxRule();
        const pageTitle = await this.pageObjects.addTaxRulesPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.addTaxRulesPage.pageTitleEdit);
      });

      it('should create new tax rule', async function () {
        const textResult = await this.pageObjects.addTaxRulesPage.createEditTaxRules(secondTaxRuleToCreate);
        await expect(textResult).to.contains(this.pageObjects.addTaxRulesPage.successfulUpdateMessage);
      });
    });

    describe('Create new product with the new tax rule', async () => {
      it('should go to Products page', async function () {
        await this.pageObjects.boBasePage.goToSubMenu(
          this.pageObjects.boBasePage.catalogParentLink,
          this.pageObjects.boBasePage.productsLink,
        );
        await this.pageObjects.boBasePage.closeSfToolBar();
        const pageTitle = await this.pageObjects.productsPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.productsPage.pageTitle);
      });

      it('should create Product', async function () {
        await this.pageObjects.productsPage.goToAddProductPage();
        const createProductMessage = await this.pageObjects.addProductPage.createEditProduct(productData);
        await expect(createProductMessage).to.equal(this.pageObjects.addProductPage.settingUpdatedMessage);
      });
    });

    describe('Create new order in FO with the created product', async () => {
      it('should go to FO and create an order', async function () {
        // Click on preview button
        page = await this.pageObjects.addProductPage.previewProduct();
        this.pageObjects = await init();
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
        const cardTitle = await this.pageObjects.orderConfirmationPage
          .getTextContent(this.pageObjects.orderConfirmationPage.orderConfirmationCardTitleH3);
        // Check the confirmation message
        await expect(cardTitle).to.contains(this.pageObjects.orderConfirmationPage.orderConfirmationCardTitle);
        page = await this.pageObjects.orderConfirmationPage.closePage(browser, 1);
        this.pageObjects = await init();
      });
    });

    describe('Generate the invoice and check the tax Breakdown', async () => {
      it('should go to orders page', async function () {
        await this.pageObjects.boBasePage.goToSubMenu(
          this.pageObjects.boBasePage.ordersParentLink,
          this.pageObjects.boBasePage.ordersLink,
        );
        const pageTitle = await this.pageObjects.ordersPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.ordersPage.pageTitle);
      });

      it('should go to the first order page', async function () {
        await this.pageObjects.ordersPage.goToOrder(1);
        const pageTitle = await this.pageObjects.viewOrderPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.viewOrderPage.pageTitle);
      });

      it('should change the order status to \'Payment accepted\' and check it', async function () {
        const result = await this.pageObjects.viewOrderPage.modifyOrderStatus(Statuses.paymentAccepted.status);
        await expect(result).to.be.true;
      });

      it('should download the invoice', async function () {
        firstInvoiceFileName = await this.pageObjects.viewOrderPage.getFileName();
        await this.pageObjects.viewOrderPage.downloadInvoice();
        const exist = await files.checkFileExistence(
          global.BO.DOWNLOAD_PATH,
          `${firstInvoiceFileName}.pdf`,
        );
        await expect(exist).to.be.true;
      });

      it('should check the tax breakdown', async () => {
        // Check the existence of the first tax
        let exist = await files.checkTextInPDF(
          global.BO.DOWNLOAD_PATH,
          `${firstInvoiceFileName}.pdf`,
          '10.000 %',
        );
        await expect(exist).to.be.true;
        // Check the existence of the second tax
        exist = await files.checkTextInPDF(
          global.BO.DOWNLOAD_PATH,
          `${firstInvoiceFileName}.pdf`,
          '20.000 %',
        );
        await expect(exist).to.be.true;
        // Delete the invoice file
        files.deleteFile(`${global.BO.DOWNLOAD_PATH}/${firstInvoiceFileName}.pdf`);
      });
    });
  });

  describe('Disable tax breakdown then check the invoice file', async () => {
    describe('Disable tax breakdown', async () => {
      it('should go to invoices page', async function () {
        await this.pageObjects.boBasePage.goToSubMenu(
          this.pageObjects.boBasePage.ordersParentLink,
          this.pageObjects.boBasePage.invoicesLink,
        );
        await this.pageObjects.boBasePage.closeSfToolBar();
        const pageTitle = await this.pageObjects.invoicesPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.invoicesPage.pageTitle);
      });

      it('should disable tax breakdown', async function () {
        await this.pageObjects.invoicesPage.enableTaxBreakdown(false);
        const textMessage = await this.pageObjects.invoicesPage.saveInvoiceOptions();
        await expect(textMessage).to.contains(this.pageObjects.invoicesPage.successfulUpdateMessage);
      });
    });

    describe('Generate the invoice and check that there is no tax breakdown', async () => {
      it('should go to the orders page', async function () {
        await this.pageObjects.boBasePage.goToSubMenu(
          this.pageObjects.boBasePage.ordersParentLink,
          this.pageObjects.boBasePage.ordersLink,
        );
        const pageTitle = await this.pageObjects.ordersPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.ordersPage.pageTitle);
      });

      it('should go to the first order page', async function () {
        await this.pageObjects.ordersPage.goToOrder(1);
        const pageTitle = await this.pageObjects.viewOrderPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.viewOrderPage.pageTitle);
      });

      it('should download the invoice', async function () {
        secondInvoiceFileName = await this.pageObjects.viewOrderPage.getFileName();
        await this.pageObjects.viewOrderPage.downloadInvoice();
        const exist = await files.checkFileExistence(
          global.BO.DOWNLOAD_PATH,
          `${secondInvoiceFileName}.pdf`,
        );
        await expect(exist).to.be.true;
      });

      it('should check that there is no tax breakdown', async () => {
        let exist = await files.checkTextInPDF(
          global.BO.DOWNLOAD_PATH,
          `${secondInvoiceFileName}.pdf`,
          '10.000 %',
        );
        await expect(exist).to.be.false;
        exist = await files.checkTextInPDF(
          global.BO.DOWNLOAD_PATH,
          `${secondInvoiceFileName}.pdf`,
          '20.000 %',
        );
        // Check that there is only one tax line 30.000 %
        await expect(exist).to.be.false;
        exist = await files.checkTextInPDF(
          global.BO.DOWNLOAD_PATH,
          `${secondInvoiceFileName}.pdf`,
          '30.000 %',
        );
        await expect(exist).to.be.true;
      });
    });
  });
});
