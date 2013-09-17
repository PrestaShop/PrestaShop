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
{extends file="helpers/list/list_header.tpl"}
{block name='leadin'}
<div class="panel">
	<div class="row">
		<div id="box-clients" class="col-lg-3 box-stats color1" >
			<div class="boxchart-overlay">
				<div class="boxchart"></div>
			</div>	
			<span class="title">Conversion Rate <br/><small>30 days</small></span>
			<span class="value">1.89%</span>
		</div>
	</div>
</div>

<script>
	var data = [4, 8, 15, 16, 23, 42, 8, 15, 16, 23, 42, 16, 23, 42, 8];
	var chart = d3.select("#box-clients .boxchart").append("svg")
		.attr("class", "data_chart")
		.attr("width", data.length * 6)
		.attr("height", 45);
	var y = d3.scale.linear()
		.domain([0, d3.max(data)])
		.range([0, d3.max(data)]);
	chart.selectAll("rect")
		.data(data)
		.enter().append("rect")
		.attr("y", function(d) { return 45 - d; })
		.attr("x", function(d, i) { return i * 6; })
		.attr("width", 4)
		.attr("height", y);
</script>

{/block}
