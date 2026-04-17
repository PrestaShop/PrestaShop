<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
