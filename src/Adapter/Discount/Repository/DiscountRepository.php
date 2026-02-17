<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Discount\Repository;

use CartRule;
use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Adapter\Discount\Validate\DiscountValidator;
use PrestaShop\PrestaShop\Core\Domain\Discount\Exception\CannotAddDiscountException;
use PrestaShop\PrestaShop\Core\Domain\Discount\Exception\CannotDeleteDiscountException;
use PrestaShop\PrestaShop\Core\Domain\Discount\Exception\CannotUpdateDiscountException;
use PrestaShop\PrestaShop\Core\Domain\Discount\Exception\DiscountNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Discount\ProductRule;
use PrestaShop\PrestaShop\Core\Domain\Discount\ProductRuleGroup;
use PrestaShop\PrestaShop\Core\Domain\Discount\ProductRuleGroupType;
use PrestaShop\PrestaShop\Core\Domain\Discount\ProductRuleType;
use PrestaShop\PrestaShop\Core\Domain\Discount\ValueObject\DiscountId;
use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;
use PrestaShop\PrestaShop\Core\Repository\AbstractObjectModelRepository;

/**
 * This repository is used for the new Discount domain, but it still relies on the legacy CartRule ObjectModel.
 */
class DiscountRepository extends AbstractObjectModelRepository
{
    public function __construct(
        protected readonly DiscountValidator $discountValidator,
        protected readonly Connection $connection,
        protected readonly string $dbPrefix,
    ) {
    }

    public function add(CartRule $cartRule): CartRule
    {
        $this->discountValidator->validate($cartRule);
        $this->addObjectModel($cartRule, CannotAddDiscountException::class);

        return $cartRule;
    }

    public function get(DiscountId $discountId): CartRule
    {
        /** @var CartRule $cartRule */
        $cartRule = $this->getObjectModel(
            $discountId->getValue(),
            CartRule::class,
            DiscountNotFoundException::class
        );

        return $cartRule;
    }

    /**
     * @param DiscountId $discountId
     *
     * @return ProductRuleGroup[]
     *
     * @throws InvalidArgumentException
     */
    public function getProductRulesGroup(DiscountId $discountId): array
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('*')
            ->from($this->dbPrefix . 'cart_rule_product_rule_group', 'prg')
            ->where('prg.id_cart_rule = :discountId')
            ->setparameter('discountId', $discountId->getValue())
        ;

        $groupsResult = $qb->executeQuery()->fetchAllAssociative();
        if (empty($groupsResult)) {
            return [];
        }

        $productRulesGroups = [];
        foreach ($groupsResult as $groupData) {
            $ruleGroupType = ProductRuleGroupType::tryFrom((string) $groupData['type']);
            if (empty($ruleGroupType)) {
                throw new InvalidArgumentException(sprintf('Unknow product rule group type %s', (string) $groupData['type']));
            }

            $qb = $this->connection->createQueryBuilder();
            $qb
                ->select('*')
                ->from($this->dbPrefix . 'cart_rule_product_rule', 'pr')
                ->where('pr.id_product_rule_group = :productRuleGroupId')
                ->setparameter('productRuleGroupId', (int) $groupData['id_product_rule_group'])
            ;
            $productRulesResult = $qb->executeQuery()->fetchAllAssociative();

            $productRules = [];
            if (!empty($productRulesResult)) {
                foreach ($productRulesResult as $productRuleData) {
                    $qb = $this->connection->createQueryBuilder();
                    $qb
                        ->select('*')
                        ->from($this->dbPrefix . 'cart_rule_product_rule_value', 'prv')
                        ->where('prv.id_product_rule = :productRuleId')
                        ->setparameter('productRuleId', $productRuleData['id_product_rule'])
                    ;
                    $productRuleValuesResult = $qb->executeQuery()->fetchAllAssociative();
                    $itemIds = [];
                    if (!empty($productRuleValuesResult)) {
                        foreach ($productRuleValuesResult as $productRuleValueData) {
                            $itemIds[] = (int) $productRuleValueData['id_item'];
                        }
                    }

                    $productType = ProductRuleType::tryFrom((string) $productRuleData['type']);
                    if (empty($productType)) {
                        throw new InvalidArgumentException(sprintf('Unknow product rule type %s', (string) $productRuleData['type']));
                    }

                    $productRules[] = new ProductRule($productType, $itemIds);
                }
            }

            $productRulesGroups[] = new ProductRuleGroup((int) $groupData['quantity'], $productRules, $ruleGroupType);
        }

