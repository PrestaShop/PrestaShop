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
/******/ 	return __webpack_require__(__webpack_require__.s = 330);
/******/ })
/************************************************************************/
/******/ ({

/***/ 258:
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

/***/ 330:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _positionsListHandler = __webpack_require__(258);

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
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAgNWQ5OTkwOTRkMTFhZWZmMGI1ODI/M2YxNioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKiIsIndlYnBhY2s6Ly8vLi9qcy9wYWdlcy9pbXByb3ZlL2Rlc2lnbl9wb3NpdGlvbnMvcG9zaXRpb25zLWxpc3QtaGFuZGxlci5qcyIsIndlYnBhY2s6Ly8vLi9qcy9wYWdlcy9pbXByb3ZlL2Rlc2lnbl9wb3NpdGlvbnMvaW5kZXguanMiXSwibmFtZXMiOlsiJCIsIndpbmRvdyIsIlBvc2l0aW9uc0xpc3RIYW5kbGVyIiwibGVuZ3RoIiwic2VsZiIsIiRwYW5lbFNlbGVjdGlvbiIsIiRwYW5lbFNlbGVjdGlvblNpbmdsZVNlbGVjdGlvbiIsIiRwYW5lbFNlbGVjdGlvbk11bHRpcGxlU2VsZWN0aW9uIiwiJHBhbmVsU2VsZWN0aW9uT3JpZ2luYWxZIiwib2Zmc2V0IiwidG9wIiwiJHNob3dNb2R1bGVzIiwiJG1vZHVsZXNMaXN0IiwiJGhvb2tQb3NpdGlvbiIsIiRob29rU2VhcmNoIiwiJG1vZHVsZVBvc2l0aW9uc0Zvcm0iLCIkbW9kdWxlVW5ob29rQnV0dG9uIiwiJG1vZHVsZUJ1dHRvbnNVcGRhdGUiLCJoYW5kbGVMaXN0IiwiaGFuZGxlU29ydGFibGUiLCJvbiIsImhhbmRsZSIsIiRzY3JvbGxUb3AiLCJzY3JvbGxUb3AiLCJjc3MiLCIkY2hlY2tlZENvdW50IiwiZmlsdGVyIiwiaGlkZSIsInNob3ciLCJodG1sIiwiZmluZCIsImNsaWNrIiwidHJpZ2dlciIsIiRob29rc0xpc3QiLCJlYWNoIiwiJHRoaXMiLCJwdXNoIiwicGFyZW50cyIsInNlbGVjdDIiLCJtb2R1bGVzUG9zaXRpb25GaWx0ZXJIb29rcyIsImUiLCJrZXlDb2RlIiwid2hpY2giLCJkYXRhIiwicHJvcCIsIiRidG4iLCIkY3VycmVudCIsImNsb3Nlc3QiLCIkZGVzdGluYXRpb24iLCJuZXh0IiwicHJldiIsImluc2VydEFmdGVyIiwiaW5zZXJ0QmVmb3JlIiwidXBkYXRlUG9zaXRpb25zIiwiaG9va0lkIiwibW9kdWxlSWQiLCJ3YXkiLCJwb3NpdGlvbnMiLCJzb3J0YWJsZSIsImZvcmNlUGxhY2Vob2xkZXJTaXplIiwic3RhcnQiLCJ1aSIsIml0ZW0iLCJpbmRleCIsInVwZGF0ZSIsIiRldmVudCIsImF0dHIiLCJzcGxpdCIsIiRkYXRhIiwidGFyZ2V0IiwiJGxpc3QiLCJjaGlsZHJlbiIsImVsZW1lbnQiLCJhamF4IiwidHlwZSIsImhlYWRlcnMiLCJ1cmwiLCJzdWNjZXNzIiwiY29uc29sZSIsImxvZyIsInNob3dTdWNjZXNzTWVzc2FnZSIsInVwZGF0ZV9zdWNjZXNzX21zZyIsIiRob29rTmFtZSIsInZhbCIsIiRtb2R1bGVJZCIsIiRyZWdleCIsIlJlZ0V4cCIsIiRpZCIsImNvbnRhaW5lciIsInRvZ2dsZSIsInRpdGxlIiwicmVtb3ZlQ2xhc3MiLCIkaG9va3NUb1Nob3dGcm9tTW9kdWxlIiwiJGhvb2tzVG9TaG93RnJvbUhvb2tOYW1lIiwiJGN1cnJlbnRIb29rcyIsIiRzdGFydCIsImFkZCIsImFkZENsYXNzIiwidG9Mb3dlckNhc2UiLCJzZWFyY2giLCJyZXBsYWNlIiwiaXMiXSwibWFwcGluZ3MiOiI7O0FBQUE7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7OztBQUdBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBLG1EQUEyQyxjQUFjOztBQUV6RDtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBLGFBQUs7QUFDTDtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBLG1DQUEyQiwwQkFBMEIsRUFBRTtBQUN2RCx5Q0FBaUMsZUFBZTtBQUNoRDtBQUNBO0FBQ0E7O0FBRUE7QUFDQSw4REFBc0QsK0RBQStEOztBQUVySDtBQUNBOztBQUVBO0FBQ0E7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7OztBQ2hFQTs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7OztBQXlCQSxJQUFNQSxJQUFJQyxPQUFPRCxDQUFqQjs7SUFFTUUsb0I7QUFDSixrQ0FBYztBQUFBOztBQUNaLFFBQUlGLEVBQUUsbUJBQUYsRUFBdUJHLE1BQXZCLEtBQWtDLENBQXRDLEVBQXlDO0FBQ3ZDO0FBQ0Q7O0FBRUQsUUFBTUMsT0FBTyxJQUFiO0FBQ0FBLFNBQUtDLGVBQUwsR0FBdUJMLEVBQUUsbUNBQUYsQ0FBdkI7QUFDQUksU0FBS0UsOEJBQUwsR0FBc0NOLEVBQUUsb0NBQUYsQ0FBdEM7QUFDQUksU0FBS0csZ0NBQUwsR0FBd0NQLEVBQUUsc0NBQUYsQ0FBeEM7O0FBRUFJLFNBQUtJLHdCQUFMLEdBQWdDSixLQUFLQyxlQUFMLENBQXFCSSxNQUFyQixHQUE4QkMsR0FBOUQ7QUFDQU4sU0FBS08sWUFBTCxHQUFvQlgsRUFBRSxlQUFGLENBQXBCO0FBQ0FJLFNBQUtRLFlBQUwsR0FBb0JaLEVBQUUsNEJBQUYsQ0FBcEI7QUFDQUksU0FBS1MsYUFBTCxHQUFxQmIsRUFBRSxnQkFBRixDQUFyQjtBQUNBSSxTQUFLVSxXQUFMLEdBQW1CZCxFQUFFLGNBQUYsQ0FBbkI7QUFDQUksU0FBS1csb0JBQUwsR0FBNEJmLEVBQUUsd0JBQUYsQ0FBNUI7QUFDQUksU0FBS1ksbUJBQUwsR0FBMkJoQixFQUFFLGdDQUFGLENBQTNCO0FBQ0FJLFNBQUthLG9CQUFMLEdBQTRCakIsRUFBRSw2QkFBRixDQUE1Qjs7QUFFQUksU0FBS2MsVUFBTDtBQUNBZCxTQUFLZSxjQUFMOztBQUVBbkIsTUFBRSx5Q0FBRixFQUE2Q29CLEVBQTdDLENBQWdELFFBQWhELEVBQTBEO0FBQUEsYUFBTWhCLEtBQUtpQixNQUFMLEVBQU47QUFBQSxLQUExRDtBQUNEOztBQUVEOzs7Ozs7O2lDQUdhO0FBQ1gsVUFBTWpCLE9BQU8sSUFBYjs7QUFFQUosUUFBRUMsTUFBRixFQUFVbUIsRUFBVixDQUFhLFFBQWIsRUFBdUIsWUFBTTtBQUMzQixZQUFNRSxhQUFhdEIsRUFBRUMsTUFBRixFQUFVc0IsU0FBVixFQUFuQjtBQUNBbkIsYUFBS0MsZUFBTCxDQUFxQm1CLEdBQXJCLENBQ0UsS0FERixFQUVFRixhQUFhLEVBQWIsR0FBa0IsQ0FBbEIsR0FBc0JBLGFBQWFsQixLQUFLSSx3QkFGMUM7QUFJRCxPQU5EOztBQVFBSixXQUFLUSxZQUFMLENBQWtCUSxFQUFsQixDQUFxQixRQUFyQixFQUErQixZQUFZO0FBQ3pDLFlBQU1LLGdCQUFnQnJCLEtBQUtRLFlBQUwsQ0FBa0JjLE1BQWxCLENBQXlCLFVBQXpCLEVBQXFDdkIsTUFBM0Q7O0FBRUEsWUFBSXNCLGtCQUFrQixDQUF0QixFQUF5QjtBQUN2QnJCLGVBQUtZLG1CQUFMLENBQXlCVyxJQUF6QjtBQUNBdkIsZUFBS0MsZUFBTCxDQUFxQnNCLElBQXJCO0FBQ0F2QixlQUFLRSw4QkFBTCxDQUFvQ3FCLElBQXBDO0FBQ0F2QixlQUFLRyxnQ0FBTCxDQUFzQ29CLElBQXRDO0FBQ0QsU0FMRCxNQUtPLElBQUlGLGtCQUFrQixDQUF0QixFQUF5QjtBQUM5QnJCLGVBQUtZLG1CQUFMLENBQXlCWSxJQUF6QjtBQUNBeEIsZUFBS0MsZUFBTCxDQUFxQnVCLElBQXJCO0FBQ0F4QixlQUFLRSw4QkFBTCxDQUFvQ3NCLElBQXBDO0FBQ0F4QixlQUFLRyxnQ0FBTCxDQUFzQ29CLElBQXRDO0FBQ0QsU0FMTSxNQUtBO0FBQ0x2QixlQUFLWSxtQkFBTCxDQUF5QlksSUFBekI7QUFDQXhCLGVBQUtDLGVBQUwsQ0FBcUJ1QixJQUFyQjtBQUNBeEIsZUFBS0UsOEJBQUwsQ0FBb0NxQixJQUFwQztBQUNBdkIsZUFBS0csZ0NBQUwsQ0FBc0NxQixJQUF0QztBQUNBNUIsWUFBRSxtQ0FBRixFQUF1QzZCLElBQXZDLENBQTRDSixhQUE1QztBQUNEO0FBQ0YsT0FwQkQ7O0FBc0JBckIsV0FBS0MsZUFBTCxDQUFxQnlCLElBQXJCLENBQTBCLFFBQTFCLEVBQW9DQyxLQUFwQyxDQUEwQyxZQUFNO0FBQzlDL0IsVUFBRSwyQkFBRixFQUErQmdDLE9BQS9CLENBQXVDLE9BQXZDO0FBQ0QsT0FGRDs7QUFJQTVCLFdBQUs2QixVQUFMLEdBQWtCLEVBQWxCO0FBQ0FqQyxRQUFFLCtCQUFGLEVBQW1Da0MsSUFBbkMsQ0FBd0MsWUFBWTtBQUNsRCxZQUFNQyxRQUFRbkMsRUFBRSxJQUFGLENBQWQ7QUFDQUksYUFBSzZCLFVBQUwsQ0FBZ0JHLElBQWhCLENBQXFCO0FBQ25CLG1CQUFTRCxNQUFNTixJQUFOLEVBRFU7QUFFbkIscUJBQVdNLEtBRlE7QUFHbkIsdUJBQWFBLE1BQU1FLE9BQU4sQ0FBYyxhQUFkO0FBSE0sU0FBckI7QUFLRCxPQVBEOztBQVNBakMsV0FBS08sWUFBTCxDQUFrQjJCLE9BQWxCO0FBQ0FsQyxXQUFLTyxZQUFMLENBQWtCUyxFQUFsQixDQUFxQixRQUFyQixFQUErQixZQUFNO0FBQ25DaEIsYUFBS21DLDBCQUFMO0FBQ0QsT0FGRDs7QUFJQW5DLFdBQUtTLGFBQUwsQ0FBbUJPLEVBQW5CLENBQXNCLFFBQXRCLEVBQWdDLFlBQU07QUFDcENoQixhQUFLbUMsMEJBQUw7QUFDRCxPQUZEOztBQUlBbkMsV0FBS1UsV0FBTCxDQUFpQk0sRUFBakIsQ0FBb0IsT0FBcEIsRUFBNkIsWUFBTTtBQUNqQ2hCLGFBQUttQywwQkFBTDtBQUNELE9BRkQ7O0FBSUFuQyxXQUFLVSxXQUFMLENBQWlCTSxFQUFqQixDQUFvQixVQUFwQixFQUFnQyxVQUFDb0IsQ0FBRCxFQUFPO0FBQ3JDLFlBQU1DLFVBQVVELEVBQUVDLE9BQUYsSUFBYUQsRUFBRUUsS0FBL0I7QUFDQSxlQUFPRCxZQUFZLEVBQW5CO0FBQ0QsT0FIRDs7QUFLQXpDLFFBQUUsZUFBRixFQUFtQm9CLEVBQW5CLENBQXNCLE9BQXRCLEVBQStCLFlBQVc7QUFDeENwQixvQkFBVUEsRUFBRSxJQUFGLEVBQVEyQyxJQUFSLENBQWEsU0FBYixDQUFWLEVBQXFDQyxJQUFyQyxDQUEwQyxTQUExQyxFQUFxRDVDLEVBQUUsSUFBRixFQUFRNEMsSUFBUixDQUFhLFNBQWIsQ0FBckQ7QUFDRCxPQUZEOztBQUlBeEMsV0FBS1EsWUFBTCxDQUFrQlEsRUFBbEIsQ0FBcUIsT0FBckIsRUFBOEIsWUFBVztBQUN2Q3BCLHFCQUFXQSxFQUFFLElBQUYsRUFBUTJDLElBQVIsQ0FBYSxTQUFiLENBQVgsRUFBc0NDLElBQXRDLENBQ0UsU0FERixFQUVFNUMsWUFBVUEsRUFBRSxJQUFGLEVBQVEyQyxJQUFSLENBQWEsU0FBYixDQUFWLHFCQUFtRHhDLE1BQW5ELEtBQThELENBRmhFO0FBSUQsT0FMRDs7QUFPQUMsV0FBS2Esb0JBQUwsQ0FBMEJHLEVBQTFCLENBQTZCLE9BQTdCLEVBQXNDLFlBQVc7QUFDL0MsWUFBTXlCLE9BQU83QyxFQUFFLElBQUYsQ0FBYjtBQUNBLFlBQU04QyxXQUFXRCxLQUFLRSxPQUFMLENBQWEsY0FBYixDQUFqQjtBQUNBLFlBQUlDLHFCQUFKOztBQUVBLFlBQUlILEtBQUtGLElBQUwsQ0FBVSxLQUFWLENBQUosRUFBc0I7QUFDcEJLLHlCQUFlRixTQUFTRyxJQUFULENBQWMsY0FBZCxDQUFmO0FBQ0QsU0FGRCxNQUVPO0FBQ0xELHlCQUFlRixTQUFTSSxJQUFULENBQWMsY0FBZCxDQUFmO0FBQ0Q7O0FBRUQsWUFBSUYsYUFBYTdDLE1BQWIsS0FBd0IsQ0FBNUIsRUFBK0I7QUFDN0IsaUJBQU8sS0FBUDtBQUNEOztBQUVELFlBQUkwQyxLQUFLRixJQUFMLENBQVUsS0FBVixDQUFKLEVBQXNCO0FBQ3BCRyxtQkFBU0ssV0FBVCxDQUFxQkgsWUFBckI7QUFDRCxTQUZELE1BRU87QUFDTEYsbUJBQVNNLFlBQVQsQ0FBc0JKLFlBQXRCO0FBQ0Q7O0FBRUQ1QyxhQUFLaUQsZUFBTCxDQUNFO0FBQ0VDLGtCQUFRVCxLQUFLRixJQUFMLENBQVUsU0FBVixDQURWO0FBRUVZLG9CQUFVVixLQUFLRixJQUFMLENBQVUsV0FBVixDQUZaO0FBR0VhLGVBQUtYLEtBQUtGLElBQUwsQ0FBVSxLQUFWLENBSFA7QUFJRWMscUJBQVc7QUFKYixTQURGLEVBT0VaLEtBQUtFLE9BQUwsQ0FBYSxJQUFiLENBUEY7O0FBVUEsZUFBTyxLQUFQO0FBQ0QsT0FoQ0Q7QUFpQ0Q7O0FBRUQ7Ozs7OztxQ0FHaUI7QUFDZixVQUFNM0MsT0FBTyxJQUFiOztBQUVBSixRQUFFLFdBQUYsRUFBZTBELFFBQWYsQ0FBd0I7QUFDdEJDLDhCQUFzQixJQURBO0FBRXRCQyxlQUFPLGVBQVNwQixDQUFULEVBQVlxQixFQUFaLEVBQWdCO0FBQ3JCN0QsWUFBRSxJQUFGLEVBQVEyQyxJQUFSLENBQWEsZ0JBQWIsRUFBK0JrQixHQUFHQyxJQUFILENBQVFDLEtBQVIsRUFBL0I7QUFDRCxTQUpxQjtBQUt0QkMsZ0JBQVEsZ0JBQVNDLE1BQVQsRUFBaUJKLEVBQWpCLEVBQXFCO0FBQUEsb0NBQ0VBLEdBQUdDLElBQUgsQ0FBUUksSUFBUixDQUFhLElBQWIsRUFBbUJDLEtBQW5CLENBQXlCLEdBQXpCLENBREY7QUFBQTtBQUFBLGNBQ25CYixNQURtQjtBQUFBLGNBQ1hDLFFBRFc7O0FBRzNCLGNBQU1hLFFBQVE7QUFDWmQsMEJBRFk7QUFFWkMsOEJBRlk7QUFHWkMsaUJBQU14RCxFQUFFLElBQUYsRUFBUTJDLElBQVIsQ0FBYSxnQkFBYixJQUFpQ2tCLEdBQUdDLElBQUgsQ0FBUUMsS0FBUixFQUFsQyxHQUFxRCxDQUFyRCxHQUF5RCxDQUhsRDtBQUlaTix1QkFBVztBQUpDLFdBQWQ7O0FBT0FyRCxlQUFLaUQsZUFBTCxDQUNFZSxLQURGLEVBRUVwRSxFQUFFaUUsT0FBT0ksTUFBVCxDQUZGO0FBSUQ7QUFuQnFCLE9BQXhCO0FBcUJEOzs7b0NBRWVELEssRUFBT0UsSyxFQUFPO0FBQzVCLFVBQU1sRSxPQUFPLElBQWI7QUFDQUosUUFBRWtDLElBQUYsQ0FBT29DLE1BQU1DLFFBQU4sRUFBUCxFQUF5QixVQUFTUixLQUFULEVBQWdCUyxPQUFoQixFQUF5QjtBQUNoREosY0FBTVgsU0FBTixDQUFnQnJCLElBQWhCLENBQXFCcEMsRUFBRXdFLE9BQUYsRUFBV04sSUFBWCxDQUFnQixJQUFoQixDQUFyQjtBQUNELE9BRkQ7O0FBSUFsRSxRQUFFeUUsSUFBRixDQUFPO0FBQ0xDLGNBQU0sTUFERDtBQUVMQyxpQkFBUyxFQUFDLGlCQUFpQixVQUFsQixFQUZKO0FBR0xDLGFBQUt4RSxLQUFLVyxvQkFBTCxDQUEwQjRCLElBQTFCLENBQStCLFlBQS9CLENBSEE7QUFJTEEsY0FBTXlCLEtBSkQ7QUFLTFMsaUJBQVMsbUJBQU07QUFDYixjQUFJakIsUUFBUSxDQUFaO0FBQ0E1RCxZQUFFa0MsSUFBRixDQUFPb0MsTUFBTUMsUUFBTixFQUFQLEVBQXlCLFVBQVNSLEtBQVQsRUFBZ0JTLE9BQWhCLEVBQXlCO0FBQ2hETSxvQkFBUUMsR0FBUixDQUFZL0UsRUFBRXdFLE9BQUYsRUFBVzFDLElBQVgsQ0FBZ0IsaUJBQWhCLENBQVo7QUFDQTlCLGNBQUV3RSxPQUFGLEVBQVcxQyxJQUFYLENBQWdCLGlCQUFoQixFQUFtQ0QsSUFBbkMsQ0FBd0MsRUFBRStCLEtBQTFDO0FBQ0QsV0FIRDs7QUFLQTNELGlCQUFPK0Usa0JBQVAsQ0FBMEIvRSxPQUFPZ0Ysa0JBQWpDO0FBQ0Q7QUFiSSxPQUFQO0FBZUQ7O0FBRUQ7Ozs7Ozs7aURBSTZCO0FBQzNCLFVBQU03RSxPQUFPLElBQWI7QUFDQSxVQUFNOEUsWUFBWTlFLEtBQUtVLFdBQUwsQ0FBaUJxRSxHQUFqQixFQUFsQjtBQUNBLFVBQU1DLFlBQVloRixLQUFLTyxZQUFMLENBQWtCd0UsR0FBbEIsRUFBbEI7QUFDQSxVQUFNRSxTQUFTLElBQUlDLE1BQUosT0FBZUosU0FBZixRQUE2QixJQUE3QixDQUFmOztBQUVBLFdBQUssSUFBSUssTUFBTSxDQUFmLEVBQWtCQSxNQUFNbkYsS0FBSzZCLFVBQUwsQ0FBZ0I5QixNQUF4QyxFQUFnRG9GLEtBQWhELEVBQXVEO0FBQ3JEbkYsYUFBSzZCLFVBQUwsQ0FBZ0JzRCxHQUFoQixFQUFxQkMsU0FBckIsQ0FBK0JDLE1BQS9CLENBQXNDUCxjQUFjLEVBQWQsSUFBb0JFLGNBQWMsS0FBeEU7QUFDQWhGLGFBQUs2QixVQUFMLENBQWdCc0QsR0FBaEIsRUFBcUJmLE9BQXJCLENBQTZCM0MsSUFBN0IsQ0FBa0N6QixLQUFLNkIsVUFBTCxDQUFnQnNELEdBQWhCLEVBQXFCRyxLQUF2RDtBQUNBdEYsYUFBSzZCLFVBQUwsQ0FBZ0JzRCxHQUFoQixFQUFxQkMsU0FBckIsQ0FBK0IxRCxJQUEvQixDQUFvQyxjQUFwQyxFQUFvRDZELFdBQXBELENBQWdFLFdBQWhFO0FBQ0Q7O0FBRUQ7QUFDQSxVQUFJVCxjQUFjLEVBQWQsSUFBb0JFLGNBQWMsS0FBdEMsRUFBNkM7QUFDM0M7QUFDQSxZQUFJUSx5QkFBeUI1RixHQUE3QjtBQUNBLFlBQUk2RiwyQkFBMkI3RixHQUEvQjtBQUNBLFlBQUk4RixzQkFBSjtBQUNBLFlBQUlDLGVBQUo7O0FBRUEsYUFBSyxJQUFJUixPQUFNLENBQWYsRUFBa0JBLE9BQU1uRixLQUFLNkIsVUFBTCxDQUFnQjlCLE1BQXhDLEVBQWdEb0YsTUFBaEQsRUFBdUQ7QUFDckQ7QUFDQSxjQUFJSCxjQUFjLEtBQWxCLEVBQXlCO0FBQ3ZCVSw0QkFBZ0IxRixLQUFLNkIsVUFBTCxDQUFnQnNELElBQWhCLEVBQXFCQyxTQUFyQixDQUErQjFELElBQS9CLHVCQUF3RHNELFNBQXhELENBQWhCO0FBQ0EsZ0JBQUlVLGNBQWMzRixNQUFkLEdBQXVCLENBQTNCLEVBQThCO0FBQzVCeUYsdUNBQXlCQSx1QkFBdUJJLEdBQXZCLENBQTJCNUYsS0FBSzZCLFVBQUwsQ0FBZ0JzRCxJQUFoQixFQUFxQkMsU0FBaEQsQ0FBekI7QUFDQU0sNEJBQWNHLFFBQWQsQ0FBdUIsV0FBdkI7QUFDRDtBQUNGOztBQUVEO0FBQ0EsY0FBSWYsY0FBYyxFQUFsQixFQUFzQjtBQUNwQmEscUJBQVMzRixLQUFLNkIsVUFBTCxDQUFnQnNELElBQWhCLEVBQXFCRyxLQUFyQixDQUEyQlEsV0FBM0IsR0FBeUNDLE1BQXpDLENBQWdEakIsVUFBVWdCLFdBQVYsRUFBaEQsQ0FBVDtBQUNBLGdCQUFJSCxXQUFXLENBQUMsQ0FBaEIsRUFBbUI7QUFDakJGLHlDQUEyQkEseUJBQXlCRyxHQUF6QixDQUE2QjVGLEtBQUs2QixVQUFMLENBQWdCc0QsSUFBaEIsRUFBcUJDLFNBQWxELENBQTNCO0FBQ0FwRixtQkFBSzZCLFVBQUwsQ0FBZ0JzRCxJQUFoQixFQUFxQmYsT0FBckIsQ0FBNkIzQyxJQUE3QixDQUNFekIsS0FBSzZCLFVBQUwsQ0FBZ0JzRCxJQUFoQixFQUFxQkcsS0FBckIsQ0FBMkJVLE9BQTNCLENBQ0VmLE1BREYsRUFFRSxtQ0FGRixDQURGO0FBTUQ7QUFDRjtBQUNGOztBQUVEO0FBQ0EsWUFBSUQsY0FBYyxLQUFkLElBQXVCRixjQUFjLEVBQXpDLEVBQTZDO0FBQzNDVyxtQ0FBeUJqRSxJQUF6QjtBQUNELFNBRkQsTUFFTyxJQUFJc0QsY0FBYyxFQUFkLElBQW9CRSxjQUFjLEtBQXRDLEVBQTZDO0FBQUU7QUFDcERRLGlDQUF1QmhFLElBQXZCO0FBQ0QsU0FGTSxNQUVBO0FBQUU7QUFDUGlFLG1DQUF5Qm5FLE1BQXpCLENBQWdDa0Usc0JBQWhDLEVBQXdEaEUsSUFBeEQ7QUFDRDtBQUNGOztBQUVELFVBQUksQ0FBQ3hCLEtBQUtTLGFBQUwsQ0FBbUIrQixJQUFuQixDQUF3QixTQUF4QixDQUFMLEVBQXlDO0FBQ3ZDLGFBQUssSUFBSTJDLFFBQU0sQ0FBZixFQUFrQkEsUUFBTW5GLEtBQUs2QixVQUFMLENBQWdCOUIsTUFBeEMsRUFBZ0RvRixPQUFoRCxFQUF1RDtBQUNyRCxjQUFJbkYsS0FBSzZCLFVBQUwsQ0FBZ0JzRCxLQUFoQixFQUFxQkMsU0FBckIsQ0FBK0JhLEVBQS9CLENBQWtDLGdCQUFsQyxDQUFKLEVBQXlEO0FBQ3ZEakcsaUJBQUs2QixVQUFMLENBQWdCc0QsS0FBaEIsRUFBcUJDLFNBQXJCLENBQStCN0QsSUFBL0I7QUFDRDtBQUNGO0FBQ0Y7QUFDRjs7Ozs7O2tCQUdZekIsb0I7Ozs7Ozs7Ozs7QUN2UWY7Ozs7OztBQUVBLElBQU1GLElBQUlDLE9BQU9ELENBQWpCLEMsQ0EzQkE7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7QUE2QkFBLEVBQUUsWUFBTTtBQUNOLE1BQUlFLDhCQUFKO0FBQ0QsQ0FGRCxFIiwiZmlsZSI6ImltcHJvdmVfZGVzaWduX3Bvc2l0aW9ucy5idW5kbGUuanMiLCJzb3VyY2VzQ29udGVudCI6WyIgXHQvLyBUaGUgbW9kdWxlIGNhY2hlXG4gXHR2YXIgaW5zdGFsbGVkTW9kdWxlcyA9IHt9O1xuXG4gXHQvLyBUaGUgcmVxdWlyZSBmdW5jdGlvblxuIFx0ZnVuY3Rpb24gX193ZWJwYWNrX3JlcXVpcmVfXyhtb2R1bGVJZCkge1xuXG4gXHRcdC8vIENoZWNrIGlmIG1vZHVsZSBpcyBpbiBjYWNoZVxuIFx0XHRpZihpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXSkge1xuIFx0XHRcdHJldHVybiBpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXS5leHBvcnRzO1xuIFx0XHR9XG4gXHRcdC8vIENyZWF0ZSBhIG5ldyBtb2R1bGUgKGFuZCBwdXQgaXQgaW50byB0aGUgY2FjaGUpXG4gXHRcdHZhciBtb2R1bGUgPSBpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXSA9IHtcbiBcdFx0XHRpOiBtb2R1bGVJZCxcbiBcdFx0XHRsOiBmYWxzZSxcbiBcdFx0XHRleHBvcnRzOiB7fVxuIFx0XHR9O1xuXG4gXHRcdC8vIEV4ZWN1dGUgdGhlIG1vZHVsZSBmdW5jdGlvblxuIFx0XHRtb2R1bGVzW21vZHVsZUlkXS5jYWxsKG1vZHVsZS5leHBvcnRzLCBtb2R1bGUsIG1vZHVsZS5leHBvcnRzLCBfX3dlYnBhY2tfcmVxdWlyZV9fKTtcblxuIFx0XHQvLyBGbGFnIHRoZSBtb2R1bGUgYXMgbG9hZGVkXG4gXHRcdG1vZHVsZS5sID0gdHJ1ZTtcblxuIFx0XHQvLyBSZXR1cm4gdGhlIGV4cG9ydHMgb2YgdGhlIG1vZHVsZVxuIFx0XHRyZXR1cm4gbW9kdWxlLmV4cG9ydHM7XG4gXHR9XG5cblxuIFx0Ly8gZXhwb3NlIHRoZSBtb2R1bGVzIG9iamVjdCAoX193ZWJwYWNrX21vZHVsZXNfXylcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubSA9IG1vZHVsZXM7XG5cbiBcdC8vIGV4cG9zZSB0aGUgbW9kdWxlIGNhY2hlXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmMgPSBpbnN0YWxsZWRNb2R1bGVzO1xuXG4gXHQvLyBpZGVudGl0eSBmdW5jdGlvbiBmb3IgY2FsbGluZyBoYXJtb255IGltcG9ydHMgd2l0aCB0aGUgY29ycmVjdCBjb250ZXh0XG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmkgPSBmdW5jdGlvbih2YWx1ZSkgeyByZXR1cm4gdmFsdWU7IH07XG5cbiBcdC8vIGRlZmluZSBnZXR0ZXIgZnVuY3Rpb24gZm9yIGhhcm1vbnkgZXhwb3J0c1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5kID0gZnVuY3Rpb24oZXhwb3J0cywgbmFtZSwgZ2V0dGVyKSB7XG4gXHRcdGlmKCFfX3dlYnBhY2tfcmVxdWlyZV9fLm8oZXhwb3J0cywgbmFtZSkpIHtcbiBcdFx0XHRPYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgbmFtZSwge1xuIFx0XHRcdFx0Y29uZmlndXJhYmxlOiBmYWxzZSxcbiBcdFx0XHRcdGVudW1lcmFibGU6IHRydWUsXG4gXHRcdFx0XHRnZXQ6IGdldHRlclxuIFx0XHRcdH0pO1xuIFx0XHR9XG4gXHR9O1xuXG4gXHQvLyBnZXREZWZhdWx0RXhwb3J0IGZ1bmN0aW9uIGZvciBjb21wYXRpYmlsaXR5IHdpdGggbm9uLWhhcm1vbnkgbW9kdWxlc1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5uID0gZnVuY3Rpb24obW9kdWxlKSB7XG4gXHRcdHZhciBnZXR0ZXIgPSBtb2R1bGUgJiYgbW9kdWxlLl9fZXNNb2R1bGUgP1xuIFx0XHRcdGZ1bmN0aW9uIGdldERlZmF1bHQoKSB7IHJldHVybiBtb2R1bGVbJ2RlZmF1bHQnXTsgfSA6XG4gXHRcdFx0ZnVuY3Rpb24gZ2V0TW9kdWxlRXhwb3J0cygpIHsgcmV0dXJuIG1vZHVsZTsgfTtcbiBcdFx0X193ZWJwYWNrX3JlcXVpcmVfXy5kKGdldHRlciwgJ2EnLCBnZXR0ZXIpO1xuIFx0XHRyZXR1cm4gZ2V0dGVyO1xuIFx0fTtcblxuIFx0Ly8gT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eS5jYWxsXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm8gPSBmdW5jdGlvbihvYmplY3QsIHByb3BlcnR5KSB7IHJldHVybiBPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGwob2JqZWN0LCBwcm9wZXJ0eSk7IH07XG5cbiBcdC8vIF9fd2VicGFja19wdWJsaWNfcGF0aF9fXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLnAgPSBcIlwiO1xuXG4gXHQvLyBMb2FkIGVudHJ5IG1vZHVsZSBhbmQgcmV0dXJuIGV4cG9ydHNcbiBcdHJldHVybiBfX3dlYnBhY2tfcmVxdWlyZV9fKF9fd2VicGFja19yZXF1aXJlX18ucyA9IDMzMCk7XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gd2VicGFjay9ib290c3RyYXAgNWQ5OTkwOTRkMTFhZWZmMGI1ODIiLCIvKipcbiAqIDIwMDctMjAxOSBQcmVzdGFTaG9wIGFuZCBDb250cmlidXRvcnNcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UudHh0LlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vd3d3LnByZXN0YXNob3AuY29tIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqL1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbmNsYXNzIFBvc2l0aW9uc0xpc3RIYW5kbGVyIHtcbiAgY29uc3RydWN0b3IoKSB7XG4gICAgaWYgKCQoXCIjcG9zaXRpb24tZmlsdGVyc1wiKS5sZW5ndGggPT09IDApIHtcbiAgICAgIHJldHVybjtcbiAgICB9XG5cbiAgICBjb25zdCBzZWxmID0gdGhpcztcbiAgICBzZWxmLiRwYW5lbFNlbGVjdGlvbiA9ICQoXCIjbW9kdWxlcy1wb3NpdGlvbi1zZWxlY3Rpb24tcGFuZWxcIik7XG4gICAgc2VsZi4kcGFuZWxTZWxlY3Rpb25TaW5nbGVTZWxlY3Rpb24gPSAkKFwiI21vZHVsZXMtcG9zaXRpb24tc2luZ2xlLXNlbGVjdGlvblwiKTtcbiAgICBzZWxmLiRwYW5lbFNlbGVjdGlvbk11bHRpcGxlU2VsZWN0aW9uID0gJChcIiNtb2R1bGVzLXBvc2l0aW9uLW11bHRpcGxlLXNlbGVjdGlvblwiKTtcblxuICAgIHNlbGYuJHBhbmVsU2VsZWN0aW9uT3JpZ2luYWxZID0gc2VsZi4kcGFuZWxTZWxlY3Rpb24ub2Zmc2V0KCkudG9wO1xuICAgIHNlbGYuJHNob3dNb2R1bGVzID0gJChcIiNzaG93LW1vZHVsZXNcIik7XG4gICAgc2VsZi4kbW9kdWxlc0xpc3QgPSAkKCcubW9kdWxlcy1wb3NpdGlvbi1jaGVja2JveCcpO1xuICAgIHNlbGYuJGhvb2tQb3NpdGlvbiA9ICQoXCIjaG9vay1wb3NpdGlvblwiKTtcbiAgICBzZWxmLiRob29rU2VhcmNoID0gJChcIiNob29rLXNlYXJjaFwiKTtcbiAgICBzZWxmLiRtb2R1bGVQb3NpdGlvbnNGb3JtID0gJCgnI21vZHVsZS1wb3NpdGlvbnMtZm9ybScpO1xuICAgIHNlbGYuJG1vZHVsZVVuaG9va0J1dHRvbiA9ICQoJyN1bmhvb2stYnV0dG9uLXBvc2l0aW9uLWJvdHRvbScpO1xuICAgIHNlbGYuJG1vZHVsZUJ1dHRvbnNVcGRhdGUgPSAkKCcubW9kdWxlLWJ1dHRvbnMtdXBkYXRlIC5idG4nKTtcblxuICAgIHNlbGYuaGFuZGxlTGlzdCgpO1xuICAgIHNlbGYuaGFuZGxlU29ydGFibGUoKTtcblxuICAgICQoJ2lucHV0W25hbWU9XCJmb3JtW2dlbmVyYWxdW2VuYWJsZV90b3NdXCJdJykub24oJ2NoYW5nZScsICgpID0+IHNlbGYuaGFuZGxlKCkpO1xuICB9XG5cbiAgLyoqXG4gICAqIEhhbmRsZSBhbGwgZXZlbnRzIGZvciBEZXNpZ24gLT4gUG9zaXRpb25zIExpc3RcbiAgICovXG4gIGhhbmRsZUxpc3QoKSB7XG4gICAgY29uc3Qgc2VsZiA9IHRoaXM7XG5cbiAgICAkKHdpbmRvdykub24oJ3Njcm9sbCcsICgpID0+IHtcbiAgICAgIGNvbnN0ICRzY3JvbGxUb3AgPSAkKHdpbmRvdykuc2Nyb2xsVG9wKCk7XG4gICAgICBzZWxmLiRwYW5lbFNlbGVjdGlvbi5jc3MoXG4gICAgICAgICd0b3AnLFxuICAgICAgICAkc2Nyb2xsVG9wIDwgMjAgPyAwIDogJHNjcm9sbFRvcCAtIHNlbGYuJHBhbmVsU2VsZWN0aW9uT3JpZ2luYWxZXG4gICAgICApO1xuICAgIH0pO1xuXG4gICAgc2VsZi4kbW9kdWxlc0xpc3Qub24oJ2NoYW5nZScsIGZ1bmN0aW9uICgpIHtcbiAgICAgIGNvbnN0ICRjaGVja2VkQ291bnQgPSBzZWxmLiRtb2R1bGVzTGlzdC5maWx0ZXIoJzpjaGVja2VkJykubGVuZ3RoO1xuXG4gICAgICBpZiAoJGNoZWNrZWRDb3VudCA9PT0gMCkge1xuICAgICAgICBzZWxmLiRtb2R1bGVVbmhvb2tCdXR0b24uaGlkZSgpO1xuICAgICAgICBzZWxmLiRwYW5lbFNlbGVjdGlvbi5oaWRlKCk7XG4gICAgICAgIHNlbGYuJHBhbmVsU2VsZWN0aW9uU2luZ2xlU2VsZWN0aW9uLmhpZGUoKTtcbiAgICAgICAgc2VsZi4kcGFuZWxTZWxlY3Rpb25NdWx0aXBsZVNlbGVjdGlvbi5oaWRlKCk7XG4gICAgICB9IGVsc2UgaWYgKCRjaGVja2VkQ291bnQgPT09IDEpIHtcbiAgICAgICAgc2VsZi4kbW9kdWxlVW5ob29rQnV0dG9uLnNob3coKTtcbiAgICAgICAgc2VsZi4kcGFuZWxTZWxlY3Rpb24uc2hvdygpO1xuICAgICAgICBzZWxmLiRwYW5lbFNlbGVjdGlvblNpbmdsZVNlbGVjdGlvbi5zaG93KCk7XG4gICAgICAgIHNlbGYuJHBhbmVsU2VsZWN0aW9uTXVsdGlwbGVTZWxlY3Rpb24uaGlkZSgpO1xuICAgICAgfSBlbHNlIHtcbiAgICAgICAgc2VsZi4kbW9kdWxlVW5ob29rQnV0dG9uLnNob3coKTtcbiAgICAgICAgc2VsZi4kcGFuZWxTZWxlY3Rpb24uc2hvdygpO1xuICAgICAgICBzZWxmLiRwYW5lbFNlbGVjdGlvblNpbmdsZVNlbGVjdGlvbi5oaWRlKCk7XG4gICAgICAgIHNlbGYuJHBhbmVsU2VsZWN0aW9uTXVsdGlwbGVTZWxlY3Rpb24uc2hvdygpO1xuICAgICAgICAkKCcjbW9kdWxlcy1wb3NpdGlvbi1zZWxlY3Rpb24tY291bnQnKS5odG1sKCRjaGVja2VkQ291bnQpO1xuICAgICAgfVxuICAgIH0pO1xuXG4gICAgc2VsZi4kcGFuZWxTZWxlY3Rpb24uZmluZCgnYnV0dG9uJykuY2xpY2soKCkgPT4ge1xuICAgICAgJCgnYnV0dG9uW25hbWU9XCJ1bmhvb2tmb3JtXCJdJykudHJpZ2dlcignY2xpY2snKTtcbiAgICB9KTtcblxuICAgIHNlbGYuJGhvb2tzTGlzdCA9IFtdO1xuICAgICQoJ3NlY3Rpb24uaG9vay1wYW5lbCAuaG9vay1uYW1lJykuZWFjaChmdW5jdGlvbiAoKSB7XG4gICAgICBjb25zdCAkdGhpcyA9ICQodGhpcyk7XG4gICAgICBzZWxmLiRob29rc0xpc3QucHVzaCh7XG4gICAgICAgICd0aXRsZSc6ICR0aGlzLmh0bWwoKSxcbiAgICAgICAgJ2VsZW1lbnQnOiAkdGhpcyxcbiAgICAgICAgJ2NvbnRhaW5lcic6ICR0aGlzLnBhcmVudHMoJy5ob29rLXBhbmVsJylcbiAgICAgIH0pO1xuICAgIH0pO1xuXG4gICAgc2VsZi4kc2hvd01vZHVsZXMuc2VsZWN0MigpO1xuICAgIHNlbGYuJHNob3dNb2R1bGVzLm9uKCdjaGFuZ2UnLCAoKSA9PiB7XG4gICAgICBzZWxmLm1vZHVsZXNQb3NpdGlvbkZpbHRlckhvb2tzKCk7XG4gICAgfSk7XG5cbiAgICBzZWxmLiRob29rUG9zaXRpb24ub24oJ2NoYW5nZScsICgpID0+IHtcbiAgICAgIHNlbGYubW9kdWxlc1Bvc2l0aW9uRmlsdGVySG9va3MoKTtcbiAgICB9KTtcblxuICAgIHNlbGYuJGhvb2tTZWFyY2gub24oJ2lucHV0JywgKCkgPT4ge1xuICAgICAgc2VsZi5tb2R1bGVzUG9zaXRpb25GaWx0ZXJIb29rcygpO1xuICAgIH0pO1xuXG4gICAgc2VsZi4kaG9va1NlYXJjaC5vbigna2V5cHJlc3MnLCAoZSkgPT4ge1xuICAgICAgY29uc3Qga2V5Q29kZSA9IGUua2V5Q29kZSB8fCBlLndoaWNoO1xuICAgICAgcmV0dXJuIGtleUNvZGUgIT09IDEzO1xuICAgIH0pO1xuXG4gICAgJCgnLmhvb2stY2hlY2tlcicpLm9uKCdjbGljaycsIGZ1bmN0aW9uKCkge1xuICAgICAgJChgLmhvb2skeyQodGhpcykuZGF0YSgnaG9vay1pZCcpfWApLnByb3AoJ2NoZWNrZWQnLCAkKHRoaXMpLnByb3AoJ2NoZWNrZWQnKSk7XG4gICAgfSk7XG5cbiAgICBzZWxmLiRtb2R1bGVzTGlzdC5vbignY2xpY2snLCBmdW5jdGlvbigpIHtcbiAgICAgICQoYCNHaG9vayR7JCh0aGlzKS5kYXRhKCdob29rLWlkJyl9YCkucHJvcChcbiAgICAgICAgJ2NoZWNrZWQnLFxuICAgICAgICAkKGAuaG9vayR7JCh0aGlzKS5kYXRhKCdob29rLWlkJyl9Om5vdCg6Y2hlY2tlZClgKS5sZW5ndGggPT09IDBcbiAgICAgICk7XG4gICAgfSk7XG5cbiAgICBzZWxmLiRtb2R1bGVCdXR0b25zVXBkYXRlLm9uKCdjbGljaycsIGZ1bmN0aW9uKCkge1xuICAgICAgY29uc3QgJGJ0biA9ICQodGhpcyk7XG4gICAgICBjb25zdCAkY3VycmVudCA9ICRidG4uY2xvc2VzdCgnLm1vZHVsZS1pdGVtJyk7XG4gICAgICBsZXQgJGRlc3RpbmF0aW9uO1xuXG4gICAgICBpZiAoJGJ0bi5kYXRhKCd3YXknKSkge1xuICAgICAgICAkZGVzdGluYXRpb24gPSAkY3VycmVudC5uZXh0KCcubW9kdWxlLWl0ZW0nKTtcbiAgICAgIH0gZWxzZSB7XG4gICAgICAgICRkZXN0aW5hdGlvbiA9ICRjdXJyZW50LnByZXYoJy5tb2R1bGUtaXRlbScpO1xuICAgICAgfVxuXG4gICAgICBpZiAoJGRlc3RpbmF0aW9uLmxlbmd0aCA9PT0gMCkge1xuICAgICAgICByZXR1cm4gZmFsc2U7XG4gICAgICB9XG5cbiAgICAgIGlmICgkYnRuLmRhdGEoJ3dheScpKSB7XG4gICAgICAgICRjdXJyZW50Lmluc2VydEFmdGVyKCRkZXN0aW5hdGlvbik7XG4gICAgICB9IGVsc2Uge1xuICAgICAgICAkY3VycmVudC5pbnNlcnRCZWZvcmUoJGRlc3RpbmF0aW9uKTtcbiAgICAgIH1cblxuICAgICAgc2VsZi51cGRhdGVQb3NpdGlvbnMoXG4gICAgICAgIHtcbiAgICAgICAgICBob29rSWQ6ICRidG4uZGF0YSgnaG9vay1pZCcpLFxuICAgICAgICAgIG1vZHVsZUlkOiAkYnRuLmRhdGEoJ21vZHVsZS1pZCcpLFxuICAgICAgICAgIHdheTogJGJ0bi5kYXRhKCd3YXknKSxcbiAgICAgICAgICBwb3NpdGlvbnM6IFtdLFxuICAgICAgICB9LFxuICAgICAgICAkYnRuLmNsb3Nlc3QoJ3VsJylcbiAgICAgICk7XG5cbiAgICAgIHJldHVybiBmYWxzZTtcbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBIYW5kbGUgc29ydGFibGUgZXZlbnRzXG4gICAqL1xuICBoYW5kbGVTb3J0YWJsZSgpIHtcbiAgICBjb25zdCBzZWxmID0gdGhpcztcblxuICAgICQoJy5zb3J0YWJsZScpLnNvcnRhYmxlKHtcbiAgICAgIGZvcmNlUGxhY2Vob2xkZXJTaXplOiB0cnVlLFxuICAgICAgc3RhcnQ6IGZ1bmN0aW9uKGUsIHVpKSB7XG4gICAgICAgICQodGhpcykuZGF0YSgncHJldmlvdXMtaW5kZXgnLCB1aS5pdGVtLmluZGV4KCkpO1xuICAgICAgfSxcbiAgICAgIHVwZGF0ZTogZnVuY3Rpb24oJGV2ZW50LCB1aSkge1xuICAgICAgICBjb25zdCBbIGhvb2tJZCwgbW9kdWxlSWQgXSA9IHVpLml0ZW0uYXR0cignaWQnKS5zcGxpdCgnXycpO1xuXG4gICAgICAgIGNvbnN0ICRkYXRhID0ge1xuICAgICAgICAgIGhvb2tJZCxcbiAgICAgICAgICBtb2R1bGVJZCxcbiAgICAgICAgICB3YXk6ICgkKHRoaXMpLmRhdGEoJ3ByZXZpb3VzLWluZGV4JykgPCB1aS5pdGVtLmluZGV4KCkpID8gMSA6IDAsXG4gICAgICAgICAgcG9zaXRpb25zOiBbXSxcbiAgICAgICAgfTtcblxuICAgICAgICBzZWxmLnVwZGF0ZVBvc2l0aW9ucyhcbiAgICAgICAgICAkZGF0YSxcbiAgICAgICAgICAkKCRldmVudC50YXJnZXQpXG4gICAgICAgICk7XG4gICAgICB9LFxuICAgIH0pO1xuICB9XG5cbiAgdXBkYXRlUG9zaXRpb25zKCRkYXRhLCAkbGlzdCkge1xuICAgIGNvbnN0IHNlbGYgPSB0aGlzO1xuICAgICQuZWFjaCgkbGlzdC5jaGlsZHJlbigpLCBmdW5jdGlvbihpbmRleCwgZWxlbWVudCkge1xuICAgICAgJGRhdGEucG9zaXRpb25zLnB1c2goJChlbGVtZW50KS5hdHRyKCdpZCcpKTtcbiAgICB9KTtcblxuICAgICQuYWpheCh7XG4gICAgICB0eXBlOiAnUE9TVCcsXG4gICAgICBoZWFkZXJzOiB7J2NhY2hlLWNvbnRyb2wnOiAnbm8tY2FjaGUnfSxcbiAgICAgIHVybDogc2VsZi4kbW9kdWxlUG9zaXRpb25zRm9ybS5kYXRhKCd1cGRhdGUtdXJsJyksXG4gICAgICBkYXRhOiAkZGF0YSxcbiAgICAgIHN1Y2Nlc3M6ICgpID0+IHtcbiAgICAgICAgbGV0IHN0YXJ0ID0gMDtcbiAgICAgICAgJC5lYWNoKCRsaXN0LmNoaWxkcmVuKCksIGZ1bmN0aW9uKGluZGV4LCBlbGVtZW50KSB7XG4gICAgICAgICAgY29uc29sZS5sb2coJChlbGVtZW50KS5maW5kKCcuaW5kZXgtcG9zaXRpb24nKSk7XG4gICAgICAgICAgJChlbGVtZW50KS5maW5kKCcuaW5kZXgtcG9zaXRpb24nKS5odG1sKCsrc3RhcnQpO1xuICAgICAgICB9KTtcblxuICAgICAgICB3aW5kb3cuc2hvd1N1Y2Nlc3NNZXNzYWdlKHdpbmRvdy51cGRhdGVfc3VjY2Vzc19tc2cpO1xuICAgICAgfVxuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIEZpbHRlciBob29rcyAvIG1vZHVsZXMgc2VhcmNoIGFuZCBldmVyeXRoaW5nXG4gICAqIGFib3V0IGhvb2tzIHBvc2l0aW9ucy5cbiAgICovXG4gIG1vZHVsZXNQb3NpdGlvbkZpbHRlckhvb2tzKCkge1xuICAgIGNvbnN0IHNlbGYgPSB0aGlzO1xuICAgIGNvbnN0ICRob29rTmFtZSA9IHNlbGYuJGhvb2tTZWFyY2gudmFsKCk7XG4gICAgY29uc3QgJG1vZHVsZUlkID0gc2VsZi4kc2hvd01vZHVsZXMudmFsKCk7XG4gICAgY29uc3QgJHJlZ2V4ID0gbmV3IFJlZ0V4cChgKCR7JGhvb2tOYW1lfSlgLCAnZ2knKTtcblxuICAgIGZvciAobGV0ICRpZCA9IDA7ICRpZCA8IHNlbGYuJGhvb2tzTGlzdC5sZW5ndGg7ICRpZCsrKSB7XG4gICAgICBzZWxmLiRob29rc0xpc3RbJGlkXS5jb250YWluZXIudG9nZ2xlKCRob29rTmFtZSA9PT0gJycgJiYgJG1vZHVsZUlkID09PSAnYWxsJyk7XG4gICAgICBzZWxmLiRob29rc0xpc3RbJGlkXS5lbGVtZW50Lmh0bWwoc2VsZi4kaG9va3NMaXN0WyRpZF0udGl0bGUpO1xuICAgICAgc2VsZi4kaG9va3NMaXN0WyRpZF0uY29udGFpbmVyLmZpbmQoJy5tb2R1bGUtaXRlbScpLnJlbW92ZUNsYXNzKCdoaWdobGlnaHQnKTtcbiAgICB9XG5cbiAgICAvLyBIYXZlIHNlbGVjdCBhIGhvb2sgbmFtZSBvciBhIG1vZHVsZSBpZFxuICAgIGlmICgkaG9va05hbWUgIT09ICcnIHx8ICRtb2R1bGVJZCAhPT0gJ2FsbCcpIHtcbiAgICAgIC8vIFByZXBhcmUgc2V0IG9mIG1hdGNoZWQgZWxlbWVudHNcbiAgICAgIGxldCAkaG9va3NUb1Nob3dGcm9tTW9kdWxlID0gJCgpO1xuICAgICAgbGV0ICRob29rc1RvU2hvd0Zyb21Ib29rTmFtZSA9ICQoKTtcbiAgICAgIGxldCAkY3VycmVudEhvb2tzO1xuICAgICAgbGV0ICRzdGFydDtcblxuICAgICAgZm9yIChsZXQgJGlkID0gMDsgJGlkIDwgc2VsZi4kaG9va3NMaXN0Lmxlbmd0aDsgJGlkKyspIHtcbiAgICAgICAgLy8gUHJlcGFyZSBoaWdobGlnaHQgd2hlbiBvbmUgbW9kdWxlIGlzIHNlbGVjdGVkXG4gICAgICAgIGlmICgkbW9kdWxlSWQgIT09ICdhbGwnKSB7XG4gICAgICAgICAgJGN1cnJlbnRIb29rcyA9IHNlbGYuJGhvb2tzTGlzdFskaWRdLmNvbnRhaW5lci5maW5kKGAubW9kdWxlLXBvc2l0aW9uLSR7JG1vZHVsZUlkfWApO1xuICAgICAgICAgIGlmICgkY3VycmVudEhvb2tzLmxlbmd0aCA+IDApIHtcbiAgICAgICAgICAgICRob29rc1RvU2hvd0Zyb21Nb2R1bGUgPSAkaG9va3NUb1Nob3dGcm9tTW9kdWxlLmFkZChzZWxmLiRob29rc0xpc3RbJGlkXS5jb250YWluZXIpO1xuICAgICAgICAgICAgJGN1cnJlbnRIb29rcy5hZGRDbGFzcygnaGlnaGxpZ2h0Jyk7XG4gICAgICAgICAgfVxuICAgICAgICB9XG5cbiAgICAgICAgLy8gUHJlcGFyZSBoaWdobGlnaHQgd2hlbiB0aGVyZSBpcyBhIGhvb2sgbmFtZVxuICAgICAgICBpZiAoJGhvb2tOYW1lICE9PSAnJykge1xuICAgICAgICAgICRzdGFydCA9IHNlbGYuJGhvb2tzTGlzdFskaWRdLnRpdGxlLnRvTG93ZXJDYXNlKCkuc2VhcmNoKCRob29rTmFtZS50b0xvd2VyQ2FzZSgpKTtcbiAgICAgICAgICBpZiAoJHN0YXJ0ICE9PSAtMSkge1xuICAgICAgICAgICAgJGhvb2tzVG9TaG93RnJvbUhvb2tOYW1lID0gJGhvb2tzVG9TaG93RnJvbUhvb2tOYW1lLmFkZChzZWxmLiRob29rc0xpc3RbJGlkXS5jb250YWluZXIpO1xuICAgICAgICAgICAgc2VsZi4kaG9va3NMaXN0WyRpZF0uZWxlbWVudC5odG1sKFxuICAgICAgICAgICAgICBzZWxmLiRob29rc0xpc3RbJGlkXS50aXRsZS5yZXBsYWNlKFxuICAgICAgICAgICAgICAgICRyZWdleCxcbiAgICAgICAgICAgICAgICAnPHNwYW4gY2xhc3M9XCJoaWdobGlnaHRcIj4kMTwvc3Bhbj4nXG4gICAgICAgICAgICAgIClcbiAgICAgICAgICAgICk7XG4gICAgICAgICAgfVxuICAgICAgICB9XG4gICAgICB9XG5cbiAgICAgIC8vIE5vdGhpbmcgc2VsZWN0ZWRcbiAgICAgIGlmICgkbW9kdWxlSWQgPT09ICdhbGwnICYmICRob29rTmFtZSAhPT0gJycpIHtcbiAgICAgICAgJGhvb2tzVG9TaG93RnJvbUhvb2tOYW1lLnNob3coKTtcbiAgICAgIH0gZWxzZSBpZiAoJGhvb2tOYW1lID09PSAnJyAmJiAkbW9kdWxlSWQgIT09ICdhbGwnKSB7IC8vIEhhdmUgbm8gaG9vayBidWcgaGF2ZSBhIG1vZHVsZVxuICAgICAgICAkaG9va3NUb1Nob3dGcm9tTW9kdWxlLnNob3coKTtcbiAgICAgIH0gZWxzZSB7IC8vIEJvdGggc2VsZWN0ZWRcbiAgICAgICAgJGhvb2tzVG9TaG93RnJvbUhvb2tOYW1lLmZpbHRlcigkaG9va3NUb1Nob3dGcm9tTW9kdWxlKS5zaG93KCk7XG4gICAgICB9XG4gICAgfVxuXG4gICAgaWYgKCFzZWxmLiRob29rUG9zaXRpb24ucHJvcCgnY2hlY2tlZCcpKSB7XG4gICAgICBmb3IgKGxldCAkaWQgPSAwOyAkaWQgPCBzZWxmLiRob29rc0xpc3QubGVuZ3RoOyAkaWQrKykge1xuICAgICAgICBpZiAoc2VsZi4kaG9va3NMaXN0WyRpZF0uY29udGFpbmVyLmlzKCcuaG9vay1wb3NpdGlvbicpKSB7XG4gICAgICAgICAgc2VsZi4kaG9va3NMaXN0WyRpZF0uY29udGFpbmVyLmhpZGUoKTtcbiAgICAgICAgfVxuICAgICAgfVxuICAgIH1cbiAgfTtcbn1cblxuZXhwb3J0IGRlZmF1bHQgUG9zaXRpb25zTGlzdEhhbmRsZXI7XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9wYWdlcy9pbXByb3ZlL2Rlc2lnbl9wb3NpdGlvbnMvcG9zaXRpb25zLWxpc3QtaGFuZGxlci5qcyIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmltcG9ydCBQb3NpdGlvbnNMaXN0SGFuZGxlciBmcm9tICcuL3Bvc2l0aW9ucy1saXN0LWhhbmRsZXInO1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbiQoKCkgPT4ge1xuICBuZXcgUG9zaXRpb25zTGlzdEhhbmRsZXIoKTtcbn0pO1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvcGFnZXMvaW1wcm92ZS9kZXNpZ25fcG9zaXRpb25zL2luZGV4LmpzIl0sInNvdXJjZVJvb3QiOiIifQ==