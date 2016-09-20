<section id="content" class="page-content page-not-found">
  <h4>{l s='Sorry for the inconvenience.' d='Shop.Theme'}</h4>
  <p>{l s='Search again what you are looking for' d='Shop.Theme'}</p>

  {block name='search'}
    {hook h='displaySearch'}
  {/block}

  {hook h='displayNotFound'}
</section>
