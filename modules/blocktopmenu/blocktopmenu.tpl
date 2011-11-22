{*
* 2007-2011 PrestaShop 
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision: 6844 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
        {if $MENU != ''}
        </div>
				<!-- Menu -->
        <div class="sf-contener clearfix">
          <ul class="sf-menu clearfix">
            {$MENU}
            {if $MENU_SEARCH}
            <li class="sf-search noBack" style="float:right">
              <form id="searchbox" action="search.php" method="get">
                <input type="hidden" value="position" name="orderby"/>
                <input type="hidden" value="desc" name="orderway"/>
                <input type="text" name="search_query" value="{if isset($smarty.get.search_query)}{$smarty.get.search_query}{/if}" />
              </form>
            </li>
            {/if}
          </ul>
        <div class="sf-right">&nbsp;</div>
        <script type="text/javascript" src="{$this_path}js/hoverIntent.js"></script>
        <script type="text/javascript" src="{$this_path}js/superfish-modified.js"></script>
        <link rel="stylesheet" type="text/css" href="{$this_path}css/superfish-modified.css" media="screen">
				<!--/ Menu -->
        {/if}	
