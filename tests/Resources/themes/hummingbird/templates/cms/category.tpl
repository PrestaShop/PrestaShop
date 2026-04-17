{**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *}

{extends file='page.tpl'}

{block name='page_title'}
  {$cms_category.name}
{/block}

{block name='page_content'}
  {block name='cms_sub_categories'}
    {if $sub_categories}
      <p>
        {l s='List of sub categories in %name%:' d='Shop.Theme.Global' sprintf=['%name%' => $cms_category.name]}
      </p>

      <ul>
        {foreach from=$sub_categories item=sub_category}
          <li>
            <a href="{$sub_category.link}">
              {$sub_category.name}
            </a>
          </li>
        {/foreach}
      </ul>
    {/if}
  {/block}

  {block name='cms_sub_pages'}
    {if $cms_pages}
      <p>
        {l s='List of pages in %category_name%:' d='Shop.Theme.Global' sprintf=['%category_name%' => $cms_category.name]}
      </p>

      <ul>
        {foreach from=$cms_pages item=cms_page}
          <li>
            <a href="{$cms_page.link}">
              {$cms_page.meta_title}
            </a>
          </li>
        {/foreach}
      </ul>
    {/if}
  {/block}
{/block}
