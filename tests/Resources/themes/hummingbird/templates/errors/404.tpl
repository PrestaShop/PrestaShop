{**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *}
{extends file='page.tpl'}

{block name='breadcrumb'}{/block}

{block name='container_class'}container container--limited-md text-center{/block}

{block name='page_header_container'}
  {block name='page_title'}
    <div class="page-header mb-2">
      <p class="display-1">{l s='404' d='Shop.Theme.Catalog'}</p>
    </div>
  {/block}
{/block}

{capture assign="errorContent"}
  <h1 class="h4">{$page.title}</h1>
  <p>
    {l
      s='If this is a recurring problem, please [1]contact us[/1]'
      d='Shop.Theme.Catalog'
      sprintf=[
        '[1]' => '<a href="{$urls.pages.contact}" class="alert-link">',
        '[/1]' => '</a>'
      ]
    }
  </p>
{/capture}

{block name='page_content_container'}
  {include file='errors/not-found.tpl' errorContent=$errorContent}
{/block}
