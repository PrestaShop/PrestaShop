<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

use PrestaShop\PrestaShop\Core\Domain\Title\ValueObject\Gender as ValueObjectGender;

/**
 * Class GenderCore.
 *
 * @since 1.5.0
 */
class GenderCore extends ObjectModel
{
    public const TYPE_MALE = ValueObjectGender::TYPE_MALE;
    public const TYPE_FEMALE = ValueObjectGender::TYPE_FEMALE;
    public const TYPE_OTHER = ValueObjectGender::TYPE_OTHER;

    /** @var int|null Object ID */
    public $id;
    public $id_gender;
    /** @var string|array<string> */
    public $name;
    /** @var int */
    public $type;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'gender',
        'primary' => 'id_gender',
        'multilang' => true,
        'fields' => [
            'type' => ['type' => self::TYPE_INT, 'required' => true],

            /* Lang fields */
            'name' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCatalogName', 'required' => true, 'size' => 20],
        ],
    ];

    /**
     * GenderCore constructor.
     *
     * @param int|null $id
     * @param int|null $idLang
     * @param int|null $idShop
     */
    public function __construct($id = null, $idLang = null, $idShop = null)
    {
        parent::__construct($id, $idLang, $idShop);

        $this->image_dir = _PS_GENDERS_DIR_;
    }

    /**
     * Get all Genders.
     *
     * @param int|null $idLang Language ID
     *
     * @return PrestaShopCollection
     */
    public static function getGenders($idLang = null)
    {
        if (null === $idLang) {
            $idLang = Context::getContext()->language->id;
        }

        return new PrestaShopCollection('Gender', $idLang);
    }

    /**
     * Get Gender image.
     *
     * @return string File path
     */
    public function getImage()
    {
        if (!isset($this->id) || empty($this->id) || !file_exists(_PS_GENDERS_DIR_ . $this->id . '.jpg')) {
            return _THEME_GENDERS_DIR_ . 'Unknown.jpg';
        }

        return _THEME_GENDERS_DIR_ . $this->id . '.jpg';
    }
}
