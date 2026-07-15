import {expect} from 'chai';
import {requestAccessToken} from '@commonTests/BO/advancedParameters/authServer';
import testContext from '@utils/testContext';

import {
  type APIRequestContext,
  boCustomerGroupsCreatePage,
  boCustomerGroupsPage,
  boCustomerSettingsPage,
  boDashboardPage, boLoginPage,
  type BrowserContext,
  dataLanguages,
  FakerGroup,
  type Page,
  utilsAPI,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_API_endpoints_customerGroup_postCustomersGroup';

describe('API : POST /customers/groups', async () => {
  let apiContext: APIRequestContext;
  let browserContext: BrowserContext;
  let page: Page;
  let accessToken: string;
  let jsonResponse: any;
  let idCustomerGroup: number;
  let numberOfGroups: number;

  const clientScope: string = 'customer_group_write';
  const createGroup: FakerGroup = new FakerGroup();

  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);

    apiContext = await utilsPlaywright.createAPIContext(global.API.URL);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('API : Fetch the access token', async () => {
    it(`should request the endpoint /access_token with scope ${clientScope}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestOauth2Token', baseContext);
      accessToken = await requestAccessToken(clientScope);
    });
  });

  describe('API : Create the Customer Group', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should request the endpoint /customers/groups', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestEndpoint', baseContext);

      const apiResponse = await apiContext.post('customers/groups', {
        headers: {
          Authorization: `Bearer ${accessToken}`,
        },
        data: {
          localizedNames: {
            [dataLanguages.french.locale]: createGroup.frName,
            [dataLanguages.english.locale]: createGroup.name,
          },
          reductionPercent: createGroup.discount,
          displayPriceTaxExcluded: createGroup.priceDisplayMethod === 'Tax excluded',
          showPrice: createGroup.shownPrices,
        },
      });
      expect(apiResponse.status()).to.eq(201);
      expect(utilsAPI.hasResponseHeader(apiResponse, 'Content-Type')).to.eq(true);
      expect(utilsAPI.getResponseHeader(apiResponse, 'Content-Type')).to.contains('application/json');

      jsonResponse = await apiResponse.json();
    });

    it('should check the JSON Response keys', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseKeys', baseContext);

      expect(jsonResponse).to.have.all.keys(
        'customerGroupId',
        'localizedNames',
        'reductionPercent',
        'displayPriceTaxExcluded',
        'showPrice',
        'shopIds',
      );
    });

    it('should check the JSON Response', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseJSON', baseContext);

      expect(jsonResponse.customerGroupId).to.be.gt(0);
      expect(jsonResponse.localizedNames).to.deep.equal({
        [dataLanguages.english.locale]: createGroup.name,
        [dataLanguages.french.locale]: createGroup.frName,
      });
      expect(jsonResponse.reductionPercent).to.equal(createGroup.discount);
      expect(jsonResponse.displayPriceTaxExcluded).to.equal(createGroup.priceDisplayMethod === 'Tax excluded');
      expect(jsonResponse.showPrice).to.equal(createGroup.shownPrices);
      expect(jsonResponse.shopIds).to.deep.equal([1]);
    });
  });

  describe('BackOffice : Check the Customer Group is created', async () => {
    it('should go to \'Shop Parameters > Customer Settings\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCustomerSettingsPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.shopParametersParentLink,
        boDashboardPage.customerSettingsLink,
      );
      await boCustomerSettingsPage.closeSfToolBar(page);

      const pageTitle = await boCustomerSettingsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCustomerSettingsPage.pageTitle);
    });

    it('should go to \'Groups\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToGroupsPage', baseContext);

      await boCustomerSettingsPage.goToGroupsPage(page);

      const pageTitle = await boCustomerGroupsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCustomerGroupsPage.pageTitle);
    });

    it('should reset all filters and get number of groups in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilter', baseContext);

      numberOfGroups = await boCustomerGroupsPage.resetAndGetNumberOfLines(page);
      expect(numberOfGroups).to.be.above(0);
    });

    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByName', baseContext);

      await boCustomerGroupsPage.filterTable(page, 'input', 'b!name', createGroup.name);

      const numberOfGroupsAfterCreation = await boCustomerGroupsPage.getNumberOfElementInGrid(page);
      expect(numberOfGroupsAfterCreation).to.be.equal(1);

      idCustomerGroup = parseInt(await boCustomerGroupsPage.getTextColumn(page, 1, 'id_group'), 10);
      expect(idCustomerGroup).to.be.equal(idCustomerGroup);
    });

    it('should edit the customer group', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editCustomerGroup', baseContext);

      await boCustomerGroupsPage.gotoEditGroupPage(page, 1);

      const pageTitle = await boCustomerGroupsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boCustomerGroupsCreatePage.pageTitleEdit);
    });

    it('should check the JSON Response : `customerGroupId`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseCustomerGroupId', baseContext);

      expect(jsonResponse.customerGroupId).to.be.equal(idCustomerGroup);
    });

    it('should check the JSON Response : `localizedNames` (EN)', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseLocalizedNamesEN', baseContext);

      const value = await boCustomerGroupsCreatePage.getValue(page, 'localizedNames', dataLanguages.english.id);
      expect(jsonResponse.localizedNames[dataLanguages.english.locale]).to.be.equal(value);
    });

    it('should check the JSON Response : `localizedNames` (FR)', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseLocalizedNamesFR', baseContext);

      const value = await boCustomerGroupsCreatePage.getValue(page, 'localizedNames', dataLanguages.french.id);
      expect(jsonResponse.localizedNames[dataLanguages.french.locale]).to.be.equal(value);
    });

    it('should check the JSON Response : `reductionPercent`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseReductionPercent', baseContext);

      const value = parseInt(await boCustomerGroupsCreatePage.getValue(page, 'reductionPercent'), 10);
      expect(jsonResponse.reductionPercent).to.be.equal(value);
    });

    it('should check the JSON Response : `displayPriceTaxExcluded`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseDisplayPriceTaxExcluded', baseContext);

      const value = (await boCustomerGroupsCreatePage.getValue(page, 'displayPriceTaxExcluded')) === 'Tax excluded';
      expect(jsonResponse.displayPriceTaxExcluded).to.be.equal(value);
    });

    it('should check the JSON Response : `showPrice`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseShowPrice', baseContext);

      const value = (await boCustomerGroupsCreatePage.getValue(page, 'showPrice')) === '1';
      expect(jsonResponse.showPrice).to.be.equal(value);
    });
  });

  describe('BackOffice : Delete the Customer Group', async () => {
    it('should go to \'Groups\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToGroupsPageForDeletion', baseContext);

      await boCustomerSettingsPage.goToGroupsPage(page);

      const pageTitle = await boCustomerGroupsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCustomerGroupsPage.pageTitle);
    });

    it('should filter list by id', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForDeletion', baseContext);

      await boCustomerGroupsPage.resetFilter(page);
      await boCustomerGroupsPage.filterTable(page, 'input', 'id_group', idCustomerGroup);

      const numberOfGroupsAfterCreation = await boCustomerGroupsPage.getNumberOfElementInGrid(page);
      expect(numberOfGroupsAfterCreation).to.be.equal(1);

      const idCustomerGroupFiltered = parseInt(await boCustomerGroupsPage.getTextColumn(page, 1, 'id_group'), 10);
      expect(idCustomerGroupFiltered).to.be.equal(idCustomerGroup);
    });

    it('should delete group', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteGroup', baseContext);

      const textResult = await boCustomerGroupsPage.deleteGroup(page, 1);
      expect(textResult).to.contains(boCustomerGroupsPage.successfulDeleteMessage);
    });

    it('should reset filter', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterDelete', baseContext);

      const numberOfGroupsAfterDelete = await boCustomerGroupsPage.resetAndGetNumberOfLines(page);
      expect(numberOfGroupsAfterDelete).to.be.equal(numberOfGroups - 1);
    });
  });
});
