{if isset($js_files)}
{foreach from=$js_files key=k item=js_uri}
<script type="text/javascript" src="{$js_uri}"></script>
{/foreach}
{/if}
<script type="text/javascript">
{if isset($js_def) && is_array($js_def) && $js_def|@count}
{foreach from=$js_def key=k item=def}
{if !empty($k)}
{if is_bool($def)}
var {$k|strval} = {$def|var_export:true};
{elseif is_int($def)}
var {$k|strval} = {$def|intval};
{elseif is_float($def)}
var {$k|strval} = {$def|floatval};
{elseif is_string($def)}
var {$k|strval} = '{$def|strval}';
{elseif is_array($def) || is_object($def)}
var {$k|strval} = {$def|json_encode};
{elseif is_null($def)}
var {$k|strval} = null;
{else}
var {$k|strval} = '{$def|@addcslashes:'\''}';
{/if}
{/if}
{/foreach}
{/if}
</script>