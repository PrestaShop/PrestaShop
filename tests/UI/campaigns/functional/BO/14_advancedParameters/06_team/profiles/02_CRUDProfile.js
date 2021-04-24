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
const addProfilePage = require('@pages/BO/advancedParameters/team/profiles/add');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_advancedParams_team_profiles_CRUDProfile';

let browserContext;
let page;

let numberOfProfiles = 0;

const profileData = new ProfileFaker();
const editProfileData = new ProfileFaker();

// Create, Read, Update and Delete profile in BO
describe('Create, Read, Update and Delete profile in BO', async () => {
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

  // 1 : Create profile
  describe('Create profile in BO', async () => {
    it('should go to add new profile page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewProfilePage', baseContext);

      await profilesPage.goToAddNewProfilePage(page);
      const pageTitle = await addProfilePage.getPageTitle(page);
      await expect(pageTitle).to.contains(addProfilePage.pageTitleCreate);
    });

    it('should create profile and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createProfile', baseContext);

      const textResult = await addProfilePage.createEditProfile(page, profileData);
      await expect(textResult).to.equal(profilesPage.successfulCreationMessage);

      const numberOfProfilesAfterCreation = await profilesPage.getNumberOfElementInGrid(page);
      await expect(numberOfProfilesAfterCreation).to.be.equal(numberOfProfiles + 1);
    });
  });

  // 2 : Update profile
  describe('Update profile', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForUpdate', baseContext);

      await profilesPage.filterProfiles(
        page,
        'input',
        'name',
        profileData.name,
      );

      const textName = await profilesPage.getTextColumnFromTable(page, 1, 'name');
      await expect(textName).to.contains(profileData.name);
    });

    it('should go to edit profile page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditProfile', baseContext);

      await profilesPage.goToEditProfilePage(page, 1);
      const pageTitle = await addProfilePage.getPageTitle(page);
      await expect(pageTitle).to.contains(addProfilePage.pageTitleEdit);
    });

    it('should update the profile', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateProfile', baseContext);

      const textResult = await addProfilePage.createEditProfile(page, editProfileData);
      await expect(textResult).to.equal(addProfilePage.successfulUpdateMessage);
    });
  });

  // 3 : Delete profile
  describe('Delete profile', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForDelete', baseContext);

      await profilesPage.filterProfiles(
        page,
        'input',
        'name',
        editProfileData.name,
      );

      const textName = await profilesPage.getTextColumnFromTable(page, 1, 'name');
      await expect(textName).to.contains(editProfileData.name);
    });

    it('should delete profile', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteProfile', baseContext);

      const textResult = await profilesPage.deleteProfile(page, 1);
      await expect(profilesPage.successfulDeleteMessage).to.contains(textResult);
    });

    it('should reset filter and check the number of profiles', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDelete', baseContext);

      const numberOfProfilesAfterDelete = await profilesPage.resetAndGetNumberOfLines(page);
      await expect(numberOfProfilesAfterDelete).to.be.equal(numberOfProfiles);
    });
  });
});
