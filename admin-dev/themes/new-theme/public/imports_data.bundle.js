/******/ (function(modules) { // webpackBootstrap
/******/ 	function hotDisposeChunk(chunkId) {
/******/ 		delete installedChunks[chunkId];
/******/ 	}
/******/ 	var parentHotUpdateCallback = this["webpackHotUpdate"];
/******/ 	this["webpackHotUpdate"] = 
/******/ 	function webpackHotUpdateCallback(chunkId, moreModules) { // eslint-disable-line no-unused-vars
/******/ 		hotAddUpdateChunk(chunkId, moreModules);
/******/ 		if(parentHotUpdateCallback) parentHotUpdateCallback(chunkId, moreModules);
/******/ 	} ;
/******/ 	
/******/ 	function hotDownloadUpdateChunk(chunkId) { // eslint-disable-line no-unused-vars
/******/ 		var head = document.getElementsByTagName("head")[0];
/******/ 		var script = document.createElement("script");
/******/ 		script.type = "text/javascript";
/******/ 		script.charset = "utf-8";
/******/ 		script.src = __webpack_require__.p + "" + chunkId + "." + hotCurrentHash + ".hot-update.js";
/******/ 		head.appendChild(script);
/******/ 	}
/******/ 	
/******/ 	function hotDownloadManifest() { // eslint-disable-line no-unused-vars
/******/ 		return new Promise(function(resolve, reject) {
/******/ 			if(typeof XMLHttpRequest === "undefined")
/******/ 				return reject(new Error("No browser support"));
/******/ 			try {
/******/ 				var request = new XMLHttpRequest();
/******/ 				var requestPath = __webpack_require__.p + "" + hotCurrentHash + ".hot-update.json";
/******/ 				request.open("GET", requestPath, true);
/******/ 				request.timeout = 10000;
/******/ 				request.send(null);
/******/ 			} catch(err) {
/******/ 				return reject(err);
/******/ 			}
/******/ 			request.onreadystatechange = function() {
/******/ 				if(request.readyState !== 4) return;
/******/ 				if(request.status === 0) {
/******/ 					// timeout
/******/ 					reject(new Error("Manifest request to " + requestPath + " timed out."));
/******/ 				} else if(request.status === 404) {
/******/ 					// no update available
/******/ 					resolve();
/******/ 				} else if(request.status !== 200 && request.status !== 304) {
/******/ 					// other failure
/******/ 					reject(new Error("Manifest request to " + requestPath + " failed."));
/******/ 				} else {
/******/ 					// success
/******/ 					try {
/******/ 						var update = JSON.parse(request.responseText);
/******/ 					} catch(e) {
/******/ 						reject(e);
/******/ 						return;
/******/ 					}
/******/ 					resolve(update);
/******/ 				}
/******/ 			};
/******/ 		});
/******/ 	}
/******/
/******/ 	
/******/ 	
/******/ 	var hotApplyOnUpdate = true;
/******/ 	var hotCurrentHash = "30e714dc5134ac0de7e5"; // eslint-disable-line no-unused-vars
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
/******/ 			var chunkId = 13;
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
/******/ 	return hotCreateRequire(522)(__webpack_require__.s = 522);
/******/ })
/************************************************************************/
/******/ ({

/***/ 246:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__ImportDataPage__ = __webpack_require__(313);
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
  new __WEBPACK_IMPORTED_MODULE_0__ImportDataPage__["a" /* default */]();
});

/***/ }),

/***/ 311:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

/**
 * 2007-2018 PrestaShop.
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

var EntityFieldsValidator = function () {
  function EntityFieldsValidator() {
    _classCallCheck(this, EntityFieldsValidator);
  }

  _createClass(EntityFieldsValidator, null, [{
    key: 'validate',

    /**
     * Validates entity fields
     *
     * @returns {boolean}
     */
    value: function validate() {
      $('.js-validation-error').addClass('d-none');

      return this._checkDuplicateSelectedValues() && this._checkRequiredFields();
    }

    /**
     * Checks if there are no duplicate selected values.
     *
     * @returns {boolean}
     * @private
     */

  }, {
    key: '_checkDuplicateSelectedValues',
    value: function _checkDuplicateSelectedValues() {
      var uniqueFields = [];
      var valid = true;

      $('.js-entity-field select').each(function () {
        var value = $(this).val();

        if (value === 'no') {
          return;
        }

        if ($.inArray(value, uniqueFields) !== -1) {
          valid = false;
          $('.js-duplicate-columns-warning').removeClass('d-none');
          return;
        }

        uniqueFields.push(value);
      });

      return valid;
    }

    /**
     * Checks if all required fields are selected.
     *
     * @returns {boolean}
     * @private
     */

  }, {
    key: '_checkRequiredFields',
    value: function _checkRequiredFields() {
      var requiredImportFields = $('.js-import-data-table').data('required-fields');

      for (var key in requiredImportFields) {
        if (0 === $('option[value="' + requiredImportFields[key] + '"]:selected').length) {
          $('.js-missing-column-warning').removeClass('d-none');
          $('.js-missing-column').text($('option[value="' + requiredImportFields[key] + '"]:first').text());

          return false;
        }
      }
      return true;
    }
  }]);

  return EntityFieldsValidator;
}();

