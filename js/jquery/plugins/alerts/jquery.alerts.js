// jQuery Alert Dialogs Plugin
//
/* eslint-disable no-underscore-dangle, func-names, no-var, prefer-template, object-shorthand, default-case, no-param-reassign, max-len, no-undef */
//
// Modernized for PrestaShop (2026).
// Based on version 1.1 by Cory S.N. LaViska - A Beautiful Site
// (http://abeautifulsite.net/, 14 May 2009).
//
// Public API is unchanged:
//
//     jAlert( message, [title, callback] )
//     jConfirm( message, [title, callback] )
//     jPrompt( message, [value, title, callback] )
//
// The $.alerts properties (okButton, cancelButton, okButtonClass,
// cancelButtonClass, dialogClass, ...) remain read/writable.
//
// The dialogs are now built with the exact same Bootstrap markup and
// classes as the native back-office ConfirmModal component
// (admin-dev/themes/new-theme/js/components/modal/confirm-modal.ts):
// .modal-dialog / .modal-content / .modal-header / .modal-message /
// .btn-confirm-submit, and they are opened with the Bootstrap modal
// plugin itself, so styling, animations, Escape/Enter handling, backdrop
// and focus management are identical to every other back-office modal.
//
// License:
//
// This plugin is dual-licensed under the GNU General Public License and the MIT
// License and is copyright 2008 A Beautiful Site, LLC.
(function ($) {
    $.alerts = {

        // These properties can be read/written by accessing $.alerts.propertyName from your scripts at any time

        okButton: '&nbsp;OK&nbsp;', // text for the OK button
        cancelButton: '&nbsp;Cancel&nbsp;', // text for the Cancel button
        okButtonClass: 'btn-primary', // class(es) of the OK button (native ConfirmModal default: btn-primary, deletion dialogs use btn-danger)
        cancelButtonClass: 'btn-outline-secondary', // class(es) of the Cancel button
        dialogClass: null, // if specified, this class will be applied to all dialogs

        // Public methods

        alert(message, title, callback) {
            if (title == null) title = 'Alert';
            $.alerts._show(title, message, null, 'alert', (result) => {
                if (callback) callback(result);
            });
        },

        confirm(message, title, callback) {
            if (title == null) title = 'Confirm';
            $.alerts._show(title, message, null, 'confirm', (result) => {
                if (callback) callback(result);
            });
        },

        prompt(message, value, title, callback) {
            if (title == null) title = 'Prompt';
            $.alerts._show(title, message, value, 'prompt', (result) => {
                if (callback) callback(result);
            });
        },

        // Private methods

        _show(title, msg, value, type, callback) {
            // Remove any dialog that is still open.
            $('#popup_container').remove();

            let confirmed = false;

            // Same DOM structure as the native ConfirmModalContainer component.
            // \n is not escaped here because in the callers \n is replaced by <br> (see AdminCartRulesController).
            const $modal = $('<div class="modal fade" id="popup_container"></div>').append(
                $('<div class="modal-dialog"></div>').append(
                    $('<div class="modal-content"></div>').append(
                        $('<div class="modal-header"></div>').append(
                            $('<h4 class="modal-title" id="popup_title"></h4>').text(title),
                            $('<button type="button" class="close" data-dismiss="modal">&times;</button>'),
                        ),
                        $('<div class="modal-body text-left font-weight-normal"></div>').append(
                            $('<p class="modal-message" id="popup_message"></p>').text(msg),
                        ),
                        $('<div class="modal-footer" id="popup_panel"></div>'),
                    ),
                ),
            );

            if ($.alerts.dialogClass) $modal.addClass($.alerts.dialogClass);

            const $footer = $('#popup_panel', $modal);

            // Cancel/Close button (same classes as the native ConfirmModal close button).
            if (type !== 'alert') {
                $footer.append(
                    `<button type="button" class="btn ${$.alerts.cancelButtonClass} btn-lg" id="popup_cancel" data-dismiss="modal">${$.alerts.cancelButton}</button>`,
                );
            }

            // Confirm button (same classes as the native ConfirmModal confirm button).
            $footer.append(
                `<button type="button" class="btn ${$.alerts.okButtonClass} btn-lg btn-confirm-submit" id="popup_ok" data-dismiss="modal">${$.alerts.okButton}</button>`,
            );

            if (type === 'prompt') {
                $('#popup_message', $modal).append(
                    '<input type="text" size="30" id="popup_prompt" class="form-control" />',
                );
                $('#popup_prompt', $modal).val(value);
            }

            $modal.on('click', '#popup_ok', () => {
                confirmed = true;
                if (type === 'prompt') {
                    callback($('#popup_prompt', $modal).val());
                } else {
                    callback(true);
                }
            });

            // Bootstrap fires hidden.bs.modal for dismiss, backdrop click and Escape.
            $modal.on('hidden.bs.modal', () => {
                $modal.remove();
                if (!confirmed) {
                    if (type === 'prompt') {
                        callback(null);
                    } else {
                        callback(false);
                    }
                }
            });

            $('BODY').append($modal);

            // Open it with the Bootstrap modal plugin: backdrop, focus trap,
            // Escape/Enter handling and animations come from the same
            // component as every other back-office dialog.
            $modal.modal({
                backdrop: 'static',
                keyboard: true,
            });
            $modal.modal('show');

            const $focusTarget = type === 'prompt' ? $('#popup_prompt', $modal) : $('#popup_ok', $modal);
            $modal.on('shown.bs.modal', () => {
                $focusTarget.trigger('focus');
                if (type === 'prompt') {
                    $focusTarget.select();
                }
            });
        },

    };

    // Shortcut functions (global API kept unchanged)
    window.jAlert = function (message, title, callback) {
        $.alerts.alert(message, title, callback);
    };

    window.jConfirm = function (message, title, callback) {
        $.alerts.confirm(message, title, callback);
    };

    window.jPrompt = function (message, value, title, callback) {
        $.alerts.prompt(message, value, title, callback);
    };
}(jQuery));
