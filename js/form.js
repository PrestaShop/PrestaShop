/*
* 2007-2013 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
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
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

function ajaxStates (id_state_selected)
{
	$.ajax({
		url: "ajax.php",
		cache: false,
		data: "ajaxStates=1&id_country="+$('#id_country').val() + "&id_state=" + $('#id_state').val(),
		success: function(html)
		{
			if (html == 'false')
			{
				$("#contains_states").fadeOut();
				$('#id_state option[value=0]').attr("selected", "selected");
			}
			else
			{
				$("#id_state").html(html);
				$("#contains_states").fadeIn();
				$('#id_state option[value=' + id_state_selected + ']').attr("selected", "selected");
			}
		}
	});

	if (module_dir && vat_number)
	{
		$.ajax({
			type: "GET",
			url: module_dir + "vatnumber/ajax.php?id_country=" + $('#id_country').val(),
			success: function(isApplicable)
			{
				if(isApplicable == 1)
					$('#vat_area').show();
				else
					$('#vat_area').hide();
			}
		});
	}
}