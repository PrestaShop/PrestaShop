/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId])
/******/ 			return installedModules[moduleId].exports;
/******/
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			exports: {},
/******/ 			id: moduleId,
/******/ 			loaded: false
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.loaded = true;
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
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(0);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */
/***/ function(module, exports, __webpack_require__) {

	module.exports = __webpack_require__(1);


/***/ },
/* 1 */
/***/ function(module, exports, __webpack_require__) {

	/* expose jQuery for modules */
	'use strict';

	function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { 'default': obj }; }

	var _expose$ExposeJQueryJquery = __webpack_require__(2);

	var _expose$ExposeJQueryJquery2 = _interopRequireDefault(_expose$ExposeJQueryJquery);

	__webpack_require__(5);

	__webpack_require__(7);

	__webpack_require__(19);

	__webpack_require__(27);

	__webpack_require__(32);

	__webpack_require__(34);

	var _componentsDropDown = __webpack_require__(35);

	var _componentsDropDown2 = _interopRequireDefault(_componentsDropDown);

	var _componentsTopMenu = __webpack_require__(36);

	var _componentsTopMenu2 = _interopRequireDefault(_componentsTopMenu);

	var _componentsProductMiniature = __webpack_require__(37);

	var _componentsProductMiniature2 = _interopRequireDefault(_componentsProductMiniature);

	var _prestashop = __webpack_require__(31);

	var _prestashop2 = _interopRequireDefault(_prestashop);

	var _events = __webpack_require__(38);

	var _events2 = _interopRequireDefault(_events);

	var _common = __webpack_require__(33);

	// "inherit" EventEmitter
	for (var i in _events2['default'].prototype) {
	  _prestashop2['default'][i] = _events2['default'].prototype[i];
	}

	(0, _expose$ExposeJQueryJquery2['default'])(document).ready(function () {
	  var dropDownEl = (0, _expose$ExposeJQueryJquery2['default'])('.js-dropdown');
	  var topMenuEl = (0, _expose$ExposeJQueryJquery2['default'])('.js-top-menu ul');
	  var dropDown = new _componentsDropDown2['default'](dropDownEl).init();
	  var topMenu = new _componentsTopMenu2['default'](topMenuEl).init();
	  var productMinitature = new _componentsProductMiniature2['default']().init();
	  (0, _common.psShowHide)();
	});

/***/ },
/* 2 */
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function(global) {module.exports = global["$"] = __webpack_require__(3);
	/* WEBPACK VAR INJECTION */}.call(exports, (function() { return this; }())))

/***/ },
/* 3 */
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function(global) {module.exports = global["jQuery"] = __webpack_require__(4);
	/* WEBPACK VAR INJECTION */}.call(exports, (function() { return this; }())))

/***/ },
/* 4 */
/***/ function(module, exports, __webpack_require__) {

	var __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/*!
	 * jQuery JavaScript Library v2.2.0
	 * http://jquery.com/
	 *
	 * Includes Sizzle.js
	 * http://sizzlejs.com/
	 *
	 * Copyright jQuery Foundation and other contributors
	 * Released under the MIT license
	 * http://jquery.org/license
	 *
	 * Date: 2016-01-08T20:02Z
	 */"use strict";(function(global,factory){if(typeof module === "object" && typeof module.exports === "object"){ // For CommonJS and CommonJS-like environments where a proper `window`
	// is present, execute the factory and get jQuery.
	// For environments that do not have a `window` with a `document`
	// (such as Node.js), expose a factory as module.exports.
	// This accentuates the need for the creation of a real `window`.
	// e.g. var jQuery = require("jquery")(window);
	// See ticket #14549 for more info.
	module.exports = global.document?factory(global,true):function(w){if(!w.document){throw new Error("jQuery requires a window with a document");}return factory(w);};}else {factory(global);} // Pass this if window is not defined yet
	})(typeof window !== "undefined"?window:undefined,function(window,noGlobal){ // Support: Firefox 18+
	// Can't be in strict mode, several libs including ASP.NET trace
	// the stack via arguments.caller.callee and Firefox dies if
	// you try to trace through "use strict" call chains. (#13335)
	//"use strict";
	var arr=[];var document=window.document;var _slice=arr.slice;var concat=arr.concat;var push=arr.push;var indexOf=arr.indexOf;var class2type={};var toString=class2type.toString;var hasOwn=class2type.hasOwnProperty;var support={};var version="2.2.0", // Define a local copy of jQuery
	jQuery=function jQuery(selector,context){ // The jQuery object is actually just the init constructor 'enhanced'
	// Need init if jQuery is called (just allow error to be thrown if not included)
	return new jQuery.fn.init(selector,context);}, // Support: Android<4.1
	// Make sure we trim BOM and NBSP
	rtrim=/^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g, // Matches dashed string for camelizing
	rmsPrefix=/^-ms-/,rdashAlpha=/-([\da-z])/gi, // Used by jQuery.camelCase as callback to replace()
	fcamelCase=function fcamelCase(all,letter){return letter.toUpperCase();};jQuery.fn = jQuery.prototype = { // The current version of jQuery being used
	jquery:version,constructor:jQuery, // Start with an empty selector
	selector:"", // The default length of a jQuery object is 0
	length:0,toArray:function toArray(){return _slice.call(this);}, // Get the Nth element in the matched element set OR
	// Get the whole matched element set as a clean array
	get:function get(num){return num != null? // Return just the one element from the set
	num < 0?this[num + this.length]:this[num]: // Return all the elements in a clean array
	_slice.call(this);}, // Take an array of elements and push it onto the stack
	// (returning the new matched element set)
	pushStack:function pushStack(elems){ // Build a new jQuery matched element set
	var ret=jQuery.merge(this.constructor(),elems); // Add the old object onto the stack (as a reference)
	ret.prevObject = this;ret.context = this.context; // Return the newly-formed element set
	return ret;}, // Execute a callback for every element in the matched set.
	each:function each(callback){return jQuery.each(this,callback);},map:function map(callback){return this.pushStack(jQuery.map(this,function(elem,i){return callback.call(elem,i,elem);}));},slice:function slice(){return this.pushStack(_slice.apply(this,arguments));},first:function first(){return this.eq(0);},last:function last(){return this.eq(-1);},eq:function eq(i){var len=this.length,j=+i + (i < 0?len:0);return this.pushStack(j >= 0 && j < len?[this[j]]:[]);},end:function end(){return this.prevObject || this.constructor();}, // For internal use only.
	// Behaves like an Array's method, not like a jQuery method.
	push:push,sort:arr.sort,splice:arr.splice};jQuery.extend = jQuery.fn.extend = function(){var options,name,src,copy,copyIsArray,clone,target=arguments[0] || {},i=1,length=arguments.length,deep=false; // Handle a deep copy situation
	if(typeof target === "boolean"){deep = target; // Skip the boolean and the target
	target = arguments[i] || {};i++;} // Handle case when target is a string or something (possible in deep copy)
	if(typeof target !== "object" && !jQuery.isFunction(target)){target = {};} // Extend jQuery itself if only one argument is passed
	if(i === length){target = this;i--;}for(;i < length;i++) { // Only deal with non-null/undefined values
	if((options = arguments[i]) != null){ // Extend the base object
	for(name in options) {src = target[name];copy = options[name]; // Prevent never-ending loop
	if(target === copy){continue;} // Recurse if we're merging plain objects or arrays
	if(deep && copy && (jQuery.isPlainObject(copy) || (copyIsArray = jQuery.isArray(copy)))){if(copyIsArray){copyIsArray = false;clone = src && jQuery.isArray(src)?src:[];}else {clone = src && jQuery.isPlainObject(src)?src:{};} // Never move original objects, clone them
	target[name] = jQuery.extend(deep,clone,copy); // Don't bring in undefined values
	}else if(copy !== undefined){target[name] = copy;}}}} // Return the modified object
	return target;};jQuery.extend({ // Unique for each copy of jQuery on the page
	expando:"jQuery" + (version + Math.random()).replace(/\D/g,""), // Assume jQuery is ready without the ready module
	isReady:true,error:function error(msg){throw new Error(msg);},noop:function noop(){},isFunction:function isFunction(obj){return jQuery.type(obj) === "function";},isArray:Array.isArray,isWindow:function isWindow(obj){return obj != null && obj === obj.window;},isNumeric:function isNumeric(obj){ // parseFloat NaNs numeric-cast false positives (null|true|false|"")
	// ...but misinterprets leading-number strings, particularly hex literals ("0x...")
	// subtraction forces infinities to NaN
	// adding 1 corrects loss of precision from parseFloat (#15100)
	var realStringObj=obj && obj.toString();return !jQuery.isArray(obj) && realStringObj - parseFloat(realStringObj) + 1 >= 0;},isPlainObject:function isPlainObject(obj){ // Not plain objects:
	// - Any object or value whose internal [[Class]] property is not "[object Object]"
	// - DOM nodes
	// - window
	if(jQuery.type(obj) !== "object" || obj.nodeType || jQuery.isWindow(obj)){return false;}if(obj.constructor && !hasOwn.call(obj.constructor.prototype,"isPrototypeOf")){return false;} // If the function hasn't returned already, we're confident that
	// |obj| is a plain object, created by {} or constructed with new Object
	return true;},isEmptyObject:function isEmptyObject(obj){var name;for(name in obj) {return false;}return true;},type:function type(obj){if(obj == null){return obj + "";} // Support: Android<4.0, iOS<6 (functionish RegExp)
	return typeof obj === "object" || typeof obj === "function"?class2type[toString.call(obj)] || "object":typeof obj;}, // Evaluates a script in a global context
	globalEval:function globalEval(code){var script,indirect=eval;code = jQuery.trim(code);if(code){ // If the code includes a valid, prologue position
	// strict mode pragma, execute code by injecting a
	// script tag into the document.
	if(code.indexOf("use strict") === 1){script = document.createElement("script");script.text = code;document.head.appendChild(script).parentNode.removeChild(script);}else { // Otherwise, avoid the DOM node creation, insertion
	// and removal by using an indirect global eval
	indirect(code);}}}, // Convert dashed to camelCase; used by the css and data modules
	// Support: IE9-11+
	// Microsoft forgot to hump their vendor prefix (#9572)
	camelCase:function camelCase(string){return string.replace(rmsPrefix,"ms-").replace(rdashAlpha,fcamelCase);},nodeName:function nodeName(elem,name){return elem.nodeName && elem.nodeName.toLowerCase() === name.toLowerCase();},each:function each(obj,callback){var length,i=0;if(isArrayLike(obj)){length = obj.length;for(;i < length;i++) {if(callback.call(obj[i],i,obj[i]) === false){break;}}}else {for(i in obj) {if(callback.call(obj[i],i,obj[i]) === false){break;}}}return obj;}, // Support: Android<4.1
	trim:function trim(text){return text == null?"":(text + "").replace(rtrim,"");}, // results is for internal usage only
	makeArray:function makeArray(arr,results){var ret=results || [];if(arr != null){if(isArrayLike(Object(arr))){jQuery.merge(ret,typeof arr === "string"?[arr]:arr);}else {push.call(ret,arr);}}return ret;},inArray:function inArray(elem,arr,i){return arr == null?-1:indexOf.call(arr,elem,i);},merge:function merge(first,second){var len=+second.length,j=0,i=first.length;for(;j < len;j++) {first[i++] = second[j];}first.length = i;return first;},grep:function grep(elems,callback,invert){var callbackInverse,matches=[],i=0,length=elems.length,callbackExpect=!invert; // Go through the array, only saving the items
	// that pass the validator function
	for(;i < length;i++) {callbackInverse = !callback(elems[i],i);if(callbackInverse !== callbackExpect){matches.push(elems[i]);}}return matches;}, // arg is for internal usage only
	map:function map(elems,callback,arg){var length,value,i=0,ret=[]; // Go through the array, translating each of the items to their new values
	if(isArrayLike(elems)){length = elems.length;for(;i < length;i++) {value = callback(elems[i],i,arg);if(value != null){ret.push(value);}} // Go through every key on the object,
	}else {for(i in elems) {value = callback(elems[i],i,arg);if(value != null){ret.push(value);}}} // Flatten any nested arrays
	return concat.apply([],ret);}, // A global GUID counter for objects
	guid:1, // Bind a function to a context, optionally partially applying any
	// arguments.
	proxy:function proxy(fn,context){var tmp,args,proxy;if(typeof context === "string"){tmp = fn[context];context = fn;fn = tmp;} // Quick check to determine if target is callable, in the spec
	// this throws a TypeError, but we will just return undefined.
	if(!jQuery.isFunction(fn)){return undefined;} // Simulated bind
	args = _slice.call(arguments,2);proxy = function(){return fn.apply(context || this,args.concat(_slice.call(arguments)));}; // Set the guid of unique handler to the same of original handler, so it can be removed
	proxy.guid = fn.guid = fn.guid || jQuery.guid++;return proxy;},now:Date.now, // jQuery.support is not used in Core but other projects attach their
	// properties to it so it needs to exist.
	support:support}); // JSHint would error on this code due to the Symbol not being defined in ES5.
	// Defining this global in .jshintrc would create a danger of using the global
	// unguarded in another place, it seems safer to just disable JSHint for these
	// three lines.
	/* jshint ignore: start */if(typeof Symbol === "function"){jQuery.fn[Symbol.iterator] = arr[Symbol.iterator];} /* jshint ignore: end */ // Populate the class2type map
	jQuery.each("Boolean Number String Function Array Date RegExp Object Error Symbol".split(" "),function(i,name){class2type["[object " + name + "]"] = name.toLowerCase();});function isArrayLike(obj){ // Support: iOS 8.2 (not reproducible in simulator)
	// `in` check used to prevent JIT error (gh-2145)
	// hasOwn isn't used here due to false negatives
	// regarding Nodelist length in IE
	var length=!!obj && "length" in obj && obj.length,type=jQuery.type(obj);if(type === "function" || jQuery.isWindow(obj)){return false;}return type === "array" || length === 0 || typeof length === "number" && length > 0 && length - 1 in obj;}var Sizzle= /*!
	 * Sizzle CSS Selector Engine v2.2.1
	 * http://sizzlejs.com/
	 *
	 * Copyright jQuery Foundation and other contributors
	 * Released under the MIT license
	 * http://jquery.org/license
	 *
	 * Date: 2015-10-17
	 */(function(window){var i,support,Expr,getText,isXML,tokenize,compile,select,outermostContext,sortInput,hasDuplicate, // Local document vars
	setDocument,document,docElem,documentIsHTML,rbuggyQSA,rbuggyMatches,matches,contains, // Instance-specific data
	expando="sizzle" + 1 * new Date(),preferredDoc=window.document,dirruns=0,done=0,classCache=createCache(),tokenCache=createCache(),compilerCache=createCache(),sortOrder=function sortOrder(a,b){if(a === b){hasDuplicate = true;}return 0;}, // General-purpose constants
	MAX_NEGATIVE=1 << 31, // Instance methods
	hasOwn=({}).hasOwnProperty,arr=[],pop=arr.pop,push_native=arr.push,push=arr.push,slice=arr.slice, // Use a stripped-down indexOf as it's faster than native
	// http://jsperf.com/thor-indexof-vs-for/5
	indexOf=function indexOf(list,elem){var i=0,len=list.length;for(;i < len;i++) {if(list[i] === elem){return i;}}return -1;},booleans="checked|selected|async|autofocus|autoplay|controls|defer|disabled|hidden|ismap|loop|multiple|open|readonly|required|scoped", // Regular expressions
	// http://www.w3.org/TR/css3-selectors/#whitespace
	whitespace="[\\x20\\t\\r\\n\\f]", // http://www.w3.org/TR/CSS21/syndata.html#value-def-identifier
	identifier="(?:\\\\.|[\\w-]|[^\\x00-\\xa0])+", // Attribute selectors: http://www.w3.org/TR/selectors/#attribute-selectors
	attributes="\\[" + whitespace + "*(" + identifier + ")(?:" + whitespace +  // Operator (capture 2)
	"*([*^$|!~]?=)" + whitespace +  // "Attribute values must be CSS identifiers [capture 5] or strings [capture 3 or capture 4]"
	"*(?:'((?:\\\\.|[^\\\\'])*)'|\"((?:\\\\.|[^\\\\\"])*)\"|(" + identifier + "))|)" + whitespace + "*\\]",pseudos=":(" + identifier + ")(?:\\((" +  // To reduce the number of selectors needing tokenize in the preFilter, prefer arguments:
	// 1. quoted (capture 3; capture 4 or capture 5)
	"('((?:\\\\.|[^\\\\'])*)'|\"((?:\\\\.|[^\\\\\"])*)\")|" +  // 2. simple (capture 6)
	"((?:\\\\.|[^\\\\()[\\]]|" + attributes + ")*)|" +  // 3. anything else (capture 2)
	".*" + ")\\)|)", // Leading and non-escaped trailing whitespace, capturing some non-whitespace characters preceding the latter
	rwhitespace=new RegExp(whitespace + "+","g"),rtrim=new RegExp("^" + whitespace + "+|((?:^|[^\\\\])(?:\\\\.)*)" + whitespace + "+$","g"),rcomma=new RegExp("^" + whitespace + "*," + whitespace + "*"),rcombinators=new RegExp("^" + whitespace + "*([>+~]|" + whitespace + ")" + whitespace + "*"),rattributeQuotes=new RegExp("=" + whitespace + "*([^\\]'\"]*?)" + whitespace + "*\\]","g"),rpseudo=new RegExp(pseudos),ridentifier=new RegExp("^" + identifier + "$"),matchExpr={"ID":new RegExp("^#(" + identifier + ")"),"CLASS":new RegExp("^\\.(" + identifier + ")"),"TAG":new RegExp("^(" + identifier + "|[*])"),"ATTR":new RegExp("^" + attributes),"PSEUDO":new RegExp("^" + pseudos),"CHILD":new RegExp("^:(only|first|last|nth|nth-last)-(child|of-type)(?:\\(" + whitespace + "*(even|odd|(([+-]|)(\\d*)n|)" + whitespace + "*(?:([+-]|)" + whitespace + "*(\\d+)|))" + whitespace + "*\\)|)","i"),"bool":new RegExp("^(?:" + booleans + ")$","i"), // For use in libraries implementing .is()
	// We use this for POS matching in `select`
	"needsContext":new RegExp("^" + whitespace + "*[>+~]|:(even|odd|eq|gt|lt|nth|first|last)(?:\\(" + whitespace + "*((?:-\\d)?\\d*)" + whitespace + "*\\)|)(?=[^-]|$)","i")},rinputs=/^(?:input|select|textarea|button)$/i,rheader=/^h\d$/i,rnative=/^[^{]+\{\s*\[native \w/, // Easily-parseable/retrievable ID or TAG or CLASS selectors
	rquickExpr=/^(?:#([\w-]+)|(\w+)|\.([\w-]+))$/,rsibling=/[+~]/,rescape=/'|\\/g, // CSS escapes http://www.w3.org/TR/CSS21/syndata.html#escaped-characters
	runescape=new RegExp("\\\\([\\da-f]{1,6}" + whitespace + "?|(" + whitespace + ")|.)","ig"),funescape=function funescape(_,escaped,escapedWhitespace){var high="0x" + escaped - 0x10000; // NaN means non-codepoint
	// Support: Firefox<24
	// Workaround erroneous numeric interpretation of +"0x"
	return high !== high || escapedWhitespace?escaped:high < 0? // BMP codepoint
	String.fromCharCode(high + 0x10000): // Supplemental Plane codepoint (surrogate pair)
	String.fromCharCode(high >> 10 | 0xD800,high & 0x3FF | 0xDC00);}, // Used for iframes
	// See setDocument()
	// Removing the function wrapper causes a "Permission Denied"
	// error in IE
	unloadHandler=function unloadHandler(){setDocument();}; // Optimize for push.apply( _, NodeList )
	try{push.apply(arr = slice.call(preferredDoc.childNodes),preferredDoc.childNodes); // Support: Android<4.0
	// Detect silently failing push.apply
	arr[preferredDoc.childNodes.length].nodeType;}catch(e) {push = {apply:arr.length? // Leverage slice if possible
	function(target,els){push_native.apply(target,slice.call(els));}: // Support: IE<9
	function(target,els){var j=target.length,i=0; // Can't trust NodeList.length
	while(target[j++] = els[i++]) {}target.length = j - 1;}};}function Sizzle(selector,context,results,seed){var m,i,elem,nid,nidselect,match,groups,newSelector,newContext=context && context.ownerDocument, // nodeType defaults to 9, since context defaults to document
	nodeType=context?context.nodeType:9;results = results || []; // Return early from calls with invalid selector or context
	if(typeof selector !== "string" || !selector || nodeType !== 1 && nodeType !== 9 && nodeType !== 11){return results;} // Try to shortcut find operations (as opposed to filters) in HTML documents
	if(!seed){if((context?context.ownerDocument || context:preferredDoc) !== document){setDocument(context);}context = context || document;if(documentIsHTML){ // If the selector is sufficiently simple, try using a "get*By*" DOM method
	// (excepting DocumentFragment context, where the methods don't exist)
	if(nodeType !== 11 && (match = rquickExpr.exec(selector))){ // ID selector
	if(m = match[1]){ // Document context
	if(nodeType === 9){if(elem = context.getElementById(m)){ // Support: IE, Opera, Webkit
	// TODO: identify versions
	// getElementById can match elements by name instead of ID
	if(elem.id === m){results.push(elem);return results;}}else {return results;} // Element context
	}else { // Support: IE, Opera, Webkit
	// TODO: identify versions
	// getElementById can match elements by name instead of ID
	if(newContext && (elem = newContext.getElementById(m)) && contains(context,elem) && elem.id === m){results.push(elem);return results;}} // Type selector
	}else if(match[2]){push.apply(results,context.getElementsByTagName(selector));return results; // Class selector
	}else if((m = match[3]) && support.getElementsByClassName && context.getElementsByClassName){push.apply(results,context.getElementsByClassName(m));return results;}} // Take advantage of querySelectorAll
	if(support.qsa && !compilerCache[selector + " "] && (!rbuggyQSA || !rbuggyQSA.test(selector))){if(nodeType !== 1){newContext = context;newSelector = selector; // qSA looks outside Element context, which is not what we want
	// Thanks to Andrew Dupont for this workaround technique
	// Support: IE <=8
	// Exclude object elements
	}else if(context.nodeName.toLowerCase() !== "object"){ // Capture the context ID, setting it first if necessary
	if(nid = context.getAttribute("id")){nid = nid.replace(rescape,"\\$&");}else {context.setAttribute("id",nid = expando);} // Prefix every selector in the list
	groups = tokenize(selector);i = groups.length;nidselect = ridentifier.test(nid)?"#" + nid:"[id='" + nid + "']";while(i--) {groups[i] = nidselect + " " + toSelector(groups[i]);}newSelector = groups.join(","); // Expand context for sibling selectors
	newContext = rsibling.test(selector) && testContext(context.parentNode) || context;}if(newSelector){try{push.apply(results,newContext.querySelectorAll(newSelector));return results;}catch(qsaError) {}finally {if(nid === expando){context.removeAttribute("id");}}}}}} // All others
	return select(selector.replace(rtrim,"$1"),context,results,seed);} /**
	 * Create key-value caches of limited size
	 * @returns {function(string, object)} Returns the Object data after storing it on itself with
	 *	property name the (space-suffixed) string and (if the cache is larger than Expr.cacheLength)
	 *	deleting the oldest entry
	 */function createCache(){var keys=[];function cache(key,value){ // Use (key + " ") to avoid collision with native prototype properties (see Issue #157)
	if(keys.push(key + " ") > Expr.cacheLength){ // Only keep the most recent entries
	delete cache[keys.shift()];}return cache[key + " "] = value;}return cache;} /**
	 * Mark a function for special use by Sizzle
	 * @param {Function} fn The function to mark
	 */function markFunction(fn){fn[expando] = true;return fn;} /**
	 * Support testing using an element
	 * @param {Function} fn Passed the created div and expects a boolean result
	 */function assert(fn){var div=document.createElement("div");try{return !!fn(div);}catch(e) {return false;}finally { // Remove from its parent by default
	if(div.parentNode){div.parentNode.removeChild(div);} // release memory in IE
	div = null;}} /**
	 * Adds the same handler for all of the specified attrs
	 * @param {String} attrs Pipe-separated list of attributes
	 * @param {Function} handler The method that will be applied
	 */function addHandle(attrs,handler){var arr=attrs.split("|"),i=arr.length;while(i--) {Expr.attrHandle[arr[i]] = handler;}} /**
	 * Checks document order of two siblings
	 * @param {Element} a
	 * @param {Element} b
	 * @returns {Number} Returns less than 0 if a precedes b, greater than 0 if a follows b
	 */function siblingCheck(a,b){var cur=b && a,diff=cur && a.nodeType === 1 && b.nodeType === 1 && (~b.sourceIndex || MAX_NEGATIVE) - (~a.sourceIndex || MAX_NEGATIVE); // Use IE sourceIndex if available on both nodes
	if(diff){return diff;} // Check if b follows a
	if(cur){while(cur = cur.nextSibling) {if(cur === b){return -1;}}}return a?1:-1;} /**
	 * Returns a function to use in pseudos for input types
	 * @param {String} type
	 */function createInputPseudo(type){return function(elem){var name=elem.nodeName.toLowerCase();return name === "input" && elem.type === type;};} /**
	 * Returns a function to use in pseudos for buttons
	 * @param {String} type
	 */function createButtonPseudo(type){return function(elem){var name=elem.nodeName.toLowerCase();return (name === "input" || name === "button") && elem.type === type;};} /**
	 * Returns a function to use in pseudos for positionals
	 * @param {Function} fn
	 */function createPositionalPseudo(fn){return markFunction(function(argument){argument = +argument;return markFunction(function(seed,matches){var j,matchIndexes=fn([],seed.length,argument),i=matchIndexes.length; // Match elements found at the specified indexes
	while(i--) {if(seed[j = matchIndexes[i]]){seed[j] = !(matches[j] = seed[j]);}}});});} /**
	 * Checks a node for validity as a Sizzle context
	 * @param {Element|Object=} context
	 * @returns {Element|Object|Boolean} The input node if acceptable, otherwise a falsy value
	 */function testContext(context){return context && typeof context.getElementsByTagName !== "undefined" && context;} // Expose support vars for convenience
	support = Sizzle.support = {}; /**
	 * Detects XML nodes
	 * @param {Element|Object} elem An element or a document
	 * @returns {Boolean} True iff elem is a non-HTML XML node
	 */isXML = Sizzle.isXML = function(elem){ // documentElement is verified for cases where it doesn't yet exist
	// (such as loading iframes in IE - #4833)
	var documentElement=elem && (elem.ownerDocument || elem).documentElement;return documentElement?documentElement.nodeName !== "HTML":false;}; /**
	 * Sets document-related variables once based on the current document
	 * @param {Element|Object} [doc] An element or document object to use to set the document
	 * @returns {Object} Returns the current document
	 */setDocument = Sizzle.setDocument = function(node){var hasCompare,parent,doc=node?node.ownerDocument || node:preferredDoc; // Return early if doc is invalid or already selected
	if(doc === document || doc.nodeType !== 9 || !doc.documentElement){return document;} // Update global variables
	document = doc;docElem = document.documentElement;documentIsHTML = !isXML(document); // Support: IE 9-11, Edge
	// Accessing iframe documents after unload throws "permission denied" errors (jQuery #13936)
	if((parent = document.defaultView) && parent.top !== parent){ // Support: IE 11
	if(parent.addEventListener){parent.addEventListener("unload",unloadHandler,false); // Support: IE 9 - 10 only
	}else if(parent.attachEvent){parent.attachEvent("onunload",unloadHandler);}} /* Attributes
		---------------------------------------------------------------------- */ // Support: IE<8
	// Verify that getAttribute really returns attributes and not properties
	// (excepting IE8 booleans)
	support.attributes = assert(function(div){div.className = "i";return !div.getAttribute("className");}); /* getElement(s)By*
		---------------------------------------------------------------------- */ // Check if getElementsByTagName("*") returns only elements
	support.getElementsByTagName = assert(function(div){div.appendChild(document.createComment(""));return !div.getElementsByTagName("*").length;}); // Support: IE<9
	support.getElementsByClassName = rnative.test(document.getElementsByClassName); // Support: IE<10
	// Check if getElementById returns elements by name
	// The broken getElementById methods don't pick up programatically-set names,
	// so use a roundabout getElementsByName test
	support.getById = assert(function(div){docElem.appendChild(div).id = expando;return !document.getElementsByName || !document.getElementsByName(expando).length;}); // ID find and filter
	if(support.getById){Expr.find["ID"] = function(id,context){if(typeof context.getElementById !== "undefined" && documentIsHTML){var m=context.getElementById(id);return m?[m]:[];}};Expr.filter["ID"] = function(id){var attrId=id.replace(runescape,funescape);return function(elem){return elem.getAttribute("id") === attrId;};};}else { // Support: IE6/7
	// getElementById is not reliable as a find shortcut
	delete Expr.find["ID"];Expr.filter["ID"] = function(id){var attrId=id.replace(runescape,funescape);return function(elem){var node=typeof elem.getAttributeNode !== "undefined" && elem.getAttributeNode("id");return node && node.value === attrId;};};} // Tag
	Expr.find["TAG"] = support.getElementsByTagName?function(tag,context){if(typeof context.getElementsByTagName !== "undefined"){return context.getElementsByTagName(tag); // DocumentFragment nodes don't have gEBTN
	}else if(support.qsa){return context.querySelectorAll(tag);}}:function(tag,context){var elem,tmp=[],i=0, // By happy coincidence, a (broken) gEBTN appears on DocumentFragment nodes too
	results=context.getElementsByTagName(tag); // Filter out possible comments
	if(tag === "*"){while(elem = results[i++]) {if(elem.nodeType === 1){tmp.push(elem);}}return tmp;}return results;}; // Class
	Expr.find["CLASS"] = support.getElementsByClassName && function(className,context){if(typeof context.getElementsByClassName !== "undefined" && documentIsHTML){return context.getElementsByClassName(className);}}; /* QSA/matchesSelector
		---------------------------------------------------------------------- */ // QSA and matchesSelector support
	// matchesSelector(:active) reports false when true (IE9/Opera 11.5)
	rbuggyMatches = []; // qSa(:focus) reports false when true (Chrome 21)
	// We allow this because of a bug in IE8/9 that throws an error
	// whenever `document.activeElement` is accessed on an iframe
	// So, we allow :focus to pass through QSA all the time to avoid the IE error
	// See http://bugs.jquery.com/ticket/13378
	rbuggyQSA = [];if(support.qsa = rnative.test(document.querySelectorAll)){ // Build QSA regex
	// Regex strategy adopted from Diego Perini
	assert(function(div){ // Select is set to empty string on purpose
	// This is to test IE's treatment of not explicitly
	// setting a boolean content attribute,
	// since its presence should be enough
	// http://bugs.jquery.com/ticket/12359
	docElem.appendChild(div).innerHTML = "<a id='" + expando + "'></a>" + "<select id='" + expando + "-\r\\' msallowcapture=''>" + "<option selected=''></option></select>"; // Support: IE8, Opera 11-12.16
	// Nothing should be selected when empty strings follow ^= or $= or *=
	// The test attribute must be unknown in Opera but "safe" for WinRT
	// http://msdn.microsoft.com/en-us/library/ie/hh465388.aspx#attribute_section
	if(div.querySelectorAll("[msallowcapture^='']").length){rbuggyQSA.push("[*^$]=" + whitespace + "*(?:''|\"\")");} // Support: IE8
	// Boolean attributes and "value" are not treated correctly
	if(!div.querySelectorAll("[selected]").length){rbuggyQSA.push("\\[" + whitespace + "*(?:value|" + booleans + ")");} // Support: Chrome<29, Android<4.4, Safari<7.0+, iOS<7.0+, PhantomJS<1.9.8+
	if(!div.querySelectorAll("[id~=" + expando + "-]").length){rbuggyQSA.push("~=");} // Webkit/Opera - :checked should return selected option elements
	// http://www.w3.org/TR/2011/REC-css3-selectors-20110929/#checked
	// IE8 throws error here and will not see later tests
	if(!div.querySelectorAll(":checked").length){rbuggyQSA.push(":checked");} // Support: Safari 8+, iOS 8+
	// https://bugs.webkit.org/show_bug.cgi?id=136851
	// In-page `selector#id sibing-combinator selector` fails
	if(!div.querySelectorAll("a#" + expando + "+*").length){rbuggyQSA.push(".#.+[+~]");}});assert(function(div){ // Support: Windows 8 Native Apps
	// The type and name attributes are restricted during .innerHTML assignment
	var input=document.createElement("input");input.setAttribute("type","hidden");div.appendChild(input).setAttribute("name","D"); // Support: IE8
	// Enforce case-sensitivity of name attribute
	if(div.querySelectorAll("[name=d]").length){rbuggyQSA.push("name" + whitespace + "*[*^$|!~]?=");} // FF 3.5 - :enabled/:disabled and hidden elements (hidden elements are still enabled)
	// IE8 throws error here and will not see later tests
	if(!div.querySelectorAll(":enabled").length){rbuggyQSA.push(":enabled",":disabled");} // Opera 10-11 does not throw on post-comma invalid pseudos
	div.querySelectorAll("*,:x");rbuggyQSA.push(",.*:");});}if(support.matchesSelector = rnative.test(matches = docElem.matches || docElem.webkitMatchesSelector || docElem.mozMatchesSelector || docElem.oMatchesSelector || docElem.msMatchesSelector)){assert(function(div){ // Check to see if it's possible to do matchesSelector
	// on a disconnected node (IE 9)
	support.disconnectedMatch = matches.call(div,"div"); // This should fail with an exception
	// Gecko does not error, returns false instead
	matches.call(div,"[s!='']:x");rbuggyMatches.push("!=",pseudos);});}rbuggyQSA = rbuggyQSA.length && new RegExp(rbuggyQSA.join("|"));rbuggyMatches = rbuggyMatches.length && new RegExp(rbuggyMatches.join("|")); /* Contains
		---------------------------------------------------------------------- */hasCompare = rnative.test(docElem.compareDocumentPosition); // Element contains another
	// Purposefully self-exclusive
	// As in, an element does not contain itself
	contains = hasCompare || rnative.test(docElem.contains)?function(a,b){var adown=a.nodeType === 9?a.documentElement:a,bup=b && b.parentNode;return a === bup || !!(bup && bup.nodeType === 1 && (adown.contains?adown.contains(bup):a.compareDocumentPosition && a.compareDocumentPosition(bup) & 16));}:function(a,b){if(b){while(b = b.parentNode) {if(b === a){return true;}}}return false;}; /* Sorting
		---------------------------------------------------------------------- */ // Document order sorting
	sortOrder = hasCompare?function(a,b){ // Flag for duplicate removal
	if(a === b){hasDuplicate = true;return 0;} // Sort on method existence if only one input has compareDocumentPosition
	var compare=!a.compareDocumentPosition - !b.compareDocumentPosition;if(compare){return compare;} // Calculate position if both inputs belong to the same document
	compare = (a.ownerDocument || a) === (b.ownerDocument || b)?a.compareDocumentPosition(b): // Otherwise we know they are disconnected
	1; // Disconnected nodes
	if(compare & 1 || !support.sortDetached && b.compareDocumentPosition(a) === compare){ // Choose the first element that is related to our preferred document
	if(a === document || a.ownerDocument === preferredDoc && contains(preferredDoc,a)){return -1;}if(b === document || b.ownerDocument === preferredDoc && contains(preferredDoc,b)){return 1;} // Maintain original order
	return sortInput?indexOf(sortInput,a) - indexOf(sortInput,b):0;}return compare & 4?-1:1;}:function(a,b){ // Exit early if the nodes are identical
	if(a === b){hasDuplicate = true;return 0;}var cur,i=0,aup=a.parentNode,bup=b.parentNode,ap=[a],bp=[b]; // Parentless nodes are either documents or disconnected
	if(!aup || !bup){return a === document?-1:b === document?1:aup?-1:bup?1:sortInput?indexOf(sortInput,a) - indexOf(sortInput,b):0; // If the nodes are siblings, we can do a quick check
	}else if(aup === bup){return siblingCheck(a,b);} // Otherwise we need full lists of their ancestors for comparison
	cur = a;while(cur = cur.parentNode) {ap.unshift(cur);}cur = b;while(cur = cur.parentNode) {bp.unshift(cur);} // Walk down the tree looking for a discrepancy
	while(ap[i] === bp[i]) {i++;}return i? // Do a sibling check if the nodes have a common ancestor
	siblingCheck(ap[i],bp[i]): // Otherwise nodes in our document sort first
	ap[i] === preferredDoc?-1:bp[i] === preferredDoc?1:0;};return document;};Sizzle.matches = function(expr,elements){return Sizzle(expr,null,null,elements);};Sizzle.matchesSelector = function(elem,expr){ // Set document vars if needed
	if((elem.ownerDocument || elem) !== document){setDocument(elem);} // Make sure that attribute selectors are quoted
	expr = expr.replace(rattributeQuotes,"='$1']");if(support.matchesSelector && documentIsHTML && !compilerCache[expr + " "] && (!rbuggyMatches || !rbuggyMatches.test(expr)) && (!rbuggyQSA || !rbuggyQSA.test(expr))){try{var ret=matches.call(elem,expr); // IE 9's matchesSelector returns false on disconnected nodes
	if(ret || support.disconnectedMatch ||  // As well, disconnected nodes are said to be in a document
	// fragment in IE 9
	elem.document && elem.document.nodeType !== 11){return ret;}}catch(e) {}}return Sizzle(expr,document,null,[elem]).length > 0;};Sizzle.contains = function(context,elem){ // Set document vars if needed
	if((context.ownerDocument || context) !== document){setDocument(context);}return contains(context,elem);};Sizzle.attr = function(elem,name){ // Set document vars if needed
	if((elem.ownerDocument || elem) !== document){setDocument(elem);}var fn=Expr.attrHandle[name.toLowerCase()], // Don't get fooled by Object.prototype properties (jQuery #13807)
	val=fn && hasOwn.call(Expr.attrHandle,name.toLowerCase())?fn(elem,name,!documentIsHTML):undefined;return val !== undefined?val:support.attributes || !documentIsHTML?elem.getAttribute(name):(val = elem.getAttributeNode(name)) && val.specified?val.value:null;};Sizzle.error = function(msg){throw new Error("Syntax error, unrecognized expression: " + msg);}; /**
	 * Document sorting and removing duplicates
	 * @param {ArrayLike} results
	 */Sizzle.uniqueSort = function(results){var elem,duplicates=[],j=0,i=0; // Unless we *know* we can detect duplicates, assume their presence
	hasDuplicate = !support.detectDuplicates;sortInput = !support.sortStable && results.slice(0);results.sort(sortOrder);if(hasDuplicate){while(elem = results[i++]) {if(elem === results[i]){j = duplicates.push(i);}}while(j--) {results.splice(duplicates[j],1);}} // Clear input after sorting to release objects
	// See https://github.com/jquery/sizzle/pull/225
	sortInput = null;return results;}; /**
	 * Utility function for retrieving the text value of an array of DOM nodes
	 * @param {Array|Element} elem
	 */getText = Sizzle.getText = function(elem){var node,ret="",i=0,nodeType=elem.nodeType;if(!nodeType){ // If no nodeType, this is expected to be an array
	while(node = elem[i++]) { // Do not traverse comment nodes
	ret += getText(node);}}else if(nodeType === 1 || nodeType === 9 || nodeType === 11){ // Use textContent for elements
	// innerText usage removed for consistency of new lines (jQuery #11153)
	if(typeof elem.textContent === "string"){return elem.textContent;}else { // Traverse its children
	for(elem = elem.firstChild;elem;elem = elem.nextSibling) {ret += getText(elem);}}}else if(nodeType === 3 || nodeType === 4){return elem.nodeValue;} // Do not include comment or processing instruction nodes
	return ret;};Expr = Sizzle.selectors = { // Can be adjusted by the user
	cacheLength:50,createPseudo:markFunction,match:matchExpr,attrHandle:{},find:{},relative:{">":{dir:"parentNode",first:true}," ":{dir:"parentNode"},"+":{dir:"previousSibling",first:true},"~":{dir:"previousSibling"}},preFilter:{"ATTR":function ATTR(match){match[1] = match[1].replace(runescape,funescape); // Move the given value to match[3] whether quoted or unquoted
	match[3] = (match[3] || match[4] || match[5] || "").replace(runescape,funescape);if(match[2] === "~="){match[3] = " " + match[3] + " ";}return match.slice(0,4);},"CHILD":function CHILD(match){ /* matches from matchExpr["CHILD"]
					1 type (only|nth|...)
					2 what (child|of-type)
					3 argument (even|odd|\d*|\d*n([+-]\d+)?|...)
					4 xn-component of xn+y argument ([+-]?\d*n|)
					5 sign of xn-component
					6 x of xn-component
					7 sign of y-component
					8 y of y-component
				*/match[1] = match[1].toLowerCase();if(match[1].slice(0,3) === "nth"){ // nth-* requires argument
	if(!match[3]){Sizzle.error(match[0]);} // numeric x and y parameters for Expr.filter.CHILD
	// remember that false/true cast respectively to 0/1
	match[4] = +(match[4]?match[5] + (match[6] || 1):2 * (match[3] === "even" || match[3] === "odd"));match[5] = +(match[7] + match[8] || match[3] === "odd"); // other types prohibit arguments
	}else if(match[3]){Sizzle.error(match[0]);}return match;},"PSEUDO":function PSEUDO(match){var excess,unquoted=!match[6] && match[2];if(matchExpr["CHILD"].test(match[0])){return null;} // Accept quoted arguments as-is
	if(match[3]){match[2] = match[4] || match[5] || ""; // Strip excess characters from unquoted arguments
	}else if(unquoted && rpseudo.test(unquoted) && ( // Get excess from tokenize (recursively)
	excess = tokenize(unquoted,true)) && ( // advance to the next closing parenthesis
	excess = unquoted.indexOf(")",unquoted.length - excess) - unquoted.length)){ // excess is a negative index
	match[0] = match[0].slice(0,excess);match[2] = unquoted.slice(0,excess);} // Return only captures needed by the pseudo filter method (type and argument)
	return match.slice(0,3);}},filter:{"TAG":function TAG(nodeNameSelector){var nodeName=nodeNameSelector.replace(runescape,funescape).toLowerCase();return nodeNameSelector === "*"?function(){return true;}:function(elem){return elem.nodeName && elem.nodeName.toLowerCase() === nodeName;};},"CLASS":function CLASS(className){var pattern=classCache[className + " "];return pattern || (pattern = new RegExp("(^|" + whitespace + ")" + className + "(" + whitespace + "|$)")) && classCache(className,function(elem){return pattern.test(typeof elem.className === "string" && elem.className || typeof elem.getAttribute !== "undefined" && elem.getAttribute("class") || "");});},"ATTR":function ATTR(name,operator,check){return function(elem){var result=Sizzle.attr(elem,name);if(result == null){return operator === "!=";}if(!operator){return true;}result += "";return operator === "="?result === check:operator === "!="?result !== check:operator === "^="?check && result.indexOf(check) === 0:operator === "*="?check && result.indexOf(check) > -1:operator === "$="?check && result.slice(-check.length) === check:operator === "~="?(" " + result.replace(rwhitespace," ") + " ").indexOf(check) > -1:operator === "|="?result === check || result.slice(0,check.length + 1) === check + "-":false;};},"CHILD":function CHILD(type,what,argument,first,last){var simple=type.slice(0,3) !== "nth",forward=type.slice(-4) !== "last",ofType=what === "of-type";return first === 1 && last === 0? // Shortcut for :nth-*(n)
	function(elem){return !!elem.parentNode;}:function(elem,context,xml){var cache,uniqueCache,outerCache,node,nodeIndex,start,dir=simple !== forward?"nextSibling":"previousSibling",parent=elem.parentNode,name=ofType && elem.nodeName.toLowerCase(),useCache=!xml && !ofType,diff=false;if(parent){ // :(first|last|only)-(child|of-type)
	if(simple){while(dir) {node = elem;while(node = node[dir]) {if(ofType?node.nodeName.toLowerCase() === name:node.nodeType === 1){return false;}} // Reverse direction for :only-* (if we haven't yet done so)
	start = dir = type === "only" && !start && "nextSibling";}return true;}start = [forward?parent.firstChild:parent.lastChild]; // non-xml :nth-child(...) stores cache data on `parent`
	if(forward && useCache){ // Seek `elem` from a previously-cached index
	// ...in a gzip-friendly way
	node = parent;outerCache = node[expando] || (node[expando] = {}); // Support: IE <9 only
	// Defend against cloned attroperties (jQuery gh-1709)
	uniqueCache = outerCache[node.uniqueID] || (outerCache[node.uniqueID] = {});cache = uniqueCache[type] || [];nodeIndex = cache[0] === dirruns && cache[1];diff = nodeIndex && cache[2];node = nodeIndex && parent.childNodes[nodeIndex];while(node = ++nodeIndex && node && node[dir] || ( // Fallback to seeking `elem` from the start
	diff = nodeIndex = 0) || start.pop()) { // When found, cache indexes on `parent` and break
	if(node.nodeType === 1 && ++diff && node === elem){uniqueCache[type] = [dirruns,nodeIndex,diff];break;}}}else { // Use previously-cached element index if available
	if(useCache){ // ...in a gzip-friendly way
	node = elem;outerCache = node[expando] || (node[expando] = {}); // Support: IE <9 only
	// Defend against cloned attroperties (jQuery gh-1709)
	uniqueCache = outerCache[node.uniqueID] || (outerCache[node.uniqueID] = {});cache = uniqueCache[type] || [];nodeIndex = cache[0] === dirruns && cache[1];diff = nodeIndex;} // xml :nth-child(...)
	// or :nth-last-child(...) or :nth(-last)?-of-type(...)
	if(diff === false){ // Use the same loop as above to seek `elem` from the start
	while(node = ++nodeIndex && node && node[dir] || (diff = nodeIndex = 0) || start.pop()) {if((ofType?node.nodeName.toLowerCase() === name:node.nodeType === 1) && ++diff){ // Cache the index of each encountered element
	if(useCache){outerCache = node[expando] || (node[expando] = {}); // Support: IE <9 only
	// Defend against cloned attroperties (jQuery gh-1709)
	uniqueCache = outerCache[node.uniqueID] || (outerCache[node.uniqueID] = {});uniqueCache[type] = [dirruns,diff];}if(node === elem){break;}}}}} // Incorporate the offset, then check against cycle size
	diff -= last;return diff === first || diff % first === 0 && diff / first >= 0;}};},"PSEUDO":function PSEUDO(pseudo,argument){ // pseudo-class names are case-insensitive
	// http://www.w3.org/TR/selectors/#pseudo-classes
	// Prioritize by case sensitivity in case custom pseudos are added with uppercase letters
	// Remember that setFilters inherits from pseudos
	var args,fn=Expr.pseudos[pseudo] || Expr.setFilters[pseudo.toLowerCase()] || Sizzle.error("unsupported pseudo: " + pseudo); // The user may use createPseudo to indicate that
	// arguments are needed to create the filter function
	// just as Sizzle does
	if(fn[expando]){return fn(argument);} // But maintain support for old signatures
	if(fn.length > 1){args = [pseudo,pseudo,"",argument];return Expr.setFilters.hasOwnProperty(pseudo.toLowerCase())?markFunction(function(seed,matches){var idx,matched=fn(seed,argument),i=matched.length;while(i--) {idx = indexOf(seed,matched[i]);seed[idx] = !(matches[idx] = matched[i]);}}):function(elem){return fn(elem,0,args);};}return fn;}},pseudos:{ // Potentially complex pseudos
	"not":markFunction(function(selector){ // Trim the selector passed to compile
	// to avoid treating leading and trailing
	// spaces as combinators
	var input=[],results=[],matcher=compile(selector.replace(rtrim,"$1"));return matcher[expando]?markFunction(function(seed,matches,context,xml){var elem,unmatched=matcher(seed,null,xml,[]),i=seed.length; // Match elements unmatched by `matcher`
	while(i--) {if(elem = unmatched[i]){seed[i] = !(matches[i] = elem);}}}):function(elem,context,xml){input[0] = elem;matcher(input,null,xml,results); // Don't keep the element (issue #299)
	input[0] = null;return !results.pop();};}),"has":markFunction(function(selector){return function(elem){return Sizzle(selector,elem).length > 0;};}),"contains":markFunction(function(text){text = text.replace(runescape,funescape);return function(elem){return (elem.textContent || elem.innerText || getText(elem)).indexOf(text) > -1;};}), // "Whether an element is represented by a :lang() selector
	// is based solely on the element's language value
	// being equal to the identifier C,
	// or beginning with the identifier C immediately followed by "-".
	// The matching of C against the element's language value is performed case-insensitively.
	// The identifier C does not have to be a valid language name."
	// http://www.w3.org/TR/selectors/#lang-pseudo
	"lang":markFunction(function(lang){ // lang value must be a valid identifier
	if(!ridentifier.test(lang || "")){Sizzle.error("unsupported lang: " + lang);}lang = lang.replace(runescape,funescape).toLowerCase();return function(elem){var elemLang;do {if(elemLang = documentIsHTML?elem.lang:elem.getAttribute("xml:lang") || elem.getAttribute("lang")){elemLang = elemLang.toLowerCase();return elemLang === lang || elemLang.indexOf(lang + "-") === 0;}}while((elem = elem.parentNode) && elem.nodeType === 1);return false;};}), // Miscellaneous
	"target":function target(elem){var hash=window.location && window.location.hash;return hash && hash.slice(1) === elem.id;},"root":function root(elem){return elem === docElem;},"focus":function focus(elem){return elem === document.activeElement && (!document.hasFocus || document.hasFocus()) && !!(elem.type || elem.href || ~elem.tabIndex);}, // Boolean properties
	"enabled":function enabled(elem){return elem.disabled === false;},"disabled":function disabled(elem){return elem.disabled === true;},"checked":function checked(elem){ // In CSS3, :checked should return both checked and selected elements
	// http://www.w3.org/TR/2011/REC-css3-selectors-20110929/#checked
	var nodeName=elem.nodeName.toLowerCase();return nodeName === "input" && !!elem.checked || nodeName === "option" && !!elem.selected;},"selected":function selected(elem){ // Accessing this property makes selected-by-default
	// options in Safari work properly
	if(elem.parentNode){elem.parentNode.selectedIndex;}return elem.selected === true;}, // Contents
	"empty":function empty(elem){ // http://www.w3.org/TR/selectors/#empty-pseudo
	// :empty is negated by element (1) or content nodes (text: 3; cdata: 4; entity ref: 5),
	//   but not by others (comment: 8; processing instruction: 7; etc.)
	// nodeType < 6 works because attributes (2) do not appear as children
	for(elem = elem.firstChild;elem;elem = elem.nextSibling) {if(elem.nodeType < 6){return false;}}return true;},"parent":function parent(elem){return !Expr.pseudos["empty"](elem);}, // Element/input types
	"header":function header(elem){return rheader.test(elem.nodeName);},"input":function input(elem){return rinputs.test(elem.nodeName);},"button":function button(elem){var name=elem.nodeName.toLowerCase();return name === "input" && elem.type === "button" || name === "button";},"text":function text(elem){var attr;return elem.nodeName.toLowerCase() === "input" && elem.type === "text" && ( // Support: IE<8
	// New HTML5 attribute values (e.g., "search") appear with elem.type === "text"
	(attr = elem.getAttribute("type")) == null || attr.toLowerCase() === "text");}, // Position-in-collection
	"first":createPositionalPseudo(function(){return [0];}),"last":createPositionalPseudo(function(matchIndexes,length){return [length - 1];}),"eq":createPositionalPseudo(function(matchIndexes,length,argument){return [argument < 0?argument + length:argument];}),"even":createPositionalPseudo(function(matchIndexes,length){var i=0;for(;i < length;i += 2) {matchIndexes.push(i);}return matchIndexes;}),"odd":createPositionalPseudo(function(matchIndexes,length){var i=1;for(;i < length;i += 2) {matchIndexes.push(i);}return matchIndexes;}),"lt":createPositionalPseudo(function(matchIndexes,length,argument){var i=argument < 0?argument + length:argument;for(;--i >= 0;) {matchIndexes.push(i);}return matchIndexes;}),"gt":createPositionalPseudo(function(matchIndexes,length,argument){var i=argument < 0?argument + length:argument;for(;++i < length;) {matchIndexes.push(i);}return matchIndexes;})}};Expr.pseudos["nth"] = Expr.pseudos["eq"]; // Add button/input type pseudos
	for(i in {radio:true,checkbox:true,file:true,password:true,image:true}) {Expr.pseudos[i] = createInputPseudo(i);}for(i in {submit:true,reset:true}) {Expr.pseudos[i] = createButtonPseudo(i);} // Easy API for creating new setFilters
	function setFilters(){}setFilters.prototype = Expr.filters = Expr.pseudos;Expr.setFilters = new setFilters();tokenize = Sizzle.tokenize = function(selector,parseOnly){var matched,match,tokens,type,soFar,groups,preFilters,cached=tokenCache[selector + " "];if(cached){return parseOnly?0:cached.slice(0);}soFar = selector;groups = [];preFilters = Expr.preFilter;while(soFar) { // Comma and first run
	if(!matched || (match = rcomma.exec(soFar))){if(match){ // Don't consume trailing commas as valid
	soFar = soFar.slice(match[0].length) || soFar;}groups.push(tokens = []);}matched = false; // Combinators
	if(match = rcombinators.exec(soFar)){matched = match.shift();tokens.push({value:matched, // Cast descendant combinators to space
	type:match[0].replace(rtrim," ")});soFar = soFar.slice(matched.length);} // Filters
	for(type in Expr.filter) {if((match = matchExpr[type].exec(soFar)) && (!preFilters[type] || (match = preFilters[type](match)))){matched = match.shift();tokens.push({value:matched,type:type,matches:match});soFar = soFar.slice(matched.length);}}if(!matched){break;}} // Return the length of the invalid excess
	// if we're just parsing
	// Otherwise, throw an error or return tokens
	return parseOnly?soFar.length:soFar?Sizzle.error(selector): // Cache the tokens
	tokenCache(selector,groups).slice(0);};function toSelector(tokens){var i=0,len=tokens.length,selector="";for(;i < len;i++) {selector += tokens[i].value;}return selector;}function addCombinator(matcher,combinator,base){var dir=combinator.dir,checkNonElements=base && dir === "parentNode",doneName=done++;return combinator.first? // Check against closest ancestor/preceding element
	function(elem,context,xml){while(elem = elem[dir]) {if(elem.nodeType === 1 || checkNonElements){return matcher(elem,context,xml);}}}: // Check against all ancestor/preceding elements
	function(elem,context,xml){var oldCache,uniqueCache,outerCache,newCache=[dirruns,doneName]; // We can't set arbitrary data on XML nodes, so they don't benefit from combinator caching
	if(xml){while(elem = elem[dir]) {if(elem.nodeType === 1 || checkNonElements){if(matcher(elem,context,xml)){return true;}}}}else {while(elem = elem[dir]) {if(elem.nodeType === 1 || checkNonElements){outerCache = elem[expando] || (elem[expando] = {}); // Support: IE <9 only
	// Defend against cloned attroperties (jQuery gh-1709)
	uniqueCache = outerCache[elem.uniqueID] || (outerCache[elem.uniqueID] = {});if((oldCache = uniqueCache[dir]) && oldCache[0] === dirruns && oldCache[1] === doneName){ // Assign to newCache so results back-propagate to previous elements
	return newCache[2] = oldCache[2];}else { // Reuse newcache so results back-propagate to previous elements
	uniqueCache[dir] = newCache; // A match means we're done; a fail means we have to keep checking
	if(newCache[2] = matcher(elem,context,xml)){return true;}}}}}};}function elementMatcher(matchers){return matchers.length > 1?function(elem,context,xml){var i=matchers.length;while(i--) {if(!matchers[i](elem,context,xml)){return false;}}return true;}:matchers[0];}function multipleContexts(selector,contexts,results){var i=0,len=contexts.length;for(;i < len;i++) {Sizzle(selector,contexts[i],results);}return results;}function condense(unmatched,map,filter,context,xml){var elem,newUnmatched=[],i=0,len=unmatched.length,mapped=map != null;for(;i < len;i++) {if(elem = unmatched[i]){if(!filter || filter(elem,context,xml)){newUnmatched.push(elem);if(mapped){map.push(i);}}}}return newUnmatched;}function setMatcher(preFilter,selector,matcher,postFilter,postFinder,postSelector){if(postFilter && !postFilter[expando]){postFilter = setMatcher(postFilter);}if(postFinder && !postFinder[expando]){postFinder = setMatcher(postFinder,postSelector);}return markFunction(function(seed,results,context,xml){var temp,i,elem,preMap=[],postMap=[],preexisting=results.length, // Get initial elements from seed or context
	elems=seed || multipleContexts(selector || "*",context.nodeType?[context]:context,[]), // Prefilter to get matcher input, preserving a map for seed-results synchronization
	matcherIn=preFilter && (seed || !selector)?condense(elems,preMap,preFilter,context,xml):elems,matcherOut=matcher? // If we have a postFinder, or filtered seed, or non-seed postFilter or preexisting results,
	postFinder || (seed?preFilter:preexisting || postFilter)? // ...intermediate processing is necessary
	[]: // ...otherwise use results directly
	results:matcherIn; // Find primary matches
	if(matcher){matcher(matcherIn,matcherOut,context,xml);} // Apply postFilter
	if(postFilter){temp = condense(matcherOut,postMap);postFilter(temp,[],context,xml); // Un-match failing elements by moving them back to matcherIn
	i = temp.length;while(i--) {if(elem = temp[i]){matcherOut[postMap[i]] = !(matcherIn[postMap[i]] = elem);}}}if(seed){if(postFinder || preFilter){if(postFinder){ // Get the final matcherOut by condensing this intermediate into postFinder contexts
	temp = [];i = matcherOut.length;while(i--) {if(elem = matcherOut[i]){ // Restore matcherIn since elem is not yet a final match
	temp.push(matcherIn[i] = elem);}}postFinder(null,matcherOut = [],temp,xml);} // Move matched elements from seed to results to keep them synchronized
	i = matcherOut.length;while(i--) {if((elem = matcherOut[i]) && (temp = postFinder?indexOf(seed,elem):preMap[i]) > -1){seed[temp] = !(results[temp] = elem);}}} // Add elements to results, through postFinder if defined
	}else {matcherOut = condense(matcherOut === results?matcherOut.splice(preexisting,matcherOut.length):matcherOut);if(postFinder){postFinder(null,results,matcherOut,xml);}else {push.apply(results,matcherOut);}}});}function matcherFromTokens(tokens){var checkContext,matcher,j,len=tokens.length,leadingRelative=Expr.relative[tokens[0].type],implicitRelative=leadingRelative || Expr.relative[" "],i=leadingRelative?1:0, // The foundational matcher ensures that elements are reachable from top-level context(s)
	matchContext=addCombinator(function(elem){return elem === checkContext;},implicitRelative,true),matchAnyContext=addCombinator(function(elem){return indexOf(checkContext,elem) > -1;},implicitRelative,true),matchers=[function(elem,context,xml){var ret=!leadingRelative && (xml || context !== outermostContext) || ((checkContext = context).nodeType?matchContext(elem,context,xml):matchAnyContext(elem,context,xml)); // Avoid hanging onto element (issue #299)
	checkContext = null;return ret;}];for(;i < len;i++) {if(matcher = Expr.relative[tokens[i].type]){matchers = [addCombinator(elementMatcher(matchers),matcher)];}else {matcher = Expr.filter[tokens[i].type].apply(null,tokens[i].matches); // Return special upon seeing a positional matcher
	if(matcher[expando]){ // Find the next relative operator (if any) for proper handling
	j = ++i;for(;j < len;j++) {if(Expr.relative[tokens[j].type]){break;}}return setMatcher(i > 1 && elementMatcher(matchers),i > 1 && toSelector( // If the preceding token was a descendant combinator, insert an implicit any-element `*`
	tokens.slice(0,i - 1).concat({value:tokens[i - 2].type === " "?"*":""})).replace(rtrim,"$1"),matcher,i < j && matcherFromTokens(tokens.slice(i,j)),j < len && matcherFromTokens(tokens = tokens.slice(j)),j < len && toSelector(tokens));}matchers.push(matcher);}}return elementMatcher(matchers);}function matcherFromGroupMatchers(elementMatchers,setMatchers){var bySet=setMatchers.length > 0,byElement=elementMatchers.length > 0,superMatcher=function superMatcher(seed,context,xml,results,outermost){var elem,j,matcher,matchedCount=0,i="0",unmatched=seed && [],setMatched=[],contextBackup=outermostContext, // We must always have either seed elements or outermost context
	elems=seed || byElement && Expr.find["TAG"]("*",outermost), // Use integer dirruns iff this is the outermost matcher
	dirrunsUnique=dirruns += contextBackup == null?1:Math.random() || 0.1,len=elems.length;if(outermost){outermostContext = context === document || context || outermost;} // Add elements passing elementMatchers directly to results
	// Support: IE<9, Safari
	// Tolerate NodeList properties (IE: "length"; Safari: <number>) matching elements by id
	for(;i !== len && (elem = elems[i]) != null;i++) {if(byElement && elem){j = 0;if(!context && elem.ownerDocument !== document){setDocument(elem);xml = !documentIsHTML;}while(matcher = elementMatchers[j++]) {if(matcher(elem,context || document,xml)){results.push(elem);break;}}if(outermost){dirruns = dirrunsUnique;}} // Track unmatched elements for set filters
	if(bySet){ // They will have gone through all possible matchers
	if(elem = !matcher && elem){matchedCount--;} // Lengthen the array for every element, matched or not
	if(seed){unmatched.push(elem);}}} // `i` is now the count of elements visited above, and adding it to `matchedCount`
	// makes the latter nonnegative.
	matchedCount += i; // Apply set filters to unmatched elements
	// NOTE: This can be skipped if there are no unmatched elements (i.e., `matchedCount`
	// equals `i`), unless we didn't visit _any_ elements in the above loop because we have
	// no element matchers and no seed.
	// Incrementing an initially-string "0" `i` allows `i` to remain a string only in that
	// case, which will result in a "00" `matchedCount` that differs from `i` but is also
	// numerically zero.
	if(bySet && i !== matchedCount){j = 0;while(matcher = setMatchers[j++]) {matcher(unmatched,setMatched,context,xml);}if(seed){ // Reintegrate element matches to eliminate the need for sorting
	if(matchedCount > 0){while(i--) {if(!(unmatched[i] || setMatched[i])){setMatched[i] = pop.call(results);}}} // Discard index placeholder values to get only actual matches
	setMatched = condense(setMatched);} // Add matches to results
	push.apply(results,setMatched); // Seedless set matches succeeding multiple successful matchers stipulate sorting
	if(outermost && !seed && setMatched.length > 0 && matchedCount + setMatchers.length > 1){Sizzle.uniqueSort(results);}} // Override manipulation of globals by nested matchers
	if(outermost){dirruns = dirrunsUnique;outermostContext = contextBackup;}return unmatched;};return bySet?markFunction(superMatcher):superMatcher;}compile = Sizzle.compile = function(selector,match /* Internal Use Only */){var i,setMatchers=[],elementMatchers=[],cached=compilerCache[selector + " "];if(!cached){ // Generate a function of recursive functions that can be used to check each element
	if(!match){match = tokenize(selector);}i = match.length;while(i--) {cached = matcherFromTokens(match[i]);if(cached[expando]){setMatchers.push(cached);}else {elementMatchers.push(cached);}} // Cache the compiled function
	cached = compilerCache(selector,matcherFromGroupMatchers(elementMatchers,setMatchers)); // Save selector and tokenization
	cached.selector = selector;}return cached;}; /**
	 * A low-level selection function that works with Sizzle's compiled
	 *  selector functions
	 * @param {String|Function} selector A selector or a pre-compiled
	 *  selector function built with Sizzle.compile
	 * @param {Element} context
	 * @param {Array} [results]
	 * @param {Array} [seed] A set of elements to match against
	 */select = Sizzle.select = function(selector,context,results,seed){var i,tokens,token,type,find,compiled=typeof selector === "function" && selector,match=!seed && tokenize(selector = compiled.selector || selector);results = results || []; // Try to minimize operations if there is only one selector in the list and no seed
	// (the latter of which guarantees us context)
	if(match.length === 1){ // Reduce context if the leading compound selector is an ID
	tokens = match[0] = match[0].slice(0);if(tokens.length > 2 && (token = tokens[0]).type === "ID" && support.getById && context.nodeType === 9 && documentIsHTML && Expr.relative[tokens[1].type]){context = (Expr.find["ID"](token.matches[0].replace(runescape,funescape),context) || [])[0];if(!context){return results; // Precompiled matchers will still verify ancestry, so step up a level
	}else if(compiled){context = context.parentNode;}selector = selector.slice(tokens.shift().value.length);} // Fetch a seed set for right-to-left matching
	i = matchExpr["needsContext"].test(selector)?0:tokens.length;while(i--) {token = tokens[i]; // Abort if we hit a combinator
	if(Expr.relative[type = token.type]){break;}if(find = Expr.find[type]){ // Search, expanding context for leading sibling combinators
	if(seed = find(token.matches[0].replace(runescape,funescape),rsibling.test(tokens[0].type) && testContext(context.parentNode) || context)){ // If seed is empty or no tokens remain, we can return early
	tokens.splice(i,1);selector = seed.length && toSelector(tokens);if(!selector){push.apply(results,seed);return results;}break;}}}} // Compile and execute a filtering function if one is not provided
	// Provide `match` to avoid retokenization if we modified the selector above
	(compiled || compile(selector,match))(seed,context,!documentIsHTML,results,!context || rsibling.test(selector) && testContext(context.parentNode) || context);return results;}; // One-time assignments
	// Sort stability
	support.sortStable = expando.split("").sort(sortOrder).join("") === expando; // Support: Chrome 14-35+
	// Always assume duplicates if they aren't passed to the comparison function
	support.detectDuplicates = !!hasDuplicate; // Initialize against the default document
	setDocument(); // Support: Webkit<537.32 - Safari 6.0.3/Chrome 25 (fixed in Chrome 27)
	// Detached nodes confoundingly follow *each other*
	support.sortDetached = assert(function(div1){ // Should return 1, but returns 4 (following)
	return div1.compareDocumentPosition(document.createElement("div")) & 1;}); // Support: IE<8
	// Prevent attribute/property "interpolation"
	// http://msdn.microsoft.com/en-us/library/ms536429%28VS.85%29.aspx
	if(!assert(function(div){div.innerHTML = "<a href='#'></a>";return div.firstChild.getAttribute("href") === "#";})){addHandle("type|href|height|width",function(elem,name,isXML){if(!isXML){return elem.getAttribute(name,name.toLowerCase() === "type"?1:2);}});} // Support: IE<9
	// Use defaultValue in place of getAttribute("value")
	if(!support.attributes || !assert(function(div){div.innerHTML = "<input/>";div.firstChild.setAttribute("value","");return div.firstChild.getAttribute("value") === "";})){addHandle("value",function(elem,name,isXML){if(!isXML && elem.nodeName.toLowerCase() === "input"){return elem.defaultValue;}});} // Support: IE<9
	// Use getAttributeNode to fetch booleans when getAttribute lies
	if(!assert(function(div){return div.getAttribute("disabled") == null;})){addHandle(booleans,function(elem,name,isXML){var val;if(!isXML){return elem[name] === true?name.toLowerCase():(val = elem.getAttributeNode(name)) && val.specified?val.value:null;}});}return Sizzle;})(window);jQuery.find = Sizzle;jQuery.expr = Sizzle.selectors;jQuery.expr[":"] = jQuery.expr.pseudos;jQuery.uniqueSort = jQuery.unique = Sizzle.uniqueSort;jQuery.text = Sizzle.getText;jQuery.isXMLDoc = Sizzle.isXML;jQuery.contains = Sizzle.contains;var dir=function dir(elem,_dir,until){var matched=[],truncate=until !== undefined;while((elem = elem[_dir]) && elem.nodeType !== 9) {if(elem.nodeType === 1){if(truncate && jQuery(elem).is(until)){break;}matched.push(elem);}}return matched;};var _siblings=function _siblings(n,elem){var matched=[];for(;n;n = n.nextSibling) {if(n.nodeType === 1 && n !== elem){matched.push(n);}}return matched;};var rneedsContext=jQuery.expr.match.needsContext;var rsingleTag=/^<([\w-]+)\s*\/?>(?:<\/\1>|)$/;var risSimple=/^.[^:#\[\.,]*$/; // Implement the identical functionality for filter and not
	function winnow(elements,qualifier,not){if(jQuery.isFunction(qualifier)){return jQuery.grep(elements,function(elem,i){ /* jshint -W018 */return !!qualifier.call(elem,i,elem) !== not;});}if(qualifier.nodeType){return jQuery.grep(elements,function(elem){return elem === qualifier !== not;});}if(typeof qualifier === "string"){if(risSimple.test(qualifier)){return jQuery.filter(qualifier,elements,not);}qualifier = jQuery.filter(qualifier,elements);}return jQuery.grep(elements,function(elem){return indexOf.call(qualifier,elem) > -1 !== not;});}jQuery.filter = function(expr,elems,not){var elem=elems[0];if(not){expr = ":not(" + expr + ")";}return elems.length === 1 && elem.nodeType === 1?jQuery.find.matchesSelector(elem,expr)?[elem]:[]:jQuery.find.matches(expr,jQuery.grep(elems,function(elem){return elem.nodeType === 1;}));};jQuery.fn.extend({find:function find(selector){var i,len=this.length,ret=[],self=this;if(typeof selector !== "string"){return this.pushStack(jQuery(selector).filter(function(){for(i = 0;i < len;i++) {if(jQuery.contains(self[i],this)){return true;}}}));}for(i = 0;i < len;i++) {jQuery.find(selector,self[i],ret);} // Needed because $( selector, context ) becomes $( context ).find( selector )
	ret = this.pushStack(len > 1?jQuery.unique(ret):ret);ret.selector = this.selector?this.selector + " " + selector:selector;return ret;},filter:function filter(selector){return this.pushStack(winnow(this,selector || [],false));},not:function not(selector){return this.pushStack(winnow(this,selector || [],true));},is:function is(selector){return !!winnow(this, // If this is a positional/relative selector, check membership in the returned set
	// so $("p:first").is("p:last") won't return true for a doc with two "p".
	typeof selector === "string" && rneedsContext.test(selector)?jQuery(selector):selector || [],false).length;}}); // Initialize a jQuery object
	// A central reference to the root jQuery(document)
	var rootjQuery, // A simple way to check for HTML strings
	// Prioritize #id over <tag> to avoid XSS via location.hash (#9521)
	// Strict HTML recognition (#11290: must start with <)
	rquickExpr=/^(?:\s*(<[\w\W]+>)[^>]*|#([\w-]*))$/,init=jQuery.fn.init = function(selector,context,root){var match,elem; // HANDLE: $(""), $(null), $(undefined), $(false)
	if(!selector){return this;} // Method init() accepts an alternate rootjQuery
	// so migrate can support jQuery.sub (gh-2101)
	root = root || rootjQuery; // Handle HTML strings
	if(typeof selector === "string"){if(selector[0] === "<" && selector[selector.length - 1] === ">" && selector.length >= 3){ // Assume that strings that start and end with <> are HTML and skip the regex check
	match = [null,selector,null];}else {match = rquickExpr.exec(selector);} // Match html or make sure no context is specified for #id
	if(match && (match[1] || !context)){ // HANDLE: $(html) -> $(array)
	if(match[1]){context = context instanceof jQuery?context[0]:context; // Option to run scripts is true for back-compat
	// Intentionally let the error be thrown if parseHTML is not present
	jQuery.merge(this,jQuery.parseHTML(match[1],context && context.nodeType?context.ownerDocument || context:document,true)); // HANDLE: $(html, props)
	if(rsingleTag.test(match[1]) && jQuery.isPlainObject(context)){for(match in context) { // Properties of context are called as methods if possible
	if(jQuery.isFunction(this[match])){this[match](context[match]); // ...and otherwise set as attributes
	}else {this.attr(match,context[match]);}}}return this; // HANDLE: $(#id)
	}else {elem = document.getElementById(match[2]); // Support: Blackberry 4.6
	// gEBID returns nodes no longer in the document (#6963)
	if(elem && elem.parentNode){ // Inject the element directly into the jQuery object
	this.length = 1;this[0] = elem;}this.context = document;this.selector = selector;return this;} // HANDLE: $(expr, $(...))
	}else if(!context || context.jquery){return (context || root).find(selector); // HANDLE: $(expr, context)
	// (which is just equivalent to: $(context).find(expr)
	}else {return this.constructor(context).find(selector);} // HANDLE: $(DOMElement)
	}else if(selector.nodeType){this.context = this[0] = selector;this.length = 1;return this; // HANDLE: $(function)
	// Shortcut for document ready
	}else if(jQuery.isFunction(selector)){return root.ready !== undefined?root.ready(selector): // Execute immediately if ready is not present
	selector(jQuery);}if(selector.selector !== undefined){this.selector = selector.selector;this.context = selector.context;}return jQuery.makeArray(selector,this);}; // Give the init function the jQuery prototype for later instantiation
	init.prototype = jQuery.fn; // Initialize central reference
	rootjQuery = jQuery(document);var rparentsprev=/^(?:parents|prev(?:Until|All))/, // Methods guaranteed to produce a unique set when starting from a unique set
	guaranteedUnique={children:true,contents:true,next:true,prev:true};jQuery.fn.extend({has:function has(target){var targets=jQuery(target,this),l=targets.length;return this.filter(function(){var i=0;for(;i < l;i++) {if(jQuery.contains(this,targets[i])){return true;}}});},closest:function closest(selectors,context){var cur,i=0,l=this.length,matched=[],pos=rneedsContext.test(selectors) || typeof selectors !== "string"?jQuery(selectors,context || this.context):0;for(;i < l;i++) {for(cur = this[i];cur && cur !== context;cur = cur.parentNode) { // Always skip document fragments
	if(cur.nodeType < 11 && (pos?pos.index(cur) > -1: // Don't pass non-elements to Sizzle
	cur.nodeType === 1 && jQuery.find.matchesSelector(cur,selectors))){matched.push(cur);break;}}}return this.pushStack(matched.length > 1?jQuery.uniqueSort(matched):matched);}, // Determine the position of an element within the set
	index:function index(elem){ // No argument, return index in parent
	if(!elem){return this[0] && this[0].parentNode?this.first().prevAll().length:-1;} // Index in selector
	if(typeof elem === "string"){return indexOf.call(jQuery(elem),this[0]);} // Locate the position of the desired element
	return indexOf.call(this, // If it receives a jQuery object, the first element is used
	elem.jquery?elem[0]:elem);},add:function add(selector,context){return this.pushStack(jQuery.uniqueSort(jQuery.merge(this.get(),jQuery(selector,context))));},addBack:function addBack(selector){return this.add(selector == null?this.prevObject:this.prevObject.filter(selector));}});function sibling(cur,dir){while((cur = cur[dir]) && cur.nodeType !== 1) {}return cur;}jQuery.each({parent:function parent(elem){var parent=elem.parentNode;return parent && parent.nodeType !== 11?parent:null;},parents:function parents(elem){return dir(elem,"parentNode");},parentsUntil:function parentsUntil(elem,i,until){return dir(elem,"parentNode",until);},next:function next(elem){return sibling(elem,"nextSibling");},prev:function prev(elem){return sibling(elem,"previousSibling");},nextAll:function nextAll(elem){return dir(elem,"nextSibling");},prevAll:function prevAll(elem){return dir(elem,"previousSibling");},nextUntil:function nextUntil(elem,i,until){return dir(elem,"nextSibling",until);},prevUntil:function prevUntil(elem,i,until){return dir(elem,"previousSibling",until);},siblings:function siblings(elem){return _siblings((elem.parentNode || {}).firstChild,elem);},children:function children(elem){return _siblings(elem.firstChild);},contents:function contents(elem){return elem.contentDocument || jQuery.merge([],elem.childNodes);}},function(name,fn){jQuery.fn[name] = function(until,selector){var matched=jQuery.map(this,fn,until);if(name.slice(-5) !== "Until"){selector = until;}if(selector && typeof selector === "string"){matched = jQuery.filter(selector,matched);}if(this.length > 1){ // Remove duplicates
	if(!guaranteedUnique[name]){jQuery.uniqueSort(matched);} // Reverse order for parents* and prev-derivatives
	if(rparentsprev.test(name)){matched.reverse();}}return this.pushStack(matched);};});var rnotwhite=/\S+/g; // Convert String-formatted options into Object-formatted ones
	function createOptions(options){var object={};jQuery.each(options.match(rnotwhite) || [],function(_,flag){object[flag] = true;});return object;} /*
	 * Create a callback list using the following parameters:
	 *
	 *	options: an optional list of space-separated options that will change how
	 *			the callback list behaves or a more traditional option object
	 *
	 * By default a callback list will act like an event callback list and can be
	 * "fired" multiple times.
	 *
	 * Possible options:
	 *
	 *	once:			will ensure the callback list can only be fired once (like a Deferred)
	 *
	 *	memory:			will keep track of previous values and will call any callback added
	 *					after the list has been fired right away with the latest "memorized"
	 *					values (like a Deferred)
	 *
	 *	unique:			will ensure a callback can only be added once (no duplicate in the list)
	 *
	 *	stopOnFalse:	interrupt callings when a callback returns false
	 *
	 */jQuery.Callbacks = function(options){ // Convert options from String-formatted to Object-formatted if needed
	// (we check in cache first)
	options = typeof options === "string"?createOptions(options):jQuery.extend({},options);var  // Flag to know if list is currently firing
	firing, // Last fire value for non-forgettable lists
	memory, // Flag to know if list was already fired
	_fired, // Flag to prevent firing
	_locked, // Actual callback list
	list=[], // Queue of execution data for repeatable lists
	queue=[], // Index of currently firing callback (modified by add/remove as needed)
	firingIndex=-1, // Fire callbacks
	fire=function fire(){ // Enforce single-firing
	_locked = options.once; // Execute callbacks for all pending executions,
	// respecting firingIndex overrides and runtime changes
	_fired = firing = true;for(;queue.length;firingIndex = -1) {memory = queue.shift();while(++firingIndex < list.length) { // Run callback and check for early termination
	if(list[firingIndex].apply(memory[0],memory[1]) === false && options.stopOnFalse){ // Jump to end and forget the data so .add doesn't re-fire
	firingIndex = list.length;memory = false;}}} // Forget the data if we're done with it
	if(!options.memory){memory = false;}firing = false; // Clean up if we're done firing for good
	if(_locked){ // Keep an empty list if we have data for future add calls
	if(memory){list = []; // Otherwise, this object is spent
	}else {list = "";}}}, // Actual Callbacks object
	self={ // Add a callback or a collection of callbacks to the list
	add:function add(){if(list){ // If we have memory from a past run, we should fire after adding
	if(memory && !firing){firingIndex = list.length - 1;queue.push(memory);}(function add(args){jQuery.each(args,function(_,arg){if(jQuery.isFunction(arg)){if(!options.unique || !self.has(arg)){list.push(arg);}}else if(arg && arg.length && jQuery.type(arg) !== "string"){ // Inspect recursively
	add(arg);}});})(arguments);if(memory && !firing){fire();}}return this;}, // Remove a callback from the list
	remove:function remove(){jQuery.each(arguments,function(_,arg){var index;while((index = jQuery.inArray(arg,list,index)) > -1) {list.splice(index,1); // Handle firing indexes
	if(index <= firingIndex){firingIndex--;}}});return this;}, // Check if a given callback is in the list.
	// If no argument is given, return whether or not list has callbacks attached.
	has:function has(fn){return fn?jQuery.inArray(fn,list) > -1:list.length > 0;}, // Remove all callbacks from the list
	empty:function empty(){if(list){list = [];}return this;}, // Disable .fire and .add
	// Abort any current/pending executions
	// Clear all callbacks and values
	disable:function disable(){_locked = queue = [];list = memory = "";return this;},disabled:function disabled(){return !list;}, // Disable .fire
	// Also disable .add unless we have memory (since it would have no effect)
	// Abort any pending executions
	lock:function lock(){_locked = queue = [];if(!memory){list = memory = "";}return this;},locked:function locked(){return !!_locked;}, // Call all callbacks with the given context and arguments
	fireWith:function fireWith(context,args){if(!_locked){args = args || [];args = [context,args.slice?args.slice():args];queue.push(args);if(!firing){fire();}}return this;}, // Call all the callbacks with the given arguments
	fire:function fire(){self.fireWith(this,arguments);return this;}, // To know if the callbacks have already been called at least once
	fired:function fired(){return !!_fired;}};return self;};jQuery.extend({Deferred:function Deferred(func){var tuples=[ // action, add listener, listener list, final state
	["resolve","done",jQuery.Callbacks("once memory"),"resolved"],["reject","fail",jQuery.Callbacks("once memory"),"rejected"],["notify","progress",jQuery.Callbacks("memory")]],_state="pending",_promise={state:function state(){return _state;},always:function always(){deferred.done(arguments).fail(arguments);return this;},then:function then() /* fnDone, fnFail, fnProgress */{var fns=arguments;return jQuery.Deferred(function(newDefer){jQuery.each(tuples,function(i,tuple){var fn=jQuery.isFunction(fns[i]) && fns[i]; // deferred[ done | fail | progress ] for forwarding actions to newDefer
	deferred[tuple[1]](function(){var returned=fn && fn.apply(this,arguments);if(returned && jQuery.isFunction(returned.promise)){returned.promise().progress(newDefer.notify).done(newDefer.resolve).fail(newDefer.reject);}else {newDefer[tuple[0] + "With"](this === _promise?newDefer.promise():this,fn?[returned]:arguments);}});});fns = null;}).promise();}, // Get a promise for this deferred
	// If obj is provided, the promise aspect is added to the object
	promise:function promise(obj){return obj != null?jQuery.extend(obj,_promise):_promise;}},deferred={}; // Keep pipe for back-compat
	_promise.pipe = _promise.then; // Add list-specific methods
	jQuery.each(tuples,function(i,tuple){var list=tuple[2],stateString=tuple[3]; // promise[ done | fail | progress ] = list.add
	_promise[tuple[1]] = list.add; // Handle state
	if(stateString){list.add(function(){ // state = [ resolved | rejected ]
	_state = stateString; // [ reject_list | resolve_list ].disable; progress_list.lock
	},tuples[i ^ 1][2].disable,tuples[2][2].lock);} // deferred[ resolve | reject | notify ]
	deferred[tuple[0]] = function(){deferred[tuple[0] + "With"](this === deferred?_promise:this,arguments);return this;};deferred[tuple[0] + "With"] = list.fireWith;}); // Make the deferred a promise
	_promise.promise(deferred); // Call given func if any
	if(func){func.call(deferred,deferred);} // All done!
	return deferred;}, // Deferred helper
	when:function when(subordinate /* , ..., subordinateN */){var i=0,resolveValues=_slice.call(arguments),length=resolveValues.length, // the count of uncompleted subordinates
	remaining=length !== 1 || subordinate && jQuery.isFunction(subordinate.promise)?length:0, // the master Deferred.
	// If resolveValues consist of only a single Deferred, just use that.
	deferred=remaining === 1?subordinate:jQuery.Deferred(), // Update function for both resolve and progress values
	updateFunc=function updateFunc(i,contexts,values){return function(value){contexts[i] = this;values[i] = arguments.length > 1?_slice.call(arguments):value;if(values === progressValues){deferred.notifyWith(contexts,values);}else if(! --remaining){deferred.resolveWith(contexts,values);}};},progressValues,progressContexts,resolveContexts; // Add listeners to Deferred subordinates; treat others as resolved
	if(length > 1){progressValues = new Array(length);progressContexts = new Array(length);resolveContexts = new Array(length);for(;i < length;i++) {if(resolveValues[i] && jQuery.isFunction(resolveValues[i].promise)){resolveValues[i].promise().progress(updateFunc(i,progressContexts,progressValues)).done(updateFunc(i,resolveContexts,resolveValues)).fail(deferred.reject);}else {--remaining;}}} // If we're not waiting on anything, resolve the master
	if(!remaining){deferred.resolveWith(resolveContexts,resolveValues);}return deferred.promise();}}); // The deferred used on DOM ready
	var readyList;jQuery.fn.ready = function(fn){ // Add the callback
	jQuery.ready.promise().done(fn);return this;};jQuery.extend({ // Is the DOM ready to be used? Set to true once it occurs.
	isReady:false, // A counter to track how many items to wait for before
	// the ready event fires. See #6781
	readyWait:1, // Hold (or release) the ready event
	holdReady:function holdReady(hold){if(hold){jQuery.readyWait++;}else {jQuery.ready(true);}}, // Handle when the DOM is ready
	ready:function ready(wait){ // Abort if there are pending holds or we're already ready
	if(wait === true?--jQuery.readyWait:jQuery.isReady){return;} // Remember that the DOM is ready
	jQuery.isReady = true; // If a normal DOM Ready event fired, decrement, and wait if need be
	if(wait !== true && --jQuery.readyWait > 0){return;} // If there are functions bound, to execute
	readyList.resolveWith(document,[jQuery]); // Trigger any bound ready events
	if(jQuery.fn.triggerHandler){jQuery(document).triggerHandler("ready");jQuery(document).off("ready");}}}); /**
	 * The ready event handler and self cleanup method
	 */function completed(){document.removeEventListener("DOMContentLoaded",completed);window.removeEventListener("load",completed);jQuery.ready();}jQuery.ready.promise = function(obj){if(!readyList){readyList = jQuery.Deferred(); // Catch cases where $(document).ready() is called
	// after the browser event has already occurred.
	// Support: IE9-10 only
	// Older IE sometimes signals "interactive" too soon
	if(document.readyState === "complete" || document.readyState !== "loading" && !document.documentElement.doScroll){ // Handle it asynchronously to allow scripts the opportunity to delay ready
	window.setTimeout(jQuery.ready);}else { // Use the handy event callback
	document.addEventListener("DOMContentLoaded",completed); // A fallback to window.onload, that will always work
	window.addEventListener("load",completed);}}return readyList.promise(obj);}; // Kick off the DOM ready check even if the user does not
	jQuery.ready.promise(); // Multifunctional method to get and set values of a collection
	// The value/s can optionally be executed if it's a function
	var access=function access(elems,fn,key,value,chainable,emptyGet,raw){var i=0,len=elems.length,bulk=key == null; // Sets many values
	if(jQuery.type(key) === "object"){chainable = true;for(i in key) {access(elems,fn,i,key[i],true,emptyGet,raw);} // Sets one value
	}else if(value !== undefined){chainable = true;if(!jQuery.isFunction(value)){raw = true;}if(bulk){ // Bulk operations run against the entire set
	if(raw){fn.call(elems,value);fn = null; // ...except when executing function values
	}else {bulk = fn;fn = function(elem,key,value){return bulk.call(jQuery(elem),value);};}}if(fn){for(;i < len;i++) {fn(elems[i],key,raw?value:value.call(elems[i],i,fn(elems[i],key)));}}}return chainable?elems: // Gets
	bulk?fn.call(elems):len?fn(elems[0],key):emptyGet;};var acceptData=function acceptData(owner){ // Accepts only:
	//  - Node
	//    - Node.ELEMENT_NODE
	//    - Node.DOCUMENT_NODE
	//  - Object
	//    - Any
	/* jshint -W018 */return owner.nodeType === 1 || owner.nodeType === 9 || ! +owner.nodeType;};function Data(){this.expando = jQuery.expando + Data.uid++;}Data.uid = 1;Data.prototype = {register:function register(owner,initial){var value=initial || {}; // If it is a node unlikely to be stringify-ed or looped over
	// use plain assignment
	if(owner.nodeType){owner[this.expando] = value; // Otherwise secure it in a non-enumerable, non-writable property
	// configurability must be true to allow the property to be
	// deleted with the delete operator
	}else {Object.defineProperty(owner,this.expando,{value:value,writable:true,configurable:true});}return owner[this.expando];},cache:function cache(owner){ // We can accept data for non-element nodes in modern browsers,
	// but we should not, see #8335.
	// Always return an empty object.
	if(!acceptData(owner)){return {};} // Check if the owner object already has a cache
	var value=owner[this.expando]; // If not, create one
	if(!value){value = {}; // We can accept data for non-element nodes in modern browsers,
	// but we should not, see #8335.
	// Always return an empty object.
	if(acceptData(owner)){ // If it is a node unlikely to be stringify-ed or looped over
	// use plain assignment
	if(owner.nodeType){owner[this.expando] = value; // Otherwise secure it in a non-enumerable property
	// configurable must be true to allow the property to be
	// deleted when data is removed
	}else {Object.defineProperty(owner,this.expando,{value:value,configurable:true});}}}return value;},set:function set(owner,data,value){var prop,cache=this.cache(owner); // Handle: [ owner, key, value ] args
	if(typeof data === "string"){cache[data] = value; // Handle: [ owner, { properties } ] args
	}else { // Copy the properties one-by-one to the cache object
	for(prop in data) {cache[prop] = data[prop];}}return cache;},get:function get(owner,key){return key === undefined?this.cache(owner):owner[this.expando] && owner[this.expando][key];},access:function access(owner,key,value){var stored; // In cases where either:
	//
	//   1. No key was specified
	//   2. A string key was specified, but no value provided
	//
	// Take the "read" path and allow the get method to determine
	// which value to return, respectively either:
	//
	//   1. The entire cache object
	//   2. The data stored at the key
	//
	if(key === undefined || key && typeof key === "string" && value === undefined){stored = this.get(owner,key);return stored !== undefined?stored:this.get(owner,jQuery.camelCase(key));} // When the key is not a string, or both a key and value
	// are specified, set or extend (existing objects) with either:
	//
	//   1. An object of properties
	//   2. A key and value
	//
	this.set(owner,key,value); // Since the "set" path can have two possible entry points
	// return the expected data based on which path was taken[*]
	return value !== undefined?value:key;},remove:function remove(owner,key){var i,name,camel,cache=owner[this.expando];if(cache === undefined){return;}if(key === undefined){this.register(owner);}else { // Support array or space separated string of keys
	if(jQuery.isArray(key)){ // If "name" is an array of keys...
	// When data is initially created, via ("key", "val") signature,
	// keys will be converted to camelCase.
	// Since there is no way to tell _how_ a key was added, remove
	// both plain key and camelCase key. #12786
	// This will only penalize the array argument path.
	name = key.concat(key.map(jQuery.camelCase));}else {camel = jQuery.camelCase(key); // Try the string as a key before any manipulation
	if(key in cache){name = [key,camel];}else { // If a key with the spaces exists, use it.
	// Otherwise, create an array by matching non-whitespace
	name = camel;name = name in cache?[name]:name.match(rnotwhite) || [];}}i = name.length;while(i--) {delete cache[name[i]];}} // Remove the expando if there's no more data
	if(key === undefined || jQuery.isEmptyObject(cache)){ // Support: Chrome <= 35-45+
	// Webkit & Blink performance suffers when deleting properties
	// from DOM nodes, so set to undefined instead
	// https://code.google.com/p/chromium/issues/detail?id=378607
	if(owner.nodeType){owner[this.expando] = undefined;}else {delete owner[this.expando];}}},hasData:function hasData(owner){var cache=owner[this.expando];return cache !== undefined && !jQuery.isEmptyObject(cache);}};var dataPriv=new Data();var dataUser=new Data(); //	Implementation Summary
	//
	//	1. Enforce API surface and semantic compatibility with 1.9.x branch
	//	2. Improve the module's maintainability by reducing the storage
	//		paths to a single mechanism.
	//	3. Use the same single mechanism to support "private" and "user" data.
	//	4. _Never_ expose "private" data to user code (TODO: Drop _data, _removeData)
	//	5. Avoid exposing implementation details on user objects (eg. expando properties)
	//	6. Provide a clear path for implementation upgrade to WeakMap in 2014
	var rbrace=/^(?:\{[\w\W]*\}|\[[\w\W]*\])$/,rmultiDash=/[A-Z]/g;function dataAttr(elem,key,data){var name; // If nothing was found internally, try to fetch any
	// data from the HTML5 data-* attribute
	if(data === undefined && elem.nodeType === 1){name = "data-" + key.replace(rmultiDash,"-$&").toLowerCase();data = elem.getAttribute(name);if(typeof data === "string"){try{data = data === "true"?true:data === "false"?false:data === "null"?null: // Only convert to a number if it doesn't change the string
	+data + "" === data?+data:rbrace.test(data)?jQuery.parseJSON(data):data;}catch(e) {} // Make sure we set the data so it isn't changed later
	dataUser.set(elem,key,data);}else {data = undefined;}}return data;}jQuery.extend({hasData:function hasData(elem){return dataUser.hasData(elem) || dataPriv.hasData(elem);},data:function data(elem,name,_data){return dataUser.access(elem,name,_data);},removeData:function removeData(elem,name){dataUser.remove(elem,name);}, // TODO: Now that all calls to _data and _removeData have been replaced
	// with direct calls to dataPriv methods, these can be deprecated.
	_data:function _data(elem,name,data){return dataPriv.access(elem,name,data);},_removeData:function _removeData(elem,name){dataPriv.remove(elem,name);}});jQuery.fn.extend({data:function data(key,value){var i,name,data,elem=this[0],attrs=elem && elem.attributes; // Gets all values
	if(key === undefined){if(this.length){data = dataUser.get(elem);if(elem.nodeType === 1 && !dataPriv.get(elem,"hasDataAttrs")){i = attrs.length;while(i--) { // Support: IE11+
	// The attrs elements can be null (#14894)
	if(attrs[i]){name = attrs[i].name;if(name.indexOf("data-") === 0){name = jQuery.camelCase(name.slice(5));dataAttr(elem,name,data[name]);}}}dataPriv.set(elem,"hasDataAttrs",true);}}return data;} // Sets multiple values
	if(typeof key === "object"){return this.each(function(){dataUser.set(this,key);});}return access(this,function(value){var data,camelKey; // The calling jQuery object (element matches) is not empty
	// (and therefore has an element appears at this[ 0 ]) and the
	// `value` parameter was not undefined. An empty jQuery object
	// will result in `undefined` for elem = this[ 0 ] which will
	// throw an exception if an attempt to read a data cache is made.
	if(elem && value === undefined){ // Attempt to get data from the cache
	// with the key as-is
	data = dataUser.get(elem,key) ||  // Try to find dashed key if it exists (gh-2779)
	// This is for 2.2.x only
	dataUser.get(elem,key.replace(rmultiDash,"-$&").toLowerCase());if(data !== undefined){return data;}camelKey = jQuery.camelCase(key); // Attempt to get data from the cache
	// with the key camelized
	data = dataUser.get(elem,camelKey);if(data !== undefined){return data;} // Attempt to "discover" the data in
	// HTML5 custom data-* attrs
	data = dataAttr(elem,camelKey,undefined);if(data !== undefined){return data;} // We tried really hard, but the data doesn't exist.
	return;} // Set the data...
	camelKey = jQuery.camelCase(key);this.each(function(){ // First, attempt to store a copy or reference of any
	// data that might've been store with a camelCased key.
	var data=dataUser.get(this,camelKey); // For HTML5 data-* attribute interop, we have to
	// store property names with dashes in a camelCase form.
	// This might not apply to all properties...*
	dataUser.set(this,camelKey,value); // *... In the case of properties that might _actually_
	// have dashes, we need to also store a copy of that
	// unchanged property.
	if(key.indexOf("-") > -1 && data !== undefined){dataUser.set(this,key,value);}});},null,value,arguments.length > 1,null,true);},removeData:function removeData(key){return this.each(function(){dataUser.remove(this,key);});}});jQuery.extend({queue:function queue(elem,type,data){var queue;if(elem){type = (type || "fx") + "queue";queue = dataPriv.get(elem,type); // Speed up dequeue by getting out quickly if this is just a lookup
	if(data){if(!queue || jQuery.isArray(data)){queue = dataPriv.access(elem,type,jQuery.makeArray(data));}else {queue.push(data);}}return queue || [];}},dequeue:function dequeue(elem,type){type = type || "fx";var queue=jQuery.queue(elem,type),startLength=queue.length,fn=queue.shift(),hooks=jQuery._queueHooks(elem,type),next=function next(){jQuery.dequeue(elem,type);}; // If the fx queue is dequeued, always remove the progress sentinel
	if(fn === "inprogress"){fn = queue.shift();startLength--;}if(fn){ // Add a progress sentinel to prevent the fx queue from being
	// automatically dequeued
	if(type === "fx"){queue.unshift("inprogress");} // Clear up the last queue stop function
	delete hooks.stop;fn.call(elem,next,hooks);}if(!startLength && hooks){hooks.empty.fire();}}, // Not public - generate a queueHooks object, or return the current one
	_queueHooks:function _queueHooks(elem,type){var key=type + "queueHooks";return dataPriv.get(elem,key) || dataPriv.access(elem,key,{empty:jQuery.Callbacks("once memory").add(function(){dataPriv.remove(elem,[type + "queue",key]);})});}});jQuery.fn.extend({queue:function queue(type,data){var setter=2;if(typeof type !== "string"){data = type;type = "fx";setter--;}if(arguments.length < setter){return jQuery.queue(this[0],type);}return data === undefined?this:this.each(function(){var queue=jQuery.queue(this,type,data); // Ensure a hooks for this queue
	jQuery._queueHooks(this,type);if(type === "fx" && queue[0] !== "inprogress"){jQuery.dequeue(this,type);}});},dequeue:function dequeue(type){return this.each(function(){jQuery.dequeue(this,type);});},clearQueue:function clearQueue(type){return this.queue(type || "fx",[]);}, // Get a promise resolved when queues of a certain type
	// are emptied (fx is the type by default)
	promise:function promise(type,obj){var tmp,count=1,defer=jQuery.Deferred(),elements=this,i=this.length,resolve=function resolve(){if(! --count){defer.resolveWith(elements,[elements]);}};if(typeof type !== "string"){obj = type;type = undefined;}type = type || "fx";while(i--) {tmp = dataPriv.get(elements[i],type + "queueHooks");if(tmp && tmp.empty){count++;tmp.empty.add(resolve);}}resolve();return defer.promise(obj);}});var pnum=/[+-]?(?:\d*\.|)\d+(?:[eE][+-]?\d+|)/.source;var rcssNum=new RegExp("^(?:([+-])=|)(" + pnum + ")([a-z%]*)$","i");var cssExpand=["Top","Right","Bottom","Left"];var isHidden=function isHidden(elem,el){ // isHidden might be called from jQuery#filter function;
	// in that case, element will be second argument
	elem = el || elem;return jQuery.css(elem,"display") === "none" || !jQuery.contains(elem.ownerDocument,elem);};function adjustCSS(elem,prop,valueParts,tween){var adjusted,scale=1,maxIterations=20,currentValue=tween?function(){return tween.cur();}:function(){return jQuery.css(elem,prop,"");},initial=currentValue(),unit=valueParts && valueParts[3] || (jQuery.cssNumber[prop]?"":"px"), // Starting value computation is required for potential unit mismatches
	initialInUnit=(jQuery.cssNumber[prop] || unit !== "px" && +initial) && rcssNum.exec(jQuery.css(elem,prop));if(initialInUnit && initialInUnit[3] !== unit){ // Trust units reported by jQuery.css
	unit = unit || initialInUnit[3]; // Make sure we update the tween properties later on
	valueParts = valueParts || []; // Iteratively approximate from a nonzero starting point
	initialInUnit = +initial || 1;do { // If previous iteration zeroed out, double until we get *something*.
	// Use string for doubling so we don't accidentally see scale as unchanged below
	scale = scale || ".5"; // Adjust and apply
	initialInUnit = initialInUnit / scale;jQuery.style(elem,prop,initialInUnit + unit); // Update scale, tolerating zero or NaN from tween.cur()
	// Break the loop if scale is unchanged or perfect, or if we've just had enough.
	}while(scale !== (scale = currentValue() / initial) && scale !== 1 && --maxIterations);}if(valueParts){initialInUnit = +initialInUnit || +initial || 0; // Apply relative offset (+=/-=) if specified
	adjusted = valueParts[1]?initialInUnit + (valueParts[1] + 1) * valueParts[2]:+valueParts[2];if(tween){tween.unit = unit;tween.start = initialInUnit;tween.end = adjusted;}}return adjusted;}var rcheckableType=/^(?:checkbox|radio)$/i;var rtagName=/<([\w:-]+)/;var rscriptType=/^$|\/(?:java|ecma)script/i; // We have to close these tags to support XHTML (#13200)
	var wrapMap={ // Support: IE9
	option:[1,"<select multiple='multiple'>","</select>"], // XHTML parsers do not magically insert elements in the
	// same way that tag soup parsers do. So we cannot shorten
	// this by omitting <tbody> or other required elements.
	thead:[1,"<table>","</table>"],col:[2,"<table><colgroup>","</colgroup></table>"],tr:[2,"<table><tbody>","</tbody></table>"],td:[3,"<table><tbody><tr>","</tr></tbody></table>"],_default:[0,"",""]}; // Support: IE9
	wrapMap.optgroup = wrapMap.option;wrapMap.tbody = wrapMap.tfoot = wrapMap.colgroup = wrapMap.caption = wrapMap.thead;wrapMap.th = wrapMap.td;function getAll(context,tag){ // Support: IE9-11+
	// Use typeof to avoid zero-argument method invocation on host objects (#15151)
	var ret=typeof context.getElementsByTagName !== "undefined"?context.getElementsByTagName(tag || "*"):typeof context.querySelectorAll !== "undefined"?context.querySelectorAll(tag || "*"):[];return tag === undefined || tag && jQuery.nodeName(context,tag)?jQuery.merge([context],ret):ret;} // Mark scripts as having already been evaluated
	function setGlobalEval(elems,refElements){var i=0,l=elems.length;for(;i < l;i++) {dataPriv.set(elems[i],"globalEval",!refElements || dataPriv.get(refElements[i],"globalEval"));}}var rhtml=/<|&#?\w+;/;function buildFragment(elems,context,scripts,selection,ignored){var elem,tmp,tag,wrap,contains,j,fragment=context.createDocumentFragment(),nodes=[],i=0,l=elems.length;for(;i < l;i++) {elem = elems[i];if(elem || elem === 0){ // Add nodes directly
	if(jQuery.type(elem) === "object"){ // Support: Android<4.1, PhantomJS<2
	// push.apply(_, arraylike) throws on ancient WebKit
	jQuery.merge(nodes,elem.nodeType?[elem]:elem); // Convert non-html into a text node
	}else if(!rhtml.test(elem)){nodes.push(context.createTextNode(elem)); // Convert html into DOM nodes
	}else {tmp = tmp || fragment.appendChild(context.createElement("div")); // Deserialize a standard representation
	tag = (rtagName.exec(elem) || ["",""])[1].toLowerCase();wrap = wrapMap[tag] || wrapMap._default;tmp.innerHTML = wrap[1] + jQuery.htmlPrefilter(elem) + wrap[2]; // Descend through wrappers to the right content
	j = wrap[0];while(j--) {tmp = tmp.lastChild;} // Support: Android<4.1, PhantomJS<2
	// push.apply(_, arraylike) throws on ancient WebKit
	jQuery.merge(nodes,tmp.childNodes); // Remember the top-level container
	tmp = fragment.firstChild; // Ensure the created nodes are orphaned (#12392)
	tmp.textContent = "";}}} // Remove wrapper from fragment
	fragment.textContent = "";i = 0;while(elem = nodes[i++]) { // Skip elements already in the context collection (trac-4087)
	if(selection && jQuery.inArray(elem,selection) > -1){if(ignored){ignored.push(elem);}continue;}contains = jQuery.contains(elem.ownerDocument,elem); // Append to fragment
	tmp = getAll(fragment.appendChild(elem),"script"); // Preserve script evaluation history
	if(contains){setGlobalEval(tmp);} // Capture executables
	if(scripts){j = 0;while(elem = tmp[j++]) {if(rscriptType.test(elem.type || "")){scripts.push(elem);}}}}return fragment;}(function(){var fragment=document.createDocumentFragment(),div=fragment.appendChild(document.createElement("div")),input=document.createElement("input"); // Support: Android 4.0-4.3, Safari<=5.1
	// Check state lost if the name is set (#11217)
	// Support: Windows Web Apps (WWA)
	// `name` and `type` must use .setAttribute for WWA (#14901)
	input.setAttribute("type","radio");input.setAttribute("checked","checked");input.setAttribute("name","t");div.appendChild(input); // Support: Safari<=5.1, Android<4.2
	// Older WebKit doesn't clone checked state correctly in fragments
	support.checkClone = div.cloneNode(true).cloneNode(true).lastChild.checked; // Support: IE<=11+
	// Make sure textarea (and checkbox) defaultValue is properly cloned
	div.innerHTML = "<textarea>x</textarea>";support.noCloneChecked = !!div.cloneNode(true).lastChild.defaultValue;})();var rkeyEvent=/^key/,rmouseEvent=/^(?:mouse|pointer|contextmenu|drag|drop)|click/,rtypenamespace=/^([^.]*)(?:\.(.+)|)/;function returnTrue(){return true;}function returnFalse(){return false;} // Support: IE9
	// See #13393 for more info
	function safeActiveElement(){try{return document.activeElement;}catch(err) {}}function _on(elem,types,selector,data,fn,one){var origFn,type; // Types can be a map of types/handlers
	if(typeof types === "object"){ // ( types-Object, selector, data )
	if(typeof selector !== "string"){ // ( types-Object, data )
	data = data || selector;selector = undefined;}for(type in types) {_on(elem,type,selector,data,types[type],one);}return elem;}if(data == null && fn == null){ // ( types, fn )
	fn = selector;data = selector = undefined;}else if(fn == null){if(typeof selector === "string"){ // ( types, selector, fn )
	fn = data;data = undefined;}else { // ( types, data, fn )
	fn = data;data = selector;selector = undefined;}}if(fn === false){fn = returnFalse;}else if(!fn){return this;}if(one === 1){origFn = fn;fn = function(event){ // Can use an empty set, since event contains the info
	jQuery().off(event);return origFn.apply(this,arguments);}; // Use same guid so caller can remove using origFn
	fn.guid = origFn.guid || (origFn.guid = jQuery.guid++);}return elem.each(function(){jQuery.event.add(this,types,fn,data,selector);});} /*
	 * Helper functions for managing events -- not part of the public interface.
	 * Props to Dean Edwards' addEvent library for many of the ideas.
	 */jQuery.event = {global:{},add:function add(elem,types,handler,data,selector){var handleObjIn,eventHandle,tmp,events,t,handleObj,special,handlers,type,namespaces,origType,elemData=dataPriv.get(elem); // Don't attach events to noData or text/comment nodes (but allow plain objects)
	if(!elemData){return;} // Caller can pass in an object of custom data in lieu of the handler
	if(handler.handler){handleObjIn = handler;handler = handleObjIn.handler;selector = handleObjIn.selector;} // Make sure that the handler has a unique ID, used to find/remove it later
	if(!handler.guid){handler.guid = jQuery.guid++;} // Init the element's event structure and main handler, if this is the first
	if(!(events = elemData.events)){events = elemData.events = {};}if(!(eventHandle = elemData.handle)){eventHandle = elemData.handle = function(e){ // Discard the second event of a jQuery.event.trigger() and
	// when an event is called after a page has unloaded
	return typeof jQuery !== "undefined" && jQuery.event.triggered !== e.type?jQuery.event.dispatch.apply(elem,arguments):undefined;};} // Handle multiple events separated by a space
	types = (types || "").match(rnotwhite) || [""];t = types.length;while(t--) {tmp = rtypenamespace.exec(types[t]) || [];type = origType = tmp[1];namespaces = (tmp[2] || "").split(".").sort(); // There *must* be a type, no attaching namespace-only handlers
	if(!type){continue;} // If event changes its type, use the special event handlers for the changed type
	special = jQuery.event.special[type] || {}; // If selector defined, determine special event api type, otherwise given type
	type = (selector?special.delegateType:special.bindType) || type; // Update special based on newly reset type
	special = jQuery.event.special[type] || {}; // handleObj is passed to all event handlers
	handleObj = jQuery.extend({type:type,origType:origType,data:data,handler:handler,guid:handler.guid,selector:selector,needsContext:selector && jQuery.expr.match.needsContext.test(selector),namespace:namespaces.join(".")},handleObjIn); // Init the event handler queue if we're the first
	if(!(handlers = events[type])){handlers = events[type] = [];handlers.delegateCount = 0; // Only use addEventListener if the special events handler returns false
	if(!special.setup || special.setup.call(elem,data,namespaces,eventHandle) === false){if(elem.addEventListener){elem.addEventListener(type,eventHandle);}}}if(special.add){special.add.call(elem,handleObj);if(!handleObj.handler.guid){handleObj.handler.guid = handler.guid;}} // Add to the element's handler list, delegates in front
	if(selector){handlers.splice(handlers.delegateCount++,0,handleObj);}else {handlers.push(handleObj);} // Keep track of which events have ever been used, for event optimization
	jQuery.event.global[type] = true;}}, // Detach an event or set of events from an element
	remove:function remove(elem,types,handler,selector,mappedTypes){var j,origCount,tmp,events,t,handleObj,special,handlers,type,namespaces,origType,elemData=dataPriv.hasData(elem) && dataPriv.get(elem);if(!elemData || !(events = elemData.events)){return;} // Once for each type.namespace in types; type may be omitted
	types = (types || "").match(rnotwhite) || [""];t = types.length;while(t--) {tmp = rtypenamespace.exec(types[t]) || [];type = origType = tmp[1];namespaces = (tmp[2] || "").split(".").sort(); // Unbind all events (on this namespace, if provided) for the element
	if(!type){for(type in events) {jQuery.event.remove(elem,type + types[t],handler,selector,true);}continue;}special = jQuery.event.special[type] || {};type = (selector?special.delegateType:special.bindType) || type;handlers = events[type] || [];tmp = tmp[2] && new RegExp("(^|\\.)" + namespaces.join("\\.(?:.*\\.|)") + "(\\.|$)"); // Remove matching events
	origCount = j = handlers.length;while(j--) {handleObj = handlers[j];if((mappedTypes || origType === handleObj.origType) && (!handler || handler.guid === handleObj.guid) && (!tmp || tmp.test(handleObj.namespace)) && (!selector || selector === handleObj.selector || selector === "**" && handleObj.selector)){handlers.splice(j,1);if(handleObj.selector){handlers.delegateCount--;}if(special.remove){special.remove.call(elem,handleObj);}}} // Remove generic event handler if we removed something and no more handlers exist
	// (avoids potential for endless recursion during removal of special event handlers)
	if(origCount && !handlers.length){if(!special.teardown || special.teardown.call(elem,namespaces,elemData.handle) === false){jQuery.removeEvent(elem,type,elemData.handle);}delete events[type];}} // Remove data and the expando if it's no longer used
	if(jQuery.isEmptyObject(events)){dataPriv.remove(elem,"handle events");}},dispatch:function dispatch(event){ // Make a writable jQuery.Event from the native event object
	event = jQuery.event.fix(event);var i,j,ret,matched,handleObj,handlerQueue=[],args=_slice.call(arguments),handlers=(dataPriv.get(this,"events") || {})[event.type] || [],special=jQuery.event.special[event.type] || {}; // Use the fix-ed jQuery.Event rather than the (read-only) native event
	args[0] = event;event.delegateTarget = this; // Call the preDispatch hook for the mapped type, and let it bail if desired
	if(special.preDispatch && special.preDispatch.call(this,event) === false){return;} // Determine handlers
	handlerQueue = jQuery.event.handlers.call(this,event,handlers); // Run delegates first; they may want to stop propagation beneath us
	i = 0;while((matched = handlerQueue[i++]) && !event.isPropagationStopped()) {event.currentTarget = matched.elem;j = 0;while((handleObj = matched.handlers[j++]) && !event.isImmediatePropagationStopped()) { // Triggered event must either 1) have no namespace, or 2) have namespace(s)
	// a subset or equal to those in the bound event (both can have no namespace).
	if(!event.rnamespace || event.rnamespace.test(handleObj.namespace)){event.handleObj = handleObj;event.data = handleObj.data;ret = ((jQuery.event.special[handleObj.origType] || {}).handle || handleObj.handler).apply(matched.elem,args);if(ret !== undefined){if((event.result = ret) === false){event.preventDefault();event.stopPropagation();}}}}} // Call the postDispatch hook for the mapped type
	if(special.postDispatch){special.postDispatch.call(this,event);}return event.result;},handlers:function handlers(event,_handlers){var i,matches,sel,handleObj,handlerQueue=[],delegateCount=_handlers.delegateCount,cur=event.target; // Support (at least): Chrome, IE9
	// Find delegate handlers
	// Black-hole SVG <use> instance trees (#13180)
	//
	// Support: Firefox<=42+
	// Avoid non-left-click in FF but don't block IE radio events (#3861, gh-2343)
	if(delegateCount && cur.nodeType && (event.type !== "click" || isNaN(event.button) || event.button < 1)){for(;cur !== this;cur = cur.parentNode || this) { // Don't check non-elements (#13208)
	// Don't process clicks on disabled elements (#6911, #8165, #11382, #11764)
	if(cur.nodeType === 1 && (cur.disabled !== true || event.type !== "click")){matches = [];for(i = 0;i < delegateCount;i++) {handleObj = _handlers[i]; // Don't conflict with Object.prototype properties (#13203)
	sel = handleObj.selector + " ";if(matches[sel] === undefined){matches[sel] = handleObj.needsContext?jQuery(sel,this).index(cur) > -1:jQuery.find(sel,this,null,[cur]).length;}if(matches[sel]){matches.push(handleObj);}}if(matches.length){handlerQueue.push({elem:cur,handlers:matches});}}}} // Add the remaining (directly-bound) handlers
	if(delegateCount < _handlers.length){handlerQueue.push({elem:this,handlers:_handlers.slice(delegateCount)});}return handlerQueue;}, // Includes some event props shared by KeyEvent and MouseEvent
	props:("altKey bubbles cancelable ctrlKey currentTarget detail eventPhase " + "metaKey relatedTarget shiftKey target timeStamp view which").split(" "),fixHooks:{},keyHooks:{props:"char charCode key keyCode".split(" "),filter:function filter(event,original){ // Add which for key events
	if(event.which == null){event.which = original.charCode != null?original.charCode:original.keyCode;}return event;}},mouseHooks:{props:("button buttons clientX clientY offsetX offsetY pageX pageY " + "screenX screenY toElement").split(" "),filter:function filter(event,original){var eventDoc,doc,body,button=original.button; // Calculate pageX/Y if missing and clientX/Y available
	if(event.pageX == null && original.clientX != null){eventDoc = event.target.ownerDocument || document;doc = eventDoc.documentElement;body = eventDoc.body;event.pageX = original.clientX + (doc && doc.scrollLeft || body && body.scrollLeft || 0) - (doc && doc.clientLeft || body && body.clientLeft || 0);event.pageY = original.clientY + (doc && doc.scrollTop || body && body.scrollTop || 0) - (doc && doc.clientTop || body && body.clientTop || 0);} // Add which for click: 1 === left; 2 === middle; 3 === right
	// Note: button is not normalized, so don't use it
	if(!event.which && button !== undefined){event.which = button & 1?1:button & 2?3:button & 4?2:0;}return event;}},fix:function fix(event){if(event[jQuery.expando]){return event;} // Create a writable copy of the event object and normalize some properties
	var i,prop,copy,type=event.type,originalEvent=event,fixHook=this.fixHooks[type];if(!fixHook){this.fixHooks[type] = fixHook = rmouseEvent.test(type)?this.mouseHooks:rkeyEvent.test(type)?this.keyHooks:{};}copy = fixHook.props?this.props.concat(fixHook.props):this.props;event = new jQuery.Event(originalEvent);i = copy.length;while(i--) {prop = copy[i];event[prop] = originalEvent[prop];} // Support: Cordova 2.5 (WebKit) (#13255)
	// All events should have a target; Cordova deviceready doesn't
	if(!event.target){event.target = document;} // Support: Safari 6.0+, Chrome<28
	// Target should not be a text node (#504, #13143)
	if(event.target.nodeType === 3){event.target = event.target.parentNode;}return fixHook.filter?fixHook.filter(event,originalEvent):event;},special:{load:{ // Prevent triggered image.load events from bubbling to window.load
	noBubble:true},focus:{ // Fire native event if possible so blur/focus sequence is correct
	trigger:function trigger(){if(this !== safeActiveElement() && this.focus){this.focus();return false;}},delegateType:"focusin"},blur:{trigger:function trigger(){if(this === safeActiveElement() && this.blur){this.blur();return false;}},delegateType:"focusout"},click:{ // For checkbox, fire native event so checked state will be right
	trigger:function trigger(){if(this.type === "checkbox" && this.click && jQuery.nodeName(this,"input")){this.click();return false;}}, // For cross-browser consistency, don't fire native .click() on links
	_default:function _default(event){return jQuery.nodeName(event.target,"a");}},beforeunload:{postDispatch:function postDispatch(event){ // Support: Firefox 20+
	// Firefox doesn't alert if the returnValue field is not set.
	if(event.result !== undefined && event.originalEvent){event.originalEvent.returnValue = event.result;}}}}};jQuery.removeEvent = function(elem,type,handle){ // This "if" is needed for plain objects
	if(elem.removeEventListener){elem.removeEventListener(type,handle);}};jQuery.Event = function(src,props){ // Allow instantiation without the 'new' keyword
	if(!(this instanceof jQuery.Event)){return new jQuery.Event(src,props);} // Event object
	if(src && src.type){this.originalEvent = src;this.type = src.type; // Events bubbling up the document may have been marked as prevented
	// by a handler lower down the tree; reflect the correct value.
	this.isDefaultPrevented = src.defaultPrevented || src.defaultPrevented === undefined &&  // Support: Android<4.0
	src.returnValue === false?returnTrue:returnFalse; // Event type
	}else {this.type = src;} // Put explicitly provided properties onto the event object
	if(props){jQuery.extend(this,props);} // Create a timestamp if incoming event doesn't have one
	this.timeStamp = src && src.timeStamp || jQuery.now(); // Mark it as fixed
	this[jQuery.expando] = true;}; // jQuery.Event is based on DOM3 Events as specified by the ECMAScript Language Binding
	// http://www.w3.org/TR/2003/WD-DOM-Level-3-Events-20030331/ecma-script-binding.html
	jQuery.Event.prototype = {constructor:jQuery.Event,isDefaultPrevented:returnFalse,isPropagationStopped:returnFalse,isImmediatePropagationStopped:returnFalse,preventDefault:function preventDefault(){var e=this.originalEvent;this.isDefaultPrevented = returnTrue;if(e){e.preventDefault();}},stopPropagation:function stopPropagation(){var e=this.originalEvent;this.isPropagationStopped = returnTrue;if(e){e.stopPropagation();}},stopImmediatePropagation:function stopImmediatePropagation(){var e=this.originalEvent;this.isImmediatePropagationStopped = returnTrue;if(e){e.stopImmediatePropagation();}this.stopPropagation();}}; // Create mouseenter/leave events using mouseover/out and event-time checks
	// so that event delegation works in jQuery.
	// Do the same for pointerenter/pointerleave and pointerover/pointerout
	//
	// Support: Safari 7 only
	// Safari sends mouseenter too often; see:
	// https://code.google.com/p/chromium/issues/detail?id=470258
	// for the description of the bug (it existed in older Chrome versions as well).
	jQuery.each({mouseenter:"mouseover",mouseleave:"mouseout",pointerenter:"pointerover",pointerleave:"pointerout"},function(orig,fix){jQuery.event.special[orig] = {delegateType:fix,bindType:fix,handle:function handle(event){var ret,target=this,related=event.relatedTarget,handleObj=event.handleObj; // For mouseenter/leave call the handler if related is outside the target.
	// NB: No relatedTarget if the mouse left/entered the browser window
	if(!related || related !== target && !jQuery.contains(target,related)){event.type = handleObj.origType;ret = handleObj.handler.apply(this,arguments);event.type = fix;}return ret;}};});jQuery.fn.extend({on:function on(types,selector,data,fn){return _on(this,types,selector,data,fn);},one:function one(types,selector,data,fn){return _on(this,types,selector,data,fn,1);},off:function off(types,selector,fn){var handleObj,type;if(types && types.preventDefault && types.handleObj){ // ( event )  dispatched jQuery.Event
	handleObj = types.handleObj;jQuery(types.delegateTarget).off(handleObj.namespace?handleObj.origType + "." + handleObj.namespace:handleObj.origType,handleObj.selector,handleObj.handler);return this;}if(typeof types === "object"){ // ( types-object [, selector] )
	for(type in types) {this.off(type,selector,types[type]);}return this;}if(selector === false || typeof selector === "function"){ // ( types [, fn] )
	fn = selector;selector = undefined;}if(fn === false){fn = returnFalse;}return this.each(function(){jQuery.event.remove(this,types,fn,selector);});}});var rxhtmlTag=/<(?!area|br|col|embed|hr|img|input|link|meta|param)(([\w:-]+)[^>]*)\/>/gi, // Support: IE 10-11, Edge 10240+
	// In IE/Edge using regex groups here causes severe slowdowns.
	// See https://connect.microsoft.com/IE/feedback/details/1736512/
	rnoInnerhtml=/<script|<style|<link/i, // checked="checked" or checked
	rchecked=/checked\s*(?:[^=]|=\s*.checked.)/i,rscriptTypeMasked=/^true\/(.*)/,rcleanScript=/^\s*<!(?:\[CDATA\[|--)|(?:\]\]|--)>\s*$/g;function manipulationTarget(elem,content){if(jQuery.nodeName(elem,"table") && jQuery.nodeName(content.nodeType !== 11?content:content.firstChild,"tr")){return elem.getElementsByTagName("tbody")[0] || elem;}return elem;} // Replace/restore the type attribute of script elements for safe DOM manipulation
	function disableScript(elem){elem.type = (elem.getAttribute("type") !== null) + "/" + elem.type;return elem;}function restoreScript(elem){var match=rscriptTypeMasked.exec(elem.type);if(match){elem.type = match[1];}else {elem.removeAttribute("type");}return elem;}function cloneCopyEvent(src,dest){var i,l,type,pdataOld,pdataCur,udataOld,udataCur,events;if(dest.nodeType !== 1){return;} // 1. Copy private data: events, handlers, etc.
	if(dataPriv.hasData(src)){pdataOld = dataPriv.access(src);pdataCur = dataPriv.set(dest,pdataOld);events = pdataOld.events;if(events){delete pdataCur.handle;pdataCur.events = {};for(type in events) {for(i = 0,l = events[type].length;i < l;i++) {jQuery.event.add(dest,type,events[type][i]);}}}} // 2. Copy user data
	if(dataUser.hasData(src)){udataOld = dataUser.access(src);udataCur = jQuery.extend({},udataOld);dataUser.set(dest,udataCur);}} // Fix IE bugs, see support tests
	function fixInput(src,dest){var nodeName=dest.nodeName.toLowerCase(); // Fails to persist the checked state of a cloned checkbox or radio button.
	if(nodeName === "input" && rcheckableType.test(src.type)){dest.checked = src.checked; // Fails to return the selected option to the default selected state when cloning options
	}else if(nodeName === "input" || nodeName === "textarea"){dest.defaultValue = src.defaultValue;}}function domManip(collection,args,callback,ignored){ // Flatten any nested arrays
	args = concat.apply([],args);var fragment,first,scripts,hasScripts,node,doc,i=0,l=collection.length,iNoClone=l - 1,value=args[0],isFunction=jQuery.isFunction(value); // We can't cloneNode fragments that contain checked, in WebKit
	if(isFunction || l > 1 && typeof value === "string" && !support.checkClone && rchecked.test(value)){return collection.each(function(index){var self=collection.eq(index);if(isFunction){args[0] = value.call(this,index,self.html());}domManip(self,args,callback,ignored);});}if(l){fragment = buildFragment(args,collection[0].ownerDocument,false,collection,ignored);first = fragment.firstChild;if(fragment.childNodes.length === 1){fragment = first;} // Require either new content or an interest in ignored elements to invoke the callback
	if(first || ignored){scripts = jQuery.map(getAll(fragment,"script"),disableScript);hasScripts = scripts.length; // Use the original fragment for the last item
	// instead of the first because it can end up
	// being emptied incorrectly in certain situations (#8070).
	for(;i < l;i++) {node = fragment;if(i !== iNoClone){node = jQuery.clone(node,true,true); // Keep references to cloned scripts for later restoration
	if(hasScripts){ // Support: Android<4.1, PhantomJS<2
	// push.apply(_, arraylike) throws on ancient WebKit
	jQuery.merge(scripts,getAll(node,"script"));}}callback.call(collection[i],node,i);}if(hasScripts){doc = scripts[scripts.length - 1].ownerDocument; // Reenable scripts
	jQuery.map(scripts,restoreScript); // Evaluate executable scripts on first document insertion
	for(i = 0;i < hasScripts;i++) {node = scripts[i];if(rscriptType.test(node.type || "") && !dataPriv.access(node,"globalEval") && jQuery.contains(doc,node)){if(node.src){ // Optional AJAX dependency, but won't run scripts if not present
	if(jQuery._evalUrl){jQuery._evalUrl(node.src);}}else {jQuery.globalEval(node.textContent.replace(rcleanScript,""));}}}}}}return collection;}function _remove(elem,selector,keepData){var node,nodes=selector?jQuery.filter(selector,elem):elem,i=0;for(;(node = nodes[i]) != null;i++) {if(!keepData && node.nodeType === 1){jQuery.cleanData(getAll(node));}if(node.parentNode){if(keepData && jQuery.contains(node.ownerDocument,node)){setGlobalEval(getAll(node,"script"));}node.parentNode.removeChild(node);}}return elem;}jQuery.extend({htmlPrefilter:function htmlPrefilter(html){return html.replace(rxhtmlTag,"<$1></$2>");},clone:function clone(elem,dataAndEvents,deepDataAndEvents){var i,l,srcElements,destElements,clone=elem.cloneNode(true),inPage=jQuery.contains(elem.ownerDocument,elem); // Fix IE cloning issues
	if(!support.noCloneChecked && (elem.nodeType === 1 || elem.nodeType === 11) && !jQuery.isXMLDoc(elem)){ // We eschew Sizzle here for performance reasons: http://jsperf.com/getall-vs-sizzle/2
	destElements = getAll(clone);srcElements = getAll(elem);for(i = 0,l = srcElements.length;i < l;i++) {fixInput(srcElements[i],destElements[i]);}} // Copy the events from the original to the clone
	if(dataAndEvents){if(deepDataAndEvents){srcElements = srcElements || getAll(elem);destElements = destElements || getAll(clone);for(i = 0,l = srcElements.length;i < l;i++) {cloneCopyEvent(srcElements[i],destElements[i]);}}else {cloneCopyEvent(elem,clone);}} // Preserve script evaluation history
	destElements = getAll(clone,"script");if(destElements.length > 0){setGlobalEval(destElements,!inPage && getAll(elem,"script"));} // Return the cloned set
	return clone;},cleanData:function cleanData(elems){var data,elem,type,special=jQuery.event.special,i=0;for(;(elem = elems[i]) !== undefined;i++) {if(acceptData(elem)){if(data = elem[dataPriv.expando]){if(data.events){for(type in data.events) {if(special[type]){jQuery.event.remove(elem,type); // This is a shortcut to avoid jQuery.event.remove's overhead
	}else {jQuery.removeEvent(elem,type,data.handle);}}} // Support: Chrome <= 35-45+
	// Assign undefined instead of using delete, see Data#remove
	elem[dataPriv.expando] = undefined;}if(elem[dataUser.expando]){ // Support: Chrome <= 35-45+
	// Assign undefined instead of using delete, see Data#remove
	elem[dataUser.expando] = undefined;}}}}});jQuery.fn.extend({ // Keep domManip exposed until 3.0 (gh-2225)
	domManip:domManip,detach:function detach(selector){return _remove(this,selector,true);},remove:function remove(selector){return _remove(this,selector);},text:function text(value){return access(this,function(value){return value === undefined?jQuery.text(this):this.empty().each(function(){if(this.nodeType === 1 || this.nodeType === 11 || this.nodeType === 9){this.textContent = value;}});},null,value,arguments.length);},append:function append(){return domManip(this,arguments,function(elem){if(this.nodeType === 1 || this.nodeType === 11 || this.nodeType === 9){var target=manipulationTarget(this,elem);target.appendChild(elem);}});},prepend:function prepend(){return domManip(this,arguments,function(elem){if(this.nodeType === 1 || this.nodeType === 11 || this.nodeType === 9){var target=manipulationTarget(this,elem);target.insertBefore(elem,target.firstChild);}});},before:function before(){return domManip(this,arguments,function(elem){if(this.parentNode){this.parentNode.insertBefore(elem,this);}});},after:function after(){return domManip(this,arguments,function(elem){if(this.parentNode){this.parentNode.insertBefore(elem,this.nextSibling);}});},empty:function empty(){var elem,i=0;for(;(elem = this[i]) != null;i++) {if(elem.nodeType === 1){ // Prevent memory leaks
	jQuery.cleanData(getAll(elem,false)); // Remove any remaining nodes
	elem.textContent = "";}}return this;},clone:function clone(dataAndEvents,deepDataAndEvents){dataAndEvents = dataAndEvents == null?false:dataAndEvents;deepDataAndEvents = deepDataAndEvents == null?dataAndEvents:deepDataAndEvents;return this.map(function(){return jQuery.clone(this,dataAndEvents,deepDataAndEvents);});},html:function html(value){return access(this,function(value){var elem=this[0] || {},i=0,l=this.length;if(value === undefined && elem.nodeType === 1){return elem.innerHTML;} // See if we can take a shortcut and just use innerHTML
	if(typeof value === "string" && !rnoInnerhtml.test(value) && !wrapMap[(rtagName.exec(value) || ["",""])[1].toLowerCase()]){value = jQuery.htmlPrefilter(value);try{for(;i < l;i++) {elem = this[i] || {}; // Remove element nodes and prevent memory leaks
	if(elem.nodeType === 1){jQuery.cleanData(getAll(elem,false));elem.innerHTML = value;}}elem = 0; // If using innerHTML throws an exception, use the fallback method
	}catch(e) {}}if(elem){this.empty().append(value);}},null,value,arguments.length);},replaceWith:function replaceWith(){var ignored=[]; // Make the changes, replacing each non-ignored context element with the new content
	return domManip(this,arguments,function(elem){var parent=this.parentNode;if(jQuery.inArray(this,ignored) < 0){jQuery.cleanData(getAll(this));if(parent){parent.replaceChild(elem,this);}} // Force callback invocation
	},ignored);}});jQuery.each({appendTo:"append",prependTo:"prepend",insertBefore:"before",insertAfter:"after",replaceAll:"replaceWith"},function(name,original){jQuery.fn[name] = function(selector){var elems,ret=[],insert=jQuery(selector),last=insert.length - 1,i=0;for(;i <= last;i++) {elems = i === last?this:this.clone(true);jQuery(insert[i])[original](elems); // Support: QtWebKit
	// .get() because push.apply(_, arraylike) throws
	push.apply(ret,elems.get());}return this.pushStack(ret);};});var iframe,elemdisplay={ // Support: Firefox
	// We have to pre-define these values for FF (#10227)
	HTML:"block",BODY:"block"}; /**
	 * Retrieve the actual display of a element
	 * @param {String} name nodeName of the element
	 * @param {Object} doc Document object
	 */ // Called only from within defaultDisplay
	function actualDisplay(name,doc){var elem=jQuery(doc.createElement(name)).appendTo(doc.body),display=jQuery.css(elem[0],"display"); // We don't have any data stored on the element,
	// so use "detach" method as fast way to get rid of the element
	elem.detach();return display;} /**
	 * Try to determine the default display value of an element
	 * @param {String} nodeName
	 */function defaultDisplay(nodeName){var doc=document,display=elemdisplay[nodeName];if(!display){display = actualDisplay(nodeName,doc); // If the simple way fails, read from inside an iframe
	if(display === "none" || !display){ // Use the already-created iframe if possible
	iframe = (iframe || jQuery("<iframe frameborder='0' width='0' height='0'/>")).appendTo(doc.documentElement); // Always write a new HTML skeleton so Webkit and Firefox don't choke on reuse
	doc = iframe[0].contentDocument; // Support: IE
	doc.write();doc.close();display = actualDisplay(nodeName,doc);iframe.detach();} // Store the correct default display
	elemdisplay[nodeName] = display;}return display;}var rmargin=/^margin/;var rnumnonpx=new RegExp("^(" + pnum + ")(?!px)[a-z%]+$","i");var getStyles=function getStyles(elem){ // Support: IE<=11+, Firefox<=30+ (#15098, #14150)
	// IE throws on elements created in popups
	// FF meanwhile throws on frame elements through "defaultView.getComputedStyle"
	var view=elem.ownerDocument.defaultView;if(!view.opener){view = window;}return view.getComputedStyle(elem);};var swap=function swap(elem,options,callback,args){var ret,name,old={}; // Remember the old values, and insert the new ones
	for(name in options) {old[name] = elem.style[name];elem.style[name] = options[name];}ret = callback.apply(elem,args || []); // Revert the old values
	for(name in options) {elem.style[name] = old[name];}return ret;};var documentElement=document.documentElement;(function(){var pixelPositionVal,boxSizingReliableVal,pixelMarginRightVal,reliableMarginLeftVal,container=document.createElement("div"),div=document.createElement("div"); // Finish early in limited (non-browser) environments
	if(!div.style){return;} // Support: IE9-11+
	// Style of cloned element affects source element cloned (#8908)
	div.style.backgroundClip = "content-box";div.cloneNode(true).style.backgroundClip = "";support.clearCloneStyle = div.style.backgroundClip === "content-box";container.style.cssText = "border:0;width:8px;height:0;top:0;left:-9999px;" + "padding:0;margin-top:1px;position:absolute";container.appendChild(div); // Executing both pixelPosition & boxSizingReliable tests require only one layout
	// so they're executed at the same time to save the second computation.
	function computeStyleTests(){div.style.cssText =  // Support: Firefox<29, Android 2.3
	// Vendor-prefix box-sizing
	"-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box;" + "position:relative;display:block;" + "margin:auto;border:1px;padding:1px;" + "top:1%;width:50%";div.innerHTML = "";documentElement.appendChild(container);var divStyle=window.getComputedStyle(div);pixelPositionVal = divStyle.top !== "1%";reliableMarginLeftVal = divStyle.marginLeft === "2px";boxSizingReliableVal = divStyle.width === "4px"; // Support: Android 4.0 - 4.3 only
	// Some styles come back with percentage values, even though they shouldn't
	div.style.marginRight = "50%";pixelMarginRightVal = divStyle.marginRight === "4px";documentElement.removeChild(container);}jQuery.extend(support,{pixelPosition:function pixelPosition(){ // This test is executed only once but we still do memoizing
	// since we can use the boxSizingReliable pre-computing.
	// No need to check if the test was already performed, though.
	computeStyleTests();return pixelPositionVal;},boxSizingReliable:function boxSizingReliable(){if(boxSizingReliableVal == null){computeStyleTests();}return boxSizingReliableVal;},pixelMarginRight:function pixelMarginRight(){ // Support: Android 4.0-4.3
	// We're checking for boxSizingReliableVal here instead of pixelMarginRightVal
	// since that compresses better and they're computed together anyway.
	if(boxSizingReliableVal == null){computeStyleTests();}return pixelMarginRightVal;},reliableMarginLeft:function reliableMarginLeft(){ // Support: IE <=8 only, Android 4.0 - 4.3 only, Firefox <=3 - 37
	if(boxSizingReliableVal == null){computeStyleTests();}return reliableMarginLeftVal;},reliableMarginRight:function reliableMarginRight(){ // Support: Android 2.3
	// Check if div with explicit width and no margin-right incorrectly
	// gets computed margin-right based on width of container. (#3333)
	// WebKit Bug 13343 - getComputedStyle returns wrong value for margin-right
	// This support function is only executed once so no memoizing is needed.
	var ret,marginDiv=div.appendChild(document.createElement("div")); // Reset CSS: box-sizing; display; margin; border; padding
	marginDiv.style.cssText = div.style.cssText =  // Support: Android 2.3
	// Vendor-prefix box-sizing
	"-webkit-box-sizing:content-box;box-sizing:content-box;" + "display:block;margin:0;border:0;padding:0";marginDiv.style.marginRight = marginDiv.style.width = "0";div.style.width = "1px";documentElement.appendChild(container);ret = !parseFloat(window.getComputedStyle(marginDiv).marginRight);documentElement.removeChild(container);div.removeChild(marginDiv);return ret;}});})();function curCSS(elem,name,computed){var width,minWidth,maxWidth,ret,style=elem.style;computed = computed || getStyles(elem); // Support: IE9
	// getPropertyValue is only needed for .css('filter') (#12537)
	if(computed){ret = computed.getPropertyValue(name) || computed[name];if(ret === "" && !jQuery.contains(elem.ownerDocument,elem)){ret = jQuery.style(elem,name);} // A tribute to the "awesome hack by Dean Edwards"
	// Android Browser returns percentage for some values,
	// but width seems to be reliably pixels.
	// This is against the CSSOM draft spec:
	// http://dev.w3.org/csswg/cssom/#resolved-values
	if(!support.pixelMarginRight() && rnumnonpx.test(ret) && rmargin.test(name)){ // Remember the original values
	width = style.width;minWidth = style.minWidth;maxWidth = style.maxWidth; // Put in the new values to get a computed value out
	style.minWidth = style.maxWidth = style.width = ret;ret = computed.width; // Revert the changed values
	style.width = width;style.minWidth = minWidth;style.maxWidth = maxWidth;}}return ret !== undefined? // Support: IE9-11+
	// IE returns zIndex value as an integer.
	ret + "":ret;}function addGetHookIf(conditionFn,hookFn){ // Define the hook, we'll check on the first run if it's really needed.
	return {get:function get(){if(conditionFn()){ // Hook not needed (or it's not possible to use it due
	// to missing dependency), remove it.
	delete this.get;return;} // Hook needed; redefine it so that the support test is not executed again.
	return (this.get = hookFn).apply(this,arguments);}};}var  // Swappable if display is none or starts with table
	// except "table", "table-cell", or "table-caption"
	// See here for display values: https://developer.mozilla.org/en-US/docs/CSS/display
	rdisplayswap=/^(none|table(?!-c[ea]).+)/,cssShow={position:"absolute",visibility:"hidden",display:"block"},cssNormalTransform={letterSpacing:"0",fontWeight:"400"},cssPrefixes=["Webkit","O","Moz","ms"],emptyStyle=document.createElement("div").style; // Return a css property mapped to a potentially vendor prefixed property
	function vendorPropName(name){ // Shortcut for names that are not vendor prefixed
	if(name in emptyStyle){return name;} // Check for vendor prefixed names
	var capName=name[0].toUpperCase() + name.slice(1),i=cssPrefixes.length;while(i--) {name = cssPrefixes[i] + capName;if(name in emptyStyle){return name;}}}function setPositiveNumber(elem,value,subtract){ // Any relative (+/-) values have already been
	// normalized at this point
	var matches=rcssNum.exec(value);return matches? // Guard against undefined "subtract", e.g., when used as in cssHooks
	Math.max(0,matches[2] - (subtract || 0)) + (matches[3] || "px"):value;}function augmentWidthOrHeight(elem,name,extra,isBorderBox,styles){var i=extra === (isBorderBox?"border":"content")? // If we already have the right measurement, avoid augmentation
	4: // Otherwise initialize for horizontal or vertical properties
	name === "width"?1:0,val=0;for(;i < 4;i += 2) { // Both box models exclude margin, so add it if we want it
	if(extra === "margin"){val += jQuery.css(elem,extra + cssExpand[i],true,styles);}if(isBorderBox){ // border-box includes padding, so remove it if we want content
	if(extra === "content"){val -= jQuery.css(elem,"padding" + cssExpand[i],true,styles);} // At this point, extra isn't border nor margin, so remove border
	if(extra !== "margin"){val -= jQuery.css(elem,"border" + cssExpand[i] + "Width",true,styles);}}else { // At this point, extra isn't content, so add padding
	val += jQuery.css(elem,"padding" + cssExpand[i],true,styles); // At this point, extra isn't content nor padding, so add border
	if(extra !== "padding"){val += jQuery.css(elem,"border" + cssExpand[i] + "Width",true,styles);}}}return val;}function getWidthOrHeight(elem,name,extra){ // Start with offset property, which is equivalent to the border-box value
	var valueIsBorderBox=true,val=name === "width"?elem.offsetWidth:elem.offsetHeight,styles=getStyles(elem),isBorderBox=jQuery.css(elem,"boxSizing",false,styles) === "border-box"; // Support: IE11 only
	// In IE 11 fullscreen elements inside of an iframe have
	// 100x too small dimensions (gh-1764).
	if(document.msFullscreenElement && window.top !== window){ // Support: IE11 only
	// Running getBoundingClientRect on a disconnected node
	// in IE throws an error.
	if(elem.getClientRects().length){val = Math.round(elem.getBoundingClientRect()[name] * 100);}} // Some non-html elements return undefined for offsetWidth, so check for null/undefined
	// svg - https://bugzilla.mozilla.org/show_bug.cgi?id=649285
	// MathML - https://bugzilla.mozilla.org/show_bug.cgi?id=491668
	if(val <= 0 || val == null){ // Fall back to computed then uncomputed css if necessary
	val = curCSS(elem,name,styles);if(val < 0 || val == null){val = elem.style[name];} // Computed unit is not pixels. Stop here and return.
	if(rnumnonpx.test(val)){return val;} // Check for style in case a browser which returns unreliable values
	// for getComputedStyle silently falls back to the reliable elem.style
	valueIsBorderBox = isBorderBox && (support.boxSizingReliable() || val === elem.style[name]); // Normalize "", auto, and prepare for extra
	val = parseFloat(val) || 0;} // Use the active box-sizing model to add/subtract irrelevant styles
	return val + augmentWidthOrHeight(elem,name,extra || (isBorderBox?"border":"content"),valueIsBorderBox,styles) + "px";}function showHide(elements,show){var display,elem,hidden,values=[],index=0,length=elements.length;for(;index < length;index++) {elem = elements[index];if(!elem.style){continue;}values[index] = dataPriv.get(elem,"olddisplay");display = elem.style.display;if(show){ // Reset the inline display of this element to learn if it is
	// being hidden by cascaded rules or not
	if(!values[index] && display === "none"){elem.style.display = "";} // Set elements which have been overridden with display: none
	// in a stylesheet to whatever the default browser style is
	// for such an element
	if(elem.style.display === "" && isHidden(elem)){values[index] = dataPriv.access(elem,"olddisplay",defaultDisplay(elem.nodeName));}}else {hidden = isHidden(elem);if(display !== "none" || !hidden){dataPriv.set(elem,"olddisplay",hidden?display:jQuery.css(elem,"display"));}}} // Set the display of most of the elements in a second loop
	// to avoid the constant reflow
	for(index = 0;index < length;index++) {elem = elements[index];if(!elem.style){continue;}if(!show || elem.style.display === "none" || elem.style.display === ""){elem.style.display = show?values[index] || "":"none";}}return elements;}jQuery.extend({ // Add in style property hooks for overriding the default
	// behavior of getting and setting a style property
	cssHooks:{opacity:{get:function get(elem,computed){if(computed){ // We should always get a number back from opacity
	var ret=curCSS(elem,"opacity");return ret === ""?"1":ret;}}}}, // Don't automatically add "px" to these possibly-unitless properties
	cssNumber:{"animationIterationCount":true,"columnCount":true,"fillOpacity":true,"flexGrow":true,"flexShrink":true,"fontWeight":true,"lineHeight":true,"opacity":true,"order":true,"orphans":true,"widows":true,"zIndex":true,"zoom":true}, // Add in properties whose names you wish to fix before
	// setting or getting the value
	cssProps:{"float":"cssFloat"}, // Get and set the style property on a DOM Node
	style:function style(elem,name,value,extra){ // Don't set styles on text and comment nodes
	if(!elem || elem.nodeType === 3 || elem.nodeType === 8 || !elem.style){return;} // Make sure that we're working with the right name
	var ret,type,hooks,origName=jQuery.camelCase(name),style=elem.style;name = jQuery.cssProps[origName] || (jQuery.cssProps[origName] = vendorPropName(origName) || origName); // Gets hook for the prefixed version, then unprefixed version
	hooks = jQuery.cssHooks[name] || jQuery.cssHooks[origName]; // Check if we're setting a value
	if(value !== undefined){type = typeof value; // Convert "+=" or "-=" to relative numbers (#7345)
	if(type === "string" && (ret = rcssNum.exec(value)) && ret[1]){value = adjustCSS(elem,name,ret); // Fixes bug #9237
	type = "number";} // Make sure that null and NaN values aren't set (#7116)
	if(value == null || value !== value){return;} // If a number was passed in, add the unit (except for certain CSS properties)
	if(type === "number"){value += ret && ret[3] || (jQuery.cssNumber[origName]?"":"px");} // Support: IE9-11+
	// background-* props affect original clone's values
	if(!support.clearCloneStyle && value === "" && name.indexOf("background") === 0){style[name] = "inherit";} // If a hook was provided, use that value, otherwise just set the specified value
	if(!hooks || !("set" in hooks) || (value = hooks.set(elem,value,extra)) !== undefined){style[name] = value;}}else { // If a hook was provided get the non-computed value from there
	if(hooks && "get" in hooks && (ret = hooks.get(elem,false,extra)) !== undefined){return ret;} // Otherwise just get the value from the style object
	return style[name];}},css:function css(elem,name,extra,styles){var val,num,hooks,origName=jQuery.camelCase(name); // Make sure that we're working with the right name
	name = jQuery.cssProps[origName] || (jQuery.cssProps[origName] = vendorPropName(origName) || origName); // Try prefixed name followed by the unprefixed name
	hooks = jQuery.cssHooks[name] || jQuery.cssHooks[origName]; // If a hook was provided get the computed value from there
	if(hooks && "get" in hooks){val = hooks.get(elem,true,extra);} // Otherwise, if a way to get the computed value exists, use that
	if(val === undefined){val = curCSS(elem,name,styles);} // Convert "normal" to computed value
	if(val === "normal" && name in cssNormalTransform){val = cssNormalTransform[name];} // Make numeric if forced or a qualifier was provided and val looks numeric
	if(extra === "" || extra){num = parseFloat(val);return extra === true || isFinite(num)?num || 0:val;}return val;}});jQuery.each(["height","width"],function(i,name){jQuery.cssHooks[name] = {get:function get(elem,computed,extra){if(computed){ // Certain elements can have dimension info if we invisibly show them
	// but it must have a current display style that would benefit
	return rdisplayswap.test(jQuery.css(elem,"display")) && elem.offsetWidth === 0?swap(elem,cssShow,function(){return getWidthOrHeight(elem,name,extra);}):getWidthOrHeight(elem,name,extra);}},set:function set(elem,value,extra){var matches,styles=extra && getStyles(elem),subtract=extra && augmentWidthOrHeight(elem,name,extra,jQuery.css(elem,"boxSizing",false,styles) === "border-box",styles); // Convert to pixels if value adjustment is needed
	if(subtract && (matches = rcssNum.exec(value)) && (matches[3] || "px") !== "px"){elem.style[name] = value;value = jQuery.css(elem,name);}return setPositiveNumber(elem,value,subtract);}};});jQuery.cssHooks.marginLeft = addGetHookIf(support.reliableMarginLeft,function(elem,computed){if(computed){return (parseFloat(curCSS(elem,"marginLeft")) || elem.getBoundingClientRect().left - swap(elem,{marginLeft:0},function(){return elem.getBoundingClientRect().left;})) + "px";}}); // Support: Android 2.3
	jQuery.cssHooks.marginRight = addGetHookIf(support.reliableMarginRight,function(elem,computed){if(computed){return swap(elem,{"display":"inline-block"},curCSS,[elem,"marginRight"]);}}); // These hooks are used by animate to expand properties
	jQuery.each({margin:"",padding:"",border:"Width"},function(prefix,suffix){jQuery.cssHooks[prefix + suffix] = {expand:function expand(value){var i=0,expanded={}, // Assumes a single number if not a string
	parts=typeof value === "string"?value.split(" "):[value];for(;i < 4;i++) {expanded[prefix + cssExpand[i] + suffix] = parts[i] || parts[i - 2] || parts[0];}return expanded;}};if(!rmargin.test(prefix)){jQuery.cssHooks[prefix + suffix].set = setPositiveNumber;}});jQuery.fn.extend({css:function css(name,value){return access(this,function(elem,name,value){var styles,len,map={},i=0;if(jQuery.isArray(name)){styles = getStyles(elem);len = name.length;for(;i < len;i++) {map[name[i]] = jQuery.css(elem,name[i],false,styles);}return map;}return value !== undefined?jQuery.style(elem,name,value):jQuery.css(elem,name);},name,value,arguments.length > 1);},show:function show(){return showHide(this,true);},hide:function hide(){return showHide(this);},toggle:function toggle(state){if(typeof state === "boolean"){return state?this.show():this.hide();}return this.each(function(){if(isHidden(this)){jQuery(this).show();}else {jQuery(this).hide();}});}});function Tween(elem,options,prop,end,easing){return new Tween.prototype.init(elem,options,prop,end,easing);}jQuery.Tween = Tween;Tween.prototype = {constructor:Tween,init:function init(elem,options,prop,end,easing,unit){this.elem = elem;this.prop = prop;this.easing = easing || jQuery.easing._default;this.options = options;this.start = this.now = this.cur();this.end = end;this.unit = unit || (jQuery.cssNumber[prop]?"":"px");},cur:function cur(){var hooks=Tween.propHooks[this.prop];return hooks && hooks.get?hooks.get(this):Tween.propHooks._default.get(this);},run:function run(percent){var eased,hooks=Tween.propHooks[this.prop];if(this.options.duration){this.pos = eased = jQuery.easing[this.easing](percent,this.options.duration * percent,0,1,this.options.duration);}else {this.pos = eased = percent;}this.now = (this.end - this.start) * eased + this.start;if(this.options.step){this.options.step.call(this.elem,this.now,this);}if(hooks && hooks.set){hooks.set(this);}else {Tween.propHooks._default.set(this);}return this;}};Tween.prototype.init.prototype = Tween.prototype;Tween.propHooks = {_default:{get:function get(tween){var result; // Use a property on the element directly when it is not a DOM element,
	// or when there is no matching style property that exists.
	if(tween.elem.nodeType !== 1 || tween.elem[tween.prop] != null && tween.elem.style[tween.prop] == null){return tween.elem[tween.prop];} // Passing an empty string as a 3rd parameter to .css will automatically
	// attempt a parseFloat and fallback to a string if the parse fails.
	// Simple values such as "10px" are parsed to Float;
	// complex values such as "rotate(1rad)" are returned as-is.
	result = jQuery.css(tween.elem,tween.prop,""); // Empty strings, null, undefined and "auto" are converted to 0.
	return !result || result === "auto"?0:result;},set:function set(tween){ // Use step hook for back compat.
	// Use cssHook if its there.
	// Use .style if available and use plain properties where available.
	if(jQuery.fx.step[tween.prop]){jQuery.fx.step[tween.prop](tween);}else if(tween.elem.nodeType === 1 && (tween.elem.style[jQuery.cssProps[tween.prop]] != null || jQuery.cssHooks[tween.prop])){jQuery.style(tween.elem,tween.prop,tween.now + tween.unit);}else {tween.elem[tween.prop] = tween.now;}}}}; // Support: IE9
	// Panic based approach to setting things on disconnected nodes
	Tween.propHooks.scrollTop = Tween.propHooks.scrollLeft = {set:function set(tween){if(tween.elem.nodeType && tween.elem.parentNode){tween.elem[tween.prop] = tween.now;}}};jQuery.easing = {linear:function linear(p){return p;},swing:function swing(p){return 0.5 - Math.cos(p * Math.PI) / 2;},_default:"swing"};jQuery.fx = Tween.prototype.init; // Back Compat <1.8 extension point
	jQuery.fx.step = {};var fxNow,timerId,rfxtypes=/^(?:toggle|show|hide)$/,rrun=/queueHooks$/; // Animations created synchronously will run synchronously
	function createFxNow(){window.setTimeout(function(){fxNow = undefined;});return fxNow = jQuery.now();} // Generate parameters to create a standard animation
	function genFx(type,includeWidth){var which,i=0,attrs={height:type}; // If we include width, step value is 1 to do all cssExpand values,
	// otherwise step value is 2 to skip over Left and Right
	includeWidth = includeWidth?1:0;for(;i < 4;i += 2 - includeWidth) {which = cssExpand[i];attrs["margin" + which] = attrs["padding" + which] = type;}if(includeWidth){attrs.opacity = attrs.width = type;}return attrs;}function createTween(value,prop,animation){var tween,collection=(Animation.tweeners[prop] || []).concat(Animation.tweeners["*"]),index=0,length=collection.length;for(;index < length;index++) {if(tween = collection[index].call(animation,prop,value)){ // We're done with this property
	return tween;}}}function defaultPrefilter(elem,props,opts){ /* jshint validthis: true */var prop,value,toggle,tween,hooks,oldfire,display,checkDisplay,anim=this,orig={},style=elem.style,hidden=elem.nodeType && isHidden(elem),dataShow=dataPriv.get(elem,"fxshow"); // Handle queue: false promises
	if(!opts.queue){hooks = jQuery._queueHooks(elem,"fx");if(hooks.unqueued == null){hooks.unqueued = 0;oldfire = hooks.empty.fire;hooks.empty.fire = function(){if(!hooks.unqueued){oldfire();}};}hooks.unqueued++;anim.always(function(){ // Ensure the complete handler is called before this completes
	anim.always(function(){hooks.unqueued--;if(!jQuery.queue(elem,"fx").length){hooks.empty.fire();}});});} // Height/width overflow pass
	if(elem.nodeType === 1 && ("height" in props || "width" in props)){ // Make sure that nothing sneaks out
	// Record all 3 overflow attributes because IE9-10 do not
	// change the overflow attribute when overflowX and
	// overflowY are set to the same value
	opts.overflow = [style.overflow,style.overflowX,style.overflowY]; // Set display property to inline-block for height/width
	// animations on inline elements that are having width/height animated
	display = jQuery.css(elem,"display"); // Test default display if display is currently "none"
	checkDisplay = display === "none"?dataPriv.get(elem,"olddisplay") || defaultDisplay(elem.nodeName):display;if(checkDisplay === "inline" && jQuery.css(elem,"float") === "none"){style.display = "inline-block";}}if(opts.overflow){style.overflow = "hidden";anim.always(function(){style.overflow = opts.overflow[0];style.overflowX = opts.overflow[1];style.overflowY = opts.overflow[2];});} // show/hide pass
	for(prop in props) {value = props[prop];if(rfxtypes.exec(value)){delete props[prop];toggle = toggle || value === "toggle";if(value === (hidden?"hide":"show")){ // If there is dataShow left over from a stopped hide or show
	// and we are going to proceed with show, we should pretend to be hidden
	if(value === "show" && dataShow && dataShow[prop] !== undefined){hidden = true;}else {continue;}}orig[prop] = dataShow && dataShow[prop] || jQuery.style(elem,prop); // Any non-fx value stops us from restoring the original display value
	}else {display = undefined;}}if(!jQuery.isEmptyObject(orig)){if(dataShow){if("hidden" in dataShow){hidden = dataShow.hidden;}}else {dataShow = dataPriv.access(elem,"fxshow",{});} // Store state if its toggle - enables .stop().toggle() to "reverse"
	if(toggle){dataShow.hidden = !hidden;}if(hidden){jQuery(elem).show();}else {anim.done(function(){jQuery(elem).hide();});}anim.done(function(){var prop;dataPriv.remove(elem,"fxshow");for(prop in orig) {jQuery.style(elem,prop,orig[prop]);}});for(prop in orig) {tween = createTween(hidden?dataShow[prop]:0,prop,anim);if(!(prop in dataShow)){dataShow[prop] = tween.start;if(hidden){tween.end = tween.start;tween.start = prop === "width" || prop === "height"?1:0;}}} // If this is a noop like .hide().hide(), restore an overwritten display value
	}else if((display === "none"?defaultDisplay(elem.nodeName):display) === "inline"){style.display = display;}}function propFilter(props,specialEasing){var index,name,easing,value,hooks; // camelCase, specialEasing and expand cssHook pass
	for(index in props) {name = jQuery.camelCase(index);easing = specialEasing[name];value = props[index];if(jQuery.isArray(value)){easing = value[1];value = props[index] = value[0];}if(index !== name){props[name] = value;delete props[index];}hooks = jQuery.cssHooks[name];if(hooks && "expand" in hooks){value = hooks.expand(value);delete props[name]; // Not quite $.extend, this won't overwrite existing keys.
	// Reusing 'index' because we have the correct "name"
	for(index in value) {if(!(index in props)){props[index] = value[index];specialEasing[index] = easing;}}}else {specialEasing[name] = easing;}}}function Animation(elem,properties,options){var result,stopped,index=0,length=Animation.prefilters.length,deferred=jQuery.Deferred().always(function(){ // Don't match elem in the :animated selector
	delete tick.elem;}),tick=function tick(){if(stopped){return false;}var currentTime=fxNow || createFxNow(),remaining=Math.max(0,animation.startTime + animation.duration - currentTime), // Support: Android 2.3
	// Archaic crash bug won't allow us to use `1 - ( 0.5 || 0 )` (#12497)
	temp=remaining / animation.duration || 0,percent=1 - temp,index=0,length=animation.tweens.length;for(;index < length;index++) {animation.tweens[index].run(percent);}deferred.notifyWith(elem,[animation,percent,remaining]);if(percent < 1 && length){return remaining;}else {deferred.resolveWith(elem,[animation]);return false;}},animation=deferred.promise({elem:elem,props:jQuery.extend({},properties),opts:jQuery.extend(true,{specialEasing:{},easing:jQuery.easing._default},options),originalProperties:properties,originalOptions:options,startTime:fxNow || createFxNow(),duration:options.duration,tweens:[],createTween:function createTween(prop,end){var tween=jQuery.Tween(elem,animation.opts,prop,end,animation.opts.specialEasing[prop] || animation.opts.easing);animation.tweens.push(tween);return tween;},stop:function stop(gotoEnd){var index=0, // If we are going to the end, we want to run all the tweens
	// otherwise we skip this part
	length=gotoEnd?animation.tweens.length:0;if(stopped){return this;}stopped = true;for(;index < length;index++) {animation.tweens[index].run(1);} // Resolve when we played the last frame; otherwise, reject
	if(gotoEnd){deferred.notifyWith(elem,[animation,1,0]);deferred.resolveWith(elem,[animation,gotoEnd]);}else {deferred.rejectWith(elem,[animation,gotoEnd]);}return this;}}),props=animation.props;propFilter(props,animation.opts.specialEasing);for(;index < length;index++) {result = Animation.prefilters[index].call(animation,elem,props,animation.opts);if(result){if(jQuery.isFunction(result.stop)){jQuery._queueHooks(animation.elem,animation.opts.queue).stop = jQuery.proxy(result.stop,result);}return result;}}jQuery.map(props,createTween,animation);if(jQuery.isFunction(animation.opts.start)){animation.opts.start.call(elem,animation);}jQuery.fx.timer(jQuery.extend(tick,{elem:elem,anim:animation,queue:animation.opts.queue})); // attach callbacks from options
	return animation.progress(animation.opts.progress).done(animation.opts.done,animation.opts.complete).fail(animation.opts.fail).always(animation.opts.always);}jQuery.Animation = jQuery.extend(Animation,{tweeners:{"*":[function(prop,value){var tween=this.createTween(prop,value);adjustCSS(tween.elem,prop,rcssNum.exec(value),tween);return tween;}]},tweener:function tweener(props,callback){if(jQuery.isFunction(props)){callback = props;props = ["*"];}else {props = props.match(rnotwhite);}var prop,index=0,length=props.length;for(;index < length;index++) {prop = props[index];Animation.tweeners[prop] = Animation.tweeners[prop] || [];Animation.tweeners[prop].unshift(callback);}},prefilters:[defaultPrefilter],prefilter:function prefilter(callback,prepend){if(prepend){Animation.prefilters.unshift(callback);}else {Animation.prefilters.push(callback);}}});jQuery.speed = function(speed,easing,fn){var opt=speed && typeof speed === "object"?jQuery.extend({},speed):{complete:fn || !fn && easing || jQuery.isFunction(speed) && speed,duration:speed,easing:fn && easing || easing && !jQuery.isFunction(easing) && easing};opt.duration = jQuery.fx.off?0:typeof opt.duration === "number"?opt.duration:opt.duration in jQuery.fx.speeds?jQuery.fx.speeds[opt.duration]:jQuery.fx.speeds._default; // Normalize opt.queue - true/undefined/null -> "fx"
	if(opt.queue == null || opt.queue === true){opt.queue = "fx";} // Queueing
	opt.old = opt.complete;opt.complete = function(){if(jQuery.isFunction(opt.old)){opt.old.call(this);}if(opt.queue){jQuery.dequeue(this,opt.queue);}};return opt;};jQuery.fn.extend({fadeTo:function fadeTo(speed,to,easing,callback){ // Show any hidden elements after setting opacity to 0
	return this.filter(isHidden).css("opacity",0).show() // Animate to the value specified
	.end().animate({opacity:to},speed,easing,callback);},animate:function animate(prop,speed,easing,callback){var empty=jQuery.isEmptyObject(prop),optall=jQuery.speed(speed,easing,callback),doAnimation=function doAnimation(){ // Operate on a copy of prop so per-property easing won't be lost
	var anim=Animation(this,jQuery.extend({},prop),optall); // Empty animations, or finishing resolves immediately
	if(empty || dataPriv.get(this,"finish")){anim.stop(true);}};doAnimation.finish = doAnimation;return empty || optall.queue === false?this.each(doAnimation):this.queue(optall.queue,doAnimation);},stop:function stop(type,clearQueue,gotoEnd){var stopQueue=function stopQueue(hooks){var stop=hooks.stop;delete hooks.stop;stop(gotoEnd);};if(typeof type !== "string"){gotoEnd = clearQueue;clearQueue = type;type = undefined;}if(clearQueue && type !== false){this.queue(type || "fx",[]);}return this.each(function(){var dequeue=true,index=type != null && type + "queueHooks",timers=jQuery.timers,data=dataPriv.get(this);if(index){if(data[index] && data[index].stop){stopQueue(data[index]);}}else {for(index in data) {if(data[index] && data[index].stop && rrun.test(index)){stopQueue(data[index]);}}}for(index = timers.length;index--;) {if(timers[index].elem === this && (type == null || timers[index].queue === type)){timers[index].anim.stop(gotoEnd);dequeue = false;timers.splice(index,1);}} // Start the next in the queue if the last step wasn't forced.
	// Timers currently will call their complete callbacks, which
	// will dequeue but only if they were gotoEnd.
	if(dequeue || !gotoEnd){jQuery.dequeue(this,type);}});},finish:function finish(type){if(type !== false){type = type || "fx";}return this.each(function(){var index,data=dataPriv.get(this),queue=data[type + "queue"],hooks=data[type + "queueHooks"],timers=jQuery.timers,length=queue?queue.length:0; // Enable finishing flag on private data
	data.finish = true; // Empty the queue first
	jQuery.queue(this,type,[]);if(hooks && hooks.stop){hooks.stop.call(this,true);} // Look for any active animations, and finish them
	for(index = timers.length;index--;) {if(timers[index].elem === this && timers[index].queue === type){timers[index].anim.stop(true);timers.splice(index,1);}} // Look for any animations in the old queue and finish them
	for(index = 0;index < length;index++) {if(queue[index] && queue[index].finish){queue[index].finish.call(this);}} // Turn off finishing flag
	delete data.finish;});}});jQuery.each(["toggle","show","hide"],function(i,name){var cssFn=jQuery.fn[name];jQuery.fn[name] = function(speed,easing,callback){return speed == null || typeof speed === "boolean"?cssFn.apply(this,arguments):this.animate(genFx(name,true),speed,easing,callback);};}); // Generate shortcuts for custom animations
	jQuery.each({slideDown:genFx("show"),slideUp:genFx("hide"),slideToggle:genFx("toggle"),fadeIn:{opacity:"show"},fadeOut:{opacity:"hide"},fadeToggle:{opacity:"toggle"}},function(name,props){jQuery.fn[name] = function(speed,easing,callback){return this.animate(props,speed,easing,callback);};});jQuery.timers = [];jQuery.fx.tick = function(){var timer,i=0,timers=jQuery.timers;fxNow = jQuery.now();for(;i < timers.length;i++) {timer = timers[i]; // Checks the timer has not already been removed
	if(!timer() && timers[i] === timer){timers.splice(i--,1);}}if(!timers.length){jQuery.fx.stop();}fxNow = undefined;};jQuery.fx.timer = function(timer){jQuery.timers.push(timer);if(timer()){jQuery.fx.start();}else {jQuery.timers.pop();}};jQuery.fx.interval = 13;jQuery.fx.start = function(){if(!timerId){timerId = window.setInterval(jQuery.fx.tick,jQuery.fx.interval);}};jQuery.fx.stop = function(){window.clearInterval(timerId);timerId = null;};jQuery.fx.speeds = {slow:600,fast:200, // Default speed
	_default:400}; // Based off of the plugin by Clint Helfers, with permission.
	// http://web.archive.org/web/20100324014747/http://blindsignals.com/index.php/2009/07/jquery-delay/
	jQuery.fn.delay = function(time,type){time = jQuery.fx?jQuery.fx.speeds[time] || time:time;type = type || "fx";return this.queue(type,function(next,hooks){var timeout=window.setTimeout(next,time);hooks.stop = function(){window.clearTimeout(timeout);};});};(function(){var input=document.createElement("input"),select=document.createElement("select"),opt=select.appendChild(document.createElement("option"));input.type = "checkbox"; // Support: iOS<=5.1, Android<=4.2+
	// Default value for a checkbox should be "on"
	support.checkOn = input.value !== ""; // Support: IE<=11+
	// Must access selectedIndex to make default options select
	support.optSelected = opt.selected; // Support: Android<=2.3
	// Options inside disabled selects are incorrectly marked as disabled
	select.disabled = true;support.optDisabled = !opt.disabled; // Support: IE<=11+
	// An input loses its value after becoming a radio
	input = document.createElement("input");input.value = "t";input.type = "radio";support.radioValue = input.value === "t";})();var boolHook,attrHandle=jQuery.expr.attrHandle;jQuery.fn.extend({attr:function attr(name,value){return access(this,jQuery.attr,name,value,arguments.length > 1);},removeAttr:function removeAttr(name){return this.each(function(){jQuery.removeAttr(this,name);});}});jQuery.extend({attr:function attr(elem,name,value){var ret,hooks,nType=elem.nodeType; // Don't get/set attributes on text, comment and attribute nodes
	if(nType === 3 || nType === 8 || nType === 2){return;} // Fallback to prop when attributes are not supported
	if(typeof elem.getAttribute === "undefined"){return jQuery.prop(elem,name,value);} // All attributes are lowercase
	// Grab necessary hook if one is defined
	if(nType !== 1 || !jQuery.isXMLDoc(elem)){name = name.toLowerCase();hooks = jQuery.attrHooks[name] || (jQuery.expr.match.bool.test(name)?boolHook:undefined);}if(value !== undefined){if(value === null){jQuery.removeAttr(elem,name);return;}if(hooks && "set" in hooks && (ret = hooks.set(elem,value,name)) !== undefined){return ret;}elem.setAttribute(name,value + "");return value;}if(hooks && "get" in hooks && (ret = hooks.get(elem,name)) !== null){return ret;}ret = jQuery.find.attr(elem,name); // Non-existent attributes return null, we normalize to undefined
	return ret == null?undefined:ret;},attrHooks:{type:{set:function set(elem,value){if(!support.radioValue && value === "radio" && jQuery.nodeName(elem,"input")){var val=elem.value;elem.setAttribute("type",value);if(val){elem.value = val;}return value;}}}},removeAttr:function removeAttr(elem,value){var name,propName,i=0,attrNames=value && value.match(rnotwhite);if(attrNames && elem.nodeType === 1){while(name = attrNames[i++]) {propName = jQuery.propFix[name] || name; // Boolean attributes get special treatment (#10870)
	if(jQuery.expr.match.bool.test(name)){ // Set corresponding property to false
	elem[propName] = false;}elem.removeAttribute(name);}}}}); // Hooks for boolean attributes
	boolHook = {set:function set(elem,value,name){if(value === false){ // Remove boolean attributes when set to false
	jQuery.removeAttr(elem,name);}else {elem.setAttribute(name,name);}return name;}};jQuery.each(jQuery.expr.match.bool.source.match(/\w+/g),function(i,name){var getter=attrHandle[name] || jQuery.find.attr;attrHandle[name] = function(elem,name,isXML){var ret,handle;if(!isXML){ // Avoid an infinite loop by temporarily removing this function from the getter
	handle = attrHandle[name];attrHandle[name] = ret;ret = getter(elem,name,isXML) != null?name.toLowerCase():null;attrHandle[name] = handle;}return ret;};});var rfocusable=/^(?:input|select|textarea|button)$/i,rclickable=/^(?:a|area)$/i;jQuery.fn.extend({prop:function prop(name,value){return access(this,jQuery.prop,name,value,arguments.length > 1);},removeProp:function removeProp(name){return this.each(function(){delete this[jQuery.propFix[name] || name];});}});jQuery.extend({prop:function prop(elem,name,value){var ret,hooks,nType=elem.nodeType; // Don't get/set properties on text, comment and attribute nodes
	if(nType === 3 || nType === 8 || nType === 2){return;}if(nType !== 1 || !jQuery.isXMLDoc(elem)){ // Fix name and attach hooks
	name = jQuery.propFix[name] || name;hooks = jQuery.propHooks[name];}if(value !== undefined){if(hooks && "set" in hooks && (ret = hooks.set(elem,value,name)) !== undefined){return ret;}return elem[name] = value;}if(hooks && "get" in hooks && (ret = hooks.get(elem,name)) !== null){return ret;}return elem[name];},propHooks:{tabIndex:{get:function get(elem){ // elem.tabIndex doesn't always return the
	// correct value when it hasn't been explicitly set
	// http://fluidproject.org/blog/2008/01/09/getting-setting-and-removing-tabindex-values-with-javascript/
	// Use proper attribute retrieval(#12072)
	var tabindex=jQuery.find.attr(elem,"tabindex");return tabindex?parseInt(tabindex,10):rfocusable.test(elem.nodeName) || rclickable.test(elem.nodeName) && elem.href?0:-1;}}},propFix:{"for":"htmlFor","class":"className"}});if(!support.optSelected){jQuery.propHooks.selected = {get:function get(elem){var parent=elem.parentNode;if(parent && parent.parentNode){parent.parentNode.selectedIndex;}return null;}};}jQuery.each(["tabIndex","readOnly","maxLength","cellSpacing","cellPadding","rowSpan","colSpan","useMap","frameBorder","contentEditable"],function(){jQuery.propFix[this.toLowerCase()] = this;});var rclass=/[\t\r\n\f]/g;function getClass(elem){return elem.getAttribute && elem.getAttribute("class") || "";}jQuery.fn.extend({addClass:function addClass(value){var classes,elem,cur,curValue,clazz,j,finalValue,i=0;if(jQuery.isFunction(value)){return this.each(function(j){jQuery(this).addClass(value.call(this,j,getClass(this)));});}if(typeof value === "string" && value){classes = value.match(rnotwhite) || [];while(elem = this[i++]) {curValue = getClass(elem);cur = elem.nodeType === 1 && (" " + curValue + " ").replace(rclass," ");if(cur){j = 0;while(clazz = classes[j++]) {if(cur.indexOf(" " + clazz + " ") < 0){cur += clazz + " ";}} // Only assign if different to avoid unneeded rendering.
	finalValue = jQuery.trim(cur);if(curValue !== finalValue){elem.setAttribute("class",finalValue);}}}}return this;},removeClass:function removeClass(value){var classes,elem,cur,curValue,clazz,j,finalValue,i=0;if(jQuery.isFunction(value)){return this.each(function(j){jQuery(this).removeClass(value.call(this,j,getClass(this)));});}if(!arguments.length){return this.attr("class","");}if(typeof value === "string" && value){classes = value.match(rnotwhite) || [];while(elem = this[i++]) {curValue = getClass(elem); // This expression is here for better compressibility (see addClass)
	cur = elem.nodeType === 1 && (" " + curValue + " ").replace(rclass," ");if(cur){j = 0;while(clazz = classes[j++]) { // Remove *all* instances
	while(cur.indexOf(" " + clazz + " ") > -1) {cur = cur.replace(" " + clazz + " "," ");}} // Only assign if different to avoid unneeded rendering.
	finalValue = jQuery.trim(cur);if(curValue !== finalValue){elem.setAttribute("class",finalValue);}}}}return this;},toggleClass:function toggleClass(value,stateVal){var type=typeof value;if(typeof stateVal === "boolean" && type === "string"){return stateVal?this.addClass(value):this.removeClass(value);}if(jQuery.isFunction(value)){return this.each(function(i){jQuery(this).toggleClass(value.call(this,i,getClass(this),stateVal),stateVal);});}return this.each(function(){var className,i,self,classNames;if(type === "string"){ // Toggle individual class names
	i = 0;self = jQuery(this);classNames = value.match(rnotwhite) || [];while(className = classNames[i++]) { // Check each className given, space separated list
	if(self.hasClass(className)){self.removeClass(className);}else {self.addClass(className);}} // Toggle whole class name
	}else if(value === undefined || type === "boolean"){className = getClass(this);if(className){ // Store className if set
	dataPriv.set(this,"__className__",className);} // If the element has a class name or if we're passed `false`,
	// then remove the whole classname (if there was one, the above saved it).
	// Otherwise bring back whatever was previously saved (if anything),
	// falling back to the empty string if nothing was stored.
	if(this.setAttribute){this.setAttribute("class",className || value === false?"":dataPriv.get(this,"__className__") || "");}}});},hasClass:function hasClass(selector){var className,elem,i=0;className = " " + selector + " ";while(elem = this[i++]) {if(elem.nodeType === 1 && (" " + getClass(elem) + " ").replace(rclass," ").indexOf(className) > -1){return true;}}return false;}});var rreturn=/\r/g;jQuery.fn.extend({val:function val(value){var hooks,ret,isFunction,elem=this[0];if(!arguments.length){if(elem){hooks = jQuery.valHooks[elem.type] || jQuery.valHooks[elem.nodeName.toLowerCase()];if(hooks && "get" in hooks && (ret = hooks.get(elem,"value")) !== undefined){return ret;}ret = elem.value;return typeof ret === "string"? // Handle most common string cases
	ret.replace(rreturn,""): // Handle cases where value is null/undef or number
	ret == null?"":ret;}return;}isFunction = jQuery.isFunction(value);return this.each(function(i){var val;if(this.nodeType !== 1){return;}if(isFunction){val = value.call(this,i,jQuery(this).val());}else {val = value;} // Treat null/undefined as ""; convert numbers to string
	if(val == null){val = "";}else if(typeof val === "number"){val += "";}else if(jQuery.isArray(val)){val = jQuery.map(val,function(value){return value == null?"":value + "";});}hooks = jQuery.valHooks[this.type] || jQuery.valHooks[this.nodeName.toLowerCase()]; // If set returns undefined, fall back to normal setting
	if(!hooks || !("set" in hooks) || hooks.set(this,val,"value") === undefined){this.value = val;}});}});jQuery.extend({valHooks:{option:{get:function get(elem){ // Support: IE<11
	// option.value not trimmed (#14858)
	return jQuery.trim(elem.value);}},select:{get:function get(elem){var value,option,options=elem.options,index=elem.selectedIndex,one=elem.type === "select-one" || index < 0,values=one?null:[],max=one?index + 1:options.length,i=index < 0?max:one?index:0; // Loop through all the selected options
	for(;i < max;i++) {option = options[i]; // IE8-9 doesn't update selected after form reset (#2551)
	if((option.selected || i === index) && ( // Don't return options that are disabled or in a disabled optgroup
	support.optDisabled?!option.disabled:option.getAttribute("disabled") === null) && (!option.parentNode.disabled || !jQuery.nodeName(option.parentNode,"optgroup"))){ // Get the specific value for the option
	value = jQuery(option).val(); // We don't need an array for one selects
	if(one){return value;} // Multi-Selects return an array
	values.push(value);}}return values;},set:function set(elem,value){var optionSet,option,options=elem.options,values=jQuery.makeArray(value),i=options.length;while(i--) {option = options[i];if(option.selected = jQuery.inArray(jQuery.valHooks.option.get(option),values) > -1){optionSet = true;}} // Force browsers to behave consistently when non-matching value is set
	if(!optionSet){elem.selectedIndex = -1;}return values;}}}}); // Radios and checkboxes getter/setter
	jQuery.each(["radio","checkbox"],function(){jQuery.valHooks[this] = {set:function set(elem,value){if(jQuery.isArray(value)){return elem.checked = jQuery.inArray(jQuery(elem).val(),value) > -1;}}};if(!support.checkOn){jQuery.valHooks[this].get = function(elem){return elem.getAttribute("value") === null?"on":elem.value;};}}); // Return jQuery for attributes-only inclusion
	var rfocusMorph=/^(?:focusinfocus|focusoutblur)$/;jQuery.extend(jQuery.event,{trigger:function trigger(event,data,elem,onlyHandlers){var i,cur,tmp,bubbleType,ontype,handle,special,eventPath=[elem || document],type=hasOwn.call(event,"type")?event.type:event,namespaces=hasOwn.call(event,"namespace")?event.namespace.split("."):[];cur = tmp = elem = elem || document; // Don't do events on text and comment nodes
	if(elem.nodeType === 3 || elem.nodeType === 8){return;} // focus/blur morphs to focusin/out; ensure we're not firing them right now
	if(rfocusMorph.test(type + jQuery.event.triggered)){return;}if(type.indexOf(".") > -1){ // Namespaced trigger; create a regexp to match event type in handle()
	namespaces = type.split(".");type = namespaces.shift();namespaces.sort();}ontype = type.indexOf(":") < 0 && "on" + type; // Caller can pass in a jQuery.Event object, Object, or just an event type string
	event = event[jQuery.expando]?event:new jQuery.Event(type,typeof event === "object" && event); // Trigger bitmask: & 1 for native handlers; & 2 for jQuery (always true)
	event.isTrigger = onlyHandlers?2:3;event.namespace = namespaces.join(".");event.rnamespace = event.namespace?new RegExp("(^|\\.)" + namespaces.join("\\.(?:.*\\.|)") + "(\\.|$)"):null; // Clean up the event in case it is being reused
	event.result = undefined;if(!event.target){event.target = elem;} // Clone any incoming data and prepend the event, creating the handler arg list
	data = data == null?[event]:jQuery.makeArray(data,[event]); // Allow special events to draw outside the lines
	special = jQuery.event.special[type] || {};if(!onlyHandlers && special.trigger && special.trigger.apply(elem,data) === false){return;} // Determine event propagation path in advance, per W3C events spec (#9951)
	// Bubble up to document, then to window; watch for a global ownerDocument var (#9724)
	if(!onlyHandlers && !special.noBubble && !jQuery.isWindow(elem)){bubbleType = special.delegateType || type;if(!rfocusMorph.test(bubbleType + type)){cur = cur.parentNode;}for(;cur;cur = cur.parentNode) {eventPath.push(cur);tmp = cur;} // Only add window if we got to document (e.g., not plain obj or detached DOM)
	if(tmp === (elem.ownerDocument || document)){eventPath.push(tmp.defaultView || tmp.parentWindow || window);}} // Fire handlers on the event path
	i = 0;while((cur = eventPath[i++]) && !event.isPropagationStopped()) {event.type = i > 1?bubbleType:special.bindType || type; // jQuery handler
	handle = (dataPriv.get(cur,"events") || {})[event.type] && dataPriv.get(cur,"handle");if(handle){handle.apply(cur,data);} // Native handler
	handle = ontype && cur[ontype];if(handle && handle.apply && acceptData(cur)){event.result = handle.apply(cur,data);if(event.result === false){event.preventDefault();}}}event.type = type; // If nobody prevented the default action, do it now
	if(!onlyHandlers && !event.isDefaultPrevented()){if((!special._default || special._default.apply(eventPath.pop(),data) === false) && acceptData(elem)){ // Call a native DOM method on the target with the same name name as the event.
	// Don't do default actions on window, that's where global variables be (#6170)
	if(ontype && jQuery.isFunction(elem[type]) && !jQuery.isWindow(elem)){ // Don't re-trigger an onFOO event when we call its FOO() method
	tmp = elem[ontype];if(tmp){elem[ontype] = null;} // Prevent re-triggering of the same event, since we already bubbled it above
	jQuery.event.triggered = type;elem[type]();jQuery.event.triggered = undefined;if(tmp){elem[ontype] = tmp;}}}}return event.result;}, // Piggyback on a donor event to simulate a different one
	simulate:function simulate(type,elem,event){var e=jQuery.extend(new jQuery.Event(),event,{type:type,isSimulated:true // Previously, `originalEvent: {}` was set here, so stopPropagation call
	// would not be triggered on donor event, since in our own
	// jQuery.event.stopPropagation function we had a check for existence of
	// originalEvent.stopPropagation method, so, consequently it would be a noop.
	//
	// But now, this "simulate" function is used only for events
	// for which stopPropagation() is noop, so there is no need for that anymore.
	//
	// For the compat branch though, guard for "click" and "submit"
	// events is still used, but was moved to jQuery.event.stopPropagation function
	// because `originalEvent` should point to the original event for the constancy
	// with other events and for more focused logic
	});jQuery.event.trigger(e,null,elem);if(e.isDefaultPrevented()){event.preventDefault();}}});jQuery.fn.extend({trigger:function trigger(type,data){return this.each(function(){jQuery.event.trigger(type,data,this);});},triggerHandler:function triggerHandler(type,data){var elem=this[0];if(elem){return jQuery.event.trigger(type,data,elem,true);}}});jQuery.each(("blur focus focusin focusout load resize scroll unload click dblclick " + "mousedown mouseup mousemove mouseover mouseout mouseenter mouseleave " + "change select submit keydown keypress keyup error contextmenu").split(" "),function(i,name){ // Handle event binding
	jQuery.fn[name] = function(data,fn){return arguments.length > 0?this.on(name,null,data,fn):this.trigger(name);};});jQuery.fn.extend({hover:function hover(fnOver,fnOut){return this.mouseenter(fnOver).mouseleave(fnOut || fnOver);}});support.focusin = "onfocusin" in window; // Support: Firefox
	// Firefox doesn't have focus(in | out) events
	// Related ticket - https://bugzilla.mozilla.org/show_bug.cgi?id=687787
	//
	// Support: Chrome, Safari
	// focus(in | out) events fire after focus & blur events,
	// which is spec violation - http://www.w3.org/TR/DOM-Level-3-Events/#events-focusevent-event-order
	// Related ticket - https://code.google.com/p/chromium/issues/detail?id=449857
	if(!support.focusin){jQuery.each({focus:"focusin",blur:"focusout"},function(orig,fix){ // Attach a single capturing handler on the document while someone wants focusin/focusout
	var handler=function handler(event){jQuery.event.simulate(fix,event.target,jQuery.event.fix(event));};jQuery.event.special[fix] = {setup:function setup(){var doc=this.ownerDocument || this,attaches=dataPriv.access(doc,fix);if(!attaches){doc.addEventListener(orig,handler,true);}dataPriv.access(doc,fix,(attaches || 0) + 1);},teardown:function teardown(){var doc=this.ownerDocument || this,attaches=dataPriv.access(doc,fix) - 1;if(!attaches){doc.removeEventListener(orig,handler,true);dataPriv.remove(doc,fix);}else {dataPriv.access(doc,fix,attaches);}}};});}var location=window.location;var nonce=jQuery.now();var rquery=/\?/; // Support: Android 2.3
	// Workaround failure to string-cast null input
	jQuery.parseJSON = function(data){return JSON.parse(data + "");}; // Cross-browser xml parsing
	jQuery.parseXML = function(data){var xml;if(!data || typeof data !== "string"){return null;} // Support: IE9
	try{xml = new window.DOMParser().parseFromString(data,"text/xml");}catch(e) {xml = undefined;}if(!xml || xml.getElementsByTagName("parsererror").length){jQuery.error("Invalid XML: " + data);}return xml;};var rhash=/#.*$/,rts=/([?&])_=[^&]*/,rheaders=/^(.*?):[ \t]*([^\r\n]*)$/mg, // #7653, #8125, #8152: local protocol detection
	rlocalProtocol=/^(?:about|app|app-storage|.+-extension|file|res|widget):$/,rnoContent=/^(?:GET|HEAD)$/,rprotocol=/^\/\//, /* Prefilters
		 * 1) They are useful to introduce custom dataTypes (see ajax/jsonp.js for an example)
		 * 2) These are called:
		 *    - BEFORE asking for a transport
		 *    - AFTER param serialization (s.data is a string if s.processData is true)
		 * 3) key is the dataType
		 * 4) the catchall symbol "*" can be used
		 * 5) execution will start with transport dataType and THEN continue down to "*" if needed
		 */prefilters={}, /* Transports bindings
		 * 1) key is the dataType
		 * 2) the catchall symbol "*" can be used
		 * 3) selection will start with transport dataType and THEN go to "*" if needed
		 */transports={}, // Avoid comment-prolog char sequence (#10098); must appease lint and evade compression
	allTypes="*/".concat("*"), // Anchor tag for parsing the document origin
	originAnchor=document.createElement("a");originAnchor.href = location.href; // Base "constructor" for jQuery.ajaxPrefilter and jQuery.ajaxTransport
	function addToPrefiltersOrTransports(structure){ // dataTypeExpression is optional and defaults to "*"
	return function(dataTypeExpression,func){if(typeof dataTypeExpression !== "string"){func = dataTypeExpression;dataTypeExpression = "*";}var dataType,i=0,dataTypes=dataTypeExpression.toLowerCase().match(rnotwhite) || [];if(jQuery.isFunction(func)){ // For each dataType in the dataTypeExpression
	while(dataType = dataTypes[i++]) { // Prepend if requested
	if(dataType[0] === "+"){dataType = dataType.slice(1) || "*";(structure[dataType] = structure[dataType] || []).unshift(func); // Otherwise append
	}else {(structure[dataType] = structure[dataType] || []).push(func);}}}};} // Base inspection function for prefilters and transports
	function inspectPrefiltersOrTransports(structure,options,originalOptions,jqXHR){var inspected={},seekingTransport=structure === transports;function inspect(dataType){var selected;inspected[dataType] = true;jQuery.each(structure[dataType] || [],function(_,prefilterOrFactory){var dataTypeOrTransport=prefilterOrFactory(options,originalOptions,jqXHR);if(typeof dataTypeOrTransport === "string" && !seekingTransport && !inspected[dataTypeOrTransport]){options.dataTypes.unshift(dataTypeOrTransport);inspect(dataTypeOrTransport);return false;}else if(seekingTransport){return !(selected = dataTypeOrTransport);}});return selected;}return inspect(options.dataTypes[0]) || !inspected["*"] && inspect("*");} // A special extend for ajax options
	// that takes "flat" options (not to be deep extended)
	// Fixes #9887
	function ajaxExtend(target,src){var key,deep,flatOptions=jQuery.ajaxSettings.flatOptions || {};for(key in src) {if(src[key] !== undefined){(flatOptions[key]?target:deep || (deep = {}))[key] = src[key];}}if(deep){jQuery.extend(true,target,deep);}return target;} /* Handles responses to an ajax request:
	 * - finds the right dataType (mediates between content-type and expected dataType)
	 * - returns the corresponding response
	 */function ajaxHandleResponses(s,jqXHR,responses){var ct,type,finalDataType,firstDataType,contents=s.contents,dataTypes=s.dataTypes; // Remove auto dataType and get content-type in the process
	while(dataTypes[0] === "*") {dataTypes.shift();if(ct === undefined){ct = s.mimeType || jqXHR.getResponseHeader("Content-Type");}} // Check if we're dealing with a known content-type
	if(ct){for(type in contents) {if(contents[type] && contents[type].test(ct)){dataTypes.unshift(type);break;}}} // Check to see if we have a response for the expected dataType
	if(dataTypes[0] in responses){finalDataType = dataTypes[0];}else { // Try convertible dataTypes
	for(type in responses) {if(!dataTypes[0] || s.converters[type + " " + dataTypes[0]]){finalDataType = type;break;}if(!firstDataType){firstDataType = type;}} // Or just use first one
	finalDataType = finalDataType || firstDataType;} // If we found a dataType
	// We add the dataType to the list if needed
	// and return the corresponding response
	if(finalDataType){if(finalDataType !== dataTypes[0]){dataTypes.unshift(finalDataType);}return responses[finalDataType];}} /* Chain conversions given the request and the original response
	 * Also sets the responseXXX fields on the jqXHR instance
	 */function ajaxConvert(s,response,jqXHR,isSuccess){var conv2,current,conv,tmp,prev,converters={}, // Work with a copy of dataTypes in case we need to modify it for conversion
	dataTypes=s.dataTypes.slice(); // Create converters map with lowercased keys
	if(dataTypes[1]){for(conv in s.converters) {converters[conv.toLowerCase()] = s.converters[conv];}}current = dataTypes.shift(); // Convert to each sequential dataType
	while(current) {if(s.responseFields[current]){jqXHR[s.responseFields[current]] = response;} // Apply the dataFilter if provided
	if(!prev && isSuccess && s.dataFilter){response = s.dataFilter(response,s.dataType);}prev = current;current = dataTypes.shift();if(current){ // There's only work to do if current dataType is non-auto
	if(current === "*"){current = prev; // Convert response if prev dataType is non-auto and differs from current
	}else if(prev !== "*" && prev !== current){ // Seek a direct converter
	conv = converters[prev + " " + current] || converters["* " + current]; // If none found, seek a pair
	if(!conv){for(conv2 in converters) { // If conv2 outputs current
	tmp = conv2.split(" ");if(tmp[1] === current){ // If prev can be converted to accepted input
	conv = converters[prev + " " + tmp[0]] || converters["* " + tmp[0]];if(conv){ // Condense equivalence converters
	if(conv === true){conv = converters[conv2]; // Otherwise, insert the intermediate dataType
	}else if(converters[conv2] !== true){current = tmp[0];dataTypes.unshift(tmp[1]);}break;}}}} // Apply converter (if not an equivalence)
	if(conv !== true){ // Unless errors are allowed to bubble, catch and return them
	if(conv && s.throws){response = conv(response);}else {try{response = conv(response);}catch(e) {return {state:"parsererror",error:conv?e:"No conversion from " + prev + " to " + current};}}}}}}return {state:"success",data:response};}jQuery.extend({ // Counter for holding the number of active queries
	active:0, // Last-Modified header cache for next request
	lastModified:{},etag:{},ajaxSettings:{url:location.href,type:"GET",isLocal:rlocalProtocol.test(location.protocol),global:true,processData:true,async:true,contentType:"application/x-www-form-urlencoded; charset=UTF-8", /*
			timeout: 0,
			data: null,
			dataType: null,
			username: null,
			password: null,
			cache: null,
			throws: false,
			traditional: false,
			headers: {},
			*/accepts:{"*":allTypes,text:"text/plain",html:"text/html",xml:"application/xml, text/xml",json:"application/json, text/javascript"},contents:{xml:/\bxml\b/,html:/\bhtml/,json:/\bjson\b/},responseFields:{xml:"responseXML",text:"responseText",json:"responseJSON"}, // Data converters
	// Keys separate source (or catchall "*") and destination types with a single space
	converters:{ // Convert anything to text
	"* text":String, // Text to html (true = no transformation)
	"text html":true, // Evaluate text as a json expression
	"text json":jQuery.parseJSON, // Parse text as xml
	"text xml":jQuery.parseXML}, // For options that shouldn't be deep extended:
	// you can add your own custom options here if
	// and when you create one that shouldn't be
	// deep extended (see ajaxExtend)
	flatOptions:{url:true,context:true}}, // Creates a full fledged settings object into target
	// with both ajaxSettings and settings fields.
	// If target is omitted, writes into ajaxSettings.
	ajaxSetup:function ajaxSetup(target,settings){return settings? // Building a settings object
	ajaxExtend(ajaxExtend(target,jQuery.ajaxSettings),settings): // Extending ajaxSettings
	ajaxExtend(jQuery.ajaxSettings,target);},ajaxPrefilter:addToPrefiltersOrTransports(prefilters),ajaxTransport:addToPrefiltersOrTransports(transports), // Main method
	ajax:function ajax(url,options){ // If url is an object, simulate pre-1.5 signature
	if(typeof url === "object"){options = url;url = undefined;} // Force options to be an object
	options = options || {};var transport, // URL without anti-cache param
	cacheURL, // Response headers
	responseHeadersString,responseHeaders, // timeout handle
	timeoutTimer, // Url cleanup var
	urlAnchor, // To know if global events are to be dispatched
	fireGlobals, // Loop variable
	i, // Create the final options object
	s=jQuery.ajaxSetup({},options), // Callbacks context
	callbackContext=s.context || s, // Context for global events is callbackContext if it is a DOM node or jQuery collection
	globalEventContext=s.context && (callbackContext.nodeType || callbackContext.jquery)?jQuery(callbackContext):jQuery.event, // Deferreds
	deferred=jQuery.Deferred(),completeDeferred=jQuery.Callbacks("once memory"), // Status-dependent callbacks
	_statusCode=s.statusCode || {}, // Headers (they are sent all at once)
	requestHeaders={},requestHeadersNames={}, // The jqXHR state
	state=0, // Default abort message
	strAbort="canceled", // Fake xhr
	jqXHR={readyState:0, // Builds headers hashtable if needed
	getResponseHeader:function getResponseHeader(key){var match;if(state === 2){if(!responseHeaders){responseHeaders = {};while(match = rheaders.exec(responseHeadersString)) {responseHeaders[match[1].toLowerCase()] = match[2];}}match = responseHeaders[key.toLowerCase()];}return match == null?null:match;}, // Raw string
	getAllResponseHeaders:function getAllResponseHeaders(){return state === 2?responseHeadersString:null;}, // Caches the header
	setRequestHeader:function setRequestHeader(name,value){var lname=name.toLowerCase();if(!state){name = requestHeadersNames[lname] = requestHeadersNames[lname] || name;requestHeaders[name] = value;}return this;}, // Overrides response content-type header
	overrideMimeType:function overrideMimeType(type){if(!state){s.mimeType = type;}return this;}, // Status-dependent callbacks
	statusCode:function statusCode(map){var code;if(map){if(state < 2){for(code in map) { // Lazy-add the new callback in a way that preserves old ones
	_statusCode[code] = [_statusCode[code],map[code]];}}else { // Execute the appropriate callbacks
	jqXHR.always(map[jqXHR.status]);}}return this;}, // Cancel the request
	abort:function abort(statusText){var finalText=statusText || strAbort;if(transport){transport.abort(finalText);}done(0,finalText);return this;}}; // Attach deferreds
	deferred.promise(jqXHR).complete = completeDeferred.add;jqXHR.success = jqXHR.done;jqXHR.error = jqXHR.fail; // Remove hash character (#7531: and string promotion)
	// Add protocol if not provided (prefilters might expect it)
	// Handle falsy url in the settings object (#10093: consistency with old signature)
	// We also use the url parameter if available
	s.url = ((url || s.url || location.href) + "").replace(rhash,"").replace(rprotocol,location.protocol + "//"); // Alias method option to type as per ticket #12004
	s.type = options.method || options.type || s.method || s.type; // Extract dataTypes list
	s.dataTypes = jQuery.trim(s.dataType || "*").toLowerCase().match(rnotwhite) || [""]; // A cross-domain request is in order when the origin doesn't match the current origin.
	if(s.crossDomain == null){urlAnchor = document.createElement("a"); // Support: IE8-11+
	// IE throws exception if url is malformed, e.g. http://example.com:80x/
	try{urlAnchor.href = s.url; // Support: IE8-11+
	// Anchor's host property isn't correctly set when s.url is relative
	urlAnchor.href = urlAnchor.href;s.crossDomain = originAnchor.protocol + "//" + originAnchor.host !== urlAnchor.protocol + "//" + urlAnchor.host;}catch(e) { // If there is an error parsing the URL, assume it is crossDomain,
	// it can be rejected by the transport if it is invalid
	s.crossDomain = true;}} // Convert data if not already a string
	if(s.data && s.processData && typeof s.data !== "string"){s.data = jQuery.param(s.data,s.traditional);} // Apply prefilters
	inspectPrefiltersOrTransports(prefilters,s,options,jqXHR); // If request was aborted inside a prefilter, stop there
	if(state === 2){return jqXHR;} // We can fire global events as of now if asked to
	// Don't fire events if jQuery.event is undefined in an AMD-usage scenario (#15118)
	fireGlobals = jQuery.event && s.global; // Watch for a new set of requests
	if(fireGlobals && jQuery.active++ === 0){jQuery.event.trigger("ajaxStart");} // Uppercase the type
	s.type = s.type.toUpperCase(); // Determine if request has content
	s.hasContent = !rnoContent.test(s.type); // Save the URL in case we're toying with the If-Modified-Since
	// and/or If-None-Match header later on
	cacheURL = s.url; // More options handling for requests with no content
	if(!s.hasContent){ // If data is available, append data to url
	if(s.data){cacheURL = s.url += (rquery.test(cacheURL)?"&":"?") + s.data; // #9682: remove data so that it's not used in an eventual retry
	delete s.data;} // Add anti-cache in url if needed
	if(s.cache === false){s.url = rts.test(cacheURL)? // If there is already a '_' parameter, set its value
	cacheURL.replace(rts,"$1_=" + nonce++): // Otherwise add one to the end
	cacheURL + (rquery.test(cacheURL)?"&":"?") + "_=" + nonce++;}} // Set the If-Modified-Since and/or If-None-Match header, if in ifModified mode.
	if(s.ifModified){if(jQuery.lastModified[cacheURL]){jqXHR.setRequestHeader("If-Modified-Since",jQuery.lastModified[cacheURL]);}if(jQuery.etag[cacheURL]){jqXHR.setRequestHeader("If-None-Match",jQuery.etag[cacheURL]);}} // Set the correct header, if data is being sent
	if(s.data && s.hasContent && s.contentType !== false || options.contentType){jqXHR.setRequestHeader("Content-Type",s.contentType);} // Set the Accepts header for the server, depending on the dataType
	jqXHR.setRequestHeader("Accept",s.dataTypes[0] && s.accepts[s.dataTypes[0]]?s.accepts[s.dataTypes[0]] + (s.dataTypes[0] !== "*"?", " + allTypes + "; q=0.01":""):s.accepts["*"]); // Check for headers option
	for(i in s.headers) {jqXHR.setRequestHeader(i,s.headers[i]);} // Allow custom headers/mimetypes and early abort
	if(s.beforeSend && (s.beforeSend.call(callbackContext,jqXHR,s) === false || state === 2)){ // Abort if not done already and return
	return jqXHR.abort();} // Aborting is no longer a cancellation
	strAbort = "abort"; // Install callbacks on deferreds
	for(i in {success:1,error:1,complete:1}) {jqXHR[i](s[i]);} // Get transport
	transport = inspectPrefiltersOrTransports(transports,s,options,jqXHR); // If no transport, we auto-abort
	if(!transport){done(-1,"No Transport");}else {jqXHR.readyState = 1; // Send global event
	if(fireGlobals){globalEventContext.trigger("ajaxSend",[jqXHR,s]);} // If request was aborted inside ajaxSend, stop there
	if(state === 2){return jqXHR;} // Timeout
	if(s.async && s.timeout > 0){timeoutTimer = window.setTimeout(function(){jqXHR.abort("timeout");},s.timeout);}try{state = 1;transport.send(requestHeaders,done);}catch(e) { // Propagate exception as error if not done
	if(state < 2){done(-1,e); // Simply rethrow otherwise
	}else {throw e;}}} // Callback for when everything is done
	function done(status,nativeStatusText,responses,headers){var isSuccess,success,error,response,modified,statusText=nativeStatusText; // Called once
	if(state === 2){return;} // State is "done" now
	state = 2; // Clear timeout if it exists
	if(timeoutTimer){window.clearTimeout(timeoutTimer);} // Dereference transport for early garbage collection
	// (no matter how long the jqXHR object will be used)
	transport = undefined; // Cache response headers
	responseHeadersString = headers || ""; // Set readyState
	jqXHR.readyState = status > 0?4:0; // Determine if successful
	isSuccess = status >= 200 && status < 300 || status === 304; // Get response data
	if(responses){response = ajaxHandleResponses(s,jqXHR,responses);} // Convert no matter what (that way responseXXX fields are always set)
	response = ajaxConvert(s,response,jqXHR,isSuccess); // If successful, handle type chaining
	if(isSuccess){ // Set the If-Modified-Since and/or If-None-Match header, if in ifModified mode.
	if(s.ifModified){modified = jqXHR.getResponseHeader("Last-Modified");if(modified){jQuery.lastModified[cacheURL] = modified;}modified = jqXHR.getResponseHeader("etag");if(modified){jQuery.etag[cacheURL] = modified;}} // if no content
	if(status === 204 || s.type === "HEAD"){statusText = "nocontent"; // if not modified
	}else if(status === 304){statusText = "notmodified"; // If we have data, let's convert it
	}else {statusText = response.state;success = response.data;error = response.error;isSuccess = !error;}}else { // Extract error from statusText and normalize for non-aborts
	error = statusText;if(status || !statusText){statusText = "error";if(status < 0){status = 0;}}} // Set data for the fake xhr object
	jqXHR.status = status;jqXHR.statusText = (nativeStatusText || statusText) + ""; // Success/Error
	if(isSuccess){deferred.resolveWith(callbackContext,[success,statusText,jqXHR]);}else {deferred.rejectWith(callbackContext,[jqXHR,statusText,error]);} // Status-dependent callbacks
	jqXHR.statusCode(_statusCode);_statusCode = undefined;if(fireGlobals){globalEventContext.trigger(isSuccess?"ajaxSuccess":"ajaxError",[jqXHR,s,isSuccess?success:error]);} // Complete
	completeDeferred.fireWith(callbackContext,[jqXHR,statusText]);if(fireGlobals){globalEventContext.trigger("ajaxComplete",[jqXHR,s]); // Handle the global AJAX counter
	if(! --jQuery.active){jQuery.event.trigger("ajaxStop");}}}return jqXHR;},getJSON:function getJSON(url,data,callback){return jQuery.get(url,data,callback,"json");},getScript:function getScript(url,callback){return jQuery.get(url,undefined,callback,"script");}});jQuery.each(["get","post"],function(i,method){jQuery[method] = function(url,data,callback,type){ // Shift arguments if data argument was omitted
	if(jQuery.isFunction(data)){type = type || callback;callback = data;data = undefined;} // The url can be an options object (which then must have .url)
	return jQuery.ajax(jQuery.extend({url:url,type:method,dataType:type,data:data,success:callback},jQuery.isPlainObject(url) && url));};});jQuery._evalUrl = function(url){return jQuery.ajax({url:url, // Make this explicit, since user can override this through ajaxSetup (#11264)
	type:"GET",dataType:"script",async:false,global:false,"throws":true});};jQuery.fn.extend({wrapAll:function wrapAll(html){var wrap;if(jQuery.isFunction(html)){return this.each(function(i){jQuery(this).wrapAll(html.call(this,i));});}if(this[0]){ // The elements to wrap the target around
	wrap = jQuery(html,this[0].ownerDocument).eq(0).clone(true);if(this[0].parentNode){wrap.insertBefore(this[0]);}wrap.map(function(){var elem=this;while(elem.firstElementChild) {elem = elem.firstElementChild;}return elem;}).append(this);}return this;},wrapInner:function wrapInner(html){if(jQuery.isFunction(html)){return this.each(function(i){jQuery(this).wrapInner(html.call(this,i));});}return this.each(function(){var self=jQuery(this),contents=self.contents();if(contents.length){contents.wrapAll(html);}else {self.append(html);}});},wrap:function wrap(html){var isFunction=jQuery.isFunction(html);return this.each(function(i){jQuery(this).wrapAll(isFunction?html.call(this,i):html);});},unwrap:function unwrap(){return this.parent().each(function(){if(!jQuery.nodeName(this,"body")){jQuery(this).replaceWith(this.childNodes);}}).end();}});jQuery.expr.filters.hidden = function(elem){return !jQuery.expr.filters.visible(elem);};jQuery.expr.filters.visible = function(elem){ // Support: Opera <= 12.12
	// Opera reports offsetWidths and offsetHeights less than zero on some elements
	// Use OR instead of AND as the element is not visible if either is true
	// See tickets #10406 and #13132
	return elem.offsetWidth > 0 || elem.offsetHeight > 0 || elem.getClientRects().length > 0;};var r20=/%20/g,rbracket=/\[\]$/,rCRLF=/\r?\n/g,rsubmitterTypes=/^(?:submit|button|image|reset|file)$/i,rsubmittable=/^(?:input|select|textarea|keygen)/i;function buildParams(prefix,obj,traditional,add){var name;if(jQuery.isArray(obj)){ // Serialize array item.
	jQuery.each(obj,function(i,v){if(traditional || rbracket.test(prefix)){ // Treat each array item as a scalar.
	add(prefix,v);}else { // Item is non-scalar (array or object), encode its numeric index.
	buildParams(prefix + "[" + (typeof v === "object" && v != null?i:"") + "]",v,traditional,add);}});}else if(!traditional && jQuery.type(obj) === "object"){ // Serialize object item.
	for(name in obj) {buildParams(prefix + "[" + name + "]",obj[name],traditional,add);}}else { // Serialize scalar item.
	add(prefix,obj);}} // Serialize an array of form elements or a set of
	// key/values into a query string
	jQuery.param = function(a,traditional){var prefix,s=[],add=function add(key,value){ // If value is a function, invoke it and return its value
	value = jQuery.isFunction(value)?value():value == null?"":value;s[s.length] = encodeURIComponent(key) + "=" + encodeURIComponent(value);}; // Set traditional to true for jQuery <= 1.3.2 behavior.
	if(traditional === undefined){traditional = jQuery.ajaxSettings && jQuery.ajaxSettings.traditional;} // If an array was passed in, assume that it is an array of form elements.
	if(jQuery.isArray(a) || a.jquery && !jQuery.isPlainObject(a)){ // Serialize the form elements
	jQuery.each(a,function(){add(this.name,this.value);});}else { // If traditional, encode the "old" way (the way 1.3.2 or older
	// did it), otherwise encode params recursively.
	for(prefix in a) {buildParams(prefix,a[prefix],traditional,add);}} // Return the resulting serialization
	return s.join("&").replace(r20,"+");};jQuery.fn.extend({serialize:function serialize(){return jQuery.param(this.serializeArray());},serializeArray:function serializeArray(){return this.map(function(){ // Can add propHook for "elements" to filter or add form elements
	var elements=jQuery.prop(this,"elements");return elements?jQuery.makeArray(elements):this;}).filter(function(){var type=this.type; // Use .is( ":disabled" ) so that fieldset[disabled] works
	return this.name && !jQuery(this).is(":disabled") && rsubmittable.test(this.nodeName) && !rsubmitterTypes.test(type) && (this.checked || !rcheckableType.test(type));}).map(function(i,elem){var val=jQuery(this).val();return val == null?null:jQuery.isArray(val)?jQuery.map(val,function(val){return {name:elem.name,value:val.replace(rCRLF,"\r\n")};}):{name:elem.name,value:val.replace(rCRLF,"\r\n")};}).get();}});jQuery.ajaxSettings.xhr = function(){try{return new window.XMLHttpRequest();}catch(e) {}};var xhrSuccessStatus={ // File protocol always yields status code 0, assume 200
	0:200, // Support: IE9
	// #1450: sometimes IE returns 1223 when it should be 204
	1223:204},xhrSupported=jQuery.ajaxSettings.xhr();support.cors = !!xhrSupported && "withCredentials" in xhrSupported;support.ajax = xhrSupported = !!xhrSupported;jQuery.ajaxTransport(function(options){var callback,errorCallback; // Cross domain only allowed if supported through XMLHttpRequest
	if(support.cors || xhrSupported && !options.crossDomain){return {send:function send(headers,complete){var i,xhr=options.xhr();xhr.open(options.type,options.url,options.async,options.username,options.password); // Apply custom fields if provided
	if(options.xhrFields){for(i in options.xhrFields) {xhr[i] = options.xhrFields[i];}} // Override mime type if needed
	if(options.mimeType && xhr.overrideMimeType){xhr.overrideMimeType(options.mimeType);} // X-Requested-With header
	// For cross-domain requests, seeing as conditions for a preflight are
	// akin to a jigsaw puzzle, we simply never set it to be sure.
	// (it can always be set on a per-request basis or even using ajaxSetup)
	// For same-domain requests, won't change header if already provided.
	if(!options.crossDomain && !headers["X-Requested-With"]){headers["X-Requested-With"] = "XMLHttpRequest";} // Set headers
	for(i in headers) {xhr.setRequestHeader(i,headers[i]);} // Callback
	callback = function(type){return function(){if(callback){callback = errorCallback = xhr.onload = xhr.onerror = xhr.onabort = xhr.onreadystatechange = null;if(type === "abort"){xhr.abort();}else if(type === "error"){ // Support: IE9
	// On a manual native abort, IE9 throws
	// errors on any property access that is not readyState
	if(typeof xhr.status !== "number"){complete(0,"error");}else {complete( // File: protocol always yields status 0; see #8605, #14207
	xhr.status,xhr.statusText);}}else {complete(xhrSuccessStatus[xhr.status] || xhr.status,xhr.statusText, // Support: IE9 only
	// IE9 has no XHR2 but throws on binary (trac-11426)
	// For XHR2 non-text, let the caller handle it (gh-2498)
	(xhr.responseType || "text") !== "text" || typeof xhr.responseText !== "string"?{binary:xhr.response}:{text:xhr.responseText},xhr.getAllResponseHeaders());}}};}; // Listen to events
	xhr.onload = callback();errorCallback = xhr.onerror = callback("error"); // Support: IE9
	// Use onreadystatechange to replace onabort
	// to handle uncaught aborts
	if(xhr.onabort !== undefined){xhr.onabort = errorCallback;}else {xhr.onreadystatechange = function(){ // Check readyState before timeout as it changes
	if(xhr.readyState === 4){ // Allow onerror to be called first,
	// but that will not handle a native abort
	// Also, save errorCallback to a variable
	// as xhr.onerror cannot be accessed
	window.setTimeout(function(){if(callback){errorCallback();}});}};} // Create the abort callback
	callback = callback("abort");try{ // Do send the request (this may raise an exception)
	xhr.send(options.hasContent && options.data || null);}catch(e) { // #14683: Only rethrow if this hasn't been notified as an error yet
	if(callback){throw e;}}},abort:function abort(){if(callback){callback();}}};}}); // Install script dataType
	jQuery.ajaxSetup({accepts:{script:"text/javascript, application/javascript, " + "application/ecmascript, application/x-ecmascript"},contents:{script:/\b(?:java|ecma)script\b/},converters:{"text script":function textScript(text){jQuery.globalEval(text);return text;}}}); // Handle cache's special case and crossDomain
	jQuery.ajaxPrefilter("script",function(s){if(s.cache === undefined){s.cache = false;}if(s.crossDomain){s.type = "GET";}}); // Bind script tag hack transport
	jQuery.ajaxTransport("script",function(s){ // This transport only deals with cross domain requests
	if(s.crossDomain){var script,callback;return {send:function send(_,complete){script = jQuery("<script>").prop({charset:s.scriptCharset,src:s.url}).on("load error",callback = function(evt){script.remove();callback = null;if(evt){complete(evt.type === "error"?404:200,evt.type);}}); // Use native DOM manipulation to avoid our domManip AJAX trickery
	document.head.appendChild(script[0]);},abort:function abort(){if(callback){callback();}}};}});var oldCallbacks=[],rjsonp=/(=)\?(?=&|$)|\?\?/; // Default jsonp settings
	jQuery.ajaxSetup({jsonp:"callback",jsonpCallback:function jsonpCallback(){var callback=oldCallbacks.pop() || jQuery.expando + "_" + nonce++;this[callback] = true;return callback;}}); // Detect, normalize options and install callbacks for jsonp requests
	jQuery.ajaxPrefilter("json jsonp",function(s,originalSettings,jqXHR){var callbackName,overwritten,responseContainer,jsonProp=s.jsonp !== false && (rjsonp.test(s.url)?"url":typeof s.data === "string" && (s.contentType || "").indexOf("application/x-www-form-urlencoded") === 0 && rjsonp.test(s.data) && "data"); // Handle iff the expected data type is "jsonp" or we have a parameter to set
	if(jsonProp || s.dataTypes[0] === "jsonp"){ // Get callback name, remembering preexisting value associated with it
	callbackName = s.jsonpCallback = jQuery.isFunction(s.jsonpCallback)?s.jsonpCallback():s.jsonpCallback; // Insert callback into url or form data
	if(jsonProp){s[jsonProp] = s[jsonProp].replace(rjsonp,"$1" + callbackName);}else if(s.jsonp !== false){s.url += (rquery.test(s.url)?"&":"?") + s.jsonp + "=" + callbackName;} // Use data converter to retrieve json after script execution
	s.converters["script json"] = function(){if(!responseContainer){jQuery.error(callbackName + " was not called");}return responseContainer[0];}; // Force json dataType
	s.dataTypes[0] = "json"; // Install callback
	overwritten = window[callbackName];window[callbackName] = function(){responseContainer = arguments;}; // Clean-up function (fires after converters)
	jqXHR.always(function(){ // If previous value didn't exist - remove it
	if(overwritten === undefined){jQuery(window).removeProp(callbackName); // Otherwise restore preexisting value
	}else {window[callbackName] = overwritten;} // Save back as free
	if(s[callbackName]){ // Make sure that re-using the options doesn't screw things around
	s.jsonpCallback = originalSettings.jsonpCallback; // Save the callback name for future use
	oldCallbacks.push(callbackName);} // Call if it was a function and we have a response
	if(responseContainer && jQuery.isFunction(overwritten)){overwritten(responseContainer[0]);}responseContainer = overwritten = undefined;}); // Delegate to script
	return "script";}}); // Support: Safari 8+
	// In Safari 8 documents created via document.implementation.createHTMLDocument
	// collapse sibling forms: the second one becomes a child of the first one.
	// Because of that, this security measure has to be disabled in Safari 8.
	// https://bugs.webkit.org/show_bug.cgi?id=137337
	support.createHTMLDocument = (function(){var body=document.implementation.createHTMLDocument("").body;body.innerHTML = "<form></form><form></form>";return body.childNodes.length === 2;})(); // Argument "data" should be string of html
	// context (optional): If specified, the fragment will be created in this context,
	// defaults to document
	// keepScripts (optional): If true, will include scripts passed in the html string
	jQuery.parseHTML = function(data,context,keepScripts){if(!data || typeof data !== "string"){return null;}if(typeof context === "boolean"){keepScripts = context;context = false;} // Stop scripts or inline event handlers from being executed immediately
	// by using document.implementation
	context = context || (support.createHTMLDocument?document.implementation.createHTMLDocument(""):document);var parsed=rsingleTag.exec(data),scripts=!keepScripts && []; // Single tag
	if(parsed){return [context.createElement(parsed[1])];}parsed = buildFragment([data],context,scripts);if(scripts && scripts.length){jQuery(scripts).remove();}return jQuery.merge([],parsed.childNodes);}; // Keep a copy of the old load method
	var _load=jQuery.fn.load; /**
	 * Load a url into a page
	 */jQuery.fn.load = function(url,params,callback){if(typeof url !== "string" && _load){return _load.apply(this,arguments);}var selector,type,response,self=this,off=url.indexOf(" ");if(off > -1){selector = jQuery.trim(url.slice(off));url = url.slice(0,off);} // If it's a function
	if(jQuery.isFunction(params)){ // We assume that it's the callback
	callback = params;params = undefined; // Otherwise, build a param string
	}else if(params && typeof params === "object"){type = "POST";} // If we have elements to modify, make the request
	if(self.length > 0){jQuery.ajax({url:url, // If "type" variable is undefined, then "GET" method will be used.
	// Make value of this field explicit since
	// user can override it through ajaxSetup method
	type:type || "GET",dataType:"html",data:params}).done(function(responseText){ // Save response for use in complete callback
	response = arguments;self.html(selector? // If a selector was specified, locate the right elements in a dummy div
	// Exclude scripts to avoid IE 'Permission Denied' errors
	jQuery("<div>").append(jQuery.parseHTML(responseText)).find(selector): // Otherwise use the full result
	responseText); // If the request succeeds, this function gets "data", "status", "jqXHR"
	// but they are ignored because response was set above.
	// If it fails, this function gets "jqXHR", "status", "error"
	}).always(callback && function(jqXHR,status){self.each(function(){callback.apply(self,response || [jqXHR.responseText,status,jqXHR]);});});}return this;}; // Attach a bunch of functions for handling common AJAX events
	jQuery.each(["ajaxStart","ajaxStop","ajaxComplete","ajaxError","ajaxSuccess","ajaxSend"],function(i,type){jQuery.fn[type] = function(fn){return this.on(type,fn);};});jQuery.expr.filters.animated = function(elem){return jQuery.grep(jQuery.timers,function(fn){return elem === fn.elem;}).length;}; /**
	 * Gets a window from an element
	 */function getWindow(elem){return jQuery.isWindow(elem)?elem:elem.nodeType === 9 && elem.defaultView;}jQuery.offset = {setOffset:function setOffset(elem,options,i){var curPosition,curLeft,curCSSTop,curTop,curOffset,curCSSLeft,calculatePosition,position=jQuery.css(elem,"position"),curElem=jQuery(elem),props={}; // Set position first, in-case top/left are set even on static elem
	if(position === "static"){elem.style.position = "relative";}curOffset = curElem.offset();curCSSTop = jQuery.css(elem,"top");curCSSLeft = jQuery.css(elem,"left");calculatePosition = (position === "absolute" || position === "fixed") && (curCSSTop + curCSSLeft).indexOf("auto") > -1; // Need to be able to calculate position if either
	// top or left is auto and position is either absolute or fixed
	if(calculatePosition){curPosition = curElem.position();curTop = curPosition.top;curLeft = curPosition.left;}else {curTop = parseFloat(curCSSTop) || 0;curLeft = parseFloat(curCSSLeft) || 0;}if(jQuery.isFunction(options)){ // Use jQuery.extend here to allow modification of coordinates argument (gh-1848)
	options = options.call(elem,i,jQuery.extend({},curOffset));}if(options.top != null){props.top = options.top - curOffset.top + curTop;}if(options.left != null){props.left = options.left - curOffset.left + curLeft;}if("using" in options){options.using.call(elem,props);}else {curElem.css(props);}}};jQuery.fn.extend({offset:function offset(options){if(arguments.length){return options === undefined?this:this.each(function(i){jQuery.offset.setOffset(this,options,i);});}var docElem,win,elem=this[0],box={top:0,left:0},doc=elem && elem.ownerDocument;if(!doc){return;}docElem = doc.documentElement; // Make sure it's not a disconnected DOM node
	if(!jQuery.contains(docElem,elem)){return box;}box = elem.getBoundingClientRect();win = getWindow(doc);return {top:box.top + win.pageYOffset - docElem.clientTop,left:box.left + win.pageXOffset - docElem.clientLeft};},position:function position(){if(!this[0]){return;}var offsetParent,offset,elem=this[0],parentOffset={top:0,left:0}; // Fixed elements are offset from window (parentOffset = {top:0, left: 0},
	// because it is its only offset parent
	if(jQuery.css(elem,"position") === "fixed"){ // Assume getBoundingClientRect is there when computed position is fixed
	offset = elem.getBoundingClientRect();}else { // Get *real* offsetParent
	offsetParent = this.offsetParent(); // Get correct offsets
	offset = this.offset();if(!jQuery.nodeName(offsetParent[0],"html")){parentOffset = offsetParent.offset();} // Add offsetParent borders
	// Subtract offsetParent scroll positions
	parentOffset.top += jQuery.css(offsetParent[0],"borderTopWidth",true) - offsetParent.scrollTop();parentOffset.left += jQuery.css(offsetParent[0],"borderLeftWidth",true) - offsetParent.scrollLeft();} // Subtract parent offsets and element margins
	return {top:offset.top - parentOffset.top - jQuery.css(elem,"marginTop",true),left:offset.left - parentOffset.left - jQuery.css(elem,"marginLeft",true)};}, // This method will return documentElement in the following cases:
	// 1) For the element inside the iframe without offsetParent, this method will return
	//    documentElement of the parent window
	// 2) For the hidden or detached element
	// 3) For body or html element, i.e. in case of the html node - it will return itself
	//
	// but those exceptions were never presented as a real life use-cases
	// and might be considered as more preferable results.
	//
	// This logic, however, is not guaranteed and can change at any point in the future
	offsetParent:function offsetParent(){return this.map(function(){var offsetParent=this.offsetParent;while(offsetParent && jQuery.css(offsetParent,"position") === "static") {offsetParent = offsetParent.offsetParent;}return offsetParent || documentElement;});}}); // Create scrollLeft and scrollTop methods
	jQuery.each({scrollLeft:"pageXOffset",scrollTop:"pageYOffset"},function(method,prop){var top="pageYOffset" === prop;jQuery.fn[method] = function(val){return access(this,function(elem,method,val){var win=getWindow(elem);if(val === undefined){return win?win[prop]:elem[method];}if(win){win.scrollTo(!top?val:win.pageXOffset,top?val:win.pageYOffset);}else {elem[method] = val;}},method,val,arguments.length);};}); // Support: Safari<7-8+, Chrome<37-44+
	// Add the top/left cssHooks using jQuery.fn.position
	// Webkit bug: https://bugs.webkit.org/show_bug.cgi?id=29084
	// Blink bug: https://code.google.com/p/chromium/issues/detail?id=229280
	// getComputedStyle returns percent when specified for top/left/bottom/right;
	// rather than make the css module depend on the offset module, just check for it here
	jQuery.each(["top","left"],function(i,prop){jQuery.cssHooks[prop] = addGetHookIf(support.pixelPosition,function(elem,computed){if(computed){computed = curCSS(elem,prop); // If curCSS returns percentage, fallback to offset
	return rnumnonpx.test(computed)?jQuery(elem).position()[prop] + "px":computed;}});}); // Create innerHeight, innerWidth, height, width, outerHeight and outerWidth methods
	jQuery.each({Height:"height",Width:"width"},function(name,type){jQuery.each({padding:"inner" + name,content:type,"":"outer" + name},function(defaultExtra,funcName){ // Margin is only for outerHeight, outerWidth
	jQuery.fn[funcName] = function(margin,value){var chainable=arguments.length && (defaultExtra || typeof margin !== "boolean"),extra=defaultExtra || (margin === true || value === true?"margin":"border");return access(this,function(elem,type,value){var doc;if(jQuery.isWindow(elem)){ // As of 5/8/2012 this will yield incorrect results for Mobile Safari, but there
	// isn't a whole lot we can do. See pull request at this URL for discussion:
	// https://github.com/jquery/jquery/pull/764
	return elem.document.documentElement["client" + name];} // Get document width or height
	if(elem.nodeType === 9){doc = elem.documentElement; // Either scroll[Width/Height] or offset[Width/Height] or client[Width/Height],
	// whichever is greatest
	return Math.max(elem.body["scroll" + name],doc["scroll" + name],elem.body["offset" + name],doc["offset" + name],doc["client" + name]);}return value === undefined? // Get width or height on the element, requesting but not forcing parseFloat
	jQuery.css(elem,type,extra): // Set width or height on the element
	jQuery.style(elem,type,value,extra);},type,chainable?margin:undefined,chainable,null);};});});jQuery.fn.extend({bind:function bind(types,data,fn){return this.on(types,null,data,fn);},unbind:function unbind(types,fn){return this.off(types,null,fn);},delegate:function delegate(selector,types,data,fn){return this.on(types,selector,data,fn);},undelegate:function undelegate(selector,types,fn){ // ( namespace ) or ( selector, types [, fn] )
	return arguments.length === 1?this.off(selector,"**"):this.off(types,selector || "**",fn);},size:function size(){return this.length;}});jQuery.fn.andSelf = jQuery.fn.addBack; // Register as a named AMD module, since jQuery can be concatenated with other
	// files that may use define, but not via a proper concatenation script that
	// understands anonymous AMD modules. A named AMD is safest and most robust
	// way to register. Lowercase jquery is used because AMD module names are
	// derived from file names, and jQuery is normally delivered in a lowercase
	// file name. Do this after creating the global so that if an AMD module wants
	// to call noConflict to hide this version of jQuery, it will work.
	// Note that for maximum portability, libraries that are not jQuery should
	// declare themselves as anonymous modules, and avoid setting a global if an
	// AMD loader is present. jQuery is a special case. For more information, see
	// https://github.com/jrburke/requirejs/wiki/Updating-existing-libraries#wiki-anon
	if(true){!(__WEBPACK_AMD_DEFINE_ARRAY__ = [], __WEBPACK_AMD_DEFINE_RESULT__ = function(){return jQuery;}.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__), __WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));}var  // Map over jQuery in case of overwrite
	_jQuery=window.jQuery, // Map over the $ in case of overwrite
	_$=window.$;jQuery.noConflict = function(deep){if(window.$ === jQuery){window.$ = _$;}if(deep && window.jQuery === jQuery){window.jQuery = _jQuery;}return jQuery;}; // Expose jQuery and $ identifiers, even in AMD
	// (#7102#comment:10, https://github.com/jquery/jquery/pull/557)
	// and CommonJS for browser emulators (#13566)
	if(!noGlobal){window.jQuery = window.$ = jQuery;}return jQuery;}); // Otherwise append directly

/***/ },
/* 5 */
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function(global) {module.exports = global["Tether"] = __webpack_require__(6);
	/* WEBPACK VAR INJECTION */}.call(exports, (function() { return this; }())))

/***/ },
/* 6 */
/***/ function(module, exports, __webpack_require__) {

	var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_RESULT__;/*! tether 1.1.0 */

	'use strict';

	(function (root, factory) {
	  if (true) {
	    !(__WEBPACK_AMD_DEFINE_FACTORY__ = (factory), __WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ? (__WEBPACK_AMD_DEFINE_FACTORY__.call(exports, __webpack_require__, exports, module)) : __WEBPACK_AMD_DEFINE_FACTORY__), __WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
	  } else if (typeof exports === 'object') {
	    module.exports = factory(require, exports, module);
	  } else {
	    root.Tether = factory();
	  }
	})(undefined, function (require, exports, module) {

	  'use strict';

	  var _createClass = (function () {
	    function defineProperties(target, props) {
	      for (var i = 0; i < props.length; i++) {
	        var descriptor = props[i];descriptor.enumerable = descriptor.enumerable || false;descriptor.configurable = true;if ('value' in descriptor) descriptor.writable = true;Object.defineProperty(target, descriptor.key, descriptor);
	      }
	    }return function (Constructor, protoProps, staticProps) {
	      if (protoProps) defineProperties(Constructor.prototype, protoProps);if (staticProps) defineProperties(Constructor, staticProps);return Constructor;
	    };
	  })();

	  function _classCallCheck(instance, Constructor) {
	    if (!(instance instanceof Constructor)) {
	      throw new TypeError('Cannot call a class as a function');
	    }
	  }

	  var TetherBase = undefined;
	  if (typeof TetherBase === 'undefined') {
	    TetherBase = { modules: [] };
	  }

	  function getScrollParent(el) {
	    var _getComputedStyle = getComputedStyle(el);

	    var position = _getComputedStyle.position;

	    if (position === 'fixed') {
	      return el;
	    }

	    var parent = el;
	    while (parent = parent.parentNode) {
	      var style = undefined;
	      try {
	        style = getComputedStyle(parent);
	      } catch (err) {}

	      if (typeof style === 'undefined' || style === null) {
	        return parent;
	      }

	      var _style = style;
	      var overflow = _style.overflow;
	      var overflowX = _style.overflowX;
	      var overflowY = _style.overflowY;

	      if (/(auto|scroll)/.test(overflow + overflowY + overflowX)) {
	        if (position !== 'absolute' || ['relative', 'absolute', 'fixed'].indexOf(style.position) >= 0) {
	          return parent;
	        }
	      }
	    }

	    return document.body;
	  }

	  var uniqueId = (function () {
	    var id = 0;
	    return function () {
	      return ++id;
	    };
	  })();

	  var zeroPosCache = {};
	  var getOrigin = function getOrigin(doc) {
	    // getBoundingClientRect is unfortunately too accurate.  It introduces a pixel or two of
	    // jitter as the user scrolls that messes with our ability to detect if two positions
	    // are equivilant or not.  We place an element at the top left of the page that will
	    // get the same jitter, so we can cancel the two out.
	    var node = doc._tetherZeroElement;
	    if (typeof node === 'undefined') {
	      node = doc.createElement('div');
	      node.setAttribute('data-tether-id', uniqueId());
	      extend(node.style, {
	        top: 0,
	        left: 0,
	        position: 'absolute'
	      });

	      doc.body.appendChild(node);

	      doc._tetherZeroElement = node;
	    }

	    var id = node.getAttribute('data-tether-id');
	    if (typeof zeroPosCache[id] === 'undefined') {
	      zeroPosCache[id] = {};

	      var rect = node.getBoundingClientRect();
	      for (var k in rect) {
	        // Can't use extend, as on IE9, elements don't resolve to be hasOwnProperty
	        zeroPosCache[id][k] = rect[k];
	      }

	      // Clear the cache when this position call is done
	      defer(function () {
	        delete zeroPosCache[id];
	      });
	    }

	    return zeroPosCache[id];
	  };

	  function getBounds(el) {
	    var doc = undefined;
	    if (el === document) {
	      doc = document;
	      el = document.documentElement;
	    } else {
	      doc = el.ownerDocument;
	    }

	    var docEl = doc.documentElement;

	    var box = {};
	    // The original object returned by getBoundingClientRect is immutable, so we clone it
	    // We can't use extend because the properties are not considered part of the object by hasOwnProperty in IE9
	    var rect = el.getBoundingClientRect();
	    for (var k in rect) {
	      box[k] = rect[k];
	    }

	    var origin = getOrigin(doc);

	    box.top -= origin.top;
	    box.left -= origin.left;

	    if (typeof box.width === 'undefined') {
	      box.width = document.body.scrollWidth - box.left - box.right;
	    }
	    if (typeof box.height === 'undefined') {
	      box.height = document.body.scrollHeight - box.top - box.bottom;
	    }

	    box.top = box.top - docEl.clientTop;
	    box.left = box.left - docEl.clientLeft;
	    box.right = doc.body.clientWidth - box.width - box.left;
	    box.bottom = doc.body.clientHeight - box.height - box.top;

	    return box;
	  }

	  function getOffsetParent(el) {
	    return el.offsetParent || document.documentElement;
	  }

	  function getScrollBarSize() {
	    var inner = document.createElement('div');
	    inner.style.width = '100%';
	    inner.style.height = '200px';

	    var outer = document.createElement('div');
	    extend(outer.style, {
	      position: 'absolute',
	      top: 0,
	      left: 0,
	      pointerEvents: 'none',
	      visibility: 'hidden',
	      width: '200px',
	      height: '150px',
	      overflow: 'hidden'
	    });

	    outer.appendChild(inner);

	    document.body.appendChild(outer);

	    var widthContained = inner.offsetWidth;
	    outer.style.overflow = 'scroll';
	    var widthScroll = inner.offsetWidth;

	    if (widthContained === widthScroll) {
	      widthScroll = outer.clientWidth;
	    }

	    document.body.removeChild(outer);

	    var width = widthContained - widthScroll;

	    return { width: width, height: width };
	  }

	  function extend() {
	    var out = arguments.length <= 0 || arguments[0] === undefined ? {} : arguments[0];

	    var args = [];

	    Array.prototype.push.apply(args, arguments);

	    args.slice(1).forEach(function (obj) {
	      if (obj) {
	        for (var key in obj) {
	          if (({}).hasOwnProperty.call(obj, key)) {
	            out[key] = obj[key];
	          }
	        }
	      }
	    });

	    return out;
	  }

	  function removeClass(el, name) {
	    if (typeof el.classList !== 'undefined') {
	      name.split(' ').forEach(function (cls) {
	        if (cls.trim()) {
	          el.classList.remove(cls);
	        }
	      });
	    } else {
	      var regex = new RegExp('(^| )' + name.split(' ').join('|') + '( |$)', 'gi');
	      var className = getClassName(el).replace(regex, ' ');
	      setClassName(el, className);
	    }
	  }

	  function addClass(el, name) {
	    if (typeof el.classList !== 'undefined') {
	      name.split(' ').forEach(function (cls) {
	        if (cls.trim()) {
	          el.classList.add(cls);
	        }
	      });
	    } else {
	      removeClass(el, name);
	      var cls = getClassName(el) + (' ' + name);
	      setClassName(el, cls);
	    }
	  }

	  function hasClass(el, name) {
	    if (typeof el.classList !== 'undefined') {
	      return el.classList.contains(name);
	    }
	    var className = getClassName(el);
	    return new RegExp('(^| )' + name + '( |$)', 'gi').test(className);
	  }

	  function getClassName(el) {
	    if (el.className instanceof SVGAnimatedString) {
	      return el.className.baseVal;
	    }
	    return el.className;
	  }

	  function setClassName(el, className) {
	    el.setAttribute('class', className);
	  }

	  function updateClasses(el, add, all) {
	    // Of the set of 'all' classes, we need the 'add' classes, and only the
	    // 'add' classes to be set.
	    all.forEach(function (cls) {
	      if (add.indexOf(cls) === -1 && hasClass(el, cls)) {
	        removeClass(el, cls);
	      }
	    });

	    add.forEach(function (cls) {
	      if (!hasClass(el, cls)) {
	        addClass(el, cls);
	      }
	    });
	  }

	  var deferred = [];

	  var defer = function defer(fn) {
	    deferred.push(fn);
	  };

	  var flush = function flush() {
	    var fn = undefined;
	    while (fn = deferred.pop()) {
	      fn();
	    }
	  };

	  var Evented = (function () {
	    function Evented() {
	      _classCallCheck(this, Evented);
	    }

	    _createClass(Evented, [{
	      key: 'on',
	      value: function on(event, handler, ctx) {
	        var once = arguments.length <= 3 || arguments[3] === undefined ? false : arguments[3];

	        if (typeof this.bindings === 'undefined') {
	          this.bindings = {};
	        }
	        if (typeof this.bindings[event] === 'undefined') {
	          this.bindings[event] = [];
	        }
	        this.bindings[event].push({ handler: handler, ctx: ctx, once: once });
	      }
	    }, {
	      key: 'once',
	      value: function once(event, handler, ctx) {
	        this.on(event, handler, ctx, true);
	      }
	    }, {
	      key: 'off',
	      value: function off(event, handler) {
	        if (typeof this.bindings !== 'undefined' && typeof this.bindings[event] !== 'undefined') {
	          return;
	        }

	        if (typeof handler === 'undefined') {
	          delete this.bindings[event];
	        } else {
	          var i = 0;
	          while (i < this.bindings[event].length) {
	            if (this.bindings[event][i].handler === handler) {
	              this.bindings[event].splice(i, 1);
	            } else {
	              ++i;
	            }
	          }
	        }
	      }
	    }, {
	      key: 'trigger',
	      value: function trigger(event) {
	        if (typeof this.bindings !== 'undefined' && this.bindings[event]) {
	          var i = 0;

	          for (var _len = arguments.length, args = Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++) {
	            args[_key - 1] = arguments[_key];
	          }

	          while (i < this.bindings[event].length) {
	            var _bindings$event$i = this.bindings[event][i];
	            var handler = _bindings$event$i.handler;
	            var ctx = _bindings$event$i.ctx;
	            var once = _bindings$event$i.once;

	            var context = ctx;
	            if (typeof context === 'undefined') {
	              context = this;
	            }

	            handler.apply(context, args);

	            if (once) {
	              this.bindings[event].splice(i, 1);
	            } else {
	              ++i;
	            }
	          }
	        }
	      }
	    }]);

	    return Evented;
	  })();

	  TetherBase.Utils = {
	    getScrollParent: getScrollParent,
	    getBounds: getBounds,
	    getOffsetParent: getOffsetParent,
	    extend: extend,
	    addClass: addClass,
	    removeClass: removeClass,
	    hasClass: hasClass,
	    updateClasses: updateClasses,
	    defer: defer,
	    flush: flush,
	    uniqueId: uniqueId,
	    Evented: Evented,
	    getScrollBarSize: getScrollBarSize
	  };
	  /* globals TetherBase, performance */

	  'use strict';

	  var _slicedToArray = (function () {
	    function sliceIterator(arr, i) {
	      var _arr = [];var _n = true;var _d = false;var _e = undefined;try {
	        for (var _i = arr[Symbol.iterator](), _s; !(_n = (_s = _i.next()).done); _n = true) {
	          _arr.push(_s.value);if (i && _arr.length === i) break;
	        }
	      } catch (err) {
	        _d = true;_e = err;
	      } finally {
	        try {
	          if (!_n && _i['return']) _i['return']();
	        } finally {
	          if (_d) throw _e;
	        }
	      }return _arr;
	    }return function (arr, i) {
	      if (Array.isArray(arr)) {
	        return arr;
	      } else if (Symbol.iterator in Object(arr)) {
	        return sliceIterator(arr, i);
	      } else {
	        throw new TypeError('Invalid attempt to destructure non-iterable instance');
	      }
	    };
	  })();

	  var _createClass = (function () {
	    function defineProperties(target, props) {
	      for (var i = 0; i < props.length; i++) {
	        var descriptor = props[i];descriptor.enumerable = descriptor.enumerable || false;descriptor.configurable = true;if ('value' in descriptor) descriptor.writable = true;Object.defineProperty(target, descriptor.key, descriptor);
	      }
	    }return function (Constructor, protoProps, staticProps) {
	      if (protoProps) defineProperties(Constructor.prototype, protoProps);if (staticProps) defineProperties(Constructor, staticProps);return Constructor;
	    };
	  })();

	  function _classCallCheck(instance, Constructor) {
	    if (!(instance instanceof Constructor)) {
	      throw new TypeError('Cannot call a class as a function');
	    }
	  }

	  if (typeof TetherBase === 'undefined') {
	    throw new Error('You must include the utils.js file before tether.js');
	  }

	  var _TetherBase$Utils = TetherBase.Utils;
	  var getScrollParent = _TetherBase$Utils.getScrollParent;
	  var getBounds = _TetherBase$Utils.getBounds;
	  var getOffsetParent = _TetherBase$Utils.getOffsetParent;
	  var extend = _TetherBase$Utils.extend;
	  var addClass = _TetherBase$Utils.addClass;
	  var removeClass = _TetherBase$Utils.removeClass;
	  var updateClasses = _TetherBase$Utils.updateClasses;
	  var defer = _TetherBase$Utils.defer;
	  var flush = _TetherBase$Utils.flush;
	  var getScrollBarSize = _TetherBase$Utils.getScrollBarSize;

	  function within(a, b) {
	    var diff = arguments.length <= 2 || arguments[2] === undefined ? 1 : arguments[2];

	    return a + diff >= b && b >= a - diff;
	  }

	  var transformKey = (function () {
	    if (typeof document === 'undefined') {
	      return '';
	    }
	    var el = document.createElement('div');

	    var transforms = ['transform', 'webkitTransform', 'OTransform', 'MozTransform', 'msTransform'];
	    for (var i = 0; i < transforms.length; ++i) {
	      var key = transforms[i];
	      if (el.style[key] !== undefined) {
	        return key;
	      }
	    }
	  })();

	  var tethers = [];

	  var position = function position() {
	    tethers.forEach(function (tether) {
	      tether.position(false);
	    });
	    flush();
	  };

	  function now() {
	    if (typeof performance !== 'undefined' && typeof performance.now !== 'undefined') {
	      return performance.now();
	    }
	    return +new Date();
	  }

	  (function () {
	    var lastCall = null;
	    var lastDuration = null;
	    var pendingTimeout = null;

	    var tick = function tick() {
	      if (typeof lastDuration !== 'undefined' && lastDuration > 16) {
	        // We voluntarily throttle ourselves if we can't manage 60fps
	        lastDuration = Math.min(lastDuration - 16, 250);

	        // Just in case this is the last event, remember to position just once more
	        pendingTimeout = setTimeout(tick, 250);
	        return;
	      }

	      if (typeof lastCall !== 'undefined' && now() - lastCall < 10) {
	        // Some browsers call events a little too frequently, refuse to run more than is reasonable
	        return;
	      }

	      if (typeof pendingTimeout !== 'undefined') {
	        clearTimeout(pendingTimeout);
	        pendingTimeout = null;
	      }

	      lastCall = now();
	      position();
	      lastDuration = now() - lastCall;
	    };

	    if (typeof window !== 'undefined') {
	      ['resize', 'scroll', 'touchmove'].forEach(function (event) {
	        window.addEventListener(event, tick);
	      });
	    }
	  })();

	  var MIRROR_LR = {
	    center: 'center',
	    left: 'right',
	    right: 'left'
	  };

	  var MIRROR_TB = {
	    middle: 'middle',
	    top: 'bottom',
	    bottom: 'top'
	  };

	  var OFFSET_MAP = {
	    top: 0,
	    left: 0,
	    middle: '50%',
	    center: '50%',
	    bottom: '100%',
	    right: '100%'
	  };

	  var autoToFixedAttachment = function autoToFixedAttachment(attachment, relativeToAttachment) {
	    var left = attachment.left;
	    var top = attachment.top;

	    if (left === 'auto') {
	      left = MIRROR_LR[relativeToAttachment.left];
	    }

	    if (top === 'auto') {
	      top = MIRROR_TB[relativeToAttachment.top];
	    }

	    return { left: left, top: top };
	  };

	  var attachmentToOffset = function attachmentToOffset(attachment) {
	    var left = attachment.left;
	    var top = attachment.top;

	    if (typeof OFFSET_MAP[attachment.left] !== 'undefined') {
	      left = OFFSET_MAP[attachment.left];
	    }

	    if (typeof OFFSET_MAP[attachment.top] !== 'undefined') {
	      top = OFFSET_MAP[attachment.top];
	    }

	    return { left: left, top: top };
	  };

	  function addOffset() {
	    var out = { top: 0, left: 0 };

	    for (var _len = arguments.length, offsets = Array(_len), _key = 0; _key < _len; _key++) {
	      offsets[_key] = arguments[_key];
	    }

	    offsets.forEach(function (_ref) {
	      var top = _ref.top;
	      var left = _ref.left;

	      if (typeof top === 'string') {
	        top = parseFloat(top, 10);
	      }
	      if (typeof left === 'string') {
	        left = parseFloat(left, 10);
	      }

	      out.top += top;
	      out.left += left;
	    });

	    return out;
	  }

	  function offsetToPx(offset, size) {
	    if (typeof offset.left === 'string' && offset.left.indexOf('%') !== -1) {
	      offset.left = parseFloat(offset.left, 10) / 100 * size.width;
	    }
	    if (typeof offset.top === 'string' && offset.top.indexOf('%') !== -1) {
	      offset.top = parseFloat(offset.top, 10) / 100 * size.height;
	    }

	    return offset;
	  }

	  var parseOffset = function parseOffset(value) {
	    var _value$split = value.split(' ');

	    var _value$split2 = _slicedToArray(_value$split, 2);

	    var top = _value$split2[0];
	    var left = _value$split2[1];

	    return { top: top, left: left };
	  };
	  var parseAttachment = parseOffset;

	  var TetherClass = (function () {
	    function TetherClass(options) {
	      var _this = this;

	      _classCallCheck(this, TetherClass);

	      this.position = this.position.bind(this);

	      tethers.push(this);

	      this.history = [];

	      this.setOptions(options, false);

	      TetherBase.modules.forEach(function (module) {
	        if (typeof module.initialize !== 'undefined') {
	          module.initialize.call(_this);
	        }
	      });

	      this.position();
	    }

	    _createClass(TetherClass, [{
	      key: 'getClass',
	      value: function getClass() {
	        var key = arguments.length <= 0 || arguments[0] === undefined ? '' : arguments[0];
	        var classes = this.options.classes;

	        if (typeof classes !== 'undefined' && classes[key]) {
	          return this.options.classes[key];
	        } else if (this.options.classPrefix) {
	          return this.options.classPrefix + '-' + key;
	        } else {
	          return key;
	        }
	      }
	    }, {
	      key: 'setOptions',
	      value: function setOptions(options) {
	        var _this2 = this;

	        var pos = arguments.length <= 1 || arguments[1] === undefined ? true : arguments[1];

	        var defaults = {
	          offset: '0 0',
	          targetOffset: '0 0',
	          targetAttachment: 'auto auto',
	          classPrefix: 'tether'
	        };

	        this.options = extend(defaults, options);

	        var _options = this.options;
	        var element = _options.element;
	        var target = _options.target;
	        var targetModifier = _options.targetModifier;

	        this.element = element;
	        this.target = target;
	        this.targetModifier = targetModifier;

	        if (this.target === 'viewport') {
	          this.target = document.body;
	          this.targetModifier = 'visible';
	        } else if (this.target === 'scroll-handle') {
	          this.target = document.body;
	          this.targetModifier = 'scroll-handle';
	        }

	        ['element', 'target'].forEach(function (key) {
	          if (typeof _this2[key] === 'undefined') {
	            throw new Error('Tether Error: Both element and target must be defined');
	          }

	          if (typeof _this2[key].jquery !== 'undefined') {
	            _this2[key] = _this2[key][0];
	          } else if (typeof _this2[key] === 'string') {
	            _this2[key] = document.querySelector(_this2[key]);
	          }
	        });

	        addClass(this.element, this.getClass('element'));
	        if (!(this.options.addTargetClasses === false)) {
	          addClass(this.target, this.getClass('target'));
	        }

	        if (!this.options.attachment) {
	          throw new Error('Tether Error: You must provide an attachment');
	        }

	        this.targetAttachment = parseAttachment(this.options.targetAttachment);
	        this.attachment = parseAttachment(this.options.attachment);
	        this.offset = parseOffset(this.options.offset);
	        this.targetOffset = parseOffset(this.options.targetOffset);

	        if (typeof this.scrollParent !== 'undefined') {
	          this.disable();
	        }

	        if (this.targetModifier === 'scroll-handle') {
	          this.scrollParent = this.target;
	        } else {
	          this.scrollParent = getScrollParent(this.target);
	        }

	        if (!(this.options.enabled === false)) {
	          this.enable(pos);
	        }
	      }
	    }, {
	      key: 'getTargetBounds',
	      value: function getTargetBounds() {
	        if (typeof this.targetModifier !== 'undefined') {
	          if (this.targetModifier === 'visible') {
	            if (this.target === document.body) {
	              return { top: pageYOffset, left: pageXOffset, height: innerHeight, width: innerWidth };
	            } else {
	              var bounds = getBounds(this.target);

	              var out = {
	                height: bounds.height,
	                width: bounds.width,
	                top: bounds.top,
	                left: bounds.left
	              };

	              out.height = Math.min(out.height, bounds.height - (pageYOffset - bounds.top));
	              out.height = Math.min(out.height, bounds.height - (bounds.top + bounds.height - (pageYOffset + innerHeight)));
	              out.height = Math.min(innerHeight, out.height);
	              out.height -= 2;

	              out.width = Math.min(out.width, bounds.width - (pageXOffset - bounds.left));
	              out.width = Math.min(out.width, bounds.width - (bounds.left + bounds.width - (pageXOffset + innerWidth)));
	              out.width = Math.min(innerWidth, out.width);
	              out.width -= 2;

	              if (out.top < pageYOffset) {
	                out.top = pageYOffset;
	              }
	              if (out.left < pageXOffset) {
	                out.left = pageXOffset;
	              }

	              return out;
	            }
	          } else if (this.targetModifier === 'scroll-handle') {
	            var bounds = undefined;
	            var target = this.target;
	            if (target === document.body) {
	              target = document.documentElement;

	              bounds = {
	                left: pageXOffset,
	                top: pageYOffset,
	                height: innerHeight,
	                width: innerWidth
	              };
	            } else {
	              bounds = getBounds(target);
	            }

	            var style = getComputedStyle(target);

	            var hasBottomScroll = target.scrollWidth > target.clientWidth || [style.overflow, style.overflowX].indexOf('scroll') >= 0 || this.target !== document.body;

	            var scrollBottom = 0;
	            if (hasBottomScroll) {
	              scrollBottom = 15;
	            }

	            var height = bounds.height - parseFloat(style.borderTopWidth) - parseFloat(style.borderBottomWidth) - scrollBottom;

	            var out = {
	              width: 15,
	              height: height * 0.975 * (height / target.scrollHeight),
	              left: bounds.left + bounds.width - parseFloat(style.borderLeftWidth) - 15
	            };

	            var fitAdj = 0;
	            if (height < 408 && this.target === document.body) {
	              fitAdj = -0.00011 * Math.pow(height, 2) - 0.00727 * height + 22.58;
	            }

	            if (this.target !== document.body) {
	              out.height = Math.max(out.height, 24);
	            }

	            var scrollPercentage = this.target.scrollTop / (target.scrollHeight - height);
	            out.top = scrollPercentage * (height - out.height - fitAdj) + bounds.top + parseFloat(style.borderTopWidth);

	            if (this.target === document.body) {
	              out.height = Math.max(out.height, 24);
	            }

	            return out;
	          }
	        } else {
	          return getBounds(this.target);
	        }
	      }
	    }, {
	      key: 'clearCache',
	      value: function clearCache() {
	        this._cache = {};
	      }
	    }, {
	      key: 'cache',
	      value: function cache(k, getter) {
	        // More than one module will often need the same DOM info, so
	        // we keep a cache which is cleared on each position call
	        if (typeof this._cache === 'undefined') {
	          this._cache = {};
	        }

	        if (typeof this._cache[k] === 'undefined') {
	          this._cache[k] = getter.call(this);
	        }

	        return this._cache[k];
	      }
	    }, {
	      key: 'enable',
	      value: function enable() {
	        var pos = arguments.length <= 0 || arguments[0] === undefined ? true : arguments[0];

	        if (!(this.options.addTargetClasses === false)) {
	          addClass(this.target, this.getClass('enabled'));
	        }
	        addClass(this.element, this.getClass('enabled'));
	        this.enabled = true;

	        if (this.scrollParent !== document) {
	          this.scrollParent.addEventListener('scroll', this.position);
	        }

	        if (pos) {
	          this.position();
	        }
	      }
	    }, {
	      key: 'disable',
	      value: function disable() {
	        removeClass(this.target, this.getClass('enabled'));
	        removeClass(this.element, this.getClass('enabled'));
	        this.enabled = false;

	        if (typeof this.scrollParent !== 'undefined') {
	          this.scrollParent.removeEventListener('scroll', this.position);
	        }
	      }
	    }, {
	      key: 'destroy',
	      value: function destroy() {
	        var _this3 = this;

	        this.disable();

	        tethers.forEach(function (tether, i) {
	          if (tether === _this3) {
	            tethers.splice(i, 1);
	            return;
	          }
	        });
	      }
	    }, {
	      key: 'updateAttachClasses',
	      value: function updateAttachClasses(elementAttach, targetAttach) {
	        var _this4 = this;

	        elementAttach = elementAttach || this.attachment;
	        targetAttach = targetAttach || this.targetAttachment;
	        var sides = ['left', 'top', 'bottom', 'right', 'middle', 'center'];

	        if (typeof this._addAttachClasses !== 'undefined' && this._addAttachClasses.length) {
	          // updateAttachClasses can be called more than once in a position call, so
	          // we need to clean up after ourselves such that when the last defer gets
	          // ran it doesn't add any extra classes from previous calls.
	          this._addAttachClasses.splice(0, this._addAttachClasses.length);
	        }

	        if (typeof this._addAttachClasses === 'undefined') {
	          this._addAttachClasses = [];
	        }
	        var add = this._addAttachClasses;

	        if (elementAttach.top) {
	          add.push(this.getClass('element-attached') + '-' + elementAttach.top);
	        }
	        if (elementAttach.left) {
	          add.push(this.getClass('element-attached') + '-' + elementAttach.left);
	        }
	        if (targetAttach.top) {
	          add.push(this.getClass('target-attached') + '-' + targetAttach.top);
	        }
	        if (targetAttach.left) {
	          add.push(this.getClass('target-attached') + '-' + targetAttach.left);
	        }

	        var all = [];
	        sides.forEach(function (side) {
	          all.push(_this4.getClass('element-attached') + '-' + side);
	          all.push(_this4.getClass('target-attached') + '-' + side);
	        });

	        defer(function () {
	          if (!(typeof _this4._addAttachClasses !== 'undefined')) {
	            return;
	          }

	          updateClasses(_this4.element, _this4._addAttachClasses, all);
	          if (!(_this4.options.addTargetClasses === false)) {
	            updateClasses(_this4.target, _this4._addAttachClasses, all);
	          }

	          delete _this4._addAttachClasses;
	        });
	      }
	    }, {
	      key: 'position',
	      value: function position() {
	        var _this5 = this;

	        var flushChanges = arguments.length <= 0 || arguments[0] === undefined ? true : arguments[0];

	        // flushChanges commits the changes immediately, leave true unless you are positioning multiple
	        // tethers (in which case call Tether.Utils.flush yourself when you're done)

	        if (!this.enabled) {
	          return;
	        }

	        this.clearCache();

	        // Turn 'auto' attachments into the appropriate corner or edge
	        var targetAttachment = autoToFixedAttachment(this.targetAttachment, this.attachment);

	        this.updateAttachClasses(this.attachment, targetAttachment);

	        var elementPos = this.cache('element-bounds', function () {
	          return getBounds(_this5.element);
	        });

	        var width = elementPos.width;
	        var height = elementPos.height;

	        if (width === 0 && height === 0 && typeof this.lastSize !== 'undefined') {
	          var _lastSize = this.lastSize;

	          // We cache the height and width to make it possible to position elements that are
	          // getting hidden.
	          width = _lastSize.width;
	          height = _lastSize.height;
	        } else {
	          this.lastSize = { width: width, height: height };
	        }

	        var targetPos = this.cache('target-bounds', function () {
	          return _this5.getTargetBounds();
	        });
	        var targetSize = targetPos;

	        // Get an actual px offset from the attachment
	        var offset = offsetToPx(attachmentToOffset(this.attachment), { width: width, height: height });
	        var targetOffset = offsetToPx(attachmentToOffset(targetAttachment), targetSize);

	        var manualOffset = offsetToPx(this.offset, { width: width, height: height });
	        var manualTargetOffset = offsetToPx(this.targetOffset, targetSize);

	        // Add the manually provided offset
	        offset = addOffset(offset, manualOffset);
	        targetOffset = addOffset(targetOffset, manualTargetOffset);

	        // It's now our goal to make (element position + offset) == (target position + target offset)
	        var left = targetPos.left + targetOffset.left - offset.left;
	        var top = targetPos.top + targetOffset.top - offset.top;

	        for (var i = 0; i < TetherBase.modules.length; ++i) {
	          var _module2 = TetherBase.modules[i];
	          var ret = _module2.position.call(this, {
	            left: left,
	            top: top,
	            targetAttachment: targetAttachment,
	            targetPos: targetPos,
	            elementPos: elementPos,
	            offset: offset,
	            targetOffset: targetOffset,
	            manualOffset: manualOffset,
	            manualTargetOffset: manualTargetOffset,
	            scrollbarSize: scrollbarSize,
	            attachment: this.attachment
	          });

	          if (ret === false) {
	            return false;
	          } else if (typeof ret === 'undefined' || typeof ret !== 'object') {
	            continue;
	          } else {
	            top = ret.top;
	            left = ret.left;
	          }
	        }

	        // We describe the position three different ways to give the optimizer
	        // a chance to decide the best possible way to position the element
	        // with the fewest repaints.
	        var next = {
	          // It's position relative to the page (absolute positioning when
	          // the element is a child of the body)
	          page: {
	            top: top,
	            left: left
	          },

	          // It's position relative to the viewport (fixed positioning)
	          viewport: {
	            top: top - pageYOffset,
	            bottom: pageYOffset - top - height + innerHeight,
	            left: left - pageXOffset,
	            right: pageXOffset - left - width + innerWidth
	          }
	        };

	        var scrollbarSize = undefined;
	        if (document.body.scrollWidth > window.innerWidth) {
	          scrollbarSize = this.cache('scrollbar-size', getScrollBarSize);
	          next.viewport.bottom -= scrollbarSize.height;
	        }

	        if (document.body.scrollHeight > window.innerHeight) {
	          scrollbarSize = this.cache('scrollbar-size', getScrollBarSize);
	          next.viewport.right -= scrollbarSize.width;
	        }

	        if (['', 'static'].indexOf(document.body.style.position) === -1 || ['', 'static'].indexOf(document.body.parentElement.style.position) === -1) {
	          // Absolute positioning in the body will be relative to the page, not the 'initial containing block'
	          next.page.bottom = document.body.scrollHeight - top - height;
	          next.page.right = document.body.scrollWidth - left - width;
	        }

	        if (typeof this.options.optimizations !== 'undefined' && this.options.optimizations.moveElement !== false && !(typeof this.targetModifier !== 'undefined')) {
	          (function () {
	            var offsetParent = _this5.cache('target-offsetparent', function () {
	              return getOffsetParent(_this5.target);
	            });
	            var offsetPosition = _this5.cache('target-offsetparent-bounds', function () {
	              return getBounds(offsetParent);
	            });
	            var offsetParentStyle = getComputedStyle(offsetParent);
	            var offsetParentSize = offsetPosition;

	            var offsetBorder = {};
	            ['Top', 'Left', 'Bottom', 'Right'].forEach(function (side) {
	              offsetBorder[side.toLowerCase()] = parseFloat(offsetParentStyle['border' + side + 'Width']);
	            });

	            offsetPosition.right = document.body.scrollWidth - offsetPosition.left - offsetParentSize.width + offsetBorder.right;
	            offsetPosition.bottom = document.body.scrollHeight - offsetPosition.top - offsetParentSize.height + offsetBorder.bottom;

	            if (next.page.top >= offsetPosition.top + offsetBorder.top && next.page.bottom >= offsetPosition.bottom) {
	              if (next.page.left >= offsetPosition.left + offsetBorder.left && next.page.right >= offsetPosition.right) {
	                // We're within the visible part of the target's scroll parent
	                var scrollTop = offsetParent.scrollTop;
	                var scrollLeft = offsetParent.scrollLeft;

	                // It's position relative to the target's offset parent (absolute positioning when
	                // the element is moved to be a child of the target's offset parent).
	                next.offset = {
	                  top: next.page.top - offsetPosition.top + scrollTop - offsetBorder.top,
	                  left: next.page.left - offsetPosition.left + scrollLeft - offsetBorder.left
	                };
	              }
	            }
	          })();
	        }

	        // We could also travel up the DOM and try each containing context, rather than only
	        // looking at the body, but we're gonna get diminishing returns.

	        this.move(next);

	        this.history.unshift(next);

	        if (this.history.length > 3) {
	          this.history.pop();
	        }

	        if (flushChanges) {
	          flush();
	        }

	        return true;
	      }

	      // THE ISSUE
	    }, {
	      key: 'move',
	      value: function move(pos) {
	        var _this6 = this;

	        if (!(typeof this.element.parentNode !== 'undefined')) {
	          return;
	        }

	        var same = {};

	        for (var type in pos) {
	          same[type] = {};

	          for (var key in pos[type]) {
	            var found = false;

	            for (var i = 0; i < this.history.length; ++i) {
	              var point = this.history[i];
	              if (typeof point[type] !== 'undefined' && !within(point[type][key], pos[type][key])) {
	                found = true;
	                break;
	              }
	            }

	            if (!found) {
	              same[type][key] = true;
	            }
	          }
	        }

	        var css = { top: '', left: '', right: '', bottom: '' };

	        var transcribe = function transcribe(_same, _pos) {
	          var hasOptimizations = typeof _this6.options.optimizations !== 'undefined';
	          var gpu = hasOptimizations ? _this6.options.optimizations.gpu : null;
	          if (gpu !== false) {
	            var yPos = undefined,
	                xPos = undefined;
	            if (_same.top) {
	              css.top = 0;
	              yPos = _pos.top;
	            } else {
	              css.bottom = 0;
	              yPos = -_pos.bottom;
	            }

	            if (_same.left) {
	              css.left = 0;
	              xPos = _pos.left;
	            } else {
	              css.right = 0;
	              xPos = -_pos.right;
	            }

	            css[transformKey] = 'translateX(' + Math.round(xPos) + 'px) translateY(' + Math.round(yPos) + 'px)';

	            if (transformKey !== 'msTransform') {
	              // The Z transform will keep this in the GPU (faster, and prevents artifacts),
	              // but IE9 doesn't support 3d transforms and will choke.
	              css[transformKey] += " translateZ(0)";
	            }
	          } else {
	            if (_same.top) {
	              css.top = _pos.top + 'px';
	            } else {
	              css.bottom = _pos.bottom + 'px';
	            }

	            if (_same.left) {
	              css.left = _pos.left + 'px';
	            } else {
	              css.right = _pos.right + 'px';
	            }
	          }
	        };

	        var moved = false;
	        if ((same.page.top || same.page.bottom) && (same.page.left || same.page.right)) {
	          css.position = 'absolute';
	          transcribe(same.page, pos.page);
	        } else if ((same.viewport.top || same.viewport.bottom) && (same.viewport.left || same.viewport.right)) {
	          css.position = 'fixed';
	          transcribe(same.viewport, pos.viewport);
	        } else if (typeof same.offset !== 'undefined' && same.offset.top && same.offset.left) {
	          (function () {
	            css.position = 'absolute';
	            var offsetParent = _this6.cache('target-offsetparent', function () {
	              return getOffsetParent(_this6.target);
	            });

	            if (getOffsetParent(_this6.element) !== offsetParent) {
	              defer(function () {
	                _this6.element.parentNode.removeChild(_this6.element);
	                offsetParent.appendChild(_this6.element);
	              });
	            }

	            transcribe(same.offset, pos.offset);
	            moved = true;
	          })();
	        } else {
	          css.position = 'absolute';
	          transcribe({ top: true, left: true }, pos.page);
	        }

	        if (!moved) {
	          var offsetParentIsBody = true;
	          var currentNode = this.element.parentNode;
	          while (currentNode && currentNode.tagName !== 'BODY') {
	            if (getComputedStyle(currentNode).position !== 'static') {
	              offsetParentIsBody = false;
	              break;
	            }

	            currentNode = currentNode.parentNode;
	          }

	          if (!offsetParentIsBody) {
	            this.element.parentNode.removeChild(this.element);
	            document.body.appendChild(this.element);
	          }
	        }

	        // Any css change will trigger a repaint, so let's avoid one if nothing changed
	        var writeCSS = {};
	        var write = false;
	        for (var key in css) {
	          var val = css[key];
	          var elVal = this.element.style[key];

	          if (elVal !== '' && val !== '' && ['top', 'left', 'bottom', 'right'].indexOf(key) >= 0) {
	            elVal = parseFloat(elVal);
	            val = parseFloat(val);
	          }

	          if (elVal !== val) {
	            write = true;
	            writeCSS[key] = val;
	          }
	        }

	        if (write) {
	          defer(function () {
	            extend(_this6.element.style, writeCSS);
	          });
	        }
	      }
	    }]);

	    return TetherClass;
	  })();

	  TetherClass.modules = [];

	  TetherBase.position = position;

	  var Tether = extend(TetherClass, TetherBase);
	  /* globals TetherBase */

	  'use strict';

	  var _slicedToArray = (function () {
	    function sliceIterator(arr, i) {
	      var _arr = [];var _n = true;var _d = false;var _e = undefined;try {
	        for (var _i = arr[Symbol.iterator](), _s; !(_n = (_s = _i.next()).done); _n = true) {
	          _arr.push(_s.value);if (i && _arr.length === i) break;
	        }
	      } catch (err) {
	        _d = true;_e = err;
	      } finally {
	        try {
	          if (!_n && _i['return']) _i['return']();
	        } finally {
	          if (_d) throw _e;
	        }
	      }return _arr;
	    }return function (arr, i) {
	      if (Array.isArray(arr)) {
	        return arr;
	      } else if (Symbol.iterator in Object(arr)) {
	        return sliceIterator(arr, i);
	      } else {
	        throw new TypeError('Invalid attempt to destructure non-iterable instance');
	      }
	    };
	  })();

	  var _TetherBase$Utils = TetherBase.Utils;
	  var getBounds = _TetherBase$Utils.getBounds;
	  var extend = _TetherBase$Utils.extend;
	  var updateClasses = _TetherBase$Utils.updateClasses;
	  var defer = _TetherBase$Utils.defer;

	  var BOUNDS_FORMAT = ['left', 'top', 'right', 'bottom'];

	  function getBoundingRect(tether, to) {
	    if (to === 'scrollParent') {
	      to = tether.scrollParent;
	    } else if (to === 'window') {
	      to = [pageXOffset, pageYOffset, innerWidth + pageXOffset, innerHeight + pageYOffset];
	    }

	    if (to === document) {
	      to = to.documentElement;
	    }

	    if (typeof to.nodeType !== 'undefined') {
	      (function () {
	        var size = getBounds(to);
	        var pos = size;
	        var style = getComputedStyle(to);

	        to = [pos.left, pos.top, size.width + pos.left, size.height + pos.top];

	        BOUNDS_FORMAT.forEach(function (side, i) {
	          side = side[0].toUpperCase() + side.substr(1);
	          if (side === 'Top' || side === 'Left') {
	            to[i] += parseFloat(style['border' + side + 'Width']);
	          } else {
	            to[i] -= parseFloat(style['border' + side + 'Width']);
	          }
	        });
	      })();
	    }

	    return to;
	  }

	  TetherBase.modules.push({
	    position: function position(_ref) {
	      var _this = this;

	      var top = _ref.top;
	      var left = _ref.left;
	      var targetAttachment = _ref.targetAttachment;

	      if (!this.options.constraints) {
	        return true;
	      }

	      var _cache = this.cache('element-bounds', function () {
	        return getBounds(_this.element);
	      });

	      var height = _cache.height;
	      var width = _cache.width;

	      if (width === 0 && height === 0 && typeof this.lastSize !== 'undefined') {
	        var _lastSize = this.lastSize;

	        // Handle the item getting hidden as a result of our positioning without glitching
	        // the classes in and out
	        width = _lastSize.width;
	        height = _lastSize.height;
	      }

	      var targetSize = this.cache('target-bounds', function () {
	        return _this.getTargetBounds();
	      });

	      var targetHeight = targetSize.height;
	      var targetWidth = targetSize.width;

	      var allClasses = [this.getClass('pinned'), this.getClass('out-of-bounds')];

	      this.options.constraints.forEach(function (constraint) {
	        var outOfBoundsClass = constraint.outOfBoundsClass;
	        var pinnedClass = constraint.pinnedClass;

	        if (outOfBoundsClass) {
	          allClasses.push(outOfBoundsClass);
	        }
	        if (pinnedClass) {
	          allClasses.push(pinnedClass);
	        }
	      });

	      allClasses.forEach(function (cls) {
	        ['left', 'top', 'right', 'bottom'].forEach(function (side) {
	          allClasses.push(cls + '-' + side);
	        });
	      });

	      var addClasses = [];

	      var tAttachment = extend({}, targetAttachment);
	      var eAttachment = extend({}, this.attachment);

	      this.options.constraints.forEach(function (constraint) {
	        var to = constraint.to;
	        var attachment = constraint.attachment;
	        var pin = constraint.pin;

	        if (typeof attachment === 'undefined') {
	          attachment = '';
	        }

	        var changeAttachX = undefined,
	            changeAttachY = undefined;
	        if (attachment.indexOf(' ') >= 0) {
	          var _attachment$split = attachment.split(' ');

	          var _attachment$split2 = _slicedToArray(_attachment$split, 2);

	          changeAttachY = _attachment$split2[0];
	          changeAttachX = _attachment$split2[1];
	        } else {
	          changeAttachX = changeAttachY = attachment;
	        }

	        var bounds = getBoundingRect(_this, to);

	        if (changeAttachY === 'target' || changeAttachY === 'both') {
	          if (top < bounds[1] && tAttachment.top === 'top') {
	            top += targetHeight;
	            tAttachment.top = 'bottom';
	          }

	          if (top + height > bounds[3] && tAttachment.top === 'bottom') {
	            top -= targetHeight;
	            tAttachment.top = 'top';
	          }
	        }

	        if (changeAttachY === 'together') {
	          if (top < bounds[1] && tAttachment.top === 'top') {
	            if (eAttachment.top === 'bottom') {
	              top += targetHeight;
	              tAttachment.top = 'bottom';

	              top += height;
	              eAttachment.top = 'top';
	            } else if (eAttachment.top === 'top') {
	              top += targetHeight;
	              tAttachment.top = 'bottom';

	              top -= height;
	              eAttachment.top = 'bottom';
	            }
	          }

	          if (top + height > bounds[3] && tAttachment.top === 'bottom') {
	            if (eAttachment.top === 'top') {
	              top -= targetHeight;
	              tAttachment.top = 'top';

	              top -= height;
	              eAttachment.top = 'bottom';
	            } else if (eAttachment.top === 'bottom') {
	              top -= targetHeight;
	              tAttachment.top = 'top';

	              top += height;
	              eAttachment.top = 'top';
	            }
	          }

	          if (tAttachment.top === 'middle') {
	            if (top + height > bounds[3] && eAttachment.top === 'top') {
	              top -= height;
	              eAttachment.top = 'bottom';
	            } else if (top < bounds[1] && eAttachment.top === 'bottom') {
	              top += height;
	              eAttachment.top = 'top';
	            }
	          }
	        }

	        if (changeAttachX === 'target' || changeAttachX === 'both') {
	          if (left < bounds[0] && tAttachment.left === 'left') {
	            left += targetWidth;
	            tAttachment.left = 'right';
	          }

	          if (left + width > bounds[2] && tAttachment.left === 'right') {
	            left -= targetWidth;
	            tAttachment.left = 'left';
	          }
	        }

	        if (changeAttachX === 'together') {
	          if (left < bounds[0] && tAttachment.left === 'left') {
	            if (eAttachment.left === 'right') {
	              left += targetWidth;
	              tAttachment.left = 'right';

	              left += width;
	              eAttachment.left = 'left';
	            } else if (eAttachment.left === 'left') {
	              left += targetWidth;
	              tAttachment.left = 'right';

	              left -= width;
	              eAttachment.left = 'right';
	            }
	          } else if (left + width > bounds[2] && tAttachment.left === 'right') {
	            if (eAttachment.left === 'left') {
	              left -= targetWidth;
	              tAttachment.left = 'left';

	              left -= width;
	              eAttachment.left = 'right';
	            } else if (eAttachment.left === 'right') {
	              left -= targetWidth;
	              tAttachment.left = 'left';

	              left += width;
	              eAttachment.left = 'left';
	            }
	          } else if (tAttachment.left === 'center') {
	            if (left + width > bounds[2] && eAttachment.left === 'left') {
	              left -= width;
	              eAttachment.left = 'right';
	            } else if (left < bounds[0] && eAttachment.left === 'right') {
	              left += width;
	              eAttachment.left = 'left';
	            }
	          }
	        }

	        if (changeAttachY === 'element' || changeAttachY === 'both') {
	          if (top < bounds[1] && eAttachment.top === 'bottom') {
	            top += height;
	            eAttachment.top = 'top';
	          }

	          if (top + height > bounds[3] && eAttachment.top === 'top') {
	            top -= height;
	            eAttachment.top = 'bottom';
	          }
	        }

	        if (changeAttachX === 'element' || changeAttachX === 'both') {
	          if (left < bounds[0] && eAttachment.left === 'right') {
	            left += width;
	            eAttachment.left = 'left';
	          }

	          if (left + width > bounds[2] && eAttachment.left === 'left') {
	            left -= width;
	            eAttachment.left = 'right';
	          }
	        }

	        if (typeof pin === 'string') {
	          pin = pin.split(',').map(function (p) {
	            return p.trim();
	          });
	        } else if (pin === true) {
	          pin = ['top', 'left', 'right', 'bottom'];
	        }

	        pin = pin || [];

	        var pinned = [];
	        var oob = [];

	        if (top < bounds[1]) {
	          if (pin.indexOf('top') >= 0) {
	            top = bounds[1];
	            pinned.push('top');
	          } else {
	            oob.push('top');
	          }
	        }

	        if (top + height > bounds[3]) {
	          if (pin.indexOf('bottom') >= 0) {
	            top = bounds[3] - height;
	            pinned.push('bottom');
	          } else {
	            oob.push('bottom');
	          }
	        }

	        if (left < bounds[0]) {
	          if (pin.indexOf('left') >= 0) {
	            left = bounds[0];
	            pinned.push('left');
	          } else {
	            oob.push('left');
	          }
	        }

	        if (left + width > bounds[2]) {
	          if (pin.indexOf('right') >= 0) {
	            left = bounds[2] - width;
	            pinned.push('right');
	          } else {
	            oob.push('right');
	          }
	        }

	        if (pinned.length) {
	          (function () {
	            var pinnedClass = undefined;
	            if (typeof _this.options.pinnedClass !== 'undefined') {
	              pinnedClass = _this.options.pinnedClass;
	            } else {
	              pinnedClass = _this.getClass('pinned');
	            }

	            addClasses.push(pinnedClass);
	            pinned.forEach(function (side) {
	              addClasses.push(pinnedClass + '-' + side);
	            });
	          })();
	        }

	        if (oob.length) {
	          (function () {
	            var oobClass = undefined;
	            if (typeof _this.options.outOfBoundsClass !== 'undefined') {
	              oobClass = _this.options.outOfBoundsClass;
	            } else {
	              oobClass = _this.getClass('out-of-bounds');
	            }

	            addClasses.push(oobClass);
	            oob.forEach(function (side) {
	              addClasses.push(oobClass + '-' + side);
	            });
	          })();
	        }

	        if (pinned.indexOf('left') >= 0 || pinned.indexOf('right') >= 0) {
	          eAttachment.left = tAttachment.left = false;
	        }
	        if (pinned.indexOf('top') >= 0 || pinned.indexOf('bottom') >= 0) {
	          eAttachment.top = tAttachment.top = false;
	        }

	        if (tAttachment.top !== targetAttachment.top || tAttachment.left !== targetAttachment.left || eAttachment.top !== _this.attachment.top || eAttachment.left !== _this.attachment.left) {
	          _this.updateAttachClasses(eAttachment, tAttachment);
	        }
	      });

	      defer(function () {
	        if (!(_this.options.addTargetClasses === false)) {
	          updateClasses(_this.target, addClasses, allClasses);
	        }
	        updateClasses(_this.element, addClasses, allClasses);
	      });

	      return { top: top, left: left };
	    }
	  });
	  /* globals TetherBase */

	  'use strict';

	  var _TetherBase$Utils = TetherBase.Utils;
	  var getBounds = _TetherBase$Utils.getBounds;
	  var updateClasses = _TetherBase$Utils.updateClasses;
	  var defer = _TetherBase$Utils.defer;

	  TetherBase.modules.push({
	    position: function position(_ref) {
	      var _this = this;

	      var top = _ref.top;
	      var left = _ref.left;

	      var _cache = this.cache('element-bounds', function () {
	        return getBounds(_this.element);
	      });

	      var height = _cache.height;
	      var width = _cache.width;

	      var targetPos = this.getTargetBounds();

	      var bottom = top + height;
	      var right = left + width;

	      var abutted = [];
	      if (top <= targetPos.bottom && bottom >= targetPos.top) {
	        ['left', 'right'].forEach(function (side) {
	          var targetPosSide = targetPos[side];
	          if (targetPosSide === left || targetPosSide === right) {
	            abutted.push(side);
	          }
	        });
	      }

	      if (left <= targetPos.right && right >= targetPos.left) {
	        ['top', 'bottom'].forEach(function (side) {
	          var targetPosSide = targetPos[side];
	          if (targetPosSide === top || targetPosSide === bottom) {
	            abutted.push(side);
	          }
	        });
	      }

	      var allClasses = [];
	      var addClasses = [];

	      var sides = ['left', 'top', 'right', 'bottom'];
	      allClasses.push(this.getClass('abutted'));
	      sides.forEach(function (side) {
	        allClasses.push(_this.getClass('abutted') + '-' + side);
	      });

	      if (abutted.length) {
	        addClasses.push(this.getClass('abutted'));
	      }

	      abutted.forEach(function (side) {
	        addClasses.push(_this.getClass('abutted') + '-' + side);
	      });

	      defer(function () {
	        if (!(_this.options.addTargetClasses === false)) {
	          updateClasses(_this.target, addClasses, allClasses);
	        }
	        updateClasses(_this.element, addClasses, allClasses);
	      });

	      return true;
	    }
	  });
	  /* globals TetherBase */

	  'use strict';

	  var _slicedToArray = (function () {
	    function sliceIterator(arr, i) {
	      var _arr = [];var _n = true;var _d = false;var _e = undefined;try {
	        for (var _i = arr[Symbol.iterator](), _s; !(_n = (_s = _i.next()).done); _n = true) {
	          _arr.push(_s.value);if (i && _arr.length === i) break;
	        }
	      } catch (err) {
	        _d = true;_e = err;
	      } finally {
	        try {
	          if (!_n && _i['return']) _i['return']();
	        } finally {
	          if (_d) throw _e;
	        }
	      }return _arr;
	    }return function (arr, i) {
	      if (Array.isArray(arr)) {
	        return arr;
	      } else if (Symbol.iterator in Object(arr)) {
	        return sliceIterator(arr, i);
	      } else {
	        throw new TypeError('Invalid attempt to destructure non-iterable instance');
	      }
	    };
	  })();

	  TetherBase.modules.push({
	    position: function position(_ref) {
	      var top = _ref.top;
	      var left = _ref.left;

	      if (!this.options.shift) {
	        return;
	      }

	      var shift = this.options.shift;
	      if (typeof this.options.shift === 'function') {
	        shift = this.options.shift.call(this, { top: top, left: left });
	      }

	      var shiftTop = undefined,
	          shiftLeft = undefined;
	      if (typeof shift === 'string') {
	        shift = shift.split(' ');
	        shift[1] = shift[1] || shift[0];

	        var _shift = shift;

	        var _shift2 = _slicedToArray(_shift, 2);

	        shiftTop = _shift2[0];
	        shiftLeft = _shift2[1];

	        shiftTop = parseFloat(shiftTop, 10);
	        shiftLeft = parseFloat(shiftLeft, 10);
	      } else {
	        shiftTop = shift.top;
	        shiftLeft = shift.left;
	      }

	      top += shiftTop;
	      left += shiftLeft;

	      return { top: top, left: left };
	    }
	  });
	  return Tether;
	});

/***/ },
/* 7 */
/***/ function(module, exports, __webpack_require__) {

	// This file is autogenerated via the `commonjs` Grunt task. You can require() this file in a CommonJS environment.
	'use strict';

	__webpack_require__(8);
	__webpack_require__(9);
	__webpack_require__(10);
	__webpack_require__(11);
	__webpack_require__(12);
	__webpack_require__(13);
	__webpack_require__(14);
	__webpack_require__(15);
	__webpack_require__(16);
	__webpack_require__(17);
	__webpack_require__(18);

/***/ },
/* 8 */
/***/ function(module, exports, __webpack_require__) {

	var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;'use strict';

	(function (global, factory) {
	  if (true) {
	    !(__WEBPACK_AMD_DEFINE_ARRAY__ = [exports, module], __WEBPACK_AMD_DEFINE_FACTORY__ = (factory), __WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ? (__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__), __WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
	  } else if (typeof exports !== 'undefined' && typeof module !== 'undefined') {
	    factory(exports, module);
	  } else {
	    var mod = {
	      exports: {}
	    };
	    factory(mod.exports, mod);
	    global.util = mod.exports;
	  }
	})(undefined, function (exports, module) {
	  /**
	   * --------------------------------------------------------------------------
	   * Bootstrap (v4.0.0-alpha.2): util.js
	   * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
	   * --------------------------------------------------------------------------
	   */

	  'use strict';

	  var Util = (function ($) {

	    /**
	     * ------------------------------------------------------------------------
	     * Private TransitionEnd Helpers
	     * ------------------------------------------------------------------------
	     */

	    var transition = false;

	    var TransitionEndEvent = {
	      WebkitTransition: 'webkitTransitionEnd',
	      MozTransition: 'transitionend',
	      OTransition: 'oTransitionEnd otransitionend',
	      transition: 'transitionend'
	    };

	    // shoutout AngusCroll (https://goo.gl/pxwQGp)
	    function toType(obj) {
	      return ({}).toString.call(obj).match(/\s([a-zA-Z]+)/)[1].toLowerCase();
	    }

	    function isElement(obj) {
	      return (obj[0] || obj).nodeType;
	    }

	    function getSpecialTransitionEndEvent() {
	      return {
	        bindType: transition.end,
	        delegateType: transition.end,
	        handle: function handle(event) {
	          if ($(event.target).is(this)) {
	            return event.handleObj.handler.apply(this, arguments);
	          }
	        }
	      };
	    }

	    function transitionEndTest() {
	      if (window.QUnit) {
	        return false;
	      }

	      var el = document.createElement('bootstrap');

	      for (var _name in TransitionEndEvent) {
	        if (el.style[_name] !== undefined) {
	          return { end: TransitionEndEvent[_name] };
	        }
	      }

	      return false;
	    }

	    function transitionEndEmulator(duration) {
	      var _this = this;

	      var called = false;

	      $(this).one(Util.TRANSITION_END, function () {
	        called = true;
	      });

	      setTimeout(function () {
	        if (!called) {
	          Util.triggerTransitionEnd(_this);
	        }
	      }, duration);

	      return this;
	    }

	    function setTransitionEndSupport() {
	      transition = transitionEndTest();

	      $.fn.emulateTransitionEnd = transitionEndEmulator;

	      if (Util.supportsTransitionEnd()) {
	        $.event.special[Util.TRANSITION_END] = getSpecialTransitionEndEvent();
	      }
	    }

	    /**
	     * --------------------------------------------------------------------------
	     * Public Util Api
	     * --------------------------------------------------------------------------
	     */

	    var Util = {

	      TRANSITION_END: 'bsTransitionEnd',

	      getUID: function getUID(prefix) {
	        do {
	          prefix += ~ ~(Math.random() * 1000000); // "~~" acts like a faster Math.floor() here
	        } while (document.getElementById(prefix));
	        return prefix;
	      },

	      getSelectorFromElement: function getSelectorFromElement(element) {
	        var selector = element.getAttribute('data-target');

	        if (!selector) {
	          selector = element.getAttribute('href') || '';
	          selector = /^#[a-z]/i.test(selector) ? selector : null;
	        }

	        return selector;
	      },

	      reflow: function reflow(element) {
	        new Function('bs', 'return bs')(element.offsetHeight);
	      },

	      triggerTransitionEnd: function triggerTransitionEnd(element) {
	        $(element).trigger(transition.end);
	      },

	      supportsTransitionEnd: function supportsTransitionEnd() {
	        return Boolean(transition);
	      },

	      typeCheckConfig: function typeCheckConfig(componentName, config, configTypes) {
	        for (var property in configTypes) {
	          if (configTypes.hasOwnProperty(property)) {
	            var expectedTypes = configTypes[property];
	            var value = config[property];
	            var valueType = undefined;

	            if (value && isElement(value)) {
	              valueType = 'element';
	            } else {
	              valueType = toType(value);
	            }

	            if (!new RegExp(expectedTypes).test(valueType)) {
	              throw new Error(componentName.toUpperCase() + ': ' + ('Option "' + property + '" provided type "' + valueType + '" ') + ('but expected type "' + expectedTypes + '".'));
	            }
	          }
	        }
	      }
	    };

	    setTransitionEndSupport();

	    return Util;
	  })(jQuery);

	  module.exports = Util;
	});

/***/ },
/* 9 */
/***/ function(module, exports, __webpack_require__) {

	var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;'use strict';

	(function (global, factory) {
	  if (true) {
	    !(__WEBPACK_AMD_DEFINE_ARRAY__ = [exports, module, __webpack_require__(8)], __WEBPACK_AMD_DEFINE_FACTORY__ = (factory), __WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ? (__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__), __WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
	  } else if (typeof exports !== 'undefined' && typeof module !== 'undefined') {
	    factory(exports, module, require('./util'));
	  } else {
	    var mod = {
	      exports: {}
	    };
	    factory(mod.exports, mod, global.Util);
	    global.alert = mod.exports;
	  }
	})(undefined, function (exports, module, _util) {
	  'use strict';

	  var _createClass = (function () {
	    function defineProperties(target, props) {
	      for (var i = 0; i < props.length; i++) {
	        var descriptor = props[i];descriptor.enumerable = descriptor.enumerable || false;descriptor.configurable = true;if ('value' in descriptor) descriptor.writable = true;Object.defineProperty(target, descriptor.key, descriptor);
	      }
	    }return function (Constructor, protoProps, staticProps) {
	      if (protoProps) defineProperties(Constructor.prototype, protoProps);if (staticProps) defineProperties(Constructor, staticProps);return Constructor;
	    };
	  })();

	  function _interopRequireDefault(obj) {
	    return obj && obj.__esModule ? obj : { 'default': obj };
	  }

	  function _classCallCheck(instance, Constructor) {
	    if (!(instance instanceof Constructor)) {
	      throw new TypeError('Cannot call a class as a function');
	    }
	  }

	  var _Util = _interopRequireDefault(_util);

	  /**
	   * --------------------------------------------------------------------------
	   * Bootstrap (v4.0.0-alpha.2): alert.js
	   * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
	   * --------------------------------------------------------------------------
	   */

	  var Alert = (function ($) {

	    /**
	     * ------------------------------------------------------------------------
	     * Constants
	     * ------------------------------------------------------------------------
	     */

	    var NAME = 'alert';
	    var VERSION = '4.0.0-alpha';
	    var DATA_KEY = 'bs.alert';
	    var EVENT_KEY = '.' + DATA_KEY;
	    var DATA_API_KEY = '.data-api';
	    var JQUERY_NO_CONFLICT = $.fn[NAME];
	    var TRANSITION_DURATION = 150;

	    var Selector = {
	      DISMISS: '[data-dismiss="alert"]'
	    };

	    var Event = {
	      CLOSE: 'close' + EVENT_KEY,
	      CLOSED: 'closed' + EVENT_KEY,
	      CLICK_DATA_API: 'click' + EVENT_KEY + DATA_API_KEY
	    };

	    var ClassName = {
	      ALERT: 'alert',
	      FADE: 'fade',
	      IN: 'in'
	    };

	    /**
	     * ------------------------------------------------------------------------
	     * Class Definition
	     * ------------------------------------------------------------------------
	     */

	    var Alert = (function () {
	      function Alert(element) {
	        _classCallCheck(this, Alert);

	        this._element = element;
	      }

	      /**
	       * ------------------------------------------------------------------------
	       * Data Api implementation
	       * ------------------------------------------------------------------------
	       */

	      // getters

	      _createClass(Alert, [{
	        key: 'close',

	        // public

	        value: function close(element) {
	          element = element || this._element;

	          var rootElement = this._getRootElement(element);
	          var customEvent = this._triggerCloseEvent(rootElement);

	          if (customEvent.isDefaultPrevented()) {
	            return;
	          }

	          this._removeElement(rootElement);
	        }
	      }, {
	        key: 'dispose',
	        value: function dispose() {
	          $.removeData(this._element, DATA_KEY);
	          this._element = null;
	        }

	        // private

	      }, {
	        key: '_getRootElement',
	        value: function _getRootElement(element) {
	          var selector = _Util['default'].getSelectorFromElement(element);
	          var parent = false;

	          if (selector) {
	            parent = $(selector)[0];
	          }

	          if (!parent) {
	            parent = $(element).closest('.' + ClassName.ALERT)[0];
	          }

	          return parent;
	        }
	      }, {
	        key: '_triggerCloseEvent',
	        value: function _triggerCloseEvent(element) {
	          var closeEvent = $.Event(Event.CLOSE);

	          $(element).trigger(closeEvent);
	          return closeEvent;
	        }
	      }, {
	        key: '_removeElement',
	        value: function _removeElement(element) {
	          $(element).removeClass(ClassName.IN);

	          if (!_Util['default'].supportsTransitionEnd() || !$(element).hasClass(ClassName.FADE)) {
	            this._destroyElement(element);
	            return;
	          }

	          $(element).one(_Util['default'].TRANSITION_END, $.proxy(this._destroyElement, this, element)).emulateTransitionEnd(TRANSITION_DURATION);
	        }
	      }, {
	        key: '_destroyElement',
	        value: function _destroyElement(element) {
	          $(element).detach().trigger(Event.CLOSED).remove();
	        }

	        // static

	      }], [{
	        key: '_jQueryInterface',
	        value: function _jQueryInterface(config) {
	          return this.each(function () {
	            var $element = $(this);
	            var data = $element.data(DATA_KEY);

	            if (!data) {
	              data = new Alert(this);
	              $element.data(DATA_KEY, data);
	            }

	            if (config === 'close') {
	              data[config](this);
	            }
	          });
	        }
	      }, {
	        key: '_handleDismiss',
	        value: function _handleDismiss(alertInstance) {
	          return function (event) {
	            if (event) {
	              event.preventDefault();
	            }

	            alertInstance.close(this);
	          };
	        }
	      }, {
	        key: 'VERSION',
	        get: function get() {
	          return VERSION;
	        }
	      }]);

	      return Alert;
	    })();

	    $(document).on(Event.CLICK_DATA_API, Selector.DISMISS, Alert._handleDismiss(new Alert()));

	    /**
	     * ------------------------------------------------------------------------
	     * jQuery
	     * ------------------------------------------------------------------------
	     */

	    $.fn[NAME] = Alert._jQueryInterface;
	    $.fn[NAME].Constructor = Alert;
	    $.fn[NAME].noConflict = function () {
	      $.fn[NAME] = JQUERY_NO_CONFLICT;
	      return Alert._jQueryInterface;
	    };

	    return Alert;
	  })(jQuery);

	  module.exports = Alert;
	});

/***/ },
/* 10 */
/***/ function(module, exports, __webpack_require__) {

	var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;'use strict';

	(function (global, factory) {
	  if (true) {
	    !(__WEBPACK_AMD_DEFINE_ARRAY__ = [exports, module], __WEBPACK_AMD_DEFINE_FACTORY__ = (factory), __WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ? (__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__), __WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
	  } else if (typeof exports !== 'undefined' && typeof module !== 'undefined') {
	    factory(exports, module);
	  } else {
	    var mod = {
	      exports: {}
	    };
	    factory(mod.exports, mod);
	    global.button = mod.exports;
	  }
	})(undefined, function (exports, module) {
	  /**
	   * --------------------------------------------------------------------------
	   * Bootstrap (v4.0.0-alpha.2): button.js
	   * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
	   * --------------------------------------------------------------------------
	   */

	  'use strict';

	  var _createClass = (function () {
	    function defineProperties(target, props) {
	      for (var i = 0; i < props.length; i++) {
	        var descriptor = props[i];descriptor.enumerable = descriptor.enumerable || false;descriptor.configurable = true;if ('value' in descriptor) descriptor.writable = true;Object.defineProperty(target, descriptor.key, descriptor);
	      }
	    }return function (Constructor, protoProps, staticProps) {
	      if (protoProps) defineProperties(Constructor.prototype, protoProps);if (staticProps) defineProperties(Constructor, staticProps);return Constructor;
	    };
	  })();

	  function _classCallCheck(instance, Constructor) {
	    if (!(instance instanceof Constructor)) {
	      throw new TypeError('Cannot call a class as a function');
	    }
	  }

	  var Button = (function ($) {

	    /**
	     * ------------------------------------------------------------------------
	     * Constants
	     * ------------------------------------------------------------------------
	     */

	    var NAME = 'button';
	    var VERSION = '4.0.0-alpha';
	    var DATA_KEY = 'bs.button';
	    var EVENT_KEY = '.' + DATA_KEY;
	    var DATA_API_KEY = '.data-api';
	    var JQUERY_NO_CONFLICT = $.fn[NAME];

	    var ClassName = {
	      ACTIVE: 'active',
	      BUTTON: 'btn',
	      FOCUS: 'focus'
	    };

	    var Selector = {
	      DATA_TOGGLE_CARROT: '[data-toggle^="button"]',
	      DATA_TOGGLE: '[data-toggle="buttons"]',
	      INPUT: 'input',
	      ACTIVE: '.active',
	      BUTTON: '.btn'
	    };

	    var Event = {
	      CLICK_DATA_API: 'click' + EVENT_KEY + DATA_API_KEY,
	      FOCUS_BLUR_DATA_API: 'focus' + EVENT_KEY + DATA_API_KEY + ' ' + ('blur' + EVENT_KEY + DATA_API_KEY)
	    };

	    /**
	     * ------------------------------------------------------------------------
	     * Class Definition
	     * ------------------------------------------------------------------------
	     */

	    var Button = (function () {
	      function Button(element) {
	        _classCallCheck(this, Button);

	        this._element = element;
	      }

	      /**
	       * ------------------------------------------------------------------------
	       * Data Api implementation
	       * ------------------------------------------------------------------------
	       */

	      // getters

	      _createClass(Button, [{
	        key: 'toggle',

	        // public

	        value: function toggle() {
	          var triggerChangeEvent = true;
	          var rootElement = $(this._element).closest(Selector.DATA_TOGGLE)[0];

	          if (rootElement) {
	            var input = $(this._element).find(Selector.INPUT)[0];

	            if (input) {
	              if (input.type === 'radio') {
	                if (input.checked && $(this._element).hasClass(ClassName.ACTIVE)) {
	                  triggerChangeEvent = false;
	                } else {
	                  var activeElement = $(rootElement).find(Selector.ACTIVE)[0];

	                  if (activeElement) {
	                    $(activeElement).removeClass(ClassName.ACTIVE);
	                  }
	                }
	              }

	              if (triggerChangeEvent) {
	                input.checked = !$(this._element).hasClass(ClassName.ACTIVE);
	                $(this._element).trigger('change');
	              }
	            }
	          } else {
	            this._element.setAttribute('aria-pressed', !$(this._element).hasClass(ClassName.ACTIVE));
	          }

	          if (triggerChangeEvent) {
	            $(this._element).toggleClass(ClassName.ACTIVE);
	          }
	        }
	      }, {
	        key: 'dispose',
	        value: function dispose() {
	          $.removeData(this._element, DATA_KEY);
	          this._element = null;
	        }

	        // static

	      }], [{
	        key: '_jQueryInterface',
	        value: function _jQueryInterface(config) {
	          return this.each(function () {
	            var data = $(this).data(DATA_KEY);

	            if (!data) {
	              data = new Button(this);
	              $(this).data(DATA_KEY, data);
	            }

	            if (config === 'toggle') {
	              data[config]();
	            }
	          });
	        }
	      }, {
	        key: 'VERSION',
	        get: function get() {
	          return VERSION;
	        }
	      }]);

	      return Button;
	    })();

	    $(document).on(Event.CLICK_DATA_API, Selector.DATA_TOGGLE_CARROT, function (event) {
	      event.preventDefault();

	      var button = event.target;

	      if (!$(button).hasClass(ClassName.BUTTON)) {
	        button = $(button).closest(Selector.BUTTON);
	      }

	      Button._jQueryInterface.call($(button), 'toggle');
	    }).on(Event.FOCUS_BLUR_DATA_API, Selector.DATA_TOGGLE_CARROT, function (event) {
	      var button = $(event.target).closest(Selector.BUTTON)[0];
	      $(button).toggleClass(ClassName.FOCUS, /^focus(in)?$/.test(event.type));
	    });

	    /**
	     * ------------------------------------------------------------------------
	     * jQuery
	     * ------------------------------------------------------------------------
	     */

	    $.fn[NAME] = Button._jQueryInterface;
	    $.fn[NAME].Constructor = Button;
	    $.fn[NAME].noConflict = function () {
	      $.fn[NAME] = JQUERY_NO_CONFLICT;
	      return Button._jQueryInterface;
	    };

	    return Button;
	  })(jQuery);

	  module.exports = Button;
	});

/***/ },
/* 11 */
/***/ function(module, exports, __webpack_require__) {

	var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;'use strict';

	(function (global, factory) {
	  if (true) {
	    !(__WEBPACK_AMD_DEFINE_ARRAY__ = [exports, module, __webpack_require__(8)], __WEBPACK_AMD_DEFINE_FACTORY__ = (factory), __WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ? (__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__), __WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
	  } else if (typeof exports !== 'undefined' && typeof module !== 'undefined') {
	    factory(exports, module, require('./util'));
	  } else {
	    var mod = {
	      exports: {}
	    };
	    factory(mod.exports, mod, global.Util);
	    global.carousel = mod.exports;
	  }
	})(undefined, function (exports, module, _util) {
	  'use strict';

	  var _createClass = (function () {
	    function defineProperties(target, props) {
	      for (var i = 0; i < props.length; i++) {
	        var descriptor = props[i];descriptor.enumerable = descriptor.enumerable || false;descriptor.configurable = true;if ('value' in descriptor) descriptor.writable = true;Object.defineProperty(target, descriptor.key, descriptor);
	      }
	    }return function (Constructor, protoProps, staticProps) {
	      if (protoProps) defineProperties(Constructor.prototype, protoProps);if (staticProps) defineProperties(Constructor, staticProps);return Constructor;
	    };
	  })();

	  function _interopRequireDefault(obj) {
	    return obj && obj.__esModule ? obj : { 'default': obj };
	  }

	  function _classCallCheck(instance, Constructor) {
	    if (!(instance instanceof Constructor)) {
	      throw new TypeError('Cannot call a class as a function');
	    }
	  }

	  var _Util = _interopRequireDefault(_util);

	  /**
	   * --------------------------------------------------------------------------
	   * Bootstrap (v4.0.0-alpha.2): carousel.js
	   * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
	   * --------------------------------------------------------------------------
	   */

	  var Carousel = (function ($) {

	    /**
	     * ------------------------------------------------------------------------
	     * Constants
	     * ------------------------------------------------------------------------
	     */

	    var NAME = 'carousel';
	    var VERSION = '4.0.0-alpha';
	    var DATA_KEY = 'bs.carousel';
	    var EVENT_KEY = '.' + DATA_KEY;
	    var DATA_API_KEY = '.data-api';
	    var JQUERY_NO_CONFLICT = $.fn[NAME];
	    var TRANSITION_DURATION = 600;

	    var Default = {
	      interval: 5000,
	      keyboard: true,
	      slide: false,
	      pause: 'hover',
	      wrap: true
	    };

	    var DefaultType = {
	      interval: '(number|boolean)',
	      keyboard: 'boolean',
	      slide: '(boolean|string)',
	      pause: '(string|boolean)',
	      wrap: 'boolean'
	    };

	    var Direction = {
	      NEXT: 'next',
	      PREVIOUS: 'prev'
	    };

	    var Event = {
	      SLIDE: 'slide' + EVENT_KEY,
	      SLID: 'slid' + EVENT_KEY,
	      KEYDOWN: 'keydown' + EVENT_KEY,
	      MOUSEENTER: 'mouseenter' + EVENT_KEY,
	      MOUSELEAVE: 'mouseleave' + EVENT_KEY,
	      LOAD_DATA_API: 'load' + EVENT_KEY + DATA_API_KEY,
	      CLICK_DATA_API: 'click' + EVENT_KEY + DATA_API_KEY
	    };

	    var ClassName = {
	      CAROUSEL: 'carousel',
	      ACTIVE: 'active',
	      SLIDE: 'slide',
	      RIGHT: 'right',
	      LEFT: 'left',
	      ITEM: 'carousel-item'
	    };

	    var Selector = {
	      ACTIVE: '.active',
	      ACTIVE_ITEM: '.active.carousel-item',
	      ITEM: '.carousel-item',
	      NEXT_PREV: '.next, .prev',
	      INDICATORS: '.carousel-indicators',
	      DATA_SLIDE: '[data-slide], [data-slide-to]',
	      DATA_RIDE: '[data-ride="carousel"]'
	    };

	    /**
	     * ------------------------------------------------------------------------
	     * Class Definition
	     * ------------------------------------------------------------------------
	     */

	    var Carousel = (function () {
	      function Carousel(element, config) {
	        _classCallCheck(this, Carousel);

	        this._items = null;
	        this._interval = null;
	        this._activeElement = null;

	        this._isPaused = false;
	        this._isSliding = false;

	        this._config = this._getConfig(config);
	        this._element = $(element)[0];
	        this._indicatorsElement = $(this._element).find(Selector.INDICATORS)[0];

	        this._addEventListeners();
	      }

	      /**
	       * ------------------------------------------------------------------------
	       * Data Api implementation
	       * ------------------------------------------------------------------------
	       */

	      // getters

	      _createClass(Carousel, [{
	        key: 'next',

	        // public

	        value: function next() {
	          if (!this._isSliding) {
	            this._slide(Direction.NEXT);
	          }
	        }
	      }, {
	        key: 'nextWhenVisible',
	        value: function nextWhenVisible() {
	          // Don't call next when the page isn't visible
	          if (!document.hidden) {
	            this.next();
	          }
	        }
	      }, {
	        key: 'prev',
	        value: function prev() {
	          if (!this._isSliding) {
	            this._slide(Direction.PREVIOUS);
	          }
	        }
	      }, {
	        key: 'pause',
	        value: function pause(event) {
	          if (!event) {
	            this._isPaused = true;
	          }

	          if ($(this._element).find(Selector.NEXT_PREV)[0] && _Util['default'].supportsTransitionEnd()) {
	            _Util['default'].triggerTransitionEnd(this._element);
	            this.cycle(true);
	          }

	          clearInterval(this._interval);
	          this._interval = null;
	        }
	      }, {
	        key: 'cycle',
	        value: function cycle(event) {
	          if (!event) {
	            this._isPaused = false;
	          }

	          if (this._interval) {
	            clearInterval(this._interval);
	            this._interval = null;
	          }

	          if (this._config.interval && !this._isPaused) {
	            this._interval = setInterval($.proxy(document.visibilityState ? this.nextWhenVisible : this.next, this), this._config.interval);
	          }
	        }
	      }, {
	        key: 'to',
	        value: function to(index) {
	          var _this = this;

	          this._activeElement = $(this._element).find(Selector.ACTIVE_ITEM)[0];

	          var activeIndex = this._getItemIndex(this._activeElement);

	          if (index > this._items.length - 1 || index < 0) {
	            return;
	          }

	          if (this._isSliding) {
	            $(this._element).one(Event.SLID, function () {
	              return _this.to(index);
	            });
	            return;
	          }

	          if (activeIndex === index) {
	            this.pause();
	            this.cycle();
	            return;
	          }

	          var direction = index > activeIndex ? Direction.NEXT : Direction.PREVIOUS;

	          this._slide(direction, this._items[index]);
	        }
	      }, {
	        key: 'dispose',
	        value: function dispose() {
	          $(this._element).off(EVENT_KEY);
	          $.removeData(this._element, DATA_KEY);

	          this._items = null;
	          this._config = null;
	          this._element = null;
	          this._interval = null;
	          this._isPaused = null;
	          this._isSliding = null;
	          this._activeElement = null;
	          this._indicatorsElement = null;
	        }

	        // private

	      }, {
	        key: '_getConfig',
	        value: function _getConfig(config) {
	          config = $.extend({}, Default, config);
	          _Util['default'].typeCheckConfig(NAME, config, DefaultType);
	          return config;
	        }
	      }, {
	        key: '_addEventListeners',
	        value: function _addEventListeners() {
	          if (this._config.keyboard) {
	            $(this._element).on(Event.KEYDOWN, $.proxy(this._keydown, this));
	          }

	          if (this._config.pause === 'hover' && !('ontouchstart' in document.documentElement)) {
	            $(this._element).on(Event.MOUSEENTER, $.proxy(this.pause, this)).on(Event.MOUSELEAVE, $.proxy(this.cycle, this));
	          }
	        }
	      }, {
	        key: '_keydown',
	        value: function _keydown(event) {
	          event.preventDefault();

	          if (/input|textarea/i.test(event.target.tagName)) {
	            return;
	          }

	          switch (event.which) {
	            case 37:
	              this.prev();break;
	            case 39:
	              this.next();break;
	            default:
	              return;
	          }
	        }
	      }, {
	        key: '_getItemIndex',
	        value: function _getItemIndex(element) {
	          this._items = $.makeArray($(element).parent().find(Selector.ITEM));
	          return this._items.indexOf(element);
	        }
	      }, {
	        key: '_getItemByDirection',
	        value: function _getItemByDirection(direction, activeElement) {
	          var isNextDirection = direction === Direction.NEXT;
	          var isPrevDirection = direction === Direction.PREVIOUS;
	          var activeIndex = this._getItemIndex(activeElement);
	          var lastItemIndex = this._items.length - 1;
	          var isGoingToWrap = isPrevDirection && activeIndex === 0 || isNextDirection && activeIndex === lastItemIndex;

	          if (isGoingToWrap && !this._config.wrap) {
	            return activeElement;
	          }

	          var delta = direction === Direction.PREVIOUS ? -1 : 1;
	          var itemIndex = (activeIndex + delta) % this._items.length;

	          return itemIndex === -1 ? this._items[this._items.length - 1] : this._items[itemIndex];
	        }
	      }, {
	        key: '_triggerSlideEvent',
	        value: function _triggerSlideEvent(relatedTarget, directionalClassname) {
	          var slideEvent = $.Event(Event.SLIDE, {
	            relatedTarget: relatedTarget,
	            direction: directionalClassname
	          });

	          $(this._element).trigger(slideEvent);

	          return slideEvent;
	        }
	      }, {
	        key: '_setActiveIndicatorElement',
	        value: function _setActiveIndicatorElement(element) {
	          if (this._indicatorsElement) {
	            $(this._indicatorsElement).find(Selector.ACTIVE).removeClass(ClassName.ACTIVE);

	            var nextIndicator = this._indicatorsElement.children[this._getItemIndex(element)];

	            if (nextIndicator) {
	              $(nextIndicator).addClass(ClassName.ACTIVE);
	            }
	          }
	        }
	      }, {
	        key: '_slide',
	        value: function _slide(direction, element) {
	          var _this2 = this;

	          var activeElement = $(this._element).find(Selector.ACTIVE_ITEM)[0];
	          var nextElement = element || activeElement && this._getItemByDirection(direction, activeElement);

	          var isCycling = Boolean(this._interval);

	          var directionalClassName = direction === Direction.NEXT ? ClassName.LEFT : ClassName.RIGHT;

	          if (nextElement && $(nextElement).hasClass(ClassName.ACTIVE)) {
	            this._isSliding = false;
	            return;
	          }

	          var slideEvent = this._triggerSlideEvent(nextElement, directionalClassName);
	          if (slideEvent.isDefaultPrevented()) {
	            return;
	          }

	          if (!activeElement || !nextElement) {
	            // some weirdness is happening, so we bail
	            return;
	          }

	          this._isSliding = true;

	          if (isCycling) {
	            this.pause();
	          }

	          this._setActiveIndicatorElement(nextElement);

	          var slidEvent = $.Event(Event.SLID, {
	            relatedTarget: nextElement,
	            direction: directionalClassName
	          });

	          if (_Util['default'].supportsTransitionEnd() && $(this._element).hasClass(ClassName.SLIDE)) {

	            $(nextElement).addClass(direction);

	            _Util['default'].reflow(nextElement);

	            $(activeElement).addClass(directionalClassName);
	            $(nextElement).addClass(directionalClassName);

	            $(activeElement).one(_Util['default'].TRANSITION_END, function () {
	              $(nextElement).removeClass(directionalClassName).removeClass(direction);

	              $(nextElement).addClass(ClassName.ACTIVE);

	              $(activeElement).removeClass(ClassName.ACTIVE).removeClass(direction).removeClass(directionalClassName);

	              _this2._isSliding = false;

	              setTimeout(function () {
	                return $(_this2._element).trigger(slidEvent);
	              }, 0);
	            }).emulateTransitionEnd(TRANSITION_DURATION);
	          } else {
	            $(activeElement).removeClass(ClassName.ACTIVE);
	            $(nextElement).addClass(ClassName.ACTIVE);

	            this._isSliding = false;
	            $(this._element).trigger(slidEvent);
	          }

	          if (isCycling) {
	            this.cycle();
	          }
	        }

	        // static

	      }], [{
	        key: '_jQueryInterface',
	        value: function _jQueryInterface(config) {
	          return this.each(function () {
	            var data = $(this).data(DATA_KEY);
	            var _config = $.extend({}, Default, $(this).data());

	            if (typeof config === 'object') {
	              $.extend(_config, config);
	            }

	            var action = typeof config === 'string' ? config : _config.slide;

	            if (!data) {
	              data = new Carousel(this, _config);
	              $(this).data(DATA_KEY, data);
	            }

	            if (typeof config === 'number') {
	              data.to(config);
	            } else if (typeof action === 'string') {
	              if (data[action] === undefined) {
	                throw new Error('No method named "' + action + '"');
	              }
	              data[action]();
	            } else if (_config.interval) {
	              data.pause();
	              data.cycle();
	            }
	          });
	        }
	      }, {
	        key: '_dataApiClickHandler',
	        value: function _dataApiClickHandler(event) {
	          var selector = _Util['default'].getSelectorFromElement(this);

	          if (!selector) {
	            return;
	          }

	          var target = $(selector)[0];

	          if (!target || !$(target).hasClass(ClassName.CAROUSEL)) {
	            return;
	          }

	          var config = $.extend({}, $(target).data(), $(this).data());
	          var slideIndex = this.getAttribute('data-slide-to');

	          if (slideIndex) {
	            config.interval = false;
	          }

	          Carousel._jQueryInterface.call($(target), config);

	          if (slideIndex) {
	            $(target).data(DATA_KEY).to(slideIndex);
	          }

	          event.preventDefault();
	        }
	      }, {
	        key: 'VERSION',
	        get: function get() {
	          return VERSION;
	        }
	      }, {
	        key: 'Default',
	        get: function get() {
	          return Default;
	        }
	      }]);

	      return Carousel;
	    })();

	    $(document).on(Event.CLICK_DATA_API, Selector.DATA_SLIDE, Carousel._dataApiClickHandler);

	    $(window).on(Event.LOAD_DATA_API, function () {
	      $(Selector.DATA_RIDE).each(function () {
	        var $carousel = $(this);
	        Carousel._jQueryInterface.call($carousel, $carousel.data());
	      });
	    });

	    /**
	     * ------------------------------------------------------------------------
	     * jQuery
	     * ------------------------------------------------------------------------
	     */

	    $.fn[NAME] = Carousel._jQueryInterface;
	    $.fn[NAME].Constructor = Carousel;
	    $.fn[NAME].noConflict = function () {
	      $.fn[NAME] = JQUERY_NO_CONFLICT;
	      return Carousel._jQueryInterface;
	    };

	    return Carousel;
	  })(jQuery);

	  module.exports = Carousel;
	});

/***/ },
/* 12 */
/***/ function(module, exports, __webpack_require__) {

	var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;'use strict';

	(function (global, factory) {
	  if (true) {
	    !(__WEBPACK_AMD_DEFINE_ARRAY__ = [exports, module, __webpack_require__(8)], __WEBPACK_AMD_DEFINE_FACTORY__ = (factory), __WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ? (__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__), __WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
	  } else if (typeof exports !== 'undefined' && typeof module !== 'undefined') {
	    factory(exports, module, require('./util'));
	  } else {
	    var mod = {
	      exports: {}
	    };
	    factory(mod.exports, mod, global.Util);
	    global.collapse = mod.exports;
	  }
	})(undefined, function (exports, module, _util) {
	  'use strict';

	  var _createClass = (function () {
	    function defineProperties(target, props) {
	      for (var i = 0; i < props.length; i++) {
	        var descriptor = props[i];descriptor.enumerable = descriptor.enumerable || false;descriptor.configurable = true;if ('value' in descriptor) descriptor.writable = true;Object.defineProperty(target, descriptor.key, descriptor);
	      }
	    }return function (Constructor, protoProps, staticProps) {
	      if (protoProps) defineProperties(Constructor.prototype, protoProps);if (staticProps) defineProperties(Constructor, staticProps);return Constructor;
	    };
	  })();

	  function _interopRequireDefault(obj) {
	    return obj && obj.__esModule ? obj : { 'default': obj };
	  }

	  function _classCallCheck(instance, Constructor) {
	    if (!(instance instanceof Constructor)) {
	      throw new TypeError('Cannot call a class as a function');
	    }
	  }

	  var _Util = _interopRequireDefault(_util);

	  /**
	   * --------------------------------------------------------------------------
	   * Bootstrap (v4.0.0-alpha.2): collapse.js
	   * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
	   * --------------------------------------------------------------------------
	   */

	  var Collapse = (function ($) {

	    /**
	     * ------------------------------------------------------------------------
	     * Constants
	     * ------------------------------------------------------------------------
	     */

	    var NAME = 'collapse';
	    var VERSION = '4.0.0-alpha';
	    var DATA_KEY = 'bs.collapse';
	    var EVENT_KEY = '.' + DATA_KEY;
	    var DATA_API_KEY = '.data-api';
	    var JQUERY_NO_CONFLICT = $.fn[NAME];
	    var TRANSITION_DURATION = 600;

	    var Default = {
	      toggle: true,
	      parent: ''
	    };

	    var DefaultType = {
	      toggle: 'boolean',
	      parent: 'string'
	    };

	    var Event = {
	      SHOW: 'show' + EVENT_KEY,
	      SHOWN: 'shown' + EVENT_KEY,
	      HIDE: 'hide' + EVENT_KEY,
	      HIDDEN: 'hidden' + EVENT_KEY,
	      CLICK_DATA_API: 'click' + EVENT_KEY + DATA_API_KEY
	    };

	    var ClassName = {
	      IN: 'in',
	      COLLAPSE: 'collapse',
	      COLLAPSING: 'collapsing',
	      COLLAPSED: 'collapsed'
	    };

	    var Dimension = {
	      WIDTH: 'width',
	      HEIGHT: 'height'
	    };

	    var Selector = {
	      ACTIVES: '.panel > .in, .panel > .collapsing',
	      DATA_TOGGLE: '[data-toggle="collapse"]'
	    };

	    /**
	     * ------------------------------------------------------------------------
	     * Class Definition
	     * ------------------------------------------------------------------------
	     */

	    var Collapse = (function () {
	      function Collapse(element, config) {
	        _classCallCheck(this, Collapse);

	        this._isTransitioning = false;
	        this._element = element;
	        this._config = this._getConfig(config);
	        this._triggerArray = $.makeArray($('[data-toggle="collapse"][href="#' + element.id + '"],' + ('[data-toggle="collapse"][data-target="#' + element.id + '"]')));

	        this._parent = this._config.parent ? this._getParent() : null;

	        if (!this._config.parent) {
	          this._addAriaAndCollapsedClass(this._element, this._triggerArray);
	        }

	        if (this._config.toggle) {
	          this.toggle();
	        }
	      }

	      /**
	       * ------------------------------------------------------------------------
	       * Data Api implementation
	       * ------------------------------------------------------------------------
	       */

	      // getters

	      _createClass(Collapse, [{
	        key: 'toggle',

	        // public

	        value: function toggle() {
	          if ($(this._element).hasClass(ClassName.IN)) {
	            this.hide();
	          } else {
	            this.show();
	          }
	        }
	      }, {
	        key: 'show',
	        value: function show() {
	          var _this = this;

	          if (this._isTransitioning || $(this._element).hasClass(ClassName.IN)) {
	            return;
	          }

	          var actives = undefined;
	          var activesData = undefined;

	          if (this._parent) {
	            actives = $.makeArray($(Selector.ACTIVES));
	            if (!actives.length) {
	              actives = null;
	            }
	          }

	          if (actives) {
	            activesData = $(actives).data(DATA_KEY);
	            if (activesData && activesData._isTransitioning) {
	              return;
	            }
	          }

	          var startEvent = $.Event(Event.SHOW);
	          $(this._element).trigger(startEvent);
	          if (startEvent.isDefaultPrevented()) {
	            return;
	          }

	          if (actives) {
	            Collapse._jQueryInterface.call($(actives), 'hide');
	            if (!activesData) {
	              $(actives).data(DATA_KEY, null);
	            }
	          }

	          var dimension = this._getDimension();

	          $(this._element).removeClass(ClassName.COLLAPSE).addClass(ClassName.COLLAPSING);

	          this._element.style[dimension] = 0;
	          this._element.setAttribute('aria-expanded', true);

	          if (this._triggerArray.length) {
	            $(this._triggerArray).removeClass(ClassName.COLLAPSED).attr('aria-expanded', true);
	          }

	          this.setTransitioning(true);

	          var complete = function complete() {
	            $(_this._element).removeClass(ClassName.COLLAPSING).addClass(ClassName.COLLAPSE).addClass(ClassName.IN);

	            _this._element.style[dimension] = '';

	            _this.setTransitioning(false);

	            $(_this._element).trigger(Event.SHOWN);
	          };

	          if (!_Util['default'].supportsTransitionEnd()) {
	            complete();
	            return;
	          }

	          var capitalizedDimension = dimension[0].toUpperCase() + dimension.slice(1);
	          var scrollSize = 'scroll' + capitalizedDimension;

	          $(this._element).one(_Util['default'].TRANSITION_END, complete).emulateTransitionEnd(TRANSITION_DURATION);

	          this._element.style[dimension] = this._element[scrollSize] + 'px';
	        }
	      }, {
	        key: 'hide',
	        value: function hide() {
	          var _this2 = this;

	          if (this._isTransitioning || !$(this._element).hasClass(ClassName.IN)) {
	            return;
	          }

	          var startEvent = $.Event(Event.HIDE);
	          $(this._element).trigger(startEvent);
	          if (startEvent.isDefaultPrevented()) {
	            return;
	          }

	          var dimension = this._getDimension();
	          var offsetDimension = dimension === Dimension.WIDTH ? 'offsetWidth' : 'offsetHeight';

	          this._element.style[dimension] = this._element[offsetDimension] + 'px';

	          _Util['default'].reflow(this._element);

	          $(this._element).addClass(ClassName.COLLAPSING).removeClass(ClassName.COLLAPSE).removeClass(ClassName.IN);

	          this._element.setAttribute('aria-expanded', false);

	          if (this._triggerArray.length) {
	            $(this._triggerArray).addClass(ClassName.COLLAPSED).attr('aria-expanded', false);
	          }

	          this.setTransitioning(true);

	          var complete = function complete() {
	            _this2.setTransitioning(false);
	            $(_this2._element).removeClass(ClassName.COLLAPSING).addClass(ClassName.COLLAPSE).trigger(Event.HIDDEN);
	          };

	          this._element.style[dimension] = 0;

	          if (!_Util['default'].supportsTransitionEnd()) {
	            complete();
	            return;
	          }

	          $(this._element).one(_Util['default'].TRANSITION_END, complete).emulateTransitionEnd(TRANSITION_DURATION);
	        }
	      }, {
	        key: 'setTransitioning',
	        value: function setTransitioning(isTransitioning) {
	          this._isTransitioning = isTransitioning;
	        }
	      }, {
	        key: 'dispose',
	        value: function dispose() {
	          $.removeData(this._element, DATA_KEY);

	          this._config = null;
	          this._parent = null;
	          this._element = null;
	          this._triggerArray = null;
	          this._isTransitioning = null;
	        }

	        // private

	      }, {
	        key: '_getConfig',
	        value: function _getConfig(config) {
	          config = $.extend({}, Default, config);
	          config.toggle = Boolean(config.toggle); // coerce string values
	          _Util['default'].typeCheckConfig(NAME, config, DefaultType);
	          return config;
	        }
	      }, {
	        key: '_getDimension',
	        value: function _getDimension() {
	          var hasWidth = $(this._element).hasClass(Dimension.WIDTH);
	          return hasWidth ? Dimension.WIDTH : Dimension.HEIGHT;
	        }
	      }, {
	        key: '_getParent',
	        value: function _getParent() {
	          var _this3 = this;

	          var parent = $(this._config.parent)[0];
	          var selector = '[data-toggle="collapse"][data-parent="' + this._config.parent + '"]';

	          $(parent).find(selector).each(function (i, element) {
	            _this3._addAriaAndCollapsedClass(Collapse._getTargetFromElement(element), [element]);
	          });

	          return parent;
	        }
	      }, {
	        key: '_addAriaAndCollapsedClass',
	        value: function _addAriaAndCollapsedClass(element, triggerArray) {
	          if (element) {
	            var isOpen = $(element).hasClass(ClassName.IN);
	            element.setAttribute('aria-expanded', isOpen);

	            if (triggerArray.length) {
	              $(triggerArray).toggleClass(ClassName.COLLAPSED, !isOpen).attr('aria-expanded', isOpen);
	            }
	          }
	        }

	        // static

	      }], [{
	        key: '_getTargetFromElement',
	        value: function _getTargetFromElement(element) {
	          var selector = _Util['default'].getSelectorFromElement(element);
	          return selector ? $(selector)[0] : null;
	        }
	      }, {
	        key: '_jQueryInterface',
	        value: function _jQueryInterface(config) {
	          return this.each(function () {
	            var $this = $(this);
	            var data = $this.data(DATA_KEY);
	            var _config = $.extend({}, Default, $this.data(), typeof config === 'object' && config);

	            if (!data && _config.toggle && /show|hide/.test(config)) {
	              _config.toggle = false;
	            }

	            if (!data) {
	              data = new Collapse(this, _config);
	              $this.data(DATA_KEY, data);
	            }

	            if (typeof config === 'string') {
	              if (data[config] === undefined) {
	                throw new Error('No method named "' + config + '"');
	              }
	              data[config]();
	            }
	          });
	        }
	      }, {
	        key: 'VERSION',
	        get: function get() {
	          return VERSION;
	        }
	      }, {
	        key: 'Default',
	        get: function get() {
	          return Default;
	        }
	      }]);

	      return Collapse;
	    })();

	    $(document).on(Event.CLICK_DATA_API, Selector.DATA_TOGGLE, function (event) {
	      event.preventDefault();

	      var target = Collapse._getTargetFromElement(this);
	      var data = $(target).data(DATA_KEY);
	      var config = data ? 'toggle' : $(this).data();

	      Collapse._jQueryInterface.call($(target), config);
	    });

	    /**
	     * ------------------------------------------------------------------------
	     * jQuery
	     * ------------------------------------------------------------------------
	     */

	    $.fn[NAME] = Collapse._jQueryInterface;
	    $.fn[NAME].Constructor = Collapse;
	    $.fn[NAME].noConflict = function () {
	      $.fn[NAME] = JQUERY_NO_CONFLICT;
	      return Collapse._jQueryInterface;
	    };

	    return Collapse;
	  })(jQuery);

	  module.exports = Collapse;
	});

/***/ },
/* 13 */
/***/ function(module, exports, __webpack_require__) {

	var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;'use strict';

	(function (global, factory) {
	  if (true) {
	    !(__WEBPACK_AMD_DEFINE_ARRAY__ = [exports, module, __webpack_require__(8)], __WEBPACK_AMD_DEFINE_FACTORY__ = (factory), __WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ? (__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__), __WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
	  } else if (typeof exports !== 'undefined' && typeof module !== 'undefined') {
	    factory(exports, module, require('./util'));
	  } else {
	    var mod = {
	      exports: {}
	    };
	    factory(mod.exports, mod, global.Util);
	    global.dropdown = mod.exports;
	  }
	})(undefined, function (exports, module, _util) {
	  'use strict';

	  var _createClass = (function () {
	    function defineProperties(target, props) {
	      for (var i = 0; i < props.length; i++) {
	        var descriptor = props[i];descriptor.enumerable = descriptor.enumerable || false;descriptor.configurable = true;if ('value' in descriptor) descriptor.writable = true;Object.defineProperty(target, descriptor.key, descriptor);
	      }
	    }return function (Constructor, protoProps, staticProps) {
	      if (protoProps) defineProperties(Constructor.prototype, protoProps);if (staticProps) defineProperties(Constructor, staticProps);return Constructor;
	    };
	  })();

	  function _interopRequireDefault(obj) {
	    return obj && obj.__esModule ? obj : { 'default': obj };
	  }

	  function _classCallCheck(instance, Constructor) {
	    if (!(instance instanceof Constructor)) {
	      throw new TypeError('Cannot call a class as a function');
	    }
	  }

	  var _Util = _interopRequireDefault(_util);

	  /**
	   * --------------------------------------------------------------------------
	   * Bootstrap (v4.0.0-alpha.2): dropdown.js
	   * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
	   * --------------------------------------------------------------------------
	   */

	  var Dropdown = (function ($) {

	    /**
	     * ------------------------------------------------------------------------
	     * Constants
	     * ------------------------------------------------------------------------
	     */

	    var NAME = 'dropdown';
	    var VERSION = '4.0.0-alpha';
	    var DATA_KEY = 'bs.dropdown';
	    var EVENT_KEY = '.' + DATA_KEY;
	    var DATA_API_KEY = '.data-api';
	    var JQUERY_NO_CONFLICT = $.fn[NAME];

	    var Event = {
	      HIDE: 'hide' + EVENT_KEY,
	      HIDDEN: 'hidden' + EVENT_KEY,
	      SHOW: 'show' + EVENT_KEY,
	      SHOWN: 'shown' + EVENT_KEY,
	      CLICK: 'click' + EVENT_KEY,
	      CLICK_DATA_API: 'click' + EVENT_KEY + DATA_API_KEY,
	      KEYDOWN_DATA_API: 'keydown' + EVENT_KEY + DATA_API_KEY
	    };

	    var ClassName = {
	      BACKDROP: 'dropdown-backdrop',
	      DISABLED: 'disabled',
	      OPEN: 'open'
	    };

	    var Selector = {
	      BACKDROP: '.dropdown-backdrop',
	      DATA_TOGGLE: '[data-toggle="dropdown"]',
	      FORM_CHILD: '.dropdown form',
	      ROLE_MENU: '[role="menu"]',
	      ROLE_LISTBOX: '[role="listbox"]',
	      NAVBAR_NAV: '.navbar-nav',
	      VISIBLE_ITEMS: '[role="menu"] li:not(.disabled) a, ' + '[role="listbox"] li:not(.disabled) a'
	    };

	    /**
	     * ------------------------------------------------------------------------
	     * Class Definition
	     * ------------------------------------------------------------------------
	     */

	    var Dropdown = (function () {
	      function Dropdown(element) {
	        _classCallCheck(this, Dropdown);

	        this._element = element;

	        this._addEventListeners();
	      }

	      /**
	       * ------------------------------------------------------------------------
	       * Data Api implementation
	       * ------------------------------------------------------------------------
	       */

	      // getters

	      _createClass(Dropdown, [{
	        key: 'toggle',

	        // public

	        value: function toggle() {
	          if (this.disabled || $(this).hasClass(ClassName.DISABLED)) {
	            return false;
	          }

	          var parent = Dropdown._getParentFromElement(this);
	          var isActive = $(parent).hasClass(ClassName.OPEN);

	          Dropdown._clearMenus();

	          if (isActive) {
	            return false;
	          }

	          if ('ontouchstart' in document.documentElement && !$(parent).closest(Selector.NAVBAR_NAV).length) {

	            // if mobile we use a backdrop because click events don't delegate
	            var dropdown = document.createElement('div');
	            dropdown.className = ClassName.BACKDROP;
	            $(dropdown).insertBefore(this);
	            $(dropdown).on('click', Dropdown._clearMenus);
	          }

	          var relatedTarget = { relatedTarget: this };
	          var showEvent = $.Event(Event.SHOW, relatedTarget);

	          $(parent).trigger(showEvent);

	          if (showEvent.isDefaultPrevented()) {
	            return false;
	          }

	          this.focus();
	          this.setAttribute('aria-expanded', 'true');

	          $(parent).toggleClass(ClassName.OPEN);
	          $(parent).trigger($.Event(Event.SHOWN, relatedTarget));

	          return false;
	        }
	      }, {
	        key: 'dispose',
	        value: function dispose() {
	          $.removeData(this._element, DATA_KEY);
	          $(this._element).off(EVENT_KEY);
	          this._element = null;
	        }

	        // private

	      }, {
	        key: '_addEventListeners',
	        value: function _addEventListeners() {
	          $(this._element).on(Event.CLICK, this.toggle);
	        }

	        // static

	      }], [{
	        key: '_jQueryInterface',
	        value: function _jQueryInterface(config) {
	          return this.each(function () {
	            var data = $(this).data(DATA_KEY);

	            if (!data) {
	              $(this).data(DATA_KEY, data = new Dropdown(this));
	            }

	            if (typeof config === 'string') {
	              if (data[config] === undefined) {
	                throw new Error('No method named "' + config + '"');
	              }
	              data[config].call(this);
	            }
	          });
	        }
	      }, {
	        key: '_clearMenus',
	        value: function _clearMenus(event) {
	          if (event && event.which === 3) {
	            return;
	          }

	          var backdrop = $(Selector.BACKDROP)[0];
	          if (backdrop) {
	            backdrop.parentNode.removeChild(backdrop);
	          }

	          var toggles = $.makeArray($(Selector.DATA_TOGGLE));

	          for (var i = 0; i < toggles.length; i++) {
	            var _parent = Dropdown._getParentFromElement(toggles[i]);
	            var relatedTarget = { relatedTarget: toggles[i] };

	            if (!$(_parent).hasClass(ClassName.OPEN)) {
	              continue;
	            }

	            if (event && event.type === 'click' && /input|textarea/i.test(event.target.tagName) && $.contains(_parent, event.target)) {
	              continue;
	            }

	            var hideEvent = $.Event(Event.HIDE, relatedTarget);
	            $(_parent).trigger(hideEvent);
	            if (hideEvent.isDefaultPrevented()) {
	              continue;
	            }

	            toggles[i].setAttribute('aria-expanded', 'false');

	            $(_parent).removeClass(ClassName.OPEN).trigger($.Event(Event.HIDDEN, relatedTarget));
	          }
	        }
	      }, {
	        key: '_getParentFromElement',
	        value: function _getParentFromElement(element) {
	          var parent = undefined;
	          var selector = _Util['default'].getSelectorFromElement(element);

	          if (selector) {
	            parent = $(selector)[0];
	          }

	          return parent || element.parentNode;
	        }
	      }, {
	        key: '_dataApiKeydownHandler',
	        value: function _dataApiKeydownHandler(event) {
	          if (!/(38|40|27|32)/.test(event.which) || /input|textarea/i.test(event.target.tagName)) {
	            return;
	          }

	          event.preventDefault();
	          event.stopPropagation();

	          if (this.disabled || $(this).hasClass(ClassName.DISABLED)) {
	            return;
	          }

	          var parent = Dropdown._getParentFromElement(this);
	          var isActive = $(parent).hasClass(ClassName.OPEN);

	          if (!isActive && event.which !== 27 || isActive && event.which === 27) {

	            if (event.which === 27) {
	              var toggle = $(parent).find(Selector.DATA_TOGGLE)[0];
	              $(toggle).trigger('focus');
	            }

	            $(this).trigger('click');
	            return;
	          }

	          var items = $.makeArray($(Selector.VISIBLE_ITEMS));

	          items = items.filter(function (item) {
	            return item.offsetWidth || item.offsetHeight;
	          });

	          if (!items.length) {
	            return;
	          }

	          var index = items.indexOf(event.target);

	          if (event.which === 38 && index > 0) {
	            // up
	            index--;
	          }

	          if (event.which === 40 && index < items.length - 1) {
	            // down
	            index++;
	          }

	          if (! ~index) {
	            index = 0;
	          }

	          items[index].focus();
	        }
	      }, {
	        key: 'VERSION',
	        get: function get() {
	          return VERSION;
	        }
	      }]);

	      return Dropdown;
	    })();

	    $(document).on(Event.KEYDOWN_DATA_API, Selector.DATA_TOGGLE, Dropdown._dataApiKeydownHandler).on(Event.KEYDOWN_DATA_API, Selector.ROLE_MENU, Dropdown._dataApiKeydownHandler).on(Event.KEYDOWN_DATA_API, Selector.ROLE_LISTBOX, Dropdown._dataApiKeydownHandler).on(Event.CLICK_DATA_API, Dropdown._clearMenus).on(Event.CLICK_DATA_API, Selector.DATA_TOGGLE, Dropdown.prototype.toggle).on(Event.CLICK_DATA_API, Selector.FORM_CHILD, function (e) {
	      e.stopPropagation();
	    });

	    /**
	     * ------------------------------------------------------------------------
	     * jQuery
	     * ------------------------------------------------------------------------
	     */

	    $.fn[NAME] = Dropdown._jQueryInterface;
	    $.fn[NAME].Constructor = Dropdown;
	    $.fn[NAME].noConflict = function () {
	      $.fn[NAME] = JQUERY_NO_CONFLICT;
	      return Dropdown._jQueryInterface;
	    };

	    return Dropdown;
	  })(jQuery);

	  module.exports = Dropdown;
	});

/***/ },
/* 14 */
/***/ function(module, exports, __webpack_require__) {

	var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;'use strict';

	(function (global, factory) {
	  if (true) {
	    !(__WEBPACK_AMD_DEFINE_ARRAY__ = [exports, module, __webpack_require__(8)], __WEBPACK_AMD_DEFINE_FACTORY__ = (factory), __WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ? (__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__), __WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
	  } else if (typeof exports !== 'undefined' && typeof module !== 'undefined') {
	    factory(exports, module, require('./util'));
	  } else {
	    var mod = {
	      exports: {}
	    };
	    factory(mod.exports, mod, global.Util);
	    global.modal = mod.exports;
	  }
	})(undefined, function (exports, module, _util) {
	  'use strict';

	  var _createClass = (function () {
	    function defineProperties(target, props) {
	      for (var i = 0; i < props.length; i++) {
	        var descriptor = props[i];descriptor.enumerable = descriptor.enumerable || false;descriptor.configurable = true;if ('value' in descriptor) descriptor.writable = true;Object.defineProperty(target, descriptor.key, descriptor);
	      }
	    }return function (Constructor, protoProps, staticProps) {
	      if (protoProps) defineProperties(Constructor.prototype, protoProps);if (staticProps) defineProperties(Constructor, staticProps);return Constructor;
	    };
	  })();

	  function _interopRequireDefault(obj) {
	    return obj && obj.__esModule ? obj : { 'default': obj };
	  }

	  function _classCallCheck(instance, Constructor) {
	    if (!(instance instanceof Constructor)) {
	      throw new TypeError('Cannot call a class as a function');
	    }
	  }

	  var _Util = _interopRequireDefault(_util);

	  /**
	   * --------------------------------------------------------------------------
	   * Bootstrap (v4.0.0-alpha.2): modal.js
	   * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
	   * --------------------------------------------------------------------------
	   */

	  var Modal = (function ($) {

	    /**
	     * ------------------------------------------------------------------------
	     * Constants
	     * ------------------------------------------------------------------------
	     */

	    var NAME = 'modal';
	    var VERSION = '4.0.0-alpha';
	    var DATA_KEY = 'bs.modal';
	    var EVENT_KEY = '.' + DATA_KEY;
	    var DATA_API_KEY = '.data-api';
	    var JQUERY_NO_CONFLICT = $.fn[NAME];
	    var TRANSITION_DURATION = 300;
	    var BACKDROP_TRANSITION_DURATION = 150;

	    var Default = {
	      backdrop: true,
	      keyboard: true,
	      focus: true,
	      show: true
	    };

	    var DefaultType = {
	      backdrop: '(boolean|string)',
	      keyboard: 'boolean',
	      focus: 'boolean',
	      show: 'boolean'
	    };

	    var Event = {
	      HIDE: 'hide' + EVENT_KEY,
	      HIDDEN: 'hidden' + EVENT_KEY,
	      SHOW: 'show' + EVENT_KEY,
	      SHOWN: 'shown' + EVENT_KEY,
	      FOCUSIN: 'focusin' + EVENT_KEY,
	      RESIZE: 'resize' + EVENT_KEY,
	      CLICK_DISMISS: 'click.dismiss' + EVENT_KEY,
	      KEYDOWN_DISMISS: 'keydown.dismiss' + EVENT_KEY,
	      MOUSEUP_DISMISS: 'mouseup.dismiss' + EVENT_KEY,
	      MOUSEDOWN_DISMISS: 'mousedown.dismiss' + EVENT_KEY,
	      CLICK_DATA_API: 'click' + EVENT_KEY + DATA_API_KEY
	    };

	    var ClassName = {
	      SCROLLBAR_MEASURER: 'modal-scrollbar-measure',
	      BACKDROP: 'modal-backdrop',
	      OPEN: 'modal-open',
	      FADE: 'fade',
	      IN: 'in'
	    };

	    var Selector = {
	      DIALOG: '.modal-dialog',
	      DATA_TOGGLE: '[data-toggle="modal"]',
	      DATA_DISMISS: '[data-dismiss="modal"]',
	      FIXED_CONTENT: '.navbar-fixed-top, .navbar-fixed-bottom, .is-fixed'
	    };

	    /**
	     * ------------------------------------------------------------------------
	     * Class Definition
	     * ------------------------------------------------------------------------
	     */

	    var Modal = (function () {
	      function Modal(element, config) {
	        _classCallCheck(this, Modal);

	        this._config = this._getConfig(config);
	        this._element = element;
	        this._dialog = $(element).find(Selector.DIALOG)[0];
	        this._backdrop = null;
	        this._isShown = false;
	        this._isBodyOverflowing = false;
	        this._ignoreBackdropClick = false;
	        this._originalBodyPadding = 0;
	        this._scrollbarWidth = 0;
	      }

	      /**
	       * ------------------------------------------------------------------------
	       * Data Api implementation
	       * ------------------------------------------------------------------------
	       */

	      // getters

	      _createClass(Modal, [{
	        key: 'toggle',

	        // public

	        value: function toggle(relatedTarget) {
	          return this._isShown ? this.hide() : this.show(relatedTarget);
	        }
	      }, {
	        key: 'show',
	        value: function show(relatedTarget) {
	          var _this = this;

	          var showEvent = $.Event(Event.SHOW, {
	            relatedTarget: relatedTarget
	          });

	          $(this._element).trigger(showEvent);

	          if (this._isShown || showEvent.isDefaultPrevented()) {
	            return;
	          }

	          this._isShown = true;

	          this._checkScrollbar();
	          this._setScrollbar();

	          $(document.body).addClass(ClassName.OPEN);

	          this._setEscapeEvent();
	          this._setResizeEvent();

	          $(this._element).on(Event.CLICK_DISMISS, Selector.DATA_DISMISS, $.proxy(this.hide, this));

	          $(this._dialog).on(Event.MOUSEDOWN_DISMISS, function () {
	            $(_this._element).one(Event.MOUSEUP_DISMISS, function (event) {
	              if ($(event.target).is(_this._element)) {
	                _this._ignoreBackdropClick = true;
	              }
	            });
	          });

	          this._showBackdrop($.proxy(this._showElement, this, relatedTarget));
	        }
	      }, {
	        key: 'hide',
	        value: function hide(event) {
	          if (event) {
	            event.preventDefault();
	          }

	          var hideEvent = $.Event(Event.HIDE);

	          $(this._element).trigger(hideEvent);

	          if (!this._isShown || hideEvent.isDefaultPrevented()) {
	            return;
	          }

	          this._isShown = false;

	          this._setEscapeEvent();
	          this._setResizeEvent();

	          $(document).off(Event.FOCUSIN);

	          $(this._element).removeClass(ClassName.IN);

	          $(this._element).off(Event.CLICK_DISMISS);
	          $(this._dialog).off(Event.MOUSEDOWN_DISMISS);

	          if (_Util['default'].supportsTransitionEnd() && $(this._element).hasClass(ClassName.FADE)) {

	            $(this._element).one(_Util['default'].TRANSITION_END, $.proxy(this._hideModal, this)).emulateTransitionEnd(TRANSITION_DURATION);
	          } else {
	            this._hideModal();
	          }
	        }
	      }, {
	        key: 'dispose',
	        value: function dispose() {
	          $.removeData(this._element, DATA_KEY);

	          $(window).off(EVENT_KEY);
	          $(document).off(EVENT_KEY);
	          $(this._element).off(EVENT_KEY);
	          $(this._backdrop).off(EVENT_KEY);

	          this._config = null;
	          this._element = null;
	          this._dialog = null;
	          this._backdrop = null;
	          this._isShown = null;
	          this._isBodyOverflowing = null;
	          this._ignoreBackdropClick = null;
	          this._originalBodyPadding = null;
	          this._scrollbarWidth = null;
	        }

	        // private

	      }, {
	        key: '_getConfig',
	        value: function _getConfig(config) {
	          config = $.extend({}, Default, config);
	          _Util['default'].typeCheckConfig(NAME, config, DefaultType);
	          return config;
	        }
	      }, {
	        key: '_showElement',
	        value: function _showElement(relatedTarget) {
	          var _this2 = this;

	          var transition = _Util['default'].supportsTransitionEnd() && $(this._element).hasClass(ClassName.FADE);

	          if (!this._element.parentNode || this._element.parentNode.nodeType !== Node.ELEMENT_NODE) {
	            // don't move modals dom position
	            document.body.appendChild(this._element);
	          }

	          this._element.style.display = 'block';
	          this._element.scrollTop = 0;

	          if (transition) {
	            _Util['default'].reflow(this._element);
	          }

	          $(this._element).addClass(ClassName.IN);

	          if (this._config.focus) {
	            this._enforceFocus();
	          }

	          var shownEvent = $.Event(Event.SHOWN, {
	            relatedTarget: relatedTarget
	          });

	          var transitionComplete = function transitionComplete() {
	            if (_this2._config.focus) {
	              _this2._element.focus();
	            }
	            $(_this2._element).trigger(shownEvent);
	          };

	          if (transition) {
	            $(this._dialog).one(_Util['default'].TRANSITION_END, transitionComplete).emulateTransitionEnd(TRANSITION_DURATION);
	          } else {
	            transitionComplete();
	          }
	        }
	      }, {
	        key: '_enforceFocus',
	        value: function _enforceFocus() {
	          var _this3 = this;

	          $(document).off(Event.FOCUSIN) // guard against infinite focus loop
	          .on(Event.FOCUSIN, function (event) {
	            if (_this3._element !== event.target && !$(_this3._element).has(event.target).length) {
	              _this3._element.focus();
	            }
	          });
	        }
	      }, {
	        key: '_setEscapeEvent',
	        value: function _setEscapeEvent() {
	          var _this4 = this;

	          if (this._isShown && this._config.keyboard) {
	            $(this._element).on(Event.KEYDOWN_DISMISS, function (event) {
	              if (event.which === 27) {
	                _this4.hide();
	              }
	            });
	          } else if (!this._isShown) {
	            $(this._element).off(Event.KEYDOWN_DISMISS);
	          }
	        }
	      }, {
	        key: '_setResizeEvent',
	        value: function _setResizeEvent() {
	          if (this._isShown) {
	            $(window).on(Event.RESIZE, $.proxy(this._handleUpdate, this));
	          } else {
	            $(window).off(Event.RESIZE);
	          }
	        }
	      }, {
	        key: '_hideModal',
	        value: function _hideModal() {
	          var _this5 = this;

	          this._element.style.display = 'none';
	          this._showBackdrop(function () {
	            $(document.body).removeClass(ClassName.OPEN);
	            _this5._resetAdjustments();
	            _this5._resetScrollbar();
	            $(_this5._element).trigger(Event.HIDDEN);
	          });
	        }
	      }, {
	        key: '_removeBackdrop',
	        value: function _removeBackdrop() {
	          if (this._backdrop) {
	            $(this._backdrop).remove();
	            this._backdrop = null;
	          }
	        }
	      }, {
	        key: '_showBackdrop',
	        value: function _showBackdrop(callback) {
	          var _this6 = this;

	          var animate = $(this._element).hasClass(ClassName.FADE) ? ClassName.FADE : '';

	          if (this._isShown && this._config.backdrop) {
	            var doAnimate = _Util['default'].supportsTransitionEnd() && animate;

	            this._backdrop = document.createElement('div');
	            this._backdrop.className = ClassName.BACKDROP;

	            if (animate) {
	              $(this._backdrop).addClass(animate);
	            }

	            $(this._backdrop).appendTo(document.body);

	            $(this._element).on(Event.CLICK_DISMISS, function (event) {
	              if (_this6._ignoreBackdropClick) {
	                _this6._ignoreBackdropClick = false;
	                return;
	              }
	              if (event.target !== event.currentTarget) {
	                return;
	              }
	              if (_this6._config.backdrop === 'static') {
	                _this6._element.focus();
	              } else {
	                _this6.hide();
	              }
	            });

	            if (doAnimate) {
	              _Util['default'].reflow(this._backdrop);
	            }

	            $(this._backdrop).addClass(ClassName.IN);

	            if (!callback) {
	              return;
	            }

	            if (!doAnimate) {
	              callback();
	              return;
	            }

	            $(this._backdrop).one(_Util['default'].TRANSITION_END, callback).emulateTransitionEnd(BACKDROP_TRANSITION_DURATION);
	          } else if (!this._isShown && this._backdrop) {
	            $(this._backdrop).removeClass(ClassName.IN);

	            var callbackRemove = function callbackRemove() {
	              _this6._removeBackdrop();
	              if (callback) {
	                callback();
	              }
	            };

	            if (_Util['default'].supportsTransitionEnd() && $(this._element).hasClass(ClassName.FADE)) {
	              $(this._backdrop).one(_Util['default'].TRANSITION_END, callbackRemove).emulateTransitionEnd(BACKDROP_TRANSITION_DURATION);
	            } else {
	              callbackRemove();
	            }
	          } else if (callback) {
	            callback();
	          }
	        }

	        // ----------------------------------------------------------------------
	        // the following methods are used to handle overflowing modals
	        // todo (fat): these should probably be refactored out of modal.js
	        // ----------------------------------------------------------------------

	      }, {
	        key: '_handleUpdate',
	        value: function _handleUpdate() {
	          this._adjustDialog();
	        }
	      }, {
	        key: '_adjustDialog',
	        value: function _adjustDialog() {
	          var isModalOverflowing = this._element.scrollHeight > document.documentElement.clientHeight;

	          if (!this._isBodyOverflowing && isModalOverflowing) {
	            this._element.style.paddingLeft = this._scrollbarWidth + 'px';
	          }

	          if (this._isBodyOverflowing && !isModalOverflowing) {
	            this._element.style.paddingRight = this._scrollbarWidth + 'px~';
	          }
	        }
	      }, {
	        key: '_resetAdjustments',
	        value: function _resetAdjustments() {
	          this._element.style.paddingLeft = '';
	          this._element.style.paddingRight = '';
	        }
	      }, {
	        key: '_checkScrollbar',
	        value: function _checkScrollbar() {
	          var fullWindowWidth = window.innerWidth;
	          if (!fullWindowWidth) {
	            // workaround for missing window.innerWidth in IE8
	            var documentElementRect = document.documentElement.getBoundingClientRect();
	            fullWindowWidth = documentElementRect.right - Math.abs(documentElementRect.left);
	          }
	          this._isBodyOverflowing = document.body.clientWidth < fullWindowWidth;
	          this._scrollbarWidth = this._getScrollbarWidth();
	        }
	      }, {
	        key: '_setScrollbar',
	        value: function _setScrollbar() {
	          var bodyPadding = parseInt($(Selector.FIXED_CONTENT).css('padding-right') || 0, 10);

	          this._originalBodyPadding = document.body.style.paddingRight || '';

	          if (this._isBodyOverflowing) {
	            document.body.style.paddingRight = bodyPadding + this._scrollbarWidth + 'px';
	          }
	        }
	      }, {
	        key: '_resetScrollbar',
	        value: function _resetScrollbar() {
	          document.body.style.paddingRight = this._originalBodyPadding;
	        }
	      }, {
	        key: '_getScrollbarWidth',
	        value: function _getScrollbarWidth() {
	          // thx d.walsh
	          var scrollDiv = document.createElement('div');
	          scrollDiv.className = ClassName.SCROLLBAR_MEASURER;
	          document.body.appendChild(scrollDiv);
	          var scrollbarWidth = scrollDiv.offsetWidth - scrollDiv.clientWidth;
	          document.body.removeChild(scrollDiv);
	          return scrollbarWidth;
	        }

	        // static

	      }], [{
	        key: '_jQueryInterface',
	        value: function _jQueryInterface(config, relatedTarget) {
	          return this.each(function () {
	            var data = $(this).data(DATA_KEY);
	            var _config = $.extend({}, Modal.Default, $(this).data(), typeof config === 'object' && config);

	            if (!data) {
	              data = new Modal(this, _config);
	              $(this).data(DATA_KEY, data);
	            }

	            if (typeof config === 'string') {
	              if (data[config] === undefined) {
	                throw new Error('No method named "' + config + '"');
	              }
	              data[config](relatedTarget);
	            } else if (_config.show) {
	              data.show(relatedTarget);
	            }
	          });
	        }
	      }, {
	        key: 'VERSION',
	        get: function get() {
	          return VERSION;
	        }
	      }, {
	        key: 'Default',
	        get: function get() {
	          return Default;
	        }
	      }]);

	      return Modal;
	    })();

	    $(document).on(Event.CLICK_DATA_API, Selector.DATA_TOGGLE, function (event) {
	      var _this7 = this;

	      var target = undefined;
	      var selector = _Util['default'].getSelectorFromElement(this);

	      if (selector) {
	        target = $(selector)[0];
	      }

	      var config = $(target).data(DATA_KEY) ? 'toggle' : $.extend({}, $(target).data(), $(this).data());

	      if (this.tagName === 'A') {
	        event.preventDefault();
	      }

	      var $target = $(target).one(Event.SHOW, function (showEvent) {
	        if (showEvent.isDefaultPrevented()) {
	          // only register focus restorer if modal will actually get shown
	          return;
	        }

	        $target.one(Event.HIDDEN, function () {
	          if ($(_this7).is(':visible')) {
	            _this7.focus();
	          }
	        });
	      });

	      Modal._jQueryInterface.call($(target), config, this);
	    });

	    /**
	     * ------------------------------------------------------------------------
	     * jQuery
	     * ------------------------------------------------------------------------
	     */

	    $.fn[NAME] = Modal._jQueryInterface;
	    $.fn[NAME].Constructor = Modal;
	    $.fn[NAME].noConflict = function () {
	      $.fn[NAME] = JQUERY_NO_CONFLICT;
	      return Modal._jQueryInterface;
	    };

	    return Modal;
	  })(jQuery);

	  module.exports = Modal;
	});

/***/ },
/* 15 */
/***/ function(module, exports, __webpack_require__) {

	var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;'use strict';

	(function (global, factory) {
	  if (true) {
	    !(__WEBPACK_AMD_DEFINE_ARRAY__ = [exports, module, __webpack_require__(8)], __WEBPACK_AMD_DEFINE_FACTORY__ = (factory), __WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ? (__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__), __WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
	  } else if (typeof exports !== 'undefined' && typeof module !== 'undefined') {
	    factory(exports, module, require('./util'));
	  } else {
	    var mod = {
	      exports: {}
	    };
	    factory(mod.exports, mod, global.Util);
	    global.scrollspy = mod.exports;
	  }
	})(undefined, function (exports, module, _util) {
	  'use strict';

	  var _createClass = (function () {
	    function defineProperties(target, props) {
	      for (var i = 0; i < props.length; i++) {
	        var descriptor = props[i];descriptor.enumerable = descriptor.enumerable || false;descriptor.configurable = true;if ('value' in descriptor) descriptor.writable = true;Object.defineProperty(target, descriptor.key, descriptor);
	      }
	    }return function (Constructor, protoProps, staticProps) {
	      if (protoProps) defineProperties(Constructor.prototype, protoProps);if (staticProps) defineProperties(Constructor, staticProps);return Constructor;
	    };
	  })();

	  function _interopRequireDefault(obj) {
	    return obj && obj.__esModule ? obj : { 'default': obj };
	  }

	  function _classCallCheck(instance, Constructor) {
	    if (!(instance instanceof Constructor)) {
	      throw new TypeError('Cannot call a class as a function');
	    }
	  }

	  var _Util = _interopRequireDefault(_util);

	  /**
	   * --------------------------------------------------------------------------
	   * Bootstrap (v4.0.0-alpha.2): scrollspy.js
	   * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
	   * --------------------------------------------------------------------------
	   */

	  var ScrollSpy = (function ($) {

	    /**
	     * ------------------------------------------------------------------------
	     * Constants
	     * ------------------------------------------------------------------------
	     */

	    var NAME = 'scrollspy';
	    var VERSION = '4.0.0-alpha';
	    var DATA_KEY = 'bs.scrollspy';
	    var EVENT_KEY = '.' + DATA_KEY;
	    var DATA_API_KEY = '.data-api';
	    var JQUERY_NO_CONFLICT = $.fn[NAME];

	    var Default = {
	      offset: 10,
	      method: 'auto',
	      target: ''
	    };

	    var DefaultType = {
	      offset: 'number',
	      method: 'string',
	      target: '(string|element)'
	    };

	    var Event = {
	      ACTIVATE: 'activate' + EVENT_KEY,
	      SCROLL: 'scroll' + EVENT_KEY,
	      LOAD_DATA_API: 'load' + EVENT_KEY + DATA_API_KEY
	    };

	    var ClassName = {
	      DROPDOWN_ITEM: 'dropdown-item',
	      DROPDOWN_MENU: 'dropdown-menu',
	      NAV_LINK: 'nav-link',
	      NAV: 'nav',
	      ACTIVE: 'active'
	    };

	    var Selector = {
	      DATA_SPY: '[data-spy="scroll"]',
	      ACTIVE: '.active',
	      LIST_ITEM: '.list-item',
	      LI: 'li',
	      LI_DROPDOWN: 'li.dropdown',
	      NAV_LINKS: '.nav-link',
	      DROPDOWN: '.dropdown',
	      DROPDOWN_ITEMS: '.dropdown-item',
	      DROPDOWN_TOGGLE: '.dropdown-toggle'
	    };

	    var OffsetMethod = {
	      OFFSET: 'offset',
	      POSITION: 'position'
	    };

	    /**
	     * ------------------------------------------------------------------------
	     * Class Definition
	     * ------------------------------------------------------------------------
	     */

	    var ScrollSpy = (function () {
	      function ScrollSpy(element, config) {
	        _classCallCheck(this, ScrollSpy);

	        this._element = element;
	        this._scrollElement = element.tagName === 'BODY' ? window : element;
	        this._config = this._getConfig(config);
	        this._selector = this._config.target + ' ' + Selector.NAV_LINKS + ',' + (this._config.target + ' ' + Selector.DROPDOWN_ITEMS);
	        this._offsets = [];
	        this._targets = [];
	        this._activeTarget = null;
	        this._scrollHeight = 0;

	        $(this._scrollElement).on(Event.SCROLL, $.proxy(this._process, this));

	        this.refresh();
	        this._process();
	      }

	      /**
	       * ------------------------------------------------------------------------
	       * Data Api implementation
	       * ------------------------------------------------------------------------
	       */

	      // getters

	      _createClass(ScrollSpy, [{
	        key: 'refresh',

	        // public

	        value: function refresh() {
	          var _this = this;

	          var autoMethod = this._scrollElement !== this._scrollElement.window ? OffsetMethod.POSITION : OffsetMethod.OFFSET;

	          var offsetMethod = this._config.method === 'auto' ? autoMethod : this._config.method;

	          var offsetBase = offsetMethod === OffsetMethod.POSITION ? this._getScrollTop() : 0;

	          this._offsets = [];
	          this._targets = [];

	          this._scrollHeight = this._getScrollHeight();

	          var targets = $.makeArray($(this._selector));

	          targets.map(function (element) {
	            var target = undefined;
	            var targetSelector = _Util['default'].getSelectorFromElement(element);

	            if (targetSelector) {
	              target = $(targetSelector)[0];
	            }

	            if (target && (target.offsetWidth || target.offsetHeight)) {
	              // todo (fat): remove sketch reliance on jQuery position/offset
	              return [$(target)[offsetMethod]().top + offsetBase, targetSelector];
	            }
	          }).filter(function (item) {
	            return item;
	          }).sort(function (a, b) {
	            return a[0] - b[0];
	          }).forEach(function (item) {
	            _this._offsets.push(item[0]);
	            _this._targets.push(item[1]);
	          });
	        }
	      }, {
	        key: 'dispose',
	        value: function dispose() {
	          $.removeData(this._element, DATA_KEY);
	          $(this._scrollElement).off(EVENT_KEY);

	          this._element = null;
	          this._scrollElement = null;
	          this._config = null;
	          this._selector = null;
	          this._offsets = null;
	          this._targets = null;
	          this._activeTarget = null;
	          this._scrollHeight = null;
	        }

	        // private

	      }, {
	        key: '_getConfig',
	        value: function _getConfig(config) {
	          config = $.extend({}, Default, config);

	          if (typeof config.target !== 'string') {
	            var id = $(config.target).attr('id');
	            if (!id) {
	              id = _Util['default'].getUID(NAME);
	              $(config.target).attr('id', id);
	            }
	            config.target = '#' + id;
	          }

	          _Util['default'].typeCheckConfig(NAME, config, DefaultType);

	          return config;
	        }
	      }, {
	        key: '_getScrollTop',
	        value: function _getScrollTop() {
	          return this._scrollElement === window ? this._scrollElement.scrollY : this._scrollElement.scrollTop;
	        }
	      }, {
	        key: '_getScrollHeight',
	        value: function _getScrollHeight() {
	          return this._scrollElement.scrollHeight || Math.max(document.body.scrollHeight, document.documentElement.scrollHeight);
	        }
	      }, {
	        key: '_process',
	        value: function _process() {
	          var scrollTop = this._getScrollTop() + this._config.offset;
	          var scrollHeight = this._getScrollHeight();
	          var maxScroll = this._config.offset + scrollHeight - this._scrollElement.offsetHeight;

	          if (this._scrollHeight !== scrollHeight) {
	            this.refresh();
	          }

	          if (scrollTop >= maxScroll) {
	            var target = this._targets[this._targets.length - 1];

	            if (this._activeTarget !== target) {
	              this._activate(target);
	            }
	          }

	          if (this._activeTarget && scrollTop < this._offsets[0]) {
	            this._activeTarget = null;
	            this._clear();
	            return;
	          }

	          for (var i = this._offsets.length; i--;) {
	            var isActiveTarget = this._activeTarget !== this._targets[i] && scrollTop >= this._offsets[i] && (this._offsets[i + 1] === undefined || scrollTop < this._offsets[i + 1]);

	            if (isActiveTarget) {
	              this._activate(this._targets[i]);
	            }
	          }
	        }
	      }, {
	        key: '_activate',
	        value: function _activate(target) {
	          this._activeTarget = target;

	          this._clear();

	          var queries = this._selector.split(',');
	          queries = queries.map(function (selector) {
	            return selector + '[data-target="' + target + '"],' + (selector + '[href="' + target + '"]');
	          });

	          var $link = $(queries.join(','));

	          if ($link.hasClass(ClassName.DROPDOWN_ITEM)) {
	            $link.closest(Selector.DROPDOWN).find(Selector.DROPDOWN_TOGGLE).addClass(ClassName.ACTIVE);
	            $link.addClass(ClassName.ACTIVE);
	          } else {
	            // todo (fat) this is kinda sus…
	            // recursively add actives to tested nav-links
	            $link.parents(Selector.LI).find(Selector.NAV_LINKS).addClass(ClassName.ACTIVE);
	          }

	          $(this._scrollElement).trigger(Event.ACTIVATE, {
	            relatedTarget: target
	          });
	        }
	      }, {
	        key: '_clear',
	        value: function _clear() {
	          $(this._selector).filter(Selector.ACTIVE).removeClass(ClassName.ACTIVE);
	        }

	        // static

	      }], [{
	        key: '_jQueryInterface',
	        value: function _jQueryInterface(config) {
	          return this.each(function () {
	            var data = $(this).data(DATA_KEY);
	            var _config = typeof config === 'object' && config || null;

	            if (!data) {
	              data = new ScrollSpy(this, _config);
	              $(this).data(DATA_KEY, data);
	            }

	            if (typeof config === 'string') {
	              if (data[config] === undefined) {
	                throw new Error('No method named "' + config + '"');
	              }
	              data[config]();
	            }
	          });
	        }
	      }, {
	        key: 'VERSION',
	        get: function get() {
	          return VERSION;
	        }
	      }, {
	        key: 'Default',
	        get: function get() {
	          return Default;
	        }
	      }]);

	      return ScrollSpy;
	    })();

	    $(window).on(Event.LOAD_DATA_API, function () {
	      var scrollSpys = $.makeArray($(Selector.DATA_SPY));

	      for (var i = scrollSpys.length; i--;) {
	        var $spy = $(scrollSpys[i]);
	        ScrollSpy._jQueryInterface.call($spy, $spy.data());
	      }
	    });

	    /**
	     * ------------------------------------------------------------------------
	     * jQuery
	     * ------------------------------------------------------------------------
	     */

	    $.fn[NAME] = ScrollSpy._jQueryInterface;
	    $.fn[NAME].Constructor = ScrollSpy;
	    $.fn[NAME].noConflict = function () {
	      $.fn[NAME] = JQUERY_NO_CONFLICT;
	      return ScrollSpy._jQueryInterface;
	    };

	    return ScrollSpy;
	  })(jQuery);

	  module.exports = ScrollSpy;
	});

/***/ },
/* 16 */
/***/ function(module, exports, __webpack_require__) {

	var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;'use strict';

	(function (global, factory) {
	  if (true) {
	    !(__WEBPACK_AMD_DEFINE_ARRAY__ = [exports, module, __webpack_require__(8)], __WEBPACK_AMD_DEFINE_FACTORY__ = (factory), __WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ? (__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__), __WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
	  } else if (typeof exports !== 'undefined' && typeof module !== 'undefined') {
	    factory(exports, module, require('./util'));
	  } else {
	    var mod = {
	      exports: {}
	    };
	    factory(mod.exports, mod, global.Util);
	    global.tab = mod.exports;
	  }
	})(undefined, function (exports, module, _util) {
	  'use strict';

	  var _createClass = (function () {
	    function defineProperties(target, props) {
	      for (var i = 0; i < props.length; i++) {
	        var descriptor = props[i];descriptor.enumerable = descriptor.enumerable || false;descriptor.configurable = true;if ('value' in descriptor) descriptor.writable = true;Object.defineProperty(target, descriptor.key, descriptor);
	      }
	    }return function (Constructor, protoProps, staticProps) {
	      if (protoProps) defineProperties(Constructor.prototype, protoProps);if (staticProps) defineProperties(Constructor, staticProps);return Constructor;
	    };
	  })();

	  function _interopRequireDefault(obj) {
	    return obj && obj.__esModule ? obj : { 'default': obj };
	  }

	  function _classCallCheck(instance, Constructor) {
	    if (!(instance instanceof Constructor)) {
	      throw new TypeError('Cannot call a class as a function');
	    }
	  }

	  var _Util = _interopRequireDefault(_util);

	  /**
	   * --------------------------------------------------------------------------
	   * Bootstrap (v4.0.0-alpha.2): tab.js
	   * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
	   * --------------------------------------------------------------------------
	   */

	  var Tab = (function ($) {

	    /**
	     * ------------------------------------------------------------------------
	     * Constants
	     * ------------------------------------------------------------------------
	     */

	    var NAME = 'tab';
	    var VERSION = '4.0.0-alpha';
	    var DATA_KEY = 'bs.tab';
	    var EVENT_KEY = '.' + DATA_KEY;
	    var DATA_API_KEY = '.data-api';
	    var JQUERY_NO_CONFLICT = $.fn[NAME];
	    var TRANSITION_DURATION = 150;

	    var Event = {
	      HIDE: 'hide' + EVENT_KEY,
	      HIDDEN: 'hidden' + EVENT_KEY,
	      SHOW: 'show' + EVENT_KEY,
	      SHOWN: 'shown' + EVENT_KEY,
	      CLICK_DATA_API: 'click' + EVENT_KEY + DATA_API_KEY
	    };

	    var ClassName = {
	      DROPDOWN_MENU: 'dropdown-menu',
	      ACTIVE: 'active',
	      FADE: 'fade',
	      IN: 'in'
	    };

	    var Selector = {
	      A: 'a',
	      LI: 'li',
	      DROPDOWN: '.dropdown',
	      UL: 'ul:not(.dropdown-menu)',
	      FADE_CHILD: '> .nav-item .fade, > .fade',
	      ACTIVE: '.active',
	      ACTIVE_CHILD: '> .nav-item > .active, > .active',
	      DATA_TOGGLE: '[data-toggle="tab"], [data-toggle="pill"]',
	      DROPDOWN_TOGGLE: '.dropdown-toggle',
	      DROPDOWN_ACTIVE_CHILD: '> .dropdown-menu .active'
	    };

	    /**
	     * ------------------------------------------------------------------------
	     * Class Definition
	     * ------------------------------------------------------------------------
	     */

	    var Tab = (function () {
	      function Tab(element) {
	        _classCallCheck(this, Tab);

	        this._element = element;
	      }

	      /**
	       * ------------------------------------------------------------------------
	       * Data Api implementation
	       * ------------------------------------------------------------------------
	       */

	      // getters

	      _createClass(Tab, [{
	        key: 'show',

	        // public

	        value: function show() {
	          var _this = this;

	          if (this._element.parentNode && this._element.parentNode.nodeType === Node.ELEMENT_NODE && $(this._element).hasClass(ClassName.ACTIVE)) {
	            return;
	          }

	          var target = undefined;
	          var previous = undefined;
	          var ulElement = $(this._element).closest(Selector.UL)[0];
	          var selector = _Util['default'].getSelectorFromElement(this._element);

	          if (ulElement) {
	            previous = $.makeArray($(ulElement).find(Selector.ACTIVE));
	            previous = previous[previous.length - 1];
	          }

	          var hideEvent = $.Event(Event.HIDE, {
	            relatedTarget: this._element
	          });

	          var showEvent = $.Event(Event.SHOW, {
	            relatedTarget: previous
	          });

	          if (previous) {
	            $(previous).trigger(hideEvent);
	          }

	          $(this._element).trigger(showEvent);

	          if (showEvent.isDefaultPrevented() || hideEvent.isDefaultPrevented()) {
	            return;
	          }

	          if (selector) {
	            target = $(selector)[0];
	          }

	          this._activate(this._element, ulElement);

	          var complete = function complete() {
	            var hiddenEvent = $.Event(Event.HIDDEN, {
	              relatedTarget: _this._element
	            });

	            var shownEvent = $.Event(Event.SHOWN, {
	              relatedTarget: previous
	            });

	            $(previous).trigger(hiddenEvent);
	            $(_this._element).trigger(shownEvent);
	          };

	          if (target) {
	            this._activate(target, target.parentNode, complete);
	          } else {
	            complete();
	          }
	        }
	      }, {
	        key: 'dispose',
	        value: function dispose() {
	          $.removeClass(this._element, DATA_KEY);
	          this._element = null;
	        }

	        // private

	      }, {
	        key: '_activate',
	        value: function _activate(element, container, callback) {
	          var active = $(container).find(Selector.ACTIVE_CHILD)[0];
	          var isTransitioning = callback && _Util['default'].supportsTransitionEnd() && (active && $(active).hasClass(ClassName.FADE) || Boolean($(container).find(Selector.FADE_CHILD)[0]));

	          var complete = $.proxy(this._transitionComplete, this, element, active, isTransitioning, callback);

	          if (active && isTransitioning) {
	            $(active).one(_Util['default'].TRANSITION_END, complete).emulateTransitionEnd(TRANSITION_DURATION);
	          } else {
	            complete();
	          }

	          if (active) {
	            $(active).removeClass(ClassName.IN);
	          }
	        }
	      }, {
	        key: '_transitionComplete',
	        value: function _transitionComplete(element, active, isTransitioning, callback) {
	          if (active) {
	            $(active).removeClass(ClassName.ACTIVE);

	            var dropdownChild = $(active).find(Selector.DROPDOWN_ACTIVE_CHILD)[0];

	            if (dropdownChild) {
	              $(dropdownChild).removeClass(ClassName.ACTIVE);
	            }

	            active.setAttribute('aria-expanded', false);
	          }

	          $(element).addClass(ClassName.ACTIVE);
	          element.setAttribute('aria-expanded', true);

	          if (isTransitioning) {
	            _Util['default'].reflow(element);
	            $(element).addClass(ClassName.IN);
	          } else {
	            $(element).removeClass(ClassName.FADE);
	          }

	          if (element.parentNode && $(element.parentNode).hasClass(ClassName.DROPDOWN_MENU)) {

	            var dropdownElement = $(element).closest(Selector.DROPDOWN)[0];
	            if (dropdownElement) {
	              $(dropdownElement).find(Selector.DROPDOWN_TOGGLE).addClass(ClassName.ACTIVE);
	            }

	            element.setAttribute('aria-expanded', true);
	          }

	          if (callback) {
	            callback();
	          }
	        }

	        // static

	      }], [{
	        key: '_jQueryInterface',
	        value: function _jQueryInterface(config) {
	          return this.each(function () {
	            var $this = $(this);
	            var data = $this.data(DATA_KEY);

	            if (!data) {
	              data = data = new Tab(this);
	              $this.data(DATA_KEY, data);
	            }

	            if (typeof config === 'string') {
	              if (data[config] === undefined) {
	                throw new Error('No method named "' + config + '"');
	              }
	              data[config]();
	            }
	          });
	        }
	      }, {
	        key: 'VERSION',
	        get: function get() {
	          return VERSION;
	        }
	      }]);

	      return Tab;
	    })();

	    $(document).on(Event.CLICK_DATA_API, Selector.DATA_TOGGLE, function (event) {
	      event.preventDefault();
	      Tab._jQueryInterface.call($(this), 'show');
	    });

	    /**
	     * ------------------------------------------------------------------------
	     * jQuery
	     * ------------------------------------------------------------------------
	     */

	    $.fn[NAME] = Tab._jQueryInterface;
	    $.fn[NAME].Constructor = Tab;
	    $.fn[NAME].noConflict = function () {
	      $.fn[NAME] = JQUERY_NO_CONFLICT;
	      return Tab._jQueryInterface;
	    };

	    return Tab;
	  })(jQuery);

	  module.exports = Tab;
	});

/***/ },
/* 17 */
/***/ function(module, exports, __webpack_require__) {

	var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;'use strict';

	(function (global, factory) {
	  if (true) {
	    !(__WEBPACK_AMD_DEFINE_ARRAY__ = [exports, module, __webpack_require__(8)], __WEBPACK_AMD_DEFINE_FACTORY__ = (factory), __WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ? (__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__), __WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
	  } else if (typeof exports !== 'undefined' && typeof module !== 'undefined') {
	    factory(exports, module, require('./util'));
	  } else {
	    var mod = {
	      exports: {}
	    };
	    factory(mod.exports, mod, global.Util);
	    global.tooltip = mod.exports;
	  }
	})(undefined, function (exports, module, _util) {
	  /* global Tether */

	  'use strict';

	  var _createClass = (function () {
	    function defineProperties(target, props) {
	      for (var i = 0; i < props.length; i++) {
	        var descriptor = props[i];descriptor.enumerable = descriptor.enumerable || false;descriptor.configurable = true;if ('value' in descriptor) descriptor.writable = true;Object.defineProperty(target, descriptor.key, descriptor);
	      }
	    }return function (Constructor, protoProps, staticProps) {
	      if (protoProps) defineProperties(Constructor.prototype, protoProps);if (staticProps) defineProperties(Constructor, staticProps);return Constructor;
	    };
	  })();

	  function _interopRequireDefault(obj) {
	    return obj && obj.__esModule ? obj : { 'default': obj };
	  }

	  function _classCallCheck(instance, Constructor) {
	    if (!(instance instanceof Constructor)) {
	      throw new TypeError('Cannot call a class as a function');
	    }
	  }

	  var _Util = _interopRequireDefault(_util);

	  /**
	   * --------------------------------------------------------------------------
	   * Bootstrap (v4.0.0-alpha.2): tooltip.js
	   * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
	   * --------------------------------------------------------------------------
	   */

	  var Tooltip = (function ($) {

	    /**
	     * Check for Tether dependency
	     * Tether - http://github.hubspot.com/tether/
	     */
	    if (window.Tether === undefined) {
	      throw new Error('Bootstrap tooltips require Tether (http://github.hubspot.com/tether/)');
	    }

	    /**
	     * ------------------------------------------------------------------------
	     * Constants
	     * ------------------------------------------------------------------------
	     */

	    var NAME = 'tooltip';
	    var VERSION = '4.0.0-alpha';
	    var DATA_KEY = 'bs.tooltip';
	    var EVENT_KEY = '.' + DATA_KEY;
	    var JQUERY_NO_CONFLICT = $.fn[NAME];
	    var TRANSITION_DURATION = 150;
	    var CLASS_PREFIX = 'bs-tether';

	    var Default = {
	      animation: true,
	      template: '<div class="tooltip" role="tooltip">' + '<div class="tooltip-arrow"></div>' + '<div class="tooltip-inner"></div></div>',
	      trigger: 'hover focus',
	      title: '',
	      delay: 0,
	      html: false,
	      selector: false,
	      placement: 'top',
	      offset: '0 0',
	      constraints: []
	    };

	    var DefaultType = {
	      animation: 'boolean',
	      template: 'string',
	      title: '(string|element|function)',
	      trigger: 'string',
	      delay: '(number|object)',
	      html: 'boolean',
	      selector: '(string|boolean)',
	      placement: '(string|function)',
	      offset: 'string',
	      constraints: 'array'
	    };

	    var AttachmentMap = {
	      TOP: 'bottom center',
	      RIGHT: 'middle left',
	      BOTTOM: 'top center',
	      LEFT: 'middle right'
	    };

	    var HoverState = {
	      IN: 'in',
	      OUT: 'out'
	    };

	    var Event = {
	      HIDE: 'hide' + EVENT_KEY,
	      HIDDEN: 'hidden' + EVENT_KEY,
	      SHOW: 'show' + EVENT_KEY,
	      SHOWN: 'shown' + EVENT_KEY,
	      INSERTED: 'inserted' + EVENT_KEY,
	      CLICK: 'click' + EVENT_KEY,
	      FOCUSIN: 'focusin' + EVENT_KEY,
	      FOCUSOUT: 'focusout' + EVENT_KEY,
	      MOUSEENTER: 'mouseenter' + EVENT_KEY,
	      MOUSELEAVE: 'mouseleave' + EVENT_KEY
	    };

	    var ClassName = {
	      FADE: 'fade',
	      IN: 'in'
	    };

	    var Selector = {
	      TOOLTIP: '.tooltip',
	      TOOLTIP_INNER: '.tooltip-inner'
	    };

	    var TetherClass = {
	      element: false,
	      enabled: false
	    };

	    var Trigger = {
	      HOVER: 'hover',
	      FOCUS: 'focus',
	      CLICK: 'click',
	      MANUAL: 'manual'
	    };

	    /**
	     * ------------------------------------------------------------------------
	     * Class Definition
	     * ------------------------------------------------------------------------
	     */

	    var Tooltip = (function () {
	      function Tooltip(element, config) {
	        _classCallCheck(this, Tooltip);

	        // private
	        this._isEnabled = true;
	        this._timeout = 0;
	        this._hoverState = '';
	        this._activeTrigger = {};
	        this._tether = null;

	        // protected
	        this.element = element;
	        this.config = this._getConfig(config);
	        this.tip = null;

	        this._setListeners();
	      }

	      /**
	       * ------------------------------------------------------------------------
	       * jQuery
	       * ------------------------------------------------------------------------
	       */

	      // getters

	      _createClass(Tooltip, [{
	        key: 'enable',

	        // public

	        value: function enable() {
	          this._isEnabled = true;
	        }
	      }, {
	        key: 'disable',
	        value: function disable() {
	          this._isEnabled = false;
	        }
	      }, {
	        key: 'toggleEnabled',
	        value: function toggleEnabled() {
	          this._isEnabled = !this._isEnabled;
	        }
	      }, {
	        key: 'toggle',
	        value: function toggle(event) {
	          if (event) {
	            var dataKey = this.constructor.DATA_KEY;
	            var context = $(event.currentTarget).data(dataKey);

	            if (!context) {
	              context = new this.constructor(event.currentTarget, this._getDelegateConfig());
	              $(event.currentTarget).data(dataKey, context);
	            }

	            context._activeTrigger.click = !context._activeTrigger.click;

	            if (context._isWithActiveTrigger()) {
	              context._enter(null, context);
	            } else {
	              context._leave(null, context);
	            }
	          } else {

	            if ($(this.getTipElement()).hasClass(ClassName.IN)) {
	              this._leave(null, this);
	              return;
	            }

	            this._enter(null, this);
	          }
	        }
	      }, {
	        key: 'dispose',
	        value: function dispose() {
	          clearTimeout(this._timeout);

	          this.cleanupTether();

	          $.removeData(this.element, this.constructor.DATA_KEY);

	          $(this.element).off(this.constructor.EVENT_KEY);

	          if (this.tip) {
	            $(this.tip).remove();
	          }

	          this._isEnabled = null;
	          this._timeout = null;
	          this._hoverState = null;
	          this._activeTrigger = null;
	          this._tether = null;

	          this.element = null;
	          this.config = null;
	          this.tip = null;
	        }
	      }, {
	        key: 'show',
	        value: function show() {
	          var _this = this;

	          var showEvent = $.Event(this.constructor.Event.SHOW);

	          if (this.isWithContent() && this._isEnabled) {
	            $(this.element).trigger(showEvent);

	            var isInTheDom = $.contains(this.element.ownerDocument.documentElement, this.element);

	            if (showEvent.isDefaultPrevented() || !isInTheDom) {
	              return;
	            }

	            var tip = this.getTipElement();
	            var tipId = _Util['default'].getUID(this.constructor.NAME);

	            tip.setAttribute('id', tipId);
	            this.element.setAttribute('aria-describedby', tipId);

	            this.setContent();

	            if (this.config.animation) {
	              $(tip).addClass(ClassName.FADE);
	            }

	            var placement = typeof this.config.placement === 'function' ? this.config.placement.call(this, tip, this.element) : this.config.placement;

	            var attachment = this._getAttachment(placement);

	            $(tip).data(this.constructor.DATA_KEY, this).appendTo(document.body);

	            $(this.element).trigger(this.constructor.Event.INSERTED);

	            this._tether = new Tether({
	              attachment: attachment,
	              element: tip,
	              target: this.element,
	              classes: TetherClass,
	              classPrefix: CLASS_PREFIX,
	              offset: this.config.offset,
	              constraints: this.config.constraints,
	              addTargetClasses: false
	            });

	            _Util['default'].reflow(tip);
	            this._tether.position();

	            $(tip).addClass(ClassName.IN);

	            var complete = function complete() {
	              var prevHoverState = _this._hoverState;
	              _this._hoverState = null;

	              $(_this.element).trigger(_this.constructor.Event.SHOWN);

	              if (prevHoverState === HoverState.OUT) {
	                _this._leave(null, _this);
	              }
	            };

	            if (_Util['default'].supportsTransitionEnd() && $(this.tip).hasClass(ClassName.FADE)) {
	              $(this.tip).one(_Util['default'].TRANSITION_END, complete).emulateTransitionEnd(Tooltip._TRANSITION_DURATION);
	              return;
	            }

	            complete();
	          }
	        }
	      }, {
	        key: 'hide',
	        value: function hide(callback) {
	          var _this2 = this;

	          var tip = this.getTipElement();
	          var hideEvent = $.Event(this.constructor.Event.HIDE);
	          var complete = function complete() {
	            if (_this2._hoverState !== HoverState.IN && tip.parentNode) {
	              tip.parentNode.removeChild(tip);
	            }

	            _this2.element.removeAttribute('aria-describedby');
	            $(_this2.element).trigger(_this2.constructor.Event.HIDDEN);
	            _this2.cleanupTether();

	            if (callback) {
	              callback();
	            }
	          };

	          $(this.element).trigger(hideEvent);

	          if (hideEvent.isDefaultPrevented()) {
	            return;
	          }

	          $(tip).removeClass(ClassName.IN);

	          if (_Util['default'].supportsTransitionEnd() && $(this.tip).hasClass(ClassName.FADE)) {

	            $(tip).one(_Util['default'].TRANSITION_END, complete).emulateTransitionEnd(TRANSITION_DURATION);
	          } else {
	            complete();
	          }

	          this._hoverState = '';
	        }

	        // protected

	      }, {
	        key: 'isWithContent',
	        value: function isWithContent() {
	          return Boolean(this.getTitle());
	        }
	      }, {
	        key: 'getTipElement',
	        value: function getTipElement() {
	          return this.tip = this.tip || $(this.config.template)[0];
	        }
	      }, {
	        key: 'setContent',
	        value: function setContent() {
	          var $tip = $(this.getTipElement());

	          this.setElementContent($tip.find(Selector.TOOLTIP_INNER), this.getTitle());

	          $tip.removeClass(ClassName.FADE).removeClass(ClassName.IN);

	          this.cleanupTether();
	        }
	      }, {
	        key: 'setElementContent',
	        value: function setElementContent($element, content) {
	          var html = this.config.html;
	          if (typeof content === 'object' && (content.nodeType || content.jquery)) {
	            // content is a DOM node or a jQuery
	            if (html) {
	              if (!$(content).parent().is($element)) {
	                $element.empty().append(content);
	              }
	            } else {
	              $element.text($(content).text());
	            }
	          } else {
	            $element[html ? 'html' : 'text'](content);
	          }
	        }
	      }, {
	        key: 'getTitle',
	        value: function getTitle() {
	          var title = this.element.getAttribute('data-original-title');

	          if (!title) {
	            title = typeof this.config.title === 'function' ? this.config.title.call(this.element) : this.config.title;
	          }

	          return title;
	        }
	      }, {
	        key: 'cleanupTether',
	        value: function cleanupTether() {
	          if (this._tether) {
	            this._tether.destroy();
	          }
	        }

	        // private

	      }, {
	        key: '_getAttachment',
	        value: function _getAttachment(placement) {
	          return AttachmentMap[placement.toUpperCase()];
	        }
	      }, {
	        key: '_setListeners',
	        value: function _setListeners() {
	          var _this3 = this;

	          var triggers = this.config.trigger.split(' ');

	          triggers.forEach(function (trigger) {
	            if (trigger === 'click') {
	              $(_this3.element).on(_this3.constructor.Event.CLICK, _this3.config.selector, $.proxy(_this3.toggle, _this3));
	            } else if (trigger !== Trigger.MANUAL) {
	              var eventIn = trigger === Trigger.HOVER ? _this3.constructor.Event.MOUSEENTER : _this3.constructor.Event.FOCUSIN;
	              var eventOut = trigger === Trigger.HOVER ? _this3.constructor.Event.MOUSELEAVE : _this3.constructor.Event.FOCUSOUT;

	              $(_this3.element).on(eventIn, _this3.config.selector, $.proxy(_this3._enter, _this3)).on(eventOut, _this3.config.selector, $.proxy(_this3._leave, _this3));
	            }
	          });

	          if (this.config.selector) {
	            this.config = $.extend({}, this.config, {
	              trigger: 'manual',
	              selector: ''
	            });
	          } else {
	            this._fixTitle();
	          }
	        }
	      }, {
	        key: '_fixTitle',
	        value: function _fixTitle() {
	          var titleType = typeof this.element.getAttribute('data-original-title');
	          if (this.element.getAttribute('title') || titleType !== 'string') {
	            this.element.setAttribute('data-original-title', this.element.getAttribute('title') || '');
	            this.element.setAttribute('title', '');
	          }
	        }
	      }, {
	        key: '_enter',
	        value: function _enter(event, context) {
	          var dataKey = this.constructor.DATA_KEY;

	          context = context || $(event.currentTarget).data(dataKey);

	          if (!context) {
	            context = new this.constructor(event.currentTarget, this._getDelegateConfig());
	            $(event.currentTarget).data(dataKey, context);
	          }

	          if (event) {
	            context._activeTrigger[event.type === 'focusin' ? Trigger.FOCUS : Trigger.HOVER] = true;
	          }

	          if ($(context.getTipElement()).hasClass(ClassName.IN) || context._hoverState === HoverState.IN) {
	            context._hoverState = HoverState.IN;
	            return;
	          }

	          clearTimeout(context._timeout);

	          context._hoverState = HoverState.IN;

	          if (!context.config.delay || !context.config.delay.show) {
	            context.show();
	            return;
	          }

	          context._timeout = setTimeout(function () {
	            if (context._hoverState === HoverState.IN) {
	              context.show();
	            }
	          }, context.config.delay.show);
	        }
	      }, {
	        key: '_leave',
	        value: function _leave(event, context) {
	          var dataKey = this.constructor.DATA_KEY;

	          context = context || $(event.currentTarget).data(dataKey);

	          if (!context) {
	            context = new this.constructor(event.currentTarget, this._getDelegateConfig());
	            $(event.currentTarget).data(dataKey, context);
	          }

	          if (event) {
	            context._activeTrigger[event.type === 'focusout' ? Trigger.FOCUS : Trigger.HOVER] = false;
	          }

	          if (context._isWithActiveTrigger()) {
	            return;
	          }

	          clearTimeout(context._timeout);

	          context._hoverState = HoverState.OUT;

	          if (!context.config.delay || !context.config.delay.hide) {
	            context.hide();
	            return;
	          }

	          context._timeout = setTimeout(function () {
	            if (context._hoverState === HoverState.OUT) {
	              context.hide();
	            }
	          }, context.config.delay.hide);
	        }
	      }, {
	        key: '_isWithActiveTrigger',
	        value: function _isWithActiveTrigger() {
	          for (var trigger in this._activeTrigger) {
	            if (this._activeTrigger[trigger]) {
	              return true;
	            }
	          }

	          return false;
	        }
	      }, {
	        key: '_getConfig',
	        value: function _getConfig(config) {
	          config = $.extend({}, this.constructor.Default, $(this.element).data(), config);

	          if (config.delay && typeof config.delay === 'number') {
	            config.delay = {
	              show: config.delay,
	              hide: config.delay
	            };
	          }

	          _Util['default'].typeCheckConfig(NAME, config, this.constructor.DefaultType);

	          return config;
	        }
	      }, {
	        key: '_getDelegateConfig',
	        value: function _getDelegateConfig() {
	          var config = {};

	          if (this.config) {
	            for (var key in this.config) {
	              if (this.constructor.Default[key] !== this.config[key]) {
	                config[key] = this.config[key];
	              }
	            }
	          }

	          return config;
	        }

	        // static

	      }], [{
	        key: '_jQueryInterface',
	        value: function _jQueryInterface(config) {
	          return this.each(function () {
	            var data = $(this).data(DATA_KEY);
	            var _config = typeof config === 'object' ? config : null;

	            if (!data && /destroy|hide/.test(config)) {
	              return;
	            }

	            if (!data) {
	              data = new Tooltip(this, _config);
	              $(this).data(DATA_KEY, data);
	            }

	            if (typeof config === 'string') {
	              if (data[config] === undefined) {
	                throw new Error('No method named "' + config + '"');
	              }
	              data[config]();
	            }
	          });
	        }
	      }, {
	        key: 'VERSION',
	        get: function get() {
	          return VERSION;
	        }
	      }, {
	        key: 'Default',
	        get: function get() {
	          return Default;
	        }
	      }, {
	        key: 'NAME',
	        get: function get() {
	          return NAME;
	        }
	      }, {
	        key: 'DATA_KEY',
	        get: function get() {
	          return DATA_KEY;
	        }
	      }, {
	        key: 'Event',
	        get: function get() {
	          return Event;
	        }
	      }, {
	        key: 'EVENT_KEY',
	        get: function get() {
	          return EVENT_KEY;
	        }
	      }, {
	        key: 'DefaultType',
	        get: function get() {
	          return DefaultType;
	        }
	      }]);

	      return Tooltip;
	    })();

	    $.fn[NAME] = Tooltip._jQueryInterface;
	    $.fn[NAME].Constructor = Tooltip;
	    $.fn[NAME].noConflict = function () {
	      $.fn[NAME] = JQUERY_NO_CONFLICT;
	      return Tooltip._jQueryInterface;
	    };

	    return Tooltip;
	  })(jQuery);

	  module.exports = Tooltip;
	});

/***/ },
/* 18 */
/***/ function(module, exports, __webpack_require__) {

	var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;'use strict';

	(function (global, factory) {
	  if (true) {
	    !(__WEBPACK_AMD_DEFINE_ARRAY__ = [exports, module, __webpack_require__(17)], __WEBPACK_AMD_DEFINE_FACTORY__ = (factory), __WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ? (__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__), __WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
	  } else if (typeof exports !== 'undefined' && typeof module !== 'undefined') {
	    factory(exports, module, require('./tooltip'));
	  } else {
	    var mod = {
	      exports: {}
	    };
	    factory(mod.exports, mod, global.Tooltip);
	    global.popover = mod.exports;
	  }
	})(undefined, function (exports, module, _tooltip) {
	  'use strict';

	  var _createClass = (function () {
	    function defineProperties(target, props) {
	      for (var i = 0; i < props.length; i++) {
	        var descriptor = props[i];descriptor.enumerable = descriptor.enumerable || false;descriptor.configurable = true;if ('value' in descriptor) descriptor.writable = true;Object.defineProperty(target, descriptor.key, descriptor);
	      }
	    }return function (Constructor, protoProps, staticProps) {
	      if (protoProps) defineProperties(Constructor.prototype, protoProps);if (staticProps) defineProperties(Constructor, staticProps);return Constructor;
	    };
	  })();

	  var _get = function get(_x, _x2, _x3) {
	    var _again = true;_function: while (_again) {
	      var object = _x,
	          property = _x2,
	          receiver = _x3;_again = false;if (object === null) object = Function.prototype;var desc = Object.getOwnPropertyDescriptor(object, property);if (desc === undefined) {
	        var parent = Object.getPrototypeOf(object);if (parent === null) {
	          return undefined;
	        } else {
	          _x = parent;_x2 = property;_x3 = receiver;_again = true;desc = parent = undefined;continue _function;
	        }
	      } else if ('value' in desc) {
	        return desc.value;
	      } else {
	        var getter = desc.get;if (getter === undefined) {
	          return undefined;
	        }return getter.call(receiver);
	      }
	    }
	  };

	  function _interopRequireDefault(obj) {
	    return obj && obj.__esModule ? obj : { 'default': obj };
	  }

	  function _classCallCheck(instance, Constructor) {
	    if (!(instance instanceof Constructor)) {
	      throw new TypeError('Cannot call a class as a function');
	    }
	  }

	  function _inherits(subClass, superClass) {
	    if (typeof superClass !== 'function' && superClass !== null) {
	      throw new TypeError('Super expression must either be null or a function, not ' + typeof superClass);
	    }subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } });if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass;
	  }

	  var _Tooltip2 = _interopRequireDefault(_tooltip);

	  /**
	   * --------------------------------------------------------------------------
	   * Bootstrap (v4.0.0-alpha.2): popover.js
	   * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
	   * --------------------------------------------------------------------------
	   */

	  var Popover = (function ($) {

	    /**
	     * ------------------------------------------------------------------------
	     * Constants
	     * ------------------------------------------------------------------------
	     */

	    var NAME = 'popover';
	    var VERSION = '4.0.0-alpha';
	    var DATA_KEY = 'bs.popover';
	    var EVENT_KEY = '.' + DATA_KEY;
	    var JQUERY_NO_CONFLICT = $.fn[NAME];

	    var Default = $.extend({}, _Tooltip2['default'].Default, {
	      placement: 'right',
	      trigger: 'click',
	      content: '',
	      template: '<div class="popover" role="tooltip">' + '<div class="popover-arrow"></div>' + '<h3 class="popover-title"></h3>' + '<div class="popover-content"></div></div>'
	    });

	    var DefaultType = $.extend({}, _Tooltip2['default'].DefaultType, {
	      content: '(string|element|function)'
	    });

	    var ClassName = {
	      FADE: 'fade',
	      IN: 'in'
	    };

	    var Selector = {
	      TITLE: '.popover-title',
	      CONTENT: '.popover-content',
	      ARROW: '.popover-arrow'
	    };

	    var Event = {
	      HIDE: 'hide' + EVENT_KEY,
	      HIDDEN: 'hidden' + EVENT_KEY,
	      SHOW: 'show' + EVENT_KEY,
	      SHOWN: 'shown' + EVENT_KEY,
	      INSERTED: 'inserted' + EVENT_KEY,
	      CLICK: 'click' + EVENT_KEY,
	      FOCUSIN: 'focusin' + EVENT_KEY,
	      FOCUSOUT: 'focusout' + EVENT_KEY,
	      MOUSEENTER: 'mouseenter' + EVENT_KEY,
	      MOUSELEAVE: 'mouseleave' + EVENT_KEY
	    };

	    /**
	     * ------------------------------------------------------------------------
	     * Class Definition
	     * ------------------------------------------------------------------------
	     */

	    var Popover = (function (_Tooltip) {
	      _inherits(Popover, _Tooltip);

	      function Popover() {
	        _classCallCheck(this, Popover);

	        _get(Object.getPrototypeOf(Popover.prototype), 'constructor', this).apply(this, arguments);
	      }

	      /**
	       * ------------------------------------------------------------------------
	       * jQuery
	       * ------------------------------------------------------------------------
	       */

	      _createClass(Popover, [{
	        key: 'isWithContent',

	        // overrides

	        value: function isWithContent() {
	          return this.getTitle() || this._getContent();
	        }
	      }, {
	        key: 'getTipElement',
	        value: function getTipElement() {
	          return this.tip = this.tip || $(this.config.template)[0];
	        }
	      }, {
	        key: 'setContent',
	        value: function setContent() {
	          var $tip = $(this.getTipElement());

	          // we use append for html objects to maintain js events
	          this.setElementContent($tip.find(Selector.TITLE), this.getTitle());
	          this.setElementContent($tip.find(Selector.CONTENT), this._getContent());

	          $tip.removeClass(ClassName.FADE).removeClass(ClassName.IN);

	          this.cleanupTether();
	        }

	        // private

	      }, {
	        key: '_getContent',
	        value: function _getContent() {
	          return this.element.getAttribute('data-content') || (typeof this.config.content === 'function' ? this.config.content.call(this.element) : this.config.content);
	        }

	        // static

	      }], [{
	        key: '_jQueryInterface',
	        value: function _jQueryInterface(config) {
	          return this.each(function () {
	            var data = $(this).data(DATA_KEY);
	            var _config = typeof config === 'object' ? config : null;

	            if (!data && /destroy|hide/.test(config)) {
	              return;
	            }

	            if (!data) {
	              data = new Popover(this, _config);
	              $(this).data(DATA_KEY, data);
	            }

	            if (typeof config === 'string') {
	              if (data[config] === undefined) {
	                throw new Error('No method named "' + config + '"');
	              }
	              data[config]();
	            }
	          });
	        }
	      }, {
	        key: 'VERSION',

	        // getters

	        get: function get() {
	          return VERSION;
	        }
	      }, {
	        key: 'Default',
	        get: function get() {
	          return Default;
	        }
	      }, {
	        key: 'NAME',
	        get: function get() {
	          return NAME;
	        }
	      }, {
	        key: 'DATA_KEY',
	        get: function get() {
	          return DATA_KEY;
	        }
	      }, {
	        key: 'Event',
	        get: function get() {
	          return Event;
	        }
	      }, {
	        key: 'EVENT_KEY',
	        get: function get() {
	          return EVENT_KEY;
	        }
	      }, {
	        key: 'DefaultType',
	        get: function get() {
	          return DefaultType;
	        }
	      }]);

	      return Popover;
	    })(_Tooltip2['default']);

	    $.fn[NAME] = Popover._jQueryInterface;
	    $.fn[NAME].Constructor = Popover;
	    $.fn[NAME].noConflict = function () {
	      $.fn[NAME] = JQUERY_NO_CONFLICT;
	      return Popover._jQueryInterface;
	    };

	    return Popover;
	  })(jQuery);

	  module.exports = Popover;
	});

/***/ },
/* 19 */
/***/ function(module, exports) {

	// removed by extract-text-webpack-plugin

/***/ },
/* 20 */,
/* 21 */,
/* 22 */,
/* 23 */,
/* 24 */,
/* 25 */,
/* 26 */,
/* 27 */
/***/ function(module, exports, __webpack_require__) {

	'use strict';

	function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { 'default': obj }; }

	var _rivets = __webpack_require__(28);

	var _rivets2 = _interopRequireDefault(_rivets);

	var _jquery = __webpack_require__(4);

	var _jquery2 = _interopRequireDefault(_jquery);

	var _prestashop = __webpack_require__(31);

	var _prestashop2 = _interopRequireDefault(_prestashop);

	_rivets2['default'].configure({
	  prefix: 'ps',
	  templateDelimiters: ['[[', ']]']
	});

	_rivets2['default'].formatters.propertyList = function (obj) {
	  var key, _results;
	  _results = [];
	  for (key in obj) {
	    _results.push(obj[key]);
	  }
	  return _results;
	};

	_rivets2['default'].formatters.customerAddress = function (obj) {
	  var address = _prestashop2['default'].customer.addresses[obj];
	  if (address) {
	    return address.formatted;
	  } else {
	    return undefined;
	  }
	};

	(0, _jquery2['default'])(document).ready(function () {
	  window.view = _rivets2['default'].bind((0, _jquery2['default'])('body'), {

	    prestashop: _prestashop2['default']

	  });
	});

/***/ },
/* 28 */
/***/ function(module, exports, __webpack_require__) {

	var __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/* WEBPACK VAR INJECTION */(function(module) {// Rivets.js
	// version: 0.8.1
	// author: Michael Richards
	// license: MIT
	'use strict';

	(function () {
	  var Rivets,
	      bindMethod,
	      unbindMethod,
	      _ref,
	      __bind = function __bind(fn, me) {
	    return function () {
	      return fn.apply(me, arguments);
	    };
	  },
	      __slice = [].slice,
	      __hasProp = ({}).hasOwnProperty,
	      __extends = function __extends(child, parent) {
	    for (var key in parent) {
	      if (__hasProp.call(parent, key)) child[key] = parent[key];
	    }function ctor() {
	      this.constructor = child;
	    }ctor.prototype = parent.prototype;child.prototype = new ctor();child.__super__ = parent.prototype;return child;
	  },
	      __indexOf = [].indexOf || function (item) {
	    for (var i = 0, l = this.length; i < l; i++) {
	      if (i in this && this[i] === item) return i;
	    }return -1;
	  };

	  Rivets = {
	    options: ['prefix', 'templateDelimiters', 'rootInterface', 'preloadData', 'handler'],
	    extensions: ['binders', 'formatters', 'components', 'adapters'],
	    "public": {
	      binders: {},
	      components: {},
	      formatters: {},
	      adapters: {},
	      prefix: 'rv',
	      templateDelimiters: ['{', '}'],
	      rootInterface: '.',
	      preloadData: true,
	      handler: function handler(context, ev, binding) {
	        return this.call(context, ev, binding.view.models);
	      },
	      configure: function configure(options) {
	        var descriptor, key, option, value;
	        if (options == null) {
	          options = {};
	        }
	        for (option in options) {
	          value = options[option];
	          if (option === 'binders' || option === 'components' || option === 'formatters' || option === 'adapters') {
	            for (key in value) {
	              descriptor = value[key];
	              Rivets[option][key] = descriptor;
	            }
	          } else {
	            Rivets["public"][option] = value;
	          }
	        }
	      },
	      bind: function bind(el, models, options) {
	        var view;
	        if (models == null) {
	          models = {};
	        }
	        if (options == null) {
	          options = {};
	        }
	        view = new Rivets.View(el, models, options);
	        view.bind();
	        return view;
	      },
	      init: function init(component, el, data) {
	        var scope, view;
	        if (data == null) {
	          data = {};
	        }
	        if (el == null) {
	          el = document.createElement('div');
	        }
	        component = Rivets["public"].components[component];
	        el.innerHTML = component.template.call(this, el);
	        scope = component.initialize.call(this, el, data);
	        view = new Rivets.View(el, scope);
	        view.bind();
	        return view;
	      }
	    }
	  };

	  if (window['jQuery'] || window['$']) {
	    _ref = 'on' in jQuery.prototype ? ['on', 'off'] : ['bind', 'unbind'], bindMethod = _ref[0], unbindMethod = _ref[1];
	    Rivets.Util = {
	      bindEvent: function bindEvent(el, event, handler) {
	        return jQuery(el)[bindMethod](event, handler);
	      },
	      unbindEvent: function unbindEvent(el, event, handler) {
	        return jQuery(el)[unbindMethod](event, handler);
	      },
	      getInputValue: function getInputValue(el) {
	        var $el;
	        $el = jQuery(el);
	        if ($el.attr('type') === 'checkbox') {
	          return $el.is(':checked');
	        } else {
	          return $el.val();
	        }
	      }
	    };
	  } else {
	    Rivets.Util = {
	      bindEvent: (function () {
	        if ('addEventListener' in window) {
	          return function (el, event, handler) {
	            return el.addEventListener(event, handler, false);
	          };
	        }
	        return function (el, event, handler) {
	          return el.attachEvent('on' + event, handler);
	        };
	      })(),
	      unbindEvent: (function () {
	        if ('removeEventListener' in window) {
	          return function (el, event, handler) {
	            return el.removeEventListener(event, handler, false);
	          };
	        }
	        return function (el, event, handler) {
	          return el.detachEvent('on' + event, handler);
	        };
	      })(),
	      getInputValue: function getInputValue(el) {
	        var o, _i, _len, _results;
	        if (el.type === 'checkbox') {
	          return el.checked;
	        } else if (el.type === 'select-multiple') {
	          _results = [];
	          for (_i = 0, _len = el.length; _i < _len; _i++) {
	            o = el[_i];
	            if (o.selected) {
	              _results.push(o.value);
	            }
	          }
	          return _results;
	        } else {
	          return el.value;
	        }
	      }
	    };
	  }

	  Rivets.TypeParser = (function () {
	    function TypeParser() {}

	    TypeParser.types = {
	      primitive: 0,
	      keypath: 1
	    };

	    TypeParser.parse = function (string) {
	      if (/^'.*'$|^".*"$/.test(string)) {
	        return {
	          type: this.types.primitive,
	          value: string.slice(1, -1)
	        };
	      } else if (string === 'true') {
	        return {
	          type: this.types.primitive,
	          value: true
	        };
	      } else if (string === 'false') {
	        return {
	          type: this.types.primitive,
	          value: false
	        };
	      } else if (string === 'null') {
	        return {
	          type: this.types.primitive,
	          value: null
	        };
	      } else if (string === 'undefined') {
	        return {
	          type: this.types.primitive,
	          value: void 0
	        };
	      } else if (isNaN(Number(string)) === false) {
	        return {
	          type: this.types.primitive,
	          value: Number(string)
	        };
	      } else {
	        return {
	          type: this.types.keypath,
	          value: string
	        };
	      }
	    };

	    return TypeParser;
	  })();

	  Rivets.TextTemplateParser = (function () {
	    function TextTemplateParser() {}

	    TextTemplateParser.types = {
	      text: 0,
	      binding: 1
	    };

	    TextTemplateParser.parse = function (template, delimiters) {
	      var index, lastIndex, lastToken, length, substring, tokens, value;
	      tokens = [];
	      length = template.length;
	      index = 0;
	      lastIndex = 0;
	      while (lastIndex < length) {
	        index = template.indexOf(delimiters[0], lastIndex);
	        if (index < 0) {
	          tokens.push({
	            type: this.types.text,
	            value: template.slice(lastIndex)
	          });
	          break;
	        } else {
	          if (index > 0 && lastIndex < index) {
	            tokens.push({
	              type: this.types.text,
	              value: template.slice(lastIndex, index)
	            });
	          }
	          lastIndex = index + delimiters[0].length;
	          index = template.indexOf(delimiters[1], lastIndex);
	          if (index < 0) {
	            substring = template.slice(lastIndex - delimiters[1].length);
	            lastToken = tokens[tokens.length - 1];
	            if ((lastToken != null ? lastToken.type : void 0) === this.types.text) {
	              lastToken.value += substring;
	            } else {
	              tokens.push({
	                type: this.types.text,
	                value: substring
	              });
	            }
	            break;
	          }
	          value = template.slice(lastIndex, index).trim();
	          tokens.push({
	            type: this.types.binding,
	            value: value
	          });
	          lastIndex = index + delimiters[1].length;
	        }
	      }
	      return tokens;
	    };

	    return TextTemplateParser;
	  })();

	  Rivets.View = (function () {
	    function View(els, models, options) {
	      var k, option, v, _base, _i, _j, _len, _len1, _ref1, _ref2, _ref3, _ref4, _ref5;
	      this.els = els;
	      this.models = models;
	      if (options == null) {
	        options = {};
	      }
	      this.update = __bind(this.update, this);
	      this.publish = __bind(this.publish, this);
	      this.sync = __bind(this.sync, this);
	      this.unbind = __bind(this.unbind, this);
	      this.bind = __bind(this.bind, this);
	      this.select = __bind(this.select, this);
	      this.traverse = __bind(this.traverse, this);
	      this.build = __bind(this.build, this);
	      this.buildBinding = __bind(this.buildBinding, this);
	      this.bindingRegExp = __bind(this.bindingRegExp, this);
	      this.options = __bind(this.options, this);
	      if (!(this.els.jquery || this.els instanceof Array)) {
	        this.els = [this.els];
	      }
	      _ref1 = Rivets.extensions;
	      for (_i = 0, _len = _ref1.length; _i < _len; _i++) {
	        option = _ref1[_i];
	        this[option] = {};
	        if (options[option]) {
	          _ref2 = options[option];
	          for (k in _ref2) {
	            v = _ref2[k];
	            this[option][k] = v;
	          }
	        }
	        _ref3 = Rivets["public"][option];
	        for (k in _ref3) {
	          v = _ref3[k];
	          if ((_base = this[option])[k] == null) {
	            _base[k] = v;
	          }
	        }
	      }
	      _ref4 = Rivets.options;
	      for (_j = 0, _len1 = _ref4.length; _j < _len1; _j++) {
	        option = _ref4[_j];
	        this[option] = (_ref5 = options[option]) != null ? _ref5 : Rivets["public"][option];
	      }
	      this.build();
	    }

	    View.prototype.options = function () {
	      var option, options, _i, _len, _ref1;
	      options = {};
	      _ref1 = Rivets.extensions.concat(Rivets.options);
	      for (_i = 0, _len = _ref1.length; _i < _len; _i++) {
	        option = _ref1[_i];
	        options[option] = this[option];
	      }
	      return options;
	    };

	    View.prototype.bindingRegExp = function () {
	      return new RegExp("^" + this.prefix + "-");
	    };

	    View.prototype.buildBinding = function (binding, node, type, declaration) {
	      var context, ctx, dependencies, keypath, options, pipe, pipes;
	      options = {};
	      pipes = (function () {
	        var _i, _len, _ref1, _results;
	        _ref1 = declaration.split('|');
	        _results = [];
	        for (_i = 0, _len = _ref1.length; _i < _len; _i++) {
	          pipe = _ref1[_i];
	          _results.push(pipe.trim());
	        }
	        return _results;
	      })();
	      context = (function () {
	        var _i, _len, _ref1, _results;
	        _ref1 = pipes.shift().split('<');
	        _results = [];
	        for (_i = 0, _len = _ref1.length; _i < _len; _i++) {
	          ctx = _ref1[_i];
	          _results.push(ctx.trim());
	        }
	        return _results;
	      })();
	      keypath = context.shift();
	      options.formatters = pipes;
	      if (dependencies = context.shift()) {
	        options.dependencies = dependencies.split(/\s+/);
	      }
	      return this.bindings.push(new Rivets[binding](this, node, type, keypath, options));
	    };

	    View.prototype.build = function () {
	      var el, parse, _i, _len, _ref1;
	      this.bindings = [];
	      parse = (function (_this) {
	        return function (node) {
	          var block, childNode, delimiters, n, parser, text, token, tokens, _i, _j, _len, _len1, _ref1, _results;
	          if (node.nodeType === 3) {
	            parser = Rivets.TextTemplateParser;
	            if (delimiters = _this.templateDelimiters) {
	              if ((tokens = parser.parse(node.data, delimiters)).length) {
	                if (!(tokens.length === 1 && tokens[0].type === parser.types.text)) {
	                  for (_i = 0, _len = tokens.length; _i < _len; _i++) {
	                    token = tokens[_i];
	                    text = document.createTextNode(token.value);
	                    node.parentNode.insertBefore(text, node);
	                    if (token.type === 1) {
	                      _this.buildBinding('TextBinding', text, null, token.value);
	                    }
	                  }
	                  node.parentNode.removeChild(node);
	                }
	              }
	            }
	          } else if (node.nodeType === 1) {
	            block = _this.traverse(node);
	          }
	          if (!block) {
	            _ref1 = (function () {
	              var _k, _len1, _ref1, _results1;
	              _ref1 = node.childNodes;
	              _results1 = [];
	              for (_k = 0, _len1 = _ref1.length; _k < _len1; _k++) {
	                n = _ref1[_k];
	                _results1.push(n);
	              }
	              return _results1;
	            })();
	            _results = [];
	            for (_j = 0, _len1 = _ref1.length; _j < _len1; _j++) {
	              childNode = _ref1[_j];
	              _results.push(parse(childNode));
	            }
	            return _results;
	          }
	        };
	      })(this);
	      _ref1 = this.els;
	      for (_i = 0, _len = _ref1.length; _i < _len; _i++) {
	        el = _ref1[_i];
	        parse(el);
	      }
	      this.bindings.sort(function (a, b) {
	        var _ref2, _ref3;
	        return (((_ref2 = b.binder) != null ? _ref2.priority : void 0) || 0) - (((_ref3 = a.binder) != null ? _ref3.priority : void 0) || 0);
	      });
	    };

	    View.prototype.traverse = function (node) {
	      var attribute, attributes, binder, bindingRegExp, block, identifier, regexp, type, value, _i, _j, _len, _len1, _ref1, _ref2, _ref3;
	      bindingRegExp = this.bindingRegExp();
	      block = node.nodeName === 'SCRIPT' || node.nodeName === 'STYLE';
	      _ref1 = node.attributes;
	      for (_i = 0, _len = _ref1.length; _i < _len; _i++) {
	        attribute = _ref1[_i];
	        if (bindingRegExp.test(attribute.name)) {
	          type = attribute.name.replace(bindingRegExp, '');
	          if (!(binder = this.binders[type])) {
	            _ref2 = this.binders;
	            for (identifier in _ref2) {
	              value = _ref2[identifier];
	              if (identifier !== '*' && identifier.indexOf('*') !== -1) {
	                regexp = new RegExp("^" + identifier.replace(/\*/g, '.+') + "$");
	                if (regexp.test(type)) {
	                  binder = value;
	                }
	              }
	            }
	          }
	          binder || (binder = this.binders['*']);
	          if (binder.block) {
	            block = true;
	            attributes = [attribute];
	          }
	        }
	      }
	      _ref3 = attributes || node.attributes;
	      for (_j = 0, _len1 = _ref3.length; _j < _len1; _j++) {
	        attribute = _ref3[_j];
	        if (bindingRegExp.test(attribute.name)) {
	          type = attribute.name.replace(bindingRegExp, '');
	          this.buildBinding('Binding', node, type, attribute.value);
	        }
	      }
	      if (!block) {
	        type = node.nodeName.toLowerCase();
	        if (this.components[type] && !node._bound) {
	          this.bindings.push(new Rivets.ComponentBinding(this, node, type));
	          block = true;
	        }
	      }
	      return block;
	    };

	    View.prototype.select = function (fn) {
	      var binding, _i, _len, _ref1, _results;
	      _ref1 = this.bindings;
	      _results = [];
	      for (_i = 0, _len = _ref1.length; _i < _len; _i++) {
	        binding = _ref1[_i];
	        if (fn(binding)) {
	          _results.push(binding);
	        }
	      }
	      return _results;
	    };

	    View.prototype.bind = function () {
	      var binding, _i, _len, _ref1, _results;
	      _ref1 = this.bindings;
	      _results = [];
	      for (_i = 0, _len = _ref1.length; _i < _len; _i++) {
	        binding = _ref1[_i];
	        _results.push(binding.bind());
	      }
	      return _results;
	    };

	    View.prototype.unbind = function () {
	      var binding, _i, _len, _ref1, _results;
	      _ref1 = this.bindings;
	      _results = [];
	      for (_i = 0, _len = _ref1.length; _i < _len; _i++) {
	        binding = _ref1[_i];
	        _results.push(binding.unbind());
	      }
	      return _results;
	    };

	    View.prototype.sync = function () {
	      var binding, _i, _len, _ref1, _results;
	      _ref1 = this.bindings;
	      _results = [];
	      for (_i = 0, _len = _ref1.length; _i < _len; _i++) {
	        binding = _ref1[_i];
	        _results.push(typeof binding.sync === "function" ? binding.sync() : void 0);
	      }
	      return _results;
	    };

	    View.prototype.publish = function () {
	      var binding, _i, _len, _ref1, _results;
	      _ref1 = this.select(function (b) {
	        var _ref1;
	        return (_ref1 = b.binder) != null ? _ref1.publishes : void 0;
	      });
	      _results = [];
	      for (_i = 0, _len = _ref1.length; _i < _len; _i++) {
	        binding = _ref1[_i];
	        _results.push(binding.publish());
	      }
	      return _results;
	    };

	    View.prototype.update = function (models) {
	      var binding, key, model, _i, _len, _ref1, _results;
	      if (models == null) {
	        models = {};
	      }
	      for (key in models) {
	        model = models[key];
	        this.models[key] = model;
	      }
	      _ref1 = this.bindings;
	      _results = [];
	      for (_i = 0, _len = _ref1.length; _i < _len; _i++) {
	        binding = _ref1[_i];
	        _results.push(typeof binding.update === "function" ? binding.update(models) : void 0);
	      }
	      return _results;
	    };

	    return View;
	  })();

	  Rivets.Binding = (function () {
	    function Binding(view, el, type, keypath, options) {
	      this.view = view;
	      this.el = el;
	      this.type = type;
	      this.keypath = keypath;
	      this.options = options != null ? options : {};
	      this.getValue = __bind(this.getValue, this);
	      this.update = __bind(this.update, this);
	      this.unbind = __bind(this.unbind, this);
	      this.bind = __bind(this.bind, this);
	      this.publish = __bind(this.publish, this);
	      this.sync = __bind(this.sync, this);
	      this.set = __bind(this.set, this);
	      this.eventHandler = __bind(this.eventHandler, this);
	      this.formattedValue = __bind(this.formattedValue, this);
	      this.parseTarget = __bind(this.parseTarget, this);
	      this.observe = __bind(this.observe, this);
	      this.setBinder = __bind(this.setBinder, this);
	      this.formatters = this.options.formatters || [];
	      this.dependencies = [];
	      this.formatterObservers = {};
	      this.model = void 0;
	      this.setBinder();
	    }

	    Binding.prototype.setBinder = function () {
	      var identifier, regexp, value, _ref1;
	      if (!(this.binder = this.view.binders[this.type])) {
	        _ref1 = this.view.binders;
	        for (identifier in _ref1) {
	          value = _ref1[identifier];
	          if (identifier !== '*' && identifier.indexOf('*') !== -1) {
	            regexp = new RegExp("^" + identifier.replace(/\*/g, '.+') + "$");
	            if (regexp.test(this.type)) {
	              this.binder = value;
	              this.args = new RegExp("^" + identifier.replace(/\*/g, '(.+)') + "$").exec(this.type);
	              this.args.shift();
	            }
	          }
	        }
	      }
	      this.binder || (this.binder = this.view.binders['*']);
	      if (this.binder instanceof Function) {
	        return this.binder = {
	          routine: this.binder
	        };
	      }
	    };

	    Binding.prototype.observe = function (obj, keypath, callback) {
	      return Rivets.sightglass(obj, keypath, callback, {
	        root: this.view.rootInterface,
	        adapters: this.view.adapters
	      });
	    };

	    Binding.prototype.parseTarget = function () {
	      var token;
	      token = Rivets.TypeParser.parse(this.keypath);
	      if (token.type === 0) {
	        return this.value = token.value;
	      } else {
	        this.observer = this.observe(this.view.models, this.keypath, this.sync);
	        return this.model = this.observer.target;
	      }
	    };

	    Binding.prototype.formattedValue = function (value) {
	      var ai, arg, args, fi, formatter, id, observer, processedArgs, _base, _i, _j, _len, _len1, _ref1;
	      _ref1 = this.formatters;
	      for (fi = _i = 0, _len = _ref1.length; _i < _len; fi = ++_i) {
	        formatter = _ref1[fi];
	        args = formatter.match(/[^\s']+|'([^']|'[^\s])*'|"([^"]|"[^\s])*"/g);
	        id = args.shift();
	        formatter = this.view.formatters[id];
	        args = (function () {
	          var _j, _len1, _results;
	          _results = [];
	          for (_j = 0, _len1 = args.length; _j < _len1; _j++) {
	            arg = args[_j];
	            _results.push(Rivets.TypeParser.parse(arg));
	          }
	          return _results;
	        })();
	        processedArgs = [];
	        for (ai = _j = 0, _len1 = args.length; _j < _len1; ai = ++_j) {
	          arg = args[ai];
	          processedArgs.push(arg.type === 0 ? arg.value : ((_base = this.formatterObservers)[fi] || (_base[fi] = {}), !(observer = this.formatterObservers[fi][ai]) ? (observer = this.observe(this.view.models, arg.value, this.sync), this.formatterObservers[fi][ai] = observer) : void 0, observer.value()));
	        }
	        if ((formatter != null ? formatter.read : void 0) instanceof Function) {
	          value = formatter.read.apply(formatter, [value].concat(__slice.call(processedArgs)));
	        } else if (formatter instanceof Function) {
	          value = formatter.apply(null, [value].concat(__slice.call(processedArgs)));
	        }
	      }
	      return value;
	    };

	    Binding.prototype.eventHandler = function (fn) {
	      var binding, handler;
	      handler = (binding = this).view.handler;
	      return function (ev) {
	        return handler.call(fn, this, ev, binding);
	      };
	    };

	    Binding.prototype.set = function (value) {
	      var _ref1;
	      value = value instanceof Function && !this.binder["function"] ? this.formattedValue(value.call(this.model)) : this.formattedValue(value);
	      return (_ref1 = this.binder.routine) != null ? _ref1.call(this, this.el, value) : void 0;
	    };

	    Binding.prototype.sync = function () {
	      var dependency, observer;
	      return this.set((function () {
	        var _i, _j, _len, _len1, _ref1, _ref2, _ref3;
	        if (this.observer) {
	          if (this.model !== this.observer.target) {
	            _ref1 = this.dependencies;
	            for (_i = 0, _len = _ref1.length; _i < _len; _i++) {
	              observer = _ref1[_i];
	              observer.unobserve();
	            }
	            this.dependencies = [];
	            if ((this.model = this.observer.target) != null && ((_ref2 = this.options.dependencies) != null ? _ref2.length : void 0)) {
	              _ref3 = this.options.dependencies;
	              for (_j = 0, _len1 = _ref3.length; _j < _len1; _j++) {
	                dependency = _ref3[_j];
	                observer = this.observe(this.model, dependency, this.sync);
	                this.dependencies.push(observer);
	              }
	            }
	          }
	          return this.observer.value();
	        } else {
	          return this.value;
	        }
	      }).call(this));
	    };

	    Binding.prototype.publish = function () {
	      var args, formatter, id, value, _i, _len, _ref1, _ref2, _ref3;
	      if (this.observer) {
	        value = this.getValue(this.el);
	        _ref1 = this.formatters.slice(0).reverse();
	        for (_i = 0, _len = _ref1.length; _i < _len; _i++) {
	          formatter = _ref1[_i];
	          args = formatter.split(/\s+/);
	          id = args.shift();
	          if ((_ref2 = this.view.formatters[id]) != null ? _ref2.publish : void 0) {
	            value = (_ref3 = this.view.formatters[id]).publish.apply(_ref3, [value].concat(__slice.call(args)));
	          }
	        }
	        return this.observer.setValue(value);
	      }
	    };

	    Binding.prototype.bind = function () {
	      var dependency, observer, _i, _len, _ref1, _ref2, _ref3;
	      this.parseTarget();
	      if ((_ref1 = this.binder.bind) != null) {
	        _ref1.call(this, this.el);
	      }
	      if (this.model != null && ((_ref2 = this.options.dependencies) != null ? _ref2.length : void 0)) {
	        _ref3 = this.options.dependencies;
	        for (_i = 0, _len = _ref3.length; _i < _len; _i++) {
	          dependency = _ref3[_i];
	          observer = this.observe(this.model, dependency, this.sync);
	          this.dependencies.push(observer);
	        }
	      }
	      if (this.view.preloadData) {
	        return this.sync();
	      }
	    };

	    Binding.prototype.unbind = function () {
	      var ai, args, fi, observer, _i, _len, _ref1, _ref2, _ref3, _ref4;
	      if ((_ref1 = this.binder.unbind) != null) {
	        _ref1.call(this, this.el);
	      }
	      if ((_ref2 = this.observer) != null) {
	        _ref2.unobserve();
	      }
	      _ref3 = this.dependencies;
	      for (_i = 0, _len = _ref3.length; _i < _len; _i++) {
	        observer = _ref3[_i];
	        observer.unobserve();
	      }
	      this.dependencies = [];
	      _ref4 = this.formatterObservers;
	      for (fi in _ref4) {
	        args = _ref4[fi];
	        for (ai in args) {
	          observer = args[ai];
	          observer.unobserve();
	        }
	      }
	      return this.formatterObservers = {};
	    };

	    Binding.prototype.update = function (models) {
	      var _ref1, _ref2;
	      if (models == null) {
	        models = {};
	      }
	      this.model = (_ref1 = this.observer) != null ? _ref1.target : void 0;
	      return (_ref2 = this.binder.update) != null ? _ref2.call(this, models) : void 0;
	    };

	    Binding.prototype.getValue = function (el) {
	      if (this.binder && this.binder.getValue != null) {
	        return this.binder.getValue.call(this, el);
	      } else {
	        return Rivets.Util.getInputValue(el);
	      }
	    };

	    return Binding;
	  })();

	  Rivets.ComponentBinding = (function (_super) {
	    __extends(ComponentBinding, _super);

	    function ComponentBinding(view, el, type) {
	      var attribute, bindingRegExp, propertyName, _i, _len, _ref1, _ref2;
	      this.view = view;
	      this.el = el;
	      this.type = type;
	      this.unbind = __bind(this.unbind, this);
	      this.bind = __bind(this.bind, this);
	      this.locals = __bind(this.locals, this);
	      this.component = this.view.components[this.type];
	      this["static"] = {};
	      this.observers = {};
	      this.upstreamObservers = {};
	      bindingRegExp = view.bindingRegExp();
	      _ref1 = this.el.attributes || [];
	      for (_i = 0, _len = _ref1.length; _i < _len; _i++) {
	        attribute = _ref1[_i];
	        if (!bindingRegExp.test(attribute.name)) {
	          propertyName = this.camelCase(attribute.name);
	          if (__indexOf.call((_ref2 = this.component["static"]) != null ? _ref2 : [], propertyName) >= 0) {
	            this["static"][propertyName] = attribute.value;
	          } else {
	            this.observers[propertyName] = attribute.value;
	          }
	        }
	      }
	    }

	    ComponentBinding.prototype.sync = function () {};

	    ComponentBinding.prototype.update = function () {};

	    ComponentBinding.prototype.publish = function () {};

	    ComponentBinding.prototype.locals = function () {
	      var key, observer, result, value, _ref1, _ref2;
	      result = {};
	      _ref1 = this["static"];
	      for (key in _ref1) {
	        value = _ref1[key];
	        result[key] = value;
	      }
	      _ref2 = this.observers;
	      for (key in _ref2) {
	        observer = _ref2[key];
	        result[key] = observer.value();
	      }
	      return result;
	    };

	    ComponentBinding.prototype.camelCase = function (string) {
	      return string.replace(/-([a-z])/g, function (grouped) {
	        return grouped[1].toUpperCase();
	      });
	    };

	    ComponentBinding.prototype.bind = function () {
	      var k, key, keypath, observer, option, options, scope, v, _base, _i, _j, _len, _len1, _ref1, _ref2, _ref3, _ref4, _ref5, _ref6, _ref7, _results;
	      if (!this.bound) {
	        _ref1 = this.observers;
	        for (key in _ref1) {
	          keypath = _ref1[key];
	          this.observers[key] = this.observe(this.view.models, keypath, (function (_this) {
	            return function (key) {
	              return function () {
	                return _this.componentView.models[key] = _this.observers[key].value();
	              };
	            };
	          })(this).call(this, key));
	        }
	        this.bound = true;
	      }
	      if (this.componentView != null) {
	        return this.componentView.bind();
	      } else {
	        this.el.innerHTML = this.component.template.call(this);
	        scope = this.component.initialize.call(this, this.el, this.locals());
	        this.el._bound = true;
	        options = {};
	        _ref2 = Rivets.extensions;
	        for (_i = 0, _len = _ref2.length; _i < _len; _i++) {
	          option = _ref2[_i];
	          options[option] = {};
	          if (this.component[option]) {
	            _ref3 = this.component[option];
	            for (k in _ref3) {
	              v = _ref3[k];
	              options[option][k] = v;
	            }
	          }
	          _ref4 = this.view[option];
	          for (k in _ref4) {
	            v = _ref4[k];
	            if ((_base = options[option])[k] == null) {
	              _base[k] = v;
	            }
	          }
	        }
	        _ref5 = Rivets.options;
	        for (_j = 0, _len1 = _ref5.length; _j < _len1; _j++) {
	          option = _ref5[_j];
	          options[option] = (_ref6 = this.component[option]) != null ? _ref6 : this.view[option];
	        }
	        this.componentView = new Rivets.View(this.el, scope, options);
	        this.componentView.bind();
	        _ref7 = this.observers;
	        _results = [];
	        for (key in _ref7) {
	          observer = _ref7[key];
	          _results.push(this.upstreamObservers[key] = this.observe(this.componentView.models, key, (function (_this) {
	            return function (key, observer) {
	              return function () {
	                return observer.setValue(_this.componentView.models[key]);
	              };
	            };
	          })(this).call(this, key, observer)));
	        }
	        return _results;
	      }
	    };

	    ComponentBinding.prototype.unbind = function () {
	      var key, observer, _ref1, _ref2, _ref3;
	      _ref1 = this.upstreamObservers;
	      for (key in _ref1) {
	        observer = _ref1[key];
	        observer.unobserve();
	      }
	      _ref2 = this.observers;
	      for (key in _ref2) {
	        observer = _ref2[key];
	        observer.unobserve();
	      }
	      return (_ref3 = this.componentView) != null ? _ref3.unbind.call(this) : void 0;
	    };

	    return ComponentBinding;
	  })(Rivets.Binding);

	  Rivets.TextBinding = (function (_super) {
	    __extends(TextBinding, _super);

	    function TextBinding(view, el, type, keypath, options) {
	      this.view = view;
	      this.el = el;
	      this.type = type;
	      this.keypath = keypath;
	      this.options = options != null ? options : {};
	      this.sync = __bind(this.sync, this);
	      this.formatters = this.options.formatters || [];
	      this.dependencies = [];
	      this.formatterObservers = {};
	    }

	    TextBinding.prototype.binder = {
	      routine: function routine(node, value) {
	        return node.data = value != null ? value : '';
	      }
	    };

	    TextBinding.prototype.sync = function () {
	      return TextBinding.__super__.sync.apply(this, arguments);
	    };

	    return TextBinding;
	  })(Rivets.Binding);

	  Rivets["public"].binders.text = function (el, value) {
	    if (el.textContent != null) {
	      return el.textContent = value != null ? value : '';
	    } else {
	      return el.innerText = value != null ? value : '';
	    }
	  };

	  Rivets["public"].binders.html = function (el, value) {
	    return el.innerHTML = value != null ? value : '';
	  };

	  Rivets["public"].binders.show = function (el, value) {
	    return el.style.display = value ? '' : 'none';
	  };

	  Rivets["public"].binders.hide = function (el, value) {
	    return el.style.display = value ? 'none' : '';
	  };

	  Rivets["public"].binders.enabled = function (el, value) {
	    return el.disabled = !value;
	  };

	  Rivets["public"].binders.disabled = function (el, value) {
	    return el.disabled = !!value;
	  };

	  Rivets["public"].binders.checked = {
	    publishes: true,
	    priority: 2000,
	    bind: function bind(el) {
	      return Rivets.Util.bindEvent(el, 'change', this.publish);
	    },
	    unbind: function unbind(el) {
	      return Rivets.Util.unbindEvent(el, 'change', this.publish);
	    },
	    routine: function routine(el, value) {
	      var _ref1;
	      if (el.type === 'radio') {
	        return el.checked = ((_ref1 = el.value) != null ? _ref1.toString() : void 0) === (value != null ? value.toString() : void 0);
	      } else {
	        return el.checked = !!value;
	      }
	    }
	  };

	  Rivets["public"].binders.unchecked = {
	    publishes: true,
	    priority: 2000,
	    bind: function bind(el) {
	      return Rivets.Util.bindEvent(el, 'change', this.publish);
	    },
	    unbind: function unbind(el) {
	      return Rivets.Util.unbindEvent(el, 'change', this.publish);
	    },
	    routine: function routine(el, value) {
	      var _ref1;
	      if (el.type === 'radio') {
	        return el.checked = ((_ref1 = el.value) != null ? _ref1.toString() : void 0) !== (value != null ? value.toString() : void 0);
	      } else {
	        return el.checked = !value;
	      }
	    }
	  };

	  Rivets["public"].binders.value = {
	    publishes: true,
	    priority: 3000,
	    bind: function bind(el) {
	      if (!(el.tagName === 'INPUT' && el.type === 'radio')) {
	        this.event = el.tagName === 'SELECT' ? 'change' : 'input';
	        return Rivets.Util.bindEvent(el, this.event, this.publish);
	      }
	    },
	    unbind: function unbind(el) {
	      if (!(el.tagName === 'INPUT' && el.type === 'radio')) {
	        return Rivets.Util.unbindEvent(el, this.event, this.publish);
	      }
	    },
	    routine: function routine(el, value) {
	      var o, _i, _len, _ref1, _ref2, _ref3, _results;
	      if (el.tagName === 'INPUT' && el.type === 'radio') {
	        return el.setAttribute('value', value);
	      } else if (window.jQuery != null) {
	        el = jQuery(el);
	        if ((value != null ? value.toString() : void 0) !== ((_ref1 = el.val()) != null ? _ref1.toString() : void 0)) {
	          return el.val(value != null ? value : '');
	        }
	      } else {
	        if (el.type === 'select-multiple') {
	          if (value != null) {
	            _results = [];
	            for (_i = 0, _len = el.length; _i < _len; _i++) {
	              o = el[_i];
	              _results.push(o.selected = (_ref2 = o.value, __indexOf.call(value, _ref2) >= 0));
	            }
	            return _results;
	          }
	        } else if ((value != null ? value.toString() : void 0) !== ((_ref3 = el.value) != null ? _ref3.toString() : void 0)) {
	          return el.value = value != null ? value : '';
	        }
	      }
	    }
	  };

	  Rivets["public"].binders["if"] = {
	    block: true,
	    priority: 4000,
	    bind: function bind(el) {
	      var attr, declaration;
	      if (this.marker == null) {
	        attr = [this.view.prefix, this.type].join('-').replace('--', '-');
	        declaration = el.getAttribute(attr);
	        this.marker = document.createComment(" rivets: " + this.type + " " + declaration + " ");
	        this.bound = false;
	        el.removeAttribute(attr);
	        el.parentNode.insertBefore(this.marker, el);
	        return el.parentNode.removeChild(el);
	      }
	    },
	    unbind: function unbind() {
	      var _ref1;
	      return (_ref1 = this.nested) != null ? _ref1.unbind() : void 0;
	    },
	    routine: function routine(el, value) {
	      var key, model, models, _ref1;
	      if (!!value === !this.bound) {
	        if (value) {
	          models = {};
	          _ref1 = this.view.models;
	          for (key in _ref1) {
	            model = _ref1[key];
	            models[key] = model;
	          }
	          (this.nested || (this.nested = new Rivets.View(el, models, this.view.options()))).bind();
	          this.marker.parentNode.insertBefore(el, this.marker.nextSibling);
	          return this.bound = true;
	        } else {
	          el.parentNode.removeChild(el);
	          this.nested.unbind();
	          return this.bound = false;
	        }
	      }
	    },
	    update: function update(models) {
	      var _ref1;
	      return (_ref1 = this.nested) != null ? _ref1.update(models) : void 0;
	    }
	  };

	  Rivets["public"].binders.unless = {
	    block: true,
	    priority: 4000,
	    bind: function bind(el) {
	      return Rivets["public"].binders["if"].bind.call(this, el);
	    },
	    unbind: function unbind() {
	      return Rivets["public"].binders["if"].unbind.call(this);
	    },
	    routine: function routine(el, value) {
	      return Rivets["public"].binders["if"].routine.call(this, el, !value);
	    },
	    update: function update(models) {
	      return Rivets["public"].binders["if"].update.call(this, models);
	    }
	  };

	  Rivets["public"].binders['on-*'] = {
	    "function": true,
	    priority: 1000,
	    unbind: function unbind(el) {
	      if (this.handler) {
	        return Rivets.Util.unbindEvent(el, this.args[0], this.handler);
	      }
	    },
	    routine: function routine(el, value) {
	      if (this.handler) {
	        Rivets.Util.unbindEvent(el, this.args[0], this.handler);
	      }
	      return Rivets.Util.bindEvent(el, this.args[0], this.handler = this.eventHandler(value));
	    }
	  };

	  Rivets["public"].binders['each-*'] = {
	    block: true,
	    priority: 4000,
	    bind: function bind(el) {
	      var attr, view, _i, _len, _ref1;
	      if (this.marker == null) {
	        attr = [this.view.prefix, this.type].join('-').replace('--', '-');
	        this.marker = document.createComment(" rivets: " + this.type + " ");
	        this.iterated = [];
	        el.removeAttribute(attr);
	        el.parentNode.insertBefore(this.marker, el);
	        el.parentNode.removeChild(el);
	      } else {
	        _ref1 = this.iterated;
	        for (_i = 0, _len = _ref1.length; _i < _len; _i++) {
	          view = _ref1[_i];
	          view.bind();
	        }
	      }
	    },
	    unbind: function unbind(el) {
	      var view, _i, _len, _ref1, _results;
	      if (this.iterated != null) {
	        _ref1 = this.iterated;
	        _results = [];
	        for (_i = 0, _len = _ref1.length; _i < _len; _i++) {
	          view = _ref1[_i];
	          _results.push(view.unbind());
	        }
	        return _results;
	      }
	    },
	    routine: function routine(el, collection) {
	      var binding, data, i, index, key, model, modelName, options, previous, template, view, _i, _j, _k, _len, _len1, _len2, _ref1, _ref2, _ref3, _results;
	      modelName = this.args[0];
	      collection = collection || [];
	      if (this.iterated.length > collection.length) {
	        _ref1 = Array(this.iterated.length - collection.length);
	        for (_i = 0, _len = _ref1.length; _i < _len; _i++) {
	          i = _ref1[_i];
	          view = this.iterated.pop();
	          view.unbind();
	          this.marker.parentNode.removeChild(view.els[0]);
	        }
	      }
	      for (index = _j = 0, _len1 = collection.length; _j < _len1; index = ++_j) {
	        model = collection[index];
	        data = {
	          index: index
	        };
	        data[modelName] = model;
	        if (this.iterated[index] == null) {
	          _ref2 = this.view.models;
	          for (key in _ref2) {
	            model = _ref2[key];
	            if (data[key] == null) {
	              data[key] = model;
	            }
	          }
	          previous = this.iterated.length ? this.iterated[this.iterated.length - 1].els[0] : this.marker;
	          options = this.view.options();
	          options.preloadData = true;
	          template = el.cloneNode(true);
	          view = new Rivets.View(template, data, options);
	          view.bind();
	          this.iterated.push(view);
	          this.marker.parentNode.insertBefore(template, previous.nextSibling);
	        } else if (this.iterated[index].models[modelName] !== model) {
	          this.iterated[index].update(data);
	        }
	      }
	      if (el.nodeName === 'OPTION') {
	        _ref3 = this.view.bindings;
	        _results = [];
	        for (_k = 0, _len2 = _ref3.length; _k < _len2; _k++) {
	          binding = _ref3[_k];
	          if (binding.el === this.marker.parentNode && binding.type === 'value') {
	            _results.push(binding.sync());
	          } else {
	            _results.push(void 0);
	          }
	        }
	        return _results;
	      }
	    },
	    update: function update(models) {
	      var data, key, model, view, _i, _len, _ref1, _results;
	      data = {};
	      for (key in models) {
	        model = models[key];
	        if (key !== this.args[0]) {
	          data[key] = model;
	        }
	      }
	      _ref1 = this.iterated;
	      _results = [];
	      for (_i = 0, _len = _ref1.length; _i < _len; _i++) {
	        view = _ref1[_i];
	        _results.push(view.update(data));
	      }
	      return _results;
	    }
	  };

	  Rivets["public"].binders['class-*'] = function (el, value) {
	    var elClass;
	    elClass = " " + el.className + " ";
	    if (!value === (elClass.indexOf(" " + this.args[0] + " ") !== -1)) {
	      return el.className = value ? "" + el.className + " " + this.args[0] : elClass.replace(" " + this.args[0] + " ", ' ').trim();
	    }
	  };

	  Rivets["public"].binders['*'] = function (el, value) {
	    if (value != null) {
	      return el.setAttribute(this.type, value);
	    } else {
	      return el.removeAttribute(this.type);
	    }
	  };

	  Rivets["public"].adapters['.'] = {
	    id: '_rv',
	    counter: 0,
	    weakmap: {},
	    weakReference: function weakReference(obj) {
	      var id, _base, _name;
	      if (!obj.hasOwnProperty(this.id)) {
	        id = this.counter++;
	        Object.defineProperty(obj, this.id, {
	          value: id
	        });
	      }
	      return (_base = this.weakmap)[_name = obj[this.id]] || (_base[_name] = {
	        callbacks: {}
	      });
	    },
	    cleanupWeakReference: function cleanupWeakReference(ref, id) {
	      if (!Object.keys(ref.callbacks).length) {
	        if (!(ref.pointers && Object.keys(ref.pointers).length)) {
	          return delete this.weakmap[id];
	        }
	      }
	    },
	    stubFunction: function stubFunction(obj, fn) {
	      var map, original, weakmap;
	      original = obj[fn];
	      map = this.weakReference(obj);
	      weakmap = this.weakmap;
	      return obj[fn] = function () {
	        var callback, k, r, response, _i, _len, _ref1, _ref2, _ref3, _ref4;
	        response = original.apply(obj, arguments);
	        _ref1 = map.pointers;
	        for (r in _ref1) {
	          k = _ref1[r];
	          _ref4 = (_ref2 = (_ref3 = weakmap[r]) != null ? _ref3.callbacks[k] : void 0) != null ? _ref2 : [];
	          for (_i = 0, _len = _ref4.length; _i < _len; _i++) {
	            callback = _ref4[_i];
	            callback();
	          }
	        }
	        return response;
	      };
	    },
	    observeMutations: function observeMutations(obj, ref, keypath) {
	      var fn, functions, map, _base, _i, _len;
	      if (Array.isArray(obj)) {
	        map = this.weakReference(obj);
	        if (map.pointers == null) {
	          map.pointers = {};
	          functions = ['push', 'pop', 'shift', 'unshift', 'sort', 'reverse', 'splice'];
	          for (_i = 0, _len = functions.length; _i < _len; _i++) {
	            fn = functions[_i];
	            this.stubFunction(obj, fn);
	          }
	        }
	        if ((_base = map.pointers)[ref] == null) {
	          _base[ref] = [];
	        }
	        if (__indexOf.call(map.pointers[ref], keypath) < 0) {
	          return map.pointers[ref].push(keypath);
	        }
	      }
	    },
	    unobserveMutations: function unobserveMutations(obj, ref, keypath) {
	      var idx, map, pointers;
	      if (Array.isArray(obj) && obj[this.id] != null) {
	        if (map = this.weakmap[obj[this.id]]) {
	          if (pointers = map.pointers[ref]) {
	            if ((idx = pointers.indexOf(keypath)) >= 0) {
	              pointers.splice(idx, 1);
	            }
	            if (!pointers.length) {
	              delete map.pointers[ref];
	            }
	            return this.cleanupWeakReference(map, obj[this.id]);
	          }
	        }
	      }
	    },
	    observe: function observe(obj, keypath, callback) {
	      var callbacks, desc, value;
	      callbacks = this.weakReference(obj).callbacks;
	      if (callbacks[keypath] == null) {
	        callbacks[keypath] = [];
	        desc = Object.getOwnPropertyDescriptor(obj, keypath);
	        if (!((desc != null ? desc.get : void 0) || (desc != null ? desc.set : void 0))) {
	          value = obj[keypath];
	          Object.defineProperty(obj, keypath, {
	            enumerable: true,
	            get: function get() {
	              return value;
	            },
	            set: (function (_this) {
	              return function (newValue) {
	                var map, _i, _len, _ref1;
	                if (newValue !== value) {
	                  _this.unobserveMutations(value, obj[_this.id], keypath);
	                  value = newValue;
	                  if (map = _this.weakmap[obj[_this.id]]) {
	                    callbacks = map.callbacks;
	                    if (callbacks[keypath]) {
	                      _ref1 = callbacks[keypath].slice();
	                      for (_i = 0, _len = _ref1.length; _i < _len; _i++) {
	                        callback = _ref1[_i];
	                        if (__indexOf.call(callbacks[keypath], callback) >= 0) {
	                          callback();
	                        }
	                      }
	                    }
	                    return _this.observeMutations(newValue, obj[_this.id], keypath);
	                  }
	                }
	              };
	            })(this)
	          });
	        }
	      }
	      if (__indexOf.call(callbacks[keypath], callback) < 0) {
	        callbacks[keypath].push(callback);
	      }
	      return this.observeMutations(obj[keypath], obj[this.id], keypath);
	    },
	    unobserve: function unobserve(obj, keypath, callback) {
	      var callbacks, idx, map;
	      if (map = this.weakmap[obj[this.id]]) {
	        if (callbacks = map.callbacks[keypath]) {
	          if ((idx = callbacks.indexOf(callback)) >= 0) {
	            callbacks.splice(idx, 1);
	            if (!callbacks.length) {
	              delete map.callbacks[keypath];
	            }
	          }
	          this.unobserveMutations(obj[keypath], obj[this.id], keypath);
	          return this.cleanupWeakReference(map, obj[this.id]);
	        }
	      }
	    },
	    get: function get(obj, keypath) {
	      return obj[keypath];
	    },
	    set: function set(obj, keypath, value) {
	      return obj[keypath] = value;
	    }
	  };

	  Rivets.factory = function (sightglass) {
	    Rivets.sightglass = sightglass;
	    Rivets["public"]._ = Rivets;
	    return Rivets["public"];
	  };

	  if (typeof (typeof module !== "undefined" && module !== null ? module.exports : void 0) === 'object') {
	    module.exports = Rivets.factory(__webpack_require__(30));
	  } else if (true) {
	    !(__WEBPACK_AMD_DEFINE_ARRAY__ = [__webpack_require__(30)], __WEBPACK_AMD_DEFINE_RESULT__ = function (sightglass) {
	      return this.rivets = Rivets.factory(sightglass);
	    }.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__), __WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
	  } else {
	    this.rivets = Rivets.factory(sightglass);
	  }
	}).call(undefined);
	/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(29)(module)))

/***/ },
/* 29 */
/***/ function(module, exports) {

	"use strict";

	module.exports = function (module) {
		if (!module.webpackPolyfill) {
			module.deprecate = function () {};
			module.paths = [];
			// module.parent = undefined by default
			module.children = [];
			module.webpackPolyfill = 1;
		}
		return module;
	};

/***/ },
/* 30 */
/***/ function(module, exports, __webpack_require__) {

	var __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;'use strict';

	(function () {
	  // Public sightglass interface.
	  function sightglass(obj, keypath, callback, options) {
	    return new Observer(obj, keypath, callback, options);
	  }

	  // Batteries not included.
	  sightglass.adapters = {};

	  // Constructs a new keypath observer and kicks things off.
	  function Observer(obj, keypath, callback, options) {
	    this.options = options || {};
	    this.options.adapters = this.options.adapters || {};
	    this.obj = obj;
	    this.keypath = keypath;
	    this.callback = callback;
	    this.objectPath = [];
	    this.parse();

	    if (isObject(this.target = this.realize())) {
	      this.set(true, this.key, this.target, this.callback);
	    }
	  }

	  // Tokenizes the provided keypath string into interface + path tokens for the
	  // observer to work with.
	  Observer.tokenize = function (keypath, interfaces, root) {
	    var tokens = [];
	    var current = { i: root, path: '' };
	    var index, chr;

	    for (index = 0; index < keypath.length; index++) {
	      chr = keypath.charAt(index);

	      if (!! ~interfaces.indexOf(chr)) {
	        tokens.push(current);
	        current = { i: chr, path: '' };
	      } else {
	        current.path += chr;
	      }
	    }

	    tokens.push(current);
	    return tokens;
	  };

	  // Parses the keypath using the interfaces defined on the view. Sets variables
	  // for the tokenized keypath as well as the end key.
	  Observer.prototype.parse = function () {
	    var interfaces = this.interfaces();
	    var root, path;

	    if (!interfaces.length) {
	      error('Must define at least one adapter interface.');
	    }

	    if (!! ~interfaces.indexOf(this.keypath[0])) {
	      root = this.keypath[0];
	      path = this.keypath.substr(1);
	    } else {
	      if (typeof (root = this.options.root || sightglass.root) === 'undefined') {
	        error('Must define a default root adapter.');
	      }

	      path = this.keypath;
	    }

	    this.tokens = Observer.tokenize(path, interfaces, root);
	    this.key = this.tokens.pop();
	  };

	  // Realizes the full keypath, attaching observers for every key and correcting
	  // old observers to any changed objects in the keypath.
	  Observer.prototype.realize = function () {
	    var current = this.obj;
	    var unreached = false;
	    var prev;

	    this.tokens.forEach(function (token, index) {
	      if (isObject(current)) {
	        if (typeof this.objectPath[index] !== 'undefined') {
	          if (current !== (prev = this.objectPath[index])) {
	            this.set(false, token, prev, this.update.bind(this));
	            this.set(true, token, current, this.update.bind(this));
	            this.objectPath[index] = current;
	          }
	        } else {
	          this.set(true, token, current, this.update.bind(this));
	          this.objectPath[index] = current;
	        }

	        current = this.get(token, current);
	      } else {
	        if (unreached === false) {
	          unreached = index;
	        }

	        if (prev = this.objectPath[index]) {
	          this.set(false, token, prev, this.update.bind(this));
	        }
	      }
	    }, this);

	    if (unreached !== false) {
	      this.objectPath.splice(unreached);
	    }

	    return current;
	  };

	  // Updates the keypath. This is called when any intermediary key is changed.
	  Observer.prototype.update = function () {
	    var next, oldValue;

	    if ((next = this.realize()) !== this.target) {
	      if (isObject(this.target)) {
	        this.set(false, this.key, this.target, this.callback);
	      }

	      if (isObject(next)) {
	        this.set(true, this.key, next, this.callback);
	      }

	      oldValue = this.value();
	      this.target = next;

	      if (this.value() !== oldValue) this.callback();
	    }
	  };

	  // Reads the current end value of the observed keypath. Returns undefined if
	  // the full keypath is unreachable.
	  Observer.prototype.value = function () {
	    if (isObject(this.target)) {
	      return this.get(this.key, this.target);
	    }
	  };

	  // Sets the current end value of the observed keypath. Calling setValue when
	  // the full keypath is unreachable is a no-op.
	  Observer.prototype.setValue = function (value) {
	    if (isObject(this.target)) {
	      this.adapter(this.key).set(this.target, this.key.path, value);
	    }
	  };

	  // Gets the provided key on an object.
	  Observer.prototype.get = function (key, obj) {
	    return this.adapter(key).get(obj, key.path);
	  };

	  // Observes or unobserves a callback on the object using the provided key.
	  Observer.prototype.set = function (active, key, obj, callback) {
	    var action = active ? 'observe' : 'unobserve';
	    this.adapter(key)[action](obj, key.path, callback);
	  };

	  // Returns an array of all unique adapter interfaces available.
	  Observer.prototype.interfaces = function () {
	    var interfaces = Object.keys(this.options.adapters);

	    Object.keys(sightglass.adapters).forEach(function (i) {
	      if (! ~interfaces.indexOf(i)) {
	        interfaces.push(i);
	      }
	    });

	    return interfaces;
	  };

	  // Convenience function to grab the adapter for a specific key.
	  Observer.prototype.adapter = function (key) {
	    return this.options.adapters[key.i] || sightglass.adapters[key.i];
	  };

	  // Unobserves the entire keypath.
	  Observer.prototype.unobserve = function () {
	    var obj;

	    this.tokens.forEach(function (token, index) {
	      if (obj = this.objectPath[index]) {
	        this.set(false, token, obj, this.update.bind(this));
	      }
	    }, this);

	    if (isObject(this.target)) {
	      this.set(false, this.key, this.target, this.callback);
	    }
	  };

	  // Check if a value is an object than can be observed.
	  function isObject(obj) {
	    return typeof obj === 'object' && obj !== null;
	  }

	  // Error thrower.
	  function error(message) {
	    throw new Error('[sightglass] ' + message);
	  }

	  // Export module for Node and the browser.
	  if (typeof module !== 'undefined' && module.exports) {
	    module.exports = sightglass;
	  } else if (true) {
	    !(__WEBPACK_AMD_DEFINE_ARRAY__ = [], __WEBPACK_AMD_DEFINE_RESULT__ = function () {
	      return this.sightglass = sightglass;
	    }.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__), __WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
	  } else {
	    this.sightglass = sightglass;
	  }
	}).call(undefined);

/***/ },
/* 31 */
/***/ function(module, exports) {

	module.exports = prestashop;

/***/ },
/* 32 */
/***/ function(module, exports, __webpack_require__) {

	'use strict';

	function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { 'default': obj }; }

	var _jquery = __webpack_require__(4);

	var _jquery2 = _interopRequireDefault(_jquery);

	var _prestashop = __webpack_require__(31);

	var _prestashop2 = _interopRequireDefault(_prestashop);

	var _common = __webpack_require__(33);

	function collapsePaymentOptions() {
	  (0, _jquery2['default'])('.js-additional-information, .js-payment-option-form').hide();
	}

	function getSelectedPaymentOption() {
	  return (0, _jquery2['default'])('input[name="payment-option"]:checked').attr('id');
	}

	function enableOrDisableOrderButton() {
	  var show = true;
	  (0, _jquery2['default'])('#conditions-to-approve input[type="checkbox"]').each(function (_, checkbox) {
	    if (!checkbox.checked) {
	      show = false;
	    }
	  });

	  collapsePaymentOptions();

	  var option = getSelectedPaymentOption();
	  if (!option) {
	    show = false;
	  }

	  (0, _jquery2['default'])('#' + option + '-additional-information').show();
	  (0, _jquery2['default'])('#pay-with-' + option + '-form').show();
	  (0, _jquery2['default'])('#payment-confirmation button').attr('disabled', !show);
	}

	function confirmPayment() {
	  var option = getSelectedPaymentOption();
	  if (option) {
	    (0, _jquery2['default'])('#pay-with-' + option + '-form form').submit();
	  }
	}

	function refreshDeliveryOptions() {
	  var params = (0, _jquery2['default'])('#delivery-method').serialize() + '&action=selectDeliveryOption';
	  _jquery2['default'].post('', params).then(function (resp) {
	    (0, _jquery2['default'])('#delivery-options').replaceWith(resp);
	    (0, _common.psShowHide)();
	  });
	}

	function hideOrShow() {
	  var elm = this.getAttribute('data-action-target');
	  var show = this.checked;

	  if (show) {
	    (0, _jquery2['default'])('body #' + elm).show();
	  } else {
	    (0, _jquery2['default'])('body #' + elm).hide();
	  }
	}

	function selectAddress(event) {
	  var form = (0, _jquery2['default'])(event.target).closest('form');
	  _jquery2['default'].post('', form.serialize(), null, 'json').then(function (resp) {
	    // TODO
	  }).fail(function (resp) {
	    // TODO
	  });
	}

	function setupCheckoutScripts() {
	  if (!(0, _jquery2['default'])('body#order')) {
	    return;
	  }

	  (0, _jquery2['default'])('#payment-confirmation button').on('click', confirmPayment);
	  (0, _jquery2['default'])('#payment-section input[type="checkbox"][disabled]').attr('disabled', false);
	  (0, _jquery2['default'])('body').on('change', '#delivery-method input[type="radio"]', refreshDeliveryOptions);
	  (0, _jquery2['default'])('body').on('change', '#conditions-to-approve input[type="checkbox"]', enableOrDisableOrderButton);
	  (0, _jquery2['default'])('body').on('change', 'input[name="payment-option"]', enableOrDisableOrderButton);
	  (0, _jquery2['default'])('body').on('change', 'input[type="checkbox"][data-action="hideOrShow"]', hideOrShow);
	  (0, _jquery2['default'])('body').on('change', '.js-address-selector input', selectAddress);
	  (0, _jquery2['default'])('body').on('click', '.checkout-step.-reachable h1', function (event) {
	    (0, _jquery2['default'])('.-js-current, .-current').removeClass('-js-current -current');
	    (0, _jquery2['default'])(event.target).closest('.checkout-step').toggleClass('-js-current');
	  });

	  collapsePaymentOptions();

	  _prestashop2['default'].on('cart updated', function () {
	    _jquery2['default'].get('', {
	      action: 'getCartSummary'
	    }).then(function (resp) {
	      (0, _jquery2['default'])('#cart-summary').html(resp);
	    });
	  });
	}

	(0, _jquery2['default'])(document).ready(setupCheckoutScripts);

/***/ },
/* 33 */
/***/ function(module, exports) {

	'use strict';

	Object.defineProperty(exports, '__esModule', {
	  value: true
	});
	exports.psShowHide = psShowHide;

	function psShowHide() {
	  $('.ps-shown-by-js').show();
	  $('.ps-hidden-by-js').hide();
	}

/***/ },
/* 34 */
/***/ function(module, exports, __webpack_require__) {

	'use strict';

	function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { 'default': obj }; }

	var _jquery = __webpack_require__(4);

	var _jquery2 = _interopRequireDefault(_jquery);

	var pendingQuery = false;

	function updateDOM(_ref) {
	    var rendered_products = _ref.rendered_products;
	    var rendered_facets = _ref.rendered_facets;

	    (0, _jquery2['default'])('#products').replaceWith(rendered_products);
	    (0, _jquery2['default'])('#search_filters').replaceWith(rendered_facets);
	}

	var onpopstate = function onpopstate(e) {
	    if (e.state && e.state.rendered_products) {
	        updateDOM(e.state);
	    }
	};

	function updateResults(data) {
	    pendingQuery = false;
	    updateDOM(data);
	    window.history.pushState(data, undefined, data.current_url);
	    window.addEventListener('popstate', onpopstate);
	}

	function handleError() {
	    // TODO: feedback
	    pendingQuery = false;
	}

	function makeQuery(url) {
	    if (pendingQuery) {
	        // wait for current results
	    } else {

	            // We need to add a parameter to the URL
	            // to make it different from the one we're on,
	            // otherwise when you do "duplicate tab" under chrome
	            // it mixes up the cache between the AJAX request (that
	            // returns JSON) and the non-AJAX request (that returns
	            // HTML) and you just get a mess of JSON on the duplicated tab.

	            var slightlyDifferentURL = [url, url.indexOf('?') >= 0 ? '&' : '?', 'from-xhr'].join('');

	            _jquery2['default'].get(slightlyDifferentURL, null, null, 'json').then(updateResults).fail(handleError);
	        }
	}

	(0, _jquery2['default'])(document).ready(function () {
	    (0, _jquery2['default'])('body').on('change', '#search_filters input[data-search-url]', function () {
	        makeQuery(event.target.dataset.searchUrl);
	    });

	    (0, _jquery2['default'])('body').on('click', '.js-search-link', function () {
	        event.preventDefault();
	        makeQuery((0, _jquery2['default'])(event.target).closest('a').get(0).href);
	    });

	    (0, _jquery2['default'])('body').on('change', '#search_filters select', function () {
	        var form = (0, _jquery2['default'])(event.target).closest('form');
	        makeQuery('?' + form.serialize());
	    });
	});

/***/ },
/* 35 */
/***/ function(module, exports) {

	'use strict';

	Object.defineProperty(exports, '__esModule', {
	  value: true
	});

	var _createClass = (function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ('value' in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; })();

	function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError('Cannot call a class as a function'); } }

	var DropDown = (function () {
	  function DropDown(el) {
	    _classCallCheck(this, DropDown);

	    this.el = el;
	  }

	  _createClass(DropDown, [{
	    key: 'init',
	    value: function init(el) {
	      this.el.on('show.bs.dropdown', function (e) {
	        $(e.target).find('.dropdown-menu').first().stop(true, true).slideDown();
	      });

	      this.el.on('hide.bs.dropdown', function (e) {
	        $(e.target).find('.dropdown-menu').first().stop(true, true).slideUp();
	      });
	    }
	  }]);

	  return DropDown;
	})();

	exports['default'] = DropDown;
	module.exports = exports['default'];

/***/ },
/* 36 */
/***/ function(module, exports, __webpack_require__) {

	'use strict';

	Object.defineProperty(exports, '__esModule', {
	  value: true
	});

	var _createClass = (function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ('value' in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; })();

	var _get = function get(_x, _x2, _x3) { var _again = true; _function: while (_again) { var object = _x, property = _x2, receiver = _x3; _again = false; if (object === null) object = Function.prototype; var desc = Object.getOwnPropertyDescriptor(object, property); if (desc === undefined) { var parent = Object.getPrototypeOf(object); if (parent === null) { return undefined; } else { _x = parent; _x2 = property; _x3 = receiver; _again = true; desc = parent = undefined; continue _function; } } else if ('value' in desc) { return desc.value; } else { var getter = desc.get; if (getter === undefined) { return undefined; } return getter.call(receiver); } } };

	function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { 'default': obj }; }

	function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError('Cannot call a class as a function'); } }

	function _inherits(subClass, superClass) { if (typeof superClass !== 'function' && superClass !== null) { throw new TypeError('Super expression must either be null or a function, not ' + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

	var _dropDown = __webpack_require__(35);

	var _dropDown2 = _interopRequireDefault(_dropDown);

	var TopMenu = (function (_DropDown) {
	  _inherits(TopMenu, _DropDown);

	  function TopMenu() {
	    _classCallCheck(this, TopMenu);

	    _get(Object.getPrototypeOf(TopMenu.prototype), 'constructor', this).apply(this, arguments);
	  }

	  _createClass(TopMenu, [{
	    key: 'init',
	    value: function init() {
	      _get(Object.getPrototypeOf(TopMenu.prototype), 'init', this).call(this);
	    }
	  }]);

	  return TopMenu;
	})(_dropDown2['default']);

	exports['default'] = TopMenu;
	module.exports = exports['default'];

/***/ },
/* 37 */
/***/ function(module, exports) {

	'use strict';

	Object.defineProperty(exports, '__esModule', {
	  value: true
	});

	var _createClass = (function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ('value' in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; })();

	function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError('Cannot call a class as a function'); } }

	var ProductMinitature = (function () {
	  function ProductMinitature() {
	    _classCallCheck(this, ProductMinitature);
	  }

	  _createClass(ProductMinitature, [{
	    key: 'init',
	    value: function init() {
	      $('.js-product-miniature').each(function (index, element) {
	        if ($(element).find('.discount-percentage').length) {
	          $(element).find('.new').remove();
	        }
	        if ($(element).find('.color').length > 5) {
	          (function () {
	            var count = 0;
	            $(element).find('.color').each(function (index, element) {
	              if (index > 4) {
	                $(element).hide();
	                count++;
	              }
	            });
	            $(element).find('.js-count').append('+' + count);
	          })();
	        }
	      });
	    }
	  }]);

	  return ProductMinitature;
	})();

	exports['default'] = ProductMinitature;
	module.exports = exports['default'];

/***/ },
/* 38 */
/***/ function(module, exports) {

	// Copyright Joyent, Inc. and other Node contributors.
	//
	// Permission is hereby granted, free of charge, to any person obtaining a
	// copy of this software and associated documentation files (the
	// "Software"), to deal in the Software without restriction, including
	// without limitation the rights to use, copy, modify, merge, publish,
	// distribute, sublicense, and/or sell copies of the Software, and to permit
	// persons to whom the Software is furnished to do so, subject to the
	// following conditions:
	//
	// The above copyright notice and this permission notice shall be included
	// in all copies or substantial portions of the Software.
	//
	// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
	// OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
	// MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN
	// NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM,
	// DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
	// OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE
	// USE OR OTHER DEALINGS IN THE SOFTWARE.

	'use strict';

	function EventEmitter() {
	  this._events = this._events || {};
	  this._maxListeners = this._maxListeners || undefined;
	}
	module.exports = EventEmitter;

	// Backwards-compat with node 0.10.x
	EventEmitter.EventEmitter = EventEmitter;

	EventEmitter.prototype._events = undefined;
	EventEmitter.prototype._maxListeners = undefined;

	// By default EventEmitters will print a warning if more than 10 listeners are
	// added to it. This is a useful default which helps finding memory leaks.
	EventEmitter.defaultMaxListeners = 10;

	// Obviously not all Emitters should be limited to 10. This function allows
	// that to be increased. Set to zero for unlimited.
	EventEmitter.prototype.setMaxListeners = function (n) {
	  if (!isNumber(n) || n < 0 || isNaN(n)) throw TypeError('n must be a positive number');
	  this._maxListeners = n;
	  return this;
	};

	EventEmitter.prototype.emit = function (type) {
	  var er, handler, len, args, i, listeners;

	  if (!this._events) this._events = {};

	  // If there is no 'error' event listener then throw.
	  if (type === 'error') {
	    if (!this._events.error || isObject(this._events.error) && !this._events.error.length) {
	      er = arguments[1];
	      if (er instanceof Error) {
	        throw er; // Unhandled 'error' event
	      }
	      throw TypeError('Uncaught, unspecified "error" event.');
	    }
	  }

	  handler = this._events[type];

	  if (isUndefined(handler)) return false;

	  if (isFunction(handler)) {
	    switch (arguments.length) {
	      // fast cases
	      case 1:
	        handler.call(this);
	        break;
	      case 2:
	        handler.call(this, arguments[1]);
	        break;
	      case 3:
	        handler.call(this, arguments[1], arguments[2]);
	        break;
	      // slower
	      default:
	        args = Array.prototype.slice.call(arguments, 1);
	        handler.apply(this, args);
	    }
	  } else if (isObject(handler)) {
	    args = Array.prototype.slice.call(arguments, 1);
	    listeners = handler.slice();
	    len = listeners.length;
	    for (i = 0; i < len; i++) listeners[i].apply(this, args);
	  }

	  return true;
	};

	EventEmitter.prototype.addListener = function (type, listener) {
	  var m;

	  if (!isFunction(listener)) throw TypeError('listener must be a function');

	  if (!this._events) this._events = {};

	  // To avoid recursion in the case that type === "newListener"! Before
	  // adding it to the listeners, first emit "newListener".
	  if (this._events.newListener) this.emit('newListener', type, isFunction(listener.listener) ? listener.listener : listener);

	  if (!this._events[type])
	    // Optimize the case of one listener. Don't need the extra array object.
	    this._events[type] = listener;else if (isObject(this._events[type]))
	    // If we've already got an array, just append.
	    this._events[type].push(listener);else
	    // Adding the second element, need to change to array.
	    this._events[type] = [this._events[type], listener];

	  // Check for listener leak
	  if (isObject(this._events[type]) && !this._events[type].warned) {
	    if (!isUndefined(this._maxListeners)) {
	      m = this._maxListeners;
	    } else {
	      m = EventEmitter.defaultMaxListeners;
	    }

	    if (m && m > 0 && this._events[type].length > m) {
	      this._events[type].warned = true;
	      console.error('(node) warning: possible EventEmitter memory ' + 'leak detected. %d listeners added. ' + 'Use emitter.setMaxListeners() to increase limit.', this._events[type].length);
	      if (typeof console.trace === 'function') {
	        // not supported in IE 10
	        console.trace();
	      }
	    }
	  }

	  return this;
	};

	EventEmitter.prototype.on = EventEmitter.prototype.addListener;

	EventEmitter.prototype.once = function (type, listener) {
	  if (!isFunction(listener)) throw TypeError('listener must be a function');

	  var fired = false;

	  function g() {
	    this.removeListener(type, g);

	    if (!fired) {
	      fired = true;
	      listener.apply(this, arguments);
	    }
	  }

	  g.listener = listener;
	  this.on(type, g);

	  return this;
	};

	// emits a 'removeListener' event iff the listener was removed
	EventEmitter.prototype.removeListener = function (type, listener) {
	  var list, position, length, i;

	  if (!isFunction(listener)) throw TypeError('listener must be a function');

	  if (!this._events || !this._events[type]) return this;

	  list = this._events[type];
	  length = list.length;
	  position = -1;

	  if (list === listener || isFunction(list.listener) && list.listener === listener) {
	    delete this._events[type];
	    if (this._events.removeListener) this.emit('removeListener', type, listener);
	  } else if (isObject(list)) {
	    for (i = length; i-- > 0;) {
	      if (list[i] === listener || list[i].listener && list[i].listener === listener) {
	        position = i;
	        break;
	      }
	    }

	    if (position < 0) return this;

	    if (list.length === 1) {
	      list.length = 0;
	      delete this._events[type];
	    } else {
	      list.splice(position, 1);
	    }

	    if (this._events.removeListener) this.emit('removeListener', type, listener);
	  }

	  return this;
	};

	EventEmitter.prototype.removeAllListeners = function (type) {
	  var key, listeners;

	  if (!this._events) return this;

	  // not listening for removeListener, no need to emit
	  if (!this._events.removeListener) {
	    if (arguments.length === 0) this._events = {};else if (this._events[type]) delete this._events[type];
	    return this;
	  }

	  // emit removeListener for all listeners on all events
	  if (arguments.length === 0) {
	    for (key in this._events) {
	      if (key === 'removeListener') continue;
	      this.removeAllListeners(key);
	    }
	    this.removeAllListeners('removeListener');
	    this._events = {};
	    return this;
	  }

	  listeners = this._events[type];

	  if (isFunction(listeners)) {
	    this.removeListener(type, listeners);
	  } else if (listeners) {
	    // LIFO order
	    while (listeners.length) this.removeListener(type, listeners[listeners.length - 1]);
	  }
	  delete this._events[type];

	  return this;
	};

	EventEmitter.prototype.listeners = function (type) {
	  var ret;
	  if (!this._events || !this._events[type]) ret = [];else if (isFunction(this._events[type])) ret = [this._events[type]];else ret = this._events[type].slice();
	  return ret;
	};

	EventEmitter.prototype.listenerCount = function (type) {
	  if (this._events) {
	    var evlistener = this._events[type];

	    if (isFunction(evlistener)) return 1;else if (evlistener) return evlistener.length;
	  }
	  return 0;
	};

	EventEmitter.listenerCount = function (emitter, type) {
	  return emitter.listenerCount(type);
	};

	function isFunction(arg) {
	  return typeof arg === 'function';
	}

	function isNumber(arg) {
	  return typeof arg === 'number';
	}

	function isObject(arg) {
	  return typeof arg === 'object' && arg !== null;
	}

	function isUndefined(arg) {
	  return arg === void 0;
	}

/***/ }
/******/ ]);
//# sourceMappingURL=theme.js.map