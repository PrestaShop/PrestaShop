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
   * @param downloadPath
   * @param fileName
   * @param timeDelay
   * @return boolean, true if exist, false if not
   */
  async checkFileExistence(downloadPath, fileName, timeDelay = 5000) {
    let found = false;
    for (let i = 0; i <= timeDelay && !found; i += 10) {
      await (new Promise(resolve => setTimeout(resolve, 10)));
      found = await fs.existsSync(`${downloadPath}/${fileName}`);
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
};
