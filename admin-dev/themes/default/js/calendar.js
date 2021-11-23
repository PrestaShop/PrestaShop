/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

/* eslint-disable */
Date.prototype.addDays = function (value) {
  this.setDate(this.getDate() + value);

  return this;
};

Date.prototype.addMonths = function (value) {
  const date = this.getDate();
  this.setMonth(this.getMonth() + value);

  if (this.getDate() < date) {
    this.setDate(0);
  }

  return this;
};

Date.prototype.addWeeks = function (value) {
  this.addDays(value * 7);

  return this;
};

Date.prototype.addYears = function (value) {
  const month = this.getMonth();
  this.setFullYear(this.getFullYear() + value);

  if (month < this.getMonth()) {
    this.setDate(0);
  }

  return this;
};

Date.parseDate = function (date, format) {
  if (format === undefined) format = 'Y-m-d';

  const formatSeparator = format.match(/[.\/\-\s].*?/);
  const formatParts = format.split(/\W+/);
  const parts = date.split(formatSeparator);
  var date = new Date();

  if (parts.length === formatParts.length) {
    date.setHours(0);
    date.setMinutes(0);
    date.setSeconds(0);
    date.setMilliseconds(0);

    for (let i = 0; i < formatParts.length; i++) {
      switch (formatParts[i]) {
        case 'dd':
        case 'd':
        case 'j':
          date.setDate(parseInt(parts[i], 10) || 1);
          break;

        case 'mm':
        case 'm':
          date.setMonth((parseInt(parts[i], 10) || 1) - 1);
          break;

        case 'yy':
        case 'y':
          date.setFullYear(2000 + (parseInt(parts[i], 10) || 1));
          break;

        case 'yyyy':
        case 'Y':
          date.setFullYear(parseInt(parts[i], 10) || 1);
          break;
      }
    }
  }

  return date;
};

Date.prototype.subDays = function (value) {
  this.setDate(this.getDate() - value);

  return this;
};

Date.prototype.subMonths = function (value) {
  const date = this.getDate();
  this.setMonth(this.getMonth() - value);

  if (this.getDate() < date) {
    this.setDate(0);
  }

  return this;
};

Date.prototype.subWeeks = function (value) {
  this.subDays(value * 7);

  return this;
};

Date.prototype.subYears = function (value) {
  const month = this.getMonth();
  this.setFullYear(this.getFullYear() - value);

  if (month < this.getMonth()) {
    this.setDate(0);
  }

  return this;
};

Date.prototype.format = function (format) {
  if (format === undefined) return this.toString();

  const formatSeparator = format.match(/[.\/\-\s].*?/);
  const formatParts = format.split(/\W+/);
  let result = '';

  for (let i = 0; i < formatParts.length; i++) {
    switch (formatParts[i]) {
      case 'd':
      case 'j':
        result += this.getDate() + formatSeparator;
        break;

      case 'dd':
        result += (this.getDate() < 10 ? '0' : '') + this.getDate() + formatSeparator;
        break;

      case 'm':
        result += (this.getMonth() + 1) + formatSeparator;
        break;

      case 'mm':
        result += (this.getMonth() < 9 ? '0' : '') + (this.getMonth() + 1) + formatSeparator;
        break;

      case 'yy':
      case 'y':
        result += this.getFullYear() + formatSeparator;
        break;

      case 'yyyy':
      case 'Y':
        result += this.getFullYear() + formatSeparator;
        break;
    }
  }

  return result.slice(0, -1);
};


function updatePickerFromInput() {
  datepickerStart.setStart($('#date-start').val());
  datepickerStart.setEnd($('#date-end').val());
  datepickerStart.update();
  datepickerEnd.setStart($('#date-start').val());
  datepickerEnd.setEnd($('#date-end').val());
  datepickerEnd.update();

  $('#date-start').trigger('change');

  if ($('#datepicker-compare').attr('checked')) {
    if ($('#compare-options').val() == 1) setPreviousPeriod();

    if ($('#compare-options').val() == 2) setPreviousYear();

    datepickerStart.setStartCompare($('#date-start-compare').val());
    datepickerStart.setEndCompare($('#date-end-compare').val());
    datepickerEnd.setStartCompare($('#date-start-compare').val());
    datepickerEnd.setEndCompare($('#date-end-compare').val());
    datepickerStart.setCompare(true);
    datepickerEnd.setCompare(true);
  }
}

/* eslint-enable */

