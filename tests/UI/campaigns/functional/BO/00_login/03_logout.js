require('module-alias/register');
// const {BObasePage} = require('@pages/BO/BObasePage');


// Import utils
const helper = require('@utils/helpers');

// Import expect from chai
const {expect} = require('chai');

// Import pages
const loginPage = require('@pages/BO/login/index');
const dashboardPage = require('@pages/BO/dashboard');

// Import login steps
const loginCommon = require('@commonTests/BO/loginBO');

// Import data
const {DefaultEmployee} = require('@data/demo/employees');
const EmployeeFaker = require('@data/faker/employee');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_login_login';

let browserContext;
let page;

const employeeData = new EmployeeFaker({password: '123456789'});

describe('BO - logout : log out from BO', async () => {
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


    it('should log out from BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'logoutFromBOPage', baseContext);
    
        await loginCommon.logoutBO(this, page);

        const pageTitle = await loginPage.getPageTitle(page);
        await expect(pageTitle).to.contains(loginPage.pageTitle);
    });
  
});
