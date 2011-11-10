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
*  @version  Release: $Revision: 8971 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<form action="{$current}&token={$token}" method="post" id="settings_form" name="settings_form">
	<fieldset style="width:230px;">
		<legend>
			<img src="../img/admin/tab-preferences.gif" />{l s='Settings'}
		</legend>
		<p>
			<strong>{l s='Graph engine'} </strong><br />
			{if count($array_graph_engines)}
				<select name="PS_STATS_RENDER">';
					{foreach $array_graph_engines as $k => $value}
						<option value="{$k}" {if $k == $graph_engine}selected="selected"{/if}>{$value[0]}</option>
					{/foreach}
				</select>
			{else}
				{l s='No graph engine module installed'}
			{/if}
		<p>
		
		<p>
			<strong>{l s='Grid engine'} </strong><br />
			{if count($array_grid_engines)}
				<select name="PS_STATS_GRID_RENDER">
					{foreach $array_grid_engines as $k => $value}
						<option value="{$k}" {if $k == $grid_engine}selected="selected"{/if}>{$value[0]}</option>
					{/foreach}
				</select>
			{else}
				{l s='No grid engine module installed'}
			{/if}
		</p>
		<p><input type="submit" value="{l s='   Save   '}" name="submitSettings" id="submitSettings" class="button" /></p>
	</fieldset>
</form>
<div class="clear space">&nbsp;</div>