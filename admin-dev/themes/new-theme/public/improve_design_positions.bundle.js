window["improve_design_positions"] =
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
/******/ 	return __webpack_require__(__webpack_require__.s = 329);
/******/ })
/************************************************************************/
/******/ ({

/***/ 257:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _slicedToArray = function () { function sliceIterator(arr, i) { var _arr = []; var _n = true; var _d = false; var _e = undefined; try { for (var _i = arr[Symbol.iterator](), _s; !(_n = (_s = _i.next()).done); _n = true) { _arr.push(_s.value); if (i && _arr.length === i) break; } } catch (err) { _d = true; _e = err; } finally { try { if (!_n && _i["return"]) _i["return"](); } finally { if (_d) throw _e; } } return _arr; } return function (arr, i) { if (Array.isArray(arr)) { return arr; } else if (Symbol.iterator in Object(arr)) { return sliceIterator(arr, i); } else { throw new TypeError("Invalid attempt to destructure non-iterable instance"); } }; }();

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

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

var PositionsListHandler = function () {
  function PositionsListHandler() {
    _classCallCheck(this, PositionsListHandler);

    if ($("#position-filters").length === 0) {
      return;
    }

    var self = this;
    self.$panelSelection = $("#modules-position-selection-panel");
    self.$panelSelectionSingleSelection = $("#modules-position-single-selection");
    self.$panelSelectionMultipleSelection = $("#modules-position-multiple-selection");

    self.$panelSelectionOriginalY = self.$panelSelection.offset().top;
    self.$showModules = $("#show-modules");
    self.$modulesList = $('.modules-position-checkbox');
    self.$hookPosition = $("#hook-position");
    self.$hookSearch = $("#hook-search");
    self.$modulePositionsForm = $('#module-positions-form');
    self.$moduleUnhookButton = $('#unhook-button-position-bottom');
    self.$moduleButtonsUpdate = $('.module-buttons-update .btn');

    self.handleList();
    self.handleSortable();

    $('input[name="form[general][enable_tos]"]').on('change', function () {
      return self.handle();
    });
  }

  /**
   * Handle all events for Design -> Positions List
   */


  _createClass(PositionsListHandler, [{
    key: "handleList",
    value: function handleList() {
      var self = this;

      $(window).on('scroll', function () {
        var $scrollTop = $(window).scrollTop();
        self.$panelSelection.css('top', $scrollTop < 20 ? 0 : $scrollTop - self.$panelSelectionOriginalY);
      });

      self.$modulesList.on('change', function () {
        var $checkedCount = self.$modulesList.filter(':checked').length;

        if ($checkedCount === 0) {
          self.$moduleUnhookButton.hide();
          self.$panelSelection.hide();
          self.$panelSelectionSingleSelection.hide();
          self.$panelSelectionMultipleSelection.hide();
        } else if ($checkedCount === 1) {
          self.$moduleUnhookButton.show();
          self.$panelSelection.show();
          self.$panelSelectionSingleSelection.show();
          self.$panelSelectionMultipleSelection.hide();
        } else {
          self.$moduleUnhookButton.show();
          self.$panelSelection.show();
          self.$panelSelectionSingleSelection.hide();
          self.$panelSelectionMultipleSelection.show();
          $('#modules-position-selection-count').html($checkedCount);
        }
      });

      self.$panelSelection.find('button').click(function () {
        $('button[name="unhookform"]').trigger('click');
      });

      self.$hooksList = [];
      $('section.hook-panel .hook-name').each(function () {
        var $this = $(this);
        self.$hooksList.push({
          'title': $this.html(),
          'element': $this,
          'container': $this.parents('.hook-panel')
        });
      });

      self.$showModules.select2();
      self.$showModules.on('change', function () {
        self.modulesPositionFilterHooks();
      });

      self.$hookPosition.on('change', function () {
        self.modulesPositionFilterHooks();
      });

      self.$hookSearch.on('input', function () {
        self.modulesPositionFilterHooks();
      });

      self.$hookSearch.on('keypress', function (e) {
        var keyCode = e.keyCode || e.which;
        return keyCode !== 13;
      });

      $('.hook-checker').on('click', function () {
        $(".hook" + $(this).data('hook-id')).prop('checked', $(this).prop('checked'));
      });

      self.$modulesList.on('click', function () {
        $("#Ghook" + $(this).data('hook-id')).prop('checked', $(".hook" + $(this).data('hook-id') + ":not(:checked)").length === 0);
      });

      self.$moduleButtonsUpdate.on('click', function () {
        var $btn = $(this);
        var $current = $btn.closest('.module-item');
        var $destination = void 0;

        if ($btn.data('way')) {
          $destination = $current.next('.module-item');
        } else {
          $destination = $current.prev('.module-item');
        }

        if ($destination.length === 0) {
          return false;
        }

        if ($btn.data('way')) {
          $current.insertAfter($destination);
        } else {
          $current.insertBefore($destination);
        }

        self.updatePositions({
          hookId: $btn.data('hook-id'),
          moduleId: $btn.data('module-id'),
          way: $btn.data('way'),
          positions: []
        }, $btn.closest('ul'));

        return false;
      });
    }

    /**
     * Handle sortable events
     */

  }, {
    key: "handleSortable",
    value: function handleSortable() {
      var self = this;

      $('.sortable').sortable({
        forcePlaceholderSize: true,
        start: function start(e, ui) {
          $(this).data('previous-index', ui.item.index());
        },
        update: function update($event, ui) {
          var _ui$item$attr$split = ui.item.attr('id').split('_'),
              _ui$item$attr$split2 = _slicedToArray(_ui$item$attr$split, 2),
              hookId = _ui$item$attr$split2[0],
              moduleId = _ui$item$attr$split2[1];

          var $data = {
            hookId: hookId,
            moduleId: moduleId,
            way: $(this).data('previous-index') < ui.item.index() ? 1 : 0,
            positions: []
          };

          self.updatePositions($data, $($event.target));
        }
      });
    }
  }, {
    key: "updatePositions",
    value: function updatePositions($data, $list) {
      var self = this;
      $.each($list.children(), function (index, element) {
        $data.positions.push($(element).attr('id'));
      });

      $.ajax({
        type: 'POST',
        headers: { 'cache-control': 'no-cache' },
        url: self.$modulePositionsForm.data('update-url'),
        data: $data,
        success: function success() {
          var start = 0;
          $.each($list.children(), function (index, element) {
            console.log($(element).find('.index-position'));
            $(element).find('.index-position').html(++start);
          });

          window.showSuccessMessage(window.update_success_msg);
        }
      });
    }

    /**
     * Filter hooks / modules search and everything
     * about hooks positions.
     */

  }, {
    key: "modulesPositionFilterHooks",
    value: function modulesPositionFilterHooks() {
      var self = this;
      var $hookName = self.$hookSearch.val();
      var $moduleId = self.$showModules.val();
      var $regex = new RegExp("(" + $hookName + ")", 'gi');

      for (var $id = 0; $id < self.$hooksList.length; $id++) {
        self.$hooksList[$id].container.toggle($hookName === '' && $moduleId === 'all');
        self.$hooksList[$id].element.html(self.$hooksList[$id].title);
        self.$hooksList[$id].container.find('.module-item').removeClass('highlight');
      }

      // Have select a hook name or a module id
      if ($hookName !== '' || $moduleId !== 'all') {
        // Prepare set of matched elements
        var $hooksToShowFromModule = $();
        var $hooksToShowFromHookName = $();
        var $currentHooks = void 0;
        var $start = void 0;

        for (var _$id = 0; _$id < self.$hooksList.length; _$id++) {
          // Prepare highlight when one module is selected
          if ($moduleId !== 'all') {
            $currentHooks = self.$hooksList[_$id].container.find(".module-position-" + $moduleId);
            if ($currentHooks.length > 0) {
              $hooksToShowFromModule = $hooksToShowFromModule.add(self.$hooksList[_$id].container);
              $currentHooks.addClass('highlight');
            }
          }

          // Prepare highlight when there is a hook name
          if ($hookName !== '') {
            $start = self.$hooksList[_$id].title.toLowerCase().search($hookName.toLowerCase());
            if ($start !== -1) {
              $hooksToShowFromHookName = $hooksToShowFromHookName.add(self.$hooksList[_$id].container);
              self.$hooksList[_$id].element.html(self.$hooksList[_$id].title.replace($regex, '<span class="highlight">$1</span>'));
            }
          }
        }

        // Nothing selected
        if ($moduleId === 'all' && $hookName !== '') {
          $hooksToShowFromHookName.show();
        } else if ($hookName === '' && $moduleId !== 'all') {
          // Have no hook bug have a module
          $hooksToShowFromModule.show();
        } else {
          // Both selected
          $hooksToShowFromHookName.filter($hooksToShowFromModule).show();
        }
      }

      if (!self.$hookPosition.prop('checked')) {
        for (var _$id2 = 0; _$id2 < self.$hooksList.length; _$id2++) {
          if (self.$hooksList[_$id2].container.is('.hook-position')) {
            self.$hooksList[_$id2].container.hide();
          }
        }
      }
    }
  }]);

  return PositionsListHandler;
}();

exports.default = PositionsListHandler;

/***/ }),

