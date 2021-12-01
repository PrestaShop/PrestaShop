const today = new Date();
const futureDate = new Date();
const pastDate = new Date();
const mm = (`0${today.getMonth() + 1}`).slice(-2); // Current month
const dd = (`0${today.getDate()}`).slice(-2); // Current day
const yyyy = today.getFullYear(); // Current year
futureDate.setFullYear(today.getFullYear() + 1); // Future year
pastDate.setFullYear(today.getFullYear() - 1); // Future year

module.exports = {
  /**
   * Get date on some formats and times
   * @param format {string} Format of the date
   * @param time {string} Time of the date (today, future, past)
   * @returns {Promise<string>}
   */
  async getDate(format, time = 'today') {
    let date = '';
    switch (format) {
      case 'mm/dd/yyyy':
        if (time === 'today') {
          date = `${mm}/${dd}/${yyyy}`;
        }
        break;
      case 'yyyy/mm/dd':
        if (time === 'today') {
          date = `${yyyy}/${mm}/${dd}`;
        }
        break;
      case 'yyyy-mm-dd':
        if (time === 'today') {
          date = today.toISOString().slice(0, 10);
        }
        if (time === 'future') {
          date = futureDate.toISOString().slice(0, 10);
        }
        if (time === 'past') {
          date = pastDate.toISOString().slice(0, 10);
        }
        break;
      default:
      // Do nothing
    }
    return date;
  },
};
