const fs = require('fs');
const pdfJs = require('pdfjs-dist/legacy/build/pdf.js');
const imgGen = require('js-image-generator');
const createCsvWriter = require('csv-writer').createObjectCsvWriter;
const path = require('path');

/**
 * @module FilesHelper
 * @description Helper to wrap functions that uses fs, pdfjs and js-image-generator libraries
 */
module.exports = {
  /**
   * Delete File if exist
   * @param filePath {string} Filepath to delete
   * @return {Promise<void>}
   */
  async deleteFile(filePath) {
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
  async doesFileExist(filePath, attempt = 5000) {
    let found = false;

    for (let i = 0; i <= attempt && !found; i += 100) {
      await (new Promise(resolve => setTimeout(resolve, 100)));
      found = await fs.existsSync(filePath);
    }

    return found;
  },

  /**
   * Get page text from PDF
   * @param pdf {PDFDocumentLoadingTask} PDF loaded with pdfjs
   * @param pageNo {number} Page number of the PDF file
   * @return {string} Text in PDF page
   */
  async getPageTextFromPdf(pdf, pageNo) {
    const page = await pdf.getPage(pageNo);
    const tokenizedText = await page.getTextContent();

    return tokenizedText.items.map(token => token.str);
  },

  /**
   * Check text in PDF
   * @param filePath {string} Path of the PDF file
   * @param text {string} Text to check on the file
   * @returns {Promise<boolean>}
   */
  async isTextInPDF(filePath, text) {
    const pdf = await pdfJs.getDocument(filePath).promise;
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
  async getImageNumberInPDF(filePath) {
    const pdf = await pdfJs.getDocument(filePath).promise;
    const nbrPages = pdf.numPages;
    let imageNumber = 0;

    for (let pageNo = 1; pageNo <= nbrPages; pageNo += 1) {
      const page = await pdf.getPage(nbrPages);
      /* eslint-disable no-loop-func */
      await page.getOperatorList().then(async (ops) => {
        for (let i = 0; i < ops.fnArray.length; i++) {
          if (ops.fnArray[i] === pdfJs.OPS.paintImageXObject) {
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
  async generateReportFilename() {
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
  async createDirectory(path) {
    if (!fs.existsSync(path)) await fs.mkdirSync(path);
  },
  /**
   * Create file with content
   * @param path {string} Path of the directory where to create
   * @param filename {string} Name of the file to create
   * @param content {string} Content to write on the file
   * @return {Promise<void>}
   */
  async createFile(path, filename, content) {
    await fs.writeFile(`${path}/${filename}`, content, (err) => {
      if (err) {
        throw new Error(err);
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
  async isTextInFile(filePath, textToCheckWith, ignoreSpaces = false, ignoreTimeZone = false, encoding = 'utf8') {
    let fileText = await fs.readFileSync(filePath, encoding);
    let text = textToCheckWith;

    if (ignoreSpaces) {
      fileText = await fileText.replace(/\s/g, '');
      text = await text.replace(/\s/g, '');
    }
    if (ignoreTimeZone) {
      fileText = await fileText.replace(/\?time=\d+/g, '', '');
      text = await text.replace(/\?time=\d+/g, '', '');
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
  async generateImage(imageName, width = 200, height = 200, quality = 1) {
    await imgGen.generateImage(width, height, quality, (err, image) => {
      fs.writeFileSync(imageName, image.data);
    });
  },

  /**
   * Rename files
   * @param oldPath {string} Old path of the file
   * @param newPath {string} New path of the file
   * @return {Promise<void>}
   */
  async renameFile(oldPath, newPath) {
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
  async createCSVFile(path, fileName, data) {
    await this.createFile(path, fileName, '');
    const csvWriter = await createCsvWriter({path: fileName, header: data.header, fieldDelimiter: ';'});
    await csvWriter.writeRecords(data.records);
  },

  /**
   * Create a random SVG file
   * @param path {string} Path of the file
   * @param fileName {string} Name of the file to create
   * @returns {Promise<void>}
   */
  async createSVGFile(path, fileName) {
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
  async getFilePathAutomaticallyGenerated(folderPath, filename) {
    return path.resolve(__dirname, '../../../', folderPath, filename);
  },
};
