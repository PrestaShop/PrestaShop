import BaseXml from '@webservices/baseXml';
import {
  utilsXML,
} from '@prestashop-core/ui-testing';

export default class CategoryXml extends BaseXml {
  private static categoriesBasicPath = '/prestashop/categories';

  private static categoriesPath = `${this.categoriesBasicPath}/*`;

  private static categoryBasicPath = '/prestashop/category';

  private static categoryPath = `${this.categoryBasicPath}/*`;

  private static attributePath = (attribute: string) => `${this.categoryBasicPath}/${attribute}`;

  private static attributeLangPath = (
    attribute: string,
    lang: string,
  ) => `${this.attributePath(attribute)}/language[@id="${lang}"]`;

  /**
   * Get the child nodes of <category>
   */
  public static getCategoryNodes(
    xml: string,
  ): Element[] {
    return utilsXML.getNodes(xml, this.categoryPath);
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
    return utilsXML.getNodeValue(xml, this.attributePath(attribute));
  }

  /**
   * Get all <category> nodes from a <categories> list response
   */
  public static getAllCategories(
    xml: string,
  ): Element[] {
    return utilsXML.getNodes(xml, this.categoriesPath);
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
    return utilsXML.getNodeValue(xml, this.attributeLangPath(attribute, lang));
  }
}
