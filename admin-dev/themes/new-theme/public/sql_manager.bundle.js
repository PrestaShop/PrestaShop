/******/!function(n){function t(r){if(e[r])return e[r].exports;var o=e[r]={i:r,l:!1,exports:{}};return n[r].call(o.exports,o,o.exports,t),o.l=!0,o.exports}// webpackBootstrap
/******/
var e={};t.m=n,t.c=e,t.i=function(n){return n},t.d=function(n,e,r){t.o(n,e)||Object.defineProperty(n,e,{configurable:!1,enumerable:!0,get:r})},t.n=function(n){var e=n&&n.__esModule?function(){return n.default}:function(){return n};return t.d(e,"a",e),e},t.o=function(n,t){return Object.prototype.hasOwnProperty.call(n,t)},t.p="",t(t.s=449)}({1:function(n,t){var e;e=function(){return this}();try{e=e||Function("return this")()||(0,eval)("this")}catch(n){"object"==typeof window&&(e=window)}n.exports=e},10:function(n,t,e){"use strict";function r(n,t){if(!(n instanceof t))throw new TypeError("Cannot call a class as a function")}var o=e(3),a=function(){function n(n,t){for(var e=0;e<t.length;e++){var r=t[e];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(n,r.key,r)}}return function(t,e,r){return e&&n(t.prototype,e),r&&n(t,r),t}}(),i=function(){function n(){r(this,n)}return a(n,[{key:"extend",value:function(n){var t=n.getContainer().find("table.table");new o.a(t).attach()}}]),n}();t.a=i},11:function(n,t,e){"use strict";function r(n,t){if(!(n instanceof t))throw new TypeError("Cannot call a class as a function")}var o=function(){function n(n,t){for(var e=0;e<t.length;e++){var r=t[e];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(n,r.key,r)}}return function(t,e,r){return e&&n(t.prototype,e),r&&n(t,r),t}}(),a=window.$,i=function(){function n(){var t=this;return r(this,n),{extend:function(n){return t.extend(n)}}}return o(n,[{key:"extend",value:function(n){var t=this;n.getContainer().on("click",".js-bulk-action-submit-btn",function(e){t.submit(e,n)})}},{key:"submit",value:function(n,t){var e=a(n.currentTarget),r=e.data("confirm-message");if(!(void 0!==r&&0<r.length)||confirm(r)){var o=a("#"+t.getId()+"_filter_form");o.attr("action",e.data("form-url")),o.attr("method",e.data("form-method")),o.submit()}}}]),n}();t.a=i},13:function(n,t,e){"use strict";function r(n,t){if(!(n instanceof t))throw new TypeError("Cannot call a class as a function")}var o=function(){function n(n,t){for(var e=0;e<t.length;e++){var r=t[e];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(n,r.key,r)}}return function(t,e,r){return e&&n(t.prototype,e),r&&n(t,r),t}}(),a=window.$,i=function(){function n(){r(this,n)}return o(n,[{key:"extend",value:function(n){n.getContainer().on("click",".js-link-row-action",function(n){var t=a(n.currentTarget).data("confirm-message");t.length&&!confirm(t)&&n.preventDefault()})}}]),n}();t.a=i},18:function(n,t,e){"use strict";function r(n,t){if(!(n instanceof t))throw new TypeError("Cannot call a class as a function")}var o=function(){function n(n,t){for(var e=0;e<t.length;e++){var r=t[e];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(n,r.key,r)}}return function(t,e,r){return e&&n(t.prototype,e),r&&n(t,r),t}}(),a=window.$,i=function(){function n(){var t=this;return r(this,n),{extend:function(n){return t.extend(n)}}}return o(n,[{key:"extend",value:function(n){var t=this;n.getContainer().on("click",".js-grid-action-submit-btn",function(e){t.handleSubmit(e,n)})}},{key:"handleSubmit",value:function(n,t){var e=a(n.currentTarget),r=e.data("confirm-message");if(!(void 0!==r&&0<r.length)||confirm(r)){var o=a("#"+t.getId()+"_filter_form");o.attr("action",e.data("url")),o.attr("method",e.data("method")),o.find('input[name="'+t.getId()+'[_token]"]').val(e.data("csrf")),o.submit()}}}]),n}();t.a=i},213:function(n,t,e){"use strict";function r(n,t){if(!(n instanceof t))throw new TypeError("Cannot call a class as a function")}Object.defineProperty(t,"__esModule",{value:!0});var o=e(4),a=e(9),i=e(7),u=e(8),c=e(10),l=e(6),s=e(11),f=e(18),d=e(13),b=function(){function n(n,t){for(var e=0;e<t.length;e++){var r=t[e];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(n,r.key,r)}}return function(t,e,r){return e&&n(t.prototype,e),r&&n(t,r),t}}(),v=window.$,h=function(){function n(){var t=this;r(this,n);var e=new o.a("sqlrequest");e.addExtension(new a.a),e.addExtension(new i.a),e.addExtension(new u.a),e.addExtension(new c.a),e.addExtension(new d.a),e.addExtension(new f.a),e.addExtension(new s.a),e.addExtension(new l.a),v(document).on("change",".js-db-tables-select",function(){return t.reloadDbTableColumns()}),v(document).on("click",".js-add-db-table-to-query-btn",function(n){return t.addDbTableToQuery(n)}),v(document).on("click",".js-add-db-table-column-to-query-btn",function(n){return t.addDbTableColumnToQuery(n)})}return b(n,[{key:"reloadDbTableColumns",value:function(){var n=v(".js-db-tables-select").find("option:selected"),t=v(".js-table-columns");v.ajax(n.data("table-columns-url")).then(function(n){v(".js-table-alert").addClass("d-none");var e=n.columns;t.removeClass("d-none"),t.find("tbody").empty(),e.forEach(function(n){var e=v("<tr>").append(v("<td>").html(n.name)).append(v("<td>").html(n.type)).append(v("<td>").addClass("text-right").append(v("<button>").addClass("btn btn-sm btn-outline-secondary js-add-db-table-column-to-query-btn").attr("data-column",n.name).html(t.data("action-btn"))));t.find("tbody").append(e)})})}},{key:"addDbTableToQuery",value:function(n){var t=v(".js-db-tables-select").find("option:selected");if(0===t.length)return void alert(v(n.target).data("choose-table-message"));this.addToQuery(t.val())}},{key:"addDbTableColumnToQuery",value:function(n){this.addToQuery(v(n.target).data("column"))}},{key:"addToQuery",value:function(n){var t=v("#form_request_sql_sql");t.val(t.val()+" "+n)}}]),n}();v(document).ready(function(){new h})},3:function(n,t,e){"use strict";(function(n){function e(n,t){if(!(n instanceof t))throw new TypeError("Cannot call a class as a function")}var r=function(){function n(n,t){for(var e=0;e<t.length;e++){var r=t[e];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(n,r.key,r)}}return function(t,e,r){return e&&n(t.prototype,e),r&&n(t,r),t}}(),o=n.$,a=function(){function n(t){e(this,n),this.selector=".ps-sortable-column",this.columns=o(t).find(this.selector)}return r(n,[{key:"attach",value:function(){var n=this;this.columns.on("click",function(t){var e=o(t.delegateTarget);n._sortByColumn(e,n._getToggledSortDirection(e))})}},{key:"sortBy",value:function(n,t){var e=this.columns.is('[data-sort-col-name="'+n+'"]');if(!e)throw new Error('Cannot sort by "'+n+'": invalid column');this._sortByColumn(e,t)}},{key:"_sortByColumn",value:function(n,t){window.location=this._getUrl(n.data("sortColName"),"desc"===t?"desc":"asc")}},{key:"_getToggledSortDirection",value:function(n){return"asc"===n.data("sortDirection")?"desc":"asc"}},{key:"_getUrl",value:function(n,t){var e=new URL(window.location.href),r=e.searchParams;return r.set("orderBy",n),r.set("sortOrder",t),e.toString()}}]),n}();t.a=a}).call(t,e(1))},4:function(n,t,e){"use strict";function r(n,t){if(!(n instanceof t))throw new TypeError("Cannot call a class as a function")}var o=function(){function n(n,t){for(var e=0;e<t.length;e++){var r=t[e];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(n,r.key,r)}}return function(t,e,r){return e&&n(t.prototype,e),r&&n(t,r),t}}(),a=window.$,i=function(){function n(t){r(this,n),this.id=t,this.$container=a("#"+this.id+"_grid")}return o(n,[{key:"getId",value:function(){return this.id}},{key:"getContainer",value:function(){return this.$container}},{key:"getHeaderContainer",value:function(){return this.$container.closest(".js-grid-panel").find(".js-grid-header")}},{key:"addExtension",value:function(n){n.extend(this)}}]),n}();t.a=i},449:function(n,t,e){n.exports=e(213)},5:function(n,t,e){"use strict";(function(n){/**
 * 2007-2018 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
var e=n.$,r=function(n,t){e.post(n).then(function(){return window.location.assign(t)})};t.a=r}).call(t,e(1))},6:function(n,t,e){"use strict";function r(n,t){if(!(n instanceof t))throw new TypeError("Cannot call a class as a function")}var o=function(){function n(n,t){for(var e=0;e<t.length;e++){var r=t[e];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(n,r.key,r)}}return function(t,e,r){return e&&n(t.prototype,e),r&&n(t,r),t}}(),a=window.$,i=function(){function n(){r(this,n)}return o(n,[{key:"extend",value:function(n){this._handleBulkActionCheckboxSelect(n),this._handleBulkActionSelectAllCheckbox(n)}},{key:"_handleBulkActionSelectAllCheckbox",value:function(n){var t=this;n.getContainer().on("change",".js-bulk-action-select-all",function(e){var r=a(e.currentTarget),o=r.is(":checked");o?t._enableBulkActionsBtn(n):t._disableBulkActionsBtn(n),n.getContainer().find(".js-bulk-action-checkbox").prop("checked",o)})}},{key:"_handleBulkActionCheckboxSelect",value:function(n){var t=this;n.getContainer().on("change",".js-bulk-action-checkbox",function(){n.getContainer().find(".js-bulk-action-checkbox:checked").length>0?t._enableBulkActionsBtn(n):t._disableBulkActionsBtn(n)})}},{key:"_enableBulkActionsBtn",value:function(n){n.getContainer().find(".js-bulk-actions-btn").prop("disabled",!1)}},{key:"_disableBulkActionsBtn",value:function(n){n.getContainer().find(".js-bulk-actions-btn").prop("disabled",!0)}}]),n}();t.a=i},7:function(n,t,e){"use strict";function r(n,t){if(!(n instanceof t))throw new TypeError("Cannot call a class as a function")}var o=function(){function n(n,t){for(var e=0;e<t.length;e++){var r=t[e];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(n,r.key,r)}}return function(t,e,r){return e&&n(t.prototype,e),r&&n(t,r),t}}(),a=window.$,i=function(){function n(){r(this,n)}return o(n,[{key:"extend",value:function(n){var t=this;n.getHeaderContainer().on("click",".js-common_show_query-grid-action",function(){return t._onShowSqlQueryClick(n)}),n.getHeaderContainer().on("click",".js-common_export_sql_manager-grid-action",function(){return t._onExportSqlManagerClick(n)})}},{key:"_onShowSqlQueryClick",value:function(n){var t=a("#"+n.getId()+"_common_show_query_modal_form");this._fillExportForm(t,n);var e=a("#"+n.getId()+"_grid_common_show_query_modal");e.modal("show"),e.on("click",".btn-sql-submit",function(){return t.submit()})}},{key:"_onExportSqlManagerClick",value:function(n){var t=a("#"+n.getId()+"_common_show_query_modal_form");this._fillExportForm(t,n),t.submit()}},{key:"_fillExportForm",value:function(n,t){var e=t.getContainer().find(".js-grid-table").data("query");n.find('textarea[name="sql"]').val(e),n.find('input[name="name"]').val(this._getNameFromBreadcrumb())}},{key:"_getNameFromBreadcrumb",value:function(){var n=a(".header-toolbar").find(".breadcrumb-item"),t="";return n.each(function(n,e){var r=a(e),o=0<r.find("a").length?r.find("a").text():r.text();0<t.length&&(t=t.concat(" > ")),t=t.concat(o)}),t}}]),n}();t.a=i},8:function(n,t,e){"use strict";function r(n,t){if(!(n instanceof t))throw new TypeError("Cannot call a class as a function")}var o=e(5),a=function(){function n(n,t){for(var e=0;e<t.length;e++){var r=t[e];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(n,r.key,r)}}return function(t,e,r){return e&&n(t.prototype,e),r&&n(t,r),t}}(),i=window.$,u=function(){function n(){r(this,n)}return a(n,[{key:"extend",value:function(n){n.getContainer().on("click",".js-reset-search",function(n){e.i(o.a)(i(n.currentTarget).data("url"),i(n.currentTarget).data("redirect"))})}}]),n}();t.a=u},9:function(n,t,e){"use strict";function r(n,t){if(!(n instanceof t))throw new TypeError("Cannot call a class as a function")}var o=function(){function n(n,t){for(var e=0;e<t.length;e++){var r=t[e];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(n,r.key,r)}}return function(t,e,r){return e&&n(t.prototype,e),r&&n(t,r),t}}(),a=function(){function n(){r(this,n)}return o(n,[{key:"extend",value:function(n){n.getHeaderContainer().on("click",".js-common_refresh_list-grid-action",function(){location.reload()})}}]),n}();t.a=a}});