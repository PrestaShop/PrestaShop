require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
const TaxRulesGroup = require('@data/faker/taxRulesGroup');
const {Statuses} = require('@data/demo/orders');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const InvoicesPage = require('@pages/BO/orders/invoices/index');
const TaxRulesPage = require('@pages/BO/international/taxes/taxRules/index');
const AddTaxRulesPage = require('@pages/BO/international/taxes/taxRules/add');
const OrdersPage = require('@pages/BO/orders/index');
const ViewOrderPage = require('@pages/BO/orders/view');

let browser;
let page;
let taxRulesGroupToCreate;

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    invoicesPage: new InvoicesPage(page),
    taxRulesPage: new TaxRulesPage(page),
    addTaxRulesPage: new AddTaxRulesPage(page),
    ordersPage: new OrdersPage(page),
    viewOrderPage: new ViewOrderPage(page),
  };
};

// enable/disable tax breakdown
describe('Test enable/disable tax breakdown', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    await helper.setDownloadBehavior(page);
    this.pageObjects = await init();
    taxRulesGroupToCreate = await (new TaxRulesGroup());
  });
  after(async () => {
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

      it('should enable Tax Breakdown', async function () {
        await this.pageObjects.invoicesPage.enableTaXBreakdown(true);
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
        await expect(pageTitle).to.contains(this.pageObjects.addTaxRulesPage.pageTitle);
      });

      it('should create new tax rules group', async function () {
        await this.pageObjects.taxRulesPage.createEditTaxRulesGroup(taxRulesGroupToCreate);
        const pageTitle = await this.pageObjects.addTaxRulesPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.addTaxRulesPage.pageTitle);
      });

      it('should create new tax rules', async function () {
        const textResult = await this.pageObjects.taxRulesPage.createEditTaxRulesGroup(taxRulesGroupToCreate);
        await expect(textResult).to.equal(this.pageObjects.taxRulesPage.successfulCreationMessage);
      });
    });

    /*it('should go to the orders page', async function () {
      await this.pageObjects.boBasePage.goToSubMenu(
        this.pageObjects.boBasePage.ordersParentLink,
        this.pageObjects.boBasePage.ordersLink,
      );
      const pageTitle = await this.pageObjects.ordersPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.ordersPage.pageTitle);
    });*/
  });
});
