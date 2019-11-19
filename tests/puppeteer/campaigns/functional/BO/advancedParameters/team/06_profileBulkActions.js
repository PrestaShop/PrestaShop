require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
const ProfileFaker = require('@data/faker/profile');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login/index');
const DashboardPage = require('@pages/BO/dashboard/index');
const EmployeesPage = require('@pages/BO/advancedParameters/team/index');
const ProfilesPage = require('@pages/BO/advancedParameters/team/profiles/index');
const AddProfile = require('@pages/BO/advancedParameters/team/profiles/add');
const ProductsPage = require('@pages/BO/catalog/products/index');
const OrdersPage = require('@pages/BO/orders/index');
const FOBasePage = require('@pages/FO/FObasePage');

let browser;
let page;
let numberOfProfiles = 0;
const firstProfileData = new ProfileFaker({name: 'todelete'});
const secondProfileData = new ProfileFaker({name: 'todelete2'});

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    employeesPage: new EmployeesPage(page),
    profilesPage: new ProfilesPage(page),
    addProfile: new AddProfile(page),
    productsPage: new ProductsPage(page),
    ordersPage: new OrdersPage(page),
    foBasePage: new FOBasePage(page),
  };
};

// Create profiles, Then Delete with Bulk actions
describe('Create profiles then Delete with Bulk actions', async () => {
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

  // 1 : Create two profiles
  describe('Create profile then filter the table', async () => {
    const profilesToCreate = [firstProfileData, secondProfileData];
    profilesToCreate.forEach((profileToCreate) => {
      it('should go to add new profile page', async function () {
        await this.pageObjects.profilesPage.goToAddNewProfilePage();
        const pageTitle = await this.pageObjects.addProfile.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.addProfile.pageTitleCreate);
      });

      it('should create profile', async function () {
        const textResult = await this.pageObjects.addProfile.createEditProfile(profileToCreate);
        await expect(textResult).to.equal(this.pageObjects.profilesPage.successfulCreationMessage);
        const numberOfProfilesAfterCreation = await this.pageObjects.profilesPage.getNumberOfElementInGrid();
        await expect(numberOfProfilesAfterCreation).to.be.equal(numberOfProfiles + 1);
      });
    });
  });

  // 2 : Delete profile with bulk actions
  describe('Delete profiles with Bulk Actions', async () => {
    it('should filter list by name', async function () {
      await this.pageObjects.profilesPage.filterProfiles(
        'input',
        'name',
        firstProfileData.name,
      );
      const textName = await this.pageObjects.profilesPage.getTextColumnFromTable(1, 'name');
      await expect(textName).to.contains(firstProfileData.name);
    });

    it('should delete profiles with Bulk Actions and check Result', async function () {
      const deleteTextResult = await this.pageObjects.profilesPage.deleteBulkActions();
      await expect(this.pageObjects.profilesPage.successfulDeleteMessage).to.be.contains(deleteTextResult);
    });

    it('should reset all filters', async function () {
      const numberOfProfilesAfterDelete = await this.pageObjects.profilesPage.resetAndGetNumberOfLines();
      await expect(numberOfProfilesAfterDelete).to.be.equal(numberOfProfiles);
    });
  });
});
