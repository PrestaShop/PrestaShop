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
var Tree = function (element, options)
{
	this.$element = $(element);
	this.options = $.extend({}, $.fn.tree.defaults, options);
	this.init();
};

function getCategoryById(param) {
	var elem = null;
	$('input[name=id_parent]').each(function (index) {
		if ($(this).val() === param + '') {
			elem = $(this);
		}
	});
	return elem;
}

function disableTreeItem(item) {
	item.find('input[name=id_parent]').attr('disabled', 'disabled');
	if (item.hasClass('tree-folder')) {
		item.find('span.tree-folder-name').addClass('tree-folder-name-disable');
		item.find('ul li').each(function (index) {
			disableTreeItem($(this));
		});
	} else if (item.hasClass('tree-item')) {
		item.addClass('tree-item-disable');
	}
}

function organizeTree() {
	if ($('#id_category').length != 0) {
		var id = $('#id_category').val();
		var item = getCategoryById(id).parent().parent();
		disableTreeItem(item);
	}
}

Tree.prototype =
{
	constructor: Tree,

	init: function ()
	{
		var name = this.$element.parent().find('ul.tree input').first().attr('name');
		var idTree = this.$element.parent().find('.cattree.tree').first().attr('id');
		this.$element.find("label.tree-toggler, .icon-folder-close, .icon-folder-open").unbind('click');
		this.$element.find("label.tree-toggler, .icon-folder-close, .icon-folder-open").click(
			function ()
			{
				if ($(this).parent().parent().children("ul.tree").is(":visible"))
				{
					$(this).parent().children(".icon-folder-open")
						.removeClass("icon-folder-open")
						.addClass("icon-folder-close");

					$(this).trigger('collapse');
					$(this).parent().parent().children("ul.tree").toggle(300);
				}
				else
				{
					$(this).parent().children(".icon-folder-close")
						.removeClass("icon-folder-close")
						.addClass("icon-folder-open");

					var load_tree = (typeof(idTree) != 'undefined'
									 && $(this).parent().closest('.tree-folder').find('ul.tree .tree-toggler').first().html() == '');
					if (load_tree)
					{
						var category = $(this).parent().children('ul.tree input').first().val();
						var inputType = $(this).parent().children('ul.tree input').first().attr('type');
						var useCheckBox = 0;
						if (inputType == 'checkbox')
						{
							useCheckBox = 1;
						}

						$.get(
							'ajax-tab.php',
							{ controller:'AdminProducts', token:currentToken, action:'getCategoryTree', type:idTree, category:category, inputName:name, useCheckBox:useCheckBox},
							function(content)
							{
								$(this).parent().closest('.tree-folder').find('ul.tree').html(content);
								$('#'+idTree).tree('collapse', $(this).closest('.tree-folder').children("ul.tree"));
								$(this).trigger('expand');
								$(this).parent().parent().children("ul.tree").toggle(300);
								$('#'+idTree).tree('init');
							}
						);
					}
					else
					{
						$(this).trigger('expand');
						$(this).parent().parent().children("ul.tree").toggle(300);
					}
				}
			}
		);
		this.$element.find("li").unbind('click');
		this.$element.find("li").click(
			function ()
			{
				$('.tree-selected').removeClass("tree-selected");
				$('li input:checked').parent().addClass("tree-selected");
			}
		);

		if (typeof(idTree) != 'undefined')
		{
			if ($('select#id_category_default').length)
			{
				this.$element.find(':input[type=checkbox]').unbind('click');
				this.$element.find(':input[type=checkbox]').click(function()
				{
					if ($(this).prop('checked'))
						addDefaultCategory($(this));
					else
					{
						$('select#id_category_default option[value=' + $(this).val() + ']').remove();
						if ($('select#id_category_default option').length == 0)
						{
							$('select#id_category_default').closest('.form-group').hide();
							$('#no_default_category').show();
						}
					}
				});
			}
			if (typeof(treeClickFunc) != 'undefined')
			{
				this.$element.find(":input[type=radio]").unbind('click');
				this.$element.find(":input[type=radio]").click(treeClickFunc);
			}
		}

		return $(this);
	},

	collapse : function(elem, $speed)
	{
		elem.find("label.tree-toggler").each(
			function()
			{
				$(this).parent().children(".icon-folder-open")
					.removeClass("icon-folder-open")
					.addClass("icon-folder-close");
				$(this).parent().parent().children("ul.tree").hide($speed);
			}
		);

		return $(this);
	},

	collapseAll : function($speed)
	{
		this.$element.find("label.tree-toggler").each(
			function()
			{
				$(this).parent().children(".icon-folder-open")
					.removeClass("icon-folder-open")
					.addClass("icon-folder-close");
				$(this).parent().parent().children("ul.tree").hide($speed);
			}
		);

		return $(this);
	},

	expandAll : function($speed)
	{
		var idTree = this.$element.parent().find('.cattree.tree').first().attr('id');
		if (typeof(idTree) != 'undefined' && !$('#'+idTree).hasClass('full_loaded'))
		{
			var targetTree = $('#'+idTree);
			var selected = [];
			targetTree.find('.tree-selected input').each(
				function()
				{
					selected.push($(this).val());
				}
			);
			var name = targetTree.find('ul.tree input').first().attr('name');
			var inputType = targetTree.find('ul.tree input').first().attr('type');
			var useCheckBox = 0;
			if (inputType == 'checkbox')
			{
				useCheckBox = 1;
			}

			$.get(
				'ajax-tab.php',
				{ controller:'AdminProducts', token:currentToken, action:'getCategoryTree', type:idTree, fullTree:1, selected:selected, inputName:name, useCheckBox:useCheckBox },
				function(content)
				{
					targetTree.html(content);
          // Function organizeTree() disabled because it no longer has known usage - Crezzur
					// organizeTree();
					targetTree.tree('init');
					targetTree.find("label.tree-toggler").each(
						function()
						{
							$(this).parent().children(".icon-folder-close")
								.removeClass("icon-folder-close")
								.addClass("icon-folder-open");
							$(this).parent().parent().children("ul.tree").show($speed);
							targetTree.addClass('full_loaded');
						}
					);
				}
			);
		}
		else
		{
			this.$element.find("label.tree-toggler").each(
				function()
				{
					$(this).parent().children(".icon-folder-close")
						.removeClass("icon-folder-close")
						.addClass("icon-folder-open");
					$(this).parent().parent().children("ul.tree").show($speed);
				}
			);
		}

		return $(this);
	}
};

$.fn.tree = function (option, value)
{
	var methodReturn;
	var $set = this.each(
		function ()
		{
			var $this = $(this);
			var data = $this.data('tree');
			var options = typeof option === 'object' && option;

			if (!data){
				$this.data('tree', (data = new Tree(this, options)));
			}
			if (typeof option === 'string') {
				methodReturn = data[option](value);
			}
		}
	);

	return (methodReturn === undefined) ? $set : methodReturn;
};

$.fn.tree.Constructor = Tree;
