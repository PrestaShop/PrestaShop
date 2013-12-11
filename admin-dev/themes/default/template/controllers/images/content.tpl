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
{if isset($content)}
	{if isset($show_page_header_toolbar) && $show_page_header_toolbar &&(!isset($lite_display) || !$lite_display)}
		<div class="leadin">
			{foreach from=$page_header_toolbar_btn item=btn key=k}
				{if $k == 'modules-list'}
				<div class="modal fade" id="modules_list_container">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
								<h3 class="modal-title">{l s='Modules'}</h3>
							</div>
							<div class="modal-body">
								<div id="modules_list_container_tab" style="display:none;"></div>
								<div id="modules_list_loader"><i class="icon-refresh icon-large icon-spin"></i> {l s='Loading'}</div>
							</div>
						</div>
					</div>
				</div>
				{/if}
			{/foreach}
		</div>
		{include file="page_header_toolbar.tpl" toolbar_btn=$page_header_toolbar_btn title=$page_header_toolbar_title}
	{/if}
	{$content}
{/if}

{if isset($display_regenerate)}

	<form class="form-horizontal" action="{$current}&token={$token}" method="post">
		<div class="panel">
			<h3>
                <i class="icon-picture"></i>
                {l s='Regenerate thumbnails'}
            </h3>

			<div class="alert alert-info">
				{l s='Regenerates thumbnails for all existing images'}<br />
				{l s='Please be patient. This can take several minutes.'}<br />
				{l s='Be careful! Manually uploaded thumbnails will be erased and replaced by automatically generated thumbnails.'}
			</div>
			
			<div class="form-group">
				<label class="control-label col-lg-3">{l s='Select an image'}</label>
				<div class="col-lg-9">
					<select name="type" onchange="changeFormat(this)">
						<option value="all">{l s='All'}</option>
						{foreach $types AS $k => $type}
							<option value="{$k}">{$type}</option>
						{/foreach}
					</select>
				</div>
			</div>

			{foreach $types AS $k => $type}
			<div class="form-group second-select format_{$k}" style="display:none;">			
				<label class="control-label col-lg-3">{l s='Select a format'}</label>
				<div class="col-lg-9 margin-form">
					<select name="format_{$k}">
						<option value="all">{l s='All'}</option>
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
					{l s='Erase previous images'}
				</label>
				<div class="col-lg-9">
					<div class="row">
						<div class="input-group col-lg-2">
							<span class="switch prestashop-switch">
								<input type="radio" name="erase" id="erase_on" value="1" checked="checked">
								<label for="erase_on" class="radioCheck">
									<i class="icon-check-sign color_success"></i> {l s='Yes'}
								</label>
								<input type="radio" name="erase" id="erase_off" value="0">
								<label for="erase_off" class="radioCheck">
									<i class="icon-ban-circle color_danger"></i> {l s='No'}
								</label>
								<a class="slide-button btn btn-default"></a>
							</span>
						</div>
					</div>
					<p class="help-block">
						{l s='Select "No" only if your server timed out and you need to resume the regeneration.'}
					</p>
				</div>
			</div>
			<div class="form-group">
				<div class="col-lg-9 col-lg-push-3">
					<input type="Submit" name="submitRegenerate{$table}" value="{l s='Regenerate thumbnails'}" class="btn btn-default" onclick="return confirm('{l s='Are you sure?'}');" />
				</div>
			</div>
		</div>
	</form>
{/if}

{if isset($display_move) && $display_move}
	{if $safe_mode}
        <div class="alert alert-warning">
            <p>{l s='PrestaShop has detected that your server configuration is not compatible with the new storage system (directive "safe_mode" is activated). You should therefore continue to use the existing system.'}</p>
        </div>
    {else}
        <form action="{$current}&token={$token}" method="post" class="form-horizontal">
            <div class="panel">
                <h3>
                    <i class="icon-picture"></i>
                    {l s='Move images'}
                </h3>
                <div class="alert alert-warning">
                    <p>{l s='You can choose to keep your images stored in the previous system. There\'s nothing wrong with that.'}</p>
                    <p>{l s='You can also decide to move your images to the new storage system. In this case, click on the "Move images" button below. Please be patient. This can take several minutes.'}</p>
                </div>
                <div class="alert alert-info">&nbsp;
                    {l s='After moving all of your product images, set the "Use the legacy image filesystem" option above to "No" for best performance.'}
                </div>
                <div class="row">
                    <div class="col-lg-12 pull-right">
                        <input type="Submit" name="submitMoveImages{$table}" value="{l s='Move images'}" class="btn btn-default" onclick="return confirm('{l s='Are you sure?'}');" />
                    </div>
                </div>
            </div>
        </form>
    {/if}
{/if}