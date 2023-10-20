{**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 *}

<div class="alert alert-danger">
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
                <p>{l s='You can also decide to move your images to the new storage system. In this case, click on the "%move_images_label%" button below. Please be patient. This can take several minutes.' d='Admin.Design.Notification' sprintf=['%move_images_label%' => {l s='Move images' d='Admin.Design.Feature'}]}</p>
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

	<form id="display_regenerate_form" class="form-horizontal" action="{$current|escape:'html':'UTF-8'}&amp;token={$token|escape:'html':'UTF-8'}" method="post">
		<div class="panel">
			<h3>
                <i class="icon-picture"></i>
                {l s='Regenerate thumbnails' d='Admin.Design.Feature'}
            </h3>

			<div class="alert alert-warning">
				{l s='Be careful! Depending on the options selected, former manually uploaded thumbnails might be erased and replaced by automatically generated thumbnails.' d='Admin.Design.Notification'}<br />
				{l s='Also, regenerating thumbnails for all existing images can take several minutes, please be patient.' d='Admin.Design.Notification'}
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
						<input type="radio" name="erase" id="erase_on" value="1">
						<label for="erase_on" class="radioCheck">
							{l s='Yes' d='Admin.Global'}
						</label>
						<input type="radio" name="erase" id="erase_off" value="0" checked="checked">
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
        <input type="hidden" name="submitRegenerate{$table}" value="" />
				<button
          type="submit"
          value=""
          class="btn btn-default pull-right"
        >
					<i class="process-icon-cogs"></i> {l s='Regenerate thumbnails' d='Admin.Design.Feature'}
				</button>
			</div>
		</div>
	</form>

  <script type="text/javascript">
    $(function() {
      $('#display_regenerate_form button[type="submit"]').on('click', function() {
        $('#modalRegenerateThumbnails').modal('show');
        return false;
      });
      $('.btn-regenerate-thumbnails').on('click', function () {
        $('#display_regenerate_form').trigger('submit');
      });
      $('.btn-confirm-delete-images-type').on('click', function () {
        document.location = $(this).attr('data-confirm-url') + '&delete_linked_images=' + $('#delete_linked_images').is(":checked");
      });

      $('#modalConfirmDeleteType ').on('hidden.bs.modal', function () {
        $('.modal-checkbox input', this).prop('checked', false)
      });
    });
  </script>
{/if}
