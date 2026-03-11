<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Feature\Repository;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Feature;
use PrestaShop\PrestaShop\Adapter\Feature\Validate\FeatureValidator;
use PrestaShop\PrestaShop\Core\Domain\Feature\Exception\CannotAddFeatureException;
use PrestaShop\PrestaShop\Core\Domain\Feature\Exception\CannotDeleteFeatureException;
use PrestaShop\PrestaShop\Core\Domain\Feature\Exception\CannotEditFeatureException;
use PrestaShop\PrestaShop\Core\Domain\Feature\Exception\FeatureNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Feature\ValueObject\FeatureId;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopGroupId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\Repository\AbstractMultiShopObjectModelRepository;

/**
 * Methods to access data storage for FeatureValue
 */
class FeatureRepository extends AbstractMultiShopObjectModelRepository
{
    public function __construct(
        protected readonly Connection $connection,
        protected readonly string $dbPrefix,
        protected readonly FeatureValidator $featureValidator
    ) {
    }

    public function get(FeatureId $featureId): Feature
    {
        /** @var Feature $feature */
        $feature = $this->getObjectModel(
            $featureId->getValue(),
            Feature::class,
            FeatureNotFoundException::class
        );

        return $feature;
    }

    /**
     * @param array<int, string> $localizedNames
     * @param ShopId[] $associatedShopIds
     *
     * @return Feature
     */
    public function create(
        array $localizedNames,
        array $associatedShopIds
    ): Feature {
        $feature = new Feature();
        $feature->name = $localizedNames;

        $this->featureValidator->validate($feature);
        $this->addObjectModelToShops($feature, $associatedShopIds, CannotAddFeatureException::class);

        return $feature;
    }

    /**
     * @param Feature $feature
     *
     * @return void
     *
     * @throws CoreException
     */
    public function update(Feature $feature): void
    {
        $this->featureValidator->validate($feature);
        $this->updateObjectModel($feature, CannotEditFeatureException::class);
    }

    public function delete(FeatureId $featureId): void
    {
        $this->deleteObjectModel($this->get($featureId), CannotDeleteFeatureException::class);
    }

    /**
     * @param FeatureId $featureId
     *
     * @throws FeatureNotFoundException
     * @throws CoreException
     */
    public function assertExists(FeatureId $featureId): void
    {
        $this->assertObjectModelExists(
            (int) $featureId->getValue(),
            'feature',
            FeatureNotFoundException::class
        );
    }

    /**
     * @param int $langId
     * @param int $shopId
     *
     * @return array<int, array<string, mixed>>
     */
    public function getFeaturesByLang(int $langId, int $shopId): array
    {
        $qb = $this->getFeaturesQueryBuilder()
            ->leftJoin('f', $this->dbPrefix . 'feature_lang', 'fl', 'fl.id_feature = f.id_feature AND fl.id_lang = :languageId')
            ->innerJoin('f', $this->dbPrefix . 'feature_shop', 'fs', 'fs.id_feature = f.id_feature AND fs.id_shop = :shopId')
            ->setParameter('languageId', $langId)
            ->setParameter('shopId', $shopId)
            ->select('f.*, fl.*')
            ->addOrderBy('fl.name', 'ASC')
        ;

        return $this->formatResult($qb->executeQuery()->fetchAllAssociative());
    }

    /**
     * @param FeatureId $featureId
     * @param LanguageId $languageId
     *
     * @return string
     *
     * @throws FeatureNotFoundException
     */
    public function getFeatureName(FeatureId $featureId, LanguageId $languageId): string
    {
        $featureIdValue = $featureId->getValue();
        $result = $this->connection->createQueryBuilder()
            ->select('fl.name')
            ->from($this->dbPrefix . 'feature', 'f')
            ->innerJoin(
                'f',
                $this->dbPrefix . 'feature_lang',
                'fl',
                'f.id_feature = fl.id_feature'
            )
            ->andWhere('f.id_feature = :featureId')
            ->andWhere('fl.id_lang = :languageId')
            ->setParameters([
                'featureId' => $featureIdValue,
                'languageId' => $languageId->getValue(),
            ])
            ->executeQuery()
            ->fetchAssociative()
        ;

        if (!isset($result['name'])) {
            throw new FeatureNotFoundException(sprintf('Feature with id "%d" name was not found', $featureIdValue));
        }

        return $result['name'];
    }

    /**
     * @param int|null $limit
     * @param int|null $offset
     * @param array|null $filters
     *
     * @return array<int, array<string, mixed>>
     */
    public function getFeatures(?int $limit = null, ?int $offset = null, ?array $filters = []): array
    {
        $qb = $this->getFeaturesQueryBuilder()
            ->select('f.*, fl.*')
            ->leftJoin('f', $this->dbPrefix . 'feature_lang', 'fl', 'fl.id_feature = f.id_feature')
            ->setFirstResult($offset ?? 0)
            ->addOrderBy('f.position', 'ASC')
            ->setMaxResults($limit)
        ;

        return $this->formatResult($qb->executeQuery()->fetchAllAssociative());
    }

