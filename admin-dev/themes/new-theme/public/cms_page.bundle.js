!function(e){function t(r){if(n[r])return n[r].exports;var i=n[r]={i:r,l:!1,exports:{}};return e[r].call(i.exports,i,i.exports,t),i.l=!0,i.exports}var n={};t.m=e,t.c=n,t.i=function(e){return e},t.d=function(e,n,r){t.o(e,n)||Object.defineProperty(e,n,{configurable:!1,enumerable:!0,get:r})},t.n=function(e){var n=e&&e.__esModule?function(){return e.default}:function(){return e};return t.d(n,"a",n),n},t.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},t.p="",t(t.s=302)}({1:function(e,t){var n;n=function(){return this}();try{n=n||Function("return this")()||(0,eval)("this")}catch(e){"object"==typeof window&&(n=window)}e.exports=n},10:function(e,t,n){"use strict";function r(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}var i=function(){function e(e,t){for(var n=0;n<t.length;n++){var r=t[n];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(e,r.key,r)}}return function(t,n,r){return n&&e(t.prototype,n),r&&e(t,r),t}}(),o=window.$,a=function(){function e(){var t=this;return r(this,e),{extend:function(e){return t.extend(e)}}}return i(e,[{key:"extend",value:function(e){var t=this;e.getContainer().on("click",".js-bulk-action-submit-btn",function(n){t.submit(n,e)})}},{key:"submit",value:function(e,t){var n=o(e.currentTarget),r=n.data("confirm-message");if(!(void 0!==r&&0<r.length)||confirm(r)){var i=o("#"+t.getId()+"_filter_form");i.attr("action",n.data("form-url")),i.attr("method",n.data("form-method")),i.submit()}}}]),e}();t.a=a},11:function(e,t,n){"use strict";function r(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}var i=function(){function e(e,t){for(var n=0;n<t.length;n++){var r=t[n];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(e,r.key,r)}}return function(t,n,r){return n&&e(t.prototype,n),r&&e(t,r),t}}(),o=window.$,a=function(){function e(){r(this,e)}return i(e,[{key:"extend",value:function(e){e.getContainer().on("click",".js-submit-row-action",function(e){e.preventDefault();var t=o(e.currentTarget),n=t.data("confirm-message");if(!n.length||confirm(n)){var r=t.data("method"),i=["GET","POST"].includes(r),a=o("<form>",{action:t.data("url"),method:i?r:"POST"}).appendTo("body");i||a.append(o("<input>",{type:"_hidden",name:"_method",value:r})),a.submit()}})}}]),e}();t.a=a},12:function(e,t,n){"use strict";(function(e){function n(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}var r=function(){function e(e,t){for(var n=0;n<t.length;n++){var r=t[n];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(e,r.key,r)}}return function(t,n,r){return n&&e(t.prototype,n),r&&e(t,r),t}}(),i=e.$,o=function(){function e(t){n(this,e),this.selector=".ps-sortable-column",this.columns=i(t).find(this.selector)}return r(e,[{key:"attach",value:function(){var e=this;this.columns.on("click",function(t){var n=i(t.delegateTarget);e._sortByColumn(n,e._getToggledSortDirection(n))})}},{key:"sortBy",value:function(e,t){var n=this.columns.is('[data-sort-col-name="'+e+'"]');if(!n)throw new Error('Cannot sort by "'+e+'": invalid column');this._sortByColumn(n,t)}},{key:"_sortByColumn",value:function(e,t){window.location=this._getUrl(e.data("sortColName"),"desc"===t?"desc":"asc",e.data("sortPrefix"))}},{key:"_getToggledSortDirection",value:function(e){return"asc"===e.data("sortDirection")?"desc":"asc"}},{key:"_getUrl",value:function(e,t,n){var r=new URL(window.location.href),i=r.searchParams;return n?(i.set(n+"[orderBy]",e),i.set(n+"[sortOrder]",t)):(i.set("orderBy",e),i.set("sortOrder",t)),r.toString()}}]),e}();t.a=o}).call(t,n(1))},13:function(e,t,n){"use strict";(function(e){/**
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
var n=e.$,r=function(e,t){n.post(e).then(function(){return window.location.assign(t)})};t.a=r}).call(t,n(1))},14:function(e,t,n){"use strict";function r(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}var i=n(18),o=function(){function e(e,t){for(var n=0;n<t.length;n++){var r=t[n];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(e,r.key,r)}}return function(t,n,r){return n&&e(t.prototype,n),r&&e(t,r),t}}(),a=window.$,s=function(){function e(t){r(this,e),t=t||{},this.localeItemSelector=t.localeItemSelector||".js-locale-item",this.localeButtonSelector=t.localeButtonSelector||".js-locale-btn",this.localeInputSelector=t.localeInputSelector||".js-locale-input",a("body").on("click",this.localeItemSelector,this.toggleLanguage.bind(this)),i.a.on("languageSelected",this.toggleInputs.bind(this))}return o(e,[{key:"toggleLanguage",value:function(e){var t=a(e.target),n=t.closest("form");i.a.emit("languageSelected",{selectedLocale:t.data("locale"),form:n})}},{key:"toggleInputs",value:function(e){var t=e.form,n=e.selectedLocale,r=t.find(this.localeButtonSelector),i=r.data("change-language-url");r.text(n),t.find(this.localeInputSelector).addClass("d-none"),t.find(this.localeInputSelector+".js-locale-"+n).removeClass("d-none"),i&&this._saveSelectedLanguage(i,n)}},{key:"_saveSelectedLanguage",value:function(e,t){a.post({url:e,data:{language_iso_code:t}})}}]),e}();t.a=s},15:function(e,t,n){"use strict";function r(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}var i=function(){function e(e,t){for(var n=0;n<t.length;n++){var r=t[n];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(e,r.key,r)}}return function(t,n,r){return n&&e(t.prototype,n),r&&e(t,r),t}}(),o=window.$,a=function(){function e(){r(this,e)}return i(e,[{key:"extend",value:function(e){e.getContainer().on("click",".js-link-row-action",function(e){var t=o(e.currentTarget).data("confirm-message");t.length&&!confirm(t)&&e.preventDefault()})}}]),e}();t.a=a},16:function(e,t,n){"use strict";(function(e){function n(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}var r=function(){function e(e,t){for(var n=0;n<t.length;n++){var r=t[n];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(e,r.key,r)}}return function(t,n,r){return n&&e(t.prototype,n),r&&e(t,r),t}}(),i=e.$,o=function(){function e(){n(this,e)}return r(e,[{key:"extend",value:function(e){var t=this;e.getContainer().find("table.table").find(".ps-togglable-row").on("click",function(e){e.preventDefault(),t._toggleValue(i(e.delegateTarget))})}},{key:"_toggleValue",value:function(e){var t=e.data("toggleUrl");this._submitAsForm(t)}},{key:"_submitAsForm",value:function(e){i("<form>",{action:e,method:"POST"}).appendTo("body").submit()}}]),e}();t.a=o}).call(t,n(1))},17:function(e,t,n){"use strict";function r(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}var i=function(){function e(e,t){for(var n=0;n<t.length;n++){var r=t[n];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(e,r.key,r)}}return function(t,n,r){return n&&e(t.prototype,n),r&&e(t,r),t}}(),o=window.$,a=function(){function e(t){var n=this;return r(this,e),this.$container=o(t),this.$container.on("click",".js-input-wrapper",function(e){var t=o(e.currentTarget);n._toggleChildTree(t)}),this.$container.on("click",".js-toggle-choice-tree-action",function(e){var t=o(e.currentTarget);n._toggleTree(t)}),{enableAutoCheckChildren:function(){return n.enableAutoCheckChildren()},enableAllInputs:function(){return n.enableAllInputs()},disableAllInputs:function(){return n.disableAllInputs()}}}return i(e,[{key:"enableAutoCheckChildren",value:function(){this.$container.on("change",'input[type="checkbox"]',function(e){var t=o(e.currentTarget);t.closest("li").find('ul input[type="checkbox"]').prop("checked",t.is(":checked"))})}},{key:"enableAllInputs",value:function(){this.$container.find("input").removeAttr("disabled")}},{key:"disableAllInputs",value:function(){this.$container.find("input").attr("disabled","disabled")}},{key:"_toggleChildTree",value:function(e){var t=e.closest("li");if(t.hasClass("expanded"))return void t.removeClass("expanded").addClass("collapsed");t.hasClass("collapsed")&&t.removeClass("collapsed").addClass("expanded")}},{key:"_toggleTree",value:function(e){var t=e.closest(".js-choice-tree-container"),n=e.data("action"),r={addClass:{expand:"expanded",collapse:"collapsed"},removeClass:{expand:"collapsed",collapse:"expanded"},nextAction:{expand:"collapse",collapse:"expand"},text:{expand:"collapsed-text",collapse:"expanded-text"},icon:{expand:"collapsed-icon",collapse:"expanded-icon"}};t.find("li").each(function(e,t){var i=o(t);i.hasClass(r.removeClass[n])&&i.removeClass(r.removeClass[n]).addClass(r.addClass[n])}),e.data("action",r.nextAction[n]),e.find(".material-icons").text(e.data(r.icon[n])),e.find(".js-toggle-text").text(e.data(r.text[n]))}}]),e}();t.a=a},18:function(e,t,n){"use strict";n.d(t,"a",function(){return o});var r=n(19),i=n.n(r),o=new i.a},19:function(e,t,n){"use strict";function r(e){console&&console.warn&&console.warn(e)}function i(){i.init.call(this)}function o(e){return void 0===e._maxListeners?i.defaultMaxListeners:e._maxListeners}function a(e,t,n,i){var a,s,l;if("function"!=typeof n)throw new TypeError('The "listener" argument must be of type Function. Received type '+typeof n);if(s=e._events,void 0===s?(s=e._events=Object.create(null),e._eventsCount=0):(void 0!==s.newListener&&(e.emit("newListener",t,n.listener?n.listener:n),s=e._events),l=s[t]),void 0===l)l=s[t]=n,++e._eventsCount;else if("function"==typeof l?l=s[t]=i?[n,l]:[l,n]:i?l.unshift(n):l.push(n),(a=o(e))>0&&l.length>a&&!l.warned){l.warned=!0;var u=new Error("Possible EventEmitter memory leak detected. "+l.length+" "+String(t)+" listeners added. Use emitter.setMaxListeners() to increase limit");u.name="MaxListenersExceededWarning",u.emitter=e,u.type=t,u.count=l.length,r(u)}return e}function s(){for(var e=[],t=0;t<arguments.length;t++)e.push(arguments[t]);this.fired||(this.target.removeListener(this.type,this.wrapFn),this.fired=!0,g(this.listener,this.target,e))}function l(e,t,n){var r={fired:!1,wrapFn:void 0,target:e,type:t,listener:n},i=s.bind(r);return i.listener=n,r.wrapFn=i,i}function u(e,t,n){var r=e._events;if(void 0===r)return[];var i=r[t];return void 0===i?[]:"function"==typeof i?n?[i.listener||i]:[i]:n?h(i):f(i,i.length)}function c(e){var t=this._events;if(void 0!==t){var n=t[e];if("function"==typeof n)return 1;if(void 0!==n)return n.length}return 0}function f(e,t){for(var n=new Array(t),r=0;r<t;++r)n[r]=e[r];return n}function d(e,t){for(;t+1<e.length;t++)e[t]=e[t+1];e.pop()}function h(e){for(var t=new Array(e.length),n=0;n<t.length;++n)t[n]=e[n].listener||e[n];return t}var p,v="object"==typeof Reflect?Reflect:null,g=v&&"function"==typeof v.apply?v.apply:function(e,t,n){return Function.prototype.apply.call(e,t,n)};p=v&&"function"==typeof v.ownKeys?v.ownKeys:Object.getOwnPropertySymbols?function(e){return Object.getOwnPropertyNames(e).concat(Object.getOwnPropertySymbols(e))}:function(e){return Object.getOwnPropertyNames(e)};var b=Number.isNaN||function(e){return e!==e};e.exports=i,i.EventEmitter=i,i.prototype._events=void 0,i.prototype._eventsCount=0,i.prototype._maxListeners=void 0;var m=10;Object.defineProperty(i,"defaultMaxListeners",{enumerable:!0,get:function(){return m},set:function(e){if("number"!=typeof e||e<0||b(e))throw new RangeError('The value of "defaultMaxListeners" is out of range. It must be a non-negative number. Received '+e+".");m=e}}),i.init=function(){void 0!==this._events&&this._events!==Object.getPrototypeOf(this)._events||(this._events=Object.create(null),this._eventsCount=0),this._maxListeners=this._maxListeners||void 0},i.prototype.setMaxListeners=function(e){if("number"!=typeof e||e<0||b(e))throw new RangeError('The value of "n" is out of range. It must be a non-negative number. Received '+e+".");return this._maxListeners=e,this},i.prototype.getMaxListeners=function(){return o(this)},i.prototype.emit=function(e){for(var t=[],n=1;n<arguments.length;n++)t.push(arguments[n]);var r="error"===e,i=this._events;if(void 0!==i)r=r&&void 0===i.error;else if(!r)return!1;if(r){var o;if(t.length>0&&(o=t[0]),o instanceof Error)throw o;var a=new Error("Unhandled error."+(o?" ("+o.message+")":""));throw a.context=o,a}var s=i[e];if(void 0===s)return!1;if("function"==typeof s)g(s,this,t);else for(var l=s.length,u=f(s,l),n=0;n<l;++n)g(u[n],this,t);return!0},i.prototype.addListener=function(e,t){return a(this,e,t,!1)},i.prototype.on=i.prototype.addListener,i.prototype.prependListener=function(e,t){return a(this,e,t,!0)},i.prototype.once=function(e,t){if("function"!=typeof t)throw new TypeError('The "listener" argument must be of type Function. Received type '+typeof t);return this.on(e,l(this,e,t)),this},i.prototype.prependOnceListener=function(e,t){if("function"!=typeof t)throw new TypeError('The "listener" argument must be of type Function. Received type '+typeof t);return this.prependListener(e,l(this,e,t)),this},i.prototype.removeListener=function(e,t){var n,r,i,o,a;if("function"!=typeof t)throw new TypeError('The "listener" argument must be of type Function. Received type '+typeof t);if(void 0===(r=this._events))return this;if(void 0===(n=r[e]))return this;if(n===t||n.listener===t)0==--this._eventsCount?this._events=Object.create(null):(delete r[e],r.removeListener&&this.emit("removeListener",e,n.listener||t));else if("function"!=typeof n){for(i=-1,o=n.length-1;o>=0;o--)if(n[o]===t||n[o].listener===t){a=n[o].listener,i=o;break}if(i<0)return this;0===i?n.shift():d(n,i),1===n.length&&(r[e]=n[0]),void 0!==r.removeListener&&this.emit("removeListener",e,a||t)}return this},i.prototype.off=i.prototype.removeListener,i.prototype.removeAllListeners=function(e){var t,n,r;if(void 0===(n=this._events))return this;if(void 0===n.removeListener)return 0===arguments.length?(this._events=Object.create(null),this._eventsCount=0):void 0!==n[e]&&(0==--this._eventsCount?this._events=Object.create(null):delete n[e]),this;if(0===arguments.length){var i,o=Object.keys(n);for(r=0;r<o.length;++r)"removeListener"!==(i=o[r])&&this.removeAllListeners(i);return this.removeAllListeners("removeListener"),this._events=Object.create(null),this._eventsCount=0,this}if("function"==typeof(t=n[e]))this.removeListener(e,t);else if(void 0!==t)for(r=t.length-1;r>=0;r--)this.removeListener(e,t[r]);return this},i.prototype.listeners=function(e){return u(this,e,!0)},i.prototype.rawListeners=function(e){return u(this,e,!1)},i.listenerCount=function(e,t){return"function"==typeof e.listenerCount?e.listenerCount(t):c.call(e,t)},i.prototype.listenerCount=c,i.prototype.eventNames=function(){return this._eventsCount>0?p(this._events):[]}},231:function(e,t,n){"use strict";function r(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}var i=n(61),o=(n.n(i),function(){function e(e,t){for(var n=0;n<t.length;n++){var r=t[n];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(e,r.key,r)}}return function(t,n,r){return n&&e(t.prototype,n),r&&e(t,r),t}}()),a=window.$,s=function(){function e(){var t=this;return r(this,e),{extend:function(e){return t.extend(e)}}}return o(e,[{key:"extend",value:function(e){var t=this;this.grid=e,this._addIdsToGridTableRows(),e.getContainer().find(".js-grid-table").tableDnD({onDragClass:"position-row-while-drag",dragHandle:".js-drag-handle",onDrop:function(e,n){return t._handlePositionChange(n)}}),e.getContainer().find(".js-drag-handle").hover(function(){a(this).closest("tr").addClass("hover")},function(){a(this).closest("tr").removeClass("hover")})}},{key:"_handlePositionChange",value:function(e){var t=a(e).find(".js-"+this.grid.getId()+"-position:first"),n=t.data("update-url"),r=t.data("update-method"),i=parseInt(t.data("pagination-offset"),10),o=this._getRowsPositions(i),s={positions:o};this._updatePosition(n,s,r)}},{key:"_getRowsPositions",value:function(e){var t=JSON.parse(a.tableDnD.jsonize()),n=t[this.grid.getId()+"_grid_table"],r=/^row_(\d+)_(\d+)$/,i=n.length,o=[],s=void 0,l=void 0;for(l=0;l<i;++l)s=r.exec(n[l]),o.push({rowId:s[1],newPosition:e+l,oldPosition:parseInt(s[2],10)});return o}},{key:"_addIdsToGridTableRows",value:function(){this.grid.getContainer().find(".js-grid-table .js-"+this.grid.getId()+"-position").each(function(e,t){var n=a(t),r=n.data("id"),i=n.data("position"),o="row_"+r+"_"+i;n.closest("tr").attr("id",o),n.closest("td").addClass("js-drag-handle")})}},{key:"_updatePosition",value:function(e,t,n){for(var r=["GET","POST"].includes(n),i=a("<form>",{action:e,method:r?n:"POST"}).appendTo("body"),o=t.positions.length,s=void 0,l=0;l<o;++l)s=t.positions[l],i.append(a("<input>",{type:"hidden",name:"positions["+l+"][rowId]",value:s.rowId}),a("<input>",{type:"hidden",name:"positions["+l+"][oldPosition]",value:s.oldPosition}),a("<input>",{type:"hidden",name:"positions["+l+"][newPosition]",value:s.newPosition}));r||i.append(a("<input>",{type:"hidden",name:"_method",value:n})),i.submit()}}]),e}();t.a=s},26:function(e,t,n){"use strict";function r(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}var i=function(){function e(e,t){for(var n=0;n<t.length;n++){var r=t[n];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(e,r.key,r)}}return function(t,n,r){return n&&e(t.prototype,n),r&&e(t,r),t}}(),o=window.$,a=function(){function e(){r(this,e)}return i(e,[{key:"extend",value:function(e){var t=e.getContainer();t.on("click",".js-remove-helper-block",function(e){t.remove();var n=o(e.target),r=n.data("closeUrl"),i=n.data("cardName");r&&o.post(r,{close:1,name:i})})}}]),e}();t.a=a},27:function(e,t,n){"use strict";function r(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}var i=function(){function e(e,t){for(var n=0;n<t.length;n++){var r=t[n];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(e,r.key,r)}}return function(t,n,r){return n&&e(t.prototype,n),r&&e(t,r),t}}(),o=window.$,a=function(){function e(t){r(this,e),this.id=t,this.$container=o("#"+this.id)}return i(e,[{key:"getContainer",value:function(){return this.$container}},{key:"addExtension",value:function(e){e.extend(this)}}]),e}();t.a=a},28:function(e,t,n){"use strict";function r(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}/**
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
var i=window.$,o=function e(t){var n=t.tokenFieldSelector,o=t.options,a=void 0===o?{}:o;r(this,e),i(n).tokenfield(a)};t.a=o},3:function(e,t){e.exports=jQuery},302:function(e,t,n){"use strict";Object.defineProperty(t,"__esModule",{value:!0});var r=n(4),i=n(7),o=n(11),a=n(5),s=n(6),l=n(9),u=n(15),c=n(10),f=n(8),d=n(16),h=n(231),p=n(17),v=n(14),g=n(32),b=n(28),m=n(27),y=n(26);(0,window.$)(function(){var e=new r.a("cms_page_category");e.addExtension(new s.a),e.addExtension(new l.a),e.addExtension(new a.a),e.addExtension(new i.a),e.addExtension(new u.a),e.addExtension(new c.a),e.addExtension(new f.a),e.addExtension(new o.a),e.addExtension(new d.a),e.addExtension(new h.a),n.i(g.a)({sourceElementSelector:'input[name^="cms_page_category[name]"]',destinationElementSelector:'input[name^="cms_page_category[friendly_url]"]'}),new p.a("#cms_page_category_parent_category"),new p.a("#cms_page_category_shop_association").enableAutoCheckChildren(),new v.a,new b.a({tokenFieldSelector:'input[name^="cms_page_category[meta_keywords]"]',options:{createTokensOnBlur:!0}});var t=new r.a("cms_page");t.addExtension(new s.a),t.addExtension(new l.a),t.addExtension(new a.a),t.addExtension(new i.a),t.addExtension(new d.a),t.addExtension(new f.a),t.addExtension(new c.a),t.addExtension(new o.a),t.addExtension(new h.a),new m.a("cms-pages-showcase-card").addExtension(new y.a)})},32:function(e,t,n){"use strict";/**
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
var r=window.$,i=function(e){var t=e.attr("data-lang-id");return void 0===t?null:parseInt(t)},o=function(e){var t=e.sourceElementSelector,n=e.destinationElementSelector,o=e.options,a=void 0===o?{eventName:"input"}:o;r(document).on(a.eventName,""+t,function(e){var t=r(e.currentTarget),o=i(t);r(null!==o?n+'[data-lang-id="'+o+'"]':n).val(str2url(t.val(),"UTF-8"))})};t.a=o},4:function(e,t,n){"use strict";function r(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}var i=function(){function e(e,t){for(var n=0;n<t.length;n++){var r=t[n];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(e,r.key,r)}}return function(t,n,r){return n&&e(t.prototype,n),r&&e(t,r),t}}(),o=window.$,a=function(){function e(t){r(this,e),this.id=t,this.$container=o("#"+this.id+"_grid")}return i(e,[{key:"getId",value:function(){return this.id}},{key:"getContainer",value:function(){return this.$container}},{key:"getHeaderContainer",value:function(){return this.$container.closest(".js-grid-panel").find(".js-grid-header")}},{key:"addExtension",value:function(e){e.extend(this)}}]),e}();t.a=a},5:function(e,t,n){"use strict";function r(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}var i=n(13),o=function(){function e(e,t){for(var n=0;n<t.length;n++){var r=t[n];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(e,r.key,r)}}return function(t,n,r){return n&&e(t.prototype,n),r&&e(t,r),t}}(),a=window.$,s=function(){function e(){r(this,e)}return o(e,[{key:"extend",value:function(e){e.getContainer().on("click",".js-reset-search",function(e){n.i(i.a)(a(e.currentTarget).data("url"),a(e.currentTarget).data("redirect"))})}}]),e}();t.a=s},6:function(e,t,n){"use strict";function r(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}var i=function(){function e(e,t){for(var n=0;n<t.length;n++){var r=t[n];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(e,r.key,r)}}return function(t,n,r){return n&&e(t.prototype,n),r&&e(t,r),t}}(),o=function(){function e(){r(this,e)}return i(e,[{key:"extend",value:function(e){e.getHeaderContainer().on("click",".js-common_refresh_list-grid-action",function(){location.reload()})}}]),e}();t.a=o},61:function(e,t,n){(function(e){/*! jquery.tablednd.js 30-12-2017 */
!function(t,n,r,i){var o="touchstart mousedown",a="touchmove mousemove",s="touchend mouseup";t(r).ready(function(){function e(e){for(var t={},n=e.match(/([^;:]+)/g)||[];n.length;)t[n.shift()]=n.shift().trim();return t}t("table").each(function(){"dnd"===t(this).data("table")&&t(this).tableDnD({onDragStyle:t(this).data("ondragstyle")&&e(t(this).data("ondragstyle"))||null,onDropStyle:t(this).data("ondropstyle")&&e(t(this).data("ondropstyle"))||null,onDragClass:void 0===t(this).data("ondragclass")&&"tDnD_whileDrag"||t(this).data("ondragclass"),onDrop:t(this).data("ondrop")&&new Function("table","row",t(this).data("ondrop")),onDragStart:t(this).data("ondragstart")&&new Function("table","row",t(this).data("ondragstart")),onDragStop:t(this).data("ondragstop")&&new Function("table","row",t(this).data("ondragstop")),scrollAmount:t(this).data("scrollamount")||5,sensitivity:t(this).data("sensitivity")||10,hierarchyLevel:t(this).data("hierarchylevel")||0,indentArtifact:t(this).data("indentartifact")||'<div class="indent">&nbsp;</div>',autoWidthAdjust:t(this).data("autowidthadjust")||!0,autoCleanRelations:t(this).data("autocleanrelations")||!0,jsonPretifySeparator:t(this).data("jsonpretifyseparator")||"\t",serializeRegexp:t(this).data("serializeregexp")&&new RegExp(t(this).data("serializeregexp"))||/[^\-]*$/,serializeParamName:t(this).data("serializeparamname")||!1,dragHandle:t(this).data("draghandle")||null})})}),e.tableDnD={currentTable:null,dragObject:null,mouseOffset:null,oldX:0,oldY:0,build:function(e){return this.each(function(){this.tableDnDConfig=t.extend({onDragStyle:null,onDropStyle:null,onDragClass:"tDnD_whileDrag",onDrop:null,onDragStart:null,onDragStop:null,scrollAmount:5,sensitivity:10,hierarchyLevel:0,indentArtifact:'<div class="indent">&nbsp;</div>',autoWidthAdjust:!0,autoCleanRelations:!0,jsonPretifySeparator:"\t",serializeRegexp:/[^\-]*$/,serializeParamName:!1,dragHandle:null},e||{}),t.tableDnD.makeDraggable(this),this.tableDnDConfig.hierarchyLevel&&t.tableDnD.makeIndented(this)}),this},makeIndented:function(e){var n,r,i=e.tableDnDConfig,o=e.rows,a=t(o).first().find("td:first")[0],s=0,l=0;if(t(e).hasClass("indtd"))return null;r=t(e).addClass("indtd").attr("style"),t(e).css({whiteSpace:"nowrap"});for(var u=0;u<o.length;u++)l<t(o[u]).find("td:first").text().length&&(l=t(o[u]).find("td:first").text().length,n=u);for(t(a).css({width:"auto"}),u=0;u<i.hierarchyLevel;u++)t(o[n]).find("td:first").prepend(i.indentArtifact);for(a&&t(a).css({width:a.offsetWidth}),r&&t(e).css(r),u=0;u<i.hierarchyLevel;u++)t(o[n]).find("td:first").children(":first").remove();return i.hierarchyLevel&&t(o).each(function(){(s=t(this).data("level")||0)<=i.hierarchyLevel&&t(this).data("level",s)||t(this).data("level",0);for(var e=0;e<t(this).data("level");e++)t(this).find("td:first").prepend(i.indentArtifact)}),this},makeDraggable:function(e){var n=e.tableDnDConfig;n.dragHandle&&t(n.dragHandle,e).each(function(){t(this).bind(o,function(r){return t.tableDnD.initialiseDrag(t(this).parents("tr")[0],e,this,r,n),!1})})||t(e.rows).each(function(){t(this).hasClass("nodrag")?t(this).css("cursor",""):t(this).bind(o,function(r){if("TD"===r.target.tagName)return t.tableDnD.initialiseDrag(this,e,this,r,n),!1}).css("cursor","move")})},currentOrder:function(){var e=this.currentTable.rows;return t.map(e,function(e){return(t(e).data("level")+e.id).replace(/\s/g,"")}).join("")},initialiseDrag:function(e,n,i,o,l){this.dragObject=e,this.currentTable=n,this.mouseOffset=this.getMouseOffset(i,o),this.originalOrder=this.currentOrder(),t(r).bind(a,this.mousemove).bind(s,this.mouseup),l.onDragStart&&l.onDragStart(n,i)},updateTables:function(){this.each(function(){this.tableDnDConfig&&t.tableDnD.makeDraggable(this)})},mouseCoords:function(e){return e.originalEvent.changedTouches?{x:e.originalEvent.changedTouches[0].clientX,y:e.originalEvent.changedTouches[0].clientY}:e.pageX||e.pageY?{x:e.pageX,y:e.pageY}:{x:e.clientX+r.body.scrollLeft-r.body.clientLeft,y:e.clientY+r.body.scrollTop-r.body.clientTop}},getMouseOffset:function(e,t){var r,i;return t=t||n.event,i=this.getPosition(e),r=this.mouseCoords(t),{x:r.x-i.x,y:r.y-i.y}},getPosition:function(e){var t=0,n=0;for(0===e.offsetHeight&&(e=e.firstChild);e.offsetParent;)t+=e.offsetLeft,n+=e.offsetTop,e=e.offsetParent;return t+=e.offsetLeft,n+=e.offsetTop,{x:t,y:n}},autoScroll:function(e){var t=this.currentTable.tableDnDConfig,i=n.pageYOffset,o=n.innerHeight?n.innerHeight:r.documentElement.clientHeight?r.documentElement.clientHeight:r.body.clientHeight;r.all&&(void 0!==r.compatMode&&"BackCompat"!==r.compatMode?i=r.documentElement.scrollTop:void 0!==r.body&&(i=r.body.scrollTop)),e.y-i<t.scrollAmount&&n.scrollBy(0,-t.scrollAmount)||o-(e.y-i)<t.scrollAmount&&n.scrollBy(0,t.scrollAmount)},moveVerticle:function(e,t){0!==e.vertical&&t&&this.dragObject!==t&&this.dragObject.parentNode===t.parentNode&&(0>e.vertical&&this.dragObject.parentNode.insertBefore(this.dragObject,t.nextSibling)||0<e.vertical&&this.dragObject.parentNode.insertBefore(this.dragObject,t))},moveHorizontal:function(e,n){var r,i=this.currentTable.tableDnDConfig;if(!i.hierarchyLevel||0===e.horizontal||!n||this.dragObject!==n)return null;r=t(n).data("level"),0<e.horizontal&&r>0&&t(n).find("td:first").children(":first").remove()&&t(n).data("level",--r),0>e.horizontal&&r<i.hierarchyLevel&&t(n).prev().data("level")>=r&&t(n).children(":first").prepend(i.indentArtifact)&&t(n).data("level",++r)},mousemove:function(e){var n,r,i,o,a,s=t(t.tableDnD.dragObject),l=t.tableDnD.currentTable.tableDnDConfig;return e&&e.preventDefault(),!!t.tableDnD.dragObject&&("touchmove"===e.type&&event.preventDefault(),l.onDragClass&&s.addClass(l.onDragClass)||s.css(l.onDragStyle),r=t.tableDnD.mouseCoords(e),o=r.x-t.tableDnD.mouseOffset.x,a=r.y-t.tableDnD.mouseOffset.y,t.tableDnD.autoScroll(r),n=t.tableDnD.findDropTargetRow(s,a),i=t.tableDnD.findDragDirection(o,a),t.tableDnD.moveVerticle(i,n),t.tableDnD.moveHorizontal(i,n),!1)},findDragDirection:function(e,t){var n=this.currentTable.tableDnDConfig.sensitivity,r=this.oldX,i=this.oldY,o=r-n,a=r+n,s=i-n,l=i+n,u={horizontal:e>=o&&e<=a?0:e>r?-1:1,vertical:t>=s&&t<=l?0:t>i?-1:1};return 0!==u.horizontal&&(this.oldX=e),0!==u.vertical&&(this.oldY=t),u},findDropTargetRow:function(e,n){for(var r=0,i=this.currentTable.rows,o=this.currentTable.tableDnDConfig,a=0,s=null,l=0;l<i.length;l++)if(s=i[l],a=this.getPosition(s).y,r=parseInt(s.offsetHeight)/2,0===s.offsetHeight&&(a=this.getPosition(s.firstChild).y,r=parseInt(s.firstChild.offsetHeight)/2),n>a-r&&n<a+r)return e.is(s)||o.onAllowDrop&&!o.onAllowDrop(e,s)||t(s).hasClass("nodrop")?null:s;return null},processMouseup:function(){if(!this.currentTable||!this.dragObject)return null;var e=this.currentTable.tableDnDConfig,n=this.dragObject,i=0,o=0;t(r).unbind(a,this.mousemove).unbind(s,this.mouseup),e.hierarchyLevel&&e.autoCleanRelations&&t(this.currentTable.rows).first().find("td:first").children().each(function(){(o=t(this).parents("tr:first").data("level"))&&t(this).parents("tr:first").data("level",--o)&&t(this).remove()})&&e.hierarchyLevel>1&&t(this.currentTable.rows).each(function(){if((o=t(this).data("level"))>1)for(i=t(this).prev().data("level");o>i+1;)t(this).find("td:first").children(":first").remove(),t(this).data("level",--o)}),e.onDragClass&&t(n).removeClass(e.onDragClass)||t(n).css(e.onDropStyle),this.dragObject=null,e.onDrop&&this.originalOrder!==this.currentOrder()&&t(n).hide().fadeIn("fast")&&e.onDrop(this.currentTable,n),e.onDragStop&&e.onDragStop(this.currentTable,n),this.currentTable=null},mouseup:function(e){return e&&e.preventDefault(),t.tableDnD.processMouseup(),!1},jsonize:function(e){var t=this.currentTable;return e?JSON.stringify(this.tableData(t),null,t.tableDnDConfig.jsonPretifySeparator):JSON.stringify(this.tableData(t))},serialize:function(){return t.param(this.tableData(this.currentTable))},serializeTable:function(e){for(var t="",n=e.tableDnDConfig.serializeParamName||e.id,r=e.rows,i=0;i<r.length;i++){t.length>0&&(t+="&");var o=r[i].id;o&&e.tableDnDConfig&&e.tableDnDConfig.serializeRegexp&&(o=o.match(e.tableDnDConfig.serializeRegexp)[0],t+=n+"[]="+o)}return t},serializeTables:function(){var e=[];return t("table").each(function(){this.id&&e.push(t.param(t.tableDnD.tableData(this)))}),e.join("&")},tableData:function(e){var n,r,i,o,a=e.tableDnDConfig,s=[],l=0,u=0,c=null,f={};if(e||(e=this.currentTable),!e||!e.rows||!e.rows.length)return{error:{code:500,message:"Not a valid table."}};if(!e.id&&!a.serializeParamName)return{error:{code:500,message:"No serializable unique id provided."}};o=a.autoCleanRelations&&e.rows||t.makeArray(e.rows),r=a.serializeParamName||e.id,i=r,n=function(e){return e&&a&&a.serializeRegexp?e.match(a.serializeRegexp)[0]:e},f[i]=[],!a.autoCleanRelations&&t(o[0]).data("level")&&o.unshift({id:"undefined"});for(var d=0;d<o.length;d++)if(a.hierarchyLevel){if(0===(u=t(o[d]).data("level")||0))i=r,s=[];else if(u>l)s.push([i,l]),i=n(o[d-1].id);else if(u<l)for(var h=0;h<s.length;h++)s[h][1]===u&&(i=s[h][0]),s[h][1]>=l&&(s[h][1]=0);l=u,t.isArray(f[i])||(f[i]=[]),(c=n(o[d].id))&&f[i].push(c)}else(c=n(o[d].id))&&f[i].push(c);return f}},e.fn.extend({tableDnD:t.tableDnD.build,tableDnDUpdate:t.tableDnD.updateTables,tableDnDSerialize:t.proxy(t.tableDnD.serialize,t.tableDnD),tableDnDSerializeAll:t.tableDnD.serializeTables,tableDnDData:t.proxy(t.tableDnD.tableData,t.tableDnD)})}(e,window,window.document)}).call(t,n(3))},7:function(e,t,n){"use strict";function r(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}var i=n(12),o=function(){function e(e,t){for(var n=0;n<t.length;n++){var r=t[n];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(e,r.key,r)}}return function(t,n,r){return n&&e(t.prototype,n),r&&e(t,r),t}}(),a=function(){function e(){r(this,e)}return o(e,[{key:"extend",value:function(e){var t=e.getContainer().find("table.table");new i.a(t).attach()}}]),e}();t.a=a},8:function(e,t,n){"use strict";function r(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}var i=function(){function e(e,t){for(var n=0;n<t.length;n++){var r=t[n];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(e,r.key,r)}}return function(t,n,r){return n&&e(t.prototype,n),r&&e(t,r),t}}(),o=window.$,a=function(){function e(){r(this,e)}return i(e,[{key:"extend",value:function(e){this._handleBulkActionCheckboxSelect(e),this._handleBulkActionSelectAllCheckbox(e)}},{key:"_handleBulkActionSelectAllCheckbox",value:function(e){var t=this;e.getContainer().on("change",".js-bulk-action-select-all",function(n){var r=o(n.currentTarget),i=r.is(":checked");i?t._enableBulkActionsBtn(e):t._disableBulkActionsBtn(e),e.getContainer().find(".js-bulk-action-checkbox").prop("checked",i)})}},{key:"_handleBulkActionCheckboxSelect",value:function(e){var t=this;e.getContainer().on("change",".js-bulk-action-checkbox",function(){e.getContainer().find(".js-bulk-action-checkbox:checked").length>0?t._enableBulkActionsBtn(e):t._disableBulkActionsBtn(e)})}},{key:"_enableBulkActionsBtn",value:function(e){e.getContainer().find(".js-bulk-actions-btn").prop("disabled",!1)}},{key:"_disableBulkActionsBtn",value:function(e){e.getContainer().find(".js-bulk-actions-btn").prop("disabled",!0)}}]),e}();t.a=a},9:function(e,t,n){"use strict";function r(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}var i=function(){function e(e,t){for(var n=0;n<t.length;n++){var r=t[n];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(e,r.key,r)}}return function(t,n,r){return n&&e(t.prototype,n),r&&e(t,r),t}}(),o=window.$,a=function(){function e(){r(this,e)}return i(e,[{key:"extend",value:function(e){var t=this;e.getHeaderContainer().on("click",".js-common_show_query-grid-action",function(){return t._onShowSqlQueryClick(e)}),e.getHeaderContainer().on("click",".js-common_export_sql_manager-grid-action",function(){return t._onExportSqlManagerClick(e)})}},{key:"_onShowSqlQueryClick",value:function(e){var t=o("#"+e.getId()+"_common_show_query_modal_form");this._fillExportForm(t,e);var n=o("#"+e.getId()+"_grid_common_show_query_modal");n.modal("show"),n.on("click",".btn-sql-submit",function(){return t.submit()})}},{key:"_onExportSqlManagerClick",value:function(e){var t=o("#"+e.getId()+"_common_show_query_modal_form");this._fillExportForm(t,e),t.submit()}},{key:"_fillExportForm",value:function(e,t){var n=t.getContainer().find(".js-grid-table").data("query");e.find('textarea[name="sql"]').val(n),e.find('input[name="name"]').val(this._getNameFromBreadcrumb())}},{key:"_getNameFromBreadcrumb",value:function(){var e=o(".header-toolbar").find(".breadcrumb-item"),t="";return e.each(function(e,n){var r=o(n),i=0<r.find("a").length?r.find("a").text():r.text();0<t.length&&(t=t.concat(" > ")),t=t.concat(i)}),t}}]),e}();t.a=a}});