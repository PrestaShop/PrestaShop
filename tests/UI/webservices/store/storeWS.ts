import BaseWS from '@webservices/baseWs';
import {APIRequestContext, APIResponse} from 'playwright';

export default class StoreWS extends BaseWS {
  public static endpoint = 'api/stores';

  public static async getBlank(
    apiContext: APIRequestContext,
    authorization: string,
  ): Promise<APIResponse> {
    return super.getBlank(apiContext, this.endpoint, authorization);
  }

  public static async getSynopsis(
    apiContext: APIRequestContext,
    authorization: string,
  ): Promise<APIResponse> {
    return super.getSynopsis(apiContext, this.endpoint, authorization);
  }

  /**
   * Get All Stores
   * @param apiContext {APIRequestContext}
   * @param authorization {string}
   */
  public static async getAll(
    apiContext: APIRequestContext,
    authorization: string,
  ): Promise<APIResponse> {
    return super.getAll(apiContext, this.endpoint, authorization);
  }

  /**
   * Get By id
   * @param apiContext {APIRequestContext}
   * @param authorization {string}
   * @param idStore {string}
   */
  public static getById(
    apiContext: APIRequestContext,
    authorization: string,
    idStore: string,
  ): Promise<APIResponse> {
    return super.getById(apiContext, this.endpoint, authorization, idStore);
  }

  /**
   * Add new Store
   * @param apiContext {APIRequestContext}
   * @param authorization {string}
   * @param data {string} Xml of a new Store
   */
  public static add(
    apiContext: APIRequestContext,
    authorization: string,
    data: string,
  ): Promise<APIResponse> {
    return super.add(apiContext, this.endpoint, authorization, data);
  }

  /**
   * Update a store
   * @param apiContext {APIRequestContext}
   * @param authorization {string}
   * @param idStore {string}
   * @param data {string} Xml of the new Store
   */
  public static update(
    apiContext: APIRequestContext,
    authorization: string,
    idStore: string,
    data: string,
  ): Promise<APIResponse> {
    return super.update(apiContext, this.endpoint, authorization, idStore, data);
  }

  /**
   * Delete a store
   * @param apiContext {APIRequestContext}
   * @param authorization {string}
   * @param idStore {string}
   */
  public static delete(
    apiContext: APIRequestContext,
    authorization: string,
    idStore: string,
  ): Promise<APIResponse> {
    return super.delete(apiContext, this.endpoint, authorization, idStore);
  }
}
