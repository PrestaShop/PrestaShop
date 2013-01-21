<ul id="tmheaderlinks">
	<li><a href="{$link->getPageLink('prices-drop')}"{if $page_name == 'prices-drop'} class="active"{/if}>{l s='specials' mod='tmheaderlinks'}</a></li>
	<li><a href="{$link->getPageLink('cms?id_cms=1')}"{if $smarty.get.id_cms == 1} class="active"{/if}>{l s='delivery' mod='tmheaderlinks'}</a></li>
	<li><a href="{$link->getPageLink('contact', true)}" {if $page_name == 'contact'} class="active"{/if}>{l s='contact' mod='tmheaderlinks'}</a></li>
    <li id="your_account"><a href="{$link->getPageLink('my-account', true)}" title="{l s='Your Account' mod='blockuserinfo'}">{l s='Your Account' mod='blockuserinfo'}</a></li>
</ul>