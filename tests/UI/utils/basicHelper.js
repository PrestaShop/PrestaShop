const today = new Date();

/**
 * @module basicHelper
 * @description Basic helper used to wrap basic methods don't used on pages
 */
module.exports = {
  /**
   * Sort array of strings or numbers
   * @param arrayToSort {Array<string|number>} Array to sort
   * @param isFloat {boolean} True if array values type are float
   * @param isDate {boolean} True if array values type are date
   * @return {Promise<Array<string|number>>}
   */
  async sortArray(arrayToSort, isFloat = false, isDate = false) {
    if (isFloat) {
      return arrayToSort.sort((a, b) => a - b);
    }

    if (isDate) {
      return arrayToSort.sort((a, b) => new Date(a) - new Date(b));
    }

    return arrayToSort.sort((a, b) => a.localeCompare(b));
  },

  /**
   * Calculate percentage
   * @param num {number} Number to do the percentage
   * @param percentage {number} Percentage value
   * @returns {Promise<number>}
   */
  async percentage(num, percentage) {
    return (num / 100) * percentage;
  },

  /**
   * Calculate age
   * @param birthdate {string} Date of birth
   * @returns {Promise<number>}
   */
  async age(birthdate) {
    return (today.getFullYear() - birthdate.getFullYear());
  },
};
