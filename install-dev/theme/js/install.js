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

$(function()
{
	$('#mainForm').on('submit', function() {
		$('#btNext').hide();
	});

	// Ajax animation
	$("#loaderSpace").ajaxStart(function()
	{
		$(this).fadeIn('slow');
		$(this).children('div').fadeIn('slow');
	});

	$("#loaderSpace").ajaxComplete(function(e, xhr, settings)
	{
		$(this).fadeOut('slow');
		$(this).children('div').fadeOut('slow');
	});

	$('select.chosen').not('.no-chosen').chosen();

	// try to pre-compile the smarty templates
	function compile_smarty_templates(bo)
	{
		$.ajax(
		{
			url: 'index.php',
			data: {
				'compile_templates': 1,
				'bo':bo
			},
			global: false
		});
	}
	compile_smarty_templates(1);
	compile_smarty_templates(0);
});
