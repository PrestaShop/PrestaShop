<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Discount\Update;

use CartRule;
use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Adapter\Discount\Repository\DiscountRepository;
use PrestaShop\PrestaShop\Adapter\Discount\Trait\ProductConditionsTrait;
use PrestaShop\PrestaShop\Core\Domain\Discount\DiscountSettings;
use PrestaShop\PrestaShop\Core\Domain\Discount\Exception\CannotUpdateDiscountException;
use PrestaShop\PrestaShop\Core\Domain\Discount\ProductRuleGroup;
use PrestaShop\PrestaShop\Core\Domain\Discount\ValueObject\DiscountId;
use PrestaShop\PrestaShop\Core\Domain\Discount\ValueObject\DiscountType;

class DiscountConditionsUpdater
{
    use ProductConditionsTrait;

    public function __construct(
        private readonly DiscountRepository $discountRepository,
        private readonly Connection $connection,
        private readonly string $dbPrefix,
    ) {
    }

    /**
     * For all provided fields, if the value is null, no modification is done and the fields remain untouched
     * (partial update), for the list of IDs if an empty array is provided the existing associations are removed
     * and no new association is created, so empty array is used to remove all existing associations.
     *
     * @param DiscountId $discountId
     * @param ProductRuleGroup[]|null $productConditions
     * @param int[]|null $carrierIds
     * @param int[]|null $countryIds
     * @param int[]|null $customerGroupIds
     *
     * @return void
     */
    public function update(
        DiscountId $discountId,
        ?array $productConditions = null,
        ?array $carrierIds = null,
        ?array $countryIds = null,
        ?array $customerGroupIds = null,
    ): void {
        // Nothing to modify we return immediately
        if ($productConditions === null
            && $carrierIds === null
            && $countryIds === null
            && $customerGroupIds === null) {
            return;
        }

        $discount = $this->discountRepository->get($discountId);
        $updatableProperties = [];
        // Product conditions can define product segments or a list of products (which is equivalent to a segment based on a product criteria)
        if (null !== $productConditions) {
            $updatableProperties = array_merge($updatableProperties, $this->applyProductConditions($discount, $productConditions));
        }
        if (null !== $carrierIds) {
            $updatableProperties = array_merge($updatableProperties, $this->applyCarrierConditions($discount, $carrierIds));
        }
        if (null !== $countryIds) {
            $updatableProperties = array_merge($updatableProperties, $this->applyCountryConditions($discount, $countryIds));
        }
        if (null !== $customerGroupIds) {
            $updatableProperties = array_merge($updatableProperties, $this->applyCustomerGroups($discount, $customerGroupIds));
        }

        $updatableProperties = array_unique($updatableProperties);
        if (!empty($updatableProperties)) {
            $this->discountRepository->partialUpdate($discount, $updatableProperties, CannotUpdateDiscountException::FAILED_UPDATE_CONDITIONS);
        }
    }

    /**
     * @param CartRule $discount
     * @param ProductRuleGroup[] $productRuleGroups
     *
     * @return array
     */
    private function applyProductConditions(
        CartRule $discount,
        array $productRuleGroups,
    ): array {
        // First clear all product rules (meaning if empty array is provided in this method, they are removed and no
        // new one is created, which is used to remove product conditions)
        $updatableProperties = $this->cleanDiscountProductRules($discount);
        if (!$this->isSegmentTargeted($productRuleGroups)) {
            return $updatableProperties;
        }

        foreach ($productRuleGroups as $productRuleGroup) {
            // First create group
            $this->connection->createQueryBuilder()
                ->insert($this->dbPrefix . 'cart_rule_product_rule_group')
                ->values([
                    'id_cart_rule' => ':discountId',
                    'quantity' => ':quantity',
                    'type' => ':type',
                ])
                ->setParameters([
                    'discountId' => (int) $discount->id,
                    'quantity' => $productRuleGroup->getQuantity(),
                    'type' => $productRuleGroup->getType()->value,
                ])
                ->executeStatement()
            ;
            $productRuleGroupId = $this->connection->lastInsertId();

            // Then create all product rules associated to the group
            foreach ($productRuleGroup->getRules() as $productRule) {
                $this->connection->createQueryBuilder()
                    ->insert($this->dbPrefix . 'cart_rule_product_rule')
                    ->values([
                        'id_product_rule_group' => ':productRuleGroupId',
                        'type' => ':type',
                    ])
                    ->setParameter('productRuleGroupId', $productRuleGroupId)
                    ->setParameter('type', $productRule->getType()->value)
                    ->executeStatement()
                ;
                $productRuleId = $this->connection->lastInsertId();

                // Finally assign all item values to the product rule via a multi insert statement
                $productRuleValues = [];
                $checkedIds = [];
                foreach ($productRule->getItemIds() as $itemId) {
                    if (in_array($itemId, $checkedIds, true)) {
                        // Skip in case there are duplicates
                        continue;
                    }

                    $productRuleValues[] = sprintf('(%d, %d)', $productRuleId, $itemId);
                    $checkedIds[] = $itemId;
                }
                $this->connection->prepare(sprintf(
                    'INSERT INTO %s (id_product_rule, id_item) VALUES %s',
                    $this->dbPrefix . 'cart_rule_product_rule_value',
                    implode(',', $productRuleValues)
                )
                )->executeStatement();
            }
        }
        $discount->product_restriction = !empty($productRuleGroups);
        $updatableProperties[] = 'product_restriction';

        // Product level discount now uses a condition on a segment of product, we need to update the
        // reduction_product property with the specific value (only for product level because this property
        // tells us that the discount applies on the whole segment)
        if ($discount->getType() === DiscountType::PRODUCT_LEVEL) {
            $discount->reduction_product = DiscountSettings::PRODUCT_SEGMENT;
            $updatableProperties[] = 'reduction_product';
        }

        return $updatableProperties;
    }

