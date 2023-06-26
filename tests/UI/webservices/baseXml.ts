import xmlHelper from '@utils/xml';

export default class BaseXml {
  protected static prestashopPath = '/prestashop/*';

  /**
   * Get root node name
   * @param xml
   */
  public static getRootNodeName(
    xml: string,
  ): string {
    return xmlHelper.getRootNodeName(xml);
  }

  /**
   * Get PrestaShop root nodes
   * @param xml
   */
  public static getPrestaShopNodes(
    xml: string,
  ): Element[] {
    return xmlHelper.getNodes(xml, this.prestashopPath);
  }
}
