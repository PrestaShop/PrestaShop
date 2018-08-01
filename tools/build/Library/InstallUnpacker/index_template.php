<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

set_time_limit(0);

define('_PS_INSTALL_MINIMUM_PHP_VERSION_ID_', 50600);
define('_PS_INSTALL_MINIMUM_PHP_VERSION_', '5.6');
define('_PS_VERSION_', '%ps-version-placeholder%');

define('ZIP_NAME', 'prestashop.zip');
define('TARGET_FOLDER', __DIR__ . '/');
define('BATCH_SIZE', 500);

// bust cache, or else it won't load the installer after the extraction is done
header("Cache-Control: no-cache, no-store, must-revalidate");

if (!defined('PHP_VERSION_ID') || PHP_VERSION_ID < _PS_INSTALL_MINIMUM_PHP_VERSION_ID_) {
    die('You need at least PHP ' . _PS_INSTALL_MINIMUM_PHP_VERSION_ . ' to install PrestaShop. Your current PHP version is ' . PHP_VERSION);
}

// --------------------------------------------------------------------------------
/** COMPUTED INLINE CLASSES **/

// --------------------------------------------------------------------------------

$installManager = new InstallManager();

function isThisTheLatestAvailableVersion()
{
    $latestVersionAvailable = getLatestStableAvailableVersion();

    return (_PS_VERSION_ === $latestVersionAvailable);
}

function getLatestStableAvailableVersion()
{
    global $installManager;
    return $installManager->getLatestStableAvailableVersion();
}

function getFileContent($fileOrContent, $debug)
{
    if ($debug) {
        return file_get_contents('content/' . $fileOrContent);
    }
    return base64_decode($fileOrContent);
}

function getZipErrorMessage($errorCode)
{
    $errors = [
        ZipArchive::ER_EXISTS => "File already exists.",
        ZipArchive::ER_INCONS => "Zip archive inconsistent or corrupted. Double check your uploaded files.",
        ZipArchive::ER_INVAL => "Invalid argument.",
        ZipArchive::ER_MEMORY => "Allocation error. Out of memory?",
        ZipArchive::ER_NOENT => "Unable to find the release zip file. Make sure that the prestashop.zip file has been uploaded and is located in the same directory as this dezipper.",
        ZipArchive::ER_NOZIP => "The release file is not a zip file or it is corrupted. Double check your uploaded files.",
        ZipArchive::ER_OPEN => "Can't open file. Make sure PHP has read access to the prestashop.zip file.",
        ZipArchive::ER_READ => "Read error.",
        ZipArchive::ER_SEEK => "Seek error.",
    ];

    if (isset($errors[$errorCode])) {
        return "Unzipping error - " . $errors[$errorCode];
    }

    return "An unknown error was found while reading the zip file";
}

$selfUri = basename(__FILE__);

$userHasChosenToDownloadLatestPSVersion = ((isset($_POST['downloadLatest'])) && ($_POST['downloadLatest'] === 'true'));
if ($userHasChosenToDownloadLatestPSVersion) {
    $issues = $installManager->testDownloadCapabilities();

    if (empty($issues)) {
        try {
            $installManager->downloadUnzipAndReplaceLatestPSVersion();
            die(json_encode([
                'success' => true,
            ]));
        } catch (\Exception $e) {
            die(json_encode([
                'error' => true,
                'message' => $e->getMessage(),
            ]));
        }
    } else {
        die(json_encode([
            'warning' => true,
            'issues' => $issues,
        ]));
    }
}

$startId = (isset($_POST['startId'])) ? (int)$_POST['startId'] : 0;

