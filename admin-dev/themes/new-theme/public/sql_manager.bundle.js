window.sql_manager=function(n){function e(r){if(t[r])return t[r].exports;var o=t[r]={i:r,l:!1,exports:{}};return n[r].call(o.exports,o,o.exports,e),o.l=!0,o.exports}var t={};return e.m=n,e.c=t,e.i=function(n){return n},e.d=function(n,t,r){e.o(n,t)||Object.defineProperty(n,t,{configurable:!1,enumerable:!0,get:r})},e.n=function(n){var t=n&&n.__esModule?function(){return n.default}:function(){return n};return e.d(t,"a",t),t},e.o=function(n,e){return Object.prototype.hasOwnProperty.call(n,e)},e.p="",e(e.s=397)}({0:function(n,e){var t;t=function(){return this}();try{t=t||Function("return this")()||(0,eval)("this")}catch(n){"object"==typeof window&&(t=window)}n.exports=t},10:function(n,e,t){"use strict";function r(n,e){if(!(n instanceof e))throw new TypeError("Cannot call a class as a function")}Object.defineProperty(e,"__esModule",{value:!0});var o=function(){function n(n,e){for(var t=0;t<e.length;t++){var r=e[t];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(n,r.key,r)}}return function(e,t,r){return t&&n(e.prototype,t),r&&n(e,r),e}}(),a=window,i=a.$,u=function(){function n(){r(this,n)}return o(n,[{key:"extend",value:function(n){this.initRowLinks(n),this.initConfirmableActions(n)}},{key:"initConfirmableActions",value:function(n){n.getContainer().on("click",".js-link-row-action",function(n){var e=i(n.currentTarget).data("confirm-message");e.length&&!window.confirm(e)&&n.preventDefault()})}},{key:"initRowLinks",value:function(n){i("tr",n.getContainer()).each(function(){var n=i(this);i(".js-link-row-action[data-clickable-row=1]:first",n).each(function(){var e=i(this),t=e.closest("td");i("td.clickable",n).not(t).addClass("cursor-pointer").click(function(){var n=e.data("confirm-message");n.length&&!window.confirm(n)||(document.location=e.attr("href"))})})})}}]),n}();e.default=u},13:function(n,e,t){"use strict";(function(n){function t(n,e){if(!(n instanceof e))throw new TypeError("Cannot call a class as a function")}Object.defineProperty(e,"__esModule",{value:!0});var r=function(){function n(n,e){for(var t=0;t<e.length;t++){var r=e[t];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(n,r.key,r)}}return function(e,t,r){return t&&n(e.prototype,t),r&&n(e,r),e}}(),o=n,a=o.$,i=function(){function n(e){t(this,n),this.selector=".ps-sortable-column",this.columns=a(e).find(this.selector)}return r(n,[{key:"attach",value:function(){var n=this;this.columns.on("click",function(e){var t=a(e.delegateTarget);n.sortByColumn(t,n.getToggledSortDirection(t))})}},{key:"sortBy",value:function(n,e){var t=this.columns.is('[data-sort-col-name="'+n+'"]');if(!t)throw new Error('Cannot sort by "'+n+'": invalid column');this.sortByColumn(t,e)}},{key:"sortByColumn",value:function(n,e){window.location=this.getUrl(n.data("sortColName"),"desc"===e?"desc":"asc",n.data("sortPrefix"))}},{key:"getToggledSortDirection",value:function(n){return"asc"===n.data("sortDirection")?"desc":"asc"}},{key:"getUrl",value:function(n,e,t){var r=new URL(window.location.href),o=r.searchParams;return t?(o.set(t+"[orderBy]",n),o.set(t+"[sortOrder]",e)):(o.set("orderBy",n),o.set("sortOrder",e)),r.toString()}}]),n}();e.default=i}).call(e,t(0))},15:function(n,e,t){"use strict";(function(n){Object.defineProperty(e,"__esModule",{value:!0});/**
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
var t=n,r=t.$,o=function(n,e){r.post(n).then(function(){return window.location.assign(e)})};e.default=o}).call(e,t(0))},2:function(n,e,t){"use strict";function r(n,e){if(!(n instanceof e))throw new TypeError("Cannot call a class as a function")}Object.defineProperty(e,"__esModule",{value:!0});var o=function(){function n(n,e){for(var t=0;t<e.length;t++){var r=e[t];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(n,r.key,r)}}return function(e,t,r){return t&&n(e.prototype,t),r&&n(e,r),e}}(),a=window,i=a.$,u=function(){function n(e){r(this,n),this.id=e,this.$container=i("#"+this.id+"_grid")}return o(n,[{key:"getId",value:function(){return this.id}},{key:"getContainer",value:function(){return this.$container}},{key:"getHeaderContainer",value:function(){return this.$container.closest(".js-grid-panel").find(".js-grid-header")}},{key:"addExtension",value:function(n){n.extend(this)}}]),n}();e.default=u},21:function(n,e,t){"use strict";function r(n,e){if(!(n instanceof e))throw new TypeError("Cannot call a class as a function")}Object.defineProperty(e,"__esModule",{value:!0});var o=function(){function n(n,e){for(var t=0;t<e.length;t++){var r=e[t];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(n,r.key,r)}}return function(e,t,r){return t&&n(e.prototype,t),r&&n(e,r),e}}(),a=window,i=a.$,u=function(){function n(){var e=this;return r(this,n),{extend:function(n){return e.extend(n)}}}return o(n,[{key:"extend",value:function(n){var e=this;n.getHeaderContainer().on("click",".js-grid-action-submit-btn",function(t){e.handleSubmit(t,n)})}},{key:"handleSubmit",value:function(n,e){var t=i(n.currentTarget),r=t.data("confirm-message");if(!(void 0!==r&&r.length>0)||window.confirm(r)){var o=i("#"+e.getId()+"_filter_form");o.attr("action",t.data("url")),o.attr("method",t.data("method")),o.find('input[name="'+e.getId()+'[_token]"]').val(t.data("csrf")),o.submit()}}}]),n}();e.default=u},3:function(n,e,t){"use strict";function r(n,e){if(!(n instanceof e))throw new TypeError("Cannot call a class as a function")}Object.defineProperty(e,"__esModule",{value:!0});var o=function(){function n(n,e){for(var t=0;t<e.length;t++){var r=e[t];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(n,r.key,r)}}return function(e,t,r){return t&&n(e.prototype,t),r&&n(e,r),e}}(),a=t(15),i=function(n){return n&&n.__esModule?n:{default:n}}(a),u=window,c=u.$,l=function(){function n(){r(this,n)}return o(n,[{key:"extend",value:function(n){n.getContainer().on("click",".js-reset-search",function(n){(0,i.default)(c(n.currentTarget).data("url"),c(n.currentTarget).data("redirect"))})}}]),n}();e.default=l},397:function(n,e,t){"use strict";function r(n){return n&&n.__esModule?n:{default:n}}function o(n,e){if(!(n instanceof e))throw new TypeError("Cannot call a class as a function")}var a=function(){function n(n,e){for(var t=0;t<e.length;t++){var r=e[t];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(n,r.key,r)}}return function(e,t,r){return t&&n(e.prototype,t),r&&n(e,r),e}}(),i=t(2),u=r(i),c=t(5),l=r(c),f=t(6),s=r(f),d=t(3),b=r(d),v=t(4),h=r(v),m=t(8),y=r(m),p=t(9),g=r(p),w=t(21),k=r(w),_=t(10),C=r(_),j=t(7),x=r(j),T=window,E=T.$,O=function(){function n(){var e=this;o(this,n);var t=new u.default("sql_request");t.addExtension(new l.default),t.addExtension(new s.default),t.addExtension(new b.default),t.addExtension(new h.default),t.addExtension(new C.default),t.addExtension(new k.default),t.addExtension(new g.default),t.addExtension(new y.default),t.addExtension(new x.default),E(document).on("change",".js-db-tables-select",function(){return e.reloadDbTableColumns()}),E(document).on("click",".js-add-db-table-to-query-btn",function(n){return e.addDbTableToQuery(n)}),E(document).on("click",".js-add-db-table-column-to-query-btn",function(n){return e.addDbTableColumnToQuery(n)})}return a(n,[{key:"reloadDbTableColumns",value:function(){var n=E(".js-db-tables-select").find("option:selected"),e=E(".js-table-columns");E.ajax(n.data("table-columns-url")).then(function(n){E(".js-table-alert").addClass("d-none");var t=n.columns;e.removeClass("d-none"),e.find("tbody").empty(),t.forEach(function(n){var t=E("<tr>").append(E("<td>").html(n.name)).append(E("<td>").html(n.type)).append(E("<td>").addClass("text-right").append(E("<button>").addClass("btn btn-sm btn-outline-secondary js-add-db-table-column-to-query-btn").attr("data-column",n.name).html(e.data("action-btn"))));e.find("tbody").append(t)})})}},{key:"addDbTableToQuery",value:function(n){var e=E(".js-db-tables-select").find("option:selected");if(0===e.length)return void alert(E(n.target).data("choose-table-message"));this.addToQuery(e.val())}},{key:"addDbTableColumnToQuery",value:function(n){this.addToQuery(E(n.target).data("column"))}},{key:"addToQuery",value:function(n){var e=E("#sql_request_sql");e.val(e.val()+" "+n)}}]),n}();E(document).ready(function(){new O})},4:function(n,e,t){"use strict";function r(n,e){if(!(n instanceof e))throw new TypeError("Cannot call a class as a function")}Object.defineProperty(e,"__esModule",{value:!0});var o=function(){function n(n,e){for(var t=0;t<e.length;t++){var r=e[t];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(n,r.key,r)}}return function(e,t,r){return t&&n(e.prototype,t),r&&n(e,r),e}}(),a=t(13),i=function(n){return n&&n.__esModule?n:{default:n}}(a),u=function(){function n(){r(this,n)}return o(n,[{key:"extend",value:function(n){var e=n.getContainer().find("table.table");new i.default(e).attach()}}]),n}();e.default=u},5:function(n,e,t){"use strict";function r(n,e){if(!(n instanceof e))throw new TypeError("Cannot call a class as a function")}Object.defineProperty(e,"__esModule",{value:!0});var o=function(){function n(n,e){for(var t=0;t<e.length;t++){var r=e[t];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(n,r.key,r)}}return function(e,t,r){return t&&n(e.prototype,t),r&&n(e,r),e}}(),a=function(){function n(){r(this,n)}return o(n,[{key:"extend",value:function(n){n.getHeaderContainer().on("click",".js-common_refresh_list-grid-action",function(){window.location.reload()})}}]),n}();e.default=a},6:function(n,e,t){"use strict";function r(n,e){if(!(n instanceof e))throw new TypeError("Cannot call a class as a function")}Object.defineProperty(e,"__esModule",{value:!0});var o=function(){function n(n,e){for(var t=0;t<e.length;t++){var r=e[t];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(n,r.key,r)}}return function(e,t,r){return t&&n(e.prototype,t),r&&n(e,r),e}}(),a=window,i=a.$,u=function(){function n(){r(this,n)}return o(n,[{key:"extend",value:function(n){var e=this;n.getHeaderContainer().on("click",".js-common_show_query-grid-action",function(){return e.onShowSqlQueryClick(n)}),n.getHeaderContainer().on("click",".js-common_export_sql_manager-grid-action",function(){return e.onExportSqlManagerClick(n)})}},{key:"onShowSqlQueryClick",value:function(n){var e=i("#"+n.getId()+"_common_show_query_modal_form");this.fillExportForm(e,n);var t=i("#"+n.getId()+"_grid_common_show_query_modal");t.modal("show"),t.on("click",".btn-sql-submit",function(){return e.submit()})}},{key:"onExportSqlManagerClick",value:function(n){var e=i("#"+n.getId()+"_common_show_query_modal_form");this.fillExportForm(e,n),e.submit()}},{key:"fillExportForm",value:function(n,e){var t=e.getContainer().find(".js-grid-table").data("query");n.find('textarea[name="sql"]').val(t),n.find('input[name="name"]').val(this.getNameFromBreadcrumb())}},{key:"getNameFromBreadcrumb",value:function(){var n=i(".header-toolbar").find(".breadcrumb-item"),e="";return n.each(function(n,t){var r=i(t),o=r.find("a").length>0?r.find("a").text():r.text();e.length>0&&(e=e.concat(" > ")),e=e.concat(o)}),e}}]),n}();e.default=u},7:function(n,e,t){"use strict";function r(n,e){if(!(n instanceof e))throw new TypeError("Cannot call a class as a function")}Object.defineProperty(e,"__esModule",{value:!0});var o=function(){function n(n,e){for(var t=0;t<e.length;t++){var r=e[t];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(n,r.key,r)}}return function(e,t,r){return t&&n(e.prototype,t),r&&n(e,r),e}}(),a=function(){function n(){r(this,n)}return o(n,[{key:"extend",value:function(n){var e=n.getContainer().find(".column-filters");e.find(".grid-search-button").prop("disabled",!0),e.find("input, select").on("input dp.change",function(){e.find(".grid-search-button").prop("disabled",!1),e.find(".js-grid-reset-button").prop("hidden",!1)})}}]),n}();e.default=a},8:function(n,e,t){"use strict";function r(n,e){if(!(n instanceof e))throw new TypeError("Cannot call a class as a function")}Object.defineProperty(e,"__esModule",{value:!0});var o=function(){function n(n,e){for(var t=0;t<e.length;t++){var r=e[t];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(n,r.key,r)}}return function(e,t,r){return t&&n(e.prototype,t),r&&n(e,r),e}}(),a=window,i=a.$,u=function(){function n(){r(this,n)}return o(n,[{key:"extend",value:function(n){this.handleBulkActionCheckboxSelect(n),this.handleBulkActionSelectAllCheckbox(n)}},{key:"handleBulkActionSelectAllCheckbox",value:function(n){var e=this;n.getContainer().on("change",".js-bulk-action-select-all",function(t){var r=i(t.currentTarget),o=r.is(":checked");o?e.enableBulkActionsBtn(n):e.disableBulkActionsBtn(n),n.getContainer().find(".js-bulk-action-checkbox").prop("checked",o)})}},{key:"handleBulkActionCheckboxSelect",value:function(n){var e=this;n.getContainer().on("change",".js-bulk-action-checkbox",function(){n.getContainer().find(".js-bulk-action-checkbox:checked").length>0?e.enableBulkActionsBtn(n):e.disableBulkActionsBtn(n)})}},{key:"enableBulkActionsBtn",value:function(n){n.getContainer().find(".js-bulk-actions-btn").prop("disabled",!1)}},{key:"disableBulkActionsBtn",value:function(n){n.getContainer().find(".js-bulk-actions-btn").prop("disabled",!0)}}]),n}();e.default=u},9:function(n,e,t){"use strict";function r(n,e){if(!(n instanceof e))throw new TypeError("Cannot call a class as a function")}Object.defineProperty(e,"__esModule",{value:!0});var o=function(){function n(n,e){for(var t=0;t<e.length;t++){var r=e[t];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(n,r.key,r)}}return function(e,t,r){return t&&n(e.prototype,t),r&&n(e,r),e}}(),a=window,i=a.$,u=function(){function n(){var e=this;return r(this,n),{extend:function(n){return e.extend(n)}}}return o(n,[{key:"extend",value:function(n){var e=this;n.getContainer().on("click",".js-bulk-action-submit-btn",function(t){e.submit(t,n)})}},{key:"submit",value:function(n,e){var t=i(n.currentTarget),r=t.data("confirm-message");if(!(void 0!==r&&r.length>0)||window.confirm(r)){var o=i("#"+e.getId()+"_filter_form");o.attr("action",t.data("form-url")),o.attr("method",t.data("form-method")),o.submit()}}}]),n}();e.default=u}});