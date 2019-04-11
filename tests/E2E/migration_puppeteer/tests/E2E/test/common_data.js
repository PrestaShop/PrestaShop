'use strict';
module.exports = {

  getCustomDate: function (numberOfDay) {
    let today = new Date();
    today.setDate(today.getDate() + numberOfDay);
    let dd = today.getDate();
    let mm = today.getMonth() + 1; //January is 0!
    let yyyy = today.getFullYear();

    if (dd < 10) {
      dd = '0' + dd;
    }

    if (mm < 10) {
      mm = '0' + mm;
    }

    return yyyy + '-' + mm + '-' + dd;
  },
};