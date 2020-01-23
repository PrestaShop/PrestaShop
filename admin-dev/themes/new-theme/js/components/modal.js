/**
 * 2007-2019 PrestaShop SA and Contributors
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
 */

const $ = window.$;

/**
 * ConfirmModal component
 *
 * @param {String} id
 * @param {String} title
 * @param {String} message
 * @param {String} closeButtonText
 * @param {String} confirmButtonText
 * @param {String} confirmButtonClass
 * @param {Boolean} closable
 * @param {Function} action
 *
 */
export default function ConfirmModal({id = 'confirm_modal', title = '', message = '', closeButtonText = 'Close', confirmButtonText = 'Accept', confirmButtonClass = 'btn-primary', closable = true}, action) {
  // Main modal element
  this.modal = document.createElement('div');
  this.modal.classList.add('modal', 'fade');
  this.modal.id = id;

  // Modal dialog element
  this.modalDialog = document.createElement('div');
  this.modalDialog.classList.add('modal-dialog');

  // Modal content element
  this.modalContent = document.createElement('div');
  this.modalContent.classList.add('modal-content');

  // Modal header element
  this.modalHeader = document.createElement('div');
  this.modalHeader.classList.add('modal-header');

  // Modal title element
  this.modalTitle = document.createElement('h4');
  this.modalTitle.classList.add('modal-title');
  this.modalTitle.innerHTML = title;

  // Modal close button icon
  this.modalCloseIcon = document.createElement('button');
  this.modalCloseIcon.classList.add('close');
  this.modalCloseIcon.setAttribute('type', 'button');
  this.modalCloseIcon.dataset.dismiss = 'modal';
  this.modalCloseIcon.innerHTML = '×';

  // Modal body element
  this.modalBody = document.createElement('div');
  this.modalBody.classList.add('modal-body', 'text-left', 'font-weight-normal');

  // Modal message element
  this.modalMessage = document.createElement('p');
  this.modalMessage.classList.add('confirm-message');
  this.modalMessage.innerHTML = message;

  // Modal footer element
  this.modalFooter = document.createElement('div');
  this.modalFooter.classList.add('modal-footer');

  // Modal close button element
  this.modalCloseButton = document.createElement('button');
  this.modalCloseButton.setAttribute('type', 'button');
  this.modalCloseButton.classList.add('btn', 'btn-outline-secondary', 'btn-lg');
  this.modalCloseButton.dataset.dismiss = 'modal';
  this.modalCloseButton.innerHTML = closeButtonText;

  // Modal close button element
  this.modalConfirmButton = document.createElement('button');
  this.modalConfirmButton.setAttribute('type', 'button');
  this.modalConfirmButton.classList.add('btn', confirmButtonClass, 'btn-lg', 'btn-confirm-submit');
  this.modalConfirmButton.dataset.dismiss = 'modal';
  this.modalConfirmButton.innerHTML = confirmButtonText;

  // Constructing the modal
  this.modalHeader.append(this.modalTitle, this.modalCloseIcon);
  this.modalBody.appendChild(this.modalMessage);
  this.modalFooter.append(this.modalCloseButton, this.modalConfirmButton);
  this.modalContent.append(this.modalHeader, this.modalBody, this.modalFooter);
  this.modalDialog.appendChild(this.modalContent);
  this.modal.appendChild(this.modalDialog);

  // jQuery modal object
  this.$modal = $(this.modal);

  this.toggle = () => {
    this.$modal.modal();
  };

  this.modalConfirmButton.addEventListener('click', action);

  this.$modal.modal({
    backdrop: (closable ? true : 'static'),
    keyboard: closable,
    closable,
    show: false,
  });

  this.$modal.on('hidden.bs.modal', () => {
    document.querySelector(`#${id}`).remove();
  });

  document.body.appendChild(this.modal);
}
