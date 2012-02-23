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
*  @version  Release: $Revision: 11894 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<script type="text/javascript">
$('document').ready(function(){
	$('#mailalerts_block_extra_add').click(function(){
		$.ajax({
			url: "{$link->getModuleLink('mailalerts', 'actions', ['process' => 'add'])}",
			type: "POST",
			data: {
				"id_product": {$smarty.get.id_product}
			},
			success: function(result){
				if (result == '0')
				{
			    	$('#mailalerts_block_extra_add').slideUp(function() {
			    		$('#mailalerts_block_extra_added').slideDown("slow");
			    	});
			    	
				}
		 	}
		});
	});
	$('#mailalerts_block_extra_remove').click(function(){
		$.ajax({
			url: "{$link->getModuleLink('mailalerts', 'actions', ['process' => 'remove'])}",
			type: "POST",
			data: {
				"id_product": {$smarty.get.id_product}
			},
			success: function(result){
				if (result == '0')
				{
			    	$('#mailalerts_block_extra_remove').slideUp(function() {
			    		$('#mailalerts_block_extra_removed').slideDown("slow");
			    	});

				}
		 	}
		});
	});
	$('#mailalerts_block_extra_added').click(function(){
		$.ajax({
			url: "{$link->getModuleLink('mailalerts', 'actions', ['process' => 'remove'])}",
			type: "POST",
			data: {
				"id_product": {$smarty.get.id_product}
			},
			success: function(result){
				if (result == '0')
				{
			    	$('#mailalerts_block_extra_added').slideUp(function() {
			    		$('#mailalerts_block_extra_removed').slideDown("slow");
			    	});

				}
		 	}
		});
	});
	$('#mailalerts_block_extra_removed').click(function(){
		$.ajax({
			url: "{$link->getModuleLink('mailalerts', 'actions', ['process' => 'add'])}",
			type: "POST",
			data: {
				"id_product": {$smarty.get.id_product}
			},
			success: function(result){
				if (result == '0')
				{
			    	$('#mailalerts_block_extra_removed').slideUp(function() {
			    		$('#mailalerts_block_extra_added').slideDown("slow");
			    	});

				}
		 	}
		});
	});
})
</script>

{if !$isCustomerMailAlert AND $isLogged}
<li id="mailalerts_block_extra_add" class="add">
	{l s='Add this product to my favorites' mod='mailalerts'}
</li>
{/if}
{if $isCustomerMailAlert AND $isLogged}
<li id="mailalerts_block_extra_remove">
	{l s='Remove this product from my favorites' mod='mailalerts'}
</li>
{/if}

<li id="mailalerts_block_extra_added">
	{l s='Remove this product from my favorites' mod='mailalerts'}
</li>
<li id="mailalerts_block_extra_removed">
	{l s='Add this product to my favorites' mod='mailalerts'}
</li>