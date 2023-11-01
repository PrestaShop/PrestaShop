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

const baseContext: string = 'functional_BO_advancedParameters_team_permission_editMenu';

describe('BO - Advanced Parameters - Team - Permission : Edit menu', async () => {
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
      expect(pageTitle).to.contains(employeesPage.pageTitle);
    });

    it('should go to \'Permissions\' tab', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToPermissionsTab', baseContext);

      const isTabOpened = await employeesPage.goToPermissionsTab(page);
      expect(isTabOpened, 'Permissions tab is not opened!').to.eq(true);
    });

    it('should click on \'Logistician\' profile', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProfileSubTab', baseContext);

      const isSubTabOpened = await permissionsPage.goToProfileSubTab(page, 'logistician');
      expect(isSubTabOpened, 'Profile sub-tab is not opened!').to.eq(true);
    });
  });

  describe('Check all checkboxes for all the menu and check it', async () => {
    it('should check the checkbox \'ALL\' from the header', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkAllCheckbox', baseContext);

      const isPermissionDefined = await permissionsPage.setPermissionOnAllPages(page, 'all');
      expect(isPermissionDefined, 'Permission is not updated').to.eq(true);
    });

    [
      {args: {action: 'view'}},
      {args: {action: 'add'}},
      {args: {action: 'edit'}},
      {args: {action: 'delete'}},
    ].forEach((test) => {
      it(`should check that everything in '${test.args.action}' permission is checked`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `check_${test.args.action}1`, baseContext);

        const isBulkPermissionPerformed = await permissionsPage.isBulkPermissionPerformed(page, test.args.action);
        expect(isBulkPermissionPerformed).to.eq(true);
      });
    });
  });

  describe('Uncheck all checkboxes for all the menu and check it', async () => {
    it('should uncheck the checkbox \'ALL\' from the header', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'uncheckAllCheckbox', baseContext);

      const isPermissionDefined = await permissionsPage.setPermissionOnAllPages(page, 'all', false);
      expect(isPermissionDefined, 'Permission is not updated').to.eq(true);
    });

    [
      {args: {action: 'view'}},
      {args: {action: 'add'}},
      {args: {action: 'edit'}},
      {args: {action: 'delete'}},
    ].forEach((test) => {
      it(`should check that everything in '${test.args.action}' permission is unchecked`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `unchecked_${test.args.action}1`, baseContext);

        const isBulkPermissionPerformed = await permissionsPage.isBulkPermissionPerformed(page, test.args.action, false);
        expect(isBulkPermissionPerformed).to.eq(false);
      });
    });
  });

  describe('Check checkbox by permission and check it', async () => {
    [
      {args: {action: 'view'}},
      {args: {action: 'add'}},
      {args: {action: 'edit'}},
      {args: {action: 'delete'}},
    ].forEach((test, index: number) => {
      it(`should check '${test.args.action}' checkbox from the header`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkAllCheckbox_${index}`, baseContext);

        const isPermissionDefined = await permissionsPage.setPermissionOnAllPages(page, test.args.action);
        expect(isPermissionDefined, 'Permission is not updated').to.eq(true);
      });

      it(`should check that everything in '${test.args.action}' permission is checked`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkAllCheckboxForpermission${index}`, baseContext);

        const isBulkPermissionPerformed = await permissionsPage.isBulkPermissionPerformed(page, test.args.action);
        expect(isBulkPermissionPerformed).to.eq(true);
      });
    });
  });

  describe('Refresh the page and check result', async () => {
    it('should refresh the page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'refreshPage', baseContext);

      await permissionsPage.reloadPage(page);

      const isSubTabOpened = await permissionsPage.goToProfileSubTab(page, 'logistician');
      expect(isSubTabOpened, 'Profile sub-tab is not opened!').to.eq(true);
    });

    [
      {args: {action: 'view'}},
      {args: {action: 'add'}},
      {args: {action: 'edit'}},
      {args: {action: 'delete'}},
      {args: {action: 'all'}},
    ].forEach((test, index: number) => {
      it(`should check that '${test.args.action}' permission is checked for all menu`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkAfterRefreshPage${index}`, baseContext);

        const number = await permissionsPage.getNumberOfPagesUnChecked(page, test.args.action);
        expect(number).to.eq(1);
      });
    });
  });

  describe('Check all permissions for Orders and check result', async () => {
    it('should uncheck the checkbox \'ALL\' from the header', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'uncheckAll', baseContext);

      await permissionsPage.setPermissionOnAllPages(page, 'all', false);

      const isPermissionDefined = await permissionsPage.setPermissionOnAllPages(page, 'all', false);
      expect(isPermissionDefined, 'Permission is not updated').to.eq(true);
    });

    it('should set the permission \'All\' for \'Orders\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setPermission', baseContext);

      const isPermissionDefined = await permissionsPage.setPermission(page, 'AdminParentOrders', 'all');
      expect(isPermissionDefined, 'Permission is not updated').to.eq(true);
    });

    it('should check the number of checkbox checked', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfCheckbox', baseContext);

      const numberOfCheckBoxes = await permissionsPage.getNumberOfElementInMenu(page);

      const number = await permissionsPage.getNumberOfPagesUnChecked(page, 'all');
      expect(numberOfCheckBoxes - number).to.eq(6);
    });

    [
      {args: {className: 'SELL'}},
      {args: {className: 'AdminParentOrders'}},
      {args: {className: 'AdminCatalog'}},
      {args: {className: 'AdminParentCustomer'}},
      {args: {className: 'AdminParentCustomerThreads'}},
      {args: {className: 'AdminStats'}},
    ].forEach((test, index: number) => {
      it(`should check that the menu '${test.args.className}' is checked`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkMenu${index}`, baseContext);

        const isChecked = await permissionsPage.isPageChecked(page, test.args.className, 'all');
        expect(isChecked).to.eq(true);
      });
    });
  });
});
