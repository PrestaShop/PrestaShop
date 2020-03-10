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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\EntityTranslation;

/**
 * Translates tabs (menu items) in database using DataLang
 */
class TabTranslator extends EntityTranslator
{
    const TABLE_NAME = _DB_PREFIX_ . 'tab';

    /**
     * @var array[] Sets of wording, wording_domain
     */
    private $sourceIndex = [];

    /**
     * Translate using wordings
     * {@inheritdoc}
     */
    public function translate(int $languageId, int $shopId)
    {
        $this->sourceIndex = $this->buildIndex();
        parent::translate($languageId, $shopId);
    }

    /**
     * {@inheritdoc}
     */
    protected function doTranslate(array $data, string $fieldName): string
    {
        $message = ($this->sourceIndex[$data['id_tab']]) ?? $this->getSourceString($data, $fieldName);

        return $this->dataLang->getFieldValue($fieldName, $message);
    }

    /**
     * Builds an index of source wordings from the entity table
     *
     * @return array[] Array of [wording, wording_domain], indexed by id_tab
     *
     * @throws \PrestaShopDatabaseException
     */
    private function buildIndex(): array
    {
        $tableName = self::TABLE_NAME;

        $sql = "SELECT id_tab, wording, wording_domain FROM $tableName";
        $results = $this->db->executeS($sql);

        $souceIndex = [];
        foreach ($results as $result) {
            $souceIndex[$result['id_tab']] = [
                $result['wording'],
                $result['wording_domain'],
            ];
        }

        return $souceIndex;
    }
}
