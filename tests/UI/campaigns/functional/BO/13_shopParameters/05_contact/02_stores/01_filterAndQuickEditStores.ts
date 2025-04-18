import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boContactsPage,
  boDashboardPage,
  boLoginPage,
  boStoresPage,
  type BrowserContext,
  dataStores,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_shopParameters_contact_stores_filterAndQuickEditStores';

describe('BO - Shop Parameters - Contact : Filter and quick edit stores', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfStores: number = 0;

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

  it('should go to \'Shop Parameters > Contact\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToContactPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.shopParametersParentLink,
      boDashboardPage.contactLink,
    );
    await boContactsPage.closeSfToolBar(page);

    const pageTitle = await boContactsPage.getPageTitle(page);
    expect(pageTitle).to.contains(boContactsPage.pageTitle);
  });

  it('should go to \'Stores\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToStoresPage', baseContext);

    await boContactsPage.goToStoresPage(page);

    const pageTitle = await boStoresPage.getPageTitle(page);
    expect(pageTitle).to.contains(boStoresPage.pageTitle);
  });

  it('should reset all filters and get number of stores in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfStores = await boStoresPage.resetAndGetNumberOfLines(page);
    expect(numberOfStores).to.be.above(0);
  });

  describe('Filter stores', async () => {
    const tests = [
      {
        args:
          {
            column: 'Id',
            filterType: 'input',
            filterBy: 'id_store',
            filterValue: dataStores.store_1.id.toString(),
          },
      },
      {
        args:
          {
            column: 'Name',
            filterType: 'input',
            filterBy: 'sl!name',
            filterValue: dataStores.store_1.name,
          },
      },
      {
        args:
          {
            column: 'Address',
            filterType: 'input',
            filterBy: 'sl!address1',
            filterValue: dataStores.store_1.address1,
          },
      },
      {
        args:
          {
            column: 'City',
            filterType: 'input',
            filterBy: 'city',
            filterValue: dataStores.store_1.city,
          },
      },
      {
        args:
          {
            column: 'PostCode',
            filterType: 'input',
            filterBy: 'postcode',
            filterValue: dataStores.store_1.postcode,
          },
      },
      {
        args:
          {
            column: 'State',
            filterType: 'input',
            filterBy: 'st!name',
            filterValue: dataStores.store_1.state,
          },
      },
      {
        args:
          {
            column: 'country',
            filterType: 'input',
            filterBy: 'cl!name',
            filterValue: dataStores.store_1.country,
          },
      },
      {
        args:
          {
            column: 'Phone',
            filterType: 'input',
            filterBy: 'phone',
            filterValue: dataStores.store_1.phone,
          },
      },
      {
        args:
          {
            column: 'Fax',
            filterType: 'input',
            filterBy: 'fax',
            filterValue: dataStores.store_1.fax,
          },
      },
      {
        args:
          {
            column: 'Status',
            filterType: 'select',
            filterBy: 'active',
            filterValue: dataStores.store_1.status ? '1' : '0',
          },
      },
    ];

    tests.forEach((test) => {
      it(`should filter by ${test.args.column} '${test.args.filterValue}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `filter${test.args.column}`, baseContext);

        await boStoresPage.filterTable(
          page,
          test.args.filterType,
          test.args.filterBy,
          test.args.filterValue,
        );

        const numberOfStoresAfterFilter = await boStoresPage.getNumberOfElementInGrid(page);
        expect(numberOfStoresAfterFilter).to.be.at.most(numberOfStores);

        for (let row = 1; row <= numberOfStoresAfterFilter; row++) {
          if (test.args.column === 'Status') {
            const storeStatus = await boStoresPage.getStoreStatus(page, row);
            expect(storeStatus).to.equal(test.args.filterValue === '1');
          } else {
            const textColumn = await boStoresPage.getTextColumn(page, row, test.args.filterBy);
            expect(textColumn).to.contains(test.args.filterValue);
          }
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `resetFilter${test.args.column}`, baseContext);

        const numberOfStoresAfterReset = await boStoresPage.resetAndGetNumberOfLines(page);
        expect(numberOfStoresAfterReset).to.equal(numberOfStores);
      });
    });
  });

  describe('Quick edit stores', async () => {
    it(`should filter by name '${dataStores.store_2.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToQuickEdit', baseContext);

      await boStoresPage.filterTable(page, 'input', 'sl!name', dataStores.store_2.name);

      const numberOfStoresAfterFilter = await boStoresPage.getNumberOfElementInGrid(page);
      expect(numberOfStoresAfterFilter).to.be.at.most(numberOfStores);

      const textColumn = await boStoresPage.getTextColumn(page, 1, 'sl!name');
      expect(textColumn).to.contains(dataStores.store_2.name);
    });

    const tests = [
      {args: {action: 'disable', statusWanted: false}},
      {args: {action: 'enable', statusWanted: true}},
    ];

    tests.forEach((test) => {
      it(`should ${test.args.action} first element in grid`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}Store`, baseContext);

        await boStoresPage.setStoreStatus(page, 1, test.args.statusWanted);

        const storeStatus = await boStoresPage.getStoreStatus(page, 1);
        expect(storeStatus).to.equal(test.args.statusWanted);
      });
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterQuickEdit', baseContext);

      const numberOfStoresAfterReset = await boStoresPage.resetAndGetNumberOfLines(page);
      expect(numberOfStoresAfterReset).to.equal(numberOfStores);
    });
  });
});
