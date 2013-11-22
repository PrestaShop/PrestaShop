<ul class="lang-tabs nav nav-tabs">
    {foreach from=$htmlitems.lang.all item=lang}
		<li id="lang-{$lang.id_lang}" class="lang-flag{if $lang.id_lang == $htmlitems.lang.default.id_lang} active{/if}"><img src="../img/l/{$lang.id_lang}.jpg" class="pointer" alt="{$lang.name}" title="{$lang.name}" /> {$lang.name}</li>
    {/foreach}
</ul>
<div class="lang-items">
{foreach name=langs from=$htmlitems.items key=lang item=langItems}
	
    <div id="items-{$lang}" class="lang-content" style="display:{if $lang == $htmlitems.lang.default.id_lang}block{else}none{/if};">
    {foreach name=hooks from=$langItems key=hook item=hookItems}
    
        <h4 class="hook-title">{l s='Hook' mod='themeconfigurator'} "{$hook}"</h4>
        {if $hookItems}
            <ul id="items">
                {foreach name=items from=$hookItems item=hItem}
                    <li id="item-{$hItem.id_item}" class="item panel">
                        <span class="item-order">{if $hItem.item_order le 9}0{/if}{$hItem.item_order}</span>
                        <!--<i class="icon-sort"></i>-->
                        <span class="item-title">{$hItem.title}</span>
                        <span class="button btn btn-default button-edit pull-right"><i class="icon-edit"></i>{l s='Edit' mod='themeconfigurator'}</span>
                        <span class="button btn btn-default button-close pull-right"><i class="icon-remove"></i>{l s='Close' mod='themeconfigurator'}</span>
                        
                        <div class="item-container">
                            <form method="post" action="{$htmlitems.postAction}" enctype="multipart/form-data" class="item-form defaultForm  form-horizontal">
                            	 <input type="hidden" name="id_lang" value="{$lang}" />
                                 <input type="hidden" name="item_id" value="{$hItem.id_item}" />
                                 <input type="hidden" name="item_order" value="{$hItem.item_order}" />
                                 <div class="image-display item-field form-group">
                                    <img src="{$module_dir}images/{$hItem.image}" alt="" title="" style="width:{$hItem.image_w}px; height:{$hItem.image_h}px;{if !$hItem.image} display:none;{/if}" class="preview" />
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
                                        <input type="text" name="item_title" size="48" value="{$hItem.title}" />
                                    </div>
                                </div>
                                <div class="title_use item-field form-group">
                                    <label class="control-label col-lg-3">{l s='Use title in front' mod='themeconfigurator'}</label>
                                    <div class="col-lg-7">
                                        <input type="checkbox" name="item_title_use" value="1"{if $hItem.title_use == 1} checked="checked"{/if} />
                                    </div>
                                </div>
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
                                        <input name="item_img_w" type="text" maxlength="4" size="4" value="{$hItem.image_w}"/>
                                    </div>
                                </div>
                                <div class="image_h item-field form-group">
                                    <label class="control-label col-lg-3">{l s='Image height' mod='themeconfigurator'}</label>
                                    <div class="col-lg-7">
                                        <input name="item_img_h" type="text" maxlength="4" size="4" value="{$hItem.image_h}"/>
                                    </div>
                                </div>
                                <div class="url item-field form-group">
                                    <label class="control-label col-lg-3">{l s='URL' mod='themeconfigurator'}</label>
                                    <div class="col-lg-7">
                                        <input type="text" name="item_url" size="48" value="{$hItem.url}" />
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
                                        <textarea name="item_html" cols="65" rows="12">{$hItem.html}</textarea>
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
