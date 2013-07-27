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

	<div class="pageTitleHome">
		<span><h3>{l s='Dashboard'}</h3></span>
	</div>
<div id="dashboard">
<div id="homepage">


	<div id="column_left">
		{if $upgrade}
		<div id="blockNewVersionCheck">
		{if $upgrade->need_upgrade}
			<div class="warning warn" style="margin-bottom:10px;"><h3>{l s='A new version of PrestaShop is available.'} : <a style="text-decoration: underline;" href="{$upgrade->link}" target="_blank">{l s='Download'} {$upgrade->version_name}</a> !</h3></div>
		{/if}
		</div>
	{else}
		<p>{l s='Update notifications are unavailable'}</p>
		<p>&nbsp;</p>
		<p>{l s='To receive PrestaShop update warnings, you need to activate you account. '} <b>allow_url_fopen</b> [<a href="http://www.php.net/manual/{$isoUser}/ref.filesystem.php">{l s='more info on php.net'}</a>]</p>
		<p>{l s='If you don\'t know how to do this, please contact your hosting provider!'}</p><br />
	{/if}

{if $employee->bo_show_screencast}
<div id="adminpresentation" style="display:block">
<h2>{l s='Video'}</h2>
		<div id="video">
			<a href="{$protocol}://screencasts.prestashop.com/v1.5/screencast.php?iso_lang={$isoUser}" id="screencast_fancybox"><img height="128" width="220" src="../img/admin/preview_fr.jpg" /><span class="mask-player"></span></a>
		</div>
			<div id="video-content">
			<p>{l s='Take part in the e-commerce adventure with PrestaShop, the best open-source shopping-cart solution on the planet. With more than 310 native features, PrestaShop comes fully equipped to help create a world of opportunity without limits. '}</p>
			</div>
	<div id="footer_iframe_home">
		<!--<a href="#">{l s='View more video tutorials.'}</a>-->
		<input type="checkbox" id="screencast_dont_show_again">
		<label for="screencast_dont_show_again">{l s='Do not show me this again.'}</label>
	</div>
				<div class="separation"></div>
</div>
<script type="text/javascript">
$(document).ready(function() {
	$('#screencast_dont_show_again').click(function() {
		if ($(this).is(':checked'))
		{
			$.ajax({
				type : 'POST',
				data : {
					ajax : '1',
					controller : 'AdminHome',
					token : '{$token}',
					id_employee : '{$employee->id}',
					action : 'hideScreencast'
				},
				url: 'ajax-tab.php',
				dataType : 'json',
				success: function(data) {
					if (!data)
						jAlert("TECHNICAL ERROR - no return status found");
					else if (data.status != "ok")
						jAlert("TECHNICAL ERROR: "+data.msg);

					$('#adminpresentation').slideUp('slow');
					
				},
				error: function(data, textStatus, errorThrown)
				{
					jAlert("TECHNICAL ERROR: "+data);
				}
			});
		}
	});
});
</script>
{/if}

<h2>{l s='Quick links'}</h2>
		<ul class="F_list clearfix">
		{foreach from=$quick_links key=k item=link}
		<li id="{$k}_block">
			<a href="{$link.href}">
				<h4>{$link.title}</h4>
				<p>{$link.description}</p>
			</a>
		</li>
		{/foreach}
		{hook h="displayAdminHomeQuickLinks"}
		</ul>

	<div id="partner_preactivation">
		<p class="center"><img src="../img/loader.gif" alt="" /></p>
	</div>

	<div class="separation"></div>


	{$tips_optimization}
	<div id="discover_prestashop"><p class="center"><img src="../img/loader.gif" alt="" />{l s='Loading...'}</p></div>

	{hook h="displayAdminHomeInfos"}
	{hook h="displayBackOfficeHome"} {*old name of the hook*}

</div>


	<div id="column_right">
	<h2>{l s='Your Information'}</h2>
		{$monthly_statistics}
		{$customers_service}
		{$stats_sales}
		{$last_orders}
		{hook h="displayAdminHomeStatistics"}
	</div>

</div>
	<div class="clear">&nbsp;</div>
	
	</div>

<script type="text/javascript">
$(document).ready(function() {
	if ({$refresh_check_version})
	{
		$('#blockNewVersionCheck').hide();
		$.ajax({
			type : 'POST',
			data : {
				ajax : '1',
				controller : 'AdminHome',
				token : '{$token}',
				id_employee : '{$employee->id}',
				action : 'refreshCheckVersion'
			},
			url: 'ajax-tab.php',
			dataType : 'json',
			success: function(data) {
				if (!data)
					jAlert("TECHNICAL ERROR - no return status found");
				else if (data.status != "ok")
					jAlert("TECHNICAL ERROR: "+data.msg);
				if(data.upgrade.need_upgrade)
				{
					$('#blockNewVersionCheck').children("a").attr('href',data.upgrade.link);
					$('#blockNewVersionCheck').children("a").html(data.upgrade.link+"pouet");
					$('#blockNewVersionCheck').fadeIn('slow');
				}

				
			},
			error: function(data, textStatus, errorThrown)
			{
				jAlert("TECHNICAL ERROR: "+data);
			}
		});
	}
	$.ajax({
		url: "ajax-tab.php",
		type: "POST",
		data:{
			token: "{$token}",
			ajax: "1",
			controller : "AdminHome",
			action: "getAdminHomeElement"
		},
		dataType: "json",
		success: function(json) {
		{if $employee->bo_show_screencast}
			if (json.screencast != 'NOK')
				$('#adminpresentation').fadeIn('slow');
			else
				$('#adminpresentation').fadeOut('slow');
		{/if}
			$('#partner_preactivation').fadeOut('slow', function() {
				if (json.partner_preactivation != 'NOK')
					$('#partner_preactivation').html(json.partner_preactivation);
				else
					$('#partner_preactivation').html('');
				$('#partner_preactivation').fadeIn('slow');
			});

			$('#discover_prestashop').fadeOut('slow', function() {
				if (json.discover_prestashop != 'NOK')
					$('#discover_prestashop').replaceWith(json.discover_prestashop);
				else
					$('#discover_prestashop').html('');
				$('#discover_prestashop').fadeIn('slow');
			});
		},
		error: function(XMLHttpRequest, textStatus, errorThrown)
		{
			// don't show/hide screencast if it's deactivated
			{if $employee->bo_show_screencast}
			$('#adminpresentation').fadeOut('slow');
			{/if}
			$('#partner_preactivation').fadeOut('slow');
			$('#discover_prestashop').fadeOut('slow');
		}
	});
	$('#screencast_fancybox').bind('click', function(event)
	{
		$.fancybox(
			this.href,
			{
				'width'				: 	660,
				'height'			: 	384,
				'transitionIn'		: 'none',
				'transitionOut'		: 'none',
				'type' 				: 'iframe',
				'scrolling'			: 'no',
				'onComplete'		: function()
					{
						// Rewrite some css properties of Fancybox
						$('#fancybox-wrap').css('width', '');
						$('#fancybox-content').css('background-color', '');
						$('#fancybox-content').css( 'border', '');
					}
			});

		event.preventDefault();
	});
});
</script>
