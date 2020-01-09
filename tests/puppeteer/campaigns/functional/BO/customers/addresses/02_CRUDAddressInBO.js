require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const AddressesPage = require('@pages/BO/customers/addresses');
const AddAddressPage = require('@pages/BO/customers/addresses/add');
// Importing data
const AddressFaker = require('@data/faker/address');

let browser;
let page;
let numberOfAddresses = 0;
const createAddressData = new AddressFaker({email: 'pub@prestashop.com'});
const editAddressData = new AddressFaker({email: ' '});

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    addressesPage: new AddressesPage(page),
    addAddressPage: new AddAddressPage(page),
  };
};

// Create, Read, Update and Delete address in BO
describe('Create, Read, Update and Delete address in BO', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    this.pageObjects = await init();
  });
  after(async () => {
    await helper.closeBrowser(browser);
  });
  // Login into BO and go to addresses page
  loginCommon.loginBO();

  it('should go to \'Customers>Addresses\' page', async function () {
    await this.pageObjects.boBasePage.goToSubMenu(
      this.pageObjects.boBasePage.customersParentLink,
      this.pageObjects.boBasePage.addressesLink,
    );
    await this.pageObjects.boBasePage.closeSfToolBar();
    const pageTitle = await this.pageObjects.addressesPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.addressesPage.pageTitle);
  });

  it('should reset all filters', async function () {
    numberOfAddresses = await this.pageObjects.addressesPage.resetAndGetNumberOfLines();
    await expect(numberOfAddresses).to.be.above(0);
  });
  // 1 : Create address
  describe('Create address in BO', async () => {
    it('should go to add new address page', async function () {
      await this.pageObjects.addressesPage.goToAddNewAddressPage();
      const pageTitle = await this.pageObjects.addAddressPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.addAddressPage.pageTitleCreate);
    });

    it('should create address and check result', async function () {
      const textResult = await this.pageObjects.addAddressPage.createEditAddress(createAddressData);
      await expect(textResult).to.equal(this.pageObjects.addressesPage.successfulCreationMessage);
      const numberOfAddressesAfterCreation = await this.pageObjects.addressesPage.getNumberOfElementInGrid();
      await expect(numberOfAddressesAfterCreation).to.be.equal(numberOfAddresses + 1);
    });
  });
  // 2 : Update address
  describe('Update address Created', async () => {
    it('should go to \'Customers>Addresses\' page', async function () {
      await this.pageObjects.boBasePage.goToSubMenu(
        this.pageObjects.boBasePage.customersParentLink,
        this.pageObjects.boBasePage.addressesLink,
      );
      const pageTitle = await this.pageObjects.addressesPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.addressesPage.pageTitle);
    });

    it('should filter list by first name and last name', async function () {
      await this.pageObjects.addressesPage.resetFilter();
      await this.pageObjects.addressesPage.filterAddresses(
        'input',
        'firstname',
        createAddressData.firstName,
      );
      await this.pageObjects.addressesPage.filterAddresses(
        'input',
        'lastname',
        createAddressData.lastName,
      );
      const firstName = await this.pageObjects.addressesPage.getTextColumnFromTableAddresses(1, 'firstname');
      await expect(firstName).to.contains(createAddressData.firstName);
      const lastName = await this.pageObjects.addressesPage.getTextColumnFromTableAddresses(1, 'lastname');
      await expect(lastName).to.contains(createAddressData.lastName);
    });

    it('should go to edit address page', async function () {
      await this.pageObjects.addressesPage.goToEditAddressPage(1);
      const pageTitle = await this.pageObjects.addAddressPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.addAddressPage.pageTitleEdit);
    });

    it('should update address', async function () {
      const textResult = await this.pageObjects.addAddressPage.createEditAddress(editAddressData);
      await expect(textResult).to.equal(this.pageObjects.addressesPage.successfulUpdateMessage);
      const numberOfAddressesAfterUpdate = await this.pageObjects.addressesPage.resetAndGetNumberOfLines();
      await expect(numberOfAddressesAfterUpdate).to.be.equal(numberOfAddresses + 1);
    });
  });
  // 3 : Delete address from BO
  describe('Delete address', async () => {
    it('should go to \'Customers>Addresses\' page', async function () {
      await this.pageObjects.boBasePage.goToSubMenu(
        this.pageObjects.boBasePage.customersParentLink,
        this.pageObjects.boBasePage.addressesLink,
      );
      const pageTitle = await this.pageObjects.addressesPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.addressesPage.pageTitle);
    });

    it('should filter list by first name and last name', async function () {
      await this.pageObjects.addressesPage.resetFilter();
      await this.pageObjects.addressesPage.filterAddresses(
        'input',
        'firstname',
        editAddressData.firstName,
      );
      await this.pageObjects.addressesPage.filterAddresses(
        'input',
        'lastname',
        editAddressData.lastName,
      );
      const firstName = await this.pageObjects.addressesPage.getTextColumnFromTableAddresses(1, 'firstname');
      await expect(firstName).to.contains(editAddressData.firstName);
      const lastName = await this.pageObjects.addressesPage.getTextColumnFromTableAddresses(1, 'lastname');
      await expect(lastName).to.contains(editAddressData.lastName);
    });

    it('should delete address', async function () {
      const textResult = await this.pageObjects.addressesPage.deleteAddress('1');
      await expect(textResult).to.equal(this.pageObjects.addressesPage.successfulDeleteMessage);
      const numberOfAddressesAfterDelete = await this.pageObjects.addressesPage.resetAndGetNumberOfLines();
      await expect(numberOfAddressesAfterDelete).to.be.equal(numberOfAddresses);
    });
  });
});
