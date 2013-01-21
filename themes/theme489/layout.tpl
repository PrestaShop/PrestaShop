{if !empty($display_header)}
	{include file='./header.tpl' HOOK_HEADER=$HOOK_HEADER}
{/if}
{if !empty($template)}
	{$template}
{/if}
{if !empty($display_footer)}
	{include file='./footer.tpl'}
{/if}
{if !empty($live_edit)}
	{$live_edit}
{/if}