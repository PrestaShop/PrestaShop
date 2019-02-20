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
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
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
/******/ 	__webpack_require__.p = "/admin-dev/themes/new-theme/public/";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = "./js/pages/improve/design_positions/index.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./js/pages/improve/design_positions/index.js":
/*!****************************************************!*\
  !*** ./js/pages/improve/design_positions/index.js ***!
  \****************************************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _positions_list_handler__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./positions-list-handler */ "./js/pages/improve/design_positions/positions-list-handler.js");
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
$(function () {
  new _positions_list_handler__WEBPACK_IMPORTED_MODULE_0__["default"]();
});

/***/ }),

/***/ "./js/pages/improve/design_positions/positions-list-handler.js":
/*!*********************************************************************!*\
  !*** ./js/pages/improve/design_positions/positions-list-handler.js ***!
  \*********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
function _slicedToArray(arr, i) { return _arrayWithHoles(arr) || _iterableToArrayLimit(arr, i) || _nonIterableRest(); }

function _nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance"); }

function _iterableToArrayLimit(arr, i) { var _arr = []; var _n = true; var _d = false; var _e = undefined; try { for (var _i = arr[Symbol.iterator](), _s; !(_n = (_s = _i.next()).done); _n = true) { _arr.push(_s.value); if (i && _arr.length === i) break; } } catch (err) { _d = true; _e = err; } finally { try { if (!_n && _i["return"] != null) _i["return"](); } finally { if (_d) throw _e; } } return _arr; }

function _arrayWithHoles(arr) { if (Array.isArray(arr)) return arr; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

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

var PositionsListHandler =
/*#__PURE__*/
function () {
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
        $(".hook".concat($(this).data('hook-id'))).prop('checked', $(this).prop('checked'));
      });
      self.$modulesList.on('click', function () {
        $("#Ghook".concat($(this).data('hook-id'))).prop('checked', $(".hook".concat($(this).data('hook-id'), ":not(:checked)")).length === 0);
      });
      self.$moduleButtonsUpdate.on('click', function () {
        var $btn = $(this);
        var $current = $btn.closest('.module-item');
        var $destination;

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
        headers: {
          'cache-control': 'no-cache'
        },
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
      var $regex = new RegExp("(".concat($hookName, ")"), 'gi');

      for (var $id = 0; $id < self.$hooksList.length; $id++) {
        self.$hooksList[$id].container.toggle($hookName === '' && $moduleId === 'all');
        self.$hooksList[$id].element.html(self.$hooksList[$id].title);
        self.$hooksList[$id].container.find('.module-item').removeClass('highlight');
      } // Have select a hook name or a module id


      if ($hookName !== '' || $moduleId !== 'all') {
        // Prepare set of matched elements
        var $hooksToShowFromModule = $();
        var $hooksToShowFromHookName = $();
        var $currentHooks;
        var $start;

        for (var _$id = 0; _$id < self.$hooksList.length; _$id++) {
          // Prepare highlight when one module is selected
          if ($moduleId !== 'all') {
            $currentHooks = self.$hooksList[_$id].container.find(".module-position-".concat($moduleId));

            if ($currentHooks.length > 0) {
              $hooksToShowFromModule = $hooksToShowFromModule.add(self.$hooksList[_$id].container);
              $currentHooks.addClass('highlight');
            }
          } // Prepare highlight when there is a hook name


          if ($hookName !== '') {
            $start = self.$hooksList[_$id].title.toLowerCase().search($hookName.toLowerCase());

            if ($start !== -1) {
              $hooksToShowFromHookName = $hooksToShowFromHookName.add(self.$hooksList[_$id].container);

              self.$hooksList[_$id].element.html(self.$hooksList[_$id].title.replace($regex, '<span class="highlight">$1</span>'));
            }
          }
        } // Nothing selected


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

/* harmony default export */ __webpack_exports__["default"] = (PositionsListHandler);

/***/ })

/******/ });
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAiLCJ3ZWJwYWNrOi8vLy4vanMvcGFnZXMvaW1wcm92ZS9kZXNpZ25fcG9zaXRpb25zL2luZGV4LmpzIiwid2VicGFjazovLy8uL2pzL3BhZ2VzL2ltcHJvdmUvZGVzaWduX3Bvc2l0aW9ucy9wb3NpdGlvbnMtbGlzdC1oYW5kbGVyLmpzIl0sIm5hbWVzIjpbIiQiLCJ3aW5kb3ciLCJQb3NpdGlvbnNMaXN0SGFuZGxlciIsImxlbmd0aCIsInNlbGYiLCIkcGFuZWxTZWxlY3Rpb24iLCIkcGFuZWxTZWxlY3Rpb25TaW5nbGVTZWxlY3Rpb24iLCIkcGFuZWxTZWxlY3Rpb25NdWx0aXBsZVNlbGVjdGlvbiIsIiRwYW5lbFNlbGVjdGlvbk9yaWdpbmFsWSIsIm9mZnNldCIsInRvcCIsIiRzaG93TW9kdWxlcyIsIiRtb2R1bGVzTGlzdCIsIiRob29rUG9zaXRpb24iLCIkaG9va1NlYXJjaCIsIiRtb2R1bGVQb3NpdGlvbnNGb3JtIiwiJG1vZHVsZVVuaG9va0J1dHRvbiIsIiRtb2R1bGVCdXR0b25zVXBkYXRlIiwiaGFuZGxlTGlzdCIsImhhbmRsZVNvcnRhYmxlIiwib24iLCJoYW5kbGUiLCIkc2Nyb2xsVG9wIiwic2Nyb2xsVG9wIiwiY3NzIiwiJGNoZWNrZWRDb3VudCIsImZpbHRlciIsImhpZGUiLCJzaG93IiwiaHRtbCIsImZpbmQiLCJjbGljayIsInRyaWdnZXIiLCIkaG9va3NMaXN0IiwiZWFjaCIsIiR0aGlzIiwicHVzaCIsInBhcmVudHMiLCJzZWxlY3QyIiwibW9kdWxlc1Bvc2l0aW9uRmlsdGVySG9va3MiLCJlIiwia2V5Q29kZSIsIndoaWNoIiwiZGF0YSIsInByb3AiLCIkYnRuIiwiJGN1cnJlbnQiLCJjbG9zZXN0IiwiJGRlc3RpbmF0aW9uIiwibmV4dCIsInByZXYiLCJpbnNlcnRBZnRlciIsImluc2VydEJlZm9yZSIsInVwZGF0ZVBvc2l0aW9ucyIsImhvb2tJZCIsIm1vZHVsZUlkIiwid2F5IiwicG9zaXRpb25zIiwic29ydGFibGUiLCJmb3JjZVBsYWNlaG9sZGVyU2l6ZSIsInN0YXJ0IiwidWkiLCJpdGVtIiwiaW5kZXgiLCJ1cGRhdGUiLCIkZXZlbnQiLCJhdHRyIiwic3BsaXQiLCIkZGF0YSIsInRhcmdldCIsIiRsaXN0IiwiY2hpbGRyZW4iLCJlbGVtZW50IiwiYWpheCIsInR5cGUiLCJoZWFkZXJzIiwidXJsIiwic3VjY2VzcyIsImNvbnNvbGUiLCJsb2ciLCJzaG93U3VjY2Vzc01lc3NhZ2UiLCJ1cGRhdGVfc3VjY2Vzc19tc2ciLCIkaG9va05hbWUiLCJ2YWwiLCIkbW9kdWxlSWQiLCIkcmVnZXgiLCJSZWdFeHAiLCIkaWQiLCJjb250YWluZXIiLCJ0b2dnbGUiLCJ0aXRsZSIsInJlbW92ZUNsYXNzIiwiJGhvb2tzVG9TaG93RnJvbU1vZHVsZSIsIiRob29rc1RvU2hvd0Zyb21Ib29rTmFtZSIsIiRjdXJyZW50SG9va3MiLCIkc3RhcnQiLCJhZGQiLCJhZGRDbGFzcyIsInRvTG93ZXJDYXNlIiwic2VhcmNoIiwicmVwbGFjZSIsImlzIl0sIm1hcHBpbmdzIjoiO0FBQUE7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7OztBQUdBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQSxrREFBMEMsZ0NBQWdDO0FBQzFFO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0EsZ0VBQXdELGtCQUFrQjtBQUMxRTtBQUNBLHlEQUFpRCxjQUFjO0FBQy9EOztBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQSxpREFBeUMsaUNBQWlDO0FBQzFFLHdIQUFnSCxtQkFBbUIsRUFBRTtBQUNySTtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBLG1DQUEyQiwwQkFBMEIsRUFBRTtBQUN2RCx5Q0FB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QUFJLENBQUNTLGFBQUwsQ0FBbUIrQixJQUFuQixDQUF3QixTQUF4QixDQUFMLEVBQXlDO0FBQ3ZDLGFBQUssSUFBSTJDLEtBQUcsR0FBRyxDQUFmLEVBQWtCQSxLQUFHLEdBQUduRixJQUFJLENBQUM2QixVQUFMLENBQWdCOUIsTUFBeEMsRUFBZ0RvRixLQUFHLEVBQW5ELEVBQXVEO0FBQ3JELGNBQUluRixJQUFJLENBQUM2QixVQUFMLENBQWdCc0QsS0FBaEIsRUFBcUJDLFNBQXJCLENBQStCYSxFQUEvQixDQUFrQyxnQkFBbEMsQ0FBSixFQUF5RDtBQUN2RGpHLGdCQUFJLENBQUM2QixVQUFMLENBQWdCc0QsS0FBaEIsRUFBcUJDLFNBQXJCLENBQStCN0QsSUFBL0I7QUFDRDtBQUNGO0FBQ0Y7QUFDRjs7Ozs7O0FBR1l6QixtRkFBZixFIiwiZmlsZSI6ImltcHJvdmVfZGVzaWduX3Bvc2l0aW9ucy5idW5kbGUuanMiLCJzb3VyY2VzQ29udGVudCI6WyIgXHQvLyBUaGUgbW9kdWxlIGNhY2hlXG4gXHR2YXIgaW5zdGFsbGVkTW9kdWxlcyA9IHt9O1xuXG4gXHQvLyBUaGUgcmVxdWlyZSBmdW5jdGlvblxuIFx0ZnVuY3Rpb24gX193ZWJwYWNrX3JlcXVpcmVfXyhtb2R1bGVJZCkge1xuXG4gXHRcdC8vIENoZWNrIGlmIG1vZHVsZSBpcyBpbiBjYWNoZVxuIFx0XHRpZihpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXSkge1xuIFx0XHRcdHJldHVybiBpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXS5leHBvcnRzO1xuIFx0XHR9XG4gXHRcdC8vIENyZWF0ZSBhIG5ldyBtb2R1bGUgKGFuZCBwdXQgaXQgaW50byB0aGUgY2FjaGUpXG4gXHRcdHZhciBtb2R1bGUgPSBpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXSA9IHtcbiBcdFx0XHRpOiBtb2R1bGVJZCxcbiBcdFx0XHRsOiBmYWxzZSxcbiBcdFx0XHRleHBvcnRzOiB7fVxuIFx0XHR9O1xuXG4gXHRcdC8vIEV4ZWN1dGUgdGhlIG1vZHVsZSBmdW5jdGlvblxuIFx0XHRtb2R1bGVzW21vZHVsZUlkXS5jYWxsKG1vZHVsZS5leHBvcnRzLCBtb2R1bGUsIG1vZHVsZS5leHBvcnRzLCBfX3dlYnBhY2tfcmVxdWlyZV9fKTtcblxuIFx0XHQvLyBGbGFnIHRoZSBtb2R1bGUgYXMgbG9hZGVkXG4gXHRcdG1vZHVsZS5sID0gdHJ1ZTtcblxuIFx0XHQvLyBSZXR1cm4gdGhlIGV4cG9ydHMgb2YgdGhlIG1vZHVsZVxuIFx0XHRyZXR1cm4gbW9kdWxlLmV4cG9ydHM7XG4gXHR9XG5cblxuIFx0Ly8gZXhwb3NlIHRoZSBtb2R1bGVzIG9iamVjdCAoX193ZWJwYWNrX21vZHVsZXNfXylcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubSA9IG1vZHVsZXM7XG5cbiBcdC8vIGV4cG9zZSB0aGUgbW9kdWxlIGNhY2hlXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmMgPSBpbnN0YWxsZWRNb2R1bGVzO1xuXG4gXHQvLyBkZWZpbmUgZ2V0dGVyIGZ1bmN0aW9uIGZvciBoYXJtb255IGV4cG9ydHNcbiBcdF9fd2VicGFja19yZXF1aXJlX18uZCA9IGZ1bmN0aW9uKGV4cG9ydHMsIG5hbWUsIGdldHRlcikge1xuIFx0XHRpZighX193ZWJwYWNrX3JlcXVpcmVfXy5vKGV4cG9ydHMsIG5hbWUpKSB7XG4gXHRcdFx0T2JqZWN0LmRlZmluZVByb3BlcnR5KGV4cG9ydHMsIG5hbWUsIHsgZW51bWVyYWJsZTogdHJ1ZSwgZ2V0OiBnZXR0ZXIgfSk7XG4gXHRcdH1cbiBcdH07XG5cbiBcdC8vIGRlZmluZSBfX2VzTW9kdWxlIG9uIGV4cG9ydHNcbiBcdF9fd2VicGFja19yZXF1aXJlX18uciA9IGZ1bmN0aW9uKGV4cG9ydHMpIHtcbiBcdFx0aWYodHlwZW9mIFN5bWJvbCAhPT0gJ3VuZGVmaW5lZCcgJiYgU3ltYm9sLnRvU3RyaW5nVGFnKSB7XG4gXHRcdFx0T2JqZWN0LmRlZmluZVByb3BlcnR5KGV4cG9ydHMsIFN5bWJvbC50b1N0cmluZ1RhZywgeyB2YWx1ZTogJ01vZHVsZScgfSk7XG4gXHRcdH1cbiBcdFx0T2JqZWN0LmRlZmluZVByb3BlcnR5KGV4cG9ydHMsICdfX2VzTW9kdWxlJywgeyB2YWx1ZTogdHJ1ZSB9KTtcbiBcdH07XG5cbiBcdC8vIGNyZWF0ZSBhIGZha2UgbmFtZXNwYWNlIG9iamVjdFxuIFx0Ly8gbW9kZSAmIDE6IHZhbHVlIGlzIGEgbW9kdWxlIGlkLCByZXF1aXJlIGl0XG4gXHQvLyBtb2RlICYgMjogbWVyZ2UgYWxsIHByb3BlcnRpZXMgb2YgdmFsdWUgaW50byB0aGUgbnNcbiBcdC8vIG1vZGUgJiA0OiByZXR1cm4gdmFsdWUgd2hlbiBhbHJlYWR5IG5zIG9iamVjdFxuIFx0Ly8gbW9kZSAmIDh8MTogYmVoYXZlIGxpa2UgcmVxdWlyZVxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy50ID0gZnVuY3Rpb24odmFsdWUsIG1vZGUpIHtcbiBcdFx0aWYobW9kZSAmIDEpIHZhbHVlID0gX193ZWJwYWNrX3JlcXVpcmVfXyh2YWx1ZSk7XG4gXHRcdGlmKG1vZGUgJiA4KSByZXR1cm4gdmFsdWU7XG4gXHRcdGlmKChtb2RlICYgNCkgJiYgdHlwZW9mIHZhbHVlID09PSAnb2JqZWN0JyAmJiB2YWx1ZSAmJiB2YWx1ZS5fX2VzTW9kdWxlKSByZXR1cm4gdmFsdWU7XG4gXHRcdHZhciBucyA9IE9iamVjdC5jcmVhdGUobnVsbCk7XG4gXHRcdF9fd2VicGFja19yZXF1aXJlX18ucihucyk7XG4gXHRcdE9iamVjdC5kZWZpbmVQcm9wZXJ0eShucywgJ2RlZmF1bHQnLCB7IGVudW1lcmFibGU6IHRydWUsIHZhbHVlOiB2YWx1ZSB9KTtcbiBcdFx0aWYobW9kZSAmIDIgJiYgdHlwZW9mIHZhbHVlICE9ICdzdHJpbmcnKSBmb3IodmFyIGtleSBpbiB2YWx1ZSkgX193ZWJwYWNrX3JlcXVpcmVfXy5kKG5zLCBrZXksIGZ1bmN0aW9uKGtleSkgeyByZXR1cm4gdmFsdWVba2V5XTsgfS5iaW5kKG51bGwsIGtleSkpO1xuIFx0XHRyZXR1cm4gbnM7XG4gXHR9O1xuXG4gXHQvLyBnZXREZWZhdWx0RXhwb3J0IGZ1bmN0aW9uIGZvciBjb21wYXRpYmlsaXR5IHdpdGggbm9uLWhhcm1vbnkgbW9kdWxlc1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5uID0gZnVuY3Rpb24obW9kdWxlKSB7XG4gXHRcdHZhciBnZXR0ZXIgPSBtb2R1bGUgJiYgbW9kdWxlLl9fZXNNb2R1bGUgP1xuIFx0XHRcdGZ1bmN0aW9uIGdldERlZmF1bHQoKSB7IHJldHVybiBtb2R1bGVbJ2RlZmF1bHQnXTsgfSA6XG4gXHRcdFx0ZnVuY3Rpb24gZ2V0TW9kdWxlRXhwb3J0cygpIHsgcmV0dXJuIG1vZHVsZTsgfTtcbiBcdFx0X193ZWJwYWNrX3JlcXVpcmVfXy5kKGdldHRlciwgJ2EnLCBnZXR0ZXIpO1xuIFx0XHRyZXR1cm4gZ2V0dGVyO1xuIFx0fTtcblxuIFx0Ly8gT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eS5jYWxsXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm8gPSBmdW5jdGlvbihvYmplY3QsIHByb3BlcnR5KSB7IHJldHVybiBPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGwob2JqZWN0LCBwcm9wZXJ0eSk7IH07XG5cbiBcdC8vIF9fd2VicGFja19wdWJsaWNfcGF0aF9fXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLnAgPSBcIi9hZG1pbi1kZXYvdGhlbWVzL25ldy10aGVtZS9wdWJsaWMvXCI7XG5cblxuIFx0Ly8gTG9hZCBlbnRyeSBtb2R1bGUgYW5kIHJldHVybiBleHBvcnRzXG4gXHRyZXR1cm4gX193ZWJwYWNrX3JlcXVpcmVfXyhfX3dlYnBhY2tfcmVxdWlyZV9fLnMgPSBcIi4vanMvcGFnZXMvaW1wcm92ZS9kZXNpZ25fcG9zaXRpb25zL2luZGV4LmpzXCIpO1xuIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuaW1wb3J0IFBvc2l0aW9uc0xpc3RIYW5kbGVyIGZyb20gJy4vcG9zaXRpb25zLWxpc3QtaGFuZGxlcic7XG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuJCgoKSA9PiB7XG4gIG5ldyBQb3NpdGlvbnNMaXN0SGFuZGxlcigpO1xufSk7XG4iLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbmNsYXNzIFBvc2l0aW9uc0xpc3RIYW5kbGVyIHtcbiAgY29uc3RydWN0b3IoKSB7XG4gICAgaWYgKCQoXCIjcG9zaXRpb24tZmlsdGVyc1wiKS5sZW5ndGggPT09IDApIHtcbiAgICAgIHJldHVybjtcbiAgICB9XG5cbiAgICBjb25zdCBzZWxmID0gdGhpcztcbiAgICBzZWxmLiRwYW5lbFNlbGVjdGlvbiA9ICQoXCIjbW9kdWxlcy1wb3NpdGlvbi1zZWxlY3Rpb24tcGFuZWxcIik7XG4gICAgc2VsZi4kcGFuZWxTZWxlY3Rpb25TaW5nbGVTZWxlY3Rpb24gPSAkKFwiI21vZHVsZXMtcG9zaXRpb24tc2luZ2xlLXNlbGVjdGlvblwiKTtcbiAgICBzZWxmLiRwYW5lbFNlbGVjdGlvbk11bHRpcGxlU2VsZWN0aW9uID0gJChcIiNtb2R1bGVzLXBvc2l0aW9uLW11bHRpcGxlLXNlbGVjdGlvblwiKTtcblxuICAgIHNlbGYuJHBhbmVsU2VsZWN0aW9uT3JpZ2luYWxZID0gc2VsZi4kcGFuZWxTZWxlY3Rpb24ub2Zmc2V0KCkudG9wO1xuICAgIHNlbGYuJHNob3dNb2R1bGVzID0gJChcIiNzaG93LW1vZHVsZXNcIik7XG4gICAgc2VsZi4kbW9kdWxlc0xpc3QgPSAkKCcubW9kdWxlcy1wb3NpdGlvbi1jaGVja2JveCcpO1xuICAgIHNlbGYuJGhvb2tQb3NpdGlvbiA9ICQoXCIjaG9vay1wb3NpdGlvblwiKTtcbiAgICBzZWxmLiRob29rU2VhcmNoID0gJChcIiNob29rLXNlYXJjaFwiKTtcbiAgICBzZWxmLiRtb2R1bGVQb3NpdGlvbnNGb3JtID0gJCgnI21vZHVsZS1wb3NpdGlvbnMtZm9ybScpO1xuICAgIHNlbGYuJG1vZHVsZVVuaG9va0J1dHRvbiA9ICQoJyN1bmhvb2stYnV0dG9uLXBvc2l0aW9uLWJvdHRvbScpO1xuICAgIHNlbGYuJG1vZHVsZUJ1dHRvbnNVcGRhdGUgPSAkKCcubW9kdWxlLWJ1dHRvbnMtdXBkYXRlIC5idG4nKTtcblxuICAgIHNlbGYuaGFuZGxlTGlzdCgpO1xuICAgIHNlbGYuaGFuZGxlU29ydGFibGUoKTtcblxuICAgICQoJ2lucHV0W25hbWU9XCJmb3JtW2dlbmVyYWxdW2VuYWJsZV90b3NdXCJdJykub24oJ2NoYW5nZScsICgpID0+IHNlbGYuaGFuZGxlKCkpO1xuICB9XG5cbiAgLyoqXG4gICAqIEhhbmRsZSBhbGwgZXZlbnRzIGZvciBEZXNpZ24gLT4gUG9zaXRpb25zIExpc3RcbiAgICovXG4gIGhhbmRsZUxpc3QoKSB7XG4gICAgY29uc3Qgc2VsZiA9IHRoaXM7XG5cbiAgICAkKHdpbmRvdykub24oJ3Njcm9sbCcsICgpID0+IHtcbiAgICAgIGNvbnN0ICRzY3JvbGxUb3AgPSAkKHdpbmRvdykuc2Nyb2xsVG9wKCk7XG4gICAgICBzZWxmLiRwYW5lbFNlbGVjdGlvbi5jc3MoXG4gICAgICAgICd0b3AnLFxuICAgICAgICAkc2Nyb2xsVG9wIDwgMjAgPyAwIDogJHNjcm9sbFRvcCAtIHNlbGYuJHBhbmVsU2VsZWN0aW9uT3JpZ2luYWxZXG4gICAgICApO1xuICAgIH0pO1xuXG4gICAgc2VsZi4kbW9kdWxlc0xpc3Qub24oJ2NoYW5nZScsIGZ1bmN0aW9uICgpIHtcbiAgICAgIGNvbnN0ICRjaGVja2VkQ291bnQgPSBzZWxmLiRtb2R1bGVzTGlzdC5maWx0ZXIoJzpjaGVja2VkJykubGVuZ3RoO1xuXG4gICAgICBpZiAoJGNoZWNrZWRDb3VudCA9PT0gMCkge1xuICAgICAgICBzZWxmLiRtb2R1bGVVbmhvb2tCdXR0b24uaGlkZSgpO1xuICAgICAgICBzZWxmLiRwYW5lbFNlbGVjdGlvbi5oaWRlKCk7XG4gICAgICAgIHNlbGYuJHBhbmVsU2VsZWN0aW9uU2luZ2xlU2VsZWN0aW9uLmhpZGUoKTtcbiAgICAgICAgc2VsZi4kcGFuZWxTZWxlY3Rpb25NdWx0aXBsZVNlbGVjdGlvbi5oaWRlKCk7XG4gICAgICB9IGVsc2UgaWYgKCRjaGVja2VkQ291bnQgPT09IDEpIHtcbiAgICAgICAgc2VsZi4kbW9kdWxlVW5ob29rQnV0dG9uLnNob3coKTtcbiAgICAgICAgc2VsZi4kcGFuZWxTZWxlY3Rpb24uc2hvdygpO1xuICAgICAgICBzZWxmLiRwYW5lbFNlbGVjdGlvblNpbmdsZVNlbGVjdGlvbi5zaG93KCk7XG4gICAgICAgIHNlbGYuJHBhbmVsU2VsZWN0aW9uTXVsdGlwbGVTZWxlY3Rpb24uaGlkZSgpO1xuICAgICAgfSBlbHNlIHtcbiAgICAgICAgc2VsZi4kbW9kdWxlVW5ob29rQnV0dG9uLnNob3coKTtcbiAgICAgICAgc2VsZi4kcGFuZWxTZWxlY3Rpb24uc2hvdygpO1xuICAgICAgICBzZWxmLiRwYW5lbFNlbGVjdGlvblNpbmdsZVNlbGVjdGlvbi5oaWRlKCk7XG4gICAgICAgIHNlbGYuJHBhbmVsU2VsZWN0aW9uTXVsdGlwbGVTZWxlY3Rpb24uc2hvdygpO1xuICAgICAgICAkKCcjbW9kdWxlcy1wb3NpdGlvbi1zZWxlY3Rpb24tY291bnQnKS5odG1sKCRjaGVja2VkQ291bnQpO1xuICAgICAgfVxuICAgIH0pO1xuXG4gICAgc2VsZi4kcGFuZWxTZWxlY3Rpb24uZmluZCgnYnV0dG9uJykuY2xpY2soKCkgPT4ge1xuICAgICAgJCgnYnV0dG9uW25hbWU9XCJ1bmhvb2tmb3JtXCJdJykudHJpZ2dlcignY2xpY2snKTtcbiAgICB9KTtcblxuICAgIHNlbGYuJGhvb2tzTGlzdCA9IFtdO1xuICAgICQoJ3NlY3Rpb24uaG9vay1wYW5lbCAuaG9vay1uYW1lJykuZWFjaChmdW5jdGlvbiAoKSB7XG4gICAgICBjb25zdCAkdGhpcyA9ICQodGhpcyk7XG4gICAgICBzZWxmLiRob29rc0xpc3QucHVzaCh7XG4gICAgICAgICd0aXRsZSc6ICR0aGlzLmh0bWwoKSxcbiAgICAgICAgJ2VsZW1lbnQnOiAkdGhpcyxcbiAgICAgICAgJ2NvbnRhaW5lcic6ICR0aGlzLnBhcmVudHMoJy5ob29rLXBhbmVsJylcbiAgICAgIH0pO1xuICAgIH0pO1xuXG4gICAgc2VsZi4kc2hvd01vZHVsZXMuc2VsZWN0MigpO1xuICAgIHNlbGYuJHNob3dNb2R1bGVzLm9uKCdjaGFuZ2UnLCAoKSA9PiB7XG4gICAgICBzZWxmLm1vZHVsZXNQb3NpdGlvbkZpbHRlckhvb2tzKCk7XG4gICAgfSk7XG5cbiAgICBzZWxmLiRob29rUG9zaXRpb24ub24oJ2NoYW5nZScsICgpID0+IHtcbiAgICAgIHNlbGYubW9kdWxlc1Bvc2l0aW9uRmlsdGVySG9va3MoKTtcbiAgICB9KTtcblxuICAgIHNlbGYuJGhvb2tTZWFyY2gub24oJ2lucHV0JywgKCkgPT4ge1xuICAgICAgc2VsZi5tb2R1bGVzUG9zaXRpb25GaWx0ZXJIb29rcygpO1xuICAgIH0pO1xuXG4gICAgc2VsZi4kaG9va1NlYXJjaC5vbigna2V5cHJlc3MnLCAoZSkgPT4ge1xuICAgICAgY29uc3Qga2V5Q29kZSA9IGUua2V5Q29kZSB8fCBlLndoaWNoO1xuICAgICAgcmV0dXJuIGtleUNvZGUgIT09IDEzO1xuICAgIH0pO1xuXG4gICAgJCgnLmhvb2stY2hlY2tlcicpLm9uKCdjbGljaycsIGZ1bmN0aW9uKCkge1xuICAgICAgJChgLmhvb2skeyQodGhpcykuZGF0YSgnaG9vay1pZCcpfWApLnByb3AoJ2NoZWNrZWQnLCAkKHRoaXMpLnByb3AoJ2NoZWNrZWQnKSk7XG4gICAgfSk7XG5cbiAgICBzZWxmLiRtb2R1bGVzTGlzdC5vbignY2xpY2snLCBmdW5jdGlvbigpIHtcbiAgICAgICQoYCNHaG9vayR7JCh0aGlzKS5kYXRhKCdob29rLWlkJyl9YCkucHJvcChcbiAgICAgICAgJ2NoZWNrZWQnLFxuICAgICAgICAkKGAuaG9vayR7JCh0aGlzKS5kYXRhKCdob29rLWlkJyl9Om5vdCg6Y2hlY2tlZClgKS5sZW5ndGggPT09IDBcbiAgICAgICk7XG4gICAgfSk7XG5cbiAgICBzZWxmLiRtb2R1bGVCdXR0b25zVXBkYXRlLm9uKCdjbGljaycsIGZ1bmN0aW9uKCkge1xuICAgICAgY29uc3QgJGJ0biA9ICQodGhpcyk7XG4gICAgICBjb25zdCAkY3VycmVudCA9ICRidG4uY2xvc2VzdCgnLm1vZHVsZS1pdGVtJyk7XG4gICAgICBsZXQgJGRlc3RpbmF0aW9uO1xuXG4gICAgICBpZiAoJGJ0bi5kYXRhKCd3YXknKSkge1xuICAgICAgICAkZGVzdGluYXRpb24gPSAkY3VycmVudC5uZXh0KCcubW9kdWxlLWl0ZW0nKTtcbiAgICAgIH0gZWxzZSB7XG4gICAgICAgICRkZXN0aW5hdGlvbiA9ICRjdXJyZW50LnByZXYoJy5tb2R1bGUtaXRlbScpO1xuICAgICAgfVxuXG4gICAgICBpZiAoJGRlc3RpbmF0aW9uLmxlbmd0aCA9PT0gMCkge1xuICAgICAgICByZXR1cm4gZmFsc2U7XG4gICAgICB9XG5cbiAgICAgIGlmICgkYnRuLmRhdGEoJ3dheScpKSB7XG4gICAgICAgICRjdXJyZW50Lmluc2VydEFmdGVyKCRkZXN0aW5hdGlvbik7XG4gICAgICB9IGVsc2Uge1xuICAgICAgICAkY3VycmVudC5pbnNlcnRCZWZvcmUoJGRlc3RpbmF0aW9uKTtcbiAgICAgIH1cblxuICAgICAgc2VsZi51cGRhdGVQb3NpdGlvbnMoXG4gICAgICAgIHtcbiAgICAgICAgICBob29rSWQ6ICRidG4uZGF0YSgnaG9vay1pZCcpLFxuICAgICAgICAgIG1vZHVsZUlkOiAkYnRuLmRhdGEoJ21vZHVsZS1pZCcpLFxuICAgICAgICAgIHdheTogJGJ0bi5kYXRhKCd3YXknKSxcbiAgICAgICAgICBwb3NpdGlvbnM6IFtdLFxuICAgICAgICB9LFxuICAgICAgICAkYnRuLmNsb3Nlc3QoJ3VsJylcbiAgICAgICk7XG5cbiAgICAgIHJldHVybiBmYWxzZTtcbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBIYW5kbGUgc29ydGFibGUgZXZlbnRzXG4gICAqL1xuICBoYW5kbGVTb3J0YWJsZSgpIHtcbiAgICBjb25zdCBzZWxmID0gdGhpcztcblxuICAgICQoJy5zb3J0YWJsZScpLnNvcnRhYmxlKHtcbiAgICAgIGZvcmNlUGxhY2Vob2xkZXJTaXplOiB0cnVlLFxuICAgICAgc3RhcnQ6IGZ1bmN0aW9uKGUsIHVpKSB7XG4gICAgICAgICQodGhpcykuZGF0YSgncHJldmlvdXMtaW5kZXgnLCB1aS5pdGVtLmluZGV4KCkpO1xuICAgICAgfSxcbiAgICAgIHVwZGF0ZTogZnVuY3Rpb24oJGV2ZW50LCB1aSkge1xuICAgICAgICBjb25zdCBbIGhvb2tJZCwgbW9kdWxlSWQgXSA9IHVpLml0ZW0uYXR0cignaWQnKS5zcGxpdCgnXycpO1xuXG4gICAgICAgIGNvbnN0ICRkYXRhID0ge1xuICAgICAgICAgIGhvb2tJZCxcbiAgICAgICAgICBtb2R1bGVJZCxcbiAgICAgICAgICB3YXk6ICgkKHRoaXMpLmRhdGEoJ3ByZXZpb3VzLWluZGV4JykgPCB1aS5pdGVtLmluZGV4KCkpID8gMSA6IDAsXG4gICAgICAgICAgcG9zaXRpb25zOiBbXSxcbiAgICAgICAgfTtcblxuICAgICAgICBzZWxmLnVwZGF0ZVBvc2l0aW9ucyhcbiAgICAgICAgICAkZGF0YSxcbiAgICAgICAgICAkKCRldmVudC50YXJnZXQpXG4gICAgICAgICk7XG4gICAgICB9LFxuICAgIH0pO1xuICB9XG5cbiAgdXBkYXRlUG9zaXRpb25zKCRkYXRhLCAkbGlzdCkge1xuICAgIGNvbnN0IHNlbGYgPSB0aGlzO1xuICAgICQuZWFjaCgkbGlzdC5jaGlsZHJlbigpLCBmdW5jdGlvbihpbmRleCwgZWxlbWVudCkge1xuICAgICAgJGRhdGEucG9zaXRpb25zLnB1c2goJChlbGVtZW50KS5hdHRyKCdpZCcpKTtcbiAgICB9KTtcblxuICAgICQuYWpheCh7XG4gICAgICB0eXBlOiAnUE9TVCcsXG4gICAgICBoZWFkZXJzOiB7J2NhY2hlLWNvbnRyb2wnOiAnbm8tY2FjaGUnfSxcbiAgICAgIHVybDogc2VsZi4kbW9kdWxlUG9zaXRpb25zRm9ybS5kYXRhKCd1cGRhdGUtdXJsJyksXG4gICAgICBkYXRhOiAkZGF0YSxcbiAgICAgIHN1Y2Nlc3M6ICgpID0+IHtcbiAgICAgICAgbGV0IHN0YXJ0ID0gMDtcbiAgICAgICAgJC5lYWNoKCRsaXN0LmNoaWxkcmVuKCksIGZ1bmN0aW9uKGluZGV4LCBlbGVtZW50KSB7XG4gICAgICAgICAgY29uc29sZS5sb2coJChlbGVtZW50KS5maW5kKCcuaW5kZXgtcG9zaXRpb24nKSk7XG4gICAgICAgICAgJChlbGVtZW50KS5maW5kKCcuaW5kZXgtcG9zaXRpb24nKS5odG1sKCsrc3RhcnQpO1xuICAgICAgICB9KTtcblxuICAgICAgICB3aW5kb3cuc2hvd1N1Y2Nlc3NNZXNzYWdlKHdpbmRvdy51cGRhdGVfc3VjY2Vzc19tc2cpO1xuICAgICAgfVxuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIEZpbHRlciBob29rcyAvIG1vZHVsZXMgc2VhcmNoIGFuZCBldmVyeXRoaW5nXG4gICAqIGFib3V0IGhvb2tzIHBvc2l0aW9ucy5cbiAgICovXG4gIG1vZHVsZXNQb3NpdGlvbkZpbHRlckhvb2tzKCkge1xuICAgIGNvbnN0IHNlbGYgPSB0aGlzO1xuICAgIGNvbnN0ICRob29rTmFtZSA9IHNlbGYuJGhvb2tTZWFyY2gudmFsKCk7XG4gICAgY29uc3QgJG1vZHVsZUlkID0gc2VsZi4kc2hvd01vZHVsZXMudmFsKCk7XG4gICAgY29uc3QgJHJlZ2V4ID0gbmV3IFJlZ0V4cChgKCR7JGhvb2tOYW1lfSlgLCAnZ2knKTtcblxuICAgIGZvciAobGV0ICRpZCA9IDA7ICRpZCA8IHNlbGYuJGhvb2tzTGlzdC5sZW5ndGg7ICRpZCsrKSB7XG4gICAgICBzZWxmLiRob29rc0xpc3RbJGlkXS5jb250YWluZXIudG9nZ2xlKCRob29rTmFtZSA9PT0gJycgJiYgJG1vZHVsZUlkID09PSAnYWxsJyk7XG4gICAgICBzZWxmLiRob29rc0xpc3RbJGlkXS5lbGVtZW50Lmh0bWwoc2VsZi4kaG9va3NMaXN0WyRpZF0udGl0bGUpO1xuICAgICAgc2VsZi4kaG9va3NMaXN0WyRpZF0uY29udGFpbmVyLmZpbmQoJy5tb2R1bGUtaXRlbScpLnJlbW92ZUNsYXNzKCdoaWdobGlnaHQnKTtcbiAgICB9XG5cbiAgICAvLyBIYXZlIHNlbGVjdCBhIGhvb2sgbmFtZSBvciBhIG1vZHVsZSBpZFxuICAgIGlmICgkaG9va05hbWUgIT09ICcnIHx8ICRtb2R1bGVJZCAhPT0gJ2FsbCcpIHtcbiAgICAgIC8vIFByZXBhcmUgc2V0IG9mIG1hdGNoZWQgZWxlbWVudHNcbiAgICAgIGxldCAkaG9va3NUb1Nob3dGcm9tTW9kdWxlID0gJCgpO1xuICAgICAgbGV0ICRob29rc1RvU2hvd0Zyb21Ib29rTmFtZSA9ICQoKTtcbiAgICAgIGxldCAkY3VycmVudEhvb2tzO1xuICAgICAgbGV0ICRzdGFydDtcblxuICAgICAgZm9yIChsZXQgJGlkID0gMDsgJGlkIDwgc2VsZi4kaG9va3NMaXN0Lmxlbmd0aDsgJGlkKyspIHtcbiAgICAgICAgLy8gUHJlcGFyZSBoaWdobGlnaHQgd2hlbiBvbmUgbW9kdWxlIGlzIHNlbGVjdGVkXG4gICAgICAgIGlmICgkbW9kdWxlSWQgIT09ICdhbGwnKSB7XG4gICAgICAgICAgJGN1cnJlbnRIb29rcyA9IHNlbGYuJGhvb2tzTGlzdFskaWRdLmNvbnRhaW5lci5maW5kKGAubW9kdWxlLXBvc2l0aW9uLSR7JG1vZHVsZUlkfWApO1xuICAgICAgICAgIGlmICgkY3VycmVudEhvb2tzLmxlbmd0aCA+IDApIHtcbiAgICAgICAgICAgICRob29rc1RvU2hvd0Zyb21Nb2R1bGUgPSAkaG9va3NUb1Nob3dGcm9tTW9kdWxlLmFkZChzZWxmLiRob29rc0xpc3RbJGlkXS5jb250YWluZXIpO1xuICAgICAgICAgICAgJGN1cnJlbnRIb29rcy5hZGRDbGFzcygnaGlnaGxpZ2h0Jyk7XG4gICAgICAgICAgfVxuICAgICAgICB9XG5cbiAgICAgICAgLy8gUHJlcGFyZSBoaWdobGlnaHQgd2hlbiB0aGVyZSBpcyBhIGhvb2sgbmFtZVxuICAgICAgICBpZiAoJGhvb2tOYW1lICE9PSAnJykge1xuICAgICAgICAgICRzdGFydCA9IHNlbGYuJGhvb2tzTGlzdFskaWRdLnRpdGxlLnRvTG93ZXJDYXNlKCkuc2VhcmNoKCRob29rTmFtZS50b0xvd2VyQ2FzZSgpKTtcbiAgICAgICAgICBpZiAoJHN0YXJ0ICE9PSAtMSkge1xuICAgICAgICAgICAgJGhvb2tzVG9TaG93RnJvbUhvb2tOYW1lID0gJGhvb2tzVG9TaG93RnJvbUhvb2tOYW1lLmFkZChzZWxmLiRob29rc0xpc3RbJGlkXS5jb250YWluZXIpO1xuICAgICAgICAgICAgc2VsZi4kaG9va3NMaXN0WyRpZF0uZWxlbWVudC5odG1sKFxuICAgICAgICAgICAgICBzZWxmLiRob29rc0xpc3RbJGlkXS50aXRsZS5yZXBsYWNlKFxuICAgICAgICAgICAgICAgICRyZWdleCxcbiAgICAgICAgICAgICAgICAnPHNwYW4gY2xhc3M9XCJoaWdobGlnaHRcIj4kMTwvc3Bhbj4nXG4gICAgICAgICAgICAgIClcbiAgICAgICAgICAgICk7XG4gICAgICAgICAgfVxuICAgICAgICB9XG4gICAgICB9XG5cbiAgICAgIC8vIE5vdGhpbmcgc2VsZWN0ZWRcbiAgICAgIGlmICgkbW9kdWxlSWQgPT09ICdhbGwnICYmICRob29rTmFtZSAhPT0gJycpIHtcbiAgICAgICAgJGhvb2tzVG9TaG93RnJvbUhvb2tOYW1lLnNob3coKTtcbiAgICAgIH0gZWxzZSBpZiAoJGhvb2tOYW1lID09PSAnJyAmJiAkbW9kdWxlSWQgIT09ICdhbGwnKSB7IC8vIEhhdmUgbm8gaG9vayBidWcgaGF2ZSBhIG1vZHVsZVxuICAgICAgICAkaG9va3NUb1Nob3dGcm9tTW9kdWxlLnNob3coKTtcbiAgICAgIH0gZWxzZSB7IC8vIEJvdGggc2VsZWN0ZWRcbiAgICAgICAgJGhvb2tzVG9TaG93RnJvbUhvb2tOYW1lLmZpbHRlcigkaG9va3NUb1Nob3dGcm9tTW9kdWxlKS5zaG93KCk7XG4gICAgICB9XG4gICAgfVxuXG4gICAgaWYgKCFzZWxmLiRob29rUG9zaXRpb24ucHJvcCgnY2hlY2tlZCcpKSB7XG4gICAgICBmb3IgKGxldCAkaWQgPSAwOyAkaWQgPCBzZWxmLiRob29rc0xpc3QubGVuZ3RoOyAkaWQrKykge1xuICAgICAgICBpZiAoc2VsZi4kaG9va3NMaXN0WyRpZF0uY29udGFpbmVyLmlzKCcuaG9vay1wb3NpdGlvbicpKSB7XG4gICAgICAgICAgc2VsZi4kaG9va3NMaXN0WyRpZF0uY29udGFpbmVyLmhpZGUoKTtcbiAgICAgICAgfVxuICAgICAgfVxuICAgIH1cbiAgfTtcbn1cblxuZXhwb3J0IGRlZmF1bHQgUG9zaXRpb25zTGlzdEhhbmRsZXI7XG4iXSwic291cmNlUm9vdCI6IiJ9