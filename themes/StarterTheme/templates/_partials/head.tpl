<meta charset="utf-8">
<meta http-equiv="x-ua-compatible" content="ie=edge">

{block name="head_seo"}
  <title>{block name="head_seo_title"}{/block}</title>
  <meta name="description" content="{block name='head_seo_description'}{/block}" />
  <meta name="keywords" content="{block name='head_seo_keywords'}{/block}" />
{/block}

<meta name="viewport" content="width=device-width, initial-scale=1" />

{* StarterTheme: favicon.ico and apple icons *}

{if isset($css_files)}
  {foreach from=$css_files key=css_uri item=media}
    <link rel="stylesheet" href="{$css_uri}" type="text/css" media="{$media}" />
  {/foreach}
{/if}
{if isset($js_defer) && !$js_defer && isset($js_files) && isset($js_def)}
  {$js_def}
  {foreach from=$js_files item=js_uri}
    <script type="text/javascript" src="{$js_uri}"></script>
  {/foreach}
{/if}

{block name="hook_header"}
  {hook h='displayHeader'}
{/block}
