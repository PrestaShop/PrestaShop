<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Combination\FeatureValue\Update;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception as DBALException;
use Doctrine\DBAL\Exception\InvalidArgumentException;
use FeatureValue;
use PrestaShop\PrestaShop\Adapter\Feature\Repository\FeatureRepository;
use PrestaShop\PrestaShop\Adapter\Feature\Repository\FeatureValueRepository;
use PrestaShop\PrestaShop\Adapter\Product\Combination\Repository\CombinationRepository;
use PrestaShop\PrestaShop\Core\Domain\Feature\Exception\CannotAddFeatureValueException;
use PrestaShop\PrestaShop\Core\Domain\Feature\Exception\CannotUpdateFeatureValueException;
use PrestaShop\PrestaShop\Core\Domain\Feature\Exception\FeatureNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Feature\Exception\FeatureValueNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Feature\ValueObject\FeatureValueId;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\FeatureValue\ValueObject\CombinationFeatureValue;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\FeatureValue\Exception\DuplicateFeatureValueAssociationException;
use PrestaShop\PrestaShop\Core\Domain\Product\FeatureValue\Exception\InvalidAssociatedFeatureException;
use PrestaShop\PrestaShop\Core\Exception\CoreException;

/**
 * Updates FeatureValue & Combination (product_attribute) relation
 */
class CombinationFeatureValueUpdater
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
     * @var CombinationRepository
     */
    private $combinationRepository;

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
     * @param CombinationRepository $combinationRepository
     * @param FeatureRepository $featureRepository
     * @param FeatureValueRepository $featureValueRepository
     */
    public function __construct(
        Connection $connection,
        string $dbPrefix,
        CombinationRepository $combinationRepository,
        FeatureRepository $featureRepository,
        FeatureValueRepository $featureValueRepository
    ) {
        $this->connection = $connection;
        $this->dbPrefix = $dbPrefix;
        $this->combinationRepository = $combinationRepository;
        $this->featureRepository = $featureRepository;
        $this->featureValueRepository = $featureValueRepository;
    }

    /**
     * @param CombinationId $combinationId
     * @param CombinationFeatureValue[] $combinationFeatureValues
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
    public function setFeatureValues(CombinationId $combinationId, array $combinationFeatureValues): array
    {
        // First assert that all entities exist
        $this->combinationRepository->assertCombinationExists($combinationId);
        $previousFeatureValueIds = [];
        foreach ($combinationFeatureValues as $combinationFeatureValue) {
            $this->featureRepository->assertExists($combinationFeatureValue->getFeatureId());
            if (null !== $combinationFeatureValue->getFeatureValueId()) {
                $featureValue = $this->featureValueRepository->get($combinationFeatureValue->getFeatureValueId());
                if ((int) $featureValue->id_feature !== $combinationFeatureValue->getFeatureId()->getValue()) {
                    throw new InvalidAssociatedFeatureException('You cannot associate a value to another feature.');
                }
                if (in_array($combinationFeatureValue->getFeatureValueId()->getValue(), $previousFeatureValueIds)) {
                    throw new DuplicateFeatureValueAssociationException('You cannot associate the same feature value more than once.');
                }
                $previousFeatureValueIds[] = $combinationFeatureValue->getFeatureValueId()->getValue();
            }
        }

        foreach ($combinationFeatureValues as $combinationFeatureValue) {
            if (null !== $combinationFeatureValue->getFeatureValueId()) {
                $this->updateFeatureValue($combinationFeatureValue);
            } else {
                $this->addFeatureValue($combinationFeatureValue);
            }
        }

        return $this->updateAssociations($combinationId, $combinationFeatureValues);
    }

    /**
     * @param CombinationId $combinationId
     * @param array $combinationFeatureValues
     *
     * @return FeatureValueId[]
     *
     * @throws DBALException
     * @throws InvalidArgumentException
     */
    private function updateAssociations(CombinationId $combinationId, array $combinationFeatureValues): array
    {
        // First delete all associations from the combination
        $this->connection->delete(
            $this->dbPrefix . 'feature_product_attribute',
            ['id_product_attribute' => $combinationId->getValue()]
        );

        // Then create all new ones
        $combinationFeatureValueIds = [];
        foreach ($combinationFeatureValues as $combinationFeatureValue) {
            $insertedValues = [
                'id_product_attribute' => $combinationId->getValue(),
                'id_feature' => $combinationFeatureValue->getFeatureId()->getValue(),
                'id_feature_value' => $combinationFeatureValue->getFeatureValueId()->getValue(),
            ];
            $this->connection->insert($this->dbPrefix . 'feature_product_attribute', $insertedValues);

            $combinationFeatureValueIds[] = $combinationFeatureValue->getFeatureValueId();
        }

        $this->cleanOrphanCustomFeatureValues();

        return $combinationFeatureValueIds;
    }

    /**
     * Remove custom feature values that are no longer associated to a product nor a combination.
     * A custom value can be referenced either by feature_product or feature_product_attribute,
     * so both tables must be checked before deleting.
     */
    private function cleanOrphanCustomFeatureValues(): void
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->from($this->dbPrefix . 'feature_value', 'fv')
            ->select('fv.id_feature_value')
            ->leftJoin('fv', $this->dbPrefix . 'feature_product', 'fp', 'fp.id_feature_value = fv.id_feature_value')
            ->leftJoin('fv', $this->dbPrefix . 'feature_product_attribute', 'fpa', 'fpa.id_feature_value = fv.id_feature_value')
            ->where($qb->expr()->andX(
                $qb->expr()->isNull('fp.id_product'),
                $qb->expr()->isNull('fpa.id_product_attribute'),
                $qb->expr()->neq('fv.custom', 0)
            ))
        ;

        $orphanCustomFeatureValues = $qb->executeQuery()->fetchAllAssociative();
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
        $qb->executeStatement();
    }

    /**
     * @param CombinationFeatureValue $combinationFeatureValue
     *
     * @throws CannotUpdateFeatureValueException
     * @throws CoreException
     * @throws FeatureValueNotFoundException
     */
    private function updateFeatureValue(CombinationFeatureValue $combinationFeatureValue): void
    {
        // Only custom values need to be updated
        if (null === $combinationFeatureValue->getLocalizedCustomValues()) {
            return;
        }
        $featureValue = $this->featureValueRepository->get($combinationFeatureValue->getFeatureValueId());
        $featureValue->value = $combinationFeatureValue->getLocalizedCustomValues();
        $this->featureValueRepository->update($featureValue);
    }

    /**
     * @param CombinationFeatureValue $combinationFeatureValue
     *
     * @throws CannotAddFeatureValueException
     * @throws CoreException
     */
    private function addFeatureValue(CombinationFeatureValue $combinationFeatureValue): void
    {
        $featureValue = new FeatureValue();
        $featureValue->id_feature = (int) $combinationFeatureValue->getFeatureId()->getValue();
        $featureValue->custom = null !== $combinationFeatureValue->getLocalizedCustomValues();
        if (null !== $combinationFeatureValue->getLocalizedCustomValues()) {
            $featureValue->value = $combinationFeatureValue->getLocalizedCustomValues();
        }
        $featureValueId = $this->featureValueRepository->add($featureValue);
        $combinationFeatureValue->setFeatureValueId($featureValueId);
    }
}