/* harmony default export */ __webpack_exports__["a"] = (EntityFieldsValidator);

/***/ }),

/***/ 312:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

/**
 * 2007-2018 PrestaShop.
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

/**
 * Class ImportBatchSizeCalculator calculates the import batch size.
 * Import batch size is the maximum number of records that
 * the import should handle in one batch.
 */
var ImportBatchSizeCalculator = function () {
  function ImportBatchSizeCalculator() {
    _classCallCheck(this, ImportBatchSizeCalculator);

    // Target execution time in milliseconds.
    this._targetExecutionTime = 5000;

    // Maximum batch size increase multiplier.
    this._maxAcceleration = 4;

    // Minimum and maximum import batch sizes.
    this._minBatchSize = 5;
    this._maxBatchSize = 100;
  }

  /**
   * Marks the start of the import operation.
   * Must be executed before starting the import,
   * to be able to calculate the import batch size later on.
   */


  _createClass(ImportBatchSizeCalculator, [{
    key: 'markImportStart',
    value: function markImportStart() {
      this._importStartTime = new Date().getTime();
    }

    /**
     * Marks the end of the import operation.
     * Must be executed after the import operation finishes,
     * to be able to calculate the import batch size later on.
     */

  }, {
    key: 'markImportEnd',
    value: function markImportEnd() {
      this._actualExecutionTime = new Date().getTime() - this._importStartTime;
    }

    /**
     * Calculates how much the import execution time can be increased to still be acceptable.
     *
     * @returns {number}
     * @private
     */

  }, {
    key: '_calculateAcceleration',
    value: function _calculateAcceleration() {
      return Math.min(this._maxAcceleration, this._targetExecutionTime / this._actualExecutionTime);
    }

    /**
     * Calculates the recommended import batch size.
     *
     * @param {number} currentBatchSize current import batch size
     * @returns {number} recommended import batch size
     */

  }, {
    key: 'calculateBatchSize',
    value: function calculateBatchSize(currentBatchSize) {
      if (!this._importStartTime) {
        throw 'Import start is not marked.';
      }

      if (!this._actualExecutionTime) {
        throw 'Import end is not marked.';
      }

      return Math.min(this._maxBatchSize, Math.max(this._minBatchSize, Math.floor(currentBatchSize * this._calculateAcceleration())));
    }
  }]);

  return ImportBatchSizeCalculator;
}();

/* harmony default export */ __webpack_exports__["a"] = (ImportBatchSizeCalculator);

/***/ }),

/***/ 313:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__ImportMatchConfiguration__ = __webpack_require__(315);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__ImportDataTable__ = __webpack_require__(314);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__EntityFieldsValidator__ = __webpack_require__(311);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3__Importer__ = __webpack_require__(317);
var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

/**
 * 2007-2018 PrestaShop.
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






var ImportDataPage = function () {
  function ImportDataPage() {
    var _this = this;

    _classCallCheck(this, ImportDataPage);

    new __WEBPACK_IMPORTED_MODULE_0__ImportMatchConfiguration__["a" /* default */]();
    new __WEBPACK_IMPORTED_MODULE_1__ImportDataTable__["a" /* default */]();
    this.importer = new __WEBPACK_IMPORTED_MODULE_3__Importer__["a" /* default */]();

    $(document).on('click', '.js-process-import', function (e) {
      return _this.importHandler(e);
    });
    $(document).on('click', '.js-abort-import', function () {
      return _this.importer.requestCancelImport();
    });
    $(document).on('click', '.js-close-modal', function () {
      return _this.importer.progressModal.hide();
    });
    $(document).on('click', '.js-continue-import', function () {
      return _this.importer.continueImport();
    });
  }

  /**
   * Import process event handler
   */


  _createClass(ImportDataPage, [{
    key: 'importHandler',
    value: function importHandler(e) {
      e.preventDefault();

      if (!__WEBPACK_IMPORTED_MODULE_2__EntityFieldsValidator__["a" /* default */].validate()) {
        return;
      }

      var configuration = {};

      // Collect the configuration from the form into an array.
      $('.import-data-configuration-form').find('#skip, select[name^=type_value], #csv, #iso_lang, #entity,' + '#truncate, #match_ref, #regenerate, #forceIDs, #sendemail,' + '#separator, #multiple_value_separator').each(function (index, $input) {
        configuration[$($input).attr('name')] = $($input).val();
      });

      this.importer.import(configuration);
    }
  }]);

  return ImportDataPage;
}();

