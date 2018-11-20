<?php
/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

/**
 * Class GenderCore
 *
 * @since 1.5.0
 */
class GenderCore extends ObjectModel
{
    public $id;
    public $id_gender;
    public $name;
    public $type;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'gender',
        'primary' => 'id_gender',
        'multilang' => true,
        'fields' => array(
            'type' => array('type' => self::TYPE_INT, 'required' => true),

            /* Lang fields */
            'name' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isString', 'required' => true, 'size' => 20),
        ),
    );

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
     * Get all Genders
     *
     * @param int|null $idLang Language ID
     *
     * @return PrestaShopCollection
     */
    public static function getGenders($idLang = null)
    {
        if (is_null($idLang)) {
            $idLang = Context::getContext()->language->id;
        }

        $genders = new PrestaShopCollection('Gender', $idLang);

        return $genders;
    }

    /**
     * Get Gender image
     *
     * @return string File path
     */
    public function getImage()
    {
        if (!isset($this->id) || empty($this->id) || !file_exists(_PS_GENDERS_DIR_.$this->id.'.jpg')) {
            return _THEME_GENDERS_DIR_.'Unknown.jpg';
        }

        return _THEME_GENDERS_DIR_.$this->id.'.jpg';
    }
}
