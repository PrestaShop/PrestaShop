<div id="tmfooterlinks"> 	
	{foreach from=$xml->link item=home_link name=links}
	<div>
		<h4>{$home_link->$field1}</h4>
		<ul>
			<li><a href="{$home_link->$field3}">{$home_link->$field2}</a></li>
			<li><a href="{$home_link->$field5}">{$home_link->$field4}</a></li>
			<li><a href="{$home_link->$field7}">{$home_link->$field6}</a></li>
			<li><a href="{$home_link->$field9}">{$home_link->$field8}</a></li>
			<li><a href="{$home_link->$field11}">{$home_link->$field10}</a></li>
		</ul>
	</div>
	{/foreach}
	<p>&copy; {$smarty.now|date_format:"%Y"} {l s='Powered by' mod='tmfooterlinks'} <a href="http://www.prestashop.com">PrestaShop</a>&trade;. {l s='All rights reserved' mod='tmfooterlinks'}</p>
</div>