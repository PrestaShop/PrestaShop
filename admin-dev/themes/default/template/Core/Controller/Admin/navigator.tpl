<div>
    &nbsp;
    <select name="paginator_select_page_limit" psurl="{$changeLimitUrl}" style="display:inline;width:6em;">
        <option value="5" {if $limit==5}selected="selected"{/if}>5</option>
        <option value="10" {if $limit==10}selected="selected"{/if}>10</option>
        <option value="20" {if $limit==20}selected="selected"{/if}>20</option>
        <option value="50" {if $limit==50}selected="selected"{/if}>50</option>
        <option value="100" {if $limit==100}selected="selected"{/if}>100</option>
    </select>
    &nbsp;
    |
    &nbsp; <a {if $first_url}href="{$first_url}"{else}nohref{/if}>&lt;&lt;</a> &nbsp;
    |
    &nbsp; <a {if $previous_url}href="{$previous_url}"{else}nohref{/if}>&lt;</a> &nbsp;
    |
    {l s="Viewing $from-$to on $total (page # $current_page / $page_count)" from=$from to=$to total=$total current_page=$current_page page_count=$page_count}
    |
    &nbsp; <a {if $next_url}href="{$next_url}"{else}nohref{/if}>&gt;</a> &nbsp;
    |
    &nbsp; <a {if $last_url}href="{$last_url}"{else}nohref{/if}>&gt;&gt;</a> &nbsp;
</div>
