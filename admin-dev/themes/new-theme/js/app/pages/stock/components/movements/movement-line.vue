<template>
  <tr>
    <td>
      <PSMedia
        :thumbnail="thumbnail"
      >
        <p>
          {{ product.product_name }}
          <small v-if="hasCombination"><br />
            {{ combinationName }}
          </small>
        </p>
      </PSMedia>
    </td>
    <td>
      {{ product.product_reference }}
    </td>
    <td>
      <a v-if="orderLink" :href="orderLink" target="_blank">
        {{ product.movement_reason }}
      </a>
      <span v-else>{{ product.movement_reason }}</span>
    </td>
    <td class="text-xs-center">
      <span class="qty-number" :class="{'is-positive' : isPositive}">
        <span v-if="isPositive">+</span>
        <span v-else>-</span>
        {{ qty }}
      </span>
    </td>
    <td class="text-xs-center">
      {{ product.date_add }}
    </td>
    <td>
      {{ employeeName }}
    </td>
  </tr>
</template>
<script>
  import PSMedia from 'app/widgets/ps-media';
  import productDesc from 'app/pages/stock/mixins/product-desc';

  export default {
    props: ['product'],
    mixins: [productDesc],
    computed: {
      qty() {
        return this.product.physical_quantity;
      },
      employeeName() {
        return `${this.product.employee_firstname} ${this.product.employee_lastname}`;
      },
      isPositive() {
        return this.product.sign > 0;
      },
      orderLink() {
        return this.product.order_link !== 'N/A' ? this.product.order_link : null;
      },
    },
    components: {
      PSMedia,
    },
  };
</script>

<style lang="sass" scoped>
  @import "~PrestaKit/scss/custom/_variables.scss";
  .qty-number {
    padding: 2px 5px;
    background-color: $gray-dark;
    display: inline-block;
    min-width: 50px;
    color: white;
    &.is-positive {
      background-color: $brand-primary;
    }
  }
</style>
