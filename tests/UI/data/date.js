const today = new Date();
const futureDate = new Date();
const pastDate = new Date();
const mm = (`0${today.getMonth() + 1}`).slice(-2); // Current month
const dd = (`0${today.getDate()}`).slice(-2); // Current day
const yyyy = today.getFullYear(); // Current year
futureDate.setFullYear(today.getFullYear() + 1); // Future year
pastDate.setFullYear(today.getFullYear() - 1); // Future year

module.exports = {
  // Get today date format 'mm/dd/yyyy'
  DateStartTwoDigitMonth: {
    todayDate: `${mm}/${dd}/${yyyy}`,
  },

  DateStartFourDigitYear: {
    // Get today date format 'yyy/mm/dd'
    todayDateFormat1: `${yyyy}/${mm}/${dd}`,

    // Get today format (yyyy-mm-dd)
    todayDateFormat2: today.toISOString().slice(0, 10),

    // Get future date format (yyyy-mm-dd)
    futureDateFormat2: futureDate.toISOString().slice(0, 10),

    // Get past date format (yyyy-mm-dd)
    pastDateFormat2: pastDate.toISOString().slice(0, 10),
  },
};
