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
<!doctype html>
<html lang="">

  <head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">

    {block name='head_seo'}
      <title>{block name='head_seo_title'}{/block}</title>
      <meta name="description" content="{block name='head_seo_description'}{/block}">
      <meta name="keywords" content="{block name='head_seo_keywords'}{/block}">
    {/block}
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {block name='stylesheets'}
      {include file="_partials/stylesheets.tpl" stylesheets=$stylesheets}
    {/block}

  </head>

  <body>

    <div id="layout-error">
      {block name='content'}
        <p>Hello world! This is HTML5 Boilerplate.</p>
      {/block}
    </div>

  </body>

</html>
