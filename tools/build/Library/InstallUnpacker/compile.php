<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

/**
 * Validate input.
 *
 * Die if input is not valid
 *
 * @param array $argv user input
 */
function validateInput($argv)
{
    if (count($argv) !== 2) {
        echo 'php compile.php <PS_VERSION>' . PHP_EOL;
        die(1);
    }
}

validateInput($argv);

$psVersion = $argv[1];

$template = file_get_contents(__DIR__ . '/index_template.php');

// compute inline assets
if ($handle = opendir(__DIR__ . '/content')) {
    while (false !== ($entry = readdir($handle))) {
        $filePath = __DIR__ . '/content/' . $entry;

        if (is_file($filePath)) {
            echo "File found: $entry\n";

            if (strpos($template, $entry)) {
                echo "\033[0;32mReplace entry: $entry\033[0m\n";

                $content = base64_encode(file_get_contents($filePath));
                $template = str_replace(
                    "getFileContent('$entry', true)",
                    "getFileContent('$content', false)",
                    $template
                );
            } else {
                echo "\033[0;31mFile not present on the template\033[0m\n";
            }
        }
    }
}

// insert Prestashop version
$template = str_replace('%ps-version-placeholder%', $psVersion, $template);

// compute inline php classes
// @todo: remove duplicate license headers
$inlineContent = '';
if ($handle = opendir(__DIR__ . '/classes')) {
    while (false !== ($entry = readdir($handle))) {
        $filePath = __DIR__ . '/classes/' . $entry;

        if (is_file($filePath)) {
            echo "PHP File found: $entry\n";

            echo "\033[0;32mInsert inline class: $entry\033[0m\n";

            $content = file_get_contents($filePath);
            $contentWithoutPHPTag = str_replace('<?php', '', $content);
            $inlineContent .= PHP_EOL . PHP_EOL . $contentWithoutPHPTag;
        }
    }
}

$placeholder = '/** COMPUTED INLINE CLASSES **/';
$template = str_replace($placeholder, $inlineContent, $template);

file_put_contents(getcwd() . '/index.php', $template);
