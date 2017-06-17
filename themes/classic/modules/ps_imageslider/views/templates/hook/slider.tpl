{**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}

{if $homeslider.slides}
  <div id="carousel" data-ride="carousel" class="carousel slide" data-interval="{$homeslider.speed}" data-wrap="{(string)$homeslider.wrap}" data-pause="{$homeslider.pause}">
    <div class="carousel-inner" role="listbox">
      {foreach from=$homeslider.slides item=slide name='homeslider'}
        <div class="carousel-item {if $smarty.foreach.homeslider.first}active{/if}" role="option" aria-hidden="{if $smarty.foreach.homeslider.first}false{else}true{/if}">
          <a href="{$slide.url}">
            <figure>
              <img src="{$slide.image_url}" alt="{$slide.legend|escape}" class="d-block img-fluid">
              {if $slide.title || $slide.description}
                <figcaption>
                  <div class="carousel-caption d-none d-md-block">
                    <h2 class="display-1 text-uppercase">{$slide.title}</h2>
                    <div class="caption-description">{$slide.description nofilter}</div>
                  </div>
                </figcaption>
              {/if}
            </figure>
          </a>
        </div>
      {/foreach}
      <div class="direction" aria-label="{l s='Carousel buttons' d='Shop.Theme.Global'}">
        <a class="carousel-control-prev" href="#carousel" role="button" data-slide="prev">
          <i class="material-icons">&#xE5CB;</i>
          <span class="sr-only">{l s='Previous' d='Shop.Theme.Global'}</span>
        </a>
        <a class="carousel-control-next" href="#carousel" role="button" data-slide="next">
          <i class="material-icons">&#xE5CC;</i>
          <span class="sr-only">{l s='Next' d='Shop.Theme.Global'}</span>
        </a>
      </div>
    </div>
  </div>
{/if}
