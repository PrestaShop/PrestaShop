<div class="product-variants">
  {foreach from=$groups key=id_attribute_group item=group}
    <div class="clearfix product-variants-item">
      <span>{$group.name}</span>
      {if $group.group_type == 'select'}
        <select data-product-attribute="{$id_attribute_group}" name="group[{$id_attribute_group}]" id="group_{$id_attribute_group}">
          {foreach from=$group.attributes key=id_attribute item=group_attribute}
            <option value="{$id_attribute}" title="{$group_attribute.name}"{if $group_attribute.selected} selected="selected"{/if}>{$group_attribute.name}</option>
          {/foreach}
        </select>
      {elseif $group.group_type == 'color'}
        <ul id="group_{$id_attribute_group}">
          {foreach from=$group.attributes key=id_attribute item=group_attribute}
            <li class="pull-xs-left input-container">
              <input class="input-color" type="radio" data-product-attribute="{$id_attribute_group}" name="group[{$id_attribute_group}]" value="{$id_attribute}"{if $group_attribute.selected} checked="checked"{/if}>
              <span
                {if $group_attribute.html_color_code}class="color" style="background-color: {$group_attribute.html_color_code}" {/if}
                {if $group_attribute.texture}class="color texture" style="background-image: url({$group_attribute.texture})" {/if}
              ><span class="sr-only">{$group_attribute.name}</span></span>
            </li>
          {/foreach}
        </ul>
      {elseif $group.group_type == 'radio'}
        <ul id="group_{$id_attribute_group}">
          {foreach from=$group.attributes key=id_attribute item=group_attribute}
            <li class="input-container pull-xs-left">
              <input class="input-radio" type="radio" data-product-attribute="{$id_attribute_group}" name="group[{$id_attribute_group}]" value="{$id_attribute}"{if $group_attribute.selected} checked="checked"{/if}>
              <span class="radio-label">{$group_attribute.name}</span>
            </li>
          {/foreach}
        </ul>
      {/if}
    </div>
  {/foreach}
</div>
