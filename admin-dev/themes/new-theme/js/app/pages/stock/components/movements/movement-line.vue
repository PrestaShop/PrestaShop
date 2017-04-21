<template>
  <tr>
    <td>
      <ProductDesc
        :name="movement.product_name"
        :thumbnail="movement.combination_thumbnail"
        :combinationName="movement.combination_name"
      />
    </td>
    <td>
      {{ movement.product_reference }}
    </td>
    <td>
      {{ movement.movement_reason }}
    </td>
    <td class="text-xs-center">
      <span class="qty-number" :class="{'is-positive' : isPositive}">{{ qty }}</span>
    </td>
    <td class="text-xs-center">
      {{ movement.date_add }}
    </td>
    <td>
      {{ employeeName }}
    </td>
  </tr>
</template>
<script>
  import ProductDesc from 'app/pages/stock/components/product/product-desc';
  export default {
    props: ['movement'],
    computed: {
      qty() {
        return this.movement.sign * this.movement.physical_quantity;
      },
      employeeName() {
        return `${this.movement.employee_firstname} ${this.movement.employee_lastname}`;
      },
      isPositive() {
        return this.movement.sign > 0;
      }
    },
    components: {
      ProductDesc
    }
  }
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
