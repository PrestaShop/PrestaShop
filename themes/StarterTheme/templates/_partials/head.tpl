  <meta charset="utf-8">
  <meta http-equiv="x-ua-compatible" content="ie=edge">

  {block name="head_seo"}
    {block name="head_seo_title"}
      <title>{$page.meta_title}</title>
    {/block}
    {block name="head_seo_description"}
      <meta name="description" content="{$page.meta_description}">
    {/block}
    {block name="head_seo_keywords"}
      <meta name="keywords" content="{$page.meta_keywords}" />
    {/block}
  {/block}

  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- TODO: favicon.ico and apple icons -->

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
