
<aside id="notifications">
  <div class="container">
    {if $notifications.error}
      <article class="alert alert-danger" role="alert">
        <ul>
          {foreach $notifications.error as $notif}
            <li>{$notif}</li>
          {/foreach}
        </ul>
      </article>
    {/if}

    {if $notifications.warning}
      <article class="alert alert-warning" role="alert">
        <ul>
          {foreach $notifications.warning as $notif}
            <li>{$notif}</li>
          {/foreach}
        </ul>
      </article>
    {/if}

    {if $notifications.success}
      <article class="alert alert-success" role="alert">
        <ul>
          {foreach $notifications.success as $notif}
            <li>{$notif}</li>
          {/foreach}
        </ul>
      </article>
    {/if}

    {if $notifications.info}
      <article class="alert alert-info" role="alert">
        <ul>
          {foreach $notifications.info as $notif}
            <li>{$notif}</li>
          {/foreach}
        </ul>
      </article>
    {/if}
  </div>
</aside>
