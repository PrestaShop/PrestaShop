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

namespace PrestaShop\PrestaShop\Adapter\Product\Repository;

use Combination;
use Db;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Adapter\AbstractObjectModelRepository;
use PrestaShop\PrestaShop\Adapter\Attribute\Repository\AttributeRepository;
use PrestaShop\PrestaShop\Adapter\Product\Validate\CombinationValidator;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Exception\CannotAddCombinationException;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Exception\CannotDeleteCombinationException;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Exception\CombinationNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShopException;

/**
 * Provides access to Combination data source
 */
class CombinationRepository extends AbstractObjectModelRepository
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
     * @var AttributeRepository
     */
    private $attributeRepository;

    /**
     * @var CombinationValidator
     */
    private $combinationValidator;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     * @param AttributeRepository $attributeRepository
     * @param CombinationValidator $combinationValidator
     */
    public function __construct(
        Connection $connection,
        string $dbPrefix,
        AttributeRepository $attributeRepository,
        CombinationValidator $combinationValidator
    ) {
        $this->connection = $connection;
        $this->dbPrefix = $dbPrefix;
        $this->attributeRepository = $attributeRepository;
        $this->combinationValidator = $combinationValidator;
    }

    /**
     * @param CombinationId $combinationId
     *
     * @return Combination
     *
     * @throws CombinationNotFoundException
     */
    public function get(CombinationId $combinationId): Combination
    {
        /** @var Combination $combination */
        $combination = $this->getObjectModel(
            $combinationId->getValue(),
            Combination::class,
            CombinationNotFoundException::class
        );

        return $combination;
    }

    /**
     * @param ProductId $productId
     * @param bool $isDefault
     *
     * @return Combination
     *
     * @throws CoreException
     */
    public function create(ProductId $productId, bool $isDefault): Combination
    {
        $combination = new Combination();
        $combination->id_product = $productId->getValue();
        $combination->default_on = $isDefault;

        $this->addObjectModel($combination, CannotAddCombinationException::class);

        return $combination;
    }

    /**
     * @param Combination $combination
     * @param array $updatableProperties
     * @param int $errorCode
     *
     * @throws CoreException
     * @throws CannotAddCombinationException
     */
    public function partialUpdate(Combination $combination, array $updatableProperties, int $errorCode): void
    {
        $this->combinationValidator->validate($combination);
        $this->partiallyUpdateObjectModel(
            $combination,
            $updatableProperties,
            CannotAddCombinationException::class,
            $errorCode
        );
    }

    /**
     * @param Combination $combination
     * @param int $errorCode
     *
     * @throws CoreException
     */
    public function delete(Combination $combination, int $errorCode = 0): void
    {
        $this->deleteObjectModel($combination, CannotDeleteCombinationException::class, $errorCode);
    }

    /**
     * @param ProductId $productId
     * @param int $limit
     * @param int $offset
     * @param array $filters
     *
     * @return array<int, array<string, mixed>>
     */
    public function getProductCombinations(ProductId $productId, ?int $limit = null, ?int $offset = null, array $filters = []): array
    {
        $qb = $this->getCombinationsQueryBuilder($productId, $filters)
            ->select('pa.*')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
        ;

        return $qb->execute()->fetchAll();
    }

    /**
     * @param ProductId $productId
     * @param array $filters
     *
     * @return int
     */
    public function getTotalCombinationsCount(ProductId $productId, array $filters = []): int
    {
        $qb = $this->getCombinationsQueryBuilder($productId, $filters)
            ->select('COUNT(pa.id_product_attribute) AS total_combinations')
        ;

        return (int) $qb->execute()->fetch()['total_combinations'];
    }

    /**
     * @param int[] $combinationIds
     * @param LanguageId $langId
     *
     * @return array<int, array<int, mixed>>
     */
    public function getAttributesInfoByCombinationIds(array $combinationIds, LanguageId $langId): array
    {
        $attributeCombinationAssociations = $this->getAttributeCombinationAssociations($combinationIds);

        $attributeIds = array_unique(array_map(function (array $attributeByCombination): int {
            return (int) $attributeByCombination['id_attribute'];
        }, $attributeCombinationAssociations));

        $attributesInfoByAttributeId = $this->getAttributesInformation($attributeIds, $langId->getValue());

        $attributesInfoByCombinationId = [];
        foreach ($attributeCombinationAssociations as $attributeCombinationAssociation) {
            $combinationId = (int) $attributeCombinationAssociation['id_product_attribute'];
            $attributeId = (int) $attributeCombinationAssociation['id_attribute'];
            $attributesInfoByCombinationId[$combinationId][] = $attributesInfoByAttributeId[$attributeId];
        }

        return $attributesInfoByCombinationId;
    }

    /**
     * @param CombinationId $combinationId
     *
     * @throws CoreException
     */
    public function assertCombinationExists(CombinationId $combinationId): void
    {
        $this->assertObjectModelExists(
            $combinationId->getValue(),
            'product_attribute',
            CombinationNotFoundException::class
        );
    }

    /**
     * @param CombinationId $combinationId
     * @param int[] $attributeIds
     */
    public function saveProductAttributeAssociation(CombinationId $combinationId, array $attributeIds): void
    {
        $this->assertCombinationExists($combinationId);
        $this->attributeRepository->assertAllAttributesExist($attributeIds);

        $attributesList = [];
        foreach ($attributeIds as $attributeId) {
            $attributesList[] = [
                'id_product_attribute' => $combinationId->getValue(),
                'id_attribute' => $attributeId,
            ];
        }

        try {
            if (!Db::getInstance()->insert('product_attribute_combination', $attributesList)) {
                throw new CannotAddCombinationException('Failed saving product-combination associations');
            }
        } catch (PrestaShopException $e) {
            throw new CoreException('Error occurred when saving product-combination associations', 0, $e);
        }
    }

    /**
     * @param int[] $combinationIds
     *
     * @return array<int, array<string, mixed>>
     */
    private function getAttributeCombinationAssociations(array $combinationIds): array
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select('pac.id_attribute')
            ->addSelect('pac.id_product_attribute')
            ->from($this->dbPrefix . 'product_attribute_combination', 'pac')
            ->where($qb->expr()->in('pac.id_product_attribute', ':combinationIds'))
            ->setParameter('combinationIds', $combinationIds, Connection::PARAM_INT_ARRAY)
        ;

        return $qb->execute()->fetchAll();
    }

    /**
     * @param int[] $attributeIds
     * @param int $langId
     *
     * @return array<int, array<int, mixed>>
     */
    private function getAttributesInformation(array $attributeIds, int $langId): array
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select('a.id_attribute')
            ->addSelect('ag.id_attribute_group')
            ->addSelect('al.name AS attribute_name')
            ->addSelect('agl.name AS attribute_group_name')
            ->from($this->dbPrefix . 'attribute', 'a')
            ->leftJoin(
                'a',
                $this->dbPrefix . 'attribute_lang',
                'al',
                'a.id_attribute = al.id_attribute AND al.id_lang = :langId'
            )->leftJoin(
                'a',
                $this->dbPrefix . 'attribute_group',
                'ag',
                'a.id_attribute_group = ag.id_attribute_group'
            )->leftJoin(
                'ag',
                $this->dbPrefix . 'attribute_group_lang',
                'agl',
                'agl.id_attribute_group = ag.id_attribute_group AND agl.id_lang = :langId'
            )->where($qb->expr()->in('a.id_attribute', ':attributeIds'))
            ->setParameter('attributeIds', $attributeIds, Connection::PARAM_INT_ARRAY)
            ->setParameter('langId', $langId)
        ;

        $attributesInfo = $qb->execute()->fetchAll();

        $attributesInfoByAttributeId = [];
        foreach ($attributesInfo as $attributeInfo) {
            $attributesInfoByAttributeId[(int) $attributeInfo['id_attribute']][] = $attributeInfo;
        }

        return $attributesInfoByAttributeId;
    }

    /**
     * @param ProductId $productId
     * @param array $filters
     *
     * @return QueryBuilder
     */
    private function getCombinationsQueryBuilder(ProductId $productId, array $filters): QueryBuilder
    {
        //@todo: filters are not handled.
        $qb = $this->connection->createQueryBuilder();
        $qb->from($this->dbPrefix . 'product_attribute', 'pa')
            ->where('pa.id_product = :productId')
            ->orderBy('id_product_attribute', 'asc')
            ->setParameter('productId', $productId->getValue())
        ;

        return $qb;
    }
}
