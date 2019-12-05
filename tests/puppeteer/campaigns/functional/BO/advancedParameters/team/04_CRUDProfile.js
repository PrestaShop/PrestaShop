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
const AddProfilePage = require('@pages/BO/advancedParameters/team/profiles/add');
const ProductsPage = require('@pages/BO/catalog/products/index');
const OrdersPage = require('@pages/BO/orders/index');
const FOBasePage = require('@pages/FO/FObasePage');

let browser;
let page;
let numberOfProfiles = 0;
let profileData;
let editProfileData;

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

// Create, Read, Update and Delete profile in BO
describe('Create, Read, Update and Delete profile in BO', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    this.pageObjects = await init();
    profileData = await (new ProfileFaker({
      defaultPage: 'Products',
      language: 'English (English)',
      permissionProfile: 'Salesman',
    }));
    editProfileData = await (new ProfileFaker({
      password: '123456789',
      defaultPage: 'Orders',
      language: 'English (English)',
      permissionProfile: 'Salesman',
    }));
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

  // 1 : Create profile
  describe('Create profile in BO', async () => {
    it('should go to add new profile page', async function () {
      await this.pageObjects.profilesPage.goToAddNewProfilePage();
      const pageTitle = await this.pageObjects.addProfilePage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.addProfilePage.pageTitleCreate);
    });

    it('should create profile and check result', async function () {
      const textResult = await this.pageObjects.addProfilePage.createEditProfile(profileData);
      await expect(textResult).to.equal(this.pageObjects.profilesPage.successfulCreationMessage);
      const numberOfProfilesAfterCreation = await this.pageObjects.profilesPage.getNumberOfElementInGrid();
      await expect(numberOfProfilesAfterCreation).to.be.equal(numberOfProfiles + 1);
    });
  });

  // 2 : Update profile
  describe('Update profile', async () => {
    it('should filter list by name', async function () {
      await this.pageObjects.profilesPage.filterProfiles(
        'input',
        'name',
        profileData.name,
      );
      const textName = await this.pageObjects.profilesPage.getTextColumnFromTable(1, 'name');
      await expect(textName).to.contains(profileData.name);
    });

    it('should go to edit profile page', async function () {
      await this.pageObjects.profilesPage.goToEditProfilePage('1');
      const pageTitle = await this.pageObjects.addProfilePage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.addProfilePage.pageTitleEdit);
    });

    it('should update the profile', async function () {
      const textResult = await this.pageObjects.addProfilePage.createEditProfile(editProfileData);
      await expect(textResult).to.equal(this.pageObjects.addProfilePage.successfulUpdateMessage);
    });
  });

  // 3 : Delete profile
  describe('Delete profile', async () => {
    it('should filter list by email', async function () {
      await this.pageObjects.profilesPage.filterProfiles(
        'input',
        'name',
        editProfileData.name,
      );
      const textName = await this.pageObjects.profilesPage.getTextColumnFromTable(1, 'name');
      await expect(textName).to.contains(editProfileData.name);
    });

    it('should delete profile', async function () {
      const textResult = await this.pageObjects.profilesPage.deleteProfile('1');
      await expect(this.pageObjects.profilesPage.successfulDeleteMessage).to.contains(textResult);
    });

    it('should reset filter and check the number of profiles', async function () {
      const numberOfProfilesAfterDelete = await this.pageObjects.profilesPage.resetAndGetNumberOfLines();
      await expect(numberOfProfilesAfterDelete).to.be.equal(numberOfProfiles);
    });
  });
});
