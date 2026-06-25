import BaseWS from '@webservices/baseWs';
import {
  type APIRequestContext,
  type APIResponse,
} from '@prestashop-core/ui-testing';

export default class CategoryWS extends BaseWS {
  public static endpoint = 'api/categories';

  /**
   * Get Blank
   * @param apiContext {APIRequestContext}
   * @param authorization {string}
   */
  public static async getBlank(
    apiContext: APIRequestContext,
    authorization: string,
  ): Promise<APIResponse> {
    return super.getBlank(apiContext, this.endpoint, authorization);
  }

  /**
   * Get Synopsis
   * @param apiContext {APIRequestContext}
   * @param authorization {string}
   */
  public static async getSynopsis(
    apiContext: APIRequestContext,
    authorization: string,
  ): Promise<APIResponse> {
    return super.getSynopsis(apiContext, this.endpoint, authorization);
  }

  /**
   * Get All categories
   * @param apiContext {APIRequestContext}
   * @param authorization {string}
   */
  public static getAll(
    apiContext: APIRequestContext,
    authorization: string,
  ): Promise<APIResponse> {
    return super.getAll(apiContext, this.endpoint, authorization);
  }

  /**
   * Get By id
   * @param apiContext {APIRequestContext}
   * @param authorization {string}
   * @param idCountry {string}
   */
  public static getById(
    apiContext: APIRequestContext,
    authorization: string,
    idCountry: string,
  ): Promise<APIResponse> {
    return super.getById(apiContext, this.endpoint, authorization, idCountry);
  }

  /**
   * Add new Category
   * @param apiContext {APIRequestContext}
   * @param authorization {string}
   * @param data {string} Xml of a new Category
   */
  public static add(
    apiContext: APIRequestContext,
    authorization: string,
    data: string,
  ): Promise<APIResponse> {
    return super.add(apiContext, this.endpoint, authorization, data);
  }

  /**
   * Update a Category
   * @param apiContext {APIRequestContext}
   * @param authorization {string}
   * @param idCountry {string}
   * @param data {string} Xml of the new Category
   */
  public static update(
    apiContext: APIRequestContext,
    authorization: string,
    idCategory: string,
    data: string,
  ): Promise<APIResponse> {
    return super.update(apiContext, this.endpoint, authorization, idCategory, data);
  }

  /**
   * Delete a Category
   * @param apiContext {APIRequestContext}
   * @param authorization {string}
   * @param idCountry {string}
   */
  public static delete(
    apiContext: APIRequestContext,
    authorization: string,
    idCategory: string,
  ): Promise<APIResponse> {
    return super.delete(apiContext, this.endpoint, authorization, idCategory);
  }
}
