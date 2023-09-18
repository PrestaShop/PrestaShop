const today: Date = new Date();

/**
 * @module basicHelper
 * @description Basic helper used to wrap basic methods don't used on pages
 */
export default {
  /**
   * Sort array of strings
   * @param arrayToSort {string[]} Array to sort
   * @return {Promise<string[]}
   */
  async sortArray(arrayToSort: string[]): Promise<string[]> {
    return arrayToSort.sort((a: string, b: string): number => a.localeCompare(b));
  },

  /**
   * Sort array of numbers
   * @param arrayToSort {number[]} Array to sort
   * @return {Promise<number[]}
   */
  async sortArrayNumber(arrayToSort: number[]): Promise<number[]> {
    return arrayToSort.sort((a: number, b: number): number => a - b);
  },

  /**
   * Sort array of dates
   * @param arrayToSort {string[]} Array to sort
   * @return {Promise<string[]}
   */
  async sortArrayDate(arrayToSort: string[]): Promise<string[]> {
    return arrayToSort.sort((a: string, b: string): number => new Date(a).getTime() - new Date(b).getTime());
  },

  /**
   * Calculate percentage
   * @param num {number|float} Number to do the percentage
   * @param percentage {number} Percentage value
   * @returns {Promise<number|float>}
   */
  async percentage(num: number, percentage: number): Promise<number> {
    return (num / 100) * percentage;
  },

  /**
   * Calculate age
   * @param birthdate {string} Date of birth
   * @returns {Promise<number>}
   */
  async age(birthdate: Date): Promise<number> {
    const age = today.getFullYear() - birthdate.getFullYear();

    if (today.getMonth() < birthdate.getMonth()
      || (today.getMonth() === birthdate.getMonth() && today.getDate() < birthdate.getDate())) {
      return age - 1;
    }

    return age;
  },

  /**
   * Make a string's first character uppercase
   * @param value {string}
   * @returns {string}
   */
  capitalize(value: string): string {
    return value.charAt(0).toUpperCase() + value.slice(1);
  },
};
