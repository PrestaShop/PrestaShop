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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 *}
<div class="col-4">
  <table class="table table-condensed">
    <thead>
      <tr>
        <th>&nbsp;</th>
        <th>Time</th>
        <th>Cumulated Time</th>
        <th>Memory Usage</th>
        <th>Memory Peak Usage</th>
      </tr>
    </thead>

    <tbody>
      {assign var="last" value=['time' => $run.startTime, 'memory_usage' => 0]}

      {foreach from=$run.profiler item=row}
        {if $row['block'] == 'checkAccess' && $row['time'] == $last['time']}
          {continue}
        {/if}

        <tr>
          <td>{$row['block']}</td>
          <td>{load_time data=($row['time'] - $last['time'])}</td>
          <td>{load_time data=($row['time'] - $run.startTime)}</td>
          <td>{memory data=($row['memory_usage'] - $last['memory_usage'])}</td>
          <td>{peak_memory data=($row['peak_memory_usage'])}</td>
        </tr>

        {assign var="last" value=$row}
      {/foreach}
    </tbody>
  </table>
</div>
