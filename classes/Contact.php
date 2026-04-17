<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

use PrestaShopBundle\Form\Admin\Type\FormattedTextareaType;

/**
 * Class ContactCore.
 */
class ContactCore extends ObjectModel
{
    public $id;

    /** @var string|array<int, string> Name */
    public $name;

    /** @var string E-mail */
    public $email;

    /** @var string|array<int, string> Detailed description */
    public $description;

    /** @var bool */
    public $customer_service;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'contact',
        'primary' => 'id_contact',
        'multilang' => true,
        'fields' => [
            'email' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isEmail',
                'size' => 255,
            ],
            'customer_service' => [
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool',
            ],

            /* Lang fields */
            'name' => [
                'type' => self::TYPE_STRING,
                'lang' => true,
                'validate' => 'isGenericName',
                'required' => true,
                'size' => 255,
            ],
            'description' => [
                'type' => self::TYPE_STRING,
                'lang' => true,
                'validate' => 'isString',
                'size' => FormattedTextareaType::LIMIT_MEDIUMTEXT_UTF8_MB4,
            ],
        ],
    ];

    /**
     * Return available contacts.
     *
     * @param int $idLang Language ID
     *
     * @return array Contacts
     */
    public static function getContacts($idLang)
    {
        $shopIds = Shop::getContextListShopID();
        $sql = 'SELECT *
                FROM `' . _DB_PREFIX_ . 'contact` c
                ' . Shop::addSqlAssociation('contact', 'c', false) . '
                LEFT JOIN `' . _DB_PREFIX_ . 'contact_lang` cl ON (c.`id_contact` = cl.`id_contact`)
                WHERE cl.`id_lang` = ' . (int) $idLang . '
                AND contact_shop.`id_shop` IN (' . implode(', ', array_map('intval', $shopIds)) . ')
                GROUP BY c.`id_contact`
                ORDER BY `name` ASC';

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }

    /**
     * Return available categories contacts.
     *
     * @return array Contacts
     */
    public static function getCategoriesContacts()
    {
        $shopIds = Shop::getContextListShopID();

        return Db::getInstance()->executeS('
            SELECT cl.*
            FROM ' . _DB_PREFIX_ . 'contact ct
            ' . Shop::addSqlAssociation('contact', 'ct', false) . '
            LEFT JOIN ' . _DB_PREFIX_ . 'contact_lang cl
                ON (cl.id_contact = ct.id_contact AND cl.id_lang = ' . (int) Context::getContext()->language->id . ')
            WHERE ct.customer_service = 1
            AND contact_shop.`id_shop` IN (' . implode(', ', array_map('intval', $shopIds)) . ')
            GROUP BY ct.`id_contact`
        ');
    }
}
