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
/******/ 	return __webpack_require__(__webpack_require__.s = 327);
/******/ })
/************************************************************************/
/******/ ({

/***/ 327:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


/**
 * 2007-2019 PrestaShop SA and Contributors
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
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAgMWU2NjI2MzkwMGU5NjZkZmJiZjA/ODU5MCoqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKiIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL2Zvcm0vZm9ybS1wb3BvdmVyLWVycm9yLmpzIl0sIm5hbWVzIjpbIiQiLCJ3aW5kb3ciLCJwb3BvdmVyIiwiaHRtbCIsImNvbnRlbnQiLCJnZXRFcnJvckNvbnRlbnQiLCJyZXBvc2l0aW9uUG9wb3ZlciIsImV2ZW50IiwiJGVsZW1lbnQiLCJjdXJyZW50VGFyZ2V0IiwiJGZvcm1Hcm91cCIsImNsb3Nlc3QiLCIkaW52YWxpZEZlZWRiYWNrQ29udGFpbmVyIiwiZmluZCIsIiRlcnJvclBvcG92ZXIiLCJsb2NhbGVWaXNpYmxlRWxlbWVudFdpZHRoIiwid2lkdGgiLCJjc3MiLCJob3Jpem9udGFsRGlmZmVyZW5jZSIsImdldEhvcml6b250YWxEaWZmZXJlbmNlIiwiaW5wdXRIb3Jpem9udGFsUG9zaXRpb24iLCJvZmZzZXQiLCJsZWZ0IiwicG9wb3Zlckhvcml6b250YWxQb3NpdGlvbiIsInBvcG92ZXJUcmlnZ2VyRWxlbWVudCIsInBvcG92ZXJUcmlnZ2VySWQiLCJkYXRhIiwiZG9jdW1lbnQiLCJvbiJdLCJtYXBwaW5ncyI6Ijs7QUFBQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTs7O0FBR0E7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0EsbURBQTJDLGNBQWM7O0FBRXpEO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsYUFBSztBQUNMO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0EsbUNBQTJCLDBCQUEwQixFQUFFO0FBQ3ZELHlDQUFpQyxlQUFlO0FBQ2hEO0FBQ0E7QUFDQTs7QUFFQTtBQUNBLDhEQUFzRCwrREFBK0Q7O0FBRXJIO0FBQ0E7O0FBRUE7QUFDQTs7Ozs7Ozs7Ozs7QUNoRUE7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7QUF5QkEsSUFBTUEsSUFBSUMsT0FBT0QsQ0FBakI7O0FBRUE7Ozs7QUFJQUEsRUFBRSxZQUFNO0FBQ047QUFDQUEsSUFBRSxvQ0FBRixFQUF3Q0UsT0FBeEMsQ0FBZ0Q7QUFDOUNDLFVBQU0sSUFEd0M7QUFFOUNDLGFBQVMsbUJBQVk7QUFDbkIsYUFBT0MsZ0JBQWdCLElBQWhCLENBQVA7QUFDRDtBQUo2QyxHQUFoRDs7QUFPQTs7Ozs7QUFLQSxNQUFNQyxvQkFBb0IsU0FBcEJBLGlCQUFvQixDQUFDQyxLQUFELEVBQVc7QUFDbkMsUUFBTUMsV0FBV1IsRUFBRU8sTUFBTUUsYUFBUixDQUFqQjtBQUNBLFFBQU1DLGFBQWFGLFNBQVNHLE9BQVQsQ0FBaUIsYUFBakIsQ0FBbkI7QUFDQSxRQUFNQyw0QkFBNEJGLFdBQVdHLElBQVgsQ0FBZ0IsNkJBQWhCLENBQWxDO0FBQ0EsUUFBTUMsZ0JBQWdCSixXQUFXRyxJQUFYLENBQWdCLHFCQUFoQixDQUF0Qjs7QUFFQSxRQUFNRSw0QkFBNEJILDBCQUEwQkksS0FBMUIsRUFBbEM7O0FBRUFGLGtCQUFjRyxHQUFkLENBQWtCLE9BQWxCLEVBQTJCRix5QkFBM0I7O0FBRUEsUUFBTUcsdUJBQXVCQyx3QkFBd0JQLHlCQUF4QixFQUFtREUsYUFBbkQsQ0FBN0I7O0FBRUFBLGtCQUFjRyxHQUFkLENBQWtCLE1BQWxCLEVBQTZCQyxvQkFBN0I7QUFDRCxHQWJEOztBQWVBOzs7Ozs7QUFNQSxNQUFNQywwQkFBMEIsU0FBMUJBLHVCQUEwQixDQUFDUCx5QkFBRCxFQUE0QkUsYUFBNUIsRUFBOEM7QUFDNUUsUUFBTU0sMEJBQTBCUiwwQkFBMEJTLE1BQTFCLEdBQW1DQyxJQUFuRTtBQUNBLFFBQU1DLDRCQUE0QlQsY0FBY08sTUFBZCxHQUF1QkMsSUFBekQ7O0FBRUEsV0FBT0YsMEJBQTBCRyx5QkFBakM7QUFDRCxHQUxEOztBQU9BOzs7Ozs7QUFNQSxNQUFNbEIsa0JBQWtCLFNBQWxCQSxlQUFrQixDQUFDbUIscUJBQUQsRUFBMkI7QUFDakQsUUFBTUMsbUJBQW1CekIsRUFBRXdCLHFCQUFGLEVBQXlCRSxJQUF6QixDQUE4QixJQUE5QixDQUF6Qjs7QUFFQSxXQUFPMUIsMENBQXdDeUIsZ0JBQXhDLFNBQThEdEIsSUFBOUQsRUFBUDtBQUNELEdBSkQ7O0FBTUE7QUFDQUgsSUFBRTJCLFFBQUYsRUFBWUMsRUFBWixDQUFlLGtCQUFmLEVBQW1DLG9DQUFuQyxFQUF5RSxVQUFDckIsS0FBRDtBQUFBLFdBQVdELGtCQUFrQkMsS0FBbEIsQ0FBWDtBQUFBLEdBQXpFO0FBQ0QsQ0F4REQsRSIsImZpbGUiOiJmb3JtX3BvcG92ZXJfZXJyb3IuYnVuZGxlLmpzIiwic291cmNlc0NvbnRlbnQiOlsiIFx0Ly8gVGhlIG1vZHVsZSBjYWNoZVxuIFx0dmFyIGluc3RhbGxlZE1vZHVsZXMgPSB7fTtcblxuIFx0Ly8gVGhlIHJlcXVpcmUgZnVuY3Rpb25cbiBcdGZ1bmN0aW9uIF9fd2VicGFja19yZXF1aXJlX18obW9kdWxlSWQpIHtcblxuIFx0XHQvLyBDaGVjayBpZiBtb2R1bGUgaXMgaW4gY2FjaGVcbiBcdFx0aWYoaW5zdGFsbGVkTW9kdWxlc1ttb2R1bGVJZF0pIHtcbiBcdFx0XHRyZXR1cm4gaW5zdGFsbGVkTW9kdWxlc1ttb2R1bGVJZF0uZXhwb3J0cztcbiBcdFx0fVxuIFx0XHQvLyBDcmVhdGUgYSBuZXcgbW9kdWxlIChhbmQgcHV0IGl0IGludG8gdGhlIGNhY2hlKVxuIFx0XHR2YXIgbW9kdWxlID0gaW5zdGFsbGVkTW9kdWxlc1ttb2R1bGVJZF0gPSB7XG4gXHRcdFx0aTogbW9kdWxlSWQsXG4gXHRcdFx0bDogZmFsc2UsXG4gXHRcdFx0ZXhwb3J0czoge31cbiBcdFx0fTtcblxuIFx0XHQvLyBFeGVjdXRlIHRoZSBtb2R1bGUgZnVuY3Rpb25cbiBcdFx0bW9kdWxlc1ttb2R1bGVJZF0uY2FsbChtb2R1bGUuZXhwb3J0cywgbW9kdWxlLCBtb2R1bGUuZXhwb3J0cywgX193ZWJwYWNrX3JlcXVpcmVfXyk7XG5cbiBcdFx0Ly8gRmxhZyB0aGUgbW9kdWxlIGFzIGxvYWRlZFxuIFx0XHRtb2R1bGUubCA9IHRydWU7XG5cbiBcdFx0Ly8gUmV0dXJuIHRoZSBleHBvcnRzIG9mIHRoZSBtb2R1bGVcbiBcdFx0cmV0dXJuIG1vZHVsZS5leHBvcnRzO1xuIFx0fVxuXG5cbiBcdC8vIGV4cG9zZSB0aGUgbW9kdWxlcyBvYmplY3QgKF9fd2VicGFja19tb2R1bGVzX18pXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm0gPSBtb2R1bGVzO1xuXG4gXHQvLyBleHBvc2UgdGhlIG1vZHVsZSBjYWNoZVxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5jID0gaW5zdGFsbGVkTW9kdWxlcztcblxuIFx0Ly8gaWRlbnRpdHkgZnVuY3Rpb24gZm9yIGNhbGxpbmcgaGFybW9ueSBpbXBvcnRzIHdpdGggdGhlIGNvcnJlY3QgY29udGV4dFxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5pID0gZnVuY3Rpb24odmFsdWUpIHsgcmV0dXJuIHZhbHVlOyB9O1xuXG4gXHQvLyBkZWZpbmUgZ2V0dGVyIGZ1bmN0aW9uIGZvciBoYXJtb255IGV4cG9ydHNcbiBcdF9fd2VicGFja19yZXF1aXJlX18uZCA9IGZ1bmN0aW9uKGV4cG9ydHMsIG5hbWUsIGdldHRlcikge1xuIFx0XHRpZighX193ZWJwYWNrX3JlcXVpcmVfXy5vKGV4cG9ydHMsIG5hbWUpKSB7XG4gXHRcdFx0T2JqZWN0LmRlZmluZVByb3BlcnR5KGV4cG9ydHMsIG5hbWUsIHtcbiBcdFx0XHRcdGNvbmZpZ3VyYWJsZTogZmFsc2UsXG4gXHRcdFx0XHRlbnVtZXJhYmxlOiB0cnVlLFxuIFx0XHRcdFx0Z2V0OiBnZXR0ZXJcbiBcdFx0XHR9KTtcbiBcdFx0fVxuIFx0fTtcblxuIFx0Ly8gZ2V0RGVmYXVsdEV4cG9ydCBmdW5jdGlvbiBmb3IgY29tcGF0aWJpbGl0eSB3aXRoIG5vbi1oYXJtb255IG1vZHVsZXNcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubiA9IGZ1bmN0aW9uKG1vZHVsZSkge1xuIFx0XHR2YXIgZ2V0dGVyID0gbW9kdWxlICYmIG1vZHVsZS5fX2VzTW9kdWxlID9cbiBcdFx0XHRmdW5jdGlvbiBnZXREZWZhdWx0KCkgeyByZXR1cm4gbW9kdWxlWydkZWZhdWx0J107IH0gOlxuIFx0XHRcdGZ1bmN0aW9uIGdldE1vZHVsZUV4cG9ydHMoKSB7IHJldHVybiBtb2R1bGU7IH07XG4gXHRcdF9fd2VicGFja19yZXF1aXJlX18uZChnZXR0ZXIsICdhJywgZ2V0dGVyKTtcbiBcdFx0cmV0dXJuIGdldHRlcjtcbiBcdH07XG5cbiBcdC8vIE9iamVjdC5wcm90b3R5cGUuaGFzT3duUHJvcGVydHkuY2FsbFxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5vID0gZnVuY3Rpb24ob2JqZWN0LCBwcm9wZXJ0eSkgeyByZXR1cm4gT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eS5jYWxsKG9iamVjdCwgcHJvcGVydHkpOyB9O1xuXG4gXHQvLyBfX3dlYnBhY2tfcHVibGljX3BhdGhfX1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5wID0gXCJcIjtcblxuIFx0Ly8gTG9hZCBlbnRyeSBtb2R1bGUgYW5kIHJldHVybiBleHBvcnRzXG4gXHRyZXR1cm4gX193ZWJwYWNrX3JlcXVpcmVfXyhfX3dlYnBhY2tfcmVxdWlyZV9fLnMgPSAzMjcpO1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIHdlYnBhY2svYm9vdHN0cmFwIDFlNjYyNjM5MDBlOTY2ZGZiYmYwIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4vKipcbiAqIENvbXBvbmVudCByZXNwb25zaWJsZSBmb3IgZGlzcGxheWluZyBmb3JtIHBvcG92ZXIgZXJyb3JzIHdpdGggbW9kaWZpZWQgd2lkdGggd2hpY2ggaXMgY2FsY3VsYXRlZCBiYXNlZCBvbiB0aGVcbiAqIGZvcm0gZ3JvdXAgd2lkdGguXG4gKi9cbiQoKCkgPT4ge1xuICAvLyBsb2FkcyBmb3JtIHBvcG92ZXIgaW5zdGFuY2VcbiAgJCgnW2RhdGEtdG9nZ2xlPVwiZm9ybS1wb3BvdmVyLWVycm9yXCJdJykucG9wb3Zlcih7XG4gICAgaHRtbDogdHJ1ZSxcbiAgICBjb250ZW50OiBmdW5jdGlvbiAoKSB7XG4gICAgICByZXR1cm4gZ2V0RXJyb3JDb250ZW50KHRoaXMpO1xuICAgIH0sXG4gIH0pO1xuXG4gIC8qKlxuICAgKiBSZWNhbGN1bGF0ZXMgcG9wb3ZlciBwb3NpdGlvbiBzbyBpdCBpcyBhbHdheXMgYWxpZ25lZCBob3Jpem9udGFsbHkgYW5kIHdpZHRoIGlzIGlkZW50aWNhbFxuICAgKiB0byB0aGUgY2hpbGQgZWxlbWVudHMgb2YgdGhlIGZvcm0uXG4gICAqIEBwYXJhbSB7T2JqZWN0fSBldmVudFxuICAgKi9cbiAgY29uc3QgcmVwb3NpdGlvblBvcG92ZXIgPSAoZXZlbnQpID0+IHtcbiAgICBjb25zdCAkZWxlbWVudCA9ICQoZXZlbnQuY3VycmVudFRhcmdldCk7XG4gICAgY29uc3QgJGZvcm1Hcm91cCA9ICRlbGVtZW50LmNsb3Nlc3QoJy5mb3JtLWdyb3VwJyk7XG4gICAgY29uc3QgJGludmFsaWRGZWVkYmFja0NvbnRhaW5lciA9ICRmb3JtR3JvdXAuZmluZCgnLmludmFsaWQtZmVlZGJhY2stY29udGFpbmVyJyk7XG4gICAgY29uc3QgJGVycm9yUG9wb3ZlciA9ICRmb3JtR3JvdXAuZmluZCgnLmZvcm0tcG9wb3Zlci1lcnJvcicpO1xuXG4gICAgY29uc3QgbG9jYWxlVmlzaWJsZUVsZW1lbnRXaWR0aCA9ICRpbnZhbGlkRmVlZGJhY2tDb250YWluZXIud2lkdGgoKTtcblxuICAgICRlcnJvclBvcG92ZXIuY3NzKCd3aWR0aCcsIGxvY2FsZVZpc2libGVFbGVtZW50V2lkdGgpO1xuXG4gICAgY29uc3QgaG9yaXpvbnRhbERpZmZlcmVuY2UgPSBnZXRIb3Jpem9udGFsRGlmZmVyZW5jZSgkaW52YWxpZEZlZWRiYWNrQ29udGFpbmVyLCAkZXJyb3JQb3BvdmVyKTtcblxuICAgICRlcnJvclBvcG92ZXIuY3NzKCdsZWZ0JywgYCR7aG9yaXpvbnRhbERpZmZlcmVuY2V9cHhgKTtcbiAgfTtcblxuICAvKipcbiAgICogZ2V0cyBob3Jpem9udGFsIGRpZmZlcmVuY2Ugd2hpY2ggaGVscHMgdG8gYWxpZ24gcG9wb3ZlciBob3Jpem9udGFsbHkuXG4gICAqIEBwYXJhbSB7alF1ZXJ5fSAkaW52YWxpZEZlZWRiYWNrQ29udGFpbmVyXG4gICAqIEBwYXJhbSB7alF1ZXJ5fSAkZXJyb3JQb3BvdmVyXG4gICAqIEByZXR1cm5zIHtudW1iZXJ9XG4gICAqL1xuICBjb25zdCBnZXRIb3Jpem9udGFsRGlmZmVyZW5jZSA9ICgkaW52YWxpZEZlZWRiYWNrQ29udGFpbmVyLCAkZXJyb3JQb3BvdmVyKSA9PiB7XG4gICAgY29uc3QgaW5wdXRIb3Jpem9udGFsUG9zaXRpb24gPSAkaW52YWxpZEZlZWRiYWNrQ29udGFpbmVyLm9mZnNldCgpLmxlZnQ7XG4gICAgY29uc3QgcG9wb3Zlckhvcml6b250YWxQb3NpdGlvbiA9ICRlcnJvclBvcG92ZXIub2Zmc2V0KCkubGVmdDtcblxuICAgIHJldHVybiBpbnB1dEhvcml6b250YWxQb3NpdGlvbiAtIHBvcG92ZXJIb3Jpem9udGFsUG9zaXRpb247XG4gIH07XG5cbiAgLyoqXG4gICAqIEdldHMgcG9wb3ZlciBlcnJvciBjb250ZW50IHByZS1mZXRjaGVkIGluIGh0bWwuIEl0IHVzZWQgdW5pcXVlIHNlbGVjdG9yIHRvIGlkZW50aWZ5IHdoaWNoIG9uZSBjb250ZW50IHRvIHJlbmRlci5cbiAgICpcbiAgICogQHBhcmFtIHBvcG92ZXJUcmlnZ2VyRWxlbWVudFxuICAgKiBAcmV0dXJucyB7alF1ZXJ5fVxuICAgKi9cbiAgY29uc3QgZ2V0RXJyb3JDb250ZW50ID0gKHBvcG92ZXJUcmlnZ2VyRWxlbWVudCkgPT4ge1xuICAgIGNvbnN0IHBvcG92ZXJUcmlnZ2VySWQgPSAkKHBvcG92ZXJUcmlnZ2VyRWxlbWVudCkuZGF0YSgnaWQnKTtcblxuICAgIHJldHVybiAkKGAuanMtcG9wb3Zlci1lcnJvci1jb250ZW50W2RhdGEtaWQ9XCIke3BvcG92ZXJUcmlnZ2VySWR9XCJdYCkuaHRtbCgpO1xuICB9O1xuXG4gIC8vIHJlZ2lzdGVycyB0aGUgZXZlbnQgd2hpY2ggZGlzcGxheXMgdGhlIHBvcG92ZXJcbiAgJChkb2N1bWVudCkub24oJ3Nob3duLmJzLnBvcG92ZXInLCAnW2RhdGEtdG9nZ2xlPVwiZm9ybS1wb3BvdmVyLWVycm9yXCJdJywgKGV2ZW50KSA9PiByZXBvc2l0aW9uUG9wb3ZlcihldmVudCkpO1xufSk7XG5cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL2NvbXBvbmVudHMvZm9ybS9mb3JtLXBvcG92ZXItZXJyb3IuanMiXSwic291cmNlUm9vdCI6IiJ9