jQuery.fn.extend({
    live: function (event, callback) {
        if (this.selector) {
            jQuery(document).on(event, this.selector, callback);
        }
        console.warn('jQuery.live() is deprecated since Prestashop 1.7.7, it will not work in future versions, please use jQuery.on() instead.');
        return this;
    }
});
