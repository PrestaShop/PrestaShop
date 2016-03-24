{extends file='page.tpl'}

{block name='page_title'}
  {l s='The page you are looking for was not found.'}
{/block}

{block name='page_content_container'}
  <section id="content" class="page-content page-not-found">
    <h4>{l s='Sorry for inconvenience.'}</h4>
    <p>{l s='Search again what you are looking for'}</p>

    {block name='search'}
      {hook h='displaySearch'}
    {/block}

    {hook h='displayNotFound'}
  </section>
{/block}
