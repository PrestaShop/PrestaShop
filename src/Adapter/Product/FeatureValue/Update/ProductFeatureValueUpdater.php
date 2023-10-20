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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\FeatureValue\Update;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Exception\InvalidArgumentException;
use FeatureValue;
use PrestaShop\PrestaShop\Adapter\Feature\Repository\FeatureRepository;
use PrestaShop\PrestaShop\Adapter\Feature\Repository\FeatureValueRepository;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Core\Domain\Feature\Exception\CannotAddFeatureValueException;
use PrestaShop\PrestaShop\Core\Domain\Feature\Exception\CannotUpdateFeatureValueException;
use PrestaShop\PrestaShop\Core\Domain\Feature\Exception\FeatureNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Feature\Exception\FeatureValueNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Feature\ValueObject\FeatureValueId;
use PrestaShop\PrestaShop\Core\Domain\Product\FeatureValue\Exception\DuplicateFeatureValueAssociationException;
use PrestaShop\PrestaShop\Core\Domain\Product\FeatureValue\Exception\InvalidAssociatedFeatureException;
use PrestaShop\PrestaShop\Core\Domain\Product\FeatureValue\ValueObject\ProductFeatureValue;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;

/**
 * Updates FeatureValue & Product relation
 */
class ProductFeatureValueUpdater
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var string
     */
    private $dbPrefix;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var FeatureRepository
     */
    private $featureRepository;

    /**
     * @var FeatureValueRepository
     */
    private $featureValueRepository;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     * @param ProductRepository $productRepository
     * @param FeatureRepository $featureRepository
     * @param FeatureValueRepository $featureValueRepository
     */
    public function __construct(
        Connection $connection,
        string $dbPrefix,
        ProductRepository $productRepository,
        FeatureRepository $featureRepository,
        FeatureValueRepository $featureValueRepository
    ) {
        $this->connection = $connection;
        $this->dbPrefix = $dbPrefix;
        $this->productRepository = $productRepository;
        $this->featureRepository = $featureRepository;
        $this->featureValueRepository = $featureValueRepository;
    }

    /**
     * @param ProductId $productId
     * @param ProductFeatureValue[] $productFeatureValues
     *
     * @return FeatureValueId[]
     *
     * @throws CannotAddFeatureValueException
     * @throws CannotUpdateFeatureValueException
     * @throws CoreException
     * @throws DBALException
     * @throws FeatureValueNotFoundException
     * @throws InvalidArgumentException
     * @throws FeatureNotFoundException
     */
    public function setFeatureValues(ProductId $productId, array $productFeatureValues): array
    {
        // First assert that all entities exist
        $this->productRepository->assertProductExists($productId);
        $previousFeatureIds = [];
        foreach ($productFeatureValues as $productFeatureValue) {
            $this->featureRepository->assertExists($productFeatureValue->getFeatureId());
            if (null !== $productFeatureValue->getFeatureValueId()) {
                $featureValue = $this->featureValueRepository->get($productFeatureValue->getFeatureValueId());
                if ((int) $featureValue->id_feature !== $productFeatureValue->getFeatureId()->getValue()) {
                    throw new InvalidAssociatedFeatureException('You cannot associate a value to another feature.');
                }
                if (in_array($productFeatureValue->getFeatureValueId()->getValue(), $previousFeatureIds)) {
                    throw new DuplicateFeatureValueAssociationException('You cannot associate the same feature value more than once.');
                }
                $previousFeatureIds[] = $productFeatureValue->getFeatureValueId()->getValue();
            }
        }

        foreach ($productFeatureValues as $productFeatureValue) {
            if (null !== $productFeatureValue->getFeatureValueId()) {
                $this->updateFeatureValue($productFeatureValue);
            } else {
                $this->addFeatureValue($productFeatureValue);
            }
        }

        return $this->updateAssociations($productId, $productFeatureValues);
    }

    /**
     * @param ProductId $productId
     * @param array $productFeatureValues
     *
     * @return FeatureValueId[]
     *
     * @throws DBALException
     * @throws InvalidArgumentException
     */
    private function updateAssociations(ProductId $productId, array $productFeatureValues): array
    {
        // First delete all associations from the product
        $this->connection->delete(
            $this->dbPrefix . 'feature_product',
            ['id_product' => $productId->getValue()]
        );

        // Then create all new ones
        $productFeatureValueIds = [];
        foreach ($productFeatureValues as $productFeatureValue) {
            $insertedValues = [
                'id_product' => $productId->getValue(),
                'id_feature' => $productFeatureValue->getFeatureId()->getValue(),
                'id_feature_value' => $productFeatureValue->getFeatureValueId()->getValue(),
            ];
            $this->connection->insert($this->dbPrefix . 'feature_product', $insertedValues);

            $productFeatureValueIds[] = $productFeatureValue->getFeatureValueId();
        }

        $this->cleanOrphanCustomFeatureValues();

        return $productFeatureValueIds;
    }

    /**
     * Remove custom feature values that are no longer associated to a product
     */
    private function cleanOrphanCustomFeatureValues(): void
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->from($this->dbPrefix . 'feature_value', 'fv')
            ->select('fv.*, fp.id_product')
            ->leftJoin('fv', $this->dbPrefix . 'feature_product', 'fp', 'fp.id_feature_value = fv.id_feature_value')
            ->where($qb->expr()->andX(
                $qb->expr()->isNull('fp.id_product')),
                $qb->expr()->neq('fv.custom', 0)
            )
        ;

        $orphanCustomFeatureValues = $qb->execute()->fetchAll();
        if (empty($orphanCustomFeatureValues)) {
            return;
        }

        $orphanIds = [];
        foreach ($orphanCustomFeatureValues as $orphanCustomFeatureValue) {
            $orphanIds[] = $orphanCustomFeatureValue['id_feature_value'];
        }

        $qb = $this->connection->createQueryBuilder();
        $qb->delete($this->dbPrefix . 'feature_value')
            ->where($qb->expr()->in('id_feature_value', $orphanIds))
        ;
        $qb->execute();
    }

    /**
     * @param ProductFeatureValue $productFeatureValue
     *
     * @throws CannotUpdateFeatureValueException
     * @throws CoreException
     * @throws FeatureValueNotFoundException
     */
    private function updateFeatureValue(ProductFeatureValue $productFeatureValue): void
    {
        // Only custom values need to be updated
        if (null === $productFeatureValue->getLocalizedCustomValues()) {
            return;
        }
        $featureValue = $this->featureValueRepository->get($productFeatureValue->getFeatureValueId());
        $featureValue->value = $productFeatureValue->getLocalizedCustomValues();
        $this->featureValueRepository->update($featureValue);
    }

    /**
     * @param ProductFeatureValue $productFeatureValue
     *
     * @throws CannotAddFeatureValueException
     * @throws CoreException
     */
    private function addFeatureValue(ProductFeatureValue $productFeatureValue): void
    {
        $featureValue = new FeatureValue();
        $featureValue->id_feature = (int) $productFeatureValue->getFeatureId()->getValue();
        $featureValue->custom = null !== $productFeatureValue->getLocalizedCustomValues();
        if (null !== $productFeatureValue->getLocalizedCustomValues()) {
            $featureValue->value = $productFeatureValue->getLocalizedCustomValues();
        }
        $featureValueId = $this->featureValueRepository->add($featureValue);
        $productFeatureValue->setFeatureValueId($featureValueId);
    }
}
