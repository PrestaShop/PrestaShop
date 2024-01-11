import BaseWS from '@webservices/baseWs';
import {APIRequestContext, APIResponse} from 'playwright';

export default class OrderWs extends BaseWS {
  public static endpoint = 'api/orders';

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
   * Get All Orders
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
   * @param idOrder {string}
   */
  public static getById(
    apiContext: APIRequestContext,
    authorization: string,
    idOrder: string,
  ): Promise<APIResponse> {
    return super.getById(apiContext, this.endpoint, authorization, idOrder);
  }

  /**
   * Add new Order
   * @param apiContext {APIRequestContext}
   * @param authorization {string}
   * @param data {string} Xml of a new Order
   */
  public static add(
    apiContext: APIRequestContext,
    authorization: string,
    data: string,
  ): Promise<APIResponse> {
    return super.add(apiContext, this.endpoint, authorization, data);
  }

  /**
   * Update a order
   * @param apiContext {APIRequestContext}
   * @param authorization {string}
   * @param idOrder {string}
   * @param data {string} Xml of the new Order
   */
  public static update(
    apiContext: APIRequestContext,
    authorization: string,
    idOrder: string,
    data: string,
  ): Promise<APIResponse> {
    return super.update(apiContext, this.endpoint, authorization, idOrder, data);
  }

  /**
   * Delete a order
   * @param apiContext {APIRequestContext}
   * @param authorization {string}
   * @param idOrder {string}
   */
  public static delete(
    apiContext: APIRequestContext,
    authorization: string,
    idOrder: string,
  ): Promise<APIResponse> {
    return super.delete(apiContext, this.endpoint, authorization, idOrder);
  }
}
