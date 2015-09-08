<!-- Hard coded CSS: we could have nothing included in case of big failure! -->
<div style="border: 7px solid {$color};">
    {foreach $messages as $message name=message}
        <ul><li>{$message}</li></ul>
        {if $smarty.foreach.messages.last}{else}<hr style="border: 1px solid {$color};" />{/if}
    {/foreach}
</div>