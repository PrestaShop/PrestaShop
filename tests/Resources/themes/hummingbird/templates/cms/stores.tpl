{**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *}

{extends file='page.tpl'}

{block name='page_title'}
  {l s='Our stores' d='Shop.Theme.Global'}
{/block}

{block name='page_content'}
  <div class="row">
    {foreach $stores as $store}
      <article id="store-{$store.id}" class="store col-md-6 col-lg-4 col-xl-6">
        <div class="card">
          <div class="card-body">
            <div class="row">
              <div class="col-xl-6 store__picture">
                <picture>
                  {if isset($store.image.bySize.default_m.sources.avif)}
                    <source 
                      srcset="
                        {$store.image.bySize.default_m.sources.avif},
                        {$store.image.bySize.default_xl.sources.avif} 2x"
                      type="image/avif"
                    >
                  {/if}

                  {if isset($store.image.bySize.default_m.sources.webp)}
                    <source 
                      srcset="
                        {$store.image.bySize.default_m.sources.webp},
                        {$store.image.bySize.default_xl.sources.webp} 2x"
                      type="image/webp"
                    >
                  {/if}

                  <img
                    class="img-fluid rounded"
                    srcset="
                      {$store.image.bySize.default_m.url},
                      {$store.image.bySize.default_xl.url} 2x"
                    loading="lazy"
                    width="{$store.image.bySize.default_m.width}"
                    height="{$store.image.bySize.default_m.height}"
                    {if !empty($store.image.legend)}
                      alt="{$store.image.legend}"
                      title="{$store.image.legend}"
                    {else}
                      alt="{$store.name}"
                    {/if}
                </picture>
              </div>

              <div class="col-xl-6 store__description d-none d-md-block">
                <h2 class="h5 store__name">
                  {$store.name}
                </h2>

                <address class="store__address">
                  {$store.address.formatted nofilter}
                </address>

                {if $store.note || $store.phone || $store.fax || $store.email}
                  <a data-bs-toggle="collapse" href="#about-{$store.id}" aria-expanded="false" aria-controls="about-{$store.id}">
                    <strong>{l s='About and Contact' d='Shop.Theme.Global'}</strong><i class="material-icons" aria-hidden="true">&#xE409;</i>
                  </a>
                {/if}

                <hr>

                <table class="store__opening-times">
                  {foreach $store.business_hours as $day}
                    <tr>
                      <th>
                        {$day.day}
                      </th>

                      <td>
                        <ul>
                          {foreach $day.hours as $h}
                            <li>
                              {$h}
                            </li>
                          {/foreach}
                        </ul>
                      </td>
                    </tr>
                  {/foreach}
                </table>
              </div>

              <div class="store__description accordion d-block d-md-none">
                <div class="accordion-item">
                  <h2 class="h5 store__name">
                    {$store.name}
                  </h2>

                  <address class="store__address">
                    {$store.address.formatted nofilter}
                  </address>

                  {if $store.note || $store.phone || $store.fax || $store.email}
                    <a data-bs-toggle="collapse" href="#about-{$store.id}" aria-expanded="false" aria-controls="about-{$store.id}">
                      <strong>{l s='About and Contact' d='Shop.Theme.Global'}</strong><i class="material-icons" aria-hidden="true">&#xE409;</i>
                    </a>
                  {/if}

                  <hr>

                  <button class="store__toggle accordion-button collapsed pb-2 px-0" data-bs-toggle="collapse" data-bs-target="#table-{$store.id}">
                    {l s='View schedules' d='Shop.Theme.Global'}
                  </button>

                  <table id="table-{$store.id}" class="store__opening-times accordion-collapse collapse">
                    {foreach $store.business_hours as $day}
                      <tr>
                        <th>
                          {$day.day|truncate:4:'.'}
                        </th>

                        <td>
                          <ul>
                            {foreach $day.hours as $h}
                              <li>
                                {$h}
                              </li>
                            {/foreach}
                          </ul>
                        </td>
                      </tr>
                    {/foreach}
                  </table>
                </div>
              </div>
            </div>
          </div>

          {if $store.note || $store.phone || $store.fax || $store.email}
            <div class="card-footer store__footer collapse" id="about-{$store.id}">
              {if $store.note}
                  <p class="store__note">{$store.note}</p>
              {/if}

              <ul class="store__contacts">
                {if $store.phone}
                  <li>
                    <i class="material-icons" aria-hidden="true">&#xE0B0;</i>{$store.phone}
                  </li>
                {/if}

                {if $store.fax}
                  <li>
                    <i class="material-icons" aria-hidden="true">&#xE8AD;</i>{$store.fax}
                  </li>
                {/if}

                {if $store.email}
                  <li>
                    <i class="material-icons" aria-hidden="true">&#xE0BE;</i>{$store.email}
                  </li>
                {/if}
              </ul>
            </div>
          {/if}
        </div>
      </article>
    {/foreach}
  </div>
{/block}
