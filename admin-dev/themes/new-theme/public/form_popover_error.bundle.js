window["form_popover_error"] =
/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// identity function for calling harmony imports with the correct context
/******/ 	__webpack_require__.i = function(value) { return value; };
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, {
/******/ 				configurable: false,
/******/ 				enumerable: true,
/******/ 				get: getter
/******/ 			});
/******/ 		}
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 308);
/******/ })
/************************************************************************/
/******/ ({

/***/ 308:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

var $ = window.$;

/**
 * Component responsible for displaying form popover errors with modified width which is calculated based on the
 * form group width.
 */
$(function () {
  // loads form popover instance
  $('[data-toggle="form-popover-error"]').popover({
    html: true,
    content: function content() {
      return getErrorContent(this);
    }
  });

  /**
   * Recalculates popover position so it is always aligned horizontally and width is identical
   * to the child elements of the form.
   * @param {Object} event
   */
  var repositionPopover = function repositionPopover(event) {
    var $element = $(event.currentTarget);
    var $formGroup = $element.closest('.form-group');
    var $invalidFeedbackContainer = $formGroup.find('.invalid-feedback-container');
    var $errorPopover = $formGroup.find('.form-popover-error');

    var localeVisibleElementWidth = $invalidFeedbackContainer.width();

    $errorPopover.css('width', localeVisibleElementWidth);

    var horizontalDifference = getHorizontalDifference($invalidFeedbackContainer, $errorPopover);

    $errorPopover.css('left', horizontalDifference + 'px');
  };

  /**
   * gets horizontal difference which helps to align popover horizontally.
   * @param {jQuery} $invalidFeedbackContainer
   * @param {jQuery} $errorPopover
   * @returns {number}
   */
  var getHorizontalDifference = function getHorizontalDifference($invalidFeedbackContainer, $errorPopover) {
    var inputHorizontalPosition = $invalidFeedbackContainer.offset().left;
    var popoverHorizontalPosition = $errorPopover.offset().left;

    return inputHorizontalPosition - popoverHorizontalPosition;
  };

  /**
   * Gets popover error content pre-fetched in html. It used unique selector to identify which one content to render.
   *
   * @param popoverTriggerElement
   * @returns {jQuery}
   */
  var getErrorContent = function getErrorContent(popoverTriggerElement) {
    var popoverTriggerId = $(popoverTriggerElement).data('id');

    return $('.js-popover-error-content[data-id="' + popoverTriggerId + '"]').html();
  };

  // registers the event which displays the popover
  $(document).on('shown.bs.popover', '[data-toggle="form-popover-error"]', function (event) {
    return repositionPopover(event);
  });
});

/***/ })

