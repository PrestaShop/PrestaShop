<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

/**
 * Class RiskCore.
 */
class RiskCore extends ObjectModel
{
    public $id;
    public $id_risk;
    public $name;
    public $color;
    public $percent;

    public static $definition = [
        'table' => 'risk',
        'primary' => 'id_risk',
        'multilang' => true,
        'fields' => [
            'name' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isString', 'required' => true, 'size' => 20],
            'color' => ['type' => self::TYPE_STRING, 'validate' => 'isColor', 'size' => 32],
            'percent' => ['type' => self::TYPE_INT, 'validate' => 'isPercentage'],
        ],
    ];

    /**
     * Get fields.
     *
     * @return mixed
     */
    public function getFields()
    {
        $this->validateFields();
        $fields['id_risk'] = (int) $this->id_risk;
        $fields['color'] = pSQL($this->color);
        $fields['percent'] = (int) $this->percent;

        return $fields;
    }

    /**
     * Get Risks.
     *
     * @param int|null $idLang Language ID
     *
     * @return PrestaShopCollection
     */
    public static function getRisks($idLang = null)
    {
        if (null === $idLang) {
            $idLang = Context::getContext()->language->id;
        }

        $risks = new PrestaShopCollection('Risk', $idLang);

        return $risks;
    }
}
