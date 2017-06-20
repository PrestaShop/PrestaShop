{**
 * 2007-2017 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}

{*
		<form action="{$smarty.server.REQUEST_URI|escape}" method="post" id="settings_form" name="settings_form" class="form-horizontal">
			<h3><i class="icon-cog"></i> {l s='Settings' d='Admin.Global'}</h3>

			<div class="form-group">
				<label for="engine_stats_render">{l s='Graph engine'} </label>
				{if count($array_graph_engines)}
					<select name="PS_STATS_RENDER" id="engine_stats_render">
						{foreach $array_graph_engines as $k => $value}
							<option value="{$k}" {if $k == $graph_engine}selected="selected"{/if}>{$value[0]}</option>
						{/foreach}
					</select>
				{else}
					{l s='No graph engine module has been installed.'}
				{/if}
			</div>

			<div class="form-group">
				<label for="engine_grid_render">{l s='Grid engine'} </label>
				{if count($array_grid_engines)}
					<select name="PS_STATS_GRID_RENDER" id="engine_grid_render">
						{foreach $array_grid_engines as $k => $value}
							<option value="{$k}" {if $k == $grid_engine}selected="selected"{/if}>{$value[0]}</option>
						{/foreach}
					</select>
				{else}
					{l s='No grid engine module has been installed.'}
				{/if}
			</div>

			<div class="form-group">
				<label for="engine_auto_clean">{l s='Auto-clean period'}</label>
				<select name="PS_STATS_OLD_CONNECT_AUTO_CLEAN" id="engine_auto_clean">
					{foreach $array_auto_clean as $k => $value}
						<option value="{$k}" {if $k == $auto_clean}selected="selected"{/if}>{$value}</option>
					{/foreach}
				</select>
			</div>

			<p>
				<input type="submit" value="{l s='Save' d='Admin.Actions'}" name="submitSettings" id="submitSettings" class="btn btn-default" />
			</p>
		</form>
*}
