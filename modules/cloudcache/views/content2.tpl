<script type="text/javascript" src="{$base_dir}modules/cloudcache/script.js"></script>
<link rel="stylesheet" type="text/css" href="{$base_dir}modules/cloudcache/style.css" />
<script type="text/javascript">
    var apikey = '{$apiKey|escape}';
  {literal}
$(document).ready(function()
		  {
		  $('#loading').hide();
		      $("#SubmitCloudcacheSettings").click(function()
		      					   {
							       if ($("#cloudcache_api_user").val() != '' && $("#cloudcache_api_key").val() != '')
							       {
								   if (apikey == '')
								       return true;
								   if (confirm("Are you sure ?"))
								       return true;
							       }
							       return false;
							   });
		  });
{/literal}
</script>
<div style="float:right;width:400px;margin-bottom:10px">
  <fieldset>
    <h3>{l s='Opening your CloudCache.com Account' mod='cloudcache'}</h3>
    <p>{l s='CloudCache provides Prestashop users an exclusive discount of 25% per month on every available package. Please click the logo below to sign up: ' mod='cloudcache'}</p><br/>
    <a href="http://www.cloudcache.com/prestashop"><img style="width: 370px;" src="{$base_dir}modules/cloudcache/cloudcache_ps_logo.png" alt="cloudcache"/></a>
  </fieldset>
</div>
<div style="width:450px">
	<div style="float: left; width: 150px">
	<img style="width:150px; float:left; margin:25px 20px 0px 0px;" src="../modules/cloudcache/cloudcache_logo.png" alt="cloudcache"/>
	</div>
	<div style="float: left; width: 250px; margin: 25px;">
		<b>{l s='This module allows you to acclerate your PrestaShop via the CloudCache Content Delivery Network.' mod='cloudcache'}</b><br /><br />
		{l s='A content Delivery Network (CDN) is for every site owner who demands a high performance, primo visitor experience.' mod='cloudcache'}
	</div>
</div>
<br/>
<br/>
{if isset($confirmMessage)}
<div style="clear:both" class="conf {$confirmMessage['class']}">
  <img src="../img/admin/{$confirmMessage['img']}" alt="" title="" />
  {$confirmMessage['text']}
</div>
{/if}
{if isset($compatibilityIssues)}
<fieldset style="clear:both" class="width6 cloudcache_fieldset">
  <legend>
    <img src="../img/admin/statsettings.gif" alt="" />
    {l s='CloudCache Compatibilty Check' mod='cloudcache'}
  </legend>
  <p>
    <div class="warn">
      <img src="../img/admin/warn2.png" />
      {foreach from=$compatibilityIssues key=i item=message}
      {if $i > 0}{$i|strip_tags} - {/if}{$message|strip_tags}<br />
      {/foreach}
    </div>
  </p>
</fieldset><br />
{else}
<ul id="menuTab" style="clear:both">
  <li id="menuTab1" class="menuTabButton selected">{l s='Why CloudCache' mod='cloudcache'}</li>
  <li id="menuTab2" class="menuTabButton">{l s='Help' mod='cloudcache'}</li>
  <li id="menuTab3" class="menuTabButton">{l s='Settings' mod='cloudcache'}</li>
  {if isset($couponUrl) && isset($apiActive) && $apiActive == 1}
  <li id="menuTab4" class="menuTabButton">{l s='Zones' mod='cloudcache'}</li>
  {/if}
</ul>
<div id="tabList">
  <div id="menuTab1Sheet" class="tabItem selected">
    <p>{l s='CloudCache is focused on delivering your web content at light speed efficiency. We keep a copy of the “heavy” objects from your PrestaShop, like images, CSS and JavaScript on our datacenters around the world and deliver these to your visitors from the closest location to them. Here are numerous benefits of having a faster loading site: ' mod='cloudcache'}</p>
{*
    <ul>
      <li><b>{l s='Thrive During Traffic Spikes!' mod='cloudcache'}</b><p style="display: block;">{l s='In case you become famous overnight (i.e your shop is mentioned on TV), your sales would skyrocket, but your server might crash from the load; we help you to load balance your traffic.' mod='cloudcache'}</p><br /></li>
      <li><p><b style="display: block;">{l s='Custom SSL made Easy' mod='cloudcache'}</b><br /></p>{l s='Our SSL acceleration offloads up to 70% of your server CPU power. Plus we made it super easy and affordable to run SSL on the cloud. We only charge $24.95 per Custom SSL Zone, per month including the SSL certificate.' mod='cloudcache'}</li>
      <li><p><b>{l s='Higher SEO Rankings' mod='cloudcache'}</b><br /></p><p>{l s='Google uses page speed as a key factor in their ranking algorithm. Faster loading pages earn higher rankings in search results, which means more traffic and more money for you.' mod='cloudcache'}</p></li>
      <li><p><b>{l s='Increase your Conversions' mod='cloudcache'}</b></p><p>{l s='Amazon found that if your site loads 100 milliseconds slower they lose 1% of their revenue. We think that this reason alone is enough to worry about page load time.' mod='cloudcache'}</p></li>
    </ul> *}

