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
use PrestaShop\PrestaShop\Adapter\AbstractObjectModelRepository;
use PrestaShop\PrestaShop\Adapter\Feature\Validate\FeatureValueValidator;
use PrestaShop\PrestaShop\Core\Domain\Feature\Exception\CannotAddFeatureValueException;
use PrestaShop\PrestaShop\Core\Domain\Feature\Exception\CannotUpdateFeatureValueException;
use PrestaShop\PrestaShop\Core\Domain\Feature\Exception\FeatureValueNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Feature\Exception\InvalidFeatureValueIdException;
use PrestaShop\PrestaShop\Core\Domain\Feature\ValueObject\FeatureValueId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;

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
        return $this->getFeatureValues($limit, $offset, array_merge($filters, ['id_product' => $productId->getValue()]));
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
            ->setFirstResult($offset)
            ->setMaxResults($limit)
        ;

        $featureValues = $qb->execute()->fetchAll();
        foreach ($featureValues as $index => $featureValue) {
            $featureValues[$index]['localized_values'] = $this->getFeatureValueLocalizedValues((int) $featureValue['id_feature_value']);
        }

        return $featureValues;
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

        return (int) $qb->execute()->fetch()['total_feature_values'];
    }

    /**
     * @param int $featureValueId
     *
     * @return array
     */
    private function getFeatureValueLocalizedValues(int $featureValueId): array
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->from($this->dbPrefix . 'feature_value_lang', 'fvl')
            ->select('fvl.*')
            ->where('fvl.id_feature_value= :featureValueId')
            ->setParameter('featureValueId', $featureValueId)
        ;

        $values = $qb->execute()->fetchAll();
        $localizedValues = [];
        foreach ($values as $value) {
            $localizedValues[(int) $value['id_lang']] = $value['value'];
        }

        return $localizedValues;
    }

    /**
     * @param array|null $filters
     *
     * @return QueryBuilder
     */
    private function getFeatureValuesQueryBuilder(?array $filters): QueryBuilder
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->from($this->dbPrefix . 'feature_value', 'fv')
            ->leftJoin('fv', $this->dbPrefix . 'feature_product', 'fp', 'fp.id_feature_value = fv.id_feature_value')
            ->leftJoin('fv', $this->dbPrefix . 'feature', 'f', 'f.id_feature = fv.id_feature')
            ->orderBy('f.position,fv.id_feature_value', 'ASC')
        ;

        $availableFilters = [
            'id_product',
            'id_feature',
            'id_feature_value',
            'custom',
        ];

        foreach ($filters as $key => $value) {
            if (!in_array($key, $availableFilters)) {
                continue;
            }

            switch ($key) {
                case 'id_product':
                    $qb
                        ->andWhere('fp.id_product = :productId')
                        ->setParameter('productId', (int) $value)
                    ;
                break;
                default:
                    $qb
                        ->andWhere(sprintf('fv.%s = :%s', $key, $key))
                        ->setParameter($key, $value)
                    ;
                break;
            }
        }

        return $qb;
    }
}
