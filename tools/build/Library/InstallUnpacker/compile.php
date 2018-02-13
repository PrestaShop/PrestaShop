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

$template = file_get_contents(__DIR__.'/index_template.php');

if ($handle = opendir(__DIR__.'/content')) {
  while (false !== ($entry = readdir($handle))) {
    $filePath = __DIR__.'/content/'.$entry;
    if (is_file($filePath)) {
      echo "File found: $entry\n";
      if (strpos($template, $entry)) {
        echo "\033[0;32mReplace entry: $entry\033[0m\n";
        $template = str_replace(
          "'$entry'",
          '<<<EOF'.PHP_EOL.base64_encode(file_get_contents($filePath)).PHP_EOL.'EOF'.PHP_EOL,
          $template
        );
      } else {
        echo "\033[0;31mFile not present on the template\033[0m\n";
      }
    }
  }
}

file_put_contents(getcwd().'/index.php', $template);