/* harmony default export */ __webpack_exports__["a"] = (ImportDataPage);

/***/ }),

/***/ 314:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

/**
 * 2007-2018 PrestaShop.
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

var $importDataTable = $('.js-import-data-table');

/**
 * Pagination directions - forward and backward.
 */
var FORWARD = 'forward';
var BACKWARD = 'backward';

var ImportDataTable = function () {
  function ImportDataTable() {
    var _this = this;

    _classCallCheck(this, ImportDataTable);

    this.numberOfColumnsPerPage = this._getNumberOfVisibleColumns();
    this.totalNumberOfColumns = this._getTotalNumberOfColumns();

    $('.js-import-next-page').on('click', function () {
      return _this.importNextPageHandler();
    });
    $('.js-import-previous-page').on('click', function () {
      return _this.importPreviousPageHandler();
    });
  }

  /**
   * Handle the next page action in import data table.
   */


  _createClass(ImportDataTable, [{
    key: 'importNextPageHandler',
    value: function importNextPageHandler() {
      this._importPaginationHandler(FORWARD);
    }

    /**
     * Handle the previous page action in import data table.
     */

  }, {
    key: 'importPreviousPageHandler',
    value: function importPreviousPageHandler() {
      this._importPaginationHandler(BACKWARD);
    }

    /**
     * Handle the forward and back buttons actions in the import table.
     *
     * @param {string} direction
     * @private
     */

  }, {
    key: '_importPaginationHandler',
    value: function _importPaginationHandler(direction) {
      var $currentPageElements = $importDataTable.find('th:visible,td:visible');
      var $oppositePaginationButton = direction === FORWARD ? $('.js-import-next-page') : $('.js-import-previous-page');
      var lastVisibleColumnFound = false;
      var numberOfVisibleColumns = 0;
      var $tableColumns = $importDataTable.find('th');

      if (direction === BACKWARD) {
        // If going backward - reverse the table columns array and use the same logic as forward
        $tableColumns = $($tableColumns.toArray().reverse());
      }

      for (var index in $tableColumns) {
        if (isNaN(index)) {
          // Reached the last column - hide the opposite pagination button
          this._hide($oppositePaginationButton);
          break;
        }

        // Searching for last visible column
        if ($($tableColumns[index]).is(':visible')) {
          lastVisibleColumnFound = true;
          continue;
        }

        // If last visible column was found - show the column after it
        if (lastVisibleColumnFound) {
          // If going backward, the column index must be counted from the last element
          var showColumnIndex = direction === BACKWARD ? this.totalNumberOfColumns - 1 - index : index;
          this._showTableColumnByIndex(showColumnIndex);
          numberOfVisibleColumns++;

          // If number of visible columns per page is already reached - break the loop
          if (numberOfVisibleColumns >= this.numberOfColumnsPerPage) {
            this._hide($oppositePaginationButton);
            break;
          }
        }
      }

      // Hide all the columns from previous page
      this._hide($currentPageElements);

      // If the first column in the table is not visible - show the "previous" pagination arrow
      if (!$importDataTable.find('th:first').is(':visible')) {
        this._show($('.js-import-previous-page'));
      }

      // If the last column in the table is not visible - show the "next" pagination arrow
      if (!$importDataTable.find('th:last').is(':visible')) {
        this._show($('.js-import-next-page'));
      }
    }

    /**
     * Gets the number of currently visible columns in the import data table.
     *
     * @returns {number}
     * @private
     */

  }, {
    key: '_getNumberOfVisibleColumns',
    value: function _getNumberOfVisibleColumns() {
      return $importDataTable.find('th:visible').length;
    }

    /**
     * Gets the total number of columns in the import data table.
     *
     * @returns {number}
     * @private
     */

  }, {
    key: '_getTotalNumberOfColumns',
    value: function _getTotalNumberOfColumns() {
      return $importDataTable.find('th').length;
    }

    /**
     * Hide the elements.
     *
     * @param $elements
     * @private
     */

  }, {
    key: '_hide',
    value: function _hide($elements) {
      $elements.addClass('d-none');
    }

    /**
     * Show the elements.
     *
     * @param $elements
     * @private
     */

  }, {
    key: '_show',
    value: function _show($elements) {
      $elements.removeClass('d-none');
    }

    /**
     * Shows a column from import data table by given index
     *
     * @param columnIndex
     * @private
     */

  }, {
    key: '_showTableColumnByIndex',
    value: function _showTableColumnByIndex(columnIndex) {
      // Increasing the index because nth-child calculates from 1 and index starts from 0
      columnIndex++;

      this._show($importDataTable.find('th:nth-child(' + columnIndex + ')'));
      this._show($importDataTable.find('tbody > tr').find('td:nth-child(' + columnIndex + ')'));
    }
  }]);

  return ImportDataTable;
}();

