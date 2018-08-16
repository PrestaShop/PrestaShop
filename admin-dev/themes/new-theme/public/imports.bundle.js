/******/!function(e){function t(o){if(i[o])return i[o].exports;var n=i[o]={i:o,l:!1,exports:{}};return e[o].call(n.exports,n,n.exports,t),n.l=!0,n.exports}// webpackBootstrap
/******/
<<<<<<< HEAD
var i={};t.m=e,t.c=i,t.i=function(e){return e},t.d=function(e,i,o){t.o(e,i)||Object.defineProperty(e,i,{configurable:!1,enumerable:!0,get:o})},t.n=function(e){var i=e&&e.__esModule?function(){return e.default}:function(){return e};return t.d(i,"a",i),i},t.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},t.p="",t(t.s=381)}({187:function(e,t,i){"use strict";Object.defineProperty(t,"__esModule",{value:!0});var o=i(231);(0,window.$)(function(){(new o.a).init()})},230:function(e,t,i){"use strict";function o(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}var n=function(){function e(e,t){for(var i=0;i<t.length;i++){var o=t[i];o.enumerable=o.enumerable||!1,o.configurable=!0,"value"in o&&(o.writable=!0),Object.defineProperty(e,o.key,o)}}return function(t,i,o){return i&&e(t.prototype,i),o&&e(t,o),t}}(),r=window.$,l=function(){function e(){o(this,e)}return n(e,[{key:"init",value:function(){r(".js-entity-select").on("change",this.toggleForm.bind(this)),this.toggleForm()}},{key:"toggleForm",value:function(){var e=r("#entity").find("option:selected"),t=parseInt(e.val()),i=e.text().toLowerCase();this.toggleEntityAlert(t),this.toggleFields(t,i),this.loadAvailableFields(t)}},{key:"toggleEntityAlert",value:function(e){var t=r(".js-entity-alert");[0,1].includes(e)?t.show():t.hide()}},{key:"toggleFields",value:function(e,t){var i=r(".js-truncate-form-group"),o=r(".js-match-ref-form-group"),n=r(".js-regenerate-form-group"),l=r(".js-force-ids-form-group"),a=r(".js-entity-name");8===e?i.hide():i.show(),[1,2].includes(e)?o.show():o.hide(),[0,1,5,6,8].includes(e)?n.show():n.hide(),[0,1,3,4,5,6,8,7].includes(e)?l.show():l.hide(),a.html(t)}},{key:"loadAvailableFields",value:function(e){r.ajax({url:"../../../ajax.php",data:{getAvailableFields:1,entity:e},dataType:"json"}).then(function(e){var t="",i=r(".js-available-fields");i.empty();for(var o=0;o<e.length;o++)t+=e[o].field;i.html(t),i.find('[data-toggle="popover"]').popover()})}}]),e}();t.a=l},231:function(e,t,i){"use strict";function o(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}var n=i(230),r=function(){function e(e,t){for(var i=0;i<t.length;i++){var o=t[i];o.enumerable=o.enumerable||!1,o.configurable=!0,"value"in o&&(o.writable=!0),Object.defineProperty(e,o.key,o)}}return function(t,i,o){return i&&e(t.prototype,i),o&&e(t,o),t}}(),l=window.$,a=function(){function e(){o(this,e)}return r(e,[{key:"init",value:function(){var e=this;(new n.a).init(),l(".js-from-files-history-btn").on("click",function(){return e.showFilesHistoryHandler()}),l(".js-close-files-history-block-btn").on("click",function(){return e.closeFilesHistoryHandler()}),l("#fileHistoryTable").on("click",".js-use-file-btn",function(t){return e.useFileFromFilesHistory(t)}),l(".js-change-import-file-btn").on("click",function(){return e.changeImportFileHandler()}),l(".js-import-file").on("change",function(){return e.uploadFile()}),this.toggleSelectedFile()}},{key:"toggleSelectedFile",value:function(){var e=l("#csv").val();e.length>0&&(this.showImportFileAlert(e),this.hideFileUploadBlock())}},{key:"changeImportFileHandler",value:function(){this.hideImportFileAlert(),this.showFileUploadBlock()}},{key:"showFilesHistoryHandler",value:function(){this.showFilesHistory(),this.hideFileUploadBlock()}},{key:"closeFilesHistoryHandler",value:function(){this.closeFilesHistory(),this.showFileUploadBlock()}},{key:"showFilesHistory",value:function(){l(".js-files-history-block").removeClass("d-none")}},{key:"closeFilesHistory",value:function(){l(".js-files-history-block").addClass("d-none")}},{key:"useFileFromFilesHistory",value:function(e){var t=l(e.target).closest(".btn-group").data("file");l(".js-import-file-input").val(t),this.showImportFileAlert(t),this.closeFilesHistory()}},{key:"showImportFileAlert",value:function(e){l(".js-import-file-alert").removeClass("d-none"),l(".js-import-file").text(e)}},{key:"hideImportFileAlert",value:function(){l(".js-import-file-alert").addClass("d-none")}},{key:"hideFileUploadBlock",value:function(){l(".js-file-upload-form-group").addClass("d-none")}},{key:"showFileUploadBlock",value:function(){l(".js-file-upload-form-group").removeClass("d-none")}},{key:"enableFilesHistoryBtn",value:function(){l(".js-from-files-history-btn").removeAttr("disabled")}},{key:"showImportFileError",value:function(e,t,i){var o=l(".js-import-file-error"),n=e+" ("+this.humanizeSize(t)+")";o.find(".js-file-data").html(n),o.find(".js-error-message").html(i),o.removeClass("d-none")}},{key:"hideImportFileError",value:function(){l(".js-import-file-error").addClass("d-none")}},{key:"humanizeSize",value:function(e){return"number"!=typeof e?"":e>=1e9?(e/1e9).toFixed(2)+" GB":e>=1e6?(e/1e6).toFixed(2)+" MB":(e/1e3).toFixed(2)+" KB"}},{key:"uploadFile",value:function(){var e=this;this.hideImportFileError();var t=l("#file"),i=t.prop("files")[0];if(t.data("max-file-upload-size")<i.size)return void this.showImportFileError(i.name,i.size,"File is too large");var o=new FormData;o.append("file",i);var n=l(".js-import-form").data("file-upload-url");l.ajax({type:"POST",url:n,data:o,cache:!1,contentType:!1,processData:!1}).then(function(t){if(t.error)return void e.showImportFileError(i.name,i.size,t.error);var o=t.file.name;l(".js-import-file-input").val(o),e.showImportFileAlert(o),e.hideFileUploadBlock(),e.addFileToHistoryTable(o),e.enableFilesHistoryBtn()})}},{key:"addFileToHistoryTable",value:function(e){var t=l("#fileHistoryTable"),i=t.data("delete-file-url"),o=i+"&filename="+encodeURIComponent(e),n=t.data("download-file-url"),r=n+"&filename="+encodeURIComponent(e),a=t.find("tr:first").clone();a.removeClass("d-none"),a.find("td:first").text(e),a.find(".btn-group").attr("data-file",e),a.find(".js-delete-file-btn").attr("href",o),a.find(".js-download-file-btn").attr("href",r),t.find("tbody").append(a);var s=t.find("tr").length-1;l(".js-files-history-number").text(s)}}]),e}();t.a=a},381:function(e,t,i){e.exports=i(187)}});
=======
/******/ 	
/******/ 	
/******/ 	var hotApplyOnUpdate = true;
/******/ 	var hotCurrentHash = "ad45789801c39aad8ed7"; // eslint-disable-line no-unused-vars
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
/******/ 			var chunkId = 7;
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
/******/ 	return hotCreateRequire(465)(__webpack_require__.s = 465);
/******/ })
/************************************************************************/
/******/ ({

/***/ 236:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__ImportPage__ = __webpack_require__(280);
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
  new __WEBPACK_IMPORTED_MODULE_0__ImportPage__["a" /* default */]();
});

