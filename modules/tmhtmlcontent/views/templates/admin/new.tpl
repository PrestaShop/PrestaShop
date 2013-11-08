<div class="new-item">
    <span class="button new-item"><i class="icon-plus-sign"></i>{l s='Add item' mod='tmhtmlcontent'}</span>
    <div class="item-container">
        <form method="post" action="{$htmlitems.postAction}" enctype="multipart/form-data" class="item-form">
            <div class="language item-field">
                <label>{l s='Language' mod='tmhtmlcontent'}</label>
                <ul class="languages">
                    {foreach from=$htmlitems.lang.all item=lang}
                        <li id="lang-{$lang.id_lang}" class="new-lang-flag{if $lang.id_lang == $htmlitems.lang.default.id_lang} active{/if}"><img src="../img/l/{$lang.id_lang}.jpg" class="pointer" alt="{$lang.name}" title="{$lang.name}" /></li>
                    {/foreach}
                </ul>
                <input type="hidden" id="lang-id" name="lang_id" value="{$htmlitems.lang.default.id_lang}" />
            </div>
            <div class="title item-field">
                <label>{l s='Title' mod='tmhtmlcontent'}</label>
                <input type="text" name="item_title" size="48" value="" />
            </div>
            <div class="title_use item-field">
                <label>{l s='Use title in front' mod='tmhtmlcontent'}</label>
                <input type="checkbox" name="item_title_use" value="1" />
            </div>
            <div class="hook item-field">
                <label>{l s='Hook' mod='tmhtmlcontent'}</label>
                <select name="item_hook" default="home">
                    <option value="home">home</option>  
                    <option value="top">top</option>
                    <option value="left">left</option>
                    <option value="right">right</option>
                    <option value="footer">footer</option>  
                </select>
            </div>
            <div class="image item-field">
                <label>{l s='Image' mod='tmhtmlcontent'}</label>
                <input type="file" name="item_img" />
            </div>
            <div class="image_w item-field">
                <label>{l s='Image width' mod='tmhtmlcontent'}</label><input name="item_img_w" type="text" maxlength="4" size="4" value=""/></br>
            </div>
            <div class="image_h item-field">
                <label>{l s='Image height' mod='tmhtmlcontent'}</label><input name="item_img_h" type="text" maxlength="4" size="4" value=""/>
            </div>
            <div class="url item-field">
                <label>{l s='URL' mod='tmhtmlcontent'}</label>
                <input type="text" name="item_url" size="48" value="http://" />
            </div>
            <div class="target item-field">
                <label>{l s='Target blank' mod='tmhtmlcontent'}</label>
                <input type="checkbox" name="item_target" value="1" />
            </div>
            <div class="html item-field">
            	<label>{l s='HTML' mod='tmhtmlcontent'}</label>
                <textarea name="item_html" cols="65" rows="12"></textarea>
            </div>
            <button type="submit" name="newItem" class="button button-save" onClick="this.form.submit();"><i class="icon-save"></i>{l s='Save' mod='tmhtmlcontent'}</button>
        </form>
    </div>
</div>
