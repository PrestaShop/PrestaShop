window.logs=function(t){function e(r){if(n[r])return n[r].exports;var o=n[r]={i:r,l:!1,exports:{}};return t[r].call(o.exports,o,o.exports,e),o.l=!0,o.exports}var n={};return e.m=t,e.c=n,e.i=function(t){return t},e.d=function(t,n,r){e.o(t,n)||Object.defineProperty(t,n,{configurable:!1,enumerable:!0,get:r})},e.n=function(t){var n=t&&t.__esModule?function(){return t.default}:function(){return t};return e.d(n,"a",n),n},e.o=function(t,e){return Object.prototype.hasOwnProperty.call(t,e)},e.p="",e(e.s=498)}({0:function(t,e,n){"use strict";e.__esModule=!0,e.default=function(t,e){if(!(t instanceof e))throw new TypeError("Cannot call a class as a function")}},1:function(t,e,n){"use strict";e.__esModule=!0;var r=n(19),o=function(t){return t&&t.__esModule?t:{default:t}}(r);e.default=function(){function t(t,e){for(var n=0;n<e.length;n++){var r=e[n];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),(0,o.default)(t,r.key,r)}}return function(e,n,r){return n&&t(e.prototype,n),r&&t(e,r),e}}()},10:function(t,e,n){var r=n(6),o=n(12);t.exports=n(2)?function(t,e,n){return r.f(t,e,o(1,n))}:function(t,e,n){return t[e]=n,t}},11:function(t,e,n){var r=n(4);t.exports=function(t){if(!r(t))throw TypeError(t+" is not an object!");return t}},12:function(t,e){t.exports=function(t,e){return{enumerable:!(1&t),configurable:!(2&t),writable:!(4&t),value:e}}},13:function(t,e,n){var r=n(4);t.exports=function(t,e){if(!r(t))return t;var n,o;if(e&&"function"==typeof(n=t.toString)&&!r(o=n.call(t)))return o;if("function"==typeof(n=t.valueOf)&&!r(o=n.call(t)))return o;if(!e&&"function"==typeof(n=t.toString)&&!r(o=n.call(t)))return o;throw TypeError("Can't convert object to primitive value")}},15:function(t,e,n){var r=n(18);t.exports=function(t,e,n){if(r(t),void 0===e)return t;switch(n){case 1:return function(n){return t.call(e,n)};case 2:return function(n,r){return t.call(e,n,r)};case 3:return function(n,r,o){return t.call(e,n,r,o)}}return function(){return t.apply(e,arguments)}}},16:function(t,e,n){var r=n(4),o=n(5).document,u=r(o)&&r(o.createElement);t.exports=function(t){return u?o.createElement(t):{}}},17:function(t,e,n){t.exports=!n(2)&&!n(7)(function(){return 7!=Object.defineProperty(n(16)("div"),"a",{get:function(){return 7}}).a})},18:function(t,e){t.exports=function(t){if("function"!=typeof t)throw TypeError(t+" is not a function!");return t}},19:function(t,e,n){t.exports={default:n(20),__esModule:!0}},2:function(t,e,n){t.exports=!n(7)(function(){return 7!=Object.defineProperty({},"a",{get:function(){return 7}}).a})},20:function(t,e,n){n(21);var r=n(3).Object;t.exports=function(t,e,n){return r.defineProperty(t,e,n)}},21:function(t,e,n){var r=n(8);r(r.S+r.F*!n(2),"Object",{defineProperty:n(6).f})},23:function(t,e,n){"use strict";function r(t){return t&&t.__esModule?t:{default:t}}Object.defineProperty(e,"__esModule",{value:!0});var o=n(0),u=r(o),i=n(1),a=r(i),c=window.$,f=function(){function t(e){(0,u.default)(this,t),this.id=e,this.$container=c("#"+this.id+"_grid")}return(0,a.default)(t,[{key:"getId",value:function(){return this.id}},{key:"getContainer",value:function(){return this.$container}},{key:"getHeaderContainer",value:function(){return this.$container.closest(".js-grid-panel").find(".js-grid-header")}},{key:"addExtension",value:function(t){t.extend(this)}}]),t}();e.default=f},24:function(t,e,n){"use strict";function r(t){return t&&t.__esModule?t:{default:t}}Object.defineProperty(e,"__esModule",{value:!0});var o=n(0),u=r(o),i=n(1),a=r(i),c=n(42),f=r(c),l=window.$,d=function(){function t(){(0,u.default)(this,t)}return(0,a.default)(t,[{key:"extend",value:function(t){t.getContainer().on("click",".js-reset-search",function(t){(0,f.default)(l(t.currentTarget).data("url"),l(t.currentTarget).data("redirect"))})}}]),t}();e.default=d},25:function(t,e,n){"use strict";function r(t){return t&&t.__esModule?t:{default:t}}Object.defineProperty(e,"__esModule",{value:!0});var o=n(0),u=r(o),i=n(1),a=r(i),c=n(40),f=r(c),l=function(){function t(){(0,u.default)(this,t)}return(0,a.default)(t,[{key:"extend",value:function(t){var e=t.getContainer().find("table.table");new f.default(e).attach()}}]),t}();/**
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
e.default=l},26:function(t,e,n){"use strict";function r(t){return t&&t.__esModule?t:{default:t}}Object.defineProperty(e,"__esModule",{value:!0});var o=n(0),u=r(o),i=n(1),a=r(i),c=function(){function t(){(0,u.default)(this,t)}return(0,a.default)(t,[{key:"extend",value:function(t){t.getHeaderContainer().on("click",".js-common_refresh_list-grid-action",function(){location.reload()})}}]),t}();e.default=c},28:function(t,e,n){"use strict";function r(t){return t&&t.__esModule?t:{default:t}}Object.defineProperty(e,"__esModule",{value:!0});var o=n(0),u=r(o),i=n(1),a=r(i),c=window.$,f=function(){function t(){(0,u.default)(this,t)}return(0,a.default)(t,[{key:"extend",value:function(t){var e=this;t.getHeaderContainer().on("click",".js-common_show_query-grid-action",function(){return e._onShowSqlQueryClick(t)}),t.getHeaderContainer().on("click",".js-common_export_sql_manager-grid-action",function(){return e._onExportSqlManagerClick(t)})}},{key:"_onShowSqlQueryClick",value:function(t){var e=c("#"+t.getId()+"_common_show_query_modal_form");this._fillExportForm(e,t);var n=c("#"+t.getId()+"_grid_common_show_query_modal");n.modal("show"),n.on("click",".btn-sql-submit",function(){return e.submit()})}},{key:"_onExportSqlManagerClick",value:function(t){var e=c("#"+t.getId()+"_common_show_query_modal_form");this._fillExportForm(e,t),e.submit()}},{key:"_fillExportForm",value:function(t,e){var n=e.getContainer().find(".js-grid-table").data("query");t.find('textarea[name="sql"]').val(n),t.find('input[name="name"]').val(this._getNameFromBreadcrumb())}},{key:"_getNameFromBreadcrumb",value:function(){var t=c(".header-toolbar").find(".breadcrumb-item"),e="";return t.each(function(t,n){var r=c(n),o=0<r.find("a").length?r.find("a").text():r.text();0<e.length&&(e=e.concat(" > ")),e=e.concat(o)}),e}}]),t}();e.default=f},3:function(t,e){var n=t.exports={version:"2.4.0"};"number"==typeof __e&&(__e=n)},30:function(t,e,n){"use strict";function r(t){return t&&t.__esModule?t:{default:t}}Object.defineProperty(e,"__esModule",{value:!0});var o=n(0),u=r(o),i=n(1),a=r(i),c=function(){function t(){(0,u.default)(this,t)}return(0,a.default)(t,[{key:"extend",value:function(t){var e=t.getContainer().find(".column-filters");e.find(".grid-search-button").prop("disabled",!0),e.find("input, select").on("input dp.change",function(){e.find(".grid-search-button").prop("disabled",!1),e.find(".js-grid-reset-button").prop("hidden",!1)})}}]),t}();e.default=c},4:function(t,e){t.exports=function(t){return"object"==typeof t?null!==t:"function"==typeof t}},40:function(t,e,n){"use strict";(function(t){function r(t){return t&&t.__esModule?t:{default:t}}Object.defineProperty(e,"__esModule",{value:!0});var o=n(0),u=r(o),i=n(1),a=r(i),c=t.$,f=function(){function t(e){(0,u.default)(this,t),this.selector=".ps-sortable-column",this.columns=c(e).find(this.selector)}return(0,a.default)(t,[{key:"attach",value:function(){var t=this;this.columns.on("click",function(e){var n=c(e.delegateTarget);t._sortByColumn(n,t._getToggledSortDirection(n))})}},{key:"sortBy",value:function(t,e){var n=this.columns.is('[data-sort-col-name="'+t+'"]');if(!n)throw new Error('Cannot sort by "'+t+'": invalid column');this._sortByColumn(n,e)}},{key:"_sortByColumn",value:function(t,e){window.location=this._getUrl(t.data("sortColName"),"desc"===e?"desc":"asc",t.data("sortPrefix"))}},{key:"_getToggledSortDirection",value:function(t){return"asc"===t.data("sortDirection")?"desc":"asc"}},{key:"_getUrl",value:function(t,e,n){var r=new URL(window.location.href),o=r.searchParams;return n?(o.set(n+"[orderBy]",t),o.set(n+"[sortOrder]",e)):(o.set("orderBy",t),o.set("sortOrder",e)),r.toString()}}]),t}();e.default=f}).call(e,n(9))},42:function(t,e,n){"use strict";(function(t){Object.defineProperty(e,"__esModule",{value:!0});/**
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
var n=t.$,r=function(t,e){n.post(t).then(function(){return window.location.assign(e)})};e.default=r}).call(e,n(9))},498:function(t,e,n){"use strict";(function(t){function e(t){return t&&t.__esModule?t:{default:t}}var r=n(23),o=e(r),u=n(26),i=e(u),a=n(28),c=e(a),f=n(24),l=e(f),d=n(25),s=e(d),v=n(65),p=e(v),_=n(30),h=e(_);/**
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
(0,t.$)(function(){var t=new o.default("logs");t.addExtension(new i.default),t.addExtension(new c.default),t.addExtension(new l.default),t.addExtension(new s.default),t.addExtension(new p.default),t.addExtension(new h.default)})}).call(e,n(9))},5:function(t,e){var n=t.exports="undefined"!=typeof window&&window.Math==Math?window:"undefined"!=typeof self&&self.Math==Math?self:Function("return this")();"number"==typeof __g&&(__g=n)},6:function(t,e,n){var r=n(11),o=n(17),u=n(13),i=Object.defineProperty;e.f=n(2)?Object.defineProperty:function(t,e,n){if(r(t),e=u(e,!0),r(n),o)try{return i(t,e,n)}catch(t){}if("get"in n||"set"in n)throw TypeError("Accessors not supported!");return"value"in n&&(t[e]=n.value),t}},65:function(t,e,n){"use strict";function r(t){return t&&t.__esModule?t:{default:t}}Object.defineProperty(e,"__esModule",{value:!0});var o=n(0),u=r(o),i=n(1),a=r(i),c=window.$,f=function(){function t(){var e=this;return(0,u.default)(this,t),{extend:function(t){return e.extend(t)}}}return(0,a.default)(t,[{key:"extend",value:function(t){var e=this;t.getHeaderContainer().on("click",".js-grid-action-submit-btn",function(n){e.handleSubmit(n,t)})}},{key:"handleSubmit",value:function(t,e){var n=c(t.currentTarget),r=n.data("confirm-message");if(!(void 0!==r&&0<r.length)||confirm(r)){var o=c("#"+e.getId()+"_filter_form");o.attr("action",n.data("url")),o.attr("method",n.data("method")),o.find('input[name="'+e.getId()+'[_token]"]').val(n.data("csrf")),o.submit()}}}]),t}();e.default=f},7:function(t,e){t.exports=function(t){try{return!!t()}catch(t){return!0}}},8:function(t,e,n){var r=n(5),o=n(3),u=n(15),i=n(10),a=function(t,e,n){var c,f,l,d=t&a.F,s=t&a.G,v=t&a.S,p=t&a.P,_=t&a.B,h=t&a.W,y=s?o:o[e]||(o[e]={}),m=y.prototype,g=s?r:v?r[e]:(r[e]||{}).prototype;s&&(n=e);for(c in n)(f=!d&&g&&void 0!==g[c])&&c in y||(l=f?g[c]:n[c],y[c]=s&&"function"!=typeof g[c]?n[c]:_&&f?u(l,r):h&&g[c]==l?function(t){var e=function(e,n,r){if(this instanceof t){switch(arguments.length){case 0:return new t;case 1:return new t(e);case 2:return new t(e,n)}return new t(e,n,r)}return t.apply(this,arguments)};return e.prototype=t.prototype,e}(l):p&&"function"==typeof l?u(Function.call,l):l,p&&((y.virtual||(y.virtual={}))[c]=l,t&a.R&&m&&!m[c]&&i(m,c,l)))};a.F=1,a.G=2,a.S=4,a.P=8,a.B=16,a.W=32,a.U=64,a.R=128,t.exports=a},9:function(t,e){var n;n=function(){return this}();try{n=n||Function("return this")()||(0,eval)("this")}catch(t){"object"==typeof window&&(n=window)}t.exports=n}});