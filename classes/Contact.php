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

    /** @var int Position in the contact list */
    public $position = 0;

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
            'position' => [
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt',
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
                ORDER BY c.`position` ASC, cl.`name` ASC';

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
            ORDER BY ct.`position` ASC, cl.`name` ASC
        ');
    }

    /**
     * {@inheritdoc}
     *
     * A newly created contact is appended at the end of the list.
     */
    public function add($autoDate = true, $nullValues = false)
    {
        if ($this->position <= 0) {
            $this->position = Contact::getHighestPosition() + 1;
        }

        return parent::add($autoDate, $nullValues);
    }

    /**
     * {@inheritdoc}
     *
     * Positions are compacted afterwards so the list keeps a gapless order.
     */
    public function delete()
    {
        return parent::delete() && Contact::cleanPositions();
    }

    /**
     * Gets the highest contact position, or -1 when there is no contact yet.
     */
    public static function getHighestPosition(): int
    {
        $position = Db::getInstance()->getValue('SELECT MAX(`position`) FROM `' . _DB_PREFIX_ . 'contact`');

        return is_numeric($position) ? (int) $position : -1;
    }

    /**
     * Reorders contact positions consecutively, starting at 0.
     * Called after deleting a contact.
     */
    public static function cleanPositions(): bool
    {
        $contacts = Db::getInstance()->executeS(
            'SELECT `id_contact`
             FROM `' . _DB_PREFIX_ . 'contact`
             ORDER BY `position` ASC, `id_contact` ASC'
        );

        if ($contacts === false) {
            return false;
        }

        $result = true;
        $position = 0;

        foreach ($contacts as $contact) {
            $result = Db::getInstance()->execute(
                'UPDATE `' . _DB_PREFIX_ . 'contact`
                 SET `position` = ' . (int) $position++ . '
                 WHERE `id_contact` = ' . (int) $contact['id_contact']
            ) && $result;
        }

        return $result;
    }
}
