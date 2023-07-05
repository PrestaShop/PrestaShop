import BaseXml from '@webservices/baseXml';
import xmlHelper from '@utils/xml';

export default class CountryXml extends BaseXml {
  private static countriesBasicPath = '/prestashop/countries';

  private static countriesPath = `${this.countriesBasicPath}/*`;

  private static countryBasicPath = '/prestashop/country';

  private static countryPath = `${this.countryBasicPath}/*`;

  private static attributePath = (attribute: string) => `${this.countryBasicPath}/${attribute}`;

  private static attributeLangPath = (
    attribute: string,
    lang: string,
  ) => `${this.attributePath(attribute)}/language[@id="${lang}"]`;

  /**
   * Get all countries nodes
   * @param xml
   */
  public static getAllCountries(
    xml: string,
  ): Element[] {
    return xmlHelper.getNodes(xml, this.countriesPath);
  }

  /**
   * Get country nodes
   * @param xml
   */
  public static getCountryNodes(
    xml: string,
  ): Element[] {
    return xmlHelper.getNodes(xml, this.countryPath);
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
