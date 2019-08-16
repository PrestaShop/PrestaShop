{**
 * 2007-2019 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}
<div class="modal fade" id="{$modal_id}" tabindex="-1">
	<div class="modal-dialog {if isset($modal_class)}{$modal_class}{/if}">
		<div class="modal-content">
			{if isset($modal_title)}
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">{$modal_title}</h4>
			</div>
			{/if}

			{$modal_content}

			{if isset($modal_actions)}
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">{l s='Close'}</button>
				{foreach $modal_actions as $action}
					{if $action.type == 'link'}
						<a href="{$action.href}" class="btn {$action.class}">{$action.label}</a>
					{elseif $action.type == 'button'}
						<button type="button" value="{$action.value}" class="btn {$action.class}">
							{$action.label}
						</button>
					{/if}
				{/foreach}
			</div>
			{/if}
		</div>
	</div>
</div>
