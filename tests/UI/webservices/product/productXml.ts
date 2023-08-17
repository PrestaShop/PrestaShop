import BaseXml from '@webservices/baseXml';
import xmlHelper from '@utils/xml';

export default class ProductXml extends BaseXml {
  private static productsBasicPath = '/prestashop/products';

  private static productsPath = `${this.productsBasicPath}/*`;

  private static productBasicPath = '/prestashop/product';

  private static productPath = `${this.productBasicPath}/*`;

  private static attributePath = (attribute: string) => `${this.productBasicPath}/${attribute}`;

  private static attributeLangPath = (
    attribute: string,
    lang: string,
  ) => `${this.attributePath(attribute)}/language[@id="${lang}"]`;

  /**
   * Get all products nodes
   * @param xml
   */
  public static getAllProducts(
    xml: string,
  ): Element[] {
    return xmlHelper.getNodes(xml, this.productsPath);
  }

  /**
   * Get product nodes
   * @param xml
   */
  public static getProductNodes(
    xml: string,
  ): Element[] {
    return xmlHelper.getNodes(xml, this.productPath);
  }

  /**
   * Get attribute value
   * @param xml
   * @param attribute
   */
  public static getAttributeValue(
    xml: string,
    attribute: string,
  ): string {
    return xmlHelper.getNodeValue(xml, this.attributePath(attribute));
  }

  /**
   * Get attribute with language value
   * @param xml
   * @param attribute
   * @param lang
   */
  public static getAttributeLangValue(
    xml: string,
    attribute: string,
    lang: string,
  ): string {
    return xmlHelper.getNodeValue(xml, this.attributeLangPath(attribute, lang));
  }
}
