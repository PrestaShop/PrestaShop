/**
 * Tooltip initiation
 */
$(function () {
    $('[data-toggle="tooltip"]').tooltip();
});

/**
 *  BO Events Handler
 */
var BOEvent = {

    on: function(eventName, callback, context) {

        document.addEventListener(eventName, function(event) {
            if (typeof context !== 'undefined') {
                callback.call(context, event);
            } else {
                callback(event);
            }
        });
    },

    emitEvent: function(eventName, eventType) {
        var _event = document.createEvent(eventType);
        // true values stand for: can bubble, and is cancellable
        _event.initEvent(eventName, true, true);
        document.dispatchEvent(_event);
    }
};
