{if $errors|count}
  <ul class='form-errors'>
    {foreach $errors as $error}
      <li>{$error}</li>
    {/foreach}
  </ul>
{/if}
