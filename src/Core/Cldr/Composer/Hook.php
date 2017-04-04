<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShop\PrestaShop\Core\Cldr\Composer;

use Composer\Script\Event;
use PrestaShop\PrestaShop\Core\Cldr\Update;

/**
 * Class Hook used to download CLDR data during composer install/update
 *
 * @package PrestaShop\PrestaShop\Core\Cldr\Composer
 */
class Hook
{
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
        $root_dir = realpath(__DIR__.'/../../../../');

        $cldr_update = new Update($root_dir.'/translations/');
        $cldr_update->init();

        // If settings file exist
        if (file_exists($root_dir.'/app/config/parameters.php')) {
            //load prestashop config to get locale env
            if (!defined('_PS_ROOT_DIR_')) {
                require_once($root_dir.'/config/config.inc.php');
            }

            //get each defined languages and fetch cldr datas
            $langs = \DbCore::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'lang');

            foreach ($langs as $lang) {
                $language_code = explode('-', $lang['language_code']);
                if (empty($language_code[1])) {
                    $language_code[1] = $language_code[0];
                }
                $cldr_update->fetchLocale($language_code['0'].'-'.strtoupper($language_code[1]));
            }
        }
        if ($event) {
            $event->getIO()->write("Finished...");
        }
    }
}
