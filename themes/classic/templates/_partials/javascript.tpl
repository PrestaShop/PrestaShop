{foreach $javascript.external as $js}
  <script type="text/javascript" src="{$js.uri}"></script>
{/foreach}

{foreach $javascript.inline as $js}
  <script type="text/javascript">
    {$js.content nofilter}
  </script>
{/foreach}
