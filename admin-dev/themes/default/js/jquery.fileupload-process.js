/*
 * jQuery File Upload Processing Plugin 1.2.2
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2012, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

/* jslint nomen: true, unparam: true */
/* global define, window */

(function (factory) {
  if (typeof define === 'function' && define.amd) {
    // Register as an anonymous AMD module:
    define([
      'jquery',
      './jquery.fileupload',
    ], factory);
  } else {
    // Browser globals:
    factory(
      window.jQuery,
    );
  }
}(($) => {
  const originalAdd = $.blueimp.fileupload.prototype.options.add;

  // The File Upload Processing plugin extends the fileupload widget
  // with file processing functionality:
  $.widget('blueimp.fileupload', $.blueimp.fileupload, {

    options: {
      // The list of processing actions:
      processQueue: [
        /*
                {
                    action: 'log',
                    type: 'debug'
                }
                */
      ],
      add(e, data) {
        const $this = $(this);
        data.process(() => $this.fileupload('process', data));
        originalAdd.call(this, e, data);
      },
    },

    processActions: {
      /*
            log: function (data, options) {
                console[options.type](
                    'Processing "' + data.files[data.index].name + '"'
                );
            }
            */
    },

    _processFile(data) {
      const that = this;
      const dfd = $.Deferred().resolveWith(that, [data]);
      let chain = dfd.promise();
      this._trigger('process', null, data);
      $.each(data.processQueue, (i, settings) => {
        const func = function (data) {
          return that.processActions[settings.action].call(
            that,
            data,
            settings,
          );
        };
        chain = chain.pipe(func, settings.always && func);
      });
      chain
        .done(() => {
          that._trigger('processdone', null, data);
          that._trigger('processalways', null, data);
        })
        .fail(() => {
          that._trigger('processfail', null, data);
          that._trigger('processalways', null, data);
        });
      return chain;
    },

    // Replaces the settings of each processQueue item that
    // are strings starting with an "@", using the remaining
    // substring as key for the option map,
    // e.g. "@autoUpload" is replaced with options.autoUpload:
    _transformProcessQueue(options) {
      const processQueue = [];
      $.each(options.processQueue, function () {
        const settings = {};
        const {action} = this;
        const prefix = this.prefix === true ? action : this.prefix;
        $.each(this, (key, value) => {
          if ($.type(value) === 'string'
                            && value.charAt(0) === '@') {
            settings[key] = options[
              value.slice(1) || (prefix ? prefix
                                + key.charAt(0).toUpperCase() + key.slice(1) : key)
            ];
          } else {
            settings[key] = value;
          }
        });
        processQueue.push(settings);
      });
      options.processQueue = processQueue;
    },

    // Returns the number of files currently in the processsing queue:
    processing() {
      return this._processing;
    },

    // Processes the files given as files property of the data parameter,
    // returns a Promise object that allows to bind callbacks:
    process(data) {
      const that = this;
      const options = $.extend({}, this.options, data);

      if (options.processQueue && options.processQueue.length) {
        this._transformProcessQueue(options);
        if (this._processing === 0) {
          this._trigger('processstart');
        }
        $.each(data.files, (index) => {
          const opts = index ? $.extend({}, options) : options;
          const func = function () {
            return that._processFile(opts);
          };
          opts.index = index;
          that._processing += 1;
          that._processingQueue = that._processingQueue.pipe(func, func)
            .always(() => {
              that._processing -= 1;
              if (that._processing === 0) {
                that._trigger('processstop');
              }
            });
        });
      }
      return this._processingQueue;
    },

    _create() {
      this._super();
      this._processing = 0;
      this._processingQueue = $.Deferred().resolveWith(this)
        .promise();
    },

  });
}));
