/******/!function(e){// webpackBootstrap
/******/
<<<<<<< HEAD
function n(e){/******/
delete installedChunks[e]}function t(e){var n=document.getElementsByTagName("head")[0],t=document.createElement("script");t.type="text/javascript",t.charset="utf-8",t.src=h.p+""+e+"."+b+".hot-update.js",n.appendChild(t)}function r(){return new Promise(function(e,n){if("undefined"==typeof XMLHttpRequest)return n(new Error("No browser support"));try{var t=new XMLHttpRequest,r=h.p+""+b+".hot-update.json";t.open("GET",r,!0),t.timeout=1e4,t.send(null)}catch(e){return n(e)}t.onreadystatechange=function(){if(4===t.readyState)if(0===t.status)n(new Error("Manifest request to "+r+" timed out."));else if(404===t.status)e();else if(200!==t.status&&304!==t.status)n(new Error("Manifest request to "+r+" failed."));else{try{var o=JSON.parse(t.responseText)}catch(e){return void n(e)}e(o)}}})}function o(e){var n=A[e];if(!n)return h;var t=function(t){return n.hot.active?(A[t]?A[t].parents.indexOf(e)<0&&A[t].parents.push(e):(k=[e],v=t),n.children.indexOf(t)<0&&n.children.push(t)):k=[],h(t)};for(var r in h)Object.prototype.hasOwnProperty.call(h,r)&&"e"!==r&&Object.defineProperty(t,r,function(e){return{configurable:!0,enumerable:!0,get:function(){return h[e]},set:function(n){h[e]=n}}}(r));return t.e=function(e){function n(){D--,"prepare"===O&&(P[e]||l(e),0===D&&0===x&&d())}return"ready"===O&&c("prepare"),D++,h.e(e).then(n,function(e){throw n(),e})},t}function i(e){var n={_acceptedDependencies:{},_declinedDependencies:{},_selfAccepted:!1,_selfDeclined:!1,_disposeHandlers:[],_main:v!==e,active:!0,accept:function(e,t){if(void 0===e)n._selfAccepted=!0;else if("function"==typeof e)n._selfAccepted=e;else if("object"==typeof e)for(var r=0;r<e.length;r++)n._acceptedDependencies[e[r]]=t||function(){};else n._acceptedDependencies[e]=t||function(){}},decline:function(e){if(void 0===e)n._selfDeclined=!0;else if("object"==typeof e)for(var t=0;t<e.length;t++)n._declinedDependencies[e[t]]=!0;else n._declinedDependencies[e]=!0},dispose:function(e){n._disposeHandlers.push(e)},addDisposeHandler:function(e){n._disposeHandlers.push(e)},removeDisposeHandler:function(e){var t=n._disposeHandlers.indexOf(e);t>=0&&n._disposeHandlers.splice(t,1)},check:s,apply:f,status:function(e){if(!e)return O;j.push(e)},addStatusHandler:function(e){j.push(e)},removeStatusHandler:function(e){var n=j.indexOf(e);n>=0&&j.splice(n,1)},data:w[e]};return v=void 0,n}function c(e){O=e;for(var n=0;n<j.length;n++)j[n].call(null,e)}function a(e){return+e+""===e?+e:e}function s(e){if("idle"!==O)throw new Error("check() is only allowed in idle status");return g=e,c("check"),r().then(function(e){if(!e)return c("idle"),null;S={},P={},C=e.c,_=e.h,c("prepare");var n=new Promise(function(e,n){m={resolve:e,reject:n}});y={};return l(3),"prepare"===O&&0===D&&0===x&&d(),n})}function u(e,n){if(C[e]&&S[e]){S[e]=!1;for(var t in n)Object.prototype.hasOwnProperty.call(n,t)&&(y[t]=n[t]);0==--x&&0===D&&d()}}function l(e){C[e]?(S[e]=!0,x++,t(e)):P[e]=!0}function d(){c("ready");var e=m;if(m=null,e)if(g)f(g).then(function(n){e.resolve(n)},function(n){e.reject(n)});else{var n=[];for(var t in y)Object.prototype.hasOwnProperty.call(y,t)&&n.push(a(t));e.resolve(n)}}function f(t){function r(e,n){for(var t=0;t<n.length;t++){var r=n[t];e.indexOf(r)<0&&e.push(r)}}if("ready"!==O)throw new Error("apply() is only allowed in ready status");t=t||{};var o,i,s,u,l,d={},f=[],p={},v=function(){};for(var m in y)if(Object.prototype.hasOwnProperty.call(y,m)){l=a(m);var g;g=y[m]?function(e){for(var n=[e],t={},o=n.slice().map(function(e){return{chain:[e],id:e}});o.length>0;){var i=o.pop(),c=i.id,a=i.chain;if((u=A[c])&&!u.hot._selfAccepted){if(u.hot._selfDeclined)return{type:"self-declined",chain:a,moduleId:c};if(u.hot._main)return{type:"unaccepted",chain:a,moduleId:c};for(var s=0;s<u.parents.length;s++){var l=u.parents[s],d=A[l];if(d){if(d.hot._declinedDependencies[c])return{type:"declined",chain:a.concat([l]),moduleId:c,parentId:l};n.indexOf(l)>=0||(d.hot._acceptedDependencies[c]?(t[l]||(t[l]=[]),r(t[l],[c])):(delete t[l],n.push(l),o.push({chain:a.concat([l]),id:l})))}}}}return{type:"accepted",moduleId:e,outdatedModules:n,outdatedDependencies:t}}(l):{type:"disposed",moduleId:m};var E=!1,j=!1,x=!1,D="";switch(g.chain&&(D="\nUpdate propagation: "+g.chain.join(" -> ")),g.type){case"self-declined":t.onDeclined&&t.onDeclined(g),t.ignoreDeclined||(E=new Error("Aborted because of self decline: "+g.moduleId+D));break;case"declined":t.onDeclined&&t.onDeclined(g),t.ignoreDeclined||(E=new Error("Aborted because of declined dependency: "+g.moduleId+" in "+g.parentId+D));break;case"unaccepted":t.onUnaccepted&&t.onUnaccepted(g),t.ignoreUnaccepted||(E=new Error("Aborted because "+l+" is not accepted"+D));break;case"accepted":t.onAccepted&&t.onAccepted(g),j=!0;break;case"disposed":t.onDisposed&&t.onDisposed(g),x=!0;break;default:throw new Error("Unexception type "+g.type)}if(E)return c("abort"),Promise.reject(E);if(j){p[l]=y[l],r(f,g.outdatedModules);for(l in g.outdatedDependencies)Object.prototype.hasOwnProperty.call(g.outdatedDependencies,l)&&(d[l]||(d[l]=[]),r(d[l],g.outdatedDependencies[l]))}x&&(r(f,[g.moduleId]),p[l]=v)}var P=[];for(i=0;i<f.length;i++)l=f[i],A[l]&&A[l].hot._selfAccepted&&P.push({module:l,errorHandler:A[l].hot._selfAccepted});c("dispose"),Object.keys(C).forEach(function(e){!1===C[e]&&n(e)});for(var S,U=f.slice();U.length>0;)if(l=U.pop(),u=A[l]){var B={},R=u.hot._disposeHandlers;for(s=0;s<R.length;s++)(o=R[s])(B);for(w[l]=B,u.hot.active=!1,delete A[l],s=0;s<u.children.length;s++){var $=A[u.children[s]];$&&((S=$.parents.indexOf(l))>=0&&$.parents.splice(S,1))}}var I,q;for(l in d)if(Object.prototype.hasOwnProperty.call(d,l)&&(u=A[l]))for(q=d[l],s=0;s<q.length;s++)I=q[s],(S=u.children.indexOf(I))>=0&&u.children.splice(S,1);c("apply"),b=_;for(l in p)Object.prototype.hasOwnProperty.call(p,l)&&(e[l]=p[l]);var H=null;for(l in d)if(Object.prototype.hasOwnProperty.call(d,l)){u=A[l],q=d[l];var L=[];for(i=0;i<q.length;i++)I=q[i],o=u.hot._acceptedDependencies[I],L.indexOf(o)>=0||L.push(o);for(i=0;i<L.length;i++){o=L[i];try{o(q)}catch(e){t.onErrored&&t.onErrored({type:"accept-errored",moduleId:l,dependencyId:q[i],error:e}),t.ignoreErrored||H||(H=e)}}}for(i=0;i<P.length;i++){var M=P[i];l=M.module,k=[l];try{h(l)}catch(e){if("function"==typeof M.errorHandler)try{M.errorHandler(e)}catch(n){t.onErrored&&t.onErrored({type:"self-accept-error-handler-errored",moduleId:l,error:n,orginalError:e}),t.ignoreErrored||H||(H=n),H||(H=e)}else t.onErrored&&t.onErrored({type:"self-accept-errored",moduleId:l,error:e}),t.ignoreErrored||H||(H=e)}}return H?(c("fail"),Promise.reject(H)):(c("idle"),new Promise(function(e){e(f)}))}function h(n){if(A[n])return A[n].exports;var t=A[n]={i:n,l:!1,exports:{},hot:i(n),parents:(E=k,k=[],E),children:[]};return e[n].call(t.exports,t,t.exports,o(n)),t.l=!0,t.exports}var p=this.webpackHotUpdate;this.webpackHotUpdate=function(e,n){u(e,n),p&&p(e,n)};var v,m,y,_,g=!0,b="84677860fd7781d2a9ae",w={},k=[],E=[],j=[],O="idle",x=0,D=0,P={},S={},C={},A={};h.m=e,h.c=A,h.i=function(e){return e},h.d=function(e,n,t){h.o(e,n)||Object.defineProperty(e,n,{configurable:!1,enumerable:!0,get:t})},h.n=function(e){var n=e&&e.__esModule?function(){return e.default}:function(){return e};return h.d(n,"a",n),n},h.o=function(e,n){return Object.prototype.hasOwnProperty.call(e,n)},h.p="",h.h=function(){return b},o(362)(h.s=362)}({16:function(e,n,t){"use strict";(function(e){var r=t(31),o=(t.n(r),e.$),i=function(){o('.datepicker input[type="text"]').datetimepicker({locale:e.full_language_code,format:"YYYY-MM-DD"})};n.a=i}).call(n,t(2))},176:function(e,n,t){"use strict";Object.defineProperty(n,"__esModule",{value:!0}),function(e){var n=t(210);(0,e.$)(function(){new n.a("#logs_grid_panel").init()})}.call(n,t(2))},18:function(e,n,t){"use strict";(function(e){function t(e,n){if(!(e instanceof n))throw new TypeError("Cannot call a class as a function")}var r=function(){function e(e,n){for(var t=0;t<n.length;t++){var r=n[t];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(e,r.key,r)}}return function(n,t,r){return t&&e(n.prototype,t),r&&e(n,r),n}}(),o=e.$,i=function(){function e(n){t(this,e),this.selector=".ps-sortable-column",this.columns=o(n).find(this.selector)}return r(e,[{key:"attach",value:function(){var e=this;this.columns.on("click",function(n){var t=o(n.delegateTarget);e._sortByColumn(t,e._getToggledSortDirection(t))})}},{key:"sortBy",value:function(e,n){var t=this.columns.is('[data-sort-col-name="'+e+'"]');if(!t)throw new Error('Cannot sort by "'+e+'": invalid column');this._sortByColumn(t,n)}},{key:"_sortByColumn",value:function(e,n){window.location=this._getUrl(e.data("sortColName"),"desc"===n?"desc":"asc")}},{key:"_getToggledSortDirection",value:function(e){return"asc"===e.data("sortDirection")?"desc":"asc"}},{key:"_getUrl",value:function(e,n){var t=new URL(window.location.href),r=t.searchParams;return r.set("orderBy",e),r.set("sortOrder",n),t.toString()}}]),e}();n.a=i}).call(n,t(2))},2:function(e,n){var t;t=function(){return this}();try{t=t||Function("return this")()||(0,eval)("this")}catch(e){"object"==typeof window&&(t=window)}e.exports=t},207:function(e,n,t){"use strict";(function(e){/**
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
/******/ 			var chunkId = 4;
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
/******/ 	return hotCreateRequire(449)(__webpack_require__.s = 449);
/******/ })
/************************************************************************/
/******/ ({

/***/ 22:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function(global) {var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

var $ = global.$;

/**
 * Makes a table sortable by columns.
 * This forces a page reload with more query parameters.
 */

var TableSorting = function () {

  /**
   * @param {jQuery} table
   */
  function TableSorting(table) {
    _classCallCheck(this, TableSorting);

    this.selector = '.ps-sortable-column';
    this.columns = $(table).find(this.selector);
  }

  /**
   * Attaches the listeners
   */


  _createClass(TableSorting, [{
    key: 'attach',
    value: function attach() {
      var _this = this;

      this.columns.on('click', function (e) {
        var $column = $(e.delegateTarget);
        _this._sortByColumn($column, _this._getToggledSortDirection($column));
      });
    }

    /**
     * Sort using a column name
     * @param {string} columnName
     * @param {string} direction "asc" or "desc"
     */

  }, {
    key: 'sortBy',
    value: function sortBy(columnName, direction) {
      var $column = this.columns.is('[data-sort-col-name="' + columnName + '"]');
      if (!$column) {
        throw new Error('Cannot sort by "' + columnName + '": invalid column');
      }

      this._sortByColumn($column, direction);
    }

    /**
     * Sort using a column element
     * @param {jQuery} column
     * @param {string} direction "asc" or "desc"
     * @private
     */

  }, {
    key: '_sortByColumn',
    value: function _sortByColumn(column, direction) {
      window.location = this._getUrl(column.data('sortColName'), direction === 'desc' ? 'desc' : 'asc');
    }

    /**
     * Returns the inverted direction to sort according to the column's current one
     * @param {jQuery} column
     * @return {string}
     * @private
     */

  }, {
    key: '_getToggledSortDirection',
    value: function _getToggledSortDirection(column) {
      return column.data('sortDirection') === 'asc' ? 'desc' : 'asc';
    }

    /**
     * Returns the url for the sorted table
     * @param {string} colName
     * @param {string} direction
     * @return {string}
     * @private
     */

  }, {
    key: '_getUrl',
    value: function _getUrl(colName, direction) {
      var url = new URL(window.location.href);
      var params = url.searchParams;

      params.set('orderBy', colName);
      params.set('sortOrder', direction);

      return url.toString();
    }
  }]);

  return TableSorting;
}();

/* harmony default export */ __webpack_exports__["a"] = (TableSorting);
/* WEBPACK VAR INJECTION */}.call(__webpack_exports__, __webpack_require__(3)))

/***/ }),

/***/ 220:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* WEBPACK VAR INJECTION */(function(global) {/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__utils_table_sorting__ = __webpack_require__(22);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__utils_sql_manager__ = __webpack_require__(257);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__components_grid__ = __webpack_require__(32);
var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */





var $ = global.$;

var LogsPage = function () {
  function LogsPage() {
    _classCallCheck(this, LogsPage);
  }

  _createClass(LogsPage, [{
    key: 'init',
    value: function init() {
      new __WEBPACK_IMPORTED_MODULE_2__components_grid__["a" /* default */]('#logs_grid_panel').init();

      var $sortableTables = $('table.table');
      var $deleteAllLogsButton = $('#logs-deleteAll');
      var $refreshButton = $('#logs-refresh');
      var $showSqlQueryButton = $('#logs-showSqlQuery');
      var $exportSqlManagerButton = $('#logs-exportSqlManager');

      this.sqlManager = new __WEBPACK_IMPORTED_MODULE_1__utils_sql_manager__["a" /* default */]();

      new __WEBPACK_IMPORTED_MODULE_0__utils_table_sorting__["a" /* default */]($sortableTables).attach();

      $deleteAllLogsButton.on('click', this._onDeleteAllLogsClick.bind(this));
      $refreshButton.on('click', this._onRefreshClick.bind(this));
      $showSqlQueryButton.on('click', this._onShowSqlQueryClick.bind(this));
      $exportSqlManagerButton.on('click', this._onExportSqlManagerClick.bind(this));
    }

    /**
     * Invoked when clicking on the "delete all logs" toolbar button
     * @param {jQuery.Event} event
     * @private
     */

  }, {
    key: '_onDeleteAllLogsClick',
    value: function _onDeleteAllLogsClick(event) {
      var clickedButton = $(event.delegateTarget);
      var confirmationMessage = clickedButton.data('confirmMessage');
      var form = clickedButton.closest('form');
      if (global.confirm(confirmationMessage)) {
        form.submit();
      }
    }

    /**
     * Invoked when clicking on the "reload" toolbar button
     * @private
     */

  }, {
    key: '_onRefreshClick',
    value: function _onRefreshClick() {
      location.reload();
    }

    /**
     * Invoked when clicking on the "show sql query" toolbar button
     * @private
     */

  }, {
    key: '_onShowSqlQueryClick',
    value: function _onShowSqlQueryClick() {
      this.sqlManager.showLastSqlQuery();
    }

    /**
     * Invoked when clicking on the "export to the sql query" toolbar button
     * @private
     */

  }, {
    key: '_onExportSqlManagerClick',
    value: function _onExportSqlManagerClick() {
      this.sqlManager.sendLastSqlQuery(this.sqlManager.createSqlQueryName());
    }
  }]);

  return LogsPage;
}();

$(function () {
  new LogsPage().init();
});
/* WEBPACK VAR INJECTION */}.call(__webpack_exports__, __webpack_require__(3)))

/***/ }),

