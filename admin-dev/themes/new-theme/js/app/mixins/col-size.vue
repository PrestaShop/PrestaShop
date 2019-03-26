<!--**
     * 2007-2019 PrestaShop and Contributors
     *
     * NOTICE OF LICENSE
     *
     * This source file is subject to the Open Software License (OSL 3.0)
     * that is bundled with this package in the file LICENSE.txt.
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
     * needs please refer to https://www.prestashop.com for more information.
     *
     * @author    PrestaShop SA <contact@prestashop.com>
     * @copyright 2007-2019 PrestaShop SA and Contributors
     * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
     * International Registered Trademark & Property of PrestaShop SA
     *-->
<script>
  /**
   * This mixin component will help you to calculate your col size
   * depending on object size.
   */

  // Bootstrap row length
  const ROW_LENGTH = 12;

  export default {
    methods: {
      getClasses(object, offsetCondition = true) {
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
