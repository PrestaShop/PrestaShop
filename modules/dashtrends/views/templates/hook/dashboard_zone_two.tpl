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

<section id="dash_trends" class="panel">
	<header class="panel-heading">
		<i class="icon-bar-chart"></i> {l s='Score Trends'}
		<span class="panel-heading-action">
			<a class="list-tooolbar-btn" href="javascript:void(0);" title="configure">
				<i class="process-icon-configure"></i>
			</a>
			<a class="list-tooolbar-btn" href="javascript:location.reload();" title="refresh">
				<i class="process-icon-refresh"></i>
			</a>
		</span>
	</header>
	<div id="dash_trends_toolbar" class="row">
		<dl class="col-xs-4 col-lg-2 active">
			<dt>{l s='Sales'}</dt>
			<dd class="size_l">$150,365</dd>
			<dd class="dash_trend dash_trend_up"><i class="icon-circle-arrow-up"></i> 0.66%</dd>
		</dl>
		<dl class="col-xs-4 col-lg-2">
			<dt>{l s='Orders'}</dt>
			<dd class="size_l">20,432</dd>
			<dd class="dash_trend dash_trend_down"><i class="icon-circle-arrow-down"></i> 0.66%</dd>
		</dl>
		<dl class="col-xs-4 col-lg-2">
			<dt>{l s='Cart Value'}</dt>
			<dd class="size_l">$125</dd>
			<dd class="dash_trend dash_trend_up"><i class="icon-circle-arrow-up"></i> 0.66%</dd>
		</dl>
		<dl class="col-xs-4 col-lg-2">
			<dt>{l s='Visits'}</dt>
			<dd class="size_l">1,200</dd>
			<dd class="dash_trend dash_trend_down"><i class="icon-circle-arrow-down"></i> 0.66%</dd>
		</dl>
		<dl class="col-xs-4 col-lg-2">
			<dt>{l s='Converstion Rate'}</dt>
			<dd class="size_l">23%</dd>
			<dd class="dash_trend dash_trend_up"><i class="icon-circle-arrow-up"></i> 0.66%</dd>
		</dl>
		<dl class="col-xs-4 col-lg-2">
			<dt>{l s='Net Profits'}</dt>
			<dd class="size_l">$23,568</dd>
			<dd class="dash_trend dash_trend_up"><i class="icon-circle-arrow-up"></i> 0.66%</dd>
		</dl>
	</div>

	<div id="dash_trends_chart1" class='chart with-transitions'>
		<svg></svg>
	</div>

</section>