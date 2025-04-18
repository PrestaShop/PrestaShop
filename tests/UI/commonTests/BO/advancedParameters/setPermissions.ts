import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boDashboardPage,
  boEmployeesPage,
  boLoginPage,
  boPermissionsPage,
  type BrowserContext,
  type EmployeePermission,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

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
      browserContext = await utilsPlaywright.createBrowserContext(this.browser);
      page = await utilsPlaywright.newTab(browserContext);
    });

    after(async () => {
      await utilsPlaywright.closeBrowserContext(browserContext);
    });

    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Advanced Parameters > Team\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToTeamPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.advancedParametersLink,
        boDashboardPage.teamLink,
      );

      const pageTitle = await boEmployeesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boEmployeesPage.pageTitle);
    });

    it('should go to \'Permissions\' tab', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToPermissionsTab', baseContext);

      const isTabOpened = await boEmployeesPage.goToPermissionsTab(page);
      expect(isTabOpened, 'Permissions tab is not opened!').to.eq(true);
    });

    it('should click on the defined profile', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProfileSubTab', baseContext);

      const isSubTabOpened = await boPermissionsPage.goToProfileSubTab(page, profileName);
      expect(isSubTabOpened, 'Profile sub-tab is not opened!').to.eq(true);
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

          const isPermissionDefined = await boPermissionsPage.setPermission(page, permission.className, access);
          expect(isPermissionDefined, 'Permission is not updated').to.eq(true);
        });
      });
    });
  });
}

export default setPermissions;
