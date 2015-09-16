<article id="address-{$address.id}" class="address">
  <header>
    <h1 class="h4">{$address.alias}</h1>
  </header>

  <p>{$address.formatted}</p>

{* StarterTheme: find a good idea to not build url in template *}
  <footer class="actions address-actions">
    <ul class="action-list">
      <li>
        <a href="{$urls.pages.address}id_address={$address.id}">
          {l s='Update'}
        </a>
      </li>
      <li>
        <a href="{$urls.pages.address}id_address={$address.id}&delete">
          {l s='Delete'}
        </a>
      </li>
    </ul>
  </footer>
</article>
