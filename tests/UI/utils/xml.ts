import {DOMParser} from '@xmldom/xmldom';
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
   * @return {string}
   */
  getNodeValue(xml: string, path: string): string {
    const xmlDocument = this.getXmlDocument(xml);

    return xpath.select1(`string(${path})`, xmlDocument) as string;
  },
};
