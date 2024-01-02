import {DOMParser} from '@xmldom/xmldom';
import {XMLValidator} from 'fast-xml-parser';
import * as xpath from 'xpath-ts';

const domParser: DOMParser = new DOMParser();

export default {
  /**
   * Returns a XML Document created from a XML string
   * @param {string} xml
   * @return {Document}
   */
  getXmlDocument(xml: string): Document {
    return domParser.parseFromString(xml);
  },

  /**
   * Returns the XML Root Node Name
   * @param {string} xml
   * @return {string}
   */
  getRootNodeName(xml: string): string {
    const xmlDocument = this.getXmlDocument(xml);
    const docElement: HTMLElement = xmlDocument.documentElement;

    return docElement.nodeName;
  },

  /**
   * Returns XML Nodes from a specific path
   * @param {string} xml
   * @param {string} path
   * @return {string}
   */
  getNodes(xml: string, path: string): Element[] {
    const xmlDocument = this.getXmlDocument(xml);

    return xpath.select(path, xmlDocument) as Element[];
  },

  /**
   * Returns a specific XML Node from a specific path
   * @param {string} xml
   * @param {string} path
   * @return {string}
   */
  getNode(xml: string, path: string): Element {
    const xmlDocument = this.getXmlDocument(xml);

    return xpath.select1(path, xmlDocument) as Element;
  },

  /**
   * Returns the XML Node value from a specific path
   * @param {string} xml
   * @param {string} path
   * @return {string|null}
   */
  getNodeValue(xml: string, path: string): string|null {
    const xmlDocument = this.getXmlDocument(xml);

    if (xpath.select1(`boolean(${path})`, xmlDocument)) {
      return xpath.select1(`string(${path})`, xmlDocument) as string;
    }
    return null;
  },

  /**
   * Returns if the XML is parseable
   * @param {string} xml
   * @return {boolean}
   */
  isValid(xml: string): boolean {
    const result = XMLValidator.validate(xml, {
      allowBooleanAttributes: true,
    });

    return result === true;
  },

  /**
   * Returns if a Node Element is empty
   * @param {Element} element
   * @return {boolean}
   */
  isEmpty(element: Element): boolean {
    if (element.nodeName === '#text') {
      return true;
    }

    if (element.childNodes.length > 0) {
      for (let o: number = 0; o < element.childNodes.length; o++) {
        if (!this.isEmpty(element.childNodes[o] as Element)) {
          return false;
        }
      }

      return true;
    }

    let xmlString: string = `<${element.nodeName}`;

    for (let a: number = 0; a < element.attributes.length; a++) {
      if (element.attributes[a].nodeName === 'xlink:href') {
        xmlString += ' xmlns:xlink="http://www.w3.org/1999/xlink"';
      }
      xmlString += ` ${element.attributes[a].nodeName}="${element.attributes[a].nodeValue}"`;
    }
    xmlString += '/>';

    return element.toString() === xmlString;
  },
};
