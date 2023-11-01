export default {
  /**
   * Get base date from which we get the format
   * @param dateTime
   * @return {Date}
   */
  getBaseDate(dateTime: string = 'today'): Date {
    const baseDate = new Date();

    // Set base date : Dates from today, future, past or exact date passed to the function
    switch (dateTime) {
      case 'past':
        baseDate.setFullYear(baseDate.getFullYear() - 1);
        break;
      case 'yesterday':
        baseDate.setDate(baseDate.getDate() - 1);
        break;
      case 'today':
        break;
      case 'tomorrow':
        baseDate.setDate(baseDate.getDate() + 1);
        break;
      case 'future':
        baseDate.setFullYear(baseDate.getFullYear() + 1);
        break;

      default:
        throw new Error(`The dateTime ${dateTime} is not handled by this helper yet`);
    }

    return baseDate;
  },

  /**
   * Get date on some formats and times
   * @param format {string} Format of the date
   * @param dateTime {string} Time of the date (present, future, past)
   * @returns {string}
   */
  getDateFormat(format: string, dateTime: string = 'today'): string {
    const date = this.getBaseDate(dateTime);

    // Get day, month and year
    const mm = (`0${date.getMonth() + 1}`).slice(-2); // Current month
    const dd = (`0${date.getDate()}`).slice(-2); // Current day
    const yyyy = date.getFullYear(); // Year

    switch (format) {
      case 'mm/dd/yyyy':
        return `${mm}/${dd}/${yyyy}`;

      case 'yyyy/mm/dd':
        return `${yyyy}/${mm}/${dd}`;

      case 'yyyy-mm-dd':
        return date.toISOString().slice(0, 10);

      default:
        throw new Error(`The format ${format} is not handled by this helper yet`);
    }
  },

  /**
   * Set dateTime format
   * @param format {string} Format of the date
   * @param dateTime {string} Date time to change format
   * @returns {string}
   */
  setDateFormat(format: string, dateTime: string): string {
    const date = new Date(dateTime);

    const mm = (`0${date.getMonth() + 1}`).slice(-2); // Current month
    const dd = (`0${date.getDate()}`).slice(-2); // Current day
    const yyyy = date.getFullYear(); // Year

    switch (format) {
      case 'mm/dd/yyyy':
        return `${mm}/${dd}/${yyyy} ${dateTime.slice(11, 20)}`;

      case 'yyyy/mm/dd':
        return `${yyyy}/${mm}/${dd} ${dateTime.slice(11, 20)}`;

      case 'yyyy-mm-dd':
        return `${date.toISOString().slice(0, 10)} ${dateTime.slice(11, 20)}`;

      default:
        throw new Error(`The format ${format} is not handled by this helper yet`);
    }
  },
};
