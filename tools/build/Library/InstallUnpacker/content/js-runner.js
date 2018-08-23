/**
 * Extract PrestaShop project from zip archive.
 * Provides visual feedback through the use of the progress bar
 *
 * @param int startId
 */
function extractFiles(startId) {
  if (typeof startId === 'undefined') {
    startId = 0;
  }

  var request = $.ajax({
    method: "POST",
    url: $('#content-install-in-progress').data('extractUrl'),
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
      $('#error-install-in-progress').html('An error has occured: <br />' + msg.message);
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
    $('#error-install-in-progress').html('An error has occurred' + textStatus);
    $('#spinner').remove();
  });
}

/**
 * Check whether there is a more recent version
 * If yes, display a form to ask the user whether he wants to download it.
 * If no, resume standard install (zip extraction process)
 */
function checkWhetherThereIsAMoreRecentVersion() {
  var request = $.ajax({
    method: "GET",
    url: $('#content-install-in-progress').data('checkVersionUrl'),
    timeout: 20000
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

    if (msg.error) {
      fallbackToExtraction();
    } else {
      if (msg.thereIsAMoreRecentPSVersionAndItCanBeInstalled == true) {
        showFormToDownloadLatestPSVersion();
      } else {
        fallbackToExtraction();
      }
    }
  });

  request.fail(function (jqXHR, textStatus, errorThrown) {
    fallbackToExtraction();
  });
}

function showFormToDownloadLatestPSVersion() {
  $('#content-install-in-progress').hide();
  $('#content-install-form').show();
}

function skipFormToDownloadLatestPSVersion() {
  $('#content-install-form').hide();
  $('#content-install-in-progress').show();
  fallbackToExtraction();
}

function fallbackToExtraction() {
  $('#initializationMessage').hide();
  $('#versionPanel').show();
  extractFiles();
}

function setupSkipButtonBehavior() {
  $('#skip-button').on('click', function (event) {
    skipFormToDownloadLatestPSVersion();
  });
}

function setupDownloadLatestVersionButtonBehavior() {

  $("#latest-button").on("click", function (event) {

    $('#latest-button').addClass('inactive-link');
    $('#waiting').html('Downloading latest version ...');

    var request = $.ajax({
      url: $('#content-install-in-progress').data('downloadLatestUrl'),
      method: "POST",
      data: {'downloadLatest': true}
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

      if (msg.success == true) {
        location.reload();
      }

      if (msg.warning == true) {
        var issuesList = computeIssuesList(msg.issues);
        errorMessage = issuesList;
      } else {
        errorMessage = msg.message;
      }

      displayErrorWhileDownloadingLatestVersion(errorMessage);
    });

    request.fail(function (jqXHR, textStatus, errorThrown) {
      displayErrorWhileDownloadingLatestVersion('We are sorry, an error has occurred ' + textStatus);
    });
  });
}

/**
 * Render issues as an HTML list
 *
 * @param array issues
 *
 * @returns string
 */
function computeIssuesList(issues) {
  var issuesList = '<ul>';

  $.each(issues, function (key, issue) {
    issuesList = issuesList + '<li>' + issue + '</li>';
  });

  var issuesList = issuesList + '</ul>';

  return issuesList;
}

/**
 * @param string errorMessage
 */
function displayErrorWhileDownloadingLatestVersion(errorMessage) {
  $('#error-install-form').html('An error has occured: <br />' + errorMessage);
  $('#waiting').remove();
  $('#fallback-after-error').show();
}

$(document).ready(function () {
  setupSkipButtonBehavior();
  setupDownloadLatestVersionButtonBehavior();

  checkWhetherThereIsAMoreRecentVersion();
});
