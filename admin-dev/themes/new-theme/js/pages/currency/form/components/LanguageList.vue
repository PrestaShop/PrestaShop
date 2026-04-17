<!--*
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
