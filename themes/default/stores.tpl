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

{capture name=path}{l s='Our stores'}{/capture}

<h1 class="page-heading">{l s='Our stores'}</h1>

{if $simplifiedStoresDiplay}
	{if $stores|@count}
	<p class="store-title"><strong class="dark">{l s='Here you can find our store locations. Please feel free to contact us:'}</strong></p>
    <table class="table table-bordered">
    	<thead>
            	<tr>
                	<th class="logo">{l s='Logo'}</th>
                    <th class="name">{l s='Store name'}</th>
                    <th class="address">{l s='Store address'}</th>
                    <th class="store-hours">{l s='Working hours'}</th>
                </tr>
            </thead>
	{foreach $stores as $store}
		<tr class="store-small">
        	
			<td class="logo">{if $store.has_picture}<div class="store-image"><img src="{$img_store_dir}{$store.id_store}-stores_default.jpg" alt="" /></div>{/if}</td>
			<td class="name">{$store.name|escape:'htmlall':'UTF-8'}</td>
            <td class="address">
				{$store.address1|escape:'htmlall':'UTF-8'}
				{if $store.address2}{$store.address2|escape:'htmlall':'UTF-8'}{/if}
				{$store.postcode} {$store.city|escape:'htmlall':'UTF-8'}{if $store.state}, {$store.state}{/if}
				{$store.country|escape:'htmlall':'UTF-8'}
				{if $store.phone}{l s='Phone:' js=0} {$store.phone}{/if}
			</td>
            <td class="store-hours">
				{if isset($store.working_hours)}{$store.working_hours}{/if}
            </td>
		</tr>
	{/foreach}
    </table>
	{/if}
{else}
	<script type="text/javascript">
		// <![CDATA[
		var map;
		var markers = [];
		var infoWindow;
		var locationSelect;

		var defaultLat = '{$defaultLat}';
		var defaultLong = '{$defaultLong}';
		
		var translation_1 = '{l s='No stores were found. Please try selecting a wider radius.' js=1}';
		var translation_2 = '{l s='store found -- see details:' js=1}';
		var translation_3 = '{l s='stores found -- view all results:' js=1}';
		var translation_4 = '{l s='Phone:' js=1}';
		var translation_5 = '{l s='Get directions' js=1}';
		var translation_6 = '{l s='Not found' js=1}';
		
		var hasStoreIcon = '{$hasStoreIcon}';
		var distance_unit = '{$distance_unit}';
		var img_store_dir = '{$img_store_dir}';
		var img_ps_dir = '{$img_ps_dir}';
		var searchUrl = '{$searchUrl}';
		var logo_store = '{$logo_store}';
		//]]>
	</script>
	<div id="map"></div>
	<p class="store-title"><strong class="dark">{l s='Enter a location (e.g. zip/postal code, address, city or country) in order to find the nearest stores.'}</strong></p>
    <div class="store-content">
        <div class="address-input">
            <label for="addressInput">{l s='Your location:'}</label>
            <input class="form-control grey" type="text" name="location" id="addressInput" value="{l s='Address, zip / postal code, city, state or country'}" onclick="this.value='';" />
        </div>
        <div class="radius-input">
            <label for="radiusSelect">{l s='Radius:'}</label> 
            <select name="radius" id="radiusSelect" class="form-control">
                <option value="15">15</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
            <img src="{$img_ps_dir}loader.gif" class="middle" alt="" id="stores_loader" />
        </div>
        <div>
            <button onclick="searchLocations();" class="button btn btn-default button-small"><span>{l s='Search'}<i class="icon-chevron-right right"></i></span></button>
        </div>
    </div>
    <div class="store-content-select selector3"><select id="locationSelect" class="form-control"><option></option></select></div>

	<table cellpadding="0" cellspacing="0" border="0" id="stores-table" class="table table-bordered">
    	<thead>
			<tr>
                <th class="num">{l s='#'}</th>
                <th>{l s='Store'}</th>
                <th>{l s='Address'}</th>
                <th>{l s='Distance'}</th>
            </tr>		
        </thead>
        <tbody>
        </tbody>
	</table>
{/if}
