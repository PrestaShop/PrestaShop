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

{extends file="helpers/list/list_header.tpl"}

{block name="leadin"}
	<div id="CustomerThreadContacts" class="row">
		{assign var=nb_categories value=count($categories)}
		{foreach $categories as $key => $val}

			{assign var=total_thread value=0}
			{assign var=id_customer_thread value=0}

			{foreach $contacts as $tmp => $tmp2}
				{if $val.id_contact == $tmp2.id_contact}
					{assign var=total_thread value=$tmp2.total}
					{assign var=id_customer_thread value=$tmp2.id_customer_thread}
				{/if}
			{/foreach}
			<div class="col-lg-3">
				<div class="panel">
					<div class="panel-heading">
						{$val.name}
					</div>
					{if $nb_categories < 6}
						<p>{$val.description}</p>
					{/if}
					{if $total_thread == 0}
						<span class="message-mail">{l s='No new messages' d='Admin.Orderscustomers.Feature'}</span>
					{else}
						<a href="{$currentIndex|escape:'html':'UTF-8'}&amp;token={$token|escape:'html':'UTF-8'}&amp;id_customer_thread={$id_customer_thread}&amp;viewcustomer_thread" class="button">
							{$total_thread}
							{if $total_thread > 1}{l s='New messages' d='Admin.Orderscustomers.Feature'}{else}{l s='New message' d='Admin.Orderscustomers.Feature'}{/if}
						</a>
					{/if}
				</div>
			</div>
		{/foreach}
		<div class="col-lg-3">
			<div id="MeaningStatus" class="panel">
				<div class="panel-heading">
					{l s='Meaning of status' d='Admin.Orderscustomers.Feature'}
				</div>
				<ul class="list-unstyled">
					<li class="text-success"><i class="icon-circle"></i> {l s='Open' d='Admin.Orderscustomers.Feature'}</li>
					<li class="text-danger"><i class="icon-circle"></i> {l s='Closed' d='Admin.Orderscustomers.Feature'}</li>
					<li class="text-warning"><i class="icon-circle"></i> {l s='Pending 1' d='Admin.Orderscustomers.Feature'}</li>
					<li class="text-warning"><i class="icon-circle"></i> {l s='Pending 2' d='Admin.Orderscustomers.Feature'}</li>
				</ul>
			</div>
		</div>
		<div class="col-lg-3">
			<div id="CustomerService" class="panel">
				<div class="panel-heading">
					{l s='Statistics' d='Admin.Orderscustomers.Feature'}
				</div>
				<ul class="list-unstyled">
					{assign var=count value=0}
					{foreach $params as $key => $val}
						{assign var=count value=$count+1}
						<li>{$key} <span class="badge">{$val}</span></li>
					{/foreach}
				</ul>
			</div>
		</div>
	</div>
{/block}
