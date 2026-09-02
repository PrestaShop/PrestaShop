/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

$(() => {
  window.prestashop.component.initComponents(
    [
      'TranslatableInput',
    ],
  );
  new window.prestashop.component.ChoiceTree('#feature_shop_association').enableAutoCheckChildren();
});
