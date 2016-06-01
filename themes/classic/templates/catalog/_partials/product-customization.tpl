<section class="product-customization card card-block">
  <h3 class="h4 card-title">{l s='Product customization'}</h3>
  {l s='Don\'t forget to save your customization to be able to add to cart'}
  <form method="post" action="{$product.url}" enctype="multipart/form-data">
    <ul class="clearfix">
      {foreach from=$customizations.fields item="field"}
        <li class="product-customization-item">
          <label> {$field.label}</label>
          {if $field.type == 'text'}
            <label>{$field.text}</label>
            <textarea placeholder="{l s='Your message here'}" class="product-message" maxlength="250" {if $field.required} required {/if} name="{$field.input_name}"></textarea>
            <small class="pull-xs-right">{l s='250 char. max'}</small>
          {elseif $field.type == 'image'}
            {if $field.is_customized}
              <br>
              <img src="{$field.image.small.url}">
              <a class="remove-image" href="{$field.remove_image_url}" rel="nofollow">{l s='Remove Image'}</a>
            {/if}
            <span class="custom-file">
                            <span class="js-file-name">{l s='No selected file'}</span>
                            <input class="file-input js-file-input" {if $field.required} required {/if} type="file" name="{$field.input_name}">
                            <button class="btn btn-primary">{l s='Choose file'}</button>
                          </span>
            <small class="pull-xs-right">{l s='.png .jpg .gif'}</small>
          {/if}
        </li>
      {/foreach}
    </ul>
    <div class="clearfix">
      <button class="btn btn-primary pull-xs-right" type="submit" name="submitCustomizedDatas">{l s='Save Customization'}</button>
    </div>
  </form>
</section>
