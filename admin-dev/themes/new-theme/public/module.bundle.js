window["module"] =
/******/ (function(modules) { // webpackBootstrap
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
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
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
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 511);
/******/ })
/************************************************************************/
/******/ ({

/***/ 0:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


exports.__esModule = true;

exports.default = function (instance, Constructor) {
  if (!(instance instanceof Constructor)) {
    throw new TypeError("Cannot call a class as a function");
  }
};

/***/ }),

/***/ 1:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


exports.__esModule = true;

var _defineProperty = __webpack_require__(19);

var _defineProperty2 = _interopRequireDefault(_defineProperty);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

exports.default = function () {
  function defineProperties(target, props) {
    for (var i = 0; i < props.length; i++) {
      var descriptor = props[i];
      descriptor.enumerable = descriptor.enumerable || false;
      descriptor.configurable = true;
      if ("value" in descriptor) descriptor.writable = true;
      (0, _defineProperty2.default)(target, descriptor.key, descriptor);
    }
  }

  return function (Constructor, protoProps, staticProps) {
    if (protoProps) defineProperties(Constructor.prototype, protoProps);
    if (staticProps) defineProperties(Constructor, staticProps);
    return Constructor;
  };
}();

/***/ }),

/***/ 10:
/***/ (function(module, exports, __webpack_require__) {

var dP         = __webpack_require__(6)
  , createDesc = __webpack_require__(12);
module.exports = __webpack_require__(2) ? function(object, key, value){
  return dP.f(object, key, createDesc(1, value));
} : function(object, key, value){
  object[key] = value;
  return object;
};

/***/ }),

/***/ 100:
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var addToUnscopables = __webpack_require__(96)
  , step             = __webpack_require__(94)
  , Iterators        = __webpack_require__(51)
  , toIObject        = __webpack_require__(22);

// 22.1.3.4 Array.prototype.entries()
// 22.1.3.13 Array.prototype.keys()
// 22.1.3.29 Array.prototype.values()
// 22.1.3.30 Array.prototype[@@iterator]()
module.exports = __webpack_require__(67)(Array, 'Array', function(iterated, kind){
  this._t = toIObject(iterated); // target
  this._i = 0;                   // next index
  this._k = kind;                // kind
// 22.1.5.2.1 %ArrayIteratorPrototype%.next()
}, function(){
  var O     = this._t
    , kind  = this._k
    , index = this._i++;
  if(!O || index >= O.length){
    this._t = undefined;
    return step(1);
  }
  if(kind == 'keys'  )return step(0, index);
  if(kind == 'values')return step(0, O[index]);
  return step(0, [index, O[index]]);
}, 'values');

// argumentsList[@@iterator] is %ArrayProto_values% (9.4.4.6, 9.4.4.7)
Iterators.Arguments = Iterators.Array;

addToUnscopables('keys');
addToUnscopables('values');
addToUnscopables('entries');

/***/ }),

/***/ 102:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


exports.__esModule = true;

var _iterator = __webpack_require__(114);

var _iterator2 = _interopRequireDefault(_iterator);

var _symbol = __webpack_require__(113);

var _symbol2 = _interopRequireDefault(_symbol);

var _typeof = typeof _symbol2.default === "function" && typeof _iterator2.default === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof _symbol2.default === "function" && obj.constructor === _symbol2.default && obj !== _symbol2.default.prototype ? "symbol" : typeof obj; };

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

exports.default = typeof _symbol2.default === "function" && _typeof(_iterator2.default) === "symbol" ? function (obj) {
  return typeof obj === "undefined" ? "undefined" : _typeof(obj);
} : function (obj) {
  return obj && typeof _symbol2.default === "function" && obj.constructor === _symbol2.default && obj !== _symbol2.default.prototype ? "symbol" : typeof obj === "undefined" ? "undefined" : _typeof(obj);
};

/***/ }),

/***/ 103:
/***/ (function(module, exports, __webpack_require__) {

var META     = __webpack_require__(40)('meta')
  , isObject = __webpack_require__(4)
  , has      = __webpack_require__(25)
  , setDesc  = __webpack_require__(6).f
  , id       = 0;
var isExtensible = Object.isExtensible || function(){
  return true;
};
var FREEZE = !__webpack_require__(7)(function(){
  return isExtensible(Object.preventExtensions({}));
});
var setMeta = function(it){
  setDesc(it, META, {value: {
    i: 'O' + ++id, // object ID
    w: {}          // weak collections IDs
  }});
};
var fastKey = function(it, create){
  // return primitive with prefix
  if(!isObject(it))return typeof it == 'symbol' ? it : (typeof it == 'string' ? 'S' : 'P') + it;
  if(!has(it, META)){
    // can't set metadata to uncaught frozen object
    if(!isExtensible(it))return 'F';
    // not necessary to add metadata
    if(!create)return 'E';
    // add missing metadata
    setMeta(it);
  // return object ID
  } return it[META].i;
};
var getWeak = function(it, create){
  if(!has(it, META)){
    // can't set metadata to uncaught frozen object
    if(!isExtensible(it))return true;
    // not necessary to add metadata
    if(!create)return false;
    // add missing metadata
    setMeta(it);
  // return hash weak collections IDs
  } return it[META].w;
};
// add metadata on freeze-family methods calling
var onFreeze = function(it){
  if(FREEZE && meta.NEED && isExtensible(it) && !has(it, META))setMeta(it);
  return it;
};
var meta = module.exports = {
  KEY:      META,
  NEED:     false,
  fastKey:  fastKey,
  getWeak:  getWeak,
  onFreeze: onFreeze
};

/***/ }),

/***/ 104:
/***/ (function(module, exports, __webpack_require__) {

var pIE            = __webpack_require__(54)
  , createDesc     = __webpack_require__(12)
  , toIObject      = __webpack_require__(22)
  , toPrimitive    = __webpack_require__(14)
  , has            = __webpack_require__(25)
  , IE8_DOM_DEFINE = __webpack_require__(17)
  , gOPD           = Object.getOwnPropertyDescriptor;

exports.f = __webpack_require__(2) ? gOPD : function getOwnPropertyDescriptor(O, P){
  O = toIObject(O);
  P = toPrimitive(P, true);
  if(IE8_DOM_DEFINE)try {
    return gOPD(O, P);
  } catch(e){ /* empty */ }
  if(has(O, P))return createDesc(!pIE.f.call(O, P), O[P]);
};

/***/ }),

/***/ 105:
/***/ (function(module, exports) {



/***/ }),

/***/ 107:
/***/ (function(module, exports, __webpack_require__) {

// 7.2.2 IsArray(argument)
var cof = __webpack_require__(48);
module.exports = Array.isArray || function isArray(arg){
  return cof(arg) == 'Array';
};

/***/ }),

/***/ 11:
/***/ (function(module, exports, __webpack_require__) {

var isObject = __webpack_require__(4);
module.exports = function(it){
  if(!isObject(it))throw TypeError(it + ' is not an object!');
  return it;
};

/***/ }),

/***/ 113:
/***/ (function(module, exports, __webpack_require__) {

module.exports = { "default": __webpack_require__(117), __esModule: true };

/***/ }),

/***/ 114:
/***/ (function(module, exports, __webpack_require__) {

module.exports = { "default": __webpack_require__(118), __esModule: true };

/***/ }),

/***/ 117:
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(124);
__webpack_require__(105);
__webpack_require__(125);
__webpack_require__(126);
module.exports = __webpack_require__(3).Symbol;

/***/ }),

/***/ 118:
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(64);
__webpack_require__(73);
module.exports = __webpack_require__(72).f('iterator');

/***/ }),

/***/ 119:
/***/ (function(module, exports, __webpack_require__) {

// all enumerable object keys, includes symbols
var getKeys = __webpack_require__(33)
  , gOPS    = __webpack_require__(59)
  , pIE     = __webpack_require__(54);
module.exports = function(it){
  var result     = getKeys(it)
    , getSymbols = gOPS.f;
  if(getSymbols){
    var symbols = getSymbols(it)
      , isEnum  = pIE.f
      , i       = 0
      , key;
    while(symbols.length > i)if(isEnum.call(it, key = symbols[i++]))result.push(key);
  } return result;
};

/***/ }),

/***/ 12:
/***/ (function(module, exports) {

module.exports = function(bitmap, value){
  return {
    enumerable  : !(bitmap & 1),
    configurable: !(bitmap & 2),
    writable    : !(bitmap & 4),
    value       : value
  };
};

/***/ }),

/***/ 122:
/***/ (function(module, exports, __webpack_require__) {

var getKeys   = __webpack_require__(33)
  , toIObject = __webpack_require__(22);
module.exports = function(object, el){
  var O      = toIObject(object)
    , keys   = getKeys(O)
    , length = keys.length
    , index  = 0
    , key;
  while(length > index)if(O[key = keys[index++]] === el)return key;
};

/***/ }),

/***/ 123:
/***/ (function(module, exports, __webpack_require__) {

// fallback for IE11 buggy Object.getOwnPropertyNames with iframe and window
var toIObject = __webpack_require__(22)
  , gOPN      = __webpack_require__(88).f
  , toString  = {}.toString;

var windowNames = typeof window == 'object' && window && Object.getOwnPropertyNames
  ? Object.getOwnPropertyNames(window) : [];

var getWindowNames = function(it){
  try {
    return gOPN(it);
  } catch(e){
    return windowNames.slice();
  }
};

module.exports.f = function getOwnPropertyNames(it){
  return windowNames && toString.call(it) == '[object Window]' ? getWindowNames(it) : gOPN(toIObject(it));
};


/***/ }),

/***/ 124:
/***/ (function(module, exports, __webpack_require__) {

"use strict";

// ECMAScript 6 symbols shim
var global         = __webpack_require__(5)
  , has            = __webpack_require__(25)
  , DESCRIPTORS    = __webpack_require__(2)
  , $export        = __webpack_require__(8)
  , redefine       = __webpack_require__(76)
  , META           = __webpack_require__(103).KEY
  , $fails         = __webpack_require__(7)
  , shared         = __webpack_require__(50)
  , setToStringTag = __webpack_require__(60)
  , uid            = __webpack_require__(40)
  , wks            = __webpack_require__(23)
  , wksExt         = __webpack_require__(72)
  , wksDefine      = __webpack_require__(71)
  , keyOf          = __webpack_require__(122)
  , enumKeys       = __webpack_require__(119)
  , isArray        = __webpack_require__(107)
  , anObject       = __webpack_require__(11)
  , toIObject      = __webpack_require__(22)
  , toPrimitive    = __webpack_require__(14)
  , createDesc     = __webpack_require__(12)
  , _create        = __webpack_require__(66)
  , gOPNExt        = __webpack_require__(123)
  , $GOPD          = __webpack_require__(104)
  , $DP            = __webpack_require__(6)
  , $keys          = __webpack_require__(33)
  , gOPD           = $GOPD.f
  , dP             = $DP.f
  , gOPN           = gOPNExt.f
  , $Symbol        = global.Symbol
  , $JSON          = global.JSON
  , _stringify     = $JSON && $JSON.stringify
  , PROTOTYPE      = 'prototype'
  , HIDDEN         = wks('_hidden')
  , TO_PRIMITIVE   = wks('toPrimitive')
  , isEnum         = {}.propertyIsEnumerable
  , SymbolRegistry = shared('symbol-registry')
  , AllSymbols     = shared('symbols')
  , OPSymbols      = shared('op-symbols')
  , ObjectProto    = Object[PROTOTYPE]
  , USE_NATIVE     = typeof $Symbol == 'function'
  , QObject        = global.QObject;
// Don't use setters in Qt Script, https://github.com/zloirock/core-js/issues/173
var setter = !QObject || !QObject[PROTOTYPE] || !QObject[PROTOTYPE].findChild;

// fallback for old Android, https://code.google.com/p/v8/issues/detail?id=687
var setSymbolDesc = DESCRIPTORS && $fails(function(){
  return _create(dP({}, 'a', {
    get: function(){ return dP(this, 'a', {value: 7}).a; }
  })).a != 7;
}) ? function(it, key, D){
  var protoDesc = gOPD(ObjectProto, key);
  if(protoDesc)delete ObjectProto[key];
  dP(it, key, D);
  if(protoDesc && it !== ObjectProto)dP(ObjectProto, key, protoDesc);
} : dP;

var wrap = function(tag){
  var sym = AllSymbols[tag] = _create($Symbol[PROTOTYPE]);
  sym._k = tag;
  return sym;
};

var isSymbol = USE_NATIVE && typeof $Symbol.iterator == 'symbol' ? function(it){
  return typeof it == 'symbol';
} : function(it){
  return it instanceof $Symbol;
};

var $defineProperty = function defineProperty(it, key, D){
  if(it === ObjectProto)$defineProperty(OPSymbols, key, D);
  anObject(it);
  key = toPrimitive(key, true);
  anObject(D);
  if(has(AllSymbols, key)){
    if(!D.enumerable){
      if(!has(it, HIDDEN))dP(it, HIDDEN, createDesc(1, {}));
      it[HIDDEN][key] = true;
    } else {
      if(has(it, HIDDEN) && it[HIDDEN][key])it[HIDDEN][key] = false;
      D = _create(D, {enumerable: createDesc(0, false)});
    } return setSymbolDesc(it, key, D);
  } return dP(it, key, D);
};
var $defineProperties = function defineProperties(it, P){
  anObject(it);
  var keys = enumKeys(P = toIObject(P))
    , i    = 0
    , l = keys.length
    , key;
  while(l > i)$defineProperty(it, key = keys[i++], P[key]);
  return it;
};
var $create = function create(it, P){
  return P === undefined ? _create(it) : $defineProperties(_create(it), P);
};
var $propertyIsEnumerable = function propertyIsEnumerable(key){
  var E = isEnum.call(this, key = toPrimitive(key, true));
  if(this === ObjectProto && has(AllSymbols, key) && !has(OPSymbols, key))return false;
  return E || !has(this, key) || !has(AllSymbols, key) || has(this, HIDDEN) && this[HIDDEN][key] ? E : true;
};
var $getOwnPropertyDescriptor = function getOwnPropertyDescriptor(it, key){
  it  = toIObject(it);
  key = toPrimitive(key, true);
  if(it === ObjectProto && has(AllSymbols, key) && !has(OPSymbols, key))return;
  var D = gOPD(it, key);
  if(D && has(AllSymbols, key) && !(has(it, HIDDEN) && it[HIDDEN][key]))D.enumerable = true;
  return D;
};
var $getOwnPropertyNames = function getOwnPropertyNames(it){
  var names  = gOPN(toIObject(it))
    , result = []
    , i      = 0
    , key;
  while(names.length > i){
    if(!has(AllSymbols, key = names[i++]) && key != HIDDEN && key != META)result.push(key);
  } return result;
};
var $getOwnPropertySymbols = function getOwnPropertySymbols(it){
  var IS_OP  = it === ObjectProto
    , names  = gOPN(IS_OP ? OPSymbols : toIObject(it))
    , result = []
    , i      = 0
    , key;
  while(names.length > i){
    if(has(AllSymbols, key = names[i++]) && (IS_OP ? has(ObjectProto, key) : true))result.push(AllSymbols[key]);
  } return result;
};

// 19.4.1.1 Symbol([description])
if(!USE_NATIVE){
  $Symbol = function Symbol(){
    if(this instanceof $Symbol)throw TypeError('Symbol is not a constructor!');
    var tag = uid(arguments.length > 0 ? arguments[0] : undefined);
    var $set = function(value){
      if(this === ObjectProto)$set.call(OPSymbols, value);
      if(has(this, HIDDEN) && has(this[HIDDEN], tag))this[HIDDEN][tag] = false;
      setSymbolDesc(this, tag, createDesc(1, value));
    };
    if(DESCRIPTORS && setter)setSymbolDesc(ObjectProto, tag, {configurable: true, set: $set});
    return wrap(tag);
  };
  redefine($Symbol[PROTOTYPE], 'toString', function toString(){
    return this._k;
  });

  $GOPD.f = $getOwnPropertyDescriptor;
  $DP.f   = $defineProperty;
  __webpack_require__(88).f = gOPNExt.f = $getOwnPropertyNames;
  __webpack_require__(54).f  = $propertyIsEnumerable;
  __webpack_require__(59).f = $getOwnPropertySymbols;

  if(DESCRIPTORS && !__webpack_require__(63)){
    redefine(ObjectProto, 'propertyIsEnumerable', $propertyIsEnumerable, true);
  }

  wksExt.f = function(name){
    return wrap(wks(name));
  }
}

$export($export.G + $export.W + $export.F * !USE_NATIVE, {Symbol: $Symbol});

for(var symbols = (
  // 19.4.2.2, 19.4.2.3, 19.4.2.4, 19.4.2.6, 19.4.2.8, 19.4.2.9, 19.4.2.10, 19.4.2.11, 19.4.2.12, 19.4.2.13, 19.4.2.14
  'hasInstance,isConcatSpreadable,iterator,match,replace,search,species,split,toPrimitive,toStringTag,unscopables'
).split(','), i = 0; symbols.length > i; )wks(symbols[i++]);

for(var symbols = $keys(wks.store), i = 0; symbols.length > i; )wksDefine(symbols[i++]);

$export($export.S + $export.F * !USE_NATIVE, 'Symbol', {
  // 19.4.2.1 Symbol.for(key)
  'for': function(key){
    return has(SymbolRegistry, key += '')
      ? SymbolRegistry[key]
      : SymbolRegistry[key] = $Symbol(key);
  },
  // 19.4.2.5 Symbol.keyFor(sym)
  keyFor: function keyFor(key){
    if(isSymbol(key))return keyOf(SymbolRegistry, key);
    throw TypeError(key + ' is not a symbol!');
  },
  useSetter: function(){ setter = true; },
  useSimple: function(){ setter = false; }
});

$export($export.S + $export.F * !USE_NATIVE, 'Object', {
  // 19.1.2.2 Object.create(O [, Properties])
  create: $create,
  // 19.1.2.4 Object.defineProperty(O, P, Attributes)
  defineProperty: $defineProperty,
  // 19.1.2.3 Object.defineProperties(O, Properties)
  defineProperties: $defineProperties,
  // 19.1.2.6 Object.getOwnPropertyDescriptor(O, P)
  getOwnPropertyDescriptor: $getOwnPropertyDescriptor,
  // 19.1.2.7 Object.getOwnPropertyNames(O)
  getOwnPropertyNames: $getOwnPropertyNames,
  // 19.1.2.8 Object.getOwnPropertySymbols(O)
  getOwnPropertySymbols: $getOwnPropertySymbols
});

// 24.3.2 JSON.stringify(value [, replacer [, space]])
$JSON && $export($export.S + $export.F * (!USE_NATIVE || $fails(function(){
  var S = $Symbol();
  // MS Edge converts symbol values to JSON as {}
  // WebKit converts symbol values to JSON as null
  // V8 throws on boxed symbols
  return _stringify([S]) != '[null]' || _stringify({a: S}) != '{}' || _stringify(Object(S)) != '{}';
})), 'JSON', {
  stringify: function stringify(it){
    if(it === undefined || isSymbol(it))return; // IE8 returns string on undefined
    var args = [it]
      , i    = 1
      , replacer, $replacer;
    while(arguments.length > i)args.push(arguments[i++]);
    replacer = args[1];
    if(typeof replacer == 'function')$replacer = replacer;
    if($replacer || !isArray(replacer))replacer = function(key, value){
      if($replacer)value = $replacer.call(this, key, value);
      if(!isSymbol(value))return value;
    };
    args[1] = replacer;
    return _stringify.apply($JSON, args);
  }
});

// 19.4.3.4 Symbol.prototype[@@toPrimitive](hint)
$Symbol[PROTOTYPE][TO_PRIMITIVE] || __webpack_require__(10)($Symbol[PROTOTYPE], TO_PRIMITIVE, $Symbol[PROTOTYPE].valueOf);
// 19.4.3.5 Symbol.prototype[@@toStringTag]
setToStringTag($Symbol, 'Symbol');
// 20.2.1.9 Math[@@toStringTag]
setToStringTag(Math, 'Math', true);
// 24.3.3 JSON[@@toStringTag]
setToStringTag(global.JSON, 'JSON', true);

/***/ }),

/***/ 125:
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(71)('asyncIterator');

/***/ }),

/***/ 126:
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(71)('observable');

/***/ }),

/***/ 13:
/***/ (function(module, exports, __webpack_require__) {

// optional / simple context binding
var aFunction = __webpack_require__(18);
module.exports = function(fn, that, length){
  aFunction(fn);
  if(that === undefined)return fn;
  switch(length){
    case 1: return function(a){
      return fn.call(that, a);
    };
    case 2: return function(a, b){
      return fn.call(that, a, b);
    };
    case 3: return function(a, b, c){
      return fn.call(that, a, b, c);
    };
  }
  return function(/* ...args */){
    return fn.apply(that, arguments);
  };
};

/***/ }),

/***/ 14:
/***/ (function(module, exports, __webpack_require__) {

// 7.1.1 ToPrimitive(input [, PreferredType])
var isObject = __webpack_require__(4);
// instead of the ES6 spec version, we didn't implement @@toPrimitive case
// and the second argument - flag - preferred type is a string
module.exports = function(it, S){
  if(!isObject(it))return it;
  var fn, val;
  if(S && typeof (fn = it.toString) == 'function' && !isObject(val = fn.call(it)))return val;
  if(typeof (fn = it.valueOf) == 'function' && !isObject(val = fn.call(it)))return val;
  if(!S && typeof (fn = it.toString) == 'function' && !isObject(val = fn.call(it)))return val;
  throw TypeError("Can't convert object to primitive value");
};

/***/ }),

/***/ 16:
/***/ (function(module, exports, __webpack_require__) {

var isObject = __webpack_require__(4)
  , document = __webpack_require__(5).document
  // in old IE typeof document.createElement is 'object'
  , is = isObject(document) && isObject(document.createElement);
module.exports = function(it){
  return is ? document.createElement(it) : {};
};

/***/ }),

/***/ 17:
/***/ (function(module, exports, __webpack_require__) {

module.exports = !__webpack_require__(2) && !__webpack_require__(7)(function(){
  return Object.defineProperty(__webpack_require__(16)('div'), 'a', {get: function(){ return 7; }}).a != 7;
});

/***/ }),

/***/ 18:
/***/ (function(module, exports) {

module.exports = function(it){
  if(typeof it != 'function')throw TypeError(it + ' is not a function!');
  return it;
};

/***/ }),

/***/ 19:
/***/ (function(module, exports, __webpack_require__) {

module.exports = { "default": __webpack_require__(20), __esModule: true };

/***/ }),

/***/ 191:
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function(jQuery) {

Object.defineProperty(exports, "__esModule", {
  value: true
});

var _keys = __webpack_require__(70);

var _keys2 = _interopRequireDefault(_keys);

var _typeof2 = __webpack_require__(102);

var _typeof3 = _interopRequireDefault(_typeof2);

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

var _createClass2 = __webpack_require__(1);

var _createClass3 = _interopRequireDefault(_createClass2);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

var $ = window.$;

var BOEvent = {
  on: function on(eventName, callback, context) {

    document.addEventListener(eventName, function (event) {
      if (typeof context !== 'undefined') {
        callback.call(context, event);
      } else {
        callback(event);
      }
    });
  },

  emitEvent: function emitEvent(eventName, eventType) {
    var _event = document.createEvent(eventType);
    // true values stand for: can bubble, and is cancellable
    _event.initEvent(eventName, true, true);
    document.dispatchEvent(_event);
  }
};

/**
 * Class is responsible for handling Module Card behavior
 *
 * This is a port of admin-dev/themes/default/js/bundle/module/module_card.js
 */

var ModuleCard = function () {
  function ModuleCard() {
    (0, _classCallCheck3.default)(this, ModuleCard);

    /* Selectors for module action links (uninstall, reset, etc...) to add a confirm popin */
    this.moduleActionMenuLinkSelector = 'button.module_action_menu_';
    this.moduleActionMenuInstallLinkSelector = 'button.module_action_menu_install';
    this.moduleActionMenuEnableLinkSelector = 'button.module_action_menu_enable';
    this.moduleActionMenuUninstallLinkSelector = 'button.module_action_menu_uninstall';
    this.moduleActionMenuDisableLinkSelector = 'button.module_action_menu_disable';
    this.moduleActionMenuEnableMobileLinkSelector = 'button.module_action_menu_enable_mobile';
    this.moduleActionMenuDisableMobileLinkSelector = 'button.module_action_menu_disable_mobile';
    this.moduleActionMenuResetLinkSelector = 'button.module_action_menu_reset';
    this.moduleActionMenuUpdateLinkSelector = 'button.module_action_menu_upgrade';
    this.moduleItemListSelector = '.module-item-list';
    this.moduleItemGridSelector = '.module-item-grid';
    this.moduleItemActionsSelector = '.module-actions';

    /* Selectors only for modal buttons */
    this.moduleActionModalDisableLinkSelector = 'a.module_action_modal_disable';
    this.moduleActionModalResetLinkSelector = 'a.module_action_modal_reset';
    this.moduleActionModalUninstallLinkSelector = 'a.module_action_modal_uninstall';
    this.forceDeletionOption = '#force_deletion';

    this.initActionButtons();
  }

  (0, _createClass3.default)(ModuleCard, [{
    key: 'initActionButtons',
    value: function initActionButtons() {
      var self = this;

      $(document).on('click', this.forceDeletionOption, function () {
        var btn = $(self.moduleActionModalUninstallLinkSelector, $("div.module-item-list[data-tech-name='" + $(this).attr("data-tech-name") + "']"));
        if ($(this).prop('checked') === true) {
          btn.attr('data-deletion', 'true');
        } else {
          btn.removeAttr('data-deletion');
        }
      });

      $(document).on('click', this.moduleActionMenuInstallLinkSelector, function () {
        if ($("#modal-prestatrust").length) {
          $("#modal-prestatrust").modal('hide');
        }
        return self._dispatchPreEvent('install', this) && self._confirmAction('install', this) && self._requestToController('install', $(this));
      });
      $(document).on('click', this.moduleActionMenuEnableLinkSelector, function () {
        return self._dispatchPreEvent('enable', this) && self._confirmAction('enable', this) && self._requestToController('enable', $(this));
      });
      $(document).on('click', this.moduleActionMenuUninstallLinkSelector, function () {
        return self._dispatchPreEvent('uninstall', this) && self._confirmAction('uninstall', this) && self._requestToController('uninstall', $(this));
      });
      $(document).on('click', this.moduleActionMenuDisableLinkSelector, function () {
        return self._dispatchPreEvent('disable', this) && self._confirmAction('disable', this) && self._requestToController('disable', $(this));
      });
      $(document).on('click', this.moduleActionMenuEnableMobileLinkSelector, function () {
        return self._dispatchPreEvent('enable_mobile', this) && self._confirmAction('enable_mobile', this) && self._requestToController('enable_mobile', $(this));
      });
      $(document).on('click', this.moduleActionMenuDisableMobileLinkSelector, function () {
        return self._dispatchPreEvent('disable_mobile', this) && self._confirmAction('disable_mobile', this) && self._requestToController('disable_mobile', $(this));
      });
      $(document).on('click', this.moduleActionMenuResetLinkSelector, function () {
        return self._dispatchPreEvent('reset', this) && self._confirmAction('reset', this) && self._requestToController('reset', $(this));
      });
      $(document).on('click', this.moduleActionMenuUpdateLinkSelector, function () {
        return self._dispatchPreEvent('update', this) && self._confirmAction('update', this) && self._requestToController('update', $(this));
      });

      $(document).on('click', this.moduleActionModalDisableLinkSelector, function () {
        return self._requestToController('disable', $(self.moduleActionMenuDisableLinkSelector, $("div.module-item-list[data-tech-name='" + $(this).attr("data-tech-name") + "']")));
      });
      $(document).on('click', this.moduleActionModalResetLinkSelector, function () {
        return self._requestToController('reset', $(self.moduleActionMenuResetLinkSelector, $("div.module-item-list[data-tech-name='" + $(this).attr("data-tech-name") + "']")));
      });
      $(document).on('click', this.moduleActionModalUninstallLinkSelector, function (e) {
        $(e.target).parents('.modal').on('hidden.bs.modal', function (event) {
          return self._requestToController('uninstall', $(self.moduleActionMenuUninstallLinkSelector, $("div.module-item-list[data-tech-name='" + $(e.target).attr("data-tech-name") + "']")), $(e.target).attr("data-deletion"));
        }.bind(e));
      });
    }
  }, {
    key: '_getModuleItemSelector',
    value: function _getModuleItemSelector() {
      if ($(this.moduleItemListSelector).length) {
        return this.moduleItemListSelector;
      } else {
        return this.moduleItemGridSelector;
      }
    }
  }, {
    key: '_confirmAction',
    value: function _confirmAction(action, element) {
      var modal = $('#' + $(element).data('confirm_modal'));
      if (modal.length != 1) {
        return true;
      }
      modal.first().modal('show');

      return false; // do not allow a.href to reload the page. The confirm modal dialog will do it async if needed.
    }
  }, {
    key: '_confirmPrestaTrust',


    /**
     * Update the content of a modal asking a confirmation for PrestaTrust and open it
     *
     * @param {array} result containing module data
     * @return {void}
     */
    value: function _confirmPrestaTrust(result) {
      var that = this;
      var modal = this._replacePrestaTrustPlaceholders(result);

      modal.find(".pstrust-install").off('click').on('click', function () {
        // Find related form, update it and submit it
        var install_button = $(that.moduleActionMenuInstallLinkSelector, '.module-item[data-tech-name="' + result.module.attributes.name + '"]');
        var form = install_button.parent("form");
        $('<input>').attr({
          type: 'hidden',
          value: '1',
          name: 'actionParams[confirmPrestaTrust]'
        }).appendTo(form);

        install_button.click();
        modal.modal('hide');
      });

      modal.modal();
    }
  }, {
    key: '_replacePrestaTrustPlaceholders',
    value: function _replacePrestaTrustPlaceholders(result) {
      var modal = $("#modal-prestatrust");
      var module = result.module.attributes;

      if (result.confirmation_subject !== 'PrestaTrust' || !modal.length) {
        return;
      }

      var alertClass = module.prestatrust.status ? 'success' : 'warning';

      if (module.prestatrust.check_list.property) {
        modal.find("#pstrust-btn-property-ok").show();
        modal.find("#pstrust-btn-property-nok").hide();
      } else {
        modal.find("#pstrust-btn-property-ok").hide();
        modal.find("#pstrust-btn-property-nok").show();
        modal.find("#pstrust-buy").attr("href", module.url).toggle(module.url !== null);
      }

      modal.find("#pstrust-img").attr({ src: module.img, alt: module.name });
      modal.find("#pstrust-name").text(module.displayName);
      modal.find("#pstrust-author").text(module.author);
      modal.find("#pstrust-label").attr("class", "text-" + alertClass).text(module.prestatrust.status ? 'OK' : 'KO');
      modal.find("#pstrust-message").attr("class", "alert alert-" + alertClass);
      modal.find("#pstrust-message > p").text(module.prestatrust.message);

      return modal;
    }
  }, {
    key: '_dispatchPreEvent',
    value: function _dispatchPreEvent(action, element) {
      var event = jQuery.Event('module_card_action_event');

      $(element).trigger(event, [action]);
      if (event.isPropagationStopped() !== false || event.isImmediatePropagationStopped() !== false) {
        return false; // if all handlers have not been called, then stop propagation of the click event.
      }

      return event.result !== false; // explicit false must be set from handlers to stop propagation of the click event.
    }
  }, {
    key: '_requestToController',
    value: function _requestToController(action, element, forceDeletion, disableCacheClear, callback) {
      var self = this;
      var jqElementObj = element.closest(this.moduleItemActionsSelector);
      var form = element.closest("form");
      var spinnerObj = $("<button class=\"btn-primary-reverse onclick unbind spinner \"></button>");
      var url = "//" + window.location.host + form.attr("action");
      var actionParams = form.serializeArray();

      if (forceDeletion === "true" || forceDeletion === true) {
        actionParams.push({ name: "actionParams[deletion]", value: true });
      }
      if (disableCacheClear === "true" || disableCacheClear === true) {
        actionParams.push({ name: "actionParams[cacheClearEnabled]", value: 0 });
      }

      $.ajax({
        url: url,
        dataType: 'json',
        method: 'POST',
        data: actionParams,
        beforeSend: function beforeSend() {
          jqElementObj.hide();
          jqElementObj.after(spinnerObj);
        }
      }).done(function (result) {
        if ((typeof result === 'undefined' ? 'undefined' : (0, _typeof3.default)(result)) === undefined) {
          $.growl.error({ message: "No answer received from server" });
          return;
        }

        if (typeof result.status !== 'undefined' && result.status === false) {
          $.growl.error({ message: result.msg });
          return;
        }

        var moduleTechName = (0, _keys2.default)(result)[0];

        if (result[moduleTechName].status === false) {
          if (typeof result[moduleTechName].confirmation_subject !== 'undefined') {
            self._confirmPrestaTrust(result[moduleTechName]);
          }

          $.growl.error({ message: result[moduleTechName].msg });
          return;
        }

        $.growl.notice({ message: result[moduleTechName].msg });

        var alteredSelector = self._getModuleItemSelector().replace('.', '');
        var mainElement = null;

        if (action == "uninstall") {
          mainElement = jqElementObj.closest('.' + alteredSelector);
          mainElement.remove();

          BOEvent.emitEvent("Module Uninstalled", "CustomEvent");
        } else if (action == "disable") {
          mainElement = jqElementObj.closest('.' + alteredSelector);
          mainElement.addClass(alteredSelector + '-isNotActive');
          mainElement.attr('data-active', '0');

          BOEvent.emitEvent("Module Disabled", "CustomEvent");
        } else if (action == "enable") {
          mainElement = jqElementObj.closest('.' + alteredSelector);
          mainElement.removeClass(alteredSelector + '-isNotActive');
          mainElement.attr('data-active', '1');

          BOEvent.emitEvent("Module Enabled", "CustomEvent");
        }

        jqElementObj.replaceWith(result[moduleTechName].action_menu_html);
      }).fail(function () {
        var moduleItem = jqElementObj.closest('module-item-list');
        var techName = moduleItem.data('techName');
        $.growl.error({ message: "Could not perform action " + action + " for module " + techName });
      }).always(function () {
        jqElementObj.fadeIn();
        spinnerObj.remove();
        if (callback) {
          callback();
        }
      });

      return false;
    }
  }]);
  return ModuleCard;
}();

exports.default = ModuleCard;
/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(42)))

/***/ }),

/***/ 2:
/***/ (function(module, exports, __webpack_require__) {

// Thank's IE8 for his funny defineProperty
module.exports = !__webpack_require__(7)(function(){
  return Object.defineProperty({}, 'a', {get: function(){ return 7; }}).a != 7;
});

/***/ }),

/***/ 20:
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(21);
var $Object = __webpack_require__(3).Object;
module.exports = function defineProperty(it, key, desc){
  return $Object.defineProperty(it, key, desc);
};

/***/ }),

/***/ 21:
/***/ (function(module, exports, __webpack_require__) {

var $export = __webpack_require__(8);
// 19.1.2.4 / 15.2.3.6 Object.defineProperty(O, P, Attributes)
$export($export.S + $export.F * !__webpack_require__(2), 'Object', {defineProperty: __webpack_require__(6).f});

/***/ }),

/***/ 22:
/***/ (function(module, exports, __webpack_require__) {

// to indexed object, toObject with fallback for non-array-like ES3 strings
var IObject = __webpack_require__(52)
  , defined = __webpack_require__(35);
module.exports = function(it){
  return IObject(defined(it));
};

/***/ }),

/***/ 23:
/***/ (function(module, exports, __webpack_require__) {

var store      = __webpack_require__(50)('wks')
  , uid        = __webpack_require__(40)
  , Symbol     = __webpack_require__(5).Symbol
  , USE_SYMBOL = typeof Symbol == 'function';

var $exports = module.exports = function(name){
  return store[name] || (store[name] =
    USE_SYMBOL && Symbol[name] || (USE_SYMBOL ? Symbol : uid)('Symbol.' + name));
};

$exports.store = store;

/***/ }),

/***/ 25:
/***/ (function(module, exports) {

var hasOwnProperty = {}.hasOwnProperty;
module.exports = function(it, key){
  return hasOwnProperty.call(it, key);
};

/***/ }),

/***/ 3:
/***/ (function(module, exports) {

var core = module.exports = {version: '2.4.0'};
if(typeof __e == 'number')__e = core; // eslint-disable-line no-undef

/***/ }),

/***/ 33:
/***/ (function(module, exports, __webpack_require__) {

// 19.1.2.14 / 15.2.3.14 Object.keys(O)
var $keys       = __webpack_require__(55)
  , enumBugKeys = __webpack_require__(49);

module.exports = Object.keys || function keys(O){
  return $keys(O, enumBugKeys);
};

/***/ }),

/***/ 35:
/***/ (function(module, exports) {

// 7.2.1 RequireObjectCoercible(argument)
module.exports = function(it){
  if(it == undefined)throw TypeError("Can't call method on  " + it);
  return it;
};

/***/ }),

/***/ 36:
/***/ (function(module, exports) {

// 7.1.4 ToInteger
var ceil  = Math.ceil
  , floor = Math.floor;
module.exports = function(it){
  return isNaN(it = +it) ? 0 : (it > 0 ? floor : ceil)(it);
};

/***/ }),

/***/ 4:
/***/ (function(module, exports) {

module.exports = function(it){
  return typeof it === 'object' ? it !== null : typeof it === 'function';
};

/***/ }),

/***/ 40:
/***/ (function(module, exports) {

var id = 0
  , px = Math.random();
module.exports = function(key){
  return 'Symbol('.concat(key === undefined ? '' : key, ')_', (++id + px).toString(36));
};

/***/ }),

/***/ 416:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

var _createClass2 = __webpack_require__(1);

var _createClass3 = _interopRequireDefault(_createClass2);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

var $ = window.$;

/**
 * Module Admin Page Controller.
 * @constructor
 */

var AdminModuleController = function () {
  /**
   * Initialize all listeners and bind everything
   * @method init
   * @memberof AdminModule
   */
  function AdminModuleController(moduleCardController) {
    (0, _classCallCheck3.default)(this, AdminModuleController);

    this.moduleCardController = moduleCardController;

    this.DEFAULT_MAX_RECENTLY_USED = 10;
    this.DEFAULT_MAX_PER_CATEGORIES = 6;
    this.DISPLAY_GRID = 'grid';
    this.DISPLAY_LIST = 'list';
    this.CATEGORY_RECENTLY_USED = 'recently-used';

    this.currentCategoryDisplay = {};
    this.currentDisplay = '';
    this.isCategoryGridDisplayed = false;
    this.currentTagsList = [];
    this.currentRefCategory = null;
    this.currentRefStatus = null;
    this.currentSorting = null;
    this.baseAddonsUrl = 'https://addons.prestashop.com/';
    this.pstaggerInput = null;
    this.lastBulkAction = null;
    this.isUploadStarted = false;

    this.recentlyUsedSelector = '#module-recently-used-list .modules-list';

    /**
     * Loaded modules list.
     * Containing the card and list display.
     * @type {Array}
     */
    this.modulesList = [];
    this.addonsCardGrid = null;
    this.addonsCardList = null;

    this.moduleShortList = '.module-short-list';
    // See more & See less selector
    this.seeMoreSelector = '.see-more';
    this.seeLessSelector = '.see-less';

    // Selectors into vars to make it easier to change them while keeping same code logic
    this.moduleItemGridSelector = '.module-item-grid';
    this.moduleItemListSelector = '.module-item-list';
    this.categorySelectorLabelSelector = '.module-category-selector-label';
    this.categorySelector = '.module-category-selector';
    this.categoryItemSelector = '.module-category-menu';
    this.addonsLoginButtonSelector = '#addons_login_btn';
    this.categoryResetBtnSelector = '.module-category-reset';
    this.moduleInstallBtnSelector = 'input.module-install-btn';
    this.moduleSortingDropdownSelector = '.module-sorting-author select';
    this.categoryGridSelector = '#modules-categories-grid';
    this.categoryGridItemSelector = '.module-category-item';
    this.addonItemGridSelector = '.module-addons-item-grid';
    this.addonItemListSelector = '.module-addons-item-list';

    // Upgrade All selectors
    this.upgradeAllSource = '.module_action_menu_upgrade_all';
    this.upgradeAllTargets = '#modules-list-container-update .module_action_menu_upgrade:visible';

    // Bulk action selectors
    this.bulkActionDropDownSelector = '.module-bulk-actions';
    this.bulkItemSelector = '.module-bulk-menu';
    this.bulkActionCheckboxListSelector = '.module-checkbox-bulk-list input';
    this.bulkActionCheckboxGridSelector = '.module-checkbox-bulk-grid input';
    this.checkedBulkActionListSelector = this.bulkActionCheckboxListSelector + ':checked';
    this.checkedBulkActionGridSelector = this.bulkActionCheckboxGridSelector + ':checked';
    this.bulkActionCheckboxSelector = '#module-modal-bulk-checkbox';
    this.bulkConfirmModalSelector = '#module-modal-bulk-confirm';
    this.bulkConfirmModalActionNameSelector = '#module-modal-bulk-confirm-action-name';
    this.bulkConfirmModalListSelector = '#module-modal-bulk-confirm-list';
    this.bulkConfirmModalAckBtnSelector = '#module-modal-confirm-bulk-ack';

    // Placeholders
    this.placeholderGlobalSelector = '.module-placeholders-wrapper';
    this.placeholderFailureGlobalSelector = '.module-placeholders-failure';
    this.placeholderFailureMsgSelector = '.module-placeholders-failure-msg';
    this.placeholderFailureRetryBtnSelector = '#module-placeholders-failure-retry';

    // Module's statuses selectors
    this.statusSelectorLabelSelector = '.module-status-selector-label';
    this.statusItemSelector = '.module-status-menu';
    this.statusResetBtnSelector = '.module-status-reset';

    // Selectors for Module Import and Addons connect
    this.addonsConnectModalBtnSelector = '#page-header-desc-configuration-addons_connect';
    this.addonsLogoutModalBtnSelector = '#page-header-desc-configuration-addons_logout';
    this.addonsImportModalBtnSelector = '#page-header-desc-configuration-add_module';
    this.dropZoneModalSelector = '#module-modal-import';
    this.dropZoneModalFooterSelector = '#module-modal-import .modal-footer';
    this.dropZoneImportZoneSelector = '#importDropzone';
    this.addonsConnectModalSelector = '#module-modal-addons-connect';
    this.addonsLogoutModalSelector = '#module-modal-addons-logout';
    this.addonsConnectForm = '#addons-connect-form';
    this.moduleImportModalCloseBtn = '#module-modal-import-closing-cross';
    this.moduleImportStartSelector = '.module-import-start';
    this.moduleImportProcessingSelector = '.module-import-processing';
    this.moduleImportSuccessSelector = '.module-import-success';
    this.moduleImportSuccessConfigureBtnSelector = '.module-import-success-configure';
    this.moduleImportFailureSelector = '.module-import-failure';
    this.moduleImportFailureRetrySelector = '.module-import-failure-retry';
    this.moduleImportFailureDetailsBtnSelector = '.module-import-failure-details-action';
    this.moduleImportSelectFileManualSelector = '.module-import-start-select-manual';
    this.moduleImportFailureMsgDetailsSelector = '.module-import-failure-details';
    this.moduleImportConfirmSelector = '.module-import-confirm';

    this.initSortingDropdown();
    this.initBOEventRegistering();
    this.initCurrentDisplay();
    this.initSortingDisplaySwitch();
    this.initBulkDropdown();
    this.initSearchBlock();
    this.initCategorySelect();
    this.initCategoriesGrid();
    this.initActionButtons();
    this.initAddonsSearch();
    this.initAddonsConnect();
    this.initAddModuleAction();
    this.initDropzone();
    this.initPageChangeProtection();
    this.initPlaceholderMechanism();
    this.initFilterStatusDropdown();
    this.fetchModulesList();
    this.getNotificationsCount();
    this.initializeSeeMore();
  }

  (0, _createClass3.default)(AdminModuleController, [{
    key: 'initFilterStatusDropdown',
    value: function initFilterStatusDropdown() {
      var self = this;
      var body = $('body');
      body.on('click', self.statusItemSelector, function () {
        // Get data from li DOM input
        self.currentRefStatus = parseInt($(this).data('status-ref'), 10);
        // Change dropdown label to set it to the current status' displayname
        $(self.statusSelectorLabelSelector).text($(this).find('a:first').text());
        $(self.statusResetBtnSelector).show();
        self.updateModuleVisibility();
      });

      body.on('click', self.statusResetBtnSelector, function () {
        $(self.statusSelectorLabelSelector).text($(this).find('a').text());
        $(this).hide();
        self.currentRefStatus = null;
        self.updateModuleVisibility();
      });
    }
  }, {
    key: 'initBulkDropdown',
    value: function initBulkDropdown() {
      var self = this;
      var body = $('body');

      body.on('click', self.getBulkCheckboxesSelector(), function () {
        var selector = $(self.bulkActionDropDownSelector);
        if ($(self.getBulkCheckboxesCheckedSelector()).length > 0) {
          selector.closest('.module-top-menu-item').removeClass('disabled');
        } else {
          selector.closest('.module-top-menu-item').addClass('disabled');
        }
      });

      body.on('click', self.bulkItemSelector, function initializeBodyChange() {
        if ($(self.getBulkCheckboxesCheckedSelector()).length === 0) {
          $.growl.warning({ message: window.translate_javascripts['Bulk Action - One module minimum'] });
          return;
        }

        self.lastBulkAction = $(this).data('ref');
        var modulesListString = self.buildBulkActionModuleList();
        var actionString = $(this).find(':checked').text().toLowerCase();
        $(self.bulkConfirmModalListSelector).html(modulesListString);
        $(self.bulkConfirmModalActionNameSelector).text(actionString);

        if (self.lastBulkAction === 'bulk-uninstall') {
          $(self.bulkActionCheckboxSelector).show();
        } else {
          $(self.bulkActionCheckboxSelector).hide();
        }

        $(self.bulkConfirmModalSelector).modal('show');
      });

      body.on('click', this.bulkConfirmModalAckBtnSelector, function (event) {
        event.preventDefault();
        event.stopPropagation();
        $(self.bulkConfirmModalSelector).modal('hide');
        self.doBulkAction(self.lastBulkAction);
      });
    }
  }, {
    key: 'initBOEventRegistering',
    value: function initBOEventRegistering() {
      window.BOEvent.on('Module Disabled', this.onModuleDisabled, this);
      window.BOEvent.on('Module Uninstalled', this.updateTotalResults, this);
    }
  }, {
    key: 'onModuleDisabled',
    value: function onModuleDisabled() {
      var self = this;
      var moduleItemSelector = self.getModuleItemSelector();

      $('.modules-list').each(function scanModulesList() {
        self.updateTotalResults();
      });
    }
  }, {
    key: 'initPlaceholderMechanism',
    value: function initPlaceholderMechanism() {
      var self = this;
      if ($(self.placeholderGlobalSelector).length) {
        self.ajaxLoadPage();
      }

      // Retry loading mechanism
      $('body').on('click', self.placeholderFailureRetryBtnSelector, function () {
        $(self.placeholderFailureGlobalSelector).fadeOut();
        $(self.placeholderGlobalSelector).fadeIn();
        self.ajaxLoadPage();
      });
    }
  }, {
    key: 'ajaxLoadPage',
    value: function ajaxLoadPage() {
      var self = this;

      $.ajax({
        method: 'GET',
        url: window.moduleURLs.catalogRefresh
      }).done(function (response) {
        if (response.status === true) {
          if (typeof response.domElements === 'undefined') response.domElements = null;
          if (typeof response.msg === 'undefined') response.msg = null;

          var stylesheet = document.styleSheets[0];
          var stylesheetRule = '{display: none}';
          var moduleGlobalSelector = '.modules-list';
          var moduleSortingSelector = '.module-sorting-menu';
          var requiredSelectorCombination = moduleGlobalSelector + ',' + moduleSortingSelector;

          if (stylesheet.insertRule) {
            stylesheet.insertRule(requiredSelectorCombination + stylesheetRule, stylesheet.cssRules.length);
          } else if (stylesheet.addRule) {
            stylesheet.addRule(requiredSelectorCombination, stylesheetRule, -1);
          }

          $(self.placeholderGlobalSelector).fadeOut(800, function () {
            $.each(response.domElements, function (index, element) {
              $(element.selector).append(element.content);
            });
            $(moduleGlobalSelector).fadeIn(800).css('display', 'flex');
            $(moduleSortingSelector).fadeIn(800);
            $('[data-toggle="popover"]').popover();
            self.initCurrentDisplay();
            self.fetchModulesList();
          });
        } else {
          $(self.placeholderGlobalSelector).fadeOut(800, function () {
            $(self.placeholderFailureMsgSelector).text(response.msg);
            $(self.placeholderFailureGlobalSelector).fadeIn(800);
          });
        }
      }).fail(function (response) {
        $(self.placeholderGlobalSelector).fadeOut(800, function () {
          $(self.placeholderFailureMsgSelector).text(response.statusText);
          $(self.placeholderFailureGlobalSelector).fadeIn(800);
        });
      });
    }
  }, {
    key: 'fetchModulesList',
    value: function fetchModulesList() {
      var self = this;
      var container = void 0;
      var $this = void 0;

      self.modulesList = [];
      $('.modules-list').each(function prepareContainer() {
        container = $(this);
        container.find('.module-item').each(function prepareModules() {
          $this = $(this);
          self.modulesList.push({
            domObject: $this,
            id: $this.data('id'),
            name: $this.data('name').toLowerCase(),
            scoring: parseFloat($this.data('scoring')),
            logo: $this.data('logo'),
            author: $this.data('author').toLowerCase(),
            version: $this.data('version'),
            description: $this.data('description').toLowerCase(),
            techName: $this.data('tech-name').toLowerCase(),
            childCategories: $this.data('child-categories'),
            categories: String($this.data('categories')).toLowerCase(),
            type: $this.data('type'),
            price: parseFloat($this.data('price')),
            active: parseInt($this.data('active'), 10),
            access: $this.data('last-access'),
            display: $this.hasClass('module-item-list') ? self.DISPLAY_LIST : self.DISPLAY_GRID,
            container: container
          });

          $this.remove();
        });
      });

      self.addonsCardGrid = $(this.addonItemGridSelector);
      self.addonsCardList = $(this.addonItemListSelector);
      self.updateModuleVisibility();
      $('body').trigger('moduleCatalogLoaded');
    }

    /**
     * Prepare sorting
     *
     */

  }, {
    key: 'updateModuleSorting',
    value: function updateModuleSorting() {
      var self = this;

      if (!self.currentSorting) {
        return;
      }

      // Modules sorting
      var order = 'asc';
      var key = self.currentSorting;
      var splittedKey = key.split('-');
      if (splittedKey.length > 1) {
        key = splittedKey[0];
        if (splittedKey[1] === 'desc') {
          order = 'desc';
        }
      }

      var currentCompare = function currentCompare(a, b) {
        var aData = a[key];
        var bData = b[key];
        if (key === 'access') {
          aData = new Date(aData).getTime();
          bData = new Date(bData).getTime();
          aData = isNaN(aData) ? 0 : aData;
          bData = isNaN(bData) ? 0 : bData;
          if (aData === bData) {
            return b.name.localeCompare(a.name);
          }
        }

        if (aData < bData) return -1;
        if (aData > bData) return 1;

        return 0;
      };

      self.modulesList.sort(currentCompare);
      if (order === 'desc') {
        self.modulesList.reverse();
      }
    }
  }, {
    key: 'updateModuleContainerDisplay',
    value: function updateModuleContainerDisplay() {
      var self = this;

      $('.module-short-list').each(function setShortListVisibility() {
        var container = $(this);
        var nbModulesInContainer = container.find('.module-item').length;
        if (self.currentRefCategory && self.currentRefCategory !== String(container.find('.modules-list').data('name')) || self.currentRefStatus !== null && nbModulesInContainer === 0 || nbModulesInContainer === 0 && String(container.find('.modules-list').data('name')) === self.CATEGORY_RECENTLY_USED || self.currentTagsList.length > 0 && nbModulesInContainer === 0) {
          container.hide();
          return;
        }

        container.show();
        if (nbModulesInContainer >= self.DEFAULT_MAX_PER_CATEGORIES) {
          container.find(self.seeMoreSelector + ', ' + self.seeLessSelector).show();
        } else {
          container.find(self.seeMoreSelector + ', ' + self.seeLessSelector).hide();
        }
      });
    }
  }, {
    key: 'updateModuleVisibility',
    value: function updateModuleVisibility() {
      var self = this;

      self.updateModuleSorting();

      $(self.recentlyUsedSelector).find('.module-item').remove();
      $('.modules-list').find('.module-item').remove();

      // Modules visibility management
      var isVisible = void 0;
      var currentModule = void 0;
      var moduleCategory = void 0;
      var tagExists = void 0;
      var newValue = void 0;

      var modulesListLength = self.modulesList.length;
      var counter = {};

      for (var i = 0; i < modulesListLength; i += 1) {
        currentModule = self.modulesList[i];
        if (currentModule.display === self.currentDisplay) {
          isVisible = true;

          moduleCategory = self.currentRefCategory === self.CATEGORY_RECENTLY_USED ? self.CATEGORY_RECENTLY_USED : currentModule.categories;

          // Check for same category
          if (self.currentRefCategory !== null) {
            isVisible &= moduleCategory === self.currentRefCategory;
          }

          // Check for same status
          if (self.currentRefStatus !== null) {
            isVisible &= currentModule.active === self.currentRefStatus;
          }

          // Check for tag list
          if (self.currentTagsList.length) {
            tagExists = false;
            $.each(self.currentTagsList, function (index, value) {
              newValue = value.toLowerCase();
              tagExists |= currentModule.name.indexOf(newValue) !== -1 || currentModule.description.indexOf(newValue) !== -1 || currentModule.author.indexOf(newValue) !== -1 || currentModule.techName.indexOf(newValue) !== -1;
            });
            isVisible &= tagExists;
          }

          /**
           * If list display without search we must display only the first 5 modules
           */
          if (self.currentDisplay === self.DISPLAY_LIST && !self.currentTagsList.length) {
            if (self.currentCategoryDisplay[moduleCategory] === undefined) {
              self.currentCategoryDisplay[moduleCategory] = false;
            }

            if (!counter[moduleCategory]) {
              counter[moduleCategory] = 0;
            }

            if (moduleCategory === self.CATEGORY_RECENTLY_USED) {
              if (counter[moduleCategory] >= self.DEFAULT_MAX_RECENTLY_USED) {
                isVisible &= self.currentCategoryDisplay[moduleCategory];
              }
            } else if (counter[moduleCategory] >= self.DEFAULT_MAX_PER_CATEGORIES) {
              isVisible &= self.currentCategoryDisplay[moduleCategory];
            }

            counter[moduleCategory] += 1;
          }

          // If visible, display (Thx captain obvious)
          if (isVisible) {
            if (self.currentRefCategory === self.CATEGORY_RECENTLY_USED) {
              $(self.recentlyUsedSelector).append(currentModule.domObject);
            } else {
              currentModule.container.append(currentModule.domObject);
            }
          }
        }
      }

      self.updateModuleContainerDisplay();

      if (self.currentTagsList.length) {
        $('.modules-list').append(this.currentDisplay === self.DISPLAY_GRID ? this.addonsCardGrid : this.addonsCardList);
      }

      self.updateTotalResults();
    }
  }, {
    key: 'initPageChangeProtection',
    value: function initPageChangeProtection() {
      var self = this;

      $(window).on('beforeunload', function () {
        if (self.isUploadStarted === true) {
          return 'It seems some critical operation are running, are you sure you want to change page ? It might cause some unexepcted behaviors.';
        }
      });
    }
  }, {
    key: 'buildBulkActionModuleList',
    value: function buildBulkActionModuleList() {
      var checkBoxesSelector = this.getBulkCheckboxesCheckedSelector();
      var moduleItemSelector = this.getModuleItemSelector();
      var alreadyDoneFlag = 0;
      var htmlGenerated = '';
      var currentElement = void 0;

      $(checkBoxesSelector).each(function prepareCheckboxes() {
        if (alreadyDoneFlag === 10) {
          // Break each
          htmlGenerated += '- ...';
          return false;
        }

        currentElement = $(this).closest(moduleItemSelector);
        htmlGenerated += '- ' + currentElement.data('name') + '<br/>';
        alreadyDoneFlag += 1;

        return true;
      });

      return htmlGenerated;
    }
  }, {
    key: 'initAddonsConnect',
    value: function initAddonsConnect() {
      var self = this;

      // Make addons connect modal ready to be clicked
      if ($(self.addonsConnectModalBtnSelector).attr('href') === '#') {
        $(self.addonsConnectModalBtnSelector).attr('data-toggle', 'modal');
        $(self.addonsConnectModalBtnSelector).attr('data-target', self.addonsConnectModalSelector);
      }

      if ($(self.addonsLogoutModalBtnSelector).attr('href') === '#') {
        $(self.addonsLogoutModalBtnSelector).attr('data-toggle', 'modal');
        $(self.addonsLogoutModalBtnSelector).attr('data-target', self.addonsLogoutModalSelector);
      }

      $('body').on('submit', self.addonsConnectForm, function initializeBodySubmit(event) {
        event.preventDefault();
        event.stopPropagation();

        $.ajax({
          method: 'POST',
          url: $(this).attr('action'),
          dataType: 'json',
          data: $(this).serialize(),
          beforeSend: function beforeSend() {
            $(self.addonsLoginButtonSelector).show();
            $('button.btn[type="submit"]', self.addonsConnectForm).hide();
          }
        }).done(function (response) {
          if (response.success === 1) {
            location.reload();
          } else {
            $.growl.error({ message: response.message });
            $(self.addonsLoginButtonSelector).hide();
            $('button.btn[type="submit"]', self.addonsConnectForm).fadeIn();
          }
        });
      });
    }
  }, {
    key: 'initAddModuleAction',
    value: function initAddModuleAction() {
      var self = this;
      var addModuleButton = $(self.addonsImportModalBtnSelector);
      addModuleButton.attr('data-toggle', 'modal');
      addModuleButton.attr('data-target', self.dropZoneModalSelector);
    }
  }, {
    key: 'initDropzone',
    value: function initDropzone() {
      var self = this;
      var body = $('body');
      var dropzone = $('.dropzone');

      // Reset modal when click on Retry in case of failure
      body.on('click', this.moduleImportFailureRetrySelector, function () {
        $(self.moduleImportSuccessSelector + ',' + self.moduleImportFailureSelector + ',' + self.moduleImportProcessingSelector).fadeOut(function () {
          /**
           * Added timeout for a better render of animation
           * and avoid to have displayed at the same time
           */
          setTimeout(function () {
            $(self.moduleImportStartSelector).fadeIn(function () {
              $(self.moduleImportFailureMsgDetailsSelector).hide();
              $(self.moduleImportSuccessConfigureBtnSelector).hide();
              dropzone.removeAttr('style');
            });
          }, 550);
        });
      });

      // Reinit modal on exit, but check if not already processing something
      body.on('hidden.bs.modal', this.dropZoneModalSelector, function () {
        $(self.moduleImportSuccessSelector + ', ' + self.moduleImportFailureSelector).hide();
        $(self.moduleImportStartSelector).show();

        dropzone.removeAttr('style');
        $(self.moduleImportFailureMsgDetailsSelector).hide();
        $(self.moduleImportSuccessConfigureBtnSelector).hide();
        $(self.dropZoneModalFooterSelector).html('');
        $(self.moduleImportConfirmSelector).hide();
      });

      // Change the way Dropzone.js lib handle file input trigger
      body.on('click', '.dropzone:not(' + this.moduleImportSelectFileManualSelector + ', ' + this.moduleImportSuccessConfigureBtnSelector + ')', function (event, manualSelect) {
        // if click comes from .module-import-start-select-manual, stop everything
        if (typeof manualSelect === 'undefined') {
          event.stopPropagation();
          event.preventDefault();
        }
      });

      body.on('click', this.moduleImportSelectFileManualSelector, function (event) {
        event.stopPropagation();
        event.preventDefault();
        /**
         * Trigger click on hidden file input, and pass extra data
         * to .dropzone click handler fro it to notice it comes from here
         */
        $('.dz-hidden-input').trigger('click', ['manual_select']);
      });

      // Handle modal closure
      body.on('click', this.moduleImportModalCloseBtn, function () {
        if (self.isUploadStarted !== true) {
          $(self.dropZoneModalSelector).modal('hide');
        }
      });

      // Fix issue on click configure button
      body.on('click', this.moduleImportSuccessConfigureBtnSelector, function initializeBodyClickOnModuleImport(event) {
        event.stopPropagation();
        event.preventDefault();
        window.location = $(this).attr('href');
      });

      // Open failure message details box
      body.on('click', this.moduleImportFailureDetailsBtnSelector, function () {
        $(self.moduleImportFailureMsgDetailsSelector).slideDown();
      });

      // @see: dropzone.js
      var dropzoneOptions = {
        url: window.moduleURLs.moduleImport,
        acceptedFiles: '.zip, .tar',
        // The name that will be used to transfer the file
        paramName: 'file_uploaded',
        maxFilesize: 50, // can't be greater than 50Mb because it's an addons limitation
        uploadMultiple: false,
        addRemoveLinks: true,
        dictDefaultMessage: '',
        hiddenInputContainer: self.dropZoneImportZoneSelector,
        /**
         * Add unlimited timeout. Otherwise dropzone timeout is 30 seconds
         *  and if a module is long to install, it is not possible to install the module.
         */
        timeout: 0,
        addedfile: function addedfile() {
          self.animateStartUpload();
        },
        processing: function processing() {
          // Leave it empty since we don't require anything while processing upload
        },
        error: function error(file, message) {
          self.displayOnUploadError(message);
        },
        complete: function complete(file) {
          if (file.status !== 'error') {
            var responseObject = $.parseJSON(file.xhr.response);
            if (typeof responseObject.is_configurable === 'undefined') responseObject.is_configurable = null;
            if (typeof responseObject.module_name === 'undefined') responseObject.module_name = null;

            self.displayOnUploadDone(responseObject);
          }
          // State that we have finish the process to unlock some actions
          self.isUploadStarted = false;
        }
      };

      dropzone.dropzone($.extend(dropzoneOptions));
    }
  }, {
    key: 'animateStartUpload',
    value: function animateStartUpload() {
      var self = this;
      var dropzone = $('.dropzone');
      // State that we start module upload
      self.isUploadStarted = true;
      $(self.moduleImportStartSelector).hide(0);
      dropzone.css('border', 'none');
      $(self.moduleImportProcessingSelector).fadeIn();
    }
  }, {
    key: 'animateEndUpload',
    value: function animateEndUpload(callback) {
      var self = this;
      $(self.moduleImportProcessingSelector).finish().fadeOut(callback);
    }

    /**
     * Method to call for upload modal, when the ajax call went well.
     *
     * @param object result containing the server response
     */

  }, {
    key: 'displayOnUploadDone',
    value: function displayOnUploadDone(result) {
      var self = this;
      self.animateEndUpload(function () {
        if (result.status === true) {
          if (result.is_configurable === true) {
            var configureLink = window.moduleURLs.configurationPage.replace(/:number:/, result.module_name);
            $(self.moduleImportSuccessConfigureBtnSelector).attr('href', configureLink);
            $(self.moduleImportSuccessConfigureBtnSelector).show();
          }
          $(self.moduleImportSuccessSelector).fadeIn();
        } else if (typeof result.confirmation_subject !== 'undefined') {
          self.displayPrestaTrustStep(result);
        } else {
          $(self.moduleImportFailureMsgDetailsSelector).html(result.msg);
          $(self.moduleImportFailureSelector).fadeIn();
        }
      });
    }

    /**
     * Method to call for upload modal, when the ajax call went wrong or when the action requested could not
     * succeed for some reason.
     *
     * @param string message explaining the error.
     */

  }, {
    key: 'displayOnUploadError',
    value: function displayOnUploadError(message) {
      var self = this;
      self.animateEndUpload(function () {
        $(self.moduleImportFailureMsgDetailsSelector).html(message);
        $(self.moduleImportFailureSelector).fadeIn();
      });
    }

    /**
     * If PrestaTrust needs to be confirmed, we ask for the confirmation
     * modal content and we display it in the currently displayed one.
     * We also generate the ajax call to trigger once we confirm we want to install
     * the module.
     *
     * @param Previous server response result
     */

  }, {
    key: 'displayPrestaTrustStep',
    value: function displayPrestaTrustStep(result) {
      var self = this;
      var modal = self.moduleCardController._replacePrestaTrustPlaceholders(result);
      var moduleName = result.module.attributes.name;

      $(this.moduleImportConfirmSelector).html(modal.find('.modal-body').html()).fadeIn();
      $(this.dropZoneModalFooterSelector).html(modal.find('.modal-footer').html()).fadeIn();

      $(this.dropZoneModalFooterSelector).find('.pstrust-install').off('click').on('click', function () {
        $(self.moduleImportConfirmSelector).hide();
        $(self.dropZoneModalFooterSelector).html('');
        self.animateStartUpload();

        // Install ajax call
        $.post(result.module.attributes.urls.install, { 'actionParams[confirmPrestaTrust]': '1' }).done(function (data) {
          self.displayOnUploadDone(data[moduleName]);
        }).fail(function (data) {
          self.displayOnUploadError(data[moduleName]);
        }).always(function () {
          self.isUploadStarted = false;
        });
      });
    }
  }, {
    key: 'getBulkCheckboxesSelector',
    value: function getBulkCheckboxesSelector() {
      return this.currentDisplay === this.DISPLAY_GRID ? this.bulkActionCheckboxGridSelector : this.bulkActionCheckboxListSelector;
    }
  }, {
    key: 'getBulkCheckboxesCheckedSelector',
    value: function getBulkCheckboxesCheckedSelector() {
      return this.currentDisplay === this.DISPLAY_GRID ? this.checkedBulkActionGridSelector : this.checkedBulkActionListSelector;
    }
  }, {
    key: 'getModuleItemSelector',
    value: function getModuleItemSelector() {
      return this.currentDisplay === this.DISPLAY_GRID ? this.moduleItemGridSelector : this.moduleItemListSelector;
    }

    /**
     * Get the module notifications count and displays it as a badge on the notification tab
     * @return void
     */

  }, {
    key: 'getNotificationsCount',
    value: function getNotificationsCount() {
      var self = this;
      $.getJSON(window.moduleURLs.notificationsCount, self.updateNotificationsCount).fail(function () {
        console.error('Could not retrieve module notifications count.');
      });
    }
  }, {
    key: 'updateNotificationsCount',
    value: function updateNotificationsCount(badge) {
      var destinationTabs = {
        to_configure: $('#subtab-AdminModulesNotifications'),
        to_update: $('#subtab-AdminModulesUpdates')
      };

      for (var key in destinationTabs) {
        if (destinationTabs[key].length === 0) {
          continue;
        }

        destinationTabs[key].find('.notification-counter').text(badge[key]);
      }
    }
  }, {
    key: 'initAddonsSearch',
    value: function initAddonsSearch() {
      var self = this;
      $('body').on('click', self.addonItemGridSelector + ', ' + self.addonItemListSelector, function () {
        var searchQuery = '';
        if (self.currentTagsList.length) {
          searchQuery = encodeURIComponent(self.currentTagsList.join(' '));
        }

        window.open(self.baseAddonsUrl + 'search.php?search_query=' + searchQuery, '_blank');
      });
    }
  }, {
    key: 'initCategoriesGrid',
    value: function initCategoriesGrid() {
      var self = this;

      $('body').on('click', this.categoryGridItemSelector, function initilaizeGridBodyClick(event) {
        event.stopPropagation();
        event.preventDefault();
        var refCategory = $(this).data('category-ref');

        // In case we have some tags we need to reset it !
        if (self.currentTagsList.length) {
          self.pstaggerInput.resetTags(false);
          self.currentTagsList = [];
        }
        var menuCategoryToTrigger = $(self.categoryItemSelector + '[data-category-ref="' + refCategory + '"]');

        if (!menuCategoryToTrigger.length) {
          console.warn('No category with ref (' + refCategory + ') seems to exist!');
          return false;
        }

        // Hide current category grid
        if (self.isCategoryGridDisplayed === true) {
          $(self.categoryGridSelector).fadeOut();
          self.isCategoryGridDisplayed = false;
        }

        // Trigger click on right category
        $(self.categoryItemSelector + '[data-category-ref="' + refCategory + '"]').click();
        return true;
      });
    }
  }, {
    key: 'initCurrentDisplay',
    value: function initCurrentDisplay() {
      this.currentDisplay = this.currentDisplay === '' ? this.DISPLAY_LIST : this.DISPLAY_GRID;
    }
  }, {
    key: 'initSortingDropdown',
    value: function initSortingDropdown() {
      var self = this;

      self.currentSorting = $(this.moduleSortingDropdownSelector).find(':checked').attr('value');
      if (!self.currentSorting) {
        self.currentSorting = 'access-desc';
      }

      $('body').on('change', self.moduleSortingDropdownSelector, function initializeBodySortingChange() {
        self.currentSorting = $(this).find(':checked').attr('value');
        self.updateModuleVisibility();
      });
    }
  }, {
    key: 'doBulkAction',
    value: function doBulkAction(requestedBulkAction) {
      // This object is used to check if requested bulkAction is available and give proper
      // url segment to be called for it
      var forceDeletion = $('#force_bulk_deletion').prop('checked');

      var bulkActionToUrl = {
        'bulk-uninstall': 'uninstall',
        'bulk-disable': 'disable',
        'bulk-enable': 'enable',
        'bulk-disable-mobile': 'disable_mobile',
        'bulk-enable-mobile': 'enable_mobile',
        'bulk-reset': 'reset'
      };

      // Note no grid selector used yet since we do not needed it at dev time
      // Maybe useful to implement this kind of things later if intended to
      // use this functionality elsewhere but "manage my module" section
      if (typeof bulkActionToUrl[requestedBulkAction] === 'undefined') {
        $.growl.error({ message: window.translate_javascripts['Bulk Action - Request not found'].replace('[1]', requestedBulkAction) });
        return false;
      }

      // Loop over all checked bulk checkboxes
      var bulkActionSelectedSelector = this.getBulkCheckboxesCheckedSelector();
      var bulkModuleAction = bulkActionToUrl[requestedBulkAction];

      if ($(bulkActionSelectedSelector).length <= 0) {
        console.warn(window.translate_javascripts['Bulk Action - One module minimum']);
        return false;
      }

      var modulesActions = [];
      var moduleTechName = void 0;
      $(bulkActionSelectedSelector).each(function bulkActionSelector() {
        moduleTechName = $(this).data('tech-name');
        modulesActions.push({
          techName: moduleTechName,
          actionMenuObj: $(this).closest('.module-checkbox-bulk-list').next()
        });
      });

      this.performModulesAction(modulesActions, bulkModuleAction, forceDeletion);

      return true;
    }
  }, {
    key: 'performModulesAction',
    value: function performModulesAction(modulesActions, bulkModuleAction, forceDeletion) {
      var self = this;
      if (typeof self.moduleCardController === 'undefined') {
        return;
      }

      //First let's filter modules that can't perform this action
      var actionMenuLinks = filterAllowedActions(modulesActions);
      if (!actionMenuLinks.length) {
        return;
      }

      var modulesRequestedCountdown = actionMenuLinks.length - 1;
      var spinnerObj = $("<button class=\"btn-primary-reverse onclick unbind spinner \"></button>");
      if (actionMenuLinks.length > 1) {
        //Loop through all the modules except the last one which waits for other
        //requests and then call its request with cache clear enabled
        $.each(actionMenuLinks, function bulkModulesLoop(index, actionMenuLink) {
          if (index >= actionMenuLinks.length - 1) {
            return;
          }
          requestModuleAction(actionMenuLink, true, countdownModulesRequest);
        });
        //Display a spinner for the last module
        var lastMenuLink = actionMenuLinks[actionMenuLinks.length - 1];
        var actionMenuObj = lastMenuLink.closest(self.moduleCardController.moduleItemActionsSelector);
        actionMenuObj.hide();
        actionMenuObj.after(spinnerObj);
      } else {
        requestModuleAction(actionMenuLinks[0]);
      }

      function requestModuleAction(actionMenuLink, disableCacheClear, requestEndCallback) {
        self.moduleCardController._requestToController(bulkModuleAction, actionMenuLink, forceDeletion, disableCacheClear, requestEndCallback);
      }

      function countdownModulesRequest() {
        modulesRequestedCountdown--;
        //Now that all other modules have performed their action WITHOUT cache clear, we
        //can request the last module request WITH cache clear
        if (modulesRequestedCountdown <= 0) {
          if (spinnerObj) {
            spinnerObj.remove();
            spinnerObj = null;
          }

          var _lastMenuLink = actionMenuLinks[actionMenuLinks.length - 1];
          var _actionMenuObj = _lastMenuLink.closest(self.moduleCardController.moduleItemActionsSelector);
          _actionMenuObj.fadeIn();
          requestModuleAction(_lastMenuLink);
        }
      }

      function filterAllowedActions(modulesActions) {
        var actionMenuLinks = [];
        var actionMenuLink = void 0;
        $.each(modulesActions, function filterAllowedModules(index, moduleData) {
          actionMenuLink = $(self.moduleCardController.moduleActionMenuLinkSelector + bulkModuleAction, moduleData.actionMenuObj);
          if (actionMenuLink.length > 0) {
            actionMenuLinks.push(actionMenuLink);
          } else {
            $.growl.error({ message: window.translate_javascripts['Bulk Action - Request not available for module'].replace('[1]', bulkModuleAction).replace('[2]', moduleData.techName) });
          }
        });

        return actionMenuLinks;
      }
    }
  }, {
    key: 'initActionButtons',
    value: function initActionButtons() {
      var _this = this;

      var self = this;
      $('body').on('click', self.moduleInstallBtnSelector, function initializeActionButtonsClick(event) {
        var $this = $(this);
        var $next = $($this.next());
        event.preventDefault();

        $this.hide();
        $next.show();

        $.ajax({
          url: $this.data('url'),
          dataType: 'json'
        }).done(function () {
          $next.fadeOut();
        });
      });

      // "Upgrade All" button handler
      $('body').on('click', self.upgradeAllSource, function (event) {
        event.preventDefault();

        if ($(self.upgradeAllTargets).length <= 0) {
          console.warn(window.translate_javascripts['Upgrade All Action - One module minimum']);
          return false;
        }

        var modulesActions = [];
        var moduleTechName = void 0;
        $(self.upgradeAllTargets).each(function bulkActionSelector() {
          var moduleItemList = $(this).closest('.module-item-list');
          moduleTechName = moduleItemList.data('tech-name');
          modulesActions.push({
            techName: moduleTechName,
            actionMenuObj: $('.module-actions', moduleItemList)
          });
        });

        _this.performModulesAction(modulesActions, 'upgrade');

        return true;
      });
    }
  }, {
    key: 'initCategorySelect',
    value: function initCategorySelect() {
      var self = this;
      var body = $('body');
      body.on('click', self.categoryItemSelector, function initializeCategorySelectClick() {
        // Get data from li DOM input
        self.currentRefCategory = $(this).data('category-ref');
        self.currentRefCategory = self.currentRefCategory ? String(self.currentRefCategory).toLowerCase() : null;
        // Change dropdown label to set it to the current category's displayname
        $(self.categorySelectorLabelSelector).text($(this).data('category-display-name'));
        $(self.categoryResetBtnSelector).show();
        self.updateModuleVisibility();
      });

      body.on('click', self.categoryResetBtnSelector, function initializeCategoryResetButtonClick() {
        var rawText = $(self.categorySelector).attr('aria-labelledby');
        var upperFirstLetter = rawText.charAt(0).toUpperCase();
        var removedFirstLetter = rawText.slice(1);
        var originalText = upperFirstLetter + removedFirstLetter;

        $(self.categorySelectorLabelSelector).text(originalText);
        $(this).hide();
        self.currentRefCategory = null;
        self.updateModuleVisibility();
      });
    }
  }, {
    key: 'initSearchBlock',
    value: function initSearchBlock() {
      var _this2 = this;

      var self = this;
      self.pstaggerInput = $('#module-search-bar').pstagger({
        onTagsChanged: function onTagsChanged(tagList) {
          self.currentTagsList = tagList;
          self.updateModuleVisibility();
        },
        onResetTags: function onResetTags() {
          self.currentTagsList = [];
          self.updateModuleVisibility();
        },
        inputPlaceholder: window.translate_javascripts['Search - placeholder'],
        closingCross: true,
        context: self
      });

      $('body').on('click', '.module-addons-search-link', function (event) {
        event.preventDefault();
        event.stopPropagation();
        window.open($(_this2).attr('href'), '_blank');
      });
    }

    /**
     * Initialize display switching between List or Grid
     */

  }, {
    key: 'initSortingDisplaySwitch',
    value: function initSortingDisplaySwitch() {
      var self = this;

      $('body').on('click', '.module-sort-switch', function switchSort() {
        var switchTo = $(this).data('switch');
        var isAlreadyDisplayed = $(this).hasClass('active-display');
        if (typeof switchTo !== 'undefined' && isAlreadyDisplayed === false) {
          self.switchSortingDisplayTo(switchTo);
          self.currentDisplay = switchTo;
        }
      });
    }
  }, {
    key: 'switchSortingDisplayTo',
    value: function switchSortingDisplayTo(switchTo) {
      if (switchTo !== this.DISPLAY_GRID && switchTo !== this.DISPLAY_LIST) {
        console.error('Can\'t switch to undefined display property "' + switchTo + '"');
        return;
      }

      $('.module-sort-switch').removeClass('module-sort-active');
      $('#module-sort-' + switchTo).addClass('module-sort-active');
      this.currentDisplay = switchTo;
      this.updateModuleVisibility();
    }
  }, {
    key: 'initializeSeeMore',
    value: function initializeSeeMore() {
      var self = this;

      $(self.moduleShortList + ' ' + self.seeMoreSelector).on('click', function seeMore() {
        self.currentCategoryDisplay[$(this).data('category')] = true;
        $(this).addClass('d-none');
        $(this).closest(self.moduleShortList).find(self.seeLessSelector).removeClass('d-none');
        self.updateModuleVisibility();
      });

      $(self.moduleShortList + ' ' + self.seeLessSelector).on('click', function seeMore() {
        self.currentCategoryDisplay[$(this).data('category')] = false;
        $(this).addClass('d-none');
        $(this).closest(self.moduleShortList).find(self.seeMoreSelector).removeClass('d-none');
        self.updateModuleVisibility();
      });
    }
  }, {
    key: 'updateTotalResults',
    value: function updateTotalResults() {
      var replaceFirstWordBy = function replaceFirstWordBy(element, value) {
        var explodedText = element.text().split(' ');
        explodedText[0] = value;
        element.text(explodedText.join(' '));
      };

      // If there are some shortlist: each shortlist count the modules on the next container.
      var $shortLists = $('.module-short-list');
      if ($shortLists.length > 0) {
        $shortLists.each(function shortLists() {
          var $this = $(this);
          replaceFirstWordBy($this.find('.module-search-result-wording'), $this.next('.modules-list').find('.module-item').length);
        });

        // If there is no shortlist: the wording directly update from the only module container.
      } else {
        var modulesCount = $('.modules-list').find('.module-item').length;
        replaceFirstWordBy($('.module-search-result-wording'), modulesCount);

        var selectorToToggle = self.currentDisplay === self.DISPLAY_LIST ? this.addonItemListSelector : this.addonItemGridSelector;
        $(selectorToToggle).toggle(modulesCount !== this.modulesList.length / 2);

        if (modulesCount === 0) {
          $('.module-addons-search-link').attr('href', this.baseAddonsUrl + 'search.php?search_query=' + encodeURIComponent(this.currentTagsList.join(' ')));
        }
      }
    }
  }]);
  return AdminModuleController;
}();

exports.default = AdminModuleController;

/***/ }),

/***/ 417:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _classCallCheck2 = __webpack_require__(0);

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

var _createClass2 = __webpack_require__(1);

var _createClass3 = _interopRequireDefault(_createClass2);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

var $ = window.$;

/**
 * Module Admin Page Loader.
 * @constructor
 */

var ModuleLoader = function () {
  function ModuleLoader() {
    (0, _classCallCheck3.default)(this, ModuleLoader);

    ModuleLoader.handleImport();
    ModuleLoader.handleEvents();
  }

  (0, _createClass3.default)(ModuleLoader, null, [{
    key: 'handleImport',
    value: function handleImport() {
      var moduleImport = $('#module-import');
      moduleImport.click(function () {
        moduleImport.addClass('onclick', 250, validate);
      });

      function validate() {
        setTimeout(function () {
          moduleImport.removeClass('onclick');
          moduleImport.addClass('validate', 450, callback);
        }, 2250);
      }
      function callback() {
        setTimeout(function () {
          moduleImport.removeClass('validate');
        }, 1250);
      }
    }
  }, {
    key: 'handleEvents',
    value: function handleEvents() {
      $('body').on('click', 'a.module-read-more-grid-btn, a.module-read-more-list-btn', function (event) {
        event.preventDefault();
        var modulePoppin = $(event.target).data('target');

        $.get(event.target.href, function (data) {
          $(modulePoppin).html(data);
          $(modulePoppin).modal();
        });
      });
    }
  }]);
  return ModuleLoader;
}();

exports.default = ModuleLoader;

/***/ }),

/***/ 42:
/***/ (function(module, exports) {

(function() { module.exports = window["jQuery"]; }());

/***/ }),

/***/ 44:
/***/ (function(module, exports, __webpack_require__) {

// 7.1.13 ToObject(argument)
var defined = __webpack_require__(35);
module.exports = function(it){
  return Object(defined(it));
};

/***/ }),

/***/ 45:
/***/ (function(module, exports, __webpack_require__) {

var shared = __webpack_require__(50)('keys')
  , uid    = __webpack_require__(40);
module.exports = function(key){
  return shared[key] || (shared[key] = uid(key));
};

/***/ }),

/***/ 48:
/***/ (function(module, exports) {

var toString = {}.toString;

module.exports = function(it){
  return toString.call(it).slice(8, -1);
};

/***/ }),

/***/ 49:
/***/ (function(module, exports) {

// IE 8- don't enum bug keys
module.exports = (
  'constructor,hasOwnProperty,isPrototypeOf,propertyIsEnumerable,toLocaleString,toString,valueOf'
).split(',');

/***/ }),

/***/ 5:
/***/ (function(module, exports) {

// https://github.com/zloirock/core-js/issues/86#issuecomment-115759028
var global = module.exports = typeof window != 'undefined' && window.Math == Math
  ? window : typeof self != 'undefined' && self.Math == Math ? self : Function('return this')();
if(typeof __g == 'number')__g = global; // eslint-disable-line no-undef

/***/ }),

/***/ 50:
/***/ (function(module, exports, __webpack_require__) {

var global = __webpack_require__(5)
  , SHARED = '__core-js_shared__'
  , store  = global[SHARED] || (global[SHARED] = {});
module.exports = function(key){
  return store[key] || (store[key] = {});
};

/***/ }),

/***/ 51:
/***/ (function(module, exports) {

module.exports = {};

/***/ }),

/***/ 511:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _moduleCard = __webpack_require__(191);

var _moduleCard2 = _interopRequireDefault(_moduleCard);

var _controller = __webpack_require__(416);

var _controller2 = _interopRequireDefault(_controller);

var _loader = __webpack_require__(417);

var _loader2 = _interopRequireDefault(_loader);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

var $ = window.$; /**
                   * Copyright since 2007 PrestaShop SA and Contributors
                   * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
                   *
                   * NOTICE OF LICENSE
                   *
                   * This source file is subject to the Open Software License (OSL 3.0)
                   * that is bundled with this package in the file LICENSE.md.
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
                   * needs please refer to https://devdocs.prestashop.com/ for more information.
                   *
                   * @author    PrestaShop SA and Contributors <contact@prestashop.com>
                   * @copyright Since 2007 PrestaShop SA and Contributors
                   * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
                   */

$(function () {
  var moduleCardController = new _moduleCard2.default();
  new _loader2.default();
  new _controller2.default(moduleCardController);
});

/***/ }),

/***/ 52:
/***/ (function(module, exports, __webpack_require__) {

// fallback for non-array-like ES3 and non-enumerable old V8 strings
var cof = __webpack_require__(48);
module.exports = Object('z').propertyIsEnumerable(0) ? Object : function(it){
  return cof(it) == 'String' ? it.split('') : Object(it);
};

/***/ }),

/***/ 53:
/***/ (function(module, exports, __webpack_require__) {

// 7.1.15 ToLength
var toInteger = __webpack_require__(36)
  , min       = Math.min;
module.exports = function(it){
  return it > 0 ? min(toInteger(it), 0x1fffffffffffff) : 0; // pow(2, 53) - 1 == 9007199254740991
};

/***/ }),

/***/ 54:
/***/ (function(module, exports) {

exports.f = {}.propertyIsEnumerable;

/***/ }),

/***/ 55:
/***/ (function(module, exports, __webpack_require__) {

var has          = __webpack_require__(25)
  , toIObject    = __webpack_require__(22)
  , arrayIndexOf = __webpack_require__(57)(false)
  , IE_PROTO     = __webpack_require__(45)('IE_PROTO');

module.exports = function(object, names){
  var O      = toIObject(object)
    , i      = 0
    , result = []
    , key;
  for(key in O)if(key != IE_PROTO)has(O, key) && result.push(key);
  // Don't enum bug & hidden keys
  while(names.length > i)if(has(O, key = names[i++])){
    ~arrayIndexOf(result, key) || result.push(key);
  }
  return result;
};

/***/ }),

/***/ 57:
/***/ (function(module, exports, __webpack_require__) {

// false -> Array#indexOf
// true  -> Array#includes
var toIObject = __webpack_require__(22)
  , toLength  = __webpack_require__(53)
  , toIndex   = __webpack_require__(58);
module.exports = function(IS_INCLUDES){
  return function($this, el, fromIndex){
    var O      = toIObject($this)
      , length = toLength(O.length)
      , index  = toIndex(fromIndex, length)
      , value;
    // Array#includes uses SameValueZero equality algorithm
    if(IS_INCLUDES && el != el)while(length > index){
      value = O[index++];
      if(value != value)return true;
    // Array#toIndex ignores holes, Array#includes - not
    } else for(;length > index; index++)if(IS_INCLUDES || index in O){
      if(O[index] === el)return IS_INCLUDES || index || 0;
    } return !IS_INCLUDES && -1;
  };
};

/***/ }),

/***/ 58:
/***/ (function(module, exports, __webpack_require__) {

var toInteger = __webpack_require__(36)
  , max       = Math.max
  , min       = Math.min;
module.exports = function(index, length){
  index = toInteger(index);
  return index < 0 ? max(index + length, 0) : min(index, length);
};

/***/ }),

/***/ 59:
/***/ (function(module, exports) {

exports.f = Object.getOwnPropertySymbols;

/***/ }),

/***/ 6:
/***/ (function(module, exports, __webpack_require__) {

var anObject       = __webpack_require__(11)
  , IE8_DOM_DEFINE = __webpack_require__(17)
  , toPrimitive    = __webpack_require__(14)
  , dP             = Object.defineProperty;

exports.f = __webpack_require__(2) ? Object.defineProperty : function defineProperty(O, P, Attributes){
  anObject(O);
  P = toPrimitive(P, true);
  anObject(Attributes);
  if(IE8_DOM_DEFINE)try {
    return dP(O, P, Attributes);
  } catch(e){ /* empty */ }
  if('get' in Attributes || 'set' in Attributes)throw TypeError('Accessors not supported!');
  if('value' in Attributes)O[P] = Attributes.value;
  return O;
};

/***/ }),

/***/ 60:
/***/ (function(module, exports, __webpack_require__) {

var def = __webpack_require__(6).f
  , has = __webpack_require__(25)
  , TAG = __webpack_require__(23)('toStringTag');

module.exports = function(it, tag, stat){
  if(it && !has(it = stat ? it : it.prototype, TAG))def(it, TAG, {configurable: true, value: tag});
};

/***/ }),

/***/ 63:
/***/ (function(module, exports) {

module.exports = true;

/***/ }),

/***/ 64:
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var $at  = __webpack_require__(99)(true);

// 21.1.3.27 String.prototype[@@iterator]()
__webpack_require__(67)(String, 'String', function(iterated){
  this._t = String(iterated); // target
  this._i = 0;                // next index
// 21.1.5.2.1 %StringIteratorPrototype%.next()
}, function(){
  var O     = this._t
    , index = this._i
    , point;
  if(index >= O.length)return {value: undefined, done: true};
  point = $at(O, index);
  this._i += point.length;
  return {value: point, done: false};
});

/***/ }),

/***/ 66:
/***/ (function(module, exports, __webpack_require__) {

// 19.1.2.2 / 15.2.3.5 Object.create(O [, Properties])
var anObject    = __webpack_require__(11)
  , dPs         = __webpack_require__(98)
  , enumBugKeys = __webpack_require__(49)
  , IE_PROTO    = __webpack_require__(45)('IE_PROTO')
  , Empty       = function(){ /* empty */ }
  , PROTOTYPE   = 'prototype';

// Create object with fake `null` prototype: use iframe Object with cleared prototype
var createDict = function(){
  // Thrash, waste and sodomy: IE GC bug
  var iframe = __webpack_require__(16)('iframe')
    , i      = enumBugKeys.length
    , lt     = '<'
    , gt     = '>'
    , iframeDocument;
  iframe.style.display = 'none';
  __webpack_require__(93).appendChild(iframe);
  iframe.src = 'javascript:'; // eslint-disable-line no-script-url
  // createDict = iframe.contentWindow.Object;
  // html.removeChild(iframe);
  iframeDocument = iframe.contentWindow.document;
  iframeDocument.open();
  iframeDocument.write(lt + 'script' + gt + 'document.F=Object' + lt + '/script' + gt);
  iframeDocument.close();
  createDict = iframeDocument.F;
  while(i--)delete createDict[PROTOTYPE][enumBugKeys[i]];
  return createDict();
};

module.exports = Object.create || function create(O, Properties){
  var result;
  if(O !== null){
    Empty[PROTOTYPE] = anObject(O);
    result = new Empty;
    Empty[PROTOTYPE] = null;
    // add "__proto__" for Object.getPrototypeOf polyfill
    result[IE_PROTO] = O;
  } else result = createDict();
  return Properties === undefined ? result : dPs(result, Properties);
};


/***/ }),

/***/ 67:
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var LIBRARY        = __webpack_require__(63)
  , $export        = __webpack_require__(8)
  , redefine       = __webpack_require__(76)
  , hide           = __webpack_require__(10)
  , has            = __webpack_require__(25)
  , Iterators      = __webpack_require__(51)
  , $iterCreate    = __webpack_require__(97)
  , setToStringTag = __webpack_require__(60)
  , getPrototypeOf = __webpack_require__(82)
  , ITERATOR       = __webpack_require__(23)('iterator')
  , BUGGY          = !([].keys && 'next' in [].keys()) // Safari has buggy iterators w/o `next`
  , FF_ITERATOR    = '@@iterator'
  , KEYS           = 'keys'
  , VALUES         = 'values';

var returnThis = function(){ return this; };

module.exports = function(Base, NAME, Constructor, next, DEFAULT, IS_SET, FORCED){
  $iterCreate(Constructor, NAME, next);
  var getMethod = function(kind){
    if(!BUGGY && kind in proto)return proto[kind];
    switch(kind){
      case KEYS: return function keys(){ return new Constructor(this, kind); };
      case VALUES: return function values(){ return new Constructor(this, kind); };
    } return function entries(){ return new Constructor(this, kind); };
  };
  var TAG        = NAME + ' Iterator'
    , DEF_VALUES = DEFAULT == VALUES
    , VALUES_BUG = false
    , proto      = Base.prototype
    , $native    = proto[ITERATOR] || proto[FF_ITERATOR] || DEFAULT && proto[DEFAULT]
    , $default   = $native || getMethod(DEFAULT)
    , $entries   = DEFAULT ? !DEF_VALUES ? $default : getMethod('entries') : undefined
    , $anyNative = NAME == 'Array' ? proto.entries || $native : $native
    , methods, key, IteratorPrototype;
  // Fix native
  if($anyNative){
    IteratorPrototype = getPrototypeOf($anyNative.call(new Base));
    if(IteratorPrototype !== Object.prototype){
      // Set @@toStringTag to native iterators
      setToStringTag(IteratorPrototype, TAG, true);
      // fix for some old engines
      if(!LIBRARY && !has(IteratorPrototype, ITERATOR))hide(IteratorPrototype, ITERATOR, returnThis);
    }
  }
  // fix Array#{values, @@iterator}.name in V8 / FF
  if(DEF_VALUES && $native && $native.name !== VALUES){
    VALUES_BUG = true;
    $default = function values(){ return $native.call(this); };
  }
  // Define iterator
  if((!LIBRARY || FORCED) && (BUGGY || VALUES_BUG || !proto[ITERATOR])){
    hide(proto, ITERATOR, $default);
  }
  // Plug for library
  Iterators[NAME] = $default;
  Iterators[TAG]  = returnThis;
  if(DEFAULT){
    methods = {
      values:  DEF_VALUES ? $default : getMethod(VALUES),
      keys:    IS_SET     ? $default : getMethod(KEYS),
      entries: $entries
    };
    if(FORCED)for(key in methods){
      if(!(key in proto))redefine(proto, key, methods[key]);
    } else $export($export.P + $export.F * (BUGGY || VALUES_BUG), NAME, methods);
  }
  return methods;
};

/***/ }),

/***/ 7:
/***/ (function(module, exports) {

module.exports = function(exec){
  try {
    return !!exec();
  } catch(e){
    return true;
  }
};

/***/ }),

/***/ 70:
/***/ (function(module, exports, __webpack_require__) {

module.exports = { "default": __webpack_require__(85), __esModule: true };

/***/ }),

/***/ 71:
/***/ (function(module, exports, __webpack_require__) {

var global         = __webpack_require__(5)
  , core           = __webpack_require__(3)
  , LIBRARY        = __webpack_require__(63)
  , wksExt         = __webpack_require__(72)
  , defineProperty = __webpack_require__(6).f;
module.exports = function(name){
  var $Symbol = core.Symbol || (core.Symbol = LIBRARY ? {} : global.Symbol || {});
  if(name.charAt(0) != '_' && !(name in $Symbol))defineProperty($Symbol, name, {value: wksExt.f(name)});
};

/***/ }),

/***/ 72:
/***/ (function(module, exports, __webpack_require__) {

exports.f = __webpack_require__(23);

/***/ }),

/***/ 73:
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(100);
var global        = __webpack_require__(5)
  , hide          = __webpack_require__(10)
  , Iterators     = __webpack_require__(51)
  , TO_STRING_TAG = __webpack_require__(23)('toStringTag');

for(var collections = ['NodeList', 'DOMTokenList', 'MediaList', 'StyleSheetList', 'CSSRuleList'], i = 0; i < 5; i++){
  var NAME       = collections[i]
    , Collection = global[NAME]
    , proto      = Collection && Collection.prototype;
  if(proto && !proto[TO_STRING_TAG])hide(proto, TO_STRING_TAG, NAME);
  Iterators[NAME] = Iterators.Array;
}

/***/ }),

/***/ 76:
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(10);

/***/ }),

/***/ 78:
/***/ (function(module, exports, __webpack_require__) {

// most Object methods by ES6 should accept primitives
var $export = __webpack_require__(8)
  , core    = __webpack_require__(3)
  , fails   = __webpack_require__(7);
module.exports = function(KEY, exec){
  var fn  = (core.Object || {})[KEY] || Object[KEY]
    , exp = {};
  exp[KEY] = exec(fn);
  $export($export.S + $export.F * fails(function(){ fn(1); }), 'Object', exp);
};

/***/ }),

/***/ 8:
/***/ (function(module, exports, __webpack_require__) {

var global    = __webpack_require__(5)
  , core      = __webpack_require__(3)
  , ctx       = __webpack_require__(13)
  , hide      = __webpack_require__(10)
  , PROTOTYPE = 'prototype';

var $export = function(type, name, source){
  var IS_FORCED = type & $export.F
    , IS_GLOBAL = type & $export.G
    , IS_STATIC = type & $export.S
    , IS_PROTO  = type & $export.P
    , IS_BIND   = type & $export.B
    , IS_WRAP   = type & $export.W
    , exports   = IS_GLOBAL ? core : core[name] || (core[name] = {})
    , expProto  = exports[PROTOTYPE]
    , target    = IS_GLOBAL ? global : IS_STATIC ? global[name] : (global[name] || {})[PROTOTYPE]
    , key, own, out;
  if(IS_GLOBAL)source = name;
  for(key in source){
    // contains in native
    own = !IS_FORCED && target && target[key] !== undefined;
    if(own && key in exports)continue;
    // export native or passed
    out = own ? target[key] : source[key];
    // prevent global pollution for namespaces
    exports[key] = IS_GLOBAL && typeof target[key] != 'function' ? source[key]
    // bind timers to global for call from export context
    : IS_BIND && own ? ctx(out, global)
    // wrap global constructors for prevent change them in library
    : IS_WRAP && target[key] == out ? (function(C){
      var F = function(a, b, c){
        if(this instanceof C){
          switch(arguments.length){
            case 0: return new C;
            case 1: return new C(a);
            case 2: return new C(a, b);
          } return new C(a, b, c);
        } return C.apply(this, arguments);
      };
      F[PROTOTYPE] = C[PROTOTYPE];
      return F;
    // make static versions for prototype methods
    })(out) : IS_PROTO && typeof out == 'function' ? ctx(Function.call, out) : out;
    // export proto methods to core.%CONSTRUCTOR%.methods.%NAME%
    if(IS_PROTO){
      (exports.virtual || (exports.virtual = {}))[key] = out;
      // export proto methods to core.%CONSTRUCTOR%.prototype.%NAME%
      if(type & $export.R && expProto && !expProto[key])hide(expProto, key, out);
    }
  }
};
// type bitmap
$export.F = 1;   // forced
$export.G = 2;   // global
$export.S = 4;   // static
$export.P = 8;   // proto
$export.B = 16;  // bind
$export.W = 32;  // wrap
$export.U = 64;  // safe
$export.R = 128; // real proto method for `library` 
module.exports = $export;

/***/ }),

/***/ 82:
/***/ (function(module, exports, __webpack_require__) {

// 19.1.2.9 / 15.2.3.2 Object.getPrototypeOf(O)
var has         = __webpack_require__(25)
  , toObject    = __webpack_require__(44)
  , IE_PROTO    = __webpack_require__(45)('IE_PROTO')
  , ObjectProto = Object.prototype;

module.exports = Object.getPrototypeOf || function(O){
  O = toObject(O);
  if(has(O, IE_PROTO))return O[IE_PROTO];
  if(typeof O.constructor == 'function' && O instanceof O.constructor){
    return O.constructor.prototype;
  } return O instanceof Object ? ObjectProto : null;
};

/***/ }),

/***/ 85:
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(90);
module.exports = __webpack_require__(3).Object.keys;

/***/ }),

/***/ 88:
/***/ (function(module, exports, __webpack_require__) {

// 19.1.2.7 / 15.2.3.4 Object.getOwnPropertyNames(O)
var $keys      = __webpack_require__(55)
  , hiddenKeys = __webpack_require__(49).concat('length', 'prototype');

exports.f = Object.getOwnPropertyNames || function getOwnPropertyNames(O){
  return $keys(O, hiddenKeys);
};

/***/ }),

/***/ 90:
/***/ (function(module, exports, __webpack_require__) {

// 19.1.2.14 Object.keys(O)
var toObject = __webpack_require__(44)
  , $keys    = __webpack_require__(33);

__webpack_require__(78)('keys', function(){
  return function keys(it){
    return $keys(toObject(it));
  };
});

/***/ }),

/***/ 93:
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(5).document && document.documentElement;

/***/ }),

/***/ 94:
/***/ (function(module, exports) {

module.exports = function(done, value){
  return {value: value, done: !!done};
};

/***/ }),

/***/ 96:
/***/ (function(module, exports) {

module.exports = function(){ /* empty */ };

/***/ }),

/***/ 97:
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var create         = __webpack_require__(66)
  , descriptor     = __webpack_require__(12)
  , setToStringTag = __webpack_require__(60)
  , IteratorPrototype = {};

// 25.1.2.1.1 %IteratorPrototype%[@@iterator]()
__webpack_require__(10)(IteratorPrototype, __webpack_require__(23)('iterator'), function(){ return this; });

module.exports = function(Constructor, NAME, next){
  Constructor.prototype = create(IteratorPrototype, {next: descriptor(1, next)});
  setToStringTag(Constructor, NAME + ' Iterator');
};

/***/ }),

/***/ 98:
/***/ (function(module, exports, __webpack_require__) {

var dP       = __webpack_require__(6)
  , anObject = __webpack_require__(11)
  , getKeys  = __webpack_require__(33);

module.exports = __webpack_require__(2) ? Object.defineProperties : function defineProperties(O, Properties){
  anObject(O);
  var keys   = getKeys(Properties)
    , length = keys.length
    , i = 0
    , P;
  while(length > i)dP.f(O, P = keys[i++], Properties[P]);
  return O;
};

/***/ }),

/***/ 99:
/***/ (function(module, exports, __webpack_require__) {

var toInteger = __webpack_require__(36)
  , defined   = __webpack_require__(35);
// true  -> String#at
// false -> String#codePointAt
module.exports = function(TO_STRING){
  return function(that, pos){
    var s = String(defined(that))
      , i = toInteger(pos)
      , l = s.length
      , a, b;
    if(i < 0 || i >= l)return TO_STRING ? '' : undefined;
    a = s.charCodeAt(i);
    return a < 0xd800 || a > 0xdbff || i + 1 === l || (b = s.charCodeAt(i + 1)) < 0xdc00 || b > 0xdfff
      ? TO_STRING ? s.charAt(i) : a
      : TO_STRING ? s.slice(i, i + 2) : (a - 0xd800 << 10) + (b - 0xdc00) + 0x10000;
  };
};

/***/ })

/******/ });
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAgM2E2MTdjZWQyOWViY2NiNmExZDA/MTI1MCoqKioqKioiLCJ3ZWJwYWNrOi8vLy4vfi9iYWJlbC1ydW50aW1lL2hlbHBlcnMvY2xhc3NDYWxsQ2hlY2suanM/MjFhZioqKioqIiwid2VicGFjazovLy8uL34vYmFiZWwtcnVudGltZS9oZWxwZXJzL2NyZWF0ZUNsYXNzLmpzPzFkZmUqKioqKiIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19oaWRlLmpzP2E2ZGEqKioqKioqIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvZXM2LmFycmF5Lml0ZXJhdG9yLmpzPzFlMDkqKioqIiwid2VicGFjazovLy8uL34vYmFiZWwtcnVudGltZS9oZWxwZXJzL3R5cGVvZi5qcz9mNGJkKioqIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX21ldGEuanM/NTUzZCoqKioiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fb2JqZWN0LWdvcGQuanM/ZDdkOCoqKiIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19pcy1hcnJheS5qcz8xODQzKioqKiIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19hbi1vYmplY3QuanM/MGRhMyoqKioqKioiLCJ3ZWJwYWNrOi8vLy4vfi9iYWJlbC1ydW50aW1lL2NvcmUtanMvc3ltYm9sLmpzP2E3MGQqKioiLCJ3ZWJwYWNrOi8vLy4vfi9iYWJlbC1ydW50aW1lL2NvcmUtanMvc3ltYm9sL2l0ZXJhdG9yLmpzP2QxNmIqKioiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvZm4vc3ltYm9sL2luZGV4LmpzP2YwN2EqKioiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvZm4vc3ltYm9sL2l0ZXJhdG9yLmpzPzIzOGQqKioiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fZW51bS1rZXlzLmpzPzcyN2EqKioiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fcHJvcGVydHktZGVzYy5qcz8xZTg2KioqKioqKiIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19rZXlvZi5qcz8wZDNiKioqIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX29iamVjdC1nb3BuLWV4dC5qcz9kMjM4KioqIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvZXM2LnN5bWJvbC5qcz82NzBhKioqIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvZXM3LnN5bWJvbC5hc3luYy1pdGVyYXRvci5qcz9iOGM1KioqIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvZXM3LnN5bWJvbC5vYnNlcnZhYmxlLmpzP2RhYTQqKioiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fY3R4LmpzP2NlMDAqKioqKioqIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX3RvLXByaW1pdGl2ZS5qcz80OWE0KioqKioqKiIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19kb20tY3JlYXRlLmpzP2FiNDQqKioqKioqIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2llOC1kb20tZGVmaW5lLmpzP2JkMWYqKioqKioqIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2EtZnVuY3Rpb24uanM/ZDUzZSoqKioqKioiLCJ3ZWJwYWNrOi8vLy4vfi9iYWJlbC1ydW50aW1lL2NvcmUtanMvb2JqZWN0L2RlZmluZS1wcm9wZXJ0eS5qcz81ZjcwKioqKioqKiIsIndlYnBhY2s6Ly8vLi9qcy9jb21wb25lbnRzL21vZHVsZS1jYXJkLmpzIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2Rlc2NyaXB0b3JzLmpzPzcwNTEqKioqKioqIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L2ZuL29iamVjdC9kZWZpbmUtcHJvcGVydHkuanM/YjdkOCoqKioqKioiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9lczYub2JqZWN0LmRlZmluZS1wcm9wZXJ0eS5qcz9jODJjKioqKioqKiIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL190by1pb2JqZWN0LmpzPzY5NDYqKioqKiIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL193a3MuanM/MzAyNyoqKioiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9faGFzLmpzP2Q4NTAqKioqKiIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19jb3JlLmpzPzFiNjIqKioqKioqIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX29iamVjdC1rZXlzLmpzP2Y1YmMqKioqKiIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19kZWZpbmVkLmpzPzQ1ZDMqKioqKiIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL190by1pbnRlZ2VyLmpzP2Y2NWYqKioqKiIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19pcy1vYmplY3QuanM/MjRjOCoqKioqKioiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fdWlkLmpzP2U4Y2QqKioqKiIsIndlYnBhY2s6Ly8vLi9qcy9wYWdlcy9tb2R1bGUvY29udHJvbGxlci5qcyIsIndlYnBhY2s6Ly8vLi9qcy9wYWdlcy9tb2R1bGUvbG9hZGVyLmpzIiwid2VicGFjazovLy9leHRlcm5hbCBcImpRdWVyeVwiPzBjYjgqKioqKiIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL190by1vYmplY3QuanM/YjVjMCoqKioqIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX3NoYXJlZC1rZXkuanM/MmE2YyoqKioqIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2NvZi5qcz80OGVhKioqKioiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fZW51bS1idWcta2V5cy5qcz83NTk4KioqKioiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fZ2xvYmFsLmpzPzc3YWEqKioqKioqIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX3NoYXJlZC5qcz83YjZjKioqKioiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9faXRlcmF0b3JzLmpzP2FmZjcqKioqIiwid2VicGFjazovLy8uL2pzL3BhZ2VzL21vZHVsZS9pbmRleC5qcyIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19pb2JqZWN0LmpzPzVjZjkqKioqKiIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL190by1sZW5ndGguanM/NjJhNyoqKioqIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX29iamVjdC1waWUuanM/ZDBkMioqKioiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fb2JqZWN0LWtleXMtaW50ZXJuYWwuanM/ZmNlYSoqKioqIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2FycmF5LWluY2x1ZGVzLmpzPzYxOTkqKioqKiIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL190by1pbmRleC5qcz85ZmQ0KioqKioiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fb2JqZWN0LWdvcHMuanM/YTVmYioqKioiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fb2JqZWN0LWRwLmpzPzQxMTYqKioqKioqIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX3NldC10by1zdHJpbmctdGFnLmpzP2M5NDUqKioqIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2xpYnJhcnkuanM/MmM4MCoqKioiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9lczYuc3RyaW5nLml0ZXJhdG9yLmpzP2ZlMTgqKioqIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX29iamVjdC1jcmVhdGUuanM/ZDhjZioqKioiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9faXRlci1kZWZpbmUuanM/OWE5NCoqKioiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fZmFpbHMuanM/OTM1ZCoqKioqKioiLCJ3ZWJwYWNrOi8vLy4vfi9iYWJlbC1ydW50aW1lL2NvcmUtanMvb2JqZWN0L2tleXMuanM/ZmUwNioqIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX3drcy1kZWZpbmUuanM/YjZlMCoqKiIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL193a3MtZXh0LmpzPzZlZTIqKioiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy93ZWIuZG9tLml0ZXJhYmxlLmpzP2JmMGUqKioqIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX3JlZGVmaW5lLmpzPzE0NTUqKioqIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX29iamVjdC1zYXAuanM/YTAzZSoqIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2V4cG9ydC5qcz9lY2UyKioqKioqKiIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19vYmplY3QtZ3BvLmpzP2Q0N2QqKioqIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L2ZuL29iamVjdC9rZXlzLmpzP2NjM2YqKiIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19vYmplY3QtZ29wbi5qcz8xZTA3KioqIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvZXM2Lm9iamVjdC5rZXlzLmpzP2M5OGYqKiIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19odG1sLmpzP2U1YWYqKioqIiwid2VicGFjazovLy8uL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2l0ZXItc3RlcC5qcz9lMjA5KioqKiIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19hZGQtdG8tdW5zY29wYWJsZXMuanM/ZDVlOCoqKioiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9faXRlci1jcmVhdGUuanM/MDEyNyoqKioiLCJ3ZWJwYWNrOi8vLy4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fb2JqZWN0LWRwcy5qcz80N2ZkKioqKiIsIndlYnBhY2s6Ly8vLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19zdHJpbmctYXQuanM/NDEzYSoqKioiXSwibmFtZXMiOlsiJCIsIndpbmRvdyIsIkJPRXZlbnQiLCJvbiIsImV2ZW50TmFtZSIsImNhbGxiYWNrIiwiY29udGV4dCIsImRvY3VtZW50IiwiYWRkRXZlbnRMaXN0ZW5lciIsImV2ZW50IiwiY2FsbCIsImVtaXRFdmVudCIsImV2ZW50VHlwZSIsIl9ldmVudCIsImNyZWF0ZUV2ZW50IiwiaW5pdEV2ZW50IiwiZGlzcGF0Y2hFdmVudCIsIk1vZHVsZUNhcmQiLCJtb2R1bGVBY3Rpb25NZW51TGlua1NlbGVjdG9yIiwibW9kdWxlQWN0aW9uTWVudUluc3RhbGxMaW5rU2VsZWN0b3IiLCJtb2R1bGVBY3Rpb25NZW51RW5hYmxlTGlua1NlbGVjdG9yIiwibW9kdWxlQWN0aW9uTWVudVVuaW5zdGFsbExpbmtTZWxlY3RvciIsIm1vZHVsZUFjdGlvbk1lbnVEaXNhYmxlTGlua1NlbGVjdG9yIiwibW9kdWxlQWN0aW9uTWVudUVuYWJsZU1vYmlsZUxpbmtTZWxlY3RvciIsIm1vZHVsZUFjdGlvbk1lbnVEaXNhYmxlTW9iaWxlTGlua1NlbGVjdG9yIiwibW9kdWxlQWN0aW9uTWVudVJlc2V0TGlua1NlbGVjdG9yIiwibW9kdWxlQWN0aW9uTWVudVVwZGF0ZUxpbmtTZWxlY3RvciIsIm1vZHVsZUl0ZW1MaXN0U2VsZWN0b3IiLCJtb2R1bGVJdGVtR3JpZFNlbGVjdG9yIiwibW9kdWxlSXRlbUFjdGlvbnNTZWxlY3RvciIsIm1vZHVsZUFjdGlvbk1vZGFsRGlzYWJsZUxpbmtTZWxlY3RvciIsIm1vZHVsZUFjdGlvbk1vZGFsUmVzZXRMaW5rU2VsZWN0b3IiLCJtb2R1bGVBY3Rpb25Nb2RhbFVuaW5zdGFsbExpbmtTZWxlY3RvciIsImZvcmNlRGVsZXRpb25PcHRpb24iLCJpbml0QWN0aW9uQnV0dG9ucyIsInNlbGYiLCJidG4iLCJhdHRyIiwicHJvcCIsInJlbW92ZUF0dHIiLCJsZW5ndGgiLCJtb2RhbCIsIl9kaXNwYXRjaFByZUV2ZW50IiwiX2NvbmZpcm1BY3Rpb24iLCJfcmVxdWVzdFRvQ29udHJvbGxlciIsImUiLCJ0YXJnZXQiLCJwYXJlbnRzIiwiYmluZCIsImFjdGlvbiIsImVsZW1lbnQiLCJkYXRhIiwiZmlyc3QiLCJyZXN1bHQiLCJ0aGF0IiwiX3JlcGxhY2VQcmVzdGFUcnVzdFBsYWNlaG9sZGVycyIsImZpbmQiLCJvZmYiLCJpbnN0YWxsX2J1dHRvbiIsIm1vZHVsZSIsImF0dHJpYnV0ZXMiLCJuYW1lIiwiZm9ybSIsInBhcmVudCIsInR5cGUiLCJ2YWx1ZSIsImFwcGVuZFRvIiwiY2xpY2siLCJjb25maXJtYXRpb25fc3ViamVjdCIsImFsZXJ0Q2xhc3MiLCJwcmVzdGF0cnVzdCIsInN0YXR1cyIsImNoZWNrX2xpc3QiLCJwcm9wZXJ0eSIsInNob3ciLCJoaWRlIiwidXJsIiwidG9nZ2xlIiwic3JjIiwiaW1nIiwiYWx0IiwidGV4dCIsImRpc3BsYXlOYW1lIiwiYXV0aG9yIiwibWVzc2FnZSIsImpRdWVyeSIsIkV2ZW50IiwidHJpZ2dlciIsImlzUHJvcGFnYXRpb25TdG9wcGVkIiwiaXNJbW1lZGlhdGVQcm9wYWdhdGlvblN0b3BwZWQiLCJmb3JjZURlbGV0aW9uIiwiZGlzYWJsZUNhY2hlQ2xlYXIiLCJqcUVsZW1lbnRPYmoiLCJjbG9zZXN0Iiwic3Bpbm5lck9iaiIsImxvY2F0aW9uIiwiaG9zdCIsImFjdGlvblBhcmFtcyIsInNlcmlhbGl6ZUFycmF5IiwicHVzaCIsImFqYXgiLCJkYXRhVHlwZSIsIm1ldGhvZCIsImJlZm9yZVNlbmQiLCJhZnRlciIsImRvbmUiLCJ1bmRlZmluZWQiLCJncm93bCIsImVycm9yIiwibXNnIiwibW9kdWxlVGVjaE5hbWUiLCJfY29uZmlybVByZXN0YVRydXN0Iiwibm90aWNlIiwiYWx0ZXJlZFNlbGVjdG9yIiwiX2dldE1vZHVsZUl0ZW1TZWxlY3RvciIsInJlcGxhY2UiLCJtYWluRWxlbWVudCIsInJlbW92ZSIsImFkZENsYXNzIiwicmVtb3ZlQ2xhc3MiLCJyZXBsYWNlV2l0aCIsImFjdGlvbl9tZW51X2h0bWwiLCJmYWlsIiwibW9kdWxlSXRlbSIsInRlY2hOYW1lIiwiYWx3YXlzIiwiZmFkZUluIiwiQWRtaW5Nb2R1bGVDb250cm9sbGVyIiwibW9kdWxlQ2FyZENvbnRyb2xsZXIiLCJERUZBVUxUX01BWF9SRUNFTlRMWV9VU0VEIiwiREVGQVVMVF9NQVhfUEVSX0NBVEVHT1JJRVMiLCJESVNQTEFZX0dSSUQiLCJESVNQTEFZX0xJU1QiLCJDQVRFR09SWV9SRUNFTlRMWV9VU0VEIiwiY3VycmVudENhdGVnb3J5RGlzcGxheSIsImN1cnJlbnREaXNwbGF5IiwiaXNDYXRlZ29yeUdyaWREaXNwbGF5ZWQiLCJjdXJyZW50VGFnc0xpc3QiLCJjdXJyZW50UmVmQ2F0ZWdvcnkiLCJjdXJyZW50UmVmU3RhdHVzIiwiY3VycmVudFNvcnRpbmciLCJiYXNlQWRkb25zVXJsIiwicHN0YWdnZXJJbnB1dCIsImxhc3RCdWxrQWN0aW9uIiwiaXNVcGxvYWRTdGFydGVkIiwicmVjZW50bHlVc2VkU2VsZWN0b3IiLCJtb2R1bGVzTGlzdCIsImFkZG9uc0NhcmRHcmlkIiwiYWRkb25zQ2FyZExpc3QiLCJtb2R1bGVTaG9ydExpc3QiLCJzZWVNb3JlU2VsZWN0b3IiLCJzZWVMZXNzU2VsZWN0b3IiLCJjYXRlZ29yeVNlbGVjdG9yTGFiZWxTZWxlY3RvciIsImNhdGVnb3J5U2VsZWN0b3IiLCJjYXRlZ29yeUl0ZW1TZWxlY3RvciIsImFkZG9uc0xvZ2luQnV0dG9uU2VsZWN0b3IiLCJjYXRlZ29yeVJlc2V0QnRuU2VsZWN0b3IiLCJtb2R1bGVJbnN0YWxsQnRuU2VsZWN0b3IiLCJtb2R1bGVTb3J0aW5nRHJvcGRvd25TZWxlY3RvciIsImNhdGVnb3J5R3JpZFNlbGVjdG9yIiwiY2F0ZWdvcnlHcmlkSXRlbVNlbGVjdG9yIiwiYWRkb25JdGVtR3JpZFNlbGVjdG9yIiwiYWRkb25JdGVtTGlzdFNlbGVjdG9yIiwidXBncmFkZUFsbFNvdXJjZSIsInVwZ3JhZGVBbGxUYXJnZXRzIiwiYnVsa0FjdGlvbkRyb3BEb3duU2VsZWN0b3IiLCJidWxrSXRlbVNlbGVjdG9yIiwiYnVsa0FjdGlvbkNoZWNrYm94TGlzdFNlbGVjdG9yIiwiYnVsa0FjdGlvbkNoZWNrYm94R3JpZFNlbGVjdG9yIiwiY2hlY2tlZEJ1bGtBY3Rpb25MaXN0U2VsZWN0b3IiLCJjaGVja2VkQnVsa0FjdGlvbkdyaWRTZWxlY3RvciIsImJ1bGtBY3Rpb25DaGVja2JveFNlbGVjdG9yIiwiYnVsa0NvbmZpcm1Nb2RhbFNlbGVjdG9yIiwiYnVsa0NvbmZpcm1Nb2RhbEFjdGlvbk5hbWVTZWxlY3RvciIsImJ1bGtDb25maXJtTW9kYWxMaXN0U2VsZWN0b3IiLCJidWxrQ29uZmlybU1vZGFsQWNrQnRuU2VsZWN0b3IiLCJwbGFjZWhvbGRlckdsb2JhbFNlbGVjdG9yIiwicGxhY2Vob2xkZXJGYWlsdXJlR2xvYmFsU2VsZWN0b3IiLCJwbGFjZWhvbGRlckZhaWx1cmVNc2dTZWxlY3RvciIsInBsYWNlaG9sZGVyRmFpbHVyZVJldHJ5QnRuU2VsZWN0b3IiLCJzdGF0dXNTZWxlY3RvckxhYmVsU2VsZWN0b3IiLCJzdGF0dXNJdGVtU2VsZWN0b3IiLCJzdGF0dXNSZXNldEJ0blNlbGVjdG9yIiwiYWRkb25zQ29ubmVjdE1vZGFsQnRuU2VsZWN0b3IiLCJhZGRvbnNMb2dvdXRNb2RhbEJ0blNlbGVjdG9yIiwiYWRkb25zSW1wb3J0TW9kYWxCdG5TZWxlY3RvciIsImRyb3Bab25lTW9kYWxTZWxlY3RvciIsImRyb3Bab25lTW9kYWxGb290ZXJTZWxlY3RvciIsImRyb3Bab25lSW1wb3J0Wm9uZVNlbGVjdG9yIiwiYWRkb25zQ29ubmVjdE1vZGFsU2VsZWN0b3IiLCJhZGRvbnNMb2dvdXRNb2RhbFNlbGVjdG9yIiwiYWRkb25zQ29ubmVjdEZvcm0iLCJtb2R1bGVJbXBvcnRNb2RhbENsb3NlQnRuIiwibW9kdWxlSW1wb3J0U3RhcnRTZWxlY3RvciIsIm1vZHVsZUltcG9ydFByb2Nlc3NpbmdTZWxlY3RvciIsIm1vZHVsZUltcG9ydFN1Y2Nlc3NTZWxlY3RvciIsIm1vZHVsZUltcG9ydFN1Y2Nlc3NDb25maWd1cmVCdG5TZWxlY3RvciIsIm1vZHVsZUltcG9ydEZhaWx1cmVTZWxlY3RvciIsIm1vZHVsZUltcG9ydEZhaWx1cmVSZXRyeVNlbGVjdG9yIiwibW9kdWxlSW1wb3J0RmFpbHVyZURldGFpbHNCdG5TZWxlY3RvciIsIm1vZHVsZUltcG9ydFNlbGVjdEZpbGVNYW51YWxTZWxlY3RvciIsIm1vZHVsZUltcG9ydEZhaWx1cmVNc2dEZXRhaWxzU2VsZWN0b3IiLCJtb2R1bGVJbXBvcnRDb25maXJtU2VsZWN0b3IiLCJpbml0U29ydGluZ0Ryb3Bkb3duIiwiaW5pdEJPRXZlbnRSZWdpc3RlcmluZyIsImluaXRDdXJyZW50RGlzcGxheSIsImluaXRTb3J0aW5nRGlzcGxheVN3aXRjaCIsImluaXRCdWxrRHJvcGRvd24iLCJpbml0U2VhcmNoQmxvY2siLCJpbml0Q2F0ZWdvcnlTZWxlY3QiLCJpbml0Q2F0ZWdvcmllc0dyaWQiLCJpbml0QWRkb25zU2VhcmNoIiwiaW5pdEFkZG9uc0Nvbm5lY3QiLCJpbml0QWRkTW9kdWxlQWN0aW9uIiwiaW5pdERyb3B6b25lIiwiaW5pdFBhZ2VDaGFuZ2VQcm90ZWN0aW9uIiwiaW5pdFBsYWNlaG9sZGVyTWVjaGFuaXNtIiwiaW5pdEZpbHRlclN0YXR1c0Ryb3Bkb3duIiwiZmV0Y2hNb2R1bGVzTGlzdCIsImdldE5vdGlmaWNhdGlvbnNDb3VudCIsImluaXRpYWxpemVTZWVNb3JlIiwiYm9keSIsInBhcnNlSW50IiwidXBkYXRlTW9kdWxlVmlzaWJpbGl0eSIsImdldEJ1bGtDaGVja2JveGVzU2VsZWN0b3IiLCJzZWxlY3RvciIsImdldEJ1bGtDaGVja2JveGVzQ2hlY2tlZFNlbGVjdG9yIiwiaW5pdGlhbGl6ZUJvZHlDaGFuZ2UiLCJ3YXJuaW5nIiwidHJhbnNsYXRlX2phdmFzY3JpcHRzIiwibW9kdWxlc0xpc3RTdHJpbmciLCJidWlsZEJ1bGtBY3Rpb25Nb2R1bGVMaXN0IiwiYWN0aW9uU3RyaW5nIiwidG9Mb3dlckNhc2UiLCJodG1sIiwicHJldmVudERlZmF1bHQiLCJzdG9wUHJvcGFnYXRpb24iLCJkb0J1bGtBY3Rpb24iLCJvbk1vZHVsZURpc2FibGVkIiwidXBkYXRlVG90YWxSZXN1bHRzIiwibW9kdWxlSXRlbVNlbGVjdG9yIiwiZ2V0TW9kdWxlSXRlbVNlbGVjdG9yIiwiZWFjaCIsInNjYW5Nb2R1bGVzTGlzdCIsImFqYXhMb2FkUGFnZSIsImZhZGVPdXQiLCJtb2R1bGVVUkxzIiwiY2F0YWxvZ1JlZnJlc2giLCJyZXNwb25zZSIsImRvbUVsZW1lbnRzIiwic3R5bGVzaGVldCIsInN0eWxlU2hlZXRzIiwic3R5bGVzaGVldFJ1bGUiLCJtb2R1bGVHbG9iYWxTZWxlY3RvciIsIm1vZHVsZVNvcnRpbmdTZWxlY3RvciIsInJlcXVpcmVkU2VsZWN0b3JDb21iaW5hdGlvbiIsImluc2VydFJ1bGUiLCJjc3NSdWxlcyIsImFkZFJ1bGUiLCJpbmRleCIsImFwcGVuZCIsImNvbnRlbnQiLCJjc3MiLCJwb3BvdmVyIiwic3RhdHVzVGV4dCIsImNvbnRhaW5lciIsIiR0aGlzIiwicHJlcGFyZUNvbnRhaW5lciIsInByZXBhcmVNb2R1bGVzIiwiZG9tT2JqZWN0IiwiaWQiLCJzY29yaW5nIiwicGFyc2VGbG9hdCIsImxvZ28iLCJ2ZXJzaW9uIiwiZGVzY3JpcHRpb24iLCJjaGlsZENhdGVnb3JpZXMiLCJjYXRlZ29yaWVzIiwiU3RyaW5nIiwicHJpY2UiLCJhY3RpdmUiLCJhY2Nlc3MiLCJkaXNwbGF5IiwiaGFzQ2xhc3MiLCJvcmRlciIsImtleSIsInNwbGl0dGVkS2V5Iiwic3BsaXQiLCJjdXJyZW50Q29tcGFyZSIsImEiLCJiIiwiYURhdGEiLCJiRGF0YSIsIkRhdGUiLCJnZXRUaW1lIiwiaXNOYU4iLCJsb2NhbGVDb21wYXJlIiwic29ydCIsInJldmVyc2UiLCJzZXRTaG9ydExpc3RWaXNpYmlsaXR5IiwibmJNb2R1bGVzSW5Db250YWluZXIiLCJ1cGRhdGVNb2R1bGVTb3J0aW5nIiwiaXNWaXNpYmxlIiwiY3VycmVudE1vZHVsZSIsIm1vZHVsZUNhdGVnb3J5IiwidGFnRXhpc3RzIiwibmV3VmFsdWUiLCJtb2R1bGVzTGlzdExlbmd0aCIsImNvdW50ZXIiLCJpIiwiaW5kZXhPZiIsInVwZGF0ZU1vZHVsZUNvbnRhaW5lckRpc3BsYXkiLCJjaGVja0JveGVzU2VsZWN0b3IiLCJhbHJlYWR5RG9uZUZsYWciLCJodG1sR2VuZXJhdGVkIiwiY3VycmVudEVsZW1lbnQiLCJwcmVwYXJlQ2hlY2tib3hlcyIsImluaXRpYWxpemVCb2R5U3VibWl0Iiwic2VyaWFsaXplIiwic3VjY2VzcyIsInJlbG9hZCIsImFkZE1vZHVsZUJ1dHRvbiIsImRyb3B6b25lIiwic2V0VGltZW91dCIsIm1hbnVhbFNlbGVjdCIsImluaXRpYWxpemVCb2R5Q2xpY2tPbk1vZHVsZUltcG9ydCIsInNsaWRlRG93biIsImRyb3B6b25lT3B0aW9ucyIsIm1vZHVsZUltcG9ydCIsImFjY2VwdGVkRmlsZXMiLCJwYXJhbU5hbWUiLCJtYXhGaWxlc2l6ZSIsInVwbG9hZE11bHRpcGxlIiwiYWRkUmVtb3ZlTGlua3MiLCJkaWN0RGVmYXVsdE1lc3NhZ2UiLCJoaWRkZW5JbnB1dENvbnRhaW5lciIsInRpbWVvdXQiLCJhZGRlZGZpbGUiLCJhbmltYXRlU3RhcnRVcGxvYWQiLCJwcm9jZXNzaW5nIiwiZmlsZSIsImRpc3BsYXlPblVwbG9hZEVycm9yIiwiY29tcGxldGUiLCJyZXNwb25zZU9iamVjdCIsInBhcnNlSlNPTiIsInhociIsImlzX2NvbmZpZ3VyYWJsZSIsIm1vZHVsZV9uYW1lIiwiZGlzcGxheU9uVXBsb2FkRG9uZSIsImV4dGVuZCIsImZpbmlzaCIsImFuaW1hdGVFbmRVcGxvYWQiLCJjb25maWd1cmVMaW5rIiwiY29uZmlndXJhdGlvblBhZ2UiLCJkaXNwbGF5UHJlc3RhVHJ1c3RTdGVwIiwibW9kdWxlTmFtZSIsInBvc3QiLCJ1cmxzIiwiaW5zdGFsbCIsImdldEpTT04iLCJub3RpZmljYXRpb25zQ291bnQiLCJ1cGRhdGVOb3RpZmljYXRpb25zQ291bnQiLCJjb25zb2xlIiwiYmFkZ2UiLCJkZXN0aW5hdGlvblRhYnMiLCJ0b19jb25maWd1cmUiLCJ0b191cGRhdGUiLCJzZWFyY2hRdWVyeSIsImVuY29kZVVSSUNvbXBvbmVudCIsImpvaW4iLCJvcGVuIiwiaW5pdGlsYWl6ZUdyaWRCb2R5Q2xpY2siLCJyZWZDYXRlZ29yeSIsInJlc2V0VGFncyIsIm1lbnVDYXRlZ29yeVRvVHJpZ2dlciIsIndhcm4iLCJpbml0aWFsaXplQm9keVNvcnRpbmdDaGFuZ2UiLCJyZXF1ZXN0ZWRCdWxrQWN0aW9uIiwiYnVsa0FjdGlvblRvVXJsIiwiYnVsa0FjdGlvblNlbGVjdGVkU2VsZWN0b3IiLCJidWxrTW9kdWxlQWN0aW9uIiwibW9kdWxlc0FjdGlvbnMiLCJidWxrQWN0aW9uU2VsZWN0b3IiLCJhY3Rpb25NZW51T2JqIiwibmV4dCIsInBlcmZvcm1Nb2R1bGVzQWN0aW9uIiwiYWN0aW9uTWVudUxpbmtzIiwiZmlsdGVyQWxsb3dlZEFjdGlvbnMiLCJtb2R1bGVzUmVxdWVzdGVkQ291bnRkb3duIiwiYnVsa01vZHVsZXNMb29wIiwiYWN0aW9uTWVudUxpbmsiLCJyZXF1ZXN0TW9kdWxlQWN0aW9uIiwiY291bnRkb3duTW9kdWxlc1JlcXVlc3QiLCJsYXN0TWVudUxpbmsiLCJyZXF1ZXN0RW5kQ2FsbGJhY2siLCJmaWx0ZXJBbGxvd2VkTW9kdWxlcyIsIm1vZHVsZURhdGEiLCJpbml0aWFsaXplQWN0aW9uQnV0dG9uc0NsaWNrIiwiJG5leHQiLCJtb2R1bGVJdGVtTGlzdCIsImluaXRpYWxpemVDYXRlZ29yeVNlbGVjdENsaWNrIiwiaW5pdGlhbGl6ZUNhdGVnb3J5UmVzZXRCdXR0b25DbGljayIsInJhd1RleHQiLCJ1cHBlckZpcnN0TGV0dGVyIiwiY2hhckF0IiwidG9VcHBlckNhc2UiLCJyZW1vdmVkRmlyc3RMZXR0ZXIiLCJzbGljZSIsIm9yaWdpbmFsVGV4dCIsInBzdGFnZ2VyIiwib25UYWdzQ2hhbmdlZCIsInRhZ0xpc3QiLCJvblJlc2V0VGFncyIsImlucHV0UGxhY2Vob2xkZXIiLCJjbG9zaW5nQ3Jvc3MiLCJzd2l0Y2hTb3J0Iiwic3dpdGNoVG8iLCJpc0FscmVhZHlEaXNwbGF5ZWQiLCJzd2l0Y2hTb3J0aW5nRGlzcGxheVRvIiwic2VlTW9yZSIsInJlcGxhY2VGaXJzdFdvcmRCeSIsImV4cGxvZGVkVGV4dCIsIiRzaG9ydExpc3RzIiwic2hvcnRMaXN0cyIsIm1vZHVsZXNDb3VudCIsInNlbGVjdG9yVG9Ub2dnbGUiLCJNb2R1bGVMb2FkZXIiLCJoYW5kbGVJbXBvcnQiLCJoYW5kbGVFdmVudHMiLCJ2YWxpZGF0ZSIsIm1vZHVsZVBvcHBpbiIsImdldCIsImhyZWYiXSwibWFwcGluZ3MiOiI7O0FBQUE7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7OztBQUdBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBLG1EQUEyQyxjQUFjOztBQUV6RDtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBLGFBQUs7QUFDTDtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBLG1DQUEyQiwwQkFBMEIsRUFBRTtBQUN2RCx5Q0FBaUMsZUFBZTtBQUNoRDtBQUNBO0FBQ0E7O0FBRUE7QUFDQSw4REFBc0QsK0RBQStEOztBQUVySDtBQUNBOztBQUVBO0FBQ0E7Ozs7Ozs7OztBQ2hFQTs7QUFFQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBLEU7Ozs7Ozs7O0FDUkE7O0FBRUE7O0FBRUE7O0FBRUE7O0FBRUEsc0NBQXNDLHVDQUF1QyxnQkFBZ0I7O0FBRTdGO0FBQ0E7QUFDQSxtQkFBbUIsa0JBQWtCO0FBQ3JDO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQSxDQUFDLEc7Ozs7Ozs7QUMxQkQ7QUFDQTtBQUNBO0FBQ0E7QUFDQSxDQUFDO0FBQ0Q7QUFDQTtBQUNBLEU7Ozs7Ozs7O0FDUEE7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsZ0NBQWdDO0FBQ2hDLGNBQWM7QUFDZCxpQkFBaUI7QUFDakI7QUFDQSxDQUFDO0FBQ0Q7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FB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QUFHO0FBQ0gsRTs7Ozs7OztBQ1pBO0FBQ0Esb0Q7Ozs7Ozs7QUNEQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBLEU7Ozs7Ozs7QUNOQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQSxDQUFDLEU7Ozs7Ozs7QUNSRCw2RTs7Ozs7OztBQ0FBO0FBQ0EsVUFBVTtBQUNWLEU7Ozs7Ozs7QUNGQSw0QkFBNEIsZTs7Ozs7Ozs7QUNBNUI7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBLDJGQUFnRixhQUFhLEVBQUU7O0FBRS9GO0FBQ0EscURBQXFELDBCQUEwQjtBQUMvRTtBQUNBLEU7Ozs7Ozs7QUNaQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBLEU7Ozs7Ozs7QUNaQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBLEUiLCJmaWxlIjoibW9kdWxlLmJ1bmRsZS5qcyIsInNvdXJjZXNDb250ZW50IjpbIiBcdC8vIFRoZSBtb2R1bGUgY2FjaGVcbiBcdHZhciBpbnN0YWxsZWRNb2R1bGVzID0ge307XG5cbiBcdC8vIFRoZSByZXF1aXJlIGZ1bmN0aW9uXG4gXHRmdW5jdGlvbiBfX3dlYnBhY2tfcmVxdWlyZV9fKG1vZHVsZUlkKSB7XG5cbiBcdFx0Ly8gQ2hlY2sgaWYgbW9kdWxlIGlzIGluIGNhY2hlXG4gXHRcdGlmKGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdKSB7XG4gXHRcdFx0cmV0dXJuIGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdLmV4cG9ydHM7XG4gXHRcdH1cbiBcdFx0Ly8gQ3JlYXRlIGEgbmV3IG1vZHVsZSAoYW5kIHB1dCBpdCBpbnRvIHRoZSBjYWNoZSlcbiBcdFx0dmFyIG1vZHVsZSA9IGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdID0ge1xuIFx0XHRcdGk6IG1vZHVsZUlkLFxuIFx0XHRcdGw6IGZhbHNlLFxuIFx0XHRcdGV4cG9ydHM6IHt9XG4gXHRcdH07XG5cbiBcdFx0Ly8gRXhlY3V0ZSB0aGUgbW9kdWxlIGZ1bmN0aW9uXG4gXHRcdG1vZHVsZXNbbW9kdWxlSWRdLmNhbGwobW9kdWxlLmV4cG9ydHMsIG1vZHVsZSwgbW9kdWxlLmV4cG9ydHMsIF9fd2VicGFja19yZXF1aXJlX18pO1xuXG4gXHRcdC8vIEZsYWcgdGhlIG1vZHVsZSBhcyBsb2FkZWRcbiBcdFx0bW9kdWxlLmwgPSB0cnVlO1xuXG4gXHRcdC8vIFJldHVybiB0aGUgZXhwb3J0cyBvZiB0aGUgbW9kdWxlXG4gXHRcdHJldHVybiBtb2R1bGUuZXhwb3J0cztcbiBcdH1cblxuXG4gXHQvLyBleHBvc2UgdGhlIG1vZHVsZXMgb2JqZWN0IChfX3dlYnBhY2tfbW9kdWxlc19fKVxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5tID0gbW9kdWxlcztcblxuIFx0Ly8gZXhwb3NlIHRoZSBtb2R1bGUgY2FjaGVcbiBcdF9fd2VicGFja19yZXF1aXJlX18uYyA9IGluc3RhbGxlZE1vZHVsZXM7XG5cbiBcdC8vIGlkZW50aXR5IGZ1bmN0aW9uIGZvciBjYWxsaW5nIGhhcm1vbnkgaW1wb3J0cyB3aXRoIHRoZSBjb3JyZWN0IGNvbnRleHRcbiBcdF9fd2VicGFja19yZXF1aXJlX18uaSA9IGZ1bmN0aW9uKHZhbHVlKSB7IHJldHVybiB2YWx1ZTsgfTtcblxuIFx0Ly8gZGVmaW5lIGdldHRlciBmdW5jdGlvbiBmb3IgaGFybW9ueSBleHBvcnRzXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmQgPSBmdW5jdGlvbihleHBvcnRzLCBuYW1lLCBnZXR0ZXIpIHtcbiBcdFx0aWYoIV9fd2VicGFja19yZXF1aXJlX18ubyhleHBvcnRzLCBuYW1lKSkge1xuIFx0XHRcdE9iamVjdC5kZWZpbmVQcm9wZXJ0eShleHBvcnRzLCBuYW1lLCB7XG4gXHRcdFx0XHRjb25maWd1cmFibGU6IGZhbHNlLFxuIFx0XHRcdFx0ZW51bWVyYWJsZTogdHJ1ZSxcbiBcdFx0XHRcdGdldDogZ2V0dGVyXG4gXHRcdFx0fSk7XG4gXHRcdH1cbiBcdH07XG5cbiBcdC8vIGdldERlZmF1bHRFeHBvcnQgZnVuY3Rpb24gZm9yIGNvbXBhdGliaWxpdHkgd2l0aCBub24taGFybW9ueSBtb2R1bGVzXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm4gPSBmdW5jdGlvbihtb2R1bGUpIHtcbiBcdFx0dmFyIGdldHRlciA9IG1vZHVsZSAmJiBtb2R1bGUuX19lc01vZHVsZSA/XG4gXHRcdFx0ZnVuY3Rpb24gZ2V0RGVmYXVsdCgpIHsgcmV0dXJuIG1vZHVsZVsnZGVmYXVsdCddOyB9IDpcbiBcdFx0XHRmdW5jdGlvbiBnZXRNb2R1bGVFeHBvcnRzKCkgeyByZXR1cm4gbW9kdWxlOyB9O1xuIFx0XHRfX3dlYnBhY2tfcmVxdWlyZV9fLmQoZ2V0dGVyLCAnYScsIGdldHRlcik7XG4gXHRcdHJldHVybiBnZXR0ZXI7XG4gXHR9O1xuXG4gXHQvLyBPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGxcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubyA9IGZ1bmN0aW9uKG9iamVjdCwgcHJvcGVydHkpIHsgcmV0dXJuIE9iamVjdC5wcm90b3R5cGUuaGFzT3duUHJvcGVydHkuY2FsbChvYmplY3QsIHByb3BlcnR5KTsgfTtcblxuIFx0Ly8gX193ZWJwYWNrX3B1YmxpY19wYXRoX19cbiBcdF9fd2VicGFja19yZXF1aXJlX18ucCA9IFwiXCI7XG5cbiBcdC8vIExvYWQgZW50cnkgbW9kdWxlIGFuZCByZXR1cm4gZXhwb3J0c1xuIFx0cmV0dXJuIF9fd2VicGFja19yZXF1aXJlX18oX193ZWJwYWNrX3JlcXVpcmVfXy5zID0gNTExKTtcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyB3ZWJwYWNrL2Jvb3RzdHJhcCAzYTYxN2NlZDI5ZWJjY2I2YTFkMCIsIlwidXNlIHN0cmljdFwiO1xuXG5leHBvcnRzLl9fZXNNb2R1bGUgPSB0cnVlO1xuXG5leHBvcnRzLmRlZmF1bHQgPSBmdW5jdGlvbiAoaW5zdGFuY2UsIENvbnN0cnVjdG9yKSB7XG4gIGlmICghKGluc3RhbmNlIGluc3RhbmNlb2YgQ29uc3RydWN0b3IpKSB7XG4gICAgdGhyb3cgbmV3IFR5cGVFcnJvcihcIkNhbm5vdCBjYWxsIGEgY2xhc3MgYXMgYSBmdW5jdGlvblwiKTtcbiAgfVxufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vYmFiZWwtcnVudGltZS9oZWxwZXJzL2NsYXNzQ2FsbENoZWNrLmpzXG4vLyBtb2R1bGUgaWQgPSAwXG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDMgNCA1IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE3IDE4IDE5IDIwIDIxIDIyIDIzIDI0IDI1IDI2IDI3IDI4IDI5IDMwIDMxIDMyIDMzIDM0IDM1IDM2IDM3IDM4IDM5IDQwIDQxIDQyIDQzIDQ0IDQ1IDQ2IDQ3IDQ4IDQ5IDUwIDUxIDUyIDUzIDU0IDU1IDU2IDU3IDU4IiwiXCJ1c2Ugc3RyaWN0XCI7XG5cbmV4cG9ydHMuX19lc01vZHVsZSA9IHRydWU7XG5cbnZhciBfZGVmaW5lUHJvcGVydHkgPSByZXF1aXJlKFwiLi4vY29yZS1qcy9vYmplY3QvZGVmaW5lLXByb3BlcnR5XCIpO1xuXG52YXIgX2RlZmluZVByb3BlcnR5MiA9IF9pbnRlcm9wUmVxdWlyZURlZmF1bHQoX2RlZmluZVByb3BlcnR5KTtcblxuZnVuY3Rpb24gX2ludGVyb3BSZXF1aXJlRGVmYXVsdChvYmopIHsgcmV0dXJuIG9iaiAmJiBvYmouX19lc01vZHVsZSA/IG9iaiA6IHsgZGVmYXVsdDogb2JqIH07IH1cblxuZXhwb3J0cy5kZWZhdWx0ID0gZnVuY3Rpb24gKCkge1xuICBmdW5jdGlvbiBkZWZpbmVQcm9wZXJ0aWVzKHRhcmdldCwgcHJvcHMpIHtcbiAgICBmb3IgKHZhciBpID0gMDsgaSA8IHByb3BzLmxlbmd0aDsgaSsrKSB7XG4gICAgICB2YXIgZGVzY3JpcHRvciA9IHByb3BzW2ldO1xuICAgICAgZGVzY3JpcHRvci5lbnVtZXJhYmxlID0gZGVzY3JpcHRvci5lbnVtZXJhYmxlIHx8IGZhbHNlO1xuICAgICAgZGVzY3JpcHRvci5jb25maWd1cmFibGUgPSB0cnVlO1xuICAgICAgaWYgKFwidmFsdWVcIiBpbiBkZXNjcmlwdG9yKSBkZXNjcmlwdG9yLndyaXRhYmxlID0gdHJ1ZTtcbiAgICAgICgwLCBfZGVmaW5lUHJvcGVydHkyLmRlZmF1bHQpKHRhcmdldCwgZGVzY3JpcHRvci5rZXksIGRlc2NyaXB0b3IpO1xuICAgIH1cbiAgfVxuXG4gIHJldHVybiBmdW5jdGlvbiAoQ29uc3RydWN0b3IsIHByb3RvUHJvcHMsIHN0YXRpY1Byb3BzKSB7XG4gICAgaWYgKHByb3RvUHJvcHMpIGRlZmluZVByb3BlcnRpZXMoQ29uc3RydWN0b3IucHJvdG90eXBlLCBwcm90b1Byb3BzKTtcbiAgICBpZiAoc3RhdGljUHJvcHMpIGRlZmluZVByb3BlcnRpZXMoQ29uc3RydWN0b3IsIHN0YXRpY1Byb3BzKTtcbiAgICByZXR1cm4gQ29uc3RydWN0b3I7XG4gIH07XG59KCk7XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2JhYmVsLXJ1bnRpbWUvaGVscGVycy9jcmVhdGVDbGFzcy5qc1xuLy8gbW9kdWxlIGlkID0gMVxuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAzIDQgNSA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxNyAxOCAxOSAyMCAyMSAyMiAyMyAyNCAyNSAyNiAyNyAyOCAyOSAzMCAzMSAzMiAzMyAzNCAzNSAzNiAzNyAzOCAzOSA0MCA0MSA0MiA0MyA0NCA0NSA0NiA0NyA0OCA0OSA1MCA1MSA1MiA1MyA1NCA1NSA1NiA1NyA1OCIsInZhciBkUCAgICAgICAgID0gcmVxdWlyZSgnLi9fb2JqZWN0LWRwJylcbiAgLCBjcmVhdGVEZXNjID0gcmVxdWlyZSgnLi9fcHJvcGVydHktZGVzYycpO1xubW9kdWxlLmV4cG9ydHMgPSByZXF1aXJlKCcuL19kZXNjcmlwdG9ycycpID8gZnVuY3Rpb24ob2JqZWN0LCBrZXksIHZhbHVlKXtcbiAgcmV0dXJuIGRQLmYob2JqZWN0LCBrZXksIGNyZWF0ZURlc2MoMSwgdmFsdWUpKTtcbn0gOiBmdW5jdGlvbihvYmplY3QsIGtleSwgdmFsdWUpe1xuICBvYmplY3Rba2V5XSA9IHZhbHVlO1xuICByZXR1cm4gb2JqZWN0O1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2hpZGUuanNcbi8vIG1vZHVsZSBpZCA9IDEwXG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDIgMyA0IDUgNiA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxNyAxOCAxOSAyMCAyMSAyMiAyMyAyNCAyNSAyNiAyNyAyOCAyOSAzMCAzMSAzMiAzMyAzNCAzNSAzNiAzNyAzOCAzOSA0MCA0MSA0MiA0MyA0NCA0NSA0NiA0NyA0OCA0OSA1MCA1MSA1MiA1MyA1NCA1NSA1NiA1NyA1OCIsIid1c2Ugc3RyaWN0JztcbnZhciBhZGRUb1Vuc2NvcGFibGVzID0gcmVxdWlyZSgnLi9fYWRkLXRvLXVuc2NvcGFibGVzJylcbiAgLCBzdGVwICAgICAgICAgICAgID0gcmVxdWlyZSgnLi9faXRlci1zdGVwJylcbiAgLCBJdGVyYXRvcnMgICAgICAgID0gcmVxdWlyZSgnLi9faXRlcmF0b3JzJylcbiAgLCB0b0lPYmplY3QgICAgICAgID0gcmVxdWlyZSgnLi9fdG8taW9iamVjdCcpO1xuXG4vLyAyMi4xLjMuNCBBcnJheS5wcm90b3R5cGUuZW50cmllcygpXG4vLyAyMi4xLjMuMTMgQXJyYXkucHJvdG90eXBlLmtleXMoKVxuLy8gMjIuMS4zLjI5IEFycmF5LnByb3RvdHlwZS52YWx1ZXMoKVxuLy8gMjIuMS4zLjMwIEFycmF5LnByb3RvdHlwZVtAQGl0ZXJhdG9yXSgpXG5tb2R1bGUuZXhwb3J0cyA9IHJlcXVpcmUoJy4vX2l0ZXItZGVmaW5lJykoQXJyYXksICdBcnJheScsIGZ1bmN0aW9uKGl0ZXJhdGVkLCBraW5kKXtcbiAgdGhpcy5fdCA9IHRvSU9iamVjdChpdGVyYXRlZCk7IC8vIHRhcmdldFxuICB0aGlzLl9pID0gMDsgICAgICAgICAgICAgICAgICAgLy8gbmV4dCBpbmRleFxuICB0aGlzLl9rID0ga2luZDsgICAgICAgICAgICAgICAgLy8ga2luZFxuLy8gMjIuMS41LjIuMSAlQXJyYXlJdGVyYXRvclByb3RvdHlwZSUubmV4dCgpXG59LCBmdW5jdGlvbigpe1xuICB2YXIgTyAgICAgPSB0aGlzLl90XG4gICAgLCBraW5kICA9IHRoaXMuX2tcbiAgICAsIGluZGV4ID0gdGhpcy5faSsrO1xuICBpZighTyB8fCBpbmRleCA+PSBPLmxlbmd0aCl7XG4gICAgdGhpcy5fdCA9IHVuZGVmaW5lZDtcbiAgICByZXR1cm4gc3RlcCgxKTtcbiAgfVxuICBpZihraW5kID09ICdrZXlzJyAgKXJldHVybiBzdGVwKDAsIGluZGV4KTtcbiAgaWYoa2luZCA9PSAndmFsdWVzJylyZXR1cm4gc3RlcCgwLCBPW2luZGV4XSk7XG4gIHJldHVybiBzdGVwKDAsIFtpbmRleCwgT1tpbmRleF1dKTtcbn0sICd2YWx1ZXMnKTtcblxuLy8gYXJndW1lbnRzTGlzdFtAQGl0ZXJhdG9yXSBpcyAlQXJyYXlQcm90b192YWx1ZXMlICg5LjQuNC42LCA5LjQuNC43KVxuSXRlcmF0b3JzLkFyZ3VtZW50cyA9IEl0ZXJhdG9ycy5BcnJheTtcblxuYWRkVG9VbnNjb3BhYmxlcygna2V5cycpO1xuYWRkVG9VbnNjb3BhYmxlcygndmFsdWVzJyk7XG5hZGRUb1Vuc2NvcGFibGVzKCdlbnRyaWVzJyk7XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL2VzNi5hcnJheS5pdGVyYXRvci5qc1xuLy8gbW9kdWxlIGlkID0gMTAwXG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDMgNCA1IDggOSAxNCIsIlwidXNlIHN0cmljdFwiO1xuXG5leHBvcnRzLl9fZXNNb2R1bGUgPSB0cnVlO1xuXG52YXIgX2l0ZXJhdG9yID0gcmVxdWlyZShcIi4uL2NvcmUtanMvc3ltYm9sL2l0ZXJhdG9yXCIpO1xuXG52YXIgX2l0ZXJhdG9yMiA9IF9pbnRlcm9wUmVxdWlyZURlZmF1bHQoX2l0ZXJhdG9yKTtcblxudmFyIF9zeW1ib2wgPSByZXF1aXJlKFwiLi4vY29yZS1qcy9zeW1ib2xcIik7XG5cbnZhciBfc3ltYm9sMiA9IF9pbnRlcm9wUmVxdWlyZURlZmF1bHQoX3N5bWJvbCk7XG5cbnZhciBfdHlwZW9mID0gdHlwZW9mIF9zeW1ib2wyLmRlZmF1bHQgPT09IFwiZnVuY3Rpb25cIiAmJiB0eXBlb2YgX2l0ZXJhdG9yMi5kZWZhdWx0ID09PSBcInN5bWJvbFwiID8gZnVuY3Rpb24gKG9iaikgeyByZXR1cm4gdHlwZW9mIG9iajsgfSA6IGZ1bmN0aW9uIChvYmopIHsgcmV0dXJuIG9iaiAmJiB0eXBlb2YgX3N5bWJvbDIuZGVmYXVsdCA9PT0gXCJmdW5jdGlvblwiICYmIG9iai5jb25zdHJ1Y3RvciA9PT0gX3N5bWJvbDIuZGVmYXVsdCAmJiBvYmogIT09IF9zeW1ib2wyLmRlZmF1bHQucHJvdG90eXBlID8gXCJzeW1ib2xcIiA6IHR5cGVvZiBvYmo7IH07XG5cbmZ1bmN0aW9uIF9pbnRlcm9wUmVxdWlyZURlZmF1bHQob2JqKSB7IHJldHVybiBvYmogJiYgb2JqLl9fZXNNb2R1bGUgPyBvYmogOiB7IGRlZmF1bHQ6IG9iaiB9OyB9XG5cbmV4cG9ydHMuZGVmYXVsdCA9IHR5cGVvZiBfc3ltYm9sMi5kZWZhdWx0ID09PSBcImZ1bmN0aW9uXCIgJiYgX3R5cGVvZihfaXRlcmF0b3IyLmRlZmF1bHQpID09PSBcInN5bWJvbFwiID8gZnVuY3Rpb24gKG9iaikge1xuICByZXR1cm4gdHlwZW9mIG9iaiA9PT0gXCJ1bmRlZmluZWRcIiA/IFwidW5kZWZpbmVkXCIgOiBfdHlwZW9mKG9iaik7XG59IDogZnVuY3Rpb24gKG9iaikge1xuICByZXR1cm4gb2JqICYmIHR5cGVvZiBfc3ltYm9sMi5kZWZhdWx0ID09PSBcImZ1bmN0aW9uXCIgJiYgb2JqLmNvbnN0cnVjdG9yID09PSBfc3ltYm9sMi5kZWZhdWx0ICYmIG9iaiAhPT0gX3N5bWJvbDIuZGVmYXVsdC5wcm90b3R5cGUgPyBcInN5bWJvbFwiIDogdHlwZW9mIG9iaiA9PT0gXCJ1bmRlZmluZWRcIiA/IFwidW5kZWZpbmVkXCIgOiBfdHlwZW9mKG9iaik7XG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9iYWJlbC1ydW50aW1lL2hlbHBlcnMvdHlwZW9mLmpzXG4vLyBtb2R1bGUgaWQgPSAxMDJcbi8vIG1vZHVsZSBjaHVua3MgPSAxIDMgNCA1IDggOSIsInZhciBNRVRBICAgICA9IHJlcXVpcmUoJy4vX3VpZCcpKCdtZXRhJylcbiAgLCBpc09iamVjdCA9IHJlcXVpcmUoJy4vX2lzLW9iamVjdCcpXG4gICwgaGFzICAgICAgPSByZXF1aXJlKCcuL19oYXMnKVxuICAsIHNldERlc2MgID0gcmVxdWlyZSgnLi9fb2JqZWN0LWRwJykuZlxuICAsIGlkICAgICAgID0gMDtcbnZhciBpc0V4dGVuc2libGUgPSBPYmplY3QuaXNFeHRlbnNpYmxlIHx8IGZ1bmN0aW9uKCl7XG4gIHJldHVybiB0cnVlO1xufTtcbnZhciBGUkVFWkUgPSAhcmVxdWlyZSgnLi9fZmFpbHMnKShmdW5jdGlvbigpe1xuICByZXR1cm4gaXNFeHRlbnNpYmxlKE9iamVjdC5wcmV2ZW50RXh0ZW5zaW9ucyh7fSkpO1xufSk7XG52YXIgc2V0TWV0YSA9IGZ1bmN0aW9uKGl0KXtcbiAgc2V0RGVzYyhpdCwgTUVUQSwge3ZhbHVlOiB7XG4gICAgaTogJ08nICsgKytpZCwgLy8gb2JqZWN0IElEXG4gICAgdzoge30gICAgICAgICAgLy8gd2VhayBjb2xsZWN0aW9ucyBJRHNcbiAgfX0pO1xufTtcbnZhciBmYXN0S2V5ID0gZnVuY3Rpb24oaXQsIGNyZWF0ZSl7XG4gIC8vIHJldHVybiBwcmltaXRpdmUgd2l0aCBwcmVmaXhcbiAgaWYoIWlzT2JqZWN0KGl0KSlyZXR1cm4gdHlwZW9mIGl0ID09ICdzeW1ib2wnID8gaXQgOiAodHlwZW9mIGl0ID09ICdzdHJpbmcnID8gJ1MnIDogJ1AnKSArIGl0O1xuICBpZighaGFzKGl0LCBNRVRBKSl7XG4gICAgLy8gY2FuJ3Qgc2V0IG1ldGFkYXRhIHRvIHVuY2F1Z2h0IGZyb3plbiBvYmplY3RcbiAgICBpZighaXNFeHRlbnNpYmxlKGl0KSlyZXR1cm4gJ0YnO1xuICAgIC8vIG5vdCBuZWNlc3NhcnkgdG8gYWRkIG1ldGFkYXRhXG4gICAgaWYoIWNyZWF0ZSlyZXR1cm4gJ0UnO1xuICAgIC8vIGFkZCBtaXNzaW5nIG1ldGFkYXRhXG4gICAgc2V0TWV0YShpdCk7XG4gIC8vIHJldHVybiBvYmplY3QgSURcbiAgfSByZXR1cm4gaXRbTUVUQV0uaTtcbn07XG52YXIgZ2V0V2VhayA9IGZ1bmN0aW9uKGl0LCBjcmVhdGUpe1xuICBpZighaGFzKGl0LCBNRVRBKSl7XG4gICAgLy8gY2FuJ3Qgc2V0IG1ldGFkYXRhIHRvIHVuY2F1Z2h0IGZyb3plbiBvYmplY3RcbiAgICBpZighaXNFeHRlbnNpYmxlKGl0KSlyZXR1cm4gdHJ1ZTtcbiAgICAvLyBub3QgbmVjZXNzYXJ5IHRvIGFkZCBtZXRhZGF0YVxuICAgIGlmKCFjcmVhdGUpcmV0dXJuIGZhbHNlO1xuICAgIC8vIGFkZCBtaXNzaW5nIG1ldGFkYXRhXG4gICAgc2V0TWV0YShpdCk7XG4gIC8vIHJldHVybiBoYXNoIHdlYWsgY29sbGVjdGlvbnMgSURzXG4gIH0gcmV0dXJuIGl0W01FVEFdLnc7XG59O1xuLy8gYWRkIG1ldGFkYXRhIG9uIGZyZWV6ZS1mYW1pbHkgbWV0aG9kcyBjYWxsaW5nXG52YXIgb25GcmVlemUgPSBmdW5jdGlvbihpdCl7XG4gIGlmKEZSRUVaRSAmJiBtZXRhLk5FRUQgJiYgaXNFeHRlbnNpYmxlKGl0KSAmJiAhaGFzKGl0LCBNRVRBKSlzZXRNZXRhKGl0KTtcbiAgcmV0dXJuIGl0O1xufTtcbnZhciBtZXRhID0gbW9kdWxlLmV4cG9ydHMgPSB7XG4gIEtFWTogICAgICBNRVRBLFxuICBORUVEOiAgICAgZmFsc2UsXG4gIGZhc3RLZXk6ICBmYXN0S2V5LFxuICBnZXRXZWFrOiAgZ2V0V2VhayxcbiAgb25GcmVlemU6IG9uRnJlZXplXG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fbWV0YS5qc1xuLy8gbW9kdWxlIGlkID0gMTAzXG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDMgNCA1IDggOSIsInZhciBwSUUgICAgICAgICAgICA9IHJlcXVpcmUoJy4vX29iamVjdC1waWUnKVxuICAsIGNyZWF0ZURlc2MgICAgID0gcmVxdWlyZSgnLi9fcHJvcGVydHktZGVzYycpXG4gICwgdG9JT2JqZWN0ICAgICAgPSByZXF1aXJlKCcuL190by1pb2JqZWN0JylcbiAgLCB0b1ByaW1pdGl2ZSAgICA9IHJlcXVpcmUoJy4vX3RvLXByaW1pdGl2ZScpXG4gICwgaGFzICAgICAgICAgICAgPSByZXF1aXJlKCcuL19oYXMnKVxuICAsIElFOF9ET01fREVGSU5FID0gcmVxdWlyZSgnLi9faWU4LWRvbS1kZWZpbmUnKVxuICAsIGdPUEQgICAgICAgICAgID0gT2JqZWN0LmdldE93blByb3BlcnR5RGVzY3JpcHRvcjtcblxuZXhwb3J0cy5mID0gcmVxdWlyZSgnLi9fZGVzY3JpcHRvcnMnKSA/IGdPUEQgOiBmdW5jdGlvbiBnZXRPd25Qcm9wZXJ0eURlc2NyaXB0b3IoTywgUCl7XG4gIE8gPSB0b0lPYmplY3QoTyk7XG4gIFAgPSB0b1ByaW1pdGl2ZShQLCB0cnVlKTtcbiAgaWYoSUU4X0RPTV9ERUZJTkUpdHJ5IHtcbiAgICByZXR1cm4gZ09QRChPLCBQKTtcbiAgfSBjYXRjaChlKXsgLyogZW1wdHkgKi8gfVxuICBpZihoYXMoTywgUCkpcmV0dXJuIGNyZWF0ZURlc2MoIXBJRS5mLmNhbGwoTywgUCksIE9bUF0pO1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX29iamVjdC1nb3BkLmpzXG4vLyBtb2R1bGUgaWQgPSAxMDRcbi8vIG1vZHVsZSBjaHVua3MgPSAxIDMgNCA1IDggOSIsIi8vIDcuMi4yIElzQXJyYXkoYXJndW1lbnQpXG52YXIgY29mID0gcmVxdWlyZSgnLi9fY29mJyk7XG5tb2R1bGUuZXhwb3J0cyA9IEFycmF5LmlzQXJyYXkgfHwgZnVuY3Rpb24gaXNBcnJheShhcmcpe1xuICByZXR1cm4gY29mKGFyZykgPT0gJ0FycmF5Jztcbn07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19pcy1hcnJheS5qc1xuLy8gbW9kdWxlIGlkID0gMTA3XG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDMgNCA1IDggOSIsInZhciBpc09iamVjdCA9IHJlcXVpcmUoJy4vX2lzLW9iamVjdCcpO1xubW9kdWxlLmV4cG9ydHMgPSBmdW5jdGlvbihpdCl7XG4gIGlmKCFpc09iamVjdChpdCkpdGhyb3cgVHlwZUVycm9yKGl0ICsgJyBpcyBub3QgYW4gb2JqZWN0IScpO1xuICByZXR1cm4gaXQ7XG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fYW4tb2JqZWN0LmpzXG4vLyBtb2R1bGUgaWQgPSAxMVxuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAyIDMgNCA1IDYgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTcgMTggMTkgMjAgMjEgMjIgMjMgMjQgMjUgMjYgMjcgMjggMjkgMzAgMzEgMzIgMzMgMzQgMzUgMzYgMzcgMzggMzkgNDAgNDEgNDIgNDMgNDQgNDUgNDYgNDcgNDggNDkgNTAgNTEgNTIgNTMgNTQgNTUgNTYgNTcgNTgiLCJtb2R1bGUuZXhwb3J0cyA9IHsgXCJkZWZhdWx0XCI6IHJlcXVpcmUoXCJjb3JlLWpzL2xpYnJhcnkvZm4vc3ltYm9sXCIpLCBfX2VzTW9kdWxlOiB0cnVlIH07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2JhYmVsLXJ1bnRpbWUvY29yZS1qcy9zeW1ib2wuanNcbi8vIG1vZHVsZSBpZCA9IDExM1xuLy8gbW9kdWxlIGNodW5rcyA9IDEgMyA0IDUgOCA5IiwibW9kdWxlLmV4cG9ydHMgPSB7IFwiZGVmYXVsdFwiOiByZXF1aXJlKFwiY29yZS1qcy9saWJyYXJ5L2ZuL3N5bWJvbC9pdGVyYXRvclwiKSwgX19lc01vZHVsZTogdHJ1ZSB9O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9iYWJlbC1ydW50aW1lL2NvcmUtanMvc3ltYm9sL2l0ZXJhdG9yLmpzXG4vLyBtb2R1bGUgaWQgPSAxMTRcbi8vIG1vZHVsZSBjaHVua3MgPSAxIDMgNCA1IDggOSIsInJlcXVpcmUoJy4uLy4uL21vZHVsZXMvZXM2LnN5bWJvbCcpO1xucmVxdWlyZSgnLi4vLi4vbW9kdWxlcy9lczYub2JqZWN0LnRvLXN0cmluZycpO1xucmVxdWlyZSgnLi4vLi4vbW9kdWxlcy9lczcuc3ltYm9sLmFzeW5jLWl0ZXJhdG9yJyk7XG5yZXF1aXJlKCcuLi8uLi9tb2R1bGVzL2VzNy5zeW1ib2wub2JzZXJ2YWJsZScpO1xubW9kdWxlLmV4cG9ydHMgPSByZXF1aXJlKCcuLi8uLi9tb2R1bGVzL19jb3JlJykuU3ltYm9sO1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvZm4vc3ltYm9sL2luZGV4LmpzXG4vLyBtb2R1bGUgaWQgPSAxMTdcbi8vIG1vZHVsZSBjaHVua3MgPSAxIDMgNCA1IDggOSIsInJlcXVpcmUoJy4uLy4uL21vZHVsZXMvZXM2LnN0cmluZy5pdGVyYXRvcicpO1xucmVxdWlyZSgnLi4vLi4vbW9kdWxlcy93ZWIuZG9tLml0ZXJhYmxlJyk7XG5tb2R1bGUuZXhwb3J0cyA9IHJlcXVpcmUoJy4uLy4uL21vZHVsZXMvX3drcy1leHQnKS5mKCdpdGVyYXRvcicpO1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvZm4vc3ltYm9sL2l0ZXJhdG9yLmpzXG4vLyBtb2R1bGUgaWQgPSAxMThcbi8vIG1vZHVsZSBjaHVua3MgPSAxIDMgNCA1IDggOSIsIi8vIGFsbCBlbnVtZXJhYmxlIG9iamVjdCBrZXlzLCBpbmNsdWRlcyBzeW1ib2xzXG52YXIgZ2V0S2V5cyA9IHJlcXVpcmUoJy4vX29iamVjdC1rZXlzJylcbiAgLCBnT1BTICAgID0gcmVxdWlyZSgnLi9fb2JqZWN0LWdvcHMnKVxuICAsIHBJRSAgICAgPSByZXF1aXJlKCcuL19vYmplY3QtcGllJyk7XG5tb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uKGl0KXtcbiAgdmFyIHJlc3VsdCAgICAgPSBnZXRLZXlzKGl0KVxuICAgICwgZ2V0U3ltYm9scyA9IGdPUFMuZjtcbiAgaWYoZ2V0U3ltYm9scyl7XG4gICAgdmFyIHN5bWJvbHMgPSBnZXRTeW1ib2xzKGl0KVxuICAgICAgLCBpc0VudW0gID0gcElFLmZcbiAgICAgICwgaSAgICAgICA9IDBcbiAgICAgICwga2V5O1xuICAgIHdoaWxlKHN5bWJvbHMubGVuZ3RoID4gaSlpZihpc0VudW0uY2FsbChpdCwga2V5ID0gc3ltYm9sc1tpKytdKSlyZXN1bHQucHVzaChrZXkpO1xuICB9IHJldHVybiByZXN1bHQ7XG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fZW51bS1rZXlzLmpzXG4vLyBtb2R1bGUgaWQgPSAxMTlcbi8vIG1vZHVsZSBjaHVua3MgPSAxIDMgNCA1IDggOSIsIm1vZHVsZS5leHBvcnRzID0gZnVuY3Rpb24oYml0bWFwLCB2YWx1ZSl7XG4gIHJldHVybiB7XG4gICAgZW51bWVyYWJsZSAgOiAhKGJpdG1hcCAmIDEpLFxuICAgIGNvbmZpZ3VyYWJsZTogIShiaXRtYXAgJiAyKSxcbiAgICB3cml0YWJsZSAgICA6ICEoYml0bWFwICYgNCksXG4gICAgdmFsdWUgICAgICAgOiB2YWx1ZVxuICB9O1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX3Byb3BlcnR5LWRlc2MuanNcbi8vIG1vZHVsZSBpZCA9IDEyXG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDIgMyA0IDUgNiA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxNyAxOCAxOSAyMCAyMSAyMiAyMyAyNCAyNSAyNiAyNyAyOCAyOSAzMCAzMSAzMiAzMyAzNCAzNSAzNiAzNyAzOCAzOSA0MCA0MSA0MiA0MyA0NCA0NSA0NiA0NyA0OCA0OSA1MCA1MSA1MiA1MyA1NCA1NSA1NiA1NyA1OCIsInZhciBnZXRLZXlzICAgPSByZXF1aXJlKCcuL19vYmplY3Qta2V5cycpXG4gICwgdG9JT2JqZWN0ID0gcmVxdWlyZSgnLi9fdG8taW9iamVjdCcpO1xubW9kdWxlLmV4cG9ydHMgPSBmdW5jdGlvbihvYmplY3QsIGVsKXtcbiAgdmFyIE8gICAgICA9IHRvSU9iamVjdChvYmplY3QpXG4gICAgLCBrZXlzICAgPSBnZXRLZXlzKE8pXG4gICAgLCBsZW5ndGggPSBrZXlzLmxlbmd0aFxuICAgICwgaW5kZXggID0gMFxuICAgICwga2V5O1xuICB3aGlsZShsZW5ndGggPiBpbmRleClpZihPW2tleSA9IGtleXNbaW5kZXgrK11dID09PSBlbClyZXR1cm4ga2V5O1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2tleW9mLmpzXG4vLyBtb2R1bGUgaWQgPSAxMjJcbi8vIG1vZHVsZSBjaHVua3MgPSAxIDMgNCA1IDggOSIsIi8vIGZhbGxiYWNrIGZvciBJRTExIGJ1Z2d5IE9iamVjdC5nZXRPd25Qcm9wZXJ0eU5hbWVzIHdpdGggaWZyYW1lIGFuZCB3aW5kb3dcbnZhciB0b0lPYmplY3QgPSByZXF1aXJlKCcuL190by1pb2JqZWN0JylcbiAgLCBnT1BOICAgICAgPSByZXF1aXJlKCcuL19vYmplY3QtZ29wbicpLmZcbiAgLCB0b1N0cmluZyAgPSB7fS50b1N0cmluZztcblxudmFyIHdpbmRvd05hbWVzID0gdHlwZW9mIHdpbmRvdyA9PSAnb2JqZWN0JyAmJiB3aW5kb3cgJiYgT2JqZWN0LmdldE93blByb3BlcnR5TmFtZXNcbiAgPyBPYmplY3QuZ2V0T3duUHJvcGVydHlOYW1lcyh3aW5kb3cpIDogW107XG5cbnZhciBnZXRXaW5kb3dOYW1lcyA9IGZ1bmN0aW9uKGl0KXtcbiAgdHJ5IHtcbiAgICByZXR1cm4gZ09QTihpdCk7XG4gIH0gY2F0Y2goZSl7XG4gICAgcmV0dXJuIHdpbmRvd05hbWVzLnNsaWNlKCk7XG4gIH1cbn07XG5cbm1vZHVsZS5leHBvcnRzLmYgPSBmdW5jdGlvbiBnZXRPd25Qcm9wZXJ0eU5hbWVzKGl0KXtcbiAgcmV0dXJuIHdpbmRvd05hbWVzICYmIHRvU3RyaW5nLmNhbGwoaXQpID09ICdbb2JqZWN0IFdpbmRvd10nID8gZ2V0V2luZG93TmFtZXMoaXQpIDogZ09QTih0b0lPYmplY3QoaXQpKTtcbn07XG5cblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX29iamVjdC1nb3BuLWV4dC5qc1xuLy8gbW9kdWxlIGlkID0gMTIzXG4vLyBtb2R1bGUgY2h1bmtzID0gMSAzIDQgNSA4IDkiLCIndXNlIHN0cmljdCc7XG4vLyBFQ01BU2NyaXB0IDYgc3ltYm9scyBzaGltXG52YXIgZ2xvYmFsICAgICAgICAgPSByZXF1aXJlKCcuL19nbG9iYWwnKVxuICAsIGhhcyAgICAgICAgICAgID0gcmVxdWlyZSgnLi9faGFzJylcbiAgLCBERVNDUklQVE9SUyAgICA9IHJlcXVpcmUoJy4vX2Rlc2NyaXB0b3JzJylcbiAgLCAkZXhwb3J0ICAgICAgICA9IHJlcXVpcmUoJy4vX2V4cG9ydCcpXG4gICwgcmVkZWZpbmUgICAgICAgPSByZXF1aXJlKCcuL19yZWRlZmluZScpXG4gICwgTUVUQSAgICAgICAgICAgPSByZXF1aXJlKCcuL19tZXRhJykuS0VZXG4gICwgJGZhaWxzICAgICAgICAgPSByZXF1aXJlKCcuL19mYWlscycpXG4gICwgc2hhcmVkICAgICAgICAgPSByZXF1aXJlKCcuL19zaGFyZWQnKVxuICAsIHNldFRvU3RyaW5nVGFnID0gcmVxdWlyZSgnLi9fc2V0LXRvLXN0cmluZy10YWcnKVxuICAsIHVpZCAgICAgICAgICAgID0gcmVxdWlyZSgnLi9fdWlkJylcbiAgLCB3a3MgICAgICAgICAgICA9IHJlcXVpcmUoJy4vX3drcycpXG4gICwgd2tzRXh0ICAgICAgICAgPSByZXF1aXJlKCcuL193a3MtZXh0JylcbiAgLCB3a3NEZWZpbmUgICAgICA9IHJlcXVpcmUoJy4vX3drcy1kZWZpbmUnKVxuICAsIGtleU9mICAgICAgICAgID0gcmVxdWlyZSgnLi9fa2V5b2YnKVxuICAsIGVudW1LZXlzICAgICAgID0gcmVxdWlyZSgnLi9fZW51bS1rZXlzJylcbiAgLCBpc0FycmF5ICAgICAgICA9IHJlcXVpcmUoJy4vX2lzLWFycmF5JylcbiAgLCBhbk9iamVjdCAgICAgICA9IHJlcXVpcmUoJy4vX2FuLW9iamVjdCcpXG4gICwgdG9JT2JqZWN0ICAgICAgPSByZXF1aXJlKCcuL190by1pb2JqZWN0JylcbiAgLCB0b1ByaW1pdGl2ZSAgICA9IHJlcXVpcmUoJy4vX3RvLXByaW1pdGl2ZScpXG4gICwgY3JlYXRlRGVzYyAgICAgPSByZXF1aXJlKCcuL19wcm9wZXJ0eS1kZXNjJylcbiAgLCBfY3JlYXRlICAgICAgICA9IHJlcXVpcmUoJy4vX29iamVjdC1jcmVhdGUnKVxuICAsIGdPUE5FeHQgICAgICAgID0gcmVxdWlyZSgnLi9fb2JqZWN0LWdvcG4tZXh0JylcbiAgLCAkR09QRCAgICAgICAgICA9IHJlcXVpcmUoJy4vX29iamVjdC1nb3BkJylcbiAgLCAkRFAgICAgICAgICAgICA9IHJlcXVpcmUoJy4vX29iamVjdC1kcCcpXG4gICwgJGtleXMgICAgICAgICAgPSByZXF1aXJlKCcuL19vYmplY3Qta2V5cycpXG4gICwgZ09QRCAgICAgICAgICAgPSAkR09QRC5mXG4gICwgZFAgICAgICAgICAgICAgPSAkRFAuZlxuICAsIGdPUE4gICAgICAgICAgID0gZ09QTkV4dC5mXG4gICwgJFN5bWJvbCAgICAgICAgPSBnbG9iYWwuU3ltYm9sXG4gICwgJEpTT04gICAgICAgICAgPSBnbG9iYWwuSlNPTlxuICAsIF9zdHJpbmdpZnkgICAgID0gJEpTT04gJiYgJEpTT04uc3RyaW5naWZ5XG4gICwgUFJPVE9UWVBFICAgICAgPSAncHJvdG90eXBlJ1xuICAsIEhJRERFTiAgICAgICAgID0gd2tzKCdfaGlkZGVuJylcbiAgLCBUT19QUklNSVRJVkUgICA9IHdrcygndG9QcmltaXRpdmUnKVxuICAsIGlzRW51bSAgICAgICAgID0ge30ucHJvcGVydHlJc0VudW1lcmFibGVcbiAgLCBTeW1ib2xSZWdpc3RyeSA9IHNoYXJlZCgnc3ltYm9sLXJlZ2lzdHJ5JylcbiAgLCBBbGxTeW1ib2xzICAgICA9IHNoYXJlZCgnc3ltYm9scycpXG4gICwgT1BTeW1ib2xzICAgICAgPSBzaGFyZWQoJ29wLXN5bWJvbHMnKVxuICAsIE9iamVjdFByb3RvICAgID0gT2JqZWN0W1BST1RPVFlQRV1cbiAgLCBVU0VfTkFUSVZFICAgICA9IHR5cGVvZiAkU3ltYm9sID09ICdmdW5jdGlvbidcbiAgLCBRT2JqZWN0ICAgICAgICA9IGdsb2JhbC5RT2JqZWN0O1xuLy8gRG9uJ3QgdXNlIHNldHRlcnMgaW4gUXQgU2NyaXB0LCBodHRwczovL2dpdGh1Yi5jb20vemxvaXJvY2svY29yZS1qcy9pc3N1ZXMvMTczXG52YXIgc2V0dGVyID0gIVFPYmplY3QgfHwgIVFPYmplY3RbUFJPVE9UWVBFXSB8fCAhUU9iamVjdFtQUk9UT1RZUEVdLmZpbmRDaGlsZDtcblxuLy8gZmFsbGJhY2sgZm9yIG9sZCBBbmRyb2lkLCBodHRwczovL2NvZGUuZ29vZ2xlLmNvbS9wL3Y4L2lzc3Vlcy9kZXRhaWw/aWQ9Njg3XG52YXIgc2V0U3ltYm9sRGVzYyA9IERFU0NSSVBUT1JTICYmICRmYWlscyhmdW5jdGlvbigpe1xuICByZXR1cm4gX2NyZWF0ZShkUCh7fSwgJ2EnLCB7XG4gICAgZ2V0OiBmdW5jdGlvbigpeyByZXR1cm4gZFAodGhpcywgJ2EnLCB7dmFsdWU6IDd9KS5hOyB9XG4gIH0pKS5hICE9IDc7XG59KSA/IGZ1bmN0aW9uKGl0LCBrZXksIEQpe1xuICB2YXIgcHJvdG9EZXNjID0gZ09QRChPYmplY3RQcm90bywga2V5KTtcbiAgaWYocHJvdG9EZXNjKWRlbGV0ZSBPYmplY3RQcm90b1trZXldO1xuICBkUChpdCwga2V5LCBEKTtcbiAgaWYocHJvdG9EZXNjICYmIGl0ICE9PSBPYmplY3RQcm90bylkUChPYmplY3RQcm90bywga2V5LCBwcm90b0Rlc2MpO1xufSA6IGRQO1xuXG52YXIgd3JhcCA9IGZ1bmN0aW9uKHRhZyl7XG4gIHZhciBzeW0gPSBBbGxTeW1ib2xzW3RhZ10gPSBfY3JlYXRlKCRTeW1ib2xbUFJPVE9UWVBFXSk7XG4gIHN5bS5fayA9IHRhZztcbiAgcmV0dXJuIHN5bTtcbn07XG5cbnZhciBpc1N5bWJvbCA9IFVTRV9OQVRJVkUgJiYgdHlwZW9mICRTeW1ib2wuaXRlcmF0b3IgPT0gJ3N5bWJvbCcgPyBmdW5jdGlvbihpdCl7XG4gIHJldHVybiB0eXBlb2YgaXQgPT0gJ3N5bWJvbCc7XG59IDogZnVuY3Rpb24oaXQpe1xuICByZXR1cm4gaXQgaW5zdGFuY2VvZiAkU3ltYm9sO1xufTtcblxudmFyICRkZWZpbmVQcm9wZXJ0eSA9IGZ1bmN0aW9uIGRlZmluZVByb3BlcnR5KGl0LCBrZXksIEQpe1xuICBpZihpdCA9PT0gT2JqZWN0UHJvdG8pJGRlZmluZVByb3BlcnR5KE9QU3ltYm9scywga2V5LCBEKTtcbiAgYW5PYmplY3QoaXQpO1xuICBrZXkgPSB0b1ByaW1pdGl2ZShrZXksIHRydWUpO1xuICBhbk9iamVjdChEKTtcbiAgaWYoaGFzKEFsbFN5bWJvbHMsIGtleSkpe1xuICAgIGlmKCFELmVudW1lcmFibGUpe1xuICAgICAgaWYoIWhhcyhpdCwgSElEREVOKSlkUChpdCwgSElEREVOLCBjcmVhdGVEZXNjKDEsIHt9KSk7XG4gICAgICBpdFtISURERU5dW2tleV0gPSB0cnVlO1xuICAgIH0gZWxzZSB7XG4gICAgICBpZihoYXMoaXQsIEhJRERFTikgJiYgaXRbSElEREVOXVtrZXldKWl0W0hJRERFTl1ba2V5XSA9IGZhbHNlO1xuICAgICAgRCA9IF9jcmVhdGUoRCwge2VudW1lcmFibGU6IGNyZWF0ZURlc2MoMCwgZmFsc2UpfSk7XG4gICAgfSByZXR1cm4gc2V0U3ltYm9sRGVzYyhpdCwga2V5LCBEKTtcbiAgfSByZXR1cm4gZFAoaXQsIGtleSwgRCk7XG59O1xudmFyICRkZWZpbmVQcm9wZXJ0aWVzID0gZnVuY3Rpb24gZGVmaW5lUHJvcGVydGllcyhpdCwgUCl7XG4gIGFuT2JqZWN0KGl0KTtcbiAgdmFyIGtleXMgPSBlbnVtS2V5cyhQID0gdG9JT2JqZWN0KFApKVxuICAgICwgaSAgICA9IDBcbiAgICAsIGwgPSBrZXlzLmxlbmd0aFxuICAgICwga2V5O1xuICB3aGlsZShsID4gaSkkZGVmaW5lUHJvcGVydHkoaXQsIGtleSA9IGtleXNbaSsrXSwgUFtrZXldKTtcbiAgcmV0dXJuIGl0O1xufTtcbnZhciAkY3JlYXRlID0gZnVuY3Rpb24gY3JlYXRlKGl0LCBQKXtcbiAgcmV0dXJuIFAgPT09IHVuZGVmaW5lZCA/IF9jcmVhdGUoaXQpIDogJGRlZmluZVByb3BlcnRpZXMoX2NyZWF0ZShpdCksIFApO1xufTtcbnZhciAkcHJvcGVydHlJc0VudW1lcmFibGUgPSBmdW5jdGlvbiBwcm9wZXJ0eUlzRW51bWVyYWJsZShrZXkpe1xuICB2YXIgRSA9IGlzRW51bS5jYWxsKHRoaXMsIGtleSA9IHRvUHJpbWl0aXZlKGtleSwgdHJ1ZSkpO1xuICBpZih0aGlzID09PSBPYmplY3RQcm90byAmJiBoYXMoQWxsU3ltYm9scywga2V5KSAmJiAhaGFzKE9QU3ltYm9scywga2V5KSlyZXR1cm4gZmFsc2U7XG4gIHJldHVybiBFIHx8ICFoYXModGhpcywga2V5KSB8fCAhaGFzKEFsbFN5bWJvbHMsIGtleSkgfHwgaGFzKHRoaXMsIEhJRERFTikgJiYgdGhpc1tISURERU5dW2tleV0gPyBFIDogdHJ1ZTtcbn07XG52YXIgJGdldE93blByb3BlcnR5RGVzY3JpcHRvciA9IGZ1bmN0aW9uIGdldE93blByb3BlcnR5RGVzY3JpcHRvcihpdCwga2V5KXtcbiAgaXQgID0gdG9JT2JqZWN0KGl0KTtcbiAga2V5ID0gdG9QcmltaXRpdmUoa2V5LCB0cnVlKTtcbiAgaWYoaXQgPT09IE9iamVjdFByb3RvICYmIGhhcyhBbGxTeW1ib2xzLCBrZXkpICYmICFoYXMoT1BTeW1ib2xzLCBrZXkpKXJldHVybjtcbiAgdmFyIEQgPSBnT1BEKGl0LCBrZXkpO1xuICBpZihEICYmIGhhcyhBbGxTeW1ib2xzLCBrZXkpICYmICEoaGFzKGl0LCBISURERU4pICYmIGl0W0hJRERFTl1ba2V5XSkpRC5lbnVtZXJhYmxlID0gdHJ1ZTtcbiAgcmV0dXJuIEQ7XG59O1xudmFyICRnZXRPd25Qcm9wZXJ0eU5hbWVzID0gZnVuY3Rpb24gZ2V0T3duUHJvcGVydHlOYW1lcyhpdCl7XG4gIHZhciBuYW1lcyAgPSBnT1BOKHRvSU9iamVjdChpdCkpXG4gICAgLCByZXN1bHQgPSBbXVxuICAgICwgaSAgICAgID0gMFxuICAgICwga2V5O1xuICB3aGlsZShuYW1lcy5sZW5ndGggPiBpKXtcbiAgICBpZighaGFzKEFsbFN5bWJvbHMsIGtleSA9IG5hbWVzW2krK10pICYmIGtleSAhPSBISURERU4gJiYga2V5ICE9IE1FVEEpcmVzdWx0LnB1c2goa2V5KTtcbiAgfSByZXR1cm4gcmVzdWx0O1xufTtcbnZhciAkZ2V0T3duUHJvcGVydHlTeW1ib2xzID0gZnVuY3Rpb24gZ2V0T3duUHJvcGVydHlTeW1ib2xzKGl0KXtcbiAgdmFyIElTX09QICA9IGl0ID09PSBPYmplY3RQcm90b1xuICAgICwgbmFtZXMgID0gZ09QTihJU19PUCA/IE9QU3ltYm9scyA6IHRvSU9iamVjdChpdCkpXG4gICAgLCByZXN1bHQgPSBbXVxuICAgICwgaSAgICAgID0gMFxuICAgICwga2V5O1xuICB3aGlsZShuYW1lcy5sZW5ndGggPiBpKXtcbiAgICBpZihoYXMoQWxsU3ltYm9scywga2V5ID0gbmFtZXNbaSsrXSkgJiYgKElTX09QID8gaGFzKE9iamVjdFByb3RvLCBrZXkpIDogdHJ1ZSkpcmVzdWx0LnB1c2goQWxsU3ltYm9sc1trZXldKTtcbiAgfSByZXR1cm4gcmVzdWx0O1xufTtcblxuLy8gMTkuNC4xLjEgU3ltYm9sKFtkZXNjcmlwdGlvbl0pXG5pZighVVNFX05BVElWRSl7XG4gICRTeW1ib2wgPSBmdW5jdGlvbiBTeW1ib2woKXtcbiAgICBpZih0aGlzIGluc3RhbmNlb2YgJFN5bWJvbCl0aHJvdyBUeXBlRXJyb3IoJ1N5bWJvbCBpcyBub3QgYSBjb25zdHJ1Y3RvciEnKTtcbiAgICB2YXIgdGFnID0gdWlkKGFyZ3VtZW50cy5sZW5ndGggPiAwID8gYXJndW1lbnRzWzBdIDogdW5kZWZpbmVkKTtcbiAgICB2YXIgJHNldCA9IGZ1bmN0aW9uKHZhbHVlKXtcbiAgICAgIGlmKHRoaXMgPT09IE9iamVjdFByb3RvKSRzZXQuY2FsbChPUFN5bWJvbHMsIHZhbHVlKTtcbiAgICAgIGlmKGhhcyh0aGlzLCBISURERU4pICYmIGhhcyh0aGlzW0hJRERFTl0sIHRhZykpdGhpc1tISURERU5dW3RhZ10gPSBmYWxzZTtcbiAgICAgIHNldFN5bWJvbERlc2ModGhpcywgdGFnLCBjcmVhdGVEZXNjKDEsIHZhbHVlKSk7XG4gICAgfTtcbiAgICBpZihERVNDUklQVE9SUyAmJiBzZXR0ZXIpc2V0U3ltYm9sRGVzYyhPYmplY3RQcm90bywgdGFnLCB7Y29uZmlndXJhYmxlOiB0cnVlLCBzZXQ6ICRzZXR9KTtcbiAgICByZXR1cm4gd3JhcCh0YWcpO1xuICB9O1xuICByZWRlZmluZSgkU3ltYm9sW1BST1RPVFlQRV0sICd0b1N0cmluZycsIGZ1bmN0aW9uIHRvU3RyaW5nKCl7XG4gICAgcmV0dXJuIHRoaXMuX2s7XG4gIH0pO1xuXG4gICRHT1BELmYgPSAkZ2V0T3duUHJvcGVydHlEZXNjcmlwdG9yO1xuICAkRFAuZiAgID0gJGRlZmluZVByb3BlcnR5O1xuICByZXF1aXJlKCcuL19vYmplY3QtZ29wbicpLmYgPSBnT1BORXh0LmYgPSAkZ2V0T3duUHJvcGVydHlOYW1lcztcbiAgcmVxdWlyZSgnLi9fb2JqZWN0LXBpZScpLmYgID0gJHByb3BlcnR5SXNFbnVtZXJhYmxlO1xuICByZXF1aXJlKCcuL19vYmplY3QtZ29wcycpLmYgPSAkZ2V0T3duUHJvcGVydHlTeW1ib2xzO1xuXG4gIGlmKERFU0NSSVBUT1JTICYmICFyZXF1aXJlKCcuL19saWJyYXJ5Jykpe1xuICAgIHJlZGVmaW5lKE9iamVjdFByb3RvLCAncHJvcGVydHlJc0VudW1lcmFibGUnLCAkcHJvcGVydHlJc0VudW1lcmFibGUsIHRydWUpO1xuICB9XG5cbiAgd2tzRXh0LmYgPSBmdW5jdGlvbihuYW1lKXtcbiAgICByZXR1cm4gd3JhcCh3a3MobmFtZSkpO1xuICB9XG59XG5cbiRleHBvcnQoJGV4cG9ydC5HICsgJGV4cG9ydC5XICsgJGV4cG9ydC5GICogIVVTRV9OQVRJVkUsIHtTeW1ib2w6ICRTeW1ib2x9KTtcblxuZm9yKHZhciBzeW1ib2xzID0gKFxuICAvLyAxOS40LjIuMiwgMTkuNC4yLjMsIDE5LjQuMi40LCAxOS40LjIuNiwgMTkuNC4yLjgsIDE5LjQuMi45LCAxOS40LjIuMTAsIDE5LjQuMi4xMSwgMTkuNC4yLjEyLCAxOS40LjIuMTMsIDE5LjQuMi4xNFxuICAnaGFzSW5zdGFuY2UsaXNDb25jYXRTcHJlYWRhYmxlLGl0ZXJhdG9yLG1hdGNoLHJlcGxhY2Usc2VhcmNoLHNwZWNpZXMsc3BsaXQsdG9QcmltaXRpdmUsdG9TdHJpbmdUYWcsdW5zY29wYWJsZXMnXG4pLnNwbGl0KCcsJyksIGkgPSAwOyBzeW1ib2xzLmxlbmd0aCA+IGk7ICl3a3Moc3ltYm9sc1tpKytdKTtcblxuZm9yKHZhciBzeW1ib2xzID0gJGtleXMod2tzLnN0b3JlKSwgaSA9IDA7IHN5bWJvbHMubGVuZ3RoID4gaTsgKXdrc0RlZmluZShzeW1ib2xzW2krK10pO1xuXG4kZXhwb3J0KCRleHBvcnQuUyArICRleHBvcnQuRiAqICFVU0VfTkFUSVZFLCAnU3ltYm9sJywge1xuICAvLyAxOS40LjIuMSBTeW1ib2wuZm9yKGtleSlcbiAgJ2Zvcic6IGZ1bmN0aW9uKGtleSl7XG4gICAgcmV0dXJuIGhhcyhTeW1ib2xSZWdpc3RyeSwga2V5ICs9ICcnKVxuICAgICAgPyBTeW1ib2xSZWdpc3RyeVtrZXldXG4gICAgICA6IFN5bWJvbFJlZ2lzdHJ5W2tleV0gPSAkU3ltYm9sKGtleSk7XG4gIH0sXG4gIC8vIDE5LjQuMi41IFN5bWJvbC5rZXlGb3Ioc3ltKVxuICBrZXlGb3I6IGZ1bmN0aW9uIGtleUZvcihrZXkpe1xuICAgIGlmKGlzU3ltYm9sKGtleSkpcmV0dXJuIGtleU9mKFN5bWJvbFJlZ2lzdHJ5LCBrZXkpO1xuICAgIHRocm93IFR5cGVFcnJvcihrZXkgKyAnIGlzIG5vdCBhIHN5bWJvbCEnKTtcbiAgfSxcbiAgdXNlU2V0dGVyOiBmdW5jdGlvbigpeyBzZXR0ZXIgPSB0cnVlOyB9LFxuICB1c2VTaW1wbGU6IGZ1bmN0aW9uKCl7IHNldHRlciA9IGZhbHNlOyB9XG59KTtcblxuJGV4cG9ydCgkZXhwb3J0LlMgKyAkZXhwb3J0LkYgKiAhVVNFX05BVElWRSwgJ09iamVjdCcsIHtcbiAgLy8gMTkuMS4yLjIgT2JqZWN0LmNyZWF0ZShPIFssIFByb3BlcnRpZXNdKVxuICBjcmVhdGU6ICRjcmVhdGUsXG4gIC8vIDE5LjEuMi40IE9iamVjdC5kZWZpbmVQcm9wZXJ0eShPLCBQLCBBdHRyaWJ1dGVzKVxuICBkZWZpbmVQcm9wZXJ0eTogJGRlZmluZVByb3BlcnR5LFxuICAvLyAxOS4xLjIuMyBPYmplY3QuZGVmaW5lUHJvcGVydGllcyhPLCBQcm9wZXJ0aWVzKVxuICBkZWZpbmVQcm9wZXJ0aWVzOiAkZGVmaW5lUHJvcGVydGllcyxcbiAgLy8gMTkuMS4yLjYgT2JqZWN0LmdldE93blByb3BlcnR5RGVzY3JpcHRvcihPLCBQKVxuICBnZXRPd25Qcm9wZXJ0eURlc2NyaXB0b3I6ICRnZXRPd25Qcm9wZXJ0eURlc2NyaXB0b3IsXG4gIC8vIDE5LjEuMi43IE9iamVjdC5nZXRPd25Qcm9wZXJ0eU5hbWVzKE8pXG4gIGdldE93blByb3BlcnR5TmFtZXM6ICRnZXRPd25Qcm9wZXJ0eU5hbWVzLFxuICAvLyAxOS4xLjIuOCBPYmplY3QuZ2V0T3duUHJvcGVydHlTeW1ib2xzKE8pXG4gIGdldE93blByb3BlcnR5U3ltYm9sczogJGdldE93blByb3BlcnR5U3ltYm9sc1xufSk7XG5cbi8vIDI0LjMuMiBKU09OLnN0cmluZ2lmeSh2YWx1ZSBbLCByZXBsYWNlciBbLCBzcGFjZV1dKVxuJEpTT04gJiYgJGV4cG9ydCgkZXhwb3J0LlMgKyAkZXhwb3J0LkYgKiAoIVVTRV9OQVRJVkUgfHwgJGZhaWxzKGZ1bmN0aW9uKCl7XG4gIHZhciBTID0gJFN5bWJvbCgpO1xuICAvLyBNUyBFZGdlIGNvbnZlcnRzIHN5bWJvbCB2YWx1ZXMgdG8gSlNPTiBhcyB7fVxuICAvLyBXZWJLaXQgY29udmVydHMgc3ltYm9sIHZhbHVlcyB0byBKU09OIGFzIG51bGxcbiAgLy8gVjggdGhyb3dzIG9uIGJveGVkIHN5bWJvbHNcbiAgcmV0dXJuIF9zdHJpbmdpZnkoW1NdKSAhPSAnW251bGxdJyB8fCBfc3RyaW5naWZ5KHthOiBTfSkgIT0gJ3t9JyB8fCBfc3RyaW5naWZ5KE9iamVjdChTKSkgIT0gJ3t9Jztcbn0pKSwgJ0pTT04nLCB7XG4gIHN0cmluZ2lmeTogZnVuY3Rpb24gc3RyaW5naWZ5KGl0KXtcbiAgICBpZihpdCA9PT0gdW5kZWZpbmVkIHx8IGlzU3ltYm9sKGl0KSlyZXR1cm47IC8vIElFOCByZXR1cm5zIHN0cmluZyBvbiB1bmRlZmluZWRcbiAgICB2YXIgYXJncyA9IFtpdF1cbiAgICAgICwgaSAgICA9IDFcbiAgICAgICwgcmVwbGFjZXIsICRyZXBsYWNlcjtcbiAgICB3aGlsZShhcmd1bWVudHMubGVuZ3RoID4gaSlhcmdzLnB1c2goYXJndW1lbnRzW2krK10pO1xuICAgIHJlcGxhY2VyID0gYXJnc1sxXTtcbiAgICBpZih0eXBlb2YgcmVwbGFjZXIgPT0gJ2Z1bmN0aW9uJykkcmVwbGFjZXIgPSByZXBsYWNlcjtcbiAgICBpZigkcmVwbGFjZXIgfHwgIWlzQXJyYXkocmVwbGFjZXIpKXJlcGxhY2VyID0gZnVuY3Rpb24oa2V5LCB2YWx1ZSl7XG4gICAgICBpZigkcmVwbGFjZXIpdmFsdWUgPSAkcmVwbGFjZXIuY2FsbCh0aGlzLCBrZXksIHZhbHVlKTtcbiAgICAgIGlmKCFpc1N5bWJvbCh2YWx1ZSkpcmV0dXJuIHZhbHVlO1xuICAgIH07XG4gICAgYXJnc1sxXSA9IHJlcGxhY2VyO1xuICAgIHJldHVybiBfc3RyaW5naWZ5LmFwcGx5KCRKU09OLCBhcmdzKTtcbiAgfVxufSk7XG5cbi8vIDE5LjQuMy40IFN5bWJvbC5wcm90b3R5cGVbQEB0b1ByaW1pdGl2ZV0oaGludClcbiRTeW1ib2xbUFJPVE9UWVBFXVtUT19QUklNSVRJVkVdIHx8IHJlcXVpcmUoJy4vX2hpZGUnKSgkU3ltYm9sW1BST1RPVFlQRV0sIFRPX1BSSU1JVElWRSwgJFN5bWJvbFtQUk9UT1RZUEVdLnZhbHVlT2YpO1xuLy8gMTkuNC4zLjUgU3ltYm9sLnByb3RvdHlwZVtAQHRvU3RyaW5nVGFnXVxuc2V0VG9TdHJpbmdUYWcoJFN5bWJvbCwgJ1N5bWJvbCcpO1xuLy8gMjAuMi4xLjkgTWF0aFtAQHRvU3RyaW5nVGFnXVxuc2V0VG9TdHJpbmdUYWcoTWF0aCwgJ01hdGgnLCB0cnVlKTtcbi8vIDI0LjMuMyBKU09OW0BAdG9TdHJpbmdUYWddXG5zZXRUb1N0cmluZ1RhZyhnbG9iYWwuSlNPTiwgJ0pTT04nLCB0cnVlKTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvZXM2LnN5bWJvbC5qc1xuLy8gbW9kdWxlIGlkID0gMTI0XG4vLyBtb2R1bGUgY2h1bmtzID0gMSAzIDQgNSA4IDkiLCJyZXF1aXJlKCcuL193a3MtZGVmaW5lJykoJ2FzeW5jSXRlcmF0b3InKTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvZXM3LnN5bWJvbC5hc3luYy1pdGVyYXRvci5qc1xuLy8gbW9kdWxlIGlkID0gMTI1XG4vLyBtb2R1bGUgY2h1bmtzID0gMSAzIDQgNSA4IDkiLCJyZXF1aXJlKCcuL193a3MtZGVmaW5lJykoJ29ic2VydmFibGUnKTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvZXM3LnN5bWJvbC5vYnNlcnZhYmxlLmpzXG4vLyBtb2R1bGUgaWQgPSAxMjZcbi8vIG1vZHVsZSBjaHVua3MgPSAxIDMgNCA1IDggOSIsIi8vIG9wdGlvbmFsIC8gc2ltcGxlIGNvbnRleHQgYmluZGluZ1xudmFyIGFGdW5jdGlvbiA9IHJlcXVpcmUoJy4vX2EtZnVuY3Rpb24nKTtcbm1vZHVsZS5leHBvcnRzID0gZnVuY3Rpb24oZm4sIHRoYXQsIGxlbmd0aCl7XG4gIGFGdW5jdGlvbihmbik7XG4gIGlmKHRoYXQgPT09IHVuZGVmaW5lZClyZXR1cm4gZm47XG4gIHN3aXRjaChsZW5ndGgpe1xuICAgIGNhc2UgMTogcmV0dXJuIGZ1bmN0aW9uKGEpe1xuICAgICAgcmV0dXJuIGZuLmNhbGwodGhhdCwgYSk7XG4gICAgfTtcbiAgICBjYXNlIDI6IHJldHVybiBmdW5jdGlvbihhLCBiKXtcbiAgICAgIHJldHVybiBmbi5jYWxsKHRoYXQsIGEsIGIpO1xuICAgIH07XG4gICAgY2FzZSAzOiByZXR1cm4gZnVuY3Rpb24oYSwgYiwgYyl7XG4gICAgICByZXR1cm4gZm4uY2FsbCh0aGF0LCBhLCBiLCBjKTtcbiAgICB9O1xuICB9XG4gIHJldHVybiBmdW5jdGlvbigvKiAuLi5hcmdzICovKXtcbiAgICByZXR1cm4gZm4uYXBwbHkodGhhdCwgYXJndW1lbnRzKTtcbiAgfTtcbn07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19jdHguanNcbi8vIG1vZHVsZSBpZCA9IDEzXG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDIgMyA0IDUgNiA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxNyAxOCAxOSAyMCAyMSAyMiAyMyAyNCAyNSAyNiAyNyAyOCAyOSAzMCAzMSAzMiAzMyAzNCAzNSAzNiAzNyAzOCAzOSA0MCA0MSA0MiA0MyA0NCA0NSA0NiA0NyA0OCA0OSA1MCA1MSA1MiA1MyA1NCA1NSA1NiA1NyA1OCIsIi8vIDcuMS4xIFRvUHJpbWl0aXZlKGlucHV0IFssIFByZWZlcnJlZFR5cGVdKVxudmFyIGlzT2JqZWN0ID0gcmVxdWlyZSgnLi9faXMtb2JqZWN0Jyk7XG4vLyBpbnN0ZWFkIG9mIHRoZSBFUzYgc3BlYyB2ZXJzaW9uLCB3ZSBkaWRuJ3QgaW1wbGVtZW50IEBAdG9QcmltaXRpdmUgY2FzZVxuLy8gYW5kIHRoZSBzZWNvbmQgYXJndW1lbnQgLSBmbGFnIC0gcHJlZmVycmVkIHR5cGUgaXMgYSBzdHJpbmdcbm1vZHVsZS5leHBvcnRzID0gZnVuY3Rpb24oaXQsIFMpe1xuICBpZighaXNPYmplY3QoaXQpKXJldHVybiBpdDtcbiAgdmFyIGZuLCB2YWw7XG4gIGlmKFMgJiYgdHlwZW9mIChmbiA9IGl0LnRvU3RyaW5nKSA9PSAnZnVuY3Rpb24nICYmICFpc09iamVjdCh2YWwgPSBmbi5jYWxsKGl0KSkpcmV0dXJuIHZhbDtcbiAgaWYodHlwZW9mIChmbiA9IGl0LnZhbHVlT2YpID09ICdmdW5jdGlvbicgJiYgIWlzT2JqZWN0KHZhbCA9IGZuLmNhbGwoaXQpKSlyZXR1cm4gdmFsO1xuICBpZighUyAmJiB0eXBlb2YgKGZuID0gaXQudG9TdHJpbmcpID09ICdmdW5jdGlvbicgJiYgIWlzT2JqZWN0KHZhbCA9IGZuLmNhbGwoaXQpKSlyZXR1cm4gdmFsO1xuICB0aHJvdyBUeXBlRXJyb3IoXCJDYW4ndCBjb252ZXJ0IG9iamVjdCB0byBwcmltaXRpdmUgdmFsdWVcIik7XG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fdG8tcHJpbWl0aXZlLmpzXG4vLyBtb2R1bGUgaWQgPSAxNFxuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAyIDMgNCA1IDYgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTcgMTggMTkgMjAgMjEgMjIgMjMgMjQgMjUgMjYgMjcgMjggMjkgMzAgMzEgMzIgMzMgMzQgMzUgMzYgMzcgMzggMzkgNDAgNDEgNDIgNDMgNDQgNDUgNDYgNDcgNDggNDkgNTAgNTEgNTIgNTMgNTQgNTUgNTYgNTcgNTgiLCJ2YXIgaXNPYmplY3QgPSByZXF1aXJlKCcuL19pcy1vYmplY3QnKVxuICAsIGRvY3VtZW50ID0gcmVxdWlyZSgnLi9fZ2xvYmFsJykuZG9jdW1lbnRcbiAgLy8gaW4gb2xkIElFIHR5cGVvZiBkb2N1bWVudC5jcmVhdGVFbGVtZW50IGlzICdvYmplY3QnXG4gICwgaXMgPSBpc09iamVjdChkb2N1bWVudCkgJiYgaXNPYmplY3QoZG9jdW1lbnQuY3JlYXRlRWxlbWVudCk7XG5tb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uKGl0KXtcbiAgcmV0dXJuIGlzID8gZG9jdW1lbnQuY3JlYXRlRWxlbWVudChpdCkgOiB7fTtcbn07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19kb20tY3JlYXRlLmpzXG4vLyBtb2R1bGUgaWQgPSAxNlxuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAyIDMgNCA1IDYgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTcgMTggMTkgMjAgMjEgMjIgMjMgMjQgMjUgMjYgMjcgMjggMjkgMzAgMzEgMzIgMzMgMzQgMzUgMzYgMzcgMzggMzkgNDAgNDEgNDIgNDMgNDQgNDUgNDYgNDcgNDggNDkgNTAgNTEgNTIgNTMgNTQgNTUgNTYgNTcgNTgiLCJtb2R1bGUuZXhwb3J0cyA9ICFyZXF1aXJlKCcuL19kZXNjcmlwdG9ycycpICYmICFyZXF1aXJlKCcuL19mYWlscycpKGZ1bmN0aW9uKCl7XG4gIHJldHVybiBPYmplY3QuZGVmaW5lUHJvcGVydHkocmVxdWlyZSgnLi9fZG9tLWNyZWF0ZScpKCdkaXYnKSwgJ2EnLCB7Z2V0OiBmdW5jdGlvbigpeyByZXR1cm4gNzsgfX0pLmEgIT0gNztcbn0pO1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9faWU4LWRvbS1kZWZpbmUuanNcbi8vIG1vZHVsZSBpZCA9IDE3XG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDIgMyA0IDUgNiA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxNyAxOCAxOSAyMCAyMSAyMiAyMyAyNCAyNSAyNiAyNyAyOCAyOSAzMCAzMSAzMiAzMyAzNCAzNSAzNiAzNyAzOCAzOSA0MCA0MSA0MiA0MyA0NCA0NSA0NiA0NyA0OCA0OSA1MCA1MSA1MiA1MyA1NCA1NSA1NiA1NyA1OCIsIm1vZHVsZS5leHBvcnRzID0gZnVuY3Rpb24oaXQpe1xuICBpZih0eXBlb2YgaXQgIT0gJ2Z1bmN0aW9uJyl0aHJvdyBUeXBlRXJyb3IoaXQgKyAnIGlzIG5vdCBhIGZ1bmN0aW9uIScpO1xuICByZXR1cm4gaXQ7XG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fYS1mdW5jdGlvbi5qc1xuLy8gbW9kdWxlIGlkID0gMThcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMiAzIDQgNSA2IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE3IDE4IDE5IDIwIDIxIDIyIDIzIDI0IDI1IDI2IDI3IDI4IDI5IDMwIDMxIDMyIDMzIDM0IDM1IDM2IDM3IDM4IDM5IDQwIDQxIDQyIDQzIDQ0IDQ1IDQ2IDQ3IDQ4IDQ5IDUwIDUxIDUyIDUzIDU0IDU1IDU2IDU3IDU4IiwibW9kdWxlLmV4cG9ydHMgPSB7IFwiZGVmYXVsdFwiOiByZXF1aXJlKFwiY29yZS1qcy9saWJyYXJ5L2ZuL29iamVjdC9kZWZpbmUtcHJvcGVydHlcIiksIF9fZXNNb2R1bGU6IHRydWUgfTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vYmFiZWwtcnVudGltZS9jb3JlLWpzL29iamVjdC9kZWZpbmUtcHJvcGVydHkuanNcbi8vIG1vZHVsZSBpZCA9IDE5XG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDIgMyA0IDUgNiA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxNyAxOCAxOSAyMCAyMSAyMiAyMyAyNCAyNSAyNiAyNyAyOCAyOSAzMCAzMSAzMiAzMyAzNCAzNSAzNiAzNyAzOCAzOSA0MCA0MSA0MiA0MyA0NCA0NSA0NiA0NyA0OCA0OSA1MCA1MSA1MiA1MyA1NCA1NSA1NiA1NyA1OCIsIi8qKlxuICogQ29weXJpZ2h0IHNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBQcmVzdGFTaG9wIGlzIGFuIEludGVybmF0aW9uYWwgUmVnaXN0ZXJlZCBUcmFkZW1hcmsgJiBQcm9wZXJ0eSBvZiBQcmVzdGFTaG9wIFNBXG4gKlxuICogTk9USUNFIE9GIExJQ0VOU0VcbiAqXG4gKiBUaGlzIHNvdXJjZSBmaWxlIGlzIHN1YmplY3QgdG8gdGhlIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqIHRoYXQgaXMgYnVuZGxlZCB3aXRoIHRoaXMgcGFja2FnZSBpbiB0aGUgZmlsZSBMSUNFTlNFLm1kLlxuICogSXQgaXMgYWxzbyBhdmFpbGFibGUgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIgYXQgdGhpcyBVUkw6XG4gKiBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjBcbiAqIElmIHlvdSBkaWQgbm90IHJlY2VpdmUgYSBjb3B5IG9mIHRoZSBsaWNlbnNlIGFuZCBhcmUgdW5hYmxlIHRvXG4gKiBvYnRhaW4gaXQgdGhyb3VnaCB0aGUgd29ybGQtd2lkZS13ZWIsIHBsZWFzZSBzZW5kIGFuIGVtYWlsXG4gKiB0byBsaWNlbnNlQHByZXN0YXNob3AuY29tIHNvIHdlIGNhbiBzZW5kIHlvdSBhIGNvcHkgaW1tZWRpYXRlbHkuXG4gKlxuICogRElTQ0xBSU1FUlxuICpcbiAqIERvIG5vdCBlZGl0IG9yIGFkZCB0byB0aGlzIGZpbGUgaWYgeW91IHdpc2ggdG8gdXBncmFkZSBQcmVzdGFTaG9wIHRvIG5ld2VyXG4gKiB2ZXJzaW9ucyBpbiB0aGUgZnV0dXJlLiBJZiB5b3Ugd2lzaCB0byBjdXN0b21pemUgUHJlc3RhU2hvcCBmb3IgeW91clxuICogbmVlZHMgcGxlYXNlIHJlZmVyIHRvIGh0dHBzOi8vZGV2ZG9jcy5wcmVzdGFzaG9wLmNvbS8gZm9yIG1vcmUgaW5mb3JtYXRpb24uXG4gKlxuICogQGF1dGhvciAgICBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnMgPGNvbnRhY3RAcHJlc3Rhc2hvcC5jb20+XG4gKiBAY29weXJpZ2h0IFNpbmNlIDIwMDcgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzXG4gKiBAbGljZW5zZSAgIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMCBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKi9cblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG52YXIgQk9FdmVudCA9IHtcbiAgb246IGZ1bmN0aW9uKGV2ZW50TmFtZSwgY2FsbGJhY2ssIGNvbnRleHQpIHtcblxuICAgIGRvY3VtZW50LmFkZEV2ZW50TGlzdGVuZXIoZXZlbnROYW1lLCBmdW5jdGlvbihldmVudCkge1xuICAgICAgaWYgKHR5cGVvZiBjb250ZXh0ICE9PSAndW5kZWZpbmVkJykge1xuICAgICAgICBjYWxsYmFjay5jYWxsKGNvbnRleHQsIGV2ZW50KTtcbiAgICAgIH0gZWxzZSB7XG4gICAgICAgIGNhbGxiYWNrKGV2ZW50KTtcbiAgICAgIH1cbiAgICB9KTtcbiAgfSxcblxuICBlbWl0RXZlbnQ6IGZ1bmN0aW9uKGV2ZW50TmFtZSwgZXZlbnRUeXBlKSB7XG4gICAgdmFyIF9ldmVudCA9IGRvY3VtZW50LmNyZWF0ZUV2ZW50KGV2ZW50VHlwZSk7XG4gICAgLy8gdHJ1ZSB2YWx1ZXMgc3RhbmQgZm9yOiBjYW4gYnViYmxlLCBhbmQgaXMgY2FuY2VsbGFibGVcbiAgICBfZXZlbnQuaW5pdEV2ZW50KGV2ZW50TmFtZSwgdHJ1ZSwgdHJ1ZSk7XG4gICAgZG9jdW1lbnQuZGlzcGF0Y2hFdmVudChfZXZlbnQpO1xuICB9XG59O1xuXG5cbi8qKlxuICogQ2xhc3MgaXMgcmVzcG9uc2libGUgZm9yIGhhbmRsaW5nIE1vZHVsZSBDYXJkIGJlaGF2aW9yXG4gKlxuICogVGhpcyBpcyBhIHBvcnQgb2YgYWRtaW4tZGV2L3RoZW1lcy9kZWZhdWx0L2pzL2J1bmRsZS9tb2R1bGUvbW9kdWxlX2NhcmQuanNcbiAqL1xuZXhwb3J0IGRlZmF1bHQgY2xhc3MgTW9kdWxlQ2FyZCB7XG5cbiAgY29uc3RydWN0b3IoKSB7XG4gICAgLyogU2VsZWN0b3JzIGZvciBtb2R1bGUgYWN0aW9uIGxpbmtzICh1bmluc3RhbGwsIHJlc2V0LCBldGMuLi4pIHRvIGFkZCBhIGNvbmZpcm0gcG9waW4gKi9cbiAgICB0aGlzLm1vZHVsZUFjdGlvbk1lbnVMaW5rU2VsZWN0b3IgPSAnYnV0dG9uLm1vZHVsZV9hY3Rpb25fbWVudV8nO1xuICAgIHRoaXMubW9kdWxlQWN0aW9uTWVudUluc3RhbGxMaW5rU2VsZWN0b3IgPSAnYnV0dG9uLm1vZHVsZV9hY3Rpb25fbWVudV9pbnN0YWxsJztcbiAgICB0aGlzLm1vZHVsZUFjdGlvbk1lbnVFbmFibGVMaW5rU2VsZWN0b3IgPSAnYnV0dG9uLm1vZHVsZV9hY3Rpb25fbWVudV9lbmFibGUnO1xuICAgIHRoaXMubW9kdWxlQWN0aW9uTWVudVVuaW5zdGFsbExpbmtTZWxlY3RvciA9ICdidXR0b24ubW9kdWxlX2FjdGlvbl9tZW51X3VuaW5zdGFsbCc7XG4gICAgdGhpcy5tb2R1bGVBY3Rpb25NZW51RGlzYWJsZUxpbmtTZWxlY3RvciA9ICdidXR0b24ubW9kdWxlX2FjdGlvbl9tZW51X2Rpc2FibGUnO1xuICAgIHRoaXMubW9kdWxlQWN0aW9uTWVudUVuYWJsZU1vYmlsZUxpbmtTZWxlY3RvciA9ICdidXR0b24ubW9kdWxlX2FjdGlvbl9tZW51X2VuYWJsZV9tb2JpbGUnO1xuICAgIHRoaXMubW9kdWxlQWN0aW9uTWVudURpc2FibGVNb2JpbGVMaW5rU2VsZWN0b3IgPSAnYnV0dG9uLm1vZHVsZV9hY3Rpb25fbWVudV9kaXNhYmxlX21vYmlsZSc7XG4gICAgdGhpcy5tb2R1bGVBY3Rpb25NZW51UmVzZXRMaW5rU2VsZWN0b3IgPSAnYnV0dG9uLm1vZHVsZV9hY3Rpb25fbWVudV9yZXNldCc7XG4gICAgdGhpcy5tb2R1bGVBY3Rpb25NZW51VXBkYXRlTGlua1NlbGVjdG9yID0gJ2J1dHRvbi5tb2R1bGVfYWN0aW9uX21lbnVfdXBncmFkZSc7XG4gICAgdGhpcy5tb2R1bGVJdGVtTGlzdFNlbGVjdG9yID0gJy5tb2R1bGUtaXRlbS1saXN0JztcbiAgICB0aGlzLm1vZHVsZUl0ZW1HcmlkU2VsZWN0b3IgPSAnLm1vZHVsZS1pdGVtLWdyaWQnO1xuICAgIHRoaXMubW9kdWxlSXRlbUFjdGlvbnNTZWxlY3RvciA9ICcubW9kdWxlLWFjdGlvbnMnO1xuXG4gICAgLyogU2VsZWN0b3JzIG9ubHkgZm9yIG1vZGFsIGJ1dHRvbnMgKi9cbiAgICB0aGlzLm1vZHVsZUFjdGlvbk1vZGFsRGlzYWJsZUxpbmtTZWxlY3RvciA9ICdhLm1vZHVsZV9hY3Rpb25fbW9kYWxfZGlzYWJsZSc7XG4gICAgdGhpcy5tb2R1bGVBY3Rpb25Nb2RhbFJlc2V0TGlua1NlbGVjdG9yID0gJ2EubW9kdWxlX2FjdGlvbl9tb2RhbF9yZXNldCc7XG4gICAgdGhpcy5tb2R1bGVBY3Rpb25Nb2RhbFVuaW5zdGFsbExpbmtTZWxlY3RvciA9ICdhLm1vZHVsZV9hY3Rpb25fbW9kYWxfdW5pbnN0YWxsJztcbiAgICB0aGlzLmZvcmNlRGVsZXRpb25PcHRpb24gPSAnI2ZvcmNlX2RlbGV0aW9uJztcblxuICAgIHRoaXMuaW5pdEFjdGlvbkJ1dHRvbnMoKTtcbiAgfVxuXG4gIGluaXRBY3Rpb25CdXR0b25zKCkge1xuICAgIGNvbnN0IHNlbGYgPSB0aGlzO1xuXG4gICAgJChkb2N1bWVudCkub24oJ2NsaWNrJywgdGhpcy5mb3JjZURlbGV0aW9uT3B0aW9uLCBmdW5jdGlvbiAoKSB7XG4gICAgICBjb25zdCBidG4gPSAkKHNlbGYubW9kdWxlQWN0aW9uTW9kYWxVbmluc3RhbGxMaW5rU2VsZWN0b3IsICQoXCJkaXYubW9kdWxlLWl0ZW0tbGlzdFtkYXRhLXRlY2gtbmFtZT0nXCIgKyAkKHRoaXMpLmF0dHIoXCJkYXRhLXRlY2gtbmFtZVwiKSArIFwiJ11cIikpO1xuICAgICAgaWYgKCQodGhpcykucHJvcCgnY2hlY2tlZCcpID09PSB0cnVlKSB7XG4gICAgICAgIGJ0bi5hdHRyKCdkYXRhLWRlbGV0aW9uJywgJ3RydWUnKTtcbiAgICAgIH0gZWxzZSB7XG4gICAgICAgIGJ0bi5yZW1vdmVBdHRyKCdkYXRhLWRlbGV0aW9uJyk7XG4gICAgICB9XG4gICAgfSk7XG5cbiAgICAkKGRvY3VtZW50KS5vbignY2xpY2snLCB0aGlzLm1vZHVsZUFjdGlvbk1lbnVJbnN0YWxsTGlua1NlbGVjdG9yLCBmdW5jdGlvbiAoKSB7XG4gICAgICBpZiAoJChcIiNtb2RhbC1wcmVzdGF0cnVzdFwiKS5sZW5ndGgpIHtcbiAgICAgICAgJChcIiNtb2RhbC1wcmVzdGF0cnVzdFwiKS5tb2RhbCgnaGlkZScpO1xuICAgICAgfVxuICAgICAgcmV0dXJuIHNlbGYuX2Rpc3BhdGNoUHJlRXZlbnQoJ2luc3RhbGwnLCB0aGlzKSAmJiBzZWxmLl9jb25maXJtQWN0aW9uKCdpbnN0YWxsJywgdGhpcykgJiYgc2VsZi5fcmVxdWVzdFRvQ29udHJvbGxlcignaW5zdGFsbCcsICQodGhpcykpO1xuICAgIH0pO1xuICAgICQoZG9jdW1lbnQpLm9uKCdjbGljaycsIHRoaXMubW9kdWxlQWN0aW9uTWVudUVuYWJsZUxpbmtTZWxlY3RvciwgZnVuY3Rpb24gKCkge1xuICAgICAgcmV0dXJuIHNlbGYuX2Rpc3BhdGNoUHJlRXZlbnQoJ2VuYWJsZScsIHRoaXMpICYmIHNlbGYuX2NvbmZpcm1BY3Rpb24oJ2VuYWJsZScsIHRoaXMpICYmIHNlbGYuX3JlcXVlc3RUb0NvbnRyb2xsZXIoJ2VuYWJsZScsICQodGhpcykpO1xuICAgIH0pO1xuICAgICQoZG9jdW1lbnQpLm9uKCdjbGljaycsIHRoaXMubW9kdWxlQWN0aW9uTWVudVVuaW5zdGFsbExpbmtTZWxlY3RvciwgZnVuY3Rpb24gKCkge1xuICAgICAgcmV0dXJuIHNlbGYuX2Rpc3BhdGNoUHJlRXZlbnQoJ3VuaW5zdGFsbCcsIHRoaXMpICYmIHNlbGYuX2NvbmZpcm1BY3Rpb24oJ3VuaW5zdGFsbCcsIHRoaXMpICYmIHNlbGYuX3JlcXVlc3RUb0NvbnRyb2xsZXIoJ3VuaW5zdGFsbCcsICQodGhpcykpO1xuICAgIH0pO1xuICAgICQoZG9jdW1lbnQpLm9uKCdjbGljaycsIHRoaXMubW9kdWxlQWN0aW9uTWVudURpc2FibGVMaW5rU2VsZWN0b3IsIGZ1bmN0aW9uICgpIHtcbiAgICAgIHJldHVybiBzZWxmLl9kaXNwYXRjaFByZUV2ZW50KCdkaXNhYmxlJywgdGhpcykgJiYgc2VsZi5fY29uZmlybUFjdGlvbignZGlzYWJsZScsIHRoaXMpICYmIHNlbGYuX3JlcXVlc3RUb0NvbnRyb2xsZXIoJ2Rpc2FibGUnLCAkKHRoaXMpKTtcbiAgICB9KTtcbiAgICAkKGRvY3VtZW50KS5vbignY2xpY2snLCB0aGlzLm1vZHVsZUFjdGlvbk1lbnVFbmFibGVNb2JpbGVMaW5rU2VsZWN0b3IsIGZ1bmN0aW9uICgpIHtcbiAgICAgIHJldHVybiBzZWxmLl9kaXNwYXRjaFByZUV2ZW50KCdlbmFibGVfbW9iaWxlJywgdGhpcykgJiYgc2VsZi5fY29uZmlybUFjdGlvbignZW5hYmxlX21vYmlsZScsIHRoaXMpICYmIHNlbGYuX3JlcXVlc3RUb0NvbnRyb2xsZXIoJ2VuYWJsZV9tb2JpbGUnLCAkKHRoaXMpKTtcbiAgICB9KTtcbiAgICAkKGRvY3VtZW50KS5vbignY2xpY2snLCB0aGlzLm1vZHVsZUFjdGlvbk1lbnVEaXNhYmxlTW9iaWxlTGlua1NlbGVjdG9yLCBmdW5jdGlvbiAoKSB7XG4gICAgICByZXR1cm4gc2VsZi5fZGlzcGF0Y2hQcmVFdmVudCgnZGlzYWJsZV9tb2JpbGUnLCB0aGlzKSAmJiBzZWxmLl9jb25maXJtQWN0aW9uKCdkaXNhYmxlX21vYmlsZScsIHRoaXMpICYmIHNlbGYuX3JlcXVlc3RUb0NvbnRyb2xsZXIoJ2Rpc2FibGVfbW9iaWxlJywgJCh0aGlzKSk7XG4gICAgfSk7XG4gICAgJChkb2N1bWVudCkub24oJ2NsaWNrJywgdGhpcy5tb2R1bGVBY3Rpb25NZW51UmVzZXRMaW5rU2VsZWN0b3IsIGZ1bmN0aW9uICgpIHtcbiAgICAgIHJldHVybiBzZWxmLl9kaXNwYXRjaFByZUV2ZW50KCdyZXNldCcsIHRoaXMpICYmIHNlbGYuX2NvbmZpcm1BY3Rpb24oJ3Jlc2V0JywgdGhpcykgJiYgc2VsZi5fcmVxdWVzdFRvQ29udHJvbGxlcigncmVzZXQnLCAkKHRoaXMpKTtcbiAgICB9KTtcbiAgICAkKGRvY3VtZW50KS5vbignY2xpY2snLCB0aGlzLm1vZHVsZUFjdGlvbk1lbnVVcGRhdGVMaW5rU2VsZWN0b3IsIGZ1bmN0aW9uICgpIHtcbiAgICAgIHJldHVybiBzZWxmLl9kaXNwYXRjaFByZUV2ZW50KCd1cGRhdGUnLCB0aGlzKSAmJiBzZWxmLl9jb25maXJtQWN0aW9uKCd1cGRhdGUnLCB0aGlzKSAmJiBzZWxmLl9yZXF1ZXN0VG9Db250cm9sbGVyKCd1cGRhdGUnLCAkKHRoaXMpKTtcbiAgICB9KTtcblxuICAgICQoZG9jdW1lbnQpLm9uKCdjbGljaycsIHRoaXMubW9kdWxlQWN0aW9uTW9kYWxEaXNhYmxlTGlua1NlbGVjdG9yLCBmdW5jdGlvbiAoKSB7XG4gICAgICByZXR1cm4gc2VsZi5fcmVxdWVzdFRvQ29udHJvbGxlcignZGlzYWJsZScsICQoc2VsZi5tb2R1bGVBY3Rpb25NZW51RGlzYWJsZUxpbmtTZWxlY3RvciwgJChcImRpdi5tb2R1bGUtaXRlbS1saXN0W2RhdGEtdGVjaC1uYW1lPSdcIiArICQodGhpcykuYXR0cihcImRhdGEtdGVjaC1uYW1lXCIpICsgXCInXVwiKSkpO1xuICAgIH0pO1xuICAgICQoZG9jdW1lbnQpLm9uKCdjbGljaycsIHRoaXMubW9kdWxlQWN0aW9uTW9kYWxSZXNldExpbmtTZWxlY3RvciwgZnVuY3Rpb24gKCkge1xuICAgICAgcmV0dXJuIHNlbGYuX3JlcXVlc3RUb0NvbnRyb2xsZXIoJ3Jlc2V0JywgJChzZWxmLm1vZHVsZUFjdGlvbk1lbnVSZXNldExpbmtTZWxlY3RvciwgJChcImRpdi5tb2R1bGUtaXRlbS1saXN0W2RhdGEtdGVjaC1uYW1lPSdcIiArICQodGhpcykuYXR0cihcImRhdGEtdGVjaC1uYW1lXCIpICsgXCInXVwiKSkpO1xuICAgIH0pO1xuICAgICQoZG9jdW1lbnQpLm9uKCdjbGljaycsIHRoaXMubW9kdWxlQWN0aW9uTW9kYWxVbmluc3RhbGxMaW5rU2VsZWN0b3IsIGZ1bmN0aW9uIChlKSB7XG4gICAgICAkKGUudGFyZ2V0KS5wYXJlbnRzKCcubW9kYWwnKS5vbignaGlkZGVuLmJzLm1vZGFsJywgZnVuY3Rpb24oZXZlbnQpIHtcbiAgICAgICAgcmV0dXJuIHNlbGYuX3JlcXVlc3RUb0NvbnRyb2xsZXIoXG4gICAgICAgICAgJ3VuaW5zdGFsbCcsXG4gICAgICAgICAgJChcbiAgICAgICAgICAgIHNlbGYubW9kdWxlQWN0aW9uTWVudVVuaW5zdGFsbExpbmtTZWxlY3RvcixcbiAgICAgICAgICAgICQoXCJkaXYubW9kdWxlLWl0ZW0tbGlzdFtkYXRhLXRlY2gtbmFtZT0nXCIgKyAkKGUudGFyZ2V0KS5hdHRyKFwiZGF0YS10ZWNoLW5hbWVcIikgKyBcIiddXCIpXG4gICAgICAgICAgKSxcbiAgICAgICAgICAkKGUudGFyZ2V0KS5hdHRyKFwiZGF0YS1kZWxldGlvblwiKVxuICAgICAgICApO1xuICAgICAgfS5iaW5kKGUpKTtcbiAgICB9KTtcbiAgfTtcblxuICBfZ2V0TW9kdWxlSXRlbVNlbGVjdG9yKCkge1xuICAgIGlmICgkKHRoaXMubW9kdWxlSXRlbUxpc3RTZWxlY3RvcikubGVuZ3RoKSB7XG4gICAgICByZXR1cm4gdGhpcy5tb2R1bGVJdGVtTGlzdFNlbGVjdG9yO1xuICAgIH0gZWxzZSB7XG4gICAgICByZXR1cm4gdGhpcy5tb2R1bGVJdGVtR3JpZFNlbGVjdG9yO1xuICAgIH1cbiAgfTtcblxuICBfY29uZmlybUFjdGlvbihhY3Rpb24sIGVsZW1lbnQpIHtcbiAgICB2YXIgbW9kYWwgPSAkKCcjJyArICQoZWxlbWVudCkuZGF0YSgnY29uZmlybV9tb2RhbCcpKTtcbiAgICBpZiAobW9kYWwubGVuZ3RoICE9IDEpIHtcbiAgICAgIHJldHVybiB0cnVlO1xuICAgIH1cbiAgICBtb2RhbC5maXJzdCgpLm1vZGFsKCdzaG93Jyk7XG5cbiAgICByZXR1cm4gZmFsc2U7IC8vIGRvIG5vdCBhbGxvdyBhLmhyZWYgdG8gcmVsb2FkIHRoZSBwYWdlLiBUaGUgY29uZmlybSBtb2RhbCBkaWFsb2cgd2lsbCBkbyBpdCBhc3luYyBpZiBuZWVkZWQuXG4gIH07XG5cbiAgLyoqXG4gICAqIFVwZGF0ZSB0aGUgY29udGVudCBvZiBhIG1vZGFsIGFza2luZyBhIGNvbmZpcm1hdGlvbiBmb3IgUHJlc3RhVHJ1c3QgYW5kIG9wZW4gaXRcbiAgICpcbiAgICogQHBhcmFtIHthcnJheX0gcmVzdWx0IGNvbnRhaW5pbmcgbW9kdWxlIGRhdGFcbiAgICogQHJldHVybiB7dm9pZH1cbiAgICovXG4gIF9jb25maXJtUHJlc3RhVHJ1c3QocmVzdWx0KSB7XG4gICAgdmFyIHRoYXQgPSB0aGlzO1xuICAgIHZhciBtb2RhbCA9IHRoaXMuX3JlcGxhY2VQcmVzdGFUcnVzdFBsYWNlaG9sZGVycyhyZXN1bHQpO1xuXG4gICAgbW9kYWwuZmluZChcIi5wc3RydXN0LWluc3RhbGxcIikub2ZmKCdjbGljaycpLm9uKCdjbGljaycsIGZ1bmN0aW9uKCkge1xuICAgICAgLy8gRmluZCByZWxhdGVkIGZvcm0sIHVwZGF0ZSBpdCBhbmQgc3VibWl0IGl0XG4gICAgICB2YXIgaW5zdGFsbF9idXR0b24gPSAkKHRoYXQubW9kdWxlQWN0aW9uTWVudUluc3RhbGxMaW5rU2VsZWN0b3IsICcubW9kdWxlLWl0ZW1bZGF0YS10ZWNoLW5hbWU9XCInICsgcmVzdWx0Lm1vZHVsZS5hdHRyaWJ1dGVzLm5hbWUgKyAnXCJdJyk7XG4gICAgICB2YXIgZm9ybSA9IGluc3RhbGxfYnV0dG9uLnBhcmVudChcImZvcm1cIik7XG4gICAgICAkKCc8aW5wdXQ+JykuYXR0cih7XG4gICAgICAgIHR5cGU6ICdoaWRkZW4nLFxuICAgICAgICB2YWx1ZTogJzEnLFxuICAgICAgICBuYW1lOiAnYWN0aW9uUGFyYW1zW2NvbmZpcm1QcmVzdGFUcnVzdF0nXG4gICAgICB9KS5hcHBlbmRUbyhmb3JtKTtcblxuICAgICAgaW5zdGFsbF9idXR0b24uY2xpY2soKTtcbiAgICAgIG1vZGFsLm1vZGFsKCdoaWRlJyk7XG4gICAgfSk7XG5cbiAgICBtb2RhbC5tb2RhbCgpO1xuICB9O1xuXG4gIF9yZXBsYWNlUHJlc3RhVHJ1c3RQbGFjZWhvbGRlcnMocmVzdWx0KSB7XG4gICAgdmFyIG1vZGFsID0gJChcIiNtb2RhbC1wcmVzdGF0cnVzdFwiKTtcbiAgICB2YXIgbW9kdWxlID0gcmVzdWx0Lm1vZHVsZS5hdHRyaWJ1dGVzO1xuXG4gICAgaWYgKHJlc3VsdC5jb25maXJtYXRpb25fc3ViamVjdCAhPT0gJ1ByZXN0YVRydXN0JyB8fCAhbW9kYWwubGVuZ3RoKSB7XG4gICAgICByZXR1cm47XG4gICAgfVxuXG4gICAgdmFyIGFsZXJ0Q2xhc3MgPSBtb2R1bGUucHJlc3RhdHJ1c3Quc3RhdHVzID8gJ3N1Y2Nlc3MnIDogJ3dhcm5pbmcnO1xuXG4gICAgaWYgKG1vZHVsZS5wcmVzdGF0cnVzdC5jaGVja19saXN0LnByb3BlcnR5KSB7XG4gICAgICBtb2RhbC5maW5kKFwiI3BzdHJ1c3QtYnRuLXByb3BlcnR5LW9rXCIpLnNob3coKTtcbiAgICAgIG1vZGFsLmZpbmQoXCIjcHN0cnVzdC1idG4tcHJvcGVydHktbm9rXCIpLmhpZGUoKTtcbiAgICB9IGVsc2Uge1xuICAgICAgbW9kYWwuZmluZChcIiNwc3RydXN0LWJ0bi1wcm9wZXJ0eS1va1wiKS5oaWRlKCk7XG4gICAgICBtb2RhbC5maW5kKFwiI3BzdHJ1c3QtYnRuLXByb3BlcnR5LW5va1wiKS5zaG93KCk7XG4gICAgICBtb2RhbC5maW5kKFwiI3BzdHJ1c3QtYnV5XCIpLmF0dHIoXCJocmVmXCIsIG1vZHVsZS51cmwpLnRvZ2dsZShtb2R1bGUudXJsICE9PSBudWxsKTtcbiAgICB9XG5cbiAgICBtb2RhbC5maW5kKFwiI3BzdHJ1c3QtaW1nXCIpLmF0dHIoe3NyYzogbW9kdWxlLmltZywgYWx0OiBtb2R1bGUubmFtZX0pO1xuICAgIG1vZGFsLmZpbmQoXCIjcHN0cnVzdC1uYW1lXCIpLnRleHQobW9kdWxlLmRpc3BsYXlOYW1lKTtcbiAgICBtb2RhbC5maW5kKFwiI3BzdHJ1c3QtYXV0aG9yXCIpLnRleHQobW9kdWxlLmF1dGhvcik7XG4gICAgbW9kYWwuZmluZChcIiNwc3RydXN0LWxhYmVsXCIpLmF0dHIoXCJjbGFzc1wiLCBcInRleHQtXCIgKyBhbGVydENsYXNzKS50ZXh0KG1vZHVsZS5wcmVzdGF0cnVzdC5zdGF0dXMgPyAnT0snIDogJ0tPJyk7XG4gICAgbW9kYWwuZmluZChcIiNwc3RydXN0LW1lc3NhZ2VcIikuYXR0cihcImNsYXNzXCIsIFwiYWxlcnQgYWxlcnQtXCIrYWxlcnRDbGFzcyk7XG4gICAgbW9kYWwuZmluZChcIiNwc3RydXN0LW1lc3NhZ2UgPiBwXCIpLnRleHQobW9kdWxlLnByZXN0YXRydXN0Lm1lc3NhZ2UpO1xuXG4gICAgcmV0dXJuIG1vZGFsO1xuICB9XG5cbiAgX2Rpc3BhdGNoUHJlRXZlbnQoYWN0aW9uLCBlbGVtZW50KSB7XG4gICAgdmFyIGV2ZW50ID0galF1ZXJ5LkV2ZW50KCdtb2R1bGVfY2FyZF9hY3Rpb25fZXZlbnQnKTtcblxuICAgICQoZWxlbWVudCkudHJpZ2dlcihldmVudCwgW2FjdGlvbl0pO1xuICAgIGlmIChldmVudC5pc1Byb3BhZ2F0aW9uU3RvcHBlZCgpICE9PSBmYWxzZSB8fCBldmVudC5pc0ltbWVkaWF0ZVByb3BhZ2F0aW9uU3RvcHBlZCgpICE9PSBmYWxzZSkge1xuICAgICAgcmV0dXJuIGZhbHNlOyAvLyBpZiBhbGwgaGFuZGxlcnMgaGF2ZSBub3QgYmVlbiBjYWxsZWQsIHRoZW4gc3RvcCBwcm9wYWdhdGlvbiBvZiB0aGUgY2xpY2sgZXZlbnQuXG4gICAgfVxuXG4gICAgcmV0dXJuIChldmVudC5yZXN1bHQgIT09IGZhbHNlKTsgLy8gZXhwbGljaXQgZmFsc2UgbXVzdCBiZSBzZXQgZnJvbSBoYW5kbGVycyB0byBzdG9wIHByb3BhZ2F0aW9uIG9mIHRoZSBjbGljayBldmVudC5cbiAgfTtcblxuICBfcmVxdWVzdFRvQ29udHJvbGxlcihhY3Rpb24sIGVsZW1lbnQsIGZvcmNlRGVsZXRpb24sIGRpc2FibGVDYWNoZUNsZWFyLCBjYWxsYmFjaykge1xuICAgIHZhciBzZWxmID0gdGhpcztcbiAgICB2YXIganFFbGVtZW50T2JqID0gZWxlbWVudC5jbG9zZXN0KHRoaXMubW9kdWxlSXRlbUFjdGlvbnNTZWxlY3Rvcik7XG4gICAgdmFyIGZvcm0gPSBlbGVtZW50LmNsb3Nlc3QoXCJmb3JtXCIpO1xuICAgIHZhciBzcGlubmVyT2JqID0gJChcIjxidXR0b24gY2xhc3M9XFxcImJ0bi1wcmltYXJ5LXJldmVyc2Ugb25jbGljayB1bmJpbmQgc3Bpbm5lciBcXFwiPjwvYnV0dG9uPlwiKTtcbiAgICB2YXIgdXJsID0gXCIvL1wiICsgd2luZG93LmxvY2F0aW9uLmhvc3QgKyBmb3JtLmF0dHIoXCJhY3Rpb25cIik7XG4gICAgdmFyIGFjdGlvblBhcmFtcyA9IGZvcm0uc2VyaWFsaXplQXJyYXkoKTtcblxuICAgIGlmIChmb3JjZURlbGV0aW9uID09PSBcInRydWVcIiB8fCBmb3JjZURlbGV0aW9uID09PSB0cnVlKSB7XG4gICAgICBhY3Rpb25QYXJhbXMucHVzaCh7bmFtZTogXCJhY3Rpb25QYXJhbXNbZGVsZXRpb25dXCIsIHZhbHVlOiB0cnVlfSk7XG4gICAgfVxuICAgIGlmIChkaXNhYmxlQ2FjaGVDbGVhciA9PT0gXCJ0cnVlXCIgfHwgZGlzYWJsZUNhY2hlQ2xlYXIgPT09IHRydWUpIHtcbiAgICAgIGFjdGlvblBhcmFtcy5wdXNoKHtuYW1lOiBcImFjdGlvblBhcmFtc1tjYWNoZUNsZWFyRW5hYmxlZF1cIiwgdmFsdWU6IDB9KTtcbiAgICB9XG5cbiAgICAkLmFqYXgoe1xuICAgICAgdXJsOiB1cmwsXG4gICAgICBkYXRhVHlwZTogJ2pzb24nLFxuICAgICAgbWV0aG9kOiAnUE9TVCcsXG4gICAgICBkYXRhOiBhY3Rpb25QYXJhbXMsXG4gICAgICBiZWZvcmVTZW5kOiBmdW5jdGlvbiAoKSB7XG4gICAgICAgIGpxRWxlbWVudE9iai5oaWRlKCk7XG4gICAgICAgIGpxRWxlbWVudE9iai5hZnRlcihzcGlubmVyT2JqKTtcbiAgICAgIH1cbiAgICB9KS5kb25lKGZ1bmN0aW9uIChyZXN1bHQpIHtcbiAgICAgIGlmICh0eXBlb2YgcmVzdWx0ID09PSB1bmRlZmluZWQpIHtcbiAgICAgICAgJC5ncm93bC5lcnJvcih7bWVzc2FnZTogXCJObyBhbnN3ZXIgcmVjZWl2ZWQgZnJvbSBzZXJ2ZXJcIn0pO1xuICAgICAgICByZXR1cm47XG4gICAgICB9XG5cbiAgICAgIGlmICh0eXBlb2YgcmVzdWx0LnN0YXR1cyAhPT0gJ3VuZGVmaW5lZCcgJiYgcmVzdWx0LnN0YXR1cyA9PT0gZmFsc2UpIHtcbiAgICAgICAgJC5ncm93bC5lcnJvcih7bWVzc2FnZTogcmVzdWx0Lm1zZ30pO1xuICAgICAgICByZXR1cm47XG4gICAgICB9XG5cbiAgICAgIHZhciBtb2R1bGVUZWNoTmFtZSA9IE9iamVjdC5rZXlzKHJlc3VsdClbMF07XG5cbiAgICAgIGlmIChyZXN1bHRbbW9kdWxlVGVjaE5hbWVdLnN0YXR1cyA9PT0gZmFsc2UpIHtcbiAgICAgICAgaWYgKHR5cGVvZiByZXN1bHRbbW9kdWxlVGVjaE5hbWVdLmNvbmZpcm1hdGlvbl9zdWJqZWN0ICE9PSAndW5kZWZpbmVkJykge1xuICAgICAgICAgIHNlbGYuX2NvbmZpcm1QcmVzdGFUcnVzdChyZXN1bHRbbW9kdWxlVGVjaE5hbWVdKTtcbiAgICAgICAgfVxuXG4gICAgICAgICQuZ3Jvd2wuZXJyb3Ioe21lc3NhZ2U6IHJlc3VsdFttb2R1bGVUZWNoTmFtZV0ubXNnfSk7XG4gICAgICAgIHJldHVybjtcbiAgICAgIH1cblxuICAgICAgJC5ncm93bC5ub3RpY2Uoe21lc3NhZ2U6IHJlc3VsdFttb2R1bGVUZWNoTmFtZV0ubXNnfSk7XG5cbiAgICAgIHZhciBhbHRlcmVkU2VsZWN0b3IgPSBzZWxmLl9nZXRNb2R1bGVJdGVtU2VsZWN0b3IoKS5yZXBsYWNlKCcuJywgJycpO1xuICAgICAgdmFyIG1haW5FbGVtZW50ID0gbnVsbDtcblxuICAgICAgaWYgKGFjdGlvbiA9PSBcInVuaW5zdGFsbFwiKSB7XG4gICAgICAgIG1haW5FbGVtZW50ID0ganFFbGVtZW50T2JqLmNsb3Nlc3QoJy4nICsgYWx0ZXJlZFNlbGVjdG9yKTtcbiAgICAgICAgbWFpbkVsZW1lbnQucmVtb3ZlKCk7XG5cbiAgICAgICAgQk9FdmVudC5lbWl0RXZlbnQoXCJNb2R1bGUgVW5pbnN0YWxsZWRcIiwgXCJDdXN0b21FdmVudFwiKTtcbiAgICAgIH0gZWxzZSBpZiAoYWN0aW9uID09IFwiZGlzYWJsZVwiKSB7XG4gICAgICAgIG1haW5FbGVtZW50ID0ganFFbGVtZW50T2JqLmNsb3Nlc3QoJy4nICsgYWx0ZXJlZFNlbGVjdG9yKTtcbiAgICAgICAgbWFpbkVsZW1lbnQuYWRkQ2xhc3MoYWx0ZXJlZFNlbGVjdG9yICsgJy1pc05vdEFjdGl2ZScpO1xuICAgICAgICBtYWluRWxlbWVudC5hdHRyKCdkYXRhLWFjdGl2ZScsICcwJyk7XG5cbiAgICAgICAgQk9FdmVudC5lbWl0RXZlbnQoXCJNb2R1bGUgRGlzYWJsZWRcIiwgXCJDdXN0b21FdmVudFwiKTtcbiAgICAgIH0gZWxzZSBpZiAoYWN0aW9uID09IFwiZW5hYmxlXCIpIHtcbiAgICAgICAgbWFpbkVsZW1lbnQgPSBqcUVsZW1lbnRPYmouY2xvc2VzdCgnLicgKyBhbHRlcmVkU2VsZWN0b3IpO1xuICAgICAgICBtYWluRWxlbWVudC5yZW1vdmVDbGFzcyhhbHRlcmVkU2VsZWN0b3IgKyAnLWlzTm90QWN0aXZlJyk7XG4gICAgICAgIG1haW5FbGVtZW50LmF0dHIoJ2RhdGEtYWN0aXZlJywgJzEnKTtcblxuICAgICAgICBCT0V2ZW50LmVtaXRFdmVudChcIk1vZHVsZSBFbmFibGVkXCIsIFwiQ3VzdG9tRXZlbnRcIik7XG4gICAgICB9XG5cbiAgICAgIGpxRWxlbWVudE9iai5yZXBsYWNlV2l0aChyZXN1bHRbbW9kdWxlVGVjaE5hbWVdLmFjdGlvbl9tZW51X2h0bWwpO1xuICAgIH0pLmZhaWwoZnVuY3Rpb24oKSB7XG4gICAgICBjb25zdCBtb2R1bGVJdGVtID0ganFFbGVtZW50T2JqLmNsb3Nlc3QoJ21vZHVsZS1pdGVtLWxpc3QnKTtcbiAgICAgIGNvbnN0IHRlY2hOYW1lID0gbW9kdWxlSXRlbS5kYXRhKCd0ZWNoTmFtZScpO1xuICAgICAgJC5ncm93bC5lcnJvcih7bWVzc2FnZTogXCJDb3VsZCBub3QgcGVyZm9ybSBhY3Rpb24gXCIrYWN0aW9uK1wiIGZvciBtb2R1bGUgXCIrdGVjaE5hbWV9KTtcbiAgICB9KS5hbHdheXMoZnVuY3Rpb24gKCkge1xuICAgICAganFFbGVtZW50T2JqLmZhZGVJbigpO1xuICAgICAgc3Bpbm5lck9iai5yZW1vdmUoKTtcbiAgICAgIGlmIChjYWxsYmFjaykge1xuICAgICAgICBjYWxsYmFjaygpO1xuICAgICAgfVxuICAgIH0pO1xuXG4gICAgcmV0dXJuIGZhbHNlO1xuICB9O1xufVxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvY29tcG9uZW50cy9tb2R1bGUtY2FyZC5qcyIsIi8vIFRoYW5rJ3MgSUU4IGZvciBoaXMgZnVubnkgZGVmaW5lUHJvcGVydHlcbm1vZHVsZS5leHBvcnRzID0gIXJlcXVpcmUoJy4vX2ZhaWxzJykoZnVuY3Rpb24oKXtcbiAgcmV0dXJuIE9iamVjdC5kZWZpbmVQcm9wZXJ0eSh7fSwgJ2EnLCB7Z2V0OiBmdW5jdGlvbigpeyByZXR1cm4gNzsgfX0pLmEgIT0gNztcbn0pO1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fZGVzY3JpcHRvcnMuanNcbi8vIG1vZHVsZSBpZCA9IDJcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMiAzIDQgNSA2IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE3IDE4IDE5IDIwIDIxIDIyIDIzIDI0IDI1IDI2IDI3IDI4IDI5IDMwIDMxIDMyIDMzIDM0IDM1IDM2IDM3IDM4IDM5IDQwIDQxIDQyIDQzIDQ0IDQ1IDQ2IDQ3IDQ4IDQ5IDUwIDUxIDUyIDUzIDU0IDU1IDU2IDU3IDU4IiwicmVxdWlyZSgnLi4vLi4vbW9kdWxlcy9lczYub2JqZWN0LmRlZmluZS1wcm9wZXJ0eScpO1xudmFyICRPYmplY3QgPSByZXF1aXJlKCcuLi8uLi9tb2R1bGVzL19jb3JlJykuT2JqZWN0O1xubW9kdWxlLmV4cG9ydHMgPSBmdW5jdGlvbiBkZWZpbmVQcm9wZXJ0eShpdCwga2V5LCBkZXNjKXtcbiAgcmV0dXJuICRPYmplY3QuZGVmaW5lUHJvcGVydHkoaXQsIGtleSwgZGVzYyk7XG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvZm4vb2JqZWN0L2RlZmluZS1wcm9wZXJ0eS5qc1xuLy8gbW9kdWxlIGlkID0gMjBcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMiAzIDQgNSA2IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE3IDE4IDE5IDIwIDIxIDIyIDIzIDI0IDI1IDI2IDI3IDI4IDI5IDMwIDMxIDMyIDMzIDM0IDM1IDM2IDM3IDM4IDM5IDQwIDQxIDQyIDQzIDQ0IDQ1IDQ2IDQ3IDQ4IDQ5IDUwIDUxIDUyIDUzIDU0IDU1IDU2IDU3IDU4IiwidmFyICRleHBvcnQgPSByZXF1aXJlKCcuL19leHBvcnQnKTtcbi8vIDE5LjEuMi40IC8gMTUuMi4zLjYgT2JqZWN0LmRlZmluZVByb3BlcnR5KE8sIFAsIEF0dHJpYnV0ZXMpXG4kZXhwb3J0KCRleHBvcnQuUyArICRleHBvcnQuRiAqICFyZXF1aXJlKCcuL19kZXNjcmlwdG9ycycpLCAnT2JqZWN0Jywge2RlZmluZVByb3BlcnR5OiByZXF1aXJlKCcuL19vYmplY3QtZHAnKS5mfSk7XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL2VzNi5vYmplY3QuZGVmaW5lLXByb3BlcnR5LmpzXG4vLyBtb2R1bGUgaWQgPSAyMVxuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAyIDMgNCA1IDYgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTcgMTggMTkgMjAgMjEgMjIgMjMgMjQgMjUgMjYgMjcgMjggMjkgMzAgMzEgMzIgMzMgMzQgMzUgMzYgMzcgMzggMzkgNDAgNDEgNDIgNDMgNDQgNDUgNDYgNDcgNDggNDkgNTAgNTEgNTIgNTMgNTQgNTUgNTYgNTcgNTgiLCIvLyB0byBpbmRleGVkIG9iamVjdCwgdG9PYmplY3Qgd2l0aCBmYWxsYmFjayBmb3Igbm9uLWFycmF5LWxpa2UgRVMzIHN0cmluZ3NcbnZhciBJT2JqZWN0ID0gcmVxdWlyZSgnLi9faW9iamVjdCcpXG4gICwgZGVmaW5lZCA9IHJlcXVpcmUoJy4vX2RlZmluZWQnKTtcbm1vZHVsZS5leHBvcnRzID0gZnVuY3Rpb24oaXQpe1xuICByZXR1cm4gSU9iamVjdChkZWZpbmVkKGl0KSk7XG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fdG8taW9iamVjdC5qc1xuLy8gbW9kdWxlIGlkID0gMjJcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMyA0IDUgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTggMTkgMjAiLCJ2YXIgc3RvcmUgICAgICA9IHJlcXVpcmUoJy4vX3NoYXJlZCcpKCd3a3MnKVxuICAsIHVpZCAgICAgICAgPSByZXF1aXJlKCcuL191aWQnKVxuICAsIFN5bWJvbCAgICAgPSByZXF1aXJlKCcuL19nbG9iYWwnKS5TeW1ib2xcbiAgLCBVU0VfU1lNQk9MID0gdHlwZW9mIFN5bWJvbCA9PSAnZnVuY3Rpb24nO1xuXG52YXIgJGV4cG9ydHMgPSBtb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uKG5hbWUpe1xuICByZXR1cm4gc3RvcmVbbmFtZV0gfHwgKHN0b3JlW25hbWVdID1cbiAgICBVU0VfU1lNQk9MICYmIFN5bWJvbFtuYW1lXSB8fCAoVVNFX1NZTUJPTCA/IFN5bWJvbCA6IHVpZCkoJ1N5bWJvbC4nICsgbmFtZSkpO1xufTtcblxuJGV4cG9ydHMuc3RvcmUgPSBzdG9yZTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX3drcy5qc1xuLy8gbW9kdWxlIGlkID0gMjNcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMyA0IDUgOCA5IDE0IiwidmFyIGhhc093blByb3BlcnR5ID0ge30uaGFzT3duUHJvcGVydHk7XG5tb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uKGl0LCBrZXkpe1xuICByZXR1cm4gaGFzT3duUHJvcGVydHkuY2FsbChpdCwga2V5KTtcbn07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19oYXMuanNcbi8vIG1vZHVsZSBpZCA9IDI1XG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDMgNCA1IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE4IDE5IDIwIiwidmFyIGNvcmUgPSBtb2R1bGUuZXhwb3J0cyA9IHt2ZXJzaW9uOiAnMi40LjAnfTtcbmlmKHR5cGVvZiBfX2UgPT0gJ251bWJlcicpX19lID0gY29yZTsgLy8gZXNsaW50LWRpc2FibGUtbGluZSBuby11bmRlZlxuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fY29yZS5qc1xuLy8gbW9kdWxlIGlkID0gM1xuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAyIDMgNCA1IDYgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTcgMTggMTkgMjAgMjEgMjIgMjMgMjQgMjUgMjYgMjcgMjggMjkgMzAgMzEgMzIgMzMgMzQgMzUgMzYgMzcgMzggMzkgNDAgNDEgNDIgNDMgNDQgNDUgNDYgNDcgNDggNDkgNTAgNTEgNTIgNTMgNTQgNTUgNTYgNTcgNTgiLCIvLyAxOS4xLjIuMTQgLyAxNS4yLjMuMTQgT2JqZWN0LmtleXMoTylcbnZhciAka2V5cyAgICAgICA9IHJlcXVpcmUoJy4vX29iamVjdC1rZXlzLWludGVybmFsJylcbiAgLCBlbnVtQnVnS2V5cyA9IHJlcXVpcmUoJy4vX2VudW0tYnVnLWtleXMnKTtcblxubW9kdWxlLmV4cG9ydHMgPSBPYmplY3Qua2V5cyB8fCBmdW5jdGlvbiBrZXlzKE8pe1xuICByZXR1cm4gJGtleXMoTywgZW51bUJ1Z0tleXMpO1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX29iamVjdC1rZXlzLmpzXG4vLyBtb2R1bGUgaWQgPSAzM1xuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAzIDQgNSA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxOCAxOSAyMCIsIi8vIDcuMi4xIFJlcXVpcmVPYmplY3RDb2VyY2libGUoYXJndW1lbnQpXG5tb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uKGl0KXtcbiAgaWYoaXQgPT0gdW5kZWZpbmVkKXRocm93IFR5cGVFcnJvcihcIkNhbid0IGNhbGwgbWV0aG9kIG9uICBcIiArIGl0KTtcbiAgcmV0dXJuIGl0O1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2RlZmluZWQuanNcbi8vIG1vZHVsZSBpZCA9IDM1XG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDMgNCA1IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE4IDE5IDIwIiwiLy8gNy4xLjQgVG9JbnRlZ2VyXG52YXIgY2VpbCAgPSBNYXRoLmNlaWxcbiAgLCBmbG9vciA9IE1hdGguZmxvb3I7XG5tb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uKGl0KXtcbiAgcmV0dXJuIGlzTmFOKGl0ID0gK2l0KSA/IDAgOiAoaXQgPiAwID8gZmxvb3IgOiBjZWlsKShpdCk7XG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fdG8taW50ZWdlci5qc1xuLy8gbW9kdWxlIGlkID0gMzZcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMyA0IDUgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTggMTkgMjAiLCJtb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uKGl0KXtcbiAgcmV0dXJuIHR5cGVvZiBpdCA9PT0gJ29iamVjdCcgPyBpdCAhPT0gbnVsbCA6IHR5cGVvZiBpdCA9PT0gJ2Z1bmN0aW9uJztcbn07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19pcy1vYmplY3QuanNcbi8vIG1vZHVsZSBpZCA9IDRcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMiAzIDQgNSA2IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE3IDE4IDE5IDIwIDIxIDIyIDIzIDI0IDI1IDI2IDI3IDI4IDI5IDMwIDMxIDMyIDMzIDM0IDM1IDM2IDM3IDM4IDM5IDQwIDQxIDQyIDQzIDQ0IDQ1IDQ2IDQ3IDQ4IDQ5IDUwIDUxIDUyIDUzIDU0IDU1IDU2IDU3IDU4IiwidmFyIGlkID0gMFxuICAsIHB4ID0gTWF0aC5yYW5kb20oKTtcbm1vZHVsZS5leHBvcnRzID0gZnVuY3Rpb24oa2V5KXtcbiAgcmV0dXJuICdTeW1ib2woJy5jb25jYXQoa2V5ID09PSB1bmRlZmluZWQgPyAnJyA6IGtleSwgJylfJywgKCsraWQgKyBweCkudG9TdHJpbmcoMzYpKTtcbn07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL191aWQuanNcbi8vIG1vZHVsZSBpZCA9IDQwXG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDMgNCA1IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE4IDE5IDIwIiwiLyoqXG4gKiBDb3B5cmlnaHQgc2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIFByZXN0YVNob3AgaXMgYW4gSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UubWQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly9kZXZkb2NzLnByZXN0YXNob3AuY29tLyBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9ycyA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgU2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqL1xuXG5jb25zdCAkID0gd2luZG93LiQ7XG5cbi8qKlxuICogTW9kdWxlIEFkbWluIFBhZ2UgQ29udHJvbGxlci5cbiAqIEBjb25zdHJ1Y3RvclxuICovXG5jbGFzcyBBZG1pbk1vZHVsZUNvbnRyb2xsZXIge1xuICAvKipcbiAgICogSW5pdGlhbGl6ZSBhbGwgbGlzdGVuZXJzIGFuZCBiaW5kIGV2ZXJ5dGhpbmdcbiAgICogQG1ldGhvZCBpbml0XG4gICAqIEBtZW1iZXJvZiBBZG1pbk1vZHVsZVxuICAgKi9cbiAgY29uc3RydWN0b3IobW9kdWxlQ2FyZENvbnRyb2xsZXIpIHtcbiAgICB0aGlzLm1vZHVsZUNhcmRDb250cm9sbGVyID0gbW9kdWxlQ2FyZENvbnRyb2xsZXI7XG5cbiAgICB0aGlzLkRFRkFVTFRfTUFYX1JFQ0VOVExZX1VTRUQgPSAxMDtcbiAgICB0aGlzLkRFRkFVTFRfTUFYX1BFUl9DQVRFR09SSUVTID0gNjtcbiAgICB0aGlzLkRJU1BMQVlfR1JJRCA9ICdncmlkJztcbiAgICB0aGlzLkRJU1BMQVlfTElTVCA9ICdsaXN0JztcbiAgICB0aGlzLkNBVEVHT1JZX1JFQ0VOVExZX1VTRUQgPSAncmVjZW50bHktdXNlZCc7XG5cbiAgICB0aGlzLmN1cnJlbnRDYXRlZ29yeURpc3BsYXkgPSB7fTtcbiAgICB0aGlzLmN1cnJlbnREaXNwbGF5ID0gJyc7XG4gICAgdGhpcy5pc0NhdGVnb3J5R3JpZERpc3BsYXllZCA9IGZhbHNlO1xuICAgIHRoaXMuY3VycmVudFRhZ3NMaXN0ID0gW107XG4gICAgdGhpcy5jdXJyZW50UmVmQ2F0ZWdvcnkgPSBudWxsO1xuICAgIHRoaXMuY3VycmVudFJlZlN0YXR1cyA9IG51bGw7XG4gICAgdGhpcy5jdXJyZW50U29ydGluZyA9IG51bGw7XG4gICAgdGhpcy5iYXNlQWRkb25zVXJsID0gJ2h0dHBzOi8vYWRkb25zLnByZXN0YXNob3AuY29tLyc7XG4gICAgdGhpcy5wc3RhZ2dlcklucHV0ID0gbnVsbDtcbiAgICB0aGlzLmxhc3RCdWxrQWN0aW9uID0gbnVsbDtcbiAgICB0aGlzLmlzVXBsb2FkU3RhcnRlZCA9IGZhbHNlO1xuXG4gICAgdGhpcy5yZWNlbnRseVVzZWRTZWxlY3RvciA9ICcjbW9kdWxlLXJlY2VudGx5LXVzZWQtbGlzdCAubW9kdWxlcy1saXN0JztcblxuICAgIC8qKlxuICAgICAqIExvYWRlZCBtb2R1bGVzIGxpc3QuXG4gICAgICogQ29udGFpbmluZyB0aGUgY2FyZCBhbmQgbGlzdCBkaXNwbGF5LlxuICAgICAqIEB0eXBlIHtBcnJheX1cbiAgICAgKi9cbiAgICB0aGlzLm1vZHVsZXNMaXN0ID0gW107XG4gICAgdGhpcy5hZGRvbnNDYXJkR3JpZCA9IG51bGw7XG4gICAgdGhpcy5hZGRvbnNDYXJkTGlzdCA9IG51bGw7XG5cbiAgICB0aGlzLm1vZHVsZVNob3J0TGlzdCA9ICcubW9kdWxlLXNob3J0LWxpc3QnO1xuICAgIC8vIFNlZSBtb3JlICYgU2VlIGxlc3Mgc2VsZWN0b3JcbiAgICB0aGlzLnNlZU1vcmVTZWxlY3RvciA9ICcuc2VlLW1vcmUnO1xuICAgIHRoaXMuc2VlTGVzc1NlbGVjdG9yID0gJy5zZWUtbGVzcyc7XG5cbiAgICAvLyBTZWxlY3RvcnMgaW50byB2YXJzIHRvIG1ha2UgaXQgZWFzaWVyIHRvIGNoYW5nZSB0aGVtIHdoaWxlIGtlZXBpbmcgc2FtZSBjb2RlIGxvZ2ljXG4gICAgdGhpcy5tb2R1bGVJdGVtR3JpZFNlbGVjdG9yID0gJy5tb2R1bGUtaXRlbS1ncmlkJztcbiAgICB0aGlzLm1vZHVsZUl0ZW1MaXN0U2VsZWN0b3IgPSAnLm1vZHVsZS1pdGVtLWxpc3QnO1xuICAgIHRoaXMuY2F0ZWdvcnlTZWxlY3RvckxhYmVsU2VsZWN0b3IgPSAnLm1vZHVsZS1jYXRlZ29yeS1zZWxlY3Rvci1sYWJlbCc7XG4gICAgdGhpcy5jYXRlZ29yeVNlbGVjdG9yID0gJy5tb2R1bGUtY2F0ZWdvcnktc2VsZWN0b3InO1xuICAgIHRoaXMuY2F0ZWdvcnlJdGVtU2VsZWN0b3IgPSAnLm1vZHVsZS1jYXRlZ29yeS1tZW51JztcbiAgICB0aGlzLmFkZG9uc0xvZ2luQnV0dG9uU2VsZWN0b3IgPSAnI2FkZG9uc19sb2dpbl9idG4nO1xuICAgIHRoaXMuY2F0ZWdvcnlSZXNldEJ0blNlbGVjdG9yID0gJy5tb2R1bGUtY2F0ZWdvcnktcmVzZXQnO1xuICAgIHRoaXMubW9kdWxlSW5zdGFsbEJ0blNlbGVjdG9yID0gJ2lucHV0Lm1vZHVsZS1pbnN0YWxsLWJ0bic7XG4gICAgdGhpcy5tb2R1bGVTb3J0aW5nRHJvcGRvd25TZWxlY3RvciA9ICcubW9kdWxlLXNvcnRpbmctYXV0aG9yIHNlbGVjdCc7XG4gICAgdGhpcy5jYXRlZ29yeUdyaWRTZWxlY3RvciA9ICcjbW9kdWxlcy1jYXRlZ29yaWVzLWdyaWQnO1xuICAgIHRoaXMuY2F0ZWdvcnlHcmlkSXRlbVNlbGVjdG9yID0gJy5tb2R1bGUtY2F0ZWdvcnktaXRlbSc7XG4gICAgdGhpcy5hZGRvbkl0ZW1HcmlkU2VsZWN0b3IgPSAnLm1vZHVsZS1hZGRvbnMtaXRlbS1ncmlkJztcbiAgICB0aGlzLmFkZG9uSXRlbUxpc3RTZWxlY3RvciA9ICcubW9kdWxlLWFkZG9ucy1pdGVtLWxpc3QnO1xuXG4gICAgLy8gVXBncmFkZSBBbGwgc2VsZWN0b3JzXG4gICAgdGhpcy51cGdyYWRlQWxsU291cmNlID0gJy5tb2R1bGVfYWN0aW9uX21lbnVfdXBncmFkZV9hbGwnO1xuICAgIHRoaXMudXBncmFkZUFsbFRhcmdldHMgPSAnI21vZHVsZXMtbGlzdC1jb250YWluZXItdXBkYXRlIC5tb2R1bGVfYWN0aW9uX21lbnVfdXBncmFkZTp2aXNpYmxlJztcblxuICAgIC8vIEJ1bGsgYWN0aW9uIHNlbGVjdG9yc1xuICAgIHRoaXMuYnVsa0FjdGlvbkRyb3BEb3duU2VsZWN0b3IgPSAnLm1vZHVsZS1idWxrLWFjdGlvbnMnO1xuICAgIHRoaXMuYnVsa0l0ZW1TZWxlY3RvciA9ICcubW9kdWxlLWJ1bGstbWVudSc7XG4gICAgdGhpcy5idWxrQWN0aW9uQ2hlY2tib3hMaXN0U2VsZWN0b3IgPSAnLm1vZHVsZS1jaGVja2JveC1idWxrLWxpc3QgaW5wdXQnO1xuICAgIHRoaXMuYnVsa0FjdGlvbkNoZWNrYm94R3JpZFNlbGVjdG9yID0gJy5tb2R1bGUtY2hlY2tib3gtYnVsay1ncmlkIGlucHV0JztcbiAgICB0aGlzLmNoZWNrZWRCdWxrQWN0aW9uTGlzdFNlbGVjdG9yID0gYCR7dGhpcy5idWxrQWN0aW9uQ2hlY2tib3hMaXN0U2VsZWN0b3J9OmNoZWNrZWRgO1xuICAgIHRoaXMuY2hlY2tlZEJ1bGtBY3Rpb25HcmlkU2VsZWN0b3IgPSBgJHt0aGlzLmJ1bGtBY3Rpb25DaGVja2JveEdyaWRTZWxlY3Rvcn06Y2hlY2tlZGA7XG4gICAgdGhpcy5idWxrQWN0aW9uQ2hlY2tib3hTZWxlY3RvciA9ICcjbW9kdWxlLW1vZGFsLWJ1bGstY2hlY2tib3gnO1xuICAgIHRoaXMuYnVsa0NvbmZpcm1Nb2RhbFNlbGVjdG9yID0gJyNtb2R1bGUtbW9kYWwtYnVsay1jb25maXJtJztcbiAgICB0aGlzLmJ1bGtDb25maXJtTW9kYWxBY3Rpb25OYW1lU2VsZWN0b3IgPSAnI21vZHVsZS1tb2RhbC1idWxrLWNvbmZpcm0tYWN0aW9uLW5hbWUnO1xuICAgIHRoaXMuYnVsa0NvbmZpcm1Nb2RhbExpc3RTZWxlY3RvciA9ICcjbW9kdWxlLW1vZGFsLWJ1bGstY29uZmlybS1saXN0JztcbiAgICB0aGlzLmJ1bGtDb25maXJtTW9kYWxBY2tCdG5TZWxlY3RvciA9ICcjbW9kdWxlLW1vZGFsLWNvbmZpcm0tYnVsay1hY2snO1xuXG4gICAgLy8gUGxhY2Vob2xkZXJzXG4gICAgdGhpcy5wbGFjZWhvbGRlckdsb2JhbFNlbGVjdG9yID0gJy5tb2R1bGUtcGxhY2Vob2xkZXJzLXdyYXBwZXInO1xuICAgIHRoaXMucGxhY2Vob2xkZXJGYWlsdXJlR2xvYmFsU2VsZWN0b3IgPSAnLm1vZHVsZS1wbGFjZWhvbGRlcnMtZmFpbHVyZSc7XG4gICAgdGhpcy5wbGFjZWhvbGRlckZhaWx1cmVNc2dTZWxlY3RvciA9ICcubW9kdWxlLXBsYWNlaG9sZGVycy1mYWlsdXJlLW1zZyc7XG4gICAgdGhpcy5wbGFjZWhvbGRlckZhaWx1cmVSZXRyeUJ0blNlbGVjdG9yID0gJyNtb2R1bGUtcGxhY2Vob2xkZXJzLWZhaWx1cmUtcmV0cnknO1xuXG4gICAgLy8gTW9kdWxlJ3Mgc3RhdHVzZXMgc2VsZWN0b3JzXG4gICAgdGhpcy5zdGF0dXNTZWxlY3RvckxhYmVsU2VsZWN0b3IgPSAnLm1vZHVsZS1zdGF0dXMtc2VsZWN0b3ItbGFiZWwnO1xuICAgIHRoaXMuc3RhdHVzSXRlbVNlbGVjdG9yID0gJy5tb2R1bGUtc3RhdHVzLW1lbnUnO1xuICAgIHRoaXMuc3RhdHVzUmVzZXRCdG5TZWxlY3RvciA9ICcubW9kdWxlLXN0YXR1cy1yZXNldCc7XG5cbiAgICAvLyBTZWxlY3RvcnMgZm9yIE1vZHVsZSBJbXBvcnQgYW5kIEFkZG9ucyBjb25uZWN0XG4gICAgdGhpcy5hZGRvbnNDb25uZWN0TW9kYWxCdG5TZWxlY3RvciA9ICcjcGFnZS1oZWFkZXItZGVzYy1jb25maWd1cmF0aW9uLWFkZG9uc19jb25uZWN0JztcbiAgICB0aGlzLmFkZG9uc0xvZ291dE1vZGFsQnRuU2VsZWN0b3IgPSAnI3BhZ2UtaGVhZGVyLWRlc2MtY29uZmlndXJhdGlvbi1hZGRvbnNfbG9nb3V0JztcbiAgICB0aGlzLmFkZG9uc0ltcG9ydE1vZGFsQnRuU2VsZWN0b3IgPSAnI3BhZ2UtaGVhZGVyLWRlc2MtY29uZmlndXJhdGlvbi1hZGRfbW9kdWxlJztcbiAgICB0aGlzLmRyb3Bab25lTW9kYWxTZWxlY3RvciA9ICcjbW9kdWxlLW1vZGFsLWltcG9ydCc7XG4gICAgdGhpcy5kcm9wWm9uZU1vZGFsRm9vdGVyU2VsZWN0b3IgPSAnI21vZHVsZS1tb2RhbC1pbXBvcnQgLm1vZGFsLWZvb3Rlcic7XG4gICAgdGhpcy5kcm9wWm9uZUltcG9ydFpvbmVTZWxlY3RvciA9ICcjaW1wb3J0RHJvcHpvbmUnO1xuICAgIHRoaXMuYWRkb25zQ29ubmVjdE1vZGFsU2VsZWN0b3IgPSAnI21vZHVsZS1tb2RhbC1hZGRvbnMtY29ubmVjdCc7XG4gICAgdGhpcy5hZGRvbnNMb2dvdXRNb2RhbFNlbGVjdG9yID0gJyNtb2R1bGUtbW9kYWwtYWRkb25zLWxvZ291dCc7XG4gICAgdGhpcy5hZGRvbnNDb25uZWN0Rm9ybSA9ICcjYWRkb25zLWNvbm5lY3QtZm9ybSc7XG4gICAgdGhpcy5tb2R1bGVJbXBvcnRNb2RhbENsb3NlQnRuID0gJyNtb2R1bGUtbW9kYWwtaW1wb3J0LWNsb3NpbmctY3Jvc3MnO1xuICAgIHRoaXMubW9kdWxlSW1wb3J0U3RhcnRTZWxlY3RvciA9ICcubW9kdWxlLWltcG9ydC1zdGFydCc7XG4gICAgdGhpcy5tb2R1bGVJbXBvcnRQcm9jZXNzaW5nU2VsZWN0b3IgPSAnLm1vZHVsZS1pbXBvcnQtcHJvY2Vzc2luZyc7XG4gICAgdGhpcy5tb2R1bGVJbXBvcnRTdWNjZXNzU2VsZWN0b3IgPSAnLm1vZHVsZS1pbXBvcnQtc3VjY2Vzcyc7XG4gICAgdGhpcy5tb2R1bGVJbXBvcnRTdWNjZXNzQ29uZmlndXJlQnRuU2VsZWN0b3IgPSAnLm1vZHVsZS1pbXBvcnQtc3VjY2Vzcy1jb25maWd1cmUnO1xuICAgIHRoaXMubW9kdWxlSW1wb3J0RmFpbHVyZVNlbGVjdG9yID0gJy5tb2R1bGUtaW1wb3J0LWZhaWx1cmUnO1xuICAgIHRoaXMubW9kdWxlSW1wb3J0RmFpbHVyZVJldHJ5U2VsZWN0b3IgPSAnLm1vZHVsZS1pbXBvcnQtZmFpbHVyZS1yZXRyeSc7XG4gICAgdGhpcy5tb2R1bGVJbXBvcnRGYWlsdXJlRGV0YWlsc0J0blNlbGVjdG9yID0gJy5tb2R1bGUtaW1wb3J0LWZhaWx1cmUtZGV0YWlscy1hY3Rpb24nO1xuICAgIHRoaXMubW9kdWxlSW1wb3J0U2VsZWN0RmlsZU1hbnVhbFNlbGVjdG9yID0gJy5tb2R1bGUtaW1wb3J0LXN0YXJ0LXNlbGVjdC1tYW51YWwnO1xuICAgIHRoaXMubW9kdWxlSW1wb3J0RmFpbHVyZU1zZ0RldGFpbHNTZWxlY3RvciA9ICcubW9kdWxlLWltcG9ydC1mYWlsdXJlLWRldGFpbHMnO1xuICAgIHRoaXMubW9kdWxlSW1wb3J0Q29uZmlybVNlbGVjdG9yID0gJy5tb2R1bGUtaW1wb3J0LWNvbmZpcm0nO1xuXG4gICAgdGhpcy5pbml0U29ydGluZ0Ryb3Bkb3duKCk7XG4gICAgdGhpcy5pbml0Qk9FdmVudFJlZ2lzdGVyaW5nKCk7XG4gICAgdGhpcy5pbml0Q3VycmVudERpc3BsYXkoKTtcbiAgICB0aGlzLmluaXRTb3J0aW5nRGlzcGxheVN3aXRjaCgpO1xuICAgIHRoaXMuaW5pdEJ1bGtEcm9wZG93bigpO1xuICAgIHRoaXMuaW5pdFNlYXJjaEJsb2NrKCk7XG4gICAgdGhpcy5pbml0Q2F0ZWdvcnlTZWxlY3QoKTtcbiAgICB0aGlzLmluaXRDYXRlZ29yaWVzR3JpZCgpO1xuICAgIHRoaXMuaW5pdEFjdGlvbkJ1dHRvbnMoKTtcbiAgICB0aGlzLmluaXRBZGRvbnNTZWFyY2goKTtcbiAgICB0aGlzLmluaXRBZGRvbnNDb25uZWN0KCk7XG4gICAgdGhpcy5pbml0QWRkTW9kdWxlQWN0aW9uKCk7XG4gICAgdGhpcy5pbml0RHJvcHpvbmUoKTtcbiAgICB0aGlzLmluaXRQYWdlQ2hhbmdlUHJvdGVjdGlvbigpO1xuICAgIHRoaXMuaW5pdFBsYWNlaG9sZGVyTWVjaGFuaXNtKCk7XG4gICAgdGhpcy5pbml0RmlsdGVyU3RhdHVzRHJvcGRvd24oKTtcbiAgICB0aGlzLmZldGNoTW9kdWxlc0xpc3QoKTtcbiAgICB0aGlzLmdldE5vdGlmaWNhdGlvbnNDb3VudCgpO1xuICAgIHRoaXMuaW5pdGlhbGl6ZVNlZU1vcmUoKTtcbiAgfVxuXG4gIGluaXRGaWx0ZXJTdGF0dXNEcm9wZG93bigpIHtcbiAgICBjb25zdCBzZWxmID0gdGhpcztcbiAgICBjb25zdCBib2R5ID0gJCgnYm9keScpO1xuICAgIGJvZHkub24oJ2NsaWNrJywgc2VsZi5zdGF0dXNJdGVtU2VsZWN0b3IsIGZ1bmN0aW9uICgpIHtcbiAgICAgIC8vIEdldCBkYXRhIGZyb20gbGkgRE9NIGlucHV0XG4gICAgICBzZWxmLmN1cnJlbnRSZWZTdGF0dXMgPSBwYXJzZUludCgkKHRoaXMpLmRhdGEoJ3N0YXR1cy1yZWYnKSwgMTApO1xuICAgICAgLy8gQ2hhbmdlIGRyb3Bkb3duIGxhYmVsIHRvIHNldCBpdCB0byB0aGUgY3VycmVudCBzdGF0dXMnIGRpc3BsYXluYW1lXG4gICAgICAkKHNlbGYuc3RhdHVzU2VsZWN0b3JMYWJlbFNlbGVjdG9yKS50ZXh0KCQodGhpcykuZmluZCgnYTpmaXJzdCcpLnRleHQoKSk7XG4gICAgICAkKHNlbGYuc3RhdHVzUmVzZXRCdG5TZWxlY3Rvcikuc2hvdygpO1xuICAgICAgc2VsZi51cGRhdGVNb2R1bGVWaXNpYmlsaXR5KCk7XG4gICAgfSk7XG5cbiAgICBib2R5Lm9uKCdjbGljaycsIHNlbGYuc3RhdHVzUmVzZXRCdG5TZWxlY3RvciwgZnVuY3Rpb24gKCkge1xuICAgICAgJChzZWxmLnN0YXR1c1NlbGVjdG9yTGFiZWxTZWxlY3RvcikudGV4dCgkKHRoaXMpLmZpbmQoJ2EnKS50ZXh0KCkpO1xuICAgICAgJCh0aGlzKS5oaWRlKCk7XG4gICAgICBzZWxmLmN1cnJlbnRSZWZTdGF0dXMgPSBudWxsO1xuICAgICAgc2VsZi51cGRhdGVNb2R1bGVWaXNpYmlsaXR5KCk7XG4gICAgfSk7XG4gIH1cblxuICBpbml0QnVsa0Ryb3Bkb3duKCkge1xuICAgIGNvbnN0IHNlbGYgPSB0aGlzO1xuICAgIGNvbnN0IGJvZHkgPSAkKCdib2R5Jyk7XG5cblxuICAgIGJvZHkub24oJ2NsaWNrJywgc2VsZi5nZXRCdWxrQ2hlY2tib3hlc1NlbGVjdG9yKCksICgpID0+IHtcbiAgICAgIGNvbnN0IHNlbGVjdG9yID0gJChzZWxmLmJ1bGtBY3Rpb25Ecm9wRG93blNlbGVjdG9yKTtcbiAgICAgIGlmICgkKHNlbGYuZ2V0QnVsa0NoZWNrYm94ZXNDaGVja2VkU2VsZWN0b3IoKSkubGVuZ3RoID4gMCkge1xuICAgICAgICBzZWxlY3Rvci5jbG9zZXN0KCcubW9kdWxlLXRvcC1tZW51LWl0ZW0nKVxuICAgICAgICAgICAgICAgIC5yZW1vdmVDbGFzcygnZGlzYWJsZWQnKTtcbiAgICAgIH0gZWxzZSB7XG4gICAgICAgIHNlbGVjdG9yLmNsb3Nlc3QoJy5tb2R1bGUtdG9wLW1lbnUtaXRlbScpXG4gICAgICAgICAgICAgICAgLmFkZENsYXNzKCdkaXNhYmxlZCcpO1xuICAgICAgfVxuICAgIH0pO1xuXG4gICAgYm9keS5vbignY2xpY2snLCBzZWxmLmJ1bGtJdGVtU2VsZWN0b3IsIGZ1bmN0aW9uIGluaXRpYWxpemVCb2R5Q2hhbmdlKCkge1xuICAgICAgaWYgKCQoc2VsZi5nZXRCdWxrQ2hlY2tib3hlc0NoZWNrZWRTZWxlY3RvcigpKS5sZW5ndGggPT09IDApIHtcbiAgICAgICAgJC5ncm93bC53YXJuaW5nKHttZXNzYWdlOiB3aW5kb3cudHJhbnNsYXRlX2phdmFzY3JpcHRzWydCdWxrIEFjdGlvbiAtIE9uZSBtb2R1bGUgbWluaW11bSddfSk7XG4gICAgICAgIHJldHVybjtcbiAgICAgIH1cblxuICAgICAgc2VsZi5sYXN0QnVsa0FjdGlvbiA9ICQodGhpcykuZGF0YSgncmVmJyk7XG4gICAgICBjb25zdCBtb2R1bGVzTGlzdFN0cmluZyA9IHNlbGYuYnVpbGRCdWxrQWN0aW9uTW9kdWxlTGlzdCgpO1xuICAgICAgY29uc3QgYWN0aW9uU3RyaW5nID0gJCh0aGlzKS5maW5kKCc6Y2hlY2tlZCcpLnRleHQoKS50b0xvd2VyQ2FzZSgpO1xuICAgICAgJChzZWxmLmJ1bGtDb25maXJtTW9kYWxMaXN0U2VsZWN0b3IpLmh0bWwobW9kdWxlc0xpc3RTdHJpbmcpO1xuICAgICAgJChzZWxmLmJ1bGtDb25maXJtTW9kYWxBY3Rpb25OYW1lU2VsZWN0b3IpLnRleHQoYWN0aW9uU3RyaW5nKTtcblxuICAgICAgaWYgKHNlbGYubGFzdEJ1bGtBY3Rpb24gPT09ICdidWxrLXVuaW5zdGFsbCcpIHtcbiAgICAgICAgJChzZWxmLmJ1bGtBY3Rpb25DaGVja2JveFNlbGVjdG9yKS5zaG93KCk7XG4gICAgICB9IGVsc2Uge1xuICAgICAgICAkKHNlbGYuYnVsa0FjdGlvbkNoZWNrYm94U2VsZWN0b3IpLmhpZGUoKTtcbiAgICAgIH1cblxuICAgICAgJChzZWxmLmJ1bGtDb25maXJtTW9kYWxTZWxlY3RvcikubW9kYWwoJ3Nob3cnKTtcbiAgICB9KTtcblxuICAgIGJvZHkub24oJ2NsaWNrJywgdGhpcy5idWxrQ29uZmlybU1vZGFsQWNrQnRuU2VsZWN0b3IsIChldmVudCkgPT4ge1xuICAgICAgZXZlbnQucHJldmVudERlZmF1bHQoKTtcbiAgICAgIGV2ZW50LnN0b3BQcm9wYWdhdGlvbigpO1xuICAgICAgJChzZWxmLmJ1bGtDb25maXJtTW9kYWxTZWxlY3RvcikubW9kYWwoJ2hpZGUnKTtcbiAgICAgIHNlbGYuZG9CdWxrQWN0aW9uKHNlbGYubGFzdEJ1bGtBY3Rpb24pO1xuICAgIH0pO1xuICB9XG5cbiAgaW5pdEJPRXZlbnRSZWdpc3RlcmluZygpIHtcbiAgICB3aW5kb3cuQk9FdmVudC5vbignTW9kdWxlIERpc2FibGVkJywgdGhpcy5vbk1vZHVsZURpc2FibGVkLCB0aGlzKTtcbiAgICB3aW5kb3cuQk9FdmVudC5vbignTW9kdWxlIFVuaW5zdGFsbGVkJywgdGhpcy51cGRhdGVUb3RhbFJlc3VsdHMsIHRoaXMpO1xuICB9XG5cbiAgb25Nb2R1bGVEaXNhYmxlZCgpIHtcbiAgICBjb25zdCBzZWxmID0gdGhpcztcbiAgICBjb25zdCBtb2R1bGVJdGVtU2VsZWN0b3IgPSBzZWxmLmdldE1vZHVsZUl0ZW1TZWxlY3RvcigpO1xuXG4gICAgJCgnLm1vZHVsZXMtbGlzdCcpLmVhY2goZnVuY3Rpb24gc2Nhbk1vZHVsZXNMaXN0KCkge1xuICAgICAgc2VsZi51cGRhdGVUb3RhbFJlc3VsdHMoKTtcbiAgICB9KTtcbiAgfVxuXG4gIGluaXRQbGFjZWhvbGRlck1lY2hhbmlzbSgpIHtcbiAgICBjb25zdCBzZWxmID0gdGhpcztcbiAgICBpZiAoJChzZWxmLnBsYWNlaG9sZGVyR2xvYmFsU2VsZWN0b3IpLmxlbmd0aCkge1xuICAgICAgc2VsZi5hamF4TG9hZFBhZ2UoKTtcbiAgICB9XG5cbiAgICAvLyBSZXRyeSBsb2FkaW5nIG1lY2hhbmlzbVxuICAgICQoJ2JvZHknKS5vbignY2xpY2snLCBzZWxmLnBsYWNlaG9sZGVyRmFpbHVyZVJldHJ5QnRuU2VsZWN0b3IsICgpID0+IHtcbiAgICAgICQoc2VsZi5wbGFjZWhvbGRlckZhaWx1cmVHbG9iYWxTZWxlY3RvcikuZmFkZU91dCgpO1xuICAgICAgJChzZWxmLnBsYWNlaG9sZGVyR2xvYmFsU2VsZWN0b3IpLmZhZGVJbigpO1xuICAgICAgc2VsZi5hamF4TG9hZFBhZ2UoKTtcbiAgICB9KTtcbiAgfVxuXG4gIGFqYXhMb2FkUGFnZSgpIHtcbiAgICBjb25zdCBzZWxmID0gdGhpcztcblxuICAgICQuYWpheCh7XG4gICAgICBtZXRob2Q6ICdHRVQnLFxuICAgICAgdXJsOiB3aW5kb3cubW9kdWxlVVJMcy5jYXRhbG9nUmVmcmVzaCxcbiAgICB9KS5kb25lKChyZXNwb25zZSkgPT4ge1xuICAgICAgaWYgKHJlc3BvbnNlLnN0YXR1cyA9PT0gdHJ1ZSkge1xuICAgICAgICBpZiAodHlwZW9mIHJlc3BvbnNlLmRvbUVsZW1lbnRzID09PSAndW5kZWZpbmVkJykgcmVzcG9uc2UuZG9tRWxlbWVudHMgPSBudWxsO1xuICAgICAgICBpZiAodHlwZW9mIHJlc3BvbnNlLm1zZyA9PT0gJ3VuZGVmaW5lZCcpIHJlc3BvbnNlLm1zZyA9IG51bGw7XG5cbiAgICAgICAgY29uc3Qgc3R5bGVzaGVldCA9IGRvY3VtZW50LnN0eWxlU2hlZXRzWzBdO1xuICAgICAgICBjb25zdCBzdHlsZXNoZWV0UnVsZSA9ICd7ZGlzcGxheTogbm9uZX0nO1xuICAgICAgICBjb25zdCBtb2R1bGVHbG9iYWxTZWxlY3RvciA9ICcubW9kdWxlcy1saXN0JztcbiAgICAgICAgY29uc3QgbW9kdWxlU29ydGluZ1NlbGVjdG9yID0gJy5tb2R1bGUtc29ydGluZy1tZW51JztcbiAgICAgICAgY29uc3QgcmVxdWlyZWRTZWxlY3RvckNvbWJpbmF0aW9uID0gYCR7bW9kdWxlR2xvYmFsU2VsZWN0b3J9LCR7bW9kdWxlU29ydGluZ1NlbGVjdG9yfWA7XG5cbiAgICAgICAgaWYgKHN0eWxlc2hlZXQuaW5zZXJ0UnVsZSkge1xuICAgICAgICAgIHN0eWxlc2hlZXQuaW5zZXJ0UnVsZShcbiAgICAgICAgICAgIHJlcXVpcmVkU2VsZWN0b3JDb21iaW5hdGlvbiArXG4gICAgICAgICAgICBzdHlsZXNoZWV0UnVsZSwgc3R5bGVzaGVldC5jc3NSdWxlcy5sZW5ndGhcbiAgICAgICAgICApO1xuICAgICAgICB9IGVsc2UgaWYgKHN0eWxlc2hlZXQuYWRkUnVsZSkge1xuICAgICAgICAgIHN0eWxlc2hlZXQuYWRkUnVsZShcbiAgICAgICAgICAgIHJlcXVpcmVkU2VsZWN0b3JDb21iaW5hdGlvbixcbiAgICAgICAgICAgIHN0eWxlc2hlZXRSdWxlLFxuICAgICAgICAgICAgLTFcbiAgICAgICAgICApO1xuICAgICAgICB9XG5cbiAgICAgICAgJChzZWxmLnBsYWNlaG9sZGVyR2xvYmFsU2VsZWN0b3IpLmZhZGVPdXQoODAwLCAoKSA9PiB7XG4gICAgICAgICAgJC5lYWNoKHJlc3BvbnNlLmRvbUVsZW1lbnRzLCAoaW5kZXgsIGVsZW1lbnQpID0+IHtcbiAgICAgICAgICAgICQoZWxlbWVudC5zZWxlY3RvcikuYXBwZW5kKGVsZW1lbnQuY29udGVudCk7XG4gICAgICAgICAgfSk7XG4gICAgICAgICAgJChtb2R1bGVHbG9iYWxTZWxlY3RvcikuZmFkZUluKDgwMCkuY3NzKCdkaXNwbGF5JywgJ2ZsZXgnKTtcbiAgICAgICAgICAkKG1vZHVsZVNvcnRpbmdTZWxlY3RvcikuZmFkZUluKDgwMCk7XG4gICAgICAgICAgJCgnW2RhdGEtdG9nZ2xlPVwicG9wb3ZlclwiXScpLnBvcG92ZXIoKTtcbiAgICAgICAgICBzZWxmLmluaXRDdXJyZW50RGlzcGxheSgpO1xuICAgICAgICAgIHNlbGYuZmV0Y2hNb2R1bGVzTGlzdCgpO1xuICAgICAgICB9KTtcbiAgICAgIH0gZWxzZSB7XG4gICAgICAgICQoc2VsZi5wbGFjZWhvbGRlckdsb2JhbFNlbGVjdG9yKS5mYWRlT3V0KDgwMCwgKCkgPT4ge1xuICAgICAgICAgICQoc2VsZi5wbGFjZWhvbGRlckZhaWx1cmVNc2dTZWxlY3RvcikudGV4dChyZXNwb25zZS5tc2cpO1xuICAgICAgICAgICQoc2VsZi5wbGFjZWhvbGRlckZhaWx1cmVHbG9iYWxTZWxlY3RvcikuZmFkZUluKDgwMCk7XG4gICAgICAgIH0pO1xuICAgICAgfVxuICAgIH0pLmZhaWwoKHJlc3BvbnNlKSA9PiB7XG4gICAgICAkKHNlbGYucGxhY2Vob2xkZXJHbG9iYWxTZWxlY3RvcikuZmFkZU91dCg4MDAsICgpID0+IHtcbiAgICAgICAgJChzZWxmLnBsYWNlaG9sZGVyRmFpbHVyZU1zZ1NlbGVjdG9yKS50ZXh0KHJlc3BvbnNlLnN0YXR1c1RleHQpO1xuICAgICAgICAkKHNlbGYucGxhY2Vob2xkZXJGYWlsdXJlR2xvYmFsU2VsZWN0b3IpLmZhZGVJbig4MDApO1xuICAgICAgfSk7XG4gICAgfSk7XG4gIH1cblxuICBmZXRjaE1vZHVsZXNMaXN0KCkge1xuICAgIGNvbnN0IHNlbGYgPSB0aGlzO1xuICAgIGxldCBjb250YWluZXI7XG4gICAgbGV0ICR0aGlzO1xuXG4gICAgc2VsZi5tb2R1bGVzTGlzdCA9IFtdO1xuICAgICQoJy5tb2R1bGVzLWxpc3QnKS5lYWNoKGZ1bmN0aW9uIHByZXBhcmVDb250YWluZXIoKSB7XG4gICAgICBjb250YWluZXIgPSAkKHRoaXMpO1xuICAgICAgY29udGFpbmVyLmZpbmQoJy5tb2R1bGUtaXRlbScpLmVhY2goZnVuY3Rpb24gcHJlcGFyZU1vZHVsZXMoKSB7XG4gICAgICAgICR0aGlzID0gJCh0aGlzKTtcbiAgICAgICAgc2VsZi5tb2R1bGVzTGlzdC5wdXNoKHtcbiAgICAgICAgICBkb21PYmplY3Q6ICR0aGlzLFxuICAgICAgICAgIGlkOiAkdGhpcy5kYXRhKCdpZCcpLFxuICAgICAgICAgIG5hbWU6ICR0aGlzLmRhdGEoJ25hbWUnKS50b0xvd2VyQ2FzZSgpLFxuICAgICAgICAgIHNjb3Jpbmc6IHBhcnNlRmxvYXQoJHRoaXMuZGF0YSgnc2NvcmluZycpKSxcbiAgICAgICAgICBsb2dvOiAkdGhpcy5kYXRhKCdsb2dvJyksXG4gICAgICAgICAgYXV0aG9yOiAkdGhpcy5kYXRhKCdhdXRob3InKS50b0xvd2VyQ2FzZSgpLFxuICAgICAgICAgIHZlcnNpb246ICR0aGlzLmRhdGEoJ3ZlcnNpb24nKSxcbiAgICAgICAgICBkZXNjcmlwdGlvbjogJHRoaXMuZGF0YSgnZGVzY3JpcHRpb24nKS50b0xvd2VyQ2FzZSgpLFxuICAgICAgICAgIHRlY2hOYW1lOiAkdGhpcy5kYXRhKCd0ZWNoLW5hbWUnKS50b0xvd2VyQ2FzZSgpLFxuICAgICAgICAgIGNoaWxkQ2F0ZWdvcmllczogJHRoaXMuZGF0YSgnY2hpbGQtY2F0ZWdvcmllcycpLFxuICAgICAgICAgIGNhdGVnb3JpZXM6IFN0cmluZygkdGhpcy5kYXRhKCdjYXRlZ29yaWVzJykpLnRvTG93ZXJDYXNlKCksXG4gICAgICAgICAgdHlwZTogJHRoaXMuZGF0YSgndHlwZScpLFxuICAgICAgICAgIHByaWNlOiBwYXJzZUZsb2F0KCR0aGlzLmRhdGEoJ3ByaWNlJykpLFxuICAgICAgICAgIGFjdGl2ZTogcGFyc2VJbnQoJHRoaXMuZGF0YSgnYWN0aXZlJyksIDEwKSxcbiAgICAgICAgICBhY2Nlc3M6ICR0aGlzLmRhdGEoJ2xhc3QtYWNjZXNzJyksXG4gICAgICAgICAgZGlzcGxheTogJHRoaXMuaGFzQ2xhc3MoJ21vZHVsZS1pdGVtLWxpc3QnKSA/IHNlbGYuRElTUExBWV9MSVNUIDogc2VsZi5ESVNQTEFZX0dSSUQsXG4gICAgICAgICAgY29udGFpbmVyLFxuICAgICAgICB9KTtcblxuICAgICAgICAkdGhpcy5yZW1vdmUoKTtcbiAgICAgIH0pO1xuICAgIH0pO1xuXG4gICAgc2VsZi5hZGRvbnNDYXJkR3JpZCA9ICQodGhpcy5hZGRvbkl0ZW1HcmlkU2VsZWN0b3IpO1xuICAgIHNlbGYuYWRkb25zQ2FyZExpc3QgPSAkKHRoaXMuYWRkb25JdGVtTGlzdFNlbGVjdG9yKTtcbiAgICBzZWxmLnVwZGF0ZU1vZHVsZVZpc2liaWxpdHkoKTtcbiAgICAkKCdib2R5JykudHJpZ2dlcignbW9kdWxlQ2F0YWxvZ0xvYWRlZCcpO1xuICB9XG5cbiAgLyoqXG4gICAqIFByZXBhcmUgc29ydGluZ1xuICAgKlxuICAgKi9cbiAgdXBkYXRlTW9kdWxlU29ydGluZygpIHtcbiAgICBjb25zdCBzZWxmID0gdGhpcztcblxuICAgIGlmICghc2VsZi5jdXJyZW50U29ydGluZykge1xuICAgICAgcmV0dXJuO1xuICAgIH1cblxuICAgIC8vIE1vZHVsZXMgc29ydGluZ1xuICAgIGxldCBvcmRlciA9ICdhc2MnO1xuICAgIGxldCBrZXkgPSBzZWxmLmN1cnJlbnRTb3J0aW5nO1xuICAgIGNvbnN0IHNwbGl0dGVkS2V5ID0ga2V5LnNwbGl0KCctJyk7XG4gICAgaWYgKHNwbGl0dGVkS2V5Lmxlbmd0aCA+IDEpIHtcbiAgICAgIGtleSA9IHNwbGl0dGVkS2V5WzBdO1xuICAgICAgaWYgKHNwbGl0dGVkS2V5WzFdID09PSAnZGVzYycpIHtcbiAgICAgICAgb3JkZXIgPSAnZGVzYyc7XG4gICAgICB9XG4gICAgfVxuXG4gICAgY29uc3QgY3VycmVudENvbXBhcmUgPSAoYSwgYikgPT4ge1xuICAgICAgbGV0IGFEYXRhID0gYVtrZXldO1xuICAgICAgbGV0IGJEYXRhID0gYltrZXldO1xuICAgICAgaWYgKGtleSA9PT0gJ2FjY2VzcycpIHtcbiAgICAgICAgYURhdGEgPSAobmV3IERhdGUoYURhdGEpKS5nZXRUaW1lKCk7XG4gICAgICAgIGJEYXRhID0gKG5ldyBEYXRlKGJEYXRhKSkuZ2V0VGltZSgpO1xuICAgICAgICBhRGF0YSA9IGlzTmFOKGFEYXRhKSA/IDAgOiBhRGF0YTtcbiAgICAgICAgYkRhdGEgPSBpc05hTihiRGF0YSkgPyAwIDogYkRhdGE7XG4gICAgICAgIGlmIChhRGF0YSA9PT0gYkRhdGEpIHtcbiAgICAgICAgICByZXR1cm4gYi5uYW1lLmxvY2FsZUNvbXBhcmUoYS5uYW1lKTtcbiAgICAgICAgfVxuICAgICAgfVxuXG4gICAgICBpZiAoYURhdGEgPCBiRGF0YSkgcmV0dXJuIC0xO1xuICAgICAgaWYgKGFEYXRhID4gYkRhdGEpIHJldHVybiAxO1xuXG4gICAgICByZXR1cm4gMDtcbiAgICB9O1xuXG4gICAgc2VsZi5tb2R1bGVzTGlzdC5zb3J0KGN1cnJlbnRDb21wYXJlKTtcbiAgICBpZiAob3JkZXIgPT09ICdkZXNjJykge1xuICAgICAgc2VsZi5tb2R1bGVzTGlzdC5yZXZlcnNlKCk7XG4gICAgfVxuICB9XG5cbiAgdXBkYXRlTW9kdWxlQ29udGFpbmVyRGlzcGxheSgpIHtcbiAgICBjb25zdCBzZWxmID0gdGhpcztcblxuICAgICQoJy5tb2R1bGUtc2hvcnQtbGlzdCcpLmVhY2goZnVuY3Rpb24gc2V0U2hvcnRMaXN0VmlzaWJpbGl0eSgpIHtcbiAgICAgIGNvbnN0IGNvbnRhaW5lciA9ICQodGhpcyk7XG4gICAgICBjb25zdCBuYk1vZHVsZXNJbkNvbnRhaW5lciA9IGNvbnRhaW5lci5maW5kKCcubW9kdWxlLWl0ZW0nKS5sZW5ndGg7XG4gICAgICBpZiAoXG4gICAgICAgIChcbiAgICAgICAgICBzZWxmLmN1cnJlbnRSZWZDYXRlZ29yeVxuICAgICAgICAgICYmIHNlbGYuY3VycmVudFJlZkNhdGVnb3J5ICE9PSBTdHJpbmcoY29udGFpbmVyLmZpbmQoJy5tb2R1bGVzLWxpc3QnKS5kYXRhKCduYW1lJykpXG4gICAgICAgICkgfHwgKFxuICAgICAgICAgIHNlbGYuY3VycmVudFJlZlN0YXR1cyAhPT0gbnVsbFxuICAgICAgICAgICYmIG5iTW9kdWxlc0luQ29udGFpbmVyID09PSAwXG4gICAgICAgICkgfHwgKFxuICAgICAgICAgIG5iTW9kdWxlc0luQ29udGFpbmVyID09PSAwXG4gICAgICAgICAgJiYgU3RyaW5nKGNvbnRhaW5lci5maW5kKCcubW9kdWxlcy1saXN0JykuZGF0YSgnbmFtZScpKSA9PT0gc2VsZi5DQVRFR09SWV9SRUNFTlRMWV9VU0VEXG4gICAgICAgICkgfHwgKFxuICAgICAgICAgIHNlbGYuY3VycmVudFRhZ3NMaXN0Lmxlbmd0aCA+IDBcbiAgICAgICAgICAmJiBuYk1vZHVsZXNJbkNvbnRhaW5lciA9PT0gMFxuICAgICAgICApXG4gICAgICApIHtcbiAgICAgICAgY29udGFpbmVyLmhpZGUoKTtcbiAgICAgICAgcmV0dXJuO1xuICAgICAgfVxuXG4gICAgICBjb250YWluZXIuc2hvdygpO1xuICAgICAgaWYgKG5iTW9kdWxlc0luQ29udGFpbmVyID49IHNlbGYuREVGQVVMVF9NQVhfUEVSX0NBVEVHT1JJRVMpIHtcbiAgICAgICAgY29udGFpbmVyLmZpbmQoYCR7c2VsZi5zZWVNb3JlU2VsZWN0b3J9LCAke3NlbGYuc2VlTGVzc1NlbGVjdG9yfWApLnNob3coKTtcbiAgICAgIH0gZWxzZSB7XG4gICAgICAgIGNvbnRhaW5lci5maW5kKGAke3NlbGYuc2VlTW9yZVNlbGVjdG9yfSwgJHtzZWxmLnNlZUxlc3NTZWxlY3Rvcn1gKS5oaWRlKCk7XG4gICAgICB9XG4gICAgfSk7XG4gIH1cblxuICB1cGRhdGVNb2R1bGVWaXNpYmlsaXR5KCkge1xuICAgIGNvbnN0IHNlbGYgPSB0aGlzO1xuXG4gICAgc2VsZi51cGRhdGVNb2R1bGVTb3J0aW5nKCk7XG5cbiAgICAkKHNlbGYucmVjZW50bHlVc2VkU2VsZWN0b3IpLmZpbmQoJy5tb2R1bGUtaXRlbScpLnJlbW92ZSgpO1xuICAgICQoJy5tb2R1bGVzLWxpc3QnKS5maW5kKCcubW9kdWxlLWl0ZW0nKS5yZW1vdmUoKTtcblxuICAgIC8vIE1vZHVsZXMgdmlzaWJpbGl0eSBtYW5hZ2VtZW50XG4gICAgbGV0IGlzVmlzaWJsZTtcbiAgICBsZXQgY3VycmVudE1vZHVsZTtcbiAgICBsZXQgbW9kdWxlQ2F0ZWdvcnk7XG4gICAgbGV0IHRhZ0V4aXN0cztcbiAgICBsZXQgbmV3VmFsdWU7XG5cbiAgICBjb25zdCBtb2R1bGVzTGlzdExlbmd0aCA9IHNlbGYubW9kdWxlc0xpc3QubGVuZ3RoO1xuICAgIGNvbnN0IGNvdW50ZXIgPSB7fTtcblxuICAgIGZvciAobGV0IGkgPSAwOyBpIDwgbW9kdWxlc0xpc3RMZW5ndGg7IGkgKz0gMSkge1xuICAgICAgY3VycmVudE1vZHVsZSA9IHNlbGYubW9kdWxlc0xpc3RbaV07XG4gICAgICBpZiAoY3VycmVudE1vZHVsZS5kaXNwbGF5ID09PSBzZWxmLmN1cnJlbnREaXNwbGF5KSB7XG4gICAgICAgIGlzVmlzaWJsZSA9IHRydWU7XG5cbiAgICAgICAgbW9kdWxlQ2F0ZWdvcnkgPSBzZWxmLmN1cnJlbnRSZWZDYXRlZ29yeSA9PT0gc2VsZi5DQVRFR09SWV9SRUNFTlRMWV9VU0VEID9cbiAgICAgICAgICAgICAgICAgICAgICAgICBzZWxmLkNBVEVHT1JZX1JFQ0VOVExZX1VTRUQgOlxuICAgICAgICAgICAgICAgICAgICAgICAgIGN1cnJlbnRNb2R1bGUuY2F0ZWdvcmllcztcblxuICAgICAgICAvLyBDaGVjayBmb3Igc2FtZSBjYXRlZ29yeVxuICAgICAgICBpZiAoc2VsZi5jdXJyZW50UmVmQ2F0ZWdvcnkgIT09IG51bGwpIHtcbiAgICAgICAgICBpc1Zpc2libGUgJj0gbW9kdWxlQ2F0ZWdvcnkgPT09IHNlbGYuY3VycmVudFJlZkNhdGVnb3J5O1xuICAgICAgICB9XG5cbiAgICAgICAgLy8gQ2hlY2sgZm9yIHNhbWUgc3RhdHVzXG4gICAgICAgIGlmIChzZWxmLmN1cnJlbnRSZWZTdGF0dXMgIT09IG51bGwpIHtcbiAgICAgICAgICBpc1Zpc2libGUgJj0gY3VycmVudE1vZHVsZS5hY3RpdmUgPT09IHNlbGYuY3VycmVudFJlZlN0YXR1cztcbiAgICAgICAgfVxuXG4gICAgICAgIC8vIENoZWNrIGZvciB0YWcgbGlzdFxuICAgICAgICBpZiAoc2VsZi5jdXJyZW50VGFnc0xpc3QubGVuZ3RoKSB7XG4gICAgICAgICAgdGFnRXhpc3RzID0gZmFsc2U7XG4gICAgICAgICAgJC5lYWNoKHNlbGYuY3VycmVudFRhZ3NMaXN0LCAoaW5kZXgsIHZhbHVlKSA9PiB7XG4gICAgICAgICAgICBuZXdWYWx1ZSA9IHZhbHVlLnRvTG93ZXJDYXNlKCk7XG4gICAgICAgICAgICB0YWdFeGlzdHMgfD0gKFxuICAgICAgICAgICAgICBjdXJyZW50TW9kdWxlLm5hbWUuaW5kZXhPZihuZXdWYWx1ZSkgIT09IC0xXG4gICAgICAgICAgICAgIHx8IGN1cnJlbnRNb2R1bGUuZGVzY3JpcHRpb24uaW5kZXhPZihuZXdWYWx1ZSkgIT09IC0xXG4gICAgICAgICAgICAgIHx8IGN1cnJlbnRNb2R1bGUuYXV0aG9yLmluZGV4T2YobmV3VmFsdWUpICE9PSAtMVxuICAgICAgICAgICAgICB8fCBjdXJyZW50TW9kdWxlLnRlY2hOYW1lLmluZGV4T2YobmV3VmFsdWUpICE9PSAtMVxuICAgICAgICAgICAgKTtcbiAgICAgICAgICB9KTtcbiAgICAgICAgICBpc1Zpc2libGUgJj0gdGFnRXhpc3RzO1xuICAgICAgICB9XG5cbiAgICAgICAgLyoqXG4gICAgICAgICAqIElmIGxpc3QgZGlzcGxheSB3aXRob3V0IHNlYXJjaCB3ZSBtdXN0IGRpc3BsYXkgb25seSB0aGUgZmlyc3QgNSBtb2R1bGVzXG4gICAgICAgICAqL1xuICAgICAgICBpZiAoc2VsZi5jdXJyZW50RGlzcGxheSA9PT0gc2VsZi5ESVNQTEFZX0xJU1QgJiYgIXNlbGYuY3VycmVudFRhZ3NMaXN0Lmxlbmd0aCkge1xuICAgICAgICAgIGlmIChzZWxmLmN1cnJlbnRDYXRlZ29yeURpc3BsYXlbbW9kdWxlQ2F0ZWdvcnldID09PSB1bmRlZmluZWQpIHtcbiAgICAgICAgICAgIHNlbGYuY3VycmVudENhdGVnb3J5RGlzcGxheVttb2R1bGVDYXRlZ29yeV0gPSBmYWxzZTtcbiAgICAgICAgICB9XG5cbiAgICAgICAgICBpZiAoIWNvdW50ZXJbbW9kdWxlQ2F0ZWdvcnldKSB7XG4gICAgICAgICAgICBjb3VudGVyW21vZHVsZUNhdGVnb3J5XSA9IDA7XG4gICAgICAgICAgfVxuXG4gICAgICAgICAgaWYgKG1vZHVsZUNhdGVnb3J5ID09PSBzZWxmLkNBVEVHT1JZX1JFQ0VOVExZX1VTRUQpIHtcbiAgICAgICAgICAgIGlmIChjb3VudGVyW21vZHVsZUNhdGVnb3J5XSA+PSBzZWxmLkRFRkFVTFRfTUFYX1JFQ0VOVExZX1VTRUQpIHtcbiAgICAgICAgICAgICAgaXNWaXNpYmxlICY9IHNlbGYuY3VycmVudENhdGVnb3J5RGlzcGxheVttb2R1bGVDYXRlZ29yeV07XG4gICAgICAgICAgICB9XG4gICAgICAgICAgfSBlbHNlIGlmIChjb3VudGVyW21vZHVsZUNhdGVnb3J5XSA+PSBzZWxmLkRFRkFVTFRfTUFYX1BFUl9DQVRFR09SSUVTKSB7XG4gICAgICAgICAgICBpc1Zpc2libGUgJj0gc2VsZi5jdXJyZW50Q2F0ZWdvcnlEaXNwbGF5W21vZHVsZUNhdGVnb3J5XTtcbiAgICAgICAgICB9XG5cbiAgICAgICAgICBjb3VudGVyW21vZHVsZUNhdGVnb3J5XSArPSAxO1xuICAgICAgICB9XG5cbiAgICAgICAgLy8gSWYgdmlzaWJsZSwgZGlzcGxheSAoVGh4IGNhcHRhaW4gb2J2aW91cylcbiAgICAgICAgaWYgKGlzVmlzaWJsZSkge1xuICAgICAgICAgIGlmIChzZWxmLmN1cnJlbnRSZWZDYXRlZ29yeSA9PT0gc2VsZi5DQVRFR09SWV9SRUNFTlRMWV9VU0VEKSB7XG4gICAgICAgICAgICAkKHNlbGYucmVjZW50bHlVc2VkU2VsZWN0b3IpLmFwcGVuZChjdXJyZW50TW9kdWxlLmRvbU9iamVjdCk7XG4gICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgIGN1cnJlbnRNb2R1bGUuY29udGFpbmVyLmFwcGVuZChjdXJyZW50TW9kdWxlLmRvbU9iamVjdCk7XG4gICAgICAgICAgfVxuICAgICAgICB9XG4gICAgICB9XG4gICAgfVxuXG4gICAgc2VsZi51cGRhdGVNb2R1bGVDb250YWluZXJEaXNwbGF5KCk7XG5cbiAgICBpZiAoc2VsZi5jdXJyZW50VGFnc0xpc3QubGVuZ3RoKSB7XG4gICAgICAkKCcubW9kdWxlcy1saXN0JykuYXBwZW5kKHRoaXMuY3VycmVudERpc3BsYXkgPT09IHNlbGYuRElTUExBWV9HUklEID8gdGhpcy5hZGRvbnNDYXJkR3JpZCA6IHRoaXMuYWRkb25zQ2FyZExpc3QpO1xuICAgIH1cblxuICAgIHNlbGYudXBkYXRlVG90YWxSZXN1bHRzKCk7XG4gIH1cblxuICBpbml0UGFnZUNoYW5nZVByb3RlY3Rpb24oKSB7XG4gICAgY29uc3Qgc2VsZiA9IHRoaXM7XG5cbiAgICAkKHdpbmRvdykub24oJ2JlZm9yZXVubG9hZCcsICgpID0+IHtcbiAgICAgIGlmIChzZWxmLmlzVXBsb2FkU3RhcnRlZCA9PT0gdHJ1ZSkge1xuICAgICAgICByZXR1cm4gJ0l0IHNlZW1zIHNvbWUgY3JpdGljYWwgb3BlcmF0aW9uIGFyZSBydW5uaW5nLCBhcmUgeW91IHN1cmUgeW91IHdhbnQgdG8gY2hhbmdlIHBhZ2UgPyBJdCBtaWdodCBjYXVzZSBzb21lIHVuZXhlcGN0ZWQgYmVoYXZpb3JzLic7XG4gICAgICB9XG4gICAgfSk7XG4gIH1cblxuXG4gIGJ1aWxkQnVsa0FjdGlvbk1vZHVsZUxpc3QoKSB7XG4gICAgY29uc3QgY2hlY2tCb3hlc1NlbGVjdG9yID0gdGhpcy5nZXRCdWxrQ2hlY2tib3hlc0NoZWNrZWRTZWxlY3RvcigpO1xuICAgIGNvbnN0IG1vZHVsZUl0ZW1TZWxlY3RvciA9IHRoaXMuZ2V0TW9kdWxlSXRlbVNlbGVjdG9yKCk7XG4gICAgbGV0IGFscmVhZHlEb25lRmxhZyA9IDA7XG4gICAgbGV0IGh0bWxHZW5lcmF0ZWQgPSAnJztcbiAgICBsZXQgY3VycmVudEVsZW1lbnQ7XG5cbiAgICAkKGNoZWNrQm94ZXNTZWxlY3RvcikuZWFjaChmdW5jdGlvbiBwcmVwYXJlQ2hlY2tib3hlcygpIHtcbiAgICAgIGlmIChhbHJlYWR5RG9uZUZsYWcgPT09IDEwKSB7XG4gICAgICAgIC8vIEJyZWFrIGVhY2hcbiAgICAgICAgaHRtbEdlbmVyYXRlZCArPSAnLSAuLi4nO1xuICAgICAgICByZXR1cm4gZmFsc2U7XG4gICAgICB9XG5cbiAgICAgIGN1cnJlbnRFbGVtZW50ID0gJCh0aGlzKS5jbG9zZXN0KG1vZHVsZUl0ZW1TZWxlY3Rvcik7XG4gICAgICBodG1sR2VuZXJhdGVkICs9IGAtICR7Y3VycmVudEVsZW1lbnQuZGF0YSgnbmFtZScpfTxici8+YDtcbiAgICAgIGFscmVhZHlEb25lRmxhZyArPSAxO1xuXG4gICAgICByZXR1cm4gdHJ1ZTtcbiAgICB9KTtcblxuICAgIHJldHVybiBodG1sR2VuZXJhdGVkO1xuICB9XG5cbiAgaW5pdEFkZG9uc0Nvbm5lY3QoKSB7XG4gICAgY29uc3Qgc2VsZiA9IHRoaXM7XG5cbiAgICAvLyBNYWtlIGFkZG9ucyBjb25uZWN0IG1vZGFsIHJlYWR5IHRvIGJlIGNsaWNrZWRcbiAgICBpZiAoJChzZWxmLmFkZG9uc0Nvbm5lY3RNb2RhbEJ0blNlbGVjdG9yKS5hdHRyKCdocmVmJykgPT09ICcjJykge1xuICAgICAgJChzZWxmLmFkZG9uc0Nvbm5lY3RNb2RhbEJ0blNlbGVjdG9yKS5hdHRyKCdkYXRhLXRvZ2dsZScsICdtb2RhbCcpO1xuICAgICAgJChzZWxmLmFkZG9uc0Nvbm5lY3RNb2RhbEJ0blNlbGVjdG9yKS5hdHRyKCdkYXRhLXRhcmdldCcsIHNlbGYuYWRkb25zQ29ubmVjdE1vZGFsU2VsZWN0b3IpO1xuICAgIH1cblxuICAgIGlmICgkKHNlbGYuYWRkb25zTG9nb3V0TW9kYWxCdG5TZWxlY3RvcikuYXR0cignaHJlZicpID09PSAnIycpIHtcbiAgICAgICQoc2VsZi5hZGRvbnNMb2dvdXRNb2RhbEJ0blNlbGVjdG9yKS5hdHRyKCdkYXRhLXRvZ2dsZScsICdtb2RhbCcpO1xuICAgICAgJChzZWxmLmFkZG9uc0xvZ291dE1vZGFsQnRuU2VsZWN0b3IpLmF0dHIoJ2RhdGEtdGFyZ2V0Jywgc2VsZi5hZGRvbnNMb2dvdXRNb2RhbFNlbGVjdG9yKTtcbiAgICB9XG5cbiAgICAkKCdib2R5Jykub24oJ3N1Ym1pdCcsIHNlbGYuYWRkb25zQ29ubmVjdEZvcm0sIGZ1bmN0aW9uIGluaXRpYWxpemVCb2R5U3VibWl0KGV2ZW50KSB7XG4gICAgICBldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgZXZlbnQuc3RvcFByb3BhZ2F0aW9uKCk7XG5cbiAgICAgICQuYWpheCh7XG4gICAgICAgIG1ldGhvZDogJ1BPU1QnLFxuICAgICAgICB1cmw6ICQodGhpcykuYXR0cignYWN0aW9uJyksXG4gICAgICAgIGRhdGFUeXBlOiAnanNvbicsXG4gICAgICAgIGRhdGE6ICQodGhpcykuc2VyaWFsaXplKCksXG4gICAgICAgIGJlZm9yZVNlbmQ6ICgpID0+IHtcbiAgICAgICAgICAkKHNlbGYuYWRkb25zTG9naW5CdXR0b25TZWxlY3Rvcikuc2hvdygpO1xuICAgICAgICAgICQoJ2J1dHRvbi5idG5bdHlwZT1cInN1Ym1pdFwiXScsIHNlbGYuYWRkb25zQ29ubmVjdEZvcm0pLmhpZGUoKTtcbiAgICAgICAgfVxuICAgICAgfSkuZG9uZSgocmVzcG9uc2UpID0+IHtcbiAgICAgICAgaWYgKHJlc3BvbnNlLnN1Y2Nlc3MgPT09IDEpIHtcbiAgICAgICAgICBsb2NhdGlvbi5yZWxvYWQoKTtcbiAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAkLmdyb3dsLmVycm9yKHttZXNzYWdlOiByZXNwb25zZS5tZXNzYWdlfSk7XG4gICAgICAgICAgJChzZWxmLmFkZG9uc0xvZ2luQnV0dG9uU2VsZWN0b3IpLmhpZGUoKTtcbiAgICAgICAgICAkKCdidXR0b24uYnRuW3R5cGU9XCJzdWJtaXRcIl0nLCBzZWxmLmFkZG9uc0Nvbm5lY3RGb3JtKS5mYWRlSW4oKTtcbiAgICAgICAgfVxuICAgICAgfSk7XG4gICAgfSk7XG4gIH1cblxuICBpbml0QWRkTW9kdWxlQWN0aW9uKCkge1xuICAgIGNvbnN0IHNlbGYgPSB0aGlzO1xuICAgIGNvbnN0IGFkZE1vZHVsZUJ1dHRvbiA9ICQoc2VsZi5hZGRvbnNJbXBvcnRNb2RhbEJ0blNlbGVjdG9yKTtcbiAgICBhZGRNb2R1bGVCdXR0b24uYXR0cignZGF0YS10b2dnbGUnLCAnbW9kYWwnKTtcbiAgICBhZGRNb2R1bGVCdXR0b24uYXR0cignZGF0YS10YXJnZXQnLCBzZWxmLmRyb3Bab25lTW9kYWxTZWxlY3Rvcik7XG4gIH1cblxuICBpbml0RHJvcHpvbmUoKSB7XG4gICAgY29uc3Qgc2VsZiA9IHRoaXM7XG4gICAgY29uc3QgYm9keSA9ICQoJ2JvZHknKTtcbiAgICBjb25zdCBkcm9wem9uZSA9ICQoJy5kcm9wem9uZScpO1xuXG4gICAgLy8gUmVzZXQgbW9kYWwgd2hlbiBjbGljayBvbiBSZXRyeSBpbiBjYXNlIG9mIGZhaWx1cmVcbiAgICBib2R5Lm9uKFxuICAgICAgJ2NsaWNrJyxcbiAgICAgIHRoaXMubW9kdWxlSW1wb3J0RmFpbHVyZVJldHJ5U2VsZWN0b3IsXG4gICAgICAoKSA9PiB7XG4gICAgICAgICQoYCR7c2VsZi5tb2R1bGVJbXBvcnRTdWNjZXNzU2VsZWN0b3J9LCR7c2VsZi5tb2R1bGVJbXBvcnRGYWlsdXJlU2VsZWN0b3J9LCR7c2VsZi5tb2R1bGVJbXBvcnRQcm9jZXNzaW5nU2VsZWN0b3J9YCkuZmFkZU91dCgoKSA9PiB7XG4gICAgICAgICAgLyoqXG4gICAgICAgICAgICogQWRkZWQgdGltZW91dCBmb3IgYSBiZXR0ZXIgcmVuZGVyIG9mIGFuaW1hdGlvblxuICAgICAgICAgICAqIGFuZCBhdm9pZCB0byBoYXZlIGRpc3BsYXllZCBhdCB0aGUgc2FtZSB0aW1lXG4gICAgICAgICAgICovXG4gICAgICAgICAgc2V0VGltZW91dCgoKSA9PiB7XG4gICAgICAgICAgICAkKHNlbGYubW9kdWxlSW1wb3J0U3RhcnRTZWxlY3RvcikuZmFkZUluKCgpID0+IHtcbiAgICAgICAgICAgICAgJChzZWxmLm1vZHVsZUltcG9ydEZhaWx1cmVNc2dEZXRhaWxzU2VsZWN0b3IpLmhpZGUoKTtcbiAgICAgICAgICAgICAgJChzZWxmLm1vZHVsZUltcG9ydFN1Y2Nlc3NDb25maWd1cmVCdG5TZWxlY3RvcikuaGlkZSgpO1xuICAgICAgICAgICAgICBkcm9wem9uZS5yZW1vdmVBdHRyKCdzdHlsZScpO1xuICAgICAgICAgICAgfSk7XG4gICAgICAgICAgfSwgNTUwKTtcbiAgICAgICAgfSk7XG4gICAgICB9XG4gICAgKTtcblxuICAgIC8vIFJlaW5pdCBtb2RhbCBvbiBleGl0LCBidXQgY2hlY2sgaWYgbm90IGFscmVhZHkgcHJvY2Vzc2luZyBzb21ldGhpbmdcbiAgICBib2R5Lm9uKCdoaWRkZW4uYnMubW9kYWwnLCB0aGlzLmRyb3Bab25lTW9kYWxTZWxlY3RvciwgKCkgPT4ge1xuICAgICAgJChgJHtzZWxmLm1vZHVsZUltcG9ydFN1Y2Nlc3NTZWxlY3Rvcn0sICR7c2VsZi5tb2R1bGVJbXBvcnRGYWlsdXJlU2VsZWN0b3J9YCkuaGlkZSgpO1xuICAgICAgJChzZWxmLm1vZHVsZUltcG9ydFN0YXJ0U2VsZWN0b3IpLnNob3coKTtcblxuICAgICAgZHJvcHpvbmUucmVtb3ZlQXR0cignc3R5bGUnKTtcbiAgICAgICQoc2VsZi5tb2R1bGVJbXBvcnRGYWlsdXJlTXNnRGV0YWlsc1NlbGVjdG9yKS5oaWRlKCk7XG4gICAgICAkKHNlbGYubW9kdWxlSW1wb3J0U3VjY2Vzc0NvbmZpZ3VyZUJ0blNlbGVjdG9yKS5oaWRlKCk7XG4gICAgICAkKHNlbGYuZHJvcFpvbmVNb2RhbEZvb3RlclNlbGVjdG9yKS5odG1sKCcnKTtcbiAgICAgICQoc2VsZi5tb2R1bGVJbXBvcnRDb25maXJtU2VsZWN0b3IpLmhpZGUoKTtcbiAgICB9KTtcblxuICAgIC8vIENoYW5nZSB0aGUgd2F5IERyb3B6b25lLmpzIGxpYiBoYW5kbGUgZmlsZSBpbnB1dCB0cmlnZ2VyXG4gICAgYm9keS5vbihcbiAgICAgICdjbGljaycsXG4gICAgICBgLmRyb3B6b25lOm5vdCgke3RoaXMubW9kdWxlSW1wb3J0U2VsZWN0RmlsZU1hbnVhbFNlbGVjdG9yfSwgJHt0aGlzLm1vZHVsZUltcG9ydFN1Y2Nlc3NDb25maWd1cmVCdG5TZWxlY3Rvcn0pYCxcbiAgICAgIChldmVudCwgbWFudWFsU2VsZWN0KSA9PiB7XG4gICAgICAgIC8vIGlmIGNsaWNrIGNvbWVzIGZyb20gLm1vZHVsZS1pbXBvcnQtc3RhcnQtc2VsZWN0LW1hbnVhbCwgc3RvcCBldmVyeXRoaW5nXG4gICAgICAgIGlmICh0eXBlb2YgbWFudWFsU2VsZWN0ID09PSAndW5kZWZpbmVkJykge1xuICAgICAgICAgIGV2ZW50LnN0b3BQcm9wYWdhdGlvbigpO1xuICAgICAgICAgIGV2ZW50LnByZXZlbnREZWZhdWx0KCk7XG4gICAgICAgIH1cbiAgICAgIH1cbiAgICApO1xuXG4gICAgYm9keS5vbignY2xpY2snLCB0aGlzLm1vZHVsZUltcG9ydFNlbGVjdEZpbGVNYW51YWxTZWxlY3RvciwgKGV2ZW50KSA9PiB7XG4gICAgICBldmVudC5zdG9wUHJvcGFnYXRpb24oKTtcbiAgICAgIGV2ZW50LnByZXZlbnREZWZhdWx0KCk7XG4gICAgICAvKipcbiAgICAgICAqIFRyaWdnZXIgY2xpY2sgb24gaGlkZGVuIGZpbGUgaW5wdXQsIGFuZCBwYXNzIGV4dHJhIGRhdGFcbiAgICAgICAqIHRvIC5kcm9wem9uZSBjbGljayBoYW5kbGVyIGZybyBpdCB0byBub3RpY2UgaXQgY29tZXMgZnJvbSBoZXJlXG4gICAgICAgKi9cbiAgICAgICQoJy5kei1oaWRkZW4taW5wdXQnKS50cmlnZ2VyKCdjbGljaycsIFsnbWFudWFsX3NlbGVjdCddKTtcbiAgICB9KTtcblxuICAgIC8vIEhhbmRsZSBtb2RhbCBjbG9zdXJlXG4gICAgYm9keS5vbignY2xpY2snLCB0aGlzLm1vZHVsZUltcG9ydE1vZGFsQ2xvc2VCdG4sICgpID0+IHtcbiAgICAgIGlmIChzZWxmLmlzVXBsb2FkU3RhcnRlZCAhPT0gdHJ1ZSkge1xuICAgICAgICAkKHNlbGYuZHJvcFpvbmVNb2RhbFNlbGVjdG9yKS5tb2RhbCgnaGlkZScpO1xuICAgICAgfVxuICAgIH0pO1xuXG4gICAgLy8gRml4IGlzc3VlIG9uIGNsaWNrIGNvbmZpZ3VyZSBidXR0b25cbiAgICBib2R5Lm9uKCdjbGljaycsIHRoaXMubW9kdWxlSW1wb3J0U3VjY2Vzc0NvbmZpZ3VyZUJ0blNlbGVjdG9yLCBmdW5jdGlvbiBpbml0aWFsaXplQm9keUNsaWNrT25Nb2R1bGVJbXBvcnQoZXZlbnQpIHtcbiAgICAgIGV2ZW50LnN0b3BQcm9wYWdhdGlvbigpO1xuICAgICAgZXZlbnQucHJldmVudERlZmF1bHQoKTtcbiAgICAgIHdpbmRvdy5sb2NhdGlvbiA9ICQodGhpcykuYXR0cignaHJlZicpO1xuICAgIH0pO1xuXG4gICAgLy8gT3BlbiBmYWlsdXJlIG1lc3NhZ2UgZGV0YWlscyBib3hcbiAgICBib2R5Lm9uKCdjbGljaycsIHRoaXMubW9kdWxlSW1wb3J0RmFpbHVyZURldGFpbHNCdG5TZWxlY3RvciwgKCkgPT4ge1xuICAgICAgJChzZWxmLm1vZHVsZUltcG9ydEZhaWx1cmVNc2dEZXRhaWxzU2VsZWN0b3IpLnNsaWRlRG93bigpO1xuICAgIH0pO1xuXG4gICAgLy8gQHNlZTogZHJvcHpvbmUuanNcbiAgICBjb25zdCBkcm9wem9uZU9wdGlvbnMgPSB7XG4gICAgICB1cmw6IHdpbmRvdy5tb2R1bGVVUkxzLm1vZHVsZUltcG9ydCxcbiAgICAgIGFjY2VwdGVkRmlsZXM6ICcuemlwLCAudGFyJyxcbiAgICAgIC8vIFRoZSBuYW1lIHRoYXQgd2lsbCBiZSB1c2VkIHRvIHRyYW5zZmVyIHRoZSBmaWxlXG4gICAgICBwYXJhbU5hbWU6ICdmaWxlX3VwbG9hZGVkJyxcbiAgICAgIG1heEZpbGVzaXplOiA1MCwgLy8gY2FuJ3QgYmUgZ3JlYXRlciB0aGFuIDUwTWIgYmVjYXVzZSBpdCdzIGFuIGFkZG9ucyBsaW1pdGF0aW9uXG4gICAgICB1cGxvYWRNdWx0aXBsZTogZmFsc2UsXG4gICAgICBhZGRSZW1vdmVMaW5rczogdHJ1ZSxcbiAgICAgIGRpY3REZWZhdWx0TWVzc2FnZTogJycsXG4gICAgICBoaWRkZW5JbnB1dENvbnRhaW5lcjogc2VsZi5kcm9wWm9uZUltcG9ydFpvbmVTZWxlY3RvcixcbiAgICAgIC8qKlxuICAgICAgICogQWRkIHVubGltaXRlZCB0aW1lb3V0LiBPdGhlcndpc2UgZHJvcHpvbmUgdGltZW91dCBpcyAzMCBzZWNvbmRzXG4gICAgICAgKiAgYW5kIGlmIGEgbW9kdWxlIGlzIGxvbmcgdG8gaW5zdGFsbCwgaXQgaXMgbm90IHBvc3NpYmxlIHRvIGluc3RhbGwgdGhlIG1vZHVsZS5cbiAgICAgICAqL1xuICAgICAgdGltZW91dDogMCxcbiAgICAgIGFkZGVkZmlsZTogKCkgPT4ge1xuICAgICAgICBzZWxmLmFuaW1hdGVTdGFydFVwbG9hZCgpO1xuICAgICAgfSxcbiAgICAgIHByb2Nlc3Npbmc6ICgpID0+IHtcbiAgICAgICAgLy8gTGVhdmUgaXQgZW1wdHkgc2luY2Ugd2UgZG9uJ3QgcmVxdWlyZSBhbnl0aGluZyB3aGlsZSBwcm9jZXNzaW5nIHVwbG9hZFxuICAgICAgfSxcbiAgICAgIGVycm9yOiAoZmlsZSwgbWVzc2FnZSkgPT4ge1xuICAgICAgICBzZWxmLmRpc3BsYXlPblVwbG9hZEVycm9yKG1lc3NhZ2UpO1xuICAgICAgfSxcbiAgICAgIGNvbXBsZXRlOiAoZmlsZSkgPT4ge1xuICAgICAgICBpZiAoZmlsZS5zdGF0dXMgIT09ICdlcnJvcicpIHtcbiAgICAgICAgICBjb25zdCByZXNwb25zZU9iamVjdCA9ICQucGFyc2VKU09OKGZpbGUueGhyLnJlc3BvbnNlKTtcbiAgICAgICAgICBpZiAodHlwZW9mIHJlc3BvbnNlT2JqZWN0LmlzX2NvbmZpZ3VyYWJsZSA9PT0gJ3VuZGVmaW5lZCcpIHJlc3BvbnNlT2JqZWN0LmlzX2NvbmZpZ3VyYWJsZSA9IG51bGw7XG4gICAgICAgICAgaWYgKHR5cGVvZiByZXNwb25zZU9iamVjdC5tb2R1bGVfbmFtZSA9PT0gJ3VuZGVmaW5lZCcpIHJlc3BvbnNlT2JqZWN0Lm1vZHVsZV9uYW1lID0gbnVsbDtcblxuICAgICAgICAgIHNlbGYuZGlzcGxheU9uVXBsb2FkRG9uZShyZXNwb25zZU9iamVjdCk7XG4gICAgICAgIH1cbiAgICAgICAgLy8gU3RhdGUgdGhhdCB3ZSBoYXZlIGZpbmlzaCB0aGUgcHJvY2VzcyB0byB1bmxvY2sgc29tZSBhY3Rpb25zXG4gICAgICAgIHNlbGYuaXNVcGxvYWRTdGFydGVkID0gZmFsc2U7XG4gICAgICB9LFxuICAgIH07XG5cbiAgICBkcm9wem9uZS5kcm9wem9uZSgkLmV4dGVuZChkcm9wem9uZU9wdGlvbnMpKTtcbiAgfVxuXG4gIGFuaW1hdGVTdGFydFVwbG9hZCgpIHtcbiAgICBjb25zdCBzZWxmID0gdGhpcztcbiAgICBjb25zdCBkcm9wem9uZSA9ICQoJy5kcm9wem9uZScpO1xuICAgIC8vIFN0YXRlIHRoYXQgd2Ugc3RhcnQgbW9kdWxlIHVwbG9hZFxuICAgIHNlbGYuaXNVcGxvYWRTdGFydGVkID0gdHJ1ZTtcbiAgICAkKHNlbGYubW9kdWxlSW1wb3J0U3RhcnRTZWxlY3RvcikuaGlkZSgwKTtcbiAgICBkcm9wem9uZS5jc3MoJ2JvcmRlcicsICdub25lJyk7XG4gICAgJChzZWxmLm1vZHVsZUltcG9ydFByb2Nlc3NpbmdTZWxlY3RvcikuZmFkZUluKCk7XG4gIH1cblxuICBhbmltYXRlRW5kVXBsb2FkKGNhbGxiYWNrKSB7XG4gICAgY29uc3Qgc2VsZiA9IHRoaXM7XG4gICAgJChzZWxmLm1vZHVsZUltcG9ydFByb2Nlc3NpbmdTZWxlY3RvcikuZmluaXNoKCkuZmFkZU91dChjYWxsYmFjayk7XG4gIH1cblxuICAvKipcbiAgICogTWV0aG9kIHRvIGNhbGwgZm9yIHVwbG9hZCBtb2RhbCwgd2hlbiB0aGUgYWpheCBjYWxsIHdlbnQgd2VsbC5cbiAgICpcbiAgICogQHBhcmFtIG9iamVjdCByZXN1bHQgY29udGFpbmluZyB0aGUgc2VydmVyIHJlc3BvbnNlXG4gICAqL1xuICBkaXNwbGF5T25VcGxvYWREb25lKHJlc3VsdCkge1xuICAgIGNvbnN0IHNlbGYgPSB0aGlzO1xuICAgIHNlbGYuYW5pbWF0ZUVuZFVwbG9hZCgoKSA9PiB7XG4gICAgICBpZiAocmVzdWx0LnN0YXR1cyA9PT0gdHJ1ZSkge1xuICAgICAgICBpZiAocmVzdWx0LmlzX2NvbmZpZ3VyYWJsZSA9PT0gdHJ1ZSkge1xuICAgICAgICAgIGNvbnN0IGNvbmZpZ3VyZUxpbmsgPSB3aW5kb3cubW9kdWxlVVJMcy5jb25maWd1cmF0aW9uUGFnZS5yZXBsYWNlKC86bnVtYmVyOi8sIHJlc3VsdC5tb2R1bGVfbmFtZSk7XG4gICAgICAgICAgJChzZWxmLm1vZHVsZUltcG9ydFN1Y2Nlc3NDb25maWd1cmVCdG5TZWxlY3RvcikuYXR0cignaHJlZicsIGNvbmZpZ3VyZUxpbmspO1xuICAgICAgICAgICQoc2VsZi5tb2R1bGVJbXBvcnRTdWNjZXNzQ29uZmlndXJlQnRuU2VsZWN0b3IpLnNob3coKTtcbiAgICAgICAgfVxuICAgICAgICAkKHNlbGYubW9kdWxlSW1wb3J0U3VjY2Vzc1NlbGVjdG9yKS5mYWRlSW4oKTtcbiAgICAgIH0gZWxzZSBpZiAodHlwZW9mIHJlc3VsdC5jb25maXJtYXRpb25fc3ViamVjdCAhPT0gJ3VuZGVmaW5lZCcpIHtcbiAgICAgICAgc2VsZi5kaXNwbGF5UHJlc3RhVHJ1c3RTdGVwKHJlc3VsdCk7XG4gICAgICB9IGVsc2Uge1xuICAgICAgICAkKHNlbGYubW9kdWxlSW1wb3J0RmFpbHVyZU1zZ0RldGFpbHNTZWxlY3RvcikuaHRtbChyZXN1bHQubXNnKTtcbiAgICAgICAgJChzZWxmLm1vZHVsZUltcG9ydEZhaWx1cmVTZWxlY3RvcikuZmFkZUluKCk7XG4gICAgICB9XG4gICAgfSk7XG4gIH1cblxuICAvKipcbiAgICogTWV0aG9kIHRvIGNhbGwgZm9yIHVwbG9hZCBtb2RhbCwgd2hlbiB0aGUgYWpheCBjYWxsIHdlbnQgd3Jvbmcgb3Igd2hlbiB0aGUgYWN0aW9uIHJlcXVlc3RlZCBjb3VsZCBub3RcbiAgICogc3VjY2VlZCBmb3Igc29tZSByZWFzb24uXG4gICAqXG4gICAqIEBwYXJhbSBzdHJpbmcgbWVzc2FnZSBleHBsYWluaW5nIHRoZSBlcnJvci5cbiAgICovXG4gIGRpc3BsYXlPblVwbG9hZEVycm9yKG1lc3NhZ2UpIHtcbiAgICBjb25zdCBzZWxmID0gdGhpcztcbiAgICBzZWxmLmFuaW1hdGVFbmRVcGxvYWQoKCkgPT4ge1xuICAgICAgJChzZWxmLm1vZHVsZUltcG9ydEZhaWx1cmVNc2dEZXRhaWxzU2VsZWN0b3IpLmh0bWwobWVzc2FnZSk7XG4gICAgICAkKHNlbGYubW9kdWxlSW1wb3J0RmFpbHVyZVNlbGVjdG9yKS5mYWRlSW4oKTtcbiAgICB9KTtcbiAgfVxuXG4gIC8qKlxuICAgKiBJZiBQcmVzdGFUcnVzdCBuZWVkcyB0byBiZSBjb25maXJtZWQsIHdlIGFzayBmb3IgdGhlIGNvbmZpcm1hdGlvblxuICAgKiBtb2RhbCBjb250ZW50IGFuZCB3ZSBkaXNwbGF5IGl0IGluIHRoZSBjdXJyZW50bHkgZGlzcGxheWVkIG9uZS5cbiAgICogV2UgYWxzbyBnZW5lcmF0ZSB0aGUgYWpheCBjYWxsIHRvIHRyaWdnZXIgb25jZSB3ZSBjb25maXJtIHdlIHdhbnQgdG8gaW5zdGFsbFxuICAgKiB0aGUgbW9kdWxlLlxuICAgKlxuICAgKiBAcGFyYW0gUHJldmlvdXMgc2VydmVyIHJlc3BvbnNlIHJlc3VsdFxuICAgKi9cbiAgZGlzcGxheVByZXN0YVRydXN0U3RlcChyZXN1bHQpIHtcbiAgICBjb25zdCBzZWxmID0gdGhpcztcbiAgICBjb25zdCBtb2RhbCA9IHNlbGYubW9kdWxlQ2FyZENvbnRyb2xsZXIuX3JlcGxhY2VQcmVzdGFUcnVzdFBsYWNlaG9sZGVycyhyZXN1bHQpO1xuICAgIGNvbnN0IG1vZHVsZU5hbWUgPSByZXN1bHQubW9kdWxlLmF0dHJpYnV0ZXMubmFtZTtcblxuICAgICQodGhpcy5tb2R1bGVJbXBvcnRDb25maXJtU2VsZWN0b3IpLmh0bWwobW9kYWwuZmluZCgnLm1vZGFsLWJvZHknKS5odG1sKCkpLmZhZGVJbigpO1xuICAgICQodGhpcy5kcm9wWm9uZU1vZGFsRm9vdGVyU2VsZWN0b3IpLmh0bWwobW9kYWwuZmluZCgnLm1vZGFsLWZvb3RlcicpLmh0bWwoKSkuZmFkZUluKCk7XG5cbiAgICAkKHRoaXMuZHJvcFpvbmVNb2RhbEZvb3RlclNlbGVjdG9yKS5maW5kKCcucHN0cnVzdC1pbnN0YWxsJykub2ZmKCdjbGljaycpLm9uKCdjbGljaycsICgpID0+IHtcbiAgICAgICQoc2VsZi5tb2R1bGVJbXBvcnRDb25maXJtU2VsZWN0b3IpLmhpZGUoKTtcbiAgICAgICQoc2VsZi5kcm9wWm9uZU1vZGFsRm9vdGVyU2VsZWN0b3IpLmh0bWwoJycpO1xuICAgICAgc2VsZi5hbmltYXRlU3RhcnRVcGxvYWQoKTtcblxuICAgICAgLy8gSW5zdGFsbCBhamF4IGNhbGxcbiAgICAgICQucG9zdChyZXN1bHQubW9kdWxlLmF0dHJpYnV0ZXMudXJscy5pbnN0YWxsLCB7J2FjdGlvblBhcmFtc1tjb25maXJtUHJlc3RhVHJ1c3RdJzogJzEnfSlcbiAgICAgICAuZG9uZSgoZGF0YSkgPT4ge1xuICAgICAgICAgc2VsZi5kaXNwbGF5T25VcGxvYWREb25lKGRhdGFbbW9kdWxlTmFtZV0pO1xuICAgICAgIH0pXG4gICAgICAgLmZhaWwoKGRhdGEpID0+IHtcbiAgICAgICAgIHNlbGYuZGlzcGxheU9uVXBsb2FkRXJyb3IoZGF0YVttb2R1bGVOYW1lXSk7XG4gICAgICAgfSlcbiAgICAgICAuYWx3YXlzKCgpID0+IHtcbiAgICAgICAgIHNlbGYuaXNVcGxvYWRTdGFydGVkID0gZmFsc2U7XG4gICAgICAgfSk7XG4gICAgfSk7XG4gIH1cblxuICBnZXRCdWxrQ2hlY2tib3hlc1NlbGVjdG9yKCkge1xuICAgIHJldHVybiB0aGlzLmN1cnJlbnREaXNwbGF5ID09PSB0aGlzLkRJU1BMQVlfR1JJRFxuICAgICAgICAgPyB0aGlzLmJ1bGtBY3Rpb25DaGVja2JveEdyaWRTZWxlY3RvclxuICAgICAgICAgOiB0aGlzLmJ1bGtBY3Rpb25DaGVja2JveExpc3RTZWxlY3RvcjtcbiAgfVxuXG5cbiAgZ2V0QnVsa0NoZWNrYm94ZXNDaGVja2VkU2VsZWN0b3IoKSB7XG4gICAgcmV0dXJuIHRoaXMuY3VycmVudERpc3BsYXkgPT09IHRoaXMuRElTUExBWV9HUklEXG4gICAgICAgICA/IHRoaXMuY2hlY2tlZEJ1bGtBY3Rpb25HcmlkU2VsZWN0b3JcbiAgICAgICAgIDogdGhpcy5jaGVja2VkQnVsa0FjdGlvbkxpc3RTZWxlY3RvcjtcbiAgfVxuXG4gIGdldE1vZHVsZUl0ZW1TZWxlY3RvcigpIHtcbiAgICByZXR1cm4gdGhpcy5jdXJyZW50RGlzcGxheSA9PT0gdGhpcy5ESVNQTEFZX0dSSURcbiAgICAgICAgID8gdGhpcy5tb2R1bGVJdGVtR3JpZFNlbGVjdG9yXG4gICAgICAgICA6IHRoaXMubW9kdWxlSXRlbUxpc3RTZWxlY3RvcjtcbiAgfVxuXG4gIC8qKlxuICAgKiBHZXQgdGhlIG1vZHVsZSBub3RpZmljYXRpb25zIGNvdW50IGFuZCBkaXNwbGF5cyBpdCBhcyBhIGJhZGdlIG9uIHRoZSBub3RpZmljYXRpb24gdGFiXG4gICAqIEByZXR1cm4gdm9pZFxuICAgKi9cbiAgZ2V0Tm90aWZpY2F0aW9uc0NvdW50KCkge1xuICAgIGNvbnN0IHNlbGYgPSB0aGlzO1xuICAgICQuZ2V0SlNPTihcbiAgICAgIHdpbmRvdy5tb2R1bGVVUkxzLm5vdGlmaWNhdGlvbnNDb3VudCxcbiAgICAgIHNlbGYudXBkYXRlTm90aWZpY2F0aW9uc0NvdW50XG4gICAgKS5mYWlsKCgpID0+IHtcbiAgICAgIGNvbnNvbGUuZXJyb3IoJ0NvdWxkIG5vdCByZXRyaWV2ZSBtb2R1bGUgbm90aWZpY2F0aW9ucyBjb3VudC4nKTtcbiAgICB9KTtcbiAgfVxuXG4gIHVwZGF0ZU5vdGlmaWNhdGlvbnNDb3VudChiYWRnZSkge1xuICAgIGNvbnN0IGRlc3RpbmF0aW9uVGFicyA9IHtcbiAgICAgIHRvX2NvbmZpZ3VyZTogJCgnI3N1YnRhYi1BZG1pbk1vZHVsZXNOb3RpZmljYXRpb25zJyksXG4gICAgICB0b191cGRhdGU6ICQoJyNzdWJ0YWItQWRtaW5Nb2R1bGVzVXBkYXRlcycpLFxuICAgIH07XG5cbiAgICBmb3IgKGxldCBrZXkgaW4gZGVzdGluYXRpb25UYWJzKSB7XG4gICAgICBpZiAoZGVzdGluYXRpb25UYWJzW2tleV0ubGVuZ3RoID09PSAwKSB7XG4gICAgICAgIGNvbnRpbnVlO1xuICAgICAgfVxuXG4gICAgICBkZXN0aW5hdGlvblRhYnNba2V5XS5maW5kKCcubm90aWZpY2F0aW9uLWNvdW50ZXInKS50ZXh0KGJhZGdlW2tleV0pO1xuICAgIH1cbiAgfVxuXG4gIGluaXRBZGRvbnNTZWFyY2goKSB7XG4gICAgY29uc3Qgc2VsZiA9IHRoaXM7XG4gICAgJCgnYm9keScpLm9uKFxuICAgICAgJ2NsaWNrJyxcbiAgICAgIGAke3NlbGYuYWRkb25JdGVtR3JpZFNlbGVjdG9yfSwgJHtzZWxmLmFkZG9uSXRlbUxpc3RTZWxlY3Rvcn1gLFxuICAgICAgKCkgPT4ge1xuICAgICAgICBsZXQgc2VhcmNoUXVlcnkgPSAnJztcbiAgICAgICAgaWYgKHNlbGYuY3VycmVudFRhZ3NMaXN0Lmxlbmd0aCkge1xuICAgICAgICAgIHNlYXJjaFF1ZXJ5ID0gZW5jb2RlVVJJQ29tcG9uZW50KHNlbGYuY3VycmVudFRhZ3NMaXN0LmpvaW4oJyAnKSk7XG4gICAgICAgIH1cblxuICAgICAgICB3aW5kb3cub3BlbihgJHtzZWxmLmJhc2VBZGRvbnNVcmx9c2VhcmNoLnBocD9zZWFyY2hfcXVlcnk9JHtzZWFyY2hRdWVyeX1gLCAnX2JsYW5rJyk7XG4gICAgICB9XG4gICAgKTtcbiAgfVxuXG4gIGluaXRDYXRlZ29yaWVzR3JpZCgpIHtcbiAgICBjb25zdCBzZWxmID0gdGhpcztcblxuICAgICQoJ2JvZHknKS5vbignY2xpY2snLCB0aGlzLmNhdGVnb3J5R3JpZEl0ZW1TZWxlY3RvciwgZnVuY3Rpb24gaW5pdGlsYWl6ZUdyaWRCb2R5Q2xpY2soZXZlbnQpIHtcbiAgICAgIGV2ZW50LnN0b3BQcm9wYWdhdGlvbigpO1xuICAgICAgZXZlbnQucHJldmVudERlZmF1bHQoKTtcbiAgICAgIGNvbnN0IHJlZkNhdGVnb3J5ID0gJCh0aGlzKS5kYXRhKCdjYXRlZ29yeS1yZWYnKTtcblxuICAgICAgLy8gSW4gY2FzZSB3ZSBoYXZlIHNvbWUgdGFncyB3ZSBuZWVkIHRvIHJlc2V0IGl0ICFcbiAgICAgIGlmIChzZWxmLmN1cnJlbnRUYWdzTGlzdC5sZW5ndGgpIHtcbiAgICAgICAgc2VsZi5wc3RhZ2dlcklucHV0LnJlc2V0VGFncyhmYWxzZSk7XG4gICAgICAgIHNlbGYuY3VycmVudFRhZ3NMaXN0ID0gW107XG4gICAgICB9XG4gICAgICBjb25zdCBtZW51Q2F0ZWdvcnlUb1RyaWdnZXIgPSAkKGAke3NlbGYuY2F0ZWdvcnlJdGVtU2VsZWN0b3J9W2RhdGEtY2F0ZWdvcnktcmVmPVwiJHtyZWZDYXRlZ29yeX1cIl1gKTtcblxuICAgICAgaWYgKCFtZW51Q2F0ZWdvcnlUb1RyaWdnZXIubGVuZ3RoKSB7XG4gICAgICAgIGNvbnNvbGUud2FybihgTm8gY2F0ZWdvcnkgd2l0aCByZWYgKCR7cmVmQ2F0ZWdvcnl9KSBzZWVtcyB0byBleGlzdCFgKTtcbiAgICAgICAgcmV0dXJuIGZhbHNlO1xuICAgICAgfVxuXG4gICAgICAvLyBIaWRlIGN1cnJlbnQgY2F0ZWdvcnkgZ3JpZFxuICAgICAgaWYgKHNlbGYuaXNDYXRlZ29yeUdyaWREaXNwbGF5ZWQgPT09IHRydWUpIHtcbiAgICAgICAgJChzZWxmLmNhdGVnb3J5R3JpZFNlbGVjdG9yKS5mYWRlT3V0KCk7XG4gICAgICAgIHNlbGYuaXNDYXRlZ29yeUdyaWREaXNwbGF5ZWQgPSBmYWxzZTtcbiAgICAgIH1cblxuICAgICAgLy8gVHJpZ2dlciBjbGljayBvbiByaWdodCBjYXRlZ29yeVxuICAgICAgJChgJHtzZWxmLmNhdGVnb3J5SXRlbVNlbGVjdG9yfVtkYXRhLWNhdGVnb3J5LXJlZj1cIiR7cmVmQ2F0ZWdvcnl9XCJdYCkuY2xpY2soKTtcbiAgICAgIHJldHVybiB0cnVlO1xuICAgIH0pO1xuICB9XG5cbiAgaW5pdEN1cnJlbnREaXNwbGF5KCkge1xuICAgIHRoaXMuY3VycmVudERpc3BsYXkgPSB0aGlzLmN1cnJlbnREaXNwbGF5ID09PSAnJyA/IHRoaXMuRElTUExBWV9MSVNUIDogdGhpcy5ESVNQTEFZX0dSSUQ7XG4gIH1cblxuICBpbml0U29ydGluZ0Ryb3Bkb3duKCkge1xuICAgIGNvbnN0IHNlbGYgPSB0aGlzO1xuXG4gICAgc2VsZi5jdXJyZW50U29ydGluZyA9ICQodGhpcy5tb2R1bGVTb3J0aW5nRHJvcGRvd25TZWxlY3RvcikuZmluZCgnOmNoZWNrZWQnKS5hdHRyKCd2YWx1ZScpO1xuICAgIGlmICghc2VsZi5jdXJyZW50U29ydGluZykge1xuICAgICAgc2VsZi5jdXJyZW50U29ydGluZyA9ICdhY2Nlc3MtZGVzYyc7XG4gICAgfVxuXG4gICAgJCgnYm9keScpLm9uKFxuICAgICAgJ2NoYW5nZScsXG4gICAgICBzZWxmLm1vZHVsZVNvcnRpbmdEcm9wZG93blNlbGVjdG9yLFxuICAgICAgZnVuY3Rpb24gaW5pdGlhbGl6ZUJvZHlTb3J0aW5nQ2hhbmdlKCkge1xuICAgICAgICBzZWxmLmN1cnJlbnRTb3J0aW5nID0gJCh0aGlzKS5maW5kKCc6Y2hlY2tlZCcpLmF0dHIoJ3ZhbHVlJyk7XG4gICAgICAgIHNlbGYudXBkYXRlTW9kdWxlVmlzaWJpbGl0eSgpO1xuICAgICAgfVxuICAgICk7XG4gIH1cblxuICBkb0J1bGtBY3Rpb24ocmVxdWVzdGVkQnVsa0FjdGlvbikge1xuICAgIC8vIFRoaXMgb2JqZWN0IGlzIHVzZWQgdG8gY2hlY2sgaWYgcmVxdWVzdGVkIGJ1bGtBY3Rpb24gaXMgYXZhaWxhYmxlIGFuZCBnaXZlIHByb3BlclxuICAgIC8vIHVybCBzZWdtZW50IHRvIGJlIGNhbGxlZCBmb3IgaXRcbiAgICBjb25zdCBmb3JjZURlbGV0aW9uID0gJCgnI2ZvcmNlX2J1bGtfZGVsZXRpb24nKS5wcm9wKCdjaGVja2VkJyk7XG5cbiAgICBjb25zdCBidWxrQWN0aW9uVG9VcmwgPSB7XG4gICAgICAnYnVsay11bmluc3RhbGwnOiAndW5pbnN0YWxsJyxcbiAgICAgICdidWxrLWRpc2FibGUnOiAnZGlzYWJsZScsXG4gICAgICAnYnVsay1lbmFibGUnOiAnZW5hYmxlJyxcbiAgICAgICdidWxrLWRpc2FibGUtbW9iaWxlJzogJ2Rpc2FibGVfbW9iaWxlJyxcbiAgICAgICdidWxrLWVuYWJsZS1tb2JpbGUnOiAnZW5hYmxlX21vYmlsZScsXG4gICAgICAnYnVsay1yZXNldCc6ICdyZXNldCcsXG4gICAgfTtcblxuICAgIC8vIE5vdGUgbm8gZ3JpZCBzZWxlY3RvciB1c2VkIHlldCBzaW5jZSB3ZSBkbyBub3QgbmVlZGVkIGl0IGF0IGRldiB0aW1lXG4gICAgLy8gTWF5YmUgdXNlZnVsIHRvIGltcGxlbWVudCB0aGlzIGtpbmQgb2YgdGhpbmdzIGxhdGVyIGlmIGludGVuZGVkIHRvXG4gICAgLy8gdXNlIHRoaXMgZnVuY3Rpb25hbGl0eSBlbHNld2hlcmUgYnV0IFwibWFuYWdlIG15IG1vZHVsZVwiIHNlY3Rpb25cbiAgICBpZiAodHlwZW9mIGJ1bGtBY3Rpb25Ub1VybFtyZXF1ZXN0ZWRCdWxrQWN0aW9uXSA9PT0gJ3VuZGVmaW5lZCcpIHtcbiAgICAgICQuZ3Jvd2wuZXJyb3Ioe21lc3NhZ2U6IHdpbmRvdy50cmFuc2xhdGVfamF2YXNjcmlwdHNbJ0J1bGsgQWN0aW9uIC0gUmVxdWVzdCBub3QgZm91bmQnXS5yZXBsYWNlKCdbMV0nLCByZXF1ZXN0ZWRCdWxrQWN0aW9uKX0pO1xuICAgICAgcmV0dXJuIGZhbHNlO1xuICAgIH1cblxuICAgIC8vIExvb3Agb3ZlciBhbGwgY2hlY2tlZCBidWxrIGNoZWNrYm94ZXNcbiAgICBjb25zdCBidWxrQWN0aW9uU2VsZWN0ZWRTZWxlY3RvciA9IHRoaXMuZ2V0QnVsa0NoZWNrYm94ZXNDaGVja2VkU2VsZWN0b3IoKTtcbiAgICBjb25zdCBidWxrTW9kdWxlQWN0aW9uID0gYnVsa0FjdGlvblRvVXJsW3JlcXVlc3RlZEJ1bGtBY3Rpb25dO1xuXG4gICAgaWYgKCQoYnVsa0FjdGlvblNlbGVjdGVkU2VsZWN0b3IpLmxlbmd0aCA8PSAwKSB7XG4gICAgICBjb25zb2xlLndhcm4od2luZG93LnRyYW5zbGF0ZV9qYXZhc2NyaXB0c1snQnVsayBBY3Rpb24gLSBPbmUgbW9kdWxlIG1pbmltdW0nXSk7XG4gICAgICByZXR1cm4gZmFsc2U7XG4gICAgfVxuXG4gICAgY29uc3QgbW9kdWxlc0FjdGlvbnMgPSBbXTtcbiAgICBsZXQgbW9kdWxlVGVjaE5hbWU7XG4gICAgJChidWxrQWN0aW9uU2VsZWN0ZWRTZWxlY3RvcikuZWFjaChmdW5jdGlvbiBidWxrQWN0aW9uU2VsZWN0b3IoKSB7XG4gICAgICBtb2R1bGVUZWNoTmFtZSA9ICQodGhpcykuZGF0YSgndGVjaC1uYW1lJyk7XG4gICAgICBtb2R1bGVzQWN0aW9ucy5wdXNoKHtcbiAgICAgICAgdGVjaE5hbWU6IG1vZHVsZVRlY2hOYW1lLFxuICAgICAgICBhY3Rpb25NZW51T2JqOiAkKHRoaXMpLmNsb3Nlc3QoJy5tb2R1bGUtY2hlY2tib3gtYnVsay1saXN0JykubmV4dCgpLFxuICAgICAgfSk7XG4gICAgfSk7XG5cbiAgICB0aGlzLnBlcmZvcm1Nb2R1bGVzQWN0aW9uKG1vZHVsZXNBY3Rpb25zLCBidWxrTW9kdWxlQWN0aW9uLCBmb3JjZURlbGV0aW9uKTtcblxuICAgIHJldHVybiB0cnVlO1xuICB9XG5cbiAgcGVyZm9ybU1vZHVsZXNBY3Rpb24obW9kdWxlc0FjdGlvbnMsIGJ1bGtNb2R1bGVBY3Rpb24sIGZvcmNlRGVsZXRpb24pIHtcbiAgICBjb25zdCBzZWxmID0gdGhpcztcbiAgICBpZiAodHlwZW9mIHNlbGYubW9kdWxlQ2FyZENvbnRyb2xsZXIgPT09ICd1bmRlZmluZWQnKSB7XG4gICAgICByZXR1cm47XG4gICAgfVxuXG4gICAgLy9GaXJzdCBsZXQncyBmaWx0ZXIgbW9kdWxlcyB0aGF0IGNhbid0IHBlcmZvcm0gdGhpcyBhY3Rpb25cbiAgICBsZXQgYWN0aW9uTWVudUxpbmtzID0gZmlsdGVyQWxsb3dlZEFjdGlvbnMobW9kdWxlc0FjdGlvbnMpO1xuICAgIGlmICghYWN0aW9uTWVudUxpbmtzLmxlbmd0aCkge1xuICAgICAgcmV0dXJuO1xuICAgIH1cblxuICAgIGxldCBtb2R1bGVzUmVxdWVzdGVkQ291bnRkb3duID0gYWN0aW9uTWVudUxpbmtzLmxlbmd0aCAtIDE7XG4gICAgbGV0IHNwaW5uZXJPYmogPSAkKFwiPGJ1dHRvbiBjbGFzcz1cXFwiYnRuLXByaW1hcnktcmV2ZXJzZSBvbmNsaWNrIHVuYmluZCBzcGlubmVyIFxcXCI+PC9idXR0b24+XCIpO1xuICAgIGlmIChhY3Rpb25NZW51TGlua3MubGVuZ3RoID4gMSkge1xuICAgICAgLy9Mb29wIHRocm91Z2ggYWxsIHRoZSBtb2R1bGVzIGV4Y2VwdCB0aGUgbGFzdCBvbmUgd2hpY2ggd2FpdHMgZm9yIG90aGVyXG4gICAgICAvL3JlcXVlc3RzIGFuZCB0aGVuIGNhbGwgaXRzIHJlcXVlc3Qgd2l0aCBjYWNoZSBjbGVhciBlbmFibGVkXG4gICAgICAkLmVhY2goYWN0aW9uTWVudUxpbmtzLCBmdW5jdGlvbiBidWxrTW9kdWxlc0xvb3AoaW5kZXgsIGFjdGlvbk1lbnVMaW5rKSB7XG4gICAgICAgIGlmIChpbmRleCA+PSBhY3Rpb25NZW51TGlua3MubGVuZ3RoIC0gMSkge1xuICAgICAgICAgIHJldHVybjtcbiAgICAgICAgfVxuICAgICAgICByZXF1ZXN0TW9kdWxlQWN0aW9uKGFjdGlvbk1lbnVMaW5rLCB0cnVlLCBjb3VudGRvd25Nb2R1bGVzUmVxdWVzdCk7XG4gICAgICB9KTtcbiAgICAgIC8vRGlzcGxheSBhIHNwaW5uZXIgZm9yIHRoZSBsYXN0IG1vZHVsZVxuICAgICAgY29uc3QgbGFzdE1lbnVMaW5rID0gYWN0aW9uTWVudUxpbmtzW2FjdGlvbk1lbnVMaW5rcy5sZW5ndGggLSAxXTtcbiAgICAgIGNvbnN0IGFjdGlvbk1lbnVPYmogPSBsYXN0TWVudUxpbmsuY2xvc2VzdChzZWxmLm1vZHVsZUNhcmRDb250cm9sbGVyLm1vZHVsZUl0ZW1BY3Rpb25zU2VsZWN0b3IpO1xuICAgICAgYWN0aW9uTWVudU9iai5oaWRlKCk7XG4gICAgICBhY3Rpb25NZW51T2JqLmFmdGVyKHNwaW5uZXJPYmopO1xuICAgIH0gZWxzZSB7XG4gICAgICByZXF1ZXN0TW9kdWxlQWN0aW9uKGFjdGlvbk1lbnVMaW5rc1swXSk7XG4gICAgfVxuXG4gICAgZnVuY3Rpb24gcmVxdWVzdE1vZHVsZUFjdGlvbihhY3Rpb25NZW51TGluaywgZGlzYWJsZUNhY2hlQ2xlYXIsIHJlcXVlc3RFbmRDYWxsYmFjaykge1xuICAgICAgc2VsZi5tb2R1bGVDYXJkQ29udHJvbGxlci5fcmVxdWVzdFRvQ29udHJvbGxlcihcbiAgICAgICAgYnVsa01vZHVsZUFjdGlvbixcbiAgICAgICAgYWN0aW9uTWVudUxpbmssXG4gICAgICAgIGZvcmNlRGVsZXRpb24sXG4gICAgICAgIGRpc2FibGVDYWNoZUNsZWFyLFxuICAgICAgICByZXF1ZXN0RW5kQ2FsbGJhY2tcbiAgICAgICk7XG4gICAgfVxuXG4gICAgZnVuY3Rpb24gY291bnRkb3duTW9kdWxlc1JlcXVlc3QoKSB7XG4gICAgICBtb2R1bGVzUmVxdWVzdGVkQ291bnRkb3duLS07XG4gICAgICAvL05vdyB0aGF0IGFsbCBvdGhlciBtb2R1bGVzIGhhdmUgcGVyZm9ybWVkIHRoZWlyIGFjdGlvbiBXSVRIT1VUIGNhY2hlIGNsZWFyLCB3ZVxuICAgICAgLy9jYW4gcmVxdWVzdCB0aGUgbGFzdCBtb2R1bGUgcmVxdWVzdCBXSVRIIGNhY2hlIGNsZWFyXG4gICAgICBpZiAobW9kdWxlc1JlcXVlc3RlZENvdW50ZG93biA8PSAwKSB7XG4gICAgICAgIGlmIChzcGlubmVyT2JqKSB7XG4gICAgICAgICAgc3Bpbm5lck9iai5yZW1vdmUoKTtcbiAgICAgICAgICBzcGlubmVyT2JqID0gbnVsbDtcbiAgICAgICAgfVxuXG4gICAgICAgIGNvbnN0IGxhc3RNZW51TGluayA9IGFjdGlvbk1lbnVMaW5rc1thY3Rpb25NZW51TGlua3MubGVuZ3RoIC0gMV07XG4gICAgICAgIGNvbnN0IGFjdGlvbk1lbnVPYmogPSBsYXN0TWVudUxpbmsuY2xvc2VzdChzZWxmLm1vZHVsZUNhcmRDb250cm9sbGVyLm1vZHVsZUl0ZW1BY3Rpb25zU2VsZWN0b3IpO1xuICAgICAgICBhY3Rpb25NZW51T2JqLmZhZGVJbigpO1xuICAgICAgICByZXF1ZXN0TW9kdWxlQWN0aW9uKGxhc3RNZW51TGluayk7XG4gICAgICB9XG4gICAgfVxuXG4gICAgZnVuY3Rpb24gZmlsdGVyQWxsb3dlZEFjdGlvbnMobW9kdWxlc0FjdGlvbnMpIHtcbiAgICAgIGxldCBhY3Rpb25NZW51TGlua3MgPSBbXTtcbiAgICAgIGxldCBhY3Rpb25NZW51TGluaztcbiAgICAgICQuZWFjaChtb2R1bGVzQWN0aW9ucywgZnVuY3Rpb24gZmlsdGVyQWxsb3dlZE1vZHVsZXMoaW5kZXgsIG1vZHVsZURhdGEpIHtcbiAgICAgICAgYWN0aW9uTWVudUxpbmsgPSAkKFxuICAgICAgICAgIHNlbGYubW9kdWxlQ2FyZENvbnRyb2xsZXIubW9kdWxlQWN0aW9uTWVudUxpbmtTZWxlY3RvciArIGJ1bGtNb2R1bGVBY3Rpb24sXG4gICAgICAgICAgbW9kdWxlRGF0YS5hY3Rpb25NZW51T2JqXG4gICAgICAgICk7XG4gICAgICAgIGlmIChhY3Rpb25NZW51TGluay5sZW5ndGggPiAwKSB7XG4gICAgICAgICAgYWN0aW9uTWVudUxpbmtzLnB1c2goYWN0aW9uTWVudUxpbmspO1xuICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICQuZ3Jvd2wuZXJyb3Ioe21lc3NhZ2U6IHdpbmRvdy50cmFuc2xhdGVfamF2YXNjcmlwdHNbJ0J1bGsgQWN0aW9uIC0gUmVxdWVzdCBub3QgYXZhaWxhYmxlIGZvciBtb2R1bGUnXVxuICAgICAgICAgICAgICAucmVwbGFjZSgnWzFdJywgYnVsa01vZHVsZUFjdGlvbilcbiAgICAgICAgICAgICAgLnJlcGxhY2UoJ1syXScsIG1vZHVsZURhdGEudGVjaE5hbWUpfSk7XG4gICAgICAgIH1cbiAgICAgIH0pO1xuXG4gICAgICByZXR1cm4gYWN0aW9uTWVudUxpbmtzO1xuICAgIH1cbiAgfVxuXG4gIGluaXRBY3Rpb25CdXR0b25zKCkge1xuICAgIGNvbnN0IHNlbGYgPSB0aGlzO1xuICAgICQoJ2JvZHknKS5vbihcbiAgICAgICdjbGljaycsXG4gICAgICBzZWxmLm1vZHVsZUluc3RhbGxCdG5TZWxlY3RvcixcbiAgICAgIGZ1bmN0aW9uIGluaXRpYWxpemVBY3Rpb25CdXR0b25zQ2xpY2soZXZlbnQpIHtcbiAgICAgICAgY29uc3QgJHRoaXMgPSAkKHRoaXMpO1xuICAgICAgICBjb25zdCAkbmV4dCA9ICQoJHRoaXMubmV4dCgpKTtcbiAgICAgICAgZXZlbnQucHJldmVudERlZmF1bHQoKTtcblxuICAgICAgICAkdGhpcy5oaWRlKCk7XG4gICAgICAgICRuZXh0LnNob3coKTtcblxuICAgICAgICAkLmFqYXgoe1xuICAgICAgICAgIHVybDogJHRoaXMuZGF0YSgndXJsJyksXG4gICAgICAgICAgZGF0YVR5cGU6ICdqc29uJyxcbiAgICAgICAgfSkuZG9uZSgoKSA9PiB7XG4gICAgICAgICAgJG5leHQuZmFkZU91dCgpO1xuICAgICAgICB9KTtcbiAgICAgIH1cbiAgICApO1xuXG4gICAgLy8gXCJVcGdyYWRlIEFsbFwiIGJ1dHRvbiBoYW5kbGVyXG4gICAgJCgnYm9keScpLm9uKCdjbGljaycsIHNlbGYudXBncmFkZUFsbFNvdXJjZSwgKGV2ZW50KSA9PiB7XG4gICAgICBldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xuXG4gICAgICBpZiAoJChzZWxmLnVwZ3JhZGVBbGxUYXJnZXRzKS5sZW5ndGggPD0gMCkge1xuICAgICAgICBjb25zb2xlLndhcm4od2luZG93LnRyYW5zbGF0ZV9qYXZhc2NyaXB0c1snVXBncmFkZSBBbGwgQWN0aW9uIC0gT25lIG1vZHVsZSBtaW5pbXVtJ10pO1xuICAgICAgICByZXR1cm4gZmFsc2U7XG4gICAgICB9XG5cbiAgICAgIGNvbnN0IG1vZHVsZXNBY3Rpb25zID0gW107XG4gICAgICBsZXQgbW9kdWxlVGVjaE5hbWU7XG4gICAgICAkKHNlbGYudXBncmFkZUFsbFRhcmdldHMpLmVhY2goZnVuY3Rpb24gYnVsa0FjdGlvblNlbGVjdG9yKCkge1xuICAgICAgICBjb25zdCBtb2R1bGVJdGVtTGlzdCA9ICQodGhpcykuY2xvc2VzdCgnLm1vZHVsZS1pdGVtLWxpc3QnKTtcbiAgICAgICAgbW9kdWxlVGVjaE5hbWUgPSBtb2R1bGVJdGVtTGlzdC5kYXRhKCd0ZWNoLW5hbWUnKTtcbiAgICAgICAgbW9kdWxlc0FjdGlvbnMucHVzaCh7XG4gICAgICAgICAgdGVjaE5hbWU6IG1vZHVsZVRlY2hOYW1lLFxuICAgICAgICAgIGFjdGlvbk1lbnVPYmo6ICQoJy5tb2R1bGUtYWN0aW9ucycsIG1vZHVsZUl0ZW1MaXN0KSxcbiAgICAgICAgfSk7XG4gICAgICB9KTtcblxuICAgICAgdGhpcy5wZXJmb3JtTW9kdWxlc0FjdGlvbihtb2R1bGVzQWN0aW9ucywgJ3VwZ3JhZGUnKTtcblxuICAgICAgcmV0dXJuIHRydWU7XG4gICAgfSk7XG4gIH1cblxuICBpbml0Q2F0ZWdvcnlTZWxlY3QoKSB7XG4gICAgY29uc3Qgc2VsZiA9IHRoaXM7XG4gICAgY29uc3QgYm9keSA9ICQoJ2JvZHknKTtcbiAgICBib2R5Lm9uKFxuICAgICAgJ2NsaWNrJyxcbiAgICAgIHNlbGYuY2F0ZWdvcnlJdGVtU2VsZWN0b3IsXG4gICAgICBmdW5jdGlvbiBpbml0aWFsaXplQ2F0ZWdvcnlTZWxlY3RDbGljaygpIHtcbiAgICAgICAgLy8gR2V0IGRhdGEgZnJvbSBsaSBET00gaW5wdXRcbiAgICAgICAgc2VsZi5jdXJyZW50UmVmQ2F0ZWdvcnkgPSAkKHRoaXMpLmRhdGEoJ2NhdGVnb3J5LXJlZicpO1xuICAgICAgICBzZWxmLmN1cnJlbnRSZWZDYXRlZ29yeSA9IHNlbGYuY3VycmVudFJlZkNhdGVnb3J5ID8gU3RyaW5nKHNlbGYuY3VycmVudFJlZkNhdGVnb3J5KS50b0xvd2VyQ2FzZSgpIDogbnVsbDtcbiAgICAgICAgLy8gQ2hhbmdlIGRyb3Bkb3duIGxhYmVsIHRvIHNldCBpdCB0byB0aGUgY3VycmVudCBjYXRlZ29yeSdzIGRpc3BsYXluYW1lXG4gICAgICAgICQoc2VsZi5jYXRlZ29yeVNlbGVjdG9yTGFiZWxTZWxlY3RvcikudGV4dCgkKHRoaXMpLmRhdGEoJ2NhdGVnb3J5LWRpc3BsYXktbmFtZScpKTtcbiAgICAgICAgJChzZWxmLmNhdGVnb3J5UmVzZXRCdG5TZWxlY3Rvcikuc2hvdygpO1xuICAgICAgICBzZWxmLnVwZGF0ZU1vZHVsZVZpc2liaWxpdHkoKTtcbiAgICAgIH1cbiAgICApO1xuXG4gICAgYm9keS5vbihcbiAgICAgICdjbGljaycsXG4gICAgICBzZWxmLmNhdGVnb3J5UmVzZXRCdG5TZWxlY3RvcixcbiAgICAgIGZ1bmN0aW9uIGluaXRpYWxpemVDYXRlZ29yeVJlc2V0QnV0dG9uQ2xpY2soKSB7XG4gICAgICAgIGNvbnN0IHJhd1RleHQgPSAkKHNlbGYuY2F0ZWdvcnlTZWxlY3RvcikuYXR0cignYXJpYS1sYWJlbGxlZGJ5Jyk7XG4gICAgICAgIGNvbnN0IHVwcGVyRmlyc3RMZXR0ZXIgPSByYXdUZXh0LmNoYXJBdCgwKS50b1VwcGVyQ2FzZSgpO1xuICAgICAgICBjb25zdCByZW1vdmVkRmlyc3RMZXR0ZXIgPSByYXdUZXh0LnNsaWNlKDEpO1xuICAgICAgICBjb25zdCBvcmlnaW5hbFRleHQgPSB1cHBlckZpcnN0TGV0dGVyICsgcmVtb3ZlZEZpcnN0TGV0dGVyO1xuXG4gICAgICAgICQoc2VsZi5jYXRlZ29yeVNlbGVjdG9yTGFiZWxTZWxlY3RvcikudGV4dChvcmlnaW5hbFRleHQpO1xuICAgICAgICAkKHRoaXMpLmhpZGUoKTtcbiAgICAgICAgc2VsZi5jdXJyZW50UmVmQ2F0ZWdvcnkgPSBudWxsO1xuICAgICAgICBzZWxmLnVwZGF0ZU1vZHVsZVZpc2liaWxpdHkoKTtcbiAgICAgIH1cbiAgICApO1xuICB9XG5cbiAgaW5pdFNlYXJjaEJsb2NrKCkge1xuICAgIGNvbnN0IHNlbGYgPSB0aGlzO1xuICAgIHNlbGYucHN0YWdnZXJJbnB1dCA9ICQoJyNtb2R1bGUtc2VhcmNoLWJhcicpLnBzdGFnZ2VyKHtcbiAgICAgIG9uVGFnc0NoYW5nZWQ6ICh0YWdMaXN0KSA9PiB7XG4gICAgICAgIHNlbGYuY3VycmVudFRhZ3NMaXN0ID0gdGFnTGlzdDtcbiAgICAgICAgc2VsZi51cGRhdGVNb2R1bGVWaXNpYmlsaXR5KCk7XG4gICAgICB9LFxuICAgICAgb25SZXNldFRhZ3M6ICgpID0+IHtcbiAgICAgICAgc2VsZi5jdXJyZW50VGFnc0xpc3QgPSBbXTtcbiAgICAgICAgc2VsZi51cGRhdGVNb2R1bGVWaXNpYmlsaXR5KCk7XG4gICAgICB9LFxuICAgICAgaW5wdXRQbGFjZWhvbGRlcjogd2luZG93LnRyYW5zbGF0ZV9qYXZhc2NyaXB0c1snU2VhcmNoIC0gcGxhY2Vob2xkZXInXSxcbiAgICAgIGNsb3NpbmdDcm9zczogdHJ1ZSxcbiAgICAgIGNvbnRleHQ6IHNlbGYsXG4gICAgfSk7XG5cbiAgICAkKCdib2R5Jykub24oJ2NsaWNrJywgJy5tb2R1bGUtYWRkb25zLXNlYXJjaC1saW5rJywgKGV2ZW50KSA9PiB7XG4gICAgICBldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgZXZlbnQuc3RvcFByb3BhZ2F0aW9uKCk7XG4gICAgICB3aW5kb3cub3BlbigkKHRoaXMpLmF0dHIoJ2hyZWYnKSwgJ19ibGFuaycpO1xuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIEluaXRpYWxpemUgZGlzcGxheSBzd2l0Y2hpbmcgYmV0d2VlbiBMaXN0IG9yIEdyaWRcbiAgICovXG4gIGluaXRTb3J0aW5nRGlzcGxheVN3aXRjaCgpIHtcbiAgICBjb25zdCBzZWxmID0gdGhpcztcblxuICAgICQoJ2JvZHknKS5vbihcbiAgICAgICdjbGljaycsXG4gICAgICAnLm1vZHVsZS1zb3J0LXN3aXRjaCcsXG4gICAgICBmdW5jdGlvbiBzd2l0Y2hTb3J0KCkge1xuICAgICAgICBjb25zdCBzd2l0Y2hUbyA9ICQodGhpcykuZGF0YSgnc3dpdGNoJyk7XG4gICAgICAgIGNvbnN0IGlzQWxyZWFkeURpc3BsYXllZCA9ICQodGhpcykuaGFzQ2xhc3MoJ2FjdGl2ZS1kaXNwbGF5Jyk7XG4gICAgICAgIGlmICh0eXBlb2Ygc3dpdGNoVG8gIT09ICd1bmRlZmluZWQnICYmIGlzQWxyZWFkeURpc3BsYXllZCA9PT0gZmFsc2UpIHtcbiAgICAgICAgICBzZWxmLnN3aXRjaFNvcnRpbmdEaXNwbGF5VG8oc3dpdGNoVG8pO1xuICAgICAgICAgIHNlbGYuY3VycmVudERpc3BsYXkgPSBzd2l0Y2hUbztcbiAgICAgICAgfVxuICAgICAgfVxuICAgICk7XG4gIH1cblxuICBzd2l0Y2hTb3J0aW5nRGlzcGxheVRvKHN3aXRjaFRvKSB7XG4gICAgaWYgKHN3aXRjaFRvICE9PSB0aGlzLkRJU1BMQVlfR1JJRCAmJiBzd2l0Y2hUbyAhPT0gdGhpcy5ESVNQTEFZX0xJU1QpIHtcbiAgICAgIGNvbnNvbGUuZXJyb3IoYENhbid0IHN3aXRjaCB0byB1bmRlZmluZWQgZGlzcGxheSBwcm9wZXJ0eSBcIiR7c3dpdGNoVG99XCJgKTtcbiAgICAgIHJldHVybjtcbiAgICB9XG5cbiAgICAkKCcubW9kdWxlLXNvcnQtc3dpdGNoJykucmVtb3ZlQ2xhc3MoJ21vZHVsZS1zb3J0LWFjdGl2ZScpO1xuICAgICQoYCNtb2R1bGUtc29ydC0ke3N3aXRjaFRvfWApLmFkZENsYXNzKCdtb2R1bGUtc29ydC1hY3RpdmUnKTtcbiAgICB0aGlzLmN1cnJlbnREaXNwbGF5ID0gc3dpdGNoVG87XG4gICAgdGhpcy51cGRhdGVNb2R1bGVWaXNpYmlsaXR5KCk7XG4gIH1cblxuICBpbml0aWFsaXplU2VlTW9yZSgpIHtcbiAgICBjb25zdCBzZWxmID0gdGhpcztcblxuICAgICQoYCR7c2VsZi5tb2R1bGVTaG9ydExpc3R9ICR7c2VsZi5zZWVNb3JlU2VsZWN0b3J9YCkub24oJ2NsaWNrJywgZnVuY3Rpb24gc2VlTW9yZSgpIHtcbiAgICAgIHNlbGYuY3VycmVudENhdGVnb3J5RGlzcGxheVskKHRoaXMpLmRhdGEoJ2NhdGVnb3J5JyldID0gdHJ1ZTtcbiAgICAgICQodGhpcykuYWRkQ2xhc3MoJ2Qtbm9uZScpO1xuICAgICAgJCh0aGlzKS5jbG9zZXN0KHNlbGYubW9kdWxlU2hvcnRMaXN0KS5maW5kKHNlbGYuc2VlTGVzc1NlbGVjdG9yKS5yZW1vdmVDbGFzcygnZC1ub25lJyk7XG4gICAgICBzZWxmLnVwZGF0ZU1vZHVsZVZpc2liaWxpdHkoKTtcbiAgICB9KTtcblxuICAgICQoYCR7c2VsZi5tb2R1bGVTaG9ydExpc3R9ICR7c2VsZi5zZWVMZXNzU2VsZWN0b3J9YCkub24oJ2NsaWNrJywgZnVuY3Rpb24gc2VlTW9yZSgpIHtcbiAgICAgIHNlbGYuY3VycmVudENhdGVnb3J5RGlzcGxheVskKHRoaXMpLmRhdGEoJ2NhdGVnb3J5JyldID0gZmFsc2U7XG4gICAgICAkKHRoaXMpLmFkZENsYXNzKCdkLW5vbmUnKTtcbiAgICAgICQodGhpcykuY2xvc2VzdChzZWxmLm1vZHVsZVNob3J0TGlzdCkuZmluZChzZWxmLnNlZU1vcmVTZWxlY3RvcikucmVtb3ZlQ2xhc3MoJ2Qtbm9uZScpO1xuICAgICAgc2VsZi51cGRhdGVNb2R1bGVWaXNpYmlsaXR5KCk7XG4gICAgfSk7XG4gIH1cblxuICB1cGRhdGVUb3RhbFJlc3VsdHMoKSB7XG4gICAgY29uc3QgcmVwbGFjZUZpcnN0V29yZEJ5ID0gKGVsZW1lbnQsIHZhbHVlKSA9PiB7XG4gICAgICBjb25zdCBleHBsb2RlZFRleHQgPSBlbGVtZW50LnRleHQoKS5zcGxpdCgnICcpO1xuICAgICAgZXhwbG9kZWRUZXh0WzBdID0gdmFsdWU7XG4gICAgICBlbGVtZW50LnRleHQoZXhwbG9kZWRUZXh0LmpvaW4oJyAnKSk7XG4gICAgfTtcblxuICAgIC8vIElmIHRoZXJlIGFyZSBzb21lIHNob3J0bGlzdDogZWFjaCBzaG9ydGxpc3QgY291bnQgdGhlIG1vZHVsZXMgb24gdGhlIG5leHQgY29udGFpbmVyLlxuICAgIGNvbnN0ICRzaG9ydExpc3RzID0gJCgnLm1vZHVsZS1zaG9ydC1saXN0Jyk7XG4gICAgaWYgKCRzaG9ydExpc3RzLmxlbmd0aCA+IDApIHtcbiAgICAgICRzaG9ydExpc3RzLmVhY2goZnVuY3Rpb24gc2hvcnRMaXN0cygpIHtcbiAgICAgICAgY29uc3QgJHRoaXMgPSAkKHRoaXMpO1xuICAgICAgICByZXBsYWNlRmlyc3RXb3JkQnkoXG4gICAgICAgICAgJHRoaXMuZmluZCgnLm1vZHVsZS1zZWFyY2gtcmVzdWx0LXdvcmRpbmcnKSxcbiAgICAgICAgICAkdGhpcy5uZXh0KCcubW9kdWxlcy1saXN0JykuZmluZCgnLm1vZHVsZS1pdGVtJykubGVuZ3RoXG4gICAgICAgICk7XG4gICAgICB9KTtcblxuICAgICAgLy8gSWYgdGhlcmUgaXMgbm8gc2hvcnRsaXN0OiB0aGUgd29yZGluZyBkaXJlY3RseSB1cGRhdGUgZnJvbSB0aGUgb25seSBtb2R1bGUgY29udGFpbmVyLlxuICAgIH0gZWxzZSB7XG4gICAgICBjb25zdCBtb2R1bGVzQ291bnQgPSAkKCcubW9kdWxlcy1saXN0JykuZmluZCgnLm1vZHVsZS1pdGVtJykubGVuZ3RoO1xuICAgICAgcmVwbGFjZUZpcnN0V29yZEJ5KCQoJy5tb2R1bGUtc2VhcmNoLXJlc3VsdC13b3JkaW5nJyksIG1vZHVsZXNDb3VudCk7XG5cbiAgICAgIGNvbnN0IHNlbGVjdG9yVG9Ub2dnbGUgPSAoc2VsZi5jdXJyZW50RGlzcGxheSA9PT0gc2VsZi5ESVNQTEFZX0xJU1QpID9cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB0aGlzLmFkZG9uSXRlbUxpc3RTZWxlY3RvciA6XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgdGhpcy5hZGRvbkl0ZW1HcmlkU2VsZWN0b3I7XG4gICAgICAkKHNlbGVjdG9yVG9Ub2dnbGUpLnRvZ2dsZShtb2R1bGVzQ291bnQgIT09ICh0aGlzLm1vZHVsZXNMaXN0Lmxlbmd0aCAvIDIpKTtcblxuICAgICAgaWYgKG1vZHVsZXNDb3VudCA9PT0gMCkge1xuICAgICAgICAkKCcubW9kdWxlLWFkZG9ucy1zZWFyY2gtbGluaycpLmF0dHIoXG4gICAgICAgICAgJ2hyZWYnLFxuICAgICAgICAgIGAke3RoaXMuYmFzZUFkZG9uc1VybH1zZWFyY2gucGhwP3NlYXJjaF9xdWVyeT0ke2VuY29kZVVSSUNvbXBvbmVudCh0aGlzLmN1cnJlbnRUYWdzTGlzdC5qb2luKCcgJykpfWBcbiAgICAgICAgKTtcbiAgICAgIH1cbiAgICB9XG4gIH1cbn1cblxuZXhwb3J0IGRlZmF1bHQgQWRtaW5Nb2R1bGVDb250cm9sbGVyO1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vanMvcGFnZXMvbW9kdWxlL2NvbnRyb2xsZXIuanMiLCIvKipcbiAqIENvcHlyaWdodCBzaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogUHJlc3RhU2hvcCBpcyBhbiBJbnRlcm5hdGlvbmFsIFJlZ2lzdGVyZWQgVHJhZGVtYXJrICYgUHJvcGVydHkgb2YgUHJlc3RhU2hvcCBTQVxuICpcbiAqIE5PVElDRSBPRiBMSUNFTlNFXG4gKlxuICogVGhpcyBzb3VyY2UgZmlsZSBpcyBzdWJqZWN0IHRvIHRoZSBPcGVuIFNvZnR3YXJlIExpY2Vuc2UgKE9TTCAzLjApXG4gKiB0aGF0IGlzIGJ1bmRsZWQgd2l0aCB0aGlzIHBhY2thZ2UgaW4gdGhlIGZpbGUgTElDRU5TRS5tZC5cbiAqIEl0IGlzIGFsc28gYXZhaWxhYmxlIHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViIGF0IHRoaXMgVVJMOlxuICogaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wXG4gKiBJZiB5b3UgZGlkIG5vdCByZWNlaXZlIGEgY29weSBvZiB0aGUgbGljZW5zZSBhbmQgYXJlIHVuYWJsZSB0b1xuICogb2J0YWluIGl0IHRocm91Z2ggdGhlIHdvcmxkLXdpZGUtd2ViLCBwbGVhc2Ugc2VuZCBhbiBlbWFpbFxuICogdG8gbGljZW5zZUBwcmVzdGFzaG9wLmNvbSBzbyB3ZSBjYW4gc2VuZCB5b3UgYSBjb3B5IGltbWVkaWF0ZWx5LlxuICpcbiAqIERJU0NMQUlNRVJcbiAqXG4gKiBEbyBub3QgZWRpdCBvciBhZGQgdG8gdGhpcyBmaWxlIGlmIHlvdSB3aXNoIHRvIHVwZ3JhZGUgUHJlc3RhU2hvcCB0byBuZXdlclxuICogdmVyc2lvbnMgaW4gdGhlIGZ1dHVyZS4gSWYgeW91IHdpc2ggdG8gY3VzdG9taXplIFByZXN0YVNob3AgZm9yIHlvdXJcbiAqIG5lZWRzIHBsZWFzZSByZWZlciB0byBodHRwczovL2RldmRvY3MucHJlc3Rhc2hvcC5jb20vIGZvciBtb3JlIGluZm9ybWF0aW9uLlxuICpcbiAqIEBhdXRob3IgICAgUHJlc3RhU2hvcCBTQSBhbmQgQ29udHJpYnV0b3JzIDxjb250YWN0QHByZXN0YXNob3AuY29tPlxuICogQGNvcHlyaWdodCBTaW5jZSAyMDA3IFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9yc1xuICogQGxpY2Vuc2UgICBodHRwczovL29wZW5zb3VyY2Uub3JnL2xpY2Vuc2VzL09TTC0zLjAgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICovXG5cbmNvbnN0ICQgPSB3aW5kb3cuJDtcblxuLyoqXG4gKiBNb2R1bGUgQWRtaW4gUGFnZSBMb2FkZXIuXG4gKiBAY29uc3RydWN0b3JcbiAqL1xuY2xhc3MgTW9kdWxlTG9hZGVyIHtcbiAgY29uc3RydWN0b3IoKSB7XG4gICAgTW9kdWxlTG9hZGVyLmhhbmRsZUltcG9ydCgpO1xuICAgIE1vZHVsZUxvYWRlci5oYW5kbGVFdmVudHMoKTtcbiAgfVxuXG4gIHN0YXRpYyBoYW5kbGVJbXBvcnQoKSB7XG4gICAgY29uc3QgbW9kdWxlSW1wb3J0ID0gJCgnI21vZHVsZS1pbXBvcnQnKTtcbiAgICBtb2R1bGVJbXBvcnQuY2xpY2soKCkgPT4ge1xuICAgICAgbW9kdWxlSW1wb3J0LmFkZENsYXNzKCdvbmNsaWNrJywgMjUwLCB2YWxpZGF0ZSk7XG4gICAgfSk7XG5cbiAgICBmdW5jdGlvbiB2YWxpZGF0ZSgpIHtcbiAgICAgIHNldFRpbWVvdXQoXG4gICAgICAgICgpID0+IHtcbiAgICAgICAgICBtb2R1bGVJbXBvcnQucmVtb3ZlQ2xhc3MoJ29uY2xpY2snKTtcbiAgICAgICAgICBtb2R1bGVJbXBvcnQuYWRkQ2xhc3MoJ3ZhbGlkYXRlJywgNDUwLCBjYWxsYmFjayk7XG4gICAgICAgIH0sXG4gICAgICAgIDIyNTBcbiAgICAgICk7XG4gICAgfVxuICAgIGZ1bmN0aW9uIGNhbGxiYWNrKCkge1xuICAgICAgc2V0VGltZW91dChcbiAgICAgICAgKCkgPT4ge1xuICAgICAgICAgIG1vZHVsZUltcG9ydC5yZW1vdmVDbGFzcygndmFsaWRhdGUnKTtcbiAgICAgICAgfSxcbiAgICAgICAgMTI1MFxuICAgICAgKTtcbiAgICB9XG4gIH1cblxuICBzdGF0aWMgaGFuZGxlRXZlbnRzKCkge1xuICAgICQoJ2JvZHknKS5vbihcbiAgICAgICdjbGljaycsXG4gICAgICAnYS5tb2R1bGUtcmVhZC1tb3JlLWdyaWQtYnRuLCBhLm1vZHVsZS1yZWFkLW1vcmUtbGlzdC1idG4nLFxuICAgICAgKGV2ZW50KSA9PiB7XG4gICAgICAgIGV2ZW50LnByZXZlbnREZWZhdWx0KCk7XG4gICAgICAgIGNvbnN0IG1vZHVsZVBvcHBpbiA9ICQoZXZlbnQudGFyZ2V0KS5kYXRhKCd0YXJnZXQnKTtcblxuICAgICAgICAkLmdldChldmVudC50YXJnZXQuaHJlZiwgKGRhdGEpID0+IHtcbiAgICAgICAgICAkKG1vZHVsZVBvcHBpbikuaHRtbChkYXRhKTtcbiAgICAgICAgICAkKG1vZHVsZVBvcHBpbikubW9kYWwoKTtcbiAgICAgICAgfSk7XG4gICAgICB9XG4gICAgKTtcbiAgfVxufVxuXG5leHBvcnQgZGVmYXVsdCBNb2R1bGVMb2FkZXI7XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9wYWdlcy9tb2R1bGUvbG9hZGVyLmpzIiwiKGZ1bmN0aW9uKCkgeyBtb2R1bGUuZXhwb3J0cyA9IHdpbmRvd1tcImpRdWVyeVwiXTsgfSgpKTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyBleHRlcm5hbCBcImpRdWVyeVwiXG4vLyBtb2R1bGUgaWQgPSA0MlxuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAyIDUgNiA3IDggOSAxNyAyNiAzMCA0NCA0OCIsIi8vIDcuMS4xMyBUb09iamVjdChhcmd1bWVudClcbnZhciBkZWZpbmVkID0gcmVxdWlyZSgnLi9fZGVmaW5lZCcpO1xubW9kdWxlLmV4cG9ydHMgPSBmdW5jdGlvbihpdCl7XG4gIHJldHVybiBPYmplY3QoZGVmaW5lZChpdCkpO1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX3RvLW9iamVjdC5qc1xuLy8gbW9kdWxlIGlkID0gNDRcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMyA0IDUgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTggMTkgMjAiLCJ2YXIgc2hhcmVkID0gcmVxdWlyZSgnLi9fc2hhcmVkJykoJ2tleXMnKVxuICAsIHVpZCAgICA9IHJlcXVpcmUoJy4vX3VpZCcpO1xubW9kdWxlLmV4cG9ydHMgPSBmdW5jdGlvbihrZXkpe1xuICByZXR1cm4gc2hhcmVkW2tleV0gfHwgKHNoYXJlZFtrZXldID0gdWlkKGtleSkpO1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX3NoYXJlZC1rZXkuanNcbi8vIG1vZHVsZSBpZCA9IDQ1XG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDMgNCA1IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE4IDE5IDIwIiwidmFyIHRvU3RyaW5nID0ge30udG9TdHJpbmc7XG5cbm1vZHVsZS5leHBvcnRzID0gZnVuY3Rpb24oaXQpe1xuICByZXR1cm4gdG9TdHJpbmcuY2FsbChpdCkuc2xpY2UoOCwgLTEpO1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2NvZi5qc1xuLy8gbW9kdWxlIGlkID0gNDhcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMyA0IDUgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTggMTkgMjAiLCIvLyBJRSA4LSBkb24ndCBlbnVtIGJ1ZyBrZXlzXG5tb2R1bGUuZXhwb3J0cyA9IChcbiAgJ2NvbnN0cnVjdG9yLGhhc093blByb3BlcnR5LGlzUHJvdG90eXBlT2YscHJvcGVydHlJc0VudW1lcmFibGUsdG9Mb2NhbGVTdHJpbmcsdG9TdHJpbmcsdmFsdWVPZidcbikuc3BsaXQoJywnKTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2VudW0tYnVnLWtleXMuanNcbi8vIG1vZHVsZSBpZCA9IDQ5XG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDMgNCA1IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE4IDE5IDIwIiwiLy8gaHR0cHM6Ly9naXRodWIuY29tL3psb2lyb2NrL2NvcmUtanMvaXNzdWVzLzg2I2lzc3VlY29tbWVudC0xMTU3NTkwMjhcbnZhciBnbG9iYWwgPSBtb2R1bGUuZXhwb3J0cyA9IHR5cGVvZiB3aW5kb3cgIT0gJ3VuZGVmaW5lZCcgJiYgd2luZG93Lk1hdGggPT0gTWF0aFxuICA/IHdpbmRvdyA6IHR5cGVvZiBzZWxmICE9ICd1bmRlZmluZWQnICYmIHNlbGYuTWF0aCA9PSBNYXRoID8gc2VsZiA6IEZ1bmN0aW9uKCdyZXR1cm4gdGhpcycpKCk7XG5pZih0eXBlb2YgX19nID09ICdudW1iZXInKV9fZyA9IGdsb2JhbDsgLy8gZXNsaW50LWRpc2FibGUtbGluZSBuby11bmRlZlxuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fZ2xvYmFsLmpzXG4vLyBtb2R1bGUgaWQgPSA1XG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDIgMyA0IDUgNiA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxNyAxOCAxOSAyMCAyMSAyMiAyMyAyNCAyNSAyNiAyNyAyOCAyOSAzMCAzMSAzMiAzMyAzNCAzNSAzNiAzNyAzOCAzOSA0MCA0MSA0MiA0MyA0NCA0NSA0NiA0NyA0OCA0OSA1MCA1MSA1MiA1MyA1NCA1NSA1NiA1NyA1OCIsInZhciBnbG9iYWwgPSByZXF1aXJlKCcuL19nbG9iYWwnKVxuICAsIFNIQVJFRCA9ICdfX2NvcmUtanNfc2hhcmVkX18nXG4gICwgc3RvcmUgID0gZ2xvYmFsW1NIQVJFRF0gfHwgKGdsb2JhbFtTSEFSRURdID0ge30pO1xubW9kdWxlLmV4cG9ydHMgPSBmdW5jdGlvbihrZXkpe1xuICByZXR1cm4gc3RvcmVba2V5XSB8fCAoc3RvcmVba2V5XSA9IHt9KTtcbn07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19zaGFyZWQuanNcbi8vIG1vZHVsZSBpZCA9IDUwXG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDMgNCA1IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE4IDE5IDIwIiwibW9kdWxlLmV4cG9ydHMgPSB7fTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2l0ZXJhdG9ycy5qc1xuLy8gbW9kdWxlIGlkID0gNTFcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMyA0IDUgOCA5IDE0IiwiLyoqXG4gKiBDb3B5cmlnaHQgc2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIFByZXN0YVNob3AgaXMgYW4gSW50ZXJuYXRpb25hbCBSZWdpc3RlcmVkIFRyYWRlbWFyayAmIFByb3BlcnR5IG9mIFByZXN0YVNob3AgU0FcbiAqXG4gKiBOT1RJQ0UgT0YgTElDRU5TRVxuICpcbiAqIFRoaXMgc291cmNlIGZpbGUgaXMgc3ViamVjdCB0byB0aGUgT3BlbiBTb2Z0d2FyZSBMaWNlbnNlIChPU0wgMy4wKVxuICogdGhhdCBpcyBidW5kbGVkIHdpdGggdGhpcyBwYWNrYWdlIGluIHRoZSBmaWxlIExJQ0VOU0UubWQuXG4gKiBJdCBpcyBhbHNvIGF2YWlsYWJsZSB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiBhdCB0aGlzIFVSTDpcbiAqIGh0dHBzOi8vb3BlbnNvdXJjZS5vcmcvbGljZW5zZXMvT1NMLTMuMFxuICogSWYgeW91IGRpZCBub3QgcmVjZWl2ZSBhIGNvcHkgb2YgdGhlIGxpY2Vuc2UgYW5kIGFyZSB1bmFibGUgdG9cbiAqIG9idGFpbiBpdCB0aHJvdWdoIHRoZSB3b3JsZC13aWRlLXdlYiwgcGxlYXNlIHNlbmQgYW4gZW1haWxcbiAqIHRvIGxpY2Vuc2VAcHJlc3Rhc2hvcC5jb20gc28gd2UgY2FuIHNlbmQgeW91IGEgY29weSBpbW1lZGlhdGVseS5cbiAqXG4gKiBESVNDTEFJTUVSXG4gKlxuICogRG8gbm90IGVkaXQgb3IgYWRkIHRvIHRoaXMgZmlsZSBpZiB5b3Ugd2lzaCB0byB1cGdyYWRlIFByZXN0YVNob3AgdG8gbmV3ZXJcbiAqIHZlcnNpb25zIGluIHRoZSBmdXR1cmUuIElmIHlvdSB3aXNoIHRvIGN1c3RvbWl6ZSBQcmVzdGFTaG9wIGZvciB5b3VyXG4gKiBuZWVkcyBwbGVhc2UgcmVmZXIgdG8gaHR0cHM6Ly9kZXZkb2NzLnByZXN0YXNob3AuY29tLyBmb3IgbW9yZSBpbmZvcm1hdGlvbi5cbiAqXG4gKiBAYXV0aG9yICAgIFByZXN0YVNob3AgU0EgYW5kIENvbnRyaWJ1dG9ycyA8Y29udGFjdEBwcmVzdGFzaG9wLmNvbT5cbiAqIEBjb3B5cmlnaHQgU2luY2UgMjAwNyBQcmVzdGFTaG9wIFNBIGFuZCBDb250cmlidXRvcnNcbiAqIEBsaWNlbnNlICAgaHR0cHM6Ly9vcGVuc291cmNlLm9yZy9saWNlbnNlcy9PU0wtMy4wIE9wZW4gU29mdHdhcmUgTGljZW5zZSAoT1NMIDMuMClcbiAqL1xuXG5pbXBvcnQgTW9kdWxlQ2FyZCBmcm9tICdAY29tcG9uZW50cy9tb2R1bGUtY2FyZCc7XG5pbXBvcnQgQWRtaW5Nb2R1bGVDb250cm9sbGVyIGZyb20gJ0BwYWdlcy9tb2R1bGUvY29udHJvbGxlcic7XG5pbXBvcnQgTW9kdWxlTG9hZGVyIGZyb20gJ0BwYWdlcy9tb2R1bGUvbG9hZGVyJztcblxuY29uc3QgJCA9IHdpbmRvdy4kO1xuXG4kKCgpID0+IHtcbiAgY29uc3QgbW9kdWxlQ2FyZENvbnRyb2xsZXIgPSBuZXcgTW9kdWxlQ2FyZCgpO1xuICBuZXcgTW9kdWxlTG9hZGVyKCk7XG4gIG5ldyBBZG1pbk1vZHVsZUNvbnRyb2xsZXIobW9kdWxlQ2FyZENvbnRyb2xsZXIpO1xufSk7XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9qcy9wYWdlcy9tb2R1bGUvaW5kZXguanMiLCIvLyBmYWxsYmFjayBmb3Igbm9uLWFycmF5LWxpa2UgRVMzIGFuZCBub24tZW51bWVyYWJsZSBvbGQgVjggc3RyaW5nc1xudmFyIGNvZiA9IHJlcXVpcmUoJy4vX2NvZicpO1xubW9kdWxlLmV4cG9ydHMgPSBPYmplY3QoJ3onKS5wcm9wZXJ0eUlzRW51bWVyYWJsZSgwKSA/IE9iamVjdCA6IGZ1bmN0aW9uKGl0KXtcbiAgcmV0dXJuIGNvZihpdCkgPT0gJ1N0cmluZycgPyBpdC5zcGxpdCgnJykgOiBPYmplY3QoaXQpO1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2lvYmplY3QuanNcbi8vIG1vZHVsZSBpZCA9IDUyXG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDMgNCA1IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE4IDE5IDIwIiwiLy8gNy4xLjE1IFRvTGVuZ3RoXG52YXIgdG9JbnRlZ2VyID0gcmVxdWlyZSgnLi9fdG8taW50ZWdlcicpXG4gICwgbWluICAgICAgID0gTWF0aC5taW47XG5tb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uKGl0KXtcbiAgcmV0dXJuIGl0ID4gMCA/IG1pbih0b0ludGVnZXIoaXQpLCAweDFmZmZmZmZmZmZmZmZmKSA6IDA7IC8vIHBvdygyLCA1MykgLSAxID09IDkwMDcxOTkyNTQ3NDA5OTFcbn07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL190by1sZW5ndGguanNcbi8vIG1vZHVsZSBpZCA9IDUzXG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDMgNCA1IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE4IDE5IDIwIiwiZXhwb3J0cy5mID0ge30ucHJvcGVydHlJc0VudW1lcmFibGU7XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19vYmplY3QtcGllLmpzXG4vLyBtb2R1bGUgaWQgPSA1NFxuLy8gbW9kdWxlIGNodW5rcyA9IDEgMyA0IDUgNyA4IDkgMTAgMTEgMTIgMTMgMTUgMTYgMTgiLCJ2YXIgaGFzICAgICAgICAgID0gcmVxdWlyZSgnLi9faGFzJylcbiAgLCB0b0lPYmplY3QgICAgPSByZXF1aXJlKCcuL190by1pb2JqZWN0JylcbiAgLCBhcnJheUluZGV4T2YgPSByZXF1aXJlKCcuL19hcnJheS1pbmNsdWRlcycpKGZhbHNlKVxuICAsIElFX1BST1RPICAgICA9IHJlcXVpcmUoJy4vX3NoYXJlZC1rZXknKSgnSUVfUFJPVE8nKTtcblxubW9kdWxlLmV4cG9ydHMgPSBmdW5jdGlvbihvYmplY3QsIG5hbWVzKXtcbiAgdmFyIE8gICAgICA9IHRvSU9iamVjdChvYmplY3QpXG4gICAgLCBpICAgICAgPSAwXG4gICAgLCByZXN1bHQgPSBbXVxuICAgICwga2V5O1xuICBmb3Ioa2V5IGluIE8paWYoa2V5ICE9IElFX1BST1RPKWhhcyhPLCBrZXkpICYmIHJlc3VsdC5wdXNoKGtleSk7XG4gIC8vIERvbid0IGVudW0gYnVnICYgaGlkZGVuIGtleXNcbiAgd2hpbGUobmFtZXMubGVuZ3RoID4gaSlpZihoYXMoTywga2V5ID0gbmFtZXNbaSsrXSkpe1xuICAgIH5hcnJheUluZGV4T2YocmVzdWx0LCBrZXkpIHx8IHJlc3VsdC5wdXNoKGtleSk7XG4gIH1cbiAgcmV0dXJuIHJlc3VsdDtcbn07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19vYmplY3Qta2V5cy1pbnRlcm5hbC5qc1xuLy8gbW9kdWxlIGlkID0gNTVcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMyA0IDUgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTggMTkgMjAiLCIvLyBmYWxzZSAtPiBBcnJheSNpbmRleE9mXG4vLyB0cnVlICAtPiBBcnJheSNpbmNsdWRlc1xudmFyIHRvSU9iamVjdCA9IHJlcXVpcmUoJy4vX3RvLWlvYmplY3QnKVxuICAsIHRvTGVuZ3RoICA9IHJlcXVpcmUoJy4vX3RvLWxlbmd0aCcpXG4gICwgdG9JbmRleCAgID0gcmVxdWlyZSgnLi9fdG8taW5kZXgnKTtcbm1vZHVsZS5leHBvcnRzID0gZnVuY3Rpb24oSVNfSU5DTFVERVMpe1xuICByZXR1cm4gZnVuY3Rpb24oJHRoaXMsIGVsLCBmcm9tSW5kZXgpe1xuICAgIHZhciBPICAgICAgPSB0b0lPYmplY3QoJHRoaXMpXG4gICAgICAsIGxlbmd0aCA9IHRvTGVuZ3RoKE8ubGVuZ3RoKVxuICAgICAgLCBpbmRleCAgPSB0b0luZGV4KGZyb21JbmRleCwgbGVuZ3RoKVxuICAgICAgLCB2YWx1ZTtcbiAgICAvLyBBcnJheSNpbmNsdWRlcyB1c2VzIFNhbWVWYWx1ZVplcm8gZXF1YWxpdHkgYWxnb3JpdGhtXG4gICAgaWYoSVNfSU5DTFVERVMgJiYgZWwgIT0gZWwpd2hpbGUobGVuZ3RoID4gaW5kZXgpe1xuICAgICAgdmFsdWUgPSBPW2luZGV4KytdO1xuICAgICAgaWYodmFsdWUgIT0gdmFsdWUpcmV0dXJuIHRydWU7XG4gICAgLy8gQXJyYXkjdG9JbmRleCBpZ25vcmVzIGhvbGVzLCBBcnJheSNpbmNsdWRlcyAtIG5vdFxuICAgIH0gZWxzZSBmb3IoO2xlbmd0aCA+IGluZGV4OyBpbmRleCsrKWlmKElTX0lOQ0xVREVTIHx8IGluZGV4IGluIE8pe1xuICAgICAgaWYoT1tpbmRleF0gPT09IGVsKXJldHVybiBJU19JTkNMVURFUyB8fCBpbmRleCB8fCAwO1xuICAgIH0gcmV0dXJuICFJU19JTkNMVURFUyAmJiAtMTtcbiAgfTtcbn07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19hcnJheS1pbmNsdWRlcy5qc1xuLy8gbW9kdWxlIGlkID0gNTdcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMyA0IDUgNyA4IDkgMTAgMTEgMTIgMTMgMTQgMTUgMTYgMTggMTkgMjAiLCJ2YXIgdG9JbnRlZ2VyID0gcmVxdWlyZSgnLi9fdG8taW50ZWdlcicpXG4gICwgbWF4ICAgICAgID0gTWF0aC5tYXhcbiAgLCBtaW4gICAgICAgPSBNYXRoLm1pbjtcbm1vZHVsZS5leHBvcnRzID0gZnVuY3Rpb24oaW5kZXgsIGxlbmd0aCl7XG4gIGluZGV4ID0gdG9JbnRlZ2VyKGluZGV4KTtcbiAgcmV0dXJuIGluZGV4IDwgMCA/IG1heChpbmRleCArIGxlbmd0aCwgMCkgOiBtaW4oaW5kZXgsIGxlbmd0aCk7XG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fdG8taW5kZXguanNcbi8vIG1vZHVsZSBpZCA9IDU4XG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDMgNCA1IDcgOCA5IDEwIDExIDEyIDEzIDE0IDE1IDE2IDE4IDE5IDIwIiwiZXhwb3J0cy5mID0gT2JqZWN0LmdldE93blByb3BlcnR5U3ltYm9scztcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX29iamVjdC1nb3BzLmpzXG4vLyBtb2R1bGUgaWQgPSA1OVxuLy8gbW9kdWxlIGNodW5rcyA9IDEgMyA0IDUgNyA4IDkgMTAgMTEgMTIgMTMgMTUgMTYgMTgiLCJ2YXIgYW5PYmplY3QgICAgICAgPSByZXF1aXJlKCcuL19hbi1vYmplY3QnKVxuICAsIElFOF9ET01fREVGSU5FID0gcmVxdWlyZSgnLi9faWU4LWRvbS1kZWZpbmUnKVxuICAsIHRvUHJpbWl0aXZlICAgID0gcmVxdWlyZSgnLi9fdG8tcHJpbWl0aXZlJylcbiAgLCBkUCAgICAgICAgICAgICA9IE9iamVjdC5kZWZpbmVQcm9wZXJ0eTtcblxuZXhwb3J0cy5mID0gcmVxdWlyZSgnLi9fZGVzY3JpcHRvcnMnKSA/IE9iamVjdC5kZWZpbmVQcm9wZXJ0eSA6IGZ1bmN0aW9uIGRlZmluZVByb3BlcnR5KE8sIFAsIEF0dHJpYnV0ZXMpe1xuICBhbk9iamVjdChPKTtcbiAgUCA9IHRvUHJpbWl0aXZlKFAsIHRydWUpO1xuICBhbk9iamVjdChBdHRyaWJ1dGVzKTtcbiAgaWYoSUU4X0RPTV9ERUZJTkUpdHJ5IHtcbiAgICByZXR1cm4gZFAoTywgUCwgQXR0cmlidXRlcyk7XG4gIH0gY2F0Y2goZSl7IC8qIGVtcHR5ICovIH1cbiAgaWYoJ2dldCcgaW4gQXR0cmlidXRlcyB8fCAnc2V0JyBpbiBBdHRyaWJ1dGVzKXRocm93IFR5cGVFcnJvcignQWNjZXNzb3JzIG5vdCBzdXBwb3J0ZWQhJyk7XG4gIGlmKCd2YWx1ZScgaW4gQXR0cmlidXRlcylPW1BdID0gQXR0cmlidXRlcy52YWx1ZTtcbiAgcmV0dXJuIE87XG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fb2JqZWN0LWRwLmpzXG4vLyBtb2R1bGUgaWQgPSA2XG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDIgMyA0IDUgNiA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxNyAxOCAxOSAyMCAyMSAyMiAyMyAyNCAyNSAyNiAyNyAyOCAyOSAzMCAzMSAzMiAzMyAzNCAzNSAzNiAzNyAzOCAzOSA0MCA0MSA0MiA0MyA0NCA0NSA0NiA0NyA0OCA0OSA1MCA1MSA1MiA1MyA1NCA1NSA1NiA1NyA1OCIsInZhciBkZWYgPSByZXF1aXJlKCcuL19vYmplY3QtZHAnKS5mXG4gICwgaGFzID0gcmVxdWlyZSgnLi9faGFzJylcbiAgLCBUQUcgPSByZXF1aXJlKCcuL193a3MnKSgndG9TdHJpbmdUYWcnKTtcblxubW9kdWxlLmV4cG9ydHMgPSBmdW5jdGlvbihpdCwgdGFnLCBzdGF0KXtcbiAgaWYoaXQgJiYgIWhhcyhpdCA9IHN0YXQgPyBpdCA6IGl0LnByb3RvdHlwZSwgVEFHKSlkZWYoaXQsIFRBRywge2NvbmZpZ3VyYWJsZTogdHJ1ZSwgdmFsdWU6IHRhZ30pO1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX3NldC10by1zdHJpbmctdGFnLmpzXG4vLyBtb2R1bGUgaWQgPSA2MFxuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAzIDQgNSA4IDkgMTQiLCJtb2R1bGUuZXhwb3J0cyA9IHRydWU7XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19saWJyYXJ5LmpzXG4vLyBtb2R1bGUgaWQgPSA2M1xuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAzIDQgNSA4IDkgMTQiLCIndXNlIHN0cmljdCc7XG52YXIgJGF0ICA9IHJlcXVpcmUoJy4vX3N0cmluZy1hdCcpKHRydWUpO1xuXG4vLyAyMS4xLjMuMjcgU3RyaW5nLnByb3RvdHlwZVtAQGl0ZXJhdG9yXSgpXG5yZXF1aXJlKCcuL19pdGVyLWRlZmluZScpKFN0cmluZywgJ1N0cmluZycsIGZ1bmN0aW9uKGl0ZXJhdGVkKXtcbiAgdGhpcy5fdCA9IFN0cmluZyhpdGVyYXRlZCk7IC8vIHRhcmdldFxuICB0aGlzLl9pID0gMDsgICAgICAgICAgICAgICAgLy8gbmV4dCBpbmRleFxuLy8gMjEuMS41LjIuMSAlU3RyaW5nSXRlcmF0b3JQcm90b3R5cGUlLm5leHQoKVxufSwgZnVuY3Rpb24oKXtcbiAgdmFyIE8gICAgID0gdGhpcy5fdFxuICAgICwgaW5kZXggPSB0aGlzLl9pXG4gICAgLCBwb2ludDtcbiAgaWYoaW5kZXggPj0gTy5sZW5ndGgpcmV0dXJuIHt2YWx1ZTogdW5kZWZpbmVkLCBkb25lOiB0cnVlfTtcbiAgcG9pbnQgPSAkYXQoTywgaW5kZXgpO1xuICB0aGlzLl9pICs9IHBvaW50Lmxlbmd0aDtcbiAgcmV0dXJuIHt2YWx1ZTogcG9pbnQsIGRvbmU6IGZhbHNlfTtcbn0pO1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9lczYuc3RyaW5nLml0ZXJhdG9yLmpzXG4vLyBtb2R1bGUgaWQgPSA2NFxuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAzIDQgNSA4IDkgMTQiLCIvLyAxOS4xLjIuMiAvIDE1LjIuMy41IE9iamVjdC5jcmVhdGUoTyBbLCBQcm9wZXJ0aWVzXSlcbnZhciBhbk9iamVjdCAgICA9IHJlcXVpcmUoJy4vX2FuLW9iamVjdCcpXG4gICwgZFBzICAgICAgICAgPSByZXF1aXJlKCcuL19vYmplY3QtZHBzJylcbiAgLCBlbnVtQnVnS2V5cyA9IHJlcXVpcmUoJy4vX2VudW0tYnVnLWtleXMnKVxuICAsIElFX1BST1RPICAgID0gcmVxdWlyZSgnLi9fc2hhcmVkLWtleScpKCdJRV9QUk9UTycpXG4gICwgRW1wdHkgICAgICAgPSBmdW5jdGlvbigpeyAvKiBlbXB0eSAqLyB9XG4gICwgUFJPVE9UWVBFICAgPSAncHJvdG90eXBlJztcblxuLy8gQ3JlYXRlIG9iamVjdCB3aXRoIGZha2UgYG51bGxgIHByb3RvdHlwZTogdXNlIGlmcmFtZSBPYmplY3Qgd2l0aCBjbGVhcmVkIHByb3RvdHlwZVxudmFyIGNyZWF0ZURpY3QgPSBmdW5jdGlvbigpe1xuICAvLyBUaHJhc2gsIHdhc3RlIGFuZCBzb2RvbXk6IElFIEdDIGJ1Z1xuICB2YXIgaWZyYW1lID0gcmVxdWlyZSgnLi9fZG9tLWNyZWF0ZScpKCdpZnJhbWUnKVxuICAgICwgaSAgICAgID0gZW51bUJ1Z0tleXMubGVuZ3RoXG4gICAgLCBsdCAgICAgPSAnPCdcbiAgICAsIGd0ICAgICA9ICc+J1xuICAgICwgaWZyYW1lRG9jdW1lbnQ7XG4gIGlmcmFtZS5zdHlsZS5kaXNwbGF5ID0gJ25vbmUnO1xuICByZXF1aXJlKCcuL19odG1sJykuYXBwZW5kQ2hpbGQoaWZyYW1lKTtcbiAgaWZyYW1lLnNyYyA9ICdqYXZhc2NyaXB0Oic7IC8vIGVzbGludC1kaXNhYmxlLWxpbmUgbm8tc2NyaXB0LXVybFxuICAvLyBjcmVhdGVEaWN0ID0gaWZyYW1lLmNvbnRlbnRXaW5kb3cuT2JqZWN0O1xuICAvLyBodG1sLnJlbW92ZUNoaWxkKGlmcmFtZSk7XG4gIGlmcmFtZURvY3VtZW50ID0gaWZyYW1lLmNvbnRlbnRXaW5kb3cuZG9jdW1lbnQ7XG4gIGlmcmFtZURvY3VtZW50Lm9wZW4oKTtcbiAgaWZyYW1lRG9jdW1lbnQud3JpdGUobHQgKyAnc2NyaXB0JyArIGd0ICsgJ2RvY3VtZW50LkY9T2JqZWN0JyArIGx0ICsgJy9zY3JpcHQnICsgZ3QpO1xuICBpZnJhbWVEb2N1bWVudC5jbG9zZSgpO1xuICBjcmVhdGVEaWN0ID0gaWZyYW1lRG9jdW1lbnQuRjtcbiAgd2hpbGUoaS0tKWRlbGV0ZSBjcmVhdGVEaWN0W1BST1RPVFlQRV1bZW51bUJ1Z0tleXNbaV1dO1xuICByZXR1cm4gY3JlYXRlRGljdCgpO1xufTtcblxubW9kdWxlLmV4cG9ydHMgPSBPYmplY3QuY3JlYXRlIHx8IGZ1bmN0aW9uIGNyZWF0ZShPLCBQcm9wZXJ0aWVzKXtcbiAgdmFyIHJlc3VsdDtcbiAgaWYoTyAhPT0gbnVsbCl7XG4gICAgRW1wdHlbUFJPVE9UWVBFXSA9IGFuT2JqZWN0KE8pO1xuICAgIHJlc3VsdCA9IG5ldyBFbXB0eTtcbiAgICBFbXB0eVtQUk9UT1RZUEVdID0gbnVsbDtcbiAgICAvLyBhZGQgXCJfX3Byb3RvX19cIiBmb3IgT2JqZWN0LmdldFByb3RvdHlwZU9mIHBvbHlmaWxsXG4gICAgcmVzdWx0W0lFX1BST1RPXSA9IE87XG4gIH0gZWxzZSByZXN1bHQgPSBjcmVhdGVEaWN0KCk7XG4gIHJldHVybiBQcm9wZXJ0aWVzID09PSB1bmRlZmluZWQgPyByZXN1bHQgOiBkUHMocmVzdWx0LCBQcm9wZXJ0aWVzKTtcbn07XG5cblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX29iamVjdC1jcmVhdGUuanNcbi8vIG1vZHVsZSBpZCA9IDY2XG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDMgNCA1IDggOSAxNCIsIid1c2Ugc3RyaWN0JztcbnZhciBMSUJSQVJZICAgICAgICA9IHJlcXVpcmUoJy4vX2xpYnJhcnknKVxuICAsICRleHBvcnQgICAgICAgID0gcmVxdWlyZSgnLi9fZXhwb3J0JylcbiAgLCByZWRlZmluZSAgICAgICA9IHJlcXVpcmUoJy4vX3JlZGVmaW5lJylcbiAgLCBoaWRlICAgICAgICAgICA9IHJlcXVpcmUoJy4vX2hpZGUnKVxuICAsIGhhcyAgICAgICAgICAgID0gcmVxdWlyZSgnLi9faGFzJylcbiAgLCBJdGVyYXRvcnMgICAgICA9IHJlcXVpcmUoJy4vX2l0ZXJhdG9ycycpXG4gICwgJGl0ZXJDcmVhdGUgICAgPSByZXF1aXJlKCcuL19pdGVyLWNyZWF0ZScpXG4gICwgc2V0VG9TdHJpbmdUYWcgPSByZXF1aXJlKCcuL19zZXQtdG8tc3RyaW5nLXRhZycpXG4gICwgZ2V0UHJvdG90eXBlT2YgPSByZXF1aXJlKCcuL19vYmplY3QtZ3BvJylcbiAgLCBJVEVSQVRPUiAgICAgICA9IHJlcXVpcmUoJy4vX3drcycpKCdpdGVyYXRvcicpXG4gICwgQlVHR1kgICAgICAgICAgPSAhKFtdLmtleXMgJiYgJ25leHQnIGluIFtdLmtleXMoKSkgLy8gU2FmYXJpIGhhcyBidWdneSBpdGVyYXRvcnMgdy9vIGBuZXh0YFxuICAsIEZGX0lURVJBVE9SICAgID0gJ0BAaXRlcmF0b3InXG4gICwgS0VZUyAgICAgICAgICAgPSAna2V5cydcbiAgLCBWQUxVRVMgICAgICAgICA9ICd2YWx1ZXMnO1xuXG52YXIgcmV0dXJuVGhpcyA9IGZ1bmN0aW9uKCl7IHJldHVybiB0aGlzOyB9O1xuXG5tb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uKEJhc2UsIE5BTUUsIENvbnN0cnVjdG9yLCBuZXh0LCBERUZBVUxULCBJU19TRVQsIEZPUkNFRCl7XG4gICRpdGVyQ3JlYXRlKENvbnN0cnVjdG9yLCBOQU1FLCBuZXh0KTtcbiAgdmFyIGdldE1ldGhvZCA9IGZ1bmN0aW9uKGtpbmQpe1xuICAgIGlmKCFCVUdHWSAmJiBraW5kIGluIHByb3RvKXJldHVybiBwcm90b1traW5kXTtcbiAgICBzd2l0Y2goa2luZCl7XG4gICAgICBjYXNlIEtFWVM6IHJldHVybiBmdW5jdGlvbiBrZXlzKCl7IHJldHVybiBuZXcgQ29uc3RydWN0b3IodGhpcywga2luZCk7IH07XG4gICAgICBjYXNlIFZBTFVFUzogcmV0dXJuIGZ1bmN0aW9uIHZhbHVlcygpeyByZXR1cm4gbmV3IENvbnN0cnVjdG9yKHRoaXMsIGtpbmQpOyB9O1xuICAgIH0gcmV0dXJuIGZ1bmN0aW9uIGVudHJpZXMoKXsgcmV0dXJuIG5ldyBDb25zdHJ1Y3Rvcih0aGlzLCBraW5kKTsgfTtcbiAgfTtcbiAgdmFyIFRBRyAgICAgICAgPSBOQU1FICsgJyBJdGVyYXRvcidcbiAgICAsIERFRl9WQUxVRVMgPSBERUZBVUxUID09IFZBTFVFU1xuICAgICwgVkFMVUVTX0JVRyA9IGZhbHNlXG4gICAgLCBwcm90byAgICAgID0gQmFzZS5wcm90b3R5cGVcbiAgICAsICRuYXRpdmUgICAgPSBwcm90b1tJVEVSQVRPUl0gfHwgcHJvdG9bRkZfSVRFUkFUT1JdIHx8IERFRkFVTFQgJiYgcHJvdG9bREVGQVVMVF1cbiAgICAsICRkZWZhdWx0ICAgPSAkbmF0aXZlIHx8IGdldE1ldGhvZChERUZBVUxUKVxuICAgICwgJGVudHJpZXMgICA9IERFRkFVTFQgPyAhREVGX1ZBTFVFUyA/ICRkZWZhdWx0IDogZ2V0TWV0aG9kKCdlbnRyaWVzJykgOiB1bmRlZmluZWRcbiAgICAsICRhbnlOYXRpdmUgPSBOQU1FID09ICdBcnJheScgPyBwcm90by5lbnRyaWVzIHx8ICRuYXRpdmUgOiAkbmF0aXZlXG4gICAgLCBtZXRob2RzLCBrZXksIEl0ZXJhdG9yUHJvdG90eXBlO1xuICAvLyBGaXggbmF0aXZlXG4gIGlmKCRhbnlOYXRpdmUpe1xuICAgIEl0ZXJhdG9yUHJvdG90eXBlID0gZ2V0UHJvdG90eXBlT2YoJGFueU5hdGl2ZS5jYWxsKG5ldyBCYXNlKSk7XG4gICAgaWYoSXRlcmF0b3JQcm90b3R5cGUgIT09IE9iamVjdC5wcm90b3R5cGUpe1xuICAgICAgLy8gU2V0IEBAdG9TdHJpbmdUYWcgdG8gbmF0aXZlIGl0ZXJhdG9yc1xuICAgICAgc2V0VG9TdHJpbmdUYWcoSXRlcmF0b3JQcm90b3R5cGUsIFRBRywgdHJ1ZSk7XG4gICAgICAvLyBmaXggZm9yIHNvbWUgb2xkIGVuZ2luZXNcbiAgICAgIGlmKCFMSUJSQVJZICYmICFoYXMoSXRlcmF0b3JQcm90b3R5cGUsIElURVJBVE9SKSloaWRlKEl0ZXJhdG9yUHJvdG90eXBlLCBJVEVSQVRPUiwgcmV0dXJuVGhpcyk7XG4gICAgfVxuICB9XG4gIC8vIGZpeCBBcnJheSN7dmFsdWVzLCBAQGl0ZXJhdG9yfS5uYW1lIGluIFY4IC8gRkZcbiAgaWYoREVGX1ZBTFVFUyAmJiAkbmF0aXZlICYmICRuYXRpdmUubmFtZSAhPT0gVkFMVUVTKXtcbiAgICBWQUxVRVNfQlVHID0gdHJ1ZTtcbiAgICAkZGVmYXVsdCA9IGZ1bmN0aW9uIHZhbHVlcygpeyByZXR1cm4gJG5hdGl2ZS5jYWxsKHRoaXMpOyB9O1xuICB9XG4gIC8vIERlZmluZSBpdGVyYXRvclxuICBpZigoIUxJQlJBUlkgfHwgRk9SQ0VEKSAmJiAoQlVHR1kgfHwgVkFMVUVTX0JVRyB8fCAhcHJvdG9bSVRFUkFUT1JdKSl7XG4gICAgaGlkZShwcm90bywgSVRFUkFUT1IsICRkZWZhdWx0KTtcbiAgfVxuICAvLyBQbHVnIGZvciBsaWJyYXJ5XG4gIEl0ZXJhdG9yc1tOQU1FXSA9ICRkZWZhdWx0O1xuICBJdGVyYXRvcnNbVEFHXSAgPSByZXR1cm5UaGlzO1xuICBpZihERUZBVUxUKXtcbiAgICBtZXRob2RzID0ge1xuICAgICAgdmFsdWVzOiAgREVGX1ZBTFVFUyA/ICRkZWZhdWx0IDogZ2V0TWV0aG9kKFZBTFVFUyksXG4gICAgICBrZXlzOiAgICBJU19TRVQgICAgID8gJGRlZmF1bHQgOiBnZXRNZXRob2QoS0VZUyksXG4gICAgICBlbnRyaWVzOiAkZW50cmllc1xuICAgIH07XG4gICAgaWYoRk9SQ0VEKWZvcihrZXkgaW4gbWV0aG9kcyl7XG4gICAgICBpZighKGtleSBpbiBwcm90bykpcmVkZWZpbmUocHJvdG8sIGtleSwgbWV0aG9kc1trZXldKTtcbiAgICB9IGVsc2UgJGV4cG9ydCgkZXhwb3J0LlAgKyAkZXhwb3J0LkYgKiAoQlVHR1kgfHwgVkFMVUVTX0JVRyksIE5BTUUsIG1ldGhvZHMpO1xuICB9XG4gIHJldHVybiBtZXRob2RzO1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2l0ZXItZGVmaW5lLmpzXG4vLyBtb2R1bGUgaWQgPSA2N1xuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAzIDQgNSA4IDkgMTQiLCJtb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uKGV4ZWMpe1xuICB0cnkge1xuICAgIHJldHVybiAhIWV4ZWMoKTtcbiAgfSBjYXRjaChlKXtcbiAgICByZXR1cm4gdHJ1ZTtcbiAgfVxufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2ZhaWxzLmpzXG4vLyBtb2R1bGUgaWQgPSA3XG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDIgMyA0IDUgNiA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxNyAxOCAxOSAyMCAyMSAyMiAyMyAyNCAyNSAyNiAyNyAyOCAyOSAzMCAzMSAzMiAzMyAzNCAzNSAzNiAzNyAzOCAzOSA0MCA0MSA0MiA0MyA0NCA0NSA0NiA0NyA0OCA0OSA1MCA1MSA1MiA1MyA1NCA1NSA1NiA1NyA1OCIsIm1vZHVsZS5leHBvcnRzID0geyBcImRlZmF1bHRcIjogcmVxdWlyZShcImNvcmUtanMvbGlicmFyeS9mbi9vYmplY3Qva2V5c1wiKSwgX19lc01vZHVsZTogdHJ1ZSB9O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9iYWJlbC1ydW50aW1lL2NvcmUtanMvb2JqZWN0L2tleXMuanNcbi8vIG1vZHVsZSBpZCA9IDcwXG4vLyBtb2R1bGUgY2h1bmtzID0gMSAzIDQgOCA5IDEwIDE1IDE5IDIwIiwidmFyIGdsb2JhbCAgICAgICAgID0gcmVxdWlyZSgnLi9fZ2xvYmFsJylcbiAgLCBjb3JlICAgICAgICAgICA9IHJlcXVpcmUoJy4vX2NvcmUnKVxuICAsIExJQlJBUlkgICAgICAgID0gcmVxdWlyZSgnLi9fbGlicmFyeScpXG4gICwgd2tzRXh0ICAgICAgICAgPSByZXF1aXJlKCcuL193a3MtZXh0JylcbiAgLCBkZWZpbmVQcm9wZXJ0eSA9IHJlcXVpcmUoJy4vX29iamVjdC1kcCcpLmY7XG5tb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uKG5hbWUpe1xuICB2YXIgJFN5bWJvbCA9IGNvcmUuU3ltYm9sIHx8IChjb3JlLlN5bWJvbCA9IExJQlJBUlkgPyB7fSA6IGdsb2JhbC5TeW1ib2wgfHwge30pO1xuICBpZihuYW1lLmNoYXJBdCgwKSAhPSAnXycgJiYgIShuYW1lIGluICRTeW1ib2wpKWRlZmluZVByb3BlcnR5KCRTeW1ib2wsIG5hbWUsIHt2YWx1ZTogd2tzRXh0LmYobmFtZSl9KTtcbn07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL193a3MtZGVmaW5lLmpzXG4vLyBtb2R1bGUgaWQgPSA3MVxuLy8gbW9kdWxlIGNodW5rcyA9IDEgMyA0IDUgOCA5IiwiZXhwb3J0cy5mID0gcmVxdWlyZSgnLi9fd2tzJyk7XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL193a3MtZXh0LmpzXG4vLyBtb2R1bGUgaWQgPSA3MlxuLy8gbW9kdWxlIGNodW5rcyA9IDEgMyA0IDUgOCA5IiwicmVxdWlyZSgnLi9lczYuYXJyYXkuaXRlcmF0b3InKTtcbnZhciBnbG9iYWwgICAgICAgID0gcmVxdWlyZSgnLi9fZ2xvYmFsJylcbiAgLCBoaWRlICAgICAgICAgID0gcmVxdWlyZSgnLi9faGlkZScpXG4gICwgSXRlcmF0b3JzICAgICA9IHJlcXVpcmUoJy4vX2l0ZXJhdG9ycycpXG4gICwgVE9fU1RSSU5HX1RBRyA9IHJlcXVpcmUoJy4vX3drcycpKCd0b1N0cmluZ1RhZycpO1xuXG5mb3IodmFyIGNvbGxlY3Rpb25zID0gWydOb2RlTGlzdCcsICdET01Ub2tlbkxpc3QnLCAnTWVkaWFMaXN0JywgJ1N0eWxlU2hlZXRMaXN0JywgJ0NTU1J1bGVMaXN0J10sIGkgPSAwOyBpIDwgNTsgaSsrKXtcbiAgdmFyIE5BTUUgICAgICAgPSBjb2xsZWN0aW9uc1tpXVxuICAgICwgQ29sbGVjdGlvbiA9IGdsb2JhbFtOQU1FXVxuICAgICwgcHJvdG8gICAgICA9IENvbGxlY3Rpb24gJiYgQ29sbGVjdGlvbi5wcm90b3R5cGU7XG4gIGlmKHByb3RvICYmICFwcm90b1tUT19TVFJJTkdfVEFHXSloaWRlKHByb3RvLCBUT19TVFJJTkdfVEFHLCBOQU1FKTtcbiAgSXRlcmF0b3JzW05BTUVdID0gSXRlcmF0b3JzLkFycmF5O1xufVxuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy93ZWIuZG9tLml0ZXJhYmxlLmpzXG4vLyBtb2R1bGUgaWQgPSA3M1xuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAzIDQgNSA4IDkgMTQiLCJtb2R1bGUuZXhwb3J0cyA9IHJlcXVpcmUoJy4vX2hpZGUnKTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX3JlZGVmaW5lLmpzXG4vLyBtb2R1bGUgaWQgPSA3NlxuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAzIDQgNSA4IDkgMTQiLCIvLyBtb3N0IE9iamVjdCBtZXRob2RzIGJ5IEVTNiBzaG91bGQgYWNjZXB0IHByaW1pdGl2ZXNcbnZhciAkZXhwb3J0ID0gcmVxdWlyZSgnLi9fZXhwb3J0JylcbiAgLCBjb3JlICAgID0gcmVxdWlyZSgnLi9fY29yZScpXG4gICwgZmFpbHMgICA9IHJlcXVpcmUoJy4vX2ZhaWxzJyk7XG5tb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uKEtFWSwgZXhlYyl7XG4gIHZhciBmbiAgPSAoY29yZS5PYmplY3QgfHwge30pW0tFWV0gfHwgT2JqZWN0W0tFWV1cbiAgICAsIGV4cCA9IHt9O1xuICBleHBbS0VZXSA9IGV4ZWMoZm4pO1xuICAkZXhwb3J0KCRleHBvcnQuUyArICRleHBvcnQuRiAqIGZhaWxzKGZ1bmN0aW9uKCl7IGZuKDEpOyB9KSwgJ09iamVjdCcsIGV4cCk7XG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fb2JqZWN0LXNhcC5qc1xuLy8gbW9kdWxlIGlkID0gNzhcbi8vIG1vZHVsZSBjaHVua3MgPSAxIDMgNCA4IDkgMTAgMTUgMTkgMjAiLCJ2YXIgZ2xvYmFsICAgID0gcmVxdWlyZSgnLi9fZ2xvYmFsJylcbiAgLCBjb3JlICAgICAgPSByZXF1aXJlKCcuL19jb3JlJylcbiAgLCBjdHggICAgICAgPSByZXF1aXJlKCcuL19jdHgnKVxuICAsIGhpZGUgICAgICA9IHJlcXVpcmUoJy4vX2hpZGUnKVxuICAsIFBST1RPVFlQRSA9ICdwcm90b3R5cGUnO1xuXG52YXIgJGV4cG9ydCA9IGZ1bmN0aW9uKHR5cGUsIG5hbWUsIHNvdXJjZSl7XG4gIHZhciBJU19GT1JDRUQgPSB0eXBlICYgJGV4cG9ydC5GXG4gICAgLCBJU19HTE9CQUwgPSB0eXBlICYgJGV4cG9ydC5HXG4gICAgLCBJU19TVEFUSUMgPSB0eXBlICYgJGV4cG9ydC5TXG4gICAgLCBJU19QUk9UTyAgPSB0eXBlICYgJGV4cG9ydC5QXG4gICAgLCBJU19CSU5EICAgPSB0eXBlICYgJGV4cG9ydC5CXG4gICAgLCBJU19XUkFQICAgPSB0eXBlICYgJGV4cG9ydC5XXG4gICAgLCBleHBvcnRzICAgPSBJU19HTE9CQUwgPyBjb3JlIDogY29yZVtuYW1lXSB8fCAoY29yZVtuYW1lXSA9IHt9KVxuICAgICwgZXhwUHJvdG8gID0gZXhwb3J0c1tQUk9UT1RZUEVdXG4gICAgLCB0YXJnZXQgICAgPSBJU19HTE9CQUwgPyBnbG9iYWwgOiBJU19TVEFUSUMgPyBnbG9iYWxbbmFtZV0gOiAoZ2xvYmFsW25hbWVdIHx8IHt9KVtQUk9UT1RZUEVdXG4gICAgLCBrZXksIG93biwgb3V0O1xuICBpZihJU19HTE9CQUwpc291cmNlID0gbmFtZTtcbiAgZm9yKGtleSBpbiBzb3VyY2Upe1xuICAgIC8vIGNvbnRhaW5zIGluIG5hdGl2ZVxuICAgIG93biA9ICFJU19GT1JDRUQgJiYgdGFyZ2V0ICYmIHRhcmdldFtrZXldICE9PSB1bmRlZmluZWQ7XG4gICAgaWYob3duICYmIGtleSBpbiBleHBvcnRzKWNvbnRpbnVlO1xuICAgIC8vIGV4cG9ydCBuYXRpdmUgb3IgcGFzc2VkXG4gICAgb3V0ID0gb3duID8gdGFyZ2V0W2tleV0gOiBzb3VyY2Vba2V5XTtcbiAgICAvLyBwcmV2ZW50IGdsb2JhbCBwb2xsdXRpb24gZm9yIG5hbWVzcGFjZXNcbiAgICBleHBvcnRzW2tleV0gPSBJU19HTE9CQUwgJiYgdHlwZW9mIHRhcmdldFtrZXldICE9ICdmdW5jdGlvbicgPyBzb3VyY2Vba2V5XVxuICAgIC8vIGJpbmQgdGltZXJzIHRvIGdsb2JhbCBmb3IgY2FsbCBmcm9tIGV4cG9ydCBjb250ZXh0XG4gICAgOiBJU19CSU5EICYmIG93biA/IGN0eChvdXQsIGdsb2JhbClcbiAgICAvLyB3cmFwIGdsb2JhbCBjb25zdHJ1Y3RvcnMgZm9yIHByZXZlbnQgY2hhbmdlIHRoZW0gaW4gbGlicmFyeVxuICAgIDogSVNfV1JBUCAmJiB0YXJnZXRba2V5XSA9PSBvdXQgPyAoZnVuY3Rpb24oQyl7XG4gICAgICB2YXIgRiA9IGZ1bmN0aW9uKGEsIGIsIGMpe1xuICAgICAgICBpZih0aGlzIGluc3RhbmNlb2YgQyl7XG4gICAgICAgICAgc3dpdGNoKGFyZ3VtZW50cy5sZW5ndGgpe1xuICAgICAgICAgICAgY2FzZSAwOiByZXR1cm4gbmV3IEM7XG4gICAgICAgICAgICBjYXNlIDE6IHJldHVybiBuZXcgQyhhKTtcbiAgICAgICAgICAgIGNhc2UgMjogcmV0dXJuIG5ldyBDKGEsIGIpO1xuICAgICAgICAgIH0gcmV0dXJuIG5ldyBDKGEsIGIsIGMpO1xuICAgICAgICB9IHJldHVybiBDLmFwcGx5KHRoaXMsIGFyZ3VtZW50cyk7XG4gICAgICB9O1xuICAgICAgRltQUk9UT1RZUEVdID0gQ1tQUk9UT1RZUEVdO1xuICAgICAgcmV0dXJuIEY7XG4gICAgLy8gbWFrZSBzdGF0aWMgdmVyc2lvbnMgZm9yIHByb3RvdHlwZSBtZXRob2RzXG4gICAgfSkob3V0KSA6IElTX1BST1RPICYmIHR5cGVvZiBvdXQgPT0gJ2Z1bmN0aW9uJyA/IGN0eChGdW5jdGlvbi5jYWxsLCBvdXQpIDogb3V0O1xuICAgIC8vIGV4cG9ydCBwcm90byBtZXRob2RzIHRvIGNvcmUuJUNPTlNUUlVDVE9SJS5tZXRob2RzLiVOQU1FJVxuICAgIGlmKElTX1BST1RPKXtcbiAgICAgIChleHBvcnRzLnZpcnR1YWwgfHwgKGV4cG9ydHMudmlydHVhbCA9IHt9KSlba2V5XSA9IG91dDtcbiAgICAgIC8vIGV4cG9ydCBwcm90byBtZXRob2RzIHRvIGNvcmUuJUNPTlNUUlVDVE9SJS5wcm90b3R5cGUuJU5BTUUlXG4gICAgICBpZih0eXBlICYgJGV4cG9ydC5SICYmIGV4cFByb3RvICYmICFleHBQcm90b1trZXldKWhpZGUoZXhwUHJvdG8sIGtleSwgb3V0KTtcbiAgICB9XG4gIH1cbn07XG4vLyB0eXBlIGJpdG1hcFxuJGV4cG9ydC5GID0gMTsgICAvLyBmb3JjZWRcbiRleHBvcnQuRyA9IDI7ICAgLy8gZ2xvYmFsXG4kZXhwb3J0LlMgPSA0OyAgIC8vIHN0YXRpY1xuJGV4cG9ydC5QID0gODsgICAvLyBwcm90b1xuJGV4cG9ydC5CID0gMTY7ICAvLyBiaW5kXG4kZXhwb3J0LlcgPSAzMjsgIC8vIHdyYXBcbiRleHBvcnQuVSA9IDY0OyAgLy8gc2FmZVxuJGV4cG9ydC5SID0gMTI4OyAvLyByZWFsIHByb3RvIG1ldGhvZCBmb3IgYGxpYnJhcnlgIFxubW9kdWxlLmV4cG9ydHMgPSAkZXhwb3J0O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fZXhwb3J0LmpzXG4vLyBtb2R1bGUgaWQgPSA4XG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDIgMyA0IDUgNiA3IDggOSAxMCAxMSAxMiAxMyAxNCAxNSAxNiAxNyAxOCAxOSAyMCAyMSAyMiAyMyAyNCAyNSAyNiAyNyAyOCAyOSAzMCAzMSAzMiAzMyAzNCAzNSAzNiAzNyAzOCAzOSA0MCA0MSA0MiA0MyA0NCA0NSA0NiA0NyA0OCA0OSA1MCA1MSA1MiA1MyA1NCA1NSA1NiA1NyA1OCIsIi8vIDE5LjEuMi45IC8gMTUuMi4zLjIgT2JqZWN0LmdldFByb3RvdHlwZU9mKE8pXG52YXIgaGFzICAgICAgICAgPSByZXF1aXJlKCcuL19oYXMnKVxuICAsIHRvT2JqZWN0ICAgID0gcmVxdWlyZSgnLi9fdG8tb2JqZWN0JylcbiAgLCBJRV9QUk9UTyAgICA9IHJlcXVpcmUoJy4vX3NoYXJlZC1rZXknKSgnSUVfUFJPVE8nKVxuICAsIE9iamVjdFByb3RvID0gT2JqZWN0LnByb3RvdHlwZTtcblxubW9kdWxlLmV4cG9ydHMgPSBPYmplY3QuZ2V0UHJvdG90eXBlT2YgfHwgZnVuY3Rpb24oTyl7XG4gIE8gPSB0b09iamVjdChPKTtcbiAgaWYoaGFzKE8sIElFX1BST1RPKSlyZXR1cm4gT1tJRV9QUk9UT107XG4gIGlmKHR5cGVvZiBPLmNvbnN0cnVjdG9yID09ICdmdW5jdGlvbicgJiYgTyBpbnN0YW5jZW9mIE8uY29uc3RydWN0b3Ipe1xuICAgIHJldHVybiBPLmNvbnN0cnVjdG9yLnByb3RvdHlwZTtcbiAgfSByZXR1cm4gTyBpbnN0YW5jZW9mIE9iamVjdCA/IE9iamVjdFByb3RvIDogbnVsbDtcbn07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19vYmplY3QtZ3BvLmpzXG4vLyBtb2R1bGUgaWQgPSA4MlxuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAzIDQgNSA4IDkgMTQiLCJyZXF1aXJlKCcuLi8uLi9tb2R1bGVzL2VzNi5vYmplY3Qua2V5cycpO1xubW9kdWxlLmV4cG9ydHMgPSByZXF1aXJlKCcuLi8uLi9tb2R1bGVzL19jb3JlJykuT2JqZWN0LmtleXM7XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9mbi9vYmplY3Qva2V5cy5qc1xuLy8gbW9kdWxlIGlkID0gODVcbi8vIG1vZHVsZSBjaHVua3MgPSAxIDMgNCA4IDkgMTAgMTUgMTkgMjAiLCIvLyAxOS4xLjIuNyAvIDE1LjIuMy40IE9iamVjdC5nZXRPd25Qcm9wZXJ0eU5hbWVzKE8pXG52YXIgJGtleXMgICAgICA9IHJlcXVpcmUoJy4vX29iamVjdC1rZXlzLWludGVybmFsJylcbiAgLCBoaWRkZW5LZXlzID0gcmVxdWlyZSgnLi9fZW51bS1idWcta2V5cycpLmNvbmNhdCgnbGVuZ3RoJywgJ3Byb3RvdHlwZScpO1xuXG5leHBvcnRzLmYgPSBPYmplY3QuZ2V0T3duUHJvcGVydHlOYW1lcyB8fCBmdW5jdGlvbiBnZXRPd25Qcm9wZXJ0eU5hbWVzKE8pe1xuICByZXR1cm4gJGtleXMoTywgaGlkZGVuS2V5cyk7XG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fb2JqZWN0LWdvcG4uanNcbi8vIG1vZHVsZSBpZCA9IDg4XG4vLyBtb2R1bGUgY2h1bmtzID0gMSAzIDQgNSA4IDkiLCIvLyAxOS4xLjIuMTQgT2JqZWN0LmtleXMoTylcbnZhciB0b09iamVjdCA9IHJlcXVpcmUoJy4vX3RvLW9iamVjdCcpXG4gICwgJGtleXMgICAgPSByZXF1aXJlKCcuL19vYmplY3Qta2V5cycpO1xuXG5yZXF1aXJlKCcuL19vYmplY3Qtc2FwJykoJ2tleXMnLCBmdW5jdGlvbigpe1xuICByZXR1cm4gZnVuY3Rpb24ga2V5cyhpdCl7XG4gICAgcmV0dXJuICRrZXlzKHRvT2JqZWN0KGl0KSk7XG4gIH07XG59KTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvZXM2Lm9iamVjdC5rZXlzLmpzXG4vLyBtb2R1bGUgaWQgPSA5MFxuLy8gbW9kdWxlIGNodW5rcyA9IDEgMyA0IDggOSAxMCAxNSAxOSAyMCIsIm1vZHVsZS5leHBvcnRzID0gcmVxdWlyZSgnLi9fZ2xvYmFsJykuZG9jdW1lbnQgJiYgZG9jdW1lbnQuZG9jdW1lbnRFbGVtZW50O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9faHRtbC5qc1xuLy8gbW9kdWxlIGlkID0gOTNcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMyA0IDUgOCA5IDE0IiwibW9kdWxlLmV4cG9ydHMgPSBmdW5jdGlvbihkb25lLCB2YWx1ZSl7XG4gIHJldHVybiB7dmFsdWU6IHZhbHVlLCBkb25lOiAhIWRvbmV9O1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2l0ZXItc3RlcC5qc1xuLy8gbW9kdWxlIGlkID0gOTRcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMyA0IDUgOCA5IDE0IiwibW9kdWxlLmV4cG9ydHMgPSBmdW5jdGlvbigpeyAvKiBlbXB0eSAqLyB9O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fYWRkLXRvLXVuc2NvcGFibGVzLmpzXG4vLyBtb2R1bGUgaWQgPSA5NlxuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAzIDQgNSA4IDkgMTQiLCIndXNlIHN0cmljdCc7XG52YXIgY3JlYXRlICAgICAgICAgPSByZXF1aXJlKCcuL19vYmplY3QtY3JlYXRlJylcbiAgLCBkZXNjcmlwdG9yICAgICA9IHJlcXVpcmUoJy4vX3Byb3BlcnR5LWRlc2MnKVxuICAsIHNldFRvU3RyaW5nVGFnID0gcmVxdWlyZSgnLi9fc2V0LXRvLXN0cmluZy10YWcnKVxuICAsIEl0ZXJhdG9yUHJvdG90eXBlID0ge307XG5cbi8vIDI1LjEuMi4xLjEgJUl0ZXJhdG9yUHJvdG90eXBlJVtAQGl0ZXJhdG9yXSgpXG5yZXF1aXJlKCcuL19oaWRlJykoSXRlcmF0b3JQcm90b3R5cGUsIHJlcXVpcmUoJy4vX3drcycpKCdpdGVyYXRvcicpLCBmdW5jdGlvbigpeyByZXR1cm4gdGhpczsgfSk7XG5cbm1vZHVsZS5leHBvcnRzID0gZnVuY3Rpb24oQ29uc3RydWN0b3IsIE5BTUUsIG5leHQpe1xuICBDb25zdHJ1Y3Rvci5wcm90b3R5cGUgPSBjcmVhdGUoSXRlcmF0b3JQcm90b3R5cGUsIHtuZXh0OiBkZXNjcmlwdG9yKDEsIG5leHQpfSk7XG4gIHNldFRvU3RyaW5nVGFnKENvbnN0cnVjdG9yLCBOQU1FICsgJyBJdGVyYXRvcicpO1xufTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL34vY29yZS1qcy9saWJyYXJ5L21vZHVsZXMvX2l0ZXItY3JlYXRlLmpzXG4vLyBtb2R1bGUgaWQgPSA5N1xuLy8gbW9kdWxlIGNodW5rcyA9IDAgMSAzIDQgNSA4IDkgMTQiLCJ2YXIgZFAgICAgICAgPSByZXF1aXJlKCcuL19vYmplY3QtZHAnKVxuICAsIGFuT2JqZWN0ID0gcmVxdWlyZSgnLi9fYW4tb2JqZWN0JylcbiAgLCBnZXRLZXlzICA9IHJlcXVpcmUoJy4vX29iamVjdC1rZXlzJyk7XG5cbm1vZHVsZS5leHBvcnRzID0gcmVxdWlyZSgnLi9fZGVzY3JpcHRvcnMnKSA/IE9iamVjdC5kZWZpbmVQcm9wZXJ0aWVzIDogZnVuY3Rpb24gZGVmaW5lUHJvcGVydGllcyhPLCBQcm9wZXJ0aWVzKXtcbiAgYW5PYmplY3QoTyk7XG4gIHZhciBrZXlzICAgPSBnZXRLZXlzKFByb3BlcnRpZXMpXG4gICAgLCBsZW5ndGggPSBrZXlzLmxlbmd0aFxuICAgICwgaSA9IDBcbiAgICAsIFA7XG4gIHdoaWxlKGxlbmd0aCA+IGkpZFAuZihPLCBQID0ga2V5c1tpKytdLCBQcm9wZXJ0aWVzW1BdKTtcbiAgcmV0dXJuIE87XG59O1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vfi9jb3JlLWpzL2xpYnJhcnkvbW9kdWxlcy9fb2JqZWN0LWRwcy5qc1xuLy8gbW9kdWxlIGlkID0gOThcbi8vIG1vZHVsZSBjaHVua3MgPSAwIDEgMyA0IDUgOCA5IDE0IiwidmFyIHRvSW50ZWdlciA9IHJlcXVpcmUoJy4vX3RvLWludGVnZXInKVxuICAsIGRlZmluZWQgICA9IHJlcXVpcmUoJy4vX2RlZmluZWQnKTtcbi8vIHRydWUgIC0+IFN0cmluZyNhdFxuLy8gZmFsc2UgLT4gU3RyaW5nI2NvZGVQb2ludEF0XG5tb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uKFRPX1NUUklORyl7XG4gIHJldHVybiBmdW5jdGlvbih0aGF0LCBwb3Mpe1xuICAgIHZhciBzID0gU3RyaW5nKGRlZmluZWQodGhhdCkpXG4gICAgICAsIGkgPSB0b0ludGVnZXIocG9zKVxuICAgICAgLCBsID0gcy5sZW5ndGhcbiAgICAgICwgYSwgYjtcbiAgICBpZihpIDwgMCB8fCBpID49IGwpcmV0dXJuIFRPX1NUUklORyA/ICcnIDogdW5kZWZpbmVkO1xuICAgIGEgPSBzLmNoYXJDb2RlQXQoaSk7XG4gICAgcmV0dXJuIGEgPCAweGQ4MDAgfHwgYSA+IDB4ZGJmZiB8fCBpICsgMSA9PT0gbCB8fCAoYiA9IHMuY2hhckNvZGVBdChpICsgMSkpIDwgMHhkYzAwIHx8IGIgPiAweGRmZmZcbiAgICAgID8gVE9fU1RSSU5HID8gcy5jaGFyQXQoaSkgOiBhXG4gICAgICA6IFRPX1NUUklORyA/IHMuc2xpY2UoaSwgaSArIDIpIDogKGEgLSAweGQ4MDAgPDwgMTApICsgKGIgLSAweGRjMDApICsgMHgxMDAwMDtcbiAgfTtcbn07XG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9+L2NvcmUtanMvbGlicmFyeS9tb2R1bGVzL19zdHJpbmctYXQuanNcbi8vIG1vZHVsZSBpZCA9IDk5XG4vLyBtb2R1bGUgY2h1bmtzID0gMCAxIDMgNCA1IDggOSAxNCJdLCJzb3VyY2VSb290IjoiIn0=