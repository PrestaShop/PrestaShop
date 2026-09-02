/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
$(function() {
  tinySetup({
    editor_selector: 'autoload_rte',
    setup: function(ed) {
      ed.on('loadContent', function(ed, e) {
        handleCounterTiny(tinymce.activeEditor.id);
      });
      ed.on('change', function(ed, e) {
        tinyMCE.triggerSave();
        handleCounterTiny(tinymce.activeEditor.id);
      });
      ed.on('blur', function(ed) {
        tinyMCE.triggerSave();
      });
    }
  });

  function handleCounterTiny(id) {
    let textarea = $('#' + id);
    let counter = textarea.attr('counter');
    let counter_type = textarea.attr('counter_type');
    const editor = window.tinyMCE.get(id);
    const max = editor.getBody() ? editor.getBody().textContent.length : 0;

    textarea
      .parent()
      .find('span.currentLength')
      .text(max);
    if ('recommended' !== counter_type && max > counter) {
      textarea
        .parent()
        .find('span.maxLength')
        .addClass('text-danger');
    } else {
      textarea
        .parent()
        .find('span.maxLength')
        .removeClass('text-danger');
    }
  }
});
