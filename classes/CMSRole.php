<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
     */
    public static function getRepositoryClassName()
    {
        return '\\PrestaShop\\PrestaShop\\Core\\CMS\\CMSRoleRepository';
    }
}
