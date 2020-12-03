/**
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
 */

const $ = window.$;

/**
 * ConfirmModal component
 *
 * @param {String} id
 * @param {String} confirmTitle
 * @param {String} confirmMessage
 * @param {String} closeButtonLabel
 * @param {String} confirmButtonLabel
 * @param {String} confirmButtonClass
 * @param {Boolean} closable
 * @param {Function} confirmCallback
 *
 */
export default function ConfirmModal(params, confirmCallback) {
  // Construct the modal
  const {id, closable} = params;
  this.modal = Modal(params);

  // jQuery modal object
  this.$modal = $(this.modal.container);

  this.show = () => {
    this.$modal.modal();
  };

  this.modal.confirmButton.addEventListener('click', confirmCallback);

  this.$modal.modal({
    backdrop: (closable ? true : 'static'),
    keyboard: closable !== undefined ? closable : true,
    closable: closable !== undefined ? closable : true,
    show: false,
  });

  this.$modal.on('hidden.bs.modal', () => {
    document.querySelector(`#${id}`).remove();
  });

  document.body.appendChild(this.modal.container);
}

/**
 * Modal component to improve lisibility by constructing the modal outside the main function
 *
 * @param {Object} params
 *
 */
function Modal(
  {
    id = 'confirm_modal',
    confirmTitle,
    confirmMessage = '',
    closeButtonLabel = 'Close',
    confirmButtonLabel = 'Accept',
    confirmButtonClass = 'btn-primary',
  }) {
  const modal = {};

  // Main modal element
  modal.container = document.createElement('div');
  modal.container.classList.add('modal', 'fade');
  modal.container.id = id;

  // Modal dialog element
  modal.dialog = document.createElement('div');
  modal.dialog.classList.add('modal-dialog');

  // Modal content element
  modal.content = document.createElement('div');
  modal.content.classList.add('modal-content');

  // Modal header element
  modal.header = document.createElement('div');
  modal.header.classList.add('modal-header');

  // Modal title element
  if (confirmTitle) {
    modal.title = document.createElement('h4');
    modal.title.classList.add('modal-title');
    modal.title.innerHTML = confirmTitle;
  }

  // Modal close button icon
  modal.closeIcon = document.createElement('button');
  modal.closeIcon.classList.add('close');
  modal.closeIcon.setAttribute('type', 'button');
  modal.closeIcon.dataset.dismiss = 'modal';
  modal.closeIcon.innerHTML = '×';

  // Modal body element
  modal.body = document.createElement('div');
  modal.body.classList.add('modal-body', 'text-left', 'font-weight-normal');

  // Modal message element
  modal.message = document.createElement('p');
  modal.message.classList.add('confirm-message');
  modal.message.innerHTML = confirmMessage;

  // Modal footer element
  modal.footer = document.createElement('div');
  modal.footer.classList.add('modal-footer');

  // Modal close button element
  modal.closeButton = document.createElement('button');
  modal.closeButton.setAttribute('type', 'button');
  modal.closeButton.classList.add('btn', 'btn-outline-secondary', 'btn-lg');
  modal.closeButton.dataset.dismiss = 'modal';
  modal.closeButton.innerHTML = closeButtonLabel;

  // Modal close button element
  modal.confirmButton = document.createElement('button');
  modal.confirmButton.setAttribute('type', 'button');
  modal.confirmButton.classList.add('btn', confirmButtonClass, 'btn-lg', 'btn-confirm-submit');
  modal.confirmButton.dataset.dismiss = 'modal';
  modal.confirmButton.innerHTML = confirmButtonLabel;

  // Constructing the modal
  if (confirmTitle) {
    modal.header.append(modal.title, modal.closeIcon);
  } else {
    modal.header.appendChild(modal.closeIcon);
  }

  modal.body.appendChild(modal.message);
  modal.footer.append(modal.closeButton, modal.confirmButton);
  modal.content.append(modal.header, modal.body, modal.footer);
  modal.dialog.appendChild(modal.content);
  modal.container.appendChild(modal.dialog);

  return modal;
}
