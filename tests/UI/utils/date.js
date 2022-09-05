/**
 * Get base date from which we get the format
 * @param dateTime
 * @return {Date}
 */
const getBaseDate = (dateTime = 'today') => {
  let baseDate = new Date();

  // Set base date : Dates from today, future, past or exact date passed to the function
  switch (dateTime) {
    case 'past':
      baseDate.setFullYear(baseDate.getFullYear() - 1);
      break;
    case 'future':
      baseDate.setFullYear(baseDate.getFullYear() + 1);
      break;
    case 'today':
      break;
    default:
      baseDate = dateTime;
  }

  return baseDate;
};

/**
 * Get date on some formats and times
 * @param format {string} Format of the date
 * @param dateTime {string} Time of the date (present, future, past)
 * @returns {string}
 */
const getDateFormat = (format, dateTime = 'today') => {
  const date = getBaseDate(dateTime);

  // Get day, month and year depending
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
};

module.exports = {getDateFormat};
