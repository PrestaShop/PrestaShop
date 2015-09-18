<div>
    &nbsp; <a {if $first_url}href="{$first_url}"{else}nohref{/if}>&lt;&lt;</a> &nbsp;
    |
    &nbsp; <a {if $previous_url}href="{$previous_url}"{else}nohref{/if}>&lt;</a> &nbsp;
    |
    viewing {$from}-{$to} on {$total}
    (page# {$current_page} / {$page_count})
    |
    &nbsp; <a {if $next_url}href="{$next_url}"{else}nohref{/if}>&gt;</a> &nbsp;
    |
    &nbsp; <a {if $last_url}href="{$last_url}"{else}nohref{/if}>&gt;&gt;</a> &nbsp;
</div>