/***/ 257:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function(global) {var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

var $ = global.$;

/**
 * Allow to display the last SQL query in a modal and redirect to SQL Manager.
 */

var SqlManager = function () {
  function SqlManager() {
    _classCallCheck(this, SqlManager);
  }

  _createClass(SqlManager, [{
    key: 'showLastSqlQuery',
    value: function showLastSqlQuery() {
      $('#catalog_sql_query_modal_content textarea[name="sql"]').val($('tbody.sql-manager').data('query'));
      $('#catalog_sql_query_modal .btn-sql-submit').click(function () {
        $('#catalog_sql_query_modal_content').submit();
      });
      $('#catalog_sql_query_modal').modal('show');
    }
  }, {
    key: 'sendLastSqlQuery',
    value: function sendLastSqlQuery(name) {
      $('#catalog_sql_query_modal_content textarea[name="sql"]').val($('tbody.sql-manager').data('query'));
      $('#catalog_sql_query_modal_content input[name="name"]').val(name);
      $('#catalog_sql_query_modal_content').submit();
    }
  }, {
    key: 'createSqlQueryName',
    value: function createSqlQueryName() {
      var container = false;
      var current = false;
      if ($('.breadcrumb')) {
        container = $('.breadcrumb li').eq(0).text().replace(/\s+/g, ' ').trim();
        current = $('.breadcrumb li').eq(-1).text().replace(/\s+/g, ' ').trim();
      }
      var title = false;
      if ($('h2.title')) {
        title = $('h2.title').first().text().replace(/\s+/g, ' ').trim();
      }

      var name = false;
      if (container && current && container != current) {
        name = container + ' > ' + current;
      } else if (container) {
        name = container;
      } else if (current) {
        name = current;
      }

      if (title && title != current && title != container) {
        if (name) {
          name = name + ' > ' + title;
        } else {
          name = title;
        }
      }

      return name.trim();
    }
  }]);

  return SqlManager;
}();

/* harmony default export */ __webpack_exports__["a"] = (SqlManager);
/* WEBPACK VAR INJECTION */}.call(__webpack_exports__, __webpack_require__(3)))

