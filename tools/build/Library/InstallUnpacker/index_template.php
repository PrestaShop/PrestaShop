<?php
set_time_limit(0);

define('ZIP_NAME', 'prestashop.zip');
define('TARGET_FOLDER', __DIR__.'/');
define('BATCH_SIZE', 500);

$startId = 0;
if (isset($_POST['startId'])) {
  $startId = (int)$_POST['startId'];
}

if (isset($_POST['extract'])) {
  if (!extension_loaded('zip')) {
    die(json_encode([
      'error' => true,
      'message' => 'You must install PHP zip extension first',
    ]));
  }

  $zip = new ZipArchive();
  if ($zip->open(__DIR__.'/'.ZIP_NAME) === true) {

    $numFiles = $zip->numFiles;
    $lastId = $startId + BATCH_SIZE;

    $fileList = array();
    for ($id = $startId; $id < min($numFiles, $lastId); $id++) {
      $currentFile = $zip->getNameIndex($id);
      if (in_array($currentFile, ['/index.php', 'index.php'])) {
        $indexContent = $zip->getFromIndex($id);
        file_put_contents(getcwd().'/index.php.temp', $indexContent);
      } else {
        $fileList[] = $currentFile;
      }
    }

    foreach ($fileList as $currentFile) {
      if ($zip->extractTo(TARGET_FOLDER, $currentFile) === false) {
        die(json_encode([
          'error' => true,
          'message' => 'An error occured during the extraction',
          'file' => $currentFile,
          'status' => $zip->getStatusString(),
          'numFiles' => $numFiles,
          'lastId' => $lastId,
          'files' => $fileList,
        ]));
      }
    }

    @chmod('install/index.php', 0644);
    @chmod('admin/index.php', 0644);
    @chmod('admin/ajax.php', 0644);
    @chmod('admin/ajax-tab.php', 0644);
    @chmod('index.php', 0644);

    $zip->close();

    if ($lastId >= $numFiles) {
      unlink(getcwd().'/index.php');
      unlink(getcwd().'/prestashop.zip');
      rename(getcwd().'/index.php.temp', getcwd().'/index.php');
      sleep(2); // we need to wait the rename creation as the ajax call is asynchronous
    }

    die(json_encode([
      'error' => false,
      'numFiles' => $numFiles,
      'lastId' => $lastId,
    ]));
  }
}

if (isset($_GET['element'])) {
  switch ($_GET['element']) {
    case 'font':
      header('Content-Type: application/font-sfnt');
      echo base64_decode('OpenSans-Regular.ttf');
      break;
    case 'css':
      header('Content-Type: text/css');
      echo base64_decode('style.css');
      break;
    case 'jquery':
      header('Content-Type: text/javascript');
      echo base64_decode('jquery-2.2.3.min.js');
      break;
    case 'gif':
      header('Content-Type: image/gif');
      echo base64_decode('installer.gif');
    break;
  }
  exit;
}

?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>PrestaShop installation</title>
  <link rel="stylesheet" type="text/css" href="index.php?element=css">
</head>
<body>
  <div id="content">
    <div>
      <img src="index.php?element=gif" />
      <div id="progressContainer">
        <div class="progressNumber">0 %</div>
        <div class="progress">
          <div class="current">
          </div>
        </div>
      </div>
      <div id="error"></div>
    </div>
  </div>
  <script type="text/javascript" src="index.php?element=jquery"></script>
  <script type="text/javascript">

    function extractFiles(startId) {

      if (typeof startId == 'undefined') {
        startId = 0;
      }

      var request = $.ajax({
        method: "POST",
        url: "index.php",
        data: {
          extract: true,
          startId: startId,
        }
      });

      request.done(function(msg) {
        try {
          msg = JSON.parse(msg);
        }catch(e){
          msg = {
            message: msg
          };
        }

        if (
          msg.fail
          || typeof msg.lastId == 'undefined'
          || typeof msg.numFiles == 'undefined'
        ) {
          $('#error').html('An error has occured : <br />'+ msg.message);
          $('.spinner').remove();
        } else {
          if (msg.lastId > msg.numFiles) {
            location.reload();
          } else {
            $("#progressContainer")
              .find(".current")
              .width((msg.lastId / msg.numFiles * 100)+'%');

            $("#progressContainer")
              .find(".progressNumber")
              .css({left: Math.round((msg.lastId / msg.numFiles * 100))+'%'})
              .html(Math.round((msg.lastId / msg.numFiles * 100))+'%');

            extractFiles(msg.lastId);
          }
        }
      });

      request.fail(function(jqXHR, textStatus, errorThrown) {
        $('#error').html('An error has occured' + textStatus);
        $('.spinner').remove();
      });
    }

    $(function() {
      extractFiles();
    });
  </script>
</body>
</html>
