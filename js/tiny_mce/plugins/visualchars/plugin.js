(function () {
var visualchars = (function (domGlobals) {
    'use strict';

    var Cell = function (initial) {
      var value = initial;
      var get = function () {
        return value;
      };
      var set = function (v) {
        value = v;
      };
      var clone = function () {
        return Cell(get());
      };
      return {
        get: get,
        set: set,
        clone: clone
      };
    };

    var global = tinymce.util.Tools.resolve('tinymce.PluginManager');

    var get = function (toggleState) {
      var isEnabled = function () {
        return toggleState.get();
      };
      return { isEnabled: isEnabled };
    };
    var Api = { get: get };

    var fireVisualChars = function (editor, state) {
      return editor.fire('VisualChars', { state: state });
    };
    var Events = { fireVisualChars: fireVisualChars };

    var noop = function () {
    };
    var constant = function (value) {
      return function () {
        return value;
      };
    };
    var never = constant(false);
    var always = constant(true);

    var none = function () {
      return NONE;
    };
    var NONE = function () {
      var eq = function (o) {
        return o.isNone();
      };
      var call = function (thunk) {
        return thunk();
      };
      var id = function (n) {
        return n;
      };
      var me = {
        fold: function (n, s) {
          return n();
        },
        is: never,
        isSome: never,
        isNone: always,
        getOr: id,
        getOrThunk: call,
        getOrDie: function (msg) {
          throw new Error(msg || 'error: getOrDie called on none.');
        },
        getOrNull: constant(null),
        getOrUndefined: constant(undefined),
        or: id,
        orThunk: call,
        map: none,
        each: noop,
        bind: none,
        exists: never,
        forall: always,
        filter: none,
        equals: eq,
        equals_: eq,
        toArray: function () {
          return [];
        },
        toString: constant('none()')
      };
      if (Object.freeze) {
        Object.freeze(me);
      }
      return me;
    }();
    var some = function (a) {
      var constant_a = constant(a);
      var self = function () {
        return me;
      };
      var bind = function (f) {
        return f(a);
      };
      var me = {
        fold: function (n, s) {
          return s(a);
        },
        is: function (v) {
          return a === v;
        },
        isSome: always,
        isNone: never,
        getOr: constant_a,
        getOrThunk: constant_a,
        getOrDie: constant_a,
        getOrNull: constant_a,
        getOrUndefined: constant_a,
        or: self,
        orThunk: self,
        map: function (f) {
          return some(f(a));
        },
        each: function (f) {
          f(a);
        },
        bind: bind,
        exists: bind,
        forall: bind,
        filter: function (f) {
          return f(a) ? me : NONE;
        },
        toArray: function () {
          return [a];
        },
        toString: function () {
          return 'some(' + a + ')';
        },
        equals: function (o) {
          return o.is(a);
        },
        equals_: function (o, elementEq) {
          return o.fold(never, function (b) {
            return elementEq(a, b);
          });
        }
      };
      return me;
    };
    var from = function (value) {
      return value === null || value === undefined ? NONE : some(value);
    };
    var Option = {
      some: some,
      none: none,
      from: from
    };

    var typeOf = function (x) {
      if (x === null) {
        return 'null';
      }
      var t = typeof x;
      if (t === 'object' && (Array.prototype.isPrototypeOf(x) || x.constructor && x.constructor.name === 'Array')) {
        return 'array';
      }
      if (t === 'object' && (String.prototype.isPrototypeOf(x) || x.constructor && x.constructor.name === 'String')) {
        return 'string';
      }
      return t;
    };
    var isType = function (type) {
      return function (value) {
        return typeOf(value) === type;
      };
    };
    var isFunction = isType('function');

    var nativeSlice = Array.prototype.slice;
    var map = function (xs, f) {
      var len = xs.length;
      var r = new Array(len);
      for (var i = 0; i < len; i++) {
        var x = xs[i];
        r[i] = f(x, i);
      }
      return r;
    };
    var each = function (xs, f) {
      for (var i = 0, len = xs.length; i < len; i++) {
        var x = xs[i];
        f(x, i);
      }
    };
    var from$1 = isFunction(Array.from) ? Array.from : function (x) {
      return nativeSlice.call(x);
    };

    var fromHtml = function (html, scope) {
      var doc = scope || domGlobals.document;
      var div = doc.createElement('div');
      div.innerHTML = html;
      if (!div.hasChildNodes() || div.childNodes.length > 1) {
        domGlobals.console.error('HTML does not have a single root node', html);
        throw new Error('HTML must have a single root node');
      }
      return fromDom(div.childNodes[0]);
    };
    var fromTag = function (tag, scope) {
      var doc = scope || domGlobals.document;
      var node = doc.createElement(tag);
      return fromDom(node);
    };
    var fromText = function (text, scope) {
      var doc = scope || domGlobals.document;
      var node = doc.createTextNode(text);
      return fromDom(node);
    };
    var fromDom = function (node) {
      if (node === null || node === undefined) {
        throw new Error('Node cannot be null or undefined');
      }
      return { dom: constant(node) };
    };
    var fromPoint = function (docElm, x, y) {
      var doc = docElm.dom();
      return Option.from(doc.elementFromPoint(x, y)).map(fromDom);
    };
    var Element = {
      fromHtml: fromHtml,
      fromTag: fromTag,
      fromText: fromText,
      fromDom: fromDom,
      fromPoint: fromPoint
    };

    var ATTRIBUTE = domGlobals.Node.ATTRIBUTE_NODE;
    var CDATA_SECTION = domGlobals.Node.CDATA_SECTION_NODE;
    var COMMENT = domGlobals.Node.COMMENT_NODE;
    var DOCUMENT = domGlobals.Node.DOCUMENT_NODE;
    var DOCUMENT_TYPE = domGlobals.Node.DOCUMENT_TYPE_NODE;
    var DOCUMENT_FRAGMENT = domGlobals.Node.DOCUMENT_FRAGMENT_NODE;
    var ELEMENT = domGlobals.Node.ELEMENT_NODE;
    var TEXT = domGlobals.Node.TEXT_NODE;
    var PROCESSING_INSTRUCTION = domGlobals.Node.PROCESSING_INSTRUCTION_NODE;
    var ENTITY_REFERENCE = domGlobals.Node.ENTITY_REFERENCE_NODE;
    var ENTITY = domGlobals.Node.ENTITY_NODE;
    var NOTATION = domGlobals.Node.NOTATION_NODE;

    var Global = typeof domGlobals.window !== 'undefined' ? domGlobals.window : Function('return this;')();

    var type = function (element) {
      return element.dom().nodeType;
    };
    var value = function (element) {
      return element.dom().nodeValue;
    };
    var isType$1 = function (t) {
      return function (element) {
        return type(element) === t;
      };
    };
    var isText = isType$1(TEXT);

    var charMap = {
      '\xA0': 'nbsp',
      '\xAD': 'shy'
    };
    var charMapToRegExp = function (charMap, global) {
      var key, regExp = '';
      for (key in charMap) {
        regExp += key;
      }
      return new RegExp('[' + regExp + ']', global ? 'g' : '');
    };
    var charMapToSelector = function (charMap) {
      var key, selector = '';
      for (key in charMap) {
        if (selector) {
          selector += ',';
        }
        selector += 'span.mce-' + charMap[key];
      }
      return selector;
    };
    var Data = {
      charMap: charMap,
      regExp: charMapToRegExp(charMap),
      regExpGlobal: charMapToRegExp(charMap, true),
      selector: charMapToSelector(charMap),
      charMapToRegExp: charMapToRegExp,
      charMapToSelector: charMapToSelector
    };

    var wrapCharWithSpan = function (value) {
      return '<span data-mce-bogus="1" class="mce-' + Data.charMap[value] + '">' + value + '</span>';
    };
    var Html = { wrapCharWithSpan: wrapCharWithSpan };

    var isMatch = function (n) {
      var value$1 = value(n);
      return isText(n) && value$1 !== undefined && Data.regExp.test(value$1);
    };
    var filterDescendants = function (scope, predicate) {
      var result = [];
      var dom = scope.dom();
      var children = map(dom.childNodes, Element.fromDom);
      each(children, function (x) {
        if (predicate(x)) {
          result = result.concat([x]);
        }
        result = result.concat(filterDescendants(x, predicate));
      });
      return result;
    };
    var findParentElm = function (elm, rootElm) {
      while (elm.parentNode) {
        if (elm.parentNode === rootElm) {
          return elm;
        }
        elm = elm.parentNode;
      }
    };
    var replaceWithSpans = function (text) {
      return text.replace(Data.regExpGlobal, Html.wrapCharWithSpan);
    };
    var Nodes = {
      isMatch: isMatch,
      filterDescendants: filterDescendants,
      findParentElm: findParentElm,
      replaceWithSpans: replaceWithSpans
    };

    var show = function (editor, rootElm) {
      var node, div;
      var nodeList = Nodes.filterDescendants(Element.fromDom(rootElm), Nodes.isMatch);
      each(nodeList, function (n) {
        var withSpans = Nodes.replaceWithSpans(editor.dom.encode(value(n)));
        div = editor.dom.create('div', null, withSpans);
        while (node = div.lastChild) {
          editor.dom.insertAfter(node, n.dom());
        }
        editor.dom.remove(n.dom());
      });
    };
    var hide = function (editor, body) {
      var nodeList = editor.dom.select(Data.selector, body);
      each(nodeList, function (node) {
        editor.dom.remove(node, 1);
      });
    };
    var toggle = function (editor) {
      var body = editor.getBody();
      var bookmark = editor.selection.getBookmark();
      var parentNode = Nodes.findParentElm(editor.selection.getNode(), body);
      parentNode = parentNode !== undefined ? parentNode : body;
      hide(editor, parentNode);
      show(editor, parentNode);
      editor.selection.moveToBookmark(bookmark);
    };
    var VisualChars = {
      show: show,
      hide: hide,
      toggle: toggle
    };

    var toggleVisualChars = function (editor, toggleState) {
      var body = editor.getBody();
      var selection = editor.selection;
      var bookmark;
      toggleState.set(!toggleState.get());
      Events.fireVisualChars(editor, toggleState.get());
      bookmark = selection.getBookmark();
      if (toggleState.get() === true) {
        VisualChars.show(editor, body);
      } else {
        VisualChars.hide(editor, body);
      }
      selection.moveToBookmark(bookmark);
    };
    var Actions = { toggleVisualChars: toggleVisualChars };

    var register = function (editor, toggleState) {
      editor.addCommand('mceVisualChars', function () {
        Actions.toggleVisualChars(editor, toggleState);
      });
    };
    var Commands = { register: register };

    var global$1 = tinymce.util.Tools.resolve('tinymce.util.Delay');

    var setup = function (editor, toggleState) {
      var debouncedToggle = global$1.debounce(function () {
        VisualChars.toggle(editor);
      }, 300);
      if (editor.settings.forced_root_block !== false) {
        editor.on('keydown', function (e) {
          if (toggleState.get() === true) {
            e.keyCode === 13 ? VisualChars.toggle(editor) : debouncedToggle();
          }
        });
      }
    };
    var Keyboard = { setup: setup };

    var isEnabledByDefault = function (editor) {
      return editor.getParam('visualchars_default_state', false);
    };
    var Settings = { isEnabledByDefault: isEnabledByDefault };

    var setup$1 = function (editor, toggleState) {
      editor.on('init', function () {
        var valueForToggling = !Settings.isEnabledByDefault(editor);
        toggleState.set(valueForToggling);
        Actions.toggleVisualChars(editor, toggleState);
      });
    };
    var Bindings = { setup: setup$1 };

    var toggleActiveState = function (editor) {
      return function (e) {
        var ctrl = e.control;
        editor.on('VisualChars', function (e) {
          ctrl.active(e.state);
        });
      };
    };
    var register$1 = function (editor) {
      editor.addButton('visualchars', {
        active: false,
        title: 'Show invisible characters',
        cmd: 'mceVisualChars',
        onPostRender: toggleActiveState(editor)
      });
      editor.addMenuItem('visualchars', {
        text: 'Show invisible characters',
        cmd: 'mceVisualChars',
        onPostRender: toggleActiveState(editor),
        selectable: true,
        context: 'view',
        prependToContext: true
      });
    };

    global.add('visualchars', function (editor) {
      var toggleState = Cell(false);
      Commands.register(editor, toggleState);
      register$1(editor);
      Keyboard.setup(editor, toggleState);
      Bindings.setup(editor, toggleState);
      return Api.get(toggleState);
    });
    function Plugin () {
    }

    return Plugin;

}(window));
})();
