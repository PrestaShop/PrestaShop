{* Quick access *}
{* TODO: TO REPLACE
{if count($quick_access) >= 0}
  <ul id="header_quick">
    <li class="dropdown">
      <a href="javascript:void(0)" id="quick_select" class="dropdown-toggle" data-toggle="dropdown">{l s='Quick Access'}</a>
      <ul class="dropdown-menu">
        {foreach $quick_access as $quick}
          <li {if $link->matchQuickLink({$quick.link})}{assign "matchQuickLink" $quick.id_quick_access}class="active"{/if}>
            <a href="{$baseAdminUrl}{$quick.link|escape:'html':'UTF-8'}"{if $quick.new_window} class="_blank"{/if}>
              {if isset($quick.icon)}
                <i class="icon-{$quick.icon} icon-fw"></i>
              {else}
                <i class="icon-chevron-right icon-fw"></i>
              {/if}
              {$quick.name}
            </a>
          </li>
        {/foreach}
        <li class="divider"></li>
        {if isset($matchQuickLink)}
          <li>
            <a href="javascript:void(0);" class="ajax-quick-link" data-method="remove" data-quicklink-id="{$matchQuickLink}">
              <i class="icon-minus-circle"></i>
              {l s='Remove from QuickAccess'}
            </a>
          </li>
        {/if}
        <li {if isset($matchQuickLink)}class="hide"{/if}>
          <a href="javascript:void(0);" class="ajax-quick-link" data-method="add">
            <i class="icon-plus-circle"></i>
            {l s='Add current page to QuickAccess'}
          </a>
        </li>
        <li>
          <a href="{$link->getAdminLink("AdminQuickAccesses")|addslashes}">
            <i class="icon-cog"></i>
            {l s='Manage quick accesses'}
          </a>
        </li>
      </ul>
    </li>
  </ul>
  {$quick_access_current_link_name = " - "|explode:$quick_access_current_link_name}
  <script>
    $(function() {
      $('.ajax-quick-link').on('click', function(e){
        e.preventDefault();

        var method = $(this).data('method');

        if(method == 'add')
          var name = prompt('{l s='Please name this shortcut:' js=1}', '{$quick_access_current_link_name.0|truncate:32}');

        if(method == 'add' && name || method == 'remove')
        {
          $.ajax({
            type: 'POST',
            headers: { "cache-control": "no-cache" },
            async: false,
            url: "{$link->getAdminLink('AdminQuickAccesses')}" + "&action=GetUrl" + "&rand={1|rand:200}" + "&ajax=1" + "&method=" + method + ( $(this).data('quicklink-id') ? "&id_quick_access=" + $(this).data('quicklink-id') : ""),
            data: {
              "url": "{$link->getQuickLink($smarty.server['REQUEST_URI'])}",
              "name": name,
              "icon": "{$quick_access_current_link_icon}"
            },
            dataType: "json",
            success: function(data) {
              var quicklink_list ='';
              $.each(data, function(index,value){
                if (typeof data[index]['name'] !== 'undefined')
                  quicklink_list += '<li><a href="' + data[index]['link'] + '&token=' + data[index]['token'] + '"><i class="icon-chevron-right"></i> ' + data[index]['name'] + '</a></li>';
              });

              if (typeof data['has_errors'] !== 'undefined' && data['has_errors'])
                $.each(data, function(index, value)
                {
                  if (typeof data[index] == 'string')
                    $.growl.error({ title: "", message: data[index]});
                });
              else if (quicklink_list)
              {
                $("#header_quick ul.dropdown-menu").html(quicklink_list);
                showSuccessMessage(update_success_msg);
              }
            }
          });
        }
      });
    });
  </script>
{/if}
*}
