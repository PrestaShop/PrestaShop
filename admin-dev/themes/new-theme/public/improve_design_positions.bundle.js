<<<<<<< HEAD
<<<<<<< HEAD
<<<<<<< HEAD
<<<<<<< HEAD
window.improve_design_positions=function(o){function e(n){if(t[n])return t[n].exports;var i=t[n]={i:n,l:!1,exports:{}};return o[n].call(i.exports,i,i.exports,e),i.l=!0,i.exports}var t={};return e.m=o,e.c=t,e.i=function(o){return o},e.d=function(o,t,n){e.o(o,t)||Object.defineProperty(o,t,{configurable:!1,enumerable:!0,get:n})},e.n=function(o){var t=o&&o.__esModule?function(){return o.default}:function(){return o};return e.d(t,"a",t),t},e.o=function(o,e){return Object.prototype.hasOwnProperty.call(o,e)},e.p="",e(e.s=364)}({277:function(o,e,t){"use strict";function n(o,e){if(!(o instanceof e))throw new TypeError("Cannot call a class as a function")}Object.defineProperty(e,"__esModule",{value:!0});var i=function(){function o(o,e){var t=[],n=!0,i=!1,l=void 0;try{for(var s,a=o[Symbol.iterator]();!(n=(s=a.next()).done)&&(t.push(s.value),!e||t.length!==e);n=!0);}catch(o){i=!0,l=o}finally{try{!n&&a.return&&a.return()}finally{if(i)throw l}}return t}return function(e,t){if(Array.isArray(e))return e;if(Symbol.iterator in Object(e))return o(e,t);throw new TypeError("Invalid attempt to destructure non-iterable instance")}}(),l=function(){function o(o,e){for(var t=0;t<e.length;t++){var n=e[t];n.enumerable=n.enumerable||!1,n.configurable=!0,"value"in n&&(n.writable=!0),Object.defineProperty(o,n.key,n)}}return function(e,t,n){return t&&o(e.prototype,t),n&&o(e,n),e}}(),s=window.$,a=function(){function o(){if(n(this,o),0!==s("#position-filters").length){var e=this;e.$panelSelection=s("#modules-position-selection-panel"),e.$panelSelectionSingleSelection=s("#modules-position-single-selection"),e.$panelSelectionMultipleSelection=s("#modules-position-multiple-selection"),e.$panelSelectionOriginalY=e.$panelSelection.offset().top,e.$showModules=s("#show-modules"),e.$modulesList=s(".modules-position-checkbox"),e.$hookPosition=s("#hook-position"),e.$hookSearch=s("#hook-search"),e.$modulePositionsForm=s("#module-positions-form"),e.$moduleUnhookButton=s("#unhook-button-position-bottom"),e.$moduleButtonsUpdate=s(".module-buttons-update .btn"),e.handleList(),e.handleSortable(),s('input[name="form[general][enable_tos]"]').on("change",function(){return e.handle()})}}return l(o,[{key:"handleList",value:function(){var o=this;s(window).on("scroll",function(){var e=s(window).scrollTop();o.$panelSelection.css("top",e<20?0:e-o.$panelSelectionOriginalY)}),o.$modulesList.on("change",function(){var e=o.$modulesList.filter(":checked").length;0===e?(o.$moduleUnhookButton.hide(),o.$panelSelection.hide(),o.$panelSelectionSingleSelection.hide(),o.$panelSelectionMultipleSelection.hide()):1===e?(o.$moduleUnhookButton.show(),o.$panelSelection.show(),o.$panelSelectionSingleSelection.show(),o.$panelSelectionMultipleSelection.hide()):(o.$moduleUnhookButton.show(),o.$panelSelection.show(),o.$panelSelectionSingleSelection.hide(),o.$panelSelectionMultipleSelection.show(),s("#modules-position-selection-count").html(e))}),o.$panelSelection.find("button").click(function(){s('button[name="unhookform"]').trigger("click")}),o.$hooksList=[],s("section.hook-panel .hook-name").each(function(){var e=s(this);o.$hooksList.push({title:e.html(),element:e,container:e.parents(".hook-panel")})}),o.$showModules.select2(),o.$showModules.on("change",function(){o.modulesPositionFilterHooks()}),o.$hookPosition.on("change",function(){o.modulesPositionFilterHooks()}),o.$hookSearch.on("input",function(){o.modulesPositionFilterHooks()}),o.$hookSearch.on("keypress",function(o){return 13!==(o.keyCode||o.which)}),s(".hook-checker").on("click",function(){s(".hook"+s(this).data("hook-id")).prop("checked",s(this).prop("checked"))}),o.$modulesList.on("click",function(){s("#Ghook"+s(this).data("hook-id")).prop("checked",0===s(".hook"+s(this).data("hook-id")+":not(:checked)").length)}),o.$moduleButtonsUpdate.on("click",function(){var e=s(this),t=e.closest(".module-item"),n=void 0;return n=e.data("way")?t.next(".module-item"):t.prev(".module-item"),0!==n.length&&(e.data("way")?t.insertAfter(n):t.insertBefore(n),o.updatePositions({hookId:e.data("hook-id"),moduleId:e.data("module-id"),way:e.data("way"),positions:[]},e.closest("ul")),!1)})}},{key:"handleSortable",value:function(){var o=this;s(".sortable").sortable({forcePlaceholderSize:!0,start:function(o,e){s(this).data("previous-index",e.item.index())},update:function(e,t){var n=t.item.attr("id").split("_"),l=i(n,2),a=l[0],r=l[1],c={hookId:a,moduleId:r,way:s(this).data("previous-index")<t.item.index()?1:0,positions:[]};o.updatePositions(c,s(e.target))}})}},{key:"updatePositions",value:function(o,e){var t=this;s.each(e.children(),function(e,t){o.positions.push(s(t).attr("id"))}),s.ajax({type:"POST",headers:{"cache-control":"no-cache"},url:t.$modulePositionsForm.data("update-url"),data:o,success:function(){var o=0;s.each(e.children(),function(e,t){console.log(s(t).find(".index-position")),s(t).find(".index-position").html(++o)}),window.showSuccessMessage(window.update_success_msg)}})}},{key:"modulesPositionFilterHooks",value:function(){for(var o=this,e=o.$hookSearch.val(),t=o.$showModules.val(),n=new RegExp("("+e+")","gi"),i=0;i<o.$hooksList.length;i++)o.$hooksList[i].container.toggle(""===e&&"all"===t),o.$hooksList[i].element.html(o.$hooksList[i].title),o.$hooksList[i].container.find(".module-item").removeClass("highlight");if(""!==e||"all"!==t){for(var l=s(),a=s(),r=void 0,c=0;c<o.$hooksList.length;c++)"all"!==t&&(r=o.$hooksList[c].container.find(".module-position-"+t),r.length>0&&(l=l.add(o.$hooksList[c].container),r.addClass("highlight"))),""!==e&&-1!==o.$hooksList[c].title.toLowerCase().search(e.toLowerCase())&&(a=a.add(o.$hooksList[c].container),o.$hooksList[c].element.html(o.$hooksList[c].title.replace(n,'<span class="highlight">$1</span>')));"all"===t&&""!==e?a.show():""===e&&"all"!==t?l.show():a.filter(l).show()}if(!o.$hookPosition.prop("checked"))for(var u=0;u<o.$hooksList.length;u++)o.$hooksList[u].container.is(".hook-position")&&o.$hooksList[u].container.hide()}}]),o}();e.default=a},364:function(o,e,t){"use strict";var n=t(277),i=function(o){return o&&o.__esModule?o:{default:o}}(n);/**
=======
=======
>>>>>>> Rebuilds assets
<<<<<<< HEAD
window.improve_design_positions=function(o){function e(n){if(t[n])return t[n].exports;var i=t[n]={i:n,l:!1,exports:{}};return o[n].call(i.exports,i,i.exports,e),i.l=!0,i.exports}var t={};return e.m=o,e.c=t,e.i=function(o){return o},e.d=function(o,t,n){e.o(o,t)||Object.defineProperty(o,t,{configurable:!1,enumerable:!0,get:n})},e.n=function(o){var t=o&&o.__esModule?function(){return o.default}:function(){return o};return e.d(t,"a",t),t},e.o=function(o,e){return Object.prototype.hasOwnProperty.call(o,e)},e.p="",e(e.s=359)}({273:function(o,e,t){"use strict";function n(o,e){if(!(o instanceof e))throw new TypeError("Cannot call a class as a function")}Object.defineProperty(e,"__esModule",{value:!0});var i=function(){function o(o,e){var t=[],n=!0,i=!1,l=void 0;try{for(var s,a=o[Symbol.iterator]();!(n=(s=a.next()).done)&&(t.push(s.value),!e||t.length!==e);n=!0);}catch(o){i=!0,l=o}finally{try{!n&&a.return&&a.return()}finally{if(i)throw l}}return t}return function(e,t){if(Array.isArray(e))return e;if(Symbol.iterator in Object(e))return o(e,t);throw new TypeError("Invalid attempt to destructure non-iterable instance")}}(),l=function(){function o(o,e){for(var t=0;t<e.length;t++){var n=e[t];n.enumerable=n.enumerable||!1,n.configurable=!0,"value"in n&&(n.writable=!0),Object.defineProperty(o,n.key,n)}}return function(e,t,n){return t&&o(e.prototype,t),n&&o(e,n),e}}(),s=window.$,a=function(){function o(){if(n(this,o),0!==s("#position-filters").length){var e=this;e.$panelSelection=s("#modules-position-selection-panel"),e.$panelSelectionSingleSelection=s("#modules-position-single-selection"),e.$panelSelectionMultipleSelection=s("#modules-position-multiple-selection"),e.$panelSelectionOriginalY=e.$panelSelection.offset().top,e.$showModules=s("#show-modules"),e.$modulesList=s(".modules-position-checkbox"),e.$hookPosition=s("#hook-position"),e.$hookSearch=s("#hook-search"),e.$modulePositionsForm=s("#module-positions-form"),e.$moduleUnhookButton=s("#unhook-button-position-bottom"),e.$moduleButtonsUpdate=s(".module-buttons-update .btn"),e.handleList(),e.handleSortable(),s('input[name="form[general][enable_tos]"]').on("change",function(){return e.handle()})}}return l(o,[{key:"handleList",value:function(){var o=this;s(window).on("scroll",function(){var e=s(window).scrollTop();o.$panelSelection.css("top",e<20?0:e-o.$panelSelectionOriginalY)}),o.$modulesList.on("change",function(){var e=o.$modulesList.filter(":checked").length;0===e?(o.$moduleUnhookButton.hide(),o.$panelSelection.hide(),o.$panelSelectionSingleSelection.hide(),o.$panelSelectionMultipleSelection.hide()):1===e?(o.$moduleUnhookButton.show(),o.$panelSelection.show(),o.$panelSelectionSingleSelection.show(),o.$panelSelectionMultipleSelection.hide()):(o.$moduleUnhookButton.show(),o.$panelSelection.show(),o.$panelSelectionSingleSelection.hide(),o.$panelSelectionMultipleSelection.show(),s("#modules-position-selection-count").html(e))}),o.$panelSelection.find("button").click(function(){s('button[name="unhookform"]').trigger("click")}),o.$hooksList=[],s("section.hook-panel .hook-name").each(function(){var e=s(this);o.$hooksList.push({title:e.html(),element:e,container:e.parents(".hook-panel")})}),o.$showModules.select2(),o.$showModules.on("change",function(){o.modulesPositionFilterHooks()}),o.$hookPosition.on("change",function(){o.modulesPositionFilterHooks()}),o.$hookSearch.on("input",function(){o.modulesPositionFilterHooks()}),o.$hookSearch.on("keypress",function(o){return 13!==(o.keyCode||o.which)}),s(".hook-checker").on("click",function(){s(".hook"+s(this).data("hook-id")).prop("checked",s(this).prop("checked"))}),o.$modulesList.on("click",function(){s("#Ghook"+s(this).data("hook-id")).prop("checked",0===s(".hook"+s(this).data("hook-id")+":not(:checked)").length)}),o.$moduleButtonsUpdate.on("click",function(){var e=s(this),t=e.closest(".module-item"),n=void 0;return n=e.data("way")?t.next(".module-item"):t.prev(".module-item"),0!==n.length&&(e.data("way")?t.insertAfter(n):t.insertBefore(n),o.updatePositions({hookId:e.data("hook-id"),moduleId:e.data("module-id"),way:e.data("way"),positions:[]},e.closest("ul")),!1)})}},{key:"handleSortable",value:function(){var o=this;s(".sortable").sortable({forcePlaceholderSize:!0,start:function(o,e){s(this).data("previous-index",e.item.index())},update:function(e,t){var n=t.item.attr("id").split("_"),l=i(n,2),a=l[0],r=l[1],c={hookId:a,moduleId:r,way:s(this).data("previous-index")<t.item.index()?1:0,positions:[]};o.updatePositions(c,s(e.target))}})}},{key:"updatePositions",value:function(o,e){var t=this;s.each(e.children(),function(e,t){o.positions.push(s(t).attr("id"))}),s.ajax({type:"POST",headers:{"cache-control":"no-cache"},url:t.$modulePositionsForm.data("update-url"),data:o,success:function(){var o=0;s.each(e.children(),function(e,t){console.log(s(t).find(".index-position")),s(t).find(".index-position").html(++o)}),window.showSuccessMessage(window.update_success_msg)}})}},{key:"modulesPositionFilterHooks",value:function(){for(var o=this,e=o.$hookSearch.val(),t=o.$showModules.val(),n=new RegExp("("+e+")","gi"),i=0;i<o.$hooksList.length;i++)o.$hooksList[i].container.toggle(""===e&&"all"===t),o.$hooksList[i].element.html(o.$hooksList[i].title),o.$hooksList[i].container.find(".module-item").removeClass("highlight");if(""!==e||"all"!==t){for(var l=s(),a=s(),r=void 0,c=0;c<o.$hooksList.length;c++)"all"!==t&&(r=o.$hooksList[c].container.find(".module-position-"+t),r.length>0&&(l=l.add(o.$hooksList[c].container),r.addClass("highlight"))),""!==e&&-1!==o.$hooksList[c].title.toLowerCase().search(e.toLowerCase())&&(a=a.add(o.$hooksList[c].container),o.$hooksList[c].element.html(o.$hooksList[c].title.replace(n,'<span class="highlight">$1</span>')));"all"===t&&""!==e?a.show():""===e&&"all"!==t?l.show():a.filter(l).show()}if(!o.$hookPosition.prop("checked"))for(var u=0;u<o.$hooksList.length;u++)o.$hooksList[u].container.is(".hook-position")&&o.$hooksList[u].container.hide()}}]),o}();e.default=a},359:function(o,e,t){"use strict";var n=t(273),i=function(o){return o&&o.__esModule?o:{default:o}}(n);/**
=======
window.improve_design_positions=function(o){function e(n){if(t[n])return t[n].exports;var i=t[n]={i:n,l:!1,exports:{}};return o[n].call(i.exports,i,i.exports,e),i.l=!0,i.exports}var t={};return e.m=o,e.c=t,e.i=function(o){return o},e.d=function(o,t,n){e.o(o,t)||Object.defineProperty(o,t,{configurable:!1,enumerable:!0,get:n})},e.n=function(o){var t=o&&o.__esModule?function(){return o.default}:function(){return o};return e.d(t,"a",t),t},e.o=function(o,e){return Object.prototype.hasOwnProperty.call(o,e)},e.p="",e(e.s=360)}({274:function(o,e,t){"use strict";function n(o,e){if(!(o instanceof e))throw new TypeError("Cannot call a class as a function")}Object.defineProperty(e,"__esModule",{value:!0});var i=function(){function o(o,e){var t=[],n=!0,i=!1,l=void 0;try{for(var s,a=o[Symbol.iterator]();!(n=(s=a.next()).done)&&(t.push(s.value),!e||t.length!==e);n=!0);}catch(o){i=!0,l=o}finally{try{!n&&a.return&&a.return()}finally{if(i)throw l}}return t}return function(e,t){if(Array.isArray(e))return e;if(Symbol.iterator in Object(e))return o(e,t);throw new TypeError("Invalid attempt to destructure non-iterable instance")}}(),l=function(){function o(o,e){for(var t=0;t<e.length;t++){var n=e[t];n.enumerable=n.enumerable||!1,n.configurable=!0,"value"in n&&(n.writable=!0),Object.defineProperty(o,n.key,n)}}return function(e,t,n){return t&&o(e.prototype,t),n&&o(e,n),e}}(),s=window.$,a=function(){function o(){if(n(this,o),0!==s("#position-filters").length){var e=this;e.$panelSelection=s("#modules-position-selection-panel"),e.$panelSelectionSingleSelection=s("#modules-position-single-selection"),e.$panelSelectionMultipleSelection=s("#modules-position-multiple-selection"),e.$panelSelectionOriginalY=e.$panelSelection.offset().top,e.$showModules=s("#show-modules"),e.$modulesList=s(".modules-position-checkbox"),e.$hookPosition=s("#hook-position"),e.$hookSearch=s("#hook-search"),e.$modulePositionsForm=s("#module-positions-form"),e.$moduleUnhookButton=s("#unhook-button-position-bottom"),e.$moduleButtonsUpdate=s(".module-buttons-update .btn"),e.handleList(),e.handleSortable(),s('input[name="form[general][enable_tos]"]').on("change",function(){return e.handle()})}}return l(o,[{key:"handleList",value:function(){var o=this;s(window).on("scroll",function(){var e=s(window).scrollTop();o.$panelSelection.css("top",e<20?0:e-o.$panelSelectionOriginalY)}),o.$modulesList.on("change",function(){var e=o.$modulesList.filter(":checked").length;0===e?(o.$moduleUnhookButton.hide(),o.$panelSelection.hide(),o.$panelSelectionSingleSelection.hide(),o.$panelSelectionMultipleSelection.hide()):1===e?(o.$moduleUnhookButton.show(),o.$panelSelection.show(),o.$panelSelectionSingleSelection.show(),o.$panelSelectionMultipleSelection.hide()):(o.$moduleUnhookButton.show(),o.$panelSelection.show(),o.$panelSelectionSingleSelection.hide(),o.$panelSelectionMultipleSelection.show(),s("#modules-position-selection-count").html(e))}),o.$panelSelection.find("button").click(function(){s('button[name="unhookform"]').trigger("click")}),o.$hooksList=[],s("section.hook-panel .hook-name").each(function(){var e=s(this);o.$hooksList.push({title:e.html(),element:e,container:e.parents(".hook-panel")})}),o.$showModules.select2(),o.$showModules.on("change",function(){o.modulesPositionFilterHooks()}),o.$hookPosition.on("change",function(){o.modulesPositionFilterHooks()}),o.$hookSearch.on("input",function(){o.modulesPositionFilterHooks()}),o.$hookSearch.on("keypress",function(o){return 13!==(o.keyCode||o.which)}),s(".hook-checker").on("click",function(){s(".hook"+s(this).data("hook-id")).prop("checked",s(this).prop("checked"))}),o.$modulesList.on("click",function(){s("#Ghook"+s(this).data("hook-id")).prop("checked",0===s(".hook"+s(this).data("hook-id")+":not(:checked)").length)}),o.$moduleButtonsUpdate.on("click",function(){var e=s(this),t=e.closest(".module-item"),n=void 0;return n=e.data("way")?t.next(".module-item"):t.prev(".module-item"),0!==n.length&&(e.data("way")?t.insertAfter(n):t.insertBefore(n),o.updatePositions({hookId:e.data("hook-id"),moduleId:e.data("module-id"),way:e.data("way"),positions:[]},e.closest("ul")),!1)})}},{key:"handleSortable",value:function(){var o=this;s(".sortable").sortable({forcePlaceholderSize:!0,start:function(o,e){s(this).data("previous-index",e.item.index())},update:function(e,t){var n=t.item.attr("id").split("_"),l=i(n,2),a=l[0],r=l[1],c={hookId:a,moduleId:r,way:s(this).data("previous-index")<t.item.index()?1:0,positions:[]};o.updatePositions(c,s(e.target))}})}},{key:"updatePositions",value:function(o,e){var t=this;s.each(e.children(),function(e,t){o.positions.push(s(t).attr("id"))}),s.ajax({type:"POST",headers:{"cache-control":"no-cache"},url:t.$modulePositionsForm.data("update-url"),data:o,success:function(){var o=0;s.each(e.children(),function(e,t){console.log(s(t).find(".index-position")),s(t).find(".index-position").html(++o)}),window.showSuccessMessage(window.update_success_msg)}})}},{key:"modulesPositionFilterHooks",value:function(){for(var o=this,e=o.$hookSearch.val(),t=o.$showModules.val(),n=new RegExp("("+e+")","gi"),i=0;i<o.$hooksList.length;i++)o.$hooksList[i].container.toggle(""===e&&"all"===t),o.$hooksList[i].element.html(o.$hooksList[i].title),o.$hooksList[i].container.find(".module-item").removeClass("highlight");if(""!==e||"all"!==t){for(var l=s(),a=s(),r=void 0,c=0;c<o.$hooksList.length;c++)"all"!==t&&(r=o.$hooksList[c].container.find(".module-position-"+t),r.length>0&&(l=l.add(o.$hooksList[c].container),r.addClass("highlight"))),""!==e&&-1!==o.$hooksList[c].title.toLowerCase().search(e.toLowerCase())&&(a=a.add(o.$hooksList[c].container),o.$hooksList[c].element.html(o.$hooksList[c].title.replace(n,'<span class="highlight">$1</span>')));"all"===t&&""!==e?a.show():""===e&&"all"!==t?l.show():a.filter(l).show()}if(!o.$hookPosition.prop("checked"))for(var u=0;u<o.$hooksList.length;u++)o.$hooksList[u].container.is(".hook-position")&&o.$hooksList[u].container.hide()}}]),o}();e.default=a},360:function(o,e,t){"use strict";var n=t(274),i=function(o){return o&&o.__esModule?o:{default:o}}(n);/**
>>>>>>> Rebuilds assets
<<<<<<< HEAD
=======
=======
window.improve_design_positions=function(o){function e(n){if(t[n])return t[n].exports;var i=t[n]={i:n,l:!1,exports:{}};return o[n].call(i.exports,i,i.exports,e),i.l=!0,i.exports}var t={};return e.m=o,e.c=t,e.i=function(o){return o},e.d=function(o,t,n){e.o(o,t)||Object.defineProperty(o,t,{configurable:!1,enumerable:!0,get:n})},e.n=function(o){var t=o&&o.__esModule?function(){return o.default}:function(){return o};return e.d(t,"a",t),t},e.o=function(o,e){return Object.prototype.hasOwnProperty.call(o,e)},e.p="",e(e.s=360)}({275:function(o,e,t){"use strict";function n(o,e){if(!(o instanceof e))throw new TypeError("Cannot call a class as a function")}Object.defineProperty(e,"__esModule",{value:!0});var i=function(){function o(o,e){var t=[],n=!0,i=!1,l=void 0;try{for(var s,a=o[Symbol.iterator]();!(n=(s=a.next()).done)&&(t.push(s.value),!e||t.length!==e);n=!0);}catch(o){i=!0,l=o}finally{try{!n&&a.return&&a.return()}finally{if(i)throw l}}return t}return function(e,t){if(Array.isArray(e))return e;if(Symbol.iterator in Object(e))return o(e,t);throw new TypeError("Invalid attempt to destructure non-iterable instance")}}(),l=function(){function o(o,e){for(var t=0;t<e.length;t++){var n=e[t];n.enumerable=n.enumerable||!1,n.configurable=!0,"value"in n&&(n.writable=!0),Object.defineProperty(o,n.key,n)}}return function(e,t,n){return t&&o(e.prototype,t),n&&o(e,n),e}}(),s=window.$,a=function(){function o(){if(n(this,o),0!==s("#position-filters").length){var e=this;e.$panelSelection=s("#modules-position-selection-panel"),e.$panelSelectionSingleSelection=s("#modules-position-single-selection"),e.$panelSelectionMultipleSelection=s("#modules-position-multiple-selection"),e.$panelSelectionOriginalY=e.$panelSelection.offset().top,e.$showModules=s("#show-modules"),e.$modulesList=s(".modules-position-checkbox"),e.$hookPosition=s("#hook-position"),e.$hookSearch=s("#hook-search"),e.$modulePositionsForm=s("#module-positions-form"),e.$moduleUnhookButton=s("#unhook-button-position-bottom"),e.$moduleButtonsUpdate=s(".module-buttons-update .btn"),e.handleList(),e.handleSortable(),s('input[name="form[general][enable_tos]"]').on("change",function(){return e.handle()})}}return l(o,[{key:"handleList",value:function(){var o=this;s(window).on("scroll",function(){var e=s(window).scrollTop();o.$panelSelection.css("top",e<20?0:e-o.$panelSelectionOriginalY)}),o.$modulesList.on("change",function(){var e=o.$modulesList.filter(":checked").length;0===e?(o.$moduleUnhookButton.hide(),o.$panelSelection.hide(),o.$panelSelectionSingleSelection.hide(),o.$panelSelectionMultipleSelection.hide()):1===e?(o.$moduleUnhookButton.show(),o.$panelSelection.show(),o.$panelSelectionSingleSelection.show(),o.$panelSelectionMultipleSelection.hide()):(o.$moduleUnhookButton.show(),o.$panelSelection.show(),o.$panelSelectionSingleSelection.hide(),o.$panelSelectionMultipleSelection.show(),s("#modules-position-selection-count").html(e))}),o.$panelSelection.find("button").click(function(){s('button[name="unhookform"]').trigger("click")}),o.$hooksList=[],s("section.hook-panel .hook-name").each(function(){var e=s(this);o.$hooksList.push({title:e.html(),element:e,container:e.parents(".hook-panel")})}),o.$showModules.select2(),o.$showModules.on("change",function(){o.modulesPositionFilterHooks()}),o.$hookPosition.on("change",function(){o.modulesPositionFilterHooks()}),o.$hookSearch.on("input",function(){o.modulesPositionFilterHooks()}),o.$hookSearch.on("keypress",function(o){return 13!==(o.keyCode||o.which)}),s(".hook-checker").on("click",function(){s(".hook"+s(this).data("hook-id")).prop("checked",s(this).prop("checked"))}),o.$modulesList.on("click",function(){s("#Ghook"+s(this).data("hook-id")).prop("checked",0===s(".hook"+s(this).data("hook-id")+":not(:checked)").length)}),o.$moduleButtonsUpdate.on("click",function(){var e=s(this),t=e.closest(".module-item"),n=void 0;return n=e.data("way")?t.next(".module-item"):t.prev(".module-item"),0!==n.length&&(e.data("way")?t.insertAfter(n):t.insertBefore(n),o.updatePositions({hookId:e.data("hook-id"),moduleId:e.data("module-id"),way:e.data("way"),positions:[]},e.closest("ul")),!1)})}},{key:"handleSortable",value:function(){var o=this;s(".sortable").sortable({forcePlaceholderSize:!0,start:function(o,e){s(this).data("previous-index",e.item.index())},update:function(e,t){var n=t.item.attr("id").split("_"),l=i(n,2),a=l[0],r=l[1],c={hookId:a,moduleId:r,way:s(this).data("previous-index")<t.item.index()?1:0,positions:[]};o.updatePositions(c,s(e.target))}})}},{key:"updatePositions",value:function(o,e){var t=this;s.each(e.children(),function(e,t){o.positions.push(s(t).attr("id"))}),s.ajax({type:"POST",headers:{"cache-control":"no-cache"},url:t.$modulePositionsForm.data("update-url"),data:o,success:function(){var o=0;s.each(e.children(),function(e,t){console.log(s(t).find(".index-position")),s(t).find(".index-position").html(++o)}),window.showSuccessMessage(window.update_success_msg)}})}},{key:"modulesPositionFilterHooks",value:function(){for(var o=this,e=o.$hookSearch.val(),t=o.$showModules.val(),n=new RegExp("("+e+")","gi"),i=0;i<o.$hooksList.length;i++)o.$hooksList[i].container.toggle(""===e&&"all"===t),o.$hooksList[i].element.html(o.$hooksList[i].title),o.$hooksList[i].container.find(".module-item").removeClass("highlight");if(""!==e||"all"!==t){for(var l=s(),a=s(),r=void 0,c=0;c<o.$hooksList.length;c++)"all"!==t&&(r=o.$hooksList[c].container.find(".module-position-"+t),r.length>0&&(l=l.add(o.$hooksList[c].container),r.addClass("highlight"))),""!==e&&-1!==o.$hooksList[c].title.toLowerCase().search(e.toLowerCase())&&(a=a.add(o.$hooksList[c].container),o.$hooksList[c].element.html(o.$hooksList[c].title.replace(n,'<span class="highlight">$1</span>')));"all"===t&&""!==e?a.show():""===e&&"all"!==t?l.show():a.filter(l).show()}if(!o.$hookPosition.prop("checked"))for(var u=0;u<o.$hooksList.length;u++)o.$hooksList[u].container.is(".hook-position")&&o.$hooksList[u].container.hide()}}]),o}();e.default=a},360:function(o,e,t){"use strict";var n=t(275),i=function(o){return o&&o.__esModule?o:{default:o}}(n);/**
>>>>>>> Rebuilds assets
>>>>>>> Rebuilds assets
=======
window.improve_design_positions=function(o){function e(n){if(t[n])return t[n].exports;var i=t[n]={i:n,l:!1,exports:{}};return o[n].call(i.exports,i,i.exports,e),i.l=!0,i.exports}var t={};return e.m=o,e.c=t,e.i=function(o){return o},e.d=function(o,t,n){e.o(o,t)||Object.defineProperty(o,t,{configurable:!1,enumerable:!0,get:n})},e.n=function(o){var t=o&&o.__esModule?function(){return o.default}:function(){return o};return e.d(t,"a",t),t},e.o=function(o,e){return Object.prototype.hasOwnProperty.call(o,e)},e.p="",e(e.s=362)}({276:function(o,e,t){"use strict";function n(o,e){if(!(o instanceof e))throw new TypeError("Cannot call a class as a function")}Object.defineProperty(e,"__esModule",{value:!0});var i=function(){function o(o,e){var t=[],n=!0,i=!1,l=void 0;try{for(var s,a=o[Symbol.iterator]();!(n=(s=a.next()).done)&&(t.push(s.value),!e||t.length!==e);n=!0);}catch(o){i=!0,l=o}finally{try{!n&&a.return&&a.return()}finally{if(i)throw l}}return t}return function(e,t){if(Array.isArray(e))return e;if(Symbol.iterator in Object(e))return o(e,t);throw new TypeError("Invalid attempt to destructure non-iterable instance")}}(),l=function(){function o(o,e){for(var t=0;t<e.length;t++){var n=e[t];n.enumerable=n.enumerable||!1,n.configurable=!0,"value"in n&&(n.writable=!0),Object.defineProperty(o,n.key,n)}}return function(e,t,n){return t&&o(e.prototype,t),n&&o(e,n),e}}(),s=window.$,a=function(){function o(){if(n(this,o),0!==s("#position-filters").length){var e=this;e.$panelSelection=s("#modules-position-selection-panel"),e.$panelSelectionSingleSelection=s("#modules-position-single-selection"),e.$panelSelectionMultipleSelection=s("#modules-position-multiple-selection"),e.$panelSelectionOriginalY=e.$panelSelection.offset().top,e.$showModules=s("#show-modules"),e.$modulesList=s(".modules-position-checkbox"),e.$hookPosition=s("#hook-position"),e.$hookSearch=s("#hook-search"),e.$modulePositionsForm=s("#module-positions-form"),e.$moduleUnhookButton=s("#unhook-button-position-bottom"),e.$moduleButtonsUpdate=s(".module-buttons-update .btn"),e.handleList(),e.handleSortable(),s('input[name="form[general][enable_tos]"]').on("change",function(){return e.handle()})}}return l(o,[{key:"handleList",value:function(){var o=this;s(window).on("scroll",function(){var e=s(window).scrollTop();o.$panelSelection.css("top",e<20?0:e-o.$panelSelectionOriginalY)}),o.$modulesList.on("change",function(){var e=o.$modulesList.filter(":checked").length;0===e?(o.$moduleUnhookButton.hide(),o.$panelSelection.hide(),o.$panelSelectionSingleSelection.hide(),o.$panelSelectionMultipleSelection.hide()):1===e?(o.$moduleUnhookButton.show(),o.$panelSelection.show(),o.$panelSelectionSingleSelection.show(),o.$panelSelectionMultipleSelection.hide()):(o.$moduleUnhookButton.show(),o.$panelSelection.show(),o.$panelSelectionSingleSelection.hide(),o.$panelSelectionMultipleSelection.show(),s("#modules-position-selection-count").html(e))}),o.$panelSelection.find("button").click(function(){s('button[name="unhookform"]').trigger("click")}),o.$hooksList=[],s("section.hook-panel .hook-name").each(function(){var e=s(this);o.$hooksList.push({title:e.html(),element:e,container:e.parents(".hook-panel")})}),o.$showModules.select2(),o.$showModules.on("change",function(){o.modulesPositionFilterHooks()}),o.$hookPosition.on("change",function(){o.modulesPositionFilterHooks()}),o.$hookSearch.on("input",function(){o.modulesPositionFilterHooks()}),o.$hookSearch.on("keypress",function(o){return 13!==(o.keyCode||o.which)}),s(".hook-checker").on("click",function(){s(".hook"+s(this).data("hook-id")).prop("checked",s(this).prop("checked"))}),o.$modulesList.on("click",function(){s("#Ghook"+s(this).data("hook-id")).prop("checked",0===s(".hook"+s(this).data("hook-id")+":not(:checked)").length)}),o.$moduleButtonsUpdate.on("click",function(){var e=s(this),t=e.closest(".module-item"),n=void 0;return n=e.data("way")?t.next(".module-item"):t.prev(".module-item"),0!==n.length&&(e.data("way")?t.insertAfter(n):t.insertBefore(n),o.updatePositions({hookId:e.data("hook-id"),moduleId:e.data("module-id"),way:e.data("way"),positions:[]},e.closest("ul")),!1)})}},{key:"handleSortable",value:function(){var o=this;s(".sortable").sortable({forcePlaceholderSize:!0,start:function(o,e){s(this).data("previous-index",e.item.index())},update:function(e,t){var n=t.item.attr("id").split("_"),l=i(n,2),a=l[0],r=l[1],c={hookId:a,moduleId:r,way:s(this).data("previous-index")<t.item.index()?1:0,positions:[]};o.updatePositions(c,s(e.target))}})}},{key:"updatePositions",value:function(o,e){var t=this;s.each(e.children(),function(e,t){o.positions.push(s(t).attr("id"))}),s.ajax({type:"POST",headers:{"cache-control":"no-cache"},url:t.$modulePositionsForm.data("update-url"),data:o,success:function(){var o=0;s.each(e.children(),function(e,t){console.log(s(t).find(".index-position")),s(t).find(".index-position").html(++o)}),window.showSuccessMessage(window.update_success_msg)}})}},{key:"modulesPositionFilterHooks",value:function(){for(var o=this,e=o.$hookSearch.val(),t=o.$showModules.val(),n=new RegExp("("+e+")","gi"),i=0;i<o.$hooksList.length;i++)o.$hooksList[i].container.toggle(""===e&&"all"===t),o.$hooksList[i].element.html(o.$hooksList[i].title),o.$hooksList[i].container.find(".module-item").removeClass("highlight");if(""!==e||"all"!==t){for(var l=s(),a=s(),r=void 0,c=0;c<o.$hooksList.length;c++)"all"!==t&&(r=o.$hooksList[c].container.find(".module-position-"+t),r.length>0&&(l=l.add(o.$hooksList[c].container),r.addClass("highlight"))),""!==e&&-1!==o.$hooksList[c].title.toLowerCase().search(e.toLowerCase())&&(a=a.add(o.$hooksList[c].container),o.$hooksList[c].element.html(o.$hooksList[c].title.replace(n,'<span class="highlight">$1</span>')));"all"===t&&""!==e?a.show():""===e&&"all"!==t?l.show():a.filter(l).show()}if(!o.$hookPosition.prop("checked"))for(var u=0;u<o.$hooksList.length;u++)o.$hooksList[u].container.is(".hook-position")&&o.$hooksList[u].container.hide()}}]),o}();e.default=a},362:function(o,e,t){"use strict";var n=t(276),i=function(o){return o&&o.__esModule?o:{default:o}}(n);/**
>>>>>>> Rebuild assets
=======
window.improve_design_positions=function(o){function e(n){if(t[n])return t[n].exports;var i=t[n]={i:n,l:!1,exports:{}};return o[n].call(i.exports,i,i.exports,e),i.l=!0,i.exports}var t={};return e.m=o,e.c=t,e.i=function(o){return o},e.d=function(o,t,n){e.o(o,t)||Object.defineProperty(o,t,{configurable:!1,enumerable:!0,get:n})},e.n=function(o){var t=o&&o.__esModule?function(){return o.default}:function(){return o};return e.d(t,"a",t),t},e.o=function(o,e){return Object.prototype.hasOwnProperty.call(o,e)},e.p="",e(e.s=364)}({279:function(o,e,t){"use strict";function n(o,e){if(!(o instanceof e))throw new TypeError("Cannot call a class as a function")}Object.defineProperty(e,"__esModule",{value:!0});var i=function(){function o(o,e){var t=[],n=!0,i=!1,l=void 0;try{for(var s,a=o[Symbol.iterator]();!(n=(s=a.next()).done)&&(t.push(s.value),!e||t.length!==e);n=!0);}catch(o){i=!0,l=o}finally{try{!n&&a.return&&a.return()}finally{if(i)throw l}}return t}return function(e,t){if(Array.isArray(e))return e;if(Symbol.iterator in Object(e))return o(e,t);throw new TypeError("Invalid attempt to destructure non-iterable instance")}}(),l=function(){function o(o,e){for(var t=0;t<e.length;t++){var n=e[t];n.enumerable=n.enumerable||!1,n.configurable=!0,"value"in n&&(n.writable=!0),Object.defineProperty(o,n.key,n)}}return function(e,t,n){return t&&o(e.prototype,t),n&&o(e,n),e}}(),s=window.$,a=function(){function o(){if(n(this,o),0!==s("#position-filters").length){var e=this;e.$panelSelection=s("#modules-position-selection-panel"),e.$panelSelectionSingleSelection=s("#modules-position-single-selection"),e.$panelSelectionMultipleSelection=s("#modules-position-multiple-selection"),e.$panelSelectionOriginalY=e.$panelSelection.offset().top,e.$showModules=s("#show-modules"),e.$modulesList=s(".modules-position-checkbox"),e.$hookPosition=s("#hook-position"),e.$hookSearch=s("#hook-search"),e.$modulePositionsForm=s("#module-positions-form"),e.$moduleUnhookButton=s("#unhook-button-position-bottom"),e.$moduleButtonsUpdate=s(".module-buttons-update .btn"),e.handleList(),e.handleSortable(),s('input[name="form[general][enable_tos]"]').on("change",function(){return e.handle()})}}return l(o,[{key:"handleList",value:function(){var o=this;s(window).on("scroll",function(){var e=s(window).scrollTop();o.$panelSelection.css("top",e<20?0:e-o.$panelSelectionOriginalY)}),o.$modulesList.on("change",function(){var e=o.$modulesList.filter(":checked").length;0===e?(o.$moduleUnhookButton.hide(),o.$panelSelection.hide(),o.$panelSelectionSingleSelection.hide(),o.$panelSelectionMultipleSelection.hide()):1===e?(o.$moduleUnhookButton.show(),o.$panelSelection.show(),o.$panelSelectionSingleSelection.show(),o.$panelSelectionMultipleSelection.hide()):(o.$moduleUnhookButton.show(),o.$panelSelection.show(),o.$panelSelectionSingleSelection.hide(),o.$panelSelectionMultipleSelection.show(),s("#modules-position-selection-count").html(e))}),o.$panelSelection.find("button").click(function(){s('button[name="unhookform"]').trigger("click")}),o.$hooksList=[],s("section.hook-panel .hook-name").each(function(){var e=s(this);o.$hooksList.push({title:e.html(),element:e,container:e.parents(".hook-panel")})}),o.$showModules.select2(),o.$showModules.on("change",function(){o.modulesPositionFilterHooks()}),o.$hookPosition.on("change",function(){o.modulesPositionFilterHooks()}),o.$hookSearch.on("input",function(){o.modulesPositionFilterHooks()}),o.$hookSearch.on("keypress",function(o){return 13!==(o.keyCode||o.which)}),s(".hook-checker").on("click",function(){s(".hook"+s(this).data("hook-id")).prop("checked",s(this).prop("checked"))}),o.$modulesList.on("click",function(){s("#Ghook"+s(this).data("hook-id")).prop("checked",0===s(".hook"+s(this).data("hook-id")+":not(:checked)").length)}),o.$moduleButtonsUpdate.on("click",function(){var e=s(this),t=e.closest(".module-item"),n=void 0;return n=e.data("way")?t.next(".module-item"):t.prev(".module-item"),0!==n.length&&(e.data("way")?t.insertAfter(n):t.insertBefore(n),o.updatePositions({hookId:e.data("hook-id"),moduleId:e.data("module-id"),way:e.data("way"),positions:[]},e.closest("ul")),!1)})}},{key:"handleSortable",value:function(){var o=this;s(".sortable").sortable({forcePlaceholderSize:!0,start:function(o,e){s(this).data("previous-index",e.item.index())},update:function(e,t){var n=t.item.attr("id").split("_"),l=i(n,2),a=l[0],r=l[1],c={hookId:a,moduleId:r,way:s(this).data("previous-index")<t.item.index()?1:0,positions:[]};o.updatePositions(c,s(e.target))}})}},{key:"updatePositions",value:function(o,e){var t=this;s.each(e.children(),function(e,t){o.positions.push(s(t).attr("id"))}),s.ajax({type:"POST",headers:{"cache-control":"no-cache"},url:t.$modulePositionsForm.data("update-url"),data:o,success:function(){var o=0;s.each(e.children(),function(e,t){console.log(s(t).find(".index-position")),s(t).find(".index-position").html(++o)}),window.showSuccessMessage(window.update_success_msg)}})}},{key:"modulesPositionFilterHooks",value:function(){for(var o=this,e=o.$hookSearch.val(),t=o.$showModules.val(),n=new RegExp("("+e+")","gi"),i=0;i<o.$hooksList.length;i++)o.$hooksList[i].container.toggle(""===e&&"all"===t),o.$hooksList[i].element.html(o.$hooksList[i].title),o.$hooksList[i].container.find(".module-item").removeClass("highlight");if(""!==e||"all"!==t){for(var l=s(),a=s(),r=void 0,c=0;c<o.$hooksList.length;c++)"all"!==t&&(r=o.$hooksList[c].container.find(".module-position-"+t),r.length>0&&(l=l.add(o.$hooksList[c].container),r.addClass("highlight"))),""!==e&&-1!==o.$hooksList[c].title.toLowerCase().search(e.toLowerCase())&&(a=a.add(o.$hooksList[c].container),o.$hooksList[c].element.html(o.$hooksList[c].title.replace(n,'<span class="highlight">$1</span>')));"all"===t&&""!==e?a.show():""===e&&"all"!==t?l.show():a.filter(l).show()}if(!o.$hookPosition.prop("checked"))for(var u=0;u<o.$hooksList.length;u++)o.$hooksList[u].container.is(".hook-position")&&o.$hooksList[u].container.hide()}}]),o}();e.default=a},364:function(o,e,t){"use strict";var n=t(279),i=function(o){return o&&o.__esModule?o:{default:o}}(n);/**
>>>>>>> Rebuild assets
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
(0,window.$)(function(){new i.default})}});