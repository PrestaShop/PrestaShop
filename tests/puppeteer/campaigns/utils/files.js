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
  async isFileExist(downloadPath, fileName) {
    return fs.existsSync(`${downloadPath}/${fileName}`);
  },
};
