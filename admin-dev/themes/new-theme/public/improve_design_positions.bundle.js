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
/******/ 	return __webpack_require__(__webpack_require__.s = 355);
/******/ })
/************************************************************************/
/******/ ({

/***/ 271:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _slicedToArray = function () { function sliceIterator(arr, i) { var _arr = []; var _n = true; var _d = false; var _e = undefined; try { for (var _i = arr[Symbol.iterator](), _s; !(_n = (_s = _i.next()).done); _n = true) { _arr.push(_s.value); if (i && _arr.length === i) break; } } catch (err) { _d = true; _e = err; } finally { try { if (!_n && _i["return"]) _i["return"](); } finally { if (_d) throw _e; } } return _arr; } return function (arr, i) { if (Array.isArray(arr)) { return arr; } else if (Symbol.iterator in Object(arr)) { return sliceIterator(arr, i); } else { throw new TypeError("Invalid attempt to destructure non-iterable instance"); } }; }();

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

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

/***/ 355:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _positionsListHandler = __webpack_require__(271);

var _positionsListHandler2 = _interopRequireDefault(_positionsListHandler);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

var $ = window.$; /**
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

$(function () {
  new _positionsListHandler2.default();
});

/***/ })

/******/ });
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAgMWU2NjI2MzkwMGU5NjZkZmJiZjA/ODU5MCoqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqIiwid2VicGFjazovLy8uL2pzL3BhZ2VzL2ltcHJvdmUvZGVzaWduX3Bvc2l0aW9ucy9wb3NpdGlvbnMtbGlzdC1oYW5kbGVyLmpzIiwid2VicGFjazovLy8uL2pzL3BhZ2VzL2ltcHJvdmUvZGVzaWduX3Bvc2l0aW9ucy9pbmRleC5qcyJdLCJuYW1lcyI6WyIkIiwid2luZG93IiwiUG9zaXRpb25zTGlzdEhhbmRsZXIiLCJsZW5ndGgiLCJzZWxmIiwiJHBhbmVsU2VsZWN0aW9uIiwiJHBhbmVsU2VsZWN0aW9uU2luZ2xlU2VsZWN0aW9uIiwiJHBhbmVsU2VsZWN0aW9uTXVsdGlwbGVTZWxlY3Rpb24iLCIkcGFuZWxTZWxlY3Rpb25PcmlnaW5hbFkiLCJvZmZzZXQiLCJ0b3AiLCIkc2hvd01vZHVsZXMiLCIkbW9kdWxlc0xpc3QiLCIkaG9va1Bvc2l0aW9uIiwiJGhvb2tTZWFyY2giLCIkbW9kdWxlUG9zaXRpb25zRm9ybSIsIiRtb2R1bGVVbmhvb2tCdXR0b24iLCIkbW9kdWxlQnV0dG9uc1VwZGF0ZSIsImhhbmRsZUxpc3QiLCJoYW5kbGVTb3J0YWJsZSIsIm9uIiwiaGFuZGxlIiwiJHNjcm9sbFRvcCIsInNjcm9sbFRvcCIsImNzcyIsIiRjaGVja2VkQ291bnQiLCJmaWx0ZXIiLCJoaWRlIiwic2hvdyIsImh0bWwiLCJmaW5kIiwiY2xpY2siLCJ0cmlnZ2VyIiwiJGhvb2tzTGlzdCIsImVhY2giLCIkdGhpcyIsInB1c2giLCJwYXJlbnRzIiwic2VsZWN0MiIsIm1vZHVsZXNQb3NpdGlvbkZpbHRlckhvb2tzIiwiZSIsImtleUNvZGUiLCJ3aGljaCIsImRhdGEiLCJwcm9wIiwiJGJ0biIsIiRjdXJyZW50IiwiY2xvc2VzdCIsIiRkZXN0aW5hdGlvbiIsIm5leHQiLCJwcmV2IiwiaW5zZXJ0QWZ0ZXIiLCJpbnNlcnRCZWZvcmUiLCJ1cGRhdGVQb3NpdGlvbnMiLCJob29rSWQiLCJtb2R1bGVJZCIsIndheSIsInBvc2l0aW9ucyIsInNvcnRhYmxlIiwiZm9yY2VQbGFjZWhvbGRlclNpemUiLCJzdGFydCIsInVpIiwiaXRlbSIsImluZGV4IiwidXBkYXRlIiwiJGV2ZW50IiwiYXR0ciIsInNwbGl0IiwiJGRhdGEiLCJ0YXJnZXQiLCIkbGlzdCIsImNoaWxkcmVuIiwiZWxlbWVudCIsImFqYXgiLCJ0eXBlIiwiaGVhZGVycyIsInVybCIsInN1Y2Nlc3MiLCJjb25zb2xlIiwibG9nIiwic2hvd1N1Y2Nlc3NNZXNzYWdlIiwidXBkYXRlX3N1Y2Nlc3NfbXNnIiwiJGhvb2tOYW1lIiwidmFsIiwiJG1vZHVsZUlkIiwiJHJlZ2V4IiwiUmVnRXhwIiwiJGlkIiwiY29udGFpbmVyIiwidG9nZ2xlIiwidGl0bGUiLCJyZW1vdmVDbGFzcyIsIiRob29rc1RvU2hvd0Zyb21Nb2R1bGUiLCIkaG9va3NUb1Nob3dGcm9tSG9va05hbWUiLCIkY3VycmVudEhvb2tzIiwiJHN0YXJ0IiwiYWRkIiwiYWRkQ2xhc3MiLCJ0b0xvd2VyQ2FzZSIsInNlYXJjaCIsInJlcGxhY2UiLCJpcyJdLCJtYXBwaW5ncyI6Ijs7QUFBQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTs7O0FBR0E7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0EsbURBQTJDLGNBQWM7O0FBRXpEO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsYUFBSztBQUNMO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0EsbUNBQTJCLDBCQUEwQixFQUFFO0FBQ3ZELHlDQUFpQyxlQUFlO0FBQ2hEO0FBQ0E7QUFDQTs7QUFFQTtBQUNBLDhEQUFzRCwrREFBK0Q7O0FBRXJIO0FBQ0E7O0FBRUE7QUFDQTs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7O0FDaEVBOzs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7O0FBeUJBLElBQU1BLElBQUlDLE9BQU9ELENBQWpCOztJQUVNRSxvQjtBQUNKLGtDQUFjO0FBQUE7O0FBQ1osUUFBSUYsRUFBRSxtQkFBRixFQUF1QkcsTUFBdkIsS0FBa0MsQ0FBdEMsRUFBeUM7QUFDdkM7QUFDRDs7QUFFRCxRQUFNQyxPQUFPLElBQWI7QUFDQUEsU0FBS0MsZUFBTCxHQUF1QkwsRUFBRSxtQ0FBRixDQUF2QjtBQUNBSSxTQUFLRSw4QkFBTCxHQUFzQ04sRUFBRSxvQ0FBRixDQUF0QztBQUNBSSxTQUFLRyxnQ0FBTCxHQUF3Q1AsRUFBRSxzQ0FBRixDQUF4Qzs7QUFFQUksU0FBS0ksd0JBQUwsR0FBZ0NKLEtBQUtDLGVBQUwsQ0FBcUJJLE1BQXJCLEdBQThCQyxHQUE5RDtBQUNBTixTQUFLTyxZQUFMLEdBQW9CWCxFQUFFLGVBQUYsQ0FBcEI7QUFDQUksU0FBS1EsWUFBTCxHQUFvQlosRUFBRSw0QkFBRixDQUFwQjtBQUNBSSxTQUFLUyxhQUFMLEdBQXFCYixFQUFFLGdCQUFGLENBQXJCO0FBQ0FJLFNBQUtVLFdBQUwsR0FBbUJkLEVBQUUsY0FBRixDQUFuQjtBQUNBSSxTQUFLVyxvQkFBTCxHQUE0QmYsRUFBRSx3QkFBRixDQUE1QjtBQUNBSSxTQUFLWSxtQkFBTCxHQUEyQmhCLEVBQUUsZ0NBQUYsQ0FBM0I7QUFDQUksU0FBS2Esb0JBQUwsR0FBNEJqQixFQUFFLDZCQUFGLENBQTVCOztBQUVBSSxTQUFLYyxVQUFMO0FBQ0FkLFNBQUtlLGNBQUw7O0FBRUFuQixNQUFFLHlDQUFGLEVBQTZDb0IsRUFBN0MsQ0FBZ0QsUUFBaEQsRUFBMEQ7QUFBQSxhQUFNaEIsS0FBS2lCLE1BQUwsRUFBTjtBQUFBLEtBQTFEO0FBQ0Q7O0FBRUQ7Ozs7Ozs7aUNBR2E7QUFDWCxVQUFNakIsT0FBTyxJQUFiOztBQUVBSixRQUFFQyxNQUFGLEVBQVVtQixFQUFWLENBQWEsUUFBYixFQUF1QixZQUFNO0FBQzNCLFlBQU1FLGFBQWF0QixFQUFFQyxNQUFGLEVBQVVzQixTQUFWLEVBQW5CO0FBQ0FuQixhQUFLQyxlQUFMLENBQXFCbUIsR0FBckIsQ0FDRSxLQURGLEVBRUVGLGFBQWEsRUFBYixHQUFrQixDQUFsQixHQUFzQkEsYUFBYWxCLEtBQUtJLHdCQUYxQztBQUlELE9BTkQ7O0FBUUFKLFdBQUtRLFlBQUwsQ0FBa0JRLEVBQWxCLENBQXFCLFFBQXJCLEVBQStCLFlBQVk7QUFDekMsWUFBTUssZ0JBQWdCckIsS0FBS1EsWUFBTCxDQUFrQmMsTUFBbEIsQ0FBeUIsVUFBekIsRUFBcUN2QixNQUEzRDs7QUFFQSxZQUFJc0Isa0JBQWtCLENBQXRCLEVBQXlCO0FBQ3ZCckIsZUFBS1ksbUJBQUwsQ0FBeUJXLElBQXpCO0FBQ0F2QixlQUFLQyxlQUFMLENBQXFCc0IsSUFBckI7QUFDQXZCLGVBQUtFLDhCQUFMLENBQW9DcUIsSUFBcEM7QUFDQXZCLGVBQUtHLGdDQUFMLENBQXNDb0IsSUFBdEM7QUFDRCxTQUxELE1BS08sSUFBSUYsa0JBQWtCLENBQXRCLEVBQXlCO0FBQzlCckIsZUFBS1ksbUJBQUwsQ0FBeUJZLElBQXpCO0FBQ0F4QixlQUFLQyxlQUFMLENBQXFCdUIsSUFBckI7QUFDQXhCLGVBQUtFLDhCQUFMLENBQW9Dc0IsSUFBcEM7QUFDQXhCLGVBQUtHLGdDQUFMLENBQXNDb0IsSUFBdEM7QUFDRCxTQUxNLE1BS0E7QUFDTHZCLGVBQUtZLG1CQUFMLENBQXlCWSxJQUF6QjtBQUNBeEIsZUFBS0MsZUFBTCxDQUFxQnVCLElBQXJCO0FBQ0F4QixlQUFLRSw4QkFBTCxDQUFvQ3FCLElBQXBDO0FBQ0F2QixlQUFLRyxnQ0FBTCxDQUFzQ3FCLElBQXRDO0FBQ0E1QixZQUFFLG1DQUFGLEVBQXVDNkIsSUFBdkMsQ0FBNENKLGFBQTVDO0FBQ0Q7QUFDRixPQXBCRDs7QUFzQkFyQixXQUFLQyxlQUFMLENBQXFCeUIsSUFBckIsQ0FBMEIsUUFBMUIsRUFBb0NDLEtBQXBDLENBQTBDLFlBQU07QUFDOUMvQixVQUFFLDJCQUFGLEVBQStCZ0MsT0FBL0IsQ0FBdUMsT0FBdkM7QUFDRCxPQUZEOztBQUlBNUIsV0FBSzZCLFVBQUwsR0FBa0IsRUFBbEI7QUFDQWpDLFFBQUUsK0JBQUYsRUFBbUNrQyxJQUFuQyxDQUF3QyxZQUFZO0FBQ2xELFlBQU1DLFFBQVFuQyxFQUFFLElBQUYsQ0FBZDtBQUNBSSxhQUFLNkIsVUFBTCxDQUFnQkcsSUFBaEIsQ0FBcUI7QUFDbkIsbUJBQVNELE1BQU1OLElBQU4sRUFEVTtBQUVuQixxQkFBV00sS0FGUTtBQUduQix1QkFBYUEsTUFBTUUsT0FBTixDQUFjLGFBQWQ7QUFITSxTQUFyQjtBQUtELE9BUEQ7O0FBU0FqQyxXQUFLTyxZQUFMLENBQWtCMkIsT0FBbEI7QUFDQWxDLFdBQUtPLFlBQUwsQ0FBa0JTLEVBQWxCLENBQXFCLFFBQXJCLEVBQStCLFlBQU07QUFDbkNoQixhQUFLbUMsMEJBQUw7QUFDRCxPQUZEOztBQUlBbkMsV0FBS1MsYUFBTCxDQUFtQk8sRUFBbkIsQ0FBc0IsUUFBdEIsRUFBZ0MsWUFBTTtBQUNwQ2hCLGFBQUttQywwQkFBTDtBQUNELE9BRkQ7O0FBSUFuQyxXQUFLVSxXQUFMLENBQWlCTSxFQUFqQixDQUFvQixPQUFwQixFQUE2QixZQUFNO0FBQ2pDaEIsYUFBS21DLDBCQUFMO0FBQ0QsT0FGRDs7QUFJQW5DLFdBQUtVLFdBQUwsQ0FBaUJNLEVBQWpCLENBQW9CLFVBQXBCLEVBQWdDLFVBQUNvQixDQUFELEVBQU87QUFDckMsWUFBTUMsVUFBVUQsRUFBRUMsT0FBRixJQUFhRCxFQUFFRSxLQUEvQjtBQUNBLGVBQU9ELFlBQVksRUFBbkI7QUFDRCxPQUhEOztBQUtBekMsUUFBRSxlQUFGLEVBQW1Cb0IsRUFBbkIsQ0FBc0IsT0FBdEIsRUFBK0IsWUFBVztBQUN4Q3BCLG9CQUFVQSxFQUFFLElBQUYsRUFBUTJDLElBQVIsQ0FBYSxTQUFiLENBQVYsRUFBcUNDLElBQXJDLENBQTBDLFNBQTFDLEVBQXFENUMsRUFBRSxJQUFGLEVBQVE0QyxJQUFSLENBQWEsU0FBYixDQUFyRDtBQUNELE9BRkQ7O0FBSUF4QyxXQUFLUSxZQUFMLENBQWtCUSxFQUFsQixDQUFxQixPQUFyQixFQUE4QixZQUFXO0FBQ3ZDcEIscUJBQVdBLEVBQUUsSUFBRixFQUFRMkMsSUFBUixDQUFhLFNBQWIsQ0FBWCxFQUFzQ0MsSUFBdEMsQ0FDRSxTQURGLEVBRUU1QyxZQUFVQSxFQUFFLElBQUYsRUFBUTJDLElBQVIsQ0FBYSxTQUFiLENBQVYscUJBQW1EeEMsTUFBbkQsS0FBOEQsQ0FGaEU7QUFJRCxPQUxEOztBQU9BQyxXQUFLYSxvQkFBTCxDQUEwQkcsRUFBMUIsQ0FBNkIsT0FBN0IsRUFBc0MsWUFBVztBQUMvQyxZQUFNeUIsT0FBTzdDLEVBQUUsSUFBRixDQUFiO0FBQ0EsWUFBTThDLFdBQVdELEtBQUtFLE9BQUwsQ0FBYSxjQUFiLENBQWpCO0FBQ0EsWUFBSUMscUJBQUo7O0FBRUEsWUFBSUgsS0FBS0YsSUFBTCxDQUFVLEtBQVYsQ0FBSixFQUFzQjtBQUNwQksseUJBQWVGLFNBQVNHLElBQVQsQ0FBYyxjQUFkLENBQWY7QUFDRCxTQUZELE1BRU87QUFDTEQseUJBQWVGLFNBQVNJLElBQVQsQ0FBYyxjQUFkLENBQWY7QUFDRDs7QUFFRCxZQUFJRixhQUFhN0MsTUFBYixLQUF3QixDQUE1QixFQUErQjtBQUM3QixpQkFBTyxLQUFQO0FBQ0Q7O0FBRUQsWUFBSTBDLEtBQUtGLElBQUwsQ0FBVSxLQUFWLENBQUosRUFBc0I7QUFDcEJHLG1CQUFTSyxXQUFULENBQXFCSCxZQUFyQjtBQUNELFNBRkQsTUFFTztBQUNMRixtQkFBU00sWUFBVCxDQUFzQkosWUFBdEI7QUFDRDs7QUFFRDVDLGFBQUtpRCxlQUFMLENBQ0U7QUFDRUMsa0JBQVFULEtBQUtGLElBQUwsQ0FBVSxTQUFWLENBRFY7QUFFRVksb0JBQVVWLEtBQUtGLElBQUwsQ0FBVSxXQUFWLENBRlo7QUFHRWEsZUFBS1gsS0FBS0YsSUFBTCxDQUFVLEtBQVYsQ0FIUDtBQUlFYyxxQkFBVztBQUpiLFNBREYsRUFPRVosS0FBS0UsT0FBTCxDQUFhLElBQWIsQ0FQRjs7QUFVQSxlQUFPLEtBQVA7QUFDRCxPQWhDRDtBQWlDRDs7QUFFRDs7Ozs7O3FDQUdpQjtBQUNmLFVBQU0zQyxPQUFPLElBQWI7O0FBRUFKLFFBQUUsV0FBRixFQUFlMEQsUUFBZixDQUF3QjtBQUN0QkMsOEJBQXNCLElBREE7QUFFdEJDLGVBQU8sZUFBU3BCLENBQVQsRUFBWXFCLEVBQVosRUFBZ0I7QUFDckI3RCxZQUFFLElBQUYsRUFBUTJDLElBQVIsQ0FBYSxnQkFBYixFQUErQmtCLEdBQUdDLElBQUgsQ0FBUUMsS0FBUixFQUEvQjtBQUNELFNBSnFCO0FBS3RCQyxnQkFBUSxnQkFBU0MsTUFBVCxFQUFpQkosRUFBakIsRUFBcUI7QUFBQSxvQ0FDRUEsR0FBR0MsSUFBSCxDQUFRSSxJQUFSLENBQWEsSUFBYixFQUFtQkMsS0FBbkIsQ0FBeUIsR0FBekIsQ0FERjtBQUFBO0FBQUEsY0FDbkJiLE1BRG1CO0FBQUEsY0FDWEMsUUFEVzs7QUFHM0IsY0FBTWEsUUFBUTtBQUNaZCwwQkFEWTtBQUVaQyw4QkFGWTtBQUdaQyxpQkFBTXhELEVBQUUsSUFBRixFQUFRMkMsSUFBUixDQUFhLGdCQUFiLElBQWlDa0IsR0FBR0MsSUFBSCxDQUFRQyxLQUFSLEVBQWxDLEdBQXFELENBQXJELEdBQXlELENBSGxEO0FBSVpOLHVCQUFXO0FBSkMsV0FBZDs7QUFPQXJELGVBQUtpRCxlQUFMLENBQ0VlLEtBREYsRUFFRXBFLEVBQUVpRSxPQUFPSSxNQUFULENBRkY7QUFJRDtBQW5CcUIsT0FBeEI7QUFxQkQ7OztvQ0FFZUQsSyxFQUFPRSxLLEVBQU87QUFDNUIsVUFBTWxFLE9BQU8sSUFBYjtBQUNBSixRQUFFa0MsSUFBRixDQUFPb0MsTUFBTUMsUUFBTixFQUFQLEVBQXlCLFVBQVNSLEtBQVQsRUFBZ0JTLE9BQWhCLEVBQXlCO0FBQ2hESixjQUFNWCxTQUFOLENBQWdCckIsSUFBaEIsQ0FBcUJwQyxFQUFFd0UsT0FBRixFQUFXTixJQUFYLENBQWdCLElBQWhCLENBQXJCO0FBQ0QsT0FGRDs7QUFJQWxFLFFBQUV5RSxJQUFGLENBQU87QUFDTEMsY0FBTSxNQUREO0FBRUxDLGlCQUFTLEVBQUMsaUJBQWlCLFVBQWxCLEVBRko7QUFHTEMsYUFBS3hFLEtBQUtXLG9CQUFMLENBQTBCNEIsSUFBMUIsQ0FBK0IsWUFBL0IsQ0FIQTtBQUlMQSxjQUFNeUIsS0FKRDtBQUtMUyxpQkFBUyxtQkFBTTtBQUNiLGNBQUlqQixRQUFRLENBQVo7QUFDQTVELFlBQUVrQyxJQUFGLENBQU9vQyxNQUFNQyxRQUFOLEVBQVAsRUFBeUIsVUFBU1IsS0FBVCxFQUFnQlMsT0FBaEIsRUFBeUI7QUFDaERNLG9CQUFRQyxHQUFSLENBQVkvRSxFQUFFd0UsT0FBRixFQUFXMUMsSUFBWCxDQUFnQixpQkFBaEIsQ0FBWjtBQUNBOUIsY0FBRXdFLE9BQUYsRUFBVzFDLElBQVgsQ0FBZ0IsaUJBQWhCLEVBQW1DRCxJQUFuQyxDQUF3QyxFQUFFK0IsS0FBMUM7QUFDRCxXQUhEOztBQUtBM0QsaUJBQU8rRSxrQkFBUCxDQUEwQi9FLE9BQU9nRixrQkFBakM7QUFDRDtBQWJJLE9BQVA7QUFlRDs7QUFFRDs7Ozs7OztpREFJNkI7QUFDM0IsVUFBTTdFLE9BQU8sSUFBYjtBQUNBLFVBQU04RSxZQUFZOUUsS0FBS1UsV0FBTCxDQUFpQnFFLEdBQWpCLEVBQWxCO0FBQ0EsVUFBTUMsWUFBWWhGLEtBQUtPLFlBQUwsQ0FBa0J3RSxHQUFsQixFQUFsQjtBQUNBLFVBQU1FLFNBQVMsSUFBSUMsTUFBSixPQUFlSixTQUFmLFFBQTZCLElBQTdCLENBQWY7O0FBRUEsV0FBSyxJQUFJSyxNQUFNLENBQWYsRUFBa0JBLE1BQU1uRixLQUFLNkIsVUFBTCxDQUFnQjlCLE1BQXhDLEVBQWdEb0YsS0FBaEQsRUFBdUQ7QUFDckRuRixhQUFLNkIsVUFBTCxDQUFnQnNELEdBQWhCLEVBQXFCQyxTQUFyQixDQUErQkMsTUFBL0IsQ0FBc0NQLGNBQWMsRUFBZCxJQUFvQkUsY0FBYyxLQUF4RTtBQUNBaEYsYUFBSzZCLFVBQUwsQ0FBZ0JzRCxHQUFoQixFQUFxQmYsT0FBckIsQ0FBNkIzQyxJQUE3QixDQUFrQ3pCLEtBQUs2QixVQUFMLENBQWdCc0QsR0FBaEIsRUFBcUJHLEtBQXZEO0FBQ0F0RixhQUFLNkIsVUFBTCxDQUFnQnNELEdBQWhCLEVBQXFCQyxTQUFyQixDQUErQjFELElBQS9CLENBQW9DLGNBQXBDLEVBQW9ENkQsV0FBcEQsQ0FBZ0UsV0FBaEU7QUFDRDs7QUFFRDtBQUNBLFVBQUlULGNBQWMsRUFBZCxJQUFvQkUsY0FBYyxLQUF0QyxFQUE2QztBQUMzQztBQUNBLFlBQUlRLHlCQUF5QjVGLEdBQTdCO0FBQ0EsWUFBSTZGLDJCQUEyQjdGLEdBQS9CO0FBQ0EsWUFBSThGLHNCQUFKO0FBQ0EsWUFBSUMsZUFBSjs7QUFFQSxhQUFLLElBQUlSLE9BQU0sQ0FBZixFQUFrQkEsT0FBTW5GLEtBQUs2QixVQUFMLENBQWdCOUIsTUFBeEMsRUFBZ0RvRixNQUFoRCxFQUF1RDtBQUNyRDtBQUNBLGNBQUlILGNBQWMsS0FBbEIsRUFBeUI7QUFDdkJVLDRCQUFnQjFGLEtBQUs2QixVQUFMLENBQWdCc0QsSUFBaEIsRUFBcUJDLFNBQXJCLENBQStCMUQsSUFBL0IsdUJBQXdEc0QsU0FBeEQsQ0FBaEI7QUFDQSxnQkFBSVUsY0FBYzNGLE1BQWQsR0FBdUIsQ0FBM0IsRUFBOEI7QUFDNUJ5Rix1Q0FBeUJBLHVCQUF1QkksR0FBdkIsQ0FBMkI1RixLQUFLNkIsVUFBTCxDQUFnQnNELElBQWhCLEVBQXFCQyxTQUFoRCxDQUF6QjtBQUNBTSw0QkFBY0csUUFBZCxDQUF1QixXQUF2QjtBQUNEO0FBQ0Y7O0FBRUQ7QUFDQSxjQUFJZixjQUFjLEVBQWxCLEVBQXNCO0FBQ3BCYSxxQkFBUzNGLEtBQUs2QixVQUFMLENBQWdCc0QsSUFBaEIsRUFBcUJHLEtBQXJCLENBQTJCUSxXQUEzQixHQUF5Q0MsTUFBekMsQ0FBZ0RqQixVQUFVZ0IsV0FBVixFQUFoRCxDQUFUO0FBQ0EsZ0JBQUlILFdBQVcsQ0FBQyxDQUFoQixFQUFtQjtBQUNqQkYseUNBQTJCQSx5QkFBeUJHLEdBQXpCLENBQTZCNUYsS0FBSzZCLFVBQUwsQ0FBZ0JzRCxJQUFoQixFQUFxQkMsU0FBbEQsQ0FBM0I7QUFDQXBGLG1CQUFLNkIsVUFBTCxDQUFnQnNELElBQWhCLEVBQXFCZixPQUFyQixDQUE2QjNDLElBQTdCLENBQ0V6QixLQUFLNkIsVUFBTCxDQUFnQnNELElBQWhCLEVBQXFCRyxLQUFyQixDQUEyQlUsT0FBM0IsQ0FDRWYsTUFERixFQUVFLG1DQUZGLENBREY7QUFNRDtBQUNGO0FBQ0Y7O0FBRUQ7QUFDQSxZQUFJRCxjQUFjLEtBQWQsSUFBdUJGLGNBQWMsRUFBekMsRUFBNkM7QUFDM0NXLG1DQUF5QmpFLElBQXpCO0FBQ0QsU0FGRCxNQUVPLElBQUlzRCxjQUFjLEVBQWQsSUFBb0JFLGNBQWMsS0FBdEMsRUFBNkM7QUFBRTtBQUNwRFEsaUNBQXVCaEUsSUFBdkI7QUFDRCxTQUZNLE1BRUE7QUFBRTtBQUNQaUUsbUNBQXlCbkUsTUFBekIsQ0FBZ0NrRSxzQkFBaEMsRUFBd0RoRSxJQUF4RDtBQUNEO0FBQ0Y7O0FBRUQsVUFBSSxDQUFDeEIsS0FBS1MsYUFBTCxDQUFtQitCLElBQW5CLENBQXdCLFNBQXhCLENBQUwsRUFBeUM7QUFDdkMsYUFBSyxJQUFJMkMsUUFBTSxDQUFmLEVBQWtCQSxRQUFNbkYsS0FBSzZCLFVBQUwsQ0FBZ0I5QixNQUF4QyxFQUFnRG9GLE9BQWhELEVBQXVEO0FBQ3JELGNBQUluRixLQUFLNkIsVUFBTCxDQUFnQnNELEtBQWhCLEVBQXFCQyxTQUFyQixDQUErQmEsRUFBL0IsQ0FBa0MsZ0JBQWxDLENBQUosRUFBeUQ7QUFDdkRqRyxpQkFBSzZCLFVBQUwsQ0FBZ0JzRCxLQUFoQixFQUFxQkMsU0FBckIsQ0FBK0I3RCxJQUEvQjtBQUNEO0FBQ0Y7QUFDRjtBQUNGOzs7Ozs7a0JBR1l6QixvQjs7Ozs7Ozs7OztBQ3ZRZjs7Ozs7O0FBRUEsSUFBTUYsSUFBSUMsT0FBT0QsQ0FBakIsQyxDQTNCQTs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7OztBQTZCQUEsRUFBRSxZQUFNO0FBQ04sTUFBSUUsOEJBQUo7QUFDRCxDQUZELEUiLCJmaWxlIjoiaW1wcm92ZV9kZXNpZ25fcG9zaXRpb25zLmJ1bmRsZS5qcyIsInNvdXJjZXNDb250ZW50IjpbIiBcdC8vIFRoZSBtb2R1bGUgY2FjaGVcbiBcdHZhciBpbnN0YWxsZWRNb2R1bGVzID0ge307XG5cbiBcdC8vIFRoZSByZXF1aXJlIGZ1bmN0aW9uXG4gXHRmdW5jdGlvbiBfX3dlYnBhY2tfcmVxdWlyZV9fKG1vZHVsZUlkKSB7XG5cbiBcdFx0Ly8gQ2hlY2sgaWYgbW9kdWxlIGlzIGluIGNhY2hlXG4gXHRcdGlmKGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdKSB7XG4gXHRcdFx0cmV0dXJuIGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdLmV4cG9ydHM7XG4gXHRcdH1cbiBcdFx0Ly8gQ3JlYXRlIGEgbmV3IG1vZHVsZSAoYW5kIHB1dCBpdCBpbnRvIHRoZSBjYWNoZSlcbiBcdFx0dmFyIG1vZHVsZSA9IGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdID0ge1xuIFx0XHRcdGk6IG1vZHVsZUlkLFxuIFx0XHRcdGw6IGZhbHNlLFxuIFx0XHRcdGV4cG9ydHM6IHt9XG4gXHRcdH07XG5cbiBcdFx0Ly8gRXhlY3V0ZSB0aGUgbW9kdWxlIGZ1bmN0aW9uXG4gXHRcdG1vZHVsZXNbbW9kdWxlSWRdLmNhbGwobW9kdWxlLmV4cG9ydHMsIG1vZHVsZSwgbW9kdWxlLmV4cG9ydHMsIF9fd2VicGFja19yZXF1aXJlX18pO1xuXG4gXHRcdC8vIEZsYWcgdGhlIG1vZHVsZSBhcyBsb2FkZWRcbiBcdFx0bW9kdWxlLmwgPSB0cnVlO1xuXG4gXHRcdC8vIFJldHVybiB0aGUgZXhwb3J0cyBvZiB0aGUgbW9kdWxlXG4gXHRcdHJldHVybiBtb2R1bGUuZXhwb3J0cztcbiBcdH1cblxuXG4gXHQvLyBleHBvc2UgdGhlIG1vZHVsZXMgb2JqZWN0IChfX3dlYnBhY2tfbW9kdWxlc19fKVxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5tID0gbW9kdWxlcztcblxuIFx0Ly8gZXhwb3NlIHRoZSBtb2R1bGUgY2FjaGVcbiBcdF9fd2VicGFja19yZXF1aXJlX18uYyA9IGluc3RhbGxlZE1vZHVsZXM7XG5cbiBcdC8vIGlkZW50aXR5IGZ1bmN0aW9uIGZvciBjYWxsaW5nIGhhcm1vbnkgaW1wb3J0cyB3aXRoIHRoZSBjb3JyZWN0IGNvbnRleHRcbiBcdF9fd2VicGFja19yZXF1aXJlX18uaSA9IGZ1bmN0aW9uKHZhbHVlKSB7IHJldHVybiB2YWx1ZTsgfTtcblxuIFx0Ly8gZGVmaW5lIGdldHRlciBmdW5jdGlvbiBmb3IgaGFybW9ueSBleHBvcnRzXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmQgPSBmdW5jdGlvbihleHBvcnRzLCBuYW1lLCBnZXR0ZXIpIHtcbiBcdFx0aWYoIV9fd2VicGFja19yZXF1aXJlX18ubyhleHBvcnRzLCBuYW1lKSkge1xuIFx0XHRcdE9iamVjdC5kZWZpbmVQcm9wZXJ0eShleHBvcnRzLCBuYW1lLCB7XG4gXHRcdFx0XHRjb25maWd1cmFibGU6IGZhbHNlLFxuIFx0XHRcdFx0ZW51bWVyYWJsZTogdHJ1ZSxcbiBcdFx0XHRcdGdldDogZ2V0dGVyXG4gXHRcdFx0fSk7XG4gXHRcdH1cbiBcdH07XG5cbiBcdC8vIGdldERlZmF1bHRFeHBvcnQgZnVuY3Rpb24gZm9yIGNvbXBhdGliaWxpdHkgd2l0aCBub24taGFybW9ueSBtb2R1bGVzXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm4gPSBmdW5jdGlvbihtb2R1bGUpIHtcbiBcdFx0dmFyIGdldHRlciA9IG1vZHVsZSAmJiBtb2R1bGUuX19lc01vZHVsZSA/XG4gXHRcdFx0ZnVuY3Rpb24gZ2V0RGVmYXVsdCgpIHsgcmV0dXJuIG1vZHVsZVsnZGVmYXVsdCddOyB9IDpcbiBcdFx0XHRmdW5jdGlvbiBnZXRNb2R1bGVFeHBvcnRzKCkgeyByZXR1cm4gbW9kdWxlOyB9O1xuIFx0XHRfX3dlYnBhY2tfcmVxdWlyZV9fLmQoZ2V0dGVyLCAnYScsIGdldHRlcik7XG4gXHRcdHJldHVybiBnZXR0ZXI7XG4gXHR9O1xuXG4gXHQvLyBPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGxcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubyA9IGZ1bmN0aW9uKG9iamVjdCwgcHJvcGVydHkpIHsgcmV0dXJuIE9iamVjdC5wcm90b3R5cGUuaGFzT3duUHJvcGVydHkuY2FsbChvYmplY3QsIHByb3BlcnR5KTsgfTtcblxuIFx0Ly8gX193ZWJwYWNrX3B1YmxpY19wYXRoX19cbiBcdF9fd2VicGFja19yZXF1aXJlX18ucCA9IFwiXCI7XG5cbiBcdC8vIExvYWQgZW50cnkgbW9kdWxlIGFuZCByZXR1cm4gZXhwb3J0c1xuIFx0cmV0dXJuIF9fd2VicGFja19yZXF1aXJlX18oX193ZWJwYWNrX3JlcXVpcmVfXy5zID0gMzU1KTtcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyB3ZWJwYWNrL2Jvb3RzdHJhcCAxZTY2MjYzOTAwZTk2NmRmYmJmMCIsIi8qKlxuICogMjAwNy0yMDE5IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS50eHQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly93d3cucHJlc3Rhc2hvcC5jb20gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICovXG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuY2xhc3MgUG9zaXRpb25zTGlzdEhhbmRsZXIge1xuICBjb25zdHJ1Y3RvcigpIHtcbiAgICBpZiAoJChcIiNwb3NpdGlvbi1maWx0ZXJzXCIpLmxlbmd0aCA9PT0gMCkge1xuICAgICAgcmV0dXJuO1xuICAgIH1cblxuICAgIGNvbnN0IHNlbGYgPSB0aGlzO1xuICAgIHNlbGYuJHBhbmVsU2VsZWN0aW9uID0gJChcIiNtb2R1bGVzLXBvc2l0aW9uLXNlbGVjdGlvbi1wYW5lbFwiKTtcbiAgICBzZWxmLiRwYW5lbFNlbGVjdGlvblNpbmdsZVNlbGVjdGlvbiA9ICQoXCIjbW9kdWxlcy1wb3NpdGlvbi1zaW5nbGUtc2VsZWN0aW9uXCIpO1xuICAgIHNlbGYuJHBhbmVsU2VsZWN0aW9uTXVsdGlwbGVTZWxlY3Rpb24gPSAkKFwiI21vZHVsZXMtcG9zaXRpb24tbXVsdGlwbGUtc2VsZWN0aW9uXCIpO1xuXG4gICAgc2VsZi4kcGFuZWxTZWxlY3Rpb25PcmlnaW5hbFkgPSBzZWxmLiRwYW5lbFNlbGVjdGlvbi5vZmZzZXQoKS50b3A7XG4gICAgc2VsZi4kc2hvd01vZHVsZXMgPSAkKFwiI3Nob3ctbW9kdWxlc1wiKTtcbiAgICBzZWxmLiRtb2R1bGVzTGlzdCA9ICQoJy5tb2R1bGVzLXBvc2l0aW9uLWNoZWNrYm94Jyk7XG4gICAgc2VsZi4kaG9va1Bvc2l0aW9uID0gJChcIiNob29rLXBvc2l0aW9uXCIpO1xuICAgIHNlbGYuJGhvb2tTZWFyY2ggPSAkKFwiI2hvb2stc2VhcmNoXCIpO1xuICAgIHNlbGYuJG1vZHVsZVBvc2l0aW9uc0Zvcm0gPSAkKCcjbW9kdWxlLXBvc2l0aW9ucy1mb3JtJyk7XG4gICAgc2VsZi4kbW9kdWxlVW5ob29rQnV0dG9uID0gJCgnI3VuaG9vay1idXR0b24tcG9zaXRpb24tYm90dG9tJyk7XG4gICAgc2VsZi4kbW9kdWxlQnV0dG9uc1VwZGF0ZSA9ICQoJy5tb2R1bGUtYnV0dG9ucy11cGRhdGUgLmJ0bicpO1xuXG4gICAgc2VsZi5oYW5kbGVMaXN0KCk7XG4gICAgc2VsZi5oYW5kbGVTb3J0YWJsZSgpO1xuXG4gICAgJCgnaW5wdXRbbmFtZT1cImZvcm1bZ2VuZXJhbF1bZW5hYmxlX3Rvc11cIl0nKS5vbignY2hhbmdlJywgKCkgPT4gc2VsZi5oYW5kbGUoKSk7XG4gIH1cblxuICAvKipcbiAgICogSGFuZGxlIGFsbCBldmVudHMgZm9yIERlc2lnbiAtPiBQb3NpdGlvbnMgTGlzdFxuICAgKi9cbiAgaGFuZGxlTGlzdCgpIHtcbiAgICBjb25zdCBzZWxmID0gdGhpcztcblxuICAgICQod2luZG93KS5vbignc2Nyb2xsJywgKCkgPT4ge1xuICAgICAgY29uc3QgJHNjcm9sbFRvcCA9ICQod2luZG93KS5zY3JvbGxUb3AoKTtcbiAgICAgIHNlbGYuJHBhbmVsU2VsZWN0aW9uLmNzcyhcbiAgICAgICAgJ3RvcCcsXG4gICAgICAgICRzY3JvbGxUb3AgPCAyMCA/IDAgOiAkc2Nyb2xsVG9wIC0gc2VsZi4kcGFuZWxTZWxlY3Rpb25PcmlnaW5hbFlcbiAgICAgICk7XG4gICAgfSk7XG5cbiAgICBzZWxmLiRtb2R1bGVzTGlzdC5vbignY2hhbmdlJywgZnVuY3Rpb24gKCkge1xuICAgICAgY29uc3QgJGNoZWNrZWRDb3VudCA9IHNlbGYuJG1vZHVsZXNMaXN0LmZpbHRlcignOmNoZWNrZWQnKS5sZW5ndGg7XG5cbiAgICAgIGlmICgkY2hlY2tlZENvdW50ID09PSAwKSB7XG4gICAgICAgIHNlbGYuJG1vZHVsZVVuaG9va0J1dHRvbi5oaWRlKCk7XG4gICAgICAgIHNlbGYuJHBhbmVsU2VsZWN0aW9uLmhpZGUoKTtcbiAgICAgICAgc2VsZi4kcGFuZWxTZWxlY3Rpb25TaW5nbGVTZWxlY3Rpb24uaGlkZSgpO1xuICAgICAgICBzZWxmLiRwYW5lbFNlbGVjdGlvbk11bHRpcGxlU2VsZWN0aW9uLmhpZGUoKTtcbiAgICAgIH0gZWxzZSBpZiAoJGNoZWNrZWRDb3VudCA9PT0gMSkge1xuICAgICAgICBzZWxmLiRtb2R1bGVVbmhvb2tCdXR0b24uc2hvdygpO1xuICAgICAgICBzZWxmLiRwYW5lbFNlbGVjdGlvbi5zaG93KCk7XG4gICAgICAgIHNlbGYuJHBhbmVsU2VsZWN0aW9uU2luZ2xlU2VsZWN0aW9uLnNob3coKTtcbiAgICAgICAgc2VsZi4kcGFuZWxTZWxlY3Rpb25NdWx0aXBsZVNlbGVjdGlvbi5oaWRlKCk7XG4gICAgICB9IGVsc2Uge1xuICAgICAgICBzZWxmLiRtb2R1bGVVbmhvb2tCdXR0b24uc2hvdygpO1xuICAgICAgICBzZWxmLiRwYW5lbFNlbGVjdGlvbi5zaG93KCk7XG4gICAgICAgIHNlbGYuJHBhbmVsU2VsZWN0aW9uU2luZ2xlU2VsZWN0aW9uLmhpZGUoKTtcbiAgICAgICAgc2VsZi4kcGFuZWxTZWxlY3Rpb25NdWx0aXBsZVNlbGVjdGlvbi5zaG93KCk7XG4gICAgICAgICQoJyNtb2R1bGVzLXBvc2l0aW9uLXNlbGVjdGlvbi1jb3VudCcpLmh0bWwoJGNoZWNrZWRDb3VudCk7XG4gICAgICB9XG4gICAgfSk7XG5cbiAgICBzZWxmLiRwYW5lbFNlbGVjdGlvbi5maW5kKCdidXR0b24nKS5jbGljaygoKSA9PiB7XG4gICAgICAkKCdidXR0b25bbmFtZT1cInVuaG9va2Zvcm1cIl0nKS50cmlnZ2VyKCdjbGljaycpO1xuICAgIH0pO1xuXG4gICAgc2VsZi4kaG9va3NMaXN0ID0gW107XG4gICAgJCgnc2VjdGlvbi5ob29rLXBhbmVsIC5ob29rLW5hbWUnKS5lYWNoKGZ1bmN0aW9uICgpIHtcbiAgICAgIGNvbnN0ICR0aGlzID0gJCh0aGlzKTtcbiAgICAgIHNlbGYuJGhvb2tzTGlzdC5wdXNoKHtcbiAgICAgICAgJ3RpdGxlJzogJHRoaXMuaHRtbCgpLFxuICAgICAgICAnZWxlbWVudCc6ICR0aGlzLFxuICAgICAgICAnY29udGFpbmVyJzogJHRoaXMucGFyZW50cygnLmhvb2stcGFuZWwnKVxuICAgICAgfSk7XG4gICAgfSk7XG5cbiAgICBzZWxmLiRzaG93TW9kdWxlcy5zZWxlY3QyKCk7XG4gICAgc2VsZi4kc2hvd01vZHVsZXMub24oJ2NoYW5nZScsICgpID0+IHtcbiAgICAgIHNlbGYubW9kdWxlc1Bvc2l0aW9uRmlsdGVySG9va3MoKTtcbiAgICB9KTtcblxuICAgIHNlbGYuJGhvb2tQb3NpdGlvbi5vbignY2hhbmdlJywgKCkgPT4ge1xuICAgICAgc2VsZi5tb2R1bGVzUG9zaXRpb25GaWx0ZXJIb29rcygpO1xuICAgIH0pO1xuXG4gICAgc2VsZi4kaG9va1NlYXJjaC5vbignaW5wdXQnLCAoKSA9PiB7XG4gICAgICBzZWxmLm1vZHVsZXNQb3NpdGlvbkZpbHRlckhvb2tzKCk7XG4gICAgfSk7XG5cbiAgICBzZWxmLiRob29rU2VhcmNoLm9uKCdrZXlwcmVzcycsIChlKSA9PiB7XG4gICAgICBjb25zdCBrZXlDb2RlID0gZS5rZXlDb2RlIHx8IGUud2hpY2g7XG4gICAgICByZXR1cm4ga2V5Q29kZSAhPT0gMTM7XG4gICAgfSk7XG5cbiAgICAkKCcuaG9vay1jaGVja2VyJykub24oJ2NsaWNrJywgZnVuY3Rpb24oKSB7XG4gICAgICAkKGAuaG9vayR7JCh0aGlzKS5kYXRhKCdob29rLWlkJyl9YCkucHJvcCgnY2hlY2tlZCcsICQodGhpcykucHJvcCgnY2hlY2tlZCcpKTtcbiAgICB9KTtcblxuICAgIHNlbGYuJG1vZHVsZXNMaXN0Lm9uKCdjbGljaycsIGZ1bmN0aW9uKCkge1xuICAgICAgJChgI0dob29rJHskKHRoaXMpLmRhdGEoJ2hvb2staWQnKX1gKS5wcm9wKFxuICAgICAgICAnY2hlY2tlZCcsXG4gICAgICAgICQoYC5ob29rJHskKHRoaXMpLmRhdGEoJ2hvb2staWQnKX06bm90KDpjaGVja2VkKWApLmxlbmd0aCA9PT0gMFxuICAgICAgKTtcbiAgICB9KTtcblxuICAgIHNlbGYuJG1vZHVsZUJ1dHRvbnNVcGRhdGUub24oJ2NsaWNrJywgZnVuY3Rpb24oKSB7XG4gICAgICBjb25zdCAkYnRuID0gJCh0aGlzKTtcbiAgICAgIGNvbnN0ICRjdXJyZW50ID0gJGJ0bi5jbG9zZXN0KCcubW9kdWxlLWl0ZW0nKTtcbiAgICAgIGxldCAkZGVzdGluYXRpb247XG5cbiAgICAgIGlmICgkYnRuLmRhdGEoJ3dheScpKSB7XG4gICAgICAgICRkZXN0aW5hdGlvbiA9ICRjdXJyZW50Lm5leHQoJy5tb2R1bGUtaXRlbScpO1xuICAgICAgfSBlbHNlIHtcbiAgICAgICAgJGRlc3RpbmF0aW9uID0gJGN1cnJlbnQucHJldignLm1vZHVsZS1pdGVtJyk7XG4gICAgICB9XG5cbiAgICAgIGlmICgkZGVzdGluYXRpb24ubGVuZ3RoID09PSAwKSB7XG4gICAgICAgIHJldHVybiBmYWxzZTtcbiAgICAgIH1cblxuICAgICAgaWYgKCRidG4uZGF0YSgnd2F5JykpIHtcbiAgICAgICAgJGN1cnJlbnQuaW5zZXJ0QWZ0ZXIoJGRlc3RpbmF0aW9uKTtcbiAgICAgIH0gZWxzZSB7XG4gICAgICAgICRjdXJyZW50Lmluc2VydEJlZm9yZSgkZGVzdGluYXRpb24pO1xuICAgICAgfVxuXG4gICAgICBzZWxmLnVwZGF0ZVBvc2l0aW9ucyhcbiAgICAgICAge1xuICAgICAgICAgIGhvb2tJZDogJGJ0bi5kYXRhKCdob29rLWlkJyksXG4gICAgICAgICAgbW9kdWxlSWQ6ICRidG4uZGF0YSgnbW9kdWxlLWlkJyksXG4gICAgICAgICAgd2F5OiAkYnRuLmRhdGEoJ3dheScpLFxuICAgICAgICAgIHBvc2l0aW9uczogW10sXG4gICAgICAgIH0sXG4gICAgICAgICRidG4uY2xvc2VzdCgndWwnKVxuICAgICAgKTtcblxuICAgICAgcmV0dXJuIGZhbHNlO1xuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIEhhbmRsZSBzb3J0YWJsZSBldmVudHNcbiAgICovXG4gIGhhbmRsZVNvcnRhYmxlKCkge1xuICAgIGNvbnN0IHNlbGYgPSB0aGlzO1xuXG4gICAgJCgnLnNvcnRhYmxlJykuc29ydGFibGUoe1xuICAgICAgZm9yY2VQbGFjZWhvbGRlclNpemU6IHRydWUsXG4gICAgICBzdGFydDogZnVuY3Rpb24oZSwgdWkpIHtcbiAgICAgICAgJCh0aGlzKS5kYXRhKCdwcmV2aW91cy1pbmRleCcsIHVpLml0ZW0uaW5kZXgoKSk7XG4gICAgICB9LFxuICAgICAgdXBkYXRlOiBmdW5jdGlvbigkZXZlbnQsIHVpKSB7XG4gICAgICAgIGNvbnN0IFsgaG9va0lkLCBtb2R1bGVJZCBdID0gdWkuaXRlbS5hdHRyKCdpZCcpLnNwbGl0KCdfJyk7XG5cbiAgICAgICAgY29uc3QgJGRhdGEgPSB7XG4gICAgICAgICAgaG9va0lkLFxuICAgICAgICAgIG1vZHVsZUlkLFxuICAgICAgICAgIHdheTogKCQodGhpcykuZGF0YSgncHJldmlvdXMtaW5kZXgnKSA8IHVpLml0ZW0uaW5kZXgoKSkgPyAxIDogMCxcbiAgICAgICAgICBwb3NpdGlvbnM6IFtdLFxuICAgICAgICB9O1xuXG4gICAgICAgIHNlbGYudXBkYXRlUG9zaXRpb25zKFxuICAgICAgICAgICRkYXRhLFxuICAgICAgICAgICQoJGV2ZW50LnRhcmdldClcbiAgICAgICAgKTtcbiAgICAgIH0sXG4gICAgfSk7XG4gIH1cblxuICB1cGRhdGVQb3NpdGlvbnMoJGRhdGEsICRsaXN0KSB7XG4gICAgY29uc3Qgc2VsZiA9IHRoaXM7XG4gICAgJC5lYWNoKCRsaXN0LmNoaWxkcmVuKCksIGZ1bmN0aW9uKGluZGV4LCBlbGVtZW50KSB7XG4gICAgICAkZGF0YS5wb3NpdGlvbnMucHVzaCgkKGVsZW1lbnQpLmF0dHIoJ2lkJykpO1xuICAgIH0pO1xuXG4gICAgJC5hamF4KHtcbiAgICAgIHR5cGU6ICdQT1NUJyxcbiAgICAgIGhlYWRlcnM6IHsnY2FjaGUtY29udHJvbCc6ICduby1jYWNoZSd9LFxuICAgICAgdXJsOiBzZWxmLiRtb2R1bGVQb3NpdGlvbnNGb3JtLmRhdGEoJ3VwZGF0ZS11cmwnKSxcbiAgICAgIGRhdGE6ICRkYXRhLFxuICAgICAgc3VjY2VzczogKCkgPT4ge1xuICAgICAgICBsZXQgc3RhcnQgPSAwO1xuICAgICAgICAkLmVhY2goJGxpc3QuY2hpbGRyZW4oKSwgZnVuY3Rpb24oaW5kZXgsIGVsZW1lbnQpIHtcbiAgICAgICAgICBjb25zb2xlLmxvZygkKGVsZW1lbnQpLmZpbmQoJy5pbmRleC1wb3NpdGlvbicpKTtcbiAgICAgICAgICAkKGVsZW1lbnQpLmZpbmQoJy5pbmRleC1wb3NpdGlvbicpLmh0bWwoKytzdGFydCk7XG4gICAgICAgIH0pO1xuXG4gICAgICAgIHdpbmRvdy5zaG93U3VjY2Vzc01lc3NhZ2Uod2luZG93LnVwZGF0ZV9zdWNjZXNzX21zZyk7XG4gICAgICB9XG4gICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogRmlsdGVyIGhvb2tzIC8gbW9kdWxlcyBzZWFyY2ggYW5kIGV2ZXJ5dGhpbmdcbiAgICogYWJvdXQgaG9va3MgcG9zaXRpb25zLlxuICAgKi9cbiAgbW9kdWxlc1Bvc2l0aW9uRmlsdGVySG9va3MoKSB7XG4gICAgY29uc3Qgc2VsZiA9IHRoaXM7XG4gICAgY29uc3QgJGhvb2tOYW1lID0gc2VsZi4kaG9va1NlYXJjaC52YWwoKTtcbiAgICBjb25zdCAkbW9kdWxlSWQgPSBzZWxmLiRzaG93TW9kdWxlcy52YWwoKTtcbiAgICBjb25zdCAkcmVnZXggPSBuZXcgUmVnRXhwKGAoJHskaG9va05hbWV9KWAsICdnaScpO1xuXG4gICAgZm9yIChsZXQgJGlkID0gMDsgJGlkIDwgc2VsZi4kaG9va3NMaXN0Lmxlbmd0aDsgJGlkKyspIHtcbiAgICAgIHNlbGYuJGhvb2tzTGlzdFskaWRdLmNvbnRhaW5lci50b2dnbGUoJGhvb2tOYW1lID09PSAnJyAmJiAkbW9kdWxlSWQgPT09ICdhbGwnKTtcbiAgICAgIHNlbGYuJGhvb2tzTGlzdFskaWRdLmVsZW1lbnQuaHRtbChzZWxmLiRob29rc0xpc3RbJGlkXS50aXRsZSk7XG4gICAgICBzZWxmLiRob29rc0xpc3RbJGlkXS5jb250YWluZXIuZmluZCgnLm1vZHVsZS1pdGVtJykucmVtb3ZlQ2xhc3MoJ2hpZ2hsaWdodCcpO1xuICAgIH1cblxuICAgIC8vIEhhdmUgc2VsZWN0IGEgaG9vayBuYW1lIG9yIGEgbW9kdWxlIGlkXG4gICAgaWYgKCRob29rTmFtZSAhPT0gJycgfHwgJG1vZHVsZUlkICE9PSAnYWxsJykge1xuICAgICAgLy8gUHJlcGFyZSBzZXQgb2YgbWF0Y2hlZCBlbGVtZW50c1xuICAgICAgbGV0ICRob29rc1RvU2hvd0Zyb21Nb2R1bGUgPSAkKCk7XG4gICAgICBsZXQgJGhvb2tzVG9TaG93RnJvbUhvb2tOYW1lID0gJCgpO1xuICAgICAgbGV0ICRjdXJyZW50SG9va3M7XG4gICAgICBsZXQgJHN0YXJ0O1xuXG4gICAgICBmb3IgKGxldCAkaWQgPSAwOyAkaWQgPCBzZWxmLiRob29rc0xpc3QubGVuZ3RoOyAkaWQrKykge1xuICAgICAgICAvLyBQcmVwYXJlIGhpZ2hsaWdodCB3aGVuIG9uZSBtb2R1bGUgaXMgc2VsZWN0ZWRcbiAgICAgICAgaWYgKCRtb2R1bGVJZCAhPT0gJ2FsbCcpIHtcbiAgICAgICAgICAkY3VycmVudEhvb2tzID0gc2VsZi4kaG9va3NMaXN0WyRpZF0uY29udGFpbmVyLmZpbmQoYC5tb2R1bGUtcG9zaXRpb24tJHskbW9kdWxlSWR9YCk7XG4gICAgICAgICAgaWYgKCRjdXJyZW50SG9va3MubGVuZ3RoID4gMCkge1xuICAgICAgICAgICAgJGhvb2tzVG9TaG93RnJvbU1vZHVsZSA9ICRob29rc1RvU2hvd0Zyb21Nb2R1bGUuYWRkKHNlbGYuJGhvb2tzTGlzdFskaWRdLmNvbnRhaW5lcik7XG4gICAgICAgICAgICAkY3VycmVudEhvb2tzLmFkZENsYXNzKCdoaWdobGlnaHQnKTtcbiAgICAgICAgICB9XG4gICAgICAgIH1cblxuICAgICAgICAvLyBQcmVwYXJlIGhpZ2hsaWdodCB3aGVuIHRoZXJlIGlzIGEgaG9vayBuYW1lXG4gICAgICAgIGlmICgkaG9va05hbWUgIT09ICcnKSB7XG4gICAgICAgICAgJHN0YXJ0ID0gc2VsZi4kaG9va3NMaXN0WyRpZF0udGl0bGUudG9Mb3dlckNhc2UoKS5zZWFyY2goJGhvb2tOYW1lLnRvTG93ZXJDYXNlKCkpO1xuICAgICAgICAgIGlmICgkc3RhcnQgIT09IC0xKSB7XG4gICAgICAgICAgICAkaG9va3NUb1Nob3dGcm9tSG9va05hbWUgPSAkaG9va3NUb1Nob3dGcm9tSG9va05hbWUuYWRkKHNlbGYuJGhvb2tzTGlzdFskaWRdLmNvbnRhaW5lcik7XG4gICAgICAgICAgICBzZWxmLiRob29rc0xpc3RbJGlkXS5lbGVtZW50Lmh0bWwoXG4gICAgICAgICAgICAgIHNlbGYuJGhvb2tzTGlzdFskaWRdLnRpdGxlLnJlcGxhY2UoXG4gICAgICAgICAgICAgICAgJHJlZ2V4LFxuICAgICAgICAgICAgICAgICc8c3BhbiBjbGFzcz1cImhpZ2hsaWdodFwiPiQxPC9zcGFuPidcbiAgICAgICAgICAgICAgKVxuICAgICAgICAgICAgKTtcbiAgICAgICAgICB9XG4gICAgICAgIH1cbiAgICAgIH1cblxuICAgICAgLy8gTm90aGluZyBzZWxlY3RlZFxuICAgICAgaWYgKCRtb2R1bGVJZCA9PT0gJ2FsbCcgJiYgJGhvb2tOYW1lICE9PSAnJykge1xuICAgICAgICAkaG9va3NUb1Nob3dGcm9tSG9va05hbWUuc2hvdygpO1xuICAgICAgfSBlbHNlIGlmICgkaG9va05hbWUgPT09ICcnICYmICRtb2R1bGVJZCAhPT0gJ2FsbCcpIHsgLy8gSGF2ZSBubyBob29rIGJ1ZyBoYXZlIGEgbW9kdWxlXG4gICAgICAgICRob29rc1RvU2hvd0Zyb21Nb2R1bGUuc2hvdygpO1xuICAgICAgfSBlbHNlIHsgLy8gQm90aCBzZWxlY3RlZFxuICAgICAgICAkaG9va3NUb1Nob3dGcm9tSG9va05hbWUuZmlsdGVyKCRob29rc1RvU2hvd0Zyb21Nb2R1bGUpLnNob3coKTtcbiAgICAgIH1cbiAgICB9XG5cbiAgICBpZiAoIXNlbGYuJGhvb2tQb3NpdGlvbi5wcm9wKCdjaGVja2VkJykpIHtcbiAgICAgIGZvciAobGV0ICRpZCA9IDA7ICRpZCA8IHNlbGYuJGhvb2tzTGlzdC5sZW5ndGg7ICRpZCsrKSB7XG4gICAgICAgIGlmIChzZWxmLiRob29rc0xpc3RbJGlkXS5jb250YWluZXIuaXMoJy5ob29rLXBvc2l0aW9uJykpIHtcbiAgICAgICAgICBzZWxmLiRob29rc0xpc3RbJGlkXS5jb250YWluZXIuaGlkZSgpO1xuICAgICAgICB9XG4gICAgICB9XG4gICAgfVxuICB9O1xufVxuXG5leHBvcnQgZGVmYXVsdCBQb3NpdGlvbnNMaXN0SGFuZGxlcjtcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL3BhZ2VzL2ltcHJvdmUvZGVzaWduX3Bvc2l0aW9ucy9wb3NpdGlvbnMtbGlzdC1oYW5kbGVyLmpzIiwiLyoqXG4gKiAyMDA3LTIwMTkgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLnR4dC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL3d3dy5wcmVzdGFzaG9wLmNvbSBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IDIwMDctMjAxOSBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKi9cblxuaW1wb3J0IFBvc2l0aW9uc0xpc3RIYW5kbGVyIGZyb20gJy4vcG9zaXRpb25zLWxpc3QtaGFuZGxlcic7XG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuJCgoKSA9PiB7XG4gIG5ldyBQb3NpdGlvbnNMaXN0SGFuZGxlcigpO1xufSk7XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9wYWdlcy9pbXByb3ZlL2Rlc2lnbl9wb3NpdGlvbnMvaW5kZXguanMiXSwic291cmNlUm9vdCI6IiJ9