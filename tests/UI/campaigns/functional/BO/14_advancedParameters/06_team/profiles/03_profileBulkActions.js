require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import data
const ProfileFaker = require('@data/faker/profile');

// Import pages
const dashboardPage = require('@pages/BO/dashboard/index');
const employeesPage = require('@pages/BO/advancedParameters/team/index');
const profilesPage = require('@pages/BO/advancedParameters/team/profiles/index');
const addProfile = require('@pages/BO/advancedParameters/team/profiles/add');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_advancedParams_team_profiles_profileBulkActions';

let browserContext;
let page;
let numberOfProfiles = 0;

const firstProfileData = new ProfileFaker({name: 'todelete'});
const secondProfileData = new ProfileFaker({name: 'todelete2'});

// Create profiles, Then Delete with Bulk actions
describe('Create profiles then Delete with Bulk actions', async () => {
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

  it('should go to "Advanced parameters>Team" page', async function () {
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

  it('should go to "Profiles" page', async function () {
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

  // 1 : Create two profiles
  describe('Create profiles then filter the table', async () => {
    const profilesToCreate = [firstProfileData, secondProfileData];

    profilesToCreate.forEach((profileToCreate, index) => {
      it('should go to add new profile page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToNewProfilePage${index + 1}`, baseContext);

        await profilesPage.goToAddNewProfilePage(page);
        const pageTitle = await addProfile.getPageTitle(page);
        await expect(pageTitle).to.contains(addProfile.pageTitleCreate);
      });

      it('should create profile', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createProfile${index + 1}`, baseContext);

        const textResult = await addProfile.createEditProfile(page, profileToCreate);
        await expect(textResult).to.equal(profilesPage.successfulCreationMessage);

        const numberOfProfilesAfterCreation = await profilesPage.getNumberOfElementInGrid(page);
        await expect(numberOfProfilesAfterCreation).to.be.equal(numberOfProfiles + index + 1);
      });
    });
  });

  // 2 : Delete profile with bulk actions
  describe('Delete profiles with Bulk Actions', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'FilterForDelete', baseContext);

      await profilesPage.filterProfiles(
        page,
        'input',
        'name',
        firstProfileData.name,
      );

      const textName = await profilesPage.getTextColumnFromTable(page, 1, 'name');
      await expect(textName).to.contains(firstProfileData.name);
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
