// Import utils
import helper from '@utils/helpers';
import mailHelper from '@utils/mailHelper';

// Import test context
import testContext from '@utils/testContext';

require('module-alias/register');
// Import common tests
const loginCommon = require('@commonTests/BO/loginBO');
const {setupSmtpConfigTest, resetSmtpConfigTest} = require('@commonTests/BO/advancedParameters/configSMTP');

// Import pages
const loginPage = require('@pages/BO/login/index');
const dashboardPage = require('@pages/BO/dashboard');
const employeesPage = require('@pages/BO/advancedParameters/team/index');
const addEmployeePage = require('@pages/BO/advancedParameters/team/add');

// Import data
const EmployeeFaker = require('@data/faker/employee');

const baseContext = 'functional_BO_login_passwordReminder';

// Import expect from chai
const {expect} = require('chai');

let browserContext: any;
let page: any;

/*
Pre-condition
- Lognn to BO
Scenario:
- Logout from BO
 */
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
})