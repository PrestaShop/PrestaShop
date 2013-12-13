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
<div class="alert alert-success">
    {l s='The theme %1s has been successfully installed.' sprintf=$themeName}
</div>

{if $doc|count > 0}
    <ul>
        {foreach $doc as $key => $item}
        <li><i><a target="_blank" href="{$item}">{$key}</a></i>
        {/foreach}
    </ul>
{/if}
{if isset($imgError['error'])}
    <div class="alert alert-warning">
        {l s='Warning: Copy/Paste your errors if you want to manually set the image type (in the "Images" page under the "Preferences" menu):'}
        <ul>
            {foreach $imgError['error'] as $error}
                <li>
                    {l s='Name image type:'} <strong>{$error['name']}</strong> {l s='Width:'} {$error['width']}
                    px {l s='Height:'} {$error['height']}px
                </li>
            {/foreach}
        </ul>

    </div>
{/if}
{if isset($imgError['ok'])}
    <div class="alert alert-success">
        {l s='Images have been correctly updated in database:'}
        <ul>
            {foreach $imgError['ok'] as $error}
                <li>
                    {l s='Image type:'} <strong>{$error['name']}</strong> {l s='Width:'} {$error['width']}
                    px {l s='Height:'} {$error['height']}px
                </li>
            {/foreach}
        </ul>

    </div>
{/if}

<a href="{$back_link}">
    <button class="btn btn-default">{l s='Finish'}</button>
</a>
