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
{literal}
	<script type="text/javascript" src="http://w.sharethis.com/button/buttons.js"></script>
	<script type="text/javascript">stLight.options({publisher: "{/literal}{$conf_row}{literal}", nativeCount:false, doNotHash: false, doNotCopy: false, hashAddressBar: false});</script>
{/literal}
{if isset($addsharethis_data)}
    <div class="share">
        <strong class="dark">{l s='Share this comparison whith your friends:' mod='addsharethis'}</strong>
        {if isset($addsharethis_data.twitter)}
            {$addsharethis_data.twitter}
        {/if}
        {if isset($addsharethis_data.google)}
            {$addsharethis_data.google}
        {/if}
        {if isset($addsharethis_data.pinterest)}
            {$addsharethis_data.pinterest}
        {/if}
        {if isset($addsharethis_data.facebook)}
            {$addsharethis_data.facebook}
        {/if}
    </div>
{/if}

