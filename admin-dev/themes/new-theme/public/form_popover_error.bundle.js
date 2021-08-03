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
/******/ 	return __webpack_require__(__webpack_require__.s = 469);
/******/ })
/************************************************************************/
/******/ ({

/***/ 469:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


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
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAgM2E2MTdjZWQyOWViY2NiNmExZDA/MTI1MCoqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioiLCJ3ZWJwYWNrOi8vLy4vanMvY29tcG9uZW50cy9mb3JtL2Zvcm0tcG9wb3Zlci1lcnJvci5qcyJdLCJuYW1lcyI6WyIkIiwid2luZG93IiwicG9wb3ZlciIsImh0bWwiLCJjb250ZW50IiwiZ2V0RXJyb3JDb250ZW50IiwicmVwb3NpdGlvblBvcG92ZXIiLCJldmVudCIsIiRlbGVtZW50IiwiY3VycmVudFRhcmdldCIsIiRmb3JtR3JvdXAiLCJjbG9zZXN0IiwiJGludmFsaWRGZWVkYmFja0NvbnRhaW5lciIsImZpbmQiLCIkZXJyb3JQb3BvdmVyIiwibG9jYWxlVmlzaWJsZUVsZW1lbnRXaWR0aCIsIndpZHRoIiwiY3NzIiwiaG9yaXpvbnRhbERpZmZlcmVuY2UiLCJnZXRIb3Jpem9udGFsRGlmZmVyZW5jZSIsImlucHV0SG9yaXpvbnRhbFBvc2l0aW9uIiwib2Zmc2V0IiwibGVmdCIsInBvcG92ZXJIb3Jpem9udGFsUG9zaXRpb24iLCJwb3BvdmVyVHJpZ2dlckVsZW1lbnQiLCJwb3BvdmVyVHJpZ2dlcklkIiwiZGF0YSIsImRvY3VtZW50Iiwib24iXSwibWFwcGluZ3MiOiI7O0FBQUE7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7OztBQUdBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBLG1EQUEyQyxjQUFjOztBQUV6RDtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBLGFBQUs7QUFDTDtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBLG1DQUEyQiwwQkFBMEIsRUFBRTtBQUN2RCx5Q0FBaUMsZUFBZTtBQUNoRDtBQUNBO0FBQ0E7O0FBRUE7QUFDQSw4REFBc0QsK0RBQStEOztBQUVySDtBQUNBOztBQUVBO0FBQ0E7Ozs7Ozs7Ozs7O0FDaEVBOzs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7O0FBeUJBLElBQU1BLElBQUlDLE9BQU9ELENBQWpCOztBQUVBOzs7O0FBSUFBLEVBQUUsWUFBTTtBQUNOO0FBQ0FBLElBQUUsb0NBQUYsRUFBd0NFLE9BQXhDLENBQWdEO0FBQzlDQyxVQUFNLElBRHdDO0FBRTlDQyxhQUFTLG1CQUFZO0FBQ25CLGFBQU9DLGdCQUFnQixJQUFoQixDQUFQO0FBQ0Q7QUFKNkMsR0FBaEQ7O0FBT0E7Ozs7O0FBS0EsTUFBTUMsb0JBQW9CLFNBQXBCQSxpQkFBb0IsQ0FBQ0MsS0FBRCxFQUFXO0FBQ25DLFFBQU1DLFdBQVdSLEVBQUVPLE1BQU1FLGFBQVIsQ0FBakI7QUFDQSxRQUFNQyxhQUFhRixTQUFTRyxPQUFULENBQWlCLGFBQWpCLENBQW5CO0FBQ0EsUUFBTUMsNEJBQTRCRixXQUFXRyxJQUFYLENBQWdCLDZCQUFoQixDQUFsQztBQUNBLFFBQU1DLGdCQUFnQkosV0FBV0csSUFBWCxDQUFnQixxQkFBaEIsQ0FBdEI7O0FBRUEsUUFBTUUsNEJBQTRCSCwwQkFBMEJJLEtBQTFCLEVBQWxDOztBQUVBRixrQkFBY0csR0FBZCxDQUFrQixPQUFsQixFQUEyQkYseUJBQTNCOztBQUVBLFFBQU1HLHVCQUF1QkMsd0JBQXdCUCx5QkFBeEIsRUFBbURFLGFBQW5ELENBQTdCOztBQUVBQSxrQkFBY0csR0FBZCxDQUFrQixNQUFsQixFQUE2QkMsb0JBQTdCO0FBQ0QsR0FiRDs7QUFlQTs7Ozs7O0FBTUEsTUFBTUMsMEJBQTBCLFNBQTFCQSx1QkFBMEIsQ0FBQ1AseUJBQUQsRUFBNEJFLGFBQTVCLEVBQThDO0FBQzVFLFFBQU1NLDBCQUEwQlIsMEJBQTBCUyxNQUExQixHQUFtQ0MsSUFBbkU7QUFDQSxRQUFNQyw0QkFBNEJULGNBQWNPLE1BQWQsR0FBdUJDLElBQXpEOztBQUVBLFdBQU9GLDBCQUEwQkcseUJBQWpDO0FBQ0QsR0FMRDs7QUFPQTs7Ozs7O0FBTUEsTUFBTWxCLGtCQUFrQixTQUFsQkEsZUFBa0IsQ0FBQ21CLHFCQUFELEVBQTJCO0FBQ2pELFFBQU1DLG1CQUFtQnpCLEVBQUV3QixxQkFBRixFQUF5QkUsSUFBekIsQ0FBOEIsSUFBOUIsQ0FBekI7O0FBRUEsV0FBTzFCLDBDQUF3Q3lCLGdCQUF4QyxTQUE4RHRCLElBQTlELEVBQVA7QUFDRCxHQUpEOztBQU1BO0FBQ0FILElBQUUyQixRQUFGLEVBQVlDLEVBQVosQ0FBZSxrQkFBZixFQUFtQyxvQ0FBbkMsRUFBeUUsVUFBQ3JCLEtBQUQ7QUFBQSxXQUFXRCxrQkFBa0JDLEtBQWxCLENBQVg7QUFBQSxHQUF6RTtBQUNELENBeERELEUiLCJmaWxlIjoiZm9ybV9wb3BvdmVyX2Vycm9yLmJ1bmRsZS5qcyIsInNvdXJjZXNDb250ZW50IjpbIiBcdC8vIFRoZSBtb2R1bGUgY2FjaGVcbiBcdHZhciBpbnN0YWxsZWRNb2R1bGVzID0ge307XG5cbiBcdC8vIFRoZSByZXF1aXJlIGZ1bmN0aW9uXG4gXHRmdW5jdGlvbiBfX3dlYnBhY2tfcmVxdWlyZV9fKG1vZHVsZUlkKSB7XG5cbiBcdFx0Ly8gQ2hlY2sgaWYgbW9kdWxlIGlzIGluIGNhY2hlXG4gXHRcdGlmKGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdKSB7XG4gXHRcdFx0cmV0dXJuIGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdLmV4cG9ydHM7XG4gXHRcdH1cbiBcdFx0Ly8gQ3JlYXRlIGEgbmV3IG1vZHVsZSAoYW5kIHB1dCBpdCBpbnRvIHRoZSBjYWNoZSlcbiBcdFx0dmFyIG1vZHVsZSA9IGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdID0ge1xuIFx0XHRcdGk6IG1vZHVsZUlkLFxuIFx0XHRcdGw6IGZhbHNlLFxuIFx0XHRcdGV4cG9ydHM6IHt9XG4gXHRcdH07XG5cbiBcdFx0Ly8gRXhlY3V0ZSB0aGUgbW9kdWxlIGZ1bmN0aW9uXG4gXHRcdG1vZHVsZXNbbW9kdWxlSWRdLmNhbGwobW9kdWxlLmV4cG9ydHMsIG1vZHVsZSwgbW9kdWxlLmV4cG9ydHMsIF9fd2VicGFja19yZXF1aXJlX18pO1xuXG4gXHRcdC8vIEZsYWcgdGhlIG1vZHVsZSBhcyBsb2FkZWRcbiBcdFx0bW9kdWxlLmwgPSB0cnVlO1xuXG4gXHRcdC8vIFJldHVybiB0aGUgZXhwb3J0cyBvZiB0aGUgbW9kdWxlXG4gXHRcdHJldHVybiBtb2R1bGUuZXhwb3J0cztcbiBcdH1cblxuXG4gXHQvLyBleHBvc2UgdGhlIG1vZHVsZXMgb2JqZWN0IChfX3dlYnBhY2tfbW9kdWxlc19fKVxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5tID0gbW9kdWxlcztcblxuIFx0Ly8gZXhwb3NlIHRoZSBtb2R1bGUgY2FjaGVcbiBcdF9fd2VicGFja19yZXF1aXJlX18uYyA9IGluc3RhbGxlZE1vZHVsZXM7XG5cbiBcdC8vIGlkZW50aXR5IGZ1bmN0aW9uIGZvciBjYWxsaW5nIGhhcm1vbnkgaW1wb3J0cyB3aXRoIHRoZSBjb3JyZWN0IGNvbnRleHRcbiBcdF9fd2VicGFja19yZXF1aXJlX18uaSA9IGZ1bmN0aW9uKHZhbHVlKSB7IHJldHVybiB2YWx1ZTsgfTtcblxuIFx0Ly8gZGVmaW5lIGdldHRlciBmdW5jdGlvbiBmb3IgaGFybW9ueSBleHBvcnRzXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmQgPSBmdW5jdGlvbihleHBvcnRzLCBuYW1lLCBnZXR0ZXIpIHtcbiBcdFx0aWYoIV9fd2VicGFja19yZXF1aXJlX18ubyhleHBvcnRzLCBuYW1lKSkge1xuIFx0XHRcdE9iamVjdC5kZWZpbmVQcm9wZXJ0eShleHBvcnRzLCBuYW1lLCB7XG4gXHRcdFx0XHRjb25maWd1cmFibGU6IGZhbHNlLFxuIFx0XHRcdFx0ZW51bWVyYWJsZTogdHJ1ZSxcbiBcdFx0XHRcdGdldDogZ2V0dGVyXG4gXHRcdFx0fSk7XG4gXHRcdH1cbiBcdH07XG5cbiBcdC8vIGdldERlZmF1bHRFeHBvcnQgZnVuY3Rpb24gZm9yIGNvbXBhdGliaWxpdHkgd2l0aCBub24taGFybW9ueSBtb2R1bGVzXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm4gPSBmdW5jdGlvbihtb2R1bGUpIHtcbiBcdFx0dmFyIGdldHRlciA9IG1vZHVsZSAmJiBtb2R1bGUuX19lc01vZHVsZSA/XG4gXHRcdFx0ZnVuY3Rpb24gZ2V0RGVmYXVsdCgpIHsgcmV0dXJuIG1vZHVsZVsnZGVmYXVsdCddOyB9IDpcbiBcdFx0XHRmdW5jdGlvbiBnZXRNb2R1bGVFeHBvcnRzKCkgeyByZXR1cm4gbW9kdWxlOyB9O1xuIFx0XHRfX3dlYnBhY2tfcmVxdWlyZV9fLmQoZ2V0dGVyLCAnYScsIGdldHRlcik7XG4gXHRcdHJldHVybiBnZXR0ZXI7XG4gXHR9O1xuXG4gXHQvLyBPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGxcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubyA9IGZ1bmN0aW9uKG9iamVjdCwgcHJvcGVydHkpIHsgcmV0dXJuIE9iamVjdC5wcm90b3R5cGUuaGFzT3duUHJvcGVydHkuY2FsbChvYmplY3QsIHByb3BlcnR5KTsgfTtcblxuIFx0Ly8gX193ZWJwYWNrX3B1YmxpY19wYXRoX19cbiBcdF9fd2VicGFja19yZXF1aXJlX18ucCA9IFwiXCI7XG5cbiBcdC8vIExvYWQgZW50cnkgbW9kdWxlIGFuZCByZXR1cm4gZXhwb3J0c1xuIFx0cmV0dXJuIF9fd2VicGFja19yZXF1aXJlX18oX193ZWJwYWNrX3JlcXVpcmVfXy5zID0gNDY5KTtcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyB3ZWJwYWNrL2Jvb3RzdHJhcCAzYTYxN2NlZDI5ZWJjY2I2YTFkMCIsIi8qKlxuICogQ29weXJpZ2h0IHNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBQcmVzdGFTaG9wIGlzIGFuIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLm1kLlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vZGV2ZG9jcy5wcmVzdGFzaG9wLmNvbS8gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnMgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IFNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKi9cblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4vKipcbiAqIENvbXBvbmVudCByZXNwb25zaWJsZSBmb3IgZGlzcGxheWluZyBmb3JtIHBvcG92ZXIgZXJyb3JzIHdpdGggbW9kaWZpZWQgd2lkdGggd2hpY2ggaXMgY2FsY3VsYXRlZCBiYXNlZCBvbiB0aGVcbiAqIGZvcm0gZ3JvdXAgd2lkdGguXG4gKi9cbiQoKCkgPT4ge1xuICAvLyBsb2FkcyBmb3JtIHBvcG92ZXIgaW5zdGFuY2VcbiAgJCgnW2RhdGEtdG9nZ2xlPVwiZm9ybS1wb3BvdmVyLWVycm9yXCJdJykucG9wb3Zlcih7XG4gICAgaHRtbDogdHJ1ZSxcbiAgICBjb250ZW50OiBmdW5jdGlvbiAoKSB7XG4gICAgICByZXR1cm4gZ2V0RXJyb3JDb250ZW50KHRoaXMpO1xuICAgIH0sXG4gIH0pO1xuXG4gIC8qKlxuICAgKiBSZWNhbGN1bGF0ZXMgcG9wb3ZlciBwb3NpdGlvbiBzbyBpdCBpcyBhbHdheXMgYWxpZ25lZCBob3Jpem9udGFsbHkgYW5kIHdpZHRoIGlzIGlkZW50aWNhbFxuICAgKiB0byB0aGUgY2hpbGQgZWxlbWVudHMgb2YgdGhlIGZvcm0uXG4gICAqIEBwYXJhbSB7T2JqZWN0fSBldmVudFxuICAgKi9cbiAgY29uc3QgcmVwb3NpdGlvblBvcG92ZXIgPSAoZXZlbnQpID0+IHtcbiAgICBjb25zdCAkZWxlbWVudCA9ICQoZXZlbnQuY3VycmVudFRhcmdldCk7XG4gICAgY29uc3QgJGZvcm1Hcm91cCA9ICRlbGVtZW50LmNsb3Nlc3QoJy5mb3JtLWdyb3VwJyk7XG4gICAgY29uc3QgJGludmFsaWRGZWVkYmFja0NvbnRhaW5lciA9ICRmb3JtR3JvdXAuZmluZCgnLmludmFsaWQtZmVlZGJhY2stY29udGFpbmVyJyk7XG4gICAgY29uc3QgJGVycm9yUG9wb3ZlciA9ICRmb3JtR3JvdXAuZmluZCgnLmZvcm0tcG9wb3Zlci1lcnJvcicpO1xuXG4gICAgY29uc3QgbG9jYWxlVmlzaWJsZUVsZW1lbnRXaWR0aCA9ICRpbnZhbGlkRmVlZGJhY2tDb250YWluZXIud2lkdGgoKTtcblxuICAgICRlcnJvclBvcG92ZXIuY3NzKCd3aWR0aCcsIGxvY2FsZVZpc2libGVFbGVtZW50V2lkdGgpO1xuXG4gICAgY29uc3QgaG9yaXpvbnRhbERpZmZlcmVuY2UgPSBnZXRIb3Jpem9udGFsRGlmZmVyZW5jZSgkaW52YWxpZEZlZWRiYWNrQ29udGFpbmVyLCAkZXJyb3JQb3BvdmVyKTtcblxuICAgICRlcnJvclBvcG92ZXIuY3NzKCdsZWZ0JywgYCR7aG9yaXpvbnRhbERpZmZlcmVuY2V9cHhgKTtcbiAgfTtcblxuICAvKipcbiAgICogZ2V0cyBob3Jpem9udGFsIGRpZmZlcmVuY2Ugd2hpY2ggaGVscHMgdG8gYWxpZ24gcG9wb3ZlciBob3Jpem9udGFsbHkuXG4gICAqIEBwYXJhbSB7alF1ZXJ5fSAkaW52YWxpZEZlZWRiYWNrQ29udGFpbmVyXG4gICAqIEBwYXJhbSB7alF1ZXJ5fSAkZXJyb3JQb3BvdmVyXG4gICAqIEByZXR1cm5zIHtudW1iZXJ9XG4gICAqL1xuICBjb25zdCBnZXRIb3Jpem9udGFsRGlmZmVyZW5jZSA9ICgkaW52YWxpZEZlZWRiYWNrQ29udGFpbmVyLCAkZXJyb3JQb3BvdmVyKSA9PiB7XG4gICAgY29uc3QgaW5wdXRIb3Jpem9udGFsUG9zaXRpb24gPSAkaW52YWxpZEZlZWRiYWNrQ29udGFpbmVyLm9mZnNldCgpLmxlZnQ7XG4gICAgY29uc3QgcG9wb3Zlckhvcml6b250YWxQb3NpdGlvbiA9ICRlcnJvclBvcG92ZXIub2Zmc2V0KCkubGVmdDtcblxuICAgIHJldHVybiBpbnB1dEhvcml6b250YWxQb3NpdGlvbiAtIHBvcG92ZXJIb3Jpem9udGFsUG9zaXRpb247XG4gIH07XG5cbiAgLyoqXG4gICAqIEdldHMgcG9wb3ZlciBlcnJvciBjb250ZW50IHByZS1mZXRjaGVkIGluIGh0bWwuIEl0IHVzZWQgdW5pcXVlIHNlbGVjdG9yIHRvIGlkZW50aWZ5IHdoaWNoIG9uZSBjb250ZW50IHRvIHJlbmRlci5cbiAgICpcbiAgICogQHBhcmFtIHBvcG92ZXJUcmlnZ2VyRWxlbWVudFxuICAgKiBAcmV0dXJucyB7alF1ZXJ5fVxuICAgKi9cbiAgY29uc3QgZ2V0RXJyb3JDb250ZW50ID0gKHBvcG92ZXJUcmlnZ2VyRWxlbWVudCkgPT4ge1xuICAgIGNvbnN0IHBvcG92ZXJUcmlnZ2VySWQgPSAkKHBvcG92ZXJUcmlnZ2VyRWxlbWVudCkuZGF0YSgnaWQnKTtcblxuICAgIHJldHVybiAkKGAuanMtcG9wb3Zlci1lcnJvci1jb250ZW50W2RhdGEtaWQ9XCIke3BvcG92ZXJUcmlnZ2VySWR9XCJdYCkuaHRtbCgpO1xuICB9O1xuXG4gIC8vIHJlZ2lzdGVycyB0aGUgZXZlbnQgd2hpY2ggZGlzcGxheXMgdGhlIHBvcG92ZXJcbiAgJChkb2N1bWVudCkub24oJ3Nob3duLmJzLnBvcG92ZXInLCAnW2RhdGEtdG9nZ2xlPVwiZm9ybS1wb3BvdmVyLWVycm9yXCJdJywgKGV2ZW50KSA9PiByZXBvc2l0aW9uUG9wb3ZlcihldmVudCkpO1xufSk7XG5cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL2NvbXBvbmVudHMvZm9ybS9mb3JtLXBvcG92ZXItZXJyb3IuanMiXSwic291cmNlUm9vdCI6IiJ9