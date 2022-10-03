require('module-alias/register');
// Import utils
const helper = require('@utils/helpers');

// Import login test
const loginCommon = require('@commonTests/BO/loginBO');

// Import BO pages
const dashboardPage = require('@pages/BO/dashboard');
const employeesPage = require('@pages/BO/advancedParameters/team/index');
const permissionsPage = require('@pages/BO/advancedParameters/team/permissions/index');

// Import test context
const testContext = require('@utils/testContext');

// Import expect from chai
const {expect} = require('chai');

let browserContext;
let page;

/**
 * Function to set the employee permissions
 * @param profileName {string}
 * @param permissionsData {array}
 * @param baseContext {string}
 */
function setPermissions(profileName, permissionsData, baseContext = 'commonTests-setPermissions') {
  describe('PRE-TEST: Set permissions to a profile', async () => {
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

    it('should go to \'Advanced Parameters > Team\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToTeamPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.advancedParametersLink,
        dashboardPage.teamLink,
      );

      const pageTitle = await employeesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(employeesPage.pageTitle);
    });

    it('should go to \'Permissions\' tab', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToPermissionsTab', baseContext);

      const isTabOpened = await employeesPage.goToPermissionsTab(page);
      await expect(isTabOpened, 'Permissions tab is not opened!').to.be.true;
    });

    it('should click on the defined profile', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProfileSubTab', baseContext);

      const isSubTabOpened = await permissionsPage.goToProfileSubTab(page, profileName);
      await expect(isSubTabOpened, 'Profile sub-tab is not opened!').to.be.true;
    });

    permissionsData.forEach((permission) => {
      permission.accesses.forEach((access) => {
        it(`should set the permission ${access} on the ${permission.className}`, async function () {
          await testContext.addContextItem(
            this,
            'testIdentifier',
            `setPermission_${permission.className}_${access}`,
            baseContext,
          );

          const isPermissionDefined = await permissionsPage.setPermission(page, permission.className, access);
          await expect(isPermissionDefined, 'Permission is not updated').to.be.true;
        });
      });
    });
  });
}

module.exports = {setPermissions};
