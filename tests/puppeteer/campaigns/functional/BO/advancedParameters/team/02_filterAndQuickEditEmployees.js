require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
const EmployeeFaker = require('@data/faker/employee');
const {DefaultAccount} = require('@data/demo/employee');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const EmployeesPage = require('@pages/BO/advancedParameters/team/employees');
const AddEmployeePage = require('@pages/BO/advancedParameters/team/addEmployee');
const ProductsPage = require('@pages/BO/products');
const OrdersPage = require('@pages/BO/orders');
const FOBasePage = require('@pages/FO/FObasePage');

let browser;
let page;
let numberOfEmployees = 0;
let createEmployeeData;

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    employeesPage: new EmployeesPage(page),
    addEmployeePage: new AddEmployeePage(page),
    productsPage: new ProductsPage(page),
    ordersPage: new OrdersPage(page),
    foBasePage: new FOBasePage(page),
  };
};

// Filter And Quick Edit Employees in BO
describe('Filter And Quick Edit Employees', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    this.pageObjects = await init();
    createEmployeeData = await (new EmployeeFaker({
      defaultPage: 'Orders',
      permissionProfile: 'Logistician',
      active: false,
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

  it('should reset all filters and get number of employees', async function () {
    numberOfEmployees = await this.pageObjects.employeesPage.resetAndGetNumberOfLines();
    await expect(numberOfEmployees).to.be.above(0);
  });

  // 1 : Create employee and Filter with all inputs and selects in grid table in BO
  describe('Create employee the filter the table', async () => {
    it('should go to add new employee page', async function () {
      await this.pageObjects.employeesPage.goToAddNewEmployeePage();
      const pageTitle = await this.pageObjects.addEmployeePage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.addEmployeePage.pageTitleCreate);
    });

    it('should create employee', async function () {
      const textResult = await this.pageObjects.addEmployeePage.createEditEmployee(createEmployeeData);
      await expect(textResult).to.equal(this.pageObjects.employeesPage.successfulCreationMessage);
    });

    it('should reset all filters', async function () {
      const numberOfEmployeesAfterCreation = await this.pageObjects.employeesPage.resetAndGetNumberOfLines();
      await expect(numberOfEmployeesAfterCreation).to.be.equal(numberOfEmployees + 1);
    });

    it('should filter by Id \'1\'', async function () {
      await this.pageObjects.employeesPage.filterEmployees('input', 'id_employee', '1');
      const numberOfEmployeesAfterFilter = await this.pageObjects.employeesPage.getNumberFromText(
        this.pageObjects.employeesPage.employeeGridTitle,
      );
      await expect(numberOfEmployeesAfterFilter).to.be.equal(1);
      const textColumn = await this.pageObjects.employeesPage.getTextContent(
        this.pageObjects.employeesPage.employeesListTableColumn
          .replace('%ROW', 1)
          .replace('%COLUMN', 'id_employee'),
      );
      await expect(textColumn).to.contains('1');
    });

    it('should reset all filters', async function () {
      const numberOfEmployeesAfterCreation = await this.pageObjects.employeesPage.resetAndGetNumberOfLines();
      await expect(numberOfEmployeesAfterCreation).to.be.equal(numberOfEmployees + 1);
    });

    it('should filter by First name', async function () {
      await this.pageObjects.employeesPage.filterEmployees('input', 'firstname', createEmployeeData.firstName);
      const numberOfEmployeesAfterFilter = await this.pageObjects.employeesPage.getNumberFromText(
        this.pageObjects.employeesPage.employeeGridTitle,
      );
      await expect(numberOfEmployeesAfterFilter).to.be.at.most(numberOfEmployees);
      /* eslint-disable no-await-in-loop */
      for (let i = 1; i <= numberOfEmployeesAfterFilter; i++) {
        const textColumn = await this.pageObjects.employeesPage.getTextContent(
          this.pageObjects.employeesPage.employeesListTableColumn
            .replace('%ROW', i)
            .replace('%COLUMN', 'firstname'),
        );
        await expect(textColumn).to.contains(createEmployeeData.firstName);
      }
      /* eslint-enable no-await-in-loop */
    });

    it('should reset all filters', async function () {
      const numberOfEmployeesAfterCreation = await this.pageObjects.employeesPage.resetAndGetNumberOfLines();
      await expect(numberOfEmployeesAfterCreation).to.be.above(numberOfEmployees);
    });

    it('should filter by Last name', async function () {
      await this.pageObjects.employeesPage.filterEmployees('input', 'lastname', DefaultAccount.lastName);
      const numberOfEmployeesAfterFilter = await this.pageObjects.employeesPage.getNumberFromText(
        this.pageObjects.employeesPage.employeeGridTitle,
      );
      await expect(numberOfEmployeesAfterFilter).to.be.at.most(numberOfEmployees);
      /* eslint-disable no-await-in-loop */
      for (let i = 1; i <= numberOfEmployeesAfterFilter; i++) {
        const textColumn = await this.pageObjects.employeesPage.getTextContent(
          this.pageObjects.employeesPage.employeesListTableColumn
            .replace('%ROW', 1)
            .replace('%COLUMN', 'lastname'),
        );
        await expect(textColumn).to.contains(DefaultAccount.lastName);
      }
      /* eslint-enable no-await-in-loop */
    });

    it('should reset all filters', async function () {
      const numberOfEmployeesAfterCreation = await this.pageObjects.employeesPage.resetAndGetNumberOfLines();
      await expect(numberOfEmployeesAfterCreation).to.be.above(numberOfEmployees);
    });

    it('should filter by Email address', async function () {
      await this.pageObjects.employeesPage.filterEmployees('input', 'email', createEmployeeData.email);
      const numberOfEmployeesAfterFilter = await this.pageObjects.employeesPage.getNumberFromText(
        this.pageObjects.employeesPage.employeeGridTitle,
      );
      await expect(numberOfEmployeesAfterFilter).to.be.at.most(numberOfEmployees);
      /* eslint-disable no-await-in-loop */
      for (let i = 1; i <= numberOfEmployeesAfterFilter; i++) {
        const textColumn = await this.pageObjects.employeesPage.getTextContent(
          this.pageObjects.employeesPage.employeesListTableColumn
            .replace('%ROW', 1)
            .replace('%COLUMN', 'email'),
        );
        await expect(textColumn).to.contains(createEmployeeData.email);
      }
      /* eslint-enable no-await-in-loop */
    });

    it('should reset all filters', async function () {
      const numberOfEmployeesAfterCreation = await this.pageObjects.employeesPage.resetAndGetNumberOfLines();
      await expect(numberOfEmployeesAfterCreation).to.be.above(numberOfEmployees);
    });

    it('should filter by Active \'No\'', async function () {
      await this.pageObjects.employeesPage.filterEmployees('select', 'active', createEmployeeData.active);
      const numberOfEmployeesAfterFilter = await this.pageObjects.employeesPage.getNumberFromText(
        this.pageObjects.employeesPage.employeeGridTitle,
      );
      await expect(numberOfEmployeesAfterFilter).to.be.at.most(numberOfEmployees);
      /* eslint-disable no-await-in-loop */
      for (let i = 1; i <= numberOfEmployeesAfterFilter; i++) {
        const textColumn = await this.pageObjects.employeesPage.getTextContent(
          this.pageObjects.employeesPage.employeesListTableColumn
            .replace('%ROW', 1)
            .replace('%COLUMN', 'active'),
        );
        await expect(textColumn).to.contains('clear');
      }
      /* eslint-enable no-await-in-loop */
    });

    it('should reset all filters', async function () {
      const numberOfEmployeesAfterCreation = await this.pageObjects.employeesPage.resetAndGetNumberOfLines();
      await expect(numberOfEmployeesAfterCreation).to.be.above(numberOfEmployees);
    });
  });

  // 2 : Editing Employees from grid table
  describe('Quick Edit Employees', async () => {
    it('should filter by Email address', async function () {
      await this.pageObjects.employeesPage.filterEmployees('input', 'email', createEmployeeData.email);
      const numberOfEmployeesAfterFilter = await this.pageObjects.employeesPage.getNumberFromText(
        this.pageObjects.employeesPage.employeeGridTitle,
      );
      await expect(numberOfEmployeesAfterFilter).to.be.at.most(numberOfEmployees);
      /* eslint-disable no-await-in-loop */
      for (let i = 1; i <= numberOfEmployeesAfterFilter; i++) {
        const textColumn = await this.pageObjects.employeesPage.getTextContent(
          this.pageObjects.employeesPage.employeesListTableColumn
            .replace('%ROW', 1)
            .replace('%COLUMN', 'email'),
        );
        await expect(textColumn).to.contains(createEmployeeData.email);
      }
      /* eslint-enable no-await-in-loop */
    });

    it('should disable the Page', async function () {
      const isActionPerformed = await this.pageObjects.employeesPage.updateToggleColumnValue('1', false);
      if (isActionPerformed) {
        const resultMessage = await this.pageObjects.employeesPage.getTextContent(
          this.pageObjects.employeesPage.alertSuccessBlockParagraph,
        );
        await expect(resultMessage).to.contains(this.pageObjects.employeesPage.successfulUpdateStatusMessage);
      }
      const isStatusChanged = await this.pageObjects.employeesPage.elementVisible(
        this.pageObjects.employeesPage.employeesListColumnNotValidIcon.replace('%ROW', 1), 100);
      await expect(isStatusChanged).to.be.true;
    });

    it('should enable the Page', async function () {
      const isActionPerformed = await this.pageObjects.employeesPage.updateToggleColumnValue('1');
      if (isActionPerformed) {
        const resultMessage = await this.pageObjects.employeesPage.getTextContent(
          this.pageObjects.employeesPage.alertSuccessBlockParagraph,
        );
        await expect(resultMessage).to.contains(this.pageObjects.employeesPage.successfulUpdateStatusMessage);
      }
      const isStatusChanged = await this.pageObjects.employeesPage.elementVisible(
        this.pageObjects.employeesPage.employeesListColumnValidIcon.replace('%ROW', 1), 100);
      await expect(isStatusChanged).to.be.true;
    });
  });

  // 3 : Delete employee from BO
  describe('Delete employee', async () => {
    it('should filter list by email', async function () {
      await this.pageObjects.employeesPage.filterEmployees(
        'input',
        'email',
        createEmployeeData.email,
      );
      const textEmail = await this.pageObjects.employeesPage.getTextContent(
        this.pageObjects.employeesPage.employeesListTableColumn.replace('%ROW', '1').replace('%COLUMN', 'email'),
      );
      await expect(textEmail).to.contains(createEmployeeData.email);
    });

    it('should delete employee', async function () {
      const textResult = await this.pageObjects.employeesPage.deleteEmployee('1');
      await expect(textResult).to.equal(this.pageObjects.employeesPage.successfulDeleteMessage);
    });

    it('should reset filter and check the number of employees', async function () {
      const numberOfEmployeesAfterDelete = await this.pageObjects.employeesPage.resetAndGetNumberOfLines();
      await expect(numberOfEmployeesAfterDelete).to.be.equal(numberOfEmployees);
    });
  });
});