/* harmony default export */ __webpack_exports__["a"] = (ImportDataTable);

/***/ }),

/***/ 315:
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

/**
 * Class is responsible for import match configuration
 * in Advanced parameters -> Import -> step 2 form.
 */

var ImportMatchConfiguration = function () {
  /**
   * Initializes all the processes related with import matches.
   */
  function ImportMatchConfiguration() {
    _classCallCheck(this, ImportMatchConfiguration);

    this.loadEvents();
  }

  /**
   * Loads all events for data match configuration.
   */


  _createClass(ImportMatchConfiguration, [{
    key: 'loadEvents',
    value: function loadEvents() {
      var _this = this;

      $(document).on('click', '.js-save-import-match', function (event) {
        return _this.save(event);
      });
      $(document).on('click', '.js-load-import-match', function (event) {
        return _this.load(event);
      });
      $(document).on('click', '.js-delete-import-match', function (event) {
        return _this.delete(event);
      });
    }

    /**
     * Save the import match configuration.
     */

  }, {
    key: 'save',
    value: function save(event) {
      var _this2 = this;

      event.preventDefault();
      var ajaxUrl = $('.js-save-import-match').attr('data-url');
      var formData = $('.import-data-configuration-form').serialize();

      $.ajax({
        type: 'POST',
        url: ajaxUrl,
        data: formData
      }).then(function (response) {
        if (typeof response.errors !== 'undefined' && response.errors.length) {
          _this2._showErrorPopUp(response.errors);
        } else if (response.matches.length > 0) {
          var $dataMatchesDropdown = _this2.matchesDropdown;

          for (var key in response.matches) {
            var $existingMatch = $dataMatchesDropdown.find('option[value=' + response.matches[key].id_import_match + ']');

            // If match already exists with same id - do nothing
            if ($existingMatch.length > 0) {
              continue;
            }

            // Append the new option to the matches dropdown
            _this2._appendOptionToDropdown($dataMatchesDropdown, response.matches[key].name, response.matches[key].id_import_match);
          }
        }
      });
    }

    /**
     * Load the import match.
     */

  }, {
    key: 'load',
    value: function load(event) {
      var _this3 = this;

      event.preventDefault();
      var ajaxUrl = $('.js-load-import-match').attr('data-url');

      $.ajax({
        type: 'GET',
        url: ajaxUrl,
        data: {
          import_match_id: this.matchesDropdown.val()
        }
      }).then(function (response) {
        if (response) {
          _this3.rowsSkipInput.val(response.skip);

          var entityFields = response.match.split('|');

          for (var i in entityFields) {
            $('#type_value_' + i).val(entityFields[i]);
          }
        }
      });
    }

    /**
     * Delete the import match.
     */

  }, {
    key: 'delete',
    value: function _delete(event) {
      event.preventDefault();
      var ajaxUrl = $('.js-delete-import-match').attr('data-url');
      var $dataMatchesDropdown = this.matchesDropdown;
      var selectedMatchId = $dataMatchesDropdown.val();

      $.ajax({
        type: 'DELETE',
        url: ajaxUrl,
        data: {
          import_match_id: selectedMatchId
        }
      }).then(function () {
        // Delete the match option from matches dropdown
        $dataMatchesDropdown.find('option[value=' + selectedMatchId + ']').remove();
      });
    }

    /**
     * Appends a new option to given dropdown.
     *
     * @param {jQuery} $dropdown
     * @param {String} optionText
     * @param {String} optionValue
     * @private
     */

  }, {
    key: '_appendOptionToDropdown',
    value: function _appendOptionToDropdown($dropdown, optionText, optionValue) {
      var $newOption = $('<option>');

      $newOption.attr('value', optionValue);
      $newOption.text(optionText);

      $dropdown.append($newOption);
    }

    /**
     * Shows error messages in the native error pop-up.
     *
     * @param {Array} errors
     * @private
     */

  }, {
    key: '_showErrorPopUp',
    value: function _showErrorPopUp(errors) {
      alert(errors);
    }

    /**
     * Get the matches dropdown.
     *
     * @returns {*|HTMLElement}
     */

  }, {
    key: 'matchesDropdown',
    get: function get() {
      return $('#matches');
    }

    /**
     * Get the "rows to skip" input.
     *
     * @returns {*|HTMLElement}
     */

  }, {
    key: 'rowsSkipInput',
    get: function get() {
      return $('#skip');
    }
  }]);

  return ImportMatchConfiguration;
}();

