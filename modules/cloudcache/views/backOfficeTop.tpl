{literal}
<script type="text/javascript">
	$(function(){
{/literal}
		mediaServerForm = $('input[name="submitMediaServers"]').parent().parent();
				ccc = $('input[name="submitCCC"]').parent().parent();
{if $isModuleActive}
			legend = $(mediaServerForm).find('legend').html();
					$(mediaServerForm).empty();
					$('<legend>' + legend + '</legend>' +
					'<div class="conf"><img alt="" src="../img/admin/ok2.png">' +
						'{l s='You are currently saving even more performance by' mod='cloudcache'}' +
						' <a href="?tab=AdminModules&configure=cloudcache&token={$adminToken}&tab_module=administration&module_name=cloudcache" style="color: blue; font-weight: bold">' +
						'{l s='using CloudCache module' mod='cloudcache'}</a>, ' +
						'{l s='the Best CDN service recommended by PrestaShop users!' mod='cloudcache'} ' +
					'</div>').prependTo(mediaServerForm);
{else}
	$('<div class="warn"><img src="../img/admin/warn2.png">' + '{l s='Save even more performance by' mod='cloudcache'}' + '<a href="?tab=AdminModules&configure=cloudcache&token={$adminToken}&tab_module=administration&module_name=cloudcache" style="color: blue; font-weight: bold">{l s='activating CloudCache module' mod='cloudcache'}</a>, {l s='the Best CDN service recommended by PrestaShop users!' mod='cloudcache'}</div>').prependTo(mediaServerForm);
{/if}

{if $isModuleActive}
    {if isset($compatibilityIssues)}
    	$('<div class="warn"><img src="../img/admin/warn2.png"> ' +
        {foreach from=$compatibilityIssues key=i item=message}
   	 	     '{if $i > 0}{$i} - {/if}{$message}' +
   	{/foreach}
	'</div>').prependTo(ccc);
    {else}
	$('<div class="conf"><img src="../img/admin/ok2.png">{l s='Everything looks good for CloudCache' mod='cloudcache'}</div>').prependTo(ccc);
    {/if}
{/if}

{literal}
});</script>
{/literal}
