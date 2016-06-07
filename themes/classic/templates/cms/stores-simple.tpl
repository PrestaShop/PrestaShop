{extends file='page.tpl'}

{block name='page_title'}
  {l s='Our stores' d='Shop.Theme'}
{/block}

{block name='page_content_container'}
  <section id="content" class="page-content page-stores">

    {foreach $stores as $store}
      <article id="store-{$store.id}" class="store-item card">
        <div class="store-item-container">
          <div class="store-picture">
            <img src="{$store.image}" alt="">
          </div>
          <div class="card-block store-description">
            <h3 class="h3 card-title">{$store.name}</h3>
            <address>{$store.address.formatted nofilter}</address>
            {if $store.note || $store.phone || $store.fax || $store.email}
              <a data-toggle="collapse" href="#about-{$store.id}" aria-expanded="false" aria-controls="about-{$store.id}"><strong>{l s='About and Contact' d='Shop.Theme'}</strong><i class="material-icons">&#xE409;</i></a>
            {/if}
          </div>
          <div class="card-block divide-left">
            <table>
              {foreach $store.business_hours as $day}
              <tr>
                <td><strong>{$day.day|truncate:4:'.'}</strong></td>
                <td>
                  <ul>
                  {foreach $day.hours as $h}
                    <li>{$h}</li>
                  {/foreach}
                  </ul>
                </td>
              </tr>
              {/foreach}
            </table>
          </div>
        </div>
        <footer id="about-{$store.id}" class="collapse">
          <div class="store-item-footer divide-top">
            <div class="card-block">
              {if $store.note}
                <p class="text-justify">{$store.note}<p>
              {/if}
            </div>
            <ul class="card-block">
              {if $store.phone}
                <li><i class="material-icons">&#xE0B0;</i>{$store.phone}</li>
              {/if}
              {if $store.fax}
                <li><i class="material-icons">&#xE8AD;</i>{$store.fax}</li>
              {/if}
              {if $store.email}
                <li><i class="material-icons">&#xE0BE;</i>{$store.email}</li>
              {/if}
            </ul>
          </div>
        </footer>
      </article>
    {/foreach}

  </section>
{/block}
