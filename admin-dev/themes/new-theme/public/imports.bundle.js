/******/!function(o){// webpackBootstrap
/******/var i={};function n(e){if(i[e])return i[e].exports;var t=i[e]={i:e,l:!1,exports:{}};return o[e].call(t.exports,t,t.exports,n),t.l=!0,t.exports}n.m=o,n.c=i,n.d=function(e,t,o){n.o(e,t)||Object.defineProperty(e,t,{enumerable:!0,get:o})},n.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},n.t=function(t,e){if(1&e&&(t=n(t)),8&e)return t;if(4&e&&"object"==typeof t&&t&&t.__esModule)return t;var o=Object.create(null);if(n.r(o),Object.defineProperty(o,"default",{enumerable:!0,value:t}),2&e&&"string"!=typeof t)for(var i in t)n.d(o,i,function(e){return t[e]}.bind(null,i));return o},n.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return n.d(t,"a",t),t},n.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},n.p="",n(n.s=268)}({268:function(e,t,o){"use strict";function n(e,t){for(var o=0;o<t.length;o++){var i=t[o];i.enumerable=i.enumerable||!1,i.configurable=!0,"value"in i&&(i.writable=!0),Object.defineProperty(e,i.key,i)}}o.r(t);
/**
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
var a=window.$,s=0,u=1,d=2,f=3,c=4,p=5,v=6,h=7,m=8,l=function(){function t(){var e=this;!function(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}(this,t),a(".js-entity-select").on("change",function(){return e.toggleForm()}),this.toggleForm()}var e,o,i;return e=t,(o=[{key:"toggleForm",value:function(){var e=a("#entity").find("option:selected"),t=parseInt(e.val()),o=e.text().toLowerCase();this.toggleEntityAlert(t),this.toggleFields(t,o),this.loadAvailableFields(t)}},{key:"toggleEntityAlert",value:function(e){var t=a(".js-entity-alert");[s,u].includes(e)?t.show():t.hide()}},{key:"toggleFields",value:function(e,t){var o=a(".js-truncate-form-group"),i=a(".js-match-ref-form-group"),n=a(".js-regenerate-form-group"),l=a(".js-force-ids-form-group"),r=a(".js-entity-name");m===e?o.hide():o.show(),[u,d].includes(e)?i.show():i.hide(),[s,u,p,v,m].includes(e)?n.show():n.hide(),[s,u,f,c,p,v,m,h].includes(e)?l.show():l.hide(),r.html(t)}},{key:"loadAvailableFields",value:function(e){var o=this,i=a(".js-available-fields");a.ajax({url:i.data("url"),data:{entity:e},dataType:"json"}).then(function(e){o._removeAvailableFields(i);for(var t=0;t<e.length;t++)o._appendAvailableField(i,e[t].label+(e[t].required?"*":""),e[t].description);i.find('[data-toggle="popover"]').popover()})}},{key:"_removeAvailableFields",value:function(e){e.find('[data-toggle="popover"]').popover("hide"),e.empty()}},{key:"_appendHelpBox",value:function(e,t){var o=a(".js-available-field-popover-template").clone();o.attr("data-content",t),o.removeClass("js-available-field-popover-template d-none"),e.append(o)}},{key:"_appendAvailableField",value:function(e,t,o){var i=a(".js-available-field-template").clone();i.text(t),o&&this._appendHelpBox(i,o),i.removeClass("js-available-field-template d-none"),i.appendTo(e)}}])&&n(e.prototype,o),i&&n(e,i),t}();function r(e,t){for(var o=0;o<t.length;o++){var i=t[o];i.enumerable=i.enumerable||!1,i.configurable=!0,"value"in i&&(i.writable=!0),Object.defineProperty(e,i.key,i)}}
/**
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
var y=window.$,i=function(){function e(){var t=this;!function(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}(this,e),new l,y(".js-from-files-history-btn").on("click",function(){return t.showFilesHistoryHandler()}),y(".js-close-files-history-block-btn").on("click",function(){return t.closeFilesHistoryHandler()}),y("#fileHistoryTable").on("click",".js-use-file-btn",function(e){return t.useFileFromFilesHistory(e)}),y(".js-change-import-file-btn").on("click",function(){return t.changeImportFileHandler()}),y(".js-import-file").on("change",function(){return t.uploadFile()}),this.toggleSelectedFile(),this.handleSubmit()}var t,o,i;return t=e,(o=[{key:"handleSubmit",value:function(){y(".js-import-form").on("submit",function(){var e=y(this);if("1"===e.find('input[name="truncate"]:checked').val())return confirm("".concat(e.data("delete-confirm-message")," ").concat(y.trim(y("#entity > option:selected").text().toLowerCase()),"?"))})}},{key:"toggleSelectedFile",value:function(){var e=y("#csv").val();0<e.length&&(this.showImportFileAlert(e),this.hideFileUploadBlock())}},{key:"changeImportFileHandler",value:function(){this.hideImportFileAlert(),this.showFileUploadBlock()}},{key:"showFilesHistoryHandler",value:function(){this.showFilesHistory(),this.hideFileUploadBlock()}},{key:"closeFilesHistoryHandler",value:function(){this.closeFilesHistory(),this.showFileUploadBlock()}},{key:"showFilesHistory",value:function(){y(".js-files-history-block").removeClass("d-none")}},{key:"closeFilesHistory",value:function(){y(".js-files-history-block").addClass("d-none")}},{key:"useFileFromFilesHistory",value:function(e){var t=y(e.target).closest(".btn-group").data("file");y(".js-import-file-input").val(t),this.showImportFileAlert(t),this.closeFilesHistory()}},{key:"showImportFileAlert",value:function(e){y(".js-import-file-alert").removeClass("d-none"),y(".js-import-file").text(e)}},{key:"hideImportFileAlert",value:function(){y(".js-import-file-alert").addClass("d-none")}},{key:"hideFileUploadBlock",value:function(){y(".js-file-upload-form-group").addClass("d-none")}},{key:"showFileUploadBlock",value:function(){y(".js-file-upload-form-group").removeClass("d-none")}},{key:"enableFilesHistoryBtn",value:function(){y(".js-from-files-history-btn").removeAttr("disabled")}},{key:"showImportFileError",value:function(e,t,o){var i=y(".js-import-file-error"),n=e+" ("+this.humanizeSize(t)+")";i.find(".js-file-data").html(n),i.find(".js-error-message").html(o),i.removeClass("d-none")}},{key:"hideImportFileError",value:function(){y(".js-import-file-error").addClass("d-none")}},{key:"humanizeSize",value:function(e){return"number"!=typeof e?"":1e9<=e?(e/1e9).toFixed(2)+" GB":1e6<=e?(e/1e6).toFixed(2)+" MB":(e/1e3).toFixed(2)+" KB"}},{key:"uploadFile",value:function(){var o=this;this.hideImportFileError();var e=y("#file"),i=e.prop("files")[0];if(e.data("max-file-upload-size")<i.size)this.showImportFileError(i.name,i.size,"File is too large");else{var t=new FormData;t.append("file",i),y.ajax({type:"POST",url:y(".js-import-form").data("file-upload-url"),data:t,cache:!1,contentType:!1,processData:!1}).then(function(e){if(e.error)o.showImportFileError(i.name,i.size,e.error);else{var t=e.file.name;y(".js-import-file-input").val(t),o.showImportFileAlert(t),o.hideFileUploadBlock(),o.addFileToHistoryTable(t),o.enableFilesHistoryBtn()}})}}},{key:"addFileToHistoryTable",value:function(e){var t=y("#fileHistoryTable"),o=t.data("delete-file-url")+"&filename="+encodeURIComponent(e),i=t.data("download-file-url")+"&filename="+encodeURIComponent(e),n=t.find("tr:first").clone();n.removeClass("d-none"),n.find("td:first").text(e),n.find(".btn-group").attr("data-file",e),n.find(".js-delete-file-btn").attr("href",o),n.find(".js-download-file-btn").attr("href",i),t.find("tbody").append(n);var l=t.find("tr").length-1;y(".js-files-history-number").text(l)}}])&&r(t.prototype,o),i&&r(t,i),e}();(0,window.$)(function(){new i})}});