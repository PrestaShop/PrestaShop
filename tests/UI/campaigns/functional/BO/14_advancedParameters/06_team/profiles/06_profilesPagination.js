require('module-alias/register');

const {expect} = require('chai');

// import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import data
const ProfileFaker = require('@data/faker/profile');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const employeesPage = require('@pages/BO/advancedParameters/team/index');
const profilesPage = require('@pages/BO/advancedParameters/team/profiles/index');
const addProfilePage = require('@pages/BO/advancedParameters/team/profiles/add');

// Test context imports
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_advancedParameters_team_profiles_paginationProfiles';

let browserContext;
let page;
let numberOfProfiles = 0;

const profileData = new ProfileFaker();

describe('Profiles pagination', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  it('should login in BO', async function () {
    await loginCommon.loginBO(this, page);
  });

  it('should go to \'Advanced parameters>Team\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToAdvancedParamsPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.advancedParametersLink,
      dashboardPage.teamLink,
    );

    await employeesPage.closeSfToolBar(page);

    const pageTitle = await employeesPage.getPageTitle(page);
    await expect(pageTitle).to.contains(employeesPage.pageTitle);
  });

  it('should go to \'Profiles\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToProfilesPage', baseContext);

    await employeesPage.goToProfilesPage(page);
    const pageTitle = await profilesPage.getPageTitle(page);
    await expect(pageTitle).to.contains(profilesPage.pageTitle);
  });

  it('should reset all filters and get number of profiles', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfProfiles = await profilesPage.resetAndGetNumberOfLines(page);
    await expect(numberOfProfiles).to.be.above(0);
  });

  // 1 : Create 11 profiles
  const tests = new Array(10).fill(0, 0, 10);

  tests.forEach((test, index) => {
    describe(`Create profile n°${index + 1} in BO`, async () => {
      it('should go to add new profile page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToNewProfilePage${index}`, baseContext);

        await profilesPage.goToAddNewProfilePage(page);
        const pageTitle = await addProfilePage.getPageTitle(page);
        await expect(pageTitle).to.contains(addProfilePage.pageTitleCreate);
      });

      it('should create profile', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `CreateProfile${index}`, baseContext);

        const textResult = await addProfilePage.createEditProfile(page, profileData);
        await expect(textResult).to.equal(profilesPage.successfulCreationMessage);
      });

      it('should check the pages number', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkPagesNumber${index}`, baseContext);

        const numberOfProfilesAfterDelete = await profilesPage.resetAndGetNumberOfLines(page);
        await expect(numberOfProfilesAfterDelete).to.be.equal(numberOfProfiles + 1 + index);
      });
    });
  });

  // 2 : Test pagination
  describe('Pagination next and previous', async () => {
    it('should change the item number to 10 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo10', baseContext);

      const paginationNumber = await profilesPage.selectPaginationLimit(page, '10');
      expect(paginationNumber).to.contain('(page 1 / 2)');
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext', baseContext);

      const paginationNumber = await profilesPage.paginationNext(page);
      expect(paginationNumber).to.contain('(page 2 / 2)');
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious', baseContext);

      const paginationNumber = await profilesPage.paginationPrevious(page);
      expect(paginationNumber).to.contain('(page 1 / 2)');
    });

    it('should change the item number to 50 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo50', baseContext);

      const paginationNumber = await profilesPage.selectPaginationLimit(page, '50');
      expect(paginationNumber).to.contain('(page 1 / 1)');
    });
  });
  // 3 : Delete the 11 profiles with bulk actions
  describe('Delete profiles with Bulk Actions', async () => {
    it('should filter list by Name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForBulkDelete', baseContext);

      await profilesPage.filterProfiles(
        page,
        'input',
        'name',
        profileData.name,
      );

      const textName = await profilesPage.getTextColumnFromTable(page, 1, 'name');
      await expect(textName).to.contains(profileData.name);
    });

    it('should delete profiles with Bulk Actions and check Result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteProfile', baseContext);

      const deleteTextResult = await profilesPage.deleteBulkActions(page);
      await expect(profilesPage.successfulDeleteMessage).to.be.contains(deleteTextResult);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDelete', baseContext);

      const numberOfProfilesAfterDelete = await profilesPage.resetAndGetNumberOfLines(page);
      await expect(numberOfProfilesAfterDelete).to.be.equal(numberOfProfiles);
    });
  });
});
