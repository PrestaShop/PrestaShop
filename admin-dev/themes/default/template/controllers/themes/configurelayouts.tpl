{*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
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
* @copyright 2007-2015 PrestaShop SA
* @license   http://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
* International Registered Trademark & Property of PrestaShop SA
*}
<div class="panel " id="">

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
              <th>Page</th>
              <th>Description</th>
              <th>Layout</th>
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
                  <select name="layouts[{$page.page}]" id="">
                    <option value="#" {if !isset($page_layouts->{$page.page}) && $page_layouts->{$page.page} == $layout}selected="selected"{/if}>
                    {foreach $available_layouts as $layout}
                        <option value="{$layout}" {if isset($page_layouts->{$page.page}) && $page_layouts->{$page.page} == $layout}selected="selected"{/if}>
                        {$layout}
                      </option>
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
      <button type="submit" class="btn btn-default pull-right" name="submitConfigureLayouts">
        <i class="process-icon-save"></i> Save
      </button>
    </div>

  </form>


</div>
