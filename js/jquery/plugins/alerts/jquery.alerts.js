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
// The $.alerts properties (okButton, cancelButton, dialogClass, ...) remain
// read/writable. Dialog markup keeps the historical #popup_* ids so custom
// back-office CSS targeting them keeps working.
//
// License:
//
// This plugin is dual-licensed under the GNU General Public License and the MIT
// License and is copyright 2008 A Beautiful Site, LLC.
(function ($) {
    $.alerts = {

        // These properties can be read/written by accessing $.alerts.propertyName from your scripts at any time

        verticalOffset: -75, // vertical offset of the dialog from center screen, in pixels
        horizontalOffset: 0, // horizontal offset of the dialog from center screen, in pixels/
        repositionOnResize: true, // re-centers the dialog on window resize
        overlayOpacity: 0.45, // transparency level of overlay
        overlayColor: '#0f172a', // base color of overlay
        draggable: false, // make the dialogs draggable (requires UI Draggables plugin)
        okButton: '&nbsp;OK&nbsp;', // text for the OK button
        cancelButton: '&nbsp;Cancel&nbsp;', // text for the Cancel button
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
            const previouslyFocused = document.activeElement;

            $.alerts._hide();
            $.alerts._overlay('show');

            $('BODY').append(
                '<div id="popup_container" role="dialog" aria-modal="true" aria-labelledby="popup_title">'
                + '<h1 id="popup_title"></h1>'
                + '<div id="popup_content">'
                  + '<div id="popup_message"></div>'
                + '</div>'
              + '</div>');

            if ($.alerts.dialogClass) $('#popup_container').addClass($.alerts.dialogClass);

            $('#popup_title').text(title);
            $('#popup_content').addClass(type);
            // \n is not between double quotes here because in the callers \n is replaced by <br> (see AdminCartRulesController).
            $('#popup_message').text(msg);
            $('#popup_message').html($('#popup_message').text().replace(/\n/g, '<br />'));

            $.alerts._reposition();
            $.alerts._maintainPosition(true);

            let buttonsHtml = '<div id="popup_panel">'
                + `<button type="button" class="jsa-btn jsa-btn-primary" id="popup_ok">${$.alerts.okButton}</button>`;
            if (type !== 'alert') {
                buttonsHtml += ` <button type="button" class="jsa-btn jsa-btn-secondary" id="popup_cancel">${$.alerts.cancelButton}</button>`;
            }
            buttonsHtml += '</div>';

            switch (type) {
                case 'alert':
                    $('#popup_message').after(buttonsHtml);
                    $('#popup_ok').on('click', () => {
                        $.alerts._hide(previouslyFocused);
                        callback(true);
                    });
                    $('#popup_ok').trigger('focus');
                    break;
                case 'confirm':
                    $('#popup_message').after(buttonsHtml);
                    $('#popup_ok').on('click', () => {
                        $.alerts._hide(previouslyFocused);
                        if (callback) callback(true);
                    });
                    $('#popup_cancel').on('click', () => {
                        $.alerts._hide(previouslyFocused);
                        if (callback) callback(false);
                    });
                    $('#popup_ok').trigger('focus');
                    break;
                case 'prompt':
                    $('#popup_message').append('<input type="text" size="30" id="popup_prompt" class="jsa-prompt-input" />').after(buttonsHtml);
                    $('#popup_ok').on('click', () => {
                        const val = $('#popup_prompt').val();
                        $.alerts._hide(previouslyFocused);
                        if (callback) callback(val);
                    });
                    $('#popup_cancel').on('click', () => {
                        $.alerts._hide(previouslyFocused);
                        if (callback) callback(null);
                    });
                    if (value) $('#popup_prompt').val(value);
                    $('#popup_prompt').trigger('focus').select();
                    break;
            }

            // Keyboard: Enter confirms, Escape cancels, Tab stays inside the dialog.
            $('#popup_container').on('keydown', (e) => {
                if (e.key === 'Enter') {
                    $('#popup_ok').trigger('click');
                } else if (e.key === 'Escape') {
                    $('#popup_cancel').trigger('click');
                } else if (e.key === 'Tab') {
                    const focusable = $('#popup_container').find('button, input').filter(':visible');
                    if (!focusable.length) {
                        return;
                    }
                    const first = focusable.first()[0];
                    const last = focusable.last()[0];
                    if (e.shiftKey && document.activeElement === first) {
                        e.preventDefault();
                        last.focus();
                    } else if (!e.shiftKey && document.activeElement === last) {
                        e.preventDefault();
                        first.focus();
                    }
                }
            });

            // Make draggable
            if ($.alerts.draggable) {
                try {
                    $('#popup_container').draggable({handle: $('#popup_title')});
                    $('#popup_title').css({cursor: 'move'});
                } catch (e) { /* requires jQuery UI draggables */ }
            }
        },

        _hide(previouslyFocused) {
            $('#popup_container').remove();
            $.alerts._overlay('hide');
            $.alerts._maintainPosition(false);
            if (previouslyFocused && $.contains(document.documentElement, previouslyFocused)) {
                previouslyFocused.focus();
            }
        },

        _overlay(status) {
            switch (status) {
                case 'show':
                    $.alerts._overlay('hide');
                    $('BODY').append('<div id="popup_overlay"></div>');
                    $('#popup_overlay').css({
                        opacity: $.alerts.overlayOpacity,
                    });
                    break;
                case 'hide':
                    $('#popup_overlay').remove();
                    break;
            }
        },

        _reposition() {
            const top = (($('#popup_container').outerHeight() / 2) * -1) + $.alerts.verticalOffset;
            const left = (($('#popup_container').outerWidth() / 2) * -1) + $.alerts.horizontalOffset;

            $('#popup_container').css({
                top: '50%',
                left: '50%',
                marginTop: `${top}px`,
                marginLeft: `${left}px`,
            });
        },

        _maintainPosition(status) {
            if ($.alerts.repositionOnResize) {
                switch (status) {
                    case true:
                        $(window).on('resize', $.alerts._reposition);
                        break;
                    case false:
                        $(window).off('resize', $.alerts._reposition);
                        break;
                }
            }
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
