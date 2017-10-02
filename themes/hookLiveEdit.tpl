{*
* 2007-2016 PrestaShop
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
*  @copyright  2007-2016 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<div rel="{$name}" id="{$id}" class="dndModule">
    <div class="toolbar">
        <div class="toolbar-content">
            <img src="{$img_src}">
            <div class="toolbar-title">{$name}</div>
        </div>
        <div class="toolbar-btn">
            <a href="#" id="{$hook_id}_{$module_id}" class="moveModule">
                <i class="material-icons md-icon">zoom_out_map</i></a>
            <a href="#" id="{$hook_id}_{$module_id}" class="unregisterHook">
                <i class="material-icons md-icon">delete_forever</i></a>
            {if $configurable}
                <a target="_blank" href="{$link_module}" id="{$hook_id}_{$module_id}" class="settingModule">
                    <i class="material-icons md-icon">settings</i></a>
            {/if}
        </div>
    </div>
    {$content nofilter}
</div>
