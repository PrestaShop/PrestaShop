/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

/**
 * The tree's AJAX calls used a bare 'index.php', which the browser resolves against the CURRENT path.
 * That is the admin dispatcher only on a legacy page; from a Symfony route such as
 * /<admin>/improve/modules/manage/action/configure/ it resolves to
 * /<admin>/improve/modules/manage/action/configure/index.php, which does not exist.
 * baseAdminDir is published by both back office layouts - AdminController::setMedia() for the legacy one
 * and the HeadTag Twig component for the Symfony one - and already ends with a slash.
 */
function adminDispatcherUrl() {
  return (typeof window.baseAdminDir !== 'undefined' ? window.baseAdminDir : '') + 'index.php';
}

window.Tree = function (element, options) {
  this.$element = $(element);
  this.options = $.extend({}, $.fn.tree.defaults, options);
  this.init();
};

Tree.prototype = {
  constructor: Tree,

  init() {
    const name = this.$element.parent().find('ul.tree input').first().attr('name');
    const idTree = this.$element.parent().find('.cattree.tree').first().attr('id');
    this.$element.find('label.tree-toggler, .icon-folder-close, .icon-folder-open').off('click');
    this.$element.find('label.tree-toggler, .icon-folder-close, .icon-folder-open').on(
      'click',
      function () {
        if ($(this).parent().parent().children('ul.tree')
          .is(':visible')) {
          $(this).parent().children('.icon-folder-open')
            .removeClass('icon-folder-open')
            .addClass('icon-folder-close');

          $(this).trigger('collapse');
          $(this).parent().parent().children('ul.tree')
            .toggle(300);
        } else {
          $(this).parent().children('.icon-folder-close')
            .removeClass('icon-folder-close')
            .addClass('icon-folder-open');

          const loadTree = (typeof (idTree) !== 'undefined' && $(this).parent().closest('.tree-folder')
            .find('ul.tree .tree-toggler')
            .first()
            .html() === '');

          if (loadTree) {
            const category = $(this).parent().children('ul.tree input').first()
              .val();
            const inputType = $(this).parent().children('ul.tree input').first()
              .attr('type');

            const useCheckBox = inputType === 'checkbox' ? 1 : 0;

            $.get(
              adminDispatcherUrl(),
              {
                ajax: 1,
                controller: 'AdminProducts',
                token: currentToken,
                action: 'getCategoryTree',
                type: idTree,
                category,
                inputName: name,
                useCheckBox,
              },
              (content) => {
                const targetTree = $(`#${idTree}`);
                $(this).parent().closest('.tree-folder').find('ul.tree')
                  .html(content);
                targetTree.tree('collapse', $(this).closest('.tree-folder').children('ul.tree'));
                $(this).trigger('expand');
                $(this).parent().parent().children('ul.tree')
                  .toggle(300);
                targetTree.tree('init');
              },
            );
          } else {
            $(this).trigger('expand');
            $(this).parent().parent().children('ul.tree')
              .toggle(300);
          }
        }
      },
    );
    this.$element.find('li').off('click');
    this.$element.find('li').on(
      'click',
      () => {
        $('.tree-selected').removeClass('tree-selected');
        $('li input:checked').parent().addClass('tree-selected');
      },
    );

    if (typeof (idTree) !== 'undefined') {
      if ($('select#id_category_default').length) {
        this.$element.find(':input[type=checkbox]').off('click');
        this.$element.find(':input[type=checkbox]').on('click', function () {
          // eslint-disable-next-line
          if ($(this).prop('checked')) addDefaultCategory($(this));
          else {
            $(`select#id_category_default option[value=${$(this).val()}]`).remove();
            if ($('select#id_category_default option').length === 0) {
              $('select#id_category_default').closest('.form-group').hide();
              $('#no_default_category').show();
            }
          }
        });
      }
      if (typeof (treeClickFunc) !== 'undefined') {
        this.$element.find(':input[type=radio]').off('click');

        // eslint-disable-next-line
        this.$element.find(':input[type=radio]').on('click', treeClickFunc);
      }
    }

    return $(this);
  },

  collapse(elem, $speed) {
    elem.find('label.tree-toggler').each(
      function () {
        $(this).parent().children('.icon-folder-open')
          .removeClass('icon-folder-open')
          .addClass('icon-folder-close');
        $(this).parent().parent().children('ul.tree')
          .hide($speed);
      },
    );

    return $(this);
  },

  collapseAll($speed) {
    this.$element.find('label.tree-toggler').each(
      function () {
        $(this).parent().children('.icon-folder-open')
          .removeClass('icon-folder-open')
          .addClass('icon-folder-close');
        $(this).parent().parent().children('ul.tree')
          .hide($speed);
      },
    );

    return $(this);
  },

  expandAll($speed) {
    const idTree = this.$element.parent().find('.cattree.tree').first().attr('id');
    const targetTree = $(`#${idTree}`);

    if (typeof (idTree) !== 'undefined' && !targetTree.hasClass('full_loaded')) {
      const selected = [];
      targetTree.find('.tree-selected input').each(
        function () {
          selected.push($(this).val());
        },
      );
      const name = targetTree.find('ul.tree input').first().attr('name');
      const inputType = targetTree.find('ul.tree input').first().attr('type');

      const useCheckBox = inputType === 'checkbox' ? 1 : 0;

      const data = {
        ajax: 1,
        controller: 'AdminProducts',
        token: currentToken,
        action: 'getCategoryTree',
        type: idTree,
        fullTree: 1,
        selected,
        inputName: name,
        useCheckBox,
      };

      // Fetch the first category of the select
      if (selected.length > 0) {
        data.category = selected[0];
      }

      $.get(
        adminDispatcherUrl(),
        data,
        (content) => {
          targetTree.html(content);
          targetTree.tree('init');
          targetTree.find('label.tree-toggler').each(
            function () {
              $(this).parent().children('.icon-folder-close')
                .removeClass('icon-folder-close')
                .addClass('icon-folder-open');
              $(this).parent().parent().children('ul.tree')
                .show($speed);
              targetTree.addClass('full_loaded');
            },
          );
        },
      );
    } else {
      this.$element.find('label.tree-toggler').each(
        function () {
          $(this).parent().children('.icon-folder-close')
            .removeClass('icon-folder-close')
            .addClass('icon-folder-open');
          $(this).parent().parent().children('ul.tree')
            .show($speed);
        },
      );
    }

    return $(this);
  },
};

$.fn.tree = function (option, value) {
  let methodReturn;
  const $set = this.each(
    function () {
      const $this = $(this);
      let data = $this.data('tree');
      const options = typeof option === 'object' && option;

      if (!data) {
        $this.data('tree', (data = new Tree(this, options)));
      }
      if (typeof option === 'string') {
        methodReturn = data[option](value);
      }
    },
  );

  return (methodReturn === undefined) ? $set : methodReturn;
};

$.fn.tree.Constructor = Tree;
