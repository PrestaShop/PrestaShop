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
};
