{**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}
{extends file='page.tpl'}

{block name='page_title'}
  {l s='Our stores' d='Shop.Theme.Global'}
{/block}

{block name='page_content_container'}
  <section id="content" class="page-content page-stores">

    {foreach $stores as $store}
      <article id="store-{$store.id}" class="store-item card">
        <div class="store-item-container clearfix">
          <div class="col-md-3 store-picture d-none d-md-block">
            <img src="{$store.image.bySize.stores_default.url}" alt="{$store.image.legend}" title="{$store.image.legend}">
          </div>
          <div class="col-sm-7 col-md-5 store-description">
            <p class="h3 card-title">{$store.name}</p>
            <address>{$store.address.formatted nofilter}</address>
            {if $store.note || $store.phone || $store.fax || $store.email}
              <a data-toggle="collapse" href="#about-{$store.id}" aria-expanded="false" aria-controls="about-{$store.id}"><strong>{l s='About and Contact' d='Shop.Theme.Global'}</strong><i class="material-icons">&#xE409;</i></a>
            {/if}
          </div>
          <div class="col-sm-5 col-md-4 divide-left">
            <table>
              {foreach $store.business_hours as $day}
              <tr>
                <th>{$day.day|truncate:4:'.'}</th>
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
            <div class="card-body">
              {if $store.note}
                <p class="text-justify">{$store.note}<p>
              {/if}
            </div>
            <ul class="card-body">
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
