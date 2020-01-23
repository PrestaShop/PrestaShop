require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login/index');
const DashboardPage = require('@pages/BO/dashboard/index');
const EmployeesPage = require('@pages/BO/advancedParameters/team/index');
const ProfilesPage = require('@pages/BO/advancedParameters/team/profiles/index');
const AddProfilePage = require('@pages/BO/advancedParameters/team/profiles/add');
const ProductsPage = require('@pages/BO/catalog/products/index');
const OrdersPage = require('@pages/BO/orders/index');
const FOBasePage = require('@pages/FO/FObasePage');

let browser;
let page;
let numberOfProfiles = 0;

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    employeesPage: new EmployeesPage(page),
    profilesPage: new ProfilesPage(page),
    addProfilePage: new AddProfilePage(page),
    productsPage: new ProductsPage(page),
    ordersPage: new OrdersPage(page),
    foBasePage: new FOBasePage(page),
  };
};

describe('Filter profiles', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    this.pageObjects = await init();
  });
  after(async () => {
    await helper.closeBrowser(browser);
  });

  // Login into BO and go to "Advanced parameters>Team" page
  loginCommon.loginBO();

  it('should go to "Advanced parameters>Team" page', async function () {
    await this.pageObjects.boBasePage.goToSubMenu(
      this.pageObjects.boBasePage.advancedParametersLink,
      this.pageObjects.boBasePage.teamLink,
    );
    await this.pageObjects.boBasePage.closeSfToolBar();
    const pageTitle = await this.pageObjects.employeesPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.employeesPage.pageTitle);
  });

  it('should go to "Profiles" page', async function () {
    await this.pageObjects.employeesPage.goToProfilesPage();
    const pageTitle = await this.pageObjects.profilesPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.profilesPage.pageTitle);
  });

  it('should reset all filters and get number of profiles', async function () {
    numberOfProfiles = await this.pageObjects.profilesPage.resetAndGetNumberOfLines();
    await expect(numberOfProfiles).to.be.above(0);
  });

  // 1 : Filter profiles table
  describe('Filter profile in BO', async () => {
    const tests = [
      {args: {filterType: 'input', filterBy: 'id_profile', filterValue: 4}},
      {args: {filterType: 'input', filterBy: 'name', filterValue: 'Logistician'}},
    ];
    tests.forEach((test) => {
      it(`should filter list by ${test.args.filterBy}`, async function () {
        await this.pageObjects.profilesPage.filterProfiles(
          test.args.filterType,
          test.args.filterBy,
          test.args.filterValue,
        );
        const numberOfProfilesAfterFilter = await this.pageObjects.profilesPage.getNumberOfElementInGrid();
        await expect(numberOfProfilesAfterFilter).to.be.at.most(numberOfProfiles);
        for (let i = 1; i <= numberOfProfilesAfterFilter; i++) {
          const textName = await this.pageObjects.profilesPage.getTextColumnFromTable(i, test.args.filterBy);
          await expect(textName).to.contains(test.args.filterValue);
        }
      });

      it('should reset filter', async function () {
        const numberOfProfilesAfterDelete = await this.pageObjects.profilesPage.resetAndGetNumberOfLines();
        await expect(numberOfProfilesAfterDelete).to.be.equal(numberOfProfiles);
      });
    });
  });
});