if (isset($_POST['extract'])) {
    if (!extension_loaded('zip')) {
        die(json_encode([
            'error' => true,
            'message' => 'You must install PHP zip extension first',
        ]));
    }

    $zip = new ZipArchive();
    if (true !== $error = $zip->open(__DIR__ . '/' . ZIP_NAME)) {
        die(json_encode([
            'error' => true,
            'message' => getZipErrorMessage($error),
        ]));
    }

    if (!is_writable(TARGET_FOLDER)) {
        die(json_encode([
            'error' => true,
            'message' => "You need to grant write permissions for PHP on the following directory: "
                . realpath(TARGET_FOLDER),
        ]));
    }

    $numFiles = $zip->numFiles;
    $lastId = $startId + BATCH_SIZE;

    $fileList = array();
    for ($id = $startId; $id < min($numFiles, $lastId); $id++) {
        $currentFile = $zip->getNameIndex($id);
        if (in_array($currentFile, ['/index.php', 'index.php'])) {
            $indexContent = $zip->getFromIndex($id);
            if (!file_put_contents(getcwd() . '/index.php.temp', $indexContent)) {
                die(json_encode([
                    'error' => true,
                    'message' => "Unable to write to file " . getcwd() . '/index.php.temp'
                ]));
            }
        } else {
            $fileList[] = $currentFile;
        }
    }

    foreach ($fileList as $currentFile) {
        if ($zip->extractTo(TARGET_FOLDER, $currentFile) === false) {
            die(json_encode([
                'error' => true,
                'message' => 'Extraction error - ' . $zip->getStatusString(),
                'file' => $currentFile,
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
        unlink(getcwd() . '/index.php');
        unlink(getcwd() . '/prestashop.zip');
        rename(getcwd() . '/index.php.temp', getcwd() . '/index.php');
    }

    die(json_encode([
        'error' => false,
        'numFiles' => $numFiles,
        'lastId' => $lastId,
    ]));
}


if (isset($_GET['element'])) {
    switch ($_GET['element']) {
        case 'font':
            header('Content-Type: application/font-sfnt');
            echo getFileContent('OpenSans-Regular.ttf', true);
            break;
        case 'css':
            header('Content-Type: text/css');
            echo getFileContent('style.css', true);
            break;
        case 'jquery':
            header('Content-Type: text/javascript');
            echo getFileContent('jquery-2.2.3.min.js', true);
            break;
        case 'gif':
            header('Content-Type: image/gif');
            echo getFileContent('installer.gif', true);
            break;
    }
    exit;
}

$thereIsAMoreRecentPSVersion = false;
$userHasChosenToIgnoreLatestPSVersion = ((isset($_POST['submit'])) && ($_POST['submit'] === 'Skip Latest'));
if (false === $userHasChosenToIgnoreLatestPSVersion) {
    try {
        if (false === isThisTheLatestAvailableVersion()) {
            $latestPrestaShopAvailableVersion = getLatestStableAvailableVersion();
            $thereIsAMoreRecentPSVersion = true;
        }
    } catch (\Exception $e) {
        // do nothing, this is an optional feature
    }
}

$showFormToDownloadLatestPSVersion = ($thereIsAMoreRecentPSVersion && !$userHasChosenToIgnoreLatestPSVersion);

if ($showFormToDownloadLatestPSVersion):
    ?>
    <div id="download-latest-version-form">

        This is not the latest version of Prestashop. Do you want to get the latest ?

        <form method="post">
            <input id="latest" type="button" name="submit" value="Download Latest">
            <input id="skip" type="submit" name="submit" value="Skip Latest">
            <div id="waiting"></div>
            <div id="error"></div>
            <div id="fallback-after-error" style="display:none;">Cannot download latest Prestashop version. Please click
                on 'Skip latest' to resume standard installation.
            </div>
            <script type="text/javascript" src="<?= $selfUri ?>?element=jquery"></script>
            <script type="text/javascript">
                $(document).ready(function () {

                    $("#latest").on("click", function (event) {

                        $.ajax({
                            url: "<?= $selfUri ?>",
                            method: "POST",
                            data: {'downloadLatest': true},
                            beforeSend: function () {
                                $('#latest').attr('disabled', 'disabled');
                                $('#waiting').html('Downloading latest version ...');
                            },
                            success: function (msg) {
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
                                    $('#error').html('An error has occured: <br />' + msg.message);
                                    $('#waiting').remove();
                                    $('#fallback-after-error').show();
                                } else {
                                    if (msg.success == true) {
                                        location.reload();
                                    } else {
                                        if (msg.warning == true) {
                                            var issuesList = 'An error has occured: <br /><ul>';

                                            $.each(msg.issues, function (key, issue) {
                                                issuesList = issuesList + '<li>' + issue + '</li>';
                                            });

                                            var issuesList = issuesList + '</ul>';

                                            $('#error').html(issuesList);
                                            $('#waiting').remove();
                                            $('#fallback-after-error').show();
                                        } else {
                                            $('#error').html('An error has occured: <br />' + msg.message);
                                            $('#waiting').remove();
                                            $('#fallback-after-error').show();
                                        }
                                    }
                                }
                            },
                            fail: function (jqXHR, textStatus, errorThrown) {
                                $('#error').html('We are sorry, an error has occurred' + textStatus);
                                $('#waiting').remove();
                            }
                        });
                    });
                });
            </script>
        </form>
    </div>
<?php else: ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>PrestaShop installation</title>
        <link rel="stylesheet" type="text/css" href="<?= $selfUri ?>?element=css">
    </head>
    <body>
    <div id="content">
        <div>
            <img id="spinner" src="<?= $selfUri ?>?element=gif"/>
            <div id="progressContainer">
                <div class="progressNumber">0 %</div>
                <div class="progress">
                    <div class="current">
                    </div>
                </div>
            </div>
            <div id="error">
            </div>
        </div>
    </div>
    <script type="text/javascript" src="<?= $selfUri ?>?element=jquery"></script>
    <script type="text/javascript">

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
    </script>
    </body>
    </html>
<?php endif; ?>
