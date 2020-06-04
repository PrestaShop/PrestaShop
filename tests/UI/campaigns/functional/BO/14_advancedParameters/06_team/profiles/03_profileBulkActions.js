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
const AddProfile = require('@pages/BO/advancedParameters/team/profiles/add');
const ProductsPage = require('@pages/BO/catalog/products/index');
const OrdersPage = require('@pages/BO/orders/index');
const FOBasePage = require('@pages/FO/FObasePage');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_advancedParams_team_profiles_profileBulkActions';


let browserContext;
let page;
let numberOfProfiles = 0;

const firstProfileData = new ProfileFaker({name: 'todelete'});
const secondProfileData = new ProfileFaker({name: 'todelete2'});

// Init objects needed
const init = async function () {
  return {
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
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    this.pageObjects = await init();
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

  // 1 : Create two profiles
  describe('Create profiles then filter the table', async () => {
    const profilesToCreate = [firstProfileData, secondProfileData];

    profilesToCreate.forEach((profileToCreate, index) => {
      it('should go to add new profile page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToNewProfilePage${index + 1}`, baseContext);

        await this.pageObjects.profilesPage.goToAddNewProfilePage();
        const pageTitle = await this.pageObjects.addProfile.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.addProfile.pageTitleCreate);
      });

      it('should create profile', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createProfile${index + 1}`, baseContext);

        const textResult = await this.pageObjects.addProfile.createEditProfile(profileToCreate);
        await expect(textResult).to.equal(this.pageObjects.profilesPage.successfulCreationMessage);

        const numberOfProfilesAfterCreation = await this.pageObjects.profilesPage.getNumberOfElementInGrid();
        await expect(numberOfProfilesAfterCreation).to.be.equal(numberOfProfiles + index + 1);
      });
    });
  });

  // 2 : Delete profile with bulk actions
  describe('Delete profiles with Bulk Actions', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'FilterForDelete', baseContext);

      await this.pageObjects.profilesPage.filterProfiles(
        'input',
        'name',
        firstProfileData.name,
      );

      const textName = await this.pageObjects.profilesPage.getTextColumnFromTable(1, 'name');
      await expect(textName).to.contains(firstProfileData.name);
    });

    it('should delete profiles with Bulk Actions and check Result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteProfile', baseContext);

      const deleteTextResult = await this.pageObjects.profilesPage.deleteBulkActions();
      await expect(this.pageObjects.profilesPage.successfulDeleteMessage).to.be.contains(deleteTextResult);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDelete', baseContext);

      const numberOfProfilesAfterDelete = await this.pageObjects.profilesPage.resetAndGetNumberOfLines();
      await expect(numberOfProfilesAfterDelete).to.be.equal(numberOfProfiles);
    });
  });
});
