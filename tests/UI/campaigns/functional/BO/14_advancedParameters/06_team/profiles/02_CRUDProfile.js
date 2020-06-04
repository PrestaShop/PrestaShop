require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import data
const ProfileFaker = require('@data/faker/profile');

// Import pages
const LoginPage = require('@pages/BO/login/index');
const DashboardPage = require('@pages/BO/dashboard/index');
const EmployeesPage = require('@pages/BO/advancedParameters/team/index');
const ProfilesPage = require('@pages/BO/advancedParameters/team/profiles/index');
const AddProfilePage = require('@pages/BO/advancedParameters/team/profiles/add');
const ProductsPage = require('@pages/BO/catalog/products/index');
const OrdersPage = require('@pages/BO/orders/index');
const FOBasePage = require('@pages/FO/FObasePage');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_advancedParams_team_profiles_CRUDProfile';

let browser;
let browserContext;
let page;

let numberOfProfiles = 0;

let profileData;
let editProfileData;

// Init objects needed
const init = async function () {
  return {
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
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    // Init page objects
    this.pageObjects = await init();

    // Init data
    profileData = await (new ProfileFaker());
    editProfileData = await (new ProfileFaker());
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  // Login into BO and go to "Advanced parameters>Team" page
  loginCommon.loginBO();

  it('should go to "Advanced parameters>Team" page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToAdvancedParamsPage', baseContext);

    await this.pageObjects.dashboardPage.goToSubMenu(
      this.pageObjects.dashboardPage.advancedParametersLink,
      this.pageObjects.dashboardPage.teamLink,
    );

    await this.pageObjects.employeesPage.closeSfToolBar();

    const pageTitle = await this.pageObjects.employeesPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.employeesPage.pageTitle);
  });

  it('should go to "Profiles" page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToProfilesPage', baseContext);

    await this.pageObjects.employeesPage.goToProfilesPage();
    const pageTitle = await this.pageObjects.profilesPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.profilesPage.pageTitle);
  });

  it('should reset all filters and get number of profiles', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfProfiles = await this.pageObjects.profilesPage.resetAndGetNumberOfLines();
    await expect(numberOfProfiles).to.be.above(0);
  });

  // 1 : Create profile
  describe('Create profile in BO', async () => {
    it('should go to add new profile page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewProfilePage', baseContext);

      await this.pageObjects.profilesPage.goToAddNewProfilePage();
      const pageTitle = await this.pageObjects.addProfilePage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.addProfilePage.pageTitleCreate);
    });

    it('should create profile and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createProfile', baseContext);

      const textResult = await this.pageObjects.addProfilePage.createEditProfile(profileData);
      await expect(textResult).to.equal(this.pageObjects.profilesPage.successfulCreationMessage);

      const numberOfProfilesAfterCreation = await this.pageObjects.profilesPage.getNumberOfElementInGrid();
      await expect(numberOfProfilesAfterCreation).to.be.equal(numberOfProfiles + 1);
    });
  });

  // 2 : Update profile
  describe('Update profile', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForUpdate', baseContext);

      await this.pageObjects.profilesPage.filterProfiles(
        'input',
        'name',
        profileData.name,
      );

      const textName = await this.pageObjects.profilesPage.getTextColumnFromTable(1, 'name');
      await expect(textName).to.contains(profileData.name);
    });

    it('should go to edit profile page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditProfile', baseContext);

      await this.pageObjects.profilesPage.goToEditProfilePage('1');
      const pageTitle = await this.pageObjects.addProfilePage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.addProfilePage.pageTitleEdit);
    });

    it('should update the profile', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateProfile', baseContext);

      const textResult = await this.pageObjects.addProfilePage.createEditProfile(editProfileData);
      await expect(textResult).to.equal(this.pageObjects.addProfilePage.successfulUpdateMessage);
    });
  });

  // 3 : Delete profile
  describe('Delete profile', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForDelete', baseContext);

      await this.pageObjects.profilesPage.filterProfiles(
        'input',
        'name',
        editProfileData.name,
      );

      const textName = await this.pageObjects.profilesPage.getTextColumnFromTable(1, 'name');
      await expect(textName).to.contains(editProfileData.name);
    });

    it('should delete profile', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteProfile', baseContext);

      const textResult = await this.pageObjects.profilesPage.deleteProfile('1');
      await expect(this.pageObjects.profilesPage.successfulDeleteMessage).to.contains(textResult);
    });

    it('should reset filter and check the number of profiles', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDelete', baseContext);

      const numberOfProfilesAfterDelete = await this.pageObjects.profilesPage.resetAndGetNumberOfLines();
      await expect(numberOfProfilesAfterDelete).to.be.equal(numberOfProfiles);
    });
  });
});
