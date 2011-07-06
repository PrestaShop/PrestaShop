{if (isset($registered) AND $registered != 0)}
	{if ($currentTab != 'home')}
		{assign var='page_include' value=$currentTab}
	{else}
		{if ($djl_mode=='TEST')}
			{if ($isLiveRequested=='1')}
				{assign var='page_include' value='home_ready'}
			{else}
				{assign var='page_include' value='home_testing'}
			{/if}
		{else}
			{assign var='page_include' value='home_active'}
		{/if}

	{/if}
{else}
	{assign var='page_include' value='home_new'}
{/if}


	<div id="adminNews">
		<iframe frameborder="no" style="margin:15px; padding: 0px; width: 900px; height: 500px;" src="http://module.pro.dejala.{$country}/tabs/{$page_include}.php"></iframe>
	</div>

</div>

