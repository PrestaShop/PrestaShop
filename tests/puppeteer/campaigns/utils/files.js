const fs = require('fs');
const PDFJS = require('pdfjs-dist');

module.exports = {
  /**
   * Delete File if exist
   * @param pathToFile
   * @return {Promise<void>}
   */
  async deleteFile(pathToFile) {
    if (fs.existsSync(pathToFile)) fs.unlinkSync(pathToFile);
  },

  /**
   * Check File existence
   * @param fileName
   * @param timeDelay
   * @param isPartialName
   * @param fileExtension
   * @return boolean, true if exist, false if not
   */
  async doesFileExist(fileName, timeDelay = 5000, isPartialName = false, fileExtension = '') {
    let found = false;
    for (let i = 0; i <= timeDelay && !found; i += 100) {
      await (new Promise(resolve => setTimeout(resolve, 10)));
      if (isPartialName) {
        found = (await fs
          .readdirSync(global.BO.DOWNLOAD_PATH)
          .filter(fn => fn.startsWith(fileName) && fn.endsWith(fileExtension))
        ) !== [];
      } else {
        found = await fs.existsSync(`${global.BO.DOWNLOAD_PATH}/${fileName}`);
      }
    }
    return found;
  },

  /**
   * Get page text from PDF
   * @param pdf
   * @param pageNo
   * @return text, text in PDF file
   */
  async getPageTextFromPdf(pdf, pageNo) {
    const page = await pdf.getPage(pageNo);
    const tokenizedText = await page.getTextContent();
    return tokenizedText.items.map(token => token.str);
  },

  /**
   * Check text in PDF
   * @param fileName
   * @param text
   * @return boolean, true if text exist, false if not
   */
  async isTextInPDF(fileName, text) {
    const pdf = await PDFJS.getDocument(`${global.BO.DOWNLOAD_PATH}/${fileName}`).promise;
    const maxPages = pdf.numPages;
    const pageTextPromises = [];
    for (let pageNo = 1; pageNo <= maxPages; pageNo += 1) {
      pageTextPromises.push(this.getPageTextFromPdf(pdf, pageNo));
    }
    const pageTexts = await Promise.all(pageTextPromises);
    return (pageTexts.join(' ').indexOf(text) !== -1);
  },

  /**
   * Get image number from PDF
   * @param fileName
   * @return imageNumber, number of images in PDF file
   */
  async getImageNumberInPDF(fileName) {
    const pdf = await PDFJS.getDocument(`${global.BO.DOWNLOAD_PATH}/${fileName}`).promise;
    const nbrPages = pdf.numPages;
    let imageNumber = 0;
    for (let pageNo = 1; pageNo <= nbrPages; pageNo += 1) {
      const page = await pdf.getPage(nbrPages);
      /* eslint-disable no-loop-func */
      await page.getOperatorList().then(async (ops) => {
        for (let i = 0; i < ops.fnArray.length; i++) {
          if (ops.fnArray[i] === PDFJS.OPS.paintImageXObject) {
            imageNumber += 1;
          }
        }
      });
      /* eslint-enable no-loop-func */
    }
    return imageNumber;
  },
  /**
   * Create file with content
   * @param path
   * @param filename
   * @param content
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
   * @param fileName
   * @param textToCheckWith
   * @param ignoreSpaces, true to delete all spaces before the check
   * @param ignoreTimeZone, true to delete timezone string added to some image url
   * @return {Promise<boolean>}
   */
  async isTextInFile(fileName, textToCheckWith, ignoreSpaces = false, ignoreTimeZone = false) {
    let fileText = await fs.readFileSync(`${global.BO.DOWNLOAD_PATH}/${fileName}`, 'utf8');
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
   * Get fileName with
   * @param dir
   * @param fileStartWith
   * @param fileEndWith
   * @return {Promise<string>}
   */
  async getFileNameFromDir(dir, fileStartWith = '', fileEndWith = '') {
    const filesNames = await fs.readdirSync(dir).filter(fn => fn.startsWith(fileStartWith) && fn.endsWith(fileEndWith));
    if (filesNames.length === 0) {
      throw Error('File was not found in directory');
    }
    return filesNames[0];
  },
};
