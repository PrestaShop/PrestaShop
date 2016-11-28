{if isset($errors) && $errors|is_array && $errors|count}
  <div class="help-block">
    <ul>
      {foreach $errors as $error}
        <li>{$error}</li>
      {/foreach}
    </ul>
  </div>
{/if}
