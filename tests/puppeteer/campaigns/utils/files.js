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
   * @return boolean, true if exist, false if not
   */
  async checkFileExistence(fileName, timeDelay = 5000) {
    let found = false;
    for (let i = 0; i <= timeDelay && !found; i += 10) {
      await (new Promise(resolve => setTimeout(resolve, 10)));
      found = await fs.existsSync(`${global.BO.DOWNLOAD_PATH}/${fileName}`);
    }
    return found;
  },

  /**
   * Get page text
   * @param pdf
   * @param pageNo
   * @return text, text in PDF file
   */
  async getPageText(pdf, pageNo) {
    const page = await pdf.getPage(pageNo);
    const tokenizedText = await page.getTextContent();
    return tokenizedText.items.map(token => token.str);
  },

  /**
   * Check text in PDF
   * @param downloadPath
   * @param fileName
   * @param text
   * @return boolean, true if text exist, false if not
   */
  async checkTextInPDF(downloadPath, fileName, text) {
    const pdf = await PDFJS.getDocument(`${downloadPath}/${fileName}`).promise;
    const maxPages = pdf.numPages;
    const pageTextPromises = [];
    for (let pageNo = 1; pageNo <= maxPages; pageNo += 1) {
      pageTextPromises.push(this.getPageText(pdf, pageNo));
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
};
