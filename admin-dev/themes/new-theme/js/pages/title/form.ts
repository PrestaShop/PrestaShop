/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import TitleMap from './title-map';

$(() => {
  window.prestashop.component.initComponents(
    [
      'TranslatableInput',
    ],
  );

  function onChangeImageInput() {
    $(TitleMap.supplierImageHeight).prop('disabled', true);
    $(TitleMap.supplierImageWidth).prop('disabled', true);
    const inputfiles = $(TitleMap.supplierImage).prop('files');

    if (inputfiles && inputfiles[0]) {
      $(TitleMap.supplierImageHeight).prop('disabled', false);
      $(TitleMap.supplierImageWidth).prop('disabled', false);
    }
  }

  // On loading
  onChangeImageInput();

  // On events
  $(TitleMap.supplierImage).on('change', () => onChangeImageInput());
});
