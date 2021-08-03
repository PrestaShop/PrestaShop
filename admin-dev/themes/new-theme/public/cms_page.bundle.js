window["cms_page"] =
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
/******/ 	return __webpack_require__(__webpack_require__.s = 485);
/******/ })
/************************************************************************/
/******/ ({

/***/ 0:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


exports.__esModule = true;

exports.default = function (instance, Constructor) {
  if (!(instance instanceof Constructor)) {
    throw new TypeError("Cannot call a class as a function");
  }
};

/***/ }),

/***/ 1:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


exports.__esModule = true;

var _defineProperty = __webpack_require__(19);

var _defineProperty2 = _interopRequireDefault(_defineProperty);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

exports.default = function () {
  function defineProperties(target, props) {
    for (var i = 0; i < props.length; i++) {
      var descriptor = props[i];
      descriptor.enumerable = descriptor.enumerable || false;
      descriptor.configurable = true;
      if ("value" in descriptor) descriptor.writable = true;
      (0, _defineProperty2.default)(target, descriptor.key, descriptor);
    }
  }

  return function (Constructor, protoProps, staticProps) {
    if (protoProps) defineProperties(Constructor.prototype, protoProps);
    if (staticProps) defineProperties(Constructor, staticProps);
    return Constructor;
  };
}();

/***/ }),

/***/ 10:
/***/ (function(module, exports, __webpack_require__) {

var dP         = __webpack_require__(6)
  , createDesc = __webpack_require__(12);
module.exports = __webpack_require__(2) ? function(object, key, value){
  return dP.f(object, key, createDesc(1, value));
} : function(object, key, value){
  object[key] = value;
  return object;
};

/***/ }),

/***/ 11:
/***/ (function(module, exports, __webpack_require__) {

var isObject = __webpack_require__(4);
module.exports = function(it){
  if(!isObject(it))throw TypeError(it + ' is not an object!');
  return it;
};

/***/ }),

/***/ 12:
/***/ (function(module, exports) {

module.exports = function(bitmap, value){
  return {
    enumerable  : !(bitmap & 1),
    configurable: !(bitmap & 2),
    writable    : !(bitmap & 4),
    value       : value
  };
};

/***/ }),

/***/ 13:
/***/ (function(module, exports, __webpack_require__) {

// optional / simple context binding
var aFunction = __webpack_require__(18);
module.exports = function(fn, that, length){
  aFunction(fn);
  if(that === undefined)return fn;
  switch(length){
    case 1: return function(a){
      return fn.call(that, a);
    };
    case 2: return function(a, b){
      return fn.call(that, a, b);
    };
    case 3: return function(a, b, c){
      return fn.call(that, a, b, c);
    };
  }
  return function(/* ...args */){
    return fn.apply(that, arguments);
  };
};

/***/ }),

/***/ 14:
/***/ (function(module, exports, __webpack_require__) {

// 7.1.1 ToPrimitive(input [, PreferredType])
var isObject = __webpack_require__(4);
// instead of the ES6 spec version, we didn't implement @@toPrimitive case
// and the second argument - flag - preferred type is a string
module.exports = function(it, S){
  if(!isObject(it))return it;
  var fn, val;
  if(S && typeof (fn = it.toString) == 'function' && !isObject(val = fn.call(it)))return val;
  if(typeof (fn = it.valueOf) == 'function' && !isObject(val = fn.call(it)))return val;
  if(!S && typeof (fn = it.toString) == 'function' && !isObject(val = fn.call(it)))return val;
  throw TypeError("Can't convert object to primitive value");
};

/***/ }),

/***/ 149:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

var _createClass2 = __webpack_require__(1);

var _createClass3 = _interopRequireDefault(_createClass2);

var _jqueryTablednd = __webpack_require__(158);

var _jqueryTablednd2 = _interopRequireDefault(_jqueryTablednd);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

var $ = window.$;

/**
 * Class PositionExtension extends Grid with reorderable positions
 */
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

var PositionExtension = function () {
  function PositionExtension() {
    var _this = this;

    (0, _classCallCheck3.default)(this, PositionExtension);

    return {
      extend: function extend(grid) {
        return _this.extend(grid);
      }
    };
  }

  /**
   * Extend grid
   *
   * @param {Grid} grid
   */


  (0, _createClass3.default)(PositionExtension, [{
    key: 'extend',
    value: function extend(grid) {
      var _this2 = this;

      this.grid = grid;
      this._addIdsToGridTableRows();
      grid.getContainer().find('.js-grid-table').tableDnD({
        onDragClass: 'position-row-while-drag',
        dragHandle: '.js-drag-handle',
        onDrop: function onDrop(table, row) {
          return _this2._handlePositionChange(row);
        }
      });
      grid.getContainer().find('.js-drag-handle').hover(function () {
        $(this).closest('tr').addClass('hover');
      }, function () {
        $(this).closest('tr').removeClass('hover');
      });
    }

    /**
     * When position is changed handle update
     *
     * @param {HTMLElement} row
     *
     * @private
     */

  }, {
    key: '_handlePositionChange',
    value: function _handlePositionChange(row) {
      var $rowPositionContainer = $(row).find('.js-' + this.grid.getId() + '-position:first');
      var updateUrl = $rowPositionContainer.data('update-url');
      var method = $rowPositionContainer.data('update-method');
      var paginationOffset = parseInt($rowPositionContainer.data('pagination-offset'), 10);
      var positions = this._getRowsPositions(paginationOffset);
      var params = { positions: positions };

      this._updatePosition(updateUrl, params, method);
    }

    /**
     * Returns the current table positions
     * @returns {Array}
     * @private
     */

  }, {
    key: '_getRowsPositions',
    value: function _getRowsPositions(paginationOffset) {
      var tableData = JSON.parse($.tableDnD.jsonize());
      var rowsData = tableData[this.grid.getId() + '_grid_table'];
      var regex = /^row_(\d+)_(\d+)$/;

      var rowsNb = rowsData.length;
      var positions = [];
      var rowData = void 0,
          i = void 0;
      for (i = 0; i < rowsNb; ++i) {
        rowData = regex.exec(rowsData[i]);
        positions.push({
          rowId: rowData[1],
          newPosition: paginationOffset + i,
          oldPosition: parseInt(rowData[2], 10)
        });
      }

      return positions;
    }

    /**
     * Add ID's to Grid table rows to make tableDnD.onDrop() function work.
     *
     * @private
     */

  }, {
    key: '_addIdsToGridTableRows',
    value: function _addIdsToGridTableRows() {
      this.grid.getContainer().find('.js-grid-table .js-' + this.grid.getId() + '-position').each(function (index, positionWrapper) {
        var $positionWrapper = $(positionWrapper);
        var rowId = $positionWrapper.data('id');
        var position = $positionWrapper.data('position');
        var id = 'row_' + rowId + '_' + position;
        $positionWrapper.closest('tr').attr('id', id);
        $positionWrapper.closest('td').addClass('js-drag-handle');
      });
    }

    /**
     * Process rows positions update
     *
     * @param {String} url
     * @param {Object} params
     * @param {String} method
     *
     * @private
     */

  }, {
    key: '_updatePosition',
    value: function _updatePosition(url, params, method) {
      var isGetOrPostMethod = ['GET', 'POST'].includes(method);

      var $form = $('<form>', {
        'action': url,
        'method': isGetOrPostMethod ? method : 'POST'
      }).appendTo('body');

      var positionsNb = params.positions.length;
      var position = void 0;
      for (var i = 0; i < positionsNb; ++i) {
        position = params.positions[i];
        $form.append($('<input>', {
          'type': 'hidden',
          'name': 'positions[' + i + '][rowId]',
          'value': position.rowId
        }), $('<input>', {
          'type': 'hidden',
          'name': 'positions[' + i + '][oldPosition]',
          'value': position.oldPosition
        }), $('<input>', {
          'type': 'hidden',
          'name': 'positions[' + i + '][newPosition]',
          'value': position.newPosition
        }));
      }

      // This _method param is used by Symfony to simulate DELETE and PUT methods
      if (!isGetOrPostMethod) {
        $form.append($('<input>', {
          'type': 'hidden',
          'name': '_method',
          'value': method
        }));
      }

      $form.submit();
    }
  }]);
  return PositionExtension;
}();

exports.default = PositionExtension;

/***/ }),

/***/ 150:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
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
 * Component which allows to copy regular text to url friendly text
 *
 * Usage example in template:
 *
 * <input name="source-input" class="js-link-rewrite-copier-source"> // The original text will be taken from this element
 * <input name="destination-input" class="js-link-rewrite-copier-destination"> // Modified text will be added to this input
 *
 * in javascript:
 *
 * textToLinkRewriteCopier({
 *   sourceElementSelector: '.js-link-rewrite-copier-source'
 *   destinationElementSelector: '.js-link-rewrite-copier-destination',
 * });
 *
 * If the source-input has value "test name" the link rewrite value will be "test-name".
 * If the source-input has value "test name #$" link rewrite will be "test-name-" since #$ are un allowed characters in url.
 *
 * You can also pass additional options to change the event name, or encoding format:
 *
 * textToLinkRewriteCopier({
 *   sourceElementSelector: '.js-link-rewrite-copier-source'
 *   destinationElementSelector: '.js-link-rewrite-copier-destination',
 *   options: {
 *     eventName: 'change', // default is 'input'
 *   }
 * });
 *
 */
var textToLinkRewriteCopier = function textToLinkRewriteCopier(_ref) {
  var sourceElementSelector = _ref.sourceElementSelector,
      destinationElementSelector = _ref.destinationElementSelector,
      _ref$options = _ref.options,
      options = _ref$options === undefined ? { eventName: 'input' } : _ref$options;

  $(document).on(options.eventName, '' + sourceElementSelector, function (event) {
    $(destinationElementSelector).val(str2url($(event.currentTarget).val(), 'UTF-8'));
  });
};

exports.default = textToLinkRewriteCopier;

/***/ }),

/***/ 158:
/***/ (function(module, exports, __webpack_require__) {

/* WEBPACK VAR INJECTION */(function(jQuery) {/*! jquery.tablednd.js 30-12-2017 */
!function(a,b,c,d){var e="touchstart mousedown",f="touchmove mousemove",g="touchend mouseup";a(c).ready(function(){function b(a){for(var b={},c=a.match(/([^;:]+)/g)||[];c.length;)b[c.shift()]=c.shift().trim();return b}a("table").each(function(){"dnd"===a(this).data("table")&&a(this).tableDnD({onDragStyle:a(this).data("ondragstyle")&&b(a(this).data("ondragstyle"))||null,onDropStyle:a(this).data("ondropstyle")&&b(a(this).data("ondropstyle"))||null,onDragClass:a(this).data("ondragclass")===d&&"tDnD_whileDrag"||a(this).data("ondragclass"),onDrop:a(this).data("ondrop")&&new Function("table","row",a(this).data("ondrop")),onDragStart:a(this).data("ondragstart")&&new Function("table","row",a(this).data("ondragstart")),onDragStop:a(this).data("ondragstop")&&new Function("table","row",a(this).data("ondragstop")),scrollAmount:a(this).data("scrollamount")||5,sensitivity:a(this).data("sensitivity")||10,hierarchyLevel:a(this).data("hierarchylevel")||0,indentArtifact:a(this).data("indentartifact")||'<div class="indent">&nbsp;</div>',autoWidthAdjust:a(this).data("autowidthadjust")||!0,autoCleanRelations:a(this).data("autocleanrelations")||!0,jsonPretifySeparator:a(this).data("jsonpretifyseparator")||"\t",serializeRegexp:a(this).data("serializeregexp")&&new RegExp(a(this).data("serializeregexp"))||/[^\-]*$/,serializeParamName:a(this).data("serializeparamname")||!1,dragHandle:a(this).data("draghandle")||null})})}),jQuery.tableDnD={currentTable:null,dragObject:null,mouseOffset:null,oldX:0,oldY:0,build:function(b){return this.each(function(){this.tableDnDConfig=a.extend({onDragStyle:null,onDropStyle:null,onDragClass:"tDnD_whileDrag",onDrop:null,onDragStart:null,onDragStop:null,scrollAmount:5,sensitivity:10,hierarchyLevel:0,indentArtifact:'<div class="indent">&nbsp;</div>',autoWidthAdjust:!0,autoCleanRelations:!0,jsonPretifySeparator:"\t",serializeRegexp:/[^\-]*$/,serializeParamName:!1,dragHandle:null},b||{}),a.tableDnD.makeDraggable(this),this.tableDnDConfig.hierarchyLevel&&a.tableDnD.makeIndented(this)}),this},makeIndented:function(b){var c,d,e=b.tableDnDConfig,f=b.rows,g=a(f).first().find("td:first")[0],h=0,i=0;if(a(b).hasClass("indtd"))return null;d=a(b).addClass("indtd").attr("style"),a(b).css({whiteSpace:"nowrap"});for(var j=0;j<f.length;j++)i<a(f[j]).find("td:first").text().length&&(i=a(f[j]).find("td:first").text().length,c=j);for(a(g).css({width:"auto"}),j=0;j<e.hierarchyLevel;j++)a(f[c]).find("td:first").prepend(e.indentArtifact);for(g&&a(g).css({width:g.offsetWidth}),d&&a(b).css(d),j=0;j<e.hierarchyLevel;j++)a(f[c]).find("td:first").children(":first").remove();return e.hierarchyLevel&&a(f).each(function(){(h=a(this).data("level")||0)<=e.hierarchyLevel&&a(this).data("level",h)||a(this).data("level",0);for(var b=0;b<a(this).data("level");b++)a(this).find("td:first").prepend(e.indentArtifact)}),this},makeDraggable:function(b){var c=b.tableDnDConfig;c.dragHandle&&a(c.dragHandle,b).each(function(){a(this).bind(e,function(d){return a.tableDnD.initialiseDrag(a(this).parents("tr")[0],b,this,d,c),!1})})||a(b.rows).each(function(){a(this).hasClass("nodrag")?a(this).css("cursor",""):a(this).bind(e,function(d){if("TD"===d.target.tagName)return a.tableDnD.initialiseDrag(this,b,this,d,c),!1}).css("cursor","move")})},currentOrder:function(){var b=this.currentTable.rows;return a.map(b,function(b){return(a(b).data("level")+b.id).replace(/\s/g,"")}).join("")},initialiseDrag:function(b,d,e,h,i){this.dragObject=b,this.currentTable=d,this.mouseOffset=this.getMouseOffset(e,h),this.originalOrder=this.currentOrder(),a(c).bind(f,this.mousemove).bind(g,this.mouseup),i.onDragStart&&i.onDragStart(d,e)},updateTables:function(){this.each(function(){this.tableDnDConfig&&a.tableDnD.makeDraggable(this)})},mouseCoords:function(a){return a.originalEvent.changedTouches?{x:a.originalEvent.changedTouches[0].clientX,y:a.originalEvent.changedTouches[0].clientY}:a.pageX||a.pageY?{x:a.pageX,y:a.pageY}:{x:a.clientX+c.body.scrollLeft-c.body.clientLeft,y:a.clientY+c.body.scrollTop-c.body.clientTop}},getMouseOffset:function(a,c){var d,e;return c=c||b.event,e=this.getPosition(a),d=this.mouseCoords(c),{x:d.x-e.x,y:d.y-e.y}},getPosition:function(a){var b=0,c=0;for(0===a.offsetHeight&&(a=a.firstChild);a.offsetParent;)b+=a.offsetLeft,c+=a.offsetTop,a=a.offsetParent;return b+=a.offsetLeft,c+=a.offsetTop,{x:b,y:c}},autoScroll:function(a){var d=this.currentTable.tableDnDConfig,e=b.pageYOffset,f=b.innerHeight?b.innerHeight:c.documentElement.clientHeight?c.documentElement.clientHeight:c.body.clientHeight;c.all&&(void 0!==c.compatMode&&"BackCompat"!==c.compatMode?e=c.documentElement.scrollTop:void 0!==c.body&&(e=c.body.scrollTop)),a.y-e<d.scrollAmount&&b.scrollBy(0,-d.scrollAmount)||f-(a.y-e)<d.scrollAmount&&b.scrollBy(0,d.scrollAmount)},moveVerticle:function(a,b){0!==a.vertical&&b&&this.dragObject!==b&&this.dragObject.parentNode===b.parentNode&&(0>a.vertical&&this.dragObject.parentNode.insertBefore(this.dragObject,b.nextSibling)||0<a.vertical&&this.dragObject.parentNode.insertBefore(this.dragObject,b))},moveHorizontal:function(b,c){var d,e=this.currentTable.tableDnDConfig;if(!e.hierarchyLevel||0===b.horizontal||!c||this.dragObject!==c)return null;d=a(c).data("level"),0<b.horizontal&&d>0&&a(c).find("td:first").children(":first").remove()&&a(c).data("level",--d),0>b.horizontal&&d<e.hierarchyLevel&&a(c).prev().data("level")>=d&&a(c).children(":first").prepend(e.indentArtifact)&&a(c).data("level",++d)},mousemove:function(b){var c,d,e,f,g,h=a(a.tableDnD.dragObject),i=a.tableDnD.currentTable.tableDnDConfig;return b&&b.preventDefault(),!!a.tableDnD.dragObject&&("touchmove"===b.type&&event.preventDefault(),i.onDragClass&&h.addClass(i.onDragClass)||h.css(i.onDragStyle),d=a.tableDnD.mouseCoords(b),f=d.x-a.tableDnD.mouseOffset.x,g=d.y-a.tableDnD.mouseOffset.y,a.tableDnD.autoScroll(d),c=a.tableDnD.findDropTargetRow(h,g),e=a.tableDnD.findDragDirection(f,g),a.tableDnD.moveVerticle(e,c),a.tableDnD.moveHorizontal(e,c),!1)},findDragDirection:function(a,b){var c=this.currentTable.tableDnDConfig.sensitivity,d=this.oldX,e=this.oldY,f=d-c,g=d+c,h=e-c,i=e+c,j={horizontal:a>=f&&a<=g?0:a>d?-1:1,vertical:b>=h&&b<=i?0:b>e?-1:1};return 0!==j.horizontal&&(this.oldX=a),0!==j.vertical&&(this.oldY=b),j},findDropTargetRow:function(b,c){for(var d=0,e=this.currentTable.rows,f=this.currentTable.tableDnDConfig,g=0,h=null,i=0;i<e.length;i++)if(h=e[i],g=this.getPosition(h).y,d=parseInt(h.offsetHeight)/2,0===h.offsetHeight&&(g=this.getPosition(h.firstChild).y,d=parseInt(h.firstChild.offsetHeight)/2),c>g-d&&c<g+d)return b.is(h)||f.onAllowDrop&&!f.onAllowDrop(b,h)||a(h).hasClass("nodrop")?null:h;return null},processMouseup:function(){if(!this.currentTable||!this.dragObject)return null;var b=this.currentTable.tableDnDConfig,d=this.dragObject,e=0,h=0;a(c).unbind(f,this.mousemove).unbind(g,this.mouseup),b.hierarchyLevel&&b.autoCleanRelations&&a(this.currentTable.rows).first().find("td:first").children().each(function(){(h=a(this).parents("tr:first").data("level"))&&a(this).parents("tr:first").data("level",--h)&&a(this).remove()})&&b.hierarchyLevel>1&&a(this.currentTable.rows).each(function(){if((h=a(this).data("level"))>1)for(e=a(this).prev().data("level");h>e+1;)a(this).find("td:first").children(":first").remove(),a(this).data("level",--h)}),b.onDragClass&&a(d).removeClass(b.onDragClass)||a(d).css(b.onDropStyle),this.dragObject=null,b.onDrop&&this.originalOrder!==this.currentOrder()&&a(d).hide().fadeIn("fast")&&b.onDrop(this.currentTable,d),b.onDragStop&&b.onDragStop(this.currentTable,d),this.currentTable=null},mouseup:function(b){return b&&b.preventDefault(),a.tableDnD.processMouseup(),!1},jsonize:function(a){var b=this.currentTable;return a?JSON.stringify(this.tableData(b),null,b.tableDnDConfig.jsonPretifySeparator):JSON.stringify(this.tableData(b))},serialize:function(){return a.param(this.tableData(this.currentTable))},serializeTable:function(a){for(var b="",c=a.tableDnDConfig.serializeParamName||a.id,d=a.rows,e=0;e<d.length;e++){b.length>0&&(b+="&");var f=d[e].id;f&&a.tableDnDConfig&&a.tableDnDConfig.serializeRegexp&&(f=f.match(a.tableDnDConfig.serializeRegexp)[0],b+=c+"[]="+f)}return b},serializeTables:function(){var b=[];return a("table").each(function(){this.id&&b.push(a.param(a.tableDnD.tableData(this)))}),b.join("&")},tableData:function(b){var c,d,e,f,g=b.tableDnDConfig,h=[],i=0,j=0,k=null,l={};if(b||(b=this.currentTable),!b||!b.rows||!b.rows.length)return{error:{code:500,message:"Not a valid table."}};if(!b.id&&!g.serializeParamName)return{error:{code:500,message:"No serializable unique id provided."}};f=g.autoCleanRelations&&b.rows||a.makeArray(b.rows),d=g.serializeParamName||b.id,e=d,c=function(a){return a&&g&&g.serializeRegexp?a.match(g.serializeRegexp)[0]:a},l[e]=[],!g.autoCleanRelations&&a(f[0]).data("level")&&f.unshift({id:"undefined"});for(var m=0;m<f.length;m++)if(g.hierarchyLevel){if(0===(j=a(f[m]).data("level")||0))e=d,h=[];else if(j>i)h.push([e,i]),e=c(f[m-1].id);else if(j<i)for(var n=0;n<h.length;n++)h[n][1]===j&&(e=h[n][0]),h[n][1]>=i&&(h[n][1]=0);i=j,a.isArray(l[e])||(l[e]=[]),k=c(f[m].id),k&&l[e].push(k)}else(k=c(f[m].id))&&l[e].push(k);return l}},jQuery.fn.extend({tableDnD:a.tableDnD.build,tableDnDUpdate:a.tableDnD.updateTables,tableDnDSerialize:a.proxy(a.tableDnD.serialize,a.tableDnD),tableDnDSerializeAll:a.tableDnD.serializeTables,tableDnDData:a.proxy(a.tableDnD.tableData,a.tableDnD)})}(jQuery,window,window.document);
/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(42)))

/***/ }),

/***/ 16:
/***/ (function(module, exports, __webpack_require__) {

var isObject = __webpack_require__(4)
  , document = __webpack_require__(5).document
  // in old IE typeof document.createElement is 'object'
  , is = isObject(document) && isObject(document.createElement);
module.exports = function(it){
  return is ? document.createElement(it) : {};
};

/***/ }),

/***/ 17:
/***/ (function(module, exports, __webpack_require__) {

module.exports = !__webpack_require__(2) && !__webpack_require__(7)(function(){
  return Object.defineProperty(__webpack_require__(16)('div'), 'a', {get: function(){ return 7; }}).a != 7;
});

/***/ }),

/***/ 18:
/***/ (function(module, exports) {

module.exports = function(it){
  if(typeof it != 'function')throw TypeError(it + ' is not a function!');
  return it;
};

/***/ }),

/***/ 19:
/***/ (function(module, exports, __webpack_require__) {

module.exports = { "default": __webpack_require__(20), __esModule: true };

/***/ }),

/***/ 2:
/***/ (function(module, exports, __webpack_require__) {

// Thank's IE8 for his funny defineProperty
module.exports = !__webpack_require__(7)(function(){
  return Object.defineProperty({}, 'a', {get: function(){ return 7; }}).a != 7;
});

/***/ }),

/***/ 20:
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(21);
var $Object = __webpack_require__(3).Object;
module.exports = function defineProperty(it, key, desc){
  return $Object.defineProperty(it, key, desc);
};

/***/ }),

/***/ 21:
/***/ (function(module, exports, __webpack_require__) {

var $export = __webpack_require__(8);
// 19.1.2.4 / 15.2.3.6 Object.defineProperty(O, P, Attributes)
$export($export.S + $export.F * !__webpack_require__(2), 'Object', {defineProperty: __webpack_require__(6).f});

/***/ }),

/***/ 24:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

var _createClass2 = __webpack_require__(1);

var _createClass3 = _interopRequireDefault(_createClass2);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

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
 * Class is responsible for handling Grid events
 */

var Grid = function () {
  /**
   * Grid id
   *
   * @param {string} id
   */
  function Grid(id) {
    (0, _classCallCheck3.default)(this, Grid);

    this.id = id;
    this.$container = $('#' + this.id + '_grid');
  }

  /**
   * Get grid id
   *
   * @returns {string}
   */


  (0, _createClass3.default)(Grid, [{
    key: 'getId',
    value: function getId() {
      return this.id;
    }

    /**
     * Get grid container
     *
     * @returns {jQuery}
     */

  }, {
    key: 'getContainer',
    value: function getContainer() {
      return this.$container;
    }

    /**
     * Get grid header container
     *
     * @returns {jQuery}
     */

  }, {
    key: 'getHeaderContainer',
    value: function getHeaderContainer() {
      return this.$container.closest('.js-grid-panel').find('.js-grid-header');
    }

    /**
     * Extend grid with external extensions
     *
     * @param {object} extension
     */

  }, {
    key: 'addExtension',
    value: function addExtension(extension) {
      extension.extend(this);
    }
  }]);
  return Grid;
}();

exports.default = Grid;

/***/ }),

/***/ 26:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

var _createClass2 = __webpack_require__(1);

var _createClass3 = _interopRequireDefault(_createClass2);

var _reset_search = __webpack_require__(43);

var _reset_search2 = _interopRequireDefault(_reset_search);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

var $ = window.$;

/**
 * Class FiltersResetExtension extends grid with filters resetting
 */
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

var FiltersResetExtension = function () {
  function FiltersResetExtension() {
    (0, _classCallCheck3.default)(this, FiltersResetExtension);
  }

  (0, _createClass3.default)(FiltersResetExtension, [{
    key: 'extend',


    /**
     * Extend grid
     *
     * @param {Grid} grid
     */
    value: function extend(grid) {
      grid.getContainer().on('click', '.js-reset-search', function (event) {
        (0, _reset_search2.default)($(event.currentTarget).data('url'), $(event.currentTarget).data('redirect'));
      });
    }
  }]);
  return FiltersResetExtension;
}();

exports.default = FiltersResetExtension;

/***/ }),

/***/ 27:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

var _createClass2 = __webpack_require__(1);

var _createClass3 = _interopRequireDefault(_createClass2);

var _tableSorting = __webpack_require__(41);

var _tableSorting2 = _interopRequireDefault(_tableSorting);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

/**
 * Class ReloadListExtension extends grid with "List reload" action
 */
var SortingExtension = function () {
  function SortingExtension() {
    (0, _classCallCheck3.default)(this, SortingExtension);
  }

  (0, _createClass3.default)(SortingExtension, [{
    key: 'extend',

    /**
     * Extend grid
     *
     * @param {Grid} grid
     */
    value: function extend(grid) {
      var $sortableTable = grid.getContainer().find('table.table');

      new _tableSorting2.default($sortableTable).attach();
    }
  }]);
  return SortingExtension;
}(); /**
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

exports.default = SortingExtension;

/***/ }),

/***/ 28:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

var _createClass2 = __webpack_require__(1);

var _createClass3 = _interopRequireDefault(_createClass2);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

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

/**
 * Class ReloadListExtension extends grid with "List reload" action
 */
var ReloadListExtension = function () {
  function ReloadListExtension() {
    (0, _classCallCheck3.default)(this, ReloadListExtension);
  }

  (0, _createClass3.default)(ReloadListExtension, [{
    key: 'extend',

    /**
     * Extend grid
     *
     * @param {Grid} grid
     */
    value: function extend(grid) {
      grid.getHeaderContainer().on('click', '.js-common_refresh_list-grid-action', function () {
        location.reload();
      });
    }
  }]);
  return ReloadListExtension;
}();

exports.default = ReloadListExtension;

/***/ }),

/***/ 29:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

var _createClass2 = __webpack_require__(1);

var _createClass3 = _interopRequireDefault(_createClass2);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

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
 * Class ExportToSqlManagerExtension extends grid with exporting query to SQL Manager
 */

var ExportToSqlManagerExtension = function () {
  function ExportToSqlManagerExtension() {
    (0, _classCallCheck3.default)(this, ExportToSqlManagerExtension);
  }

  (0, _createClass3.default)(ExportToSqlManagerExtension, [{
    key: 'extend',

    /**
     * Extend grid
     *
     * @param {Grid} grid
     */
    value: function extend(grid) {
      var _this = this;

      grid.getHeaderContainer().on('click', '.js-common_show_query-grid-action', function () {
        return _this._onShowSqlQueryClick(grid);
      });
      grid.getHeaderContainer().on('click', '.js-common_export_sql_manager-grid-action', function () {
        return _this._onExportSqlManagerClick(grid);
      });
    }

    /**
     * Invoked when clicking on the "show sql query" toolbar button
     *
     * @param {Grid} grid
     *
     * @private
     */

  }, {
    key: '_onShowSqlQueryClick',
    value: function _onShowSqlQueryClick(grid) {
      var $sqlManagerForm = $('#' + grid.getId() + '_common_show_query_modal_form');
      this._fillExportForm($sqlManagerForm, grid);

      var $modal = $('#' + grid.getId() + '_grid_common_show_query_modal');
      $modal.modal('show');

      $modal.on('click', '.btn-sql-submit', function () {
        return $sqlManagerForm.submit();
      });
    }

    /**
     * Invoked when clicking on the "export to the sql query" toolbar button
     *
     * @param {Grid} grid
     *
     * @private
     */

  }, {
    key: '_onExportSqlManagerClick',
    value: function _onExportSqlManagerClick(grid) {
      var $sqlManagerForm = $('#' + grid.getId() + '_common_show_query_modal_form');

      this._fillExportForm($sqlManagerForm, grid);

      $sqlManagerForm.submit();
    }

    /**
     * Fill export form with SQL and it's name
     *
     * @param {jQuery} $sqlManagerForm
     * @param {Grid} grid
     *
     * @private
     */

  }, {
    key: '_fillExportForm',
    value: function _fillExportForm($sqlManagerForm, grid) {
      var query = grid.getContainer().find('.js-grid-table').data('query');

      $sqlManagerForm.find('textarea[name="sql"]').val(query);
      $sqlManagerForm.find('input[name="name"]').val(this._getNameFromBreadcrumb());
    }

    /**
     * Get export name from page's breadcrumb
     *
     * @return {String}
     *
     * @private
     */

  }, {
    key: '_getNameFromBreadcrumb',
    value: function _getNameFromBreadcrumb() {
      var $breadcrumbs = $('.header-toolbar').find('.breadcrumb-item');
      var name = '';

      $breadcrumbs.each(function (i, item) {
        var $breadcrumb = $(item);

        var breadcrumbTitle = 0 < $breadcrumb.find('a').length ? $breadcrumb.find('a').text() : $breadcrumb.text();

        if (0 < name.length) {
          name = name.concat(' > ');
        }

        name = name.concat(breadcrumbTitle);
      });

      return name;
    }
  }]);
  return ExportToSqlManagerExtension;
}();

exports.default = ExportToSqlManagerExtension;

/***/ }),

/***/ 3:
/***/ (function(module, exports) {

var core = module.exports = {version: '2.4.0'};
if(typeof __e == 'number')__e = core; // eslint-disable-line no-undef

/***/ }),

/***/ 30:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

var _createClass2 = __webpack_require__(1);

var _createClass3 = _interopRequireDefault(_createClass2);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

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

/**
 * Responsible for grid filters search and reset button availability when filter inputs changes.
 */
var FiltersSubmitButtonEnablerExtension = function () {
  function FiltersSubmitButtonEnablerExtension() {
    (0, _classCallCheck3.default)(this, FiltersSubmitButtonEnablerExtension);
  }

  (0, _createClass3.default)(FiltersSubmitButtonEnablerExtension, [{
    key: 'extend',


    /**
     * Extend grid
     *
     * @param {Grid} grid
     */
    value: function extend(grid) {
      var $filtersRow = grid.getContainer().find('.column-filters');
      $filtersRow.find('.grid-search-button').prop('disabled', true);

      $filtersRow.find('input:not(.js-bulk-action-select-all), select').on('input dp.change', function () {
        $filtersRow.find('.grid-search-button').prop('disabled', false);
        $filtersRow.find('.js-grid-reset-button').prop('hidden', false);
      });
    }
  }]);
  return FiltersSubmitButtonEnablerExtension;
}();

exports.default = FiltersSubmitButtonEnablerExtension;

/***/ }),

/***/ 31:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

var _createClass2 = __webpack_require__(1);

var _createClass3 = _interopRequireDefault(_createClass2);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

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
 * Class BulkActionSelectCheckboxExtension
 */

var BulkActionCheckboxExtension = function () {
  function BulkActionCheckboxExtension() {
    (0, _classCallCheck3.default)(this, BulkActionCheckboxExtension);
  }

  (0, _createClass3.default)(BulkActionCheckboxExtension, [{
    key: 'extend',

    /**
     * Extend grid with bulk action checkboxes handling functionality
     *
     * @param {Grid} grid
     */
    value: function extend(grid) {
      this._handleBulkActionCheckboxSelect(grid);
      this._handleBulkActionSelectAllCheckbox(grid);
    }

    /**
     * Handles "Select all" button in the grid
     *
     * @param {Grid} grid
     *
     * @private
     */

  }, {
    key: '_handleBulkActionSelectAllCheckbox',
    value: function _handleBulkActionSelectAllCheckbox(grid) {
      var _this = this;

      grid.getContainer().on('change', '.js-bulk-action-select-all', function (e) {
        var $checkbox = $(e.currentTarget);

        var isChecked = $checkbox.is(':checked');
        if (isChecked) {
          _this._enableBulkActionsBtn(grid);
        } else {
          _this._disableBulkActionsBtn(grid);
        }

        grid.getContainer().find('.js-bulk-action-checkbox').prop('checked', isChecked);
      });
    }

    /**
     * Handles each bulk action checkbox select in the grid
     *
     * @param {Grid} grid
     *
     * @private
     */

  }, {
    key: '_handleBulkActionCheckboxSelect',
    value: function _handleBulkActionCheckboxSelect(grid) {
      var _this2 = this;

      grid.getContainer().on('change', '.js-bulk-action-checkbox', function () {
        var checkedRowsCount = grid.getContainer().find('.js-bulk-action-checkbox:checked').length;

        if (checkedRowsCount > 0) {
          _this2._enableBulkActionsBtn(grid);
        } else {
          _this2._disableBulkActionsBtn(grid);
        }
      });
    }

    /**
     * Enable bulk actions button
     *
     * @param {Grid} grid
     *
     * @private
     */

  }, {
    key: '_enableBulkActionsBtn',
    value: function _enableBulkActionsBtn(grid) {
      grid.getContainer().find('.js-bulk-actions-btn').prop('disabled', false);
    }

    /**
     * Disable bulk actions button
     *
     * @param {Grid} grid
     *
     * @private
     */

  }, {
    key: '_disableBulkActionsBtn',
    value: function _disableBulkActionsBtn(grid) {
      grid.getContainer().find('.js-bulk-actions-btn').prop('disabled', true);
    }
  }]);
  return BulkActionCheckboxExtension;
}();

exports.default = BulkActionCheckboxExtension;

/***/ }),

/***/ 32:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

var _createClass2 = __webpack_require__(1);

var _createClass3 = _interopRequireDefault(_createClass2);

var _modal = __webpack_require__(47);

var _modal2 = _interopRequireDefault(_modal);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

var $ = window.$;

/**
 * Handles submit of grid actions
 */
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

var SubmitBulkActionExtension = function () {
  function SubmitBulkActionExtension() {
    var _this = this;

    (0, _classCallCheck3.default)(this, SubmitBulkActionExtension);

    return {
      extend: function extend(grid) {
        return _this.extend(grid);
      }
    };
  }

  /**
   * Extend grid with bulk action submitting
   *
   * @param {Grid} grid
   */


  (0, _createClass3.default)(SubmitBulkActionExtension, [{
    key: 'extend',
    value: function extend(grid) {
      var _this2 = this;

      grid.getContainer().on('click', '.js-bulk-action-submit-btn', function (event) {
        _this2.submit(event, grid);
      });
    }

    /**
     * Handle bulk action submitting
     *
     * @param {Event} event
     * @param {Grid} grid
     *
     * @private
     */

  }, {
    key: 'submit',
    value: function submit(event, grid) {
      var $submitBtn = $(event.currentTarget);
      var confirmMessage = $submitBtn.data('confirm-message');
      var confirmTitle = $submitBtn.data('confirmTitle');

      if (confirmMessage !== undefined && 0 < confirmMessage.length) {
        if (confirmTitle !== undefined) {
          this.showConfirmModal($submitBtn, grid, confirmMessage, confirmTitle);
        } else if (confirm(confirmMessage)) {
          this.postForm($submitBtn, grid);
        }
      } else {
        this.postForm($submitBtn, grid);
      }
    }

    /**
     * @param {jQuery} $submitBtn
     * @param {Grid} grid
     * @param {string} confirmMessage
     * @param {string} confirmTitle
     */

  }, {
    key: 'showConfirmModal',
    value: function showConfirmModal($submitBtn, grid, confirmMessage, confirmTitle) {
      var _this3 = this;

      var confirmButtonLabel = $submitBtn.data('confirmButtonLabel');
      var closeButtonLabel = $submitBtn.data('closeButtonLabel');
      var confirmButtonClass = $submitBtn.data('confirmButtonClass');

      var modal = new _modal2.default({
        id: grid.getId() + '_grid_confirm_modal',
        confirmTitle: confirmTitle,
        confirmMessage: confirmMessage,
        confirmButtonLabel: confirmButtonLabel,
        closeButtonLabel: closeButtonLabel,
        confirmButtonClass: confirmButtonClass
      }, function () {
        return _this3.postForm($submitBtn, grid);
      });

      modal.show();
    }

    /**
     * @param {jQuery} $submitBtn
     * @param {Grid} grid
     */

  }, {
    key: 'postForm',
    value: function postForm($submitBtn, grid) {
      var $form = $('#' + grid.getId() + '_filter_form');

      $form.attr('action', $submitBtn.data('form-url'));
      $form.attr('method', $submitBtn.data('form-method'));
      $form.submit();
    }
  }]);
  return SubmitBulkActionExtension;
}();

exports.default = SubmitBulkActionExtension;

/***/ }),

/***/ 34:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

var _createClass2 = __webpack_require__(1);

var _createClass3 = _interopRequireDefault(_createClass2);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

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
 * Class LinkRowActionExtension handles link row actions
 */

var LinkRowActionExtension = function () {
  function LinkRowActionExtension() {
    (0, _classCallCheck3.default)(this, LinkRowActionExtension);
  }

  (0, _createClass3.default)(LinkRowActionExtension, [{
    key: 'extend',

    /**
     * Extend grid
     *
     * @param {Grid} grid
     */
    value: function extend(grid) {
      this.initRowLinks(grid);
      this.initConfirmableActions(grid);
    }

    /**
     * Extend grid
     *
     * @param {Grid} grid
     */

  }, {
    key: 'initConfirmableActions',
    value: function initConfirmableActions(grid) {
      grid.getContainer().on('click', '.js-link-row-action', function (event) {
        var confirmMessage = $(event.currentTarget).data('confirm-message');

        if (confirmMessage.length && !confirm(confirmMessage)) {
          event.preventDefault();
        }
      });
    }

    /**
     * Add a click event on rows that matches the first link action (if present)
     *
     * @param {Grid} grid
     */

  }, {
    key: 'initRowLinks',
    value: function initRowLinks(grid) {
      $('tr', grid.getContainer()).each(function initEachRow() {
        var $parentRow = $(this);

        $('.js-link-row-action[data-clickable-row=1]:first', $parentRow).each(function propagateFirstLinkAction() {
          var $rowAction = $(this);
          var $parentCell = $rowAction.closest('td');

          var clickableCells = $('td.clickable', $parentRow).not($parentCell);

          clickableCells.addClass('cursor-pointer').click(function () {
            var confirmMessage = $rowAction.data('confirm-message');

            if (!confirmMessage.length || confirm(confirmMessage)) {
              document.location = $rowAction.attr('href');
            }
          });
        });
      });
    }
  }]);
  return LinkRowActionExtension;
}();

exports.default = LinkRowActionExtension;

/***/ }),

/***/ 37:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

var _createClass2 = __webpack_require__(1);

var _createClass3 = _interopRequireDefault(_createClass2);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

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
 * Class SubmitRowActionExtension handles submitting of row action
 */

var SubmitRowActionExtension = function () {
  function SubmitRowActionExtension() {
    (0, _classCallCheck3.default)(this, SubmitRowActionExtension);
  }

  (0, _createClass3.default)(SubmitRowActionExtension, [{
    key: 'extend',

    /**
     * Extend grid
     *
     * @param {Grid} grid
     */
    value: function extend(grid) {
      grid.getContainer().on('click', '.js-submit-row-action', function (event) {
        event.preventDefault();

        var $button = $(event.currentTarget);
        var confirmMessage = $button.data('confirm-message');

        if (confirmMessage.length && !confirm(confirmMessage)) {
          return;
        }

        var method = $button.data('method');
        var isGetOrPostMethod = ['GET', 'POST'].includes(method);

        var $form = $('<form>', {
          'action': $button.data('url'),
          'method': isGetOrPostMethod ? method : 'POST'
        }).appendTo('body');

        if (!isGetOrPostMethod) {
          $form.append($('<input>', {
            'type': '_hidden',
            'name': '_method',
            'value': method
          }));
        }

        $form.submit();
      });
    }
  }]);
  return SubmitRowActionExtension;
}();

exports.default = SubmitRowActionExtension;

/***/ }),

/***/ 38:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.EventEmitter = undefined;

var _events = __webpack_require__(56);

var _events2 = _interopRequireDefault(_events);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

/**
 * We instanciate one EventEmitter (restricted via a const) so that every components
 * register/dispatch on the same one and can communicate with each other.
 */
var EventEmitter = exports.EventEmitter = new _events2.default(); /**
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

/***/ }),

/***/ 4:
/***/ (function(module, exports) {

module.exports = function(it){
  return typeof it === 'object' ? it !== null : typeof it === 'function';
};

/***/ }),

/***/ 41:
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function(global) {

Object.defineProperty(exports, "__esModule", {
  value: true
});

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

var _createClass2 = __webpack_require__(1);

var _createClass3 = _interopRequireDefault(_createClass2);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

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

var $ = global.$;

/**
 * Makes a table sortable by columns.
 * This forces a page reload with more query parameters.
 */

var TableSorting = function () {

  /**
   * @param {jQuery} table
   */
  function TableSorting(table) {
    (0, _classCallCheck3.default)(this, TableSorting);

    this.selector = '.ps-sortable-column';
    this.columns = $(table).find(this.selector);
  }

  /**
   * Attaches the listeners
   */


  (0, _createClass3.default)(TableSorting, [{
    key: 'attach',
    value: function attach() {
      var _this = this;

      this.columns.on('click', function (e) {
        var $column = $(e.delegateTarget);
        _this._sortByColumn($column, _this._getToggledSortDirection($column));
      });
    }

    /**
     * Sort using a column name
     * @param {string} columnName
     * @param {string} direction "asc" or "desc"
     */

  }, {
    key: 'sortBy',
    value: function sortBy(columnName, direction) {
      var $column = this.columns.is('[data-sort-col-name="' + columnName + '"]');
      if (!$column) {
        throw new Error('Cannot sort by "' + columnName + '": invalid column');
      }

      this._sortByColumn($column, direction);
    }

    /**
     * Sort using a column element
     * @param {jQuery} column
     * @param {string} direction "asc" or "desc"
     * @private
     */

  }, {
    key: '_sortByColumn',
    value: function _sortByColumn(column, direction) {
      window.location = this._getUrl(column.data('sortColName'), direction === 'desc' ? 'desc' : 'asc', column.data('sortPrefix'));
    }

    /**
     * Returns the inverted direction to sort according to the column's current one
     * @param {jQuery} column
     * @return {string}
     * @private
     */

  }, {
    key: '_getToggledSortDirection',
    value: function _getToggledSortDirection(column) {
      return column.data('sortDirection') === 'asc' ? 'desc' : 'asc';
    }

    /**
     * Returns the url for the sorted table
     * @param {string} colName
     * @param {string} direction
     * @param {string} prefix
     * @return {string}
     * @private
     */

  }, {
    key: '_getUrl',
    value: function _getUrl(colName, direction, prefix) {
      var url = new URL(window.location.href);
      var params = url.searchParams;

      if (prefix) {
        params.set(prefix + '[orderBy]', colName);
        params.set(prefix + '[sortOrder]', direction);
      } else {
        params.set('orderBy', colName);
        params.set('sortOrder', direction);
      }

      return url.toString();
    }
  }]);
  return TableSorting;
}();

exports.default = TableSorting;
/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(9)))

/***/ }),

/***/ 42:
/***/ (function(module, exports) {

(function() { module.exports = window["jQuery"]; }());

/***/ }),

/***/ 43:
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function(global) {

Object.defineProperty(exports, "__esModule", {
  value: true
});
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

/**
 * Send a Post Request to reset search Action.
 */

var $ = global.$;

var init = function resetSearch(url, redirectUrl) {
  $.post(url).then(function () {
    return window.location.assign(redirectUrl);
  });
};

exports.default = init;
/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(9)))

/***/ }),

/***/ 46:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

var _createClass2 = __webpack_require__(1);

var _createClass3 = _interopRequireDefault(_createClass2);

var _eventEmitter = __webpack_require__(38);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

var $ = window.$;

/**
 * This class is used to automatically toggle translated inputs (displayed with one
 * input and a language selector using the TranslatableType Symfony form type).
 * Also compatible with TranslatableField changes.
 */
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

var TranslatableInput = function () {
  function TranslatableInput(options) {
    (0, _classCallCheck3.default)(this, TranslatableInput);

    options = options || {};

    this.localeItemSelector = options.localeItemSelector || '.js-locale-item';
    this.localeButtonSelector = options.localeButtonSelector || '.js-locale-btn';
    this.localeInputSelector = options.localeInputSelector || '.js-locale-input';

    $('body').on('click', this.localeItemSelector, this.toggleLanguage.bind(this));
    _eventEmitter.EventEmitter.on('languageSelected', this.toggleInputs.bind(this));
  }

  /**
   * Dispatch event on language selection to update inputs and other components which depend on the locale.
   *
   * @param event
   */


  (0, _createClass3.default)(TranslatableInput, [{
    key: 'toggleLanguage',
    value: function toggleLanguage(event) {
      var localeItem = $(event.target);
      var form = localeItem.closest('form');
      _eventEmitter.EventEmitter.emit('languageSelected', {
        selectedLocale: localeItem.data('locale'),
        form: form
      });
    }

    /**
     * Toggle all translatable inputs in form in which locale was changed
     *
     * @param {Event} event
     */

  }, {
    key: 'toggleInputs',
    value: function toggleInputs(event) {
      var form = event.form;
      var selectedLocale = event.selectedLocale;
      var localeButton = form.find(this.localeButtonSelector);
      var changeLanguageUrl = localeButton.data('change-language-url');

      localeButton.text(selectedLocale);
      form.find(this.localeInputSelector).addClass('d-none');
      form.find(this.localeInputSelector + '.js-locale-' + selectedLocale).removeClass('d-none');

      if (changeLanguageUrl) {
        this._saveSelectedLanguage(changeLanguageUrl, selectedLocale);
      }
    }

    /**
     * Save language choice for employee forms.
     *
     * @param {String} changeLanguageUrl
     * @param {String} selectedLocale
     *
     * @private
     */

  }, {
    key: '_saveSelectedLanguage',
    value: function _saveSelectedLanguage(changeLanguageUrl, selectedLocale) {
      $.post({
        url: changeLanguageUrl,
        data: {
          language_iso_code: selectedLocale
        }
      });
    }
  }]);
  return TranslatableInput;
}();

exports.default = TranslatableInput;

/***/ }),

/***/ 47:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = ConfirmModal;
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
 * ConfirmModal component
 *
 * @param {String} id
 * @param {String} confirmTitle
 * @param {String} confirmMessage
 * @param {String} closeButtonLabel
 * @param {String} confirmButtonLabel
 * @param {String} confirmButtonClass
 * @param {Boolean} closable
 * @param {Function} confirmCallback
 *
 */
function ConfirmModal(params, confirmCallback) {
  var _this = this;

  // Construct the modal
  var id = params.id,
      closable = params.closable;

  this.modal = Modal(params);

  // jQuery modal object
  this.$modal = $(this.modal.container);

  this.show = function () {
    _this.$modal.modal();
  };

  this.modal.confirmButton.addEventListener('click', confirmCallback);

  this.$modal.modal({
    backdrop: closable ? true : 'static',
    keyboard: closable !== undefined ? closable : true,
    closable: closable !== undefined ? closable : true,
    show: false
  });

  this.$modal.on('hidden.bs.modal', function () {
    document.querySelector('#' + id).remove();
  });

  document.body.appendChild(this.modal.container);
}

/**
 * Modal component to improve lisibility by constructing the modal outside the main function
 *
 * @param {Object} params
 *
 */
function Modal(_ref) {
  var _ref$id = _ref.id,
      id = _ref$id === undefined ? 'confirm_modal' : _ref$id,
      confirmTitle = _ref.confirmTitle,
      _ref$confirmMessage = _ref.confirmMessage,
      confirmMessage = _ref$confirmMessage === undefined ? '' : _ref$confirmMessage,
      _ref$closeButtonLabel = _ref.closeButtonLabel,
      closeButtonLabel = _ref$closeButtonLabel === undefined ? 'Close' : _ref$closeButtonLabel,
      _ref$confirmButtonLab = _ref.confirmButtonLabel,
      confirmButtonLabel = _ref$confirmButtonLab === undefined ? 'Accept' : _ref$confirmButtonLab,
      _ref$confirmButtonCla = _ref.confirmButtonClass,
      confirmButtonClass = _ref$confirmButtonCla === undefined ? 'btn-primary' : _ref$confirmButtonCla;

  var modal = {};

  // Main modal element
  modal.container = document.createElement('div');
  modal.container.classList.add('modal', 'fade');
  modal.container.id = id;

  // Modal dialog element
  modal.dialog = document.createElement('div');
  modal.dialog.classList.add('modal-dialog');

  // Modal content element
  modal.content = document.createElement('div');
  modal.content.classList.add('modal-content');

  // Modal header element
  modal.header = document.createElement('div');
  modal.header.classList.add('modal-header');

  // Modal title element
  if (confirmTitle) {
    modal.title = document.createElement('h4');
    modal.title.classList.add('modal-title');
    modal.title.innerHTML = confirmTitle;
  }

  // Modal close button icon
  modal.closeIcon = document.createElement('button');
  modal.closeIcon.classList.add('close');
  modal.closeIcon.setAttribute('type', 'button');
  modal.closeIcon.dataset.dismiss = 'modal';
  modal.closeIcon.innerHTML = '×';

  // Modal body element
  modal.body = document.createElement('div');
  modal.body.classList.add('modal-body', 'text-left', 'font-weight-normal');

  // Modal message element
  modal.message = document.createElement('p');
  modal.message.classList.add('confirm-message');
  modal.message.innerHTML = confirmMessage;

  // Modal footer element
  modal.footer = document.createElement('div');
  modal.footer.classList.add('modal-footer');

  // Modal close button element
  modal.closeButton = document.createElement('button');
  modal.closeButton.setAttribute('type', 'button');
  modal.closeButton.classList.add('btn', 'btn-outline-secondary', 'btn-lg');
  modal.closeButton.dataset.dismiss = 'modal';
  modal.closeButton.innerHTML = closeButtonLabel;

  // Modal close button element
  modal.confirmButton = document.createElement('button');
  modal.confirmButton.setAttribute('type', 'button');
  modal.confirmButton.classList.add('btn', confirmButtonClass, 'btn-lg', 'btn-confirm-submit');
  modal.confirmButton.dataset.dismiss = 'modal';
  modal.confirmButton.innerHTML = confirmButtonLabel;

  // Constructing the modal
  if (confirmTitle) {
    modal.header.append(modal.title, modal.closeIcon);
  } else {
    modal.header.appendChild(modal.closeIcon);
  }

  modal.body.appendChild(modal.message);
  modal.footer.append(modal.closeButton, modal.confirmButton);
  modal.content.append(modal.header, modal.body, modal.footer);
  modal.dialog.appendChild(modal.content);
  modal.container.appendChild(modal.dialog);

  return modal;
}

/***/ }),

/***/ 485:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _grid = __webpack_require__(24);

var _grid2 = _interopRequireDefault(_grid);

var _sortingExtension = __webpack_require__(27);

var _sortingExtension2 = _interopRequireDefault(_sortingExtension);

var _submitRowActionExtension = __webpack_require__(37);

var _submitRowActionExtension2 = _interopRequireDefault(_submitRowActionExtension);

var _filtersResetExtension = __webpack_require__(26);

var _filtersResetExtension2 = _interopRequireDefault(_filtersResetExtension);

var _reloadListExtension = __webpack_require__(28);

var _reloadListExtension2 = _interopRequireDefault(_reloadListExtension);

var _exportToSqlManagerExtension = __webpack_require__(29);

var _exportToSqlManagerExtension2 = _interopRequireDefault(_exportToSqlManagerExtension);

var _linkRowActionExtension = __webpack_require__(34);

var _linkRowActionExtension2 = _interopRequireDefault(_linkRowActionExtension);

var _submitBulkActionExtension = __webpack_require__(32);

var _submitBulkActionExtension2 = _interopRequireDefault(_submitBulkActionExtension);

var _bulkActionCheckboxExtension = __webpack_require__(31);

var _bulkActionCheckboxExtension2 = _interopRequireDefault(_bulkActionCheckboxExtension);

var _columnTogglingExtension = __webpack_require__(62);

var _columnTogglingExtension2 = _interopRequireDefault(_columnTogglingExtension);

var _positionExtension = __webpack_require__(149);

var _positionExtension2 = _interopRequireDefault(_positionExtension);

var _choiceTree = __webpack_require__(61);

var _choiceTree2 = _interopRequireDefault(_choiceTree);

var _translatableInput = __webpack_require__(46);

var _translatableInput2 = _interopRequireDefault(_translatableInput);

var _textToLinkRewriteCopier = __webpack_require__(150);

var _textToLinkRewriteCopier2 = _interopRequireDefault(_textToLinkRewriteCopier);

var _filtersSubmitButtonEnablerExtension = __webpack_require__(30);

var _filtersSubmitButtonEnablerExtension2 = _interopRequireDefault(_filtersSubmitButtonEnablerExtension);

var _taggableField = __webpack_require__(92);

var _taggableField2 = _interopRequireDefault(_taggableField);

var _showcaseCard = __webpack_require__(81);

var _showcaseCard2 = _interopRequireDefault(_showcaseCard);

var _showcaseCardCloseExtension = __webpack_require__(80);

var _showcaseCardCloseExtension2 = _interopRequireDefault(_showcaseCardCloseExtension);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

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

$(function () {
  var cmsCategory = new _grid2.default('cms_page_category');

  cmsCategory.addExtension(new _reloadListExtension2.default());
  cmsCategory.addExtension(new _exportToSqlManagerExtension2.default());
  cmsCategory.addExtension(new _filtersResetExtension2.default());
  cmsCategory.addExtension(new _sortingExtension2.default());
  cmsCategory.addExtension(new _linkRowActionExtension2.default());
  cmsCategory.addExtension(new _submitBulkActionExtension2.default());
  cmsCategory.addExtension(new _bulkActionCheckboxExtension2.default());
  cmsCategory.addExtension(new _submitRowActionExtension2.default());
  cmsCategory.addExtension(new _columnTogglingExtension2.default());
  cmsCategory.addExtension(new _positionExtension2.default());
  cmsCategory.addExtension(new _filtersSubmitButtonEnablerExtension2.default());

  var translatorInput = new _translatableInput2.default();

  (0, _textToLinkRewriteCopier2.default)({
    sourceElementSelector: 'input[name^="cms_page_category[name]"]',
    destinationElementSelector: translatorInput.localeInputSelector + ':not(.d-none) input[name^="cms_page_category[friendly_url]"]'
  });

  new _choiceTree2.default('#cms_page_category_parent_category');

  var shopChoiceTree = new _choiceTree2.default('#cms_page_category_shop_association');
  shopChoiceTree.enableAutoCheckChildren();

  new _taggableField2.default({
    tokenFieldSelector: 'input[name^="cms_page_category[meta_keywords]"]',
    options: {
      createTokensOnBlur: true
    }
  });

  var cmsGrid = new _grid2.default('cms_page');
  cmsGrid.addExtension(new _reloadListExtension2.default());
  cmsGrid.addExtension(new _exportToSqlManagerExtension2.default());
  cmsGrid.addExtension(new _filtersResetExtension2.default());
  cmsGrid.addExtension(new _sortingExtension2.default());
  cmsGrid.addExtension(new _columnTogglingExtension2.default());
  cmsGrid.addExtension(new _bulkActionCheckboxExtension2.default());
  cmsGrid.addExtension(new _submitBulkActionExtension2.default());
  cmsGrid.addExtension(new _submitRowActionExtension2.default());
  cmsGrid.addExtension(new _positionExtension2.default());
  cmsGrid.addExtension(new _filtersSubmitButtonEnablerExtension2.default());
  cmsGrid.addExtension(new _linkRowActionExtension2.default());

  var helperBlock = new _showcaseCard2.default('cms-pages-showcase-card');
  helperBlock.addExtension(new _showcaseCardCloseExtension2.default());
});

/***/ }),

/***/ 5:
/***/ (function(module, exports) {

// https://github.com/zloirock/core-js/issues/86#issuecomment-115759028
var global = module.exports = typeof window != 'undefined' && window.Math == Math
  ? window : typeof self != 'undefined' && self.Math == Math ? self : Function('return this')();
if(typeof __g == 'number')__g = global; // eslint-disable-line no-undef

/***/ }),

/***/ 56:
/***/ (function(module, exports, __webpack_require__) {

"use strict";
// Copyright Joyent, Inc. and other Node contributors.
//
// Permission is hereby granted, free of charge, to any person obtaining a
// copy of this software and associated documentation files (the
// "Software"), to deal in the Software without restriction, including
// without limitation the rights to use, copy, modify, merge, publish,
// distribute, sublicense, and/or sell copies of the Software, and to permit
// persons to whom the Software is furnished to do so, subject to the
// following conditions:
//
// The above copyright notice and this permission notice shall be included
// in all copies or substantial portions of the Software.
//
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
// OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
// MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN
// NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM,
// DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
// OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE
// USE OR OTHER DEALINGS IN THE SOFTWARE.



var R = typeof Reflect === 'object' ? Reflect : null
var ReflectApply = R && typeof R.apply === 'function'
  ? R.apply
  : function ReflectApply(target, receiver, args) {
    return Function.prototype.apply.call(target, receiver, args);
  }

var ReflectOwnKeys
if (R && typeof R.ownKeys === 'function') {
  ReflectOwnKeys = R.ownKeys
} else if (Object.getOwnPropertySymbols) {
  ReflectOwnKeys = function ReflectOwnKeys(target) {
    return Object.getOwnPropertyNames(target)
      .concat(Object.getOwnPropertySymbols(target));
  };
} else {
  ReflectOwnKeys = function ReflectOwnKeys(target) {
    return Object.getOwnPropertyNames(target);
  };
}

function ProcessEmitWarning(warning) {
  if (console && console.warn) console.warn(warning);
}

var NumberIsNaN = Number.isNaN || function NumberIsNaN(value) {
  return value !== value;
}

function EventEmitter() {
  EventEmitter.init.call(this);
}
module.exports = EventEmitter;

// Backwards-compat with node 0.10.x
EventEmitter.EventEmitter = EventEmitter;

EventEmitter.prototype._events = undefined;
EventEmitter.prototype._eventsCount = 0;
EventEmitter.prototype._maxListeners = undefined;

// By default EventEmitters will print a warning if more than 10 listeners are
// added to it. This is a useful default which helps finding memory leaks.
var defaultMaxListeners = 10;

Object.defineProperty(EventEmitter, 'defaultMaxListeners', {
  enumerable: true,
  get: function() {
    return defaultMaxListeners;
  },
  set: function(arg) {
    if (typeof arg !== 'number' || arg < 0 || NumberIsNaN(arg)) {
      throw new RangeError('The value of "defaultMaxListeners" is out of range. It must be a non-negative number. Received ' + arg + '.');
    }
    defaultMaxListeners = arg;
  }
});

EventEmitter.init = function() {

  if (this._events === undefined ||
      this._events === Object.getPrototypeOf(this)._events) {
    this._events = Object.create(null);
    this._eventsCount = 0;
  }

  this._maxListeners = this._maxListeners || undefined;
};

// Obviously not all Emitters should be limited to 10. This function allows
// that to be increased. Set to zero for unlimited.
EventEmitter.prototype.setMaxListeners = function setMaxListeners(n) {
  if (typeof n !== 'number' || n < 0 || NumberIsNaN(n)) {
    throw new RangeError('The value of "n" is out of range. It must be a non-negative number. Received ' + n + '.');
  }
  this._maxListeners = n;
  return this;
};

function $getMaxListeners(that) {
  if (that._maxListeners === undefined)
    return EventEmitter.defaultMaxListeners;
  return that._maxListeners;
}

EventEmitter.prototype.getMaxListeners = function getMaxListeners() {
  return $getMaxListeners(this);
};

EventEmitter.prototype.emit = function emit(type) {
  var args = [];
  for (var i = 1; i < arguments.length; i++) args.push(arguments[i]);
  var doError = (type === 'error');

  var events = this._events;
  if (events !== undefined)
    doError = (doError && events.error === undefined);
  else if (!doError)
    return false;

  // If there is no 'error' event listener then throw.
  if (doError) {
    var er;
    if (args.length > 0)
      er = args[0];
    if (er instanceof Error) {
      // Note: The comments on the `throw` lines are intentional, they show
      // up in Node's output if this results in an unhandled exception.
      throw er; // Unhandled 'error' event
    }
    // At least give some kind of context to the user
    var err = new Error('Unhandled error.' + (er ? ' (' + er.message + ')' : ''));
    err.context = er;
    throw err; // Unhandled 'error' event
  }

  var handler = events[type];

  if (handler === undefined)
    return false;

  if (typeof handler === 'function') {
    ReflectApply(handler, this, args);
  } else {
    var len = handler.length;
    var listeners = arrayClone(handler, len);
    for (var i = 0; i < len; ++i)
      ReflectApply(listeners[i], this, args);
  }

  return true;
};

function _addListener(target, type, listener, prepend) {
  var m;
  var events;
  var existing;

  if (typeof listener !== 'function') {
    throw new TypeError('The "listener" argument must be of type Function. Received type ' + typeof listener);
  }

  events = target._events;
  if (events === undefined) {
    events = target._events = Object.create(null);
    target._eventsCount = 0;
  } else {
    // To avoid recursion in the case that type === "newListener"! Before
    // adding it to the listeners, first emit "newListener".
    if (events.newListener !== undefined) {
      target.emit('newListener', type,
                  listener.listener ? listener.listener : listener);

      // Re-assign `events` because a newListener handler could have caused the
      // this._events to be assigned to a new object
      events = target._events;
    }
    existing = events[type];
  }

  if (existing === undefined) {
    // Optimize the case of one listener. Don't need the extra array object.
    existing = events[type] = listener;
    ++target._eventsCount;
  } else {
    if (typeof existing === 'function') {
      // Adding the second element, need to change to array.
      existing = events[type] =
        prepend ? [listener, existing] : [existing, listener];
      // If we've already got an array, just append.
    } else if (prepend) {
      existing.unshift(listener);
    } else {
      existing.push(listener);
    }

    // Check for listener leak
    m = $getMaxListeners(target);
    if (m > 0 && existing.length > m && !existing.warned) {
      existing.warned = true;
      // No error code for this since it is a Warning
      // eslint-disable-next-line no-restricted-syntax
      var w = new Error('Possible EventEmitter memory leak detected. ' +
                          existing.length + ' ' + String(type) + ' listeners ' +
                          'added. Use emitter.setMaxListeners() to ' +
                          'increase limit');
      w.name = 'MaxListenersExceededWarning';
      w.emitter = target;
      w.type = type;
      w.count = existing.length;
      ProcessEmitWarning(w);
    }
  }

  return target;
}

EventEmitter.prototype.addListener = function addListener(type, listener) {
  return _addListener(this, type, listener, false);
};

EventEmitter.prototype.on = EventEmitter.prototype.addListener;

EventEmitter.prototype.prependListener =
    function prependListener(type, listener) {
      return _addListener(this, type, listener, true);
    };

function onceWrapper() {
  var args = [];
  for (var i = 0; i < arguments.length; i++) args.push(arguments[i]);
  if (!this.fired) {
    this.target.removeListener(this.type, this.wrapFn);
    this.fired = true;
    ReflectApply(this.listener, this.target, args);
  }
}

function _onceWrap(target, type, listener) {
  var state = { fired: false, wrapFn: undefined, target: target, type: type, listener: listener };
  var wrapped = onceWrapper.bind(state);
  wrapped.listener = listener;
  state.wrapFn = wrapped;
  return wrapped;
}

EventEmitter.prototype.once = function once(type, listener) {
  if (typeof listener !== 'function') {
    throw new TypeError('The "listener" argument must be of type Function. Received type ' + typeof listener);
  }
  this.on(type, _onceWrap(this, type, listener));
  return this;
};

EventEmitter.prototype.prependOnceListener =
    function prependOnceListener(type, listener) {
      if (typeof listener !== 'function') {
        throw new TypeError('The "listener" argument must be of type Function. Received type ' + typeof listener);
      }
      this.prependListener(type, _onceWrap(this, type, listener));
      return this;
    };

// Emits a 'removeListener' event if and only if the listener was removed.
EventEmitter.prototype.removeListener =
    function removeListener(type, listener) {
      var list, events, position, i, originalListener;

      if (typeof listener !== 'function') {
        throw new TypeError('The "listener" argument must be of type Function. Received type ' + typeof listener);
      }

      events = this._events;
      if (events === undefined)
        return this;

      list = events[type];
      if (list === undefined)
        return this;

      if (list === listener || list.listener === listener) {
        if (--this._eventsCount === 0)
          this._events = Object.create(null);
        else {
          delete events[type];
          if (events.removeListener)
            this.emit('removeListener', type, list.listener || listener);
        }
      } else if (typeof list !== 'function') {
        position = -1;

        for (i = list.length - 1; i >= 0; i--) {
          if (list[i] === listener || list[i].listener === listener) {
            originalListener = list[i].listener;
            position = i;
            break;
          }
        }

        if (position < 0)
          return this;

        if (position === 0)
          list.shift();
        else {
          spliceOne(list, position);
        }

        if (list.length === 1)
          events[type] = list[0];

        if (events.removeListener !== undefined)
          this.emit('removeListener', type, originalListener || listener);
      }

      return this;
    };

EventEmitter.prototype.off = EventEmitter.prototype.removeListener;

EventEmitter.prototype.removeAllListeners =
    function removeAllListeners(type) {
      var listeners, events, i;

      events = this._events;
      if (events === undefined)
        return this;

      // not listening for removeListener, no need to emit
      if (events.removeListener === undefined) {
        if (arguments.length === 0) {
          this._events = Object.create(null);
          this._eventsCount = 0;
        } else if (events[type] !== undefined) {
          if (--this._eventsCount === 0)
            this._events = Object.create(null);
          else
            delete events[type];
        }
        return this;
      }

      // emit removeListener for all listeners on all events
      if (arguments.length === 0) {
        var keys = Object.keys(events);
        var key;
        for (i = 0; i < keys.length; ++i) {
          key = keys[i];
          if (key === 'removeListener') continue;
          this.removeAllListeners(key);
        }
        this.removeAllListeners('removeListener');
        this._events = Object.create(null);
        this._eventsCount = 0;
        return this;
      }

      listeners = events[type];

      if (typeof listeners === 'function') {
        this.removeListener(type, listeners);
      } else if (listeners !== undefined) {
        // LIFO order
        for (i = listeners.length - 1; i >= 0; i--) {
          this.removeListener(type, listeners[i]);
        }
      }

      return this;
    };

function _listeners(target, type, unwrap) {
  var events = target._events;

  if (events === undefined)
    return [];

  var evlistener = events[type];
  if (evlistener === undefined)
    return [];

  if (typeof evlistener === 'function')
    return unwrap ? [evlistener.listener || evlistener] : [evlistener];

  return unwrap ?
    unwrapListeners(evlistener) : arrayClone(evlistener, evlistener.length);
}

EventEmitter.prototype.listeners = function listeners(type) {
  return _listeners(this, type, true);
};

EventEmitter.prototype.rawListeners = function rawListeners(type) {
  return _listeners(this, type, false);
};

EventEmitter.listenerCount = function(emitter, type) {
  if (typeof emitter.listenerCount === 'function') {
    return emitter.listenerCount(type);
  } else {
    return listenerCount.call(emitter, type);
  }
};

EventEmitter.prototype.listenerCount = listenerCount;
function listenerCount(type) {
  var events = this._events;

  if (events !== undefined) {
    var evlistener = events[type];

    if (typeof evlistener === 'function') {
      return 1;
    } else if (evlistener !== undefined) {
      return evlistener.length;
    }
  }

  return 0;
}

EventEmitter.prototype.eventNames = function eventNames() {
  return this._eventsCount > 0 ? ReflectOwnKeys(this._events) : [];
};

function arrayClone(arr, n) {
  var copy = new Array(n);
  for (var i = 0; i < n; ++i)
    copy[i] = arr[i];
  return copy;
}

function spliceOne(list, index) {
  for (; index + 1 < list.length; index++)
    list[index] = list[index + 1];
  list.pop();
}

function unwrapListeners(arr) {
  var ret = new Array(arr.length);
  for (var i = 0; i < ret.length; ++i) {
    ret[i] = arr[i].listener || arr[i];
  }
  return ret;
}


/***/ }),

/***/ 6:
/***/ (function(module, exports, __webpack_require__) {

var anObject       = __webpack_require__(11)
  , IE8_DOM_DEFINE = __webpack_require__(17)
  , toPrimitive    = __webpack_require__(14)
  , dP             = Object.defineProperty;

exports.f = __webpack_require__(2) ? Object.defineProperty : function defineProperty(O, P, Attributes){
  anObject(O);
  P = toPrimitive(P, true);
  anObject(Attributes);
  if(IE8_DOM_DEFINE)try {
    return dP(O, P, Attributes);
  } catch(e){ /* empty */ }
  if('get' in Attributes || 'set' in Attributes)throw TypeError('Accessors not supported!');
  if('value' in Attributes)O[P] = Attributes.value;
  return O;
};

/***/ }),

/***/ 61:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

var _createClass2 = __webpack_require__(1);

var _createClass3 = _interopRequireDefault(_createClass2);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

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
 * Handles UI interactions of choice tree
 */

var ChoiceTree = function () {
  /**
   * @param {String} treeSelector
   */
  function ChoiceTree(treeSelector) {
    var _this = this;

    (0, _classCallCheck3.default)(this, ChoiceTree);

    this.$container = $(treeSelector);

    this.$container.on('click', '.js-input-wrapper', function (event) {
      var $inputWrapper = $(event.currentTarget);

      _this._toggleChildTree($inputWrapper);
    });

    this.$container.on('click', '.js-toggle-choice-tree-action', function (event) {
      var $action = $(event.currentTarget);

      _this._toggleTree($action);
    });

    return {
      enableAutoCheckChildren: function enableAutoCheckChildren() {
        return _this.enableAutoCheckChildren();
      },
      enableAllInputs: function enableAllInputs() {
        return _this.enableAllInputs();
      },
      disableAllInputs: function disableAllInputs() {
        return _this.disableAllInputs();
      }
    };
  }

  /**
   * Enable automatic check/uncheck of clicked item's children.
   */


  (0, _createClass3.default)(ChoiceTree, [{
    key: 'enableAutoCheckChildren',
    value: function enableAutoCheckChildren() {
      this.$container.on('change', 'input[type="checkbox"]', function (event) {
        var $clickedCheckbox = $(event.currentTarget);
        var $itemWithChildren = $clickedCheckbox.closest('li');

        $itemWithChildren.find('ul input[type="checkbox"]').prop('checked', $clickedCheckbox.is(':checked'));
      });
    }

    /**
     * Enable all inputs in the choice tree.
     */

  }, {
    key: 'enableAllInputs',
    value: function enableAllInputs() {
      this.$container.find('input').removeAttr('disabled');
    }

    /**
     * Disable all inputs in the choice tree.
     */

  }, {
    key: 'disableAllInputs',
    value: function disableAllInputs() {
      this.$container.find('input').attr('disabled', 'disabled');
    }

    /**
     * Collapse or expand sub-tree for single parent
     *
     * @param {jQuery} $inputWrapper
     *
     * @private
     */

  }, {
    key: '_toggleChildTree',
    value: function _toggleChildTree($inputWrapper) {
      var $parentWrapper = $inputWrapper.closest('li');

      if ($parentWrapper.hasClass('expanded')) {
        $parentWrapper.removeClass('expanded').addClass('collapsed');

        return;
      }

      if ($parentWrapper.hasClass('collapsed')) {
        $parentWrapper.removeClass('collapsed').addClass('expanded');
      }
    }

    /**
     * Collapse or expand whole tree
     *
     * @param {jQuery} $action
     *
     * @private
     */

  }, {
    key: '_toggleTree',
    value: function _toggleTree($action) {
      var $parentContainer = $action.closest('.js-choice-tree-container');
      var action = $action.data('action');

      // toggle action configuration
      var config = {
        addClass: {
          expand: 'expanded',
          collapse: 'collapsed'
        },
        removeClass: {
          expand: 'collapsed',
          collapse: 'expanded'
        },
        nextAction: {
          expand: 'collapse',
          collapse: 'expand'
        },
        text: {
          expand: 'collapsed-text',
          collapse: 'expanded-text'
        },
        icon: {
          expand: 'collapsed-icon',
          collapse: 'expanded-icon'
        }
      };

      $parentContainer.find('li').each(function (index, item) {
        var $item = $(item);

        if ($item.hasClass(config.removeClass[action])) {
          $item.removeClass(config.removeClass[action]).addClass(config.addClass[action]);
        }
      });

      $action.data('action', config.nextAction[action]);
      $action.find('.material-icons').text($action.data(config.icon[action]));
      $action.find('.js-toggle-text').text($action.data(config.text[action]));
    }
  }]);
  return ChoiceTree;
}();

exports.default = ChoiceTree;

/***/ }),

/***/ 62:
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function(global) {

Object.defineProperty(exports, "__esModule", {
  value: true
});

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

var _createClass2 = __webpack_require__(1);

var _createClass3 = _interopRequireDefault(_createClass2);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

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

var $ = global.$;

/**
 * Class ReloadListExtension extends grid with "Column toggling" feature
 */

var ColumnTogglingExtension = function () {
  function ColumnTogglingExtension() {
    (0, _classCallCheck3.default)(this, ColumnTogglingExtension);
  }

  (0, _createClass3.default)(ColumnTogglingExtension, [{
    key: 'extend',


    /**
     * Extend grid
     *
     * @param {Grid} grid
     */
    value: function extend(grid) {
      var _this = this;

      var $table = grid.getContainer().find('table.table');
      $table.find('.ps-togglable-row').on('click', function (e) {
        e.preventDefault();
        _this._toggleValue($(e.delegateTarget));
      });
    }

    /**
     * @param {jQuery} row
     * @private
     */

  }, {
    key: '_toggleValue',
    value: function _toggleValue(row) {
      var toggleUrl = row.data('toggleUrl');

      this._submitAsForm(toggleUrl);
    }

    /**
     * Submits request url as form
     *
     * @param {string} toggleUrl
     * @private
     */

  }, {
    key: '_submitAsForm',
    value: function _submitAsForm(toggleUrl) {
      var $form = $('<form>', {
        action: toggleUrl,
        method: 'POST'
      }).appendTo('body');

      $form.submit();
    }
  }]);
  return ColumnTogglingExtension;
}();

exports.default = ColumnTogglingExtension;
/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(9)))

/***/ }),

/***/ 7:
/***/ (function(module, exports) {

module.exports = function(exec){
  try {
    return !!exec();
  } catch(e){
    return true;
  }
};

/***/ }),

/***/ 8:
/***/ (function(module, exports, __webpack_require__) {

var global    = __webpack_require__(5)
  , core      = __webpack_require__(3)
  , ctx       = __webpack_require__(13)
  , hide      = __webpack_require__(10)
  , PROTOTYPE = 'prototype';

var $export = function(type, name, source){
  var IS_FORCED = type & $export.F
    , IS_GLOBAL = type & $export.G
    , IS_STATIC = type & $export.S
    , IS_PROTO  = type & $export.P
    , IS_BIND   = type & $export.B
    , IS_WRAP   = type & $export.W
    , exports   = IS_GLOBAL ? core : core[name] || (core[name] = {})
    , expProto  = exports[PROTOTYPE]
    , target    = IS_GLOBAL ? global : IS_STATIC ? global[name] : (global[name] || {})[PROTOTYPE]
    , key, own, out;
  if(IS_GLOBAL)source = name;
  for(key in source){
    // contains in native
    own = !IS_FORCED && target && target[key] !== undefined;
    if(own && key in exports)continue;
    // export native or passed
    out = own ? target[key] : source[key];
    // prevent global pollution for namespaces
    exports[key] = IS_GLOBAL && typeof target[key] != 'function' ? source[key]
    // bind timers to global for call from export context
    : IS_BIND && own ? ctx(out, global)
    // wrap global constructors for prevent change them in library
    : IS_WRAP && target[key] == out ? (function(C){
      var F = function(a, b, c){
        if(this instanceof C){
          switch(arguments.length){
            case 0: return new C;
            case 1: return new C(a);
            case 2: return new C(a, b);
          } return new C(a, b, c);
        } return C.apply(this, arguments);
      };
      F[PROTOTYPE] = C[PROTOTYPE];
      return F;
    // make static versions for prototype methods
    })(out) : IS_PROTO && typeof out == 'function' ? ctx(Function.call, out) : out;
    // export proto methods to core.%CONSTRUCTOR%.methods.%NAME%
    if(IS_PROTO){
      (exports.virtual || (exports.virtual = {}))[key] = out;
      // export proto methods to core.%CONSTRUCTOR%.prototype.%NAME%
      if(type & $export.R && expProto && !expProto[key])hide(expProto, key, out);
    }
  }
};
// type bitmap
$export.F = 1;   // forced
$export.G = 2;   // global
$export.S = 4;   // static
$export.P = 8;   // proto
$export.B = 16;  // bind
$export.W = 32;  // wrap
$export.U = 64;  // safe
$export.R = 128; // real proto method for `library` 
module.exports = $export;

/***/ }),

/***/ 80:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

var _createClass2 = __webpack_require__(1);

var _createClass3 = _interopRequireDefault(_createClass2);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

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
 * Class ShowcaseCardCloseExtension is responsible for providing helper block closing behavior
 */

var ShowcaseCardCloseExtension = function () {
  function ShowcaseCardCloseExtension() {
    (0, _classCallCheck3.default)(this, ShowcaseCardCloseExtension);
  }

  (0, _createClass3.default)(ShowcaseCardCloseExtension, [{
    key: 'extend',


    /**
     * Extend helper block.
     *
     * @param {ShowcaseCard} helperBlock
     */
    value: function extend(helperBlock) {
      var container = helperBlock.getContainer();
      container.on('click', '.js-remove-helper-block', function (evt) {
        container.remove();

        var $btn = $(evt.target);
        var url = $btn.data('closeUrl');
        var cardName = $btn.data('cardName');

        if (url) {
          // notify the card was closed
          $.post(url, {
            close: 1,
            name: cardName
          });
        }
      });
    }
  }]);
  return ShowcaseCardCloseExtension;
}();

exports.default = ShowcaseCardCloseExtension;

/***/ }),

/***/ 81:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

var _createClass2 = __webpack_require__(1);

var _createClass3 = _interopRequireDefault(_createClass2);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

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
 * Class ShowcaseCard is responsible for handling events related with showcase card.
 */

var ShowcaseCard = function () {

  /**
   * Showcase card id.
   *
   * @param {string} id
   */
  function ShowcaseCard(id) {
    (0, _classCallCheck3.default)(this, ShowcaseCard);

    this.id = id;
    this.$container = $('#' + this.id);
  }

  /**
   * Get showcase card container.
   *
   * @returns {jQuery}
   */


  (0, _createClass3.default)(ShowcaseCard, [{
    key: 'getContainer',
    value: function getContainer() {
      return this.$container;
    }

    /**
     * Extend showcase card with external extensions.
     *
     * @param {object} extension
     */

  }, {
    key: 'addExtension',
    value: function addExtension(extension) {
      extension.extend(this);
    }
  }]);
  return ShowcaseCard;
}();

exports.default = ShowcaseCard;

/***/ }),

/***/ 9:
/***/ (function(module, exports) {

var g;

// This works in non-strict mode
g = (function() {
	return this;
})();

try {
	// This works if eval is allowed (see CSP)
	g = g || Function("return this")() || (1,eval)("this");
} catch(e) {
	// This works if the window reference is available
	if(typeof window === "object")
		g = window;
}

// g can still be undefined, but nothing to do about it...
// We return undefined, instead of nothing here, so it's
// easier to handle this case. if(!global) { ...}

module.exports = g;


/***/ }),

/***/ 92:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

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
 * class TaggableField is responsible for providing functionality from bootstrap-tokenfield plugin.
 * It allows to have taggable fields which are split in separate blocks once you click enter. Values originally saved
 * in comma split strings.
 */

var TaggableField =
/**
 * @param {string} tokenFieldSelector -  a selector which is used within jQuery object.
 * @param {object} options - extends basic tokenField behavior with additional options such as minLength, delimiter,
 * allow to add token on focus out action. See bootstrap-tokenfield docs for more information.
 */
function TaggableField(_ref) {
  var tokenFieldSelector = _ref.tokenFieldSelector,
      _ref$options = _ref.options,
      options = _ref$options === undefined ? {} : _ref$options;
  (0, _classCallCheck3.default)(this, TaggableField);

  $(tokenFieldSelector).tokenfield(options);
};

exports.default = TaggableField;

/***/ })

/******/ });
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAgM2E2MTdjZWQyOWViY2NiNmExZDA/MTI1MCoqKioqKioqKioqKioqKioiLCJ3ZWJwYWNrOi8vLy4vfi9iYWJlbC1ydW50aW1lL2hlbHBlcnMvY2xhc3NDYWxsQ2hlY2suanM/MjFhZioqKioqKioqKioqKioqIiwid2VicGFjazovLy8uL34vYmFiZWwtcnVudGltZS9oZWxwZXJzL2NyZWF0ZUNsYXNzLmpzPzFkZmUqKioqKioqKioqKioqKiIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19oaWRlLmpzP2E2ZGEqKioqKioqKioqKioqKioqIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2FuLW9iamVjdC5qcz8wZGEzKioqKioqKioqKioqKioqKiIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19wcm9wZXJ0eS1kZXNjLmpzPzFlODYqKioqKioqKioqKioqKioqIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2N0eC5qcz9jZTAwKioqKioqKioqKioqKioqKiIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL190by1wcmltaXRpdmUuanM/NDlhNCoqKioqKioqKioqKioqKioiLCJ3ZWJwYWNrOi8vLy4vanMvY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9wb3NpdGlvbi1leHRlbnNpb24uanMiLCJ3ZWJwYWNrOi8vLy4vanMvY29tcG9uZW50cy90ZXh0LXRvLWxpbmstcmV3cml0ZS1jb3BpZXIuanM/N2Y0ZSoiLCJ3ZWJwYWNrOi8vLy4vfi90YWJsZWRuZC9kaXN0L2pxdWVyeS50YWJsZWRuZC5taW4uanM/MzEwOCIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19kb20tY3JlYXRlLmpzP2FiNDQqKioqKioqKioqKioqKioqIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2llOC1kb20tZGVmaW5lLmpzP2JkMWYqKioqKioqKioqKioqKioqIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2EtZnVuY3Rpb24uanM/ZDUzZSoqKioqKioqKioqKioqKioiLCJ3ZWJwYWNrOi8vLy4vfi9iYWJlbC1ydW50aW1lL2NvcmUtanMvb2JqZWN0L2RlZmluZS1wcm9wZXJ0eS5qcz81ZjcwKioqKioqKioqKioqKioqKiIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19kZXNjcmlwdG9ycy5qcz83MDUxKioqKioqKioqKioqKioqKiIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9mbi9vYmplY3QvZGVmaW5lLXByb3BlcnR5LmpzP2I3ZDgqKioqKioqKioqKioqKioqIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvZXM2Lm9iamVjdC5kZWZpbmUtcHJvcGVydHkuanM/YzgyYyoqKioqKioqKioqKioqKioiLCJ3ZWJwYWNrOi8vLy4vanMvY29tcG9uZW50cy9ncmlkL2dyaWQuanM/ODEzYSoqIiwid2VicGFjazovLy8uL2pzL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vZmlsdGVycy1yZXNldC1leHRlbnNpb24uanM/MTZmMSoqIiwid2VicGFjazovLy8uL2pzL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vc29ydGluZy1leHRlbnNpb24uanM/MTEzZSoqIiwid2VicGFjazovLy8uL2pzL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vcmVsb2FkLWxpc3QtZXh0ZW5zaW9uLmpzP2QzZTAqKiIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2V4cG9ydC10by1zcWwtbWFuYWdlci1leHRlbnNpb24uanM/ZWQyYSoqIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2NvcmUuanM/MWI2MioqKioqKioqKioqKioqKioiLCJ3ZWJwYWNrOi8vLy4vanMvY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9maWx0ZXJzLXN1Ym1pdC1idXR0b24tZW5hYmxlci1leHRlbnNpb24uanM/MDM3OSoqIiwid2VicGFjazovLy8uL2pzL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vYnVsay1hY3Rpb24tY2hlY2tib3gtZXh0ZW5zaW9uLmpzP2IwOTcqKiIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL3N1Ym1pdC1idWxrLWFjdGlvbi1leHRlbnNpb24uanM/MWIxZioqIiwid2VicGFjazovLy8uL2pzL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vbGluay1yb3ctYWN0aW9uLWV4dGVuc2lvbi5qcz8zOWRjKioiLCJ3ZWJwYWNrOi8vLy4vanMvY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9hY3Rpb24vcm93L3N1Ym1pdC1yb3ctYWN0aW9uLWV4dGVuc2lvbi5qcz8yN2QxKiIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL2V2ZW50LWVtaXR0ZXIuanM/MGUwMyoqKioqKioiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9faXMtb2JqZWN0LmpzPzI0YzgqKioqKioqKioqKioqKioqIiwid2VicGFjazovLy8uL2pzL2FwcC91dGlscy90YWJsZS1zb3J0aW5nLmpzPzE1ZDQqKiIsIndlYnBhY2s6Ly8vZXh0ZXJuYWwgXCJqUXVlcnlcIj8wY2I4KioqKioqKiIsIndlYnBhY2s6Ly8vLi9qcy9hcHAvdXRpbHMvcmVzZXRfc2VhcmNoLmpzPzFhN2YqKiIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL3RyYW5zbGF0YWJsZS1pbnB1dC5qcz8xNTk0KioqKioiLCJ3ZWJwYWNrOi8vLy4vanMvY29tcG9uZW50cy9tb2RhbC5qcz83NTU4KioqKiIsIndlYnBhY2s6Ly8vLi9qcy9wYWdlcy9jbXMtcGFnZS9pbmRleC5qcyIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19nbG9iYWwuanM/NzdhYSoqKioqKioqKioqKioqKioiLCJ3ZWJwYWNrOi8vLy4vfi9ldmVudHMvZXZlbnRzLmpzPzdjNzEqKioqKioqIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX29iamVjdC1kcC5qcz80MTE2KioqKioqKioqKioqKioqKiIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL2Zvcm0vY2hvaWNlLXRyZWUuanM/NTQxYSoqKioiLCJ3ZWJwYWNrOi8vLy4vanMvY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9jb2x1bW4tdG9nZ2xpbmctZXh0ZW5zaW9uLmpzPzY5NDMiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fZmFpbHMuanM/OTM1ZCoqKioqKioqKioqKioqKioiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fZXhwb3J0LmpzP2VjZTIqKioqKioqKioqKioqKioqIiwid2VicGFjazovLy8uL2pzL2NvbXBvbmVudHMvc2hvd2Nhc2UtY2FyZC9leHRlbnNpb24vc2hvd2Nhc2UtY2FyZC1jbG9zZS1leHRlbnNpb24uanM/ZDE0MCIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL3Nob3djYXNlLWNhcmQvc2hvd2Nhc2UtY2FyZC5qcz83NjM0Iiwid2VicGFjazovLy8od2VicGFjaykvYnVpbGRpbi9nbG9iYWwuanM/MzY5OCoqKioqKioqKioqKiIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL3RhZ2dhYmxlLWZpZWxkLmpzPzY0M2QqKioiXSwibmFtZXMiOlsiJCIsIndpbmRvdyIsIlBvc2l0aW9uRXh0ZW5zaW9uIiwiZXh0ZW5kIiwiZ3JpZCIsIl9hZGRJZHNUb0dyaWRUYWJsZVJvd3MiLCJnZXRDb250YWluZXIiLCJmaW5kIiwidGFibGVEbkQiLCJvbkRyYWdDbGFzcyIsImRyYWdIYW5kbGUiLCJvbkRyb3AiLCJ0YWJsZSIsInJvdyIsIl9oYW5kbGVQb3NpdGlvbkNoYW5nZSIsImhvdmVyIiwiY2xvc2VzdCIsImFkZENsYXNzIiwicmVtb3ZlQ2xhc3MiLCIkcm93UG9zaXRpb25Db250YWluZXIiLCJnZXRJZCIsInVwZGF0ZVVybCIsImRhdGEiLCJtZXRob2QiLCJwYWdpbmF0aW9uT2Zmc2V0IiwicGFyc2VJbnQiLCJwb3NpdGlvbnMiLCJfZ2V0Um93c1Bvc2l0aW9ucyIsInBhcmFtcyIsIl91cGRhdGVQb3NpdGlvbiIsInRhYmxlRGF0YSIsIkpTT04iLCJwYXJzZSIsImpzb25pemUiLCJyb3dzRGF0YSIsInJlZ2V4Iiwicm93c05iIiwibGVuZ3RoIiwicm93RGF0YSIsImkiLCJleGVjIiwicHVzaCIsInJvd0lkIiwibmV3UG9zaXRpb24iLCJvbGRQb3NpdGlvbiIsImVhY2giLCJpbmRleCIsInBvc2l0aW9uV3JhcHBlciIsIiRwb3NpdGlvbldyYXBwZXIiLCJwb3NpdGlvbiIsImlkIiwiYXR0ciIsInVybCIsImlzR2V0T3JQb3N0TWV0aG9kIiwiaW5jbHVkZXMiLCIkZm9ybSIsImFwcGVuZFRvIiwicG9zaXRpb25zTmIiLCJhcHBlbmQiLCJzdWJtaXQiLCJ0ZXh0VG9MaW5rUmV3cml0ZUNvcGllciIsInNvdXJjZUVsZW1lbnRTZWxlY3RvciIsImRlc3RpbmF0aW9uRWxlbWVudFNlbGVjdG9yIiwib3B0aW9ucyIsImV2ZW50TmFtZSIsImRvY3VtZW50Iiwib24iLCJldmVudCIsInZhbCIsInN0cjJ1cmwiLCJjdXJyZW50VGFyZ2V0IiwiR3JpZCIsIiRjb250YWluZXIiLCJleHRlbnNpb24iLCJGaWx0ZXJzUmVzZXRFeHRlbnNpb24iLCJTb3J0aW5nRXh0ZW5zaW9uIiwiJHNvcnRhYmxlVGFibGUiLCJUYWJsZVNvcnRpbmciLCJhdHRhY2giLCJSZWxvYWRMaXN0RXh0ZW5zaW9uIiwiZ2V0SGVhZGVyQ29udGFpbmVyIiwibG9jYXRpb24iLCJyZWxvYWQiLCJFeHBvcnRUb1NxbE1hbmFnZXJFeHRlbnNpb24iLCJfb25TaG93U3FsUXVlcnlDbGljayIsIl9vbkV4cG9ydFNxbE1hbmFnZXJDbGljayIsIiRzcWxNYW5hZ2VyRm9ybSIsIl9maWxsRXhwb3J0Rm9ybSIsIiRtb2RhbCIsIm1vZGFsIiwicXVlcnkiLCJfZ2V0TmFtZUZyb21CcmVhZGNydW1iIiwiJGJyZWFkY3J1bWJzIiwibmFtZSIsIml0ZW0iLCIkYnJlYWRjcnVtYiIsImJyZWFkY3J1bWJUaXRsZSIsInRleHQiLCJjb25jYXQiLCJGaWx0ZXJzU3VibWl0QnV0dG9uRW5hYmxlckV4dGVuc2lvbiIsIiRmaWx0ZXJzUm93IiwicHJvcCIsIkJ1bGtBY3Rpb25DaGVja2JveEV4dGVuc2lvbiIsIl9oYW5kbGVCdWxrQWN0aW9uQ2hlY2tib3hTZWxlY3QiLCJfaGFuZGxlQnVsa0FjdGlvblNlbGVjdEFsbENoZWNrYm94IiwiZSIsIiRjaGVja2JveCIsImlzQ2hlY2tlZCIsImlzIiwiX2VuYWJsZUJ1bGtBY3Rpb25zQnRuIiwiX2Rpc2FibGVCdWxrQWN0aW9uc0J0biIsImNoZWNrZWRSb3dzQ291bnQiLCJTdWJtaXRCdWxrQWN0aW9uRXh0ZW5zaW9uIiwiJHN1Ym1pdEJ0biIsImNvbmZpcm1NZXNzYWdlIiwiY29uZmlybVRpdGxlIiwidW5kZWZpbmVkIiwic2hvd0NvbmZpcm1Nb2RhbCIsImNvbmZpcm0iLCJwb3N0Rm9ybSIsImNvbmZpcm1CdXR0b25MYWJlbCIsImNsb3NlQnV0dG9uTGFiZWwiLCJjb25maXJtQnV0dG9uQ2xhc3MiLCJDb25maXJtTW9kYWwiLCJzaG93IiwiTGlua1Jvd0FjdGlvbkV4dGVuc2lvbiIsImluaXRSb3dMaW5rcyIsImluaXRDb25maXJtYWJsZUFjdGlvbnMiLCJwcmV2ZW50RGVmYXVsdCIsImluaXRFYWNoUm93IiwiJHBhcmVudFJvdyIsInByb3BhZ2F0ZUZpcnN0TGlua0FjdGlvbiIsIiRyb3dBY3Rpb24iLCIkcGFyZW50Q2VsbCIsImNsaWNrYWJsZUNlbGxzIiwibm90IiwiY2xpY2siLCJTdWJtaXRSb3dBY3Rpb25FeHRlbnNpb24iLCIkYnV0dG9uIiwiRXZlbnRFbWl0dGVyIiwiRXZlbnRFbWl0dGVyQ2xhc3MiLCJnbG9iYWwiLCJzZWxlY3RvciIsImNvbHVtbnMiLCIkY29sdW1uIiwiZGVsZWdhdGVUYXJnZXQiLCJfc29ydEJ5Q29sdW1uIiwiX2dldFRvZ2dsZWRTb3J0RGlyZWN0aW9uIiwiY29sdW1uTmFtZSIsImRpcmVjdGlvbiIsIkVycm9yIiwiY29sdW1uIiwiX2dldFVybCIsImNvbE5hbWUiLCJwcmVmaXgiLCJVUkwiLCJocmVmIiwic2VhcmNoUGFyYW1zIiwic2V0IiwidG9TdHJpbmciLCJpbml0IiwicmVzZXRTZWFyY2giLCJyZWRpcmVjdFVybCIsInBvc3QiLCJ0aGVuIiwiYXNzaWduIiwiVHJhbnNsYXRhYmxlSW5wdXQiLCJsb2NhbGVJdGVtU2VsZWN0b3IiLCJsb2NhbGVCdXR0b25TZWxlY3RvciIsImxvY2FsZUlucHV0U2VsZWN0b3IiLCJ0b2dnbGVMYW5ndWFnZSIsImJpbmQiLCJ0b2dnbGVJbnB1dHMiLCJsb2NhbGVJdGVtIiwidGFyZ2V0IiwiZm9ybSIsImVtaXQiLCJzZWxlY3RlZExvY2FsZSIsImxvY2FsZUJ1dHRvbiIsImNoYW5nZUxhbmd1YWdlVXJsIiwiX3NhdmVTZWxlY3RlZExhbmd1YWdlIiwibGFuZ3VhZ2VfaXNvX2NvZGUiLCJjb25maXJtQ2FsbGJhY2siLCJjbG9zYWJsZSIsIk1vZGFsIiwiY29udGFpbmVyIiwiY29uZmlybUJ1dHRvbiIsImFkZEV2ZW50TGlzdGVuZXIiLCJiYWNrZHJvcCIsImtleWJvYXJkIiwicXVlcnlTZWxlY3RvciIsInJlbW92ZSIsImJvZHkiLCJhcHBlbmRDaGlsZCIsImNyZWF0ZUVsZW1lbnQiLCJjbGFzc0xpc3QiLCJhZGQiLCJkaWFsb2ciLCJjb250ZW50IiwiaGVhZGVyIiwidGl0bGUiLCJpbm5lckhUTUwiLCJjbG9zZUljb24iLCJzZXRBdHRyaWJ1dGUiLCJkYXRhc2V0IiwiZGlzbWlzcyIsIm1lc3NhZ2UiLCJmb290ZXIiLCJjbG9zZUJ1dHRvbiIsImNtc0NhdGVnb3J5IiwiYWRkRXh0ZW5zaW9uIiwiUmVsb2FkTGlzdEFjdGlvbkV4dGVuc2lvbiIsIlN1Ym1pdEJ1bGtFeHRlbnNpb24iLCJDb2x1bW5Ub2dnbGluZ0V4dGVuc2lvbiIsInRyYW5zbGF0b3JJbnB1dCIsIkNob2ljZVRyZWUiLCJzaG9wQ2hvaWNlVHJlZSIsImVuYWJsZUF1dG9DaGVja0NoaWxkcmVuIiwiVGFnZ2FibGVGaWVsZCIsInRva2VuRmllbGRTZWxlY3RvciIsImNyZWF0ZVRva2Vuc09uQmx1ciIsImNtc0dyaWQiLCJoZWxwZXJCbG9jayIsIlNob3djYXNlQ2FyZCIsIlNob3djYXNlQ2FyZENsb3NlRXh0ZW5zaW9uIiwidHJlZVNlbGVjdG9yIiwiJGlucHV0V3JhcHBlciIsIl90b2dnbGVDaGlsZFRyZWUiLCIkYWN0aW9uIiwiX3RvZ2dsZVRyZWUiLCJlbmFibGVBbGxJbnB1dHMiLCJkaXNhYmxlQWxsSW5wdXRzIiwiJGNsaWNrZWRDaGVja2JveCIsIiRpdGVtV2l0aENoaWxkcmVuIiwicmVtb3ZlQXR0ciIsIiRwYXJlbnRXcmFwcGVyIiwiaGFzQ2xhc3MiLCIkcGFyZW50Q29udGFpbmVyIiwiYWN0aW9uIiwiY29uZmlnIiwiZXhwYW5kIiwiY29sbGFwc2UiLCJuZXh0QWN0aW9uIiwiaWNvbiIsIiRpdGVtIiwiJHRhYmxlIiwiX3RvZ2dsZVZhbHVlIiwidG9nZ2xlVXJsIiwiX3N1Ym1pdEFzRm9ybSIsImV2dCIsIiRidG4iLCJjYXJkTmFtZSIsImNsb3NlIiwidG9rZW5maWVsZCJdLCJtYXBwaW5ncyI6Ijs7QUFBQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTs7O0FBR0E7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0EsbURBQTJDLGNBQWM7O0FBRXpEO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsYUFBSztBQUNMO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0EsbUNBQTJCLDBCQUEwQixFQUFFO0FBQ3ZELHlDQUFpQyxlQUFlO0FBQ2hEO0FBQ0E7QUFDQTs7QUFFQTtBQUNBLDhEQUFzRCwrREFBK0Q7O0FBRXJIO0FBQ0E7O0FBRUE7QUFDQTs7Ozs7Ozs7O0FDaEVBOztBQUVBOztBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsRTs7Ozs7Ozs7QUNSQTs7QUFFQTs7QUFFQTs7QUFFQTs7QUFFQSxzQ0FBc0MsdUNBQXVDLGdCQUFnQjs7QUFFN0Y7QUFDQTtBQUNBLG1CQUFtQixrQkFBa0I7QUFDckM7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBLENBQUMsRzs7Ozs7OztBQzFCRDtBQUNBO0FBQ0E7QUFDQTtBQUNBLENBQUM7QUFDRDtBQUNBO0FBQ0EsRTs7Ozs7OztBQ1BBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsRTs7Ozs7OztBQ0pBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsRTs7Ozs7OztBQ1BBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsRTs7Ozs7OztBQ25CQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsRTs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7OztBQ2NBOzs7Ozs7QUFFQSxJQUFNQSxJQUFJQyxPQUFPRCxDQUFqQjs7QUFFQTs7O0FBN0JBOzs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7O0lBZ0NxQkUsaUI7QUFDbkIsK0JBQWM7QUFBQTs7QUFBQTs7QUFDWixXQUFPO0FBQ0xDLGNBQVEsZ0JBQUNDLElBQUQ7QUFBQSxlQUFVLE1BQUtELE1BQUwsQ0FBWUMsSUFBWixDQUFWO0FBQUE7QUFESCxLQUFQO0FBR0Q7O0FBRUQ7Ozs7Ozs7OzsyQkFLT0EsSSxFQUFNO0FBQUE7O0FBQ1gsV0FBS0EsSUFBTCxHQUFZQSxJQUFaO0FBQ0EsV0FBS0Msc0JBQUw7QUFDQUQsV0FBS0UsWUFBTCxHQUFvQkMsSUFBcEIsQ0FBeUIsZ0JBQXpCLEVBQTJDQyxRQUEzQyxDQUFvRDtBQUNsREMscUJBQWEseUJBRHFDO0FBRWxEQyxvQkFBWSxpQkFGc0M7QUFHbERDLGdCQUFRLGdCQUFDQyxLQUFELEVBQVFDLEdBQVI7QUFBQSxpQkFBZ0IsT0FBS0MscUJBQUwsQ0FBMkJELEdBQTNCLENBQWhCO0FBQUE7QUFIMEMsT0FBcEQ7QUFLQVQsV0FBS0UsWUFBTCxHQUFvQkMsSUFBcEIsQ0FBeUIsaUJBQXpCLEVBQTRDUSxLQUE1QyxDQUNFLFlBQVc7QUFDVGYsVUFBRSxJQUFGLEVBQVFnQixPQUFSLENBQWdCLElBQWhCLEVBQXNCQyxRQUF0QixDQUErQixPQUEvQjtBQUNELE9BSEgsRUFJRSxZQUFXO0FBQ1RqQixVQUFFLElBQUYsRUFBUWdCLE9BQVIsQ0FBZ0IsSUFBaEIsRUFBc0JFLFdBQXRCLENBQWtDLE9BQWxDO0FBQ0QsT0FOSDtBQVFEOztBQUVEOzs7Ozs7Ozs7OzBDQU9zQkwsRyxFQUFLO0FBQ3pCLFVBQU1NLHdCQUF3Qm5CLEVBQUVhLEdBQUYsRUFBT04sSUFBUCxDQUFZLFNBQVMsS0FBS0gsSUFBTCxDQUFVZ0IsS0FBVixFQUFULEdBQTZCLGlCQUF6QyxDQUE5QjtBQUNBLFVBQU1DLFlBQVlGLHNCQUFzQkcsSUFBdEIsQ0FBMkIsWUFBM0IsQ0FBbEI7QUFDQSxVQUFNQyxTQUFTSixzQkFBc0JHLElBQXRCLENBQTJCLGVBQTNCLENBQWY7QUFDQSxVQUFNRSxtQkFBbUJDLFNBQVNOLHNCQUFzQkcsSUFBdEIsQ0FBMkIsbUJBQTNCLENBQVQsRUFBMEQsRUFBMUQsQ0FBekI7QUFDQSxVQUFNSSxZQUFZLEtBQUtDLGlCQUFMLENBQXVCSCxnQkFBdkIsQ0FBbEI7QUFDQSxVQUFNSSxTQUFTLEVBQUNGLG9CQUFELEVBQWY7O0FBRUEsV0FBS0csZUFBTCxDQUFxQlIsU0FBckIsRUFBZ0NPLE1BQWhDLEVBQXdDTCxNQUF4QztBQUNEOztBQUVEOzs7Ozs7OztzQ0FLa0JDLGdCLEVBQWtCO0FBQ2xDLFVBQU1NLFlBQVlDLEtBQUtDLEtBQUwsQ0FBV2hDLEVBQUVRLFFBQUYsQ0FBV3lCLE9BQVgsRUFBWCxDQUFsQjtBQUNBLFVBQU1DLFdBQVdKLFVBQVUsS0FBSzFCLElBQUwsQ0FBVWdCLEtBQVYsS0FBa0IsYUFBNUIsQ0FBakI7QUFDQSxVQUFNZSxRQUFRLG1CQUFkOztBQUVBLFVBQU1DLFNBQVNGLFNBQVNHLE1BQXhCO0FBQ0EsVUFBTVgsWUFBWSxFQUFsQjtBQUNBLFVBQUlZLGdCQUFKO0FBQUEsVUFBYUMsVUFBYjtBQUNBLFdBQUtBLElBQUksQ0FBVCxFQUFZQSxJQUFJSCxNQUFoQixFQUF3QixFQUFFRyxDQUExQixFQUE2QjtBQUMzQkQsa0JBQVVILE1BQU1LLElBQU4sQ0FBV04sU0FBU0ssQ0FBVCxDQUFYLENBQVY7QUFDQWIsa0JBQVVlLElBQVYsQ0FBZTtBQUNiQyxpQkFBT0osUUFBUSxDQUFSLENBRE07QUFFYkssdUJBQWFuQixtQkFBbUJlLENBRm5CO0FBR2JLLHVCQUFhbkIsU0FBU2EsUUFBUSxDQUFSLENBQVQsRUFBcUIsRUFBckI7QUFIQSxTQUFmO0FBS0Q7O0FBRUQsYUFBT1osU0FBUDtBQUNEOztBQUVEOzs7Ozs7Ozs2Q0FLeUI7QUFDdkIsV0FBS3RCLElBQUwsQ0FBVUUsWUFBVixHQUNHQyxJQURILENBQ1Esd0JBQXdCLEtBQUtILElBQUwsQ0FBVWdCLEtBQVYsRUFBeEIsR0FBNEMsV0FEcEQsRUFFR3lCLElBRkgsQ0FFUSxVQUFDQyxLQUFELEVBQVFDLGVBQVIsRUFBNEI7QUFDaEMsWUFBTUMsbUJBQW1CaEQsRUFBRStDLGVBQUYsQ0FBekI7QUFDQSxZQUFNTCxRQUFRTSxpQkFBaUIxQixJQUFqQixDQUFzQixJQUF0QixDQUFkO0FBQ0EsWUFBTTJCLFdBQVdELGlCQUFpQjFCLElBQWpCLENBQXNCLFVBQXRCLENBQWpCO0FBQ0EsWUFBTTRCLGNBQVlSLEtBQVosU0FBcUJPLFFBQTNCO0FBQ0FELHlCQUFpQmhDLE9BQWpCLENBQXlCLElBQXpCLEVBQStCbUMsSUFBL0IsQ0FBb0MsSUFBcEMsRUFBMENELEVBQTFDO0FBQ0FGLHlCQUFpQmhDLE9BQWpCLENBQXlCLElBQXpCLEVBQStCQyxRQUEvQixDQUF3QyxnQkFBeEM7QUFDRCxPQVRIO0FBVUQ7O0FBRUQ7Ozs7Ozs7Ozs7OztvQ0FTZ0JtQyxHLEVBQUt4QixNLEVBQVFMLE0sRUFBUTtBQUNuQyxVQUFNOEIsb0JBQW9CLENBQUMsS0FBRCxFQUFRLE1BQVIsRUFBZ0JDLFFBQWhCLENBQXlCL0IsTUFBekIsQ0FBMUI7O0FBRUEsVUFBTWdDLFFBQVF2RCxFQUFFLFFBQUYsRUFBWTtBQUN4QixrQkFBVW9ELEdBRGM7QUFFeEIsa0JBQVVDLG9CQUFvQjlCLE1BQXBCLEdBQTZCO0FBRmYsT0FBWixFQUdYaUMsUUFIVyxDQUdGLE1BSEUsQ0FBZDs7QUFLQSxVQUFNQyxjQUFjN0IsT0FBT0YsU0FBUCxDQUFpQlcsTUFBckM7QUFDQSxVQUFJWSxpQkFBSjtBQUNBLFdBQUssSUFBSVYsSUFBSSxDQUFiLEVBQWdCQSxJQUFJa0IsV0FBcEIsRUFBaUMsRUFBRWxCLENBQW5DLEVBQXNDO0FBQ3BDVSxtQkFBV3JCLE9BQU9GLFNBQVAsQ0FBaUJhLENBQWpCLENBQVg7QUFDQWdCLGNBQU1HLE1BQU4sQ0FDRTFELEVBQUUsU0FBRixFQUFhO0FBQ1gsa0JBQVEsUUFERztBQUVYLGtCQUFRLGVBQWF1QyxDQUFiLEdBQWUsVUFGWjtBQUdYLG1CQUFTVSxTQUFTUDtBQUhQLFNBQWIsQ0FERixFQU1FMUMsRUFBRSxTQUFGLEVBQWE7QUFDWCxrQkFBUSxRQURHO0FBRVgsa0JBQVEsZUFBYXVDLENBQWIsR0FBZSxnQkFGWjtBQUdYLG1CQUFTVSxTQUFTTDtBQUhQLFNBQWIsQ0FORixFQVdFNUMsRUFBRSxTQUFGLEVBQWE7QUFDWCxrQkFBUSxRQURHO0FBRVgsa0JBQVEsZUFBYXVDLENBQWIsR0FBZSxnQkFGWjtBQUdYLG1CQUFTVSxTQUFTTjtBQUhQLFNBQWIsQ0FYRjtBQWlCRDs7QUFFRDtBQUNBLFVBQUksQ0FBQ1UsaUJBQUwsRUFBd0I7QUFDdEJFLGNBQU1HLE1BQU4sQ0FBYTFELEVBQUUsU0FBRixFQUFhO0FBQ3hCLGtCQUFRLFFBRGdCO0FBRXhCLGtCQUFRLFNBRmdCO0FBR3hCLG1CQUFTdUI7QUFIZSxTQUFiLENBQWI7QUFLRDs7QUFFRGdDLFlBQU1JLE1BQU47QUFDRDs7Ozs7a0JBN0lrQnpELGlCOzs7Ozs7Ozs7Ozs7O0FDaENyQjs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7OztBQXlCQSxJQUFNRixJQUFJQyxPQUFPRCxDQUFqQjs7QUFFQTs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7QUE2QkEsSUFBTTRELDBCQUEwQixTQUExQkEsdUJBQTBCLE9BQXlGO0FBQUEsTUFBdkZDLHFCQUF1RixRQUF2RkEscUJBQXVGO0FBQUEsTUFBaEVDLDBCQUFnRSxRQUFoRUEsMEJBQWdFO0FBQUEsMEJBQXBDQyxPQUFvQztBQUFBLE1BQXBDQSxPQUFvQyxnQ0FBMUIsRUFBQ0MsV0FBVyxPQUFaLEVBQTBCOztBQUN2SGhFLElBQUVpRSxRQUFGLEVBQVlDLEVBQVosQ0FBZUgsUUFBUUMsU0FBdkIsT0FBcUNILHFCQUFyQyxFQUE4RCxVQUFDTSxLQUFELEVBQVc7QUFDdkVuRSxNQUFFOEQsMEJBQUYsRUFBOEJNLEdBQTlCLENBQWtDQyxRQUFRckUsRUFBRW1FLE1BQU1HLGFBQVIsRUFBdUJGLEdBQXZCLEVBQVIsRUFBc0MsT0FBdEMsQ0FBbEM7QUFDRCxHQUZEO0FBR0QsQ0FKRDs7a0JBTWVSLHVCOzs7Ozs7O0FDOURmO0FBQ0EsbUJBQW1CLDBFQUEwRSxzQkFBc0IsY0FBYyxZQUFZLGdCQUFnQixZQUFZLFNBQVMsK0JBQStCLFNBQVMsMkJBQTJCLGlEQUFpRCw0dEJBQTR0QixvWUFBb1ksRUFBRSxFQUFFLG1CQUFtQixtRkFBbUYsNEJBQTRCLDhCQUE4QixxTUFBcU0sMklBQTJJLE1BQU0sbUdBQW1HLE9BQU8sMEJBQTBCLCtFQUErRSxzQ0FBc0MsaURBQWlELG9CQUFvQixFQUFFLFlBQVksV0FBVyw2RkFBNkYsY0FBYyxhQUFhLE1BQU0sbUJBQW1CLHVEQUF1RCxpQkFBaUIsb0JBQW9CLHFCQUFxQixtQkFBbUIseURBQXlELDhDQUE4QyxpR0FBaUcsWUFBWSx3QkFBd0IsdURBQXVELE9BQU8sMkJBQTJCLHVCQUF1QixnREFBZ0QsMkJBQTJCLHlFQUF5RSxFQUFFLDZCQUE2QiwrRUFBK0UsZ0ZBQWdGLHVCQUF1QixFQUFFLHlCQUF5Qiw2QkFBNkIsMkJBQTJCLGtEQUFrRCxXQUFXLG9DQUFvQywwTUFBME0seUJBQXlCLHFCQUFxQixvREFBb0QsRUFBRSx5QkFBeUIsdUNBQXVDLHdGQUF3RixtQkFBbUIsb0JBQW9CLEVBQUUsK0ZBQStGLDhCQUE4QixRQUFRLGlFQUFpRSxxQkFBcUIseUJBQXlCLFlBQVkseUNBQXlDLGVBQWUsaURBQWlELHVDQUF1QyxTQUFTLHdCQUF3Qix1S0FBdUssNE9BQTRPLDRCQUE0QixvUEFBb1AsOEJBQThCLHlDQUF5Qyw0RUFBNEUsZ1FBQWdRLHVCQUF1QixrRkFBa0YsOFpBQThaLGlDQUFpQyxzR0FBc0csaUVBQWlFLHVFQUF1RSxpQ0FBaUMsdUZBQXVGLFdBQVcsb1FBQW9RLFlBQVksMkJBQTJCLG9EQUFvRCxpRUFBaUUsMktBQTJLLCtHQUErRyxpRUFBaUUsa0VBQWtFLE1BQU0sZ0ZBQWdGLG9SQUFvUixxQkFBcUIsNERBQTRELHFCQUFxQix3QkFBd0Isd0hBQXdILHNCQUFzQixrREFBa0QsNEJBQTRCLHNFQUFzRSxXQUFXLEtBQUsscUJBQXFCLGNBQWMscUhBQXFILFNBQVMsNEJBQTRCLFNBQVMsa0NBQWtDLHFEQUFxRCxjQUFjLHVCQUF1Qix3REFBd0QsK0RBQStELE9BQU8sd0NBQXdDLHVDQUF1QyxPQUFPLHlEQUF5RCxtR0FBbUcsK0RBQStELGtFQUFrRSxlQUFlLEVBQUUsWUFBWSxXQUFXLHlCQUF5Qiw2Q0FBNkMseUNBQXlDLHdCQUF3QixXQUFXLHFEQUFxRCw0REFBNEQsaUNBQWlDLFVBQVUsbUJBQW1CLGtPQUFrTyxFQUFFLGdDOzs7Ozs7OztBQ0QzcVM7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsRTs7Ozs7OztBQ05BO0FBQ0EscUVBQXNFLGdCQUFnQixVQUFVLEdBQUc7QUFDbkcsQ0FBQyxFOzs7Ozs7O0FDRkQ7QUFDQTtBQUNBO0FBQ0EsRTs7Ozs7OztBQ0hBLGtCQUFrQix3RDs7Ozs7OztBQ0FsQjtBQUNBO0FBQ0EsaUNBQWlDLFFBQVEsZ0JBQWdCLFVBQVUsR0FBRztBQUN0RSxDQUFDLEU7Ozs7Ozs7QUNIRDtBQUNBO0FBQ0E7QUFDQTtBQUNBLEU7Ozs7Ozs7QUNKQTtBQUNBO0FBQ0Esb0VBQXVFLHlDQUEwQyxFOzs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7QUNGakg7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7QUF5QkEsSUFBTTVELElBQUlDLE9BQU9ELENBQWpCOztBQUVBOzs7O0lBR3FCdUUsSTtBQUNuQjs7Ozs7QUFLQSxnQkFBWXJCLEVBQVosRUFBZ0I7QUFBQTs7QUFDZCxTQUFLQSxFQUFMLEdBQVVBLEVBQVY7QUFDQSxTQUFLc0IsVUFBTCxHQUFrQnhFLEVBQUUsTUFBTSxLQUFLa0QsRUFBWCxHQUFnQixPQUFsQixDQUFsQjtBQUNEOztBQUVEOzs7Ozs7Ozs7NEJBS1E7QUFDTixhQUFPLEtBQUtBLEVBQVo7QUFDRDs7QUFFRDs7Ozs7Ozs7bUNBS2U7QUFDYixhQUFPLEtBQUtzQixVQUFaO0FBQ0Q7O0FBRUQ7Ozs7Ozs7O3lDQUtxQjtBQUNuQixhQUFPLEtBQUtBLFVBQUwsQ0FBZ0J4RCxPQUFoQixDQUF3QixnQkFBeEIsRUFBMENULElBQTFDLENBQStDLGlCQUEvQyxDQUFQO0FBQ0Q7O0FBRUQ7Ozs7Ozs7O2lDQUtha0UsUyxFQUFXO0FBQ3RCQSxnQkFBVXRFLE1BQVYsQ0FBaUIsSUFBakI7QUFDRDs7Ozs7a0JBN0NrQm9FLEk7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7QUNMckI7Ozs7OztBQUVBLElBQU12RSxJQUFJQyxPQUFPRCxDQUFqQjs7QUFFQTs7O0FBN0JBOzs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7O0lBZ0NxQjBFLHFCOzs7Ozs7Ozs7QUFFbkI7Ozs7OzJCQUtPdEUsSSxFQUFNO0FBQ1hBLFdBQUtFLFlBQUwsR0FBb0I0RCxFQUFwQixDQUF1QixPQUF2QixFQUFnQyxrQkFBaEMsRUFBb0QsVUFBQ0MsS0FBRCxFQUFXO0FBQzdELG9DQUFZbkUsRUFBRW1FLE1BQU1HLGFBQVIsRUFBdUJoRCxJQUF2QixDQUE0QixLQUE1QixDQUFaLEVBQWdEdEIsRUFBRW1FLE1BQU1HLGFBQVIsRUFBdUJoRCxJQUF2QixDQUE0QixVQUE1QixDQUFoRDtBQUNELE9BRkQ7QUFHRDs7Ozs7a0JBWGtCb0QscUI7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7QUNQckI7Ozs7OztBQUVBOzs7SUFHcUJDLGdCOzs7Ozs7OztBQUNuQjs7Ozs7MkJBS092RSxJLEVBQU07QUFDWCxVQUFNd0UsaUJBQWlCeEUsS0FBS0UsWUFBTCxHQUFvQkMsSUFBcEIsQ0FBeUIsYUFBekIsQ0FBdkI7O0FBRUEsVUFBSXNFLHNCQUFKLENBQWlCRCxjQUFqQixFQUFpQ0UsTUFBakM7QUFDRDs7O0tBeENIOzs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7O2tCQThCcUJILGdCOzs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7QUM5QnJCOzs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7O0FBeUJBOzs7SUFHcUJJLG1COzs7Ozs7OztBQUNuQjs7Ozs7MkJBS08zRSxJLEVBQU07QUFDWEEsV0FBSzRFLGtCQUFMLEdBQTBCZCxFQUExQixDQUE2QixPQUE3QixFQUFzQyxxQ0FBdEMsRUFBNkUsWUFBTTtBQUNqRmUsaUJBQVNDLE1BQVQ7QUFDRCxPQUZEO0FBR0Q7Ozs7O2tCQVZrQkgsbUI7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7OztBQzVCckI7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7QUF5QkEsSUFBTS9FLElBQUlDLE9BQU9ELENBQWpCOztBQUVBOzs7O0lBR3FCbUYsMkI7Ozs7Ozs7O0FBQ25COzs7OzsyQkFLTy9FLEksRUFBTTtBQUFBOztBQUNYQSxXQUFLNEUsa0JBQUwsR0FBMEJkLEVBQTFCLENBQTZCLE9BQTdCLEVBQXNDLG1DQUF0QyxFQUEyRTtBQUFBLGVBQU0sTUFBS2tCLG9CQUFMLENBQTBCaEYsSUFBMUIsQ0FBTjtBQUFBLE9BQTNFO0FBQ0FBLFdBQUs0RSxrQkFBTCxHQUEwQmQsRUFBMUIsQ0FBNkIsT0FBN0IsRUFBc0MsMkNBQXRDLEVBQW1GO0FBQUEsZUFBTSxNQUFLbUIsd0JBQUwsQ0FBOEJqRixJQUE5QixDQUFOO0FBQUEsT0FBbkY7QUFDRDs7QUFFRDs7Ozs7Ozs7Ozt5Q0FPcUJBLEksRUFBTTtBQUN6QixVQUFNa0Ysa0JBQWtCdEYsRUFBRSxNQUFNSSxLQUFLZ0IsS0FBTCxFQUFOLEdBQXFCLCtCQUF2QixDQUF4QjtBQUNBLFdBQUttRSxlQUFMLENBQXFCRCxlQUFyQixFQUFzQ2xGLElBQXRDOztBQUVBLFVBQU1vRixTQUFTeEYsRUFBRSxNQUFNSSxLQUFLZ0IsS0FBTCxFQUFOLEdBQXFCLCtCQUF2QixDQUFmO0FBQ0FvRSxhQUFPQyxLQUFQLENBQWEsTUFBYjs7QUFFQUQsYUFBT3RCLEVBQVAsQ0FBVSxPQUFWLEVBQW1CLGlCQUFuQixFQUFzQztBQUFBLGVBQU1vQixnQkFBZ0IzQixNQUFoQixFQUFOO0FBQUEsT0FBdEM7QUFDRDs7QUFFRDs7Ozs7Ozs7Ozs2Q0FPeUJ2RCxJLEVBQU07QUFDN0IsVUFBTWtGLGtCQUFrQnRGLEVBQUUsTUFBTUksS0FBS2dCLEtBQUwsRUFBTixHQUFxQiwrQkFBdkIsQ0FBeEI7O0FBRUEsV0FBS21FLGVBQUwsQ0FBcUJELGVBQXJCLEVBQXNDbEYsSUFBdEM7O0FBRUFrRixzQkFBZ0IzQixNQUFoQjtBQUNEOztBQUVEOzs7Ozs7Ozs7OztvQ0FRZ0IyQixlLEVBQWlCbEYsSSxFQUFNO0FBQ3JDLFVBQU1zRixRQUFRdEYsS0FBS0UsWUFBTCxHQUFvQkMsSUFBcEIsQ0FBeUIsZ0JBQXpCLEVBQTJDZSxJQUEzQyxDQUFnRCxPQUFoRCxDQUFkOztBQUVBZ0Usc0JBQWdCL0UsSUFBaEIsQ0FBcUIsc0JBQXJCLEVBQTZDNkQsR0FBN0MsQ0FBaURzQixLQUFqRDtBQUNBSixzQkFBZ0IvRSxJQUFoQixDQUFxQixvQkFBckIsRUFBMkM2RCxHQUEzQyxDQUErQyxLQUFLdUIsc0JBQUwsRUFBL0M7QUFDRDs7QUFFRDs7Ozs7Ozs7Ozs2Q0FPeUI7QUFDdkIsVUFBTUMsZUFBZTVGLEVBQUUsaUJBQUYsRUFBcUJPLElBQXJCLENBQTBCLGtCQUExQixDQUFyQjtBQUNBLFVBQUlzRixPQUFPLEVBQVg7O0FBRUFELG1CQUFhL0MsSUFBYixDQUFrQixVQUFDTixDQUFELEVBQUl1RCxJQUFKLEVBQWE7QUFDN0IsWUFBTUMsY0FBYy9GLEVBQUU4RixJQUFGLENBQXBCOztBQUVBLFlBQU1FLGtCQUFrQixJQUFJRCxZQUFZeEYsSUFBWixDQUFpQixHQUFqQixFQUFzQjhCLE1BQTFCLEdBQ3RCMEQsWUFBWXhGLElBQVosQ0FBaUIsR0FBakIsRUFBc0IwRixJQUF0QixFQURzQixHQUV0QkYsWUFBWUUsSUFBWixFQUZGOztBQUlBLFlBQUksSUFBSUosS0FBS3hELE1BQWIsRUFBcUI7QUFDbkJ3RCxpQkFBT0EsS0FBS0ssTUFBTCxDQUFZLEtBQVosQ0FBUDtBQUNEOztBQUVETCxlQUFPQSxLQUFLSyxNQUFMLENBQVlGLGVBQVosQ0FBUDtBQUNELE9BWkQ7O0FBY0EsYUFBT0gsSUFBUDtBQUNEOzs7OztrQkFwRmtCViwyQjs7Ozs7OztBQzlCckIsNkJBQTZCO0FBQzdCLHFDQUFxQyxnQzs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7O0FDRHJDOzs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7O0FBeUJBOzs7SUFHcUJnQixtQzs7Ozs7Ozs7O0FBRW5COzs7OzsyQkFLTy9GLEksRUFBTTtBQUNYLFVBQU1nRyxjQUFjaEcsS0FBS0UsWUFBTCxHQUFvQkMsSUFBcEIsQ0FBeUIsaUJBQXpCLENBQXBCO0FBQ0E2RixrQkFBWTdGLElBQVosQ0FBaUIscUJBQWpCLEVBQXdDOEYsSUFBeEMsQ0FBNkMsVUFBN0MsRUFBeUQsSUFBekQ7O0FBRUFELGtCQUFZN0YsSUFBWixDQUFpQiwrQ0FBakIsRUFBa0UyRCxFQUFsRSxDQUFxRSxpQkFBckUsRUFBd0YsWUFBTTtBQUM1RmtDLG9CQUFZN0YsSUFBWixDQUFpQixxQkFBakIsRUFBd0M4RixJQUF4QyxDQUE2QyxVQUE3QyxFQUF5RCxLQUF6RDtBQUNBRCxvQkFBWTdGLElBQVosQ0FBaUIsdUJBQWpCLEVBQTBDOEYsSUFBMUMsQ0FBK0MsUUFBL0MsRUFBeUQsS0FBekQ7QUFDRCxPQUhEO0FBSUQ7Ozs7O2tCQWZrQkYsbUM7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7OztBQzVCckI7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7QUF5QkEsSUFBTW5HLElBQUlDLE9BQU9ELENBQWpCOztBQUVBOzs7O0lBR3FCc0csMkI7Ozs7Ozs7O0FBQ25COzs7OzsyQkFLT2xHLEksRUFBTTtBQUNYLFdBQUttRywrQkFBTCxDQUFxQ25HLElBQXJDO0FBQ0EsV0FBS29HLGtDQUFMLENBQXdDcEcsSUFBeEM7QUFDRDs7QUFFRDs7Ozs7Ozs7Ozt1REFPbUNBLEksRUFBTTtBQUFBOztBQUN2Q0EsV0FBS0UsWUFBTCxHQUFvQjRELEVBQXBCLENBQXVCLFFBQXZCLEVBQWlDLDRCQUFqQyxFQUErRCxVQUFDdUMsQ0FBRCxFQUFPO0FBQ3BFLFlBQU1DLFlBQVkxRyxFQUFFeUcsRUFBRW5DLGFBQUosQ0FBbEI7O0FBRUEsWUFBTXFDLFlBQVlELFVBQVVFLEVBQVYsQ0FBYSxVQUFiLENBQWxCO0FBQ0EsWUFBSUQsU0FBSixFQUFlO0FBQ2IsZ0JBQUtFLHFCQUFMLENBQTJCekcsSUFBM0I7QUFDRCxTQUZELE1BRU87QUFDTCxnQkFBSzBHLHNCQUFMLENBQTRCMUcsSUFBNUI7QUFDRDs7QUFFREEsYUFBS0UsWUFBTCxHQUFvQkMsSUFBcEIsQ0FBeUIsMEJBQXpCLEVBQXFEOEYsSUFBckQsQ0FBMEQsU0FBMUQsRUFBcUVNLFNBQXJFO0FBQ0QsT0FYRDtBQVlEOztBQUVEOzs7Ozs7Ozs7O29EQU9nQ3ZHLEksRUFBTTtBQUFBOztBQUNwQ0EsV0FBS0UsWUFBTCxHQUFvQjRELEVBQXBCLENBQXVCLFFBQXZCLEVBQWlDLDBCQUFqQyxFQUE2RCxZQUFNO0FBQ2pFLFlBQU02QyxtQkFBbUIzRyxLQUFLRSxZQUFMLEdBQW9CQyxJQUFwQixDQUF5QixrQ0FBekIsRUFBNkQ4QixNQUF0Rjs7QUFFQSxZQUFJMEUsbUJBQW1CLENBQXZCLEVBQTBCO0FBQ3hCLGlCQUFLRixxQkFBTCxDQUEyQnpHLElBQTNCO0FBQ0QsU0FGRCxNQUVPO0FBQ0wsaUJBQUswRyxzQkFBTCxDQUE0QjFHLElBQTVCO0FBQ0Q7QUFDRixPQVJEO0FBU0Q7O0FBRUQ7Ozs7Ozs7Ozs7MENBT3NCQSxJLEVBQU07QUFDMUJBLFdBQUtFLFlBQUwsR0FBb0JDLElBQXBCLENBQXlCLHNCQUF6QixFQUFpRDhGLElBQWpELENBQXNELFVBQXRELEVBQWtFLEtBQWxFO0FBQ0Q7O0FBRUQ7Ozs7Ozs7Ozs7MkNBT3VCakcsSSxFQUFNO0FBQzNCQSxXQUFLRSxZQUFMLEdBQW9CQyxJQUFwQixDQUF5QixzQkFBekIsRUFBaUQ4RixJQUFqRCxDQUFzRCxVQUF0RCxFQUFrRSxJQUFsRTtBQUNEOzs7OztrQkF4RWtCQywyQjs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7OztBQ0xyQjs7Ozs7O0FBRUEsSUFBTXRHLElBQUlDLE9BQU9ELENBQWpCOztBQUVBOzs7QUE3QkE7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7SUFnQ3FCZ0gseUI7QUFDbkIsdUNBQWM7QUFBQTs7QUFBQTs7QUFDWixXQUFPO0FBQ0w3RyxjQUFRLGdCQUFDQyxJQUFEO0FBQUEsZUFBVSxNQUFLRCxNQUFMLENBQVlDLElBQVosQ0FBVjtBQUFBO0FBREgsS0FBUDtBQUdEOztBQUVEOzs7Ozs7Ozs7MkJBS09BLEksRUFBTTtBQUFBOztBQUNYQSxXQUFLRSxZQUFMLEdBQW9CNEQsRUFBcEIsQ0FBdUIsT0FBdkIsRUFBZ0MsNEJBQWhDLEVBQThELFVBQUNDLEtBQUQsRUFBVztBQUN2RSxlQUFLUixNQUFMLENBQVlRLEtBQVosRUFBbUIvRCxJQUFuQjtBQUNELE9BRkQ7QUFHRDs7QUFFRDs7Ozs7Ozs7Ozs7MkJBUU8rRCxLLEVBQU8vRCxJLEVBQU07QUFDbEIsVUFBTTZHLGFBQWFqSCxFQUFFbUUsTUFBTUcsYUFBUixDQUFuQjtBQUNBLFVBQU00QyxpQkFBaUJELFdBQVczRixJQUFYLENBQWdCLGlCQUFoQixDQUF2QjtBQUNBLFVBQU02RixlQUFlRixXQUFXM0YsSUFBWCxDQUFnQixjQUFoQixDQUFyQjs7QUFFQSxVQUFJNEYsbUJBQW1CRSxTQUFuQixJQUFnQyxJQUFJRixlQUFlN0UsTUFBdkQsRUFBK0Q7QUFDN0QsWUFBSThFLGlCQUFpQkMsU0FBckIsRUFBZ0M7QUFDOUIsZUFBS0MsZ0JBQUwsQ0FBc0JKLFVBQXRCLEVBQWtDN0csSUFBbEMsRUFBd0M4RyxjQUF4QyxFQUF3REMsWUFBeEQ7QUFDRCxTQUZELE1BRU8sSUFBSUcsUUFBUUosY0FBUixDQUFKLEVBQTZCO0FBQ2xDLGVBQUtLLFFBQUwsQ0FBY04sVUFBZCxFQUEwQjdHLElBQTFCO0FBQ0Q7QUFDRixPQU5ELE1BTU87QUFDTCxhQUFLbUgsUUFBTCxDQUFjTixVQUFkLEVBQTBCN0csSUFBMUI7QUFDRDtBQUNGOztBQUVEOzs7Ozs7Ozs7cUNBTWlCNkcsVSxFQUFZN0csSSxFQUFNOEcsYyxFQUFnQkMsWSxFQUFjO0FBQUE7O0FBQy9ELFVBQU1LLHFCQUFxQlAsV0FBVzNGLElBQVgsQ0FBZ0Isb0JBQWhCLENBQTNCO0FBQ0EsVUFBTW1HLG1CQUFtQlIsV0FBVzNGLElBQVgsQ0FBZ0Isa0JBQWhCLENBQXpCO0FBQ0EsVUFBTW9HLHFCQUFxQlQsV0FBVzNGLElBQVgsQ0FBZ0Isb0JBQWhCLENBQTNCOztBQUVBLFVBQU1tRSxRQUFRLElBQUlrQyxlQUFKLENBQWlCO0FBQzdCekUsWUFBTzlDLEtBQUtnQixLQUFMLEVBQVAsd0JBRDZCO0FBRTdCK0Ysa0NBRjZCO0FBRzdCRCxzQ0FINkI7QUFJN0JNLDhDQUo2QjtBQUs3QkMsMENBTDZCO0FBTTdCQztBQU42QixPQUFqQixFQU9YO0FBQUEsZUFBTSxPQUFLSCxRQUFMLENBQWNOLFVBQWQsRUFBMEI3RyxJQUExQixDQUFOO0FBQUEsT0FQVyxDQUFkOztBQVNBcUYsWUFBTW1DLElBQU47QUFDRDs7QUFFRDs7Ozs7Ozs2QkFJU1gsVSxFQUFZN0csSSxFQUFNO0FBQ3pCLFVBQU1tRCxRQUFRdkQsRUFBRSxNQUFNSSxLQUFLZ0IsS0FBTCxFQUFOLEdBQXFCLGNBQXZCLENBQWQ7O0FBRUFtQyxZQUFNSixJQUFOLENBQVcsUUFBWCxFQUFxQjhELFdBQVczRixJQUFYLENBQWdCLFVBQWhCLENBQXJCO0FBQ0FpQyxZQUFNSixJQUFOLENBQVcsUUFBWCxFQUFxQjhELFdBQVczRixJQUFYLENBQWdCLGFBQWhCLENBQXJCO0FBQ0FpQyxZQUFNSSxNQUFOO0FBQ0Q7Ozs7O2tCQTNFa0JxRCx5Qjs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7O0FDaENyQjs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7OztBQXlCQSxJQUFNaEgsSUFBSUMsT0FBT0QsQ0FBakI7O0FBRUE7Ozs7SUFHcUI2SCxzQjs7Ozs7Ozs7QUFDbkI7Ozs7OzJCQUtPekgsSSxFQUFNO0FBQ1gsV0FBSzBILFlBQUwsQ0FBa0IxSCxJQUFsQjtBQUNBLFdBQUsySCxzQkFBTCxDQUE0QjNILElBQTVCO0FBQ0Q7O0FBRUQ7Ozs7Ozs7OzJDQUt1QkEsSSxFQUFNO0FBQzNCQSxXQUFLRSxZQUFMLEdBQW9CNEQsRUFBcEIsQ0FBdUIsT0FBdkIsRUFBZ0MscUJBQWhDLEVBQXVELFVBQUNDLEtBQUQsRUFBVztBQUNoRSxZQUFNK0MsaUJBQWlCbEgsRUFBRW1FLE1BQU1HLGFBQVIsRUFBdUJoRCxJQUF2QixDQUE0QixpQkFBNUIsQ0FBdkI7O0FBRUEsWUFBSTRGLGVBQWU3RSxNQUFmLElBQXlCLENBQUNpRixRQUFRSixjQUFSLENBQTlCLEVBQXVEO0FBQ3JEL0MsZ0JBQU02RCxjQUFOO0FBQ0Q7QUFDRixPQU5EO0FBT0Q7O0FBRUQ7Ozs7Ozs7O2lDQUthNUgsSSxFQUFNO0FBQ2pCSixRQUFFLElBQUYsRUFBUUksS0FBS0UsWUFBTCxFQUFSLEVBQTZCdUMsSUFBN0IsQ0FBa0MsU0FBU29GLFdBQVQsR0FBdUI7QUFDdkQsWUFBTUMsYUFBYWxJLEVBQUUsSUFBRixDQUFuQjs7QUFFQUEsVUFBRSxpREFBRixFQUFxRGtJLFVBQXJELEVBQWlFckYsSUFBakUsQ0FBc0UsU0FBU3NGLHdCQUFULEdBQW9DO0FBQ3hHLGNBQU1DLGFBQWFwSSxFQUFFLElBQUYsQ0FBbkI7QUFDQSxjQUFNcUksY0FBY0QsV0FBV3BILE9BQVgsQ0FBbUIsSUFBbkIsQ0FBcEI7O0FBRUEsY0FBTXNILGlCQUFpQnRJLEVBQUUsY0FBRixFQUFrQmtJLFVBQWxCLEVBQ3BCSyxHQURvQixDQUNoQkYsV0FEZ0IsQ0FBdkI7O0FBSUFDLHlCQUFlckgsUUFBZixDQUF3QixnQkFBeEIsRUFBMEN1SCxLQUExQyxDQUFnRCxZQUFNO0FBQ3BELGdCQUFNdEIsaUJBQWlCa0IsV0FBVzlHLElBQVgsQ0FBZ0IsaUJBQWhCLENBQXZCOztBQUVBLGdCQUFJLENBQUM0RixlQUFlN0UsTUFBaEIsSUFBMEJpRixRQUFRSixjQUFSLENBQTlCLEVBQXVEO0FBQ3JEakQsdUJBQVNnQixRQUFULEdBQW9CbUQsV0FBV2pGLElBQVgsQ0FBZ0IsTUFBaEIsQ0FBcEI7QUFDRDtBQUNGLFdBTkQ7QUFPRCxTQWZEO0FBZ0JELE9BbkJEO0FBb0JEOzs7OztrQkFwRGtCMEUsc0I7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7OztBQzlCckI7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7QUF5QkEsSUFBTTdILElBQUlDLE9BQU9ELENBQWpCOztBQUVBOzs7O0lBR3FCeUksd0I7Ozs7Ozs7O0FBQ25COzs7OzsyQkFLT3JJLEksRUFBTTtBQUNYQSxXQUFLRSxZQUFMLEdBQW9CNEQsRUFBcEIsQ0FBdUIsT0FBdkIsRUFBZ0MsdUJBQWhDLEVBQXlELFVBQUNDLEtBQUQsRUFBVztBQUNsRUEsY0FBTTZELGNBQU47O0FBRUEsWUFBTVUsVUFBVTFJLEVBQUVtRSxNQUFNRyxhQUFSLENBQWhCO0FBQ0EsWUFBTTRDLGlCQUFpQndCLFFBQVFwSCxJQUFSLENBQWEsaUJBQWIsQ0FBdkI7O0FBRUEsWUFBSTRGLGVBQWU3RSxNQUFmLElBQXlCLENBQUNpRixRQUFRSixjQUFSLENBQTlCLEVBQXVEO0FBQ3JEO0FBQ0Q7O0FBRUQsWUFBTTNGLFNBQVNtSCxRQUFRcEgsSUFBUixDQUFhLFFBQWIsQ0FBZjtBQUNBLFlBQU0rQixvQkFBb0IsQ0FBQyxLQUFELEVBQVEsTUFBUixFQUFnQkMsUUFBaEIsQ0FBeUIvQixNQUF6QixDQUExQjs7QUFFQSxZQUFNZ0MsUUFBUXZELEVBQUUsUUFBRixFQUFZO0FBQ3hCLG9CQUFVMEksUUFBUXBILElBQVIsQ0FBYSxLQUFiLENBRGM7QUFFeEIsb0JBQVUrQixvQkFBb0I5QixNQUFwQixHQUE2QjtBQUZmLFNBQVosRUFHWGlDLFFBSFcsQ0FHRixNQUhFLENBQWQ7O0FBS0EsWUFBSSxDQUFDSCxpQkFBTCxFQUF3QjtBQUN0QkUsZ0JBQU1HLE1BQU4sQ0FBYTFELEVBQUUsU0FBRixFQUFhO0FBQ3hCLG9CQUFRLFNBRGdCO0FBRXhCLG9CQUFRLFNBRmdCO0FBR3hCLHFCQUFTdUI7QUFIZSxXQUFiLENBQWI7QUFLRDs7QUFFRGdDLGNBQU1JLE1BQU47QUFDRCxPQTNCRDtBQTRCRDs7Ozs7a0JBbkNrQjhFLHdCOzs7Ozs7Ozs7Ozs7Ozs7QUNMckI7Ozs7OztBQUVBOzs7O0FBSU8sSUFBTUUsc0NBQWUsSUFBSUMsZ0JBQUosRUFBckIsQyxDQS9CUDs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7O0FDQUE7QUFDQTtBQUNBLEU7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7OztBQ0ZBOzs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7O0FBeUJBLElBQU01SSxJQUFJNkksT0FBTzdJLENBQWpCOztBQUVBOzs7OztJQUlNNkUsWTs7QUFFSjs7O0FBR0Esd0JBQVlqRSxLQUFaLEVBQW1CO0FBQUE7O0FBQ2pCLFNBQUtrSSxRQUFMLEdBQWdCLHFCQUFoQjtBQUNBLFNBQUtDLE9BQUwsR0FBZS9JLEVBQUVZLEtBQUYsRUFBU0wsSUFBVCxDQUFjLEtBQUt1SSxRQUFuQixDQUFmO0FBQ0Q7O0FBRUQ7Ozs7Ozs7NkJBR1M7QUFBQTs7QUFDUCxXQUFLQyxPQUFMLENBQWE3RSxFQUFiLENBQWdCLE9BQWhCLEVBQXlCLFVBQUN1QyxDQUFELEVBQU87QUFDOUIsWUFBTXVDLFVBQVVoSixFQUFFeUcsRUFBRXdDLGNBQUosQ0FBaEI7QUFDQSxjQUFLQyxhQUFMLENBQW1CRixPQUFuQixFQUE0QixNQUFLRyx3QkFBTCxDQUE4QkgsT0FBOUIsQ0FBNUI7QUFDRCxPQUhEO0FBSUQ7O0FBRUQ7Ozs7Ozs7OzJCQUtPSSxVLEVBQVlDLFMsRUFBVztBQUM1QixVQUFNTCxVQUFVLEtBQUtELE9BQUwsQ0FBYW5DLEVBQWIsMkJBQXdDd0MsVUFBeEMsUUFBaEI7QUFDQSxVQUFJLENBQUNKLE9BQUwsRUFBYztBQUNaLGNBQU0sSUFBSU0sS0FBSixzQkFBNkJGLFVBQTdCLHVCQUFOO0FBQ0Q7O0FBRUQsV0FBS0YsYUFBTCxDQUFtQkYsT0FBbkIsRUFBNEJLLFNBQTVCO0FBQ0Q7O0FBRUQ7Ozs7Ozs7OztrQ0FNY0UsTSxFQUFRRixTLEVBQVc7QUFDL0JwSixhQUFPZ0YsUUFBUCxHQUFrQixLQUFLdUUsT0FBTCxDQUFhRCxPQUFPakksSUFBUCxDQUFZLGFBQVosQ0FBYixFQUEwQytILGNBQWMsTUFBZixHQUF5QixNQUF6QixHQUFrQyxLQUEzRSxFQUFrRkUsT0FBT2pJLElBQVAsQ0FBWSxZQUFaLENBQWxGLENBQWxCO0FBQ0Q7O0FBRUQ7Ozs7Ozs7Ozs2Q0FNeUJpSSxNLEVBQVE7QUFDL0IsYUFBT0EsT0FBT2pJLElBQVAsQ0FBWSxlQUFaLE1BQWlDLEtBQWpDLEdBQXlDLE1BQXpDLEdBQWtELEtBQXpEO0FBQ0Q7O0FBRUQ7Ozs7Ozs7Ozs7OzRCQVFRbUksTyxFQUFTSixTLEVBQVdLLE0sRUFBUTtBQUNsQyxVQUFNdEcsTUFBTSxJQUFJdUcsR0FBSixDQUFRMUosT0FBT2dGLFFBQVAsQ0FBZ0IyRSxJQUF4QixDQUFaO0FBQ0EsVUFBTWhJLFNBQVN3QixJQUFJeUcsWUFBbkI7O0FBRUEsVUFBSUgsTUFBSixFQUFZO0FBQ1Y5SCxlQUFPa0ksR0FBUCxDQUFXSixTQUFPLFdBQWxCLEVBQStCRCxPQUEvQjtBQUNBN0gsZUFBT2tJLEdBQVAsQ0FBV0osU0FBTyxhQUFsQixFQUFpQ0wsU0FBakM7QUFDRCxPQUhELE1BR087QUFDTHpILGVBQU9rSSxHQUFQLENBQVcsU0FBWCxFQUFzQkwsT0FBdEI7QUFDQTdILGVBQU9rSSxHQUFQLENBQVcsV0FBWCxFQUF3QlQsU0FBeEI7QUFDRDs7QUFFRCxhQUFPakcsSUFBSTJHLFFBQUosRUFBUDtBQUNEOzs7OztrQkFHWWxGLFk7Ozs7Ozs7O0FDN0dmLGFBQWEsbUNBQW1DLEVBQUUsSTs7Ozs7Ozs7Ozs7OztBQ0FsRDs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7OztBQXlCQTs7OztBQUlBLElBQU03RSxJQUFJNkksT0FBTzdJLENBQWpCOztBQUVBLElBQU1nSyxPQUFPLFNBQVNDLFdBQVQsQ0FBcUI3RyxHQUFyQixFQUEwQjhHLFdBQTFCLEVBQXVDO0FBQ2hEbEssSUFBRW1LLElBQUYsQ0FBTy9HLEdBQVAsRUFBWWdILElBQVosQ0FBaUI7QUFBQSxXQUFNbkssT0FBT2dGLFFBQVAsQ0FBZ0JvRixNQUFoQixDQUF1QkgsV0FBdkIsQ0FBTjtBQUFBLEdBQWpCO0FBQ0gsQ0FGRDs7a0JBSWVGLEk7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7O0FDVmY7Ozs7QUFFQSxJQUFNaEssSUFBSUMsT0FBT0QsQ0FBakI7O0FBRUE7Ozs7O0FBN0JBOzs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7O0lBa0NNc0ssaUI7QUFDSiw2QkFBWXZHLE9BQVosRUFBcUI7QUFBQTs7QUFDbkJBLGNBQVVBLFdBQVcsRUFBckI7O0FBRUEsU0FBS3dHLGtCQUFMLEdBQTBCeEcsUUFBUXdHLGtCQUFSLElBQThCLGlCQUF4RDtBQUNBLFNBQUtDLG9CQUFMLEdBQTRCekcsUUFBUXlHLG9CQUFSLElBQWdDLGdCQUE1RDtBQUNBLFNBQUtDLG1CQUFMLEdBQTJCMUcsUUFBUTBHLG1CQUFSLElBQStCLGtCQUExRDs7QUFFQXpLLE1BQUUsTUFBRixFQUFVa0UsRUFBVixDQUFhLE9BQWIsRUFBc0IsS0FBS3FHLGtCQUEzQixFQUErQyxLQUFLRyxjQUFMLENBQW9CQyxJQUFwQixDQUF5QixJQUF6QixDQUEvQztBQUNBaEMsK0JBQWF6RSxFQUFiLENBQWdCLGtCQUFoQixFQUFvQyxLQUFLMEcsWUFBTCxDQUFrQkQsSUFBbEIsQ0FBdUIsSUFBdkIsQ0FBcEM7QUFDRDs7QUFFRDs7Ozs7Ozs7O21DQUtleEcsSyxFQUFPO0FBQ3BCLFVBQU0wRyxhQUFhN0ssRUFBRW1FLE1BQU0yRyxNQUFSLENBQW5CO0FBQ0EsVUFBTUMsT0FBT0YsV0FBVzdKLE9BQVgsQ0FBbUIsTUFBbkIsQ0FBYjtBQUNBMkgsaUNBQWFxQyxJQUFiLENBQWtCLGtCQUFsQixFQUFzQztBQUNwQ0Msd0JBQWdCSixXQUFXdkosSUFBWCxDQUFnQixRQUFoQixDQURvQjtBQUVwQ3lKLGNBQU1BO0FBRjhCLE9BQXRDO0FBSUQ7O0FBRUQ7Ozs7Ozs7O2lDQUthNUcsSyxFQUFPO0FBQ2xCLFVBQU00RyxPQUFPNUcsTUFBTTRHLElBQW5CO0FBQ0EsVUFBTUUsaUJBQWlCOUcsTUFBTThHLGNBQTdCO0FBQ0EsVUFBTUMsZUFBZUgsS0FBS3hLLElBQUwsQ0FBVSxLQUFLaUssb0JBQWYsQ0FBckI7QUFDQSxVQUFNVyxvQkFBb0JELGFBQWE1SixJQUFiLENBQWtCLHFCQUFsQixDQUExQjs7QUFFQTRKLG1CQUFhakYsSUFBYixDQUFrQmdGLGNBQWxCO0FBQ0FGLFdBQUt4SyxJQUFMLENBQVUsS0FBS2tLLG1CQUFmLEVBQW9DeEosUUFBcEMsQ0FBNkMsUUFBN0M7QUFDQThKLFdBQUt4SyxJQUFMLENBQWEsS0FBS2tLLG1CQUFsQixtQkFBbURRLGNBQW5ELEVBQXFFL0osV0FBckUsQ0FBaUYsUUFBakY7O0FBRUEsVUFBSWlLLGlCQUFKLEVBQXVCO0FBQ3JCLGFBQUtDLHFCQUFMLENBQTJCRCxpQkFBM0IsRUFBOENGLGNBQTlDO0FBQ0Q7QUFDRjs7QUFFRDs7Ozs7Ozs7Ozs7MENBUXNCRSxpQixFQUFtQkYsYyxFQUFnQjtBQUN2RGpMLFFBQUVtSyxJQUFGLENBQU87QUFDTC9HLGFBQUsrSCxpQkFEQTtBQUVMN0osY0FBTTtBQUNKK0osNkJBQW1CSjtBQURmO0FBRkQsT0FBUDtBQU1EOzs7OztrQkFHWVgsaUI7Ozs7Ozs7Ozs7Ozs7a0JDMURTM0MsWTtBQXhDeEI7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7QUF5QkEsSUFBTTNILElBQUlDLE9BQU9ELENBQWpCOztBQUVBOzs7Ozs7Ozs7Ozs7O0FBYWUsU0FBUzJILFlBQVQsQ0FBc0IvRixNQUF0QixFQUE4QjBKLGVBQTlCLEVBQStDO0FBQUE7O0FBQzVEO0FBRDRELE1BRXJEcEksRUFGcUQsR0FFckN0QixNQUZxQyxDQUVyRHNCLEVBRnFEO0FBQUEsTUFFakRxSSxRQUZpRCxHQUVyQzNKLE1BRnFDLENBRWpEMkosUUFGaUQ7O0FBRzVELE9BQUs5RixLQUFMLEdBQWErRixNQUFNNUosTUFBTixDQUFiOztBQUVBO0FBQ0EsT0FBSzRELE1BQUwsR0FBY3hGLEVBQUUsS0FBS3lGLEtBQUwsQ0FBV2dHLFNBQWIsQ0FBZDs7QUFFQSxPQUFLN0QsSUFBTCxHQUFZLFlBQU07QUFDaEIsVUFBS3BDLE1BQUwsQ0FBWUMsS0FBWjtBQUNELEdBRkQ7O0FBSUEsT0FBS0EsS0FBTCxDQUFXaUcsYUFBWCxDQUF5QkMsZ0JBQXpCLENBQTBDLE9BQTFDLEVBQW1ETCxlQUFuRDs7QUFFQSxPQUFLOUYsTUFBTCxDQUFZQyxLQUFaLENBQWtCO0FBQ2hCbUcsY0FBV0wsV0FBVyxJQUFYLEdBQWtCLFFBRGI7QUFFaEJNLGNBQVVOLGFBQWFuRSxTQUFiLEdBQXlCbUUsUUFBekIsR0FBb0MsSUFGOUI7QUFHaEJBLGNBQVVBLGFBQWFuRSxTQUFiLEdBQXlCbUUsUUFBekIsR0FBb0MsSUFIOUI7QUFJaEIzRCxVQUFNO0FBSlUsR0FBbEI7O0FBT0EsT0FBS3BDLE1BQUwsQ0FBWXRCLEVBQVosQ0FBZSxpQkFBZixFQUFrQyxZQUFNO0FBQ3RDRCxhQUFTNkgsYUFBVCxPQUEyQjVJLEVBQTNCLEVBQWlDNkksTUFBakM7QUFDRCxHQUZEOztBQUlBOUgsV0FBUytILElBQVQsQ0FBY0MsV0FBZCxDQUEwQixLQUFLeEcsS0FBTCxDQUFXZ0csU0FBckM7QUFDRDs7QUFFRDs7Ozs7O0FBTUEsU0FBU0QsS0FBVCxPQVFLO0FBQUEscUJBTkR0SSxFQU1DO0FBQUEsTUFOREEsRUFNQywyQkFOSSxlQU1KO0FBQUEsTUFMRGlFLFlBS0MsUUFMREEsWUFLQztBQUFBLGlDQUpERCxjQUlDO0FBQUEsTUFKREEsY0FJQyx1Q0FKZ0IsRUFJaEI7QUFBQSxtQ0FIRE8sZ0JBR0M7QUFBQSxNQUhEQSxnQkFHQyx5Q0FIa0IsT0FHbEI7QUFBQSxtQ0FGREQsa0JBRUM7QUFBQSxNQUZEQSxrQkFFQyx5Q0FGb0IsUUFFcEI7QUFBQSxtQ0FEREUsa0JBQ0M7QUFBQSxNQUREQSxrQkFDQyx5Q0FEb0IsYUFDcEI7O0FBQ0gsTUFBTWpDLFFBQVEsRUFBZDs7QUFFQTtBQUNBQSxRQUFNZ0csU0FBTixHQUFrQnhILFNBQVNpSSxhQUFULENBQXVCLEtBQXZCLENBQWxCO0FBQ0F6RyxRQUFNZ0csU0FBTixDQUFnQlUsU0FBaEIsQ0FBMEJDLEdBQTFCLENBQThCLE9BQTlCLEVBQXVDLE1BQXZDO0FBQ0EzRyxRQUFNZ0csU0FBTixDQUFnQnZJLEVBQWhCLEdBQXFCQSxFQUFyQjs7QUFFQTtBQUNBdUMsUUFBTTRHLE1BQU4sR0FBZXBJLFNBQVNpSSxhQUFULENBQXVCLEtBQXZCLENBQWY7QUFDQXpHLFFBQU00RyxNQUFOLENBQWFGLFNBQWIsQ0FBdUJDLEdBQXZCLENBQTJCLGNBQTNCOztBQUVBO0FBQ0EzRyxRQUFNNkcsT0FBTixHQUFnQnJJLFNBQVNpSSxhQUFULENBQXVCLEtBQXZCLENBQWhCO0FBQ0F6RyxRQUFNNkcsT0FBTixDQUFjSCxTQUFkLENBQXdCQyxHQUF4QixDQUE0QixlQUE1Qjs7QUFFQTtBQUNBM0csUUFBTThHLE1BQU4sR0FBZXRJLFNBQVNpSSxhQUFULENBQXVCLEtBQXZCLENBQWY7QUFDQXpHLFFBQU04RyxNQUFOLENBQWFKLFNBQWIsQ0FBdUJDLEdBQXZCLENBQTJCLGNBQTNCOztBQUVBO0FBQ0EsTUFBSWpGLFlBQUosRUFBa0I7QUFDaEIxQixVQUFNK0csS0FBTixHQUFjdkksU0FBU2lJLGFBQVQsQ0FBdUIsSUFBdkIsQ0FBZDtBQUNBekcsVUFBTStHLEtBQU4sQ0FBWUwsU0FBWixDQUFzQkMsR0FBdEIsQ0FBMEIsYUFBMUI7QUFDQTNHLFVBQU0rRyxLQUFOLENBQVlDLFNBQVosR0FBd0J0RixZQUF4QjtBQUNEOztBQUVEO0FBQ0ExQixRQUFNaUgsU0FBTixHQUFrQnpJLFNBQVNpSSxhQUFULENBQXVCLFFBQXZCLENBQWxCO0FBQ0F6RyxRQUFNaUgsU0FBTixDQUFnQlAsU0FBaEIsQ0FBMEJDLEdBQTFCLENBQThCLE9BQTlCO0FBQ0EzRyxRQUFNaUgsU0FBTixDQUFnQkMsWUFBaEIsQ0FBNkIsTUFBN0IsRUFBcUMsUUFBckM7QUFDQWxILFFBQU1pSCxTQUFOLENBQWdCRSxPQUFoQixDQUF3QkMsT0FBeEIsR0FBa0MsT0FBbEM7QUFDQXBILFFBQU1pSCxTQUFOLENBQWdCRCxTQUFoQixHQUE0QixHQUE1Qjs7QUFFQTtBQUNBaEgsUUFBTXVHLElBQU4sR0FBYS9ILFNBQVNpSSxhQUFULENBQXVCLEtBQXZCLENBQWI7QUFDQXpHLFFBQU11RyxJQUFOLENBQVdHLFNBQVgsQ0FBcUJDLEdBQXJCLENBQXlCLFlBQXpCLEVBQXVDLFdBQXZDLEVBQW9ELG9CQUFwRDs7QUFFQTtBQUNBM0csUUFBTXFILE9BQU4sR0FBZ0I3SSxTQUFTaUksYUFBVCxDQUF1QixHQUF2QixDQUFoQjtBQUNBekcsUUFBTXFILE9BQU4sQ0FBY1gsU0FBZCxDQUF3QkMsR0FBeEIsQ0FBNEIsaUJBQTVCO0FBQ0EzRyxRQUFNcUgsT0FBTixDQUFjTCxTQUFkLEdBQTBCdkYsY0FBMUI7O0FBRUE7QUFDQXpCLFFBQU1zSCxNQUFOLEdBQWU5SSxTQUFTaUksYUFBVCxDQUF1QixLQUF2QixDQUFmO0FBQ0F6RyxRQUFNc0gsTUFBTixDQUFhWixTQUFiLENBQXVCQyxHQUF2QixDQUEyQixjQUEzQjs7QUFFQTtBQUNBM0csUUFBTXVILFdBQU4sR0FBb0IvSSxTQUFTaUksYUFBVCxDQUF1QixRQUF2QixDQUFwQjtBQUNBekcsUUFBTXVILFdBQU4sQ0FBa0JMLFlBQWxCLENBQStCLE1BQS9CLEVBQXVDLFFBQXZDO0FBQ0FsSCxRQUFNdUgsV0FBTixDQUFrQmIsU0FBbEIsQ0FBNEJDLEdBQTVCLENBQWdDLEtBQWhDLEVBQXVDLHVCQUF2QyxFQUFnRSxRQUFoRTtBQUNBM0csUUFBTXVILFdBQU4sQ0FBa0JKLE9BQWxCLENBQTBCQyxPQUExQixHQUFvQyxPQUFwQztBQUNBcEgsUUFBTXVILFdBQU4sQ0FBa0JQLFNBQWxCLEdBQThCaEYsZ0JBQTlCOztBQUVBO0FBQ0FoQyxRQUFNaUcsYUFBTixHQUFzQnpILFNBQVNpSSxhQUFULENBQXVCLFFBQXZCLENBQXRCO0FBQ0F6RyxRQUFNaUcsYUFBTixDQUFvQmlCLFlBQXBCLENBQWlDLE1BQWpDLEVBQXlDLFFBQXpDO0FBQ0FsSCxRQUFNaUcsYUFBTixDQUFvQlMsU0FBcEIsQ0FBOEJDLEdBQTlCLENBQWtDLEtBQWxDLEVBQXlDMUUsa0JBQXpDLEVBQTZELFFBQTdELEVBQXVFLG9CQUF2RTtBQUNBakMsUUFBTWlHLGFBQU4sQ0FBb0JrQixPQUFwQixDQUE0QkMsT0FBNUIsR0FBc0MsT0FBdEM7QUFDQXBILFFBQU1pRyxhQUFOLENBQW9CZSxTQUFwQixHQUFnQ2pGLGtCQUFoQzs7QUFFQTtBQUNBLE1BQUlMLFlBQUosRUFBa0I7QUFDaEIxQixVQUFNOEcsTUFBTixDQUFhN0ksTUFBYixDQUFvQitCLE1BQU0rRyxLQUExQixFQUFpQy9HLE1BQU1pSCxTQUF2QztBQUNELEdBRkQsTUFFTztBQUNMakgsVUFBTThHLE1BQU4sQ0FBYU4sV0FBYixDQUF5QnhHLE1BQU1pSCxTQUEvQjtBQUNEOztBQUVEakgsUUFBTXVHLElBQU4sQ0FBV0MsV0FBWCxDQUF1QnhHLE1BQU1xSCxPQUE3QjtBQUNBckgsUUFBTXNILE1BQU4sQ0FBYXJKLE1BQWIsQ0FBb0IrQixNQUFNdUgsV0FBMUIsRUFBdUN2SCxNQUFNaUcsYUFBN0M7QUFDQWpHLFFBQU02RyxPQUFOLENBQWM1SSxNQUFkLENBQXFCK0IsTUFBTThHLE1BQTNCLEVBQW1DOUcsTUFBTXVHLElBQXpDLEVBQStDdkcsTUFBTXNILE1BQXJEO0FBQ0F0SCxRQUFNNEcsTUFBTixDQUFhSixXQUFiLENBQXlCeEcsTUFBTTZHLE9BQS9CO0FBQ0E3RyxRQUFNZ0csU0FBTixDQUFnQlEsV0FBaEIsQ0FBNEJ4RyxNQUFNNEcsTUFBbEM7O0FBRUEsU0FBTzVHLEtBQVA7QUFDRCxDOzs7Ozs7Ozs7O0FDcElEOzs7O0FBQ0E7Ozs7QUFDQTs7OztBQUNBOzs7O0FBQ0E7Ozs7QUFDQTs7OztBQUNBOzs7O0FBQ0E7Ozs7QUFDQTs7OztBQUNBOzs7O0FBQ0E7Ozs7QUFDQTs7OztBQUNBOzs7O0FBQ0E7Ozs7QUFDQTs7OztBQUVBOzs7O0FBQ0E7Ozs7QUFDQTs7Ozs7O0FBM0NBOzs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7O0FBNkNBLElBQU16RixJQUFJQyxPQUFPRCxDQUFqQjs7QUFFQUEsRUFBRSxZQUFNO0FBQ04sTUFBTWlOLGNBQWMsSUFBSTFJLGNBQUosQ0FBUyxtQkFBVCxDQUFwQjs7QUFFQTBJLGNBQVlDLFlBQVosQ0FBeUIsSUFBSUMsNkJBQUosRUFBekI7QUFDQUYsY0FBWUMsWUFBWixDQUF5QixJQUFJL0gscUNBQUosRUFBekI7QUFDQThILGNBQVlDLFlBQVosQ0FBeUIsSUFBSXhJLCtCQUFKLEVBQXpCO0FBQ0F1SSxjQUFZQyxZQUFaLENBQXlCLElBQUl2SSwwQkFBSixFQUF6QjtBQUNBc0ksY0FBWUMsWUFBWixDQUF5QixJQUFJckYsZ0NBQUosRUFBekI7QUFDQW9GLGNBQVlDLFlBQVosQ0FBeUIsSUFBSUUsbUNBQUosRUFBekI7QUFDQUgsY0FBWUMsWUFBWixDQUF5QixJQUFJNUcscUNBQUosRUFBekI7QUFDQTJHLGNBQVlDLFlBQVosQ0FBeUIsSUFBSXpFLGtDQUFKLEVBQXpCO0FBQ0F3RSxjQUFZQyxZQUFaLENBQXlCLElBQUlHLGlDQUFKLEVBQXpCO0FBQ0FKLGNBQVlDLFlBQVosQ0FBeUIsSUFBSWhOLDJCQUFKLEVBQXpCO0FBQ0ErTSxjQUFZQyxZQUFaLENBQXlCLElBQUkvRyw2Q0FBSixFQUF6Qjs7QUFFQSxNQUFNbUgsa0JBQWtCLElBQUloRCwyQkFBSixFQUF4Qjs7QUFFQSx5Q0FBd0I7QUFDdEJ6RywyQkFBdUIsd0NBREQ7QUFFdEJDLGdDQUErQndKLGdCQUFnQjdDLG1CQUEvQztBQUZzQixHQUF4Qjs7QUFLQSxNQUFJOEMsb0JBQUosQ0FBZSxvQ0FBZjs7QUFFQSxNQUFNQyxpQkFBaUIsSUFBSUQsb0JBQUosQ0FBZSxxQ0FBZixDQUF2QjtBQUNBQyxpQkFBZUMsdUJBQWY7O0FBR0EsTUFBSUMsdUJBQUosQ0FBa0I7QUFDaEJDLHdCQUFvQixpREFESjtBQUVoQjVKLGFBQVM7QUFDUDZKLDBCQUFvQjtBQURiO0FBRk8sR0FBbEI7O0FBT0EsTUFBTUMsVUFBVSxJQUFJdEosY0FBSixDQUFTLFVBQVQsQ0FBaEI7QUFDQXNKLFVBQVFYLFlBQVIsQ0FBcUIsSUFBSUMsNkJBQUosRUFBckI7QUFDQVUsVUFBUVgsWUFBUixDQUFxQixJQUFJL0gscUNBQUosRUFBckI7QUFDQTBJLFVBQVFYLFlBQVIsQ0FBcUIsSUFBSXhJLCtCQUFKLEVBQXJCO0FBQ0FtSixVQUFRWCxZQUFSLENBQXFCLElBQUl2SSwwQkFBSixFQUFyQjtBQUNBa0osVUFBUVgsWUFBUixDQUFxQixJQUFJRyxpQ0FBSixFQUFyQjtBQUNBUSxVQUFRWCxZQUFSLENBQXFCLElBQUk1RyxxQ0FBSixFQUFyQjtBQUNBdUgsVUFBUVgsWUFBUixDQUFxQixJQUFJRSxtQ0FBSixFQUFyQjtBQUNBUyxVQUFRWCxZQUFSLENBQXFCLElBQUl6RSxrQ0FBSixFQUFyQjtBQUNBb0YsVUFBUVgsWUFBUixDQUFxQixJQUFJaE4sMkJBQUosRUFBckI7QUFDQTJOLFVBQVFYLFlBQVIsQ0FBcUIsSUFBSS9HLDZDQUFKLEVBQXJCO0FBQ0EwSCxVQUFRWCxZQUFSLENBQXFCLElBQUlyRixnQ0FBSixFQUFyQjs7QUFFQSxNQUFNaUcsY0FBYyxJQUFJQyxzQkFBSixDQUFpQix5QkFBakIsQ0FBcEI7QUFDQUQsY0FBWVosWUFBWixDQUF5QixJQUFJYyxvQ0FBSixFQUF6QjtBQUNELENBbERELEU7Ozs7Ozs7QUMvQ0E7QUFDQTtBQUNBO0FBQ0EsdUNBQXVDLGdDOzs7Ozs7OztBQ0h2QztBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQUVBOztBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQSxDQUFDO0FBQ0Q7QUFDQTtBQUNBO0FBQ0E7QUFDQSxDQUFDO0FBQ0Q7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQSxHQUFHO0FBQ0g7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsQ0FBQzs7QUFFRDs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBOztBQUVBO0FBQ0E7QUFDQSxpQkFBaUIsc0JBQXNCO0FBQ3ZDOztBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBLGVBQWU7QUFDZjtBQUNBO0FBQ0E7QUFDQTtBQUNBLGNBQWM7QUFDZDs7QUFFQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7QUFDQSxHQUFHO0FBQ0g7QUFDQTtBQUNBLG1CQUFtQixTQUFTO0FBQzVCO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQSxHQUFHO0FBQ0g7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQSxHQUFHO0FBQ0g7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBLEtBQUs7QUFDTDtBQUNBLEtBQUs7QUFDTDtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7O0FBRUE7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBLGlCQUFpQixzQkFBc0I7QUFDdkM7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQUVBO0FBQ0EsZUFBZTtBQUNmO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsT0FBTztBQUNQOztBQUVBLGlDQUFpQyxRQUFRO0FBQ3pDO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTs7QUFFQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQSxTQUFTO0FBQ1Q7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7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bkNBLGtCQUFtQztBQUFBLDBCQUFmNUosT0FBZTtBQUFBLE1BQWZBLE9BQWUsZ0NBQUwsRUFBSztBQUFBOztBQUM5Qy9ELElBQUUyTixrQkFBRixFQUFzQmtDLFVBQXRCLENBQWlDOUwsT0FBakM7QUFDRCxDOztrQkFSa0IySixhIiwiZmlsZSI6ImNtc19wYWdlLmJ1bmRsZS5qcyIsInNvdXJjZXNDb250ZW50IjpbIiBcdC8vIFRoZSBtb2R1bGUgY2FjaGVcbiBcdHZhciBpbnN0YWxsZWRNb2R1bGVzID0ge307XG5cbiBcdC8vIFRoZSByZXF1aXJlIGZ1bmN0aW9uXG4gXHRmdW5jdGlvbiBfX3dlYnBhY2tfcmVxdWlyZV9fKG1vZHVsZUlkKSB7XG5cbiBcdFx0Ly8gQ2hlY2sgaWYgbW9kdWxlIGlzIGluIGNhY2hlXG4gXHRcdGlmKGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdKSB7XG4gXHRcdFx0cmV0dXJuIGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdLmV4cG9ydHM7XG4gXHRcdH1cbiBcdFx0Ly8gQ3JlYXRlIGEgbmV3IG1vZHVsZSAoYW5kIHB1dCBpdCBpbnRvIHRoZSBjYWNoZSlcbiBcdFx0dmFyIG1vZHVsZSA9IGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdID0ge1xuIFx0XHRcdGk6IG1vZHVsZUlkLFxuIFx0XHRcdGw6IGZhbHNlLFxuIFx0XHRcdGV4cG9ydHM6IHt9XG4gXHRcdH07XG5cbiBcdFx0Ly8gRXhlY3V0ZSB0aGUgbW9kdWxlIGZ1bmN0aW9uXG4gXHRcdG1vZHVsZXNbbW9kdWxlSWRdLmNhbGwobW9kdWxlLmV4cG9ydHMsIG1vZHVsZSwgbW9kdWxlLmV4cG9ydHMsIF9fd2VicGFja19yZXF1aXJlX18pO1xuXG4gXHRcdC8vIEZsYWcgdGhlIG1vZHVsZSBhcyBsb2FkZWRcbiBcdFx0bW9kdWxlLmwgPSB0cnVlO1xuXG4gXHRcdC8vIFJldHVybiB0aGUgZXhwb3J0cyBvZiB0aGUgbW9kdWxlXG4gXHRcdHJldHVybiBtb2R1bGUuZXhwb3J0cztcbiBcdH1cblxuXG4gXHQvLyBleHBvc2UgdGhlIG1vZHVsZXMgb2JqZWN0IChfX3dlYnBhY2tfbW9kdWxlc19fKVxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5tID0gbW9kdWxlcztcblxuIFx0Ly8gZXhwb3NlIHRoZSBtb2R1bGUgY2FjaGVcbiBcdF9fd2VicGFja19yZXF1aXJlX18uYyA9IGluc3RhbGxlZE1vZHVsZXM7XG5cbiBcdC8vIGlkZW50aXR5IGZ1bmN0aW9uIGZvciBjYWxsaW5nIGhhcm1vbnkgaW1wb3J0cyB3aXRoIHRoZSBjb3JyZWN0IGNvbnRleHRcbiBcdF9fd2VicGFja19yZXF1aXJlX18uaSA9IGZ1bmN0aW9uKHZhbHVlKSB7IHJldHVybiB2YWx1ZTsgfTtcblxuIFx0Ly8gZGVmaW5lIGdldHRlciBmdW5jdGlvbiBmb3IgaGFybW9ueSBleHBvcnRzXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmQgPSBmdW5jdGlvbihleHBvcnRzLCBuYW1lLCBnZXR0ZXIpIHtcbiBcdFx0aWYoIV9fd2VicGFja19yZXF1aXJlX18ubyhleHBvcnRzLCBuYW1lKSkge1xuIFx0XHRcdE9iamVjdC5kZWZpbmVQcm9wZXJ0eShleHBvcnRzLCBuYW1lLCB7XG4gXHRcdFx0XHRjb25maWd1cmFibGU6IGZhbHNlLFxuIFx0XHRcdFx0ZW51bWVyYWJsZTogdHJ1ZSxcbiBcdFx0XHRcdGdldDogZ2V0dGVyXG4gXHRcdFx0fSk7XG4gXHRcdH1cbiBcdH07XG5cbiBcdC8vIGdldERlZmF1bHRFeHBvcnQgZnVuY3Rpb24gZm9yIGNvbXBhdGliaWxpdHkgd2l0aCBub24taGFybW9ueSBtb2R1bGVzXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm4gPSBmdW5jdGlvbihtb2R1bGUpIHtcbiBcdFx0dmFyIGdldHRlciA9IG1vZHVsZSAmJiBtb2R1bGUuX19lc01vZHVsZSA/XG4gXHRcdFx0ZnVuY3Rpb24gZ2V0RGVmYXVsdCgpIHsgcmV0dXJuIG1vZHVsZVsnZGVmYXVsdCddOyB9IDpcbiBcdFx0XHRmdW5jdGlvbiBnZXRNb2R1bGVFeHBvcnRzKCkgeyByZXR1cm4gbW9kdWxlOyB9O1xuIFx0XHRfX3dlYnBhY2tfcmVxdWlyZV9fLmQoZ2V0dGVyLCAnYScsIGdldHRlcik7XG4gXHRcdHJldHVybiBnZXR0ZXI7XG4gXHR9O1xuXG4gXHQvLyBPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGxcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubyA9IGZ1bmN0aW9uKG9iamVjdCwgcHJvcGVydHkpIHsgcmV0dXJuIE9iamVjdC5wcm90b3R5cGUuaGFzT3duUHJvcGVydHkuY2FsbChvYmplY3QsIHByb3BlcnR5KTsgfTtcblxuIFx0Ly8gX193ZWJwYWNrX3B1YmxpY19wYXRoX19cbiBcdF9fd2VicGFja19yZXF1aXJlX18ucCA9IFwiXCI7XG5cbiBcdC8vIExvYWQgZW50cnkgbW9kdWxlIGFuZCByZXR1cm4gZXhwb3J0c1xuIFx0cmV0dXJuIF9fd2VicGFja19yZXF1aXJlX18oX193ZWJwYWNrX3JlcXVpcmVfXy5zID0gNDg1KTtcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyB3ZWJwYWNrL2Jvb3RzdHJhcCAzYTYxN2NlZDI5ZWJjY2I2YTFkMCIsIlwidXNlIHN0cmljdFwiO1xuXG5leHBvcnRzLl9fZXNNb2R1bGUgPSB0cnVlO1xuXG5leHBvcnRzLmRlZmF1bHQgPSBmdW5jdGlvbiAoaW5zdGFuY2UsIENvbnN0cnVjdG9yKSB7XG4gIGlmICghKGluc3RhbmNlIGluc3RhbmNlb2YgQ29uc3RydWN0b3IpKSB7XG4gICAgdGhyb3cgbmV3IFR5cGVFcnJvcihcIkNhbm5vdCBjYWxsIGEgY2xhc3MgYXMgYSBmdW5jdGlvblwiKTtcbiAgfVxufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vYmFiZWwtcnVudGltZS9oZWxwZXJzL2NsYXNzQ2FsbENoZWNrLmpzXG4vLyBtb2R1bGUgaWQgPSAwXG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDMgNCA1IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE3IDE4IDE5IDIwIDIxIDIyIDIzIDI0IDI1IDI2IDI3IDI4IDI5IDMwIDMxIDMyIDMzIDM0IDM1IDM2IDM3IDM4IDM5IDQwIDQxIDQyIDQzIDQ0IDQ1IDQ2IDQ3IDQ4IDQ5IDUwIDUxIDUyIDUzIDU0IDU1IDU2IDU3IDU4IiwiXCJ1c2Ugc3RyaWN0XCI7XG5cbmV4cG9ydHMuX19lc01vZHVsZSA9IHRydWU7XG5cbnZhciBfZGVmaW5lUHJvcGVydHkgPSByZXF1aXJlKFwiLi4vY29yZS1qcy9vYmplY3QvZGVmaW5lLXByb3BlcnR5XCIpO1xuXG52YXIgX2RlZmluZVByb3BlcnR5MiA9IF9pbnRlcm9wUmVxdWlyZURlZmF1bHQoX2RlZmluZVByb3BlcnR5KTtcblxuZnVuY3Rpb24gX2ludGVyb3BSZXF1aXJlRGVmYXVsdChvYmopIHsgcmV0dXJuIG9iaiAmJiBvYmouX19lc01vZHVsZSA/IG9iaiA6IHsgZGVmYXVsdDogb2JqIH07IH1cblxuZXhwb3J0cy5kZWZhdWx0ID0gZnVuY3Rpb24gKCkge1xuICBmdW5jdGlvbiBkZWZpbmVQcm9wZXJ0aWVzKHRhcmdldCwgcHJvcHMpIHtcbiAgICBmb3IgKHZhciBpID0gMDsgaSA8IHByb3BzLmxlbmd0aDsgaSsrKSB7XG4gICAgICB2YXIgZGVzY3JpcHRvciA9IHByb3BzW2ldO1xuICAgICAgZGVzY3JpcHRvci5lbnVtZXJhYmxlID0gZGVzY3JpcHRvci5lbnVtZXJhYmxlIHx8IGZhbHNlO1xuICAgICAgZGVzY3JpcHRvci5jb25maWd1cmFibGUgPSB0cnVlO1xuICAgICAgaWYgKFwidmFsdWVcIiBpbiBkZXNjcmlwdG9yKSBkZXNjcmlwdG9yLndyaXRhYmxlID0gdHJ1ZTtcbiAgICAgICgwLCBfZGVmaW5lUHJvcGVydHkyLmRlZmF1bHQpKHRhcmdldCwgZGVzY3JpcHRvci5rZXksIGRlc2NyaXB0b3IpO1xuICAgIH1cbiAgfVxuXG4gIHJldHVybiBmdW5jdGlvbiAoQ29uc3RydWN0b3IsIHByb3RvUHJvcHMsIHN0YXRpY1Byb3BzKSB7XG4gICAgaWYgKHByb3RvUHJvcHMpIGRlZmluZVByb3BlcnRpZXMoQ29uc3RydWN0b3IucHJvdG90eXBlLCBwcm90b1Byb3BzKTtcbiAgICBpZiAoc3RhdGljUHJvcHMpIGRlZmluZVByb3BlcnRpZXMoQ29uc3RydWN0b3IsIHN0YXRpY1Byb3BzKTtcbiAgICByZXR1cm4gQ29uc3RydWN0b3I7XG4gIH07XG59KCk7XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2JhYmVsLXJ1bnRpbWUvaGVscGVycy9jcmVhdGVDbGFzcy5qc1xuLy8gbW9kdWxlIGlkID0gMVxuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAzIDQgNSA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxNyAxOCAxOSAyMCAyMSAyMiAyMyAyNCAyNSAyNiAyNyAyOCAyOSAzMCAzMSAzMiAzMyAzNCAzNSAzNiAzNyAzOCAzOSA0MCA0MSA0MiA0MyA0NCA0NSA0NiA0NyA0OCA0OSA1MCA1MSA1MiA1MyA1NCA1NSA1NiA1NyA1OCIsInZhciBkUCAgICAgICAgID0gcmVxdWlyZSgnLi9fb2JqZWN0LWRwJylcbiAgLCBjcmVhdGVEZXNjID0gcmVxdWlyZSgnLi9fcHJvcGVydHktZGVzYycpO1xubW9kdWxlLmV4cG9ydHMgPSByZXF1aXJlKCcuL19kZXNjcmlwdG9ycycpID8gZnVuY3Rpb24ob2JqZWN0LCBrZXksIHZhbHVlKXtcbiAgcmV0dXJuIGRQLmYob2JqZWN0LCBrZXksIGNyZWF0ZURlc2MoMSwgdmFsdWUpKTtcbn0gOiBmdW5jdGlvbihvYmplY3QsIGtleSwgdmFsdWUpe1xuICBvYmplY3Rba2V5XSA9IHZhbHVlO1xuICByZXR1cm4gb2JqZWN0O1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2hpZGUuanNcbi8vIG1vZHVsZSBpZCA9IDEwXG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDIgMyA0IDUgNiA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxNyAxOCAxOSAyMCAyMSAyMiAyMyAyNCAyNSAyNiAyNyAyOCAyOSAzMCAzMSAzMiAzMyAzNCAzNSAzNiAzNyAzOCAzOSA0MCA0MSA0MiA0MyA0NCA0NSA0NiA0NyA0OCA0OSA1MCA1MSA1MiA1MyA1NCA1NSA1NiA1NyA1OCIsInZhciBpc09iamVjdCA9IHJlcXVpcmUoJy4vX2lzLW9iamVjdCcpO1xubW9kdWxlLmV4cG9ydHMgPSBmdW5jdGlvbihpdCl7XG4gIGlmKCFpc09iamVjdChpdCkpdGhyb3cgVHlwZUVycm9yKGl0ICsgJyBpcyBub3QgYW4gb2JqZWN0IScpO1xuICByZXR1cm4gaXQ7XG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fYW4tb2JqZWN0LmpzXG4vLyBtb2R1bGUgaWQgPSAxMVxuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAyIDMgNCA1IDYgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTcgMTggMTkgMjAgMjEgMjIgMjMgMjQgMjUgMjYgMjcgMjggMjkgMzAgMzEgMzIgMzMgMzQgMzUgMzYgMzcgMzggMzkgNDAgNDEgNDIgNDMgNDQgNDUgNDYgNDcgNDggNDkgNTAgNTEgNTIgNTMgNTQgNTUgNTYgNTcgNTgiLCJtb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uKGJpdG1hcCwgdmFsdWUpe1xuICByZXR1cm4ge1xuICAgIGVudW1lcmFibGUgIDogIShiaXRtYXAgJiAxKSxcbiAgICBjb25maWd1cmFibGU6ICEoYml0bWFwICYgMiksXG4gICAgd3JpdGFibGUgICAgOiAhKGJpdG1hcCAmIDQpLFxuICAgIHZhbHVlICAgICAgIDogdmFsdWVcbiAgfTtcbn07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19wcm9wZXJ0eS1kZXNjLmpzXG4vLyBtb2R1bGUgaWQgPSAxMlxuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAyIDMgNCA1IDYgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTcgMTggMTkgMjAgMjEgMjIgMjMgMjQgMjUgMjYgMjcgMjggMjkgMzAgMzEgMzIgMzMgMzQgMzUgMzYgMzcgMzggMzkgNDAgNDEgNDIgNDMgNDQgNDUgNDYgNDcgNDggNDkgNTAgNTEgNTIgNTMgNTQgNTUgNTYgNTcgNTgiLCIvLyBvcHRpb25hbCAvIHNpbXBsZSBjb250ZXh0IGJpbmRpbmdcbnZhciBhRnVuY3Rpb24gPSByZXF1aXJlKCcuL19hLWZ1bmN0aW9uJyk7XG5tb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uKGZuLCB0aGF0LCBsZW5ndGgpe1xuICBhRnVuY3Rpb24oZm4pO1xuICBpZih0aGF0ID09PSB1bmRlZmluZWQpcmV0dXJuIGZuO1xuICBzd2l0Y2gobGVuZ3RoKXtcbiAgICBjYXNlIDE6IHJldHVybiBmdW5jdGlvbihhKXtcbiAgICAgIHJldHVybiBmbi5jYWxsKHRoYXQsIGEpO1xuICAgIH07XG4gICAgY2FzZSAyOiByZXR1cm4gZnVuY3Rpb24oYSwgYil7XG4gICAgICByZXR1cm4gZm4uY2FsbCh0aGF0LCBhLCBiKTtcbiAgICB9O1xuICAgIGNhc2UgMzogcmV0dXJuIGZ1bmN0aW9uKGEsIGIsIGMpe1xuICAgICAgcmV0dXJuIGZuLmNhbGwodGhhdCwgYSwgYiwgYyk7XG4gICAgfTtcbiAgfVxuICByZXR1cm4gZnVuY3Rpb24oLyogLi4uYXJncyAqLyl7XG4gICAgcmV0dXJuIGZuLmFwcGx5KHRoYXQsIGFyZ3VtZW50cyk7XG4gIH07XG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fY3R4LmpzXG4vLyBtb2R1bGUgaWQgPSAxM1xuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAyIDMgNCA1IDYgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTcgMTggMTkgMjAgMjEgMjIgMjMgMjQgMjUgMjYgMjcgMjggMjkgMzAgMzEgMzIgMzMgMzQgMzUgMzYgMzcgMzggMzkgNDAgNDEgNDIgNDMgNDQgNDUgNDYgNDcgNDggNDkgNTAgNTEgNTIgNTMgNTQgNTUgNTYgNTcgNTgiLCIvLyA3LjEuMSBUb1ByaW1pdGl2ZShpbnB1dCBbLCBQcmVmZXJyZWRUeXBlXSlcbnZhciBpc09iamVjdCA9IHJlcXVpcmUoJy4vX2lzLW9iamVjdCcpO1xuLy8gaW5zdGVhZCBvZiB0aGUgRVM2IHNwZWMgdmVyc2lvbiwgd2UgZGlkbid0IGltcGxlbWVudCBAQHRvUHJpbWl0aXZlIGNhc2Vcbi8vIGFuZCB0aGUgc2Vjb25kIGFyZ3VtZW50IC0gZmxhZyAtIHByZWZlcnJlZCB0eXBlIGlzIGEgc3RyaW5nXG5tb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uKGl0LCBTKXtcbiAgaWYoIWlzT2JqZWN0KGl0KSlyZXR1cm4gaXQ7XG4gIHZhciBmbiwgdmFsO1xuICBpZihTICYmIHR5cGVvZiAoZm4gPSBpdC50b1N0cmluZykgPT0gJ2Z1bmN0aW9uJyAmJiAhaXNPYmplY3QodmFsID0gZm4uY2FsbChpdCkpKXJldHVybiB2YWw7XG4gIGlmKHR5cGVvZiAoZm4gPSBpdC52YWx1ZU9mKSA9PSAnZnVuY3Rpb24nICYmICFpc09iamVjdCh2YWwgPSBmbi5jYWxsKGl0KSkpcmV0dXJuIHZhbDtcbiAgaWYoIVMgJiYgdHlwZW9mIChmbiA9IGl0LnRvU3RyaW5nKSA9PSAnZnVuY3Rpb24nICYmICFpc09iamVjdCh2YWwgPSBmbi5jYWxsKGl0KSkpcmV0dXJuIHZhbDtcbiAgdGhyb3cgVHlwZUVycm9yKFwiQ2FuJ3QgY29udmVydCBvYmplY3QgdG8gcHJpbWl0aXZlIHZhbHVlXCIpO1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX3RvLXByaW1pdGl2ZS5qc1xuLy8gbW9kdWxlIGlkID0gMTRcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMiAzIDQgNSA2IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE3IDE4IDE5IDIwIDIxIDIyIDIzIDI0IDI1IDI2IDI3IDI4IDI5IDMwIDMxIDMyIDMzIDM0IDM1IDM2IDM3IDM4IDM5IDQwIDQxIDQyIDQzIDQ0IDQ1IDQ2IDQ3IDQ4IDQ5IDUwIDUxIDUyIDUzIDU0IDU1IDU2IDU3IDU4IiwiLyoqXG4gKiBDb3B5cmlnaHQgc2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIFByZXN0YVNob3AgaXMgYW4gSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UubWQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly9kZXZkb2NzLnByZXN0YXNob3AuY29tLyBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9ycyA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgU2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqL1xuXG5pbXBvcnQgdGFibGVEbkQgZnJvbSBcInRhYmxlZG5kL2Rpc3QvanF1ZXJ5LnRhYmxlZG5kLm1pblwiO1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbi8qKlxuICogQ2xhc3MgUG9zaXRpb25FeHRlbnNpb24gZXh0ZW5kcyBHcmlkIHdpdGggcmVvcmRlcmFibGUgcG9zaXRpb25zXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIFBvc2l0aW9uRXh0ZW5zaW9uIHtcbiAgY29uc3RydWN0b3IoKSB7XG4gICAgcmV0dXJuIHtcbiAgICAgIGV4dGVuZDogKGdyaWQpID0+IHRoaXMuZXh0ZW5kKGdyaWQpLFxuICAgIH1cbiAgfVxuXG4gIC8qKlxuICAgKiBFeHRlbmQgZ3JpZFxuICAgKlxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICovXG4gIGV4dGVuZChncmlkKSB7XG4gICAgdGhpcy5ncmlkID0gZ3JpZDtcbiAgICB0aGlzLl9hZGRJZHNUb0dyaWRUYWJsZVJvd3MoKTtcbiAgICBncmlkLmdldENvbnRhaW5lcigpLmZpbmQoJy5qcy1ncmlkLXRhYmxlJykudGFibGVEbkQoe1xuICAgICAgb25EcmFnQ2xhc3M6ICdwb3NpdGlvbi1yb3ctd2hpbGUtZHJhZycsXG4gICAgICBkcmFnSGFuZGxlOiAnLmpzLWRyYWctaGFuZGxlJyxcbiAgICAgIG9uRHJvcDogKHRhYmxlLCByb3cpID0+IHRoaXMuX2hhbmRsZVBvc2l0aW9uQ2hhbmdlKHJvdyksXG4gICAgfSk7XG4gICAgZ3JpZC5nZXRDb250YWluZXIoKS5maW5kKCcuanMtZHJhZy1oYW5kbGUnKS5ob3ZlcihcbiAgICAgIGZ1bmN0aW9uKCkge1xuICAgICAgICAkKHRoaXMpLmNsb3Nlc3QoJ3RyJykuYWRkQ2xhc3MoJ2hvdmVyJyk7XG4gICAgICB9LFxuICAgICAgZnVuY3Rpb24oKSB7XG4gICAgICAgICQodGhpcykuY2xvc2VzdCgndHInKS5yZW1vdmVDbGFzcygnaG92ZXInKTtcbiAgICAgIH1cbiAgICApO1xuICB9XG5cbiAgLyoqXG4gICAqIFdoZW4gcG9zaXRpb24gaXMgY2hhbmdlZCBoYW5kbGUgdXBkYXRlXG4gICAqXG4gICAqIEBwYXJhbSB7SFRNTEVsZW1lbnR9IHJvd1xuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2hhbmRsZVBvc2l0aW9uQ2hhbmdlKHJvdykge1xuICAgIGNvbnN0ICRyb3dQb3NpdGlvbkNvbnRhaW5lciA9ICQocm93KS5maW5kKCcuanMtJyArIHRoaXMuZ3JpZC5nZXRJZCgpICsgJy1wb3NpdGlvbjpmaXJzdCcpO1xuICAgIGNvbnN0IHVwZGF0ZVVybCA9ICRyb3dQb3NpdGlvbkNvbnRhaW5lci5kYXRhKCd1cGRhdGUtdXJsJyk7XG4gICAgY29uc3QgbWV0aG9kID0gJHJvd1Bvc2l0aW9uQ29udGFpbmVyLmRhdGEoJ3VwZGF0ZS1tZXRob2QnKTtcbiAgICBjb25zdCBwYWdpbmF0aW9uT2Zmc2V0ID0gcGFyc2VJbnQoJHJvd1Bvc2l0aW9uQ29udGFpbmVyLmRhdGEoJ3BhZ2luYXRpb24tb2Zmc2V0JyksIDEwKTtcbiAgICBjb25zdCBwb3NpdGlvbnMgPSB0aGlzLl9nZXRSb3dzUG9zaXRpb25zKHBhZ2luYXRpb25PZmZzZXQpO1xuICAgIGNvbnN0IHBhcmFtcyA9IHtwb3NpdGlvbnN9O1xuXG4gICAgdGhpcy5fdXBkYXRlUG9zaXRpb24odXBkYXRlVXJsLCBwYXJhbXMsIG1ldGhvZCk7XG4gIH1cblxuICAvKipcbiAgICogUmV0dXJucyB0aGUgY3VycmVudCB0YWJsZSBwb3NpdGlvbnNcbiAgICogQHJldHVybnMge0FycmF5fVxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2dldFJvd3NQb3NpdGlvbnMocGFnaW5hdGlvbk9mZnNldCkge1xuICAgIGNvbnN0IHRhYmxlRGF0YSA9IEpTT04ucGFyc2UoJC50YWJsZURuRC5qc29uaXplKCkpO1xuICAgIGNvbnN0IHJvd3NEYXRhID0gdGFibGVEYXRhW3RoaXMuZ3JpZC5nZXRJZCgpKydfZ3JpZF90YWJsZSddO1xuICAgIGNvbnN0IHJlZ2V4ID0gL15yb3dfKFxcZCspXyhcXGQrKSQvO1xuXG4gICAgY29uc3Qgcm93c05iID0gcm93c0RhdGEubGVuZ3RoO1xuICAgIGNvbnN0IHBvc2l0aW9ucyA9IFtdO1xuICAgIGxldCByb3dEYXRhLCBpO1xuICAgIGZvciAoaSA9IDA7IGkgPCByb3dzTmI7ICsraSkge1xuICAgICAgcm93RGF0YSA9IHJlZ2V4LmV4ZWMocm93c0RhdGFbaV0pO1xuICAgICAgcG9zaXRpb25zLnB1c2goe1xuICAgICAgICByb3dJZDogcm93RGF0YVsxXSxcbiAgICAgICAgbmV3UG9zaXRpb246IHBhZ2luYXRpb25PZmZzZXQgKyBpLFxuICAgICAgICBvbGRQb3NpdGlvbjogcGFyc2VJbnQocm93RGF0YVsyXSwgMTApLFxuICAgICAgfSk7XG4gICAgfVxuXG4gICAgcmV0dXJuIHBvc2l0aW9ucztcbiAgfVxuXG4gIC8qKlxuICAgKiBBZGQgSUQncyB0byBHcmlkIHRhYmxlIHJvd3MgdG8gbWFrZSB0YWJsZURuRC5vbkRyb3AoKSBmdW5jdGlvbiB3b3JrLlxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2FkZElkc1RvR3JpZFRhYmxlUm93cygpIHtcbiAgICB0aGlzLmdyaWQuZ2V0Q29udGFpbmVyKClcbiAgICAgIC5maW5kKCcuanMtZ3JpZC10YWJsZSAuanMtJyArIHRoaXMuZ3JpZC5nZXRJZCgpICsgJy1wb3NpdGlvbicpXG4gICAgICAuZWFjaCgoaW5kZXgsIHBvc2l0aW9uV3JhcHBlcikgPT4ge1xuICAgICAgICBjb25zdCAkcG9zaXRpb25XcmFwcGVyID0gJChwb3NpdGlvbldyYXBwZXIpO1xuICAgICAgICBjb25zdCByb3dJZCA9ICRwb3NpdGlvbldyYXBwZXIuZGF0YSgnaWQnKTtcbiAgICAgICAgY29uc3QgcG9zaXRpb24gPSAkcG9zaXRpb25XcmFwcGVyLmRhdGEoJ3Bvc2l0aW9uJyk7XG4gICAgICAgIGNvbnN0IGlkID0gYHJvd18ke3Jvd0lkfV8ke3Bvc2l0aW9ufWA7XG4gICAgICAgICRwb3NpdGlvbldyYXBwZXIuY2xvc2VzdCgndHInKS5hdHRyKCdpZCcsIGlkKTtcbiAgICAgICAgJHBvc2l0aW9uV3JhcHBlci5jbG9zZXN0KCd0ZCcpLmFkZENsYXNzKCdqcy1kcmFnLWhhbmRsZScpO1xuICAgICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogUHJvY2VzcyByb3dzIHBvc2l0aW9ucyB1cGRhdGVcbiAgICpcbiAgICogQHBhcmFtIHtTdHJpbmd9IHVybFxuICAgKiBAcGFyYW0ge09iamVjdH0gcGFyYW1zXG4gICAqIEBwYXJhbSB7U3RyaW5nfSBtZXRob2RcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF91cGRhdGVQb3NpdGlvbih1cmwsIHBhcmFtcywgbWV0aG9kKSB7XG4gICAgY29uc3QgaXNHZXRPclBvc3RNZXRob2QgPSBbJ0dFVCcsICdQT1NUJ10uaW5jbHVkZXMobWV0aG9kKTtcblxuICAgIGNvbnN0ICRmb3JtID0gJCgnPGZvcm0+Jywge1xuICAgICAgJ2FjdGlvbic6IHVybCxcbiAgICAgICdtZXRob2QnOiBpc0dldE9yUG9zdE1ldGhvZCA/IG1ldGhvZCA6ICdQT1NUJyxcbiAgICB9KS5hcHBlbmRUbygnYm9keScpO1xuXG4gICAgY29uc3QgcG9zaXRpb25zTmIgPSBwYXJhbXMucG9zaXRpb25zLmxlbmd0aDtcbiAgICBsZXQgcG9zaXRpb247XG4gICAgZm9yIChsZXQgaSA9IDA7IGkgPCBwb3NpdGlvbnNOYjsgKytpKSB7XG4gICAgICBwb3NpdGlvbiA9IHBhcmFtcy5wb3NpdGlvbnNbaV07XG4gICAgICAkZm9ybS5hcHBlbmQoXG4gICAgICAgICQoJzxpbnB1dD4nLCB7XG4gICAgICAgICAgJ3R5cGUnOiAnaGlkZGVuJyxcbiAgICAgICAgICAnbmFtZSc6ICdwb3NpdGlvbnNbJytpKyddW3Jvd0lkXScsXG4gICAgICAgICAgJ3ZhbHVlJzogcG9zaXRpb24ucm93SWRcbiAgICAgICAgfSksXG4gICAgICAgICQoJzxpbnB1dD4nLCB7XG4gICAgICAgICAgJ3R5cGUnOiAnaGlkZGVuJyxcbiAgICAgICAgICAnbmFtZSc6ICdwb3NpdGlvbnNbJytpKyddW29sZFBvc2l0aW9uXScsXG4gICAgICAgICAgJ3ZhbHVlJzogcG9zaXRpb24ub2xkUG9zaXRpb25cbiAgICAgICAgfSksXG4gICAgICAgICQoJzxpbnB1dD4nLCB7XG4gICAgICAgICAgJ3R5cGUnOiAnaGlkZGVuJyxcbiAgICAgICAgICAnbmFtZSc6ICdwb3NpdGlvbnNbJytpKyddW25ld1Bvc2l0aW9uXScsXG4gICAgICAgICAgJ3ZhbHVlJzogcG9zaXRpb24ubmV3UG9zaXRpb25cbiAgICAgICAgfSlcbiAgICAgICk7XG4gICAgfVxuXG4gICAgLy8gVGhpcyBfbWV0aG9kIHBhcmFtIGlzIHVzZWQgYnkgU3ltZm9ueSB0byBzaW11bGF0ZSBERUxFVEUgYW5kIFBVVCBtZXRob2RzXG4gICAgaWYgKCFpc0dldE9yUG9zdE1ldGhvZCkge1xuICAgICAgJGZvcm0uYXBwZW5kKCQoJzxpbnB1dD4nLCB7XG4gICAgICAgICd0eXBlJzogJ2hpZGRlbicsXG4gICAgICAgICduYW1lJzogJ19tZXRob2QnLFxuICAgICAgICAndmFsdWUnOiBtZXRob2QsXG4gICAgICB9KSk7XG4gICAgfVxuXG4gICAgJGZvcm0uc3VibWl0KCk7XG4gIH1cbn1cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vcG9zaXRpb24tZXh0ZW5zaW9uLmpzIiwiLyoqXG4gKiBDb3B5cmlnaHQgc2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIFByZXN0YVNob3AgaXMgYW4gSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UubWQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly9kZXZkb2NzLnByZXN0YXNob3AuY29tLyBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9ycyA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgU2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqL1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbi8qKlxuICogQ29tcG9uZW50IHdoaWNoIGFsbG93cyB0byBjb3B5IHJlZ3VsYXIgdGV4dCB0byB1cmwgZnJpZW5kbHkgdGV4dFxuICpcbiAqIFVzYWdlIGV4YW1wbGUgaW4gdGVtcGxhdGU6XG4gKlxuICogPGlucHV0IG5hbWU9XCJzb3VyY2UtaW5wdXRcIiBjbGFzcz1cImpzLWxpbmstcmV3cml0ZS1jb3BpZXItc291cmNlXCI+IC8vIFRoZSBvcmlnaW5hbCB0ZXh0IHdpbGwgYmUgdGFrZW4gZnJvbSB0aGlzIGVsZW1lbnRcbiAqIDxpbnB1dCBuYW1lPVwiZGVzdGluYXRpb24taW5wdXRcIiBjbGFzcz1cImpzLWxpbmstcmV3cml0ZS1jb3BpZXItZGVzdGluYXRpb25cIj4gLy8gTW9kaWZpZWQgdGV4dCB3aWxsIGJlIGFkZGVkIHRvIHRoaXMgaW5wdXRcbiAqXG4gKiBpbiBqYXZhc2NyaXB0OlxuICpcbiAqIHRleHRUb0xpbmtSZXdyaXRlQ29waWVyKHtcbiAqICAgc291cmNlRWxlbWVudFNlbGVjdG9yOiAnLmpzLWxpbmstcmV3cml0ZS1jb3BpZXItc291cmNlJ1xuICogICBkZXN0aW5hdGlvbkVsZW1lbnRTZWxlY3RvcjogJy5qcy1saW5rLXJld3JpdGUtY29waWVyLWRlc3RpbmF0aW9uJyxcbiAqIH0pO1xuICpcbiAqIElmIHRoZSBzb3VyY2UtaW5wdXQgaGFzIHZhbHVlIFwidGVzdCBuYW1lXCIgdGhlIGxpbmsgcmV3cml0ZSB2YWx1ZSB3aWxsIGJlIFwidGVzdC1uYW1lXCIuXG4gKiBJZiB0aGUgc291cmNlLWlucHV0IGhhcyB2YWx1ZSBcInRlc3QgbmFtZSAjJFwiIGxpbmsgcmV3cml0ZSB3aWxsIGJlIFwidGVzdC1uYW1lLVwiIHNpbmNlICMkIGFyZSB1biBhbGxvd2VkIGNoYXJhY3RlcnMgaW4gdXJsLlxuICpcbiAqIFlvdSBjYW4gYWxzbyBwYXNzIGFkZGl0aW9uYWwgb3B0aW9ucyB0byBjaGFuZ2UgdGhlIGV2ZW50IG5hbWUsIG9yIGVuY29kaW5nIGZvcm1hdDpcbiAqXG4gKiB0ZXh0VG9MaW5rUmV3cml0ZUNvcGllcih7XG4gKiAgIHNvdXJjZUVsZW1lbnRTZWxlY3RvcjogJy5qcy1saW5rLXJld3JpdGUtY29waWVyLXNvdXJjZSdcbiAqICAgZGVzdGluYXRpb25FbGVtZW50U2VsZWN0b3I6ICcuanMtbGluay1yZXdyaXRlLWNvcGllci1kZXN0aW5hdGlvbicsXG4gKiAgIG9wdGlvbnM6IHtcbiAqICAgICBldmVudE5hbWU6ICdjaGFuZ2UnLCAvLyBkZWZhdWx0IGlzICdpbnB1dCdcbiAqICAgfVxuICogfSk7XG4gKlxuICovXG5jb25zdCB0ZXh0VG9MaW5rUmV3cml0ZUNvcGllciA9ICh7c291cmNlRWxlbWVudFNlbGVjdG9yLCBkZXN0aW5hdGlvbkVsZW1lbnRTZWxlY3Rvciwgb3B0aW9ucyA9IHtldmVudE5hbWU6ICdpbnB1dCd9fSkgPT4ge1xuICAkKGRvY3VtZW50KS5vbihvcHRpb25zLmV2ZW50TmFtZSwgYCR7c291cmNlRWxlbWVudFNlbGVjdG9yfWAsIChldmVudCkgPT4ge1xuICAgICQoZGVzdGluYXRpb25FbGVtZW50U2VsZWN0b3IpLnZhbChzdHIydXJsKCQoZXZlbnQuY3VycmVudFRhcmdldCkudmFsKCksICdVVEYtOCcpKTtcbiAgfSk7XG59O1xuXG5leHBvcnQgZGVmYXVsdCB0ZXh0VG9MaW5rUmV3cml0ZUNvcGllcjtcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL2NvbXBvbmVudHMvdGV4dC10by1saW5rLXJld3JpdGUtY29waWVyLmpzIiwiLyohIGpxdWVyeS50YWJsZWRuZC5qcyAzMC0xMi0yMDE3ICovXG4hZnVuY3Rpb24oYSxiLGMsZCl7dmFyIGU9XCJ0b3VjaHN0YXJ0IG1vdXNlZG93blwiLGY9XCJ0b3VjaG1vdmUgbW91c2Vtb3ZlXCIsZz1cInRvdWNoZW5kIG1vdXNldXBcIjthKGMpLnJlYWR5KGZ1bmN0aW9uKCl7ZnVuY3Rpb24gYihhKXtmb3IodmFyIGI9e30sYz1hLm1hdGNoKC8oW147Ol0rKS9nKXx8W107Yy5sZW5ndGg7KWJbYy5zaGlmdCgpXT1jLnNoaWZ0KCkudHJpbSgpO3JldHVybiBifWEoXCJ0YWJsZVwiKS5lYWNoKGZ1bmN0aW9uKCl7XCJkbmRcIj09PWEodGhpcykuZGF0YShcInRhYmxlXCIpJiZhKHRoaXMpLnRhYmxlRG5EKHtvbkRyYWdTdHlsZTphKHRoaXMpLmRhdGEoXCJvbmRyYWdzdHlsZVwiKSYmYihhKHRoaXMpLmRhdGEoXCJvbmRyYWdzdHlsZVwiKSl8fG51bGwsb25Ecm9wU3R5bGU6YSh0aGlzKS5kYXRhKFwib25kcm9wc3R5bGVcIikmJmIoYSh0aGlzKS5kYXRhKFwib25kcm9wc3R5bGVcIikpfHxudWxsLG9uRHJhZ0NsYXNzOmEodGhpcykuZGF0YShcIm9uZHJhZ2NsYXNzXCIpPT09ZCYmXCJ0RG5EX3doaWxlRHJhZ1wifHxhKHRoaXMpLmRhdGEoXCJvbmRyYWdjbGFzc1wiKSxvbkRyb3A6YSh0aGlzKS5kYXRhKFwib25kcm9wXCIpJiZuZXcgRnVuY3Rpb24oXCJ0YWJsZVwiLFwicm93XCIsYSh0aGlzKS5kYXRhKFwib25kcm9wXCIpKSxvbkRyYWdTdGFydDphKHRoaXMpLmRhdGEoXCJvbmRyYWdzdGFydFwiKSYmbmV3IEZ1bmN0aW9uKFwidGFibGVcIixcInJvd1wiLGEodGhpcykuZGF0YShcIm9uZHJhZ3N0YXJ0XCIpKSxvbkRyYWdTdG9wOmEodGhpcykuZGF0YShcIm9uZHJhZ3N0b3BcIikmJm5ldyBGdW5jdGlvbihcInRhYmxlXCIsXCJyb3dcIixhKHRoaXMpLmRhdGEoXCJvbmRyYWdzdG9wXCIpKSxzY3JvbGxBbW91bnQ6YSh0aGlzKS5kYXRhKFwic2Nyb2xsYW1vdW50XCIpfHw1LHNlbnNpdGl2aXR5OmEodGhpcykuZGF0YShcInNlbnNpdGl2aXR5XCIpfHwxMCxoaWVyYXJjaHlMZXZlbDphKHRoaXMpLmRhdGEoXCJoaWVyYXJjaHlsZXZlbFwiKXx8MCxpbmRlbnRBcnRpZmFjdDphKHRoaXMpLmRhdGEoXCJpbmRlbnRhcnRpZmFjdFwiKXx8JzxkaXYgY2xhc3M9XCJpbmRlbnRcIj4mbmJzcDs8L2Rpdj4nLGF1dG9XaWR0aEFkanVzdDphKHRoaXMpLmRhdGEoXCJhdXRvd2lkdGhhZGp1c3RcIil8fCEwLGF1dG9DbGVhblJlbGF0aW9uczphKHRoaXMpLmRhdGEoXCJhdXRvY2xlYW5yZWxhdGlvbnNcIil8fCEwLGpzb25QcmV0aWZ5U2VwYXJhdG9yOmEodGhpcykuZGF0YShcImpzb25wcmV0aWZ5c2VwYXJhdG9yXCIpfHxcIlxcdFwiLHNlcmlhbGl6ZVJlZ2V4cDphKHRoaXMpLmRhdGEoXCJzZXJpYWxpemVyZWdleHBcIikmJm5ldyBSZWdFeHAoYSh0aGlzKS5kYXRhKFwic2VyaWFsaXplcmVnZXhwXCIpKXx8L1teXFwtXSokLyxzZXJpYWxpemVQYXJhbU5hbWU6YSh0aGlzKS5kYXRhKFwic2VyaWFsaXplcGFyYW1uYW1lXCIpfHwhMSxkcmFnSGFuZGxlOmEodGhpcykuZGF0YShcImRyYWdoYW5kbGVcIil8fG51bGx9KX0pfSksalF1ZXJ5LnRhYmxlRG5EPXtjdXJyZW50VGFibGU6bnVsbCxkcmFnT2JqZWN0Om51bGwsbW91c2VPZmZzZXQ6bnVsbCxvbGRYOjAsb2xkWTowLGJ1aWxkOmZ1bmN0aW9uKGIpe3JldHVybiB0aGlzLmVhY2goZnVuY3Rpb24oKXt0aGlzLnRhYmxlRG5EQ29uZmlnPWEuZXh0ZW5kKHtvbkRyYWdTdHlsZTpudWxsLG9uRHJvcFN0eWxlOm51bGwsb25EcmFnQ2xhc3M6XCJ0RG5EX3doaWxlRHJhZ1wiLG9uRHJvcDpudWxsLG9uRHJhZ1N0YXJ0Om51bGwsb25EcmFnU3RvcDpudWxsLHNjcm9sbEFtb3VudDo1LHNlbnNpdGl2aXR5OjEwLGhpZXJhcmNoeUxldmVsOjAsaW5kZW50QXJ0aWZhY3Q6JzxkaXYgY2xhc3M9XCJpbmRlbnRcIj4mbmJzcDs8L2Rpdj4nLGF1dG9XaWR0aEFkanVzdDohMCxhdXRvQ2xlYW5SZWxhdGlvbnM6ITAsanNvblByZXRpZnlTZXBhcmF0b3I6XCJcXHRcIixzZXJpYWxpemVSZWdleHA6L1teXFwtXSokLyxzZXJpYWxpemVQYXJhbU5hbWU6ITEsZHJhZ0hhbmRsZTpudWxsfSxifHx7fSksYS50YWJsZURuRC5tYWtlRHJhZ2dhYmxlKHRoaXMpLHRoaXMudGFibGVEbkRDb25maWcuaGllcmFyY2h5TGV2ZWwmJmEudGFibGVEbkQubWFrZUluZGVudGVkKHRoaXMpfSksdGhpc30sbWFrZUluZGVudGVkOmZ1bmN0aW9uKGIpe3ZhciBjLGQsZT1iLnRhYmxlRG5EQ29uZmlnLGY9Yi5yb3dzLGc9YShmKS5maXJzdCgpLmZpbmQoXCJ0ZDpmaXJzdFwiKVswXSxoPTAsaT0wO2lmKGEoYikuaGFzQ2xhc3MoXCJpbmR0ZFwiKSlyZXR1cm4gbnVsbDtkPWEoYikuYWRkQ2xhc3MoXCJpbmR0ZFwiKS5hdHRyKFwic3R5bGVcIiksYShiKS5jc3Moe3doaXRlU3BhY2U6XCJub3dyYXBcIn0pO2Zvcih2YXIgaj0wO2o8Zi5sZW5ndGg7aisrKWk8YShmW2pdKS5maW5kKFwidGQ6Zmlyc3RcIikudGV4dCgpLmxlbmd0aCYmKGk9YShmW2pdKS5maW5kKFwidGQ6Zmlyc3RcIikudGV4dCgpLmxlbmd0aCxjPWopO2ZvcihhKGcpLmNzcyh7d2lkdGg6XCJhdXRvXCJ9KSxqPTA7ajxlLmhpZXJhcmNoeUxldmVsO2orKylhKGZbY10pLmZpbmQoXCJ0ZDpmaXJzdFwiKS5wcmVwZW5kKGUuaW5kZW50QXJ0aWZhY3QpO2ZvcihnJiZhKGcpLmNzcyh7d2lkdGg6Zy5vZmZzZXRXaWR0aH0pLGQmJmEoYikuY3NzKGQpLGo9MDtqPGUuaGllcmFyY2h5TGV2ZWw7aisrKWEoZltjXSkuZmluZChcInRkOmZpcnN0XCIpLmNoaWxkcmVuKFwiOmZpcnN0XCIpLnJlbW92ZSgpO3JldHVybiBlLmhpZXJhcmNoeUxldmVsJiZhKGYpLmVhY2goZnVuY3Rpb24oKXsoaD1hKHRoaXMpLmRhdGEoXCJsZXZlbFwiKXx8MCk8PWUuaGllcmFyY2h5TGV2ZWwmJmEodGhpcykuZGF0YShcImxldmVsXCIsaCl8fGEodGhpcykuZGF0YShcImxldmVsXCIsMCk7Zm9yKHZhciBiPTA7YjxhKHRoaXMpLmRhdGEoXCJsZXZlbFwiKTtiKyspYSh0aGlzKS5maW5kKFwidGQ6Zmlyc3RcIikucHJlcGVuZChlLmluZGVudEFydGlmYWN0KX0pLHRoaXN9LG1ha2VEcmFnZ2FibGU6ZnVuY3Rpb24oYil7dmFyIGM9Yi50YWJsZURuRENvbmZpZztjLmRyYWdIYW5kbGUmJmEoYy5kcmFnSGFuZGxlLGIpLmVhY2goZnVuY3Rpb24oKXthKHRoaXMpLmJpbmQoZSxmdW5jdGlvbihkKXtyZXR1cm4gYS50YWJsZURuRC5pbml0aWFsaXNlRHJhZyhhKHRoaXMpLnBhcmVudHMoXCJ0clwiKVswXSxiLHRoaXMsZCxjKSwhMX0pfSl8fGEoYi5yb3dzKS5lYWNoKGZ1bmN0aW9uKCl7YSh0aGlzKS5oYXNDbGFzcyhcIm5vZHJhZ1wiKT9hKHRoaXMpLmNzcyhcImN1cnNvclwiLFwiXCIpOmEodGhpcykuYmluZChlLGZ1bmN0aW9uKGQpe2lmKFwiVERcIj09PWQudGFyZ2V0LnRhZ05hbWUpcmV0dXJuIGEudGFibGVEbkQuaW5pdGlhbGlzZURyYWcodGhpcyxiLHRoaXMsZCxjKSwhMX0pLmNzcyhcImN1cnNvclwiLFwibW92ZVwiKX0pfSxjdXJyZW50T3JkZXI6ZnVuY3Rpb24oKXt2YXIgYj10aGlzLmN1cnJlbnRUYWJsZS5yb3dzO3JldHVybiBhLm1hcChiLGZ1bmN0aW9uKGIpe3JldHVybihhKGIpLmRhdGEoXCJsZXZlbFwiKStiLmlkKS5yZXBsYWNlKC9cXHMvZyxcIlwiKX0pLmpvaW4oXCJcIil9LGluaXRpYWxpc2VEcmFnOmZ1bmN0aW9uKGIsZCxlLGgsaSl7dGhpcy5kcmFnT2JqZWN0PWIsdGhpcy5jdXJyZW50VGFibGU9ZCx0aGlzLm1vdXNlT2Zmc2V0PXRoaXMuZ2V0TW91c2VPZmZzZXQoZSxoKSx0aGlzLm9yaWdpbmFsT3JkZXI9dGhpcy5jdXJyZW50T3JkZXIoKSxhKGMpLmJpbmQoZix0aGlzLm1vdXNlbW92ZSkuYmluZChnLHRoaXMubW91c2V1cCksaS5vbkRyYWdTdGFydCYmaS5vbkRyYWdTdGFydChkLGUpfSx1cGRhdGVUYWJsZXM6ZnVuY3Rpb24oKXt0aGlzLmVhY2goZnVuY3Rpb24oKXt0aGlzLnRhYmxlRG5EQ29uZmlnJiZhLnRhYmxlRG5ELm1ha2VEcmFnZ2FibGUodGhpcyl9KX0sbW91c2VDb29yZHM6ZnVuY3Rpb24oYSl7cmV0dXJuIGEub3JpZ2luYWxFdmVudC5jaGFuZ2VkVG91Y2hlcz97eDphLm9yaWdpbmFsRXZlbnQuY2hhbmdlZFRvdWNoZXNbMF0uY2xpZW50WCx5OmEub3JpZ2luYWxFdmVudC5jaGFuZ2VkVG91Y2hlc1swXS5jbGllbnRZfTphLnBhZ2VYfHxhLnBhZ2VZP3t4OmEucGFnZVgseTphLnBhZ2VZfTp7eDphLmNsaWVudFgrYy5ib2R5LnNjcm9sbExlZnQtYy5ib2R5LmNsaWVudExlZnQseTphLmNsaWVudFkrYy5ib2R5LnNjcm9sbFRvcC1jLmJvZHkuY2xpZW50VG9wfX0sZ2V0TW91c2VPZmZzZXQ6ZnVuY3Rpb24oYSxjKXt2YXIgZCxlO3JldHVybiBjPWN8fGIuZXZlbnQsZT10aGlzLmdldFBvc2l0aW9uKGEpLGQ9dGhpcy5tb3VzZUNvb3JkcyhjKSx7eDpkLngtZS54LHk6ZC55LWUueX19LGdldFBvc2l0aW9uOmZ1bmN0aW9uKGEpe3ZhciBiPTAsYz0wO2ZvcigwPT09YS5vZmZzZXRIZWlnaHQmJihhPWEuZmlyc3RDaGlsZCk7YS5vZmZzZXRQYXJlbnQ7KWIrPWEub2Zmc2V0TGVmdCxjKz1hLm9mZnNldFRvcCxhPWEub2Zmc2V0UGFyZW50O3JldHVybiBiKz1hLm9mZnNldExlZnQsYys9YS5vZmZzZXRUb3Ase3g6Yix5OmN9fSxhdXRvU2Nyb2xsOmZ1bmN0aW9uKGEpe3ZhciBkPXRoaXMuY3VycmVudFRhYmxlLnRhYmxlRG5EQ29uZmlnLGU9Yi5wYWdlWU9mZnNldCxmPWIuaW5uZXJIZWlnaHQ/Yi5pbm5lckhlaWdodDpjLmRvY3VtZW50RWxlbWVudC5jbGllbnRIZWlnaHQ/Yy5kb2N1bWVudEVsZW1lbnQuY2xpZW50SGVpZ2h0OmMuYm9keS5jbGllbnRIZWlnaHQ7Yy5hbGwmJih2b2lkIDAhPT1jLmNvbXBhdE1vZGUmJlwiQmFja0NvbXBhdFwiIT09Yy5jb21wYXRNb2RlP2U9Yy5kb2N1bWVudEVsZW1lbnQuc2Nyb2xsVG9wOnZvaWQgMCE9PWMuYm9keSYmKGU9Yy5ib2R5LnNjcm9sbFRvcCkpLGEueS1lPGQuc2Nyb2xsQW1vdW50JiZiLnNjcm9sbEJ5KDAsLWQuc2Nyb2xsQW1vdW50KXx8Zi0oYS55LWUpPGQuc2Nyb2xsQW1vdW50JiZiLnNjcm9sbEJ5KDAsZC5zY3JvbGxBbW91bnQpfSxtb3ZlVmVydGljbGU6ZnVuY3Rpb24oYSxiKXswIT09YS52ZXJ0aWNhbCYmYiYmdGhpcy5kcmFnT2JqZWN0IT09YiYmdGhpcy5kcmFnT2JqZWN0LnBhcmVudE5vZGU9PT1iLnBhcmVudE5vZGUmJigwPmEudmVydGljYWwmJnRoaXMuZHJhZ09iamVjdC5wYXJlbnROb2RlLmluc2VydEJlZm9yZSh0aGlzLmRyYWdPYmplY3QsYi5uZXh0U2libGluZyl8fDA8YS52ZXJ0aWNhbCYmdGhpcy5kcmFnT2JqZWN0LnBhcmVudE5vZGUuaW5zZXJ0QmVmb3JlKHRoaXMuZHJhZ09iamVjdCxiKSl9LG1vdmVIb3Jpem9udGFsOmZ1bmN0aW9uKGIsYyl7dmFyIGQsZT10aGlzLmN1cnJlbnRUYWJsZS50YWJsZURuRENvbmZpZztpZighZS5oaWVyYXJjaHlMZXZlbHx8MD09PWIuaG9yaXpvbnRhbHx8IWN8fHRoaXMuZHJhZ09iamVjdCE9PWMpcmV0dXJuIG51bGw7ZD1hKGMpLmRhdGEoXCJsZXZlbFwiKSwwPGIuaG9yaXpvbnRhbCYmZD4wJiZhKGMpLmZpbmQoXCJ0ZDpmaXJzdFwiKS5jaGlsZHJlbihcIjpmaXJzdFwiKS5yZW1vdmUoKSYmYShjKS5kYXRhKFwibGV2ZWxcIiwtLWQpLDA+Yi5ob3Jpem9udGFsJiZkPGUuaGllcmFyY2h5TGV2ZWwmJmEoYykucHJldigpLmRhdGEoXCJsZXZlbFwiKT49ZCYmYShjKS5jaGlsZHJlbihcIjpmaXJzdFwiKS5wcmVwZW5kKGUuaW5kZW50QXJ0aWZhY3QpJiZhKGMpLmRhdGEoXCJsZXZlbFwiLCsrZCl9LG1vdXNlbW92ZTpmdW5jdGlvbihiKXt2YXIgYyxkLGUsZixnLGg9YShhLnRhYmxlRG5ELmRyYWdPYmplY3QpLGk9YS50YWJsZURuRC5jdXJyZW50VGFibGUudGFibGVEbkRDb25maWc7cmV0dXJuIGImJmIucHJldmVudERlZmF1bHQoKSwhIWEudGFibGVEbkQuZHJhZ09iamVjdCYmKFwidG91Y2htb3ZlXCI9PT1iLnR5cGUmJmV2ZW50LnByZXZlbnREZWZhdWx0KCksaS5vbkRyYWdDbGFzcyYmaC5hZGRDbGFzcyhpLm9uRHJhZ0NsYXNzKXx8aC5jc3MoaS5vbkRyYWdTdHlsZSksZD1hLnRhYmxlRG5ELm1vdXNlQ29vcmRzKGIpLGY9ZC54LWEudGFibGVEbkQubW91c2VPZmZzZXQueCxnPWQueS1hLnRhYmxlRG5ELm1vdXNlT2Zmc2V0LnksYS50YWJsZURuRC5hdXRvU2Nyb2xsKGQpLGM9YS50YWJsZURuRC5maW5kRHJvcFRhcmdldFJvdyhoLGcpLGU9YS50YWJsZURuRC5maW5kRHJhZ0RpcmVjdGlvbihmLGcpLGEudGFibGVEbkQubW92ZVZlcnRpY2xlKGUsYyksYS50YWJsZURuRC5tb3ZlSG9yaXpvbnRhbChlLGMpLCExKX0sZmluZERyYWdEaXJlY3Rpb246ZnVuY3Rpb24oYSxiKXt2YXIgYz10aGlzLmN1cnJlbnRUYWJsZS50YWJsZURuRENvbmZpZy5zZW5zaXRpdml0eSxkPXRoaXMub2xkWCxlPXRoaXMub2xkWSxmPWQtYyxnPWQrYyxoPWUtYyxpPWUrYyxqPXtob3Jpem9udGFsOmE+PWYmJmE8PWc/MDphPmQ/LTE6MSx2ZXJ0aWNhbDpiPj1oJiZiPD1pPzA6Yj5lPy0xOjF9O3JldHVybiAwIT09ai5ob3Jpem9udGFsJiYodGhpcy5vbGRYPWEpLDAhPT1qLnZlcnRpY2FsJiYodGhpcy5vbGRZPWIpLGp9LGZpbmREcm9wVGFyZ2V0Um93OmZ1bmN0aW9uKGIsYyl7Zm9yKHZhciBkPTAsZT10aGlzLmN1cnJlbnRUYWJsZS5yb3dzLGY9dGhpcy5jdXJyZW50VGFibGUudGFibGVEbkRDb25maWcsZz0wLGg9bnVsbCxpPTA7aTxlLmxlbmd0aDtpKyspaWYoaD1lW2ldLGc9dGhpcy5nZXRQb3NpdGlvbihoKS55LGQ9cGFyc2VJbnQoaC5vZmZzZXRIZWlnaHQpLzIsMD09PWgub2Zmc2V0SGVpZ2h0JiYoZz10aGlzLmdldFBvc2l0aW9uKGguZmlyc3RDaGlsZCkueSxkPXBhcnNlSW50KGguZmlyc3RDaGlsZC5vZmZzZXRIZWlnaHQpLzIpLGM+Zy1kJiZjPGcrZClyZXR1cm4gYi5pcyhoKXx8Zi5vbkFsbG93RHJvcCYmIWYub25BbGxvd0Ryb3AoYixoKXx8YShoKS5oYXNDbGFzcyhcIm5vZHJvcFwiKT9udWxsOmg7cmV0dXJuIG51bGx9LHByb2Nlc3NNb3VzZXVwOmZ1bmN0aW9uKCl7aWYoIXRoaXMuY3VycmVudFRhYmxlfHwhdGhpcy5kcmFnT2JqZWN0KXJldHVybiBudWxsO3ZhciBiPXRoaXMuY3VycmVudFRhYmxlLnRhYmxlRG5EQ29uZmlnLGQ9dGhpcy5kcmFnT2JqZWN0LGU9MCxoPTA7YShjKS51bmJpbmQoZix0aGlzLm1vdXNlbW92ZSkudW5iaW5kKGcsdGhpcy5tb3VzZXVwKSxiLmhpZXJhcmNoeUxldmVsJiZiLmF1dG9DbGVhblJlbGF0aW9ucyYmYSh0aGlzLmN1cnJlbnRUYWJsZS5yb3dzKS5maXJzdCgpLmZpbmQoXCJ0ZDpmaXJzdFwiKS5jaGlsZHJlbigpLmVhY2goZnVuY3Rpb24oKXsoaD1hKHRoaXMpLnBhcmVudHMoXCJ0cjpmaXJzdFwiKS5kYXRhKFwibGV2ZWxcIikpJiZhKHRoaXMpLnBhcmVudHMoXCJ0cjpmaXJzdFwiKS5kYXRhKFwibGV2ZWxcIiwtLWgpJiZhKHRoaXMpLnJlbW92ZSgpfSkmJmIuaGllcmFyY2h5TGV2ZWw+MSYmYSh0aGlzLmN1cnJlbnRUYWJsZS5yb3dzKS5lYWNoKGZ1bmN0aW9uKCl7aWYoKGg9YSh0aGlzKS5kYXRhKFwibGV2ZWxcIikpPjEpZm9yKGU9YSh0aGlzKS5wcmV2KCkuZGF0YShcImxldmVsXCIpO2g+ZSsxOylhKHRoaXMpLmZpbmQoXCJ0ZDpmaXJzdFwiKS5jaGlsZHJlbihcIjpmaXJzdFwiKS5yZW1vdmUoKSxhKHRoaXMpLmRhdGEoXCJsZXZlbFwiLC0taCl9KSxiLm9uRHJhZ0NsYXNzJiZhKGQpLnJlbW92ZUNsYXNzKGIub25EcmFnQ2xhc3MpfHxhKGQpLmNzcyhiLm9uRHJvcFN0eWxlKSx0aGlzLmRyYWdPYmplY3Q9bnVsbCxiLm9uRHJvcCYmdGhpcy5vcmlnaW5hbE9yZGVyIT09dGhpcy5jdXJyZW50T3JkZXIoKSYmYShkKS5oaWRlKCkuZmFkZUluKFwiZmFzdFwiKSYmYi5vbkRyb3AodGhpcy5jdXJyZW50VGFibGUsZCksYi5vbkRyYWdTdG9wJiZiLm9uRHJhZ1N0b3AodGhpcy5jdXJyZW50VGFibGUsZCksdGhpcy5jdXJyZW50VGFibGU9bnVsbH0sbW91c2V1cDpmdW5jdGlvbihiKXtyZXR1cm4gYiYmYi5wcmV2ZW50RGVmYXVsdCgpLGEudGFibGVEbkQucHJvY2Vzc01vdXNldXAoKSwhMX0sanNvbml6ZTpmdW5jdGlvbihhKXt2YXIgYj10aGlzLmN1cnJlbnRUYWJsZTtyZXR1cm4gYT9KU09OLnN0cmluZ2lmeSh0aGlzLnRhYmxlRGF0YShiKSxudWxsLGIudGFibGVEbkRDb25maWcuanNvblByZXRpZnlTZXBhcmF0b3IpOkpTT04uc3RyaW5naWZ5KHRoaXMudGFibGVEYXRhKGIpKX0sc2VyaWFsaXplOmZ1bmN0aW9uKCl7cmV0dXJuIGEucGFyYW0odGhpcy50YWJsZURhdGEodGhpcy5jdXJyZW50VGFibGUpKX0sc2VyaWFsaXplVGFibGU6ZnVuY3Rpb24oYSl7Zm9yKHZhciBiPVwiXCIsYz1hLnRhYmxlRG5EQ29uZmlnLnNlcmlhbGl6ZVBhcmFtTmFtZXx8YS5pZCxkPWEucm93cyxlPTA7ZTxkLmxlbmd0aDtlKyspe2IubGVuZ3RoPjAmJihiKz1cIiZcIik7dmFyIGY9ZFtlXS5pZDtmJiZhLnRhYmxlRG5EQ29uZmlnJiZhLnRhYmxlRG5EQ29uZmlnLnNlcmlhbGl6ZVJlZ2V4cCYmKGY9Zi5tYXRjaChhLnRhYmxlRG5EQ29uZmlnLnNlcmlhbGl6ZVJlZ2V4cClbMF0sYis9YytcIltdPVwiK2YpfXJldHVybiBifSxzZXJpYWxpemVUYWJsZXM6ZnVuY3Rpb24oKXt2YXIgYj1bXTtyZXR1cm4gYShcInRhYmxlXCIpLmVhY2goZnVuY3Rpb24oKXt0aGlzLmlkJiZiLnB1c2goYS5wYXJhbShhLnRhYmxlRG5ELnRhYmxlRGF0YSh0aGlzKSkpfSksYi5qb2luKFwiJlwiKX0sdGFibGVEYXRhOmZ1bmN0aW9uKGIpe3ZhciBjLGQsZSxmLGc9Yi50YWJsZURuRENvbmZpZyxoPVtdLGk9MCxqPTAsaz1udWxsLGw9e307aWYoYnx8KGI9dGhpcy5jdXJyZW50VGFibGUpLCFifHwhYi5yb3dzfHwhYi5yb3dzLmxlbmd0aClyZXR1cm57ZXJyb3I6e2NvZGU6NTAwLG1lc3NhZ2U6XCJOb3QgYSB2YWxpZCB0YWJsZS5cIn19O2lmKCFiLmlkJiYhZy5zZXJpYWxpemVQYXJhbU5hbWUpcmV0dXJue2Vycm9yOntjb2RlOjUwMCxtZXNzYWdlOlwiTm8gc2VyaWFsaXphYmxlIHVuaXF1ZSBpZCBwcm92aWRlZC5cIn19O2Y9Zy5hdXRvQ2xlYW5SZWxhdGlvbnMmJmIucm93c3x8YS5tYWtlQXJyYXkoYi5yb3dzKSxkPWcuc2VyaWFsaXplUGFyYW1OYW1lfHxiLmlkLGU9ZCxjPWZ1bmN0aW9uKGEpe3JldHVybiBhJiZnJiZnLnNlcmlhbGl6ZVJlZ2V4cD9hLm1hdGNoKGcuc2VyaWFsaXplUmVnZXhwKVswXTphfSxsW2VdPVtdLCFnLmF1dG9DbGVhblJlbGF0aW9ucyYmYShmWzBdKS5kYXRhKFwibGV2ZWxcIikmJmYudW5zaGlmdCh7aWQ6XCJ1bmRlZmluZWRcIn0pO2Zvcih2YXIgbT0wO208Zi5sZW5ndGg7bSsrKWlmKGcuaGllcmFyY2h5TGV2ZWwpe2lmKDA9PT0oaj1hKGZbbV0pLmRhdGEoXCJsZXZlbFwiKXx8MCkpZT1kLGg9W107ZWxzZSBpZihqPmkpaC5wdXNoKFtlLGldKSxlPWMoZlttLTFdLmlkKTtlbHNlIGlmKGo8aSlmb3IodmFyIG49MDtuPGgubGVuZ3RoO24rKyloW25dWzFdPT09aiYmKGU9aFtuXVswXSksaFtuXVsxXT49aSYmKGhbbl1bMV09MCk7aT1qLGEuaXNBcnJheShsW2VdKXx8KGxbZV09W10pLGs9YyhmW21dLmlkKSxrJiZsW2VdLnB1c2goayl9ZWxzZShrPWMoZlttXS5pZCkpJiZsW2VdLnB1c2goayk7cmV0dXJuIGx9fSxqUXVlcnkuZm4uZXh0ZW5kKHt0YWJsZURuRDphLnRhYmxlRG5ELmJ1aWxkLHRhYmxlRG5EVXBkYXRlOmEudGFibGVEbkQudXBkYXRlVGFibGVzLHRhYmxlRG5EU2VyaWFsaXplOmEucHJveHkoYS50YWJsZURuRC5zZXJpYWxpemUsYS50YWJsZURuRCksdGFibGVEbkRTZXJpYWxpemVBbGw6YS50YWJsZURuRC5zZXJpYWxpemVUYWJsZXMsdGFibGVEbkREYXRhOmEucHJveHkoYS50YWJsZURuRC50YWJsZURhdGEsYS50YWJsZURuRCl9KX0oalF1ZXJ5LHdpbmRvdyx3aW5kb3cuZG9jdW1lbnQpO1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi90YWJsZWRuZC9kaXN0L2pxdWVyeS50YWJsZWRuZC5taW4uanNcbi8vIG1vZHVsZSBpZCA9IDE1OFxuLy8gbW9kdWxlIGNodW5rcyA9IDcgMTcgMjYgMzAiLCJ2YXIgaXNPYmplY3QgPSByZXF1aXJlKCcuL19pcy1vYmplY3QnKVxuICAsIGRvY3VtZW50ID0gcmVxdWlyZSgnLi9fZ2xvYmFsJykuZG9jdW1lbnRcbiAgLy8gaW4gb2xkIElFIHR5cGVvZiBkb2N1bWVudC5jcmVhdGVFbGVtZW50IGlzICdvYmplY3QnXG4gICwgaXMgPSBpc09iamVjdChkb2N1bWVudCkgJiYgaXNPYmplY3QoZG9jdW1lbnQuY3JlYXRlRWxlbWVudCk7XG5tb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uKGl0KXtcbiAgcmV0dXJuIGlzID8gZG9jdW1lbnQuY3JlYXRlRWxlbWVudChpdCkgOiB7fTtcbn07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19kb20tY3JlYXRlLmpzXG4vLyBtb2R1bGUgaWQgPSAxNlxuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAyIDMgNCA1IDYgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTcgMTggMTkgMjAgMjEgMjIgMjMgMjQgMjUgMjYgMjcgMjggMjkgMzAgMzEgMzIgMzMgMzQgMzUgMzYgMzcgMzggMzkgNDAgNDEgNDIgNDMgNDQgNDUgNDYgNDcgNDggNDkgNTAgNTEgNTIgNTMgNTQgNTUgNTYgNTcgNTgiLCJtb2R1bGUuZXhwb3J0cyA9ICFyZXF1aXJlKCcuL19kZXNjcmlwdG9ycycpICYmICFyZXF1aXJlKCcuL19mYWlscycpKGZ1bmN0aW9uKCl7XG4gIHJldHVybiBPYmplY3QuZGVmaW5lUHJvcGVydHkocmVxdWlyZSgnLi9fZG9tLWNyZWF0ZScpKCdkaXYnKSwgJ2EnLCB7Z2V0OiBmdW5jdGlvbigpeyByZXR1cm4gNzsgfX0pLmEgIT0gNztcbn0pO1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9faWU4LWRvbS1kZWZpbmUuanNcbi8vIG1vZHVsZSBpZCA9IDE3XG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDIgMyA0IDUgNiA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxNyAxOCAxOSAyMCAyMSAyMiAyMyAyNCAyNSAyNiAyNyAyOCAyOSAzMCAzMSAzMiAzMyAzNCAzNSAzNiAzNyAzOCAzOSA0MCA0MSA0MiA0MyA0NCA0NSA0NiA0NyA0OCA0OSA1MCA1MSA1MiA1MyA1NCA1NSA1NiA1NyA1OCIsIm1vZHVsZS5leHBvcnRzID0gZnVuY3Rpb24oaXQpe1xuICBpZih0eXBlb2YgaXQgIT0gJ2Z1bmN0aW9uJyl0aHJvdyBUeXBlRXJyb3IoaXQgKyAnIGlzIG5vdCBhIGZ1bmN0aW9uIScpO1xuICByZXR1cm4gaXQ7XG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fYS1mdW5jdGlvbi5qc1xuLy8gbW9kdWxlIGlkID0gMThcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMiAzIDQgNSA2IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE3IDE4IDE5IDIwIDIxIDIyIDIzIDI0IDI1IDI2IDI3IDI4IDI5IDMwIDMxIDMyIDMzIDM0IDM1IDM2IDM3IDM4IDM5IDQwIDQxIDQyIDQzIDQ0IDQ1IDQ2IDQ3IDQ4IDQ5IDUwIDUxIDUyIDUzIDU0IDU1IDU2IDU3IDU4IiwibW9kdWxlLmV4cG9ydHMgPSB7IFwiZGVmYXVsdFwiOiByZXF1aXJlKFwiY29yZS1qcy9saWJyYXJ5L2ZuL29iamVjdC9kZWZpbmUtcHJvcGVydHlcIiksIF9fZXNNb2R1bGU6IHRydWUgfTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vYmFiZWwtcnVudGltZS9jb3JlLWpzL29iamVjdC9kZWZpbmUtcHJvcGVydHkuanNcbi8vIG1vZHVsZSBpZCA9IDE5XG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDIgMyA0IDUgNiA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxNyAxOCAxOSAyMCAyMSAyMiAyMyAyNCAyNSAyNiAyNyAyOCAyOSAzMCAzMSAzMiAzMyAzNCAzNSAzNiAzNyAzOCAzOSA0MCA0MSA0MiA0MyA0NCA0NSA0NiA0NyA0OCA0OSA1MCA1MSA1MiA1MyA1NCA1NSA1NiA1NyA1OCIsIi8vIFRoYW5rJ3MgSUU4IGZvciBoaXMgZnVubnkgZGVmaW5lUHJvcGVydHlcbm1vZHVsZS5leHBvcnRzID0gIXJlcXVpcmUoJy4vX2ZhaWxzJykoZnVuY3Rpb24oKXtcbiAgcmV0dXJuIE9iamVjdC5kZWZpbmVQcm9wZXJ0eSh7fSwgJ2EnLCB7Z2V0OiBmdW5jdGlvbigpeyByZXR1cm4gNzsgfX0pLmEgIT0gNztcbn0pO1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fZGVzY3JpcHRvcnMuanNcbi8vIG1vZHVsZSBpZCA9IDJcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMiAzIDQgNSA2IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE3IDE4IDE5IDIwIDIxIDIyIDIzIDI0IDI1IDI2IDI3IDI4IDI5IDMwIDMxIDMyIDMzIDM0IDM1IDM2IDM3IDM4IDM5IDQwIDQxIDQyIDQzIDQ0IDQ1IDQ2IDQ3IDQ4IDQ5IDUwIDUxIDUyIDUzIDU0IDU1IDU2IDU3IDU4IiwicmVxdWlyZSgnLi4vLi4vbW9kdWxlcy9lczYub2JqZWN0LmRlZmluZS1wcm9wZXJ0eScpO1xudmFyICRPYmplY3QgPSByZXF1aXJlKCcuLi8uLi9tb2R1bGVzL19jb3JlJykuT2JqZWN0O1xubW9kdWxlLmV4cG9ydHMgPSBmdW5jdGlvbiBkZWZpbmVQcm9wZXJ0eShpdCwga2V5LCBkZXNjKXtcbiAgcmV0dXJuICRPYmplY3QuZGVmaW5lUHJvcGVydHkoaXQsIGtleSwgZGVzYyk7XG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvZm4vb2JqZWN0L2RlZmluZS1wcm9wZXJ0eS5qc1xuLy8gbW9kdWxlIGlkID0gMjBcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMiAzIDQgNSA2IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE3IDE4IDE5IDIwIDIxIDIyIDIzIDI0IDI1IDI2IDI3IDI4IDI5IDMwIDMxIDMyIDMzIDM0IDM1IDM2IDM3IDM4IDM5IDQwIDQxIDQyIDQzIDQ0IDQ1IDQ2IDQ3IDQ4IDQ5IDUwIDUxIDUyIDUzIDU0IDU1IDU2IDU3IDU4IiwidmFyICRleHBvcnQgPSByZXF1aXJlKCcuL19leHBvcnQnKTtcbi8vIDE5LjEuMi40IC8gMTUuMi4zLjYgT2JqZWN0LmRlZmluZVByb3BlcnR5KE8sIFAsIEF0dHJpYnV0ZXMpXG4kZXhwb3J0KCRleHBvcnQuUyArICRleHBvcnQuRiAqICFyZXF1aXJlKCcuL19kZXNjcmlwdG9ycycpLCAnT2JqZWN0Jywge2RlZmluZVByb3BlcnR5OiByZXF1aXJlKCcuL19vYmplY3QtZHAnKS5mfSk7XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL2VzNi5vYmplY3QuZGVmaW5lLXByb3BlcnR5LmpzXG4vLyBtb2R1bGUgaWQgPSAyMVxuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAyIDMgNCA1IDYgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTcgMTggMTkgMjAgMjEgMjIgMjMgMjQgMjUgMjYgMjcgMjggMjkgMzAgMzEgMzIgMzMgMzQgMzUgMzYgMzcgMzggMzkgNDAgNDEgNDIgNDMgNDQgNDUgNDYgNDcgNDggNDkgNTAgNTEgNTIgNTMgNTQgNTUgNTYgNTcgNTgiLCIvKipcbiAqIENvcHlyaWdodCBzaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogUHJlc3RhU2hvcCBpcyBhbiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS5tZC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL2RldmRvY3MucHJlc3Rhc2hvcC5jb20vIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCBTaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICovXG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuLyoqXG4gKiBDbGFzcyBpcyByZXNwb25zaWJsZSBmb3IgaGFuZGxpbmcgR3JpZCBldmVudHNcbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgR3JpZCB7XG4gIC8qKlxuICAgKiBHcmlkIGlkXG4gICAqXG4gICAqIEBwYXJhbSB7c3RyaW5nfSBpZFxuICAgKi9cbiAgY29uc3RydWN0b3IoaWQpIHtcbiAgICB0aGlzLmlkID0gaWQ7XG4gICAgdGhpcy4kY29udGFpbmVyID0gJCgnIycgKyB0aGlzLmlkICsgJ19ncmlkJyk7XG4gIH1cblxuICAvKipcbiAgICogR2V0IGdyaWQgaWRcbiAgICpcbiAgICogQHJldHVybnMge3N0cmluZ31cbiAgICovXG4gIGdldElkKCkge1xuICAgIHJldHVybiB0aGlzLmlkO1xuICB9XG5cbiAgLyoqXG4gICAqIEdldCBncmlkIGNvbnRhaW5lclxuICAgKlxuICAgKiBAcmV0dXJucyB7alF1ZXJ5fVxuICAgKi9cbiAgZ2V0Q29udGFpbmVyKCkge1xuICAgIHJldHVybiB0aGlzLiRjb250YWluZXI7XG4gIH1cblxuICAvKipcbiAgICogR2V0IGdyaWQgaGVhZGVyIGNvbnRhaW5lclxuICAgKlxuICAgKiBAcmV0dXJucyB7alF1ZXJ5fVxuICAgKi9cbiAgZ2V0SGVhZGVyQ29udGFpbmVyKCkge1xuICAgIHJldHVybiB0aGlzLiRjb250YWluZXIuY2xvc2VzdCgnLmpzLWdyaWQtcGFuZWwnKS5maW5kKCcuanMtZ3JpZC1oZWFkZXInKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBFeHRlbmQgZ3JpZCB3aXRoIGV4dGVybmFsIGV4dGVuc2lvbnNcbiAgICpcbiAgICogQHBhcmFtIHtvYmplY3R9IGV4dGVuc2lvblxuICAgKi9cbiAgYWRkRXh0ZW5zaW9uKGV4dGVuc2lvbikge1xuICAgIGV4dGVuc2lvbi5leHRlbmQodGhpcyk7XG4gIH1cbn1cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL2NvbXBvbmVudHMvZ3JpZC9ncmlkLmpzIiwiLyoqXG4gKiBDb3B5cmlnaHQgc2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIFByZXN0YVNob3AgaXMgYW4gSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UubWQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly9kZXZkb2NzLnByZXN0YXNob3AuY29tLyBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9ycyA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgU2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqL1xuXG5pbXBvcnQgcmVzZXRTZWFyY2ggZnJvbSAnQGFwcC91dGlscy9yZXNldF9zZWFyY2gnO1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbi8qKlxuICogQ2xhc3MgRmlsdGVyc1Jlc2V0RXh0ZW5zaW9uIGV4dGVuZHMgZ3JpZCB3aXRoIGZpbHRlcnMgcmVzZXR0aW5nXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIEZpbHRlcnNSZXNldEV4dGVuc2lvbiB7XG5cbiAgLyoqXG4gICAqIEV4dGVuZCBncmlkXG4gICAqXG4gICAqIEBwYXJhbSB7R3JpZH0gZ3JpZFxuICAgKi9cbiAgZXh0ZW5kKGdyaWQpIHtcbiAgICBncmlkLmdldENvbnRhaW5lcigpLm9uKCdjbGljaycsICcuanMtcmVzZXQtc2VhcmNoJywgKGV2ZW50KSA9PiB7XG4gICAgICByZXNldFNlYXJjaCgkKGV2ZW50LmN1cnJlbnRUYXJnZXQpLmRhdGEoJ3VybCcpLCAkKGV2ZW50LmN1cnJlbnRUYXJnZXQpLmRhdGEoJ3JlZGlyZWN0JykpO1xuICAgIH0pO1xuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2ZpbHRlcnMtcmVzZXQtZXh0ZW5zaW9uLmpzIiwiLyoqXG4gKiBDb3B5cmlnaHQgc2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIFByZXN0YVNob3AgaXMgYW4gSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UubWQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly9kZXZkb2NzLnByZXN0YXNob3AuY29tLyBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9ycyA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgU2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqL1xuXG5pbXBvcnQgVGFibGVTb3J0aW5nIGZyb20gJ0BhcHAvdXRpbHMvdGFibGUtc29ydGluZyc7XG5cbi8qKlxuICogQ2xhc3MgUmVsb2FkTGlzdEV4dGVuc2lvbiBleHRlbmRzIGdyaWQgd2l0aCBcIkxpc3QgcmVsb2FkXCIgYWN0aW9uXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIFNvcnRpbmdFeHRlbnNpb24ge1xuICAvKipcbiAgICogRXh0ZW5kIGdyaWRcbiAgICpcbiAgICogQHBhcmFtIHtHcmlkfSBncmlkXG4gICAqL1xuICBleHRlbmQoZ3JpZCkge1xuICAgIGNvbnN0ICRzb3J0YWJsZVRhYmxlID0gZ3JpZC5nZXRDb250YWluZXIoKS5maW5kKCd0YWJsZS50YWJsZScpO1xuXG4gICAgbmV3IFRhYmxlU29ydGluZygkc29ydGFibGVUYWJsZSkuYXR0YWNoKCk7XG4gIH1cbn1cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vc29ydGluZy1leHRlbnNpb24uanMiLCIvKipcbiAqIENvcHlyaWdodCBzaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogUHJlc3RhU2hvcCBpcyBhbiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS5tZC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL2RldmRvY3MucHJlc3Rhc2hvcC5jb20vIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCBTaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICovXG5cbi8qKlxuICogQ2xhc3MgUmVsb2FkTGlzdEV4dGVuc2lvbiBleHRlbmRzIGdyaWQgd2l0aCBcIkxpc3QgcmVsb2FkXCIgYWN0aW9uXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIFJlbG9hZExpc3RFeHRlbnNpb24ge1xuICAvKipcbiAgICogRXh0ZW5kIGdyaWRcbiAgICpcbiAgICogQHBhcmFtIHtHcmlkfSBncmlkXG4gICAqL1xuICBleHRlbmQoZ3JpZCkge1xuICAgIGdyaWQuZ2V0SGVhZGVyQ29udGFpbmVyKCkub24oJ2NsaWNrJywgJy5qcy1jb21tb25fcmVmcmVzaF9saXN0LWdyaWQtYWN0aW9uJywgKCkgPT4ge1xuICAgICAgbG9jYXRpb24ucmVsb2FkKCk7XG4gICAgfSk7XG4gIH1cbn1cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vcmVsb2FkLWxpc3QtZXh0ZW5zaW9uLmpzIiwiLyoqXG4gKiBDb3B5cmlnaHQgc2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIFByZXN0YVNob3AgaXMgYW4gSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UubWQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly9kZXZkb2NzLnByZXN0YXNob3AuY29tLyBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9ycyA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgU2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqL1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbi8qKlxuICogQ2xhc3MgRXhwb3J0VG9TcWxNYW5hZ2VyRXh0ZW5zaW9uIGV4dGVuZHMgZ3JpZCB3aXRoIGV4cG9ydGluZyBxdWVyeSB0byBTUUwgTWFuYWdlclxuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBFeHBvcnRUb1NxbE1hbmFnZXJFeHRlbnNpb24ge1xuICAvKipcbiAgICogRXh0ZW5kIGdyaWRcbiAgICpcbiAgICogQHBhcmFtIHtHcmlkfSBncmlkXG4gICAqL1xuICBleHRlbmQoZ3JpZCkge1xuICAgIGdyaWQuZ2V0SGVhZGVyQ29udGFpbmVyKCkub24oJ2NsaWNrJywgJy5qcy1jb21tb25fc2hvd19xdWVyeS1ncmlkLWFjdGlvbicsICgpID0+IHRoaXMuX29uU2hvd1NxbFF1ZXJ5Q2xpY2soZ3JpZCkpO1xuICAgIGdyaWQuZ2V0SGVhZGVyQ29udGFpbmVyKCkub24oJ2NsaWNrJywgJy5qcy1jb21tb25fZXhwb3J0X3NxbF9tYW5hZ2VyLWdyaWQtYWN0aW9uJywgKCkgPT4gdGhpcy5fb25FeHBvcnRTcWxNYW5hZ2VyQ2xpY2soZ3JpZCkpO1xuICB9XG5cbiAgLyoqXG4gICAqIEludm9rZWQgd2hlbiBjbGlja2luZyBvbiB0aGUgXCJzaG93IHNxbCBxdWVyeVwiIHRvb2xiYXIgYnV0dG9uXG4gICAqXG4gICAqIEBwYXJhbSB7R3JpZH0gZ3JpZFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX29uU2hvd1NxbFF1ZXJ5Q2xpY2soZ3JpZCkge1xuICAgIGNvbnN0ICRzcWxNYW5hZ2VyRm9ybSA9ICQoJyMnICsgZ3JpZC5nZXRJZCgpICsgJ19jb21tb25fc2hvd19xdWVyeV9tb2RhbF9mb3JtJyk7XG4gICAgdGhpcy5fZmlsbEV4cG9ydEZvcm0oJHNxbE1hbmFnZXJGb3JtLCBncmlkKTtcblxuICAgIGNvbnN0ICRtb2RhbCA9ICQoJyMnICsgZ3JpZC5nZXRJZCgpICsgJ19ncmlkX2NvbW1vbl9zaG93X3F1ZXJ5X21vZGFsJyk7XG4gICAgJG1vZGFsLm1vZGFsKCdzaG93Jyk7XG5cbiAgICAkbW9kYWwub24oJ2NsaWNrJywgJy5idG4tc3FsLXN1Ym1pdCcsICgpID0+ICRzcWxNYW5hZ2VyRm9ybS5zdWJtaXQoKSk7XG4gIH1cblxuICAvKipcbiAgICogSW52b2tlZCB3aGVuIGNsaWNraW5nIG9uIHRoZSBcImV4cG9ydCB0byB0aGUgc3FsIHF1ZXJ5XCIgdG9vbGJhciBidXR0b25cbiAgICpcbiAgICogQHBhcmFtIHtHcmlkfSBncmlkXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfb25FeHBvcnRTcWxNYW5hZ2VyQ2xpY2soZ3JpZCkge1xuICAgIGNvbnN0ICRzcWxNYW5hZ2VyRm9ybSA9ICQoJyMnICsgZ3JpZC5nZXRJZCgpICsgJ19jb21tb25fc2hvd19xdWVyeV9tb2RhbF9mb3JtJyk7XG5cbiAgICB0aGlzLl9maWxsRXhwb3J0Rm9ybSgkc3FsTWFuYWdlckZvcm0sIGdyaWQpO1xuXG4gICAgJHNxbE1hbmFnZXJGb3JtLnN1Ym1pdCgpO1xuICB9XG5cbiAgLyoqXG4gICAqIEZpbGwgZXhwb3J0IGZvcm0gd2l0aCBTUUwgYW5kIGl0J3MgbmFtZVxuICAgKlxuICAgKiBAcGFyYW0ge2pRdWVyeX0gJHNxbE1hbmFnZXJGb3JtXG4gICAqIEBwYXJhbSB7R3JpZH0gZ3JpZFxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX2ZpbGxFeHBvcnRGb3JtKCRzcWxNYW5hZ2VyRm9ybSwgZ3JpZCkge1xuICAgIGNvbnN0IHF1ZXJ5ID0gZ3JpZC5nZXRDb250YWluZXIoKS5maW5kKCcuanMtZ3JpZC10YWJsZScpLmRhdGEoJ3F1ZXJ5Jyk7XG5cbiAgICAkc3FsTWFuYWdlckZvcm0uZmluZCgndGV4dGFyZWFbbmFtZT1cInNxbFwiXScpLnZhbChxdWVyeSk7XG4gICAgJHNxbE1hbmFnZXJGb3JtLmZpbmQoJ2lucHV0W25hbWU9XCJuYW1lXCJdJykudmFsKHRoaXMuX2dldE5hbWVGcm9tQnJlYWRjcnVtYigpKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBHZXQgZXhwb3J0IG5hbWUgZnJvbSBwYWdlJ3MgYnJlYWRjcnVtYlxuICAgKlxuICAgKiBAcmV0dXJuIHtTdHJpbmd9XG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfZ2V0TmFtZUZyb21CcmVhZGNydW1iKCkge1xuICAgIGNvbnN0ICRicmVhZGNydW1icyA9ICQoJy5oZWFkZXItdG9vbGJhcicpLmZpbmQoJy5icmVhZGNydW1iLWl0ZW0nKTtcbiAgICBsZXQgbmFtZSA9ICcnO1xuXG4gICAgJGJyZWFkY3J1bWJzLmVhY2goKGksIGl0ZW0pID0+IHtcbiAgICAgIGNvbnN0ICRicmVhZGNydW1iID0gJChpdGVtKTtcblxuICAgICAgY29uc3QgYnJlYWRjcnVtYlRpdGxlID0gMCA8ICRicmVhZGNydW1iLmZpbmQoJ2EnKS5sZW5ndGggP1xuICAgICAgICAkYnJlYWRjcnVtYi5maW5kKCdhJykudGV4dCgpIDpcbiAgICAgICAgJGJyZWFkY3J1bWIudGV4dCgpO1xuXG4gICAgICBpZiAoMCA8IG5hbWUubGVuZ3RoKSB7XG4gICAgICAgIG5hbWUgPSBuYW1lLmNvbmNhdCgnID4gJyk7XG4gICAgICB9XG5cbiAgICAgIG5hbWUgPSBuYW1lLmNvbmNhdChicmVhZGNydW1iVGl0bGUpO1xuICAgIH0pO1xuXG4gICAgcmV0dXJuIG5hbWU7XG4gIH1cbn1cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vZXhwb3J0LXRvLXNxbC1tYW5hZ2VyLWV4dGVuc2lvbi5qcyIsInZhciBjb3JlID0gbW9kdWxlLmV4cG9ydHMgPSB7dmVyc2lvbjogJzIuNC4wJ307XG5pZih0eXBlb2YgX19lID09ICdudW1iZXInKV9fZSA9IGNvcmU7IC8vIGVzbGludC1kaXNhYmxlLWxpbmUgbm8tdW5kZWZcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2NvcmUuanNcbi8vIG1vZHVsZSBpZCA9IDNcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMiAzIDQgNSA2IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE3IDE4IDE5IDIwIDIxIDIyIDIzIDI0IDI1IDI2IDI3IDI4IDI5IDMwIDMxIDMyIDMzIDM0IDM1IDM2IDM3IDM4IDM5IDQwIDQxIDQyIDQzIDQ0IDQ1IDQ2IDQ3IDQ4IDQ5IDUwIDUxIDUyIDUzIDU0IDU1IDU2IDU3IDU4IiwiLyoqXG4gKiBDb3B5cmlnaHQgc2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIFByZXN0YVNob3AgaXMgYW4gSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UubWQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly9kZXZkb2NzLnByZXN0YXNob3AuY29tLyBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9ycyA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgU2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqL1xuXG4vKipcbiAqIFJlc3BvbnNpYmxlIGZvciBncmlkIGZpbHRlcnMgc2VhcmNoIGFuZCByZXNldCBidXR0b24gYXZhaWxhYmlsaXR5IHdoZW4gZmlsdGVyIGlucHV0cyBjaGFuZ2VzLlxuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBGaWx0ZXJzU3VibWl0QnV0dG9uRW5hYmxlckV4dGVuc2lvbiB7XG5cbiAgLyoqXG4gICAqIEV4dGVuZCBncmlkXG4gICAqXG4gICAqIEBwYXJhbSB7R3JpZH0gZ3JpZFxuICAgKi9cbiAgZXh0ZW5kKGdyaWQpIHtcbiAgICBjb25zdCAkZmlsdGVyc1JvdyA9IGdyaWQuZ2V0Q29udGFpbmVyKCkuZmluZCgnLmNvbHVtbi1maWx0ZXJzJyk7XG4gICAgJGZpbHRlcnNSb3cuZmluZCgnLmdyaWQtc2VhcmNoLWJ1dHRvbicpLnByb3AoJ2Rpc2FibGVkJywgdHJ1ZSk7XG5cbiAgICAkZmlsdGVyc1Jvdy5maW5kKCdpbnB1dDpub3QoLmpzLWJ1bGstYWN0aW9uLXNlbGVjdC1hbGwpLCBzZWxlY3QnKS5vbignaW5wdXQgZHAuY2hhbmdlJywgKCkgPT4ge1xuICAgICAgJGZpbHRlcnNSb3cuZmluZCgnLmdyaWQtc2VhcmNoLWJ1dHRvbicpLnByb3AoJ2Rpc2FibGVkJywgZmFsc2UpO1xuICAgICAgJGZpbHRlcnNSb3cuZmluZCgnLmpzLWdyaWQtcmVzZXQtYnV0dG9uJykucHJvcCgnaGlkZGVuJywgZmFsc2UpO1xuICAgIH0pO1xuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2ZpbHRlcnMtc3VibWl0LWJ1dHRvbi1lbmFibGVyLWV4dGVuc2lvbi5qcyIsIi8qKlxuICogQ29weXJpZ2h0IHNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBQcmVzdGFTaG9wIGlzIGFuIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLm1kLlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vZGV2ZG9jcy5wcmVzdGFzaG9wLmNvbS8gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnMgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IFNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKi9cblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4vKipcbiAqIENsYXNzIEJ1bGtBY3Rpb25TZWxlY3RDaGVja2JveEV4dGVuc2lvblxuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBCdWxrQWN0aW9uQ2hlY2tib3hFeHRlbnNpb24ge1xuICAvKipcbiAgICogRXh0ZW5kIGdyaWQgd2l0aCBidWxrIGFjdGlvbiBjaGVja2JveGVzIGhhbmRsaW5nIGZ1bmN0aW9uYWxpdHlcbiAgICpcbiAgICogQHBhcmFtIHtHcmlkfSBncmlkXG4gICAqL1xuICBleHRlbmQoZ3JpZCkge1xuICAgIHRoaXMuX2hhbmRsZUJ1bGtBY3Rpb25DaGVja2JveFNlbGVjdChncmlkKTtcbiAgICB0aGlzLl9oYW5kbGVCdWxrQWN0aW9uU2VsZWN0QWxsQ2hlY2tib3goZ3JpZCk7XG4gIH1cblxuICAvKipcbiAgICogSGFuZGxlcyBcIlNlbGVjdCBhbGxcIiBidXR0b24gaW4gdGhlIGdyaWRcbiAgICpcbiAgICogQHBhcmFtIHtHcmlkfSBncmlkXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfaGFuZGxlQnVsa0FjdGlvblNlbGVjdEFsbENoZWNrYm94KGdyaWQpIHtcbiAgICBncmlkLmdldENvbnRhaW5lcigpLm9uKCdjaGFuZ2UnLCAnLmpzLWJ1bGstYWN0aW9uLXNlbGVjdC1hbGwnLCAoZSkgPT4ge1xuICAgICAgY29uc3QgJGNoZWNrYm94ID0gJChlLmN1cnJlbnRUYXJnZXQpO1xuXG4gICAgICBjb25zdCBpc0NoZWNrZWQgPSAkY2hlY2tib3guaXMoJzpjaGVja2VkJyk7XG4gICAgICBpZiAoaXNDaGVja2VkKSB7XG4gICAgICAgIHRoaXMuX2VuYWJsZUJ1bGtBY3Rpb25zQnRuKGdyaWQpO1xuICAgICAgfSBlbHNlIHtcbiAgICAgICAgdGhpcy5fZGlzYWJsZUJ1bGtBY3Rpb25zQnRuKGdyaWQpO1xuICAgICAgfVxuXG4gICAgICBncmlkLmdldENvbnRhaW5lcigpLmZpbmQoJy5qcy1idWxrLWFjdGlvbi1jaGVja2JveCcpLnByb3AoJ2NoZWNrZWQnLCBpc0NoZWNrZWQpO1xuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIEhhbmRsZXMgZWFjaCBidWxrIGFjdGlvbiBjaGVja2JveCBzZWxlY3QgaW4gdGhlIGdyaWRcbiAgICpcbiAgICogQHBhcmFtIHtHcmlkfSBncmlkXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfaGFuZGxlQnVsa0FjdGlvbkNoZWNrYm94U2VsZWN0KGdyaWQpIHtcbiAgICBncmlkLmdldENvbnRhaW5lcigpLm9uKCdjaGFuZ2UnLCAnLmpzLWJ1bGstYWN0aW9uLWNoZWNrYm94JywgKCkgPT4ge1xuICAgICAgY29uc3QgY2hlY2tlZFJvd3NDb3VudCA9IGdyaWQuZ2V0Q29udGFpbmVyKCkuZmluZCgnLmpzLWJ1bGstYWN0aW9uLWNoZWNrYm94OmNoZWNrZWQnKS5sZW5ndGg7XG5cbiAgICAgIGlmIChjaGVja2VkUm93c0NvdW50ID4gMCkge1xuICAgICAgICB0aGlzLl9lbmFibGVCdWxrQWN0aW9uc0J0bihncmlkKTtcbiAgICAgIH0gZWxzZSB7XG4gICAgICAgIHRoaXMuX2Rpc2FibGVCdWxrQWN0aW9uc0J0bihncmlkKTtcbiAgICAgIH1cbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBFbmFibGUgYnVsayBhY3Rpb25zIGJ1dHRvblxuICAgKlxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9lbmFibGVCdWxrQWN0aW9uc0J0bihncmlkKSB7XG4gICAgZ3JpZC5nZXRDb250YWluZXIoKS5maW5kKCcuanMtYnVsay1hY3Rpb25zLWJ0bicpLnByb3AoJ2Rpc2FibGVkJywgZmFsc2UpO1xuICB9XG5cbiAgLyoqXG4gICAqIERpc2FibGUgYnVsayBhY3Rpb25zIGJ1dHRvblxuICAgKlxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9kaXNhYmxlQnVsa0FjdGlvbnNCdG4oZ3JpZCkge1xuICAgIGdyaWQuZ2V0Q29udGFpbmVyKCkuZmluZCgnLmpzLWJ1bGstYWN0aW9ucy1idG4nKS5wcm9wKCdkaXNhYmxlZCcsIHRydWUpO1xuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9jb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2J1bGstYWN0aW9uLWNoZWNrYm94LWV4dGVuc2lvbi5qcyIsIi8qKlxuICogQ29weXJpZ2h0IHNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBQcmVzdGFTaG9wIGlzIGFuIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLm1kLlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vZGV2ZG9jcy5wcmVzdGFzaG9wLmNvbS8gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnMgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IFNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKi9cblxuaW1wb3J0IENvbmZpcm1Nb2RhbCBmcm9tICdAY29tcG9uZW50cy9tb2RhbCc7XG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuLyoqXG4gKiBIYW5kbGVzIHN1Ym1pdCBvZiBncmlkIGFjdGlvbnNcbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgU3VibWl0QnVsa0FjdGlvbkV4dGVuc2lvbiB7XG4gIGNvbnN0cnVjdG9yKCkge1xuICAgIHJldHVybiB7XG4gICAgICBleHRlbmQ6IChncmlkKSA9PiB0aGlzLmV4dGVuZChncmlkKSxcbiAgICB9O1xuICB9XG5cbiAgLyoqXG4gICAqIEV4dGVuZCBncmlkIHdpdGggYnVsayBhY3Rpb24gc3VibWl0dGluZ1xuICAgKlxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICovXG4gIGV4dGVuZChncmlkKSB7XG4gICAgZ3JpZC5nZXRDb250YWluZXIoKS5vbignY2xpY2snLCAnLmpzLWJ1bGstYWN0aW9uLXN1Ym1pdC1idG4nLCAoZXZlbnQpID0+IHtcbiAgICAgIHRoaXMuc3VibWl0KGV2ZW50LCBncmlkKTtcbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBIYW5kbGUgYnVsayBhY3Rpb24gc3VibWl0dGluZ1xuICAgKlxuICAgKiBAcGFyYW0ge0V2ZW50fSBldmVudFxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIHN1Ym1pdChldmVudCwgZ3JpZCkge1xuICAgIGNvbnN0ICRzdWJtaXRCdG4gPSAkKGV2ZW50LmN1cnJlbnRUYXJnZXQpO1xuICAgIGNvbnN0IGNvbmZpcm1NZXNzYWdlID0gJHN1Ym1pdEJ0bi5kYXRhKCdjb25maXJtLW1lc3NhZ2UnKTtcbiAgICBjb25zdCBjb25maXJtVGl0bGUgPSAkc3VibWl0QnRuLmRhdGEoJ2NvbmZpcm1UaXRsZScpO1xuXG4gICAgaWYgKGNvbmZpcm1NZXNzYWdlICE9PSB1bmRlZmluZWQgJiYgMCA8IGNvbmZpcm1NZXNzYWdlLmxlbmd0aCkge1xuICAgICAgaWYgKGNvbmZpcm1UaXRsZSAhPT0gdW5kZWZpbmVkKSB7XG4gICAgICAgIHRoaXMuc2hvd0NvbmZpcm1Nb2RhbCgkc3VibWl0QnRuLCBncmlkLCBjb25maXJtTWVzc2FnZSwgY29uZmlybVRpdGxlKTtcbiAgICAgIH0gZWxzZSBpZiAoY29uZmlybShjb25maXJtTWVzc2FnZSkpIHtcbiAgICAgICAgdGhpcy5wb3N0Rm9ybSgkc3VibWl0QnRuLCBncmlkKTtcbiAgICAgIH1cbiAgICB9IGVsc2Uge1xuICAgICAgdGhpcy5wb3N0Rm9ybSgkc3VibWl0QnRuLCBncmlkKTtcbiAgICB9XG4gIH1cblxuICAvKipcbiAgICogQHBhcmFtIHtqUXVlcnl9ICRzdWJtaXRCdG5cbiAgICogQHBhcmFtIHtHcmlkfSBncmlkXG4gICAqIEBwYXJhbSB7c3RyaW5nfSBjb25maXJtTWVzc2FnZVxuICAgKiBAcGFyYW0ge3N0cmluZ30gY29uZmlybVRpdGxlXG4gICAqL1xuICBzaG93Q29uZmlybU1vZGFsKCRzdWJtaXRCdG4sIGdyaWQsIGNvbmZpcm1NZXNzYWdlLCBjb25maXJtVGl0bGUpIHtcbiAgICBjb25zdCBjb25maXJtQnV0dG9uTGFiZWwgPSAkc3VibWl0QnRuLmRhdGEoJ2NvbmZpcm1CdXR0b25MYWJlbCcpO1xuICAgIGNvbnN0IGNsb3NlQnV0dG9uTGFiZWwgPSAkc3VibWl0QnRuLmRhdGEoJ2Nsb3NlQnV0dG9uTGFiZWwnKTtcbiAgICBjb25zdCBjb25maXJtQnV0dG9uQ2xhc3MgPSAkc3VibWl0QnRuLmRhdGEoJ2NvbmZpcm1CdXR0b25DbGFzcycpO1xuXG4gICAgY29uc3QgbW9kYWwgPSBuZXcgQ29uZmlybU1vZGFsKHtcbiAgICAgIGlkOiBgJHtncmlkLmdldElkKCl9X2dyaWRfY29uZmlybV9tb2RhbGAsXG4gICAgICBjb25maXJtVGl0bGUsXG4gICAgICBjb25maXJtTWVzc2FnZSxcbiAgICAgIGNvbmZpcm1CdXR0b25MYWJlbCxcbiAgICAgIGNsb3NlQnV0dG9uTGFiZWwsXG4gICAgICBjb25maXJtQnV0dG9uQ2xhc3MsXG4gICAgfSwgKCkgPT4gdGhpcy5wb3N0Rm9ybSgkc3VibWl0QnRuLCBncmlkKSk7XG5cbiAgICBtb2RhbC5zaG93KCk7XG4gIH1cblxuICAvKipcbiAgICogQHBhcmFtIHtqUXVlcnl9ICRzdWJtaXRCdG5cbiAgICogQHBhcmFtIHtHcmlkfSBncmlkXG4gICAqL1xuICBwb3N0Rm9ybSgkc3VibWl0QnRuLCBncmlkKSB7XG4gICAgY29uc3QgJGZvcm0gPSAkKCcjJyArIGdyaWQuZ2V0SWQoKSArICdfZmlsdGVyX2Zvcm0nKTtcblxuICAgICRmb3JtLmF0dHIoJ2FjdGlvbicsICRzdWJtaXRCdG4uZGF0YSgnZm9ybS11cmwnKSk7XG4gICAgJGZvcm0uYXR0cignbWV0aG9kJywgJHN1Ym1pdEJ0bi5kYXRhKCdmb3JtLW1ldGhvZCcpKTtcbiAgICAkZm9ybS5zdWJtaXQoKTtcbiAgfVxufVxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9zdWJtaXQtYnVsay1hY3Rpb24tZXh0ZW5zaW9uLmpzIiwiLyoqXG4gKiBDb3B5cmlnaHQgc2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIFByZXN0YVNob3AgaXMgYW4gSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UubWQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly9kZXZkb2NzLnByZXN0YXNob3AuY29tLyBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9ycyA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgU2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqL1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbi8qKlxuICogQ2xhc3MgTGlua1Jvd0FjdGlvbkV4dGVuc2lvbiBoYW5kbGVzIGxpbmsgcm93IGFjdGlvbnNcbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgTGlua1Jvd0FjdGlvbkV4dGVuc2lvbiB7XG4gIC8qKlxuICAgKiBFeHRlbmQgZ3JpZFxuICAgKlxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICovXG4gIGV4dGVuZChncmlkKSB7XG4gICAgdGhpcy5pbml0Um93TGlua3MoZ3JpZCk7XG4gICAgdGhpcy5pbml0Q29uZmlybWFibGVBY3Rpb25zKGdyaWQpO1xuICB9XG5cbiAgLyoqXG4gICAqIEV4dGVuZCBncmlkXG4gICAqXG4gICAqIEBwYXJhbSB7R3JpZH0gZ3JpZFxuICAgKi9cbiAgaW5pdENvbmZpcm1hYmxlQWN0aW9ucyhncmlkKSB7XG4gICAgZ3JpZC5nZXRDb250YWluZXIoKS5vbignY2xpY2snLCAnLmpzLWxpbmstcm93LWFjdGlvbicsIChldmVudCkgPT4ge1xuICAgICAgY29uc3QgY29uZmlybU1lc3NhZ2UgPSAkKGV2ZW50LmN1cnJlbnRUYXJnZXQpLmRhdGEoJ2NvbmZpcm0tbWVzc2FnZScpO1xuXG4gICAgICBpZiAoY29uZmlybU1lc3NhZ2UubGVuZ3RoICYmICFjb25maXJtKGNvbmZpcm1NZXNzYWdlKSkge1xuICAgICAgICBldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgfVxuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIEFkZCBhIGNsaWNrIGV2ZW50IG9uIHJvd3MgdGhhdCBtYXRjaGVzIHRoZSBmaXJzdCBsaW5rIGFjdGlvbiAoaWYgcHJlc2VudClcbiAgICpcbiAgICogQHBhcmFtIHtHcmlkfSBncmlkXG4gICAqL1xuICBpbml0Um93TGlua3MoZ3JpZCkge1xuICAgICQoJ3RyJywgZ3JpZC5nZXRDb250YWluZXIoKSkuZWFjaChmdW5jdGlvbiBpbml0RWFjaFJvdygpIHtcbiAgICAgIGNvbnN0ICRwYXJlbnRSb3cgPSAkKHRoaXMpO1xuXG4gICAgICAkKCcuanMtbGluay1yb3ctYWN0aW9uW2RhdGEtY2xpY2thYmxlLXJvdz0xXTpmaXJzdCcsICRwYXJlbnRSb3cpLmVhY2goZnVuY3Rpb24gcHJvcGFnYXRlRmlyc3RMaW5rQWN0aW9uKCkge1xuICAgICAgICBjb25zdCAkcm93QWN0aW9uID0gJCh0aGlzKTtcbiAgICAgICAgY29uc3QgJHBhcmVudENlbGwgPSAkcm93QWN0aW9uLmNsb3Nlc3QoJ3RkJyk7XG4gICAgICAgIFxuICAgICAgICBjb25zdCBjbGlja2FibGVDZWxscyA9ICQoJ3RkLmNsaWNrYWJsZScsICRwYXJlbnRSb3cpXG4gICAgICAgICAgLm5vdCgkcGFyZW50Q2VsbClcbiAgICAgICAgO1xuXG4gICAgICAgIGNsaWNrYWJsZUNlbGxzLmFkZENsYXNzKCdjdXJzb3ItcG9pbnRlcicpLmNsaWNrKCgpID0+IHtcbiAgICAgICAgICBjb25zdCBjb25maXJtTWVzc2FnZSA9ICRyb3dBY3Rpb24uZGF0YSgnY29uZmlybS1tZXNzYWdlJyk7XG5cbiAgICAgICAgICBpZiAoIWNvbmZpcm1NZXNzYWdlLmxlbmd0aCB8fCBjb25maXJtKGNvbmZpcm1NZXNzYWdlKSkge1xuICAgICAgICAgICAgZG9jdW1lbnQubG9jYXRpb24gPSAkcm93QWN0aW9uLmF0dHIoJ2hyZWYnKTtcbiAgICAgICAgICB9XG4gICAgICAgIH0pO1xuICAgICAgfSk7XG4gICAgfSk7XG4gIH1cbn1cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL2NvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vbGluay1yb3ctYWN0aW9uLWV4dGVuc2lvbi5qcyIsIi8qKlxuICogQ29weXJpZ2h0IHNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBQcmVzdGFTaG9wIGlzIGFuIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLm1kLlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vZGV2ZG9jcy5wcmVzdGFzaG9wLmNvbS8gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnMgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IFNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKi9cblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4vKipcbiAqIENsYXNzIFN1Ym1pdFJvd0FjdGlvbkV4dGVuc2lvbiBoYW5kbGVzIHN1Ym1pdHRpbmcgb2Ygcm93IGFjdGlvblxuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBTdWJtaXRSb3dBY3Rpb25FeHRlbnNpb24ge1xuICAvKipcbiAgICogRXh0ZW5kIGdyaWRcbiAgICpcbiAgICogQHBhcmFtIHtHcmlkfSBncmlkXG4gICAqL1xuICBleHRlbmQoZ3JpZCkge1xuICAgIGdyaWQuZ2V0Q29udGFpbmVyKCkub24oJ2NsaWNrJywgJy5qcy1zdWJtaXQtcm93LWFjdGlvbicsIChldmVudCkgPT4ge1xuICAgICAgZXZlbnQucHJldmVudERlZmF1bHQoKTtcblxuICAgICAgY29uc3QgJGJ1dHRvbiA9ICQoZXZlbnQuY3VycmVudFRhcmdldCk7XG4gICAgICBjb25zdCBjb25maXJtTWVzc2FnZSA9ICRidXR0b24uZGF0YSgnY29uZmlybS1tZXNzYWdlJyk7XG5cbiAgICAgIGlmIChjb25maXJtTWVzc2FnZS5sZW5ndGggJiYgIWNvbmZpcm0oY29uZmlybU1lc3NhZ2UpKSB7XG4gICAgICAgIHJldHVybjtcbiAgICAgIH1cblxuICAgICAgY29uc3QgbWV0aG9kID0gJGJ1dHRvbi5kYXRhKCdtZXRob2QnKTtcbiAgICAgIGNvbnN0IGlzR2V0T3JQb3N0TWV0aG9kID0gWydHRVQnLCAnUE9TVCddLmluY2x1ZGVzKG1ldGhvZCk7XG5cbiAgICAgIGNvbnN0ICRmb3JtID0gJCgnPGZvcm0+Jywge1xuICAgICAgICAnYWN0aW9uJzogJGJ1dHRvbi5kYXRhKCd1cmwnKSxcbiAgICAgICAgJ21ldGhvZCc6IGlzR2V0T3JQb3N0TWV0aG9kID8gbWV0aG9kIDogJ1BPU1QnLFxuICAgICAgfSkuYXBwZW5kVG8oJ2JvZHknKTtcblxuICAgICAgaWYgKCFpc0dldE9yUG9zdE1ldGhvZCkge1xuICAgICAgICAkZm9ybS5hcHBlbmQoJCgnPGlucHV0PicsIHtcbiAgICAgICAgICAndHlwZSc6ICdfaGlkZGVuJyxcbiAgICAgICAgICAnbmFtZSc6ICdfbWV0aG9kJyxcbiAgICAgICAgICAndmFsdWUnOiBtZXRob2RcbiAgICAgICAgfSkpO1xuICAgICAgfVxuXG4gICAgICAkZm9ybS5zdWJtaXQoKTtcbiAgICB9KTtcbiAgfVxufVxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9hY3Rpb24vcm93L3N1Ym1pdC1yb3ctYWN0aW9uLWV4dGVuc2lvbi5qcyIsIi8qKlxuICogQ29weXJpZ2h0IHNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBQcmVzdGFTaG9wIGlzIGFuIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLm1kLlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vZGV2ZG9jcy5wcmVzdGFzaG9wLmNvbS8gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnMgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IFNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKi9cblxuaW1wb3J0IEV2ZW50RW1pdHRlckNsYXNzIGZyb20gJ2V2ZW50cyc7XG5cbi8qKlxuICogV2UgaW5zdGFuY2lhdGUgb25lIEV2ZW50RW1pdHRlciAocmVzdHJpY3RlZCB2aWEgYSBjb25zdCkgc28gdGhhdCBldmVyeSBjb21wb25lbnRzXG4gKiByZWdpc3Rlci9kaXNwYXRjaCBvbiB0aGUgc2FtZSBvbmUgYW5kIGNhbiBjb21tdW5pY2F0ZSB3aXRoIGVhY2ggb3RoZXIuXG4gKi9cbmV4cG9ydCBjb25zdCBFdmVudEVtaXR0ZXIgPSBuZXcgRXZlbnRFbWl0dGVyQ2xhc3MoKTtcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL2NvbXBvbmVudHMvZXZlbnQtZW1pdHRlci5qcyIsIm1vZHVsZS5leHBvcnRzID0gZnVuY3Rpb24oaXQpe1xuICByZXR1cm4gdHlwZW9mIGl0ID09PSAnb2JqZWN0JyA/IGl0ICE9PSBudWxsIDogdHlwZW9mIGl0ID09PSAnZnVuY3Rpb24nO1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2lzLW9iamVjdC5qc1xuLy8gbW9kdWxlIGlkID0gNFxuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAyIDMgNCA1IDYgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTcgMTggMTkgMjAgMjEgMjIgMjMgMjQgMjUgMjYgMjcgMjggMjkgMzAgMzEgMzIgMzMgMzQgMzUgMzYgMzcgMzggMzkgNDAgNDEgNDIgNDMgNDQgNDUgNDYgNDcgNDggNDkgNTAgNTEgNTIgNTMgNTQgNTUgNTYgNTcgNTgiLCIvKipcbiAqIENvcHlyaWdodCBzaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogUHJlc3RhU2hvcCBpcyBhbiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS5tZC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL2RldmRvY3MucHJlc3Rhc2hvcC5jb20vIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCBTaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICovXG5cbmNvbnN0ICQgPSBnbG9iYWwuJDtcblxuLyoqXG4gKiBNYWtlcyBhIHRhYmxlIHNvcnRhYmxlIGJ5IGNvbHVtbnMuXG4gKiBUaGlzIGZvcmNlcyBhIHBhZ2UgcmVsb2FkIHdpdGggbW9yZSBxdWVyeSBwYXJhbWV0ZXJzLlxuICovXG5jbGFzcyBUYWJsZVNvcnRpbmcge1xuXG4gIC8qKlxuICAgKiBAcGFyYW0ge2pRdWVyeX0gdGFibGVcbiAgICovXG4gIGNvbnN0cnVjdG9yKHRhYmxlKSB7XG4gICAgdGhpcy5zZWxlY3RvciA9ICcucHMtc29ydGFibGUtY29sdW1uJztcbiAgICB0aGlzLmNvbHVtbnMgPSAkKHRhYmxlKS5maW5kKHRoaXMuc2VsZWN0b3IpO1xuICB9XG5cbiAgLyoqXG4gICAqIEF0dGFjaGVzIHRoZSBsaXN0ZW5lcnNcbiAgICovXG4gIGF0dGFjaCgpIHtcbiAgICB0aGlzLmNvbHVtbnMub24oJ2NsaWNrJywgKGUpID0+IHtcbiAgICAgIGNvbnN0ICRjb2x1bW4gPSAkKGUuZGVsZWdhdGVUYXJnZXQpO1xuICAgICAgdGhpcy5fc29ydEJ5Q29sdW1uKCRjb2x1bW4sIHRoaXMuX2dldFRvZ2dsZWRTb3J0RGlyZWN0aW9uKCRjb2x1bW4pKTtcbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBTb3J0IHVzaW5nIGEgY29sdW1uIG5hbWVcbiAgICogQHBhcmFtIHtzdHJpbmd9IGNvbHVtbk5hbWVcbiAgICogQHBhcmFtIHtzdHJpbmd9IGRpcmVjdGlvbiBcImFzY1wiIG9yIFwiZGVzY1wiXG4gICAqL1xuICBzb3J0QnkoY29sdW1uTmFtZSwgZGlyZWN0aW9uKSB7XG4gICAgY29uc3QgJGNvbHVtbiA9IHRoaXMuY29sdW1ucy5pcyhgW2RhdGEtc29ydC1jb2wtbmFtZT1cIiR7Y29sdW1uTmFtZX1cIl1gKTtcbiAgICBpZiAoISRjb2x1bW4pIHtcbiAgICAgIHRocm93IG5ldyBFcnJvcihgQ2Fubm90IHNvcnQgYnkgXCIke2NvbHVtbk5hbWV9XCI6IGludmFsaWQgY29sdW1uYCk7XG4gICAgfVxuXG4gICAgdGhpcy5fc29ydEJ5Q29sdW1uKCRjb2x1bW4sIGRpcmVjdGlvbik7XG4gIH1cblxuICAvKipcbiAgICogU29ydCB1c2luZyBhIGNvbHVtbiBlbGVtZW50XG4gICAqIEBwYXJhbSB7alF1ZXJ5fSBjb2x1bW5cbiAgICogQHBhcmFtIHtzdHJpbmd9IGRpcmVjdGlvbiBcImFzY1wiIG9yIFwiZGVzY1wiXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfc29ydEJ5Q29sdW1uKGNvbHVtbiwgZGlyZWN0aW9uKSB7XG4gICAgd2luZG93LmxvY2F0aW9uID0gdGhpcy5fZ2V0VXJsKGNvbHVtbi5kYXRhKCdzb3J0Q29sTmFtZScpLCAoZGlyZWN0aW9uID09PSAnZGVzYycpID8gJ2Rlc2MnIDogJ2FzYycsIGNvbHVtbi5kYXRhKCdzb3J0UHJlZml4JykpO1xuICB9XG5cbiAgLyoqXG4gICAqIFJldHVybnMgdGhlIGludmVydGVkIGRpcmVjdGlvbiB0byBzb3J0IGFjY29yZGluZyB0byB0aGUgY29sdW1uJ3MgY3VycmVudCBvbmVcbiAgICogQHBhcmFtIHtqUXVlcnl9IGNvbHVtblxuICAgKiBAcmV0dXJuIHtzdHJpbmd9XG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfZ2V0VG9nZ2xlZFNvcnREaXJlY3Rpb24oY29sdW1uKSB7XG4gICAgcmV0dXJuIGNvbHVtbi5kYXRhKCdzb3J0RGlyZWN0aW9uJykgPT09ICdhc2MnID8gJ2Rlc2MnIDogJ2FzYyc7XG4gIH1cblxuICAvKipcbiAgICogUmV0dXJucyB0aGUgdXJsIGZvciB0aGUgc29ydGVkIHRhYmxlXG4gICAqIEBwYXJhbSB7c3RyaW5nfSBjb2xOYW1lXG4gICAqIEBwYXJhbSB7c3RyaW5nfSBkaXJlY3Rpb25cbiAgICogQHBhcmFtIHtzdHJpbmd9IHByZWZpeFxuICAgKiBAcmV0dXJuIHtzdHJpbmd9XG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfZ2V0VXJsKGNvbE5hbWUsIGRpcmVjdGlvbiwgcHJlZml4KSB7XG4gICAgY29uc3QgdXJsID0gbmV3IFVSTCh3aW5kb3cubG9jYXRpb24uaHJlZik7XG4gICAgY29uc3QgcGFyYW1zID0gdXJsLnNlYXJjaFBhcmFtcztcblxuICAgIGlmIChwcmVmaXgpIHtcbiAgICAgIHBhcmFtcy5zZXQocHJlZml4Kydbb3JkZXJCeV0nLCBjb2xOYW1lKTtcbiAgICAgIHBhcmFtcy5zZXQocHJlZml4Kydbc29ydE9yZGVyXScsIGRpcmVjdGlvbik7XG4gICAgfSBlbHNlIHtcbiAgICAgIHBhcmFtcy5zZXQoJ29yZGVyQnknLCBjb2xOYW1lKTtcbiAgICAgIHBhcmFtcy5zZXQoJ3NvcnRPcmRlcicsIGRpcmVjdGlvbik7XG4gICAgfVxuXG4gICAgcmV0dXJuIHVybC50b1N0cmluZygpO1xuICB9XG59XG5cbmV4cG9ydCBkZWZhdWx0IFRhYmxlU29ydGluZztcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL2FwcC91dGlscy90YWJsZS1zb3J0aW5nLmpzIiwiKGZ1bmN0aW9uKCkgeyBtb2R1bGUuZXhwb3J0cyA9IHdpbmRvd1tcImpRdWVyeVwiXTsgfSgpKTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyBleHRlcm5hbCBcImpRdWVyeVwiXG4vLyBtb2R1bGUgaWQgPSA0MlxuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAyIDUgNiA3IDggOSAxNyAyNiAzMCA0NCA0OCIsIi8qKlxuICogQ29weXJpZ2h0IHNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBQcmVzdGFTaG9wIGlzIGFuIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLm1kLlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vZGV2ZG9jcy5wcmVzdGFzaG9wLmNvbS8gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnMgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IFNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKi9cblxuLyoqXG4gKiBTZW5kIGEgUG9zdCBSZXF1ZXN0IHRvIHJlc2V0IHNlYXJjaCBBY3Rpb24uXG4gKi9cblxuY29uc3QgJCA9IGdsb2JhbC4kO1xuXG5jb25zdCBpbml0ID0gZnVuY3Rpb24gcmVzZXRTZWFyY2godXJsLCByZWRpcmVjdFVybCkge1xuICAgICQucG9zdCh1cmwpLnRoZW4oKCkgPT4gd2luZG93LmxvY2F0aW9uLmFzc2lnbihyZWRpcmVjdFVybCkpO1xufTtcblxuZXhwb3J0IGRlZmF1bHQgaW5pdDtcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL2FwcC91dGlscy9yZXNldF9zZWFyY2guanMiLCIvKipcbiAqIENvcHlyaWdodCBzaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogUHJlc3RhU2hvcCBpcyBhbiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS5tZC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL2RldmRvY3MucHJlc3Rhc2hvcC5jb20vIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCBTaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICovXG5cbmltcG9ydCB7RXZlbnRFbWl0dGVyfSBmcm9tICcuL2V2ZW50LWVtaXR0ZXInO1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbi8qKlxuICogVGhpcyBjbGFzcyBpcyB1c2VkIHRvIGF1dG9tYXRpY2FsbHkgdG9nZ2xlIHRyYW5zbGF0ZWQgaW5wdXRzIChkaXNwbGF5ZWQgd2l0aCBvbmVcbiAqIGlucHV0IGFuZCBhIGxhbmd1YWdlIHNlbGVjdG9yIHVzaW5nIHRoZSBUcmFuc2xhdGFibGVUeXBlIFN5bWZvbnkgZm9ybSB0eXBlKS5cbiAqIEFsc28gY29tcGF0aWJsZSB3aXRoIFRyYW5zbGF0YWJsZUZpZWxkIGNoYW5nZXMuXG4gKi9cbmNsYXNzIFRyYW5zbGF0YWJsZUlucHV0IHtcbiAgY29uc3RydWN0b3Iob3B0aW9ucykge1xuICAgIG9wdGlvbnMgPSBvcHRpb25zIHx8IHt9O1xuXG4gICAgdGhpcy5sb2NhbGVJdGVtU2VsZWN0b3IgPSBvcHRpb25zLmxvY2FsZUl0ZW1TZWxlY3RvciB8fCAnLmpzLWxvY2FsZS1pdGVtJztcbiAgICB0aGlzLmxvY2FsZUJ1dHRvblNlbGVjdG9yID0gb3B0aW9ucy5sb2NhbGVCdXR0b25TZWxlY3RvciB8fCAnLmpzLWxvY2FsZS1idG4nO1xuICAgIHRoaXMubG9jYWxlSW5wdXRTZWxlY3RvciA9IG9wdGlvbnMubG9jYWxlSW5wdXRTZWxlY3RvciB8fCAnLmpzLWxvY2FsZS1pbnB1dCc7XG5cbiAgICAkKCdib2R5Jykub24oJ2NsaWNrJywgdGhpcy5sb2NhbGVJdGVtU2VsZWN0b3IsIHRoaXMudG9nZ2xlTGFuZ3VhZ2UuYmluZCh0aGlzKSk7XG4gICAgRXZlbnRFbWl0dGVyLm9uKCdsYW5ndWFnZVNlbGVjdGVkJywgdGhpcy50b2dnbGVJbnB1dHMuYmluZCh0aGlzKSk7XG4gIH1cblxuICAvKipcbiAgICogRGlzcGF0Y2ggZXZlbnQgb24gbGFuZ3VhZ2Ugc2VsZWN0aW9uIHRvIHVwZGF0ZSBpbnB1dHMgYW5kIG90aGVyIGNvbXBvbmVudHMgd2hpY2ggZGVwZW5kIG9uIHRoZSBsb2NhbGUuXG4gICAqXG4gICAqIEBwYXJhbSBldmVudFxuICAgKi9cbiAgdG9nZ2xlTGFuZ3VhZ2UoZXZlbnQpIHtcbiAgICBjb25zdCBsb2NhbGVJdGVtID0gJChldmVudC50YXJnZXQpO1xuICAgIGNvbnN0IGZvcm0gPSBsb2NhbGVJdGVtLmNsb3Nlc3QoJ2Zvcm0nKTtcbiAgICBFdmVudEVtaXR0ZXIuZW1pdCgnbGFuZ3VhZ2VTZWxlY3RlZCcsIHtcbiAgICAgIHNlbGVjdGVkTG9jYWxlOiBsb2NhbGVJdGVtLmRhdGEoJ2xvY2FsZScpLFxuICAgICAgZm9ybTogZm9ybVxuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIFRvZ2dsZSBhbGwgdHJhbnNsYXRhYmxlIGlucHV0cyBpbiBmb3JtIGluIHdoaWNoIGxvY2FsZSB3YXMgY2hhbmdlZFxuICAgKlxuICAgKiBAcGFyYW0ge0V2ZW50fSBldmVudFxuICAgKi9cbiAgdG9nZ2xlSW5wdXRzKGV2ZW50KSB7XG4gICAgY29uc3QgZm9ybSA9IGV2ZW50LmZvcm07XG4gICAgY29uc3Qgc2VsZWN0ZWRMb2NhbGUgPSBldmVudC5zZWxlY3RlZExvY2FsZTtcbiAgICBjb25zdCBsb2NhbGVCdXR0b24gPSBmb3JtLmZpbmQodGhpcy5sb2NhbGVCdXR0b25TZWxlY3Rvcik7XG4gICAgY29uc3QgY2hhbmdlTGFuZ3VhZ2VVcmwgPSBsb2NhbGVCdXR0b24uZGF0YSgnY2hhbmdlLWxhbmd1YWdlLXVybCcpO1xuXG4gICAgbG9jYWxlQnV0dG9uLnRleHQoc2VsZWN0ZWRMb2NhbGUpO1xuICAgIGZvcm0uZmluZCh0aGlzLmxvY2FsZUlucHV0U2VsZWN0b3IpLmFkZENsYXNzKCdkLW5vbmUnKTtcbiAgICBmb3JtLmZpbmQoYCR7dGhpcy5sb2NhbGVJbnB1dFNlbGVjdG9yfS5qcy1sb2NhbGUtJHtzZWxlY3RlZExvY2FsZX1gKS5yZW1vdmVDbGFzcygnZC1ub25lJyk7XG5cbiAgICBpZiAoY2hhbmdlTGFuZ3VhZ2VVcmwpIHtcbiAgICAgIHRoaXMuX3NhdmVTZWxlY3RlZExhbmd1YWdlKGNoYW5nZUxhbmd1YWdlVXJsLCBzZWxlY3RlZExvY2FsZSk7XG4gICAgfVxuICB9XG5cbiAgLyoqXG4gICAqIFNhdmUgbGFuZ3VhZ2UgY2hvaWNlIGZvciBlbXBsb3llZSBmb3Jtcy5cbiAgICpcbiAgICogQHBhcmFtIHtTdHJpbmd9IGNoYW5nZUxhbmd1YWdlVXJsXG4gICAqIEBwYXJhbSB7U3RyaW5nfSBzZWxlY3RlZExvY2FsZVxuICAgKlxuICAgKiBAcHJpdmF0ZVxuICAgKi9cbiAgX3NhdmVTZWxlY3RlZExhbmd1YWdlKGNoYW5nZUxhbmd1YWdlVXJsLCBzZWxlY3RlZExvY2FsZSkge1xuICAgICQucG9zdCh7XG4gICAgICB1cmw6IGNoYW5nZUxhbmd1YWdlVXJsLFxuICAgICAgZGF0YToge1xuICAgICAgICBsYW5ndWFnZV9pc29fY29kZTogc2VsZWN0ZWRMb2NhbGVcbiAgICAgIH1cbiAgICB9KTtcbiAgfVxufVxuXG5leHBvcnQgZGVmYXVsdCBUcmFuc2xhdGFibGVJbnB1dDtcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2pzL2NvbXBvbmVudHMvdHJhbnNsYXRhYmxlLWlucHV0LmpzIiwiLyoqXG4gKiBDb3B5cmlnaHQgc2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIFByZXN0YVNob3AgaXMgYW4gSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UubWQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly9kZXZkb2NzLnByZXN0YXNob3AuY29tLyBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9ycyA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgU2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqL1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbi8qKlxuICogQ29uZmlybU1vZGFsIGNvbXBvbmVudFxuICpcbiAqIEBwYXJhbSB7U3RyaW5nfSBpZFxuICogQHBhcmFtIHtTdHJpbmd9IGNvbmZpcm1UaXRsZVxuICogQHBhcmFtIHtTdHJpbmd9IGNvbmZpcm1NZXNzYWdlXG4gKiBAcGFyYW0ge1N0cmluZ30gY2xvc2VCdXR0b25MYWJlbFxuICogQHBhcmFtIHtTdHJpbmd9IGNvbmZpcm1CdXR0b25MYWJlbFxuICogQHBhcmFtIHtTdHJpbmd9IGNvbmZpcm1CdXR0b25DbGFzc1xuICogQHBhcmFtIHtCb29sZWFufSBjbG9zYWJsZVxuICogQHBhcmFtIHtGdW5jdGlvbn0gY29uZmlybUNhbGxiYWNrXG4gKlxuICovXG5leHBvcnQgZGVmYXVsdCBmdW5jdGlvbiBDb25maXJtTW9kYWwocGFyYW1zLCBjb25maXJtQ2FsbGJhY2spIHtcbiAgLy8gQ29uc3RydWN0IHRoZSBtb2RhbFxuICBjb25zdCB7aWQsIGNsb3NhYmxlfSA9IHBhcmFtcztcbiAgdGhpcy5tb2RhbCA9IE1vZGFsKHBhcmFtcyk7XG5cbiAgLy8galF1ZXJ5IG1vZGFsIG9iamVjdFxuICB0aGlzLiRtb2RhbCA9ICQodGhpcy5tb2RhbC5jb250YWluZXIpO1xuXG4gIHRoaXMuc2hvdyA9ICgpID0+IHtcbiAgICB0aGlzLiRtb2RhbC5tb2RhbCgpO1xuICB9O1xuXG4gIHRoaXMubW9kYWwuY29uZmlybUJ1dHRvbi5hZGRFdmVudExpc3RlbmVyKCdjbGljaycsIGNvbmZpcm1DYWxsYmFjayk7XG5cbiAgdGhpcy4kbW9kYWwubW9kYWwoe1xuICAgIGJhY2tkcm9wOiAoY2xvc2FibGUgPyB0cnVlIDogJ3N0YXRpYycpLFxuICAgIGtleWJvYXJkOiBjbG9zYWJsZSAhPT0gdW5kZWZpbmVkID8gY2xvc2FibGUgOiB0cnVlLFxuICAgIGNsb3NhYmxlOiBjbG9zYWJsZSAhPT0gdW5kZWZpbmVkID8gY2xvc2FibGUgOiB0cnVlLFxuICAgIHNob3c6IGZhbHNlLFxuICB9KTtcblxuICB0aGlzLiRtb2RhbC5vbignaGlkZGVuLmJzLm1vZGFsJywgKCkgPT4ge1xuICAgIGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3IoYCMke2lkfWApLnJlbW92ZSgpO1xuICB9KTtcblxuICBkb2N1bWVudC5ib2R5LmFwcGVuZENoaWxkKHRoaXMubW9kYWwuY29udGFpbmVyKTtcbn1cblxuLyoqXG4gKiBNb2RhbCBjb21wb25lbnQgdG8gaW1wcm92ZSBsaXNpYmlsaXR5IGJ5IGNvbnN0cnVjdGluZyB0aGUgbW9kYWwgb3V0c2lkZSB0aGUgbWFpbiBmdW5jdGlvblxuICpcbiAqIEBwYXJhbSB7T2JqZWN0fSBwYXJhbXNcbiAqXG4gKi9cbmZ1bmN0aW9uIE1vZGFsKFxuICB7XG4gICAgaWQgPSAnY29uZmlybV9tb2RhbCcsXG4gICAgY29uZmlybVRpdGxlLFxuICAgIGNvbmZpcm1NZXNzYWdlID0gJycsXG4gICAgY2xvc2VCdXR0b25MYWJlbCA9ICdDbG9zZScsXG4gICAgY29uZmlybUJ1dHRvbkxhYmVsID0gJ0FjY2VwdCcsXG4gICAgY29uZmlybUJ1dHRvbkNsYXNzID0gJ2J0bi1wcmltYXJ5JyxcbiAgfSkge1xuICBjb25zdCBtb2RhbCA9IHt9O1xuXG4gIC8vIE1haW4gbW9kYWwgZWxlbWVudFxuICBtb2RhbC5jb250YWluZXIgPSBkb2N1bWVudC5jcmVhdGVFbGVtZW50KCdkaXYnKTtcbiAgbW9kYWwuY29udGFpbmVyLmNsYXNzTGlzdC5hZGQoJ21vZGFsJywgJ2ZhZGUnKTtcbiAgbW9kYWwuY29udGFpbmVyLmlkID0gaWQ7XG5cbiAgLy8gTW9kYWwgZGlhbG9nIGVsZW1lbnRcbiAgbW9kYWwuZGlhbG9nID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgnZGl2Jyk7XG4gIG1vZGFsLmRpYWxvZy5jbGFzc0xpc3QuYWRkKCdtb2RhbC1kaWFsb2cnKTtcblxuICAvLyBNb2RhbCBjb250ZW50IGVsZW1lbnRcbiAgbW9kYWwuY29udGVudCA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoJ2RpdicpO1xuICBtb2RhbC5jb250ZW50LmNsYXNzTGlzdC5hZGQoJ21vZGFsLWNvbnRlbnQnKTtcblxuICAvLyBNb2RhbCBoZWFkZXIgZWxlbWVudFxuICBtb2RhbC5oZWFkZXIgPSBkb2N1bWVudC5jcmVhdGVFbGVtZW50KCdkaXYnKTtcbiAgbW9kYWwuaGVhZGVyLmNsYXNzTGlzdC5hZGQoJ21vZGFsLWhlYWRlcicpO1xuXG4gIC8vIE1vZGFsIHRpdGxlIGVsZW1lbnRcbiAgaWYgKGNvbmZpcm1UaXRsZSkge1xuICAgIG1vZGFsLnRpdGxlID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgnaDQnKTtcbiAgICBtb2RhbC50aXRsZS5jbGFzc0xpc3QuYWRkKCdtb2RhbC10aXRsZScpO1xuICAgIG1vZGFsLnRpdGxlLmlubmVySFRNTCA9IGNvbmZpcm1UaXRsZTtcbiAgfVxuXG4gIC8vIE1vZGFsIGNsb3NlIGJ1dHRvbiBpY29uXG4gIG1vZGFsLmNsb3NlSWNvbiA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoJ2J1dHRvbicpO1xuICBtb2RhbC5jbG9zZUljb24uY2xhc3NMaXN0LmFkZCgnY2xvc2UnKTtcbiAgbW9kYWwuY2xvc2VJY29uLnNldEF0dHJpYnV0ZSgndHlwZScsICdidXR0b24nKTtcbiAgbW9kYWwuY2xvc2VJY29uLmRhdGFzZXQuZGlzbWlzcyA9ICdtb2RhbCc7XG4gIG1vZGFsLmNsb3NlSWNvbi5pbm5lckhUTUwgPSAnw5cnO1xuXG4gIC8vIE1vZGFsIGJvZHkgZWxlbWVudFxuICBtb2RhbC5ib2R5ID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgnZGl2Jyk7XG4gIG1vZGFsLmJvZHkuY2xhc3NMaXN0LmFkZCgnbW9kYWwtYm9keScsICd0ZXh0LWxlZnQnLCAnZm9udC13ZWlnaHQtbm9ybWFsJyk7XG5cbiAgLy8gTW9kYWwgbWVzc2FnZSBlbGVtZW50XG4gIG1vZGFsLm1lc3NhZ2UgPSBkb2N1bWVudC5jcmVhdGVFbGVtZW50KCdwJyk7XG4gIG1vZGFsLm1lc3NhZ2UuY2xhc3NMaXN0LmFkZCgnY29uZmlybS1tZXNzYWdlJyk7XG4gIG1vZGFsLm1lc3NhZ2UuaW5uZXJIVE1MID0gY29uZmlybU1lc3NhZ2U7XG5cbiAgLy8gTW9kYWwgZm9vdGVyIGVsZW1lbnRcbiAgbW9kYWwuZm9vdGVyID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgnZGl2Jyk7XG4gIG1vZGFsLmZvb3Rlci5jbGFzc0xpc3QuYWRkKCdtb2RhbC1mb290ZXInKTtcblxuICAvLyBNb2RhbCBjbG9zZSBidXR0b24gZWxlbWVudFxuICBtb2RhbC5jbG9zZUJ1dHRvbiA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoJ2J1dHRvbicpO1xuICBtb2RhbC5jbG9zZUJ1dHRvbi5zZXRBdHRyaWJ1dGUoJ3R5cGUnLCAnYnV0dG9uJyk7XG4gIG1vZGFsLmNsb3NlQnV0dG9uLmNsYXNzTGlzdC5hZGQoJ2J0bicsICdidG4tb3V0bGluZS1zZWNvbmRhcnknLCAnYnRuLWxnJyk7XG4gIG1vZGFsLmNsb3NlQnV0dG9uLmRhdGFzZXQuZGlzbWlzcyA9ICdtb2RhbCc7XG4gIG1vZGFsLmNsb3NlQnV0dG9uLmlubmVySFRNTCA9IGNsb3NlQnV0dG9uTGFiZWw7XG5cbiAgLy8gTW9kYWwgY2xvc2UgYnV0dG9uIGVsZW1lbnRcbiAgbW9kYWwuY29uZmlybUJ1dHRvbiA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoJ2J1dHRvbicpO1xuICBtb2RhbC5jb25maXJtQnV0dG9uLnNldEF0dHJpYnV0ZSgndHlwZScsICdidXR0b24nKTtcbiAgbW9kYWwuY29uZmlybUJ1dHRvbi5jbGFzc0xpc3QuYWRkKCdidG4nLCBjb25maXJtQnV0dG9uQ2xhc3MsICdidG4tbGcnLCAnYnRuLWNvbmZpcm0tc3VibWl0Jyk7XG4gIG1vZGFsLmNvbmZpcm1CdXR0b24uZGF0YXNldC5kaXNtaXNzID0gJ21vZGFsJztcbiAgbW9kYWwuY29uZmlybUJ1dHRvbi5pbm5lckhUTUwgPSBjb25maXJtQnV0dG9uTGFiZWw7XG5cbiAgLy8gQ29uc3RydWN0aW5nIHRoZSBtb2RhbFxuICBpZiAoY29uZmlybVRpdGxlKSB7XG4gICAgbW9kYWwuaGVhZGVyLmFwcGVuZChtb2RhbC50aXRsZSwgbW9kYWwuY2xvc2VJY29uKTtcbiAgfSBlbHNlIHtcbiAgICBtb2RhbC5oZWFkZXIuYXBwZW5kQ2hpbGQobW9kYWwuY2xvc2VJY29uKTtcbiAgfVxuXG4gIG1vZGFsLmJvZHkuYXBwZW5kQ2hpbGQobW9kYWwubWVzc2FnZSk7XG4gIG1vZGFsLmZvb3Rlci5hcHBlbmQobW9kYWwuY2xvc2VCdXR0b24sIG1vZGFsLmNvbmZpcm1CdXR0b24pO1xuICBtb2RhbC5jb250ZW50LmFwcGVuZChtb2RhbC5oZWFkZXIsIG1vZGFsLmJvZHksIG1vZGFsLmZvb3Rlcik7XG4gIG1vZGFsLmRpYWxvZy5hcHBlbmRDaGlsZChtb2RhbC5jb250ZW50KTtcbiAgbW9kYWwuY29udGFpbmVyLmFwcGVuZENoaWxkKG1vZGFsLmRpYWxvZyk7XG5cbiAgcmV0dXJuIG1vZGFsO1xufVxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvY29tcG9uZW50cy9tb2RhbC5qcyIsIi8qKlxuICogQ29weXJpZ2h0IHNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBQcmVzdGFTaG9wIGlzIGFuIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLm1kLlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vZGV2ZG9jcy5wcmVzdGFzaG9wLmNvbS8gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnMgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IFNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKi9cblxuaW1wb3J0IEdyaWQgZnJvbSAnQGNvbXBvbmVudHMvZ3JpZC9ncmlkJztcbmltcG9ydCBTb3J0aW5nRXh0ZW5zaW9uIGZyb20gJ0Bjb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL3NvcnRpbmctZXh0ZW5zaW9uJztcbmltcG9ydCBTdWJtaXRSb3dBY3Rpb25FeHRlbnNpb24gZnJvbSAnQGNvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vYWN0aW9uL3Jvdy9zdWJtaXQtcm93LWFjdGlvbi1leHRlbnNpb24nO1xuaW1wb3J0IEZpbHRlcnNSZXNldEV4dGVuc2lvbiBmcm9tICdAY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9maWx0ZXJzLXJlc2V0LWV4dGVuc2lvbic7XG5pbXBvcnQgUmVsb2FkTGlzdEFjdGlvbkV4dGVuc2lvbiBmcm9tICdAY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9yZWxvYWQtbGlzdC1leHRlbnNpb24nO1xuaW1wb3J0IEV4cG9ydFRvU3FsTWFuYWdlckV4dGVuc2lvbiBmcm9tICdAY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9leHBvcnQtdG8tc3FsLW1hbmFnZXItZXh0ZW5zaW9uJztcbmltcG9ydCBMaW5rUm93QWN0aW9uRXh0ZW5zaW9uIGZyb20gJ0Bjb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2xpbmstcm93LWFjdGlvbi1leHRlbnNpb24nO1xuaW1wb3J0IFN1Ym1pdEJ1bGtFeHRlbnNpb24gZnJvbSAnQGNvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vc3VibWl0LWJ1bGstYWN0aW9uLWV4dGVuc2lvbic7XG5pbXBvcnQgQnVsa0FjdGlvbkNoZWNrYm94RXh0ZW5zaW9uIGZyb20gJ0Bjb21wb25lbnRzL2dyaWQvZXh0ZW5zaW9uL2J1bGstYWN0aW9uLWNoZWNrYm94LWV4dGVuc2lvbic7XG5pbXBvcnQgQ29sdW1uVG9nZ2xpbmdFeHRlbnNpb24gZnJvbSAnQGNvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vY29sdW1uLXRvZ2dsaW5nLWV4dGVuc2lvbic7XG5pbXBvcnQgUG9zaXRpb25FeHRlbnNpb24gZnJvbSAnQGNvbXBvbmVudHMvZ3JpZC9leHRlbnNpb24vcG9zaXRpb24tZXh0ZW5zaW9uJztcbmltcG9ydCBDaG9pY2VUcmVlIGZyb20gJ0Bjb21wb25lbnRzL2Zvcm0vY2hvaWNlLXRyZWUnO1xuaW1wb3J0IFRyYW5zbGF0YWJsZUlucHV0IGZyb20gJ0Bjb21wb25lbnRzL3RyYW5zbGF0YWJsZS1pbnB1dCc7XG5pbXBvcnQgdGV4dFRvTGlua1Jld3JpdGVDb3BpZXIgZnJvbSAnQGNvbXBvbmVudHMvdGV4dC10by1saW5rLXJld3JpdGUtY29waWVyJztcbmltcG9ydCBGaWx0ZXJzU3VibWl0QnV0dG9uRW5hYmxlckV4dGVuc2lvblxuICBmcm9tICdAY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9maWx0ZXJzLXN1Ym1pdC1idXR0b24tZW5hYmxlci1leHRlbnNpb24nO1xuaW1wb3J0IFRhZ2dhYmxlRmllbGQgZnJvbSAnQGNvbXBvbmVudHMvdGFnZ2FibGUtZmllbGQnO1xuaW1wb3J0IFNob3djYXNlQ2FyZCBmcm9tICdAY29tcG9uZW50cy9zaG93Y2FzZS1jYXJkL3Nob3djYXNlLWNhcmQnO1xuaW1wb3J0IFNob3djYXNlQ2FyZENsb3NlRXh0ZW5zaW9uIGZyb20gJ0Bjb21wb25lbnRzL3Nob3djYXNlLWNhcmQvZXh0ZW5zaW9uL3Nob3djYXNlLWNhcmQtY2xvc2UtZXh0ZW5zaW9uJztcblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4kKCgpID0+IHtcbiAgY29uc3QgY21zQ2F0ZWdvcnkgPSBuZXcgR3JpZCgnY21zX3BhZ2VfY2F0ZWdvcnknKTtcblxuICBjbXNDYXRlZ29yeS5hZGRFeHRlbnNpb24obmV3IFJlbG9hZExpc3RBY3Rpb25FeHRlbnNpb24oKSk7XG4gIGNtc0NhdGVnb3J5LmFkZEV4dGVuc2lvbihuZXcgRXhwb3J0VG9TcWxNYW5hZ2VyRXh0ZW5zaW9uKCkpO1xuICBjbXNDYXRlZ29yeS5hZGRFeHRlbnNpb24obmV3IEZpbHRlcnNSZXNldEV4dGVuc2lvbigpKTtcbiAgY21zQ2F0ZWdvcnkuYWRkRXh0ZW5zaW9uKG5ldyBTb3J0aW5nRXh0ZW5zaW9uKCkpO1xuICBjbXNDYXRlZ29yeS5hZGRFeHRlbnNpb24obmV3IExpbmtSb3dBY3Rpb25FeHRlbnNpb24oKSk7XG4gIGNtc0NhdGVnb3J5LmFkZEV4dGVuc2lvbihuZXcgU3VibWl0QnVsa0V4dGVuc2lvbigpKTtcbiAgY21zQ2F0ZWdvcnkuYWRkRXh0ZW5zaW9uKG5ldyBCdWxrQWN0aW9uQ2hlY2tib3hFeHRlbnNpb24oKSk7XG4gIGNtc0NhdGVnb3J5LmFkZEV4dGVuc2lvbihuZXcgU3VibWl0Um93QWN0aW9uRXh0ZW5zaW9uKCkpO1xuICBjbXNDYXRlZ29yeS5hZGRFeHRlbnNpb24obmV3IENvbHVtblRvZ2dsaW5nRXh0ZW5zaW9uKCkpO1xuICBjbXNDYXRlZ29yeS5hZGRFeHRlbnNpb24obmV3IFBvc2l0aW9uRXh0ZW5zaW9uKCkpO1xuICBjbXNDYXRlZ29yeS5hZGRFeHRlbnNpb24obmV3IEZpbHRlcnNTdWJtaXRCdXR0b25FbmFibGVyRXh0ZW5zaW9uKCkpO1xuXG4gIGNvbnN0IHRyYW5zbGF0b3JJbnB1dCA9IG5ldyBUcmFuc2xhdGFibGVJbnB1dCgpO1xuXG4gIHRleHRUb0xpbmtSZXdyaXRlQ29waWVyKHtcbiAgICBzb3VyY2VFbGVtZW50U2VsZWN0b3I6ICdpbnB1dFtuYW1lXj1cImNtc19wYWdlX2NhdGVnb3J5W25hbWVdXCJdJyxcbiAgICBkZXN0aW5hdGlvbkVsZW1lbnRTZWxlY3RvcjogYCR7dHJhbnNsYXRvcklucHV0LmxvY2FsZUlucHV0U2VsZWN0b3J9Om5vdCguZC1ub25lKSBpbnB1dFtuYW1lXj1cImNtc19wYWdlX2NhdGVnb3J5W2ZyaWVuZGx5X3VybF1cIl1gLFxuICB9KTtcblxuICBuZXcgQ2hvaWNlVHJlZSgnI2Ntc19wYWdlX2NhdGVnb3J5X3BhcmVudF9jYXRlZ29yeScpO1xuXG4gIGNvbnN0IHNob3BDaG9pY2VUcmVlID0gbmV3IENob2ljZVRyZWUoJyNjbXNfcGFnZV9jYXRlZ29yeV9zaG9wX2Fzc29jaWF0aW9uJyk7XG4gIHNob3BDaG9pY2VUcmVlLmVuYWJsZUF1dG9DaGVja0NoaWxkcmVuKCk7XG5cblxuICBuZXcgVGFnZ2FibGVGaWVsZCh7XG4gICAgdG9rZW5GaWVsZFNlbGVjdG9yOiAnaW5wdXRbbmFtZV49XCJjbXNfcGFnZV9jYXRlZ29yeVttZXRhX2tleXdvcmRzXVwiXScsXG4gICAgb3B0aW9uczoge1xuICAgICAgY3JlYXRlVG9rZW5zT25CbHVyOiB0cnVlLFxuICAgIH0sXG4gIH0pO1xuXG4gIGNvbnN0IGNtc0dyaWQgPSBuZXcgR3JpZCgnY21zX3BhZ2UnKTtcbiAgY21zR3JpZC5hZGRFeHRlbnNpb24obmV3IFJlbG9hZExpc3RBY3Rpb25FeHRlbnNpb24oKSk7XG4gIGNtc0dyaWQuYWRkRXh0ZW5zaW9uKG5ldyBFeHBvcnRUb1NxbE1hbmFnZXJFeHRlbnNpb24oKSk7XG4gIGNtc0dyaWQuYWRkRXh0ZW5zaW9uKG5ldyBGaWx0ZXJzUmVzZXRFeHRlbnNpb24oKSk7XG4gIGNtc0dyaWQuYWRkRXh0ZW5zaW9uKG5ldyBTb3J0aW5nRXh0ZW5zaW9uKCkpO1xuICBjbXNHcmlkLmFkZEV4dGVuc2lvbihuZXcgQ29sdW1uVG9nZ2xpbmdFeHRlbnNpb24oKSk7XG4gIGNtc0dyaWQuYWRkRXh0ZW5zaW9uKG5ldyBCdWxrQWN0aW9uQ2hlY2tib3hFeHRlbnNpb24oKSk7XG4gIGNtc0dyaWQuYWRkRXh0ZW5zaW9uKG5ldyBTdWJtaXRCdWxrRXh0ZW5zaW9uKCkpO1xuICBjbXNHcmlkLmFkZEV4dGVuc2lvbihuZXcgU3VibWl0Um93QWN0aW9uRXh0ZW5zaW9uKCkpO1xuICBjbXNHcmlkLmFkZEV4dGVuc2lvbihuZXcgUG9zaXRpb25FeHRlbnNpb24oKSk7XG4gIGNtc0dyaWQuYWRkRXh0ZW5zaW9uKG5ldyBGaWx0ZXJzU3VibWl0QnV0dG9uRW5hYmxlckV4dGVuc2lvbigpKTtcbiAgY21zR3JpZC5hZGRFeHRlbnNpb24obmV3IExpbmtSb3dBY3Rpb25FeHRlbnNpb24oKSk7XG5cbiAgY29uc3QgaGVscGVyQmxvY2sgPSBuZXcgU2hvd2Nhc2VDYXJkKCdjbXMtcGFnZXMtc2hvd2Nhc2UtY2FyZCcpO1xuICBoZWxwZXJCbG9jay5hZGRFeHRlbnNpb24obmV3IFNob3djYXNlQ2FyZENsb3NlRXh0ZW5zaW9uKCkpO1xufSk7XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9wYWdlcy9jbXMtcGFnZS9pbmRleC5qcyIsIi8vIGh0dHBzOi8vZ2l0aHViLmNvbS96bG9pcm9jay9jb3JlLWpzL2lzc3Vlcy84NiNpc3N1ZWNvbW1lbnQtMTE1NzU5MDI4XG52YXIgZ2xvYmFsID0gbW9kdWxlLmV4cG9ydHMgPSB0eXBlb2Ygd2luZG93ICE9ICd1bmRlZmluZWQnICYmIHdpbmRvdy5NYXRoID09IE1hdGhcbiAgPyB3aW5kb3cgOiB0eXBlb2Ygc2VsZiAhPSAndW5kZWZpbmVkJyAmJiBzZWxmLk1hdGggPT0gTWF0aCA/IHNlbGYgOiBGdW5jdGlvbigncmV0dXJuIHRoaXMnKSgpO1xuaWYodHlwZW9mIF9fZyA9PSAnbnVtYmVyJylfX2cgPSBnbG9iYWw7IC8vIGVzbGludC1kaXNhYmxlLWxpbmUgbm8tdW5kZWZcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2dsb2JhbC5qc1xuLy8gbW9kdWxlIGlkID0gNVxuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAyIDMgNCA1IDYgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTcgMTggMTkgMjAgMjEgMjIgMjMgMjQgMjUgMjYgMjcgMjggMjkgMzAgMzEgMzIgMzMgMzQgMzUgMzYgMzcgMzggMzkgNDAgNDEgNDIgNDMgNDQgNDUgNDYgNDcgNDggNDkgNTAgNTEgNTIgNTMgNTQgNTUgNTYgNTcgNTgiLCIvLyBDb3B5cmlnaHQgSm95ZW50LCBJbmMuIGFuZCBvdGhlciBOb2RlIGNvbnRyaWJ1dG9ycy5cbi8vXG4vLyBQZXJtaXNzaW9uIGlzIGhlcmVieSBncmFudGVkLCBmcmVlIG9mIGNoYXJnZSwgdG8gYW55IHBlcnNvbiBvYnRhaW5pbmcgYVxuLy8gY29weSBvZiB0aGlzIHNvZnR3YXJlIGFuZCBhc3NvY2lhdGVkIGRvY3VtZW50YXRpb24gZmlsZXMgKHRoZVxuLy8gXCJTb2Z0d2FyZVwiKSwgdG8gZGVhbCBpbiB0aGUgU29mdHdhcmUgd2l0aG91dCByZXN0cmljdGlvbiwgaW5jbHVkaW5nXG4vLyB3aXRob3V0IGxpbWl0YXRpb24gdGhlIHJpZ2h0cyB0byB1c2UsIGNvcHksIG1vZGlmeSwgbWVyZ2UsIHB1Ymxpc2gsXG4vLyBkaXN0cmlidXRlLCBzdWJsaWNlbnNlLCBhbmQvb3Igc2VsbCBjb3BpZXMgb2YgdGhlIFNvZnR3YXJlLCBhbmQgdG8gcGVybWl0XG4vLyBwZXJzb25zIHRvIHdob20gdGhlIFNvZnR3YXJlIGlzIGZ1cm5pc2hlZCB0byBkbyBzbywgc3ViamVjdCB0byB0aGVcbi8vIGZvbGxvd2luZyBjb25kaXRpb25zOlxuLy9cbi8vIFRoZSBhYm92ZSBjb3B5cmlnaHQgbm90aWNlIGFuZCB0aGlzIHBlcm1pc3Npb24gbm90aWNlIHNoYWxsIGJlIGluY2x1ZGVkXG4vLyBpbiBhbGwgY29waWVzIG9yIHN1YnN0YW50aWFsIHBvcnRpb25zIG9mIHRoZSBTb2Z0d2FyZS5cbi8vXG4vLyBUSEUgU09GVFdBUkUgSVMgUFJPVklERUQgXCJBUyBJU1wiLCBXSVRIT1VUIFdBUlJBTlRZIE9GIEFOWSBLSU5ELCBFWFBSRVNTXG4vLyBPUiBJTVBMSUVELCBJTkNMVURJTkcgQlVUIE5PVCBMSU1JVEVEIFRPIFRIRSBXQVJSQU5USUVTIE9GXG4vLyBNRVJDSEFOVEFCSUxJVFksIEZJVE5FU1MgRk9SIEEgUEFSVElDVUxBUiBQVVJQT1NFIEFORCBOT05JTkZSSU5HRU1FTlQuIElOXG4vLyBOTyBFVkVOVCBTSEFMTCBUSEUgQVVUSE9SUyBPUiBDT1BZUklHSFQgSE9MREVSUyBCRSBMSUFCTEUgRk9SIEFOWSBDTEFJTSxcbi8vIERBTUFHRVMgT1IgT1RIRVIgTElBQklMSVRZLCBXSEVUSEVSIElOIEFOIEFDVElPTiBPRiBDT05UUkFDVCwgVE9SVCBPUlxuLy8gT1RIRVJXSVNFLCBBUklTSU5HIEZST00sIE9VVCBPRiBPUiBJTiBDT05ORUNUSU9OIFdJVEggVEhFIFNPRlRXQVJFIE9SIFRIRVxuLy8gVVNFIE9SIE9USEVSIERFQUxJTkdTIElOIFRIRSBTT0ZUV0FSRS5cblxuJ3VzZSBzdHJpY3QnO1xuXG52YXIgUiA9IHR5cGVvZiBSZWZsZWN0ID09PSAnb2JqZWN0JyA/IFJlZmxlY3QgOiBudWxsXG52YXIgUmVmbGVjdEFwcGx5ID0gUiAmJiB0eXBlb2YgUi5hcHBseSA9PT0gJ2Z1bmN0aW9uJ1xuICA/IFIuYXBwbHlcbiAgOiBmdW5jdGlvbiBSZWZsZWN0QXBwbHkodGFyZ2V0LCByZWNlaXZlciwgYXJncykge1xuICAgIHJldHVybiBGdW5jdGlvbi5wcm90b3R5cGUuYXBwbHkuY2FsbCh0YXJnZXQsIHJlY2VpdmVyLCBhcmdzKTtcbiAgfVxuXG52YXIgUmVmbGVjdE93bktleXNcbmlmIChSICYmIHR5cGVvZiBSLm93bktleXMgPT09ICdmdW5jdGlvbicpIHtcbiAgUmVmbGVjdE93bktleXMgPSBSLm93bktleXNcbn0gZWxzZSBpZiAoT2JqZWN0LmdldE93blByb3BlcnR5U3ltYm9scykge1xuICBSZWZsZWN0T3duS2V5cyA9IGZ1bmN0aW9uIFJlZmxlY3RPd25LZXlzKHRhcmdldCkge1xuICAgIHJldHVybiBPYmplY3QuZ2V0T3duUHJvcGVydHlOYW1lcyh0YXJnZXQpXG4gICAgICAuY29uY2F0KE9iamVjdC5nZXRPd25Qcm9wZXJ0eVN5bWJvbHModGFyZ2V0KSk7XG4gIH07XG59IGVsc2Uge1xuICBSZWZsZWN0T3duS2V5cyA9IGZ1bmN0aW9uIFJlZmxlY3RPd25LZXlzKHRhcmdldCkge1xuICAgIHJldHVybiBPYmplY3QuZ2V0T3duUHJvcGVydHlOYW1lcyh0YXJnZXQpO1xuICB9O1xufVxuXG5mdW5jdGlvbiBQcm9jZXNzRW1pdFdhcm5pbmcod2FybmluZykge1xuICBpZiAoY29uc29sZSAmJiBjb25zb2xlLndhcm4pIGNvbnNvbGUud2Fybih3YXJuaW5nKTtcbn1cblxudmFyIE51bWJlcklzTmFOID0gTnVtYmVyLmlzTmFOIHx8IGZ1bmN0aW9uIE51bWJlcklzTmFOKHZhbHVlKSB7XG4gIHJldHVybiB2YWx1ZSAhPT0gdmFsdWU7XG59XG5cbmZ1bmN0aW9uIEV2ZW50RW1pdHRlcigpIHtcbiAgRXZlbnRFbWl0dGVyLmluaXQuY2FsbCh0aGlzKTtcbn1cbm1vZHVsZS5leHBvcnRzID0gRXZlbnRFbWl0dGVyO1xuXG4vLyBCYWNrd2FyZHMtY29tcGF0IHdpdGggbm9kZSAwLjEwLnhcbkV2ZW50RW1pdHRlci5FdmVudEVtaXR0ZXIgPSBFdmVudEVtaXR0ZXI7XG5cbkV2ZW50RW1pdHRlci5wcm90b3R5cGUuX2V2ZW50cyA9IHVuZGVmaW5lZDtcbkV2ZW50RW1pdHRlci5wcm90b3R5cGUuX2V2ZW50c0NvdW50ID0gMDtcbkV2ZW50RW1pdHRlci5wcm90b3R5cGUuX21heExpc3RlbmVycyA9IHVuZGVmaW5lZDtcblxuLy8gQnkgZGVmYXVsdCBFdmVudEVtaXR0ZXJzIHdpbGwgcHJpbnQgYSB3YXJuaW5nIGlmIG1vcmUgdGhhbiAxMCBsaXN0ZW5lcnMgYXJlXG4vLyBhZGRlZCB0byBpdC4gVGhpcyBpcyBhIHVzZWZ1bCBkZWZhdWx0IHdoaWNoIGhlbHBzIGZpbmRpbmcgbWVtb3J5IGxlYWtzLlxudmFyIGRlZmF1bHRNYXhMaXN0ZW5lcnMgPSAxMDtcblxuT2JqZWN0LmRlZmluZVByb3BlcnR5KEV2ZW50RW1pdHRlciwgJ2RlZmF1bHRNYXhMaXN0ZW5lcnMnLCB7XG4gIGVudW1lcmFibGU6IHRydWUsXG4gIGdldDogZnVuY3Rpb24oKSB7XG4gICAgcmV0dXJuIGRlZmF1bHRNYXhMaXN0ZW5lcnM7XG4gIH0sXG4gIHNldDogZnVuY3Rpb24oYXJnKSB7XG4gICAgaWYgKHR5cGVvZiBhcmcgIT09ICdudW1iZXInIHx8IGFyZyA8IDAgfHwgTnVtYmVySXNOYU4oYXJnKSkge1xuICAgICAgdGhyb3cgbmV3IFJhbmdlRXJyb3IoJ1RoZSB2YWx1ZSBvZiBcImRlZmF1bHRNYXhMaXN0ZW5lcnNcIiBpcyBvdXQgb2YgcmFuZ2UuIEl0IG11c3QgYmUgYSBub24tbmVnYXRpdmUgbnVtYmVyLiBSZWNlaXZlZCAnICsgYXJnICsgJy4nKTtcbiAgICB9XG4gICAgZGVmYXVsdE1heExpc3RlbmVycyA9IGFyZztcbiAgfVxufSk7XG5cbkV2ZW50RW1pdHRlci5pbml0ID0gZnVuY3Rpb24oKSB7XG5cbiAgaWYgKHRoaXMuX2V2ZW50cyA9PT0gdW5kZWZpbmVkIHx8XG4gICAgICB0aGlzLl9ldmVudHMgPT09IE9iamVjdC5nZXRQcm90b3R5cGVPZih0aGlzKS5fZXZlbnRzKSB7XG4gICAgdGhpcy5fZXZlbnRzID0gT2JqZWN0LmNyZWF0ZShudWxsKTtcbiAgICB0aGlzLl9ldmVudHNDb3VudCA9IDA7XG4gIH1cblxuICB0aGlzLl9tYXhMaXN0ZW5lcnMgPSB0aGlzLl9tYXhMaXN0ZW5lcnMgfHwgdW5kZWZpbmVkO1xufTtcblxuLy8gT2J2aW91c2x5IG5vdCBhbGwgRW1pdHRlcnMgc2hvdWxkIGJlIGxpbWl0ZWQgdG8gMTAuIFRoaXMgZnVuY3Rpb24gYWxsb3dzXG4vLyB0aGF0IHRvIGJlIGluY3JlYXNlZC4gU2V0IHRvIHplcm8gZm9yIHVubGltaXRlZC5cbkV2ZW50RW1pdHRlci5wcm90b3R5cGUuc2V0TWF4TGlzdGVuZXJzID0gZnVuY3Rpb24gc2V0TWF4TGlzdGVuZXJzKG4pIHtcbiAgaWYgKHR5cGVvZiBuICE9PSAnbnVtYmVyJyB8fCBuIDwgMCB8fCBOdW1iZXJJc05hTihuKSkge1xuICAgIHRocm93IG5ldyBSYW5nZUVycm9yKCdUaGUgdmFsdWUgb2YgXCJuXCIgaXMgb3V0IG9mIHJhbmdlLiBJdCBtdXN0IGJlIGEgbm9uLW5lZ2F0aXZlIG51bWJlci4gUmVjZWl2ZWQgJyArIG4gKyAnLicpO1xuICB9XG4gIHRoaXMuX21heExpc3RlbmVycyA9IG47XG4gIHJldHVybiB0aGlzO1xufTtcblxuZnVuY3Rpb24gJGdldE1heExpc3RlbmVycyh0aGF0KSB7XG4gIGlmICh0aGF0Ll9tYXhMaXN0ZW5lcnMgPT09IHVuZGVmaW5lZClcbiAgICByZXR1cm4gRXZlbnRFbWl0dGVyLmRlZmF1bHRNYXhMaXN0ZW5lcnM7XG4gIHJldHVybiB0aGF0Ll9tYXhMaXN0ZW5lcnM7XG59XG5cbkV2ZW50RW1pdHRlci5wcm90b3R5cGUuZ2V0TWF4TGlzdGVuZXJzID0gZnVuY3Rpb24gZ2V0TWF4TGlzdGVuZXJzKCkge1xuICByZXR1cm4gJGdldE1heExpc3RlbmVycyh0aGlzKTtcbn07XG5cbkV2ZW50RW1pdHRlci5wcm90b3R5cGUuZW1pdCA9IGZ1bmN0aW9uIGVtaXQodHlwZSkge1xuICB2YXIgYXJncyA9IFtdO1xuICBmb3IgKHZhciBpID0gMTsgaSA8IGFyZ3VtZW50cy5sZW5ndGg7IGkrKykgYXJncy5wdXNoKGFyZ3VtZW50c1tpXSk7XG4gIHZhciBkb0Vycm9yID0gKHR5cGUgPT09ICdlcnJvcicpO1xuXG4gIHZhciBldmVudHMgPSB0aGlzLl9ldmVudHM7XG4gIGlmIChldmVudHMgIT09IHVuZGVmaW5lZClcbiAgICBkb0Vycm9yID0gKGRvRXJyb3IgJiYgZXZlbnRzLmVycm9yID09PSB1bmRlZmluZWQpO1xuICBlbHNlIGlmICghZG9FcnJvcilcbiAgICByZXR1cm4gZmFsc2U7XG5cbiAgLy8gSWYgdGhlcmUgaXMgbm8gJ2Vycm9yJyBldmVudCBsaXN0ZW5lciB0aGVuIHRocm93LlxuICBpZiAoZG9FcnJvcikge1xuICAgIHZhciBlcjtcbiAgICBpZiAoYXJncy5sZW5ndGggPiAwKVxuICAgICAgZXIgPSBhcmdzWzBdO1xuICAgIGlmIChlciBpbnN0YW5jZW9mIEVycm9yKSB7XG4gICAgICAvLyBOb3RlOiBUaGUgY29tbWVudHMgb24gdGhlIGB0aHJvd2AgbGluZXMgYXJlIGludGVudGlvbmFsLCB0aGV5IHNob3dcbiAgICAgIC8vIHVwIGluIE5vZGUncyBvdXRwdXQgaWYgdGhpcyByZXN1bHRzIGluIGFuIHVuaGFuZGxlZCBleGNlcHRpb24uXG4gICAgICB0aHJvdyBlcjsgLy8gVW5oYW5kbGVkICdlcnJvcicgZXZlbnRcbiAgICB9XG4gICAgLy8gQXQgbGVhc3QgZ2l2ZSBzb21lIGtpbmQgb2YgY29udGV4dCB0byB0aGUgdXNlclxuICAgIHZhciBlcnIgPSBuZXcgRXJyb3IoJ1VuaGFuZGxlZCBlcnJvci4nICsgKGVyID8gJyAoJyArIGVyLm1lc3NhZ2UgKyAnKScgOiAnJykpO1xuICAgIGVyci5jb250ZXh0ID0gZXI7XG4gICAgdGhyb3cgZXJyOyAvLyBVbmhhbmRsZWQgJ2Vycm9yJyBldmVudFxuICB9XG5cbiAgdmFyIGhhbmRsZXIgPSBldmVudHNbdHlwZV07XG5cbiAgaWYgKGhhbmRsZXIgPT09IHVuZGVmaW5lZClcbiAgICByZXR1cm4gZmFsc2U7XG5cbiAgaWYgKHR5cGVvZiBoYW5kbGVyID09PSAnZnVuY3Rpb24nKSB7XG4gICAgUmVmbGVjdEFwcGx5KGhhbmRsZXIsIHRoaXMsIGFyZ3MpO1xuICB9IGVsc2Uge1xuICAgIHZhciBsZW4gPSBoYW5kbGVyLmxlbmd0aDtcbiAgICB2YXIgbGlzdGVuZXJzID0gYXJyYXlDbG9uZShoYW5kbGVyLCBsZW4pO1xuICAgIGZvciAodmFyIGkgPSAwOyBpIDwgbGVuOyArK2kpXG4gICAgICBSZWZsZWN0QXBwbHkobGlzdGVuZXJzW2ldLCB0aGlzLCBhcmdzKTtcbiAgfVxuXG4gIHJldHVybiB0cnVlO1xufTtcblxuZnVuY3Rpb24gX2FkZExpc3RlbmVyKHRhcmdldCwgdHlwZSwgbGlzdGVuZXIsIHByZXBlbmQpIHtcbiAgdmFyIG07XG4gIHZhciBldmVudHM7XG4gIHZhciBleGlzdGluZztcblxuICBpZiAodHlwZW9mIGxpc3RlbmVyICE9PSAnZnVuY3Rpb24nKSB7XG4gICAgdGhyb3cgbmV3IFR5cGVFcnJvcignVGhlIFwibGlzdGVuZXJcIiBhcmd1bWVudCBtdXN0IGJlIG9mIHR5cGUgRnVuY3Rpb24uIFJlY2VpdmVkIHR5cGUgJyArIHR5cGVvZiBsaXN0ZW5lcik7XG4gIH1cblxuICBldmVudHMgPSB0YXJnZXQuX2V2ZW50cztcbiAgaWYgKGV2ZW50cyA9PT0gdW5kZWZpbmVkKSB7XG4gICAgZXZlbnRzID0gdGFyZ2V0Ll9ldmVudHMgPSBPYmplY3QuY3JlYXRlKG51bGwpO1xuICAgIHRhcmdldC5fZXZlbnRzQ291bnQgPSAwO1xuICB9IGVsc2Uge1xuICAgIC8vIFRvIGF2b2lkIHJlY3Vyc2lvbiBpbiB0aGUgY2FzZSB0aGF0IHR5cGUgPT09IFwibmV3TGlzdGVuZXJcIiEgQmVmb3JlXG4gICAgLy8gYWRkaW5nIGl0IHRvIHRoZSBsaXN0ZW5lcnMsIGZpcnN0IGVtaXQgXCJuZXdMaXN0ZW5lclwiLlxuICAgIGlmIChldmVudHMubmV3TGlzdGVuZXIgIT09IHVuZGVmaW5lZCkge1xuICAgICAgdGFyZ2V0LmVtaXQoJ25ld0xpc3RlbmVyJywgdHlwZSxcbiAgICAgICAgICAgICAgICAgIGxpc3RlbmVyLmxpc3RlbmVyID8gbGlzdGVuZXIubGlzdGVuZXIgOiBsaXN0ZW5lcik7XG5cbiAgICAgIC8vIFJlLWFzc2lnbiBgZXZlbnRzYCBiZWNhdXNlIGEgbmV3TGlzdGVuZXIgaGFuZGxlciBjb3VsZCBoYXZlIGNhdXNlZCB0aGVcbiAgICAgIC8vIHRoaXMuX2V2ZW50cyB0byBiZSBhc3NpZ25lZCB0byBhIG5ldyBvYmplY3RcbiAgICAgIGV2ZW50cyA9IHRhcmdldC5fZXZlbnRzO1xuICAgIH1cbiAgICBleGlzdGluZyA9IGV2ZW50c1t0eXBlXTtcbiAgfVxuXG4gIGlmIChleGlzdGluZyA9PT0gdW5kZWZpbmVkKSB7XG4gICAgLy8gT3B0aW1pemUgdGhlIGNhc2Ugb2Ygb25lIGxpc3RlbmVyLiBEb24ndCBuZWVkIHRoZSBleHRyYSBhcnJheSBvYmplY3QuXG4gICAgZXhpc3RpbmcgPSBldmVudHNbdHlwZV0gPSBsaXN0ZW5lcjtcbiAgICArK3RhcmdldC5fZXZlbnRzQ291bnQ7XG4gIH0gZWxzZSB7XG4gICAgaWYgKHR5cGVvZiBleGlzdGluZyA9PT0gJ2Z1bmN0aW9uJykge1xuICAgICAgLy8gQWRkaW5nIHRoZSBzZWNvbmQgZWxlbWVudCwgbmVlZCB0byBjaGFuZ2UgdG8gYXJyYXkuXG4gICAgICBleGlzdGluZyA9IGV2ZW50c1t0eXBlXSA9XG4gICAgICAgIHByZXBlbmQgPyBbbGlzdGVuZXIsIGV4aXN0aW5nXSA6IFtleGlzdGluZywgbGlzdGVuZXJdO1xuICAgICAgLy8gSWYgd2UndmUgYWxyZWFkeSBnb3QgYW4gYXJyYXksIGp1c3QgYXBwZW5kLlxuICAgIH0gZWxzZSBpZiAocHJlcGVuZCkge1xuICAgICAgZXhpc3RpbmcudW5zaGlmdChsaXN0ZW5lcik7XG4gICAgfSBlbHNlIHtcbiAgICAgIGV4aXN0aW5nLnB1c2gobGlzdGVuZXIpO1xuICAgIH1cblxuICAgIC8vIENoZWNrIGZvciBsaXN0ZW5lciBsZWFrXG4gICAgbSA9ICRnZXRNYXhMaXN0ZW5lcnModGFyZ2V0KTtcbiAgICBpZiAobSA+IDAgJiYgZXhpc3RpbmcubGVuZ3RoID4gbSAmJiAhZXhpc3Rpbmcud2FybmVkKSB7XG4gICAgICBleGlzdGluZy53YXJuZWQgPSB0cnVlO1xuICAgICAgLy8gTm8gZXJyb3IgY29kZSBmb3IgdGhpcyBzaW5jZSBpdCBpcyBhIFdhcm5pbmdcbiAgICAgIC8vIGVzbGludC1kaXNhYmxlLW5leHQtbGluZSBuby1yZXN0cmljdGVkLXN5bnRheFxuICAgICAgdmFyIHcgPSBuZXcgRXJyb3IoJ1Bvc3NpYmxlIEV2ZW50RW1pdHRlciBtZW1vcnkgbGVhayBkZXRlY3RlZC4gJyArXG4gICAgICAgICAgICAgICAgICAgICAgICAgIGV4aXN0aW5nLmxlbmd0aCArICcgJyArIFN0cmluZyh0eXBlKSArICcgbGlzdGVuZXJzICcgK1xuICAgICAgICAgICAgICAgICAgICAgICAgICAnYWRkZWQuIFVzZSBlbWl0dGVyLnNldE1heExpc3RlbmVycygpIHRvICcgK1xuICAgICAgICAgICAgICAgICAgICAgICAgICAnaW5jcmVhc2UgbGltaXQnKTtcbiAgICAgIHcubmFtZSA9ICdNYXhMaXN0ZW5lcnNFeGNlZWRlZFdhcm5pbmcnO1xuICAgICAgdy5lbWl0dGVyID0gdGFyZ2V0O1xuICAgICAgdy50eXBlID0gdHlwZTtcbiAgICAgIHcuY291bnQgPSBleGlzdGluZy5sZW5ndGg7XG4gICAgICBQcm9jZXNzRW1pdFdhcm5pbmcodyk7XG4gICAgfVxuICB9XG5cbiAgcmV0dXJuIHRhcmdldDtcbn1cblxuRXZlbnRFbWl0dGVyLnByb3RvdHlwZS5hZGRMaXN0ZW5lciA9IGZ1bmN0aW9uIGFkZExpc3RlbmVyKHR5cGUsIGxpc3RlbmVyKSB7XG4gIHJldHVybiBfYWRkTGlzdGVuZXIodGhpcywgdHlwZSwgbGlzdGVuZXIsIGZhbHNlKTtcbn07XG5cbkV2ZW50RW1pdHRlci5wcm90b3R5cGUub24gPSBFdmVudEVtaXR0ZXIucHJvdG90eXBlLmFkZExpc3RlbmVyO1xuXG5FdmVudEVtaXR0ZXIucHJvdG90eXBlLnByZXBlbmRMaXN0ZW5lciA9XG4gICAgZnVuY3Rpb24gcHJlcGVuZExpc3RlbmVyKHR5cGUsIGxpc3RlbmVyKSB7XG4gICAgICByZXR1cm4gX2FkZExpc3RlbmVyKHRoaXMsIHR5cGUsIGxpc3RlbmVyLCB0cnVlKTtcbiAgICB9O1xuXG5mdW5jdGlvbiBvbmNlV3JhcHBlcigpIHtcbiAgdmFyIGFyZ3MgPSBbXTtcbiAgZm9yICh2YXIgaSA9IDA7IGkgPCBhcmd1bWVudHMubGVuZ3RoOyBpKyspIGFyZ3MucHVzaChhcmd1bWVudHNbaV0pO1xuICBpZiAoIXRoaXMuZmlyZWQpIHtcbiAgICB0aGlzLnRhcmdldC5yZW1vdmVMaXN0ZW5lcih0aGlzLnR5cGUsIHRoaXMud3JhcEZuKTtcbiAgICB0aGlzLmZpcmVkID0gdHJ1ZTtcbiAgICBSZWZsZWN0QXBwbHkodGhpcy5saXN0ZW5lciwgdGhpcy50YXJnZXQsIGFyZ3MpO1xuICB9XG59XG5cbmZ1bmN0aW9uIF9vbmNlV3JhcCh0YXJnZXQsIHR5cGUsIGxpc3RlbmVyKSB7XG4gIHZhciBzdGF0ZSA9IHsgZmlyZWQ6IGZhbHNlLCB3cmFwRm46IHVuZGVmaW5lZCwgdGFyZ2V0OiB0YXJnZXQsIHR5cGU6IHR5cGUsIGxpc3RlbmVyOiBsaXN0ZW5lciB9O1xuICB2YXIgd3JhcHBlZCA9IG9uY2VXcmFwcGVyLmJpbmQoc3RhdGUpO1xuICB3cmFwcGVkLmxpc3RlbmVyID0gbGlzdGVuZXI7XG4gIHN0YXRlLndyYXBGbiA9IHdyYXBwZWQ7XG4gIHJldHVybiB3cmFwcGVkO1xufVxuXG5FdmVudEVtaXR0ZXIucHJvdG90eXBlLm9uY2UgPSBmdW5jdGlvbiBvbmNlKHR5cGUsIGxpc3RlbmVyKSB7XG4gIGlmICh0eXBlb2YgbGlzdGVuZXIgIT09ICdmdW5jdGlvbicpIHtcbiAgICB0aHJvdyBuZXcgVHlwZUVycm9yKCdUaGUgXCJsaXN0ZW5lclwiIGFyZ3VtZW50IG11c3QgYmUgb2YgdHlwZSBGdW5jdGlvbi4gUmVjZWl2ZWQgdHlwZSAnICsgdHlwZW9mIGxpc3RlbmVyKTtcbiAgfVxuICB0aGlzLm9uKHR5cGUsIF9vbmNlV3JhcCh0aGlzLCB0eXBlLCBsaXN0ZW5lcikpO1xuICByZXR1cm4gdGhpcztcbn07XG5cbkV2ZW50RW1pdHRlci5wcm90b3R5cGUucHJlcGVuZE9uY2VMaXN0ZW5lciA9XG4gICAgZnVuY3Rpb24gcHJlcGVuZE9uY2VMaXN0ZW5lcih0eXBlLCBsaXN0ZW5lcikge1xuICAgICAgaWYgKHR5cGVvZiBsaXN0ZW5lciAhPT0gJ2Z1bmN0aW9uJykge1xuICAgICAgICB0aHJvdyBuZXcgVHlwZUVycm9yKCdUaGUgXCJsaXN0ZW5lclwiIGFyZ3VtZW50IG11c3QgYmUgb2YgdHlwZSBGdW5jdGlvbi4gUmVjZWl2ZWQgdHlwZSAnICsgdHlwZW9mIGxpc3RlbmVyKTtcbiAgICAgIH1cbiAgICAgIHRoaXMucHJlcGVuZExpc3RlbmVyKHR5cGUsIF9vbmNlV3JhcCh0aGlzLCB0eXBlLCBsaXN0ZW5lcikpO1xuICAgICAgcmV0dXJuIHRoaXM7XG4gICAgfTtcblxuLy8gRW1pdHMgYSAncmVtb3ZlTGlzdGVuZXInIGV2ZW50IGlmIGFuZCBvbmx5IGlmIHRoZSBsaXN0ZW5lciB3YXMgcmVtb3ZlZC5cbkV2ZW50RW1pdHRlci5wcm90b3R5cGUucmVtb3ZlTGlzdGVuZXIgPVxuICAgIGZ1bmN0aW9uIHJlbW92ZUxpc3RlbmVyKHR5cGUsIGxpc3RlbmVyKSB7XG4gICAgICB2YXIgbGlzdCwgZXZlbnRzLCBwb3NpdGlvbiwgaSwgb3JpZ2luYWxMaXN0ZW5lcjtcblxuICAgICAgaWYgKHR5cGVvZiBsaXN0ZW5lciAhPT0gJ2Z1bmN0aW9uJykge1xuICAgICAgICB0aHJvdyBuZXcgVHlwZUVycm9yKCdUaGUgXCJsaXN0ZW5lclwiIGFyZ3VtZW50IG11c3QgYmUgb2YgdHlwZSBGdW5jdGlvbi4gUmVjZWl2ZWQgdHlwZSAnICsgdHlwZW9mIGxpc3RlbmVyKTtcbiAgICAgIH1cblxuICAgICAgZXZlbnRzID0gdGhpcy5fZXZlbnRzO1xuICAgICAgaWYgKGV2ZW50cyA9PT0gdW5kZWZpbmVkKVxuICAgICAgICByZXR1cm4gdGhpcztcblxuICAgICAgbGlzdCA9IGV2ZW50c1t0eXBlXTtcbiAgICAgIGlmIChsaXN0ID09PSB1bmRlZmluZWQpXG4gICAgICAgIHJldHVybiB0aGlzO1xuXG4gICAgICBpZiAobGlzdCA9PT0gbGlzdGVuZXIgfHwgbGlzdC5saXN0ZW5lciA9PT0gbGlzdGVuZXIpIHtcbiAgICAgICAgaWYgKC0tdGhpcy5fZXZlbnRzQ291bnQgPT09IDApXG4gICAgICAgICAgdGhpcy5fZXZlbnRzID0gT2JqZWN0LmNyZWF0ZShudWxsKTtcbiAgICAgICAgZWxzZSB7XG4gICAgICAgICAgZGVsZXRlIGV2ZW50c1t0eXBlXTtcbiAgICAgICAgICBpZiAoZXZlbnRzLnJlbW92ZUxpc3RlbmVyKVxuICAgICAgICAgICAgdGhpcy5lbWl0KCdyZW1vdmVMaXN0ZW5lcicsIHR5cGUsIGxpc3QubGlzdGVuZXIgfHwgbGlzdGVuZXIpO1xuICAgICAgICB9XG4gICAgICB9IGVsc2UgaWYgKHR5cGVvZiBsaXN0ICE9PSAnZnVuY3Rpb24nKSB7XG4gICAgICAgIHBvc2l0aW9uID0gLTE7XG5cbiAgICAgICAgZm9yIChpID0gbGlzdC5sZW5ndGggLSAxOyBpID49IDA7IGktLSkge1xuICAgICAgICAgIGlmIChsaXN0W2ldID09PSBsaXN0ZW5lciB8fCBsaXN0W2ldLmxpc3RlbmVyID09PSBsaXN0ZW5lcikge1xuICAgICAgICAgICAgb3JpZ2luYWxMaXN0ZW5lciA9IGxpc3RbaV0ubGlzdGVuZXI7XG4gICAgICAgICAgICBwb3NpdGlvbiA9IGk7XG4gICAgICAgICAgICBicmVhaztcbiAgICAgICAgICB9XG4gICAgICAgIH1cblxuICAgICAgICBpZiAocG9zaXRpb24gPCAwKVxuICAgICAgICAgIHJldHVybiB0aGlzO1xuXG4gICAgICAgIGlmIChwb3NpdGlvbiA9PT0gMClcbiAgICAgICAgICBsaXN0LnNoaWZ0KCk7XG4gICAgICAgIGVsc2Uge1xuICAgICAgICAgIHNwbGljZU9uZShsaXN0LCBwb3NpdGlvbik7XG4gICAgICAgIH1cblxuICAgICAgICBpZiAobGlzdC5sZW5ndGggPT09IDEpXG4gICAgICAgICAgZXZlbnRzW3R5cGVdID0gbGlzdFswXTtcblxuICAgICAgICBpZiAoZXZlbnRzLnJlbW92ZUxpc3RlbmVyICE9PSB1bmRlZmluZWQpXG4gICAgICAgICAgdGhpcy5lbWl0KCdyZW1vdmVMaXN0ZW5lcicsIHR5cGUsIG9yaWdpbmFsTGlzdGVuZXIgfHwgbGlzdGVuZXIpO1xuICAgICAgfVxuXG4gICAgICByZXR1cm4gdGhpcztcbiAgICB9O1xuXG5FdmVudEVtaXR0ZXIucHJvdG90eXBlLm9mZiA9IEV2ZW50RW1pdHRlci5wcm90b3R5cGUucmVtb3ZlTGlzdGVuZXI7XG5cbkV2ZW50RW1pdHRlci5wcm90b3R5cGUucmVtb3ZlQWxsTGlzdGVuZXJzID1cbiAgICBmdW5jdGlvbiByZW1vdmVBbGxMaXN0ZW5lcnModHlwZSkge1xuICAgICAgdmFyIGxpc3RlbmVycywgZXZlbnRzLCBpO1xuXG4gICAgICBldmVudHMgPSB0aGlzLl9ldmVudHM7XG4gICAgICBpZiAoZXZlbnRzID09PSB1bmRlZmluZWQpXG4gICAgICAgIHJldHVybiB0aGlzO1xuXG4gICAgICAvLyBub3QgbGlzdGVuaW5nIGZvciByZW1vdmVMaXN0ZW5lciwgbm8gbmVlZCB0byBlbWl0XG4gICAgICBpZiAoZXZlbnRzLnJlbW92ZUxpc3RlbmVyID09PSB1bmRlZmluZWQpIHtcbiAgICAgICAgaWYgKGFyZ3VtZW50cy5sZW5ndGggPT09IDApIHtcbiAgICAgICAgICB0aGlzLl9ldmVudHMgPSBPYmplY3QuY3JlYXRlKG51bGwpO1xuICAgICAgICAgIHRoaXMuX2V2ZW50c0NvdW50ID0gMDtcbiAgICAgICAgfSBlbHNlIGlmIChldmVudHNbdHlwZV0gIT09IHVuZGVmaW5lZCkge1xuICAgICAgICAgIGlmICgtLXRoaXMuX2V2ZW50c0NvdW50ID09PSAwKVxuICAgICAgICAgICAgdGhpcy5fZXZlbnRzID0gT2JqZWN0LmNyZWF0ZShudWxsKTtcbiAgICAgICAgICBlbHNlXG4gICAgICAgICAgICBkZWxldGUgZXZlbnRzW3R5cGVdO1xuICAgICAgICB9XG4gICAgICAgIHJldHVybiB0aGlzO1xuICAgICAgfVxuXG4gICAgICAvLyBlbWl0IHJlbW92ZUxpc3RlbmVyIGZvciBhbGwgbGlzdGVuZXJzIG9uIGFsbCBldmVudHNcbiAgICAgIGlmIChhcmd1bWVudHMubGVuZ3RoID09PSAwKSB7XG4gICAgICAgIHZhciBrZXlzID0gT2JqZWN0LmtleXMoZXZlbnRzKTtcbiAgICAgICAgdmFyIGtleTtcbiAgICAgICAgZm9yIChpID0gMDsgaSA8IGtleXMubGVuZ3RoOyArK2kpIHtcbiAgICAgICAgICBrZXkgPSBrZXlzW2ldO1xuICAgICAgICAgIGlmIChrZXkgPT09ICdyZW1vdmVMaXN0ZW5lcicpIGNvbnRpbnVlO1xuICAgICAgICAgIHRoaXMucmVtb3ZlQWxsTGlzdGVuZXJzKGtleSk7XG4gICAgICAgIH1cbiAgICAgICAgdGhpcy5yZW1vdmVBbGxMaXN0ZW5lcnMoJ3JlbW92ZUxpc3RlbmVyJyk7XG4gICAgICAgIHRoaXMuX2V2ZW50cyA9IE9iamVjdC5jcmVhdGUobnVsbCk7XG4gICAgICAgIHRoaXMuX2V2ZW50c0NvdW50ID0gMDtcbiAgICAgICAgcmV0dXJuIHRoaXM7XG4gICAgICB9XG5cbiAgICAgIGxpc3RlbmVycyA9IGV2ZW50c1t0eXBlXTtcblxuICAgICAgaWYgKHR5cGVvZiBsaXN0ZW5lcnMgPT09ICdmdW5jdGlvbicpIHtcbiAgICAgICAgdGhpcy5yZW1vdmVMaXN0ZW5lcih0eXBlLCBsaXN0ZW5lcnMpO1xuICAgICAgfSBlbHNlIGlmIChsaXN0ZW5lcnMgIT09IHVuZGVmaW5lZCkge1xuICAgICAgICAvLyBMSUZPIG9yZGVyXG4gICAgICAgIGZvciAoaSA9IGxpc3RlbmVycy5sZW5ndGggLSAxOyBpID49IDA7IGktLSkge1xuICAgICAgICAgIHRoaXMucmVtb3ZlTGlzdGVuZXIodHlwZSwgbGlzdGVuZXJzW2ldKTtcbiAgICAgICAgfVxuICAgICAgfVxuXG4gICAgICByZXR1cm4gdGhpcztcbiAgICB9O1xuXG5mdW5jdGlvbiBfbGlzdGVuZXJzKHRhcmdldCwgdHlwZSwgdW53cmFwKSB7XG4gIHZhciBldmVudHMgPSB0YXJnZXQuX2V2ZW50cztcblxuICBpZiAoZXZlbnRzID09PSB1bmRlZmluZWQpXG4gICAgcmV0dXJuIFtdO1xuXG4gIHZhciBldmxpc3RlbmVyID0gZXZlbnRzW3R5cGVdO1xuICBpZiAoZXZsaXN0ZW5lciA9PT0gdW5kZWZpbmVkKVxuICAgIHJldHVybiBbXTtcblxuICBpZiAodHlwZW9mIGV2bGlzdGVuZXIgPT09ICdmdW5jdGlvbicpXG4gICAgcmV0dXJuIHVud3JhcCA/IFtldmxpc3RlbmVyLmxpc3RlbmVyIHx8IGV2bGlzdGVuZXJdIDogW2V2bGlzdGVuZXJdO1xuXG4gIHJldHVybiB1bndyYXAgP1xuICAgIHVud3JhcExpc3RlbmVycyhldmxpc3RlbmVyKSA6IGFycmF5Q2xvbmUoZXZsaXN0ZW5lciwgZXZsaXN0ZW5lci5sZW5ndGgpO1xufVxuXG5FdmVudEVtaXR0ZXIucHJvdG90eXBlLmxpc3RlbmVycyA9IGZ1bmN0aW9uIGxpc3RlbmVycyh0eXBlKSB7XG4gIHJldHVybiBfbGlzdGVuZXJzKHRoaXMsIHR5cGUsIHRydWUpO1xufTtcblxuRXZlbnRFbWl0dGVyLnByb3RvdHlwZS5yYXdMaXN0ZW5lcnMgPSBmdW5jdGlvbiByYXdMaXN0ZW5lcnModHlwZSkge1xuICByZXR1cm4gX2xpc3RlbmVycyh0aGlzLCB0eXBlLCBmYWxzZSk7XG59O1xuXG5FdmVudEVtaXR0ZXIubGlzdGVuZXJDb3VudCA9IGZ1bmN0aW9uKGVtaXR0ZXIsIHR5cGUpIHtcbiAgaWYgKHR5cGVvZiBlbWl0dGVyLmxpc3RlbmVyQ291bnQgPT09ICdmdW5jdGlvbicpIHtcbiAgICByZXR1cm4gZW1pdHRlci5saXN0ZW5lckNvdW50KHR5cGUpO1xuICB9IGVsc2Uge1xuICAgIHJldHVybiBsaXN0ZW5lckNvdW50LmNhbGwoZW1pdHRlciwgdHlwZSk7XG4gIH1cbn07XG5cbkV2ZW50RW1pdHRlci5wcm90b3R5cGUubGlzdGVuZXJDb3VudCA9IGxpc3RlbmVyQ291bnQ7XG5mdW5jdGlvbiBsaXN0ZW5lckNvdW50KHR5cGUpIHtcbiAgdmFyIGV2ZW50cyA9IHRoaXMuX2V2ZW50cztcblxuICBpZiAoZXZlbnRzICE9PSB1bmRlZmluZWQpIHtcbiAgICB2YXIgZXZsaXN0ZW5lciA9IGV2ZW50c1t0eXBlXTtcblxuICAgIGlmICh0eXBlb2YgZXZsaXN0ZW5lciA9PT0gJ2Z1bmN0aW9uJykge1xuICAgICAgcmV0dXJuIDE7XG4gICAgfSBlbHNlIGlmIChldmxpc3RlbmVyICE9PSB1bmRlZmluZWQpIHtcbiAgICAgIHJldHVybiBldmxpc3RlbmVyLmxlbmd0aDtcbiAgICB9XG4gIH1cblxuICByZXR1cm4gMDtcbn1cblxuRXZlbnRFbWl0dGVyLnByb3RvdHlwZS5ldmVudE5hbWVzID0gZnVuY3Rpb24gZXZlbnROYW1lcygpIHtcbiAgcmV0dXJuIHRoaXMuX2V2ZW50c0NvdW50ID4gMCA/IFJlZmxlY3RPd25LZXlzKHRoaXMuX2V2ZW50cykgOiBbXTtcbn07XG5cbmZ1bmN0aW9uIGFycmF5Q2xvbmUoYXJyLCBuKSB7XG4gIHZhciBjb3B5ID0gbmV3IEFycmF5KG4pO1xuICBmb3IgKHZhciBpID0gMDsgaSA8IG47ICsraSlcbiAgICBjb3B5W2ldID0gYXJyW2ldO1xuICByZXR1cm4gY29weTtcbn1cblxuZnVuY3Rpb24gc3BsaWNlT25lKGxpc3QsIGluZGV4KSB7XG4gIGZvciAoOyBpbmRleCArIDEgPCBsaXN0Lmxlbmd0aDsgaW5kZXgrKylcbiAgICBsaXN0W2luZGV4XSA9IGxpc3RbaW5kZXggKyAxXTtcbiAgbGlzdC5wb3AoKTtcbn1cblxuZnVuY3Rpb24gdW53cmFwTGlzdGVuZXJzKGFycikge1xuICB2YXIgcmV0ID0gbmV3IEFycmF5KGFyci5sZW5ndGgpO1xuICBmb3IgKHZhciBpID0gMDsgaSA8IHJldC5sZW5ndGg7ICsraSkge1xuICAgIHJldFtpXSA9IGFycltpXS5saXN0ZW5lciB8fCBhcnJbaV07XG4gIH1cbiAgcmV0dXJuIHJldDtcbn1cblxuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9ldmVudHMvZXZlbnRzLmpzXG4vLyBtb2R1bGUgaWQgPSA1NlxuLy8gbW9kdWxlIGNodW5rcyA9IDEgMyA3IDEwIDExIDEyIDE1IDE2IDE3IDE4IDIxIDIzIDI0IDI1IDM0IDQxIDQzIDQ2IDQ4IDUwIDUxIiwidmFyIGFuT2JqZWN0ICAgICAgID0gcmVxdWlyZSgnLi9fYW4tb2JqZWN0JylcbiAgLCBJRThfRE9NX0RFRklORSA9IHJlcXVpcmUoJy4vX2llOC1kb20tZGVmaW5lJylcbiAgLCB0b1ByaW1pdGl2ZSAgICA9IHJlcXVpcmUoJy4vX3RvLXByaW1pdGl2ZScpXG4gICwgZFAgICAgICAgICAgICAgPSBPYmplY3QuZGVmaW5lUHJvcGVydHk7XG5cbmV4cG9ydHMuZiA9IHJlcXVpcmUoJy4vX2Rlc2NyaXB0b3JzJykgPyBPYmplY3QuZGVmaW5lUHJvcGVydHkgOiBmdW5jdGlvbiBkZWZpbmVQcm9wZXJ0eShPLCBQLCBBdHRyaWJ1dGVzKXtcbiAgYW5PYmplY3QoTyk7XG4gIFAgPSB0b1ByaW1pdGl2ZShQLCB0cnVlKTtcbiAgYW5PYmplY3QoQXR0cmlidXRlcyk7XG4gIGlmKElFOF9ET01fREVGSU5FKXRyeSB7XG4gICAgcmV0dXJuIGRQKE8sIFAsIEF0dHJpYnV0ZXMpO1xuICB9IGNhdGNoKGUpeyAvKiBlbXB0eSAqLyB9XG4gIGlmKCdnZXQnIGluIEF0dHJpYnV0ZXMgfHwgJ3NldCcgaW4gQXR0cmlidXRlcyl0aHJvdyBUeXBlRXJyb3IoJ0FjY2Vzc29ycyBub3Qgc3VwcG9ydGVkIScpO1xuICBpZigndmFsdWUnIGluIEF0dHJpYnV0ZXMpT1tQXSA9IEF0dHJpYnV0ZXMudmFsdWU7XG4gIHJldHVybiBPO1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX29iamVjdC1kcC5qc1xuLy8gbW9kdWxlIGlkID0gNlxuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAyIDMgNCA1IDYgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTcgMTggMTkgMjAgMjEgMjIgMjMgMjQgMjUgMjYgMjcgMjggMjkgMzAgMzEgMzIgMzMgMzQgMzUgMzYgMzcgMzggMzkgNDAgNDEgNDIgNDMgNDQgNDUgNDYgNDcgNDggNDkgNTAgNTEgNTIgNTMgNTQgNTUgNTYgNTcgNTgiLCIvKipcbiAqIENvcHlyaWdodCBzaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogUHJlc3RhU2hvcCBpcyBhbiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS5tZC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL2RldmRvY3MucHJlc3Rhc2hvcC5jb20vIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCBTaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICovXG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuLyoqXG4gKiBIYW5kbGVzIFVJIGludGVyYWN0aW9ucyBvZiBjaG9pY2UgdHJlZVxuICovXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBDaG9pY2VUcmVlIHtcbiAgLyoqXG4gICAqIEBwYXJhbSB7U3RyaW5nfSB0cmVlU2VsZWN0b3JcbiAgICovXG4gIGNvbnN0cnVjdG9yKHRyZWVTZWxlY3Rvcikge1xuICAgIHRoaXMuJGNvbnRhaW5lciA9ICQodHJlZVNlbGVjdG9yKTtcblxuICAgIHRoaXMuJGNvbnRhaW5lci5vbignY2xpY2snLCAnLmpzLWlucHV0LXdyYXBwZXInLCAoZXZlbnQpID0+IHtcbiAgICAgIGNvbnN0ICRpbnB1dFdyYXBwZXIgPSAkKGV2ZW50LmN1cnJlbnRUYXJnZXQpO1xuXG4gICAgICB0aGlzLl90b2dnbGVDaGlsZFRyZWUoJGlucHV0V3JhcHBlcik7XG4gICAgfSk7XG5cbiAgICB0aGlzLiRjb250YWluZXIub24oJ2NsaWNrJywgJy5qcy10b2dnbGUtY2hvaWNlLXRyZWUtYWN0aW9uJywgKGV2ZW50KSA9PiB7XG4gICAgICBjb25zdCAkYWN0aW9uID0gJChldmVudC5jdXJyZW50VGFyZ2V0KTtcblxuICAgICAgdGhpcy5fdG9nZ2xlVHJlZSgkYWN0aW9uKTtcbiAgICB9KTtcblxuICAgIHJldHVybiB7XG4gICAgICBlbmFibGVBdXRvQ2hlY2tDaGlsZHJlbjogKCkgPT4gdGhpcy5lbmFibGVBdXRvQ2hlY2tDaGlsZHJlbigpLFxuICAgICAgZW5hYmxlQWxsSW5wdXRzOiAoKSA9PiB0aGlzLmVuYWJsZUFsbElucHV0cygpLFxuICAgICAgZGlzYWJsZUFsbElucHV0czogKCkgPT4gdGhpcy5kaXNhYmxlQWxsSW5wdXRzKCksXG4gICAgfTtcbiAgfVxuXG4gIC8qKlxuICAgKiBFbmFibGUgYXV0b21hdGljIGNoZWNrL3VuY2hlY2sgb2YgY2xpY2tlZCBpdGVtJ3MgY2hpbGRyZW4uXG4gICAqL1xuICBlbmFibGVBdXRvQ2hlY2tDaGlsZHJlbigpIHtcbiAgICB0aGlzLiRjb250YWluZXIub24oJ2NoYW5nZScsICdpbnB1dFt0eXBlPVwiY2hlY2tib3hcIl0nLCAoZXZlbnQpID0+IHtcbiAgICAgIGNvbnN0ICRjbGlja2VkQ2hlY2tib3ggPSAkKGV2ZW50LmN1cnJlbnRUYXJnZXQpO1xuICAgICAgY29uc3QgJGl0ZW1XaXRoQ2hpbGRyZW4gPSAkY2xpY2tlZENoZWNrYm94LmNsb3Nlc3QoJ2xpJyk7XG5cbiAgICAgICRpdGVtV2l0aENoaWxkcmVuXG4gICAgICAgIC5maW5kKCd1bCBpbnB1dFt0eXBlPVwiY2hlY2tib3hcIl0nKVxuICAgICAgICAucHJvcCgnY2hlY2tlZCcsICRjbGlja2VkQ2hlY2tib3guaXMoJzpjaGVja2VkJykpO1xuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIEVuYWJsZSBhbGwgaW5wdXRzIGluIHRoZSBjaG9pY2UgdHJlZS5cbiAgICovXG4gIGVuYWJsZUFsbElucHV0cygpIHtcbiAgICB0aGlzLiRjb250YWluZXIuZmluZCgnaW5wdXQnKS5yZW1vdmVBdHRyKCdkaXNhYmxlZCcpO1xuICB9XG5cbiAgLyoqXG4gICAqIERpc2FibGUgYWxsIGlucHV0cyBpbiB0aGUgY2hvaWNlIHRyZWUuXG4gICAqL1xuICBkaXNhYmxlQWxsSW5wdXRzKCkge1xuICAgIHRoaXMuJGNvbnRhaW5lci5maW5kKCdpbnB1dCcpLmF0dHIoJ2Rpc2FibGVkJywgJ2Rpc2FibGVkJyk7XG4gIH1cblxuICAvKipcbiAgICogQ29sbGFwc2Ugb3IgZXhwYW5kIHN1Yi10cmVlIGZvciBzaW5nbGUgcGFyZW50XG4gICAqXG4gICAqIEBwYXJhbSB7alF1ZXJ5fSAkaW5wdXRXcmFwcGVyXG4gICAqXG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfdG9nZ2xlQ2hpbGRUcmVlKCRpbnB1dFdyYXBwZXIpIHtcbiAgICBjb25zdCAkcGFyZW50V3JhcHBlciA9ICRpbnB1dFdyYXBwZXIuY2xvc2VzdCgnbGknKTtcblxuICAgIGlmICgkcGFyZW50V3JhcHBlci5oYXNDbGFzcygnZXhwYW5kZWQnKSkge1xuICAgICAgJHBhcmVudFdyYXBwZXJcbiAgICAgICAgLnJlbW92ZUNsYXNzKCdleHBhbmRlZCcpXG4gICAgICAgIC5hZGRDbGFzcygnY29sbGFwc2VkJyk7XG5cbiAgICAgIHJldHVybjtcbiAgICB9XG5cbiAgICBpZiAoJHBhcmVudFdyYXBwZXIuaGFzQ2xhc3MoJ2NvbGxhcHNlZCcpKSB7XG4gICAgICAkcGFyZW50V3JhcHBlclxuICAgICAgICAucmVtb3ZlQ2xhc3MoJ2NvbGxhcHNlZCcpXG4gICAgICAgIC5hZGRDbGFzcygnZXhwYW5kZWQnKTtcbiAgICB9XG4gIH1cblxuICAvKipcbiAgICogQ29sbGFwc2Ugb3IgZXhwYW5kIHdob2xlIHRyZWVcbiAgICpcbiAgICogQHBhcmFtIHtqUXVlcnl9ICRhY3Rpb25cbiAgICpcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF90b2dnbGVUcmVlKCRhY3Rpb24pIHtcbiAgICBjb25zdCAkcGFyZW50Q29udGFpbmVyID0gJGFjdGlvbi5jbG9zZXN0KCcuanMtY2hvaWNlLXRyZWUtY29udGFpbmVyJyk7XG4gICAgY29uc3QgYWN0aW9uID0gJGFjdGlvbi5kYXRhKCdhY3Rpb24nKTtcblxuICAgIC8vIHRvZ2dsZSBhY3Rpb24gY29uZmlndXJhdGlvblxuICAgIGNvbnN0IGNvbmZpZyA9IHtcbiAgICAgIGFkZENsYXNzOiB7XG4gICAgICAgIGV4cGFuZDogJ2V4cGFuZGVkJyxcbiAgICAgICAgY29sbGFwc2U6ICdjb2xsYXBzZWQnLFxuICAgICAgfSxcbiAgICAgIHJlbW92ZUNsYXNzOiB7XG4gICAgICAgIGV4cGFuZDogJ2NvbGxhcHNlZCcsXG4gICAgICAgIGNvbGxhcHNlOiAnZXhwYW5kZWQnLFxuICAgICAgfSxcbiAgICAgIG5leHRBY3Rpb246IHtcbiAgICAgICAgZXhwYW5kOiAnY29sbGFwc2UnLFxuICAgICAgICBjb2xsYXBzZTogJ2V4cGFuZCcsXG4gICAgICB9LFxuICAgICAgdGV4dDoge1xuICAgICAgICBleHBhbmQ6ICdjb2xsYXBzZWQtdGV4dCcsXG4gICAgICAgIGNvbGxhcHNlOiAnZXhwYW5kZWQtdGV4dCcsXG4gICAgICB9LFxuICAgICAgaWNvbjoge1xuICAgICAgICBleHBhbmQ6ICdjb2xsYXBzZWQtaWNvbicsXG4gICAgICAgIGNvbGxhcHNlOiAnZXhwYW5kZWQtaWNvbicsXG4gICAgICB9XG4gICAgfTtcblxuICAgICRwYXJlbnRDb250YWluZXIuZmluZCgnbGknKS5lYWNoKChpbmRleCwgaXRlbSkgPT4ge1xuICAgICAgY29uc3QgJGl0ZW0gPSAkKGl0ZW0pO1xuXG4gICAgICBpZiAoJGl0ZW0uaGFzQ2xhc3MoY29uZmlnLnJlbW92ZUNsYXNzW2FjdGlvbl0pKSB7XG4gICAgICAgICAgJGl0ZW0ucmVtb3ZlQ2xhc3MoY29uZmlnLnJlbW92ZUNsYXNzW2FjdGlvbl0pXG4gICAgICAgICAgICAuYWRkQ2xhc3MoY29uZmlnLmFkZENsYXNzW2FjdGlvbl0pO1xuICAgICAgfVxuICAgIH0pO1xuXG4gICAgJGFjdGlvbi5kYXRhKCdhY3Rpb24nLCBjb25maWcubmV4dEFjdGlvblthY3Rpb25dKTtcbiAgICAkYWN0aW9uLmZpbmQoJy5tYXRlcmlhbC1pY29ucycpLnRleHQoJGFjdGlvbi5kYXRhKGNvbmZpZy5pY29uW2FjdGlvbl0pKTtcbiAgICAkYWN0aW9uLmZpbmQoJy5qcy10b2dnbGUtdGV4dCcpLnRleHQoJGFjdGlvbi5kYXRhKGNvbmZpZy50ZXh0W2FjdGlvbl0pKTtcbiAgfVxufVxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvY29tcG9uZW50cy9mb3JtL2Nob2ljZS10cmVlLmpzIiwiLyoqXG4gKiBDb3B5cmlnaHQgc2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIFByZXN0YVNob3AgaXMgYW4gSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UubWQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly9kZXZkb2NzLnByZXN0YXNob3AuY29tLyBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9ycyA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgU2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqL1xuXG5jb25zdCAkID0gZ2xvYmFsLiQ7XG5cbi8qKlxuICogQ2xhc3MgUmVsb2FkTGlzdEV4dGVuc2lvbiBleHRlbmRzIGdyaWQgd2l0aCBcIkNvbHVtbiB0b2dnbGluZ1wiIGZlYXR1cmVcbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgQ29sdW1uVG9nZ2xpbmdFeHRlbnNpb24ge1xuXG4gIC8qKlxuICAgKiBFeHRlbmQgZ3JpZFxuICAgKlxuICAgKiBAcGFyYW0ge0dyaWR9IGdyaWRcbiAgICovXG4gIGV4dGVuZChncmlkKSB7XG4gICAgY29uc3QgJHRhYmxlID0gZ3JpZC5nZXRDb250YWluZXIoKS5maW5kKCd0YWJsZS50YWJsZScpO1xuICAgICR0YWJsZS5maW5kKCcucHMtdG9nZ2xhYmxlLXJvdycpLm9uKCdjbGljaycsIChlKSA9PiB7XG4gICAgICBlLnByZXZlbnREZWZhdWx0KCk7XG4gICAgICB0aGlzLl90b2dnbGVWYWx1ZSgkKGUuZGVsZWdhdGVUYXJnZXQpKTtcbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBAcGFyYW0ge2pRdWVyeX0gcm93XG4gICAqIEBwcml2YXRlXG4gICAqL1xuICBfdG9nZ2xlVmFsdWUocm93KSB7XG4gICAgY29uc3QgdG9nZ2xlVXJsID0gcm93LmRhdGEoJ3RvZ2dsZVVybCcpO1xuXG4gICAgdGhpcy5fc3VibWl0QXNGb3JtKHRvZ2dsZVVybCk7XG4gIH1cblxuICAvKipcbiAgICogU3VibWl0cyByZXF1ZXN0IHVybCBhcyBmb3JtXG4gICAqXG4gICAqIEBwYXJhbSB7c3RyaW5nfSB0b2dnbGVVcmxcbiAgICogQHByaXZhdGVcbiAgICovXG4gIF9zdWJtaXRBc0Zvcm0odG9nZ2xlVXJsKSB7XG4gICAgY29uc3QgJGZvcm0gPSAkKCc8Zm9ybT4nLCB7XG4gICAgICBhY3Rpb246IHRvZ2dsZVVybCxcbiAgICAgIG1ldGhvZDogJ1BPU1QnLFxuICAgIH0pLmFwcGVuZFRvKCdib2R5Jyk7XG5cbiAgICAkZm9ybS5zdWJtaXQoKTtcbiAgfVxufVxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvY29tcG9uZW50cy9ncmlkL2V4dGVuc2lvbi9jb2x1bW4tdG9nZ2xpbmctZXh0ZW5zaW9uLmpzIiwibW9kdWxlLmV4cG9ydHMgPSBmdW5jdGlvbihleGVjKXtcbiAgdHJ5IHtcbiAgICByZXR1cm4gISFleGVjKCk7XG4gIH0gY2F0Y2goZSl7XG4gICAgcmV0dXJuIHRydWU7XG4gIH1cbn07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19mYWlscy5qc1xuLy8gbW9kdWxlIGlkID0gN1xuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAyIDMgNCA1IDYgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTcgMTggMTkgMjAgMjEgMjIgMjMgMjQgMjUgMjYgMjcgMjggMjkgMzAgMzEgMzIgMzMgMzQgMzUgMzYgMzcgMzggMzkgNDAgNDEgNDIgNDMgNDQgNDUgNDYgNDcgNDggNDkgNTAgNTEgNTIgNTMgNTQgNTUgNTYgNTcgNTgiLCJ2YXIgZ2xvYmFsICAgID0gcmVxdWlyZSgnLi9fZ2xvYmFsJylcbiAgLCBjb3JlICAgICAgPSByZXF1aXJlKCcuL19jb3JlJylcbiAgLCBjdHggICAgICAgPSByZXF1aXJlKCcuL19jdHgnKVxuICAsIGhpZGUgICAgICA9IHJlcXVpcmUoJy4vX2hpZGUnKVxuICAsIFBST1RPVFlQRSA9ICdwcm90b3R5cGUnO1xuXG52YXIgJGV4cG9ydCA9IGZ1bmN0aW9uKHR5cGUsIG5hbWUsIHNvdXJjZSl7XG4gIHZhciBJU19GT1JDRUQgPSB0eXBlICYgJGV4cG9ydC5GXG4gICAgLCBJU19HTE9CQUwgPSB0eXBlICYgJGV4cG9ydC5HXG4gICAgLCBJU19TVEFUSUMgPSB0eXBlICYgJGV4cG9ydC5TXG4gICAgLCBJU19QUk9UTyAgPSB0eXBlICYgJGV4cG9ydC5QXG4gICAgLCBJU19CSU5EICAgPSB0eXBlICYgJGV4cG9ydC5CXG4gICAgLCBJU19XUkFQICAgPSB0eXBlICYgJGV4cG9ydC5XXG4gICAgLCBleHBvcnRzICAgPSBJU19HTE9CQUwgPyBjb3JlIDogY29yZVtuYW1lXSB8fCAoY29yZVtuYW1lXSA9IHt9KVxuICAgICwgZXhwUHJvdG8gID0gZXhwb3J0c1tQUk9UT1RZUEVdXG4gICAgLCB0YXJnZXQgICAgPSBJU19HTE9CQUwgPyBnbG9iYWwgOiBJU19TVEFUSUMgPyBnbG9iYWxbbmFtZV0gOiAoZ2xvYmFsW25hbWVdIHx8IHt9KVtQUk9UT1RZUEVdXG4gICAgLCBrZXksIG93biwgb3V0O1xuICBpZihJU19HTE9CQUwpc291cmNlID0gbmFtZTtcbiAgZm9yKGtleSBpbiBzb3VyY2Upe1xuICAgIC8vIGNvbnRhaW5zIGluIG5hdGl2ZVxuICAgIG93biA9ICFJU19GT1JDRUQgJiYgdGFyZ2V0ICYmIHRhcmdldFtrZXldICE9PSB1bmRlZmluZWQ7XG4gICAgaWYob3duICYmIGtleSBpbiBleHBvcnRzKWNvbnRpbnVlO1xuICAgIC8vIGV4cG9ydCBuYXRpdmUgb3IgcGFzc2VkXG4gICAgb3V0ID0gb3duID8gdGFyZ2V0W2tleV0gOiBzb3VyY2Vba2V5XTtcbiAgICAvLyBwcmV2ZW50IGdsb2JhbCBwb2xsdXRpb24gZm9yIG5hbWVzcGFjZXNcbiAgICBleHBvcnRzW2tleV0gPSBJU19HTE9CQUwgJiYgdHlwZW9mIHRhcmdldFtrZXldICE9ICdmdW5jdGlvbicgPyBzb3VyY2Vba2V5XVxuICAgIC8vIGJpbmQgdGltZXJzIHRvIGdsb2JhbCBmb3IgY2FsbCBmcm9tIGV4cG9ydCBjb250ZXh0XG4gICAgOiBJU19CSU5EICYmIG93biA/IGN0eChvdXQsIGdsb2JhbClcbiAgICAvLyB3cmFwIGdsb2JhbCBjb25zdHJ1Y3RvcnMgZm9yIHByZXZlbnQgY2hhbmdlIHRoZW0gaW4gbGlicmFyeVxuICAgIDogSVNfV1JBUCAmJiB0YXJnZXRba2V5XSA9PSBvdXQgPyAoZnVuY3Rpb24oQyl7XG4gICAgICB2YXIgRiA9IGZ1bmN0aW9uKGEsIGIsIGMpe1xuICAgICAgICBpZih0aGlzIGluc3RhbmNlb2YgQyl7XG4gICAgICAgICAgc3dpdGNoKGFyZ3VtZW50cy5sZW5ndGgpe1xuICAgICAgICAgICAgY2FzZSAwOiByZXR1cm4gbmV3IEM7XG4gICAgICAgICAgICBjYXNlIDE6IHJldHVybiBuZXcgQyhhKTtcbiAgICAgICAgICAgIGNhc2UgMjogcmV0dXJuIG5ldyBDKGEsIGIpO1xuICAgICAgICAgIH0gcmV0dXJuIG5ldyBDKGEsIGIsIGMpO1xuICAgICAgICB9IHJldHVybiBDLmFwcGx5KHRoaXMsIGFyZ3VtZW50cyk7XG4gICAgICB9O1xuICAgICAgRltQUk9UT1RZUEVdID0gQ1tQUk9UT1RZUEVdO1xuICAgICAgcmV0dXJuIEY7XG4gICAgLy8gbWFrZSBzdGF0aWMgdmVyc2lvbnMgZm9yIHByb3RvdHlwZSBtZXRob2RzXG4gICAgfSkob3V0KSA6IElTX1BST1RPICYmIHR5cGVvZiBvdXQgPT0gJ2Z1bmN0aW9uJyA/IGN0eChGdW5jdGlvbi5jYWxsLCBvdXQpIDogb3V0O1xuICAgIC8vIGV4cG9ydCBwcm90byBtZXRob2RzIHRvIGNvcmUuJUNPTlNUUlVDVE9SJS5tZXRob2RzLiVOQU1FJVxuICAgIGlmKElTX1BST1RPKXtcbiAgICAgIChleHBvcnRzLnZpcnR1YWwgfHwgKGV4cG9ydHMudmlydHVhbCA9IHt9KSlba2V5XSA9IG91dDtcbiAgICAgIC8vIGV4cG9ydCBwcm90byBtZXRob2RzIHRvIGNvcmUuJUNPTlNUUlVDVE9SJS5wcm90b3R5cGUuJU5BTUUlXG4gICAgICBpZih0eXBlICYgJGV4cG9ydC5SICYmIGV4cFByb3RvICYmICFleHBQcm90b1trZXldKWhpZGUoZXhwUHJvdG8sIGtleSwgb3V0KTtcbiAgICB9XG4gIH1cbn07XG4vLyB0eXBlIGJpdG1hcFxuJGV4cG9ydC5GID0gMTsgICAvLyBmb3JjZWRcbiRleHBvcnQuRyA9IDI7ICAgLy8gZ2xvYmFsXG4kZXhwb3J0LlMgPSA0OyAgIC8vIHN0YXRpY1xuJGV4cG9ydC5QID0gODsgICAvLyBwcm90b1xuJGV4cG9ydC5CID0gMTY7ICAvLyBiaW5kXG4kZXhwb3J0LlcgPSAzMjsgIC8vIHdyYXBcbiRleHBvcnQuVSA9IDY0OyAgLy8gc2FmZVxuJGV4cG9ydC5SID0gMTI4OyAvLyByZWFsIHByb3RvIG1ldGhvZCBmb3IgYGxpYnJhcnlgIFxubW9kdWxlLmV4cG9ydHMgPSAkZXhwb3J0O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fZXhwb3J0LmpzXG4vLyBtb2R1bGUgaWQgPSA4XG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDIgMyA0IDUgNiA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxNyAxOCAxOSAyMCAyMSAyMiAyMyAyNCAyNSAyNiAyNyAyOCAyOSAzMCAzMSAzMiAzMyAzNCAzNSAzNiAzNyAzOCAzOSA0MCA0MSA0MiA0MyA0NCA0NSA0NiA0NyA0OCA0OSA1MCA1MSA1MiA1MyA1NCA1NSA1NiA1NyA1OCIsIi8qKlxuICogQ29weXJpZ2h0IHNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBQcmVzdGFTaG9wIGlzIGFuIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLm1kLlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vZGV2ZG9jcy5wcmVzdGFzaG9wLmNvbS8gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnMgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IFNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKi9cblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4vKipcbiAqIENsYXNzIFNob3djYXNlQ2FyZENsb3NlRXh0ZW5zaW9uIGlzIHJlc3BvbnNpYmxlIGZvciBwcm92aWRpbmcgaGVscGVyIGJsb2NrIGNsb3NpbmcgYmVoYXZpb3JcbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgU2hvd2Nhc2VDYXJkQ2xvc2VFeHRlbnNpb24ge1xuXG4gIC8qKlxuICAgKiBFeHRlbmQgaGVscGVyIGJsb2NrLlxuICAgKlxuICAgKiBAcGFyYW0ge1Nob3djYXNlQ2FyZH0gaGVscGVyQmxvY2tcbiAgICovXG4gIGV4dGVuZChoZWxwZXJCbG9jaykge1xuICAgIGNvbnN0IGNvbnRhaW5lciA9IGhlbHBlckJsb2NrLmdldENvbnRhaW5lcigpO1xuICAgIGNvbnRhaW5lci5vbignY2xpY2snLCAnLmpzLXJlbW92ZS1oZWxwZXItYmxvY2snLCAoZXZ0KSA9PiB7XG4gICAgICBjb250YWluZXIucmVtb3ZlKCk7XG5cbiAgICAgIGNvbnN0ICRidG4gPSAkKGV2dC50YXJnZXQpO1xuICAgICAgY29uc3QgdXJsID0gJGJ0bi5kYXRhKCdjbG9zZVVybCcpO1xuICAgICAgY29uc3QgY2FyZE5hbWUgPSAkYnRuLmRhdGEoJ2NhcmROYW1lJyk7XG5cbiAgICAgIGlmICh1cmwpIHtcbiAgICAgICAgLy8gbm90aWZ5IHRoZSBjYXJkIHdhcyBjbG9zZWRcbiAgICAgICAgJC5wb3N0KFxuICAgICAgICAgIHVybCxcbiAgICAgICAgICB7XG4gICAgICAgICAgICBjbG9zZTogMSxcbiAgICAgICAgICAgIG5hbWU6IGNhcmROYW1lXG4gICAgICAgICAgfVxuICAgICAgICApO1xuICAgICAgfVxuICAgIH0pO1xuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9jb21wb25lbnRzL3Nob3djYXNlLWNhcmQvZXh0ZW5zaW9uL3Nob3djYXNlLWNhcmQtY2xvc2UtZXh0ZW5zaW9uLmpzIiwiLyoqXG4gKiBDb3B5cmlnaHQgc2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIFByZXN0YVNob3AgaXMgYW4gSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UubWQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly9kZXZkb2NzLnByZXN0YXNob3AuY29tLyBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9ycyA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgU2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqL1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbi8qKlxuICogQ2xhc3MgU2hvd2Nhc2VDYXJkIGlzIHJlc3BvbnNpYmxlIGZvciBoYW5kbGluZyBldmVudHMgcmVsYXRlZCB3aXRoIHNob3djYXNlIGNhcmQuXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIFNob3djYXNlQ2FyZCB7XG5cbiAgLyoqXG4gICAqIFNob3djYXNlIGNhcmQgaWQuXG4gICAqXG4gICAqIEBwYXJhbSB7c3RyaW5nfSBpZFxuICAgKi9cbiAgY29uc3RydWN0b3IoaWQpIHtcbiAgICB0aGlzLmlkID0gaWQ7XG4gICAgdGhpcy4kY29udGFpbmVyID0gJCgnIycgKyB0aGlzLmlkKTtcbiAgfVxuXG4gIC8qKlxuICAgKiBHZXQgc2hvd2Nhc2UgY2FyZCBjb250YWluZXIuXG4gICAqXG4gICAqIEByZXR1cm5zIHtqUXVlcnl9XG4gICAqL1xuICBnZXRDb250YWluZXIoKSB7XG4gICAgcmV0dXJuIHRoaXMuJGNvbnRhaW5lcjtcbiAgfVxuXG4gIC8qKlxuICAgKiBFeHRlbmQgc2hvd2Nhc2UgY2FyZCB3aXRoIGV4dGVybmFsIGV4dGVuc2lvbnMuXG4gICAqXG4gICAqIEBwYXJhbSB7b2JqZWN0fSBleHRlbnNpb25cbiAgICovXG4gIGFkZEV4dGVuc2lvbihleHRlbnNpb24pIHtcbiAgICBleHRlbnNpb24uZXh0ZW5kKHRoaXMpO1xuICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9jb21wb25lbnRzL3Nob3djYXNlLWNhcmQvc2hvd2Nhc2UtY2FyZC5qcyIsInZhciBnO1xyXG5cclxuLy8gVGhpcyB3b3JrcyBpbiBub24tc3RyaWN0IG1vZGVcclxuZyA9IChmdW5jdGlvbigpIHtcclxuXHRyZXR1cm4gdGhpcztcclxufSkoKTtcclxuXHJcbnRyeSB7XHJcblx0Ly8gVGhpcyB3b3JrcyBpZiBldmFsIGlzIGFsbG93ZWQgKHNlZSBDU1ApXHJcblx0ZyA9IGcgfHwgRnVuY3Rpb24oXCJyZXR1cm4gdGhpc1wiKSgpIHx8ICgxLGV2YWwpKFwidGhpc1wiKTtcclxufSBjYXRjaChlKSB7XHJcblx0Ly8gVGhpcyB3b3JrcyBpZiB0aGUgd2luZG93IHJlZmVyZW5jZSBpcyBhdmFpbGFibGVcclxuXHRpZih0eXBlb2Ygd2luZG93ID09PSBcIm9iamVjdFwiKVxyXG5cdFx0ZyA9IHdpbmRvdztcclxufVxyXG5cclxuLy8gZyBjYW4gc3RpbGwgYmUgdW5kZWZpbmVkLCBidXQgbm90aGluZyB0byBkbyBhYm91dCBpdC4uLlxyXG4vLyBXZSByZXR1cm4gdW5kZWZpbmVkLCBpbnN0ZWFkIG9mIG5vdGhpbmcgaGVyZSwgc28gaXQnc1xyXG4vLyBlYXNpZXIgdG8gaGFuZGxlIHRoaXMgY2FzZS4gaWYoIWdsb2JhbCkgeyAuLi59XHJcblxyXG5tb2R1bGUuZXhwb3J0cyA9IGc7XHJcblxuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vICh3ZWJwYWNrKS9idWlsZGluL2dsb2JhbC5qc1xuLy8gbW9kdWxlIGlkID0gOVxuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAyIDMgNCA1IDYgNyA5IDEwIDExIDEyIDEzIDE3IDIxIDIyIDIzIDI0IDI1IDI2IDI3IDI4IDI5IDMwIDMxIDMyIDMzIDM0IDM1IDM2IDM3IDM4IDM5IDQwIDQxIDQyIDQzIDQ2IDU0IiwiLyoqXG4gKiBDb3B5cmlnaHQgc2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIFByZXN0YVNob3AgaXMgYW4gSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UubWQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly9kZXZkb2NzLnByZXN0YXNob3AuY29tLyBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9ycyA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgU2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqL1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbi8qKlxuICogY2xhc3MgVGFnZ2FibGVGaWVsZCBpcyByZXNwb25zaWJsZSBmb3IgcHJvdmlkaW5nIGZ1bmN0aW9uYWxpdHkgZnJvbSBib290c3RyYXAtdG9rZW5maWVsZCBwbHVnaW4uXG4gKiBJdCBhbGxvd3MgdG8gaGF2ZSB0YWdnYWJsZSBmaWVsZHMgd2hpY2ggYXJlIHNwbGl0IGluIHNlcGFyYXRlIGJsb2NrcyBvbmNlIHlvdSBjbGljayBlbnRlci4gVmFsdWVzIG9yaWdpbmFsbHkgc2F2ZWRcbiAqIGluIGNvbW1hIHNwbGl0IHN0cmluZ3MuXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIFRhZ2dhYmxlRmllbGQge1xuICAvKipcbiAgICogQHBhcmFtIHtzdHJpbmd9IHRva2VuRmllbGRTZWxlY3RvciAtICBhIHNlbGVjdG9yIHdoaWNoIGlzIHVzZWQgd2l0aGluIGpRdWVyeSBvYmplY3QuXG4gICAqIEBwYXJhbSB7b2JqZWN0fSBvcHRpb25zIC0gZXh0ZW5kcyBiYXNpYyB0b2tlbkZpZWxkIGJlaGF2aW9yIHdpdGggYWRkaXRpb25hbCBvcHRpb25zIHN1Y2ggYXMgbWluTGVuZ3RoLCBkZWxpbWl0ZXIsXG4gICAqIGFsbG93IHRvIGFkZCB0b2tlbiBvbiBmb2N1cyBvdXQgYWN0aW9uLiBTZWUgYm9vdHN0cmFwLXRva2VuZmllbGQgZG9jcyBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAgICovXG4gIGNvbnN0cnVjdG9yKHt0b2tlbkZpZWxkU2VsZWN0b3IsIG9wdGlvbnMgPSB7fX0pIHtcbiAgICAkKHRva2VuRmllbGRTZWxlY3RvcikudG9rZW5maWVsZChvcHRpb25zKTtcbiAgfVxufVxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvY29tcG9uZW50cy90YWdnYWJsZS1maWVsZC5qcyJdLCJzb3VyY2VSb290IjoiIn0=