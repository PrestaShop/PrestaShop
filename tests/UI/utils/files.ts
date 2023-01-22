import {createObjectCsvWriter} from 'csv-writer';
import fs from 'fs';
import imgGen from 'js-image-generator';
import path from 'path';
import {getDocument, OPS, PDFDocumentProxy} from 'pdfjs-dist/legacy/build/pdf.js';
import {TextItem, TextMarkedContent} from 'pdfjs-dist/types/src/display/api';

/**
 * @module FilesHelper
 * @description Helper to wrap functions that uses fs, pdfjs and js-image-generator libraries
 */
export default {
  /**
   * Delete File if exist
   * @param filePath {string} Filepath to delete
   * @return {Promise<void>}
   */
  async deleteFile(filePath: string): Promise<void> {
    if (fs.existsSync(filePath)) {
      fs.unlinkSync(filePath);
    }
  },

  /**
   * Check if file was download in path
   * @param filePath {string} Filepath to check
   * @param attempt {number} Number of attempt to check for the file
   * @returns {Promise<boolean>}
   */
  async doesFileExist(filePath: string, attempt: number = 5000): Promise<boolean> {
    let found = false;

    for (let i = 0; i <= attempt && !found; i += 100) {
      await (new Promise((resolve) => {
        setTimeout(resolve, 100);
      }));
      found = fs.existsSync(filePath);
    }

    return found;
  },

  /**
   * Get page text from PDF
   * @param pdf {PDFDocumentProxy} PDF loaded with pdfjs
   * @param pageNo {number} Page number of the PDF file
   * @return {Promise<string[]>} Text in PDF page
   */
  async getPageTextFromPdf(pdf: PDFDocumentProxy, pageNo: number): Promise<string[]> {
    const page = await pdf.getPage(pageNo);
    const tokenizedText = await page.getTextContent();

    return tokenizedText.items.map((token: TextItem|TextMarkedContent): string => ('str' in token ? token.str : ''));
  },

  /**
   * Check text in PDF
   * @param filePath {string} Path of the PDF file
   * @param text {string} Text to check on the file
   * @returns {Promise<boolean>}
   */
  async isTextInPDF(filePath: string, text: string): Promise<boolean> {
    const pdf = await getDocument(filePath).promise;
    const maxPages = pdf.numPages;
    const pageTextPromises = [];

    for (let pageNo = 1; pageNo <= maxPages; pageNo += 1) {
      pageTextPromises.push(this.getPageTextFromPdf(pdf, pageNo));
    }

    const pageTexts = await Promise.all(pageTextPromises);

    return (pageTexts.join(' ').indexOf(text) !== -1);
  },

  /**
   * Get quantity of images on the PDF
   * @param filePath {string} FilePath of the PDF file
   * @return {Promise<number>}
   */
  async getImageNumberInPDF(filePath: string): Promise<number> {
    const pdf = await getDocument(filePath).promise;
    const nbrPages = pdf.numPages;
    let imageNumber = 0;

    for (let pageNo = 1; pageNo <= nbrPages; pageNo += 1) {
      const page = await pdf.getPage(nbrPages);
      /* eslint-disable no-loop-func */
      await page.getOperatorList().then(async (ops) => {
        for (let i = 0; i < ops.fnArray.length; i++) {
          if (ops.fnArray[i] === OPS.paintImageXObject) {
            imageNumber += 1;
          }
        }
      });
      /* eslint-enable no-loop-func */
    }

    return imageNumber;
  },
  /**
   * Generate report filename
   * @return {Promise<string>}
   */
  async generateReportFilename(): Promise<string> {
    const curDate = new Date();

    return `report-${
      curDate.toJSON().slice(0, 10)}-${
      curDate.getHours()}-${
      curDate.getMinutes()}-${
      curDate.getSeconds()}`;
  },
  /**
   * Create directory if not exist
   * @param path {string} Path of the directory to create
   * @return {Promise<void>}
   */
  async createDirectory(path: string): Promise<void> {
    if (!fs.existsSync(path)) await fs.mkdirSync(path);
  },
  /**
   * Create file with content
   * @param path {string} Path of the directory where to create
   * @param filename {string} Name of the file to create
   * @param content {string} Content to write on the file
   * @return {Promise<void>}
   */
  async createFile(path: string, filename: string, content: string): Promise<void> {
    await fs.writeFile(`${path}/${filename}`, content, (err: Error|null) => {
      if (err) {
        throw err;
      }
    });
  },
  /**
   * Check text in file
   * @param filePath {string} Filepath to check
   * @param textToCheckWith {string} Text to check on the file
   * @param ignoreSpaces {boolean} True to delete all spaces before the check
   * @param ignoreTimeZone {boolean} True to delete timezone string added to some image url
   * @param encoding {string} Encoding for the file
   * @return {Promise<boolean>}
   */
  async isTextInFile(
    filePath: string,
    textToCheckWith: string,
    ignoreSpaces: boolean = false,
    ignoreTimeZone: boolean = false,
    encoding: BufferEncoding = 'utf8',
  ): Promise<boolean> {
    let fileText: string = await fs.readFileSync(filePath, {
      encoding,
    });
    let text: string = textToCheckWith;

    if (ignoreSpaces) {
      fileText = fileText.replace(/\s/g, '');
      text = text.replace(/\s/g, '');
    }
    if (ignoreTimeZone) {
      fileText = fileText.replace(/\?time=\d+/g, '');
      text = text.replace(/\?time=\d+/g, '');
    }
    return fileText.includes(text);
  },

  /**
   * Generate image with js-image-generator
   * @param imageName {string} Filename/Filepath of the image
   * @param width {number} Width chosen for the image
   * @param height {number} Height chosen for the image
   * @param quality {number} Quality chosen for the image
   * @return {Promise<void>}
   */
  async generateImage(imageName: string, width: number = 200, height: number = 200, quality:number = 1): Promise<void> {
    await imgGen.generateImage(width, height, quality, (err: Error, image: object) => {
      if ('data' in image) {
        fs.writeFileSync(imageName, image.data);
      }
    });
  },

  /**
   * Rename files
   * @param oldPath {string} Old path of the file
   * @param newPath {string} New path of the file
   * @return {Promise<void>}
   */
  async renameFile(oldPath: string, newPath: string): Promise<void> {
    await fs.rename(oldPath, newPath, (err) => {
      if (err) throw err;
    });
  },

  /**
   * Create csv file
   * @param path {string} Path of the file
   * @param fileName {string} Name of the file to create
   * @param data {Object} Data to create csv file
   * @returns {Promise<void>}
   */
  async createCSVFile(path: string, fileName: string, data: object): Promise<void> {
    await this.createFile(path, fileName, '');
    if ('header' in data && 'records' in data) {
      const csvWriter = createObjectCsvWriter({path: fileName, header: data.header, fieldDelimiter: ';'});
      await csvWriter.writeRecords(data.records);
    }
  },

  /**
   * Create a random SVG file
   * @param path {string} Path of the file
   * @param fileName {string} Name of the file to create
   * @returns {Promise<void>}
   */
  async createSVGFile(path: string, fileName: string): Promise<void> {
    const centerX = Math.floor(Math.random() * 15);
    const centerY = Math.floor(Math.random() * 15);
    const radius = Math.floor(Math.random() * 10);
    const style = `fill:rgb(${Math.floor(Math.random() * 255)},${Math.floor(Math.random() * 255)},`
      + `${Math.floor(Math.random() * 255)});`;

    let svg = '<?xml version="1.0" encoding="UTF-8" standalone="no"?>'
      + '<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">'
      + '<svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">';

    for (let x = 0; x < 12; x++) {
      svg += `<circle cx="${centerX + (x * ((radius * 2) + 5))}" cy="${centerY}" r="${radius}" style="${style}"/>`;
    }
    svg += '</svg>';

    await fs.writeFile(`${path}/${fileName}`, svg, (err) => {
      if (err) throw err;
    });
  },

  /**
   * Get the path of the file automatically generated
   * @param folderPath {string} Path of the folder where the file exists
   * @param filename {string} Path of the file automatically created
   * @returns {Promise<string>}
   */
  async getFilePathAutomaticallyGenerated(folderPath: string, filename: string): Promise<string> {
    return path.resolve(__dirname, '../../../', folderPath, filename);
  },
};
