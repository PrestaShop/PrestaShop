{*
* 2007-2014 PrestaShop
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
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<div class="bootstrap">
	<div class="col-lg-2">
		<img src="{$image}" alt="{$displayName}" class="img-thumbnail" />
		{if isset($badges)}
		{foreach $badges as $badge}
			<img src="{$badge}" alt="" class="clearfix quickview-badge" />
		{/foreach}
		{/if}
	</div>
	<div class="col-lg-10">
		<h1>{$displayName}</h1>
		<div class="row">
			{if (int)$nb_rates > 0}
			<div class="col-lg-4">
				<span class="rating">
					<span class="star{if $avg_rate == 5} active{/if}"></span>
					<span class="star{if $avg_rate == 4} active{/if}"></span>
					<span class="star{if $avg_rate == 3} active{/if}"></span>
					<span class="star{if $avg_rate == 2} active{/if}"></span>
					<span class="star{if $avg_rate == 1} active{/if}"></span>
				</span>
				<p class="small">{if (int)$nb_rates > 1}{l s="(%s votes)" sprintf=$nb_rates}{else}{l s="(%s vote)" sprintf=$nb_rates}{/if}</p>
			</div>
			{/if}
			{if isset($compatibility) && $compatibility|count == 2}
			<div class="col-lg-8">
				<h4>{l s="Compatibility: PrestaShop v%1s - v%2s" sprintf=[$compatibility['from']|escape,$compatibility['to']|escape]}</h4>
			</div>
			{/if}
		</div>
		<h3>{l s="Description"}</h3>
		<p class="text-justify">{$description_full}</p>
		{if isset($additional_description) && trim($additional_description) != ''}
			<h3>{l s="Merchant benefits"}</h3>
			<p class="text-justify">{$additional_description}</p>
		{/if}
	</div>
</div>