/* harmony default export */ __webpack_exports__["a"] = (ImportMatchConfiguration);

/***/ }),

/***/ 316:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

/**
 * 2007-2018 PrestaShop.
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

var ImportProgressModal = function () {
  function ImportProgressModal() {
    _classCallCheck(this, ImportProgressModal);
  }

  _createClass(ImportProgressModal, [{
    key: 'show',

    /**
     * Show the import progress modal window.
     */
    value: function show() {
      this.progressModal.modal('show');
    }

    /**
     * Hide the import progress modal window.
     */

  }, {
    key: 'hide',
    value: function hide() {
      this.progressModal.modal('hide');
    }

    /**
     * Updates the import progressbar.
     *
     * @param {number} completed number of completed items.
     * @param {number} total number of items in total.
     */

  }, {
    key: 'updateProgress',
    value: function updateProgress(completed, total) {
      completed = parseInt(completed);
      total = parseInt(total);

      var $progressBar = this.progressBar,
          percentage = completed / total * 100;

      $progressBar.css('width', percentage + '%');
      $progressBar.find('> span').text(completed + '/' + total);
    }

    /**
     * Updates the progress bar label.
     *
     * @param {String} label if not provided - will use the default label
     */

  }, {
    key: 'updateProgressLabel',
    value: function updateProgressLabel(label) {
      this.progressLabel.text(label);
    }

    /**
     * Sets the progress label to "importing"
     */

  }, {
    key: 'setImportingProgressLabel',
    value: function setImportingProgressLabel() {
      this.updateProgressLabel(this.progressModal.find('.modal-body').data('importing-label'));
    }

    /**
     * Sets the progress label to "imported"
     */

  }, {
    key: 'setImportedProgressLabel',
    value: function setImportedProgressLabel() {
      this.updateProgressLabel(this.progressModal.find('.modal-body').data('imported-label'));
    }

    /**
     * Shows information messages in the import modal.
     *
     * @param {Array} messages
     */

  }, {
    key: 'showInfoMessages',
    value: function showInfoMessages(messages) {
      this._showMessages(this.infoMessageBlock, messages);
    }

    /**
     * Shows warning messages in the import modal.
     *
     * @param {Array} messages
     */

  }, {
    key: 'showWarningMessages',
    value: function showWarningMessages(messages) {
      this._showMessages(this.warningMessageBlock, messages);
    }

    /**
     * Shows error messages in the import modal.
     *
     * @param {Array} messages
     */

  }, {
    key: 'showErrorMessages',
    value: function showErrorMessages(messages) {
      this._showMessages(this.errorMessageBlock, messages);
    }

    /**
     * Shows the import success message.
     */

  }, {
    key: 'showSuccessMessage',
    value: function showSuccessMessage() {
      this.successMessageBlock.removeClass('d-none');
    }

    /**
     * Shows the post size limit warning message.
     *
     * @param {number} postSizeValue to be shown in the warning
     */

  }, {
    key: 'showPostLimitMessage',
    value: function showPostLimitMessage(postSizeValue) {
      this.postLimitMessage.find('#post_limit_value').text(postSizeValue);
      this.postLimitMessage.removeClass('d-none');
    }

    /**
     * Show messages in given message block.
     *
     * @param {jQuery} $messageBlock
     * @param {Array} messages
     * @private
     */

  }, {
    key: '_showMessages',
    value: function _showMessages($messageBlock, messages) {
      var showMessagesBlock = false;

      for (var key in messages) {
        // Indicate that the messages block should be displayed
        showMessagesBlock = true;

        var message = $('<div>');
        message.text(messages[key]);
        message.addClass('message');

        $messageBlock.append(message);
      }

      if (showMessagesBlock) {
        $messageBlock.removeClass('d-none');
      }
    }

    /**
     * Show the "Ignore warnings and continue" button.
     */

  }, {
    key: 'showContinueImportButton',
    value: function showContinueImportButton() {
      this.continueImportButton.removeClass('d-none');
    }

    /**
     * Hide the "Ignore warnings and continue" button.
     */

  }, {
    key: 'hideContinueImportButton',
    value: function hideContinueImportButton() {
      this.continueImportButton.addClass('d-none');
    }

    /**
     * Show the "Abort import" button.
     */

  }, {
    key: 'showAbortImportButton',
    value: function showAbortImportButton() {
      this.abortImportButton.removeClass('d-none');
    }

    /**
     * Hide the "Abort import" button.
     */

  }, {
    key: 'hideAbortImportButton',
    value: function hideAbortImportButton() {
      this.abortImportButton.addClass('d-none');
    }

    /**
     * Show the "Close" button of the modal.
     */

  }, {
    key: 'showCloseModalButton',
    value: function showCloseModalButton() {
      this.closeModalButton.removeClass('d-none');
    }

    /**
     * Hide the "Close" button.
     */

  }, {
    key: 'hideCloseModalButton',
    value: function hideCloseModalButton() {
      this.closeModalButton.addClass('d-none');
    }

    /**
     * Clears all warning messages from the modal.
     */

  }, {
    key: 'clearWarningMessages',
    value: function clearWarningMessages() {
      this.warningMessageBlock.addClass('d-none').find('.message').remove();
    }

    /**
     * Reset the modal - resets progress bar and removes messages.
     */

  }, {
    key: 'reset',
    value: function reset() {
      // Reset the progress bar
      this.updateProgress(0, 0);
      this.updateProgressLabel(this.progressLabel.attr('default-value'));

      // Hide action buttons
      this.continueImportButton.addClass('d-none');
      this.abortImportButton.addClass('d-none');
      this.closeModalButton.addClass('d-none');

      // Remove messages
      this.successMessageBlock.addClass('d-none');
      this.infoMessageBlock.addClass('d-none').find('.message').remove();
      this.errorMessageBlock.addClass('d-none').find('.message').remove();
      this.postLimitMessage.addClass('d-none');
      this.clearWarningMessages();
    }

    /**
     * Gets import progress modal.
     *
     * @returns {jQuery}
     */

  }, {
    key: 'progressModal',
    get: function get() {
      return $('#import_progress_modal');
    }

    /**
     * Gets import progress bar.
     *
     * @returns {jQuery}
     */

  }, {
    key: 'progressBar',
    get: function get() {
      return this.progressModal.find('.progress-bar');
    }

    /**
     * Gets information messages block.
     *
     * @returns {jQuery|HTMLElement}
     */

  }, {
    key: 'infoMessageBlock',
    get: function get() {
      return $('.js-import-info');
    }

    /**
     * Gets error messages block.
     *
     * @returns {jQuery|HTMLElement}
     */

  }, {
    key: 'errorMessageBlock',
    get: function get() {
      return $('.js-import-errors');
    }

    /**
     * Gets warning messages block.
     *
     * @returns {jQuery|HTMLElement}
     */

  }, {
    key: 'warningMessageBlock',
    get: function get() {
      return $('.js-import-warnings');
    }

    /**
     * Gets success messages block.
     *
     * @returns {jQuery|HTMLElement}
     */

  }, {
    key: 'successMessageBlock',
    get: function get() {
      return $('.js-import-success');
    }

    /**
     * Gets post limit message.
     *
     * @returns {jQuery|HTMLElement}
     */

  }, {
    key: 'postLimitMessage',
    get: function get() {
      return $('.js-post-limit-warning');
    }

    /**
     * Gets "Ignore warnings and continue" button.
     *
     * @returns {jQuery|HTMLElement}
     */

  }, {
    key: 'continueImportButton',
    get: function get() {
      return $('.js-continue-import');
    }

    /**
     * Gets "Abort import" button.
     *
     * @returns {jQuery|HTMLElement}
     */

  }, {
    key: 'abortImportButton',
    get: function get() {
      return $('.js-abort-import');
    }

    /**
     * Gets "Close" button of the modal.
     *
     * @returns {jQuery|HTMLElement}
     */

  }, {
    key: 'closeModalButton',
    get: function get() {
      return $('.js-close-modal');
    }

    /**
     * Gets progress bar label.
     *
     * @returns {jQuery|HTMLElement}
     */

  }, {
    key: 'progressLabel',
    get: function get() {
      return $('#import_progress_bar').find('.progress-details-text');
    }
  }]);

  return ImportProgressModal;
}();

