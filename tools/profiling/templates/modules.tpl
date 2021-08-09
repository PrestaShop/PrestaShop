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
<div class="col-5">
  <table class="table table-condensed">
    <thead>
      <tr>
        <th>Module</th>
        <th>Time</th>
        <th>Memory Usage</th>
      </tr>
    </thead>

    <tbody>
      {foreach $modules.perfs as $moduleName => $perfs}
        <tr>
          <td>
            <a href="javascript:void(0);" onclick="$('.{$moduleName}_modules_details').toggle();">{$moduleName}</a>
            </td>
            
          </td>
          <td>
            {load_time data=$perfs['total_time']}
          </td>
          <td>
            {memory data=$perfs['total_memory']}
          </td>
        </tr>
        {foreach $perfs['details'] as $perfs}
          <tr class="{$moduleName}_modules_details" style="background-color:#EFEFEF;display:none">
            <td>
              {$perfs['method']}
            </td>
            <td>
              {load_time data=$perfs['time']}
            </td>
            <td>
              {memory data=$perfs['memory']}
            </td>
          </tr>
        {/foreach}
      {/foreach}
      
    </tbody>

    <tfoot>
      <tr>
        <th><b>{$modules.perfs|count} module(s)</b></th>
        <th>{load_time data=$modules.totalHooksTime}</th>
        <th>{memory data=$modules.totalHooksMemory}</th>
      </tr>
    </tfoot>
  </table>
</div>
