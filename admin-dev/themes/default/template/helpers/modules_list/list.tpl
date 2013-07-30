{*
* 2007-2013 PrestaShop
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
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<legend>{l s='Modules list'}</legend>
<div id="modules_list_container_tab" class="row">
	<div class="col-lg-12">
		{if count($modules_list)}
			<table border="0" cellpadding="0" cellspacing="5" class="table table-striped table-hover">
				{counter start=1  assign="count"}
					{foreach from=$modules_list item=module}	
						<div style="width:400px">{include file='controllers/modules/tab_module_line.tpl' class_row={cycle values=",row alt"}}</div>
						{if $count %3 == 0}
			</li>
			<li>
						{/if}
					{counter}
				{/foreach}
			</table>
		{else}
			<table border="0" cellpadding="0" cellspacing="5" class="table table-striped table-hover">
				<tr>
					<td>
						<div class="alert alert-block">{l s='No modules available in this section.'}</div>
					</td>
				</tr>
			</table>
		{/if}
	</div>
</div>
