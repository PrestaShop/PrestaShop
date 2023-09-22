import BaseWS from '@webservices/baseWs';
import {APIRequestContext, APIResponse} from 'playwright';

export default class ProductWs extends BaseWS {
  public static endpoint = 'api/products';

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
   * Get All Countries
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
   * @param idProduct {string}
   */
  public static getById(
    apiContext: APIRequestContext,
    authorization: string,
    idProduct: string,
  ): Promise<APIResponse> {
    return super.getById(apiContext, this.endpoint, authorization, idProduct);
  }

  /**
   * Add new Country
   * @param apiContext {APIRequestContext}
   * @param authorization {string}
   * @param data {string} Xml of a new Country
   */
  public static add(
    apiContext: APIRequestContext,
    authorization: string,
    data: string,
  ): Promise<APIResponse> {
    return super.add(apiContext, this.endpoint, authorization, data);
  }

  /**
   * Update a country
   * @param apiContext {APIRequestContext}
   * @param authorization {string}
   * @param idProduct {string}
   * @param data {string} Xml of the new Country
   */
  public static update(
    apiContext: APIRequestContext,
    authorization: string,
    idProduct: string,
    data: string,
  ): Promise<APIResponse> {
    return super.update(apiContext, this.endpoint, authorization, idProduct, data);
  }

  /**
   * Delete a country
   * @param apiContext {APIRequestContext}
   * @param authorization {string}
   * @param idProduct {string}
   */
  public static delete(
    apiContext: APIRequestContext,
    authorization: string,
    idProduct: string,
  ): Promise<APIResponse> {
    return super.delete(apiContext, this.endpoint, authorization, idProduct);
  }
}
