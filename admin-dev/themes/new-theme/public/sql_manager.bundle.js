window.sql_manager=function(t){function e(o){if(n[o])return n[o].exports;var r=n[o]={i:o,l:!1,exports:{}};return t[o].call(r.exports,r,r.exports,e),r.l=!0,r.exports}var n={};return e.m=t,e.c=n,e.i=function(t){return t},e.d=function(t,n,o){e.o(t,n)||Object.defineProperty(t,n,{configurable:!1,enumerable:!0,get:o})},e.n=function(t){var n=t&&t.__esModule?function(){return t.default}:function(){return t};return e.d(n,"a",n),n},e.o=function(t,e){return Object.prototype.hasOwnProperty.call(t,e)},e.p="",e(e.s=523)}({0:function(t,e,n){"use strict";e.__esModule=!0,e.default=function(t,e){if(!(t instanceof e))throw new TypeError("Cannot call a class as a function")}},1:function(t,e,n){"use strict";e.__esModule=!0;var o=n(19),r=function(t){return t&&t.__esModule?t:{default:t}}(o);e.default=function(){function t(t,e){for(var n=0;n<e.length;n++){var o=e[n];o.enumerable=o.enumerable||!1,o.configurable=!0,"value"in o&&(o.writable=!0),(0,r.default)(t,o.key,o)}}return function(e,n,o){return n&&t(e.prototype,n),o&&t(e,o),e}}()},10:function(t,e,n){var o=n(6),r=n(12);t.exports=n(2)?function(t,e,n){return o.f(t,e,r(1,n))}:function(t,e,n){return t[e]=n,t}},11:function(t,e,n){var o=n(3);t.exports=function(t){if(!o(t))throw TypeError(t+" is not an object!");return t}},12:function(t,e){t.exports=function(t,e){return{enumerable:!(1&t),configurable:!(2&t),writable:!(4&t),value:e}}},14:function(t,e,n){var o=n(3);t.exports=function(t,e){if(!o(t))return t;var n,r;if(e&&"function"==typeof(n=t.toString)&&!o(r=n.call(t)))return r;if("function"==typeof(n=t.valueOf)&&!o(r=n.call(t)))return r;if(!e&&"function"==typeof(n=t.toString)&&!o(r=n.call(t)))return r;throw TypeError("Can't convert object to primitive value")}},15:function(t,e,n){var o=n(18);t.exports=function(t,e,n){if(o(t),void 0===e)return t;switch(n){case 1:return function(n){return t.call(e,n)};case 2:return function(n,o){return t.call(e,n,o)};case 3:return function(n,o,r){return t.call(e,n,o,r)}}return function(){return t.apply(e,arguments)}}},16:function(t,e,n){var o=n(3),r=n(5).document,i=o(r)&&o(r.createElement);t.exports=function(t){return i?r.createElement(t):{}}},17:function(t,e,n){t.exports=!n(2)&&!n(7)(function(){return 7!=Object.defineProperty(n(16)("div"),"a",{get:function(){return 7}}).a})},18:function(t,e){t.exports=function(t){if("function"!=typeof t)throw TypeError(t+" is not a function!");return t}},19:function(t,e,n){t.exports={default:n(20),__esModule:!0}},2:function(t,e,n){t.exports=!n(7)(function(){return 7!=Object.defineProperty({},"a",{get:function(){return 7}}).a})},20:function(t,e,n){n(21);var o=n(4).Object;t.exports=function(t,e,n){return o.defineProperty(t,e,n)}},21:function(t,e,n){var o=n(8);o(o.S+o.F*!n(2),"Object",{defineProperty:n(6).f})},22:function(t,e,n){"use strict";function o(t){return t&&t.__esModule?t:{default:t}}Object.defineProperty(e,"__esModule",{value:!0});var r=n(0),i=o(r),a=n(1),u=o(a),c=window.$,l=function(){function t(e){(0,i.default)(this,t),this.id=e,this.$container=c("#"+this.id+"_grid")}return(0,u.default)(t,[{key:"getId",value:function(){return this.id}},{key:"getContainer",value:function(){return this.$container}},{key:"getHeaderContainer",value:function(){return this.$container.closest(".js-grid-panel").find(".js-grid-header")}},{key:"addExtension",value:function(t){t.extend(this)}}]),t}();e.default=l},23:function(t,e,n){"use strict";function o(t){return t&&t.__esModule?t:{default:t}}Object.defineProperty(e,"__esModule",{value:!0});var r=n(0),i=o(r),a=n(1),u=o(a),c=n(41),l=o(c),d=window.$,s=function(){function t(){(0,i.default)(this,t)}return(0,u.default)(t,[{key:"extend",value:function(t){t.getContainer().on("click",".js-reset-search",function(t){(0,l.default)(d(t.currentTarget).data("url"),d(t.currentTarget).data("redirect"))})}}]),t}();e.default=s},24:function(t,e,n){"use strict";function o(t){return t&&t.__esModule?t:{default:t}}Object.defineProperty(e,"__esModule",{value:!0});var r=n(0),i=o(r),a=n(1),u=o(a),c=n(39),l=o(c),d=function(){function t(){(0,i.default)(this,t)}return(0,u.default)(t,[{key:"extend",value:function(t){var e=t.getContainer().find("table.table");new l.default(e).attach()}}]),t}();/**
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
e.default=d},26:function(t,e,n){"use strict";function o(t){return t&&t.__esModule?t:{default:t}}Object.defineProperty(e,"__esModule",{value:!0});var r=n(0),i=o(r),a=n(1),u=o(a),c=function(){function t(){(0,i.default)(this,t)}return(0,u.default)(t,[{key:"extend",value:function(t){t.getHeaderContainer().on("click",".js-common_refresh_list-grid-action",function(){location.reload()})}}]),t}();e.default=c},27:function(t,e,n){"use strict";function o(t){return t&&t.__esModule?t:{default:t}}Object.defineProperty(e,"__esModule",{value:!0});var r=n(0),i=o(r),a=n(1),u=o(a),c=window.$,l=function(){function t(){(0,i.default)(this,t)}return(0,u.default)(t,[{key:"extend",value:function(t){var e=this;t.getHeaderContainer().on("click",".js-common_show_query-grid-action",function(){return e._onShowSqlQueryClick(t)}),t.getHeaderContainer().on("click",".js-common_export_sql_manager-grid-action",function(){return e._onExportSqlManagerClick(t)})}},{key:"_onShowSqlQueryClick",value:function(t){var e=c("#"+t.getId()+"_common_show_query_modal_form");this._fillExportForm(e,t);var n=c("#"+t.getId()+"_grid_common_show_query_modal");n.modal("show"),n.on("click",".btn-sql-submit",function(){return e.submit()})}},{key:"_onExportSqlManagerClick",value:function(t){var e=c("#"+t.getId()+"_common_show_query_modal_form");this._fillExportForm(e,t),e.submit()}},{key:"_fillExportForm",value:function(t,e){var n=e.getContainer().find(".js-grid-table").data("query");t.find('textarea[name="sql"]').val(n),t.find('input[name="name"]').val(this._getNameFromBreadcrumb())}},{key:"_getNameFromBreadcrumb",value:function(){var t=c(".header-toolbar").find(".breadcrumb-item"),e="";return t.each(function(t,n){var o=c(n),r=0<o.find("a").length?o.find("a").text():o.text();0<e.length&&(e=e.concat(" > ")),e=e.concat(r)}),e}}]),t}();e.default=l},28:function(t,e,n){"use strict";function o(t){return t&&t.__esModule?t:{default:t}}Object.defineProperty(e,"__esModule",{value:!0});var r=n(0),i=o(r),a=n(1),u=o(a),c=function(){function t(){(0,i.default)(this,t)}return(0,u.default)(t,[{key:"extend",value:function(t){var e=t.getContainer().find(".column-filters");e.find(".grid-search-button").prop("disabled",!0),e.find("input, select").on("input dp.change",function(){e.find(".grid-search-button").prop("disabled",!1),e.find(".js-grid-reset-button").prop("hidden",!1)})}}]),t}();e.default=c},3:function(t,e){t.exports=function(t){return"object"==typeof t?null!==t:"function"==typeof t}},30:function(t,e,n){"use strict";function o(t){return t&&t.__esModule?t:{default:t}}Object.defineProperty(e,"__esModule",{value:!0});var r=n(0),i=o(r),a=n(1),u=o(a),c=window.$,l=function(){function t(){(0,i.default)(this,t)}return(0,u.default)(t,[{key:"extend",value:function(t){this._handleBulkActionCheckboxSelect(t),this._handleBulkActionSelectAllCheckbox(t)}},{key:"_handleBulkActionSelectAllCheckbox",value:function(t){var e=this;t.getContainer().on("change",".js-bulk-action-select-all",function(n){var o=c(n.currentTarget),r=o.is(":checked");r?e._enableBulkActionsBtn(t):e._disableBulkActionsBtn(t),t.getContainer().find(".js-bulk-action-checkbox").prop("checked",r)})}},{key:"_handleBulkActionCheckboxSelect",value:function(t){var e=this;t.getContainer().on("change",".js-bulk-action-checkbox",function(){t.getContainer().find(".js-bulk-action-checkbox:checked").length>0?e._enableBulkActionsBtn(t):e._disableBulkActionsBtn(t)})}},{key:"_enableBulkActionsBtn",value:function(t){t.getContainer().find(".js-bulk-actions-btn").prop("disabled",!1)}},{key:"_disableBulkActionsBtn",value:function(t){t.getContainer().find(".js-bulk-actions-btn").prop("disabled",!0)}}]),t}();e.default=l},31:function(t,e,n){"use strict";function o(t){return t&&t.__esModule?t:{default:t}}Object.defineProperty(e,"__esModule",{value:!0});var r=n(0),i=o(r),a=n(1),u=o(a),c=n(45),l=o(c),d=window.$,s=function(){function t(){var e=this;return(0,i.default)(this,t),{extend:function(t){return e.extend(t)}}}return(0,u.default)(t,[{key:"extend",value:function(t){var e=this;t.getContainer().on("click",".js-bulk-action-submit-btn",function(n){e.submit(n,t)})}},{key:"submit",value:function(t,e){var n=d(t.currentTarget),o=n.data("confirm-message"),r=n.data("confirmTitle");void 0!==o&&0<o.length?void 0!==r?this.showConfirmModal(n,e,o,r):confirm(o)&&this.postForm(n,e):this.postForm(n,e)}},{key:"showConfirmModal",value:function(t,e,n,o){var r=this,i=t.data("confirmButtonLabel"),a=t.data("closeButtonLabel"),u=t.data("confirmButtonClass");new l.default({id:e.getId()+"_grid_confirm_modal",confirmTitle:o,confirmMessage:n,confirmButtonLabel:i,closeButtonLabel:a,confirmButtonClass:u},function(){return r.postForm(t,e)}).show()}},{key:"postForm",value:function(t,e){var n=d("#"+e.getId()+"_filter_form");n.attr("action",t.data("form-url")),n.attr("method",t.data("form-method")),n.submit()}}]),t}();e.default=s},32:function(t,e,n){"use strict";function o(t){return t&&t.__esModule?t:{default:t}}Object.defineProperty(e,"__esModule",{value:!0});var r=n(0),i=o(r),a=n(1),u=o(a),c=window.$,l=function(){function t(){(0,i.default)(this,t)}return(0,u.default)(t,[{key:"extend",value:function(t){this.initRowLinks(t),this.initConfirmableActions(t)}},{key:"initConfirmableActions",value:function(t){t.getContainer().on("click",".js-link-row-action",function(t){var e=c(t.currentTarget).data("confirm-message");e.length&&!confirm(e)&&t.preventDefault()})}},{key:"initRowLinks",value:function(t){c("tr",t.getContainer()).each(function(){var t=c(this);c(".js-link-row-action[data-clickable-row=1]:first",t).each(function(){var e=c(this),n=e.closest("td");c("td.clickable",t).not(n).addClass("cursor-pointer").click(function(){var t=e.data("confirm-message");t.length&&!confirm(t)||(document.location=e.attr("href"))})})})}}]),t}();e.default=l},39:function(t,e,n){"use strict";(function(t){function o(t){return t&&t.__esModule?t:{default:t}}Object.defineProperty(e,"__esModule",{value:!0});var r=n(0),i=o(r),a=n(1),u=o(a),c=t.$,l=function(){function t(e){(0,i.default)(this,t),this.selector=".ps-sortable-column",this.columns=c(e).find(this.selector)}return(0,u.default)(t,[{key:"attach",value:function(){var t=this;this.columns.on("click",function(e){var n=c(e.delegateTarget);t._sortByColumn(n,t._getToggledSortDirection(n))})}},{key:"sortBy",value:function(t,e){var n=this.columns.is('[data-sort-col-name="'+t+'"]');if(!n)throw new Error('Cannot sort by "'+t+'": invalid column');this._sortByColumn(n,e)}},{key:"_sortByColumn",value:function(t,e){window.location=this._getUrl(t.data("sortColName"),"desc"===e?"desc":"asc",t.data("sortPrefix"))}},{key:"_getToggledSortDirection",value:function(t){return"asc"===t.data("sortDirection")?"desc":"asc"}},{key:"_getUrl",value:function(t,e,n){var o=new URL(window.location.href),r=o.searchParams;return n?(r.set(n+"[orderBy]",t),r.set(n+"[sortOrder]",e)):(r.set("orderBy",t),r.set("sortOrder",e)),o.toString()}}]),t}();e.default=l}).call(e,n(9))},4:function(t,e){var n=t.exports={version:"2.4.0"};"number"==typeof __e&&(__e=n)},41:function(t,e,n){"use strict";(function(t){Object.defineProperty(e,"__esModule",{value:!0});/**
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
var n=t.$,o=function(t,e){n.post(t).then(function(){return window.location.assign(e)})};e.default=o}).call(e,n(9))},45:function(t,e,n){"use strict";function o(t,e){var n=this,o=t.id,a=t.closable;this.modal=r(t),this.$modal=i(this.modal.container),this.show=function(){n.$modal.modal()},this.modal.confirmButton.addEventListener("click",e),this.$modal.modal({backdrop:!!a||"static",keyboard:void 0===a||a,closable:void 0===a||a,show:!1}),this.$modal.on("hidden.bs.modal",function(){document.querySelector("#"+o).remove()}),document.body.appendChild(this.modal.container)}function r(t){var e=t.id,n=void 0===e?"confirm_modal":e,o=t.confirmTitle,r=t.confirmMessage,i=void 0===r?"":r,a=t.closeButtonLabel,u=void 0===a?"Close":a,c=t.confirmButtonLabel,l=void 0===c?"Accept":c,d=t.confirmButtonClass,s=void 0===d?"btn-primary":d,f={};return f.container=document.createElement("div"),f.container.classList.add("modal","fade"),f.container.id=n,f.dialog=document.createElement("div"),f.dialog.classList.add("modal-dialog"),f.content=document.createElement("div"),f.content.classList.add("modal-content"),f.header=document.createElement("div"),f.header.classList.add("modal-header"),o&&(f.title=document.createElement("h4"),f.title.classList.add("modal-title"),f.title.innerHTML=o),f.closeIcon=document.createElement("button"),f.closeIcon.classList.add("close"),f.closeIcon.setAttribute("type","button"),f.closeIcon.dataset.dismiss="modal",f.closeIcon.innerHTML="×",f.body=document.createElement("div"),f.body.classList.add("modal-body","text-left","font-weight-normal"),f.message=document.createElement("p"),f.message.classList.add("confirm-message"),f.message.innerHTML=i,f.footer=document.createElement("div"),f.footer.classList.add("modal-footer"),f.closeButton=document.createElement("button"),f.closeButton.setAttribute("type","button"),f.closeButton.classList.add("btn","btn-outline-secondary","btn-lg"),f.closeButton.dataset.dismiss="modal",f.closeButton.innerHTML=u,f.confirmButton=document.createElement("button"),f.confirmButton.setAttribute("type","button"),f.confirmButton.classList.add("btn",s,"btn-lg","btn-confirm-submit"),f.confirmButton.dataset.dismiss="modal",f.confirmButton.innerHTML=l,o?f.header.append(f.title,f.closeIcon):f.header.appendChild(f.closeIcon),f.body.appendChild(f.message),f.footer.append(f.closeButton,f.confirmButton),f.content.append(f.header,f.body,f.footer),f.dialog.appendChild(f.content),f.container.appendChild(f.dialog),f}Object.defineProperty(e,"__esModule",{value:!0}),e.default=o;/**
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
var i=window.$},5:function(t,e){var n=t.exports="undefined"!=typeof window&&window.Math==Math?window:"undefined"!=typeof self&&self.Math==Math?self:Function("return this")();"number"==typeof __g&&(__g=n)},523:function(t,e,n){"use strict";function o(t){return t&&t.__esModule?t:{default:t}}var r=n(0),i=o(r),a=n(1),u=o(a),c=n(22),l=o(c),d=n(26),s=o(d),f=n(27),m=o(f),b=n(23),v=o(b),h=n(24),p=o(h),y=n(30),_=o(y),g=n(31),k=o(g),w=n(62),x=o(w),C=n(32),j=o(C),B=n(28),M=o(B),E=window.$,T=function(){function t(){var e=this;(0,i.default)(this,t);var n=new l.default("sql_request");n.addExtension(new s.default),n.addExtension(new m.default),n.addExtension(new v.default),n.addExtension(new p.default),n.addExtension(new j.default),n.addExtension(new x.default),n.addExtension(new k.default),n.addExtension(new _.default),n.addExtension(new M.default),E(document).on("change",".js-db-tables-select",function(){return e.reloadDbTableColumns()}),E(document).on("click",".js-add-db-table-to-query-btn",function(t){return e.addDbTableToQuery(t)}),E(document).on("click",".js-add-db-table-column-to-query-btn",function(t){return e.addDbTableColumnToQuery(t)})}return(0,u.default)(t,[{key:"reloadDbTableColumns",value:function(){var t=E(".js-db-tables-select").find("option:selected"),e=E(".js-table-columns");E.ajax(t.data("table-columns-url")).then(function(t){E(".js-table-alert").addClass("d-none");var n=t.columns;e.removeClass("d-none"),e.find("tbody").empty(),n.forEach(function(t){var n=E("<tr>").append(E("<td>").html(t.name)).append(E("<td>").html(t.type)).append(E("<td>").addClass("text-right").append(E("<button>").addClass("btn btn-sm btn-outline-secondary js-add-db-table-column-to-query-btn").attr("data-column",t.name).html(e.data("action-btn"))));e.find("tbody").append(n)})})}},{key:"addDbTableToQuery",value:function(t){var e=E(".js-db-tables-select").find("option:selected");if(0===e.length)return void alert(E(t.target).data("choose-table-message"));this.addToQuery(e.val())}},{key:"addDbTableColumnToQuery",value:function(t){this.addToQuery(E(t.target).data("column"))}},{key:"addToQuery",value:function(t){var e=E("#sql_request_sql");e.val(e.val()+" "+t)}}]),t}();E(document).ready(function(){new T})},6:function(t,e,n){var o=n(11),r=n(17),i=n(14),a=Object.defineProperty;e.f=n(2)?Object.defineProperty:function(t,e,n){if(o(t),e=i(e,!0),o(n),r)try{return a(t,e,n)}catch(t){}if("get"in n||"set"in n)throw TypeError("Accessors not supported!");return"value"in n&&(t[e]=n.value),t}},62:function(t,e,n){"use strict";function o(t){return t&&t.__esModule?t:{default:t}}Object.defineProperty(e,"__esModule",{value:!0});var r=n(0),i=o(r),a=n(1),u=o(a),c=window.$,l=function(){function t(){var e=this;return(0,i.default)(this,t),{extend:function(t){return e.extend(t)}}}return(0,u.default)(t,[{key:"extend",value:function(t){var e=this;t.getHeaderContainer().on("click",".js-grid-action-submit-btn",function(n){e.handleSubmit(n,t)})}},{key:"handleSubmit",value:function(t,e){var n=c(t.currentTarget),o=n.data("confirm-message");if(!(void 0!==o&&0<o.length)||confirm(o)){var r=c("#"+e.getId()+"_filter_form");r.attr("action",n.data("url")),r.attr("method",n.data("method")),r.find('input[name="'+e.getId()+'[_token]"]').val(n.data("csrf")),r.submit()}}}]),t}();e.default=l},7:function(t,e){t.exports=function(t){try{return!!t()}catch(t){return!0}}},8:function(t,e,n){var o=n(5),r=n(4),i=n(15),a=n(10),u=function(t,e,n){var c,l,d,s=t&u.F,f=t&u.G,m=t&u.S,b=t&u.P,v=t&u.B,h=t&u.W,p=f?r:r[e]||(r[e]={}),y=p.prototype,_=f?o:m?o[e]:(o[e]||{}).prototype;f&&(n=e);for(c in n)(l=!s&&_&&void 0!==_[c])&&c in p||(d=l?_[c]:n[c],p[c]=f&&"function"!=typeof _[c]?n[c]:v&&l?i(d,o):h&&_[c]==d?function(t){var e=function(e,n,o){if(this instanceof t){switch(arguments.length){case 0:return new t;case 1:return new t(e);case 2:return new t(e,n)}return new t(e,n,o)}return t.apply(this,arguments)};return e.prototype=t.prototype,e}(d):b&&"function"==typeof d?i(Function.call,d):d,b&&((p.virtual||(p.virtual={}))[c]=d,t&u.R&&y&&!y[c]&&a(y,c,d)))};u.F=1,u.G=2,u.S=4,u.P=8,u.B=16,u.W=32,u.U=64,u.R=128,t.exports=u},9:function(t,e){var n;n=function(){return this}();try{n=n||Function("return this")()||(0,eval)("this")}catch(t){"object"==typeof window&&(n=window)}t.exports=n}});