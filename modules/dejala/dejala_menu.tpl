{if (isset($registered) AND $registered != 0)}
<div>
	<ul id="menu">
		<li {if ($currentTab == 'home')}class="active"{/if}>
			<a href="{$moduleConfigURL}">{l s='Home' mod='dejala'}</a>
		</li>
		<li {if ($currentTab == 'location')}class="active"{/if}>
			<a href="{$moduleConfigURL}&cat=location">{l s='Location' mod='dejala'}</a>
		</li>
		<li {if ($currentTab == 'contacts')}class="active"{/if}>
			<a href="{$moduleConfigURL}&cat=contacts">{l s='Contacts' mod='dejala'}</a>
		</li>
		<li {if ($currentTab == 'processes')}class="active"{/if}>
			<a href="{$moduleConfigURL}&cat=processes">{l s='Processes' mod='dejala'}</a>
		</li>
		<li {if ($currentTab == 'delivery_options')}class="active"{/if}>
			<a href="{$moduleConfigURL}&cat=delivery_options">{l s='Delivery Options' mod='dejala'}</a>
		</li>
		<li {if ($currentTab == 'prices')}class="active"{/if}>
			<a href="{$moduleConfigURL}&cat=prices">{l s='Prices' mod='dejala'}</a>
		</li>
		<li {if ($currentTab == 'accounting')}class="active"{/if}>
			<a href="{$moduleConfigURL}&cat=accounting">{l s='Accounting' mod='dejala'}</a>
		</li>
		<li {if ($currentTab == 'technical_options')}class="active"{/if}>
			<a href="{$moduleConfigURL}&cat=technical_options">{l s='Technical options' mod='dejala'}</a>
		</li>
		<li {if ($currentTab == 'dejala')}class="active"{/if}>
			<a href="{$moduleConfigURL}&cat=dejala">{l s='DEJALA PRO' mod='dejala'}</a>
		</li>
	</ul>
</div>
<br class='clear'/>
{/if}
<div style="margin-top:-2px; border: 1px solid #999;">
<br/>

