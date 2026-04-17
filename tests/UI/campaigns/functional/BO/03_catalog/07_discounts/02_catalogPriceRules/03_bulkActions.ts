import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boCartRulesPage,
  boCatalogPriceRulesCreatePage,
  boCatalogPriceRulesPage,
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  FakerCatalogPriceRule,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_catalog_discounts_catalogPriceRules_bulkActions';

describe('BO - Catalog price Rules : Bulk actions', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const catalogPriceRuleData0: FakerCatalogPriceRule = new FakerCatalogPriceRule({
    name: 'Deleted 1',
    currency: 'All currencies',
    country: 'All countries',
    group: 'All groups',
    reductionType: 'Amount',
    reductionTax: 'Tax included',
    fromQuantity: 1,
    reduction: 1,
  });
  const catalogPriceRuleData1: FakerCatalogPriceRule = new FakerCatalogPriceRule({
    name: 'Deleted 2',
    currency: 'All currencies',
    country: 'All countries',
    group: 'All groups',
    reductionType: 'Amount',
    reductionTax: 'Tax included',
    fromQuantity: 1,
    reduction: 1,
  });
  const catalogPriceRuleData: FakerCatalogPriceRule[] = [
    catalogPriceRuleData0,
    catalogPriceRuleData1,
  ];

  describe('Bulk actions', async () => {
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

    it('should go to \'Catalog > Discounts\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToDiscountsPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.catalogParentLink,
        boDashboardPage.discountsLink,
      );

      const pageTitle = await boCartRulesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCartRulesPage.pageTitle);
    });

    it('should go to \'Catalog Price Rules\' tab', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCatalogPriceRulesTab', baseContext);

      await boCartRulesPage.goToCatalogPriceRulesTab(page);

      const pageTitle = await boCatalogPriceRulesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCatalogPriceRulesPage.pageTitle);
    });

    catalogPriceRuleData.forEach((catalogPriceRuleDatum: FakerCatalogPriceRule, index: number) => {
      it('should go the page "Add new catalog price rule"', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToNewCatalogPriceRule${index}`, baseContext);

        await boCatalogPriceRulesPage.goToAddNewCatalogPriceRulePage(page);

        const pageTitle = await boCatalogPriceRulesCreatePage.getPageTitle(page);
        expect(pageTitle).to.contains(boCatalogPriceRulesCreatePage.pageTitle);
      });

      it('should create new catalog price rule', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createCatalogPriceRule${index}`, baseContext);

        const validationMessage = await boCatalogPriceRulesCreatePage.setCatalogPriceRule(page, catalogPriceRuleDatum);
        expect(validationMessage).to.contains(boCatalogPriceRulesPage.successfulCreationMessage);
      });
    });

    it('should select all', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkSelectAll', baseContext);

      await boCatalogPriceRulesPage.bulkSelectRows(page, true);

      const numRows = await boCatalogPriceRulesPage.getNumberOfElementInGrid(page);
      expect(numRows).to.equals(catalogPriceRuleData.length);

      const numSelectedRows = await boCatalogPriceRulesPage.getSelectedRowsCount(page);
      expect(numSelectedRows).to.equals(numRows);
    });

    it('should unselect all', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkUnselectAll', baseContext);

      await boCatalogPriceRulesPage.bulkSelectRows(page, false);

      const numRows = await boCatalogPriceRulesPage.getNumberOfElementInGrid(page);
      expect(numRows).to.equals(catalogPriceRuleData.length);

      const numSelectedRows = await boCatalogPriceRulesPage.getSelectedRowsCount(page);
      expect(numSelectedRows).to.equals(0);
    });

    it('should try to bulk delete cart rules', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteCatalogPriceRulesWError', baseContext);

      const deleteTextResult = await boCatalogPriceRulesPage.bulkDeletePriceRules(page, false);
      expect(deleteTextResult).to.be.contains(boCatalogPriceRulesPage.messageMustSelectAtLeastOneElementToDelete);
    });

    it('should select all', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkSelectAllForDelete', baseContext);

      await boCatalogPriceRulesPage.bulkSelectRows(page, true);

      const numRows = await boCatalogPriceRulesPage.getNumberOfElementInGrid(page);
      expect(numRows).to.equals(catalogPriceRuleData.length);

      const numSelectedRows = await boCatalogPriceRulesPage.getSelectedRowsCount(page);
      expect(numSelectedRows).to.equals(numRows);
    });

    it('should bulk delete cart rules', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteCatalogPriceRules', baseContext);

      const deleteTextResult = await boCatalogPriceRulesPage.bulkDeletePriceRules(page, false);
      expect(deleteTextResult).to.be.contains(boCatalogPriceRulesPage.successfulMultiDeleteMessage);
    });
  });
});