function setDayPeriod() {
  const date = new Date();
  $('#date-start').val(date.format($('#date-start').data('date-format')));
  $('#date-end').val(date.format($('#date-end').data('date-format')));
  $('#date-start').trigger('change');

  updatePickerFromInput();
  $('#datepicker-from-info').html($('#date-start').val());
  $('#datepicker-to-info').html($('#date-end').val());
  $('#preselectDateRange').val('day');
  $('button[name="submitDateRange"]').click();
}

function setPreviousDayPeriod() {
  let date = new Date();
  date = date.subDays(1);
  $('#date-start').val(date.format($('#date-start').data('date-format')));
  $('#date-end').val(date.format($('#date-end').data('date-format')));
  $('#date-start').trigger('change');

  updatePickerFromInput();
  $('#datepicker-from-info').html($('#date-start').val());
  $('#datepicker-to-info').html($('#date-end').val());
  $('#preselectDateRange').val('prev-day');
  $('button[name="submitDateRange"]').click();
}

function setMonthPeriod() {
  let date = new Date();
  $('#date-end').val(date.format($('#date-end').data('date-format')));
  date = new Date(date.setDate(1));
  $('#date-start').val(date.format($('#date-start').data('date-format')));
  $('#date-start').trigger('change');

  updatePickerFromInput();
  $('#datepicker-from-info').html($('#date-start').val());
  $('#datepicker-to-info').html($('#date-end').val());
  $('#preselectDateRange').val('month');
  $('button[name="submitDateRange"]').click();
}

function setPreviousMonthPeriod() {
  let date = new Date();
  date = new Date(date.getFullYear(), date.getMonth(), 0);
  $('#date-end').val(date.format($('#date-end').data('date-format')));
  date = new Date(date.setDate(1));
  $('#date-start').val(date.format($('#date-start').data('date-format')));
  $('#date-start').trigger('change');

  updatePickerFromInput();
  $('#datepicker-from-info').html($('#date-start').val());
  $('#datepicker-to-info').html($('#date-end').val());
  $('#preselectDateRange').val('prev-month');
  $('button[name="submitDateRange"]').click();
}

function setYearPeriod() {
  let date = new Date();
  $('#date-end').val(date.format($('#date-end').data('date-format')));
  date = new Date(date.getFullYear(), 0, 1);
  $('#date-start').val(date.format($('#date-start').data('date-format')));
  $('#date-start').trigger('change');

  updatePickerFromInput();
  $('#datepicker-from-info').html($('#date-start').val());
  $('#datepicker-to-info').html($('#date-end').val());
  $('#preselectDateRange').val('year');
  $('button[name="submitDateRange"]').click();
}

function setPreviousYearPeriod() {
  let date = new Date();
  date = new Date(date.getFullYear(), 11, 31);
  date = date.subYears(1);
  $('#date-end').val(date.format($('#date-end').data('date-format')));
  date = new Date(date.getFullYear(), 0, 1);
  $('#date-start').val(date.format($('#date-start').data('date-format')));
  $('#date-start').trigger('change');

  updatePickerFromInput();
  $('#datepicker-from-info').html($('#date-start').val());
  $('#datepicker-to-info').html($('#date-end').val());
  $('#preselectDateRange').val('prev-year');
  $('button[name="submitDateRange"]').click();
}

function setPreviousPeriod() {
  const startDate = Date.parseDate($('#date-start').val(), $('#date-start').data('date-format')).subDays(1);
  const endDate = Date.parseDate($('#date-end').val(), $('#date-end').data('date-format')).subDays(1);

  const diff = endDate - startDate;
  const startDateCompare = new Date(startDate - diff);

  $('#date-end-compare').val(startDate.format($('#date-end-compare').data('date-format')));
  $('#date-start-compare').val(startDateCompare.format($('#date-start-compare').data('date-format')));
}

function setPreviousYear() {
  const startDate = Date.parseDate($('#date-start').val(), $('#date-start').data('date-format')).subYears(1);
  const endDate = Date.parseDate($('#date-end').val(), $('#date-end').data('date-format')).subYears(1);
  $('#date-start-compare').val(startDate.format($('#date-start').data('date-format')));
  $('#date-end-compare').val(endDate.format($('#date-start').data('date-format')));
}

let datepickerStart;
let datepickerEnd;

