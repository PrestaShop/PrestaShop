const fs = require('fs');
const pdfJs = require('pdfjs-dist/es5/build/pdf.js');
const imgGen = require('js-image-generator');

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
   * @return {boolean}
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
   * @return {number}
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
   * @return {Promise<boolean>}
   */
  async isTextInFile(filePath, textToCheckWith, ignoreSpaces = false, ignoreTimeZone = false) {
    let fileText = await fs.readFileSync(filePath, 'utf8');
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
};
