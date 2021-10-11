{**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 *}
<script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "Organization",
    "name" : "{$shop.name}",
    "url" : "{$urls.pages.index}",
    {if $shop.logo}
      "logo": {
        "@type": "ImageObject",
        "url":"{$shop.logo.src}"
      }
    {/if}
  }
</script>

<script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "WebPage",
    "isPartOf": {
      "@type": "WebSite",
      "url":  "{$urls.pages.index}",
      "name": "{$shop.name}"
    },
    "name": "{$page.meta.title}",
    "url":  "{$urls.current_url}"
  }
</script>

{if $page.page_name == 'index'}
  <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "WebSite",
      "url" : "{$urls.pages.index}",
      {if $shop.logo}
        "image": {
          "@type": "ImageObject",
          "url":"{$shop.logo.src}"
        },
      {/if}
      "potentialAction": {
        "@type": "SearchAction",
        "target": "{'--search_term_string--'|str_replace:'{search_term_string}':$link->getPageLink('search',true,null,['search_query'=>'--search_term_string--'])}",
        "query-input": "required name=search_term_string"
      }
    }
  </script>
{/if}

{if isset($breadcrumb.links[1])}
  <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "BreadcrumbList",
      "itemListElement": [
        {foreach from=$breadcrumb.links item=path name=breadcrumb}
          {
            "@type": "ListItem",
            "position": {$smarty.foreach.breadcrumb.iteration},
            "name": "{$path.title}",
            "item": "{$path.url}"
          }{if !$smarty.foreach.breadcrumb.last},{/if}
        {/foreach}
      ]
    }
  </script>
{/if}
