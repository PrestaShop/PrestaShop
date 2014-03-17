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

<ul class="nav nav-tabs">
	{foreach from=$htmlitems.lang.all item=lang}
		<li id="lang-{$lang.id_lang|escape:'htmlall':'UTF-8'}" class="lang-flag{if $lang.id_lang == $htmlitems.lang.default.id_lang} active{/if}">
			<a href="#items-{$lang.id_lang|escape:'htmlall':'UTF-8'}" data-toggle="tab">{$lang.name|escape:'htmlall':'UTF-8'}</a>
		</li>
	{/foreach}
</ul>
<div class="lang-items tab-content">
{foreach name=langs from=$htmlitems.items key=lang item=langItems}
	<div id="items-{$lang|escape:'htmlall':'UTF-8'}" class="lang-content tab-pane {if $smarty.foreach.langs.first}active{/if}" >
	{foreach name=hooks from=$langItems key=hook item=hookItems}
		<h4 class="hook-title">{l s='Hook' mod='themeconfigurator'} "{$hook|escape:'htmlall':'UTF-8'}"</h4>
		{if $hookItems}
			<ul id="items" class="list-unstyled">
				{foreach name=items from=$hookItems item=hItem}
					<li id="item-{$hItem.id_item|escape:'htmlall':'UTF-8'}" class="item well">
						<form method="post" action="{$htmlitems.postAction|escape:'htmlall':'UTF-8'}" enctype="multipart/form-data" class="item-form defaultForm  form-horizontal">
							<div class="btn-group pull-right">
								<button class="btn btn-default button-edit">
									<span class="button-edit-edit"><i class="icon-edit"></i> {l s='Edit' mod='themeconfigurator'}</span>
									<span class="button-edit-close hide"><i class="icon-remove"></i> {l s='Close' mod='themeconfigurator'}</span>
								</button>
								<button class="btn btn-default dropdown-toggle" data-toggle="dropdown">
									<i class="icon-caret-down"></i>
								</button>
								<ul class="dropdown-menu">
									<li>
										<a href="#" name="removeItem" class="link-item-delete">
											<i class="icon-trash"></i> {l s='Delete item' mod='themeconfigurator'}
										</a>
									</li>
								</ul>
							</div>
							<span class="item-order">{if $hItem.item_order le 9}0{/if}{$hItem.item_order|escape:'htmlall':'UTF-8'}</span>
							<span class="item-title">{$hItem.title|escape:'htmlall':'UTF-8'}</span>
							<br>
							{if $hItem.image}
								<img src="{$module_dir}img/{$hItem.image}" rel="#comments_{$hItem.id_item}" class="preview img-thumbnail" />
							{/if}
							<div class="item-container clearfix">
								<input type="hidden" name="id_lang" value="{$lang|escape:'htmlall':'UTF-8'}" />
								<input type="hidden" name="item_id" value="{$hItem.id_item|escape:'htmlall':'UTF-8'}" />
								<input type="hidden" name="item_order" value="{$hItem.item_order|escape:'htmlall':'UTF-8'}" />
								<div class="item-field form-group">
									<div class="col-lg-9 col-lg-offset-3">
										<div class="checkbox">
											<label class="control-label">
												{l s='Active' mod='themeconfigurator'}
												<input type="checkbox" name="item_active" value="1"{if $hItem.active == 1} checked="checked"{/if} />
											</label>
										</div>
									</div>
								</div>
								<div class="title item-field form-group">
									<label class="control-label col-lg-3">{l s='Title' mod='themeconfigurator'}</label>
									<div class="col-lg-7">
										<input type="text" name="item_title" value="{$hItem.title|escape:'htmlall':'UTF-8'}" />
									</div>
								</div>
								<div class="hook item-field form-group">
									<label class="control-label col-lg-3">{l s='Hook' mod='themeconfigurator'}</label>
									<div class="col-lg-7">
										<select name="item_hook" default="home" class="fixed-width-lg">
											<option value="home"{if $hItem.hook == 'home'} selected="selected"{/if}>home</option>  
											<option value="top"{if $hItem.hook == 'top'} selected="selected"{/if}>top</option>
											<option value="left"{if $hItem.hook == 'left'} selected="selected"{/if}>left</option>
											<option value="right"{if $hItem.hook == 'right'} selected="selected"{/if}>right</option>
											<option value="footer"{if $hItem.hook == 'footer'} selected="selected"{/if}>footer</option>  
										</select>
									</div>
								</div>
								<div class="image item-field form-group">
									<label class="control-label col-lg-3">{l s='Image' mod='themeconfigurator'}</label>
									<div class="col-lg-7">
										<input type="file" name="item_img" />
									</div>
								</div>
								<div class="image_w item-field form-group">
									<label class="control-label col-lg-3">{l s='Image width' mod='themeconfigurator'}</label>
									<div class="col-lg-7">
										<div class="input-group fixed-width-lg">
											<span class="input-group-addon">{l s='px'}</span>
											<input name="item_img_w" type="text" maxlength="4" size="4" value="{$hItem.image_w|escape:'htmlall':'UTF-8'}"/>
										</div>
									</div>
								</div>
								<div class="image_h item-field form-group">
									<label class="control-label col-lg-3">{l s='Image height' mod='themeconfigurator'}</label>
									<div class="col-lg-7">
										<div class="input-group fixed-width-lg">
											<span class="input-group-addon">{l s='px'}</span>
											<input name="item_img_h" type="text" maxlength="4" size="4" value="{$hItem.image_h|escape:'htmlall':'UTF-8'}"/>
										</div>
									</div>
								</div>
								<div class="url item-field form-group">
									<label class="control-label col-lg-3">{l s='URL' mod='themeconfigurator'}</label>
									<div class="col-lg-7">
										<input type="text" name="item_url" value="{$hItem.url|escape:'htmlall':'UTF-8'}" />
									</div>
								</div>
								<div class="target item-field form-group">
									<div class="col-lg-9 col-lg-offset-3">
										<div class="checkbox">
											<label class="control-label">
												{l s='Target blank' mod='themeconfigurator'}
												<input type="checkbox" name="item_target" value="1"{if $hItem.target == 1} checked="checked"{/if} />
											</label>
										</div>
									</div>
								</div>
								<div class="html item-field form-group">
									<label class="control-label col-lg-3">{l s='HTML' mod='themeconfigurator'}</label>
									<div class="col-lg-7">
										<textarea name="item_html" cols="65" rows="12">{$hItem.html|escape:'htmlall':'UTF-8'}</textarea>
									</div>
								</div>
								<div class="form-group">
									<div class="col-lg-7 col-lg-offset-3">
										<button type="button" class="btn btn-default button-item-edit-cancel" >
											<i class="icon-remove"></i> {l s='Cancel' mod='themeconfigurator'}
										</button>
										<button type="submit" name="updateItem" class="btn btn-success button-save pull-right" onClick="this.form.submit();">
											<i class="icon-save"></i> {l s='Save' mod='themeconfigurator'}
										</button>
									</div>
								</div>
							</div>
						</form>
					</li>
				{/foreach}
			</ul>
		{else}
			<div class="item">
				<span class="text-muted">{l s='No items available' mod='themeconfigurator'}</span>
			</div>
		{/if}
	{/foreach}
	</div>
{/foreach}
</div>
