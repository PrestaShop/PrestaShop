/******/!function(e){// webpackBootstrap
/******/
<<<<<<< HEAD
function t(e){/******/
delete installedChunks[e]}function n(e){var t=document.getElementsByTagName("head")[0],n=document.createElement("script");n.type="text/javascript",n.charset="utf-8",n.src=p.p+""+e+"."+w+".hot-update.js",t.appendChild(n)}function r(){return new Promise(function(e,t){if("undefined"==typeof XMLHttpRequest)return t(new Error("No browser support"));try{var n=new XMLHttpRequest,r=p.p+""+w+".hot-update.json";n.open("GET",r,!0),n.timeout=1e4,n.send(null)}catch(e){return t(e)}n.onreadystatechange=function(){if(4===n.readyState)if(0===n.status)t(new Error("Manifest request to "+r+" timed out."));else if(404===n.status)e();else if(200!==n.status&&304!==n.status)t(new Error("Manifest request to "+r+" failed."));else{try{var o=JSON.parse(n.responseText)}catch(e){return void t(e)}e(o)}}})}function o(e){var t=A[e];if(!t)return p;var n=function(n){return t.hot.active?(A[n]?A[n].parents.indexOf(e)<0&&A[n].parents.push(e):(F=[e],y=n),t.children.indexOf(n)<0&&t.children.push(n)):F=[],p(n)};for(var r in p)Object.prototype.hasOwnProperty.call(p,r)&&"e"!==r&&Object.defineProperty(n,r,function(e){return{configurable:!0,enumerable:!0,get:function(){return p[e]},set:function(t){p[e]=t}}}(r));return n.e=function(e){function t(){I--,"prepare"===O&&(_[e]||d(e),0===I&&0===E&&u())}return"ready"===O&&l("prepare"),I++,p.e(e).then(t,function(e){throw t(),e})},n}function i(e){var t={_acceptedDependencies:{},_declinedDependencies:{},_selfAccepted:!1,_selfDeclined:!1,_disposeHandlers:[],_main:y!==e,active:!0,accept:function(e,n){if(void 0===e)t._selfAccepted=!0;else if("function"==typeof e)t._selfAccepted=e;else if("object"==typeof e)for(var r=0;r<e.length;r++)t._acceptedDependencies[e[r]]=n||function(){};else t._acceptedDependencies[e]=n||function(){}},decline:function(e){if(void 0===e)t._selfDeclined=!0;else if("object"==typeof e)for(var n=0;n<e.length;n++)t._declinedDependencies[e[n]]=!0;else t._declinedDependencies[e]=!0},dispose:function(e){t._disposeHandlers.push(e)},addDisposeHandler:function(e){t._disposeHandlers.push(e)},removeDisposeHandler:function(e){var n=t._disposeHandlers.indexOf(e);n>=0&&t._disposeHandlers.splice(n,1)},check:s,apply:f,status:function(e){if(!e)return O;H.push(e)},addStatusHandler:function(e){H.push(e)},removeStatusHandler:function(e){var t=H.indexOf(e);t>=0&&H.splice(t,1)},data:j[e]};return y=void 0,t}function l(e){O=e;for(var t=0;t<H.length;t++)H[t].call(null,e)}function a(e){return+e+""===e?+e:e}function s(e){if("idle"!==O)throw new Error("check() is only allowed in idle status");return b=e,l("check"),r().then(function(e){if(!e)return l("idle"),null;x={},_={},D=e.c,g=e.h,l("prepare");var t=new Promise(function(e,t){v={resolve:e,reject:t}});m={};return d(4),"prepare"===O&&0===I&&0===E&&u(),t})}function c(e,t){if(D[e]&&x[e]){x[e]=!1;for(var n in t)Object.prototype.hasOwnProperty.call(t,n)&&(m[n]=t[n]);0==--E&&0===I&&u()}}function d(e){D[e]?(x[e]=!0,E++,n(e)):_[e]=!0}function u(){l("ready");var e=v;if(v=null,e)if(b)f(b).then(function(t){e.resolve(t)},function(t){e.reject(t)});else{var t=[];for(var n in m)Object.prototype.hasOwnProperty.call(m,n)&&t.push(a(n));e.resolve(t)}}function f(n){function r(e,t){for(var n=0;n<t.length;n++){var r=t[n];e.indexOf(r)<0&&e.push(r)}}if("ready"!==O)throw new Error("apply() is only allowed in ready status");n=n||{};var o,i,s,c,d,u={},f=[],h={},y=function(){};for(var v in m)if(Object.prototype.hasOwnProperty.call(m,v)){d=a(v);var b;b=m[v]?function(e){for(var t=[e],n={},o=t.slice().map(function(e){return{chain:[e],id:e}});o.length>0;){var i=o.pop(),l=i.id,a=i.chain;if((c=A[l])&&!c.hot._selfAccepted){if(c.hot._selfDeclined)return{type:"self-declined",chain:a,moduleId:l};if(c.hot._main)return{type:"unaccepted",chain:a,moduleId:l};for(var s=0;s<c.parents.length;s++){var d=c.parents[s],u=A[d];if(u){if(u.hot._declinedDependencies[l])return{type:"declined",chain:a.concat([d]),moduleId:l,parentId:d};t.indexOf(d)>=0||(u.hot._acceptedDependencies[l]?(n[d]||(n[d]=[]),r(n[d],[l])):(delete n[d],t.push(d),o.push({chain:a.concat([d]),id:d})))}}}}return{type:"accepted",moduleId:e,outdatedModules:t,outdatedDependencies:n}}(d):{type:"disposed",moduleId:v};var k=!1,H=!1,E=!1,I="";switch(b.chain&&(I="\nUpdate propagation: "+b.chain.join(" -> ")),b.type){case"self-declined":n.onDeclined&&n.onDeclined(b),n.ignoreDeclined||(k=new Error("Aborted because of self decline: "+b.moduleId+I));break;case"declined":n.onDeclined&&n.onDeclined(b),n.ignoreDeclined||(k=new Error("Aborted because of declined dependency: "+b.moduleId+" in "+b.parentId+I));break;case"unaccepted":n.onUnaccepted&&n.onUnaccepted(b),n.ignoreUnaccepted||(k=new Error("Aborted because "+d+" is not accepted"+I));break;case"accepted":n.onAccepted&&n.onAccepted(b),H=!0;break;case"disposed":n.onDisposed&&n.onDisposed(b),E=!0;break;default:throw new Error("Unexception type "+b.type)}if(k)return l("abort"),Promise.reject(k);if(H){h[d]=m[d],r(f,b.outdatedModules);for(d in b.outdatedDependencies)Object.prototype.hasOwnProperty.call(b.outdatedDependencies,d)&&(u[d]||(u[d]=[]),r(u[d],b.outdatedDependencies[d]))}E&&(r(f,[b.moduleId]),h[d]=y)}var _=[];for(i=0;i<f.length;i++)d=f[i],A[d]&&A[d].hot._selfAccepted&&_.push({module:d,errorHandler:A[d].hot._selfAccepted});l("dispose"),Object.keys(D).forEach(function(e){!1===D[e]&&t(e)});for(var x,P=f.slice();P.length>0;)if(d=P.pop(),c=A[d]){var C={},U=c.hot._disposeHandlers;for(s=0;s<U.length;s++)(o=U[s])(C);for(j[d]=C,c.hot.active=!1,delete A[d],s=0;s<c.children.length;s++){var T=A[c.children[s]];T&&((x=T.parents.indexOf(d))>=0&&T.parents.splice(x,1))}}var B,M;for(d in u)if(Object.prototype.hasOwnProperty.call(u,d)&&(c=A[d]))for(M=u[d],s=0;s<M.length;s++)B=M[s],(x=c.children.indexOf(B))>=0&&c.children.splice(x,1);l("apply"),w=g;for(d in h)Object.prototype.hasOwnProperty.call(h,d)&&(e[d]=h[d]);var S=null;for(d in u)if(Object.prototype.hasOwnProperty.call(u,d)){c=A[d],M=u[d];var z=[];for(i=0;i<M.length;i++)B=M[i],o=c.hot._acceptedDependencies[B],z.indexOf(o)>=0||z.push(o);for(i=0;i<z.length;i++){o=z[i];try{o(M)}catch(e){n.onErrored&&n.onErrored({type:"accept-errored",moduleId:d,dependencyId:M[i],error:e}),n.ignoreErrored||S||(S=e)}}}for(i=0;i<_.length;i++){var q=_[i];d=q.module,F=[d];try{p(d)}catch(e){if("function"==typeof q.errorHandler)try{q.errorHandler(e)}catch(t){n.onErrored&&n.onErrored({type:"self-accept-error-handler-errored",moduleId:d,error:t,orginalError:e}),n.ignoreErrored||S||(S=t),S||(S=e)}else n.onErrored&&n.onErrored({type:"self-accept-errored",moduleId:d,error:e}),n.ignoreErrored||S||(S=e)}}return S?(l("fail"),Promise.reject(S)):(l("idle"),new Promise(function(e){e(f)}))}function p(t){if(A[t])return A[t].exports;var n=A[t]={i:t,l:!1,exports:{},hot:i(t),parents:(k=F,F=[],k),children:[]};return e[t].call(n.exports,n,n.exports,o(t)),n.l=!0,n.exports}var h=this.webpackHotUpdate;this.webpackHotUpdate=function(e,t){c(e,t),h&&h(e,t)};var y,v,m,g,b=!0,w="84677860fd7781d2a9ae",j={},F=[],k=[],H=[],O="idle",E=0,I=0,_={},x={},D={},A={};p.m=e,p.c=A,p.i=function(e){return e},p.d=function(e,t,n){p.o(e,t)||Object.defineProperty(e,t,{configurable:!1,enumerable:!0,get:n})},p.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return p.d(t,"a",t),t},p.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},p.p="",p.h=function(){return w},o(366)(p.s=366)}({180:function(e,t,n){"use strict";Object.defineProperty(t,"__esModule",{value:!0});var r=n(218);(0,window.$)(function(){(new r.a).init()})},217:function(e,t,n){"use strict";function r(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}var o=function(){function e(e,t){for(var n=0;n<t.length;n++){var r=t[n];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(e,r.key,r)}}return function(t,n,r){return n&&e(t.prototype,n),r&&e(t,r),t}}(),i=window.$,l=function(){function e(){r(this,e)}return o(e,[{key:"init",value:function(){i(".js-entity-select").on("change",this.toggleForm.bind(this)),this.toggleForm()}},{key:"toggleForm",value:function(){var e=i("#entity").find("option:selected"),t=parseInt(e.val()),n=e.text().toLowerCase();this.toggleEntityAlert(t),this.toggleFields(t,n),this.loadAvailableFields(t)}},{key:"toggleEntityAlert",value:function(e){var t=i(".js-entity-alert");[0,1].includes(e)?t.show():t.hide()}},{key:"toggleFields",value:function(e,t){var n=i(".js-truncate-form-group"),r=i(".js-match-ref-form-group"),o=i(".js-regenerate-form-group"),l=i(".js-force-ids-form-group"),a=i(".js-entity-name");8===e?n.hide():n.show(),[1,2].includes(e)?r.show():r.hide(),[0,1,5,6,8].includes(e)?o.show():o.hide(),[0,1,3,4,5,6,8,7].includes(e)?l.show():l.hide(),a.html(t)}},{key:"loadAvailableFields",value:function(e){i.ajax({url:"../../../ajax.php",data:{getAvailableFields:1,entity:e},dataType:"json"}).then(function(e){var t="",n=i(".js-available-fields");n.empty();for(var r=0;r<e.length;r++)t+=e[r].field;n.html(t),n.find('[data-toggle="popover"]').popover()})}}]),e}();t.a=l},218:function(e,t,n){"use strict";function r(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}var o=n(217),i=function(){function e(e,t){for(var n=0;n<t.length;n++){var r=t[n];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(e,r.key,r)}}return function(t,n,r){return n&&e(t.prototype,n),r&&e(t,r),t}}(),l=window.$,a=function(){function e(){r(this,e)}return i(e,[{key:"init",value:function(){var e=this;(new o.a).init(),l(".js-from-files-history-btn").on("click",function(){return e.showFilesHistoryHandler()}),l(".js-close-files-history-block-btn").on("click",function(){return e.closeFilesHistoryHandler()}),l("#fileHistoryTable").on("click",".js-use-file-btn",function(t){return e.useFileFromFilesHistory(t)}),l(".js-change-import-file-btn").on("click",function(){return e.changeImportFileHandler()}),l(".js-import-file").on("change",function(){return e.uploadFile()}),this.toggleSelectedFile()}},{key:"toggleSelectedFile",value:function(){var e=l("#csv").val();e.length>0&&(this.showImportFileAlert(e),this.hideFileUploadBlock())}},{key:"changeImportFileHandler",value:function(){this.hideImportFileAlert(),this.showFileUploadBlock()}},{key:"showFilesHistoryHandler",value:function(){this.showFilesHistory(),this.hideFileUploadBlock()}},{key:"closeFilesHistoryHandler",value:function(){this.closeFilesHistory(),this.showFileUploadBlock()}},{key:"showFilesHistory",value:function(){l(".js-files-history-block").removeClass("d-none")}},{key:"closeFilesHistory",value:function(){l(".js-files-history-block").addClass("d-none")}},{key:"useFileFromFilesHistory",value:function(e){var t=l(e.target).closest(".btn-group").data("file");l(".js-import-file-input").val(t),this.showImportFileAlert(t),this.closeFilesHistory()}},{key:"showImportFileAlert",value:function(e){l(".js-import-file-alert").removeClass("d-none"),l(".js-import-file").text(e)}},{key:"hideImportFileAlert",value:function(){l(".js-import-file-alert").addClass("d-none")}},{key:"hideFileUploadBlock",value:function(){l(".js-file-upload-form-group").addClass("d-none")}},{key:"showFileUploadBlock",value:function(){l(".js-file-upload-form-group").removeClass("d-none")}},{key:"enableFilesHistoryBtn",value:function(){l(".js-from-files-history-btn").removeAttr("disabled")}},{key:"showImportFileError",value:function(e,t,n){var r=l(".js-import-file-error"),o=e+" ("+this.humanizeSize(t)+")";r.find(".js-file-data").html(o),r.find(".js-error-message").html(n),r.removeClass("d-none")}},{key:"hideImportFileError",value:function(){l(".js-import-file-error").addClass("d-none")}},{key:"humanizeSize",value:function(e){return"number"!=typeof e?"":e>=1e9?(e/1e9).toFixed(2)+" GB":e>=1e6?(e/1e6).toFixed(2)+" MB":(e/1e3).toFixed(2)+" KB"}},{key:"uploadFile",value:function(){var e=this;this.hideImportFileError();var t=l("#file"),n=t.prop("files")[0];if(t.data("max-file-upload-size")<n.size)return void this.showImportFileError(n.name,n.size,"File is too large");var r=new FormData;r.append("file",n);var o=l(".js-import-form").data("file-upload-url");l.ajax({type:"POST",url:o,data:r,cache:!1,contentType:!1,processData:!1}).then(function(t){if(t.error)return void e.showImportFileError(n.name,n.size,t.error);var r=t.file.name;l(".js-import-file-input").val(r),e.showImportFileAlert(r),e.hideFileUploadBlock(),e.addFileToHistoryTable(r),e.enableFilesHistoryBtn()})}},{key:"addFileToHistoryTable",value:function(e){var t=l("#fileHistoryTable"),n=t.data("delete-file-url"),r=n+"&filename="+encodeURIComponent(e),o=t.data("download-file-url"),i=o+"&filename="+encodeURIComponent(e),a=t.find("tr:first").clone();a.removeClass("d-none"),a.find("td:first").text(e),a.find(".btn-group").attr("data-file",e),a.find(".js-delete-file-btn").attr("href",r),a.find(".js-download-file-btn").attr("href",i),t.find("tbody").append(a);var s=t.find("tr").length-1;l(".js-files-history-number").text(s)}}]),e}();t.a=a},366:function(e,t,n){e.exports=n(180)}});
=======
/******/ 	
/******/ 	
/******/ 	var hotApplyOnUpdate = true;
/******/ 	var hotCurrentHash = "e331c63050094a2ddd7b"; // eslint-disable-line no-unused-vars
/******/ 	var hotCurrentModuleData = {};
/******/ 	var hotCurrentChildModule; // eslint-disable-line no-unused-vars
/******/ 	var hotCurrentParents = []; // eslint-disable-line no-unused-vars
/******/ 	var hotCurrentParentsTemp = []; // eslint-disable-line no-unused-vars
/******/ 	
/******/ 	function hotCreateRequire(moduleId) { // eslint-disable-line no-unused-vars
/******/ 		var me = installedModules[moduleId];
/******/ 		if(!me) return __webpack_require__;
/******/ 		var fn = function(request) {
/******/ 			if(me.hot.active) {
/******/ 				if(installedModules[request]) {
/******/ 					if(installedModules[request].parents.indexOf(moduleId) < 0)
/******/ 						installedModules[request].parents.push(moduleId);
/******/ 				} else {
/******/ 					hotCurrentParents = [moduleId];
/******/ 					hotCurrentChildModule = request;
/******/ 				}
/******/ 				if(me.children.indexOf(request) < 0)
/******/ 					me.children.push(request);
/******/ 			} else {
/******/ 				console.warn("[HMR] unexpected require(" + request + ") from disposed module " + moduleId);
/******/ 				hotCurrentParents = [];
/******/ 			}
/******/ 			return __webpack_require__(request);
/******/ 		};
/******/ 		var ObjectFactory = function ObjectFactory(name) {
/******/ 			return {
/******/ 				configurable: true,
/******/ 				enumerable: true,
/******/ 				get: function() {
/******/ 					return __webpack_require__[name];
/******/ 				},
/******/ 				set: function(value) {
/******/ 					__webpack_require__[name] = value;
/******/ 				}
/******/ 			};
/******/ 		};
/******/ 		for(var name in __webpack_require__) {
/******/ 			if(Object.prototype.hasOwnProperty.call(__webpack_require__, name) && name !== "e") {
/******/ 				Object.defineProperty(fn, name, ObjectFactory(name));
/******/ 			}
/******/ 		}
/******/ 		fn.e = function(chunkId) {
/******/ 			if(hotStatus === "ready")
/******/ 				hotSetStatus("prepare");
/******/ 			hotChunksLoading++;
/******/ 			return __webpack_require__.e(chunkId).then(finishChunkLoading, function(err) {
/******/ 				finishChunkLoading();
/******/ 				throw err;
/******/ 			});
/******/ 	
/******/ 			function finishChunkLoading() {
/******/ 				hotChunksLoading--;
/******/ 				if(hotStatus === "prepare") {
/******/ 					if(!hotWaitingFilesMap[chunkId]) {
/******/ 						hotEnsureUpdateChunk(chunkId);
/******/ 					}
/******/ 					if(hotChunksLoading === 0 && hotWaitingFiles === 0) {
/******/ 						hotUpdateDownloaded();
/******/ 					}
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 		return fn;
/******/ 	}
/******/ 	
/******/ 	function hotCreateModule(moduleId) { // eslint-disable-line no-unused-vars
/******/ 		var hot = {
/******/ 			// private stuff
/******/ 			_acceptedDependencies: {},
/******/ 			_declinedDependencies: {},
/******/ 			_selfAccepted: false,
/******/ 			_selfDeclined: false,
/******/ 			_disposeHandlers: [],
/******/ 			_main: hotCurrentChildModule !== moduleId,
/******/ 	
/******/ 			// Module API
/******/ 			active: true,
/******/ 			accept: function(dep, callback) {
/******/ 				if(typeof dep === "undefined")
/******/ 					hot._selfAccepted = true;
/******/ 				else if(typeof dep === "function")
/******/ 					hot._selfAccepted = dep;
/******/ 				else if(typeof dep === "object")
/******/ 					for(var i = 0; i < dep.length; i++)
/******/ 						hot._acceptedDependencies[dep[i]] = callback || function() {};
/******/ 				else
/******/ 					hot._acceptedDependencies[dep] = callback || function() {};
/******/ 			},
/******/ 			decline: function(dep) {
/******/ 				if(typeof dep === "undefined")
/******/ 					hot._selfDeclined = true;
/******/ 				else if(typeof dep === "object")
/******/ 					for(var i = 0; i < dep.length; i++)
/******/ 						hot._declinedDependencies[dep[i]] = true;
/******/ 				else
/******/ 					hot._declinedDependencies[dep] = true;
/******/ 			},
/******/ 			dispose: function(callback) {
/******/ 				hot._disposeHandlers.push(callback);
/******/ 			},
/******/ 			addDisposeHandler: function(callback) {
/******/ 				hot._disposeHandlers.push(callback);
/******/ 			},
/******/ 			removeDisposeHandler: function(callback) {
/******/ 				var idx = hot._disposeHandlers.indexOf(callback);
/******/ 				if(idx >= 0) hot._disposeHandlers.splice(idx, 1);
/******/ 			},
/******/ 	
/******/ 			// Management API
/******/ 			check: hotCheck,
/******/ 			apply: hotApply,
/******/ 			status: function(l) {
/******/ 				if(!l) return hotStatus;
/******/ 				hotStatusHandlers.push(l);
/******/ 			},
/******/ 			addStatusHandler: function(l) {
/******/ 				hotStatusHandlers.push(l);
/******/ 			},
/******/ 			removeStatusHandler: function(l) {
/******/ 				var idx = hotStatusHandlers.indexOf(l);
/******/ 				if(idx >= 0) hotStatusHandlers.splice(idx, 1);
/******/ 			},
/******/ 	
/******/ 			//inherit from previous dispose call
/******/ 			data: hotCurrentModuleData[moduleId]
/******/ 		};
/******/ 		hotCurrentChildModule = undefined;
/******/ 		return hot;
/******/ 	}
/******/ 	
/******/ 	var hotStatusHandlers = [];
/******/ 	var hotStatus = "idle";
/******/ 	
/******/ 	function hotSetStatus(newStatus) {
/******/ 		hotStatus = newStatus;
/******/ 		for(var i = 0; i < hotStatusHandlers.length; i++)
/******/ 			hotStatusHandlers[i].call(null, newStatus);
/******/ 	}
/******/ 	
/******/ 	// while downloading
/******/ 	var hotWaitingFiles = 0;
/******/ 	var hotChunksLoading = 0;
/******/ 	var hotWaitingFilesMap = {};
/******/ 	var hotRequestedFilesMap = {};
/******/ 	var hotAvailableFilesMap = {};
/******/ 	var hotDeferred;
/******/ 	
/******/ 	// The update info
/******/ 	var hotUpdate, hotUpdateNewHash;
/******/ 	
/******/ 	function toModuleId(id) {
/******/ 		var isNumber = (+id) + "" === id;
/******/ 		return isNumber ? +id : id;
/******/ 	}
/******/ 	
/******/ 	function hotCheck(apply) {
/******/ 		if(hotStatus !== "idle") throw new Error("check() is only allowed in idle status");
/******/ 		hotApplyOnUpdate = apply;
/******/ 		hotSetStatus("check");
/******/ 		return hotDownloadManifest().then(function(update) {
/******/ 			if(!update) {
/******/ 				hotSetStatus("idle");
/******/ 				return null;
/******/ 			}
/******/ 			hotRequestedFilesMap = {};
/******/ 			hotWaitingFilesMap = {};
/******/ 			hotAvailableFilesMap = update.c;
/******/ 			hotUpdateNewHash = update.h;
/******/ 	
/******/ 			hotSetStatus("prepare");
/******/ 			var promise = new Promise(function(resolve, reject) {
/******/ 				hotDeferred = {
/******/ 					resolve: resolve,
/******/ 					reject: reject
/******/ 				};
/******/ 			});
/******/ 			hotUpdate = {};
/******/ 			var chunkId = 5;
/******/ 			{ // eslint-disable-line no-lone-blocks
/******/ 				/*globals chunkId */
/******/ 				hotEnsureUpdateChunk(chunkId);
/******/ 			}
/******/ 			if(hotStatus === "prepare" && hotChunksLoading === 0 && hotWaitingFiles === 0) {
/******/ 				hotUpdateDownloaded();
/******/ 			}
/******/ 			return promise;
/******/ 		});
/******/ 	}
/******/ 	
/******/ 	function hotAddUpdateChunk(chunkId, moreModules) { // eslint-disable-line no-unused-vars
/******/ 		if(!hotAvailableFilesMap[chunkId] || !hotRequestedFilesMap[chunkId])
/******/ 			return;
/******/ 		hotRequestedFilesMap[chunkId] = false;
/******/ 		for(var moduleId in moreModules) {
/******/ 			if(Object.prototype.hasOwnProperty.call(moreModules, moduleId)) {
/******/ 				hotUpdate[moduleId] = moreModules[moduleId];
/******/ 			}
/******/ 		}
/******/ 		if(--hotWaitingFiles === 0 && hotChunksLoading === 0) {
/******/ 			hotUpdateDownloaded();
/******/ 		}
/******/ 	}
/******/ 	
/******/ 	function hotEnsureUpdateChunk(chunkId) {
/******/ 		if(!hotAvailableFilesMap[chunkId]) {
/******/ 			hotWaitingFilesMap[chunkId] = true;
/******/ 		} else {
/******/ 			hotRequestedFilesMap[chunkId] = true;
/******/ 			hotWaitingFiles++;
/******/ 			hotDownloadUpdateChunk(chunkId);
/******/ 		}
/******/ 	}
/******/ 	
/******/ 	function hotUpdateDownloaded() {
/******/ 		hotSetStatus("ready");
/******/ 		var deferred = hotDeferred;
/******/ 		hotDeferred = null;
/******/ 		if(!deferred) return;
/******/ 		if(hotApplyOnUpdate) {
/******/ 			hotApply(hotApplyOnUpdate).then(function(result) {
/******/ 				deferred.resolve(result);
/******/ 			}, function(err) {
/******/ 				deferred.reject(err);
/******/ 			});
/******/ 		} else {
/******/ 			var outdatedModules = [];
/******/ 			for(var id in hotUpdate) {
/******/ 				if(Object.prototype.hasOwnProperty.call(hotUpdate, id)) {
/******/ 					outdatedModules.push(toModuleId(id));
/******/ 				}
/******/ 			}
/******/ 			deferred.resolve(outdatedModules);
/******/ 		}
/******/ 	}
/******/ 	
/******/ 	function hotApply(options) {
/******/ 		if(hotStatus !== "ready") throw new Error("apply() is only allowed in ready status");
/******/ 		options = options || {};
/******/ 	
/******/ 		var cb;
/******/ 		var i;
/******/ 		var j;
/******/ 		var module;
/******/ 		var moduleId;
/******/ 	
/******/ 		function getAffectedStuff(updateModuleId) {
/******/ 			var outdatedModules = [updateModuleId];
/******/ 			var outdatedDependencies = {};
/******/ 	
/******/ 			var queue = outdatedModules.slice().map(function(id) {
/******/ 				return {
/******/ 					chain: [id],
/******/ 					id: id
/******/ 				};
/******/ 			});
/******/ 			while(queue.length > 0) {
/******/ 				var queueItem = queue.pop();
/******/ 				var moduleId = queueItem.id;
/******/ 				var chain = queueItem.chain;
/******/ 				module = installedModules[moduleId];
/******/ 				if(!module || module.hot._selfAccepted)
/******/ 					continue;
/******/ 				if(module.hot._selfDeclined) {
/******/ 					return {
/******/ 						type: "self-declined",
/******/ 						chain: chain,
/******/ 						moduleId: moduleId
/******/ 					};
/******/ 				}
/******/ 				if(module.hot._main) {
/******/ 					return {
/******/ 						type: "unaccepted",
/******/ 						chain: chain,
/******/ 						moduleId: moduleId
/******/ 					};
/******/ 				}
/******/ 				for(var i = 0; i < module.parents.length; i++) {
/******/ 					var parentId = module.parents[i];
/******/ 					var parent = installedModules[parentId];
/******/ 					if(!parent) continue;
/******/ 					if(parent.hot._declinedDependencies[moduleId]) {
/******/ 						return {
/******/ 							type: "declined",
/******/ 							chain: chain.concat([parentId]),
/******/ 							moduleId: moduleId,
/******/ 							parentId: parentId
/******/ 						};
/******/ 					}
/******/ 					if(outdatedModules.indexOf(parentId) >= 0) continue;
/******/ 					if(parent.hot._acceptedDependencies[moduleId]) {
/******/ 						if(!outdatedDependencies[parentId])
/******/ 							outdatedDependencies[parentId] = [];
/******/ 						addAllToSet(outdatedDependencies[parentId], [moduleId]);
/******/ 						continue;
/******/ 					}
/******/ 					delete outdatedDependencies[parentId];
/******/ 					outdatedModules.push(parentId);
/******/ 					queue.push({
/******/ 						chain: chain.concat([parentId]),
/******/ 						id: parentId
/******/ 					});
/******/ 				}
/******/ 			}
/******/ 	
/******/ 			return {
/******/ 				type: "accepted",
/******/ 				moduleId: updateModuleId,
/******/ 				outdatedModules: outdatedModules,
/******/ 				outdatedDependencies: outdatedDependencies
/******/ 			};
/******/ 		}
/******/ 	
/******/ 		function addAllToSet(a, b) {
/******/ 			for(var i = 0; i < b.length; i++) {
/******/ 				var item = b[i];
/******/ 				if(a.indexOf(item) < 0)
/******/ 					a.push(item);
/******/ 			}
/******/ 		}
/******/ 	
/******/ 		// at begin all updates modules are outdated
/******/ 		// the "outdated" status can propagate to parents if they don't accept the children
/******/ 		var outdatedDependencies = {};
/******/ 		var outdatedModules = [];
/******/ 		var appliedUpdate = {};
/******/ 	
/******/ 		var warnUnexpectedRequire = function warnUnexpectedRequire() {
/******/ 			console.warn("[HMR] unexpected require(" + result.moduleId + ") to disposed module");
/******/ 		};
/******/ 	
/******/ 		for(var id in hotUpdate) {
/******/ 			if(Object.prototype.hasOwnProperty.call(hotUpdate, id)) {
/******/ 				moduleId = toModuleId(id);
/******/ 				var result;
/******/ 				if(hotUpdate[id]) {
/******/ 					result = getAffectedStuff(moduleId);
/******/ 				} else {
/******/ 					result = {
/******/ 						type: "disposed",
/******/ 						moduleId: id
/******/ 					};
/******/ 				}
/******/ 				var abortError = false;
/******/ 				var doApply = false;
/******/ 				var doDispose = false;
/******/ 				var chainInfo = "";
/******/ 				if(result.chain) {
/******/ 					chainInfo = "\nUpdate propagation: " + result.chain.join(" -> ");
/******/ 				}
/******/ 				switch(result.type) {
/******/ 					case "self-declined":
/******/ 						if(options.onDeclined)
/******/ 							options.onDeclined(result);
/******/ 						if(!options.ignoreDeclined)
/******/ 							abortError = new Error("Aborted because of self decline: " + result.moduleId + chainInfo);
/******/ 						break;
/******/ 					case "declined":
/******/ 						if(options.onDeclined)
/******/ 							options.onDeclined(result);
/******/ 						if(!options.ignoreDeclined)
/******/ 							abortError = new Error("Aborted because of declined dependency: " + result.moduleId + " in " + result.parentId + chainInfo);
/******/ 						break;
/******/ 					case "unaccepted":
/******/ 						if(options.onUnaccepted)
/******/ 							options.onUnaccepted(result);
/******/ 						if(!options.ignoreUnaccepted)
/******/ 							abortError = new Error("Aborted because " + moduleId + " is not accepted" + chainInfo);
/******/ 						break;
/******/ 					case "accepted":
/******/ 						if(options.onAccepted)
/******/ 							options.onAccepted(result);
/******/ 						doApply = true;
/******/ 						break;
/******/ 					case "disposed":
/******/ 						if(options.onDisposed)
/******/ 							options.onDisposed(result);
/******/ 						doDispose = true;
/******/ 						break;
/******/ 					default:
/******/ 						throw new Error("Unexception type " + result.type);
/******/ 				}
/******/ 				if(abortError) {
/******/ 					hotSetStatus("abort");
/******/ 					return Promise.reject(abortError);
/******/ 				}
/******/ 				if(doApply) {
/******/ 					appliedUpdate[moduleId] = hotUpdate[moduleId];
/******/ 					addAllToSet(outdatedModules, result.outdatedModules);
/******/ 					for(moduleId in result.outdatedDependencies) {
/******/ 						if(Object.prototype.hasOwnProperty.call(result.outdatedDependencies, moduleId)) {
/******/ 							if(!outdatedDependencies[moduleId])
/******/ 								outdatedDependencies[moduleId] = [];
/******/ 							addAllToSet(outdatedDependencies[moduleId], result.outdatedDependencies[moduleId]);
/******/ 						}
/******/ 					}
/******/ 				}
/******/ 				if(doDispose) {
/******/ 					addAllToSet(outdatedModules, [result.moduleId]);
/******/ 					appliedUpdate[moduleId] = warnUnexpectedRequire;
/******/ 				}
/******/ 			}
/******/ 		}
/******/ 	
/******/ 		// Store self accepted outdated modules to require them later by the module system
/******/ 		var outdatedSelfAcceptedModules = [];
/******/ 		for(i = 0; i < outdatedModules.length; i++) {
/******/ 			moduleId = outdatedModules[i];
/******/ 			if(installedModules[moduleId] && installedModules[moduleId].hot._selfAccepted)
/******/ 				outdatedSelfAcceptedModules.push({
/******/ 					module: moduleId,
/******/ 					errorHandler: installedModules[moduleId].hot._selfAccepted
/******/ 				});
/******/ 		}
/******/ 	
/******/ 		// Now in "dispose" phase
/******/ 		hotSetStatus("dispose");
/******/ 		Object.keys(hotAvailableFilesMap).forEach(function(chunkId) {
/******/ 			if(hotAvailableFilesMap[chunkId] === false) {
/******/ 				hotDisposeChunk(chunkId);
/******/ 			}
/******/ 		});
/******/ 	
/******/ 		var idx;
/******/ 		var queue = outdatedModules.slice();
/******/ 		while(queue.length > 0) {
/******/ 			moduleId = queue.pop();
/******/ 			module = installedModules[moduleId];
/******/ 			if(!module) continue;
/******/ 	
/******/ 			var data = {};
/******/ 	
/******/ 			// Call dispose handlers
/******/ 			var disposeHandlers = module.hot._disposeHandlers;
/******/ 			for(j = 0; j < disposeHandlers.length; j++) {
/******/ 				cb = disposeHandlers[j];
/******/ 				cb(data);
/******/ 			}
/******/ 			hotCurrentModuleData[moduleId] = data;
/******/ 	
/******/ 			// disable module (this disables requires from this module)
/******/ 			module.hot.active = false;
/******/ 	
/******/ 			// remove module from cache
/******/ 			delete installedModules[moduleId];
/******/ 	
/******/ 			// remove "parents" references from all children
/******/ 			for(j = 0; j < module.children.length; j++) {
/******/ 				var child = installedModules[module.children[j]];
/******/ 				if(!child) continue;
/******/ 				idx = child.parents.indexOf(moduleId);
/******/ 				if(idx >= 0) {
/******/ 					child.parents.splice(idx, 1);
/******/ 				}
/******/ 			}
/******/ 		}
/******/ 	
/******/ 		// remove outdated dependency from module children
/******/ 		var dependency;
/******/ 		var moduleOutdatedDependencies;
/******/ 		for(moduleId in outdatedDependencies) {
/******/ 			if(Object.prototype.hasOwnProperty.call(outdatedDependencies, moduleId)) {
/******/ 				module = installedModules[moduleId];
/******/ 				if(module) {
/******/ 					moduleOutdatedDependencies = outdatedDependencies[moduleId];
/******/ 					for(j = 0; j < moduleOutdatedDependencies.length; j++) {
/******/ 						dependency = moduleOutdatedDependencies[j];
/******/ 						idx = module.children.indexOf(dependency);
/******/ 						if(idx >= 0) module.children.splice(idx, 1);
/******/ 					}
/******/ 				}
/******/ 			}
/******/ 		}
/******/ 	
/******/ 		// Not in "apply" phase
/******/ 		hotSetStatus("apply");
/******/ 	
/******/ 		hotCurrentHash = hotUpdateNewHash;
/******/ 	
/******/ 		// insert new code
/******/ 		for(moduleId in appliedUpdate) {
/******/ 			if(Object.prototype.hasOwnProperty.call(appliedUpdate, moduleId)) {
/******/ 				modules[moduleId] = appliedUpdate[moduleId];
/******/ 			}
/******/ 		}
/******/ 	
/******/ 		// call accept handlers
/******/ 		var error = null;
/******/ 		for(moduleId in outdatedDependencies) {
/******/ 			if(Object.prototype.hasOwnProperty.call(outdatedDependencies, moduleId)) {
/******/ 				module = installedModules[moduleId];
/******/ 				moduleOutdatedDependencies = outdatedDependencies[moduleId];
/******/ 				var callbacks = [];
/******/ 				for(i = 0; i < moduleOutdatedDependencies.length; i++) {
/******/ 					dependency = moduleOutdatedDependencies[i];
/******/ 					cb = module.hot._acceptedDependencies[dependency];
/******/ 					if(callbacks.indexOf(cb) >= 0) continue;
/******/ 					callbacks.push(cb);
/******/ 				}
/******/ 				for(i = 0; i < callbacks.length; i++) {
/******/ 					cb = callbacks[i];
/******/ 					try {
/******/ 						cb(moduleOutdatedDependencies);
/******/ 					} catch(err) {
/******/ 						if(options.onErrored) {
/******/ 							options.onErrored({
/******/ 								type: "accept-errored",
/******/ 								moduleId: moduleId,
/******/ 								dependencyId: moduleOutdatedDependencies[i],
/******/ 								error: err
/******/ 							});
/******/ 						}
/******/ 						if(!options.ignoreErrored) {
/******/ 							if(!error)
/******/ 								error = err;
/******/ 						}
/******/ 					}
/******/ 				}
/******/ 			}
/******/ 		}
/******/ 	
/******/ 		// Load self accepted modules
/******/ 		for(i = 0; i < outdatedSelfAcceptedModules.length; i++) {
/******/ 			var item = outdatedSelfAcceptedModules[i];
/******/ 			moduleId = item.module;
/******/ 			hotCurrentParents = [moduleId];
/******/ 			try {
/******/ 				__webpack_require__(moduleId);
/******/ 			} catch(err) {
/******/ 				if(typeof item.errorHandler === "function") {
/******/ 					try {
/******/ 						item.errorHandler(err);
/******/ 					} catch(err2) {
/******/ 						if(options.onErrored) {
/******/ 							options.onErrored({
/******/ 								type: "self-accept-error-handler-errored",
/******/ 								moduleId: moduleId,
/******/ 								error: err2,
/******/ 								orginalError: err
/******/ 							});
/******/ 						}
/******/ 						if(!options.ignoreErrored) {
/******/ 							if(!error)
/******/ 								error = err2;
/******/ 						}
/******/ 						if(!error)
/******/ 							error = err;
/******/ 					}
/******/ 				} else {
/******/ 					if(options.onErrored) {
/******/ 						options.onErrored({
/******/ 							type: "self-accept-errored",
/******/ 							moduleId: moduleId,
/******/ 							error: err
/******/ 						});
/******/ 					}
/******/ 					if(!options.ignoreErrored) {
/******/ 						if(!error)
/******/ 							error = err;
/******/ 					}
/******/ 				}
/******/ 			}
/******/ 		}
/******/ 	
/******/ 		// handle errors in accept handlers and self accepted module load
/******/ 		if(error) {
/******/ 			hotSetStatus("fail");
/******/ 			return Promise.reject(error);
/******/ 		}
/******/ 	
/******/ 		hotSetStatus("idle");
/******/ 		return new Promise(function(resolve) {
/******/ 			resolve(outdatedModules);
/******/ 		});
/******/ 	}
/******/
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {},
/******/ 			hot: hotCreateModule(moduleId),
/******/ 			parents: (hotCurrentParentsTemp = hotCurrentParents, hotCurrentParents = [], hotCurrentParentsTemp),
/******/ 			children: []
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, hotCreateRequire(moduleId));
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// identity function for calling harmony imports with the correct context
/******/ 	__webpack_require__.i = function(value) { return value; };
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, {
/******/ 				configurable: false,
/******/ 				enumerable: true,
/******/ 				get: getter
/******/ 			});
/******/ 		}
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/ 	// __webpack_hash__
/******/ 	__webpack_require__.h = function() { return hotCurrentHash; };
/******/
/******/ 	// Load entry module and return exports
/******/ 	return hotCreateRequire(453)(__webpack_require__.s = 453);
/******/ })
/************************************************************************/
/******/ ({

/***/ 224:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__ImportPage__ = __webpack_require__(268);
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



var $ = window.$;

$(function () {
  new __WEBPACK_IMPORTED_MODULE_0__ImportPage__["a" /* default */]().init();
});

/***/ }),

