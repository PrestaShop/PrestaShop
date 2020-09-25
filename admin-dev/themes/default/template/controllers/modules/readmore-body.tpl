{**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 *}
<div class="container-fluid ">
	<div class="row">
		{if (int)$nb_rates > 0}
			<div class="rating col-md-3">
				<span class="star{if $avg_rate == 5} active{/if}"></span>
				<span class="star{if $avg_rate == 4} active{/if}"></span>
				<span class="star{if $avg_rate == 3} active{/if}"></span>
				<span class="star{if $avg_rate == 2} active{/if}"></span>
				<span class="star{if $avg_rate == 1} active{/if}"></span>
			</div>
			<div class="col-md-2">{if (int)$nb_rates > 1}{l s="(%s votes)" sprintf=[$nb_rates]}{else}{l s="(%s vote)" sprintf=[$nb_rates]}{/if}</div>
			<div class="col-md-3">
			{if isset($badges)}
				{foreach $badges as $badge}
					<img src="{$badge}" alt="" class="clearfix" />
				{/foreach}
			{/if}
			</div>
			{if (int)$price}
				<div class="quickview-price">
					{displayPrice price=$price currency=$id_currency}
				</div>
			{/if}
		{/if}
	</div>
	<div class="row">
		<hr />
		<h4>{l s="Description"}</h3>
		<hr />
		<p class="text-justify">{$description_full}</p>
	</div>
	{if isset($additional_description) && trim($additional_description) != ''}
	<div class="row">
		<hr />
		<h4>{l s="Merchant benefits"}</h3>
		<hr />
		<p class="text-justify">{$additional_description}</p>
	</div>
	{/if}
	<div class="row">
		{if $installed}
			<div class="btn-group-action pull-right">
				{if $options|count > 0}
				<div class="btn-group">
					{assign var=option value=$options[0]}
					{$option}
					{if $options|count > 1}
					<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" >
						<span class="caret">&nbsp;</span>
					</button>
					<ul class="dropdown-menu pull-right">

					{foreach $options key=key item=option}
						{if $key != 0}
							{if strpos($option, 'title="divider"') !== false}
								<li class="divider"></li>
							{else}
								<li>{$option}</li>
							{/if}
						{/if}
					{/foreach}
					</ul>
					{/if}
				</div>
				{/if}
			</div>
		{elseif $is_addons_partner}
			<a class="btn btn-success btn-lg pull-right" href="{$url}">{l s='Install module'}</a>
		{else}
			<a class="btn btn-success btn-lg pull-right" href="{$url}" onclick="return !window.open(this.href, '_blank');">{l s='View on PrestaShop Addons'}</a>
		{/if}
	</div>
</div>
