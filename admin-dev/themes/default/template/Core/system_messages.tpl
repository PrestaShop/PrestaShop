<!-- Hard coded CSS: we could have nothing included in case of big failure! -->
<div style="border: 7px solid {$color};">
    <ul>
    {foreach $exceptions as $exception}
        <li><b>{get_class($exception)}</b><br/>{$exception->__toString()}</li>
    {/foreach}
    </ul>
</div>