<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\EntityTranslation;

use PrestaShopDatabaseException;

/**
 * Translates tabs (menu items) in database using DataLang
 */
class TabTranslator extends EntityTranslator
{
    /**
     * @var array[] Sets of wording, wording_domain
     */
    private $sourceIndex = [];

    /**
     * Translate using wordings
     * {@inheritdoc}
     */
    public function translate(int $languageId, int $shopId): void
    {
        $this->sourceIndex = $this->buildIndex();
        parent::translate($languageId, $shopId);
    }

    /**
     * {@inheritdoc}
     */
    protected function doTranslate(array $data, string $fieldName): string
    {
        $message = $this->sourceIndex[$data['id_tab']] ?? $this->getSourceString($data, $fieldName);

        return $this->dataLang->getFieldValue($fieldName, $message);
    }

    /**
     * Builds an index of source wordings from the entity table
     *
     * @return array[] Array of [wording, wording_domain], indexed by id_tab
     *
     * @throws PrestaShopDatabaseException
     */
    private function buildIndex(): array
    {
        $tableName = $this->dbPrefix . 'tab';

        $sql = "SELECT id_tab, wording, wording_domain FROM $tableName WHERE wording > '' and wording_domain > ''";
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