/* harmony default export */ __webpack_exports__["a"] = (ImportProgressModal);

/***/ }),

/***/ 317:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__ImportProgressModal__ = __webpack_require__(316);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__ImportBatchSizeCalculator__ = __webpack_require__(312);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__PostSizeChecker__ = __webpack_require__(318);
var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

/**
 * 2007-2018 PrestaShop.
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





var Importer = function () {
  function Importer() {
    _classCallCheck(this, Importer);

    this.progressModal = new __WEBPACK_IMPORTED_MODULE_0__ImportProgressModal__["a" /* default */]();
    this.batchSizeCalculator = new __WEBPACK_IMPORTED_MODULE_1__ImportBatchSizeCalculator__["a" /* default */]();
    this.postSizeChecker = new __WEBPACK_IMPORTED_MODULE_2__PostSizeChecker__["a" /* default */]();

    // Default number of rows in one batch of the import.
    this.defaultBatchSize = 5;
  }

  /**
   * Process the import.
   *
   * @param {Object} configuration import configuration.
   */


  _createClass(Importer, [{
    key: 'import',
    value: function _import(configuration) {
      this.configuration = {
        ajax: 1,
        action: 'import',
        tab: 'AdminImport',
        token: token
      };
      this._mergeConfiguration(configuration);

      // Total number of rows to be imported.
      this.totalRowsCount = 0;

      // Flags that mark that there were warnings or errors during import.
      this.hasWarnings = false;
      this.hasErrors = false;

      // Resetting the import progress modal and showing it.
      this.progressModal.reset();
      this.progressModal.show();

      // Starting the import with 5 elements in batch.
      this._ajaxImport(0, this.defaultBatchSize);
    }

    /**
     * Process the ajax import request.
     *
     * @param {number} offset row number, from which the import job will start processing data.
     * @param {number} batchSize batch size of this import job.
     * @param {boolean} validateOnly whether the data should be only validated, if false - the data will be imported.
     * @param {number} stepIndex current step index, retrieved from the ajax response
     * @param {Object} recurringVariables variables which are recurring between import batch jobs.
     * @private
     */

  }, {
    key: '_ajaxImport',
    value: function _ajaxImport(offset, batchSize) {
      var validateOnly = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : true;

      var _this = this;

      var stepIndex = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : 0;
      var recurringVariables = arguments.length > 4 && arguments[4] !== undefined ? arguments[4] : {};

      this._mergeConfiguration({
        offset: offset,
        limit: batchSize,
        validateOnly: validateOnly ? 1 : 0,
        moreStep: stepIndex,
        crossStepsVars: JSON.stringify(recurringVariables)
      });

      this._onImportStart();

      $.post({
        url: 'index.php',
        dataType: 'json',
        data: this.configuration,
        success: function success(response) {
          if (_this._importCancelRequested) {
            _this._cancelImport();
            return false;
          }

          var nextStepIndex = response.oneMoreStep !== undefined ? response.oneMoreStep : stepIndex;

          if (response.totalCount !== undefined) {
            // The total rows count is retrieved only in the first batch response.
            _this.totalRowsCount = response.totalCount;
          }

          // Update import progress.
          _this.progressModal.updateProgress(response.doneCount, _this.totalRowsCount);

          if (!validateOnly) {
            // Set the progress label to "Importing".
            _this.progressModal.setImportingProgressLabel();
          }

          // Information messages are not shown during validation.
          if (!validateOnly && response.informations) {
            _this.progressModal.showInfoMessages(response.informations);
          }

          if (response.errors) {
            _this.hasErrors = true;
            _this.progressModal.showErrorMessages(response.errors);

            // If there are errors and it's not validation step - stop the import.
            // If it's validation step - we will show all errors once it finishes.
            if (!validateOnly) {
              _this._onImportStop();
              return false;
            }
          } else if (response.warnings) {
            _this.hasWarnings = true;
            _this.progressModal.showWarningMessages(response.warnings);
          }

          if (!response.isFinished) {
            // Marking the end of import operation.
            _this.batchSizeCalculator.markImportEnd();

            // Calculate next import batch size and offset.
            var nextBatchSize = _this.batchSizeCalculator.calculateBatchSize(batchSize);
            var nextOffset = offset + batchSize;

            // Showing a warning if post size limit is about to be reached.
            if (_this.postSizeChecker.isReachingPostSizeLimit(response.postSizeLimit, response.nextPostSize)) {
              _this.progressModal.showPostLimitMessage(_this.postSizeChecker.getRequiredPostSizeInMegabytes(response.nextPostSize));
            }

            // Run the import again for the next batch.
            return _this._ajaxImport(nextOffset, nextBatchSize, validateOnly, nextStepIndex, response.crossStepsVariables);
          }

          // All import batches are finished successfully.
          // If it was only validating the import data until this point,
          // we have to run the data import now.
          if (validateOnly) {
            // If errors occurred during validation - stop the import.
            if (_this.hasErrors) {
              _this._onImportStop();
              return false;
            }

            if (_this.hasWarnings) {
              // Show the button to ignore warnings.
              _this.progressModal.showContinueImportButton();
              _this._onImportStop();
              return false;
            }

            // Update the progress bar to 100%.
            _this.progressModal.updateProgress(_this.totalRowsCount, _this.totalRowsCount);

            // Continue with the data import.
            return _this._ajaxImport(0, _this.defaultBatchSize, false);
          }

          if (stepIndex < nextStepIndex) {
            // If it's still not the last step of the import - continue with the next step.
            return _this._ajaxImport(0, _this.defaultBatchSize, false, nextStepIndex, response.crossStepsVariables);
          }

          // Import is completely finished.
          _this._onImportFinish();
        },
        error: function error(XMLHttpRequest, textStatus, errorCode) {
          if (textStatus === 'parsererror') {
            textStatus = 'Technical error: Unexpected response returned by server. Import stopped.';
          }

          _this._onImportStop();
          _this.progressModal.showErrorMessages([textStatus]);
        }
      });
    }

    /**
     * Continue the import when it was stopped.
     */

  }, {
    key: 'continueImport',
    value: function continueImport() {
      if (!this.configuration) {
        throw 'Missing import configuration. Make sure the import had started before continuing.';
      }

      this.progressModal.hideContinueImportButton();
      this.progressModal.hideCloseModalButton();
      this.progressModal.clearWarningMessages();
      this._ajaxImport(0, this.defaultBatchSize, false);
    }

    /**
     * Set the import configuration.
     *
     * @param importConfiguration
     */

  }, {
    key: 'requestCancelImport',


    /**
     * Request import cancellation.
     * Import operation will be cancelled at next iteration when requested.
     */
    value: function requestCancelImport() {
      this._importCancelRequested = true;
    }

    /**
     * Merge given configuration into current import configuration.
     *
     * @param {Object} configuration
     * @private
     */

  }, {
    key: '_mergeConfiguration',
    value: function _mergeConfiguration(configuration) {
      for (var key in configuration) {
        this._importConfiguration[key] = configuration[key];
      }
    }

    /**
     * Cancel the import process.
     * @private
     */

  }, {
    key: '_cancelImport',
    value: function _cancelImport() {
      this.progressModal.hide();
      this._importCancelRequested = false;
    }

    /**
     * Additional actions when import is stopped.
     * @private
     */

  }, {
    key: '_onImportStop',
    value: function _onImportStop() {
      this.progressModal.showCloseModalButton();
      this.progressModal.hideAbortImportButton();
    }

    /**
     * Additional actions when import is finished.
     * @private
     */

  }, {
    key: '_onImportFinish',
    value: function _onImportFinish() {
      this._onImportStop();
      this.progressModal.showSuccessMessage();
      this.progressModal.setImportedProgressLabel();
      this.progressModal.updateProgress(this.totalRowsCount, this.totalRowsCount);
    }

    /**
     * Additional actions when import is starting.
     * @private
     */

  }, {
    key: '_onImportStart',
    value: function _onImportStart() {
      // Marking the start of import operation.
      this.batchSizeCalculator.markImportStart();
      this.progressModal.showAbortImportButton();
    }
  }, {
    key: 'configuration',
    set: function set(importConfiguration) {
      this._importConfiguration = importConfiguration;
    }

    /**
     * Get the import configuration.
     *
     * @returns {*}
     */
    ,
    get: function get() {
      return this._importConfiguration;
    }

    /**
     * Set progress modal.
     *
     * @param {ImportProgressModal} modal
     */

  }, {
    key: 'progressModal',
    set: function set(modal) {
      this._modal = modal;
    }

    /**
     * Get progress modal.
     *
     * @returns {ImportProgressModal}
     */
    ,
    get: function get() {
      return this._modal;
    }
  }]);

  return Importer;
}();

