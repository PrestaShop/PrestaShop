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

/**
 * Class RiskCore.
 *
 * @since 1.5.0
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
