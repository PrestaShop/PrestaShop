/**
 * 2007-2018 PrestaShop.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

export default class Importer {
  import() {
    this._updateProgress(0);
    this._showProgressModal();

    this._ajaxImport();
  }

  _ajaxImport() {
    let offset = 0,
        limit = 0,
        validateOnly = false,
        moreStep = 0;

    //@todo fix parameters
    $.post({
      url: 'index.php?offset='+offset+'&limit='+limit+(validateOnly?'&validateOnly=1':'')+((moreStep>0)?'&moreStep='+moreStep:''),
      data: {
        ajax: 1,
        action: 'import',
        tab: 'AdminImport',
        token: token
      }
    });
  }

  /**
   * Show the import progress modal window.
   * @private
   */
  _showProgressModal() {
    this.progressModal.modal('show');
  }

  _hideProgressModal() {
    this.progressModal.modal('hide');
  }

  /**
   * Updates the import progressbar.
   *
   * @param {int} percentage
   * @private
   */
  _updateProgress(percentage) {
    let $progressBar = this.progressBar;

    $progressBar.css('width', percentage + '%');
    $progressBar.find('> span').text(percentage + ' %');
  }

  /**
   * Gets import progress modal.
   *
   * @returns {jQuery}
   */
  get progressModal() {
    return $('#import_progress_modal');
  }

  /**
   * Gets import progress bar.
   *
   * @returns {jQuery}
   */
  get progressBar() {
    return this.progressModal.find('.progress-bar');
  }
}
