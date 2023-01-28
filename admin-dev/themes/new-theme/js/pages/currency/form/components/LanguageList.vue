<!--**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
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
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 *-->
<template>
  <table class="grid-table js-grid-table table">
    <thead class="thead-default">
      <tr class="column-headers">
        <th scope="col">
          {{ $t('list.language') }}
        </th>
        <th scope="col">
          {{ $t('list.example') }}
        </th>
        <th scope="col">
          <div class="text-right">
            {{ $t('list.edit') }}
          </div>
        </th>
        <th scope="col">
          <div class="grid-actions-header-text">
            {{ $t('list.reset') }}
          </div>
        </th>
      </tr>
    </thead>
    <tbody>
      <tr
        v-for="language in languages"
        :key="language.id"
      >
        <td>
          {{ language.name }}
        </td>
        <td>
          {{ displayFormat(language) }}
        </td>
        <td>
          <div class="btn-group-action text-right">
            <div class="btn-group">
              <button
                type="button"
                class="btn"
                @click.prevent.stop="$emit('selectLanguage', language)"
              >
                <i class="material-icons">edit</i>
              </button>
            </div>
          </div>
        </td>
        <td>
          <div class="btn-group-action text-right">
            <div class="btn-group">
              <button
                type="button"
                class="btn"
                @click.prevent.stop="$emit('resetLanguage', language)"
              >
                <i class="material-icons">refresh</i>
              </button>
            </div>
          </div>
        </td>
      </tr>
    </tbody>
  </table>
</template>

<script>
  import {NumberFormatter} from '@app/cldr';

  export default {
    name: 'LanguageList',
    props: {
      languages: {
        type: Array,
        required: true,
      },
    },
    methods: {
      displayFormat(language) {
        const currencyFormatter = NumberFormatter.build(language.priceSpecification);

        return this.$t('list.example.format', {
          price: currencyFormatter.format(14251999.42),
          discount: currencyFormatter.format(-566.268),
        });
      },
    },
  };
</script>
