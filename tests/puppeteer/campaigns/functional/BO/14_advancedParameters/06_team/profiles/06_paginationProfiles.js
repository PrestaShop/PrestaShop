require('module-alias/register');
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
const ProfileFaker = require('@data/faker/profile');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const EmployeesPage = require('@pages/BO/advancedParameters/team/index');
const ProfilesPage = require('@pages/BO/advancedParameters/team/profiles/index');
const AddProfilePage = require('@pages/BO/advancedParameters/team/profiles/add');
// Test context imports
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_advancedParameters_team_profiles_paginationProfiles';

let browser;
let page;
let numberOfProfiles = 0;
const profileData = new ProfileFaker();

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    employeesPage: new EmployeesPage(page),
    profilesPage: new ProfilesPage(page),
    addProfilePage: new AddProfilePage(page),
  };
};

describe('Profiles pagination', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    this.pageObjects = await init();
  });
  after(async () => {
    await helper.closeBrowser(browser);
  });
  // Login into BO
  loginCommon.loginBO();

  it('should go to \'Advanced parameters>Team\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToAdvancedParamsPage', baseContext);
    await this.pageObjects.boBasePage.goToSubMenu(
      this.pageObjects.boBasePage.advancedParametersLink,
      this.pageObjects.boBasePage.teamLink,
    );
    await this.pageObjects.boBasePage.closeSfToolBar();
    const pageTitle = await this.pageObjects.employeesPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.employeesPage.pageTitle);
  });

  it('should go to \'Profiles\' page', async function () {
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
  // 1 : Create 11 pages
  /* eslint-disable no-loop-func */
  for (let i = 0; i < 11; i++) {
    describe(`Create profile n°${i + 1} in BO`, async () => {
      it('should go to add new profile page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToNewProfilePage${i}`, baseContext);
        await this.pageObjects.profilesPage.goToAddNewProfilePage();
        const pageTitle = await this.pageObjects.addProfilePage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.addProfilePage.pageTitleCreate);
      });

      it('should create profile', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `CreateProfile${i}`, baseContext);
        const textResult = await this.pageObjects.addProfilePage.createEditProfile(profileData);
        await expect(textResult).to.equal(this.pageObjects.profilesPage.successfulCreationMessage);
      });

      it('should check the pages number', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkPagesNumber${i}`, baseContext);
        const numberOfProfilesAfterDelete = await this.pageObjects.profilesPage.resetAndGetNumberOfLines();
        await expect(numberOfProfilesAfterDelete).to.be.equal(numberOfProfiles + 1 + i);
      });
    });
  }
  /* eslint-enable no-loop-func */
  // 2 : Test pagination
  describe('Pagination next and previous', async () => {
    it('should change the item number to 10 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo10', baseContext);
      const paginationNumber = await this.pageObjects.profilesPage.selectPaginationLimit('10');
      expect(paginationNumber).to.contain('(page 1 / 2)');
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext', baseContext);
      const paginationNumber = await this.pageObjects.profilesPage.paginationNext();
      expect(paginationNumber).to.contain('(page 2 / 2)');
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious', baseContext);
      const paginationNumber = await this.pageObjects.profilesPage.paginationPrevious();
      expect(paginationNumber).to.contain('(page 1 / 2)');
    });

    it('should change the item number to 50 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo50', baseContext);
      const paginationNumber = await this.pageObjects.profilesPage.selectPaginationLimit('50');
      expect(paginationNumber).to.contain('(page 1 / 1)');
    });
  });
  // 3 : Delete the 11 profiles with bulk actions
  describe('Delete profiles with Bulk Actions', async () => {
    it('should filter list by Name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForBulkDelete', baseContext);
      await this.pageObjects.profilesPage.filterProfiles(
        'input',
        'name',
        profileData.name,
      );
      const textName = await this.pageObjects.profilesPage.getTextColumnFromTable(1, 'name');
      await expect(textName).to.contains(profileData.name);
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
