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

<ul class="lang-tabs nav nav-tabs">
    {foreach from=$htmlitems.lang.all item=lang}
		<li id="lang-{$lang.id_lang|escape:'htmlall':'UTF-8'}" class="lang-flag{if $lang.id_lang == $htmlitems.lang.default.id_lang} active{/if}"><img src="../img/l/{$lang.id_lang|escape:'htmlall':'UTF-8'}.jpg" class="pointer" alt="{$lang.name|escape:'htmlall':'UTF-8'}" title="{$lang.name|escape:'htmlall':'UTF-8'}" /> {$lang.name|escape:'htmlall':'UTF-8'}</li>
    {/foreach}
</ul>
<div class="lang-items">
{foreach name=langs from=$htmlitems.items key=lang item=langItems}
	
    <div id="items-{$lang|escape:'htmlall':'UTF-8'}" class="lang-content" style="display:{if $lang == $htmlitems.lang.default.id_lang}block{else}none{/if};">
    {foreach name=hooks from=$langItems key=hook item=hookItems}
    
        <h4 class="hook-title">{l s='Hook' mod='themeconfigurator'} "{$hook|escape:'htmlall':'UTF-8'}"</h4>
        {if $hookItems}
            <ul id="items">
                {foreach name=items from=$hookItems item=hItem}
                    <li id="item-{$hItem.id_item|escape:'htmlall':'UTF-8'}" class="item panel">
                    	<div class="clearfix">
                        <span class="item-order pull-left">{if $hItem.item_order le 9}0{/if}{$hItem.item_order|escape:'htmlall':'UTF-8'}</span>
                        {if $hItem.image}
                        	
                        	<span class="pull-left">
                            	<img width="130" src="{$module_dir}img/{$hItem.image}" rel="#comments_{$hItem.id_item}" alt="" title="" class="preview" />
                            	<br /><small class="normal">{l s='Click image to enlarge/reduce' mod='themeconfigurator'}</small>
                            </span>
                            <script type="text/javascript">
								$(document).ready(function() {
									var htmlContent = $('#comments_{$hItem.id_item|escape:'htmlall':'UTF-8'}').html();
									$("[rel=#comments_{$hItem.id_item|escape:'htmlall':'UTF-8'}]").popover({
										placement : 'bottom', //placement of the popover. also can use top, bottom, left or right
										title : false, //this is the top title bar of the popover. add some basic css
										html: 'true', //needed to show html of course
										content : htmlContent  //this is the content of the html box. add the image here or anything you want really.
									});
								});
							</script>
                            <div style="display:none" id="comments_{$hItem.id_item|escape:'htmlall':'UTF-8'}">
                            	<img class="img-responsive" src="{$module_dir}img/{$hItem.image}" alt="" title="" class="preview" />
                            </div>
                        {/if}
                        <span class="item-title">{$hItem.title|escape:'htmlall':'UTF-8'}</span>
                        <span class="button btn btn-default button-edit pull-right"><i class="icon-edit"></i>{l s='Edit' mod='themeconfigurator'}</span>
                        <span class="button btn btn-default button-close pull-right"><i class="icon-remove"></i>{l s='Close' mod='themeconfigurator'}</span>
                        </div>
                        <div class="item-container">
                            <form method="post" action="{$htmlitems.postAction|escape:'htmlall':'UTF-8'}" enctype="multipart/form-data" class="item-form defaultForm  form-horizontal">
                            	 <input type="hidden" name="id_lang" value="{$lang|escape:'htmlall':'UTF-8'}" />
                                 <input type="hidden" name="item_id" value="{$hItem.id_item|escape:'htmlall':'UTF-8'}" />
                                 <input type="hidden" name="item_order" value="{$hItem.item_order|escape:'htmlall':'UTF-8'}" />
                                 <div class="image-display item-field form-group">
                                    <img src="{$module_dir}img/{$hItem.image}" alt="" title="" style="width:{$hItem.image_w}px; height:{$hItem.image_h}px;{if !$hItem.image} display:none;{/if}" class="preview" />
                                </div>
                                 <div class="active item-field form-group">
                                    <label class="control-label col-lg-3">{l s='Active' mod='themeconfigurator'}</label>
                                    <div class="col-lg-7">
                                        <input type="checkbox" name="item_active" value="1"{if $hItem.active == 1} checked="checked"{/if} />
                                    </div>
                                </div>
                                <div class="title item-field form-group">
                                    <label class="control-label col-lg-3">{l s='Title' mod='themeconfigurator'}</label>
                                    <div class="col-lg-7">
                                        <input type="text" name="item_title" size="48" value="{$hItem.title|escape:'htmlall':'UTF-8'}" />
                                    </div>
                                </div>
                                <!--
                                <div class="title_use item-field form-group">
                                    <label class="control-label col-lg-3">{l s='Use title in front' mod='themeconfigurator'}</label>
                                    <div class="col-lg-7">
                                        <input type="checkbox" name="item_title_use" value="1"{if $hItem.title_use == 1} checked="checked"{/if} />
                                    </div>
                                </div>-->
                                <div class="hook item-field form-group">
                                    <label class="control-label col-lg-3">{l s='Hook' mod='themeconfigurator'}</label>
                                    <div class="col-lg-7">
                                        <select name="item_hook" default="home">
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
                                        <input name="item_img_w" type="text" maxlength="4" size="4" value="{$hItem.image_w|escape:'htmlall':'UTF-8'}"/>
                                    </div>
                                </div>
                                <div class="image_h item-field form-group">
                                    <label class="control-label col-lg-3">{l s='Image height' mod='themeconfigurator'}</label>
                                    <div class="col-lg-7">
                                        <input name="item_img_h" type="text" maxlength="4" size="4" value="{$hItem.image_h|escape:'htmlall':'UTF-8'}"/>
                                    </div>
                                </div>
                                <div class="url item-field form-group">
                                    <label class="control-label col-lg-3">{l s='URL' mod='themeconfigurator'}</label>
                                    <div class="col-lg-7">
                                        <input type="text" name="item_url" size="48" value="{$hItem.url|escape:'htmlall':'UTF-8'}" />
                                    </div>
                                </div>
                                <div class="target item-field form-group">
                                    <label class="control-label col-lg-3">{l s='Target blank' mod='themeconfigurator'}</label>
                                    <div class="col-lg-7">
                                        <input type="checkbox" name="item_target" value="1"{if $hItem.target == 1} checked="checked"{/if} />
                                    </div>
                                </div>
                                <div class="html item-field form-group">
                                    <label class="control-label col-lg-3">{l s='HTML' mod='themeconfigurator'}</label>
                                    <div class="col-lg-7">
                                        <textarea name="item_html" cols="65" rows="12">{$hItem.html|escape:'htmlall':'UTF-8'}</textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                	<div class="col-lg-9 col-lg-offset-3">
                                        <button type="submit" name="removeItem" class="button btn btn-default button-remove" onClick="this.form.submit();"><i class="icon-remove-sign"></i>{l s='Remove' mod='themeconfigurator'}</button>
                                        <button type="submit" name="updateItem" class="button btn btn-default button-save" onClick="this.form.submit();"><i class="icon-save"></i>{l s='Save' mod='themeconfigurator'}</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </li>
                {/foreach}
            </ul>
        {else}
            <div class="item">
                {l s='No items available' mod='themeconfigurator'}
            </div>
        {/if}
    {/foreach}
	</div>
{/foreach}
</div>
