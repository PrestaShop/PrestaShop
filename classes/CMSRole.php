<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

/**
 * Class CMSRoleCore.
 */
class CMSRoleCore extends ObjectModel
{
    /** @var string name */
    public $name;
    /** @var int id_cms */
    public $id_cms;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'cms_role',
        'primary' => 'id_cms_role',
        'fields' => [
            'name' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 50],
            'id_cms' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
        ],
    ];

    /**
     * @return string
     *
     * @since 1.7.0
     */
    public static function getRepositoryClassName()
    {
        return '\\PrestaShop\\PrestaShop\\Core\\CMS\\CMSRoleRepository';
    }
}