        return $productRulesGroups;
    }

    /**
     * @return int[]
     */
    public function getCarriersIds(DiscountId $discountId): array
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('crc.id_carrier')
            ->from($this->dbPrefix . 'cart_rule_carrier', 'crc')
            ->where('crc.id_cart_rule = :discountId')
            ->setparameter('discountId', $discountId->getValue())
        ;

        return array_map(fn (array $row) => (int) $row['id_carrier'], $qb->executeQuery()->fetchAllAssociative());
    }

    /**
     * @return int[]
     */
    public function getCountriesIds(DiscountId $discountId): array
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('crc.id_country')
            ->from($this->dbPrefix . 'cart_rule_country', 'crc')
            ->where('crc.id_cart_rule = :discountId')
            ->setparameter('discountId', $discountId->getValue())
        ;

        return array_map(fn (array $row) => (int) $row['id_country'], $qb->executeQuery()->fetchAllAssociative());
    }

    /**
     * @return int[]
     */
    public function getCustomerGroupsIds(DiscountId $discountId): array
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('crg.id_group')
            ->from($this->dbPrefix . 'cart_rule_group', 'crg')
            ->where('crg.id_cart_rule = :discountId')
            ->setparameter('discountId', $discountId->getValue())
        ;

        return array_map(fn (array $row) => (int) $row['id_group'], $qb->executeQuery()->fetchAllAssociative());
    }

    /**
     * @return int[]
     */
    public function getCompatibleTypesIds(DiscountId $discountId): array
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('crt.id_cart_rule_type')
            ->from($this->dbPrefix . 'cart_rule_compatible_types', 'crt')
            ->where('crt.id_cart_rule = :discountId')
            ->setparameter('discountId', $discountId->getValue())
        ;

        return array_map(fn (array $row) => (int) $row['id_cart_rule_type'], $qb->executeQuery()->fetchAllAssociative());
    }

    /**
     * @return int[]
     */
    public function getGroupsIds(DiscountId $discountId): array
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('crg.id_group')
            ->from($this->dbPrefix . 'cart_rule_group', 'crg')
            ->where('crg.id_cart_rule = :discountId')
            ->setparameter('discountId', $discountId->getValue())
        ;

        return array_map(fn (array $row) => (int) $row['id_group'], $qb->executeQuery()->fetchAllAssociative());
    }

    /**
     * @return int[]
     */
    public function getShopsIds(DiscountId $discountId): array
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('crs.id_shop')
            ->from($this->dbPrefix . 'cart_rule_shop', 'crs')
            ->where('crs.id_cart_rule = :discountId')
            ->setparameter('discountId', $discountId->getValue())
        ;

        return array_map(fn (array $row) => (int) $row['id_shop'], $qb->executeQuery()->fetchAllAssociative());
    }

    /**
     * Returns the ID of a discount by its code.
     * null is returned if the discount does not exist.
     */
    public function getIdByCode(string $code): ?int
    {
        $cartRuleId = $this->connection->createQueryBuilder()
            ->select('id_cart_rule')
            ->from($this->dbPrefix . 'cart_rule')
            ->where('UPPER(code) = :code')
            ->setParameter('code', mb_strtoupper(trim($code)))
            ->fetchOne()
        ;

        if (false === $cartRuleId) {
            return null;
        }

        return $cartRuleId;
    }

    public function partialUpdate(CartRule $cartRule, array $updatableProperties, int $errorCode): void
    {
        $this->discountValidator->validate($cartRule);

        $this->partiallyUpdateObjectModel(
            $cartRule,
            $updatableProperties,
            CannotUpdateDiscountException::class,
            $errorCode
        );
    }

    public function delete(DiscountId $discountId): void
    {
        $this->deleteObjectModel($this->get($discountId), CannotDeleteDiscountException::class);
    }
}
