const fs = require('fs');

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
   * @return boolean, true if exist, false if not
   */
  async checkFileExistence(downloadPath, fileName) {
    return fs.existsSync(`${downloadPath}/${fileName}`);
  },

  async waitForFileToDownload(downloadPath) {
    console.log('Waiting to download file...');
    let fileName;
    while (!fileName || fileName.endsWith('.crdownload')) {
      fileName = fs.readdirSync(downloadPath);
    }
    return fileName;
  },
};
