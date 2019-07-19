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
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAgNWQ5OTkwOTRkMTFhZWZmMGI1ODI/M2YxNioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioiLCJ3ZWJwYWNrOi8vLy4vanMvY29tcG9uZW50cy9mb3JtL2Zvcm0tcG9wb3Zlci1lcnJvci5qcyJdLCJuYW1lcyI6WyIkIiwid2luZG93IiwicG9wb3ZlciIsImh0bWwiLCJjb250ZW50IiwiZ2V0RXJyb3JDb250ZW50IiwicmVwb3NpdGlvblBvcG92ZXIiLCJldmVudCIsIiRlbGVtZW50IiwiY3VycmVudFRhcmdldCIsIiRmb3JtR3JvdXAiLCJjbG9zZXN0IiwiJGludmFsaWRGZWVkYmFja0NvbnRhaW5lciIsImZpbmQiLCIkZXJyb3JQb3BvdmVyIiwibG9jYWxlVmlzaWJsZUVsZW1lbnRXaWR0aCIsIndpZHRoIiwiY3NzIiwiaG9yaXpvbnRhbERpZmZlcmVuY2UiLCJnZXRIb3Jpem9udGFsRGlmZmVyZW5jZSIsImlucHV0SG9yaXpvbnRhbFBvc2l0aW9uIiwib2Zmc2V0IiwibGVmdCIsInBvcG92ZXJIb3Jpem9udGFsUG9zaXRpb24iLCJwb3BvdmVyVHJpZ2dlckVsZW1lbnQiLCJwb3BvdmVyVHJpZ2dlcklkIiwiZGF0YSIsImRvY3VtZW50Iiwib24iXSwibWFwcGluZ3MiOiI7O0FBQUE7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7OztBQUdBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBLG1EQUEyQyxjQUFjOztBQUV6RDtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBLGFBQUs7QUFDTDtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBLG1DQUEyQiwwQkFBMEIsRUFBRTtBQUN2RCx5Q0FBaUMsZUFBZTtBQUNoRDtBQUNBO0FBQ0E7O0FBRUE7QUFDQSw4REFBc0QsK0RBQStEOztBQUVySDtBQUNBOztBQUVBO0FBQ0E7Ozs7Ozs7Ozs7O0FDaEVBOzs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7O0FBeUJBLElBQU1BLElBQUlDLE9BQU9ELENBQWpCOztBQUVBOzs7O0FBSUFBLEVBQUUsWUFBTTtBQUNOO0FBQ0FBLElBQUUsb0NBQUYsRUFBd0NFLE9BQXhDLENBQWdEO0FBQzlDQyxVQUFNLElBRHdDO0FBRTlDQyxhQUFTLG1CQUFZO0FBQ25CLGFBQU9DLGdCQUFnQixJQUFoQixDQUFQO0FBQ0Q7QUFKNkMsR0FBaEQ7O0FBT0E7Ozs7O0FBS0EsTUFBTUMsb0JBQW9CLFNBQXBCQSxpQkFBb0IsQ0FBQ0MsS0FBRCxFQUFXO0FBQ25DLFFBQU1DLFdBQVdSLEVBQUVPLE1BQU1FLGFBQVIsQ0FBakI7QUFDQSxRQUFNQyxhQUFhRixTQUFTRyxPQUFULENBQWlCLGFBQWpCLENBQW5CO0FBQ0EsUUFBTUMsNEJBQTRCRixXQUFXRyxJQUFYLENBQWdCLDZCQUFoQixDQUFsQztBQUNBLFFBQU1DLGdCQUFnQkosV0FBV0csSUFBWCxDQUFnQixxQkFBaEIsQ0FBdEI7O0FBRUEsUUFBTUUsNEJBQTRCSCwwQkFBMEJJLEtBQTFCLEVBQWxDOztBQUVBRixrQkFBY0csR0FBZCxDQUFrQixPQUFsQixFQUEyQkYseUJBQTNCOztBQUVBLFFBQU1HLHVCQUF1QkMsd0JBQXdCUCx5QkFBeEIsRUFBbURFLGFBQW5ELENBQTdCOztBQUVBQSxrQkFBY0csR0FBZCxDQUFrQixNQUFsQixFQUE2QkMsb0JBQTdCO0FBQ0QsR0FiRDs7QUFlQTs7Ozs7O0FBTUEsTUFBTUMsMEJBQTBCLFNBQTFCQSx1QkFBMEIsQ0FBQ1AseUJBQUQsRUFBNEJFLGFBQTVCLEVBQThDO0FBQzVFLFFBQU1NLDBCQUEwQlIsMEJBQTBCUyxNQUExQixHQUFtQ0MsSUFBbkU7QUFDQSxRQUFNQyw0QkFBNEJULGNBQWNPLE1BQWQsR0FBdUJDLElBQXpEOztBQUVBLFdBQU9GLDBCQUEwQkcseUJBQWpDO0FBQ0QsR0FMRDs7QUFPQTs7Ozs7O0FBTUEsTUFBTWxCLGtCQUFrQixTQUFsQkEsZUFBa0IsQ0FBQ21CLHFCQUFELEVBQTJCO0FBQ2pELFFBQU1DLG1CQUFtQnpCLEVBQUV3QixxQkFBRixFQUF5QkUsSUFBekIsQ0FBOEIsSUFBOUIsQ0FBekI7O0FBRUEsV0FBTzFCLDBDQUF3Q3lCLGdCQUF4QyxTQUE4RHRCLElBQTlELEVBQVA7QUFDRCxHQUpEOztBQU1BO0FBQ0FILElBQUUyQixRQUFGLEVBQVlDLEVBQVosQ0FBZSxrQkFBZixFQUFtQyxvQ0FBbkMsRUFBeUUsVUFBQ3JCLEtBQUQ7QUFBQSxXQUFXRCxrQkFBa0JDLEtBQWxCLENBQVg7QUFBQSxHQUF6RTtBQUNELENBeERELEUiLCJmaWxlIjoiZm9ybV9wb3BvdmVyX2Vycm9yLmJ1bmRsZS5qcyIsInNvdXJjZXNDb250ZW50IjpbIiBcdC8vIFRoZSBtb2R1bGUgY2FjaGVcbiBcdHZhciBpbnN0YWxsZWRNb2R1bGVzID0ge307XG5cbiBcdC8vIFRoZSByZXF1aXJlIGZ1bmN0aW9uXG4gXHRmdW5jdGlvbiBfX3dlYnBhY2tfcmVxdWlyZV9fKG1vZHVsZUlkKSB7XG5cbiBcdFx0Ly8gQ2hlY2sgaWYgbW9kdWxlIGlzIGluIGNhY2hlXG4gXHRcdGlmKGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdKSB7XG4gXHRcdFx0cmV0dXJuIGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdLmV4cG9ydHM7XG4gXHRcdH1cbiBcdFx0Ly8gQ3JlYXRlIGEgbmV3IG1vZHVsZSAoYW5kIHB1dCBpdCBpbnRvIHRoZSBjYWNoZSlcbiBcdFx0dmFyIG1vZHVsZSA9IGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdID0ge1xuIFx0XHRcdGk6IG1vZHVsZUlkLFxuIFx0XHRcdGw6IGZhbHNlLFxuIFx0XHRcdGV4cG9ydHM6IHt9XG4gXHRcdH07XG5cbiBcdFx0Ly8gRXhlY3V0ZSB0aGUgbW9kdWxlIGZ1bmN0aW9uXG4gXHRcdG1vZHVsZXNbbW9kdWxlSWRdLmNhbGwobW9kdWxlLmV4cG9ydHMsIG1vZHVsZSwgbW9kdWxlLmV4cG9ydHMsIF9fd2VicGFja19yZXF1aXJlX18pO1xuXG4gXHRcdC8vIEZsYWcgdGhlIG1vZHVsZSBhcyBsb2FkZWRcbiBcdFx0bW9kdWxlLmwgPSB0cnVlO1xuXG4gXHRcdC8vIFJldHVybiB0aGUgZXhwb3J0cyBvZiB0aGUgbW9kdWxlXG4gXHRcdHJldHVybiBtb2R1bGUuZXhwb3J0cztcbiBcdH1cblxuXG4gXHQvLyBleHBvc2UgdGhlIG1vZHVsZXMgb2JqZWN0IChfX3dlYnBhY2tfbW9kdWxlc19fKVxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5tID0gbW9kdWxlcztcblxuIFx0Ly8gZXhwb3NlIHRoZSBtb2R1bGUgY2FjaGVcbiBcdF9fd2VicGFja19yZXF1aXJlX18uYyA9IGluc3RhbGxlZE1vZHVsZXM7XG5cbiBcdC8vIGlkZW50aXR5IGZ1bmN0aW9uIGZvciBjYWxsaW5nIGhhcm1vbnkgaW1wb3J0cyB3aXRoIHRoZSBjb3JyZWN0IGNvbnRleHRcbiBcdF9fd2VicGFja19yZXF1aXJlX18uaSA9IGZ1bmN0aW9uKHZhbHVlKSB7IHJldHVybiB2YWx1ZTsgfTtcblxuIFx0Ly8gZGVmaW5lIGdldHRlciBmdW5jdGlvbiBmb3IgaGFybW9ueSBleHBvcnRzXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmQgPSBmdW5jdGlvbihleHBvcnRzLCBuYW1lLCBnZXR0ZXIpIHtcbiBcdFx0aWYoIV9fd2VicGFja19yZXF1aXJlX18ubyhleHBvcnRzLCBuYW1lKSkge1xuIFx0XHRcdE9iamVjdC5kZWZpbmVQcm9wZXJ0eShleHBvcnRzLCBuYW1lLCB7XG4gXHRcdFx0XHRjb25maWd1cmFibGU6IGZhbHNlLFxuIFx0XHRcdFx0ZW51bWVyYWJsZTogdHJ1ZSxcbiBcdFx0XHRcdGdldDogZ2V0dGVyXG4gXHRcdFx0fSk7XG4gXHRcdH1cbiBcdH07XG5cbiBcdC8vIGdldERlZmF1bHRFeHBvcnQgZnVuY3Rpb24gZm9yIGNvbXBhdGliaWxpdHkgd2l0aCBub24taGFybW9ueSBtb2R1bGVzXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm4gPSBmdW5jdGlvbihtb2R1bGUpIHtcbiBcdFx0dmFyIGdldHRlciA9IG1vZHVsZSAmJiBtb2R1bGUuX19lc01vZHVsZSA/XG4gXHRcdFx0ZnVuY3Rpb24gZ2V0RGVmYXVsdCgpIHsgcmV0dXJuIG1vZHVsZVsnZGVmYXVsdCddOyB9IDpcbiBcdFx0XHRmdW5jdGlvbiBnZXRNb2R1bGVFeHBvcnRzKCkgeyByZXR1cm4gbW9kdWxlOyB9O1xuIFx0XHRfX3dlYnBhY2tfcmVxdWlyZV9fLmQoZ2V0dGVyLCAnYScsIGdldHRlcik7XG4gXHRcdHJldHVybiBnZXR0ZXI7XG4gXHR9O1xuXG4gXHQvLyBPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGxcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubyA9IGZ1bmN0aW9uKG9iamVjdCwgcHJvcGVydHkpIHsgcmV0dXJuIE9iamVjdC5wcm90b3R5cGUuaGFzT3duUHJvcGVydHkuY2FsbChvYmplY3QsIHByb3BlcnR5KTsgfTtcblxuIFx0Ly8gX193ZWJwYWNrX3B1YmxpY19wYXRoX19cbiBcdF9fd2VicGFja19yZXF1aXJlX18ucCA9IFwiXCI7XG5cbiBcdC8vIExvYWQgZW50cnkgbW9kdWxlIGFuZCByZXR1cm4gZXhwb3J0c1xuIFx0cmV0dXJuIF9fd2VicGFja19yZXF1aXJlX18oX193ZWJwYWNrX3JlcXVpcmVfXy5zID0gMzA4KTtcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyB3ZWJwYWNrL2Jvb3RzdHJhcCA1ZDk5OTA5NGQxMWFlZmYwYjU4MiIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuLyoqXG4gKiBDb21wb25lbnQgcmVzcG9uc2libGUgZm9yIGRpc3BsYXlpbmcgZm9ybSBwb3BvdmVyIGVycm9ycyB3aXRoIG1vZGlmaWVkIHdpZHRoIHdoaWNoIGlzIGNhbGN1bGF0ZWQgYmFzZWQgb24gdGhlXG4gKiBmb3JtIGdyb3VwIHdpZHRoLlxuICovXG4kKCgpID0+IHtcbiAgLy8gbG9hZHMgZm9ybSBwb3BvdmVyIGluc3RhbmNlXG4gICQoJ1tkYXRhLXRvZ2dsZT1cImZvcm0tcG9wb3Zlci1lcnJvclwiXScpLnBvcG92ZXIoe1xuICAgIGh0bWw6IHRydWUsXG4gICAgY29udGVudDogZnVuY3Rpb24gKCkge1xuICAgICAgcmV0dXJuIGdldEVycm9yQ29udGVudCh0aGlzKTtcbiAgICB9LFxuICB9KTtcblxuICAvKipcbiAgICogUmVjYWxjdWxhdGVzIHBvcG92ZXIgcG9zaXRpb24gc28gaXQgaXMgYWx3YXlzIGFsaWduZWQgaG9yaXpvbnRhbGx5IGFuZCB3aWR0aCBpcyBpZGVudGljYWxcbiAgICogdG8gdGhlIGNoaWxkIGVsZW1lbnRzIG9mIHRoZSBmb3JtLlxuICAgKiBAcGFyYW0ge09iamVjdH0gZXZlbnRcbiAgICovXG4gIGNvbnN0IHJlcG9zaXRpb25Qb3BvdmVyID0gKGV2ZW50KSA9PiB7XG4gICAgY29uc3QgJGVsZW1lbnQgPSAkKGV2ZW50LmN1cnJlbnRUYXJnZXQpO1xuICAgIGNvbnN0ICRmb3JtR3JvdXAgPSAkZWxlbWVudC5jbG9zZXN0KCcuZm9ybS1ncm91cCcpO1xuICAgIGNvbnN0ICRpbnZhbGlkRmVlZGJhY2tDb250YWluZXIgPSAkZm9ybUdyb3VwLmZpbmQoJy5pbnZhbGlkLWZlZWRiYWNrLWNvbnRhaW5lcicpO1xuICAgIGNvbnN0ICRlcnJvclBvcG92ZXIgPSAkZm9ybUdyb3VwLmZpbmQoJy5mb3JtLXBvcG92ZXItZXJyb3InKTtcblxuICAgIGNvbnN0IGxvY2FsZVZpc2libGVFbGVtZW50V2lkdGggPSAkaW52YWxpZEZlZWRiYWNrQ29udGFpbmVyLndpZHRoKCk7XG5cbiAgICAkZXJyb3JQb3BvdmVyLmNzcygnd2lkdGgnLCBsb2NhbGVWaXNpYmxlRWxlbWVudFdpZHRoKTtcblxuICAgIGNvbnN0IGhvcml6b250YWxEaWZmZXJlbmNlID0gZ2V0SG9yaXpvbnRhbERpZmZlcmVuY2UoJGludmFsaWRGZWVkYmFja0NvbnRhaW5lciwgJGVycm9yUG9wb3Zlcik7XG5cbiAgICAkZXJyb3JQb3BvdmVyLmNzcygnbGVmdCcsIGAke2hvcml6b250YWxEaWZmZXJlbmNlfXB4YCk7XG4gIH07XG5cbiAgLyoqXG4gICAqIGdldHMgaG9yaXpvbnRhbCBkaWZmZXJlbmNlIHdoaWNoIGhlbHBzIHRvIGFsaWduIHBvcG92ZXIgaG9yaXpvbnRhbGx5LlxuICAgKiBAcGFyYW0ge2pRdWVyeX0gJGludmFsaWRGZWVkYmFja0NvbnRhaW5lclxuICAgKiBAcGFyYW0ge2pRdWVyeX0gJGVycm9yUG9wb3ZlclxuICAgKiBAcmV0dXJucyB7bnVtYmVyfVxuICAgKi9cbiAgY29uc3QgZ2V0SG9yaXpvbnRhbERpZmZlcmVuY2UgPSAoJGludmFsaWRGZWVkYmFja0NvbnRhaW5lciwgJGVycm9yUG9wb3ZlcikgPT4ge1xuICAgIGNvbnN0IGlucHV0SG9yaXpvbnRhbFBvc2l0aW9uID0gJGludmFsaWRGZWVkYmFja0NvbnRhaW5lci5vZmZzZXQoKS5sZWZ0O1xuICAgIGNvbnN0IHBvcG92ZXJIb3Jpem9udGFsUG9zaXRpb24gPSAkZXJyb3JQb3BvdmVyLm9mZnNldCgpLmxlZnQ7XG5cbiAgICByZXR1cm4gaW5wdXRIb3Jpem9udGFsUG9zaXRpb24gLSBwb3BvdmVySG9yaXpvbnRhbFBvc2l0aW9uO1xuICB9O1xuXG4gIC8qKlxuICAgKiBHZXRzIHBvcG92ZXIgZXJyb3IgY29udGVudCBwcmUtZmV0Y2hlZCBpbiBodG1sLiBJdCB1c2VkIHVuaXF1ZSBzZWxlY3RvciB0byBpZGVudGlmeSB3aGljaCBvbmUgY29udGVudCB0byByZW5kZXIuXG4gICAqXG4gICAqIEBwYXJhbSBwb3BvdmVyVHJpZ2dlckVsZW1lbnRcbiAgICogQHJldHVybnMge2pRdWVyeX1cbiAgICovXG4gIGNvbnN0IGdldEVycm9yQ29udGVudCA9IChwb3BvdmVyVHJpZ2dlckVsZW1lbnQpID0+IHtcbiAgICBjb25zdCBwb3BvdmVyVHJpZ2dlcklkID0gJChwb3BvdmVyVHJpZ2dlckVsZW1lbnQpLmRhdGEoJ2lkJyk7XG5cbiAgICByZXR1cm4gJChgLmpzLXBvcG92ZXItZXJyb3ItY29udGVudFtkYXRhLWlkPVwiJHtwb3BvdmVyVHJpZ2dlcklkfVwiXWApLmh0bWwoKTtcbiAgfTtcblxuICAvLyByZWdpc3RlcnMgdGhlIGV2ZW50IHdoaWNoIGRpc3BsYXlzIHRoZSBwb3BvdmVyXG4gICQoZG9jdW1lbnQpLm9uKCdzaG93bi5icy5wb3BvdmVyJywgJ1tkYXRhLXRvZ2dsZT1cImZvcm0tcG9wb3Zlci1lcnJvclwiXScsIChldmVudCkgPT4gcmVwb3NpdGlvblBvcG92ZXIoZXZlbnQpKTtcbn0pO1xuXG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9jb21wb25lbnRzL2Zvcm0vZm9ybS1wb3BvdmVyLWVycm9yLmpzIl0sInNvdXJjZVJvb3QiOiIifQ==