<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Combination\Content\Repository;

use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;

/**
 * Localized values overriding the product ones for a combination, stored per shop
 */
class CombinationContentRepository
{
    public const FIELDS = ['description', 'description_short', 'link_rewrite', 'meta_description', 'meta_title'];

    public function __construct(
        private readonly Connection $connection,
        private readonly string $dbPrefix,
    ) {
    }

    /**
     * @return array<string, array<int, string>> values indexed by field, then by language id
     */
    public function getLocalizedValues(CombinationId $combinationId, ShopId $shopId): array
    {
        $rows = $this->connection->createQueryBuilder()
            ->select('pac.id_lang, pac.' . implode(', pac.', self::FIELDS))
            ->from($this->dbPrefix . 'product_attribute_content', 'pac')
            ->where('pac.id_product_attribute = :combinationId')
            ->andWhere('pac.id_shop = :shopId')
            ->setParameter('combinationId', $combinationId->getValue())
            ->setParameter('shopId', $shopId->getValue())
            ->executeQuery()
            ->fetchAllAssociative()
        ;

        $localizedValues = array_fill_keys(self::FIELDS, []);
        foreach ($rows as $row) {
            foreach (self::FIELDS as $field) {
                $localizedValues[$field][(int) $row['id_lang']] = (string) $row[$field];
            }
        }

        return $localizedValues;
    }

    /**
     * @return array<int, string> non empty link rewrites indexed by language id
     */
    public function getLinkRewrites(CombinationId $combinationId, ShopId $shopId): array
    {
        return $this->connection->createQueryBuilder()
            ->select('pac.id_lang, pac.link_rewrite')
            ->from($this->dbPrefix . 'product_attribute_content', 'pac')
            ->where('pac.id_product_attribute = :combinationId')
            ->andWhere('pac.id_shop = :shopId')
            ->andWhere("pac.link_rewrite <> ''")
            ->setParameter('combinationId', $combinationId->getValue())
            ->setParameter('shopId', $shopId->getValue())
            ->executeQuery()
            ->fetchAllKeyValue()
        ;
    }

    /**
     * @param ShopId[] $shopIds
     * @param array<string, array<int, string>> $localizedValues values indexed by field, then by language id
     */
    public function update(CombinationId $combinationId, array $shopIds, array $localizedValues): void
    {
        $valuesByLanguage = [];
        foreach ($localizedValues as $field => $values) {
            foreach ($values as $langId => $value) {
                $valuesByLanguage[$langId][$field] = $value;
            }
        }

        foreach ($shopIds as $shopId) {
            $existingLanguageIds = $this->getLanguageIds($combinationId, $shopId);
            foreach ($valuesByLanguage as $langId => $values) {
                $key = [
                    'id_product_attribute' => $combinationId->getValue(),
                    'id_shop' => $shopId->getValue(),
                    'id_lang' => $langId,
                ];
                if (in_array($langId, $existingLanguageIds, true)) {
                    $this->connection->update($this->dbPrefix . 'product_attribute_content', $values, $key);
                } else {
                    $this->connection->insert($this->dbPrefix . 'product_attribute_content', $key + $values);
                }
            }
        }
    }

    /**
     * @return int[]
     */
    private function getLanguageIds(CombinationId $combinationId, ShopId $shopId): array
    {
        return array_map('intval', $this->connection->createQueryBuilder()
            ->select('pac.id_lang')
            ->from($this->dbPrefix . 'product_attribute_content', 'pac')
            ->where('pac.id_product_attribute = :combinationId')
            ->andWhere('pac.id_shop = :shopId')
            ->setParameter('combinationId', $combinationId->getValue())
            ->setParameter('shopId', $shopId->getValue())
            ->executeQuery()
            ->fetchFirstColumn()
        );
    }
}
