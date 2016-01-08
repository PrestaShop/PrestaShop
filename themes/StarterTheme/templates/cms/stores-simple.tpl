{extends file="page.tpl"}

{block name="page_title"}
  {l s="Our stores"}
{/block}

{block name="page_content_container"}
  <section id="content" class="page-content page-stores">

    {foreach $stores as $store}
      <article id="store-{$store.id}" class="store-item">
        <header>
          <h3>{$store.name}</h3>
        </header>

        <div>
          <div class="store-picture">
            <img src="" alt="">
          </div>
          <ul>
            <li>{$store.address.formatted nofilter}</li>
            {if $store.phone}
              <li>{l s="Phone:"} {$store.phone}</li>
            {/if}
            {if $store.fax}
              <li>{l s="Fax:"} {$store.fax}</li>
            {/if}
            {if $store.email}
              <li>{l s="Email:"} {$store.email}</li>
            {/if}
          </ul>
          <p>{l s="Openning hours"}</p>
          <table>
            {foreach $store.business_hours as $day}
            <tr>
              <td>{$day.day}</td>
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

      </article>
    {/foreach}

  </section>
{/block}
