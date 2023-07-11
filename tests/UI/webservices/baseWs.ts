import type {APIRequestContext, APIResponse} from 'playwright';

export default class BaseWS {
  /**
   * Returns a blank XML tree of the chosen resource
   * @param apiContext {APIRequestContext}
   * @param url {string}
   * @param authorization {string}
   */
  protected static getBlank(
    apiContext: APIRequestContext,
    url: string,
    authorization: string,
  ): Promise<APIResponse> {
    return apiContext.get(`${url}`, {
      headers: {
        Authorization: authorization,
      },
      params: {
        schema: 'blank',
        display: 'full',
      },
    });
  };

  /**
   * Returns a blank XML tree of the chosen resource, with the format that is expected for each value and specific indicators
   * @param apiContext {APIRequestContext}
   * @param url {string}
   * @param authorization {string}
   */
  protected static getSynopsis(
    apiContext: APIRequestContext,
    url: string,
    authorization: string,
  ): Promise<APIResponse> {
    return apiContext.get(`${url}`, {
      headers: {
        Authorization: authorization,
      },
      params: {
        schema: 'synopsis',
        display: 'full',
      },
    });
  };

  /**
   * Get all element
   * @param apiContext {APIRequestContext}
   * @param url {string}
   * @param authorization {string}
   */
  protected static getAll(
    apiContext: APIRequestContext,
    url: string,
    authorization: string,
  ): Promise<APIResponse> {
    return apiContext.get(url, {
      headers: {
        Authorization: authorization,
      },
    });
  };

  /**
   * Get element by id
   * @param apiContext {APIRequestContext}
   * @param url {string}
   * @param authorization {string}
   * @param id {string}
   */
  protected static getById(
    apiContext: APIRequestContext,
    url: string,
    authorization: string,
    id: string,
  ): Promise<APIResponse> {
    return apiContext.get(`${url}/${id}`, {
      headers: {
        Authorization: authorization,
      },
    });
  };

  /**
   * Add new element
   * @param apiContext {APIRequestContext}
   * @param url {string}
   * @param authorization {string}
   * @param data {string} Xml of a new Country
   */
  protected static add(
    apiContext: APIRequestContext,
    url: string,
    authorization: string,
    data: string,
  ): Promise<APIResponse> {
    return apiContext.post(url, {
      headers: {
        Authorization: authorization,
      },
      data,
    });
  };

  /**
   * Update an element
   * @param apiContext {APIRequestContext}
   * @param url {string}
   * @param authorization {string}
   * @param id {string}
   * @param data {string} Xml of the new Country
   */
  protected static update(
    apiContext: APIRequestContext,
    url: string,
    authorization: string,
    id: string,
    data: string,
  ): Promise<APIResponse> {
    return apiContext.put(`${url}/${id}`, {
      headers: {
        Authorization: authorization,
      },
      data,
    });
  };

  /**
   * Delete an element
   * @param apiContext {APIRequestContext}
   * @param url {string}
   * @param authorization {string}
   * @param id {string}
   */
  protected static delete(
    apiContext: APIRequestContext,
    url: string,
    authorization: string,
    id: string,
  ): Promise<APIResponse> {
    return apiContext.delete(`${url}/${id}`, {
      headers: {
        Authorization: authorization,
      },
    });
  };
}
