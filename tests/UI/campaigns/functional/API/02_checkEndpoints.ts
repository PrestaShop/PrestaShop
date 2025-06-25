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

describe('API : Check endpoints', async () => {
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

    it('should check that no records found', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkThatNoRecordFound', baseContext);

      const noRecordsFoundText = await boApiClientsPage.getTextForEmptyTable(page);
      expect(noRecordsFoundText).to.contains('warning No records found');
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

      let endpoints: string[] = [];

      // eslint-disable-next-line no-restricted-syntax
      for (const [endpointPath, endpointsJSON] of Object.entries(jsonPaths)) {
        // eslint-disable-next-line no-restricted-syntax
        for (const [endpointMethod] of Object.entries(endpointsJSON as object)) {
          endpoints.push(`${endpointPath}: ${endpointMethod.toUpperCase()}`);
        }
      }
      endpoints = endpoints.sort();

      expect(endpoints.length).to.be.greaterThan(0);
      // Dear developers, the CI is broken when you update the module ps_apiresources on the Core.
      // It's normal : it's time to add them UI Tests.
      expect(endpoints).to.deep.equals([
        // tests/UI/campaigns/functional/API/02_endpoints/01_apiClient/01_getApiClientInfos.ts
        '/api-client/infos: GET',
        // tests/UI/campaigns/functional/API/02_endpoints/01_apiClient/02_deleteApiClientId.ts
        '/api-client/{apiClientId}: DELETE',
        // tests/UI/campaigns/functional/API/02_endpoints/01_apiClient/03_getApiClientId.ts
        '/api-client/{apiClientId}: GET',
        // tests/UI/campaigns/functional/API/02_endpoints/01_apiClient/04_patchApiClientId.ts
        '/api-client/{apiClientId}: PATCH',
        // tests/UI/campaigns/functional/API/02_endpoints/01_apiClient/05_postApiClient.ts
        '/api-client: POST',
        // tests/UI/campaigns/functional/API/02_endpoints/01_apiClient/06_getApiClients.ts
        '/api-clients: GET',
        // tests/UI/campaigns/functional/API/02_endpoints/02_customerGroup/01_deleteCustomerGroupsId.ts
        '/customers/group/{customerGroupId}: DELETE',
        // tests/UI/campaigns/functional/API/02_endpoints/02_customerGroup/02_getCustomerGroupsId.ts
        '/customers/group/{customerGroupId}: GET',
        // tests/UI/campaigns/functional/API/02_endpoints/02_customerGroup/03_putCustomerGroupsId.ts
        '/customers/group/{customerGroupId}: PUT',
        // tests/UI/campaigns/functional/API/02_endpoints/02_customerGroup/04_postCustomersGroup.ts
        '/customers/group: POST',
        // tests/UI/campaigns/functional/API/02_endpoints/03_hook/01_putHookStatusId.ts
        '/hook-status: PUT',
        // tests/UI/campaigns/functional/API/02_endpoints/03_hook/02_getHooksId.ts
        '/hook/{id}: GET',
        // tests/UI/campaigns/functional/API/02_endpoints/03_hook/03_getHooks.ts
        '/hooks: GET',
        // tests/UI/campaigns/functional/API/02_endpoints/04_language/01_getLanguages.ts
        '/languages: GET',
        // tests/UI/campaigns/functional/API/02_endpoints/05_module/01_postModuleUploadArchive.ts
        '/module/upload-archive: POST',
        // tests/UI/campaigns/functional/API/02_endpoints/05_module/02_postModuleUploadSource.ts
        '/module/upload-source: POST',
        // tests/UI/campaigns/functional/API/02_endpoints/05_module/03_putModuleTechnicalNameInstall.ts
        '/module/{technicalName}/install: PUT',
        // tests/UI/campaigns/functional/API/02_endpoints/05_module/04_patchModuleTechnicalNameReset.ts
        '/module/{technicalName}/reset: PATCH',
        // tests/UI/campaigns/functional/API/02_endpoints/05_module/05_putModuleTechnicalNameStatus.ts
        '/module/{technicalName}/status: PUT',
        // tests/UI/campaigns/functional/API/02_endpoints/05_module/06_putModuleTechnicalNameUninstall.ts
        '/module/{technicalName}/uninstall: PUT',
        // tests/UI/campaigns/functional/API/02_endpoints/05_module/07_putModuleTechnicalNameUpgrade.ts
        '/module/{technicalName}/upgrade: PUT',
        // tests/UI/campaigns/functional/API/02_endpoints/05_module/08_getModuleTechnicalName.ts
        '/module/{technicalName}: GET',
        // tests/UI/campaigns/functional/API/02_endpoints/05_module/09_putModulesToggleStatus.ts
        '/modules/toggle-status: PUT',
        // tests/UI/campaigns/functional/API/02_endpoints/05_module/10_putModulesUninstall.ts
        '/modules/uninstall: PUT',
        // tests/UI/campaigns/functional/API/02_endpoints/05_module/11_getModules.ts
        '/modules: GET',
        // tests/UI/campaigns/functional/API/02_endpoints/06_product/01_getProductImageId.ts
        '/product/image/{imageId}: GET',
        // tests/UI/campaigns/functional/API/02_endpoints/06_product/02_postProductImageId.ts
        '/product/image/{imageId}: POST',
        // tests/UI/campaigns/functional/API/02_endpoints/06_product/03_postProductIdImage.ts
        '/product/{productId}/image: POST',
        // tests/UI/campaigns/functional/API/02_endpoints/06_product/04_getProductIdImages.ts
        '/product/{productId}/images: GET',
        // tests/UI/campaigns/functional/API/02_endpoints/06_product/05_patchProductIdShops.ts
        '/product/{productId}/shops: PATCH',
        // tests/UI/campaigns/functional/API/02_endpoints/06_product/06_deleteProductId.ts
        '/product/{productId}: DELETE',
        // tests/UI/campaigns/functional/API/02_endpoints/06_product/07_getProductId.ts
        '/product/{productId}: GET',
        // tests/UI/campaigns/functional/API/02_endpoints/06_product/08_patchProductId.ts
        '/product/{productId}: PATCH',
        // tests/UI/campaigns/functional/API/02_endpoints/06_product/09_postProduct.ts
        '/product: POST',
        // tests/UI/campaigns/functional/API/02_endpoints/06_product/10_getProductsSearch.ts
        '/products/search/{phrase}/{resultsLimit}/{isoCode}: GET',
        // tests/UI/campaigns/functional/API/02_endpoints/06_product/11_getProducts.ts
        '/products: GET',
      ]);
    });
  });
});
