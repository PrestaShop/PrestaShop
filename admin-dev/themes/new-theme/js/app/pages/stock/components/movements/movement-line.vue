<!--*
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *-->
<template>
  <tr>
    <td>
      {{ product.product_id }}
    </td>
    <td>
      <div class="d-flex align-items-center">
        <PSMedia
          class="d-flex align-items-center"
          :thumbnail="thumbnail"
        >
          <p>
            <a
              :href="product.product_url"
              class="product-name-link"
            >{{ product.product_name }}</a>
            <small v-if="hasCombination"><br>
              {{ product.combination_name }}
            </small>
          </p>
        </PSMedia>
      </div>
    </td>
    <td>
      {{ product.product_reference }}
    </td>
    <td>
      <a
        v-if="orderLink"
        :href="orderLink"
        target="_blank"
      >
        {{ product.movement_reason }}
      </a>
      <span v-else>{{ product.movement_reason }}</span>
    </td>
    <td class="text-sm-center">
      <span
        class="qty-number"
        :class="{'is-positive' : isPositive}"
      >
        <span v-if="isPositive">+</span>
        <span v-else>-</span>
        {{ qty }}
      </span>
    </td>
    <td class="text-sm-center">
      {{ product.date_add_formatted }}
    </td>
    <td>
      {{ employeeName }}
    </td>
  </tr>
</template>

<script lang="ts">
  import PSMedia from '@app/widgets/ps-media.vue';
  import productDesc from '@app/pages/stock/mixins/product-desc';
  import {defineComponent, PropType} from 'vue';
  import {StockMovement} from './index.vue';

  export default defineComponent({
    props: {
      product: {
        type: Object as PropType<StockMovement>,
        required: true,
      },
    },
    mixins: [productDesc],
    computed: {
      qty(): number {
        return this.product.physical_quantity;
      },
      employeeName(): string {
        return `${this.product.employee_firstname} ${this.product.employee_lastname}`;
      },
      isPositive(): boolean {
        return this.product.sign > 0;
      },
      orderLink(): string | null {
        return this.product.order_link !== 'N/A' ? this.product.order_link : null;
      },
    },
    components: {
      PSMedia,
    },
  });
</script>

<style lang="scss" scoped>
@import '~@scss/config/_settings.scss';

.product-name-link {
  color: inherit;
  text-decoration: none;

  &:hover {
    color: var(--#{$cdk}primary-500);
    text-decoration: underline;
  }
}
</style>