/***/ 329:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _positionsListHandler = __webpack_require__(257);

var _positionsListHandler2 = _interopRequireDefault(_positionsListHandler);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

var $ = window.$; /**
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

$(function () {
  new _positionsListHandler2.default();
});

/***/ })

/******/ });
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAgNjhlODI5MWYxMzYwNzBmMjc2YmQ/MjBkNCoqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioiLCJ3ZWJwYWNrOi8vLy4vanMvcGFnZXMvaW1wcm92ZS9kZXNpZ25fcG9zaXRpb25zL3Bvc2l0aW9ucy1saXN0LWhhbmRsZXIuanMiLCJ3ZWJwYWNrOi8vLy4vanMvcGFnZXMvaW1wcm92ZS9kZXNpZ25fcG9zaXRpb25zL2luZGV4LmpzIl0sIm5hbWVzIjpbIiQiLCJ3aW5kb3ciLCJQb3NpdGlvbnNMaXN0SGFuZGxlciIsImxlbmd0aCIsInNlbGYiLCIkcGFuZWxTZWxlY3Rpb24iLCIkcGFuZWxTZWxlY3Rpb25TaW5nbGVTZWxlY3Rpb24iLCIkcGFuZWxTZWxlY3Rpb25NdWx0aXBsZVNlbGVjdGlvbiIsIiRwYW5lbFNlbGVjdGlvbk9yaWdpbmFsWSIsIm9mZnNldCIsInRvcCIsIiRzaG93TW9kdWxlcyIsIiRtb2R1bGVzTGlzdCIsIiRob29rUG9zaXRpb24iLCIkaG9va1NlYXJjaCIsIiRtb2R1bGVQb3NpdGlvbnNGb3JtIiwiJG1vZHVsZVVuaG9va0J1dHRvbiIsIiRtb2R1bGVCdXR0b25zVXBkYXRlIiwiaGFuZGxlTGlzdCIsImhhbmRsZVNvcnRhYmxlIiwib24iLCJoYW5kbGUiLCIkc2Nyb2xsVG9wIiwic2Nyb2xsVG9wIiwiY3NzIiwiJGNoZWNrZWRDb3VudCIsImZpbHRlciIsImhpZGUiLCJzaG93IiwiaHRtbCIsImZpbmQiLCJjbGljayIsInRyaWdnZXIiLCIkaG9va3NMaXN0IiwiZWFjaCIsIiR0aGlzIiwicHVzaCIsInBhcmVudHMiLCJzZWxlY3QyIiwibW9kdWxlc1Bvc2l0aW9uRmlsdGVySG9va3MiLCJlIiwia2V5Q29kZSIsIndoaWNoIiwiZGF0YSIsInByb3AiLCIkYnRuIiwiJGN1cnJlbnQiLCJjbG9zZXN0IiwiJGRlc3RpbmF0aW9uIiwibmV4dCIsInByZXYiLCJpbnNlcnRBZnRlciIsImluc2VydEJlZm9yZSIsInVwZGF0ZVBvc2l0aW9ucyIsImhvb2tJZCIsIm1vZHVsZUlkIiwid2F5IiwicG9zaXRpb25zIiwic29ydGFibGUiLCJmb3JjZVBsYWNlaG9sZGVyU2l6ZSIsInN0YXJ0IiwidWkiLCJpdGVtIiwiaW5kZXgiLCJ1cGRhdGUiLCIkZXZlbnQiLCJhdHRyIiwic3BsaXQiLCIkZGF0YSIsInRhcmdldCIsIiRsaXN0IiwiY2hpbGRyZW4iLCJlbGVtZW50IiwiYWpheCIsInR5cGUiLCJoZWFkZXJzIiwidXJsIiwic3VjY2VzcyIsImNvbnNvbGUiLCJsb2ciLCJzaG93U3VjY2Vzc01lc3NhZ2UiLCJ1cGRhdGVfc3VjY2Vzc19tc2ciLCIkaG9va05hbWUiLCJ2YWwiLCIkbW9kdWxlSWQiLCIkcmVnZXgiLCJSZWdFeHAiLCIkaWQiLCJjb250YWluZXIiLCJ0b2dnbGUiLCJ0aXRsZSIsInJlbW92ZUNsYXNzIiwiJGhvb2tzVG9TaG93RnJvbU1vZHVsZSIsIiRob29rc1RvU2hvd0Zyb21Ib29rTmFtZSIsIiRjdXJyZW50SG9va3MiLCIkc3RhcnQiLCJhZGQiLCJhZGRDbGFzcyIsInRvTG93ZXJDYXNlIiwic2VhcmNoIiwicmVwbGFjZSIsImlzIl0sIm1hcHBpbmdzIjoiOztBQUFBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBOzs7QUFHQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQSxtREFBMkMsY0FBYzs7QUFFekQ7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQSxhQUFLO0FBQ0w7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQSxtQ0FBMkIsMEJBQTBCLEVBQUU7QUFDdkQseUNBQWlDLGVBQWU7QUFDaEQ7QUFDQTtBQUNBOztBQUVBO0FBQ0EsOERBQXNELCtEQUErRDs7QUFFckg7QUFDQTs7QUFFQTtBQUNBOzs7Ozs7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QUNELENBRkQsRSIsImZpbGUiOiJpbXByb3ZlX2Rlc2lnbl9wb3NpdGlvbnMuYnVuZGxlLmpzIiwic291cmNlc0NvbnRlbnQiOlsiIFx0Ly8gVGhlIG1vZHVsZSBjYWNoZVxuIFx0dmFyIGluc3RhbGxlZE1vZHVsZXMgPSB7fTtcblxuIFx0Ly8gVGhlIHJlcXVpcmUgZnVuY3Rpb25cbiBcdGZ1bmN0aW9uIF9fd2VicGFja19yZXF1aXJlX18obW9kdWxlSWQpIHtcblxuIFx0XHQvLyBDaGVjayBpZiBtb2R1bGUgaXMgaW4gY2FjaGVcbiBcdFx0aWYoaW5zdGFsbGVkTW9kdWxlc1ttb2R1bGVJZF0pIHtcbiBcdFx0XHRyZXR1cm4gaW5zdGFsbGVkTW9kdWxlc1ttb2R1bGVJZF0uZXhwb3J0cztcbiBcdFx0fVxuIFx0XHQvLyBDcmVhdGUgYSBuZXcgbW9kdWxlIChhbmQgcHV0IGl0IGludG8gdGhlIGNhY2hlKVxuIFx0XHR2YXIgbW9kdWxlID0gaW5zdGFsbGVkTW9kdWxlc1ttb2R1bGVJZF0gPSB7XG4gXHRcdFx0aTogbW9kdWxlSWQsXG4gXHRcdFx0bDogZmFsc2UsXG4gXHRcdFx0ZXhwb3J0czoge31cbiBcdFx0fTtcblxuIFx0XHQvLyBFeGVjdXRlIHRoZSBtb2R1bGUgZnVuY3Rpb25cbiBcdFx0bW9kdWxlc1ttb2R1bGVJZF0uY2FsbChtb2R1bGUuZXhwb3J0cywgbW9kdWxlLCBtb2R1bGUuZXhwb3J0cywgX193ZWJwYWNrX3JlcXVpcmVfXyk7XG5cbiBcdFx0Ly8gRmxhZyB0aGUgbW9kdWxlIGFzIGxvYWRlZFxuIFx0XHRtb2R1bGUubCA9IHRydWU7XG5cbiBcdFx0Ly8gUmV0dXJuIHRoZSBleHBvcnRzIG9mIHRoZSBtb2R1bGVcbiBcdFx0cmV0dXJuIG1vZHVsZS5leHBvcnRzO1xuIFx0fVxuXG5cbiBcdC8vIGV4cG9zZSB0aGUgbW9kdWxlcyBvYmplY3QgKF9fd2VicGFja19tb2R1bGVzX18pXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm0gPSBtb2R1bGVzO1xuXG4gXHQvLyBleHBvc2UgdGhlIG1vZHVsZSBjYWNoZVxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5jID0gaW5zdGFsbGVkTW9kdWxlcztcblxuIFx0Ly8gaWRlbnRpdHkgZnVuY3Rpb24gZm9yIGNhbGxpbmcgaGFybW9ueSBpbXBvcnRzIHdpdGggdGhlIGNvcnJlY3QgY29udGV4dFxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5pID0gZnVuY3Rpb24odmFsdWUpIHsgcmV0dXJuIHZhbHVlOyB9O1xuXG4gXHQvLyBkZWZpbmUgZ2V0dGVyIGZ1bmN0aW9uIGZvciBoYXJtb255IGV4cG9ydHNcbiBcdF9fd2VicGFja19yZXF1aXJlX18uZCA9IGZ1bmN0aW9uKGV4cG9ydHMsIG5hbWUsIGdldHRlcikge1xuIFx0XHRpZighX193ZWJwYWNrX3JlcXVpcmVfXy5vKGV4cG9ydHMsIG5hbWUpKSB7XG4gXHRcdFx0T2JqZWN0LmRlZmluZVByb3BlcnR5KGV4cG9ydHMsIG5hbWUsIHtcbiBcdFx0XHRcdGNvbmZpZ3VyYWJsZTogZmFsc2UsXG4gXHRcdFx0XHRlbnVtZXJhYmxlOiB0cnVlLFxuIFx0XHRcdFx0Z2V0OiBnZXR0ZXJcbiBcdFx0XHR9KTtcbiBcdFx0fVxuIFx0fTtcblxuIFx0Ly8gZ2V0RGVmYXVsdEV4cG9ydCBmdW5jdGlvbiBmb3IgY29tcGF0aWJpbGl0eSB3aXRoIG5vbi1oYXJtb255IG1vZHVsZXNcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubiA9IGZ1bmN0aW9uKG1vZHVsZSkge1xuIFx0XHR2YXIgZ2V0dGVyID0gbW9kdWxlICYmIG1vZHVsZS5fX2VzTW9kdWxlID9cbiBcdFx0XHRmdW5jdGlvbiBnZXREZWZhdWx0KCkgeyByZXR1cm4gbW9kdWxlWydkZWZhdWx0J107IH0gOlxuIFx0XHRcdGZ1bmN0aW9uIGdldE1vZHVsZUV4cG9ydHMoKSB7IHJldHVybiBtb2R1bGU7IH07XG4gXHRcdF9fd2VicGFja19yZXF1aXJlX18uZChnZXR0ZXIsICdhJywgZ2V0dGVyKTtcbiBcdFx0cmV0dXJuIGdldHRlcjtcbiBcdH07XG5cbiBcdC8vIE9iamVjdC5wcm90b3R5cGUuaGFzT3duUHJvcGVydHkuY2FsbFxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5vID0gZnVuY3Rpb24ob2JqZWN0LCBwcm9wZXJ0eSkgeyByZXR1cm4gT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eS5jYWxsKG9iamVjdCwgcHJvcGVydHkpOyB9O1xuXG4gXHQvLyBfX3dlYnBhY2tfcHVibGljX3BhdGhfX1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5wID0gXCJcIjtcblxuIFx0Ly8gTG9hZCBlbnRyeSBtb2R1bGUgYW5kIHJldHVybiBleHBvcnRzXG4gXHRyZXR1cm4gX193ZWJwYWNrX3JlcXVpcmVfXyhfX3dlYnBhY2tfcmVxdWlyZV9fLnMgPSAzMjkpO1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIHdlYnBhY2svYm9vdHN0cmFwIDY4ZTgyOTFmMTM2MDcwZjI3NmJkIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG5jbGFzcyBQb3NpdGlvbnNMaXN0SGFuZGxlciB7XG4gIGNvbnN0cnVjdG9yKCkge1xuICAgIGlmICgkKFwiI3Bvc2l0aW9uLWZpbHRlcnNcIikubGVuZ3RoID09PSAwKSB7XG4gICAgICByZXR1cm47XG4gICAgfVxuXG4gICAgY29uc3Qgc2VsZiA9IHRoaXM7XG4gICAgc2VsZi4kcGFuZWxTZWxlY3Rpb24gPSAkKFwiI21vZHVsZXMtcG9zaXRpb24tc2VsZWN0aW9uLXBhbmVsXCIpO1xuICAgIHNlbGYuJHBhbmVsU2VsZWN0aW9uU2luZ2xlU2VsZWN0aW9uID0gJChcIiNtb2R1bGVzLXBvc2l0aW9uLXNpbmdsZS1zZWxlY3Rpb25cIik7XG4gICAgc2VsZi4kcGFuZWxTZWxlY3Rpb25NdWx0aXBsZVNlbGVjdGlvbiA9ICQoXCIjbW9kdWxlcy1wb3NpdGlvbi1tdWx0aXBsZS1zZWxlY3Rpb25cIik7XG5cbiAgICBzZWxmLiRwYW5lbFNlbGVjdGlvbk9yaWdpbmFsWSA9IHNlbGYuJHBhbmVsU2VsZWN0aW9uLm9mZnNldCgpLnRvcDtcbiAgICBzZWxmLiRzaG93TW9kdWxlcyA9ICQoXCIjc2hvdy1tb2R1bGVzXCIpO1xuICAgIHNlbGYuJG1vZHVsZXNMaXN0ID0gJCgnLm1vZHVsZXMtcG9zaXRpb24tY2hlY2tib3gnKTtcbiAgICBzZWxmLiRob29rUG9zaXRpb24gPSAkKFwiI2hvb2stcG9zaXRpb25cIik7XG4gICAgc2VsZi4kaG9va1NlYXJjaCA9ICQoXCIjaG9vay1zZWFyY2hcIik7XG4gICAgc2VsZi4kbW9kdWxlUG9zaXRpb25zRm9ybSA9ICQoJyNtb2R1bGUtcG9zaXRpb25zLWZvcm0nKTtcbiAgICBzZWxmLiRtb2R1bGVVbmhvb2tCdXR0b24gPSAkKCcjdW5ob29rLWJ1dHRvbi1wb3NpdGlvbi1ib3R0b20nKTtcbiAgICBzZWxmLiRtb2R1bGVCdXR0b25zVXBkYXRlID0gJCgnLm1vZHVsZS1idXR0b25zLXVwZGF0ZSAuYnRuJyk7XG5cbiAgICBzZWxmLmhhbmRsZUxpc3QoKTtcbiAgICBzZWxmLmhhbmRsZVNvcnRhYmxlKCk7XG5cbiAgICAkKCdpbnB1dFtuYW1lPVwiZm9ybVtnZW5lcmFsXVtlbmFibGVfdG9zXVwiXScpLm9uKCdjaGFuZ2UnLCAoKSA9PiBzZWxmLmhhbmRsZSgpKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBIYW5kbGUgYWxsIGV2ZW50cyBmb3IgRGVzaWduIC0+IFBvc2l0aW9ucyBMaXN0XG4gICAqL1xuICBoYW5kbGVMaXN0KCkge1xuICAgIGNvbnN0IHNlbGYgPSB0aGlzO1xuXG4gICAgJCh3aW5kb3cpLm9uKCdzY3JvbGwnLCAoKSA9PiB7XG4gICAgICBjb25zdCAkc2Nyb2xsVG9wID0gJCh3aW5kb3cpLnNjcm9sbFRvcCgpO1xuICAgICAgc2VsZi4kcGFuZWxTZWxlY3Rpb24uY3NzKFxuICAgICAgICAndG9wJyxcbiAgICAgICAgJHNjcm9sbFRvcCA8IDIwID8gMCA6ICRzY3JvbGxUb3AgLSBzZWxmLiRwYW5lbFNlbGVjdGlvbk9yaWdpbmFsWVxuICAgICAgKTtcbiAgICB9KTtcblxuICAgIHNlbGYuJG1vZHVsZXNMaXN0Lm9uKCdjaGFuZ2UnLCBmdW5jdGlvbiAoKSB7XG4gICAgICBjb25zdCAkY2hlY2tlZENvdW50ID0gc2VsZi4kbW9kdWxlc0xpc3QuZmlsdGVyKCc6Y2hlY2tlZCcpLmxlbmd0aDtcblxuICAgICAgaWYgKCRjaGVja2VkQ291bnQgPT09IDApIHtcbiAgICAgICAgc2VsZi4kbW9kdWxlVW5ob29rQnV0dG9uLmhpZGUoKTtcbiAgICAgICAgc2VsZi4kcGFuZWxTZWxlY3Rpb24uaGlkZSgpO1xuICAgICAgICBzZWxmLiRwYW5lbFNlbGVjdGlvblNpbmdsZVNlbGVjdGlvbi5oaWRlKCk7XG4gICAgICAgIHNlbGYuJHBhbmVsU2VsZWN0aW9uTXVsdGlwbGVTZWxlY3Rpb24uaGlkZSgpO1xuICAgICAgfSBlbHNlIGlmICgkY2hlY2tlZENvdW50ID09PSAxKSB7XG4gICAgICAgIHNlbGYuJG1vZHVsZVVuaG9va0J1dHRvbi5zaG93KCk7XG4gICAgICAgIHNlbGYuJHBhbmVsU2VsZWN0aW9uLnNob3coKTtcbiAgICAgICAgc2VsZi4kcGFuZWxTZWxlY3Rpb25TaW5nbGVTZWxlY3Rpb24uc2hvdygpO1xuICAgICAgICBzZWxmLiRwYW5lbFNlbGVjdGlvbk11bHRpcGxlU2VsZWN0aW9uLmhpZGUoKTtcbiAgICAgIH0gZWxzZSB7XG4gICAgICAgIHNlbGYuJG1vZHVsZVVuaG9va0J1dHRvbi5zaG93KCk7XG4gICAgICAgIHNlbGYuJHBhbmVsU2VsZWN0aW9uLnNob3coKTtcbiAgICAgICAgc2VsZi4kcGFuZWxTZWxlY3Rpb25TaW5nbGVTZWxlY3Rpb24uaGlkZSgpO1xuICAgICAgICBzZWxmLiRwYW5lbFNlbGVjdGlvbk11bHRpcGxlU2VsZWN0aW9uLnNob3coKTtcbiAgICAgICAgJCgnI21vZHVsZXMtcG9zaXRpb24tc2VsZWN0aW9uLWNvdW50JykuaHRtbCgkY2hlY2tlZENvdW50KTtcbiAgICAgIH1cbiAgICB9KTtcblxuICAgIHNlbGYuJHBhbmVsU2VsZWN0aW9uLmZpbmQoJ2J1dHRvbicpLmNsaWNrKCgpID0+IHtcbiAgICAgICQoJ2J1dHRvbltuYW1lPVwidW5ob29rZm9ybVwiXScpLnRyaWdnZXIoJ2NsaWNrJyk7XG4gICAgfSk7XG5cbiAgICBzZWxmLiRob29rc0xpc3QgPSBbXTtcbiAgICAkKCdzZWN0aW9uLmhvb2stcGFuZWwgLmhvb2stbmFtZScpLmVhY2goZnVuY3Rpb24gKCkge1xuICAgICAgY29uc3QgJHRoaXMgPSAkKHRoaXMpO1xuICAgICAgc2VsZi4kaG9va3NMaXN0LnB1c2goe1xuICAgICAgICAndGl0bGUnOiAkdGhpcy5odG1sKCksXG4gICAgICAgICdlbGVtZW50JzogJHRoaXMsXG4gICAgICAgICdjb250YWluZXInOiAkdGhpcy5wYXJlbnRzKCcuaG9vay1wYW5lbCcpXG4gICAgICB9KTtcbiAgICB9KTtcblxuICAgIHNlbGYuJHNob3dNb2R1bGVzLnNlbGVjdDIoKTtcbiAgICBzZWxmLiRzaG93TW9kdWxlcy5vbignY2hhbmdlJywgKCkgPT4ge1xuICAgICAgc2VsZi5tb2R1bGVzUG9zaXRpb25GaWx0ZXJIb29rcygpO1xuICAgIH0pO1xuXG4gICAgc2VsZi4kaG9va1Bvc2l0aW9uLm9uKCdjaGFuZ2UnLCAoKSA9PiB7XG4gICAgICBzZWxmLm1vZHVsZXNQb3NpdGlvbkZpbHRlckhvb2tzKCk7XG4gICAgfSk7XG5cbiAgICBzZWxmLiRob29rU2VhcmNoLm9uKCdpbnB1dCcsICgpID0+IHtcbiAgICAgIHNlbGYubW9kdWxlc1Bvc2l0aW9uRmlsdGVySG9va3MoKTtcbiAgICB9KTtcblxuICAgIHNlbGYuJGhvb2tTZWFyY2gub24oJ2tleXByZXNzJywgKGUpID0+IHtcbiAgICAgIGNvbnN0IGtleUNvZGUgPSBlLmtleUNvZGUgfHwgZS53aGljaDtcbiAgICAgIHJldHVybiBrZXlDb2RlICE9PSAxMztcbiAgICB9KTtcblxuICAgICQoJy5ob29rLWNoZWNrZXInKS5vbignY2xpY2snLCBmdW5jdGlvbigpIHtcbiAgICAgICQoYC5ob29rJHskKHRoaXMpLmRhdGEoJ2hvb2staWQnKX1gKS5wcm9wKCdjaGVja2VkJywgJCh0aGlzKS5wcm9wKCdjaGVja2VkJykpO1xuICAgIH0pO1xuXG4gICAgc2VsZi4kbW9kdWxlc0xpc3Qub24oJ2NsaWNrJywgZnVuY3Rpb24oKSB7XG4gICAgICAkKGAjR2hvb2skeyQodGhpcykuZGF0YSgnaG9vay1pZCcpfWApLnByb3AoXG4gICAgICAgICdjaGVja2VkJyxcbiAgICAgICAgJChgLmhvb2skeyQodGhpcykuZGF0YSgnaG9vay1pZCcpfTpub3QoOmNoZWNrZWQpYCkubGVuZ3RoID09PSAwXG4gICAgICApO1xuICAgIH0pO1xuXG4gICAgc2VsZi4kbW9kdWxlQnV0dG9uc1VwZGF0ZS5vbignY2xpY2snLCBmdW5jdGlvbigpIHtcbiAgICAgIGNvbnN0ICRidG4gPSAkKHRoaXMpO1xuICAgICAgY29uc3QgJGN1cnJlbnQgPSAkYnRuLmNsb3Nlc3QoJy5tb2R1bGUtaXRlbScpO1xuICAgICAgbGV0ICRkZXN0aW5hdGlvbjtcblxuICAgICAgaWYgKCRidG4uZGF0YSgnd2F5JykpIHtcbiAgICAgICAgJGRlc3RpbmF0aW9uID0gJGN1cnJlbnQubmV4dCgnLm1vZHVsZS1pdGVtJyk7XG4gICAgICB9IGVsc2Uge1xuICAgICAgICAkZGVzdGluYXRpb24gPSAkY3VycmVudC5wcmV2KCcubW9kdWxlLWl0ZW0nKTtcbiAgICAgIH1cblxuICAgICAgaWYgKCRkZXN0aW5hdGlvbi5sZW5ndGggPT09IDApIHtcbiAgICAgICAgcmV0dXJuIGZhbHNlO1xuICAgICAgfVxuXG4gICAgICBpZiAoJGJ0bi5kYXRhKCd3YXknKSkge1xuICAgICAgICAkY3VycmVudC5pbnNlcnRBZnRlcigkZGVzdGluYXRpb24pO1xuICAgICAgfSBlbHNlIHtcbiAgICAgICAgJGN1cnJlbnQuaW5zZXJ0QmVmb3JlKCRkZXN0aW5hdGlvbik7XG4gICAgICB9XG5cbiAgICAgIHNlbGYudXBkYXRlUG9zaXRpb25zKFxuICAgICAgICB7XG4gICAgICAgICAgaG9va0lkOiAkYnRuLmRhdGEoJ2hvb2staWQnKSxcbiAgICAgICAgICBtb2R1bGVJZDogJGJ0bi5kYXRhKCdtb2R1bGUtaWQnKSxcbiAgICAgICAgICB3YXk6ICRidG4uZGF0YSgnd2F5JyksXG4gICAgICAgICAgcG9zaXRpb25zOiBbXSxcbiAgICAgICAgfSxcbiAgICAgICAgJGJ0bi5jbG9zZXN0KCd1bCcpXG4gICAgICApO1xuXG4gICAgICByZXR1cm4gZmFsc2U7XG4gICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogSGFuZGxlIHNvcnRhYmxlIGV2ZW50c1xuICAgKi9cbiAgaGFuZGxlU29ydGFibGUoKSB7XG4gICAgY29uc3Qgc2VsZiA9IHRoaXM7XG5cbiAgICAkKCcuc29ydGFibGUnKS5zb3J0YWJsZSh7XG4gICAgICBmb3JjZVBsYWNlaG9sZGVyU2l6ZTogdHJ1ZSxcbiAgICAgIHN0YXJ0OiBmdW5jdGlvbihlLCB1aSkge1xuICAgICAgICAkKHRoaXMpLmRhdGEoJ3ByZXZpb3VzLWluZGV4JywgdWkuaXRlbS5pbmRleCgpKTtcbiAgICAgIH0sXG4gICAgICB1cGRhdGU6IGZ1bmN0aW9uKCRldmVudCwgdWkpIHtcbiAgICAgICAgY29uc3QgWyBob29rSWQsIG1vZHVsZUlkIF0gPSB1aS5pdGVtLmF0dHIoJ2lkJykuc3BsaXQoJ18nKTtcblxuICAgICAgICBjb25zdCAkZGF0YSA9IHtcbiAgICAgICAgICBob29rSWQsXG4gICAgICAgICAgbW9kdWxlSWQsXG4gICAgICAgICAgd2F5OiAoJCh0aGlzKS5kYXRhKCdwcmV2aW91cy1pbmRleCcpIDwgdWkuaXRlbS5pbmRleCgpKSA/IDEgOiAwLFxuICAgICAgICAgIHBvc2l0aW9uczogW10sXG4gICAgICAgIH07XG5cbiAgICAgICAgc2VsZi51cGRhdGVQb3NpdGlvbnMoXG4gICAgICAgICAgJGRhdGEsXG4gICAgICAgICAgJCgkZXZlbnQudGFyZ2V0KVxuICAgICAgICApO1xuICAgICAgfSxcbiAgICB9KTtcbiAgfVxuXG4gIHVwZGF0ZVBvc2l0aW9ucygkZGF0YSwgJGxpc3QpIHtcbiAgICBjb25zdCBzZWxmID0gdGhpcztcbiAgICAkLmVhY2goJGxpc3QuY2hpbGRyZW4oKSwgZnVuY3Rpb24oaW5kZXgsIGVsZW1lbnQpIHtcbiAgICAgICRkYXRhLnBvc2l0aW9ucy5wdXNoKCQoZWxlbWVudCkuYXR0cignaWQnKSk7XG4gICAgfSk7XG5cbiAgICAkLmFqYXgoe1xuICAgICAgdHlwZTogJ1BPU1QnLFxuICAgICAgaGVhZGVyczogeydjYWNoZS1jb250cm9sJzogJ25vLWNhY2hlJ30sXG4gICAgICB1cmw6IHNlbGYuJG1vZHVsZVBvc2l0aW9uc0Zvcm0uZGF0YSgndXBkYXRlLXVybCcpLFxuICAgICAgZGF0YTogJGRhdGEsXG4gICAgICBzdWNjZXNzOiAoKSA9PiB7XG4gICAgICAgIGxldCBzdGFydCA9IDA7XG4gICAgICAgICQuZWFjaCgkbGlzdC5jaGlsZHJlbigpLCBmdW5jdGlvbihpbmRleCwgZWxlbWVudCkge1xuICAgICAgICAgIGNvbnNvbGUubG9nKCQoZWxlbWVudCkuZmluZCgnLmluZGV4LXBvc2l0aW9uJykpO1xuICAgICAgICAgICQoZWxlbWVudCkuZmluZCgnLmluZGV4LXBvc2l0aW9uJykuaHRtbCgrK3N0YXJ0KTtcbiAgICAgICAgfSk7XG5cbiAgICAgICAgd2luZG93LnNob3dTdWNjZXNzTWVzc2FnZSh3aW5kb3cudXBkYXRlX3N1Y2Nlc3NfbXNnKTtcbiAgICAgIH1cbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBGaWx0ZXIgaG9va3MgLyBtb2R1bGVzIHNlYXJjaCBhbmQgZXZlcnl0aGluZ1xuICAgKiBhYm91dCBob29rcyBwb3NpdGlvbnMuXG4gICAqL1xuICBtb2R1bGVzUG9zaXRpb25GaWx0ZXJIb29rcygpIHtcbiAgICBjb25zdCBzZWxmID0gdGhpcztcbiAgICBjb25zdCAkaG9va05hbWUgPSBzZWxmLiRob29rU2VhcmNoLnZhbCgpO1xuICAgIGNvbnN0ICRtb2R1bGVJZCA9IHNlbGYuJHNob3dNb2R1bGVzLnZhbCgpO1xuICAgIGNvbnN0ICRyZWdleCA9IG5ldyBSZWdFeHAoYCgkeyRob29rTmFtZX0pYCwgJ2dpJyk7XG5cbiAgICBmb3IgKGxldCAkaWQgPSAwOyAkaWQgPCBzZWxmLiRob29rc0xpc3QubGVuZ3RoOyAkaWQrKykge1xuICAgICAgc2VsZi4kaG9va3NMaXN0WyRpZF0uY29udGFpbmVyLnRvZ2dsZSgkaG9va05hbWUgPT09ICcnICYmICRtb2R1bGVJZCA9PT0gJ2FsbCcpO1xuICAgICAgc2VsZi4kaG9va3NMaXN0WyRpZF0uZWxlbWVudC5odG1sKHNlbGYuJGhvb2tzTGlzdFskaWRdLnRpdGxlKTtcbiAgICAgIHNlbGYuJGhvb2tzTGlzdFskaWRdLmNvbnRhaW5lci5maW5kKCcubW9kdWxlLWl0ZW0nKS5yZW1vdmVDbGFzcygnaGlnaGxpZ2h0Jyk7XG4gICAgfVxuXG4gICAgLy8gSGF2ZSBzZWxlY3QgYSBob29rIG5hbWUgb3IgYSBtb2R1bGUgaWRcbiAgICBpZiAoJGhvb2tOYW1lICE9PSAnJyB8fCAkbW9kdWxlSWQgIT09ICdhbGwnKSB7XG4gICAgICAvLyBQcmVwYXJlIHNldCBvZiBtYXRjaGVkIGVsZW1lbnRzXG4gICAgICBsZXQgJGhvb2tzVG9TaG93RnJvbU1vZHVsZSA9ICQoKTtcbiAgICAgIGxldCAkaG9va3NUb1Nob3dGcm9tSG9va05hbWUgPSAkKCk7XG4gICAgICBsZXQgJGN1cnJlbnRIb29rcztcbiAgICAgIGxldCAkc3RhcnQ7XG5cbiAgICAgIGZvciAobGV0ICRpZCA9IDA7ICRpZCA8IHNlbGYuJGhvb2tzTGlzdC5sZW5ndGg7ICRpZCsrKSB7XG4gICAgICAgIC8vIFByZXBhcmUgaGlnaGxpZ2h0IHdoZW4gb25lIG1vZHVsZSBpcyBzZWxlY3RlZFxuICAgICAgICBpZiAoJG1vZHVsZUlkICE9PSAnYWxsJykge1xuICAgICAgICAgICRjdXJyZW50SG9va3MgPSBzZWxmLiRob29rc0xpc3RbJGlkXS5jb250YWluZXIuZmluZChgLm1vZHVsZS1wb3NpdGlvbi0keyRtb2R1bGVJZH1gKTtcbiAgICAgICAgICBpZiAoJGN1cnJlbnRIb29rcy5sZW5ndGggPiAwKSB7XG4gICAgICAgICAgICAkaG9va3NUb1Nob3dGcm9tTW9kdWxlID0gJGhvb2tzVG9TaG93RnJvbU1vZHVsZS5hZGQoc2VsZi4kaG9va3NMaXN0WyRpZF0uY29udGFpbmVyKTtcbiAgICAgICAgICAgICRjdXJyZW50SG9va3MuYWRkQ2xhc3MoJ2hpZ2hsaWdodCcpO1xuICAgICAgICAgIH1cbiAgICAgICAgfVxuXG4gICAgICAgIC8vIFByZXBhcmUgaGlnaGxpZ2h0IHdoZW4gdGhlcmUgaXMgYSBob29rIG5hbWVcbiAgICAgICAgaWYgKCRob29rTmFtZSAhPT0gJycpIHtcbiAgICAgICAgICAkc3RhcnQgPSBzZWxmLiRob29rc0xpc3RbJGlkXS50aXRsZS50b0xvd2VyQ2FzZSgpLnNlYXJjaCgkaG9va05hbWUudG9Mb3dlckNhc2UoKSk7XG4gICAgICAgICAgaWYgKCRzdGFydCAhPT0gLTEpIHtcbiAgICAgICAgICAgICRob29rc1RvU2hvd0Zyb21Ib29rTmFtZSA9ICRob29rc1RvU2hvd0Zyb21Ib29rTmFtZS5hZGQoc2VsZi4kaG9va3NMaXN0WyRpZF0uY29udGFpbmVyKTtcbiAgICAgICAgICAgIHNlbGYuJGhvb2tzTGlzdFskaWRdLmVsZW1lbnQuaHRtbChcbiAgICAgICAgICAgICAgc2VsZi4kaG9va3NMaXN0WyRpZF0udGl0bGUucmVwbGFjZShcbiAgICAgICAgICAgICAgICAkcmVnZXgsXG4gICAgICAgICAgICAgICAgJzxzcGFuIGNsYXNzPVwiaGlnaGxpZ2h0XCI+JDE8L3NwYW4+J1xuICAgICAgICAgICAgICApXG4gICAgICAgICAgICApO1xuICAgICAgICAgIH1cbiAgICAgICAgfVxuICAgICAgfVxuXG4gICAgICAvLyBOb3RoaW5nIHNlbGVjdGVkXG4gICAgICBpZiAoJG1vZHVsZUlkID09PSAnYWxsJyAmJiAkaG9va05hbWUgIT09ICcnKSB7XG4gICAgICAgICRob29rc1RvU2hvd0Zyb21Ib29rTmFtZS5zaG93KCk7XG4gICAgICB9IGVsc2UgaWYgKCRob29rTmFtZSA9PT0gJycgJiYgJG1vZHVsZUlkICE9PSAnYWxsJykgeyAvLyBIYXZlIG5vIGhvb2sgYnVnIGhhdmUgYSBtb2R1bGVcbiAgICAgICAgJGhvb2tzVG9TaG93RnJvbU1vZHVsZS5zaG93KCk7XG4gICAgICB9IGVsc2UgeyAvLyBCb3RoIHNlbGVjdGVkXG4gICAgICAgICRob29rc1RvU2hvd0Zyb21Ib29rTmFtZS5maWx0ZXIoJGhvb2tzVG9TaG93RnJvbU1vZHVsZSkuc2hvdygpO1xuICAgICAgfVxuICAgIH1cblxuICAgIGlmICghc2VsZi4kaG9va1Bvc2l0aW9uLnByb3AoJ2NoZWNrZWQnKSkge1xuICAgICAgZm9yIChsZXQgJGlkID0gMDsgJGlkIDwgc2VsZi4kaG9va3NMaXN0Lmxlbmd0aDsgJGlkKyspIHtcbiAgICAgICAgaWYgKHNlbGYuJGhvb2tzTGlzdFskaWRdLmNvbnRhaW5lci5pcygnLmhvb2stcG9zaXRpb24nKSkge1xuICAgICAgICAgIHNlbGYuJGhvb2tzTGlzdFskaWRdLmNvbnRhaW5lci5oaWRlKCk7XG4gICAgICAgIH1cbiAgICAgIH1cbiAgICB9XG4gIH07XG59XG5cbmV4cG9ydCBkZWZhdWx0IFBvc2l0aW9uc0xpc3RIYW5kbGVyO1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvcGFnZXMvaW1wcm92ZS9kZXNpZ25fcG9zaXRpb25zL3Bvc2l0aW9ucy1saXN0LWhhbmRsZXIuanMiLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5pbXBvcnQgUG9zaXRpb25zTGlzdEhhbmRsZXIgZnJvbSAnLi9wb3NpdGlvbnMtbGlzdC1oYW5kbGVyJztcblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4kKCgpID0+IHtcbiAgbmV3IFBvc2l0aW9uc0xpc3RIYW5kbGVyKCk7XG59KTtcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL3BhZ2VzL2ltcHJvdmUvZGVzaWduX3Bvc2l0aW9ucy9pbmRleC5qcyJdLCJzb3VyY2VSb290IjoiIn0=