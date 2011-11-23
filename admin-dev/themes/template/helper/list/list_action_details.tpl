{*
* 2007-2011 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision: 9597 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<script type="text/javascript">
$(document).ready(function() {
	$('#details_{$id}').click(function() {
		if (typeof(this.dataMaped) == 'undefined') {
			$.ajax({
				url: 'index.php',
				data: {
					id: '{$id}',
					controller: '{$controller}',
					token: '{$token}',
					action: '{$action}',
					ajax: true,
					{foreach $params as $key => $param}
						{$key}: {$param},
					{/foreach}
				},
				context: document.body,
				dataType: 'json',
				context: this,
				async: false,
				success: function(data) {
					if (typeof(data.use_parent_structure) == 'undefined' || (data.use_parent_structure == true))
					{
						if ($('#details_{$id}').parent().parent().hasClass('alt_row'))
							var alt_row = true;
						else
							var alt_row = false;
						$('#details_{$id}').parent().parent().after($('<tr class="details_{$id} small '+(alt_row ? 'alt_row' : '')+'"></tr>')
									.append($('<td style="border:none!important;" class="empty"></td>')
									.attr('colspan', $('#details_{$id}').parent().parent().find('td').length)));
						$.each(data.data, function(it, row)
						{
							var content = $('<tr class="action_details details_{$id} '+(alt_row ? 'alt_row' : '')+'"></tr>');
							content.append($('<td class="empty"></td>'));
							var first = true;
							var count = 0; // Number of non-empty collum
							$.each(row, function(it)
							{
								if(typeof(data.fields_display[it]) != 'undefined')
									count++;
							});
							$.each(data.fields_display, function(it, line)
							{
								if (typeof(row[it]) == 'undefined')
								{
									if (first || count == 0)
										content.append($('<td class="'+this.align+' empty"></td>'));
									else
										content.append($('<td class="'+this.align+'"></td>'));
								}
								else
								{
									count--;
									if (first)
									{
										first = false;
										content.append($('<td class="'+this.align+' first">'+row[it]+'</td>'));
									}
									else if (count == 0)
										content.append($('<td class="'+this.align+' last">'+row[it]+'</td>'));
									else
										content.append($('<td class="'+this.align+' '+count+'">'+row[it]+'</td>'));
								}
							});
							content.append($('<td class="empty"></td>'));
							$('#details_{$id}').parent().parent().after(content.show('slow'));
						});
					}
					else
					{
						if ($('#details_{$id}').parent().parent().hasClass('alt_row'))
							var content = $('<tr class="details_{$id} alt_row"></tr>');
						else
							var content = $('<tr class="details_{$id}"></tr>');
						content.append($('<td style="border:none!important;">'+data.data+'</td>').attr('colspan', $('#details_{$id}').parent().parent().find('td').length));
						$('#details_{$id}').parent().parent().after(content.show('slow'));
					}
					this.dataMaped = true;
					this.opened = false;
					initTableDnD('.details_{$id} table.tableDnD');
				}
			});
		}

		if (this.opened)
		{
			$(this).find('img').attr('src', '../img/admin/more.png');
			$(this).parent().parent().parent().find('.details_{$id}').hide('slow');
			this.opened = false
		}
		else
		{
			$(this).find('img').attr('src', '../img/admin/less.png');
			$(this).parent().parent().parent().find('.details_{$id}').show('slow');
			this.opened = true;
		}
		return false;
	});
});
</script>
<a href="#" id="details_{$id}">
	<img src="../img/admin/more.png" alt="{$action}" title="{$action}" />
</a>
