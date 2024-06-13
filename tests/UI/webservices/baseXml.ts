import {
  utilsXML,
} from '@prestashop-core/ui-testing';

export default class BaseXml {
  protected static prestashopPath = '/prestashop/*';

  /**
   * Get root node name
   * @param xml
   */
  public static getRootNodeName(
    xml: string,
  ): string {
    return utilsXML.getRootNodeName(xml);
  }

  /**
   * Get PrestaShop root nodes
   * @param xml
   */
  public static getPrestaShopNodes(
    xml: string,
  ): Element[] {
    return utilsXML.getNodes(xml, this.prestashopPath);
  }
}
