{**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *}
{include file="./functions.tpl"}
{include file="./styles.tpl"}

<div id="prestashop-profiling" class="container">
  {include file="./links.tpl"}
  <div class="row">
    {include file="./summary.tpl" summary=$summary}
    {include file="./configuration.tpl" configuration=$configuration}
    {include file="./run.tpl" run=$run}
  </div>
  <div class="row">
    {include file="./hooks.tpl" hooks=$hooks}
    {include file="./modules.tpl" modules=$modules}
  </div>

  {include file="./stopwatch.tpl" stopwatchQueries=$stopwatchQueries}
  {include file="./doubles.tpl" doublesQueries=$doublesQueries}
  {include file="./table-stress.tpl" tableStress=$tableStress}
  {include file="./objectmodel.tpl" objectmodel=$objectmodel}
  {include file="./files.tpl" files=$files}
</div>
