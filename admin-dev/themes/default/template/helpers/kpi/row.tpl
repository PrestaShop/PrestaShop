{**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *}
<div class="panel kpi-container">
	{if $refresh}
		<div class="kpi-refresh"><button class="close refresh" type="button" onclick="refresh_kpis(true);"><i class="process-icon-refresh" style="font-size:1em"></i></button></div>
	{/if}
	<div class="row">
		{assign var='col' value=(int)(12 / $kpis|count)}
		{foreach from=$kpis item=i name=kpi}
			{if $smarty.foreach.kpi.iteration > $col+1}
				</div>
				<div class="row">
			{/if}
			<div class="col-sm-6 col-lg-{$col}">{$i}</div>
		{/foreach}
	</div>
</div>
