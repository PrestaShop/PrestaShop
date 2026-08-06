// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import {requestAccessToken} from '@commonTests/BO/advancedParameters/authServer';

import {
  type APIRequestContext,
  boCustomerGroupsPage,
  boCustomerGroupsCreatePage,
  boCustomerSettingsPage,
  boDashboardPage,
  type BrowserContext,
  dataLanguages,
  FakerGroup,
  type Page,
  utilsAPI,
  utilsPlaywright, boLoginPage,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';

const baseContext: string = 'functional_API_endpoints_customerGroup_putCustomerGroupsId';

describe('API : PUT /customers/groups/{customerGroupId}', async () => {
  let apiContext: APIRequestContext;
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfGroups: number;
  let idCustomerGroup: number;
  let jsonResponse: any;
  let accessToken: string;

  const clientScope: string = 'customer_group_write';
  const createGroupData: FakerGroup = new FakerGroup({
    priceDisplayMethod: 'Tax included',
  });
  const updateGroupData: FakerGroup = new FakerGroup({
    name: 'Customer Group EN',
    frName: 'Customer Group FR',
    discount: 42,
    shownPrices: false,
    priceDisplayMethod: 'Tax excluded',
  });

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

  describe('BackOffice : Create a Customer Group', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

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
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

      numberOfGroups = await boCustomerGroupsPage.resetAndGetNumberOfLines(page);
      expect(numberOfGroups).to.be.above(0);
    });

    it('should go to add new group page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewGroup', baseContext);

      await boCustomerGroupsPage.goToNewGroupPage(page);

      const pageTitle = await boCustomerGroupsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boCustomerGroupsCreatePage.pageTitleCreate);
    });

    it('should create group and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createGroup', baseContext);

      const textResult = await boCustomerGroupsCreatePage.createEditGroup(page, createGroupData);
      expect(textResult).to.contains(boCustomerGroupsPage.successfulCreationMessage);

      const numberOfGroupsAfterCreation = await boCustomerGroupsPage.getNumberOfElementInGrid(page);
      expect(numberOfGroupsAfterCreation).to.be.equal(numberOfGroups + 1);
    });

    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterAfterCreation', baseContext);

      await boCustomerGroupsPage.resetFilter(page);
      await boCustomerGroupsPage.filterTable(page, 'input', 'b!name', createGroupData.name);

      const numberOfGroupsAfterCreation = await boCustomerGroupsPage.getNumberOfElementInGrid(page);
      expect(numberOfGroupsAfterCreation).to.be.equal(1);

      const textEmail = await boCustomerGroupsPage.getTextColumn(page, 1, 'b!name');
      expect(textEmail).to.contains(createGroupData.name);

      idCustomerGroup = parseInt(await boCustomerGroupsPage.getTextColumn(page, 1, 'id_group'), 10);
      expect(idCustomerGroup).to.be.gt(0);
    });
  });

  describe('API : Update the Customer Group', async () => {
    it('should request the endpoint /customers/groups/{customerGroupId}', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestEndpoint', baseContext);

      const apiResponse = await apiContext.put(`customers/groups/${idCustomerGroup}`, {
        headers: {
          Authorization: `Bearer ${accessToken}`,
        },
        data: {
          customerGroupId: idCustomerGroup,
          localizedNames: {
            [dataLanguages.french.locale]: updateGroupData.frName,
            [dataLanguages.english.locale]: updateGroupData.name,
          },
          reductionPercent: updateGroupData.discount,
          displayPriceTaxExcluded: updateGroupData.priceDisplayMethod === 'Tax excluded',
          showPrice: updateGroupData.shownPrices,
        },
      });
      expect(apiResponse.status()).to.eq(200);
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

      expect(jsonResponse.customerGroupId).to.equal(idCustomerGroup);
      expect(jsonResponse.localizedNames).to.deep.equal({
        [dataLanguages.english.locale]: updateGroupData.name,
        [dataLanguages.french.locale]: updateGroupData.frName,
      });
      expect(jsonResponse.reductionPercent).to.equal(updateGroupData.discount);
      expect(jsonResponse.displayPriceTaxExcluded).to.equal(updateGroupData.priceDisplayMethod === 'Tax excluded');
      expect(jsonResponse.showPrice).to.equal(updateGroupData.shownPrices);
      expect(jsonResponse.shopIds).to.deep.equal([1]);
    });
  });

  describe('BackOffice : Check the Customer Group is updated', async () => {
    it('should filter list by id', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterAfterUpdate', baseContext);

      await boCustomerGroupsPage.resetFilter(page);
      await boCustomerGroupsPage.filterTable(page, 'input', 'id_group', idCustomerGroup);

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

      idCustomerGroup = parseInt(await boCustomerGroupsPage.getTextColumn(page, 1, 'id_group'), 10);
      expect(idCustomerGroup).to.be.equal(idCustomerGroup);
    });

    it('should delete group', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteGroup', baseContext);

      const textResult = await boCustomerGroupsPage.deleteGroup(page, 1);
      expect(textResult).to.contains(boCustomerGroupsPage.successfulDeleteMessage);
    });

    it('should reset filter', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterDelete', baseContext);

      const numberOfGroupsAfterDelete = await boCustomerGroupsPage.resetAndGetNumberOfLines(page);
      expect(numberOfGroupsAfterDelete).to.be.equal(numberOfGroups);
    });
  });
});