/******/ });
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAgNjhlODI5MWYxMzYwNzBmMjc2YmQ/MjBkNCoqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqIiwid2VicGFjazovLy8uL2pzL2NvbXBvbmVudHMvZm9ybS9mb3JtLXBvcG92ZXItZXJyb3IuanMiXSwibmFtZXMiOlsiJCIsIndpbmRvdyIsInBvcG92ZXIiLCJodG1sIiwiY29udGVudCIsImdldEVycm9yQ29udGVudCIsInJlcG9zaXRpb25Qb3BvdmVyIiwiZXZlbnQiLCIkZWxlbWVudCIsImN1cnJlbnRUYXJnZXQiLCIkZm9ybUdyb3VwIiwiY2xvc2VzdCIsIiRpbnZhbGlkRmVlZGJhY2tDb250YWluZXIiLCJmaW5kIiwiJGVycm9yUG9wb3ZlciIsImxvY2FsZVZpc2libGVFbGVtZW50V2lkdGgiLCJ3aWR0aCIsImNzcyIsImhvcml6b250YWxEaWZmZXJlbmNlIiwiZ2V0SG9yaXpvbnRhbERpZmZlcmVuY2UiLCJpbnB1dEhvcml6b250YWxQb3NpdGlvbiIsIm9mZnNldCIsImxlZnQiLCJwb3BvdmVySG9yaXpvbnRhbFBvc2l0aW9uIiwicG9wb3ZlclRyaWdnZXJFbGVtZW50IiwicG9wb3ZlclRyaWdnZXJJZCIsImRhdGEiLCJkb2N1bWVudCIsIm9uIl0sIm1hcHBpbmdzIjoiOztBQUFBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBOzs7QUFHQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQSxtREFBMkMsY0FBYzs7QUFFekQ7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQSxhQUFLO0FBQ0w7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQSxtQ0FBMkIsMEJBQTBCLEVBQUU7QUFDdkQseUNBQWlDLGVBQWU7QUFDaEQ7QUFDQTtBQUNBOztBQUVBO0FBQ0EsOERBQXNELCtEQUErRDs7QUFFckg7QUFDQTs7QUFFQTtBQUNBOzs7Ozs7Ozs7OztBQ2hFQTs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7OztBQXlCQSxJQUFNQSxJQUFJQyxPQUFPRCxDQUFqQjs7QUFFQTs7OztBQUlBQSxFQUFFLFlBQU07QUFDTjtBQUNBQSxJQUFFLG9DQUFGLEVBQXdDRSxPQUF4QyxDQUFnRDtBQUM5Q0MsVUFBTSxJQUR3QztBQUU5Q0MsYUFBUyxtQkFBWTtBQUNuQixhQUFPQyxnQkFBZ0IsSUFBaEIsQ0FBUDtBQUNEO0FBSjZDLEdBQWhEOztBQU9BOzs7OztBQUtBLE1BQU1DLG9CQUFvQixTQUFwQkEsaUJBQW9CLENBQUNDLEtBQUQsRUFBVztBQUNuQyxRQUFNQyxXQUFXUixFQUFFTyxNQUFNRSxhQUFSLENBQWpCO0FBQ0EsUUFBTUMsYUFBYUYsU0FBU0csT0FBVCxDQUFpQixhQUFqQixDQUFuQjtBQUNBLFFBQU1DLDRCQUE0QkYsV0FBV0csSUFBWCxDQUFnQiw2QkFBaEIsQ0FBbEM7QUFDQSxRQUFNQyxnQkFBZ0JKLFdBQVdHLElBQVgsQ0FBZ0IscUJBQWhCLENBQXRCOztBQUVBLFFBQU1FLDRCQUE0QkgsMEJBQTBCSSxLQUExQixFQUFsQzs7QUFFQUYsa0JBQWNHLEdBQWQsQ0FBa0IsT0FBbEIsRUFBMkJGLHlCQUEzQjs7QUFFQSxRQUFNRyx1QkFBdUJDLHdCQUF3QlAseUJBQXhCLEVBQW1ERSxhQUFuRCxDQUE3Qjs7QUFFQUEsa0JBQWNHLEdBQWQsQ0FBa0IsTUFBbEIsRUFBNkJDLG9CQUE3QjtBQUNELEdBYkQ7O0FBZUE7Ozs7OztBQU1BLE1BQU1DLDBCQUEwQixTQUExQkEsdUJBQTBCLENBQUNQLHlCQUFELEVBQTRCRSxhQUE1QixFQUE4QztBQUM1RSxRQUFNTSwwQkFBMEJSLDBCQUEwQlMsTUFBMUIsR0FBbUNDLElBQW5FO0FBQ0EsUUFBTUMsNEJBQTRCVCxjQUFjTyxNQUFkLEdBQXVCQyxJQUF6RDs7QUFFQSxXQUFPRiwwQkFBMEJHLHlCQUFqQztBQUNELEdBTEQ7O0FBT0E7Ozs7OztBQU1BLE1BQU1sQixrQkFBa0IsU0FBbEJBLGVBQWtCLENBQUNtQixxQkFBRCxFQUEyQjtBQUNqRCxRQUFNQyxtQkFBbUJ6QixFQUFFd0IscUJBQUYsRUFBeUJFLElBQXpCLENBQThCLElBQTlCLENBQXpCOztBQUVBLFdBQU8xQiwwQ0FBd0N5QixnQkFBeEMsU0FBOER0QixJQUE5RCxFQUFQO0FBQ0QsR0FKRDs7QUFNQTtBQUNBSCxJQUFFMkIsUUFBRixFQUFZQyxFQUFaLENBQWUsa0JBQWYsRUFBbUMsb0NBQW5DLEVBQXlFLFVBQUNyQixLQUFEO0FBQUEsV0FBV0Qsa0JBQWtCQyxLQUFsQixDQUFYO0FBQUEsR0FBekU7QUFDRCxDQXhERCxFIiwiZmlsZSI6ImZvcm1fcG9wb3Zlcl9lcnJvci5idW5kbGUuanMiLCJzb3VyY2VzQ29udGVudCI6WyIgXHQvLyBUaGUgbW9kdWxlIGNhY2hlXG4gXHR2YXIgaW5zdGFsbGVkTW9kdWxlcyA9IHt9O1xuXG4gXHQvLyBUaGUgcmVxdWlyZSBmdW5jdGlvblxuIFx0ZnVuY3Rpb24gX193ZWJwYWNrX3JlcXVpcmVfXyhtb2R1bGVJZCkge1xuXG4gXHRcdC8vIENoZWNrIGlmIG1vZHVsZSBpcyBpbiBjYWNoZVxuIFx0XHRpZihpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXSkge1xuIFx0XHRcdHJldHVybiBpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXS5leHBvcnRzO1xuIFx0XHR9XG4gXHRcdC8vIENyZWF0ZSBhIG5ldyBtb2R1bGUgKGFuZCBwdXQgaXQgaW50byB0aGUgY2FjaGUpXG4gXHRcdHZhciBtb2R1bGUgPSBpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXSA9IHtcbiBcdFx0XHRpOiBtb2R1bGVJZCxcbiBcdFx0XHRsOiBmYWxzZSxcbiBcdFx0XHRleHBvcnRzOiB7fVxuIFx0XHR9O1xuXG4gXHRcdC8vIEV4ZWN1dGUgdGhlIG1vZHVsZSBmdW5jdGlvblxuIFx0XHRtb2R1bGVzW21vZHVsZUlkXS5jYWxsKG1vZHVsZS5leHBvcnRzLCBtb2R1bGUsIG1vZHVsZS5leHBvcnRzLCBfX3dlYnBhY2tfcmVxdWlyZV9fKTtcblxuIFx0XHQvLyBGbGFnIHRoZSBtb2R1bGUgYXMgbG9hZGVkXG4gXHRcdG1vZHVsZS5sID0gdHJ1ZTtcblxuIFx0XHQvLyBSZXR1cm4gdGhlIGV4cG9ydHMgb2YgdGhlIG1vZHVsZVxuIFx0XHRyZXR1cm4gbW9kdWxlLmV4cG9ydHM7XG4gXHR9XG5cblxuIFx0Ly8gZXhwb3NlIHRoZSBtb2R1bGVzIG9iamVjdCAoX193ZWJwYWNrX21vZHVsZXNfXylcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubSA9IG1vZHVsZXM7XG5cbiBcdC8vIGV4cG9zZSB0aGUgbW9kdWxlIGNhY2hlXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmMgPSBpbnN0YWxsZWRNb2R1bGVzO1xuXG4gXHQvLyBpZGVudGl0eSBmdW5jdGlvbiBmb3IgY2FsbGluZyBoYXJtb255IGltcG9ydHMgd2l0aCB0aGUgY29ycmVjdCBjb250ZXh0XG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmkgPSBmdW5jdGlvbih2YWx1ZSkgeyByZXR1cm4gdmFsdWU7IH07XG5cbiBcdC8vIGRlZmluZSBnZXR0ZXIgZnVuY3Rpb24gZm9yIGhhcm1vbnkgZXhwb3J0c1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5kID0gZnVuY3Rpb24oZXhwb3J0cywgbmFtZSwgZ2V0dGVyKSB7XG4gXHRcdGlmKCFfX3dlYnBhY2tfcmVxdWlyZV9fLm8oZXhwb3J0cywgbmFtZSkpIHtcbiBcdFx0XHRPYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgbmFtZSwge1xuIFx0XHRcdFx0Y29uZmlndXJhYmxlOiBmYWxzZSxcbiBcdFx0XHRcdGVudW1lcmFibGU6IHRydWUsXG4gXHRcdFx0XHRnZXQ6IGdldHRlclxuIFx0XHRcdH0pO1xuIFx0XHR9XG4gXHR9O1xuXG4gXHQvLyBnZXREZWZhdWx0RXhwb3J0IGZ1bmN0aW9uIGZvciBjb21wYXRpYmlsaXR5IHdpdGggbm9uLWhhcm1vbnkgbW9kdWxlc1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5uID0gZnVuY3Rpb24obW9kdWxlKSB7XG4gXHRcdHZhciBnZXR0ZXIgPSBtb2R1bGUgJiYgbW9kdWxlLl9fZXNNb2R1bGUgP1xuIFx0XHRcdGZ1bmN0aW9uIGdldERlZmF1bHQoKSB7IHJldHVybiBtb2R1bGVbJ2RlZmF1bHQnXTsgfSA6XG4gXHRcdFx0ZnVuY3Rpb24gZ2V0TW9kdWxlRXhwb3J0cygpIHsgcmV0dXJuIG1vZHVsZTsgfTtcbiBcdFx0X193ZWJwYWNrX3JlcXVpcmVfXy5kKGdldHRlciwgJ2EnLCBnZXR0ZXIpO1xuIFx0XHRyZXR1cm4gZ2V0dGVyO1xuIFx0fTtcblxuIFx0Ly8gT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eS5jYWxsXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm8gPSBmdW5jdGlvbihvYmplY3QsIHByb3BlcnR5KSB7IHJldHVybiBPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGwob2JqZWN0LCBwcm9wZXJ0eSk7IH07XG5cbiBcdC8vIF9fd2VicGFja19wdWJsaWNfcGF0aF9fXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLnAgPSBcIlwiO1xuXG4gXHQvLyBMb2FkIGVudHJ5IG1vZHVsZSBhbmQgcmV0dXJuIGV4cG9ydHNcbiBcdHJldHVybiBfX3dlYnBhY2tfcmVxdWlyZV9fKF9fd2VicGFja19yZXF1aXJlX18ucyA9IDMwOCk7XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gd2VicGFjay9ib290c3RyYXAgNjhlODI5MWYxMzYwNzBmMjc2YmQiLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbi8qKlxuICogQ29tcG9uZW50IHJlc3BvbnNpYmxlIGZvciBkaXNwbGF5aW5nIGZvcm0gcG9wb3ZlciBlcnJvcnMgd2l0aCBtb2RpZmllZCB3aWR0aCB3aGljaCBpcyBjYWxjdWxhdGVkIGJhc2VkIG9uIHRoZVxuICogZm9ybSBncm91cCB3aWR0aC5cbiAqL1xuJCgoKSA9PiB7XG4gIC8vIGxvYWRzIGZvcm0gcG9wb3ZlciBpbnN0YW5jZVxuICAkKCdbZGF0YS10b2dnbGU9XCJmb3JtLXBvcG92ZXItZXJyb3JcIl0nKS5wb3BvdmVyKHtcbiAgICBodG1sOiB0cnVlLFxuICAgIGNvbnRlbnQ6IGZ1bmN0aW9uICgpIHtcbiAgICAgIHJldHVybiBnZXRFcnJvckNvbnRlbnQodGhpcyk7XG4gICAgfSxcbiAgfSk7XG5cbiAgLyoqXG4gICAqIFJlY2FsY3VsYXRlcyBwb3BvdmVyIHBvc2l0aW9uIHNvIGl0IGlzIGFsd2F5cyBhbGlnbmVkIGhvcml6b250YWxseSBhbmQgd2lkdGggaXMgaWRlbnRpY2FsXG4gICAqIHRvIHRoZSBjaGlsZCBlbGVtZW50cyBvZiB0aGUgZm9ybS5cbiAgICogQHBhcmFtIHtPYmplY3R9IGV2ZW50XG4gICAqL1xuICBjb25zdCByZXBvc2l0aW9uUG9wb3ZlciA9IChldmVudCkgPT4ge1xuICAgIGNvbnN0ICRlbGVtZW50ID0gJChldmVudC5jdXJyZW50VGFyZ2V0KTtcbiAgICBjb25zdCAkZm9ybUdyb3VwID0gJGVsZW1lbnQuY2xvc2VzdCgnLmZvcm0tZ3JvdXAnKTtcbiAgICBjb25zdCAkaW52YWxpZEZlZWRiYWNrQ29udGFpbmVyID0gJGZvcm1Hcm91cC5maW5kKCcuaW52YWxpZC1mZWVkYmFjay1jb250YWluZXInKTtcbiAgICBjb25zdCAkZXJyb3JQb3BvdmVyID0gJGZvcm1Hcm91cC5maW5kKCcuZm9ybS1wb3BvdmVyLWVycm9yJyk7XG5cbiAgICBjb25zdCBsb2NhbGVWaXNpYmxlRWxlbWVudFdpZHRoID0gJGludmFsaWRGZWVkYmFja0NvbnRhaW5lci53aWR0aCgpO1xuXG4gICAgJGVycm9yUG9wb3Zlci5jc3MoJ3dpZHRoJywgbG9jYWxlVmlzaWJsZUVsZW1lbnRXaWR0aCk7XG5cbiAgICBjb25zdCBob3Jpem9udGFsRGlmZmVyZW5jZSA9IGdldEhvcml6b250YWxEaWZmZXJlbmNlKCRpbnZhbGlkRmVlZGJhY2tDb250YWluZXIsICRlcnJvclBvcG92ZXIpO1xuXG4gICAgJGVycm9yUG9wb3Zlci5jc3MoJ2xlZnQnLCBgJHtob3Jpem9udGFsRGlmZmVyZW5jZX1weGApO1xuICB9O1xuXG4gIC8qKlxuICAgKiBnZXRzIGhvcml6b250YWwgZGlmZmVyZW5jZSB3aGljaCBoZWxwcyB0byBhbGlnbiBwb3BvdmVyIGhvcml6b250YWxseS5cbiAgICogQHBhcmFtIHtqUXVlcnl9ICRpbnZhbGlkRmVlZGJhY2tDb250YWluZXJcbiAgICogQHBhcmFtIHtqUXVlcnl9ICRlcnJvclBvcG92ZXJcbiAgICogQHJldHVybnMge251bWJlcn1cbiAgICovXG4gIGNvbnN0IGdldEhvcml6b250YWxEaWZmZXJlbmNlID0gKCRpbnZhbGlkRmVlZGJhY2tDb250YWluZXIsICRlcnJvclBvcG92ZXIpID0+IHtcbiAgICBjb25zdCBpbnB1dEhvcml6b250YWxQb3NpdGlvbiA9ICRpbnZhbGlkRmVlZGJhY2tDb250YWluZXIub2Zmc2V0KCkubGVmdDtcbiAgICBjb25zdCBwb3BvdmVySG9yaXpvbnRhbFBvc2l0aW9uID0gJGVycm9yUG9wb3Zlci5vZmZzZXQoKS5sZWZ0O1xuXG4gICAgcmV0dXJuIGlucHV0SG9yaXpvbnRhbFBvc2l0aW9uIC0gcG9wb3Zlckhvcml6b250YWxQb3NpdGlvbjtcbiAgfTtcblxuICAvKipcbiAgICogR2V0cyBwb3BvdmVyIGVycm9yIGNvbnRlbnQgcHJlLWZldGNoZWQgaW4gaHRtbC4gSXQgdXNlZCB1bmlxdWUgc2VsZWN0b3IgdG8gaWRlbnRpZnkgd2hpY2ggb25lIGNvbnRlbnQgdG8gcmVuZGVyLlxuICAgKlxuICAgKiBAcGFyYW0gcG9wb3ZlclRyaWdnZXJFbGVtZW50XG4gICAqIEByZXR1cm5zIHtqUXVlcnl9XG4gICAqL1xuICBjb25zdCBnZXRFcnJvckNvbnRlbnQgPSAocG9wb3ZlclRyaWdnZXJFbGVtZW50KSA9PiB7XG4gICAgY29uc3QgcG9wb3ZlclRyaWdnZXJJZCA9ICQocG9wb3ZlclRyaWdnZXJFbGVtZW50KS5kYXRhKCdpZCcpO1xuXG4gICAgcmV0dXJuICQoYC5qcy1wb3BvdmVyLWVycm9yLWNvbnRlbnRbZGF0YS1pZD1cIiR7cG9wb3ZlclRyaWdnZXJJZH1cIl1gKS5odG1sKCk7XG4gIH07XG5cbiAgLy8gcmVnaXN0ZXJzIHRoZSBldmVudCB3aGljaCBkaXNwbGF5cyB0aGUgcG9wb3ZlclxuICAkKGRvY3VtZW50KS5vbignc2hvd24uYnMucG9wb3ZlcicsICdbZGF0YS10b2dnbGU9XCJmb3JtLXBvcG92ZXItZXJyb3JcIl0nLCAoZXZlbnQpID0+IHJlcG9zaXRpb25Qb3BvdmVyKGV2ZW50KSk7XG59KTtcblxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvY29tcG9uZW50cy9mb3JtL2Zvcm0tcG9wb3Zlci1lcnJvci5qcyJdLCJzb3VyY2VSb290IjoiIn0=