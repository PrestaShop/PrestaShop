
<aside id="notifications">
  <div class="container">
    {if $notifications.error}
      <article class="alert alert-danger" role="alert" data-alert="danger">
        <ul>
          {foreach $notifications.error as $notif}
            <li>{$notif}</li>
          {/foreach}
        </ul>
      </article>
    {/if}

    {if $notifications.warning}
      <article class="alert alert-warning" role="alert" data-alert="warning">
        <ul>
          {foreach $notifications.warning as $notif}
            <li>{$notif}</li>
          {/foreach}
        </ul>
      </article>
    {/if}

    {if $notifications.success}
      <article class="alert alert-success" role="alert" data-alert="success">
        <ul>
          {foreach $notifications.success as $notif}
            <li>{$notif}</li>
          {/foreach}
        </ul>
      </article>
    {/if}

    {if $notifications.info}
      <article class="alert alert-info" role="alert" data-alert="info">
        <ul>
          {foreach $notifications.info as $notif}
            <li>{$notif}</li>
          {/foreach}
        </ul>
      </article>
    {/if}
  </div>
</aside>