$(document).ready(() => {
  // Instanciate datepickers
  datepickerStart = $('.datepicker1').daterangepicker({
    dates: window.translated_dates,
    weekStart: 1,
    start: $('#date-start').val(),
    end: $('#date-end').val(),
  }).on('changeDate', (ev) => {
    if (ev.date.valueOf() >= datepickerEnd.date.valueOf()) {
      datepickerEnd.setValue(ev.date.setMonth(ev.date.getMonth() + 1));
    }
  }).data('daterangepicker');

  datepickerEnd = $('.datepicker2').daterangepicker({
    dates: window.translated_dates,
    weekStart: 1,
    start: $('#date-start').val(),
    end: $('#date-end').val(),
  }).on('changeDate', (ev) => {
    if (ev.date.valueOf() <= datepickerStart.date.valueOf()) {
      datepickerStart.setValue(ev.date.setMonth(ev.date.getMonth() - 1));
    }
  }).data('daterangepicker');

  // Set first date picker to month -1 if same month
  const startDate = Date.parseDate($('#date-start').val(), $('#date-start').data('date-format'));
  const endDate = Date.parseDate($('#date-end').val(), $('#date-end').data('date-format'));

  if (
    startDate.getFullYear() === endDate.getFullYear()
    && startDate.getMonth() === endDate.getMonth()
  ) {
    datepickerStart.setValue(startDate.subMonths(1));
  }

  // Events binding
  $('#date-start').focus(function () {
    datepickerStart.setCompare(false);
    datepickerEnd.setCompare(false);
    $('.date-input').removeClass('input-selected');
    $(this).addClass('input-selected');
  });

  $('#date-end').focus(function () {
    datepickerStart.setCompare(false);
    datepickerEnd.setCompare(false);
    $('.date-input').removeClass('input-selected');
    $(this).addClass('input-selected');
  });

  $('#date-start-compare').focus(function () {
    datepickerStart.setCompare(true);
    datepickerEnd.setCompare(true);
    $('#compare-options').val(3);
    $('.date-input').removeClass('input-selected');
    $(this).addClass('input-selected');
  });

  $('#date-end-compare').focus(function () {
    datepickerStart.setCompare(true);
    datepickerEnd.setCompare(true);
    $('#compare-options').val(3);
    $('.date-input').removeClass('input-selected');
    $(this).addClass('input-selected');
  });

  $('#datepicker-cancel').click(() => {
    $('#datepicker').addClass('hide');
  });

  $('#datepicker').show(() => {
    $('#date-start').focus();
    $('#date-start').trigger('change');
  });

  $('#datepicker-compare').click(function () {
    if ($(this).prop('checked')) {
      $('#compare-options').trigger('change');
      $('#form-date-body-compare').show();
      $('#compare-options').prop('disabled', false);
    } else {
      datepickerStart.setStartCompare(null);
      datepickerStart.setEndCompare(null);
      datepickerEnd.setStartCompare(null);
      datepickerEnd.setEndCompare(null);
      $('#form-date-body-compare').hide();
      $('#compare-options').prop('disabled', true);
      $('#date-start').focus();
    }
  });

  $('#compare-options').change(function () {
    if (this.value === '1') setPreviousPeriod();

    if (this.value === '2') setPreviousYear();

    datepickerStart.setStartCompare($('#date-start-compare').val());
    datepickerStart.setEndCompare($('#date-end-compare').val());
    datepickerEnd.setStartCompare($('#date-start-compare').val());
    datepickerEnd.setEndCompare($('#date-end-compare').val());
    datepickerStart.setCompare(true);
    datepickerEnd.setCompare(true);

    if (this.value === '3') $('#date-start-compare').focus();
  });

  if ($('#datepicker-compare').attr('checked')) {
    if ($('#date-start-compare').val().replace(/^\s+|\s+$/g, '').length === 0) $('#compare-options').trigger('change');

    datepickerStart.setStartCompare($('#date-start-compare').val());
    datepickerStart.setEndCompare($('#date-end-compare').val());
    datepickerEnd.setStartCompare($('#date-start-compare').val());
    datepickerEnd.setEndCompare($('#date-end-compare').val());
    datepickerStart.setCompare(true);
    datepickerEnd.setCompare(true);
  }

  $('#datepickerExpand').on('click', () => {
    if ($('#datepicker').hasClass('hide')) {
      $('#datepicker').removeClass('hide');
      $('#date-start').focus();
    } else $('#datepicker').addClass('hide');
  });

  $('.submitDateDay').on('click', (e) => {
    e.preventDefault();
    setDayPeriod();
  });
  $('.submitDateMonth').on('click', (e) => {
    e.preventDefault();
    setMonthPeriod();
  });
  $('.submitDateYear').on('click', (e) => {
    e.preventDefault();
    setYearPeriod();
  });
  $('.submitDateDayPrev').on('click', (e) => {
    e.preventDefault();
    setPreviousDayPeriod();
  });
  $('.submitDateMonthPrev').on('click', (e) => {
    e.preventDefault();
    setPreviousMonthPeriod();
  });
  $('.submitDateYearPrev').on('click', (e) => {
    e.preventDefault();
    setPreviousYearPeriod();
  });
});
