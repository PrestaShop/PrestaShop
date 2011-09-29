<div>
	<h1>{l s='Dashboard'}</h1>
	<hr style="background-color: #812143;color: #812143;" />
	{if $upgrade}
		<div class="warning warn" style="margin-bottom:10px;"><h3>{l s ='New PrestaShop version available'} : <a style="text-decoration: underline;" href="{$upgrade->link}" target="_blank">{l s ='Download'} {$upgrade->version_name}</a> !</h3></div>
	{else}
		<p>{l s ='Update notification unavailable'}</p>
		<p>&nbsp;</p>
		<p>{l s ='To receive PrestaShop update warnings, you need to activate the <b>allow_url_fopen</b> command in your <b>php.ini</b> config file.'} [<a href="http://www.php.net/manual/'.$isoUser.'/ref.filesystem.php">{l s ='more info'}</a>]</p>
		<p>{l s ='If you don\'t know how to do that, please contact your host administrator !'}</p><br />
	{/if}
{if $show_screencast}
<div id="adminpresentation">
	<iframe src="{$protocol}://screencasts.prestashop.com/screencast.php?iso_lang={$isoUser}" style="border:none;width:100%;height:420px;" scrolling="no"></iframe>
	<div id="footer_iframe_home">
		<!--<a href="#">{l s ='View more video tutorials'}</a>-->
		<input type="checkbox" id="screencast_dont_show_again">
		<label for="screencast_dont_show_again">{l s='don\'t show again'}</label>
	</div>
</div>
<script type="text/javascript">
$(document).ready(function() {
	$('#screencast_dont_show_again').click(function() {
		if ($(this).is(':checked'))
		{
			$.ajax({
				type: 'POST',
				async: true,
				url: 'ajax.php?toggleScreencast',
				success: function(data) {
					$('#adminpresentation').slideUp('slow');
				}
			});
		}
	});
});
</script>
{/if}
	<div id="column_left">
		<ul class="F_list clearfix">
		{foreach from=$quick_links key=k item=link}
		<li id="{$k}_block">
			<h4><a href="{$link.href}">{$link.title}</a></h4>
			<p>{$link.description}</p>
		</li>
		{/foreach}
		</ul>
		{$monthly_statistics}
		{$customers_service}
		{$stats_sales}
		{$last_orders}
	</div>

	<div id="column_right">
	{$tips_optimization}
	<div id="partner_preactivation"><p class="center"><img src="../img/loader.gif" alt="" />{l s='Loading...'}</p></div>
	<div id="discover_prestashop"><p class="center"><img src="../img/loader.gif" alt="" />{l s='Loading...'}</p></div>
	</div>
	{$HOOK_BACKOFFICEHOME}
	<div class="clear">&nbsp;</div>
<script type="text/javascript">
$(document).ready(function() {
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

			if (json.screencast != 'NOK')
				$('#adminpresentation').fadeIn('slow');
			else
				$('#adminpresentation').fadeOut('slow');
				
			$('#partner_preactivation').fadeOut('slow', function() {
				if (json.partner_preactivation != 'NOK')
					$('#partner_preactivation').html(json.partner_preactivation);
				else
					$('#partner_preactivation').html('');
				$('#partner_preactivation').fadeIn('slow');
			});
			
			$('#discover_prestashop').fadeOut('slow', function() {
				if (json.discover_prestashop != 'NOK')
					$('#discover_prestashop').html(json.discover_prestashop);
				else
					$('#discover_prestashop').html('');
				$('#discover_prestashop').fadeIn('slow');
			});
		},
		error: function(XMLHttpRequest, textStatus, errorThrown)
		{
			$('#adminpresentation').fadeOut('slow');
			$('#partner_preactivation').fadeOut('slow');	
			$('#discover_prestashop').fadeOut('slow');
		}
	});
});
</script>