    private function applyCarrierConditions(CartRule $discount, array $carrierIds): array
    {
        $updatableProperties = $this->cleanDiscountCarriers($discount);
        if (empty($carrierIds)) {
            return $updatableProperties;
        }

        $discount->carrier_restriction = true;
        foreach ($carrierIds as $carrierId) {
            $this->connection->createQueryBuilder()
                ->insert($this->dbPrefix . 'cart_rule_carrier')
                ->values([
                    'id_cart_rule' => (int) $discount->id,
                    'id_carrier' => $carrierId,
                ])
                ->executeStatement()
            ;
        }

        return ['carrier_restriction'];
    }

    private function applyCountryConditions(CartRule $discount, array $countryIds): array
    {
        $updatableProperties = $this->cleanDiscountCountries($discount);
        if (empty($countryIds)) {
            return $updatableProperties;
        }

        $discount->country_restriction = true;
        foreach ($countryIds as $countryId) {
            $this->connection->createQueryBuilder()
                ->insert($this->dbPrefix . 'cart_rule_country')
                ->values([
                    'id_cart_rule' => (int) $discount->id,
                    'id_country' => $countryId,
                ])
                ->executeStatement()
            ;
        }

        return ['country_restriction'];
    }

    private function cleanDiscountProductRules(CartRule $discount): array
    {
        // Disable product restriction
        $discount->product_restriction = false;

        // First delete all associated product rule groups
        $this->connection->createQueryBuilder()
            ->delete($this->dbPrefix . 'cart_rule_product_rule_group')
            ->where('id_cart_rule = :discountId')
            ->setParameter('discountId', (int) $discount->id)
            ->executeStatement()
        ;

        // Then clean orphan rows in the related tables
        $this->connection->prepare('
            DELETE prv FROM ' . $this->dbPrefix . 'cart_rule_product_rule AS pr
            LEFT JOIN ' . $this->dbPrefix . 'cart_rule_product_group AS prg ON prg.id_product_rule_group = pr.id_product_rule_group
            WHERE prg.id_product_rule_group = NULL
        ');

        $this->connection->prepare('
            DELETE prv FROM ' . $this->dbPrefix . 'cart_rule_product_rule_value AS prv
            LEFT JOIN ' . $this->dbPrefix . 'cart_rule_product_rule AS pr ON prv.id_product_rule = pr.id_product_rule
            WHERE pr.id_product_rule = NULL
        ');

        $updatableProperties = ['product_restriction'];

        // If the discount was targeting a product segment, since we just removed it we reset the reduction_product property
        if ($discount->reduction_product === DiscountSettings::PRODUCT_SEGMENT) {
            // No more segment, no more target
            $discount->reduction_product = 0;
            $updatableProperties[] = 'reduction_product';
        }

        return $updatableProperties;
    }

    private function cleanDiscountCarriers(CartRule $discount): array
    {
        // Disable carrier restriction
        $discount->carrier_restriction = false;

        $this->connection->createQueryBuilder()
            ->delete($this->dbPrefix . 'cart_rule_carrier')
            ->where('id_cart_rule = :discountId')
            ->setParameter('discountId', (int) $discount->id)
            ->executeStatement()
        ;

        return ['carrier_restriction'];
    }

    private function cleanDiscountCountries(CartRule $discount): array
    {
        // Disable country restriction
        $discount->country_restriction = false;

        $this->connection->createQueryBuilder()
            ->delete($this->dbPrefix . 'cart_rule_country')
            ->where('id_cart_rule = :discountId')
            ->setParameter('discountId', (int) $discount->id)
            ->executeStatement()
        ;

        return ['country_restriction'];
    }

    /**
     * Update customer group restrictions for a discount.
     *
     * @param CartRule $discount
     * @param int[] $customerGroupIds
     *
     * @return array List of updated properties
     */
    private function applyCustomerGroups(CartRule $discount, array $customerGroupIds): array
    {
        $updatableProperties = $this->cleanCustomerGroups($discount);
        if (empty($customerGroupIds)) {
            return $updatableProperties;
        }

        $discount->group_restriction = true;
        foreach ($customerGroupIds as $groupId) {
            $this->connection->createQueryBuilder()
                ->insert($this->dbPrefix . 'cart_rule_group')
                ->values([
                    'id_cart_rule' => (int) $discount->id,
                    'id_group' => $groupId,
                ])
                ->executeStatement()
            ;
        }

        return ['group_restriction'];
    }

    /**
     * Clean all customer groups for a discount.
     *
     * @return array List of updated properties
     */
    private function cleanCustomerGroups(CartRule $discount): array
    {
        $discount->group_restriction = false;

        $this->connection->createQueryBuilder()
            ->delete($this->dbPrefix . 'cart_rule_group')
            ->where('id_cart_rule = :discountId')
            ->setParameter('discountId', (int) $discount->id)
            ->executeStatement()
        ;

        return ['group_restriction'];
    }
}
