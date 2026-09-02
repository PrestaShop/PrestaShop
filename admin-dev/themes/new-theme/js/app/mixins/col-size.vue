<!--*
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *-->
<script lang="ts">
  /**
   * This mixin component will help you to calculate your col size
   * depending on object size.
   */

  // Bootstrap row length
  const ROW_LENGTH = 12;

  export default {
    methods: {
      getClasses(object: Record<string, any>, offsetCondition = true): Array<string> {
        const objectLength = Object.keys(object).length;
        const size = Math.floor(ROW_LENGTH / objectLength);
        const left = Math.ceil(ROW_LENGTH % objectLength);
        const classes = [`col-${size}`];

        if (left !== 0 && offsetCondition) {
          classes.push(`offset-${left}`);
        }

        return classes;
      },
    },
  };
</script>
