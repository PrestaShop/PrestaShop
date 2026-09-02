{**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *}
<div class="content container-fluid">
  <div class="row">
    <div class="col-md-12">
      <div class="kpi-container">
        <div class="row">
          {assign var='col' value=(int)(12 / $kpis|count)}
          {foreach from=$kpis item=i name=kpi}
            {if $smarty.foreach.kpi.iteration > $col+1}
              </div>
              <div class="row">
            {/if}
            <div class="col-md-{$col}">{$i}</div>
          {/foreach}
        </div>
      </div>
    </div>
  </div>
</div>
