/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
import {defineComponent} from 'vue';

interface ProductDescProps {
  product: Record<string, any>;
  thumbnail?: string;
  hasCombination?: boolean;
}

export default defineComponent<ProductDescProps>({
  computed: {
    thumbnail(): string | undefined {
      if (this.product.combination_thumbnail !== 'N/A') {
        return `${this.product.combination_thumbnail}`;
      }

      if (this.product.product_thumbnail !== 'N/A') {
        return `${this.product.product_thumbnail}`;
      }

      return undefined;
    },

    combinationName(): string {
      const combinations = this.product.combination_name.split(',');
      const attributes = this.product.attribute_name.split(',');
      const separator = ' - ';
      let attr = '';

      combinations.forEach((attribute: string, index: string) => {
        const value = attribute.trim().slice(attributes[index].trim().length + separator.length);
        attr += attr.length ? ` - ${value}` : value;
      });

      return attr;
    },

    hasCombination() {
      return !!this.product.combination_id;
    },
  },
});
