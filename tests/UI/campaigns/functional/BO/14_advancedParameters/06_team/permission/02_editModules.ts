// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import employeesPage from '@pages/BO/advancedParameters/team';
import permissionsPage from '@pages/BO/advancedParameters/team/permissions';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_advancedParameters_team_permission_editModules';

describe('BO - Advanced Parameters - Team - Permission : Edit modules', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('Go to \'Logistician\' profile', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Advanced Parameters > Team\' page', async function () {
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

    it('should go to \'Permissions\' tab', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToPermissionsTab', baseContext);

      const isTabOpened = await employeesPage.goToPermissionsTab(page);
      await expect(isTabOpened, 'Permissions tab is not opened!').to.be.true;
    });

    it('should click on \'Logistician\' profile', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProfileSubTab', baseContext);

      const isSubTabOpened = await permissionsPage.goToProfileSubTab(page, 'logistician');
      await expect(isSubTabOpened, 'Profile sub-tab is not opened!').to.be.true;
    });
  });

  describe('Check checkbox by permission for all modules and check it', async () => {
    [
      {args: {action: 'view'}},
      {args: {action: 'configure'}},
      {args: {action: 'uninstall'}},
    ].forEach((test, index: number) => {
      it(`should check '${test.args.action}' checkbox from the header`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkAllCheckbox_${index}`, baseContext);

        const isPermissionDefined = await permissionsPage.setPermissionOnAllModules(page, test.args.action);
        await expect(isPermissionDefined, 'Permission is not updated').to.be.true;
      });

      it(`should check that everything in '${test.args.action}' permission is checked`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkAllCheckboxForBlock${index}`, baseContext);

        const isBulkPermissionPerformed = await permissionsPage.isAllPermissionPerformed(page, test.args.action);
        await expect(isBulkPermissionPerformed).to.be.true;
      });
    });
  });

  describe('Refresh the page and check result', async () => {
    it('should refresh the page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'refreshPage', baseContext);

      await permissionsPage.reloadPage(page);

      const isSubTabOpened = await permissionsPage.goToProfileSubTab(page, 'logistician');
      await expect(isSubTabOpened, 'Profile sub-tab is not opened!').to.be.true;
    });

    [
      {args: {action: 'view'}},
      {args: {action: 'configure'}},
      {args: {action: 'uninstall'}},
    ].forEach((test, index: number) => {
      it(`should check that '${test.args.action}' permission is checked for all menu`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkAfterRefreshPage${index}`, baseContext);

        const numberOfModulesUnchecked = await permissionsPage.getNumberOfModulesUnChecked(page, test.args.action);
        await expect(numberOfModulesUnchecked).to.eq(0);
      });
    });
  });
});
