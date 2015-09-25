<?php
/**
 * 2007-2015 PrestaShop
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
 *  @author 	PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2015 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShop\PrestaShop\Adapter\Language;

use PrestaShop\PrestaShop\Adapter\AbstractAdminDataProvider;

/**
 * This class will provide data from DB / ORM about Language
 */
class LanguageDataProvider extends AbstractAdminDataProvider
{
    /**
     * Returns available languages
     *
     * @param bool     $active   Select only active languages
     * @param int|bool $id_shop  Shop ID
     * @param bool     $ids_only If true, returns an array of language IDs
     *
     * @return array Languages
     */
    public function getLanguages($active = true, $id_shop = false, $ids_only = false)
    {
        return \Language::getLanguages($active, $id_shop, $ids_only);
    }
}
