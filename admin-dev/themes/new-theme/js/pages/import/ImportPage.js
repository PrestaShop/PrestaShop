/**
 * 2007-2018 PrestaShop
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

import FormFieldToggle from "./FormFieldToggle";

const $ = window.$;

export default class ImportPage {
  init() {
    new FormFieldToggle().init();

    $('.js-from-files-history-btn').on('click', this.showFilesHistoryHandler.bind(this));
    $('.js-close-files-history-block-btn').on('click', this.closeFilesHistoryHandler.bind(this));
    $('#fileHistoryTable').on('click', '.js-use-file-btn', this.useFileFromFilesHistory.bind(this));
    $('.js-change-import-file-btn').on('click', this.changeImportFileHandler.bind(this));
    $('.js-import-file').on('change', this.uploadFile.bind(this));
  }

  changeImportFileHandler() {
    this.hideImportFileAlert();
    this.showFileUploadBlock();
  }

  /**
   * Show files history event handler
   */
  showFilesHistoryHandler() {
    this.showFilesHistory();
    this.hideFileUploadBlock();
  }

  /**
   * Close files history event handler
   */
  closeFilesHistoryHandler() {
    this.closeFilesHistory();
    this.showFileUploadBlock();
  }

  /**
   * Show files history block
   */
  showFilesHistory() {
    $('.js-files-history-block').removeClass('d-none');
  }

  /**
   * Hide files history block
   */
  closeFilesHistory() {
    $('.js-files-history-block').addClass('d-none');
  }

  /**
   *  Prefill hidden file input with selected file name from history
   */
  useFileFromFilesHistory(event) {
    let filename = $(event.target).closest('.btn-group').data('file');

    $('.js-import-file-input').val(filename);

    this.showImportFileAlert(filename);
    this.closeFilesHistory();
  }

  /**
   * Show alert with imported file name
   */
  showImportFileAlert(filename) {
    $('.js-import-file-alert').removeClass('d-none');
    $('.js-import-file').text(filename);
  }

  /**
   * Hides selected import file alert
   */
  hideImportFileAlert() {
    $('.js-import-file-alert').addClass('d-none');
  }

  /**
   * Hides import file upload block
   */
  hideFileUploadBlock() {
    $('.js-file-upload-form-group').addClass('d-none');
  }

  /**
   * Hides import file upload block
   */
  showFileUploadBlock() {
    $('.js-file-upload-form-group').removeClass('d-none');
  }

  enableFilesHistoryBtn() {
    $('.js-from-files-history-btn').removeAttr('disabled');
  }

  disableFilesHistoryBtn() {
    $('.js-from-files-history-btn').attr('disabled', 'disabled');
  }

  /**
   * Upload selected import file
   */
  uploadFile() {
    const uplodedFile = $('#file').prop('files')[0];

    const data = new FormData(uplodedFile);
    data.append('file', uplodedFile);

    const url = $('.js-import-form').data('file-upload-url');

    //@todo: add progress bar when uploading

    $.ajax({
      type: 'POST',
      url: url,
      data: data,
      cache: false,
      contentType: false,
      processData: false,
    }).then(response => {
      let filename = response.file.name;

      $('.js-import-file-input').val(filename);

      this.showImportFileAlert(filename);
      this.hideFileUploadBlock();
      this.addFileToHistoryTable(filename);
      this.enableFilesHistoryBtn();
    }).catch(error => {
      //@todo: display error to admin?
      console.log(error);
    });
  }

  /**
   * Renders new row in files history table
   *
   * @param {string} filename
   */
  addFileToHistoryTable(filename) {
    const $table = $('#fileHistoryTable');

    let $template = $table.find('tr:first').clone();
    $template.removeClass('d-none');
    $template.find('td:first').text(filename);
    $template.find('.btn-group').attr('data-file', filename);

    $table.find('tbody').append($template);

    let filesNumber = $table.find('tr').length - 1;
    $('.js-files-history-number').text(filesNumber);
  }
}
