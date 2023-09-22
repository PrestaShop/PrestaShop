// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import cartRulesPage from '@pages/BO/catalog/discounts';
import addCartRulePage from '@pages/BO/catalog/discounts/add';

// Import data
import CartRuleData from '@data/faker/cartRule';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_catalog_discounts_cartRules_filterQuickEditAndBulkActionsCartRules';

/*
Create 2 cart rules
Filter cart rules by id, priority, code, quantity, status
Quick edit first cart rule in list
Enable, disable and delete cart rules by bulk actions
 */
describe('BO - Catalog - Discounts : Filter, quick edit and bulk actions cart rules', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfCartRules: number = 0;

  const firstCartRule: CartRuleData = new CartRuleData({
    name: 'todelete1',
    code: '4QABV6I0',
    discountType: 'Percent',
    discountPercent: 20,
  });
  const secondCartRule: CartRuleData = new CartRuleData({
    name: 'todelete2',
    code: '3PAJA674',
    discountType: 'Percent',
    discountPercent: 30,
  });

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

  it('should go to \'Catalog > Discounts\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToDiscountsPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.catalogParentLink,
      dashboardPage.discountsLink,
    );

    const pageTitle = await cartRulesPage.getPageTitle(page);
    expect(pageTitle).to.contains(cartRulesPage.pageTitle);
  });

  it('should reset and get number of cart rules', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFirst', baseContext);

    numberOfCartRules = await cartRulesPage.resetAndGetNumberOfLines(page);
    expect(numberOfCartRules).to.be.at.least(0);
  });

  describe('Create 2 cart rules', async () => {
    [firstCartRule, secondCartRule]
      .forEach((cartRuleToCreate: CartRuleData, index: number) => {
        it('should go to new cart rule page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToNewCartRulePage${index}`, baseContext);

          await cartRulesPage.goToAddNewCartRulesPage(page);

          const pageTitle = await addCartRulePage.getPageTitle(page);
          expect(pageTitle).to.contains(addCartRulePage.pageTitle);
        });

        it('should create new cart rule', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `createCartRule${index}`, baseContext);

          const validationMessage = await addCartRulePage.createEditCartRules(page, cartRuleToCreate);
          expect(validationMessage).to.contains(addCartRulePage.successfulCreationMessage);

          const numberOfCartRulesAfterCreation = await cartRulesPage.getNumberOfElementInGrid(page);
          expect(numberOfCartRulesAfterCreation).to.be.at.most(numberOfCartRules + index + 1);
        });
      });
  });

  describe('Filter cart rules table', async () => {
    const tests = [
      {
        args: {
          testIdentifier: 'filterName', filterType: 'input', filterBy: 'name', filterValue: firstCartRule.name,
        },
      },
      {
        args: {
          testIdentifier: 'filterPriority', filterType: 'input', filterBy: 'priority', filterValue: '1',
        },
      },
      {
        args: {
          testIdentifier: 'filterCode', filterType: 'input', filterBy: 'code', filterValue: firstCartRule.code,
        },
      },
      {
        args: {
          testIdentifier: 'filterQuantity', filterType: 'input', filterBy: 'quantity', filterValue: '1',
        },
      },
      {
        args: {
          testIdentifier: 'filterStatus', filterType: 'select', filterBy: 'active', filterValue: '1',
        },
      },
    ];

    tests.forEach((test) => {
      it(`should filter by ${test.args.filterBy} '${test.args.filterValue}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        await cartRulesPage.filterCartRules(
          page,
          test.args.filterType,
          test.args.filterBy,
          test.args.filterValue,
        );

        const numberOfCartRulesAfterFilter = await cartRulesPage.getNumberOfElementInGrid(page);
        expect(numberOfCartRulesAfterFilter).to.be.at.most(numberOfCartRules + 2);

        for (let row = 1; row <= numberOfCartRulesAfterFilter; row++) {
          if (test.args.filterBy === 'active') {
            const cartRuleStatus = await cartRulesPage.getCartRuleStatus(page, row);
            expect(cartRuleStatus).to.equal(test.args.filterValue === '1');
          } else {
            const textColumn = await cartRulesPage.getTextColumn(
              page,
              row,
              test.args.filterBy,
            );
            expect(textColumn).to.contains(test.args.filterValue);
          }
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);

        const numberOfCartRulesAfterReset = await cartRulesPage.resetAndGetNumberOfLines(page);
        expect(numberOfCartRulesAfterReset).to.equal(numberOfCartRules + 2);
      });
    });
  });

  describe('Quick edit cart rule', async () => {
    it(`should filter by name '${firstCartRule.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToQuickEdit', baseContext);

      await cartRulesPage.filterCartRules(page, 'input', 'name', firstCartRule.name);

      const textColumn = await cartRulesPage.getTextColumn(page, 1, 'name');
      expect(textColumn).to.contains(firstCartRule.name);
    });

    [
      {args: {status: 'disable', enable: false}},
      {args: {status: 'enable', enable: true}},
    ].forEach((status) => {
      it(`should ${status.args.status} the first cart rule`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${status.args.status}CartRule`, baseContext);

        await cartRulesPage.setCartRuleStatus(page, 1, status.args.enable);

        const currentStatus = await cartRulesPage.getCartRuleStatus(page, 1);
        expect(currentStatus).to.be.equal(status.args.enable);
      });
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterQuickEdit', baseContext);

      const numberOfCartRulesAfterReset = await cartRulesPage.resetAndGetNumberOfLines(page);
      expect(numberOfCartRulesAfterReset).to.equal(numberOfCartRules + 2);
    });
  });

  describe('Bulk actions cart rules', async () => {
    it('should filter by name \'todelete\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForBulkActions', baseContext);

      await cartRulesPage.filterCartRules(
        page,
        'input',
        'name',
        'todelete',
      );

      const numberOfCartRulesAfterFilter = await cartRulesPage.getNumberOfElementInGrid(page);
      expect(numberOfCartRulesAfterFilter).to.be.at.most(numberOfCartRules + 2);

      for (let row = 1; row <= numberOfCartRulesAfterFilter; row++) {
        const textColumn = await cartRulesPage.getTextColumn(page, row, 'name');
        expect(textColumn).to.contains('todelete');
      }
    });

    [
      {action: 'enable', wantedStatus: true},
      {action: 'disable', wantedStatus: false},
    ].forEach((test) => {
      it(`should ${test.action} cart rules with bulk actions`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.action}CartRules`, baseContext);

        await cartRulesPage.bulkSetStatus(page, test.wantedStatus);

        const numberOfCartRulesBulkActions = await cartRulesPage.getNumberOfElementInGrid(page);

        for (let row = 1; row <= numberOfCartRulesBulkActions; row++) {
          const rowStatus = await cartRulesPage.getCartRuleStatus(page, row);
          expect(rowStatus).to.equal(test.wantedStatus);
        }
      });
    });

    it('should bulk delete cart rules', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteCartRules', baseContext);

      const deleteTextResult = await cartRulesPage.bulkDeleteCartRules(page);
      expect(deleteTextResult).to.be.contains(cartRulesPage.successfulMultiDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterBulkDelete', baseContext);

      const numberOfCartRulesAfterDelete = await cartRulesPage.resetAndGetNumberOfLines(page);
      expect(numberOfCartRulesAfterDelete).to.equal(numberOfCartRules);
    });
  });
});
