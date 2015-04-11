var Tree = function (element, options)
{
	this.$element = $(element);
	this.options = $.extend({}, $.fn.tree.defaults, options);
	this.init();
};

Tree.prototype =
{
	constructor: Tree,

	init: function ()
	{
		var that = $(this);
		this.$element.find("label.tree-toggler, .icon-folder-close, .icon-folder-open").unbind('click');
		this.$element.find("label.tree-toggler, .icon-folder-close, .icon-folder-open").click(
			function ()
			{
				if ($(this).parent().parent().children("ul.tree").is(":visible"))
				{
					$(this).parent().children(".icon-folder-open")
						.removeClass("icon-folder-open")
						.addClass("icon-folder-close");

					that.trigger('collapse');
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
						var thatOne = $(this);
						$.get(
							'ajax-tab.php',
							{controller:'AdminProducts',token:currentToken,action:'getCategoryTree',type:idTree,category:category},
							function(content)
							{
								thatOne.parent().closest('.tree-folder').find('ul.tree').html(content);
								$('#'+idTree).tree('collapse', thatOne.closest('.tree-folder').children("ul.tree"));
								that.trigger('expand');
								thatOne.parent().parent().children("ul.tree").toggle(300);
								$('#'+idTree).tree('init');
							}
						);
					}
					else
					{
						that.trigger('expand');
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

			this.$element.find(":input[type=radio]").unbind('click');
			this.$element.find(":input[type=radio]").click(
				function()
				{
					location.href = location.href.replace(
						/&id_category=[0-9]*/, "")+"&id_category="
						+$(this).val();
				}
			);
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
		if (typeof(idTree) != 'undefined' && typeof(full_loaded) == 'undefined')
		{
			var selected = [];
			that = this;
			$('#'+idTree).find('.tree-selected input').each(
				function()
				{
					selected.push($(this).val());
				}
			);
			$.get(
				'ajax-tab.php',
				{controller:'AdminProducts',token:currentToken,action:'getCategoryTree',type:idTree,fullTree:1,selected:selected},
				function(content)
				{
					$('#'+idTree).html(content);
					$('#'+idTree).tree('init');
					that.$element.find("label.tree-toggler").each(
						function()
						{
							$(this).parent().children(".icon-folder-close")
								.removeClass("icon-folder-close")
								.addClass("icon-folder-open");
							$(this).parent().parent().children("ul.tree").show($speed);
							full_loaded = true;
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