<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\TaxRulesGroup\Repository;

use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Adapter\TaxRulesGroup\Validate\TaxRulesGroupValidator;
use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
use PrestaShop\PrestaShop\Core\Domain\Store\Exception\CannotUpdateStoreException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\CannotAddTaxRulesGroupException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\CannotUpdateTaxRulesGroupException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\TaxRulesGroupNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\ValueObject\TaxRulesGroupId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\Repository\AbstractMultiShopObjectModelRepository;
use Product;
use TaxRulesGroup;

/**
 * Provides access to TaxRulesGroup data source
 */
class TaxRulesGroupRepository extends AbstractMultiShopObjectModelRepository
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
     * @var TaxRulesGroupValidator
     */
    private $taxRulesGroupValidator;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     * @param TaxRulesGroupValidator $taxRulesGroupValidator
     */
    public function __construct(
        Connection $connection,
        string $dbPrefix,
        TaxRulesGroupValidator $taxRulesGroupValidator
    ) {
        $this->connection = $connection;
        $this->dbPrefix = $dbPrefix;
        $this->taxRulesGroupValidator = $taxRulesGroupValidator;
    }

    /**
     * @param TaxRulesGroupId $taxRulesGroupId
     * @param CountryId $countryId
     *
     * @return int
     */
    public function getTaxRulesGroupDefaultStateId(TaxRulesGroupId $taxRulesGroupId, CountryId $countryId): int
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('tr.id_state')
            ->from($this->dbPrefix . 'tax_rules_group', 'trg')
            ->innerJoin('trg', $this->dbPrefix . 'tax_rule', 'tr', 'tr.id_tax_rules_group = trg.id_tax_rules_group')
            ->innerJoin('tr', $this->dbPrefix . 'tax', 't', 't.id_tax = tr.id_tax')
            ->andWhere('trg.id_tax_rules_group = :taxRulesGroupId')
            ->andWhere('tr.id_country = :countryId')
            ->andWhere('trg.deleted = 0')
            ->setParameters([
                'taxRulesGroupId' => $taxRulesGroupId->getValue(),
                'countryId' => $countryId->getValue(),
            ])
        ;

        $rawData = $qb->executeQuery()->fetchAllAssociative();
        if (empty($rawData)) {
            return 0;
        }
        $firstRow = reset($rawData);

        return (int) $firstRow['id_state'];
    }

    /**
     * @param TaxRulesGroupId $taxRulesGroupId
     *
     * @return TaxRulesGroup
     *
     * @throws CoreException
     * @throws TaxRulesGroupNotFoundException
     */
    public function get(TaxRulesGroupId $taxRulesGroupId): TaxRulesGroup
    {
        /** @var TaxRulesGroup $taxRulesGroup */
        $taxRulesGroup = $this->getObjectModel(
            $taxRulesGroupId->getValue(),
            TaxRulesGroup::class,
            TaxRulesGroupNotFoundException::class
        );

        return $taxRulesGroup;
    }

    /**
     * @param TaxRulesGroupId $taxRulesGroupId
     *
     * @throws CoreException
     * @throws TaxRulesGroupNotFoundException
     */
    public function assertTaxRulesGroupExists(TaxRulesGroupId $taxRulesGroupId): void
    {
        $this->assertObjectModelExists(
            $taxRulesGroupId->getValue(),
            'tax_rules_group',
            TaxRulesGroupNotFoundException::class
        );
    }

    /**
     * @param TaxRulesGroup $taxRulesGroup
     * @param ShopId[] $shopIds
     * @param int $errorCode
     *
     * @return TaxRulesGroupId
     */
    public function add(TaxRulesGroup $taxRulesGroup, array $shopIds, int $errorCode = 0): TaxRulesGroupId
    {
        $this->taxRulesGroupValidator->validate($taxRulesGroup);
        $id = $this->addObjectModelToShops(
            $taxRulesGroup,
            $shopIds,
            CannotAddTaxRulesGroupException::class,
            $errorCode
        );

        return new TaxRulesGroupId($id);
    }

    /**
     * @param TaxRulesGroup $taxRulesGroup
     * @param ShopId[] $shopIds
     */
    public function update(TaxRulesGroup $taxRulesGroup, array $shopIds): void
    {
        $this->taxRulesGroupValidator->validate($taxRulesGroup);
        $this->updateObjectModelForShops(
            $taxRulesGroup,
            $shopIds,
            CannotUpdateTaxRulesGroupException::class
        );
    }

    /**
     * @param TaxRulesGroup $taxRulesGroup
     * @param array $propertiesToUpdate
     * @param ShopId[] $shopIds
     * @param int $errorCode
     */
    public function partialUpdate(
        TaxRulesGroup $taxRulesGroup,
        array $propertiesToUpdate,
        array $shopIds,
        int $errorCode
    ): void {
        $this->partiallyUpdateObjectModelForShops(
            $taxRulesGroup,
            $propertiesToUpdate,
            $shopIds,
            CannotUpdateStoreException::class,
            $errorCode
        );
    }

    /**
     * Get most used Tax.
     *
     * @return int
     */
    public function getIdTaxRulesGroupMostUsed()
    {
        return (int) Product::getIdTaxRulesGroupMostUsed();
    }
}