/***/ }),

/***/ 279:
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

    $('.js-entity-select').on('change', this.toggleForm.bind(this));

    this.toggleForm();
  }

  _createClass(FormFieldToggle, [{
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
      var url = -1 === window.location.href.indexOf('index.php') ? '../../../ajax.php' : '../../../../ajax.php';

      $.ajax({
        url: url,
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

/***/ 280:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__FormFieldToggle__ = __webpack_require__(279);
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
    var _this = this;

    _classCallCheck(this, ImportPage);

    new __WEBPACK_IMPORTED_MODULE_0__FormFieldToggle__["a" /* default */]();

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
    this.handleSubmit();
  }

  /**
   * Handle submit and add confirm box in case the toggle button about
   * deleting all entities before import is checked
   */


  _createClass(ImportPage, [{
    key: 'handleSubmit',
    value: function handleSubmit() {
      $('.js-import-form').on('submit', function () {
        var $this = $(this);
        if ($this.find('input[name="truncate"]:checked').val() === '1') {
          return confirm($this.data('delete-confirm-message') + ' ' + $.trim($('#entity > option:selected').text().toLowerCase()) + '?');
        }
      });
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

      $.ajax({
        type: 'POST',
        url: $('.js-import-form').data('file-upload-url'),
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

/***/ 465:
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(236);


/***/ })

/******/ });
>>>>>>> 9953d197b297b5ff1aa261b6465d75d5d7da1c9b
