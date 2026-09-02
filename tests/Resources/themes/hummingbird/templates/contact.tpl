{**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *}
{extends file='page.tpl'}

{block name='page_header_container'}{/block}

{if $layout === 'layouts/layout-left-column.tpl'}
  {block name='left_column'}
    <div id="left-column" class="wrapper__left-column col-md-4 col-lg-3">
      {hook h='displayContactLeftColumn'}
    </div>
  {/block}
{elseif $layout === 'layouts/layout-right-column.tpl'}
  {block name='right_column'}
    <div id="right-column" class="wrapper__right-column col-md-4 col-lg-3">
      {hook h='displayContactRightColumn'}
    </div>
  {/block}
{/if}

{block name='page_content'}
  {hook h='displayContactContent'}
{/block}
