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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

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
    this.$element.find('label.tree-toggler, .icon-folder-close, .icon-folder-open').unbind('click');
    this.$element.find('label.tree-toggler, .icon-folder-close, .icon-folder-open').click(
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
              'index.php',
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
    this.$element.find('li').unbind('click');
    this.$element.find('li').click(
      () => {
        $('.tree-selected').removeClass('tree-selected');
        $('li input:checked').parent().addClass('tree-selected');
      },
    );

    if (typeof (idTree) !== 'undefined') {
      if ($('select#id_category_default').length) {
        this.$element.find(':input[type=checkbox]').unbind('click');
        this.$element.find(':input[type=checkbox]').click(function () {
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
        this.$element.find(':input[type=radio]').unbind('click');

        // eslint-disable-next-line
        this.$element.find(':input[type=radio]').click(treeClickFunc);
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
        'index.php',
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
