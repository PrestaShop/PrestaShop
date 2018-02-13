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
namespace PrestaShop\PrestaShop\Core\Cldr\Composer;

use Composer\Script\Event;

/**
 * Class Hook used to download CLDR data during composer install/update
 *
 * @package PrestaShop\PrestaShop\Core\Cldr\Composer
 */
class Hook
{
    /** @var string */
    const ZIP_CORE_URL = 'https://i18n.prestashop.com/cldr/core.zip';

    /**
     * Triggers CLDR download
     *
     * @param Event $event
     * @throws \Exception
     * @throws \PrestaShopDatabaseException
     */
    public static function init(Event $event = null)
    {
        if ($event) {
            $event->getIO()->write("Init CLDR data download...");
        }
        $root_dir = realpath(__DIR__ . '/../../../../');
        $cldrFolder = "$root_dir/translations/cldr";
        $coreFilePath = "$cldrFolder/core.zip";
        $zipUrl = self::ZIP_CORE_URL;

        if (!file_exists($coreFilePath)) {
            $fp = fopen($coreFilePath, "w");
            $ch = curl_init($zipUrl);
            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_exec($ch);
            $error = curl_error($ch);
            curl_close($ch);
            fclose($fp);

            if (!empty($error)) {
                throw new \Exception("Failed to download '$zipUrl', error: '$error'.");
            };
        }

        if ($event) {
            $event->getIO()->write("Finished...");
        }
    }
}
