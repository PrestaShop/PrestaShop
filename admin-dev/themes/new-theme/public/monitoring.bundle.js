window.monitoring=function(e){function n(r){if(t[r])return t[r].exports;var o=t[r]={i:r,l:!1,exports:{}};return e[r].call(o.exports,o,o.exports,n),o.l=!0,o.exports}var t={};return n.m=e,n.c=t,n.i=function(e){return e},n.d=function(e,t,r){n.o(e,t)||Object.defineProperty(e,t,{configurable:!1,enumerable:!0,get:r})},n.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return n.d(t,"a",t),t},n.o=function(e,n){return Object.prototype.hasOwnProperty.call(e,n)},n.p="",n(n.s=379)}({0:function(e,n){var t;t=function(){return this}();try{t=t||Function("return this")()||(0,eval)("this")}catch(e){"object"==typeof window&&(t=window)}e.exports=t},10:function(e,n,t){"use strict";function r(e,n){if(!(e instanceof n))throw new TypeError("Cannot call a class as a function")}Object.defineProperty(n,"__esModule",{value:!0});var o=function(){function e(e,n){for(var t=0;t<n.length;t++){var r=n[t];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(e,r.key,r)}}return function(n,t,r){return t&&e(n.prototype,t),r&&e(n,r),n}}(),i=window,a=i.$,u=function(){function e(){r(this,e)}return o(e,[{key:"extend",value:function(e){this.initRowLinks(e),this.initConfirmableActions(e)}},{key:"initConfirmableActions",value:function(e){e.getContainer().on("click",".js-link-row-action",function(e){var n=a(e.currentTarget).data("confirm-message");n.length&&!window.confirm(n)&&e.preventDefault()})}},{key:"initRowLinks",value:function(e){a("tr",e.getContainer()).each(function(){var e=a(this);a(".js-link-row-action[data-clickable-row=1]:first",e).each(function(){var n=a(this),t=n.closest("td");a("td.clickable",e).not(t).addClass("cursor-pointer").click(function(){var e=n.data("confirm-message");e.length&&!window.confirm(e)||(document.location=n.attr("href"))})})})}}]),e}();n.default=u},11:function(e,n,t){"use strict";function r(e,n){if(!(e instanceof n))throw new TypeError("Cannot call a class as a function")}Object.defineProperty(n,"__esModule",{value:!0});var o=function(){function e(e,n){for(var t=0;t<n.length;t++){var r=n[t];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(e,r.key,r)}}return function(n,t,r){return t&&e(n.prototype,t),r&&e(n,r),n}}(),i=window,a=i.$,u=function(){function e(){r(this,e)}return o(e,[{key:"extend",value:function(e){e.getContainer().on("click",".js-submit-row-action",function(e){e.preventDefault();var n=a(e.currentTarget),t=n.data("confirm-message");if(!t.length||window.confirm(t)){var r=n.data("method"),o=["GET","POST"].includes(r),i=a("<form>",{action:n.data("url"),method:o?r:"POST"}).appendTo("body");o||i.append(a("<input>",{type:"_hidden",name:"_method",value:r})),i.submit()}})}}]),e}();n.default=u},13:function(e,n,t){"use strict";(function(e){function t(e,n){if(!(e instanceof n))throw new TypeError("Cannot call a class as a function")}Object.defineProperty(n,"__esModule",{value:!0});var r=function(){function e(e,n){for(var t=0;t<n.length;t++){var r=n[t];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(e,r.key,r)}}return function(n,t,r){return t&&e(n.prototype,t),r&&e(n,r),n}}(),o=e,i=o.$,a=function(){function e(n){t(this,e),this.selector=".ps-sortable-column",this.columns=i(n).find(this.selector)}return r(e,[{key:"attach",value:function(){var e=this;this.columns.on("click",function(n){var t=i(n.delegateTarget);e.sortByColumn(t,e.getToggledSortDirection(t))})}},{key:"sortBy",value:function(e,n){var t=this.columns.is('[data-sort-col-name="'+e+'"]');if(!t)throw new Error('Cannot sort by "'+e+'": invalid column');this.sortByColumn(t,n)}},{key:"sortByColumn",value:function(e,n){window.location=this.getUrl(e.data("sortColName"),"desc"===n?"desc":"asc",e.data("sortPrefix"))}},{key:"getToggledSortDirection",value:function(e){return"asc"===e.data("sortDirection")?"desc":"asc"}},{key:"getUrl",value:function(e,n,t){var r=new URL(window.location.href),o=r.searchParams;return t?(o.set(t+"[orderBy]",e),o.set(t+"[sortOrder]",n)):(o.set("orderBy",e),o.set("sortOrder",n)),r.toString()}}]),e}();n.default=a}).call(n,t(0))},15:function(e,n,t){"use strict";(function(e){Object.defineProperty(n,"__esModule",{value:!0});/**
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
var t=e,r=t.$,o=function(e,n){r.post(e).then(function(){return window.location.assign(n)})};n.default=o}).call(n,t(0))},2:function(e,n,t){"use strict";function r(e,n){if(!(e instanceof n))throw new TypeError("Cannot call a class as a function")}Object.defineProperty(n,"__esModule",{value:!0});var o=function(){function e(e,n){for(var t=0;t<n.length;t++){var r=n[t];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(e,r.key,r)}}return function(n,t,r){return t&&e(n.prototype,t),r&&e(n,r),n}}(),i=window,a=i.$,u=function(){function e(n){r(this,e),this.id=n,this.$container=a("#"+this.id+"_grid")}return o(e,[{key:"getId",value:function(){return this.id}},{key:"getContainer",value:function(){return this.$container}},{key:"getHeaderContainer",value:function(){return this.$container.closest(".js-grid-panel").find(".js-grid-header")}},{key:"addExtension",value:function(e){e.extend(this)}}]),e}();n.default=u},25:function(e,n,t){"use strict";function r(e,n){if(!(e instanceof n))throw new TypeError("Cannot call a class as a function")}Object.defineProperty(n,"__esModule",{value:!0});var o=function(){function e(e,n){for(var t=0;t<n.length;t++){var r=n[t];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(e,r.key,r)}}return function(n,t,r){return t&&e(n.prototype,t),r&&e(n,r),n}}(),i=window,a=i.$,u=function(){function e(){r(this,e)}return o(e,[{key:"extend",value:function(e){var n=e.getContainer();n.on("click",".js-remove-helper-block",function(e){n.remove();var t=a(e.target),r=t.data("closeUrl"),o=t.data("cardName");r&&a.post(r,{close:1,name:o})})}}]),e}();n.default=u},26:function(e,n,t){"use strict";function r(e,n){if(!(e instanceof n))throw new TypeError("Cannot call a class as a function")}Object.defineProperty(n,"__esModule",{value:!0});var o=function(){function e(e,n){for(var t=0;t<n.length;t++){var r=n[t];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(e,r.key,r)}}return function(n,t,r){return t&&e(n.prototype,t),r&&e(n,r),n}}(),i=window,a=i.$,u=function(){function e(n){r(this,e),this.id=n,this.$container=a("#"+this.id)}return o(e,[{key:"getContainer",value:function(){return this.$container}},{key:"addExtension",value:function(e){e.extend(this)}}]),e}();n.default=u},3:function(e,n,t){"use strict";function r(e,n){if(!(e instanceof n))throw new TypeError("Cannot call a class as a function")}Object.defineProperty(n,"__esModule",{value:!0});var o=function(){function e(e,n){for(var t=0;t<n.length;t++){var r=n[t];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(e,r.key,r)}}return function(n,t,r){return t&&e(n.prototype,t),r&&e(n,r),n}}(),i=t(15),a=function(e){return e&&e.__esModule?e:{default:e}}(i),u=window,c=u.$,l=function(){function e(){r(this,e)}return o(e,[{key:"extend",value:function(e){e.getContainer().on("click",".js-reset-search",function(e){(0,a.default)(c(e.currentTarget).data("url"),c(e.currentTarget).data("redirect"))})}}]),e}();n.default=l},379:function(e,n,t){"use strict";function r(e){return e&&e.__esModule?e:{default:e}}var o=t(2),i=r(o),a=t(3),u=r(a),c=t(4),l=r(c),f=t(11),s=r(f),d=t(10),v=r(d),g=t(69),w=r(g),p=t(70),h=r(p),y=t(7),b=r(y),m=t(5),_=r(m),k=t(6),x=r(k),j=t(26),C=r(j),E=t(25),O=r(E);(0,window.$)(function(){var e=new i.default("empty_category");e.addExtension(new u.default),e.addExtension(new l.default),e.addExtension(new _.default),e.addExtension(new s.default),e.addExtension(new v.default),e.addExtension(new h.default),e.addExtension(new w.default),e.addExtension(new b.default),["no_qty_product_with_combination","no_qty_product_without_combination","disabled_product","product_without_image","product_without_description","product_without_price"].forEach(function(e){var n=new i.default(e);n.addExtension(new x.default),n.addExtension(new _.default),n.addExtension(new u.default),n.addExtension(new h.default),n.addExtension(new v.default),n.addExtension(new b.default)}),new C.default("monitoringShowcaseCard").addExtension(new O.default)})},4:function(e,n,t){"use strict";function r(e,n){if(!(e instanceof n))throw new TypeError("Cannot call a class as a function")}Object.defineProperty(n,"__esModule",{value:!0});var o=function(){function e(e,n){for(var t=0;t<n.length;t++){var r=n[t];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(e,r.key,r)}}return function(n,t,r){return t&&e(n.prototype,t),r&&e(n,r),n}}(),i=t(13),a=function(e){return e&&e.__esModule?e:{default:e}}(i),u=function(){function e(){r(this,e)}return o(e,[{key:"extend",value:function(e){var n=e.getContainer().find("table.table");new a.default(n).attach()}}]),e}();n.default=u},5:function(e,n,t){"use strict";function r(e,n){if(!(e instanceof n))throw new TypeError("Cannot call a class as a function")}Object.defineProperty(n,"__esModule",{value:!0});var o=function(){function e(e,n){for(var t=0;t<n.length;t++){var r=n[t];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(e,r.key,r)}}return function(n,t,r){return t&&e(n.prototype,t),r&&e(n,r),n}}(),i=function(){function e(){r(this,e)}return o(e,[{key:"extend",value:function(e){e.getHeaderContainer().on("click",".js-common_refresh_list-grid-action",function(){window.location.reload()})}}]),e}();n.default=i},6:function(e,n,t){"use strict";function r(e,n){if(!(e instanceof n))throw new TypeError("Cannot call a class as a function")}Object.defineProperty(n,"__esModule",{value:!0});var o=function(){function e(e,n){for(var t=0;t<n.length;t++){var r=n[t];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(e,r.key,r)}}return function(n,t,r){return t&&e(n.prototype,t),r&&e(n,r),n}}(),i=window,a=i.$,u=function(){function e(){r(this,e)}return o(e,[{key:"extend",value:function(e){var n=this;e.getHeaderContainer().on("click",".js-common_show_query-grid-action",function(){return n.onShowSqlQueryClick(e)}),e.getHeaderContainer().on("click",".js-common_export_sql_manager-grid-action",function(){return n.onExportSqlManagerClick(e)})}},{key:"onShowSqlQueryClick",value:function(e){var n=a("#"+e.getId()+"_common_show_query_modal_form");this.fillExportForm(n,e);var t=a("#"+e.getId()+"_grid_common_show_query_modal");t.modal("show"),t.on("click",".btn-sql-submit",function(){return n.submit()})}},{key:"onExportSqlManagerClick",value:function(e){var n=a("#"+e.getId()+"_common_show_query_modal_form");this.fillExportForm(n,e),n.submit()}},{key:"fillExportForm",value:function(e,n){var t=n.getContainer().find(".js-grid-table").data("query");e.find('textarea[name="sql"]').val(t),e.find('input[name="name"]').val(this.getNameFromBreadcrumb())}},{key:"getNameFromBreadcrumb",value:function(){var e=a(".header-toolbar").find(".breadcrumb-item"),n="";return e.each(function(e,t){var r=a(t),o=r.find("a").length>0?r.find("a").text():r.text();n.length>0&&(n=n.concat(" > ")),n=n.concat(o)}),n}}]),e}();n.default=u},69:function(e,n,t){"use strict";function r(e,n){if(!(e instanceof n))throw new TypeError("Cannot call a class as a function")}Object.defineProperty(n,"__esModule",{value:!0});var o=function(){function e(e,n){for(var t=0;t<n.length;t++){var r=n[t];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(e,r.key,r)}}return function(n,t,r){return t&&e(n.prototype,t),r&&e(n,r),n}}(),i=window,a=i.$,u=function(){function e(){var n=this;return r(this,e),{extend:function(e){return n.extend(e)}}}return o(e,[{key:"extend",value:function(e){e.getContainer().on("click",".js-delete-category-row-action",function(n){n.preventDefault();var t=a("#"+e.getId()+"_grid_delete_categories_modal");t.modal("show"),t.on("click",".js-submit-delete-categories",function(){var e=a(n.currentTarget),r=e.data("category-id"),o=a("#delete_categories_categories_to_delete"),i=o.data("prototype").replace(/__name__/g,o.children().length),u=a(a.parseHTML(i)[0]);u.val(r),o.append(u);var c=t.find("form");c.attr("action",e.data("category-delete-url")),c.submit()})})}}]),e}();n.default=u},7:function(e,n,t){"use strict";function r(e,n){if(!(e instanceof n))throw new TypeError("Cannot call a class as a function")}Object.defineProperty(n,"__esModule",{value:!0});var o=function(){function e(e,n){for(var t=0;t<n.length;t++){var r=n[t];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(e,r.key,r)}}return function(n,t,r){return t&&e(n.prototype,t),r&&e(n,r),n}}(),i=function(){function e(){r(this,e)}return o(e,[{key:"extend",value:function(e){var n=e.getContainer().find(".column-filters");n.find(".grid-search-button").prop("disabled",!0),n.find("input, select").on("input dp.change",function(){n.find(".grid-search-button").prop("disabled",!1),n.find(".js-grid-reset-button").prop("hidden",!1)})}}]),e}();n.default=i},70:function(e,n,t){"use strict";function r(e,n){if(!(e instanceof n))throw new TypeError("Cannot call a class as a function")}Object.defineProperty(n,"__esModule",{value:!0});var o=function(){function e(e,n){for(var t=0;t<n.length;t++){var r=n[t];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(e,r.key,r)}}return function(n,t,r){return t&&e(n.prototype,t),r&&e(n,r),n}}(),i=window,a=i.$,u=function(){function e(){var n=this;return r(this,e),{extend:function(e){return n.extend(e)}}}return o(e,[{key:"extend",value:function(e){var n=this;e.getContainer().find(".js-grid-table").on("click",".ps-togglable-row",function(e){e.preventDefault();var t=a(e.currentTarget);a.post({url:t.data("toggle-url")}).then(function(e){if(e.status)return window.showSuccessMessage(e.message),void n.toggleButtonDisplay(t);window.showErrorMessage(e.message)}).catch(function(e){var n=e.responseJSON;window.showErrorMessage(n.message)})})}},{key:"toggleButtonDisplay",value:function(e){var n=e.hasClass("grid-toggler-icon-valid"),t=n?"grid-toggler-icon-not-valid":"grid-toggler-icon-valid",r=n?"grid-toggler-icon-valid":"grid-toggler-icon-not-valid",o=n?"clear":"check";e.removeClass(r),e.addClass(t),e.text(o)}}]),e}();n.default=u}});