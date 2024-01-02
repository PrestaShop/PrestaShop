import BaseXml from '@webservices/baseXml';
import xmlHelper from '@utils/xml';

export default class OrderXml extends BaseXml {
  private static ordersBasicPath = '/prestashop/orders';

  private static ordersPath = `${this.ordersBasicPath}/*`;

  private static orderBasicPath = '/prestashop/order';

  private static orderPath = `${this.orderBasicPath}/*`;

  private static attributePath = (attribute: string) => `${this.orderBasicPath}/${attribute}`;

  private static attributeLangPath = (
    attribute: string,
    lang: string,
  ) => `${this.attributePath(attribute)}/language[@id="${lang}"]`;

  /**
   * Get all orders nodes
   * @param xml
   */
  public static getAllOrders(
    xml: string,
  ): Element[] {
    return xmlHelper.getNodes(xml, this.ordersPath);
  }

  /**
   * Get order nodes
   * @param xml
   */
  public static getOrderNodes(
    xml: string,
  ): Element[] {
    return xmlHelper.getNodes(xml, this.orderPath);
  }

  /**
   * Get attribute value
   * @param xml
   * @param attribute
   */
  public static getAttributeValue(
    xml: string,
    attribute: string,
  ): string|null {
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
  ): string|null {
    return xmlHelper.getNodeValue(xml, this.attributeLangPath(attribute, lang));
  }
}
