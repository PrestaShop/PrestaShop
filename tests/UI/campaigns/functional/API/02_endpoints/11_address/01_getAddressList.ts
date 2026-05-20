// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import {requestAccessToken} from '@commonTests/BO/advancedParameters/authServer';
import {createAddressTest} from '@commonTests/BO/customers/address';

import {expect} from 'chai';
import {
  type APIRequestContext,
  boAddressesPage,
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  FakerAddress,
  utilsAPI,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_API_endpoints_address_getAddressList';

describe('API : Get /addresses/{addressId}', async () => {
  let apiContext: APIRequestContext;
  let browserContext: BrowserContext;
  let page: Page;
  let jsonResponse: any;
  let accessToken: string;

  const clientScope: string = 'address_read';

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
      expect(accessToken).to.not.be.empty;
    });
  });

  describe('API : Fetch data', async () => {
    it('should request the endpoint /addresses', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestEndpoint', baseContext);

      const apiResponse = await apiContext.get('addresses', {
        headers: {
          Authorization: `Bearer ${accessToken}`,
        },
      });
      
      expect(apiResponse.status()).to.eq(200);
      expect(utilsAPI.hasResponseHeader(apiResponse, 'Content-Type')).to.eq(true);
      expect(utilsAPI.getResponseHeader(apiResponse, 'Content-Type')).to.contains('application/json');

      jsonResponse = await apiResponse.json();
      console.log(jsonResponse);
    });

   /* it('should check the JSON Response keys', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseKeys', baseContext);
      console.log(jsonResponse);
      expect(jsonResponse).to.have.all.keys(
        'addressId',
        'firstname',
        'lastname',
        'address1',
        'postcode',
        'city',
        'country_name',
      );

      expect(jsonResponse.totalItems).to.be.gt(0);
    });*/
  });
});
