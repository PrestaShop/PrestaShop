{**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *}

{extends file='page.tpl'}

{block name='page_title'}
  {l s='Sitemap' d='Shop.Theme.Global'}
{/block}

{block name='page_content'}
  <div class="row sitemap">
    {foreach $sitemapUrls as $group}
      <div class="col-md-6 col-lg-3">
        <h2 class="h3">
          {$group.name}
        </h2>

        {include file='cms/_partials/sitemap-nested-list.tpl' links=$group.links}

        <hr class="d-block d-md-none">
      </div>
    {/foreach}
  </div>
{/block}
