<!-- Hard coded CSS: we could have nothing included in case of big failure! -->
<div style="border: 7px solid {$color};">
    {foreach $exceptions as $exception name=exceptions}
        <ul><li>{if $exception instanceof Exception}{$exception->__toString()}{else}{$exception}{/if}</li></ul>
        {if !$smarty.foreach.exceptions.last}<hr style="border: 1px solid {$color};" />{/if}
    {/foreach}
</div>