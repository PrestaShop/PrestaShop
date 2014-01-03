{strip}
<script type="text/javascript">
{if isset($js_defs)}
{foreach from=$js_defs key=k item=js_def}
var {$k} = '{$js_def|@addcslashes:'\''}';
{/foreach}
{/if}
</script>
{if isset($js_files)}
{foreach from=$js_files key=k item=js_uri}
<script type="text/javascript" src="{$js_uri}"></script>
{/foreach}
{/if}
{/strip}