<div class="new-item">
	<div class="form-group clearfix">
    	<span class="button btn btn-default new-item"><i class="icon-plus-sign"></i>{l s='Add item' mod='tmhtmlcontent'}</span>
    </div>
    <div class="item-container">
        <form method="post" action="{$htmlitems.postAction}" enctype="multipart/form-data" class="item-form defaultForm  form-horizontal">
        	<div class="panel">
                <div class="language item-field form-group">
                    <label class="control-label col-lg-3">{l s='Language' mod='tmhtmlcontent'}</label>
                    <div class="col-lg-7">
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" >
                            {foreach from=$htmlitems.lang.all item=lang}
                                {if $lang.id_lang == $htmlitems.lang.default.id_lang} {$lang.iso_code}{/if}
                            {/foreach}
                            <span class="caret">&nbsp;</span>
                        </button>
                        <ul class="languages dropdown-menu">
                            {foreach from=$htmlitems.lang.all item=lang}
                                <li id="lang-{$lang.id_lang}" class="new-lang-flag"><a href="javascript:hideOtherLanguage({$lang.id_lang});">{$lang.name}</a></li>
                            {/foreach}
                        </ul>
                        <input type="hidden" id="lang-id" name="lang_id" value="{$htmlitems.lang.default.id_lang}" />
                    </div>
                </div>
                <div class="title item-field form-group">
                    <label class="control-label col-lg-3 ">{l s='Title' mod='tmhtmlcontent'}</label>
                    <div class="col-lg-7">
                    	<input class="form-control" type="text" name="item_title" size="48" value="" />
                    </div>
                </div>
                <div class="title_use item-field form-group">
                    <label class="control-label col-lg-3">{l s='Use title in front' mod='tmhtmlcontent'}</label>
                    <input type="checkbox" name="item_title_use" value="1" />
                </div>
                <div class="hook item-field form-group">
                    <label class="control-label col-lg-3">{l s='Hook' mod='tmhtmlcontent'}</label>
                    <div class="col-lg-7">
                        <select class="form-control" name="item_hook" default="home">
                            <option value="home">home</option>  
                            <option value="top">top</option>
                            <option value="left">left</option>
                            <option value="right">right</option>
                            <option value="footer">footer</option>  
                        </select>
                    </div>
                </div>
                <div class="image item-field form-group">
                    <label class="control-label col-lg-3">{l s='Image' mod='tmhtmlcontent'}</label>
                    <div class="col-lg-7">
                    	<input type="file" name="item_img" />
                    </div>
                </div>
                <div class="image_w item-field form-group">
                    <label class="control-label col-lg-3">{l s='Image width' mod='tmhtmlcontent'}</label>
                    <div class="col-lg-7">
                    	<input name="item_img_w" type="text" maxlength="4" size="4" value=""/>
                    </div>
                </div>
                <div class="image_h item-field form-group">
                    <label class="control-label col-lg-3">{l s='Image height' mod='tmhtmlcontent'}</label>
                    <div class="col-lg-7">
                   		<input name="item_img_h" type="text" maxlength="4" size="4" value=""/>
                    </div>
                </div>
                <div class="url item-field form-group">
                    <label class="control-label col-lg-3">{l s='URL' mod='tmhtmlcontent'}</label>
                    <div class="col-lg-7">
                    	<input type="text" name="item_url" size="48" value="http://" />
                    </div>
                </div>
                <div class="target item-field form-group">
                    <label class="control-label col-lg-3">{l s='Target blank' mod='tmhtmlcontent'}</label>
                    <div class="col-lg-7">
                    	<input type="checkbox" name="item_target" value="1" />
                    </div>
                </div>
                <div class="html item-field form-group">
                    <label class="control-label col-lg-3">{l s='HTML' mod='tmhtmlcontent'}</label>
                    <div class="col-lg-7">
                    	<textarea name="item_html" cols="65" rows="12"></textarea>
                    </div>
                </div>
                <div class="form-group">
                	<div class="col-lg-9 col-lg-offset-3">
                		<button type="submit" name="newItem" class="button-save btn btn-default" onClick="this.form.submit();">{l s='Save' mod='tmhtmlcontent'}</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
