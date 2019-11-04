require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
const {Profiles} = require('@data/demo/profile');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const EmployeesPage = require('@pages/BO/advancedParameters/team/employees');
const ProfilesPage = require('@pages/BO/advancedParameters/team/profiles');
const AddProfilePage = require('@pages/BO/advancedParameters/team/addProfile');
const ProductsPage = require('@pages/BO/products');
const OrdersPage = require('@pages/BO/orders');
const FOBasePage = require('@pages/FO/FObasePage');
const ProfileFaker = require('@data/faker/profile');

let browser;
let page;
let numberOfProfiles = 0;
let profileData;

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
  describe('Create profile in BO', async () => {
    it('should filter list by \'Id\'', async function () {
      await this.pageObjects.profilesPage.filterProfiles(
        'input',
        'id_profile',
        Profiles.translator.id,
      );
      const numberOfProfilesAfterFilter = await this.pageObjects.profilesPage.getNumberFromText(
        this.pageObjects.profilesPage.profileGridTitle);
      await expect(numberOfProfilesAfterFilter).to.be.at.most(numberOfProfiles);
      /* eslint-disable no-await-in-loop */
      for (let i = 1; i <= numberOfProfilesAfterFilter; i++) {
        const textName = await this.pageObjects.profilesPage.getTextContent(
          this.pageObjects.profilesPage.profilesListTableColumn.replace('%ROW', i).replace('%COLUMN', 'id_profile'),
        );
        await expect(textName).to.contains(Profiles.translator.id);
      }
      /* eslint-enable no-await-in-loop */
    });

    it('should reset filter', async function () {
      const numberOfProfilesAfterDelete = await this.pageObjects.profilesPage.resetAndGetNumberOfLines();
      await expect(numberOfProfilesAfterDelete).to.be.equal(numberOfProfiles);
    });

    it('should filter list by name', async function () {
      await this.pageObjects.profilesPage.filterProfiles(
        'input',
        'name',
        Profiles.logistician.name,
      );
      const numberOfProfilesAfterFilter = await this.pageObjects.profilesPage.getNumberFromText(
        this.pageObjects.profilesPage.profileGridTitle);
      await expect(numberOfProfilesAfterFilter).to.be.at.most(numberOfProfiles);
      /* eslint-disable no-await-in-loop */
      for (let i = 1; i <= numberOfProfilesAfterFilter; i++) {
        const textName = await this.pageObjects.profilesPage.getTextContent(
          this.pageObjects.profilesPage.profilesListTableColumn.replace('%ROW', i).replace('%COLUMN', 'name'),
        );
        await expect(textName).to.contains(Profiles.logistician.name);
      }
      /* eslint-enable no-await-in-loop */
    });

    it('should reset filter', async function () {
      const numberOfProfilesAfterDelete = await this.pageObjects.profilesPage.resetAndGetNumberOfLines();
      await expect(numberOfProfilesAfterDelete).to.be.equal(numberOfProfiles);
    });
  });
})
;
