window.maintenance=function(e){function i(n){if(t[n])return t[n].exports;var a=t[n]={i:n,l:!1,exports:{}};return e[n].call(a.exports,a,a.exports,i),a.l=!0,a.exports}var t={};return i.m=e,i.c=t,i.i=function(e){return e},i.d=function(e,t,n){i.o(e,t)||Object.defineProperty(e,t,{configurable:!1,enumerable:!0,get:n})},i.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return i.d(t,"a",t),t},i.o=function(e,i){return Object.prototype.hasOwnProperty.call(e,i)},i.p="",i(i.s=371)}({31:function(e,i,t){"use strict";function n(e,i){if(!(e instanceof i))throw new TypeError("Cannot call a class as a function")}Object.defineProperty(i,"__esModule",{value:!0});var a=function(){function e(e,i){for(var t=0;t<i.length;t++){var n=i[t];n.enumerable=n.enumerable||!1,n.configurable=!0,"value"in n&&(n.writable=!0),Object.defineProperty(e,n.key,n)}}return function(i,t,n){return t&&e(i.prototype,t),n&&e(i,n),i}}(),l=window.$,r=function(){function e(i){if(n(this,e),i=i||{},this.tinyMCELoaded=!1,void 0===i.baseAdminUrl)if(void 0!==window.baseAdminDir)i.baseAdminUrl=window.baseAdminDir;else{var t=window.location.pathname.split("/");t.every(function(e){return""===e||(i.baseAdminUrl="/"+e+"/",!1)})}void 0===i.langIsRtl&&(i.langIsRtl=void 0!==window.lang_is_rtl&&"1"===window.lang_is_rtl),this.setupTinyMCE(i)}return a(e,[{key:"setupTinyMCE",value:function(e){"undefined"==typeof tinyMCE?this.loadAndInitTinyMCE(e):this.initTinyMCE(e)}},{key:"initTinyMCE",value:function(e){var i=this;e=Object.assign({selector:".rte",plugins:"align colorpicker link image filemanager table media placeholder advlist code table autoresize",browser_spellcheck:!0,toolbar1:"code,colorpicker,bold,italic,underline,strikethrough,blockquote,link,align,bullist,numlist,table,image,media,formatselect",toolbar2:"",external_filemanager_path:e.baseAdminUrl+"filemanager/",filemanager_title:"File manager",external_plugins:{filemanager:e.baseAdminUrl+"filemanager/plugin.min.js"},language:iso_user,content_style:e.langIsRtl?"body {direction:rtl;}":"",skin:"prestashop",menubar:!1,statusbar:!1,relative_urls:!1,convert_urls:!1,entity_encoding:"raw",extended_valid_elements:"em[class|name|id],@[role|data-*|aria-*]",valid_children:"+*[*]",valid_elements:"*[*]",rel_list:[{title:"nofollow",value:"nofollow"}],editor_selector:"autoload_rte",init_instance_callback:function(){i.changeToMaterial()},setup:function(e){i.setupEditor(e)}},e),void 0!==e.editor_selector&&(e.selector="."+e.editor_selector),l("body").on("click",".mce-btn, .mce-open, .mce-menu-item",function(){i.changeToMaterial()}),tinyMCE.init(e),this.watchTabChanges(e)}},{key:"setupEditor",value:function(e){var i=this;e.on("loadContent",function(e){i.handleCounterTiny(e.target.id)}),e.on("change",function(e){tinyMCE.triggerSave(),i.handleCounterTiny(e.target.id)}),e.on("blur",function(){tinyMCE.triggerSave()})}},{key:"watchTabChanges",value:function(e){l(e.selector).each(function(e,i){var t=l(i).closest(".translation-field"),n=l(i).closest(".translations.tabbable");if(t.length&&n.length){var a=t.data("locale");l('.nav-item a[data-locale="'+a+'"]',n).on("shown.bs.tab",function(){var e=tinyMCE.get(i.id);e&&e.setContent(e.getContent())})}})}},{key:"loadAndInitTinyMCE",value:function(e){var i=this;if(!this.tinyMCELoaded){this.tinyMCELoaded=!0;var t=e.baseAdminUrl.split("/");t.splice(t.length-2,2);var n=t.join("/");window.tinyMCEPreInit={},window.tinyMCEPreInit.base=n+"/js/tiny_mce",window.tinyMCEPreInit.suffix=".min",l.getScript(n+"/js/tiny_mce/tinymce.min.js",function(){i.setupTinyMCE(e)})}}},{key:"changeToMaterial",value:function(){var e={"mce-i-code":'<i class="material-icons">code</i>',"mce-i-none":'<i class="material-icons">format_color_text</i>',"mce-i-bold":'<i class="material-icons">format_bold</i>',"mce-i-italic":'<i class="material-icons">format_italic</i>',"mce-i-underline":'<i class="material-icons">format_underlined</i>',"mce-i-strikethrough":'<i class="material-icons">format_strikethrough</i>',"mce-i-blockquote":'<i class="material-icons">format_quote</i>',"mce-i-link":'<i class="material-icons">link</i>',"mce-i-alignleft":'<i class="material-icons">format_align_left</i>',"mce-i-aligncenter":'<i class="material-icons">format_align_center</i>',"mce-i-alignright":'<i class="material-icons">format_align_right</i>',"mce-i-alignjustify":'<i class="material-icons">format_align_justify</i>',"mce-i-bullist":'<i class="material-icons">format_list_bulleted</i>',"mce-i-numlist":'<i class="material-icons">format_list_numbered</i>',"mce-i-image":'<i class="material-icons">image</i>',"mce-i-table":'<i class="material-icons">grid_on</i>',"mce-i-media":'<i class="material-icons">video_library</i>',"mce-i-browse":'<i class="material-icons">attachment</i>',"mce-i-checkbox":'<i class="mce-ico mce-i-checkbox"></i>'};l.each(e,function(e,i){l("."+e).replaceWith(i)})}},{key:"handleCounterTiny",value:function(e){var i=l("#"+e),t=i.attr("counter"),n=i.attr("counter_type"),a=tinyMCE.activeEditor.getBody().textContent.length;i.parent().find("span.currentLength").text(a),"recommended"!==n&&a>t?i.parent().find("span.maxLength").addClass("text-danger"):i.parent().find("span.maxLength").removeClass("text-danger")}}]),e}();i.default=r},371:function(e,i,t){"use strict";var n=t(31),a=function(e){return e&&e.__esModule?e:{default:e}}(n);/**
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
(0,window.$)(function(){new a.default})}});