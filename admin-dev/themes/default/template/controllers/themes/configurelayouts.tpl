{**
 * 2007-2017 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}
<div class="panel" id="">

  <div class="panel-heading">
    <i class="icon-cogs"></i>
    {l s='Choose layouts'}
  </div>

  <form action="{$link->getAdminLink('AdminThemes')}" method="post">
    <div class="form-wrapper clearfix">
      <div class="form-group">

        <div class="col-lg-12">
          <table class="table table-stripped">

            <tr>
              <th>{l s='Page'}</th>
              <th>{l s='Description' d='Admin.Global'}</th>
              <th>{l s='Layout'}</th>
            </tr>

            {foreach $pages as $page}
              <tr>
                <td>
                  {if $page.title}
                    {$page.title}
                  {else}
                    {$page.page}
                  {/if}
                </td>
                  <td>
                    {$page.description}
                  </td>
                <td>
                  {assign var="defaultKey" value=""}
                  <select name="layouts[{$page.page}]" id="">
                    {if !isset($page_layouts.{$page.page})}
                      {assign var="defaultKey" value="{$default_layout.key}"}
                      <option value="{$default_layout.key}" selected="selected">
                        {$default_layout.name} - {$default_layout.description}
                      </option>
                    {/if}
                    {foreach $available_layouts as $key => $layout}
                      {if {$key} !== {$defaultKey} }
                        <option value="{$key}" {if isset($page_layouts.{$page.page}) && $page_layouts.{$page.page} == $key}selected="selected"{/if}>
                          {$layout.name} - {$layout.description}
                        </option>
                      {/if}
                    {/foreach}
                  </select>
                </td>
              </tr>
            {/foreach}

          </table>
        </div>

      </div>
    </div><!-- /.form-wrapper -->

    <div class="panel-footer">
      <input type="hidden" name="action" value="submitConfigureLayouts">
      <button type="submit" class="btn btn-default pull-right" name="submitConfigureLayouts">
        <i class="process-icon-save"></i> {l s='Save' d='Admin.Actions'}
      </button>
    </div>

  </form>


</div>
