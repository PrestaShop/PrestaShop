<!-- Hard coded CSS: we could have nothing included in case of big failure! -->
<div style="border: 7px solid {$color};">
    <script src="https://code.jquery.com/jquery-1.11.3.min.js"></script>
    {foreach $exceptions as $exception name=exceptions}
        <ul><li>{$exception->__toStringHtml()}</li></ul>
        {if $smarty.foreach.exceptions.last}{else}<hr style="border: 1px solid {$color};" />{/if}
    {/foreach}
</div>