    /**
     * @param array|null $filters
     *
     * @return int
     */
    public function getFeaturesCount(?array $filters = []): int
    {
        $qb = $this->getFeaturesQueryBuilder()
            ->select('COUNT(f.id_feature_value) AS total_feature_values')
            ->addGroupBy('f.id_feature_value')
        ;

        return (int) $qb->executeQuery()->fetch()['total_feature_values'];
    }

    private function formatResult(array $results): array
    {
        $localizedNames = [];
        $featuresById = [];
        foreach ($results as $result) {
            $featureId = (int) $result['id_feature'];
            $localizedNames[$featureId][(int) $result['id_lang']] = $result['name'];
            $featuresById[$featureId] = [
                'id_feature' => $featureId,
                'position' => (int) $result['position'],
            ];
        }

        $features = [];
        foreach ($featuresById as $featureById) {
            $features[] = [
                'id_feature' => $featureById['id_feature'],
                'position' => $featureById['position'],
                'localized_names' => $localizedNames[$featureById['id_feature']],
            ];
        }

        return $features;
    }

    /**
     * @param ShopConstraint $shopConstraint
     *
     * @return ShopId[]
     */
    public function getShopIdsByConstraint(ShopConstraint $shopConstraint): array
    {
        if ($shopConstraint->getShopGroupId()) {
            return $this->getAssociatedShopIdsFromGroup($shopConstraint->getShopGroupId());
        }

        if ($shopConstraint->forAllShops()) {
            return array_map(static function (array $result): ShopId {
                return new ShopId((int) $result['id_shop']);
            }, $this->connection->createQueryBuilder()
                ->select('id_shop')
                ->from($this->dbPrefix . 'feature_shop', 'fs')
                ->executeQuery()
                ->fetchAllAssociative()
            );
        }

        return [$shopConstraint->getShopId()];
    }

    /**
     * @param ShopGroupId $shopGroupId
     *
     * @return ShopId[]
     */
    public function getAssociatedShopIdsFromGroup(ShopGroupId $shopGroupId): array
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('fs.id_shop')
            ->from($this->dbPrefix . 'feature_shop', 'fs')
            ->innerJoin(
                'fs',
                $this->dbPrefix . 'shop',
                's',
                's.id_shop = fs.id_shop'
            )
            ->andWhere('s.id_shop_group = :shopGroupId')
            ->setParameter('shopGroupId', $shopGroupId->getValue())
            ->groupBy('id_shop')
        ;

        return array_map(static function (array $result): ShopId {
            return new ShopId((int) $result['id_shop']);
        }, $qb->executeQuery()->fetchAllAssociative());
    }

    /**
     * Retrieve features with values (id, name).
     *
     * @param int $languageId
     * @param int[] $shopIds
     *
     * @return array<int, array{feature_id: int, name: string, values: array<int, array{item_id: int, name: string}>}>
     */
    public function getFeaturesWithValues(int $languageId, array $shopIds = []): array
    {
        $qb = $this->getFeaturesQueryBuilder()
            ->select(
                'DISTINCT f.id_feature AS feature_id',
                'fl.name AS name',
                'fvl.id_feature_value AS value_id',
                'fvl.value AS value_name'
            )
            ->leftJoin('f', $this->dbPrefix . 'feature_lang', 'fl',
                'f.id_feature = fl.id_feature AND fl.id_lang = :language_id'
            )
            ->leftJoin('f', $this->dbPrefix . 'feature_value', 'fv',
                'f.id_feature = fv.id_feature'
            )
            ->leftJoin('fv', $this->dbPrefix . 'feature_value_lang', 'fvl',
                'fvl.id_lang = :language_id AND fvl.id_feature_value = fv.id_feature_value'
            )
            ->where('fv.custom = 0')
            ->orderBy('f.id_feature')
            ->addOrderBy('fvl.id_feature_value')
            ->setParameter('language_id', $languageId);

        if (!empty($shopIds)) {
            $qb
                ->innerJoin('f', $this->dbPrefix . 'feature_shop', 'fs',
                    'fs.id_feature = f.id_feature AND fs.id_shop IN (:shop_ids)'
                )
                ->setParameter('shop_ids', $shopIds, ArrayParameterType::INTEGER);
        }

        $rows = $qb->executeQuery()->fetchAllAssociative();

        $features = [];
        foreach ($rows as $row) {
            $featureId = (int) $row['feature_id'];
            if (!isset($features[$featureId])) {
                $features[$featureId] = [
                    'feature_id' => (int) $featureId,
                    'name' => (string) $row['name'],
                    'values' => [],
                ];
            }
            $features[$featureId]['values'][] = [
                'item_id' => (int) $row['value_id'],
                'name' => (string) $row['value_name'],
            ];
        }

        return array_values($features);
    }

    /**
     * @return QueryBuilder
     */
    private function getFeaturesQueryBuilder(): QueryBuilder
    {
        // Filters not handled yet
        $qb = $this->connection->createQueryBuilder();
        $qb->from($this->dbPrefix . 'feature', 'f');

        return $qb;
    }
}
