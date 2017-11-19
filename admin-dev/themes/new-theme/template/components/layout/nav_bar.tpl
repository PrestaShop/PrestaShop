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
<nav class="nav-bar">
  <ul class="main-menu">

    {foreach $tabs as $level1}
      {if $level1.active}

        {$level1Href = $level1.href|escape:'html':'UTF-8'}
        {if $level1.sub_tabs|@count && isset($level1.sub_tabs[0].href)}
          {$level1Href = $level1.sub_tabs[0].href|escape:'html':'UTF-8'}
        {/if}

        {$level1Name = $level1.name|escape:'html':'UTF-8'}
        {if $level1.name eq ''}
          {$level1Name = $level1.class_name|escape:'html':'UTF-8'}
        {/if}

        {if $level1.icon != ''}

          <li class="link-levelone {if $level1.current}-active{/if}" data-submenu="{$level1.id_tab}">
            <a href="{$level1Href}" class="link" >
              <i class="material-icons">{$level1.icon}</i> <span>{$level1Name}</span>
            </a>
          </li>

        {else}

          <li class="category-title d-none d-sm-block {if $level1.current}-active{/if}" data-submenu="{$level1.id_tab}">
              <span class="title">{$level1Name}</span>
          </li>

            {foreach $level1.sub_tabs as $level2}
              {if $level2.active}

                {$level2Href = $level2.href|escape:'html':'UTF-8'}

                {$level2Name = $level2.name|escape:'html':'UTF-8'}
                {if $level2.name eq ''}
                  {$level2Name = $level2.class_name|escape:'html':'UTF-8'}
                {/if}

                <li class="link-levelone {if $level2.current}-active{/if}" data-submenu="{$level2.id_tab}">
                  <a href="{$level2Href}" class="link">
                    <i class="material-icons">{$level2.icon}</i>
                    <span>
                    {$level2Name}
                    {if $level2.sub_tabs|@count}
                      <i class="material-icons float-right d-md-none">keyboard_arrow_down</i>
                    {/if}
                    </span>

                  </a>
                    {if $level2.sub_tabs|@count}
                      <ul id="collapse-{$level2.id_tab}" class="submenu panel-collapse">
                        {foreach $level2.sub_tabs as $level3}
                          {if $level3.active}

                            {$level3Href = $level3.href|escape:'html':'UTF-8'}

                            {$level3Name = $level3.name|escape:'html':'UTF-8'}
                            {if $level3.name eq ''}
                              {$level3Name = $level3.class_name|escape:'html':'UTF-8'}
                            {/if}

                            <li class="link-leveltwo {if $level3.current}-active{/if}" data-submenu="{$level3.id_tab}">
                              <a href="{$level3Href}" class="link"> {$level3Name}
                              </a>
                            </li>

                          {/if}
                        {/foreach}
                      </ul>
                    {/if}
                </li>
              {/if}
            {/foreach}

        {/if}

      {/if}
    {/foreach}
  </ul>

  <span class="menu-collapse d-none d-md-inline-block">
    <i class="material-icons">&#xE8EE;</i>
  </span>

  {hook h='displayAdminNavBarBeforeEnd'}
</nav>
