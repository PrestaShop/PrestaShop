import Endpoints from '@webservices/country/endpoints.enum';
import type {APIRequestContext, APIResponse} from 'playwright';

export default {
  /**
   * Get All Countries
   * @param apiContext {APIRequestContext}
   * @param authorization {string}
   */
  getAll(
    apiContext: APIRequestContext,
    authorization: string,
  ): Promise<APIResponse> {
    return apiContext.get(Endpoints.COUNTRIES, {
      headers: {
        Authorization: authorization,
      },
    });
  },
  /**
   * Get By id
   * @param apiContext {APIRequestContext}
   * @param authorization {string}
   * @param idCountry {string}
   */
  getById(
    apiContext: APIRequestContext,
    authorization: string,
    idCountry: string,
  ): Promise<APIResponse> {
    return apiContext.get(Endpoints.COUNTRIES + idCountry, {
      headers: {
        Authorization: authorization,
      },
    });
  },
  /**
   * Add new Country
   * @param apiContext {APIRequestContext}
   * @param authorization {string}
   * @param data {string} Xml of a new Country
   */
  add(
    apiContext: APIRequestContext,
    authorization: string,
    data: string,
  ): Promise<APIResponse> {
    return apiContext.post(Endpoints.COUNTRIES, {
      headers: {
        Authorization: authorization,
      },
      data,
    });
  },
  /**
   * Update a country
   * @param apiContext {APIRequestContext}
   * @param authorization {string}
   * @param idCountry {string}
   * @param data {string} Xml of the new Country
   */
  update(
    apiContext: APIRequestContext,
    authorization: string,
    idCountry: string,
    data: string,
  ): Promise<APIResponse> {
    return apiContext.put(Endpoints.COUNTRIES + idCountry, {
      headers: {
        Authorization: authorization,
      },
      data,
    });
  },
  /**
   * Delete a country
   * @param apiContext {APIRequestContext}
   * @param authorization {string}
   * @param idCountry {string}
   */
  delete(
    apiContext: APIRequestContext,
    authorization: string,
    idCountry: string,
  ): Promise<APIResponse> {
    return apiContext.delete(Endpoints.COUNTRIES + idCountry, {
      headers: {
        Authorization: authorization,
      },
    });
  },
};