/* harmony default export */ __webpack_exports__["a"] = (Importer);

/***/ }),

/***/ 318:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

/**
 * 2007-2018 PrestaShop.
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

var PostSizeChecker = function () {
  function PostSizeChecker() {
    _classCallCheck(this, PostSizeChecker);

    // How close can we get to the post size limit. 0.9 means 90%.
    this.postSizeLimitThreshold = 0.9;
  }

  /**
   * Check if given postSizeLimit is reaching the required post size
   *
   * @param {number} postSizeLimit
   * @param {number} requiredPostSize
   *
   * @returns {boolean}
   */


  _createClass(PostSizeChecker, [{
    key: "isReachingPostSizeLimit",
    value: function isReachingPostSizeLimit(postSizeLimit, requiredPostSize) {
      return requiredPostSize >= postSizeLimit * this.postSizeLimitThreshold;
    }

    /**
     * Get required post size in megabytes.
     *
     * @param {number} requiredPostSize
     *
     * @returns {number}
     */

  }, {
    key: "getRequiredPostSizeInMegabytes",
    value: function getRequiredPostSizeInMegabytes(requiredPostSize) {
      return parseInt(requiredPostSize / (1024 * 1024));
    }
  }]);

  return PostSizeChecker;
}();

/* harmony default export */ __webpack_exports__["a"] = (PostSizeChecker);

/***/ }),

/***/ 522:
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(246);


/***/ })

/******/ });