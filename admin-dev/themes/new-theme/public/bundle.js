/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};

/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {

/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId])
/******/ 			return installedModules[moduleId].exports;

/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			exports: {},
/******/ 			id: moduleId,
/******/ 			loaded: false
/******/ 		};

/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);

/******/ 		// Flag the module as loaded
/******/ 		module.loaded = true;

/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}


/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;

/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;

/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";

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

	'use strict';

	var _expose$ExposeJQueryJquery = __webpack_require__(2);

	var _expose$ExposeJQueryJquery2 = _interopRequireDefault(_expose$ExposeJQueryJquery);

	__webpack_require__(6);

	var _exposeTetherTether = __webpack_require__(7);

	var _exposeTetherTether2 = _interopRequireDefault(_exposeTetherTether);

	__webpack_require__(9);

	__webpack_require__(10);

	__webpack_require__(11);

	__webpack_require__(12);

	__webpack_require__(13);

	__webpack_require__(14);

	__webpack_require__(15);

	__webpack_require__(19);

	__webpack_require__(21);

	__webpack_require__(28);

	__webpack_require__(30);

	__webpack_require__(32);

	var _nav_bar = __webpack_require__(37);

	var _nav_bar2 = _interopRequireDefault(_nav_bar);

	function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

	new _nav_bar2.default();

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

	var __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/* WEBPACK VAR INJECTION */(function(module) {"use strict";var _typeof=typeof Symbol==="function"&&typeof Symbol.iterator==="symbol"?function(obj){return typeof obj;}:function(obj){return obj&&typeof Symbol==="function"&&obj.constructor===Symbol?"symbol":typeof obj;}; /*!
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
	 */(function(global,factory){if(( false?"undefined":_typeof(module))==="object"&&_typeof(module.exports)==="object"){ // For CommonJS and CommonJS-like environments where a proper `window`
	// is present, execute the factory and get jQuery.
	// For environments that do not have a `window` with a `document`
	// (such as Node.js), expose a factory as module.exports.
	// This accentuates the need for the creation of a real `window`.
	// e.g. var jQuery = require("jquery")(window);
	// See ticket #14549 for more info.
	module.exports=global.document?factory(global,true):function(w){if(!w.document){throw new Error("jQuery requires a window with a document");}return factory(w);};}else {factory(global);} // Pass this if window is not defined yet
	})(typeof window!=="undefined"?window:undefined,function(window,noGlobal){ // Support: Firefox 18+
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
	fcamelCase=function fcamelCase(all,letter){return letter.toUpperCase();};jQuery.fn=jQuery.prototype={ // The current version of jQuery being used
	jquery:version,constructor:jQuery, // Start with an empty selector
	selector:"", // The default length of a jQuery object is 0
	length:0,toArray:function toArray(){return _slice.call(this);}, // Get the Nth element in the matched element set OR
	// Get the whole matched element set as a clean array
	get:function get(num){return num!=null? // Return just the one element from the set
	num<0?this[num+this.length]:this[num]: // Return all the elements in a clean array
	_slice.call(this);}, // Take an array of elements and push it onto the stack
	// (returning the new matched element set)
	pushStack:function pushStack(elems){ // Build a new jQuery matched element set
	var ret=jQuery.merge(this.constructor(),elems); // Add the old object onto the stack (as a reference)
	ret.prevObject=this;ret.context=this.context; // Return the newly-formed element set
	return ret;}, // Execute a callback for every element in the matched set.
	each:function each(callback){return jQuery.each(this,callback);},map:function map(callback){return this.pushStack(jQuery.map(this,function(elem,i){return callback.call(elem,i,elem);}));},slice:function slice(){return this.pushStack(_slice.apply(this,arguments));},first:function first(){return this.eq(0);},last:function last(){return this.eq(-1);},eq:function eq(i){var len=this.length,j=+i+(i<0?len:0);return this.pushStack(j>=0&&j<len?[this[j]]:[]);},end:function end(){return this.prevObject||this.constructor();}, // For internal use only.
	// Behaves like an Array's method, not like a jQuery method.
	push:push,sort:arr.sort,splice:arr.splice};jQuery.extend=jQuery.fn.extend=function(){var options,name,src,copy,copyIsArray,clone,target=arguments[0]||{},i=1,length=arguments.length,deep=false; // Handle a deep copy situation
	if(typeof target==="boolean"){deep=target; // Skip the boolean and the target
	target=arguments[i]||{};i++;} // Handle case when target is a string or something (possible in deep copy)
	if((typeof target==="undefined"?"undefined":_typeof(target))!=="object"&&!jQuery.isFunction(target)){target={};} // Extend jQuery itself if only one argument is passed
	if(i===length){target=this;i--;}for(;i<length;i++){ // Only deal with non-null/undefined values
	if((options=arguments[i])!=null){ // Extend the base object
	for(name in options){src=target[name];copy=options[name]; // Prevent never-ending loop
	if(target===copy){continue;} // Recurse if we're merging plain objects or arrays
	if(deep&&copy&&(jQuery.isPlainObject(copy)||(copyIsArray=jQuery.isArray(copy)))){if(copyIsArray){copyIsArray=false;clone=src&&jQuery.isArray(src)?src:[];}else {clone=src&&jQuery.isPlainObject(src)?src:{};} // Never move original objects, clone them
	target[name]=jQuery.extend(deep,clone,copy); // Don't bring in undefined values
	}else if(copy!==undefined){target[name]=copy;}}}} // Return the modified object
	return target;};jQuery.extend({ // Unique for each copy of jQuery on the page
	expando:"jQuery"+(version+Math.random()).replace(/\D/g,""), // Assume jQuery is ready without the ready module
	isReady:true,error:function error(msg){throw new Error(msg);},noop:function noop(){},isFunction:function isFunction(obj){return jQuery.type(obj)==="function";},isArray:Array.isArray,isWindow:function isWindow(obj){return obj!=null&&obj===obj.window;},isNumeric:function isNumeric(obj){ // parseFloat NaNs numeric-cast false positives (null|true|false|"")
	// ...but misinterprets leading-number strings, particularly hex literals ("0x...")
	// subtraction forces infinities to NaN
	// adding 1 corrects loss of precision from parseFloat (#15100)
	var realStringObj=obj&&obj.toString();return !jQuery.isArray(obj)&&realStringObj-parseFloat(realStringObj)+1>=0;},isPlainObject:function isPlainObject(obj){ // Not plain objects:
	// - Any object or value whose internal [[Class]] property is not "[object Object]"
	// - DOM nodes
	// - window
	if(jQuery.type(obj)!=="object"||obj.nodeType||jQuery.isWindow(obj)){return false;}if(obj.constructor&&!hasOwn.call(obj.constructor.prototype,"isPrototypeOf")){return false;} // If the function hasn't returned already, we're confident that
	// |obj| is a plain object, created by {} or constructed with new Object
	return true;},isEmptyObject:function isEmptyObject(obj){var name;for(name in obj){return false;}return true;},type:function type(obj){if(obj==null){return obj+"";} // Support: Android<4.0, iOS<6 (functionish RegExp)
	return (typeof obj==="undefined"?"undefined":_typeof(obj))==="object"||typeof obj==="function"?class2type[toString.call(obj)]||"object":typeof obj==="undefined"?"undefined":_typeof(obj);}, // Evaluates a script in a global context
	globalEval:function globalEval(code){var script,indirect=eval;code=jQuery.trim(code);if(code){ // If the code includes a valid, prologue position
	// strict mode pragma, execute code by injecting a
	// script tag into the document.
	if(code.indexOf("use strict")===1){script=document.createElement("script");script.text=code;document.head.appendChild(script).parentNode.removeChild(script);}else { // Otherwise, avoid the DOM node creation, insertion
	// and removal by using an indirect global eval
	indirect(code);}}}, // Convert dashed to camelCase; used by the css and data modules
	// Support: IE9-11+
	// Microsoft forgot to hump their vendor prefix (#9572)
	camelCase:function camelCase(string){return string.replace(rmsPrefix,"ms-").replace(rdashAlpha,fcamelCase);},nodeName:function nodeName(elem,name){return elem.nodeName&&elem.nodeName.toLowerCase()===name.toLowerCase();},each:function each(obj,callback){var length,i=0;if(isArrayLike(obj)){length=obj.length;for(;i<length;i++){if(callback.call(obj[i],i,obj[i])===false){break;}}}else {for(i in obj){if(callback.call(obj[i],i,obj[i])===false){break;}}}return obj;}, // Support: Android<4.1
	trim:function trim(text){return text==null?"":(text+"").replace(rtrim,"");}, // results is for internal usage only
	makeArray:function makeArray(arr,results){var ret=results||[];if(arr!=null){if(isArrayLike(Object(arr))){jQuery.merge(ret,typeof arr==="string"?[arr]:arr);}else {push.call(ret,arr);}}return ret;},inArray:function inArray(elem,arr,i){return arr==null?-1:indexOf.call(arr,elem,i);},merge:function merge(first,second){var len=+second.length,j=0,i=first.length;for(;j<len;j++){first[i++]=second[j];}first.length=i;return first;},grep:function grep(elems,callback,invert){var callbackInverse,matches=[],i=0,length=elems.length,callbackExpect=!invert; // Go through the array, only saving the items
	// that pass the validator function
	for(;i<length;i++){callbackInverse=!callback(elems[i],i);if(callbackInverse!==callbackExpect){matches.push(elems[i]);}}return matches;}, // arg is for internal usage only
	map:function map(elems,callback,arg){var length,value,i=0,ret=[]; // Go through the array, translating each of the items to their new values
	if(isArrayLike(elems)){length=elems.length;for(;i<length;i++){value=callback(elems[i],i,arg);if(value!=null){ret.push(value);}} // Go through every key on the object,
	}else {for(i in elems){value=callback(elems[i],i,arg);if(value!=null){ret.push(value);}}} // Flatten any nested arrays
	return concat.apply([],ret);}, // A global GUID counter for objects
	guid:1, // Bind a function to a context, optionally partially applying any
	// arguments.
	proxy:function proxy(fn,context){var tmp,args,proxy;if(typeof context==="string"){tmp=fn[context];context=fn;fn=tmp;} // Quick check to determine if target is callable, in the spec
	// this throws a TypeError, but we will just return undefined.
	if(!jQuery.isFunction(fn)){return undefined;} // Simulated bind
	args=_slice.call(arguments,2);proxy=function proxy(){return fn.apply(context||this,args.concat(_slice.call(arguments)));}; // Set the guid of unique handler to the same of original handler, so it can be removed
	proxy.guid=fn.guid=fn.guid||jQuery.guid++;return proxy;},now:Date.now, // jQuery.support is not used in Core but other projects attach their
	// properties to it so it needs to exist.
	support:support}); // JSHint would error on this code due to the Symbol not being defined in ES5.
	// Defining this global in .jshintrc would create a danger of using the global
	// unguarded in another place, it seems safer to just disable JSHint for these
	// three lines.
	/* jshint ignore: start */if(typeof Symbol==="function"){jQuery.fn[Symbol.iterator]=arr[Symbol.iterator];} /* jshint ignore: end */ // Populate the class2type map
	jQuery.each("Boolean Number String Function Array Date RegExp Object Error Symbol".split(" "),function(i,name){class2type["[object "+name+"]"]=name.toLowerCase();});function isArrayLike(obj){ // Support: iOS 8.2 (not reproducible in simulator)
	// `in` check used to prevent JIT error (gh-2145)
	// hasOwn isn't used here due to false negatives
	// regarding Nodelist length in IE
	var length=!!obj&&"length" in obj&&obj.length,type=jQuery.type(obj);if(type==="function"||jQuery.isWindow(obj)){return false;}return type==="array"||length===0||typeof length==="number"&&length>0&&length-1 in obj;}var Sizzle= /*!
	 * Sizzle CSS Selector Engine v2.2.1
	 * http://sizzlejs.com/
	 *
	 * Copyright jQuery Foundation and other contributors
	 * Released under the MIT license
	 * http://jquery.org/license
	 *
	 * Date: 2015-10-17
	 */function(window){var i,support,Expr,getText,isXML,tokenize,compile,select,outermostContext,sortInput,hasDuplicate, // Local document vars
	setDocument,document,docElem,documentIsHTML,rbuggyQSA,rbuggyMatches,matches,contains, // Instance-specific data
	expando="sizzle"+1*new Date(),preferredDoc=window.document,dirruns=0,done=0,classCache=createCache(),tokenCache=createCache(),compilerCache=createCache(),sortOrder=function sortOrder(a,b){if(a===b){hasDuplicate=true;}return 0;}, // General-purpose constants
	MAX_NEGATIVE=1<<31, // Instance methods
	hasOwn={}.hasOwnProperty,arr=[],pop=arr.pop,push_native=arr.push,push=arr.push,slice=arr.slice, // Use a stripped-down indexOf as it's faster than native
	// http://jsperf.com/thor-indexof-vs-for/5
	indexOf=function indexOf(list,elem){var i=0,len=list.length;for(;i<len;i++){if(list[i]===elem){return i;}}return -1;},booleans="checked|selected|async|autofocus|autoplay|controls|defer|disabled|hidden|ismap|loop|multiple|open|readonly|required|scoped", // Regular expressions
	// http://www.w3.org/TR/css3-selectors/#whitespace
	whitespace="[\\x20\\t\\r\\n\\f]", // http://www.w3.org/TR/CSS21/syndata.html#value-def-identifier
	identifier="(?:\\\\.|[\\w-]|[^\\x00-\\xa0])+", // Attribute selectors: http://www.w3.org/TR/selectors/#attribute-selectors
	attributes="\\["+whitespace+"*("+identifier+")(?:"+whitespace+ // Operator (capture 2)
	"*([*^$|!~]?=)"+whitespace+ // "Attribute values must be CSS identifiers [capture 5] or strings [capture 3 or capture 4]"
	"*(?:'((?:\\\\.|[^\\\\'])*)'|\"((?:\\\\.|[^\\\\\"])*)\"|("+identifier+"))|)"+whitespace+"*\\]",pseudos=":("+identifier+")(?:\\(("+ // To reduce the number of selectors needing tokenize in the preFilter, prefer arguments:
	// 1. quoted (capture 3; capture 4 or capture 5)
	"('((?:\\\\.|[^\\\\'])*)'|\"((?:\\\\.|[^\\\\\"])*)\")|"+ // 2. simple (capture 6)
	"((?:\\\\.|[^\\\\()[\\]]|"+attributes+")*)|"+ // 3. anything else (capture 2)
	".*"+")\\)|)", // Leading and non-escaped trailing whitespace, capturing some non-whitespace characters preceding the latter
	rwhitespace=new RegExp(whitespace+"+","g"),rtrim=new RegExp("^"+whitespace+"+|((?:^|[^\\\\])(?:\\\\.)*)"+whitespace+"+$","g"),rcomma=new RegExp("^"+whitespace+"*,"+whitespace+"*"),rcombinators=new RegExp("^"+whitespace+"*([>+~]|"+whitespace+")"+whitespace+"*"),rattributeQuotes=new RegExp("="+whitespace+"*([^\\]'\"]*?)"+whitespace+"*\\]","g"),rpseudo=new RegExp(pseudos),ridentifier=new RegExp("^"+identifier+"$"),matchExpr={"ID":new RegExp("^#("+identifier+")"),"CLASS":new RegExp("^\\.("+identifier+")"),"TAG":new RegExp("^("+identifier+"|[*])"),"ATTR":new RegExp("^"+attributes),"PSEUDO":new RegExp("^"+pseudos),"CHILD":new RegExp("^:(only|first|last|nth|nth-last)-(child|of-type)(?:\\("+whitespace+"*(even|odd|(([+-]|)(\\d*)n|)"+whitespace+"*(?:([+-]|)"+whitespace+"*(\\d+)|))"+whitespace+"*\\)|)","i"),"bool":new RegExp("^(?:"+booleans+")$","i"), // For use in libraries implementing .is()
	// We use this for POS matching in `select`
	"needsContext":new RegExp("^"+whitespace+"*[>+~]|:(even|odd|eq|gt|lt|nth|first|last)(?:\\("+whitespace+"*((?:-\\d)?\\d*)"+whitespace+"*\\)|)(?=[^-]|$)","i")},rinputs=/^(?:input|select|textarea|button)$/i,rheader=/^h\d$/i,rnative=/^[^{]+\{\s*\[native \w/, // Easily-parseable/retrievable ID or TAG or CLASS selectors
	rquickExpr=/^(?:#([\w-]+)|(\w+)|\.([\w-]+))$/,rsibling=/[+~]/,rescape=/'|\\/g, // CSS escapes http://www.w3.org/TR/CSS21/syndata.html#escaped-characters
	runescape=new RegExp("\\\\([\\da-f]{1,6}"+whitespace+"?|("+whitespace+")|.)","ig"),funescape=function funescape(_,escaped,escapedWhitespace){var high="0x"+escaped-0x10000; // NaN means non-codepoint
	// Support: Firefox<24
	// Workaround erroneous numeric interpretation of +"0x"
	return high!==high||escapedWhitespace?escaped:high<0? // BMP codepoint
	String.fromCharCode(high+0x10000): // Supplemental Plane codepoint (surrogate pair)
	String.fromCharCode(high>>10|0xD800,high&0x3FF|0xDC00);}, // Used for iframes
	// See setDocument()
	// Removing the function wrapper causes a "Permission Denied"
	// error in IE
	unloadHandler=function unloadHandler(){setDocument();}; // Optimize for push.apply( _, NodeList )
	try{push.apply(arr=slice.call(preferredDoc.childNodes),preferredDoc.childNodes); // Support: Android<4.0
	// Detect silently failing push.apply
	arr[preferredDoc.childNodes.length].nodeType;}catch(e){push={apply:arr.length? // Leverage slice if possible
	function(target,els){push_native.apply(target,slice.call(els));}: // Support: IE<9
	// Otherwise append directly
	function(target,els){var j=target.length,i=0; // Can't trust NodeList.length
	while(target[j++]=els[i++]){}target.length=j-1;}};}function Sizzle(selector,context,results,seed){var m,i,elem,nid,nidselect,match,groups,newSelector,newContext=context&&context.ownerDocument, // nodeType defaults to 9, since context defaults to document
	nodeType=context?context.nodeType:9;results=results||[]; // Return early from calls with invalid selector or context
	if(typeof selector!=="string"||!selector||nodeType!==1&&nodeType!==9&&nodeType!==11){return results;} // Try to shortcut find operations (as opposed to filters) in HTML documents
	if(!seed){if((context?context.ownerDocument||context:preferredDoc)!==document){setDocument(context);}context=context||document;if(documentIsHTML){ // If the selector is sufficiently simple, try using a "get*By*" DOM method
	// (excepting DocumentFragment context, where the methods don't exist)
	if(nodeType!==11&&(match=rquickExpr.exec(selector))){ // ID selector
	if(m=match[1]){ // Document context
	if(nodeType===9){if(elem=context.getElementById(m)){ // Support: IE, Opera, Webkit
	// TODO: identify versions
	// getElementById can match elements by name instead of ID
	if(elem.id===m){results.push(elem);return results;}}else {return results;} // Element context
	}else { // Support: IE, Opera, Webkit
	// TODO: identify versions
	// getElementById can match elements by name instead of ID
	if(newContext&&(elem=newContext.getElementById(m))&&contains(context,elem)&&elem.id===m){results.push(elem);return results;}} // Type selector
	}else if(match[2]){push.apply(results,context.getElementsByTagName(selector));return results; // Class selector
	}else if((m=match[3])&&support.getElementsByClassName&&context.getElementsByClassName){push.apply(results,context.getElementsByClassName(m));return results;}} // Take advantage of querySelectorAll
	if(support.qsa&&!compilerCache[selector+" "]&&(!rbuggyQSA||!rbuggyQSA.test(selector))){if(nodeType!==1){newContext=context;newSelector=selector; // qSA looks outside Element context, which is not what we want
	// Thanks to Andrew Dupont for this workaround technique
	// Support: IE <=8
	// Exclude object elements
	}else if(context.nodeName.toLowerCase()!=="object"){ // Capture the context ID, setting it first if necessary
	if(nid=context.getAttribute("id")){nid=nid.replace(rescape,"\\$&");}else {context.setAttribute("id",nid=expando);} // Prefix every selector in the list
	groups=tokenize(selector);i=groups.length;nidselect=ridentifier.test(nid)?"#"+nid:"[id='"+nid+"']";while(i--){groups[i]=nidselect+" "+toSelector(groups[i]);}newSelector=groups.join(","); // Expand context for sibling selectors
	newContext=rsibling.test(selector)&&testContext(context.parentNode)||context;}if(newSelector){try{push.apply(results,newContext.querySelectorAll(newSelector));return results;}catch(qsaError){}finally {if(nid===expando){context.removeAttribute("id");}}}}}} // All others
	return select(selector.replace(rtrim,"$1"),context,results,seed);} /**
	 * Create key-value caches of limited size
	 * @returns {function(string, object)} Returns the Object data after storing it on itself with
	 *	property name the (space-suffixed) string and (if the cache is larger than Expr.cacheLength)
	 *	deleting the oldest entry
	 */function createCache(){var keys=[];function cache(key,value){ // Use (key + " ") to avoid collision with native prototype properties (see Issue #157)
	if(keys.push(key+" ")>Expr.cacheLength){ // Only keep the most recent entries
	delete cache[keys.shift()];}return cache[key+" "]=value;}return cache;} /**
	 * Mark a function for special use by Sizzle
	 * @param {Function} fn The function to mark
	 */function markFunction(fn){fn[expando]=true;return fn;} /**
	 * Support testing using an element
	 * @param {Function} fn Passed the created div and expects a boolean result
	 */function assert(fn){var div=document.createElement("div");try{return !!fn(div);}catch(e){return false;}finally { // Remove from its parent by default
	if(div.parentNode){div.parentNode.removeChild(div);} // release memory in IE
	div=null;}} /**
	 * Adds the same handler for all of the specified attrs
	 * @param {String} attrs Pipe-separated list of attributes
	 * @param {Function} handler The method that will be applied
	 */function addHandle(attrs,handler){var arr=attrs.split("|"),i=arr.length;while(i--){Expr.attrHandle[arr[i]]=handler;}} /**
	 * Checks document order of two siblings
	 * @param {Element} a
	 * @param {Element} b
	 * @returns {Number} Returns less than 0 if a precedes b, greater than 0 if a follows b
	 */function siblingCheck(a,b){var cur=b&&a,diff=cur&&a.nodeType===1&&b.nodeType===1&&(~b.sourceIndex||MAX_NEGATIVE)-(~a.sourceIndex||MAX_NEGATIVE); // Use IE sourceIndex if available on both nodes
	if(diff){return diff;} // Check if b follows a
	if(cur){while(cur=cur.nextSibling){if(cur===b){return -1;}}}return a?1:-1;} /**
	 * Returns a function to use in pseudos for input types
	 * @param {String} type
	 */function createInputPseudo(type){return function(elem){var name=elem.nodeName.toLowerCase();return name==="input"&&elem.type===type;};} /**
	 * Returns a function to use in pseudos for buttons
	 * @param {String} type
	 */function createButtonPseudo(type){return function(elem){var name=elem.nodeName.toLowerCase();return (name==="input"||name==="button")&&elem.type===type;};} /**
	 * Returns a function to use in pseudos for positionals
	 * @param {Function} fn
	 */function createPositionalPseudo(fn){return markFunction(function(argument){argument=+argument;return markFunction(function(seed,matches){var j,matchIndexes=fn([],seed.length,argument),i=matchIndexes.length; // Match elements found at the specified indexes
	while(i--){if(seed[j=matchIndexes[i]]){seed[j]=!(matches[j]=seed[j]);}}});});} /**
	 * Checks a node for validity as a Sizzle context
	 * @param {Element|Object=} context
	 * @returns {Element|Object|Boolean} The input node if acceptable, otherwise a falsy value
	 */function testContext(context){return context&&typeof context.getElementsByTagName!=="undefined"&&context;} // Expose support vars for convenience
	support=Sizzle.support={}; /**
	 * Detects XML nodes
	 * @param {Element|Object} elem An element or a document
	 * @returns {Boolean} True iff elem is a non-HTML XML node
	 */isXML=Sizzle.isXML=function(elem){ // documentElement is verified for cases where it doesn't yet exist
	// (such as loading iframes in IE - #4833)
	var documentElement=elem&&(elem.ownerDocument||elem).documentElement;return documentElement?documentElement.nodeName!=="HTML":false;}; /**
	 * Sets document-related variables once based on the current document
	 * @param {Element|Object} [doc] An element or document object to use to set the document
	 * @returns {Object} Returns the current document
	 */setDocument=Sizzle.setDocument=function(node){var hasCompare,parent,doc=node?node.ownerDocument||node:preferredDoc; // Return early if doc is invalid or already selected
	if(doc===document||doc.nodeType!==9||!doc.documentElement){return document;} // Update global variables
	document=doc;docElem=document.documentElement;documentIsHTML=!isXML(document); // Support: IE 9-11, Edge
	// Accessing iframe documents after unload throws "permission denied" errors (jQuery #13936)
	if((parent=document.defaultView)&&parent.top!==parent){ // Support: IE 11
	if(parent.addEventListener){parent.addEventListener("unload",unloadHandler,false); // Support: IE 9 - 10 only
	}else if(parent.attachEvent){parent.attachEvent("onunload",unloadHandler);}} /* Attributes
		---------------------------------------------------------------------- */ // Support: IE<8
	// Verify that getAttribute really returns attributes and not properties
	// (excepting IE8 booleans)
	support.attributes=assert(function(div){div.className="i";return !div.getAttribute("className");}); /* getElement(s)By*
		---------------------------------------------------------------------- */ // Check if getElementsByTagName("*") returns only elements
	support.getElementsByTagName=assert(function(div){div.appendChild(document.createComment(""));return !div.getElementsByTagName("*").length;}); // Support: IE<9
	support.getElementsByClassName=rnative.test(document.getElementsByClassName); // Support: IE<10
	// Check if getElementById returns elements by name
	// The broken getElementById methods don't pick up programatically-set names,
	// so use a roundabout getElementsByName test
	support.getById=assert(function(div){docElem.appendChild(div).id=expando;return !document.getElementsByName||!document.getElementsByName(expando).length;}); // ID find and filter
	if(support.getById){Expr.find["ID"]=function(id,context){if(typeof context.getElementById!=="undefined"&&documentIsHTML){var m=context.getElementById(id);return m?[m]:[];}};Expr.filter["ID"]=function(id){var attrId=id.replace(runescape,funescape);return function(elem){return elem.getAttribute("id")===attrId;};};}else { // Support: IE6/7
	// getElementById is not reliable as a find shortcut
	delete Expr.find["ID"];Expr.filter["ID"]=function(id){var attrId=id.replace(runescape,funescape);return function(elem){var node=typeof elem.getAttributeNode!=="undefined"&&elem.getAttributeNode("id");return node&&node.value===attrId;};};} // Tag
	Expr.find["TAG"]=support.getElementsByTagName?function(tag,context){if(typeof context.getElementsByTagName!=="undefined"){return context.getElementsByTagName(tag); // DocumentFragment nodes don't have gEBTN
	}else if(support.qsa){return context.querySelectorAll(tag);}}:function(tag,context){var elem,tmp=[],i=0, // By happy coincidence, a (broken) gEBTN appears on DocumentFragment nodes too
	results=context.getElementsByTagName(tag); // Filter out possible comments
	if(tag==="*"){while(elem=results[i++]){if(elem.nodeType===1){tmp.push(elem);}}return tmp;}return results;}; // Class
	Expr.find["CLASS"]=support.getElementsByClassName&&function(className,context){if(typeof context.getElementsByClassName!=="undefined"&&documentIsHTML){return context.getElementsByClassName(className);}}; /* QSA/matchesSelector
		---------------------------------------------------------------------- */ // QSA and matchesSelector support
	// matchesSelector(:active) reports false when true (IE9/Opera 11.5)
	rbuggyMatches=[]; // qSa(:focus) reports false when true (Chrome 21)
	// We allow this because of a bug in IE8/9 that throws an error
	// whenever `document.activeElement` is accessed on an iframe
	// So, we allow :focus to pass through QSA all the time to avoid the IE error
	// See http://bugs.jquery.com/ticket/13378
	rbuggyQSA=[];if(support.qsa=rnative.test(document.querySelectorAll)){ // Build QSA regex
	// Regex strategy adopted from Diego Perini
	assert(function(div){ // Select is set to empty string on purpose
	// This is to test IE's treatment of not explicitly
	// setting a boolean content attribute,
	// since its presence should be enough
	// http://bugs.jquery.com/ticket/12359
	docElem.appendChild(div).innerHTML="<a id='"+expando+"'></a>"+"<select id='"+expando+"-\r\\' msallowcapture=''>"+"<option selected=''></option></select>"; // Support: IE8, Opera 11-12.16
	// Nothing should be selected when empty strings follow ^= or $= or *=
	// The test attribute must be unknown in Opera but "safe" for WinRT
	// http://msdn.microsoft.com/en-us/library/ie/hh465388.aspx#attribute_section
	if(div.querySelectorAll("[msallowcapture^='']").length){rbuggyQSA.push("[*^$]="+whitespace+"*(?:''|\"\")");} // Support: IE8
	// Boolean attributes and "value" are not treated correctly
	if(!div.querySelectorAll("[selected]").length){rbuggyQSA.push("\\["+whitespace+"*(?:value|"+booleans+")");} // Support: Chrome<29, Android<4.4, Safari<7.0+, iOS<7.0+, PhantomJS<1.9.8+
	if(!div.querySelectorAll("[id~="+expando+"-]").length){rbuggyQSA.push("~=");} // Webkit/Opera - :checked should return selected option elements
	// http://www.w3.org/TR/2011/REC-css3-selectors-20110929/#checked
	// IE8 throws error here and will not see later tests
	if(!div.querySelectorAll(":checked").length){rbuggyQSA.push(":checked");} // Support: Safari 8+, iOS 8+
	// https://bugs.webkit.org/show_bug.cgi?id=136851
	// In-page `selector#id sibing-combinator selector` fails
	if(!div.querySelectorAll("a#"+expando+"+*").length){rbuggyQSA.push(".#.+[+~]");}});assert(function(div){ // Support: Windows 8 Native Apps
	// The type and name attributes are restricted during .innerHTML assignment
	var input=document.createElement("input");input.setAttribute("type","hidden");div.appendChild(input).setAttribute("name","D"); // Support: IE8
	// Enforce case-sensitivity of name attribute
	if(div.querySelectorAll("[name=d]").length){rbuggyQSA.push("name"+whitespace+"*[*^$|!~]?=");} // FF 3.5 - :enabled/:disabled and hidden elements (hidden elements are still enabled)
	// IE8 throws error here and will not see later tests
	if(!div.querySelectorAll(":enabled").length){rbuggyQSA.push(":enabled",":disabled");} // Opera 10-11 does not throw on post-comma invalid pseudos
	div.querySelectorAll("*,:x");rbuggyQSA.push(",.*:");});}if(support.matchesSelector=rnative.test(matches=docElem.matches||docElem.webkitMatchesSelector||docElem.mozMatchesSelector||docElem.oMatchesSelector||docElem.msMatchesSelector)){assert(function(div){ // Check to see if it's possible to do matchesSelector
	// on a disconnected node (IE 9)
	support.disconnectedMatch=matches.call(div,"div"); // This should fail with an exception
	// Gecko does not error, returns false instead
	matches.call(div,"[s!='']:x");rbuggyMatches.push("!=",pseudos);});}rbuggyQSA=rbuggyQSA.length&&new RegExp(rbuggyQSA.join("|"));rbuggyMatches=rbuggyMatches.length&&new RegExp(rbuggyMatches.join("|")); /* Contains
		---------------------------------------------------------------------- */hasCompare=rnative.test(docElem.compareDocumentPosition); // Element contains another
	// Purposefully self-exclusive
	// As in, an element does not contain itself
	contains=hasCompare||rnative.test(docElem.contains)?function(a,b){var adown=a.nodeType===9?a.documentElement:a,bup=b&&b.parentNode;return a===bup||!!(bup&&bup.nodeType===1&&(adown.contains?adown.contains(bup):a.compareDocumentPosition&&a.compareDocumentPosition(bup)&16));}:function(a,b){if(b){while(b=b.parentNode){if(b===a){return true;}}}return false;}; /* Sorting
		---------------------------------------------------------------------- */ // Document order sorting
	sortOrder=hasCompare?function(a,b){ // Flag for duplicate removal
	if(a===b){hasDuplicate=true;return 0;} // Sort on method existence if only one input has compareDocumentPosition
	var compare=!a.compareDocumentPosition-!b.compareDocumentPosition;if(compare){return compare;} // Calculate position if both inputs belong to the same document
	compare=(a.ownerDocument||a)===(b.ownerDocument||b)?a.compareDocumentPosition(b): // Otherwise we know they are disconnected
	1; // Disconnected nodes
	if(compare&1||!support.sortDetached&&b.compareDocumentPosition(a)===compare){ // Choose the first element that is related to our preferred document
	if(a===document||a.ownerDocument===preferredDoc&&contains(preferredDoc,a)){return -1;}if(b===document||b.ownerDocument===preferredDoc&&contains(preferredDoc,b)){return 1;} // Maintain original order
	return sortInput?indexOf(sortInput,a)-indexOf(sortInput,b):0;}return compare&4?-1:1;}:function(a,b){ // Exit early if the nodes are identical
	if(a===b){hasDuplicate=true;return 0;}var cur,i=0,aup=a.parentNode,bup=b.parentNode,ap=[a],bp=[b]; // Parentless nodes are either documents or disconnected
	if(!aup||!bup){return a===document?-1:b===document?1:aup?-1:bup?1:sortInput?indexOf(sortInput,a)-indexOf(sortInput,b):0; // If the nodes are siblings, we can do a quick check
	}else if(aup===bup){return siblingCheck(a,b);} // Otherwise we need full lists of their ancestors for comparison
	cur=a;while(cur=cur.parentNode){ap.unshift(cur);}cur=b;while(cur=cur.parentNode){bp.unshift(cur);} // Walk down the tree looking for a discrepancy
	while(ap[i]===bp[i]){i++;}return i? // Do a sibling check if the nodes have a common ancestor
	siblingCheck(ap[i],bp[i]): // Otherwise nodes in our document sort first
	ap[i]===preferredDoc?-1:bp[i]===preferredDoc?1:0;};return document;};Sizzle.matches=function(expr,elements){return Sizzle(expr,null,null,elements);};Sizzle.matchesSelector=function(elem,expr){ // Set document vars if needed
	if((elem.ownerDocument||elem)!==document){setDocument(elem);} // Make sure that attribute selectors are quoted
	expr=expr.replace(rattributeQuotes,"='$1']");if(support.matchesSelector&&documentIsHTML&&!compilerCache[expr+" "]&&(!rbuggyMatches||!rbuggyMatches.test(expr))&&(!rbuggyQSA||!rbuggyQSA.test(expr))){try{var ret=matches.call(elem,expr); // IE 9's matchesSelector returns false on disconnected nodes
	if(ret||support.disconnectedMatch|| // As well, disconnected nodes are said to be in a document
	// fragment in IE 9
	elem.document&&elem.document.nodeType!==11){return ret;}}catch(e){}}return Sizzle(expr,document,null,[elem]).length>0;};Sizzle.contains=function(context,elem){ // Set document vars if needed
	if((context.ownerDocument||context)!==document){setDocument(context);}return contains(context,elem);};Sizzle.attr=function(elem,name){ // Set document vars if needed
	if((elem.ownerDocument||elem)!==document){setDocument(elem);}var fn=Expr.attrHandle[name.toLowerCase()], // Don't get fooled by Object.prototype properties (jQuery #13807)
	val=fn&&hasOwn.call(Expr.attrHandle,name.toLowerCase())?fn(elem,name,!documentIsHTML):undefined;return val!==undefined?val:support.attributes||!documentIsHTML?elem.getAttribute(name):(val=elem.getAttributeNode(name))&&val.specified?val.value:null;};Sizzle.error=function(msg){throw new Error("Syntax error, unrecognized expression: "+msg);}; /**
	 * Document sorting and removing duplicates
	 * @param {ArrayLike} results
	 */Sizzle.uniqueSort=function(results){var elem,duplicates=[],j=0,i=0; // Unless we *know* we can detect duplicates, assume their presence
	hasDuplicate=!support.detectDuplicates;sortInput=!support.sortStable&&results.slice(0);results.sort(sortOrder);if(hasDuplicate){while(elem=results[i++]){if(elem===results[i]){j=duplicates.push(i);}}while(j--){results.splice(duplicates[j],1);}} // Clear input after sorting to release objects
	// See https://github.com/jquery/sizzle/pull/225
	sortInput=null;return results;}; /**
	 * Utility function for retrieving the text value of an array of DOM nodes
	 * @param {Array|Element} elem
	 */getText=Sizzle.getText=function(elem){var node,ret="",i=0,nodeType=elem.nodeType;if(!nodeType){ // If no nodeType, this is expected to be an array
	while(node=elem[i++]){ // Do not traverse comment nodes
	ret+=getText(node);}}else if(nodeType===1||nodeType===9||nodeType===11){ // Use textContent for elements
	// innerText usage removed for consistency of new lines (jQuery #11153)
	if(typeof elem.textContent==="string"){return elem.textContent;}else { // Traverse its children
	for(elem=elem.firstChild;elem;elem=elem.nextSibling){ret+=getText(elem);}}}else if(nodeType===3||nodeType===4){return elem.nodeValue;} // Do not include comment or processing instruction nodes
	return ret;};Expr=Sizzle.selectors={ // Can be adjusted by the user
	cacheLength:50,createPseudo:markFunction,match:matchExpr,attrHandle:{},find:{},relative:{">":{dir:"parentNode",first:true}," ":{dir:"parentNode"},"+":{dir:"previousSibling",first:true},"~":{dir:"previousSibling"}},preFilter:{"ATTR":function ATTR(match){match[1]=match[1].replace(runescape,funescape); // Move the given value to match[3] whether quoted or unquoted
	match[3]=(match[3]||match[4]||match[5]||"").replace(runescape,funescape);if(match[2]==="~="){match[3]=" "+match[3]+" ";}return match.slice(0,4);},"CHILD":function CHILD(match){ /* matches from matchExpr["CHILD"]
					1 type (only|nth|...)
					2 what (child|of-type)
					3 argument (even|odd|\d*|\d*n([+-]\d+)?|...)
					4 xn-component of xn+y argument ([+-]?\d*n|)
					5 sign of xn-component
					6 x of xn-component
					7 sign of y-component
					8 y of y-component
				*/match[1]=match[1].toLowerCase();if(match[1].slice(0,3)==="nth"){ // nth-* requires argument
	if(!match[3]){Sizzle.error(match[0]);} // numeric x and y parameters for Expr.filter.CHILD
	// remember that false/true cast respectively to 0/1
	match[4]=+(match[4]?match[5]+(match[6]||1):2*(match[3]==="even"||match[3]==="odd"));match[5]=+(match[7]+match[8]||match[3]==="odd"); // other types prohibit arguments
	}else if(match[3]){Sizzle.error(match[0]);}return match;},"PSEUDO":function PSEUDO(match){var excess,unquoted=!match[6]&&match[2];if(matchExpr["CHILD"].test(match[0])){return null;} // Accept quoted arguments as-is
	if(match[3]){match[2]=match[4]||match[5]||""; // Strip excess characters from unquoted arguments
	}else if(unquoted&&rpseudo.test(unquoted)&&( // Get excess from tokenize (recursively)
	excess=tokenize(unquoted,true))&&( // advance to the next closing parenthesis
	excess=unquoted.indexOf(")",unquoted.length-excess)-unquoted.length)){ // excess is a negative index
	match[0]=match[0].slice(0,excess);match[2]=unquoted.slice(0,excess);} // Return only captures needed by the pseudo filter method (type and argument)
	return match.slice(0,3);}},filter:{"TAG":function TAG(nodeNameSelector){var nodeName=nodeNameSelector.replace(runescape,funescape).toLowerCase();return nodeNameSelector==="*"?function(){return true;}:function(elem){return elem.nodeName&&elem.nodeName.toLowerCase()===nodeName;};},"CLASS":function CLASS(className){var pattern=classCache[className+" "];return pattern||(pattern=new RegExp("(^|"+whitespace+")"+className+"("+whitespace+"|$)"))&&classCache(className,function(elem){return pattern.test(typeof elem.className==="string"&&elem.className||typeof elem.getAttribute!=="undefined"&&elem.getAttribute("class")||"");});},"ATTR":function ATTR(name,operator,check){return function(elem){var result=Sizzle.attr(elem,name);if(result==null){return operator==="!=";}if(!operator){return true;}result+="";return operator==="="?result===check:operator==="!="?result!==check:operator==="^="?check&&result.indexOf(check)===0:operator==="*="?check&&result.indexOf(check)>-1:operator==="$="?check&&result.slice(-check.length)===check:operator==="~="?(" "+result.replace(rwhitespace," ")+" ").indexOf(check)>-1:operator==="|="?result===check||result.slice(0,check.length+1)===check+"-":false;};},"CHILD":function CHILD(type,what,argument,first,last){var simple=type.slice(0,3)!=="nth",forward=type.slice(-4)!=="last",ofType=what==="of-type";return first===1&&last===0? // Shortcut for :nth-*(n)
	function(elem){return !!elem.parentNode;}:function(elem,context,xml){var cache,uniqueCache,outerCache,node,nodeIndex,start,dir=simple!==forward?"nextSibling":"previousSibling",parent=elem.parentNode,name=ofType&&elem.nodeName.toLowerCase(),useCache=!xml&&!ofType,diff=false;if(parent){ // :(first|last|only)-(child|of-type)
	if(simple){while(dir){node=elem;while(node=node[dir]){if(ofType?node.nodeName.toLowerCase()===name:node.nodeType===1){return false;}} // Reverse direction for :only-* (if we haven't yet done so)
	start=dir=type==="only"&&!start&&"nextSibling";}return true;}start=[forward?parent.firstChild:parent.lastChild]; // non-xml :nth-child(...) stores cache data on `parent`
	if(forward&&useCache){ // Seek `elem` from a previously-cached index
	// ...in a gzip-friendly way
	node=parent;outerCache=node[expando]||(node[expando]={}); // Support: IE <9 only
	// Defend against cloned attroperties (jQuery gh-1709)
	uniqueCache=outerCache[node.uniqueID]||(outerCache[node.uniqueID]={});cache=uniqueCache[type]||[];nodeIndex=cache[0]===dirruns&&cache[1];diff=nodeIndex&&cache[2];node=nodeIndex&&parent.childNodes[nodeIndex];while(node=++nodeIndex&&node&&node[dir]||( // Fallback to seeking `elem` from the start
	diff=nodeIndex=0)||start.pop()){ // When found, cache indexes on `parent` and break
	if(node.nodeType===1&&++diff&&node===elem){uniqueCache[type]=[dirruns,nodeIndex,diff];break;}}}else { // Use previously-cached element index if available
	if(useCache){ // ...in a gzip-friendly way
	node=elem;outerCache=node[expando]||(node[expando]={}); // Support: IE <9 only
	// Defend against cloned attroperties (jQuery gh-1709)
	uniqueCache=outerCache[node.uniqueID]||(outerCache[node.uniqueID]={});cache=uniqueCache[type]||[];nodeIndex=cache[0]===dirruns&&cache[1];diff=nodeIndex;} // xml :nth-child(...)
	// or :nth-last-child(...) or :nth(-last)?-of-type(...)
	if(diff===false){ // Use the same loop as above to seek `elem` from the start
	while(node=++nodeIndex&&node&&node[dir]||(diff=nodeIndex=0)||start.pop()){if((ofType?node.nodeName.toLowerCase()===name:node.nodeType===1)&&++diff){ // Cache the index of each encountered element
	if(useCache){outerCache=node[expando]||(node[expando]={}); // Support: IE <9 only
	// Defend against cloned attroperties (jQuery gh-1709)
	uniqueCache=outerCache[node.uniqueID]||(outerCache[node.uniqueID]={});uniqueCache[type]=[dirruns,diff];}if(node===elem){break;}}}}} // Incorporate the offset, then check against cycle size
	diff-=last;return diff===first||diff%first===0&&diff/first>=0;}};},"PSEUDO":function PSEUDO(pseudo,argument){ // pseudo-class names are case-insensitive
	// http://www.w3.org/TR/selectors/#pseudo-classes
	// Prioritize by case sensitivity in case custom pseudos are added with uppercase letters
	// Remember that setFilters inherits from pseudos
	var args,fn=Expr.pseudos[pseudo]||Expr.setFilters[pseudo.toLowerCase()]||Sizzle.error("unsupported pseudo: "+pseudo); // The user may use createPseudo to indicate that
	// arguments are needed to create the filter function
	// just as Sizzle does
	if(fn[expando]){return fn(argument);} // But maintain support for old signatures
	if(fn.length>1){args=[pseudo,pseudo,"",argument];return Expr.setFilters.hasOwnProperty(pseudo.toLowerCase())?markFunction(function(seed,matches){var idx,matched=fn(seed,argument),i=matched.length;while(i--){idx=indexOf(seed,matched[i]);seed[idx]=!(matches[idx]=matched[i]);}}):function(elem){return fn(elem,0,args);};}return fn;}},pseudos:{ // Potentially complex pseudos
	"not":markFunction(function(selector){ // Trim the selector passed to compile
	// to avoid treating leading and trailing
	// spaces as combinators
	var input=[],results=[],matcher=compile(selector.replace(rtrim,"$1"));return matcher[expando]?markFunction(function(seed,matches,context,xml){var elem,unmatched=matcher(seed,null,xml,[]),i=seed.length; // Match elements unmatched by `matcher`
	while(i--){if(elem=unmatched[i]){seed[i]=!(matches[i]=elem);}}}):function(elem,context,xml){input[0]=elem;matcher(input,null,xml,results); // Don't keep the element (issue #299)
	input[0]=null;return !results.pop();};}),"has":markFunction(function(selector){return function(elem){return Sizzle(selector,elem).length>0;};}),"contains":markFunction(function(text){text=text.replace(runescape,funescape);return function(elem){return (elem.textContent||elem.innerText||getText(elem)).indexOf(text)>-1;};}), // "Whether an element is represented by a :lang() selector
	// is based solely on the element's language value
	// being equal to the identifier C,
	// or beginning with the identifier C immediately followed by "-".
	// The matching of C against the element's language value is performed case-insensitively.
	// The identifier C does not have to be a valid language name."
	// http://www.w3.org/TR/selectors/#lang-pseudo
	"lang":markFunction(function(lang){ // lang value must be a valid identifier
	if(!ridentifier.test(lang||"")){Sizzle.error("unsupported lang: "+lang);}lang=lang.replace(runescape,funescape).toLowerCase();return function(elem){var elemLang;do {if(elemLang=documentIsHTML?elem.lang:elem.getAttribute("xml:lang")||elem.getAttribute("lang")){elemLang=elemLang.toLowerCase();return elemLang===lang||elemLang.indexOf(lang+"-")===0;}}while((elem=elem.parentNode)&&elem.nodeType===1);return false;};}), // Miscellaneous
	"target":function target(elem){var hash=window.location&&window.location.hash;return hash&&hash.slice(1)===elem.id;},"root":function root(elem){return elem===docElem;},"focus":function focus(elem){return elem===document.activeElement&&(!document.hasFocus||document.hasFocus())&&!!(elem.type||elem.href||~elem.tabIndex);}, // Boolean properties
	"enabled":function enabled(elem){return elem.disabled===false;},"disabled":function disabled(elem){return elem.disabled===true;},"checked":function checked(elem){ // In CSS3, :checked should return both checked and selected elements
	// http://www.w3.org/TR/2011/REC-css3-selectors-20110929/#checked
	var nodeName=elem.nodeName.toLowerCase();return nodeName==="input"&&!!elem.checked||nodeName==="option"&&!!elem.selected;},"selected":function selected(elem){ // Accessing this property makes selected-by-default
	// options in Safari work properly
	if(elem.parentNode){elem.parentNode.selectedIndex;}return elem.selected===true;}, // Contents
	"empty":function empty(elem){ // http://www.w3.org/TR/selectors/#empty-pseudo
	// :empty is negated by element (1) or content nodes (text: 3; cdata: 4; entity ref: 5),
	//   but not by others (comment: 8; processing instruction: 7; etc.)
	// nodeType < 6 works because attributes (2) do not appear as children
	for(elem=elem.firstChild;elem;elem=elem.nextSibling){if(elem.nodeType<6){return false;}}return true;},"parent":function parent(elem){return !Expr.pseudos["empty"](elem);}, // Element/input types
	"header":function header(elem){return rheader.test(elem.nodeName);},"input":function input(elem){return rinputs.test(elem.nodeName);},"button":function button(elem){var name=elem.nodeName.toLowerCase();return name==="input"&&elem.type==="button"||name==="button";},"text":function text(elem){var attr;return elem.nodeName.toLowerCase()==="input"&&elem.type==="text"&&( // Support: IE<8
	// New HTML5 attribute values (e.g., "search") appear with elem.type === "text"
	(attr=elem.getAttribute("type"))==null||attr.toLowerCase()==="text");}, // Position-in-collection
	"first":createPositionalPseudo(function(){return [0];}),"last":createPositionalPseudo(function(matchIndexes,length){return [length-1];}),"eq":createPositionalPseudo(function(matchIndexes,length,argument){return [argument<0?argument+length:argument];}),"even":createPositionalPseudo(function(matchIndexes,length){var i=0;for(;i<length;i+=2){matchIndexes.push(i);}return matchIndexes;}),"odd":createPositionalPseudo(function(matchIndexes,length){var i=1;for(;i<length;i+=2){matchIndexes.push(i);}return matchIndexes;}),"lt":createPositionalPseudo(function(matchIndexes,length,argument){var i=argument<0?argument+length:argument;for(;--i>=0;){matchIndexes.push(i);}return matchIndexes;}),"gt":createPositionalPseudo(function(matchIndexes,length,argument){var i=argument<0?argument+length:argument;for(;++i<length;){matchIndexes.push(i);}return matchIndexes;})}};Expr.pseudos["nth"]=Expr.pseudos["eq"]; // Add button/input type pseudos
	for(i in {radio:true,checkbox:true,file:true,password:true,image:true}){Expr.pseudos[i]=createInputPseudo(i);}for(i in {submit:true,reset:true}){Expr.pseudos[i]=createButtonPseudo(i);} // Easy API for creating new setFilters
	function setFilters(){}setFilters.prototype=Expr.filters=Expr.pseudos;Expr.setFilters=new setFilters();tokenize=Sizzle.tokenize=function(selector,parseOnly){var matched,match,tokens,type,soFar,groups,preFilters,cached=tokenCache[selector+" "];if(cached){return parseOnly?0:cached.slice(0);}soFar=selector;groups=[];preFilters=Expr.preFilter;while(soFar){ // Comma and first run
	if(!matched||(match=rcomma.exec(soFar))){if(match){ // Don't consume trailing commas as valid
	soFar=soFar.slice(match[0].length)||soFar;}groups.push(tokens=[]);}matched=false; // Combinators
	if(match=rcombinators.exec(soFar)){matched=match.shift();tokens.push({value:matched, // Cast descendant combinators to space
	type:match[0].replace(rtrim," ")});soFar=soFar.slice(matched.length);} // Filters
	for(type in Expr.filter){if((match=matchExpr[type].exec(soFar))&&(!preFilters[type]||(match=preFilters[type](match)))){matched=match.shift();tokens.push({value:matched,type:type,matches:match});soFar=soFar.slice(matched.length);}}if(!matched){break;}} // Return the length of the invalid excess
	// if we're just parsing
	// Otherwise, throw an error or return tokens
	return parseOnly?soFar.length:soFar?Sizzle.error(selector): // Cache the tokens
	tokenCache(selector,groups).slice(0);};function toSelector(tokens){var i=0,len=tokens.length,selector="";for(;i<len;i++){selector+=tokens[i].value;}return selector;}function addCombinator(matcher,combinator,base){var dir=combinator.dir,checkNonElements=base&&dir==="parentNode",doneName=done++;return combinator.first? // Check against closest ancestor/preceding element
	function(elem,context,xml){while(elem=elem[dir]){if(elem.nodeType===1||checkNonElements){return matcher(elem,context,xml);}}}: // Check against all ancestor/preceding elements
	function(elem,context,xml){var oldCache,uniqueCache,outerCache,newCache=[dirruns,doneName]; // We can't set arbitrary data on XML nodes, so they don't benefit from combinator caching
	if(xml){while(elem=elem[dir]){if(elem.nodeType===1||checkNonElements){if(matcher(elem,context,xml)){return true;}}}}else {while(elem=elem[dir]){if(elem.nodeType===1||checkNonElements){outerCache=elem[expando]||(elem[expando]={}); // Support: IE <9 only
	// Defend against cloned attroperties (jQuery gh-1709)
	uniqueCache=outerCache[elem.uniqueID]||(outerCache[elem.uniqueID]={});if((oldCache=uniqueCache[dir])&&oldCache[0]===dirruns&&oldCache[1]===doneName){ // Assign to newCache so results back-propagate to previous elements
	return newCache[2]=oldCache[2];}else { // Reuse newcache so results back-propagate to previous elements
	uniqueCache[dir]=newCache; // A match means we're done; a fail means we have to keep checking
	if(newCache[2]=matcher(elem,context,xml)){return true;}}}}}};}function elementMatcher(matchers){return matchers.length>1?function(elem,context,xml){var i=matchers.length;while(i--){if(!matchers[i](elem,context,xml)){return false;}}return true;}:matchers[0];}function multipleContexts(selector,contexts,results){var i=0,len=contexts.length;for(;i<len;i++){Sizzle(selector,contexts[i],results);}return results;}function condense(unmatched,map,filter,context,xml){var elem,newUnmatched=[],i=0,len=unmatched.length,mapped=map!=null;for(;i<len;i++){if(elem=unmatched[i]){if(!filter||filter(elem,context,xml)){newUnmatched.push(elem);if(mapped){map.push(i);}}}}return newUnmatched;}function setMatcher(preFilter,selector,matcher,postFilter,postFinder,postSelector){if(postFilter&&!postFilter[expando]){postFilter=setMatcher(postFilter);}if(postFinder&&!postFinder[expando]){postFinder=setMatcher(postFinder,postSelector);}return markFunction(function(seed,results,context,xml){var temp,i,elem,preMap=[],postMap=[],preexisting=results.length, // Get initial elements from seed or context
	elems=seed||multipleContexts(selector||"*",context.nodeType?[context]:context,[]), // Prefilter to get matcher input, preserving a map for seed-results synchronization
	matcherIn=preFilter&&(seed||!selector)?condense(elems,preMap,preFilter,context,xml):elems,matcherOut=matcher? // If we have a postFinder, or filtered seed, or non-seed postFilter or preexisting results,
	postFinder||(seed?preFilter:preexisting||postFilter)? // ...intermediate processing is necessary
	[]: // ...otherwise use results directly
	results:matcherIn; // Find primary matches
	if(matcher){matcher(matcherIn,matcherOut,context,xml);} // Apply postFilter
	if(postFilter){temp=condense(matcherOut,postMap);postFilter(temp,[],context,xml); // Un-match failing elements by moving them back to matcherIn
	i=temp.length;while(i--){if(elem=temp[i]){matcherOut[postMap[i]]=!(matcherIn[postMap[i]]=elem);}}}if(seed){if(postFinder||preFilter){if(postFinder){ // Get the final matcherOut by condensing this intermediate into postFinder contexts
	temp=[];i=matcherOut.length;while(i--){if(elem=matcherOut[i]){ // Restore matcherIn since elem is not yet a final match
	temp.push(matcherIn[i]=elem);}}postFinder(null,matcherOut=[],temp,xml);} // Move matched elements from seed to results to keep them synchronized
	i=matcherOut.length;while(i--){if((elem=matcherOut[i])&&(temp=postFinder?indexOf(seed,elem):preMap[i])>-1){seed[temp]=!(results[temp]=elem);}}} // Add elements to results, through postFinder if defined
	}else {matcherOut=condense(matcherOut===results?matcherOut.splice(preexisting,matcherOut.length):matcherOut);if(postFinder){postFinder(null,results,matcherOut,xml);}else {push.apply(results,matcherOut);}}});}function matcherFromTokens(tokens){var checkContext,matcher,j,len=tokens.length,leadingRelative=Expr.relative[tokens[0].type],implicitRelative=leadingRelative||Expr.relative[" "],i=leadingRelative?1:0, // The foundational matcher ensures that elements are reachable from top-level context(s)
	matchContext=addCombinator(function(elem){return elem===checkContext;},implicitRelative,true),matchAnyContext=addCombinator(function(elem){return indexOf(checkContext,elem)>-1;},implicitRelative,true),matchers=[function(elem,context,xml){var ret=!leadingRelative&&(xml||context!==outermostContext)||((checkContext=context).nodeType?matchContext(elem,context,xml):matchAnyContext(elem,context,xml)); // Avoid hanging onto element (issue #299)
	checkContext=null;return ret;}];for(;i<len;i++){if(matcher=Expr.relative[tokens[i].type]){matchers=[addCombinator(elementMatcher(matchers),matcher)];}else {matcher=Expr.filter[tokens[i].type].apply(null,tokens[i].matches); // Return special upon seeing a positional matcher
	if(matcher[expando]){ // Find the next relative operator (if any) for proper handling
	j=++i;for(;j<len;j++){if(Expr.relative[tokens[j].type]){break;}}return setMatcher(i>1&&elementMatcher(matchers),i>1&&toSelector( // If the preceding token was a descendant combinator, insert an implicit any-element `*`
	tokens.slice(0,i-1).concat({value:tokens[i-2].type===" "?"*":""})).replace(rtrim,"$1"),matcher,i<j&&matcherFromTokens(tokens.slice(i,j)),j<len&&matcherFromTokens(tokens=tokens.slice(j)),j<len&&toSelector(tokens));}matchers.push(matcher);}}return elementMatcher(matchers);}function matcherFromGroupMatchers(elementMatchers,setMatchers){var bySet=setMatchers.length>0,byElement=elementMatchers.length>0,superMatcher=function superMatcher(seed,context,xml,results,outermost){var elem,j,matcher,matchedCount=0,i="0",unmatched=seed&&[],setMatched=[],contextBackup=outermostContext, // We must always have either seed elements or outermost context
	elems=seed||byElement&&Expr.find["TAG"]("*",outermost), // Use integer dirruns iff this is the outermost matcher
	dirrunsUnique=dirruns+=contextBackup==null?1:Math.random()||0.1,len=elems.length;if(outermost){outermostContext=context===document||context||outermost;} // Add elements passing elementMatchers directly to results
	// Support: IE<9, Safari
	// Tolerate NodeList properties (IE: "length"; Safari: <number>) matching elements by id
	for(;i!==len&&(elem=elems[i])!=null;i++){if(byElement&&elem){j=0;if(!context&&elem.ownerDocument!==document){setDocument(elem);xml=!documentIsHTML;}while(matcher=elementMatchers[j++]){if(matcher(elem,context||document,xml)){results.push(elem);break;}}if(outermost){dirruns=dirrunsUnique;}} // Track unmatched elements for set filters
	if(bySet){ // They will have gone through all possible matchers
	if(elem=!matcher&&elem){matchedCount--;} // Lengthen the array for every element, matched or not
	if(seed){unmatched.push(elem);}}} // `i` is now the count of elements visited above, and adding it to `matchedCount`
	// makes the latter nonnegative.
	matchedCount+=i; // Apply set filters to unmatched elements
	// NOTE: This can be skipped if there are no unmatched elements (i.e., `matchedCount`
	// equals `i`), unless we didn't visit _any_ elements in the above loop because we have
	// no element matchers and no seed.
	// Incrementing an initially-string "0" `i` allows `i` to remain a string only in that
	// case, which will result in a "00" `matchedCount` that differs from `i` but is also
	// numerically zero.
	if(bySet&&i!==matchedCount){j=0;while(matcher=setMatchers[j++]){matcher(unmatched,setMatched,context,xml);}if(seed){ // Reintegrate element matches to eliminate the need for sorting
	if(matchedCount>0){while(i--){if(!(unmatched[i]||setMatched[i])){setMatched[i]=pop.call(results);}}} // Discard index placeholder values to get only actual matches
	setMatched=condense(setMatched);} // Add matches to results
	push.apply(results,setMatched); // Seedless set matches succeeding multiple successful matchers stipulate sorting
	if(outermost&&!seed&&setMatched.length>0&&matchedCount+setMatchers.length>1){Sizzle.uniqueSort(results);}} // Override manipulation of globals by nested matchers
	if(outermost){dirruns=dirrunsUnique;outermostContext=contextBackup;}return unmatched;};return bySet?markFunction(superMatcher):superMatcher;}compile=Sizzle.compile=function(selector,match /* Internal Use Only */){var i,setMatchers=[],elementMatchers=[],cached=compilerCache[selector+" "];if(!cached){ // Generate a function of recursive functions that can be used to check each element
	if(!match){match=tokenize(selector);}i=match.length;while(i--){cached=matcherFromTokens(match[i]);if(cached[expando]){setMatchers.push(cached);}else {elementMatchers.push(cached);}} // Cache the compiled function
	cached=compilerCache(selector,matcherFromGroupMatchers(elementMatchers,setMatchers)); // Save selector and tokenization
	cached.selector=selector;}return cached;}; /**
	 * A low-level selection function that works with Sizzle's compiled
	 *  selector functions
	 * @param {String|Function} selector A selector or a pre-compiled
	 *  selector function built with Sizzle.compile
	 * @param {Element} context
	 * @param {Array} [results]
	 * @param {Array} [seed] A set of elements to match against
	 */select=Sizzle.select=function(selector,context,results,seed){var i,tokens,token,type,find,compiled=typeof selector==="function"&&selector,match=!seed&&tokenize(selector=compiled.selector||selector);results=results||[]; // Try to minimize operations if there is only one selector in the list and no seed
	// (the latter of which guarantees us context)
	if(match.length===1){ // Reduce context if the leading compound selector is an ID
	tokens=match[0]=match[0].slice(0);if(tokens.length>2&&(token=tokens[0]).type==="ID"&&support.getById&&context.nodeType===9&&documentIsHTML&&Expr.relative[tokens[1].type]){context=(Expr.find["ID"](token.matches[0].replace(runescape,funescape),context)||[])[0];if(!context){return results; // Precompiled matchers will still verify ancestry, so step up a level
	}else if(compiled){context=context.parentNode;}selector=selector.slice(tokens.shift().value.length);} // Fetch a seed set for right-to-left matching
	i=matchExpr["needsContext"].test(selector)?0:tokens.length;while(i--){token=tokens[i]; // Abort if we hit a combinator
	if(Expr.relative[type=token.type]){break;}if(find=Expr.find[type]){ // Search, expanding context for leading sibling combinators
	if(seed=find(token.matches[0].replace(runescape,funescape),rsibling.test(tokens[0].type)&&testContext(context.parentNode)||context)){ // If seed is empty or no tokens remain, we can return early
	tokens.splice(i,1);selector=seed.length&&toSelector(tokens);if(!selector){push.apply(results,seed);return results;}break;}}}} // Compile and execute a filtering function if one is not provided
	// Provide `match` to avoid retokenization if we modified the selector above
	(compiled||compile(selector,match))(seed,context,!documentIsHTML,results,!context||rsibling.test(selector)&&testContext(context.parentNode)||context);return results;}; // One-time assignments
	// Sort stability
	support.sortStable=expando.split("").sort(sortOrder).join("")===expando; // Support: Chrome 14-35+
	// Always assume duplicates if they aren't passed to the comparison function
	support.detectDuplicates=!!hasDuplicate; // Initialize against the default document
	setDocument(); // Support: Webkit<537.32 - Safari 6.0.3/Chrome 25 (fixed in Chrome 27)
	// Detached nodes confoundingly follow *each other*
	support.sortDetached=assert(function(div1){ // Should return 1, but returns 4 (following)
	return div1.compareDocumentPosition(document.createElement("div"))&1;}); // Support: IE<8
	// Prevent attribute/property "interpolation"
	// http://msdn.microsoft.com/en-us/library/ms536429%28VS.85%29.aspx
	if(!assert(function(div){div.innerHTML="<a href='#'></a>";return div.firstChild.getAttribute("href")==="#";})){addHandle("type|href|height|width",function(elem,name,isXML){if(!isXML){return elem.getAttribute(name,name.toLowerCase()==="type"?1:2);}});} // Support: IE<9
	// Use defaultValue in place of getAttribute("value")
	if(!support.attributes||!assert(function(div){div.innerHTML="<input/>";div.firstChild.setAttribute("value","");return div.firstChild.getAttribute("value")==="";})){addHandle("value",function(elem,name,isXML){if(!isXML&&elem.nodeName.toLowerCase()==="input"){return elem.defaultValue;}});} // Support: IE<9
	// Use getAttributeNode to fetch booleans when getAttribute lies
	if(!assert(function(div){return div.getAttribute("disabled")==null;})){addHandle(booleans,function(elem,name,isXML){var val;if(!isXML){return elem[name]===true?name.toLowerCase():(val=elem.getAttributeNode(name))&&val.specified?val.value:null;}});}return Sizzle;}(window);jQuery.find=Sizzle;jQuery.expr=Sizzle.selectors;jQuery.expr[":"]=jQuery.expr.pseudos;jQuery.uniqueSort=jQuery.unique=Sizzle.uniqueSort;jQuery.text=Sizzle.getText;jQuery.isXMLDoc=Sizzle.isXML;jQuery.contains=Sizzle.contains;var dir=function dir(elem,_dir,until){var matched=[],truncate=until!==undefined;while((elem=elem[_dir])&&elem.nodeType!==9){if(elem.nodeType===1){if(truncate&&jQuery(elem).is(until)){break;}matched.push(elem);}}return matched;};var _siblings=function _siblings(n,elem){var matched=[];for(;n;n=n.nextSibling){if(n.nodeType===1&&n!==elem){matched.push(n);}}return matched;};var rneedsContext=jQuery.expr.match.needsContext;var rsingleTag=/^<([\w-]+)\s*\/?>(?:<\/\1>|)$/;var risSimple=/^.[^:#\[\.,]*$/; // Implement the identical functionality for filter and not
	function winnow(elements,qualifier,not){if(jQuery.isFunction(qualifier)){return jQuery.grep(elements,function(elem,i){ /* jshint -W018 */return !!qualifier.call(elem,i,elem)!==not;});}if(qualifier.nodeType){return jQuery.grep(elements,function(elem){return elem===qualifier!==not;});}if(typeof qualifier==="string"){if(risSimple.test(qualifier)){return jQuery.filter(qualifier,elements,not);}qualifier=jQuery.filter(qualifier,elements);}return jQuery.grep(elements,function(elem){return indexOf.call(qualifier,elem)>-1!==not;});}jQuery.filter=function(expr,elems,not){var elem=elems[0];if(not){expr=":not("+expr+")";}return elems.length===1&&elem.nodeType===1?jQuery.find.matchesSelector(elem,expr)?[elem]:[]:jQuery.find.matches(expr,jQuery.grep(elems,function(elem){return elem.nodeType===1;}));};jQuery.fn.extend({find:function find(selector){var i,len=this.length,ret=[],self=this;if(typeof selector!=="string"){return this.pushStack(jQuery(selector).filter(function(){for(i=0;i<len;i++){if(jQuery.contains(self[i],this)){return true;}}}));}for(i=0;i<len;i++){jQuery.find(selector,self[i],ret);} // Needed because $( selector, context ) becomes $( context ).find( selector )
	ret=this.pushStack(len>1?jQuery.unique(ret):ret);ret.selector=this.selector?this.selector+" "+selector:selector;return ret;},filter:function filter(selector){return this.pushStack(winnow(this,selector||[],false));},not:function not(selector){return this.pushStack(winnow(this,selector||[],true));},is:function is(selector){return !!winnow(this, // If this is a positional/relative selector, check membership in the returned set
	// so $("p:first").is("p:last") won't return true for a doc with two "p".
	typeof selector==="string"&&rneedsContext.test(selector)?jQuery(selector):selector||[],false).length;}}); // Initialize a jQuery object
	// A central reference to the root jQuery(document)
	var rootjQuery, // A simple way to check for HTML strings
	// Prioritize #id over <tag> to avoid XSS via location.hash (#9521)
	// Strict HTML recognition (#11290: must start with <)
	rquickExpr=/^(?:\s*(<[\w\W]+>)[^>]*|#([\w-]*))$/,init=jQuery.fn.init=function(selector,context,root){var match,elem; // HANDLE: $(""), $(null), $(undefined), $(false)
	if(!selector){return this;} // Method init() accepts an alternate rootjQuery
	// so migrate can support jQuery.sub (gh-2101)
	root=root||rootjQuery; // Handle HTML strings
	if(typeof selector==="string"){if(selector[0]==="<"&&selector[selector.length-1]===">"&&selector.length>=3){ // Assume that strings that start and end with <> are HTML and skip the regex check
	match=[null,selector,null];}else {match=rquickExpr.exec(selector);} // Match html or make sure no context is specified for #id
	if(match&&(match[1]||!context)){ // HANDLE: $(html) -> $(array)
	if(match[1]){context=context instanceof jQuery?context[0]:context; // Option to run scripts is true for back-compat
	// Intentionally let the error be thrown if parseHTML is not present
	jQuery.merge(this,jQuery.parseHTML(match[1],context&&context.nodeType?context.ownerDocument||context:document,true)); // HANDLE: $(html, props)
	if(rsingleTag.test(match[1])&&jQuery.isPlainObject(context)){for(match in context){ // Properties of context are called as methods if possible
	if(jQuery.isFunction(this[match])){this[match](context[match]); // ...and otherwise set as attributes
	}else {this.attr(match,context[match]);}}}return this; // HANDLE: $(#id)
	}else {elem=document.getElementById(match[2]); // Support: Blackberry 4.6
	// gEBID returns nodes no longer in the document (#6963)
	if(elem&&elem.parentNode){ // Inject the element directly into the jQuery object
	this.length=1;this[0]=elem;}this.context=document;this.selector=selector;return this;} // HANDLE: $(expr, $(...))
	}else if(!context||context.jquery){return (context||root).find(selector); // HANDLE: $(expr, context)
	// (which is just equivalent to: $(context).find(expr)
	}else {return this.constructor(context).find(selector);} // HANDLE: $(DOMElement)
	}else if(selector.nodeType){this.context=this[0]=selector;this.length=1;return this; // HANDLE: $(function)
	// Shortcut for document ready
	}else if(jQuery.isFunction(selector)){return root.ready!==undefined?root.ready(selector): // Execute immediately if ready is not present
	selector(jQuery);}if(selector.selector!==undefined){this.selector=selector.selector;this.context=selector.context;}return jQuery.makeArray(selector,this);}; // Give the init function the jQuery prototype for later instantiation
	init.prototype=jQuery.fn; // Initialize central reference
	rootjQuery=jQuery(document);var rparentsprev=/^(?:parents|prev(?:Until|All))/, // Methods guaranteed to produce a unique set when starting from a unique set
	guaranteedUnique={children:true,contents:true,next:true,prev:true};jQuery.fn.extend({has:function has(target){var targets=jQuery(target,this),l=targets.length;return this.filter(function(){var i=0;for(;i<l;i++){if(jQuery.contains(this,targets[i])){return true;}}});},closest:function closest(selectors,context){var cur,i=0,l=this.length,matched=[],pos=rneedsContext.test(selectors)||typeof selectors!=="string"?jQuery(selectors,context||this.context):0;for(;i<l;i++){for(cur=this[i];cur&&cur!==context;cur=cur.parentNode){ // Always skip document fragments
	if(cur.nodeType<11&&(pos?pos.index(cur)>-1: // Don't pass non-elements to Sizzle
	cur.nodeType===1&&jQuery.find.matchesSelector(cur,selectors))){matched.push(cur);break;}}}return this.pushStack(matched.length>1?jQuery.uniqueSort(matched):matched);}, // Determine the position of an element within the set
	index:function index(elem){ // No argument, return index in parent
	if(!elem){return this[0]&&this[0].parentNode?this.first().prevAll().length:-1;} // Index in selector
	if(typeof elem==="string"){return indexOf.call(jQuery(elem),this[0]);} // Locate the position of the desired element
	return indexOf.call(this, // If it receives a jQuery object, the first element is used
	elem.jquery?elem[0]:elem);},add:function add(selector,context){return this.pushStack(jQuery.uniqueSort(jQuery.merge(this.get(),jQuery(selector,context))));},addBack:function addBack(selector){return this.add(selector==null?this.prevObject:this.prevObject.filter(selector));}});function sibling(cur,dir){while((cur=cur[dir])&&cur.nodeType!==1){}return cur;}jQuery.each({parent:function parent(elem){var parent=elem.parentNode;return parent&&parent.nodeType!==11?parent:null;},parents:function parents(elem){return dir(elem,"parentNode");},parentsUntil:function parentsUntil(elem,i,until){return dir(elem,"parentNode",until);},next:function next(elem){return sibling(elem,"nextSibling");},prev:function prev(elem){return sibling(elem,"previousSibling");},nextAll:function nextAll(elem){return dir(elem,"nextSibling");},prevAll:function prevAll(elem){return dir(elem,"previousSibling");},nextUntil:function nextUntil(elem,i,until){return dir(elem,"nextSibling",until);},prevUntil:function prevUntil(elem,i,until){return dir(elem,"previousSibling",until);},siblings:function siblings(elem){return _siblings((elem.parentNode||{}).firstChild,elem);},children:function children(elem){return _siblings(elem.firstChild);},contents:function contents(elem){return elem.contentDocument||jQuery.merge([],elem.childNodes);}},function(name,fn){jQuery.fn[name]=function(until,selector){var matched=jQuery.map(this,fn,until);if(name.slice(-5)!=="Until"){selector=until;}if(selector&&typeof selector==="string"){matched=jQuery.filter(selector,matched);}if(this.length>1){ // Remove duplicates
	if(!guaranteedUnique[name]){jQuery.uniqueSort(matched);} // Reverse order for parents* and prev-derivatives
	if(rparentsprev.test(name)){matched.reverse();}}return this.pushStack(matched);};});var rnotwhite=/\S+/g; // Convert String-formatted options into Object-formatted ones
	function createOptions(options){var object={};jQuery.each(options.match(rnotwhite)||[],function(_,flag){object[flag]=true;});return object;} /*
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
	 */jQuery.Callbacks=function(options){ // Convert options from String-formatted to Object-formatted if needed
	// (we check in cache first)
	options=typeof options==="string"?createOptions(options):jQuery.extend({},options);var  // Flag to know if list is currently firing
	firing, // Last fire value for non-forgettable lists
	memory, // Flag to know if list was already fired
	_fired, // Flag to prevent firing
	_locked, // Actual callback list
	list=[], // Queue of execution data for repeatable lists
	queue=[], // Index of currently firing callback (modified by add/remove as needed)
	firingIndex=-1, // Fire callbacks
	fire=function fire(){ // Enforce single-firing
	_locked=options.once; // Execute callbacks for all pending executions,
	// respecting firingIndex overrides and runtime changes
	_fired=firing=true;for(;queue.length;firingIndex=-1){memory=queue.shift();while(++firingIndex<list.length){ // Run callback and check for early termination
	if(list[firingIndex].apply(memory[0],memory[1])===false&&options.stopOnFalse){ // Jump to end and forget the data so .add doesn't re-fire
	firingIndex=list.length;memory=false;}}} // Forget the data if we're done with it
	if(!options.memory){memory=false;}firing=false; // Clean up if we're done firing for good
	if(_locked){ // Keep an empty list if we have data for future add calls
	if(memory){list=[]; // Otherwise, this object is spent
	}else {list="";}}}, // Actual Callbacks object
	self={ // Add a callback or a collection of callbacks to the list
	add:function add(){if(list){ // If we have memory from a past run, we should fire after adding
	if(memory&&!firing){firingIndex=list.length-1;queue.push(memory);}(function add(args){jQuery.each(args,function(_,arg){if(jQuery.isFunction(arg)){if(!options.unique||!self.has(arg)){list.push(arg);}}else if(arg&&arg.length&&jQuery.type(arg)!=="string"){ // Inspect recursively
	add(arg);}});})(arguments);if(memory&&!firing){fire();}}return this;}, // Remove a callback from the list
	remove:function remove(){jQuery.each(arguments,function(_,arg){var index;while((index=jQuery.inArray(arg,list,index))>-1){list.splice(index,1); // Handle firing indexes
	if(index<=firingIndex){firingIndex--;}}});return this;}, // Check if a given callback is in the list.
	// If no argument is given, return whether or not list has callbacks attached.
	has:function has(fn){return fn?jQuery.inArray(fn,list)>-1:list.length>0;}, // Remove all callbacks from the list
	empty:function empty(){if(list){list=[];}return this;}, // Disable .fire and .add
	// Abort any current/pending executions
	// Clear all callbacks and values
	disable:function disable(){_locked=queue=[];list=memory="";return this;},disabled:function disabled(){return !list;}, // Disable .fire
	// Also disable .add unless we have memory (since it would have no effect)
	// Abort any pending executions
	lock:function lock(){_locked=queue=[];if(!memory){list=memory="";}return this;},locked:function locked(){return !!_locked;}, // Call all callbacks with the given context and arguments
	fireWith:function fireWith(context,args){if(!_locked){args=args||[];args=[context,args.slice?args.slice():args];queue.push(args);if(!firing){fire();}}return this;}, // Call all the callbacks with the given arguments
	fire:function fire(){self.fireWith(this,arguments);return this;}, // To know if the callbacks have already been called at least once
	fired:function fired(){return !!_fired;}};return self;};jQuery.extend({Deferred:function Deferred(func){var tuples=[ // action, add listener, listener list, final state
	["resolve","done",jQuery.Callbacks("once memory"),"resolved"],["reject","fail",jQuery.Callbacks("once memory"),"rejected"],["notify","progress",jQuery.Callbacks("memory")]],_state="pending",_promise={state:function state(){return _state;},always:function always(){deferred.done(arguments).fail(arguments);return this;},then:function then() /* fnDone, fnFail, fnProgress */{var fns=arguments;return jQuery.Deferred(function(newDefer){jQuery.each(tuples,function(i,tuple){var fn=jQuery.isFunction(fns[i])&&fns[i]; // deferred[ done | fail | progress ] for forwarding actions to newDefer
	deferred[tuple[1]](function(){var returned=fn&&fn.apply(this,arguments);if(returned&&jQuery.isFunction(returned.promise)){returned.promise().progress(newDefer.notify).done(newDefer.resolve).fail(newDefer.reject);}else {newDefer[tuple[0]+"With"](this===_promise?newDefer.promise():this,fn?[returned]:arguments);}});});fns=null;}).promise();}, // Get a promise for this deferred
	// If obj is provided, the promise aspect is added to the object
	promise:function promise(obj){return obj!=null?jQuery.extend(obj,_promise):_promise;}},deferred={}; // Keep pipe for back-compat
	_promise.pipe=_promise.then; // Add list-specific methods
	jQuery.each(tuples,function(i,tuple){var list=tuple[2],stateString=tuple[3]; // promise[ done | fail | progress ] = list.add
	_promise[tuple[1]]=list.add; // Handle state
	if(stateString){list.add(function(){ // state = [ resolved | rejected ]
	_state=stateString; // [ reject_list | resolve_list ].disable; progress_list.lock
	},tuples[i^1][2].disable,tuples[2][2].lock);} // deferred[ resolve | reject | notify ]
	deferred[tuple[0]]=function(){deferred[tuple[0]+"With"](this===deferred?_promise:this,arguments);return this;};deferred[tuple[0]+"With"]=list.fireWith;}); // Make the deferred a promise
	_promise.promise(deferred); // Call given func if any
	if(func){func.call(deferred,deferred);} // All done!
	return deferred;}, // Deferred helper
	when:function when(subordinate /* , ..., subordinateN */){var i=0,resolveValues=_slice.call(arguments),length=resolveValues.length, // the count of uncompleted subordinates
	remaining=length!==1||subordinate&&jQuery.isFunction(subordinate.promise)?length:0, // the master Deferred.
	// If resolveValues consist of only a single Deferred, just use that.
	deferred=remaining===1?subordinate:jQuery.Deferred(), // Update function for both resolve and progress values
	updateFunc=function updateFunc(i,contexts,values){return function(value){contexts[i]=this;values[i]=arguments.length>1?_slice.call(arguments):value;if(values===progressValues){deferred.notifyWith(contexts,values);}else if(! --remaining){deferred.resolveWith(contexts,values);}};},progressValues,progressContexts,resolveContexts; // Add listeners to Deferred subordinates; treat others as resolved
	if(length>1){progressValues=new Array(length);progressContexts=new Array(length);resolveContexts=new Array(length);for(;i<length;i++){if(resolveValues[i]&&jQuery.isFunction(resolveValues[i].promise)){resolveValues[i].promise().progress(updateFunc(i,progressContexts,progressValues)).done(updateFunc(i,resolveContexts,resolveValues)).fail(deferred.reject);}else {--remaining;}}} // If we're not waiting on anything, resolve the master
	if(!remaining){deferred.resolveWith(resolveContexts,resolveValues);}return deferred.promise();}}); // The deferred used on DOM ready
	var readyList;jQuery.fn.ready=function(fn){ // Add the callback
	jQuery.ready.promise().done(fn);return this;};jQuery.extend({ // Is the DOM ready to be used? Set to true once it occurs.
	isReady:false, // A counter to track how many items to wait for before
	// the ready event fires. See #6781
	readyWait:1, // Hold (or release) the ready event
	holdReady:function holdReady(hold){if(hold){jQuery.readyWait++;}else {jQuery.ready(true);}}, // Handle when the DOM is ready
	ready:function ready(wait){ // Abort if there are pending holds or we're already ready
	if(wait===true?--jQuery.readyWait:jQuery.isReady){return;} // Remember that the DOM is ready
	jQuery.isReady=true; // If a normal DOM Ready event fired, decrement, and wait if need be
	if(wait!==true&&--jQuery.readyWait>0){return;} // If there are functions bound, to execute
	readyList.resolveWith(document,[jQuery]); // Trigger any bound ready events
	if(jQuery.fn.triggerHandler){jQuery(document).triggerHandler("ready");jQuery(document).off("ready");}}}); /**
	 * The ready event handler and self cleanup method
	 */function completed(){document.removeEventListener("DOMContentLoaded",completed);window.removeEventListener("load",completed);jQuery.ready();}jQuery.ready.promise=function(obj){if(!readyList){readyList=jQuery.Deferred(); // Catch cases where $(document).ready() is called
	// after the browser event has already occurred.
	// Support: IE9-10 only
	// Older IE sometimes signals "interactive" too soon
	if(document.readyState==="complete"||document.readyState!=="loading"&&!document.documentElement.doScroll){ // Handle it asynchronously to allow scripts the opportunity to delay ready
	window.setTimeout(jQuery.ready);}else { // Use the handy event callback
	document.addEventListener("DOMContentLoaded",completed); // A fallback to window.onload, that will always work
	window.addEventListener("load",completed);}}return readyList.promise(obj);}; // Kick off the DOM ready check even if the user does not
	jQuery.ready.promise(); // Multifunctional method to get and set values of a collection
	// The value/s can optionally be executed if it's a function
	var access=function access(elems,fn,key,value,chainable,emptyGet,raw){var i=0,len=elems.length,bulk=key==null; // Sets many values
	if(jQuery.type(key)==="object"){chainable=true;for(i in key){access(elems,fn,i,key[i],true,emptyGet,raw);} // Sets one value
	}else if(value!==undefined){chainable=true;if(!jQuery.isFunction(value)){raw=true;}if(bulk){ // Bulk operations run against the entire set
	if(raw){fn.call(elems,value);fn=null; // ...except when executing function values
	}else {bulk=fn;fn=function fn(elem,key,value){return bulk.call(jQuery(elem),value);};}}if(fn){for(;i<len;i++){fn(elems[i],key,raw?value:value.call(elems[i],i,fn(elems[i],key)));}}}return chainable?elems: // Gets
	bulk?fn.call(elems):len?fn(elems[0],key):emptyGet;};var acceptData=function acceptData(owner){ // Accepts only:
	//  - Node
	//    - Node.ELEMENT_NODE
	//    - Node.DOCUMENT_NODE
	//  - Object
	//    - Any
	/* jshint -W018 */return owner.nodeType===1||owner.nodeType===9||! +owner.nodeType;};function Data(){this.expando=jQuery.expando+Data.uid++;}Data.uid=1;Data.prototype={register:function register(owner,initial){var value=initial||{}; // If it is a node unlikely to be stringify-ed or looped over
	// use plain assignment
	if(owner.nodeType){owner[this.expando]=value; // Otherwise secure it in a non-enumerable, non-writable property
	// configurability must be true to allow the property to be
	// deleted with the delete operator
	}else {Object.defineProperty(owner,this.expando,{value:value,writable:true,configurable:true});}return owner[this.expando];},cache:function cache(owner){ // We can accept data for non-element nodes in modern browsers,
	// but we should not, see #8335.
	// Always return an empty object.
	if(!acceptData(owner)){return {};} // Check if the owner object already has a cache
	var value=owner[this.expando]; // If not, create one
	if(!value){value={}; // We can accept data for non-element nodes in modern browsers,
	// but we should not, see #8335.
	// Always return an empty object.
	if(acceptData(owner)){ // If it is a node unlikely to be stringify-ed or looped over
	// use plain assignment
	if(owner.nodeType){owner[this.expando]=value; // Otherwise secure it in a non-enumerable property
	// configurable must be true to allow the property to be
	// deleted when data is removed
	}else {Object.defineProperty(owner,this.expando,{value:value,configurable:true});}}}return value;},set:function set(owner,data,value){var prop,cache=this.cache(owner); // Handle: [ owner, key, value ] args
	if(typeof data==="string"){cache[data]=value; // Handle: [ owner, { properties } ] args
	}else { // Copy the properties one-by-one to the cache object
	for(prop in data){cache[prop]=data[prop];}}return cache;},get:function get(owner,key){return key===undefined?this.cache(owner):owner[this.expando]&&owner[this.expando][key];},access:function access(owner,key,value){var stored; // In cases where either:
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
	if(key===undefined||key&&typeof key==="string"&&value===undefined){stored=this.get(owner,key);return stored!==undefined?stored:this.get(owner,jQuery.camelCase(key));} // When the key is not a string, or both a key and value
	// are specified, set or extend (existing objects) with either:
	//
	//   1. An object of properties
	//   2. A key and value
	//
	this.set(owner,key,value); // Since the "set" path can have two possible entry points
	// return the expected data based on which path was taken[*]
	return value!==undefined?value:key;},remove:function remove(owner,key){var i,name,camel,cache=owner[this.expando];if(cache===undefined){return;}if(key===undefined){this.register(owner);}else { // Support array or space separated string of keys
	if(jQuery.isArray(key)){ // If "name" is an array of keys...
	// When data is initially created, via ("key", "val") signature,
	// keys will be converted to camelCase.
	// Since there is no way to tell _how_ a key was added, remove
	// both plain key and camelCase key. #12786
	// This will only penalize the array argument path.
	name=key.concat(key.map(jQuery.camelCase));}else {camel=jQuery.camelCase(key); // Try the string as a key before any manipulation
	if(key in cache){name=[key,camel];}else { // If a key with the spaces exists, use it.
	// Otherwise, create an array by matching non-whitespace
	name=camel;name=name in cache?[name]:name.match(rnotwhite)||[];}}i=name.length;while(i--){delete cache[name[i]];}} // Remove the expando if there's no more data
	if(key===undefined||jQuery.isEmptyObject(cache)){ // Support: Chrome <= 35-45+
	// Webkit & Blink performance suffers when deleting properties
	// from DOM nodes, so set to undefined instead
	// https://code.google.com/p/chromium/issues/detail?id=378607
	if(owner.nodeType){owner[this.expando]=undefined;}else {delete owner[this.expando];}}},hasData:function hasData(owner){var cache=owner[this.expando];return cache!==undefined&&!jQuery.isEmptyObject(cache);}};var dataPriv=new Data();var dataUser=new Data(); //	Implementation Summary
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
	if(data===undefined&&elem.nodeType===1){name="data-"+key.replace(rmultiDash,"-$&").toLowerCase();data=elem.getAttribute(name);if(typeof data==="string"){try{data=data==="true"?true:data==="false"?false:data==="null"?null: // Only convert to a number if it doesn't change the string
	+data+""===data?+data:rbrace.test(data)?jQuery.parseJSON(data):data;}catch(e){} // Make sure we set the data so it isn't changed later
	dataUser.set(elem,key,data);}else {data=undefined;}}return data;}jQuery.extend({hasData:function hasData(elem){return dataUser.hasData(elem)||dataPriv.hasData(elem);},data:function data(elem,name,_data){return dataUser.access(elem,name,_data);},removeData:function removeData(elem,name){dataUser.remove(elem,name);}, // TODO: Now that all calls to _data and _removeData have been replaced
	// with direct calls to dataPriv methods, these can be deprecated.
	_data:function _data(elem,name,data){return dataPriv.access(elem,name,data);},_removeData:function _removeData(elem,name){dataPriv.remove(elem,name);}});jQuery.fn.extend({data:function data(key,value){var i,name,data,elem=this[0],attrs=elem&&elem.attributes; // Gets all values
	if(key===undefined){if(this.length){data=dataUser.get(elem);if(elem.nodeType===1&&!dataPriv.get(elem,"hasDataAttrs")){i=attrs.length;while(i--){ // Support: IE11+
	// The attrs elements can be null (#14894)
	if(attrs[i]){name=attrs[i].name;if(name.indexOf("data-")===0){name=jQuery.camelCase(name.slice(5));dataAttr(elem,name,data[name]);}}}dataPriv.set(elem,"hasDataAttrs",true);}}return data;} // Sets multiple values
	if((typeof key==="undefined"?"undefined":_typeof(key))==="object"){return this.each(function(){dataUser.set(this,key);});}return access(this,function(value){var data,camelKey; // The calling jQuery object (element matches) is not empty
	// (and therefore has an element appears at this[ 0 ]) and the
	// `value` parameter was not undefined. An empty jQuery object
	// will result in `undefined` for elem = this[ 0 ] which will
	// throw an exception if an attempt to read a data cache is made.
	if(elem&&value===undefined){ // Attempt to get data from the cache
	// with the key as-is
	data=dataUser.get(elem,key)|| // Try to find dashed key if it exists (gh-2779)
	// This is for 2.2.x only
	dataUser.get(elem,key.replace(rmultiDash,"-$&").toLowerCase());if(data!==undefined){return data;}camelKey=jQuery.camelCase(key); // Attempt to get data from the cache
	// with the key camelized
	data=dataUser.get(elem,camelKey);if(data!==undefined){return data;} // Attempt to "discover" the data in
	// HTML5 custom data-* attrs
	data=dataAttr(elem,camelKey,undefined);if(data!==undefined){return data;} // We tried really hard, but the data doesn't exist.
	return;} // Set the data...
	camelKey=jQuery.camelCase(key);this.each(function(){ // First, attempt to store a copy or reference of any
	// data that might've been store with a camelCased key.
	var data=dataUser.get(this,camelKey); // For HTML5 data-* attribute interop, we have to
	// store property names with dashes in a camelCase form.
	// This might not apply to all properties...*
	dataUser.set(this,camelKey,value); // *... In the case of properties that might _actually_
	// have dashes, we need to also store a copy of that
	// unchanged property.
	if(key.indexOf("-")>-1&&data!==undefined){dataUser.set(this,key,value);}});},null,value,arguments.length>1,null,true);},removeData:function removeData(key){return this.each(function(){dataUser.remove(this,key);});}});jQuery.extend({queue:function queue(elem,type,data){var queue;if(elem){type=(type||"fx")+"queue";queue=dataPriv.get(elem,type); // Speed up dequeue by getting out quickly if this is just a lookup
	if(data){if(!queue||jQuery.isArray(data)){queue=dataPriv.access(elem,type,jQuery.makeArray(data));}else {queue.push(data);}}return queue||[];}},dequeue:function dequeue(elem,type){type=type||"fx";var queue=jQuery.queue(elem,type),startLength=queue.length,fn=queue.shift(),hooks=jQuery._queueHooks(elem,type),next=function next(){jQuery.dequeue(elem,type);}; // If the fx queue is dequeued, always remove the progress sentinel
	if(fn==="inprogress"){fn=queue.shift();startLength--;}if(fn){ // Add a progress sentinel to prevent the fx queue from being
	// automatically dequeued
	if(type==="fx"){queue.unshift("inprogress");} // Clear up the last queue stop function
	delete hooks.stop;fn.call(elem,next,hooks);}if(!startLength&&hooks){hooks.empty.fire();}}, // Not public - generate a queueHooks object, or return the current one
	_queueHooks:function _queueHooks(elem,type){var key=type+"queueHooks";return dataPriv.get(elem,key)||dataPriv.access(elem,key,{empty:jQuery.Callbacks("once memory").add(function(){dataPriv.remove(elem,[type+"queue",key]);})});}});jQuery.fn.extend({queue:function queue(type,data){var setter=2;if(typeof type!=="string"){data=type;type="fx";setter--;}if(arguments.length<setter){return jQuery.queue(this[0],type);}return data===undefined?this:this.each(function(){var queue=jQuery.queue(this,type,data); // Ensure a hooks for this queue
	jQuery._queueHooks(this,type);if(type==="fx"&&queue[0]!=="inprogress"){jQuery.dequeue(this,type);}});},dequeue:function dequeue(type){return this.each(function(){jQuery.dequeue(this,type);});},clearQueue:function clearQueue(type){return this.queue(type||"fx",[]);}, // Get a promise resolved when queues of a certain type
	// are emptied (fx is the type by default)
	promise:function promise(type,obj){var tmp,count=1,defer=jQuery.Deferred(),elements=this,i=this.length,resolve=function resolve(){if(! --count){defer.resolveWith(elements,[elements]);}};if(typeof type!=="string"){obj=type;type=undefined;}type=type||"fx";while(i--){tmp=dataPriv.get(elements[i],type+"queueHooks");if(tmp&&tmp.empty){count++;tmp.empty.add(resolve);}}resolve();return defer.promise(obj);}});var pnum=/[+-]?(?:\d*\.|)\d+(?:[eE][+-]?\d+|)/.source;var rcssNum=new RegExp("^(?:([+-])=|)("+pnum+")([a-z%]*)$","i");var cssExpand=["Top","Right","Bottom","Left"];var isHidden=function isHidden(elem,el){ // isHidden might be called from jQuery#filter function;
	// in that case, element will be second argument
	elem=el||elem;return jQuery.css(elem,"display")==="none"||!jQuery.contains(elem.ownerDocument,elem);};function adjustCSS(elem,prop,valueParts,tween){var adjusted,scale=1,maxIterations=20,currentValue=tween?function(){return tween.cur();}:function(){return jQuery.css(elem,prop,"");},initial=currentValue(),unit=valueParts&&valueParts[3]||(jQuery.cssNumber[prop]?"":"px"), // Starting value computation is required for potential unit mismatches
	initialInUnit=(jQuery.cssNumber[prop]||unit!=="px"&&+initial)&&rcssNum.exec(jQuery.css(elem,prop));if(initialInUnit&&initialInUnit[3]!==unit){ // Trust units reported by jQuery.css
	unit=unit||initialInUnit[3]; // Make sure we update the tween properties later on
	valueParts=valueParts||[]; // Iteratively approximate from a nonzero starting point
	initialInUnit=+initial||1;do { // If previous iteration zeroed out, double until we get *something*.
	// Use string for doubling so we don't accidentally see scale as unchanged below
	scale=scale||".5"; // Adjust and apply
	initialInUnit=initialInUnit/scale;jQuery.style(elem,prop,initialInUnit+unit); // Update scale, tolerating zero or NaN from tween.cur()
	// Break the loop if scale is unchanged or perfect, or if we've just had enough.
	}while(scale!==(scale=currentValue()/initial)&&scale!==1&&--maxIterations);}if(valueParts){initialInUnit=+initialInUnit||+initial||0; // Apply relative offset (+=/-=) if specified
	adjusted=valueParts[1]?initialInUnit+(valueParts[1]+1)*valueParts[2]:+valueParts[2];if(tween){tween.unit=unit;tween.start=initialInUnit;tween.end=adjusted;}}return adjusted;}var rcheckableType=/^(?:checkbox|radio)$/i;var rtagName=/<([\w:-]+)/;var rscriptType=/^$|\/(?:java|ecma)script/i; // We have to close these tags to support XHTML (#13200)
	var wrapMap={ // Support: IE9
	option:[1,"<select multiple='multiple'>","</select>"], // XHTML parsers do not magically insert elements in the
	// same way that tag soup parsers do. So we cannot shorten
	// this by omitting <tbody> or other required elements.
	thead:[1,"<table>","</table>"],col:[2,"<table><colgroup>","</colgroup></table>"],tr:[2,"<table><tbody>","</tbody></table>"],td:[3,"<table><tbody><tr>","</tr></tbody></table>"],_default:[0,"",""]}; // Support: IE9
	wrapMap.optgroup=wrapMap.option;wrapMap.tbody=wrapMap.tfoot=wrapMap.colgroup=wrapMap.caption=wrapMap.thead;wrapMap.th=wrapMap.td;function getAll(context,tag){ // Support: IE9-11+
	// Use typeof to avoid zero-argument method invocation on host objects (#15151)
	var ret=typeof context.getElementsByTagName!=="undefined"?context.getElementsByTagName(tag||"*"):typeof context.querySelectorAll!=="undefined"?context.querySelectorAll(tag||"*"):[];return tag===undefined||tag&&jQuery.nodeName(context,tag)?jQuery.merge([context],ret):ret;} // Mark scripts as having already been evaluated
	function setGlobalEval(elems,refElements){var i=0,l=elems.length;for(;i<l;i++){dataPriv.set(elems[i],"globalEval",!refElements||dataPriv.get(refElements[i],"globalEval"));}}var rhtml=/<|&#?\w+;/;function buildFragment(elems,context,scripts,selection,ignored){var elem,tmp,tag,wrap,contains,j,fragment=context.createDocumentFragment(),nodes=[],i=0,l=elems.length;for(;i<l;i++){elem=elems[i];if(elem||elem===0){ // Add nodes directly
	if(jQuery.type(elem)==="object"){ // Support: Android<4.1, PhantomJS<2
	// push.apply(_, arraylike) throws on ancient WebKit
	jQuery.merge(nodes,elem.nodeType?[elem]:elem); // Convert non-html into a text node
	}else if(!rhtml.test(elem)){nodes.push(context.createTextNode(elem)); // Convert html into DOM nodes
	}else {tmp=tmp||fragment.appendChild(context.createElement("div")); // Deserialize a standard representation
	tag=(rtagName.exec(elem)||["",""])[1].toLowerCase();wrap=wrapMap[tag]||wrapMap._default;tmp.innerHTML=wrap[1]+jQuery.htmlPrefilter(elem)+wrap[2]; // Descend through wrappers to the right content
	j=wrap[0];while(j--){tmp=tmp.lastChild;} // Support: Android<4.1, PhantomJS<2
	// push.apply(_, arraylike) throws on ancient WebKit
	jQuery.merge(nodes,tmp.childNodes); // Remember the top-level container
	tmp=fragment.firstChild; // Ensure the created nodes are orphaned (#12392)
	tmp.textContent="";}}} // Remove wrapper from fragment
	fragment.textContent="";i=0;while(elem=nodes[i++]){ // Skip elements already in the context collection (trac-4087)
	if(selection&&jQuery.inArray(elem,selection)>-1){if(ignored){ignored.push(elem);}continue;}contains=jQuery.contains(elem.ownerDocument,elem); // Append to fragment
	tmp=getAll(fragment.appendChild(elem),"script"); // Preserve script evaluation history
	if(contains){setGlobalEval(tmp);} // Capture executables
	if(scripts){j=0;while(elem=tmp[j++]){if(rscriptType.test(elem.type||"")){scripts.push(elem);}}}}return fragment;}(function(){var fragment=document.createDocumentFragment(),div=fragment.appendChild(document.createElement("div")),input=document.createElement("input"); // Support: Android 4.0-4.3, Safari<=5.1
	// Check state lost if the name is set (#11217)
	// Support: Windows Web Apps (WWA)
	// `name` and `type` must use .setAttribute for WWA (#14901)
	input.setAttribute("type","radio");input.setAttribute("checked","checked");input.setAttribute("name","t");div.appendChild(input); // Support: Safari<=5.1, Android<4.2
	// Older WebKit doesn't clone checked state correctly in fragments
	support.checkClone=div.cloneNode(true).cloneNode(true).lastChild.checked; // Support: IE<=11+
	// Make sure textarea (and checkbox) defaultValue is properly cloned
	div.innerHTML="<textarea>x</textarea>";support.noCloneChecked=!!div.cloneNode(true).lastChild.defaultValue;})();var rkeyEvent=/^key/,rmouseEvent=/^(?:mouse|pointer|contextmenu|drag|drop)|click/,rtypenamespace=/^([^.]*)(?:\.(.+)|)/;function returnTrue(){return true;}function returnFalse(){return false;} // Support: IE9
	// See #13393 for more info
	function safeActiveElement(){try{return document.activeElement;}catch(err){}}function _on(elem,types,selector,data,fn,one){var origFn,type; // Types can be a map of types/handlers
	if((typeof types==="undefined"?"undefined":_typeof(types))==="object"){ // ( types-Object, selector, data )
	if(typeof selector!=="string"){ // ( types-Object, data )
	data=data||selector;selector=undefined;}for(type in types){_on(elem,type,selector,data,types[type],one);}return elem;}if(data==null&&fn==null){ // ( types, fn )
	fn=selector;data=selector=undefined;}else if(fn==null){if(typeof selector==="string"){ // ( types, selector, fn )
	fn=data;data=undefined;}else { // ( types, data, fn )
	fn=data;data=selector;selector=undefined;}}if(fn===false){fn=returnFalse;}else if(!fn){return this;}if(one===1){origFn=fn;fn=function fn(event){ // Can use an empty set, since event contains the info
	jQuery().off(event);return origFn.apply(this,arguments);}; // Use same guid so caller can remove using origFn
	fn.guid=origFn.guid||(origFn.guid=jQuery.guid++);}return elem.each(function(){jQuery.event.add(this,types,fn,data,selector);});} /*
	 * Helper functions for managing events -- not part of the public interface.
	 * Props to Dean Edwards' addEvent library for many of the ideas.
	 */jQuery.event={global:{},add:function add(elem,types,handler,data,selector){var handleObjIn,eventHandle,tmp,events,t,handleObj,special,handlers,type,namespaces,origType,elemData=dataPriv.get(elem); // Don't attach events to noData or text/comment nodes (but allow plain objects)
	if(!elemData){return;} // Caller can pass in an object of custom data in lieu of the handler
	if(handler.handler){handleObjIn=handler;handler=handleObjIn.handler;selector=handleObjIn.selector;} // Make sure that the handler has a unique ID, used to find/remove it later
	if(!handler.guid){handler.guid=jQuery.guid++;} // Init the element's event structure and main handler, if this is the first
	if(!(events=elemData.events)){events=elemData.events={};}if(!(eventHandle=elemData.handle)){eventHandle=elemData.handle=function(e){ // Discard the second event of a jQuery.event.trigger() and
	// when an event is called after a page has unloaded
	return typeof jQuery!=="undefined"&&jQuery.event.triggered!==e.type?jQuery.event.dispatch.apply(elem,arguments):undefined;};} // Handle multiple events separated by a space
	types=(types||"").match(rnotwhite)||[""];t=types.length;while(t--){tmp=rtypenamespace.exec(types[t])||[];type=origType=tmp[1];namespaces=(tmp[2]||"").split(".").sort(); // There *must* be a type, no attaching namespace-only handlers
	if(!type){continue;} // If event changes its type, use the special event handlers for the changed type
	special=jQuery.event.special[type]||{}; // If selector defined, determine special event api type, otherwise given type
	type=(selector?special.delegateType:special.bindType)||type; // Update special based on newly reset type
	special=jQuery.event.special[type]||{}; // handleObj is passed to all event handlers
	handleObj=jQuery.extend({type:type,origType:origType,data:data,handler:handler,guid:handler.guid,selector:selector,needsContext:selector&&jQuery.expr.match.needsContext.test(selector),namespace:namespaces.join(".")},handleObjIn); // Init the event handler queue if we're the first
	if(!(handlers=events[type])){handlers=events[type]=[];handlers.delegateCount=0; // Only use addEventListener if the special events handler returns false
	if(!special.setup||special.setup.call(elem,data,namespaces,eventHandle)===false){if(elem.addEventListener){elem.addEventListener(type,eventHandle);}}}if(special.add){special.add.call(elem,handleObj);if(!handleObj.handler.guid){handleObj.handler.guid=handler.guid;}} // Add to the element's handler list, delegates in front
	if(selector){handlers.splice(handlers.delegateCount++,0,handleObj);}else {handlers.push(handleObj);} // Keep track of which events have ever been used, for event optimization
	jQuery.event.global[type]=true;}}, // Detach an event or set of events from an element
	remove:function remove(elem,types,handler,selector,mappedTypes){var j,origCount,tmp,events,t,handleObj,special,handlers,type,namespaces,origType,elemData=dataPriv.hasData(elem)&&dataPriv.get(elem);if(!elemData||!(events=elemData.events)){return;} // Once for each type.namespace in types; type may be omitted
	types=(types||"").match(rnotwhite)||[""];t=types.length;while(t--){tmp=rtypenamespace.exec(types[t])||[];type=origType=tmp[1];namespaces=(tmp[2]||"").split(".").sort(); // Unbind all events (on this namespace, if provided) for the element
	if(!type){for(type in events){jQuery.event.remove(elem,type+types[t],handler,selector,true);}continue;}special=jQuery.event.special[type]||{};type=(selector?special.delegateType:special.bindType)||type;handlers=events[type]||[];tmp=tmp[2]&&new RegExp("(^|\\.)"+namespaces.join("\\.(?:.*\\.|)")+"(\\.|$)"); // Remove matching events
	origCount=j=handlers.length;while(j--){handleObj=handlers[j];if((mappedTypes||origType===handleObj.origType)&&(!handler||handler.guid===handleObj.guid)&&(!tmp||tmp.test(handleObj.namespace))&&(!selector||selector===handleObj.selector||selector==="**"&&handleObj.selector)){handlers.splice(j,1);if(handleObj.selector){handlers.delegateCount--;}if(special.remove){special.remove.call(elem,handleObj);}}} // Remove generic event handler if we removed something and no more handlers exist
	// (avoids potential for endless recursion during removal of special event handlers)
	if(origCount&&!handlers.length){if(!special.teardown||special.teardown.call(elem,namespaces,elemData.handle)===false){jQuery.removeEvent(elem,type,elemData.handle);}delete events[type];}} // Remove data and the expando if it's no longer used
	if(jQuery.isEmptyObject(events)){dataPriv.remove(elem,"handle events");}},dispatch:function dispatch(event){ // Make a writable jQuery.Event from the native event object
	event=jQuery.event.fix(event);var i,j,ret,matched,handleObj,handlerQueue=[],args=_slice.call(arguments),handlers=(dataPriv.get(this,"events")||{})[event.type]||[],special=jQuery.event.special[event.type]||{}; // Use the fix-ed jQuery.Event rather than the (read-only) native event
	args[0]=event;event.delegateTarget=this; // Call the preDispatch hook for the mapped type, and let it bail if desired
	if(special.preDispatch&&special.preDispatch.call(this,event)===false){return;} // Determine handlers
	handlerQueue=jQuery.event.handlers.call(this,event,handlers); // Run delegates first; they may want to stop propagation beneath us
	i=0;while((matched=handlerQueue[i++])&&!event.isPropagationStopped()){event.currentTarget=matched.elem;j=0;while((handleObj=matched.handlers[j++])&&!event.isImmediatePropagationStopped()){ // Triggered event must either 1) have no namespace, or 2) have namespace(s)
	// a subset or equal to those in the bound event (both can have no namespace).
	if(!event.rnamespace||event.rnamespace.test(handleObj.namespace)){event.handleObj=handleObj;event.data=handleObj.data;ret=((jQuery.event.special[handleObj.origType]||{}).handle||handleObj.handler).apply(matched.elem,args);if(ret!==undefined){if((event.result=ret)===false){event.preventDefault();event.stopPropagation();}}}}} // Call the postDispatch hook for the mapped type
	if(special.postDispatch){special.postDispatch.call(this,event);}return event.result;},handlers:function handlers(event,_handlers){var i,matches,sel,handleObj,handlerQueue=[],delegateCount=_handlers.delegateCount,cur=event.target; // Support (at least): Chrome, IE9
	// Find delegate handlers
	// Black-hole SVG <use> instance trees (#13180)
	//
	// Support: Firefox<=42+
	// Avoid non-left-click in FF but don't block IE radio events (#3861, gh-2343)
	if(delegateCount&&cur.nodeType&&(event.type!=="click"||isNaN(event.button)||event.button<1)){for(;cur!==this;cur=cur.parentNode||this){ // Don't check non-elements (#13208)
	// Don't process clicks on disabled elements (#6911, #8165, #11382, #11764)
	if(cur.nodeType===1&&(cur.disabled!==true||event.type!=="click")){matches=[];for(i=0;i<delegateCount;i++){handleObj=_handlers[i]; // Don't conflict with Object.prototype properties (#13203)
	sel=handleObj.selector+" ";if(matches[sel]===undefined){matches[sel]=handleObj.needsContext?jQuery(sel,this).index(cur)>-1:jQuery.find(sel,this,null,[cur]).length;}if(matches[sel]){matches.push(handleObj);}}if(matches.length){handlerQueue.push({elem:cur,handlers:matches});}}}} // Add the remaining (directly-bound) handlers
	if(delegateCount<_handlers.length){handlerQueue.push({elem:this,handlers:_handlers.slice(delegateCount)});}return handlerQueue;}, // Includes some event props shared by KeyEvent and MouseEvent
	props:("altKey bubbles cancelable ctrlKey currentTarget detail eventPhase "+"metaKey relatedTarget shiftKey target timeStamp view which").split(" "),fixHooks:{},keyHooks:{props:"char charCode key keyCode".split(" "),filter:function filter(event,original){ // Add which for key events
	if(event.which==null){event.which=original.charCode!=null?original.charCode:original.keyCode;}return event;}},mouseHooks:{props:("button buttons clientX clientY offsetX offsetY pageX pageY "+"screenX screenY toElement").split(" "),filter:function filter(event,original){var eventDoc,doc,body,button=original.button; // Calculate pageX/Y if missing and clientX/Y available
	if(event.pageX==null&&original.clientX!=null){eventDoc=event.target.ownerDocument||document;doc=eventDoc.documentElement;body=eventDoc.body;event.pageX=original.clientX+(doc&&doc.scrollLeft||body&&body.scrollLeft||0)-(doc&&doc.clientLeft||body&&body.clientLeft||0);event.pageY=original.clientY+(doc&&doc.scrollTop||body&&body.scrollTop||0)-(doc&&doc.clientTop||body&&body.clientTop||0);} // Add which for click: 1 === left; 2 === middle; 3 === right
	// Note: button is not normalized, so don't use it
	if(!event.which&&button!==undefined){event.which=button&1?1:button&2?3:button&4?2:0;}return event;}},fix:function fix(event){if(event[jQuery.expando]){return event;} // Create a writable copy of the event object and normalize some properties
	var i,prop,copy,type=event.type,originalEvent=event,fixHook=this.fixHooks[type];if(!fixHook){this.fixHooks[type]=fixHook=rmouseEvent.test(type)?this.mouseHooks:rkeyEvent.test(type)?this.keyHooks:{};}copy=fixHook.props?this.props.concat(fixHook.props):this.props;event=new jQuery.Event(originalEvent);i=copy.length;while(i--){prop=copy[i];event[prop]=originalEvent[prop];} // Support: Cordova 2.5 (WebKit) (#13255)
	// All events should have a target; Cordova deviceready doesn't
	if(!event.target){event.target=document;} // Support: Safari 6.0+, Chrome<28
	// Target should not be a text node (#504, #13143)
	if(event.target.nodeType===3){event.target=event.target.parentNode;}return fixHook.filter?fixHook.filter(event,originalEvent):event;},special:{load:{ // Prevent triggered image.load events from bubbling to window.load
	noBubble:true},focus:{ // Fire native event if possible so blur/focus sequence is correct
	trigger:function trigger(){if(this!==safeActiveElement()&&this.focus){this.focus();return false;}},delegateType:"focusin"},blur:{trigger:function trigger(){if(this===safeActiveElement()&&this.blur){this.blur();return false;}},delegateType:"focusout"},click:{ // For checkbox, fire native event so checked state will be right
	trigger:function trigger(){if(this.type==="checkbox"&&this.click&&jQuery.nodeName(this,"input")){this.click();return false;}}, // For cross-browser consistency, don't fire native .click() on links
	_default:function _default(event){return jQuery.nodeName(event.target,"a");}},beforeunload:{postDispatch:function postDispatch(event){ // Support: Firefox 20+
	// Firefox doesn't alert if the returnValue field is not set.
	if(event.result!==undefined&&event.originalEvent){event.originalEvent.returnValue=event.result;}}}}};jQuery.removeEvent=function(elem,type,handle){ // This "if" is needed for plain objects
	if(elem.removeEventListener){elem.removeEventListener(type,handle);}};jQuery.Event=function(src,props){ // Allow instantiation without the 'new' keyword
	if(!(this instanceof jQuery.Event)){return new jQuery.Event(src,props);} // Event object
	if(src&&src.type){this.originalEvent=src;this.type=src.type; // Events bubbling up the document may have been marked as prevented
	// by a handler lower down the tree; reflect the correct value.
	this.isDefaultPrevented=src.defaultPrevented||src.defaultPrevented===undefined&& // Support: Android<4.0
	src.returnValue===false?returnTrue:returnFalse; // Event type
	}else {this.type=src;} // Put explicitly provided properties onto the event object
	if(props){jQuery.extend(this,props);} // Create a timestamp if incoming event doesn't have one
	this.timeStamp=src&&src.timeStamp||jQuery.now(); // Mark it as fixed
	this[jQuery.expando]=true;}; // jQuery.Event is based on DOM3 Events as specified by the ECMAScript Language Binding
	// http://www.w3.org/TR/2003/WD-DOM-Level-3-Events-20030331/ecma-script-binding.html
	jQuery.Event.prototype={constructor:jQuery.Event,isDefaultPrevented:returnFalse,isPropagationStopped:returnFalse,isImmediatePropagationStopped:returnFalse,preventDefault:function preventDefault(){var e=this.originalEvent;this.isDefaultPrevented=returnTrue;if(e){e.preventDefault();}},stopPropagation:function stopPropagation(){var e=this.originalEvent;this.isPropagationStopped=returnTrue;if(e){e.stopPropagation();}},stopImmediatePropagation:function stopImmediatePropagation(){var e=this.originalEvent;this.isImmediatePropagationStopped=returnTrue;if(e){e.stopImmediatePropagation();}this.stopPropagation();}}; // Create mouseenter/leave events using mouseover/out and event-time checks
	// so that event delegation works in jQuery.
	// Do the same for pointerenter/pointerleave and pointerover/pointerout
	//
	// Support: Safari 7 only
	// Safari sends mouseenter too often; see:
	// https://code.google.com/p/chromium/issues/detail?id=470258
	// for the description of the bug (it existed in older Chrome versions as well).
	jQuery.each({mouseenter:"mouseover",mouseleave:"mouseout",pointerenter:"pointerover",pointerleave:"pointerout"},function(orig,fix){jQuery.event.special[orig]={delegateType:fix,bindType:fix,handle:function handle(event){var ret,target=this,related=event.relatedTarget,handleObj=event.handleObj; // For mouseenter/leave call the handler if related is outside the target.
	// NB: No relatedTarget if the mouse left/entered the browser window
	if(!related||related!==target&&!jQuery.contains(target,related)){event.type=handleObj.origType;ret=handleObj.handler.apply(this,arguments);event.type=fix;}return ret;}};});jQuery.fn.extend({on:function on(types,selector,data,fn){return _on(this,types,selector,data,fn);},one:function one(types,selector,data,fn){return _on(this,types,selector,data,fn,1);},off:function off(types,selector,fn){var handleObj,type;if(types&&types.preventDefault&&types.handleObj){ // ( event )  dispatched jQuery.Event
	handleObj=types.handleObj;jQuery(types.delegateTarget).off(handleObj.namespace?handleObj.origType+"."+handleObj.namespace:handleObj.origType,handleObj.selector,handleObj.handler);return this;}if((typeof types==="undefined"?"undefined":_typeof(types))==="object"){ // ( types-object [, selector] )
	for(type in types){this.off(type,selector,types[type]);}return this;}if(selector===false||typeof selector==="function"){ // ( types [, fn] )
	fn=selector;selector=undefined;}if(fn===false){fn=returnFalse;}return this.each(function(){jQuery.event.remove(this,types,fn,selector);});}});var rxhtmlTag=/<(?!area|br|col|embed|hr|img|input|link|meta|param)(([\w:-]+)[^>]*)\/>/gi, // Support: IE 10-11, Edge 10240+
	// In IE/Edge using regex groups here causes severe slowdowns.
	// See https://connect.microsoft.com/IE/feedback/details/1736512/
	rnoInnerhtml=/<script|<style|<link/i, // checked="checked" or checked
	rchecked=/checked\s*(?:[^=]|=\s*.checked.)/i,rscriptTypeMasked=/^true\/(.*)/,rcleanScript=/^\s*<!(?:\[CDATA\[|--)|(?:\]\]|--)>\s*$/g;function manipulationTarget(elem,content){if(jQuery.nodeName(elem,"table")&&jQuery.nodeName(content.nodeType!==11?content:content.firstChild,"tr")){return elem.getElementsByTagName("tbody")[0]||elem;}return elem;} // Replace/restore the type attribute of script elements for safe DOM manipulation
	function disableScript(elem){elem.type=(elem.getAttribute("type")!==null)+"/"+elem.type;return elem;}function restoreScript(elem){var match=rscriptTypeMasked.exec(elem.type);if(match){elem.type=match[1];}else {elem.removeAttribute("type");}return elem;}function cloneCopyEvent(src,dest){var i,l,type,pdataOld,pdataCur,udataOld,udataCur,events;if(dest.nodeType!==1){return;} // 1. Copy private data: events, handlers, etc.
	if(dataPriv.hasData(src)){pdataOld=dataPriv.access(src);pdataCur=dataPriv.set(dest,pdataOld);events=pdataOld.events;if(events){delete pdataCur.handle;pdataCur.events={};for(type in events){for(i=0,l=events[type].length;i<l;i++){jQuery.event.add(dest,type,events[type][i]);}}}} // 2. Copy user data
	if(dataUser.hasData(src)){udataOld=dataUser.access(src);udataCur=jQuery.extend({},udataOld);dataUser.set(dest,udataCur);}} // Fix IE bugs, see support tests
	function fixInput(src,dest){var nodeName=dest.nodeName.toLowerCase(); // Fails to persist the checked state of a cloned checkbox or radio button.
	if(nodeName==="input"&&rcheckableType.test(src.type)){dest.checked=src.checked; // Fails to return the selected option to the default selected state when cloning options
	}else if(nodeName==="input"||nodeName==="textarea"){dest.defaultValue=src.defaultValue;}}function domManip(collection,args,callback,ignored){ // Flatten any nested arrays
	args=concat.apply([],args);var fragment,first,scripts,hasScripts,node,doc,i=0,l=collection.length,iNoClone=l-1,value=args[0],isFunction=jQuery.isFunction(value); // We can't cloneNode fragments that contain checked, in WebKit
	if(isFunction||l>1&&typeof value==="string"&&!support.checkClone&&rchecked.test(value)){return collection.each(function(index){var self=collection.eq(index);if(isFunction){args[0]=value.call(this,index,self.html());}domManip(self,args,callback,ignored);});}if(l){fragment=buildFragment(args,collection[0].ownerDocument,false,collection,ignored);first=fragment.firstChild;if(fragment.childNodes.length===1){fragment=first;} // Require either new content or an interest in ignored elements to invoke the callback
	if(first||ignored){scripts=jQuery.map(getAll(fragment,"script"),disableScript);hasScripts=scripts.length; // Use the original fragment for the last item
	// instead of the first because it can end up
	// being emptied incorrectly in certain situations (#8070).
	for(;i<l;i++){node=fragment;if(i!==iNoClone){node=jQuery.clone(node,true,true); // Keep references to cloned scripts for later restoration
	if(hasScripts){ // Support: Android<4.1, PhantomJS<2
	// push.apply(_, arraylike) throws on ancient WebKit
	jQuery.merge(scripts,getAll(node,"script"));}}callback.call(collection[i],node,i);}if(hasScripts){doc=scripts[scripts.length-1].ownerDocument; // Reenable scripts
	jQuery.map(scripts,restoreScript); // Evaluate executable scripts on first document insertion
	for(i=0;i<hasScripts;i++){node=scripts[i];if(rscriptType.test(node.type||"")&&!dataPriv.access(node,"globalEval")&&jQuery.contains(doc,node)){if(node.src){ // Optional AJAX dependency, but won't run scripts if not present
	if(jQuery._evalUrl){jQuery._evalUrl(node.src);}}else {jQuery.globalEval(node.textContent.replace(rcleanScript,""));}}}}}}return collection;}function _remove(elem,selector,keepData){var node,nodes=selector?jQuery.filter(selector,elem):elem,i=0;for(;(node=nodes[i])!=null;i++){if(!keepData&&node.nodeType===1){jQuery.cleanData(getAll(node));}if(node.parentNode){if(keepData&&jQuery.contains(node.ownerDocument,node)){setGlobalEval(getAll(node,"script"));}node.parentNode.removeChild(node);}}return elem;}jQuery.extend({htmlPrefilter:function htmlPrefilter(html){return html.replace(rxhtmlTag,"<$1></$2>");},clone:function clone(elem,dataAndEvents,deepDataAndEvents){var i,l,srcElements,destElements,clone=elem.cloneNode(true),inPage=jQuery.contains(elem.ownerDocument,elem); // Fix IE cloning issues
	if(!support.noCloneChecked&&(elem.nodeType===1||elem.nodeType===11)&&!jQuery.isXMLDoc(elem)){ // We eschew Sizzle here for performance reasons: http://jsperf.com/getall-vs-sizzle/2
	destElements=getAll(clone);srcElements=getAll(elem);for(i=0,l=srcElements.length;i<l;i++){fixInput(srcElements[i],destElements[i]);}} // Copy the events from the original to the clone
	if(dataAndEvents){if(deepDataAndEvents){srcElements=srcElements||getAll(elem);destElements=destElements||getAll(clone);for(i=0,l=srcElements.length;i<l;i++){cloneCopyEvent(srcElements[i],destElements[i]);}}else {cloneCopyEvent(elem,clone);}} // Preserve script evaluation history
	destElements=getAll(clone,"script");if(destElements.length>0){setGlobalEval(destElements,!inPage&&getAll(elem,"script"));} // Return the cloned set
	return clone;},cleanData:function cleanData(elems){var data,elem,type,special=jQuery.event.special,i=0;for(;(elem=elems[i])!==undefined;i++){if(acceptData(elem)){if(data=elem[dataPriv.expando]){if(data.events){for(type in data.events){if(special[type]){jQuery.event.remove(elem,type); // This is a shortcut to avoid jQuery.event.remove's overhead
	}else {jQuery.removeEvent(elem,type,data.handle);}}} // Support: Chrome <= 35-45+
	// Assign undefined instead of using delete, see Data#remove
	elem[dataPriv.expando]=undefined;}if(elem[dataUser.expando]){ // Support: Chrome <= 35-45+
	// Assign undefined instead of using delete, see Data#remove
	elem[dataUser.expando]=undefined;}}}}});jQuery.fn.extend({ // Keep domManip exposed until 3.0 (gh-2225)
	domManip:domManip,detach:function detach(selector){return _remove(this,selector,true);},remove:function remove(selector){return _remove(this,selector);},text:function text(value){return access(this,function(value){return value===undefined?jQuery.text(this):this.empty().each(function(){if(this.nodeType===1||this.nodeType===11||this.nodeType===9){this.textContent=value;}});},null,value,arguments.length);},append:function append(){return domManip(this,arguments,function(elem){if(this.nodeType===1||this.nodeType===11||this.nodeType===9){var target=manipulationTarget(this,elem);target.appendChild(elem);}});},prepend:function prepend(){return domManip(this,arguments,function(elem){if(this.nodeType===1||this.nodeType===11||this.nodeType===9){var target=manipulationTarget(this,elem);target.insertBefore(elem,target.firstChild);}});},before:function before(){return domManip(this,arguments,function(elem){if(this.parentNode){this.parentNode.insertBefore(elem,this);}});},after:function after(){return domManip(this,arguments,function(elem){if(this.parentNode){this.parentNode.insertBefore(elem,this.nextSibling);}});},empty:function empty(){var elem,i=0;for(;(elem=this[i])!=null;i++){if(elem.nodeType===1){ // Prevent memory leaks
	jQuery.cleanData(getAll(elem,false)); // Remove any remaining nodes
	elem.textContent="";}}return this;},clone:function clone(dataAndEvents,deepDataAndEvents){dataAndEvents=dataAndEvents==null?false:dataAndEvents;deepDataAndEvents=deepDataAndEvents==null?dataAndEvents:deepDataAndEvents;return this.map(function(){return jQuery.clone(this,dataAndEvents,deepDataAndEvents);});},html:function html(value){return access(this,function(value){var elem=this[0]||{},i=0,l=this.length;if(value===undefined&&elem.nodeType===1){return elem.innerHTML;} // See if we can take a shortcut and just use innerHTML
	if(typeof value==="string"&&!rnoInnerhtml.test(value)&&!wrapMap[(rtagName.exec(value)||["",""])[1].toLowerCase()]){value=jQuery.htmlPrefilter(value);try{for(;i<l;i++){elem=this[i]||{}; // Remove element nodes and prevent memory leaks
	if(elem.nodeType===1){jQuery.cleanData(getAll(elem,false));elem.innerHTML=value;}}elem=0; // If using innerHTML throws an exception, use the fallback method
	}catch(e){}}if(elem){this.empty().append(value);}},null,value,arguments.length);},replaceWith:function replaceWith(){var ignored=[]; // Make the changes, replacing each non-ignored context element with the new content
	return domManip(this,arguments,function(elem){var parent=this.parentNode;if(jQuery.inArray(this,ignored)<0){jQuery.cleanData(getAll(this));if(parent){parent.replaceChild(elem,this);}} // Force callback invocation
	},ignored);}});jQuery.each({appendTo:"append",prependTo:"prepend",insertBefore:"before",insertAfter:"after",replaceAll:"replaceWith"},function(name,original){jQuery.fn[name]=function(selector){var elems,ret=[],insert=jQuery(selector),last=insert.length-1,i=0;for(;i<=last;i++){elems=i===last?this:this.clone(true);jQuery(insert[i])[original](elems); // Support: QtWebKit
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
	 */function defaultDisplay(nodeName){var doc=document,display=elemdisplay[nodeName];if(!display){display=actualDisplay(nodeName,doc); // If the simple way fails, read from inside an iframe
	if(display==="none"||!display){ // Use the already-created iframe if possible
	iframe=(iframe||jQuery("<iframe frameborder='0' width='0' height='0'/>")).appendTo(doc.documentElement); // Always write a new HTML skeleton so Webkit and Firefox don't choke on reuse
	doc=iframe[0].contentDocument; // Support: IE
	doc.write();doc.close();display=actualDisplay(nodeName,doc);iframe.detach();} // Store the correct default display
	elemdisplay[nodeName]=display;}return display;}var rmargin=/^margin/;var rnumnonpx=new RegExp("^("+pnum+")(?!px)[a-z%]+$","i");var getStyles=function getStyles(elem){ // Support: IE<=11+, Firefox<=30+ (#15098, #14150)
	// IE throws on elements created in popups
	// FF meanwhile throws on frame elements through "defaultView.getComputedStyle"
	var view=elem.ownerDocument.defaultView;if(!view.opener){view=window;}return view.getComputedStyle(elem);};var swap=function swap(elem,options,callback,args){var ret,name,old={}; // Remember the old values, and insert the new ones
	for(name in options){old[name]=elem.style[name];elem.style[name]=options[name];}ret=callback.apply(elem,args||[]); // Revert the old values
	for(name in options){elem.style[name]=old[name];}return ret;};var documentElement=document.documentElement;(function(){var pixelPositionVal,boxSizingReliableVal,pixelMarginRightVal,reliableMarginLeftVal,container=document.createElement("div"),div=document.createElement("div"); // Finish early in limited (non-browser) environments
	if(!div.style){return;} // Support: IE9-11+
	// Style of cloned element affects source element cloned (#8908)
	div.style.backgroundClip="content-box";div.cloneNode(true).style.backgroundClip="";support.clearCloneStyle=div.style.backgroundClip==="content-box";container.style.cssText="border:0;width:8px;height:0;top:0;left:-9999px;"+"padding:0;margin-top:1px;position:absolute";container.appendChild(div); // Executing both pixelPosition & boxSizingReliable tests require only one layout
	// so they're executed at the same time to save the second computation.
	function computeStyleTests(){div.style.cssText= // Support: Firefox<29, Android 2.3
	// Vendor-prefix box-sizing
	"-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box;"+"position:relative;display:block;"+"margin:auto;border:1px;padding:1px;"+"top:1%;width:50%";div.innerHTML="";documentElement.appendChild(container);var divStyle=window.getComputedStyle(div);pixelPositionVal=divStyle.top!=="1%";reliableMarginLeftVal=divStyle.marginLeft==="2px";boxSizingReliableVal=divStyle.width==="4px"; // Support: Android 4.0 - 4.3 only
	// Some styles come back with percentage values, even though they shouldn't
	div.style.marginRight="50%";pixelMarginRightVal=divStyle.marginRight==="4px";documentElement.removeChild(container);}jQuery.extend(support,{pixelPosition:function pixelPosition(){ // This test is executed only once but we still do memoizing
	// since we can use the boxSizingReliable pre-computing.
	// No need to check if the test was already performed, though.
	computeStyleTests();return pixelPositionVal;},boxSizingReliable:function boxSizingReliable(){if(boxSizingReliableVal==null){computeStyleTests();}return boxSizingReliableVal;},pixelMarginRight:function pixelMarginRight(){ // Support: Android 4.0-4.3
	// We're checking for boxSizingReliableVal here instead of pixelMarginRightVal
	// since that compresses better and they're computed together anyway.
	if(boxSizingReliableVal==null){computeStyleTests();}return pixelMarginRightVal;},reliableMarginLeft:function reliableMarginLeft(){ // Support: IE <=8 only, Android 4.0 - 4.3 only, Firefox <=3 - 37
	if(boxSizingReliableVal==null){computeStyleTests();}return reliableMarginLeftVal;},reliableMarginRight:function reliableMarginRight(){ // Support: Android 2.3
	// Check if div with explicit width and no margin-right incorrectly
	// gets computed margin-right based on width of container. (#3333)
	// WebKit Bug 13343 - getComputedStyle returns wrong value for margin-right
	// This support function is only executed once so no memoizing is needed.
	var ret,marginDiv=div.appendChild(document.createElement("div")); // Reset CSS: box-sizing; display; margin; border; padding
	marginDiv.style.cssText=div.style.cssText= // Support: Android 2.3
	// Vendor-prefix box-sizing
	"-webkit-box-sizing:content-box;box-sizing:content-box;"+"display:block;margin:0;border:0;padding:0";marginDiv.style.marginRight=marginDiv.style.width="0";div.style.width="1px";documentElement.appendChild(container);ret=!parseFloat(window.getComputedStyle(marginDiv).marginRight);documentElement.removeChild(container);div.removeChild(marginDiv);return ret;}});})();function curCSS(elem,name,computed){var width,minWidth,maxWidth,ret,style=elem.style;computed=computed||getStyles(elem); // Support: IE9
	// getPropertyValue is only needed for .css('filter') (#12537)
	if(computed){ret=computed.getPropertyValue(name)||computed[name];if(ret===""&&!jQuery.contains(elem.ownerDocument,elem)){ret=jQuery.style(elem,name);} // A tribute to the "awesome hack by Dean Edwards"
	// Android Browser returns percentage for some values,
	// but width seems to be reliably pixels.
	// This is against the CSSOM draft spec:
	// http://dev.w3.org/csswg/cssom/#resolved-values
	if(!support.pixelMarginRight()&&rnumnonpx.test(ret)&&rmargin.test(name)){ // Remember the original values
	width=style.width;minWidth=style.minWidth;maxWidth=style.maxWidth; // Put in the new values to get a computed value out
	style.minWidth=style.maxWidth=style.width=ret;ret=computed.width; // Revert the changed values
	style.width=width;style.minWidth=minWidth;style.maxWidth=maxWidth;}}return ret!==undefined? // Support: IE9-11+
	// IE returns zIndex value as an integer.
	ret+"":ret;}function addGetHookIf(conditionFn,hookFn){ // Define the hook, we'll check on the first run if it's really needed.
	return {get:function get(){if(conditionFn()){ // Hook not needed (or it's not possible to use it due
	// to missing dependency), remove it.
	delete this.get;return;} // Hook needed; redefine it so that the support test is not executed again.
	return (this.get=hookFn).apply(this,arguments);}};}var  // Swappable if display is none or starts with table
	// except "table", "table-cell", or "table-caption"
	// See here for display values: https://developer.mozilla.org/en-US/docs/CSS/display
	rdisplayswap=/^(none|table(?!-c[ea]).+)/,cssShow={position:"absolute",visibility:"hidden",display:"block"},cssNormalTransform={letterSpacing:"0",fontWeight:"400"},cssPrefixes=["Webkit","O","Moz","ms"],emptyStyle=document.createElement("div").style; // Return a css property mapped to a potentially vendor prefixed property
	function vendorPropName(name){ // Shortcut for names that are not vendor prefixed
	if(name in emptyStyle){return name;} // Check for vendor prefixed names
	var capName=name[0].toUpperCase()+name.slice(1),i=cssPrefixes.length;while(i--){name=cssPrefixes[i]+capName;if(name in emptyStyle){return name;}}}function setPositiveNumber(elem,value,subtract){ // Any relative (+/-) values have already been
	// normalized at this point
	var matches=rcssNum.exec(value);return matches? // Guard against undefined "subtract", e.g., when used as in cssHooks
	Math.max(0,matches[2]-(subtract||0))+(matches[3]||"px"):value;}function augmentWidthOrHeight(elem,name,extra,isBorderBox,styles){var i=extra===(isBorderBox?"border":"content")? // If we already have the right measurement, avoid augmentation
	4: // Otherwise initialize for horizontal or vertical properties
	name==="width"?1:0,val=0;for(;i<4;i+=2){ // Both box models exclude margin, so add it if we want it
	if(extra==="margin"){val+=jQuery.css(elem,extra+cssExpand[i],true,styles);}if(isBorderBox){ // border-box includes padding, so remove it if we want content
	if(extra==="content"){val-=jQuery.css(elem,"padding"+cssExpand[i],true,styles);} // At this point, extra isn't border nor margin, so remove border
	if(extra!=="margin"){val-=jQuery.css(elem,"border"+cssExpand[i]+"Width",true,styles);}}else { // At this point, extra isn't content, so add padding
	val+=jQuery.css(elem,"padding"+cssExpand[i],true,styles); // At this point, extra isn't content nor padding, so add border
	if(extra!=="padding"){val+=jQuery.css(elem,"border"+cssExpand[i]+"Width",true,styles);}}}return val;}function getWidthOrHeight(elem,name,extra){ // Start with offset property, which is equivalent to the border-box value
	var valueIsBorderBox=true,val=name==="width"?elem.offsetWidth:elem.offsetHeight,styles=getStyles(elem),isBorderBox=jQuery.css(elem,"boxSizing",false,styles)==="border-box"; // Support: IE11 only
	// In IE 11 fullscreen elements inside of an iframe have
	// 100x too small dimensions (gh-1764).
	if(document.msFullscreenElement&&window.top!==window){ // Support: IE11 only
	// Running getBoundingClientRect on a disconnected node
	// in IE throws an error.
	if(elem.getClientRects().length){val=Math.round(elem.getBoundingClientRect()[name]*100);}} // Some non-html elements return undefined for offsetWidth, so check for null/undefined
	// svg - https://bugzilla.mozilla.org/show_bug.cgi?id=649285
	// MathML - https://bugzilla.mozilla.org/show_bug.cgi?id=491668
	if(val<=0||val==null){ // Fall back to computed then uncomputed css if necessary
	val=curCSS(elem,name,styles);if(val<0||val==null){val=elem.style[name];} // Computed unit is not pixels. Stop here and return.
	if(rnumnonpx.test(val)){return val;} // Check for style in case a browser which returns unreliable values
	// for getComputedStyle silently falls back to the reliable elem.style
	valueIsBorderBox=isBorderBox&&(support.boxSizingReliable()||val===elem.style[name]); // Normalize "", auto, and prepare for extra
	val=parseFloat(val)||0;} // Use the active box-sizing model to add/subtract irrelevant styles
	return val+augmentWidthOrHeight(elem,name,extra||(isBorderBox?"border":"content"),valueIsBorderBox,styles)+"px";}function showHide(elements,show){var display,elem,hidden,values=[],index=0,length=elements.length;for(;index<length;index++){elem=elements[index];if(!elem.style){continue;}values[index]=dataPriv.get(elem,"olddisplay");display=elem.style.display;if(show){ // Reset the inline display of this element to learn if it is
	// being hidden by cascaded rules or not
	if(!values[index]&&display==="none"){elem.style.display="";} // Set elements which have been overridden with display: none
	// in a stylesheet to whatever the default browser style is
	// for such an element
	if(elem.style.display===""&&isHidden(elem)){values[index]=dataPriv.access(elem,"olddisplay",defaultDisplay(elem.nodeName));}}else {hidden=isHidden(elem);if(display!=="none"||!hidden){dataPriv.set(elem,"olddisplay",hidden?display:jQuery.css(elem,"display"));}}} // Set the display of most of the elements in a second loop
	// to avoid the constant reflow
	for(index=0;index<length;index++){elem=elements[index];if(!elem.style){continue;}if(!show||elem.style.display==="none"||elem.style.display===""){elem.style.display=show?values[index]||"":"none";}}return elements;}jQuery.extend({ // Add in style property hooks for overriding the default
	// behavior of getting and setting a style property
	cssHooks:{opacity:{get:function get(elem,computed){if(computed){ // We should always get a number back from opacity
	var ret=curCSS(elem,"opacity");return ret===""?"1":ret;}}}}, // Don't automatically add "px" to these possibly-unitless properties
	cssNumber:{"animationIterationCount":true,"columnCount":true,"fillOpacity":true,"flexGrow":true,"flexShrink":true,"fontWeight":true,"lineHeight":true,"opacity":true,"order":true,"orphans":true,"widows":true,"zIndex":true,"zoom":true}, // Add in properties whose names you wish to fix before
	// setting or getting the value
	cssProps:{"float":"cssFloat"}, // Get and set the style property on a DOM Node
	style:function style(elem,name,value,extra){ // Don't set styles on text and comment nodes
	if(!elem||elem.nodeType===3||elem.nodeType===8||!elem.style){return;} // Make sure that we're working with the right name
	var ret,type,hooks,origName=jQuery.camelCase(name),style=elem.style;name=jQuery.cssProps[origName]||(jQuery.cssProps[origName]=vendorPropName(origName)||origName); // Gets hook for the prefixed version, then unprefixed version
	hooks=jQuery.cssHooks[name]||jQuery.cssHooks[origName]; // Check if we're setting a value
	if(value!==undefined){type=typeof value==="undefined"?"undefined":_typeof(value); // Convert "+=" or "-=" to relative numbers (#7345)
	if(type==="string"&&(ret=rcssNum.exec(value))&&ret[1]){value=adjustCSS(elem,name,ret); // Fixes bug #9237
	type="number";} // Make sure that null and NaN values aren't set (#7116)
	if(value==null||value!==value){return;} // If a number was passed in, add the unit (except for certain CSS properties)
	if(type==="number"){value+=ret&&ret[3]||(jQuery.cssNumber[origName]?"":"px");} // Support: IE9-11+
	// background-* props affect original clone's values
	if(!support.clearCloneStyle&&value===""&&name.indexOf("background")===0){style[name]="inherit";} // If a hook was provided, use that value, otherwise just set the specified value
	if(!hooks||!("set" in hooks)||(value=hooks.set(elem,value,extra))!==undefined){style[name]=value;}}else { // If a hook was provided get the non-computed value from there
	if(hooks&&"get" in hooks&&(ret=hooks.get(elem,false,extra))!==undefined){return ret;} // Otherwise just get the value from the style object
	return style[name];}},css:function css(elem,name,extra,styles){var val,num,hooks,origName=jQuery.camelCase(name); // Make sure that we're working with the right name
	name=jQuery.cssProps[origName]||(jQuery.cssProps[origName]=vendorPropName(origName)||origName); // Try prefixed name followed by the unprefixed name
	hooks=jQuery.cssHooks[name]||jQuery.cssHooks[origName]; // If a hook was provided get the computed value from there
	if(hooks&&"get" in hooks){val=hooks.get(elem,true,extra);} // Otherwise, if a way to get the computed value exists, use that
	if(val===undefined){val=curCSS(elem,name,styles);} // Convert "normal" to computed value
	if(val==="normal"&&name in cssNormalTransform){val=cssNormalTransform[name];} // Make numeric if forced or a qualifier was provided and val looks numeric
	if(extra===""||extra){num=parseFloat(val);return extra===true||isFinite(num)?num||0:val;}return val;}});jQuery.each(["height","width"],function(i,name){jQuery.cssHooks[name]={get:function get(elem,computed,extra){if(computed){ // Certain elements can have dimension info if we invisibly show them
	// but it must have a current display style that would benefit
	return rdisplayswap.test(jQuery.css(elem,"display"))&&elem.offsetWidth===0?swap(elem,cssShow,function(){return getWidthOrHeight(elem,name,extra);}):getWidthOrHeight(elem,name,extra);}},set:function set(elem,value,extra){var matches,styles=extra&&getStyles(elem),subtract=extra&&augmentWidthOrHeight(elem,name,extra,jQuery.css(elem,"boxSizing",false,styles)==="border-box",styles); // Convert to pixels if value adjustment is needed
	if(subtract&&(matches=rcssNum.exec(value))&&(matches[3]||"px")!=="px"){elem.style[name]=value;value=jQuery.css(elem,name);}return setPositiveNumber(elem,value,subtract);}};});jQuery.cssHooks.marginLeft=addGetHookIf(support.reliableMarginLeft,function(elem,computed){if(computed){return (parseFloat(curCSS(elem,"marginLeft"))||elem.getBoundingClientRect().left-swap(elem,{marginLeft:0},function(){return elem.getBoundingClientRect().left;}))+"px";}}); // Support: Android 2.3
	jQuery.cssHooks.marginRight=addGetHookIf(support.reliableMarginRight,function(elem,computed){if(computed){return swap(elem,{"display":"inline-block"},curCSS,[elem,"marginRight"]);}}); // These hooks are used by animate to expand properties
	jQuery.each({margin:"",padding:"",border:"Width"},function(prefix,suffix){jQuery.cssHooks[prefix+suffix]={expand:function expand(value){var i=0,expanded={}, // Assumes a single number if not a string
	parts=typeof value==="string"?value.split(" "):[value];for(;i<4;i++){expanded[prefix+cssExpand[i]+suffix]=parts[i]||parts[i-2]||parts[0];}return expanded;}};if(!rmargin.test(prefix)){jQuery.cssHooks[prefix+suffix].set=setPositiveNumber;}});jQuery.fn.extend({css:function css(name,value){return access(this,function(elem,name,value){var styles,len,map={},i=0;if(jQuery.isArray(name)){styles=getStyles(elem);len=name.length;for(;i<len;i++){map[name[i]]=jQuery.css(elem,name[i],false,styles);}return map;}return value!==undefined?jQuery.style(elem,name,value):jQuery.css(elem,name);},name,value,arguments.length>1);},show:function show(){return showHide(this,true);},hide:function hide(){return showHide(this);},toggle:function toggle(state){if(typeof state==="boolean"){return state?this.show():this.hide();}return this.each(function(){if(isHidden(this)){jQuery(this).show();}else {jQuery(this).hide();}});}});function Tween(elem,options,prop,end,easing){return new Tween.prototype.init(elem,options,prop,end,easing);}jQuery.Tween=Tween;Tween.prototype={constructor:Tween,init:function init(elem,options,prop,end,easing,unit){this.elem=elem;this.prop=prop;this.easing=easing||jQuery.easing._default;this.options=options;this.start=this.now=this.cur();this.end=end;this.unit=unit||(jQuery.cssNumber[prop]?"":"px");},cur:function cur(){var hooks=Tween.propHooks[this.prop];return hooks&&hooks.get?hooks.get(this):Tween.propHooks._default.get(this);},run:function run(percent){var eased,hooks=Tween.propHooks[this.prop];if(this.options.duration){this.pos=eased=jQuery.easing[this.easing](percent,this.options.duration*percent,0,1,this.options.duration);}else {this.pos=eased=percent;}this.now=(this.end-this.start)*eased+this.start;if(this.options.step){this.options.step.call(this.elem,this.now,this);}if(hooks&&hooks.set){hooks.set(this);}else {Tween.propHooks._default.set(this);}return this;}};Tween.prototype.init.prototype=Tween.prototype;Tween.propHooks={_default:{get:function get(tween){var result; // Use a property on the element directly when it is not a DOM element,
	// or when there is no matching style property that exists.
	if(tween.elem.nodeType!==1||tween.elem[tween.prop]!=null&&tween.elem.style[tween.prop]==null){return tween.elem[tween.prop];} // Passing an empty string as a 3rd parameter to .css will automatically
	// attempt a parseFloat and fallback to a string if the parse fails.
	// Simple values such as "10px" are parsed to Float;
	// complex values such as "rotate(1rad)" are returned as-is.
	result=jQuery.css(tween.elem,tween.prop,""); // Empty strings, null, undefined and "auto" are converted to 0.
	return !result||result==="auto"?0:result;},set:function set(tween){ // Use step hook for back compat.
	// Use cssHook if its there.
	// Use .style if available and use plain properties where available.
	if(jQuery.fx.step[tween.prop]){jQuery.fx.step[tween.prop](tween);}else if(tween.elem.nodeType===1&&(tween.elem.style[jQuery.cssProps[tween.prop]]!=null||jQuery.cssHooks[tween.prop])){jQuery.style(tween.elem,tween.prop,tween.now+tween.unit);}else {tween.elem[tween.prop]=tween.now;}}}}; // Support: IE9
	// Panic based approach to setting things on disconnected nodes
	Tween.propHooks.scrollTop=Tween.propHooks.scrollLeft={set:function set(tween){if(tween.elem.nodeType&&tween.elem.parentNode){tween.elem[tween.prop]=tween.now;}}};jQuery.easing={linear:function linear(p){return p;},swing:function swing(p){return 0.5-Math.cos(p*Math.PI)/2;},_default:"swing"};jQuery.fx=Tween.prototype.init; // Back Compat <1.8 extension point
	jQuery.fx.step={};var fxNow,timerId,rfxtypes=/^(?:toggle|show|hide)$/,rrun=/queueHooks$/; // Animations created synchronously will run synchronously
	function createFxNow(){window.setTimeout(function(){fxNow=undefined;});return fxNow=jQuery.now();} // Generate parameters to create a standard animation
	function genFx(type,includeWidth){var which,i=0,attrs={height:type}; // If we include width, step value is 1 to do all cssExpand values,
	// otherwise step value is 2 to skip over Left and Right
	includeWidth=includeWidth?1:0;for(;i<4;i+=2-includeWidth){which=cssExpand[i];attrs["margin"+which]=attrs["padding"+which]=type;}if(includeWidth){attrs.opacity=attrs.width=type;}return attrs;}function createTween(value,prop,animation){var tween,collection=(Animation.tweeners[prop]||[]).concat(Animation.tweeners["*"]),index=0,length=collection.length;for(;index<length;index++){if(tween=collection[index].call(animation,prop,value)){ // We're done with this property
	return tween;}}}function defaultPrefilter(elem,props,opts){ /* jshint validthis: true */var prop,value,toggle,tween,hooks,oldfire,display,checkDisplay,anim=this,orig={},style=elem.style,hidden=elem.nodeType&&isHidden(elem),dataShow=dataPriv.get(elem,"fxshow"); // Handle queue: false promises
	if(!opts.queue){hooks=jQuery._queueHooks(elem,"fx");if(hooks.unqueued==null){hooks.unqueued=0;oldfire=hooks.empty.fire;hooks.empty.fire=function(){if(!hooks.unqueued){oldfire();}};}hooks.unqueued++;anim.always(function(){ // Ensure the complete handler is called before this completes
	anim.always(function(){hooks.unqueued--;if(!jQuery.queue(elem,"fx").length){hooks.empty.fire();}});});} // Height/width overflow pass
	if(elem.nodeType===1&&("height" in props||"width" in props)){ // Make sure that nothing sneaks out
	// Record all 3 overflow attributes because IE9-10 do not
	// change the overflow attribute when overflowX and
	// overflowY are set to the same value
	opts.overflow=[style.overflow,style.overflowX,style.overflowY]; // Set display property to inline-block for height/width
	// animations on inline elements that are having width/height animated
	display=jQuery.css(elem,"display"); // Test default display if display is currently "none"
	checkDisplay=display==="none"?dataPriv.get(elem,"olddisplay")||defaultDisplay(elem.nodeName):display;if(checkDisplay==="inline"&&jQuery.css(elem,"float")==="none"){style.display="inline-block";}}if(opts.overflow){style.overflow="hidden";anim.always(function(){style.overflow=opts.overflow[0];style.overflowX=opts.overflow[1];style.overflowY=opts.overflow[2];});} // show/hide pass
	for(prop in props){value=props[prop];if(rfxtypes.exec(value)){delete props[prop];toggle=toggle||value==="toggle";if(value===(hidden?"hide":"show")){ // If there is dataShow left over from a stopped hide or show
	// and we are going to proceed with show, we should pretend to be hidden
	if(value==="show"&&dataShow&&dataShow[prop]!==undefined){hidden=true;}else {continue;}}orig[prop]=dataShow&&dataShow[prop]||jQuery.style(elem,prop); // Any non-fx value stops us from restoring the original display value
	}else {display=undefined;}}if(!jQuery.isEmptyObject(orig)){if(dataShow){if("hidden" in dataShow){hidden=dataShow.hidden;}}else {dataShow=dataPriv.access(elem,"fxshow",{});} // Store state if its toggle - enables .stop().toggle() to "reverse"
	if(toggle){dataShow.hidden=!hidden;}if(hidden){jQuery(elem).show();}else {anim.done(function(){jQuery(elem).hide();});}anim.done(function(){var prop;dataPriv.remove(elem,"fxshow");for(prop in orig){jQuery.style(elem,prop,orig[prop]);}});for(prop in orig){tween=createTween(hidden?dataShow[prop]:0,prop,anim);if(!(prop in dataShow)){dataShow[prop]=tween.start;if(hidden){tween.end=tween.start;tween.start=prop==="width"||prop==="height"?1:0;}}} // If this is a noop like .hide().hide(), restore an overwritten display value
	}else if((display==="none"?defaultDisplay(elem.nodeName):display)==="inline"){style.display=display;}}function propFilter(props,specialEasing){var index,name,easing,value,hooks; // camelCase, specialEasing and expand cssHook pass
	for(index in props){name=jQuery.camelCase(index);easing=specialEasing[name];value=props[index];if(jQuery.isArray(value)){easing=value[1];value=props[index]=value[0];}if(index!==name){props[name]=value;delete props[index];}hooks=jQuery.cssHooks[name];if(hooks&&"expand" in hooks){value=hooks.expand(value);delete props[name]; // Not quite $.extend, this won't overwrite existing keys.
	// Reusing 'index' because we have the correct "name"
	for(index in value){if(!(index in props)){props[index]=value[index];specialEasing[index]=easing;}}}else {specialEasing[name]=easing;}}}function Animation(elem,properties,options){var result,stopped,index=0,length=Animation.prefilters.length,deferred=jQuery.Deferred().always(function(){ // Don't match elem in the :animated selector
	delete tick.elem;}),tick=function tick(){if(stopped){return false;}var currentTime=fxNow||createFxNow(),remaining=Math.max(0,animation.startTime+animation.duration-currentTime), // Support: Android 2.3
	// Archaic crash bug won't allow us to use `1 - ( 0.5 || 0 )` (#12497)
	temp=remaining/animation.duration||0,percent=1-temp,index=0,length=animation.tweens.length;for(;index<length;index++){animation.tweens[index].run(percent);}deferred.notifyWith(elem,[animation,percent,remaining]);if(percent<1&&length){return remaining;}else {deferred.resolveWith(elem,[animation]);return false;}},animation=deferred.promise({elem:elem,props:jQuery.extend({},properties),opts:jQuery.extend(true,{specialEasing:{},easing:jQuery.easing._default},options),originalProperties:properties,originalOptions:options,startTime:fxNow||createFxNow(),duration:options.duration,tweens:[],createTween:function createTween(prop,end){var tween=jQuery.Tween(elem,animation.opts,prop,end,animation.opts.specialEasing[prop]||animation.opts.easing);animation.tweens.push(tween);return tween;},stop:function stop(gotoEnd){var index=0, // If we are going to the end, we want to run all the tweens
	// otherwise we skip this part
	length=gotoEnd?animation.tweens.length:0;if(stopped){return this;}stopped=true;for(;index<length;index++){animation.tweens[index].run(1);} // Resolve when we played the last frame; otherwise, reject
	if(gotoEnd){deferred.notifyWith(elem,[animation,1,0]);deferred.resolveWith(elem,[animation,gotoEnd]);}else {deferred.rejectWith(elem,[animation,gotoEnd]);}return this;}}),props=animation.props;propFilter(props,animation.opts.specialEasing);for(;index<length;index++){result=Animation.prefilters[index].call(animation,elem,props,animation.opts);if(result){if(jQuery.isFunction(result.stop)){jQuery._queueHooks(animation.elem,animation.opts.queue).stop=jQuery.proxy(result.stop,result);}return result;}}jQuery.map(props,createTween,animation);if(jQuery.isFunction(animation.opts.start)){animation.opts.start.call(elem,animation);}jQuery.fx.timer(jQuery.extend(tick,{elem:elem,anim:animation,queue:animation.opts.queue})); // attach callbacks from options
	return animation.progress(animation.opts.progress).done(animation.opts.done,animation.opts.complete).fail(animation.opts.fail).always(animation.opts.always);}jQuery.Animation=jQuery.extend(Animation,{tweeners:{"*":[function(prop,value){var tween=this.createTween(prop,value);adjustCSS(tween.elem,prop,rcssNum.exec(value),tween);return tween;}]},tweener:function tweener(props,callback){if(jQuery.isFunction(props)){callback=props;props=["*"];}else {props=props.match(rnotwhite);}var prop,index=0,length=props.length;for(;index<length;index++){prop=props[index];Animation.tweeners[prop]=Animation.tweeners[prop]||[];Animation.tweeners[prop].unshift(callback);}},prefilters:[defaultPrefilter],prefilter:function prefilter(callback,prepend){if(prepend){Animation.prefilters.unshift(callback);}else {Animation.prefilters.push(callback);}}});jQuery.speed=function(speed,easing,fn){var opt=speed&&(typeof speed==="undefined"?"undefined":_typeof(speed))==="object"?jQuery.extend({},speed):{complete:fn||!fn&&easing||jQuery.isFunction(speed)&&speed,duration:speed,easing:fn&&easing||easing&&!jQuery.isFunction(easing)&&easing};opt.duration=jQuery.fx.off?0:typeof opt.duration==="number"?opt.duration:opt.duration in jQuery.fx.speeds?jQuery.fx.speeds[opt.duration]:jQuery.fx.speeds._default; // Normalize opt.queue - true/undefined/null -> "fx"
	if(opt.queue==null||opt.queue===true){opt.queue="fx";} // Queueing
	opt.old=opt.complete;opt.complete=function(){if(jQuery.isFunction(opt.old)){opt.old.call(this);}if(opt.queue){jQuery.dequeue(this,opt.queue);}};return opt;};jQuery.fn.extend({fadeTo:function fadeTo(speed,to,easing,callback){ // Show any hidden elements after setting opacity to 0
	return this.filter(isHidden).css("opacity",0).show() // Animate to the value specified
	.end().animate({opacity:to},speed,easing,callback);},animate:function animate(prop,speed,easing,callback){var empty=jQuery.isEmptyObject(prop),optall=jQuery.speed(speed,easing,callback),doAnimation=function doAnimation(){ // Operate on a copy of prop so per-property easing won't be lost
	var anim=Animation(this,jQuery.extend({},prop),optall); // Empty animations, or finishing resolves immediately
	if(empty||dataPriv.get(this,"finish")){anim.stop(true);}};doAnimation.finish=doAnimation;return empty||optall.queue===false?this.each(doAnimation):this.queue(optall.queue,doAnimation);},stop:function stop(type,clearQueue,gotoEnd){var stopQueue=function stopQueue(hooks){var stop=hooks.stop;delete hooks.stop;stop(gotoEnd);};if(typeof type!=="string"){gotoEnd=clearQueue;clearQueue=type;type=undefined;}if(clearQueue&&type!==false){this.queue(type||"fx",[]);}return this.each(function(){var dequeue=true,index=type!=null&&type+"queueHooks",timers=jQuery.timers,data=dataPriv.get(this);if(index){if(data[index]&&data[index].stop){stopQueue(data[index]);}}else {for(index in data){if(data[index]&&data[index].stop&&rrun.test(index)){stopQueue(data[index]);}}}for(index=timers.length;index--;){if(timers[index].elem===this&&(type==null||timers[index].queue===type)){timers[index].anim.stop(gotoEnd);dequeue=false;timers.splice(index,1);}} // Start the next in the queue if the last step wasn't forced.
	// Timers currently will call their complete callbacks, which
	// will dequeue but only if they were gotoEnd.
	if(dequeue||!gotoEnd){jQuery.dequeue(this,type);}});},finish:function finish(type){if(type!==false){type=type||"fx";}return this.each(function(){var index,data=dataPriv.get(this),queue=data[type+"queue"],hooks=data[type+"queueHooks"],timers=jQuery.timers,length=queue?queue.length:0; // Enable finishing flag on private data
	data.finish=true; // Empty the queue first
	jQuery.queue(this,type,[]);if(hooks&&hooks.stop){hooks.stop.call(this,true);} // Look for any active animations, and finish them
	for(index=timers.length;index--;){if(timers[index].elem===this&&timers[index].queue===type){timers[index].anim.stop(true);timers.splice(index,1);}} // Look for any animations in the old queue and finish them
	for(index=0;index<length;index++){if(queue[index]&&queue[index].finish){queue[index].finish.call(this);}} // Turn off finishing flag
	delete data.finish;});}});jQuery.each(["toggle","show","hide"],function(i,name){var cssFn=jQuery.fn[name];jQuery.fn[name]=function(speed,easing,callback){return speed==null||typeof speed==="boolean"?cssFn.apply(this,arguments):this.animate(genFx(name,true),speed,easing,callback);};}); // Generate shortcuts for custom animations
	jQuery.each({slideDown:genFx("show"),slideUp:genFx("hide"),slideToggle:genFx("toggle"),fadeIn:{opacity:"show"},fadeOut:{opacity:"hide"},fadeToggle:{opacity:"toggle"}},function(name,props){jQuery.fn[name]=function(speed,easing,callback){return this.animate(props,speed,easing,callback);};});jQuery.timers=[];jQuery.fx.tick=function(){var timer,i=0,timers=jQuery.timers;fxNow=jQuery.now();for(;i<timers.length;i++){timer=timers[i]; // Checks the timer has not already been removed
	if(!timer()&&timers[i]===timer){timers.splice(i--,1);}}if(!timers.length){jQuery.fx.stop();}fxNow=undefined;};jQuery.fx.timer=function(timer){jQuery.timers.push(timer);if(timer()){jQuery.fx.start();}else {jQuery.timers.pop();}};jQuery.fx.interval=13;jQuery.fx.start=function(){if(!timerId){timerId=window.setInterval(jQuery.fx.tick,jQuery.fx.interval);}};jQuery.fx.stop=function(){window.clearInterval(timerId);timerId=null;};jQuery.fx.speeds={slow:600,fast:200, // Default speed
	_default:400}; // Based off of the plugin by Clint Helfers, with permission.
	// http://web.archive.org/web/20100324014747/http://blindsignals.com/index.php/2009/07/jquery-delay/
	jQuery.fn.delay=function(time,type){time=jQuery.fx?jQuery.fx.speeds[time]||time:time;type=type||"fx";return this.queue(type,function(next,hooks){var timeout=window.setTimeout(next,time);hooks.stop=function(){window.clearTimeout(timeout);};});};(function(){var input=document.createElement("input"),select=document.createElement("select"),opt=select.appendChild(document.createElement("option"));input.type="checkbox"; // Support: iOS<=5.1, Android<=4.2+
	// Default value for a checkbox should be "on"
	support.checkOn=input.value!==""; // Support: IE<=11+
	// Must access selectedIndex to make default options select
	support.optSelected=opt.selected; // Support: Android<=2.3
	// Options inside disabled selects are incorrectly marked as disabled
	select.disabled=true;support.optDisabled=!opt.disabled; // Support: IE<=11+
	// An input loses its value after becoming a radio
	input=document.createElement("input");input.value="t";input.type="radio";support.radioValue=input.value==="t";})();var boolHook,attrHandle=jQuery.expr.attrHandle;jQuery.fn.extend({attr:function attr(name,value){return access(this,jQuery.attr,name,value,arguments.length>1);},removeAttr:function removeAttr(name){return this.each(function(){jQuery.removeAttr(this,name);});}});jQuery.extend({attr:function attr(elem,name,value){var ret,hooks,nType=elem.nodeType; // Don't get/set attributes on text, comment and attribute nodes
	if(nType===3||nType===8||nType===2){return;} // Fallback to prop when attributes are not supported
	if(typeof elem.getAttribute==="undefined"){return jQuery.prop(elem,name,value);} // All attributes are lowercase
	// Grab necessary hook if one is defined
	if(nType!==1||!jQuery.isXMLDoc(elem)){name=name.toLowerCase();hooks=jQuery.attrHooks[name]||(jQuery.expr.match.bool.test(name)?boolHook:undefined);}if(value!==undefined){if(value===null){jQuery.removeAttr(elem,name);return;}if(hooks&&"set" in hooks&&(ret=hooks.set(elem,value,name))!==undefined){return ret;}elem.setAttribute(name,value+"");return value;}if(hooks&&"get" in hooks&&(ret=hooks.get(elem,name))!==null){return ret;}ret=jQuery.find.attr(elem,name); // Non-existent attributes return null, we normalize to undefined
	return ret==null?undefined:ret;},attrHooks:{type:{set:function set(elem,value){if(!support.radioValue&&value==="radio"&&jQuery.nodeName(elem,"input")){var val=elem.value;elem.setAttribute("type",value);if(val){elem.value=val;}return value;}}}},removeAttr:function removeAttr(elem,value){var name,propName,i=0,attrNames=value&&value.match(rnotwhite);if(attrNames&&elem.nodeType===1){while(name=attrNames[i++]){propName=jQuery.propFix[name]||name; // Boolean attributes get special treatment (#10870)
	if(jQuery.expr.match.bool.test(name)){ // Set corresponding property to false
	elem[propName]=false;}elem.removeAttribute(name);}}}}); // Hooks for boolean attributes
	boolHook={set:function set(elem,value,name){if(value===false){ // Remove boolean attributes when set to false
	jQuery.removeAttr(elem,name);}else {elem.setAttribute(name,name);}return name;}};jQuery.each(jQuery.expr.match.bool.source.match(/\w+/g),function(i,name){var getter=attrHandle[name]||jQuery.find.attr;attrHandle[name]=function(elem,name,isXML){var ret,handle;if(!isXML){ // Avoid an infinite loop by temporarily removing this function from the getter
	handle=attrHandle[name];attrHandle[name]=ret;ret=getter(elem,name,isXML)!=null?name.toLowerCase():null;attrHandle[name]=handle;}return ret;};});var rfocusable=/^(?:input|select|textarea|button)$/i,rclickable=/^(?:a|area)$/i;jQuery.fn.extend({prop:function prop(name,value){return access(this,jQuery.prop,name,value,arguments.length>1);},removeProp:function removeProp(name){return this.each(function(){delete this[jQuery.propFix[name]||name];});}});jQuery.extend({prop:function prop(elem,name,value){var ret,hooks,nType=elem.nodeType; // Don't get/set properties on text, comment and attribute nodes
	if(nType===3||nType===8||nType===2){return;}if(nType!==1||!jQuery.isXMLDoc(elem)){ // Fix name and attach hooks
	name=jQuery.propFix[name]||name;hooks=jQuery.propHooks[name];}if(value!==undefined){if(hooks&&"set" in hooks&&(ret=hooks.set(elem,value,name))!==undefined){return ret;}return elem[name]=value;}if(hooks&&"get" in hooks&&(ret=hooks.get(elem,name))!==null){return ret;}return elem[name];},propHooks:{tabIndex:{get:function get(elem){ // elem.tabIndex doesn't always return the
	// correct value when it hasn't been explicitly set
	// http://fluidproject.org/blog/2008/01/09/getting-setting-and-removing-tabindex-values-with-javascript/
	// Use proper attribute retrieval(#12072)
	var tabindex=jQuery.find.attr(elem,"tabindex");return tabindex?parseInt(tabindex,10):rfocusable.test(elem.nodeName)||rclickable.test(elem.nodeName)&&elem.href?0:-1;}}},propFix:{"for":"htmlFor","class":"className"}});if(!support.optSelected){jQuery.propHooks.selected={get:function get(elem){var parent=elem.parentNode;if(parent&&parent.parentNode){parent.parentNode.selectedIndex;}return null;}};}jQuery.each(["tabIndex","readOnly","maxLength","cellSpacing","cellPadding","rowSpan","colSpan","useMap","frameBorder","contentEditable"],function(){jQuery.propFix[this.toLowerCase()]=this;});var rclass=/[\t\r\n\f]/g;function getClass(elem){return elem.getAttribute&&elem.getAttribute("class")||"";}jQuery.fn.extend({addClass:function addClass(value){var classes,elem,cur,curValue,clazz,j,finalValue,i=0;if(jQuery.isFunction(value)){return this.each(function(j){jQuery(this).addClass(value.call(this,j,getClass(this)));});}if(typeof value==="string"&&value){classes=value.match(rnotwhite)||[];while(elem=this[i++]){curValue=getClass(elem);cur=elem.nodeType===1&&(" "+curValue+" ").replace(rclass," ");if(cur){j=0;while(clazz=classes[j++]){if(cur.indexOf(" "+clazz+" ")<0){cur+=clazz+" ";}} // Only assign if different to avoid unneeded rendering.
	finalValue=jQuery.trim(cur);if(curValue!==finalValue){elem.setAttribute("class",finalValue);}}}}return this;},removeClass:function removeClass(value){var classes,elem,cur,curValue,clazz,j,finalValue,i=0;if(jQuery.isFunction(value)){return this.each(function(j){jQuery(this).removeClass(value.call(this,j,getClass(this)));});}if(!arguments.length){return this.attr("class","");}if(typeof value==="string"&&value){classes=value.match(rnotwhite)||[];while(elem=this[i++]){curValue=getClass(elem); // This expression is here for better compressibility (see addClass)
	cur=elem.nodeType===1&&(" "+curValue+" ").replace(rclass," ");if(cur){j=0;while(clazz=classes[j++]){ // Remove *all* instances
	while(cur.indexOf(" "+clazz+" ")>-1){cur=cur.replace(" "+clazz+" "," ");}} // Only assign if different to avoid unneeded rendering.
	finalValue=jQuery.trim(cur);if(curValue!==finalValue){elem.setAttribute("class",finalValue);}}}}return this;},toggleClass:function toggleClass(value,stateVal){var type=typeof value==="undefined"?"undefined":_typeof(value);if(typeof stateVal==="boolean"&&type==="string"){return stateVal?this.addClass(value):this.removeClass(value);}if(jQuery.isFunction(value)){return this.each(function(i){jQuery(this).toggleClass(value.call(this,i,getClass(this),stateVal),stateVal);});}return this.each(function(){var className,i,self,classNames;if(type==="string"){ // Toggle individual class names
	i=0;self=jQuery(this);classNames=value.match(rnotwhite)||[];while(className=classNames[i++]){ // Check each className given, space separated list
	if(self.hasClass(className)){self.removeClass(className);}else {self.addClass(className);}} // Toggle whole class name
	}else if(value===undefined||type==="boolean"){className=getClass(this);if(className){ // Store className if set
	dataPriv.set(this,"__className__",className);} // If the element has a class name or if we're passed `false`,
	// then remove the whole classname (if there was one, the above saved it).
	// Otherwise bring back whatever was previously saved (if anything),
	// falling back to the empty string if nothing was stored.
	if(this.setAttribute){this.setAttribute("class",className||value===false?"":dataPriv.get(this,"__className__")||"");}}});},hasClass:function hasClass(selector){var className,elem,i=0;className=" "+selector+" ";while(elem=this[i++]){if(elem.nodeType===1&&(" "+getClass(elem)+" ").replace(rclass," ").indexOf(className)>-1){return true;}}return false;}});var rreturn=/\r/g;jQuery.fn.extend({val:function val(value){var hooks,ret,isFunction,elem=this[0];if(!arguments.length){if(elem){hooks=jQuery.valHooks[elem.type]||jQuery.valHooks[elem.nodeName.toLowerCase()];if(hooks&&"get" in hooks&&(ret=hooks.get(elem,"value"))!==undefined){return ret;}ret=elem.value;return typeof ret==="string"? // Handle most common string cases
	ret.replace(rreturn,""): // Handle cases where value is null/undef or number
	ret==null?"":ret;}return;}isFunction=jQuery.isFunction(value);return this.each(function(i){var val;if(this.nodeType!==1){return;}if(isFunction){val=value.call(this,i,jQuery(this).val());}else {val=value;} // Treat null/undefined as ""; convert numbers to string
	if(val==null){val="";}else if(typeof val==="number"){val+="";}else if(jQuery.isArray(val)){val=jQuery.map(val,function(value){return value==null?"":value+"";});}hooks=jQuery.valHooks[this.type]||jQuery.valHooks[this.nodeName.toLowerCase()]; // If set returns undefined, fall back to normal setting
	if(!hooks||!("set" in hooks)||hooks.set(this,val,"value")===undefined){this.value=val;}});}});jQuery.extend({valHooks:{option:{get:function get(elem){ // Support: IE<11
	// option.value not trimmed (#14858)
	return jQuery.trim(elem.value);}},select:{get:function get(elem){var value,option,options=elem.options,index=elem.selectedIndex,one=elem.type==="select-one"||index<0,values=one?null:[],max=one?index+1:options.length,i=index<0?max:one?index:0; // Loop through all the selected options
	for(;i<max;i++){option=options[i]; // IE8-9 doesn't update selected after form reset (#2551)
	if((option.selected||i===index)&&( // Don't return options that are disabled or in a disabled optgroup
	support.optDisabled?!option.disabled:option.getAttribute("disabled")===null)&&(!option.parentNode.disabled||!jQuery.nodeName(option.parentNode,"optgroup"))){ // Get the specific value for the option
	value=jQuery(option).val(); // We don't need an array for one selects
	if(one){return value;} // Multi-Selects return an array
	values.push(value);}}return values;},set:function set(elem,value){var optionSet,option,options=elem.options,values=jQuery.makeArray(value),i=options.length;while(i--){option=options[i];if(option.selected=jQuery.inArray(jQuery.valHooks.option.get(option),values)>-1){optionSet=true;}} // Force browsers to behave consistently when non-matching value is set
	if(!optionSet){elem.selectedIndex=-1;}return values;}}}}); // Radios and checkboxes getter/setter
	jQuery.each(["radio","checkbox"],function(){jQuery.valHooks[this]={set:function set(elem,value){if(jQuery.isArray(value)){return elem.checked=jQuery.inArray(jQuery(elem).val(),value)>-1;}}};if(!support.checkOn){jQuery.valHooks[this].get=function(elem){return elem.getAttribute("value")===null?"on":elem.value;};}}); // Return jQuery for attributes-only inclusion
	var rfocusMorph=/^(?:focusinfocus|focusoutblur)$/;jQuery.extend(jQuery.event,{trigger:function trigger(event,data,elem,onlyHandlers){var i,cur,tmp,bubbleType,ontype,handle,special,eventPath=[elem||document],type=hasOwn.call(event,"type")?event.type:event,namespaces=hasOwn.call(event,"namespace")?event.namespace.split("."):[];cur=tmp=elem=elem||document; // Don't do events on text and comment nodes
	if(elem.nodeType===3||elem.nodeType===8){return;} // focus/blur morphs to focusin/out; ensure we're not firing them right now
	if(rfocusMorph.test(type+jQuery.event.triggered)){return;}if(type.indexOf(".")>-1){ // Namespaced trigger; create a regexp to match event type in handle()
	namespaces=type.split(".");type=namespaces.shift();namespaces.sort();}ontype=type.indexOf(":")<0&&"on"+type; // Caller can pass in a jQuery.Event object, Object, or just an event type string
	event=event[jQuery.expando]?event:new jQuery.Event(type,(typeof event==="undefined"?"undefined":_typeof(event))==="object"&&event); // Trigger bitmask: & 1 for native handlers; & 2 for jQuery (always true)
	event.isTrigger=onlyHandlers?2:3;event.namespace=namespaces.join(".");event.rnamespace=event.namespace?new RegExp("(^|\\.)"+namespaces.join("\\.(?:.*\\.|)")+"(\\.|$)"):null; // Clean up the event in case it is being reused
	event.result=undefined;if(!event.target){event.target=elem;} // Clone any incoming data and prepend the event, creating the handler arg list
	data=data==null?[event]:jQuery.makeArray(data,[event]); // Allow special events to draw outside the lines
	special=jQuery.event.special[type]||{};if(!onlyHandlers&&special.trigger&&special.trigger.apply(elem,data)===false){return;} // Determine event propagation path in advance, per W3C events spec (#9951)
	// Bubble up to document, then to window; watch for a global ownerDocument var (#9724)
	if(!onlyHandlers&&!special.noBubble&&!jQuery.isWindow(elem)){bubbleType=special.delegateType||type;if(!rfocusMorph.test(bubbleType+type)){cur=cur.parentNode;}for(;cur;cur=cur.parentNode){eventPath.push(cur);tmp=cur;} // Only add window if we got to document (e.g., not plain obj or detached DOM)
	if(tmp===(elem.ownerDocument||document)){eventPath.push(tmp.defaultView||tmp.parentWindow||window);}} // Fire handlers on the event path
	i=0;while((cur=eventPath[i++])&&!event.isPropagationStopped()){event.type=i>1?bubbleType:special.bindType||type; // jQuery handler
	handle=(dataPriv.get(cur,"events")||{})[event.type]&&dataPriv.get(cur,"handle");if(handle){handle.apply(cur,data);} // Native handler
	handle=ontype&&cur[ontype];if(handle&&handle.apply&&acceptData(cur)){event.result=handle.apply(cur,data);if(event.result===false){event.preventDefault();}}}event.type=type; // If nobody prevented the default action, do it now
	if(!onlyHandlers&&!event.isDefaultPrevented()){if((!special._default||special._default.apply(eventPath.pop(),data)===false)&&acceptData(elem)){ // Call a native DOM method on the target with the same name name as the event.
	// Don't do default actions on window, that's where global variables be (#6170)
	if(ontype&&jQuery.isFunction(elem[type])&&!jQuery.isWindow(elem)){ // Don't re-trigger an onFOO event when we call its FOO() method
	tmp=elem[ontype];if(tmp){elem[ontype]=null;} // Prevent re-triggering of the same event, since we already bubbled it above
	jQuery.event.triggered=type;elem[type]();jQuery.event.triggered=undefined;if(tmp){elem[ontype]=tmp;}}}}return event.result;}, // Piggyback on a donor event to simulate a different one
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
	});jQuery.event.trigger(e,null,elem);if(e.isDefaultPrevented()){event.preventDefault();}}});jQuery.fn.extend({trigger:function trigger(type,data){return this.each(function(){jQuery.event.trigger(type,data,this);});},triggerHandler:function triggerHandler(type,data){var elem=this[0];if(elem){return jQuery.event.trigger(type,data,elem,true);}}});jQuery.each(("blur focus focusin focusout load resize scroll unload click dblclick "+"mousedown mouseup mousemove mouseover mouseout mouseenter mouseleave "+"change select submit keydown keypress keyup error contextmenu").split(" "),function(i,name){ // Handle event binding
	jQuery.fn[name]=function(data,fn){return arguments.length>0?this.on(name,null,data,fn):this.trigger(name);};});jQuery.fn.extend({hover:function hover(fnOver,fnOut){return this.mouseenter(fnOver).mouseleave(fnOut||fnOver);}});support.focusin="onfocusin" in window; // Support: Firefox
	// Firefox doesn't have focus(in | out) events
	// Related ticket - https://bugzilla.mozilla.org/show_bug.cgi?id=687787
	//
	// Support: Chrome, Safari
	// focus(in | out) events fire after focus & blur events,
	// which is spec violation - http://www.w3.org/TR/DOM-Level-3-Events/#events-focusevent-event-order
	// Related ticket - https://code.google.com/p/chromium/issues/detail?id=449857
	if(!support.focusin){jQuery.each({focus:"focusin",blur:"focusout"},function(orig,fix){ // Attach a single capturing handler on the document while someone wants focusin/focusout
	var handler=function handler(event){jQuery.event.simulate(fix,event.target,jQuery.event.fix(event));};jQuery.event.special[fix]={setup:function setup(){var doc=this.ownerDocument||this,attaches=dataPriv.access(doc,fix);if(!attaches){doc.addEventListener(orig,handler,true);}dataPriv.access(doc,fix,(attaches||0)+1);},teardown:function teardown(){var doc=this.ownerDocument||this,attaches=dataPriv.access(doc,fix)-1;if(!attaches){doc.removeEventListener(orig,handler,true);dataPriv.remove(doc,fix);}else {dataPriv.access(doc,fix,attaches);}}};});}var location=window.location;var nonce=jQuery.now();var rquery=/\?/; // Support: Android 2.3
	// Workaround failure to string-cast null input
	jQuery.parseJSON=function(data){return JSON.parse(data+"");}; // Cross-browser xml parsing
	jQuery.parseXML=function(data){var xml;if(!data||typeof data!=="string"){return null;} // Support: IE9
	try{xml=new window.DOMParser().parseFromString(data,"text/xml");}catch(e){xml=undefined;}if(!xml||xml.getElementsByTagName("parsererror").length){jQuery.error("Invalid XML: "+data);}return xml;};var rhash=/#.*$/,rts=/([?&])_=[^&]*/,rheaders=/^(.*?):[ \t]*([^\r\n]*)$/mg, // #7653, #8125, #8152: local protocol detection
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
	originAnchor=document.createElement("a");originAnchor.href=location.href; // Base "constructor" for jQuery.ajaxPrefilter and jQuery.ajaxTransport
	function addToPrefiltersOrTransports(structure){ // dataTypeExpression is optional and defaults to "*"
	return function(dataTypeExpression,func){if(typeof dataTypeExpression!=="string"){func=dataTypeExpression;dataTypeExpression="*";}var dataType,i=0,dataTypes=dataTypeExpression.toLowerCase().match(rnotwhite)||[];if(jQuery.isFunction(func)){ // For each dataType in the dataTypeExpression
	while(dataType=dataTypes[i++]){ // Prepend if requested
	if(dataType[0]==="+"){dataType=dataType.slice(1)||"*";(structure[dataType]=structure[dataType]||[]).unshift(func); // Otherwise append
	}else {(structure[dataType]=structure[dataType]||[]).push(func);}}}};} // Base inspection function for prefilters and transports
	function inspectPrefiltersOrTransports(structure,options,originalOptions,jqXHR){var inspected={},seekingTransport=structure===transports;function inspect(dataType){var selected;inspected[dataType]=true;jQuery.each(structure[dataType]||[],function(_,prefilterOrFactory){var dataTypeOrTransport=prefilterOrFactory(options,originalOptions,jqXHR);if(typeof dataTypeOrTransport==="string"&&!seekingTransport&&!inspected[dataTypeOrTransport]){options.dataTypes.unshift(dataTypeOrTransport);inspect(dataTypeOrTransport);return false;}else if(seekingTransport){return !(selected=dataTypeOrTransport);}});return selected;}return inspect(options.dataTypes[0])||!inspected["*"]&&inspect("*");} // A special extend for ajax options
	// that takes "flat" options (not to be deep extended)
	// Fixes #9887
	function ajaxExtend(target,src){var key,deep,flatOptions=jQuery.ajaxSettings.flatOptions||{};for(key in src){if(src[key]!==undefined){(flatOptions[key]?target:deep||(deep={}))[key]=src[key];}}if(deep){jQuery.extend(true,target,deep);}return target;} /* Handles responses to an ajax request:
	 * - finds the right dataType (mediates between content-type and expected dataType)
	 * - returns the corresponding response
	 */function ajaxHandleResponses(s,jqXHR,responses){var ct,type,finalDataType,firstDataType,contents=s.contents,dataTypes=s.dataTypes; // Remove auto dataType and get content-type in the process
	while(dataTypes[0]==="*"){dataTypes.shift();if(ct===undefined){ct=s.mimeType||jqXHR.getResponseHeader("Content-Type");}} // Check if we're dealing with a known content-type
	if(ct){for(type in contents){if(contents[type]&&contents[type].test(ct)){dataTypes.unshift(type);break;}}} // Check to see if we have a response for the expected dataType
	if(dataTypes[0] in responses){finalDataType=dataTypes[0];}else { // Try convertible dataTypes
	for(type in responses){if(!dataTypes[0]||s.converters[type+" "+dataTypes[0]]){finalDataType=type;break;}if(!firstDataType){firstDataType=type;}} // Or just use first one
	finalDataType=finalDataType||firstDataType;} // If we found a dataType
	// We add the dataType to the list if needed
	// and return the corresponding response
	if(finalDataType){if(finalDataType!==dataTypes[0]){dataTypes.unshift(finalDataType);}return responses[finalDataType];}} /* Chain conversions given the request and the original response
	 * Also sets the responseXXX fields on the jqXHR instance
	 */function ajaxConvert(s,response,jqXHR,isSuccess){var conv2,current,conv,tmp,prev,converters={}, // Work with a copy of dataTypes in case we need to modify it for conversion
	dataTypes=s.dataTypes.slice(); // Create converters map with lowercased keys
	if(dataTypes[1]){for(conv in s.converters){converters[conv.toLowerCase()]=s.converters[conv];}}current=dataTypes.shift(); // Convert to each sequential dataType
	while(current){if(s.responseFields[current]){jqXHR[s.responseFields[current]]=response;} // Apply the dataFilter if provided
	if(!prev&&isSuccess&&s.dataFilter){response=s.dataFilter(response,s.dataType);}prev=current;current=dataTypes.shift();if(current){ // There's only work to do if current dataType is non-auto
	if(current==="*"){current=prev; // Convert response if prev dataType is non-auto and differs from current
	}else if(prev!=="*"&&prev!==current){ // Seek a direct converter
	conv=converters[prev+" "+current]||converters["* "+current]; // If none found, seek a pair
	if(!conv){for(conv2 in converters){ // If conv2 outputs current
	tmp=conv2.split(" ");if(tmp[1]===current){ // If prev can be converted to accepted input
	conv=converters[prev+" "+tmp[0]]||converters["* "+tmp[0]];if(conv){ // Condense equivalence converters
	if(conv===true){conv=converters[conv2]; // Otherwise, insert the intermediate dataType
	}else if(converters[conv2]!==true){current=tmp[0];dataTypes.unshift(tmp[1]);}break;}}}} // Apply converter (if not an equivalence)
	if(conv!==true){ // Unless errors are allowed to bubble, catch and return them
	if(conv&&s.throws){response=conv(response);}else {try{response=conv(response);}catch(e){return {state:"parsererror",error:conv?e:"No conversion from "+prev+" to "+current};}}}}}}return {state:"success",data:response};}jQuery.extend({ // Counter for holding the number of active queries
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
	if((typeof url==="undefined"?"undefined":_typeof(url))==="object"){options=url;url=undefined;} // Force options to be an object
	options=options||{};var transport, // URL without anti-cache param
	cacheURL, // Response headers
	responseHeadersString,responseHeaders, // timeout handle
	timeoutTimer, // Url cleanup var
	urlAnchor, // To know if global events are to be dispatched
	fireGlobals, // Loop variable
	i, // Create the final options object
	s=jQuery.ajaxSetup({},options), // Callbacks context
	callbackContext=s.context||s, // Context for global events is callbackContext if it is a DOM node or jQuery collection
	globalEventContext=s.context&&(callbackContext.nodeType||callbackContext.jquery)?jQuery(callbackContext):jQuery.event, // Deferreds
	deferred=jQuery.Deferred(),completeDeferred=jQuery.Callbacks("once memory"), // Status-dependent callbacks
	_statusCode=s.statusCode||{}, // Headers (they are sent all at once)
	requestHeaders={},requestHeadersNames={}, // The jqXHR state
	state=0, // Default abort message
	strAbort="canceled", // Fake xhr
	jqXHR={readyState:0, // Builds headers hashtable if needed
	getResponseHeader:function getResponseHeader(key){var match;if(state===2){if(!responseHeaders){responseHeaders={};while(match=rheaders.exec(responseHeadersString)){responseHeaders[match[1].toLowerCase()]=match[2];}}match=responseHeaders[key.toLowerCase()];}return match==null?null:match;}, // Raw string
	getAllResponseHeaders:function getAllResponseHeaders(){return state===2?responseHeadersString:null;}, // Caches the header
	setRequestHeader:function setRequestHeader(name,value){var lname=name.toLowerCase();if(!state){name=requestHeadersNames[lname]=requestHeadersNames[lname]||name;requestHeaders[name]=value;}return this;}, // Overrides response content-type header
	overrideMimeType:function overrideMimeType(type){if(!state){s.mimeType=type;}return this;}, // Status-dependent callbacks
	statusCode:function statusCode(map){var code;if(map){if(state<2){for(code in map){ // Lazy-add the new callback in a way that preserves old ones
	_statusCode[code]=[_statusCode[code],map[code]];}}else { // Execute the appropriate callbacks
	jqXHR.always(map[jqXHR.status]);}}return this;}, // Cancel the request
	abort:function abort(statusText){var finalText=statusText||strAbort;if(transport){transport.abort(finalText);}done(0,finalText);return this;}}; // Attach deferreds
	deferred.promise(jqXHR).complete=completeDeferred.add;jqXHR.success=jqXHR.done;jqXHR.error=jqXHR.fail; // Remove hash character (#7531: and string promotion)
	// Add protocol if not provided (prefilters might expect it)
	// Handle falsy url in the settings object (#10093: consistency with old signature)
	// We also use the url parameter if available
	s.url=((url||s.url||location.href)+"").replace(rhash,"").replace(rprotocol,location.protocol+"//"); // Alias method option to type as per ticket #12004
	s.type=options.method||options.type||s.method||s.type; // Extract dataTypes list
	s.dataTypes=jQuery.trim(s.dataType||"*").toLowerCase().match(rnotwhite)||[""]; // A cross-domain request is in order when the origin doesn't match the current origin.
	if(s.crossDomain==null){urlAnchor=document.createElement("a"); // Support: IE8-11+
	// IE throws exception if url is malformed, e.g. http://example.com:80x/
	try{urlAnchor.href=s.url; // Support: IE8-11+
	// Anchor's host property isn't correctly set when s.url is relative
	urlAnchor.href=urlAnchor.href;s.crossDomain=originAnchor.protocol+"//"+originAnchor.host!==urlAnchor.protocol+"//"+urlAnchor.host;}catch(e){ // If there is an error parsing the URL, assume it is crossDomain,
	// it can be rejected by the transport if it is invalid
	s.crossDomain=true;}} // Convert data if not already a string
	if(s.data&&s.processData&&typeof s.data!=="string"){s.data=jQuery.param(s.data,s.traditional);} // Apply prefilters
	inspectPrefiltersOrTransports(prefilters,s,options,jqXHR); // If request was aborted inside a prefilter, stop there
	if(state===2){return jqXHR;} // We can fire global events as of now if asked to
	// Don't fire events if jQuery.event is undefined in an AMD-usage scenario (#15118)
	fireGlobals=jQuery.event&&s.global; // Watch for a new set of requests
	if(fireGlobals&&jQuery.active++===0){jQuery.event.trigger("ajaxStart");} // Uppercase the type
	s.type=s.type.toUpperCase(); // Determine if request has content
	s.hasContent=!rnoContent.test(s.type); // Save the URL in case we're toying with the If-Modified-Since
	// and/or If-None-Match header later on
	cacheURL=s.url; // More options handling for requests with no content
	if(!s.hasContent){ // If data is available, append data to url
	if(s.data){cacheURL=s.url+=(rquery.test(cacheURL)?"&":"?")+s.data; // #9682: remove data so that it's not used in an eventual retry
	delete s.data;} // Add anti-cache in url if needed
	if(s.cache===false){s.url=rts.test(cacheURL)? // If there is already a '_' parameter, set its value
	cacheURL.replace(rts,"$1_="+nonce++): // Otherwise add one to the end
	cacheURL+(rquery.test(cacheURL)?"&":"?")+"_="+nonce++;}} // Set the If-Modified-Since and/or If-None-Match header, if in ifModified mode.
	if(s.ifModified){if(jQuery.lastModified[cacheURL]){jqXHR.setRequestHeader("If-Modified-Since",jQuery.lastModified[cacheURL]);}if(jQuery.etag[cacheURL]){jqXHR.setRequestHeader("If-None-Match",jQuery.etag[cacheURL]);}} // Set the correct header, if data is being sent
	if(s.data&&s.hasContent&&s.contentType!==false||options.contentType){jqXHR.setRequestHeader("Content-Type",s.contentType);} // Set the Accepts header for the server, depending on the dataType
	jqXHR.setRequestHeader("Accept",s.dataTypes[0]&&s.accepts[s.dataTypes[0]]?s.accepts[s.dataTypes[0]]+(s.dataTypes[0]!=="*"?", "+allTypes+"; q=0.01":""):s.accepts["*"]); // Check for headers option
	for(i in s.headers){jqXHR.setRequestHeader(i,s.headers[i]);} // Allow custom headers/mimetypes and early abort
	if(s.beforeSend&&(s.beforeSend.call(callbackContext,jqXHR,s)===false||state===2)){ // Abort if not done already and return
	return jqXHR.abort();} // Aborting is no longer a cancellation
	strAbort="abort"; // Install callbacks on deferreds
	for(i in {success:1,error:1,complete:1}){jqXHR[i](s[i]);} // Get transport
	transport=inspectPrefiltersOrTransports(transports,s,options,jqXHR); // If no transport, we auto-abort
	if(!transport){done(-1,"No Transport");}else {jqXHR.readyState=1; // Send global event
	if(fireGlobals){globalEventContext.trigger("ajaxSend",[jqXHR,s]);} // If request was aborted inside ajaxSend, stop there
	if(state===2){return jqXHR;} // Timeout
	if(s.async&&s.timeout>0){timeoutTimer=window.setTimeout(function(){jqXHR.abort("timeout");},s.timeout);}try{state=1;transport.send(requestHeaders,done);}catch(e){ // Propagate exception as error if not done
	if(state<2){done(-1,e); // Simply rethrow otherwise
	}else {throw e;}}} // Callback for when everything is done
	function done(status,nativeStatusText,responses,headers){var isSuccess,success,error,response,modified,statusText=nativeStatusText; // Called once
	if(state===2){return;} // State is "done" now
	state=2; // Clear timeout if it exists
	if(timeoutTimer){window.clearTimeout(timeoutTimer);} // Dereference transport for early garbage collection
	// (no matter how long the jqXHR object will be used)
	transport=undefined; // Cache response headers
	responseHeadersString=headers||""; // Set readyState
	jqXHR.readyState=status>0?4:0; // Determine if successful
	isSuccess=status>=200&&status<300||status===304; // Get response data
	if(responses){response=ajaxHandleResponses(s,jqXHR,responses);} // Convert no matter what (that way responseXXX fields are always set)
	response=ajaxConvert(s,response,jqXHR,isSuccess); // If successful, handle type chaining
	if(isSuccess){ // Set the If-Modified-Since and/or If-None-Match header, if in ifModified mode.
	if(s.ifModified){modified=jqXHR.getResponseHeader("Last-Modified");if(modified){jQuery.lastModified[cacheURL]=modified;}modified=jqXHR.getResponseHeader("etag");if(modified){jQuery.etag[cacheURL]=modified;}} // if no content
	if(status===204||s.type==="HEAD"){statusText="nocontent"; // if not modified
	}else if(status===304){statusText="notmodified"; // If we have data, let's convert it
	}else {statusText=response.state;success=response.data;error=response.error;isSuccess=!error;}}else { // Extract error from statusText and normalize for non-aborts
	error=statusText;if(status||!statusText){statusText="error";if(status<0){status=0;}}} // Set data for the fake xhr object
	jqXHR.status=status;jqXHR.statusText=(nativeStatusText||statusText)+""; // Success/Error
	if(isSuccess){deferred.resolveWith(callbackContext,[success,statusText,jqXHR]);}else {deferred.rejectWith(callbackContext,[jqXHR,statusText,error]);} // Status-dependent callbacks
	jqXHR.statusCode(_statusCode);_statusCode=undefined;if(fireGlobals){globalEventContext.trigger(isSuccess?"ajaxSuccess":"ajaxError",[jqXHR,s,isSuccess?success:error]);} // Complete
	completeDeferred.fireWith(callbackContext,[jqXHR,statusText]);if(fireGlobals){globalEventContext.trigger("ajaxComplete",[jqXHR,s]); // Handle the global AJAX counter
	if(! --jQuery.active){jQuery.event.trigger("ajaxStop");}}}return jqXHR;},getJSON:function getJSON(url,data,callback){return jQuery.get(url,data,callback,"json");},getScript:function getScript(url,callback){return jQuery.get(url,undefined,callback,"script");}});jQuery.each(["get","post"],function(i,method){jQuery[method]=function(url,data,callback,type){ // Shift arguments if data argument was omitted
	if(jQuery.isFunction(data)){type=type||callback;callback=data;data=undefined;} // The url can be an options object (which then must have .url)
	return jQuery.ajax(jQuery.extend({url:url,type:method,dataType:type,data:data,success:callback},jQuery.isPlainObject(url)&&url));};});jQuery._evalUrl=function(url){return jQuery.ajax({url:url, // Make this explicit, since user can override this through ajaxSetup (#11264)
	type:"GET",dataType:"script",async:false,global:false,"throws":true});};jQuery.fn.extend({wrapAll:function wrapAll(html){var wrap;if(jQuery.isFunction(html)){return this.each(function(i){jQuery(this).wrapAll(html.call(this,i));});}if(this[0]){ // The elements to wrap the target around
	wrap=jQuery(html,this[0].ownerDocument).eq(0).clone(true);if(this[0].parentNode){wrap.insertBefore(this[0]);}wrap.map(function(){var elem=this;while(elem.firstElementChild){elem=elem.firstElementChild;}return elem;}).append(this);}return this;},wrapInner:function wrapInner(html){if(jQuery.isFunction(html)){return this.each(function(i){jQuery(this).wrapInner(html.call(this,i));});}return this.each(function(){var self=jQuery(this),contents=self.contents();if(contents.length){contents.wrapAll(html);}else {self.append(html);}});},wrap:function wrap(html){var isFunction=jQuery.isFunction(html);return this.each(function(i){jQuery(this).wrapAll(isFunction?html.call(this,i):html);});},unwrap:function unwrap(){return this.parent().each(function(){if(!jQuery.nodeName(this,"body")){jQuery(this).replaceWith(this.childNodes);}}).end();}});jQuery.expr.filters.hidden=function(elem){return !jQuery.expr.filters.visible(elem);};jQuery.expr.filters.visible=function(elem){ // Support: Opera <= 12.12
	// Opera reports offsetWidths and offsetHeights less than zero on some elements
	// Use OR instead of AND as the element is not visible if either is true
	// See tickets #10406 and #13132
	return elem.offsetWidth>0||elem.offsetHeight>0||elem.getClientRects().length>0;};var r20=/%20/g,rbracket=/\[\]$/,rCRLF=/\r?\n/g,rsubmitterTypes=/^(?:submit|button|image|reset|file)$/i,rsubmittable=/^(?:input|select|textarea|keygen)/i;function buildParams(prefix,obj,traditional,add){var name;if(jQuery.isArray(obj)){ // Serialize array item.
	jQuery.each(obj,function(i,v){if(traditional||rbracket.test(prefix)){ // Treat each array item as a scalar.
	add(prefix,v);}else { // Item is non-scalar (array or object), encode its numeric index.
	buildParams(prefix+"["+((typeof v==="undefined"?"undefined":_typeof(v))==="object"&&v!=null?i:"")+"]",v,traditional,add);}});}else if(!traditional&&jQuery.type(obj)==="object"){ // Serialize object item.
	for(name in obj){buildParams(prefix+"["+name+"]",obj[name],traditional,add);}}else { // Serialize scalar item.
	add(prefix,obj);}} // Serialize an array of form elements or a set of
	// key/values into a query string
	jQuery.param=function(a,traditional){var prefix,s=[],add=function add(key,value){ // If value is a function, invoke it and return its value
	value=jQuery.isFunction(value)?value():value==null?"":value;s[s.length]=encodeURIComponent(key)+"="+encodeURIComponent(value);}; // Set traditional to true for jQuery <= 1.3.2 behavior.
	if(traditional===undefined){traditional=jQuery.ajaxSettings&&jQuery.ajaxSettings.traditional;} // If an array was passed in, assume that it is an array of form elements.
	if(jQuery.isArray(a)||a.jquery&&!jQuery.isPlainObject(a)){ // Serialize the form elements
	jQuery.each(a,function(){add(this.name,this.value);});}else { // If traditional, encode the "old" way (the way 1.3.2 or older
	// did it), otherwise encode params recursively.
	for(prefix in a){buildParams(prefix,a[prefix],traditional,add);}} // Return the resulting serialization
	return s.join("&").replace(r20,"+");};jQuery.fn.extend({serialize:function serialize(){return jQuery.param(this.serializeArray());},serializeArray:function serializeArray(){return this.map(function(){ // Can add propHook for "elements" to filter or add form elements
	var elements=jQuery.prop(this,"elements");return elements?jQuery.makeArray(elements):this;}).filter(function(){var type=this.type; // Use .is( ":disabled" ) so that fieldset[disabled] works
	return this.name&&!jQuery(this).is(":disabled")&&rsubmittable.test(this.nodeName)&&!rsubmitterTypes.test(type)&&(this.checked||!rcheckableType.test(type));}).map(function(i,elem){var val=jQuery(this).val();return val==null?null:jQuery.isArray(val)?jQuery.map(val,function(val){return {name:elem.name,value:val.replace(rCRLF,"\r\n")};}):{name:elem.name,value:val.replace(rCRLF,"\r\n")};}).get();}});jQuery.ajaxSettings.xhr=function(){try{return new window.XMLHttpRequest();}catch(e){}};var xhrSuccessStatus={ // File protocol always yields status code 0, assume 200
	0:200, // Support: IE9
	// #1450: sometimes IE returns 1223 when it should be 204
	1223:204},xhrSupported=jQuery.ajaxSettings.xhr();support.cors=!!xhrSupported&&"withCredentials" in xhrSupported;support.ajax=xhrSupported=!!xhrSupported;jQuery.ajaxTransport(function(options){var _callback,errorCallback; // Cross domain only allowed if supported through XMLHttpRequest
	if(support.cors||xhrSupported&&!options.crossDomain){return {send:function send(headers,complete){var i,xhr=options.xhr();xhr.open(options.type,options.url,options.async,options.username,options.password); // Apply custom fields if provided
	if(options.xhrFields){for(i in options.xhrFields){xhr[i]=options.xhrFields[i];}} // Override mime type if needed
	if(options.mimeType&&xhr.overrideMimeType){xhr.overrideMimeType(options.mimeType);} // X-Requested-With header
	// For cross-domain requests, seeing as conditions for a preflight are
	// akin to a jigsaw puzzle, we simply never set it to be sure.
	// (it can always be set on a per-request basis or even using ajaxSetup)
	// For same-domain requests, won't change header if already provided.
	if(!options.crossDomain&&!headers["X-Requested-With"]){headers["X-Requested-With"]="XMLHttpRequest";} // Set headers
	for(i in headers){xhr.setRequestHeader(i,headers[i]);} // Callback
	_callback=function callback(type){return function(){if(_callback){_callback=errorCallback=xhr.onload=xhr.onerror=xhr.onabort=xhr.onreadystatechange=null;if(type==="abort"){xhr.abort();}else if(type==="error"){ // Support: IE9
	// On a manual native abort, IE9 throws
	// errors on any property access that is not readyState
	if(typeof xhr.status!=="number"){complete(0,"error");}else {complete( // File: protocol always yields status 0; see #8605, #14207
	xhr.status,xhr.statusText);}}else {complete(xhrSuccessStatus[xhr.status]||xhr.status,xhr.statusText, // Support: IE9 only
	// IE9 has no XHR2 but throws on binary (trac-11426)
	// For XHR2 non-text, let the caller handle it (gh-2498)
	(xhr.responseType||"text")!=="text"||typeof xhr.responseText!=="string"?{binary:xhr.response}:{text:xhr.responseText},xhr.getAllResponseHeaders());}}};}; // Listen to events
	xhr.onload=_callback();errorCallback=xhr.onerror=_callback("error"); // Support: IE9
	// Use onreadystatechange to replace onabort
	// to handle uncaught aborts
	if(xhr.onabort!==undefined){xhr.onabort=errorCallback;}else {xhr.onreadystatechange=function(){ // Check readyState before timeout as it changes
	if(xhr.readyState===4){ // Allow onerror to be called first,
	// but that will not handle a native abort
	// Also, save errorCallback to a variable
	// as xhr.onerror cannot be accessed
	window.setTimeout(function(){if(_callback){errorCallback();}});}};} // Create the abort callback
	_callback=_callback("abort");try{ // Do send the request (this may raise an exception)
	xhr.send(options.hasContent&&options.data||null);}catch(e){ // #14683: Only rethrow if this hasn't been notified as an error yet
	if(_callback){throw e;}}},abort:function abort(){if(_callback){_callback();}}};}}); // Install script dataType
	jQuery.ajaxSetup({accepts:{script:"text/javascript, application/javascript, "+"application/ecmascript, application/x-ecmascript"},contents:{script:/\b(?:java|ecma)script\b/},converters:{"text script":function textScript(text){jQuery.globalEval(text);return text;}}}); // Handle cache's special case and crossDomain
	jQuery.ajaxPrefilter("script",function(s){if(s.cache===undefined){s.cache=false;}if(s.crossDomain){s.type="GET";}}); // Bind script tag hack transport
	jQuery.ajaxTransport("script",function(s){ // This transport only deals with cross domain requests
	if(s.crossDomain){var script,_callback2;return {send:function send(_,complete){script=jQuery("<script>").prop({charset:s.scriptCharset,src:s.url}).on("load error",_callback2=function callback(evt){script.remove();_callback2=null;if(evt){complete(evt.type==="error"?404:200,evt.type);}}); // Use native DOM manipulation to avoid our domManip AJAX trickery
	document.head.appendChild(script[0]);},abort:function abort(){if(_callback2){_callback2();}}};}});var oldCallbacks=[],rjsonp=/(=)\?(?=&|$)|\?\?/; // Default jsonp settings
	jQuery.ajaxSetup({jsonp:"callback",jsonpCallback:function jsonpCallback(){var callback=oldCallbacks.pop()||jQuery.expando+"_"+nonce++;this[callback]=true;return callback;}}); // Detect, normalize options and install callbacks for jsonp requests
	jQuery.ajaxPrefilter("json jsonp",function(s,originalSettings,jqXHR){var callbackName,overwritten,responseContainer,jsonProp=s.jsonp!==false&&(rjsonp.test(s.url)?"url":typeof s.data==="string"&&(s.contentType||"").indexOf("application/x-www-form-urlencoded")===0&&rjsonp.test(s.data)&&"data"); // Handle iff the expected data type is "jsonp" or we have a parameter to set
	if(jsonProp||s.dataTypes[0]==="jsonp"){ // Get callback name, remembering preexisting value associated with it
	callbackName=s.jsonpCallback=jQuery.isFunction(s.jsonpCallback)?s.jsonpCallback():s.jsonpCallback; // Insert callback into url or form data
	if(jsonProp){s[jsonProp]=s[jsonProp].replace(rjsonp,"$1"+callbackName);}else if(s.jsonp!==false){s.url+=(rquery.test(s.url)?"&":"?")+s.jsonp+"="+callbackName;} // Use data converter to retrieve json after script execution
	s.converters["script json"]=function(){if(!responseContainer){jQuery.error(callbackName+" was not called");}return responseContainer[0];}; // Force json dataType
	s.dataTypes[0]="json"; // Install callback
	overwritten=window[callbackName];window[callbackName]=function(){responseContainer=arguments;}; // Clean-up function (fires after converters)
	jqXHR.always(function(){ // If previous value didn't exist - remove it
	if(overwritten===undefined){jQuery(window).removeProp(callbackName); // Otherwise restore preexisting value
	}else {window[callbackName]=overwritten;} // Save back as free
	if(s[callbackName]){ // Make sure that re-using the options doesn't screw things around
	s.jsonpCallback=originalSettings.jsonpCallback; // Save the callback name for future use
	oldCallbacks.push(callbackName);} // Call if it was a function and we have a response
	if(responseContainer&&jQuery.isFunction(overwritten)){overwritten(responseContainer[0]);}responseContainer=overwritten=undefined;}); // Delegate to script
	return "script";}}); // Support: Safari 8+
	// In Safari 8 documents created via document.implementation.createHTMLDocument
	// collapse sibling forms: the second one becomes a child of the first one.
	// Because of that, this security measure has to be disabled in Safari 8.
	// https://bugs.webkit.org/show_bug.cgi?id=137337
	support.createHTMLDocument=function(){var body=document.implementation.createHTMLDocument("").body;body.innerHTML="<form></form><form></form>";return body.childNodes.length===2;}(); // Argument "data" should be string of html
	// context (optional): If specified, the fragment will be created in this context,
	// defaults to document
	// keepScripts (optional): If true, will include scripts passed in the html string
	jQuery.parseHTML=function(data,context,keepScripts){if(!data||typeof data!=="string"){return null;}if(typeof context==="boolean"){keepScripts=context;context=false;} // Stop scripts or inline event handlers from being executed immediately
	// by using document.implementation
	context=context||(support.createHTMLDocument?document.implementation.createHTMLDocument(""):document);var parsed=rsingleTag.exec(data),scripts=!keepScripts&&[]; // Single tag
	if(parsed){return [context.createElement(parsed[1])];}parsed=buildFragment([data],context,scripts);if(scripts&&scripts.length){jQuery(scripts).remove();}return jQuery.merge([],parsed.childNodes);}; // Keep a copy of the old load method
	var _load=jQuery.fn.load; /**
	 * Load a url into a page
	 */jQuery.fn.load=function(url,params,callback){if(typeof url!=="string"&&_load){return _load.apply(this,arguments);}var selector,type,response,self=this,off=url.indexOf(" ");if(off>-1){selector=jQuery.trim(url.slice(off));url=url.slice(0,off);} // If it's a function
	if(jQuery.isFunction(params)){ // We assume that it's the callback
	callback=params;params=undefined; // Otherwise, build a param string
	}else if(params&&(typeof params==="undefined"?"undefined":_typeof(params))==="object"){type="POST";} // If we have elements to modify, make the request
	if(self.length>0){jQuery.ajax({url:url, // If "type" variable is undefined, then "GET" method will be used.
	// Make value of this field explicit since
	// user can override it through ajaxSetup method
	type:type||"GET",dataType:"html",data:params}).done(function(responseText){ // Save response for use in complete callback
	response=arguments;self.html(selector? // If a selector was specified, locate the right elements in a dummy div
	// Exclude scripts to avoid IE 'Permission Denied' errors
	jQuery("<div>").append(jQuery.parseHTML(responseText)).find(selector): // Otherwise use the full result
	responseText); // If the request succeeds, this function gets "data", "status", "jqXHR"
	// but they are ignored because response was set above.
	// If it fails, this function gets "jqXHR", "status", "error"
	}).always(callback&&function(jqXHR,status){self.each(function(){callback.apply(self,response||[jqXHR.responseText,status,jqXHR]);});});}return this;}; // Attach a bunch of functions for handling common AJAX events
	jQuery.each(["ajaxStart","ajaxStop","ajaxComplete","ajaxError","ajaxSuccess","ajaxSend"],function(i,type){jQuery.fn[type]=function(fn){return this.on(type,fn);};});jQuery.expr.filters.animated=function(elem){return jQuery.grep(jQuery.timers,function(fn){return elem===fn.elem;}).length;}; /**
	 * Gets a window from an element
	 */function getWindow(elem){return jQuery.isWindow(elem)?elem:elem.nodeType===9&&elem.defaultView;}jQuery.offset={setOffset:function setOffset(elem,options,i){var curPosition,curLeft,curCSSTop,curTop,curOffset,curCSSLeft,calculatePosition,position=jQuery.css(elem,"position"),curElem=jQuery(elem),props={}; // Set position first, in-case top/left are set even on static elem
	if(position==="static"){elem.style.position="relative";}curOffset=curElem.offset();curCSSTop=jQuery.css(elem,"top");curCSSLeft=jQuery.css(elem,"left");calculatePosition=(position==="absolute"||position==="fixed")&&(curCSSTop+curCSSLeft).indexOf("auto")>-1; // Need to be able to calculate position if either
	// top or left is auto and position is either absolute or fixed
	if(calculatePosition){curPosition=curElem.position();curTop=curPosition.top;curLeft=curPosition.left;}else {curTop=parseFloat(curCSSTop)||0;curLeft=parseFloat(curCSSLeft)||0;}if(jQuery.isFunction(options)){ // Use jQuery.extend here to allow modification of coordinates argument (gh-1848)
	options=options.call(elem,i,jQuery.extend({},curOffset));}if(options.top!=null){props.top=options.top-curOffset.top+curTop;}if(options.left!=null){props.left=options.left-curOffset.left+curLeft;}if("using" in options){options.using.call(elem,props);}else {curElem.css(props);}}};jQuery.fn.extend({offset:function offset(options){if(arguments.length){return options===undefined?this:this.each(function(i){jQuery.offset.setOffset(this,options,i);});}var docElem,win,elem=this[0],box={top:0,left:0},doc=elem&&elem.ownerDocument;if(!doc){return;}docElem=doc.documentElement; // Make sure it's not a disconnected DOM node
	if(!jQuery.contains(docElem,elem)){return box;}box=elem.getBoundingClientRect();win=getWindow(doc);return {top:box.top+win.pageYOffset-docElem.clientTop,left:box.left+win.pageXOffset-docElem.clientLeft};},position:function position(){if(!this[0]){return;}var offsetParent,offset,elem=this[0],parentOffset={top:0,left:0}; // Fixed elements are offset from window (parentOffset = {top:0, left: 0},
	// because it is its only offset parent
	if(jQuery.css(elem,"position")==="fixed"){ // Assume getBoundingClientRect is there when computed position is fixed
	offset=elem.getBoundingClientRect();}else { // Get *real* offsetParent
	offsetParent=this.offsetParent(); // Get correct offsets
	offset=this.offset();if(!jQuery.nodeName(offsetParent[0],"html")){parentOffset=offsetParent.offset();} // Add offsetParent borders
	// Subtract offsetParent scroll positions
	parentOffset.top+=jQuery.css(offsetParent[0],"borderTopWidth",true)-offsetParent.scrollTop();parentOffset.left+=jQuery.css(offsetParent[0],"borderLeftWidth",true)-offsetParent.scrollLeft();} // Subtract parent offsets and element margins
	return {top:offset.top-parentOffset.top-jQuery.css(elem,"marginTop",true),left:offset.left-parentOffset.left-jQuery.css(elem,"marginLeft",true)};}, // This method will return documentElement in the following cases:
	// 1) For the element inside the iframe without offsetParent, this method will return
	//    documentElement of the parent window
	// 2) For the hidden or detached element
	// 3) For body or html element, i.e. in case of the html node - it will return itself
	//
	// but those exceptions were never presented as a real life use-cases
	// and might be considered as more preferable results.
	//
	// This logic, however, is not guaranteed and can change at any point in the future
	offsetParent:function offsetParent(){return this.map(function(){var offsetParent=this.offsetParent;while(offsetParent&&jQuery.css(offsetParent,"position")==="static"){offsetParent=offsetParent.offsetParent;}return offsetParent||documentElement;});}}); // Create scrollLeft and scrollTop methods
	jQuery.each({scrollLeft:"pageXOffset",scrollTop:"pageYOffset"},function(method,prop){var top="pageYOffset"===prop;jQuery.fn[method]=function(val){return access(this,function(elem,method,val){var win=getWindow(elem);if(val===undefined){return win?win[prop]:elem[method];}if(win){win.scrollTo(!top?val:win.pageXOffset,top?val:win.pageYOffset);}else {elem[method]=val;}},method,val,arguments.length);};}); // Support: Safari<7-8+, Chrome<37-44+
	// Add the top/left cssHooks using jQuery.fn.position
	// Webkit bug: https://bugs.webkit.org/show_bug.cgi?id=29084
	// Blink bug: https://code.google.com/p/chromium/issues/detail?id=229280
	// getComputedStyle returns percent when specified for top/left/bottom/right;
	// rather than make the css module depend on the offset module, just check for it here
	jQuery.each(["top","left"],function(i,prop){jQuery.cssHooks[prop]=addGetHookIf(support.pixelPosition,function(elem,computed){if(computed){computed=curCSS(elem,prop); // If curCSS returns percentage, fallback to offset
	return rnumnonpx.test(computed)?jQuery(elem).position()[prop]+"px":computed;}});}); // Create innerHeight, innerWidth, height, width, outerHeight and outerWidth methods
	jQuery.each({Height:"height",Width:"width"},function(name,type){jQuery.each({padding:"inner"+name,content:type,"":"outer"+name},function(defaultExtra,funcName){ // Margin is only for outerHeight, outerWidth
	jQuery.fn[funcName]=function(margin,value){var chainable=arguments.length&&(defaultExtra||typeof margin!=="boolean"),extra=defaultExtra||(margin===true||value===true?"margin":"border");return access(this,function(elem,type,value){var doc;if(jQuery.isWindow(elem)){ // As of 5/8/2012 this will yield incorrect results for Mobile Safari, but there
	// isn't a whole lot we can do. See pull request at this URL for discussion:
	// https://github.com/jquery/jquery/pull/764
	return elem.document.documentElement["client"+name];} // Get document width or height
	if(elem.nodeType===9){doc=elem.documentElement; // Either scroll[Width/Height] or offset[Width/Height] or client[Width/Height],
	// whichever is greatest
	return Math.max(elem.body["scroll"+name],doc["scroll"+name],elem.body["offset"+name],doc["offset"+name],doc["client"+name]);}return value===undefined? // Get width or height on the element, requesting but not forcing parseFloat
	jQuery.css(elem,type,extra): // Set width or height on the element
	jQuery.style(elem,type,value,extra);},type,chainable?margin:undefined,chainable,null);};});});jQuery.fn.extend({bind:function bind(types,data,fn){return this.on(types,null,data,fn);},unbind:function unbind(types,fn){return this.off(types,null,fn);},delegate:function delegate(selector,types,data,fn){return this.on(types,selector,data,fn);},undelegate:function undelegate(selector,types,fn){ // ( namespace ) or ( selector, types [, fn] )
	return arguments.length===1?this.off(selector,"**"):this.off(types,selector||"**",fn);},size:function size(){return this.length;}});jQuery.fn.andSelf=jQuery.fn.addBack; // Register as a named AMD module, since jQuery can be concatenated with other
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
	_$=window.$;jQuery.noConflict=function(deep){if(window.$===jQuery){window.$=_$;}if(deep&&window.jQuery===jQuery){window.jQuery=_jQuery;}return jQuery;}; // Expose jQuery and $ identifiers, even in AMD
	// (#7102#comment:10, https://github.com/jquery/jquery/pull/557)
	// and CommonJS for browser emulators (#13566)
	if(!noGlobal){window.jQuery=window.$=jQuery;}return jQuery;});
	/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(5)(module)))

/***/ },
/* 5 */
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
/* 6 */
/***/ function(module, exports, __webpack_require__) {

	"use strict";var _typeof=typeof Symbol==="function"&&typeof Symbol.iterator==="symbol"?function(obj){return typeof obj;}:function(obj){return obj&&typeof Symbol==="function"&&obj.constructor===Symbol?"symbol":typeof obj;};var jQuery=__webpack_require__(4); /*! jQuery UI - v1.10.3 - 2013-05-03
	* http://jqueryui.com
	* Includes: jquery.ui.core.js, jquery.ui.widget.js, jquery.ui.mouse.js, jquery.ui.draggable.js, jquery.ui.droppable.js, jquery.ui.resizable.js, jquery.ui.selectable.js, jquery.ui.sortable.js, jquery.ui.effect.js, jquery.ui.accordion.js, jquery.ui.autocomplete.js, jquery.ui.button.js, jquery.ui.datepicker.js, jquery.ui.dialog.js, jquery.ui.effect-blind.js, jquery.ui.effect-bounce.js, jquery.ui.effect-clip.js, jquery.ui.effect-drop.js, jquery.ui.effect-explode.js, jquery.ui.effect-fade.js, jquery.ui.effect-fold.js, jquery.ui.effect-highlight.js, jquery.ui.effect-pulsate.js, jquery.ui.effect-scale.js, jquery.ui.effect-shake.js, jquery.ui.effect-slide.js, jquery.ui.effect-transfer.js, jquery.ui.menu.js, jquery.ui.position.js, jquery.ui.progressbar.js, jquery.ui.slider.js, jquery.ui.spinner.js, jquery.ui.tabs.js, jquery.ui.tooltip.js
	* Copyright 2013 jQuery Foundation and other contributors; Licensed MIT */(function($,undefined){var uuid=0,runiqueId=/^ui-id-\d+$/; // $.ui might exist from components with no dependencies, e.g., $.ui.position
	$.ui=$.ui||{};$.extend($.ui,{version:"1.10.3",keyCode:{BACKSPACE:8,COMMA:188,DELETE:46,DOWN:40,END:35,ENTER:13,ESCAPE:27,HOME:36,LEFT:37,NUMPAD_ADD:107,NUMPAD_DECIMAL:110,NUMPAD_DIVIDE:111,NUMPAD_ENTER:108,NUMPAD_MULTIPLY:106,NUMPAD_SUBTRACT:109,PAGE_DOWN:34,PAGE_UP:33,PERIOD:190,RIGHT:39,SPACE:32,TAB:9,UP:38}}); // plugins
	$.fn.extend({focus:function(orig){return function(delay,fn){return typeof delay==="number"?this.each(function(){var elem=this;setTimeout(function(){$(elem).focus();if(fn){fn.call(elem);}},delay);}):orig.apply(this,arguments);};}($.fn.focus),scrollParent:function scrollParent(){var scrollParent;if($.ui.ie&&/(static|relative)/.test(this.css("position"))||/absolute/.test(this.css("position"))){scrollParent=this.parents().filter(function(){return (/(relative|absolute|fixed)/.test($.css(this,"position"))&&/(auto|scroll)/.test($.css(this,"overflow")+$.css(this,"overflow-y")+$.css(this,"overflow-x")));}).eq(0);}else {scrollParent=this.parents().filter(function(){return (/(auto|scroll)/.test($.css(this,"overflow")+$.css(this,"overflow-y")+$.css(this,"overflow-x")));}).eq(0);}return (/fixed/.test(this.css("position"))||!scrollParent.length?$(document):scrollParent);},zIndex:function zIndex(_zIndex){if(_zIndex!==undefined){return this.css("zIndex",_zIndex);}if(this.length){var elem=$(this[0]),position,value;while(elem.length&&elem[0]!==document){ // Ignore z-index if position is set to a value where z-index is ignored by the browser
	// This makes behavior of this function consistent across browsers
	// WebKit always returns auto if the element is positioned
	position=elem.css("position");if(position==="absolute"||position==="relative"||position==="fixed"){ // IE returns 0 when zIndex is not specified
	// other browsers return a string
	// we ignore the case of nested elements with an explicit value of 0
	// <div style="z-index: -10;"><div style="z-index: 0;"></div></div>
	value=parseInt(elem.css("zIndex"),10);if(!isNaN(value)&&value!==0){return value;}}elem=elem.parent();}}return 0;},uniqueId:function uniqueId(){return this.each(function(){if(!this.id){this.id="ui-id-"+ ++uuid;}});},removeUniqueId:function removeUniqueId(){return this.each(function(){if(runiqueId.test(this.id)){$(this).removeAttr("id");}});}}); // selectors
	function _focusable(element,isTabIndexNotNaN){var map,mapName,img,nodeName=element.nodeName.toLowerCase();if("area"===nodeName){map=element.parentNode;mapName=map.name;if(!element.href||!mapName||map.nodeName.toLowerCase()!=="map"){return false;}img=$("img[usemap=#"+mapName+"]")[0];return !!img&&visible(img);}return (/input|select|textarea|button|object/.test(nodeName)?!element.disabled:"a"===nodeName?element.href||isTabIndexNotNaN:isTabIndexNotNaN)&& // the element and all of its ancestors must be visible
	visible(element);}function visible(element){return $.expr.filters.visible(element)&&!$(element).parents().addBack().filter(function(){return $.css(this,"visibility")==="hidden";}).length;}$.extend($.expr[":"],{data:$.expr.createPseudo?$.expr.createPseudo(function(dataName){return function(elem){return !!$.data(elem,dataName);};}): // support: jQuery <1.8
	function(elem,i,match){return !!$.data(elem,match[3]);},focusable:function focusable(element){return _focusable(element,!isNaN($.attr(element,"tabindex")));},tabbable:function tabbable(element){var tabIndex=$.attr(element,"tabindex"),isTabIndexNaN=isNaN(tabIndex);return (isTabIndexNaN||tabIndex>=0)&&_focusable(element,!isTabIndexNaN);}}); // support: jQuery <1.8
	if(!$("<a>").outerWidth(1).jquery){$.each(["Width","Height"],function(i,name){var side=name==="Width"?["Left","Right"]:["Top","Bottom"],type=name.toLowerCase(),orig={innerWidth:$.fn.innerWidth,innerHeight:$.fn.innerHeight,outerWidth:$.fn.outerWidth,outerHeight:$.fn.outerHeight};function reduce(elem,size,border,margin){$.each(side,function(){size-=parseFloat($.css(elem,"padding"+this))||0;if(border){size-=parseFloat($.css(elem,"border"+this+"Width"))||0;}if(margin){size-=parseFloat($.css(elem,"margin"+this))||0;}});return size;}$.fn["inner"+name]=function(size){if(size===undefined){return orig["inner"+name].call(this);}return this.each(function(){$(this).css(type,reduce(this,size)+"px");});};$.fn["outer"+name]=function(size,margin){if(typeof size!=="number"){return orig["outer"+name].call(this,size);}return this.each(function(){$(this).css(type,reduce(this,size,true,margin)+"px");});};});} // support: jQuery <1.8
	if(!$.fn.addBack){$.fn.addBack=function(selector){return this.add(selector==null?this.prevObject:this.prevObject.filter(selector));};} // support: jQuery 1.6.1, 1.6.2 (http://bugs.jquery.com/ticket/9413)
	if($("<a>").data("a-b","a").removeData("a-b").data("a-b")){$.fn.removeData=function(removeData){return function(key){if(arguments.length){return removeData.call(this,$.camelCase(key));}else {return removeData.call(this);}};}($.fn.removeData);} // deprecated
	$.ui.ie=!!/msie [\w.]+/.exec(navigator.userAgent.toLowerCase());$.support.selectstart="onselectstart" in document.createElement("div");$.fn.extend({disableSelection:function disableSelection(){return this.bind(($.support.selectstart?"selectstart":"mousedown")+".ui-disableSelection",function(event){event.preventDefault();});},enableSelection:function enableSelection(){return this.unbind(".ui-disableSelection");}});$.extend($.ui,{ // $.ui.plugin is deprecated. Use $.widget() extensions instead.
	plugin:{add:function add(module,option,set){var i,proto=$.ui[module].prototype;for(i in set){proto.plugins[i]=proto.plugins[i]||[];proto.plugins[i].push([option,set[i]]);}},call:function call(instance,name,args){var i,set=instance.plugins[name];if(!set||!instance.element[0].parentNode||instance.element[0].parentNode.nodeType===11){return;}for(i=0;i<set.length;i++){if(instance.options[set[i][0]]){set[i][1].apply(instance.element,args);}}}}, // only used by resizable
	hasScroll:function hasScroll(el,a){ //If overflow is hidden, the element might have extra content, but the user wants to hide it
	if($(el).css("overflow")==="hidden"){return false;}var scroll=a&&a==="left"?"scrollLeft":"scrollTop",has=false;if(el[scroll]>0){return true;} // TODO: determine which cases actually cause this to happen
	// if the element doesn't have the scroll set, see if it's possible to
	// set the scroll
	el[scroll]=1;has=el[scroll]>0;el[scroll]=0;return has;}});})(jQuery);(function($,undefined){var uuid=0,slice=Array.prototype.slice,_cleanData=$.cleanData;$.cleanData=function(elems){for(var i=0,elem;(elem=elems[i])!=null;i++){try{$(elem).triggerHandler("remove"); // http://bugs.jquery.com/ticket/8235
	}catch(e){}}_cleanData(elems);};$.widget=function(name,base,prototype){var fullName,existingConstructor,constructor,basePrototype, // proxiedPrototype allows the provided prototype to remain unmodified
	// so that it can be used as a mixin for multiple widgets (#8876)
	proxiedPrototype={},namespace=name.split(".")[0];name=name.split(".")[1];fullName=namespace+"-"+name;if(!prototype){prototype=base;base=$.Widget;} // create selector for plugin
	$.expr[":"][fullName.toLowerCase()]=function(elem){return !!$.data(elem,fullName);};$[namespace]=$[namespace]||{};existingConstructor=$[namespace][name];constructor=$[namespace][name]=function(options,element){ // allow instantiation without "new" keyword
	if(!this._createWidget){return new constructor(options,element);} // allow instantiation without initializing for simple inheritance
	// must use "new" keyword (the code above always passes args)
	if(arguments.length){this._createWidget(options,element);}}; // extend with the existing constructor to carry over any static properties
	$.extend(constructor,existingConstructor,{version:prototype.version, // copy the object used to create the prototype in case we need to
	// redefine the widget later
	_proto:$.extend({},prototype), // track widgets that inherit from this widget in case this widget is
	// redefined after a widget inherits from it
	_childConstructors:[]});basePrototype=new base(); // we need to make the options hash a property directly on the new instance
	// otherwise we'll modify the options hash on the prototype that we're
	// inheriting from
	basePrototype.options=$.widget.extend({},basePrototype.options);$.each(prototype,function(prop,value){if(!$.isFunction(value)){proxiedPrototype[prop]=value;return;}proxiedPrototype[prop]=function(){var _super=function _super(){return base.prototype[prop].apply(this,arguments);},_superApply=function _superApply(args){return base.prototype[prop].apply(this,args);};return function(){var __super=this._super,__superApply=this._superApply,returnValue;this._super=_super;this._superApply=_superApply;returnValue=value.apply(this,arguments);this._super=__super;this._superApply=__superApply;return returnValue;};}();});constructor.prototype=$.widget.extend(basePrototype,{ // TODO: remove support for widgetEventPrefix
	// always use the name + a colon as the prefix, e.g., draggable:start
	// don't prefix for widgets that aren't DOM-based
	widgetEventPrefix:existingConstructor?basePrototype.widgetEventPrefix:name},proxiedPrototype,{constructor:constructor,namespace:namespace,widgetName:name,widgetFullName:fullName}); // If this widget is being redefined then we need to find all widgets that
	// are inheriting from it and redefine all of them so that they inherit from
	// the new version of this widget. We're essentially trying to replace one
	// level in the prototype chain.
	if(existingConstructor){$.each(existingConstructor._childConstructors,function(i,child){var childPrototype=child.prototype; // redefine the child widget using the same prototype that was
	// originally used, but inherit from the new version of the base
	$.widget(childPrototype.namespace+"."+childPrototype.widgetName,constructor,child._proto);}); // remove the list of existing child constructors from the old constructor
	// so the old child constructors can be garbage collected
	delete existingConstructor._childConstructors;}else {base._childConstructors.push(constructor);}$.widget.bridge(name,constructor);};$.widget.extend=function(target){var input=slice.call(arguments,1),inputIndex=0,inputLength=input.length,key,value;for(;inputIndex<inputLength;inputIndex++){for(key in input[inputIndex]){value=input[inputIndex][key];if(input[inputIndex].hasOwnProperty(key)&&value!==undefined){ // Clone objects
	if($.isPlainObject(value)){target[key]=$.isPlainObject(target[key])?$.widget.extend({},target[key],value): // Don't extend strings, arrays, etc. with objects
	$.widget.extend({},value); // Copy everything else by reference
	}else {target[key]=value;}}}}return target;};$.widget.bridge=function(name,object){var fullName=object.prototype.widgetFullName||name;$.fn[name]=function(options){var isMethodCall=typeof options==="string",args=slice.call(arguments,1),returnValue=this; // allow multiple hashes to be passed on init
	options=!isMethodCall&&args.length?$.widget.extend.apply(null,[options].concat(args)):options;if(isMethodCall){this.each(function(){var methodValue,instance=$.data(this,fullName);if(!instance){return $.error("cannot call methods on "+name+" prior to initialization; "+"attempted to call method '"+options+"'");}if(!$.isFunction(instance[options])||options.charAt(0)==="_"){return $.error("no such method '"+options+"' for "+name+" widget instance");}methodValue=instance[options].apply(instance,args);if(methodValue!==instance&&methodValue!==undefined){returnValue=methodValue&&methodValue.jquery?returnValue.pushStack(methodValue.get()):methodValue;return false;}});}else {this.each(function(){var instance=$.data(this,fullName);if(instance){instance.option(options||{})._init();}else {$.data(this,fullName,new object(options,this));}});}return returnValue;};};$.Widget=function() /* options, element */{};$.Widget._childConstructors=[];$.Widget.prototype={widgetName:"widget",widgetEventPrefix:"",defaultElement:"<div>",options:{disabled:false, // callbacks
	create:null},_createWidget:function _createWidget(options,element){element=$(element||this.defaultElement||this)[0];this.element=$(element);this.uuid=uuid++;this.eventNamespace="."+this.widgetName+this.uuid;this.options=$.widget.extend({},this.options,this._getCreateOptions(),options);this.bindings=$();this.hoverable=$();this.focusable=$();if(element!==this){$.data(element,this.widgetFullName,this);this._on(true,this.element,{remove:function remove(event){if(event.target===element){this.destroy();}}});this.document=$(element.style? // element within the document
	element.ownerDocument: // element is window or document
	element.document||element);this.window=$(this.document[0].defaultView||this.document[0].parentWindow);}this._create();this._trigger("create",null,this._getCreateEventData());this._init();},_getCreateOptions:$.noop,_getCreateEventData:$.noop,_create:$.noop,_init:$.noop,destroy:function destroy(){this._destroy(); // we can probably remove the unbind calls in 2.0
	// all event bindings should go through this._on()
	this.element.unbind(this.eventNamespace) // 1.9 BC for #7810
	// TODO remove dual storage
	.removeData(this.widgetName).removeData(this.widgetFullName) // support: jquery <1.6.3
	// http://bugs.jquery.com/ticket/9413
	.removeData($.camelCase(this.widgetFullName));this.widget().unbind(this.eventNamespace).removeAttr("aria-disabled").removeClass(this.widgetFullName+"-disabled "+"ui-state-disabled"); // clean up events and states
	this.bindings.unbind(this.eventNamespace);this.hoverable.removeClass("ui-state-hover");this.focusable.removeClass("ui-state-focus");},_destroy:$.noop,widget:function widget(){return this.element;},option:function option(key,value){var options=key,parts,curOption,i;if(arguments.length===0){ // don't return a reference to the internal hash
	return $.widget.extend({},this.options);}if(typeof key==="string"){ // handle nested keys, e.g., "foo.bar" => { foo: { bar: ___ } }
	options={};parts=key.split(".");key=parts.shift();if(parts.length){curOption=options[key]=$.widget.extend({},this.options[key]);for(i=0;i<parts.length-1;i++){curOption[parts[i]]=curOption[parts[i]]||{};curOption=curOption[parts[i]];}key=parts.pop();if(value===undefined){return curOption[key]===undefined?null:curOption[key];}curOption[key]=value;}else {if(value===undefined){return this.options[key]===undefined?null:this.options[key];}options[key]=value;}}this._setOptions(options);return this;},_setOptions:function _setOptions(options){var key;for(key in options){this._setOption(key,options[key]);}return this;},_setOption:function _setOption(key,value){this.options[key]=value;if(key==="disabled"){this.widget().toggleClass(this.widgetFullName+"-disabled ui-state-disabled",!!value).attr("aria-disabled",value);this.hoverable.removeClass("ui-state-hover");this.focusable.removeClass("ui-state-focus");}return this;},enable:function enable(){return this._setOption("disabled",false);},disable:function disable(){return this._setOption("disabled",true);},_on:function _on(suppressDisabledCheck,element,handlers){var delegateElement,instance=this; // no suppressDisabledCheck flag, shuffle arguments
	if(typeof suppressDisabledCheck!=="boolean"){handlers=element;element=suppressDisabledCheck;suppressDisabledCheck=false;} // no element argument, shuffle and use this.element
	if(!handlers){handlers=element;element=this.element;delegateElement=this.widget();}else { // accept selectors, DOM elements
	element=delegateElement=$(element);this.bindings=this.bindings.add(element);}$.each(handlers,function(event,handler){function handlerProxy(){ // allow widgets to customize the disabled handling
	// - disabled as an array instead of boolean
	// - disabled class as method for disabling individual parts
	if(!suppressDisabledCheck&&(instance.options.disabled===true||$(this).hasClass("ui-state-disabled"))){return;}return (typeof handler==="string"?instance[handler]:handler).apply(instance,arguments);} // copy the guid so direct unbinding works
	if(typeof handler!=="string"){handlerProxy.guid=handler.guid=handler.guid||handlerProxy.guid||$.guid++;}var match=event.match(/^(\w+)\s*(.*)$/),eventName=match[1]+instance.eventNamespace,selector=match[2];if(selector){delegateElement.delegate(selector,eventName,handlerProxy);}else {element.bind(eventName,handlerProxy);}});},_off:function _off(element,eventName){eventName=(eventName||"").split(" ").join(this.eventNamespace+" ")+this.eventNamespace;element.unbind(eventName).undelegate(eventName);},_delay:function _delay(handler,delay){function handlerProxy(){return (typeof handler==="string"?instance[handler]:handler).apply(instance,arguments);}var instance=this;return setTimeout(handlerProxy,delay||0);},_hoverable:function _hoverable(element){this.hoverable=this.hoverable.add(element);this._on(element,{mouseenter:function mouseenter(event){$(event.currentTarget).addClass("ui-state-hover");},mouseleave:function mouseleave(event){$(event.currentTarget).removeClass("ui-state-hover");}});},_focusable:function _focusable(element){this.focusable=this.focusable.add(element);this._on(element,{focusin:function focusin(event){$(event.currentTarget).addClass("ui-state-focus");},focusout:function focusout(event){$(event.currentTarget).removeClass("ui-state-focus");}});},_trigger:function _trigger(type,event,data){var prop,orig,callback=this.options[type];data=data||{};event=$.Event(event);event.type=(type===this.widgetEventPrefix?type:this.widgetEventPrefix+type).toLowerCase(); // the original event may come from any element
	// so we need to reset the target on the new event
	event.target=this.element[0]; // copy original event properties over to the new event
	orig=event.originalEvent;if(orig){for(prop in orig){if(!(prop in event)){event[prop]=orig[prop];}}}this.element.trigger(event,data);return !($.isFunction(callback)&&callback.apply(this.element[0],[event].concat(data))===false||event.isDefaultPrevented());}};$.each({show:"fadeIn",hide:"fadeOut"},function(method,defaultEffect){$.Widget.prototype["_"+method]=function(element,options,callback){if(typeof options==="string"){options={effect:options};}var hasOptions,effectName=!options?method:options===true||typeof options==="number"?defaultEffect:options.effect||defaultEffect;options=options||{};if(typeof options==="number"){options={duration:options};}hasOptions=!$.isEmptyObject(options);options.complete=callback;if(options.delay){element.delay(options.delay);}if(hasOptions&&$.effects&&$.effects.effect[effectName]){element[method](options);}else if(effectName!==method&&element[effectName]){element[effectName](options.duration,options.easing,callback);}else {element.queue(function(next){$(this)[method]();if(callback){callback.call(element[0]);}next();});}};});})(jQuery);(function($,undefined){var mouseHandled=false;$(document).mouseup(function(){mouseHandled=false;});$.widget("ui.mouse",{version:"1.10.3",options:{cancel:"input,textarea,button,select,option",distance:1,delay:0},_mouseInit:function _mouseInit(){var that=this;this.element.bind("mousedown."+this.widgetName,function(event){return that._mouseDown(event);}).bind("click."+this.widgetName,function(event){if(true===$.data(event.target,that.widgetName+".preventClickEvent")){$.removeData(event.target,that.widgetName+".preventClickEvent");event.stopImmediatePropagation();return false;}});this.started=false;}, // TODO: make sure destroying one instance of mouse doesn't mess with
	// other instances of mouse
	_mouseDestroy:function _mouseDestroy(){this.element.unbind("."+this.widgetName);if(this._mouseMoveDelegate){$(document).unbind("mousemove."+this.widgetName,this._mouseMoveDelegate).unbind("mouseup."+this.widgetName,this._mouseUpDelegate);}},_mouseDown:function _mouseDown(event){ // don't let more than one widget handle mouseStart
	if(mouseHandled){return;} // we may have missed mouseup (out of window)
	this._mouseStarted&&this._mouseUp(event);this._mouseDownEvent=event;var that=this,btnIsLeft=event.which===1, // event.target.nodeName works around a bug in IE 8 with
	// disabled inputs (#7620)
	elIsCancel=typeof this.options.cancel==="string"&&event.target.nodeName?$(event.target).closest(this.options.cancel).length:false;if(!btnIsLeft||elIsCancel||!this._mouseCapture(event)){return true;}this.mouseDelayMet=!this.options.delay;if(!this.mouseDelayMet){this._mouseDelayTimer=setTimeout(function(){that.mouseDelayMet=true;},this.options.delay);}if(this._mouseDistanceMet(event)&&this._mouseDelayMet(event)){this._mouseStarted=this._mouseStart(event)!==false;if(!this._mouseStarted){event.preventDefault();return true;}} // Click event may never have fired (Gecko & Opera)
	if(true===$.data(event.target,this.widgetName+".preventClickEvent")){$.removeData(event.target,this.widgetName+".preventClickEvent");} // these delegates are required to keep context
	this._mouseMoveDelegate=function(event){return that._mouseMove(event);};this._mouseUpDelegate=function(event){return that._mouseUp(event);};$(document).bind("mousemove."+this.widgetName,this._mouseMoveDelegate).bind("mouseup."+this.widgetName,this._mouseUpDelegate);event.preventDefault();mouseHandled=true;return true;},_mouseMove:function _mouseMove(event){ // IE mouseup check - mouseup happened when mouse was out of window
	if($.ui.ie&&(!document.documentMode||document.documentMode<9)&&!event.button){return this._mouseUp(event);}if(this._mouseStarted){this._mouseDrag(event);return event.preventDefault();}if(this._mouseDistanceMet(event)&&this._mouseDelayMet(event)){this._mouseStarted=this._mouseStart(this._mouseDownEvent,event)!==false;this._mouseStarted?this._mouseDrag(event):this._mouseUp(event);}return !this._mouseStarted;},_mouseUp:function _mouseUp(event){$(document).unbind("mousemove."+this.widgetName,this._mouseMoveDelegate).unbind("mouseup."+this.widgetName,this._mouseUpDelegate);if(this._mouseStarted){this._mouseStarted=false;if(event.target===this._mouseDownEvent.target){$.data(event.target,this.widgetName+".preventClickEvent",true);}this._mouseStop(event);}return false;},_mouseDistanceMet:function _mouseDistanceMet(event){return Math.max(Math.abs(this._mouseDownEvent.pageX-event.pageX),Math.abs(this._mouseDownEvent.pageY-event.pageY))>=this.options.distance;},_mouseDelayMet:function _mouseDelayMet() /* event */{return this.mouseDelayMet;}, // These are placeholder methods, to be overriden by extending plugin
	_mouseStart:function _mouseStart() /* event */{},_mouseDrag:function _mouseDrag() /* event */{},_mouseStop:function _mouseStop() /* event */{},_mouseCapture:function _mouseCapture() /* event */{return true;}});})(jQuery);(function($,undefined){$.widget("ui.draggable",$.ui.mouse,{version:"1.10.3",widgetEventPrefix:"drag",options:{addClasses:true,appendTo:"parent",axis:false,connectToSortable:false,containment:false,cursor:"auto",cursorAt:false,grid:false,handle:false,helper:"original",iframeFix:false,opacity:false,refreshPositions:false,revert:false,revertDuration:500,scope:"default",scroll:true,scrollSensitivity:20,scrollSpeed:20,snap:false,snapMode:"both",snapTolerance:20,stack:false,zIndex:false, // callbacks
	drag:null,start:null,stop:null},_create:function _create(){if(this.options.helper==="original"&&!/^(?:r|a|f)/.test(this.element.css("position"))){this.element[0].style.position="relative";}if(this.options.addClasses){this.element.addClass("ui-draggable");}if(this.options.disabled){this.element.addClass("ui-draggable-disabled");}this._mouseInit();},_destroy:function _destroy(){this.element.removeClass("ui-draggable ui-draggable-dragging ui-draggable-disabled");this._mouseDestroy();},_mouseCapture:function _mouseCapture(event){var o=this.options; // among others, prevent a drag on a resizable-handle
	if(this.helper||o.disabled||$(event.target).closest(".ui-resizable-handle").length>0){return false;} //Quit if we're not on a valid handle
	this.handle=this._getHandle(event);if(!this.handle){return false;}$(o.iframeFix===true?"iframe":o.iframeFix).each(function(){$("<div class='ui-draggable-iframeFix' style='background: #fff;'></div>").css({width:this.offsetWidth+"px",height:this.offsetHeight+"px",position:"absolute",opacity:"0.001",zIndex:1000}).css($(this).offset()).appendTo("body");});return true;},_mouseStart:function _mouseStart(event){var o=this.options; //Create and append the visible helper
	this.helper=this._createHelper(event);this.helper.addClass("ui-draggable-dragging"); //Cache the helper size
	this._cacheHelperProportions(); //If ddmanager is used for droppables, set the global draggable
	if($.ui.ddmanager){$.ui.ddmanager.current=this;} /*
			 * - Position generation -
			 * This block generates everything position related - it's the core of draggables.
			 */ //Cache the margins of the original element
	this._cacheMargins(); //Store the helper's css position
	this.cssPosition=this.helper.css("position");this.scrollParent=this.helper.scrollParent();this.offsetParent=this.helper.offsetParent();this.offsetParentCssPosition=this.offsetParent.css("position"); //The element's absolute position on the page minus margins
	this.offset=this.positionAbs=this.element.offset();this.offset={top:this.offset.top-this.margins.top,left:this.offset.left-this.margins.left}; //Reset scroll cache
	this.offset.scroll=false;$.extend(this.offset,{click:{ //Where the click happened, relative to the element
	left:event.pageX-this.offset.left,top:event.pageY-this.offset.top},parent:this._getParentOffset(),relative:this._getRelativeOffset() //This is a relative to absolute position minus the actual position calculation - only used for relative positioned helper
	}); //Generate the original position
	this.originalPosition=this.position=this._generatePosition(event);this.originalPageX=event.pageX;this.originalPageY=event.pageY; //Adjust the mouse offset relative to the helper if "cursorAt" is supplied
	o.cursorAt&&this._adjustOffsetFromHelper(o.cursorAt); //Set a containment if given in the options
	this._setContainment(); //Trigger event + callbacks
	if(this._trigger("start",event)===false){this._clear();return false;} //Recache the helper size
	this._cacheHelperProportions(); //Prepare the droppable offsets
	if($.ui.ddmanager&&!o.dropBehaviour){$.ui.ddmanager.prepareOffsets(this,event);}this._mouseDrag(event,true); //Execute the drag once - this causes the helper not to be visible before getting its correct position
	//If the ddmanager is used for droppables, inform the manager that dragging has started (see #5003)
	if($.ui.ddmanager){$.ui.ddmanager.dragStart(this,event);}return true;},_mouseDrag:function _mouseDrag(event,noPropagation){ // reset any necessary cached properties (see #5009)
	if(this.offsetParentCssPosition==="fixed"){this.offset.parent=this._getParentOffset();} //Compute the helpers position
	this.position=this._generatePosition(event);this.positionAbs=this._convertPositionTo("absolute"); //Call plugins and callbacks and use the resulting position if something is returned
	if(!noPropagation){var ui=this._uiHash();if(this._trigger("drag",event,ui)===false){this._mouseUp({});return false;}this.position=ui.position;}if(!this.options.axis||this.options.axis!=="y"){this.helper[0].style.left=this.position.left+"px";}if(!this.options.axis||this.options.axis!=="x"){this.helper[0].style.top=this.position.top+"px";}if($.ui.ddmanager){$.ui.ddmanager.drag(this,event);}return false;},_mouseStop:function _mouseStop(event){ //If we are using droppables, inform the manager about the drop
	var that=this,dropped=false;if($.ui.ddmanager&&!this.options.dropBehaviour){dropped=$.ui.ddmanager.drop(this,event);} //if a drop comes from outside (a sortable)
	if(this.dropped){dropped=this.dropped;this.dropped=false;} //if the original element is no longer in the DOM don't bother to continue (see #8269)
	if(this.options.helper==="original"&&!$.contains(this.element[0].ownerDocument,this.element[0])){return false;}if(this.options.revert==="invalid"&&!dropped||this.options.revert==="valid"&&dropped||this.options.revert===true||$.isFunction(this.options.revert)&&this.options.revert.call(this.element,dropped)){$(this.helper).animate(this.originalPosition,parseInt(this.options.revertDuration,10),function(){if(that._trigger("stop",event)!==false){that._clear();}});}else {if(this._trigger("stop",event)!==false){this._clear();}}return false;},_mouseUp:function _mouseUp(event){ //Remove frame helpers
	$("div.ui-draggable-iframeFix").each(function(){this.parentNode.removeChild(this);}); //If the ddmanager is used for droppables, inform the manager that dragging has stopped (see #5003)
	if($.ui.ddmanager){$.ui.ddmanager.dragStop(this,event);}return $.ui.mouse.prototype._mouseUp.call(this,event);},cancel:function cancel(){if(this.helper.is(".ui-draggable-dragging")){this._mouseUp({});}else {this._clear();}return this;},_getHandle:function _getHandle(event){return this.options.handle?!!$(event.target).closest(this.element.find(this.options.handle)).length:true;},_createHelper:function _createHelper(event){var o=this.options,helper=$.isFunction(o.helper)?$(o.helper.apply(this.element[0],[event])):o.helper==="clone"?this.element.clone().removeAttr("id"):this.element;if(!helper.parents("body").length){helper.appendTo(o.appendTo==="parent"?this.element[0].parentNode:o.appendTo);}if(helper[0]!==this.element[0]&&!/(fixed|absolute)/.test(helper.css("position"))){helper.css("position","absolute");}return helper;},_adjustOffsetFromHelper:function _adjustOffsetFromHelper(obj){if(typeof obj==="string"){obj=obj.split(" ");}if($.isArray(obj)){obj={left:+obj[0],top:+obj[1]||0};}if("left" in obj){this.offset.click.left=obj.left+this.margins.left;}if("right" in obj){this.offset.click.left=this.helperProportions.width-obj.right+this.margins.left;}if("top" in obj){this.offset.click.top=obj.top+this.margins.top;}if("bottom" in obj){this.offset.click.top=this.helperProportions.height-obj.bottom+this.margins.top;}},_getParentOffset:function _getParentOffset(){ //Get the offsetParent and cache its position
	var po=this.offsetParent.offset(); // This is a special case where we need to modify a offset calculated on start, since the following happened:
	// 1. The position of the helper is absolute, so it's position is calculated based on the next positioned parent
	// 2. The actual offset parent is a child of the scroll parent, and the scroll parent isn't the document, which means that
	//    the scroll is included in the initial calculation of the offset of the parent, and never recalculated upon drag
	if(this.cssPosition==="absolute"&&this.scrollParent[0]!==document&&$.contains(this.scrollParent[0],this.offsetParent[0])){po.left+=this.scrollParent.scrollLeft();po.top+=this.scrollParent.scrollTop();} //This needs to be actually done for all browsers, since pageX/pageY includes this information
	//Ugly IE fix
	if(this.offsetParent[0]===document.body||this.offsetParent[0].tagName&&this.offsetParent[0].tagName.toLowerCase()==="html"&&$.ui.ie){po={top:0,left:0};}return {top:po.top+(parseInt(this.offsetParent.css("borderTopWidth"),10)||0),left:po.left+(parseInt(this.offsetParent.css("borderLeftWidth"),10)||0)};},_getRelativeOffset:function _getRelativeOffset(){if(this.cssPosition==="relative"){var p=this.element.position();return {top:p.top-(parseInt(this.helper.css("top"),10)||0)+this.scrollParent.scrollTop(),left:p.left-(parseInt(this.helper.css("left"),10)||0)+this.scrollParent.scrollLeft()};}else {return {top:0,left:0};}},_cacheMargins:function _cacheMargins(){this.margins={left:parseInt(this.element.css("marginLeft"),10)||0,top:parseInt(this.element.css("marginTop"),10)||0,right:parseInt(this.element.css("marginRight"),10)||0,bottom:parseInt(this.element.css("marginBottom"),10)||0};},_cacheHelperProportions:function _cacheHelperProportions(){this.helperProportions={width:this.helper.outerWidth(),height:this.helper.outerHeight()};},_setContainment:function _setContainment(){var over,c,ce,o=this.options;if(!o.containment){this.containment=null;return;}if(o.containment==="window"){this.containment=[$(window).scrollLeft()-this.offset.relative.left-this.offset.parent.left,$(window).scrollTop()-this.offset.relative.top-this.offset.parent.top,$(window).scrollLeft()+$(window).width()-this.helperProportions.width-this.margins.left,$(window).scrollTop()+($(window).height()||document.body.parentNode.scrollHeight)-this.helperProportions.height-this.margins.top];return;}if(o.containment==="document"){this.containment=[0,0,$(document).width()-this.helperProportions.width-this.margins.left,($(document).height()||document.body.parentNode.scrollHeight)-this.helperProportions.height-this.margins.top];return;}if(o.containment.constructor===Array){this.containment=o.containment;return;}if(o.containment==="parent"){o.containment=this.helper[0].parentNode;}c=$(o.containment);ce=c[0];if(!ce){return;}over=c.css("overflow")!=="hidden";this.containment=[(parseInt(c.css("borderLeftWidth"),10)||0)+(parseInt(c.css("paddingLeft"),10)||0),(parseInt(c.css("borderTopWidth"),10)||0)+(parseInt(c.css("paddingTop"),10)||0),(over?Math.max(ce.scrollWidth,ce.offsetWidth):ce.offsetWidth)-(parseInt(c.css("borderRightWidth"),10)||0)-(parseInt(c.css("paddingRight"),10)||0)-this.helperProportions.width-this.margins.left-this.margins.right,(over?Math.max(ce.scrollHeight,ce.offsetHeight):ce.offsetHeight)-(parseInt(c.css("borderBottomWidth"),10)||0)-(parseInt(c.css("paddingBottom"),10)||0)-this.helperProportions.height-this.margins.top-this.margins.bottom];this.relative_container=c;},_convertPositionTo:function _convertPositionTo(d,pos){if(!pos){pos=this.position;}var mod=d==="absolute"?1:-1,scroll=this.cssPosition==="absolute"&&!(this.scrollParent[0]!==document&&$.contains(this.scrollParent[0],this.offsetParent[0]))?this.offsetParent:this.scrollParent; //Cache the scroll
	if(!this.offset.scroll){this.offset.scroll={top:scroll.scrollTop(),left:scroll.scrollLeft()};}return {top:pos.top+ // The absolute mouse position
	this.offset.relative.top*mod+ // Only for relative positioned nodes: Relative offset from element to offset parent
	this.offset.parent.top*mod- // The offsetParent's offset without borders (offset + border)
	(this.cssPosition==="fixed"?-this.scrollParent.scrollTop():this.offset.scroll.top)*mod,left:pos.left+ // The absolute mouse position
	this.offset.relative.left*mod+ // Only for relative positioned nodes: Relative offset from element to offset parent
	this.offset.parent.left*mod- // The offsetParent's offset without borders (offset + border)
	(this.cssPosition==="fixed"?-this.scrollParent.scrollLeft():this.offset.scroll.left)*mod};},_generatePosition:function _generatePosition(event){var containment,co,top,left,o=this.options,scroll=this.cssPosition==="absolute"&&!(this.scrollParent[0]!==document&&$.contains(this.scrollParent[0],this.offsetParent[0]))?this.offsetParent:this.scrollParent,pageX=event.pageX,pageY=event.pageY; //Cache the scroll
	if(!this.offset.scroll){this.offset.scroll={top:scroll.scrollTop(),left:scroll.scrollLeft()};} /*
			 * - Position constraining -
			 * Constrain the position to a mix of grid, containment.
			 */ // If we are not dragging yet, we won't check for options
	if(this.originalPosition){if(this.containment){if(this.relative_container){co=this.relative_container.offset();containment=[this.containment[0]+co.left,this.containment[1]+co.top,this.containment[2]+co.left,this.containment[3]+co.top];}else {containment=this.containment;}if(event.pageX-this.offset.click.left<containment[0]){pageX=containment[0]+this.offset.click.left;}if(event.pageY-this.offset.click.top<containment[1]){pageY=containment[1]+this.offset.click.top;}if(event.pageX-this.offset.click.left>containment[2]){pageX=containment[2]+this.offset.click.left;}if(event.pageY-this.offset.click.top>containment[3]){pageY=containment[3]+this.offset.click.top;}}if(o.grid){ //Check for grid elements set to 0 to prevent divide by 0 error causing invalid argument errors in IE (see ticket #6950)
	top=o.grid[1]?this.originalPageY+Math.round((pageY-this.originalPageY)/o.grid[1])*o.grid[1]:this.originalPageY;pageY=containment?top-this.offset.click.top>=containment[1]||top-this.offset.click.top>containment[3]?top:top-this.offset.click.top>=containment[1]?top-o.grid[1]:top+o.grid[1]:top;left=o.grid[0]?this.originalPageX+Math.round((pageX-this.originalPageX)/o.grid[0])*o.grid[0]:this.originalPageX;pageX=containment?left-this.offset.click.left>=containment[0]||left-this.offset.click.left>containment[2]?left:left-this.offset.click.left>=containment[0]?left-o.grid[0]:left+o.grid[0]:left;}}return {top:pageY- // The absolute mouse position
	this.offset.click.top- // Click offset (relative to the element)
	this.offset.relative.top- // Only for relative positioned nodes: Relative offset from element to offset parent
	this.offset.parent.top+( // The offsetParent's offset without borders (offset + border)
	this.cssPosition==="fixed"?-this.scrollParent.scrollTop():this.offset.scroll.top),left:pageX- // The absolute mouse position
	this.offset.click.left- // Click offset (relative to the element)
	this.offset.relative.left- // Only for relative positioned nodes: Relative offset from element to offset parent
	this.offset.parent.left+( // The offsetParent's offset without borders (offset + border)
	this.cssPosition==="fixed"?-this.scrollParent.scrollLeft():this.offset.scroll.left)};},_clear:function _clear(){this.helper.removeClass("ui-draggable-dragging");if(this.helper[0]!==this.element[0]&&!this.cancelHelperRemoval){this.helper.remove();}this.helper=null;this.cancelHelperRemoval=false;}, // From now on bulk stuff - mainly helpers
	_trigger:function _trigger(type,event,ui){ui=ui||this._uiHash();$.ui.plugin.call(this,type,[event,ui]); //The absolute position has to be recalculated after plugins
	if(type==="drag"){this.positionAbs=this._convertPositionTo("absolute");}return $.Widget.prototype._trigger.call(this,type,event,ui);},plugins:{},_uiHash:function _uiHash(){return {helper:this.helper,position:this.position,originalPosition:this.originalPosition,offset:this.positionAbs};}});$.ui.plugin.add("draggable","connectToSortable",{start:function start(event,ui){var inst=$(this).data("ui-draggable"),o=inst.options,uiSortable=$.extend({},ui,{item:inst.element});inst.sortables=[];$(o.connectToSortable).each(function(){var sortable=$.data(this,"ui-sortable");if(sortable&&!sortable.options.disabled){inst.sortables.push({instance:sortable,shouldRevert:sortable.options.revert});sortable.refreshPositions(); // Call the sortable's refreshPositions at drag start to refresh the containerCache since the sortable container cache is used in drag and needs to be up to date (this will ensure it's initialised as well as being kept in step with any changes that might have happened on the page).
	sortable._trigger("activate",event,uiSortable);}});},stop:function stop(event,ui){ //If we are still over the sortable, we fake the stop event of the sortable, but also remove helper
	var inst=$(this).data("ui-draggable"),uiSortable=$.extend({},ui,{item:inst.element});$.each(inst.sortables,function(){if(this.instance.isOver){this.instance.isOver=0;inst.cancelHelperRemoval=true; //Don't remove the helper in the draggable instance
	this.instance.cancelHelperRemoval=false; //Remove it in the sortable instance (so sortable plugins like revert still work)
	//The sortable revert is supported, and we have to set a temporary dropped variable on the draggable to support revert: "valid/invalid"
	if(this.shouldRevert){this.instance.options.revert=this.shouldRevert;} //Trigger the stop of the sortable
	this.instance._mouseStop(event);this.instance.options.helper=this.instance.options._helper; //If the helper has been the original item, restore properties in the sortable
	if(inst.options.helper==="original"){this.instance.currentItem.css({top:"auto",left:"auto"});}}else {this.instance.cancelHelperRemoval=false; //Remove the helper in the sortable instance
	this.instance._trigger("deactivate",event,uiSortable);}});},drag:function drag(event,ui){var inst=$(this).data("ui-draggable"),that=this;$.each(inst.sortables,function(){var innermostIntersecting=false,thisSortable=this; //Copy over some variables to allow calling the sortable's native _intersectsWith
	this.instance.positionAbs=inst.positionAbs;this.instance.helperProportions=inst.helperProportions;this.instance.offset.click=inst.offset.click;if(this.instance._intersectsWith(this.instance.containerCache)){innermostIntersecting=true;$.each(inst.sortables,function(){this.instance.positionAbs=inst.positionAbs;this.instance.helperProportions=inst.helperProportions;this.instance.offset.click=inst.offset.click;if(this!==thisSortable&&this.instance._intersectsWith(this.instance.containerCache)&&$.contains(thisSortable.instance.element[0],this.instance.element[0])){innermostIntersecting=false;}return innermostIntersecting;});}if(innermostIntersecting){ //If it intersects, we use a little isOver variable and set it once, so our move-in stuff gets fired only once
	if(!this.instance.isOver){this.instance.isOver=1; //Now we fake the start of dragging for the sortable instance,
	//by cloning the list group item, appending it to the sortable and using it as inst.currentItem
	//We can then fire the start event of the sortable with our passed browser event, and our own helper (so it doesn't create a new one)
	this.instance.currentItem=$(that).clone().removeAttr("id").appendTo(this.instance.element).data("ui-sortable-item",true);this.instance.options._helper=this.instance.options.helper; //Store helper option to later restore it
	this.instance.options.helper=function(){return ui.helper[0];};event.target=this.instance.currentItem[0];this.instance._mouseCapture(event,true);this.instance._mouseStart(event,true,true); //Because the browser event is way off the new appended portlet, we modify a couple of variables to reflect the changes
	this.instance.offset.click.top=inst.offset.click.top;this.instance.offset.click.left=inst.offset.click.left;this.instance.offset.parent.left-=inst.offset.parent.left-this.instance.offset.parent.left;this.instance.offset.parent.top-=inst.offset.parent.top-this.instance.offset.parent.top;inst._trigger("toSortable",event);inst.dropped=this.instance.element; //draggable revert needs that
	//hack so receive/update callbacks work (mostly)
	inst.currentItem=inst.element;this.instance.fromOutside=inst;} //Provided we did all the previous steps, we can fire the drag event of the sortable on every draggable drag, when it intersects with the sortable
	if(this.instance.currentItem){this.instance._mouseDrag(event);}}else { //If it doesn't intersect with the sortable, and it intersected before,
	//we fake the drag stop of the sortable, but make sure it doesn't remove the helper by using cancelHelperRemoval
	if(this.instance.isOver){this.instance.isOver=0;this.instance.cancelHelperRemoval=true; //Prevent reverting on this forced stop
	this.instance.options.revert=false; // The out event needs to be triggered independently
	this.instance._trigger("out",event,this.instance._uiHash(this.instance));this.instance._mouseStop(event,true);this.instance.options.helper=this.instance.options._helper; //Now we remove our currentItem, the list group clone again, and the placeholder, and animate the helper back to it's original size
	this.instance.currentItem.remove();if(this.instance.placeholder){this.instance.placeholder.remove();}inst._trigger("fromSortable",event);inst.dropped=false; //draggable revert needs that
	}}});}});$.ui.plugin.add("draggable","cursor",{start:function start(){var t=$("body"),o=$(this).data("ui-draggable").options;if(t.css("cursor")){o._cursor=t.css("cursor");}t.css("cursor",o.cursor);},stop:function stop(){var o=$(this).data("ui-draggable").options;if(o._cursor){$("body").css("cursor",o._cursor);}}});$.ui.plugin.add("draggable","opacity",{start:function start(event,ui){var t=$(ui.helper),o=$(this).data("ui-draggable").options;if(t.css("opacity")){o._opacity=t.css("opacity");}t.css("opacity",o.opacity);},stop:function stop(event,ui){var o=$(this).data("ui-draggable").options;if(o._opacity){$(ui.helper).css("opacity",o._opacity);}}});$.ui.plugin.add("draggable","scroll",{start:function start(){var i=$(this).data("ui-draggable");if(i.scrollParent[0]!==document&&i.scrollParent[0].tagName!=="HTML"){i.overflowOffset=i.scrollParent.offset();}},drag:function drag(event){var i=$(this).data("ui-draggable"),o=i.options,scrolled=false;if(i.scrollParent[0]!==document&&i.scrollParent[0].tagName!=="HTML"){if(!o.axis||o.axis!=="x"){if(i.overflowOffset.top+i.scrollParent[0].offsetHeight-event.pageY<o.scrollSensitivity){i.scrollParent[0].scrollTop=scrolled=i.scrollParent[0].scrollTop+o.scrollSpeed;}else if(event.pageY-i.overflowOffset.top<o.scrollSensitivity){i.scrollParent[0].scrollTop=scrolled=i.scrollParent[0].scrollTop-o.scrollSpeed;}}if(!o.axis||o.axis!=="y"){if(i.overflowOffset.left+i.scrollParent[0].offsetWidth-event.pageX<o.scrollSensitivity){i.scrollParent[0].scrollLeft=scrolled=i.scrollParent[0].scrollLeft+o.scrollSpeed;}else if(event.pageX-i.overflowOffset.left<o.scrollSensitivity){i.scrollParent[0].scrollLeft=scrolled=i.scrollParent[0].scrollLeft-o.scrollSpeed;}}}else {if(!o.axis||o.axis!=="x"){if(event.pageY-$(document).scrollTop()<o.scrollSensitivity){scrolled=$(document).scrollTop($(document).scrollTop()-o.scrollSpeed);}else if($(window).height()-(event.pageY-$(document).scrollTop())<o.scrollSensitivity){scrolled=$(document).scrollTop($(document).scrollTop()+o.scrollSpeed);}}if(!o.axis||o.axis!=="y"){if(event.pageX-$(document).scrollLeft()<o.scrollSensitivity){scrolled=$(document).scrollLeft($(document).scrollLeft()-o.scrollSpeed);}else if($(window).width()-(event.pageX-$(document).scrollLeft())<o.scrollSensitivity){scrolled=$(document).scrollLeft($(document).scrollLeft()+o.scrollSpeed);}}}if(scrolled!==false&&$.ui.ddmanager&&!o.dropBehaviour){$.ui.ddmanager.prepareOffsets(i,event);}}});$.ui.plugin.add("draggable","snap",{start:function start(){var i=$(this).data("ui-draggable"),o=i.options;i.snapElements=[];$(o.snap.constructor!==String?o.snap.items||":data(ui-draggable)":o.snap).each(function(){var $t=$(this),$o=$t.offset();if(this!==i.element[0]){i.snapElements.push({item:this,width:$t.outerWidth(),height:$t.outerHeight(),top:$o.top,left:$o.left});}});},drag:function drag(event,ui){var ts,bs,ls,rs,l,r,t,b,i,first,inst=$(this).data("ui-draggable"),o=inst.options,d=o.snapTolerance,x1=ui.offset.left,x2=x1+inst.helperProportions.width,y1=ui.offset.top,y2=y1+inst.helperProportions.height;for(i=inst.snapElements.length-1;i>=0;i--){l=inst.snapElements[i].left;r=l+inst.snapElements[i].width;t=inst.snapElements[i].top;b=t+inst.snapElements[i].height;if(x2<l-d||x1>r+d||y2<t-d||y1>b+d||!$.contains(inst.snapElements[i].item.ownerDocument,inst.snapElements[i].item)){if(inst.snapElements[i].snapping){inst.options.snap.release&&inst.options.snap.release.call(inst.element,event,$.extend(inst._uiHash(),{snapItem:inst.snapElements[i].item}));}inst.snapElements[i].snapping=false;continue;}if(o.snapMode!=="inner"){ts=Math.abs(t-y2)<=d;bs=Math.abs(b-y1)<=d;ls=Math.abs(l-x2)<=d;rs=Math.abs(r-x1)<=d;if(ts){ui.position.top=inst._convertPositionTo("relative",{top:t-inst.helperProportions.height,left:0}).top-inst.margins.top;}if(bs){ui.position.top=inst._convertPositionTo("relative",{top:b,left:0}).top-inst.margins.top;}if(ls){ui.position.left=inst._convertPositionTo("relative",{top:0,left:l-inst.helperProportions.width}).left-inst.margins.left;}if(rs){ui.position.left=inst._convertPositionTo("relative",{top:0,left:r}).left-inst.margins.left;}}first=ts||bs||ls||rs;if(o.snapMode!=="outer"){ts=Math.abs(t-y1)<=d;bs=Math.abs(b-y2)<=d;ls=Math.abs(l-x1)<=d;rs=Math.abs(r-x2)<=d;if(ts){ui.position.top=inst._convertPositionTo("relative",{top:t,left:0}).top-inst.margins.top;}if(bs){ui.position.top=inst._convertPositionTo("relative",{top:b-inst.helperProportions.height,left:0}).top-inst.margins.top;}if(ls){ui.position.left=inst._convertPositionTo("relative",{top:0,left:l}).left-inst.margins.left;}if(rs){ui.position.left=inst._convertPositionTo("relative",{top:0,left:r-inst.helperProportions.width}).left-inst.margins.left;}}if(!inst.snapElements[i].snapping&&(ts||bs||ls||rs||first)){inst.options.snap.snap&&inst.options.snap.snap.call(inst.element,event,$.extend(inst._uiHash(),{snapItem:inst.snapElements[i].item}));}inst.snapElements[i].snapping=ts||bs||ls||rs||first;}}});$.ui.plugin.add("draggable","stack",{start:function start(){var min,o=this.data("ui-draggable").options,group=$.makeArray($(o.stack)).sort(function(a,b){return (parseInt($(a).css("zIndex"),10)||0)-(parseInt($(b).css("zIndex"),10)||0);});if(!group.length){return;}min=parseInt($(group[0]).css("zIndex"),10)||0;$(group).each(function(i){$(this).css("zIndex",min+i);});this.css("zIndex",min+group.length);}});$.ui.plugin.add("draggable","zIndex",{start:function start(event,ui){var t=$(ui.helper),o=$(this).data("ui-draggable").options;if(t.css("zIndex")){o._zIndex=t.css("zIndex");}t.css("zIndex",o.zIndex);},stop:function stop(event,ui){var o=$(this).data("ui-draggable").options;if(o._zIndex){$(ui.helper).css("zIndex",o._zIndex);}}});})(jQuery);(function($,undefined){function isOverAxis(x,reference,size){return x>reference&&x<reference+size;}$.widget("ui.droppable",{version:"1.10.3",widgetEventPrefix:"drop",options:{accept:"*",activeClass:false,addClasses:true,greedy:false,hoverClass:false,scope:"default",tolerance:"intersect", // callbacks
	activate:null,deactivate:null,drop:null,out:null,over:null},_create:function _create(){var o=this.options,accept=o.accept;this.isover=false;this.isout=true;this.accept=$.isFunction(accept)?accept:function(d){return d.is(accept);}; //Store the droppable's proportions
	this.proportions={width:this.element[0].offsetWidth,height:this.element[0].offsetHeight}; // Add the reference and positions to the manager
	$.ui.ddmanager.droppables[o.scope]=$.ui.ddmanager.droppables[o.scope]||[];$.ui.ddmanager.droppables[o.scope].push(this);o.addClasses&&this.element.addClass("ui-droppable");},_destroy:function _destroy(){var i=0,drop=$.ui.ddmanager.droppables[this.options.scope];for(;i<drop.length;i++){if(drop[i]===this){drop.splice(i,1);}}this.element.removeClass("ui-droppable ui-droppable-disabled");},_setOption:function _setOption(key,value){if(key==="accept"){this.accept=$.isFunction(value)?value:function(d){return d.is(value);};}$.Widget.prototype._setOption.apply(this,arguments);},_activate:function _activate(event){var draggable=$.ui.ddmanager.current;if(this.options.activeClass){this.element.addClass(this.options.activeClass);}if(draggable){this._trigger("activate",event,this.ui(draggable));}},_deactivate:function _deactivate(event){var draggable=$.ui.ddmanager.current;if(this.options.activeClass){this.element.removeClass(this.options.activeClass);}if(draggable){this._trigger("deactivate",event,this.ui(draggable));}},_over:function _over(event){var draggable=$.ui.ddmanager.current; // Bail if draggable and droppable are same element
	if(!draggable||(draggable.currentItem||draggable.element)[0]===this.element[0]){return;}if(this.accept.call(this.element[0],draggable.currentItem||draggable.element)){if(this.options.hoverClass){this.element.addClass(this.options.hoverClass);}this._trigger("over",event,this.ui(draggable));}},_out:function _out(event){var draggable=$.ui.ddmanager.current; // Bail if draggable and droppable are same element
	if(!draggable||(draggable.currentItem||draggable.element)[0]===this.element[0]){return;}if(this.accept.call(this.element[0],draggable.currentItem||draggable.element)){if(this.options.hoverClass){this.element.removeClass(this.options.hoverClass);}this._trigger("out",event,this.ui(draggable));}},_drop:function _drop(event,custom){var draggable=custom||$.ui.ddmanager.current,childrenIntersection=false; // Bail if draggable and droppable are same element
	if(!draggable||(draggable.currentItem||draggable.element)[0]===this.element[0]){return false;}this.element.find(":data(ui-droppable)").not(".ui-draggable-dragging").each(function(){var inst=$.data(this,"ui-droppable");if(inst.options.greedy&&!inst.options.disabled&&inst.options.scope===draggable.options.scope&&inst.accept.call(inst.element[0],draggable.currentItem||draggable.element)&&$.ui.intersect(draggable,$.extend(inst,{offset:inst.element.offset()}),inst.options.tolerance)){childrenIntersection=true;return false;}});if(childrenIntersection){return false;}if(this.accept.call(this.element[0],draggable.currentItem||draggable.element)){if(this.options.activeClass){this.element.removeClass(this.options.activeClass);}if(this.options.hoverClass){this.element.removeClass(this.options.hoverClass);}this._trigger("drop",event,this.ui(draggable));return this.element;}return false;},ui:function ui(c){return {draggable:c.currentItem||c.element,helper:c.helper,position:c.position,offset:c.positionAbs};}});$.ui.intersect=function(draggable,droppable,toleranceMode){if(!droppable.offset){return false;}var draggableLeft,draggableTop,x1=(draggable.positionAbs||draggable.position.absolute).left,x2=x1+draggable.helperProportions.width,y1=(draggable.positionAbs||draggable.position.absolute).top,y2=y1+draggable.helperProportions.height,l=droppable.offset.left,r=l+droppable.proportions.width,t=droppable.offset.top,b=t+droppable.proportions.height;switch(toleranceMode){case "fit":return l<=x1&&x2<=r&&t<=y1&&y2<=b;case "intersect":return l<x1+draggable.helperProportions.width/2&& // Right Half
	x2-draggable.helperProportions.width/2<r&& // Left Half
	t<y1+draggable.helperProportions.height/2&& // Bottom Half
	y2-draggable.helperProportions.height/2<b; // Top Half
	case "pointer":draggableLeft=(draggable.positionAbs||draggable.position.absolute).left+(draggable.clickOffset||draggable.offset.click).left;draggableTop=(draggable.positionAbs||draggable.position.absolute).top+(draggable.clickOffset||draggable.offset.click).top;return isOverAxis(draggableTop,t,droppable.proportions.height)&&isOverAxis(draggableLeft,l,droppable.proportions.width);case "touch":return (y1>=t&&y1<=b|| // Top edge touching
	y2>=t&&y2<=b|| // Bottom edge touching
	y1<t&&y2>b // Surrounded vertically
	)&&(x1>=l&&x1<=r|| // Left edge touching
	x2>=l&&x2<=r|| // Right edge touching
	x1<l&&x2>r // Surrounded horizontally
	);default:return false;}}; /*
		This manager tracks offsets of draggables and droppables
	*/$.ui.ddmanager={current:null,droppables:{"default":[]},prepareOffsets:function prepareOffsets(t,event){var i,j,m=$.ui.ddmanager.droppables[t.options.scope]||[],type=event?event.type:null, // workaround for #2317
	list=(t.currentItem||t.element).find(":data(ui-droppable)").addBack();droppablesLoop: for(i=0;i<m.length;i++){ //No disabled and non-accepted
	if(m[i].options.disabled||t&&!m[i].accept.call(m[i].element[0],t.currentItem||t.element)){continue;} // Filter out elements in the current dragged item
	for(j=0;j<list.length;j++){if(list[j]===m[i].element[0]){m[i].proportions.height=0;continue droppablesLoop;}}m[i].visible=m[i].element.css("display")!=="none";if(!m[i].visible){continue;} //Activate the droppable if used directly from draggables
	if(type==="mousedown"){m[i]._activate.call(m[i],event);}m[i].offset=m[i].element.offset();m[i].proportions={width:m[i].element[0].offsetWidth,height:m[i].element[0].offsetHeight};}},drop:function drop(draggable,event){var dropped=false; // Create a copy of the droppables in case the list changes during the drop (#9116)
	$.each(($.ui.ddmanager.droppables[draggable.options.scope]||[]).slice(),function(){if(!this.options){return;}if(!this.options.disabled&&this.visible&&$.ui.intersect(draggable,this,this.options.tolerance)){dropped=this._drop.call(this,event)||dropped;}if(!this.options.disabled&&this.visible&&this.accept.call(this.element[0],draggable.currentItem||draggable.element)){this.isout=true;this.isover=false;this._deactivate.call(this,event);}});return dropped;},dragStart:function dragStart(draggable,event){ //Listen for scrolling so that if the dragging causes scrolling the position of the droppables can be recalculated (see #5003)
	draggable.element.parentsUntil("body").bind("scroll.droppable",function(){if(!draggable.options.refreshPositions){$.ui.ddmanager.prepareOffsets(draggable,event);}});},drag:function drag(draggable,event){ //If you have a highly dynamic page, you might try this option. It renders positions every time you move the mouse.
	if(draggable.options.refreshPositions){$.ui.ddmanager.prepareOffsets(draggable,event);} //Run through all droppables and check their positions based on specific tolerance options
	$.each($.ui.ddmanager.droppables[draggable.options.scope]||[],function(){if(this.options.disabled||this.greedyChild||!this.visible){return;}var parentInstance,scope,parent,intersects=$.ui.intersect(draggable,this,this.options.tolerance),c=!intersects&&this.isover?"isout":intersects&&!this.isover?"isover":null;if(!c){return;}if(this.options.greedy){ // find droppable parents with same scope
	scope=this.options.scope;parent=this.element.parents(":data(ui-droppable)").filter(function(){return $.data(this,"ui-droppable").options.scope===scope;});if(parent.length){parentInstance=$.data(parent[0],"ui-droppable");parentInstance.greedyChild=c==="isover";}} // we just moved into a greedy child
	if(parentInstance&&c==="isover"){parentInstance.isover=false;parentInstance.isout=true;parentInstance._out.call(parentInstance,event);}this[c]=true;this[c==="isout"?"isover":"isout"]=false;this[c==="isover"?"_over":"_out"].call(this,event); // we just moved out of a greedy child
	if(parentInstance&&c==="isout"){parentInstance.isout=false;parentInstance.isover=true;parentInstance._over.call(parentInstance,event);}});},dragStop:function dragStop(draggable,event){draggable.element.parentsUntil("body").unbind("scroll.droppable"); //Call prepareOffsets one final time since IE does not fire return scroll events when overflow was caused by drag (see #5003)
	if(!draggable.options.refreshPositions){$.ui.ddmanager.prepareOffsets(draggable,event);}}};})(jQuery);(function($,undefined){function num(v){return parseInt(v,10)||0;}function isNumber(value){return !isNaN(parseInt(value,10));}$.widget("ui.resizable",$.ui.mouse,{version:"1.10.3",widgetEventPrefix:"resize",options:{alsoResize:false,animate:false,animateDuration:"slow",animateEasing:"swing",aspectRatio:false,autoHide:false,containment:false,ghost:false,grid:false,handles:"e,s,se",helper:false,maxHeight:null,maxWidth:null,minHeight:10,minWidth:10, // See #7960
	zIndex:90, // callbacks
	resize:null,start:null,stop:null},_create:function _create(){var n,i,handle,axis,hname,that=this,o=this.options;this.element.addClass("ui-resizable");$.extend(this,{_aspectRatio:!!o.aspectRatio,aspectRatio:o.aspectRatio,originalElement:this.element,_proportionallyResizeElements:[],_helper:o.helper||o.ghost||o.animate?o.helper||"ui-resizable-helper":null}); //Wrap the element if it cannot hold child nodes
	if(this.element[0].nodeName.match(/canvas|textarea|input|select|button|img/i)){ //Create a wrapper element and set the wrapper to the new current internal element
	this.element.wrap($("<div class='ui-wrapper' style='overflow: hidden;'></div>").css({position:this.element.css("position"),width:this.element.outerWidth(),height:this.element.outerHeight(),top:this.element.css("top"),left:this.element.css("left")})); //Overwrite the original this.element
	this.element=this.element.parent().data("ui-resizable",this.element.data("ui-resizable"));this.elementIsWrapper=true; //Move margins to the wrapper
	this.element.css({marginLeft:this.originalElement.css("marginLeft"),marginTop:this.originalElement.css("marginTop"),marginRight:this.originalElement.css("marginRight"),marginBottom:this.originalElement.css("marginBottom")});this.originalElement.css({marginLeft:0,marginTop:0,marginRight:0,marginBottom:0}); //Prevent Safari textarea resize
	this.originalResizeStyle=this.originalElement.css("resize");this.originalElement.css("resize","none"); //Push the actual element to our proportionallyResize internal array
	this._proportionallyResizeElements.push(this.originalElement.css({position:"static",zoom:1,display:"block"})); // avoid IE jump (hard set the margin)
	this.originalElement.css({margin:this.originalElement.css("margin")}); // fix handlers offset
	this._proportionallyResize();}this.handles=o.handles||(!$(".ui-resizable-handle",this.element).length?"e,s,se":{n:".ui-resizable-n",e:".ui-resizable-e",s:".ui-resizable-s",w:".ui-resizable-w",se:".ui-resizable-se",sw:".ui-resizable-sw",ne:".ui-resizable-ne",nw:".ui-resizable-nw"});if(this.handles.constructor===String){if(this.handles==="all"){this.handles="n,e,s,w,se,sw,ne,nw";}n=this.handles.split(",");this.handles={};for(i=0;i<n.length;i++){handle=$.trim(n[i]);hname="ui-resizable-"+handle;axis=$("<div class='ui-resizable-handle "+hname+"'></div>"); // Apply zIndex to all handles - see #7960
	axis.css({zIndex:o.zIndex}); //TODO : What's going on here?
	if("se"===handle){axis.addClass("ui-icon ui-icon-gripsmall-diagonal-se");} //Insert into internal handles object and append to element
	this.handles[handle]=".ui-resizable-"+handle;this.element.append(axis);}}this._renderAxis=function(target){var i,axis,padPos,padWrapper;target=target||this.element;for(i in this.handles){if(this.handles[i].constructor===String){this.handles[i]=$(this.handles[i],this.element).show();} //Apply pad to wrapper element, needed to fix axis position (textarea, inputs, scrolls)
	if(this.elementIsWrapper&&this.originalElement[0].nodeName.match(/textarea|input|select|button/i)){axis=$(this.handles[i],this.element); //Checking the correct pad and border
	padWrapper=/sw|ne|nw|se|n|s/.test(i)?axis.outerHeight():axis.outerWidth(); //The padding type i have to apply...
	padPos=["padding",/ne|nw|n/.test(i)?"Top":/se|sw|s/.test(i)?"Bottom":/^e$/.test(i)?"Right":"Left"].join("");target.css(padPos,padWrapper);this._proportionallyResize();} //TODO: What's that good for? There's not anything to be executed left
	if(!$(this.handles[i]).length){continue;}}}; //TODO: make renderAxis a prototype function
	this._renderAxis(this.element);this._handles=$(".ui-resizable-handle",this.element).disableSelection(); //Matching axis name
	this._handles.mouseover(function(){if(!that.resizing){if(this.className){axis=this.className.match(/ui-resizable-(se|sw|ne|nw|n|e|s|w)/i);} //Axis, default = se
	that.axis=axis&&axis[1]?axis[1]:"se";}}); //If we want to auto hide the elements
	if(o.autoHide){this._handles.hide();$(this.element).addClass("ui-resizable-autohide").mouseenter(function(){if(o.disabled){return;}$(this).removeClass("ui-resizable-autohide");that._handles.show();}).mouseleave(function(){if(o.disabled){return;}if(!that.resizing){$(this).addClass("ui-resizable-autohide");that._handles.hide();}});} //Initialize the mouse interaction
	this._mouseInit();},_destroy:function _destroy(){this._mouseDestroy();var wrapper,_destroy=function _destroy(exp){$(exp).removeClass("ui-resizable ui-resizable-disabled ui-resizable-resizing").removeData("resizable").removeData("ui-resizable").unbind(".resizable").find(".ui-resizable-handle").remove();}; //TODO: Unwrap at same DOM position
	if(this.elementIsWrapper){_destroy(this.element);wrapper=this.element;this.originalElement.css({position:wrapper.css("position"),width:wrapper.outerWidth(),height:wrapper.outerHeight(),top:wrapper.css("top"),left:wrapper.css("left")}).insertAfter(wrapper);wrapper.remove();}this.originalElement.css("resize",this.originalResizeStyle);_destroy(this.originalElement);return this;},_mouseCapture:function _mouseCapture(event){var i,handle,capture=false;for(i in this.handles){handle=$(this.handles[i])[0];if(handle===event.target||$.contains(handle,event.target)){capture=true;}}return !this.options.disabled&&capture;},_mouseStart:function _mouseStart(event){var curleft,curtop,cursor,o=this.options,iniPos=this.element.position(),el=this.element;this.resizing=true; // bugfix for http://dev.jquery.com/ticket/1749
	if(/absolute/.test(el.css("position"))){el.css({position:"absolute",top:el.css("top"),left:el.css("left")});}else if(el.is(".ui-draggable")){el.css({position:"absolute",top:iniPos.top,left:iniPos.left});}this._renderProxy();curleft=num(this.helper.css("left"));curtop=num(this.helper.css("top"));if(o.containment){curleft+=$(o.containment).scrollLeft()||0;curtop+=$(o.containment).scrollTop()||0;} //Store needed variables
	this.offset=this.helper.offset();this.position={left:curleft,top:curtop};this.size=this._helper?{width:el.outerWidth(),height:el.outerHeight()}:{width:el.width(),height:el.height()};this.originalSize=this._helper?{width:el.outerWidth(),height:el.outerHeight()}:{width:el.width(),height:el.height()};this.originalPosition={left:curleft,top:curtop};this.sizeDiff={width:el.outerWidth()-el.width(),height:el.outerHeight()-el.height()};this.originalMousePosition={left:event.pageX,top:event.pageY}; //Aspect Ratio
	this.aspectRatio=typeof o.aspectRatio==="number"?o.aspectRatio:this.originalSize.width/this.originalSize.height||1;cursor=$(".ui-resizable-"+this.axis).css("cursor");$("body").css("cursor",cursor==="auto"?this.axis+"-resize":cursor);el.addClass("ui-resizable-resizing");this._propagate("start",event);return true;},_mouseDrag:function _mouseDrag(event){ //Increase performance, avoid regex
	var data,el=this.helper,props={},smp=this.originalMousePosition,a=this.axis,prevTop=this.position.top,prevLeft=this.position.left,prevWidth=this.size.width,prevHeight=this.size.height,dx=event.pageX-smp.left||0,dy=event.pageY-smp.top||0,trigger=this._change[a];if(!trigger){return false;} // Calculate the attrs that will be change
	data=trigger.apply(this,[event,dx,dy]); // Put this in the mouseDrag handler since the user can start pressing shift while resizing
	this._updateVirtualBoundaries(event.shiftKey);if(this._aspectRatio||event.shiftKey){data=this._updateRatio(data,event);}data=this._respectSize(data,event);this._updateCache(data); // plugins callbacks need to be called first
	this._propagate("resize",event);if(this.position.top!==prevTop){props.top=this.position.top+"px";}if(this.position.left!==prevLeft){props.left=this.position.left+"px";}if(this.size.width!==prevWidth){props.width=this.size.width+"px";}if(this.size.height!==prevHeight){props.height=this.size.height+"px";}el.css(props);if(!this._helper&&this._proportionallyResizeElements.length){this._proportionallyResize();} // Call the user callback if the element was resized
	if(!$.isEmptyObject(props)){this._trigger("resize",event,this.ui());}return false;},_mouseStop:function _mouseStop(event){this.resizing=false;var pr,ista,soffseth,soffsetw,s,left,top,o=this.options,that=this;if(this._helper){pr=this._proportionallyResizeElements;ista=pr.length&&/textarea/i.test(pr[0].nodeName);soffseth=ista&&$.ui.hasScroll(pr[0],"left") /* TODO - jump height */?0:that.sizeDiff.height;soffsetw=ista?0:that.sizeDiff.width;s={width:that.helper.width()-soffsetw,height:that.helper.height()-soffseth};left=parseInt(that.element.css("left"),10)+(that.position.left-that.originalPosition.left)||null;top=parseInt(that.element.css("top"),10)+(that.position.top-that.originalPosition.top)||null;if(!o.animate){this.element.css($.extend(s,{top:top,left:left}));}that.helper.height(that.size.height);that.helper.width(that.size.width);if(this._helper&&!o.animate){this._proportionallyResize();}}$("body").css("cursor","auto");this.element.removeClass("ui-resizable-resizing");this._propagate("stop",event);if(this._helper){this.helper.remove();}return false;},_updateVirtualBoundaries:function _updateVirtualBoundaries(forceAspectRatio){var pMinWidth,pMaxWidth,pMinHeight,pMaxHeight,b,o=this.options;b={minWidth:isNumber(o.minWidth)?o.minWidth:0,maxWidth:isNumber(o.maxWidth)?o.maxWidth:Infinity,minHeight:isNumber(o.minHeight)?o.minHeight:0,maxHeight:isNumber(o.maxHeight)?o.maxHeight:Infinity};if(this._aspectRatio||forceAspectRatio){ // We want to create an enclosing box whose aspect ration is the requested one
	// First, compute the "projected" size for each dimension based on the aspect ratio and other dimension
	pMinWidth=b.minHeight*this.aspectRatio;pMinHeight=b.minWidth/this.aspectRatio;pMaxWidth=b.maxHeight*this.aspectRatio;pMaxHeight=b.maxWidth/this.aspectRatio;if(pMinWidth>b.minWidth){b.minWidth=pMinWidth;}if(pMinHeight>b.minHeight){b.minHeight=pMinHeight;}if(pMaxWidth<b.maxWidth){b.maxWidth=pMaxWidth;}if(pMaxHeight<b.maxHeight){b.maxHeight=pMaxHeight;}}this._vBoundaries=b;},_updateCache:function _updateCache(data){this.offset=this.helper.offset();if(isNumber(data.left)){this.position.left=data.left;}if(isNumber(data.top)){this.position.top=data.top;}if(isNumber(data.height)){this.size.height=data.height;}if(isNumber(data.width)){this.size.width=data.width;}},_updateRatio:function _updateRatio(data){var cpos=this.position,csize=this.size,a=this.axis;if(isNumber(data.height)){data.width=data.height*this.aspectRatio;}else if(isNumber(data.width)){data.height=data.width/this.aspectRatio;}if(a==="sw"){data.left=cpos.left+(csize.width-data.width);data.top=null;}if(a==="nw"){data.top=cpos.top+(csize.height-data.height);data.left=cpos.left+(csize.width-data.width);}return data;},_respectSize:function _respectSize(data){var o=this._vBoundaries,a=this.axis,ismaxw=isNumber(data.width)&&o.maxWidth&&o.maxWidth<data.width,ismaxh=isNumber(data.height)&&o.maxHeight&&o.maxHeight<data.height,isminw=isNumber(data.width)&&o.minWidth&&o.minWidth>data.width,isminh=isNumber(data.height)&&o.minHeight&&o.minHeight>data.height,dw=this.originalPosition.left+this.originalSize.width,dh=this.position.top+this.size.height,cw=/sw|nw|w/.test(a),ch=/nw|ne|n/.test(a);if(isminw){data.width=o.minWidth;}if(isminh){data.height=o.minHeight;}if(ismaxw){data.width=o.maxWidth;}if(ismaxh){data.height=o.maxHeight;}if(isminw&&cw){data.left=dw-o.minWidth;}if(ismaxw&&cw){data.left=dw-o.maxWidth;}if(isminh&&ch){data.top=dh-o.minHeight;}if(ismaxh&&ch){data.top=dh-o.maxHeight;} // fixing jump error on top/left - bug #2330
	if(!data.width&&!data.height&&!data.left&&data.top){data.top=null;}else if(!data.width&&!data.height&&!data.top&&data.left){data.left=null;}return data;},_proportionallyResize:function _proportionallyResize(){if(!this._proportionallyResizeElements.length){return;}var i,j,borders,paddings,prel,element=this.helper||this.element;for(i=0;i<this._proportionallyResizeElements.length;i++){prel=this._proportionallyResizeElements[i];if(!this.borderDif){this.borderDif=[];borders=[prel.css("borderTopWidth"),prel.css("borderRightWidth"),prel.css("borderBottomWidth"),prel.css("borderLeftWidth")];paddings=[prel.css("paddingTop"),prel.css("paddingRight"),prel.css("paddingBottom"),prel.css("paddingLeft")];for(j=0;j<borders.length;j++){this.borderDif[j]=(parseInt(borders[j],10)||0)+(parseInt(paddings[j],10)||0);}}prel.css({height:element.height()-this.borderDif[0]-this.borderDif[2]||0,width:element.width()-this.borderDif[1]-this.borderDif[3]||0});}},_renderProxy:function _renderProxy(){var el=this.element,o=this.options;this.elementOffset=el.offset();if(this._helper){this.helper=this.helper||$("<div style='overflow:hidden;'></div>");this.helper.addClass(this._helper).css({width:this.element.outerWidth()-1,height:this.element.outerHeight()-1,position:"absolute",left:this.elementOffset.left+"px",top:this.elementOffset.top+"px",zIndex:++o.zIndex //TODO: Don't modify option
	});this.helper.appendTo("body").disableSelection();}else {this.helper=this.element;}},_change:{e:function e(event,dx){return {width:this.originalSize.width+dx};},w:function w(event,dx){var cs=this.originalSize,sp=this.originalPosition;return {left:sp.left+dx,width:cs.width-dx};},n:function n(event,dx,dy){var cs=this.originalSize,sp=this.originalPosition;return {top:sp.top+dy,height:cs.height-dy};},s:function s(event,dx,dy){return {height:this.originalSize.height+dy};},se:function se(event,dx,dy){return $.extend(this._change.s.apply(this,arguments),this._change.e.apply(this,[event,dx,dy]));},sw:function sw(event,dx,dy){return $.extend(this._change.s.apply(this,arguments),this._change.w.apply(this,[event,dx,dy]));},ne:function ne(event,dx,dy){return $.extend(this._change.n.apply(this,arguments),this._change.e.apply(this,[event,dx,dy]));},nw:function nw(event,dx,dy){return $.extend(this._change.n.apply(this,arguments),this._change.w.apply(this,[event,dx,dy]));}},_propagate:function _propagate(n,event){$.ui.plugin.call(this,n,[event,this.ui()]);n!=="resize"&&this._trigger(n,event,this.ui());},plugins:{},ui:function ui(){return {originalElement:this.originalElement,element:this.element,helper:this.helper,position:this.position,size:this.size,originalSize:this.originalSize,originalPosition:this.originalPosition};}}); /*
	 * Resizable Extensions
	 */$.ui.plugin.add("resizable","animate",{stop:function stop(event){var that=$(this).data("ui-resizable"),o=that.options,pr=that._proportionallyResizeElements,ista=pr.length&&/textarea/i.test(pr[0].nodeName),soffseth=ista&&$.ui.hasScroll(pr[0],"left") /* TODO - jump height */?0:that.sizeDiff.height,soffsetw=ista?0:that.sizeDiff.width,style={width:that.size.width-soffsetw,height:that.size.height-soffseth},left=parseInt(that.element.css("left"),10)+(that.position.left-that.originalPosition.left)||null,top=parseInt(that.element.css("top"),10)+(that.position.top-that.originalPosition.top)||null;that.element.animate($.extend(style,top&&left?{top:top,left:left}:{}),{duration:o.animateDuration,easing:o.animateEasing,step:function step(){var data={width:parseInt(that.element.css("width"),10),height:parseInt(that.element.css("height"),10),top:parseInt(that.element.css("top"),10),left:parseInt(that.element.css("left"),10)};if(pr&&pr.length){$(pr[0]).css({width:data.width,height:data.height});} // propagating resize, and updating values for each animation step
	that._updateCache(data);that._propagate("resize",event);}});}});$.ui.plugin.add("resizable","containment",{start:function start(){var element,p,co,ch,cw,width,height,that=$(this).data("ui-resizable"),o=that.options,el=that.element,oc=o.containment,ce=oc instanceof $?oc.get(0):/parent/.test(oc)?el.parent().get(0):oc;if(!ce){return;}that.containerElement=$(ce);if(/document/.test(oc)||oc===document){that.containerOffset={left:0,top:0};that.containerPosition={left:0,top:0};that.parentData={element:$(document),left:0,top:0,width:$(document).width(),height:$(document).height()||document.body.parentNode.scrollHeight};} // i'm a node, so compute top, left, right, bottom
	else {element=$(ce);p=[];$(["Top","Right","Left","Bottom"]).each(function(i,name){p[i]=num(element.css("padding"+name));});that.containerOffset=element.offset();that.containerPosition=element.position();that.containerSize={height:element.innerHeight()-p[3],width:element.innerWidth()-p[1]};co=that.containerOffset;ch=that.containerSize.height;cw=that.containerSize.width;width=$.ui.hasScroll(ce,"left")?ce.scrollWidth:cw;height=$.ui.hasScroll(ce)?ce.scrollHeight:ch;that.parentData={element:ce,left:co.left,top:co.top,width:width,height:height};}},resize:function resize(event){var woset,hoset,isParent,isOffsetRelative,that=$(this).data("ui-resizable"),o=that.options,co=that.containerOffset,cp=that.position,pRatio=that._aspectRatio||event.shiftKey,cop={top:0,left:0},ce=that.containerElement;if(ce[0]!==document&&/static/.test(ce.css("position"))){cop=co;}if(cp.left<(that._helper?co.left:0)){that.size.width=that.size.width+(that._helper?that.position.left-co.left:that.position.left-cop.left);if(pRatio){that.size.height=that.size.width/that.aspectRatio;}that.position.left=o.helper?co.left:0;}if(cp.top<(that._helper?co.top:0)){that.size.height=that.size.height+(that._helper?that.position.top-co.top:that.position.top);if(pRatio){that.size.width=that.size.height*that.aspectRatio;}that.position.top=that._helper?co.top:0;}that.offset.left=that.parentData.left+that.position.left;that.offset.top=that.parentData.top+that.position.top;woset=Math.abs((that._helper?that.offset.left-cop.left:that.offset.left-cop.left)+that.sizeDiff.width);hoset=Math.abs((that._helper?that.offset.top-cop.top:that.offset.top-co.top)+that.sizeDiff.height);isParent=that.containerElement.get(0)===that.element.parent().get(0);isOffsetRelative=/relative|absolute/.test(that.containerElement.css("position"));if(isParent&&isOffsetRelative){woset-=that.parentData.left;}if(woset+that.size.width>=that.parentData.width){that.size.width=that.parentData.width-woset;if(pRatio){that.size.height=that.size.width/that.aspectRatio;}}if(hoset+that.size.height>=that.parentData.height){that.size.height=that.parentData.height-hoset;if(pRatio){that.size.width=that.size.height*that.aspectRatio;}}},stop:function stop(){var that=$(this).data("ui-resizable"),o=that.options,co=that.containerOffset,cop=that.containerPosition,ce=that.containerElement,helper=$(that.helper),ho=helper.offset(),w=helper.outerWidth()-that.sizeDiff.width,h=helper.outerHeight()-that.sizeDiff.height;if(that._helper&&!o.animate&&/relative/.test(ce.css("position"))){$(this).css({left:ho.left-cop.left-co.left,width:w,height:h});}if(that._helper&&!o.animate&&/static/.test(ce.css("position"))){$(this).css({left:ho.left-cop.left-co.left,width:w,height:h});}}});$.ui.plugin.add("resizable","alsoResize",{start:function start(){var that=$(this).data("ui-resizable"),o=that.options,_store=function _store(exp){$(exp).each(function(){var el=$(this);el.data("ui-resizable-alsoresize",{width:parseInt(el.width(),10),height:parseInt(el.height(),10),left:parseInt(el.css("left"),10),top:parseInt(el.css("top"),10)});});};if(_typeof(o.alsoResize)==="object"&&!o.alsoResize.parentNode){if(o.alsoResize.length){o.alsoResize=o.alsoResize[0];_store(o.alsoResize);}else {$.each(o.alsoResize,function(exp){_store(exp);});}}else {_store(o.alsoResize);}},resize:function resize(event,ui){var that=$(this).data("ui-resizable"),o=that.options,os=that.originalSize,op=that.originalPosition,delta={height:that.size.height-os.height||0,width:that.size.width-os.width||0,top:that.position.top-op.top||0,left:that.position.left-op.left||0},_alsoResize=function _alsoResize(exp,c){$(exp).each(function(){var el=$(this),start=$(this).data("ui-resizable-alsoresize"),style={},css=c&&c.length?c:el.parents(ui.originalElement[0]).length?["width","height"]:["width","height","top","left"];$.each(css,function(i,prop){var sum=(start[prop]||0)+(delta[prop]||0);if(sum&&sum>=0){style[prop]=sum||null;}});el.css(style);});};if(_typeof(o.alsoResize)==="object"&&!o.alsoResize.nodeType){$.each(o.alsoResize,function(exp,c){_alsoResize(exp,c);});}else {_alsoResize(o.alsoResize);}},stop:function stop(){$(this).removeData("resizable-alsoresize");}});$.ui.plugin.add("resizable","ghost",{start:function start(){var that=$(this).data("ui-resizable"),o=that.options,cs=that.size;that.ghost=that.originalElement.clone();that.ghost.css({opacity:0.25,display:"block",position:"relative",height:cs.height,width:cs.width,margin:0,left:0,top:0}).addClass("ui-resizable-ghost").addClass(typeof o.ghost==="string"?o.ghost:"");that.ghost.appendTo(that.helper);},resize:function resize(){var that=$(this).data("ui-resizable");if(that.ghost){that.ghost.css({position:"relative",height:that.size.height,width:that.size.width});}},stop:function stop(){var that=$(this).data("ui-resizable");if(that.ghost&&that.helper){that.helper.get(0).removeChild(that.ghost.get(0));}}});$.ui.plugin.add("resizable","grid",{resize:function resize(){var that=$(this).data("ui-resizable"),o=that.options,cs=that.size,os=that.originalSize,op=that.originalPosition,a=that.axis,grid=typeof o.grid==="number"?[o.grid,o.grid]:o.grid,gridX=grid[0]||1,gridY=grid[1]||1,ox=Math.round((cs.width-os.width)/gridX)*gridX,oy=Math.round((cs.height-os.height)/gridY)*gridY,newWidth=os.width+ox,newHeight=os.height+oy,isMaxWidth=o.maxWidth&&o.maxWidth<newWidth,isMaxHeight=o.maxHeight&&o.maxHeight<newHeight,isMinWidth=o.minWidth&&o.minWidth>newWidth,isMinHeight=o.minHeight&&o.minHeight>newHeight;o.grid=grid;if(isMinWidth){newWidth=newWidth+gridX;}if(isMinHeight){newHeight=newHeight+gridY;}if(isMaxWidth){newWidth=newWidth-gridX;}if(isMaxHeight){newHeight=newHeight-gridY;}if(/^(se|s|e)$/.test(a)){that.size.width=newWidth;that.size.height=newHeight;}else if(/^(ne)$/.test(a)){that.size.width=newWidth;that.size.height=newHeight;that.position.top=op.top-oy;}else if(/^(sw)$/.test(a)){that.size.width=newWidth;that.size.height=newHeight;that.position.left=op.left-ox;}else {that.size.width=newWidth;that.size.height=newHeight;that.position.top=op.top-oy;that.position.left=op.left-ox;}}});})(jQuery);(function($,undefined){$.widget("ui.selectable",$.ui.mouse,{version:"1.10.3",options:{appendTo:"body",autoRefresh:true,distance:0,filter:"*",tolerance:"touch", // callbacks
	selected:null,selecting:null,start:null,stop:null,unselected:null,unselecting:null},_create:function _create(){var selectees,that=this;this.element.addClass("ui-selectable");this.dragged=false; // cache selectee children based on filter
	this.refresh=function(){selectees=$(that.options.filter,that.element[0]);selectees.addClass("ui-selectee");selectees.each(function(){var $this=$(this),pos=$this.offset();$.data(this,"selectable-item",{element:this,$element:$this,left:pos.left,top:pos.top,right:pos.left+$this.outerWidth(),bottom:pos.top+$this.outerHeight(),startselected:false,selected:$this.hasClass("ui-selected"),selecting:$this.hasClass("ui-selecting"),unselecting:$this.hasClass("ui-unselecting")});});};this.refresh();this.selectees=selectees.addClass("ui-selectee");this._mouseInit();this.helper=$("<div class='ui-selectable-helper'></div>");},_destroy:function _destroy(){this.selectees.removeClass("ui-selectee").removeData("selectable-item");this.element.removeClass("ui-selectable ui-selectable-disabled");this._mouseDestroy();},_mouseStart:function _mouseStart(event){var that=this,options=this.options;this.opos=[event.pageX,event.pageY];if(this.options.disabled){return;}this.selectees=$(options.filter,this.element[0]);this._trigger("start",event);$(options.appendTo).append(this.helper); // position helper (lasso)
	this.helper.css({"left":event.pageX,"top":event.pageY,"width":0,"height":0});if(options.autoRefresh){this.refresh();}this.selectees.filter(".ui-selected").each(function(){var selectee=$.data(this,"selectable-item");selectee.startselected=true;if(!event.metaKey&&!event.ctrlKey){selectee.$element.removeClass("ui-selected");selectee.selected=false;selectee.$element.addClass("ui-unselecting");selectee.unselecting=true; // selectable UNSELECTING callback
	that._trigger("unselecting",event,{unselecting:selectee.element});}});$(event.target).parents().addBack().each(function(){var doSelect,selectee=$.data(this,"selectable-item");if(selectee){doSelect=!event.metaKey&&!event.ctrlKey||!selectee.$element.hasClass("ui-selected");selectee.$element.removeClass(doSelect?"ui-unselecting":"ui-selected").addClass(doSelect?"ui-selecting":"ui-unselecting");selectee.unselecting=!doSelect;selectee.selecting=doSelect;selectee.selected=doSelect; // selectable (UN)SELECTING callback
	if(doSelect){that._trigger("selecting",event,{selecting:selectee.element});}else {that._trigger("unselecting",event,{unselecting:selectee.element});}return false;}});},_mouseDrag:function _mouseDrag(event){this.dragged=true;if(this.options.disabled){return;}var tmp,that=this,options=this.options,x1=this.opos[0],y1=this.opos[1],x2=event.pageX,y2=event.pageY;if(x1>x2){tmp=x2;x2=x1;x1=tmp;}if(y1>y2){tmp=y2;y2=y1;y1=tmp;}this.helper.css({left:x1,top:y1,width:x2-x1,height:y2-y1});this.selectees.each(function(){var selectee=$.data(this,"selectable-item"),hit=false; //prevent helper from being selected if appendTo: selectable
	if(!selectee||selectee.element===that.element[0]){return;}if(options.tolerance==="touch"){hit=!(selectee.left>x2||selectee.right<x1||selectee.top>y2||selectee.bottom<y1);}else if(options.tolerance==="fit"){hit=selectee.left>x1&&selectee.right<x2&&selectee.top>y1&&selectee.bottom<y2;}if(hit){ // SELECT
	if(selectee.selected){selectee.$element.removeClass("ui-selected");selectee.selected=false;}if(selectee.unselecting){selectee.$element.removeClass("ui-unselecting");selectee.unselecting=false;}if(!selectee.selecting){selectee.$element.addClass("ui-selecting");selectee.selecting=true; // selectable SELECTING callback
	that._trigger("selecting",event,{selecting:selectee.element});}}else { // UNSELECT
	if(selectee.selecting){if((event.metaKey||event.ctrlKey)&&selectee.startselected){selectee.$element.removeClass("ui-selecting");selectee.selecting=false;selectee.$element.addClass("ui-selected");selectee.selected=true;}else {selectee.$element.removeClass("ui-selecting");selectee.selecting=false;if(selectee.startselected){selectee.$element.addClass("ui-unselecting");selectee.unselecting=true;} // selectable UNSELECTING callback
	that._trigger("unselecting",event,{unselecting:selectee.element});}}if(selectee.selected){if(!event.metaKey&&!event.ctrlKey&&!selectee.startselected){selectee.$element.removeClass("ui-selected");selectee.selected=false;selectee.$element.addClass("ui-unselecting");selectee.unselecting=true; // selectable UNSELECTING callback
	that._trigger("unselecting",event,{unselecting:selectee.element});}}}});return false;},_mouseStop:function _mouseStop(event){var that=this;this.dragged=false;$(".ui-unselecting",this.element[0]).each(function(){var selectee=$.data(this,"selectable-item");selectee.$element.removeClass("ui-unselecting");selectee.unselecting=false;selectee.startselected=false;that._trigger("unselected",event,{unselected:selectee.element});});$(".ui-selecting",this.element[0]).each(function(){var selectee=$.data(this,"selectable-item");selectee.$element.removeClass("ui-selecting").addClass("ui-selected");selectee.selecting=false;selectee.selected=true;selectee.startselected=true;that._trigger("selected",event,{selected:selectee.element});});this._trigger("stop",event);this.helper.remove();return false;}});})(jQuery);(function($,undefined){ /*jshint loopfunc: true */function isOverAxis(x,reference,size){return x>reference&&x<reference+size;}function isFloating(item){return (/left|right/.test(item.css("float"))||/inline|table-cell/.test(item.css("display")));}$.widget("ui.sortable",$.ui.mouse,{version:"1.10.3",widgetEventPrefix:"sort",ready:false,options:{appendTo:"parent",axis:false,connectWith:false,containment:false,cursor:"auto",cursorAt:false,dropOnEmpty:true,forcePlaceholderSize:false,forceHelperSize:false,grid:false,handle:false,helper:"original",items:"> *",opacity:false,placeholder:false,revert:false,scroll:true,scrollSensitivity:20,scrollSpeed:20,scope:"default",tolerance:"intersect",zIndex:1000, // callbacks
	activate:null,beforeStop:null,change:null,deactivate:null,out:null,over:null,receive:null,remove:null,sort:null,start:null,stop:null,update:null},_create:function _create(){var o=this.options;this.containerCache={};this.element.addClass("ui-sortable"); //Get the items
	this.refresh(); //Let's determine if the items are being displayed horizontally
	this.floating=this.items.length?o.axis==="x"||isFloating(this.items[0].item):false; //Let's determine the parent's offset
	this.offset=this.element.offset(); //Initialize mouse events for interaction
	this._mouseInit(); //We're ready to go
	this.ready=true;},_destroy:function _destroy(){this.element.removeClass("ui-sortable ui-sortable-disabled");this._mouseDestroy();for(var i=this.items.length-1;i>=0;i--){this.items[i].item.removeData(this.widgetName+"-item");}return this;},_setOption:function _setOption(key,value){if(key==="disabled"){this.options[key]=value;this.widget().toggleClass("ui-sortable-disabled",!!value);}else { // Don't call widget base _setOption for disable as it adds ui-state-disabled class
	$.Widget.prototype._setOption.apply(this,arguments);}},_mouseCapture:function _mouseCapture(event,overrideHandle){var currentItem=null,validHandle=false,that=this;if(this.reverting){return false;}if(this.options.disabled||this.options.type==="static"){return false;} //We have to refresh the items data once first
	this._refreshItems(event); //Find out if the clicked node (or one of its parents) is a actual item in this.items
	$(event.target).parents().each(function(){if($.data(this,that.widgetName+"-item")===that){currentItem=$(this);return false;}});if($.data(event.target,that.widgetName+"-item")===that){currentItem=$(event.target);}if(!currentItem){return false;}if(this.options.handle&&!overrideHandle){$(this.options.handle,currentItem).find("*").addBack().each(function(){if(this===event.target){validHandle=true;}});if(!validHandle){return false;}}this.currentItem=currentItem;this._removeCurrentsFromItems();return true;},_mouseStart:function _mouseStart(event,overrideHandle,noActivation){var i,body,o=this.options;this.currentContainer=this; //We only need to call refreshPositions, because the refreshItems call has been moved to mouseCapture
	this.refreshPositions(); //Create and append the visible helper
	this.helper=this._createHelper(event); //Cache the helper size
	this._cacheHelperProportions(); /*
			 * - Position generation -
			 * This block generates everything position related - it's the core of draggables.
			 */ //Cache the margins of the original element
	this._cacheMargins(); //Get the next scrolling parent
	this.scrollParent=this.helper.scrollParent(); //The element's absolute position on the page minus margins
	this.offset=this.currentItem.offset();this.offset={top:this.offset.top-this.margins.top,left:this.offset.left-this.margins.left};$.extend(this.offset,{click:{ //Where the click happened, relative to the element
	left:event.pageX-this.offset.left,top:event.pageY-this.offset.top},parent:this._getParentOffset(),relative:this._getRelativeOffset() //This is a relative to absolute position minus the actual position calculation - only used for relative positioned helper
	}); // Only after we got the offset, we can change the helper's position to absolute
	// TODO: Still need to figure out a way to make relative sorting possible
	this.helper.css("position","absolute");this.cssPosition=this.helper.css("position"); //Generate the original position
	this.originalPosition=this._generatePosition(event);this.originalPageX=event.pageX;this.originalPageY=event.pageY; //Adjust the mouse offset relative to the helper if "cursorAt" is supplied
	o.cursorAt&&this._adjustOffsetFromHelper(o.cursorAt); //Cache the former DOM position
	this.domPosition={prev:this.currentItem.prev()[0],parent:this.currentItem.parent()[0]}; //If the helper is not the original, hide the original so it's not playing any role during the drag, won't cause anything bad this way
	if(this.helper[0]!==this.currentItem[0]){this.currentItem.hide();} //Create the placeholder
	this._createPlaceholder(); //Set a containment if given in the options
	if(o.containment){this._setContainment();}if(o.cursor&&o.cursor!=="auto"){ // cursor option
	body=this.document.find("body"); // support: IE
	this.storedCursor=body.css("cursor");body.css("cursor",o.cursor);this.storedStylesheet=$("<style>*{ cursor: "+o.cursor+" !important; }</style>").appendTo(body);}if(o.opacity){ // opacity option
	if(this.helper.css("opacity")){this._storedOpacity=this.helper.css("opacity");}this.helper.css("opacity",o.opacity);}if(o.zIndex){ // zIndex option
	if(this.helper.css("zIndex")){this._storedZIndex=this.helper.css("zIndex");}this.helper.css("zIndex",o.zIndex);} //Prepare scrolling
	if(this.scrollParent[0]!==document&&this.scrollParent[0].tagName!=="HTML"){this.overflowOffset=this.scrollParent.offset();} //Call callbacks
	this._trigger("start",event,this._uiHash()); //Recache the helper size
	if(!this._preserveHelperProportions){this._cacheHelperProportions();} //Post "activate" events to possible containers
	if(!noActivation){for(i=this.containers.length-1;i>=0;i--){this.containers[i]._trigger("activate",event,this._uiHash(this));}} //Prepare possible droppables
	if($.ui.ddmanager){$.ui.ddmanager.current=this;}if($.ui.ddmanager&&!o.dropBehaviour){$.ui.ddmanager.prepareOffsets(this,event);}this.dragging=true;this.helper.addClass("ui-sortable-helper");this._mouseDrag(event); //Execute the drag once - this causes the helper not to be visible before getting its correct position
	return true;},_mouseDrag:function _mouseDrag(event){var i,item,itemElement,intersection,o=this.options,scrolled=false; //Compute the helpers position
	this.position=this._generatePosition(event);this.positionAbs=this._convertPositionTo("absolute");if(!this.lastPositionAbs){this.lastPositionAbs=this.positionAbs;} //Do scrolling
	if(this.options.scroll){if(this.scrollParent[0]!==document&&this.scrollParent[0].tagName!=="HTML"){if(this.overflowOffset.top+this.scrollParent[0].offsetHeight-event.pageY<o.scrollSensitivity){this.scrollParent[0].scrollTop=scrolled=this.scrollParent[0].scrollTop+o.scrollSpeed;}else if(event.pageY-this.overflowOffset.top<o.scrollSensitivity){this.scrollParent[0].scrollTop=scrolled=this.scrollParent[0].scrollTop-o.scrollSpeed;}if(this.overflowOffset.left+this.scrollParent[0].offsetWidth-event.pageX<o.scrollSensitivity){this.scrollParent[0].scrollLeft=scrolled=this.scrollParent[0].scrollLeft+o.scrollSpeed;}else if(event.pageX-this.overflowOffset.left<o.scrollSensitivity){this.scrollParent[0].scrollLeft=scrolled=this.scrollParent[0].scrollLeft-o.scrollSpeed;}}else {if(event.pageY-$(document).scrollTop()<o.scrollSensitivity){scrolled=$(document).scrollTop($(document).scrollTop()-o.scrollSpeed);}else if($(window).height()-(event.pageY-$(document).scrollTop())<o.scrollSensitivity){scrolled=$(document).scrollTop($(document).scrollTop()+o.scrollSpeed);}if(event.pageX-$(document).scrollLeft()<o.scrollSensitivity){scrolled=$(document).scrollLeft($(document).scrollLeft()-o.scrollSpeed);}else if($(window).width()-(event.pageX-$(document).scrollLeft())<o.scrollSensitivity){scrolled=$(document).scrollLeft($(document).scrollLeft()+o.scrollSpeed);}}if(scrolled!==false&&$.ui.ddmanager&&!o.dropBehaviour){$.ui.ddmanager.prepareOffsets(this,event);}} //Regenerate the absolute position used for position checks
	this.positionAbs=this._convertPositionTo("absolute"); //Set the helper position
	if(!this.options.axis||this.options.axis!=="y"){this.helper[0].style.left=this.position.left+"px";}if(!this.options.axis||this.options.axis!=="x"){this.helper[0].style.top=this.position.top+"px";} //Rearrange
	for(i=this.items.length-1;i>=0;i--){ //Cache variables and intersection, continue if no intersection
	item=this.items[i];itemElement=item.item[0];intersection=this._intersectsWithPointer(item);if(!intersection){continue;} // Only put the placeholder inside the current Container, skip all
	// items form other containers. This works because when moving
	// an item from one container to another the
	// currentContainer is switched before the placeholder is moved.
	//
	// Without this moving items in "sub-sortables" can cause the placeholder to jitter
	// beetween the outer and inner container.
	if(item.instance!==this.currentContainer){continue;} // cannot intersect with itself
	// no useless actions that have been done before
	// no action if the item moved is the parent of the item checked
	if(itemElement!==this.currentItem[0]&&this.placeholder[intersection===1?"next":"prev"]()[0]!==itemElement&&!$.contains(this.placeholder[0],itemElement)&&(this.options.type==="semi-dynamic"?!$.contains(this.element[0],itemElement):true)){this.direction=intersection===1?"down":"up";if(this.options.tolerance==="pointer"||this._intersectsWithSides(item)){this._rearrange(event,item);}else {break;}this._trigger("change",event,this._uiHash());break;}} //Post events to containers
	this._contactContainers(event); //Interconnect with droppables
	if($.ui.ddmanager){$.ui.ddmanager.drag(this,event);} //Call callbacks
	this._trigger("sort",event,this._uiHash());this.lastPositionAbs=this.positionAbs;return false;},_mouseStop:function _mouseStop(event,noPropagation){if(!event){return;} //If we are using droppables, inform the manager about the drop
	if($.ui.ddmanager&&!this.options.dropBehaviour){$.ui.ddmanager.drop(this,event);}if(this.options.revert){var that=this,cur=this.placeholder.offset(),axis=this.options.axis,animation={};if(!axis||axis==="x"){animation.left=cur.left-this.offset.parent.left-this.margins.left+(this.offsetParent[0]===document.body?0:this.offsetParent[0].scrollLeft);}if(!axis||axis==="y"){animation.top=cur.top-this.offset.parent.top-this.margins.top+(this.offsetParent[0]===document.body?0:this.offsetParent[0].scrollTop);}this.reverting=true;$(this.helper).animate(animation,parseInt(this.options.revert,10)||500,function(){that._clear(event);});}else {this._clear(event,noPropagation);}return false;},cancel:function cancel(){if(this.dragging){this._mouseUp({target:null});if(this.options.helper==="original"){this.currentItem.css(this._storedCSS).removeClass("ui-sortable-helper");}else {this.currentItem.show();} //Post deactivating events to containers
	for(var i=this.containers.length-1;i>=0;i--){this.containers[i]._trigger("deactivate",null,this._uiHash(this));if(this.containers[i].containerCache.over){this.containers[i]._trigger("out",null,this._uiHash(this));this.containers[i].containerCache.over=0;}}}if(this.placeholder){ //$(this.placeholder[0]).remove(); would have been the jQuery way - unfortunately, it unbinds ALL events from the original node!
	if(this.placeholder[0].parentNode){this.placeholder[0].parentNode.removeChild(this.placeholder[0]);}if(this.options.helper!=="original"&&this.helper&&this.helper[0].parentNode){this.helper.remove();}$.extend(this,{helper:null,dragging:false,reverting:false,_noFinalSort:null});if(this.domPosition.prev){$(this.domPosition.prev).after(this.currentItem);}else {$(this.domPosition.parent).prepend(this.currentItem);}}return this;},serialize:function serialize(o){var items=this._getItemsAsjQuery(o&&o.connected),str=[];o=o||{};$(items).each(function(){var res=($(o.item||this).attr(o.attribute||"id")||"").match(o.expression||/(.+)[\-=_](.+)/);if(res){str.push((o.key||res[1]+"[]")+"="+(o.key&&o.expression?res[1]:res[2]));}});if(!str.length&&o.key){str.push(o.key+"=");}return str.join("&");},toArray:function toArray(o){var items=this._getItemsAsjQuery(o&&o.connected),ret=[];o=o||{};items.each(function(){ret.push($(o.item||this).attr(o.attribute||"id")||"");});return ret;}, /* Be careful with the following core functions */_intersectsWith:function _intersectsWith(item){var x1=this.positionAbs.left,x2=x1+this.helperProportions.width,y1=this.positionAbs.top,y2=y1+this.helperProportions.height,l=item.left,r=l+item.width,t=item.top,b=t+item.height,dyClick=this.offset.click.top,dxClick=this.offset.click.left,isOverElementHeight=this.options.axis==="x"||y1+dyClick>t&&y1+dyClick<b,isOverElementWidth=this.options.axis==="y"||x1+dxClick>l&&x1+dxClick<r,isOverElement=isOverElementHeight&&isOverElementWidth;if(this.options.tolerance==="pointer"||this.options.forcePointerForContainers||this.options.tolerance!=="pointer"&&this.helperProportions[this.floating?"width":"height"]>item[this.floating?"width":"height"]){return isOverElement;}else {return l<x1+this.helperProportions.width/2&& // Right Half
	x2-this.helperProportions.width/2<r&& // Left Half
	t<y1+this.helperProportions.height/2&& // Bottom Half
	y2-this.helperProportions.height/2<b; // Top Half
	}},_intersectsWithPointer:function _intersectsWithPointer(item){var isOverElementHeight=this.options.axis==="x"||isOverAxis(this.positionAbs.top+this.offset.click.top,item.top,item.height),isOverElementWidth=this.options.axis==="y"||isOverAxis(this.positionAbs.left+this.offset.click.left,item.left,item.width),isOverElement=isOverElementHeight&&isOverElementWidth,verticalDirection=this._getDragVerticalDirection(),horizontalDirection=this._getDragHorizontalDirection();if(!isOverElement){return false;}return this.floating?horizontalDirection&&horizontalDirection==="right"||verticalDirection==="down"?2:1:verticalDirection&&(verticalDirection==="down"?2:1);},_intersectsWithSides:function _intersectsWithSides(item){var isOverBottomHalf=isOverAxis(this.positionAbs.top+this.offset.click.top,item.top+item.height/2,item.height),isOverRightHalf=isOverAxis(this.positionAbs.left+this.offset.click.left,item.left+item.width/2,item.width),verticalDirection=this._getDragVerticalDirection(),horizontalDirection=this._getDragHorizontalDirection();if(this.floating&&horizontalDirection){return horizontalDirection==="right"&&isOverRightHalf||horizontalDirection==="left"&&!isOverRightHalf;}else {return verticalDirection&&(verticalDirection==="down"&&isOverBottomHalf||verticalDirection==="up"&&!isOverBottomHalf);}},_getDragVerticalDirection:function _getDragVerticalDirection(){var delta=this.positionAbs.top-this.lastPositionAbs.top;return delta!==0&&(delta>0?"down":"up");},_getDragHorizontalDirection:function _getDragHorizontalDirection(){var delta=this.positionAbs.left-this.lastPositionAbs.left;return delta!==0&&(delta>0?"right":"left");},refresh:function refresh(event){this._refreshItems(event);this.refreshPositions();return this;},_connectWith:function _connectWith(){var options=this.options;return options.connectWith.constructor===String?[options.connectWith]:options.connectWith;},_getItemsAsjQuery:function _getItemsAsjQuery(connected){var i,j,cur,inst,items=[],queries=[],connectWith=this._connectWith();if(connectWith&&connected){for(i=connectWith.length-1;i>=0;i--){cur=$(connectWith[i]);for(j=cur.length-1;j>=0;j--){inst=$.data(cur[j],this.widgetFullName);if(inst&&inst!==this&&!inst.options.disabled){queries.push([$.isFunction(inst.options.items)?inst.options.items.call(inst.element):$(inst.options.items,inst.element).not(".ui-sortable-helper").not(".ui-sortable-placeholder"),inst]);}}}}queries.push([$.isFunction(this.options.items)?this.options.items.call(this.element,null,{options:this.options,item:this.currentItem}):$(this.options.items,this.element).not(".ui-sortable-helper").not(".ui-sortable-placeholder"),this]);for(i=queries.length-1;i>=0;i--){queries[i][0].each(function(){items.push(this);});}return $(items);},_removeCurrentsFromItems:function _removeCurrentsFromItems(){var list=this.currentItem.find(":data("+this.widgetName+"-item)");this.items=$.grep(this.items,function(item){for(var j=0;j<list.length;j++){if(list[j]===item.item[0]){return false;}}return true;});},_refreshItems:function _refreshItems(event){this.items=[];this.containers=[this];var i,j,cur,inst,targetData,_queries,item,queriesLength,items=this.items,queries=[[$.isFunction(this.options.items)?this.options.items.call(this.element[0],event,{item:this.currentItem}):$(this.options.items,this.element),this]],connectWith=this._connectWith();if(connectWith&&this.ready){ //Shouldn't be run the first time through due to massive slow-down
	for(i=connectWith.length-1;i>=0;i--){cur=$(connectWith[i]);for(j=cur.length-1;j>=0;j--){inst=$.data(cur[j],this.widgetFullName);if(inst&&inst!==this&&!inst.options.disabled){queries.push([$.isFunction(inst.options.items)?inst.options.items.call(inst.element[0],event,{item:this.currentItem}):$(inst.options.items,inst.element),inst]);this.containers.push(inst);}}}}for(i=queries.length-1;i>=0;i--){targetData=queries[i][1];_queries=queries[i][0];for(j=0,queriesLength=_queries.length;j<queriesLength;j++){item=$(_queries[j]);item.data(this.widgetName+"-item",targetData); // Data for target checking (mouse manager)
	items.push({item:item,instance:targetData,width:0,height:0,left:0,top:0});}}},refreshPositions:function refreshPositions(fast){ //This has to be redone because due to the item being moved out/into the offsetParent, the offsetParent's position will change
	if(this.offsetParent&&this.helper){this.offset.parent=this._getParentOffset();}var i,item,t,p;for(i=this.items.length-1;i>=0;i--){item=this.items[i]; //We ignore calculating positions of all connected containers when we're not over them
	if(item.instance!==this.currentContainer&&this.currentContainer&&item.item[0]!==this.currentItem[0]){continue;}t=this.options.toleranceElement?$(this.options.toleranceElement,item.item):item.item;if(!fast){item.width=t.outerWidth();item.height=t.outerHeight();}p=t.offset();item.left=p.left;item.top=p.top;}if(this.options.custom&&this.options.custom.refreshContainers){this.options.custom.refreshContainers.call(this);}else {for(i=this.containers.length-1;i>=0;i--){p=this.containers[i].element.offset();this.containers[i].containerCache.left=p.left;this.containers[i].containerCache.top=p.top;this.containers[i].containerCache.width=this.containers[i].element.outerWidth();this.containers[i].containerCache.height=this.containers[i].element.outerHeight();}}return this;},_createPlaceholder:function _createPlaceholder(that){that=that||this;var className,o=that.options;if(!o.placeholder||o.placeholder.constructor===String){className=o.placeholder;o.placeholder={element:function element(){var nodeName=that.currentItem[0].nodeName.toLowerCase(),element=$("<"+nodeName+">",that.document[0]).addClass(className||that.currentItem[0].className+" ui-sortable-placeholder").removeClass("ui-sortable-helper");if(nodeName==="tr"){that.currentItem.children().each(function(){$("<td>&#160;</td>",that.document[0]).attr("colspan",$(this).attr("colspan")||1).appendTo(element);});}else if(nodeName==="img"){element.attr("src",that.currentItem.attr("src"));}if(!className){element.css("visibility","hidden");}return element;},update:function update(container,p){ // 1. If a className is set as 'placeholder option, we don't force sizes - the class is responsible for that
	// 2. The option 'forcePlaceholderSize can be enabled to force it even if a class name is specified
	if(className&&!o.forcePlaceholderSize){return;} //If the element doesn't have a actual height by itself (without styles coming from a stylesheet), it receives the inline height from the dragged item
	if(!p.height()){p.height(that.currentItem.innerHeight()-parseInt(that.currentItem.css("paddingTop")||0,10)-parseInt(that.currentItem.css("paddingBottom")||0,10));}if(!p.width()){p.width(that.currentItem.innerWidth()-parseInt(that.currentItem.css("paddingLeft")||0,10)-parseInt(that.currentItem.css("paddingRight")||0,10));}}};} //Create the placeholder
	that.placeholder=$(o.placeholder.element.call(that.element,that.currentItem)); //Append it after the actual current item
	that.currentItem.after(that.placeholder); //Update the size of the placeholder (TODO: Logic to fuzzy, see line 316/317)
	o.placeholder.update(that,that.placeholder);},_contactContainers:function _contactContainers(event){var i,j,dist,itemWithLeastDistance,posProperty,sizeProperty,base,cur,nearBottom,floating,innermostContainer=null,innermostIndex=null; // get innermost container that intersects with item
	for(i=this.containers.length-1;i>=0;i--){ // never consider a container that's located within the item itself
	if($.contains(this.currentItem[0],this.containers[i].element[0])){continue;}if(this._intersectsWith(this.containers[i].containerCache)){ // if we've already found a container and it's more "inner" than this, then continue
	if(innermostContainer&&$.contains(this.containers[i].element[0],innermostContainer.element[0])){continue;}innermostContainer=this.containers[i];innermostIndex=i;}else { // container doesn't intersect. trigger "out" event if necessary
	if(this.containers[i].containerCache.over){this.containers[i]._trigger("out",event,this._uiHash(this));this.containers[i].containerCache.over=0;}}} // if no intersecting containers found, return
	if(!innermostContainer){return;} // move the item into the container if it's not there already
	if(this.containers.length===1){if(!this.containers[innermostIndex].containerCache.over){this.containers[innermostIndex]._trigger("over",event,this._uiHash(this));this.containers[innermostIndex].containerCache.over=1;}}else { //When entering a new container, we will find the item with the least distance and append our item near it
	dist=10000;itemWithLeastDistance=null;floating=innermostContainer.floating||isFloating(this.currentItem);posProperty=floating?"left":"top";sizeProperty=floating?"width":"height";base=this.positionAbs[posProperty]+this.offset.click[posProperty];for(j=this.items.length-1;j>=0;j--){if(!$.contains(this.containers[innermostIndex].element[0],this.items[j].item[0])){continue;}if(this.items[j].item[0]===this.currentItem[0]){continue;}if(floating&&!isOverAxis(this.positionAbs.top+this.offset.click.top,this.items[j].top,this.items[j].height)){continue;}cur=this.items[j].item.offset()[posProperty];nearBottom=false;if(Math.abs(cur-base)>Math.abs(cur+this.items[j][sizeProperty]-base)){nearBottom=true;cur+=this.items[j][sizeProperty];}if(Math.abs(cur-base)<dist){dist=Math.abs(cur-base);itemWithLeastDistance=this.items[j];this.direction=nearBottom?"up":"down";}} //Check if dropOnEmpty is enabled
	if(!itemWithLeastDistance&&!this.options.dropOnEmpty){return;}if(this.currentContainer===this.containers[innermostIndex]){return;}itemWithLeastDistance?this._rearrange(event,itemWithLeastDistance,null,true):this._rearrange(event,null,this.containers[innermostIndex].element,true);this._trigger("change",event,this._uiHash());this.containers[innermostIndex]._trigger("change",event,this._uiHash(this));this.currentContainer=this.containers[innermostIndex]; //Update the placeholder
	this.options.placeholder.update(this.currentContainer,this.placeholder);this.containers[innermostIndex]._trigger("over",event,this._uiHash(this));this.containers[innermostIndex].containerCache.over=1;}},_createHelper:function _createHelper(event){var o=this.options,helper=$.isFunction(o.helper)?$(o.helper.apply(this.element[0],[event,this.currentItem])):o.helper==="clone"?this.currentItem.clone():this.currentItem; //Add the helper to the DOM if that didn't happen already
	if(!helper.parents("body").length){$(o.appendTo!=="parent"?o.appendTo:this.currentItem[0].parentNode)[0].appendChild(helper[0]);}if(helper[0]===this.currentItem[0]){this._storedCSS={width:this.currentItem[0].style.width,height:this.currentItem[0].style.height,position:this.currentItem.css("position"),top:this.currentItem.css("top"),left:this.currentItem.css("left")};}if(!helper[0].style.width||o.forceHelperSize){helper.width(this.currentItem.width());}if(!helper[0].style.height||o.forceHelperSize){helper.height(this.currentItem.height());}return helper;},_adjustOffsetFromHelper:function _adjustOffsetFromHelper(obj){if(typeof obj==="string"){obj=obj.split(" ");}if($.isArray(obj)){obj={left:+obj[0],top:+obj[1]||0};}if("left" in obj){this.offset.click.left=obj.left+this.margins.left;}if("right" in obj){this.offset.click.left=this.helperProportions.width-obj.right+this.margins.left;}if("top" in obj){this.offset.click.top=obj.top+this.margins.top;}if("bottom" in obj){this.offset.click.top=this.helperProportions.height-obj.bottom+this.margins.top;}},_getParentOffset:function _getParentOffset(){ //Get the offsetParent and cache its position
	this.offsetParent=this.helper.offsetParent();var po=this.offsetParent.offset(); // This is a special case where we need to modify a offset calculated on start, since the following happened:
	// 1. The position of the helper is absolute, so it's position is calculated based on the next positioned parent
	// 2. The actual offset parent is a child of the scroll parent, and the scroll parent isn't the document, which means that
	//    the scroll is included in the initial calculation of the offset of the parent, and never recalculated upon drag
	if(this.cssPosition==="absolute"&&this.scrollParent[0]!==document&&$.contains(this.scrollParent[0],this.offsetParent[0])){po.left+=this.scrollParent.scrollLeft();po.top+=this.scrollParent.scrollTop();} // This needs to be actually done for all browsers, since pageX/pageY includes this information
	// with an ugly IE fix
	if(this.offsetParent[0]===document.body||this.offsetParent[0].tagName&&this.offsetParent[0].tagName.toLowerCase()==="html"&&$.ui.ie){po={top:0,left:0};}return {top:po.top+(parseInt(this.offsetParent.css("borderTopWidth"),10)||0),left:po.left+(parseInt(this.offsetParent.css("borderLeftWidth"),10)||0)};},_getRelativeOffset:function _getRelativeOffset(){if(this.cssPosition==="relative"){var p=this.currentItem.position();return {top:p.top-(parseInt(this.helper.css("top"),10)||0)+this.scrollParent.scrollTop(),left:p.left-(parseInt(this.helper.css("left"),10)||0)+this.scrollParent.scrollLeft()};}else {return {top:0,left:0};}},_cacheMargins:function _cacheMargins(){this.margins={left:parseInt(this.currentItem.css("marginLeft"),10)||0,top:parseInt(this.currentItem.css("marginTop"),10)||0};},_cacheHelperProportions:function _cacheHelperProportions(){this.helperProportions={width:this.helper.outerWidth(),height:this.helper.outerHeight()};},_setContainment:function _setContainment(){var ce,co,over,o=this.options;if(o.containment==="parent"){o.containment=this.helper[0].parentNode;}if(o.containment==="document"||o.containment==="window"){this.containment=[0-this.offset.relative.left-this.offset.parent.left,0-this.offset.relative.top-this.offset.parent.top,$(o.containment==="document"?document:window).width()-this.helperProportions.width-this.margins.left,($(o.containment==="document"?document:window).height()||document.body.parentNode.scrollHeight)-this.helperProportions.height-this.margins.top];}if(!/^(document|window|parent)$/.test(o.containment)){ce=$(o.containment)[0];co=$(o.containment).offset();over=$(ce).css("overflow")!=="hidden";this.containment=[co.left+(parseInt($(ce).css("borderLeftWidth"),10)||0)+(parseInt($(ce).css("paddingLeft"),10)||0)-this.margins.left,co.top+(parseInt($(ce).css("borderTopWidth"),10)||0)+(parseInt($(ce).css("paddingTop"),10)||0)-this.margins.top,co.left+(over?Math.max(ce.scrollWidth,ce.offsetWidth):ce.offsetWidth)-(parseInt($(ce).css("borderLeftWidth"),10)||0)-(parseInt($(ce).css("paddingRight"),10)||0)-this.helperProportions.width-this.margins.left,co.top+(over?Math.max(ce.scrollHeight,ce.offsetHeight):ce.offsetHeight)-(parseInt($(ce).css("borderTopWidth"),10)||0)-(parseInt($(ce).css("paddingBottom"),10)||0)-this.helperProportions.height-this.margins.top];}},_convertPositionTo:function _convertPositionTo(d,pos){if(!pos){pos=this.position;}var mod=d==="absolute"?1:-1,scroll=this.cssPosition==="absolute"&&!(this.scrollParent[0]!==document&&$.contains(this.scrollParent[0],this.offsetParent[0]))?this.offsetParent:this.scrollParent,scrollIsRootNode=/(html|body)/i.test(scroll[0].tagName);return {top:pos.top+ // The absolute mouse position
	this.offset.relative.top*mod+ // Only for relative positioned nodes: Relative offset from element to offset parent
	this.offset.parent.top*mod- // The offsetParent's offset without borders (offset + border)
	(this.cssPosition==="fixed"?-this.scrollParent.scrollTop():scrollIsRootNode?0:scroll.scrollTop())*mod,left:pos.left+ // The absolute mouse position
	this.offset.relative.left*mod+ // Only for relative positioned nodes: Relative offset from element to offset parent
	this.offset.parent.left*mod- // The offsetParent's offset without borders (offset + border)
	(this.cssPosition==="fixed"?-this.scrollParent.scrollLeft():scrollIsRootNode?0:scroll.scrollLeft())*mod};},_generatePosition:function _generatePosition(event){var top,left,o=this.options,pageX=event.pageX,pageY=event.pageY,scroll=this.cssPosition==="absolute"&&!(this.scrollParent[0]!==document&&$.contains(this.scrollParent[0],this.offsetParent[0]))?this.offsetParent:this.scrollParent,scrollIsRootNode=/(html|body)/i.test(scroll[0].tagName); // This is another very weird special case that only happens for relative elements:
	// 1. If the css position is relative
	// 2. and the scroll parent is the document or similar to the offset parent
	// we have to refresh the relative offset during the scroll so there are no jumps
	if(this.cssPosition==="relative"&&!(this.scrollParent[0]!==document&&this.scrollParent[0]!==this.offsetParent[0])){this.offset.relative=this._getRelativeOffset();} /*
			 * - Position constraining -
			 * Constrain the position to a mix of grid, containment.
			 */if(this.originalPosition){ //If we are not dragging yet, we won't check for options
	if(this.containment){if(event.pageX-this.offset.click.left<this.containment[0]){pageX=this.containment[0]+this.offset.click.left;}if(event.pageY-this.offset.click.top<this.containment[1]){pageY=this.containment[1]+this.offset.click.top;}if(event.pageX-this.offset.click.left>this.containment[2]){pageX=this.containment[2]+this.offset.click.left;}if(event.pageY-this.offset.click.top>this.containment[3]){pageY=this.containment[3]+this.offset.click.top;}}if(o.grid){top=this.originalPageY+Math.round((pageY-this.originalPageY)/o.grid[1])*o.grid[1];pageY=this.containment?top-this.offset.click.top>=this.containment[1]&&top-this.offset.click.top<=this.containment[3]?top:top-this.offset.click.top>=this.containment[1]?top-o.grid[1]:top+o.grid[1]:top;left=this.originalPageX+Math.round((pageX-this.originalPageX)/o.grid[0])*o.grid[0];pageX=this.containment?left-this.offset.click.left>=this.containment[0]&&left-this.offset.click.left<=this.containment[2]?left:left-this.offset.click.left>=this.containment[0]?left-o.grid[0]:left+o.grid[0]:left;}}return {top:pageY- // The absolute mouse position
	this.offset.click.top- // Click offset (relative to the element)
	this.offset.relative.top- // Only for relative positioned nodes: Relative offset from element to offset parent
	this.offset.parent.top+( // The offsetParent's offset without borders (offset + border)
	this.cssPosition==="fixed"?-this.scrollParent.scrollTop():scrollIsRootNode?0:scroll.scrollTop()),left:pageX- // The absolute mouse position
	this.offset.click.left- // Click offset (relative to the element)
	this.offset.relative.left- // Only for relative positioned nodes: Relative offset from element to offset parent
	this.offset.parent.left+( // The offsetParent's offset without borders (offset + border)
	this.cssPosition==="fixed"?-this.scrollParent.scrollLeft():scrollIsRootNode?0:scroll.scrollLeft())};},_rearrange:function _rearrange(event,i,a,hardRefresh){a?a[0].appendChild(this.placeholder[0]):i.item[0].parentNode.insertBefore(this.placeholder[0],this.direction==="down"?i.item[0]:i.item[0].nextSibling); //Various things done here to improve the performance:
	// 1. we create a setTimeout, that calls refreshPositions
	// 2. on the instance, we have a counter variable, that get's higher after every append
	// 3. on the local scope, we copy the counter variable, and check in the timeout, if it's still the same
	// 4. this lets only the last addition to the timeout stack through
	this.counter=this.counter?++this.counter:1;var counter=this.counter;this._delay(function(){if(counter===this.counter){this.refreshPositions(!hardRefresh); //Precompute after each DOM insertion, NOT on mousemove
	}});},_clear:function _clear(event,noPropagation){this.reverting=false; // We delay all events that have to be triggered to after the point where the placeholder has been removed and
	// everything else normalized again
	var i,delayedTriggers=[]; // We first have to update the dom position of the actual currentItem
	// Note: don't do it if the current item is already removed (by a user), or it gets reappended (see #4088)
	if(!this._noFinalSort&&this.currentItem.parent().length){this.placeholder.before(this.currentItem);}this._noFinalSort=null;if(this.helper[0]===this.currentItem[0]){for(i in this._storedCSS){if(this._storedCSS[i]==="auto"||this._storedCSS[i]==="static"){this._storedCSS[i]="";}}this.currentItem.css(this._storedCSS).removeClass("ui-sortable-helper");}else {this.currentItem.show();}if(this.fromOutside&&!noPropagation){delayedTriggers.push(function(event){this._trigger("receive",event,this._uiHash(this.fromOutside));});}if((this.fromOutside||this.domPosition.prev!==this.currentItem.prev().not(".ui-sortable-helper")[0]||this.domPosition.parent!==this.currentItem.parent()[0])&&!noPropagation){delayedTriggers.push(function(event){this._trigger("update",event,this._uiHash());}); //Trigger update callback if the DOM position has changed
	} // Check if the items Container has Changed and trigger appropriate
	// events.
	if(this!==this.currentContainer){if(!noPropagation){delayedTriggers.push(function(event){this._trigger("remove",event,this._uiHash());});delayedTriggers.push(function(c){return function(event){c._trigger("receive",event,this._uiHash(this));};}.call(this,this.currentContainer));delayedTriggers.push(function(c){return function(event){c._trigger("update",event,this._uiHash(this));};}.call(this,this.currentContainer));}} //Post events to containers
	for(i=this.containers.length-1;i>=0;i--){if(!noPropagation){delayedTriggers.push(function(c){return function(event){c._trigger("deactivate",event,this._uiHash(this));};}.call(this,this.containers[i]));}if(this.containers[i].containerCache.over){delayedTriggers.push(function(c){return function(event){c._trigger("out",event,this._uiHash(this));};}.call(this,this.containers[i]));this.containers[i].containerCache.over=0;}} //Do what was originally in plugins
	if(this.storedCursor){this.document.find("body").css("cursor",this.storedCursor);this.storedStylesheet.remove();}if(this._storedOpacity){this.helper.css("opacity",this._storedOpacity);}if(this._storedZIndex){this.helper.css("zIndex",this._storedZIndex==="auto"?"":this._storedZIndex);}this.dragging=false;if(this.cancelHelperRemoval){if(!noPropagation){this._trigger("beforeStop",event,this._uiHash());for(i=0;i<delayedTriggers.length;i++){delayedTriggers[i].call(this,event);} //Trigger all delayed events
	this._trigger("stop",event,this._uiHash());}this.fromOutside=false;return false;}if(!noPropagation){this._trigger("beforeStop",event,this._uiHash());} //$(this.placeholder[0]).remove(); would have been the jQuery way - unfortunately, it unbinds ALL events from the original node!
	this.placeholder[0].parentNode.removeChild(this.placeholder[0]);if(this.helper[0]!==this.currentItem[0]){this.helper.remove();}this.helper=null;if(!noPropagation){for(i=0;i<delayedTriggers.length;i++){delayedTriggers[i].call(this,event);} //Trigger all delayed events
	this._trigger("stop",event,this._uiHash());}this.fromOutside=false;return true;},_trigger:function _trigger(){if($.Widget.prototype._trigger.apply(this,arguments)===false){this.cancel();}},_uiHash:function _uiHash(_inst){var inst=_inst||this;return {helper:inst.helper,placeholder:inst.placeholder||$([]),position:inst.position,originalPosition:inst.originalPosition,offset:inst.positionAbs,item:inst.currentItem,sender:_inst?_inst.element:null};}});})(jQuery);(function($,undefined){var dataSpace="ui-effects-";$.effects={effect:{}}; /*!
	 * jQuery Color Animations v2.1.2
	 * https://github.com/jquery/jquery-color
	 *
	 * Copyright 2013 jQuery Foundation and other contributors
	 * Released under the MIT license.
	 * http://jquery.org/license
	 *
	 * Date: Wed Jan 16 08:47:09 2013 -0600
	 */(function(jQuery,undefined){var stepHooks="backgroundColor borderBottomColor borderLeftColor borderRightColor borderTopColor color columnRuleColor outlineColor textDecorationColor textEmphasisColor", // plusequals test for += 100 -= 100
	rplusequals=/^([\-+])=\s*(\d+\.?\d*)/, // a set of RE's that can match strings and generate color tuples.
	stringParsers=[{re:/rgba?\(\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(\d{1,3})\s*(?:,\s*(\d?(?:\.\d+)?)\s*)?\)/,parse:function parse(execResult){return [execResult[1],execResult[2],execResult[3],execResult[4]];}},{re:/rgba?\(\s*(\d+(?:\.\d+)?)\%\s*,\s*(\d+(?:\.\d+)?)\%\s*,\s*(\d+(?:\.\d+)?)\%\s*(?:,\s*(\d?(?:\.\d+)?)\s*)?\)/,parse:function parse(execResult){return [execResult[1]*2.55,execResult[2]*2.55,execResult[3]*2.55,execResult[4]];}},{ // this regex ignores A-F because it's compared against an already lowercased string
	re:/#([a-f0-9]{2})([a-f0-9]{2})([a-f0-9]{2})/,parse:function parse(execResult){return [parseInt(execResult[1],16),parseInt(execResult[2],16),parseInt(execResult[3],16)];}},{ // this regex ignores A-F because it's compared against an already lowercased string
	re:/#([a-f0-9])([a-f0-9])([a-f0-9])/,parse:function parse(execResult){return [parseInt(execResult[1]+execResult[1],16),parseInt(execResult[2]+execResult[2],16),parseInt(execResult[3]+execResult[3],16)];}},{re:/hsla?\(\s*(\d+(?:\.\d+)?)\s*,\s*(\d+(?:\.\d+)?)\%\s*,\s*(\d+(?:\.\d+)?)\%\s*(?:,\s*(\d?(?:\.\d+)?)\s*)?\)/,space:"hsla",parse:function parse(execResult){return [execResult[1],execResult[2]/100,execResult[3]/100,execResult[4]];}}], // jQuery.Color( )
	color=jQuery.Color=function(color,green,blue,alpha){return new jQuery.Color.fn.parse(color,green,blue,alpha);},spaces={rgba:{props:{red:{idx:0,type:"byte"},green:{idx:1,type:"byte"},blue:{idx:2,type:"byte"}}},hsla:{props:{hue:{idx:0,type:"degrees"},saturation:{idx:1,type:"percent"},lightness:{idx:2,type:"percent"}}}},propTypes={"byte":{floor:true,max:255},"percent":{max:1},"degrees":{mod:360,floor:true}},support=color.support={}, // element for support tests
	supportElem=jQuery("<p>")[0], // colors = jQuery.Color.names
	colors, // local aliases of functions called often
	each=jQuery.each; // determine rgba support immediately
	supportElem.style.cssText="background-color:rgba(1,1,1,.5)";support.rgba=supportElem.style.backgroundColor.indexOf("rgba")>-1; // define cache name and alpha properties
	// for rgba and hsla spaces
	each(spaces,function(spaceName,space){space.cache="_"+spaceName;space.props.alpha={idx:3,type:"percent",def:1};});function clamp(value,prop,allowEmpty){var type=propTypes[prop.type]||{};if(value==null){return allowEmpty||!prop.def?null:prop.def;} // ~~ is an short way of doing floor for positive numbers
	value=type.floor?~ ~value:parseFloat(value); // IE will pass in empty strings as value for alpha,
	// which will hit this case
	if(isNaN(value)){return prop.def;}if(type.mod){ // we add mod before modding to make sure that negatives values
	// get converted properly: -10 -> 350
	return (value+type.mod)%type.mod;} // for now all property types without mod have min and max
	return 0>value?0:type.max<value?type.max:value;}function stringParse(string){var inst=color(),rgba=inst._rgba=[];string=string.toLowerCase();each(stringParsers,function(i,parser){var parsed,match=parser.re.exec(string),values=match&&parser.parse(match),spaceName=parser.space||"rgba";if(values){parsed=inst[spaceName](values); // if this was an rgba parse the assignment might happen twice
	// oh well....
	inst[spaces[spaceName].cache]=parsed[spaces[spaceName].cache];rgba=inst._rgba=parsed._rgba; // exit each( stringParsers ) here because we matched
	return false;}}); // Found a stringParser that handled it
	if(rgba.length){ // if this came from a parsed string, force "transparent" when alpha is 0
	// chrome, (and maybe others) return "transparent" as rgba(0,0,0,0)
	if(rgba.join()==="0,0,0,0"){jQuery.extend(rgba,colors.transparent);}return inst;} // named colors
	return colors[string];}color.fn=jQuery.extend(color.prototype,{parse:function parse(red,green,blue,alpha){if(red===undefined){this._rgba=[null,null,null,null];return this;}if(red.jquery||red.nodeType){red=jQuery(red).css(green);green=undefined;}var inst=this,type=jQuery.type(red),rgba=this._rgba=[]; // more than 1 argument specified - assume ( red, green, blue, alpha )
	if(green!==undefined){red=[red,green,blue,alpha];type="array";}if(type==="string"){return this.parse(stringParse(red)||colors._default);}if(type==="array"){each(spaces.rgba.props,function(key,prop){rgba[prop.idx]=clamp(red[prop.idx],prop);});return this;}if(type==="object"){if(red instanceof color){each(spaces,function(spaceName,space){if(red[space.cache]){inst[space.cache]=red[space.cache].slice();}});}else {each(spaces,function(spaceName,space){var cache=space.cache;each(space.props,function(key,prop){ // if the cache doesn't exist, and we know how to convert
	if(!inst[cache]&&space.to){ // if the value was null, we don't need to copy it
	// if the key was alpha, we don't need to copy it either
	if(key==="alpha"||red[key]==null){return;}inst[cache]=space.to(inst._rgba);} // this is the only case where we allow nulls for ALL properties.
	// call clamp with alwaysAllowEmpty
	inst[cache][prop.idx]=clamp(red[key],prop,true);}); // everything defined but alpha?
	if(inst[cache]&&jQuery.inArray(null,inst[cache].slice(0,3))<0){ // use the default of 1
	inst[cache][3]=1;if(space.from){inst._rgba=space.from(inst[cache]);}}});}return this;}},is:function is(compare){var is=color(compare),same=true,inst=this;each(spaces,function(_,space){var localCache,isCache=is[space.cache];if(isCache){localCache=inst[space.cache]||space.to&&space.to(inst._rgba)||[];each(space.props,function(_,prop){if(isCache[prop.idx]!=null){same=isCache[prop.idx]===localCache[prop.idx];return same;}});}return same;});return same;},_space:function _space(){var used=[],inst=this;each(spaces,function(spaceName,space){if(inst[space.cache]){used.push(spaceName);}});return used.pop();},transition:function transition(other,distance){var end=color(other),spaceName=end._space(),space=spaces[spaceName],startColor=this.alpha()===0?color("transparent"):this,start=startColor[space.cache]||space.to(startColor._rgba),result=start.slice();end=end[space.cache];each(space.props,function(key,prop){var index=prop.idx,startValue=start[index],endValue=end[index],type=propTypes[prop.type]||{}; // if null, don't override start value
	if(endValue===null){return;} // if null - use end
	if(startValue===null){result[index]=endValue;}else {if(type.mod){if(endValue-startValue>type.mod/2){startValue+=type.mod;}else if(startValue-endValue>type.mod/2){startValue-=type.mod;}}result[index]=clamp((endValue-startValue)*distance+startValue,prop);}});return this[spaceName](result);},blend:function blend(opaque){ // if we are already opaque - return ourself
	if(this._rgba[3]===1){return this;}var rgb=this._rgba.slice(),a=rgb.pop(),blend=color(opaque)._rgba;return color(jQuery.map(rgb,function(v,i){return (1-a)*blend[i]+a*v;}));},toRgbaString:function toRgbaString(){var prefix="rgba(",rgba=jQuery.map(this._rgba,function(v,i){return v==null?i>2?1:0:v;});if(rgba[3]===1){rgba.pop();prefix="rgb(";}return prefix+rgba.join()+")";},toHslaString:function toHslaString(){var prefix="hsla(",hsla=jQuery.map(this.hsla(),function(v,i){if(v==null){v=i>2?1:0;} // catch 1 and 2
	if(i&&i<3){v=Math.round(v*100)+"%";}return v;});if(hsla[3]===1){hsla.pop();prefix="hsl(";}return prefix+hsla.join()+")";},toHexString:function toHexString(includeAlpha){var rgba=this._rgba.slice(),alpha=rgba.pop();if(includeAlpha){rgba.push(~ ~(alpha*255));}return "#"+jQuery.map(rgba,function(v){ // default to 0 when nulls exist
	v=(v||0).toString(16);return v.length===1?"0"+v:v;}).join("");},toString:function toString(){return this._rgba[3]===0?"transparent":this.toRgbaString();}});color.fn.parse.prototype=color.fn; // hsla conversions adapted from:
	// https://code.google.com/p/maashaack/source/browse/packages/graphics/trunk/src/graphics/colors/HUE2RGB.as?r=5021
	function hue2rgb(p,q,h){h=(h+1)%1;if(h*6<1){return p+(q-p)*h*6;}if(h*2<1){return q;}if(h*3<2){return p+(q-p)*(2/3-h)*6;}return p;}spaces.hsla.to=function(rgba){if(rgba[0]==null||rgba[1]==null||rgba[2]==null){return [null,null,null,rgba[3]];}var r=rgba[0]/255,g=rgba[1]/255,b=rgba[2]/255,a=rgba[3],max=Math.max(r,g,b),min=Math.min(r,g,b),diff=max-min,add=max+min,l=add*0.5,h,s;if(min===max){h=0;}else if(r===max){h=60*(g-b)/diff+360;}else if(g===max){h=60*(b-r)/diff+120;}else {h=60*(r-g)/diff+240;} // chroma (diff) == 0 means greyscale which, by definition, saturation = 0%
	// otherwise, saturation is based on the ratio of chroma (diff) to lightness (add)
	if(diff===0){s=0;}else if(l<=0.5){s=diff/add;}else {s=diff/(2-add);}return [Math.round(h)%360,s,l,a==null?1:a];};spaces.hsla.from=function(hsla){if(hsla[0]==null||hsla[1]==null||hsla[2]==null){return [null,null,null,hsla[3]];}var h=hsla[0]/360,s=hsla[1],l=hsla[2],a=hsla[3],q=l<=0.5?l*(1+s):l+s-l*s,p=2*l-q;return [Math.round(hue2rgb(p,q,h+1/3)*255),Math.round(hue2rgb(p,q,h)*255),Math.round(hue2rgb(p,q,h-1/3)*255),a];};each(spaces,function(spaceName,space){var props=space.props,cache=space.cache,to=space.to,from=space.from; // makes rgba() and hsla()
	color.fn[spaceName]=function(value){ // generate a cache for this space if it doesn't exist
	if(to&&!this[cache]){this[cache]=to(this._rgba);}if(value===undefined){return this[cache].slice();}var ret,type=jQuery.type(value),arr=type==="array"||type==="object"?value:arguments,local=this[cache].slice();each(props,function(key,prop){var val=arr[type==="object"?key:prop.idx];if(val==null){val=local[prop.idx];}local[prop.idx]=clamp(val,prop);});if(from){ret=color(from(local));ret[cache]=local;return ret;}else {return color(local);}}; // makes red() green() blue() alpha() hue() saturation() lightness()
	each(props,function(key,prop){ // alpha is included in more than one space
	if(color.fn[key]){return;}color.fn[key]=function(value){var vtype=jQuery.type(value),fn=key==="alpha"?this._hsla?"hsla":"rgba":spaceName,local=this[fn](),cur=local[prop.idx],match;if(vtype==="undefined"){return cur;}if(vtype==="function"){value=value.call(this,cur);vtype=jQuery.type(value);}if(value==null&&prop.empty){return this;}if(vtype==="string"){match=rplusequals.exec(value);if(match){value=cur+parseFloat(match[2])*(match[1]==="+"?1:-1);}}local[prop.idx]=value;return this[fn](local);};});}); // add cssHook and .fx.step function for each named hook.
	// accept a space separated string of properties
	color.hook=function(hook){var hooks=hook.split(" ");each(hooks,function(i,hook){jQuery.cssHooks[hook]={set:function set(elem,value){var parsed,curElem,backgroundColor="";if(value!=="transparent"&&(jQuery.type(value)!=="string"||(parsed=stringParse(value)))){value=color(parsed||value);if(!support.rgba&&value._rgba[3]!==1){curElem=hook==="backgroundColor"?elem.parentNode:elem;while((backgroundColor===""||backgroundColor==="transparent")&&curElem&&curElem.style){try{backgroundColor=jQuery.css(curElem,"backgroundColor");curElem=curElem.parentNode;}catch(e){}}value=value.blend(backgroundColor&&backgroundColor!=="transparent"?backgroundColor:"_default");}value=value.toRgbaString();}try{elem.style[hook]=value;}catch(e){ // wrapped to prevent IE from throwing errors on "invalid" values like 'auto' or 'inherit'
	}}};jQuery.fx.step[hook]=function(fx){if(!fx.colorInit){fx.start=color(fx.elem,hook);fx.end=color(fx.end);fx.colorInit=true;}jQuery.cssHooks[hook].set(fx.elem,fx.start.transition(fx.end,fx.pos));};});};color.hook(stepHooks);jQuery.cssHooks.borderColor={expand:function expand(value){var expanded={};each(["Top","Right","Bottom","Left"],function(i,part){expanded["border"+part+"Color"]=value;});return expanded;}}; // Basic color names only.
	// Usage of any of the other color names requires adding yourself or including
	// jquery.color.svg-names.js.
	colors=jQuery.Color.names={ // 4.1. Basic color keywords
	aqua:"#00ffff",black:"#000000",blue:"#0000ff",fuchsia:"#ff00ff",gray:"#808080",green:"#008000",lime:"#00ff00",maroon:"#800000",navy:"#000080",olive:"#808000",purple:"#800080",red:"#ff0000",silver:"#c0c0c0",teal:"#008080",white:"#ffffff",yellow:"#ffff00", // 4.2.3. "transparent" color keyword
	transparent:[null,null,null,0],_default:"#ffffff"};})(jQuery); /******************************************************************************/ /****************************** CLASS ANIMATIONS ******************************/ /******************************************************************************/(function(){var classAnimationActions=["add","remove","toggle"],shorthandStyles={border:1,borderBottom:1,borderColor:1,borderLeft:1,borderRight:1,borderTop:1,borderWidth:1,margin:1,padding:1};$.each(["borderLeftStyle","borderRightStyle","borderBottomStyle","borderTopStyle"],function(_,prop){$.fx.step[prop]=function(fx){if(fx.end!=="none"&&!fx.setAttr||fx.pos===1&&!fx.setAttr){jQuery.style(fx.elem,prop,fx.end);fx.setAttr=true;}};});function getElementStyles(elem){var key,len,style=elem.ownerDocument.defaultView?elem.ownerDocument.defaultView.getComputedStyle(elem,null):elem.currentStyle,styles={};if(style&&style.length&&style[0]&&style[style[0]]){len=style.length;while(len--){key=style[len];if(typeof style[key]==="string"){styles[$.camelCase(key)]=style[key];}} // support: Opera, IE <9
	}else {for(key in style){if(typeof style[key]==="string"){styles[key]=style[key];}}}return styles;}function styleDifference(oldStyle,newStyle){var diff={},name,value;for(name in newStyle){value=newStyle[name];if(oldStyle[name]!==value){if(!shorthandStyles[name]){if($.fx.step[name]||!isNaN(parseFloat(value))){diff[name]=value;}}}}return diff;} // support: jQuery <1.8
	if(!$.fn.addBack){$.fn.addBack=function(selector){return this.add(selector==null?this.prevObject:this.prevObject.filter(selector));};}$.effects.animateClass=function(value,duration,easing,callback){var o=$.speed(duration,easing,callback);return this.queue(function(){var animated=$(this),baseClass=animated.attr("class")||"",applyClassChange,allAnimations=o.children?animated.find("*").addBack():animated; // map the animated objects to store the original styles.
	allAnimations=allAnimations.map(function(){var el=$(this);return {el:el,start:getElementStyles(this)};}); // apply class change
	applyClassChange=function applyClassChange(){$.each(classAnimationActions,function(i,action){if(value[action]){animated[action+"Class"](value[action]);}});};applyClassChange(); // map all animated objects again - calculate new styles and diff
	allAnimations=allAnimations.map(function(){this.end=getElementStyles(this.el[0]);this.diff=styleDifference(this.start,this.end);return this;}); // apply original class
	animated.attr("class",baseClass); // map all animated objects again - this time collecting a promise
	allAnimations=allAnimations.map(function(){var styleInfo=this,dfd=$.Deferred(),opts=$.extend({},o,{queue:false,complete:function complete(){dfd.resolve(styleInfo);}});this.el.animate(this.diff,opts);return dfd.promise();}); // once all animations have completed:
	$.when.apply($,allAnimations.get()).done(function(){ // set the final class
	applyClassChange(); // for each animated element,
	// clear all css properties that were animated
	$.each(arguments,function(){var el=this.el;$.each(this.diff,function(key){el.css(key,"");});}); // this is guarnteed to be there if you use jQuery.speed()
	// it also handles dequeuing the next anim...
	o.complete.call(animated[0]);});});};$.fn.extend({addClass:function(orig){return function(classNames,speed,easing,callback){return speed?$.effects.animateClass.call(this,{add:classNames},speed,easing,callback):orig.apply(this,arguments);};}($.fn.addClass),removeClass:function(orig){return function(classNames,speed,easing,callback){return arguments.length>1?$.effects.animateClass.call(this,{remove:classNames},speed,easing,callback):orig.apply(this,arguments);};}($.fn.removeClass),toggleClass:function(orig){return function(classNames,force,speed,easing,callback){if(typeof force==="boolean"||force===undefined){if(!speed){ // without speed parameter
	return orig.apply(this,arguments);}else {return $.effects.animateClass.call(this,force?{add:classNames}:{remove:classNames},speed,easing,callback);}}else { // without force parameter
	return $.effects.animateClass.call(this,{toggle:classNames},force,speed,easing);}};}($.fn.toggleClass),switchClass:function switchClass(remove,add,speed,easing,callback){return $.effects.animateClass.call(this,{add:add,remove:remove},speed,easing,callback);}});})(); /******************************************************************************/ /*********************************** EFFECTS **********************************/ /******************************************************************************/(function(){$.extend($.effects,{version:"1.10.3", // Saves a set of properties in a data storage
	save:function save(element,set){for(var i=0;i<set.length;i++){if(set[i]!==null){element.data(dataSpace+set[i],element[0].style[set[i]]);}}}, // Restores a set of previously saved properties from a data storage
	restore:function restore(element,set){var val,i;for(i=0;i<set.length;i++){if(set[i]!==null){val=element.data(dataSpace+set[i]); // support: jQuery 1.6.2
	// http://bugs.jquery.com/ticket/9917
	// jQuery 1.6.2 incorrectly returns undefined for any falsy value.
	// We can't differentiate between "" and 0 here, so we just assume
	// empty string since it's likely to be a more common value...
	if(val===undefined){val="";}element.css(set[i],val);}}},setMode:function setMode(el,mode){if(mode==="toggle"){mode=el.is(":hidden")?"show":"hide";}return mode;}, // Translates a [top,left] array into a baseline value
	// this should be a little more flexible in the future to handle a string & hash
	getBaseline:function getBaseline(origin,original){var y,x;switch(origin[0]){case "top":y=0;break;case "middle":y=0.5;break;case "bottom":y=1;break;default:y=origin[0]/original.height;}switch(origin[1]){case "left":x=0;break;case "center":x=0.5;break;case "right":x=1;break;default:x=origin[1]/original.width;}return {x:x,y:y};}, // Wraps the element around a wrapper that copies position properties
	createWrapper:function createWrapper(element){ // if the element is already wrapped, return it
	if(element.parent().is(".ui-effects-wrapper")){return element.parent();} // wrap the element
	var props={width:element.outerWidth(true),height:element.outerHeight(true),"float":element.css("float")},wrapper=$("<div></div>").addClass("ui-effects-wrapper").css({fontSize:"100%",background:"transparent",border:"none",margin:0,padding:0}), // Store the size in case width/height are defined in % - Fixes #5245
	size={width:element.width(),height:element.height()},active=document.activeElement; // support: Firefox
	// Firefox incorrectly exposes anonymous content
	// https://bugzilla.mozilla.org/show_bug.cgi?id=561664
	try{active.id;}catch(e){active=document.body;}element.wrap(wrapper); // Fixes #7595 - Elements lose focus when wrapped.
	if(element[0]===active||$.contains(element[0],active)){$(active).focus();}wrapper=element.parent(); //Hotfix for jQuery 1.4 since some change in wrap() seems to actually lose the reference to the wrapped element
	// transfer positioning properties to the wrapper
	if(element.css("position")==="static"){wrapper.css({position:"relative"});element.css({position:"relative"});}else {$.extend(props,{position:element.css("position"),zIndex:element.css("z-index")});$.each(["top","left","bottom","right"],function(i,pos){props[pos]=element.css(pos);if(isNaN(parseInt(props[pos],10))){props[pos]="auto";}});element.css({position:"relative",top:0,left:0,right:"auto",bottom:"auto"});}element.css(size);return wrapper.css(props).show();},removeWrapper:function removeWrapper(element){var active=document.activeElement;if(element.parent().is(".ui-effects-wrapper")){element.parent().replaceWith(element); // Fixes #7595 - Elements lose focus when wrapped.
	if(element[0]===active||$.contains(element[0],active)){$(active).focus();}}return element;},setTransition:function setTransition(element,list,factor,value){value=value||{};$.each(list,function(i,x){var unit=element.cssUnit(x);if(unit[0]>0){value[x]=unit[0]*factor+unit[1];}});return value;}}); // return an effect options object for the given parameters:
	function _normalizeArguments(effect,options,speed,callback){ // allow passing all options as the first parameter
	if($.isPlainObject(effect)){options=effect;effect=effect.effect;} // convert to an object
	effect={effect:effect}; // catch (effect, null, ...)
	if(options==null){options={};} // catch (effect, callback)
	if($.isFunction(options)){callback=options;speed=null;options={};} // catch (effect, speed, ?)
	if(typeof options==="number"||$.fx.speeds[options]){callback=speed;speed=options;options={};} // catch (effect, options, callback)
	if($.isFunction(speed)){callback=speed;speed=null;} // add options to effect
	if(options){$.extend(effect,options);}speed=speed||options.duration;effect.duration=$.fx.off?0:typeof speed==="number"?speed:speed in $.fx.speeds?$.fx.speeds[speed]:$.fx.speeds._default;effect.complete=callback||options.complete;return effect;}function standardAnimationOption(option){ // Valid standard speeds (nothing, number, named speed)
	if(!option||typeof option==="number"||$.fx.speeds[option]){return true;} // Invalid strings - treat as "normal" speed
	if(typeof option==="string"&&!$.effects.effect[option]){return true;} // Complete callback
	if($.isFunction(option)){return true;} // Options hash (but not naming an effect)
	if((typeof option==="undefined"?"undefined":_typeof(option))==="object"&&!option.effect){return true;} // Didn't match any standard API
	return false;}$.fn.extend({effect:function effect() /* effect, options, speed, callback */{var args=_normalizeArguments.apply(this,arguments),mode=args.mode,queue=args.queue,effectMethod=$.effects.effect[args.effect];if($.fx.off||!effectMethod){ // delegate to the original method (e.g., .show()) if possible
	if(mode){return this[mode](args.duration,args.complete);}else {return this.each(function(){if(args.complete){args.complete.call(this);}});}}function run(next){var elem=$(this),complete=args.complete,mode=args.mode;function done(){if($.isFunction(complete)){complete.call(elem[0]);}if($.isFunction(next)){next();}} // If the element already has the correct final state, delegate to
	// the core methods so the internal tracking of "olddisplay" works.
	if(elem.is(":hidden")?mode==="hide":mode==="show"){elem[mode]();done();}else {effectMethod.call(elem[0],args,done);}}return queue===false?this.each(run):this.queue(queue||"fx",run);},show:function(orig){return function(option){if(standardAnimationOption(option)){return orig.apply(this,arguments);}else {var args=_normalizeArguments.apply(this,arguments);args.mode="show";return this.effect.call(this,args);}};}($.fn.show),hide:function(orig){return function(option){if(standardAnimationOption(option)){return orig.apply(this,arguments);}else {var args=_normalizeArguments.apply(this,arguments);args.mode="hide";return this.effect.call(this,args);}};}($.fn.hide),toggle:function(orig){return function(option){if(standardAnimationOption(option)||typeof option==="boolean"){return orig.apply(this,arguments);}else {var args=_normalizeArguments.apply(this,arguments);args.mode="toggle";return this.effect.call(this,args);}};}($.fn.toggle), // helper functions
	cssUnit:function cssUnit(key){var style=this.css(key),val=[];$.each(["em","px","%","pt"],function(i,unit){if(style.indexOf(unit)>0){val=[parseFloat(style),unit];}});return val;}});})(); /******************************************************************************/ /*********************************** EASING ***********************************/ /******************************************************************************/(function(){ // based on easing equations from Robert Penner (http://www.robertpenner.com/easing)
	var baseEasings={};$.each(["Quad","Cubic","Quart","Quint","Expo"],function(i,name){baseEasings[name]=function(p){return Math.pow(p,i+2);};});$.extend(baseEasings,{Sine:function Sine(p){return 1-Math.cos(p*Math.PI/2);},Circ:function Circ(p){return 1-Math.sqrt(1-p*p);},Elastic:function Elastic(p){return p===0||p===1?p:-Math.pow(2,8*(p-1))*Math.sin(((p-1)*80-7.5)*Math.PI/15);},Back:function Back(p){return p*p*(3*p-2);},Bounce:function Bounce(p){var pow2,bounce=4;while(p<((pow2=Math.pow(2,--bounce))-1)/11){}return 1/Math.pow(4,3-bounce)-7.5625*Math.pow((pow2*3-2)/22-p,2);}});$.each(baseEasings,function(name,easeIn){$.easing["easeIn"+name]=easeIn;$.easing["easeOut"+name]=function(p){return 1-easeIn(1-p);};$.easing["easeInOut"+name]=function(p){return p<0.5?easeIn(p*2)/2:1-easeIn(p*-2+2)/2;};});})();})(jQuery);(function($,undefined){var uid=0,hideProps={},showProps={};hideProps.height=hideProps.paddingTop=hideProps.paddingBottom=hideProps.borderTopWidth=hideProps.borderBottomWidth="hide";showProps.height=showProps.paddingTop=showProps.paddingBottom=showProps.borderTopWidth=showProps.borderBottomWidth="show";$.widget("ui.accordion",{version:"1.10.3",options:{active:0,animate:{},collapsible:false,event:"click",header:"> li > :first-child,> :not(li):even",heightStyle:"auto",icons:{activeHeader:"ui-icon-triangle-1-s",header:"ui-icon-triangle-1-e"}, // callbacks
	activate:null,beforeActivate:null},_create:function _create(){var options=this.options;this.prevShow=this.prevHide=$();this.element.addClass("ui-accordion ui-widget ui-helper-reset") // ARIA
	.attr("role","tablist"); // don't allow collapsible: false and active: false / null
	if(!options.collapsible&&(options.active===false||options.active==null)){options.active=0;}this._processPanels(); // handle negative values
	if(options.active<0){options.active+=this.headers.length;}this._refresh();},_getCreateEventData:function _getCreateEventData(){return {header:this.active,panel:!this.active.length?$():this.active.next(),content:!this.active.length?$():this.active.next()};},_createIcons:function _createIcons(){var icons=this.options.icons;if(icons){$("<span>").addClass("ui-accordion-header-icon ui-icon "+icons.header).prependTo(this.headers);this.active.children(".ui-accordion-header-icon").removeClass(icons.header).addClass(icons.activeHeader);this.headers.addClass("ui-accordion-icons");}},_destroyIcons:function _destroyIcons(){this.headers.removeClass("ui-accordion-icons").children(".ui-accordion-header-icon").remove();},_destroy:function _destroy(){var contents; // clean up main element
	this.element.removeClass("ui-accordion ui-widget ui-helper-reset").removeAttr("role"); // clean up headers
	this.headers.removeClass("ui-accordion-header ui-accordion-header-active ui-helper-reset ui-state-default ui-corner-all ui-state-active ui-state-disabled ui-corner-top").removeAttr("role").removeAttr("aria-selected").removeAttr("aria-controls").removeAttr("tabIndex").each(function(){if(/^ui-accordion/.test(this.id)){this.removeAttribute("id");}});this._destroyIcons(); // clean up content panels
	contents=this.headers.next().css("display","").removeAttr("role").removeAttr("aria-expanded").removeAttr("aria-hidden").removeAttr("aria-labelledby").removeClass("ui-helper-reset ui-widget-content ui-corner-bottom ui-accordion-content ui-accordion-content-active ui-state-disabled").each(function(){if(/^ui-accordion/.test(this.id)){this.removeAttribute("id");}});if(this.options.heightStyle!=="content"){contents.css("height","");}},_setOption:function _setOption(key,value){if(key==="active"){ // _activate() will handle invalid values and update this.options
	this._activate(value);return;}if(key==="event"){if(this.options.event){this._off(this.headers,this.options.event);}this._setupEvents(value);}this._super(key,value); // setting collapsible: false while collapsed; open first panel
	if(key==="collapsible"&&!value&&this.options.active===false){this._activate(0);}if(key==="icons"){this._destroyIcons();if(value){this._createIcons();}} // #5332 - opacity doesn't cascade to positioned elements in IE
	// so we need to add the disabled class to the headers and panels
	if(key==="disabled"){this.headers.add(this.headers.next()).toggleClass("ui-state-disabled",!!value);}},_keydown:function _keydown(event){ /*jshint maxcomplexity:15*/if(event.altKey||event.ctrlKey){return;}var keyCode=$.ui.keyCode,length=this.headers.length,currentIndex=this.headers.index(event.target),toFocus=false;switch(event.keyCode){case keyCode.RIGHT:case keyCode.DOWN:toFocus=this.headers[(currentIndex+1)%length];break;case keyCode.LEFT:case keyCode.UP:toFocus=this.headers[(currentIndex-1+length)%length];break;case keyCode.SPACE:case keyCode.ENTER:this._eventHandler(event);break;case keyCode.HOME:toFocus=this.headers[0];break;case keyCode.END:toFocus=this.headers[length-1];break;}if(toFocus){$(event.target).attr("tabIndex",-1);$(toFocus).attr("tabIndex",0);toFocus.focus();event.preventDefault();}},_panelKeyDown:function _panelKeyDown(event){if(event.keyCode===$.ui.keyCode.UP&&event.ctrlKey){$(event.currentTarget).prev().focus();}},refresh:function refresh(){var options=this.options;this._processPanels(); // was collapsed or no panel
	if(options.active===false&&options.collapsible===true||!this.headers.length){options.active=false;this.active=$(); // active false only when collapsible is true
	}else if(options.active===false){this._activate(0); // was active, but active panel is gone
	}else if(this.active.length&&!$.contains(this.element[0],this.active[0])){ // all remaining panel are disabled
	if(this.headers.length===this.headers.find(".ui-state-disabled").length){options.active=false;this.active=$(); // activate previous panel
	}else {this._activate(Math.max(0,options.active-1));} // was active, active panel still exists
	}else { // make sure active index is correct
	options.active=this.headers.index(this.active);}this._destroyIcons();this._refresh();},_processPanels:function _processPanels(){this.headers=this.element.find(this.options.header).addClass("ui-accordion-header ui-helper-reset ui-state-default ui-corner-all");this.headers.next().addClass("ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom").filter(":not(.ui-accordion-content-active)").hide();},_refresh:function _refresh(){var maxHeight,options=this.options,heightStyle=options.heightStyle,parent=this.element.parent(),accordionId=this.accordionId="ui-accordion-"+(this.element.attr("id")||++uid);this.active=this._findActive(options.active).addClass("ui-accordion-header-active ui-state-active ui-corner-top").removeClass("ui-corner-all");this.active.next().addClass("ui-accordion-content-active").show();this.headers.attr("role","tab").each(function(i){var header=$(this),headerId=header.attr("id"),panel=header.next(),panelId=panel.attr("id");if(!headerId){headerId=accordionId+"-header-"+i;header.attr("id",headerId);}if(!panelId){panelId=accordionId+"-panel-"+i;panel.attr("id",panelId);}header.attr("aria-controls",panelId);panel.attr("aria-labelledby",headerId);}).next().attr("role","tabpanel");this.headers.not(this.active).attr({"aria-selected":"false",tabIndex:-1}).next().attr({"aria-expanded":"false","aria-hidden":"true"}).hide(); // make sure at least one header is in the tab order
	if(!this.active.length){this.headers.eq(0).attr("tabIndex",0);}else {this.active.attr({"aria-selected":"true",tabIndex:0}).next().attr({"aria-expanded":"true","aria-hidden":"false"});}this._createIcons();this._setupEvents(options.event);if(heightStyle==="fill"){maxHeight=parent.height();this.element.siblings(":visible").each(function(){var elem=$(this),position=elem.css("position");if(position==="absolute"||position==="fixed"){return;}maxHeight-=elem.outerHeight(true);});this.headers.each(function(){maxHeight-=$(this).outerHeight(true);});this.headers.next().each(function(){$(this).height(Math.max(0,maxHeight-$(this).innerHeight()+$(this).height()));}).css("overflow","auto");}else if(heightStyle==="auto"){maxHeight=0;this.headers.next().each(function(){maxHeight=Math.max(maxHeight,$(this).css("height","").height());}).height(maxHeight);}},_activate:function _activate(index){var active=this._findActive(index)[0]; // trying to activate the already active panel
	if(active===this.active[0]){return;} // trying to collapse, simulate a click on the currently active header
	active=active||this.active[0];this._eventHandler({target:active,currentTarget:active,preventDefault:$.noop});},_findActive:function _findActive(selector){return typeof selector==="number"?this.headers.eq(selector):$();},_setupEvents:function _setupEvents(event){var events={keydown:"_keydown"};if(event){$.each(event.split(" "),function(index,eventName){events[eventName]="_eventHandler";});}this._off(this.headers.add(this.headers.next()));this._on(this.headers,events);this._on(this.headers.next(),{keydown:"_panelKeyDown"});this._hoverable(this.headers);this._focusable(this.headers);},_eventHandler:function _eventHandler(event){var options=this.options,active=this.active,clicked=$(event.currentTarget),clickedIsActive=clicked[0]===active[0],collapsing=clickedIsActive&&options.collapsible,toShow=collapsing?$():clicked.next(),toHide=active.next(),eventData={oldHeader:active,oldPanel:toHide,newHeader:collapsing?$():clicked,newPanel:toShow};event.preventDefault();if( // click on active header, but not collapsible
	clickedIsActive&&!options.collapsible|| // allow canceling activation
	this._trigger("beforeActivate",event,eventData)===false){return;}options.active=collapsing?false:this.headers.index(clicked); // when the call to ._toggle() comes after the class changes
	// it causes a very odd bug in IE 8 (see #6720)
	this.active=clickedIsActive?$():clicked;this._toggle(eventData); // switch classes
	// corner classes on the previously active header stay after the animation
	active.removeClass("ui-accordion-header-active ui-state-active");if(options.icons){active.children(".ui-accordion-header-icon").removeClass(options.icons.activeHeader).addClass(options.icons.header);}if(!clickedIsActive){clicked.removeClass("ui-corner-all").addClass("ui-accordion-header-active ui-state-active ui-corner-top");if(options.icons){clicked.children(".ui-accordion-header-icon").removeClass(options.icons.header).addClass(options.icons.activeHeader);}clicked.next().addClass("ui-accordion-content-active");}},_toggle:function _toggle(data){var toShow=data.newPanel,toHide=this.prevShow.length?this.prevShow:data.oldPanel; // handle activating a panel during the animation for another activation
	this.prevShow.add(this.prevHide).stop(true,true);this.prevShow=toShow;this.prevHide=toHide;if(this.options.animate){this._animate(toShow,toHide,data);}else {toHide.hide();toShow.show();this._toggleComplete(data);}toHide.attr({"aria-expanded":"false","aria-hidden":"true"});toHide.prev().attr("aria-selected","false"); // if we're switching panels, remove the old header from the tab order
	// if we're opening from collapsed state, remove the previous header from the tab order
	// if we're collapsing, then keep the collapsing header in the tab order
	if(toShow.length&&toHide.length){toHide.prev().attr("tabIndex",-1);}else if(toShow.length){this.headers.filter(function(){return $(this).attr("tabIndex")===0;}).attr("tabIndex",-1);}toShow.attr({"aria-expanded":"true","aria-hidden":"false"}).prev().attr({"aria-selected":"true",tabIndex:0});},_animate:function _animate(toShow,toHide,data){var total,easing,duration,that=this,adjust=0,down=toShow.length&&(!toHide.length||toShow.index()<toHide.index()),animate=this.options.animate||{},options=down&&animate.down||animate,complete=function complete(){that._toggleComplete(data);};if(typeof options==="number"){duration=options;}if(typeof options==="string"){easing=options;} // fall back from options to animation in case of partial down settings
	easing=easing||options.easing||animate.easing;duration=duration||options.duration||animate.duration;if(!toHide.length){return toShow.animate(showProps,duration,easing,complete);}if(!toShow.length){return toHide.animate(hideProps,duration,easing,complete);}total=toShow.show().outerHeight();toHide.animate(hideProps,{duration:duration,easing:easing,step:function step(now,fx){fx.now=Math.round(now);}});toShow.hide().animate(showProps,{duration:duration,easing:easing,complete:complete,step:function step(now,fx){fx.now=Math.round(now);if(fx.prop!=="height"){adjust+=fx.now;}else if(that.options.heightStyle!=="content"){fx.now=Math.round(total-toHide.outerHeight()-adjust);adjust=0;}}});},_toggleComplete:function _toggleComplete(data){var toHide=data.oldPanel;toHide.removeClass("ui-accordion-content-active").prev().removeClass("ui-corner-top").addClass("ui-corner-all"); // Work around for rendering bug in IE (#5421)
	if(toHide.length){toHide.parent()[0].className=toHide.parent()[0].className;}this._trigger("activate",null,data);}});})(jQuery);(function($,undefined){ // used to prevent race conditions with remote data sources
	var requestIndex=0;$.widget("ui.autocomplete",{version:"1.10.3",defaultElement:"<input>",options:{appendTo:null,autoFocus:false,delay:300,minLength:1,position:{my:"left top",at:"left bottom",collision:"none"},source:null, // callbacks
	change:null,close:null,focus:null,open:null,response:null,search:null,select:null},pending:0,_create:function _create(){ // Some browsers only repeat keydown events, not keypress events,
	// so we use the suppressKeyPress flag to determine if we've already
	// handled the keydown event. #7269
	// Unfortunately the code for & in keypress is the same as the up arrow,
	// so we use the suppressKeyPressRepeat flag to avoid handling keypress
	// events when we know the keydown event was used to modify the
	// search term. #7799
	var suppressKeyPress,suppressKeyPressRepeat,suppressInput,nodeName=this.element[0].nodeName.toLowerCase(),isTextarea=nodeName==="textarea",isInput=nodeName==="input";this.isMultiLine= // Textareas are always multi-line
	isTextarea?true: // Inputs are always single-line, even if inside a contentEditable element
	// IE also treats inputs as contentEditable
	isInput?false: // All other element types are determined by whether or not they're contentEditable
	this.element.prop("isContentEditable");this.valueMethod=this.element[isTextarea||isInput?"val":"text"];this.isNewMenu=true;this.element.addClass("ui-autocomplete-input").attr("autocomplete","off");this._on(this.element,{keydown:function keydown(event){ /*jshint maxcomplexity:15*/if(this.element.prop("readOnly")){suppressKeyPress=true;suppressInput=true;suppressKeyPressRepeat=true;return;}suppressKeyPress=false;suppressInput=false;suppressKeyPressRepeat=false;var keyCode=$.ui.keyCode;switch(event.keyCode){case keyCode.PAGE_UP:suppressKeyPress=true;this._move("previousPage",event);break;case keyCode.PAGE_DOWN:suppressKeyPress=true;this._move("nextPage",event);break;case keyCode.UP:suppressKeyPress=true;this._keyEvent("previous",event);break;case keyCode.DOWN:suppressKeyPress=true;this._keyEvent("next",event);break;case keyCode.ENTER:case keyCode.NUMPAD_ENTER: // when menu is open and has focus
	if(this.menu.active){ // #6055 - Opera still allows the keypress to occur
	// which causes forms to submit
	suppressKeyPress=true;event.preventDefault();this.menu.select(event);}break;case keyCode.TAB:if(this.menu.active){this.menu.select(event);}break;case keyCode.ESCAPE:if(this.menu.element.is(":visible")){this._value(this.term);this.close(event); // Different browsers have different default behavior for escape
	// Single press can mean undo or clear
	// Double press in IE means clear the whole form
	event.preventDefault();}break;default:suppressKeyPressRepeat=true; // search timeout should be triggered before the input value is changed
	this._searchTimeout(event);break;}},keypress:function keypress(event){if(suppressKeyPress){suppressKeyPress=false;if(!this.isMultiLine||this.menu.element.is(":visible")){event.preventDefault();}return;}if(suppressKeyPressRepeat){return;} // replicate some key handlers to allow them to repeat in Firefox and Opera
	var keyCode=$.ui.keyCode;switch(event.keyCode){case keyCode.PAGE_UP:this._move("previousPage",event);break;case keyCode.PAGE_DOWN:this._move("nextPage",event);break;case keyCode.UP:this._keyEvent("previous",event);break;case keyCode.DOWN:this._keyEvent("next",event);break;}},input:function input(event){if(suppressInput){suppressInput=false;event.preventDefault();return;}this._searchTimeout(event);},focus:function focus(){this.selectedItem=null;this.previous=this._value();},blur:function blur(event){if(this.cancelBlur){delete this.cancelBlur;return;}clearTimeout(this.searching);this.close(event);this._change(event);}});this._initSource();this.menu=$("<ul>").addClass("ui-autocomplete ui-front").appendTo(this._appendTo()).menu({ // disable ARIA support, the live region takes care of that
	role:null}).hide().data("ui-menu");this._on(this.menu.element,{mousedown:function mousedown(event){ // prevent moving focus out of the text field
	event.preventDefault(); // IE doesn't prevent moving focus even with event.preventDefault()
	// so we set a flag to know when we should ignore the blur event
	this.cancelBlur=true;this._delay(function(){delete this.cancelBlur;}); // clicking on the scrollbar causes focus to shift to the body
	// but we can't detect a mouseup or a click immediately afterward
	// so we have to track the next mousedown and close the menu if
	// the user clicks somewhere outside of the autocomplete
	var menuElement=this.menu.element[0];if(!$(event.target).closest(".ui-menu-item").length){this._delay(function(){var that=this;this.document.one("mousedown",function(event){if(event.target!==that.element[0]&&event.target!==menuElement&&!$.contains(menuElement,event.target)){that.close();}});});}},menufocus:function menufocus(event,ui){ // support: Firefox
	// Prevent accidental activation of menu items in Firefox (#7024 #9118)
	if(this.isNewMenu){this.isNewMenu=false;if(event.originalEvent&&/^mouse/.test(event.originalEvent.type)){this.menu.blur();this.document.one("mousemove",function(){$(event.target).trigger(event.originalEvent);});return;}}var item=ui.item.data("ui-autocomplete-item");if(false!==this._trigger("focus",event,{item:item})){ // use value to match what will end up in the input, if it was a key event
	if(event.originalEvent&&/^key/.test(event.originalEvent.type)){this._value(item.value);}}else { // Normally the input is populated with the item's value as the
	// menu is navigated, causing screen readers to notice a change and
	// announce the item. Since the focus event was canceled, this doesn't
	// happen, so we update the live region so that screen readers can
	// still notice the change and announce it.
	this.liveRegion.text(item.value);}},menuselect:function menuselect(event,ui){var item=ui.item.data("ui-autocomplete-item"),previous=this.previous; // only trigger when focus was lost (click on menu)
	if(this.element[0]!==this.document[0].activeElement){this.element.focus();this.previous=previous; // #6109 - IE triggers two focus events and the second
	// is asynchronous, so we need to reset the previous
	// term synchronously and asynchronously :-(
	this._delay(function(){this.previous=previous;this.selectedItem=item;});}if(false!==this._trigger("select",event,{item:item})){this._value(item.value);} // reset the term after the select event
	// this allows custom select handling to work properly
	this.term=this._value();this.close(event);this.selectedItem=item;}});this.liveRegion=$("<span>",{role:"status","aria-live":"polite"}).addClass("ui-helper-hidden-accessible").insertBefore(this.element); // turning off autocomplete prevents the browser from remembering the
	// value when navigating through history, so we re-enable autocomplete
	// if the page is unloaded before the widget is destroyed. #7790
	this._on(this.window,{beforeunload:function beforeunload(){this.element.removeAttr("autocomplete");}});},_destroy:function _destroy(){clearTimeout(this.searching);this.element.removeClass("ui-autocomplete-input").removeAttr("autocomplete");this.menu.element.remove();this.liveRegion.remove();},_setOption:function _setOption(key,value){this._super(key,value);if(key==="source"){this._initSource();}if(key==="appendTo"){this.menu.element.appendTo(this._appendTo());}if(key==="disabled"&&value&&this.xhr){this.xhr.abort();}},_appendTo:function _appendTo(){var element=this.options.appendTo;if(element){element=element.jquery||element.nodeType?$(element):this.document.find(element).eq(0);}if(!element){element=this.element.closest(".ui-front");}if(!element.length){element=this.document[0].body;}return element;},_initSource:function _initSource(){var array,url,that=this;if($.isArray(this.options.source)){array=this.options.source;this.source=function(request,response){response($.ui.autocomplete.filter(array,request.term));};}else if(typeof this.options.source==="string"){url=this.options.source;this.source=function(request,response){if(that.xhr){that.xhr.abort();}that.xhr=$.ajax({url:url,data:request,dataType:"json",success:function success(data){response(data);},error:function error(){response([]);}});};}else {this.source=this.options.source;}},_searchTimeout:function _searchTimeout(event){clearTimeout(this.searching);this.searching=this._delay(function(){ // only search if the value has changed
	if(this.term!==this._value()){this.selectedItem=null;this.search(null,event);}},this.options.delay);},search:function search(value,event){value=value!=null?value:this._value(); // always save the actual value, not the one passed as an argument
	this.term=this._value();if(value.length<this.options.minLength){return this.close(event);}if(this._trigger("search",event)===false){return;}return this._search(value);},_search:function _search(value){this.pending++;this.element.addClass("ui-autocomplete-loading");this.cancelSearch=false;this.source({term:value},this._response());},_response:function _response(){var that=this,index=++requestIndex;return function(content){if(index===requestIndex){that.__response(content);}that.pending--;if(!that.pending){that.element.removeClass("ui-autocomplete-loading");}};},__response:function __response(content){if(content){content=this._normalize(content);}this._trigger("response",null,{content:content});if(!this.options.disabled&&content&&content.length&&!this.cancelSearch){this._suggest(content);this._trigger("open");}else { // use ._close() instead of .close() so we don't cancel future searches
	this._close();}},close:function close(event){this.cancelSearch=true;this._close(event);},_close:function _close(event){if(this.menu.element.is(":visible")){this.menu.element.hide();this.menu.blur();this.isNewMenu=true;this._trigger("close",event);}},_change:function _change(event){if(this.previous!==this._value()){this._trigger("change",event,{item:this.selectedItem});}},_normalize:function _normalize(items){ // assume all items have the right format when the first item is complete
	if(items.length&&items[0].label&&items[0].value){return items;}return $.map(items,function(item){if(typeof item==="string"){return {label:item,value:item};}return $.extend({label:item.label||item.value,value:item.value||item.label},item);});},_suggest:function _suggest(items){var ul=this.menu.element.empty();this._renderMenu(ul,items);this.isNewMenu=true;this.menu.refresh(); // size and position menu
	ul.show();this._resizeMenu();ul.position($.extend({of:this.element},this.options.position));if(this.options.autoFocus){this.menu.next();}},_resizeMenu:function _resizeMenu(){var ul=this.menu.element;ul.outerWidth(Math.max( // Firefox wraps long text (possibly a rounding bug)
	// so we add 1px to avoid the wrapping (#7513)
	ul.width("").outerWidth()+1,this.element.outerWidth()));},_renderMenu:function _renderMenu(ul,items){var that=this;$.each(items,function(index,item){that._renderItemData(ul,item);});},_renderItemData:function _renderItemData(ul,item){return this._renderItem(ul,item).data("ui-autocomplete-item",item);},_renderItem:function _renderItem(ul,item){return $("<li>").append($("<a>").text(item.label)).appendTo(ul);},_move:function _move(direction,event){if(!this.menu.element.is(":visible")){this.search(null,event);return;}if(this.menu.isFirstItem()&&/^previous/.test(direction)||this.menu.isLastItem()&&/^next/.test(direction)){this._value(this.term);this.menu.blur();return;}this.menu[direction](event);},widget:function widget(){return this.menu.element;},_value:function _value(){return this.valueMethod.apply(this.element,arguments);},_keyEvent:function _keyEvent(keyEvent,event){if(!this.isMultiLine||this.menu.element.is(":visible")){this._move(keyEvent,event); // prevents moving cursor to beginning/end of the text field in some browsers
	event.preventDefault();}}});$.extend($.ui.autocomplete,{escapeRegex:function escapeRegex(value){return value.replace(/[\-\[\]{}()*+?.,\\\^$|#\s]/g,"\\$&");},filter:function filter(array,term){var matcher=new RegExp($.ui.autocomplete.escapeRegex(term),"i");return $.grep(array,function(value){return matcher.test(value.label||value.value||value);});}}); // live region extension, adding a `messages` option
	// NOTE: This is an experimental API. We are still investigating
	// a full solution for string manipulation and internationalization.
	$.widget("ui.autocomplete",$.ui.autocomplete,{options:{messages:{noResults:"No search results.",results:function results(amount){return amount+(amount>1?" results are":" result is")+" available, use up and down arrow keys to navigate.";}}},__response:function __response(content){var message;this._superApply(arguments);if(this.options.disabled||this.cancelSearch){return;}if(content&&content.length){message=this.options.messages.results(content.length);}else {message=this.options.messages.noResults;}this.liveRegion.text(message);}});})(jQuery);(function($,undefined){var lastActive,startXPos,startYPos,clickDragged,baseClasses="ui-button ui-widget ui-state-default ui-corner-all",stateClasses="ui-state-hover ui-state-active ",typeClasses="ui-button-icons-only ui-button-icon-only ui-button-text-icons ui-button-text-icon-primary ui-button-text-icon-secondary ui-button-text-only",formResetHandler=function formResetHandler(){var form=$(this);setTimeout(function(){form.find(":ui-button").button("refresh");},1);},radioGroup=function radioGroup(radio){var name=radio.name,form=radio.form,radios=$([]);if(name){name=name.replace(/'/g,"\\'");if(form){radios=$(form).find("[name='"+name+"']");}else {radios=$("[name='"+name+"']",radio.ownerDocument).filter(function(){return !this.form;});}}return radios;};$.widget("ui.button",{version:"1.10.3",defaultElement:"<button>",options:{disabled:null,text:true,label:null,icons:{primary:null,secondary:null}},_create:function _create(){this.element.closest("form").unbind("reset"+this.eventNamespace).bind("reset"+this.eventNamespace,formResetHandler);if(typeof this.options.disabled!=="boolean"){this.options.disabled=!!this.element.prop("disabled");}else {this.element.prop("disabled",this.options.disabled);}this._determineButtonType();this.hasTitle=!!this.buttonElement.attr("title");var that=this,options=this.options,toggleButton=this.type==="checkbox"||this.type==="radio",activeClass=!toggleButton?"ui-state-active":"",focusClass="ui-state-focus";if(options.label===null){options.label=this.type==="input"?this.buttonElement.val():this.buttonElement.html();}this._hoverable(this.buttonElement);this.buttonElement.addClass(baseClasses).attr("role","button").bind("mouseenter"+this.eventNamespace,function(){if(options.disabled){return;}if(this===lastActive){$(this).addClass("ui-state-active");}}).bind("mouseleave"+this.eventNamespace,function(){if(options.disabled){return;}$(this).removeClass(activeClass);}).bind("click"+this.eventNamespace,function(event){if(options.disabled){event.preventDefault();event.stopImmediatePropagation();}});this.element.bind("focus"+this.eventNamespace,function(){ // no need to check disabled, focus won't be triggered anyway
	that.buttonElement.addClass(focusClass);}).bind("blur"+this.eventNamespace,function(){that.buttonElement.removeClass(focusClass);});if(toggleButton){this.element.bind("change"+this.eventNamespace,function(){if(clickDragged){return;}that.refresh();}); // if mouse moves between mousedown and mouseup (drag) set clickDragged flag
	// prevents issue where button state changes but checkbox/radio checked state
	// does not in Firefox (see ticket #6970)
	this.buttonElement.bind("mousedown"+this.eventNamespace,function(event){if(options.disabled){return;}clickDragged=false;startXPos=event.pageX;startYPos=event.pageY;}).bind("mouseup"+this.eventNamespace,function(event){if(options.disabled){return;}if(startXPos!==event.pageX||startYPos!==event.pageY){clickDragged=true;}});}if(this.type==="checkbox"){this.buttonElement.bind("click"+this.eventNamespace,function(){if(options.disabled||clickDragged){return false;}});}else if(this.type==="radio"){this.buttonElement.bind("click"+this.eventNamespace,function(){if(options.disabled||clickDragged){return false;}$(this).addClass("ui-state-active");that.buttonElement.attr("aria-pressed","true");var radio=that.element[0];radioGroup(radio).not(radio).map(function(){return $(this).button("widget")[0];}).removeClass("ui-state-active").attr("aria-pressed","false");});}else {this.buttonElement.bind("mousedown"+this.eventNamespace,function(){if(options.disabled){return false;}$(this).addClass("ui-state-active");lastActive=this;that.document.one("mouseup",function(){lastActive=null;});}).bind("mouseup"+this.eventNamespace,function(){if(options.disabled){return false;}$(this).removeClass("ui-state-active");}).bind("keydown"+this.eventNamespace,function(event){if(options.disabled){return false;}if(event.keyCode===$.ui.keyCode.SPACE||event.keyCode===$.ui.keyCode.ENTER){$(this).addClass("ui-state-active");}}) // see #8559, we bind to blur here in case the button element loses
	// focus between keydown and keyup, it would be left in an "active" state
	.bind("keyup"+this.eventNamespace+" blur"+this.eventNamespace,function(){$(this).removeClass("ui-state-active");});if(this.buttonElement.is("a")){this.buttonElement.keyup(function(event){if(event.keyCode===$.ui.keyCode.SPACE){ // TODO pass through original event correctly (just as 2nd argument doesn't work)
	$(this).click();}});}} // TODO: pull out $.Widget's handling for the disabled option into
	// $.Widget.prototype._setOptionDisabled so it's easy to proxy and can
	// be overridden by individual plugins
	this._setOption("disabled",options.disabled);this._resetButton();},_determineButtonType:function _determineButtonType(){var ancestor,labelSelector,checked;if(this.element.is("[type=checkbox]")){this.type="checkbox";}else if(this.element.is("[type=radio]")){this.type="radio";}else if(this.element.is("input")){this.type="input";}else {this.type="button";}if(this.type==="checkbox"||this.type==="radio"){ // we don't search against the document in case the element
	// is disconnected from the DOM
	ancestor=this.element.parents().last();labelSelector="label[for='"+this.element.attr("id")+"']";this.buttonElement=ancestor.find(labelSelector);if(!this.buttonElement.length){ancestor=ancestor.length?ancestor.siblings():this.element.siblings();this.buttonElement=ancestor.filter(labelSelector);if(!this.buttonElement.length){this.buttonElement=ancestor.find(labelSelector);}}this.element.addClass("ui-helper-hidden-accessible");checked=this.element.is(":checked");if(checked){this.buttonElement.addClass("ui-state-active");}this.buttonElement.prop("aria-pressed",checked);}else {this.buttonElement=this.element;}},widget:function widget(){return this.buttonElement;},_destroy:function _destroy(){this.element.removeClass("ui-helper-hidden-accessible");this.buttonElement.removeClass(baseClasses+" "+stateClasses+" "+typeClasses).removeAttr("role").removeAttr("aria-pressed").html(this.buttonElement.find(".ui-button-text").html());if(!this.hasTitle){this.buttonElement.removeAttr("title");}},_setOption:function _setOption(key,value){this._super(key,value);if(key==="disabled"){if(value){this.element.prop("disabled",true);}else {this.element.prop("disabled",false);}return;}this._resetButton();},refresh:function refresh(){ //See #8237 & #8828
	var isDisabled=this.element.is("input, button")?this.element.is(":disabled"):this.element.hasClass("ui-button-disabled");if(isDisabled!==this.options.disabled){this._setOption("disabled",isDisabled);}if(this.type==="radio"){radioGroup(this.element[0]).each(function(){if($(this).is(":checked")){$(this).button("widget").addClass("ui-state-active").attr("aria-pressed","true");}else {$(this).button("widget").removeClass("ui-state-active").attr("aria-pressed","false");}});}else if(this.type==="checkbox"){if(this.element.is(":checked")){this.buttonElement.addClass("ui-state-active").attr("aria-pressed","true");}else {this.buttonElement.removeClass("ui-state-active").attr("aria-pressed","false");}}},_resetButton:function _resetButton(){if(this.type==="input"){if(this.options.label){this.element.val(this.options.label);}return;}var buttonElement=this.buttonElement.removeClass(typeClasses),buttonText=$("<span></span>",this.document[0]).addClass("ui-button-text").html(this.options.label).appendTo(buttonElement.empty()).text(),icons=this.options.icons,multipleIcons=icons.primary&&icons.secondary,buttonClasses=[];if(icons.primary||icons.secondary){if(this.options.text){buttonClasses.push("ui-button-text-icon"+(multipleIcons?"s":icons.primary?"-primary":"-secondary"));}if(icons.primary){buttonElement.prepend("<span class='ui-button-icon-primary ui-icon "+icons.primary+"'></span>");}if(icons.secondary){buttonElement.append("<span class='ui-button-icon-secondary ui-icon "+icons.secondary+"'></span>");}if(!this.options.text){buttonClasses.push(multipleIcons?"ui-button-icons-only":"ui-button-icon-only");if(!this.hasTitle){buttonElement.attr("title",$.trim(buttonText));}}}else {buttonClasses.push("ui-button-text-only");}buttonElement.addClass(buttonClasses.join(" "));}});$.widget("ui.buttonset",{version:"1.10.3",options:{items:"button, input[type=button], input[type=submit], input[type=reset], input[type=checkbox], input[type=radio], a, :data(ui-button)"},_create:function _create(){this.element.addClass("ui-buttonset");},_init:function _init(){this.refresh();},_setOption:function _setOption(key,value){if(key==="disabled"){this.buttons.button("option",key,value);}this._super(key,value);},refresh:function refresh(){var rtl=this.element.css("direction")==="rtl";this.buttons=this.element.find(this.options.items).filter(":ui-button").button("refresh").end().not(":ui-button").button().end().map(function(){return $(this).button("widget")[0];}).removeClass("ui-corner-all ui-corner-left ui-corner-right").filter(":first").addClass(rtl?"ui-corner-right":"ui-corner-left").end().filter(":last").addClass(rtl?"ui-corner-left":"ui-corner-right").end().end();},_destroy:function _destroy(){this.element.removeClass("ui-buttonset");this.buttons.map(function(){return $(this).button("widget")[0];}).removeClass("ui-corner-left ui-corner-right").end().button("destroy");}});})(jQuery);(function($,undefined){$.extend($.ui,{datepicker:{version:"1.10.3"}});var PROP_NAME="datepicker",instActive; /* Date picker manager.
	   Use the singleton instance of this class, $.datepicker, to interact with the date picker.
	   Settings for (groups of) date pickers are maintained in an instance object,
	   allowing multiple different settings on the same page. */function Datepicker(){this._curInst=null; // The current instance in use
	this._keyEvent=false; // If the last event was a key event
	this._disabledInputs=[]; // List of date picker inputs that have been disabled
	this._datepickerShowing=false; // True if the popup picker is showing , false if not
	this._inDialog=false; // True if showing within a "dialog", false if not
	this._mainDivId="ui-datepicker-div"; // The ID of the main datepicker division
	this._inlineClass="ui-datepicker-inline"; // The name of the inline marker class
	this._appendClass="ui-datepicker-append"; // The name of the append marker class
	this._triggerClass="ui-datepicker-trigger"; // The name of the trigger marker class
	this._dialogClass="ui-datepicker-dialog"; // The name of the dialog marker class
	this._disableClass="ui-datepicker-disabled"; // The name of the disabled covering marker class
	this._unselectableClass="ui-datepicker-unselectable"; // The name of the unselectable cell marker class
	this._currentClass="ui-datepicker-current-day"; // The name of the current day marker class
	this._dayOverClass="ui-datepicker-days-cell-over"; // The name of the day hover marker class
	this.regional=[]; // Available regional settings, indexed by language code
	this.regional[""]={ // Default regional settings
	closeText:"Done", // Display text for close link
	prevText:"Prev", // Display text for previous month link
	nextText:"Next", // Display text for next month link
	currentText:"Today", // Display text for current month link
	monthNames:["January","February","March","April","May","June","July","August","September","October","November","December"], // Names of months for drop-down and formatting
	monthNamesShort:["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"], // For formatting
	dayNames:["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"], // For formatting
	dayNamesShort:["Sun","Mon","Tue","Wed","Thu","Fri","Sat"], // For formatting
	dayNamesMin:["Su","Mo","Tu","We","Th","Fr","Sa"], // Column headings for days starting at Sunday
	weekHeader:"Wk", // Column header for week of the year
	dateFormat:"mm/dd/yy", // See format options on parseDate
	firstDay:0, // The first day of the week, Sun = 0, Mon = 1, ...
	isRTL:false, // True if right-to-left language, false if left-to-right
	showMonthAfterYear:false, // True if the year select precedes month, false for month then year
	yearSuffix:"" // Additional text to append to the year in the month headers
	};this._defaults={ // Global defaults for all the date picker instances
	showOn:"focus", // "focus" for popup on focus,
	// "button" for trigger button, or "both" for either
	showAnim:"fadeIn", // Name of jQuery animation for popup
	showOptions:{}, // Options for enhanced animations
	defaultDate:null, // Used when field is blank: actual date,
	// +/-number for offset from today, null for today
	appendText:"", // Display text following the input box, e.g. showing the format
	buttonText:"...", // Text for trigger button
	buttonImage:"", // URL for trigger button image
	buttonImageOnly:false, // True if the image appears alone, false if it appears on a button
	hideIfNoPrevNext:false, // True to hide next/previous month links
	// if not applicable, false to just disable them
	navigationAsDateFormat:false, // True if date formatting applied to prev/today/next links
	gotoCurrent:false, // True if today link goes back to current selection instead
	changeMonth:false, // True if month can be selected directly, false if only prev/next
	changeYear:false, // True if year can be selected directly, false if only prev/next
	yearRange:"c-10:c+10", // Range of years to display in drop-down,
	// either relative to today's year (-nn:+nn), relative to currently displayed year
	// (c-nn:c+nn), absolute (nnnn:nnnn), or a combination of the above (nnnn:-n)
	showOtherMonths:false, // True to show dates in other months, false to leave blank
	selectOtherMonths:false, // True to allow selection of dates in other months, false for unselectable
	showWeek:false, // True to show week of the year, false to not show it
	calculateWeek:this.iso8601Week, // How to calculate the week of the year,
	// takes a Date and returns the number of the week for it
	shortYearCutoff:"+10", // Short year values < this are in the current century,
	// > this are in the previous century,
	// string value starting with "+" for current year + value
	minDate:null, // The earliest selectable date, or null for no limit
	maxDate:null, // The latest selectable date, or null for no limit
	duration:"fast", // Duration of display/closure
	beforeShowDay:null, // Function that takes a date and returns an array with
	// [0] = true if selectable, false if not, [1] = custom CSS class name(s) or "",
	// [2] = cell title (optional), e.g. $.datepicker.noWeekends
	beforeShow:null, // Function that takes an input field and
	// returns a set of custom settings for the date picker
	onSelect:null, // Define a callback function when a date is selected
	onChangeMonthYear:null, // Define a callback function when the month or year is changed
	onClose:null, // Define a callback function when the datepicker is closed
	numberOfMonths:1, // Number of months to show at a time
	showCurrentAtPos:0, // The position in multipe months at which to show the current month (starting at 0)
	stepMonths:1, // Number of months to step back/forward
	stepBigMonths:12, // Number of months to step back/forward for the big links
	altField:"", // Selector for an alternate field to store selected dates into
	altFormat:"", // The date format to use for the alternate field
	constrainInput:true, // The input is constrained by the current date format
	showButtonPanel:false, // True to show button panel, false to not show it
	autoSize:false, // True to size the input for the date format, false to leave as is
	disabled:false // The initial disabled state
	};$.extend(this._defaults,this.regional[""]);this.dpDiv=bindHover($("<div id='"+this._mainDivId+"' class='ui-datepicker ui-widget ui-widget-content ui-helper-clearfix ui-corner-all'></div>"));}$.extend(Datepicker.prototype,{ /* Class name added to elements to indicate already configured with a date picker. */markerClassName:"hasDatepicker", //Keep track of the maximum number of rows displayed (see #7043)
	maxRows:4, // TODO rename to "widget" when switching to widget factory
	_widgetDatepicker:function _widgetDatepicker(){return this.dpDiv;}, /* Override the default settings for all instances of the date picker.
		 * @param  settings  object - the new settings to use as defaults (anonymous object)
		 * @return the manager object
		 */setDefaults:function setDefaults(settings){extendRemove(this._defaults,settings||{});return this;}, /* Attach the date picker to a jQuery selection.
		 * @param  target	element - the target input field or division or span
		 * @param  settings  object - the new settings to use for this date picker instance (anonymous)
		 */_attachDatepicker:function _attachDatepicker(target,settings){var nodeName,inline,inst;nodeName=target.nodeName.toLowerCase();inline=nodeName==="div"||nodeName==="span";if(!target.id){this.uuid+=1;target.id="dp"+this.uuid;}inst=this._newInst($(target),inline);inst.settings=$.extend({},settings||{});if(nodeName==="input"){this._connectDatepicker(target,inst);}else if(inline){this._inlineDatepicker(target,inst);}}, /* Create a new instance object. */_newInst:function _newInst(target,inline){var id=target[0].id.replace(/([^A-Za-z0-9_\-])/g,"\\\\$1"); // escape jQuery meta chars
	return {id:id,input:target, // associated target
	selectedDay:0,selectedMonth:0,selectedYear:0, // current selection
	drawMonth:0,drawYear:0, // month being drawn
	inline:inline, // is datepicker inline or not
	dpDiv:!inline?this.dpDiv: // presentation div
	bindHover($("<div class='"+this._inlineClass+" ui-datepicker ui-widget ui-widget-content ui-helper-clearfix ui-corner-all'></div>"))};}, /* Attach the date picker to an input field. */_connectDatepicker:function _connectDatepicker(target,inst){var input=$(target);inst.append=$([]);inst.trigger=$([]);if(input.hasClass(this.markerClassName)){return;}this._attachments(input,inst);input.addClass(this.markerClassName).keydown(this._doKeyDown).keypress(this._doKeyPress).keyup(this._doKeyUp);this._autoSize(inst);$.data(target,PROP_NAME,inst); //If disabled option is true, disable the datepicker once it has been attached to the input (see ticket #5665)
	if(inst.settings.disabled){this._disableDatepicker(target);}}, /* Make attachments based on settings. */_attachments:function _attachments(input,inst){var showOn,buttonText,buttonImage,appendText=this._get(inst,"appendText"),isRTL=this._get(inst,"isRTL");if(inst.append){inst.append.remove();}if(appendText){inst.append=$("<span class='"+this._appendClass+"'>"+appendText+"</span>");input[isRTL?"before":"after"](inst.append);}input.unbind("focus",this._showDatepicker);if(inst.trigger){inst.trigger.remove();}showOn=this._get(inst,"showOn");if(showOn==="focus"||showOn==="both"){ // pop-up date picker when in the marked field
	input.focus(this._showDatepicker);}if(showOn==="button"||showOn==="both"){ // pop-up date picker when button clicked
	buttonText=this._get(inst,"buttonText");buttonImage=this._get(inst,"buttonImage");inst.trigger=$(this._get(inst,"buttonImageOnly")?$("<img/>").addClass(this._triggerClass).attr({src:buttonImage,alt:buttonText,title:buttonText}):$("<button type='button'></button>").addClass(this._triggerClass).html(!buttonImage?buttonText:$("<img/>").attr({src:buttonImage,alt:buttonText,title:buttonText})));input[isRTL?"before":"after"](inst.trigger);inst.trigger.click(function(){if($.datepicker._datepickerShowing&&$.datepicker._lastInput===input[0]){$.datepicker._hideDatepicker();}else if($.datepicker._datepickerShowing&&$.datepicker._lastInput!==input[0]){$.datepicker._hideDatepicker();$.datepicker._showDatepicker(input[0]);}else {$.datepicker._showDatepicker(input[0]);}return false;});}}, /* Apply the maximum length for the date format. */_autoSize:function _autoSize(inst){if(this._get(inst,"autoSize")&&!inst.inline){var findMax,max,maxI,i,date=new Date(2009,12-1,20), // Ensure double digits
	dateFormat=this._get(inst,"dateFormat");if(dateFormat.match(/[DM]/)){findMax=function findMax(names){max=0;maxI=0;for(i=0;i<names.length;i++){if(names[i].length>max){max=names[i].length;maxI=i;}}return maxI;};date.setMonth(findMax(this._get(inst,dateFormat.match(/MM/)?"monthNames":"monthNamesShort")));date.setDate(findMax(this._get(inst,dateFormat.match(/DD/)?"dayNames":"dayNamesShort"))+20-date.getDay());}inst.input.attr("size",this._formatDate(inst,date).length);}}, /* Attach an inline date picker to a div. */_inlineDatepicker:function _inlineDatepicker(target,inst){var divSpan=$(target);if(divSpan.hasClass(this.markerClassName)){return;}divSpan.addClass(this.markerClassName).append(inst.dpDiv);$.data(target,PROP_NAME,inst);this._setDate(inst,this._getDefaultDate(inst),true);this._updateDatepicker(inst);this._updateAlternate(inst); //If disabled option is true, disable the datepicker before showing it (see ticket #5665)
	if(inst.settings.disabled){this._disableDatepicker(target);} // Set display:block in place of inst.dpDiv.show() which won't work on disconnected elements
	// http://bugs.jqueryui.com/ticket/7552 - A Datepicker created on a detached div has zero height
	inst.dpDiv.css("display","block");}, /* Pop-up the date picker in a "dialog" box.
		 * @param  input element - ignored
		 * @param  date	string or Date - the initial date to display
		 * @param  onSelect  function - the function to call when a date is selected
		 * @param  settings  object - update the dialog date picker instance's settings (anonymous object)
		 * @param  pos int[2] - coordinates for the dialog's position within the screen or
		 *					event - with x/y coordinates or
		 *					leave empty for default (screen centre)
		 * @return the manager object
		 */_dialogDatepicker:function _dialogDatepicker(input,date,onSelect,settings,pos){var id,browserWidth,browserHeight,scrollX,scrollY,inst=this._dialogInst; // internal instance
	if(!inst){this.uuid+=1;id="dp"+this.uuid;this._dialogInput=$("<input type='text' id='"+id+"' style='position: absolute; top: -100px; width: 0px;'/>");this._dialogInput.keydown(this._doKeyDown);$("body").append(this._dialogInput);inst=this._dialogInst=this._newInst(this._dialogInput,false);inst.settings={};$.data(this._dialogInput[0],PROP_NAME,inst);}extendRemove(inst.settings,settings||{});date=date&&date.constructor===Date?this._formatDate(inst,date):date;this._dialogInput.val(date);this._pos=pos?pos.length?pos:[pos.pageX,pos.pageY]:null;if(!this._pos){browserWidth=document.documentElement.clientWidth;browserHeight=document.documentElement.clientHeight;scrollX=document.documentElement.scrollLeft||document.body.scrollLeft;scrollY=document.documentElement.scrollTop||document.body.scrollTop;this._pos= // should use actual width/height below
	[browserWidth/2-100+scrollX,browserHeight/2-150+scrollY];} // move input on screen for focus, but hidden behind dialog
	this._dialogInput.css("left",this._pos[0]+20+"px").css("top",this._pos[1]+"px");inst.settings.onSelect=onSelect;this._inDialog=true;this.dpDiv.addClass(this._dialogClass);this._showDatepicker(this._dialogInput[0]);if($.blockUI){$.blockUI(this.dpDiv);}$.data(this._dialogInput[0],PROP_NAME,inst);return this;}, /* Detach a datepicker from its control.
		 * @param  target	element - the target input field or division or span
		 */_destroyDatepicker:function _destroyDatepicker(target){var nodeName,$target=$(target),inst=$.data(target,PROP_NAME);if(!$target.hasClass(this.markerClassName)){return;}nodeName=target.nodeName.toLowerCase();$.removeData(target,PROP_NAME);if(nodeName==="input"){inst.append.remove();inst.trigger.remove();$target.removeClass(this.markerClassName).unbind("focus",this._showDatepicker).unbind("keydown",this._doKeyDown).unbind("keypress",this._doKeyPress).unbind("keyup",this._doKeyUp);}else if(nodeName==="div"||nodeName==="span"){$target.removeClass(this.markerClassName).empty();}}, /* Enable the date picker to a jQuery selection.
		 * @param  target	element - the target input field or division or span
		 */_enableDatepicker:function _enableDatepicker(target){var nodeName,inline,$target=$(target),inst=$.data(target,PROP_NAME);if(!$target.hasClass(this.markerClassName)){return;}nodeName=target.nodeName.toLowerCase();if(nodeName==="input"){target.disabled=false;inst.trigger.filter("button").each(function(){this.disabled=false;}).end().filter("img").css({opacity:"1.0",cursor:""});}else if(nodeName==="div"||nodeName==="span"){inline=$target.children("."+this._inlineClass);inline.children().removeClass("ui-state-disabled");inline.find("select.ui-datepicker-month, select.ui-datepicker-year").prop("disabled",false);}this._disabledInputs=$.map(this._disabledInputs,function(value){return value===target?null:value;}); // delete entry
	}, /* Disable the date picker to a jQuery selection.
		 * @param  target	element - the target input field or division or span
		 */_disableDatepicker:function _disableDatepicker(target){var nodeName,inline,$target=$(target),inst=$.data(target,PROP_NAME);if(!$target.hasClass(this.markerClassName)){return;}nodeName=target.nodeName.toLowerCase();if(nodeName==="input"){target.disabled=true;inst.trigger.filter("button").each(function(){this.disabled=true;}).end().filter("img").css({opacity:"0.5",cursor:"default"});}else if(nodeName==="div"||nodeName==="span"){inline=$target.children("."+this._inlineClass);inline.children().addClass("ui-state-disabled");inline.find("select.ui-datepicker-month, select.ui-datepicker-year").prop("disabled",true);}this._disabledInputs=$.map(this._disabledInputs,function(value){return value===target?null:value;}); // delete entry
	this._disabledInputs[this._disabledInputs.length]=target;}, /* Is the first field in a jQuery collection disabled as a datepicker?
		 * @param  target	element - the target input field or division or span
		 * @return boolean - true if disabled, false if enabled
		 */_isDisabledDatepicker:function _isDisabledDatepicker(target){if(!target){return false;}for(var i=0;i<this._disabledInputs.length;i++){if(this._disabledInputs[i]===target){return true;}}return false;}, /* Retrieve the instance data for the target control.
		 * @param  target  element - the target input field or division or span
		 * @return  object - the associated instance data
		 * @throws  error if a jQuery problem getting data
		 */_getInst:function _getInst(target){try{return $.data(target,PROP_NAME);}catch(err){throw "Missing instance data for this datepicker";}}, /* Update or retrieve the settings for a date picker attached to an input field or division.
		 * @param  target  element - the target input field or division or span
		 * @param  name	object - the new settings to update or
		 *				string - the name of the setting to change or retrieve,
		 *				when retrieving also "all" for all instance settings or
		 *				"defaults" for all global defaults
		 * @param  value   any - the new value for the setting
		 *				(omit if above is an object or to retrieve a value)
		 */_optionDatepicker:function _optionDatepicker(target,name,value){var settings,date,minDate,maxDate,inst=this._getInst(target);if(arguments.length===2&&typeof name==="string"){return name==="defaults"?$.extend({},$.datepicker._defaults):inst?name==="all"?$.extend({},inst.settings):this._get(inst,name):null;}settings=name||{};if(typeof name==="string"){settings={};settings[name]=value;}if(inst){if(this._curInst===inst){this._hideDatepicker();}date=this._getDateDatepicker(target,true);minDate=this._getMinMaxDate(inst,"min");maxDate=this._getMinMaxDate(inst,"max");extendRemove(inst.settings,settings); // reformat the old minDate/maxDate values if dateFormat changes and a new minDate/maxDate isn't provided
	if(minDate!==null&&settings.dateFormat!==undefined&&settings.minDate===undefined){inst.settings.minDate=this._formatDate(inst,minDate);}if(maxDate!==null&&settings.dateFormat!==undefined&&settings.maxDate===undefined){inst.settings.maxDate=this._formatDate(inst,maxDate);}if("disabled" in settings){if(settings.disabled){this._disableDatepicker(target);}else {this._enableDatepicker(target);}}this._attachments($(target),inst);this._autoSize(inst);this._setDate(inst,date);this._updateAlternate(inst);this._updateDatepicker(inst);}}, // change method deprecated
	_changeDatepicker:function _changeDatepicker(target,name,value){this._optionDatepicker(target,name,value);}, /* Redraw the date picker attached to an input field or division.
		 * @param  target  element - the target input field or division or span
		 */_refreshDatepicker:function _refreshDatepicker(target){var inst=this._getInst(target);if(inst){this._updateDatepicker(inst);}}, /* Set the dates for a jQuery selection.
		 * @param  target element - the target input field or division or span
		 * @param  date	Date - the new date
		 */_setDateDatepicker:function _setDateDatepicker(target,date){var inst=this._getInst(target);if(inst){this._setDate(inst,date);this._updateDatepicker(inst);this._updateAlternate(inst);}}, /* Get the date(s) for the first entry in a jQuery selection.
		 * @param  target element - the target input field or division or span
		 * @param  noDefault boolean - true if no default date is to be used
		 * @return Date - the current date
		 */_getDateDatepicker:function _getDateDatepicker(target,noDefault){var inst=this._getInst(target);if(inst&&!inst.inline){this._setDateFromField(inst,noDefault);}return inst?this._getDate(inst):null;}, /* Handle keystrokes. */_doKeyDown:function _doKeyDown(event){var onSelect,dateStr,sel,inst=$.datepicker._getInst(event.target),handled=true,isRTL=inst.dpDiv.is(".ui-datepicker-rtl");inst._keyEvent=true;if($.datepicker._datepickerShowing){switch(event.keyCode){case 9:$.datepicker._hideDatepicker();handled=false;break; // hide on tab out
	case 13:sel=$("td."+$.datepicker._dayOverClass+":not(."+$.datepicker._currentClass+")",inst.dpDiv);if(sel[0]){$.datepicker._selectDay(event.target,inst.selectedMonth,inst.selectedYear,sel[0]);}onSelect=$.datepicker._get(inst,"onSelect");if(onSelect){dateStr=$.datepicker._formatDate(inst); // trigger custom callback
	onSelect.apply(inst.input?inst.input[0]:null,[dateStr,inst]);}else {$.datepicker._hideDatepicker();}return false; // don't submit the form
	case 27:$.datepicker._hideDatepicker();break; // hide on escape
	case 33:$.datepicker._adjustDate(event.target,event.ctrlKey?-$.datepicker._get(inst,"stepBigMonths"):-$.datepicker._get(inst,"stepMonths"),"M");break; // previous month/year on page up/+ ctrl
	case 34:$.datepicker._adjustDate(event.target,event.ctrlKey?+$.datepicker._get(inst,"stepBigMonths"):+$.datepicker._get(inst,"stepMonths"),"M");break; // next month/year on page down/+ ctrl
	case 35:if(event.ctrlKey||event.metaKey){$.datepicker._clearDate(event.target);}handled=event.ctrlKey||event.metaKey;break; // clear on ctrl or command +end
	case 36:if(event.ctrlKey||event.metaKey){$.datepicker._gotoToday(event.target);}handled=event.ctrlKey||event.metaKey;break; // current on ctrl or command +home
	case 37:if(event.ctrlKey||event.metaKey){$.datepicker._adjustDate(event.target,isRTL?+1:-1,"D");}handled=event.ctrlKey||event.metaKey; // -1 day on ctrl or command +left
	if(event.originalEvent.altKey){$.datepicker._adjustDate(event.target,event.ctrlKey?-$.datepicker._get(inst,"stepBigMonths"):-$.datepicker._get(inst,"stepMonths"),"M");} // next month/year on alt +left on Mac
	break;case 38:if(event.ctrlKey||event.metaKey){$.datepicker._adjustDate(event.target,-7,"D");}handled=event.ctrlKey||event.metaKey;break; // -1 week on ctrl or command +up
	case 39:if(event.ctrlKey||event.metaKey){$.datepicker._adjustDate(event.target,isRTL?-1:+1,"D");}handled=event.ctrlKey||event.metaKey; // +1 day on ctrl or command +right
	if(event.originalEvent.altKey){$.datepicker._adjustDate(event.target,event.ctrlKey?+$.datepicker._get(inst,"stepBigMonths"):+$.datepicker._get(inst,"stepMonths"),"M");} // next month/year on alt +right
	break;case 40:if(event.ctrlKey||event.metaKey){$.datepicker._adjustDate(event.target,+7,"D");}handled=event.ctrlKey||event.metaKey;break; // +1 week on ctrl or command +down
	default:handled=false;}}else if(event.keyCode===36&&event.ctrlKey){ // display the date picker on ctrl+home
	$.datepicker._showDatepicker(this);}else {handled=false;}if(handled){event.preventDefault();event.stopPropagation();}}, /* Filter entered characters - based on date format. */_doKeyPress:function _doKeyPress(event){var chars,chr,inst=$.datepicker._getInst(event.target);if($.datepicker._get(inst,"constrainInput")){chars=$.datepicker._possibleChars($.datepicker._get(inst,"dateFormat"));chr=String.fromCharCode(event.charCode==null?event.keyCode:event.charCode);return event.ctrlKey||event.metaKey||chr<" "||!chars||chars.indexOf(chr)>-1;}}, /* Synchronise manual entry and field/alternate field. */_doKeyUp:function _doKeyUp(event){var date,inst=$.datepicker._getInst(event.target);if(inst.input.val()!==inst.lastVal){try{date=$.datepicker.parseDate($.datepicker._get(inst,"dateFormat"),inst.input?inst.input.val():null,$.datepicker._getFormatConfig(inst));if(date){ // only if valid
	$.datepicker._setDateFromField(inst);$.datepicker._updateAlternate(inst);$.datepicker._updateDatepicker(inst);}}catch(err){}}return true;}, /* Pop-up the date picker for a given input field.
		 * If false returned from beforeShow event handler do not show.
		 * @param  input  element - the input field attached to the date picker or
		 *					event - if triggered by focus
		 */_showDatepicker:function _showDatepicker(input){input=input.target||input;if(input.nodeName.toLowerCase()!=="input"){ // find from button/image trigger
	input=$("input",input.parentNode)[0];}if($.datepicker._isDisabledDatepicker(input)||$.datepicker._lastInput===input){ // already here
	return;}var inst,beforeShow,beforeShowSettings,isFixed,offset,showAnim,duration;inst=$.datepicker._getInst(input);if($.datepicker._curInst&&$.datepicker._curInst!==inst){$.datepicker._curInst.dpDiv.stop(true,true);if(inst&&$.datepicker._datepickerShowing){$.datepicker._hideDatepicker($.datepicker._curInst.input[0]);}}beforeShow=$.datepicker._get(inst,"beforeShow");beforeShowSettings=beforeShow?beforeShow.apply(input,[input,inst]):{};if(beforeShowSettings===false){return;}extendRemove(inst.settings,beforeShowSettings);inst.lastVal=null;$.datepicker._lastInput=input;$.datepicker._setDateFromField(inst);if($.datepicker._inDialog){ // hide cursor
	input.value="";}if(!$.datepicker._pos){ // position below input
	$.datepicker._pos=$.datepicker._findPos(input);$.datepicker._pos[1]+=input.offsetHeight; // add the height
	}isFixed=false;$(input).parents().each(function(){isFixed|=$(this).css("position")==="fixed";return !isFixed;});offset={left:$.datepicker._pos[0],top:$.datepicker._pos[1]};$.datepicker._pos=null; //to avoid flashes on Firefox
	inst.dpDiv.empty(); // determine sizing offscreen
	inst.dpDiv.css({position:"absolute",display:"block",top:"-1000px"});$.datepicker._updateDatepicker(inst); // fix width for dynamic number of date pickers
	// and adjust position before showing
	offset=$.datepicker._checkOffset(inst,offset,isFixed);inst.dpDiv.css({position:$.datepicker._inDialog&&$.blockUI?"static":isFixed?"fixed":"absolute",display:"none",left:offset.left+"px",top:offset.top+"px"});if(!inst.inline){showAnim=$.datepicker._get(inst,"showAnim");duration=$.datepicker._get(inst,"duration");inst.dpDiv.zIndex($(input).zIndex()+1);$.datepicker._datepickerShowing=true;if($.effects&&$.effects.effect[showAnim]){inst.dpDiv.show(showAnim,$.datepicker._get(inst,"showOptions"),duration);}else {inst.dpDiv[showAnim||"show"](showAnim?duration:null);}if($.datepicker._shouldFocusInput(inst)){inst.input.focus();}$.datepicker._curInst=inst;}}, /* Generate the date picker content. */_updateDatepicker:function _updateDatepicker(inst){this.maxRows=4; //Reset the max number of rows being displayed (see #7043)
	instActive=inst; // for delegate hover events
	inst.dpDiv.empty().append(this._generateHTML(inst));this._attachHandlers(inst);inst.dpDiv.find("."+this._dayOverClass+" a").mouseover();var origyearshtml,numMonths=this._getNumberOfMonths(inst),cols=numMonths[1],width=17;inst.dpDiv.removeClass("ui-datepicker-multi-2 ui-datepicker-multi-3 ui-datepicker-multi-4").width("");if(cols>1){inst.dpDiv.addClass("ui-datepicker-multi-"+cols).css("width",width*cols+"em");}inst.dpDiv[(numMonths[0]!==1||numMonths[1]!==1?"add":"remove")+"Class"]("ui-datepicker-multi");inst.dpDiv[(this._get(inst,"isRTL")?"add":"remove")+"Class"]("ui-datepicker-rtl");if(inst===$.datepicker._curInst&&$.datepicker._datepickerShowing&&$.datepicker._shouldFocusInput(inst)){inst.input.focus();} // deffered render of the years select (to avoid flashes on Firefox)
	if(inst.yearshtml){origyearshtml=inst.yearshtml;setTimeout(function(){ //assure that inst.yearshtml didn't change.
	if(origyearshtml===inst.yearshtml&&inst.yearshtml){inst.dpDiv.find("select.ui-datepicker-year:first").replaceWith(inst.yearshtml);}origyearshtml=inst.yearshtml=null;},0);}}, // #6694 - don't focus the input if it's already focused
	// this breaks the change event in IE
	// Support: IE and jQuery <1.9
	_shouldFocusInput:function _shouldFocusInput(inst){return inst.input&&inst.input.is(":visible")&&!inst.input.is(":disabled")&&!inst.input.is(":focus");}, /* Check positioning to remain on screen. */_checkOffset:function _checkOffset(inst,offset,isFixed){var dpWidth=inst.dpDiv.outerWidth(),dpHeight=inst.dpDiv.outerHeight(),inputWidth=inst.input?inst.input.outerWidth():0,inputHeight=inst.input?inst.input.outerHeight():0,viewWidth=document.documentElement.clientWidth+(isFixed?0:$(document).scrollLeft()),viewHeight=document.documentElement.clientHeight+(isFixed?0:$(document).scrollTop());offset.left-=this._get(inst,"isRTL")?dpWidth-inputWidth:0;offset.left-=isFixed&&offset.left===inst.input.offset().left?$(document).scrollLeft():0;offset.top-=isFixed&&offset.top===inst.input.offset().top+inputHeight?$(document).scrollTop():0; // now check if datepicker is showing outside window viewport - move to a better place if so.
	offset.left-=Math.min(offset.left,offset.left+dpWidth>viewWidth&&viewWidth>dpWidth?Math.abs(offset.left+dpWidth-viewWidth):0);offset.top-=Math.min(offset.top,offset.top+dpHeight>viewHeight&&viewHeight>dpHeight?Math.abs(dpHeight+inputHeight):0);return offset;}, /* Find an object's position on the screen. */_findPos:function _findPos(obj){var position,inst=this._getInst(obj),isRTL=this._get(inst,"isRTL");while(obj&&(obj.type==="hidden"||obj.nodeType!==1||$.expr.filters.hidden(obj))){obj=obj[isRTL?"previousSibling":"nextSibling"];}position=$(obj).offset();return [position.left,position.top];}, /* Hide the date picker from view.
		 * @param  input  element - the input field attached to the date picker
		 */_hideDatepicker:function _hideDatepicker(input){var showAnim,duration,postProcess,onClose,inst=this._curInst;if(!inst||input&&inst!==$.data(input,PROP_NAME)){return;}if(this._datepickerShowing){showAnim=this._get(inst,"showAnim");duration=this._get(inst,"duration");postProcess=function postProcess(){$.datepicker._tidyDialog(inst);}; // DEPRECATED: after BC for 1.8.x $.effects[ showAnim ] is not needed
	if($.effects&&($.effects.effect[showAnim]||$.effects[showAnim])){inst.dpDiv.hide(showAnim,$.datepicker._get(inst,"showOptions"),duration,postProcess);}else {inst.dpDiv[showAnim==="slideDown"?"slideUp":showAnim==="fadeIn"?"fadeOut":"hide"](showAnim?duration:null,postProcess);}if(!showAnim){postProcess();}this._datepickerShowing=false;onClose=this._get(inst,"onClose");if(onClose){onClose.apply(inst.input?inst.input[0]:null,[inst.input?inst.input.val():"",inst]);}this._lastInput=null;if(this._inDialog){this._dialogInput.css({position:"absolute",left:"0",top:"-100px"});if($.blockUI){$.unblockUI();$("body").append(this.dpDiv);}}this._inDialog=false;}}, /* Tidy up after a dialog display. */_tidyDialog:function _tidyDialog(inst){inst.dpDiv.removeClass(this._dialogClass).unbind(".ui-datepicker-calendar");}, /* Close date picker if clicked elsewhere. */_checkExternalClick:function _checkExternalClick(event){if(!$.datepicker._curInst){return;}var $target=$(event.target),inst=$.datepicker._getInst($target[0]);if($target[0].id!==$.datepicker._mainDivId&&$target.parents("#"+$.datepicker._mainDivId).length===0&&!$target.hasClass($.datepicker.markerClassName)&&!$target.closest("."+$.datepicker._triggerClass).length&&$.datepicker._datepickerShowing&&!($.datepicker._inDialog&&$.blockUI)||$target.hasClass($.datepicker.markerClassName)&&$.datepicker._curInst!==inst){$.datepicker._hideDatepicker();}}, /* Adjust one of the date sub-fields. */_adjustDate:function _adjustDate(id,offset,period){var target=$(id),inst=this._getInst(target[0]);if(this._isDisabledDatepicker(target[0])){return;}this._adjustInstDate(inst,offset+(period==="M"?this._get(inst,"showCurrentAtPos"):0), // undo positioning
	period);this._updateDatepicker(inst);}, /* Action for current link. */_gotoToday:function _gotoToday(id){var date,target=$(id),inst=this._getInst(target[0]);if(this._get(inst,"gotoCurrent")&&inst.currentDay){inst.selectedDay=inst.currentDay;inst.drawMonth=inst.selectedMonth=inst.currentMonth;inst.drawYear=inst.selectedYear=inst.currentYear;}else {date=new Date();inst.selectedDay=date.getDate();inst.drawMonth=inst.selectedMonth=date.getMonth();inst.drawYear=inst.selectedYear=date.getFullYear();}this._notifyChange(inst);this._adjustDate(target);}, /* Action for selecting a new month/year. */_selectMonthYear:function _selectMonthYear(id,select,period){var target=$(id),inst=this._getInst(target[0]);inst["selected"+(period==="M"?"Month":"Year")]=inst["draw"+(period==="M"?"Month":"Year")]=parseInt(select.options[select.selectedIndex].value,10);this._notifyChange(inst);this._adjustDate(target);}, /* Action for selecting a day. */_selectDay:function _selectDay(id,month,year,td){var inst,target=$(id);if($(td).hasClass(this._unselectableClass)||this._isDisabledDatepicker(target[0])){return;}inst=this._getInst(target[0]);inst.selectedDay=inst.currentDay=$("a",td).html();inst.selectedMonth=inst.currentMonth=month;inst.selectedYear=inst.currentYear=year;this._selectDate(id,this._formatDate(inst,inst.currentDay,inst.currentMonth,inst.currentYear));}, /* Erase the input field and hide the date picker. */_clearDate:function _clearDate(id){var target=$(id);this._selectDate(target,"");}, /* Update the input field with the selected date. */_selectDate:function _selectDate(id,dateStr){var onSelect,target=$(id),inst=this._getInst(target[0]);dateStr=dateStr!=null?dateStr:this._formatDate(inst);if(inst.input){inst.input.val(dateStr);}this._updateAlternate(inst);onSelect=this._get(inst,"onSelect");if(onSelect){onSelect.apply(inst.input?inst.input[0]:null,[dateStr,inst]); // trigger custom callback
	}else if(inst.input){inst.input.trigger("change"); // fire the change event
	}if(inst.inline){this._updateDatepicker(inst);}else {this._hideDatepicker();this._lastInput=inst.input[0];if(_typeof(inst.input[0])!=="object"){inst.input.focus(); // restore focus
	}this._lastInput=null;}}, /* Update any alternate field to synchronise with the main field. */_updateAlternate:function _updateAlternate(inst){var altFormat,date,dateStr,altField=this._get(inst,"altField");if(altField){ // update alternate field too
	altFormat=this._get(inst,"altFormat")||this._get(inst,"dateFormat");date=this._getDate(inst);dateStr=this.formatDate(altFormat,date,this._getFormatConfig(inst));$(altField).each(function(){$(this).val(dateStr);});}}, /* Set as beforeShowDay function to prevent selection of weekends.
		 * @param  date  Date - the date to customise
		 * @return [boolean, string] - is this date selectable?, what is its CSS class?
		 */noWeekends:function noWeekends(date){var day=date.getDay();return [day>0&&day<6,""];}, /* Set as calculateWeek to determine the week of the year based on the ISO 8601 definition.
		 * @param  date  Date - the date to get the week for
		 * @return  number - the number of the week within the year that contains this date
		 */iso8601Week:function iso8601Week(date){var time,checkDate=new Date(date.getTime()); // Find Thursday of this week starting on Monday
	checkDate.setDate(checkDate.getDate()+4-(checkDate.getDay()||7));time=checkDate.getTime();checkDate.setMonth(0); // Compare with Jan 1
	checkDate.setDate(1);return Math.floor(Math.round((time-checkDate)/86400000)/7)+1;}, /* Parse a string value into a date object.
		 * See formatDate below for the possible formats.
		 *
		 * @param  format string - the expected format of the date
		 * @param  value string - the date in the above format
		 * @param  settings Object - attributes include:
		 *					shortYearCutoff  number - the cutoff year for determining the century (optional)
		 *					dayNamesShort	string[7] - abbreviated names of the days from Sunday (optional)
		 *					dayNames		string[7] - names of the days from Sunday (optional)
		 *					monthNamesShort string[12] - abbreviated names of the months (optional)
		 *					monthNames		string[12] - names of the months (optional)
		 * @return  Date - the extracted date value or null if value is blank
		 */parseDate:function parseDate(format,value,settings){if(format==null||value==null){throw "Invalid arguments";}value=(typeof value==="undefined"?"undefined":_typeof(value))==="object"?value.toString():value+"";if(value===""){return null;}var iFormat,dim,extra,iValue=0,shortYearCutoffTemp=(settings?settings.shortYearCutoff:null)||this._defaults.shortYearCutoff,shortYearCutoff=typeof shortYearCutoffTemp!=="string"?shortYearCutoffTemp:new Date().getFullYear()%100+parseInt(shortYearCutoffTemp,10),dayNamesShort=(settings?settings.dayNamesShort:null)||this._defaults.dayNamesShort,dayNames=(settings?settings.dayNames:null)||this._defaults.dayNames,monthNamesShort=(settings?settings.monthNamesShort:null)||this._defaults.monthNamesShort,monthNames=(settings?settings.monthNames:null)||this._defaults.monthNames,year=-1,month=-1,day=-1,doy=-1,literal=false,date, // Check whether a format character is doubled
	lookAhead=function lookAhead(match){var matches=iFormat+1<format.length&&format.charAt(iFormat+1)===match;if(matches){iFormat++;}return matches;}, // Extract a number from the string value
	getNumber=function getNumber(match){var isDoubled=lookAhead(match),size=match==="@"?14:match==="!"?20:match==="y"&&isDoubled?4:match==="o"?3:2,digits=new RegExp("^\\d{1,"+size+"}"),num=value.substring(iValue).match(digits);if(!num){throw "Missing number at position "+iValue;}iValue+=num[0].length;return parseInt(num[0],10);}, // Extract a name from the string value and convert to an index
	getName=function getName(match,shortNames,longNames){var index=-1,names=$.map(lookAhead(match)?longNames:shortNames,function(v,k){return [[k,v]];}).sort(function(a,b){return -(a[1].length-b[1].length);});$.each(names,function(i,pair){var name=pair[1];if(value.substr(iValue,name.length).toLowerCase()===name.toLowerCase()){index=pair[0];iValue+=name.length;return false;}});if(index!==-1){return index+1;}else {throw "Unknown name at position "+iValue;}}, // Confirm that a literal character matches the string value
	checkLiteral=function checkLiteral(){if(value.charAt(iValue)!==format.charAt(iFormat)){throw "Unexpected literal at position "+iValue;}iValue++;};for(iFormat=0;iFormat<format.length;iFormat++){if(literal){if(format.charAt(iFormat)==="'"&&!lookAhead("'")){literal=false;}else {checkLiteral();}}else {switch(format.charAt(iFormat)){case "d":day=getNumber("d");break;case "D":getName("D",dayNamesShort,dayNames);break;case "o":doy=getNumber("o");break;case "m":month=getNumber("m");break;case "M":month=getName("M",monthNamesShort,monthNames);break;case "y":year=getNumber("y");break;case "@":date=new Date(getNumber("@"));year=date.getFullYear();month=date.getMonth()+1;day=date.getDate();break;case "!":date=new Date((getNumber("!")-this._ticksTo1970)/10000);year=date.getFullYear();month=date.getMonth()+1;day=date.getDate();break;case "'":if(lookAhead("'")){checkLiteral();}else {literal=true;}break;default:checkLiteral();}}}if(iValue<value.length){extra=value.substr(iValue);if(!/^\s+/.test(extra)){throw "Extra/unparsed characters found in date: "+extra;}}if(year===-1){year=new Date().getFullYear();}else if(year<100){year+=new Date().getFullYear()-new Date().getFullYear()%100+(year<=shortYearCutoff?0:-100);}if(doy>-1){month=1;day=doy;do {dim=this._getDaysInMonth(year,month-1);if(day<=dim){break;}month++;day-=dim;}while(true);}date=this._daylightSavingAdjust(new Date(year,month-1,day));if(date.getFullYear()!==year||date.getMonth()+1!==month||date.getDate()!==day){throw "Invalid date"; // E.g. 31/02/00
	}return date;}, /* Standard date formats. */ATOM:"yy-mm-dd", // RFC 3339 (ISO 8601)
	COOKIE:"D, dd M yy",ISO_8601:"yy-mm-dd",RFC_822:"D, d M y",RFC_850:"DD, dd-M-y",RFC_1036:"D, d M y",RFC_1123:"D, d M yy",RFC_2822:"D, d M yy",RSS:"D, d M y", // RFC 822
	TICKS:"!",TIMESTAMP:"@",W3C:"yy-mm-dd", // ISO 8601
	_ticksTo1970:((1970-1)*365+Math.floor(1970/4)-Math.floor(1970/100)+Math.floor(1970/400))*24*60*60*10000000, /* Format a date object into a string value.
		 * The format can be combinations of the following:
		 * d  - day of month (no leading zero)
		 * dd - day of month (two digit)
		 * o  - day of year (no leading zeros)
		 * oo - day of year (three digit)
		 * D  - day name short
		 * DD - day name long
		 * m  - month of year (no leading zero)
		 * mm - month of year (two digit)
		 * M  - month name short
		 * MM - month name long
		 * y  - year (two digit)
		 * yy - year (four digit)
		 * @ - Unix timestamp (ms since 01/01/1970)
		 * ! - Windows ticks (100ns since 01/01/0001)
		 * "..." - literal text
		 * '' - single quote
		 *
		 * @param  format string - the desired format of the date
		 * @param  date Date - the date value to format
		 * @param  settings Object - attributes include:
		 *					dayNamesShort	string[7] - abbreviated names of the days from Sunday (optional)
		 *					dayNames		string[7] - names of the days from Sunday (optional)
		 *					monthNamesShort string[12] - abbreviated names of the months (optional)
		 *					monthNames		string[12] - names of the months (optional)
		 * @return  string - the date in the above format
		 */formatDate:function formatDate(format,date,settings){if(!date){return "";}var iFormat,dayNamesShort=(settings?settings.dayNamesShort:null)||this._defaults.dayNamesShort,dayNames=(settings?settings.dayNames:null)||this._defaults.dayNames,monthNamesShort=(settings?settings.monthNamesShort:null)||this._defaults.monthNamesShort,monthNames=(settings?settings.monthNames:null)||this._defaults.monthNames, // Check whether a format character is doubled
	lookAhead=function lookAhead(match){var matches=iFormat+1<format.length&&format.charAt(iFormat+1)===match;if(matches){iFormat++;}return matches;}, // Format a number, with leading zero if necessary
	formatNumber=function formatNumber(match,value,len){var num=""+value;if(lookAhead(match)){while(num.length<len){num="0"+num;}}return num;}, // Format a name, short or long as requested
	formatName=function formatName(match,value,shortNames,longNames){return lookAhead(match)?longNames[value]:shortNames[value];},output="",literal=false;if(date){for(iFormat=0;iFormat<format.length;iFormat++){if(literal){if(format.charAt(iFormat)==="'"&&!lookAhead("'")){literal=false;}else {output+=format.charAt(iFormat);}}else {switch(format.charAt(iFormat)){case "d":output+=formatNumber("d",date.getDate(),2);break;case "D":output+=formatName("D",date.getDay(),dayNamesShort,dayNames);break;case "o":output+=formatNumber("o",Math.round((new Date(date.getFullYear(),date.getMonth(),date.getDate()).getTime()-new Date(date.getFullYear(),0,0).getTime())/86400000),3);break;case "m":output+=formatNumber("m",date.getMonth()+1,2);break;case "M":output+=formatName("M",date.getMonth(),monthNamesShort,monthNames);break;case "y":output+=lookAhead("y")?date.getFullYear():(date.getYear()%100<10?"0":"")+date.getYear()%100;break;case "@":output+=date.getTime();break;case "!":output+=date.getTime()*10000+this._ticksTo1970;break;case "'":if(lookAhead("'")){output+="'";}else {literal=true;}break;default:output+=format.charAt(iFormat);}}}}return output;}, /* Extract all possible characters from the date format. */_possibleChars:function _possibleChars(format){var iFormat,chars="",literal=false, // Check whether a format character is doubled
	lookAhead=function lookAhead(match){var matches=iFormat+1<format.length&&format.charAt(iFormat+1)===match;if(matches){iFormat++;}return matches;};for(iFormat=0;iFormat<format.length;iFormat++){if(literal){if(format.charAt(iFormat)==="'"&&!lookAhead("'")){literal=false;}else {chars+=format.charAt(iFormat);}}else {switch(format.charAt(iFormat)){case "d":case "m":case "y":case "@":chars+="0123456789";break;case "D":case "M":return null; // Accept anything
	case "'":if(lookAhead("'")){chars+="'";}else {literal=true;}break;default:chars+=format.charAt(iFormat);}}}return chars;}, /* Get a setting value, defaulting if necessary. */_get:function _get(inst,name){return inst.settings[name]!==undefined?inst.settings[name]:this._defaults[name];}, /* Parse existing date and initialise date picker. */_setDateFromField:function _setDateFromField(inst,noDefault){if(inst.input.val()===inst.lastVal){return;}var dateFormat=this._get(inst,"dateFormat"),dates=inst.lastVal=inst.input?inst.input.val():null,defaultDate=this._getDefaultDate(inst),date=defaultDate,settings=this._getFormatConfig(inst);try{date=this.parseDate(dateFormat,dates,settings)||defaultDate;}catch(event){dates=noDefault?"":dates;}inst.selectedDay=date.getDate();inst.drawMonth=inst.selectedMonth=date.getMonth();inst.drawYear=inst.selectedYear=date.getFullYear();inst.currentDay=dates?date.getDate():0;inst.currentMonth=dates?date.getMonth():0;inst.currentYear=dates?date.getFullYear():0;this._adjustInstDate(inst);}, /* Retrieve the default date shown on opening. */_getDefaultDate:function _getDefaultDate(inst){return this._restrictMinMax(inst,this._determineDate(inst,this._get(inst,"defaultDate"),new Date()));}, /* A date may be specified as an exact value or a relative one. */_determineDate:function _determineDate(inst,date,defaultDate){var offsetNumeric=function offsetNumeric(offset){var date=new Date();date.setDate(date.getDate()+offset);return date;},offsetString=function offsetString(offset){try{return $.datepicker.parseDate($.datepicker._get(inst,"dateFormat"),offset,$.datepicker._getFormatConfig(inst));}catch(e){ // Ignore
	}var date=(offset.toLowerCase().match(/^c/)?$.datepicker._getDate(inst):null)||new Date(),year=date.getFullYear(),month=date.getMonth(),day=date.getDate(),pattern=/([+\-]?[0-9]+)\s*(d|D|w|W|m|M|y|Y)?/g,matches=pattern.exec(offset);while(matches){switch(matches[2]||"d"){case "d":case "D":day+=parseInt(matches[1],10);break;case "w":case "W":day+=parseInt(matches[1],10)*7;break;case "m":case "M":month+=parseInt(matches[1],10);day=Math.min(day,$.datepicker._getDaysInMonth(year,month));break;case "y":case "Y":year+=parseInt(matches[1],10);day=Math.min(day,$.datepicker._getDaysInMonth(year,month));break;}matches=pattern.exec(offset);}return new Date(year,month,day);},newDate=date==null||date===""?defaultDate:typeof date==="string"?offsetString(date):typeof date==="number"?isNaN(date)?defaultDate:offsetNumeric(date):new Date(date.getTime());newDate=newDate&&newDate.toString()==="Invalid Date"?defaultDate:newDate;if(newDate){newDate.setHours(0);newDate.setMinutes(0);newDate.setSeconds(0);newDate.setMilliseconds(0);}return this._daylightSavingAdjust(newDate);}, /* Handle switch to/from daylight saving.
		 * Hours may be non-zero on daylight saving cut-over:
		 * > 12 when midnight changeover, but then cannot generate
		 * midnight datetime, so jump to 1AM, otherwise reset.
		 * @param  date  (Date) the date to check
		 * @return  (Date) the corrected date
		 */_daylightSavingAdjust:function _daylightSavingAdjust(date){if(!date){return null;}date.setHours(date.getHours()>12?date.getHours()+2:0);return date;}, /* Set the date(s) directly. */_setDate:function _setDate(inst,date,noChange){var clear=!date,origMonth=inst.selectedMonth,origYear=inst.selectedYear,newDate=this._restrictMinMax(inst,this._determineDate(inst,date,new Date()));inst.selectedDay=inst.currentDay=newDate.getDate();inst.drawMonth=inst.selectedMonth=inst.currentMonth=newDate.getMonth();inst.drawYear=inst.selectedYear=inst.currentYear=newDate.getFullYear();if((origMonth!==inst.selectedMonth||origYear!==inst.selectedYear)&&!noChange){this._notifyChange(inst);}this._adjustInstDate(inst);if(inst.input){inst.input.val(clear?"":this._formatDate(inst));}}, /* Retrieve the date(s) directly. */_getDate:function _getDate(inst){var startDate=!inst.currentYear||inst.input&&inst.input.val()===""?null:this._daylightSavingAdjust(new Date(inst.currentYear,inst.currentMonth,inst.currentDay));return startDate;}, /* Attach the onxxx handlers.  These are declared statically so
		 * they work with static code transformers like Caja.
		 */_attachHandlers:function _attachHandlers(inst){var stepMonths=this._get(inst,"stepMonths"),id="#"+inst.id.replace(/\\\\/g,"\\");inst.dpDiv.find("[data-handler]").map(function(){var handler={prev:function prev(){$.datepicker._adjustDate(id,-stepMonths,"M");},next:function next(){$.datepicker._adjustDate(id,+stepMonths,"M");},hide:function hide(){$.datepicker._hideDatepicker();},today:function today(){$.datepicker._gotoToday(id);},selectDay:function selectDay(){$.datepicker._selectDay(id,+this.getAttribute("data-month"),+this.getAttribute("data-year"),this);return false;},selectMonth:function selectMonth(){$.datepicker._selectMonthYear(id,this,"M");return false;},selectYear:function selectYear(){$.datepicker._selectMonthYear(id,this,"Y");return false;}};$(this).bind(this.getAttribute("data-event"),handler[this.getAttribute("data-handler")]);});}, /* Generate the HTML for the current state of the date picker. */_generateHTML:function _generateHTML(inst){var maxDraw,prevText,prev,nextText,next,currentText,gotoDate,controls,buttonPanel,firstDay,showWeek,dayNames,dayNamesMin,monthNames,monthNamesShort,beforeShowDay,showOtherMonths,selectOtherMonths,defaultDate,html,dow,row,group,col,selectedDate,cornerClass,calender,thead,day,daysInMonth,leadDays,curRows,numRows,printDate,dRow,tbody,daySettings,otherMonth,unselectable,tempDate=new Date(),today=this._daylightSavingAdjust(new Date(tempDate.getFullYear(),tempDate.getMonth(),tempDate.getDate())), // clear time
	isRTL=this._get(inst,"isRTL"),showButtonPanel=this._get(inst,"showButtonPanel"),hideIfNoPrevNext=this._get(inst,"hideIfNoPrevNext"),navigationAsDateFormat=this._get(inst,"navigationAsDateFormat"),numMonths=this._getNumberOfMonths(inst),showCurrentAtPos=this._get(inst,"showCurrentAtPos"),stepMonths=this._get(inst,"stepMonths"),isMultiMonth=numMonths[0]!==1||numMonths[1]!==1,currentDate=this._daylightSavingAdjust(!inst.currentDay?new Date(9999,9,9):new Date(inst.currentYear,inst.currentMonth,inst.currentDay)),minDate=this._getMinMaxDate(inst,"min"),maxDate=this._getMinMaxDate(inst,"max"),drawMonth=inst.drawMonth-showCurrentAtPos,drawYear=inst.drawYear;if(drawMonth<0){drawMonth+=12;drawYear--;}if(maxDate){maxDraw=this._daylightSavingAdjust(new Date(maxDate.getFullYear(),maxDate.getMonth()-numMonths[0]*numMonths[1]+1,maxDate.getDate()));maxDraw=minDate&&maxDraw<minDate?minDate:maxDraw;while(this._daylightSavingAdjust(new Date(drawYear,drawMonth,1))>maxDraw){drawMonth--;if(drawMonth<0){drawMonth=11;drawYear--;}}}inst.drawMonth=drawMonth;inst.drawYear=drawYear;prevText=this._get(inst,"prevText");prevText=!navigationAsDateFormat?prevText:this.formatDate(prevText,this._daylightSavingAdjust(new Date(drawYear,drawMonth-stepMonths,1)),this._getFormatConfig(inst));prev=this._canAdjustMonth(inst,-1,drawYear,drawMonth)?"<a class='ui-datepicker-prev ui-corner-all' data-handler='prev' data-event='click'"+" title='"+prevText+"'><span class='ui-icon ui-icon-circle-triangle-"+(isRTL?"e":"w")+"'>"+prevText+"</span></a>":hideIfNoPrevNext?"":"<a class='ui-datepicker-prev ui-corner-all ui-state-disabled' title='"+prevText+"'><span class='ui-icon ui-icon-circle-triangle-"+(isRTL?"e":"w")+"'>"+prevText+"</span></a>";nextText=this._get(inst,"nextText");nextText=!navigationAsDateFormat?nextText:this.formatDate(nextText,this._daylightSavingAdjust(new Date(drawYear,drawMonth+stepMonths,1)),this._getFormatConfig(inst));next=this._canAdjustMonth(inst,+1,drawYear,drawMonth)?"<a class='ui-datepicker-next ui-corner-all' data-handler='next' data-event='click'"+" title='"+nextText+"'><span class='ui-icon ui-icon-circle-triangle-"+(isRTL?"w":"e")+"'>"+nextText+"</span></a>":hideIfNoPrevNext?"":"<a class='ui-datepicker-next ui-corner-all ui-state-disabled' title='"+nextText+"'><span class='ui-icon ui-icon-circle-triangle-"+(isRTL?"w":"e")+"'>"+nextText+"</span></a>";currentText=this._get(inst,"currentText");gotoDate=this._get(inst,"gotoCurrent")&&inst.currentDay?currentDate:today;currentText=!navigationAsDateFormat?currentText:this.formatDate(currentText,gotoDate,this._getFormatConfig(inst));controls=!inst.inline?"<button type='button' class='ui-datepicker-close ui-state-default ui-priority-primary ui-corner-all' data-handler='hide' data-event='click'>"+this._get(inst,"closeText")+"</button>":"";buttonPanel=showButtonPanel?"<div class='ui-datepicker-buttonpane ui-widget-content'>"+(isRTL?controls:"")+(this._isInRange(inst,gotoDate)?"<button type='button' class='ui-datepicker-current ui-state-default ui-priority-secondary ui-corner-all' data-handler='today' data-event='click'"+">"+currentText+"</button>":"")+(isRTL?"":controls)+"</div>":"";firstDay=parseInt(this._get(inst,"firstDay"),10);firstDay=isNaN(firstDay)?0:firstDay;showWeek=this._get(inst,"showWeek");dayNames=this._get(inst,"dayNames");dayNamesMin=this._get(inst,"dayNamesMin");monthNames=this._get(inst,"monthNames");monthNamesShort=this._get(inst,"monthNamesShort");beforeShowDay=this._get(inst,"beforeShowDay");showOtherMonths=this._get(inst,"showOtherMonths");selectOtherMonths=this._get(inst,"selectOtherMonths");defaultDate=this._getDefaultDate(inst);html="";dow;for(row=0;row<numMonths[0];row++){group="";this.maxRows=4;for(col=0;col<numMonths[1];col++){selectedDate=this._daylightSavingAdjust(new Date(drawYear,drawMonth,inst.selectedDay));cornerClass=" ui-corner-all";calender="";if(isMultiMonth){calender+="<div class='ui-datepicker-group";if(numMonths[1]>1){switch(col){case 0:calender+=" ui-datepicker-group-first";cornerClass=" ui-corner-"+(isRTL?"right":"left");break;case numMonths[1]-1:calender+=" ui-datepicker-group-last";cornerClass=" ui-corner-"+(isRTL?"left":"right");break;default:calender+=" ui-datepicker-group-middle";cornerClass="";break;}}calender+="'>";}calender+="<div class='ui-datepicker-header ui-widget-header ui-helper-clearfix"+cornerClass+"'>"+(/all|left/.test(cornerClass)&&row===0?isRTL?next:prev:"")+(/all|right/.test(cornerClass)&&row===0?isRTL?prev:next:"")+this._generateMonthYearHeader(inst,drawMonth,drawYear,minDate,maxDate,row>0||col>0,monthNames,monthNamesShort)+ // draw month headers
	"</div><table class='ui-datepicker-calendar'><thead>"+"<tr>";thead=showWeek?"<th class='ui-datepicker-week-col'>"+this._get(inst,"weekHeader")+"</th>":"";for(dow=0;dow<7;dow++){ // days of the week
	day=(dow+firstDay)%7;thead+="<th"+((dow+firstDay+6)%7>=5?" class='ui-datepicker-week-end'":"")+">"+"<span title='"+dayNames[day]+"'>"+dayNamesMin[day]+"</span></th>";}calender+=thead+"</tr></thead><tbody>";daysInMonth=this._getDaysInMonth(drawYear,drawMonth);if(drawYear===inst.selectedYear&&drawMonth===inst.selectedMonth){inst.selectedDay=Math.min(inst.selectedDay,daysInMonth);}leadDays=(this._getFirstDayOfMonth(drawYear,drawMonth)-firstDay+7)%7;curRows=Math.ceil((leadDays+daysInMonth)/7); // calculate the number of rows to generate
	numRows=isMultiMonth?this.maxRows>curRows?this.maxRows:curRows:curRows; //If multiple months, use the higher number of rows (see #7043)
	this.maxRows=numRows;printDate=this._daylightSavingAdjust(new Date(drawYear,drawMonth,1-leadDays));for(dRow=0;dRow<numRows;dRow++){ // create date picker rows
	calender+="<tr>";tbody=!showWeek?"":"<td class='ui-datepicker-week-col'>"+this._get(inst,"calculateWeek")(printDate)+"</td>";for(dow=0;dow<7;dow++){ // create date picker days
	daySettings=beforeShowDay?beforeShowDay.apply(inst.input?inst.input[0]:null,[printDate]):[true,""];otherMonth=printDate.getMonth()!==drawMonth;unselectable=otherMonth&&!selectOtherMonths||!daySettings[0]||minDate&&printDate<minDate||maxDate&&printDate>maxDate;tbody+="<td class='"+((dow+firstDay+6)%7>=5?" ui-datepicker-week-end":"")+( // highlight weekends
	otherMonth?" ui-datepicker-other-month":"")+( // highlight days from other months
	printDate.getTime()===selectedDate.getTime()&&drawMonth===inst.selectedMonth&&inst._keyEvent|| // user pressed key
	defaultDate.getTime()===printDate.getTime()&&defaultDate.getTime()===selectedDate.getTime()? // or defaultDate is current printedDate and defaultDate is selectedDate
	" "+this._dayOverClass:"")+( // highlight selected day
	unselectable?" "+this._unselectableClass+" ui-state-disabled":"")+( // highlight unselectable days
	otherMonth&&!showOtherMonths?"":" "+daySettings[1]+( // highlight custom dates
	printDate.getTime()===currentDate.getTime()?" "+this._currentClass:"")+( // highlight selected day
	printDate.getTime()===today.getTime()?" ui-datepicker-today":""))+"'"+( // highlight today (if different)
	(!otherMonth||showOtherMonths)&&daySettings[2]?" title='"+daySettings[2].replace(/'/g,"&#39;")+"'":"")+( // cell title
	unselectable?"":" data-handler='selectDay' data-event='click' data-month='"+printDate.getMonth()+"' data-year='"+printDate.getFullYear()+"'")+">"+( // actions
	otherMonth&&!showOtherMonths?"&#xa0;": // display for other months
	unselectable?"<span class='ui-state-default'>"+printDate.getDate()+"</span>":"<a class='ui-state-default"+(printDate.getTime()===today.getTime()?" ui-state-highlight":"")+(printDate.getTime()===currentDate.getTime()?" ui-state-active":"")+( // highlight selected day
	otherMonth?" ui-priority-secondary":"")+ // distinguish dates from other months
	"' href='#'>"+printDate.getDate()+"</a>")+"</td>"; // display selectable date
	printDate.setDate(printDate.getDate()+1);printDate=this._daylightSavingAdjust(printDate);}calender+=tbody+"</tr>";}drawMonth++;if(drawMonth>11){drawMonth=0;drawYear++;}calender+="</tbody></table>"+(isMultiMonth?"</div>"+(numMonths[0]>0&&col===numMonths[1]-1?"<div class='ui-datepicker-row-break'></div>":""):"");group+=calender;}html+=group;}html+=buttonPanel;inst._keyEvent=false;return html;}, /* Generate the month and year header. */_generateMonthYearHeader:function _generateMonthYearHeader(inst,drawMonth,drawYear,minDate,maxDate,secondary,monthNames,monthNamesShort){var inMinYear,inMaxYear,month,years,thisYear,determineYear,year,endYear,changeMonth=this._get(inst,"changeMonth"),changeYear=this._get(inst,"changeYear"),showMonthAfterYear=this._get(inst,"showMonthAfterYear"),html="<div class='ui-datepicker-title'>",monthHtml=""; // month selection
	if(secondary||!changeMonth){monthHtml+="<span class='ui-datepicker-month'>"+monthNames[drawMonth]+"</span>";}else {inMinYear=minDate&&minDate.getFullYear()===drawYear;inMaxYear=maxDate&&maxDate.getFullYear()===drawYear;monthHtml+="<select class='ui-datepicker-month' data-handler='selectMonth' data-event='change'>";for(month=0;month<12;month++){if((!inMinYear||month>=minDate.getMonth())&&(!inMaxYear||month<=maxDate.getMonth())){monthHtml+="<option value='"+month+"'"+(month===drawMonth?" selected='selected'":"")+">"+monthNamesShort[month]+"</option>";}}monthHtml+="</select>";}if(!showMonthAfterYear){html+=monthHtml+(secondary||!(changeMonth&&changeYear)?"&#xa0;":"");} // year selection
	if(!inst.yearshtml){inst.yearshtml="";if(secondary||!changeYear){html+="<span class='ui-datepicker-year'>"+drawYear+"</span>";}else { // determine range of years to display
	years=this._get(inst,"yearRange").split(":");thisYear=new Date().getFullYear();determineYear=function determineYear(value){var year=value.match(/c[+\-].*/)?drawYear+parseInt(value.substring(1),10):value.match(/[+\-].*/)?thisYear+parseInt(value,10):parseInt(value,10);return isNaN(year)?thisYear:year;};year=determineYear(years[0]);endYear=Math.max(year,determineYear(years[1]||""));year=minDate?Math.max(year,minDate.getFullYear()):year;endYear=maxDate?Math.min(endYear,maxDate.getFullYear()):endYear;inst.yearshtml+="<select class='ui-datepicker-year' data-handler='selectYear' data-event='change'>";for(;year<=endYear;year++){inst.yearshtml+="<option value='"+year+"'"+(year===drawYear?" selected='selected'":"")+">"+year+"</option>";}inst.yearshtml+="</select>";html+=inst.yearshtml;inst.yearshtml=null;}}html+=this._get(inst,"yearSuffix");if(showMonthAfterYear){html+=(secondary||!(changeMonth&&changeYear)?"&#xa0;":"")+monthHtml;}html+="</div>"; // Close datepicker_header
	return html;}, /* Adjust one of the date sub-fields. */_adjustInstDate:function _adjustInstDate(inst,offset,period){var year=inst.drawYear+(period==="Y"?offset:0),month=inst.drawMonth+(period==="M"?offset:0),day=Math.min(inst.selectedDay,this._getDaysInMonth(year,month))+(period==="D"?offset:0),date=this._restrictMinMax(inst,this._daylightSavingAdjust(new Date(year,month,day)));inst.selectedDay=date.getDate();inst.drawMonth=inst.selectedMonth=date.getMonth();inst.drawYear=inst.selectedYear=date.getFullYear();if(period==="M"||period==="Y"){this._notifyChange(inst);}}, /* Ensure a date is within any min/max bounds. */_restrictMinMax:function _restrictMinMax(inst,date){var minDate=this._getMinMaxDate(inst,"min"),maxDate=this._getMinMaxDate(inst,"max"),newDate=minDate&&date<minDate?minDate:date;return maxDate&&newDate>maxDate?maxDate:newDate;}, /* Notify change of month/year. */_notifyChange:function _notifyChange(inst){var onChange=this._get(inst,"onChangeMonthYear");if(onChange){onChange.apply(inst.input?inst.input[0]:null,[inst.selectedYear,inst.selectedMonth+1,inst]);}}, /* Determine the number of months to show. */_getNumberOfMonths:function _getNumberOfMonths(inst){var numMonths=this._get(inst,"numberOfMonths");return numMonths==null?[1,1]:typeof numMonths==="number"?[1,numMonths]:numMonths;}, /* Determine the current maximum date - ensure no time components are set. */_getMinMaxDate:function _getMinMaxDate(inst,minMax){return this._determineDate(inst,this._get(inst,minMax+"Date"),null);}, /* Find the number of days in a given month. */_getDaysInMonth:function _getDaysInMonth(year,month){return 32-this._daylightSavingAdjust(new Date(year,month,32)).getDate();}, /* Find the day of the week of the first of a month. */_getFirstDayOfMonth:function _getFirstDayOfMonth(year,month){return new Date(year,month,1).getDay();}, /* Determines if we should allow a "next/prev" month display change. */_canAdjustMonth:function _canAdjustMonth(inst,offset,curYear,curMonth){var numMonths=this._getNumberOfMonths(inst),date=this._daylightSavingAdjust(new Date(curYear,curMonth+(offset<0?offset:numMonths[0]*numMonths[1]),1));if(offset<0){date.setDate(this._getDaysInMonth(date.getFullYear(),date.getMonth()));}return this._isInRange(inst,date);}, /* Is the given date in the accepted range? */_isInRange:function _isInRange(inst,date){var yearSplit,currentYear,minDate=this._getMinMaxDate(inst,"min"),maxDate=this._getMinMaxDate(inst,"max"),minYear=null,maxYear=null,years=this._get(inst,"yearRange");if(years){yearSplit=years.split(":");currentYear=new Date().getFullYear();minYear=parseInt(yearSplit[0],10);maxYear=parseInt(yearSplit[1],10);if(yearSplit[0].match(/[+\-].*/)){minYear+=currentYear;}if(yearSplit[1].match(/[+\-].*/)){maxYear+=currentYear;}}return (!minDate||date.getTime()>=minDate.getTime())&&(!maxDate||date.getTime()<=maxDate.getTime())&&(!minYear||date.getFullYear()>=minYear)&&(!maxYear||date.getFullYear()<=maxYear);}, /* Provide the configuration settings for formatting/parsing. */_getFormatConfig:function _getFormatConfig(inst){var shortYearCutoff=this._get(inst,"shortYearCutoff");shortYearCutoff=typeof shortYearCutoff!=="string"?shortYearCutoff:new Date().getFullYear()%100+parseInt(shortYearCutoff,10);return {shortYearCutoff:shortYearCutoff,dayNamesShort:this._get(inst,"dayNamesShort"),dayNames:this._get(inst,"dayNames"),monthNamesShort:this._get(inst,"monthNamesShort"),monthNames:this._get(inst,"monthNames")};}, /* Format the given date for display. */_formatDate:function _formatDate(inst,day,month,year){if(!day){inst.currentDay=inst.selectedDay;inst.currentMonth=inst.selectedMonth;inst.currentYear=inst.selectedYear;}var date=day?(typeof day==="undefined"?"undefined":_typeof(day))==="object"?day:this._daylightSavingAdjust(new Date(year,month,day)):this._daylightSavingAdjust(new Date(inst.currentYear,inst.currentMonth,inst.currentDay));return this.formatDate(this._get(inst,"dateFormat"),date,this._getFormatConfig(inst));}}); /*
	 * Bind hover events for datepicker elements.
	 * Done via delegate so the binding only occurs once in the lifetime of the parent div.
	 * Global instActive, set by _updateDatepicker allows the handlers to find their way back to the active picker.
	 */function bindHover(dpDiv){var selector="button, .ui-datepicker-prev, .ui-datepicker-next, .ui-datepicker-calendar td a";return dpDiv.delegate(selector,"mouseout",function(){$(this).removeClass("ui-state-hover");if(this.className.indexOf("ui-datepicker-prev")!==-1){$(this).removeClass("ui-datepicker-prev-hover");}if(this.className.indexOf("ui-datepicker-next")!==-1){$(this).removeClass("ui-datepicker-next-hover");}}).delegate(selector,"mouseover",function(){if(!$.datepicker._isDisabledDatepicker(instActive.inline?dpDiv.parent()[0]:instActive.input[0])){$(this).parents(".ui-datepicker-calendar").find("a").removeClass("ui-state-hover");$(this).addClass("ui-state-hover");if(this.className.indexOf("ui-datepicker-prev")!==-1){$(this).addClass("ui-datepicker-prev-hover");}if(this.className.indexOf("ui-datepicker-next")!==-1){$(this).addClass("ui-datepicker-next-hover");}}});} /* jQuery extend now ignores nulls! */function extendRemove(target,props){$.extend(target,props);for(var name in props){if(props[name]==null){target[name]=props[name];}}return target;} /* Invoke the datepicker functionality.
	   @param  options  string - a command, optionally followed by additional parameters or
						Object - settings for attaching new datepicker functionality
	   @return  jQuery object */$.fn.datepicker=function(options){ /* Verify an empty collection wasn't passed - Fixes #6976 */if(!this.length){return this;} /* Initialise the date picker. */if(!$.datepicker.initialized){$(document).mousedown($.datepicker._checkExternalClick);$.datepicker.initialized=true;} /* Append datepicker main container to body if not exist. */if($("#"+$.datepicker._mainDivId).length===0){$("body").append($.datepicker.dpDiv);}var otherArgs=Array.prototype.slice.call(arguments,1);if(typeof options==="string"&&(options==="isDisabled"||options==="getDate"||options==="widget")){return $.datepicker["_"+options+"Datepicker"].apply($.datepicker,[this[0]].concat(otherArgs));}if(options==="option"&&arguments.length===2&&typeof arguments[1]==="string"){return $.datepicker["_"+options+"Datepicker"].apply($.datepicker,[this[0]].concat(otherArgs));}return this.each(function(){typeof options==="string"?$.datepicker["_"+options+"Datepicker"].apply($.datepicker,[this].concat(otherArgs)):$.datepicker._attachDatepicker(this,options);});};$.datepicker=new Datepicker(); // singleton instance
	$.datepicker.initialized=false;$.datepicker.uuid=new Date().getTime();$.datepicker.version="1.10.3";})(jQuery);(function($,undefined){var sizeRelatedOptions={buttons:true,height:true,maxHeight:true,maxWidth:true,minHeight:true,minWidth:true,width:true},resizableRelatedOptions={maxHeight:true,maxWidth:true,minHeight:true,minWidth:true};$.widget("ui.dialog",{version:"1.10.3",options:{appendTo:"body",autoOpen:true,buttons:[],closeOnEscape:true,closeText:"close",dialogClass:"",draggable:true,hide:null,height:"auto",maxHeight:null,maxWidth:null,minHeight:150,minWidth:150,modal:false,position:{my:"center",at:"center",of:window,collision:"fit", // Ensure the titlebar is always visible
	using:function using(pos){var topOffset=$(this).css(pos).offset().top;if(topOffset<0){$(this).css("top",pos.top-topOffset);}}},resizable:true,show:null,title:null,width:300, // callbacks
	beforeClose:null,close:null,drag:null,dragStart:null,dragStop:null,focus:null,open:null,resize:null,resizeStart:null,resizeStop:null},_create:function _create(){this.originalCss={display:this.element[0].style.display,width:this.element[0].style.width,minHeight:this.element[0].style.minHeight,maxHeight:this.element[0].style.maxHeight,height:this.element[0].style.height};this.originalPosition={parent:this.element.parent(),index:this.element.parent().children().index(this.element)};this.originalTitle=this.element.attr("title");this.options.title=this.options.title||this.originalTitle;this._createWrapper();this.element.show().removeAttr("title").addClass("ui-dialog-content ui-widget-content").appendTo(this.uiDialog);this._createTitlebar();this._createButtonPane();if(this.options.draggable&&$.fn.draggable){this._makeDraggable();}if(this.options.resizable&&$.fn.resizable){this._makeResizable();}this._isOpen=false;},_init:function _init(){if(this.options.autoOpen){this.open();}},_appendTo:function _appendTo(){var element=this.options.appendTo;if(element&&(element.jquery||element.nodeType)){return $(element);}return this.document.find(element||"body").eq(0);},_destroy:function _destroy(){var next,originalPosition=this.originalPosition;this._destroyOverlay();this.element.removeUniqueId().removeClass("ui-dialog-content ui-widget-content").css(this.originalCss) // Without detaching first, the following becomes really slow
	.detach();this.uiDialog.stop(true,true).remove();if(this.originalTitle){this.element.attr("title",this.originalTitle);}next=originalPosition.parent.children().eq(originalPosition.index); // Don't try to place the dialog next to itself (#8613)
	if(next.length&&next[0]!==this.element[0]){next.before(this.element);}else {originalPosition.parent.append(this.element);}},widget:function widget(){return this.uiDialog;},disable:$.noop,enable:$.noop,close:function close(event){var that=this;if(!this._isOpen||this._trigger("beforeClose",event)===false){return;}this._isOpen=false;this._destroyOverlay();if(!this.opener.filter(":focusable").focus().length){ // Hiding a focused element doesn't trigger blur in WebKit
	// so in case we have nothing to focus on, explicitly blur the active element
	// https://bugs.webkit.org/show_bug.cgi?id=47182
	$(this.document[0].activeElement).blur();}this._hide(this.uiDialog,this.options.hide,function(){that._trigger("close",event);});},isOpen:function isOpen(){return this._isOpen;},moveToTop:function moveToTop(){this._moveToTop();},_moveToTop:function _moveToTop(event,silent){var moved=!!this.uiDialog.nextAll(":visible").insertBefore(this.uiDialog).length;if(moved&&!silent){this._trigger("focus",event);}return moved;},open:function open(){var that=this;if(this._isOpen){if(this._moveToTop()){this._focusTabbable();}return;}this._isOpen=true;this.opener=$(this.document[0].activeElement);this._size();this._position();this._createOverlay();this._moveToTop(null,true);this._show(this.uiDialog,this.options.show,function(){that._focusTabbable();that._trigger("focus");});this._trigger("open");},_focusTabbable:function _focusTabbable(){ // Set focus to the first match:
	// 1. First element inside the dialog matching [autofocus]
	// 2. Tabbable element inside the content element
	// 3. Tabbable element inside the buttonpane
	// 4. The close button
	// 5. The dialog itself
	var hasFocus=this.element.find("[autofocus]");if(!hasFocus.length){hasFocus=this.element.find(":tabbable");}if(!hasFocus.length){hasFocus=this.uiDialogButtonPane.find(":tabbable");}if(!hasFocus.length){hasFocus=this.uiDialogTitlebarClose.filter(":tabbable");}if(!hasFocus.length){hasFocus=this.uiDialog;}hasFocus.eq(0).focus();},_keepFocus:function _keepFocus(event){function checkFocus(){var activeElement=this.document[0].activeElement,isActive=this.uiDialog[0]===activeElement||$.contains(this.uiDialog[0],activeElement);if(!isActive){this._focusTabbable();}}event.preventDefault();checkFocus.call(this); // support: IE
	// IE <= 8 doesn't prevent moving focus even with event.preventDefault()
	// so we check again later
	this._delay(checkFocus);},_createWrapper:function _createWrapper(){this.uiDialog=$("<div>").addClass("ui-dialog ui-widget ui-widget-content ui-corner-all ui-front "+this.options.dialogClass).hide().attr({ // Setting tabIndex makes the div focusable
	tabIndex:-1,role:"dialog"}).appendTo(this._appendTo());this._on(this.uiDialog,{keydown:function keydown(event){if(this.options.closeOnEscape&&!event.isDefaultPrevented()&&event.keyCode&&event.keyCode===$.ui.keyCode.ESCAPE){event.preventDefault();this.close(event);return;} // prevent tabbing out of dialogs
	if(event.keyCode!==$.ui.keyCode.TAB){return;}var tabbables=this.uiDialog.find(":tabbable"),first=tabbables.filter(":first"),last=tabbables.filter(":last");if((event.target===last[0]||event.target===this.uiDialog[0])&&!event.shiftKey){first.focus(1);event.preventDefault();}else if((event.target===first[0]||event.target===this.uiDialog[0])&&event.shiftKey){last.focus(1);event.preventDefault();}},mousedown:function mousedown(event){if(this._moveToTop(event)){this._focusTabbable();}}}); // We assume that any existing aria-describedby attribute means
	// that the dialog content is marked up properly
	// otherwise we brute force the content as the description
	if(!this.element.find("[aria-describedby]").length){this.uiDialog.attr({"aria-describedby":this.element.uniqueId().attr("id")});}},_createTitlebar:function _createTitlebar(){var uiDialogTitle;this.uiDialogTitlebar=$("<div>").addClass("ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix").prependTo(this.uiDialog);this._on(this.uiDialogTitlebar,{mousedown:function mousedown(event){ // Don't prevent click on close button (#8838)
	// Focusing a dialog that is partially scrolled out of view
	// causes the browser to scroll it into view, preventing the click event
	if(!$(event.target).closest(".ui-dialog-titlebar-close")){ // Dialog isn't getting focus when dragging (#8063)
	this.uiDialog.focus();}}});this.uiDialogTitlebarClose=$("<button></button>").button({label:this.options.closeText,icons:{primary:"ui-icon-closethick"},text:false}).addClass("ui-dialog-titlebar-close").appendTo(this.uiDialogTitlebar);this._on(this.uiDialogTitlebarClose,{click:function click(event){event.preventDefault();this.close(event);}});uiDialogTitle=$("<span>").uniqueId().addClass("ui-dialog-title").prependTo(this.uiDialogTitlebar);this._title(uiDialogTitle);this.uiDialog.attr({"aria-labelledby":uiDialogTitle.attr("id")});},_title:function _title(title){if(!this.options.title){title.html("&#160;");}title.text(this.options.title);},_createButtonPane:function _createButtonPane(){this.uiDialogButtonPane=$("<div>").addClass("ui-dialog-buttonpane ui-widget-content ui-helper-clearfix");this.uiButtonSet=$("<div>").addClass("ui-dialog-buttonset").appendTo(this.uiDialogButtonPane);this._createButtons();},_createButtons:function _createButtons(){var that=this,buttons=this.options.buttons; // if we already have a button pane, remove it
	this.uiDialogButtonPane.remove();this.uiButtonSet.empty();if($.isEmptyObject(buttons)||$.isArray(buttons)&&!buttons.length){this.uiDialog.removeClass("ui-dialog-buttons");return;}$.each(buttons,function(name,props){var click,buttonOptions;props=$.isFunction(props)?{click:props,text:name}:props; // Default to a non-submitting button
	props=$.extend({type:"button"},props); // Change the context for the click callback to be the main element
	click=props.click;props.click=function(){click.apply(that.element[0],arguments);};buttonOptions={icons:props.icons,text:props.showText};delete props.icons;delete props.showText;$("<button></button>",props).button(buttonOptions).appendTo(that.uiButtonSet);});this.uiDialog.addClass("ui-dialog-buttons");this.uiDialogButtonPane.appendTo(this.uiDialog);},_makeDraggable:function _makeDraggable(){var that=this,options=this.options;function filteredUi(ui){return {position:ui.position,offset:ui.offset};}this.uiDialog.draggable({cancel:".ui-dialog-content, .ui-dialog-titlebar-close",handle:".ui-dialog-titlebar",containment:"document",start:function start(event,ui){$(this).addClass("ui-dialog-dragging");that._blockFrames();that._trigger("dragStart",event,filteredUi(ui));},drag:function drag(event,ui){that._trigger("drag",event,filteredUi(ui));},stop:function stop(event,ui){options.position=[ui.position.left-that.document.scrollLeft(),ui.position.top-that.document.scrollTop()];$(this).removeClass("ui-dialog-dragging");that._unblockFrames();that._trigger("dragStop",event,filteredUi(ui));}});},_makeResizable:function _makeResizable(){var that=this,options=this.options,handles=options.resizable, // .ui-resizable has position: relative defined in the stylesheet
	// but dialogs have to use absolute or fixed positioning
	position=this.uiDialog.css("position"),resizeHandles=typeof handles==="string"?handles:"n,e,s,w,se,sw,ne,nw";function filteredUi(ui){return {originalPosition:ui.originalPosition,originalSize:ui.originalSize,position:ui.position,size:ui.size};}this.uiDialog.resizable({cancel:".ui-dialog-content",containment:"document",alsoResize:this.element,maxWidth:options.maxWidth,maxHeight:options.maxHeight,minWidth:options.minWidth,minHeight:this._minHeight(),handles:resizeHandles,start:function start(event,ui){$(this).addClass("ui-dialog-resizing");that._blockFrames();that._trigger("resizeStart",event,filteredUi(ui));},resize:function resize(event,ui){that._trigger("resize",event,filteredUi(ui));},stop:function stop(event,ui){options.height=$(this).height();options.width=$(this).width();$(this).removeClass("ui-dialog-resizing");that._unblockFrames();that._trigger("resizeStop",event,filteredUi(ui));}}).css("position",position);},_minHeight:function _minHeight(){var options=this.options;return options.height==="auto"?options.minHeight:Math.min(options.minHeight,options.height);},_position:function _position(){ // Need to show the dialog to get the actual offset in the position plugin
	var isVisible=this.uiDialog.is(":visible");if(!isVisible){this.uiDialog.show();}this.uiDialog.position(this.options.position);if(!isVisible){this.uiDialog.hide();}},_setOptions:function _setOptions(options){var that=this,resize=false,resizableOptions={};$.each(options,function(key,value){that._setOption(key,value);if(key in sizeRelatedOptions){resize=true;}if(key in resizableRelatedOptions){resizableOptions[key]=value;}});if(resize){this._size();this._position();}if(this.uiDialog.is(":data(ui-resizable)")){this.uiDialog.resizable("option",resizableOptions);}},_setOption:function _setOption(key,value){ /*jshint maxcomplexity:15*/var isDraggable,isResizable,uiDialog=this.uiDialog;if(key==="dialogClass"){uiDialog.removeClass(this.options.dialogClass).addClass(value);}if(key==="disabled"){return;}this._super(key,value);if(key==="appendTo"){this.uiDialog.appendTo(this._appendTo());}if(key==="buttons"){this._createButtons();}if(key==="closeText"){this.uiDialogTitlebarClose.button({ // Ensure that we always pass a string
	label:""+value});}if(key==="draggable"){isDraggable=uiDialog.is(":data(ui-draggable)");if(isDraggable&&!value){uiDialog.draggable("destroy");}if(!isDraggable&&value){this._makeDraggable();}}if(key==="position"){this._position();}if(key==="resizable"){ // currently resizable, becoming non-resizable
	isResizable=uiDialog.is(":data(ui-resizable)");if(isResizable&&!value){uiDialog.resizable("destroy");} // currently resizable, changing handles
	if(isResizable&&typeof value==="string"){uiDialog.resizable("option","handles",value);} // currently non-resizable, becoming resizable
	if(!isResizable&&value!==false){this._makeResizable();}}if(key==="title"){this._title(this.uiDialogTitlebar.find(".ui-dialog-title"));}},_size:function _size(){ // If the user has resized the dialog, the .ui-dialog and .ui-dialog-content
	// divs will both have width and height set, so we need to reset them
	var nonContentHeight,minContentHeight,maxContentHeight,options=this.options; // Reset content sizing
	this.element.show().css({width:"auto",minHeight:0,maxHeight:"none",height:0});if(options.minWidth>options.width){options.width=options.minWidth;} // reset wrapper sizing
	// determine the height of all the non-content elements
	nonContentHeight=this.uiDialog.css({height:"auto",width:options.width}).outerHeight();minContentHeight=Math.max(0,options.minHeight-nonContentHeight);maxContentHeight=typeof options.maxHeight==="number"?Math.max(0,options.maxHeight-nonContentHeight):"none";if(options.height==="auto"){this.element.css({minHeight:minContentHeight,maxHeight:maxContentHeight,height:"auto"});}else {this.element.height(Math.max(0,options.height-nonContentHeight));}if(this.uiDialog.is(":data(ui-resizable)")){this.uiDialog.resizable("option","minHeight",this._minHeight());}},_blockFrames:function _blockFrames(){this.iframeBlocks=this.document.find("iframe").map(function(){var iframe=$(this);return $("<div>").css({position:"absolute",width:iframe.outerWidth(),height:iframe.outerHeight()}).appendTo(iframe.parent()).offset(iframe.offset())[0];});},_unblockFrames:function _unblockFrames(){if(this.iframeBlocks){this.iframeBlocks.remove();delete this.iframeBlocks;}},_allowInteraction:function _allowInteraction(event){if($(event.target).closest(".ui-dialog").length){return true;} // TODO: Remove hack when datepicker implements
	// the .ui-front logic (#8989)
	return !!$(event.target).closest(".ui-datepicker").length;},_createOverlay:function _createOverlay(){if(!this.options.modal){return;}var that=this,widgetFullName=this.widgetFullName;if(!$.ui.dialog.overlayInstances){ // Prevent use of anchors and inputs.
	// We use a delay in case the overlay is created from an
	// event that we're going to be cancelling. (#2804)
	this._delay(function(){ // Handle .dialog().dialog("close") (#4065)
	if($.ui.dialog.overlayInstances){this.document.bind("focusin.dialog",function(event){if(!that._allowInteraction(event)){event.preventDefault();$(".ui-dialog:visible:last .ui-dialog-content").data(widgetFullName)._focusTabbable();}});}});}this.overlay=$("<div>").addClass("ui-widget-overlay ui-front").appendTo(this._appendTo());this._on(this.overlay,{mousedown:"_keepFocus"});$.ui.dialog.overlayInstances++;},_destroyOverlay:function _destroyOverlay(){if(!this.options.modal){return;}if(this.overlay){$.ui.dialog.overlayInstances--;if(!$.ui.dialog.overlayInstances){this.document.unbind("focusin.dialog");}this.overlay.remove();this.overlay=null;}}});$.ui.dialog.overlayInstances=0; // DEPRECATED
	if($.uiBackCompat!==false){ // position option with array notation
	// just override with old implementation
	$.widget("ui.dialog",$.ui.dialog,{_position:function _position(){var position=this.options.position,myAt=[],offset=[0,0],isVisible;if(position){if(typeof position==="string"||(typeof position==="undefined"?"undefined":_typeof(position))==="object"&&"0" in position){myAt=position.split?position.split(" "):[position[0],position[1]];if(myAt.length===1){myAt[1]=myAt[0];}$.each(["left","top"],function(i,offsetPosition){if(+myAt[i]===myAt[i]){offset[i]=myAt[i];myAt[i]=offsetPosition;}});position={my:myAt[0]+(offset[0]<0?offset[0]:"+"+offset[0])+" "+myAt[1]+(offset[1]<0?offset[1]:"+"+offset[1]),at:myAt.join(" ")};}position=$.extend({},$.ui.dialog.prototype.options.position,position);}else {position=$.ui.dialog.prototype.options.position;} // need to show the dialog to get the actual offset in the position plugin
	isVisible=this.uiDialog.is(":visible");if(!isVisible){this.uiDialog.show();}this.uiDialog.position(position);if(!isVisible){this.uiDialog.hide();}}});}})(jQuery);(function($,undefined){var rvertical=/up|down|vertical/,rpositivemotion=/up|left|vertical|horizontal/;$.effects.effect.blind=function(o,done){ // Create element
	var el=$(this),props=["position","top","bottom","left","right","height","width"],mode=$.effects.setMode(el,o.mode||"hide"),direction=o.direction||"up",vertical=rvertical.test(direction),ref=vertical?"height":"width",ref2=vertical?"top":"left",motion=rpositivemotion.test(direction),animation={},show=mode==="show",wrapper,distance,margin; // if already wrapped, the wrapper's properties are my property. #6245
	if(el.parent().is(".ui-effects-wrapper")){$.effects.save(el.parent(),props);}else {$.effects.save(el,props);}el.show();wrapper=$.effects.createWrapper(el).css({overflow:"hidden"});distance=wrapper[ref]();margin=parseFloat(wrapper.css(ref2))||0;animation[ref]=show?distance:0;if(!motion){el.css(vertical?"bottom":"right",0).css(vertical?"top":"left","auto").css({position:"absolute"});animation[ref2]=show?margin:distance+margin;} // start at 0 if we are showing
	if(show){wrapper.css(ref,0);if(!motion){wrapper.css(ref2,margin+distance);}} // Animate
	wrapper.animate(animation,{duration:o.duration,easing:o.easing,queue:false,complete:function complete(){if(mode==="hide"){el.hide();}$.effects.restore(el,props);$.effects.removeWrapper(el);done();}});};})(jQuery);(function($,undefined){$.effects.effect.bounce=function(o,done){var el=$(this),props=["position","top","bottom","left","right","height","width"], // defaults:
	mode=$.effects.setMode(el,o.mode||"effect"),hide=mode==="hide",show=mode==="show",direction=o.direction||"up",distance=o.distance,times=o.times||5, // number of internal animations
	anims=times*2+(show||hide?1:0),speed=o.duration/anims,easing=o.easing, // utility:
	ref=direction==="up"||direction==="down"?"top":"left",motion=direction==="up"||direction==="left",i,upAnim,downAnim, // we will need to re-assemble the queue to stack our animations in place
	queue=el.queue(),queuelen=queue.length; // Avoid touching opacity to prevent clearType and PNG issues in IE
	if(show||hide){props.push("opacity");}$.effects.save(el,props);el.show();$.effects.createWrapper(el); // Create Wrapper
	// default distance for the BIGGEST bounce is the outer Distance / 3
	if(!distance){distance=el[ref==="top"?"outerHeight":"outerWidth"]()/3;}if(show){downAnim={opacity:1};downAnim[ref]=0; // if we are showing, force opacity 0 and set the initial position
	// then do the "first" animation
	el.css("opacity",0).css(ref,motion?-distance*2:distance*2).animate(downAnim,speed,easing);} // start at the smallest distance if we are hiding
	if(hide){distance=distance/Math.pow(2,times-1);}downAnim={};downAnim[ref]=0; // Bounces up/down/left/right then back to 0 -- times * 2 animations happen here
	for(i=0;i<times;i++){upAnim={};upAnim[ref]=(motion?"-=":"+=")+distance;el.animate(upAnim,speed,easing).animate(downAnim,speed,easing);distance=hide?distance*2:distance/2;} // Last Bounce when Hiding
	if(hide){upAnim={opacity:0};upAnim[ref]=(motion?"-=":"+=")+distance;el.animate(upAnim,speed,easing);}el.queue(function(){if(hide){el.hide();}$.effects.restore(el,props);$.effects.removeWrapper(el);done();}); // inject all the animations we just queued to be first in line (after "inprogress")
	if(queuelen>1){queue.splice.apply(queue,[1,0].concat(queue.splice(queuelen,anims+1)));}el.dequeue();};})(jQuery);(function($,undefined){$.effects.effect.clip=function(o,done){ // Create element
	var el=$(this),props=["position","top","bottom","left","right","height","width"],mode=$.effects.setMode(el,o.mode||"hide"),show=mode==="show",direction=o.direction||"vertical",vert=direction==="vertical",size=vert?"height":"width",position=vert?"top":"left",animation={},wrapper,animate,distance; // Save & Show
	$.effects.save(el,props);el.show(); // Create Wrapper
	wrapper=$.effects.createWrapper(el).css({overflow:"hidden"});animate=el[0].tagName==="IMG"?wrapper:el;distance=animate[size](); // Shift
	if(show){animate.css(size,0);animate.css(position,distance/2);} // Create Animation Object:
	animation[size]=show?distance:0;animation[position]=show?0:distance/2; // Animate
	animate.animate(animation,{queue:false,duration:o.duration,easing:o.easing,complete:function complete(){if(!show){el.hide();}$.effects.restore(el,props);$.effects.removeWrapper(el);done();}});};})(jQuery);(function($,undefined){$.effects.effect.drop=function(o,done){var el=$(this),props=["position","top","bottom","left","right","opacity","height","width"],mode=$.effects.setMode(el,o.mode||"hide"),show=mode==="show",direction=o.direction||"left",ref=direction==="up"||direction==="down"?"top":"left",motion=direction==="up"||direction==="left"?"pos":"neg",animation={opacity:show?1:0},distance; // Adjust
	$.effects.save(el,props);el.show();$.effects.createWrapper(el);distance=o.distance||el[ref==="top"?"outerHeight":"outerWidth"](true)/2;if(show){el.css("opacity",0).css(ref,motion==="pos"?-distance:distance);} // Animation
	animation[ref]=(show?motion==="pos"?"+=":"-=":motion==="pos"?"-=":"+=")+distance; // Animate
	el.animate(animation,{queue:false,duration:o.duration,easing:o.easing,complete:function complete(){if(mode==="hide"){el.hide();}$.effects.restore(el,props);$.effects.removeWrapper(el);done();}});};})(jQuery);(function($,undefined){$.effects.effect.explode=function(o,done){var rows=o.pieces?Math.round(Math.sqrt(o.pieces)):3,cells=rows,el=$(this),mode=$.effects.setMode(el,o.mode||"hide"),show=mode==="show", // show and then visibility:hidden the element before calculating offset
	offset=el.show().css("visibility","hidden").offset(), // width and height of a piece
	width=Math.ceil(el.outerWidth()/cells),height=Math.ceil(el.outerHeight()/rows),pieces=[], // loop
	i,j,left,top,mx,my; // children animate complete:
	function childComplete(){pieces.push(this);if(pieces.length===rows*cells){animComplete();}} // clone the element for each row and cell.
	for(i=0;i<rows;i++){ // ===>
	top=offset.top+i*height;my=i-(rows-1)/2;for(j=0;j<cells;j++){ // |||
	left=offset.left+j*width;mx=j-(cells-1)/2; // Create a clone of the now hidden main element that will be absolute positioned
	// within a wrapper div off the -left and -top equal to size of our pieces
	el.clone().appendTo("body").wrap("<div></div>").css({position:"absolute",visibility:"visible",left:-j*width,top:-i*height}) // select the wrapper - make it overflow: hidden and absolute positioned based on
	// where the original was located +left and +top equal to the size of pieces
	.parent().addClass("ui-effects-explode").css({position:"absolute",overflow:"hidden",width:width,height:height,left:left+(show?mx*width:0),top:top+(show?my*height:0),opacity:show?0:1}).animate({left:left+(show?0:mx*width),top:top+(show?0:my*height),opacity:show?1:0},o.duration||500,o.easing,childComplete);}}function animComplete(){el.css({visibility:"visible"});$(pieces).remove();if(!show){el.hide();}done();}};})(jQuery);(function($,undefined){$.effects.effect.fade=function(o,done){var el=$(this),mode=$.effects.setMode(el,o.mode||"toggle");el.animate({opacity:mode},{queue:false,duration:o.duration,easing:o.easing,complete:done});};})(jQuery);(function($,undefined){$.effects.effect.fold=function(o,done){ // Create element
	var el=$(this),props=["position","top","bottom","left","right","height","width"],mode=$.effects.setMode(el,o.mode||"hide"),show=mode==="show",hide=mode==="hide",size=o.size||15,percent=/([0-9]+)%/.exec(size),horizFirst=!!o.horizFirst,widthFirst=show!==horizFirst,ref=widthFirst?["width","height"]:["height","width"],duration=o.duration/2,wrapper,distance,animation1={},animation2={};$.effects.save(el,props);el.show(); // Create Wrapper
	wrapper=$.effects.createWrapper(el).css({overflow:"hidden"});distance=widthFirst?[wrapper.width(),wrapper.height()]:[wrapper.height(),wrapper.width()];if(percent){size=parseInt(percent[1],10)/100*distance[hide?0:1];}if(show){wrapper.css(horizFirst?{height:0,width:size}:{height:size,width:0});} // Animation
	animation1[ref[0]]=show?distance[0]:size;animation2[ref[1]]=show?distance[1]:0; // Animate
	wrapper.animate(animation1,duration,o.easing).animate(animation2,duration,o.easing,function(){if(hide){el.hide();}$.effects.restore(el,props);$.effects.removeWrapper(el);done();});};})(jQuery);(function($,undefined){$.effects.effect.highlight=function(o,done){var elem=$(this),props=["backgroundImage","backgroundColor","opacity"],mode=$.effects.setMode(elem,o.mode||"show"),animation={backgroundColor:elem.css("backgroundColor")};if(mode==="hide"){animation.opacity=0;}$.effects.save(elem,props);elem.show().css({backgroundImage:"none",backgroundColor:o.color||"#ffff99"}).animate(animation,{queue:false,duration:o.duration,easing:o.easing,complete:function complete(){if(mode==="hide"){elem.hide();}$.effects.restore(elem,props);done();}});};})(jQuery);(function($,undefined){$.effects.effect.pulsate=function(o,done){var elem=$(this),mode=$.effects.setMode(elem,o.mode||"show"),show=mode==="show",hide=mode==="hide",showhide=show||mode==="hide", // showing or hiding leaves of the "last" animation
	anims=(o.times||5)*2+(showhide?1:0),duration=o.duration/anims,animateTo=0,queue=elem.queue(),queuelen=queue.length,i;if(show||!elem.is(":visible")){elem.css("opacity",0).show();animateTo=1;} // anims - 1 opacity "toggles"
	for(i=1;i<anims;i++){elem.animate({opacity:animateTo},duration,o.easing);animateTo=1-animateTo;}elem.animate({opacity:animateTo},duration,o.easing);elem.queue(function(){if(hide){elem.hide();}done();}); // We just queued up "anims" animations, we need to put them next in the queue
	if(queuelen>1){queue.splice.apply(queue,[1,0].concat(queue.splice(queuelen,anims+1)));}elem.dequeue();};})(jQuery);(function($,undefined){$.effects.effect.puff=function(o,done){var elem=$(this),mode=$.effects.setMode(elem,o.mode||"hide"),hide=mode==="hide",percent=parseInt(o.percent,10)||150,factor=percent/100,original={height:elem.height(),width:elem.width(),outerHeight:elem.outerHeight(),outerWidth:elem.outerWidth()};$.extend(o,{effect:"scale",queue:false,fade:true,mode:mode,complete:done,percent:hide?percent:100,from:hide?original:{height:original.height*factor,width:original.width*factor,outerHeight:original.outerHeight*factor,outerWidth:original.outerWidth*factor}});elem.effect(o);};$.effects.effect.scale=function(o,done){ // Create element
	var el=$(this),options=$.extend(true,{},o),mode=$.effects.setMode(el,o.mode||"effect"),percent=parseInt(o.percent,10)||(parseInt(o.percent,10)===0?0:mode==="hide"?0:100),direction=o.direction||"both",origin=o.origin,original={height:el.height(),width:el.width(),outerHeight:el.outerHeight(),outerWidth:el.outerWidth()},factor={y:direction!=="horizontal"?percent/100:1,x:direction!=="vertical"?percent/100:1}; // We are going to pass this effect to the size effect:
	options.effect="size";options.queue=false;options.complete=done; // Set default origin and restore for show/hide
	if(mode!=="effect"){options.origin=origin||["middle","center"];options.restore=true;}options.from=o.from||(mode==="show"?{height:0,width:0,outerHeight:0,outerWidth:0}:original);options.to={height:original.height*factor.y,width:original.width*factor.x,outerHeight:original.outerHeight*factor.y,outerWidth:original.outerWidth*factor.x}; // Fade option to support puff
	if(options.fade){if(mode==="show"){options.from.opacity=0;options.to.opacity=1;}if(mode==="hide"){options.from.opacity=1;options.to.opacity=0;}} // Animate
	el.effect(options);};$.effects.effect.size=function(o,done){ // Create element
	var original,baseline,factor,el=$(this),props0=["position","top","bottom","left","right","width","height","overflow","opacity"], // Always restore
	props1=["position","top","bottom","left","right","overflow","opacity"], // Copy for children
	props2=["width","height","overflow"],cProps=["fontSize"],vProps=["borderTopWidth","borderBottomWidth","paddingTop","paddingBottom"],hProps=["borderLeftWidth","borderRightWidth","paddingLeft","paddingRight"], // Set options
	mode=$.effects.setMode(el,o.mode||"effect"),restore=o.restore||mode!=="effect",scale=o.scale||"both",origin=o.origin||["middle","center"],position=el.css("position"),props=restore?props0:props1,zero={height:0,width:0,outerHeight:0,outerWidth:0};if(mode==="show"){el.show();}original={height:el.height(),width:el.width(),outerHeight:el.outerHeight(),outerWidth:el.outerWidth()};if(o.mode==="toggle"&&mode==="show"){el.from=o.to||zero;el.to=o.from||original;}else {el.from=o.from||(mode==="show"?zero:original);el.to=o.to||(mode==="hide"?zero:original);} // Set scaling factor
	factor={from:{y:el.from.height/original.height,x:el.from.width/original.width},to:{y:el.to.height/original.height,x:el.to.width/original.width}}; // Scale the css box
	if(scale==="box"||scale==="both"){ // Vertical props scaling
	if(factor.from.y!==factor.to.y){props=props.concat(vProps);el.from=$.effects.setTransition(el,vProps,factor.from.y,el.from);el.to=$.effects.setTransition(el,vProps,factor.to.y,el.to);} // Horizontal props scaling
	if(factor.from.x!==factor.to.x){props=props.concat(hProps);el.from=$.effects.setTransition(el,hProps,factor.from.x,el.from);el.to=$.effects.setTransition(el,hProps,factor.to.x,el.to);}} // Scale the content
	if(scale==="content"||scale==="both"){ // Vertical props scaling
	if(factor.from.y!==factor.to.y){props=props.concat(cProps).concat(props2);el.from=$.effects.setTransition(el,cProps,factor.from.y,el.from);el.to=$.effects.setTransition(el,cProps,factor.to.y,el.to);}}$.effects.save(el,props);el.show();$.effects.createWrapper(el);el.css("overflow","hidden").css(el.from); // Adjust
	if(origin){ // Calculate baseline shifts
	baseline=$.effects.getBaseline(origin,original);el.from.top=(original.outerHeight-el.outerHeight())*baseline.y;el.from.left=(original.outerWidth-el.outerWidth())*baseline.x;el.to.top=(original.outerHeight-el.to.outerHeight)*baseline.y;el.to.left=(original.outerWidth-el.to.outerWidth)*baseline.x;}el.css(el.from); // set top & left
	// Animate
	if(scale==="content"||scale==="both"){ // Scale the children
	// Add margins/font-size
	vProps=vProps.concat(["marginTop","marginBottom"]).concat(cProps);hProps=hProps.concat(["marginLeft","marginRight"]);props2=props0.concat(vProps).concat(hProps);el.find("*[width]").each(function(){var child=$(this),c_original={height:child.height(),width:child.width(),outerHeight:child.outerHeight(),outerWidth:child.outerWidth()};if(restore){$.effects.save(child,props2);}child.from={height:c_original.height*factor.from.y,width:c_original.width*factor.from.x,outerHeight:c_original.outerHeight*factor.from.y,outerWidth:c_original.outerWidth*factor.from.x};child.to={height:c_original.height*factor.to.y,width:c_original.width*factor.to.x,outerHeight:c_original.height*factor.to.y,outerWidth:c_original.width*factor.to.x}; // Vertical props scaling
	if(factor.from.y!==factor.to.y){child.from=$.effects.setTransition(child,vProps,factor.from.y,child.from);child.to=$.effects.setTransition(child,vProps,factor.to.y,child.to);} // Horizontal props scaling
	if(factor.from.x!==factor.to.x){child.from=$.effects.setTransition(child,hProps,factor.from.x,child.from);child.to=$.effects.setTransition(child,hProps,factor.to.x,child.to);} // Animate children
	child.css(child.from);child.animate(child.to,o.duration,o.easing,function(){ // Restore children
	if(restore){$.effects.restore(child,props2);}});});} // Animate
	el.animate(el.to,{queue:false,duration:o.duration,easing:o.easing,complete:function complete(){if(el.to.opacity===0){el.css("opacity",el.from.opacity);}if(mode==="hide"){el.hide();}$.effects.restore(el,props);if(!restore){ // we need to calculate our new positioning based on the scaling
	if(position==="static"){el.css({position:"relative",top:el.to.top,left:el.to.left});}else {$.each(["top","left"],function(idx,pos){el.css(pos,function(_,str){var val=parseInt(str,10),toRef=idx?el.to.left:el.to.top; // if original was "auto", recalculate the new value from wrapper
	if(str==="auto"){return toRef+"px";}return val+toRef+"px";});});}}$.effects.removeWrapper(el);done();}});};})(jQuery);(function($,undefined){$.effects.effect.shake=function(o,done){var el=$(this),props=["position","top","bottom","left","right","height","width"],mode=$.effects.setMode(el,o.mode||"effect"),direction=o.direction||"left",distance=o.distance||20,times=o.times||3,anims=times*2+1,speed=Math.round(o.duration/anims),ref=direction==="up"||direction==="down"?"top":"left",positiveMotion=direction==="up"||direction==="left",animation={},animation1={},animation2={},i, // we will need to re-assemble the queue to stack our animations in place
	queue=el.queue(),queuelen=queue.length;$.effects.save(el,props);el.show();$.effects.createWrapper(el); // Animation
	animation[ref]=(positiveMotion?"-=":"+=")+distance;animation1[ref]=(positiveMotion?"+=":"-=")+distance*2;animation2[ref]=(positiveMotion?"-=":"+=")+distance*2; // Animate
	el.animate(animation,speed,o.easing); // Shakes
	for(i=1;i<times;i++){el.animate(animation1,speed,o.easing).animate(animation2,speed,o.easing);}el.animate(animation1,speed,o.easing).animate(animation,speed/2,o.easing).queue(function(){if(mode==="hide"){el.hide();}$.effects.restore(el,props);$.effects.removeWrapper(el);done();}); // inject all the animations we just queued to be first in line (after "inprogress")
	if(queuelen>1){queue.splice.apply(queue,[1,0].concat(queue.splice(queuelen,anims+1)));}el.dequeue();};})(jQuery);(function($,undefined){$.effects.effect.slide=function(o,done){ // Create element
	var el=$(this),props=["position","top","bottom","left","right","width","height"],mode=$.effects.setMode(el,o.mode||"show"),show=mode==="show",direction=o.direction||"left",ref=direction==="up"||direction==="down"?"top":"left",positiveMotion=direction==="up"||direction==="left",distance,animation={}; // Adjust
	$.effects.save(el,props);el.show();distance=o.distance||el[ref==="top"?"outerHeight":"outerWidth"](true);$.effects.createWrapper(el).css({overflow:"hidden"});if(show){el.css(ref,positiveMotion?isNaN(distance)?"-"+distance:-distance:distance);} // Animation
	animation[ref]=(show?positiveMotion?"+=":"-=":positiveMotion?"-=":"+=")+distance; // Animate
	el.animate(animation,{queue:false,duration:o.duration,easing:o.easing,complete:function complete(){if(mode==="hide"){el.hide();}$.effects.restore(el,props);$.effects.removeWrapper(el);done();}});};})(jQuery);(function($,undefined){$.effects.effect.transfer=function(o,done){var elem=$(this),target=$(o.to),targetFixed=target.css("position")==="fixed",body=$("body"),fixTop=targetFixed?body.scrollTop():0,fixLeft=targetFixed?body.scrollLeft():0,endPosition=target.offset(),animation={top:endPosition.top-fixTop,left:endPosition.left-fixLeft,height:target.innerHeight(),width:target.innerWidth()},startPosition=elem.offset(),transfer=$("<div class='ui-effects-transfer'></div>").appendTo(document.body).addClass(o.className).css({top:startPosition.top-fixTop,left:startPosition.left-fixLeft,height:elem.innerHeight(),width:elem.innerWidth(),position:targetFixed?"fixed":"absolute"}).animate(animation,o.duration,o.easing,function(){transfer.remove();done();});};})(jQuery);(function($,undefined){$.widget("ui.menu",{version:"1.10.3",defaultElement:"<ul>",delay:300,options:{icons:{submenu:"ui-icon-carat-1-e"},menus:"ul",position:{my:"left top",at:"right top"},role:"menu", // callbacks
	blur:null,focus:null,select:null},_create:function _create(){this.activeMenu=this.element; // flag used to prevent firing of the click handler
	// as the event bubbles up through nested menus
	this.mouseHandled=false;this.element.uniqueId().addClass("ui-menu ui-widget ui-widget-content ui-corner-all").toggleClass("ui-menu-icons",!!this.element.find(".ui-icon").length).attr({role:this.options.role,tabIndex:0}) // need to catch all clicks on disabled menu
	// not possible through _on
	.bind("click"+this.eventNamespace,$.proxy(function(event){if(this.options.disabled){event.preventDefault();}},this));if(this.options.disabled){this.element.addClass("ui-state-disabled").attr("aria-disabled","true");}this._on({ // Prevent focus from sticking to links inside menu after clicking
	// them (focus should always stay on UL during navigation).
	"mousedown .ui-menu-item > a":function mousedownUiMenuItemA(event){event.preventDefault();},"click .ui-state-disabled > a":function clickUiStateDisabledA(event){event.preventDefault();},"click .ui-menu-item:has(a)":function clickUiMenuItemHasA(event){var target=$(event.target).closest(".ui-menu-item");if(!this.mouseHandled&&target.not(".ui-state-disabled").length){this.mouseHandled=true;this.select(event); // Open submenu on click
	if(target.has(".ui-menu").length){this.expand(event);}else if(!this.element.is(":focus")){ // Redirect focus to the menu
	this.element.trigger("focus",[true]); // If the active item is on the top level, let it stay active.
	// Otherwise, blur the active item since it is no longer visible.
	if(this.active&&this.active.parents(".ui-menu").length===1){clearTimeout(this.timer);}}}},"mouseenter .ui-menu-item":function mouseenterUiMenuItem(event){var target=$(event.currentTarget); // Remove ui-state-active class from siblings of the newly focused menu item
	// to avoid a jump caused by adjacent elements both having a class with a border
	target.siblings().children(".ui-state-active").removeClass("ui-state-active");this.focus(event,target);},mouseleave:"collapseAll","mouseleave .ui-menu":"collapseAll",focus:function focus(event,keepActiveItem){ // If there's already an active item, keep it active
	// If not, activate the first item
	var item=this.active||this.element.children(".ui-menu-item").eq(0);if(!keepActiveItem){this.focus(event,item);}},blur:function blur(event){this._delay(function(){if(!$.contains(this.element[0],this.document[0].activeElement)){this.collapseAll(event);}});},keydown:"_keydown"});this.refresh(); // Clicks outside of a menu collapse any open menus
	this._on(this.document,{click:function click(event){if(!$(event.target).closest(".ui-menu").length){this.collapseAll(event);} // Reset the mouseHandled flag
	this.mouseHandled=false;}});},_destroy:function _destroy(){ // Destroy (sub)menus
	this.element.removeAttr("aria-activedescendant").find(".ui-menu").addBack().removeClass("ui-menu ui-widget ui-widget-content ui-corner-all ui-menu-icons").removeAttr("role").removeAttr("tabIndex").removeAttr("aria-labelledby").removeAttr("aria-expanded").removeAttr("aria-hidden").removeAttr("aria-disabled").removeUniqueId().show(); // Destroy menu items
	this.element.find(".ui-menu-item").removeClass("ui-menu-item").removeAttr("role").removeAttr("aria-disabled").children("a").removeUniqueId().removeClass("ui-corner-all ui-state-hover").removeAttr("tabIndex").removeAttr("role").removeAttr("aria-haspopup").children().each(function(){var elem=$(this);if(elem.data("ui-menu-submenu-carat")){elem.remove();}}); // Destroy menu dividers
	this.element.find(".ui-menu-divider").removeClass("ui-menu-divider ui-widget-content");},_keydown:function _keydown(event){ /*jshint maxcomplexity:20*/var match,prev,character,skip,regex,preventDefault=true;function escape(value){return value.replace(/[\-\[\]{}()*+?.,\\\^$|#\s]/g,"\\$&");}switch(event.keyCode){case $.ui.keyCode.PAGE_UP:this.previousPage(event);break;case $.ui.keyCode.PAGE_DOWN:this.nextPage(event);break;case $.ui.keyCode.HOME:this._move("first","first",event);break;case $.ui.keyCode.END:this._move("last","last",event);break;case $.ui.keyCode.UP:this.previous(event);break;case $.ui.keyCode.DOWN:this.next(event);break;case $.ui.keyCode.LEFT:this.collapse(event);break;case $.ui.keyCode.RIGHT:if(this.active&&!this.active.is(".ui-state-disabled")){this.expand(event);}break;case $.ui.keyCode.ENTER:case $.ui.keyCode.SPACE:this._activate(event);break;case $.ui.keyCode.ESCAPE:this.collapse(event);break;default:preventDefault=false;prev=this.previousFilter||"";character=String.fromCharCode(event.keyCode);skip=false;clearTimeout(this.filterTimer);if(character===prev){skip=true;}else {character=prev+character;}regex=new RegExp("^"+escape(character),"i");match=this.activeMenu.children(".ui-menu-item").filter(function(){return regex.test($(this).children("a").text());});match=skip&&match.index(this.active.next())!==-1?this.active.nextAll(".ui-menu-item"):match; // If no matches on the current filter, reset to the last character pressed
	// to move down the menu to the first item that starts with that character
	if(!match.length){character=String.fromCharCode(event.keyCode);regex=new RegExp("^"+escape(character),"i");match=this.activeMenu.children(".ui-menu-item").filter(function(){return regex.test($(this).children("a").text());});}if(match.length){this.focus(event,match);if(match.length>1){this.previousFilter=character;this.filterTimer=this._delay(function(){delete this.previousFilter;},1000);}else {delete this.previousFilter;}}else {delete this.previousFilter;}}if(preventDefault){event.preventDefault();}},_activate:function _activate(event){if(!this.active.is(".ui-state-disabled")){if(this.active.children("a[aria-haspopup='true']").length){this.expand(event);}else {this.select(event);}}},refresh:function refresh(){var menus,icon=this.options.icons.submenu,submenus=this.element.find(this.options.menus); // Initialize nested menus
	submenus.filter(":not(.ui-menu)").addClass("ui-menu ui-widget ui-widget-content ui-corner-all").hide().attr({role:this.options.role,"aria-hidden":"true","aria-expanded":"false"}).each(function(){var menu=$(this),item=menu.prev("a"),submenuCarat=$("<span>").addClass("ui-menu-icon ui-icon "+icon).data("ui-menu-submenu-carat",true);item.attr("aria-haspopup","true").prepend(submenuCarat);menu.attr("aria-labelledby",item.attr("id"));});menus=submenus.add(this.element); // Don't refresh list items that are already adapted
	menus.children(":not(.ui-menu-item):has(a)").addClass("ui-menu-item").attr("role","presentation").children("a").uniqueId().addClass("ui-corner-all").attr({tabIndex:-1,role:this._itemRole()}); // Initialize unlinked menu-items containing spaces and/or dashes only as dividers
	menus.children(":not(.ui-menu-item)").each(function(){var item=$(this); // hyphen, em dash, en dash
	if(!/[^\-\u2014\u2013\s]/.test(item.text())){item.addClass("ui-widget-content ui-menu-divider");}}); // Add aria-disabled attribute to any disabled menu item
	menus.children(".ui-state-disabled").attr("aria-disabled","true"); // If the active item has been removed, blur the menu
	if(this.active&&!$.contains(this.element[0],this.active[0])){this.blur();}},_itemRole:function _itemRole(){return {menu:"menuitem",listbox:"option"}[this.options.role];},_setOption:function _setOption(key,value){if(key==="icons"){this.element.find(".ui-menu-icon").removeClass(this.options.icons.submenu).addClass(value.submenu);}this._super(key,value);},focus:function focus(event,item){var nested,focused;this.blur(event,event&&event.type==="focus");this._scrollIntoView(item);this.active=item.first();focused=this.active.children("a").addClass("ui-state-focus"); // Only update aria-activedescendant if there's a role
	// otherwise we assume focus is managed elsewhere
	if(this.options.role){this.element.attr("aria-activedescendant",focused.attr("id"));} // Highlight active parent menu item, if any
	this.active.parent().closest(".ui-menu-item").children("a:first").addClass("ui-state-active");if(event&&event.type==="keydown"){this._close();}else {this.timer=this._delay(function(){this._close();},this.delay);}nested=item.children(".ui-menu");if(nested.length&&/^mouse/.test(event.type)){this._startOpening(nested);}this.activeMenu=item.parent();this._trigger("focus",event,{item:item});},_scrollIntoView:function _scrollIntoView(item){var borderTop,paddingTop,offset,scroll,elementHeight,itemHeight;if(this._hasScroll()){borderTop=parseFloat($.css(this.activeMenu[0],"borderTopWidth"))||0;paddingTop=parseFloat($.css(this.activeMenu[0],"paddingTop"))||0;offset=item.offset().top-this.activeMenu.offset().top-borderTop-paddingTop;scroll=this.activeMenu.scrollTop();elementHeight=this.activeMenu.height();itemHeight=item.height();if(offset<0){this.activeMenu.scrollTop(scroll+offset);}else if(offset+itemHeight>elementHeight){this.activeMenu.scrollTop(scroll+offset-elementHeight+itemHeight);}}},blur:function blur(event,fromFocus){if(!fromFocus){clearTimeout(this.timer);}if(!this.active){return;}this.active.children("a").removeClass("ui-state-focus");this.active=null;this._trigger("blur",event,{item:this.active});},_startOpening:function _startOpening(submenu){clearTimeout(this.timer); // Don't open if already open fixes a Firefox bug that caused a .5 pixel
	// shift in the submenu position when mousing over the carat icon
	if(submenu.attr("aria-hidden")!=="true"){return;}this.timer=this._delay(function(){this._close();this._open(submenu);},this.delay);},_open:function _open(submenu){var position=$.extend({of:this.active},this.options.position);clearTimeout(this.timer);this.element.find(".ui-menu").not(submenu.parents(".ui-menu")).hide().attr("aria-hidden","true");submenu.show().removeAttr("aria-hidden").attr("aria-expanded","true").position(position);},collapseAll:function collapseAll(event,all){clearTimeout(this.timer);this.timer=this._delay(function(){ // If we were passed an event, look for the submenu that contains the event
	var currentMenu=all?this.element:$(event&&event.target).closest(this.element.find(".ui-menu")); // If we found no valid submenu ancestor, use the main menu to close all sub menus anyway
	if(!currentMenu.length){currentMenu=this.element;}this._close(currentMenu);this.blur(event);this.activeMenu=currentMenu;},this.delay);}, // With no arguments, closes the currently active menu - if nothing is active
	// it closes all menus.  If passed an argument, it will search for menus BELOW
	_close:function _close(startMenu){if(!startMenu){startMenu=this.active?this.active.parent():this.element;}startMenu.find(".ui-menu").hide().attr("aria-hidden","true").attr("aria-expanded","false").end().find("a.ui-state-active").removeClass("ui-state-active");},collapse:function collapse(event){var newItem=this.active&&this.active.parent().closest(".ui-menu-item",this.element);if(newItem&&newItem.length){this._close();this.focus(event,newItem);}},expand:function expand(event){var newItem=this.active&&this.active.children(".ui-menu ").children(".ui-menu-item").first();if(newItem&&newItem.length){this._open(newItem.parent()); // Delay so Firefox will not hide activedescendant change in expanding submenu from AT
	this._delay(function(){this.focus(event,newItem);});}},next:function next(event){this._move("next","first",event);},previous:function previous(event){this._move("prev","last",event);},isFirstItem:function isFirstItem(){return this.active&&!this.active.prevAll(".ui-menu-item").length;},isLastItem:function isLastItem(){return this.active&&!this.active.nextAll(".ui-menu-item").length;},_move:function _move(direction,filter,event){var next;if(this.active){if(direction==="first"||direction==="last"){next=this.active[direction==="first"?"prevAll":"nextAll"](".ui-menu-item").eq(-1);}else {next=this.active[direction+"All"](".ui-menu-item").eq(0);}}if(!next||!next.length||!this.active){next=this.activeMenu.children(".ui-menu-item")[filter]();}this.focus(event,next);},nextPage:function nextPage(event){var item,base,height;if(!this.active){this.next(event);return;}if(this.isLastItem()){return;}if(this._hasScroll()){base=this.active.offset().top;height=this.element.height();this.active.nextAll(".ui-menu-item").each(function(){item=$(this);return item.offset().top-base-height<0;});this.focus(event,item);}else {this.focus(event,this.activeMenu.children(".ui-menu-item")[!this.active?"first":"last"]());}},previousPage:function previousPage(event){var item,base,height;if(!this.active){this.next(event);return;}if(this.isFirstItem()){return;}if(this._hasScroll()){base=this.active.offset().top;height=this.element.height();this.active.prevAll(".ui-menu-item").each(function(){item=$(this);return item.offset().top-base+height>0;});this.focus(event,item);}else {this.focus(event,this.activeMenu.children(".ui-menu-item").first());}},_hasScroll:function _hasScroll(){return this.element.outerHeight()<this.element.prop("scrollHeight");},select:function select(event){ // TODO: It should never be possible to not have an active item at this
	// point, but the tests don't trigger mouseenter before click.
	this.active=this.active||$(event.target).closest(".ui-menu-item");var ui={item:this.active};if(!this.active.has(".ui-menu").length){this.collapseAll(event,true);}this._trigger("select",event,ui);}});})(jQuery);(function($,undefined){$.ui=$.ui||{};var cachedScrollbarWidth,max=Math.max,abs=Math.abs,round=Math.round,rhorizontal=/left|center|right/,rvertical=/top|center|bottom/,roffset=/[\+\-]\d+(\.[\d]+)?%?/,rposition=/^\w+/,rpercent=/%$/,_position=$.fn.position;function getOffsets(offsets,width,height){return [parseFloat(offsets[0])*(rpercent.test(offsets[0])?width/100:1),parseFloat(offsets[1])*(rpercent.test(offsets[1])?height/100:1)];}function parseCss(element,property){return parseInt($.css(element,property),10)||0;}function getDimensions(elem){var raw=elem[0];if(raw.nodeType===9){return {width:elem.width(),height:elem.height(),offset:{top:0,left:0}};}if($.isWindow(raw)){return {width:elem.width(),height:elem.height(),offset:{top:elem.scrollTop(),left:elem.scrollLeft()}};}if(raw.preventDefault){return {width:0,height:0,offset:{top:raw.pageY,left:raw.pageX}};}return {width:elem.outerWidth(),height:elem.outerHeight(),offset:elem.offset()};}$.position={scrollbarWidth:function scrollbarWidth(){if(cachedScrollbarWidth!==undefined){return cachedScrollbarWidth;}var w1,w2,div=$("<div style='display:block;width:50px;height:50px;overflow:hidden;'><div style='height:100px;width:auto;'></div></div>"),innerDiv=div.children()[0];$("body").append(div);w1=innerDiv.offsetWidth;div.css("overflow","scroll");w2=innerDiv.offsetWidth;if(w1===w2){w2=div[0].clientWidth;}div.remove();return cachedScrollbarWidth=w1-w2;},getScrollInfo:function getScrollInfo(within){var overflowX=within.isWindow?"":within.element.css("overflow-x"),overflowY=within.isWindow?"":within.element.css("overflow-y"),hasOverflowX=overflowX==="scroll"||overflowX==="auto"&&within.width<within.element[0].scrollWidth,hasOverflowY=overflowY==="scroll"||overflowY==="auto"&&within.height<within.element[0].scrollHeight;return {width:hasOverflowY?$.position.scrollbarWidth():0,height:hasOverflowX?$.position.scrollbarWidth():0};},getWithinInfo:function getWithinInfo(element){var withinElement=$(element||window),isWindow=$.isWindow(withinElement[0]);return {element:withinElement,isWindow:isWindow,offset:withinElement.offset()||{left:0,top:0},scrollLeft:withinElement.scrollLeft(),scrollTop:withinElement.scrollTop(),width:isWindow?withinElement.width():withinElement.outerWidth(),height:isWindow?withinElement.height():withinElement.outerHeight()};}};$.fn.position=function(options){if(!options||!options.of){return _position.apply(this,arguments);} // make a copy, we don't want to modify arguments
	options=$.extend({},options);var atOffset,targetWidth,targetHeight,targetOffset,basePosition,dimensions,target=$(options.of),within=$.position.getWithinInfo(options.within),scrollInfo=$.position.getScrollInfo(within),collision=(options.collision||"flip").split(" "),offsets={};dimensions=getDimensions(target);if(target[0].preventDefault){ // force left top to allow flipping
	options.at="left top";}targetWidth=dimensions.width;targetHeight=dimensions.height;targetOffset=dimensions.offset; // clone to reuse original targetOffset later
	basePosition=$.extend({},targetOffset); // force my and at to have valid horizontal and vertical positions
	// if a value is missing or invalid, it will be converted to center
	$.each(["my","at"],function(){var pos=(options[this]||"").split(" "),horizontalOffset,verticalOffset;if(pos.length===1){pos=rhorizontal.test(pos[0])?pos.concat(["center"]):rvertical.test(pos[0])?["center"].concat(pos):["center","center"];}pos[0]=rhorizontal.test(pos[0])?pos[0]:"center";pos[1]=rvertical.test(pos[1])?pos[1]:"center"; // calculate offsets
	horizontalOffset=roffset.exec(pos[0]);verticalOffset=roffset.exec(pos[1]);offsets[this]=[horizontalOffset?horizontalOffset[0]:0,verticalOffset?verticalOffset[0]:0]; // reduce to just the positions without the offsets
	options[this]=[rposition.exec(pos[0])[0],rposition.exec(pos[1])[0]];}); // normalize collision option
	if(collision.length===1){collision[1]=collision[0];}if(options.at[0]==="right"){basePosition.left+=targetWidth;}else if(options.at[0]==="center"){basePosition.left+=targetWidth/2;}if(options.at[1]==="bottom"){basePosition.top+=targetHeight;}else if(options.at[1]==="center"){basePosition.top+=targetHeight/2;}atOffset=getOffsets(offsets.at,targetWidth,targetHeight);basePosition.left+=atOffset[0];basePosition.top+=atOffset[1];return this.each(function(){var collisionPosition,using,elem=$(this),elemWidth=elem.outerWidth(),elemHeight=elem.outerHeight(),marginLeft=parseCss(this,"marginLeft"),marginTop=parseCss(this,"marginTop"),collisionWidth=elemWidth+marginLeft+parseCss(this,"marginRight")+scrollInfo.width,collisionHeight=elemHeight+marginTop+parseCss(this,"marginBottom")+scrollInfo.height,position=$.extend({},basePosition),myOffset=getOffsets(offsets.my,elem.outerWidth(),elem.outerHeight());if(options.my[0]==="right"){position.left-=elemWidth;}else if(options.my[0]==="center"){position.left-=elemWidth/2;}if(options.my[1]==="bottom"){position.top-=elemHeight;}else if(options.my[1]==="center"){position.top-=elemHeight/2;}position.left+=myOffset[0];position.top+=myOffset[1]; // if the browser doesn't support fractions, then round for consistent results
	if(!$.support.offsetFractions){position.left=round(position.left);position.top=round(position.top);}collisionPosition={marginLeft:marginLeft,marginTop:marginTop};$.each(["left","top"],function(i,dir){if($.ui.position[collision[i]]){$.ui.position[collision[i]][dir](position,{targetWidth:targetWidth,targetHeight:targetHeight,elemWidth:elemWidth,elemHeight:elemHeight,collisionPosition:collisionPosition,collisionWidth:collisionWidth,collisionHeight:collisionHeight,offset:[atOffset[0]+myOffset[0],atOffset[1]+myOffset[1]],my:options.my,at:options.at,within:within,elem:elem});}});if(options.using){ // adds feedback as second argument to using callback, if present
	using=function using(props){var left=targetOffset.left-position.left,right=left+targetWidth-elemWidth,top=targetOffset.top-position.top,bottom=top+targetHeight-elemHeight,feedback={target:{element:target,left:targetOffset.left,top:targetOffset.top,width:targetWidth,height:targetHeight},element:{element:elem,left:position.left,top:position.top,width:elemWidth,height:elemHeight},horizontal:right<0?"left":left>0?"right":"center",vertical:bottom<0?"top":top>0?"bottom":"middle"};if(targetWidth<elemWidth&&abs(left+right)<targetWidth){feedback.horizontal="center";}if(targetHeight<elemHeight&&abs(top+bottom)<targetHeight){feedback.vertical="middle";}if(max(abs(left),abs(right))>max(abs(top),abs(bottom))){feedback.important="horizontal";}else {feedback.important="vertical";}options.using.call(this,props,feedback);};}elem.offset($.extend(position,{using:using}));});};$.ui.position={fit:{left:function left(position,data){var within=data.within,withinOffset=within.isWindow?within.scrollLeft:within.offset.left,outerWidth=within.width,collisionPosLeft=position.left-data.collisionPosition.marginLeft,overLeft=withinOffset-collisionPosLeft,overRight=collisionPosLeft+data.collisionWidth-outerWidth-withinOffset,newOverRight; // element is wider than within
	if(data.collisionWidth>outerWidth){ // element is initially over the left side of within
	if(overLeft>0&&overRight<=0){newOverRight=position.left+overLeft+data.collisionWidth-outerWidth-withinOffset;position.left+=overLeft-newOverRight; // element is initially over right side of within
	}else if(overRight>0&&overLeft<=0){position.left=withinOffset; // element is initially over both left and right sides of within
	}else {if(overLeft>overRight){position.left=withinOffset+outerWidth-data.collisionWidth;}else {position.left=withinOffset;}} // too far left -> align with left edge
	}else if(overLeft>0){position.left+=overLeft; // too far right -> align with right edge
	}else if(overRight>0){position.left-=overRight; // adjust based on position and margin
	}else {position.left=max(position.left-collisionPosLeft,position.left);}},top:function top(position,data){var within=data.within,withinOffset=within.isWindow?within.scrollTop:within.offset.top,outerHeight=data.within.height,collisionPosTop=position.top-data.collisionPosition.marginTop,overTop=withinOffset-collisionPosTop,overBottom=collisionPosTop+data.collisionHeight-outerHeight-withinOffset,newOverBottom; // element is taller than within
	if(data.collisionHeight>outerHeight){ // element is initially over the top of within
	if(overTop>0&&overBottom<=0){newOverBottom=position.top+overTop+data.collisionHeight-outerHeight-withinOffset;position.top+=overTop-newOverBottom; // element is initially over bottom of within
	}else if(overBottom>0&&overTop<=0){position.top=withinOffset; // element is initially over both top and bottom of within
	}else {if(overTop>overBottom){position.top=withinOffset+outerHeight-data.collisionHeight;}else {position.top=withinOffset;}} // too far up -> align with top
	}else if(overTop>0){position.top+=overTop; // too far down -> align with bottom edge
	}else if(overBottom>0){position.top-=overBottom; // adjust based on position and margin
	}else {position.top=max(position.top-collisionPosTop,position.top);}}},flip:{left:function left(position,data){var within=data.within,withinOffset=within.offset.left+within.scrollLeft,outerWidth=within.width,offsetLeft=within.isWindow?within.scrollLeft:within.offset.left,collisionPosLeft=position.left-data.collisionPosition.marginLeft,overLeft=collisionPosLeft-offsetLeft,overRight=collisionPosLeft+data.collisionWidth-outerWidth-offsetLeft,myOffset=data.my[0]==="left"?-data.elemWidth:data.my[0]==="right"?data.elemWidth:0,atOffset=data.at[0]==="left"?data.targetWidth:data.at[0]==="right"?-data.targetWidth:0,offset=-2*data.offset[0],newOverRight,newOverLeft;if(overLeft<0){newOverRight=position.left+myOffset+atOffset+offset+data.collisionWidth-outerWidth-withinOffset;if(newOverRight<0||newOverRight<abs(overLeft)){position.left+=myOffset+atOffset+offset;}}else if(overRight>0){newOverLeft=position.left-data.collisionPosition.marginLeft+myOffset+atOffset+offset-offsetLeft;if(newOverLeft>0||abs(newOverLeft)<overRight){position.left+=myOffset+atOffset+offset;}}},top:function top(position,data){var within=data.within,withinOffset=within.offset.top+within.scrollTop,outerHeight=within.height,offsetTop=within.isWindow?within.scrollTop:within.offset.top,collisionPosTop=position.top-data.collisionPosition.marginTop,overTop=collisionPosTop-offsetTop,overBottom=collisionPosTop+data.collisionHeight-outerHeight-offsetTop,top=data.my[1]==="top",myOffset=top?-data.elemHeight:data.my[1]==="bottom"?data.elemHeight:0,atOffset=data.at[1]==="top"?data.targetHeight:data.at[1]==="bottom"?-data.targetHeight:0,offset=-2*data.offset[1],newOverTop,newOverBottom;if(overTop<0){newOverBottom=position.top+myOffset+atOffset+offset+data.collisionHeight-outerHeight-withinOffset;if(position.top+myOffset+atOffset+offset>overTop&&(newOverBottom<0||newOverBottom<abs(overTop))){position.top+=myOffset+atOffset+offset;}}else if(overBottom>0){newOverTop=position.top-data.collisionPosition.marginTop+myOffset+atOffset+offset-offsetTop;if(position.top+myOffset+atOffset+offset>overBottom&&(newOverTop>0||abs(newOverTop)<overBottom)){position.top+=myOffset+atOffset+offset;}}}},flipfit:{left:function left(){$.ui.position.flip.left.apply(this,arguments);$.ui.position.fit.left.apply(this,arguments);},top:function top(){$.ui.position.flip.top.apply(this,arguments);$.ui.position.fit.top.apply(this,arguments);}}}; // fraction support test
	(function(){var testElement,testElementParent,testElementStyle,offsetLeft,i,body=document.getElementsByTagName("body")[0],div=document.createElement("div"); //Create a "fake body" for testing based on method used in jQuery.support
	testElement=document.createElement(body?"div":"body");testElementStyle={visibility:"hidden",width:0,height:0,border:0,margin:0,background:"none"};if(body){$.extend(testElementStyle,{position:"absolute",left:"-1000px",top:"-1000px"});}for(i in testElementStyle){testElement.style[i]=testElementStyle[i];}testElement.appendChild(div);testElementParent=body||document.documentElement;testElementParent.insertBefore(testElement,testElementParent.firstChild);div.style.cssText="position: absolute; left: 10.7432222px;";offsetLeft=$(div).offset().left;$.support.offsetFractions=offsetLeft>10&&offsetLeft<11;testElement.innerHTML="";testElementParent.removeChild(testElement);})();})(jQuery);(function($,undefined){$.widget("ui.progressbar",{version:"1.10.3",options:{max:100,value:0,change:null,complete:null},min:0,_create:function _create(){ // Constrain initial value
	this.oldValue=this.options.value=this._constrainedValue();this.element.addClass("ui-progressbar ui-widget ui-widget-content ui-corner-all").attr({ // Only set static values, aria-valuenow and aria-valuemax are
	// set inside _refreshValue()
	role:"progressbar","aria-valuemin":this.min});this.valueDiv=$("<div class='ui-progressbar-value ui-widget-header ui-corner-left'></div>").appendTo(this.element);this._refreshValue();},_destroy:function _destroy(){this.element.removeClass("ui-progressbar ui-widget ui-widget-content ui-corner-all").removeAttr("role").removeAttr("aria-valuemin").removeAttr("aria-valuemax").removeAttr("aria-valuenow");this.valueDiv.remove();},value:function value(newValue){if(newValue===undefined){return this.options.value;}this.options.value=this._constrainedValue(newValue);this._refreshValue();},_constrainedValue:function _constrainedValue(newValue){if(newValue===undefined){newValue=this.options.value;}this.indeterminate=newValue===false; // sanitize value
	if(typeof newValue!=="number"){newValue=0;}return this.indeterminate?false:Math.min(this.options.max,Math.max(this.min,newValue));},_setOptions:function _setOptions(options){ // Ensure "value" option is set after other values (like max)
	var value=options.value;delete options.value;this._super(options);this.options.value=this._constrainedValue(value);this._refreshValue();},_setOption:function _setOption(key,value){if(key==="max"){ // Don't allow a max less than min
	value=Math.max(this.min,value);}this._super(key,value);},_percentage:function _percentage(){return this.indeterminate?100:100*(this.options.value-this.min)/(this.options.max-this.min);},_refreshValue:function _refreshValue(){var value=this.options.value,percentage=this._percentage();this.valueDiv.toggle(this.indeterminate||value>this.min).toggleClass("ui-corner-right",value===this.options.max).width(percentage.toFixed(0)+"%");this.element.toggleClass("ui-progressbar-indeterminate",this.indeterminate);if(this.indeterminate){this.element.removeAttr("aria-valuenow");if(!this.overlayDiv){this.overlayDiv=$("<div class='ui-progressbar-overlay'></div>").appendTo(this.valueDiv);}}else {this.element.attr({"aria-valuemax":this.options.max,"aria-valuenow":value});if(this.overlayDiv){this.overlayDiv.remove();this.overlayDiv=null;}}if(this.oldValue!==value){this.oldValue=value;this._trigger("change");}if(value===this.options.max){this._trigger("complete");}}});})(jQuery);(function($,undefined){ // number of pages in a slider
	// (how many times can you page up/down to go through the whole range)
	var numPages=5;$.widget("ui.slider",$.ui.mouse,{version:"1.10.3",widgetEventPrefix:"slide",options:{animate:false,distance:0,max:100,min:0,orientation:"horizontal",range:false,step:1,value:0,values:null, // callbacks
	change:null,slide:null,start:null,stop:null},_create:function _create(){this._keySliding=false;this._mouseSliding=false;this._animateOff=true;this._handleIndex=null;this._detectOrientation();this._mouseInit();this.element.addClass("ui-slider"+" ui-slider-"+this.orientation+" ui-widget"+" ui-widget-content"+" ui-corner-all");this._refresh();this._setOption("disabled",this.options.disabled);this._animateOff=false;},_refresh:function _refresh(){this._createRange();this._createHandles();this._setupEvents();this._refreshValue();},_createHandles:function _createHandles(){var i,handleCount,options=this.options,existingHandles=this.element.find(".ui-slider-handle").addClass("ui-state-default ui-corner-all"),handle="<a class='ui-slider-handle ui-state-default ui-corner-all' href='#'></a>",handles=[];handleCount=options.values&&options.values.length||1;if(existingHandles.length>handleCount){existingHandles.slice(handleCount).remove();existingHandles=existingHandles.slice(0,handleCount);}for(i=existingHandles.length;i<handleCount;i++){handles.push(handle);}this.handles=existingHandles.add($(handles.join("")).appendTo(this.element));this.handle=this.handles.eq(0);this.handles.each(function(i){$(this).data("ui-slider-handle-index",i);});},_createRange:function _createRange(){var options=this.options,classes="";if(options.range){if(options.range===true){if(!options.values){options.values=[this._valueMin(),this._valueMin()];}else if(options.values.length&&options.values.length!==2){options.values=[options.values[0],options.values[0]];}else if($.isArray(options.values)){options.values=options.values.slice(0);}}if(!this.range||!this.range.length){this.range=$("<div></div>").appendTo(this.element);classes="ui-slider-range"+ // note: this isn't the most fittingly semantic framework class for this element,
	// but worked best visually with a variety of themes
	" ui-widget-header ui-corner-all";}else {this.range.removeClass("ui-slider-range-min ui-slider-range-max") // Handle range switching from true to min/max
	.css({"left":"","bottom":""});}this.range.addClass(classes+(options.range==="min"||options.range==="max"?" ui-slider-range-"+options.range:""));}else {this.range=$([]);}},_setupEvents:function _setupEvents(){var elements=this.handles.add(this.range).filter("a");this._off(elements);this._on(elements,this._handleEvents);this._hoverable(elements);this._focusable(elements);},_destroy:function _destroy(){this.handles.remove();this.range.remove();this.element.removeClass("ui-slider"+" ui-slider-horizontal"+" ui-slider-vertical"+" ui-widget"+" ui-widget-content"+" ui-corner-all");this._mouseDestroy();},_mouseCapture:function _mouseCapture(event){var position,normValue,distance,closestHandle,index,allowed,offset,mouseOverHandle,that=this,o=this.options;if(o.disabled){return false;}this.elementSize={width:this.element.outerWidth(),height:this.element.outerHeight()};this.elementOffset=this.element.offset();position={x:event.pageX,y:event.pageY};normValue=this._normValueFromMouse(position);distance=this._valueMax()-this._valueMin()+1;this.handles.each(function(i){var thisDistance=Math.abs(normValue-that.values(i));if(distance>thisDistance||distance===thisDistance&&(i===that._lastChangedValue||that.values(i)===o.min)){distance=thisDistance;closestHandle=$(this);index=i;}});allowed=this._start(event,index);if(allowed===false){return false;}this._mouseSliding=true;this._handleIndex=index;closestHandle.addClass("ui-state-active").focus();offset=closestHandle.offset();mouseOverHandle=!$(event.target).parents().addBack().is(".ui-slider-handle");this._clickOffset=mouseOverHandle?{left:0,top:0}:{left:event.pageX-offset.left-closestHandle.width()/2,top:event.pageY-offset.top-closestHandle.height()/2-(parseInt(closestHandle.css("borderTopWidth"),10)||0)-(parseInt(closestHandle.css("borderBottomWidth"),10)||0)+(parseInt(closestHandle.css("marginTop"),10)||0)};if(!this.handles.hasClass("ui-state-hover")){this._slide(event,index,normValue);}this._animateOff=true;return true;},_mouseStart:function _mouseStart(){return true;},_mouseDrag:function _mouseDrag(event){var position={x:event.pageX,y:event.pageY},normValue=this._normValueFromMouse(position);this._slide(event,this._handleIndex,normValue);return false;},_mouseStop:function _mouseStop(event){this.handles.removeClass("ui-state-active");this._mouseSliding=false;this._stop(event,this._handleIndex);this._change(event,this._handleIndex);this._handleIndex=null;this._clickOffset=null;this._animateOff=false;return false;},_detectOrientation:function _detectOrientation(){this.orientation=this.options.orientation==="vertical"?"vertical":"horizontal";},_normValueFromMouse:function _normValueFromMouse(position){var pixelTotal,pixelMouse,percentMouse,valueTotal,valueMouse;if(this.orientation==="horizontal"){pixelTotal=this.elementSize.width;pixelMouse=position.x-this.elementOffset.left-(this._clickOffset?this._clickOffset.left:0);}else {pixelTotal=this.elementSize.height;pixelMouse=position.y-this.elementOffset.top-(this._clickOffset?this._clickOffset.top:0);}percentMouse=pixelMouse/pixelTotal;if(percentMouse>1){percentMouse=1;}if(percentMouse<0){percentMouse=0;}if(this.orientation==="vertical"){percentMouse=1-percentMouse;}valueTotal=this._valueMax()-this._valueMin();valueMouse=this._valueMin()+percentMouse*valueTotal;return this._trimAlignValue(valueMouse);},_start:function _start(event,index){var uiHash={handle:this.handles[index],value:this.value()};if(this.options.values&&this.options.values.length){uiHash.value=this.values(index);uiHash.values=this.values();}return this._trigger("start",event,uiHash);},_slide:function _slide(event,index,newVal){var otherVal,newValues,allowed;if(this.options.values&&this.options.values.length){otherVal=this.values(index?0:1);if(this.options.values.length===2&&this.options.range===true&&(index===0&&newVal>otherVal||index===1&&newVal<otherVal)){newVal=otherVal;}if(newVal!==this.values(index)){newValues=this.values();newValues[index]=newVal; // A slide can be canceled by returning false from the slide callback
	allowed=this._trigger("slide",event,{handle:this.handles[index],value:newVal,values:newValues});otherVal=this.values(index?0:1);if(allowed!==false){this.values(index,newVal,true);}}}else {if(newVal!==this.value()){ // A slide can be canceled by returning false from the slide callback
	allowed=this._trigger("slide",event,{handle:this.handles[index],value:newVal});if(allowed!==false){this.value(newVal);}}}},_stop:function _stop(event,index){var uiHash={handle:this.handles[index],value:this.value()};if(this.options.values&&this.options.values.length){uiHash.value=this.values(index);uiHash.values=this.values();}this._trigger("stop",event,uiHash);},_change:function _change(event,index){if(!this._keySliding&&!this._mouseSliding){var uiHash={handle:this.handles[index],value:this.value()};if(this.options.values&&this.options.values.length){uiHash.value=this.values(index);uiHash.values=this.values();} //store the last changed value index for reference when handles overlap
	this._lastChangedValue=index;this._trigger("change",event,uiHash);}},value:function value(newValue){if(arguments.length){this.options.value=this._trimAlignValue(newValue);this._refreshValue();this._change(null,0);return;}return this._value();},values:function values(index,newValue){var vals,newValues,i;if(arguments.length>1){this.options.values[index]=this._trimAlignValue(newValue);this._refreshValue();this._change(null,index);return;}if(arguments.length){if($.isArray(arguments[0])){vals=this.options.values;newValues=arguments[0];for(i=0;i<vals.length;i+=1){vals[i]=this._trimAlignValue(newValues[i]);this._change(null,i);}this._refreshValue();}else {if(this.options.values&&this.options.values.length){return this._values(index);}else {return this.value();}}}else {return this._values();}},_setOption:function _setOption(key,value){var i,valsLength=0;if(key==="range"&&this.options.range===true){if(value==="min"){this.options.value=this._values(0);this.options.values=null;}else if(value==="max"){this.options.value=this._values(this.options.values.length-1);this.options.values=null;}}if($.isArray(this.options.values)){valsLength=this.options.values.length;}$.Widget.prototype._setOption.apply(this,arguments);switch(key){case "orientation":this._detectOrientation();this.element.removeClass("ui-slider-horizontal ui-slider-vertical").addClass("ui-slider-"+this.orientation);this._refreshValue();break;case "value":this._animateOff=true;this._refreshValue();this._change(null,0);this._animateOff=false;break;case "values":this._animateOff=true;this._refreshValue();for(i=0;i<valsLength;i+=1){this._change(null,i);}this._animateOff=false;break;case "min":case "max":this._animateOff=true;this._refreshValue();this._animateOff=false;break;case "range":this._animateOff=true;this._refresh();this._animateOff=false;break;}}, //internal value getter
	// _value() returns value trimmed by min and max, aligned by step
	_value:function _value(){var val=this.options.value;val=this._trimAlignValue(val);return val;}, //internal values getter
	// _values() returns array of values trimmed by min and max, aligned by step
	// _values( index ) returns single value trimmed by min and max, aligned by step
	_values:function _values(index){var val,vals,i;if(arguments.length){val=this.options.values[index];val=this._trimAlignValue(val);return val;}else if(this.options.values&&this.options.values.length){ // .slice() creates a copy of the array
	// this copy gets trimmed by min and max and then returned
	vals=this.options.values.slice();for(i=0;i<vals.length;i+=1){vals[i]=this._trimAlignValue(vals[i]);}return vals;}else {return [];}}, // returns the step-aligned value that val is closest to, between (inclusive) min and max
	_trimAlignValue:function _trimAlignValue(val){if(val<=this._valueMin()){return this._valueMin();}if(val>=this._valueMax()){return this._valueMax();}var step=this.options.step>0?this.options.step:1,valModStep=(val-this._valueMin())%step,alignValue=val-valModStep;if(Math.abs(valModStep)*2>=step){alignValue+=valModStep>0?step:-step;} // Since JavaScript has problems with large floats, round
	// the final value to 5 digits after the decimal point (see #4124)
	return parseFloat(alignValue.toFixed(5));},_valueMin:function _valueMin(){return this.options.min;},_valueMax:function _valueMax(){return this.options.max;},_refreshValue:function _refreshValue(){var lastValPercent,valPercent,value,valueMin,valueMax,oRange=this.options.range,o=this.options,that=this,animate=!this._animateOff?o.animate:false,_set={};if(this.options.values&&this.options.values.length){this.handles.each(function(i){valPercent=(that.values(i)-that._valueMin())/(that._valueMax()-that._valueMin())*100;_set[that.orientation==="horizontal"?"left":"bottom"]=valPercent+"%";$(this).stop(1,1)[animate?"animate":"css"](_set,o.animate);if(that.options.range===true){if(that.orientation==="horizontal"){if(i===0){that.range.stop(1,1)[animate?"animate":"css"]({left:valPercent+"%"},o.animate);}if(i===1){that.range[animate?"animate":"css"]({width:valPercent-lastValPercent+"%"},{queue:false,duration:o.animate});}}else {if(i===0){that.range.stop(1,1)[animate?"animate":"css"]({bottom:valPercent+"%"},o.animate);}if(i===1){that.range[animate?"animate":"css"]({height:valPercent-lastValPercent+"%"},{queue:false,duration:o.animate});}}}lastValPercent=valPercent;});}else {value=this.value();valueMin=this._valueMin();valueMax=this._valueMax();valPercent=valueMax!==valueMin?(value-valueMin)/(valueMax-valueMin)*100:0;_set[this.orientation==="horizontal"?"left":"bottom"]=valPercent+"%";this.handle.stop(1,1)[animate?"animate":"css"](_set,o.animate);if(oRange==="min"&&this.orientation==="horizontal"){this.range.stop(1,1)[animate?"animate":"css"]({width:valPercent+"%"},o.animate);}if(oRange==="max"&&this.orientation==="horizontal"){this.range[animate?"animate":"css"]({width:100-valPercent+"%"},{queue:false,duration:o.animate});}if(oRange==="min"&&this.orientation==="vertical"){this.range.stop(1,1)[animate?"animate":"css"]({height:valPercent+"%"},o.animate);}if(oRange==="max"&&this.orientation==="vertical"){this.range[animate?"animate":"css"]({height:100-valPercent+"%"},{queue:false,duration:o.animate});}}},_handleEvents:{keydown:function keydown(event){ /*jshint maxcomplexity:25*/var allowed,curVal,newVal,step,index=$(event.target).data("ui-slider-handle-index");switch(event.keyCode){case $.ui.keyCode.HOME:case $.ui.keyCode.END:case $.ui.keyCode.PAGE_UP:case $.ui.keyCode.PAGE_DOWN:case $.ui.keyCode.UP:case $.ui.keyCode.RIGHT:case $.ui.keyCode.DOWN:case $.ui.keyCode.LEFT:event.preventDefault();if(!this._keySliding){this._keySliding=true;$(event.target).addClass("ui-state-active");allowed=this._start(event,index);if(allowed===false){return;}}break;}step=this.options.step;if(this.options.values&&this.options.values.length){curVal=newVal=this.values(index);}else {curVal=newVal=this.value();}switch(event.keyCode){case $.ui.keyCode.HOME:newVal=this._valueMin();break;case $.ui.keyCode.END:newVal=this._valueMax();break;case $.ui.keyCode.PAGE_UP:newVal=this._trimAlignValue(curVal+(this._valueMax()-this._valueMin())/numPages);break;case $.ui.keyCode.PAGE_DOWN:newVal=this._trimAlignValue(curVal-(this._valueMax()-this._valueMin())/numPages);break;case $.ui.keyCode.UP:case $.ui.keyCode.RIGHT:if(curVal===this._valueMax()){return;}newVal=this._trimAlignValue(curVal+step);break;case $.ui.keyCode.DOWN:case $.ui.keyCode.LEFT:if(curVal===this._valueMin()){return;}newVal=this._trimAlignValue(curVal-step);break;}this._slide(event,index,newVal);},click:function click(event){event.preventDefault();},keyup:function keyup(event){var index=$(event.target).data("ui-slider-handle-index");if(this._keySliding){this._keySliding=false;this._stop(event,index);this._change(event,index);$(event.target).removeClass("ui-state-active");}}}});})(jQuery);(function($){function modifier(fn){return function(){var previous=this.element.val();fn.apply(this,arguments);this._refresh();if(previous!==this.element.val()){this._trigger("change");}};}$.widget("ui.spinner",{version:"1.10.3",defaultElement:"<input>",widgetEventPrefix:"spin",options:{culture:null,icons:{down:"ui-icon-triangle-1-s",up:"ui-icon-triangle-1-n"},incremental:true,max:null,min:null,numberFormat:null,page:10,step:1,change:null,spin:null,start:null,stop:null},_create:function _create(){ // handle string values that need to be parsed
	this._setOption("max",this.options.max);this._setOption("min",this.options.min);this._setOption("step",this.options.step); // format the value, but don't constrain
	this._value(this.element.val(),true);this._draw();this._on(this._events);this._refresh(); // turning off autocomplete prevents the browser from remembering the
	// value when navigating through history, so we re-enable autocomplete
	// if the page is unloaded before the widget is destroyed. #7790
	this._on(this.window,{beforeunload:function beforeunload(){this.element.removeAttr("autocomplete");}});},_getCreateOptions:function _getCreateOptions(){var options={},element=this.element;$.each(["min","max","step"],function(i,option){var value=element.attr(option);if(value!==undefined&&value.length){options[option]=value;}});return options;},_events:{keydown:function keydown(event){if(this._start(event)&&this._keydown(event)){event.preventDefault();}},keyup:"_stop",focus:function focus(){this.previous=this.element.val();},blur:function blur(event){if(this.cancelBlur){delete this.cancelBlur;return;}this._stop();this._refresh();if(this.previous!==this.element.val()){this._trigger("change",event);}},mousewheel:function mousewheel(event,delta){if(!delta){return;}if(!this.spinning&&!this._start(event)){return false;}this._spin((delta>0?1:-1)*this.options.step,event);clearTimeout(this.mousewheelTimer);this.mousewheelTimer=this._delay(function(){if(this.spinning){this._stop(event);}},100);event.preventDefault();},"mousedown .ui-spinner-button":function mousedownUiSpinnerButton(event){var previous; // We never want the buttons to have focus; whenever the user is
	// interacting with the spinner, the focus should be on the input.
	// If the input is focused then this.previous is properly set from
	// when the input first received focus. If the input is not focused
	// then we need to set this.previous based on the value before spinning.
	previous=this.element[0]===this.document[0].activeElement?this.previous:this.element.val();function checkFocus(){var isActive=this.element[0]===this.document[0].activeElement;if(!isActive){this.element.focus();this.previous=previous; // support: IE
	// IE sets focus asynchronously, so we need to check if focus
	// moved off of the input because the user clicked on the button.
	this._delay(function(){this.previous=previous;});}} // ensure focus is on (or stays on) the text field
	event.preventDefault();checkFocus.call(this); // support: IE
	// IE doesn't prevent moving focus even with event.preventDefault()
	// so we set a flag to know when we should ignore the blur event
	// and check (again) if focus moved off of the input.
	this.cancelBlur=true;this._delay(function(){delete this.cancelBlur;checkFocus.call(this);});if(this._start(event)===false){return;}this._repeat(null,$(event.currentTarget).hasClass("ui-spinner-up")?1:-1,event);},"mouseup .ui-spinner-button":"_stop","mouseenter .ui-spinner-button":function mouseenterUiSpinnerButton(event){ // button will add ui-state-active if mouse was down while mouseleave and kept down
	if(!$(event.currentTarget).hasClass("ui-state-active")){return;}if(this._start(event)===false){return false;}this._repeat(null,$(event.currentTarget).hasClass("ui-spinner-up")?1:-1,event);}, // TODO: do we really want to consider this a stop?
	// shouldn't we just stop the repeater and wait until mouseup before
	// we trigger the stop event?
	"mouseleave .ui-spinner-button":"_stop"},_draw:function _draw(){var uiSpinner=this.uiSpinner=this.element.addClass("ui-spinner-input").attr("autocomplete","off").wrap(this._uiSpinnerHtml()).parent() // add buttons
	.append(this._buttonHtml());this.element.attr("role","spinbutton"); // button bindings
	this.buttons=uiSpinner.find(".ui-spinner-button").attr("tabIndex",-1).button().removeClass("ui-corner-all"); // IE 6 doesn't understand height: 50% for the buttons
	// unless the wrapper has an explicit height
	if(this.buttons.height()>Math.ceil(uiSpinner.height()*0.5)&&uiSpinner.height()>0){uiSpinner.height(uiSpinner.height());} // disable spinner if element was already disabled
	if(this.options.disabled){this.disable();}},_keydown:function _keydown(event){var options=this.options,keyCode=$.ui.keyCode;switch(event.keyCode){case keyCode.UP:this._repeat(null,1,event);return true;case keyCode.DOWN:this._repeat(null,-1,event);return true;case keyCode.PAGE_UP:this._repeat(null,options.page,event);return true;case keyCode.PAGE_DOWN:this._repeat(null,-options.page,event);return true;}return false;},_uiSpinnerHtml:function _uiSpinnerHtml(){return "<span class='ui-spinner ui-widget ui-widget-content ui-corner-all'></span>";},_buttonHtml:function _buttonHtml(){return ""+"<a class='ui-spinner-button ui-spinner-up ui-corner-tr'>"+"<span class='ui-icon "+this.options.icons.up+"'>&#9650;</span>"+"</a>"+"<a class='ui-spinner-button ui-spinner-down ui-corner-br'>"+"<span class='ui-icon "+this.options.icons.down+"'>&#9660;</span>"+"</a>";},_start:function _start(event){if(!this.spinning&&this._trigger("start",event)===false){return false;}if(!this.counter){this.counter=1;}this.spinning=true;return true;},_repeat:function _repeat(i,steps,event){i=i||500;clearTimeout(this.timer);this.timer=this._delay(function(){this._repeat(40,steps,event);},i);this._spin(steps*this.options.step,event);},_spin:function _spin(step,event){var value=this.value()||0;if(!this.counter){this.counter=1;}value=this._adjustValue(value+step*this._increment(this.counter));if(!this.spinning||this._trigger("spin",event,{value:value})!==false){this._value(value);this.counter++;}},_increment:function _increment(i){var incremental=this.options.incremental;if(incremental){return $.isFunction(incremental)?incremental(i):Math.floor(i*i*i/50000-i*i/500+17*i/200+1);}return 1;},_precision:function _precision(){var precision=this._precisionOf(this.options.step);if(this.options.min!==null){precision=Math.max(precision,this._precisionOf(this.options.min));}return precision;},_precisionOf:function _precisionOf(num){var str=num.toString(),decimal=str.indexOf(".");return decimal===-1?0:str.length-decimal-1;},_adjustValue:function _adjustValue(value){var base,aboveMin,options=this.options; // make sure we're at a valid step
	// - find out where we are relative to the base (min or 0)
	base=options.min!==null?options.min:0;aboveMin=value-base; // - round to the nearest step
	aboveMin=Math.round(aboveMin/options.step)*options.step; // - rounding is based on 0, so adjust back to our base
	value=base+aboveMin; // fix precision from bad JS floating point math
	value=parseFloat(value.toFixed(this._precision())); // clamp the value
	if(options.max!==null&&value>options.max){return options.max;}if(options.min!==null&&value<options.min){return options.min;}return value;},_stop:function _stop(event){if(!this.spinning){return;}clearTimeout(this.timer);clearTimeout(this.mousewheelTimer);this.counter=0;this.spinning=false;this._trigger("stop",event);},_setOption:function _setOption(key,value){if(key==="culture"||key==="numberFormat"){var prevValue=this._parse(this.element.val());this.options[key]=value;this.element.val(this._format(prevValue));return;}if(key==="max"||key==="min"||key==="step"){if(typeof value==="string"){value=this._parse(value);}}if(key==="icons"){this.buttons.first().find(".ui-icon").removeClass(this.options.icons.up).addClass(value.up);this.buttons.last().find(".ui-icon").removeClass(this.options.icons.down).addClass(value.down);}this._super(key,value);if(key==="disabled"){if(value){this.element.prop("disabled",true);this.buttons.button("disable");}else {this.element.prop("disabled",false);this.buttons.button("enable");}}},_setOptions:modifier(function(options){this._super(options);this._value(this.element.val());}),_parse:function _parse(val){if(typeof val==="string"&&val!==""){val=window.Globalize&&this.options.numberFormat?Globalize.parseFloat(val,10,this.options.culture):+val;}return val===""||isNaN(val)?null:val;},_format:function _format(value){if(value===""){return "";}return window.Globalize&&this.options.numberFormat?Globalize.format(value,this.options.numberFormat,this.options.culture):value;},_refresh:function _refresh(){this.element.attr({"aria-valuemin":this.options.min,"aria-valuemax":this.options.max, // TODO: what should we do with values that can't be parsed?
	"aria-valuenow":this._parse(this.element.val())});}, // update the value without triggering change
	_value:function _value(value,allowAny){var parsed;if(value!==""){parsed=this._parse(value);if(parsed!==null){if(!allowAny){parsed=this._adjustValue(parsed);}value=this._format(parsed);}}this.element.val(value);this._refresh();},_destroy:function _destroy(){this.element.removeClass("ui-spinner-input").prop("disabled",false).removeAttr("autocomplete").removeAttr("role").removeAttr("aria-valuemin").removeAttr("aria-valuemax").removeAttr("aria-valuenow");this.uiSpinner.replaceWith(this.element);},stepUp:modifier(function(steps){this._stepUp(steps);}),_stepUp:function _stepUp(steps){if(this._start()){this._spin((steps||1)*this.options.step);this._stop();}},stepDown:modifier(function(steps){this._stepDown(steps);}),_stepDown:function _stepDown(steps){if(this._start()){this._spin((steps||1)*-this.options.step);this._stop();}},pageUp:modifier(function(pages){this._stepUp((pages||1)*this.options.page);}),pageDown:modifier(function(pages){this._stepDown((pages||1)*this.options.page);}),value:function value(newVal){if(!arguments.length){return this._parse(this.element.val());}modifier(this._value).call(this,newVal);},widget:function widget(){return this.uiSpinner;}});})(jQuery);(function($,undefined){var tabId=0,rhash=/#.*$/;function getNextTabId(){return ++tabId;}function isLocal(anchor){return anchor.hash.length>1&&decodeURIComponent(anchor.href.replace(rhash,""))===decodeURIComponent(location.href.replace(rhash,""));}$.widget("ui.tabs",{version:"1.10.3",delay:300,options:{active:null,collapsible:false,event:"click",heightStyle:"content",hide:null,show:null, // callbacks
	activate:null,beforeActivate:null,beforeLoad:null,load:null},_create:function _create(){var that=this,options=this.options;this.running=false;this.element.addClass("ui-tabs ui-widget ui-widget-content ui-corner-all").toggleClass("ui-tabs-collapsible",options.collapsible) // Prevent users from focusing disabled tabs via click
	.delegate(".ui-tabs-nav > li","mousedown"+this.eventNamespace,function(event){if($(this).is(".ui-state-disabled")){event.preventDefault();}}) // support: IE <9
	// Preventing the default action in mousedown doesn't prevent IE
	// from focusing the element, so if the anchor gets focused, blur.
	// We don't have to worry about focusing the previously focused
	// element since clicking on a non-focusable element should focus
	// the body anyway.
	.delegate(".ui-tabs-anchor","focus"+this.eventNamespace,function(){if($(this).closest("li").is(".ui-state-disabled")){this.blur();}});this._processTabs();options.active=this._initialActive(); // Take disabling tabs via class attribute from HTML
	// into account and update option properly.
	if($.isArray(options.disabled)){options.disabled=$.unique(options.disabled.concat($.map(this.tabs.filter(".ui-state-disabled"),function(li){return that.tabs.index(li);}))).sort();} // check for length avoids error when initializing empty list
	if(this.options.active!==false&&this.anchors.length){this.active=this._findActive(options.active);}else {this.active=$();}this._refresh();if(this.active.length){this.load(options.active);}},_initialActive:function _initialActive(){var active=this.options.active,collapsible=this.options.collapsible,locationHash=location.hash.substring(1);if(active===null){ // check the fragment identifier in the URL
	if(locationHash){this.tabs.each(function(i,tab){if($(tab).attr("aria-controls")===locationHash){active=i;return false;}});} // check for a tab marked active via a class
	if(active===null){active=this.tabs.index(this.tabs.filter(".ui-tabs-active"));} // no active tab, set to false
	if(active===null||active===-1){active=this.tabs.length?0:false;}} // handle numbers: negative, out of range
	if(active!==false){active=this.tabs.index(this.tabs.eq(active));if(active===-1){active=collapsible?false:0;}} // don't allow collapsible: false and active: false
	if(!collapsible&&active===false&&this.anchors.length){active=0;}return active;},_getCreateEventData:function _getCreateEventData(){return {tab:this.active,panel:!this.active.length?$():this._getPanelForTab(this.active)};},_tabKeydown:function _tabKeydown(event){ /*jshint maxcomplexity:15*/var focusedTab=$(this.document[0].activeElement).closest("li"),selectedIndex=this.tabs.index(focusedTab),goingForward=true;if(this._handlePageNav(event)){return;}switch(event.keyCode){case $.ui.keyCode.RIGHT:case $.ui.keyCode.DOWN:selectedIndex++;break;case $.ui.keyCode.UP:case $.ui.keyCode.LEFT:goingForward=false;selectedIndex--;break;case $.ui.keyCode.END:selectedIndex=this.anchors.length-1;break;case $.ui.keyCode.HOME:selectedIndex=0;break;case $.ui.keyCode.SPACE: // Activate only, no collapsing
	event.preventDefault();clearTimeout(this.activating);this._activate(selectedIndex);return;case $.ui.keyCode.ENTER: // Toggle (cancel delayed activation, allow collapsing)
	event.preventDefault();clearTimeout(this.activating); // Determine if we should collapse or activate
	this._activate(selectedIndex===this.options.active?false:selectedIndex);return;default:return;} // Focus the appropriate tab, based on which key was pressed
	event.preventDefault();clearTimeout(this.activating);selectedIndex=this._focusNextTab(selectedIndex,goingForward); // Navigating with control key will prevent automatic activation
	if(!event.ctrlKey){ // Update aria-selected immediately so that AT think the tab is already selected.
	// Otherwise AT may confuse the user by stating that they need to activate the tab,
	// but the tab will already be activated by the time the announcement finishes.
	focusedTab.attr("aria-selected","false");this.tabs.eq(selectedIndex).attr("aria-selected","true");this.activating=this._delay(function(){this.option("active",selectedIndex);},this.delay);}},_panelKeydown:function _panelKeydown(event){if(this._handlePageNav(event)){return;} // Ctrl+up moves focus to the current tab
	if(event.ctrlKey&&event.keyCode===$.ui.keyCode.UP){event.preventDefault();this.active.focus();}}, // Alt+page up/down moves focus to the previous/next tab (and activates)
	_handlePageNav:function _handlePageNav(event){if(event.altKey&&event.keyCode===$.ui.keyCode.PAGE_UP){this._activate(this._focusNextTab(this.options.active-1,false));return true;}if(event.altKey&&event.keyCode===$.ui.keyCode.PAGE_DOWN){this._activate(this._focusNextTab(this.options.active+1,true));return true;}},_findNextTab:function _findNextTab(index,goingForward){var lastTabIndex=this.tabs.length-1;function constrain(){if(index>lastTabIndex){index=0;}if(index<0){index=lastTabIndex;}return index;}while($.inArray(constrain(),this.options.disabled)!==-1){index=goingForward?index+1:index-1;}return index;},_focusNextTab:function _focusNextTab(index,goingForward){index=this._findNextTab(index,goingForward);this.tabs.eq(index).focus();return index;},_setOption:function _setOption(key,value){if(key==="active"){ // _activate() will handle invalid values and update this.options
	this._activate(value);return;}if(key==="disabled"){ // don't use the widget factory's disabled handling
	this._setupDisabled(value);return;}this._super(key,value);if(key==="collapsible"){this.element.toggleClass("ui-tabs-collapsible",value); // Setting collapsible: false while collapsed; open first panel
	if(!value&&this.options.active===false){this._activate(0);}}if(key==="event"){this._setupEvents(value);}if(key==="heightStyle"){this._setupHeightStyle(value);}},_tabId:function _tabId(tab){return tab.attr("aria-controls")||"ui-tabs-"+getNextTabId();},_sanitizeSelector:function _sanitizeSelector(hash){return hash?hash.replace(/[!"$%&'()*+,.\/:;<=>?@\[\]\^`{|}~]/g,"\\$&"):"";},refresh:function refresh(){var options=this.options,lis=this.tablist.children(":has(a[href])"); // get disabled tabs from class attribute from HTML
	// this will get converted to a boolean if needed in _refresh()
	options.disabled=$.map(lis.filter(".ui-state-disabled"),function(tab){return lis.index(tab);});this._processTabs(); // was collapsed or no tabs
	if(options.active===false||!this.anchors.length){options.active=false;this.active=$(); // was active, but active tab is gone
	}else if(this.active.length&&!$.contains(this.tablist[0],this.active[0])){ // all remaining tabs are disabled
	if(this.tabs.length===options.disabled.length){options.active=false;this.active=$(); // activate previous tab
	}else {this._activate(this._findNextTab(Math.max(0,options.active-1),false));} // was active, active tab still exists
	}else { // make sure active index is correct
	options.active=this.tabs.index(this.active);}this._refresh();},_refresh:function _refresh(){this._setupDisabled(this.options.disabled);this._setupEvents(this.options.event);this._setupHeightStyle(this.options.heightStyle);this.tabs.not(this.active).attr({"aria-selected":"false",tabIndex:-1});this.panels.not(this._getPanelForTab(this.active)).hide().attr({"aria-expanded":"false","aria-hidden":"true"}); // Make sure one tab is in the tab order
	if(!this.active.length){this.tabs.eq(0).attr("tabIndex",0);}else {this.active.addClass("ui-tabs-active ui-state-active").attr({"aria-selected":"true",tabIndex:0});this._getPanelForTab(this.active).show().attr({"aria-expanded":"true","aria-hidden":"false"});}},_processTabs:function _processTabs(){var that=this;this.tablist=this._getList().addClass("ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all").attr("role","tablist");this.tabs=this.tablist.find("> li:has(a[href])").addClass("ui-state-default ui-corner-top").attr({role:"tab",tabIndex:-1});this.anchors=this.tabs.map(function(){return $("a",this)[0];}).addClass("ui-tabs-anchor").attr({role:"presentation",tabIndex:-1});this.panels=$();this.anchors.each(function(i,anchor){var selector,panel,panelId,anchorId=$(anchor).uniqueId().attr("id"),tab=$(anchor).closest("li"),originalAriaControls=tab.attr("aria-controls"); // inline tab
	if(isLocal(anchor)){selector=anchor.hash;panel=that.element.find(that._sanitizeSelector(selector)); // remote tab
	}else {panelId=that._tabId(tab);selector="#"+panelId;panel=that.element.find(selector);if(!panel.length){panel=that._createPanel(panelId);panel.insertAfter(that.panels[i-1]||that.tablist);}panel.attr("aria-live","polite");}if(panel.length){that.panels=that.panels.add(panel);}if(originalAriaControls){tab.data("ui-tabs-aria-controls",originalAriaControls);}tab.attr({"aria-controls":selector.substring(1),"aria-labelledby":anchorId});panel.attr("aria-labelledby",anchorId);});this.panels.addClass("ui-tabs-panel ui-widget-content ui-corner-bottom").attr("role","tabpanel");}, // allow overriding how to find the list for rare usage scenarios (#7715)
	_getList:function _getList(){return this.element.find("ol,ul").eq(0);},_createPanel:function _createPanel(id){return $("<div>").attr("id",id).addClass("ui-tabs-panel ui-widget-content ui-corner-bottom").data("ui-tabs-destroy",true);},_setupDisabled:function _setupDisabled(disabled){if($.isArray(disabled)){if(!disabled.length){disabled=false;}else if(disabled.length===this.anchors.length){disabled=true;}} // disable tabs
	for(var i=0,li;li=this.tabs[i];i++){if(disabled===true||$.inArray(i,disabled)!==-1){$(li).addClass("ui-state-disabled").attr("aria-disabled","true");}else {$(li).removeClass("ui-state-disabled").removeAttr("aria-disabled");}}this.options.disabled=disabled;},_setupEvents:function _setupEvents(event){var events={click:function click(event){event.preventDefault();}};if(event){$.each(event.split(" "),function(index,eventName){events[eventName]="_eventHandler";});}this._off(this.anchors.add(this.tabs).add(this.panels));this._on(this.anchors,events);this._on(this.tabs,{keydown:"_tabKeydown"});this._on(this.panels,{keydown:"_panelKeydown"});this._focusable(this.tabs);this._hoverable(this.tabs);},_setupHeightStyle:function _setupHeightStyle(heightStyle){var maxHeight,parent=this.element.parent();if(heightStyle==="fill"){maxHeight=parent.height();maxHeight-=this.element.outerHeight()-this.element.height();this.element.siblings(":visible").each(function(){var elem=$(this),position=elem.css("position");if(position==="absolute"||position==="fixed"){return;}maxHeight-=elem.outerHeight(true);});this.element.children().not(this.panels).each(function(){maxHeight-=$(this).outerHeight(true);});this.panels.each(function(){$(this).height(Math.max(0,maxHeight-$(this).innerHeight()+$(this).height()));}).css("overflow","auto");}else if(heightStyle==="auto"){maxHeight=0;this.panels.each(function(){maxHeight=Math.max(maxHeight,$(this).height("").height());}).height(maxHeight);}},_eventHandler:function _eventHandler(event){var options=this.options,active=this.active,anchor=$(event.currentTarget),tab=anchor.closest("li"),clickedIsActive=tab[0]===active[0],collapsing=clickedIsActive&&options.collapsible,toShow=collapsing?$():this._getPanelForTab(tab),toHide=!active.length?$():this._getPanelForTab(active),eventData={oldTab:active,oldPanel:toHide,newTab:collapsing?$():tab,newPanel:toShow};event.preventDefault();if(tab.hasClass("ui-state-disabled")|| // tab is already loading
	tab.hasClass("ui-tabs-loading")|| // can't switch durning an animation
	this.running|| // click on active header, but not collapsible
	clickedIsActive&&!options.collapsible|| // allow canceling activation
	this._trigger("beforeActivate",event,eventData)===false){return;}options.active=collapsing?false:this.tabs.index(tab);this.active=clickedIsActive?$():tab;if(this.xhr){this.xhr.abort();}if(!toHide.length&&!toShow.length){$.error("jQuery UI Tabs: Mismatching fragment identifier.");}if(toShow.length){this.load(this.tabs.index(tab),event);}this._toggle(event,eventData);}, // handles show/hide for selecting tabs
	_toggle:function _toggle(event,eventData){var that=this,toShow=eventData.newPanel,toHide=eventData.oldPanel;this.running=true;function complete(){that.running=false;that._trigger("activate",event,eventData);}function show(){eventData.newTab.closest("li").addClass("ui-tabs-active ui-state-active");if(toShow.length&&that.options.show){that._show(toShow,that.options.show,complete);}else {toShow.show();complete();}} // start out by hiding, then showing, then completing
	if(toHide.length&&this.options.hide){this._hide(toHide,this.options.hide,function(){eventData.oldTab.closest("li").removeClass("ui-tabs-active ui-state-active");show();});}else {eventData.oldTab.closest("li").removeClass("ui-tabs-active ui-state-active");toHide.hide();show();}toHide.attr({"aria-expanded":"false","aria-hidden":"true"});eventData.oldTab.attr("aria-selected","false"); // If we're switching tabs, remove the old tab from the tab order.
	// If we're opening from collapsed state, remove the previous tab from the tab order.
	// If we're collapsing, then keep the collapsing tab in the tab order.
	if(toShow.length&&toHide.length){eventData.oldTab.attr("tabIndex",-1);}else if(toShow.length){this.tabs.filter(function(){return $(this).attr("tabIndex")===0;}).attr("tabIndex",-1);}toShow.attr({"aria-expanded":"true","aria-hidden":"false"});eventData.newTab.attr({"aria-selected":"true",tabIndex:0});},_activate:function _activate(index){var anchor,active=this._findActive(index); // trying to activate the already active panel
	if(active[0]===this.active[0]){return;} // trying to collapse, simulate a click on the current active header
	if(!active.length){active=this.active;}anchor=active.find(".ui-tabs-anchor")[0];this._eventHandler({target:anchor,currentTarget:anchor,preventDefault:$.noop});},_findActive:function _findActive(index){return index===false?$():this.tabs.eq(index);},_getIndex:function _getIndex(index){ // meta-function to give users option to provide a href string instead of a numerical index.
	if(typeof index==="string"){index=this.anchors.index(this.anchors.filter("[href$='"+index+"']"));}return index;},_destroy:function _destroy(){if(this.xhr){this.xhr.abort();}this.element.removeClass("ui-tabs ui-widget ui-widget-content ui-corner-all ui-tabs-collapsible");this.tablist.removeClass("ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all").removeAttr("role");this.anchors.removeClass("ui-tabs-anchor").removeAttr("role").removeAttr("tabIndex").removeUniqueId();this.tabs.add(this.panels).each(function(){if($.data(this,"ui-tabs-destroy")){$(this).remove();}else {$(this).removeClass("ui-state-default ui-state-active ui-state-disabled "+"ui-corner-top ui-corner-bottom ui-widget-content ui-tabs-active ui-tabs-panel").removeAttr("tabIndex").removeAttr("aria-live").removeAttr("aria-busy").removeAttr("aria-selected").removeAttr("aria-labelledby").removeAttr("aria-hidden").removeAttr("aria-expanded").removeAttr("role");}});this.tabs.each(function(){var li=$(this),prev=li.data("ui-tabs-aria-controls");if(prev){li.attr("aria-controls",prev).removeData("ui-tabs-aria-controls");}else {li.removeAttr("aria-controls");}});this.panels.show();if(this.options.heightStyle!=="content"){this.panels.css("height","");}},enable:function enable(index){var disabled=this.options.disabled;if(disabled===false){return;}if(index===undefined){disabled=false;}else {index=this._getIndex(index);if($.isArray(disabled)){disabled=$.map(disabled,function(num){return num!==index?num:null;});}else {disabled=$.map(this.tabs,function(li,num){return num!==index?num:null;});}}this._setupDisabled(disabled);},disable:function disable(index){var disabled=this.options.disabled;if(disabled===true){return;}if(index===undefined){disabled=true;}else {index=this._getIndex(index);if($.inArray(index,disabled)!==-1){return;}if($.isArray(disabled)){disabled=$.merge([index],disabled).sort();}else {disabled=[index];}}this._setupDisabled(disabled);},load:function load(index,event){index=this._getIndex(index);var that=this,tab=this.tabs.eq(index),anchor=tab.find(".ui-tabs-anchor"),panel=this._getPanelForTab(tab),eventData={tab:tab,panel:panel}; // not remote
	if(isLocal(anchor[0])){return;}this.xhr=$.ajax(this._ajaxSettings(anchor,event,eventData)); // support: jQuery <1.8
	// jQuery <1.8 returns false if the request is canceled in beforeSend,
	// but as of 1.8, $.ajax() always returns a jqXHR object.
	if(this.xhr&&this.xhr.statusText!=="canceled"){tab.addClass("ui-tabs-loading");panel.attr("aria-busy","true");this.xhr.success(function(response){ // support: jQuery <1.8
	// http://bugs.jquery.com/ticket/11778
	setTimeout(function(){panel.html(response);that._trigger("load",event,eventData);},1);}).complete(function(jqXHR,status){ // support: jQuery <1.8
	// http://bugs.jquery.com/ticket/11778
	setTimeout(function(){if(status==="abort"){that.panels.stop(false,true);}tab.removeClass("ui-tabs-loading");panel.removeAttr("aria-busy");if(jqXHR===that.xhr){delete that.xhr;}},1);});}},_ajaxSettings:function _ajaxSettings(anchor,event,eventData){var that=this;return {url:anchor.attr("href"),beforeSend:function beforeSend(jqXHR,settings){return that._trigger("beforeLoad",event,$.extend({jqXHR:jqXHR,ajaxSettings:settings},eventData));}};},_getPanelForTab:function _getPanelForTab(tab){var id=$(tab).attr("aria-controls");return this.element.find(this._sanitizeSelector("#"+id));}});})(jQuery);(function($){var increments=0;function addDescribedBy(elem,id){var describedby=(elem.attr("aria-describedby")||"").split(/\s+/);describedby.push(id);elem.data("ui-tooltip-id",id).attr("aria-describedby",$.trim(describedby.join(" ")));}function removeDescribedBy(elem){var id=elem.data("ui-tooltip-id"),describedby=(elem.attr("aria-describedby")||"").split(/\s+/),index=$.inArray(id,describedby);if(index!==-1){describedby.splice(index,1);}elem.removeData("ui-tooltip-id");describedby=$.trim(describedby.join(" "));if(describedby){elem.attr("aria-describedby",describedby);}else {elem.removeAttr("aria-describedby");}}$.widget("ui.tooltip",{version:"1.10.3",options:{content:function content(){ // support: IE<9, Opera in jQuery <1.7
	// .text() can't accept undefined, so coerce to a string
	var title=$(this).attr("title")||""; // Escape title, since we're going from an attribute to raw HTML
	return $("<a>").text(title).html();},hide:true, // Disabled elements have inconsistent behavior across browsers (#8661)
	items:"[title]:not([disabled])",position:{my:"left top+15",at:"left bottom",collision:"flipfit flip"},show:true,tooltipClass:null,track:false, // callbacks
	close:null,open:null},_create:function _create(){this._on({mouseover:"open",focusin:"open"}); // IDs of generated tooltips, needed for destroy
	this.tooltips={}; // IDs of parent tooltips where we removed the title attribute
	this.parents={};if(this.options.disabled){this._disable();}},_setOption:function _setOption(key,value){var that=this;if(key==="disabled"){this[value?"_disable":"_enable"]();this.options[key]=value; // disable element style changes
	return;}this._super(key,value);if(key==="content"){$.each(this.tooltips,function(id,element){that._updateContent(element);});}},_disable:function _disable(){var that=this; // close open tooltips
	$.each(this.tooltips,function(id,element){var event=$.Event("blur");event.target=event.currentTarget=element[0];that.close(event,true);}); // remove title attributes to prevent native tooltips
	this.element.find(this.options.items).addBack().each(function(){var element=$(this);if(element.is("[title]")){element.data("ui-tooltip-title",element.attr("title")).attr("title","");}});},_enable:function _enable(){ // restore title attributes
	this.element.find(this.options.items).addBack().each(function(){var element=$(this);if(element.data("ui-tooltip-title")){element.attr("title",element.data("ui-tooltip-title"));}});},open:function open(event){var that=this,target=$(event?event.target:this.element) // we need closest here due to mouseover bubbling,
	// but always pointing at the same event target
	.closest(this.options.items); // No element to show a tooltip for or the tooltip is already open
	if(!target.length||target.data("ui-tooltip-id")){return;}if(target.attr("title")){target.data("ui-tooltip-title",target.attr("title"));}target.data("ui-tooltip-open",true); // kill parent tooltips, custom or native, for hover
	if(event&&event.type==="mouseover"){target.parents().each(function(){var parent=$(this),blurEvent;if(parent.data("ui-tooltip-open")){blurEvent=$.Event("blur");blurEvent.target=blurEvent.currentTarget=this;that.close(blurEvent,true);}if(parent.attr("title")){parent.uniqueId();that.parents[this.id]={element:this,title:parent.attr("title")};parent.attr("title","");}});}this._updateContent(target,event);},_updateContent:function _updateContent(target,event){var content,contentOption=this.options.content,that=this,eventType=event?event.type:null;if(typeof contentOption==="string"){return this._open(event,target,contentOption);}content=contentOption.call(target[0],function(response){ // ignore async response if tooltip was closed already
	if(!target.data("ui-tooltip-open")){return;} // IE may instantly serve a cached response for ajax requests
	// delay this call to _open so the other call to _open runs first
	that._delay(function(){ // jQuery creates a special event for focusin when it doesn't
	// exist natively. To improve performance, the native event
	// object is reused and the type is changed. Therefore, we can't
	// rely on the type being correct after the event finished
	// bubbling, so we set it back to the previous value. (#8740)
	if(event){event.type=eventType;}this._open(event,target,response);});});if(content){this._open(event,target,content);}},_open:function _open(event,target,content){var tooltip,events,delayedShow,positionOption=$.extend({},this.options.position);if(!content){return;} // Content can be updated multiple times. If the tooltip already
	// exists, then just update the content and bail.
	tooltip=this._find(target);if(tooltip.length){tooltip.find(".ui-tooltip-content").html(content);return;} // if we have a title, clear it to prevent the native tooltip
	// we have to check first to avoid defining a title if none exists
	// (we don't want to cause an element to start matching [title])
	//
	// We use removeAttr only for key events, to allow IE to export the correct
	// accessible attributes. For mouse events, set to empty string to avoid
	// native tooltip showing up (happens only when removing inside mouseover).
	if(target.is("[title]")){if(event&&event.type==="mouseover"){target.attr("title","");}else {target.removeAttr("title");}}tooltip=this._tooltip(target);addDescribedBy(target,tooltip.attr("id"));tooltip.find(".ui-tooltip-content").html(content);function position(event){positionOption.of=event;if(tooltip.is(":hidden")){return;}tooltip.position(positionOption);}if(this.options.track&&event&&/^mouse/.test(event.type)){this._on(this.document,{mousemove:position}); // trigger once to override element-relative positioning
	position(event);}else {tooltip.position($.extend({of:target},this.options.position));}tooltip.hide();this._show(tooltip,this.options.show); // Handle tracking tooltips that are shown with a delay (#8644). As soon
	// as the tooltip is visible, position the tooltip using the most recent
	// event.
	if(this.options.show&&this.options.show.delay){delayedShow=this.delayedShow=setInterval(function(){if(tooltip.is(":visible")){position(positionOption.of);clearInterval(delayedShow);}},$.fx.interval);}this._trigger("open",event,{tooltip:tooltip});events={keyup:function keyup(event){if(event.keyCode===$.ui.keyCode.ESCAPE){var fakeEvent=$.Event(event);fakeEvent.currentTarget=target[0];this.close(fakeEvent,true);}},remove:function remove(){this._removeTooltip(tooltip);}};if(!event||event.type==="mouseover"){events.mouseleave="close";}if(!event||event.type==="focusin"){events.focusout="close";}this._on(true,target,events);},close:function close(event){var that=this,target=$(event?event.currentTarget:this.element),tooltip=this._find(target); // disabling closes the tooltip, so we need to track when we're closing
	// to avoid an infinite loop in case the tooltip becomes disabled on close
	if(this.closing){return;} // Clear the interval for delayed tracking tooltips
	clearInterval(this.delayedShow); // only set title if we had one before (see comment in _open())
	if(target.data("ui-tooltip-title")){target.attr("title",target.data("ui-tooltip-title"));}removeDescribedBy(target);tooltip.stop(true);this._hide(tooltip,this.options.hide,function(){that._removeTooltip($(this));});target.removeData("ui-tooltip-open");this._off(target,"mouseleave focusout keyup"); // Remove 'remove' binding only on delegated targets
	if(target[0]!==this.element[0]){this._off(target,"remove");}this._off(this.document,"mousemove");if(event&&event.type==="mouseleave"){$.each(this.parents,function(id,parent){$(parent.element).attr("title",parent.title);delete that.parents[id];});}this.closing=true;this._trigger("close",event,{tooltip:tooltip});this.closing=false;},_tooltip:function _tooltip(element){var id="ui-tooltip-"+increments++,tooltip=$("<div>").attr({id:id,role:"tooltip"}).addClass("ui-tooltip ui-widget ui-corner-all ui-widget-content "+(this.options.tooltipClass||""));$("<div>").addClass("ui-tooltip-content").appendTo(tooltip);tooltip.appendTo(this.document[0].body);this.tooltips[id]=element;return tooltip;},_find:function _find(target){var id=target.data("ui-tooltip-id");return id?$("#"+id):$();},_removeTooltip:function _removeTooltip(tooltip){tooltip.remove();delete this.tooltips[tooltip.attr("id")];},_destroy:function _destroy(){var that=this; // close open tooltips
	$.each(this.tooltips,function(id,element){ // Delegate to close method to handle common cleanup
	var event=$.Event("blur");event.target=event.currentTarget=element[0];that.close(event,true); // Remove immediately; destroying an open tooltip doesn't use the
	// hide animation
	$("#"+id).remove(); // Restore the title
	if(element.data("ui-tooltip-title")){element.attr("title",element.data("ui-tooltip-title"));element.removeData("ui-tooltip-title");}});}});})(jQuery);

/***/ },
/* 7 */
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function(global) {module.exports = global["Tether"] = __webpack_require__(8);
	/* WEBPACK VAR INJECTION */}.call(exports, (function() { return this; }())))

/***/ },
/* 8 */
/***/ function(module, exports, __webpack_require__) {

	var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_RESULT__;'use strict';

	var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol ? "symbol" : typeof obj; };

	/*! tether 1.1.0 */

	(function (root, factory) {
	  if (true) {
	    !(__WEBPACK_AMD_DEFINE_FACTORY__ = (factory), __WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ? (__WEBPACK_AMD_DEFINE_FACTORY__.call(exports, __webpack_require__, exports, module)) : __WEBPACK_AMD_DEFINE_FACTORY__), __WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
	  } else if ((typeof exports === 'undefined' ? 'undefined' : _typeof(exports)) === 'object') {
	    module.exports = factory(require, exports, module);
	  } else {
	    root.Tether = factory();
	  }
	})(undefined, function (require, exports, module) {

	  'use strict';

	  var _createClass = function () {
	    function defineProperties(target, props) {
	      for (var i = 0; i < props.length; i++) {
	        var descriptor = props[i];descriptor.enumerable = descriptor.enumerable || false;descriptor.configurable = true;if ('value' in descriptor) descriptor.writable = true;Object.defineProperty(target, descriptor.key, descriptor);
	      }
	    }return function (Constructor, protoProps, staticProps) {
	      if (protoProps) defineProperties(Constructor.prototype, protoProps);if (staticProps) defineProperties(Constructor, staticProps);return Constructor;
	    };
	  }();

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

	  var uniqueId = function () {
	    var id = 0;
	    return function () {
	      return ++id;
	    };
	  }();

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
	          if ({}.hasOwnProperty.call(obj, key)) {
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

	  var Evented = function () {
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
	  }();

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

	  var _slicedToArray = function () {
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
	  }();

	  var _createClass = function () {
	    function defineProperties(target, props) {
	      for (var i = 0; i < props.length; i++) {
	        var descriptor = props[i];descriptor.enumerable = descriptor.enumerable || false;descriptor.configurable = true;if ('value' in descriptor) descriptor.writable = true;Object.defineProperty(target, descriptor.key, descriptor);
	      }
	    }return function (Constructor, protoProps, staticProps) {
	      if (protoProps) defineProperties(Constructor.prototype, protoProps);if (staticProps) defineProperties(Constructor, staticProps);return Constructor;
	    };
	  }();

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

	  var transformKey = function () {
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
	  }();

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

	  var TetherClass = function () {
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
	          } else if (typeof ret === 'undefined' || (typeof ret === 'undefined' ? 'undefined' : _typeof(ret)) !== 'object') {
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
	  }();

	  TetherClass.modules = [];

	  TetherBase.position = position;

	  var Tether = extend(TetherClass, TetherBase);
	  /* globals TetherBase */

	  'use strict';

	  var _slicedToArray = function () {
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
	  }();

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

	  var _slicedToArray = function () {
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
	  }();

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
/* 9 */
/***/ function(module, exports) {

	"use strict";

	var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol ? "symbol" : typeof obj; };

	/*!
	 * Bootstrap v4.0.0-alpha.2 (http://getbootstrap.com)
	 * Copyright 2011-2015 Twitter, Inc.
	 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
	 */
	if ("undefined" == typeof jQuery) throw new Error("Bootstrap's JavaScript requires jQuery");+function (a) {
	  var b = a.fn.jquery.split(" ")[0].split(".");if (b[0] < 2 && b[1] < 9 || 1 == b[0] && 9 == b[1] && b[2] < 1 || b[0] >= 3) throw new Error("Bootstrap's JavaScript requires at least jQuery v1.9.1 but less than v3.0.0");
	}(jQuery), +function (a) {
	  "use strict";
	  function b(a, b) {
	    if ("function" != typeof b && null !== b) throw new TypeError("Super expression must either be null or a function, not " + (typeof b === "undefined" ? "undefined" : _typeof(b)));a.prototype = Object.create(b && b.prototype, { constructor: { value: a, enumerable: !1, writable: !0, configurable: !0 } }), b && (Object.setPrototypeOf ? Object.setPrototypeOf(a, b) : a.__proto__ = b);
	  }function c(a, b) {
	    if (!(a instanceof b)) throw new TypeError("Cannot call a class as a function");
	  }var d = function d(a, b, c) {
	    for (var d = !0; d;) {
	      var e = a,
	          f = b,
	          g = c;d = !1, null === e && (e = Function.prototype);var h = Object.getOwnPropertyDescriptor(e, f);if (void 0 !== h) {
	        if ("value" in h) return h.value;var i = h.get;if (void 0 === i) return;return i.call(g);
	      }var j = Object.getPrototypeOf(e);if (null === j) return;a = j, b = f, c = g, d = !0, h = j = void 0;
	    }
	  },
	      e = function () {
	    function a(a, b) {
	      for (var c = 0; c < b.length; c++) {
	        var d = b[c];d.enumerable = d.enumerable || !1, d.configurable = !0, "value" in d && (d.writable = !0), Object.defineProperty(a, d.key, d);
	      }
	    }return function (b, c, d) {
	      return c && a(b.prototype, c), d && a(b, d), b;
	    };
	  }(),
	      f = function (a) {
	    function b(a) {
	      return {}.toString.call(a).match(/\s([a-zA-Z]+)/)[1].toLowerCase();
	    }function c(a) {
	      return (a[0] || a).nodeType;
	    }function d() {
	      return { bindType: h.end, delegateType: h.end, handle: function handle(b) {
	          return a(b.target).is(this) ? b.handleObj.handler.apply(this, arguments) : void 0;
	        } };
	    }function e() {
	      if (window.QUnit) return !1;var a = document.createElement("bootstrap");for (var b in i) {
	        if (void 0 !== a.style[b]) return { end: i[b] };
	      }return !1;
	    }function f(b) {
	      var c = this,
	          d = !1;return a(this).one(j.TRANSITION_END, function () {
	        d = !0;
	      }), setTimeout(function () {
	        d || j.triggerTransitionEnd(c);
	      }, b), this;
	    }function g() {
	      h = e(), a.fn.emulateTransitionEnd = f, j.supportsTransitionEnd() && (a.event.special[j.TRANSITION_END] = d());
	    }var h = !1,
	        i = { WebkitTransition: "webkitTransitionEnd", MozTransition: "transitionend", OTransition: "oTransitionEnd otransitionend", transition: "transitionend" },
	        j = { TRANSITION_END: "bsTransitionEnd", getUID: function getUID(a) {
	        do {
	          a += ~ ~(1e6 * Math.random());
	        } while (document.getElementById(a));return a;
	      }, getSelectorFromElement: function getSelectorFromElement(a) {
	        var b = a.getAttribute("data-target");return b || (b = a.getAttribute("href") || "", b = /^#[a-z]/i.test(b) ? b : null), b;
	      }, reflow: function reflow(a) {
	        new Function("bs", "return bs")(a.offsetHeight);
	      }, triggerTransitionEnd: function triggerTransitionEnd(b) {
	        a(b).trigger(h.end);
	      }, supportsTransitionEnd: function supportsTransitionEnd() {
	        return Boolean(h);
	      }, typeCheckConfig: function typeCheckConfig(a, d, e) {
	        for (var f in e) {
	          if (e.hasOwnProperty(f)) {
	            var g = e[f],
	                h = d[f],
	                i = void 0;if (i = h && c(h) ? "element" : b(h), !new RegExp(g).test(i)) throw new Error(a.toUpperCase() + ": " + ('Option "' + f + '" provided type "' + i + '" ') + ('but expected type "' + g + '".'));
	          }
	        }
	      } };return g(), j;
	  }(jQuery),
	      g = (function (a) {
	    var b = "alert",
	        d = "4.0.0-alpha",
	        g = "bs.alert",
	        h = "." + g,
	        i = ".data-api",
	        j = a.fn[b],
	        k = 150,
	        l = { DISMISS: '[data-dismiss="alert"]' },
	        m = { CLOSE: "close" + h, CLOSED: "closed" + h, CLICK_DATA_API: "click" + h + i },
	        n = { ALERT: "alert", FADE: "fade", IN: "in" },
	        o = function () {
	      function b(a) {
	        c(this, b), this._element = a;
	      }return e(b, [{ key: "close", value: function value(a) {
	          a = a || this._element;var b = this._getRootElement(a),
	              c = this._triggerCloseEvent(b);c.isDefaultPrevented() || this._removeElement(b);
	        } }, { key: "dispose", value: function value() {
	          a.removeData(this._element, g), this._element = null;
	        } }, { key: "_getRootElement", value: function value(b) {
	          var c = f.getSelectorFromElement(b),
	              d = !1;return c && (d = a(c)[0]), d || (d = a(b).closest("." + n.ALERT)[0]), d;
	        } }, { key: "_triggerCloseEvent", value: function value(b) {
	          var c = a.Event(m.CLOSE);return a(b).trigger(c), c;
	        } }, { key: "_removeElement", value: function value(b) {
	          return a(b).removeClass(n.IN), f.supportsTransitionEnd() && a(b).hasClass(n.FADE) ? void a(b).one(f.TRANSITION_END, a.proxy(this._destroyElement, this, b)).emulateTransitionEnd(k) : void this._destroyElement(b);
	        } }, { key: "_destroyElement", value: function value(b) {
	          a(b).detach().trigger(m.CLOSED).remove();
	        } }], [{ key: "_jQueryInterface", value: function value(c) {
	          return this.each(function () {
	            var d = a(this),
	                e = d.data(g);e || (e = new b(this), d.data(g, e)), "close" === c && e[c](this);
	          });
	        } }, { key: "_handleDismiss", value: function value(a) {
	          return function (b) {
	            b && b.preventDefault(), a.close(this);
	          };
	        } }, { key: "VERSION", get: function get() {
	          return d;
	        } }]), b;
	    }();return a(document).on(m.CLICK_DATA_API, l.DISMISS, o._handleDismiss(new o())), a.fn[b] = o._jQueryInterface, a.fn[b].Constructor = o, a.fn[b].noConflict = function () {
	      return a.fn[b] = j, o._jQueryInterface;
	    }, o;
	  }(jQuery), function (a) {
	    var b = "button",
	        d = "4.0.0-alpha",
	        f = "bs.button",
	        g = "." + f,
	        h = ".data-api",
	        i = a.fn[b],
	        j = { ACTIVE: "active", BUTTON: "btn", FOCUS: "focus" },
	        k = { DATA_TOGGLE_CARROT: '[data-toggle^="button"]', DATA_TOGGLE: '[data-toggle="buttons"]', INPUT: "input", ACTIVE: ".active", BUTTON: ".btn" },
	        l = { CLICK_DATA_API: "click" + g + h, FOCUS_BLUR_DATA_API: "focus" + g + h + " " + ("blur" + g + h) },
	        m = function () {
	      function b(a) {
	        c(this, b), this._element = a;
	      }return e(b, [{ key: "toggle", value: function value() {
	          var b = !0,
	              c = a(this._element).closest(k.DATA_TOGGLE)[0];if (c) {
	            var d = a(this._element).find(k.INPUT)[0];if (d) {
	              if ("radio" === d.type) if (d.checked && a(this._element).hasClass(j.ACTIVE)) b = !1;else {
	                var e = a(c).find(k.ACTIVE)[0];e && a(e).removeClass(j.ACTIVE);
	              }b && (d.checked = !a(this._element).hasClass(j.ACTIVE), a(this._element).trigger("change"));
	            }
	          } else this._element.setAttribute("aria-pressed", !a(this._element).hasClass(j.ACTIVE));b && a(this._element).toggleClass(j.ACTIVE);
	        } }, { key: "dispose", value: function value() {
	          a.removeData(this._element, f), this._element = null;
	        } }], [{ key: "_jQueryInterface", value: function value(c) {
	          return this.each(function () {
	            var d = a(this).data(f);d || (d = new b(this), a(this).data(f, d)), "toggle" === c && d[c]();
	          });
	        } }, { key: "VERSION", get: function get() {
	          return d;
	        } }]), b;
	    }();return a(document).on(l.CLICK_DATA_API, k.DATA_TOGGLE_CARROT, function (b) {
	      b.preventDefault();var c = b.target;a(c).hasClass(j.BUTTON) || (c = a(c).closest(k.BUTTON)), m._jQueryInterface.call(a(c), "toggle");
	    }).on(l.FOCUS_BLUR_DATA_API, k.DATA_TOGGLE_CARROT, function (b) {
	      var c = a(b.target).closest(k.BUTTON)[0];a(c).toggleClass(j.FOCUS, /^focus(in)?$/.test(b.type));
	    }), a.fn[b] = m._jQueryInterface, a.fn[b].Constructor = m, a.fn[b].noConflict = function () {
	      return a.fn[b] = i, m._jQueryInterface;
	    }, m;
	  }(jQuery), function (a) {
	    var b = "carousel",
	        d = "4.0.0-alpha",
	        g = "bs.carousel",
	        h = "." + g,
	        i = ".data-api",
	        j = a.fn[b],
	        k = 600,
	        l = { interval: 5e3, keyboard: !0, slide: !1, pause: "hover", wrap: !0 },
	        m = { interval: "(number|boolean)", keyboard: "boolean", slide: "(boolean|string)", pause: "(string|boolean)", wrap: "boolean" },
	        n = { NEXT: "next", PREVIOUS: "prev" },
	        o = { SLIDE: "slide" + h, SLID: "slid" + h, KEYDOWN: "keydown" + h, MOUSEENTER: "mouseenter" + h, MOUSELEAVE: "mouseleave" + h, LOAD_DATA_API: "load" + h + i, CLICK_DATA_API: "click" + h + i },
	        p = { CAROUSEL: "carousel", ACTIVE: "active", SLIDE: "slide", RIGHT: "right", LEFT: "left", ITEM: "carousel-item" },
	        q = { ACTIVE: ".active", ACTIVE_ITEM: ".active.carousel-item", ITEM: ".carousel-item", NEXT_PREV: ".next, .prev", INDICATORS: ".carousel-indicators", DATA_SLIDE: "[data-slide], [data-slide-to]", DATA_RIDE: '[data-ride="carousel"]' },
	        r = function () {
	      function i(b, d) {
	        c(this, i), this._items = null, this._interval = null, this._activeElement = null, this._isPaused = !1, this._isSliding = !1, this._config = this._getConfig(d), this._element = a(b)[0], this._indicatorsElement = a(this._element).find(q.INDICATORS)[0], this._addEventListeners();
	      }return e(i, [{ key: "next", value: function value() {
	          this._isSliding || this._slide(n.NEXT);
	        } }, { key: "nextWhenVisible", value: function value() {
	          document.hidden || this.next();
	        } }, { key: "prev", value: function value() {
	          this._isSliding || this._slide(n.PREVIOUS);
	        } }, { key: "pause", value: function value(b) {
	          b || (this._isPaused = !0), a(this._element).find(q.NEXT_PREV)[0] && f.supportsTransitionEnd() && (f.triggerTransitionEnd(this._element), this.cycle(!0)), clearInterval(this._interval), this._interval = null;
	        } }, { key: "cycle", value: function value(b) {
	          b || (this._isPaused = !1), this._interval && (clearInterval(this._interval), this._interval = null), this._config.interval && !this._isPaused && (this._interval = setInterval(a.proxy(document.visibilityState ? this.nextWhenVisible : this.next, this), this._config.interval));
	        } }, { key: "to", value: function value(b) {
	          var c = this;this._activeElement = a(this._element).find(q.ACTIVE_ITEM)[0];var d = this._getItemIndex(this._activeElement);if (!(b > this._items.length - 1 || 0 > b)) {
	            if (this._isSliding) return void a(this._element).one(o.SLID, function () {
	              return c.to(b);
	            });if (d === b) return this.pause(), void this.cycle();var e = b > d ? n.NEXT : n.PREVIOUS;this._slide(e, this._items[b]);
	          }
	        } }, { key: "dispose", value: function value() {
	          a(this._element).off(h), a.removeData(this._element, g), this._items = null, this._config = null, this._element = null, this._interval = null, this._isPaused = null, this._isSliding = null, this._activeElement = null, this._indicatorsElement = null;
	        } }, { key: "_getConfig", value: function value(c) {
	          return c = a.extend({}, l, c), f.typeCheckConfig(b, c, m), c;
	        } }, { key: "_addEventListeners", value: function value() {
	          this._config.keyboard && a(this._element).on(o.KEYDOWN, a.proxy(this._keydown, this)), "hover" !== this._config.pause || "ontouchstart" in document.documentElement || a(this._element).on(o.MOUSEENTER, a.proxy(this.pause, this)).on(o.MOUSELEAVE, a.proxy(this.cycle, this));
	        } }, { key: "_keydown", value: function value(a) {
	          if (a.preventDefault(), !/input|textarea/i.test(a.target.tagName)) switch (a.which) {case 37:
	              this.prev();break;case 39:
	              this.next();break;default:
	              return;}
	        } }, { key: "_getItemIndex", value: function value(b) {
	          return this._items = a.makeArray(a(b).parent().find(q.ITEM)), this._items.indexOf(b);
	        } }, { key: "_getItemByDirection", value: function value(a, b) {
	          var c = a === n.NEXT,
	              d = a === n.PREVIOUS,
	              e = this._getItemIndex(b),
	              f = this._items.length - 1,
	              g = d && 0 === e || c && e === f;if (g && !this._config.wrap) return b;var h = a === n.PREVIOUS ? -1 : 1,
	              i = (e + h) % this._items.length;return -1 === i ? this._items[this._items.length - 1] : this._items[i];
	        } }, { key: "_triggerSlideEvent", value: function value(b, c) {
	          var d = a.Event(o.SLIDE, { relatedTarget: b, direction: c });return a(this._element).trigger(d), d;
	        } }, { key: "_setActiveIndicatorElement", value: function value(b) {
	          if (this._indicatorsElement) {
	            a(this._indicatorsElement).find(q.ACTIVE).removeClass(p.ACTIVE);var c = this._indicatorsElement.children[this._getItemIndex(b)];c && a(c).addClass(p.ACTIVE);
	          }
	        } }, { key: "_slide", value: function value(b, c) {
	          var d = this,
	              e = a(this._element).find(q.ACTIVE_ITEM)[0],
	              g = c || e && this._getItemByDirection(b, e),
	              h = Boolean(this._interval),
	              i = b === n.NEXT ? p.LEFT : p.RIGHT;if (g && a(g).hasClass(p.ACTIVE)) return void (this._isSliding = !1);var j = this._triggerSlideEvent(g, i);if (!j.isDefaultPrevented() && e && g) {
	            this._isSliding = !0, h && this.pause(), this._setActiveIndicatorElement(g);var l = a.Event(o.SLID, { relatedTarget: g, direction: i });f.supportsTransitionEnd() && a(this._element).hasClass(p.SLIDE) ? (a(g).addClass(b), f.reflow(g), a(e).addClass(i), a(g).addClass(i), a(e).one(f.TRANSITION_END, function () {
	              a(g).removeClass(i).removeClass(b), a(g).addClass(p.ACTIVE), a(e).removeClass(p.ACTIVE).removeClass(b).removeClass(i), d._isSliding = !1, setTimeout(function () {
	                return a(d._element).trigger(l);
	              }, 0);
	            }).emulateTransitionEnd(k)) : (a(e).removeClass(p.ACTIVE), a(g).addClass(p.ACTIVE), this._isSliding = !1, a(this._element).trigger(l)), h && this.cycle();
	          }
	        } }], [{ key: "_jQueryInterface", value: function value(b) {
	          return this.each(function () {
	            var c = a(this).data(g),
	                d = a.extend({}, l, a(this).data());"object" == (typeof b === "undefined" ? "undefined" : _typeof(b)) && a.extend(d, b);var e = "string" == typeof b ? b : d.slide;if (c || (c = new i(this, d), a(this).data(g, c)), "number" == typeof b) c.to(b);else if ("string" == typeof e) {
	              if (void 0 === c[e]) throw new Error('No method named "' + e + '"');c[e]();
	            } else d.interval && (c.pause(), c.cycle());
	          });
	        } }, { key: "_dataApiClickHandler", value: function value(b) {
	          var c = f.getSelectorFromElement(this);if (c) {
	            var d = a(c)[0];if (d && a(d).hasClass(p.CAROUSEL)) {
	              var e = a.extend({}, a(d).data(), a(this).data()),
	                  h = this.getAttribute("data-slide-to");h && (e.interval = !1), i._jQueryInterface.call(a(d), e), h && a(d).data(g).to(h), b.preventDefault();
	            }
	          }
	        } }, { key: "VERSION", get: function get() {
	          return d;
	        } }, { key: "Default", get: function get() {
	          return l;
	        } }]), i;
	    }();return a(document).on(o.CLICK_DATA_API, q.DATA_SLIDE, r._dataApiClickHandler), a(window).on(o.LOAD_DATA_API, function () {
	      a(q.DATA_RIDE).each(function () {
	        var b = a(this);r._jQueryInterface.call(b, b.data());
	      });
	    }), a.fn[b] = r._jQueryInterface, a.fn[b].Constructor = r, a.fn[b].noConflict = function () {
	      return a.fn[b] = j, r._jQueryInterface;
	    }, r;
	  }(jQuery), function (a) {
	    var b = "collapse",
	        d = "4.0.0-alpha",
	        g = "bs.collapse",
	        h = "." + g,
	        i = ".data-api",
	        j = a.fn[b],
	        k = 600,
	        l = { toggle: !0, parent: "" },
	        m = { toggle: "boolean", parent: "string" },
	        n = { SHOW: "show" + h, SHOWN: "shown" + h, HIDE: "hide" + h, HIDDEN: "hidden" + h, CLICK_DATA_API: "click" + h + i },
	        o = { IN: "in", COLLAPSE: "collapse", COLLAPSING: "collapsing", COLLAPSED: "collapsed" },
	        p = { WIDTH: "width", HEIGHT: "height" },
	        q = { ACTIVES: ".panel > .in, .panel > .collapsing", DATA_TOGGLE: '[data-toggle="collapse"]' },
	        r = function () {
	      function h(b, d) {
	        c(this, h), this._isTransitioning = !1, this._element = b, this._config = this._getConfig(d), this._triggerArray = a.makeArray(a('[data-toggle="collapse"][href="#' + b.id + '"],' + ('[data-toggle="collapse"][data-target="#' + b.id + '"]'))), this._parent = this._config.parent ? this._getParent() : null, this._config.parent || this._addAriaAndCollapsedClass(this._element, this._triggerArray), this._config.toggle && this.toggle();
	      }return e(h, [{ key: "toggle", value: function value() {
	          a(this._element).hasClass(o.IN) ? this.hide() : this.show();
	        } }, { key: "show", value: function value() {
	          var b = this;if (!this._isTransitioning && !a(this._element).hasClass(o.IN)) {
	            var c = void 0,
	                d = void 0;if (this._parent && (c = a.makeArray(a(q.ACTIVES)), c.length || (c = null)), !(c && (d = a(c).data(g), d && d._isTransitioning))) {
	              var e = a.Event(n.SHOW);if (a(this._element).trigger(e), !e.isDefaultPrevented()) {
	                c && (h._jQueryInterface.call(a(c), "hide"), d || a(c).data(g, null));var i = this._getDimension();a(this._element).removeClass(o.COLLAPSE).addClass(o.COLLAPSING), this._element.style[i] = 0, this._element.setAttribute("aria-expanded", !0), this._triggerArray.length && a(this._triggerArray).removeClass(o.COLLAPSED).attr("aria-expanded", !0), this.setTransitioning(!0);var j = function j() {
	                  a(b._element).removeClass(o.COLLAPSING).addClass(o.COLLAPSE).addClass(o.IN), b._element.style[i] = "", b.setTransitioning(!1), a(b._element).trigger(n.SHOWN);
	                };if (!f.supportsTransitionEnd()) return void j();var l = i[0].toUpperCase() + i.slice(1),
	                    m = "scroll" + l;a(this._element).one(f.TRANSITION_END, j).emulateTransitionEnd(k), this._element.style[i] = this._element[m] + "px";
	              }
	            }
	          }
	        } }, { key: "hide", value: function value() {
	          var b = this;if (!this._isTransitioning && a(this._element).hasClass(o.IN)) {
	            var c = a.Event(n.HIDE);if (a(this._element).trigger(c), !c.isDefaultPrevented()) {
	              var d = this._getDimension(),
	                  e = d === p.WIDTH ? "offsetWidth" : "offsetHeight";this._element.style[d] = this._element[e] + "px", f.reflow(this._element), a(this._element).addClass(o.COLLAPSING).removeClass(o.COLLAPSE).removeClass(o.IN), this._element.setAttribute("aria-expanded", !1), this._triggerArray.length && a(this._triggerArray).addClass(o.COLLAPSED).attr("aria-expanded", !1), this.setTransitioning(!0);var g = function g() {
	                b.setTransitioning(!1), a(b._element).removeClass(o.COLLAPSING).addClass(o.COLLAPSE).trigger(n.HIDDEN);
	              };return this._element.style[d] = 0, f.supportsTransitionEnd() ? void a(this._element).one(f.TRANSITION_END, g).emulateTransitionEnd(k) : void g();
	            }
	          }
	        } }, { key: "setTransitioning", value: function value(a) {
	          this._isTransitioning = a;
	        } }, { key: "dispose", value: function value() {
	          a.removeData(this._element, g), this._config = null, this._parent = null, this._element = null, this._triggerArray = null, this._isTransitioning = null;
	        } }, { key: "_getConfig", value: function value(c) {
	          return c = a.extend({}, l, c), c.toggle = Boolean(c.toggle), f.typeCheckConfig(b, c, m), c;
	        } }, { key: "_getDimension", value: function value() {
	          var b = a(this._element).hasClass(p.WIDTH);return b ? p.WIDTH : p.HEIGHT;
	        } }, { key: "_getParent", value: function value() {
	          var b = this,
	              c = a(this._config.parent)[0],
	              d = '[data-toggle="collapse"][data-parent="' + this._config.parent + '"]';return a(c).find(d).each(function (a, c) {
	            b._addAriaAndCollapsedClass(h._getTargetFromElement(c), [c]);
	          }), c;
	        } }, { key: "_addAriaAndCollapsedClass", value: function value(b, c) {
	          if (b) {
	            var d = a(b).hasClass(o.IN);b.setAttribute("aria-expanded", d), c.length && a(c).toggleClass(o.COLLAPSED, !d).attr("aria-expanded", d);
	          }
	        } }], [{ key: "_getTargetFromElement", value: function value(b) {
	          var c = f.getSelectorFromElement(b);return c ? a(c)[0] : null;
	        } }, { key: "_jQueryInterface", value: function value(b) {
	          return this.each(function () {
	            var c = a(this),
	                d = c.data(g),
	                e = a.extend({}, l, c.data(), "object" == (typeof b === "undefined" ? "undefined" : _typeof(b)) && b);if (!d && e.toggle && /show|hide/.test(b) && (e.toggle = !1), d || (d = new h(this, e), c.data(g, d)), "string" == typeof b) {
	              if (void 0 === d[b]) throw new Error('No method named "' + b + '"');d[b]();
	            }
	          });
	        } }, { key: "VERSION", get: function get() {
	          return d;
	        } }, { key: "Default", get: function get() {
	          return l;
	        } }]), h;
	    }();return a(document).on(n.CLICK_DATA_API, q.DATA_TOGGLE, function (b) {
	      b.preventDefault();var c = r._getTargetFromElement(this),
	          d = a(c).data(g),
	          e = d ? "toggle" : a(this).data();r._jQueryInterface.call(a(c), e);
	    }), a.fn[b] = r._jQueryInterface, a.fn[b].Constructor = r, a.fn[b].noConflict = function () {
	      return a.fn[b] = j, r._jQueryInterface;
	    }, r;
	  }(jQuery), function (a) {
	    var b = "dropdown",
	        d = "4.0.0-alpha",
	        g = "bs.dropdown",
	        h = "." + g,
	        i = ".data-api",
	        j = a.fn[b],
	        k = { HIDE: "hide" + h, HIDDEN: "hidden" + h, SHOW: "show" + h, SHOWN: "shown" + h, CLICK: "click" + h, CLICK_DATA_API: "click" + h + i, KEYDOWN_DATA_API: "keydown" + h + i },
	        l = { BACKDROP: "dropdown-backdrop", DISABLED: "disabled", OPEN: "open" },
	        m = { BACKDROP: ".dropdown-backdrop", DATA_TOGGLE: '[data-toggle="dropdown"]', FORM_CHILD: ".dropdown form", ROLE_MENU: '[role="menu"]', ROLE_LISTBOX: '[role="listbox"]', NAVBAR_NAV: ".navbar-nav", VISIBLE_ITEMS: '[role="menu"] li:not(.disabled) a, [role="listbox"] li:not(.disabled) a' },
	        n = function () {
	      function b(a) {
	        c(this, b), this._element = a, this._addEventListeners();
	      }return e(b, [{ key: "toggle", value: function value() {
	          if (this.disabled || a(this).hasClass(l.DISABLED)) return !1;var c = b._getParentFromElement(this),
	              d = a(c).hasClass(l.OPEN);if (b._clearMenus(), d) return !1;if ("ontouchstart" in document.documentElement && !a(c).closest(m.NAVBAR_NAV).length) {
	            var e = document.createElement("div");e.className = l.BACKDROP, a(e).insertBefore(this), a(e).on("click", b._clearMenus);
	          }var f = { relatedTarget: this },
	              g = a.Event(k.SHOW, f);return a(c).trigger(g), g.isDefaultPrevented() ? !1 : (this.focus(), this.setAttribute("aria-expanded", "true"), a(c).toggleClass(l.OPEN), a(c).trigger(a.Event(k.SHOWN, f)), !1);
	        } }, { key: "dispose", value: function value() {
	          a.removeData(this._element, g), a(this._element).off(h), this._element = null;
	        } }, { key: "_addEventListeners", value: function value() {
	          a(this._element).on(k.CLICK, this.toggle);
	        } }], [{ key: "_jQueryInterface", value: function value(c) {
	          return this.each(function () {
	            var d = a(this).data(g);if (d || a(this).data(g, d = new b(this)), "string" == typeof c) {
	              if (void 0 === d[c]) throw new Error('No method named "' + c + '"');d[c].call(this);
	            }
	          });
	        } }, { key: "_clearMenus", value: function value(c) {
	          if (!c || 3 !== c.which) {
	            var d = a(m.BACKDROP)[0];d && d.parentNode.removeChild(d);for (var e = a.makeArray(a(m.DATA_TOGGLE)), f = 0; f < e.length; f++) {
	              var g = b._getParentFromElement(e[f]),
	                  h = { relatedTarget: e[f] };if (a(g).hasClass(l.OPEN) && !(c && "click" === c.type && /input|textarea/i.test(c.target.tagName) && a.contains(g, c.target))) {
	                var i = a.Event(k.HIDE, h);a(g).trigger(i), i.isDefaultPrevented() || (e[f].setAttribute("aria-expanded", "false"), a(g).removeClass(l.OPEN).trigger(a.Event(k.HIDDEN, h)));
	              }
	            }
	          }
	        } }, { key: "_getParentFromElement", value: function value(b) {
	          var c = void 0,
	              d = f.getSelectorFromElement(b);return d && (c = a(d)[0]), c || b.parentNode;
	        } }, { key: "_dataApiKeydownHandler", value: function value(c) {
	          if (/(38|40|27|32)/.test(c.which) && !/input|textarea/i.test(c.target.tagName) && (c.preventDefault(), c.stopPropagation(), !this.disabled && !a(this).hasClass(l.DISABLED))) {
	            var d = b._getParentFromElement(this),
	                e = a(d).hasClass(l.OPEN);if (!e && 27 !== c.which || e && 27 === c.which) {
	              if (27 === c.which) {
	                var f = a(d).find(m.DATA_TOGGLE)[0];a(f).trigger("focus");
	              }return void a(this).trigger("click");
	            }var g = a.makeArray(a(m.VISIBLE_ITEMS));if (g = g.filter(function (a) {
	              return a.offsetWidth || a.offsetHeight;
	            }), g.length) {
	              var h = g.indexOf(c.target);38 === c.which && h > 0 && h--, 40 === c.which && h < g.length - 1 && h++, ~h || (h = 0), g[h].focus();
	            }
	          }
	        } }, { key: "VERSION", get: function get() {
	          return d;
	        } }]), b;
	    }();return a(document).on(k.KEYDOWN_DATA_API, m.DATA_TOGGLE, n._dataApiKeydownHandler).on(k.KEYDOWN_DATA_API, m.ROLE_MENU, n._dataApiKeydownHandler).on(k.KEYDOWN_DATA_API, m.ROLE_LISTBOX, n._dataApiKeydownHandler).on(k.CLICK_DATA_API, n._clearMenus).on(k.CLICK_DATA_API, m.DATA_TOGGLE, n.prototype.toggle).on(k.CLICK_DATA_API, m.FORM_CHILD, function (a) {
	      a.stopPropagation();
	    }), a.fn[b] = n._jQueryInterface, a.fn[b].Constructor = n, a.fn[b].noConflict = function () {
	      return a.fn[b] = j, n._jQueryInterface;
	    }, n;
	  }(jQuery), function (a) {
	    var b = "modal",
	        d = "4.0.0-alpha",
	        g = "bs.modal",
	        h = "." + g,
	        i = ".data-api",
	        j = a.fn[b],
	        k = 300,
	        l = 150,
	        m = { backdrop: !0, keyboard: !0, focus: !0, show: !0 },
	        n = { backdrop: "(boolean|string)", keyboard: "boolean", focus: "boolean", show: "boolean" },
	        o = { HIDE: "hide" + h, HIDDEN: "hidden" + h, SHOW: "show" + h, SHOWN: "shown" + h, FOCUSIN: "focusin" + h, RESIZE: "resize" + h, CLICK_DISMISS: "click.dismiss" + h, KEYDOWN_DISMISS: "keydown.dismiss" + h, MOUSEUP_DISMISS: "mouseup.dismiss" + h, MOUSEDOWN_DISMISS: "mousedown.dismiss" + h, CLICK_DATA_API: "click" + h + i },
	        p = { SCROLLBAR_MEASURER: "modal-scrollbar-measure", BACKDROP: "modal-backdrop", OPEN: "modal-open", FADE: "fade", IN: "in" },
	        q = { DIALOG: ".modal-dialog", DATA_TOGGLE: '[data-toggle="modal"]', DATA_DISMISS: '[data-dismiss="modal"]', FIXED_CONTENT: ".navbar-fixed-top, .navbar-fixed-bottom, .is-fixed" },
	        r = function () {
	      function i(b, d) {
	        c(this, i), this._config = this._getConfig(d), this._element = b, this._dialog = a(b).find(q.DIALOG)[0], this._backdrop = null, this._isShown = !1, this._isBodyOverflowing = !1, this._ignoreBackdropClick = !1, this._originalBodyPadding = 0, this._scrollbarWidth = 0;
	      }return e(i, [{ key: "toggle", value: function value(a) {
	          return this._isShown ? this.hide() : this.show(a);
	        } }, { key: "show", value: function value(b) {
	          var c = this,
	              d = a.Event(o.SHOW, { relatedTarget: b });a(this._element).trigger(d), this._isShown || d.isDefaultPrevented() || (this._isShown = !0, this._checkScrollbar(), this._setScrollbar(), a(document.body).addClass(p.OPEN), this._setEscapeEvent(), this._setResizeEvent(), a(this._element).on(o.CLICK_DISMISS, q.DATA_DISMISS, a.proxy(this.hide, this)), a(this._dialog).on(o.MOUSEDOWN_DISMISS, function () {
	            a(c._element).one(o.MOUSEUP_DISMISS, function (b) {
	              a(b.target).is(c._element) && (c._ignoreBackdropClick = !0);
	            });
	          }), this._showBackdrop(a.proxy(this._showElement, this, b)));
	        } }, { key: "hide", value: function value(b) {
	          b && b.preventDefault();var c = a.Event(o.HIDE);a(this._element).trigger(c), this._isShown && !c.isDefaultPrevented() && (this._isShown = !1, this._setEscapeEvent(), this._setResizeEvent(), a(document).off(o.FOCUSIN), a(this._element).removeClass(p.IN), a(this._element).off(o.CLICK_DISMISS), a(this._dialog).off(o.MOUSEDOWN_DISMISS), f.supportsTransitionEnd() && a(this._element).hasClass(p.FADE) ? a(this._element).one(f.TRANSITION_END, a.proxy(this._hideModal, this)).emulateTransitionEnd(k) : this._hideModal());
	        } }, { key: "dispose", value: function value() {
	          a.removeData(this._element, g), a(window).off(h), a(document).off(h), a(this._element).off(h), a(this._backdrop).off(h), this._config = null, this._element = null, this._dialog = null, this._backdrop = null, this._isShown = null, this._isBodyOverflowing = null, this._ignoreBackdropClick = null, this._originalBodyPadding = null, this._scrollbarWidth = null;
	        } }, { key: "_getConfig", value: function value(c) {
	          return c = a.extend({}, m, c), f.typeCheckConfig(b, c, n), c;
	        } }, { key: "_showElement", value: function value(b) {
	          var c = this,
	              d = f.supportsTransitionEnd() && a(this._element).hasClass(p.FADE);this._element.parentNode && this._element.parentNode.nodeType === Node.ELEMENT_NODE || document.body.appendChild(this._element), this._element.style.display = "block", this._element.scrollTop = 0, d && f.reflow(this._element), a(this._element).addClass(p.IN), this._config.focus && this._enforceFocus();var e = a.Event(o.SHOWN, { relatedTarget: b }),
	              g = function g() {
	            c._config.focus && c._element.focus(), a(c._element).trigger(e);
	          };d ? a(this._dialog).one(f.TRANSITION_END, g).emulateTransitionEnd(k) : g();
	        } }, { key: "_enforceFocus", value: function value() {
	          var b = this;a(document).off(o.FOCUSIN).on(o.FOCUSIN, function (c) {
	            b._element === c.target || a(b._element).has(c.target).length || b._element.focus();
	          });
	        } }, { key: "_setEscapeEvent", value: function value() {
	          var b = this;this._isShown && this._config.keyboard ? a(this._element).on(o.KEYDOWN_DISMISS, function (a) {
	            27 === a.which && b.hide();
	          }) : this._isShown || a(this._element).off(o.KEYDOWN_DISMISS);
	        } }, { key: "_setResizeEvent", value: function value() {
	          this._isShown ? a(window).on(o.RESIZE, a.proxy(this._handleUpdate, this)) : a(window).off(o.RESIZE);
	        } }, { key: "_hideModal", value: function value() {
	          var b = this;this._element.style.display = "none", this._showBackdrop(function () {
	            a(document.body).removeClass(p.OPEN), b._resetAdjustments(), b._resetScrollbar(), a(b._element).trigger(o.HIDDEN);
	          });
	        } }, { key: "_removeBackdrop", value: function value() {
	          this._backdrop && (a(this._backdrop).remove(), this._backdrop = null);
	        } }, { key: "_showBackdrop", value: function value(b) {
	          var c = this,
	              d = a(this._element).hasClass(p.FADE) ? p.FADE : "";if (this._isShown && this._config.backdrop) {
	            var e = f.supportsTransitionEnd() && d;if (this._backdrop = document.createElement("div"), this._backdrop.className = p.BACKDROP, d && a(this._backdrop).addClass(d), a(this._backdrop).appendTo(document.body), a(this._element).on(o.CLICK_DISMISS, function (a) {
	              return c._ignoreBackdropClick ? void (c._ignoreBackdropClick = !1) : void (a.target === a.currentTarget && ("static" === c._config.backdrop ? c._element.focus() : c.hide()));
	            }), e && f.reflow(this._backdrop), a(this._backdrop).addClass(p.IN), !b) return;if (!e) return void b();a(this._backdrop).one(f.TRANSITION_END, b).emulateTransitionEnd(l);
	          } else if (!this._isShown && this._backdrop) {
	            a(this._backdrop).removeClass(p.IN);var g = function g() {
	              c._removeBackdrop(), b && b();
	            };f.supportsTransitionEnd() && a(this._element).hasClass(p.FADE) ? a(this._backdrop).one(f.TRANSITION_END, g).emulateTransitionEnd(l) : g();
	          } else b && b();
	        } }, { key: "_handleUpdate", value: function value() {
	          this._adjustDialog();
	        } }, { key: "_adjustDialog", value: function value() {
	          var a = this._element.scrollHeight > document.documentElement.clientHeight;!this._isBodyOverflowing && a && (this._element.style.paddingLeft = this._scrollbarWidth + "px"), this._isBodyOverflowing && !a && (this._element.style.paddingRight = this._scrollbarWidth + "px~");
	        } }, { key: "_resetAdjustments", value: function value() {
	          this._element.style.paddingLeft = "", this._element.style.paddingRight = "";
	        } }, { key: "_checkScrollbar", value: function value() {
	          var a = window.innerWidth;if (!a) {
	            var b = document.documentElement.getBoundingClientRect();a = b.right - Math.abs(b.left);
	          }this._isBodyOverflowing = document.body.clientWidth < a, this._scrollbarWidth = this._getScrollbarWidth();
	        } }, { key: "_setScrollbar", value: function value() {
	          var b = parseInt(a(q.FIXED_CONTENT).css("padding-right") || 0, 10);this._originalBodyPadding = document.body.style.paddingRight || "", this._isBodyOverflowing && (document.body.style.paddingRight = b + this._scrollbarWidth + "px");
	        } }, { key: "_resetScrollbar", value: function value() {
	          document.body.style.paddingRight = this._originalBodyPadding;
	        } }, { key: "_getScrollbarWidth", value: function value() {
	          var a = document.createElement("div");a.className = p.SCROLLBAR_MEASURER, document.body.appendChild(a);var b = a.offsetWidth - a.clientWidth;return document.body.removeChild(a), b;
	        } }], [{ key: "_jQueryInterface", value: function value(b, c) {
	          return this.each(function () {
	            var d = a(this).data(g),
	                e = a.extend({}, i.Default, a(this).data(), "object" == (typeof b === "undefined" ? "undefined" : _typeof(b)) && b);if (d || (d = new i(this, e), a(this).data(g, d)), "string" == typeof b) {
	              if (void 0 === d[b]) throw new Error('No method named "' + b + '"');d[b](c);
	            } else e.show && d.show(c);
	          });
	        } }, { key: "VERSION", get: function get() {
	          return d;
	        } }, { key: "Default", get: function get() {
	          return m;
	        } }]), i;
	    }();return a(document).on(o.CLICK_DATA_API, q.DATA_TOGGLE, function (b) {
	      var c = this,
	          d = void 0,
	          e = f.getSelectorFromElement(this);e && (d = a(e)[0]);var h = a(d).data(g) ? "toggle" : a.extend({}, a(d).data(), a(this).data());"A" === this.tagName && b.preventDefault();var i = a(d).one(o.SHOW, function (b) {
	        b.isDefaultPrevented() || i.one(o.HIDDEN, function () {
	          a(c).is(":visible") && c.focus();
	        });
	      });r._jQueryInterface.call(a(d), h, this);
	    }), a.fn[b] = r._jQueryInterface, a.fn[b].Constructor = r, a.fn[b].noConflict = function () {
	      return a.fn[b] = j, r._jQueryInterface;
	    }, r;
	  }(jQuery), function (a) {
	    var b = "scrollspy",
	        d = "4.0.0-alpha",
	        g = "bs.scrollspy",
	        h = "." + g,
	        i = ".data-api",
	        j = a.fn[b],
	        k = { offset: 10, method: "auto", target: "" },
	        l = { offset: "number", method: "string", target: "(string|element)" },
	        m = { ACTIVATE: "activate" + h, SCROLL: "scroll" + h, LOAD_DATA_API: "load" + h + i },
	        n = { DROPDOWN_ITEM: "dropdown-item", DROPDOWN_MENU: "dropdown-menu", NAV_LINK: "nav-link", NAV: "nav", ACTIVE: "active" },
	        o = { DATA_SPY: '[data-spy="scroll"]', ACTIVE: ".active", LIST_ITEM: ".list-item", LI: "li", LI_DROPDOWN: "li.dropdown", NAV_LINKS: ".nav-link", DROPDOWN: ".dropdown", DROPDOWN_ITEMS: ".dropdown-item", DROPDOWN_TOGGLE: ".dropdown-toggle" },
	        p = { OFFSET: "offset", POSITION: "position" },
	        q = function () {
	      function i(b, d) {
	        c(this, i), this._element = b, this._scrollElement = "BODY" === b.tagName ? window : b, this._config = this._getConfig(d), this._selector = this._config.target + " " + o.NAV_LINKS + "," + (this._config.target + " " + o.DROPDOWN_ITEMS), this._offsets = [], this._targets = [], this._activeTarget = null, this._scrollHeight = 0, a(this._scrollElement).on(m.SCROLL, a.proxy(this._process, this)), this.refresh(), this._process();
	      }return e(i, [{ key: "refresh", value: function value() {
	          var b = this,
	              c = this._scrollElement !== this._scrollElement.window ? p.POSITION : p.OFFSET,
	              d = "auto" === this._config.method ? c : this._config.method,
	              e = d === p.POSITION ? this._getScrollTop() : 0;this._offsets = [], this._targets = [], this._scrollHeight = this._getScrollHeight();var g = a.makeArray(a(this._selector));g.map(function (b) {
	            var c = void 0,
	                g = f.getSelectorFromElement(b);return g && (c = a(g)[0]), c && (c.offsetWidth || c.offsetHeight) ? [a(c)[d]().top + e, g] : void 0;
	          }).filter(function (a) {
	            return a;
	          }).sort(function (a, b) {
	            return a[0] - b[0];
	          }).forEach(function (a) {
	            b._offsets.push(a[0]), b._targets.push(a[1]);
	          });
	        } }, { key: "dispose", value: function value() {
	          a.removeData(this._element, g), a(this._scrollElement).off(h), this._element = null, this._scrollElement = null, this._config = null, this._selector = null, this._offsets = null, this._targets = null, this._activeTarget = null, this._scrollHeight = null;
	        } }, { key: "_getConfig", value: function value(c) {
	          if (c = a.extend({}, k, c), "string" != typeof c.target) {
	            var d = a(c.target).attr("id");d || (d = f.getUID(b), a(c.target).attr("id", d)), c.target = "#" + d;
	          }return f.typeCheckConfig(b, c, l), c;
	        } }, { key: "_getScrollTop", value: function value() {
	          return this._scrollElement === window ? this._scrollElement.scrollY : this._scrollElement.scrollTop;
	        } }, { key: "_getScrollHeight", value: function value() {
	          return this._scrollElement.scrollHeight || Math.max(document.body.scrollHeight, document.documentElement.scrollHeight);
	        } }, { key: "_process", value: function value() {
	          var a = this._getScrollTop() + this._config.offset,
	              b = this._getScrollHeight(),
	              c = this._config.offset + b - this._scrollElement.offsetHeight;if (this._scrollHeight !== b && this.refresh(), a >= c) {
	            var d = this._targets[this._targets.length - 1];this._activeTarget !== d && this._activate(d);
	          }if (this._activeTarget && a < this._offsets[0]) return this._activeTarget = null, void this._clear();for (var e = this._offsets.length; e--;) {
	            var f = this._activeTarget !== this._targets[e] && a >= this._offsets[e] && (void 0 === this._offsets[e + 1] || a < this._offsets[e + 1]);f && this._activate(this._targets[e]);
	          }
	        } }, { key: "_activate", value: function value(b) {
	          this._activeTarget = b, this._clear();var c = this._selector.split(",");c = c.map(function (a) {
	            return a + '[data-target="' + b + '"],' + (a + '[href="' + b + '"]');
	          });var d = a(c.join(","));d.hasClass(n.DROPDOWN_ITEM) ? (d.closest(o.DROPDOWN).find(o.DROPDOWN_TOGGLE).addClass(n.ACTIVE), d.addClass(n.ACTIVE)) : d.parents(o.LI).find(o.NAV_LINKS).addClass(n.ACTIVE), a(this._scrollElement).trigger(m.ACTIVATE, { relatedTarget: b });
	        } }, { key: "_clear", value: function value() {
	          a(this._selector).filter(o.ACTIVE).removeClass(n.ACTIVE);
	        } }], [{ key: "_jQueryInterface", value: function value(b) {
	          return this.each(function () {
	            var c = a(this).data(g),
	                d = "object" == (typeof b === "undefined" ? "undefined" : _typeof(b)) && b || null;if (c || (c = new i(this, d), a(this).data(g, c)), "string" == typeof b) {
	              if (void 0 === c[b]) throw new Error('No method named "' + b + '"');c[b]();
	            }
	          });
	        } }, { key: "VERSION", get: function get() {
	          return d;
	        } }, { key: "Default", get: function get() {
	          return k;
	        } }]), i;
	    }();return a(window).on(m.LOAD_DATA_API, function () {
	      for (var b = a.makeArray(a(o.DATA_SPY)), c = b.length; c--;) {
	        var d = a(b[c]);q._jQueryInterface.call(d, d.data());
	      }
	    }), a.fn[b] = q._jQueryInterface, a.fn[b].Constructor = q, a.fn[b].noConflict = function () {
	      return a.fn[b] = j, q._jQueryInterface;
	    }, q;
	  }(jQuery), function (a) {
	    var b = "tab",
	        d = "4.0.0-alpha",
	        g = "bs.tab",
	        h = "." + g,
	        i = ".data-api",
	        j = a.fn[b],
	        k = 150,
	        l = { HIDE: "hide" + h, HIDDEN: "hidden" + h, SHOW: "show" + h, SHOWN: "shown" + h, CLICK_DATA_API: "click" + h + i },
	        m = { DROPDOWN_MENU: "dropdown-menu", ACTIVE: "active", FADE: "fade", IN: "in" },
	        n = { A: "a", LI: "li", DROPDOWN: ".dropdown", UL: "ul:not(.dropdown-menu)", FADE_CHILD: "> .nav-item .fade, > .fade", ACTIVE: ".active", ACTIVE_CHILD: "> .nav-item > .active, > .active",
	      DATA_TOGGLE: '[data-toggle="tab"], [data-toggle="pill"]', DROPDOWN_TOGGLE: ".dropdown-toggle", DROPDOWN_ACTIVE_CHILD: "> .dropdown-menu .active" },
	        o = function () {
	      function b(a) {
	        c(this, b), this._element = a;
	      }return e(b, [{ key: "show", value: function value() {
	          var b = this;if (!this._element.parentNode || this._element.parentNode.nodeType !== Node.ELEMENT_NODE || !a(this._element).hasClass(m.ACTIVE)) {
	            var c = void 0,
	                d = void 0,
	                e = a(this._element).closest(n.UL)[0],
	                g = f.getSelectorFromElement(this._element);e && (d = a.makeArray(a(e).find(n.ACTIVE)), d = d[d.length - 1]);var h = a.Event(l.HIDE, { relatedTarget: this._element }),
	                i = a.Event(l.SHOW, { relatedTarget: d });if (d && a(d).trigger(h), a(this._element).trigger(i), !i.isDefaultPrevented() && !h.isDefaultPrevented()) {
	              g && (c = a(g)[0]), this._activate(this._element, e);var j = function j() {
	                var c = a.Event(l.HIDDEN, { relatedTarget: b._element }),
	                    e = a.Event(l.SHOWN, { relatedTarget: d });a(d).trigger(c), a(b._element).trigger(e);
	              };c ? this._activate(c, c.parentNode, j) : j();
	            }
	          }
	        } }, { key: "dispose", value: function value() {
	          a.removeClass(this._element, g), this._element = null;
	        } }, { key: "_activate", value: function value(b, c, d) {
	          var e = a(c).find(n.ACTIVE_CHILD)[0],
	              g = d && f.supportsTransitionEnd() && (e && a(e).hasClass(m.FADE) || Boolean(a(c).find(n.FADE_CHILD)[0])),
	              h = a.proxy(this._transitionComplete, this, b, e, g, d);e && g ? a(e).one(f.TRANSITION_END, h).emulateTransitionEnd(k) : h(), e && a(e).removeClass(m.IN);
	        } }, { key: "_transitionComplete", value: function value(b, c, d, e) {
	          if (c) {
	            a(c).removeClass(m.ACTIVE);var g = a(c).find(n.DROPDOWN_ACTIVE_CHILD)[0];g && a(g).removeClass(m.ACTIVE), c.setAttribute("aria-expanded", !1);
	          }if (a(b).addClass(m.ACTIVE), b.setAttribute("aria-expanded", !0), d ? (f.reflow(b), a(b).addClass(m.IN)) : a(b).removeClass(m.FADE), b.parentNode && a(b.parentNode).hasClass(m.DROPDOWN_MENU)) {
	            var h = a(b).closest(n.DROPDOWN)[0];h && a(h).find(n.DROPDOWN_TOGGLE).addClass(m.ACTIVE), b.setAttribute("aria-expanded", !0);
	          }e && e();
	        } }], [{ key: "_jQueryInterface", value: function value(c) {
	          return this.each(function () {
	            var d = a(this),
	                e = d.data(g);if (e || (e = e = new b(this), d.data(g, e)), "string" == typeof c) {
	              if (void 0 === e[c]) throw new Error('No method named "' + c + '"');e[c]();
	            }
	          });
	        } }, { key: "VERSION", get: function get() {
	          return d;
	        } }]), b;
	    }();return a(document).on(l.CLICK_DATA_API, n.DATA_TOGGLE, function (b) {
	      b.preventDefault(), o._jQueryInterface.call(a(this), "show");
	    }), a.fn[b] = o._jQueryInterface, a.fn[b].Constructor = o, a.fn[b].noConflict = function () {
	      return a.fn[b] = j, o._jQueryInterface;
	    }, o;
	  }(jQuery), function (a) {
	    if (void 0 === window.Tether) throw new Error("Bootstrap tooltips require Tether (http://github.hubspot.com/tether/)");var b = "tooltip",
	        d = "4.0.0-alpha",
	        g = "bs.tooltip",
	        h = "." + g,
	        i = a.fn[b],
	        j = 150,
	        k = "bs-tether",
	        l = { animation: !0, template: '<div class="tooltip" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>', trigger: "hover focus", title: "", delay: 0, html: !1, selector: !1, placement: "top", offset: "0 0", constraints: [] },
	        m = { animation: "boolean", template: "string", title: "(string|element|function)", trigger: "string", delay: "(number|object)", html: "boolean", selector: "(string|boolean)", placement: "(string|function)", offset: "string", constraints: "array" },
	        n = { TOP: "bottom center", RIGHT: "middle left", BOTTOM: "top center", LEFT: "middle right" },
	        o = { IN: "in", OUT: "out" },
	        p = { HIDE: "hide" + h, HIDDEN: "hidden" + h, SHOW: "show" + h, SHOWN: "shown" + h, INSERTED: "inserted" + h, CLICK: "click" + h, FOCUSIN: "focusin" + h, FOCUSOUT: "focusout" + h, MOUSEENTER: "mouseenter" + h, MOUSELEAVE: "mouseleave" + h },
	        q = { FADE: "fade", IN: "in" },
	        r = { TOOLTIP: ".tooltip", TOOLTIP_INNER: ".tooltip-inner" },
	        s = { element: !1, enabled: !1 },
	        t = { HOVER: "hover", FOCUS: "focus", CLICK: "click", MANUAL: "manual" },
	        u = function () {
	      function i(a, b) {
	        c(this, i), this._isEnabled = !0, this._timeout = 0, this._hoverState = "", this._activeTrigger = {}, this._tether = null, this.element = a, this.config = this._getConfig(b), this.tip = null, this._setListeners();
	      }return e(i, [{ key: "enable", value: function value() {
	          this._isEnabled = !0;
	        } }, { key: "disable", value: function value() {
	          this._isEnabled = !1;
	        } }, { key: "toggleEnabled", value: function value() {
	          this._isEnabled = !this._isEnabled;
	        } }, { key: "toggle", value: function value(b) {
	          if (b) {
	            var c = this.constructor.DATA_KEY,
	                d = a(b.currentTarget).data(c);d || (d = new this.constructor(b.currentTarget, this._getDelegateConfig()), a(b.currentTarget).data(c, d)), d._activeTrigger.click = !d._activeTrigger.click, d._isWithActiveTrigger() ? d._enter(null, d) : d._leave(null, d);
	          } else {
	            if (a(this.getTipElement()).hasClass(q.IN)) return void this._leave(null, this);this._enter(null, this);
	          }
	        } }, { key: "dispose", value: function value() {
	          clearTimeout(this._timeout), this.cleanupTether(), a.removeData(this.element, this.constructor.DATA_KEY), a(this.element).off(this.constructor.EVENT_KEY), this.tip && a(this.tip).remove(), this._isEnabled = null, this._timeout = null, this._hoverState = null, this._activeTrigger = null, this._tether = null, this.element = null, this.config = null, this.tip = null;
	        } }, { key: "show", value: function value() {
	          var b = this,
	              c = a.Event(this.constructor.Event.SHOW);if (this.isWithContent() && this._isEnabled) {
	            a(this.element).trigger(c);var d = a.contains(this.element.ownerDocument.documentElement, this.element);if (c.isDefaultPrevented() || !d) return;var e = this.getTipElement(),
	                g = f.getUID(this.constructor.NAME);e.setAttribute("id", g), this.element.setAttribute("aria-describedby", g), this.setContent(), this.config.animation && a(e).addClass(q.FADE);var h = "function" == typeof this.config.placement ? this.config.placement.call(this, e, this.element) : this.config.placement,
	                j = this._getAttachment(h);a(e).data(this.constructor.DATA_KEY, this).appendTo(document.body), a(this.element).trigger(this.constructor.Event.INSERTED), this._tether = new Tether({ attachment: j, element: e, target: this.element, classes: s, classPrefix: k, offset: this.config.offset, constraints: this.config.constraints, addTargetClasses: !1 }), f.reflow(e), this._tether.position(), a(e).addClass(q.IN);var l = function l() {
	              var c = b._hoverState;b._hoverState = null, a(b.element).trigger(b.constructor.Event.SHOWN), c === o.OUT && b._leave(null, b);
	            };if (f.supportsTransitionEnd() && a(this.tip).hasClass(q.FADE)) return void a(this.tip).one(f.TRANSITION_END, l).emulateTransitionEnd(i._TRANSITION_DURATION);l();
	          }
	        } }, { key: "hide", value: function value(b) {
	          var c = this,
	              d = this.getTipElement(),
	              e = a.Event(this.constructor.Event.HIDE),
	              g = function g() {
	            c._hoverState !== o.IN && d.parentNode && d.parentNode.removeChild(d), c.element.removeAttribute("aria-describedby"), a(c.element).trigger(c.constructor.Event.HIDDEN), c.cleanupTether(), b && b();
	          };a(this.element).trigger(e), e.isDefaultPrevented() || (a(d).removeClass(q.IN), f.supportsTransitionEnd() && a(this.tip).hasClass(q.FADE) ? a(d).one(f.TRANSITION_END, g).emulateTransitionEnd(j) : g(), this._hoverState = "");
	        } }, { key: "isWithContent", value: function value() {
	          return Boolean(this.getTitle());
	        } }, { key: "getTipElement", value: function value() {
	          return this.tip = this.tip || a(this.config.template)[0];
	        } }, { key: "setContent", value: function value() {
	          var b = a(this.getTipElement());this.setElementContent(b.find(r.TOOLTIP_INNER), this.getTitle()), b.removeClass(q.FADE).removeClass(q.IN), this.cleanupTether();
	        } }, { key: "setElementContent", value: function value(b, c) {
	          var d = this.config.html;"object" == (typeof c === "undefined" ? "undefined" : _typeof(c)) && (c.nodeType || c.jquery) ? d ? a(c).parent().is(b) || b.empty().append(c) : b.text(a(c).text()) : b[d ? "html" : "text"](c);
	        } }, { key: "getTitle", value: function value() {
	          var a = this.element.getAttribute("data-original-title");return a || (a = "function" == typeof this.config.title ? this.config.title.call(this.element) : this.config.title), a;
	        } }, { key: "cleanupTether", value: function value() {
	          this._tether && this._tether.destroy();
	        } }, { key: "_getAttachment", value: function value(a) {
	          return n[a.toUpperCase()];
	        } }, { key: "_setListeners", value: function value() {
	          var b = this,
	              c = this.config.trigger.split(" ");c.forEach(function (c) {
	            if ("click" === c) a(b.element).on(b.constructor.Event.CLICK, b.config.selector, a.proxy(b.toggle, b));else if (c !== t.MANUAL) {
	              var d = c === t.HOVER ? b.constructor.Event.MOUSEENTER : b.constructor.Event.FOCUSIN,
	                  e = c === t.HOVER ? b.constructor.Event.MOUSELEAVE : b.constructor.Event.FOCUSOUT;a(b.element).on(d, b.config.selector, a.proxy(b._enter, b)).on(e, b.config.selector, a.proxy(b._leave, b));
	            }
	          }), this.config.selector ? this.config = a.extend({}, this.config, { trigger: "manual", selector: "" }) : this._fixTitle();
	        } }, { key: "_fixTitle", value: function value() {
	          var a = _typeof(this.element.getAttribute("data-original-title"));(this.element.getAttribute("title") || "string" !== a) && (this.element.setAttribute("data-original-title", this.element.getAttribute("title") || ""), this.element.setAttribute("title", ""));
	        } }, { key: "_enter", value: function value(b, c) {
	          var d = this.constructor.DATA_KEY;return c = c || a(b.currentTarget).data(d), c || (c = new this.constructor(b.currentTarget, this._getDelegateConfig()), a(b.currentTarget).data(d, c)), b && (c._activeTrigger["focusin" === b.type ? t.FOCUS : t.HOVER] = !0), a(c.getTipElement()).hasClass(q.IN) || c._hoverState === o.IN ? void (c._hoverState = o.IN) : (clearTimeout(c._timeout), c._hoverState = o.IN, c.config.delay && c.config.delay.show ? void (c._timeout = setTimeout(function () {
	            c._hoverState === o.IN && c.show();
	          }, c.config.delay.show)) : void c.show());
	        } }, { key: "_leave", value: function value(b, c) {
	          var d = this.constructor.DATA_KEY;return c = c || a(b.currentTarget).data(d), c || (c = new this.constructor(b.currentTarget, this._getDelegateConfig()), a(b.currentTarget).data(d, c)), b && (c._activeTrigger["focusout" === b.type ? t.FOCUS : t.HOVER] = !1), c._isWithActiveTrigger() ? void 0 : (clearTimeout(c._timeout), c._hoverState = o.OUT, c.config.delay && c.config.delay.hide ? void (c._timeout = setTimeout(function () {
	            c._hoverState === o.OUT && c.hide();
	          }, c.config.delay.hide)) : void c.hide());
	        } }, { key: "_isWithActiveTrigger", value: function value() {
	          for (var a in this._activeTrigger) {
	            if (this._activeTrigger[a]) return !0;
	          }return !1;
	        } }, { key: "_getConfig", value: function value(c) {
	          return c = a.extend({}, this.constructor.Default, a(this.element).data(), c), c.delay && "number" == typeof c.delay && (c.delay = { show: c.delay, hide: c.delay }), f.typeCheckConfig(b, c, this.constructor.DefaultType), c;
	        } }, { key: "_getDelegateConfig", value: function value() {
	          var a = {};if (this.config) for (var b in this.config) {
	            this.constructor.Default[b] !== this.config[b] && (a[b] = this.config[b]);
	          }return a;
	        } }], [{ key: "_jQueryInterface", value: function value(b) {
	          return this.each(function () {
	            var c = a(this).data(g),
	                d = "object" == (typeof b === "undefined" ? "undefined" : _typeof(b)) ? b : null;if ((c || !/destroy|hide/.test(b)) && (c || (c = new i(this, d), a(this).data(g, c)), "string" == typeof b)) {
	              if (void 0 === c[b]) throw new Error('No method named "' + b + '"');c[b]();
	            }
	          });
	        } }, { key: "VERSION", get: function get() {
	          return d;
	        } }, { key: "Default", get: function get() {
	          return l;
	        } }, { key: "NAME", get: function get() {
	          return b;
	        } }, { key: "DATA_KEY", get: function get() {
	          return g;
	        } }, { key: "Event", get: function get() {
	          return p;
	        } }, { key: "EVENT_KEY", get: function get() {
	          return h;
	        } }, { key: "DefaultType", get: function get() {
	          return m;
	        } }]), i;
	    }();return a.fn[b] = u._jQueryInterface, a.fn[b].Constructor = u, a.fn[b].noConflict = function () {
	      return a.fn[b] = i, u._jQueryInterface;
	    }, u;
	  }(jQuery));(function (a) {
	    var f = "popover",
	        h = "4.0.0-alpha",
	        i = "bs.popover",
	        j = "." + i,
	        k = a.fn[f],
	        l = a.extend({}, g.Default, { placement: "right", trigger: "click", content: "", template: '<div class="popover" role="tooltip"><div class="popover-arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>' }),
	        m = a.extend({}, g.DefaultType, { content: "(string|element|function)" }),
	        n = { FADE: "fade", IN: "in" },
	        o = { TITLE: ".popover-title", CONTENT: ".popover-content", ARROW: ".popover-arrow" },
	        p = { HIDE: "hide" + j, HIDDEN: "hidden" + j, SHOW: "show" + j, SHOWN: "shown" + j, INSERTED: "inserted" + j, CLICK: "click" + j, FOCUSIN: "focusin" + j, FOCUSOUT: "focusout" + j, MOUSEENTER: "mouseenter" + j, MOUSELEAVE: "mouseleave" + j },
	        q = function (g) {
	      function k() {
	        c(this, k), d(Object.getPrototypeOf(k.prototype), "constructor", this).apply(this, arguments);
	      }return b(k, g), e(k, [{ key: "isWithContent", value: function value() {
	          return this.getTitle() || this._getContent();
	        } }, { key: "getTipElement", value: function value() {
	          return this.tip = this.tip || a(this.config.template)[0];
	        } }, { key: "setContent", value: function value() {
	          var b = a(this.getTipElement());this.setElementContent(b.find(o.TITLE), this.getTitle()), this.setElementContent(b.find(o.CONTENT), this._getContent()), b.removeClass(n.FADE).removeClass(n.IN), this.cleanupTether();
	        } }, { key: "_getContent", value: function value() {
	          return this.element.getAttribute("data-content") || ("function" == typeof this.config.content ? this.config.content.call(this.element) : this.config.content);
	        } }], [{ key: "_jQueryInterface", value: function value(b) {
	          return this.each(function () {
	            var c = a(this).data(i),
	                d = "object" == (typeof b === "undefined" ? "undefined" : _typeof(b)) ? b : null;if ((c || !/destroy|hide/.test(b)) && (c || (c = new k(this, d), a(this).data(i, c)), "string" == typeof b)) {
	              if (void 0 === c[b]) throw new Error('No method named "' + b + '"');c[b]();
	            }
	          });
	        } }, { key: "VERSION", get: function get() {
	          return h;
	        } }, { key: "Default", get: function get() {
	          return l;
	        } }, { key: "NAME", get: function get() {
	          return f;
	        } }, { key: "DATA_KEY", get: function get() {
	          return i;
	        } }, { key: "Event", get: function get() {
	          return p;
	        } }, { key: "EVENT_KEY", get: function get() {
	          return j;
	        } }, { key: "DefaultType", get: function get() {
	          return m;
	        } }]), k;
	    }(g);return a.fn[f] = q._jQueryInterface, a.fn[f].Constructor = q, a.fn[f].noConflict = function () {
	      return a.fn[f] = k, q._jQueryInterface;
	    }, q;
	  })(jQuery);
	}(jQuery);

/***/ },
/* 10 */
/***/ function(module, exports, __webpack_require__) {

	var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;"use strict";

	var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol ? "symbol" : typeof obj; };

	/*! Magnific Popup - v1.0.1 - 2015-12-30
	* http://dimsemenov.com/plugins/magnific-popup/
	* Copyright (c) 2015 Dmitry Semenov; */
	!function (a) {
	   true ? !(__WEBPACK_AMD_DEFINE_ARRAY__ = [__webpack_require__(4)], __WEBPACK_AMD_DEFINE_FACTORY__ = (a), __WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ? (__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__), __WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__)) : a("object" == (typeof exports === "undefined" ? "undefined" : _typeof(exports)) ? require("jquery") : window.jQuery || window.Zepto);
	}(function (a) {
	  var b,
	      c,
	      d,
	      e,
	      f,
	      g,
	      h = "Close",
	      i = "BeforeClose",
	      j = "AfterClose",
	      k = "BeforeAppend",
	      l = "MarkupParse",
	      m = "Open",
	      n = "Change",
	      o = "mfp",
	      p = "." + o,
	      q = "mfp-ready",
	      r = "mfp-removing",
	      s = "mfp-prevent-close",
	      t = function t() {},
	      u = !!window.jQuery,
	      v = a(window),
	      w = function w(a, c) {
	    b.ev.on(o + a + p, c);
	  },
	      x = function x(b, c, d, e) {
	    var f = document.createElement("div");return f.className = "mfp-" + b, d && (f.innerHTML = d), e ? c && c.appendChild(f) : (f = a(f), c && f.appendTo(c)), f;
	  },
	      y = function y(c, d) {
	    b.ev.triggerHandler(o + c, d), b.st.callbacks && (c = c.charAt(0).toLowerCase() + c.slice(1), b.st.callbacks[c] && b.st.callbacks[c].apply(b, a.isArray(d) ? d : [d]));
	  },
	      z = function z(c) {
	    return c === g && b.currTemplate.closeBtn || (b.currTemplate.closeBtn = a(b.st.closeMarkup.replace("%title%", b.st.tClose)), g = c), b.currTemplate.closeBtn;
	  },
	      A = function A() {
	    a.magnificPopup.instance || (b = new t(), b.init(), a.magnificPopup.instance = b);
	  },
	      B = function B() {
	    var a = document.createElement("p").style,
	        b = ["ms", "O", "Moz", "Webkit"];if (void 0 !== a.transition) return !0;for (; b.length;) {
	      if (b.pop() + "Transition" in a) return !0;
	    }return !1;
	  };t.prototype = { constructor: t, init: function init() {
	      var c = navigator.appVersion;b.isIE7 = -1 !== c.indexOf("MSIE 7."), b.isIE8 = -1 !== c.indexOf("MSIE 8."), b.isLowIE = b.isIE7 || b.isIE8, b.isAndroid = /android/gi.test(c), b.isIOS = /iphone|ipad|ipod/gi.test(c), b.supportsTransition = B(), b.probablyMobile = b.isAndroid || b.isIOS || /(Opera Mini)|Kindle|webOS|BlackBerry|(Opera Mobi)|(Windows Phone)|IEMobile/i.test(navigator.userAgent), d = a(document), b.popupsCache = {};
	    }, open: function open(c) {
	      var e;if (c.isObj === !1) {
	        b.items = c.items.toArray(), b.index = 0;var g,
	            h = c.items;for (e = 0; e < h.length; e++) {
	          if (g = h[e], g.parsed && (g = g.el[0]), g === c.el[0]) {
	            b.index = e;break;
	          }
	        }
	      } else b.items = a.isArray(c.items) ? c.items : [c.items], b.index = c.index || 0;if (b.isOpen) return void b.updateItemHTML();b.types = [], f = "", c.mainEl && c.mainEl.length ? b.ev = c.mainEl.eq(0) : b.ev = d, c.key ? (b.popupsCache[c.key] || (b.popupsCache[c.key] = {}), b.currTemplate = b.popupsCache[c.key]) : b.currTemplate = {}, b.st = a.extend(!0, {}, a.magnificPopup.defaults, c), b.fixedContentPos = "auto" === b.st.fixedContentPos ? !b.probablyMobile : b.st.fixedContentPos, b.st.modal && (b.st.closeOnContentClick = !1, b.st.closeOnBgClick = !1, b.st.showCloseBtn = !1, b.st.enableEscapeKey = !1), b.bgOverlay || (b.bgOverlay = x("bg").on("click" + p, function () {
	        b.close();
	      }), b.wrap = x("wrap").attr("tabindex", -1).on("click" + p, function (a) {
	        b._checkIfClose(a.target) && b.close();
	      }), b.container = x("container", b.wrap)), b.contentContainer = x("content"), b.st.preloader && (b.preloader = x("preloader", b.container, b.st.tLoading));var i = a.magnificPopup.modules;for (e = 0; e < i.length; e++) {
	        var j = i[e];j = j.charAt(0).toUpperCase() + j.slice(1), b["init" + j].call(b);
	      }y("BeforeOpen"), b.st.showCloseBtn && (b.st.closeBtnInside ? (w(l, function (a, b, c, d) {
	        c.close_replaceWith = z(d.type);
	      }), f += " mfp-close-btn-in") : b.wrap.append(z())), b.st.alignTop && (f += " mfp-align-top"), b.fixedContentPos ? b.wrap.css({ overflow: b.st.overflowY, overflowX: "hidden", overflowY: b.st.overflowY }) : b.wrap.css({ top: v.scrollTop(), position: "absolute" }), (b.st.fixedBgPos === !1 || "auto" === b.st.fixedBgPos && !b.fixedContentPos) && b.bgOverlay.css({ height: d.height(), position: "absolute" }), b.st.enableEscapeKey && d.on("keyup" + p, function (a) {
	        27 === a.keyCode && b.close();
	      }), v.on("resize" + p, function () {
	        b.updateSize();
	      }), b.st.closeOnContentClick || (f += " mfp-auto-cursor"), f && b.wrap.addClass(f);var k = b.wH = v.height(),
	          n = {};if (b.fixedContentPos && b._hasScrollBar(k)) {
	        var o = b._getScrollbarSize();o && (n.marginRight = o);
	      }b.fixedContentPos && (b.isIE7 ? a("body, html").css("overflow", "hidden") : n.overflow = "hidden");var r = b.st.mainClass;return b.isIE7 && (r += " mfp-ie7"), r && b._addClassToMFP(r), b.updateItemHTML(), y("BuildControls"), a("html").css(n), b.bgOverlay.add(b.wrap).prependTo(b.st.prependTo || a(document.body)), b._lastFocusedEl = document.activeElement, setTimeout(function () {
	        b.content ? (b._addClassToMFP(q), b._setFocus()) : b.bgOverlay.addClass(q), d.on("focusin" + p, b._onFocusIn);
	      }, 16), b.isOpen = !0, b.updateSize(k), y(m), c;
	    }, close: function close() {
	      b.isOpen && (y(i), b.isOpen = !1, b.st.removalDelay && !b.isLowIE && b.supportsTransition ? (b._addClassToMFP(r), setTimeout(function () {
	        b._close();
	      }, b.st.removalDelay)) : b._close());
	    }, _close: function _close() {
	      y(h);var c = r + " " + q + " ";if (b.bgOverlay.detach(), b.wrap.detach(), b.container.empty(), b.st.mainClass && (c += b.st.mainClass + " "), b._removeClassFromMFP(c), b.fixedContentPos) {
	        var e = { marginRight: "" };b.isIE7 ? a("body, html").css("overflow", "") : e.overflow = "", a("html").css(e);
	      }d.off("keyup" + p + " focusin" + p), b.ev.off(p), b.wrap.attr("class", "mfp-wrap").removeAttr("style"), b.bgOverlay.attr("class", "mfp-bg"), b.container.attr("class", "mfp-container"), !b.st.showCloseBtn || b.st.closeBtnInside && b.currTemplate[b.currItem.type] !== !0 || b.currTemplate.closeBtn && b.currTemplate.closeBtn.detach(), b.st.autoFocusLast && b._lastFocusedEl && a(b._lastFocusedEl).focus(), b.currItem = null, b.content = null, b.currTemplate = null, b.prevHeight = 0, y(j);
	    }, updateSize: function updateSize(a) {
	      if (b.isIOS) {
	        var c = document.documentElement.clientWidth / window.innerWidth,
	            d = window.innerHeight * c;b.wrap.css("height", d), b.wH = d;
	      } else b.wH = a || v.height();b.fixedContentPos || b.wrap.css("height", b.wH), y("Resize");
	    }, updateItemHTML: function updateItemHTML() {
	      var c = b.items[b.index];b.contentContainer.detach(), b.content && b.content.detach(), c.parsed || (c = b.parseEl(b.index));var d = c.type;if (y("BeforeChange", [b.currItem ? b.currItem.type : "", d]), b.currItem = c, !b.currTemplate[d]) {
	        var f = b.st[d] ? b.st[d].markup : !1;y("FirstMarkupParse", f), f ? b.currTemplate[d] = a(f) : b.currTemplate[d] = !0;
	      }e && e !== c.type && b.container.removeClass("mfp-" + e + "-holder");var g = b["get" + d.charAt(0).toUpperCase() + d.slice(1)](c, b.currTemplate[d]);b.appendContent(g, d), c.preloaded = !0, y(n, c), e = c.type, b.container.prepend(b.contentContainer), y("AfterChange");
	    }, appendContent: function appendContent(a, c) {
	      b.content = a, a ? b.st.showCloseBtn && b.st.closeBtnInside && b.currTemplate[c] === !0 ? b.content.find(".mfp-close").length || b.content.append(z()) : b.content = a : b.content = "", y(k), b.container.addClass("mfp-" + c + "-holder"), b.contentContainer.append(b.content);
	    }, parseEl: function parseEl(c) {
	      var d,
	          e = b.items[c];if (e.tagName ? e = { el: a(e) } : (d = e.type, e = { data: e, src: e.src }), e.el) {
	        for (var f = b.types, g = 0; g < f.length; g++) {
	          if (e.el.hasClass("mfp-" + f[g])) {
	            d = f[g];break;
	          }
	        }e.src = e.el.attr("data-mfp-src"), e.src || (e.src = e.el.attr("href"));
	      }return e.type = d || b.st.type || "inline", e.index = c, e.parsed = !0, b.items[c] = e, y("ElementParse", e), b.items[c];
	    }, addGroup: function addGroup(a, c) {
	      var d = function d(_d) {
	        _d.mfpEl = this, b._openClick(_d, a, c);
	      };c || (c = {});var e = "click.magnificPopup";c.mainEl = a, c.items ? (c.isObj = !0, a.off(e).on(e, d)) : (c.isObj = !1, c.delegate ? a.off(e).on(e, c.delegate, d) : (c.items = a, a.off(e).on(e, d)));
	    }, _openClick: function _openClick(c, d, e) {
	      var f = void 0 !== e.midClick ? e.midClick : a.magnificPopup.defaults.midClick;if (f || !(2 === c.which || c.ctrlKey || c.metaKey || c.altKey || c.shiftKey)) {
	        var g = void 0 !== e.disableOn ? e.disableOn : a.magnificPopup.defaults.disableOn;if (g) if (a.isFunction(g)) {
	          if (!g.call(b)) return !0;
	        } else if (v.width() < g) return !0;c.type && (c.preventDefault(), b.isOpen && c.stopPropagation()), e.el = a(c.mfpEl), e.delegate && (e.items = d.find(e.delegate)), b.open(e);
	      }
	    }, updateStatus: function updateStatus(a, d) {
	      if (b.preloader) {
	        c !== a && b.container.removeClass("mfp-s-" + c), d || "loading" !== a || (d = b.st.tLoading);var e = { status: a, text: d };y("UpdateStatus", e), a = e.status, d = e.text, b.preloader.html(d), b.preloader.find("a").on("click", function (a) {
	          a.stopImmediatePropagation();
	        }), b.container.addClass("mfp-s-" + a), c = a;
	      }
	    }, _checkIfClose: function _checkIfClose(c) {
	      if (!a(c).hasClass(s)) {
	        var d = b.st.closeOnContentClick,
	            e = b.st.closeOnBgClick;if (d && e) return !0;if (!b.content || a(c).hasClass("mfp-close") || b.preloader && c === b.preloader[0]) return !0;if (c === b.content[0] || a.contains(b.content[0], c)) {
	          if (d) return !0;
	        } else if (e && a.contains(document, c)) return !0;return !1;
	      }
	    }, _addClassToMFP: function _addClassToMFP(a) {
	      b.bgOverlay.addClass(a), b.wrap.addClass(a);
	    }, _removeClassFromMFP: function _removeClassFromMFP(a) {
	      this.bgOverlay.removeClass(a), b.wrap.removeClass(a);
	    }, _hasScrollBar: function _hasScrollBar(a) {
	      return (b.isIE7 ? d.height() : document.body.scrollHeight) > (a || v.height());
	    }, _setFocus: function _setFocus() {
	      (b.st.focus ? b.content.find(b.st.focus).eq(0) : b.wrap).focus();
	    }, _onFocusIn: function _onFocusIn(c) {
	      return c.target === b.wrap[0] || a.contains(b.wrap[0], c.target) ? void 0 : (b._setFocus(), !1);
	    }, _parseMarkup: function _parseMarkup(b, c, d) {
	      var e;d.data && (c = a.extend(d.data, c)), y(l, [b, c, d]), a.each(c, function (a, c) {
	        if (void 0 === c || c === !1) return !0;if (e = a.split("_"), e.length > 1) {
	          var d = b.find(p + "-" + e[0]);if (d.length > 0) {
	            var f = e[1];"replaceWith" === f ? d[0] !== c[0] && d.replaceWith(c) : "img" === f ? d.is("img") ? d.attr("src", c) : d.replaceWith('<img src="' + c + '" class="' + d.attr("class") + '" />') : d.attr(e[1], c);
	          }
	        } else b.find(p + "-" + a).html(c);
	      });
	    }, _getScrollbarSize: function _getScrollbarSize() {
	      if (void 0 === b.scrollbarSize) {
	        var a = document.createElement("div");a.style.cssText = "width: 99px; height: 99px; overflow: scroll; position: absolute; top: -9999px;", document.body.appendChild(a), b.scrollbarSize = a.offsetWidth - a.clientWidth, document.body.removeChild(a);
	      }return b.scrollbarSize;
	    } }, a.magnificPopup = { instance: null, proto: t.prototype, modules: [], open: function open(b, c) {
	      return A(), b = b ? a.extend(!0, {}, b) : {}, b.isObj = !0, b.index = c || 0, this.instance.open(b);
	    }, close: function close() {
	      return a.magnificPopup.instance && a.magnificPopup.instance.close();
	    }, registerModule: function registerModule(b, c) {
	      c.options && (a.magnificPopup.defaults[b] = c.options), a.extend(this.proto, c.proto), this.modules.push(b);
	    }, defaults: { disableOn: 0, key: null, midClick: !1, mainClass: "", preloader: !0, focus: "", closeOnContentClick: !1, closeOnBgClick: !0, closeBtnInside: !0, showCloseBtn: !0, enableEscapeKey: !0, modal: !1, alignTop: !1, removalDelay: 0, prependTo: null, fixedContentPos: "auto", fixedBgPos: "auto", overflowY: "auto", closeMarkup: '<button title="%title%" type="button" class="mfp-close">&#215;</button>', tClose: "Close (Esc)", tLoading: "Loading...", autoFocusLast: !0 } }, a.fn.magnificPopup = function (c) {
	    A();var d = a(this);if ("string" == typeof c) {
	      if ("open" === c) {
	        var e,
	            f = u ? d.data("magnificPopup") : d[0].magnificPopup,
	            g = parseInt(arguments[1], 10) || 0;f.items ? e = f.items[g] : (e = d, f.delegate && (e = e.find(f.delegate)), e = e.eq(g)), b._openClick({ mfpEl: e }, d, f);
	      } else b.isOpen && b[c].apply(b, Array.prototype.slice.call(arguments, 1));
	    } else c = a.extend(!0, {}, c), u ? d.data("magnificPopup", c) : d[0].magnificPopup = c, b.addGroup(d, c);return d;
	  };var C,
	      D,
	      E,
	      F = "inline",
	      G = function G() {
	    E && (D.after(E.addClass(C)).detach(), E = null);
	  };a.magnificPopup.registerModule(F, { options: { hiddenClass: "hide", markup: "", tNotFound: "Content not found" }, proto: { initInline: function initInline() {
	        b.types.push(F), w(h + "." + F, function () {
	          G();
	        });
	      }, getInline: function getInline(c, d) {
	        if (G(), c.src) {
	          var e = b.st.inline,
	              f = a(c.src);if (f.length) {
	            var g = f[0].parentNode;g && g.tagName && (D || (C = e.hiddenClass, D = x(C), C = "mfp-" + C), E = f.after(D).detach().removeClass(C)), b.updateStatus("ready");
	          } else b.updateStatus("error", e.tNotFound), f = a("<div>");return c.inlineElement = f, f;
	        }return b.updateStatus("ready"), b._parseMarkup(d, {}, c), d;
	      } } });var H,
	      I = "ajax",
	      J = function J() {
	    H && a(document.body).removeClass(H);
	  },
	      K = function K() {
	    J(), b.req && b.req.abort();
	  };a.magnificPopup.registerModule(I, { options: { settings: null, cursor: "mfp-ajax-cur", tError: '<a href="%url%">The content</a> could not be loaded.' }, proto: { initAjax: function initAjax() {
	        b.types.push(I), H = b.st.ajax.cursor, w(h + "." + I, K), w("BeforeChange." + I, K);
	      }, getAjax: function getAjax(c) {
	        H && a(document.body).addClass(H), b.updateStatus("loading");var d = a.extend({ url: c.src, success: function success(d, e, f) {
	            var g = { data: d, xhr: f };y("ParseAjax", g), b.appendContent(a(g.data), I), c.finished = !0, J(), b._setFocus(), setTimeout(function () {
	              b.wrap.addClass(q);
	            }, 16), b.updateStatus("ready"), y("AjaxContentAdded");
	          }, error: function error() {
	            J(), c.finished = c.loadError = !0, b.updateStatus("error", b.st.ajax.tError.replace("%url%", c.src));
	          } }, b.st.ajax.settings);return b.req = a.ajax(d), "";
	      } } });var L,
	      M = function M(c) {
	    if (c.data && void 0 !== c.data.title) return c.data.title;var d = b.st.image.titleSrc;if (d) {
	      if (a.isFunction(d)) return d.call(b, c);if (c.el) return c.el.attr(d) || "";
	    }return "";
	  };a.magnificPopup.registerModule("image", { options: { markup: '<div class="mfp-figure"><div class="mfp-close"></div><figure><div class="mfp-img"></div><figcaption><div class="mfp-bottom-bar"><div class="mfp-title"></div><div class="mfp-counter"></div></div></figcaption></figure></div>', cursor: "mfp-zoom-out-cur", titleSrc: "title", verticalFit: !0, tError: '<a href="%url%">The image</a> could not be loaded.' }, proto: { initImage: function initImage() {
	        var c = b.st.image,
	            d = ".image";b.types.push("image"), w(m + d, function () {
	          "image" === b.currItem.type && c.cursor && a(document.body).addClass(c.cursor);
	        }), w(h + d, function () {
	          c.cursor && a(document.body).removeClass(c.cursor), v.off("resize" + p);
	        }), w("Resize" + d, b.resizeImage), b.isLowIE && w("AfterChange", b.resizeImage);
	      }, resizeImage: function resizeImage() {
	        var a = b.currItem;if (a && a.img && b.st.image.verticalFit) {
	          var c = 0;b.isLowIE && (c = parseInt(a.img.css("padding-top"), 10) + parseInt(a.img.css("padding-bottom"), 10)), a.img.css("max-height", b.wH - c);
	        }
	      }, _onImageHasSize: function _onImageHasSize(a) {
	        a.img && (a.hasSize = !0, L && clearInterval(L), a.isCheckingImgSize = !1, y("ImageHasSize", a), a.imgHidden && (b.content && b.content.removeClass("mfp-loading"), a.imgHidden = !1));
	      }, findImageSize: function findImageSize(a) {
	        var c = 0,
	            d = a.img[0],
	            e = function e(f) {
	          L && clearInterval(L), L = setInterval(function () {
	            return d.naturalWidth > 0 ? void b._onImageHasSize(a) : (c > 200 && clearInterval(L), c++, void (3 === c ? e(10) : 40 === c ? e(50) : 100 === c && e(500)));
	          }, f);
	        };e(1);
	      }, getImage: function getImage(c, d) {
	        var e = 0,
	            f = function f() {
	          c && (c.img[0].complete ? (c.img.off(".mfploader"), c === b.currItem && (b._onImageHasSize(c), b.updateStatus("ready")), c.hasSize = !0, c.loaded = !0, y("ImageLoadComplete")) : (e++, 200 > e ? setTimeout(f, 100) : g()));
	        },
	            g = function g() {
	          c && (c.img.off(".mfploader"), c === b.currItem && (b._onImageHasSize(c), b.updateStatus("error", h.tError.replace("%url%", c.src))), c.hasSize = !0, c.loaded = !0, c.loadError = !0);
	        },
	            h = b.st.image,
	            i = d.find(".mfp-img");if (i.length) {
	          var j = document.createElement("img");j.className = "mfp-img", c.el && c.el.find("img").length && (j.alt = c.el.find("img").attr("alt")), c.img = a(j).on("load.mfploader", f).on("error.mfploader", g), j.src = c.src, i.is("img") && (c.img = c.img.clone()), j = c.img[0], j.naturalWidth > 0 ? c.hasSize = !0 : j.width || (c.hasSize = !1);
	        }return b._parseMarkup(d, { title: M(c), img_replaceWith: c.img }, c), b.resizeImage(), c.hasSize ? (L && clearInterval(L), c.loadError ? (d.addClass("mfp-loading"), b.updateStatus("error", h.tError.replace("%url%", c.src))) : (d.removeClass("mfp-loading"), b.updateStatus("ready")), d) : (b.updateStatus("loading"), c.loading = !0, c.hasSize || (c.imgHidden = !0, d.addClass("mfp-loading"), b.findImageSize(c)), d);
	      } } });var N,
	      O = function O() {
	    return void 0 === N && (N = void 0 !== document.createElement("p").style.MozTransform), N;
	  };a.magnificPopup.registerModule("zoom", { options: { enabled: !1, easing: "ease-in-out", duration: 300, opener: function opener(a) {
	        return a.is("img") ? a : a.find("img");
	      } }, proto: { initZoom: function initZoom() {
	        var a,
	            c = b.st.zoom,
	            d = ".zoom";if (c.enabled && b.supportsTransition) {
	          var e,
	              f,
	              g = c.duration,
	              j = function j(a) {
	            var b = a.clone().removeAttr("style").removeAttr("class").addClass("mfp-animated-image"),
	                d = "all " + c.duration / 1e3 + "s " + c.easing,
	                e = { position: "fixed", zIndex: 9999, left: 0, top: 0, "-webkit-backface-visibility": "hidden" },
	                f = "transition";return e["-webkit-" + f] = e["-moz-" + f] = e["-o-" + f] = e[f] = d, b.css(e), b;
	          },
	              k = function k() {
	            b.content.css("visibility", "visible");
	          };w("BuildControls" + d, function () {
	            if (b._allowZoom()) {
	              if (clearTimeout(e), b.content.css("visibility", "hidden"), a = b._getItemToZoom(), !a) return void k();f = j(a), f.css(b._getOffset()), b.wrap.append(f), e = setTimeout(function () {
	                f.css(b._getOffset(!0)), e = setTimeout(function () {
	                  k(), setTimeout(function () {
	                    f.remove(), a = f = null, y("ZoomAnimationEnded");
	                  }, 16);
	                }, g);
	              }, 16);
	            }
	          }), w(i + d, function () {
	            if (b._allowZoom()) {
	              if (clearTimeout(e), b.st.removalDelay = g, !a) {
	                if (a = b._getItemToZoom(), !a) return;f = j(a);
	              }f.css(b._getOffset(!0)), b.wrap.append(f), b.content.css("visibility", "hidden"), setTimeout(function () {
	                f.css(b._getOffset());
	              }, 16);
	            }
	          }), w(h + d, function () {
	            b._allowZoom() && (k(), f && f.remove(), a = null);
	          });
	        }
	      }, _allowZoom: function _allowZoom() {
	        return "image" === b.currItem.type;
	      }, _getItemToZoom: function _getItemToZoom() {
	        return b.currItem.hasSize ? b.currItem.img : !1;
	      }, _getOffset: function _getOffset(c) {
	        var d;d = c ? b.currItem.img : b.st.zoom.opener(b.currItem.el || b.currItem);var e = d.offset(),
	            f = parseInt(d.css("padding-top"), 10),
	            g = parseInt(d.css("padding-bottom"), 10);e.top -= a(window).scrollTop() - f;var h = { width: d.width(), height: (u ? d.innerHeight() : d[0].offsetHeight) - g - f };return O() ? h["-moz-transform"] = h.transform = "translate(" + e.left + "px," + e.top + "px)" : (h.left = e.left, h.top = e.top), h;
	      } } });var P = "iframe",
	      Q = "//about:blank",
	      R = function R(a) {
	    if (b.currTemplate[P]) {
	      var c = b.currTemplate[P].find("iframe");c.length && (a || (c[0].src = Q), b.isIE8 && c.css("display", a ? "block" : "none"));
	    }
	  };a.magnificPopup.registerModule(P, { options: { markup: '<div class="mfp-iframe-scaler"><div class="mfp-close"></div><iframe class="mfp-iframe" src="//about:blank" frameborder="0" allowfullscreen></iframe></div>', srcAction: "iframe_src", patterns: { youtube: { index: "youtube.com", id: "v=", src: "//www.youtube.com/embed/%id%?autoplay=1" }, vimeo: { index: "vimeo.com/", id: "/", src: "//player.vimeo.com/video/%id%?autoplay=1" }, gmaps: { index: "//maps.google.", src: "%id%&output=embed" } } }, proto: { initIframe: function initIframe() {
	        b.types.push(P), w("BeforeChange", function (a, b, c) {
	          b !== c && (b === P ? R() : c === P && R(!0));
	        }), w(h + "." + P, function () {
	          R();
	        });
	      }, getIframe: function getIframe(c, d) {
	        var e = c.src,
	            f = b.st.iframe;a.each(f.patterns, function () {
	          return e.indexOf(this.index) > -1 ? (this.id && (e = "string" == typeof this.id ? e.substr(e.lastIndexOf(this.id) + this.id.length, e.length) : this.id.call(this, e)), e = this.src.replace("%id%", e), !1) : void 0;
	        });var g = {};return f.srcAction && (g[f.srcAction] = e), b._parseMarkup(d, g, c), b.updateStatus("ready"), d;
	      } } });var S = function S(a) {
	    var c = b.items.length;return a > c - 1 ? a - c : 0 > a ? c + a : a;
	  },
	      T = function T(a, b, c) {
	    return a.replace(/%curr%/gi, b + 1).replace(/%total%/gi, c);
	  };a.magnificPopup.registerModule("gallery", { options: { enabled: !1, arrowMarkup: '<button title="%title%" type="button" class="mfp-arrow mfp-arrow-%dir%"></button>', preload: [0, 2], navigateByImgClick: !0, arrows: !0, tPrev: "Previous (Left arrow key)", tNext: "Next (Right arrow key)", tCounter: "%curr% of %total%" }, proto: { initGallery: function initGallery() {
	        var c = b.st.gallery,
	            e = ".mfp-gallery",
	            g = Boolean(a.fn.mfpFastClick);return b.direction = !0, c && c.enabled ? (f += " mfp-gallery", w(m + e, function () {
	          c.navigateByImgClick && b.wrap.on("click" + e, ".mfp-img", function () {
	            return b.items.length > 1 ? (b.next(), !1) : void 0;
	          }), d.on("keydown" + e, function (a) {
	            37 === a.keyCode ? b.prev() : 39 === a.keyCode && b.next();
	          });
	        }), w("UpdateStatus" + e, function (a, c) {
	          c.text && (c.text = T(c.text, b.currItem.index, b.items.length));
	        }), w(l + e, function (a, d, e, f) {
	          var g = b.items.length;e.counter = g > 1 ? T(c.tCounter, f.index, g) : "";
	        }), w("BuildControls" + e, function () {
	          if (b.items.length > 1 && c.arrows && !b.arrowLeft) {
	            var d = c.arrowMarkup,
	                e = b.arrowLeft = a(d.replace(/%title%/gi, c.tPrev).replace(/%dir%/gi, "left")).addClass(s),
	                f = b.arrowRight = a(d.replace(/%title%/gi, c.tNext).replace(/%dir%/gi, "right")).addClass(s),
	                h = g ? "mfpFastClick" : "click";e[h](function () {
	              b.prev();
	            }), f[h](function () {
	              b.next();
	            }), b.isIE7 && (x("b", e[0], !1, !0), x("a", e[0], !1, !0), x("b", f[0], !1, !0), x("a", f[0], !1, !0)), b.container.append(e.add(f));
	          }
	        }), w(n + e, function () {
	          b._preloadTimeout && clearTimeout(b._preloadTimeout), b._preloadTimeout = setTimeout(function () {
	            b.preloadNearbyImages(), b._preloadTimeout = null;
	          }, 16);
	        }), void w(h + e, function () {
	          d.off(e), b.wrap.off("click" + e), b.arrowLeft && g && b.arrowLeft.add(b.arrowRight).destroyMfpFastClick(), b.arrowRight = b.arrowLeft = null;
	        })) : !1;
	      }, next: function next() {
	        b.direction = !0, b.index = S(b.index + 1), b.updateItemHTML();
	      }, prev: function prev() {
	        b.direction = !1, b.index = S(b.index - 1), b.updateItemHTML();
	      }, goTo: function goTo(a) {
	        b.direction = a >= b.index, b.index = a, b.updateItemHTML();
	      }, preloadNearbyImages: function preloadNearbyImages() {
	        var a,
	            c = b.st.gallery.preload,
	            d = Math.min(c[0], b.items.length),
	            e = Math.min(c[1], b.items.length);for (a = 1; a <= (b.direction ? e : d); a++) {
	          b._preloadItem(b.index + a);
	        }for (a = 1; a <= (b.direction ? d : e); a++) {
	          b._preloadItem(b.index - a);
	        }
	      }, _preloadItem: function _preloadItem(c) {
	        if (c = S(c), !b.items[c].preloaded) {
	          var d = b.items[c];d.parsed || (d = b.parseEl(c)), y("LazyLoad", d), "image" === d.type && (d.img = a('<img class="mfp-img" />').on("load.mfploader", function () {
	            d.hasSize = !0;
	          }).on("error.mfploader", function () {
	            d.hasSize = !0, d.loadError = !0, y("LazyLoadError", d);
	          }).attr("src", d.src)), d.preloaded = !0;
	        }
	      } } });var U = "retina";a.magnificPopup.registerModule(U, { options: { replaceSrc: function replaceSrc(a) {
	        return a.src.replace(/\.\w+$/, function (a) {
	          return "@2x" + a;
	        });
	      }, ratio: 1 }, proto: { initRetina: function initRetina() {
	        if (window.devicePixelRatio > 1) {
	          var a = b.st.retina,
	              c = a.ratio;c = isNaN(c) ? c() : c, c > 1 && (w("ImageHasSize." + U, function (a, b) {
	            b.img.css({ "max-width": b.img[0].naturalWidth / c, width: "100%" });
	          }), w("ElementParse." + U, function (b, d) {
	            d.src = a.replaceSrc(d, c);
	          }));
	        }
	      } } }), function () {
	    var b = 1e3,
	        c = "ontouchstart" in window,
	        d = function d() {
	      v.off("touchmove" + f + " touchend" + f);
	    },
	        e = "mfpFastClick",
	        f = "." + e;a.fn.mfpFastClick = function (e) {
	      return a(this).each(function () {
	        var g,
	            h = a(this);if (c) {
	          var i, j, k, l, m, n;h.on("touchstart" + f, function (a) {
	            l = !1, n = 1, m = a.originalEvent ? a.originalEvent.touches[0] : a.touches[0], j = m.clientX, k = m.clientY, v.on("touchmove" + f, function (a) {
	              m = a.originalEvent ? a.originalEvent.touches : a.touches, n = m.length, m = m[0], (Math.abs(m.clientX - j) > 10 || Math.abs(m.clientY - k) > 10) && (l = !0, d());
	            }).on("touchend" + f, function (a) {
	              d(), l || n > 1 || (g = !0, a.preventDefault(), clearTimeout(i), i = setTimeout(function () {
	                g = !1;
	              }, b), e());
	            });
	          });
	        }h.on("click" + f, function () {
	          g || e();
	        });
	      });
	    }, a.fn.destroyMfpFastClick = function () {
	      a(this).off("touchstart" + f + " click" + f), c && v.off("touchmove" + f + " touchend" + f);
	    };
	  }(), A();
	});

/***/ },
/* 11 */
/***/ function(module, exports, __webpack_require__) {

	var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;"use strict";

	var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol ? "symbol" : typeof obj; };

	/*! Select2 4.0.1 | https://github.com/select2/select2/blob/master/LICENSE.md */!function (a) {
	   true ? !(__WEBPACK_AMD_DEFINE_ARRAY__ = [__webpack_require__(4)], __WEBPACK_AMD_DEFINE_FACTORY__ = (a), __WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ? (__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__), __WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__)) : a("object" == (typeof exports === "undefined" ? "undefined" : _typeof(exports)) ? require("jquery") : jQuery);
	}(function (a) {
	  var b = function () {
	    if (a && a.fn && a.fn.select2 && a.fn.select2.amd) var b = a.fn.select2.amd;var b;return function () {
	      if (!b || !b.requirejs) {
	        b ? c = b : b = {};var a, c, d;!function (b) {
	          function e(a, b) {
	            return u.call(a, b);
	          }function f(a, b) {
	            var c,
	                d,
	                e,
	                f,
	                g,
	                h,
	                i,
	                j,
	                k,
	                l,
	                m,
	                n = b && b.split("/"),
	                o = s.map,
	                p = o && o["*"] || {};if (a && "." === a.charAt(0)) if (b) {
	              for (a = a.split("/"), g = a.length - 1, s.nodeIdCompat && w.test(a[g]) && (a[g] = a[g].replace(w, "")), a = n.slice(0, n.length - 1).concat(a), k = 0; k < a.length; k += 1) {
	                if (m = a[k], "." === m) a.splice(k, 1), k -= 1;else if (".." === m) {
	                  if (1 === k && (".." === a[2] || ".." === a[0])) break;k > 0 && (a.splice(k - 1, 2), k -= 2);
	                }
	              }a = a.join("/");
	            } else 0 === a.indexOf("./") && (a = a.substring(2));if ((n || p) && o) {
	              for (c = a.split("/"), k = c.length; k > 0; k -= 1) {
	                if (d = c.slice(0, k).join("/"), n) for (l = n.length; l > 0; l -= 1) {
	                  if (e = o[n.slice(0, l).join("/")], e && (e = e[d])) {
	                    f = e, h = k;break;
	                  }
	                }if (f) break;!i && p && p[d] && (i = p[d], j = k);
	              }!f && i && (f = i, h = j), f && (c.splice(0, h, f), a = c.join("/"));
	            }return a;
	          }function g(a, c) {
	            return function () {
	              var d = v.call(arguments, 0);return "string" != typeof d[0] && 1 === d.length && d.push(null), _n.apply(b, d.concat([a, c]));
	            };
	          }function h(a) {
	            return function (b) {
	              return f(b, a);
	            };
	          }function i(a) {
	            return function (b) {
	              q[a] = b;
	            };
	          }function j(a) {
	            if (e(r, a)) {
	              var c = r[a];delete r[a], t[a] = !0, m.apply(b, c);
	            }if (!e(q, a) && !e(t, a)) throw new Error("No " + a);return q[a];
	          }function k(a) {
	            var b,
	                c = a ? a.indexOf("!") : -1;return c > -1 && (b = a.substring(0, c), a = a.substring(c + 1, a.length)), [b, a];
	          }function l(a) {
	            return function () {
	              return s && s.config && s.config[a] || {};
	            };
	          }var m,
	              _n,
	              o,
	              p,
	              q = {},
	              r = {},
	              s = {},
	              t = {},
	              u = Object.prototype.hasOwnProperty,
	              v = [].slice,
	              w = /\.js$/;o = function o(a, b) {
	            var c,
	                d = k(a),
	                e = d[0];return a = d[1], e && (e = f(e, b), c = j(e)), e ? a = c && c.normalize ? c.normalize(a, h(b)) : f(a, b) : (a = f(a, b), d = k(a), e = d[0], a = d[1], e && (c = j(e))), { f: e ? e + "!" + a : a, n: a, pr: e, p: c };
	          }, p = { require: function require(a) {
	              return g(a);
	            }, exports: function exports(a) {
	              var b = q[a];return "undefined" != typeof b ? b : q[a] = {};
	            }, module: function module(a) {
	              return { id: a, uri: "", exports: q[a], config: l(a) };
	            } }, m = function m(a, c, d, f) {
	            var h,
	                k,
	                l,
	                m,
	                n,
	                s,
	                u = [],
	                v = typeof d === "undefined" ? "undefined" : _typeof(d);if (f = f || a, "undefined" === v || "function" === v) {
	              for (c = !c.length && d.length ? ["require", "exports", "module"] : c, n = 0; n < c.length; n += 1) {
	                if (m = o(c[n], f), k = m.f, "require" === k) u[n] = p.require(a);else if ("exports" === k) u[n] = p.exports(a), s = !0;else if ("module" === k) h = u[n] = p.module(a);else if (e(q, k) || e(r, k) || e(t, k)) u[n] = j(k);else {
	                  if (!m.p) throw new Error(a + " missing " + k);m.p.load(m.n, g(f, !0), i(k), {}), u[n] = q[k];
	                }
	              }l = d ? d.apply(q[a], u) : void 0, a && (h && h.exports !== b && h.exports !== q[a] ? q[a] = h.exports : l === b && s || (q[a] = l));
	            } else a && (q[a] = d);
	          }, a = c = _n = function n(a, c, d, e, f) {
	            if ("string" == typeof a) return p[a] ? p[a](c) : j(o(a, c).f);if (!a.splice) {
	              if (s = a, s.deps && _n(s.deps, s.callback), !c) return;c.splice ? (a = c, c = d, d = null) : a = b;
	            }return c = c || function () {}, "function" == typeof d && (d = e, e = f), e ? m(b, a, c, d) : setTimeout(function () {
	              m(b, a, c, d);
	            }, 4), _n;
	          }, _n.config = function (a) {
	            return _n(a);
	          }, a._defined = q, d = function d(a, b, c) {
	            if ("string" != typeof a) throw new Error("See almond README: incorrect module build, no module name");b.splice || (c = b, b = []), e(q, a) || e(r, a) || (r[a] = [a, b, c]);
	          }, d.amd = { jQuery: !0 };
	        }(), b.requirejs = a, b.require = c, b.define = d;
	      }
	    }(), b.define("almond", function () {}), b.define("jquery", [], function () {
	      var b = a || $;return null == b && console && console.error && console.error("Select2: An instance of jQuery or a jQuery-compatible library was not found. Make sure that you are including jQuery before Select2 on your web page."), b;
	    }), b.define("select2/utils", ["jquery"], function (a) {
	      function b(a) {
	        var b = a.prototype,
	            c = [];for (var d in b) {
	          var e = b[d];"function" == typeof e && "constructor" !== d && c.push(d);
	        }return c;
	      }var c = {};c.Extend = function (a, b) {
	        function c() {
	          this.constructor = a;
	        }var d = {}.hasOwnProperty;for (var e in b) {
	          d.call(b, e) && (a[e] = b[e]);
	        }return c.prototype = b.prototype, a.prototype = new c(), a.__super__ = b.prototype, a;
	      }, c.Decorate = function (a, c) {
	        function d() {
	          var b = Array.prototype.unshift,
	              d = c.prototype.constructor.length,
	              e = a.prototype.constructor;d > 0 && (b.call(arguments, a.prototype.constructor), e = c.prototype.constructor), e.apply(this, arguments);
	        }function e() {
	          this.constructor = d;
	        }var f = b(c),
	            g = b(a);c.displayName = a.displayName, d.prototype = new e();for (var h = 0; h < g.length; h++) {
	          var i = g[h];d.prototype[i] = a.prototype[i];
	        }for (var j = function j(a) {
	          var b = function b() {};(a in d.prototype) && (b = d.prototype[a]);var e = c.prototype[a];return function () {
	            var a = Array.prototype.unshift;return a.call(arguments, b), e.apply(this, arguments);
	          };
	        }, k = 0; k < f.length; k++) {
	          var l = f[k];d.prototype[l] = j(l);
	        }return d;
	      };var d = function d() {
	        this.listeners = {};
	      };return d.prototype.on = function (a, b) {
	        this.listeners = this.listeners || {}, a in this.listeners ? this.listeners[a].push(b) : this.listeners[a] = [b];
	      }, d.prototype.trigger = function (a) {
	        var b = Array.prototype.slice;this.listeners = this.listeners || {}, a in this.listeners && this.invoke(this.listeners[a], b.call(arguments, 1)), "*" in this.listeners && this.invoke(this.listeners["*"], arguments);
	      }, d.prototype.invoke = function (a, b) {
	        for (var c = 0, d = a.length; d > c; c++) {
	          a[c].apply(this, b);
	        }
	      }, c.Observable = d, c.generateChars = function (a) {
	        for (var b = "", c = 0; a > c; c++) {
	          var d = Math.floor(36 * Math.random());b += d.toString(36);
	        }return b;
	      }, c.bind = function (a, b) {
	        return function () {
	          a.apply(b, arguments);
	        };
	      }, c._convertData = function (a) {
	        for (var b in a) {
	          var c = b.split("-"),
	              d = a;if (1 !== c.length) {
	            for (var e = 0; e < c.length; e++) {
	              var f = c[e];f = f.substring(0, 1).toLowerCase() + f.substring(1), f in d || (d[f] = {}), e == c.length - 1 && (d[f] = a[b]), d = d[f];
	            }delete a[b];
	          }
	        }return a;
	      }, c.hasScroll = function (b, c) {
	        var d = a(c),
	            e = c.style.overflowX,
	            f = c.style.overflowY;return e !== f || "hidden" !== f && "visible" !== f ? "scroll" === e || "scroll" === f ? !0 : d.innerHeight() < c.scrollHeight || d.innerWidth() < c.scrollWidth : !1;
	      }, c.escapeMarkup = function (a) {
	        var b = { "\\": "&#92;", "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;", "/": "&#47;" };return "string" != typeof a ? a : String(a).replace(/[&<>"'\/\\]/g, function (a) {
	          return b[a];
	        });
	      }, c.appendMany = function (b, c) {
	        if ("1.7" === a.fn.jquery.substr(0, 3)) {
	          var d = a();a.map(c, function (a) {
	            d = d.add(a);
	          }), c = d;
	        }b.append(c);
	      }, c;
	    }), b.define("select2/results", ["jquery", "./utils"], function (a, b) {
	      function c(a, b, d) {
	        this.$element = a, this.data = d, this.options = b, c.__super__.constructor.call(this);
	      }return b.Extend(c, b.Observable), c.prototype.render = function () {
	        var b = a('<ul class="select2-results__options" role="tree"></ul>');return this.options.get("multiple") && b.attr("aria-multiselectable", "true"), this.$results = b, b;
	      }, c.prototype.clear = function () {
	        this.$results.empty();
	      }, c.prototype.displayMessage = function (b) {
	        var c = this.options.get("escapeMarkup");this.clear(), this.hideLoading();var d = a('<li role="treeitem" aria-live="assertive" class="select2-results__option"></li>'),
	            e = this.options.get("translations").get(b.message);d.append(c(e(b.args))), d[0].className += " select2-results__message", this.$results.append(d);
	      }, c.prototype.hideMessages = function () {
	        this.$results.find(".select2-results__message").remove();
	      }, c.prototype.append = function (a) {
	        this.hideLoading();var b = [];if (null == a.results || 0 === a.results.length) return void (0 === this.$results.children().length && this.trigger("results:message", { message: "noResults" }));a.results = this.sort(a.results);for (var c = 0; c < a.results.length; c++) {
	          var d = a.results[c],
	              e = this.option(d);b.push(e);
	        }this.$results.append(b);
	      }, c.prototype.position = function (a, b) {
	        var c = b.find(".select2-results");c.append(a);
	      }, c.prototype.sort = function (a) {
	        var b = this.options.get("sorter");return b(a);
	      }, c.prototype.setClasses = function () {
	        var b = this;this.data.current(function (c) {
	          var d = a.map(c, function (a) {
	            return a.id.toString();
	          }),
	              e = b.$results.find(".select2-results__option[aria-selected]");e.each(function () {
	            var b = a(this),
	                c = a.data(this, "data"),
	                e = "" + c.id;null != c.element && c.element.selected || null == c.element && a.inArray(e, d) > -1 ? b.attr("aria-selected", "true") : b.attr("aria-selected", "false");
	          });var f = e.filter("[aria-selected=true]");f.length > 0 ? f.first().trigger("mouseenter") : e.first().trigger("mouseenter");
	        });
	      }, c.prototype.showLoading = function (a) {
	        this.hideLoading();var b = this.options.get("translations").get("searching"),
	            c = { disabled: !0, loading: !0, text: b(a) },
	            d = this.option(c);d.className += " loading-results", this.$results.prepend(d);
	      }, c.prototype.hideLoading = function () {
	        this.$results.find(".loading-results").remove();
	      }, c.prototype.option = function (b) {
	        var c = document.createElement("li");c.className = "select2-results__option";var d = { role: "treeitem", "aria-selected": "false" };b.disabled && (delete d["aria-selected"], d["aria-disabled"] = "true"), null == b.id && delete d["aria-selected"], null != b._resultId && (c.id = b._resultId), b.title && (c.title = b.title), b.children && (d.role = "group", d["aria-label"] = b.text, delete d["aria-selected"]);for (var e in d) {
	          var f = d[e];c.setAttribute(e, f);
	        }if (b.children) {
	          var g = a(c),
	              h = document.createElement("strong");h.className = "select2-results__group";a(h);this.template(b, h);for (var i = [], j = 0; j < b.children.length; j++) {
	            var k = b.children[j],
	                l = this.option(k);i.push(l);
	          }var m = a("<ul></ul>", { "class": "select2-results__options select2-results__options--nested" });m.append(i), g.append(h), g.append(m);
	        } else this.template(b, c);return a.data(c, "data", b), c;
	      }, c.prototype.bind = function (b, c) {
	        var d = this,
	            e = b.id + "-results";this.$results.attr("id", e), b.on("results:all", function (a) {
	          d.clear(), d.append(a.data), b.isOpen() && d.setClasses();
	        }), b.on("results:append", function (a) {
	          d.append(a.data), b.isOpen() && d.setClasses();
	        }), b.on("query", function (a) {
	          d.hideMessages(), d.showLoading(a);
	        }), b.on("select", function () {
	          b.isOpen() && d.setClasses();
	        }), b.on("unselect", function () {
	          b.isOpen() && d.setClasses();
	        }), b.on("open", function () {
	          d.$results.attr("aria-expanded", "true"), d.$results.attr("aria-hidden", "false"), d.setClasses(), d.ensureHighlightVisible();
	        }), b.on("close", function () {
	          d.$results.attr("aria-expanded", "false"), d.$results.attr("aria-hidden", "true"), d.$results.removeAttr("aria-activedescendant");
	        }), b.on("results:toggle", function () {
	          var a = d.getHighlightedResults();0 !== a.length && a.trigger("mouseup");
	        }), b.on("results:select", function () {
	          var a = d.getHighlightedResults();if (0 !== a.length) {
	            var b = a.data("data");"true" == a.attr("aria-selected") ? d.trigger("close", {}) : d.trigger("select", { data: b });
	          }
	        }), b.on("results:previous", function () {
	          var a = d.getHighlightedResults(),
	              b = d.$results.find("[aria-selected]"),
	              c = b.index(a);if (0 !== c) {
	            var e = c - 1;0 === a.length && (e = 0);var f = b.eq(e);f.trigger("mouseenter");var g = d.$results.offset().top,
	                h = f.offset().top,
	                i = d.$results.scrollTop() + (h - g);0 === e ? d.$results.scrollTop(0) : 0 > h - g && d.$results.scrollTop(i);
	          }
	        }), b.on("results:next", function () {
	          var a = d.getHighlightedResults(),
	              b = d.$results.find("[aria-selected]"),
	              c = b.index(a),
	              e = c + 1;if (!(e >= b.length)) {
	            var f = b.eq(e);f.trigger("mouseenter");var g = d.$results.offset().top + d.$results.outerHeight(!1),
	                h = f.offset().top + f.outerHeight(!1),
	                i = d.$results.scrollTop() + h - g;0 === e ? d.$results.scrollTop(0) : h > g && d.$results.scrollTop(i);
	          }
	        }), b.on("results:focus", function (a) {
	          a.element.addClass("select2-results__option--highlighted");
	        }), b.on("results:message", function (a) {
	          d.displayMessage(a);
	        }), a.fn.mousewheel && this.$results.on("mousewheel", function (a) {
	          var b = d.$results.scrollTop(),
	              c = d.$results.get(0).scrollHeight - d.$results.scrollTop() + a.deltaY,
	              e = a.deltaY > 0 && b - a.deltaY <= 0,
	              f = a.deltaY < 0 && c <= d.$results.height();e ? (d.$results.scrollTop(0), a.preventDefault(), a.stopPropagation()) : f && (d.$results.scrollTop(d.$results.get(0).scrollHeight - d.$results.height()), a.preventDefault(), a.stopPropagation());
	        }), this.$results.on("mouseup", ".select2-results__option[aria-selected]", function (b) {
	          var c = a(this),
	              e = c.data("data");return "true" === c.attr("aria-selected") ? void (d.options.get("multiple") ? d.trigger("unselect", { originalEvent: b, data: e }) : d.trigger("close", {})) : void d.trigger("select", { originalEvent: b, data: e });
	        }), this.$results.on("mouseenter", ".select2-results__option[aria-selected]", function (b) {
	          var c = a(this).data("data");d.getHighlightedResults().removeClass("select2-results__option--highlighted"), d.trigger("results:focus", { data: c, element: a(this) });
	        });
	      }, c.prototype.getHighlightedResults = function () {
	        var a = this.$results.find(".select2-results__option--highlighted");return a;
	      }, c.prototype.destroy = function () {
	        this.$results.remove();
	      }, c.prototype.ensureHighlightVisible = function () {
	        var a = this.getHighlightedResults();if (0 !== a.length) {
	          var b = this.$results.find("[aria-selected]"),
	              c = b.index(a),
	              d = this.$results.offset().top,
	              e = a.offset().top,
	              f = this.$results.scrollTop() + (e - d),
	              g = e - d;f -= 2 * a.outerHeight(!1), 2 >= c ? this.$results.scrollTop(0) : (g > this.$results.outerHeight() || 0 > g) && this.$results.scrollTop(f);
	        }
	      }, c.prototype.template = function (b, c) {
	        var d = this.options.get("templateResult"),
	            e = this.options.get("escapeMarkup"),
	            f = d(b, c);null == f ? c.style.display = "none" : "string" == typeof f ? c.innerHTML = e(f) : a(c).append(f);
	      }, c;
	    }), b.define("select2/keys", [], function () {
	      var a = { BACKSPACE: 8, TAB: 9, ENTER: 13, SHIFT: 16, CTRL: 17, ALT: 18, ESC: 27, SPACE: 32, PAGE_UP: 33, PAGE_DOWN: 34, END: 35, HOME: 36, LEFT: 37, UP: 38, RIGHT: 39, DOWN: 40, DELETE: 46 };return a;
	    }), b.define("select2/selection/base", ["jquery", "../utils", "../keys"], function (a, b, c) {
	      function d(a, b) {
	        this.$element = a, this.options = b, d.__super__.constructor.call(this);
	      }return b.Extend(d, b.Observable), d.prototype.render = function () {
	        var b = a('<span class="select2-selection" role="combobox"  aria-haspopup="true" aria-expanded="false"></span>');return this._tabindex = 0, null != this.$element.data("old-tabindex") ? this._tabindex = this.$element.data("old-tabindex") : null != this.$element.attr("tabindex") && (this._tabindex = this.$element.attr("tabindex")), b.attr("title", this.$element.attr("title")), b.attr("tabindex", this._tabindex), this.$selection = b, b;
	      }, d.prototype.bind = function (a, b) {
	        var d = this,
	            e = (a.id + "-container", a.id + "-results");this.container = a, this.$selection.on("focus", function (a) {
	          d.trigger("focus", a);
	        }), this.$selection.on("blur", function (a) {
	          d._handleBlur(a);
	        }), this.$selection.on("keydown", function (a) {
	          d.trigger("keypress", a), a.which === c.SPACE && a.preventDefault();
	        }), a.on("results:focus", function (a) {
	          d.$selection.attr("aria-activedescendant", a.data._resultId);
	        }), a.on("selection:update", function (a) {
	          d.update(a.data);
	        }), a.on("open", function () {
	          d.$selection.attr("aria-expanded", "true"), d.$selection.attr("aria-owns", e), d._attachCloseHandler(a);
	        }), a.on("close", function () {
	          d.$selection.attr("aria-expanded", "false"), d.$selection.removeAttr("aria-activedescendant"), d.$selection.removeAttr("aria-owns"), d.$selection.focus(), d._detachCloseHandler(a);
	        }), a.on("enable", function () {
	          d.$selection.attr("tabindex", d._tabindex);
	        }), a.on("disable", function () {
	          d.$selection.attr("tabindex", "-1");
	        });
	      }, d.prototype._handleBlur = function (b) {
	        var c = this;window.setTimeout(function () {
	          document.activeElement == c.$selection[0] || a.contains(c.$selection[0], document.activeElement) || c.trigger("blur", b);
	        }, 1);
	      }, d.prototype._attachCloseHandler = function (b) {
	        a(document.body).on("mousedown.select2." + b.id, function (b) {
	          var c = a(b.target),
	              d = c.closest(".select2"),
	              e = a(".select2.select2-container--open");e.each(function () {
	            var b = a(this);if (this != d[0]) {
	              var c = b.data("element");c.select2("close");
	            }
	          });
	        });
	      }, d.prototype._detachCloseHandler = function (b) {
	        a(document.body).off("mousedown.select2." + b.id);
	      }, d.prototype.position = function (a, b) {
	        var c = b.find(".selection");c.append(a);
	      }, d.prototype.destroy = function () {
	        this._detachCloseHandler(this.container);
	      }, d.prototype.update = function (a) {
	        throw new Error("The `update` method must be defined in child classes.");
	      }, d;
	    }), b.define("select2/selection/single", ["jquery", "./base", "../utils", "../keys"], function (a, b, c, d) {
	      function e() {
	        e.__super__.constructor.apply(this, arguments);
	      }return c.Extend(e, b), e.prototype.render = function () {
	        var a = e.__super__.render.call(this);return a.addClass("select2-selection--single"), a.html('<span class="select2-selection__rendered"></span><span class="select2-selection__arrow" role="presentation"><b role="presentation"></b></span>'), a;
	      }, e.prototype.bind = function (a, b) {
	        var c = this;e.__super__.bind.apply(this, arguments);var d = a.id + "-container";this.$selection.find(".select2-selection__rendered").attr("id", d), this.$selection.attr("aria-labelledby", d), this.$selection.on("mousedown", function (a) {
	          1 === a.which && c.trigger("toggle", { originalEvent: a });
	        }), this.$selection.on("focus", function (a) {}), this.$selection.on("blur", function (a) {}), a.on("selection:update", function (a) {
	          c.update(a.data);
	        });
	      }, e.prototype.clear = function () {
	        this.$selection.find(".select2-selection__rendered").empty();
	      }, e.prototype.display = function (a, b) {
	        var c = this.options.get("templateSelection"),
	            d = this.options.get("escapeMarkup");return d(c(a, b));
	      }, e.prototype.selectionContainer = function () {
	        return a("<span></span>");
	      }, e.prototype.update = function (a) {
	        if (0 === a.length) return void this.clear();var b = a[0],
	            c = this.$selection.find(".select2-selection__rendered"),
	            d = this.display(b, c);c.empty().append(d), c.prop("title", b.title || b.text);
	      }, e;
	    }), b.define("select2/selection/multiple", ["jquery", "./base", "../utils"], function (a, b, c) {
	      function d(a, b) {
	        d.__super__.constructor.apply(this, arguments);
	      }return c.Extend(d, b), d.prototype.render = function () {
	        var a = d.__super__.render.call(this);return a.addClass("select2-selection--multiple"), a.html('<ul class="select2-selection__rendered"></ul>'), a;
	      }, d.prototype.bind = function (b, c) {
	        var e = this;d.__super__.bind.apply(this, arguments), this.$selection.on("click", function (a) {
	          e.trigger("toggle", { originalEvent: a });
	        }), this.$selection.on("click", ".select2-selection__choice__remove", function (b) {
	          if (!e.options.get("disabled")) {
	            var c = a(this),
	                d = c.parent(),
	                f = d.data("data");e.trigger("unselect", { originalEvent: b, data: f });
	          }
	        });
	      }, d.prototype.clear = function () {
	        this.$selection.find(".select2-selection__rendered").empty();
	      }, d.prototype.display = function (a, b) {
	        var c = this.options.get("templateSelection"),
	            d = this.options.get("escapeMarkup");return d(c(a, b));
	      }, d.prototype.selectionContainer = function () {
	        var b = a('<li class="select2-selection__choice"><span class="select2-selection__choice__remove" role="presentation">&times;</span></li>');return b;
	      }, d.prototype.update = function (a) {
	        if (this.clear(), 0 !== a.length) {
	          for (var b = [], d = 0; d < a.length; d++) {
	            var e = a[d],
	                f = this.selectionContainer(),
	                g = this.display(e, f);f.append(g), f.prop("title", e.title || e.text), f.data("data", e), b.push(f);
	          }var h = this.$selection.find(".select2-selection__rendered");c.appendMany(h, b);
	        }
	      }, d;
	    }), b.define("select2/selection/placeholder", ["../utils"], function (a) {
	      function b(a, b, c) {
	        this.placeholder = this.normalizePlaceholder(c.get("placeholder")), a.call(this, b, c);
	      }return b.prototype.normalizePlaceholder = function (a, b) {
	        return "string" == typeof b && (b = { id: "", text: b }), b;
	      }, b.prototype.createPlaceholder = function (a, b) {
	        var c = this.selectionContainer();return c.html(this.display(b)), c.addClass("select2-selection__placeholder").removeClass("select2-selection__choice"), c;
	      }, b.prototype.update = function (a, b) {
	        var c = 1 == b.length && b[0].id != this.placeholder.id,
	            d = b.length > 1;if (d || c) return a.call(this, b);this.clear();var e = this.createPlaceholder(this.placeholder);this.$selection.find(".select2-selection__rendered").append(e);
	      }, b;
	    }), b.define("select2/selection/allowClear", ["jquery", "../keys"], function (a, b) {
	      function c() {}return c.prototype.bind = function (a, b, c) {
	        var d = this;a.call(this, b, c), null == this.placeholder && this.options.get("debug") && window.console && console.error && console.error("Select2: The `allowClear` option should be used in combination with the `placeholder` option."), this.$selection.on("mousedown", ".select2-selection__clear", function (a) {
	          d._handleClear(a);
	        }), b.on("keypress", function (a) {
	          d._handleKeyboardClear(a, b);
	        });
	      }, c.prototype._handleClear = function (a, b) {
	        if (!this.options.get("disabled")) {
	          var c = this.$selection.find(".select2-selection__clear");if (0 !== c.length) {
	            b.stopPropagation();for (var d = c.data("data"), e = 0; e < d.length; e++) {
	              var f = { data: d[e] };if (this.trigger("unselect", f), f.prevented) return;
	            }this.$element.val(this.placeholder.id).trigger("change"), this.trigger("toggle", {});
	          }
	        }
	      }, c.prototype._handleKeyboardClear = function (a, c, d) {
	        d.isOpen() || (c.which == b.DELETE || c.which == b.BACKSPACE) && this._handleClear(c);
	      }, c.prototype.update = function (b, c) {
	        if (b.call(this, c), !(this.$selection.find(".select2-selection__placeholder").length > 0 || 0 === c.length)) {
	          var d = a('<span class="select2-selection__clear">&times;</span>');d.data("data", c), this.$selection.find(".select2-selection__rendered").prepend(d);
	        }
	      }, c;
	    }), b.define("select2/selection/search", ["jquery", "../utils", "../keys"], function (a, b, c) {
	      function d(a, b, c) {
	        a.call(this, b, c);
	      }return d.prototype.render = function (b) {
	        var c = a('<li class="select2-search select2-search--inline"><input class="select2-search__field" type="search" tabindex="-1" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" role="textbox" aria-autocomplete="list" /></li>');this.$searchContainer = c, this.$search = c.find("input");var d = b.call(this);return this._transferTabIndex(), d;
	      }, d.prototype.bind = function (a, b, d) {
	        var e = this;a.call(this, b, d), b.on("open", function () {
	          e.$search.trigger("focus");
	        }), b.on("close", function () {
	          e.$search.val(""), e.$search.removeAttr("aria-activedescendant"), e.$search.trigger("focus");
	        }), b.on("enable", function () {
	          e.$search.prop("disabled", !1), e._transferTabIndex();
	        }), b.on("disable", function () {
	          e.$search.prop("disabled", !0);
	        }), b.on("focus", function (a) {
	          e.$search.trigger("focus");
	        }), b.on("results:focus", function (a) {
	          e.$search.attr("aria-activedescendant", a.id);
	        }), this.$selection.on("focusin", ".select2-search--inline", function (a) {
	          e.trigger("focus", a);
	        }), this.$selection.on("focusout", ".select2-search--inline", function (a) {
	          e._handleBlur(a);
	        }), this.$selection.on("keydown", ".select2-search--inline", function (a) {
	          a.stopPropagation(), e.trigger("keypress", a), e._keyUpPrevented = a.isDefaultPrevented();var b = a.which;if (b === c.BACKSPACE && "" === e.$search.val()) {
	            var d = e.$searchContainer.prev(".select2-selection__choice");if (d.length > 0) {
	              var f = d.data("data");e.searchRemoveChoice(f), a.preventDefault();
	            }
	          }
	        });var f = document.documentMode,
	            g = f && 11 >= f;this.$selection.on("input.searchcheck", ".select2-search--inline", function (a) {
	          return g ? void e.$selection.off("input.search input.searchcheck") : void e.$selection.off("keyup.search");
	        }), this.$selection.on("keyup.search input.search", ".select2-search--inline", function (a) {
	          if (g && "input" === a.type) return void e.$selection.off("input.search input.searchcheck");var b = a.which;b != c.SHIFT && b != c.CTRL && b != c.ALT && b != c.TAB && e.handleSearch(a);
	        });
	      }, d.prototype._transferTabIndex = function (a) {
	        this.$search.attr("tabindex", this.$selection.attr("tabindex")), this.$selection.attr("tabindex", "-1");
	      }, d.prototype.createPlaceholder = function (a, b) {
	        this.$search.attr("placeholder", b.text);
	      }, d.prototype.update = function (a, b) {
	        var c = this.$search[0] == document.activeElement;this.$search.attr("placeholder", ""), a.call(this, b), this.$selection.find(".select2-selection__rendered").append(this.$searchContainer), this.resizeSearch(), c && this.$search.focus();
	      }, d.prototype.handleSearch = function () {
	        if (this.resizeSearch(), !this._keyUpPrevented) {
	          var a = this.$search.val();this.trigger("query", { term: a });
	        }this._keyUpPrevented = !1;
	      }, d.prototype.searchRemoveChoice = function (a, b) {
	        this.trigger("unselect", { data: b }), this.$search.val(b.text), this.handleSearch();
	      }, d.prototype.resizeSearch = function () {
	        this.$search.css("width", "25px");var a = "";if ("" !== this.$search.attr("placeholder")) a = this.$selection.find(".select2-selection__rendered").innerWidth();else {
	          var b = this.$search.val().length + 1;a = .75 * b + "em";
	        }this.$search.css("width", a);
	      }, d;
	    }), b.define("select2/selection/eventRelay", ["jquery"], function (a) {
	      function b() {}return b.prototype.bind = function (b, c, d) {
	        var e = this,
	            f = ["open", "opening", "close", "closing", "select", "selecting", "unselect", "unselecting"],
	            g = ["opening", "closing", "selecting", "unselecting"];b.call(this, c, d), c.on("*", function (b, c) {
	          if (-1 !== a.inArray(b, f)) {
	            c = c || {};var d = a.Event("select2:" + b, { params: c });e.$element.trigger(d), -1 !== a.inArray(b, g) && (c.prevented = d.isDefaultPrevented());
	          }
	        });
	      }, b;
	    }), b.define("select2/translation", ["jquery", "require"], function (a, b) {
	      function c(a) {
	        this.dict = a || {};
	      }return c.prototype.all = function () {
	        return this.dict;
	      }, c.prototype.get = function (a) {
	        return this.dict[a];
	      }, c.prototype.extend = function (b) {
	        this.dict = a.extend({}, b.all(), this.dict);
	      }, c._cache = {}, c.loadPath = function (a) {
	        if (!(a in c._cache)) {
	          var d = b(a);c._cache[a] = d;
	        }return new c(c._cache[a]);
	      }, c;
	    }), b.define("select2/diacritics", [], function () {
	      var a = { "Ⓐ": "A", "Ａ": "A", "À": "A", "Á": "A", "Â": "A", "Ầ": "A", "Ấ": "A", "Ẫ": "A", "Ẩ": "A", "Ã": "A", "Ā": "A", "Ă": "A", "Ằ": "A", "Ắ": "A", "Ẵ": "A", "Ẳ": "A", "Ȧ": "A", "Ǡ": "A", "Ä": "A", "Ǟ": "A", "Ả": "A", "Å": "A", "Ǻ": "A", "Ǎ": "A", "Ȁ": "A", "Ȃ": "A", "Ạ": "A", "Ậ": "A", "Ặ": "A", "Ḁ": "A", "Ą": "A", "Ⱥ": "A", "Ɐ": "A", "Ꜳ": "AA", "Æ": "AE", "Ǽ": "AE", "Ǣ": "AE", "Ꜵ": "AO", "Ꜷ": "AU", "Ꜹ": "AV", "Ꜻ": "AV", "Ꜽ": "AY", "Ⓑ": "B", "Ｂ": "B", "Ḃ": "B", "Ḅ": "B", "Ḇ": "B", "Ƀ": "B", "Ƃ": "B", "Ɓ": "B", "Ⓒ": "C", "Ｃ": "C", "Ć": "C", "Ĉ": "C", "Ċ": "C", "Č": "C", "Ç": "C", "Ḉ": "C", "Ƈ": "C", "Ȼ": "C", "Ꜿ": "C", "Ⓓ": "D", "Ｄ": "D", "Ḋ": "D", "Ď": "D", "Ḍ": "D", "Ḑ": "D", "Ḓ": "D", "Ḏ": "D", "Đ": "D", "Ƌ": "D", "Ɗ": "D", "Ɖ": "D", "Ꝺ": "D", "Ǳ": "DZ", "Ǆ": "DZ", "ǲ": "Dz", "ǅ": "Dz", "Ⓔ": "E", "Ｅ": "E", "È": "E", "É": "E", "Ê": "E", "Ề": "E", "Ế": "E", "Ễ": "E", "Ể": "E", "Ẽ": "E", "Ē": "E", "Ḕ": "E", "Ḗ": "E", "Ĕ": "E", "Ė": "E", "Ë": "E", "Ẻ": "E", "Ě": "E", "Ȅ": "E", "Ȇ": "E", "Ẹ": "E", "Ệ": "E", "Ȩ": "E", "Ḝ": "E", "Ę": "E", "Ḙ": "E", "Ḛ": "E", "Ɛ": "E", "Ǝ": "E", "Ⓕ": "F", "Ｆ": "F", "Ḟ": "F", "Ƒ": "F", "Ꝼ": "F", "Ⓖ": "G", "Ｇ": "G", "Ǵ": "G", "Ĝ": "G", "Ḡ": "G", "Ğ": "G", "Ġ": "G", "Ǧ": "G", "Ģ": "G", "Ǥ": "G", "Ɠ": "G", "Ꞡ": "G", "Ᵹ": "G", "Ꝿ": "G", "Ⓗ": "H", "Ｈ": "H", "Ĥ": "H", "Ḣ": "H", "Ḧ": "H", "Ȟ": "H", "Ḥ": "H", "Ḩ": "H", "Ḫ": "H", "Ħ": "H", "Ⱨ": "H", "Ⱶ": "H", "Ɥ": "H", "Ⓘ": "I", "Ｉ": "I", "Ì": "I", "Í": "I", "Î": "I", "Ĩ": "I", "Ī": "I", "Ĭ": "I", "İ": "I", "Ï": "I", "Ḯ": "I", "Ỉ": "I", "Ǐ": "I", "Ȉ": "I", "Ȋ": "I", "Ị": "I", "Į": "I", "Ḭ": "I", "Ɨ": "I", "Ⓙ": "J", "Ｊ": "J", "Ĵ": "J", "Ɉ": "J", "Ⓚ": "K", "Ｋ": "K", "Ḱ": "K", "Ǩ": "K", "Ḳ": "K", "Ķ": "K", "Ḵ": "K", "Ƙ": "K", "Ⱪ": "K", "Ꝁ": "K", "Ꝃ": "K", "Ꝅ": "K", "Ꞣ": "K", "Ⓛ": "L", "Ｌ": "L", "Ŀ": "L", "Ĺ": "L", "Ľ": "L", "Ḷ": "L", "Ḹ": "L", "Ļ": "L", "Ḽ": "L", "Ḻ": "L", "Ł": "L", "Ƚ": "L", "Ɫ": "L", "Ⱡ": "L", "Ꝉ": "L", "Ꝇ": "L", "Ꞁ": "L", "Ǉ": "LJ", "ǈ": "Lj", "Ⓜ": "M", "Ｍ": "M", "Ḿ": "M", "Ṁ": "M", "Ṃ": "M", "Ɱ": "M", "Ɯ": "M", "Ⓝ": "N", "Ｎ": "N", "Ǹ": "N", "Ń": "N", "Ñ": "N", "Ṅ": "N", "Ň": "N", "Ṇ": "N", "Ņ": "N", "Ṋ": "N", "Ṉ": "N", "Ƞ": "N", "Ɲ": "N", "Ꞑ": "N", "Ꞥ": "N", "Ǌ": "NJ", "ǋ": "Nj", "Ⓞ": "O", "Ｏ": "O", "Ò": "O", "Ó": "O", "Ô": "O", "Ồ": "O", "Ố": "O", "Ỗ": "O", "Ổ": "O", "Õ": "O", "Ṍ": "O", "Ȭ": "O", "Ṏ": "O", "Ō": "O", "Ṑ": "O", "Ṓ": "O", "Ŏ": "O", "Ȯ": "O", "Ȱ": "O", "Ö": "O", "Ȫ": "O", "Ỏ": "O", "Ő": "O", "Ǒ": "O", "Ȍ": "O", "Ȏ": "O", "Ơ": "O", "Ờ": "O", "Ớ": "O", "Ỡ": "O", "Ở": "O", "Ợ": "O", "Ọ": "O", "Ộ": "O", "Ǫ": "O", "Ǭ": "O", "Ø": "O", "Ǿ": "O", "Ɔ": "O", "Ɵ": "O", "Ꝋ": "O", "Ꝍ": "O", "Ƣ": "OI", "Ꝏ": "OO", "Ȣ": "OU", "Ⓟ": "P", "Ｐ": "P", "Ṕ": "P", "Ṗ": "P", "Ƥ": "P", "Ᵽ": "P", "Ꝑ": "P", "Ꝓ": "P", "Ꝕ": "P", "Ⓠ": "Q", "Ｑ": "Q", "Ꝗ": "Q", "Ꝙ": "Q", "Ɋ": "Q", "Ⓡ": "R", "Ｒ": "R", "Ŕ": "R", "Ṙ": "R", "Ř": "R", "Ȑ": "R", "Ȓ": "R", "Ṛ": "R", "Ṝ": "R", "Ŗ": "R", "Ṟ": "R", "Ɍ": "R", "Ɽ": "R", "Ꝛ": "R", "Ꞧ": "R", "Ꞃ": "R", "Ⓢ": "S", "Ｓ": "S", "ẞ": "S", "Ś": "S", "Ṥ": "S", "Ŝ": "S", "Ṡ": "S", "Š": "S", "Ṧ": "S", "Ṣ": "S", "Ṩ": "S", "Ș": "S", "Ş": "S", "Ȿ": "S", "Ꞩ": "S", "Ꞅ": "S", "Ⓣ": "T", "Ｔ": "T", "Ṫ": "T", "Ť": "T", "Ṭ": "T", "Ț": "T", "Ţ": "T", "Ṱ": "T", "Ṯ": "T", "Ŧ": "T", "Ƭ": "T", "Ʈ": "T", "Ⱦ": "T", "Ꞇ": "T", "Ꜩ": "TZ", "Ⓤ": "U", "Ｕ": "U", "Ù": "U", "Ú": "U", "Û": "U", "Ũ": "U", "Ṹ": "U", "Ū": "U", "Ṻ": "U", "Ŭ": "U", "Ü": "U", "Ǜ": "U", "Ǘ": "U", "Ǖ": "U", "Ǚ": "U", "Ủ": "U", "Ů": "U", "Ű": "U", "Ǔ": "U", "Ȕ": "U", "Ȗ": "U", "Ư": "U", "Ừ": "U", "Ứ": "U", "Ữ": "U", "Ử": "U", "Ự": "U", "Ụ": "U", "Ṳ": "U", "Ų": "U", "Ṷ": "U", "Ṵ": "U", "Ʉ": "U", "Ⓥ": "V", "Ｖ": "V", "Ṽ": "V", "Ṿ": "V", "Ʋ": "V", "Ꝟ": "V", "Ʌ": "V", "Ꝡ": "VY", "Ⓦ": "W", "Ｗ": "W", "Ẁ": "W", "Ẃ": "W", "Ŵ": "W", "Ẇ": "W", "Ẅ": "W", "Ẉ": "W", "Ⱳ": "W", "Ⓧ": "X", "Ｘ": "X", "Ẋ": "X", "Ẍ": "X", "Ⓨ": "Y", "Ｙ": "Y", "Ỳ": "Y", "Ý": "Y", "Ŷ": "Y", "Ỹ": "Y", "Ȳ": "Y", "Ẏ": "Y", "Ÿ": "Y", "Ỷ": "Y", "Ỵ": "Y", "Ƴ": "Y", "Ɏ": "Y", "Ỿ": "Y", "Ⓩ": "Z", "Ｚ": "Z", "Ź": "Z", "Ẑ": "Z", "Ż": "Z", "Ž": "Z", "Ẓ": "Z", "Ẕ": "Z", "Ƶ": "Z", "Ȥ": "Z", "Ɀ": "Z", "Ⱬ": "Z", "Ꝣ": "Z", "ⓐ": "a", "ａ": "a", "ẚ": "a", "à": "a", "á": "a", "â": "a", "ầ": "a", "ấ": "a", "ẫ": "a", "ẩ": "a", "ã": "a", "ā": "a", "ă": "a", "ằ": "a", "ắ": "a", "ẵ": "a", "ẳ": "a", "ȧ": "a", "ǡ": "a", "ä": "a", "ǟ": "a", "ả": "a", "å": "a", "ǻ": "a", "ǎ": "a", "ȁ": "a", "ȃ": "a", "ạ": "a", "ậ": "a", "ặ": "a", "ḁ": "a", "ą": "a", "ⱥ": "a", "ɐ": "a", "ꜳ": "aa", "æ": "ae", "ǽ": "ae", "ǣ": "ae", "ꜵ": "ao", "ꜷ": "au", "ꜹ": "av", "ꜻ": "av", "ꜽ": "ay", "ⓑ": "b", "ｂ": "b", "ḃ": "b", "ḅ": "b", "ḇ": "b", "ƀ": "b", "ƃ": "b", "ɓ": "b", "ⓒ": "c", "ｃ": "c", "ć": "c", "ĉ": "c", "ċ": "c", "č": "c", "ç": "c", "ḉ": "c", "ƈ": "c", "ȼ": "c", "ꜿ": "c", "ↄ": "c", "ⓓ": "d", "ｄ": "d", "ḋ": "d", "ď": "d", "ḍ": "d", "ḑ": "d", "ḓ": "d", "ḏ": "d", "đ": "d", "ƌ": "d", "ɖ": "d", "ɗ": "d", "ꝺ": "d", "ǳ": "dz", "ǆ": "dz", "ⓔ": "e", "ｅ": "e", "è": "e", "é": "e", "ê": "e", "ề": "e", "ế": "e", "ễ": "e", "ể": "e", "ẽ": "e", "ē": "e", "ḕ": "e", "ḗ": "e", "ĕ": "e", "ė": "e", "ë": "e", "ẻ": "e", "ě": "e", "ȅ": "e", "ȇ": "e", "ẹ": "e", "ệ": "e", "ȩ": "e", "ḝ": "e", "ę": "e", "ḙ": "e", "ḛ": "e", "ɇ": "e", "ɛ": "e", "ǝ": "e", "ⓕ": "f", "ｆ": "f", "ḟ": "f", "ƒ": "f", "ꝼ": "f", "ⓖ": "g", "ｇ": "g", "ǵ": "g", "ĝ": "g", "ḡ": "g", "ğ": "g", "ġ": "g", "ǧ": "g", "ģ": "g", "ǥ": "g", "ɠ": "g", "ꞡ": "g", "ᵹ": "g", "ꝿ": "g", "ⓗ": "h", "ｈ": "h", "ĥ": "h", "ḣ": "h", "ḧ": "h", "ȟ": "h", "ḥ": "h", "ḩ": "h", "ḫ": "h", "ẖ": "h", "ħ": "h", "ⱨ": "h", "ⱶ": "h", "ɥ": "h", "ƕ": "hv", "ⓘ": "i", "ｉ": "i", "ì": "i", "í": "i", "î": "i", "ĩ": "i", "ī": "i", "ĭ": "i", "ï": "i", "ḯ": "i", "ỉ": "i", "ǐ": "i", "ȉ": "i", "ȋ": "i", "ị": "i", "į": "i", "ḭ": "i", "ɨ": "i", "ı": "i", "ⓙ": "j", "ｊ": "j", "ĵ": "j", "ǰ": "j", "ɉ": "j", "ⓚ": "k", "ｋ": "k", "ḱ": "k", "ǩ": "k", "ḳ": "k", "ķ": "k", "ḵ": "k", "ƙ": "k", "ⱪ": "k", "ꝁ": "k", "ꝃ": "k", "ꝅ": "k", "ꞣ": "k", "ⓛ": "l", "ｌ": "l", "ŀ": "l", "ĺ": "l", "ľ": "l", "ḷ": "l", "ḹ": "l", "ļ": "l", "ḽ": "l", "ḻ": "l", "ſ": "l", "ł": "l", "ƚ": "l", "ɫ": "l", "ⱡ": "l", "ꝉ": "l", "ꞁ": "l", "ꝇ": "l", "ǉ": "lj", "ⓜ": "m", "ｍ": "m", "ḿ": "m", "ṁ": "m", "ṃ": "m", "ɱ": "m", "ɯ": "m", "ⓝ": "n", "ｎ": "n", "ǹ": "n", "ń": "n", "ñ": "n", "ṅ": "n", "ň": "n", "ṇ": "n", "ņ": "n", "ṋ": "n", "ṉ": "n", "ƞ": "n", "ɲ": "n", "ŉ": "n", "ꞑ": "n", "ꞥ": "n", "ǌ": "nj", "ⓞ": "o", "ｏ": "o", "ò": "o", "ó": "o", "ô": "o", "ồ": "o", "ố": "o", "ỗ": "o", "ổ": "o", "õ": "o", "ṍ": "o", "ȭ": "o", "ṏ": "o", "ō": "o", "ṑ": "o", "ṓ": "o", "ŏ": "o", "ȯ": "o", "ȱ": "o", "ö": "o", "ȫ": "o", "ỏ": "o", "ő": "o", "ǒ": "o", "ȍ": "o", "ȏ": "o", "ơ": "o", "ờ": "o", "ớ": "o", "ỡ": "o", "ở": "o", "ợ": "o", "ọ": "o", "ộ": "o", "ǫ": "o", "ǭ": "o", "ø": "o", "ǿ": "o", "ɔ": "o", "ꝋ": "o", "ꝍ": "o", "ɵ": "o", "ƣ": "oi", "ȣ": "ou", "ꝏ": "oo", "ⓟ": "p", "ｐ": "p", "ṕ": "p", "ṗ": "p", "ƥ": "p", "ᵽ": "p", "ꝑ": "p", "ꝓ": "p", "ꝕ": "p", "ⓠ": "q", "ｑ": "q", "ɋ": "q", "ꝗ": "q", "ꝙ": "q", "ⓡ": "r", "ｒ": "r", "ŕ": "r", "ṙ": "r", "ř": "r", "ȑ": "r", "ȓ": "r", "ṛ": "r", "ṝ": "r", "ŗ": "r", "ṟ": "r", "ɍ": "r", "ɽ": "r", "ꝛ": "r", "ꞧ": "r", "ꞃ": "r", "ⓢ": "s", "ｓ": "s", "ß": "s", "ś": "s", "ṥ": "s", "ŝ": "s", "ṡ": "s", "š": "s", "ṧ": "s", "ṣ": "s", "ṩ": "s", "ș": "s", "ş": "s", "ȿ": "s", "ꞩ": "s", "ꞅ": "s", "ẛ": "s", "ⓣ": "t", "ｔ": "t", "ṫ": "t", "ẗ": "t", "ť": "t", "ṭ": "t", "ț": "t", "ţ": "t", "ṱ": "t", "ṯ": "t", "ŧ": "t", "ƭ": "t", "ʈ": "t", "ⱦ": "t", "ꞇ": "t", "ꜩ": "tz", "ⓤ": "u", "ｕ": "u", "ù": "u", "ú": "u", "û": "u", "ũ": "u", "ṹ": "u", "ū": "u", "ṻ": "u", "ŭ": "u", "ü": "u", "ǜ": "u", "ǘ": "u", "ǖ": "u", "ǚ": "u", "ủ": "u", "ů": "u", "ű": "u", "ǔ": "u", "ȕ": "u", "ȗ": "u", "ư": "u", "ừ": "u", "ứ": "u", "ữ": "u", "ử": "u", "ự": "u", "ụ": "u", "ṳ": "u", "ų": "u", "ṷ": "u", "ṵ": "u", "ʉ": "u", "ⓥ": "v", "ｖ": "v", "ṽ": "v", "ṿ": "v", "ʋ": "v", "ꝟ": "v", "ʌ": "v", "ꝡ": "vy", "ⓦ": "w", "ｗ": "w", "ẁ": "w", "ẃ": "w", "ŵ": "w", "ẇ": "w", "ẅ": "w", "ẘ": "w", "ẉ": "w", "ⱳ": "w", "ⓧ": "x", "ｘ": "x", "ẋ": "x", "ẍ": "x", "ⓨ": "y", "ｙ": "y", "ỳ": "y", "ý": "y", "ŷ": "y", "ỹ": "y", "ȳ": "y", "ẏ": "y", "ÿ": "y", "ỷ": "y", "ẙ": "y", "ỵ": "y", "ƴ": "y", "ɏ": "y", "ỿ": "y", "ⓩ": "z", "ｚ": "z", "ź": "z", "ẑ": "z", "ż": "z", "ž": "z", "ẓ": "z", "ẕ": "z", "ƶ": "z", "ȥ": "z", "ɀ": "z", "ⱬ": "z", "ꝣ": "z", "Ά": "Α", "Έ": "Ε", "Ή": "Η", "Ί": "Ι", "Ϊ": "Ι", "Ό": "Ο", "Ύ": "Υ", "Ϋ": "Υ", "Ώ": "Ω", "ά": "α", "έ": "ε", "ή": "η", "ί": "ι", "ϊ": "ι", "ΐ": "ι", "ό": "ο", "ύ": "υ", "ϋ": "υ", "ΰ": "υ", "ω": "ω", "ς": "σ" };return a;
	    }), b.define("select2/data/base", ["../utils"], function (a) {
	      function b(a, c) {
	        b.__super__.constructor.call(this);
	      }return a.Extend(b, a.Observable), b.prototype.current = function (a) {
	        throw new Error("The `current` method must be defined in child classes.");
	      }, b.prototype.query = function (a, b) {
	        throw new Error("The `query` method must be defined in child classes.");
	      }, b.prototype.bind = function (a, b) {}, b.prototype.destroy = function () {}, b.prototype.generateResultId = function (b, c) {
	        var d = b.id + "-result-";return d += a.generateChars(4), d += null != c.id ? "-" + c.id.toString() : "-" + a.generateChars(4);
	      }, b;
	    }), b.define("select2/data/select", ["./base", "../utils", "jquery"], function (a, b, c) {
	      function d(a, b) {
	        this.$element = a, this.options = b, d.__super__.constructor.call(this);
	      }return b.Extend(d, a), d.prototype.current = function (a) {
	        var b = [],
	            d = this;this.$element.find(":selected").each(function () {
	          var a = c(this),
	              e = d.item(a);b.push(e);
	        }), a(b);
	      }, d.prototype.select = function (a) {
	        var b = this;if (a.selected = !0, c(a.element).is("option")) return a.element.selected = !0, void this.$element.trigger("change");if (this.$element.prop("multiple")) this.current(function (d) {
	          var e = [];a = [a], a.push.apply(a, d);for (var f = 0; f < a.length; f++) {
	            var g = a[f].id;-1 === c.inArray(g, e) && e.push(g);
	          }b.$element.val(e), b.$element.trigger("change");
	        });else {
	          var d = a.id;this.$element.val(d), this.$element.trigger("change");
	        }
	      }, d.prototype.unselect = function (a) {
	        var b = this;if (this.$element.prop("multiple")) return a.selected = !1, c(a.element).is("option") ? (a.element.selected = !1, void this.$element.trigger("change")) : void this.current(function (d) {
	          for (var e = [], f = 0; f < d.length; f++) {
	            var g = d[f].id;g !== a.id && -1 === c.inArray(g, e) && e.push(g);
	          }b.$element.val(e), b.$element.trigger("change");
	        });
	      }, d.prototype.bind = function (a, b) {
	        var c = this;this.container = a, a.on("select", function (a) {
	          c.select(a.data);
	        }), a.on("unselect", function (a) {
	          c.unselect(a.data);
	        });
	      }, d.prototype.destroy = function () {
	        this.$element.find("*").each(function () {
	          c.removeData(this, "data");
	        });
	      }, d.prototype.query = function (a, b) {
	        var d = [],
	            e = this,
	            f = this.$element.children();f.each(function () {
	          var b = c(this);if (b.is("option") || b.is("optgroup")) {
	            var f = e.item(b),
	                g = e.matches(a, f);null !== g && d.push(g);
	          }
	        }), b({ results: d });
	      }, d.prototype.addOptions = function (a) {
	        b.appendMany(this.$element, a);
	      }, d.prototype.option = function (a) {
	        var b;a.children ? (b = document.createElement("optgroup"), b.label = a.text) : (b = document.createElement("option"), void 0 !== b.textContent ? b.textContent = a.text : b.innerText = a.text), a.id && (b.value = a.id), a.disabled && (b.disabled = !0), a.selected && (b.selected = !0), a.title && (b.title = a.title);var d = c(b),
	            e = this._normalizeItem(a);return e.element = b, c.data(b, "data", e), d;
	      }, d.prototype.item = function (a) {
	        var b = {};if (b = c.data(a[0], "data"), null != b) return b;if (a.is("option")) b = { id: a.val(), text: a.text(), disabled: a.prop("disabled"), selected: a.prop("selected"), title: a.prop("title") };else if (a.is("optgroup")) {
	          b = { text: a.prop("label"), children: [], title: a.prop("title") };for (var d = a.children("option"), e = [], f = 0; f < d.length; f++) {
	            var g = c(d[f]),
	                h = this.item(g);e.push(h);
	          }b.children = e;
	        }return b = this._normalizeItem(b), b.element = a[0], c.data(a[0], "data", b), b;
	      }, d.prototype._normalizeItem = function (a) {
	        c.isPlainObject(a) || (a = { id: a, text: a }), a = c.extend({}, { text: "" }, a);var b = { selected: !1, disabled: !1 };return null != a.id && (a.id = a.id.toString()), null != a.text && (a.text = a.text.toString()), null == a._resultId && a.id && null != this.container && (a._resultId = this.generateResultId(this.container, a)), c.extend({}, b, a);
	      }, d.prototype.matches = function (a, b) {
	        var c = this.options.get("matcher");return c(a, b);
	      }, d;
	    }), b.define("select2/data/array", ["./select", "../utils", "jquery"], function (a, b, c) {
	      function d(a, b) {
	        var c = b.get("data") || [];d.__super__.constructor.call(this, a, b), this.addOptions(this.convertToOptions(c));
	      }return b.Extend(d, a), d.prototype.select = function (a) {
	        var b = this.$element.find("option").filter(function (b, c) {
	          return c.value == a.id.toString();
	        });0 === b.length && (b = this.option(a), this.addOptions(b)), d.__super__.select.call(this, a);
	      }, d.prototype.convertToOptions = function (a) {
	        function d(a) {
	          return function () {
	            return c(this).val() == a.id;
	          };
	        }for (var e = this, f = this.$element.find("option"), g = f.map(function () {
	          return e.item(c(this)).id;
	        }).get(), h = [], i = 0; i < a.length; i++) {
	          var j = this._normalizeItem(a[i]);if (c.inArray(j.id, g) >= 0) {
	            var k = f.filter(d(j)),
	                l = this.item(k),
	                m = c.extend(!0, {}, l, j),
	                n = this.option(m);k.replaceWith(n);
	          } else {
	            var o = this.option(j);if (j.children) {
	              var p = this.convertToOptions(j.children);b.appendMany(o, p);
	            }h.push(o);
	          }
	        }return h;
	      }, d;
	    }), b.define("select2/data/ajax", ["./array", "../utils", "jquery"], function (a, b, c) {
	      function d(a, b) {
	        this.ajaxOptions = this._applyDefaults(b.get("ajax")), null != this.ajaxOptions.processResults && (this.processResults = this.ajaxOptions.processResults), d.__super__.constructor.call(this, a, b);
	      }return b.Extend(d, a), d.prototype._applyDefaults = function (a) {
	        var b = { data: function data(a) {
	            return c.extend({}, a, { q: a.term });
	          }, transport: function transport(a, b, d) {
	            var e = c.ajax(a);return e.then(b), e.fail(d), e;
	          } };return c.extend({}, b, a, !0);
	      }, d.prototype.processResults = function (a) {
	        return a;
	      }, d.prototype.query = function (a, b) {
	        function d() {
	          var d = f.transport(f, function (d) {
	            var f = e.processResults(d, a);e.options.get("debug") && window.console && console.error && (f && f.results && c.isArray(f.results) || console.error("Select2: The AJAX results did not return an array in the `results` key of the response.")), b(f);
	          }, function () {});e._request = d;
	        }var e = this;null != this._request && (c.isFunction(this._request.abort) && this._request.abort(), this._request = null);var f = c.extend({ type: "GET" }, this.ajaxOptions);"function" == typeof f.url && (f.url = f.url.call(this.$element, a)), "function" == typeof f.data && (f.data = f.data.call(this.$element, a)), this.ajaxOptions.delay && "" !== a.term ? (this._queryTimeout && window.clearTimeout(this._queryTimeout), this._queryTimeout = window.setTimeout(d, this.ajaxOptions.delay)) : d();
	      }, d;
	    }), b.define("select2/data/tags", ["jquery"], function (a) {
	      function b(b, c, d) {
	        var e = d.get("tags"),
	            f = d.get("createTag");if (void 0 !== f && (this.createTag = f), b.call(this, c, d), a.isArray(e)) for (var g = 0; g < e.length; g++) {
	          var h = e[g],
	              i = this._normalizeItem(h),
	              j = this.option(i);this.$element.append(j);
	        }
	      }return b.prototype.query = function (a, b, c) {
	        function d(a, f) {
	          for (var g = a.results, h = 0; h < g.length; h++) {
	            var i = g[h],
	                j = null != i.children && !d({ results: i.children }, !0),
	                k = i.text === b.term;if (k || j) return f ? !1 : (a.data = g, void c(a));
	          }if (f) return !0;var l = e.createTag(b);if (null != l) {
	            var m = e.option(l);m.attr("data-select2-tag", !0), e.addOptions([m]), e.insertTag(g, l);
	          }a.results = g, c(a);
	        }var e = this;return this._removeOldTags(), null == b.term || null != b.page ? void a.call(this, b, c) : void a.call(this, b, d);
	      }, b.prototype.createTag = function (b, c) {
	        var d = a.trim(c.term);return "" === d ? null : { id: d, text: d };
	      }, b.prototype.insertTag = function (a, b, c) {
	        b.unshift(c);
	      }, b.prototype._removeOldTags = function (b) {
	        var c = (this._lastTag, this.$element.find("option[data-select2-tag]"));c.each(function () {
	          this.selected || a(this).remove();
	        });
	      }, b;
	    }), b.define("select2/data/tokenizer", ["jquery"], function (a) {
	      function b(a, b, c) {
	        var d = c.get("tokenizer");void 0 !== d && (this.tokenizer = d), a.call(this, b, c);
	      }return b.prototype.bind = function (a, b, c) {
	        a.call(this, b, c), this.$search = b.dropdown.$search || b.selection.$search || c.find(".select2-search__field");
	      }, b.prototype.query = function (a, b, c) {
	        function d(a) {
	          e.trigger("select", { data: a });
	        }var e = this;b.term = b.term || "";var f = this.tokenizer(b, this.options, d);f.term !== b.term && (this.$search.length && (this.$search.val(f.term), this.$search.focus()), b.term = f.term), a.call(this, b, c);
	      }, b.prototype.tokenizer = function (b, c, d, e) {
	        for (var f = d.get("tokenSeparators") || [], g = c.term, h = 0, i = this.createTag || function (a) {
	          return { id: a.term, text: a.term };
	        }; h < g.length;) {
	          var j = g[h];if (-1 !== a.inArray(j, f)) {
	            var k = g.substr(0, h),
	                l = a.extend({}, c, { term: k }),
	                m = i(l);null != m ? (e(m), g = g.substr(h + 1) || "", h = 0) : h++;
	          } else h++;
	        }return { term: g };
	      }, b;
	    }), b.define("select2/data/minimumInputLength", [], function () {
	      function a(a, b, c) {
	        this.minimumInputLength = c.get("minimumInputLength"), a.call(this, b, c);
	      }return a.prototype.query = function (a, b, c) {
	        return b.term = b.term || "", b.term.length < this.minimumInputLength ? void this.trigger("results:message", { message: "inputTooShort", args: { minimum: this.minimumInputLength, input: b.term, params: b } }) : void a.call(this, b, c);
	      }, a;
	    }), b.define("select2/data/maximumInputLength", [], function () {
	      function a(a, b, c) {
	        this.maximumInputLength = c.get("maximumInputLength"), a.call(this, b, c);
	      }return a.prototype.query = function (a, b, c) {
	        return b.term = b.term || "", this.maximumInputLength > 0 && b.term.length > this.maximumInputLength ? void this.trigger("results:message", { message: "inputTooLong", args: { maximum: this.maximumInputLength, input: b.term, params: b } }) : void a.call(this, b, c);
	      }, a;
	    }), b.define("select2/data/maximumSelectionLength", [], function () {
	      function a(a, b, c) {
	        this.maximumSelectionLength = c.get("maximumSelectionLength"), a.call(this, b, c);
	      }return a.prototype.query = function (a, b, c) {
	        var d = this;this.current(function (e) {
	          var f = null != e ? e.length : 0;return d.maximumSelectionLength > 0 && f >= d.maximumSelectionLength ? void d.trigger("results:message", { message: "maximumSelected", args: { maximum: d.maximumSelectionLength } }) : void a.call(d, b, c);
	        });
	      }, a;
	    }), b.define("select2/dropdown", ["jquery", "./utils"], function (a, b) {
	      function c(a, b) {
	        this.$element = a, this.options = b, c.__super__.constructor.call(this);
	      }return b.Extend(c, b.Observable), c.prototype.render = function () {
	        var b = a('<span class="select2-dropdown"><span class="select2-results"></span></span>');return b.attr("dir", this.options.get("dir")), this.$dropdown = b, b;
	      }, c.prototype.bind = function () {}, c.prototype.position = function (a, b) {}, c.prototype.destroy = function () {
	        this.$dropdown.remove();
	      }, c;
	    }), b.define("select2/dropdown/search", ["jquery", "../utils"], function (a, b) {
	      function c() {}return c.prototype.render = function (b) {
	        var c = b.call(this),
	            d = a('<span class="select2-search select2-search--dropdown"><input class="select2-search__field" type="search" tabindex="-1" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" role="textbox" /></span>');return this.$searchContainer = d, this.$search = d.find("input"), c.prepend(d), c;
	      }, c.prototype.bind = function (b, c, d) {
	        var e = this;b.call(this, c, d), this.$search.on("keydown", function (a) {
	          e.trigger("keypress", a), e._keyUpPrevented = a.isDefaultPrevented();
	        }), this.$search.on("input", function (b) {
	          a(this).off("keyup");
	        }), this.$search.on("keyup input", function (a) {
	          e.handleSearch(a);
	        }), c.on("open", function () {
	          e.$search.attr("tabindex", 0), e.$search.focus(), window.setTimeout(function () {
	            e.$search.focus();
	          }, 0);
	        }), c.on("close", function () {
	          e.$search.attr("tabindex", -1), e.$search.val("");
	        }), c.on("results:all", function (a) {
	          if (null == a.query.term || "" === a.query.term) {
	            var b = e.showSearch(a);b ? e.$searchContainer.removeClass("select2-search--hide") : e.$searchContainer.addClass("select2-search--hide");
	          }
	        });
	      }, c.prototype.handleSearch = function (a) {
	        if (!this._keyUpPrevented) {
	          var b = this.$search.val();this.trigger("query", { term: b });
	        }this._keyUpPrevented = !1;
	      }, c.prototype.showSearch = function (a, b) {
	        return !0;
	      }, c;
	    }), b.define("select2/dropdown/hidePlaceholder", [], function () {
	      function a(a, b, c, d) {
	        this.placeholder = this.normalizePlaceholder(c.get("placeholder")), a.call(this, b, c, d);
	      }return a.prototype.append = function (a, b) {
	        b.results = this.removePlaceholder(b.results), a.call(this, b);
	      }, a.prototype.normalizePlaceholder = function (a, b) {
	        return "string" == typeof b && (b = { id: "", text: b }), b;
	      }, a.prototype.removePlaceholder = function (a, b) {
	        for (var c = b.slice(0), d = b.length - 1; d >= 0; d--) {
	          var e = b[d];this.placeholder.id === e.id && c.splice(d, 1);
	        }return c;
	      }, a;
	    }), b.define("select2/dropdown/infiniteScroll", ["jquery"], function (a) {
	      function b(a, b, c, d) {
	        this.lastParams = {}, a.call(this, b, c, d), this.$loadingMore = this.createLoadingMore(), this.loading = !1;
	      }return b.prototype.append = function (a, b) {
	        this.$loadingMore.remove(), this.loading = !1, a.call(this, b), this.showLoadingMore(b) && this.$results.append(this.$loadingMore);
	      }, b.prototype.bind = function (b, c, d) {
	        var e = this;b.call(this, c, d), c.on("query", function (a) {
	          e.lastParams = a, e.loading = !0;
	        }), c.on("query:append", function (a) {
	          e.lastParams = a, e.loading = !0;
	        }), this.$results.on("scroll", function () {
	          var b = a.contains(document.documentElement, e.$loadingMore[0]);if (!e.loading && b) {
	            var c = e.$results.offset().top + e.$results.outerHeight(!1),
	                d = e.$loadingMore.offset().top + e.$loadingMore.outerHeight(!1);c + 50 >= d && e.loadMore();
	          }
	        });
	      }, b.prototype.loadMore = function () {
	        this.loading = !0;var b = a.extend({}, { page: 1 }, this.lastParams);b.page++, this.trigger("query:append", b);
	      }, b.prototype.showLoadingMore = function (a, b) {
	        return b.pagination && b.pagination.more;
	      }, b.prototype.createLoadingMore = function () {
	        var b = a('<li class="select2-results__option select2-results__option--load-more"role="treeitem" aria-disabled="true"></li>'),
	            c = this.options.get("translations").get("loadingMore");return b.html(c(this.lastParams)), b;
	      }, b;
	    }), b.define("select2/dropdown/attachBody", ["jquery", "../utils"], function (a, b) {
	      function c(b, c, d) {
	        this.$dropdownParent = d.get("dropdownParent") || a(document.body), b.call(this, c, d);
	      }return c.prototype.bind = function (a, b, c) {
	        var d = this,
	            e = !1;a.call(this, b, c), b.on("open", function () {
	          d._showDropdown(), d._attachPositioningHandler(b), e || (e = !0, b.on("results:all", function () {
	            d._positionDropdown(), d._resizeDropdown();
	          }), b.on("results:append", function () {
	            d._positionDropdown(), d._resizeDropdown();
	          }));
	        }), b.on("close", function () {
	          d._hideDropdown(), d._detachPositioningHandler(b);
	        }), this.$dropdownContainer.on("mousedown", function (a) {
	          a.stopPropagation();
	        });
	      }, c.prototype.destroy = function (a) {
	        a.call(this), this.$dropdownContainer.remove();
	      }, c.prototype.position = function (a, b, c) {
	        b.attr("class", c.attr("class")), b.removeClass("select2"), b.addClass("select2-container--open"), b.css({ position: "absolute", top: -999999 }), this.$container = c;
	      }, c.prototype.render = function (b) {
	        var c = a("<span></span>"),
	            d = b.call(this);return c.append(d), this.$dropdownContainer = c, c;
	      }, c.prototype._hideDropdown = function (a) {
	        this.$dropdownContainer.detach();
	      }, c.prototype._attachPositioningHandler = function (c, d) {
	        var e = this,
	            f = "scroll.select2." + d.id,
	            g = "resize.select2." + d.id,
	            h = "orientationchange.select2." + d.id,
	            i = this.$container.parents().filter(b.hasScroll);i.each(function () {
	          a(this).data("select2-scroll-position", { x: a(this).scrollLeft(), y: a(this).scrollTop() });
	        }), i.on(f, function (b) {
	          var c = a(this).data("select2-scroll-position");a(this).scrollTop(c.y);
	        }), a(window).on(f + " " + g + " " + h, function (a) {
	          e._positionDropdown(), e._resizeDropdown();
	        });
	      }, c.prototype._detachPositioningHandler = function (c, d) {
	        var e = "scroll.select2." + d.id,
	            f = "resize.select2." + d.id,
	            g = "orientationchange.select2." + d.id,
	            h = this.$container.parents().filter(b.hasScroll);h.off(e), a(window).off(e + " " + f + " " + g);
	      }, c.prototype._positionDropdown = function () {
	        var b = a(window),
	            c = this.$dropdown.hasClass("select2-dropdown--above"),
	            d = this.$dropdown.hasClass("select2-dropdown--below"),
	            e = null,
	            f = (this.$container.position(), this.$container.offset());f.bottom = f.top + this.$container.outerHeight(!1);var g = { height: this.$container.outerHeight(!1) };g.top = f.top, g.bottom = f.top + g.height;var h = { height: this.$dropdown.outerHeight(!1) },
	            i = { top: b.scrollTop(), bottom: b.scrollTop() + b.height() },
	            j = i.top < f.top - h.height,
	            k = i.bottom > f.bottom + h.height,
	            l = { left: f.left, top: g.bottom };if ("static" !== this.$dropdownParent[0].style.position) {
	          var m = this.$dropdownParent.offset();l.top -= m.top, l.left -= m.left;
	        }c || d || (e = "below"), k || !j || c ? !j && k && c && (e = "below") : e = "above", ("above" == e || c && "below" !== e) && (l.top = g.top - h.height), null != e && (this.$dropdown.removeClass("select2-dropdown--below select2-dropdown--above").addClass("select2-dropdown--" + e), this.$container.removeClass("select2-container--below select2-container--above").addClass("select2-container--" + e)), this.$dropdownContainer.css(l);
	      }, c.prototype._resizeDropdown = function () {
	        var a = { width: this.$container.outerWidth(!1) + "px" };this.options.get("dropdownAutoWidth") && (a.minWidth = a.width, a.width = "auto"), this.$dropdown.css(a);
	      }, c.prototype._showDropdown = function (a) {
	        this.$dropdownContainer.appendTo(this.$dropdownParent), this._positionDropdown(), this._resizeDropdown();
	      }, c;
	    }), b.define("select2/dropdown/minimumResultsForSearch", [], function () {
	      function a(b) {
	        for (var c = 0, d = 0; d < b.length; d++) {
	          var e = b[d];e.children ? c += a(e.children) : c++;
	        }return c;
	      }function b(a, b, c, d) {
	        this.minimumResultsForSearch = c.get("minimumResultsForSearch"), this.minimumResultsForSearch < 0 && (this.minimumResultsForSearch = 1 / 0), a.call(this, b, c, d);
	      }return b.prototype.showSearch = function (b, c) {
	        return a(c.data.results) < this.minimumResultsForSearch ? !1 : b.call(this, c);
	      }, b;
	    }), b.define("select2/dropdown/selectOnClose", [], function () {
	      function a() {}return a.prototype.bind = function (a, b, c) {
	        var d = this;a.call(this, b, c), b.on("close", function () {
	          d._handleSelectOnClose();
	        });
	      }, a.prototype._handleSelectOnClose = function () {
	        var a = this.getHighlightedResults();if (!(a.length < 1)) {
	          var b = a.data("data");null != b.element && b.element.selected || null == b.element && b.selected || this.trigger("select", { data: b });
	        }
	      }, a;
	    }), b.define("select2/dropdown/closeOnSelect", [], function () {
	      function a() {}return a.prototype.bind = function (a, b, c) {
	        var d = this;a.call(this, b, c), b.on("select", function (a) {
	          d._selectTriggered(a);
	        }), b.on("unselect", function (a) {
	          d._selectTriggered(a);
	        });
	      }, a.prototype._selectTriggered = function (a, b) {
	        var c = b.originalEvent;c && c.ctrlKey || this.trigger("close", {});
	      }, a;
	    }), b.define("select2/i18n/en", [], function () {
	      return { errorLoading: function errorLoading() {
	          return "The results could not be loaded.";
	        }, inputTooLong: function inputTooLong(a) {
	          var b = a.input.length - a.maximum,
	              c = "Please delete " + b + " character";return 1 != b && (c += "s"), c;
	        }, inputTooShort: function inputTooShort(a) {
	          var b = a.minimum - a.input.length,
	              c = "Please enter " + b + " or more characters";return c;
	        }, loadingMore: function loadingMore() {
	          return "Loading more results…";
	        }, maximumSelected: function maximumSelected(a) {
	          var b = "You can only select " + a.maximum + " item";return 1 != a.maximum && (b += "s"), b;
	        }, noResults: function noResults() {
	          return "No results found";
	        }, searching: function searching() {
	          return "Searching…";
	        } };
	    }), b.define("select2/defaults", ["jquery", "require", "./results", "./selection/single", "./selection/multiple", "./selection/placeholder", "./selection/allowClear", "./selection/search", "./selection/eventRelay", "./utils", "./translation", "./diacritics", "./data/select", "./data/array", "./data/ajax", "./data/tags", "./data/tokenizer", "./data/minimumInputLength", "./data/maximumInputLength", "./data/maximumSelectionLength", "./dropdown", "./dropdown/search", "./dropdown/hidePlaceholder", "./dropdown/infiniteScroll", "./dropdown/attachBody", "./dropdown/minimumResultsForSearch", "./dropdown/selectOnClose", "./dropdown/closeOnSelect", "./i18n/en"], function (a, b, c, d, e, f, g, h, i, j, k, l, m, n, o, p, q, r, s, t, u, v, w, x, y, z, A, B, C) {
	      function D() {
	        this.reset();
	      }D.prototype.apply = function (l) {
	        if (l = a.extend({}, this.defaults, l), null == l.dataAdapter) {
	          if (null != l.ajax ? l.dataAdapter = o : null != l.data ? l.dataAdapter = n : l.dataAdapter = m, l.minimumInputLength > 0 && (l.dataAdapter = j.Decorate(l.dataAdapter, r)), l.maximumInputLength > 0 && (l.dataAdapter = j.Decorate(l.dataAdapter, s)), l.maximumSelectionLength > 0 && (l.dataAdapter = j.Decorate(l.dataAdapter, t)), l.tags && (l.dataAdapter = j.Decorate(l.dataAdapter, p)), (null != l.tokenSeparators || null != l.tokenizer) && (l.dataAdapter = j.Decorate(l.dataAdapter, q)), null != l.query) {
	            var C = b(l.amdBase + "compat/query");l.dataAdapter = j.Decorate(l.dataAdapter, C);
	          }if (null != l.initSelection) {
	            var D = b(l.amdBase + "compat/initSelection");l.dataAdapter = j.Decorate(l.dataAdapter, D);
	          }
	        }if (null == l.resultsAdapter && (l.resultsAdapter = c, null != l.ajax && (l.resultsAdapter = j.Decorate(l.resultsAdapter, x)), null != l.placeholder && (l.resultsAdapter = j.Decorate(l.resultsAdapter, w)), l.selectOnClose && (l.resultsAdapter = j.Decorate(l.resultsAdapter, A))), null == l.dropdownAdapter) {
	          if (l.multiple) l.dropdownAdapter = u;else {
	            var E = j.Decorate(u, v);l.dropdownAdapter = E;
	          }if (0 !== l.minimumResultsForSearch && (l.dropdownAdapter = j.Decorate(l.dropdownAdapter, z)), l.closeOnSelect && (l.dropdownAdapter = j.Decorate(l.dropdownAdapter, B)), null != l.dropdownCssClass || null != l.dropdownCss || null != l.adaptDropdownCssClass) {
	            var F = b(l.amdBase + "compat/dropdownCss");l.dropdownAdapter = j.Decorate(l.dropdownAdapter, F);
	          }l.dropdownAdapter = j.Decorate(l.dropdownAdapter, y);
	        }if (null == l.selectionAdapter) {
	          if (l.multiple ? l.selectionAdapter = e : l.selectionAdapter = d, null != l.placeholder && (l.selectionAdapter = j.Decorate(l.selectionAdapter, f)), l.allowClear && (l.selectionAdapter = j.Decorate(l.selectionAdapter, g)), l.multiple && (l.selectionAdapter = j.Decorate(l.selectionAdapter, h)), null != l.containerCssClass || null != l.containerCss || null != l.adaptContainerCssClass) {
	            var G = b(l.amdBase + "compat/containerCss");l.selectionAdapter = j.Decorate(l.selectionAdapter, G);
	          }l.selectionAdapter = j.Decorate(l.selectionAdapter, i);
	        }if ("string" == typeof l.language) if (l.language.indexOf("-") > 0) {
	          var H = l.language.split("-"),
	              I = H[0];l.language = [l.language, I];
	        } else l.language = [l.language];if (a.isArray(l.language)) {
	          var J = new k();l.language.push("en");for (var K = l.language, L = 0; L < K.length; L++) {
	            var M = K[L],
	                N = {};try {
	              N = k.loadPath(M);
	            } catch (O) {
	              try {
	                M = this.defaults.amdLanguageBase + M, N = k.loadPath(M);
	              } catch (P) {
	                l.debug && window.console && console.warn && console.warn('Select2: The language file for "' + M + '" could not be automatically loaded. A fallback will be used instead.');continue;
	              }
	            }J.extend(N);
	          }l.translations = J;
	        } else {
	          var Q = k.loadPath(this.defaults.amdLanguageBase + "en"),
	              R = new k(l.language);R.extend(Q), l.translations = R;
	        }return l;
	      }, D.prototype.reset = function () {
	        function b(a) {
	          function b(a) {
	            return l[a] || a;
	          }return a.replace(/[^\u0000-\u007E]/g, b);
	        }function c(d, e) {
	          if ("" === a.trim(d.term)) return e;if (e.children && e.children.length > 0) {
	            for (var f = a.extend(!0, {}, e), g = e.children.length - 1; g >= 0; g--) {
	              var h = e.children[g],
	                  i = c(d, h);null == i && f.children.splice(g, 1);
	            }return f.children.length > 0 ? f : c(d, f);
	          }var j = b(e.text).toUpperCase(),
	              k = b(d.term).toUpperCase();return j.indexOf(k) > -1 ? e : null;
	        }this.defaults = { amdBase: "./", amdLanguageBase: "./i18n/", closeOnSelect: !0, debug: !1, dropdownAutoWidth: !1, escapeMarkup: j.escapeMarkup, language: C, matcher: c, minimumInputLength: 0, maximumInputLength: 0, maximumSelectionLength: 0, minimumResultsForSearch: 0, selectOnClose: !1, sorter: function sorter(a) {
	            return a;
	          }, templateResult: function templateResult(a) {
	            return a.text;
	          }, templateSelection: function templateSelection(a) {
	            return a.text;
	          }, theme: "default", width: "resolve" };
	      }, D.prototype.set = function (b, c) {
	        var d = a.camelCase(b),
	            e = {};e[d] = c;var f = j._convertData(e);a.extend(this.defaults, f);
	      };var E = new D();return E;
	    }), b.define("select2/options", ["require", "jquery", "./defaults", "./utils"], function (a, b, c, d) {
	      function e(b, e) {
	        if (this.options = b, null != e && this.fromElement(e), this.options = c.apply(this.options), e && e.is("input")) {
	          var f = a(this.get("amdBase") + "compat/inputData");this.options.dataAdapter = d.Decorate(this.options.dataAdapter, f);
	        }
	      }return e.prototype.fromElement = function (a) {
	        var c = ["select2"];null == this.options.multiple && (this.options.multiple = a.prop("multiple")), null == this.options.disabled && (this.options.disabled = a.prop("disabled")), null == this.options.language && (a.prop("lang") ? this.options.language = a.prop("lang").toLowerCase() : a.closest("[lang]").prop("lang") && (this.options.language = a.closest("[lang]").prop("lang"))), null == this.options.dir && (a.prop("dir") ? this.options.dir = a.prop("dir") : a.closest("[dir]").prop("dir") ? this.options.dir = a.closest("[dir]").prop("dir") : this.options.dir = "ltr"), a.prop("disabled", this.options.disabled), a.prop("multiple", this.options.multiple), a.data("select2Tags") && (this.options.debug && window.console && console.warn && console.warn('Select2: The `data-select2-tags` attribute has been changed to use the `data-data` and `data-tags="true"` attributes and will be removed in future versions of Select2.'), a.data("data", a.data("select2Tags")), a.data("tags", !0)), a.data("ajaxUrl") && (this.options.debug && window.console && console.warn && console.warn("Select2: The `data-ajax-url` attribute has been changed to `data-ajax--url` and support for the old attribute will be removed in future versions of Select2."), a.attr("ajax--url", a.data("ajaxUrl")), a.data("ajax--url", a.data("ajaxUrl")));var e = {};e = b.fn.jquery && "1." == b.fn.jquery.substr(0, 2) && a[0].dataset ? b.extend(!0, {}, a[0].dataset, a.data()) : a.data();var f = b.extend(!0, {}, e);f = d._convertData(f);for (var g in f) {
	          b.inArray(g, c) > -1 || (b.isPlainObject(this.options[g]) ? b.extend(this.options[g], f[g]) : this.options[g] = f[g]);
	        }return this;
	      }, e.prototype.get = function (a) {
	        return this.options[a];
	      }, e.prototype.set = function (a, b) {
	        this.options[a] = b;
	      }, e;
	    }), b.define("select2/core", ["jquery", "./options", "./utils", "./keys"], function (a, b, c, d) {
	      var e = function e(a, c) {
	        null != a.data("select2") && a.data("select2").destroy(), this.$element = a, this.id = this._generateId(a), c = c || {}, this.options = new b(c, a), e.__super__.constructor.call(this);var d = a.attr("tabindex") || 0;a.data("old-tabindex", d), a.attr("tabindex", "-1");var f = this.options.get("dataAdapter");this.dataAdapter = new f(a, this.options);var g = this.render();this._placeContainer(g);var h = this.options.get("selectionAdapter");this.selection = new h(a, this.options), this.$selection = this.selection.render(), this.selection.position(this.$selection, g);var i = this.options.get("dropdownAdapter");this.dropdown = new i(a, this.options), this.$dropdown = this.dropdown.render(), this.dropdown.position(this.$dropdown, g);var j = this.options.get("resultsAdapter");this.results = new j(a, this.options, this.dataAdapter), this.$results = this.results.render(), this.results.position(this.$results, this.$dropdown);var k = this;this._bindAdapters(), this._registerDomEvents(), this._registerDataEvents(), this._registerSelectionEvents(), this._registerDropdownEvents(), this._registerResultsEvents(), this._registerEvents(), this.dataAdapter.current(function (a) {
	          k.trigger("selection:update", { data: a });
	        }), a.addClass("select2-hidden-accessible"), a.attr("aria-hidden", "true"), this._syncAttributes(), a.data("select2", this);
	      };return c.Extend(e, c.Observable), e.prototype._generateId = function (a) {
	        var b = "";return b = null != a.attr("id") ? a.attr("id") : null != a.attr("name") ? a.attr("name") + "-" + c.generateChars(2) : c.generateChars(4), b = "select2-" + b;
	      }, e.prototype._placeContainer = function (a) {
	        a.insertAfter(this.$element);var b = this._resolveWidth(this.$element, this.options.get("width"));null != b && a.css("width", b);
	      }, e.prototype._resolveWidth = function (a, b) {
	        var c = /^width:(([-+]?([0-9]*\.)?[0-9]+)(px|em|ex|%|in|cm|mm|pt|pc))/i;if ("resolve" == b) {
	          var d = this._resolveWidth(a, "style");return null != d ? d : this._resolveWidth(a, "element");
	        }if ("element" == b) {
	          var e = a.outerWidth(!1);return 0 >= e ? "auto" : e + "px";
	        }if ("style" == b) {
	          var f = a.attr("style");if ("string" != typeof f) return null;for (var g = f.split(";"), h = 0, i = g.length; i > h; h += 1) {
	            var j = g[h].replace(/\s/g, ""),
	                k = j.match(c);if (null !== k && k.length >= 1) return k[1];
	          }return null;
	        }return b;
	      }, e.prototype._bindAdapters = function () {
	        this.dataAdapter.bind(this, this.$container), this.selection.bind(this, this.$container), this.dropdown.bind(this, this.$container), this.results.bind(this, this.$container);
	      }, e.prototype._registerDomEvents = function () {
	        var b = this;this.$element.on("change.select2", function () {
	          b.dataAdapter.current(function (a) {
	            b.trigger("selection:update", { data: a });
	          });
	        }), this._sync = c.bind(this._syncAttributes, this), this.$element[0].attachEvent && this.$element[0].attachEvent("onpropertychange", this._sync);var d = window.MutationObserver || window.WebKitMutationObserver || window.MozMutationObserver;null != d ? (this._observer = new d(function (c) {
	          a.each(c, b._sync);
	        }), this._observer.observe(this.$element[0], { attributes: !0, subtree: !1 })) : this.$element[0].addEventListener && this.$element[0].addEventListener("DOMAttrModified", b._sync, !1);
	      }, e.prototype._registerDataEvents = function () {
	        var a = this;this.dataAdapter.on("*", function (b, c) {
	          a.trigger(b, c);
	        });
	      }, e.prototype._registerSelectionEvents = function () {
	        var b = this,
	            c = ["toggle", "focus"];this.selection.on("toggle", function () {
	          b.toggleDropdown();
	        }), this.selection.on("focus", function (a) {
	          b.focus(a);
	        }), this.selection.on("*", function (d, e) {
	          -1 === a.inArray(d, c) && b.trigger(d, e);
	        });
	      }, e.prototype._registerDropdownEvents = function () {
	        var a = this;this.dropdown.on("*", function (b, c) {
	          a.trigger(b, c);
	        });
	      }, e.prototype._registerResultsEvents = function () {
	        var a = this;this.results.on("*", function (b, c) {
	          a.trigger(b, c);
	        });
	      }, e.prototype._registerEvents = function () {
	        var a = this;this.on("open", function () {
	          a.$container.addClass("select2-container--open");
	        }), this.on("close", function () {
	          a.$container.removeClass("select2-container--open");
	        }), this.on("enable", function () {
	          a.$container.removeClass("select2-container--disabled");
	        }), this.on("disable", function () {
	          a.$container.addClass("select2-container--disabled");
	        }), this.on("blur", function () {
	          a.$container.removeClass("select2-container--focus");
	        }), this.on("query", function (b) {
	          a.isOpen() || a.trigger("open", {}), this.dataAdapter.query(b, function (c) {
	            a.trigger("results:all", { data: c, query: b });
	          });
	        }), this.on("query:append", function (b) {
	          this.dataAdapter.query(b, function (c) {
	            a.trigger("results:append", { data: c, query: b });
	          });
	        }), this.on("keypress", function (b) {
	          var c = b.which;a.isOpen() ? c === d.ESC || c === d.TAB || c === d.UP && b.altKey ? (a.close(), b.preventDefault()) : c === d.ENTER ? (a.trigger("results:select", {}), b.preventDefault()) : c === d.SPACE && b.ctrlKey ? (a.trigger("results:toggle", {}), b.preventDefault()) : c === d.UP ? (a.trigger("results:previous", {}), b.preventDefault()) : c === d.DOWN && (a.trigger("results:next", {}), b.preventDefault()) : (c === d.ENTER || c === d.SPACE || c === d.DOWN && b.altKey) && (a.open(), b.preventDefault());
	        });
	      }, e.prototype._syncAttributes = function () {
	        this.options.set("disabled", this.$element.prop("disabled")), this.options.get("disabled") ? (this.isOpen() && this.close(), this.trigger("disable", {})) : this.trigger("enable", {});
	      }, e.prototype.trigger = function (a, b) {
	        var c = e.__super__.trigger,
	            d = { open: "opening", close: "closing", select: "selecting", unselect: "unselecting" };if (void 0 === b && (b = {}), a in d) {
	          var f = d[a],
	              g = { prevented: !1, name: a, args: b };if (c.call(this, f, g), g.prevented) return void (b.prevented = !0);
	        }c.call(this, a, b);
	      }, e.prototype.toggleDropdown = function () {
	        this.options.get("disabled") || (this.isOpen() ? this.close() : this.open());
	      }, e.prototype.open = function () {
	        this.isOpen() || this.trigger("query", {});
	      }, e.prototype.close = function () {
	        this.isOpen() && this.trigger("close", {});
	      }, e.prototype.isOpen = function () {
	        return this.$container.hasClass("select2-container--open");
	      }, e.prototype.hasFocus = function () {
	        return this.$container.hasClass("select2-container--focus");
	      }, e.prototype.focus = function (a) {
	        this.hasFocus() || (this.$container.addClass("select2-container--focus"), this.trigger("focus", {}));
	      }, e.prototype.enable = function (a) {
	        this.options.get("debug") && window.console && console.warn && console.warn('Select2: The `select2("enable")` method has been deprecated and will be removed in later Select2 versions. Use $element.prop("disabled") instead.'), (null == a || 0 === a.length) && (a = [!0]);var b = !a[0];this.$element.prop("disabled", b);
	      }, e.prototype.data = function () {
	        this.options.get("debug") && arguments.length > 0 && window.console && console.warn && console.warn('Select2: Data can no longer be set using `select2("data")`. You should consider setting the value instead using `$element.val()`.');var a = [];return this.dataAdapter.current(function (b) {
	          a = b;
	        }), a;
	      }, e.prototype.val = function (b) {
	        if (this.options.get("debug") && window.console && console.warn && console.warn('Select2: The `select2("val")` method has been deprecated and will be removed in later Select2 versions. Use $element.val() instead.'), null == b || 0 === b.length) return this.$element.val();var c = b[0];a.isArray(c) && (c = a.map(c, function (a) {
	          return a.toString();
	        })), this.$element.val(c).trigger("change");
	      }, e.prototype.destroy = function () {
	        this.$container.remove(), this.$element[0].detachEvent && this.$element[0].detachEvent("onpropertychange", this._sync), null != this._observer ? (this._observer.disconnect(), this._observer = null) : this.$element[0].removeEventListener && this.$element[0].removeEventListener("DOMAttrModified", this._sync, !1), this._sync = null, this.$element.off(".select2"), this.$element.attr("tabindex", this.$element.data("old-tabindex")), this.$element.removeClass("select2-hidden-accessible"), this.$element.attr("aria-hidden", "false"), this.$element.removeData("select2"), this.dataAdapter.destroy(), this.selection.destroy(), this.dropdown.destroy(), this.results.destroy(), this.dataAdapter = null, this.selection = null, this.dropdown = null, this.results = null;
	      }, e.prototype.render = function () {
	        var b = a('<span class="select2 select2-container"><span class="selection"></span><span class="dropdown-wrapper" aria-hidden="true"></span></span>');return b.attr("dir", this.options.get("dir")), this.$container = b, this.$container.addClass("select2-container--" + this.options.get("theme")), b.data("element", this.$element), b;
	      }, e;
	    }), b.define("jquery-mousewheel", ["jquery"], function (a) {
	      return a;
	    }), b.define("jquery.select2", ["jquery", "jquery-mousewheel", "./select2/core", "./select2/defaults"], function (a, b, c, d) {
	      if (null == a.fn.select2) {
	        var e = ["open", "close", "destroy"];a.fn.select2 = function (b) {
	          if (b = b || {}, "object" == (typeof b === "undefined" ? "undefined" : _typeof(b))) return this.each(function () {
	            var d = a.extend(!0, {}, b);new c(a(this), d);
	          }), this;if ("string" == typeof b) {
	            var d;return this.each(function () {
	              var c = a(this).data("select2");null == c && window.console && console.error && console.error("The select2('" + b + "') method was called on an element that is not using Select2.");var e = Array.prototype.slice.call(arguments, 1);d = c[b].apply(c, e);
	            }), a.inArray(b, e) > -1 ? this : d;
	          }throw new Error("Invalid arguments for Select2: " + b);
	        };
	      }return null == a.fn.select2.defaults && (a.fn.select2.defaults = d), c;
	    }), { define: b.define, require: b.require };
	  }(),
	      c = b.require("jquery.select2");return a.fn.select2.amd = b, c;
	});

/***/ },
/* 12 */
/***/ function(module, exports) {

	"use strict";

	/* ========================================================================
	 * bootstrap-switch - v3.3.2
	 * http://www.bootstrap-switch.org
	 * ========================================================================
	 * Copyright 2012-2013 Mattia Larentis
	 *
	 * ========================================================================
	 * Licensed under the Apache License, Version 2.0 (the "License");
	 * you may not use this file except in compliance with the License.
	 * You may obtain a copy of the License at
	 *
	 *     http://www.apache.org/licenses/LICENSE-2.0
	 *
	 * Unless required by applicable law or agreed to in writing, software
	 * distributed under the License is distributed on an "AS IS" BASIS,
	 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
	 * See the License for the specific language governing permissions and
	 * limitations under the License.
	 * ========================================================================
	 */

	(function () {
	  var t = [].slice;!function (e, i) {
	    "use strict";
	    var n;return n = function () {
	      function t(t, i) {
	        null == i && (i = {}), this.$element = e(t), this.options = e.extend({}, e.fn.bootstrapSwitch.defaults, { state: this.$element.is(":checked"), size: this.$element.data("size"), animate: this.$element.data("animate"), disabled: this.$element.is(":disabled"), readonly: this.$element.is("[readonly]"), indeterminate: this.$element.data("indeterminate"), inverse: this.$element.data("inverse"), radioAllOff: this.$element.data("radio-all-off"), onColor: this.$element.data("on-color"), offColor: this.$element.data("off-color"), onText: this.$element.data("on-text"), offText: this.$element.data("off-text"), labelText: this.$element.data("label-text"), handleWidth: this.$element.data("handle-width"), labelWidth: this.$element.data("label-width"), baseClass: this.$element.data("base-class"), wrapperClass: this.$element.data("wrapper-class") }, i), this.$wrapper = e("<div>", { "class": function (t) {
	            return function () {
	              var e;return e = ["" + t.options.baseClass].concat(t._getClasses(t.options.wrapperClass)), e.push(t.options.state ? "" + t.options.baseClass + "-on" : "" + t.options.baseClass + "-off"), null != t.options.size && e.push("" + t.options.baseClass + "-" + t.options.size), t.options.disabled && e.push("" + t.options.baseClass + "-disabled"), t.options.readonly && e.push("" + t.options.baseClass + "-readonly"), t.options.indeterminate && e.push("" + t.options.baseClass + "-indeterminate"), t.options.inverse && e.push("" + t.options.baseClass + "-inverse"), t.$element.attr("id") && e.push("" + t.options.baseClass + "-id-" + t.$element.attr("id")), e.join(" ");
	            };
	          }(this)() }), this.$container = e("<div>", { "class": "" + this.options.baseClass + "-container" }), this.$on = e("<span>", { html: this.options.onText, "class": "" + this.options.baseClass + "-handle-on " + this.options.baseClass + "-" + this.options.onColor }), this.$off = e("<span>", { html: this.options.offText, "class": "" + this.options.baseClass + "-handle-off " + this.options.baseClass + "-" + this.options.offColor }), this.$label = e("<span>", { html: this.options.labelText, "class": "" + this.options.baseClass + "-label" }), this.$element.on("init.bootstrapSwitch", function (e) {
	          return function () {
	            return e.options.onInit.apply(t, arguments);
	          };
	        }(this)), this.$element.on("switchChange.bootstrapSwitch", function (e) {
	          return function () {
	            return e.options.onSwitchChange.apply(t, arguments);
	          };
	        }(this)), this.$container = this.$element.wrap(this.$container).parent(), this.$wrapper = this.$container.wrap(this.$wrapper).parent(), this.$element.before(this.options.inverse ? this.$off : this.$on).before(this.$label).before(this.options.inverse ? this.$on : this.$off), this.options.indeterminate && this.$element.prop("indeterminate", !0), this._init(), this._elementHandlers(), this._handleHandlers(), this._labelHandlers(), this._formHandler(), this._externalLabelHandler(), this.$element.trigger("init.bootstrapSwitch");
	      }return t.prototype._constructor = t, t.prototype.state = function (t, e) {
	        return "undefined" == typeof t ? this.options.state : this.options.disabled || this.options.readonly ? this.$element : this.options.state && !this.options.radioAllOff && this.$element.is(":radio") ? this.$element : (this.options.indeterminate && this.indeterminate(!1), t = !!t, this.$element.prop("checked", t).trigger("change.bootstrapSwitch", e), this.$element);
	      }, t.prototype.toggleState = function (t) {
	        return this.options.disabled || this.options.readonly ? this.$element : this.options.indeterminate ? (this.indeterminate(!1), this.state(!0)) : this.$element.prop("checked", !this.options.state).trigger("change.bootstrapSwitch", t);
	      }, t.prototype.size = function (t) {
	        return "undefined" == typeof t ? this.options.size : (null != this.options.size && this.$wrapper.removeClass("" + this.options.baseClass + "-" + this.options.size), t && this.$wrapper.addClass("" + this.options.baseClass + "-" + t), this._width(), this._containerPosition(), this.options.size = t, this.$element);
	      }, t.prototype.animate = function (t) {
	        return "undefined" == typeof t ? this.options.animate : (t = !!t, t === this.options.animate ? this.$element : this.toggleAnimate());
	      }, t.prototype.toggleAnimate = function () {
	        return this.options.animate = !this.options.animate, this.$wrapper.toggleClass("" + this.options.baseClass + "-animate"), this.$element;
	      }, t.prototype.disabled = function (t) {
	        return "undefined" == typeof t ? this.options.disabled : (t = !!t, t === this.options.disabled ? this.$element : this.toggleDisabled());
	      }, t.prototype.toggleDisabled = function () {
	        return this.options.disabled = !this.options.disabled, this.$element.prop("disabled", this.options.disabled), this.$wrapper.toggleClass("" + this.options.baseClass + "-disabled"), this.$element;
	      }, t.prototype.readonly = function (t) {
	        return "undefined" == typeof t ? this.options.readonly : (t = !!t, t === this.options.readonly ? this.$element : this.toggleReadonly());
	      }, t.prototype.toggleReadonly = function () {
	        return this.options.readonly = !this.options.readonly, this.$element.prop("readonly", this.options.readonly), this.$wrapper.toggleClass("" + this.options.baseClass + "-readonly"), this.$element;
	      }, t.prototype.indeterminate = function (t) {
	        return "undefined" == typeof t ? this.options.indeterminate : (t = !!t, t === this.options.indeterminate ? this.$element : this.toggleIndeterminate());
	      }, t.prototype.toggleIndeterminate = function () {
	        return this.options.indeterminate = !this.options.indeterminate, this.$element.prop("indeterminate", this.options.indeterminate), this.$wrapper.toggleClass("" + this.options.baseClass + "-indeterminate"), this._containerPosition(), this.$element;
	      }, t.prototype.inverse = function (t) {
	        return "undefined" == typeof t ? this.options.inverse : (t = !!t, t === this.options.inverse ? this.$element : this.toggleInverse());
	      }, t.prototype.toggleInverse = function () {
	        var t, e;return this.$wrapper.toggleClass("" + this.options.baseClass + "-inverse"), e = this.$on.clone(!0), t = this.$off.clone(!0), this.$on.replaceWith(t), this.$off.replaceWith(e), this.$on = t, this.$off = e, this.options.inverse = !this.options.inverse, this.$element;
	      }, t.prototype.onColor = function (t) {
	        var e;return e = this.options.onColor, "undefined" == typeof t ? e : (null != e && this.$on.removeClass("" + this.options.baseClass + "-" + e), this.$on.addClass("" + this.options.baseClass + "-" + t), this.options.onColor = t, this.$element);
	      }, t.prototype.offColor = function (t) {
	        var e;return e = this.options.offColor, "undefined" == typeof t ? e : (null != e && this.$off.removeClass("" + this.options.baseClass + "-" + e), this.$off.addClass("" + this.options.baseClass + "-" + t), this.options.offColor = t, this.$element);
	      }, t.prototype.onText = function (t) {
	        return "undefined" == typeof t ? this.options.onText : (this.$on.html(t), this._width(), this._containerPosition(), this.options.onText = t, this.$element);
	      }, t.prototype.offText = function (t) {
	        return "undefined" == typeof t ? this.options.offText : (this.$off.html(t), this._width(), this._containerPosition(), this.options.offText = t, this.$element);
	      }, t.prototype.labelText = function (t) {
	        return "undefined" == typeof t ? this.options.labelText : (this.$label.html(t), this._width(), this.options.labelText = t, this.$element);
	      }, t.prototype.handleWidth = function (t) {
	        return "undefined" == typeof t ? this.options.handleWidth : (this.options.handleWidth = t, this._width(), this._containerPosition(), this.$element);
	      }, t.prototype.labelWidth = function (t) {
	        return "undefined" == typeof t ? this.options.labelWidth : (this.options.labelWidth = t, this._width(), this._containerPosition(), this.$element);
	      }, t.prototype.baseClass = function () {
	        return this.options.baseClass;
	      }, t.prototype.wrapperClass = function (t) {
	        return "undefined" == typeof t ? this.options.wrapperClass : (t || (t = e.fn.bootstrapSwitch.defaults.wrapperClass), this.$wrapper.removeClass(this._getClasses(this.options.wrapperClass).join(" ")), this.$wrapper.addClass(this._getClasses(t).join(" ")), this.options.wrapperClass = t, this.$element);
	      }, t.prototype.radioAllOff = function (t) {
	        return "undefined" == typeof t ? this.options.radioAllOff : (t = !!t, t === this.options.radioAllOff ? this.$element : (this.options.radioAllOff = t, this.$element));
	      }, t.prototype.onInit = function (t) {
	        return "undefined" == typeof t ? this.options.onInit : (t || (t = e.fn.bootstrapSwitch.defaults.onInit), this.options.onInit = t, this.$element);
	      }, t.prototype.onSwitchChange = function (t) {
	        return "undefined" == typeof t ? this.options.onSwitchChange : (t || (t = e.fn.bootstrapSwitch.defaults.onSwitchChange), this.options.onSwitchChange = t, this.$element);
	      }, t.prototype.destroy = function () {
	        var t;return t = this.$element.closest("form"), t.length && t.off("reset.bootstrapSwitch").removeData("bootstrap-switch"), this.$container.children().not(this.$element).remove(), this.$element.unwrap().unwrap().off(".bootstrapSwitch").removeData("bootstrap-switch"), this.$element;
	      }, t.prototype._width = function () {
	        var t, e;return t = this.$on.add(this.$off), t.add(this.$label).css("width", ""), e = "auto" === this.options.handleWidth ? Math.max(this.$on.width(), this.$off.width()) : this.options.handleWidth, t.width(e), this.$label.width(function (t) {
	          return function (i, n) {
	            return "auto" !== t.options.labelWidth ? t.options.labelWidth : e > n ? e : n;
	          };
	        }(this)), this._handleWidth = this.$on.outerWidth(), this._labelWidth = this.$label.outerWidth(), this.$container.width(2 * this._handleWidth + this._labelWidth), this.$wrapper.width(this._handleWidth + this._labelWidth);
	      }, t.prototype._containerPosition = function (t, e) {
	        return null == t && (t = this.options.state), this.$container.css("margin-left", function (e) {
	          return function () {
	            var i;return i = [0, "-" + e._handleWidth + "px"], e.options.indeterminate ? "-" + e._handleWidth / 2 + "px" : t ? e.options.inverse ? i[1] : i[0] : e.options.inverse ? i[0] : i[1];
	          };
	        }(this)), e ? setTimeout(function () {
	          return e();
	        }, 50) : void 0;
	      }, t.prototype._init = function () {
	        var t, e;return t = function (t) {
	          return function () {
	            return t._width(), t._containerPosition(null, function () {
	              return t.options.animate ? t.$wrapper.addClass("" + t.options.baseClass + "-animate") : void 0;
	            });
	          };
	        }(this), this.$wrapper.is(":visible") ? t() : e = i.setInterval(function (n) {
	          return function () {
	            return n.$wrapper.is(":visible") ? (t(), i.clearInterval(e)) : void 0;
	          };
	        }(this), 50);
	      }, t.prototype._elementHandlers = function () {
	        return this.$element.on({ "change.bootstrapSwitch": function (t) {
	            return function (i, n) {
	              var o;return i.preventDefault(), i.stopImmediatePropagation(), o = t.$element.is(":checked"), t._containerPosition(o), o !== t.options.state ? (t.options.state = o, t.$wrapper.toggleClass("" + t.options.baseClass + "-off").toggleClass("" + t.options.baseClass + "-on"), n ? void 0 : (t.$element.is(":radio") && e("[name='" + t.$element.attr("name") + "']").not(t.$element).prop("checked", !1).trigger("change.bootstrapSwitch", !0), t.$element.trigger("switchChange.bootstrapSwitch", [o]))) : void 0;
	            };
	          }(this), "focus.bootstrapSwitch": function (t) {
	            return function (e) {
	              return e.preventDefault(), t.$wrapper.addClass("" + t.options.baseClass + "-focused");
	            };
	          }(this), "blur.bootstrapSwitch": function (t) {
	            return function (e) {
	              return e.preventDefault(), t.$wrapper.removeClass("" + t.options.baseClass + "-focused");
	            };
	          }(this), "keydown.bootstrapSwitch": function (t) {
	            return function (e) {
	              if (e.which && !t.options.disabled && !t.options.readonly) switch (e.which) {case 37:
	                  return e.preventDefault(), e.stopImmediatePropagation(), t.state(!1);case 39:
	                  return e.preventDefault(), e.stopImmediatePropagation(), t.state(!0);}
	            };
	          }(this) });
	      }, t.prototype._handleHandlers = function () {
	        return this.$on.on("click.bootstrapSwitch", function (t) {
	          return function (e) {
	            return e.preventDefault(), e.stopPropagation(), t.state(!1), t.$element.trigger("focus.bootstrapSwitch");
	          };
	        }(this)), this.$off.on("click.bootstrapSwitch", function (t) {
	          return function (e) {
	            return e.preventDefault(), e.stopPropagation(), t.state(!0), t.$element.trigger("focus.bootstrapSwitch");
	          };
	        }(this));
	      }, t.prototype._labelHandlers = function () {
	        return this.$label.on({ "mousedown.bootstrapSwitch touchstart.bootstrapSwitch": function (t) {
	            return function (e) {
	              return t._dragStart || t.options.disabled || t.options.readonly ? void 0 : (e.preventDefault(), e.stopPropagation(), t._dragStart = (e.pageX || e.originalEvent.touches[0].pageX) - parseInt(t.$container.css("margin-left"), 10), t.options.animate && t.$wrapper.removeClass("" + t.options.baseClass + "-animate"), t.$element.trigger("focus.bootstrapSwitch"));
	            };
	          }(this), "mousemove.bootstrapSwitch touchmove.bootstrapSwitch": function (t) {
	            return function (e) {
	              var i;if (null != t._dragStart && (e.preventDefault(), i = (e.pageX || e.originalEvent.touches[0].pageX) - t._dragStart, !(i < -t._handleWidth || i > 0))) return t._dragEnd = i, t.$container.css("margin-left", "" + t._dragEnd + "px");
	            };
	          }(this), "mouseup.bootstrapSwitch touchend.bootstrapSwitch": function (t) {
	            return function (e) {
	              var i;if (t._dragStart) return e.preventDefault(), t.options.animate && t.$wrapper.addClass("" + t.options.baseClass + "-animate"), t._dragEnd ? (i = t._dragEnd > -(t._handleWidth / 2), t._dragEnd = !1, t.state(t.options.inverse ? !i : i)) : t.state(!t.options.state), t._dragStart = !1;
	            };
	          }(this), "mouseleave.bootstrapSwitch": function (t) {
	            return function () {
	              return t.$label.trigger("mouseup.bootstrapSwitch");
	            };
	          }(this) });
	      }, t.prototype._externalLabelHandler = function () {
	        var t;return t = this.$element.closest("label"), t.on("click", function (e) {
	          return function (i) {
	            return i.preventDefault(), i.stopImmediatePropagation(), i.target === t[0] ? e.toggleState() : void 0;
	          };
	        }(this));
	      }, t.prototype._formHandler = function () {
	        var t;return t = this.$element.closest("form"), t.data("bootstrap-switch") ? void 0 : t.on("reset.bootstrapSwitch", function () {
	          return i.setTimeout(function () {
	            return t.find("input").filter(function () {
	              return e(this).data("bootstrap-switch");
	            }).each(function () {
	              return e(this).bootstrapSwitch("state", this.checked);
	            });
	          }, 1);
	        }).data("bootstrap-switch", !0);
	      }, t.prototype._getClasses = function (t) {
	        var i, n, o, s;if (!e.isArray(t)) return ["" + this.options.baseClass + "-" + t];for (n = [], o = 0, s = t.length; s > o; o++) {
	          i = t[o], n.push("" + this.options.baseClass + "-" + i);
	        }return n;
	      }, t;
	    }(), e.fn.bootstrapSwitch = function () {
	      var i, o, s;return o = arguments[0], i = 2 <= arguments.length ? t.call(arguments, 1) : [], s = this, this.each(function () {
	        var t, a;return t = e(this), a = t.data("bootstrap-switch"), a || t.data("bootstrap-switch", a = new n(this, o)), "string" == typeof o ? s = a[o].apply(a, i) : void 0;
	      }), s;
	    }, e.fn.bootstrapSwitch.Constructor = n, e.fn.bootstrapSwitch.defaults = { state: !0, size: null, animate: !0, disabled: !1, readonly: !1, indeterminate: !1, inverse: !1, radioAllOff: !1, onColor: "primary", offColor: "default", onText: "ON", offText: "OFF", labelText: "&nbsp;", handleWidth: "auto", labelWidth: "auto", baseClass: "bootstrap-switch", wrapperClass: "wrapper", onInit: function onInit() {}, onSwitchChange: function onSwitchChange() {} };
	  }(window.jQuery, window);
	}).call(undefined);

/***/ },
/* 13 */
/***/ function(module, exports) {

	"use strict";

	/*
	* jQuery pstagger plugin
	* https://www.prestashop.com
	*
	* Copyright PrestaShop and other contributors
	* Released under the MIT license
	* https://tldrlegal.com/license/mit-license
	*
	* Date: 2016-01-13
	*/

	(function (c) {
	  var b = null,
	      g = [],
	      d = null,
	      k = { wrapperClassAdditional: "", tagsWrapperClassAdditional: "", tagClassAdditional: "", closingCrossClassAdditionnal: "", tagInputWrapperClassAdditional: "", tagInputClassAdditional: "", delimiter: " ", inputPlaceholder: "Add tag ...", closingCross: !0, context: null, clearAllBtn: !1, clearAllIconClassAdditional: "", clearAllSpanClassAdditional: "", onTagsChanged: null, onResetTags: null },
	      m = function m() {
	    d.keypress(function (a) {
	      13 == a.keyCode && (g = [], h());
	    });d.focusout(function (a) {
	      if (c(".pstaggerResetTagsBtn:hover").length) return !1;
	      d.val().length && (g = [], h());
	    });
	  },
	      h = function h() {
	    var a = d.val(),
	        e = a.split(b.delimiter);if (a.length) {
	      for (var f in e) {
	        a = e[f], "" !== a && g.push(a);
	      }e = "";for (f in g) {
	        a = g[f], a = '<span class="pstaggerTag ' + b.tagClassAdditional + '"><span>' + c("<div/>").text(a).html() + "</span>", !0 === b.closingCross && (a += '<a class="pstaggerClosingCross ' + b.closingCrossClassAdditionnal + '" href="#">x</a>'), a += "</span>", e += a;
	      }c(".pstaggerTagsWrapper").empty().prepend(e).css("display", "block");c(".pstaggerAddTagWrapper").css("display", "none");
	    } else c(".pstaggerTagsWrapper").css("display", "none"), c(".pstaggerAddTagWrapper").css("display", "block"), d.focus();null !== b.onTagsChanged && b.onTagsChanged.call(b.context, g);
	  },
	      n = function n() {
	    c(".pstaggerTagsWrapper").on("click", function (a) {
	      a = a.target.className.match(RegExp("pstaggerClosingCross", "g"));c(".pstaggerAddTagWrapper").is(":hidden") && null === a && (c(".pstaggerTagsWrapper").css("display", "none"), c(".pstaggerAddTagWrapper").css("display", "block"), d.focus());
	    });
	  },
	      p = function p() {
	    c(document).delegate(".pstaggerResetTagsBtn", "click", function () {
	      l(!0);
	    });
	  },
	      l = function l(a) {
	    g = [];d.val("");c(".pstaggerTagsWrapper").css("display", "none");c(".pstaggerAddTagWrapper").css("display", "block");d.focus();c(".pstaggerTag").remove();null !== b.onResetTags && !0 === a && b.onResetTags.call(b.context);
	  },
	      r = function r() {
	    c(document).delegate(".pstaggerClosingCross", "click", function (a) {
	      a = c(this).parent();var b = a.index(),
	          b = q(b);d.val(b);a.remove();g = [];h();
	    });
	  },
	      q = function q(a) {
	    var b = "";c(".pstaggerTag").each(function (d, g) {
	      if (a == c(this).index()) return !0;b += " " + c(this).children().first().text();
	    });
	    return b;
	  };c.fn.pstagger = function (a) {
	    var e = {},
	        f;for (f in k) {
	      a.hasOwnProperty(f) ? e[f] = a[f] : e[f] = k[f];
	    }e.originalInput = this;b = e;b.originalInput.css("display", "none");a = "";!0 === b.clearAllBtn && (a += '<span class="pstaggerResetTagsBtn ' + b.clearAllSpanClassAdditional + '"><i class=" ' + b.clearAllIconClassAdditional + '"></i></span>', p());b.originalInput.after('<div class="pstaggerWrapper ' + b.wrapperClassAdditional + '">' + a + '<div class="pstaggerTagsWrapper ' + b.tagsWrapperClassAdditional + '"></div><div class="pstaggerAddTagWrapper ' + b.tagInputWrapperClassAdditional + '"><input class="pstaggerAddTagInput ' + b.tagInputClassAdditional + '"></div></div>');d = c(".pstaggerAddTagInput");d.attr("placeholder", b.inputPlaceholder);m();n();r();return { resetTags: l };
	  };
	})(jQuery);

/***/ },
/* 14 */
/***/ function(module, exports) {

	"use strict";

	/*!
	 * Prestige v1.0.0 (http://getbootstrap.com)
	 * Copyright 2015-2015
	 * Copy License
	 */

	$(function () {

	    // Select2
	    //
	    // Set default theme to prestakit for Select2
	    $.fn.select2.defaults.set("theme", "prestakit");

	    // Template
	    function formatData(data) {
	        var $res = $('<span></span>');
	        var $check = $('<input type="checkbox" />');
	        $res.text(data.text);
	        if (data.element) {
	            $res.prepend($check);
	            $check.prop('checked', data.element.selected);
	        }
	        return $res;
	    };

	    function formatRepo(data) {
	        console.log(data);
	        var $res = $('<span class="select2-selection__tags"></span>');
	        $res.text(data.text);
	        return $res;
	    }

	    // Enable Select2 everywhere
	    //
	    // TODO Add templateResult
	    $('[data-toggle="select2"]').each(function () {

	        var newObj = { "minimumResultsForSearch": -1 };

	        for (var attr in $(this).data()) {
	            if (!attr.localeCompare("templateresult")) newObj["templateResult"] = eval($(this).data()[attr]);else if (!attr.localeCompare("templateselection")) newObj["templateSelection"] = eval($(this).data()[attr]);else if (!attr.localeCompare("minimumresultsforsearch")) newObj["minimumResultsForSearch"] = $(this).data()[attr];else if (attr.localeCompare("toggle")) newObj[attr] = $(this).data()[attr];
	        }

	        $(this).select2(newObj);
	    });

	    // Enable Switch Button everywhere
	    $('[data-toggle="switch"]').bootstrapSwitch({
	        onText: '<i class="material-icons">check</i>',
	        offText: '<i class="material-icons">close</i>'
	    });

	    // Enable tooltips everywhere
	    $('[data-toggle="tooltip"]').tooltip();

	    // Error tooltips template
	    var options = {
	        template: '<div class="tooltip"><div class="tooltip-error"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div></div>'
	    };

	    // Enable Error tooltips
	    $('.tooltip-error').tooltip(options);

	    // Enable popovers everywhere
	    $('[data-toggle="popover"]').popover();

	    // Keep unique configuration
	    var setConfig = function setConfig(givenConfig, defaultConfig) {
	        var finalConfig = {};
	        for (var property in defaultConfig) {
	            if (givenConfig.hasOwnProperty(property)) {
	                finalConfig[property] = givenConfig[property];
	            } else {
	                finalConfig[property] = defaultConfig[property];
	            }
	        }
	        return finalConfig;
	    };

	    // Spinner
	    // @TODO: Add addEventListener, prototype, callback
	    // 1.0.0
	    $.fn.psdwl = function (_config) {
	        var config = null;

	        // Default Configuration
	        var defaultConfig = {
	            hover: 'install',
	            validate: '<i class="material-icons">check</i>',
	            text: 'default',
	            time: 3000,
	            default: true
	        };

	        var psdwl = this;
	        config = setConfig(_config, defaultConfig);

	        if (config.default) {
	            var value = psdwl.attr('class').replace(/(btn-\w+)/, "$1-reverse");
	            psdwl.attr('class', value);
	        }

	        if (typeof $(psdwl.selector).html() != "undefined" && $(psdwl.selector).html() !== "") {
	            config.text = $(psdwl.selector).text();
	        }

	        // Width correction on Hover
	        psdwl.html(config.hover);
	        var hw = this.css('width');
	        psdwl.html(config.text);
	        var w = this.css('width');

	        // Higher width or default
	        var width = parseInt(w, 10) < parseInt(hw, 10) ? hw : w;
	        width = parseInt(width, 10) < 95 ? '95px' : width;

	        psdwl.css('width', width);

	        psdwl.hover(function () {
	            psdwl.html(config.hover);
	        }, function () {
	            psdwl.html(config.text);
	        });

	        psdwl.click(function () {
	            psdwl.css('border-left-color', psdwl.css('border-color'));
	            psdwl.addClass('onclick');
	            psdwl.unbind('mouseenter').unbind('mouseleave').unbind('click');
	            var nw = parseInt(width, 10);
	            psdwl.css({
	                'width': '',
	                'margin-left': nw / 4,
	                'margin-right': nw / 4
	            });

	            setTimeout(function () {
	                psdwl.removeClass("onclick");
	                psdwl.css({
	                    'margin-left': '',
	                    'margin-right': '',
	                    'width': width,
	                    'border-left-color': ''
	                });
	                psdwl.html(config.validate);

	                if (config.default) {
	                    var value = psdwl.attr('class').replace('-reverse', "");
	                    psdwl.attr('class', value);
	                }
	            }, config.time);
	        });
	    };

	    // Apply toggle is long text
	    $(".alert-text").each(function () {
	        var height = $(this).height();
	        var lineHeight = parseFloat($(this).css("lineHeight"));
	        var rows = height / lineHeight;
	        var lines = 5;

	        if (Math.ceil(rows) > lines) {
	            var actualHtml = $(this).html();
	            var actualClass = $(this).parent().attr('class');
	            $(this).parent().addClass("alert-drop");

	            if (typeof $(this).data('title') != "undefined" && $(this).data('title') !== "") {
	                $(this).html('<b>' + $(this).data('title') + '</b>');
	            } else {
	                $(this).html("<b>Read More</b>");
	            }
	            $(this).css('cursor', 'pointer');
	            $(this).parent().after('<div class="' + actualClass + ' alert-down" role="alert"><p class="alert-down-text"></p></div>');
	            $(".alert-down-text").html(actualHtml);
	        }
	    });

	    $('.alert-drop').each(function () {
	        $(this).click(function () {
	            var radius = $(this).css("border-radius");
	            if ($(this).next('div').is(":hidden")) {
	                $(this).css("border-radius", '0');
	                $(this).css("border-bottom", 'none');
	            } else {
	                $(this).css("border-radius", radius);
	                $(this).css("border-bottom", '');
	            }
	            $(this).next('div').slideToggle(400);
	        });
	    });
	});

/***/ },
/* 15 */
/***/ function(module, exports) {

	// removed by extract-text-webpack-plugin

/***/ },
/* 16 */,
/* 17 */,
/* 18 */,
/* 19 */
/***/ function(module, exports) {

	// removed by extract-text-webpack-plugin

/***/ },
/* 20 */,
/* 21 */
/***/ function(module, exports) {

	// removed by extract-text-webpack-plugin

/***/ },
/* 22 */,
/* 23 */,
/* 24 */,
/* 25 */,
/* 26 */,
/* 27 */,
/* 28 */
/***/ function(module, exports) {

	// removed by extract-text-webpack-plugin

/***/ },
/* 29 */,
/* 30 */
/***/ function(module, exports) {

	// removed by extract-text-webpack-plugin

/***/ },
/* 31 */,
/* 32 */
/***/ function(module, exports) {

	// removed by extract-text-webpack-plugin

/***/ },
/* 33 */,
/* 34 */,
/* 35 */,
/* 36 */,
/* 37 */
/***/ function(module, exports, __webpack_require__) {

	"use strict";

	Object.defineProperty(exports, "__esModule", {
	  value: true
	});

	var _jquery = __webpack_require__(4);

	var _jquery2 = _interopRequireDefault(_jquery);

	function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

	function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

	var NavBar = function NavBar() {
	  _classCallCheck(this, NavBar);

	  (0, _jquery2.default)(function () {
	    (0, _jquery2.default)(".nav-bar").find(".link-levelone").hover(function () {
	      (0, _jquery2.default)(this).addClass("-hover");
	    }, function () {
	      (0, _jquery2.default)(this).removeClass("-hover");
	    });
	  });
	};

	exports.default = NavBar;

/***/ }
/******/ ]);