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

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Feature\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use FeatureValue;
use PrestaShop\PrestaShop\Adapter\Feature\Validate\FeatureValueValidator;
use PrestaShop\PrestaShop\Core\Domain\Feature\Exception\CannotAddFeatureValueException;
use PrestaShop\PrestaShop\Core\Domain\Feature\Exception\CannotDeleteFeatureValueException;
use PrestaShop\PrestaShop\Core\Domain\Feature\Exception\CannotUpdateFeatureValueException;
use PrestaShop\PrestaShop\Core\Domain\Feature\Exception\FeatureValueNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Feature\Exception\InvalidFeatureValueIdException;
use PrestaShop\PrestaShop\Core\Domain\Feature\ValueObject\FeatureValueId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\Repository\AbstractObjectModelRepository;

/**
 * Methods to access data storage for FeatureValue
 */
class FeatureValueRepository extends AbstractObjectModelRepository
{
    /**
     * @var FeatureValueValidator
     */
    private $featureValueValidator;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var string
     */
    private $dbPrefix;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     * @param FeatureValueValidator $featureValueValidator
     */
    public function __construct(
        Connection $connection,
        string $dbPrefix,
        FeatureValueValidator $featureValueValidator
    ) {
        $this->connection = $connection;
        $this->dbPrefix = $dbPrefix;
        $this->featureValueValidator = $featureValueValidator;
    }

    /**
     * @param FeatureValue $featureValue
     * @param int $errorCode
     *
     * @return FeatureValueId
     *
     * @throws CannotAddFeatureValueException
     * @throws InvalidFeatureValueIdException
     * @throws CoreException
     */
    public function add(FeatureValue $featureValue, int $errorCode = 0): FeatureValueId
    {
        $this->featureValueValidator->validate($featureValue);
        $id = $this->addObjectModel($featureValue, CannotAddFeatureValueException::class, $errorCode);

        return new FeatureValueId($id);
    }

    /**
     * @param FeatureValue $featureValue
     *
     * @throws CannotUpdateFeatureValueException
     * @throws CoreException
     */
    public function update(FeatureValue $featureValue): void
    {
        $this->featureValueValidator->validate($featureValue);
        $this->updateObjectModel(
            $featureValue,
            CannotUpdateFeatureValueException::class
        );
    }

    /**
     * @param FeatureValueId $featureValueId
     *
     * @return FeatureValue
     *
     * @throws FeatureValueNotFoundException
     */
    public function get(FeatureValueId $featureValueId): FeatureValue
    {
        /** @var FeatureValue $featureValue */
        $featureValue = $this->getObjectModel(
            $featureValueId->getValue(),
            FeatureValue::class,
            FeatureValueNotFoundException::class
        );

        return $featureValue;
    }

    /**
     * @param FeatureValueId $featureValueId
     *
     * @throws FeatureValueNotFoundException
     * @throws CoreException
     */
    public function assertExists(FeatureValueId $featureValueId): void
    {
        $this->assertObjectModelExists(
            $featureValueId->getValue(),
            'feature_value',
            FeatureValueNotFoundException::class
        );
    }

    /**
     * @param ProductId $productId
     * @param int|null $limit
     * @param int|null $offset
     * @param array|null $filters
     *
     * @return array<int, array<string, mixed>>
     */
    public function getProductFeatureValues(ProductId $productId, ?int $limit = null, ?int $offset = null, ?array $filters = []): array
    {
        return $this->getFeatureValues($limit, $offset, array_merge($filters ?? [], ['id_product' => $productId->getValue()]));
    }

    public function getAllProductFeatureValues(ProductId $productId): array
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->from($this->dbPrefix . 'feature_value', 'fv')
            ->innerJoin('fv', $this->dbPrefix . 'feature_product', 'fp', 'fp.id_feature_value = fv.id_feature_value AND fp.id_product = :productId')
            ->leftJoin('fv', $this->dbPrefix . 'feature_value_lang', 'fvl', 'fvl.id_feature_value = fv.id_feature_value')
            ->select('fv.*, fvl.*')
            ->setParameter('productId', $productId->getValue())
        ;

        $result = $qb->execute()->fetchAllAssociative();
        $featureValues = [];
        foreach ($result as $featureValue) {
            $featureValueId = (int) $featureValue['id_feature_value'];
            if (!isset($featureValues[$featureValueId])) {
                $featureValues[$featureValueId] = [
                    'id_feature_value' => $featureValueId,
                    'id_feature' => (int) $featureValue['id_feature'],
                    'custom' => (int) $featureValue['custom'],
                ];
            }
            $featureValues[$featureValueId]['localized_values'][(int) $featureValue['id_lang']] = $featureValue['value'];
        }

