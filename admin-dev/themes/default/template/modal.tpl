{*
* 2007-2014 PrestaShop
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
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<div class="modal fade" id="{$modal_id}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog {if isset($modal_class)}{$modal_class}{/if}">
		<div class="modal-content">
			{if isset($modal_title)}
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="myModalLabel">{$modal_title}</h4>
			</div>
			{/if}
			<div class="modal-body">
				{$modal_content}
			</div>
			{if isset($modal_actions)}
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">{l s='Close'}</button>
				{foreach $modal_actions as $action}
					<a href="{$action.link}" class="btn {$action.class}">{$action.label}</a>
				{/foreach}
			</div>
			{/if}
		</div>
	</div>
</div>