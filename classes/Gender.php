<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

use PrestaShop\PrestaShop\Core\Domain\Title\ValueObject\Gender as ValueObjectGender;

/**
 * Class GenderCore.
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
