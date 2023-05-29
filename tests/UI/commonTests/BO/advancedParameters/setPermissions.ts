// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import employeesPage from '@pages/BO/advancedParameters/team';
import permissionsPage from '@pages/BO/advancedParameters/team/permissions';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import {EmployeePermission} from '@data/types/employee';

let browserContext: BrowserContext;
let page: Page;

/**
 * Function to set the employee permissions
 * @param profileName {string}
 * @param permissionsData {EmployeePermission[]}
 * @param baseContext {string}
 */
function setPermissions(
  profileName: string,
  permissionsData: EmployeePermission[],
  baseContext: string = 'commonTests-setPermissions',
): void {
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

    permissionsData.forEach((permission: EmployeePermission) => {
      permission.accesses.forEach((access: string) => {
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

export default setPermissions;