/***/ }),

/***/ 3:
/***/ (function(module, exports) {

var g;

// This works in non-strict mode
g = (function() {
	return this;
})();

try {
	// This works if eval is allowed (see CSP)
	g = g || Function("return this")() || (1,eval)("this");
} catch(e) {
	// This works if the window reference is available
	if(typeof window === "object")
		g = window;
}

// g can still be undefined, but nothing to do about it...
// We return undefined, instead of nothing here, so it's
// easier to handle this case. if(!global) { ...}

module.exports = g;


/***/ }),

/***/ 32:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

/**
>>>>>>> 0c9e0a681f... rebuild assets
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
<<<<<<< HEAD
var t=e.$,r=function(e){t.post(e)};n.a=r}).call(n,t(2))},210:function(e,n,t){"use strict";function r(e,n){if(!(e instanceof n))throw new TypeError("Cannot call a class as a function")}var o=t(207),i=t(18),c=t(16),a=function(){function e(e,n){for(var t=0;t<n.length;t++){var r=n[t];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(e,r.key,r)}}return function(n,t,r){return t&&e(n.prototype,t),r&&e(n,r),n}}(),s=window.$,u=function(){function e(n){r(this,e),this.$grid=s(n)}return a(e,[{key:"init",value:function(){this._handleBulkActionSelectAllCheckbox(),this._handleBulkActionCheckboxSelect(),this._handleCommonGridActions(),this._handleSortingGrid(),this._enableDatePickers()}},{key:"_handleCommonGridActions",value:function(){var e=this,n=this.$grid.find(".js-grid").attr("id"),r="#"+n+"_action_",i=r+"common_refresh_list",c=r+"common_show_query",a=r+"common_export_sql_manager";this.$grid.on("click",i,function(){return e._onRefreshClick()}),this.$grid.on("click",c,function(){return e._onShowSqlQueryClick()}),this.$grid.on("click",a,function(){return e._onExportSqlManagerClick()}),s(".reset-search").on("click",function(e){t.i(o.a)(s(e.target).data("url"))})}},{key:"_handleSortingGrid",value:function(){var e=this.$grid.find("table.table");new i.a(e).attach()}},{key:"_enableDatePickers",value:function(){t.i(c.a)()}},{key:"_handleBulkActionSelectAllCheckbox",value:function(){var e=this;s(document).on("change",".js-select-all-bulk-actions-checkbox",function(n){var t=s(n.target),r=t.is(":checked");r?e._enableBulkActionsBtn():e._disableBulkActionsBtn(),e.$grid.find(".js-bulk-action-checkbox").prop("checked",r)})}},{key:"_handleBulkActionCheckboxSelect",value:function(){var e=this;this.$grid.on("change",".js-bulk-action-checkbox",function(){e.$grid.find(".js-bulk-action-checkbox:checked").length>0?e._enableBulkActionsBtn():e._disableBulkActionsBtn()})}},{key:"_enableBulkActionsBtn",value:function(){this.$grid.find(".js-bulk-actions-btn").prop("disabled",!1)}},{key:"_disableBulkActionsBtn",value:function(){this.$grid.find(".js-bulk-actions-btn").prop("disabled",!0)}},{key:"_onRefreshClick",value:function(){location.reload()}},{key:"_onShowSqlQueryClick",value:function(){var e=this.$grid.find(".js-grid").attr("id"),n=this.$grid.find(".js-grid-table").data("query"),t=s("#"+e+"_common_show_query_modal_form");t.find('textarea[name="sql"]').val(n);var r=s("#"+e+"_common_show_query_modal");r.modal("show"),r.on("click",".btn-sql-submit",function(){return t.submit()})}},{key:"_onExportSqlManagerClick",value:function(){var e=this.$grid.find(".js-grid").attr("id"),n=this.$grid.find(".js-grid-table").data("query"),t=s("#"+e+"_common_show_query_modal_form");t.find('textarea[name="sql"]').val(n),t.submit()}}]),e}();n.a=u},31:function(e,n,t){(function(e){!function(e){var n=function(){try{return!!Symbol.iterator}catch(e){return!1}}(),t=function(e){var t={next:function(){var n=e.shift();return{done:void 0===n,value:n}}};return n&&(t[Symbol.iterator]=function(){return t}),t};"URLSearchParams"in e&&"a=1"===new URLSearchParams("?a=1").toString()||function(){var r=function(e){if(Object.defineProperty(this,"_entries",{value:{}}),"string"==typeof e){if(""!==e){e=e.replace(/^\?/,"");for(var n,t=e.split("&"),o=0;o<t.length;o++)n=t[o].split("="),this.append(decodeURIComponent(n[0]),n.length>1?decodeURIComponent(n[1]):"")}}else if(e instanceof r){var i=this;e.forEach(function(e,n){i.append(e,n)})}},o=r.prototype;o.append=function(e,n){e in this._entries?this._entries[e].push(n.toString()):this._entries[e]=[n.toString()]},o.delete=function(e){delete this._entries[e]},o.get=function(e){return e in this._entries?this._entries[e][0]:null},o.getAll=function(e){return e in this._entries?this._entries[e].slice(0):[]},o.has=function(e){return e in this._entries},o.set=function(e,n){this._entries[e]=[n.toString()]},o.forEach=function(e,n){var t;for(var r in this._entries)if(this._entries.hasOwnProperty(r)){t=this._entries[r];for(var o=0;o<t.length;o++)e.call(n,t[o],r,this)}},o.keys=function(){var e=[];return this.forEach(function(n,t){e.push(t)}),t(e)},o.values=function(){var e=[];return this.forEach(function(n){e.push(n)}),t(e)},o.entries=function(){var e=[];return this.forEach(function(n,t){e.push([t,n])}),t(e)},n&&(o[Symbol.iterator]=o.entries),o.toString=function(){var e="";return this.forEach(function(n,t){e.length>0&&(e+="&"),e+=encodeURIComponent(t)+"="+encodeURIComponent(n)}),e},e.URLSearchParams=r}()}(void 0!==e?e:"undefined"!=typeof window?window:"undefined"!=typeof self?self:this),function(e){if(function(){try{var e=new URL("b","http://a");return e.pathname="c%20d","http://a/c%20d"===e.href&&e.searchParams}catch(e){return!1}}()||function(){var n=e.URL,t=function(e,n){"string"!=typeof e&&(e=String(e));var t=document.implementation.createHTMLDocument("");if(window.doc=t,n){var r=t.createElement("base");r.href=n,t.head.appendChild(r)}var o=t.createElement("a");if(o.href=e,t.body.appendChild(o),o.href=o.href,":"===o.protocol||!/:/.test(o.href))throw new TypeError("Invalid URL");Object.defineProperty(this,"_anchorElement",{value:o})},r=t.prototype,o=function(e){Object.defineProperty(r,e,{get:function(){return this._anchorElement[e]},set:function(n){this._anchorElement[e]=n},enumerable:!0})};["hash","host","hostname","port","protocol","search"].forEach(function(e){o(e)}),Object.defineProperties(r,{toString:{get:function(){var e=this;return function(){return e.href}}},href:{get:function(){return this._anchorElement.href.replace(/\?$/,"")},set:function(e){this._anchorElement.href=e},enumerable:!0},pathname:{get:function(){return this._anchorElement.pathname.replace(/(^\/?)/,"/")},set:function(e){this._anchorElement.pathname=e},enumerable:!0},origin:{get:function(){return this._anchorElement.protocol+"//"+this._anchorElement.hostname+(this._anchorElement.port?":"+this._anchorElement.port:"")},enumerable:!0},password:{get:function(){return""},set:function(e){},enumerable:!0},username:{get:function(){return""},set:function(e){},enumerable:!0},searchParams:{get:function(){var e=new URLSearchParams(this.search),n=this;return["append","delete","set"].forEach(function(t){var r=e[t];e[t]=function(){r.apply(e,arguments),n.search=e.toString()}}),e},enumerable:!0}}),t.createObjectURL=function(e){return n.createObjectURL.apply(n,arguments)},t.revokeObjectURL=function(e){return n.revokeObjectURL.apply(n,arguments)},e.URL=t}(),void 0!==e.location&&!("origin"in e.location)){var n=function(){return e.location.protocol+"//"+e.location.hostname+(e.location.port?":"+e.location.port:"")};try{Object.defineProperty(e.location,"origin",{get:n,enumerable:!0})}catch(t){setInterval(function(){e.location.origin=n()},100)}}}(void 0!==e?e:"undefined"!=typeof window?window:"undefined"!=typeof self?self:this)}).call(n,t(2))},362:function(e,n,t){e.exports=t(176)}});
=======

var $ = window.$;

/**
 * Class is responsible for handling Grid events
 */

