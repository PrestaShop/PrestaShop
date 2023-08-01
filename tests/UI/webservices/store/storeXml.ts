import BaseXml from '@webservices/baseXml';
import xmlHelper from '@utils/xml';

export default class StoreXml extends BaseXml {
  private static storesBasicPath = '/prestashop/stores';

  private static storesPath = `${this.storesBasicPath}/*`;

  private static storeBasicPath = '/prestashop/store';

  private static storePath = `${this.storeBasicPath}/*`;

  private static eltPath = (eltName: string) => `${this.storeBasicPath}/${eltName}`;

  private static eltLangPath = (
    eltName: string,
    lang: string,
  ) => `${this.eltPath(eltName)}/language[@id="${lang}"]`;

  /**
   * Get all stores nodes
   * @param xml
   */
  public static getAllStores(
    xml: string,
  ): Element[] {
    return xmlHelper.getNodes(xml, this.storesPath);
  }

  /**
   * Get store nodes
   * @param xml
   */
  public static getStoreNodes(
    xml: string,
  ): Element[] {
    return xmlHelper.getNodes(xml, this.storePath);
  }

  /**
   * Get element value
   * @param xml
   * @param eltName
   */
  public static getEltTextContent(
    xml: string,
    eltName: string,
  ): string {
    return xmlHelper.getNodeValue(xml, this.eltPath(eltName));
  }

  /**
   * Get attribute with language value
   * @param xml
   * @param eltName
   * @param lang
   */
  public static getLangEltTextContent(
    xml: string,
    eltName: string,
    lang: string,
  ): string {
    return xmlHelper.getNodeValue(xml, this.eltLangPath(eltName, lang));
  }
}
