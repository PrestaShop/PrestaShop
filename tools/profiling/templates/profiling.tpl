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
