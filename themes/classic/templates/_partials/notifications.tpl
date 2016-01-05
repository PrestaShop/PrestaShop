<aside id="notifications">

  {if $notifications.error}
    <article class="notification notification-error">
      <ul>
        {foreach $notifications.error as $notif}
          <li>{$notif}</li>
        {/foreach}
      </ul>
    </article>
  {/if}

  {if $notifications.warning}
    <article class="notification notification-warning">
      <ul>
        {foreach $notifications.warning as $notif}
          <li>{$notif}</li>
        {/foreach}
      </ul>
    </article>
  {/if}

  {if $notifications.success}
    <article class="notification notification-success">
      <ul>
        {foreach $notifications.success as $notif}
          <li>{$notif}</li>
        {/foreach}
      </ul>
    </article>
  {/if}

  {if $notifications.info}
    <article class="notification notification-info">
      <ul>
        {foreach $notifications.info as $notif}
          <li>{$notif}</li>
        {/foreach}
      </ul>
    </article>
  {/if}

</aside>
