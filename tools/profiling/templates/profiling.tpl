{include file="./functions.tpl"}
{include file="./styles.tpl"}

<div id="prestashop_profiling" class="container">
  <div class="row">
    {include file="./summary.tpl" summary=$summary}
    {include file="./configuration.tpl" configuration=$configuration}
    {include file="./run.tpl" run=$run}
  </div>
  <div class="row">
    {include file="./hooks.tpl" hooks=$hooks}
  </div>
</div>
