{if $errors|count}
    {foreach $errors as $error}
    <div class="alert alert-danger" role="alert">{$error}</div>
    {/foreach}
{/if}
