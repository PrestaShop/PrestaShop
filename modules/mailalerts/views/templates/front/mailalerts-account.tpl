{*
* 2007-2012 PrestaShop 
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
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision: 8084 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<script type="text/javascript">
$('document').ready(function()
{
	$('img[rel^=ajax_id_mailalert_]').click(function()
	{
		var ids =  $(this).attr('rel').replace('ajax_id_mailalert_', '');
		ids = ids.split('_');
		var id_product_mail_alert = ids[0];
		var id_product_attribute_mail_alert = ids[1];
		var parent = $(this).parent().parent();

		$.ajax({
			url: "{$link->getModuleLink('mailalerts', 'actions', ['process' => 'remove'])}",
			type: "POST",
			data: {
				'id_product': id_product_mail_alert,
				'id_product_attribute': id_product_attribute_mail_alert
			},
			success: function(result)
			{
				if (result == '0')
				{
					parent.fadeOut("normal", function()
					{
						parent.remove();
					});
				}
 		 	}
		});
	});
});
</script>

{capture name=path}<a href="{$link->getPageLink('my-account', true)}">{l s='My account' mod='mailalerts'}</a><span class="navigation-pipe">{$navigationPipe}</span>{l s='My alerts' mod='mailalerts'}{/capture}
{include file="$tpl_dir./breadcrumb.tpl"}

<div id="mailalerts_block_account">
	<h2>{l s='My alerts' mod='mailalerts'}</h2>
	{if $mailAlerts}
		<div>
			{foreach from=$mailAlerts item=mailAlert}
			<div class="mailalert clearfix">
				<a href="{$link->getProductLink($mailAlert.id_product, null, null, null, null, $mailAlert.id_shop)}" class="product_img_link"><img src="{$link->getImageLink($mailAlert.link_rewrite, $mailAlert.cover, 'small')}" alt=""/></a>
				<h3><a href="{$link->getProductLink($mailAlert.id_product, null, null, null, null, $mailAlert.id_shop)}">{$mailAlert.name}</a></h3>
				<div class="product_desc">{$mailAlert.attributes_small|escape:'htmlall':'UTF-8'}</div>

				<div class="remove">
					<img rel="ajax_id_mailalert_{$mailAlert.id_product}_{$mailAlert.id_product_attribute}" src="{$img_dir}icon/delete.gif" alt="" class="icon" />
				</div>
			</div>
			{/foreach}
		</div>
	{else}
		<p class="warning">{l s='No mail alerts yet.' mod='mailalerts'}</p>
	{/if}

	<ul class="footer_links">
		<li class="fleft"><a href="{$link->getPageLink('my-account', true)}"><img src="{$img_dir}icon/my-account.gif" alt="" class="icon" /></a><a href="{$link->getPageLink('my-account', true)}">{l s='Back to Your Account' mod='mailalerts'}</a></li>
	</ul>
</div>