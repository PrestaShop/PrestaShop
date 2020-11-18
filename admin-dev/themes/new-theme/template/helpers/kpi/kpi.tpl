{**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}

{if isset($href) && $href}
  <a href="{$href|escape:'html':'UTF-8'}" id="{$id|escape:'html':'UTF-8'}" class="kpi-container box-stats">
{else}
  <div id="{$id|escape:'html':'UTF-8'}" class="kpi-container box-stats">
{/if}
  <div class="kpi-content -{$color|escape}" data-original-title="{$tooltip|escape}" data-toggle="pstooltip">
    {if isset($icon) && $icon}
      <i class="material-icons">{$icon|escape}</i>
    {/if}
    {if isset($chart) && $chart}
      <div class="boxchart-overlay">
        <div class="boxchart">
        </div>
      </div>
    {/if}
    <span class="title">{$title|escape}</span>
    <span cLass="subtitle">{$subtitle|escape}</span>
    <span class="value">{$value|escape|replace:'&amp;':'&'}</span>
  </div>
{if isset($href) && $href}
  </a>
{else}
  </div>
{/if}

<script>
  function refresh_{$id|replace:'-':'_'|addslashes}()
  {
    {if !isset($source) || $source == '' || !isset($refresh) || $refresh == ''}
    if (arguments.length < 1 || arguments[0] != true) {
      // refresh kpis only if force mode is true (pass true as first argument of this function).
      return;
    }
    {/if}
    $.ajax({
      url: '{$source|addslashes}' + '&rand=' + new Date().getTime(),
      dataType: 'json',
      type: 'GET',
      cache: false,
      headers: { 'cache-control': 'no-cache' },
      success: function(jsonData){
        if (!jsonData.has_errors)
        {
          if (jsonData.value != undefined)
          {
            $('#{$id|addslashes} .value').html(jsonData.value);
            $('#{$id|addslashes}').attr('data-original-title', jsonData.tooltip);
          }
          if (jsonData.data != undefined)
          {
            $("#{$id|addslashes} .boxchart svg").remove();
            set_d3_{$id|replace:'-':'_'|addslashes}(jsonData.data);
          }
        }
      }
    });
  }
</script>

{if $chart}
  <script>
    function set_d3_{$id|str_replace:'-':'_'|addslashes}(jsonObject)
    {
      var data = new Array;
      $.each(jsonObject, function (index, value) {
        data.push(value);
      });
      var data_max = d3.max(data);

      var chart = d3.select("#{$id|addslashes} .boxchart").append("svg")
              .attr("class", "data_chart")
              .attr("width", data.length * 6)
              .attr("height", 45);

      var y = d3.scale.linear()
              .domain([0, data_max])
              .range([0, data_max * 45]);

      chart.selectAll("rect")
              .data(data)
              .enter().append("rect")
              .attr("y", function(d) { return 45 - d * 45 / data_max; })
              .attr("x", function(d, i) { return i * 6; })
              .attr("width", 4)
              .attr("height", y);
    }

    {if $data}
    set_d3_{$id|replace:'-':'_'|addslashes}($.parseJSON("{$data|addslashes}"));
    {/if}
  </script>
{/if}
