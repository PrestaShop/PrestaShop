(function () {
var modern = (function (domGlobals) {
    'use strict';

    var global = tinymce.util.Tools.resolve('tinymce.ThemeManager');

    var global$1 = tinymce.util.Tools.resolve('tinymce.EditorManager');

    var global$2 = tinymce.util.Tools.resolve('tinymce.util.Tools');

    var isBrandingEnabled = function (editor) {
      return editor.getParam('branding', true, 'boolean');
    };
    var hasMenubar = function (editor) {
      return getMenubar(editor) !== false;
    };
    var getMenubar = function (editor) {
      return editor.getParam('menubar');
    };
    var hasStatusbar = function (editor) {
      return editor.getParam('statusbar', true, 'boolean');
    };
    var getToolbarSize = function (editor) {
      return editor.getParam('toolbar_items_size');
    };
    var isReadOnly = function (editor) {
      return editor.getParam('readonly', false, 'boolean');
    };
    var getFixedToolbarContainer = function (editor) {
      return editor.getParam('fixed_toolbar_container');
    };
    var getInlineToolbarPositionHandler = function (editor) {
      return editor.getParam('inline_toolbar_position_handler');
    };
    var getMenu = function (editor) {
      return editor.getParam('menu');
    };
    var getRemovedMenuItems = function (editor) {
      return editor.getParam('removed_menuitems', '');
    };
    var getMinWidth = function (editor) {
      return editor.getParam('min_width', 100, 'number');
    };
    var getMinHeight = function (editor) {
      return editor.getParam('min_height', 100, 'number');
    };
    var getMaxWidth = function (editor) {
      return editor.getParam('max_width', 65535, 'number');
    };
    var getMaxHeight = function (editor) {
      return editor.getParam('max_height', 65535, 'number');
    };
    var isSkinDisabled = function (editor) {
      return editor.settings.skin === false;
    };
    var isInline = function (editor) {
      return editor.getParam('inline', false, 'boolean');
    };
    var getResize = function (editor) {
      var resize = editor.getParam('resize', 'vertical');
      if (resize === false) {
        return 'none';
      } else if (resize === 'both') {
        return 'both';
      } else {
        return 'vertical';
      }
    };
    var getSkinUrl = function (editor) {
      var settings = editor.settings;
      var skin = settings.skin;
      var skinUrl = settings.skin_url;
      if (skin !== false) {
        var skinName = skin ? skin : 'lightgray';
        if (skinUrl) {
          skinUrl = editor.documentBaseURI.toAbsolute(skinUrl);
        } else {
          skinUrl = global$1.baseURL + '/skins/' + skinName;
        }
      }
      return skinUrl;
    };
    var getIndexedToolbars = function (settings, defaultToolbar) {
      var toolbars = [];
      for (var i = 1; i < 10; i++) {
        var toolbar = settings['toolbar' + i];
        if (!toolbar) {
          break;
        }
        toolbars.push(toolbar);
      }
      var mainToolbar = settings.toolbar ? [settings.toolbar] : [defaultToolbar];
      return toolbars.length > 0 ? toolbars : mainToolbar;
    };
    var getToolbars = function (editor) {
      var toolbar = editor.getParam('toolbar');
      var defaultToolbar = 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image';
      if (toolbar === false) {
        return [];
      } else if (global$2.isArray(toolbar)) {
        return global$2.grep(toolbar, function (toolbar) {
          return toolbar.length > 0;
        });
      } else {
        return getIndexedToolbars(editor.settings, defaultToolbar);
      }
    };

    var global$3 = tinymce.util.Tools.resolve('tinymce.dom.DOMUtils');

    var global$4 = tinymce.util.Tools.resolve('tinymce.ui.Factory');

    var global$5 = tinymce.util.Tools.resolve('tinymce.util.I18n');

    var fireSkinLoaded = function (editor) {
      return editor.fire('SkinLoaded');
    };
    var fireResizeEditor = function (editor) {
      return editor.fire('ResizeEditor');
    };
    var fireBeforeRenderUI = function (editor) {
      return editor.fire('BeforeRenderUI');
    };
    var Events = {
      fireSkinLoaded: fireSkinLoaded,
      fireResizeEditor: fireResizeEditor,
      fireBeforeRenderUI: fireBeforeRenderUI
    };

    var focus = function (panel, type) {
      return function () {
        var item = panel.find(type)[0];
        if (item) {
          item.focus(true);
        }
      };
    };
    var addKeys = function (editor, panel) {
      editor.shortcuts.add('Alt+F9', '', focus(panel, 'menubar'));
      editor.shortcuts.add('Alt+F10,F10', '', focus(panel, 'toolbar'));
      editor.shortcuts.add('Alt+F11', '', focus(panel, 'elementpath'));
      panel.on('cancel', function () {
        editor.focus();
      });
    };
    var A11y = { addKeys: addKeys };

    var global$6 = tinymce.util.Tools.resolve('tinymce.geom.Rect');

    var global$7 = tinymce.util.Tools.resolve('tinymce.util.Delay');

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

    var getUiContainerDelta = function (ctrl) {
      var uiContainer = getUiContainer(ctrl);
      if (uiContainer && global$3.DOM.getStyle(uiContainer, 'position', true) !== 'static') {
        var containerPos = global$3.DOM.getPos(uiContainer);
        var dx = uiContainer.scrollLeft - containerPos.x;
        var dy = uiContainer.scrollTop - containerPos.y;
        return Option.some({
          x: dx,
          y: dy
        });
      } else {
        return Option.none();
      }
    };
    var setUiContainer = function (editor, ctrl) {
      var uiContainer = global$3.DOM.select(editor.settings.ui_container)[0];
      ctrl.getRoot().uiContainer = uiContainer;
    };
    var getUiContainer = function (ctrl) {
      return ctrl ? ctrl.getRoot().uiContainer : null;
    };
    var inheritUiContainer = function (fromCtrl, toCtrl) {
      return toCtrl.uiContainer = getUiContainer(fromCtrl);
    };
    var UiContainer = {
      getUiContainerDelta: getUiContainerDelta,
      setUiContainer: setUiContainer,
      getUiContainer: getUiContainer,
      inheritUiContainer: inheritUiContainer
    };

    var createToolbar = function (editor, items, size) {
      var toolbarItems = [];
      var buttonGroup;
      if (!items) {
        return;
      }
      global$2.each(items.split(/[ ,]/), function (item) {
        var itemName;
        var bindSelectorChanged = function () {
          var selection = editor.selection;
          if (item.settings.stateSelector) {
            selection.selectorChanged(item.settings.stateSelector, function (state) {
              item.active(state);
            }, true);
          }
          if (item.settings.disabledStateSelector) {
            selection.selectorChanged(item.settings.disabledStateSelector, function (state) {
              item.disabled(state);
            });
          }
        };
        if (item === '|') {
          buttonGroup = null;
        } else {
          if (!buttonGroup) {
            buttonGroup = {
              type: 'buttongroup',
              items: []
            };
            toolbarItems.push(buttonGroup);
          }
          if (editor.buttons[item]) {
            itemName = item;
            item = editor.buttons[itemName];
            if (typeof item === 'function') {
              item = item();
            }
            item.type = item.type || 'button';
            item.size = size;
            item = global$4.create(item);
            buttonGroup.items.push(item);
            if (editor.initialized) {
              bindSelectorChanged();
            } else {
              editor.on('init', bindSelectorChanged);
            }
          }
        }
      });
      return {
        type: 'toolbar',
        layout: 'flow',
        items: toolbarItems
      };
    };
    var createToolbars = function (editor, size) {
      var toolbars = [];
      var addToolbar = function (items) {
        if (items) {
          toolbars.push(createToolbar(editor, items, size));
        }
      };
      global$2.each(getToolbars(editor), function (toolbar) {
        addToolbar(toolbar);
      });
      if (toolbars.length) {
        return {
          type: 'panel',
          layout: 'stack',
          classes: 'toolbar-grp',
          ariaRoot: true,
          ariaRemember: true,
          items: toolbars
        };
      }
    };
    var Toolbar = {
      createToolbar: createToolbar,
      createToolbars: createToolbars
    };

    var DOM = global$3.DOM;
    var toClientRect = function (geomRect) {
      return {
        left: geomRect.x,
        top: geomRect.y,
        width: geomRect.w,
        height: geomRect.h,
        right: geomRect.x + geomRect.w,
        bottom: geomRect.y + geomRect.h
      };
    };
    var hideAllFloatingPanels = function (editor) {
      global$2.each(editor.contextToolbars, function (toolbar) {
        if (toolbar.panel) {
          toolbar.panel.hide();
        }
      });
    };
    var movePanelTo = function (panel, pos) {
      panel.moveTo(pos.left, pos.top);
    };
    var togglePositionClass = function (panel, relPos, predicate) {
      relPos = relPos ? relPos.substr(0, 2) : '';
      global$2.each({
        t: 'down',
        b: 'up'
      }, function (cls, pos) {
        panel.classes.toggle('arrow-' + cls, predicate(pos, relPos.substr(0, 1)));
      });
      global$2.each({
        l: 'left',
        r: 'right'
      }, function (cls, pos) {
        panel.classes.toggle('arrow-' + cls, predicate(pos, relPos.substr(1, 1)));
      });
    };
    var userConstrain = function (handler, x, y, elementRect, contentAreaRect, panelRect) {
      panelRect = toClientRect({
        x: x,
        y: y,
        w: panelRect.w,
        h: panelRect.h
      });
      if (handler) {
        panelRect = handler({
          elementRect: toClientRect(elementRect),
          contentAreaRect: toClientRect(contentAreaRect),
          panelRect: panelRect
        });
      }
      return panelRect;
    };
    var addContextualToolbars = function (editor) {
      var scrollContainer;
      var getContextToolbars = function () {
        return editor.contextToolbars || [];
      };
      var getElementRect = function (elm) {
        var pos, targetRect, root;
        pos = DOM.getPos(editor.getContentAreaContainer());
        targetRect = editor.dom.getRect(elm);
        root = editor.dom.getRoot();
        if (root.nodeName === 'BODY') {
          targetRect.x -= root.ownerDocument.documentElement.scrollLeft || root.scrollLeft;
          targetRect.y -= root.ownerDocument.documentElement.scrollTop || root.scrollTop;
        }
        targetRect.x += pos.x;
        targetRect.y += pos.y;
        return targetRect;
      };
      var reposition = function (match, shouldShow) {
        var relPos, panelRect, elementRect, contentAreaRect, panel, relRect, testPositions, smallElementWidthThreshold;
        var handler = getInlineToolbarPositionHandler(editor);
        if (editor.removed) {
          return;
        }
        if (!match || !match.toolbar.panel) {
          hideAllFloatingPanels(editor);
          return;
        }
        testPositions = [
          'bc-tc',
          'tc-bc',
          'tl-bl',
          'bl-tl',
          'tr-br',
          'br-tr'
        ];
        panel = match.toolbar.panel;
        if (shouldShow) {
          panel.show();
        }
        elementRect = getElementRect(match.element);
        panelRect = DOM.getRect(panel.getEl());
        contentAreaRect = DOM.getRect(editor.getContentAreaContainer() || editor.getBody());
        var delta = UiContainer.getUiContainerDelta(panel).getOr({
          x: 0,
          y: 0
        });
        elementRect.x += delta.x;
        elementRect.y += delta.y;
        panelRect.x += delta.x;
        panelRect.y += delta.y;
        contentAreaRect.x += delta.x;
        contentAreaRect.y += delta.y;
        smallElementWidthThreshold = 25;
        if (DOM.getStyle(match.element, 'display', true) !== 'inline') {
          var clientRect = match.element.getBoundingClientRect();
          elementRect.w = clientRect.width;
          elementRect.h = clientRect.height;
        }
        if (!editor.inline) {
          contentAreaRect.w = editor.getDoc().documentElement.offsetWidth;
        }
        if (editor.selection.controlSelection.isResizable(match.element) && elementRect.w < smallElementWidthThreshold) {
          elementRect = global$6.inflate(elementRect, 0, 8);
        }
        relPos = global$6.findBestRelativePosition(panelRect, elementRect, contentAreaRect, testPositions);
        elementRect = global$6.clamp(elementRect, contentAreaRect);
        if (relPos) {
          relRect = global$6.relativePosition(panelRect, elementRect, relPos);
          movePanelTo(panel, userConstrain(handler, relRect.x, relRect.y, elementRect, contentAreaRect, panelRect));
        } else {
          contentAreaRect.h += panelRect.h;
          elementRect = global$6.intersect(contentAreaRect, elementRect);
          if (elementRect) {
            relPos = global$6.findBestRelativePosition(panelRect, elementRect, contentAreaRect, [
              'bc-tc',
              'bl-tl',
              'br-tr'
            ]);
            if (relPos) {
              relRect = global$6.relativePosition(panelRect, elementRect, relPos);
              movePanelTo(panel, userConstrain(handler, relRect.x, relRect.y, elementRect, contentAreaRect, panelRect));
            } else {
              movePanelTo(panel, userConstrain(handler, elementRect.x, elementRect.y, elementRect, contentAreaRect, panelRect));
            }
          } else {
            panel.hide();
          }
        }
        togglePositionClass(panel, relPos, function (pos1, pos2) {
          return pos1 === pos2;
        });
      };
      var repositionHandler = function (show) {
        return function () {
          var execute = function () {
            if (editor.selection) {
              reposition(findFrontMostMatch(editor.selection.getNode()), show);
            }
          };
          global$7.requestAnimationFrame(execute);
        };
      };
      var bindScrollEvent = function (panel) {
        if (!scrollContainer) {
          var reposition_1 = repositionHandler(true);
          var uiContainer_1 = UiContainer.getUiContainer(panel);
          scrollContainer = editor.selection.getScrollContainer() || editor.getWin();
          DOM.bind(scrollContainer, 'scroll', reposition_1);
          DOM.bind(uiContainer_1, 'scroll', reposition_1);
          editor.on('remove', function () {
            DOM.unbind(scrollContainer, 'scroll', reposition_1);
            DOM.unbind(uiContainer_1, 'scroll', reposition_1);
          });
        }
      };
      var showContextToolbar = function (match) {
        var panel;
        if (match.toolbar.panel) {
          match.toolbar.panel.show();
          reposition(match);
          return;
        }
        panel = global$4.create({
          type: 'floatpanel',
          role: 'dialog',
          classes: 'tinymce tinymce-inline arrow',
          ariaLabel: 'Inline toolbar',
          layout: 'flex',
          direction: 'column',
          align: 'stretch',
          autohide: false,
          autofix: true,
          fixed: true,
          border: 1,
          items: Toolbar.createToolbar(editor, match.toolbar.items),
          oncancel: function () {
            editor.focus();
          }
        });
        UiContainer.setUiContainer(editor, panel);
        bindScrollEvent(panel);
        match.toolbar.panel = panel;
        panel.renderTo().reflow();
        reposition(match);
      };
      var hideAllContextToolbars = function () {
        global$2.each(getContextToolbars(), function (toolbar) {
          if (toolbar.panel) {
            toolbar.panel.hide();
          }
        });
      };
      var findFrontMostMatch = function (targetElm) {
        var i, y, parentsAndSelf;
        var toolbars = getContextToolbars();
        parentsAndSelf = editor.$(targetElm).parents().add(targetElm);
        for (i = parentsAndSelf.length - 1; i >= 0; i--) {
          for (y = toolbars.length - 1; y >= 0; y--) {
            if (toolbars[y].predicate(parentsAndSelf[i])) {
              return {
                toolbar: toolbars[y],
                element: parentsAndSelf[i]
              };
            }
          }
        }
        return null;
      };
      editor.on('click keyup setContent ObjectResized', function (e) {
        if (e.type === 'setcontent' && !e.selection) {
          return;
        }
        global$7.setEditorTimeout(editor, function () {
          var match;
          match = findFrontMostMatch(editor.selection.getNode());
          if (match) {
            hideAllContextToolbars();
            showContextToolbar(match);
          } else {
            hideAllContextToolbars();
          }
        });
      });
      editor.on('blur hide contextmenu', hideAllContextToolbars);
      editor.on('ObjectResizeStart', function () {
        var match = findFrontMostMatch(editor.selection.getNode());
        if (match && match.toolbar.panel) {
          match.toolbar.panel.hide();
        }
      });
      editor.on('ResizeEditor ResizeWindow', repositionHandler(true));
      editor.on('nodeChange', repositionHandler(false));
      editor.on('remove', function () {
        global$2.each(getContextToolbars(), function (toolbar) {
          if (toolbar.panel) {
            toolbar.panel.remove();
          }
        });
        editor.contextToolbars = {};
      });
      editor.shortcuts.add('ctrl+F9', '', function () {
        var match = findFrontMostMatch(editor.selection.getNode());
        if (match && match.toolbar.panel) {
          match.toolbar.panel.items()[0].focus();
        }
      });
    };
    var ContextToolbars = { addContextualToolbars: addContextualToolbars };

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
    var isArray = isType('array');
    var isFunction = isType('function');
    var isNumber = isType('number');

    var nativeSlice = Array.prototype.slice;
    var nativeIndexOf = Array.prototype.indexOf;
    var nativePush = Array.prototype.push;
    var rawIndexOf = function (ts, t) {
      return nativeIndexOf.call(ts, t);
    };
    var indexOf = function (xs, x) {
      var r = rawIndexOf(xs, x);
      return r === -1 ? Option.none() : Option.some(r);
    };
    var exists = function (xs, pred) {
      for (var i = 0, len = xs.length; i < len; i++) {
        var x = xs[i];
        if (pred(x, i)) {
          return true;
        }
      }
      return false;
    };
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
    var filter = function (xs, pred) {
      var r = [];
      for (var i = 0, len = xs.length; i < len; i++) {
        var x = xs[i];
        if (pred(x, i)) {
          r.push(x);
        }
      }
      return r;
    };
    var foldl = function (xs, f, acc) {
      each(xs, function (x) {
        acc = f(acc, x);
      });
      return acc;
    };
    var find = function (xs, pred) {
      for (var i = 0, len = xs.length; i < len; i++) {
        var x = xs[i];
        if (pred(x, i)) {
          return Option.some(x);
        }
      }
      return Option.none();
    };
    var findIndex = function (xs, pred) {
      for (var i = 0, len = xs.length; i < len; i++) {
        var x = xs[i];
        if (pred(x, i)) {
          return Option.some(i);
        }
      }
      return Option.none();
    };
    var flatten = function (xs) {
      var r = [];
      for (var i = 0, len = xs.length; i < len; ++i) {
        if (!isArray(xs[i])) {
          throw new Error('Arr.flatten item ' + i + ' was not an array, input: ' + xs);
        }
        nativePush.apply(r, xs[i]);
      }
      return r;
    };
    var from$1 = isFunction(Array.from) ? Array.from : function (x) {
      return nativeSlice.call(x);
    };

    var defaultMenus = {
      file: {
        title: 'File',
        items: 'newdocument restoredraft | preview | print'
      },
      edit: {
        title: 'Edit',
        items: 'undo redo | cut copy paste pastetext | selectall'
      },
      view: {
        title: 'View',
        items: 'code | visualaid visualchars visualblocks | spellchecker | preview fullscreen'
      },
      insert: {
        title: 'Insert',
        items: 'image link media template codesample inserttable | charmap hr | pagebreak nonbreaking anchor toc | insertdatetime'
      },
      format: {
        title: 'Format',
        items: 'bold italic underline strikethrough superscript subscript codeformat | blockformats align | removeformat'
      },
      tools: {
        title: 'Tools',
        items: 'spellchecker spellcheckerlanguage | a11ycheck code'
      },
      table: { title: 'Table' },
      help: { title: 'Help' }
    };
    var delimiterMenuNamePair = function () {
      return {
        name: '|',
        item: { text: '|' }
      };
    };
    var createMenuNameItemPair = function (name, item) {
      var menuItem = item ? {
        name: name,
        item: item
      } : null;
      return name === '|' ? delimiterMenuNamePair() : menuItem;
    };
    var hasItemName = function (namedMenuItems, name) {
      return findIndex(namedMenuItems, function (namedMenuItem) {
        return namedMenuItem.name === name;
      }).isSome();
    };
    var isSeparator = function (namedMenuItem) {
      return namedMenuItem && namedMenuItem.item.text === '|';
    };
    var cleanupMenu = function (namedMenuItems, removedMenuItems) {
      var menuItemsPass1 = filter(namedMenuItems, function (namedMenuItem) {
        return removedMenuItems.hasOwnProperty(namedMenuItem.name) === false;
      });
      var menuItemsPass2 = filter(menuItemsPass1, function (namedMenuItem, i) {
        return !isSeparator(namedMenuItem) || !isSeparator(menuItemsPass1[i - 1]);
      });
      return filter(menuItemsPass2, function (namedMenuItem, i) {
        return !isSeparator(namedMenuItem) || i > 0 && i < menuItemsPass2.length - 1;
      });
    };
    var createMenu = function (editorMenuItems, menus, removedMenuItems, context) {
      var menuButton, menu, namedMenuItems, isUserDefined;
      if (menus) {
        menu = menus[context];
        isUserDefined = true;
      } else {
        menu = defaultMenus[context];
      }
      if (menu) {
        menuButton = { text: menu.title };
        namedMenuItems = [];
        global$2.each((menu.items || '').split(/[ ,]/), function (name) {
          var namedMenuItem = createMenuNameItemPair(name, editorMenuItems[name]);
          if (namedMenuItem) {
            namedMenuItems.push(namedMenuItem);
          }
        });
        if (!isUserDefined) {
          global$2.each(editorMenuItems, function (item, name) {
            if (item.context === context && !hasItemName(namedMenuItems, name)) {
              if (item.separator === 'before') {
                namedMenuItems.push(delimiterMenuNamePair());
              }
              if (item.prependToContext) {
                namedMenuItems.unshift(createMenuNameItemPair(name, item));
              } else {
                namedMenuItems.push(createMenuNameItemPair(name, item));
              }
              if (item.separator === 'after') {
                namedMenuItems.push(delimiterMenuNamePair());
              }
            }
          });
        }
        menuButton.menu = map(cleanupMenu(namedMenuItems, removedMenuItems), function (menuItem) {
          return menuItem.item;
        });
        if (!menuButton.menu.length) {
          return null;
        }
      }
      return menuButton;
    };
    var getDefaultMenubar = function (editor) {
      var name;
      var defaultMenuBar = [];
      var menu = getMenu(editor);
      if (menu) {
        for (name in menu) {
          defaultMenuBar.push(name);
        }
      } else {
        for (name in defaultMenus) {
          defaultMenuBar.push(name);
        }
      }
      return defaultMenuBar;
    };
    var createMenuButtons = function (editor) {
      var menuButtons = [];
      var defaultMenuBar = getDefaultMenubar(editor);
      var removedMenuItems = global$2.makeMap(getRemovedMenuItems(editor).split(/[ ,]/));
      var menubar = getMenubar(editor);
      var enabledMenuNames = typeof menubar === 'string' ? menubar.split(/[ ,]/) : defaultMenuBar;
      for (var i = 0; i < enabledMenuNames.length; i++) {
        var menuItems = enabledMenuNames[i];
        var menu = createMenu(editor.menuItems, getMenu(editor), removedMenuItems, menuItems);
        if (menu) {
          menuButtons.push(menu);
        }
      }
      return menuButtons;
    };
    var Menubar = { createMenuButtons: createMenuButtons };

    var DOM$1 = global$3.DOM;
    var getSize = function (elm) {
      return {
        width: elm.clientWidth,
        height: elm.clientHeight
      };
    };
    var resizeTo = function (editor, width, height) {
      var containerElm, iframeElm, containerSize, iframeSize;
      containerElm = editor.getContainer();
      iframeElm = editor.getContentAreaContainer().firstChild;
      containerSize = getSize(containerElm);
      iframeSize = getSize(iframeElm);
      if (width !== null) {
        width = Math.max(getMinWidth(editor), width);
        width = Math.min(getMaxWidth(editor), width);
        DOM$1.setStyle(containerElm, 'width', width + (containerSize.width - iframeSize.width));
        DOM$1.setStyle(iframeElm, 'width', width);
      }
      height = Math.max(getMinHeight(editor), height);
      height = Math.min(getMaxHeight(editor), height);
      DOM$1.setStyle(iframeElm, 'height', height);
      Events.fireResizeEditor(editor);
    };
    var resizeBy = function (editor, dw, dh) {
      var elm = editor.getContentAreaContainer();
      resizeTo(editor, elm.clientWidth + dw, elm.clientHeight + dh);
    };
    var Resize = {
      resizeTo: resizeTo,
      resizeBy: resizeBy
    };

    var global$8 = tinymce.util.Tools.resolve('tinymce.Env');

    var api = function (elm) {
      return {
        element: function () {
          return elm;
        }
      };
    };
    var trigger = function (sidebar, panel, callbackName) {
      var callback = sidebar.settings[callbackName];
      if (callback) {
        callback(api(panel.getEl('body')));
      }
    };
    var hidePanels = function (name, container, sidebars) {
      global$2.each(sidebars, function (sidebar) {
        var panel = container.items().filter('#' + sidebar.name)[0];
        if (panel && panel.visible() && sidebar.name !== name) {
          trigger(sidebar, panel, 'onhide');
          panel.visible(false);
        }
      });
    };
    var deactivateButtons = function (toolbar) {
      toolbar.items().each(function (ctrl) {
        ctrl.active(false);
      });
    };
    var findSidebar = function (sidebars, name) {
      return global$2.grep(sidebars, function (sidebar) {
        return sidebar.name === name;
      })[0];
    };
    var showPanel = function (editor, name, sidebars) {
      return function (e) {
        var btnCtrl = e.control;
        var container = btnCtrl.parents().filter('panel')[0];
        var panel = container.find('#' + name)[0];
        var sidebar = findSidebar(sidebars, name);
        hidePanels(name, container, sidebars);
        deactivateButtons(btnCtrl.parent());
        if (panel && panel.visible()) {
          trigger(sidebar, panel, 'onhide');
          panel.hide();
          btnCtrl.active(false);
        } else {
          if (panel) {
            panel.show();
            trigger(sidebar, panel, 'onshow');
          } else {
            panel = global$4.create({
              type: 'container',
              name: name,
              layout: 'stack',
              classes: 'sidebar-panel',
              html: ''
            });
            container.prepend(panel);
            trigger(sidebar, panel, 'onrender');
            trigger(sidebar, panel, 'onshow');
          }
          btnCtrl.active(true);
        }
        Events.fireResizeEditor(editor);
      };
    };
    var isModernBrowser = function () {
      return !global$8.ie || global$8.ie >= 11;
    };
    var hasSidebar = function (editor) {
      return isModernBrowser() && editor.sidebars ? editor.sidebars.length > 0 : false;
    };
    var createSidebar = function (editor) {
      var buttons = global$2.map(editor.sidebars, function (sidebar) {
        var settings = sidebar.settings;
        return {
          type: 'button',
          icon: settings.icon,
          image: settings.image,
          tooltip: settings.tooltip,
          onclick: showPanel(editor, sidebar.name, editor.sidebars)
        };
      });
      return {
        type: 'panel',
        name: 'sidebar',
        layout: 'stack',
        classes: 'sidebar',
        items: [{
            type: 'toolbar',
            layout: 'stack',
            classes: 'sidebar-toolbar',
            items: buttons
          }]
      };
    };
    var Sidebar = {
      hasSidebar: hasSidebar,
      createSidebar: createSidebar
    };

    var fireSkinLoaded$1 = function (editor) {
      var done = function () {
        editor._skinLoaded = true;
        Events.fireSkinLoaded(editor);
      };
      return function () {
        if (editor.initialized) {
          done();
        } else {
          editor.on('init', done);
        }
      };
    };
    var SkinLoaded = { fireSkinLoaded: fireSkinLoaded$1 };

    var DOM$2 = global$3.DOM;
    var switchMode = function (panel) {
      return function (e) {
        panel.find('*').disabled(e.mode === 'readonly');
      };
    };
    var editArea = function (border) {
      return {
        type: 'panel',
        name: 'iframe',
        layout: 'stack',
        classes: 'edit-area',
        border: border,
        html: ''
      };
    };
    var editAreaContainer = function (editor) {
      return {
        type: 'panel',
        layout: 'stack',
        classes: 'edit-aria-container',
        border: '1 0 0 0',
        items: [
          editArea('0'),
          Sidebar.createSidebar(editor)
        ]
      };
    };
    var render = function (editor, theme, args) {
      var panel, resizeHandleCtrl, startSize;
      if (isSkinDisabled(editor) === false && args.skinUiCss) {
        DOM$2.styleSheetLoader.load(args.skinUiCss, SkinLoaded.fireSkinLoaded(editor));
      } else {
        SkinLoaded.fireSkinLoaded(editor)();
      }
      panel = theme.panel = global$4.create({
        type: 'panel',
        role: 'application',
        classes: 'tinymce',
        style: 'visibility: hidden',
        layout: 'stack',
        border: 1,
        items: [
          {
            type: 'container',
            classes: 'top-part',
            items: [
              hasMenubar(editor) === false ? null : {
                type: 'menubar',
                border: '0 0 1 0',
                items: Menubar.createMenuButtons(editor)
              },
              Toolbar.createToolbars(editor, getToolbarSize(editor))
            ]
          },
          Sidebar.hasSidebar(editor) ? editAreaContainer(editor) : editArea('1 0 0 0')
        ]
      });
      UiContainer.setUiContainer(editor, panel);
      if (getResize(editor) !== 'none') {
        resizeHandleCtrl = {
          type: 'resizehandle',
          direction: getResize(editor),
          onResizeStart: function () {
            var elm = editor.getContentAreaContainer().firstChild;
            startSize = {
              width: elm.clientWidth,
              height: elm.clientHeight
            };
          },
          onResize: function (e) {
            if (getResize(editor) === 'both') {
              Resize.resizeTo(editor, startSize.width + e.deltaX, startSize.height + e.deltaY);
            } else {
              Resize.resizeTo(editor, null, startSize.height + e.deltaY);
            }
          }
        };
      }
      if (hasStatusbar(editor)) {
        var linkHtml = '<a href="https://www.tiny.cloud/?utm_campaign=editor_referral&amp;utm_medium=poweredby&amp;utm_source=tinymce" rel="noopener" target="_blank" role="presentation" tabindex="-1">Tiny</a>';
        var html = global$5.translate([
          'Powered by {0}',
          linkHtml
        ]);
        var brandingLabel = isBrandingEnabled(editor) ? {
          type: 'label',
          classes: 'branding',
          html: ' ' + html
        } : null;
        panel.add({
          type: 'panel',
          name: 'statusbar',
          classes: 'statusbar',
          layout: 'flow',
          border: '1 0 0 0',
          ariaRoot: true,
          items: [
            {
              type: 'elementpath',
              editor: editor
            },
            resizeHandleCtrl,
            brandingLabel
          ]
        });
      }
      Events.fireBeforeRenderUI(editor);
      editor.on('SwitchMode', switchMode(panel));
      panel.renderBefore(args.targetNode).reflow();
      if (isReadOnly(editor)) {
        editor.setMode('readonly');
      }
      if (args.width) {
        DOM$2.setStyle(panel.getEl(), 'width', args.width);
      }
      editor.on('remove', function () {
        panel.remove();
        panel = null;
      });
      A11y.addKeys(editor, panel);
      ContextToolbars.addContextualToolbars(editor);
      return {
        iframeContainer: panel.find('#iframe')[0].getEl(),
        editorContainer: panel.getEl()
      };
    };
    var Iframe = { render: render };

    var global$9 = tinymce.util.Tools.resolve('tinymce.dom.DomQuery');

    var count = 0;
    var funcs = {
      id: function () {
        return 'mceu_' + count++;
      },
      create: function (name, attrs, children) {
        var elm = domGlobals.document.createElement(name);
        global$3.DOM.setAttribs(elm, attrs);
        if (typeof children === 'string') {
          elm.innerHTML = children;
        } else {
          global$2.each(children, function (child) {
            if (child.nodeType) {
              elm.appendChild(child);
            }
          });
        }
        return elm;
      },
      createFragment: function (html) {
        return global$3.DOM.createFragment(html);
      },
      getWindowSize: function () {
        return global$3.DOM.getViewPort();
      },
      getSize: function (elm) {
        var width, height;
        if (elm.getBoundingClientRect) {
          var rect = elm.getBoundingClientRect();
          width = Math.max(rect.width || rect.right - rect.left, elm.offsetWidth);
          height = Math.max(rect.height || rect.bottom - rect.bottom, elm.offsetHeight);
        } else {
          width = elm.offsetWidth;
          height = elm.offsetHeight;
        }
        return {
          width: width,
          height: height
        };
      },
      getPos: function (elm, root) {
        return global$3.DOM.getPos(elm, root || funcs.getContainer());
      },
      getContainer: function () {
        return global$8.container ? global$8.container : domGlobals.document.body;
      },
      getViewPort: function (win) {
        return global$3.DOM.getViewPort(win);
      },
      get: function (id) {
        return domGlobals.document.getElementById(id);
      },
      addClass: function (elm, cls) {
        return global$3.DOM.addClass(elm, cls);
      },
      removeClass: function (elm, cls) {
        return global$3.DOM.removeClass(elm, cls);
      },
      hasClass: function (elm, cls) {
        return global$3.DOM.hasClass(elm, cls);
      },
      toggleClass: function (elm, cls, state) {
        return global$3.DOM.toggleClass(elm, cls, state);
      },
      css: function (elm, name, value) {
        return global$3.DOM.setStyle(elm, name, value);
      },
      getRuntimeStyle: function (elm, name) {
        return global$3.DOM.getStyle(elm, name, true);
      },
      on: function (target, name, callback, scope) {
        return global$3.DOM.bind(target, name, callback, scope);
      },
      off: function (target, name, callback) {
        return global$3.DOM.unbind(target, name, callback);
      },
      fire: function (target, name, args) {
        return global$3.DOM.fire(target, name, args);
      },
      innerHtml: function (elm, html) {
        global$3.DOM.setHTML(elm, html);
      }
    };

    var isStatic = function (elm) {
      return funcs.getRuntimeStyle(elm, 'position') === 'static';
    };
    var isFixed = function (ctrl) {
      return ctrl.state.get('fixed');
    };
    function calculateRelativePosition(ctrl, targetElm, rel) {
      var ctrlElm, pos, x, y, selfW, selfH, targetW, targetH, viewport, size;
      viewport = getWindowViewPort();
      pos = funcs.getPos(targetElm, UiContainer.getUiContainer(ctrl));
      x = pos.x;
      y = pos.y;
      if (isFixed(ctrl) && isStatic(domGlobals.document.body)) {
        x -= viewport.x;
        y -= viewport.y;
      }
      ctrlElm = ctrl.getEl();
      size = funcs.getSize(ctrlElm);
      selfW = size.width;
      selfH = size.height;
      size = funcs.getSize(targetElm);
      targetW = size.width;
      targetH = size.height;
      rel = (rel || '').split('');
      if (rel[0] === 'b') {
        y += targetH;
      }
      if (rel[1] === 'r') {
        x += targetW;
      }
      if (rel[0] === 'c') {
        y += Math.round(targetH / 2);
      }
      if (rel[1] === 'c') {
        x += Math.round(targetW / 2);
      }
      if (rel[3] === 'b') {
        y -= selfH;
      }
      if (rel[4] === 'r') {
        x -= selfW;
      }
      if (rel[3] === 'c') {
        y -= Math.round(selfH / 2);
      }
      if (rel[4] === 'c') {
        x -= Math.round(selfW / 2);
      }
      return {
        x: x,
        y: y,
        w: selfW,
        h: selfH
      };
    }
    var getUiContainerViewPort = function (customUiContainer) {
      return {
        x: 0,
        y: 0,
        w: customUiContainer.scrollWidth - 1,
        h: customUiContainer.scrollHeight - 1
      };
    };
    var getWindowViewPort = function () {
      var win = domGlobals.window;
      var x = Math.max(win.pageXOffset, domGlobals.document.body.scrollLeft, domGlobals.document.documentElement.scrollLeft);
      var y = Math.max(win.pageYOffset, domGlobals.document.body.scrollTop, domGlobals.document.documentElement.scrollTop);
      var w = win.innerWidth || domGlobals.document.documentElement.clientWidth;
      var h = win.innerHeight || domGlobals.document.documentElement.clientHeight;
      return {
        x: x,
        y: y,
        w: w,
        h: h
      };
    };
    var getViewPortRect = function (ctrl) {
      var customUiContainer = UiContainer.getUiContainer(ctrl);
      return customUiContainer && !isFixed(ctrl) ? getUiContainerViewPort(customUiContainer) : getWindowViewPort();
    };
    var Movable = {
      testMoveRel: function (elm, rels) {
        var viewPortRect = getViewPortRect(this);
        for (var i = 0; i < rels.length; i++) {
          var pos = calculateRelativePosition(this, elm, rels[i]);
          if (isFixed(this)) {
            if (pos.x > 0 && pos.x + pos.w < viewPortRect.w && pos.y > 0 && pos.y + pos.h < viewPortRect.h) {
              return rels[i];
            }
          } else {
            if (pos.x > viewPortRect.x && pos.x + pos.w < viewPortRect.w + viewPortRect.x && pos.y > viewPortRect.y && pos.y + pos.h < viewPortRect.h + viewPortRect.y) {
              return rels[i];
            }
          }
        }
        return rels[0];
      },
      moveRel: function (elm, rel) {
        if (typeof rel !== 'string') {
          rel = this.testMoveRel(elm, rel);
        }
        var pos = calculateRelativePosition(this, elm, rel);
        return this.moveTo(pos.x, pos.y);
      },
      moveBy: function (dx, dy) {
        var self = this, rect = self.layoutRect();
        self.moveTo(rect.x + dx, rect.y + dy);
        return self;
      },
      moveTo: function (x, y) {
        var self = this;
        function constrain(value, max, size) {
          if (value < 0) {
            return 0;
          }
          if (value + size > max) {
            value = max - size;
            return value < 0 ? 0 : value;
          }
          return value;
        }
        if (self.settings.constrainToViewport) {
          var viewPortRect = getViewPortRect(this);
          var layoutRect = self.layoutRect();
          x = constrain(x, viewPortRect.w + viewPortRect.x, layoutRect.w);
          y = constrain(y, viewPortRect.h + viewPortRect.y, layoutRect.h);
        }
        var uiContainer = UiContainer.getUiContainer(self);
        if (uiContainer && isStatic(uiContainer) && !isFixed(self)) {
          x -= uiContainer.scrollLeft;
          y -= uiContainer.scrollTop;
        }
        if (uiContainer) {
          x += 1;
          y += 1;
        }
        if (self.state.get('rendered')) {
          self.layoutRect({
            x: x,
            y: y
          }).repaint();
        } else {
          self.settings.x = x;
          self.settings.y = y;
        }
        self.fire('move', {
          x: x,
          y: y
        });
        return self;
      }
    };

    var global$a = tinymce.util.Tools.resolve('tinymce.util.Class');

    var global$b = tinymce.util.Tools.resolve('tinymce.util.EventDispatcher');

    var BoxUtils = {
      parseBox: function (value) {
        var len;
        var radix = 10;
        if (!value) {
          return;
        }
        if (typeof value === 'number') {
          value = value || 0;
          return {
            top: value,
            left: value,
            bottom: value,
            right: value
          };
        }
        value = value.split(' ');
        len = value.length;
        if (len === 1) {
          value[1] = value[2] = value[3] = value[0];
        } else if (len === 2) {
          value[2] = value[0];
          value[3] = value[1];
        } else if (len === 3) {
          value[3] = value[1];
        }
        return {
          top: parseInt(value[0], radix) || 0,
          right: parseInt(value[1], radix) || 0,
          bottom: parseInt(value[2], radix) || 0,
          left: parseInt(value[3], radix) || 0
        };
      },
      measureBox: function (elm, prefix) {
        function getStyle(name) {
          var defaultView = elm.ownerDocument.defaultView;
          if (defaultView) {
            var computedStyle = defaultView.getComputedStyle(elm, null);
            if (computedStyle) {
              name = name.replace(/[A-Z]/g, function (a) {
                return '-' + a;
              });
              return computedStyle.getPropertyValue(name);
            } else {
              return null;
            }
          }
          return elm.currentStyle[name];
        }
        function getSide(name) {
          var val = parseFloat(getStyle(name));
          return isNaN(val) ? 0 : val;
        }
        return {
          top: getSide(prefix + 'TopWidth'),
          right: getSide(prefix + 'RightWidth'),
          bottom: getSide(prefix + 'BottomWidth'),
          left: getSide(prefix + 'LeftWidth')
        };
      }
    };

    function noop$1() {
    }
    function ClassList(onchange) {
      this.cls = [];
      this.cls._map = {};
      this.onchange = onchange || noop$1;
      this.prefix = '';
    }
    global$2.extend(ClassList.prototype, {
      add: function (cls) {
        if (cls && !this.contains(cls)) {
          this.cls._map[cls] = true;
          this.cls.push(cls);
          this._change();
        }
        return this;
      },
      remove: function (cls) {
        if (this.contains(cls)) {
          var i = void 0;
          for (i = 0; i < this.cls.length; i++) {
            if (this.cls[i] === cls) {
              break;
            }
          }
          this.cls.splice(i, 1);
          delete this.cls._map[cls];
          this._change();
        }
        return this;
      },
      toggle: function (cls, state) {
        var curState = this.contains(cls);
        if (curState !== state) {
          if (curState) {
            this.remove(cls);
          } else {
            this.add(cls);
          }
          this._change();
        }
        return this;
      },
      contains: function (cls) {
        return !!this.cls._map[cls];
      },
      _change: function () {
        delete this.clsValue;
        this.onchange.call(this);
      }
    });
    ClassList.prototype.toString = function () {
      var value;
      if (this.clsValue) {
        return this.clsValue;
      }
      value = '';
      for (var i = 0; i < this.cls.length; i++) {
        if (i > 0) {
          value += ' ';
        }
        value += this.prefix + this.cls[i];
      }
      return value;
    };

    function unique(array) {
      var uniqueItems = [];
      var i = array.length, item;
      while (i--) {
        item = array[i];
        if (!item.__checked) {
          uniqueItems.push(item);
          item.__checked = 1;
        }
      }
      i = uniqueItems.length;
      while (i--) {
        delete uniqueItems[i].__checked;
      }
      return uniqueItems;
    }
    var expression = /^([\w\\*]+)?(?:#([\w\-\\]+))?(?:\.([\w\\\.]+))?(?:\[\@?([\w\\]+)([\^\$\*!~]?=)([\w\\]+)\])?(?:\:(.+))?/i;
    var chunker = /((?:\((?:\([^()]+\)|[^()]+)+\)|\[(?:\[[^\[\]]*\]|['"][^'"]*['"]|[^\[\]'"]+)+\]|\\.|[^ >+~,(\[\\]+)+|[>+~])(\s*,\s*)?((?:.|\r|\n)*)/g;
    var whiteSpace = /^\s*|\s*$/g;
    var Collection;
    var Selector = global$a.extend({
      init: function (selector) {
        var match = this.match;
        function compileNameFilter(name) {
          if (name) {
            name = name.toLowerCase();
            return function (item) {
              return name === '*' || item.type === name;
            };
          }
        }
        function compileIdFilter(id) {
          if (id) {
            return function (item) {
              return item._name === id;
            };
          }
        }
        function compileClassesFilter(classes) {
          if (classes) {
            classes = classes.split('.');
            return function (item) {
              var i = classes.length;
              while (i--) {
                if (!item.classes.contains(classes[i])) {
                  return false;
                }
              }
              return true;
            };
          }
        }
        function compileAttrFilter(name, cmp, check) {
          if (name) {
            return function (item) {
              var value = item[name] ? item[name]() : '';
              return !cmp ? !!check : cmp === '=' ? value === check : cmp === '*=' ? value.indexOf(check) >= 0 : cmp === '~=' ? (' ' + value + ' ').indexOf(' ' + check + ' ') >= 0 : cmp === '!=' ? value !== check : cmp === '^=' ? value.indexOf(check) === 0 : cmp === '$=' ? value.substr(value.length - check.length) === check : false;
            };
          }
        }
        function compilePsuedoFilter(name) {
          var notSelectors;
          if (name) {
            name = /(?:not\((.+)\))|(.+)/i.exec(name);
            if (!name[1]) {
              name = name[2];
              return function (item, index, length) {
                return name === 'first' ? index === 0 : name === 'last' ? index === length - 1 : name === 'even' ? index % 2 === 0 : name === 'odd' ? index % 2 === 1 : item[name] ? item[name]() : false;
              };
            }
            notSelectors = parseChunks(name[1], []);
            return function (item) {
              return !match(item, notSelectors);
            };
          }
        }
        function compile(selector, filters, direct) {
          var parts;
          function add(filter) {
            if (filter) {
              filters.push(filter);
            }
          }
          parts = expression.exec(selector.replace(whiteSpace, ''));
          add(compileNameFilter(parts[1]));
          add(compileIdFilter(parts[2]));
          add(compileClassesFilter(parts[3]));
          add(compileAttrFilter(parts[4], parts[5], parts[6]));
          add(compilePsuedoFilter(parts[7]));
          filters.pseudo = !!parts[7];
          filters.direct = direct;
          return filters;
        }
        function parseChunks(selector, selectors) {
          var parts = [];
          var extra, matches, i;
          do {
            chunker.exec('');
            matches = chunker.exec(selector);
            if (matches) {
              selector = matches[3];
              parts.push(matches[1]);
              if (matches[2]) {
                extra = matches[3];
                break;
              }
            }
          } while (matches);
          if (extra) {
            parseChunks(extra, selectors);
          }
          selector = [];
          for (i = 0; i < parts.length; i++) {
            if (parts[i] !== '>') {
              selector.push(compile(parts[i], [], parts[i - 1] === '>'));
            }
          }
          selectors.push(selector);
          return selectors;
        }
        this._selectors = parseChunks(selector, []);
      },
      match: function (control, selectors) {
        var i, l, si, sl, selector, fi, fl, filters, index, length, siblings, count, item;
        selectors = selectors || this._selectors;
        for (i = 0, l = selectors.length; i < l; i++) {
          selector = selectors[i];
          sl = selector.length;
          item = control;
          count = 0;
          for (si = sl - 1; si >= 0; si--) {
            filters = selector[si];
            while (item) {
              if (filters.pseudo) {
                siblings = item.parent().items();
                index = length = siblings.length;
                while (index--) {
                  if (siblings[index] === item) {
                    break;
                  }
                }
              }
              for (fi = 0, fl = filters.length; fi < fl; fi++) {
                if (!filters[fi](item, index, length)) {
                  fi = fl + 1;
                  break;
                }
              }
              if (fi === fl) {
                count++;
                break;
              } else {
                if (si === sl - 1) {
                  break;
                }
              }
              item = item.parent();
            }
          }
          if (count === sl) {
            return true;
          }
        }
        return false;
      },
      find: function (container) {
        var matches = [], i, l;
        var selectors = this._selectors;
        function collect(items, selector, index) {
          var i, l, fi, fl, item;
          var filters = selector[index];
          for (i = 0, l = items.length; i < l; i++) {
            item = items[i];
            for (fi = 0, fl = filters.length; fi < fl; fi++) {
              if (!filters[fi](item, i, l)) {
                fi = fl + 1;
                break;
              }
            }
            if (fi === fl) {
              if (index === selector.length - 1) {
                matches.push(item);
              } else {
                if (item.items) {
                  collect(item.items(), selector, index + 1);
                }
              }
            } else if (filters.direct) {
              return;
            }
            if (item.items) {
              collect(item.items(), selector, index);
            }
          }
        }
        if (container.items) {
          for (i = 0, l = selectors.length; i < l; i++) {
            collect(container.items(), selectors[i], 0);
          }
          if (l > 1) {
            matches = unique(matches);
          }
        }
        if (!Collection) {
          Collection = Selector.Collection;
        }
        return new Collection(matches);
      }
    });

    var Collection$1, proto;
    var push = Array.prototype.push, slice = Array.prototype.slice;
    proto = {
      length: 0,
      init: function (items) {
        if (items) {
          this.add(items);
        }
      },
      add: function (items) {
        var self = this;
        if (!global$2.isArray(items)) {
          if (items instanceof Collection$1) {
            self.add(items.toArray());
          } else {
            push.call(self, items);
          }
        } else {
          push.apply(self, items);
        }
        return self;
      },
      set: function (items) {
        var self = this;
        var len = self.length;
        var i;
        self.length = 0;
        self.add(items);
        for (i = self.length; i < len; i++) {
          delete self[i];
        }
        return self;
      },
      filter: function (selector) {
        var self = this;
        var i, l;
        var matches = [];
        var item, match;
        if (typeof selector === 'string') {
          selector = new Selector(selector);
          match = function (item) {
            return selector.match(item);
          };
        } else {
          match = selector;
        }
        for (i = 0, l = self.length; i < l; i++) {
          item = self[i];
          if (match(item)) {
            matches.push(item);
          }
        }
        return new Collection$1(matches);
      },
      slice: function () {
        return new Collection$1(slice.apply(this, arguments));
      },
      eq: function (index) {
        return index === -1 ? this.slice(index) : this.slice(index, +index + 1);
      },
      each: function (callback) {
        global$2.each(this, callback);
        return this;
      },
      toArray: function () {
        return global$2.toArray(this);
      },
      indexOf: function (ctrl) {
        var self = this;
        var i = self.length;
        while (i--) {
          if (self[i] === ctrl) {
            break;
          }
        }
        return i;
      },
      reverse: function () {
        return new Collection$1(global$2.toArray(this).reverse());
      },
      hasClass: function (cls) {
        return this[0] ? this[0].classes.contains(cls) : false;
      },
      prop: function (name, value) {
        var self = this;
        var item;
        if (value !== undefined) {
          self.each(function (item) {
            if (item[name]) {
              item[name](value);
            }
          });
          return self;
        }
        item = self[0];
        if (item && item[name]) {
          return item[name]();
        }
      },
      exec: function (name) {
        var self = this, args = global$2.toArray(arguments).slice(1);
        self.each(function (item) {
          if (item[name]) {
            item[name].apply(item, args);
          }
        });
        return self;
      },
      remove: function () {
        var i = this.length;
        while (i--) {
          this[i].remove();
        }
        return this;
      },
      addClass: function (cls) {
        return this.each(function (item) {
          item.classes.add(cls);
        });
      },
      removeClass: function (cls) {
        return this.each(function (item) {
          item.classes.remove(cls);
        });
      }
    };
    global$2.each('fire on off show hide append prepend before after reflow'.split(' '), function (name) {
      proto[name] = function () {
        var args = global$2.toArray(arguments);
        this.each(function (ctrl) {
          if (name in ctrl) {
            ctrl[name].apply(ctrl, args);
          }
        });
        return this;
      };
    });
    global$2.each('text name disabled active selected checked visible parent value data'.split(' '), function (name) {
      proto[name] = function (value) {
        return this.prop(name, value);
      };
    });
    Collection$1 = global$a.extend(proto);
    Selector.Collection = Collection$1;
    var Collection$2 = Collection$1;

    var Binding = function (settings) {
      this.create = settings.create;
    };
    Binding.create = function (model, name) {
      return new Binding({
        create: function (otherModel, otherName) {
          var bindings;
          var fromSelfToOther = function (e) {
            otherModel.set(otherName, e.value);
          };
          var fromOtherToSelf = function (e) {
            model.set(name, e.value);
          };
          otherModel.on('change:' + otherName, fromOtherToSelf);
          model.on('change:' + name, fromSelfToOther);
          bindings = otherModel._bindings;
          if (!bindings) {
            bindings = otherModel._bindings = [];
            otherModel.on('destroy', function () {
              var i = bindings.length;
              while (i--) {
                bindings[i]();
              }
            });
          }
          bindings.push(function () {
            model.off('change:' + name, fromSelfToOther);
          });
          return model.get(name);
        }
      });
    };

    var global$c = tinymce.util.Tools.resolve('tinymce.util.Observable');

    function isNode(node) {
      return node.nodeType > 0;
    }
    function isEqual(a, b) {
      var k, checked;
      if (a === b) {
        return true;
      }
      if (a === null || b === null) {
        return a === b;
      }
      if (typeof a !== 'object' || typeof b !== 'object') {
        return a === b;
      }
      if (global$2.isArray(b)) {
        if (a.length !== b.length) {
          return false;
        }
        k = a.length;
        while (k--) {
          if (!isEqual(a[k], b[k])) {
            return false;
          }
        }
      }
      if (isNode(a) || isNode(b)) {
        return a === b;
      }
      checked = {};
      for (k in b) {
        if (!isEqual(a[k], b[k])) {
          return false;
        }
        checked[k] = true;
      }
      for (k in a) {
        if (!checked[k] && !isEqual(a[k], b[k])) {
          return false;
        }
      }
      return true;
    }
    var ObservableObject = global$a.extend({
      Mixins: [global$c],
      init: function (data) {
        var name, value;
        data = data || {};
        for (name in data) {
          value = data[name];
          if (value instanceof Binding) {
            data[name] = value.create(this, name);
          }
        }
        this.data = data;
      },
      set: function (name, value) {
        var key, args;
        var oldValue = this.data[name];
        if (value instanceof Binding) {
          value = value.create(this, name);
        }
        if (typeof name === 'object') {
          for (key in name) {
            this.set(key, name[key]);
          }
          return this;
        }
        if (!isEqual(oldValue, value)) {
          this.data[name] = value;
          args = {
            target: this,
            name: name,
            value: value,
            oldValue: oldValue
          };
          this.fire('change:' + name, args);
          this.fire('change', args);
        }
        return this;
      },
      get: function (name) {
        return this.data[name];
      },
      has: function (name) {
        return name in this.data;
      },
      bind: function (name) {
        return Binding.create(this, name);
      },
      destroy: function () {
        this.fire('destroy');
      }
    });

    var dirtyCtrls = {}, animationFrameRequested;
    var ReflowQueue = {
      add: function (ctrl) {
        var parent = ctrl.parent();
        if (parent) {
          if (!parent._layout || parent._layout.isNative()) {
            return;
          }
          if (!dirtyCtrls[parent._id]) {
            dirtyCtrls[parent._id] = parent;
          }
          if (!animationFrameRequested) {
            animationFrameRequested = true;
            global$7.requestAnimationFrame(function () {
              var id, ctrl;
              animationFrameRequested = false;
              for (id in dirtyCtrls) {
                ctrl = dirtyCtrls[id];
                if (ctrl.state.get('rendered')) {
                  ctrl.reflow();
                }
              }
              dirtyCtrls = {};
            }, domGlobals.document.body);
          }
        }
      },
      remove: function (ctrl) {
        if (dirtyCtrls[ctrl._id]) {
          delete dirtyCtrls[ctrl._id];
        }
      }
    };

    var hasMouseWheelEventSupport = 'onmousewheel' in domGlobals.document;
    var hasWheelEventSupport = false;
    var classPrefix = 'mce-';
    var Control, idCounter = 0;
    var proto$1 = {
      Statics: { classPrefix: classPrefix },
      isRtl: function () {
        return Control.rtl;
      },
      classPrefix: classPrefix,
      init: function (settings) {
        var self = this;
        var classes, defaultClasses;
        function applyClasses(classes) {
          var i;
          classes = classes.split(' ');
          for (i = 0; i < classes.length; i++) {
            self.classes.add(classes[i]);
          }
        }
        self.settings = settings = global$2.extend({}, self.Defaults, settings);
        self._id = settings.id || 'mceu_' + idCounter++;
        self._aria = { role: settings.role };
        self._elmCache = {};
        self.$ = global$9;
        self.state = new ObservableObject({
          visible: true,
          active: false,
          disabled: false,
          value: ''
        });
        self.data = new ObservableObject(settings.data);
        self.classes = new ClassList(function () {
          if (self.state.get('rendered')) {
            self.getEl().className = this.toString();
          }
        });
        self.classes.prefix = self.classPrefix;
        classes = settings.classes;
        if (classes) {
          if (self.Defaults) {
            defaultClasses = self.Defaults.classes;
            if (defaultClasses && classes !== defaultClasses) {
              applyClasses(defaultClasses);
            }
          }
          applyClasses(classes);
        }
        global$2.each('title text name visible disabled active value'.split(' '), function (name) {
          if (name in settings) {
            self[name](settings[name]);
          }
        });
        self.on('click', function () {
          if (self.disabled()) {
            return false;
          }
        });
        self.settings = settings;
        self.borderBox = BoxUtils.parseBox(settings.border);
        self.paddingBox = BoxUtils.parseBox(settings.padding);
        self.marginBox = BoxUtils.parseBox(settings.margin);
        if (settings.hidden) {
          self.hide();
        }
      },
      Properties: 'parent,name',
      getContainerElm: function () {
        var uiContainer = UiContainer.getUiContainer(this);
        return uiContainer ? uiContainer : funcs.getContainer();
      },
      getParentCtrl: function (elm) {
        var ctrl;
        var lookup = this.getRoot().controlIdLookup;
        while (elm && lookup) {
          ctrl = lookup[elm.id];
          if (ctrl) {
            break;
          }
          elm = elm.parentNode;
        }
        return ctrl;
      },
      initLayoutRect: function () {
        var self = this;
        var settings = self.settings;
        var borderBox, layoutRect;
        var elm = self.getEl();
        var width, height, minWidth, minHeight, autoResize;
        var startMinWidth, startMinHeight, initialSize;
        borderBox = self.borderBox = self.borderBox || BoxUtils.measureBox(elm, 'border');
        self.paddingBox = self.paddingBox || BoxUtils.measureBox(elm, 'padding');
        self.marginBox = self.marginBox || BoxUtils.measureBox(elm, 'margin');
        initialSize = funcs.getSize(elm);
        startMinWidth = settings.minWidth;
        startMinHeight = settings.minHeight;
        minWidth = startMinWidth || initialSize.width;
        minHeight = startMinHeight || initialSize.height;
        width = settings.width;
        height = settings.height;
        autoResize = settings.autoResize;
        autoResize = typeof autoResize !== 'undefined' ? autoResize : !width && !height;
        width = width || minWidth;
        height = height || minHeight;
        var deltaW = borderBox.left + borderBox.right;
        var deltaH = borderBox.top + borderBox.bottom;
        var maxW = settings.maxWidth || 65535;
        var maxH = settings.maxHeight || 65535;
        self._layoutRect = layoutRect = {
          x: settings.x || 0,
          y: settings.y || 0,
          w: width,
          h: height,
          deltaW: deltaW,
          deltaH: deltaH,
          contentW: width - deltaW,
          contentH: height - deltaH,
          innerW: width - deltaW,
          innerH: height - deltaH,
          startMinWidth: startMinWidth || 0,
          startMinHeight: startMinHeight || 0,
          minW: Math.min(minWidth, maxW),
          minH: Math.min(minHeight, maxH),
          maxW: maxW,
          maxH: maxH,
          autoResize: autoResize,
          scrollW: 0
        };
        self._lastLayoutRect = {};
        return layoutRect;
      },
      layoutRect: function (newRect) {
        var self = this;
        var curRect = self._layoutRect, lastLayoutRect, size, deltaWidth, deltaHeight, repaintControls;
        if (!curRect) {
          curRect = self.initLayoutRect();
        }
        if (newRect) {
          deltaWidth = curRect.deltaW;
          deltaHeight = curRect.deltaH;
          if (newRect.x !== undefined) {
            curRect.x = newRect.x;
          }
          if (newRect.y !== undefined) {
            curRect.y = newRect.y;
          }
          if (newRect.minW !== undefined) {
            curRect.minW = newRect.minW;
          }
          if (newRect.minH !== undefined) {
            curRect.minH = newRect.minH;
          }
          size = newRect.w;
          if (size !== undefined) {
            size = size < curRect.minW ? curRect.minW : size;
            size = size > curRect.maxW ? curRect.maxW : size;
            curRect.w = size;
            curRect.innerW = size - deltaWidth;
          }
          size = newRect.h;
          if (size !== undefined) {
            size = size < curRect.minH ? curRect.minH : size;
            size = size > curRect.maxH ? curRect.maxH : size;
            curRect.h = size;
            curRect.innerH = size - deltaHeight;
          }
          size = newRect.innerW;
          if (size !== undefined) {
            size = size < curRect.minW - deltaWidth ? curRect.minW - deltaWidth : size;
            size = size > curRect.maxW - deltaWidth ? curRect.maxW - deltaWidth : size;
            curRect.innerW = size;
            curRect.w = size + deltaWidth;
          }
          size = newRect.innerH;
          if (size !== undefined) {
            size = size < curRect.minH - deltaHeight ? curRect.minH - deltaHeight : size;
            size = size > curRect.maxH - deltaHeight ? curRect.maxH - deltaHeight : size;
            curRect.innerH = size;
            curRect.h = size + deltaHeight;
          }
          if (newRect.contentW !== undefined) {
            curRect.contentW = newRect.contentW;
          }
          if (newRect.contentH !== undefined) {
            curRect.contentH = newRect.contentH;
          }
          lastLayoutRect = self._lastLayoutRect;
          if (lastLayoutRect.x !== curRect.x || lastLayoutRect.y !== curRect.y || lastLayoutRect.w !== curRect.w || lastLayoutRect.h !== curRect.h) {
            repaintControls = Control.repaintControls;
            if (repaintControls) {
              if (repaintControls.map && !repaintControls.map[self._id]) {
                repaintControls.push(self);
                repaintControls.map[self._id] = true;
              }
            }
            lastLayoutRect.x = curRect.x;
            lastLayoutRect.y = curRect.y;
            lastLayoutRect.w = curRect.w;
            lastLayoutRect.h = curRect.h;
          }
          return self;
        }
        return curRect;
      },
      repaint: function () {
        var self = this;
        var style, bodyStyle, bodyElm, rect, borderBox;
        var borderW, borderH, lastRepaintRect, round, value;
        round = !domGlobals.document.createRange ? Math.round : function (value) {
          return value;
        };
        style = self.getEl().style;
        rect = self._layoutRect;
        lastRepaintRect = self._lastRepaintRect || {};
        borderBox = self.borderBox;
        borderW = borderBox.left + borderBox.right;
        borderH = borderBox.top + borderBox.bottom;
        if (rect.x !== lastRepaintRect.x) {
          style.left = round(rect.x) + 'px';
          lastRepaintRect.x = rect.x;
        }
        if (rect.y !== lastRepaintRect.y) {
          style.top = round(rect.y) + 'px';
          lastRepaintRect.y = rect.y;
        }
        if (rect.w !== lastRepaintRect.w) {
          value = round(rect.w - borderW);
          style.width = (value >= 0 ? value : 0) + 'px';
          lastRepaintRect.w = rect.w;
        }
        if (rect.h !== lastRepaintRect.h) {
          value = round(rect.h - borderH);
          style.height = (value >= 0 ? value : 0) + 'px';
          lastRepaintRect.h = rect.h;
        }
        if (self._hasBody && rect.innerW !== lastRepaintRect.innerW) {
          value = round(rect.innerW);
          bodyElm = self.getEl('body');
          if (bodyElm) {
            bodyStyle = bodyElm.style;
            bodyStyle.width = (value >= 0 ? value : 0) + 'px';
          }
          lastRepaintRect.innerW = rect.innerW;
        }
        if (self._hasBody && rect.innerH !== lastRepaintRect.innerH) {
          value = round(rect.innerH);
          bodyElm = bodyElm || self.getEl('body');
          if (bodyElm) {
            bodyStyle = bodyStyle || bodyElm.style;
            bodyStyle.height = (value >= 0 ? value : 0) + 'px';
          }
          lastRepaintRect.innerH = rect.innerH;
        }
        self._lastRepaintRect = lastRepaintRect;
        self.fire('repaint', {}, false);
      },
      updateLayoutRect: function () {
        var self = this;
        self.parent()._lastRect = null;
        funcs.css(self.getEl(), {
          width: '',
          height: ''
        });
        self._layoutRect = self._lastRepaintRect = self._lastLayoutRect = null;
        self.initLayoutRect();
      },
      on: function (name, callback) {
        var self = this;
        function resolveCallbackName(name) {
          var callback, scope;
          if (typeof name !== 'string') {
            return name;
          }
          return function (e) {
            if (!callback) {
              self.parentsAndSelf().each(function (ctrl) {
                var callbacks = ctrl.settings.callbacks;
                if (callbacks && (callback = callbacks[name])) {
                  scope = ctrl;
                  return false;
                }
              });
            }
            if (!callback) {
              e.action = name;
              this.fire('execute', e);
              return;
            }
            return callback.call(scope, e);
          };
        }
        getEventDispatcher(self).on(name, resolveCallbackName(callback));
        return self;
      },
      off: function (name, callback) {
        getEventDispatcher(this).off(name, callback);
        return this;
      },
      fire: function (name, args, bubble) {
        var self = this;
        args = args || {};
        if (!args.control) {
          args.control = self;
        }
        args = getEventDispatcher(self).fire(name, args);
        if (bubble !== false && self.parent) {
          var parent = self.parent();
          while (parent && !args.isPropagationStopped()) {
            parent.fire(name, args, false);
            parent = parent.parent();
          }
        }
        return args;
      },
      hasEventListeners: function (name) {
        return getEventDispatcher(this).has(name);
      },
      parents: function (selector) {
        var self = this;
        var ctrl, parents = new Collection$2();
        for (ctrl = self.parent(); ctrl; ctrl = ctrl.parent()) {
          parents.add(ctrl);
        }
        if (selector) {
          parents = parents.filter(selector);
        }
        return parents;
      },
      parentsAndSelf: function (selector) {
        return new Collection$2(this).add(this.parents(selector));
      },
      next: function () {
        var parentControls = this.parent().items();
        return parentControls[parentControls.indexOf(this) + 1];
      },
      prev: function () {
        var parentControls = this.parent().items();
        return parentControls[parentControls.indexOf(this) - 1];
      },
      innerHtml: function (html) {
        this.$el.html(html);
        return this;
      },
      getEl: function (suffix) {
        var id = suffix ? this._id + '-' + suffix : this._id;
        if (!this._elmCache[id]) {
          this._elmCache[id] = global$9('#' + id)[0];
        }
        return this._elmCache[id];
      },
      show: function () {
        return this.visible(true);
      },
      hide: function () {
        return this.visible(false);
      },
      focus: function () {
        try {
          this.getEl().focus();
        } catch (ex) {
        }
        return this;
      },
      blur: function () {
        this.getEl().blur();
        return this;
      },
      aria: function (name, value) {
        var self = this, elm = self.getEl(self.ariaTarget);
        if (typeof value === 'undefined') {
          return self._aria[name];
        }
        self._aria[name] = value;
        if (self.state.get('rendered')) {
          elm.setAttribute(name === 'role' ? name : 'aria-' + name, value);
        }
        return self;
      },
      encode: function (text, translate) {
        if (translate !== false) {
          text = this.translate(text);
        }
        return (text || '').replace(/[&<>"]/g, function (match) {
          return '&#' + match.charCodeAt(0) + ';';
        });
      },
      translate: function (text) {
        return Control.translate ? Control.translate(text) : text;
      },
      before: function (items) {
        var self = this, parent = self.parent();
        if (parent) {
          parent.insert(items, parent.items().indexOf(self), true);
        }
        return self;
      },
      after: function (items) {
        var self = this, parent = self.parent();
        if (parent) {
          parent.insert(items, parent.items().indexOf(self));
        }
        return self;
      },
      remove: function () {
        var self = this;
        var elm = self.getEl();
        var parent = self.parent();
        var newItems, i;
        if (self.items) {
          var controls = self.items().toArray();
          i = controls.length;
          while (i--) {
            controls[i].remove();
          }
        }
        if (parent && parent.items) {
          newItems = [];
          parent.items().each(function (item) {
            if (item !== self) {
              newItems.push(item);
            }
          });
          parent.items().set(newItems);
          parent._lastRect = null;
        }
        if (self._eventsRoot && self._eventsRoot === self) {
          global$9(elm).off();
        }
        var lookup = self.getRoot().controlIdLookup;
        if (lookup) {
          delete lookup[self._id];
        }
        if (elm && elm.parentNode) {
          elm.parentNode.removeChild(elm);
        }
        self.state.set('rendered', false);
        self.state.destroy();
        self.fire('remove');
        return self;
      },
      renderBefore: function (elm) {
        global$9(elm).before(this.renderHtml());
        this.postRender();
        return this;
      },
      renderTo: function (elm) {
        global$9(elm || this.getContainerElm()).append(this.renderHtml());
        this.postRender();
        return this;
      },
      preRender: function () {
      },
      render: function () {
      },
      renderHtml: function () {
        return '<div id="' + this._id + '" class="' + this.classes + '"></div>';
      },
      postRender: function () {
        var self = this;
        var settings = self.settings;
        var elm, box, parent, name, parentEventsRoot;
        self.$el = global$9(self.getEl());
        self.state.set('rendered', true);
        for (name in settings) {
          if (name.indexOf('on') === 0) {
            self.on(name.substr(2), settings[name]);
          }
        }
        if (self._eventsRoot) {
          for (parent = self.parent(); !parentEventsRoot && parent; parent = parent.parent()) {
            parentEventsRoot = parent._eventsRoot;
          }
          if (parentEventsRoot) {
            for (name in parentEventsRoot._nativeEvents) {
              self._nativeEvents[name] = true;
            }
          }
        }
        bindPendingEvents(self);
        if (settings.style) {
          elm = self.getEl();
          if (elm) {
            elm.setAttribute('style', settings.style);
            elm.style.cssText = settings.style;
          }
        }
        if (self.settings.border) {
          box = self.borderBox;
          self.$el.css({
            'border-top-width': box.top,
            'border-right-width': box.right,
            'border-bottom-width': box.bottom,
            'border-left-width': box.left
          });
        }
        var root = self.getRoot();
        if (!root.controlIdLookup) {
          root.controlIdLookup = {};
        }
        root.controlIdLookup[self._id] = self;
        for (var key in self._aria) {
          self.aria(key, self._aria[key]);
        }
        if (self.state.get('visible') === false) {
          self.getEl().style.display = 'none';
        }
        self.bindStates();
        self.state.on('change:visible', function (e) {
          var state = e.value;
          var parentCtrl;
          if (self.state.get('rendered')) {
            self.getEl().style.display = state === false ? 'none' : '';
            self.getEl().getBoundingClientRect();
          }
          parentCtrl = self.parent();
          if (parentCtrl) {
            parentCtrl._lastRect = null;
          }
          self.fire(state ? 'show' : 'hide');
          ReflowQueue.add(self);
        });
        self.fire('postrender', {}, false);
      },
      bindStates: function () {
      },
      scrollIntoView: function (align) {
        function getOffset(elm, rootElm) {
          var x, y, parent = elm;
          x = y = 0;
          while (parent && parent !== rootElm && parent.nodeType) {
            x += parent.offsetLeft || 0;
            y += parent.offsetTop || 0;
            parent = parent.offsetParent;
          }
          return {
            x: x,
            y: y
          };
        }
        var elm = this.getEl(), parentElm = elm.parentNode;
        var x, y, width, height, parentWidth, parentHeight;
        var pos = getOffset(elm, parentElm);
        x = pos.x;
        y = pos.y;
        width = elm.offsetWidth;
        height = elm.offsetHeight;
        parentWidth = parentElm.clientWidth;
        parentHeight = parentElm.clientHeight;
        if (align === 'end') {
          x -= parentWidth - width;
          y -= parentHeight - height;
        } else if (align === 'center') {
          x -= parentWidth / 2 - width / 2;
          y -= parentHeight / 2 - height / 2;
        }
        parentElm.scrollLeft = x;
        parentElm.scrollTop = y;
        return this;
      },
      getRoot: function () {
        var ctrl = this, rootControl;
        var parents = [];
        while (ctrl) {
          if (ctrl.rootControl) {
            rootControl = ctrl.rootControl;
            break;
          }
          parents.push(ctrl);
          rootControl = ctrl;
          ctrl = ctrl.parent();
        }
        if (!rootControl) {
          rootControl = this;
        }
        var i = parents.length;
        while (i--) {
          parents[i].rootControl = rootControl;
        }
        return rootControl;
      },
      reflow: function () {
        ReflowQueue.remove(this);
        var parent = this.parent();
        if (parent && parent._layout && !parent._layout.isNative()) {
          parent.reflow();
        }
        return this;
      }
    };
    global$2.each('text title visible disabled active value'.split(' '), function (name) {
      proto$1[name] = function (value) {
        if (arguments.length === 0) {
          return this.state.get(name);
        }
        if (typeof value !== 'undefined') {
          this.state.set(name, value);
        }
        return this;
      };
    });
    Control = global$a.extend(proto$1);
    function getEventDispatcher(obj) {
      if (!obj._eventDispatcher) {
        obj._eventDispatcher = new global$b({
          scope: obj,
          toggleEvent: function (name, state) {
            if (state && global$b.isNative(name)) {
              if (!obj._nativeEvents) {
                obj._nativeEvents = {};
              }
              obj._nativeEvents[name] = true;
              if (obj.state.get('rendered')) {
                bindPendingEvents(obj);
              }
            }
          }
        });
      }
      return obj._eventDispatcher;
    }
    function bindPendingEvents(eventCtrl) {
      var i, l, parents, eventRootCtrl, nativeEvents, name;
      function delegate(e) {
        var control = eventCtrl.getParentCtrl(e.target);
        if (control) {
          control.fire(e.type, e);
        }
      }
      function mouseLeaveHandler() {
        var ctrl = eventRootCtrl._lastHoverCtrl;
        if (ctrl) {
          ctrl.fire('mouseleave', { target: ctrl.getEl() });
          ctrl.parents().each(function (ctrl) {
            ctrl.fire('mouseleave', { target: ctrl.getEl() });
          });
          eventRootCtrl._lastHoverCtrl = null;
        }
      }
      function mouseEnterHandler(e) {
        var ctrl = eventCtrl.getParentCtrl(e.target), lastCtrl = eventRootCtrl._lastHoverCtrl, idx = 0, i, parents, lastParents;
        if (ctrl !== lastCtrl) {
          eventRootCtrl._lastHoverCtrl = ctrl;
          parents = ctrl.parents().toArray().reverse();
          parents.push(ctrl);
          if (lastCtrl) {
            lastParents = lastCtrl.parents().toArray().reverse();
            lastParents.push(lastCtrl);
            for (idx = 0; idx < lastParents.length; idx++) {
              if (parents[idx] !== lastParents[idx]) {
                break;
              }
            }
            for (i = lastParents.length - 1; i >= idx; i--) {
              lastCtrl = lastParents[i];
              lastCtrl.fire('mouseleave', { target: lastCtrl.getEl() });
            }
          }
          for (i = idx; i < parents.length; i++) {
            ctrl = parents[i];
            ctrl.fire('mouseenter', { target: ctrl.getEl() });
          }
        }
      }
      function fixWheelEvent(e) {
        e.preventDefault();
        if (e.type === 'mousewheel') {
          e.deltaY = -1 / 40 * e.wheelDelta;
          if (e.wheelDeltaX) {
            e.deltaX = -1 / 40 * e.wheelDeltaX;
          }
        } else {
          e.deltaX = 0;
          e.deltaY = e.detail;
        }
        e = eventCtrl.fire('wheel', e);
      }
      nativeEvents = eventCtrl._nativeEvents;
      if (nativeEvents) {
        parents = eventCtrl.parents().toArray();
        parents.unshift(eventCtrl);
        for (i = 0, l = parents.length; !eventRootCtrl && i < l; i++) {
          eventRootCtrl = parents[i]._eventsRoot;
        }
        if (!eventRootCtrl) {
          eventRootCtrl = parents[parents.length - 1] || eventCtrl;
        }
        eventCtrl._eventsRoot = eventRootCtrl;
        for (l = i, i = 0; i < l; i++) {
          parents[i]._eventsRoot = eventRootCtrl;
        }
        var eventRootDelegates = eventRootCtrl._delegates;
        if (!eventRootDelegates) {
          eventRootDelegates = eventRootCtrl._delegates = {};
        }
        for (name in nativeEvents) {
          if (!nativeEvents) {
            return false;
          }
          if (name === 'wheel' && !hasWheelEventSupport) {
            if (hasMouseWheelEventSupport) {
              global$9(eventCtrl.getEl()).on('mousewheel', fixWheelEvent);
            } else {
              global$9(eventCtrl.getEl()).on('DOMMouseScroll', fixWheelEvent);
            }
            continue;
          }
          if (name === 'mouseenter' || name === 'mouseleave') {
            if (!eventRootCtrl._hasMouseEnter) {
              global$9(eventRootCtrl.getEl()).on('mouseleave', mouseLeaveHandler).on('mouseover', mouseEnterHandler);
              eventRootCtrl._hasMouseEnter = 1;
            }
          } else if (!eventRootDelegates[name]) {
            global$9(eventRootCtrl.getEl()).on(name, delegate);
            eventRootDelegates[name] = true;
          }
          nativeEvents[name] = false;
        }
      }
    }
    var Control$1 = Control;

    var hasTabstopData = function (elm) {
      return elm.getAttribute('data-mce-tabstop') ? true : false;
    };
    function KeyboardNavigation (settings) {
      var root = settings.root;
      var focusedElement, focusedControl;
      function isElement(node) {
        return node && node.nodeType === 1;
      }
      try {
        focusedElement = domGlobals.document.activeElement;
      } catch (ex) {
        focusedElement = domGlobals.document.body;
      }
      focusedControl = root.getParentCtrl(focusedElement);
      function getRole(elm) {
        elm = elm || focusedElement;
        if (isElement(elm)) {
          return elm.getAttribute('role');
        }
        return null;
      }
      function getParentRole(elm) {
        var role, parent = elm || focusedElement;
        while (parent = parent.parentNode) {
          if (role = getRole(parent)) {
            return role;
          }
        }
      }
      function getAriaProp(name) {
        var elm = focusedElement;
        if (isElement(elm)) {
          return elm.getAttribute('aria-' + name);
        }
      }
      function isTextInputElement(elm) {
        var tagName = elm.tagName.toUpperCase();
        return tagName === 'INPUT' || tagName === 'TEXTAREA' || tagName === 'SELECT';
      }
      function canFocus(elm) {
        if (isTextInputElement(elm) && !elm.hidden) {
          return true;
        }
        if (hasTabstopData(elm)) {
          return true;
        }
        if (/^(button|menuitem|checkbox|tab|menuitemcheckbox|option|gridcell|slider)$/.test(getRole(elm))) {
          return true;
        }
        return false;
      }
      function getFocusElements(elm) {
        var elements = [];
        function collect(elm) {
          if (elm.nodeType !== 1 || elm.style.display === 'none' || elm.disabled) {
            return;
          }
          if (canFocus(elm)) {
            elements.push(elm);
          }
          for (var i = 0; i < elm.childNodes.length; i++) {
            collect(elm.childNodes[i]);
          }
        }
        collect(elm || root.getEl());
        return elements;
      }
      function getNavigationRoot(targetControl) {
        var navigationRoot, controls;
        targetControl = targetControl || focusedControl;
        controls = targetControl.parents().toArray();
        controls.unshift(targetControl);
        for (var i = 0; i < controls.length; i++) {
          navigationRoot = controls[i];
          if (navigationRoot.settings.ariaRoot) {
            break;
          }
        }
        return navigationRoot;
      }
      function focusFirst(targetControl) {
        var navigationRoot = getNavigationRoot(targetControl);
        var focusElements = getFocusElements(navigationRoot.getEl());
        if (navigationRoot.settings.ariaRemember && 'lastAriaIndex' in navigationRoot) {
          moveFocusToIndex(navigationRoot.lastAriaIndex, focusElements);
        } else {
          moveFocusToIndex(0, focusElements);
        }
      }
      function moveFocusToIndex(idx, elements) {
        if (idx < 0) {
          idx = elements.length - 1;
        } else if (idx >= elements.length) {
          idx = 0;
        }
        if (elements[idx]) {
          elements[idx].focus();
        }
        return idx;
      }
      function moveFocus(dir, elements) {
        var idx = -1;
        var navigationRoot = getNavigationRoot();
        elements = elements || getFocusElements(navigationRoot.getEl());
        for (var i = 0; i < elements.length; i++) {
          if (elements[i] === focusedElement) {
            idx = i;
          }
        }
        idx += dir;
        navigationRoot.lastAriaIndex = moveFocusToIndex(idx, elements);
      }
      function left() {
        var parentRole = getParentRole();
        if (parentRole === 'tablist') {
          moveFocus(-1, getFocusElements(focusedElement.parentNode));
        } else if (focusedControl.parent().submenu) {
          cancel();
        } else {
          moveFocus(-1);
        }
      }
      function right() {
        var role = getRole(), parentRole = getParentRole();
        if (parentRole === 'tablist') {
          moveFocus(1, getFocusElements(focusedElement.parentNode));
        } else if (role === 'menuitem' && parentRole === 'menu' && getAriaProp('haspopup')) {
          enter();
        } else {
          moveFocus(1);
        }
      }
      function up() {
        moveFocus(-1);
      }
      function down() {
        var role = getRole(), parentRole = getParentRole();
        if (role === 'menuitem' && parentRole === 'menubar') {
          enter();
        } else if (role === 'button' && getAriaProp('haspopup')) {
          enter({ key: 'down' });
        } else {
          moveFocus(1);
        }
      }
      function tab(e) {
        var parentRole = getParentRole();
        if (parentRole === 'tablist') {
          var elm = getFocusElements(focusedControl.getEl('body'))[0];
          if (elm) {
            elm.focus();
          }
        } else {
          moveFocus(e.shiftKey ? -1 : 1);
        }
      }
      function cancel() {
        focusedControl.fire('cancel');
      }
      function enter(aria) {
        aria = aria || {};
        focusedControl.fire('click', {
          target: focusedElement,
          aria: aria
        });
      }
      root.on('keydown', function (e) {
        function handleNonTabOrEscEvent(e, handler) {
          if (isTextInputElement(focusedElement) || hasTabstopData(focusedElement)) {
            return;
          }
          if (getRole(focusedElement) === 'slider') {
            return;
          }
          if (handler(e) !== false) {
            e.preventDefault();
          }
        }
        if (e.isDefaultPrevented()) {
          return;
        }
        switch (e.keyCode) {
        case 37:
          handleNonTabOrEscEvent(e, left);
          break;
        case 39:
          handleNonTabOrEscEvent(e, right);
          break;
        case 38:
          handleNonTabOrEscEvent(e, up);
          break;
        case 40:
          handleNonTabOrEscEvent(e, down);
          break;
        case 27:
          cancel();
          break;
        case 14:
        case 13:
        case 32:
          handleNonTabOrEscEvent(e, enter);
          break;
        case 9:
          tab(e);
          e.preventDefault();
          break;
        }
      });
      root.on('focusin', function (e) {
        focusedElement = e.target;
        focusedControl = e.control;
      });
      return { focusFirst: focusFirst };
    }

    var selectorCache = {};
    var Container = Control$1.extend({
      init: function (settings) {
        var self = this;
        self._super(settings);
        settings = self.settings;
        if (settings.fixed) {
          self.state.set('fixed', true);
        }
        self._items = new Collection$2();
        if (self.isRtl()) {
          self.classes.add('rtl');
        }
        self.bodyClasses = new ClassList(function () {
          if (self.state.get('rendered')) {
            self.getEl('body').className = this.toString();
          }
        });
        self.bodyClasses.prefix = self.classPrefix;
        self.classes.add('container');
        self.bodyClasses.add('container-body');
        if (settings.containerCls) {
          self.classes.add(settings.containerCls);
        }
        self._layout = global$4.create((settings.layout || '') + 'layout');
        if (self.settings.items) {
          self.add(self.settings.items);
        } else {
          self.add(self.render());
        }
        self._hasBody = true;
      },
      items: function () {
        return this._items;
      },
      find: function (selector) {
        selector = selectorCache[selector] = selectorCache[selector] || new Selector(selector);
        return selector.find(this);
      },
      add: function (items) {
        var self = this;
        self.items().add(self.create(items)).parent(self);
        return self;
      },
      focus: function (keyboard) {
        var self = this;
        var focusCtrl, keyboardNav, items;
        if (keyboard) {
          keyboardNav = self.keyboardNav || self.parents().eq(-1)[0].keyboardNav;
          if (keyboardNav) {
            keyboardNav.focusFirst(self);
            return;
          }
        }
        items = self.find('*');
        if (self.statusbar) {
          items.add(self.statusbar.items());
        }
        items.each(function (ctrl) {
          if (ctrl.settings.autofocus) {
            focusCtrl = null;
            return false;
          }
          if (ctrl.canFocus) {
            focusCtrl = focusCtrl || ctrl;
          }
        });
        if (focusCtrl) {
          focusCtrl.focus();
        }
        return self;
      },
      replace: function (oldItem, newItem) {
        var ctrlElm;
        var items = this.items();
        var i = items.length;
        while (i--) {
          if (items[i] === oldItem) {
            items[i] = newItem;
            break;
          }
        }
        if (i >= 0) {
          ctrlElm = newItem.getEl();
          if (ctrlElm) {
            ctrlElm.parentNode.removeChild(ctrlElm);
          }
          ctrlElm = oldItem.getEl();
          if (ctrlElm) {
            ctrlElm.parentNode.removeChild(ctrlElm);
          }
        }
        newItem.parent(this);
      },
      create: function (items) {
        var self = this;
        var settings;
        var ctrlItems = [];
        if (!global$2.isArray(items)) {
          items = [items];
        }
        global$2.each(items, function (item) {
          if (item) {
            if (!(item instanceof Control$1)) {
              if (typeof item === 'string') {
                item = { type: item };
              }
              settings = global$2.extend({}, self.settings.defaults, item);
              item.type = settings.type = settings.type || item.type || self.settings.defaultType || (settings.defaults ? settings.defaults.type : null);
              item = global$4.create(settings);
            }
            ctrlItems.push(item);
          }
        });
        return ctrlItems;
      },
      renderNew: function () {
        var self = this;
        self.items().each(function (ctrl, index) {
          var containerElm;
          ctrl.parent(self);
          if (!ctrl.state.get('rendered')) {
            containerElm = self.getEl('body');
            if (containerElm.hasChildNodes() && index <= containerElm.childNodes.length - 1) {
              global$9(containerElm.childNodes[index]).before(ctrl.renderHtml());
            } else {
              global$9(containerElm).append(ctrl.renderHtml());
            }
            ctrl.postRender();
            ReflowQueue.add(ctrl);
          }
        });
        self._layout.applyClasses(self.items().filter(':visible'));
        self._lastRect = null;
        return self;
      },
      append: function (items) {
        return this.add(items).renderNew();
      },
      prepend: function (items) {
        var self = this;
        self.items().set(self.create(items).concat(self.items().toArray()));
        return self.renderNew();
      },
      insert: function (items, index, before) {
        var self = this;
        var curItems, beforeItems, afterItems;
        items = self.create(items);
        curItems = self.items();
        if (!before && index < curItems.length - 1) {
          index += 1;
        }
        if (index >= 0 && index < curItems.length) {
          beforeItems = curItems.slice(0, index).toArray();
          afterItems = curItems.slice(index).toArray();
          curItems.set(beforeItems.concat(items, afterItems));
        }
        return self.renderNew();
      },
      fromJSON: function (data) {
        var self = this;
        for (var name in data) {
          self.find('#' + name).value(data[name]);
        }
        return self;
      },
      toJSON: function () {
        var self = this, data = {};
        self.find('*').each(function (ctrl) {
          var name = ctrl.name(), value = ctrl.value();
          if (name && typeof value !== 'undefined') {
            data[name] = value;
          }
        });
        return data;
      },
      renderHtml: function () {
        var self = this, layout = self._layout, role = this.settings.role;
        self.preRender();
        layout.preRender(self);
        return '<div id="' + self._id + '" class="' + self.classes + '"' + (role ? ' role="' + this.settings.role + '"' : '') + '>' + '<div id="' + self._id + '-body" class="' + self.bodyClasses + '">' + (self.settings.html || '') + layout.renderHtml(self) + '</div>' + '</div>';
      },
      postRender: function () {
        var self = this;
        var box;
        self.items().exec('postRender');
        self._super();
        self._layout.postRender(self);
        self.state.set('rendered', true);
        if (self.settings.style) {
          self.$el.css(self.settings.style);
        }
        if (self.settings.border) {
          box = self.borderBox;
          self.$el.css({
            'border-top-width': box.top,
            'border-right-width': box.right,
            'border-bottom-width': box.bottom,
            'border-left-width': box.left
          });
        }
        if (!self.parent()) {
          self.keyboardNav = KeyboardNavigation({ root: self });
        }
        return self;
      },
      initLayoutRect: function () {
        var self = this, layoutRect = self._super();
        self._layout.recalc(self);
        return layoutRect;
      },
      recalc: function () {
        var self = this;
        var rect = self._layoutRect;
        var lastRect = self._lastRect;
        if (!lastRect || lastRect.w !== rect.w || lastRect.h !== rect.h) {
          self._layout.recalc(self);
          rect = self.layoutRect();
          self._lastRect = {
            x: rect.x,
            y: rect.y,
            w: rect.w,
            h: rect.h
          };
          return true;
        }
      },
      reflow: function () {
        var i;
        ReflowQueue.remove(this);
        if (this.visible()) {
          Control$1.repaintControls = [];
          Control$1.repaintControls.map = {};
          this.recalc();
          i = Control$1.repaintControls.length;
          while (i--) {
            Control$1.repaintControls[i].repaint();
          }
          if (this.settings.layout !== 'flow' && this.settings.layout !== 'stack') {
            this.repaint();
          }
          Control$1.repaintControls = [];
        }
        return this;
      }
    });

    function getDocumentSize(doc) {
      var documentElement, body, scrollWidth, clientWidth;
      var offsetWidth, scrollHeight, clientHeight, offsetHeight;
      var max = Math.max;
      documentElement = doc.documentElement;
      body = doc.body;
      scrollWidth = max(documentElement.scrollWidth, body.scrollWidth);
      clientWidth = max(documentElement.clientWidth, body.clientWidth);
      offsetWidth = max(documentElement.offsetWidth, body.offsetWidth);
      scrollHeight = max(documentElement.scrollHeight, body.scrollHeight);
      clientHeight = max(documentElement.clientHeight, body.clientHeight);
      offsetHeight = max(documentElement.offsetHeight, body.offsetHeight);
      return {
        width: scrollWidth < offsetWidth ? clientWidth : scrollWidth,
        height: scrollHeight < offsetHeight ? clientHeight : scrollHeight
      };
    }
    function updateWithTouchData(e) {
      var keys, i;
      if (e.changedTouches) {
        keys = 'screenX screenY pageX pageY clientX clientY'.split(' ');
        for (i = 0; i < keys.length; i++) {
          e[keys[i]] = e.changedTouches[0][keys[i]];
        }
      }
    }
    function DragHelper (id, settings) {
      var $eventOverlay;
      var doc = settings.document || domGlobals.document;
      var downButton;
      var start, stop, drag, startX, startY;
      settings = settings || {};
      var handleElement = doc.getElementById(settings.handle || id);
      start = function (e) {
        var docSize = getDocumentSize(doc);
        var handleElm, cursor;
        updateWithTouchData(e);
        e.preventDefault();
        downButton = e.button;
        handleElm = handleElement;
        startX = e.screenX;
        startY = e.screenY;
        if (domGlobals.window.getComputedStyle) {
          cursor = domGlobals.window.getComputedStyle(handleElm, null).getPropertyValue('cursor');
        } else {
          cursor = handleElm.runtimeStyle.cursor;
        }
        $eventOverlay = global$9('<div></div>').css({
          position: 'absolute',
          top: 0,
          left: 0,
          width: docSize.width,
          height: docSize.height,
          zIndex: 2147483647,
          opacity: 0.0001,
          cursor: cursor
        }).appendTo(doc.body);
        global$9(doc).on('mousemove touchmove', drag).on('mouseup touchend', stop);
        settings.start(e);
      };
      drag = function (e) {
        updateWithTouchData(e);
        if (e.button !== downButton) {
          return stop(e);
        }
        e.deltaX = e.screenX - startX;
        e.deltaY = e.screenY - startY;
        e.preventDefault();
        settings.drag(e);
      };
      stop = function (e) {
        updateWithTouchData(e);
        global$9(doc).off('mousemove touchmove', drag).off('mouseup touchend', stop);
        $eventOverlay.remove();
        if (settings.stop) {
          settings.stop(e);
        }
      };
      this.destroy = function () {
        global$9(handleElement).off();
      };
      global$9(handleElement).on('mousedown touchstart', start);
    }

    var Scrollable = {
      init: function () {
        var self = this;
        self.on('repaint', self.renderScroll);
      },
      renderScroll: function () {
        var self = this, margin = 2;
        function repaintScroll() {
          var hasScrollH, hasScrollV, bodyElm;
          function repaintAxis(axisName, posName, sizeName, contentSizeName, hasScroll, ax) {
            var containerElm, scrollBarElm, scrollThumbElm;
            var containerSize, scrollSize, ratio, rect;
            var posNameLower, sizeNameLower;
            scrollBarElm = self.getEl('scroll' + axisName);
            if (scrollBarElm) {
              posNameLower = posName.toLowerCase();
              sizeNameLower = sizeName.toLowerCase();
              global$9(self.getEl('absend')).css(posNameLower, self.layoutRect()[contentSizeName] - 1);
              if (!hasScroll) {
                global$9(scrollBarElm).css('display', 'none');
                return;
              }
              global$9(scrollBarElm).css('display', 'block');
              containerElm = self.getEl('body');
              scrollThumbElm = self.getEl('scroll' + axisName + 't');
              containerSize = containerElm['client' + sizeName] - margin * 2;
              containerSize -= hasScrollH && hasScrollV ? scrollBarElm['client' + ax] : 0;
              scrollSize = containerElm['scroll' + sizeName];
              ratio = containerSize / scrollSize;
              rect = {};
              rect[posNameLower] = containerElm['offset' + posName] + margin;
              rect[sizeNameLower] = containerSize;
              global$9(scrollBarElm).css(rect);
              rect = {};
              rect[posNameLower] = containerElm['scroll' + posName] * ratio;
              rect[sizeNameLower] = containerSize * ratio;
              global$9(scrollThumbElm).css(rect);
            }
          }
          bodyElm = self.getEl('body');
          hasScrollH = bodyElm.scrollWidth > bodyElm.clientWidth;
          hasScrollV = bodyElm.scrollHeight > bodyElm.clientHeight;
          repaintAxis('h', 'Left', 'Width', 'contentW', hasScrollH, 'Height');
          repaintAxis('v', 'Top', 'Height', 'contentH', hasScrollV, 'Width');
        }
        function addScroll() {
          function addScrollAxis(axisName, posName, sizeName, deltaPosName, ax) {
            var scrollStart;
            var axisId = self._id + '-scroll' + axisName, prefix = self.classPrefix;
            global$9(self.getEl()).append('<div id="' + axisId + '" class="' + prefix + 'scrollbar ' + prefix + 'scrollbar-' + axisName + '">' + '<div id="' + axisId + 't" class="' + prefix + 'scrollbar-thumb"></div>' + '</div>');
            self.draghelper = new DragHelper(axisId + 't', {
              start: function () {
                scrollStart = self.getEl('body')['scroll' + posName];
                global$9('#' + axisId).addClass(prefix + 'active');
              },
              drag: function (e) {
                var ratio, hasScrollH, hasScrollV, containerSize;
                var layoutRect = self.layoutRect();
                hasScrollH = layoutRect.contentW > layoutRect.innerW;
                hasScrollV = layoutRect.contentH > layoutRect.innerH;
                containerSize = self.getEl('body')['client' + sizeName] - margin * 2;
                containerSize -= hasScrollH && hasScrollV ? self.getEl('scroll' + axisName)['client' + ax] : 0;
                ratio = containerSize / self.getEl('body')['scroll' + sizeName];
                self.getEl('body')['scroll' + posName] = scrollStart + e['delta' + deltaPosName] / ratio;
              },
              stop: function () {
                global$9('#' + axisId).removeClass(prefix + 'active');
              }
            });
          }
          self.classes.add('scroll');
          addScrollAxis('v', 'Top', 'Height', 'Y', 'Width');
          addScrollAxis('h', 'Left', 'Width', 'X', 'Height');
        }
        if (self.settings.autoScroll) {
          if (!self._hasScroll) {
            self._hasScroll = true;
            addScroll();
            self.on('wheel', function (e) {
              var bodyEl = self.getEl('body');
              bodyEl.scrollLeft += (e.deltaX || 0) * 10;
              bodyEl.scrollTop += e.deltaY * 10;
              repaintScroll();
            });
            global$9(self.getEl('body')).on('scroll', repaintScroll);
          }
          repaintScroll();
        }
      }
    };

    var Panel = Container.extend({
      Defaults: {
        layout: 'fit',
        containerCls: 'panel'
      },
      Mixins: [Scrollable],
      renderHtml: function () {
        var self = this;
        var layout = self._layout;
        var innerHtml = self.settings.html;
        self.preRender();
        layout.preRender(self);
        if (typeof innerHtml === 'undefined') {
          innerHtml = '<div id="' + self._id + '-body" class="' + self.bodyClasses + '">' + layout.renderHtml(self) + '</div>';
        } else {
          if (typeof innerHtml === 'function') {
            innerHtml = innerHtml.call(self);
          }
          self._hasBody = false;
        }
        return '<div id="' + self._id + '" class="' + self.classes + '" hidefocus="1" tabindex="-1" role="group">' + (self._preBodyHtml || '') + innerHtml + '</div>';
      }
    });

    var Resizable = {
      resizeToContent: function () {
        this._layoutRect.autoResize = true;
        this._lastRect = null;
        this.reflow();
      },
      resizeTo: function (w, h) {
        if (w <= 1 || h <= 1) {
          var rect = funcs.getWindowSize();
          w = w <= 1 ? w * rect.w : w;
          h = h <= 1 ? h * rect.h : h;
        }
        this._layoutRect.autoResize = false;
        return this.layoutRect({
          minW: w,
          minH: h,
          w: w,
          h: h
        }).reflow();
      },
      resizeBy: function (dw, dh) {
        var self = this, rect = self.layoutRect();
        return self.resizeTo(rect.w + dw, rect.h + dh);
      }
    };

    var documentClickHandler, documentScrollHandler, windowResizeHandler;
    var visiblePanels = [];
    var zOrder = [];
    var hasModal;
    function isChildOf(ctrl, parent) {
      while (ctrl) {
        if (ctrl === parent) {
          return true;
        }
        ctrl = ctrl.parent();
      }
    }
    function skipOrHidePanels(e) {
      var i = visiblePanels.length;
      while (i--) {
        var panel = visiblePanels[i], clickCtrl = panel.getParentCtrl(e.target);
        if (panel.settings.autohide) {
          if (clickCtrl) {
            if (isChildOf(clickCtrl, panel) || panel.parent() === clickCtrl) {
              continue;
            }
          }
          e = panel.fire('autohide', { target: e.target });
          if (!e.isDefaultPrevented()) {
            panel.hide();
          }
        }
      }
    }
    function bindDocumentClickHandler() {
      if (!documentClickHandler) {
        documentClickHandler = function (e) {
          if (e.button === 2) {
            return;
          }
          skipOrHidePanels(e);
        };
        global$9(domGlobals.document).on('click touchstart', documentClickHandler);
      }
    }
    function bindDocumentScrollHandler() {
      if (!documentScrollHandler) {
        documentScrollHandler = function () {
          var i;
          i = visiblePanels.length;
          while (i--) {
            repositionPanel(visiblePanels[i]);
          }
        };
        global$9(domGlobals.window).on('scroll', documentScrollHandler);
      }
    }
    function bindWindowResizeHandler() {
      if (!windowResizeHandler) {
        var docElm_1 = domGlobals.document.documentElement;
        var clientWidth_1 = docElm_1.clientWidth, clientHeight_1 = docElm_1.clientHeight;
        windowResizeHandler = function () {
          if (!domGlobals.document.all || clientWidth_1 !== docElm_1.clientWidth || clientHeight_1 !== docElm_1.clientHeight) {
            clientWidth_1 = docElm_1.clientWidth;
            clientHeight_1 = docElm_1.clientHeight;
            FloatPanel.hideAll();
          }
        };
        global$9(domGlobals.window).on('resize', windowResizeHandler);
      }
    }
    function repositionPanel(panel) {
      var scrollY = funcs.getViewPort().y;
      function toggleFixedChildPanels(fixed, deltaY) {
        var parent;
        for (var i = 0; i < visiblePanels.length; i++) {
          if (visiblePanels[i] !== panel) {
            parent = visiblePanels[i].parent();
            while (parent && (parent = parent.parent())) {
              if (parent === panel) {
                visiblePanels[i].fixed(fixed).moveBy(0, deltaY).repaint();
              }
            }
          }
        }
      }
      if (panel.settings.autofix) {
        if (!panel.state.get('fixed')) {
          panel._autoFixY = panel.layoutRect().y;
          if (panel._autoFixY < scrollY) {
            panel.fixed(true).layoutRect({ y: 0 }).repaint();
            toggleFixedChildPanels(true, scrollY - panel._autoFixY);
          }
        } else {
          if (panel._autoFixY > scrollY) {
            panel.fixed(false).layoutRect({ y: panel._autoFixY }).repaint();
            toggleFixedChildPanels(false, panel._autoFixY - scrollY);
          }
        }
      }
    }
    function addRemove(add, ctrl) {
      var i, zIndex = FloatPanel.zIndex || 65535, topModal;
      if (add) {
        zOrder.push(ctrl);
      } else {
        i = zOrder.length;
        while (i--) {
          if (zOrder[i] === ctrl) {
            zOrder.splice(i, 1);
          }
        }
      }
      if (zOrder.length) {
        for (i = 0; i < zOrder.length; i++) {
          if (zOrder[i].modal) {
            zIndex++;
            topModal = zOrder[i];
          }
          zOrder[i].getEl().style.zIndex = zIndex;
          zOrder[i].zIndex = zIndex;
          zIndex++;
        }
      }
      var modalBlockEl = global$9('#' + ctrl.classPrefix + 'modal-block', ctrl.getContainerElm())[0];
      if (topModal) {
        global$9(modalBlockEl).css('z-index', topModal.zIndex - 1);
      } else if (modalBlockEl) {
        modalBlockEl.parentNode.removeChild(modalBlockEl);
        hasModal = false;
      }
      FloatPanel.currentZIndex = zIndex;
    }
    var FloatPanel = Panel.extend({
      Mixins: [
        Movable,
        Resizable
      ],
      init: function (settings) {
        var self = this;
        self._super(settings);
        self._eventsRoot = self;
        self.classes.add('floatpanel');
        if (settings.autohide) {
          bindDocumentClickHandler();
          bindWindowResizeHandler();
          visiblePanels.push(self);
        }
        if (settings.autofix) {
          bindDocumentScrollHandler();
          self.on('move', function () {
            repositionPanel(this);
          });
        }
        self.on('postrender show', function (e) {
          if (e.control === self) {
            var $modalBlockEl_1;
            var prefix_1 = self.classPrefix;
            if (self.modal && !hasModal) {
              $modalBlockEl_1 = global$9('#' + prefix_1 + 'modal-block', self.getContainerElm());
              if (!$modalBlockEl_1[0]) {
                $modalBlockEl_1 = global$9('<div id="' + prefix_1 + 'modal-block" class="' + prefix_1 + 'reset ' + prefix_1 + 'fade"></div>').appendTo(self.getContainerElm());
              }
              global$7.setTimeout(function () {
                $modalBlockEl_1.addClass(prefix_1 + 'in');
                global$9(self.getEl()).addClass(prefix_1 + 'in');
              });
              hasModal = true;
            }
            addRemove(true, self);
          }
        });
        self.on('show', function () {
          self.parents().each(function (ctrl) {
            if (ctrl.state.get('fixed')) {
              self.fixed(true);
              return false;
            }
          });
        });
        if (settings.popover) {
          self._preBodyHtml = '<div class="' + self.classPrefix + 'arrow"></div>';
          self.classes.add('popover').add('bottom').add(self.isRtl() ? 'end' : 'start');
        }
        self.aria('label', settings.ariaLabel);
        self.aria('labelledby', self._id);
        self.aria('describedby', self.describedBy || self._id + '-none');
      },
      fixed: function (state) {
        var self = this;
        if (self.state.get('fixed') !== state) {
          if (self.state.get('rendered')) {
            var viewport = funcs.getViewPort();
            if (state) {
              self.layoutRect().y -= viewport.y;
            } else {
              self.layoutRect().y += viewport.y;
            }
          }
          self.classes.toggle('fixed', state);
          self.state.set('fixed', state);
        }
        return self;
      },
      show: function () {
        var self = this;
        var i;
        var state = self._super();
        i = visiblePanels.length;
        while (i--) {
          if (visiblePanels[i] === self) {
            break;
          }
        }
        if (i === -1) {
          visiblePanels.push(self);
        }
        return state;
      },
      hide: function () {
        removeVisiblePanel(this);
        addRemove(false, this);
        return this._super();
      },
      hideAll: function () {
        FloatPanel.hideAll();
      },
      close: function () {
        var self = this;
        if (!self.fire('close').isDefaultPrevented()) {
          self.remove();
          addRemove(false, self);
        }
        return self;
      },
      remove: function () {
        removeVisiblePanel(this);
        this._super();
      },
      postRender: function () {
        var self = this;
        if (self.settings.bodyRole) {
          this.getEl('body').setAttribute('role', self.settings.bodyRole);
        }
        return self._super();
      }
    });
    FloatPanel.hideAll = function () {
      var i = visiblePanels.length;
      while (i--) {
        var panel = visiblePanels[i];
        if (panel && panel.settings.autohide) {
          panel.hide();
          visiblePanels.splice(i, 1);
        }
      }
    };
    function removeVisiblePanel(panel) {
      var i;
      i = visiblePanels.length;
      while (i--) {
        if (visiblePanels[i] === panel) {
          visiblePanels.splice(i, 1);
        }
      }
      i = zOrder.length;
      while (i--) {
        if (zOrder[i] === panel) {
          zOrder.splice(i, 1);
        }
      }
    }

    var isFixed$1 = function (inlineToolbarContainer, editor) {
      return !!(inlineToolbarContainer && !editor.settings.ui_container);
    };
    var render$1 = function (editor, theme, args) {
      var panel, inlineToolbarContainer;
      var DOM = global$3.DOM;
      var fixedToolbarContainer = getFixedToolbarContainer(editor);
      if (fixedToolbarContainer) {
        inlineToolbarContainer = DOM.select(fixedToolbarContainer)[0];
      }
      var reposition = function () {
        if (panel && panel.moveRel && panel.visible() && !panel._fixed) {
          var scrollContainer = editor.selection.getScrollContainer(), body = editor.getBody();
          var deltaX = 0, deltaY = 0;
          if (scrollContainer) {
            var bodyPos = DOM.getPos(body), scrollContainerPos = DOM.getPos(scrollContainer);
            deltaX = Math.max(0, scrollContainerPos.x - bodyPos.x);
            deltaY = Math.max(0, scrollContainerPos.y - bodyPos.y);
          }
          panel.fixed(false).moveRel(body, editor.rtl ? [
            'tr-br',
            'br-tr'
          ] : [
            'tl-bl',
            'bl-tl',
            'tr-br'
          ]).moveBy(deltaX, deltaY);
        }
      };
      var show = function () {
        if (panel) {
          panel.show();
          reposition();
          DOM.addClass(editor.getBody(), 'mce-edit-focus');
        }
      };
      var hide = function () {
        if (panel) {
          panel.hide();
          FloatPanel.hideAll();
          DOM.removeClass(editor.getBody(), 'mce-edit-focus');
        }
      };
      var render = function () {
        if (panel) {
          if (!panel.visible()) {
            show();
          }
          return;
        }
        panel = theme.panel = global$4.create({
          type: inlineToolbarContainer ? 'panel' : 'floatpanel',
          role: 'application',
          classes: 'tinymce tinymce-inline',
          layout: 'flex',
          direction: 'column',
          align: 'stretch',
          autohide: false,
          autofix: true,
          fixed: isFixed$1(inlineToolbarContainer, editor),
          border: 1,
          items: [
            hasMenubar(editor) === false ? null : {
              type: 'menubar',
              border: '0 0 1 0',
              items: Menubar.createMenuButtons(editor)
            },
            Toolbar.createToolbars(editor, getToolbarSize(editor))
          ]
        });
        UiContainer.setUiContainer(editor, panel);
        Events.fireBeforeRenderUI(editor);
        if (inlineToolbarContainer) {
          panel.renderTo(inlineToolbarContainer).reflow();
        } else {
          panel.renderTo().reflow();
        }
        A11y.addKeys(editor, panel);
        show();
        ContextToolbars.addContextualToolbars(editor);
        editor.on('nodeChange', reposition);
        editor.on('ResizeWindow', reposition);
        editor.on('activate', show);
        editor.on('deactivate', hide);
        editor.nodeChanged();
      };
      editor.settings.content_editable = true;
      editor.on('focus', function () {
        if (isSkinDisabled(editor) === false && args.skinUiCss) {
          DOM.styleSheetLoader.load(args.skinUiCss, render, render);
        } else {
          render();
        }
      });
      editor.on('blur hide', hide);
      editor.on('remove', function () {
        if (panel) {
          panel.remove();
          panel = null;
        }
      });
      if (isSkinDisabled(editor) === false && args.skinUiCss) {
        DOM.styleSheetLoader.load(args.skinUiCss, SkinLoaded.fireSkinLoaded(editor));
      } else {
        SkinLoaded.fireSkinLoaded(editor)();
      }
      return {};
    };
    var Inline = { render: render$1 };

    function Throbber (elm, inline) {
      var self = this;
      var state;
      var classPrefix = Control$1.classPrefix;
      var timer;
      self.show = function (time, callback) {
        function render() {
          if (state) {
            global$9(elm).append('<div class="' + classPrefix + 'throbber' + (inline ? ' ' + classPrefix + 'throbber-inline' : '') + '"></div>');
            if (callback) {
              callback();
            }
          }
        }
        self.hide();
        state = true;
        if (time) {
          timer = global$7.setTimeout(render, time);
        } else {
          render();
        }
        return self;
      };
      self.hide = function () {
        var child = elm.lastChild;
        global$7.clearTimeout(timer);
        if (child && child.className.indexOf('throbber') !== -1) {
          child.parentNode.removeChild(child);
        }
        state = false;
        return self;
      };
    }

    var setup = function (editor, theme) {
      var throbber;
      editor.on('ProgressState', function (e) {
        throbber = throbber || new Throbber(theme.panel.getEl('body'));
        if (e.state) {
          throbber.show(e.time);
        } else {
          throbber.hide();
        }
      });
    };
    var ProgressState = { setup: setup };

    var renderUI = function (editor, theme, args) {
      var skinUrl = getSkinUrl(editor);
      if (skinUrl) {
        args.skinUiCss = skinUrl + '/skin.min.css';
        editor.contentCSS.push(skinUrl + '/content' + (editor.inline ? '.inline' : '') + '.min.css');
      }
      ProgressState.setup(editor, theme);
      return isInline(editor) ? Inline.render(editor, theme, args) : Iframe.render(editor, theme, args);
    };
    var Render = { renderUI: renderUI };

    var Tooltip = Control$1.extend({
      Mixins: [Movable],
      Defaults: { classes: 'widget tooltip tooltip-n' },
      renderHtml: function () {
        var self = this, prefix = self.classPrefix;
        return '<div id="' + self._id + '" class="' + self.classes + '" role="presentation">' + '<div class="' + prefix + 'tooltip-arrow"></div>' + '<div class="' + prefix + 'tooltip-inner">' + self.encode(self.state.get('text')) + '</div>' + '</div>';
      },
      bindStates: function () {
        var self = this;
        self.state.on('change:text', function (e) {
          self.getEl().lastChild.innerHTML = self.encode(e.value);
        });
        return self._super();
      },
      repaint: function () {
        var self = this;
        var style, rect;
        style = self.getEl().style;
        rect = self._layoutRect;
        style.left = rect.x + 'px';
        style.top = rect.y + 'px';
        style.zIndex = 65535 + 65535;
      }
    });

    var Widget = Control$1.extend({
      init: function (settings) {
        var self = this;
        self._super(settings);
        settings = self.settings;
        self.canFocus = true;
        if (settings.tooltip && Widget.tooltips !== false) {
          self.on('mouseenter', function (e) {
            var tooltip = self.tooltip().moveTo(-65535);
            if (e.control === self) {
              var rel = tooltip.text(settings.tooltip).show().testMoveRel(self.getEl(), [
                'bc-tc',
                'bc-tl',
                'bc-tr'
              ]);
              tooltip.classes.toggle('tooltip-n', rel === 'bc-tc');
              tooltip.classes.toggle('tooltip-nw', rel === 'bc-tl');
              tooltip.classes.toggle('tooltip-ne', rel === 'bc-tr');
              tooltip.moveRel(self.getEl(), rel);
            } else {
              tooltip.hide();
            }
          });
          self.on('mouseleave mousedown click', function () {
            self.tooltip().remove();
            self._tooltip = null;
          });
        }
        self.aria('label', settings.ariaLabel || settings.tooltip);
      },
      tooltip: function () {
        if (!this._tooltip) {
          this._tooltip = new Tooltip({ type: 'tooltip' });
          UiContainer.inheritUiContainer(this, this._tooltip);
          this._tooltip.renderTo();
        }
        return this._tooltip;
      },
      postRender: function () {
        var self = this, settings = self.settings;
        self._super();
        if (!self.parent() && (settings.width || settings.height)) {
          self.initLayoutRect();
          self.repaint();
        }
        if (settings.autofocus) {
          self.focus();
        }
      },
      bindStates: function () {
        var self = this;
        function disable(state) {
          self.aria('disabled', state);
          self.classes.toggle('disabled', state);
        }
        function active(state) {
          self.aria('pressed', state);
          self.classes.toggle('active', state);
        }
        self.state.on('change:disabled', function (e) {
          disable(e.value);
        });
        self.state.on('change:active', function (e) {
          active(e.value);
        });
        if (self.state.get('disabled')) {
          disable(true);
        }
        if (self.state.get('active')) {
          active(true);
        }
        return self._super();
      },
      remove: function () {
        this._super();
        if (this._tooltip) {
          this._tooltip.remove();
          this._tooltip = null;
        }
      }
    });

    var Progress = Widget.extend({
      Defaults: { value: 0 },
      init: function (settings) {
        var self = this;
        self._super(settings);
        self.classes.add('progress');
        if (!self.settings.filter) {
          self.settings.filter = function (value) {
            return Math.round(value);
          };
        }
      },
      renderHtml: function () {
        var self = this, id = self._id, prefix = this.classPrefix;
        return '<div id="' + id + '" class="' + self.classes + '">' + '<div class="' + prefix + 'bar-container">' + '<div class="' + prefix + 'bar"></div>' + '</div>' + '<div class="' + prefix + 'text">0%</div>' + '</div>';
      },
      postRender: function () {
        var self = this;
        self._super();
        self.value(self.settings.value);
        return self;
      },
      bindStates: function () {
        var self = this;
        function setValue(value) {
          value = self.settings.filter(value);
          self.getEl().lastChild.innerHTML = value + '%';
          self.getEl().firstChild.firstChild.style.width = value + '%';
        }
        self.state.on('change:value', function (e) {
          setValue(e.value);
        });
        setValue(self.state.get('value'));
        return self._super();
      }
    });

    var updateLiveRegion = function (ctx, text) {
      ctx.getEl().lastChild.textContent = text + (ctx.progressBar ? ' ' + ctx.progressBar.value() + '%' : '');
    };
    var Notification = Control$1.extend({
      Mixins: [Movable],
      Defaults: { classes: 'widget notification' },
      init: function (settings) {
        var self = this;
        self._super(settings);
        self.maxWidth = settings.maxWidth;
        if (settings.text) {
          self.text(settings.text);
        }
        if (settings.icon) {
          self.icon = settings.icon;
        }
        if (settings.color) {
          self.color = settings.color;
        }
        if (settings.type) {
          self.classes.add('notification-' + settings.type);
        }
        if (settings.timeout && (settings.timeout < 0 || settings.timeout > 0) && !settings.closeButton) {
          self.closeButton = false;
        } else {
          self.classes.add('has-close');
          self.closeButton = true;
        }
        if (settings.progressBar) {
          self.progressBar = new Progress();
        }
        self.on('click', function (e) {
          if (e.target.className.indexOf(self.classPrefix + 'close') !== -1) {
            self.close();
          }
        });
      },
      renderHtml: function () {
        var self = this;
        var prefix = self.classPrefix;
        var icon = '', closeButton = '', progressBar = '', notificationStyle = '';
        if (self.icon) {
          icon = '<i class="' + prefix + 'ico' + ' ' + prefix + 'i-' + self.icon + '"></i>';
        }
        notificationStyle = ' style="max-width: ' + self.maxWidth + 'px;' + (self.color ? 'background-color: ' + self.color + ';"' : '"');
        if (self.closeButton) {
          closeButton = '<button type="button" class="' + prefix + 'close" aria-hidden="true">\xD7</button>';
        }
        if (self.progressBar) {
          progressBar = self.progressBar.renderHtml();
        }
        return '<div id="' + self._id + '" class="' + self.classes + '"' + notificationStyle + ' role="presentation">' + icon + '<div class="' + prefix + 'notification-inner">' + self.state.get('text') + '</div>' + progressBar + closeButton + '<div style="clip: rect(1px, 1px, 1px, 1px);height: 1px;overflow: hidden;position: absolute;width: 1px;"' + ' aria-live="assertive" aria-relevant="additions" aria-atomic="true"></div>' + '</div>';
      },
      postRender: function () {
        var self = this;
        global$7.setTimeout(function () {
          self.$el.addClass(self.classPrefix + 'in');
          updateLiveRegion(self, self.state.get('text'));
        }, 100);
        return self._super();
      },
      bindStates: function () {
        var self = this;
        self.state.on('change:text', function (e) {
          self.getEl().firstChild.innerHTML = e.value;
          updateLiveRegion(self, e.value);
        });
        if (self.progressBar) {
          self.progressBar.bindStates();
          self.progressBar.state.on('change:value', function (e) {
            updateLiveRegion(self, self.state.get('text'));
          });
        }
        return self._super();
      },
      close: function () {
        var self = this;
        if (!self.fire('close').isDefaultPrevented()) {
          self.remove();
        }
        return self;
      },
      repaint: function () {
        var self = this;
        var style, rect;
        style = self.getEl().style;
        rect = self._layoutRect;
        style.left = rect.x + 'px';
        style.top = rect.y + 'px';
        style.zIndex = 65535 - 1;
      }
    });

    function NotificationManagerImpl (editor) {
      var getEditorContainer = function (editor) {
        return editor.inline ? editor.getElement() : editor.getContentAreaContainer();
      };
      var getContainerWidth = function () {
        var container = getEditorContainer(editor);
        return funcs.getSize(container).width;
      };
      var prePositionNotifications = function (notifications) {
        each(notifications, function (notification) {
          notification.moveTo(0, 0);
        });
      };
      var positionNotifications = function (notifications) {
        if (notifications.length > 0) {
          var firstItem = notifications.slice(0, 1)[0];
          var container = getEditorContainer(editor);
          firstItem.moveRel(container, 'tc-tc');
          each(notifications, function (notification, index) {
            if (index > 0) {
              notification.moveRel(notifications[index - 1].getEl(), 'bc-tc');
            }
          });
        }
      };
      var reposition = function (notifications) {
        prePositionNotifications(notifications);
        positionNotifications(notifications);
      };
      var open = function (args, closeCallback) {
        var extendedArgs = global$2.extend(args, { maxWidth: getContainerWidth() });
        var notif = new Notification(extendedArgs);
        notif.args = extendedArgs;
        if (extendedArgs.timeout > 0) {
          notif.timer = setTimeout(function () {
            notif.close();
            closeCallback();
          }, extendedArgs.timeout);
        }
        notif.on('close', function () {
          closeCallback();
        });
        notif.renderTo();
        return notif;
      };
      var close = function (notification) {
        notification.close();
      };
      var getArgs = function (notification) {
        return notification.args;
      };
      return {
        open: open,
        close: close,
        reposition: reposition,
        getArgs: getArgs
      };
    }

    var windows = [];
    var oldMetaValue = '';
    function toggleFullScreenState(state) {
      var noScaleMetaValue = 'width=device-width,initial-scale=1.0,user-scalable=0,minimum-scale=1.0,maximum-scale=1.0';
      var viewport = global$9('meta[name=viewport]')[0], contentValue;
      if (global$8.overrideViewPort === false) {
        return;
      }
      if (!viewport) {
        viewport = domGlobals.document.createElement('meta');
        viewport.setAttribute('name', 'viewport');
        domGlobals.document.getElementsByTagName('head')[0].appendChild(viewport);
      }
      contentValue = viewport.getAttribute('content');
      if (contentValue && typeof oldMetaValue !== 'undefined') {
        oldMetaValue = contentValue;
      }
      viewport.setAttribute('content', state ? noScaleMetaValue : oldMetaValue);
    }
    function toggleBodyFullScreenClasses(classPrefix, state) {
      if (checkFullscreenWindows() && state === false) {
        global$9([
          domGlobals.document.documentElement,
          domGlobals.document.body
        ]).removeClass(classPrefix + 'fullscreen');
      }
    }
    function checkFullscreenWindows() {
      for (var i = 0; i < windows.length; i++) {
        if (windows[i]._fullscreen) {
          return true;
        }
      }
      return false;
    }
    function handleWindowResize() {
      if (!global$8.desktop) {
        var lastSize_1 = {
          w: domGlobals.window.innerWidth,
          h: domGlobals.window.innerHeight
        };
        global$7.setInterval(function () {
          var w = domGlobals.window.innerWidth, h = domGlobals.window.innerHeight;
          if (lastSize_1.w !== w || lastSize_1.h !== h) {
            lastSize_1 = {
              w: w,
              h: h
            };
            global$9(domGlobals.window).trigger('resize');
          }
        }, 100);
      }
      function reposition() {
        var i;
        var rect = funcs.getWindowSize();
        var layoutRect;
        for (i = 0; i < windows.length; i++) {
          layoutRect = windows[i].layoutRect();
          windows[i].moveTo(windows[i].settings.x || Math.max(0, rect.w / 2 - layoutRect.w / 2), windows[i].settings.y || Math.max(0, rect.h / 2 - layoutRect.h / 2));
        }
      }
      global$9(domGlobals.window).on('resize', reposition);
    }
    var Window = FloatPanel.extend({
      modal: true,
      Defaults: {
        border: 1,
        layout: 'flex',
        containerCls: 'panel',
        role: 'dialog',
        callbacks: {
          submit: function () {
            this.fire('submit', { data: this.toJSON() });
          },
          close: function () {
            this.close();
          }
        }
      },
      init: function (settings) {
        var self = this;
        self._super(settings);
        if (self.isRtl()) {
          self.classes.add('rtl');
        }
        self.classes.add('window');
        self.bodyClasses.add('window-body');
        self.state.set('fixed', true);
        if (settings.buttons) {
          self.statusbar = new Panel({
            layout: 'flex',
            border: '1 0 0 0',
            spacing: 3,
            padding: 10,
            align: 'center',
            pack: self.isRtl() ? 'start' : 'end',
            defaults: { type: 'button' },
            items: settings.buttons
          });
          self.statusbar.classes.add('foot');
          self.statusbar.parent(self);
        }
        self.on('click', function (e) {
          var closeClass = self.classPrefix + 'close';
          if (funcs.hasClass(e.target, closeClass) || funcs.hasClass(e.target.parentNode, closeClass)) {
            self.close();
          }
        });
        self.on('cancel', function () {
          self.close();
        });
        self.on('move', function (e) {
          if (e.control === self) {
            FloatPanel.hideAll();
          }
        });
        self.aria('describedby', self.describedBy || self._id + '-none');
        self.aria('label', settings.title);
        self._fullscreen = false;
      },
      recalc: function () {
        var self = this;
        var statusbar = self.statusbar;
        var layoutRect, width, x, needsRecalc;
        if (self._fullscreen) {
          self.layoutRect(funcs.getWindowSize());
          self.layoutRect().contentH = self.layoutRect().innerH;
        }
        self._super();
        layoutRect = self.layoutRect();
        if (self.settings.title && !self._fullscreen) {
          width = layoutRect.headerW;
          if (width > layoutRect.w) {
            x = layoutRect.x - Math.max(0, width / 2);
            self.layoutRect({
              w: width,
              x: x
            });
            needsRecalc = true;
          }
        }
        if (statusbar) {
          statusbar.layoutRect({ w: self.layoutRect().innerW }).recalc();
          width = statusbar.layoutRect().minW + layoutRect.deltaW;
          if (width > layoutRect.w) {
            x = layoutRect.x - Math.max(0, width - layoutRect.w);
            self.layoutRect({
              w: width,
              x: x
            });
            needsRecalc = true;
          }
        }
        if (needsRecalc) {
          self.recalc();
        }
      },
      initLayoutRect: function () {
        var self = this;
        var layoutRect = self._super();
        var deltaH = 0, headEl;
        if (self.settings.title && !self._fullscreen) {
          headEl = self.getEl('head');
          var size = funcs.getSize(headEl);
          layoutRect.headerW = size.width;
          layoutRect.headerH = size.height;
          deltaH += layoutRect.headerH;
        }
        if (self.statusbar) {
          deltaH += self.statusbar.layoutRect().h;
        }
        layoutRect.deltaH += deltaH;
        layoutRect.minH += deltaH;
        layoutRect.h += deltaH;
        var rect = funcs.getWindowSize();
        layoutRect.x = self.settings.x || Math.max(0, rect.w / 2 - layoutRect.w / 2);
        layoutRect.y = self.settings.y || Math.max(0, rect.h / 2 - layoutRect.h / 2);
        return layoutRect;
      },
      renderHtml: function () {
        var self = this, layout = self._layout, id = self._id, prefix = self.classPrefix;
        var settings = self.settings;
        var headerHtml = '', footerHtml = '', html = settings.html;
        self.preRender();
        layout.preRender(self);
        if (settings.title) {
          headerHtml = '<div id="' + id + '-head" class="' + prefix + 'window-head">' + '<div id="' + id + '-title" class="' + prefix + 'title">' + self.encode(settings.title) + '</div>' + '<div id="' + id + '-dragh" class="' + prefix + 'dragh"></div>' + '<button type="button" class="' + prefix + 'close" aria-hidden="true">' + '<i class="mce-ico mce-i-remove"></i>' + '</button>' + '</div>';
        }
        if (settings.url) {
          html = '<iframe src="' + settings.url + '" tabindex="-1"></iframe>';
        }
        if (typeof html === 'undefined') {
          html = layout.renderHtml(self);
        }
        if (self.statusbar) {
          footerHtml = self.statusbar.renderHtml();
        }
        return '<div id="' + id + '" class="' + self.classes + '" hidefocus="1">' + '<div class="' + self.classPrefix + 'reset" role="application">' + headerHtml + '<div id="' + id + '-body" class="' + self.bodyClasses + '">' + html + '</div>' + footerHtml + '</div>' + '</div>';
      },
      fullscreen: function (state) {
        var self = this;
        var documentElement = domGlobals.document.documentElement;
        var slowRendering;
        var prefix = self.classPrefix;
        var layoutRect;
        if (state !== self._fullscreen) {
          global$9(domGlobals.window).on('resize', function () {
            var time;
            if (self._fullscreen) {
              if (!slowRendering) {
                time = new Date().getTime();
                var rect = funcs.getWindowSize();
                self.moveTo(0, 0).resizeTo(rect.w, rect.h);
                if (new Date().getTime() - time > 50) {
                  slowRendering = true;
                }
              } else {
                if (!self._timer) {
                  self._timer = global$7.setTimeout(function () {
                    var rect = funcs.getWindowSize();
                    self.moveTo(0, 0).resizeTo(rect.w, rect.h);
                    self._timer = 0;
                  }, 50);
                }
              }
            }
          });
          layoutRect = self.layoutRect();
          self._fullscreen = state;
          if (!state) {
            self.borderBox = BoxUtils.parseBox(self.settings.border);
            self.getEl('head').style.display = '';
            layoutRect.deltaH += layoutRect.headerH;
            global$9([
              documentElement,
              domGlobals.document.body
            ]).removeClass(prefix + 'fullscreen');
            self.classes.remove('fullscreen');
            self.moveTo(self._initial.x, self._initial.y).resizeTo(self._initial.w, self._initial.h);
          } else {
            self._initial = {
              x: layoutRect.x,
              y: layoutRect.y,
              w: layoutRect.w,
              h: layoutRect.h
            };
            self.borderBox = BoxUtils.parseBox('0');
            self.getEl('head').style.display = 'none';
            layoutRect.deltaH -= layoutRect.headerH + 2;
            global$9([
              documentElement,
              domGlobals.document.body
            ]).addClass(prefix + 'fullscreen');
            self.classes.add('fullscreen');
            var rect = funcs.getWindowSize();
            self.moveTo(0, 0).resizeTo(rect.w, rect.h);
          }
        }
        return self.reflow();
      },
      postRender: function () {
        var self = this;
        var startPos;
        setTimeout(function () {
          self.classes.add('in');
          self.fire('open');
        }, 0);
        self._super();
        if (self.statusbar) {
          self.statusbar.postRender();
        }
        self.focus();
        this.dragHelper = new DragHelper(self._id + '-dragh', {
          start: function () {
            startPos = {
              x: self.layoutRect().x,
              y: self.layoutRect().y
            };
          },
          drag: function (e) {
            self.moveTo(startPos.x + e.deltaX, startPos.y + e.deltaY);
          }
        });
        self.on('submit', function (e) {
          if (!e.isDefaultPrevented()) {
            self.close();
          }
        });
        windows.push(self);
        toggleFullScreenState(true);
      },
      submit: function () {
        return this.fire('submit', { data: this.toJSON() });
      },
      remove: function () {
        var self = this;
        var i;
        self.dragHelper.destroy();
        self._super();
        if (self.statusbar) {
          this.statusbar.remove();
        }
        toggleBodyFullScreenClasses(self.classPrefix, false);
        i = windows.length;
        while (i--) {
          if (windows[i] === self) {
            windows.splice(i, 1);
          }
        }
        toggleFullScreenState(windows.length > 0);
      },
      getContentWindow: function () {
        var ifr = this.getEl().getElementsByTagName('iframe')[0];
        return ifr ? ifr.contentWindow : null;
      }
    });
    handleWindowResize();

    var MessageBox = Window.extend({
      init: function (settings) {
        settings = {
          border: 1,
          padding: 20,
          layout: 'flex',
          pack: 'center',
          align: 'center',
          containerCls: 'panel',
          autoScroll: true,
          buttons: {
            type: 'button',
            text: 'Ok',
            action: 'ok'
          },
          items: {
            type: 'label',
            multiline: true,
            maxWidth: 500,
            maxHeight: 200
          }
        };
        this._super(settings);
      },
      Statics: {
        OK: 1,
        OK_CANCEL: 2,
        YES_NO: 3,
        YES_NO_CANCEL: 4,
        msgBox: function (settings) {
          var buttons;
          var callback = settings.callback || function () {
          };
          function createButton(text, status, primary) {
            return {
              type: 'button',
              text: text,
              subtype: primary ? 'primary' : '',
              onClick: function (e) {
                e.control.parents()[1].close();
                callback(status);
              }
            };
          }
          switch (settings.buttons) {
          case MessageBox.OK_CANCEL:
            buttons = [
              createButton('Ok', true, true),
              createButton('Cancel', false)
            ];
            break;
          case MessageBox.YES_NO:
          case MessageBox.YES_NO_CANCEL:
            buttons = [
              createButton('Yes', 1, true),
              createButton('No', 0)
            ];
            if (settings.buttons === MessageBox.YES_NO_CANCEL) {
              buttons.push(createButton('Cancel', -1));
            }
            break;
          default:
            buttons = [createButton('Ok', true, true)];
            break;
          }
          return new Window({
            padding: 20,
            x: settings.x,
            y: settings.y,
            minWidth: 300,
            minHeight: 100,
            layout: 'flex',
            pack: 'center',
            align: 'center',
            buttons: buttons,
            title: settings.title,
            role: 'alertdialog',
            items: {
              type: 'label',
              multiline: true,
              maxWidth: 500,
              maxHeight: 200,
              text: settings.text
            },
            onPostRender: function () {
              this.aria('describedby', this.items()[0]._id);
            },
            onClose: settings.onClose,
            onCancel: function () {
              callback(false);
            }
          }).renderTo(domGlobals.document.body).reflow();
        },
        alert: function (settings, callback) {
          if (typeof settings === 'string') {
            settings = { text: settings };
          }
          settings.callback = callback;
          return MessageBox.msgBox(settings);
        },
        confirm: function (settings, callback) {
          if (typeof settings === 'string') {
            settings = { text: settings };
          }
          settings.callback = callback;
          settings.buttons = MessageBox.OK_CANCEL;
          return MessageBox.msgBox(settings);
        }
      }
    });

    function WindowManagerImpl (editor) {
      var open = function (args, params, closeCallback) {
        var win;
        args.title = args.title || ' ';
        args.url = args.url || args.file;
        if (args.url) {
          args.width = parseInt(args.width || 320, 10);
          args.height = parseInt(args.height || 240, 10);
        }
        if (args.body) {
          args.items = {
            defaults: args.defaults,
            type: args.bodyType || 'form',
            items: args.body,
            data: args.data,
            callbacks: args.commands
          };
        }
        if (!args.url && !args.buttons) {
          args.buttons = [
            {
              text: 'Ok',
              subtype: 'primary',
              onclick: function () {
                win.find('form')[0].submit();
              }
            },
            {
              text: 'Cancel',
              onclick: function () {
                win.close();
              }
            }
          ];
        }
        win = new Window(args);
        win.on('close', function () {
          closeCallback(win);
        });
        if (args.data) {
          win.on('postRender', function () {
            this.find('*').each(function (ctrl) {
              var name = ctrl.name();
              if (name in args.data) {
                ctrl.value(args.data[name]);
              }
            });
          });
        }
        win.features = args || {};
        win.params = params || {};
        win = win.renderTo(domGlobals.document.body).reflow();
        return win;
      };
      var alert = function (message, choiceCallback, closeCallback) {
        var win;
        win = MessageBox.alert(message, function () {
          choiceCallback();
        });
        win.on('close', function () {
          closeCallback(win);
        });
        return win;
      };
      var confirm = function (message, choiceCallback, closeCallback) {
        var win;
        win = MessageBox.confirm(message, function (state) {
          choiceCallback(state);
        });
        win.on('close', function () {
          closeCallback(win);
        });
        return win;
      };
      var close = function (window) {
        window.close();
      };
      var getParams = function (window) {
        return window.params;
      };
      var setParams = function (window, params) {
        window.params = params;
      };
      return {
        open: open,
        alert: alert,
        confirm: confirm,
        close: close,
        getParams: getParams,
        setParams: setParams
      };
    }

    var get = function (editor) {
      var renderUI = function (args) {
        return Render.renderUI(editor, this, args);
      };
      var resizeTo = function (w, h) {
        return Resize.resizeTo(editor, w, h);
      };
      var resizeBy = function (dw, dh) {
        return Resize.resizeBy(editor, dw, dh);
      };
      var getNotificationManagerImpl = function () {
        return NotificationManagerImpl(editor);
      };
      var getWindowManagerImpl = function () {
        return WindowManagerImpl();
      };
      return {
        renderUI: renderUI,
        resizeTo: resizeTo,
        resizeBy: resizeBy,
        getNotificationManagerImpl: getNotificationManagerImpl,
        getWindowManagerImpl: getWindowManagerImpl
      };
    };
    var ThemeApi = { get: get };

    var Layout = global$a.extend({
      Defaults: {
        firstControlClass: 'first',
        lastControlClass: 'last'
      },
      init: function (settings) {
        this.settings = global$2.extend({}, this.Defaults, settings);
      },
      preRender: function (container) {
        container.bodyClasses.add(this.settings.containerClass);
      },
      applyClasses: function (items) {
        var self = this;
        var settings = self.settings;
        var firstClass, lastClass, firstItem, lastItem;
        firstClass = settings.firstControlClass;
        lastClass = settings.lastControlClass;
        items.each(function (item) {
          item.classes.remove(firstClass).remove(lastClass).add(settings.controlClass);
          if (item.visible()) {
            if (!firstItem) {
              firstItem = item;
            }
            lastItem = item;
          }
        });
        if (firstItem) {
          firstItem.classes.add(firstClass);
        }
        if (lastItem) {
          lastItem.classes.add(lastClass);
        }
      },
      renderHtml: function (container) {
        var self = this;
        var html = '';
        self.applyClasses(container.items());
        container.items().each(function (item) {
          html += item.renderHtml();
        });
        return html;
      },
      recalc: function () {
      },
      postRender: function () {
      },
      isNative: function () {
        return false;
      }
    });

    var AbsoluteLayout = Layout.extend({
      Defaults: {
        containerClass: 'abs-layout',
        controlClass: 'abs-layout-item'
      },
      recalc: function (container) {
        container.items().filter(':visible').each(function (ctrl) {
          var settings = ctrl.settings;
          ctrl.layoutRect({
            x: settings.x,
            y: settings.y,
            w: settings.w,
            h: settings.h
          });
          if (ctrl.recalc) {
            ctrl.recalc();
          }
        });
      },
      renderHtml: function (container) {
        return '<div id="' + container._id + '-absend" class="' + container.classPrefix + 'abs-end"></div>' + this._super(container);
      }
    });

    var Button = Widget.extend({
      Defaults: {
        classes: 'widget btn',
        role: 'button'
      },
      init: function (settings) {
        var self = this;
        var size;
        self._super(settings);
        settings = self.settings;
        size = self.settings.size;
        self.on('click mousedown', function (e) {
          e.preventDefault();
        });
        self.on('touchstart', function (e) {
          self.fire('click', e);
          e.preventDefault();
        });
        if (settings.subtype) {
          self.classes.add(settings.subtype);
        }
        if (size) {
          self.classes.add('btn-' + size);
        }
        if (settings.icon) {
          self.icon(settings.icon);
        }
      },
      icon: function (icon) {
        if (!arguments.length) {
          return this.state.get('icon');
        }
        this.state.set('icon', icon);
        return this;
      },
      repaint: function () {
        var btnElm = this.getEl().firstChild;
        var btnStyle;
        if (btnElm) {
          btnStyle = btnElm.style;
          btnStyle.width = btnStyle.height = '100%';
        }
        this._super();
      },
      renderHtml: function () {
        var self = this, id = self._id, prefix = self.classPrefix;
        var icon = self.state.get('icon'), image;
        var text = self.state.get('text');
        var textHtml = '';
        var ariaPressed;
        var settings = self.settings;
        image = settings.image;
        if (image) {
          icon = 'none';
          if (typeof image !== 'string') {
            image = domGlobals.window.getSelection ? image[0] : image[1];
          }
          image = ' style="background-image: url(\'' + image + '\')"';
        } else {
          image = '';
        }
        if (text) {
          self.classes.add('btn-has-text');
          textHtml = '<span class="' + prefix + 'txt">' + self.encode(text) + '</span>';
        }
        icon = icon ? prefix + 'ico ' + prefix + 'i-' + icon : '';
        ariaPressed = typeof settings.active === 'boolean' ? ' aria-pressed="' + settings.active + '"' : '';
        return '<div id="' + id + '" class="' + self.classes + '" tabindex="-1"' + ariaPressed + '>' + '<button id="' + id + '-button" role="presentation" type="button" tabindex="-1">' + (icon ? '<i class="' + icon + '"' + image + '></i>' : '') + textHtml + '</button>' + '</div>';
      },
      bindStates: function () {
        var self = this, $ = self.$, textCls = self.classPrefix + 'txt';
        function setButtonText(text) {
          var $span = $('span.' + textCls, self.getEl());
          if (text) {
            if (!$span[0]) {
              $('button:first', self.getEl()).append('<span class="' + textCls + '"></span>');
              $span = $('span.' + textCls, self.getEl());
            }
            $span.html(self.encode(text));
          } else {
            $span.remove();
          }
          self.classes.toggle('btn-has-text', !!text);
        }
        self.state.on('change:text', function (e) {
          setButtonText(e.value);
        });
        self.state.on('change:icon', function (e) {
          var icon = e.value;
          var prefix = self.classPrefix;
          self.settings.icon = icon;
          icon = icon ? prefix + 'ico ' + prefix + 'i-' + self.settings.icon : '';
          var btnElm = self.getEl().firstChild;
          var iconElm = btnElm.getElementsByTagName('i')[0];
          if (icon) {
            if (!iconElm || iconElm !== btnElm.firstChild) {
              iconElm = domGlobals.document.createElement('i');
              btnElm.insertBefore(iconElm, btnElm.firstChild);
            }
            iconElm.className = icon;
          } else if (iconElm) {
            btnElm.removeChild(iconElm);
          }
          setButtonText(self.state.get('text'));
        });
        return self._super();
      }
    });

    var BrowseButton = Button.extend({
      init: function (settings) {
        var self = this;
        settings = global$2.extend({
          text: 'Browse...',
          multiple: false,
          accept: null
        }, settings);
        self._super(settings);
        self.classes.add('browsebutton');
        if (settings.multiple) {
          self.classes.add('multiple');
        }
      },
      postRender: function () {
        var self = this;
        var input = funcs.create('input', {
          type: 'file',
          id: self._id + '-browse',
          accept: self.settings.accept
        });
        self._super();
        global$9(input).on('change', function (e) {
          var files = e.target.files;
          self.value = function () {
            if (!files.length) {
              return null;
            } else if (self.settings.multiple) {
              return files;
            } else {
              return files[0];
            }
          };
          e.preventDefault();
          if (files.length) {
            self.fire('change', e);
          }
        });
        global$9(input).on('click', function (e) {
          e.stopPropagation();
        });
        global$9(self.getEl('button')).on('click touchstart', function (e) {
          e.stopPropagation();
          input.click();
          e.preventDefault();
        });
        self.getEl().appendChild(input);
      },
      remove: function () {
        global$9(this.getEl('button')).off();
        global$9(this.getEl('input')).off();
        this._super();
      }
    });

    var ButtonGroup = Container.extend({
      Defaults: {
        defaultType: 'button',
        role: 'group'
      },
      renderHtml: function () {
        var self = this, layout = self._layout;
        self.classes.add('btn-group');
        self.preRender();
        layout.preRender(self);
        return '<div id="' + self._id + '" class="' + self.classes + '">' + '<div id="' + self._id + '-body">' + (self.settings.html || '') + layout.renderHtml(self) + '</div>' + '</div>';
      }
    });

    var Checkbox = Widget.extend({
      Defaults: {
        classes: 'checkbox',
        role: 'checkbox',
        checked: false
      },
      init: function (settings) {
        var self = this;
        self._super(settings);
        self.on('click mousedown', function (e) {
          e.preventDefault();
        });
        self.on('click', function (e) {
          e.preventDefault();
          if (!self.disabled()) {
            self.checked(!self.checked());
          }
        });
        self.checked(self.settings.checked);
      },
      checked: function (state) {
        if (!arguments.length) {
          return this.state.get('checked');
        }
        this.state.set('checked', state);
        return this;
      },
      value: function (state) {
        if (!arguments.length) {
          return this.checked();
        }
        return this.checked(state);
      },
      renderHtml: function () {
        var self = this, id = self._id, prefix = self.classPrefix;
        return '<div id="' + id + '" class="' + self.classes + '" unselectable="on" aria-labelledby="' + id + '-al" tabindex="-1">' + '<i class="' + prefix + 'ico ' + prefix + 'i-checkbox"></i>' + '<span id="' + id + '-al" class="' + prefix + 'label">' + self.encode(self.state.get('text')) + '</span>' + '</div>';
      },
      bindStates: function () {
        var self = this;
        function checked(state) {
          self.classes.toggle('checked', state);
          self.aria('checked', state);
        }
        self.state.on('change:text', function (e) {
          self.getEl('al').firstChild.data = self.translate(e.value);
        });
        self.state.on('change:checked change:value', function (e) {
          self.fire('change');
          checked(e.value);
        });
        self.state.on('change:icon', function (e) {
          var icon = e.value;
          var prefix = self.classPrefix;
          if (typeof icon === 'undefined') {
            return self.settings.icon;
          }
          self.settings.icon = icon;
          icon = icon ? prefix + 'ico ' + prefix + 'i-' + self.settings.icon : '';
          var btnElm = self.getEl().firstChild;
          var iconElm = btnElm.getElementsByTagName('i')[0];
          if (icon) {
            if (!iconElm || iconElm !== btnElm.firstChild) {
              iconElm = domGlobals.document.createElement('i');
              btnElm.insertBefore(iconElm, btnElm.firstChild);
            }
            iconElm.className = icon;
          } else if (iconElm) {
            btnElm.removeChild(iconElm);
          }
        });
        if (self.state.get('checked')) {
          checked(true);
        }
        return self._super();
      }
    });

    var global$d = tinymce.util.Tools.resolve('tinymce.util.VK');

    var ComboBox = Widget.extend({
      init: function (settings) {
        var self = this;
        self._super(settings);
        settings = self.settings;
        self.classes.add('combobox');
        self.subinput = true;
        self.ariaTarget = 'inp';
        settings.menu = settings.menu || settings.values;
        if (settings.menu) {
          settings.icon = 'caret';
        }
        self.on('click', function (e) {
          var elm = e.target;
          var root = self.getEl();
          if (!global$9.contains(root, elm) && elm !== root) {
            return;
          }
          while (elm && elm !== root) {
            if (elm.id && elm.id.indexOf('-open') !== -1) {
              self.fire('action');
              if (settings.menu) {
                self.showMenu();
                if (e.aria) {
                  self.menu.items()[0].focus();
                }
              }
            }
            elm = elm.parentNode;
          }
        });
        self.on('keydown', function (e) {
          var rootControl;
          if (e.keyCode === 13 && e.target.nodeName === 'INPUT') {
            e.preventDefault();
            self.parents().reverse().each(function (ctrl) {
              if (ctrl.toJSON) {
                rootControl = ctrl;
                return false;
              }
            });
            self.fire('submit', { data: rootControl.toJSON() });
          }
        });
        self.on('keyup', function (e) {
          if (e.target.nodeName === 'INPUT') {
            var oldValue = self.state.get('value');
            var newValue = e.target.value;
            if (newValue !== oldValue) {
              self.state.set('value', newValue);
              self.fire('autocomplete', e);
            }
          }
        });
        self.on('mouseover', function (e) {
          var tooltip = self.tooltip().moveTo(-65535);
          if (self.statusLevel() && e.target.className.indexOf(self.classPrefix + 'status') !== -1) {
            var statusMessage = self.statusMessage() || 'Ok';
            var rel = tooltip.text(statusMessage).show().testMoveRel(e.target, [
              'bc-tc',
              'bc-tl',
              'bc-tr'
            ]);
            tooltip.classes.toggle('tooltip-n', rel === 'bc-tc');
            tooltip.classes.toggle('tooltip-nw', rel === 'bc-tl');
            tooltip.classes.toggle('tooltip-ne', rel === 'bc-tr');
            tooltip.moveRel(e.target, rel);
          }
        });
      },
      statusLevel: function (value) {
        if (arguments.length > 0) {
          this.state.set('statusLevel', value);
        }
        return this.state.get('statusLevel');
      },
      statusMessage: function (value) {
        if (arguments.length > 0) {
          this.state.set('statusMessage', value);
        }
        return this.state.get('statusMessage');
      },
      showMenu: function () {
        var self = this;
        var settings = self.settings;
        var menu;
        if (!self.menu) {
          menu = settings.menu || [];
          if (menu.length) {
            menu = {
              type: 'menu',
              items: menu
            };
          } else {
            menu.type = menu.type || 'menu';
          }
          self.menu = global$4.create(menu).parent(self).renderTo(self.getContainerElm());
          self.fire('createmenu');
          self.menu.reflow();
          self.menu.on('cancel', function (e) {
            if (e.control === self.menu) {
              self.focus();
            }
          });
          self.menu.on('show hide', function (e) {
            e.control.items().each(function (ctrl) {
              ctrl.active(ctrl.value() === self.value());
            });
          }).fire('show');
          self.menu.on('select', function (e) {
            self.value(e.control.value());
          });
          self.on('focusin', function (e) {
            if (e.target.tagName.toUpperCase() === 'INPUT') {
              self.menu.hide();
            }
          });
          self.aria('expanded', true);
        }
        self.menu.show();
        self.menu.layoutRect({ w: self.layoutRect().w });
        self.menu.moveRel(self.getEl(), self.isRtl() ? [
          'br-tr',
          'tr-br'
        ] : [
          'bl-tl',
          'tl-bl'
        ]);
      },
      focus: function () {
        this.getEl('inp').focus();
      },
      repaint: function () {
        var self = this, elm = self.getEl(), openElm = self.getEl('open'), rect = self.layoutRect();
        var width, lineHeight, innerPadding = 0;
        var inputElm = elm.firstChild;
        if (self.statusLevel() && self.statusLevel() !== 'none') {
          innerPadding = parseInt(funcs.getRuntimeStyle(inputElm, 'padding-right'), 10) - parseInt(funcs.getRuntimeStyle(inputElm, 'padding-left'), 10);
        }
        if (openElm) {
          width = rect.w - funcs.getSize(openElm).width - 10;
        } else {
          width = rect.w - 10;
        }
        var doc = domGlobals.document;
        if (doc.all && (!doc.documentMode || doc.documentMode <= 8)) {
          lineHeight = self.layoutRect().h - 2 + 'px';
        }
        global$9(inputElm).css({
          width: width - innerPadding,
          lineHeight: lineHeight
        });
        self._super();
        return self;
      },
      postRender: function () {
        var self = this;
        global$9(this.getEl('inp')).on('change', function (e) {
          self.state.set('value', e.target.value);
          self.fire('change', e);
        });
        return self._super();
      },
      renderHtml: function () {
        var self = this, id = self._id, settings = self.settings, prefix = self.classPrefix;
        var value = self.state.get('value') || '';
        var icon, text, openBtnHtml = '', extraAttrs = '', statusHtml = '';
        if ('spellcheck' in settings) {
          extraAttrs += ' spellcheck="' + settings.spellcheck + '"';
        }
        if (settings.maxLength) {
          extraAttrs += ' maxlength="' + settings.maxLength + '"';
        }
        if (settings.size) {
          extraAttrs += ' size="' + settings.size + '"';
        }
        if (settings.subtype) {
          extraAttrs += ' type="' + settings.subtype + '"';
        }
        statusHtml = '<i id="' + id + '-status" class="mce-status mce-ico" style="display: none"></i>';
        if (self.disabled()) {
          extraAttrs += ' disabled="disabled"';
        }
        icon = settings.icon;
        if (icon && icon !== 'caret') {
          icon = prefix + 'ico ' + prefix + 'i-' + settings.icon;
        }
        text = self.state.get('text');
        if (icon || text) {
          openBtnHtml = '<div id="' + id + '-open" class="' + prefix + 'btn ' + prefix + 'open" tabIndex="-1" role="button">' + '<button id="' + id + '-action" type="button" hidefocus="1" tabindex="-1">' + (icon !== 'caret' ? '<i class="' + icon + '"></i>' : '<i class="' + prefix + 'caret"></i>') + (text ? (icon ? ' ' : '') + text : '') + '</button>' + '</div>';
          self.classes.add('has-open');
        }
        return '<div id="' + id + '" class="' + self.classes + '">' + '<input id="' + id + '-inp" class="' + prefix + 'textbox" value="' + self.encode(value, false) + '" hidefocus="1"' + extraAttrs + ' placeholder="' + self.encode(settings.placeholder) + '" />' + statusHtml + openBtnHtml + '</div>';
      },
      value: function (value) {
        if (arguments.length) {
          this.state.set('value', value);
          return this;
        }
        if (this.state.get('rendered')) {
          this.state.set('value', this.getEl('inp').value);
        }
        return this.state.get('value');
      },
      showAutoComplete: function (items, term) {
        var self = this;
        if (items.length === 0) {
          self.hideMenu();
          return;
        }
        var insert = function (value, title) {
          return function () {
            self.fire('selectitem', {
              title: title,
              value: value
            });
          };
        };
        if (self.menu) {
          self.menu.items().remove();
        } else {
          self.menu = global$4.create({
            type: 'menu',
            classes: 'combobox-menu',
            layout: 'flow'
          }).parent(self).renderTo();
        }
        global$2.each(items, function (item) {
          self.menu.add({
            text: item.title,
            url: item.previewUrl,
            match: term,
            classes: 'menu-item-ellipsis',
            onclick: insert(item.value, item.title)
          });
        });
        self.menu.renderNew();
        self.hideMenu();
        self.menu.on('cancel', function (e) {
          if (e.control.parent() === self.menu) {
            e.stopPropagation();
            self.focus();
            self.hideMenu();
          }
        });
        self.menu.on('select', function () {
          self.focus();
        });
        var maxW = self.layoutRect().w;
        self.menu.layoutRect({
          w: maxW,
          minW: 0,
          maxW: maxW
        });
        self.menu.repaint();
        self.menu.reflow();
        self.menu.show();
        self.menu.moveRel(self.getEl(), self.isRtl() ? [
          'br-tr',
          'tr-br'
        ] : [
          'bl-tl',
          'tl-bl'
        ]);
      },
      hideMenu: function () {
        if (this.menu) {
          this.menu.hide();
        }
      },
      bindStates: function () {
        var self = this;
        self.state.on('change:value', function (e) {
          if (self.getEl('inp').value !== e.value) {
            self.getEl('inp').value = e.value;
          }
        });
        self.state.on('change:disabled', function (e) {
          self.getEl('inp').disabled = e.value;
        });
        self.state.on('change:statusLevel', function (e) {
          var statusIconElm = self.getEl('status');
          var prefix = self.classPrefix, value = e.value;
          funcs.css(statusIconElm, 'display', value === 'none' ? 'none' : '');
          funcs.toggleClass(statusIconElm, prefix + 'i-checkmark', value === 'ok');
          funcs.toggleClass(statusIconElm, prefix + 'i-warning', value === 'warn');
          funcs.toggleClass(statusIconElm, prefix + 'i-error', value === 'error');
          self.classes.toggle('has-status', value !== 'none');
          self.repaint();
        });
        funcs.on(self.getEl('status'), 'mouseleave', function () {
          self.tooltip().hide();
        });
        self.on('cancel', function (e) {
          if (self.menu && self.menu.visible()) {
            e.stopPropagation();
            self.hideMenu();
          }
        });
        var focusIdx = function (idx, menu) {
          if (menu && menu.items().length > 0) {
            menu.items().eq(idx)[0].focus();
          }
        };
        self.on('keydown', function (e) {
          var keyCode = e.keyCode;
          if (e.target.nodeName === 'INPUT') {
            if (keyCode === global$d.DOWN) {
              e.preventDefault();
              self.fire('autocomplete');
              focusIdx(0, self.menu);
            } else if (keyCode === global$d.UP) {
              e.preventDefault();
              focusIdx(-1, self.menu);
            }
          }
        });
        return self._super();
      },
      remove: function () {
        global$9(this.getEl('inp')).off();
        if (this.menu) {
          this.menu.remove();
        }
        this._super();
      }
    });

    var ColorBox = ComboBox.extend({
      init: function (settings) {
        var self = this;
        settings.spellcheck = false;
        if (settings.onaction) {
          settings.icon = 'none';
        }
        self._super(settings);
        self.classes.add('colorbox');
        self.on('change keyup postrender', function () {
          self.repaintColor(self.value());
        });
      },
      repaintColor: function (value) {
        var openElm = this.getEl('open');
        var elm = openElm ? openElm.getElementsByTagName('i')[0] : null;
        if (elm) {
          try {
            elm.style.background = value;
          } catch (ex) {
          }
        }
      },
      bindStates: function () {
        var self = this;
        self.state.on('change:value', function (e) {
          if (self.state.get('rendered')) {
            self.repaintColor(e.value);
          }
        });
        return self._super();
      }
    });

    var PanelButton = Button.extend({
      showPanel: function () {
        var self = this, settings = self.settings;
        self.classes.add('opened');
        if (!self.panel) {
          var panelSettings = settings.panel;
          if (panelSettings.type) {
            panelSettings = {
              layout: 'grid',
              items: panelSettings
            };
          }
          panelSettings.role = panelSettings.role || 'dialog';
          panelSettings.popover = true;
          panelSettings.autohide = true;
          panelSettings.ariaRoot = true;
          self.panel = new FloatPanel(panelSettings).on('hide', function () {
            self.classes.remove('opened');
          }).on('cancel', function (e) {
            e.stopPropagation();
            self.focus();
            self.hidePanel();
          }).parent(self).renderTo(self.getContainerElm());
          self.panel.fire('show');
          self.panel.reflow();
        } else {
          self.panel.show();
        }
        var rtlRels = [
          'bc-tc',
          'bc-tl',
          'bc-tr'
        ];
        var ltrRels = [
          'bc-tc',
          'bc-tr',
          'bc-tl',
          'tc-bc',
          'tc-br',
          'tc-bl'
        ];
        var rel = self.panel.testMoveRel(self.getEl(), settings.popoverAlign || (self.isRtl() ? rtlRels : ltrRels));
        self.panel.classes.toggle('start', rel.substr(-1) === 'l');
        self.panel.classes.toggle('end', rel.substr(-1) === 'r');
        var isTop = rel.substr(0, 1) === 't';
        self.panel.classes.toggle('bottom', !isTop);
        self.panel.classes.toggle('top', isTop);
        self.panel.moveRel(self.getEl(), rel);
      },
      hidePanel: function () {
        var self = this;
        if (self.panel) {
          self.panel.hide();
        }
      },
      postRender: function () {
        var self = this;
        self.aria('haspopup', true);
        self.on('click', function (e) {
          if (e.control === self) {
            if (self.panel && self.panel.visible()) {
              self.hidePanel();
            } else {
              self.showPanel();
              self.panel.focus(!!e.aria);
            }
          }
        });
        return self._super();
      },
      remove: function () {
        if (this.panel) {
          this.panel.remove();
          this.panel = null;
        }
        return this._super();
      }
    });

    var DOM$3 = global$3.DOM;
    var ColorButton = PanelButton.extend({
      init: function (settings) {
        this._super(settings);
        this.classes.add('splitbtn');
        this.classes.add('colorbutton');
      },
      color: function (color) {
        if (color) {
          this._color = color;
          this.getEl('preview').style.backgroundColor = color;
          return this;
        }
        return this._color;
      },
      resetColor: function () {
        this._color = null;
        this.getEl('preview').style.backgroundColor = null;
        return this;
      },
      renderHtml: function () {
        var self = this, id = self._id, prefix = self.classPrefix, text = self.state.get('text');
        var icon = self.settings.icon ? prefix + 'ico ' + prefix + 'i-' + self.settings.icon : '';
        var image = self.settings.image ? ' style="background-image: url(\'' + self.settings.image + '\')"' : '';
        var textHtml = '';
        if (text) {
          self.classes.add('btn-has-text');
          textHtml = '<span class="' + prefix + 'txt">' + self.encode(text) + '</span>';
        }
        return '<div id="' + id + '" class="' + self.classes + '" role="button" tabindex="-1" aria-haspopup="true">' + '<button role="presentation" hidefocus="1" type="button" tabindex="-1">' + (icon ? '<i class="' + icon + '"' + image + '></i>' : '') + '<span id="' + id + '-preview" class="' + prefix + 'preview"></span>' + textHtml + '</button>' + '<button type="button" class="' + prefix + 'open" hidefocus="1" tabindex="-1">' + ' <i class="' + prefix + 'caret"></i>' + '</button>' + '</div>';
      },
      postRender: function () {
        var self = this, onClickHandler = self.settings.onclick;
        self.on('click', function (e) {
          if (e.aria && e.aria.key === 'down') {
            return;
          }
          if (e.control === self && !DOM$3.getParent(e.target, '.' + self.classPrefix + 'open')) {
            e.stopImmediatePropagation();
            onClickHandler.call(self, e);
          }
        });
        delete self.settings.onclick;
        return self._super();
      }
    });

    var global$e = tinymce.util.Tools.resolve('tinymce.util.Color');

    var ColorPicker = Widget.extend({
      Defaults: { classes: 'widget colorpicker' },
      init: function (settings) {
        this._super(settings);
      },
      postRender: function () {
        var self = this;
        var color = self.color();
        var hsv, hueRootElm, huePointElm, svRootElm, svPointElm;
        hueRootElm = self.getEl('h');
        huePointElm = self.getEl('hp');
        svRootElm = self.getEl('sv');
        svPointElm = self.getEl('svp');
        function getPos(elm, event) {
          var pos = funcs.getPos(elm);
          var x, y;
          x = event.pageX - pos.x;
          y = event.pageY - pos.y;
          x = Math.max(0, Math.min(x / elm.clientWidth, 1));
          y = Math.max(0, Math.min(y / elm.clientHeight, 1));
          return {
            x: x,
            y: y
          };
        }
        function updateColor(hsv, hueUpdate) {
          var hue = (360 - hsv.h) / 360;
          funcs.css(huePointElm, { top: hue * 100 + '%' });
          if (!hueUpdate) {
            funcs.css(svPointElm, {
              left: hsv.s + '%',
              top: 100 - hsv.v + '%'
            });
          }
          svRootElm.style.background = global$e({
            s: 100,
            v: 100,
            h: hsv.h
          }).toHex();
          self.color().parse({
            s: hsv.s,
            v: hsv.v,
            h: hsv.h
          });
        }
        function updateSaturationAndValue(e) {
          var pos;
          pos = getPos(svRootElm, e);
          hsv.s = pos.x * 100;
          hsv.v = (1 - pos.y) * 100;
          updateColor(hsv);
          self.fire('change');
        }
        function updateHue(e) {
          var pos;
          pos = getPos(hueRootElm, e);
          hsv = color.toHsv();
          hsv.h = (1 - pos.y) * 360;
          updateColor(hsv, true);
          self.fire('change');
        }
        self._repaint = function () {
          hsv = color.toHsv();
          updateColor(hsv);
        };
        self._super();
        self._svdraghelper = new DragHelper(self._id + '-sv', {
          start: updateSaturationAndValue,
          drag: updateSaturationAndValue
        });
        self._hdraghelper = new DragHelper(self._id + '-h', {
          start: updateHue,
          drag: updateHue
        });
        self._repaint();
      },
      rgb: function () {
        return this.color().toRgb();
      },
      value: function (value) {
        var self = this;
        if (arguments.length) {
          self.color().parse(value);
          if (self._rendered) {
            self._repaint();
          }
        } else {
          return self.color().toHex();
        }
      },
      color: function () {
        if (!this._color) {
          this._color = global$e();
        }
        return this._color;
      },
      renderHtml: function () {
        var self = this;
        var id = self._id;
        var prefix = self.classPrefix;
        var hueHtml;
        var stops = '#ff0000,#ff0080,#ff00ff,#8000ff,#0000ff,#0080ff,#00ffff,#00ff80,#00ff00,#80ff00,#ffff00,#ff8000,#ff0000';
        function getOldIeFallbackHtml() {
          var i, l, html = '', gradientPrefix, stopsList;
          gradientPrefix = 'filter:progid:DXImageTransform.Microsoft.gradient(GradientType=0,startColorstr=';
          stopsList = stops.split(',');
          for (i = 0, l = stopsList.length - 1; i < l; i++) {
            html += '<div class="' + prefix + 'colorpicker-h-chunk" style="' + 'height:' + 100 / l + '%;' + gradientPrefix + stopsList[i] + ',endColorstr=' + stopsList[i + 1] + ');' + '-ms-' + gradientPrefix + stopsList[i] + ',endColorstr=' + stopsList[i + 1] + ')' + '"></div>';
          }
          return html;
        }
        var gradientCssText = 'background: -ms-linear-gradient(top,' + stops + ');' + 'background: linear-gradient(to bottom,' + stops + ');';
        hueHtml = '<div id="' + id + '-h" class="' + prefix + 'colorpicker-h" style="' + gradientCssText + '">' + getOldIeFallbackHtml() + '<div id="' + id + '-hp" class="' + prefix + 'colorpicker-h-marker"></div>' + '</div>';
        return '<div id="' + id + '" class="' + self.classes + '">' + '<div id="' + id + '-sv" class="' + prefix + 'colorpicker-sv">' + '<div class="' + prefix + 'colorpicker-overlay1">' + '<div class="' + prefix + 'colorpicker-overlay2">' + '<div id="' + id + '-svp" class="' + prefix + 'colorpicker-selector1">' + '<div class="' + prefix + 'colorpicker-selector2"></div>' + '</div>' + '</div>' + '</div>' + '</div>' + hueHtml + '</div>';
      }
    });

    var DropZone = Widget.extend({
      init: function (settings) {
        var self = this;
        settings = global$2.extend({
          height: 100,
          text: 'Drop an image here',
          multiple: false,
          accept: null
        }, settings);
        self._super(settings);
        self.classes.add('dropzone');
        if (settings.multiple) {
          self.classes.add('multiple');
        }
      },
      renderHtml: function () {
        var self = this;
        var attrs, elm;
        var cfg = self.settings;
        attrs = {
          id: self._id,
          hidefocus: '1'
        };
        elm = funcs.create('div', attrs, '<span>' + this.translate(cfg.text) + '</span>');
        if (cfg.height) {
          funcs.css(elm, 'height', cfg.height + 'px');
        }
        if (cfg.width) {
          funcs.css(elm, 'width', cfg.width + 'px');
        }
        elm.className = self.classes;
        return elm.outerHTML;
      },
      postRender: function () {
        var self = this;
        var toggleDragClass = function (e) {
          e.preventDefault();
          self.classes.toggle('dragenter');
          self.getEl().className = self.classes;
        };
        var filter = function (files) {
          var accept = self.settings.accept;
          if (typeof accept !== 'string') {
            return files;
          }
          var re = new RegExp('(' + accept.split(/\s*,\s*/).join('|') + ')$', 'i');
          return global$2.grep(files, function (file) {
            return re.test(file.name);
          });
        };
        self._super();
        self.$el.on('dragover', function (e) {
          e.preventDefault();
        });
        self.$el.on('dragenter', toggleDragClass);
        self.$el.on('dragleave', toggleDragClass);
        self.$el.on('drop', function (e) {
          e.preventDefault();
          if (self.state.get('disabled')) {
            return;
          }
          var files = filter(e.dataTransfer.files);
          self.value = function () {
            if (!files.length) {
              return null;
            } else if (self.settings.multiple) {
              return files;
            } else {
              return files[0];
            }
          };
          if (files.length) {
            self.fire('change', e);
          }
        });
      },
      remove: function () {
        this.$el.off();
        this._super();
      }
    });

    var Path = Widget.extend({
      init: function (settings) {
        var self = this;
        if (!settings.delimiter) {
          settings.delimiter = '\xBB';
        }
        self._super(settings);
        self.classes.add('path');
        self.canFocus = true;
        self.on('click', function (e) {
          var index;
          var target = e.target;
          if (index = target.getAttribute('data-index')) {
            self.fire('select', {
              value: self.row()[index],
              index: index
            });
          }
        });
        self.row(self.settings.row);
      },
      focus: function () {
        var self = this;
        self.getEl().firstChild.focus();
        return self;
      },
      row: function (row) {
        if (!arguments.length) {
          return this.state.get('row');
        }
        this.state.set('row', row);
        return this;
      },
      renderHtml: function () {
        var self = this;
        return '<div id="' + self._id + '" class="' + self.classes + '">' + self._getDataPathHtml(self.state.get('row')) + '</div>';
      },
      bindStates: function () {
        var self = this;
        self.state.on('change:row', function (e) {
          self.innerHtml(self._getDataPathHtml(e.value));
        });
        return self._super();
      },
      _getDataPathHtml: function (data) {
        var self = this;
        var parts = data || [];
        var i, l, html = '';
        var prefix = self.classPrefix;
        for (i = 0, l = parts.length; i < l; i++) {
          html += (i > 0 ? '<div class="' + prefix + 'divider" aria-hidden="true"> ' + self.settings.delimiter + ' </div>' : '') + '<div role="button" class="' + prefix + 'path-item' + (i === l - 1 ? ' ' + prefix + 'last' : '') + '" data-index="' + i + '" tabindex="-1" id="' + self._id + '-' + i + '" aria-level="' + (i + 1) + '">' + parts[i].name + '</div>';
        }
        if (!html) {
          html = '<div class="' + prefix + 'path-item">\xA0</div>';
        }
        return html;
      }
    });

    var ElementPath = Path.extend({
      postRender: function () {
        var self = this, editor = self.settings.editor;
        function isHidden(elm) {
          if (elm.nodeType === 1) {
            if (elm.nodeName === 'BR' || !!elm.getAttribute('data-mce-bogus')) {
              return true;
            }
            if (elm.getAttribute('data-mce-type') === 'bookmark') {
              return true;
            }
          }
          return false;
        }
        if (editor.settings.elementpath !== false) {
          self.on('select', function (e) {
            editor.focus();
            editor.selection.select(this.row()[e.index].element);
            editor.nodeChanged();
          });
          editor.on('nodeChange', function (e) {
            var outParents = [];
            var parents = e.parents;
            var i = parents.length;
            while (i--) {
              if (parents[i].nodeType === 1 && !isHidden(parents[i])) {
                var args = editor.fire('ResolveName', {
                  name: parents[i].nodeName.toLowerCase(),
                  target: parents[i]
                });
                if (!args.isDefaultPrevented()) {
                  outParents.push({
                    name: args.name,
                    element: parents[i]
                  });
                }
                if (args.isPropagationStopped()) {
                  break;
                }
              }
            }
            self.row(outParents);
          });
        }
        return self._super();
      }
    });

    var FormItem = Container.extend({
      Defaults: {
        layout: 'flex',
        align: 'center',
        defaults: { flex: 1 }
      },
      renderHtml: function () {
        var self = this, layout = self._layout, prefix = self.classPrefix;
        self.classes.add('formitem');
        layout.preRender(self);
        return '<div id="' + self._id + '" class="' + self.classes + '" hidefocus="1" tabindex="-1">' + (self.settings.title ? '<div id="' + self._id + '-title" class="' + prefix + 'title">' + self.settings.title + '</div>' : '') + '<div id="' + self._id + '-body" class="' + self.bodyClasses + '">' + (self.settings.html || '') + layout.renderHtml(self) + '</div>' + '</div>';
      }
    });

    var Form = Container.extend({
      Defaults: {
        containerCls: 'form',
        layout: 'flex',
        direction: 'column',
        align: 'stretch',
        flex: 1,
        padding: 15,
        labelGap: 30,
        spacing: 10,
        callbacks: {
          submit: function () {
            this.submit();
          }
        }
      },
      preRender: function () {
        var self = this, items = self.items();
        if (!self.settings.formItemDefaults) {
          self.settings.formItemDefaults = {
            layout: 'flex',
            autoResize: 'overflow',
            defaults: { flex: 1 }
          };
        }
        items.each(function (ctrl) {
          var formItem;
          var label = ctrl.settings.label;
          if (label) {
            formItem = new FormItem(global$2.extend({
              items: {
                type: 'label',
                id: ctrl._id + '-l',
                text: label,
                flex: 0,
                forId: ctrl._id,
                disabled: ctrl.disabled()
              }
            }, self.settings.formItemDefaults));
            formItem.type = 'formitem';
            ctrl.aria('labelledby', ctrl._id + '-l');
            if (typeof ctrl.settings.flex === 'undefined') {
              ctrl.settings.flex = 1;
            }
            self.replace(ctrl, formItem);
            formItem.add(ctrl);
          }
        });
      },
      submit: function () {
        return this.fire('submit', { data: this.toJSON() });
      },
      postRender: function () {
        var self = this;
        self._super();
        self.fromJSON(self.settings.data);
      },
      bindStates: function () {
        var self = this;
        self._super();
        function recalcLabels() {
          var maxLabelWidth = 0;
          var labels = [];
          var i, labelGap, items;
          if (self.settings.labelGapCalc === false) {
            return;
          }
          if (self.settings.labelGapCalc === 'children') {
            items = self.find('formitem');
          } else {
            items = self.items();
          }
          items.filter('formitem').each(function (item) {
            var labelCtrl = item.items()[0], labelWidth = labelCtrl.getEl().clientWidth;
            maxLabelWidth = labelWidth > maxLabelWidth ? labelWidth : maxLabelWidth;
            labels.push(labelCtrl);
          });
          labelGap = self.settings.labelGap || 0;
          i = labels.length;
          while (i--) {
            labels[i].settings.minWidth = maxLabelWidth + labelGap;
          }
        }
        self.on('show', recalcLabels);
        recalcLabels();
      }
    });

    var FieldSet = Form.extend({
      Defaults: {
        containerCls: 'fieldset',
        layout: 'flex',
        direction: 'column',
        align: 'stretch',
        flex: 1,
        padding: '25 15 5 15',
        labelGap: 30,
        spacing: 10,
        border: 1
      },
      renderHtml: function () {
        var self = this, layout = self._layout, prefix = self.classPrefix;
        self.preRender();
        layout.preRender(self);
        return '<fieldset id="' + self._id + '" class="' + self.classes + '" hidefocus="1" tabindex="-1">' + (self.settings.title ? '<legend id="' + self._id + '-title" class="' + prefix + 'fieldset-title">' + self.settings.title + '</legend>' : '') + '<div id="' + self._id + '-body" class="' + self.bodyClasses + '">' + (self.settings.html || '') + layout.renderHtml(self) + '</div>' + '</fieldset>';
      }
    });

    var unique$1 = 0;
    var generate = function (prefix) {
      var date = new Date();
      var time = date.getTime();
      var random = Math.floor(Math.random() * 1000000000);
      unique$1++;
      return prefix + '_' + random + unique$1 + String(time);
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

    var cached = function (f) {
      var called = false;
      var r;
      return function () {
        var args = [];
        for (var _i = 0; _i < arguments.length; _i++) {
          args[_i] = arguments[_i];
        }
        if (!called) {
          called = true;
          r = f.apply(null, args);
        }
        return r;
      };
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

    var path = function (parts, scope) {
      var o = scope !== undefined && scope !== null ? scope : Global;
      for (var i = 0; i < parts.length && o !== undefined && o !== null; ++i) {
        o = o[parts[i]];
      }
      return o;
    };
    var resolve = function (p, scope) {
      var parts = p.split('.');
      return path(parts, scope);
    };

    var unsafe = function (name, scope) {
      return resolve(name, scope);
    };
    var getOrDie = function (name, scope) {
      var actual = unsafe(name, scope);
      if (actual === undefined || actual === null) {
        throw new Error(name + ' not available on this browser');
      }
      return actual;
    };
    var Global$1 = { getOrDie: getOrDie };

    var Immutable = function () {
      var fields = [];
      for (var _i = 0; _i < arguments.length; _i++) {
        fields[_i] = arguments[_i];
      }
      return function () {
        var values = [];
        for (var _i = 0; _i < arguments.length; _i++) {
          values[_i] = arguments[_i];
        }
        if (fields.length !== values.length) {
          throw new Error('Wrong number of arguments to struct. Expected "[' + fields.length + ']", got ' + values.length + ' arguments');
        }
        var struct = {};
        each(fields, function (name, i) {
          struct[name] = constant(values[i]);
        });
        return struct;
      };
    };

    var node = function () {
      var f = Global$1.getOrDie('Node');
      return f;
    };
    var compareDocumentPosition = function (a, b, match) {
      return (a.compareDocumentPosition(b) & match) !== 0;
    };
    var documentPositionPreceding = function (a, b) {
      return compareDocumentPosition(a, b, node().DOCUMENT_POSITION_PRECEDING);
    };
    var documentPositionContainedBy = function (a, b) {
      return compareDocumentPosition(a, b, node().DOCUMENT_POSITION_CONTAINED_BY);
    };
    var Node = {
      documentPositionPreceding: documentPositionPreceding,
      documentPositionContainedBy: documentPositionContainedBy
    };

    var firstMatch = function (regexes, s) {
      for (var i = 0; i < regexes.length; i++) {
        var x = regexes[i];
        if (x.test(s)) {
          return x;
        }
      }
      return undefined;
    };
    var find$1 = function (regexes, agent) {
      var r = firstMatch(regexes, agent);
      if (!r) {
        return {
          major: 0,
          minor: 0
        };
      }
      var group = function (i) {
        return Number(agent.replace(r, '$' + i));
      };
      return nu(group(1), group(2));
    };
    var detect = function (versionRegexes, agent) {
      var cleanedAgent = String(agent).toLowerCase();
      if (versionRegexes.length === 0) {
        return unknown();
      }
      return find$1(versionRegexes, cleanedAgent);
    };
    var unknown = function () {
      return nu(0, 0);
    };
    var nu = function (major, minor) {
      return {
        major: major,
        minor: minor
      };
    };
    var Version = {
      nu: nu,
      detect: detect,
      unknown: unknown
    };

    var edge = 'Edge';
    var chrome = 'Chrome';
    var ie = 'IE';
    var opera = 'Opera';
    var firefox = 'Firefox';
    var safari = 'Safari';
    var isBrowser = function (name, current) {
      return function () {
        return current === name;
      };
    };
    var unknown$1 = function () {
      return nu$1({
        current: undefined,
        version: Version.unknown()
      });
    };
    var nu$1 = function (info) {
      var current = info.current;
      var version = info.version;
      return {
        current: current,
        version: version,
        isEdge: isBrowser(edge, current),
        isChrome: isBrowser(chrome, current),
        isIE: isBrowser(ie, current),
        isOpera: isBrowser(opera, current),
        isFirefox: isBrowser(firefox, current),
        isSafari: isBrowser(safari, current)
      };
    };
    var Browser = {
      unknown: unknown$1,
      nu: nu$1,
      edge: constant(edge),
      chrome: constant(chrome),
      ie: constant(ie),
      opera: constant(opera),
      firefox: constant(firefox),
      safari: constant(safari)
    };

    var windows$1 = 'Windows';
    var ios = 'iOS';
    var android = 'Android';
    var linux = 'Linux';
    var osx = 'OSX';
    var solaris = 'Solaris';
    var freebsd = 'FreeBSD';
    var isOS = function (name, current) {
      return function () {
        return current === name;
      };
    };
    var unknown$2 = function () {
      return nu$2({
        current: undefined,
        version: Version.unknown()
      });
    };
    var nu$2 = function (info) {
      var current = info.current;
      var version = info.version;
      return {
        current: current,
        version: version,
        isWindows: isOS(windows$1, current),
        isiOS: isOS(ios, current),
        isAndroid: isOS(android, current),
        isOSX: isOS(osx, current),
        isLinux: isOS(linux, current),
        isSolaris: isOS(solaris, current),
        isFreeBSD: isOS(freebsd, current)
      };
    };
    var OperatingSystem = {
      unknown: unknown$2,
      nu: nu$2,
      windows: constant(windows$1),
      ios: constant(ios),
      android: constant(android),
      linux: constant(linux),
      osx: constant(osx),
      solaris: constant(solaris),
      freebsd: constant(freebsd)
    };

    var DeviceType = function (os, browser, userAgent) {
      var isiPad = os.isiOS() && /ipad/i.test(userAgent) === true;
      var isiPhone = os.isiOS() && !isiPad;
      var isAndroid3 = os.isAndroid() && os.version.major === 3;
      var isAndroid4 = os.isAndroid() && os.version.major === 4;
      var isTablet = isiPad || isAndroid3 || isAndroid4 && /mobile/i.test(userAgent) === true;
      var isTouch = os.isiOS() || os.isAndroid();
      var isPhone = isTouch && !isTablet;
      var iOSwebview = browser.isSafari() && os.isiOS() && /safari/i.test(userAgent) === false;
      return {
        isiPad: constant(isiPad),
        isiPhone: constant(isiPhone),
        isTablet: constant(isTablet),
        isPhone: constant(isPhone),
        isTouch: constant(isTouch),
        isAndroid: os.isAndroid,
        isiOS: os.isiOS,
        isWebView: constant(iOSwebview)
      };
    };

    var detect$1 = function (candidates, userAgent) {
      var agent = String(userAgent).toLowerCase();
      return find(candidates, function (candidate) {
        return candidate.search(agent);
      });
    };
    var detectBrowser = function (browsers, userAgent) {
      return detect$1(browsers, userAgent).map(function (browser) {
        var version = Version.detect(browser.versionRegexes, userAgent);
        return {
          current: browser.name,
          version: version
        };
      });
    };
    var detectOs = function (oses, userAgent) {
      return detect$1(oses, userAgent).map(function (os) {
        var version = Version.detect(os.versionRegexes, userAgent);
        return {
          current: os.name,
          version: version
        };
      });
    };
    var UaString = {
      detectBrowser: detectBrowser,
      detectOs: detectOs
    };

    var contains = function (str, substr) {
      return str.indexOf(substr) !== -1;
    };

    var normalVersionRegex = /.*?version\/\ ?([0-9]+)\.([0-9]+).*/;
    var checkContains = function (target) {
      return function (uastring) {
        return contains(uastring, target);
      };
    };
    var browsers = [
      {
        name: 'Edge',
        versionRegexes: [/.*?edge\/ ?([0-9]+)\.([0-9]+)$/],
        search: function (uastring) {
          return contains(uastring, 'edge/') && contains(uastring, 'chrome') && contains(uastring, 'safari') && contains(uastring, 'applewebkit');
        }
      },
      {
        name: 'Chrome',
        versionRegexes: [
          /.*?chrome\/([0-9]+)\.([0-9]+).*/,
          normalVersionRegex
        ],
        search: function (uastring) {
          return contains(uastring, 'chrome') && !contains(uastring, 'chromeframe');
        }
      },
      {
        name: 'IE',
        versionRegexes: [
          /.*?msie\ ?([0-9]+)\.([0-9]+).*/,
          /.*?rv:([0-9]+)\.([0-9]+).*/
        ],
        search: function (uastring) {
          return contains(uastring, 'msie') || contains(uastring, 'trident');
        }
      },
      {
        name: 'Opera',
        versionRegexes: [
          normalVersionRegex,
          /.*?opera\/([0-9]+)\.([0-9]+).*/
        ],
        search: checkContains('opera')
      },
      {
        name: 'Firefox',
        versionRegexes: [/.*?firefox\/\ ?([0-9]+)\.([0-9]+).*/],
        search: checkContains('firefox')
      },
      {
        name: 'Safari',
        versionRegexes: [
          normalVersionRegex,
          /.*?cpu os ([0-9]+)_([0-9]+).*/
        ],
        search: function (uastring) {
          return (contains(uastring, 'safari') || contains(uastring, 'mobile/')) && contains(uastring, 'applewebkit');
        }
      }
    ];
    var oses = [
      {
        name: 'Windows',
        search: checkContains('win'),
        versionRegexes: [/.*?windows\ nt\ ?([0-9]+)\.([0-9]+).*/]
      },
      {
        name: 'iOS',
        search: function (uastring) {
          return contains(uastring, 'iphone') || contains(uastring, 'ipad');
        },
        versionRegexes: [
          /.*?version\/\ ?([0-9]+)\.([0-9]+).*/,
          /.*cpu os ([0-9]+)_([0-9]+).*/,
          /.*cpu iphone os ([0-9]+)_([0-9]+).*/
        ]
      },
      {
        name: 'Android',
        search: checkContains('android'),
        versionRegexes: [/.*?android\ ?([0-9]+)\.([0-9]+).*/]
      },
      {
        name: 'OSX',
        search: checkContains('os x'),
        versionRegexes: [/.*?os\ x\ ?([0-9]+)_([0-9]+).*/]
      },
      {
        name: 'Linux',
        search: checkContains('linux'),
        versionRegexes: []
      },
      {
        name: 'Solaris',
        search: checkContains('sunos'),
        versionRegexes: []
      },
      {
        name: 'FreeBSD',
        search: checkContains('freebsd'),
        versionRegexes: []
      }
    ];
    var PlatformInfo = {
      browsers: constant(browsers),
      oses: constant(oses)
    };

    var detect$2 = function (userAgent) {
      var browsers = PlatformInfo.browsers();
      var oses = PlatformInfo.oses();
      var browser = UaString.detectBrowser(browsers, userAgent).fold(Browser.unknown, Browser.nu);
      var os = UaString.detectOs(oses, userAgent).fold(OperatingSystem.unknown, OperatingSystem.nu);
      var deviceType = DeviceType(os, browser, userAgent);
      return {
        browser: browser,
        os: os,
        deviceType: deviceType
      };
    };
    var PlatformDetection = { detect: detect$2 };

    var detect$3 = cached(function () {
      var userAgent = domGlobals.navigator.userAgent;
      return PlatformDetection.detect(userAgent);
    });
    var PlatformDetection$1 = { detect: detect$3 };

    var ELEMENT$1 = ELEMENT;
    var DOCUMENT$1 = DOCUMENT;
    var bypassSelector = function (dom) {
      return dom.nodeType !== ELEMENT$1 && dom.nodeType !== DOCUMENT$1 || dom.childElementCount === 0;
    };
    var all = function (selector, scope) {
      var base = scope === undefined ? domGlobals.document : scope.dom();
      return bypassSelector(base) ? [] : map(base.querySelectorAll(selector), Element.fromDom);
    };
    var one = function (selector, scope) {
      var base = scope === undefined ? domGlobals.document : scope.dom();
      return bypassSelector(base) ? Option.none() : Option.from(base.querySelector(selector)).map(Element.fromDom);
    };

    var regularContains = function (e1, e2) {
      var d1 = e1.dom();
      var d2 = e2.dom();
      return d1 === d2 ? false : d1.contains(d2);
    };
    var ieContains = function (e1, e2) {
      return Node.documentPositionContainedBy(e1.dom(), e2.dom());
    };
    var browser = PlatformDetection$1.detect().browser;
    var contains$1 = browser.isIE() ? ieContains : regularContains;

    var spot = Immutable('element', 'offset');

    var descendants = function (scope, selector) {
      return all(selector, scope);
    };

    var trim = global$2.trim;
    var hasContentEditableState = function (value) {
      return function (node) {
        if (node && node.nodeType === 1) {
          if (node.contentEditable === value) {
            return true;
          }
          if (node.getAttribute('data-mce-contenteditable') === value) {
            return true;
          }
        }
        return false;
      };
    };
    var isContentEditableTrue = hasContentEditableState('true');
    var isContentEditableFalse = hasContentEditableState('false');
    var create = function (type, title, url, level, attach) {
      return {
        type: type,
        title: title,
        url: url,
        level: level,
        attach: attach
      };
    };
    var isChildOfContentEditableTrue = function (node) {
      while (node = node.parentNode) {
        var value = node.contentEditable;
        if (value && value !== 'inherit') {
          return isContentEditableTrue(node);
        }
      }
      return false;
    };
    var select = function (selector, root) {
      return map(descendants(Element.fromDom(root), selector), function (element) {
        return element.dom();
      });
    };
    var getElementText = function (elm) {
      return elm.innerText || elm.textContent;
    };
    var getOrGenerateId = function (elm) {
      return elm.id ? elm.id : generate('h');
    };
    var isAnchor = function (elm) {
      return elm && elm.nodeName === 'A' && (elm.id || elm.name);
    };
    var isValidAnchor = function (elm) {
      return isAnchor(elm) && isEditable(elm);
    };
    var isHeader = function (elm) {
      return elm && /^(H[1-6])$/.test(elm.nodeName);
    };
    var isEditable = function (elm) {
      return isChildOfContentEditableTrue(elm) && !isContentEditableFalse(elm);
    };
    var isValidHeader = function (elm) {
      return isHeader(elm) && isEditable(elm);
    };
    var getLevel = function (elm) {
      return isHeader(elm) ? parseInt(elm.nodeName.substr(1), 10) : 0;
    };
    var headerTarget = function (elm) {
      var headerId = getOrGenerateId(elm);
      var attach = function () {
        elm.id = headerId;
      };
      return create('header', getElementText(elm), '#' + headerId, getLevel(elm), attach);
    };
    var anchorTarget = function (elm) {
      var anchorId = elm.id || elm.name;
      var anchorText = getElementText(elm);
      return create('anchor', anchorText ? anchorText : '#' + anchorId, '#' + anchorId, 0, noop);
    };
    var getHeaderTargets = function (elms) {
      return map(filter(elms, isValidHeader), headerTarget);
    };
    var getAnchorTargets = function (elms) {
      return map(filter(elms, isValidAnchor), anchorTarget);
    };
    var getTargetElements = function (elm) {
      var elms = select('h1,h2,h3,h4,h5,h6,a:not([href])', elm);
      return elms;
    };
    var hasTitle = function (target) {
      return trim(target.title).length > 0;
    };
    var find$2 = function (elm) {
      var elms = getTargetElements(elm);
      return filter(getHeaderTargets(elms).concat(getAnchorTargets(elms)), hasTitle);
    };
    var LinkTargets = { find: find$2 };

    var getActiveEditor = function () {
      return window.tinymce ? window.tinymce.activeEditor : global$1.activeEditor;
    };
    var history = {};
    var HISTORY_LENGTH = 5;
    var clearHistory = function () {
      history = {};
    };
    var toMenuItem = function (target) {
      return {
        title: target.title,
        value: {
          title: { raw: target.title },
          url: target.url,
          attach: target.attach
        }
      };
    };
    var toMenuItems = function (targets) {
      return global$2.map(targets, toMenuItem);
    };
    var staticMenuItem = function (title, url) {
      return {
        title: title,
        value: {
          title: title,
          url: url,
          attach: noop
        }
      };
    };
    var isUniqueUrl = function (url, targets) {
      var foundTarget = exists(targets, function (target) {
        return target.url === url;
      });
      return !foundTarget;
    };
    var getSetting = function (editorSettings, name, defaultValue) {
      var value = name in editorSettings ? editorSettings[name] : defaultValue;
      return value === false ? null : value;
    };
    var createMenuItems = function (term, targets, fileType, editorSettings) {
      var separator = { title: '-' };
      var fromHistoryMenuItems = function (history) {
        var historyItems = history.hasOwnProperty(fileType) ? history[fileType] : [];
        var uniqueHistory = filter(historyItems, function (url) {
          return isUniqueUrl(url, targets);
        });
        return global$2.map(uniqueHistory, function (url) {
          return {
            title: url,
            value: {
              title: url,
              url: url,
              attach: noop
            }
          };
        });
      };
      var fromMenuItems = function (type) {
        var filteredTargets = filter(targets, function (target) {
          return target.type === type;
        });
        return toMenuItems(filteredTargets);
      };
      var anchorMenuItems = function () {
        var anchorMenuItems = fromMenuItems('anchor');
        var topAnchor = getSetting(editorSettings, 'anchor_top', '#top');
        var bottomAchor = getSetting(editorSettings, 'anchor_bottom', '#bottom');
        if (topAnchor !== null) {
          anchorMenuItems.unshift(staticMenuItem('<top>', topAnchor));
        }
        if (bottomAchor !== null) {
          anchorMenuItems.push(staticMenuItem('<bottom>', bottomAchor));
        }
        return anchorMenuItems;
      };
      var join = function (items) {
        return foldl(items, function (a, b) {
          var bothEmpty = a.length === 0 || b.length === 0;
          return bothEmpty ? a.concat(b) : a.concat(separator, b);
        }, []);
      };
      if (editorSettings.typeahead_urls === false) {
        return [];
      }
      return fileType === 'file' ? join([
        filterByQuery(term, fromHistoryMenuItems(history)),
        filterByQuery(term, fromMenuItems('header')),
        filterByQuery(term, anchorMenuItems())
      ]) : filterByQuery(term, fromHistoryMenuItems(history));
    };
    var addToHistory = function (url, fileType) {
      var items = history[fileType];
      if (!/^https?/.test(url)) {
        return;
      }
      if (items) {
        if (indexOf(items, url).isNone()) {
          history[fileType] = items.slice(0, HISTORY_LENGTH).concat(url);
        }
      } else {
        history[fileType] = [url];
      }
    };
    var filterByQuery = function (term, menuItems) {
      var lowerCaseTerm = term.toLowerCase();
      var result = global$2.grep(menuItems, function (item) {
        return item.title.toLowerCase().indexOf(lowerCaseTerm) !== -1;
      });
      return result.length === 1 && result[0].title === term ? [] : result;
    };
    var getTitle = function (linkDetails) {
      var title = linkDetails.title;
      return title.raw ? title.raw : title;
    };
    var setupAutoCompleteHandler = function (ctrl, editorSettings, bodyElm, fileType) {
      var autocomplete = function (term) {
        var linkTargets = LinkTargets.find(bodyElm);
        var menuItems = createMenuItems(term, linkTargets, fileType, editorSettings);
        ctrl.showAutoComplete(menuItems, term);
      };
      ctrl.on('autocomplete', function () {
        autocomplete(ctrl.value());
      });
      ctrl.on('selectitem', function (e) {
        var linkDetails = e.value;
        ctrl.value(linkDetails.url);
        var title = getTitle(linkDetails);
        if (fileType === 'image') {
          ctrl.fire('change', {
            meta: {
              alt: title,
              attach: linkDetails.attach
            }
          });
        } else {
          ctrl.fire('change', {
            meta: {
              text: title,
              attach: linkDetails.attach
            }
          });
        }
        ctrl.focus();
      });
      ctrl.on('click', function (e) {
        if (ctrl.value().length === 0 && e.target.nodeName === 'INPUT') {
          autocomplete('');
        }
      });
      ctrl.on('PostRender', function () {
        ctrl.getRoot().on('submit', function (e) {
          if (!e.isDefaultPrevented()) {
            addToHistory(ctrl.value(), fileType);
          }
        });
      });
    };
    var statusToUiState = function (result) {
      var status = result.status, message = result.message;
      if (status === 'valid') {
        return {
          status: 'ok',
          message: message
        };
      } else if (status === 'unknown') {
        return {
          status: 'warn',
          message: message
        };
      } else if (status === 'invalid') {
        return {
          status: 'warn',
          message: message
        };
      } else {
        return {
          status: 'none',
          message: ''
        };
      }
    };
    var setupLinkValidatorHandler = function (ctrl, editorSettings, fileType) {
      var validatorHandler = editorSettings.filepicker_validator_handler;
      if (validatorHandler) {
        var validateUrl_1 = function (url) {
          if (url.length === 0) {
            ctrl.statusLevel('none');
            return;
          }
          validatorHandler({
            url: url,
            type: fileType
          }, function (result) {
            var uiState = statusToUiState(result);
            ctrl.statusMessage(uiState.message);
            ctrl.statusLevel(uiState.status);
          });
        };
        ctrl.state.on('change:value', function (e) {
          validateUrl_1(e.value);
        });
      }
    };
    var FilePicker = ComboBox.extend({
      Statics: { clearHistory: clearHistory },
      init: function (settings) {
        var self = this, editor = getActiveEditor(), editorSettings = editor.settings;
        var actionCallback, fileBrowserCallback, fileBrowserCallbackTypes;
        var fileType = settings.filetype;
        settings.spellcheck = false;
        fileBrowserCallbackTypes = editorSettings.file_picker_types || editorSettings.file_browser_callback_types;
        if (fileBrowserCallbackTypes) {
          fileBrowserCallbackTypes = global$2.makeMap(fileBrowserCallbackTypes, /[, ]/);
        }
        if (!fileBrowserCallbackTypes || fileBrowserCallbackTypes[fileType]) {
          fileBrowserCallback = editorSettings.file_picker_callback;
          if (fileBrowserCallback && (!fileBrowserCallbackTypes || fileBrowserCallbackTypes[fileType])) {
            actionCallback = function () {
              var meta = self.fire('beforecall').meta;
              meta = global$2.extend({ filetype: fileType }, meta);
              fileBrowserCallback.call(editor, function (value, meta) {
                self.value(value).fire('change', { meta: meta });
              }, self.value(), meta);
            };
          } else {
            fileBrowserCallback = editorSettings.file_browser_callback;
            if (fileBrowserCallback && (!fileBrowserCallbackTypes || fileBrowserCallbackTypes[fileType])) {
              actionCallback = function () {
                fileBrowserCallback(self.getEl('inp').id, self.value(), fileType, window);
              };
            }
          }
        }
        if (actionCallback) {
          settings.icon = 'browse';
          settings.onaction = actionCallback;
        }
        self._super(settings);
        self.classes.add('filepicker');
        setupAutoCompleteHandler(self, editorSettings, editor.getBody(), fileType);
        setupLinkValidatorHandler(self, editorSettings, fileType);
      }
    });

    var FitLayout = AbsoluteLayout.extend({
      recalc: function (container) {
        var contLayoutRect = container.layoutRect(), paddingBox = container.paddingBox;
        container.items().filter(':visible').each(function (ctrl) {
          ctrl.layoutRect({
            x: paddingBox.left,
            y: paddingBox.top,
            w: contLayoutRect.innerW - paddingBox.right - paddingBox.left,
            h: contLayoutRect.innerH - paddingBox.top - paddingBox.bottom
          });
          if (ctrl.recalc) {
            ctrl.recalc();
          }
        });
      }
    });

    var FlexLayout = AbsoluteLayout.extend({
      recalc: function (container) {
        var i, l, items, contLayoutRect, contPaddingBox, contSettings, align, pack, spacing, totalFlex, availableSpace, direction;
        var ctrl, ctrlLayoutRect, ctrlSettings, flex;
        var maxSizeItems = [];
        var size, maxSize, ratio, rect, pos, maxAlignEndPos;
        var sizeName, minSizeName, posName, maxSizeName, beforeName, innerSizeName, deltaSizeName, contentSizeName;
        var alignAxisName, alignInnerSizeName, alignSizeName, alignMinSizeName, alignBeforeName, alignAfterName;
        var alignDeltaSizeName, alignContentSizeName;
        var max = Math.max, min = Math.min;
        items = container.items().filter(':visible');
        contLayoutRect = container.layoutRect();
        contPaddingBox = container.paddingBox;
        contSettings = container.settings;
        direction = container.isRtl() ? contSettings.direction || 'row-reversed' : contSettings.direction;
        align = contSettings.align;
        pack = container.isRtl() ? contSettings.pack || 'end' : contSettings.pack;
        spacing = contSettings.spacing || 0;
        if (direction === 'row-reversed' || direction === 'column-reverse') {
          items = items.set(items.toArray().reverse());
          direction = direction.split('-')[0];
        }
        if (direction === 'column') {
          posName = 'y';
          sizeName = 'h';
          minSizeName = 'minH';
          maxSizeName = 'maxH';
          innerSizeName = 'innerH';
          beforeName = 'top';
          deltaSizeName = 'deltaH';
          contentSizeName = 'contentH';
          alignBeforeName = 'left';
          alignSizeName = 'w';
          alignAxisName = 'x';
          alignInnerSizeName = 'innerW';
          alignMinSizeName = 'minW';
          alignAfterName = 'right';
          alignDeltaSizeName = 'deltaW';
          alignContentSizeName = 'contentW';
        } else {
          posName = 'x';
          sizeName = 'w';
          minSizeName = 'minW';
          maxSizeName = 'maxW';
          innerSizeName = 'innerW';
          beforeName = 'left';
          deltaSizeName = 'deltaW';
          contentSizeName = 'contentW';
          alignBeforeName = 'top';
          alignSizeName = 'h';
          alignAxisName = 'y';
          alignInnerSizeName = 'innerH';
          alignMinSizeName = 'minH';
          alignAfterName = 'bottom';
          alignDeltaSizeName = 'deltaH';
          alignContentSizeName = 'contentH';
        }
        availableSpace = contLayoutRect[innerSizeName] - contPaddingBox[beforeName] - contPaddingBox[beforeName];
        maxAlignEndPos = totalFlex = 0;
        for (i = 0, l = items.length; i < l; i++) {
          ctrl = items[i];
          ctrlLayoutRect = ctrl.layoutRect();
          ctrlSettings = ctrl.settings;
          flex = ctrlSettings.flex;
          availableSpace -= i < l - 1 ? spacing : 0;
          if (flex > 0) {
            totalFlex += flex;
            if (ctrlLayoutRect[maxSizeName]) {
              maxSizeItems.push(ctrl);
            }
            ctrlLayoutRect.flex = flex;
          }
          availableSpace -= ctrlLayoutRect[minSizeName];
          size = contPaddingBox[alignBeforeName] + ctrlLayoutRect[alignMinSizeName] + contPaddingBox[alignAfterName];
          if (size > maxAlignEndPos) {
            maxAlignEndPos = size;
          }
        }
        rect = {};
        if (availableSpace < 0) {
          rect[minSizeName] = contLayoutRect[minSizeName] - availableSpace + contLayoutRect[deltaSizeName];
        } else {
          rect[minSizeName] = contLayoutRect[innerSizeName] - availableSpace + contLayoutRect[deltaSizeName];
        }
        rect[alignMinSizeName] = maxAlignEndPos + contLayoutRect[alignDeltaSizeName];
        rect[contentSizeName] = contLayoutRect[innerSizeName] - availableSpace;
        rect[alignContentSizeName] = maxAlignEndPos;
        rect.minW = min(rect.minW, contLayoutRect.maxW);
        rect.minH = min(rect.minH, contLayoutRect.maxH);
        rect.minW = max(rect.minW, contLayoutRect.startMinWidth);
        rect.minH = max(rect.minH, contLayoutRect.startMinHeight);
        if (contLayoutRect.autoResize && (rect.minW !== contLayoutRect.minW || rect.minH !== contLayoutRect.minH)) {
          rect.w = rect.minW;
          rect.h = rect.minH;
          container.layoutRect(rect);
          this.recalc(container);
          if (container._lastRect === null) {
            var parentCtrl = container.parent();
            if (parentCtrl) {
              parentCtrl._lastRect = null;
              parentCtrl.recalc();
            }
          }
          return;
        }
        ratio = availableSpace / totalFlex;
        for (i = 0, l = maxSizeItems.length; i < l; i++) {
          ctrl = maxSizeItems[i];
          ctrlLayoutRect = ctrl.layoutRect();
          maxSize = ctrlLayoutRect[maxSizeName];
          size = ctrlLayoutRect[minSizeName] + ctrlLayoutRect.flex * ratio;
          if (size > maxSize) {
            availableSpace -= ctrlLayoutRect[maxSizeName] - ctrlLayoutRect[minSizeName];
            totalFlex -= ctrlLayoutRect.flex;
            ctrlLayoutRect.flex = 0;
            ctrlLayoutRect.maxFlexSize = maxSize;
          } else {
            ctrlLayoutRect.maxFlexSize = 0;
          }
        }
        ratio = availableSpace / totalFlex;
        pos = contPaddingBox[beforeName];
        rect = {};
        if (totalFlex === 0) {
          if (pack === 'end') {
            pos = availableSpace + contPaddingBox[beforeName];
          } else if (pack === 'center') {
            pos = Math.round(contLayoutRect[innerSizeName] / 2 - (contLayoutRect[innerSizeName] - availableSpace) / 2) + contPaddingBox[beforeName];
            if (pos < 0) {
              pos = contPaddingBox[beforeName];
            }
          } else if (pack === 'justify') {
            pos = contPaddingBox[beforeName];
            spacing = Math.floor(availableSpace / (items.length - 1));
          }
        }
        rect[alignAxisName] = contPaddingBox[alignBeforeName];
        for (i = 0, l = items.length; i < l; i++) {
          ctrl = items[i];
          ctrlLayoutRect = ctrl.layoutRect();
          size = ctrlLayoutRect.maxFlexSize || ctrlLayoutRect[minSizeName];
          if (align === 'center') {
            rect[alignAxisName] = Math.round(contLayoutRect[alignInnerSizeName] / 2 - ctrlLayoutRect[alignSizeName] / 2);
          } else if (align === 'stretch') {
            rect[alignSizeName] = max(ctrlLayoutRect[alignMinSizeName] || 0, contLayoutRect[alignInnerSizeName] - contPaddingBox[alignBeforeName] - contPaddingBox[alignAfterName]);
            rect[alignAxisName] = contPaddingBox[alignBeforeName];
          } else if (align === 'end') {
            rect[alignAxisName] = contLayoutRect[alignInnerSizeName] - ctrlLayoutRect[alignSizeName] - contPaddingBox.top;
          }
          if (ctrlLayoutRect.flex > 0) {
            size += ctrlLayoutRect.flex * ratio;
          }
          rect[sizeName] = size;
          rect[posName] = pos;
          ctrl.layoutRect(rect);
          if (ctrl.recalc) {
            ctrl.recalc();
          }
          pos += size + spacing;
        }
      }
    });

    var FlowLayout = Layout.extend({
      Defaults: {
        containerClass: 'flow-layout',
        controlClass: 'flow-layout-item',
        endClass: 'break'
      },
      recalc: function (container) {
        container.items().filter(':visible').each(function (ctrl) {
          if (ctrl.recalc) {
            ctrl.recalc();
          }
        });
      },
      isNative: function () {
        return true;
      }
    });

    var descendant = function (scope, selector) {
      return one(selector, scope);
    };

    var toggleFormat = function (editor, fmt) {
      return function () {
        editor.execCommand('mceToggleFormat', false, fmt);
      };
    };
    var addFormatChangedListener = function (editor, name, changed) {
      var handler = function (state) {
        changed(state, name);
      };
      if (editor.formatter) {
        editor.formatter.formatChanged(name, handler);
      } else {
        editor.on('init', function () {
          editor.formatter.formatChanged(name, handler);
        });
      }
    };
    var postRenderFormatToggle = function (editor, name) {
      return function (e) {
        addFormatChangedListener(editor, name, function (state) {
          e.control.active(state);
        });
      };
    };

    var register = function (editor) {
      var alignFormats = [
        'alignleft',
        'aligncenter',
        'alignright',
        'alignjustify'
      ];
      var defaultAlign = 'alignleft';
      var alignMenuItems = [
        {
          text: 'Left',
          icon: 'alignleft',
          onclick: toggleFormat(editor, 'alignleft')
        },
        {
          text: 'Center',
          icon: 'aligncenter',
          onclick: toggleFormat(editor, 'aligncenter')
        },
        {
          text: 'Right',
          icon: 'alignright',
          onclick: toggleFormat(editor, 'alignright')
        },
        {
          text: 'Justify',
          icon: 'alignjustify',
          onclick: toggleFormat(editor, 'alignjustify')
        }
      ];
      editor.addMenuItem('align', {
        text: 'Align',
        menu: alignMenuItems
      });
      editor.addButton('align', {
        type: 'menubutton',
        icon: defaultAlign,
        menu: alignMenuItems,
        onShowMenu: function (e) {
          var menu = e.control.menu;
          global$2.each(alignFormats, function (formatName, idx) {
            menu.items().eq(idx).each(function (item) {
              return item.active(editor.formatter.match(formatName));
            });
          });
        },
        onPostRender: function (e) {
          var ctrl = e.control;
          global$2.each(alignFormats, function (formatName, idx) {
            addFormatChangedListener(editor, formatName, function (state) {
              ctrl.icon(defaultAlign);
              if (state) {
                ctrl.icon(formatName);
              }
            });
          });
        }
      });
      global$2.each({
        alignleft: [
          'Align left',
          'JustifyLeft'
        ],
        aligncenter: [
          'Align center',
          'JustifyCenter'
        ],
        alignright: [
          'Align right',
          'JustifyRight'
        ],
        alignjustify: [
          'Justify',
          'JustifyFull'
        ],
        alignnone: [
          'No alignment',
          'JustifyNone'
        ]
      }, function (item, name) {
        editor.addButton(name, {
          active: false,
          tooltip: item[0],
          cmd: item[1],
          onPostRender: postRenderFormatToggle(editor, name)
        });
      });
    };
    var Align = { register: register };

    var getFirstFont = function (fontFamily) {
      return fontFamily ? fontFamily.split(',')[0] : '';
    };
    var findMatchingValue = function (items, fontFamily) {
      var font = fontFamily ? fontFamily.toLowerCase() : '';
      var value;
      global$2.each(items, function (item) {
        if (item.value.toLowerCase() === font) {
          value = item.value;
        }
      });
      global$2.each(items, function (item) {
        if (!value && getFirstFont(item.value).toLowerCase() === getFirstFont(font).toLowerCase()) {
          value = item.value;
        }
      });
      return value;
    };
    var createFontNameListBoxChangeHandler = function (editor, items) {
      return function () {
        var self = this;
        self.state.set('value', null);
        editor.on('init nodeChange', function (e) {
          var fontFamily = editor.queryCommandValue('FontName');
          var match = findMatchingValue(items, fontFamily);
          self.value(match ? match : null);
          if (!match && fontFamily) {
            self.text(getFirstFont(fontFamily));
          }
        });
      };
    };
    var createFormats = function (formats) {
      formats = formats.replace(/;$/, '').split(';');
      var i = formats.length;
      while (i--) {
        formats[i] = formats[i].split('=');
      }
      return formats;
    };
    var getFontItems = function (editor) {
      var defaultFontsFormats = 'Andale Mono=andale mono,monospace;' + 'Arial=arial,helvetica,sans-serif;' + 'Arial Black=arial black,sans-serif;' + 'Book Antiqua=book antiqua,palatino,serif;' + 'Comic Sans MS=comic sans ms,sans-serif;' + 'Courier New=courier new,courier,monospace;' + 'Georgia=georgia,palatino,serif;' + 'Helvetica=helvetica,arial,sans-serif;' + 'Impact=impact,sans-serif;' + 'Symbol=symbol;' + 'Tahoma=tahoma,arial,helvetica,sans-serif;' + 'Terminal=terminal,monaco,monospace;' + 'Times New Roman=times new roman,times,serif;' + 'Trebuchet MS=trebuchet ms,geneva,sans-serif;' + 'Verdana=verdana,geneva,sans-serif;' + 'Webdings=webdings;' + 'Wingdings=wingdings,zapf dingbats';
      var fonts = createFormats(editor.settings.font_formats || defaultFontsFormats);
      return global$2.map(fonts, function (font) {
        return {
          text: { raw: font[0] },
          value: font[1],
          textStyle: font[1].indexOf('dings') === -1 ? 'font-family:' + font[1] : ''
        };
      });
    };
    var registerButtons = function (editor) {
      editor.addButton('fontselect', function () {
        var items = getFontItems(editor);
        return {
          type: 'listbox',
          text: 'Font Family',
          tooltip: 'Font Family',
          values: items,
          fixedWidth: true,
          onPostRender: createFontNameListBoxChangeHandler(editor, items),
          onselect: function (e) {
            if (e.control.settings.value) {
              editor.execCommand('FontName', false, e.control.settings.value);
            }
          }
        };
      });
    };
    var register$1 = function (editor) {
      registerButtons(editor);
    };
    var FontSelect = { register: register$1 };

    var round = function (number, precision) {
      var factor = Math.pow(10, precision);
      return Math.round(number * factor) / factor;
    };
    var toPt = function (fontSize, precision) {
      if (/[0-9.]+px$/.test(fontSize)) {
        return round(parseInt(fontSize, 10) * 72 / 96, precision || 0) + 'pt';
      }
      return fontSize;
    };
    var findMatchingValue$1 = function (items, pt, px) {
      var value;
      global$2.each(items, function (item) {
        if (item.value === px) {
          value = px;
        } else if (item.value === pt) {
          value = pt;
        }
      });
      return value;
    };
    var createFontSizeListBoxChangeHandler = function (editor, items) {
      return function () {
        var self = this;
        editor.on('init nodeChange', function (e) {
          var px, pt, precision, match;
          px = editor.queryCommandValue('FontSize');
          if (px) {
            for (precision = 3; !match && precision >= 0; precision--) {
              pt = toPt(px, precision);
              match = findMatchingValue$1(items, pt, px);
            }
          }
          self.value(match ? match : null);
          if (!match) {
            self.text(pt);
          }
        });
      };
    };
    var getFontSizeItems = function (editor) {
      var defaultFontsizeFormats = '8pt 10pt 12pt 14pt 18pt 24pt 36pt';
      var fontsizeFormats = editor.settings.fontsize_formats || defaultFontsizeFormats;
      return global$2.map(fontsizeFormats.split(' '), function (item) {
        var text = item, value = item;
        var values = item.split('=');
        if (values.length > 1) {
          text = values[0];
          value = values[1];
        }
        return {
          text: text,
          value: value
        };
      });
    };
    var registerButtons$1 = function (editor) {
      editor.addButton('fontsizeselect', function () {
        var items = getFontSizeItems(editor);
        return {
          type: 'listbox',
          text: 'Font Sizes',
          tooltip: 'Font Sizes',
          values: items,
          fixedWidth: true,
          onPostRender: createFontSizeListBoxChangeHandler(editor, items),
          onclick: function (e) {
            if (e.control.settings.value) {
              editor.execCommand('FontSize', false, e.control.settings.value);
            }
          }
        };
      });
    };
    var register$2 = function (editor) {
      registerButtons$1(editor);
    };
    var FontSizeSelect = { register: register$2 };

    var hideMenuObjects = function (editor, menu) {
      var count = menu.length;
      global$2.each(menu, function (item) {
        if (item.menu) {
          item.hidden = hideMenuObjects(editor, item.menu) === 0;
        }
        var formatName = item.format;
        if (formatName) {
          item.hidden = !editor.formatter.canApply(formatName);
        }
        if (item.hidden) {
          count--;
        }
      });
      return count;
    };
    var hideFormatMenuItems = function (editor, menu) {
      var count = menu.items().length;
      menu.items().each(function (item) {
        if (item.menu) {
          item.visible(hideFormatMenuItems(editor, item.menu) > 0);
        }
        if (!item.menu && item.settings.menu) {
          item.visible(hideMenuObjects(editor, item.settings.menu) > 0);
        }
        var formatName = item.settings.format;
        if (formatName) {
          item.visible(editor.formatter.canApply(formatName));
        }
        if (!item.visible()) {
          count--;
        }
      });
      return count;
    };
    var createFormatMenu = function (editor) {
      var count = 0;
      var newFormats = [];
      var defaultStyleFormats = [
        {
          title: 'Headings',
          items: [
            {
              title: 'Heading 1',
              format: 'h1'
            },
            {
              title: 'Heading 2',
              format: 'h2'
            },
            {
              title: 'Heading 3',
              format: 'h3'
            },
            {
              title: 'Heading 4',
              format: 'h4'
            },
            {
              title: 'Heading 5',
              format: 'h5'
            },
            {
              title: 'Heading 6',
              format: 'h6'
            }
          ]
        },
        {
          title: 'Inline',
          items: [
            {
              title: 'Bold',
              icon: 'bold',
              format: 'bold'
            },
            {
              title: 'Italic',
              icon: 'italic',
              format: 'italic'
            },
            {
              title: 'Underline',
              icon: 'underline',
              format: 'underline'
            },
            {
              title: 'Strikethrough',
              icon: 'strikethrough',
              format: 'strikethrough'
            },
            {
              title: 'Superscript',
              icon: 'superscript',
              format: 'superscript'
            },
            {
              title: 'Subscript',
              icon: 'subscript',
              format: 'subscript'
            },
            {
              title: 'Code',
              icon: 'code',
              format: 'code'
            }
          ]
        },
        {
          title: 'Blocks',
          items: [
            {
              title: 'Paragraph',
              format: 'p'
            },
            {
              title: 'Blockquote',
              format: 'blockquote'
            },
            {
              title: 'Div',
              format: 'div'
            },
            {
              title: 'Pre',
              format: 'pre'
            }
          ]
        },
        {
          title: 'Alignment',
          items: [
            {
              title: 'Left',
              icon: 'alignleft',
              format: 'alignleft'
            },
            {
              title: 'Center',
              icon: 'aligncenter',
              format: 'aligncenter'
            },
            {
              title: 'Right',
              icon: 'alignright',
              format: 'alignright'
            },
            {
              title: 'Justify',
              icon: 'alignjustify',
              format: 'alignjustify'
            }
          ]
        }
      ];
      var createMenu = function (formats) {
        var menu = [];
        if (!formats) {
          return;
        }
        global$2.each(formats, function (format) {
          var menuItem = {
            text: format.title,
            icon: format.icon
          };
          if (format.items) {
            menuItem.menu = createMenu(format.items);
          } else {
            var formatName = format.format || 'custom' + count++;
            if (!format.format) {
              format.name = formatName;
              newFormats.push(format);
            }
            menuItem.format = formatName;
            menuItem.cmd = format.cmd;
          }
          menu.push(menuItem);
        });
        return menu;
      };
      var createStylesMenu = function () {
        var menu;
        if (editor.settings.style_formats_merge) {
          if (editor.settings.style_formats) {
            menu = createMenu(defaultStyleFormats.concat(editor.settings.style_formats));
          } else {
            menu = createMenu(defaultStyleFormats);
          }
        } else {
          menu = createMenu(editor.settings.style_formats || defaultStyleFormats);
        }
        return menu;
      };
      editor.on('init', function () {
        global$2.each(newFormats, function (format) {
          editor.formatter.register(format.name, format);
        });
      });
      return {
        type: 'menu',
        items: createStylesMenu(),
        onPostRender: function (e) {
          editor.fire('renderFormatsMenu', { control: e.control });
        },
        itemDefaults: {
          preview: true,
          textStyle: function () {
            if (this.settings.format) {
              return editor.formatter.getCssText(this.settings.format);
            }
          },
          onPostRender: function () {
            var self = this;
            self.parent().on('show', function () {
              var formatName, command;
              formatName = self.settings.format;
              if (formatName) {
                self.disabled(!editor.formatter.canApply(formatName));
                self.active(editor.formatter.match(formatName));
              }
              command = self.settings.cmd;
              if (command) {
                self.active(editor.queryCommandState(command));
              }
            });
          },
          onclick: function () {
            if (this.settings.format) {
              toggleFormat(editor, this.settings.format)();
            }
            if (this.settings.cmd) {
              editor.execCommand(this.settings.cmd);
            }
          }
        }
      };
    };
    var registerMenuItems = function (editor, formatMenu) {
      editor.addMenuItem('formats', {
        text: 'Formats',
        menu: formatMenu
      });
    };
    var registerButtons$2 = function (editor, formatMenu) {
      editor.addButton('styleselect', {
        type: 'menubutton',
        text: 'Formats',
        menu: formatMenu,
        onShowMenu: function () {
          if (editor.settings.style_formats_autohide) {
            hideFormatMenuItems(editor, this.menu);
          }
        }
      });
    };
    var register$3 = function (editor) {
      var formatMenu = createFormatMenu(editor);
      registerMenuItems(editor, formatMenu);
      registerButtons$2(editor, formatMenu);
    };
    var Formats = { register: register$3 };

    var defaultBlocks = 'Paragraph=p;' + 'Heading 1=h1;' + 'Heading 2=h2;' + 'Heading 3=h3;' + 'Heading 4=h4;' + 'Heading 5=h5;' + 'Heading 6=h6;' + 'Preformatted=pre';
    var createFormats$1 = function (formats) {
      formats = formats.replace(/;$/, '').split(';');
      var i = formats.length;
      while (i--) {
        formats[i] = formats[i].split('=');
      }
      return formats;
    };
    var createListBoxChangeHandler = function (editor, items, formatName) {
      return function () {
        var self = this;
        editor.on('nodeChange', function (e) {
          var formatter = editor.formatter;
          var value = null;
          global$2.each(e.parents, function (node) {
            global$2.each(items, function (item) {
              if (formatName) {
                if (formatter.matchNode(node, formatName, { value: item.value })) {
                  value = item.value;
                }
              } else {
                if (formatter.matchNode(node, item.value)) {
                  value = item.value;
                }
              }
              if (value) {
                return false;
              }
            });
            if (value) {
              return false;
            }
          });
          self.value(value);
        });
      };
    };
    var lazyFormatSelectBoxItems = function (editor, blocks) {
      return function () {
        var items = [];
        global$2.each(blocks, function (block) {
          items.push({
            text: block[0],
            value: block[1],
            textStyle: function () {
              return editor.formatter.getCssText(block[1]);
            }
          });
        });
        return {
          type: 'listbox',
          text: blocks[0][0],
          values: items,
          fixedWidth: true,
          onselect: function (e) {
            if (e.control) {
              var fmt = e.control.value();
              toggleFormat(editor, fmt)();
            }
          },
          onPostRender: createListBoxChangeHandler(editor, items)
        };
      };
    };
    var buildMenuItems = function (editor, blocks) {
      return global$2.map(blocks, function (block) {
        return {
          text: block[0],
          onclick: toggleFormat(editor, block[1]),
          textStyle: function () {
            return editor.formatter.getCssText(block[1]);
          }
        };
      });
    };
    var register$4 = function (editor) {
      var blocks = createFormats$1(editor.settings.block_formats || defaultBlocks);
      editor.addMenuItem('blockformats', {
        text: 'Blocks',
        menu: buildMenuItems(editor, blocks)
      });
      editor.addButton('formatselect', lazyFormatSelectBoxItems(editor, blocks));
    };
    var FormatSelect = { register: register$4 };

    var createCustomMenuItems = function (editor, names) {
      var items, nameList;
      if (typeof names === 'string') {
        nameList = names.split(' ');
      } else if (global$2.isArray(names)) {
        return flatten(global$2.map(names, function (names) {
          return createCustomMenuItems(editor, names);
        }));
      }
      items = global$2.grep(nameList, function (name) {
        return name === '|' || name in editor.menuItems;
      });
      return global$2.map(items, function (name) {
        return name === '|' ? { text: '-' } : editor.menuItems[name];
      });
    };
    var isSeparator$1 = function (menuItem) {
      return menuItem && menuItem.text === '-';
    };
    var trimMenuItems = function (menuItems) {
      var menuItems2 = filter(menuItems, function (menuItem, i) {
        return !isSeparator$1(menuItem) || !isSeparator$1(menuItems[i - 1]);
      });
      return filter(menuItems2, function (menuItem, i) {
        return !isSeparator$1(menuItem) || i > 0 && i < menuItems2.length - 1;
      });
    };
    var createContextMenuItems = function (editor, context) {
      var outputMenuItems = [{ text: '-' }];
      var menuItems = global$2.grep(editor.menuItems, function (menuItem) {
        return menuItem.context === context;
      });
      global$2.each(menuItems, function (menuItem) {
        if (menuItem.separator === 'before') {
          outputMenuItems.push({ text: '|' });
        }
        if (menuItem.prependToContext) {
          outputMenuItems.unshift(menuItem);
        } else {
          outputMenuItems.push(menuItem);
        }
        if (menuItem.separator === 'after') {
          outputMenuItems.push({ text: '|' });
        }
      });
      return outputMenuItems;
    };
    var createInsertMenu = function (editor) {
      var insertButtonItems = editor.settings.insert_button_items;
      if (insertButtonItems) {
        return trimMenuItems(createCustomMenuItems(editor, insertButtonItems));
      } else {
        return trimMenuItems(createContextMenuItems(editor, 'insert'));
      }
    };
    var registerButtons$3 = function (editor) {
      editor.addButton('insert', {
        type: 'menubutton',
        icon: 'insert',
        menu: [],
        oncreatemenu: function () {
          this.menu.add(createInsertMenu(editor));
          this.menu.renderNew();
        }
      });
    };
    var register$5 = function (editor) {
      registerButtons$3(editor);
    };
    var InsertButton = { register: register$5 };

    var registerFormatButtons = function (editor) {
      global$2.each({
        bold: 'Bold',
        italic: 'Italic',
        underline: 'Underline',
        strikethrough: 'Strikethrough',
        subscript: 'Subscript',
        superscript: 'Superscript'
      }, function (text, name) {
        editor.addButton(name, {
          active: false,
          tooltip: text,
          onPostRender: postRenderFormatToggle(editor, name),
          onclick: toggleFormat(editor, name)
        });
      });
    };
    var registerCommandButtons = function (editor) {
      global$2.each({
        outdent: [
          'Decrease indent',
          'Outdent'
        ],
        indent: [
          'Increase indent',
          'Indent'
        ],
        cut: [
          'Cut',
          'Cut'
        ],
        copy: [
          'Copy',
          'Copy'
        ],
        paste: [
          'Paste',
          'Paste'
        ],
        help: [
          'Help',
          'mceHelp'
        ],
        selectall: [
          'Select all',
          'SelectAll'
        ],
        visualaid: [
          'Visual aids',
          'mceToggleVisualAid'
        ],
        newdocument: [
          'New document',
          'mceNewDocument'
        ],
        removeformat: [
          'Clear formatting',
          'RemoveFormat'
        ],
        remove: [
          'Remove',
          'Delete'
        ]
      }, function (item, name) {
        editor.addButton(name, {
          tooltip: item[0],
          cmd: item[1]
        });
      });
    };
    var registerCommandToggleButtons = function (editor) {
      global$2.each({
        blockquote: [
          'Blockquote',
          'mceBlockQuote'
        ],
        subscript: [
          'Subscript',
          'Subscript'
        ],
        superscript: [
          'Superscript',
          'Superscript'
        ]
      }, function (item, name) {
        editor.addButton(name, {
          active: false,
          tooltip: item[0],
          cmd: item[1],
          onPostRender: postRenderFormatToggle(editor, name)
        });
      });
    };
    var registerButtons$4 = function (editor) {
      registerFormatButtons(editor);
      registerCommandButtons(editor);
      registerCommandToggleButtons(editor);
    };
    var registerMenuItems$1 = function (editor) {
      global$2.each({
        bold: [
          'Bold',
          'Bold',
          'Meta+B'
        ],
        italic: [
          'Italic',
          'Italic',
          'Meta+I'
        ],
        underline: [
          'Underline',
          'Underline',
          'Meta+U'
        ],
        strikethrough: [
          'Strikethrough',
          'Strikethrough'
        ],
        subscript: [
          'Subscript',
          'Subscript'
        ],
        superscript: [
          'Superscript',
          'Superscript'
        ],
        removeformat: [
          'Clear formatting',
          'RemoveFormat'
        ],
        newdocument: [
          'New document',
          'mceNewDocument'
        ],
        cut: [
          'Cut',
          'Cut',
          'Meta+X'
        ],
        copy: [
          'Copy',
          'Copy',
          'Meta+C'
        ],
        paste: [
          'Paste',
          'Paste',
          'Meta+V'
        ],
        selectall: [
          'Select all',
          'SelectAll',
          'Meta+A'
        ]
      }, function (item, name) {
        editor.addMenuItem(name, {
          text: item[0],
          icon: name,
          shortcut: item[2],
          cmd: item[1]
        });
      });
      editor.addMenuItem('codeformat', {
        text: 'Code',
        icon: 'code',
        onclick: toggleFormat(editor, 'code')
      });
    };
    var register$6 = function (editor) {
      registerButtons$4(editor);
      registerMenuItems$1(editor);
    };
    var SimpleControls = { register: register$6 };

    var toggleUndoRedoState = function (editor, type) {
      return function () {
        var self = this;
        var checkState = function () {
          var typeFn = type === 'redo' ? 'hasRedo' : 'hasUndo';
          return editor.undoManager ? editor.undoManager[typeFn]() : false;
        };
        self.disabled(!checkState());
        editor.on('Undo Redo AddUndo TypingUndo ClearUndos SwitchMode', function () {
          self.disabled(editor.readonly || !checkState());
        });
      };
    };
    var registerMenuItems$2 = function (editor) {
      editor.addMenuItem('undo', {
        text: 'Undo',
        icon: 'undo',
        shortcut: 'Meta+Z',
        onPostRender: toggleUndoRedoState(editor, 'undo'),
        cmd: 'undo'
      });
      editor.addMenuItem('redo', {
        text: 'Redo',
        icon: 'redo',
        shortcut: 'Meta+Y',
        onPostRender: toggleUndoRedoState(editor, 'redo'),
        cmd: 'redo'
      });
    };
    var registerButtons$5 = function (editor) {
      editor.addButton('undo', {
        tooltip: 'Undo',
        onPostRender: toggleUndoRedoState(editor, 'undo'),
        cmd: 'undo'
      });
      editor.addButton('redo', {
        tooltip: 'Redo',
        onPostRender: toggleUndoRedoState(editor, 'redo'),
        cmd: 'redo'
      });
    };
    var register$7 = function (editor) {
      registerMenuItems$2(editor);
      registerButtons$5(editor);
    };
    var UndoRedo = { register: register$7 };

    var toggleVisualAidState = function (editor) {
      return function () {
        var self = this;
        editor.on('VisualAid', function (e) {
          self.active(e.hasVisual);
        });
        self.active(editor.hasVisual);
      };
    };
    var registerMenuItems$3 = function (editor) {
      editor.addMenuItem('visualaid', {
        text: 'Visual aids',
        selectable: true,
        onPostRender: toggleVisualAidState(editor),
        cmd: 'mceToggleVisualAid'
      });
    };
    var register$8 = function (editor) {
      registerMenuItems$3(editor);
    };
    var VisualAid = { register: register$8 };

    var setupEnvironment = function () {
      Widget.tooltips = !global$8.iOS;
      Control$1.translate = function (text) {
        return global$1.translate(text);
      };
    };
    var setupUiContainer = function (editor) {
      if (editor.settings.ui_container) {
        global$8.container = descendant(Element.fromDom(domGlobals.document.body), editor.settings.ui_container).fold(constant(null), function (elm) {
          return elm.dom();
        });
      }
    };
    var setupRtlMode = function (editor) {
      if (editor.rtl) {
        Control$1.rtl = true;
      }
    };
    var setupHideFloatPanels = function (editor) {
      editor.on('mousedown progressstate', function () {
        FloatPanel.hideAll();
      });
    };
    var setup$1 = function (editor) {
      setupRtlMode(editor);
      setupHideFloatPanels(editor);
      setupUiContainer(editor);
      setupEnvironment();
      FormatSelect.register(editor);
      Align.register(editor);
      SimpleControls.register(editor);
      UndoRedo.register(editor);
      FontSizeSelect.register(editor);
      FontSelect.register(editor);
      Formats.register(editor);
      VisualAid.register(editor);
      InsertButton.register(editor);
    };
    var FormatControls = { setup: setup$1 };

    var GridLayout = AbsoluteLayout.extend({
      recalc: function (container) {
        var settings, rows, cols, items, contLayoutRect, width, height, rect, ctrlLayoutRect, ctrl, x, y, posX, posY, ctrlSettings, contPaddingBox, align, spacingH, spacingV, alignH, alignV, maxX, maxY;
        var colWidths = [];
        var rowHeights = [];
        var ctrlMinWidth, ctrlMinHeight, availableWidth, availableHeight, reverseRows, idx;
        settings = container.settings;
        items = container.items().filter(':visible');
        contLayoutRect = container.layoutRect();
        cols = settings.columns || Math.ceil(Math.sqrt(items.length));
        rows = Math.ceil(items.length / cols);
        spacingH = settings.spacingH || settings.spacing || 0;
        spacingV = settings.spacingV || settings.spacing || 0;
        alignH = settings.alignH || settings.align;
        alignV = settings.alignV || settings.align;
        contPaddingBox = container.paddingBox;
        reverseRows = 'reverseRows' in settings ? settings.reverseRows : container.isRtl();
        if (alignH && typeof alignH === 'string') {
          alignH = [alignH];
        }
        if (alignV && typeof alignV === 'string') {
          alignV = [alignV];
        }
        for (x = 0; x < cols; x++) {
          colWidths.push(0);
        }
        for (y = 0; y < rows; y++) {
          rowHeights.push(0);
        }
        for (y = 0; y < rows; y++) {
          for (x = 0; x < cols; x++) {
            ctrl = items[y * cols + x];
            if (!ctrl) {
              break;
            }
            ctrlLayoutRect = ctrl.layoutRect();
            ctrlMinWidth = ctrlLayoutRect.minW;
            ctrlMinHeight = ctrlLayoutRect.minH;
            colWidths[x] = ctrlMinWidth > colWidths[x] ? ctrlMinWidth : colWidths[x];
            rowHeights[y] = ctrlMinHeight > rowHeights[y] ? ctrlMinHeight : rowHeights[y];
          }
        }
        availableWidth = contLayoutRect.innerW - contPaddingBox.left - contPaddingBox.right;
        for (maxX = 0, x = 0; x < cols; x++) {
          maxX += colWidths[x] + (x > 0 ? spacingH : 0);
          availableWidth -= (x > 0 ? spacingH : 0) + colWidths[x];
        }
        availableHeight = contLayoutRect.innerH - contPaddingBox.top - contPaddingBox.bottom;
        for (maxY = 0, y = 0; y < rows; y++) {
          maxY += rowHeights[y] + (y > 0 ? spacingV : 0);
          availableHeight -= (y > 0 ? spacingV : 0) + rowHeights[y];
        }
        maxX += contPaddingBox.left + contPaddingBox.right;
        maxY += contPaddingBox.top + contPaddingBox.bottom;
        rect = {};
        rect.minW = maxX + (contLayoutRect.w - contLayoutRect.innerW);
        rect.minH = maxY + (contLayoutRect.h - contLayoutRect.innerH);
        rect.contentW = rect.minW - contLayoutRect.deltaW;
        rect.contentH = rect.minH - contLayoutRect.deltaH;
        rect.minW = Math.min(rect.minW, contLayoutRect.maxW);
        rect.minH = Math.min(rect.minH, contLayoutRect.maxH);
        rect.minW = Math.max(rect.minW, contLayoutRect.startMinWidth);
        rect.minH = Math.max(rect.minH, contLayoutRect.startMinHeight);
        if (contLayoutRect.autoResize && (rect.minW !== contLayoutRect.minW || rect.minH !== contLayoutRect.minH)) {
          rect.w = rect.minW;
          rect.h = rect.minH;
          container.layoutRect(rect);
          this.recalc(container);
          if (container._lastRect === null) {
            var parentCtrl = container.parent();
            if (parentCtrl) {
              parentCtrl._lastRect = null;
              parentCtrl.recalc();
            }
          }
          return;
        }
        if (contLayoutRect.autoResize) {
          rect = container.layoutRect(rect);
          rect.contentW = rect.minW - contLayoutRect.deltaW;
          rect.contentH = rect.minH - contLayoutRect.deltaH;
        }
        var flexV;
        if (settings.packV === 'start') {
          flexV = 0;
        } else {
          flexV = availableHeight > 0 ? Math.floor(availableHeight / rows) : 0;
        }
        var totalFlex = 0;
        var flexWidths = settings.flexWidths;
        if (flexWidths) {
          for (x = 0; x < flexWidths.length; x++) {
            totalFlex += flexWidths[x];
          }
        } else {
          totalFlex = cols;
        }
        var ratio = availableWidth / totalFlex;
        for (x = 0; x < cols; x++) {
          colWidths[x] += flexWidths ? flexWidths[x] * ratio : ratio;
        }
        posY = contPaddingBox.top;
        for (y = 0; y < rows; y++) {
          posX = contPaddingBox.left;
          height = rowHeights[y] + flexV;
          for (x = 0; x < cols; x++) {
            if (reverseRows) {
              idx = y * cols + cols - 1 - x;
            } else {
              idx = y * cols + x;
            }
            ctrl = items[idx];
            if (!ctrl) {
              break;
            }
            ctrlSettings = ctrl.settings;
            ctrlLayoutRect = ctrl.layoutRect();
            width = Math.max(colWidths[x], ctrlLayoutRect.startMinWidth);
            ctrlLayoutRect.x = posX;
            ctrlLayoutRect.y = posY;
            align = ctrlSettings.alignH || (alignH ? alignH[x] || alignH[0] : null);
            if (align === 'center') {
              ctrlLayoutRect.x = posX + width / 2 - ctrlLayoutRect.w / 2;
            } else if (align === 'right') {
              ctrlLayoutRect.x = posX + width - ctrlLayoutRect.w;
            } else if (align === 'stretch') {
              ctrlLayoutRect.w = width;
            }
            align = ctrlSettings.alignV || (alignV ? alignV[x] || alignV[0] : null);
            if (align === 'center') {
              ctrlLayoutRect.y = posY + height / 2 - ctrlLayoutRect.h / 2;
            } else if (align === 'bottom') {
              ctrlLayoutRect.y = posY + height - ctrlLayoutRect.h;
            } else if (align === 'stretch') {
              ctrlLayoutRect.h = height;
            }
            ctrl.layoutRect(ctrlLayoutRect);
            posX += width + spacingH;
            if (ctrl.recalc) {
              ctrl.recalc();
            }
          }
          posY += height + spacingV;
        }
      }
    });

    var Iframe$1 = Widget.extend({
      renderHtml: function () {
        var self = this;
        self.classes.add('iframe');
        self.canFocus = false;
        return '<iframe id="' + self._id + '" class="' + self.classes + '" tabindex="-1" src="' + (self.settings.url || 'javascript:\'\'') + '" frameborder="0"></iframe>';
      },
      src: function (src) {
        this.getEl().src = src;
      },
      html: function (html, callback) {
        var self = this, body = this.getEl().contentWindow.document.body;
        if (!body) {
          global$7.setTimeout(function () {
            self.html(html);
          });
        } else {
          body.innerHTML = html;
          if (callback) {
            callback();
          }
        }
        return this;
      }
    });

    var InfoBox = Widget.extend({
      init: function (settings) {
        var self = this;
        self._super(settings);
        self.classes.add('widget').add('infobox');
        self.canFocus = false;
      },
      severity: function (level) {
        this.classes.remove('error');
        this.classes.remove('warning');
        this.classes.remove('success');
        this.classes.add(level);
      },
      help: function (state) {
        this.state.set('help', state);
      },
      renderHtml: function () {
        var self = this, prefix = self.classPrefix;
        return '<div id="' + self._id + '" class="' + self.classes + '">' + '<div id="' + self._id + '-body">' + self.encode(self.state.get('text')) + '<button role="button" tabindex="-1">' + '<i class="' + prefix + 'ico ' + prefix + 'i-help"></i>' + '</button>' + '</div>' + '</div>';
      },
      bindStates: function () {
        var self = this;
        self.state.on('change:text', function (e) {
          self.getEl('body').firstChild.data = self.encode(e.value);
          if (self.state.get('rendered')) {
            self.updateLayoutRect();
          }
        });
        self.state.on('change:help', function (e) {
          self.classes.toggle('has-help', e.value);
          if (self.state.get('rendered')) {
            self.updateLayoutRect();
          }
        });
        return self._super();
      }
    });

    var Label = Widget.extend({
      init: function (settings) {
        var self = this;
        self._super(settings);
        self.classes.add('widget').add('label');
        self.canFocus = false;
        if (settings.multiline) {
          self.classes.add('autoscroll');
        }
        if (settings.strong) {
          self.classes.add('strong');
        }
      },
      initLayoutRect: function () {
        var self = this, layoutRect = self._super();
        if (self.settings.multiline) {
          var size = funcs.getSize(self.getEl());
          if (size.width > layoutRect.maxW) {
            layoutRect.minW = layoutRect.maxW;
            self.classes.add('multiline');
          }
          self.getEl().style.width = layoutRect.minW + 'px';
          layoutRect.startMinH = layoutRect.h = layoutRect.minH = Math.min(layoutRect.maxH, funcs.getSize(self.getEl()).height);
        }
        return layoutRect;
      },
      repaint: function () {
        var self = this;
        if (!self.settings.multiline) {
          self.getEl().style.lineHeight = self.layoutRect().h + 'px';
        }
        return self._super();
      },
      severity: function (level) {
        this.classes.remove('error');
        this.classes.remove('warning');
        this.classes.remove('success');
        this.classes.add(level);
      },
      renderHtml: function () {
        var self = this;
        var targetCtrl, forName, forId = self.settings.forId;
        var text = self.settings.html ? self.settings.html : self.encode(self.state.get('text'));
        if (!forId && (forName = self.settings.forName)) {
          targetCtrl = self.getRoot().find('#' + forName)[0];
          if (targetCtrl) {
            forId = targetCtrl._id;
          }
        }
        if (forId) {
          return '<label id="' + self._id + '" class="' + self.classes + '"' + (forId ? ' for="' + forId + '"' : '') + '>' + text + '</label>';
        }
        return '<span id="' + self._id + '" class="' + self.classes + '">' + text + '</span>';
      },
      bindStates: function () {
        var self = this;
        self.state.on('change:text', function (e) {
          self.innerHtml(self.encode(e.value));
          if (self.state.get('rendered')) {
            self.updateLayoutRect();
          }
        });
        return self._super();
      }
    });

    var Toolbar$1 = Container.extend({
      Defaults: {
        role: 'toolbar',
        layout: 'flow'
      },
      init: function (settings) {
        var self = this;
        self._super(settings);
        self.classes.add('toolbar');
      },
      postRender: function () {
        var self = this;
        self.items().each(function (ctrl) {
          ctrl.classes.add('toolbar-item');
        });
        return self._super();
      }
    });

    var MenuBar = Toolbar$1.extend({
      Defaults: {
        role: 'menubar',
        containerCls: 'menubar',
        ariaRoot: true,
        defaults: { type: 'menubutton' }
      }
    });

    function isChildOf$1(node, parent) {
      while (node) {
        if (parent === node) {
          return true;
        }
        node = node.parentNode;
      }
      return false;
    }
    var MenuButton = Button.extend({
      init: function (settings) {
        var self = this;
        self._renderOpen = true;
        self._super(settings);
        settings = self.settings;
        self.classes.add('menubtn');
        if (settings.fixedWidth) {
          self.classes.add('fixed-width');
        }
        self.aria('haspopup', true);
        self.state.set('menu', settings.menu || self.render());
      },
      showMenu: function (toggle) {
        var self = this;
        var menu;
        if (self.menu && self.menu.visible() && toggle !== false) {
          return self.hideMenu();
        }
        if (!self.menu) {
          menu = self.state.get('menu') || [];
          self.classes.add('opened');
          if (menu.length) {
            menu = {
              type: 'menu',
              animate: true,
              items: menu
            };
          } else {
            menu.type = menu.type || 'menu';
            menu.animate = true;
          }
          if (!menu.renderTo) {
            self.menu = global$4.create(menu).parent(self).renderTo();
          } else {
            self.menu = menu.parent(self).show().renderTo();
          }
          self.fire('createmenu');
          self.menu.reflow();
          self.menu.on('cancel', function (e) {
            if (e.control.parent() === self.menu) {
              e.stopPropagation();
              self.focus();
              self.hideMenu();
            }
          });
          self.menu.on('select', function () {
            self.focus();
          });
          self.menu.on('show hide', function (e) {
            if (e.type === 'hide' && e.control.parent() === self) {
              self.classes.remove('opened-under');
            }
            if (e.control === self.menu) {
              self.activeMenu(e.type === 'show');
              self.classes.toggle('opened', e.type === 'show');
            }
            self.aria('expanded', e.type === 'show');
          }).fire('show');
        }
        self.menu.show();
        self.menu.layoutRect({ w: self.layoutRect().w });
        self.menu.repaint();
        self.menu.moveRel(self.getEl(), self.isRtl() ? [
          'br-tr',
          'tr-br'
        ] : [
          'bl-tl',
          'tl-bl'
        ]);
        var menuLayoutRect = self.menu.layoutRect();
        var selfBottom = self.$el.offset().top + self.layoutRect().h;
        if (selfBottom > menuLayoutRect.y && selfBottom < menuLayoutRect.y + menuLayoutRect.h) {
          self.classes.add('opened-under');
        }
        self.fire('showmenu');
      },
      hideMenu: function () {
        var self = this;
        if (self.menu) {
          self.menu.items().each(function (item) {
            if (item.hideMenu) {
              item.hideMenu();
            }
          });
          self.menu.hide();
        }
      },
      activeMenu: function (state) {
        this.classes.toggle('active', state);
      },
      renderHtml: function () {
        var self = this, id = self._id, prefix = self.classPrefix;
        var icon = self.settings.icon, image;
        var text = self.state.get('text');
        var textHtml = '';
        image = self.settings.image;
        if (image) {
          icon = 'none';
          if (typeof image !== 'string') {
            image = domGlobals.window.getSelection ? image[0] : image[1];
          }
          image = ' style="background-image: url(\'' + image + '\')"';
        } else {
          image = '';
        }
        if (text) {
          self.classes.add('btn-has-text');
          textHtml = '<span class="' + prefix + 'txt">' + self.encode(text) + '</span>';
        }
        icon = self.settings.icon ? prefix + 'ico ' + prefix + 'i-' + icon : '';
        self.aria('role', self.parent() instanceof MenuBar ? 'menuitem' : 'button');
        return '<div id="' + id + '" class="' + self.classes + '" tabindex="-1" aria-labelledby="' + id + '">' + '<button id="' + id + '-open" role="presentation" type="button" tabindex="-1">' + (icon ? '<i class="' + icon + '"' + image + '></i>' : '') + textHtml + ' <i class="' + prefix + 'caret"></i>' + '</button>' + '</div>';
      },
      postRender: function () {
        var self = this;
        self.on('click', function (e) {
          if (e.control === self && isChildOf$1(e.target, self.getEl())) {
            self.focus();
            self.showMenu(!e.aria);
            if (e.aria) {
              self.menu.items().filter(':visible')[0].focus();
            }
          }
        });
        self.on('mouseenter', function (e) {
          var overCtrl = e.control;
          var parent = self.parent();
          var hasVisibleSiblingMenu;
          if (overCtrl && parent && overCtrl instanceof MenuButton && overCtrl.parent() === parent) {
            parent.items().filter('MenuButton').each(function (ctrl) {
              if (ctrl.hideMenu && ctrl !== overCtrl) {
                if (ctrl.menu && ctrl.menu.visible()) {
                  hasVisibleSiblingMenu = true;
                }
                ctrl.hideMenu();
              }
            });
            if (hasVisibleSiblingMenu) {
              overCtrl.focus();
              overCtrl.showMenu();
            }
          }
        });
        return self._super();
      },
      bindStates: function () {
        var self = this;
        self.state.on('change:menu', function () {
          if (self.menu) {
            self.menu.remove();
          }
          self.menu = null;
        });
        return self._super();
      },
      remove: function () {
        this._super();
        if (this.menu) {
          this.menu.remove();
        }
      }
    });

    var Menu = FloatPanel.extend({
      Defaults: {
        defaultType: 'menuitem',
        border: 1,
        layout: 'stack',
        role: 'application',
        bodyRole: 'menu',
        ariaRoot: true
      },
      init: function (settings) {
        var self = this;
        settings.autohide = true;
        settings.constrainToViewport = true;
        if (typeof settings.items === 'function') {
          settings.itemsFactory = settings.items;
          settings.items = [];
        }
        if (settings.itemDefaults) {
          var items = settings.items;
          var i = items.length;
          while (i--) {
            items[i] = global$2.extend({}, settings.itemDefaults, items[i]);
          }
        }
        self._super(settings);
        self.classes.add('menu');
        if (settings.animate && global$8.ie !== 11) {
          self.classes.add('animate');
        }
      },
      repaint: function () {
        this.classes.toggle('menu-align', true);
        this._super();
        this.getEl().style.height = '';
        this.getEl('body').style.height = '';
        return this;
      },
      cancel: function () {
        var self = this;
        self.hideAll();
        self.fire('select');
      },
      load: function () {
        var self = this;
        var time, factory;
        function hideThrobber() {
          if (self.throbber) {
            self.throbber.hide();
            self.throbber = null;
          }
        }
        factory = self.settings.itemsFactory;
        if (!factory) {
          return;
        }
        if (!self.throbber) {
          self.throbber = new Throbber(self.getEl('body'), true);
          if (self.items().length === 0) {
            self.throbber.show();
            self.fire('loading');
          } else {
            self.throbber.show(100, function () {
              self.items().remove();
              self.fire('loading');
            });
          }
          self.on('hide close', hideThrobber);
        }
        self.requestTime = time = new Date().getTime();
        self.settings.itemsFactory(function (items) {
          if (items.length === 0) {
            self.hide();
            return;
          }
          if (self.requestTime !== time) {
            return;
          }
          self.getEl().style.width = '';
          self.getEl('body').style.width = '';
          hideThrobber();
          self.items().remove();
          self.getEl('body').innerHTML = '';
          self.add(items);
          self.renderNew();
          self.fire('loaded');
        });
      },
      hideAll: function () {
        var self = this;
        this.find('menuitem').exec('hideMenu');
        return self._super();
      },
      preRender: function () {
        var self = this;
        self.items().each(function (ctrl) {
          var settings = ctrl.settings;
          if (settings.icon || settings.image || settings.selectable) {
            self._hasIcons = true;
            return false;
          }
        });
        if (self.settings.itemsFactory) {
          self.on('postrender', function () {
            if (self.settings.itemsFactory) {
              self.load();
            }
          });
        }
        self.on('show hide', function (e) {
          if (e.control === self) {
            if (e.type === 'show') {
              global$7.setTimeout(function () {
                self.classes.add('in');
              }, 0);
            } else {
              self.classes.remove('in');
            }
          }
        });
        return self._super();
      }
    });

    var ListBox = MenuButton.extend({
      init: function (settings) {
        var self = this;
        var values, selected, selectedText, lastItemCtrl;
        function setSelected(menuValues) {
          for (var i = 0; i < menuValues.length; i++) {
            selected = menuValues[i].selected || settings.value === menuValues[i].value;
            if (selected) {
              selectedText = selectedText || menuValues[i].text;
              self.state.set('value', menuValues[i].value);
              return true;
            }
            if (menuValues[i].menu) {
              if (setSelected(menuValues[i].menu)) {
                return true;
              }
            }
          }
        }
        self._super(settings);
        settings = self.settings;
        self._values = values = settings.values;
        if (values) {
          if (typeof settings.value !== 'undefined') {
            setSelected(values);
          }
          if (!selected && values.length > 0) {
            selectedText = values[0].text;
            self.state.set('value', values[0].value);
          }
          self.state.set('menu', values);
        }
        self.state.set('text', settings.text || selectedText);
        self.classes.add('listbox');
        self.on('select', function (e) {
          var ctrl = e.control;
          if (lastItemCtrl) {
            e.lastControl = lastItemCtrl;
          }
          if (settings.multiple) {
            ctrl.active(!ctrl.active());
          } else {
            self.value(e.control.value());
          }
          lastItemCtrl = ctrl;
        });
      },
      value: function (value) {
        if (arguments.length === 0) {
          return this.state.get('value');
        }
        if (typeof value === 'undefined') {
          return this;
        }
        function valueExists(values) {
          return exists(values, function (a) {
            return a.menu ? valueExists(a.menu) : a.value === value;
          });
        }
        if (this.settings.values) {
          if (valueExists(this.settings.values)) {
            this.state.set('value', value);
          } else if (value === null) {
            this.state.set('value', null);
          }
        } else {
          this.state.set('value', value);
        }
        return this;
      },
      bindStates: function () {
        var self = this;
        function activateMenuItemsByValue(menu, value) {
          if (menu instanceof Menu) {
            menu.items().each(function (ctrl) {
              if (!ctrl.hasMenus()) {
                ctrl.active(ctrl.value() === value);
              }
            });
          }
        }
        function getSelectedItem(menuValues, value) {
          var selectedItem;
          if (!menuValues) {
            return;
          }
          for (var i = 0; i < menuValues.length; i++) {
            if (menuValues[i].value === value) {
              return menuValues[i];
            }
            if (menuValues[i].menu) {
              selectedItem = getSelectedItem(menuValues[i].menu, value);
              if (selectedItem) {
                return selectedItem;
              }
            }
          }
        }
        self.on('show', function (e) {
          activateMenuItemsByValue(e.control, self.value());
        });
        self.state.on('change:value', function (e) {
          var selectedItem = getSelectedItem(self.state.get('menu'), e.value);
          if (selectedItem) {
            self.text(selectedItem.text);
          } else {
            self.text(self.settings.text);
          }
        });
        return self._super();
      }
    });

    var toggleTextStyle = function (ctrl, state) {
      var textStyle = ctrl._textStyle;
      if (textStyle) {
        var textElm = ctrl.getEl('text');
        textElm.setAttribute('style', textStyle);
        if (state) {
          textElm.style.color = '';
          textElm.style.backgroundColor = '';
        }
      }
    };
    var MenuItem = Widget.extend({
      Defaults: {
        border: 0,
        role: 'menuitem'
      },
      init: function (settings) {
        var self = this;
        var text;
        self._super(settings);
        settings = self.settings;
        self.classes.add('menu-item');
        if (settings.menu) {
          self.classes.add('menu-item-expand');
        }
        if (settings.preview) {
          self.classes.add('menu-item-preview');
        }
        text = self.state.get('text');
        if (text === '-' || text === '|') {
          self.classes.add('menu-item-sep');
          self.aria('role', 'separator');
          self.state.set('text', '-');
        }
        if (settings.selectable) {
          self.aria('role', 'menuitemcheckbox');
          self.classes.add('menu-item-checkbox');
          settings.icon = 'selected';
        }
        if (!settings.preview && !settings.selectable) {
          self.classes.add('menu-item-normal');
        }
        self.on('mousedown', function (e) {
          e.preventDefault();
        });
        if (settings.menu && !settings.ariaHideMenu) {
          self.aria('haspopup', true);
        }
      },
      hasMenus: function () {
        return !!this.settings.menu;
      },
      showMenu: function () {
        var self = this;
        var settings = self.settings;
        var menu;
        var parent = self.parent();
        parent.items().each(function (ctrl) {
          if (ctrl !== self) {
            ctrl.hideMenu();
          }
        });
        if (settings.menu) {
          menu = self.menu;
          if (!menu) {
            menu = settings.menu;
            if (menu.length) {
              menu = {
                type: 'menu',
                items: menu
              };
            } else {
              menu.type = menu.type || 'menu';
            }
            if (parent.settings.itemDefaults) {
              menu.itemDefaults = parent.settings.itemDefaults;
            }
            menu = self.menu = global$4.create(menu).parent(self).renderTo();
            menu.reflow();
            menu.on('cancel', function (e) {
              e.stopPropagation();
              self.focus();
              menu.hide();
            });
            menu.on('show hide', function (e) {
              if (e.control.items) {
                e.control.items().each(function (ctrl) {
                  ctrl.active(ctrl.settings.selected);
                });
              }
            }).fire('show');
            menu.on('hide', function (e) {
              if (e.control === menu) {
                self.classes.remove('selected');
              }
            });
            menu.submenu = true;
          } else {
            menu.show();
          }
          menu._parentMenu = parent;
          menu.classes.add('menu-sub');
          var rel = menu.testMoveRel(self.getEl(), self.isRtl() ? [
            'tl-tr',
            'bl-br',
            'tr-tl',
            'br-bl'
          ] : [
            'tr-tl',
            'br-bl',
            'tl-tr',
            'bl-br'
          ]);
          menu.moveRel(self.getEl(), rel);
          menu.rel = rel;
          rel = 'menu-sub-' + rel;
          menu.classes.remove(menu._lastRel).add(rel);
          menu._lastRel = rel;
          self.classes.add('selected');
          self.aria('expanded', true);
        }
      },
      hideMenu: function () {
        var self = this;
        if (self.menu) {
          self.menu.items().each(function (item) {
            if (item.hideMenu) {
              item.hideMenu();
            }
          });
          self.menu.hide();
          self.aria('expanded', false);
        }
        return self;
      },
      renderHtml: function () {
        var self = this;
        var id = self._id;
        var settings = self.settings;
        var prefix = self.classPrefix;
        var text = self.state.get('text');
        var icon = self.settings.icon, image = '', shortcut = settings.shortcut;
        var url = self.encode(settings.url), iconHtml = '';
        function convertShortcut(shortcut) {
          var i, value, replace = {};
          if (global$8.mac) {
            replace = {
              alt: '&#x2325;',
              ctrl: '&#x2318;',
              shift: '&#x21E7;',
              meta: '&#x2318;'
            };
          } else {
            replace = { meta: 'Ctrl' };
          }
          shortcut = shortcut.split('+');
          for (i = 0; i < shortcut.length; i++) {
            value = replace[shortcut[i].toLowerCase()];
            if (value) {
              shortcut[i] = value;
            }
          }
          return shortcut.join('+');
        }
        function escapeRegExp(str) {
          return str.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        }
        function markMatches(text) {
          var match = settings.match || '';
          return match ? text.replace(new RegExp(escapeRegExp(match), 'gi'), function (match) {
            return '!mce~match[' + match + ']mce~match!';
          }) : text;
        }
        function boldMatches(text) {
          return text.replace(new RegExp(escapeRegExp('!mce~match['), 'g'), '<b>').replace(new RegExp(escapeRegExp(']mce~match!'), 'g'), '</b>');
        }
        if (icon) {
          self.parent().classes.add('menu-has-icons');
        }
        if (settings.image) {
          image = ' style="background-image: url(\'' + settings.image + '\')"';
        }
        if (shortcut) {
          shortcut = convertShortcut(shortcut);
        }
        icon = prefix + 'ico ' + prefix + 'i-' + (self.settings.icon || 'none');
        iconHtml = text !== '-' ? '<i class="' + icon + '"' + image + '></i>\xA0' : '';
        text = boldMatches(self.encode(markMatches(text)));
        url = boldMatches(self.encode(markMatches(url)));
        return '<div id="' + id + '" class="' + self.classes + '" tabindex="-1">' + iconHtml + (text !== '-' ? '<span id="' + id + '-text" class="' + prefix + 'text">' + text + '</span>' : '') + (shortcut ? '<div id="' + id + '-shortcut" class="' + prefix + 'menu-shortcut">' + shortcut + '</div>' : '') + (settings.menu ? '<div class="' + prefix + 'caret"></div>' : '') + (url ? '<div class="' + prefix + 'menu-item-link">' + url + '</div>' : '') + '</div>';
      },
      postRender: function () {
        var self = this, settings = self.settings;
        var textStyle = settings.textStyle;
        if (typeof textStyle === 'function') {
          textStyle = textStyle.call(this);
        }
        if (textStyle) {
          var textElm = self.getEl('text');
          if (textElm) {
            textElm.setAttribute('style', textStyle);
            self._textStyle = textStyle;
          }
        }
        self.on('mouseenter click', function (e) {
          if (e.control === self) {
            if (!settings.menu && e.type === 'click') {
              self.fire('select');
              global$7.requestAnimationFrame(function () {
                self.parent().hideAll();
              });
            } else {
              self.showMenu();
              if (e.aria) {
                self.menu.focus(true);
              }
            }
          }
        });
        self._super();
        return self;
      },
      hover: function () {
        var self = this;
        self.parent().items().each(function (ctrl) {
          ctrl.classes.remove('selected');
        });
        self.classes.toggle('selected', true);
        return self;
      },
      active: function (state) {
        toggleTextStyle(this, state);
        if (typeof state !== 'undefined') {
          this.aria('checked', state);
        }
        return this._super(state);
      },
      remove: function () {
        this._super();
        if (this.menu) {
          this.menu.remove();
        }
      }
    });

    var Radio = Checkbox.extend({
      Defaults: {
        classes: 'radio',
        role: 'radio'
      }
    });

    var ResizeHandle = Widget.extend({
      renderHtml: function () {
        var self = this, prefix = self.classPrefix;
        self.classes.add('resizehandle');
        if (self.settings.direction === 'both') {
          self.classes.add('resizehandle-both');
        }
        self.canFocus = false;
        return '<div id="' + self._id + '" class="' + self.classes + '">' + '<i class="' + prefix + 'ico ' + prefix + 'i-resize"></i>' + '</div>';
      },
      postRender: function () {
        var self = this;
        self._super();
        self.resizeDragHelper = new DragHelper(this._id, {
          start: function () {
            self.fire('ResizeStart');
          },
          drag: function (e) {
            if (self.settings.direction !== 'both') {
              e.deltaX = 0;
            }
            self.fire('Resize', e);
          },
          stop: function () {
            self.fire('ResizeEnd');
          }
        });
      },
      remove: function () {
        if (this.resizeDragHelper) {
          this.resizeDragHelper.destroy();
        }
        return this._super();
      }
    });

    function createOptions(options) {
      var strOptions = '';
      if (options) {
        for (var i = 0; i < options.length; i++) {
          strOptions += '<option value="' + options[i] + '">' + options[i] + '</option>';
        }
      }
      return strOptions;
    }
    var SelectBox = Widget.extend({
      Defaults: {
        classes: 'selectbox',
        role: 'selectbox',
        options: []
      },
      init: function (settings) {
        var self = this;
        self._super(settings);
        if (self.settings.size) {
          self.size = self.settings.size;
        }
        if (self.settings.options) {
          self._options = self.settings.options;
        }
        self.on('keydown', function (e) {
          var rootControl;
          if (e.keyCode === 13) {
            e.preventDefault();
            self.parents().reverse().each(function (ctrl) {
              if (ctrl.toJSON) {
                rootControl = ctrl;
                return false;
              }
            });
            self.fire('submit', { data: rootControl.toJSON() });
          }
        });
      },
      options: function (state) {
        if (!arguments.length) {
          return this.state.get('options');
        }
        this.state.set('options', state);
        return this;
      },
      renderHtml: function () {
        var self = this;
        var options, size = '';
        options = createOptions(self._options);
        if (self.size) {
          size = ' size = "' + self.size + '"';
        }
        return '<select id="' + self._id + '" class="' + self.classes + '"' + size + '>' + options + '</select>';
      },
      bindStates: function () {
        var self = this;
        self.state.on('change:options', function (e) {
          self.getEl().innerHTML = createOptions(e.value);
        });
        return self._super();
      }
    });

    function constrain(value, minVal, maxVal) {
      if (value < minVal) {
        value = minVal;
      }
      if (value > maxVal) {
        value = maxVal;
      }
      return value;
    }
    function setAriaProp(el, name, value) {
      el.setAttribute('aria-' + name, value);
    }
    function updateSliderHandle(ctrl, value) {
      var maxHandlePos, shortSizeName, sizeName, stylePosName, styleValue, handleEl;
      if (ctrl.settings.orientation === 'v') {
        stylePosName = 'top';
        sizeName = 'height';
        shortSizeName = 'h';
      } else {
        stylePosName = 'left';
        sizeName = 'width';
        shortSizeName = 'w';
      }
      handleEl = ctrl.getEl('handle');
      maxHandlePos = (ctrl.layoutRect()[shortSizeName] || 100) - funcs.getSize(handleEl)[sizeName];
      styleValue = maxHandlePos * ((value - ctrl._minValue) / (ctrl._maxValue - ctrl._minValue)) + 'px';
      handleEl.style[stylePosName] = styleValue;
      handleEl.style.height = ctrl.layoutRect().h + 'px';
      setAriaProp(handleEl, 'valuenow', value);
      setAriaProp(handleEl, 'valuetext', '' + ctrl.settings.previewFilter(value));
      setAriaProp(handleEl, 'valuemin', ctrl._minValue);
      setAriaProp(handleEl, 'valuemax', ctrl._maxValue);
    }
    var Slider = Widget.extend({
      init: function (settings) {
        var self = this;
        if (!settings.previewFilter) {
          settings.previewFilter = function (value) {
            return Math.round(value * 100) / 100;
          };
        }
        self._super(settings);
        self.classes.add('slider');
        if (settings.orientation === 'v') {
          self.classes.add('vertical');
        }
        self._minValue = isNumber(settings.minValue) ? settings.minValue : 0;
        self._maxValue = isNumber(settings.maxValue) ? settings.maxValue : 100;
        self._initValue = self.state.get('value');
      },
      renderHtml: function () {
        var self = this, id = self._id, prefix = self.classPrefix;
        return '<div id="' + id + '" class="' + self.classes + '">' + '<div id="' + id + '-handle" class="' + prefix + 'slider-handle" role="slider" tabindex="-1"></div>' + '</div>';
      },
      reset: function () {
        this.value(this._initValue).repaint();
      },
      postRender: function () {
        var self = this;
        var minValue, maxValue, screenCordName, stylePosName, sizeName, shortSizeName;
        function toFraction(min, max, val) {
          return (val + min) / (max - min);
        }
        function fromFraction(min, max, val) {
          return val * (max - min) - min;
        }
        function handleKeyboard(minValue, maxValue) {
          function alter(delta) {
            var value;
            value = self.value();
            value = fromFraction(minValue, maxValue, toFraction(minValue, maxValue, value) + delta * 0.05);
            value = constrain(value, minValue, maxValue);
            self.value(value);
            self.fire('dragstart', { value: value });
            self.fire('drag', { value: value });
            self.fire('dragend', { value: value });
          }
          self.on('keydown', function (e) {
            switch (e.keyCode) {
            case 37:
            case 38:
              alter(-1);
              break;
            case 39:
            case 40:
              alter(1);
              break;
            }
          });
        }
        function handleDrag(minValue, maxValue, handleEl) {
          var startPos, startHandlePos, maxHandlePos, handlePos, value;
          self._dragHelper = new DragHelper(self._id, {
            handle: self._id + '-handle',
            start: function (e) {
              startPos = e[screenCordName];
              startHandlePos = parseInt(self.getEl('handle').style[stylePosName], 10);
              maxHandlePos = (self.layoutRect()[shortSizeName] || 100) - funcs.getSize(handleEl)[sizeName];
              self.fire('dragstart', { value: value });
            },
            drag: function (e) {
              var delta = e[screenCordName] - startPos;
              handlePos = constrain(startHandlePos + delta, 0, maxHandlePos);
              handleEl.style[stylePosName] = handlePos + 'px';
              value = minValue + handlePos / maxHandlePos * (maxValue - minValue);
              self.value(value);
              self.tooltip().text('' + self.settings.previewFilter(value)).show().moveRel(handleEl, 'bc tc');
              self.fire('drag', { value: value });
            },
            stop: function () {
              self.tooltip().hide();
              self.fire('dragend', { value: value });
            }
          });
        }
        minValue = self._minValue;
        maxValue = self._maxValue;
        if (self.settings.orientation === 'v') {
          screenCordName = 'screenY';
          stylePosName = 'top';
          sizeName = 'height';
          shortSizeName = 'h';
        } else {
          screenCordName = 'screenX';
          stylePosName = 'left';
          sizeName = 'width';
          shortSizeName = 'w';
        }
        self._super();
        handleKeyboard(minValue, maxValue);
        handleDrag(minValue, maxValue, self.getEl('handle'));
      },
      repaint: function () {
        this._super();
        updateSliderHandle(this, this.value());
      },
      bindStates: function () {
        var self = this;
        self.state.on('change:value', function (e) {
          updateSliderHandle(self, e.value);
        });
        return self._super();
      }
    });

    var Spacer = Widget.extend({
      renderHtml: function () {
        var self = this;
        self.classes.add('spacer');
        self.canFocus = false;
        return '<div id="' + self._id + '" class="' + self.classes + '"></div>';
      }
    });

    var SplitButton = MenuButton.extend({
      Defaults: {
        classes: 'widget btn splitbtn',
        role: 'button'
      },
      repaint: function () {
        var self = this;
        var elm = self.getEl();
        var rect = self.layoutRect();
        var mainButtonElm, menuButtonElm;
        self._super();
        mainButtonElm = elm.firstChild;
        menuButtonElm = elm.lastChild;
        global$9(mainButtonElm).css({
          width: rect.w - funcs.getSize(menuButtonElm).width,
          height: rect.h - 2
        });
        global$9(menuButtonElm).css({ height: rect.h - 2 });
        return self;
      },
      activeMenu: function (state) {
        var self = this;
        global$9(self.getEl().lastChild).toggleClass(self.classPrefix + 'active', state);
      },
      renderHtml: function () {
        var self = this;
        var id = self._id;
        var prefix = self.classPrefix;
        var image;
        var icon = self.state.get('icon');
        var text = self.state.get('text');
        var settings = self.settings;
        var textHtml = '', ariaPressed;
        image = settings.image;
        if (image) {
          icon = 'none';
          if (typeof image !== 'string') {
            image = domGlobals.window.getSelection ? image[0] : image[1];
          }
          image = ' style="background-image: url(\'' + image + '\')"';
        } else {
          image = '';
        }
        icon = settings.icon ? prefix + 'ico ' + prefix + 'i-' + icon : '';
        if (text) {
          self.classes.add('btn-has-text');
          textHtml = '<span class="' + prefix + 'txt">' + self.encode(text) + '</span>';
        }
        ariaPressed = typeof settings.active === 'boolean' ? ' aria-pressed="' + settings.active + '"' : '';
        return '<div id="' + id + '" class="' + self.classes + '" role="button"' + ariaPressed + ' tabindex="-1">' + '<button type="button" hidefocus="1" tabindex="-1">' + (icon ? '<i class="' + icon + '"' + image + '></i>' : '') + textHtml + '</button>' + '<button type="button" class="' + prefix + 'open" hidefocus="1" tabindex="-1">' + (self._menuBtnText ? (icon ? '\xA0' : '') + self._menuBtnText : '') + ' <i class="' + prefix + 'caret"></i>' + '</button>' + '</div>';
      },
      postRender: function () {
        var self = this, onClickHandler = self.settings.onclick;
        self.on('click', function (e) {
          var node = e.target;
          if (e.control === this) {
            while (node) {
              if (e.aria && e.aria.key !== 'down' || node.nodeName === 'BUTTON' && node.className.indexOf('open') === -1) {
                e.stopImmediatePropagation();
                if (onClickHandler) {
                  onClickHandler.call(this, e);
                }
                return;
              }
              node = node.parentNode;
            }
          }
        });
        delete self.settings.onclick;
        return self._super();
      }
    });

    var StackLayout = FlowLayout.extend({
      Defaults: {
        containerClass: 'stack-layout',
        controlClass: 'stack-layout-item',
        endClass: 'break'
      },
      isNative: function () {
        return true;
      }
    });

    var TabPanel = Panel.extend({
      Defaults: {
        layout: 'absolute',
        defaults: { type: 'panel' }
      },
      activateTab: function (idx) {
        var activeTabElm;
        if (this.activeTabId) {
          activeTabElm = this.getEl(this.activeTabId);
          global$9(activeTabElm).removeClass(this.classPrefix + 'active');
          activeTabElm.setAttribute('aria-selected', 'false');
        }
        this.activeTabId = 't' + idx;
        activeTabElm = this.getEl('t' + idx);
        activeTabElm.setAttribute('aria-selected', 'true');
        global$9(activeTabElm).addClass(this.classPrefix + 'active');
        this.items()[idx].show().fire('showtab');
        this.reflow();
        this.items().each(function (item, i) {
          if (idx !== i) {
            item.hide();
          }
        });
      },
      renderHtml: function () {
        var self = this;
        var layout = self._layout;
        var tabsHtml = '';
        var prefix = self.classPrefix;
        self.preRender();
        layout.preRender(self);
        self.items().each(function (ctrl, i) {
          var id = self._id + '-t' + i;
          ctrl.aria('role', 'tabpanel');
          ctrl.aria('labelledby', id);
          tabsHtml += '<div id="' + id + '" class="' + prefix + 'tab" ' + 'unselectable="on" role="tab" aria-controls="' + ctrl._id + '" aria-selected="false" tabIndex="-1">' + self.encode(ctrl.settings.title) + '</div>';
        });
        return '<div id="' + self._id + '" class="' + self.classes + '" hidefocus="1" tabindex="-1">' + '<div id="' + self._id + '-head" class="' + prefix + 'tabs" role="tablist">' + tabsHtml + '</div>' + '<div id="' + self._id + '-body" class="' + self.bodyClasses + '">' + layout.renderHtml(self) + '</div>' + '</div>';
      },
      postRender: function () {
        var self = this;
        self._super();
        self.settings.activeTab = self.settings.activeTab || 0;
        self.activateTab(self.settings.activeTab);
        this.on('click', function (e) {
          var targetParent = e.target.parentNode;
          if (targetParent && targetParent.id === self._id + '-head') {
            var i = targetParent.childNodes.length;
            while (i--) {
              if (targetParent.childNodes[i] === e.target) {
                self.activateTab(i);
              }
            }
          }
        });
      },
      initLayoutRect: function () {
        var self = this;
        var rect, minW, minH;
        minW = funcs.getSize(self.getEl('head')).width;
        minW = minW < 0 ? 0 : minW;
        minH = 0;
        self.items().each(function (item) {
          minW = Math.max(minW, item.layoutRect().minW);
          minH = Math.max(minH, item.layoutRect().minH);
        });
        self.items().each(function (ctrl) {
          ctrl.settings.x = 0;
          ctrl.settings.y = 0;
          ctrl.settings.w = minW;
          ctrl.settings.h = minH;
          ctrl.layoutRect({
            x: 0,
            y: 0,
            w: minW,
            h: minH
          });
        });
        var headH = funcs.getSize(self.getEl('head')).height;
        self.settings.minWidth = minW;
        self.settings.minHeight = minH + headH;
        rect = self._super();
        rect.deltaH += headH;
        rect.innerH = rect.h - rect.deltaH;
        return rect;
      }
    });

    var TextBox = Widget.extend({
      init: function (settings) {
        var self = this;
        self._super(settings);
        self.classes.add('textbox');
        if (settings.multiline) {
          self.classes.add('multiline');
        } else {
          self.on('keydown', function (e) {
            var rootControl;
            if (e.keyCode === 13) {
              e.preventDefault();
              self.parents().reverse().each(function (ctrl) {
                if (ctrl.toJSON) {
                  rootControl = ctrl;
                  return false;
                }
              });
              self.fire('submit', { data: rootControl.toJSON() });
            }
          });
          self.on('keyup', function (e) {
            self.state.set('value', e.target.value);
          });
        }
      },
      repaint: function () {
        var self = this;
        var style, rect, borderBox, borderW, borderH = 0, lastRepaintRect;
        style = self.getEl().style;
        rect = self._layoutRect;
        lastRepaintRect = self._lastRepaintRect || {};
        var doc = domGlobals.document;
        if (!self.settings.multiline && doc.all && (!doc.documentMode || doc.documentMode <= 8)) {
          style.lineHeight = rect.h - borderH + 'px';
        }
        borderBox = self.borderBox;
        borderW = borderBox.left + borderBox.right + 8;
        borderH = borderBox.top + borderBox.bottom + (self.settings.multiline ? 8 : 0);
        if (rect.x !== lastRepaintRect.x) {
          style.left = rect.x + 'px';
          lastRepaintRect.x = rect.x;
        }
        if (rect.y !== lastRepaintRect.y) {
          style.top = rect.y + 'px';
          lastRepaintRect.y = rect.y;
        }
        if (rect.w !== lastRepaintRect.w) {
          style.width = rect.w - borderW + 'px';
          lastRepaintRect.w = rect.w;
        }
        if (rect.h !== lastRepaintRect.h) {
          style.height = rect.h - borderH + 'px';
          lastRepaintRect.h = rect.h;
        }
        self._lastRepaintRect = lastRepaintRect;
        self.fire('repaint', {}, false);
        return self;
      },
      renderHtml: function () {
        var self = this;
        var settings = self.settings;
        var attrs, elm;
        attrs = {
          id: self._id,
          hidefocus: '1'
        };
        global$2.each([
          'rows',
          'spellcheck',
          'maxLength',
          'size',
          'readonly',
          'min',
          'max',
          'step',
          'list',
          'pattern',
          'placeholder',
          'required',
          'multiple'
        ], function (name) {
          attrs[name] = settings[name];
        });
        if (self.disabled()) {
          attrs.disabled = 'disabled';
        }
        if (settings.subtype) {
          attrs.type = settings.subtype;
        }
        elm = funcs.create(settings.multiline ? 'textarea' : 'input', attrs);
        elm.value = self.state.get('value');
        elm.className = self.classes.toString();
        return elm.outerHTML;
      },
      value: function (value) {
        if (arguments.length) {
          this.state.set('value', value);
          return this;
        }
        if (this.state.get('rendered')) {
          this.state.set('value', this.getEl().value);
        }
        return this.state.get('value');
      },
      postRender: function () {
        var self = this;
        self.getEl().value = self.state.get('value');
        self._super();
        self.$el.on('change', function (e) {
          self.state.set('value', e.target.value);
          self.fire('change', e);
        });
      },
      bindStates: function () {
        var self = this;
        self.state.on('change:value', function (e) {
          if (self.getEl().value !== e.value) {
            self.getEl().value = e.value;
          }
        });
        self.state.on('change:disabled', function (e) {
          self.getEl().disabled = e.value;
        });
        return self._super();
      },
      remove: function () {
        this.$el.off();
        this._super();
      }
    });

    var getApi = function () {
      return {
        Selector: Selector,
        Collection: Collection$2,
        ReflowQueue: ReflowQueue,
        Control: Control$1,
        Factory: global$4,
        KeyboardNavigation: KeyboardNavigation,
        Container: Container,
        DragHelper: DragHelper,
        Scrollable: Scrollable,
        Panel: Panel,
        Movable: Movable,
        Resizable: Resizable,
        FloatPanel: FloatPanel,
        Window: Window,
        MessageBox: MessageBox,
        Tooltip: Tooltip,
        Widget: Widget,
        Progress: Progress,
        Notification: Notification,
        Layout: Layout,
        AbsoluteLayout: AbsoluteLayout,
        Button: Button,
        ButtonGroup: ButtonGroup,
        Checkbox: Checkbox,
        ComboBox: ComboBox,
        ColorBox: ColorBox,
        PanelButton: PanelButton,
        ColorButton: ColorButton,
        ColorPicker: ColorPicker,
        Path: Path,
        ElementPath: ElementPath,
        FormItem: FormItem,
        Form: Form,
        FieldSet: FieldSet,
        FilePicker: FilePicker,
        FitLayout: FitLayout,
        FlexLayout: FlexLayout,
        FlowLayout: FlowLayout,
        FormatControls: FormatControls,
        GridLayout: GridLayout,
        Iframe: Iframe$1,
        InfoBox: InfoBox,
        Label: Label,
        Toolbar: Toolbar$1,
        MenuBar: MenuBar,
        MenuButton: MenuButton,
        MenuItem: MenuItem,
        Throbber: Throbber,
        Menu: Menu,
        ListBox: ListBox,
        Radio: Radio,
        ResizeHandle: ResizeHandle,
        SelectBox: SelectBox,
        Slider: Slider,
        Spacer: Spacer,
        SplitButton: SplitButton,
        StackLayout: StackLayout,
        TabPanel: TabPanel,
        TextBox: TextBox,
        DropZone: DropZone,
        BrowseButton: BrowseButton
      };
    };
    var appendTo = function (target) {
      if (target.ui) {
        global$2.each(getApi(), function (ref, key) {
          target.ui[key] = ref;
        });
      } else {
        target.ui = getApi();
      }
    };
    var registerToFactory = function () {
      global$2.each(getApi(), function (ref, key) {
        global$4.add(key, ref);
      });
    };
    var Api = {
      appendTo: appendTo,
      registerToFactory: registerToFactory
    };

    Api.registerToFactory();
    Api.appendTo(window.tinymce ? window.tinymce : {});
    global.add('modern', function (editor) {
      FormatControls.setup(editor);
      return ThemeApi.get(editor);
    });
    function Theme () {
    }

    return Theme;

}(window));
})();
