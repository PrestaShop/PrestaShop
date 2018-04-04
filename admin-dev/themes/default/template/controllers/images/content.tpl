{**
 * 2007-2018 PrestaShop
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
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}

<div class="alert alert-warning">
	{l s='By default, all images settings are already installed in your store. Do not delete them, you will need it!' d='Admin.Design.Help'}
</div>

{if isset($content)}
	{$content}
{/if}

{if isset($display_move) && $display_move}
    <form action="{$current|escape:'html':'UTF-8'}&amp;token={$token|escape:'html':'UTF-8'}" method="post" class="form-horizontal">
        <div class="panel">
            <h3>
                <i class="icon-picture"></i>
                {l s='Move images' d='Admin.Design.Feature'}
            </h3>
            <div class="alert alert-warning">
                <p>{l s='You can choose to keep your images stored in the previous system. There\'s nothing wrong with that.' d='Admin.Design.Notification'}</p>
                <p>{l s='You can also decide to move your images to the new storage system. In this case, click on the "Move images" button below. Please be patient. This can take several minutes.' d='Admin.Design.Notification'}</p>
            </div>
            <div class="alert alert-info">&nbsp;
                {l s='After moving all of your product images, set the "Use the legacy image filesystem" option above to "No" for best performance.' d='Admin.Design.Notification'}
            </div>
            <div class="row">
                <div class="col-lg-12 pull-right">
                    <button type="submit" name="submitMoveImages{$table}" class="btn btn-default pull-right" onclick="return confirm('{l s='Are you sure?' d='Admin.Notifications.Warning'}');"><i class="process-icon-cogs"></i> {l s='Move images' d='Admin.Design.Feature'}</button>
                </div>
            </div>
        </div>
    </form>
{/if}

{if isset($display_regenerate)}

	<form class="form-horizontal" action="{$current|escape:'html':'UTF-8'}&amp;token={$token|escape:'html':'UTF-8'}" method="post">
		<div class="panel">
			<h3>
                <i class="icon-picture"></i>
                {l s='Regenerate thumbnails' d='Admin.Design.Feature'}
            </h3>

			<div class="alert alert-info">
				{l s='Regenerates thumbnails for all existing images' d='Admin.Design.Help'}<br />
				{l s='Please be patient. This can take several minutes.' d='Admin.Design.Help'}<br />
				{l s='Be careful! Manually uploaded thumbnails will be erased and replaced by automatically generated thumbnails.' d='Admin.Design.Help'}
			</div>

			<div class="form-group">
				<label class="control-label col-lg-3">{l s='Select an image' d='Admin.Design.Feature'}</label>
				<div class="col-lg-9">
					<select name="type" onchange="changeFormat(this)">
						<option value="all">{l s='All' d='Admin.Global'}</option>
						{foreach $types AS $k => $type}
							<option value="{$k}">{$type}</option>
						{/foreach}
					</select>
				</div>
			</div>

			{foreach $types AS $k => $type}
			<div class="form-group second-select format_{$k}" style="display:none;">
				<label class="control-label col-lg-3">{l s='Select a format' d='Admin.Design.Feature'}</label>
				<div class="col-lg-9 margin-form">
					<select name="format_{$k}">
						<option value="all">{l s='All' d='Admin.Global'}</option>
						{foreach $formats[$k] AS $format}
							<option value="{$format['id_image_type']}">{$format['name']}</option>
						{/foreach}
					</select>
				</div>
			</div>
			{/foreach}
			<script>
				function changeFormat(elt)
				{ldelim}
					$('.second-select').hide();
					$('.format_' + $(elt).val()).show();
				{rdelim}
			</script>

			<div class="form-group">
				<label class="control-label col-lg-3">
					{l s='Erase previous images' d='Admin.Design.Feature'}
				</label>
				<div class="col-lg-9">
					<span class="switch prestashop-switch fixed-width-lg">
						<input type="radio" name="erase" id="erase_on" value="1" checked="checked">
						<label for="erase_on" class="radioCheck">
							{l s='Yes' d='Admin.Global'}
						</label>
						<input type="radio" name="erase" id="erase_off" value="0">
						<label for="erase_off" class="radioCheck">
							{l s='No' d='Admin.Global'}
						</label>
						<a class="slide-button btn"></a>
					</span>
					<p class="help-block">
						{l s='Select "No" only if your server timed out and you need to resume the regeneration.' html=1 d='Admin.Design.Help'}
					</p>
				</div>
			</div>
			<div class="panel-footer">
				<button type="submit" name="submitRegenerate{$table}" class="btn btn-default pull-right" onclick="return confirm('{l s='Are you sure?' d='Admin.Notifications.Warning'}');">
					<i class="process-icon-cogs"></i> {l s='Regenerate thumbnails' d='Admin.Design.Feature'}
				</button>
			</div>
		</div>
	</form>
{/if}
