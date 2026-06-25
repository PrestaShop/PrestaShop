import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boApiClientsPage,
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_API_checkEndpoints';

// @todo : https://github.com/PrestaShop/PrestaShop/issues/41109
// Dear developers, the CI is broken when you update the module ps_apiresources on the Core.
// It's normal : it's time to add them UI Tests.
describe('API : Check endpoints', async () => {
  const subsetEndpoints: string[] = [
    // @todo: add tests
    '/addresses/bulk-delete: DELETE',
    // @todo: add tests
    '/addresses/carts/{cartId}: PATCH',
    // @todo: add tests
    '/addresses/customers/{addressId}: GET',
    // @todo: add tests
    '/addresses/customers/{addressId}: PATCH',
    // @todo: add tests
    '/addresses/customers: POST',
    // @todo: add tests
    '/addresses/manufacturers/{addressId}: GET',
    // @todo: add tests
    '/addresses/manufacturers/{addressId}: PATCH',
    // @todo: add tests
    '/addresses/manufacturers: POST',
    // @todo: add tests
    '/addresses/orders/{orderId}: PATCH',
    // @todo: add tests
    '/addresses/{addressId}: DELETE',
    // @todo: add tests
    '/addresses: GET',
    // tests/UI/campaigns/functional/API/02_endpoints/apiClient/01_getApiClientInfos.ts
    '/api-clients/infos: GET',
    // tests/UI/campaigns/functional/API/02_endpoints/apiClient/02_deleteApiClientId.ts
    '/api-clients/{apiClientId}: DELETE',
    // tests/UI/campaigns/functional/API/02_endpoints/apiClient/03_getApiClientId.ts
    '/api-clients/{apiClientId}: GET',
    // tests/UI/campaigns/functional/API/02_endpoints/apiClient/04_patchApiClientId.ts
    '/api-clients/{apiClientId}: PATCH',
    // tests/UI/campaigns/functional/API/02_endpoints/apiClient/06_getApiClients.ts
    '/api-clients: GET',
    // tests/UI/campaigns/functional/API/02_endpoints/apiClient/05_postApiClient.ts
    '/api-clients: POST',
    // @todo: add tests
    '/attributes/attributes/bulk-delete: DELETE',
    // tests/UI/campaigns/functional/API/02_endpoints/attribute/01_deleteAttributesAttributeId.ts
    '/attributes/attributes/{attributeId}: DELETE',
    // tests/UI/campaigns/functional/API/02_endpoints/attribute/02_getAttributesAttributeId.ts
    '/attributes/attributes/{attributeId}: GET',
    // @todo: add tests
    '/attributes/attributes/{attributeId}: PATCH',
    // tests/UI/campaigns/functional/API/02_endpoints/attributes/04_postAttributesAttribute.ts
    '/attributes/attributes: POST',
    // tests/UI/campaigns/functional/API/02_endpoints/attribute/11_deleteAttributesGroupsBulkDelete.ts
    '/attributes/groups/bulk-delete: DELETE',
    // @todo: add tests
    '/attributes/groups/positions: PATCH',
    // @todo: add tests
    '/attributes/groups/{attributeGroupId}/attributes/positions: PATCH',
    // @todo: add tests
    '/attributes/groups/{attributeGroupId}/attributes: GET',
    // tests/UI/campaigns/functional/API/02_endpoints/attribute/07_deleteAttributesGroupId.ts
    '/attributes/groups/{attributeGroupId}: DELETE',
    // tests/UI/campaigns/functional/API/02_endpoints/attribute/08_getAttributesGroupId.ts
    '/attributes/groups/{attributeGroupId}: GET',
    // tests/UI/campaigns/functional/API/02_endpoints/attribute/09_patchAttributesGroupId.ts
    '/attributes/groups/{attributeGroupId}: PATCH',
    // tests/UI/campaigns/functional/API/02_endpoints/attribute/12_getAttributesGroups.ts
    '/attributes/groups: GET',
    // tests/UI/campaigns/functional/API/02_endpoints/attribute/10_postAttributesGroup.ts
    '/attributes/groups: POST',
    // @todo: add tests
    '/categories/bulk-delete/{deleteMode}: DELETE',
    // @todo: add tests
    '/categories/bulk-update-status: PUT',
    // tests/UI/campaigns/functional/API/02_endpoints/category/04_deleteCategoryIdCover.ts
    '/categories/{categoryId}/cover: DELETE',
    // @todo: add tests
    '/categories/{categoryId}/status: PATCH',
    // @todo: add tests
    '/categories/{categoryId}/thumbnail: DELETE',
    // tests/UI/campaigns/functional/API/02_endpoints/category/07_deleteCategoryIdMode.ts
    '/categories/{categoryId}/{mode}: DELETE',
    // @todo: add tests
    '/categories/{categoryId}: GET',
    // @todo: add tests
    '/categories/{categoryId}: PATCH',
    // tests/UI/campaigns/functional/API/02_endpoints/category/03_getCategories.ts
    '/categories: GET',
    // @todo: add tests
    '/categories: POST',
    // @todo: add tests
    '/contacts/{contactId}: GET',
    // @todo: add tests
    '/contacts/{contactId}: PATCH',
    // @todo: add tests
    '/contacts: GET',
    // @todo: add tests
    '/contacts: POST',
    // @todo: add tests
    '/countries/{countryId}: GET',
    // @todo: add tests
    '/countries: POST',
    // @todo: add tests
    '/customers/bulk-delete: DELETE',
    // @todo: add tests
    '/customers/bulk-disable: PUT',
    // @todo: add tests
    '/customers/bulk-enable: PUT',
    // tests/UI/campaigns/functional/API/02_endpoints/customerGroup/01_deleteCustomerGroupsId.ts
    '/customers/groups/{customerGroupId}: DELETE',
    // tests/UI/campaigns/functional/API/02_endpoints/customerGroup/02_getCustomerGroupsId.ts
    '/customers/groups/{customerGroupId}: GET',
    // tests/UI/campaigns/functional/API/02_endpoints/customerGroup/03_putCustomerGroupsId.ts
    '/customers/groups/{customerGroupId}: PUT',
    // tests/UI/campaigns/functional/API/02_endpoints/customerGroup/05_getCustomersGroups.ts
    '/customers/groups: GET',
    // tests/UI/campaigns/functional/API/02_endpoints/customerGroup/04_postCustomersGroup.ts
    '/customers/groups: POST',
    // @todo: add tests
    '/customers/search: GET',
    // @todo: add tests
    '/customers/{customerId}/details: GET',
    // @todo: add tests
    '/customers/{customerId}: DELETE',
    // @todo: add tests
    '/customers/{customerId}: GET',
    // @todo: add tests
    '/customers/{customerId}: PATCH',
    // @todo: add tests
    '/customers: POST',
    // @todo: add tests
    '/discounts/bulk-delete: DELETE',
    // @todo: add tests
    '/discounts/bulk-update-status: PATCH',
    // tests/UI/campaigns/functional/API/02_endpoints/discount/01_getDiscountTypes.ts
    '/discounts/types: GET',
    // @todo: add tests
    '/discounts/{discountId}/duplicate: POST',
    // tests/UI/campaigns/functional/API/02_endpoints/discount/02_deleteDiscountsDiscountsId.ts
    '/discounts/{discountId}: DELETE',
    // @todo : https://github.com/PrestaShop/PrestaShop/issues/38647
    '/discounts/{discountId}: GET',
    // @todo : https://github.com/PrestaShop/PrestaShop/issues/39682
    '/discounts/{discountId}: PATCH',
    // tests/UI/campaigns/functional/API/02_endpoints/discount/03_getDiscounts.ts
    '/discounts: GET',
    // tests/UI/campaigns/functional/API/02_endpoints/discount/03_postDiscounts.ts
    '/discounts: POST',
    // @todo: add tests
    '/features/bulk-delete: DELETE',
    // @todo: add tests
    '/features/values/bulk-delete: DELETE',
    // @todo: add tests
    '/features/values/{featureValueId}: DELETE',
    // @todo: add tests
    '/features/values/{featureValueId}: GET',
    // @todo: add tests
    '/features/values/{featureValueId}: PATCH',
    // @todo: add tests
    '/features/values: POST',
    // @todo: add tests
    '/features/{featureId}/values: GET',
    // @todo: add tests
    '/features/{featureId}: DELETE',
    // @todo: add tests
    '/features/{featureId}: GET',
    // @todo: add tests
    '/features/{featureId}: PATCH',
    // @todo: add tests
    '/features: GET',
    // @todo: add tests
    '/features: POST',
    // @todo: add tests
    '/hooks/{hookId}/status: GET',
    // tests/UI/campaigns/functional/API/02_endpoints/hook/01_putHookIdStatus.ts
    '/hooks/{hookId}/status: PUT',
    // tests/UI/campaigns/functional/API/02_endpoints/hook/02_getHookId.ts
    '/hooks/{hookId}: GET',
    // tests/UI/campaigns/functional/API/02_endpoints/hook/03_getHooks.ts
    '/hooks: GET',
    // tests/UI/campaigns/functional/API/02_endpoints/language/01_getLanguages.ts
    '/languages: GET',
    // tests/UI/campaigns/functional/API/02_endpoints/module/10_putModulesBulkUninstall.ts
    '/modules/bulk-uninstall: PUT',
    // tests/UI/campaigns/functional/API/02_endpoints/module/09_putModulesBulkUpdateStatus.ts
    '/modules/bulk-update-status: PUT',
    // tests/UI/campaigns/functional/API/02_endpoints/module/01_postModuleUploadArchive.ts
    '/modules/upload-archive: POST',
    // tests/UI/campaigns/functional/API/02_endpoints/module/02_postModuleUploadSource.ts
    '/modules/upload-source: POST',
    // tests/UI/campaigns/functional/API/02_endpoints/module/03_putModuleTechnicalNameInstall.ts
    '/modules/{technicalName}/install: PUT',
    // tests/UI/campaigns/functional/API/02_endpoints/module/04_patchModuleTechnicalNameReset.ts
    '/modules/{technicalName}/reset: PATCH',
    // tests/UI/campaigns/functional/API/02_endpoints/module/05_putModuleTechnicalNameStatus.ts
    '/modules/{technicalName}/status: PUT',
    // tests/UI/campaigns/functional/API/02_endpoints/module/06_putModuleTechnicalNameUninstall.ts
    '/modules/{technicalName}/uninstall: PUT',
    // tests/UI/campaigns/functional/API/02_endpoints/module/07_putModuleTechnicalNameUpgrade.ts
    '/modules/{technicalName}/upgrade: PUT',
    // tests/UI/campaigns/functional/API/02_endpoints/module/08_getModuleTechnicalName.ts
    '/modules/{technicalName}: GET',
    // tests/UI/campaigns/functional/API/02_endpoints/module/11_getModules.ts
    '/modules: GET',
    // @todo: add tests
    '/products/combinations/{combinationId}: GET',
    // tests/UI/campaigns/functional/API/02_endpoints/product/01_deleteProductImageId.ts
    '/products/images/{imageId}: DELETE',
    // tests/UI/campaigns/functional/API/02_endpoints/product/02_getProductImageId.ts
    '/products/images/{imageId}: GET',
    // tests/UI/campaigns/functional/API/02_endpoints/product/03_postProductImageId.ts
    '/products/images/{imageId}: POST',
    // tests/UI/campaigns/functional/API/02_endpoints/product/11_getProductsSearch.ts
    '/products/search: GET',
    // @todo: add tests
    '/products/{productId}/combination-ids: GET',
    // @todo: add tests
    '/products/{productId}/combinations: GET',
    // @todo: add tests
    '/products/{productId}/generate-combinations: POST',
    // tests/UI/campaigns/functional/API/02_endpoints/product/05_getProductIdImages.ts
    '/products/{productId}/images: GET',
    // tests/UI/campaigns/functional/API/02_endpoints/product/04_postProductIdImage.ts
    '/products/{productId}/images: POST',
    // tests/UI/campaigns/functional/API/02_endpoints/product/06_patchProductIdShops.ts
    '/products/{productId}/shops: PATCH',
    // tests/UI/campaigns/functional/API/02_endpoints/product/07_deleteProductId.ts
    '/products/{productId}: DELETE',
    // tests/UI/campaigns/functional/API/02_endpoints/product/08_getProductId.ts
    '/products/{productId}: GET',
    // tests/UI/campaigns/functional/API/02_endpoints/product/09_patchProductId.ts
    '/products/{productId}: PATCH',
    // tests/UI/campaigns/functional/API/02_endpoints/product/12_getProducts.ts
    '/products: GET',
    // tests/UI/campaigns/functional/API/02_endpoints/product/10_postProduct.ts
    '/products: POST',
    // @todo: add tests
    '/profiles/{profileId}: DELETE',
    // @todo: add tests
    '/profiles/{profileId}: GET',
    // @todo: add tests
    '/profiles: POST',
    // @todo: add tests
    '/search-aliases/bulk-delete: DELETE',
    // @todo: add tests
    '/search-aliases/{searchTerm}: GET',
    // @todo: add tests
    '/search-aliases/{searchTerm}: PUT',
    // @todo: add tests
    '/search-aliases: POST',
    // tests/UI/campaigns/functional/API/02_endpoints/searchAlias/01_deleteSearch.ts
    // '/search-aliases: DELETE',
    // @todo: add tests
    '/search-engines/bulk-delete: DELETE',
    // @todo: add tests
    '/search-engines/{searchEngineId}: DELETE',
    // @todo: add tests
    '/search-engines/{searchEngineId}: GET',
    // @todo: add tests
    '/search-engines/{searchEngineId}: PATCH',
    // @todo: add tests
    '/search-engines: GET',
    // @todo: add tests
    '/search-engines: POST',
    // @todo: add tests
    '/showcase-cards/{showcaseCardName}/{employeeId}/close: PUT',
    // @todo: add tests
    '/showcase-cards/{showcaseCardName}/{employeeId}: GET',
    // @todo: add tests
    '/stores/bulk-delete: DELETE',
    // @todo: add tests
    '/stores/bulk-update-status: PUT',
    // @todo: add tests
    '/stores/{storeId}/toggle-status: PUT',
    // @todo: add tests
    '/stores/{storeId}: DELETE',
    // @todo: add tests
    '/stores/{storeId}: GET',
    // @todo: add tests
    '/suppliers/bulk-delete: DELETE',
    // @todo: add tests
    '/suppliers/bulk-disable: PUT',
    // @todo: add tests
    '/suppliers/bulk-enable: PUT',
    // @todo: add tests
    '/suppliers/{supplierId}/logo: DELETE',
    // @todo: add tests
    '/suppliers/{supplierId}/toggle-status: PUT',
    // @todo: add tests
    '/suppliers/{supplierId}: DELETE',
    // @todo: add tests
    '/suppliers/{supplierId}: GET',
    // @todo: add tests
    '/suppliers/{supplierId}: PATCH',
    // @todo: add tests
    '/suppliers: GET',
    // @todo: add tests
    '/suppliers: POST',
    // @todo: add tests
    '/tabs/{className}: PUT',
    // @todo: add tests
    '/tax-rules-groups/bulk-delete: DELETE',
    // @todo: add tests
    '/tax-rules-groups/bulk-update-status: PUT',
    // @todo: add tests
    '/tax-rules-groups/{taxRulesGroupId}: DELETE',
    // @todo: add tests
    '/tax-rules-groups/{taxRulesGroupId}: GET',
    // @todo: add tests
    '/tax-rules-groups/{taxRulesGroupId}: PATCH',
    // @todo: add tests
    '/tax-rules-groups: GET',
    // @todo: add tests
    '/tax-rules-groups: POST',
    // @todo: add tests
    '/taxes/bulk-delete: PUT',
    // @todo: add tests
    '/taxes/bulk-set-status: PUT',
    // @todo: add tests
    '/taxes/{taxId}: DELETE',
    // @todo: add tests
    '/taxes/{taxId}: GET',
    // @todo: add tests
    '/taxes/{taxId}: PATCH',
    // @todo: add tests
    '/taxes: GET',
    // @todo: add tests
    '/taxes: POST',
    // @todo: add tests
    '/titles/bulk-delete: DELETE',
    // @todo: add tests
    '/titles/{titleId}: DELETE',
    // @todo: add tests
    '/titles/{titleId}: GET',
    // @todo: add tests
    '/titles/{titleId}: PATCH',
    // @todo: add tests
    '/titles: GET',
    // @todo: add tests
    '/titles: POST',
    // @todo: add tests
    '/webservice-keys/{webserviceKeyId}: GET',
    // @todo: add tests
    '/webservice-keys/{webserviceKeyId}: PATCH',
    // @todo: add tests
    '/webservice-keys: GET',
    // @todo: add tests
    '/webservice-keys: POST',
    // @todo: add tests
    '/zones/bulk-delete: DELETE',
    // @todo: add tests
    '/zones/bulk-update-status: PUT',
    // @todo: add tests
    '/zones/{zoneId}/toggle-status: PUT',
    // @todo: add tests
    '/zones/{zoneId}: DELETE',
    // @todo: add tests
    '/zones/{zoneId}: GET',
    // @todo: add tests
    '/zones/{zoneId}: PUT',
    // tests/UI/campaigns/functional/API/02_endpoints/zone/08_getZones.ts
    '/zones: GET',
    // @todo: add tests
    '/zones: POST',
  ];
  let browserContext: BrowserContext;
  let page: Page;
  let jsonPaths: object;

  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('Check endpoints', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Advanced Parameters > API Client\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAdminAPIPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.advancedParametersLink,
        boDashboardPage.adminAPILink,
      );

      const pageTitle = await boApiClientsPage.getPageTitle(page);
      expect(pageTitle).to.eq(boApiClientsPage.pageTitle);
    });

    it('should check that at least one API client is present', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkThatOneAPIClientExists', baseContext);

      const apiClientsNumber = await boApiClientsPage.getNumberOfElementInGrid(page);
      expect(apiClientsNumber).to.be.greaterThanOrEqual(1);
    });

    it('should fetch the documentation in JSON', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'fetchJSONDocumentation', baseContext);

      const jsonDoc = await boApiClientsPage.getJSONDocumentation(page);
      expect(jsonDoc).to.be.not.equals(null);
      expect(jsonDoc).to.have.property('paths');

      jsonPaths = jsonDoc.paths;
    });

    it('should check endpoints', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkEndpoints', baseContext);

      let lastIdxEndpoint = -1;
      let endpoints: string[] = [];

      // eslint-disable-next-line no-restricted-syntax
      for (const [endpointPath, endpointsJSON] of Object.entries(jsonPaths)) {
        // eslint-disable-next-line no-restricted-syntax
        for (const [endpointMethod] of Object.entries(endpointsJSON as object)) {
          endpoints.push(`${endpointPath}: ${endpointMethod.toUpperCase()}`);
        }
      }
      endpoints = endpoints.sort();

      // @todo : Check regulary
      //expect(endpoints).to.deep.equals(subsetEndpoints);

      expect(endpoints.length).to.be.greaterThan(0);
      subsetEndpoints.forEach((endpoint: string) => {
        const idxEndpoint = endpoints.indexOf(endpoint);
        expect(idxEndpoint).to.be.greaterThan(lastIdxEndpoint);
        lastIdxEndpoint = idxEndpoint;
      });
    });
  });
});
