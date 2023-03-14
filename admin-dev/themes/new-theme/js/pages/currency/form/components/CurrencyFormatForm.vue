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
  <div class="row">
    <div class="col-4">
      <h4>{{ $t('step.symbol') }}</h4>
      <input
        data-role="custom-symbol"
        type="text"
        v-model="customSymbol"
      >
    </div>
    <div class="col-8 border-left">
      <h4>{{ $t('step.format') }}</h4>
      <div class="row">
        <div
          class="ps-radio col-6"
          v-for="(pattern, transformation) in availableFormats"
          :key="transformation"
          :id="transformation"
        >
          <input
            type="radio"
            :checked="transformation === customTransformation"
            :value="transformation"
          >
          <label @click.prevent.stop="customTransformation = transformation">
            {{ displayPattern(pattern) }}
          </label>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import {NumberFormatter} from '@app/cldr';
  import {defineComponent} from 'vue';

  export default defineComponent({
    name: 'CurrencyFormatForm',
    data: () => ({
      value: {
        symbol: '',
        transformation: '',
      },
    }),
    props: {
      language: {
        type: Object,
        required: true,
        default: () => {},
      },
    },
    computed: {
      availableFormats() {
        return this.language.transformations;
      },
      customSymbol: {
        get() {
          return this.value.symbol;
        },
        set(symbol) {
          this.value.symbol = symbol;
          this.$emit('formatChange', this.value);
        },
      },
      customTransformation: {
        get() {
          return this.value.transformation;
        },
        set(transformation) {
          this.value.transformation = transformation;
          this.$emit('formatChange', this.value);
        },
      },
    },
    methods: {
      displayPattern(pattern) {
        const patterns = pattern.split(';');
        const priceSpecification = {...this.language.priceSpecification};
        priceSpecification.positivePattern = patterns[0];
        priceSpecification.negativePattern = patterns.length > 1 ? patterns[1] : `-${pattern}`;
        priceSpecification.currencySymbol = this.customSymbol;

        const currencyFormatter = NumberFormatter.build(priceSpecification);

        return currencyFormatter.format(14251999.42);
      },
    },
    mounted() {
      this.customSymbol = this.language.priceSpecification.currencySymbol;
      const currencyPattern = this.language.priceSpecification.positivePattern;

      // Detect which transformation matches the language pattern
      /* eslint-disable-next-line no-restricted-syntax,guard-for-in */
      for (const transformation in this.language.transformations) {
        const transformationPatterns = this.language.transformations[
          transformation
        ].split(';');

        if (transformationPatterns[0] === currencyPattern) {
          this.customTransformation = transformation;
          break;
        }
      }
    },
  });
</script>
