{**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *}
{extends file='page_header_toolbar.tpl'}

{block name=pageTitle}
  {if !isset($use_regular_h1_structure)}
    {assign var="use_regular_h1_structure" value=true}
  {/if}

  {if $use_regular_h1_structure}
    <h1 class="title">
      {if is_array($title)}{$title|end|escape}{else}{$title|escape}{/if}
    </h1>
  {else}
    {$title}
  {/if}
{/block}
