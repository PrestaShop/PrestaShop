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
<div class="col-4" id="summary">
  <table class="table table-condensed">
    <tr>
      <td>
        Load Time
      </td>
      <td>
        {load_time data=$summary.loadTime}
      </td>
    </tr>
    <tr>
      <td>Querying Time</td>
      <td>
        {total_querying_time data=$summary.queryTime} ms
      </td>
    </tr>
    <tr>
      <td>
        Queries
      </td>
      <td>
        {total_queries data=$summary.nbQueries}
      </td>
    </tr>
    <tr>
      <td>
        Memory Peak Usage
      </td>
      <td>
        {peak_memory data=$summary.peakMemoryUsage}
      </td>
    </tr>
    <tr>
      <td>
        Included Files
      </td>
      <td>
        {$summary.includedFiles} files - {memory data=$summary.totalFileSize}
      </td>
    </tr>
    <tr>
      <td>
        PrestaShop Cache
      </td>
      <td>
        {memory data=$summary.totalCacheSize}
      </td>
    </tr>
    <tr>
      <td>
        <a href="javascript:void(0);" onclick="$('.global_vars_detail').toggle();">Global vars</a>
      </td>
      <td>
        {memory data=$summary.totalGlobalVarSize}
      </td>
    </tr>

    {foreach $summary.globalVarSize as $global=>$size}
      <tr class="global_vars_detail" style="display:none">
        <td>
          - global ${$global}
        </td>
        <td>
          {$size}k
        </td>
      </tr>
    {/foreach}
  </table>
</div>