/***/ 267:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

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

var $ = window.$;

var entityCategories = 0;
var entityProducts = 1;
var entityCombinations = 2;
var entityCustomers = 3;
var entityAddresses = 4;
var entityBrands = 5;
var entitySuppliers = 6;
var entityAlias = 7;
var entityStoreContacts = 8;

var FormFieldToggle = function () {
  function FormFieldToggle() {
    _classCallCheck(this, FormFieldToggle);
  }

  _createClass(FormFieldToggle, [{
    key: 'init',
    value: function init() {
      $('.js-entity-select').on('change', this.toggleForm.bind(this));

      this.toggleForm();
    }
  }, {
    key: 'toggleForm',
    value: function toggleForm() {
      var selectedOption = $('#entity').find('option:selected');
      var selectedEntity = parseInt(selectedOption.val());
      var entityName = selectedOption.text().toLowerCase();

      this.toggleEntityAlert(selectedEntity);
      this.toggleFields(selectedEntity, entityName);
      this.loadAvailableFields(selectedEntity);
    }

    /**
     * Toggle alert warning for selected import entity
     *
     * @param {int} selectedEntity
     */

  }, {
    key: 'toggleEntityAlert',
    value: function toggleEntityAlert(selectedEntity) {
      var $alert = $('.js-entity-alert');

      if ([entityCategories, entityProducts].includes(selectedEntity)) {
        $alert.show();
      } else {
        $alert.hide();
      }
    }

    /**
     * Toggle available options for selected entity
     *
     * @param {int} selectedEntity
     * @param {string} entityName
     */

  }, {
    key: 'toggleFields',
    value: function toggleFields(selectedEntity, entityName) {
      var $truncateFormGroup = $('.js-truncate-form-group');
      var $matchRefFormGroup = $('.js-match-ref-form-group');
      var $regenerateFormGroup = $('.js-regenerate-form-group');
      var $forceIdsFormGroup = $('.js-force-ids-form-group');
      var $entityNamePlaceholder = $('.js-entity-name');

      if (entityStoreContacts === selectedEntity) {
        $truncateFormGroup.hide();
      } else {
        $truncateFormGroup.show();
      }

      if ([entityProducts, entityCombinations].includes(selectedEntity)) {
        $matchRefFormGroup.show();
      } else {
        $matchRefFormGroup.hide();
      }

      if ([entityCategories, entityProducts, entityBrands, entitySuppliers, entityStoreContacts].includes(selectedEntity)) {
        $regenerateFormGroup.show();
      } else {
        $regenerateFormGroup.hide();
      }

      if ([entityCategories, entityProducts, entityCustomers, entityAddresses, entityBrands, entitySuppliers, entityStoreContacts, entityAlias].includes(selectedEntity)) {
        $forceIdsFormGroup.show();
      } else {
        $forceIdsFormGroup.hide();
      }

      $entityNamePlaceholder.html(entityName);
    }

    /**
     * Load available fields for given entity
     *
     * @param {int} entity
     */

  }, {
    key: 'loadAvailableFields',
    value: function loadAvailableFields(entity) {
      $.ajax({
        url: '../../../ajax.php',
        data: {
          getAvailableFields: 1,
          entity: entity
        },
        dataType: 'json'
      }).then(function (response) {
        var fields = '';
        var $availableFields = $('.js-available-fields');
        $availableFields.empty();

        for (var i = 0; i < response.length; i++) {
          fields += response[i].field;
        }

        $availableFields.html(fields);
        $availableFields.find('[data-toggle="popover"]').popover();
      });
    }
  }]);

  return FormFieldToggle;
}();

