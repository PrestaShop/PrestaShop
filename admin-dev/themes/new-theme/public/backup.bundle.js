<<<<<<< HEAD
<<<<<<< HEAD
window.backup=function(n){function e(i){if(t[i])return t[i].exports;var r=t[i]={i:i,l:!1,exports:{}};return n[i].call(r.exports,r,r.exports,e),r.l=!0,r.exports}var t={};return e.m=n,e.c=t,e.i=function(n){return n},e.d=function(n,t,i){e.o(n,t)||Object.defineProperty(n,t,{configurable:!1,enumerable:!0,get:i})},e.n=function(n){var t=n&&n.__esModule?function(){return n.default}:function(){return n};return e.d(t,"a",t),t},e.o=function(n,e){return Object.prototype.hasOwnProperty.call(n,e)},e.p="",e(e.s=342)}({10:function(n,e,t){"use strict";function i(n,e){if(!(n instanceof e))throw new TypeError("Cannot call a class as a function")}Object.defineProperty(e,"__esModule",{value:!0});var r=function(){function n(n,e){for(var t=0;t<e.length;t++){var i=e[t];i.enumerable=i.enumerable||!1,i.configurable=!0,"value"in i&&(i.writable=!0),Object.defineProperty(n,i.key,i)}}return function(e,t,i){return t&&n(e.prototype,t),i&&n(e,i),e}}(),o=window.$,a=function(){function n(){i(this,n)}return r(n,[{key:"extend",value:function(n){this.initRowLinks(n),this.initConfirmableActions(n)}},{key:"initConfirmableActions",value:function(n){n.getContainer().on("click",".js-link-row-action",function(n){var e=o(n.currentTarget).data("confirm-message");e.length&&!confirm(e)&&n.preventDefault()})}},{key:"initRowLinks",value:function(n){o("tr",n.getContainer()).each(function(){var n=o(this);o(".js-link-row-action[data-clickable-row=1]:first",n).each(function(){var e=o(this),t=e.closest("td");o("td.clickable",n).not(t).addClass("cursor-pointer").click(function(){var n=e.data("confirm-message");n.length&&!confirm(n)||(document.location=e.attr("href"))})})})}}]),n}();e.default=a},11:function(n,e,t){"use strict";function i(n,e){if(!(n instanceof e))throw new TypeError("Cannot call a class as a function")}Object.defineProperty(e,"__esModule",{value:!0});var r=function(){function n(n,e){for(var t=0;t<e.length;t++){var i=e[t];i.enumerable=i.enumerable||!1,i.configurable=!0,"value"in i&&(i.writable=!0),Object.defineProperty(n,i.key,i)}}return function(e,t,i){return t&&n(e.prototype,t),i&&n(e,i),e}}(),o=window.$,a=function(){function n(){i(this,n)}return r(n,[{key:"extend",value:function(n){n.getContainer().on("click",".js-submit-row-action",function(n){n.preventDefault();var e=o(n.currentTarget),t=e.data("confirm-message");if(!t.length||confirm(t)){var i=e.data("method"),r=["GET","POST"].includes(i),a=o("<form>",{action:e.data("url"),method:r?i:"POST"}).appendTo("body");r||a.append(o("<input>",{type:"_hidden",name:"_method",value:i})),a.submit()}})}}]),n}();e.default=a},2:function(n,e,t){"use strict";function i(n,e){if(!(n instanceof e))throw new TypeError("Cannot call a class as a function")}Object.defineProperty(e,"__esModule",{value:!0});var r=function(){function n(n,e){for(var t=0;t<e.length;t++){var i=e[t];i.enumerable=i.enumerable||!1,i.configurable=!0,"value"in i&&(i.writable=!0),Object.defineProperty(n,i.key,i)}}return function(e,t,i){return t&&n(e.prototype,t),i&&n(e,i),e}}(),o=window.$,a=function(){function n(e){i(this,n),this.id=e,this.$container=o("#"+this.id+"_grid")}return r(n,[{key:"getId",value:function(){return this.id}},{key:"getContainer",value:function(){return this.$container}},{key:"getHeaderContainer",value:function(){return this.$container.closest(".js-grid-panel").find(".js-grid-header")}},{key:"addExtension",value:function(n){n.extend(this)}}]),n}();e.default=a},342:function(n,e,t){"use strict";function i(n){return n&&n.__esModule?n:{default:n}}var r=t(2),o=i(r),a=t(8),u=i(a),c=t(9),l=i(c),f=t(10),s=i(f),d=t(11),b=i(d),h=t(7),v=i(h);(0,window.$)(function(){var n=new o.default("backup");n.addExtension(new u.default),n.addExtension(new l.default),n.addExtension(new s.default),n.addExtension(new b.default),n.addExtension(new v.default)})},7:function(n,e,t){"use strict";function i(n,e){if(!(n instanceof e))throw new TypeError("Cannot call a class as a function")}Object.defineProperty(e,"__esModule",{value:!0});var r=function(){function n(n,e){for(var t=0;t<e.length;t++){var i=e[t];i.enumerable=i.enumerable||!1,i.configurable=!0,"value"in i&&(i.writable=!0),Object.defineProperty(n,i.key,i)}}return function(e,t,i){return t&&n(e.prototype,t),i&&n(e,i),e}}(),o=function(){function n(){i(this,n)}return r(n,[{key:"extend",value:function(n){var e=n.getContainer().find(".column-filters");e.find(".grid-search-button").prop("disabled",!0),e.find("input, select").on("input dp.change",function(){e.find(".grid-search-button").prop("disabled",!1),e.find(".js-grid-reset-button").prop("hidden",!1)})}}]),n}();e.default=o},8:function(n,e,t){"use strict";function i(n,e){if(!(n instanceof e))throw new TypeError("Cannot call a class as a function")}Object.defineProperty(e,"__esModule",{value:!0});var r=function(){function n(n,e){for(var t=0;t<e.length;t++){var i=e[t];i.enumerable=i.enumerable||!1,i.configurable=!0,"value"in i&&(i.writable=!0),Object.defineProperty(n,i.key,i)}}return function(e,t,i){return t&&n(e.prototype,t),i&&n(e,i),e}}(),o=window.$,a=function(){function n(){i(this,n)}return r(n,[{key:"extend",value:function(n){this._handleBulkActionCheckboxSelect(n),this._handleBulkActionSelectAllCheckbox(n)}},{key:"_handleBulkActionSelectAllCheckbox",value:function(n){var e=this;n.getContainer().on("change",".js-bulk-action-select-all",function(t){var i=o(t.currentTarget),r=i.is(":checked");r?e._enableBulkActionsBtn(n):e._disableBulkActionsBtn(n),n.getContainer().find(".js-bulk-action-checkbox").prop("checked",r)})}},{key:"_handleBulkActionCheckboxSelect",value:function(n){var e=this;n.getContainer().on("change",".js-bulk-action-checkbox",function(){n.getContainer().find(".js-bulk-action-checkbox:checked").length>0?e._enableBulkActionsBtn(n):e._disableBulkActionsBtn(n)})}},{key:"_enableBulkActionsBtn",value:function(n){n.getContainer().find(".js-bulk-actions-btn").prop("disabled",!1)}},{key:"_disableBulkActionsBtn",value:function(n){n.getContainer().find(".js-bulk-actions-btn").prop("disabled",!0)}}]),n}();e.default=a},9:function(n,e,t){"use strict";function i(n,e){if(!(n instanceof e))throw new TypeError("Cannot call a class as a function")}Object.defineProperty(e,"__esModule",{value:!0});var r=function(){function n(n,e){for(var t=0;t<e.length;t++){var i=e[t];i.enumerable=i.enumerable||!1,i.configurable=!0,"value"in i&&(i.writable=!0),Object.defineProperty(n,i.key,i)}}return function(e,t,i){return t&&n(e.prototype,t),i&&n(e,i),e}}(),o=window.$,a=function(){function n(){var e=this;return i(this,n),{extend:function(n){return e.extend(n)}}}return r(n,[{key:"extend",value:function(n){var e=this;n.getContainer().on("click",".js-bulk-action-submit-btn",function(t){e.submit(t,n)})}},{key:"submit",value:function(n,e){var t=o(n.currentTarget),i=t.data("confirm-message");if(!(void 0!==i&&0<i.length)||confirm(i)){var r=o("#"+e.getId()+"_filter_form");r.attr("action",t.data("form-url")),r.attr("method",t.data("form-method")),r.submit()}}}]),n}();e.default=a}});
=======
<<<<<<< HEAD
window.backup=function(n){function e(i){if(t[i])return t[i].exports;var r=t[i]={i:i,l:!1,exports:{}};return n[i].call(r.exports,r,r.exports,e),r.l=!0,r.exports}var t={};return e.m=n,e.c=t,e.i=function(n){return n},e.d=function(n,t,i){e.o(n,t)||Object.defineProperty(n,t,{configurable:!1,enumerable:!0,get:i})},e.n=function(n){var t=n&&n.__esModule?function(){return n.default}:function(){return n};return e.d(t,"a",t),t},e.o=function(n,e){return Object.prototype.hasOwnProperty.call(n,e)},e.p="",e(e.s=338)}({10:function(n,e,t){"use strict";function i(n,e){if(!(n instanceof e))throw new TypeError("Cannot call a class as a function")}Object.defineProperty(e,"__esModule",{value:!0});var r=function(){function n(n,e){for(var t=0;t<e.length;t++){var i=e[t];i.enumerable=i.enumerable||!1,i.configurable=!0,"value"in i&&(i.writable=!0),Object.defineProperty(n,i.key,i)}}return function(e,t,i){return t&&n(e.prototype,t),i&&n(e,i),e}}(),o=window.$,a=function(){function n(){i(this,n)}return r(n,[{key:"extend",value:function(n){this.initRowLinks(n),this.initConfirmableActions(n)}},{key:"initConfirmableActions",value:function(n){n.getContainer().on("click",".js-link-row-action",function(n){var e=o(n.currentTarget).data("confirm-message");e.length&&!confirm(e)&&n.preventDefault()})}},{key:"initRowLinks",value:function(n){o("tr",n.getContainer()).each(function(){var n=o(this);o(".js-link-row-action[data-clickable-row=1]:first",n).each(function(){var e=o(this),t=e.closest("td");o("td.clickable",n).not(t).addClass("cursor-pointer").click(function(){var n=e.data("confirm-message");n.length&&!confirm(n)||(document.location=e.attr("href"))})})})}}]),n}();e.default=a},11:function(n,e,t){"use strict";function i(n,e){if(!(n instanceof e))throw new TypeError("Cannot call a class as a function")}Object.defineProperty(e,"__esModule",{value:!0});var r=function(){function n(n,e){for(var t=0;t<e.length;t++){var i=e[t];i.enumerable=i.enumerable||!1,i.configurable=!0,"value"in i&&(i.writable=!0),Object.defineProperty(n,i.key,i)}}return function(e,t,i){return t&&n(e.prototype,t),i&&n(e,i),e}}(),o=window.$,a=function(){function n(){i(this,n)}return r(n,[{key:"extend",value:function(n){n.getContainer().on("click",".js-submit-row-action",function(n){n.preventDefault();var e=o(n.currentTarget),t=e.data("confirm-message");if(!t.length||confirm(t)){var i=e.data("method"),r=["GET","POST"].includes(i),a=o("<form>",{action:e.data("url"),method:r?i:"POST"}).appendTo("body");r||a.append(o("<input>",{type:"_hidden",name:"_method",value:i})),a.submit()}})}}]),n}();e.default=a},2:function(n,e,t){"use strict";function i(n,e){if(!(n instanceof e))throw new TypeError("Cannot call a class as a function")}Object.defineProperty(e,"__esModule",{value:!0});var r=function(){function n(n,e){for(var t=0;t<e.length;t++){var i=e[t];i.enumerable=i.enumerable||!1,i.configurable=!0,"value"in i&&(i.writable=!0),Object.defineProperty(n,i.key,i)}}return function(e,t,i){return t&&n(e.prototype,t),i&&n(e,i),e}}(),o=window.$,a=function(){function n(e){i(this,n),this.id=e,this.$container=o("#"+this.id+"_grid")}return r(n,[{key:"getId",value:function(){return this.id}},{key:"getContainer",value:function(){return this.$container}},{key:"getHeaderContainer",value:function(){return this.$container.closest(".js-grid-panel").find(".js-grid-header")}},{key:"addExtension",value:function(n){n.extend(this)}}]),n}();e.default=a},338:function(n,e,t){"use strict";function i(n){return n&&n.__esModule?n:{default:n}}var r=t(2),o=i(r),a=t(8),u=i(a),c=t(9),l=i(c),f=t(10),s=i(f),d=t(11),b=i(d),h=t(7),v=i(h);(0,window.$)(function(){var n=new o.default("backup");n.addExtension(new u.default),n.addExtension(new l.default),n.addExtension(new s.default),n.addExtension(new b.default),n.addExtension(new v.default)})},7:function(n,e,t){"use strict";function i(n,e){if(!(n instanceof e))throw new TypeError("Cannot call a class as a function")}Object.defineProperty(e,"__esModule",{value:!0});var r=function(){function n(n,e){for(var t=0;t<e.length;t++){var i=e[t];i.enumerable=i.enumerable||!1,i.configurable=!0,"value"in i&&(i.writable=!0),Object.defineProperty(n,i.key,i)}}return function(e,t,i){return t&&n(e.prototype,t),i&&n(e,i),e}}(),o=function(){function n(){i(this,n)}return r(n,[{key:"extend",value:function(n){var e=n.getContainer().find(".column-filters");e.find(".grid-search-button").prop("disabled",!0),e.find("input, select").on("input dp.change",function(){e.find(".grid-search-button").prop("disabled",!1),e.find(".js-grid-reset-button").prop("hidden",!1)})}}]),n}();e.default=o},8:function(n,e,t){"use strict";function i(n,e){if(!(n instanceof e))throw new TypeError("Cannot call a class as a function")}Object.defineProperty(e,"__esModule",{value:!0});var r=function(){function n(n,e){for(var t=0;t<e.length;t++){var i=e[t];i.enumerable=i.enumerable||!1,i.configurable=!0,"value"in i&&(i.writable=!0),Object.defineProperty(n,i.key,i)}}return function(e,t,i){return t&&n(e.prototype,t),i&&n(e,i),e}}(),o=window.$,a=function(){function n(){i(this,n)}return r(n,[{key:"extend",value:function(n){this._handleBulkActionCheckboxSelect(n),this._handleBulkActionSelectAllCheckbox(n)}},{key:"_handleBulkActionSelectAllCheckbox",value:function(n){var e=this;n.getContainer().on("change",".js-bulk-action-select-all",function(t){var i=o(t.currentTarget),r=i.is(":checked");r?e._enableBulkActionsBtn(n):e._disableBulkActionsBtn(n),n.getContainer().find(".js-bulk-action-checkbox").prop("checked",r)})}},{key:"_handleBulkActionCheckboxSelect",value:function(n){var e=this;n.getContainer().on("change",".js-bulk-action-checkbox",function(){n.getContainer().find(".js-bulk-action-checkbox:checked").length>0?e._enableBulkActionsBtn(n):e._disableBulkActionsBtn(n)})}},{key:"_enableBulkActionsBtn",value:function(n){n.getContainer().find(".js-bulk-actions-btn").prop("disabled",!1)}},{key:"_disableBulkActionsBtn",value:function(n){n.getContainer().find(".js-bulk-actions-btn").prop("disabled",!0)}}]),n}();e.default=a},9:function(n,e,t){"use strict";function i(n,e){if(!(n instanceof e))throw new TypeError("Cannot call a class as a function")}Object.defineProperty(e,"__esModule",{value:!0});var r=function(){function n(n,e){for(var t=0;t<e.length;t++){var i=e[t];i.enumerable=i.enumerable||!1,i.configurable=!0,"value"in i&&(i.writable=!0),Object.defineProperty(n,i.key,i)}}return function(e,t,i){return t&&n(e.prototype,t),i&&n(e,i),e}}(),o=window.$,a=function(){function n(){var e=this;return i(this,n),{extend:function(n){return e.extend(n)}}}return r(n,[{key:"extend",value:function(n){var e=this;n.getContainer().on("click",".js-bulk-action-submit-btn",function(t){e.submit(t,n)})}},{key:"submit",value:function(n,e){var t=o(n.currentTarget),i=t.data("confirm-message");if(!(void 0!==i&&0<i.length)||confirm(i)){var r=o("#"+e.getId()+"_filter_form");r.attr("action",t.data("form-url")),r.attr("method",t.data("form-method")),r.submit()}}}]),n}();e.default=a}});
=======
window.backup=function(n){function e(i){if(t[i])return t[i].exports;var r=t[i]={i:i,l:!1,exports:{}};return n[i].call(r.exports,r,r.exports,e),r.l=!0,r.exports}var t={};return e.m=n,e.c=t,e.i=function(n){return n},e.d=function(n,t,i){e.o(n,t)||Object.defineProperty(n,t,{configurable:!1,enumerable:!0,get:i})},e.n=function(n){var t=n&&n.__esModule?function(){return n.default}:function(){return n};return e.d(t,"a",t),t},e.o=function(n,e){return Object.prototype.hasOwnProperty.call(n,e)},e.p="",e(e.s=339)}({10:function(n,e,t){"use strict";function i(n,e){if(!(n instanceof e))throw new TypeError("Cannot call a class as a function")}Object.defineProperty(e,"__esModule",{value:!0});var r=function(){function n(n,e){for(var t=0;t<e.length;t++){var i=e[t];i.enumerable=i.enumerable||!1,i.configurable=!0,"value"in i&&(i.writable=!0),Object.defineProperty(n,i.key,i)}}return function(e,t,i){return t&&n(e.prototype,t),i&&n(e,i),e}}(),o=window.$,a=function(){function n(){i(this,n)}return r(n,[{key:"extend",value:function(n){this.initRowLinks(n),this.initConfirmableActions(n)}},{key:"initConfirmableActions",value:function(n){n.getContainer().on("click",".js-link-row-action",function(n){var e=o(n.currentTarget).data("confirm-message");e.length&&!confirm(e)&&n.preventDefault()})}},{key:"initRowLinks",value:function(n){o("tr",n.getContainer()).each(function(){var n=o(this);o(".js-link-row-action[data-clickable-row=1]:first",n).each(function(){var e=o(this),t=e.closest("td");o("td.data-type, td.identifier-type:not(:has(input)), td.badge-type, td.position-type",n).not(t).addClass("cursor-pointer").click(function(){var n=e.data("confirm-message");n.length&&!confirm(n)||(document.location=e.attr("href"))})})})}}]),n}();e.default=a},11:function(n,e,t){"use strict";function i(n,e){if(!(n instanceof e))throw new TypeError("Cannot call a class as a function")}Object.defineProperty(e,"__esModule",{value:!0});var r=function(){function n(n,e){for(var t=0;t<e.length;t++){var i=e[t];i.enumerable=i.enumerable||!1,i.configurable=!0,"value"in i&&(i.writable=!0),Object.defineProperty(n,i.key,i)}}return function(e,t,i){return t&&n(e.prototype,t),i&&n(e,i),e}}(),o=window.$,a=function(){function n(){i(this,n)}return r(n,[{key:"extend",value:function(n){n.getContainer().on("click",".js-submit-row-action",function(n){n.preventDefault();var e=o(n.currentTarget),t=e.data("confirm-message");if(!t.length||confirm(t)){var i=e.data("method"),r=["GET","POST"].includes(i),a=o("<form>",{action:e.data("url"),method:r?i:"POST"}).appendTo("body");r||a.append(o("<input>",{type:"_hidden",name:"_method",value:i})),a.submit()}})}}]),n}();e.default=a},2:function(n,e,t){"use strict";function i(n,e){if(!(n instanceof e))throw new TypeError("Cannot call a class as a function")}Object.defineProperty(e,"__esModule",{value:!0});var r=function(){function n(n,e){for(var t=0;t<e.length;t++){var i=e[t];i.enumerable=i.enumerable||!1,i.configurable=!0,"value"in i&&(i.writable=!0),Object.defineProperty(n,i.key,i)}}return function(e,t,i){return t&&n(e.prototype,t),i&&n(e,i),e}}(),o=window.$,a=function(){function n(e){i(this,n),this.id=e,this.$container=o("#"+this.id+"_grid")}return r(n,[{key:"getId",value:function(){return this.id}},{key:"getContainer",value:function(){return this.$container}},{key:"getHeaderContainer",value:function(){return this.$container.closest(".js-grid-panel").find(".js-grid-header")}},{key:"addExtension",value:function(n){n.extend(this)}}]),n}();e.default=a},339:function(n,e,t){"use strict";function i(n){return n&&n.__esModule?n:{default:n}}var r=t(2),o=i(r),a=t(6),u=i(a),c=t(8),l=i(c),f=t(10),s=i(f),d=t(11),b=i(d),p=t(9),h=i(p);(0,window.$)(function(){var n=new o.default("backup");n.addExtension(new u.default),n.addExtension(new l.default),n.addExtension(new s.default),n.addExtension(new b.default),n.addExtension(new h.default)})},6:function(n,e,t){"use strict";function i(n,e){if(!(n instanceof e))throw new TypeError("Cannot call a class as a function")}Object.defineProperty(e,"__esModule",{value:!0});var r=function(){function n(n,e){for(var t=0;t<e.length;t++){var i=e[t];i.enumerable=i.enumerable||!1,i.configurable=!0,"value"in i&&(i.writable=!0),Object.defineProperty(n,i.key,i)}}return function(e,t,i){return t&&n(e.prototype,t),i&&n(e,i),e}}(),o=window.$,a=function(){function n(){i(this,n)}return r(n,[{key:"extend",value:function(n){this._handleBulkActionCheckboxSelect(n),this._handleBulkActionSelectAllCheckbox(n)}},{key:"_handleBulkActionSelectAllCheckbox",value:function(n){var e=this;n.getContainer().on("change",".js-bulk-action-select-all",function(t){var i=o(t.currentTarget),r=i.is(":checked");r?e._enableBulkActionsBtn(n):e._disableBulkActionsBtn(n),n.getContainer().find(".js-bulk-action-checkbox").prop("checked",r)})}},{key:"_handleBulkActionCheckboxSelect",value:function(n){var e=this;n.getContainer().on("change",".js-bulk-action-checkbox",function(){n.getContainer().find(".js-bulk-action-checkbox:checked").length>0?e._enableBulkActionsBtn(n):e._disableBulkActionsBtn(n)})}},{key:"_enableBulkActionsBtn",value:function(n){n.getContainer().find(".js-bulk-actions-btn").prop("disabled",!1)}},{key:"_disableBulkActionsBtn",value:function(n){n.getContainer().find(".js-bulk-actions-btn").prop("disabled",!0)}}]),n}();e.default=a},8:function(n,e,t){"use strict";function i(n,e){if(!(n instanceof e))throw new TypeError("Cannot call a class as a function")}Object.defineProperty(e,"__esModule",{value:!0});var r=function(){function n(n,e){for(var t=0;t<e.length;t++){var i=e[t];i.enumerable=i.enumerable||!1,i.configurable=!0,"value"in i&&(i.writable=!0),Object.defineProperty(n,i.key,i)}}return function(e,t,i){return t&&n(e.prototype,t),i&&n(e,i),e}}(),o=window.$,a=function(){function n(){var e=this;return i(this,n),{extend:function(n){return e.extend(n)}}}return r(n,[{key:"extend",value:function(n){var e=this;n.getContainer().on("click",".js-bulk-action-submit-btn",function(t){e.submit(t,n)})}},{key:"submit",value:function(n,e){var t=o(n.currentTarget),i=t.data("confirm-message");if(!(void 0!==i&&0<i.length)||confirm(i)){var r=o("#"+e.getId()+"_filter_form");r.attr("action",t.data("form-url")),r.attr("method",t.data("form-method")),r.submit()}}}]),n}();e.default=a},9:function(n,e,t){"use strict";function i(n,e){if(!(n instanceof e))throw new TypeError("Cannot call a class as a function")}Object.defineProperty(e,"__esModule",{value:!0});var r=function(){function n(n,e){for(var t=0;t<e.length;t++){var i=e[t];i.enumerable=i.enumerable||!1,i.configurable=!0,"value"in i&&(i.writable=!0),Object.defineProperty(n,i.key,i)}}return function(e,t,i){return t&&n(e.prototype,t),i&&n(e,i),e}}(),o=function(){function n(){i(this,n)}return r(n,[{key:"extend",value:function(n){var e=n.getContainer().find(".column-filters");e.find(".grid-search-button").prop("disabled",!0),e.find("input, select").on("input dp.change",function(){e.find(".grid-search-button").prop("disabled",!1),e.find(".js-grid-reset-button").prop("hidden",!1)})}}]),n}();e.default=o}});
>>>>>>> Rebuilds assets
>>>>>>> Rebuilds assets
=======
window.backup=function(n){function e(i){if(t[i])return t[i].exports;var r=t[i]={i:i,l:!1,exports:{}};return n[i].call(r.exports,r,r.exports,e),r.l=!0,r.exports}var t={};return e.m=n,e.c=t,e.i=function(n){return n},e.d=function(n,t,i){e.o(n,t)||Object.defineProperty(n,t,{configurable:!1,enumerable:!0,get:i})},e.n=function(n){var t=n&&n.__esModule?function(){return n.default}:function(){return n};return e.d(t,"a",t),t},e.o=function(n,e){return Object.prototype.hasOwnProperty.call(n,e)},e.p="",e(e.s=340)}({10:function(n,e,t){"use strict";function i(n,e){if(!(n instanceof e))throw new TypeError("Cannot call a class as a function")}Object.defineProperty(e,"__esModule",{value:!0});var r=function(){function n(n,e){for(var t=0;t<e.length;t++){var i=e[t];i.enumerable=i.enumerable||!1,i.configurable=!0,"value"in i&&(i.writable=!0),Object.defineProperty(n,i.key,i)}}return function(e,t,i){return t&&n(e.prototype,t),i&&n(e,i),e}}(),o=window.$,a=function(){function n(){i(this,n)}return r(n,[{key:"extend",value:function(n){this.initRowLinks(n),this.initConfirmableActions(n)}},{key:"initConfirmableActions",value:function(n){n.getContainer().on("click",".js-link-row-action",function(n){var e=o(n.currentTarget).data("confirm-message");e.length&&!confirm(e)&&n.preventDefault()})}},{key:"initRowLinks",value:function(n){o("tr",n.getContainer()).each(function(){var n=o(this);o(".js-link-row-action[data-clickable-row=1]:first",n).each(function(){var e=o(this),t=e.closest("td");o("td.clickable",n).not(t).addClass("cursor-pointer").click(function(){var n=e.data("confirm-message");n.length&&!confirm(n)||(document.location=e.attr("href"))})})})}}]),n}();e.default=a},11:function(n,e,t){"use strict";function i(n,e){if(!(n instanceof e))throw new TypeError("Cannot call a class as a function")}Object.defineProperty(e,"__esModule",{value:!0});var r=function(){function n(n,e){for(var t=0;t<e.length;t++){var i=e[t];i.enumerable=i.enumerable||!1,i.configurable=!0,"value"in i&&(i.writable=!0),Object.defineProperty(n,i.key,i)}}return function(e,t,i){return t&&n(e.prototype,t),i&&n(e,i),e}}(),o=window.$,a=function(){function n(){i(this,n)}return r(n,[{key:"extend",value:function(n){n.getContainer().on("click",".js-submit-row-action",function(n){n.preventDefault();var e=o(n.currentTarget),t=e.data("confirm-message");if(!t.length||confirm(t)){var i=e.data("method"),r=["GET","POST"].includes(i),a=o("<form>",{action:e.data("url"),method:r?i:"POST"}).appendTo("body");r||a.append(o("<input>",{type:"_hidden",name:"_method",value:i})),a.submit()}})}}]),n}();e.default=a},2:function(n,e,t){"use strict";function i(n,e){if(!(n instanceof e))throw new TypeError("Cannot call a class as a function")}Object.defineProperty(e,"__esModule",{value:!0});var r=function(){function n(n,e){for(var t=0;t<e.length;t++){var i=e[t];i.enumerable=i.enumerable||!1,i.configurable=!0,"value"in i&&(i.writable=!0),Object.defineProperty(n,i.key,i)}}return function(e,t,i){return t&&n(e.prototype,t),i&&n(e,i),e}}(),o=window.$,a=function(){function n(e){i(this,n),this.id=e,this.$container=o("#"+this.id+"_grid")}return r(n,[{key:"getId",value:function(){return this.id}},{key:"getContainer",value:function(){return this.$container}},{key:"getHeaderContainer",value:function(){return this.$container.closest(".js-grid-panel").find(".js-grid-header")}},{key:"addExtension",value:function(n){n.extend(this)}}]),n}();e.default=a},340:function(n,e,t){"use strict";function i(n){return n&&n.__esModule?n:{default:n}}var r=t(2),o=i(r),a=t(8),u=i(a),c=t(9),l=i(c),f=t(10),s=i(f),d=t(11),b=i(d),h=t(7),v=i(h);(0,window.$)(function(){var n=new o.default("backup");n.addExtension(new u.default),n.addExtension(new l.default),n.addExtension(new s.default),n.addExtension(new b.default),n.addExtension(new v.default)})},7:function(n,e,t){"use strict";function i(n,e){if(!(n instanceof e))throw new TypeError("Cannot call a class as a function")}Object.defineProperty(e,"__esModule",{value:!0});var r=function(){function n(n,e){for(var t=0;t<e.length;t++){var i=e[t];i.enumerable=i.enumerable||!1,i.configurable=!0,"value"in i&&(i.writable=!0),Object.defineProperty(n,i.key,i)}}return function(e,t,i){return t&&n(e.prototype,t),i&&n(e,i),e}}(),o=function(){function n(){i(this,n)}return r(n,[{key:"extend",value:function(n){var e=n.getContainer().find(".column-filters");e.find(".grid-search-button").prop("disabled",!0),e.find("input, select").on("input dp.change",function(){e.find(".grid-search-button").prop("disabled",!1),e.find(".js-grid-reset-button").prop("hidden",!1)})}}]),n}();e.default=o},8:function(n,e,t){"use strict";function i(n,e){if(!(n instanceof e))throw new TypeError("Cannot call a class as a function")}Object.defineProperty(e,"__esModule",{value:!0});var r=function(){function n(n,e){for(var t=0;t<e.length;t++){var i=e[t];i.enumerable=i.enumerable||!1,i.configurable=!0,"value"in i&&(i.writable=!0),Object.defineProperty(n,i.key,i)}}return function(e,t,i){return t&&n(e.prototype,t),i&&n(e,i),e}}(),o=window.$,a=function(){function n(){i(this,n)}return r(n,[{key:"extend",value:function(n){this._handleBulkActionCheckboxSelect(n),this._handleBulkActionSelectAllCheckbox(n)}},{key:"_handleBulkActionSelectAllCheckbox",value:function(n){var e=this;n.getContainer().on("change",".js-bulk-action-select-all",function(t){var i=o(t.currentTarget),r=i.is(":checked");r?e._enableBulkActionsBtn(n):e._disableBulkActionsBtn(n),n.getContainer().find(".js-bulk-action-checkbox").prop("checked",r)})}},{key:"_handleBulkActionCheckboxSelect",value:function(n){var e=this;n.getContainer().on("change",".js-bulk-action-checkbox",function(){n.getContainer().find(".js-bulk-action-checkbox:checked").length>0?e._enableBulkActionsBtn(n):e._disableBulkActionsBtn(n)})}},{key:"_enableBulkActionsBtn",value:function(n){n.getContainer().find(".js-bulk-actions-btn").prop("disabled",!1)}},{key:"_disableBulkActionsBtn",value:function(n){n.getContainer().find(".js-bulk-actions-btn").prop("disabled",!0)}}]),n}();e.default=a},9:function(n,e,t){"use strict";function i(n,e){if(!(n instanceof e))throw new TypeError("Cannot call a class as a function")}Object.defineProperty(e,"__esModule",{value:!0});var r=function(){function n(n,e){for(var t=0;t<e.length;t++){var i=e[t];i.enumerable=i.enumerable||!1,i.configurable=!0,"value"in i&&(i.writable=!0),Object.defineProperty(n,i.key,i)}}return function(e,t,i){return t&&n(e.prototype,t),i&&n(e,i),e}}(),o=window.$,a=function(){function n(){var e=this;return i(this,n),{extend:function(n){return e.extend(n)}}}return r(n,[{key:"extend",value:function(n){var e=this;n.getContainer().on("click",".js-bulk-action-submit-btn",function(t){e.submit(t,n)})}},{key:"submit",value:function(n,e){var t=o(n.currentTarget),i=t.data("confirm-message");if(!(void 0!==i&&0<i.length)||confirm(i)){var r=o("#"+e.getId()+"_filter_form");r.attr("action",t.data("form-url")),r.attr("method",t.data("form-method")),r.submit()}}}]),n}();e.default=a}});
>>>>>>> Rebuild assets
