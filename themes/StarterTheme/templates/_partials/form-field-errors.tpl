{if $errors|count}
  <ul>
    {foreach $errors as $error}
      <li>{$error nofilter}</li>
    {/foreach}
  </ul>
{/if}
