<?php
/*
* 2007-2012 PrestaShop
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision: 6844 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/


class HelpAccessCore
{
    const URL = 'http://help.prestashop.com';

    /**
     * Store in the local database that the user has seen a specific help page
     *
     * @static
     * @param $label
     * @param $version
     */
    public static function trackClick($label, $version)
    {
        Db::getInstance()->execute('
        INSERT INTO `'._DB_PREFIX_.'help_access` (`label`, `version`) VALUES (\''.pSQL($label).'\',\''.pSQL($version).'\')
        ON DUPLICATE KEY UPDATE `version` = \''.pSQL($version).'\'
        ');
    }

    /**
     * Returns the last version seen of a help page seen by the user
     *
     * @static
     * @param $label
     * @return mixed
     */
    public static function getVersion($label)
    {
        return Db::getInstance()->getValue('
        SELECT `version` FROM `'._DB_PREFIX_.'help_access`
        WHERE `label` = \''.pSQL($label).'\'
        ');
    }

    /**
     * Fetch information from the help website in order to know:
     * - if the help page exists
     * - his version
     * - the associated tooltip
     *
     * @static
     * @param $label
     * @param $iso_lang
     * @param $country
     * @param $version
     *
     * @return array
     */
    public static function retrieveInfos($label, $iso_lang, $country, $version)
    {
   	    $url = HelpAccess::URL.'/documentation/renderIcon?label='.$label.'&iso_lang='.$iso_lang.'&country='.$country.'&version='.$version;
        $tooltip = '';

        $ctx = @stream_context_create(array('http' => array('timeout' => 10)));
        $res = @file_get_contents($url, 0, $ctx);

	    $infos = preg_split('/\|/', $res);
	    if (count($infos) > 0)
	    {
            $version = trim($infos[0]);
            if (!empty($version))
            {
                if (count($infos) > 1)
                    $tooltip = trim($infos[1]);
            }
	    }

	    return array('version' => $version, 'tooltip' => $tooltip);
	}
}

