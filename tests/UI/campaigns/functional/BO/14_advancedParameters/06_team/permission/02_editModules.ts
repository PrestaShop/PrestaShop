import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boDashboardPage,
  boEmployeesPage,
  boLoginPage,
  boPermissionsPage,
  type BrowserContext,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_advancedParameters_team_permission_editModules';

describe('BO - Advanced Parameters - Team - Permission : Edit modules', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('Go to \'Logistician\' profile', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Advanced Parameters > Team\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAdvancedParamsPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.advancedParametersLink,
        boDashboardPage.teamLink,
      );
      await boEmployeesPage.closeSfToolBar(page);

      const pageTitle = await boEmployeesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boEmployeesPage.pageTitle);
    });

    it('should go to \'Permissions\' tab', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToPermissionsTab', baseContext);

      const isTabOpened = await boEmployeesPage.goToPermissionsTab(page);
      expect(isTabOpened, 'Permissions tab is not opened!').to.eq(true);
    });

    it('should click on \'Logistician\' profile', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProfileSubTab', baseContext);

      const isSubTabOpened = await boPermissionsPage.goToProfileSubTab(page, 'logistician');
      expect(isSubTabOpened, 'Profile sub-tab is not opened!').to.eq(true);
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

        const isPermissionDefined = await boPermissionsPage.setPermissionOnAllModules(page, test.args.action);
        expect(isPermissionDefined, 'Permission is not updated').to.eq(true);
      });

      it(`should check that everything in '${test.args.action}' permission is checked`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkAllCheckboxForBlock${index}`, baseContext);

        const isBulkPermissionPerformed = await boPermissionsPage.isAllPermissionPerformed(page, test.args.action);
        expect(isBulkPermissionPerformed).to.eq(true);
      });
    });
  });

  describe('Refresh the page and check result', async () => {
    it('should refresh the page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'refreshPage', baseContext);

      await boPermissionsPage.reloadPage(page);

      const isSubTabOpened = await boPermissionsPage.goToProfileSubTab(page, 'logistician');
      expect(isSubTabOpened, 'Profile sub-tab is not opened!').to.eq(true);
    });

    [
      {args: {action: 'view'}},
      {args: {action: 'configure'}},
      {args: {action: 'uninstall'}},
    ].forEach((test, index: number) => {
      it(`should check that '${test.args.action}' permission is checked for all menu`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkAfterRefreshPage${index}`, baseContext);

        const numberOfModulesUnchecked = await boPermissionsPage.getNumberOfModulesUnChecked(page, test.args.action);
        expect(numberOfModulesUnchecked).to.eq(0);
      });
    });
  });
});