/* harmony default export */ __webpack_exports__["a"] = (FormFieldToggle);

/***/ }),

/***/ 268:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__FormFieldToggle__ = __webpack_require__(267);
var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

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



var $ = window.$;

var ImportPage = function () {
  function ImportPage() {
    _classCallCheck(this, ImportPage);
  }

  _createClass(ImportPage, [{
    key: 'init',
    value: function init() {
      var _this = this;

      new __WEBPACK_IMPORTED_MODULE_0__FormFieldToggle__["a" /* default */]().init();

      $('.js-from-files-history-btn').on('click', function () {
        return _this.showFilesHistoryHandler();
      });
      $('.js-close-files-history-block-btn').on('click', function () {
        return _this.closeFilesHistoryHandler();
      });
      $('#fileHistoryTable').on('click', '.js-use-file-btn', function (event) {
        return _this.useFileFromFilesHistory(event);
      });
      $('.js-change-import-file-btn').on('click', function () {
        return _this.changeImportFileHandler();
      });
      $('.js-import-file').on('change', function () {
        return _this.uploadFile();
      });

      this.toggleSelectedFile();
    }

    /**
     * Check if selected file names exists and if so, then display it
     */

  }, {
    key: 'toggleSelectedFile',
    value: function toggleSelectedFile() {
      var selectFilename = $('#csv').val();
      if (selectFilename.length > 0) {
        this.showImportFileAlert(selectFilename);
        this.hideFileUploadBlock();
      }
    }
  }, {
    key: 'changeImportFileHandler',
    value: function changeImportFileHandler() {
      this.hideImportFileAlert();
      this.showFileUploadBlock();
    }

    /**
     * Show files history event handler
     */

  }, {
    key: 'showFilesHistoryHandler',
    value: function showFilesHistoryHandler() {
      this.showFilesHistory();
      this.hideFileUploadBlock();
    }

    /**
     * Close files history event handler
     */

  }, {
    key: 'closeFilesHistoryHandler',
    value: function closeFilesHistoryHandler() {
      this.closeFilesHistory();
      this.showFileUploadBlock();
    }

    /**
     * Show files history block
     */

  }, {
    key: 'showFilesHistory',
    value: function showFilesHistory() {
      $('.js-files-history-block').removeClass('d-none');
    }

    /**
     * Hide files history block
     */

  }, {
    key: 'closeFilesHistory',
    value: function closeFilesHistory() {
      $('.js-files-history-block').addClass('d-none');
    }

    /**
     *  Prefill hidden file input with selected file name from history
     */

  }, {
    key: 'useFileFromFilesHistory',
    value: function useFileFromFilesHistory(event) {
      var filename = $(event.target).closest('.btn-group').data('file');

      $('.js-import-file-input').val(filename);

      this.showImportFileAlert(filename);
      this.closeFilesHistory();
    }

    /**
     * Show alert with imported file name
     */

  }, {
    key: 'showImportFileAlert',
    value: function showImportFileAlert(filename) {
      $('.js-import-file-alert').removeClass('d-none');
      $('.js-import-file').text(filename);
    }

    /**
     * Hides selected import file alert
     */

  }, {
    key: 'hideImportFileAlert',
    value: function hideImportFileAlert() {
      $('.js-import-file-alert').addClass('d-none');
    }

    /**
     * Hides import file upload block
     */

  }, {
    key: 'hideFileUploadBlock',
    value: function hideFileUploadBlock() {
      $('.js-file-upload-form-group').addClass('d-none');
    }

    /**
     * Hides import file upload block
     */

  }, {
    key: 'showFileUploadBlock',
    value: function showFileUploadBlock() {
      $('.js-file-upload-form-group').removeClass('d-none');
    }

    /**
     * Make file history button clickable
     */

  }, {
    key: 'enableFilesHistoryBtn',
    value: function enableFilesHistoryBtn() {
      $('.js-from-files-history-btn').removeAttr('disabled');
    }

    /**
     * Show error message if file uploading failed
     *
     * @param {string} fileName
     * @param {integer} fileSize
     * @param {string} message
     */

  }, {
    key: 'showImportFileError',
    value: function showImportFileError(fileName, fileSize, message) {
      var $alert = $('.js-import-file-error');

      var fileData = fileName + ' (' + this.humanizeSize(fileSize) + ')';

      $alert.find('.js-file-data').html(fileData);
      $alert.find('.js-error-message').html(message);
      $alert.removeClass('d-none');
    }

    /**
     * Hide file uploading error
     */

  }, {
    key: 'hideImportFileError',
    value: function hideImportFileError() {
      var $alert = $('.js-import-file-error');
      $alert.addClass('d-none');
    }

    /**
     * Show file size in human readable format
     *
     * @param {int} bytes
     *
     * @returns {string}
     */

  }, {
    key: 'humanizeSize',
    value: function humanizeSize(bytes) {
      if (typeof bytes !== 'number') {
        return '';
      }

      if (bytes >= 1000000000) {
        return (bytes / 1000000000).toFixed(2) + ' GB';
      }

      if (bytes >= 1000000) {
        return (bytes / 1000000).toFixed(2) + ' MB';
      }

      return (bytes / 1000).toFixed(2) + ' KB';
    }

    /**
     * Upload selected import file
     */

  }, {
    key: 'uploadFile',
    value: function uploadFile() {
      var _this2 = this;

      this.hideImportFileError();

      var $input = $('#file');
      var uploadedFile = $input.prop('files')[0];

      var maxUploadSize = $input.data('max-file-upload-size');
      if (maxUploadSize < uploadedFile.size) {
        this.showImportFileError(uploadedFile.name, uploadedFile.size, 'File is too large');
        return;
      }

      var data = new FormData();
      data.append('file', uploadedFile);

      var url = $('.js-import-form').data('file-upload-url');

      $.ajax({
        type: 'POST',
        url: url,
        data: data,
        cache: false,
        contentType: false,
        processData: false
      }).then(function (response) {
        if (response.error) {
          _this2.showImportFileError(uploadedFile.name, uploadedFile.size, response.error);
          return;
        }

        var filename = response.file.name;

        $('.js-import-file-input').val(filename);

        _this2.showImportFileAlert(filename);
        _this2.hideFileUploadBlock();
        _this2.addFileToHistoryTable(filename);
        _this2.enableFilesHistoryBtn();
      });
    }

    /**
     * Renders new row in files history table
     *
     * @param {string} filename
     */

  }, {
    key: 'addFileToHistoryTable',
    value: function addFileToHistoryTable(filename) {
      var $table = $('#fileHistoryTable');

      var baseDeleteUrl = $table.data('delete-file-url');
      var deleteUrl = baseDeleteUrl + '&filename=' + encodeURIComponent(filename);

      var baseDownloadUrl = $table.data('download-file-url');
      var downloadUrl = baseDownloadUrl + '&filename=' + encodeURIComponent(filename);

      var $template = $table.find('tr:first').clone();

      $template.removeClass('d-none');
      $template.find('td:first').text(filename);
      $template.find('.btn-group').attr('data-file', filename);
      $template.find('.js-delete-file-btn').attr('href', deleteUrl);
      $template.find('.js-download-file-btn').attr('href', downloadUrl);

      $table.find('tbody').append($template);

      var filesNumber = $table.find('tr').length - 1;
      $('.js-files-history-number').text(filesNumber);
    }
  }]);

  return ImportPage;
}();

/* harmony default export */ __webpack_exports__["a"] = (ImportPage);

/***/ }),

/***/ 453:
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(224);


/***/ })

/******/ });
>>>>>>> 0c9e0a681f... rebuild assets
