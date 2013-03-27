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

{extends file="helpers/form/form.tpl"}

{block name="input"}

    {if $input.type == 'cms_blocks'}

        <script type="text/javascript">
            var come_from = '{$name_controller}';
            var token = '{$token}';
            var alternate = 1;
        </script>

        {assign var=cms_blocks_positions value=$input.values}
        {if isset($cms_blocks_positions) && count($cms_blocks_positions) > 0}

            {foreach $cms_blocks_positions as $key => $cms_blocks_position}
                <div style="float:left;{if $key == 0}margin-right:2em;{/if}">

                    <h3 style="margin-top:1px;">
                        {if $key == 0}
                            {l s='Left blocks' mod='blockcms'}
                        {else}
                            {l s='Right blocks' mod='blockcms'}
                        {/if}
                    </h3>

                    <table cellspacing="0" cellpadding="0" style="min-width:40em;" class="table tableDnD cms" id="cms_block_{$key%2}">
                        <thead>
                            <tr class="nodrag nodrop">
                                <th>{l s='ID' mod='blockcms'}</th>
                                <th>{l s='Name of the block' mod='blockcms'}</th>
                                <th>{l s='Category name' mod='blockcms'}</th>
                                <th>{l s='Position' mod='blockcms'}</th>
                                <th>{l s='Actions' mod='blockcms'}</th>
                            </tr>
                        </thead>
                        <tbody>
                            {foreach $cms_blocks_position as $key => $cms_block}
                                <tr class="{if $key%2}alt_row{else}not_alt_row{/if} row_hover" id="tr_{$key%2}_{$cms_block['id_cms_block']}_{$cms_block['position']}">
                                    <td>{$cms_block['id_cms_block']}</td>
                                    <td>{$cms_block['block_name']}</td>
                                    <td>{$cms_block['category_name']}</td>
                                    <td class="center pointer dragHandle" id="td_{$key%2}_{$cms_block['id_cms_block']}">
                                        <a
                                            {if (($key == (sizeof($cms_blocks_position) - 1)) || (sizeof($cms_blocks_position) == 1))}
                                                    style="display: none;"
                                            {/if}
                                                    href="{$current}&configure=blockcms&id_cms_block={$cms_block['id_cms_block']}&way=1&position={(int)$cms_block['position'] + 1}&location=0&token={$token}">
                                            <img src="{$smarty.const._PS_ADMIN_IMG_}down.gif" alt="{l s='Down' mod='blockcms'}" title="{l s='Down' mod='blockcms'}" />
                                        </a>
                                        <a
                                            {if (($cms_block['position'] == 0) || ($key == 0))}
                                                    style="display: none;"
                                            {/if}
                                                    href="{$current}&configure=blockcms&id_cms_block={$cms_block['id_cms_block']}&way=0&position={(int)$cms_block['position'] - 1}&location=0&token={$token}">
                                            <img src="{$smarty.const._PS_ADMIN_IMG_}up.gif" alt="{l s='Up' mod='blockcms'}" title="{l s='Up' mod='blockcms'}" />
                                        </a>
                                    </td>
                                    <td>
                                        <a href="{$current}&token={$token}&editBlockCMS&id_cms_block={(int)$cms_block['id_cms_block']}" title="{l s='Edit' mod='blockcms'}"><img src="{$smarty.const._PS_ADMIN_IMG_}edit.gif" alt="" /></a>
                                        <a href="{$current}&token={$token}&deleteBlockCMS&id_cms_block={(int)$cms_block['id_cms_block']}" title="{l s='Delete' mod='blockcms'}"><img src="{$smarty.const._PS_ADMIN_IMG_}delete.gif" alt="" /></a>
                                    </td>
                                </tr>
                            {/foreach}
                        </tbody>

                    </table>

                </div>
            {/foreach}
        {/if}
    {elseif $input.type == 'select_category'}

        {function name=render_select level=0}
            {foreach $items as $item}
                {if (isset($item['id_cms_category']))}
                    <option id="category_{$item['id_cms_category']}" value="{$item['id_cms_category']}"
                        {if (isset($fields_value['id_category']) && ($item['id_cms_category'] == $fields_value['id_category']))}
                            selected
                        {/if}
                    >
                        {str_repeat('&nbsp;', $level * 4)|cat:$item['name']}
                    </option>

                    {if isset($item['sub_categories']) && count($item['sub_categories']) > 0}
                        {call name=render_select items=$item['sub_categories'] level=$level+1}
                    {/if}
                {/if}
            {/foreach}
        {/function}

        {if isset($input.options.query) && count($input.options.query) > 0}
            {assign var=categories value=$input.options.query}

            <select id="{$input.name}" name="{$input.name}">
                {call render_select items=$categories}
            </select>
        {/if}
    {elseif $input.type == 'cms_pages'}

        {assign var=cms value=$input.values}
        {if isset($cms) && count($cms) > 0}

            <table cellspacing="0" cellpadding="0" class="table" style="min-width:40em;">
                <tr>
                    <th>
                        <input type="checkbox" name="checkme" id="checkme" class="noborder" onclick="checkDelBoxes(this.form, '{$input.name}', this.checked)" />
                    </th>
                    <th>{l s='ID' mod='blockcms'}</th>
                    <th>{l s='Name' mod='blockcms'}</th>
                </tr>

                {foreach $cms as $key => $cms_category}
                    <tr {if $key%2}class="alt_row"{/if}>
                        <td>
                            {assign var=id_checkbox value=1|cat:'_'|cat:$cms_category['id_cms_category']}
                            <input type="checkbox" class="cmsBox" name="{$input.name}" id="{$id_checkbox}" value="{$id_checkbox}" {if isset($fields_value[$id_checkbox])}checked="checked"{/if} />
                        </td>
                        <td>
                            <strong>{$cms_category['id_cms_category']}</strong>
                        </td>
                        <td><label for="{$id_checkbox}" class="t"><strong>{str_repeat('&nbsp;', ($cms_category['level_depth'] - 1) * 4)|cat:$cms_category['name']}</strong></label></td>
                    </tr>

                    {foreach $cms_category['cms_pages'] as $subkey => $cms_page}
                        <tr class="subitem{if ($subkey+$key-1)%2} alt_row{/if}">
                            <td>
                                {assign var=id_checkbox value=0|cat:'_'|cat:$cms_page['id_cms']}
                                <input type="checkbox" class="cmsBox" name="{$input.name}" id="{$id_checkbox}" value="{$id_checkbox}" {if isset($fields_value[$id_checkbox])}checked="checked"{/if} />
                            </td>
                            <td>
                                {$cms_page['id_cms']}
                            </td>
                            <td><label for="{$id_checkbox}" class="t">{str_repeat('&nbsp;', $cms_category['level_depth'] * 4)|cat:$cms_page['meta_title']}</label></td>
                        </tr>
                    {/foreach}

                {/foreach}

            </table>
            {else}
            <p>{l s='No pages have been created.' mod='blockcms'}</p>
        {/if}
	{else}
		{$smarty.block.parent}
    {/if}

{/block}
