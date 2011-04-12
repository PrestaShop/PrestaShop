<?php
/*
* 2007-2011 PrestaShop
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
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/


class HelpAccessCore
{
    const URL = 'http://help.prestashop.com';

    protected static $_images = array(0 => 'none',
                             1 => 'help2.png',
                             2 => 'help-new.png');

    public static function trackClick($label, $version)
    {
        Db::getInstance()->Execute('
        INSERT INTO `'._DB_PREFIX_.'help_access` (`label`, `version`) VALUES (\''.pSQL($label).'\',\''.pSQL($version).'\')
        ON DUPLICATE KEY UPDATE `version` = \''.pSQL($version).'\'
        ');
    }

    public static function getVersion($label)
    {
        return Db::getInstance()->getValue('
        SELECT `version` FROM `'._DB_PREFIX_.'help_access`
        WHERE `label` = \''.pSQL($label).'\'
        ');
    }

    public static function retrieveInfos($label, $iso_lang, $country, $version)
    {
   	    $image = self::$_images[0];
	       $tooltip = '';
   	    $url = HelpAccess::URL.'/documentation/renderIcon?label='.$label.'&iso_lang='.$iso_lang.'&country='.$country.'&version='.$version;

   	    $ctx = stream_context_create(array(
                    'http' => array(
                    'timeout' => 10
                    )
                ));

        $res = @file_get_contents($url, 0, $ctx);

	    $infos = preg_split('/\|/', $res);
	    if (sizeof($infos) > 0)
	    {
            $version = trim($infos[0]);
            if (!empty($version))
            {
        	    $image = self::$_images[1];

                if (sizeof($infos) > 1)
                    $tooltip = trim('|'.$infos[1]);
            }
	    }

        $last_version = HelpAccess::getVersion($label);

        if (!empty($version) && $version != $last_version)
            $image = self::$_images[2];

	    return array('version' => $version, 'image' => $image, 'tooltip' => $tooltip);
	}

    public static function displayHelp($label, $iso_lang, $country, $ps_version)
    {
        $infos = HelpAccess::retrieveInfos($label, $iso_lang, $country, $ps_version);
        if (array_key_exists('image', $infos) && $infos['image'] != 'none')
        {
	        echo '
			        <a class="help-button" href="#" onclick="showHelp(\''.HelpAccess::URL.'\',\''.$label.'\',\''.$iso_lang.'\',\''.$ps_version.'\',\''.$infos['version'].'\',\''.$country.'\');" title="'.Tools::htmlentitiesUTF8($infos['tooltip']).'">
			        <img id="help-'.$label.'" src="../img/admin/'.Tools::htmlentitiesUTF8($infos['image']).'" alt="" class="middle" style="margin-top: -5px"/> '.Tools::displayError('HELP').'
			        </a>

		          ';


		     if (!empty($infos['tooltip']))
    		     echo ' <script type="text/javascript">
			            $(document).ready(function() {
              			      $("a.help-button").cluetip({
				              	splitTitle: "|",
				              	cluetipClass: "help-button",
				                showTitle: false,
				                arrows: true,
				                dropShadow: false,
				                positionBy: "auto"
			                  });
			            });
		              </script>';
		 }
    }
}