        return array_values($featureValues);
    }

    /**
     * @param int $langId
     * @param array $filters
     *
     * @return array
     */
    public function getFeatureValuesByLang(int $langId, array $filters): array
    {
        $qb = $this->getFeatureValuesQueryBuilder(array_merge($filters, ['id_lang' => $langId]))
            ->leftJoin('f', $this->dbPrefix . 'feature_value_lang', 'fvl', 'fvl.id_feature_value = fv.id_feature_value AND fvl.id_lang = :langId')
            ->setParameter('langId', $langId)
            ->select('fv.*, fvl.value')
            // Override the default order by feature position and ID
            ->orderBy('fvl.value')
        ;

        return $qb->execute()->fetchAllAssociative();
    }

    /**
     * @param int|null $limit
     * @param int|null $offset
     * @param array|null $filters
     *
     * @return array
     */
    public function getFeatureValues(?int $limit = null, ?int $offset = null, ?array $filters = []): array
    {
        $qb = $this->getFeatureValuesQueryBuilder($filters)
            ->select('fv.*')
            ->setFirstResult($offset ?? 0)
            ->setMaxResults($limit)
        ;

        $featureValues = $qb->executeQuery()->fetchAllAssociative();

        $indexedFeatureValues = [];
        foreach ($featureValues as $featureValue) {
            $indexedFeatureValues[$featureValue['id_feature_value']] = $featureValue;
        }
        $featureValueIds = array_keys($indexedFeatureValues);

        $localizedFeatureValues = $this->getFeatureValueLocalizedValues($featureValueIds, $filters);
        foreach ($localizedFeatureValues as $localizedFeatureValue) {
            $indexedFeatureValues[$localizedFeatureValue['id_feature_value']]['localized_values'][$localizedFeatureValue['id_lang']] = $localizedFeatureValue['value'];
        }

        return array_values($indexedFeatureValues);
    }

    /**
     * @param ProductId $productId
     * @param array|null $filters
     *
     * @return int
     */
    public function getProductFeatureValuesCount(ProductId $productId, ?array $filters = []): int
    {
        return $this->getFeatureValuesCount(array_merge($filters, ['id_product' => $productId->getValue()]));
    }

    /**
     * @param array|null $filters
     *
     * @return int
     */
    public function getFeatureValuesCount(?array $filters = []): int
    {
        $qb = $this->getFeatureValuesQueryBuilder($filters)
            ->select('COUNT(fv.id_feature_value) AS total_feature_values')
        ;

        return (int) $qb->executeQuery()->fetchAssociative()['total_feature_values'];
    }

    public function delete(FeatureValueId $featureValueId): void
    {
        $this->deleteObjectModel($this->get($featureValueId), CannotDeleteFeatureValueException::class);
    }

    /**
     * @param array $featureValuesIds
     * @param array $filters
     *
     * @return array
     */
    private function getFeatureValueLocalizedValues(array $featureValuesIds, array $filters): array
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->from($this->dbPrefix . 'feature_value_lang', 'fvl')
            ->select('fvl.*')
            ->where('fvl.id_feature_value IN(:featureValueIds)')
            ->setParameter('featureValueIds', $featureValuesIds, Connection::PARAM_INT_ARRAY)
        ;

        if (!empty($filters['id_lang'])) {
            $languageIds = is_array($filters['id_lang']) ? $filters['id_lang'] : [$filters['id_lang']];
            $qb
                ->andWhere('fvl.id_lang IN (:languageIds)')
                ->setParameter('languageIds', $languageIds, Connection::PARAM_INT_ARRAY)
            ;
        }

        return $qb->executeQuery()->fetchAllAssociative();
    }

    /**
     * @param array|null $filters
     *
     * @return QueryBuilder
     */
    private function getFeatureValuesQueryBuilder(?array $filters): QueryBuilder
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->from($this->dbPrefix . 'feature_value', 'fv')
        ;

        // Join only on specified feature if requested
        if (!empty($filters['id_feature'])) {
            $qb
                ->innerJoin('fv', $this->dbPrefix . 'feature', 'f', 'f.id_feature = fv.id_feature AND f.id_feature = :featureId')
                ->setParameter('featureId', (int) $filters['id_feature'])
            ;
        } else {
            $qb->leftJoin('fv', $this->dbPrefix . 'feature', 'f', 'f.id_feature = fv.id_feature');
        }

        // Join only on specified product if requested
        if (!empty($filters['id_product'])) {
            $qb
                ->innerJoin('fv', $this->dbPrefix . 'feature_product', 'fp', 'fp.id_feature_value = fv.id_feature_value AND fp.id_product = :productId')
                ->setParameter('productId', (int) $filters['id_product'])
            ;
        }

        $qb
            ->addGroupBy('fv.id_feature_value')
            ->orderBy('f.position, fv.id_feature_value', 'ASC')
        ;

        $availableFilters = [
            'id_feature_value',
            'custom',
        ];

        foreach ($filters as $key => $value) {
            if (!in_array($key, $availableFilters)) {
                continue;
            }

            $qb
                ->andWhere(sprintf('fv.%s = :%s', $key, $key))
                ->setParameter($key, $value)
            ;
        }

        return $qb;
    }
}
