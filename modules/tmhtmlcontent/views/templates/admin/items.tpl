<ul class="lang-tabs">
    {foreach from=$htmlitems.lang.all item=lang}
		<li id="lang-{$lang.id_lang}" class="lang-flag{if $lang.id_lang == $htmlitems.lang.default.id_lang} active{/if}"><img src="../img/l/{$lang.id_lang}.jpg" class="pointer" alt="{$lang.name}" title="{$lang.name}" /> {$lang.name}</li>
    {/foreach}
</ul>
<div class="lang-items">
{foreach name=langs from=$htmlitems.items key=lang item=langItems}
	
    <div id="items-{$lang}" class="lang-content" style="display:{if $lang == $htmlitems.lang.default.id_lang}block{else}none{/if};">
    {foreach name=hooks from=$langItems key=hook item=hookItems}
    
        <h3 class="hook-title">{l s='Hook' mod='tmhtmlcontent'} "{$hook}"</h3>
        {if $hookItems}
            <ul id="items">
                {foreach name=items from=$hookItems item=hItem}
                    <li id="item-{$hItem.id_item}" class="item">
                        <span class="item-order">{if $hItem.item_order le 9}0{/if}{$hItem.item_order}</span>
                        <!--<i class="icon-sort"></i>-->
                        <span class="item-title">{$hItem.title}</span>
                        <span class="button button-edit"><i class="icon-edit"></i>{l s='Edit' mod='tmhtmlcontent'}</span>
                        <span class="button button-close"><i class="icon-remove"></i>{l s='Close' mod='tmhtmlcontent'}</span>
                        
                        <div class="item-container">
                            <form method="post" action="{$htmlitems.postAction}" enctype="multipart/form-data" class="item-form">
                                <input type="hidden" name="lang_id" value="{$lang}" />
                                <input type="hidden" name="item_id" value="{$hItem.id_item}" />
                                <input type="hidden" name="item_order" value="{$hItem.item_order}" />
                                <div class="active item-field">
                                    <label>{l s='Active' mod='tmhtmlcontent'}</label>
                                    <input type="checkbox" name="item_active" value="1"{if $hItem.active == 1} checked="checked"{/if} />
                                </div>
                                <div class="title item-field">
                                    <label>{l s='Title' mod='tmhtmlcontent'}</label>
                                    <input type="text" name="item_title" size="48" value="{$hItem.title}" />
                                </div>
                                <div class="title_use item-field">
                                    <label>{l s='Use title in front' mod='tmhtmlcontent'}</label>
                                    <input type="checkbox" name="item_title_use" value="1"{if $hItem.title_use == 1} checked="checked"{/if} />
                                </div>
                                <div class="hook item-field">
                                    <label>{l s='Hook' mod='tmhtmlcontent'}</label>
                                    <select name="item_hook" default="home">
                                        <option value="home"{if $hItem.hook == 'home'} selected="selected"{/if}>home</option>  
                                        <option value="top"{if $hItem.hook == 'top'} selected="selected"{/if}>top</option>
                                        <option value="left"{if $hItem.hook == 'left'} selected="selected"{/if}>left</option>
                                        <option value="right"{if $hItem.hook == 'right'} selected="selected"{/if}>right</option>
                                        <option value="footer"{if $hItem.hook == 'footer'} selected="selected"{/if}>footer</option>  
                                    </select>
                                </div>
                                <div class="image item-field">
                                    <label>{l s='Image' mod='tmhtmlcontent'}</label>
                                    <input type="file" name="item_img" />
                                </div>
                                <div class="image-display item-field">
                                    <img src="{$module_dir}images/{$hItem.image}" alt="" title="" style="width:{$hItem.image_w}px; height:{$hItem.image_h}px;{if !$hItem.image} display:none;{/if}" class="preview" />
                                </div>
                                <div class="image_w item-field">
                                    <label>{l s='Image width' mod='tmhtmlcontent'}</label>
                                    <input name="item_img_w" type="text" maxlength="4" size="4" value="{$hItem.image_w}"/></br>
                                </div>
                                <div class="image_h item-field">
                                    <label>{l s='Image height' mod='tmhtmlcontent'}</label>
                                    <input name="item_img_h" type="text" maxlength="4" size="4" value="{$hItem.image_h}"/>
                                </div>
                                <div class="url item-field">
                                    <label>{l s='URL' mod='tmhtmlcontent'}</label>
                                    <input type="text" name="item_url" size="48" value="{$hItem.url}" />
                                </div>
                                <div class="target item-field">
                                    <label>{l s='Target blank' mod='tmhtmlcontent'}</label>
                                    <input type="checkbox" name="item_target" value="1"{if $hItem.target == 1} checked="checked"{/if} />
                                </div>
                                <div class="html item-field">
                                    <label>{l s='HTML' mod='tmhtmlcontent'}</label>
                                    <textarea name="item_html" cols="65" rows="12">{$hItem.html}</textarea>
                                </div>
                                <button type="submit" name="removeItem" class="button button-remove" onClick="this.form.submit();"><i class="icon-remove-sign"></i>{l s='Remove' mod='tmhtmlcontent'}</button>
                                <button type="submit" name="updateItem" class="button button-save" onClick="this.form.submit();"><i class="icon-save"></i>{l s='Save' mod='tmhtmlcontent'}</button>
                            </form>
                        </div>
                    </li>
                {/foreach}
            </ul>
        {else}
            <div class="item">
                {l s='No items available' mod='tmhtmlcontent'}
            </div>
        {/if}
    {/foreach}
	</div>
{/foreach}
</div>
