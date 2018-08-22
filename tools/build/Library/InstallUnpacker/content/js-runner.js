function extractFiles(startId) {

  if (typeof startId === 'undefined') {
    startId = 0;
  }

  var request = $.ajax({
    method: "POST",
    url: "<?= $selfUri ?>",
    data: {
      extract: true,
      startId: startId,
    }
  });

  request.done(function (msg) {
    try {
      msg = JSON.parse(msg);
    } catch (e) {
      if (String(msg).match("<tittle>PrestaShop")) {
        msg = "Invalid server response";
      }
      msg = {
        message: msg
      };
    }

    if (
        msg.error
        || typeof msg.lastId === 'undefined'
        || typeof msg.numFiles === 'undefined'
    ) {
      $('#error').html('An error has occured: <br />' + msg.message);
      $('#spinner').remove();
    } else {
      if (msg.lastId > msg.numFiles) {
        // end
        window.location.href = 'install/';
      } else {
        $("#progressContainer")
            .find(".current")
            .width((msg.lastId / msg.numFiles * 100) + '%');

        $("#progressContainer")
            .find(".progressNumber")
            .css({left: Math.round((msg.lastId / msg.numFiles * 100)) + '%'})
            .html(Math.round((msg.lastId / msg.numFiles * 100)) + '%');

        extractFiles(msg.lastId);
      }
    }
  });

  request.fail(function (jqXHR, textStatus, errorThrown) {
    $('#error').html('An error has occurred' + textStatus);
    $('#spinner').remove();
  });
}

$(function () {
  extractFiles();
});
