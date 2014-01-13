<script type="text/javascript">
{if isset($js_def) && is_array($js_def) && $js_def|@count}
{foreach from=$js_def key=k item=def}
{if !empty($k) && is_string($k)}
{if is_bool($def)}
var {$k} = {$def|var_export:true};
{elseif is_int($def)}
var {$k} = {$def|intval};
{elseif is_float($def)}
var {$k} = {$def|floatval|replace:',':'.'};
{elseif is_string($def)}
var {$k} = '{$def|strval}';
{elseif is_array($def) || is_object($def)}
var {$k} = {$def|json_encode};
{elseif is_null($def)}
var {$k} = null;
{else}
var {$k} = '{$def|@addcslashes:'\''}';
{/if}
{/if}
{/foreach}
{/if}
</script>
{if isset($js_files) && $js_files|@count}
{foreach from=$js_files key=k item=js_uri}
<script type="text/javascript" src="{$js_uri}"></script>
{/foreach}
{/if}
{if isset($js_inline) && $js_inline|@count}
<script type="text/javascript">
{foreach from=$js_inline key=k item=inline}
{$inline}
{/foreach}
</script>
{/if}