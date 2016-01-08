{if $adminActionDisplay}
  {block name="draft_links"}
    <ul>
      {foreach from=$draftLinks item=draftLink}
        <li><a href="{$draftLink.url}">{$draftLink.title}</a></li>
      {/foreach}
    </ul>
  {/block}
{/if}