var Grid = function () {
  /**
   * Grid's selector
   *
   * @param {string} gridPanelSelector
   */
  function Grid(gridPanelSelector) {
    _classCallCheck(this, Grid);

    this.$gridPanel = $(gridPanelSelector);
    this.gridId = this.$gridPanel.data('grid-id');
    this.$grid = this.$gridPanel.find('#' + this.gridId + '_grid');
  }

  /**
   * Initialize grid events
   */


  _createClass(Grid, [{
    key: 'init',
    value: function init() {
      this._handleBulkActionSelectAllCheckbox();
      this._handleBulkActionCheckboxSelect();
      this._handleCommonGridActions();
      this._handleBulkActionsSubmit();
    }

    /**
     * Handles most common grid actions (show sql, refresh list & etc.)
     *
     * @private
     */

  }, {
    key: '_handleCommonGridActions',
    value: function _handleCommonGridActions() {
      var _this = this;

      var commonActionSuffix = '#' + this.gridId + '_grid_action_';

      var refreshListActionId = commonActionSuffix + 'common_refresh_list';
      var showSqlActionId = commonActionSuffix + 'common_show_query';
      var exportSqlManagerActionId = commonActionSuffix + 'common_export_sql_manager';

      this.$gridPanel.on('click', refreshListActionId, function () {
        return _this._onRefreshClick();
      });
      this.$gridPanel.on('click', showSqlActionId, function () {
        return _this._onShowSqlQueryClick();
      });
      this.$gridPanel.on('click', exportSqlManagerActionId, function () {
        return _this._onExportSqlManagerClick();
      });
    }

    /**
     * Handles "Select all" button in the grid
     *
     * @private
     */

  }, {
    key: '_handleBulkActionSelectAllCheckbox',
    value: function _handleBulkActionSelectAllCheckbox() {
      var _this2 = this;

      $(document).on('change', '.js-bulk-action-select-all', function (e) {
        var $checkbox = $(e.target);

        var isChecked = $checkbox.is(':checked');
        if (isChecked) {
          _this2._enableBulkActionsBtn();
        } else {
          _this2._disableBulkActionsBtn();
        }

        _this2.$gridPanel.find('.js-bulk-action-checkbox').prop('checked', isChecked);
      });
    }

    /**
     * Handles each bulk action checkbox select in the grid
     *
     * @private
     */

  }, {
    key: '_handleBulkActionCheckboxSelect',
    value: function _handleBulkActionCheckboxSelect() {
      var _this3 = this;

      this.$gridPanel.on('change', '.js-bulk-action-checkbox', function () {
        var checkedRowsCount = _this3.$gridPanel.find('.js-bulk-action-checkbox:checked').length;

        if (checkedRowsCount > 0) {
          _this3._enableBulkActionsBtn();
        } else {
          _this3._disableBulkActionsBtn();
        }
      });
    }

    /**
     * Handles bulk action submit
     *
     * @private
     */

  }, {
    key: '_handleBulkActionsSubmit',
    value: function _handleBulkActionsSubmit() {
      var _this4 = this;

      this.$gridPanel.on('click', '.js-bulk-action-btn', function (e) {
        var $button = $(e.target);

        var confirmationMessage = $button.data('confirm-message').toString();

        if (confirmationMessage) {
          var confirmed = confirm(confirmationMessage);
          if (!confirmed) {
            return;
          }
        }

        var formUrl = $button.data('form-url');
        var formMethod = $button.data('form-method');

        var $form = _this4.$gridPanel.find('#' + _this4.gridId + '_grid_form');
        $form.attr('action', formUrl);
        $form.attr('method', formMethod);

        $form.submit();
      });
    }

    /**
     * Enable bulk actions button
     *
     * @private
     */

  }, {
    key: '_enableBulkActionsBtn',
    value: function _enableBulkActionsBtn() {
      this.$gridPanel.find('.js-bulk-actions-btn').prop('disabled', false);
    }

    /**
     * Disable bulk actions button
     *
     * @private
     */

  }, {
    key: '_disableBulkActionsBtn',
    value: function _disableBulkActionsBtn() {
      this.$gridPanel.find('.js-bulk-actions-btn').prop('disabled', true);
    }

    /**
     * Invoked when clicking on the "reload" toolbar button
     *
     * @private
     */

  }, {
    key: '_onRefreshClick',
    value: function _onRefreshClick() {
      location.reload();
    }

    /**
     * Invoked when clicking on the "show sql query" toolbar button
     *
     * @private
     */

  }, {
    key: '_onShowSqlQueryClick',
    value: function _onShowSqlQueryClick() {
      var identifier = this.$gridPanel.find('.js-grid').attr('id');
      var query = this.$gridPanel.find('.js-grid-table').data('query');

      var $sqlManagerForm = $('#' + identifier + '_common_show_query_modal_form');
      $sqlManagerForm.find('textarea[name="sql"]').val(query);

      var $modal = $('#' + identifier + '_common_show_query_modal');
      $modal.modal('show');

      $modal.on('click', '.btn-sql-submit', function () {
        return $sqlManagerForm.submit();
      });
    }

    /**
     * Invoked when clicking on the "export to the sql query" toolbar button
     *
     * @private
     */

  }, {
    key: '_onExportSqlManagerClick',
    value: function _onExportSqlManagerClick() {
      var query = this.$gridPanel.find('.js-grid-table').data('query');

      var $sqlManagerForm = $('#' + this.gridId + '_common_show_query_modal_form');
      $sqlManagerForm.find('textarea[name="sql"]').val(query);
      $sqlManagerForm.submit();
    }
  }]);

  return Grid;
}();

/* harmony default export */ __webpack_exports__["a"] = (Grid);

/***/ }),

/***/ 449:
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(220);


/***/ })

/******/ });
>>>>>>> 0c9e0a681f... rebuild assets
