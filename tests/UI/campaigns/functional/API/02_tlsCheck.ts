// Import utils
import api from '@utils/api';
import helpers from '@utils/helpers';
import testContext from '@utils/testContext';

import {expect} from 'chai';
import type {APIRequestContext} from 'playwright';
import https from 'https';

const baseContext: string = 'functional_API_tlsCheck';

describe('API : Basic Test', async () => {
  const boURLinSecure: string = global.BO.URL
    .replace('https', 'http');
  const foURL: string[] = global.FO.URL
    .replace('https://', '')
    .replace('/', '')
    .split(':');
  const foURLBasePath: string = foURL[0];
  const foURLPort: number = foURL[1] ? parseInt(foURL[1], 10) : 443;

  let apiContextHttp: APIRequestContext;

  before(async () => {
    console.log(boURLinSecure);
    apiContextHttp = await helpers.createAPIContext(boURLinSecure, false);
  });

  describe('TLS Check', async () => {
    it('should request the endpoint /admin-dev/api/oauth2/token in HTTP', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestHttp', baseContext);

      const apiResponse = await apiContextHttp.post('api/oauth2/token', {
        form: {
          client_id: 'my_client_id',
          client_secret: 'prestashop',
          grant_type: 'client_credentials',
        },
        maxRedirects: 0,
      });
      console.log((await apiResponse.body()).toString());
      await expect(apiResponse.status()).to.eq(308);
      await expect(api.hasResponseHeader(apiResponse, 'Location')).to.be.true;
      await expect(api.getResponseHeader(apiResponse, 'Location')).to.contains(global.BO.URL);
    });
    it('should request the endpoint /admin-dev/api/oauth2/token in HTTPS/TLS1.0', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestHttpsTls1.0', baseContext);

      const postData: string = 'client_id=my_client_id&client_secret=prestashop&grant_type=client_credentials';

      const postRequest = https.request({
        hostname: foURLBasePath,
        port: foURLPort.toString(),
        path: '/admin-dev/api/oauth2/token',
        method: 'POST',
        //secureProtocol: 'TLSv1_2_method',
        minVersion: 'TLSv1',
        maxVersion: 'TLSv1',
        rejectUnauthorized: false,
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
          'Content-Length': Buffer.byteLength(postData),
        },
      },
      (res): void => {
        let body = '';
        res.on('data', (data) => body += data);
        res.on('end', () => {
          console.log(`Body : ${body}`);
          const data = JSON.parse(body);
          console.log(`SSL Version: ${data.tls_version}`);
        });
      });
      postRequest.on('error', (err: Error): void => {
        // This gets called if a connection cannot be established.
        console.log(`Error : ${err.message}`);
      });
      postRequest.on('information', (info) => {
        console.log(`Information : HttpVersion : ${info.httpVersion}`);
        console.log(`Information : StatusCode : ${info.statusCode}`);
        console.log('Information : Headers :');
        console.log(info.rawHeaders);
      });
      postRequest.write(postData);
      postRequest.end();
    });
  });
});
