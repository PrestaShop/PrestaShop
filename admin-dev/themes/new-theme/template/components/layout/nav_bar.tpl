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

        {* TODO: DASHBOARD EXCEPTION TO MODIFY WHEN THE DIFFERENTS SUBMENUS LEVEL CAN AVOID PARENTS *}
        {if $level1.class_name == 'AdminDashboard'}

          <li class="link-levelone {if $level1.current}-active{/if}">
            <a href="{$level1Href}" class="link" >
              <i class="material-icons">{$level1.icon}</i> {$level1Name}
            </a>
          </li>

        {else}

          <li class="category-title {if $level1.current}-active{/if}">
              <span class="title">{$level1Name}</span>
          </li>

            {foreach $level1.sub_tabs as $level2}
              {if $level2.active}

                {$level2Href = $level2.href|escape:'html':'UTF-8'}

                {$level2Name = $level2.name|escape:'html':'UTF-8'}
                {if $level2.name eq ''}
                  {$level2Name = $level2.class_name|escape:'html':'UTF-8'}
                {/if}

                <li class="link-levelone {if $level2.current}-active{/if}">
                  <a href="{$level2Href}" class="link">
                    <i class="material-icons">{$level2.icon}</i> {$level2Name}
                  </a>
                    {if $level2.sub_tabs|@count}
                      <ul class="submenu">
                        {foreach $level2.sub_tabs as $level3}
                          {if $level3.active}

                            {$level3Href = $level3.href|escape:'html':'UTF-8'}

                            {$level3Name = $level3.name|escape:'html':'UTF-8'}
                            {if $level3.name eq ''}
                              {$level3Name = $level3.class_name|escape:'html':'UTF-8'}
                            {/if}

                            <li class="link-leveltwo {if $level3.current}-active{/if}">
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

  <span class="menu-collapse">
    <i class="icon-align-justify icon-rotate-90"></i>
  </span>

  {hook h='displayAdminNavBarBeforeEnd'}

</nav>