<ul style="float: right; width: 45%;">
<li style="margin-bottom: 10px;"><b>Thrive During Traffic Spikes!</b><br />In case you become famous overnight (i.e your shop is mentioned on TV), your sales would skyrocket, but your server might crash from the load; we help you to load balance your traffic.</li>
<li><b>Custom SSL made Easy</b><br />Our SSL acceleration offloads up to 70% of your server CPU power. Plus we made it super easy and affordable to run SSL on the cloud. We only charge $24.95 per Custom SSL Zone, per month including the SSL certificate.</li>
</ul>
<ul style="float: right; width: 45%">
<li style="margin-bottom: 10px;"><b>Higher SEO Rankings</b><br />Google uses page speed as a key factor in their ranking algorithm. Faster loading pages earn higher rankings in search results, which means more traffic and more money for you.<br /></li>
<li><b>Increase your Conversions</b><br />Amazon found that if your site loads 100 milliseconds slower they lose 1% of their revenue. We think that this reason alone is enough to worry about page load time.<br /></li>
</ul>

<div style="clear:both;"></div>

  </div>
  <div id="menuTab2Sheet" class="tabItem">
    <h3>{l s='How to configure CloudCache' mod='cloudcache'}</h3>
    - {l s='Subscribe to CloudCache by clicking on the logo on the right' mod='cloudcache'}<br />
    - {l s='Create an API user and retrieve the User ID and the API key' mod='cloudcache'} (<a style="color:blue;text-decoration:underline" href="http://www.youtube.com/watch?v=YfKFkq-J2CU">{l s='see how-to video' mod='cloudcache'}</a>)<br /><br />
    <h3>{l s='How to configure the Module:' mod='cloudcache'}</h3>
    - {l s='Fill the CloudCache User ID and API key fields with those provided by CloudCache.' mod='cloudcache'}<br />
    - {l s='Click on the "Save Settings" button and then Test your connection.' mod='cloudcache'}<br />
    - {l s='If you do not have existing zones, a default zone will be created.' mod='cloudcache'}<br />
    - {l s='In order to increase the performance, you can go to your CloudCache account by clicking on the link at the right, go to the Pullzones list, choose your zone, edit it, go to the Advanced Settings and uncheck the "Query String" option. In order to save the change, click on the "Update".' mod='cloudcache'}<br /><br />
{*    <h3>{l s='Module purpose:' mod='cloudcache'}</h3>
    {l s='A Content Delivery Network (CDN) is for every site owner who demands a high performance, primo visitor experience. Why? Because our CDN delivers your downloadables – images, video, scripts, css – with super-fast load times. ' mod='cloudcache'}<br /><br /> *}
    <h3>{l s='What modifications does the module do on my store?' mod='cloudcache'}</h3>
    - {l s='Tools.php will be overwritten.' mod='cloudcache'}<br />
    - {l s='[Preferences Tab -> Performance -> Media Servers] will display a link to the CloudCache module configurations and a notification when the module is active.' mod='cloudcache'}<br />

  </div>
  <div id="menuTab3Sheet" class="tabItem">
    <form action="{$serverRequestUri|strip_tags}&id_tab=3" method="post">
      {if isset($connectionTestResult)}
      <div id="test_connection" style="background: {$connectionTestResult[1]};" >
	{$connectionTestResult[0]}<br />
	{if $connectionTestResult[1] == '#FFD8D8'}
	<b style="color: red">{l s='An error occured while testing the connection.' mod='cloudcache'}</b>
	{elseif isset($connectionTestResult['newZone'])}
	<b style="color: green;">{l s='Success! That is all you have to do!' mod='cloudcache'} {$connectionTestResult['newZone']|escape} {l s='is now accelerated by cloudcache.com' mod='cloudcache'}</b>
	{else}
	<b style="color: green;">{l s='Success! The connection have been established!' mod='cloudcache'}</b>
	{/if}<br />
      </div>
      {/if}
    <div style="height: 0px;">
      <div style="display: block; float: right; margin-top: 14px;">
	{if !empty($companyId)}
	<fieldset>
	  <legend>CloudCache</legend>
	  <a href="http://login.cloudcache.com">
	    {l s='Click Here to access your CloudCache account' mod='cloudcache'}
	  </a>
	</fieldset>
	{/if}
      </div>
      <div style="clear: both;"></div>
      {*if isset($couponUrl) && isset($apiActive) && $apiActive == 1}
      <div class="prepaid-bandwidth MT20" style="display: block;">
	<b>{l s='Your available bandwidth is:' mod='cloudcache'}</b><br />
	<div class="bandwidth-value">{$prepaidBandwith|escape} Tb left</div>
      </div>
      {/if*}
    </div>
      <table border="0" cellspacing="5">
	{if 0 && !empty($companyId)}<tr>
	  <td class="cloudcache_column">{l s='Company ID' mod='cloudcache'}</td>
	  <td><input type="text" name="cloudcache_api_company_id" value="{if isset($companyId)}{$companyId|escape}{/if}" /></td>
      </tr> {/if}
	<tr>
	  <td class="cloudcache_column">{l s='API User' mod='cloudcache'}</td>
	  <td><input type="text" name="cloudcache_api_user" id="cloudcache_api_user" value="{if isset($apiUser)}{$apiUser|escape}{/if}" /></td>
	</tr>
	<tr>
	  <td class="cloudcache_column">{l s='API Key' mod='cloudcache'}</td>
	  <td><input type="{if isset($apiKey) && !empty($apiKey)}password{else}text{/if}" id="cloudcache_api_key" name="cloudcache_api_key" value="{if isset($apiKey)}{$apiKey|escape}{/if}"/></td>
      </tr>
      </table>
      <center><input type="submit" id="SubmitCloudcacheSettings" class="button cloudcache_button" name="SubmitCloudcacheSettings" value="{l s='Save Settings' mod='cloudcache'}" /></center>
      <hr size="1" style="margin: 14px auto;" noshade />
      <center><img src="../img/admin/exchangesrate.gif" alt="" /> <input type="submit" onClick="$('#loading').show();" id="cloudcache_test_connection" class="button cloudcache_button" name="SubmitCloudcacheTestConnection" value="{l s='Click here to Test Connection' mod='cloudcache'}" style="margin-top: 0;" /><div id="loading" >{l s='Loading' mod='cloudcache'} <img src="{$base_dir|escape}modules/cloudcache/loading.gif" style="height: 25px;" /></div></center>
    </form>
  </div>
  {if isset($couponUrl) && isset($apiActive) && $apiActive == 1}
  <div id="menuTab4Sheet" class="tabItem">

    <!-- Menu -->
    {if !empty($zones)}
    <ul class="tabbed-form menu">
      {foreach from=$allAvailableZones key=type item=name}
      <li>{$name|escape}</li>
      {/foreach}
    </ul>
    <ul class="tabbed-form content" style="margin-top: 23px">
      <li id="tab1_content">
	<table style="display: inline-block;" class="spaced-table2">
	  <tr>
	    <th>Name</th>
	    <th>File Type</th>
	    <th>Origin</th>
	    <th>Vanity URL</th>
	  {*  <th>Label</th> *}
	    <th>Compress</th>
	    <th>BW Last Month</th>
	    <th>BW Last Week</th>
	    <th>BW Yesterday</th>
	    <th style="min-width: 150px;"></th>
	  </tr>
	  {foreach from=$zones key=id_zone item=zone}
	  <tr align="left" valign="top">
	    <td>{$zone['name']|escape}</td>
	    <td>{if $zone['file_type'] eq null or $zone['file_type'] eq 'none' or $zone['file_type'] eq '0'}{l s='N/A' mod='cloudcache'}{else}{$zone['file_type']|upper|escape}{/if}</td>
	    <td><div class="jexcerpt-short">{$zone['origin']|substr:0:10|escape}{if $zone['origin']|strlen > 10}...{/if}</div>{if $zone['origin']|strlen > 10}<div class="jexcerpt-long">{$zone['origin']}</div>{/if}</td>
	    <td><span class="jexcerpt-short">{$zone['cdn_url']|substr:0:10|escape}{if $zone['cdn_url']|strlen > 10}...{/if}</span>{if $zone['cdn_url']|strlen > 10}<span class="jexcerpt-long">{$zone['cdn_url']|escape}</span>{/if}</td>
	    {*	    <td>{if isset($zone.label)}{$zone['label']|escape}{/if}</td> *}
	    <td>{if isset($zone['compress']) && $zone['compress'] == 1}{l s='YES' mod='cloudcache'}{else}{l s='NO' mod='cloudcache'}{/if}</td>
    <td>{if $zone.bw_last_month != -1}{$zone['bw_last_month']|escape}{else}{l s='N/A' mod='cloudcache'}{/if}</td>
	    <td>{if $zone.bw_last_week != -1}{$zone['bw_last_week']|escape}{else}{l s='N/A' mod='cloudcache'}{/if}</td>
	    <td>{if $zone.bw_yesterday != -1}{$zone['bw_yesterday']|escape}{else}{l s='N/A' mod='cloudcache'}{/if}</td>

	    <td>
	      <form method="post" action="{$serverRequestUri|strip_tags}&id_tab=4">
		<input type="hidden" value="{$id_zone|escape}" name="id_zone"/>
		<input type="hidden" value="" name="CloudcacheZone_action" class="CloudcacheZone_action"/>
		<input type="submit" name="SubmitCloudcacheEditZoneAction" value="Edit" class="button"/>
		<input type="submit" name="SubmitCloudcacheClearZoneCache" value="{l s='Purge Cache' mod='cloudcache'}" class="button"/>
	      </form>
	    </td>
	  </tr>
	  {/foreach}
	</table>
      </li>
    </ul>
    {/if}
    <form method="post" action="{$serverRequestUri|strip_tags}&id_tab=4" class="MT20">
      <div class="R">
	<input type="submit" class="button MB10" id="SubmitCloudcacheSync" name="SubmitCloudcacheSync" value="{l s='Sync Zones!' mod='cloudcache'}" />
	{if !isset($edit_zone_info)}
	<input type="submit" class="button MB10" id="cloudcache_add_zone" name="cloudcache_add_zone" value="{l s='Add zones' mod='cloudcache'}" />
						{/if}
	<input type="submit" class="button MB10" id="SubmitCloudcacheClearAllCache" name="SubmitCloudcacheClearAllCache" value="{l s='Clear All Cache' mod='cloudcache'}" />
      </div>
    </form>
    <div style="margin-bottom: 60px;"></div>
    {if isset($edit_zone_info)}
    <div id="cloudcache_edit_zone_form" class="cloudcache-dialogbox">
      <form method="post" action="{$serverRequestUri|strip_tags}&id_tab=4">
	<input type="hidden" name="id_zone" id="id_zone" value="{$edit_zone_info['id_zone']|escape}"/>
	<table border="0" cellspacing="5" class="bold-td">
	  <tr>
	    <td>{l s='ID Zone' mod='cloudcache'}</td>
	    <td>{$edit_zone_info['id_zone']|escape}</td>
	  </tr>
	  <tr>
	    <td>{l s='Pull Zone Name' mod='cloudcache'}</td>
	    <td><input type="hidden" name="name" id="name" size="20" maxlength="30" value="{$edit_zone_info['name']|escape}"/>{$edit_zone_info['name']|escape}</td>
	  </tr>
	  <tr>
	    <td>{l s='Origin Server Url' mod='cloudcache'}</td>
	    <td><input type="hidden" name="origin" id="origin" size="20" maxlength="30" value="{$edit_zone_info['origin']|escape}"/>{$edit_zone_info['origin']|escape}</td>
	  </tr>
	  <tr>
	    <td>{l s='Custom CDN Domain' mod='cloudcache'}</td>
	    <td><input type="text" name="vanity_domain" id="vanity_domain" size="20" maxlength="30" value="{$edit_zone_info['cdn_url']|escape}"/></td>
	  </tr>
	  <tr>
	    <td>{l s='Label' mod='cloudcache'}</td>
	    <td><input type="text" name="label" id="label" size="20" maxlength="30" value="{$edit_zone_info['label']|escape}"/></td>
	  </tr>
	  <tr>
	    <td>{l s='Compression' mod='cloudcache'}</td>
	    <td>
	      <input type="checkbox" name="compress" id="compress" size="20" maxlength="30" {if $edit_zone_info['compress'] == 1}checked="checked"{/if}/>
	    </td>
	  </tr>
	  <tr>
	    <td>{l s='File Type' mod='cloudcache'}</td>
	    <td>
	      <select name="file_type" id="file_type">
		<option value="all" {if $edit_zone_info['file_type'] == 'all'}selected="selected"{/if}>{l s='All' mod='cloudcache'}</option>
		<option value="0" {if !$edit_zone_info['file_type']}selected="selected"{/if}>--not assigned--</option>
		<option value="css" {if $edit_zone_info['file_type'] == 'css'}selected="selected"{/if}>CSS</option>
		<option value="js" {if $edit_zone_info['file_type'] == 'js'}selected="selected"{/if}>JS</option>
		<option value="img" {if $edit_zone_info['file_type'] == 'img'}selected="selected"{/if}>Images</option>
		<option value="other" {if $edit_zone_info['file_type'] == 'other'}selected="selected"{/if}>Others</option>
	      </select>
	      <input type="hidden" style="display: none;" name="type" value="pullzone" />
	    </td>
	  </tr>
{*	  <tr>
	    <td>{l s='Zone Type' mod='cloudcache'}</td>
	    <td><select name="type" id="type">
		{foreach from=$allAvailableZones key=type item=name}
		<option value="{$type|escape}" {if $edit_zone_info['type'] == $type}selected="selected"{/if}>{$name|escape}</option>
		{/foreach}
	      </select>
	    </td>
	  </tr>
*}
	  <tr>
	    <td></td>
	    <td>
	      <input type="submit" class="button R" style="margin: 0px 10px;" id="cloudcache_close_edit" name="cloudcache_close_edit" value="{l s='Cancel' mod='cloudcache'}" />
	      <input type="submit" name="SubmitCloudcacheEdit_zone" id="SubmitCloudcacheEdit_zone" value="{l s='Save' mod='cloudcache'}" class="button R ML20" style="margin: 0px 10px;" />
	    </td>
	  </tr>
	</table>
	<div class="C"></div>
      </form>
    </div>
    {else}
    <div id="cloudcache_add_zone_form" class="cloudcache-dialogbox">
      <form method="post" action="{$serverRequestUri|strip_tags}&id_tab=4">
	<table border="0" cellspacing="5" class="bold-td">
	  <tr>
	    <td>{l s='Pull Zone Name' mod='cloudcache'}<sup> *</sup></td>
	    <td><input type="text" name="name" id="name" size="20" maxlength="30" /></td>
	  </tr>
	  <tr>
	    <td>{l s='Origin Server Url' mod='cloudcache'}<sup> *</sup></td>
	    <td><input type="text" name="origin" id="origin" size="20" maxlength="30" value="{$defaultOriginServerURL|strip_tags}"/></td>
	  </tr>
	  <tr>
	    <td>{l s='Custom CDN Domain' mod='cloudcache'}</td>
	    <td><input type="text" name="vanity_domain" id="vanity_domain" size="20" maxlength="30" /></td>
	  </tr>
	  <tr>
	    <td>{l s='Label' mod='cloudcache'}</td>
	    <td><input type="text" name="label" id="label" size="20" maxlength="30" /></td>
	  </tr>
	  <tr>
	    <td>{l s='Compression' mod='cloudcache'}</td>
	    <td><input type="checkbox" name="compress" id="compress" size="20" maxlength="30" /></td>
	  </tr>
	  <tr>
	    <td>{l s='Zone Type' mod='cloudcache'}</td>
	    <td><select name="type" id="type">
		{foreach from=$allAvailableZones key=type item=name}
		<option value="{$type|escape}">{$name|escape}</option>
		{/foreach}
	      </select>
	    </td>
	  </tr>
	  <tr>
	    <td></td>
	    <td><input type="submit" name="SubmitCloudcacheAdd_zone" id="SubmitCloudcacheAdd_zone" value="{l s='Create Zone' mod='cloudcache'}" class="button R MR20" /></td>
	  </tr>
	</table>
	<div class="C"></div>
      </form>
    </div>
    {/if}
  </div>
  {/if}
</div>
{/if}
<br clear="left" />
<fieldset class="width2 cloudcache_fieldset">
  <legend><img src="../img/admin/statsettings.gif" alt="" />{l s='Cloudcache and PrestaShop' mod='cloudcache'}</legend>
  <p><a href="http://www.prestashop.com/en/industry-partners/management/cloudcache" target="_blank">{l s='Learn more about Cloudcache at PrestaShop.com' mod='cloudcache'}</a></p>
</fieldset><br />
<br />

<script type="text/javascript">
{literal}
	$(".menuTabButton").click(function () {
		$(".menuTabButton.selected").removeClass("selected");
		$(this).addClass("selected");
		$(".tabItem.selected").removeClass("selected");
		$("#" + this.id + "Sheet").addClass("selected");
	});
{/literal}
{if (isset($cloudcache_id_tab))}
    var id_tab = '{$cloudcache_id_tab}';
{literal}
$(".menuTabButton.selected").removeClass("selected");
$("#menuTab"+id_tab).addClass("selected");
$(".tabItem.selected").removeClass("selected");
$("#menuTab"+id_tab+"Sheet").addClass("selected");
{/literal}
{/if}
</script>
