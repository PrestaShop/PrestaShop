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
<div class="js-productinfo mt-1">
  {if isset($vars_nb_people)}
    <p>
      {if $vars_nb_people['%nb_people%'] == 1}
        {l s='1 person is currently watching this product.' d='Shop.Theme.Catalog'}
      {else}
        {l s='%nb_people% people are currently watching this product.' sprintf=$vars_nb_people d='Shop.Theme.Catalog'}
      {/if}
    </p>
  {/if}

  {if isset($vars_date_last_order)}
    <p>{l s='Last time this product was bought: %date_last_order%' sprintf=$vars_date_last_order d='Shop.Theme.Catalog'}</p>
  {/if}

  {if isset($vars_date_last_cart)}
    <p>{l s='Last time this product was added to a cart: %date_last_cart%' sprintf=$vars_date_last_cart d='Shop.Theme.Catalog'}</p>
  {/if}
</div>
