{capture name=path}{l s='Our stores'}{/capture}
{include file="$tpl_dir./breadcrumb.tpl"}
<h1>{l s='Our stores'}</h1>
<div id="stores_block">
{if $simplifiedStoresDiplay}
	{if $stores|@count}
	<p>{l s='Here are our store locations, please feel free to contact us:'}</p>
	{foreach $stores as $store}
		<div class="store-small">
			{if $store.has_picture}<p><img src="{$img_store_dir}{$store.id_store}-medium_default.jpg" alt="" width="{$mediumSize.width}" height="{$mediumSize.height}" /></p>{/if}
			<p>
				<b>{$store.name|escape:'htmlall':'UTF-8'}</b><br />
				{$store.address1|escape:'htmlall':'UTF-8'}<br />
				{if $store.address2}{$store.address2|escape:'htmlall':'UTF-8'}{/if}<br />
				{$store.postcode} {$store.city|escape:'htmlall':'UTF-8'}{if $store.state}, {$store.state}{/if}<br />
				{$store.country|escape:'htmlall':'UTF-8'}<br />
				{if $store.phone}{l s='Phone:' js=0} {$store.phone}{/if}
			</p>
			{if isset($store.working_hours)}{$store.working_hours}{/if}
		</div>
	{/foreach}
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
		
		var translation_1 = '{l s='No stores found, try selecting a wider radius' js=1}';
		var translation_2 = '{l s='store found - see details:' js=1}';
		var translation_3 = '{l s='stores found - see all results:' js=1}';
		var translation_4 = '{l s='Phone:' js=1}';
		var translation_5 = '{l s='Get Directions' js=1}';
		var translation_6 = '{l s='Not found' js=1}';
		
		var hasStoreIcon = '{$hasStoreIcon}';
		var distance_unit = '{$distance_unit}';
		var img_store_dir = '{$img_store_dir}';
		var img_ps_dir = '{$img_ps_dir}';
		var searchUrl = '{$searchUrl}';
		var logo_store = '{$logo_store}';
		//]]>
	</script>
	<p>{l s='Enter a location (e.g. zip/postal code, address, city or country) in order to find the nearest stores.'}</p>
	<p class="text clearfix">
		<label for="addressInput">{l s='Your location:'}</label>
		<input type="text" name="location" id="addressInput" value="{l s='Address, zip/postal code, city, state or country'}" onclick="this.value='';" />
	</p>
	<p class="select clearfix">
		<label for="radiusSelect">{l s='Radius:'}</label> 
		<select name="radius" id="radiusSelect">
			<option value="15">15</option>
			<option value="25">25</option>
			<option value="50">50</option>
			<option value="100">100</option>
		</select> <span>{$distance_unit}</span>
		<img src="{$img_ps_dir}loader.gif" class="middle" alt="" id="stores_loader" />
	</p>
	<p class="submit clearfix">
		<input type="button" class="button" onclick="searchLocations();" value="{l s='Search'}" style="display: inline;" /> 
	</p>
<select id="locationSelect"><option></option></select>
    <div id="map"></div>
	<table cellpadding="0" cellspacing="0" id="stores-table" class="std">
		<tr>
			<th>{l s='#'}</th>
			<th>{l s='Store'}</th>
			<th>{l s='Address'}</th>
			<th>{l s='Distance'}</th>
		</tr>		
	</table>
{/if}
</div>